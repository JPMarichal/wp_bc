<?php
/**
 * Refina desambiguación para gnosis duplicados y Caná.
 * Uso: docker exec wp_bc_cli wp eval-file scripts/tracking/refine-disamb.php --allow-root
 */

$updates = [
    // Baalah: same name, different biblical refs
    612 => 'Josué 15:29 (territorio de Judá, ciudad del Néguev)',
    613 => 'Josué 15:9–10 (frontera norte de Judá, también llamada Quiriat-jearim)',
    
    // Baalath: same name, different refs
    614 => 'Josué 19:44 (territorio de Dan)',
    615 => '1 Reyes 9:18 / 2 Crónicas 8:6 (construida por Salomón)',
    
    // Bet-dagón: same name, different tribal territories
    631 => 'Josué 15:41 (territorio de Judá, Sefelá)',
    632 => 'Josué 19:27 (territorio de Aser)',
    890 => 'Otra posible ubicación de Bet-dagón (Beth-dagon 2)',
    
    // Edar / Eder: same name, different numbering
    670 => 'Josué 15:21 (territorio de Judá, Néguev)',
    900 => 'Posible ubicación alternativa de Edar (Eder 1)',
    
    // Caná: two different places with same Spanish name
    117 => 'Juan 2:1 – Galilea, lugar de las bodas de Caná',
    283 => 'Josué 16:8 – frontera de Efraín (Kanah)',
];

foreach ($updates as $id => $disamb) {
    update_post_meta($id, '_bc_loc_disambiguation', $disamb);
    echo "  ID {$id}: → \"{$disamb}\"\n";
}

echo "\nRefined " . count($updates) . " entries.\n";
