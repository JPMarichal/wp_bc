<?php
/**
 * Importa entradas gnosis faltantes a WordPress como bc_location.
 * Infiere feature_type para las que no tienen.
 * Uso: docker exec wp_bc_cli wp eval-file scripts/tracking/import-gnosis.php --allow-root
 */

// --- Config ---
$batch_size = 50;
$file = WP_CONTENT_DIR . '/plugins/bc-scripture-map/data/gnosis-places.json';

// --- Type inference ---
function infer_feature_type($name, $aliases, $verses) {
    $text = strtolower($name . ' ' . implode(' ', $aliases));
    
    // Island patterns
    if (preg_match('/\bisland\b|\bisle\b|\bcoast\b|\bchios\b|\bcrete\b|\bcyprus\b|\bpatmos\b|\bsamos\b|\bmalta\b/i', $text)) {
        return 'island';
    }
    
    // Mountain patterns
    if (preg_match('/\bmount\b|\bmountain\b|\bhill\b|\bheight\b|\bpeak\b/i', $text)) {
        return 'mountain';
    }
    
    // Water patterns
    if (preg_match('/\briver\b|\bbrook\b|\bstream\b|\bspring\b|\bwell\b|\bfountain\b|\bsea\b|\bwater\b|\baqueduct\b|\bcanal\b|\bpool\b|\bflood\b/i', $text)) {
        return 'water';
    }
    
    // Valley patterns
    if (preg_match('/\bvalley\b|\bvale\b|\bplain\b|\bmeadow\b|\bglen\b|\bwadi\b|\braca\b/i', $text)) {
        return 'valley';
    }
    
    // Region patterns
    if (preg_match('/\bwilderness\b|\bdesert\b|\bland of\b|\bregion\b|\bcountry\b|\bplain of\b|\bfield\b|\bterritory\b|\bforest\b|\bdesolate\b/i', $text)) {
        return 'region';
    }
    
    // Landmark patterns
    if (preg_match('/\btower\b|\bgarden\b|\bcave\b|\bgate\b|\baltar\b|\bpillar\b|\bstone\b|\boak\b|\btree\b|\bwell of\b|\bcistern\b|\bbarn\b|\bfort\b|\bcastle\b|\bpalace\b|\btemple\b|\bsynagogue\b|\bchurch\b|\bmonument\b|\btomb\b|\bsepulchre\b|\bgrave\b|\bhigh place\b|\bhighplace\b/i', $text)) {
        return 'landmark';
    }
    
    // Path patterns
    if (preg_match('/\bway\b|\bpath\b|\broad\b|\bhighway\b|\bpass\b/i', $text) && !preg_match('/\bhighway of\b|\bway of\b/i', $text)) {
        return 'path';
    }
    
    // Default to city/settlement
    return 'city';
}

// --- Load data ---
$json = file_get_contents($file);
$gnosis_data = json_decode($json, true);
if (!$gnosis_data) {
    echo "Error: Could not parse gnosis-places.json\n";
    exit(1);
}

// Get existing posts by name_en
$posts = get_posts(array(
    'post_type' => 'bc_location',
    'post_status' => 'publish',
    'posts_per_page' => -1,
));
$existing = [];
foreach ($posts as $p) {
    $name_en = get_post_meta($p->ID, '_bc_loc_name_en', true);
    if (!empty($name_en)) {
        $existing[strtolower(trim($name_en))] = $p->ID;
    } else {
        $existing[strtolower(trim($p->post_title))] = $p->ID;
    }
}

// Also index by gnosis ID (some might have been imported with matching IDs)
$existing_by_id = [];
foreach ($posts as $p) {
    $existing_by_id[$p->post_name] = $p->ID;
}

// --- Process ---
$imported = 0;
$skipped = 0;
$inferred = 0;
$batch = [];
$total_to_import = 0;

// First pass: collect entries to import
$to_import = [];
foreach ($gnosis_data as $key => $entry) {
    $name = $entry['name'] ?? $key;
    $name_lower = strtolower(trim($name));
    
    // Skip if already exists by name_en
    if (isset($existing[$name_lower])) {
        $skipped++;
        continue;
    }
    
    // Skip if already exists by gnosis ID
    if (isset($existing_by_id[$key]) || isset($existing_by_id[sanitize_title($name)])) {
        $skipped++;
        continue;
    }
    
    $to_import[] = ['key' => $key, 'entry' => $entry];
}

$total_to_import = count($to_import);
echo "Found $total_to_import entries to import.\n\n";

// Second pass: import in batches
for ($i = 0; $i < $total_to_import; $i += $batch_size) {
    $batch = array_slice($to_import, $i, $batch_size);
    
    foreach ($batch as $item) {
        $key = $item['key'];
        $entry = $item['entry'];
        $name = $entry['name'] ?? $key;
        
        // Determine feature type
        $ft = $entry['feature_type'] ?? '';
        $fst = $entry['feature_sub_type'] ?? '';
        $aliases = $entry['aliases'] ?? [];
        $verses = $entry['verses'] ?? [];
        
        if (empty($ft)) {
            $ft = infer_feature_type($name, is_array($aliases) ? $aliases : [], $verses);
            $inferred++;
        }
        
        // Map to lowercase bc_loc_type
        $type_map = [
            'City' => 'city',
            'Region' => 'region',
            'Landmark' => 'landmark',
            'Mountain' => 'mountain',
            'Valley' => 'valley',
            'Water' => 'water',
            'Island' => 'island',
            'Path' => 'path',
        ];
        $loc_type = $type_map[$ft] ?? 'city';
        $kjv_name = $entry['kjv_name'] ?? $name;
        
        // Build alt_names from aliases (excluding the main name)
        $alt_names = [];
        if (!empty($aliases) && is_array($aliases)) {
            foreach ($aliases as $a) {
                $a = trim($a);
                if (!empty($a) && strtolower($a) !== strtolower($name) && strtolower($a) !== strtolower($kjv_name)) {
                    $alt_names[] = $a;
                }
            }
        }
        
        // Build scriptures array
        $scriptures = [];
        if (!empty($verses) && is_array($verses)) {
            foreach ($verses as $v) {
                $ref = trim($v);
                if (!empty($ref)) {
                    $ref_std = preg_replace('/^(\d)([A-Z])/', '$1 $2', $ref);
                    $scriptures[] = $ref_std;
                }
            }
        }
        
        // Build slug
        $slug = sanitize_title($name);
        // Avoid collisions
        $base_slug = $slug;
        $counter = 1;
        while (get_page_by_path($slug, OBJECT, 'bc_location')) {
            $slug = $base_slug . '-' . $counter;
            $counter++;
        }
        
        // Prepare post data
        $post_data = array(
            'post_title' => $name,
            'post_name' => $slug,
            'post_type' => 'bc_location',
            'post_status' => 'publish',
            'post_content' => '',
        );
        
        $post_id = wp_insert_post($post_data, true);
        
        if (is_wp_error($post_id)) {
            echo "  Error creating {$name}: " . $post_id->get_error_message() . "\n";
            continue;
        }
        
        // Set meta
        update_post_meta($post_id, '_bc_loc_name_en', $kjv_name);
        update_post_meta($post_id, '_bc_loc_name_es', $name);
        update_post_meta($post_id, '_bc_loc_source', 'gnosis');
        update_post_meta($post_id, '_bc_loc_type', $loc_type);
        
        if (!empty($entry['latitude'])) {
            update_post_meta($post_id, '_bc_loc_lat', (string)$entry['latitude']);
        }
        if (!empty($entry['longitude'])) {
            update_post_meta($post_id, '_bc_loc_lng', (string)$entry['longitude']);
        }
        if (!empty($scriptures)) {
            update_post_meta($post_id, '_bc_loc_scriptures', $scriptures);
        }
        if (!empty($alt_names)) {
            update_post_meta($post_id, '_bc_loc_alt_names', $alt_names);
        }
        
        $imported++;
        
        if ($imported <= 10 || $imported % 100 === 0) {
            $ft_label = $ft ?: 'inferred_' . $loc_type;
            $alias_count = count($alt_names);
            echo "  ID {$post_id}: {$name} ({$loc_type}, {$ft_label}, {$alias_count} aliases, " . count($scriptures) . " refs)\n";
        }
    }
    
    // Free memory
    wp_cache_flush();
}

echo "\n--- Summary ---\n";
echo "Total gnosis in JSON: " . count($gnosis_data) . "\n";
echo "Imported: $imported\n";
echo "Skipped (already exist): $skipped\n";
echo "Types inferred: $inferred\n";

// Count types of imported entries
$imported_posts = get_posts(array(
    'post_type' => 'bc_location',
    'post_status' => 'publish',
    'posts_per_page' => -1,
));
$type_counts = [];
$gnosis_count = 0;
foreach ($imported_posts as $p) {
    $source = get_post_meta($p->ID, '_bc_loc_source', true);
    if ($source === 'gnosis') {
        $gnosis_count++;
        $t = get_post_meta($p->ID, '_bc_loc_type', true);
        $type_counts[$t] = ($type_counts[$t] ?? 0) + 1;
    }
}
echo "\nTotal gnosis locations in WP now: $gnosis_count\n";
echo "By type:\n";
foreach ($type_counts as $t => $c) {
    echo "  $t: $c\n";
}
