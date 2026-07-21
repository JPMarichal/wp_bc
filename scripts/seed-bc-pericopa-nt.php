<?php
/**
 * Seed all pericope definitions from plan-pericopas-nt-epistolas.md into bc_pericopa taxonomy.
 * Run: podman exec -i wp_bc_cli wp eval-file - < scripts/seed-bc-pericopa-nt.php
 * Or local if wp is available.
 */

// We will parse docs/juego-del-cinco/plan-pericopas-nt-epistolas.md line by line.
// Format:
//   ### {Libro} {Capitulo}     e.g. ### Romanos 1, ### 1 Corintios 3
//   | # | Título | Slug | v | Estilo | _evento_canonico | _cita_de |
// The _cita_de column is optional (only present in 2 Pedro tables).

$plans = [
    [
        'file' => '/tmp/seed/plan-pericopas-nt-epistolas.md',
        'fallback' => __DIR__ . '/../docs/juego-del-cinco/plan-pericopas-nt-epistolas.md',
        // Match lines like: ### Romanos 1, ### 1 Corintios 3, ### 2 Pedro 2
        'pattern' => '/^###\s+((?:\d\s+)?[a-zA-ZáéíóúÁÉÍÓÚñÑ]+\s+\d+)/u',
        'prefix' => ''
    ]
];

$terms_parsed = [];

foreach ($plans as $plan) {
    $filePath = file_exists($plan['file']) ? $plan['file'] : $plan['fallback'];
    if (!file_exists($filePath)) {
        echo "ERROR: File not found at {$filePath}\n";
        continue;
    }

    $lines = file($filePath);
    $current_section = null;

    foreach ($lines as $line) {
        if (preg_match($plan['pattern'], $line, $matches)) {
            $current_section = trim($matches[1]);
            continue;
        }

        // Skip non-data rows (header, separator, empty).
        if (!$current_section || !preg_match('/^\|\s*(\d+)\s*\|/', $line)) {
            continue;
        }

        // Split by '|' and trim each cell.
        $cells = array_map('trim', explode('|', $line));
        // After split, the row has empty first and last elements from leading/trailing pipes.
        $cells = array_filter($cells, function($c) { return $c !== ''; });
        $cells = array_values($cells);

        // Expected: [index, title, slug, v_range, estilo, _evento_canonico, _cita_de?]
        if (count($cells) < 6) {
            continue;
        }

        $index = intval($cells[0]);
        $title = $cells[1];
        // Slug is plain (no backticks in our plan). Strip any if present.
        $slug = trim($cells[2], '`');
        $v_range = $cells[3];
        $evento = $cells[5] ?? '';
        $cita_de = $cells[6] ?? '';

        if ($evento === '—' || $evento === '-') {
            $evento = '';
        }
        if ($cita_de === '—' || $cita_de === '-') {
            $cita_de = '';
        }

        // Clean range representation to find start and end.
        // e.g. "1–7", "1-7", "30".
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
            echo "WARN: Cannot parse verse range '{$v_range}' for slug {$slug}. Skipping.\n";
            continue;
        }

        $terms_parsed[] = [
            'chapter_name' => $current_section,
            'title' => $title,
            'slug' => $slug,
            'v_inicio' => $v_inicio,
            'v_fin' => $v_fin,
            '_evento_canonico' => $evento,
            '_cita_de' => $cita_de,
        ];
    }
}

echo "Parsed " . count($terms_parsed) . " pericope terms from NT epistolas plan.\n";

$inserted = 0;
$skipped = 0;
$errors = 0;
$missing_chapters = [];

foreach ($terms_parsed as $data) {
    // 1. Get or confirm the bc_chapter term exists.
    $chapter_term = term_exists($data['chapter_name'], 'bc_chapter');
    if (!$chapter_term) {
        if (!in_array($data['chapter_name'], $missing_chapters)) {
            $missing_chapters[] = $data['chapter_name'];
        }
        $errors++;
        continue;
    }
    $chapter_id = is_array($chapter_term) ? intval($chapter_term['term_id']) : intval($chapter_term);

    // 2. Insert or get pericopa term.
    $pericopa_term = term_exists($data['slug'], 'bc_pericopa');
    if (!$pericopa_term) {
        $result = wp_insert_term($data['title'], 'bc_pericopa', [
            'slug' => $data['slug'],
        ]);

        if (is_wp_error($result)) {
            echo "ERROR: Failed to insert term '{$data['title']}' ({$data['slug']}): " . $result->get_error_message() . "\n";
            $errors++;
            continue;
        }

        $term_id = $result['term_id'];
        $inserted++;
    } else {
        $term_id = is_array($pericopa_term) ? intval($pericopa_term['term_id']) : intval($pericopa_term);
        // Update name and slug in case it changed.
        wp_update_term($term_id, 'bc_pericopa', [
            'name' => $data['title'],
            'slug' => $data['slug'],
        ]);
        $skipped++;
    }

    // 3. Update term metadata.
    update_term_meta($term_id, 'v_inicio', $data['v_inicio']);
    update_term_meta($term_id, 'v_fin', $data['v_fin']);
    update_term_meta($term_id, 'bc_chapter_id', $chapter_id);

    if ($data['_evento_canonico']) {
        update_term_meta($term_id, '_evento_canonico', $data['_evento_canonico']);
    } else {
        delete_term_meta($term_id, '_evento_canonico');
    }

    if ($data['_cita_de']) {
        update_term_meta($term_id, '_cita_de', $data['_cita_de']);
    } else {
        delete_term_meta($term_id, '_cita_de');
    }
}

echo "\n=== Semillero Fase C (NT Epístolas) ===\n";
echo "Creados: {$inserted}\n";
echo "Existentes/Actualizados: {$skipped}\n";
echo "Errores: {$errors}\n";

if (!empty($missing_chapters)) {
    echo "\nCapítulos faltantes en bc_chapter (no se pueden sembrar perícopas hasta que existan):\n";
    foreach (array_unique($missing_chapters) as $ch) {
        echo "  - {$ch}\n";
    }
}
