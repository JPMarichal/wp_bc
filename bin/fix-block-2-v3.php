<?php
$post_id = 2589;
$content = get_post($post_id)->post_content;
$blocks = parse_blocks($content);

foreach ($blocks as $i => $block) {
    if ($block['blockName'] !== 'merpress/mermaidjs') continue;
    
    $html = $block['innerHTML'];
    
    if (strpos($html, 'graph TB') !== false) {
        echo "Fixing block $i: removing indent\n";
        
        $html = preg_replace(
            '/<pre class="mermaid">\s*graph TB\s*\n\s+X\[Superficie inestable\]\s*-->\s*Y\[Cavidades de disolucion\]\s*\n\s+Y\s*-->\s*Z\[Capas profundas de Halita\]\s*<\/pre>/s',
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
}

$new_content = serialize_blocks($blocks);
global $wpdb;
$wpdb->update($wpdb->posts, ['post_content' => $new_content], ['ID' => $post_id], ['%s'], ['%d']);

// Show result
$blocks2 = parse_blocks(get_post($post_id)->post_content);
foreach ($blocks2 as $b) {
    if ($b['blockName'] === 'merpress/mermaidjs') {
        preg_match('/<pre class="mermaid">(.*?)<\/pre>/s', $b['innerHTML'], $m);
        echo json_encode($m[1], JSON_UNESCAPED_UNICODE) . "\n";
    }
}
