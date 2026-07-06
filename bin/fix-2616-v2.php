<?php
$post_id = 2616;
$content = get_post($post_id)->post_content;

// Split into top-level HTML blocks and wrap each in proper Gutenberg markers
$parts = preg_split('/(<h[1-6]\b[^>]*>.*?<\/h[1-6]>|<p\b[^>]*>.*?<\/p>|<blockquote\b[^>]*>.*?<\/blockquote>|<ul\b[^>]*>.*?<\/ul>|<ol\b[^>]*>.*?<\/ol>)/is', $content, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

$new_parts = [];
foreach ($parts as $part) {
    $trimmed = trim($part);
    if (empty($trimmed)) continue;

    if (preg_match('/^<h([1-6])\b/', $trimmed, $m)) {
        $level = (int)$m[1];
        $attrs = $level === 2 ? '' : ' {"level":' . $level . '}';
        $new_parts[] = '<!-- wp:heading' . $attrs . ' -->' . $trimmed . '<!-- /wp:heading -->';
    } elseif (strpos($trimmed, '<blockquote') === 0) {
        $new_parts[] = '<!-- wp:quote -->' . $trimmed . '<!-- /wp:quote -->';
    } elseif (preg_match('/^<(ul|ol)\b/', $trimmed)) {
        $new_parts[] = '<!-- wp:list -->' . $trimmed . '<!-- /wp:list -->';
    } elseif (strpos($trimmed, '<p') === 0) {
        $new_parts[] = '<!-- wp:paragraph -->' . $trimmed . '<!-- /wp:paragraph -->';
    }
}

$new_content = implode("\n", $new_parts);

global $wpdb;
$wpdb->update($wpdb->posts, ['post_content' => $new_content], ['ID' => $post_id], ['%s'], ['%d']);

$blocks = parse_blocks(get_post($post_id)->post_content);
echo "Total blocks: " . count($blocks) . "\n";
$classic = 0;
foreach ($blocks as $i => $b) {
    $name = $b["blockName"] ?: "(classic)";
    if ($b["blockName"] === null) $classic++;
    echo "  $i: $name\n";
}
echo "Classic blocks: $classic\n";
