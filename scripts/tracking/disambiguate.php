<?php
/**
 * Puebla _bc_loc_disambiguation para ubicaciones homónimas.
 * Uso: docker exec wp_bc_cli wp eval-file scripts/tracking/disambiguate.php --allow-root
 */

$posts = get_posts(array(
    'post_type' => 'bc_location',
    'post_status' => 'publish',
    'posts_per_page' => -1,
));

// Agrupar por título base (sin número)
$groups = [];
foreach ($posts as $p) {
    $title = $p->post_title;
    $base = preg_replace('/\s+\d+$/', '', $title);
    $groups[$base][] = $p;
}
$homonyms = array_filter($groups, function($g) { return count($g) > 1; });

$updated = 0;
$skipped = 0;

foreach ($homonyms as $base => $entries) {
    usort($entries, function($a, $b) { return $a->ID - $b->ID; });
    
    foreach ($entries as $p) {
        $existing = get_post_meta($p->ID, '_bc_loc_disambiguation', true);
        if (!empty($existing)) {
            $skipped++;
            continue;
        }
        
        $title = $p->post_title;
        $source = get_post_meta($p->ID, '_bc_loc_source', true) ?: '';
        $type = get_post_meta($p->ID, '_bc_loc_type', true) ?: '';
        $name_en = get_post_meta($p->ID, '_bc_loc_name_en', true) ?: '';
        $slug = $p->post_name;
        
        $disamb = '';
        
        // Extract number suffix if any
        $num = '';
        if (preg_match('/\s+(\d+)$/', $title, $m)) {
            $num = $m[1];
        }
        
        if ($source === 'book-of-mormon') {
            $disamb = 'América / Libro de Mormón';
        } elseif ($source === 'manual') {
            $disamb = 'Identificación manual';
        } elseif ($source === 'church-history') {
            $disamb = 'Historia SUD';
        } elseif ($num === '2') {
            $disamb = 'Posible ubicación alternativa de ' . $base;
        } elseif ($num === '3') {
            $disamb = 'Otra posible ubicación de ' . $base;
        } elseif ($type === 'river' || $type === 'sea') {
            // Differentiate by type
            foreach ($entries as $other) {
                if ($other->ID !== $p->ID) {
                    $other_type = get_post_meta($other->ID, '_bc_loc_type', true);
                    $other_src = get_post_meta($other->ID, '_bc_loc_source', true);
                    if ($other_type !== $type) {
                        $other_type_es = ($other_type === 'city') ? 'ciudad' : ($other_type ?: $other_type);
                        $type_es = ($type === 'river') ? 'río' : (($type === 'sea') ? 'mar' : $type);
                        $disamb = ucfirst($type_es) . " (diferenciar de {$other_type_es} de {$other_src})";
                    }
                }
            }
            if (empty($disamb)) {
                $disamb = "Datos de {$source}";
            }
        } elseif ($source === 'openbible') {
            $disamb = 'Referencia bíblica (openbible)';
        } elseif ($source === 'gnosis') {
            $disamb = 'Referencia arqueológica (gnosis)';
        } else {
            $disamb = "Fuente: {$source}";
        }
        
        if (!empty($disamb)) {
            update_post_meta($p->ID, '_bc_loc_disambiguation', $disamb);
            $updated++;
            echo "  ID {$p->ID}: \"{$title}\" → \"{$disamb}\"\n";
        } else {
            $skipped++;
        }
    }
}

echo "\nDisambiguation complete: $updated updated, $skipped skipped.\n";
