<?php
$post_id = 2589;
$content = get_post($post_id)->post_content;
$blocks = parse_blocks($content);

foreach ($blocks as $i => $block) {
    $html = $block['innerHTML'];
    if (strpos($html, 'Superficie inestable') !== false && strpos($html, 'Halita') !== false) {
        echo "Fixing block $i: " . $block['blockName'] . " -> merpress/mermaidjs\n";

        // Fix HTML entities and add newlines
        $html = str_replace('--&gt;', '-->', $html);
        $html = preg_replace(
            '/<pre class="mermaid">\s*graph TB\s*X\[[^\]]*\]\s*-->\s*Y\[[^\]]*\]\s*Y\s*-->\s*Z\[[^\]]*\]\s*<\/pre>/s',
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

// Verify
$blocks2 = parse_blocks(get_post($post_id)->post_content);
$merpress = 0;
foreach ($blocks2 as $b) {
    if ($b['blockName'] === 'merpress/mermaidjs') {
        $merpress++;
        preg_match('/<pre class="mermaid">(.*?)<\/pre>/s', $b['innerHTML'], $m);
        echo "MerPress block content:\n" . $m[1] . "\n---\n";
    }
}
echo "Total blocks: " . count($blocks2) . "\n";
echo "MerPress blocks: $merpress\n";
