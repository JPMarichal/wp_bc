<?php
$post_id = 2616;
$content = get_post($post_id)->post_content;

$parts = preg_split('/(<h[1-6]\b[^>]*>.*?<\/h[1-6]>|<p\b[^>]*>.*?<\/p>|<blockquote\b[^>]*>.*?<\/blockquote>|<ul\b[^>]*>.*?<\/ul>|<ol\b[^>]*>.*?<\/ol>)/is', $content, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

echo "Number of parts: " . count($parts) . "\n";
foreach ($parts as $i => $part) {
    $tag = strip_tags(substr($part, 0, 60));
    echo "Part $i: " . substr($part, 0, 40) . "... => $tag\n";
}
