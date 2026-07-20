<?php
/**
 * Seed all pericope definitions from plan-pericopas-dyc.md into bc_pericopa taxonomy.
 * Run: podman exec -i wp_bc_cli wp eval-file - < scripts/seed-bc-pericopa.php
 * Or local if wp is available.
 */

// We will parse docs/juego-del-cinco/plan-pericopas-dyc.md line by line.
// We need to find:
// 1. "### Sección {num}" to know the current chapter context.
// 2. "| {index} | {title} | `{slug}` | {range} | {event} | {notes} |" to parse terms.

$filePath = '/tmp/plan-pericopas-dyc.md';
if (!file_exists($filePath)) {
    // Fallback to local workspace structure
    $filePath = __DIR__ . '/../docs/juego-del-cinco/plan-pericopas-dyc.md';
}
if (!file_exists($filePath)) {
    echo "ERROR: File not found at {$filePath}\n";
    exit(1);
}

$lines = file($filePath);
$current_section = null;
$terms_parsed = [];

foreach ($lines as $line) {
    if (preg_match('/^###\s+Sección\s+(\d+)/i', $line, $matches)) {
        $current_section = "DyC " . $matches[1];
        continue;
    }

    // Match rows like:
    // | 1 | Voz de amonestación para todo pueblo | `dyc-1-voz-amonestacion-todo-pueblo` | 1–7 | — |  |
    if ($current_section && preg_match('/^\|\s*(\d+)\s*\|\s*([^|]+?)\s*\|\s*`([^`]+)`\s*\|\s*([^|]+?)\s*\|\s*([^|]*?)\s*\|/', $line, $matches)) {
        $index = intval($matches[1]);
        $title = trim($matches[2]);
        $slug = trim($matches[3]);
        $v_range = trim($matches[4]);
        $evento = trim($matches[5]);

        if ($evento === '—') {
            $evento = '';
        }

        // Clean range representation to find start and end
        // e.g. "1–7", "50–50", "30"
        $v_inicio = 0;
        $v_fin = 0;
        $clean_range = str_replace(['–', '-'], '-', $v_range); // Standardize dash
        if (preg_match('/^(\d+)-(\d+)$/', $clean_range, $r_matches)) {
            $v_inicio = intval($r_matches[1]);
            $v_fin = intval($r_matches[2]);
        } elseif (preg_match('/^(\d+)$/', $clean_range, $r_matches)) {
            $v_inicio = intval($r_matches[1]);
            $v_fin = intval($r_matches[1]);
        }

        $terms_parsed[] = [
            'chapter_name' => $current_section,
            'title' => $title,
            'slug' => $slug,
            'v_inicio' => $v_inicio,
            'v_fin' => $v_fin,
            '_evento_canonico' => $evento,
        ];
    }
}

echo "Parsed " . count($terms_parsed) . " pericope terms from plan.\n";

$inserted = 0;
$skipped = 0;
$errors = 0;

foreach ($terms_parsed as $data) {
    // 1. Get or confirm the bc_chapter term exists
    $chapter_term = term_exists($data['chapter_name'], 'bc_chapter');
    if (!$chapter_term) {
        echo "ERROR: Chapter '{$data['chapter_name']}' does not exist in bc_chapter taxonomy. Run scriptures seed first.\n";
        $errors++;
        continue;
    }
    $chapter_id = is_array($chapter_term) ? intval($chapter_term['term_id']) : intval($chapter_term);

    // 2. Insert or get pericopa term
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
        // Update name in case it changed
        wp_update_term($term_id, 'bc_pericopa', [
            'name' => $data['title']
        ]);
        $skipped++;
    }

    // 3. Update term metadata
    update_term_meta($term_id, 'v_inicio', $data['v_inicio']);
    update_term_meta($term_id, 'v_fin', $data['v_fin']);
    update_term_meta($term_id, 'bc_chapter_id', $chapter_id); // Relate to chapter explicitly

    if ($data['_evento_canonico']) {
        update_term_meta($term_id, '_evento_canonico', $data['_evento_canonico']);
    } else {
        delete_term_meta($term_id, '_evento_canonico');
    }
}

echo "\nSemillero completado.\n";
echo "Creados: {$inserted}\n";
echo "Existentes/Actualizados: {$skipped}\n";
echo "Errores: {$errors}\n";
