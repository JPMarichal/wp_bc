<?php
$post_id = 2616;
$content = get_post($post_id)->post_content;

preg_match_all('%
    <(h[1-6]|p|blockquote)\b[^>]*>.*?</\1>|
    <(ul|ol)\b[^>]*>.*?</\4>
%isx', $content, $matches);

$new_parts = [];
foreach ($matches[0] as $element) {
    if (preg_match('/^<h([1-6])\b/', $element, $m)) {
        $level = (int)$m[1];
        $attrs = $level === 2 ? '' : ' {"level":' . $level . '}';
        $new_parts[] = '<!-- wp:heading' . $attrs . ' -->' . $element . '<!-- /wp:heading -->';
    } elseif (strpos($element, '<blockquote') === 0) {
        $new_parts[] = '<!-- wp:quote -->' . $element . '<!-- /wp:quote -->';
    } elseif (preg_match('/^<(ul|ol)\b/', $element)) {
        $new_parts[] = '<!-- wp:list -->' . $element . '<!-- /wp:list -->';
    } elseif (strpos($element, '<p') === 0) {
        $new_parts[] = '<!-- wp:paragraph -->' . $element . '<!-- /wp:paragraph -->';
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
echo "has_blocks: " . (has_blocks(get_post($post_id)->post_content) ? "yes" : "no") . "\n";
