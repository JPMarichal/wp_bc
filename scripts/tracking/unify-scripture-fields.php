<?php
/**
 * Unifica _bc_loc_scripture (singular) en _bc_loc_scriptures (plural).
 * Elimina el campo singular después de migrar.
 * Uso: docker exec wp_bc_cli wp eval-file scripts/tracking/unify-scripture-fields.php --allow-root
 */

global $wpdb;

// Find all posts with _bc_loc_scripture
$rows = $wpdb->get_results("
    SELECT post_id, meta_value as singular_val
    FROM {$wpdb->postmeta}
    WHERE meta_key = '_bc_loc_scripture' AND meta_value != ''
");

$merged = 0;
$converted = 0;
$skipped = 0;

foreach ($rows as $row) {
    $post_id = $row->post_id;
    $singular = trim($row->singular_val);
    if (empty($singular)) continue;

    $plural_raw = get_post_meta($post_id, '_bc_loc_scriptures', true);
    $plural = [];

    if (!empty($plural_raw)) {
        if (is_string($plural_raw)) {
            $decoded = json_decode($plural_raw, true);
            $plural = is_array($decoded) ? $decoded : [$plural_raw];
        } elseif (is_array($plural_raw)) {
            $plural = $plural_raw;
        }
    }

    $singular_ref = ['ref' => $singular];

    // Check if already in plural
    $found = false;
    foreach ($plural as $item) {
        $ref = is_array($item) ? ($item['ref'] ?? '') : $item;
        if (strcasecmp(trim($ref), $singular) === 0) {
            $found = true;
            break;
        }
    }

    if (!$found) {
        $plural[] = $singular_ref;
        update_post_meta($post_id, '_bc_loc_scriptures', $plural);
        if (!empty($plural_raw)) {
            $merged++;
        } else {
            $converted++;
        }
    } else {
        $skipped++;
    }

    // Delete singular meta
    delete_post_meta($post_id, '_bc_loc_scripture');
}

echo "Merged (singular into existing plural): $merged\n";
echo "Converted (singular → new plural): $converted\n";
echo "Skipped (already in plural): $skipped\n";

// Verify
$remaining = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE meta_key = '_bc_loc_scripture'");
$plurals = $wpdb->get_var("SELECT COUNT(DISTINCT post_id) FROM {$wpdb->postmeta} WHERE meta_key = '_bc_loc_scriptures' AND meta_value != ''");
echo "\nRemaining _bc_loc_scripture: $remaining\n";
echo "Posts with _bc_loc_scriptures: $plurals\n";
