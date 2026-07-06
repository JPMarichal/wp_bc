<?php
$post_id = 2589;
$post = get_post($post_id);
$content = $post->post_content;

// Replace block 1
$old1 = 'graph LR A["Placa Africana (Sinai)"] -->|"N-S"| B["Fosa del Mar Muerto"] B -->|"S-N"| C["Placa Arabiga"]';
$new1 = "graph LR\n    A[Placa Africana Sinai] -->|N-S| B[Fosa del Mar Muerto]\n    B -->|S-N| C[Placa Arabiga]";
$content = str_replace($old1, $new1, $content);

// Replace block 2
$old2 = 'graph TB A["Superficie inestable (costra de sal)"] -->|"carga sismica"| B["Cavidades de disolucion"] B --> C["Capas profundas de Halita"]';
$new2 = "graph TB\n    X[Superficie inestable] --> Y[Cavidades de disolucion]\n    Y --> Z[Capas profundas de Halita]";
$content = str_replace($old2, $new2, $content);

// Check if replacements actually happened
if (strpos($content, $old1) !== false) {
    echo "ERROR: Block 1 old string still found after replacement!\n";
}
if (strpos($content, $old2) !== false) {
    echo "ERROR: Block 2 old string still found after replacement!\n";
}

// Save via wp_update_post to trigger all hooks
$result = wp_update_post(array(
    'ID' => $post_id,
    'post_content' => $content,
));

if ($result == $post_id) {
    echo "Post $post_id updated correctly via wp_update_post.\n";
} else {
    echo "ERROR: wp_update_post returned $result\n";
}
