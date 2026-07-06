<?php
$post_id = 2589;
$content = get_post($post_id)->post_content;

// Block 1: replace the simple test with proper diagrams
// Avoiding: parentheses, special chars, quotes
// Using: A[text] instead of A["text"] for simplicity
$old1 = 'graph LR
    A --> B';
$new1 = 'graph LR
    A[Placa Africana Sinai]
    B[Fosa del Mar Muerto]
    C[Placa Arabiga]
    A -->|N-S| B
    B -->|S-N| C';
$content = str_replace($old1, $new1, $content);

$old2 = 'graph TB
    X --> Y';
$new2 = 'graph TB
    X[Superficie inestable]
    Y[Cavidades de disolucion]
    Z[Capas profundas de Halita]
    X --> Y
    Y --> Z';
$content = str_replace($old2, $new2, $content);

global $wpdb;
$wpdb->update(
    $wpdb->posts,
    array('post_content' => $content),
    array('ID' => $post_id),
    array('%s'),
    array('%d')
);

echo "Post $post_id v3.\n";
