<?php
$post_id = 2589;
$post = get_post($post_id);
$content = $post->post_content;

// Block 1: match actual content with real newlines
$old1 = "graph LR\n    A[\"Placa Africana (Sinai)\"] -->|\"N-S\"| B[\"Fosa del Mar Muerto\"]\n    B -->|\"S-N\"| C[\"Placa Arabiga\"]";
$new1 = "graph LR\n    A[Placa Africana Sinai] -->|N-S| B[Fosa del Mar Muerto]\n    B -->|S-N| C[Placa Arabiga]";
$content = str_replace($old1, $new1, $content);

// Block 2: match actual content with real newlines
$old2 = "graph TB\n    A[\"Superficie inestable (costra de sal)\"] -->|\"carga sismica\"| B[\"Cavidades de disolucion\"]\n    B --> C[\"Capas profundas de Halita\"]";
$new2 = "graph TB\n    X[Superficie inestable] --> Y[Cavidades de disolucion]\n    Y --> Z[Capas profundas de Halita]";
$content = str_replace($old2, $new2, $content);

// Verify
$found1 = strpos($content, $old1);
$found2 = strpos($content, $old2);
echo "Block 1 old still present: " . ($found1 !== false ? "YES (at $found1)" : "NO") . "\n";
echo "Block 2 old still present: " . ($found2 !== false ? "YES (at $found2)" : "NO") . "\n";

global $wpdb;
$wpdb->update(
    $wpdb->posts,
    array('post_content' => $content),
    array('ID' => $post_id),
    array('%s'),
    array('%d')
);
echo "Post $post_id saved.\n";
