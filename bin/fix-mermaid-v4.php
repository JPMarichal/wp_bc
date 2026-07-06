<?php
$post_id = 2589;
$content = get_post($post_id)->post_content;

// Block 1: Replace single-line syntax with parentheses with multi-line clean syntax
$old1 = 'graph LR A["Placa Africana (Sinai)"] -->|"N-S"| B["Fosa del Mar Muerto"] B -->|"S-N"| C["Placa Arabiga"]';
$new1 = 'graph LR
    A[Placa Africana Sinai] -->|N-S| B[Fosa del Mar Muerto]
    B -->|S-N| C[Placa Arabiga]';
$content = str_replace($old1, $new1, $content);

// Block 2: Replace single-line syntax with parentheses with multi-line clean syntax
$old2 = 'graph TB A["Superficie inestable (costra de sal)"] -->|"carga sismica"| B["Cavidades de disolucion"] B --> C["Capas profundas de Halita"]';
$new2 = 'graph TB
    X[Superficie inestable] --> Y[Cavidades de disolucion]
    Y --> Z[Capas profundas de Halita]';
$content = str_replace($old2, $new2, $content);

global $wpdb;
$wpdb->update(
    $wpdb->posts,
    array('post_content' => $content),
    array('ID' => $post_id),
    array('%s'),
    array('%d')
);

echo "Post $post_id v4.\n";
