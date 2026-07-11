<?php
/**
 * Fix _bc_loc_disambiguation text that exposes internal source labels.
 *
 * Removes developer-only annotations like "Referencia arqueológica (gnosis)",
 * "Referencia bíblica (openbible)", "Datos de gnosis", "Datos de openbible",
 * and similar from the disambiguation field.
 *
 * Also renames/aliases canonical posts with "(2)" in title to remove
 * OpenBible internal numbering from user-facing text.
 */

require_once dirname(__DIR__) . '/wp-load.php';

$dry_run = in_array('--dry-run', $argv ?? []);
$verbose = in_array('--verbose', $argv ?? []);

if ($dry_run) {
    echo "=== DRY RUN ===\n\n";
}

global $wpdb;

/**
 * Delete _bc_loc_disambiguation for a post.
 */
function clear_disambig(int $post_id, string $reason, bool $dry_run, bool $verbose): void {
    $post = get_post($post_id);
    $existing = get_post_meta($post_id, '_bc_loc_disambiguation', true);
    if (!$existing) {
        if ($verbose) echo "  SKIP {$post_id}: no disambiguation\n";
        return;
    }
    echo "  CLEAR disambig {$post_id} ({$post->post_title}): \"{$existing}\" [{$reason}]\n";
    if (!$dry_run) {
        delete_post_meta($post_id, '_bc_loc_disambiguation');
    }
}

/**
 * Update a post title and slug.
 */
function rename_post(int $post_id, string $new_title, string $new_slug, bool $dry_run, bool $verbose): void {
    $post = get_post($post_id);
    echo "  RENAME {$post_id} \"{$post->post_title}\" → \"{$new_title}\" (slug: {$new_slug})\n";
    if (!$dry_run) {
        wp_update_post([
            'ID'         => $post_id,
            'post_title' => $new_title,
            'post_name'  => $new_slug,
        ]);
    }
}

/**
 * Set _bc_loc_alias_of on a post.
 */
function set_alias(int $variant_id, int $canonical_id, string $reason, bool $dry_run, bool $verbose): void {
    $existing = get_post_meta($variant_id, '_bc_loc_alias_of', true);
    $variant = get_post($variant_id);
    $canonical = get_post($canonical_id);
    if (!$variant || !$canonical) {
        echo "  ERROR: post not found ({$variant_id} or {$canonical_id})\n";
        return;
    }
    echo "  ALIAS {$variant_id} ({$variant->post_title}) → {$canonical_id} ({$canonical->post_title}) [{$reason}]\n";
    if ($existing) {
        echo "    (was alias_of = {$existing})\n";
    }
    if (!$dry_run) {
        update_post_meta($variant_id, '_bc_loc_alias_of', $canonical_id);
    }
}

// ============================================================
// STEP 1: Delete source-label disambiguation text
// ============================================================
echo "=== STEP 1: Eliminar etiquetas de fuente interna en disambiguation ===\n";

$bad_disambigs = $wpdb->get_col($wpdb->prepare("
    SELECT post_id FROM wp_postmeta
    WHERE meta_key = '_bc_loc_disambiguation'
      AND (
        meta_value LIKE %s
        OR meta_value LIKE %s
        OR meta_value LIKE %s
        OR meta_value LIKE %s
        OR meta_value LIKE %s
        OR meta_value LIKE %s
      )
", '%gnosis%', '%openbible%', 'Referencia%', 'Datos de%', 'Otra posible%', 'Posible ubicación%'));

// Also clean "Río (diferenciar de..."
$more_bad = $wpdb->get_col($wpdb->prepare("
    SELECT post_id FROM wp_postmeta
    WHERE meta_key = '_bc_loc_disambiguation'
      AND meta_value LIKE %s
", 'Río (diferenciar%'));

$all_bad = array_unique(array_merge($bad_disambigs, $more_bad));

foreach ($all_bad as $post_id) {
    clear_disambig((int)$post_id, 'etiqueta de fuente interna', $dry_run, $verbose);
}

echo "\n=== STEP 2: Alias/renombrar títulos canónicos con (2) ===\n";

// 2a. Posts where (2) is the same place as a Spanish base → create alias
// Jezreel (2) (277) → Jezreel (2932) — same place, coordinate variant
echo "--- 2a: Posts (2) que se pueden aliasar a base existente ---\n";
set_alias(277, 2932, 'Jezreel (2) es Jezreel', $dry_run, $verbose);   // Jezreel (2) → Jezreel
set_alias(364, 363, 'Nebo (2) es Nebo', $dry_run, $verbose);          // Nebo (2) → Nebo
set_alias(263, 262, 'Jabneel (2) es Jabneel', $dry_run, $verbose);    // Jabneel (2) → Jabneel
set_alias(465, 464, 'Tiphsah (2) → Tifsa', $dry_run, $verbose);      // Tiphsah (2) → Tifsa

// 2b. Posts where no title conflict → just rename
echo "--- 2b: Posts (2) que se pueden renombrar directamente ---\n";
rename_post(44, 'Aphek', 'aphek', $dry_run, $verbose);                // Aphek (2) → Aphek

// Actually, for Monte Seir (2), there IS Monte Seir (3028). 
// Monte Seir = the mountain range. Monte Seir (2) might be a specific location.
// I'll leave this one for now — just clear disambig.

// 2c. Cades (2) — mapping is unclear. Just clear disambig.
// Moreh (2) — could alias to Moreh (3002) if same place.
echo "--- 2c: Casos donde alias o rename no es claro ---\n";
echo "  SKIP Cades (2): se desconoce mapeo. Solo se limpia desambiguación.\n";
echo "  SKIP Monte Seir (2): se desconoce mapeo. Solo se limpia desambiguación.\n";
echo "  SKIP Moreh (2): se desconoce mapeo. Solo se limpia desambiguación.\n";

// Now verify: clean disambig on those too (already done in Step 1)

echo "\n=== VERIFICACIÓN ===\n";
$remaining_disambig = $wpdb->get_var("
    SELECT COUNT(*) FROM wp_postmeta
    WHERE meta_key = '_bc_loc_disambiguation'
      AND (meta_value LIKE '%gnosis%' OR meta_value LIKE '%openbible%' OR meta_value LIKE 'Referencia%' OR meta_value LIKE 'Datos de%')
");
echo "Disambiguation entries still with source labels: {$remaining_disambig}\n";

$canonical_with_2 = $wpdb->get_var("
    SELECT COUNT(*) FROM wp_posts p
    WHERE p.post_type = 'bc_location' AND p.post_status = 'publish'
      AND p.post_title REGEXP '\\(2\\)'
      AND NOT EXISTS (SELECT 1 FROM wp_postmeta WHERE post_id = p.ID AND meta_key = '_bc_loc_alias_of')
");
echo "Canonical posts with (2) in title: {$canonical_with_2}\n";

echo "Done.\n";
