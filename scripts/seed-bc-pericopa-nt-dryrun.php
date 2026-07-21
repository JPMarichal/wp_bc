<?php
/**
 * Dry-run: parse the NT epistolas plan and report stats without writing to DB.
 * Run: podman exec -i wp_bc_cli wp eval-file - < scripts/seed-bc-pericopa-nt-dryrun.php
 */

$filePath = '/tmp/seed/plan.md';
if (!file_exists($filePath)) {
    $filePath = __DIR__ . '/../docs/juego-del-cinco/plan-pericopas-nt-epistolas.md';
}
if (!file_exists($filePath)) {
    echo "ERROR: File not found\n";
    exit(1);
}

$lines = file($filePath);
$current_section = null;
$terms_parsed = [];
$section_pattern = '/^###\s+((?:\d\s+)?[a-zA-ZáéíóúÁÉÍÓÚñÑ]+\s+\d+)/u';

foreach ($lines as $line) {
    if (preg_match($section_pattern, $line, $matches)) {
        $current_section = trim($matches[1]);
        continue;
    }

    if (!$current_section || !preg_match('/^\|\s*(\d+)\s*\|/', $line)) {
        continue;
    }

    $cells = array_map('trim', explode('|', $line));
    $cells = array_filter($cells, function($c) { return $c !== ''; });
    $cells = array_values($cells);

    if (count($cells) < 6) {
        continue;
    }

    $title = $cells[1];
    $slug = trim($cells[2], '`');
    $v_range = $cells[3];
    $evento = $cells[5] ?? '';
    $cita_de = $cells[6] ?? '';

    $v_inicio = 0;
    $v_fin = 0;
    $clean_range = str_replace(['–', '—'], '-', $v_range);
    if (preg_match('/^(\d+)-(\d+)$/', $clean_range, $r_matches)) {
        $v_inicio = intval($r_matches[1]);
        $v_fin = intval($r_matches[2]);
    } elseif (preg_match('/^(\d+)$/', $clean_range, $r_matches)) {
        $v_inicio = intval($r_matches[1]);
        $v_fin = intval($r_matches[1]);
    } else {
        echo "WARN: Cannot parse verse range '{$v_range}' for slug {$slug}\n";
        continue;
    }

    $terms_parsed[] = [
        'chapter' => $current_section,
        'title' => $title,
        'slug' => $slug,
        'v_range' => $v_range,
        'v_inicio' => $v_inicio,
        'v_fin' => $v_fin,
        '_evento_canonico' => $evento,
        '_cita_de' => $cita_de,
    ];
}

echo "Total parsed: " . count($terms_parsed) . "\n\n";

// Group by chapter
$by_chapter = [];
foreach ($terms_parsed as $t) {
    $by_chapter[$t['chapter']][] = $t;
}

echo "Perícopas por capítulo:\n";
foreach ($by_chapter as $ch => $items) {
    $count = count($items);
    $verses = [];
    foreach ($items as $it) {
        $verses[] = "{$it['v_inicio']}-{$it['v_fin']}";
    }
    printf("  %-30s %3d perícopas [%s]\n", $ch, $count, implode(', ', $verses));
}

// Check for verse gaps/overlaps within each chapter
echo "\nValidación de cobertura versicular (por capítulo):\n";
$issues = 0;
foreach ($by_chapter as $ch => $items) {
    usort($items, function($a, $b) {
        return $a['v_inicio'] <=> $b['v_inicio'];
    });
    $prev_end = 0;
    foreach ($items as $it) {
        if ($prev_end > 0 && $it['v_inicio'] <= $prev_end) {
            echo "  OVERLAP in {$ch}: previous ends at v{$prev_end}, current starts at v{$it['v_inicio']}\n";
            $issues++;
        }
        if ($prev_end > 0 && $it['v_inicio'] > $prev_end + 1) {
            echo "  GAP in {$ch}: between v{$prev_end} and v{$it['v_inicio']}\n";
            // Gaps are not always errors (chapter may end before next starts in non-1-25 cases)
        }
        $prev_end = $it['v_fin'];
    }
}

if ($issues === 0) {
    echo "  Sin solapamientos detectados.\n";
}

// Unique event counts
$eventos = [];
foreach ($terms_parsed as $t) {
    if ($t['_evento_canonico']) {
        $e = $t['_evento_canonico'];
        $eventos[$e] = ($eventos[$e] ?? 0) + 1;
    }
}
echo "\nEventos canónicos únicos: " . count($eventos) . "\n";

// Title uniqueness
$titles = [];
$dup_titles = [];
foreach ($terms_parsed as $t) {
    $key = mb_strtolower($t['title']);
    if (isset($titles[$key])) {
        $dup_titles[$key] = ($dup_titles[$key] ?? 1) + 1;
    } else {
        $titles[$key] = 1;
    }
}
echo "\nTítulos únicos: " . count($titles) . " (de " . count($terms_parsed) . " total)\n";
if (!empty($dup_titles)) {
    echo "Títulos duplicados:\n";
    foreach ($dup_titles as $t => $c) {
        echo "  '{$t}' aparece {$c} veces\n";
    }
}

// Slug uniqueness
$slugs = [];
$dup_slugs = [];
foreach ($terms_parsed as $t) {
    if (isset($slugs[$t['slug']])) {
        $dup_slugs[$t['slug']] = ($dup_slugs[$t['slug']] ?? 1) + 1;
    } else {
        $slugs[$t['slug']] = 1;
    }
}
if (!empty($dup_slugs)) {
    echo "\nSlugs duplicados:\n";
    foreach ($dup_slugs as $s => $c) {
        echo "  '{$s}' aparece {$c} veces\n";
    }
} else {
    echo "Slugs únicos: OK\n";
}
