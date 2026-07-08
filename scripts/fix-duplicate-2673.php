<?php
/**
 * Fix duplicate paragraph in article 2673 (prophetic quotes duplicated)
 */
require_once '/var/www/html/wp-load.php';

global $wpdb;

$post_id = 2673;
$content = $wpdb->get_var($wpdb->prepare(
    "SELECT post_content FROM wp_posts WHERE ID = %d AND post_type = 'post'", $post_id
));

if (!$content) die("Article $post_id not found.\n");

$original = $content;

// The enrichment created a duplicate: the prophetic quotes appear twice.
// The first occurrence is inside the enrichment paragraph, the second is
// leftover from the original paragraph that was only partially replaced.
// Remove the duplicate text and the orphan </p> it leaves behind.

$duplicate_start = '(Mateo 10:15; 11:24).</p>';
$pos_orig = strpos($content, $duplicate_start);

if ($pos_orig === false) {
    die("Pattern not found.\n");
}

// Get the second occurrence of the closing tag (the orphan duplicate)
$after_first = $pos_orig + strlen($duplicate_start);
$pos_dup = strpos($content, $duplicate_start, $after_first);

if ($pos_dup === false) {
    die("Second occurrence not found - may have been fixed already.\n");
}

// The text between the first </p> and the second </p> + close is the orphan
$to_remove = substr($content, $after_first, ($pos_dup + strlen($duplicate_start)) - $after_first);

// Verify this is the duplicate (should contain "Isaías" and not "José Smith")
if (strpos($to_remove, 'José Smith') !== false) {
    die("ERROR: Would remove enrichment content. Aborting.\n");
}
if (strpos($to_remove, 'Isaías') === false) {
    die("ERROR: Duplicate does not contain Isaías. Aborting.\n");
}

$content = str_replace($to_remove, '', $content);

if ($content !== $original) {
    $wpdb->update('wp_posts', ['post_content' => $content], ['ID' => $post_id]);
    echo "Duplicate removed successfully.\n";
} else {
    echo "No changes made.\n";
}

// Verify: count how many times the prophetic quotes appear
$count_isaías = substr_count($content, 'Isaías:');
echo "Occurrences of 'Isaías:' (should be 1): $count_isaías\n";

// verify has_blocks
$saved = $wpdb->get_var("SELECT post_content FROM wp_posts WHERE ID = $post_id");
echo "has_blocks(): " . (has_blocks($saved) ? "YES" : "NO") . "\n";
