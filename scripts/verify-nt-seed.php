<?php
/**
 * Verify NT pericopa seed: count, overlaps, gaps, concordance.
 * Correctly extracts book+chapter from slug by matching the slug prefix
 * against the known bc_chapter names.
 */
$terms = get_terms(['taxonomy' => 'bc_pericopa', 'hide_empty' => false]);

// Build map of chapter name -> (book, chapter_num)
$chapters = get_terms(['taxonomy' => 'bc_chapter', 'hide_empty' => false]);
$ch_map = []; // "1 corintios 3" => ['book'=>'1 corintios', 'cap'=>3]
foreach ($chapters as $ch) {
    if (preg_match('/^(.+?)\s+(\d+)$/', $ch->name, $m)) {
        $ch_map[$m[1] . ' ' . $m[2]] = ['book' => $m[1], 'cap' => intval($m[2]), 'id' => intval($ch->term_id)];
    }
}

// Build map from slug-prefix to chapter: e.g. "1-corintios-3" => chapter data
$slug_prefix_map = [];
foreach ($ch_map as $name => $data) {
    $slug_prefix = str_replace(' ', '-', $name);
    // Normalize: remove tildes, accents
    $slug_prefix = strtolower($slug_prefix);
    $slug_prefix_map[$slug_prefix] = $data;
}

$nt = [];
$errors_meta = 0;
foreach ($terms as $t) {
    $slug = $t->slug;
    // Find the longest matching chapter prefix
    $matched = null;
    foreach ($slug_prefix_map as $prefix => $data) {
        if (strpos($slug, $prefix . '-') === 0) {
            if ($matched === null || strlen($prefix) > strlen($matched['prefix'])) {
                $matched = ['prefix' => $prefix, 'book' => $data['book'], 'cap' => $data['cap']];
            }
        }
    }
    if (!$matched) continue;

    $vi = get_term_meta($t->term_id, 'v_inicio', true);
    $vf = get_term_meta($t->term_id, 'v_fin', true);
    $ch_meta = get_term_meta($t->term_id, 'bc_chapter_id', true);
    if (!$vi || !$vf) { $errors_meta++; continue; }

    $nt[$matched['book']][$matched['cap']][] = [
        'slug' => $slug,
        'vi' => intval($vi),
        'vf' => intval($vf),
        'name' => $t->name,
        'ch_meta' => intval($ch_meta),
    ];
}

$total = 0;
foreach ($nt as $b => $caps) {
    foreach ($caps as $c => $items) {
        $total += count($items);
    }
}
echo "Total NT pericopas: $total\n";
echo "Books: " . count($nt) . "\n";
echo "Errores metadata: $errors_meta\n\n";

// Check overlaps and gaps within each book/chapter
$overlaps = [];
$gaps = [];
foreach ($nt as $b => $caps) {
    foreach ($caps as $c => $items) {
        usort($items, function($a, $b) { return $a['vi'] <=> $b['vi']; });
        $prev_end = 0;
        foreach ($items as $it) {
            if ($prev_end > 0 && $it['vi'] <= $prev_end) {
                $overlaps[] = "$b $c: {$it['slug']} (v{$it['vi']}-v{$it['vf']}) starts at v{$it['vi']} but previous ended at v$prev_end";
            }
            if ($prev_end > 0 && $it['vi'] > $prev_end + 1) {
                $gaps[] = "$b $c: verses " . ($prev_end + 1) . "-" . ($it['vi'] - 1) . " not covered";
            }
            $prev_end = max($prev_end, $it['vf']);
        }
    }
}

echo "=== OVERLAPS ===\n";
if (empty($overlaps)) echo "  Ninguno\n";
foreach ($overlaps as $o) echo "  $o\n";

echo "\n=== GAPS ===\n";
if (empty($gaps)) echo "  Ninguno\n";
foreach ($gaps as $g) echo "  $g\n";

// Concordance Pedro-Judas
echo "\n=== Concordancia Pedro-Judas ===\n";
$pedro2 = [];
$judas1 = [];
foreach ($terms as $t) {
    if (strpos($t->slug, '2-pedro-2-') === 0) $pedro2[] = $t;
    if (strpos($t->slug, 'judas-1-') === 0) $judas1[] = $t;
}
$pedro_eventos = [];
foreach ($pedro2 as $t) {
    $e = get_term_meta($t->term_id, '_evento_canonico', true);
    if ($e) $pedro_eventos[$e] = ($pedro_eventos[$e] ?? 0) + 1;
}
$judas_eventos = [];
foreach ($judas1 as $t) {
    $e = get_term_meta($t->term_id, '_evento_canonico', true);
    if ($e) $judas_eventos[$e] = ($judas_eventos[$e] ?? 0) + 1;
}
$compartidos = array_intersect_key($pedro_eventos, $judas_eventos);
echo "Eventos compartidos: " . count($compartidos) . "\n";
foreach ($compartidos as $e => $c) {
    echo "  $e (2 Pedro: {$pedro_eventos[$e]}, Judas: {$judas_eventos[$e]})\n";
}

$cita_count = 0;
foreach ($terms as $t) {
    if (get_term_meta($t->term_id, '_cita_de', true)) $cita_count++;
}
echo "\nPericopas con _cita_de: $cita_count\n";
