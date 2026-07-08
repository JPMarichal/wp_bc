<?php
/**
 * Fix sources list in article 2673
 */
require_once '/var/www/html/wp-load.php';

global $wpdb;

$post_id = 2673;
$content = $wpdb->get_var($wpdb->prepare(
    "SELECT post_content FROM wp_posts WHERE ID = %d AND post_type = 'post'", $post_id
));

if (!$content) die("Article $post_id not found.\n");

$old = "<li>La Santa Biblia, Reina-Valera 1960. Edición SUD.</li>\n<li>Bible Dictionary, \"Sodom\", \"Gomorrah\", \"Zoar\".</li>\n<li>Guía para el Estudio de las Escrituras, \"Sodoma\", \"Gomorra\", \"Admá\", \"Zeboim\", \"Zoar\", \"Valle de Sidim\".</li>\n<li>Archivo gnosis-places.json, plugin bc-scripture-map, datos geográficos de ciudades bíblicas, fuente: openbible.co / Theographic.</li>";

$new = "<li>La Santa Biblia, Reina-Valera 1960. Edición SUD.</li>\n<li>Bible Dictionary, \"Sodom\", \"Gomorrah\", \"Zoar\".</li>\n<li>Guía para el Estudio de las Escrituras, \"Sodoma\", \"Gomorra\", \"Admá\", \"Zeboim\", \"Zoar\", \"Valle de Sidim\".</li>\n<li>Archivo gnosis-places.json, plugin bc-scripture-map, datos geográficos de ciudades bíblicas, fuente: openbible.co / Theographic.</li>\n<li><em>International Standard Bible Encyclopedia</em> (ISBE), artículos \"Sodom\", \"Cities of the Plain; Ciccar\", \"Siddim, Vale of\", \"Zoar\".</li>\n<li>Smith's Bible Dictionary, artículos \"Sodom\", \"Zoar\".</li>\n<li><em>Hastings Dictionary of the Bible</em>, artículos \"Plain, Cities of the\", \"Zoar\".</li>\n<li><em>Easton's Bible Dictionary</em> (1897), artículo \"Sodom\".</li>\n<li>José Smith, <em>Enseñanzas del Profeta José Smith</em>, comp. Joseph Fielding Smith, Deseret Book, 1976, pág. 213.</li>\n<li><em>Scripture Helps: Genesis 18–23</em>, La Iglesia de Jesucristo de los Santos de los Últimos Días, 2014.</li>\n<li><em>Old Testament Seminary Teacher Manual</em>, \"Génesis 19\", La Iglesia de Jesucristo de los Santos de los Últimos Días, 2014.</li>\n<li>Gordon B. Hinckley, \"Ya rompe el alba\", <em>Liahona</em>, abril de 2004.</li>\n<li><em>Interpreter: A Journal of Latter-day Saint Faith and Scholarship</em>, \"Feet of Clay: Queer Theory and the Church of Jesus Christ\", vol. 41, 2020.</li>";

$original = $content;
$content = str_replace($old, $new, $content);

if ($content !== $original) {
    $wpdb->update('wp_posts', ['post_content' => $content], ['ID' => $post_id]);
    echo "Sources updated.\n";
} else {
    echo "No match found.\n";
}

// Verify
$saved = $wpdb->get_var("SELECT post_content FROM wp_posts WHERE ID = $post_id");
$li_count = preg_match_all('/<li>/', $saved);
echo "Total <li> tags: $li_count\n";
echo "has_blocks(): " . (has_blocks($saved) ? "YES" : "NO") . "\n";
