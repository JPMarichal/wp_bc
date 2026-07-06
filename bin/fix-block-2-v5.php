<?php
$post_id = 2589;
$blocks = parse_blocks(get_post($post_id)->post_content);

foreach ($blocks as $i => $block) {
    if ($block['blockName'] !== 'merpress/mermaidjs') continue;
    
    $html = $block['innerHTML'];
    if (strpos($html, 'graph TB') === false) continue;

    // Replace exact mermaid content inside pre tag
    $html = str_replace(
        '<pre class="mermaid">graph TB
 X[Superficie inestable] --> Y[Cavidades de disolucion]
 Y --> Z[Capas profundas de Halita]</pre>',
        '<pre class="mermaid">graph TB
X[Superficie inestable] --> Y[Cavidades de disolucion]
Y --> Z[Capas profundas de Halita]</pre>',
        $html
    );

    $blocks[$i] = [
        'blockName' => 'merpress/mermaidjs',
        'attrs' => [],
        'innerBlocks' => [],
        'innerHTML' => $html,
        'innerContent' => [$html],
    ];
}

$new_content = serialize_blocks($blocks);
global $wpdb;
$wpdb->update($wpdb->posts, ['post_content' => $new_content], ['ID' => $post_id], ['%s'], ['%d']);

// Verify
preg_match_all('/<pre class="mermaid">(.*?)<\/pre>/s', get_post($post_id)->post_content, $m);
foreach ($m[1] as $i => $mc) {
    echo "Block " . ($i+1) . ": " . json_encode($mc, JSON_UNESCAPED_UNICODE) . "\n";
}
