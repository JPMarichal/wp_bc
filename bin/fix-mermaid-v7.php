<?php
$post_id = 2589;
$content = get_post($post_id)->post_content;

// Block 1
$old1 = 'graph LR
 A["Placa Africana (Sinai)"] -->|"N-S"| B["Fosa del Mar Muerto"]
 B -->|"S-N"| C["Placa Arabiga"]';

$new1 = 'graph LR
    A[Placa Africana Sinai] -->|N-S| B[Fosa del Mar Muerto]
    B -->|S-N| C[Placa Arabiga]';

$content = str_replace($old1, $new1, $content);

// Block 2
$old2 = 'graph TB
 A["Superficie inestable (costra de sal)"] -->|"carga sismica"| B["Cavidades de disolucion"]
 B --> C["Capas profundas de Halita"]';

$new2 = 'graph TB
    X[Superficie inestable] --> Y[Cavidades de disolucion]
    Y --> Z[Capas profundas de Halita]';

$content = str_replace($old2, $new2, $content);

$found1 = strpos($content, $old1);
$found2 = strpos($content, $old2);
echo "Block 1 old still found: " . ($found1 !== false ? "YES" : "NO") . "\n";
echo "Block 2 old still found: " . ($found2 !== false ? "YES" : "NO") . "\n";

global $wpdb;
$wpdb->update($wpdb->posts, ['post_content' => $content], ['ID' => $post_id], ['%s'], ['%d']);
echo "Saved.\n";
