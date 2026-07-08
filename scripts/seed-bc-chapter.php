<?php
/**
 * Seed all 89 books + chapters into bc_chapter taxonomy.
 * Run: wp eval-file scripts/seed-bc-chapter.php
 */

// Book definitions: [name, chapter_count] or [name, "label"] for non-numeric children
$books = [
  // ---- Antiguo Testamento (39) ----
  ["Génesis", 50],
  ["Éxodo", 40],
  ["Levítico", 27],
  ["Números", 36],
  ["Deuteronomio", 34],
  ["Josué", 24],
  ["Jueces", 21],
  ["Rut", 4],
  ["1 Samuel", 31],
  ["2 Samuel", 24],
  ["1 Reyes", 22],
  ["2 Reyes", 25],
  ["1 Crónicas", 29],
  ["2 Crónicas", 36],
  ["Esdras", 10],
  ["Nehemías", 13],
  ["Ester", 10],
  ["Job", 42],
  ["Salmos", 150],
  ["Proverbios", 31],
  ["Eclesiastés", 12],
  ["Cantares", 8],
  ["Isaías", 66],
  ["Jeremías", 52],
  ["Lamentaciones", 5],
  ["Ezequiel", 48],
  ["Daniel", 12],
  ["Oseas", 14],
  ["Joel", 3],
  ["Amós", 9],
  ["Abdías", 1],
  ["Jonás", 4],
  ["Miqueas", 7],
  ["Nahum", 3],
  ["Habacuc", 3],
  ["Sofonías", 3],
  ["Hageo", 2],
  ["Zacarías", 14],
  ["Malaquías", 4],

  // ---- Nuevo Testamento (27) ----
  ["Mateo", 28],
  ["Marcos", 16],
  ["Lucas", 24],
  ["Juan", 21],
  ["Hechos", 28],
  ["Romanos", 16],
  ["1 Corintios", 16],
  ["2 Corintios", 13],
  ["Gálatas", 6],
  ["Efesios", 6],
  ["Filipenses", 4],
  ["Colosenses", 4],
  ["1 Tesalonicenses", 5],
  ["2 Tesalonicenses", 3],
  ["1 Timoteo", 6],
  ["2 Timoteo", 4],
  ["Tito", 3],
  ["Filemón", 1],
  ["Hebreos", 13],
  ["Santiago", 5],
  ["1 Pedro", 5],
  ["2 Pedro", 3],
  ["1 Juan", 5],
  ["2 Juan", 1],
  ["3 Juan", 1],
  ["Judas", 1],
  ["Apocalipsis", 22],

  // ---- Libro de Mormón (15) ----
  ["1 Nefi", 22],
  ["2 Nefi", 33],
  ["Jacob", 7],
  ["Enós", 1],
  ["Jarom", 1],
  ["Omni", 1],
  ["Palabras de Mormón", 1],
  ["Mosíah", 29],
  ["Alma", 63],
  ["Helamán", 16],
  ["3 Nefi", 30],
  ["4 Nefi", 1],
  ["Mormón", 9],
  ["Éter", 15],
  ["Moroni", 10],

  // ---- Doctrina y Convenios (2 "libros") ----
  ["Doctrina y Convenios", 138],
  ["Declaraciones Oficiales", ["Declaración Oficial 1", "Declaración Oficial 2"]],

  // ---- Perla de Gran Precio (6) ----
  ["Moisés", 8],
  ["Abraham", 5],
  ["José Smith—Mateo", 1],
  ["José Smith—Historia", 1],
  ["Artículos de Fe", 1],
  ["Apéndice de la Traducción de José Smith", 1],
];

$total_parents = 0;
$total_children = 0;
$errors = 0;

foreach ($books as $book) {
  $book_name = $book[0];
  $chapters = $book[1];

  // Create parent (book)
  $parent = term_exists($book_name, "bc_chapter");
  if (!$parent) {
    $parent = wp_insert_term($book_name, "bc_chapter", [
      "slug" => sanitize_title($book_name),
    ]);
  }
  if (is_wp_error($parent)) {
    echo "ERROR creating book '{$book_name}': {$parent->get_error_message()}\n";
    $errors++;
    continue;
  }
  $parent_id = is_array($parent) ? $parent["term_id"] : $parent["term_id"];
  $total_parents++;

  // Create children (chapters)
  if (is_array($chapters)) {
    // Custom chapter names
    foreach ($chapters as $ch_name) {
      $exists = term_exists($ch_name, "bc_chapter");
      if (!$exists) {
        $result = wp_insert_term($ch_name, "bc_chapter", [
          "slug"   => sanitize_title($ch_name),
          "parent" => $parent_id,
        ]);
        if (is_wp_error($result)) {
          echo "  ERROR: {$ch_name} - {$result->get_error_message()}\n";
          $errors++;
        } else {
          $total_children++;
        }
      }
    }
  } else {
    // Numeric chapters
    for ($i = 1; $i <= $chapters; $i++) {
      $ch_name = "{$book_name} {$i}";
      $exists = term_exists($ch_name, "bc_chapter");
      if (!$exists) {
        $result = wp_insert_term($ch_name, "bc_chapter", [
          "slug"   => sanitize_title($ch_name),
          "parent" => $parent_id,
        ]);
        if (is_wp_error($result)) {
          echo "  ERROR: {$ch_name} - {$result->get_error_message()}\n";
          $errors++;
        } else {
          $total_children++;
        }
      }
    }
  }

  echo "OK {$book_name}: " . (is_array($chapters) ? count($chapters) : $chapters) . " chapters\n";
}

echo "\n=== RESULT ===\n";
echo "Parents (books): {$total_parents}\n";
echo "Children (chapters): {$total_children}\n";
echo "Total terms: " . ($total_parents + $total_children) . "\n";
echo "Errors: {$errors}\n";
