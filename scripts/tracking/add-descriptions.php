<?php
/**
 * Genera _bc_loc_description para ubicaciones sin descripción.
 * Uso: docker exec wp_bc_cli wp eval-file scripts/tracking/add-descriptions.php --allow-root
 */

$posts = get_posts(array(
    'post_type' => 'bc_location',
    'post_status' => 'publish',
    'posts_per_page' => -1,
));

$type_labels = [
    'city' => 'Ciudad',
    'region' => 'Región',
    'mountain' => 'Monte',
    'valley' => 'Valle',
    'water' => 'Cuerpo de agua',
    'island' => 'Isla',
    'landmark' => 'Lugar emblemático',
    'path' => 'Camino',
];

$source_labels = [
    'openbible' => 'referencia bíblica',
    'gnosis' => 'estudios bíblicos',
    'church-history' => 'historia SUD',
    'manual' => 'registro histórico',
    'book-of-mormon' => 'Libro de Mormón',
];

$filled = 0;
$skipped = 0;

foreach ($posts as $p) {
    $desc = get_post_meta($p->ID, '_bc_loc_description', true);
    if (!empty($desc)) {
        $skipped++;
        continue;
    }

    $type = get_post_meta($p->ID, '_bc_loc_type', true);
    $source = get_post_meta($p->ID, '_bc_loc_source', true);
    $scriptures = get_post_meta($p->ID, '_bc_loc_scriptures', true);
    $title = $p->post_title;
    $name_en = get_post_meta($p->ID, '_bc_loc_name_en', true) ?: $title;

    $type_label = $type_labels[$type] ?? 'Lugar';
    $source_label = $source_labels[$source] ?? $source;
    $scr_count = 0;
    $first_ref = '';
    if (!empty($scriptures)) {
        $refs = is_array($scriptures) ? $scriptures : json_decode($scriptures, true);
        if (is_array($refs)) {
            $scr_count = count($refs);
            $first = reset($refs);
            $first_ref = is_array($first) ? ($first['ref'] ?? '') : $first;
        }
    }

    $parts = [];
    if (!empty($first_ref)) {
        $parts[] = "{$type_label} mencionada en {$source_label}s con referencia en {$first_ref}";
        if ($scr_count > 1) {
            $parts[0] .= " y otros " . ($scr_count - 1) . " pasajes";
        }
        $parts[0] .= ".";
    } else {
        $parts[] = "{$type_label} identificada por {$source_label}.";
    }

    // Add temple-specific text
    if ($source === 'church-history' && stripos($title, 'templo') !== false) {
        $parts[] = " Lugar sagrado para La Iglesia de Jesucristo de los Santos de los Últimos Días.";
    } elseif ($source === 'church-history') {
        $parts[] = " Lugar de importancia histórica para la Iglesia SUD.";
    }

    $description = implode('', $parts);

    update_post_meta($p->ID, '_bc_loc_description', $description);
    $filled++;

    if ($filled <= 5 || $filled % 200 === 0) {
        echo "  ID {$p->ID}: \"{$title}\"\n";
    }
}

echo "\nDescriptions filled: $filled, skipped: $skipped\n";
