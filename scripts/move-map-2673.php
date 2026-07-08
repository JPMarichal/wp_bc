<?php
/**
 * Move the bc/scripture-map block in article 2673
 * from its current position to before the H2 "El valle de Sidim".
 */
require_once '/var/www/html/wp-load.php';

global $wpdb;

$post_id = 2673;

$content = $wpdb->get_var($wpdb->prepare(
    "SELECT post_content FROM wp_posts WHERE ID = %d AND post_type = 'post'", $post_id
));
if (!$content) die("Article $post_id not found.\n");

$blocks = parse_blocks($content);

// Extract map block + rebuild without it
$map_block = null;
$others = [];
foreach ($blocks as $b) {
    if ($b['blockName'] === 'bc/scripture-map') {
        $map_block = $b;
    } else {
        $others[] = $b;
    }
}

if (!$map_block) die("Map block not found.\n");

// Find the index of the Sidim H2 in the remaining blocks
$target_idx = null;
foreach ($others as $i => $b) {
    if ($b['blockName'] === 'core/heading'
        && strpos($b['innerHTML'] ?? '', 'El valle de Sidim') !== false) {
        $target_idx = $i;
        break;
    }
}

if ($target_idx === null) die("Target H2 not found.\n");

// Insert map block before the target H2
array_splice($others, $target_idx, 0, [$map_block]);

$new_content = serialize_blocks($others);

$wpdb->update('wp_posts', ['post_content' => $new_content], ['ID' => $post_id]);
echo "Map block moved to before 'El valle de Sidim' H2.\n";

// Verify
$saved = $wpdb->get_var("SELECT post_content FROM wp_posts WHERE ID = $post_id");
echo "has_blocks(): " . (has_blocks($saved) ? "YES" : "NO") . "\n";

$parsed = parse_blocks($saved);
foreach ($parsed as $i => $b) {
    if ($b['blockName'] === 'bc/scripture-map') {
        echo "Map block now at index: $i\n";
    }
    if ($b['blockName'] === 'core/heading'
        && strpos($b['innerHTML'] ?? '', 'El valle de Sidim') !== false) {
        echo "Sidim H2 at index: $i\n";
    }
}
