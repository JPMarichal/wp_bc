<?php
/**
 * Inicializa SQLite con dump completo de bc_location.
 * Uso: docker exec -i wp_bc_cli wp eval-file tracking/init-db.php --allow-root
 */

$db_path = '/var/www/html/tracking/locations.db';
$schema_path = '/var/www/html/tracking/schema.sql';

// Crear directorio si no existe
$dir = dirname($db_path);
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}

// Conectar SQLite
$db = new SQLite3($db_path);
$db->exec('PRAGMA journal_mode=WAL');
$db->exec('PRAGMA foreign_keys=ON');

// Cargar schema
$schema = file_get_contents($schema_path);
$db->exec($schema);

// Limpiar tabla existente
$db->exec('DELETE FROM locations');

// Obtener todos los posts bc_location
$posts = get_posts(array(
    'post_type' => 'bc_location',
    'post_status' => 'publish',
    'posts_per_page' => -1,
));

$count = 0;
$insert = $db->prepare(
    'INSERT OR REPLACE INTO locations
    (post_id, title, name_en, name_es_meta, slug, source, loc_type,
     has_scriptures, scriptures_json, has_content, es_status, alt_names, alias_of)
    VALUES (:pid, :title, :name_en, :name_es_meta, :slug, :source, :type,
            :has_scr, :scr_json, :has_content, :status, :alt_names, :alias_of)'
);

foreach ($posts as $p) {
    $name_en = get_post_meta($p->ID, '_bc_loc_name_en', true);
    $name_es = get_post_meta($p->ID, '_bc_loc_name_es', true);
    $source = get_post_meta($p->ID, '_bc_loc_source', true);
    $type = get_post_meta($p->ID, '_bc_loc_type', true);
    $scriptures = get_post_meta($p->ID, '_bc_loc_scriptures', true);
    $alt_names = get_post_meta($p->ID, '_bc_loc_alt_names', true);
    $alias_of = get_post_meta($p->ID, '_bc_loc_alias_of', true);
    $has_content = !empty($p->post_content);
    $title = $p->post_title;

    // Determinar status
    if (!empty($name_es)) {
        $status = 'done';
        $name_es_dest = $name_es;
    } elseif ($title !== $name_en) {
        $status = 'pending_meta';
        $name_es_dest = $title;
    } else {
        $status = 'pending_translate';
        $name_es_dest = '';
    }

    $insert->bindValue(':pid', $p->ID, SQLITE3_INTEGER);
    $insert->bindValue(':title', $title, SQLITE3_TEXT);
    $insert->bindValue(':name_en', $name_en ?: $title, SQLITE3_TEXT);
    $insert->bindValue(':name_es_meta', $name_es ?: '', SQLITE3_TEXT);
    $insert->bindValue(':slug', $p->post_name, SQLITE3_TEXT);
    $insert->bindValue(':source', $source ?: '', SQLITE3_TEXT);
    $insert->bindValue(':type', $type ?: '', SQLITE3_TEXT);
    $insert->bindValue(':has_scr', !empty($scriptures) ? 1 : 0, SQLITE3_INTEGER);
    $insert->bindValue(':scr_json', $scriptures ?: '', SQLITE3_TEXT);
    $insert->bindValue(':has_content', $has_content ? 1 : 0, SQLITE3_INTEGER);
    $insert->bindValue(':status', $status, SQLITE3_TEXT);
    $insert->bindValue(':alt_names', is_array($alt_names) ? json_encode($alt_names) : ($alt_names ?: ''), SQLITE3_TEXT);
    $insert->bindValue(':alias_of', $alias_of ? (int)$alias_of : 0, SQLITE3_INTEGER);
    $insert->execute();
    $count++;
}

echo "Init complete: $count locations imported.\n";

// Mostrar resumen
$res = $db->querySingle(
    "SELECT
        COUNT(*) as total,
        SUM(CASE WHEN es_status='done' THEN 1 ELSE 0 END) as done,
        SUM(CASE WHEN es_status='pending_meta' THEN 1 ELSE 0 END) as pending_meta,
        SUM(CASE WHEN es_status='pending_translate' THEN 1 ELSE 0 END) as pending_translate,
        SUM(CASE WHEN es_status='pending_translate' AND has_scriptures=1 THEN 1 ELSE 0 END) as translate_with_scr,
        SUM(CASE WHEN es_status='pending_translate' AND has_scriptures=0 THEN 1 ELSE 0 END) as translate_no_scr
    FROM locations", true
);
echo "Total: {$res['total']} | Done: {$res['done']} | Pending meta: {$res['pending_meta']} | Pending translate: {$res['pending_translate']} (with scr: {$res['translate_with_scr']}, no scr: {$res['translate_no_scr']})\n";

$db->close();
