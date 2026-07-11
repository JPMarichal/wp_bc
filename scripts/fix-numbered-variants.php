<?php
require_once '/var/www/html/wp-load.php';

// Find all numbered variant posts (title ends with " N") that don't already have alias_of
$variants = get_posts(array(
    'post_type'      => 'bc_location',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'meta_query'     => array(
        array(
            'key'     => '_bc_loc_alias_of',
            'compare' => 'NOT EXISTS',
        ),
    ),
    'fields' => 'ids',
));

$processed = 0;
$aliased = 0;
$no_base = 0;
$skipped = 0;

foreach ($variants as $pid) {
    $post = get_post($pid);
    $title = $post->post_title;

    // Check if title ends with space + number
    if (!preg_match('/^(.*)\s+(\d+)$/', $title, $m)) {
        continue;
    }

    $base_name = $m[1];

    // Find all unnumbered posts with this base name (excluding self)
    $bases = get_posts(array(
        'post_type'      => 'bc_location',
        'post_status'    => 'publish',
        'title'          => $base_name,
        'exact'          => true,
        'exclude'        => array($pid),
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ));

    if (empty($bases)) {
        $no_base++;
        continue;
    }

    // Pick the best base: prefer openbible source, then highest confidence, then lowest ID
    $best = null;
    $best_score = -1;
    foreach ($bases as $bid) {
        $source = get_post_meta($bid, '_bc_loc_source', true);
        $confidence = get_post_meta($bid, '_bc_loc_confidence', true);
        $alias = get_post_meta($bid, '_bc_loc_alias_of', true);
        if (!empty($alias)) continue; // skip if base is itself an alias

        $score = 0;
        if ($source === 'openbible') $score += 100;
        if ($source === 'manual') $score += 50;
        if ($confidence === 'high') $score += 30;
        if ($confidence === 'medium') $score += 10;
        $score += 1000 - $bid; // prefer lower ID (older post)

        if ($score > $best_score) {
            $best_score = $score;
            $best = $bid;
        }
    }

    if (!$best) {
        $skipped++;
        continue;
    }

    update_post_meta($pid, '_bc_loc_alias_of', $best);
    echo "ALIAS: $pid ({$post->post_title}) → $best (" . get_post($best)->post_title . ")\n";
    $aliased++;
}

echo "\n--- Also handle duplicate unnumbered bases (same title, no number) ---\n";

// Find unnumbered posts (title doesn't end with space + number) that have duplicates
$unnumbered = get_posts(array(
    'post_type'      => 'bc_location',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'fields'         => 'ids',
));

$by_title = array();
foreach ($unnumbered as $pid) {
    $title = get_post($pid)->post_title;
    if (preg_match('/\s+\d+$/', $title)) continue; // skip numbered
    $clean = strtolower(trim($title));
    $by_title[$clean][] = $pid;
}

$dup_fixed = 0;
foreach ($by_title as $clean_title => $ids) {
    if (count($ids) < 2) continue;
    if (count($ids) > 10) {
        echo "SKIP group '$clean_title' has " . count($ids) . " posts — too many, manual review needed\n";
        continue;
    }

    // Score each to find canonical
    $scored = array();
    foreach ($ids as $pid) {
        $source = get_post_meta($pid, '_bc_loc_source', true);
        $confidence = get_post_meta($pid, '_bc_loc_confidence', true);
        $alias = get_post_meta($pid, '_bc_loc_alias_of', true);
        if (!empty($alias)) continue;

        $score = 0;
        $slug = get_post($pid)->post_name;
        // Prefer clean slug (no -N suffix, no openbible- prefix)
        if (!preg_match('/-\d+$/', $slug)) $score += 20;
        if (strpos($slug, 'openbible-') !== 0) $score += 20;
        if ($source === 'openbible') $score += 100;
        if ($confidence === 'medium') $score += 10;
        if ($confidence === 'high') $score += 30;
        $score += 1000 - $pid;

        $scored[$pid] = $score;
    }

    if (empty($scored)) continue;

    arsort($scored);
    $canonical_id = key($scored);
    array_shift($scored); // remove canonical

    foreach ($scored as $pid => $score) {
        $existing = get_post_meta($pid, '_bc_loc_alias_of', true);
        if (!empty($existing)) continue;
        update_post_meta($pid, '_bc_loc_alias_of', $canonical_id);
        echo "DUP: $pid (" . get_post($pid)->post_title . ") → $canonical_id (" . get_post($canonical_id)->post_title . ")\n";
        $dup_fixed++;
    }
}

echo "\n=== Summary ===\n";
echo "Numbered variants aliased: $aliased\n";
echo "No base found: $no_base\n";
echo "Skipped (base itself is alias): $skipped\n";
echo "Duplicate unnumbered bases aliased: $dup_fixed\n";
