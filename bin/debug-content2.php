<?php
$content = get_post(2589)->post_content;

// Find mermaid blocks
preg_match_all('/<pre class="mermaid">(.*?)<\/pre>/s', $content, $matches);
foreach ($matches[1] as $i => $mermaid) {
    echo "=== Block " . ($i+1) . " ===\n";
    echo "Length: " . strlen($mermaid) . "\n";
    echo "---RAW---\n";
    echo $mermaid;
    echo "\n---HEX---\n";
    echo chunk_split(bin2hex($mermaid), 32, ' ');
    echo "\n\n";
}
