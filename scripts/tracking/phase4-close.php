<?php
/**
 * Fase 4: Cierre — aplica _bc_loc_name_es para los 64 sin escrituras.
 * Todos tienen title === name_en, pero muchos ya están en español.
 * Uso: docker exec wp_bc_cli wp eval-file scripts/tracking/phase4-close.php --allow-root
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
    update_post_meta($p->ID, '_bc_loc_name_es', $title);
    $updated++;

    $db->exec("UPDATE locations SET name_es_meta='" . SQLite3::escapeString($title) . "', es_status='done' WHERE post_id={$p->ID}");
    $db->exec("INSERT INTO translation_log (post_id, action, old_value, new_value)
               VALUES ({$p->ID}, 'phase4_close', '', '" . SQLite3::escapeString($title) . "')");

    if ($updated <= 5 || $updated % 20 === 0) {
        echo "  ID {$p->ID}: '{$title}'\n";
    }
}

// Resumen
$res = $db->querySingle(
    "SELECT
        COUNT(*) as total,
        SUM(CASE WHEN es_status='done' THEN 1 ELSE 0 END) as done,
        SUM(CASE WHEN es_status='pending_translate' THEN 1 ELSE 0 END) as pending
    FROM locations", true
);

echo "\nPhase 4 complete: $updated updated, $skipped already done.\n";
echo "Total: {$res['total']} | Done: {$res['done']} | Pending: {$res['pending']}\n";

$db->close();
