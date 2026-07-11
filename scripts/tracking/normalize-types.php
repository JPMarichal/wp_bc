<?php
/**
 * Normaliza _bc_loc_type a valores estándar.
 * Uso: docker exec wp_bc_cli wp eval-file scripts/tracking/normalize-types.php --allow-root
 */

$map = [
    'settlement' => 'city',
    'sea' => 'water',
    'river' => 'water',
];

$posts = get_posts(array(
    'post_type' => 'bc_location',
    'post_status' => 'publish',
    'posts_per_page' => -1,
));

$fixed = 0;
foreach ($posts as $p) {
    $t = get_post_meta($p->ID, '_bc_loc_type', true);
    if (isset($map[$t])) {
        $new = $map[$t];
        update_post_meta($p->ID, '_bc_loc_type', $new);
        $fixed++;
        echo "  ID {$p->ID}: \"{$p->post_title}\": {$t} → {$new}\n";
    }
}

echo "\nFixed: $fixed\n";
