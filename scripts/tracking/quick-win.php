<?php
/**
 * Quick Win: Copia post_title → _bc_loc_name_es donde title ya está en español.
 * Uso: docker exec wp_bc_cli wp eval-file scripts/tracking/quick-win.php --allow-root
 */

$db_path = '/var/www/html/scripts/tracking/locations.db';
$db = new SQLite3($db_path);

$posts = get_posts(array(
    'post_type' => 'bc_location',
    'post_status' => 'publish',
    'posts_per_page' => -1,
));

$updated = 0;
$skipped = 0;

foreach ($posts as $p) {
    $name_es = get_post_meta($p->ID, '_bc_loc_name_es', true);
    if (!empty($name_es)) {
        $skipped++;
        continue;
    }
    $title = $p->post_title;
    $name_en = get_post_meta($p->ID, '_bc_loc_name_en', true);

    if ($title === $name_en) {
        $skipped++;
        continue;
    }

    // Title is different from name_en → already Spanish
    update_post_meta($p->ID, '_bc_loc_name_es', $title);
    $updated++;

    $db->exec("UPDATE locations SET name_es_meta='$title', es_status='done' WHERE post_id={$p->ID}");
    $db->exec("INSERT INTO translation_log (post_id, action, old_value, new_value)
               VALUES ({$p->ID}, 'quick_win', '', '$title')");

    echo "  ID {$p->ID}: '{$name_en}' → '{$title}'\n";
}

echo "\nQuick Win complete: $updated updated, $skipped skipped.\n";

// Actualizar resumen
$res = $db->querySingle(
    "SELECT
        COUNT(*) as total,
        SUM(CASE WHEN es_status='done' THEN 1 ELSE 0 END) as done,
        SUM(CASE WHEN es_status='pending_meta' THEN 1 ELSE 0 END) as pending_meta,
        SUM(CASE WHEN es_status='pending_translate' THEN 1 ELSE 0 END) as pending_translate
    FROM locations", true
);
echo "Total: {$res['total']} | Done: {$res['done']} | Pending_meta: {$res['pending_meta']} | Pending_translate: {$res['pending_translate']}\n";

$db->close();
