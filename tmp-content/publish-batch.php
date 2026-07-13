<?php
// publish-batch.php — copiar a wp_bc_cli:/tmp/ y ejecutar con wp eval
// Uso: docker cp tmp-content/publish-batch.php wp_bc_cli:/tmp/publish-batch.php
//      docker exec wp_bc_cli wp eval-file /tmp/publish-batch.php --allow-root
$base = '/tmp/publish';
$ids_file = "$base/ids.txt";
if (!file_exists($ids_file)) {
    fwrite(STDERR, "ERROR: $ids_file not found. Create with IDs, one per line.\n");
    exit(1);
}
$ids = array_filter(array_map('trim', file($ids_file)));
$success = 0; $errors = 0;
foreach ($ids as $id) {
    $file = "$base/$id.html";
    if (!file_exists($file)) {
        echo "SKIP $id: file not found\n";
        $errors++;
        continue;
    }
    $content = file_get_contents($file);
    $result = wp_update_post(array('ID' => (int)$id, 'post_content' => $content), true);
    if (is_wp_error($result)) {
        echo "ERROR $id: " . $result->get_error_message() . "\n";
        $errors++;
    } else {
        echo "OK $id: " . strlen($content) . " chars\n";
        $success++;
    }
}
echo "\nDone: $success updated, $errors errors\n";
