<?php
require_once '/var/www/html/wp-load.php';

// Group A: gnosis_alias_of_openbible — give openbible the clean slug
$group_a = array(
  array(54, 892, 'arroyo-de-egipto'),
  array(112, 2947, 'cades-barnea'),
  array(118, 2792, 'capernaum'),
  array(230, 2892, 'haran'),
  array(237, 2900, 'hebron'),
  array(319, 824, 'mar-salado'),
  array(340, 781, 'monte-carmelo'),
  array(341, 3024, 'monte-de-los-olivos'),
  array(346, 3019, 'monte-hermon'),
  array(347, 783, 'monte-hor'),
  array(359, 3039, 'nain'),
  array(392, 803, 'piton'),
  array(432, 3139, 'sidon'),
  array(454, 3167, 'tema'),
  array(487, 870, 'valle-de-sorec'),
  array(488, 871, 'valle-de-zeboim'),
);

// Group B+C+D: duplicates that need alias_of (openbible → canonical)
$group_bcd = array(
  array(14, 3220),   // Achzib (2) → Achzib 2
  array(28, 3225),   // Ain (2) → Ain 2
  array(45, 3232),   // Aphek (3) → Aphek 3
  array(53, 3238),   // Aroer (3) → Aroer 3
  array(99, 3264),   // Bet-shemesh (2) → Beth-shemesh 2
  array(106, 3267),  // Bezek (2) → Bezek 2
  array(170, 3290),  // En-gannim (2) → En-gannim 2
  array(182, 3295),  // Etam (3) → Etam 3
  array(207, 3322),  // Gihon (2) → Gihon 2
  array(216, 3329),  // Goshen (2) → Goshen 2
  array(263, 3356),  // Jabneel (2) → Jabneel 2
  array(268, 3357),  // Janoah (2) → Janoah 2
  array(277, 2934),  // Jezreel (2) → Jezreel
  array(318, 3109),  // Mar de Galilea → mar de Galilea
  array(338, 3396),  // Mizpah (3) → Mizpah 3
  array(353, 3403),  // Moreh (2) → Moreh 2
  array(364, 3411),  // Nebo (2) → Nebo 2
  array(378, 3418),  // Ophrah (2) → Ophrah 2
  array(426, 3454),  // Shamir (2) → Shamir 2
  array(448, 3473),  // Tamar (2) → Tamar 2
  array(462, 3480),  // Timnah (2) → Timnah 2
  array(465, 3482),  // Tiphsah (2) → Tiphsah 2
  array(480, 863),   // Valle de Ela → Valle de Elah
  array(493, 3496),  // Zanoah (2) → Zanoah 2
  array(18, 2643),   // Adma → Admá (manual)
  array(510, 2645),  // Zoar → Zoar (Bela) (manual)
  array(117, 283),   // Caná → Caná (openbible duplicate)
);

// Group E: fix wrong alias on Dedán
// ID 2815 has alias_of = 405 (Rodas), should be 146 (Dedán)

echo "=== Group A: Give clean slugs to openbible canonicals ===\n";

foreach ($group_a as $item) {
  $openbible_id = $item[0];
  $gnosis_id = $item[1];
  $clean_slug = $item[2];

  $openbible = get_post($openbible_id);
  $gnosis = get_post($gnosis_id);

  if (!$openbible || !$gnosis) {
    echo "SKIP: post not found ($openbible_id, $gnosis_id)\n";
    continue;
  }

  // Step 1: Rename gnosis alias slug to free the clean slug
  $alias_slug = $clean_slug . '-alias-' . $gnosis_id;
  $result = wp_update_post(array(
    'ID' => $gnosis_id,
    'post_name' => wp_unique_post_slug($alias_slug, $gnosis_id, 'publish', 'bc_location', 0),
  ));

  if (is_wp_error($result)) {
    echo "ERR rename gnosis $gnosis_id: " . $result->get_error_message() . "\n";
    continue;
  }

  // Step 2: Give openbible the clean slug
  $result = wp_update_post(array(
    'ID' => $openbible_id,
    'post_name' => $clean_slug,
  ));

  if (is_wp_error($result)) {
    echo "ERR rename openbible $openbible_id: " . $result->get_error_message() . "\n";
    // Restore gnosis slug?
    continue;
  }

  echo "OK: openbible $openbible_id ({$openbible->post_title}) → $clean_slug (gnosis $gnosis_id slug freed)\n";
}

echo "\n=== Groups B+C+D: Set alias_of on openbible → canonical ===\n";

foreach ($group_bcd as $item) {
  $openbible_id = $item[0];
  $canonical_id = $item[1];

  $openbible = get_post($openbible_id);
  $canonical = get_post($canonical_id);

  if (!$openbible || !$canonical) {
    echo "SKIP: post not found ($openbible_id, $canonical_id)\n";
    continue;
  }

  $existing = get_post_meta($openbible_id, '_bc_loc_alias_of', true);
  if (!empty($existing)) {
    echo "SKIP: $openbible_id ({$openbible->post_title}) already has alias_of=$existing\n";
    continue;
  }

  update_post_meta($openbible_id, '_bc_loc_alias_of', $canonical_id);
  echo "OK: $openbible_id ({$openbible->post_title}) → canonical $canonical_id ({$canonical->post_title})\n";
}

echo "\n=== Group E: Fix Dedán alias ===\n";

$dedan_gnosis_id = 2815;
$dedan_openbible_id = 146;

$current = get_post_meta($dedan_gnosis_id, '_bc_loc_alias_of', true);
echo "Current alias_of on $dedan_gnosis_id: " . ($current ?: 'none') . "\n";

update_post_meta($dedan_gnosis_id, '_bc_loc_alias_of', $dedan_openbible_id);
$dedan_canonical = get_post($dedan_openbible_id);
echo "FIXED: gnosis Dedán (ID 2815) now aliases to openbible Dedán (ID $dedan_openbible_id: {$dedan_canonical->post_title})\n";

echo "\n=== Done ===\n";
