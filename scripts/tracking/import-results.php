<?php
/**
 * Importa resultados de traducción desde translation-results.json
 * y actualiza _bc_loc_name_es + _bc_loc_scripture en WordPress.
 *
 * Uso: docker exec wp_bc_cli wp eval-file scripts/tracking/import-results.php --allow-root
 */

$json_path = '/var/www/html/scripts/tracking/translation-results.json';
$db_path = '/var/www/html/scripts/tracking/locations.db';

if (!file_exists($json_path)) {
    die("ERROR: translation-results.json not found at $json_path\n");
}

$results = json_decode(file_get_contents($json_path), true);
if (!$results) {
    die("ERROR: Failed to parse translation-results.json\n");
}

$db = new SQLite3($db_path);

$updated = 0;
$skipped = 0;
$errors = 0;

foreach ($results as $item) {
    $pid = (int)$item['post_id'];
    $name_es = $item['name_es'];
    $name_en = $item['name_en'];
    $confidence = $item['confidence'];
    $ref = $item['ref'];

    // Skip "none" confidence (no scriptures) - handle in Phase 4
    if ($confidence === 'none') {
        $skipped++;
        continue;
    }

    // Skip if already has ES name
    $existing = get_post_meta($pid, '_bc_loc_name_es', true);
    if (!empty($existing)) {
        $skipped++;
        continue;
    }

    // Check post exists
    $post = get_post($pid);
    if (!$post || $post->post_type !== 'bc_location') {
        echo "ERROR: Post $pid not found or not bc_location\n";
        $errors++;
        continue;
    }

    // Update _bc_loc_name_es
    update_post_meta($pid, '_bc_loc_name_es', $name_es);
    $updated++;

    // Update _bc_loc_scripture with Spanish reference if we have a ref
    if ($ref) {
        $spanish_ref = convertRefToSpanish($ref);
        update_post_meta($pid, '_bc_loc_scripture', $spanish_ref);
    }

    // Update SQLite
    $name_es_esc = SQLite3::escapeString($name_es);
    $db->exec("UPDATE locations SET name_es_meta='$name_es_esc', es_status='done' WHERE post_id=$pid");
    $db->exec("INSERT INTO translation_log (post_id, action, old_value, new_value)
               VALUES ($pid, 'pipeline', '$name_en', '$name_es_esc')");

    if ($updated <= 5 || $updated % 50 === 0) {
        echo "  ID $pid: '$name_en' → '$name_es' ($confidence)\n";
    }
}

// Resumen
$res = $db->querySingle(
    "SELECT
        COUNT(*) as total,
        SUM(CASE WHEN es_status='done' THEN 1 ELSE 0 END) as done,
        SUM(CASE WHEN es_status='pending_translate' THEN 1 ELSE 0 END) as pending
    FROM locations", true
);

echo "\nImport complete: $updated updated, $skipped skipped, $errors errors.\n";
echo "Total: {$res['total']} | Done: {$res['done']} | Pending: {$res['pending']}\n";

$db->close();

// ============================================================
function convertRefToSpanish($ref) {
    $bookMap = [
        'Gen' => 'Génesis', 'Exod' => 'Éxodo', 'Lev' => 'Levítico',
        'Num' => 'Números', 'Deut' => 'Deuteronomio',
        'Josh' => 'Josué', 'Judg' => 'Jueces', 'Ruth' => 'Rut',
        '1Sam' => '1 Samuel', '2Sam' => '2 Samuel',
        '1Kgs' => '1 Reyes', '2Kgs' => '2 Reyes',
        '1Chr' => '1 Crónicas', '2Chr' => '2 Crónicas',
        'Ezra' => 'Esdras', 'Neh' => 'Nehemías', 'Esth' => 'Ester',
        'Job' => 'Job', 'Ps' => 'Salmos', 'Prov' => 'Proverbios',
        'Eccl' => 'Eclesiastés', 'Song' => 'Cantares', 'Isa' => 'Isaías',
        'Jer' => 'Jeremías', 'Lam' => 'Lamentaciones', 'Ezek' => 'Ezequiel',
        'Dan' => 'Daniel', 'Hos' => 'Oseas', 'Joel' => 'Joel',
        'Amos' => 'Amós', 'Obad' => 'Abdías', 'Jonah' => 'Jonás',
        'Mic' => 'Miqueas', 'Nah' => 'Nahum', 'Hab' => 'Habacuc',
        'Zeph' => 'Sofonías', 'Hag' => 'Hageo', 'Zech' => 'Zacarías',
        'Mal' => 'Malaquías',
        'Matt' => 'Mateo', 'Mark' => 'Marcos', 'Luke' => 'Lucas',
        'John' => 'Juan', 'Acts' => 'Hechos', 'Rom' => 'Romanos',
        '1Cor' => '1 Corintios', '2Cor' => '2 Corintios',
        'Gal' => 'Gálatas', 'Eph' => 'Efesios', 'Phil' => 'Filipenses',
        'Col' => 'Colosenses', '1Thess' => '1 Tesalonicenses',
        '2Thess' => '2 Tesalonicenses', '1Tim' => '1 Timoteo',
        '2Tim' => '2 Timoteo', 'Titus' => 'Tito', 'Philem' => 'Filemón',
        'Heb' => 'Hebreos', 'Jas' => 'Santiago', '1Pet' => '1 Pedro',
        '2Pet' => '2 Pedro', '1John' => '1 Juan', '2John' => '2 Juan',
        '3John' => '3 Juan', 'Jude' => 'Judas', 'Rev' => 'Apocalipsis',
        '1Ne' => '1 Nefi', '2Ne' => '2 Nefi', '3Ne' => '3 Nefi',
        '4Ne' => '4 Nefi', 'Mosiah' => 'Mosíah', 'Alma' => 'Alma',
        'Hel' => 'Helamán', 'Morm' => 'Mormón', 'Ether' => 'Éter',
        'Moro' => 'Moroni',
        'DyC' => 'DyC', 'D&C' => 'DyC',
    ];

    // Parse: "Gen 10:10" or "2 Kgs 5:12"
    $parts = explode(' ', $ref);
    if (count($parts) === 3) {
        $book = $parts[0] . ' ' . $parts[1];
        $chap_verse = $parts[2];
    } elseif (count($parts) === 2) {
        $book = $parts[0];
        $chap_verse = $parts[1];
    } else {
        return $ref;
    }

    $esBook = $bookMap[$book] ?? $book;
    return "$esBook $chap_verse";
}
