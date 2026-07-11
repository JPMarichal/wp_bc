<?php
/**
 * Exporta ubicaciones pendientes de traducir a JSON para el pipeline Alejandría.
 * Uso: docker exec wp_bc_cli wp eval-file scripts/tracking/export-pending.php --allow-root
 * Output: scripts/tracking/pending-translate.json
 */

$db_path = '/var/www/html/scripts/tracking/locations.db';
$db = new SQLite3($db_path);

$results = $db->query(
    "SELECT post_id, title, name_en, source, loc_type, has_scriptures, scriptures_json, alt_names, alias_of
     FROM locations
     WHERE es_status = 'pending_translate'
     ORDER BY
       CASE source
         WHEN 'openbible' THEN 1
         WHEN 'gnosis' THEN 2
         WHEN 'manual' THEN 3
         ELSE 4
       END,
       title ASC"
);

$pending = array();
while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
    $item = array(
        'post_id' => (int)$row['post_id'],
        'title' => $row['title'],
        'name_en' => $row['name_en'],
        'source' => $row['source'],
        'type' => $row['loc_type'],
        'has_scriptures' => (bool)$row['has_scriptures'],
    );

    if ($row['scriptures_json']) {
        $item['scriptures'] = json_decode($row['scriptures_json'], true);
    }

    $pending[] = $item;
}

$output_path = __DIR__ . '/pending-translate.json';
file_put_contents($output_path, json_encode($pending, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "Exported " . count($pending) . " pending locations to $output_path\n";

// Breakdown by source
$by_source = array();
foreach ($pending as $p) {
    $s = $p['source'];
    if (!isset($by_source[$s])) $by_source[$s] = 0;
    $by_source[$s]++;
}
foreach ($by_source as $s => $c) {
    echo "  $s: $c\n";
}

$db->close();
