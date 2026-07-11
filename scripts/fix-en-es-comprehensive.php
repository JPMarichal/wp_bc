<?php
/**
 * Comprehensive fix script for bc_location:
 * 1. Fix Jocmeam/Jokmeam/Jocneam incorrect alias
 * 2. Alias all EN/ES duplicate pairs
 * 3. Translate English-only names to Spanish
 * 4. Fix system duplicates/typos
 */

$fixes = [];
$errors = [];

// ===== PHASE 1: Fix Jocmeam/Jokmeam/Jocneam =====
$fixes[] = [2935, '_bc_loc_alias_of', '', 'delete']; // remove wrong alias to 279
$fixes[] = [2935, '_bc_loc_alias_of', 920, 'meta'];  // alias to Jocmeam

$fixes[] = [2936, '_bc_loc_alias_of', 920, 'meta'];  // same place (1 Chr 6:68)

// ===== PHASE 2: EN/ES duplicate pairs (alias EN → ES) =====
$en_es_pairs = [
  [44,  43],    // Aphek → Afec
  [2730, 63],   // Athens → Atenas
  [2774, 2773], // Bethany → Betania
  [2777, 2776], // Bethlehem → Belén
  [2780, 104],  // Bethsaida → Betsaida
  [2792, 118],  // Capernaum → Capernaúm
  [2806, 133],  // Corinth → Corinto
  [2811, 141],  // Damascus → Damasco
  [2828, 2827], // Egypt → Egipto
  [2838, 167],  // Emmaus → Emaús
  [2874, 191],  // Gibeon → Gabaón
  [2929, 274],  // Jericho → Jericó
  [3044, 360],  // Nazareth → Nazaret
  [3050, 372],  // Nineveh → Nínive
  [3105, 408],  // Rome → Roma
  [3073, 187],  // Philadelphia → Filadelfia
  [3074, 188],  // Philippi → Filipos
  [3182, 466],  // Tyre → Tiro
  [3139, 432],  // Sidon → Sidón
  [3124, 434],  // Shechem → Siquem
  [216, 215],   // Goshen (2) → Gosén
  [2731, 63],   // Athens (v2?) → Atenas (another entry)
];

foreach ($en_es_pairs as [$en_id, $es_id]) {
  $existing = get_post_meta($en_id, '_bc_loc_alias_of', true);
  if ($existing && $existing != $es_id) {
    $errors[] = "ID $en_id already aliased to $existing, can't alias to $es_id";
    continue;
  }
  if ($existing == $es_id) {
    // Already correct, skip
    continue;
  }
  $fixes[] = [$en_id, '_bc_loc_alias_of', $es_id, 'meta'];
}

// ===== PHASE 3: Translate English-only names to Spanish =====
$translations = [
  // Gates
  [2749, 'Puerta Hermosa', 'Beautiful gate'],
  [2853, 'Puerta del Pescado', 'fish gate'],
  [2861, 'Puerta de Efraín', 'gate of Ephraim'],
  [2862, 'Puerta del Pueblo', 'gate of the children of the people'],
  [2863, 'Puerta del Fundamento', 'gate of the foundation'],
  [2858, 'Puerta de Mifcad', 'gate Miphkad'],
  [2912, 'Puerta de los Caballos', 'horse gate'],
  [3126, 'Puerta de las Ovejas', 'sheep gate'],
  [3086, 'Puerta de la Cárcel', 'prison gate'],
  [3048, 'Puerta Nueva', 'new gate'],
  [3056, 'Puerta Vieja', 'old gate'],
  [3205, 'Puerta del Agua', 'water gate'],
  [3390, 'Puerta del Medio', 'Middle Gate'],
  [3150, 'Puerta Recta', 'Straight Gate'],
  [3416, 'Puerta del Norte', 'North Gate'],
  [3421, 'Puerta de los Tiestos', 'Potsherd Gate'],
  [3465, 'Puerta del Sur', 'South Gate'],
  [3493, 'Puerta del Oeste', 'West Gate'],
  [2855, 'Gabata', 'Gabbatha'],

  // Lower/Upper
  [3377, 'Gulloth Inferior', 'Lower Gulloth'],
  [3378, 'Estanque Inferior', 'Lower Pool'],
  [3417, 'Estanque Viejo', 'Old Pool'],
  [3485, 'Gulloth Superior', 'Upper Gulloth'],
  [3486, 'Estanque Superior', 'Upper Pool'],

  // Valley
  [3491, 'Valle de la Decisión', 'Valley of Decision'],
  [3083, 'Llanura de Avén', 'plain of Aven'],
  [3186, 'Valle de Hebrón', 'vale of Hebron'],
  [3187, 'Valle de Sidim', 'vale of Siddim'],
  [3189, 'Valle de Beraca', 'valley of Beracah'],
  [3190, 'Valle de Gabaón', 'valley of Gibeon'],
  [3191, 'Valle de Hamongog', 'valley of Hamongog'],
  [3194, 'Valle de Jericó', 'valley of Jericho'],
  [3195, 'Valle de Mizpa', 'valley of Mizpeh'],
  [3198, 'Valle de Save', 'valley of Shaveh'],
  [3199, 'Valle de la Matanza', 'valley of slaughter'],
  [3200, 'Valle de Sucot', 'valley of Succoth'],
  [3201, 'Valle de los Pasajeros', 'valley of the passengers'],
  [3202, 'Valle del Hijo de Hinom', 'valley of the son of Hinnom'],
  [3203, 'Valle de Zered', 'valley of Zered'],
  [3492, 'Aguas de Jericó', 'Waters of Jericho'],

  // Sea / Waters
  [3108, 'Mar de Cineret', 'sea of Chinneroth'],
  [3111, 'Mar de la Llanura', 'sea of the plain'],
  [3112, 'Mar de Tiberias', 'sea of Tiberias'],

  // Mount / Hill
  [2909, 'Colina de Mizar', 'hill Mizar'],
  [3010, 'Monte Baala', 'mount Baalah'],
  [3011, 'Monte Baal-hermón', 'mount Baalhermon'],
  [3013, 'Monte de Efraín', 'mount Ephraim'],
  [3015, 'Monte Gerizim', 'mount Gerizim'],
  [3016, 'Monte Gilboa', 'mount Gilboa'],
  [3017, 'Monte Halac', 'mount Halak'],
  [3018, 'Monte Heres', 'mount Heres'],
  [3021, 'Monte Jearim', 'mount Jearim'],
  [3022, 'Monte Moriah', 'mount Moriah (Jearim)'],
  [3023, 'Monte Nebo', 'mount Nebo'],
  [3025, 'Monte de los Olivos', 'mount of Olives'],
  [3026, 'Monte Parán', 'mount Paran'],
  [3027, 'Monte Perazim', 'mount Perazim'],
  [3030, 'Monte Sáfer', 'mount Shapher'],
  [3031, 'Monte Sinaí', 'mount Sinai'],
  [3032, 'Monte Sión', 'mount Sion'],
  [3033, 'Monte Tabor', 'mount Tabor'],
  [3034, 'Monte Salmón', 'mount Zalmon'],
  [3035, 'Monte Zemaraim', 'mount Zemaraim'],
  [3405, 'Monte Basán', 'Mount Bashan'],
  [3406, 'Monte Hor 2', 'Mount Hor 2'],

  // Other places
  [2786, 'Muro Ancho', 'Broad Wall'],
  [2819, 'Roble de los Adivinos', "Diviners' Oak"],
  [2820, 'Manantial del Dragón', 'Dragon Spring'],
  [2907, 'Camino Real', 'Highway'],
  [2910, 'Lugar Santo', 'Holy Place'],
  [2943, 'Juicio', 'Judgment'],
  [3007, 'Santísimo', 'most holy'],
  [3008, 'Lugar Santísimo', 'most holy place'],
  [3009, 'Lugar Santísimo', 'most holy place'],
  [3113, 'Segundo Barrio', 'Second Quarter'],
  [3117, 'Piedra de la Serpiente', "Serpent's Stone"],
  [3148, 'Pórtico de Salomón', "Solomon's Portico"],
  [3206, 'Camino de Santidad', 'way of holiness'],
  [3273, 'Ciudad de David', 'City of David'],
  [3274, 'Ciudad de las Palmeras 1', 'City of Palms 1'],
  [3275, 'Ciudad de las Palmeras 2', 'City of Palms 2'],
  [3285, 'Plaza Oriental', 'East Square'],
  [3355, 'Casa del Bosque del Líbano', 'House of the Forest of Lebanon'],
  [3404, 'Lugar Santísimo 2', 'Most Holy Place 2'],
  [2946, 'Tahtim-hodsi', 'Tahtimhodshi'],
  [3385, 'Magdala', 'Magdala'], // keep same in Spanish
  [3092, 'Rahab', 'Rahab'],      // keep same

  // Phenicia → Fenicia
  [3072, 'Fenicia', 'Phenicia'],
  [3071, 'Fénice', 'Phenice'],
];

foreach ($translations as [$id, $es_name, $en_name]) {
  $fixes[] = [$id, 'post_title', $es_name, 'title'];
  $fixes[] = [$id, '_bc_loc_name_es', $es_name, 'meta'];
  if ($en_name) {
    $fixes[] = [$id, '_bc_loc_name_en', $en_name, 'meta'];
  }
}

// ===== PHASE 4: System duplicates =====
// Perez-uzza → Pérez-uza (typo fix)
$fixes[] = [3068, '_bc_loc_alias_of', 3069, 'meta'];

// Sodoma: 2641 → 436
$fixes[] = [2641, '_bc_loc_alias_of', 436, 'meta'];

// Gomorra: 699 → 214, 2642 → 214
$fixes[] = [699, '_bc_loc_alias_of', 214, 'meta'];
$fixes[] = [2642, '_bc_loc_alias_of', 214, 'meta'];

// Bethany 2 → Bethany (2774)
$fixes[] = [3254, '_bc_loc_alias_of', 2774, 'meta'];

// Adma 18 → Admá 2643
$fixes[] = [18, '_bc_loc_alias_of', 2643, 'meta'];

// Debir: 897 (Debir 1) → 145 (Debir = Quiriat-séfer)
$fixes[] = [897, '_bc_loc_alias_of', 145, 'meta'];


// ===== EXECUTE =====
foreach ($fixes as $fix) {
  [$id, $key, $value, $type] = $fix;
  
  $post = get_post($id);
  if (!$post || $post->post_type !== 'bc_location') {
    $errors[] = "ID $id: not found or wrong post type";
    continue;
  }

  if ($type === 'meta') {
    $old = get_post_meta($id, $key, true);
    if ($old == $value && $value !== '') {
      continue; // already correct
    }
    if ($value === '' && $key === '_bc_loc_alias_of') {
      delete_post_meta($id, $key);
      echo "ID $id: deleted meta $key\n";
    } else {
      update_post_meta($id, $key, $value);
      echo "ID $id: set meta $key = $value (was: " . var_export($old, true) . ")\n";
    }
  } elseif ($type === 'title') {
    $old = $post->post_title;
    if ($old === $value) {
      continue;
    }
    wp_update_post(['ID' => $id, 'post_title' => $value]);
    echo "ID $id: renamed title '$old' → '$value'\n";
  } elseif ($type === 'delete') {
    delete_post_meta($id, $key);
    echo "ID $id: deleted meta $key\n";
  }
}

echo "\n=== DONE ===\n";
if ($errors) {
  echo "\n=== ERRORS ===\n";
  foreach ($errors as $e) echo "$e\n";
}
