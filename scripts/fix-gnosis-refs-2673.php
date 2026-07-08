<?php
/**
 * Fix gnosis-places.json references to cite public sources directly
 */
require_once '/var/www/html/wp-load.php';

global $wpdb;

$post_id = 2673;
$content = $wpdb->get_var($wpdb->prepare(
    "SELECT post_content FROM wp_posts WHERE ID = %d AND post_type = 'post'", $post_id
));

if (!$content) die("Article $post_id not found.\n");

$original = $content;

// 1. First paragraph reference
$old1 = 'recopilados en el archivo <em>gnosis-places.json</em> del plugin bc-scripture-map, asignan coordenadas a estas ciudades';
$new1 = 'recopilados en las bases de datos geográficas <em>OpenBible</em> (openbible.co) y <em>Theographic</em> (theographic.com), asignan coordenadas a estas ciudades';

// 2. Admah paragraph reference
$old2 = 'En el archivo de datos geográficos <em>gnosis-places.json</em>, Admá aparece con el mismo código de referencia que Sodoma y Gomorra (status: &quot;publish&quot;), mientras que Zeboim de la llanura';
$new2 = 'En las bases de datos geográficas <em>OpenBible</em> y <em>Theographic</em>, Admá aparece con la misma referencia que Sodoma y Gomorra, mientras que Zeboim de la llanura';

// 3. Zoar paragraph
$old3 = 'En <em>gnosis-places.json</em>, Zoar aparece con coordenadas distintas a las de las otras ciudades';
$new3 = 'En <em>OpenBible</em> y <em>Theographic</em>, Zoar aparece con coordenadas distintas a las de las otras ciudades';

// 4. Sources list item
$old4 = '<li>Archivo gnosis-places.json, plugin bc-scripture-map, datos geográficos de ciudades bíblicas, fuente: openbible.co / Theographic.</li>';
$new4 = '<li>OpenBible (openbible.co) y Theographic (theographic.com), datos geográficos de ciudades bíblicas.</li>';

$content = str_replace($old1, $new1, $content);
$content = str_replace($old2, $new2, $content);
$content = str_replace($old3, $new3, $content);
$content = str_replace($old4, $new4, $content);

if ($content !== $original) {
    $wpdb->update('wp_posts', ['post_content' => $content], ['ID' => $post_id]);
    echo "Article $post_id: gnosis references fixed.\n";
} else {
    echo "No changes made.\n";
    exit(1);
}

// Verify no more gnosis references
$remaining = substr_count($content, 'gnosis');
echo "Remaining 'gnosis' references: $remaining\n";
