<?php
/**
 * Actualiza post_title al español preservando el slug existente.
 * Uso: docker exec wp_bc_cli wp eval-file scripts/tracking/update-titles.php --allow-root
 */

$posts = get_posts(array(
    'post_type' => 'bc_location',
    'post_status' => 'publish',
    'posts_per_page' => -1,
));

$updated = 0;
$skipped = 0;

foreach ($posts as $p) {
    $title = $p->post_title;
    $name_es = get_post_meta($p->ID, '_bc_loc_name_es', true);
    $name_en = get_post_meta($p->ID, '_bc_loc_name_en', true);
    
    if (empty($name_es)) {
        $skipped++;
        continue;
    }
    if ($title === $name_es) {
        $skipped++;
        continue;
    }
    if ($title !== $name_en) {
        // Already has Spanish title
        $skipped++;
        continue;
    }
    
    // Preserve slug, update title
    $slug = $p->post_name;
    wp_update_post(array(
        'ID' => $p->ID,
        'post_title' => $name_es,
        'post_name' => $slug,
    ));
    $updated++;
    
    if ($updated <= 10 || $updated % 50 === 0) {
        echo "  ID {$p->ID}: \"{$title}\" → \"{$name_es}\"\n";
    }
}

echo "\nTitles updated: $updated, skipped: $skipped.\n";
