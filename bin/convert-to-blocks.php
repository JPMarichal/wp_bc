<?php
$post_id = 2589;
$content = get_post($post_id)->post_content;
$blocks = parse_blocks($content);

$new_blocks = [];
foreach ($blocks as $block) {
    if ($block['blockName'] === null) {
        // Convert classic HTML into proper blocks
        $inner = $block['innerHTML'];
        $sub_blocks = html_to_blocks($inner);
        $new_blocks = array_merge($new_blocks, $sub_blocks);
    } else {
        $new_blocks[] = $block;
    }
}

$new_content = serialize_blocks($new_blocks);

global $wpdb;
$wpdb->update($wpdb->posts, ['post_content' => $new_content], ['ID' => $post_id], ['%s'], ['%d']);

$count = count($new_blocks);
echo "Converted to $count blocks.\n";

function html_to_blocks($html) {
    $blocks = [];

    // Split into top-level HTML elements using regex
    preg_match_all('%
        <(h[1-6]|p|figure|blockquote|ol|ul)(\s[^>]*)?>.*?</\1>|
        <div\s[^>]*class="[^"]*wp-block-merpress[^"]*"[^>]*>.*?</div>
    %isx', trim($html), $matches);

    foreach ($matches[0] as $element) {
        $block = element_to_block($element);
        if ($block) {
            $blocks[] = $block;
        }
    }

    // If no matches found via regex, fall back to treating whole thing as a paragraph
    if (empty($blocks) && trim(strip_tags($html)) !== '') {
        $blocks[] = [
            'blockName' => 'core/paragraph',
            'attrs' => [],
            'innerBlocks' => [],
            'innerHTML' => $html,
            'innerContent' => [$html],
        ];
    }

    return $blocks;
}

function element_to_block($html) {
    // Detect block type from tag
    if (preg_match('/^<(h[1-6])\b/', $html, $m)) {
        $level = (int)substr($m[1], 1);
        $attrs = $level === 2 ? [] : ['level' => $level];
        return [
            'blockName' => 'core/heading',
            'attrs' => $attrs,
            'innerBlocks' => [],
            'innerHTML' => $html,
            'innerContent' => [$html],
        ];
    }

    if (strpos($html, '<figure') === 0) {
        return [
            'blockName' => 'core/image',
            'attrs' => [],
            'innerBlocks' => [],
            'innerHTML' => $html,
            'innerContent' => [$html],
        ];
    }

    if (strpos($html, '<blockquote') === 0) {
        return [
            'blockName' => 'core/quote',
            'attrs' => [],
            'innerBlocks' => [],
            'innerHTML' => $html,
            'innerContent' => [$html],
        ];
    }

    if (preg_match('/^<(ol|ul)\b/', $html)) {
        return [
            'blockName' => 'core/list',
            'attrs' => [],
            'innerBlocks' => [],
            'innerHTML' => $html,
            'innerContent' => [$html],
        ];
    }

    if (strpos($html, '<div') === 0) {
        // Already a block (MerPress), return as-is
        return [
            'blockName' => null,
            'attrs' => [],
            'innerBlocks' => [],
            'innerHTML' => $html,
            'innerContent' => [$html],
        ];
    }

    // Default: paragraph
    if (strpos($html, '<p') === 0) {
        return [
            'blockName' => 'core/paragraph',
            'attrs' => [],
            'innerBlocks' => [],
            'innerHTML' => $html,
            'innerContent' => [$html],
        ];
    }

    return null;
}
