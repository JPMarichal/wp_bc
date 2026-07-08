<?php
/**
 * Create bc_location posts for the 5 cities of the plain and
 * insert a bc/scripture-map block into article 2673.
 */
require_once '/var/www/html/wp-load.php';

global $wpdb;

$post_id = 2673;

// ── 1. Define city data ────────────────────────────────────────────────
$cities = [
    'sodom' => [
        'title' => 'Sodoma',
        'slug'  => 'sodom',
        'lat'   => 31.20849,
        'lng'   => 35.449223,
        'type'  => 'city',
        'desc'  => 'Una de las cinco ciudades de la llanura (pentápolis del Valle de Sidim). Centro principal de la confederación, su rey era Bera. Destruida por fuego y azufre junto con las demás ciudades de la llanura.',
        'verses' => ['Gen.10.19','Gen.13.10','Gen.13.13','Gen.14.2','Gen.14.8','Gen.14.10','Gen.14.11','Gen.14.12','Gen.14.17','Gen.14.21','Gen.14.22','Gen.18.16','Gen.18.20','Gen.18.22','Gen.18.26','Gen.19.1','Gen.19.4','Gen.19.24','Gen.19.28','Deut.29.23','Isa.13.19','Jer.49.18','Amos.4.11','Matt.10.15','Matt.11.23','Luke.10.12','2Pet.2.6','Jude.1.7','Rev.11.8'],
    ],
    'gomorrah' => [
        'title' => 'Gomorra',
        'slug'  => 'gomorrah',
        'lat'   => 31.20849,
        'lng'   => 35.449223,
        'type'  => 'city',
        'desc'  => 'Una de las cinco ciudades de la llanura. Su rey era Birsha. Destruida junto con Sodoma, Admá y Zeboim. Su nombre llegó a ser sinónimo de juicio divino en los profetas y en el Nuevo Testamento.',
        'verses' => ['Gen.10.19','Gen.13.10','Gen.14.2','Gen.14.8','Gen.14.10','Gen.14.11','Gen.18.20','Gen.19.24','Gen.19.28','Deut.29.23','Isa.1.9','Isa.13.19','Jer.49.18','Jer.50.40','Amos.4.11','Matt.10.15','Rom.9.29','2Pet.2.6','Jude.1.7'],
    ],
    'admah' => [
        'title' => 'Admá',
        'slug'  => 'admah',
        'lat'   => 31.20849,
        'lng'   => 35.449223,
        'type'  => 'city',
        'desc'  => 'Una de las cinco ciudades de la llanura. Su rey era Shinab. Mencionada junto con Sodoma y Gomorra en la destrucción (Deut 29:23). El profeta Oseas la usa como símbolo del límite del castigo divino (Os 11:8).',
        'verses' => ['Gen.10.19','Gen.14.2','Gen.14.8','Deut.29.23','Hos.11.8'],
    ],
    'zeboim-plain' => [
        'title' => 'Zeboim (de la llanura)',
        'slug'  => 'zeboim-plain',
        'lat'   => 31.20849,
        'lng'   => 35.449223,
        'type'  => 'city',
        'desc'  => 'Una de las cinco ciudades de la llanura, destruida junto con Sodoma y Gomorra. Mencionada en Gén 10:19, 14:2,8; Deut 29:23 y Os 11:8. No debe confundirse con el Zeboim de Benjamín (Neh 11:34).',
        'verses' => ['Gen.10.19','Gen.14.2','Gen.14.8','Deut.29.23','Hos.11.8'],
    ],
    'zoar' => [
        'title' => 'Zoar (Bela)',
        'slug'  => 'zoar',
        'lat'   => 31.036904,
        'lng'   => 35.487657,
        'type'  => 'city',
        'desc'  => 'La más pequeña de las cinco ciudades de la llanura. Originalmente llamada Bela, fue el refugio de Lot durante la destrucción (Gén 19:20-22). Perdonada por su pequeñez, continuó existiendo como ciudad moabita.',
        'verses' => ['Gen.13.10','Gen.14.2','Gen.14.8','Gen.19.22','Gen.19.23','Gen.19.30','Deut.34.3','Isa.15.5','Jer.48.34'],
    ],
];

// ── 2. Create bc_location posts ─────────────────────────────────────────
$location_ids = [];

foreach ($cities as $key => $city) {
    $existing = $wpdb->get_var($wpdb->prepare(
        "SELECT ID FROM wp_posts WHERE post_type = 'bc_location' AND post_name = %s",
        $city['slug']
    ));
    if ($existing) {
        echo "Location '{$city['title']}' already exists (ID $existing).\n";
        $location_ids[] = $existing;
        continue;
    }

    $post_data = [
        'post_title'   => $city['title'],
        'post_name'    => $city['slug'],
        'post_type'    => 'bc_location',
        'post_status'  => 'publish',
    ];
    $wpdb->insert('wp_posts', $post_data);
    $loc_id = $wpdb->insert_id;

    update_post_meta($loc_id, '_bc_loc_lat',         $city['lat']);
    update_post_meta($loc_id, '_bc_loc_lng',         $city['lng']);
    update_post_meta($loc_id, '_bc_loc_type',        $city['type']);
    update_post_meta($loc_id, '_bc_loc_description', $city['desc']);
    update_post_meta($loc_id, '_bc_loc_source',      'manual');
    update_post_meta($loc_id, '_bc_loc_confidence',  'medium');
    update_post_meta($loc_id, '_bc_loc_scriptures',  json_encode(array_map(function ($r) {
        return ['ref' => $r];
    }, $city['verses'])));

    echo "Location '{$city['title']}' created (ID $loc_id).\n";
    $location_ids[] = $loc_id;
}

echo "\nLocation IDs: " . implode(', ', $location_ids) . "\n\n";

// ── 3. Build the map block ──────────────────────────────────────────────
// Region: Valle de Sidim (southern Dead Sea area)
$regions = [[
    'type'        => 'Polygon',
    'coordinates' => [[
        [35.30, 31.40],
        [35.65, 31.40],
        [35.65, 30.85],
        [35.30, 30.85],
        [35.30, 31.40],
    ]],
    'properties' => [
        'color'   => '#ff6b35',
        'opacity' => 0.12,
        'label'   => 'Valle de Sidim',
    ],
]];

$block_attrs = [
    'locationIds'  => $location_ids,
    'centerLat'    => 31.15,
    'centerLng'    => 35.45,
    'zoom'         => 10,
    'pitch'        => 50,
    'exaggeration' => 1.5,
    'height'       => 520,
    'showLabels'   => true,
    'mapTitle'     => 'Las cinco ciudades de la llanura',
    'regions'      => $regions,
    'tileProvider' => 'satellite',
];

$block_html = '<!-- wp:bc/scripture-map ' . json_encode($block_attrs, JSON_UNESCAPED_UNICODE) . ' /-->';

// ── 4. Insert map block after intro paragraph ──────────────────────────
$content = $wpdb->get_var($wpdb->prepare(
    "SELECT post_content FROM wp_posts WHERE ID = %d AND post_type = 'post'", $post_id
));
if (!$content) die("Article $post_id not found.\n");

// Check if map block already exists
if (strpos($content, '<!-- wp:bc/scripture-map') !== false) {
    die("Map block already exists in article $post_id. Aborting.\n");
}

// Insert after first `<!-- /wp:paragraph -->` (closing of the intro)
$insert_after = '<!-- /wp:paragraph -->';
$pos = strpos($content, $insert_after);
if ($pos === false) {
    die("Could not find intro paragraph close marker.\n");
}

$pos += strlen($insert_after);
$new_content = substr($content, 0, $pos) . "\n\n" . $block_html . "\n" . substr($content, $pos);

// ── 5. Update ──────────────────────────────────────────────────────────
$original = $content;
if ($new_content !== $original) {
    $wpdb->update('wp_posts', ['post_content' => $new_content], ['ID' => $post_id]);
    echo "Map block inserted into article $post_id.\n";
} else {
    echo "No changes made.\n";
}

// ── 6. Verify ──────────────────────────────────────────────────────────
$saved = $wpdb->get_var("SELECT post_content FROM wp_posts WHERE ID = $post_id");
echo "has_blocks(): " . (has_blocks($saved) ? 'YES' : 'NO') . "\n";

$parsed = parse_blocks($saved);
$block_names = [];
foreach ($parsed as $b) {
    if ($b['blockName']) {
        $block_names[$b['blockName']] = ($block_names[$b['blockName']] ?? 0) + 1;
    }
}
foreach ($block_names as $name => $c) {
    echo "  $name: $c\n";
}

// Verify bc_location posts
$loc_count = $wpdb->get_var("SELECT COUNT(*) FROM wp_posts WHERE post_type = 'bc_location' AND post_name IN ('sodom','gomorrah','admah','zeboim-plain','zoar')");
echo "bc_location posts created: $loc_count\n";

echo "\nDone.\n";
echo "Article: http://localhost:8080/?p=$post_id\n";
