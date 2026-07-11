<?php
/**
 * Completa alt_names, confidence, icon, alias_of para todas las bc_location.
 * Uso: docker exec wp_bc_cli wp eval-file scripts/tracking/complete-fields.php --allow-root
 */

$file = WP_CONTENT_DIR . '/plugins/bc-scripture-map/data/gnosis-places.json';
$gnosis_data = json_decode(file_get_contents($file), true);

$posts = get_posts(array(
    'post_type' => 'bc_location',
    'post_status' => 'publish',
    'posts_per_page' => -1,
));

$stats = [
    'alt_names_filled' => 0, 'alt_names_skipped' => 0,
    'confidence_filled' => 0, 'confidence_skipped' => 0,
    'icon_filled' => 0, 'icon_skipped' => 0,
    'alias_of_set' => 0, 'alias_of_skipped' => 0,
];

// Index posts by title+source for alias matching
$by_title = [];
foreach ($posts as $p) {
    $t = strtolower(trim($p->post_title));
    $by_title[$t][] = $p;
}

// ================================================================
// 1. ALT_NAMES
// ================================================================
echo "=== 1. ALT_NAMES ===\n";

foreach ($posts as $p) {
    $alt = get_post_meta($p->ID, '_bc_loc_alt_names', true);
    if (!empty($alt)) {
        $stats['alt_names_skipped']++;
        continue;
    }

    $name_en = get_post_meta($p->ID, '_bc_loc_name_en', true) ?: $p->post_title;
    $source = get_post_meta($p->ID, '_bc_loc_source', true);
    $title = $p->post_title;
    $candidates = [];

    // 1a) Gnosis: check KJV/ESV name variants in JSON
    if ($source === 'gnosis') {
        foreach ($gnosis_data as $key => $entry) {
            if (strtolower($entry['name'] ?? '') === strtolower($name_en)) {
                $kjv = $entry['kjv_name'] ?? '';
                $esv = $entry['esv_name'] ?? '';
                $gnosis_aliases = $entry['aliases'] ?? [];
                foreach ($gnosis_aliases as $a) {
                    $a = trim($a);
                    if (!empty($a) && strtolower($a) !== strtolower($name_en) && strtolower($a) !== strtolower($title)) {
                        $candidates[] = $a;
                    }
                }
                if (!empty($kjv) && $kjv !== $name_en && $kjv !== $title) $candidates[] = $kjv;
                if (!empty($esv) && $esv !== $name_en && $esv !== $kjv && $esv !== $title) $candidates[] = $esv;
                break;
            }
        }
    }

    // 1b) Transliteration variants for all
    // th → t
    if (strpos($title, 'th') !== false) {
        $var = str_replace('th', 't', $title);
        if ($var !== $title) $candidates[] = $var;
    }
    // ph → f
    if (strpos($title, 'ph') !== false) {
        $var = str_replace('ph', 'f', $title);
        if ($var !== $title) $candidates[] = $var;
    }
    // -ah → -a
    if (preg_match('/ah$/i', $title)) {
        $var = preg_replace('/ah$/i', 'a', $title);
        if ($var !== $title) $candidates[] = $var;
    }
    // Beth- → Bet-
    if (preg_match('/\bBeth-/i', $title)) {
        $var = preg_replace('/\bBeth-/i', 'Bet-', $title);
        if ($var !== $title) $candidates[] = $var;
    }
    // -oth → -ot
    if (preg_match('/oth$/i', $title)) {
        $var = preg_replace('/oth$/i', 'ot', $title);
        if ($var !== $title) $candidates[] = $var;
    }
    // ch → c (medial/final)
    if (preg_match('/ch/i', $title) && !preg_match('/^Ch/i', $title)) {
        $var = str_ireplace('ch', 'c', $title);
        if ($var !== $title) $candidates[] = $var;
    }
    // y → i (medial)
    if (preg_match('/y/i', $title) && !preg_match('/^Y/i', $title)) {
        $var = preg_replace('/y/i', 'i', $title);
        if ($var !== $title) $candidates[] = $var;
    }

    // 1c) Openbible: add name_en if differs from title
    if ($source === 'openbible' && $name_en !== $title && !empty($name_en)) {
        $candidates[] = $name_en;
    }

    // Deduplicate and filter
    $unique = array_values(array_unique(array_filter($candidates, function($v) use ($title, $name_en) {
        return $v !== $title && strtolower($v) !== strtolower($name_en);
    })));

    if (!empty($unique)) {
        update_post_meta($p->ID, '_bc_loc_alt_names', array_slice($unique, 0, 5));
        $stats['alt_names_filled']++;
    } else {
        $stats['alt_names_skipped']++;
    }
}

echo "  Filled: {$stats['alt_names_filled']}, Skipped: {$stats['alt_names_skipped']}\n";

// ================================================================
// 2. CONFIDENCE
// ================================================================
echo "\n=== 2. CONFIDENCE ===\n";

foreach ($posts as $p) {
    $conf = get_post_meta($p->ID, '_bc_loc_confidence', true);
    if (!empty($conf)) {
        $stats['confidence_skipped']++;
        continue;
    }
    $source = get_post_meta($p->ID, '_bc_loc_source', true);
    $scriptures = get_post_meta($p->ID, '_bc_loc_scriptures', true);

    if ($source === 'openbible' || $source === 'manual') {
        $conf = !empty($scriptures) ? 'high' : 'medium';
    } elseif ($source === 'gnosis') {
        $conf = !empty($scriptures) ? 'medium' : 'low';
    } elseif ($source === 'church-history') {
        $conf = 'medium';
    } elseif ($source === 'book-of-mormon') {
        $conf = 'medium';
    } else {
        $conf = 'low';
    }
    update_post_meta($p->ID, '_bc_loc_confidence', $conf);
    $stats['confidence_filled']++;
}

echo "  Filled: {$stats['confidence_filled']}, Skipped: {$stats['confidence_skipped']}\n";

// ================================================================
// 3. ICON (from type)
// ================================================================
echo "\n=== 3. ICON ===\n";

$icon_map = [
    'city' => 'pin',
    'region' => 'region',
    'mountain' => 'mountain',
    'valley' => 'valley',
    'water' => 'water',
    'island' => 'island',
    'landmark' => 'landmark',
    'path' => 'path',
];

foreach ($posts as $p) {
    $icon = get_post_meta($p->ID, '_bc_loc_icon', true);
    if (!empty($icon)) {
        $stats['icon_skipped']++;
        continue;
    }
    $type = get_post_meta($p->ID, '_bc_loc_type', true);
    $title = $p->post_title;

    // Temple override for church-history
    if (stripos($title, 'templo') !== false || stripos($title, 'Temple') !== false) {
        $icon = 'temple';
    } else {
        $icon = $icon_map[$type] ?? 'default';
    }
    update_post_meta($p->ID, '_bc_loc_icon', $icon);
    $stats['icon_filled']++;
}

echo "  Filled: {$stats['icon_filled']}, Skipped: {$stats['icon_skipped']}\n";

// ================================================================
// 4. ALIAS_OF (cross-source duplicates)
// ================================================================
echo "\n=== 4. ALIAS_OF ===\n";

// Priority order: openbible > manual > church-history > book-of-mormon > gnosis
$priority = ['openbible' => 0, 'manual' => 1, 'church-history' => 2, 'book-of-mormon' => 3, 'gnosis' => 4];

// Process same-title groups
foreach ($by_title as $t => $list) {
    if (count($list) < 2) continue;

    // Find the primary (highest priority source)
    usort($list, function($a, $b) use ($priority) {
        $sa = get_post_meta($a->ID, '_bc_loc_source', true);
        $sb = get_post_meta($b->ID, '_bc_loc_source', true);
        $pa = $priority[$sa] ?? 99;
        $pb = $priority[$sb] ?? 99;
        if ($pa !== $pb) return $pa - $pb;
        return $a->ID - $b->ID;
    });

    $primary = $list[0];
    $has_alias = false;

    // Don't create aliases for Jerusalén BOM → Church History (they're different places)
    $skip_pairs = [];
    $s1 = get_post_meta($primary->ID, '_bc_loc_source', true);
    $t1 = strtolower($primary->post_title);
    if ($t1 === 'jerusalén' && $s1 === 'church-history') {
        // The BOM Jerusalén is a different place
        continue;
    }

    // Check if primary already has an alias pointing to it
    foreach ($list as $p) {
        $ao = get_post_meta($p->ID, '_bc_loc_alias_of', true);
        if (!empty($ao)) $has_alias = true;
    }

    if ($has_alias) continue; // Already configured

    // Set aliases for all non-primary entries
    for ($i = 1; $i < count($list); $i++) {
        $secondary = $list[$i];
        $ao = get_post_meta($secondary->ID, '_bc_loc_alias_of', true);
        if (!empty($ao)) continue;

        // Skip if same source (gnosis+gnosis duplicates are coordinate variants, not aliases)
        $ss = get_post_meta($secondary->ID, '_bc_loc_source', true);
        $ps = get_post_meta($primary->ID, '_bc_loc_source', true);
        if ($ss === $ps && $ss === 'gnosis') continue;

        update_post_meta($secondary->ID, '_bc_loc_alias_of', $primary->ID);
        $stats['alias_of_set']++;
        echo "  \"{$secondary->post_title}\" (ID {$secondary->ID}, {$ss}) → \"{$primary->post_title}\" (ID {$primary->ID}, {$ps})\n";
    }
}

// Special: Sión → Jerusalén (church-history)
$zion = get_posts(array('post_type' => 'bc_location', 'post_status' => 'publish', 'title' => 'Sión', 'posts_per_page' => 1));
$jer = get_posts(array('post_type' => 'bc_location', 'post_status' => 'publish', 'posts_per_page' => 1, 'meta_key' => '_bc_loc_source', 'meta_value' => 'church-history'));
if (!empty($zion) && !empty($jer)) {
    $z = $zion[0];
    $j = $jer[0];
    $ao = get_post_meta($z->ID, '_bc_loc_alias_of', true);
    if (empty($ao) && $z->ID !== $j->ID) {
        update_post_meta($z->ID, '_bc_loc_alias_of', $j->ID);
        $stats['alias_of_set']++;
        echo "  \"Sión\" (ID {$z->ID}) → \"{$j->post_title}\" (ID {$j->ID})\n";
    }
}

echo "  Total alias_of set: {$stats['alias_of_set']}\n";

// ================================================================
// SUMMARY
// ================================================================
echo "\n========================================\n";
echo "SUMMARY:\n";
echo "  alt_names: {$stats['alt_names_filled']} filled, {$stats['alt_names_skipped']} skipped\n";
echo "  confidence: {$stats['confidence_filled']} filled, {$stats['confidence_skipped']} skipped\n";
echo "  icon: {$stats['icon_filled']} filled, {$stats['icon_skipped']} skipped\n";
echo "  alias_of: {$stats['alias_of_set']} new relations\n";
echo "========================================\n";
