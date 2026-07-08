<?php
/**
 * Fix General Authority titles in article 2681:
 * - Never refer to a GA by last name only
 * - Always: "el presidente Holland", "el élder Maxwell", etc.
 */
require_once '/var/www/html/wp-load.php';

global $wpdb;

$post_id = 2681;
$content = $wpdb->get_var($wpdb->prepare(
    "SELECT post_content FROM wp_posts WHERE ID = %d", $post_id
));
if (!$content) die("Article $post_id not found.\n");

$original = $content;

// Fix Holland references in body text
// After comma: "sugiere Holland," → "sugiere el presidente Holland,"
$content = str_replace('sugiere Holland,', 'sugiere el presidente Holland,', $content);

// After period: ". Holland identifica" → ". El presidente Holland identifica"
$content = str_replace('. Holland identifica', '. El presidente Holland identifica', $content);
$content = str_replace('. Holland resume', '. El presidente Holland resume', $content);
$content = str_replace('. Holland lo expresa', '. El presidente Holland lo expresa', $content);

// "análisis de Holland" → "análisis del presidente Holland"
$content = str_replace('análisis de Holland', 'análisis del presidente Holland', $content);

// "escribe Holland" → "escribe el presidente Holland"
$content = str_replace('escribe Holland', 'escribe el presidente Holland', $content);

// "Como dice Holland" → "Como dice el presidente Holland"
$content = str_replace('dice Holland', 'dice el presidente Holland', $content);

// "Como advierte Holland" → "Como advierte el presidente Holland"
$content = str_replace('advierte Holland', 'advierte el presidente Holland', $content);

// Fix Maxwell reference
$content = str_replace(', Maxwell dijo', ', el élder Maxwell dijo', $content);

// Fix sources list
// "<li>Jeffrey R. Holland" → "<li>presidente Jeffrey R. Holland"
$content = str_replace('<li>Jeffrey R. Holland', '<li>presidente Jeffrey R. Holland', $content);
// "<li>Neal A. Maxwell" → "<li>élder Neal A. Maxwell"
$content = str_replace('<li>Neal A. Maxwell', '<li>élder Neal A. Maxwell', $content);
// "citado en Jeffrey R. Holland" → "citado en el presidente Jeffrey R. Holland"
$content = str_replace('en Jeffrey R. Holland', 'en el presidente Jeffrey R. Holland', $content);

// Prevent double prefix: "el presidente el presidente" → "el presidente"
$content = str_replace('el presidente el presidente', 'el presidente', $content);
$content = str_replace('el élder el élder', 'el élder', $content);

if ($content === $original) {
    echo "No changes made.\n";
    exit;
}

$wpdb->update('wp_posts', ['post_content' => $content], ['ID' => $post_id]);
echo "Titles fixed.\n";

// Verify
$saved = $wpdb->get_var("SELECT post_content FROM wp_posts WHERE ID = $post_id");
echo "has_blocks(): " . (has_blocks($saved) ? "YES" : "NO") . "\n";

// Count references
$holland_plain = preg_match_all('/\bHolland\b(?!([^(]*\)))/', $saved, $m);
$holland_title = preg_match_all('/presidente Holland/', $saved, $m);
$maxwell_plain = preg_match_all('/\bMaxwell\b/', $saved, $m);
$maxwell_title = preg_match_all('/élder Maxwell/', $saved, $m);

echo "Bare 'Holland': $holland_plain\n";
echo "'presidente Holland': $holland_title\n";
echo "Bare 'Maxwell': $maxwell_plain\n";
echo "'élder Maxwell': $maxwell_title\n";
