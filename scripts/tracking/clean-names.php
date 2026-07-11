<?php
/**
 * Clean up _bc_loc_name_es values with formatting issues (quotes, newlines).
 * Uso: docker exec wp_bc_cli wp eval-file scripts/tracking/clean-names.php --allow-root
 */

$posts = get_posts(array(
    'post_type' => 'bc_location',
    'post_status' => 'publish',
    'posts_per_page' => -1,
));

$cleaned = 0;
$checked = 0;

foreach ($posts as $p) {
    $name_es = get_post_meta($p->ID, '_bc_loc_name_es', true);
    if (empty($name_es)) continue;
    $checked++;
    
    $original = $name_es;
    $name_es = trim($name_es);
    $name_es = trim($name_es, "\"'");
    $name_es = preg_replace('/\s+/', ' ', $name_es);
    
    if ($name_es !== $original) {
        update_post_meta($p->ID, '_bc_loc_name_es', $name_es);
        $cleaned++;
        if ($cleaned <= 20 || $cleaned % 50 === 0) {
            echo "  ID {$p->ID}: \"" . substr($original, 0, 40) . "\" → \"{$name_es}\"\n";
        }
    }
}

echo "\nCleaned: $cleaned of $checked checked.\n";
