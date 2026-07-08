<?php
/**
 * Enrich article 2673 with extrabiblical sources from:
 * - ISBE (International Standard Bible Encyclopedia)
 * - Smith's Bible Dictionary
 * - Hastings Dictionary of the Bible
 * - Easton's Bible Dictionary
 * - Interpreter Journal (LDS scholarly)
 * - Scripture Helps manual (LDS)
 * - Joseph Smith (rejecting prophets)
 * - General Conference (Gordon B. Hinckley)
 */
require_once '/var/www/html/wp-load.php';

global $wpdb;

$post_id = 2673;
$content = $wpdb->get_var($wpdb->prepare(
    "SELECT post_content FROM wp_posts WHERE ID = %d AND post_type = 'post'", $post_id
));

if (!$content) {
    die("Article $post_id not found.\n");
}

$original = $content;

// ============================================================
// 1. Enrich "El valle de Sidim" — add geological theories
// ============================================================
$old_valle = '<p>La descripción que hace Génesis 13 de la llanura del Jordán antes de la destrucción es igualmente reveladora:';
$new_valle = '<p>Los estudiosos de la geología bíblica han debatido durante siglos el mecanismo de la catástrofe. El <em>International Standard Bible Encyclopedia</em> (ISBE) señala que los pozos de betún del valle de Sidim habrían sido el combustible de un incendio desatado por una tormenta de rayos, posiblemente acompañada de una descarga de piedras meteóricas (ISBE, "Sodom"). La ignición del betún —abundante en el subsuelo y que aún hoy flota en bloques en la superficie del mar Muerto— habría producido una conflagración de tal intensidad que pudo haber causado el hundimiento del valle por debajo del nivel del lago, inundándolo parcialmente. Esta teoría, recogida también por Smith\'s Bible Dictionary y por Hastings Dictionary of the Bible, reconcilia el lenguaje del texto sagrado —"azufre y fuego de parte de Jehová desde los cielos" (Génesis 19:24)— con los datos geológicos observables en la región del mar Muerto: una falla tectónica activa, depósitos masivos de sal y azufre, y emanaciones naturales de hidrocarburos.</p><p>En cuanto a la ubicación misma de las ciudades, el debate entre los especialistas es antiguo y persistente. El <em>International Standard Bible Encyclopedia</em> documenta que una tradición sostenida desde Flavio Josefo y Jerónimo sitúa a las ciudades en el extremo sur del mar Muerto, donde el monte Jebel Usdum —cuyo nombre preserva las consonantes de Sodoma (S-D-M)— se eleva como un macizo de sal de unos 150 metros de altura (ISBE, "Cities of the Plain"). Otros eruditos, basándose en que la "llanura del Jordán" descrita en Génesis 13 es visible desde Betel —al norte del mar Muerto—, defienden una ubicación en el extremo norte del lago. La mención en Deuteronomio 34:3 de que Moisés contempló "la llanura del valle de Jericó, la ciudad de las palmeras, hasta Zoar" sugiere que Zoar estaba en el rango de visión desde el monte Pisga, lo que favorecería el extremo sur. El erudito G. W. Wade, en <em>Hastings Dictionary of the Bible</em>, resume que aunque la evidencia es conflictiva, "el peso preponderante parece apoyar una ubicación al norte", mientras que otros especialistas como Robinson y G. A. Smith defienden el sur (ISBE, "Siddim, Vale of").</p><p>La descripción que hace Génesis 13 de la llanura del Jordán antes de la destrucción es igualmente reveladora:';

// ============================================================
// 2. Enrich "Admá y Zeboim" — add LDS early reference
// ============================================================
$old_admah = '<p>En el archivo de datos geográficos <em>gnosis-places.json</em>, Admá aparece con el mismo código de referencia que Sodoma y Gomorra (status: "publish"), mientras que Zeboim de la llanura —la mencionada en Génesis 10:19, 14:2,8 y Deuteronomio 29:23— no tiene una entrada diferenciada de la Zeboim de Benjamín mencionada en Nehemías 11:34. Esta ausencia en los sistemas de datos modernos refleja, irónicamente, el mismo olvido que denuncian los profetas.</p>';
$new_admah = '<p>En el archivo de datos geográficos <em>gnosis-places.json</em>, Admá aparece con el mismo código de referencia que Sodoma y Gomorra (status: "publish"), mientras que Zeboim de la llanura —la mencionada en Génesis 10:19, 14:2,8 y Deuteronomio 29:23— no tiene una entrada diferenciada de la Zeboim de Benjamín mencionada en Nehemías 11:34. Esta ausencia en los sistemas de datos modernos refleja, irónicamente, el mismo olvido que denuncian los profetas.</p><p>No obstante, en la tradición de la Iglesia restaurada, Admá y Zeboim no han sido olvidadas. El profeta José Smith, en una de sus reflexiones sobre la historia de Sodoma, agrupó expresamente a las cinco ciudades de la llanura en su lista de aquellas que "aborrecieron a los mensajeros de Dios": "Otra iglesia evangélica muy eminente existía en gran número, en Asia; había varias ciudades muy notables que eran eminentemente hábiles en la doctrina de no prestar atención a los mensajes que les pudieran ser enviados. Me refiero a las famosas ciudades de Admá, Zeboim, Sodoma, Gomorra, Zoar, etc. Cuando los ángeles de Dios fueron, ellos los abusaron" (José Smith, citado en el <em>Interpreter Journal</em>, "Feet of Clay: Queer Theory and the Church of Jesus Christ", 2020). Esta declaración —en la que el profeta coloca a Admá y Zeboim a la par de Sodoma y Gomorra como ciudades que rechazaron a los mensajeros divinos— confirma que en la teología de la Restauración la pentápolis completa es tipo y sombra del juicio que aguarda a quienes desprecian las advertencias del Señor.</p>';

// ============================================================
// 3. Enrich "La destrucción" — add Jebel Usdum, Joseph Smith, Easton
// ============================================================
$old_destruccion_geologica = '<p>Abraham, al observar la escena al día siguiente, "miró hacia Sodoma y Gomorra, y hacia toda la tierra de aquella llanura; y miró, y he aquí que el humo subía de la tierra como el humo de un horno" (Génesis 19:28). La imagen del horno evoca actividad volcánica o de combustión de hidrocarburos —consistente con la mención de los pozos de asfalto en Génesis 14:10— y ha llevado a numerosos estudiosos a proponer explicaciones geológicas para el cataclismo.</p>';
$new_destruccion_geologica = '<p>Abraham, al observar la escena al día siguiente, "miró hacia Sodoma y Gomorra, y hacia toda la tierra de aquella llanura; y miró, y he aquí que el humo subía de la tierra como el humo de un horno" (Génesis 19:28). La imagen del horno evoca actividad volcánica o de combustión de hidrocarburos —consistente con la mención de los pozos de asfalto en Génesis 14:10— y ha llevado a numerosos estudiosos a proponer explicaciones geológicas para el cataclismo. El <em>Easton\'s Bible Dictionary</em> (1897) registra que "no se ha descubierto ningún rastro de [Sodoma] ni de las otras ciudades de la llanura, tan completa fue su destrucción". El <em>International Standard Bible Encyclopedia</em> coincide: la única huella toponímica que sobrevive es el nombre de Jebel Usdum, la montaña de sal en la esquina suroccidental del mar Muerto, cuyas tres consonantes (S-D-M) reproducen las del nombre hebreo de Sodoma (ISBE, "Sodom").</p>';

// ============================================================
// 4. Enrich "La destrucción" — add Joseph Smith teaching
// ============================================================
$old_profetas = '<p>Los profetas posteriores convirtieron a Sodoma y Gomorra en el arquetipo del juicio divino.';
$new_profetas = '<p>Los profetas posteriores convirtieron a Sodoma y Gomorra en el arquetipo del juicio divino. El profeta José Smith enseñó: "Las ciudades de Sodoma y Gomorra fueron destruidas por haber rechazado a los profetas" (<em>Enseñanzas del Profeta José Smith</em>, pág. 213; citado también en Scripture Helps, Génesis 18-23). Esta declaración no contradice —como a veces se ha pretendido— la dimensión sexual de los pecados de Sodoma que el texto bíblico registra en Génesis 19:5, sino que la complementa: el rechazo de los profetas fue la culminación de una cadena de iniquidad que incluía la soberbia, la ociosidad, la opresión del pobre (Ezequiel 16:49-50) y la violencia sexual contra el extranjero (Génesis 19:4-9). La Traducción de José Smith, lejos de mitigar la condena de las prácticas homosexuales, fortaleció la narración al aclarar que la turba exigía tanto a los huéspedes como a las hijas de Lot (TJS, Génesis 19:11-14).</p><p>Isaías: "Babilonia, hermosura de reinos y ornamento de la grandeza de los caldeos, será como Sodoma y Gomorra, a las que trastornó Dios" (Isaías 13:19). Jeremías: "Como sucedió la destrucción de Sodoma y de Gomorra y de sus ciudades vecinas, dice Jehová, no morará allí nadie" (Jeremías 49:18). Amós: "Os trastorné como cuando Dios trastornó a Sodoma y a Gomorra, y fuisteis como tizón escapado del fuego" (Amós 4:11). En el Nuevo Testamento, Jesús mismo utiliza a Sodoma como medida del juicio: "Por tanto, os digo que el castigo será más tolerable para Sodoma en el día del juicio, que para aquella ciudad" (Mateo 10:15; 11:24).</p>';

// ============================================================
// 5. Expand "Fuentes consultadas"
// ============================================================
$old_sources = '<ul><li>La Santa Biblia, Reina-Valera 1960. Edición SUD.</li><li>Bible Dictionary, "Sodom", "Gomorrah", "Zoar".</li><li>Guía para el Estudio de las Escrituras, "Sodoma", "Gomorra", "Admá", "Zeboim", "Zoar", "Valle de Sidim".</li><li>Archivo gnosis-places.json, plugin bc-scripture-map, datos geográficos de ciudades bíblicas, fuente: openbible.co / Theographic.</li></ul>';
$new_sources = '<ul><li>La Santa Biblia, Reina-Valera 1960. Edición SUD.</li><li>Bible Dictionary, "Sodom", "Gomorrah", "Zoar".</li><li>Guía para el Estudio de las Escrituras, "Sodoma", "Gomorra", "Admá", "Zeboim", "Zoar", "Valle de Sidim".</li><li>Archivo gnosis-places.json, plugin bc-scripture-map, datos geográficos de ciudades bíblicas, fuente: openbible.co / Theographic.</li><li><em>International Standard Bible Encyclopedia</em> (ISBE), artículos "Sodom", "Cities of the Plain; Ciccar", "Siddim, Vale of", "Zoar".</li><li>Smith\'s Bible Dictionary, artículos "Sodom", "Zoar".</li><li><em>Hastings Dictionary of the Bible</em>, artículos "Plain, Cities of the", "Zoar".</li><li><em>Easton\'s Bible Dictionary</em> (1897), artículo "Sodom".</li><li>José Smith, <em>Enseñanzas del Profeta José Smith</em>, compilación de Joseph Fielding Smith, Deseret Book, 1976, pág. 213.</li><li><em>Scripture Helps: Genesis 18–23</em>, "Old Testament Scripture Helps", La Iglesia de Jesucristo de los Santos de los Últimos Días, 2014.</li><li><em>Old Testament Seminary Teacher Manual</em>, "Génesis 19", La Iglesia de Jesucristo de los Santos de los Últimos Días, 2014.</li><li>Gordon B. Hinckley, "Ya rompe el alba", <em>Liahona</em>, abril de 2004.</li><li><em>Interpreter: A Journal of Latter-day Saint Faith and Scholarship</em>, "Feet of Clay: Queer Theory and the Church of Jesus Christ", vol. 41, 2020.</li></ul>';

// Apply all replacements
$content = str_replace($old_valle, $new_valle, $content);
$content = str_replace($old_admah, $new_admah, $content);
$content = str_replace($old_destruccion_geologica, $new_destruccion_geologica, $content);
$content = str_replace($old_profetas, $new_profetas, $content);
$content = str_replace($old_sources, $new_sources, $content);

if ($content !== $original) {
    $result = $wpdb->update(
        'wp_posts',
        ['post_content' => $content],
        ['ID' => $post_id]
    );
    if ($result !== false) {
        echo "Article $post_id enriched. Rows affected: $result\n";
    } else {
        echo "Article $post_id: update failed.\n";
        exit(1);
    }
} else {
    echo "Article $post_id: no changes made (patterns not found).\n";
    exit(1);
}

// Verify blocks
$saved = $wpdb->get_var("SELECT post_content FROM wp_posts WHERE ID = $post_id");
$blocks_ok = has_blocks($saved);
echo "has_blocks(): " . ($blocks_ok ? "YES" : "NO") . "\n";

if ($blocks_ok) {
    $parsed = parse_blocks($saved);
    $count = count($parsed);
    echo "Parsed blocks: $count\n";
    $types = [];
    foreach ($parsed as $b) {
        if ($b['blockName']) $types[$b['blockName']] = ($types[$b['blockName']] ?? 0) + 1;
    }
    foreach ($types as $name => $c) {
        echo "  $name: $c\n";
    }
}

echo "Done. URL: http://localhost:8080/las-cinco-ciudades-de-la-llanura-sodoma-gomorra-adma-zeboim-y-zoar/\n";
