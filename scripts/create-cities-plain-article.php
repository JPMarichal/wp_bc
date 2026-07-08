<?php
/**
 * Article: "Las cinco ciudades de la llanura: Sodoma, Gomorra, Admá, Zeboim y Zoar"
 *
 * Geografía, composición y estructura de la pentápolis del Jordán.
 */
require_once '/var/www/html/wp-load.php';

global $wpdb;

$title = 'Las cinco ciudades de la llanura: Sodoma, Gomorra, Admá, Zeboim y Zoar';
$slug = sanitize_title($title);

$content = '<!-- wp:paragraph -->
<p>Cuando la Biblia menciona a Sodoma y Gomorra, la mayoría de los lectores asume que se trata de dos ciudades gemelas, las únicas habitantes de la llanura del Jordán antes de su destrucción. Pero el texto bíblico revela una realidad más compleja: eran cinco ciudades —una pentápolis— que compartían un mismo valle, una misma historia política y, finalmente, un mismo juicio divino. Admá, Zeboim y Zoar (también llamada Bela) completan el cuadro de una región densamente poblada y económicamente próspera, cuya memoria sobrevive no solo en las Escrituras sino en el registro geográfico de los sistemas de datos bíblicos modernos.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Las cinco ciudades y su ubicación</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>El Antiguo Testamento menciona sistemáticamente a las cinco ciudades en conjunto. La primera aparición ocurre en la tabla de las naciones, donde se describe el territorio de los cananeos: "Y fue el territorio de los cananeos desde Sidón, viniendo a Gerar hasta Gaza, hasta entrar en Sodoma y Gomorra, Adma y Zeboim, hasta Lasa" (Génesis 10:19). Esta referencia geográfica ubica a las ciudades en el límite suroriental de Canaán, en la región que más tarde sería el mar Muerto.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>El mismo pasaje que introduce a las ciudades en la narrativa de Abraham las enumera con sus reyes: "Bera, rey de Sodoma; Birsa, rey de Gomorra; Sinab, rey de Adma; Semeber, rey de Zeboim; y el rey de Bela, que es Zoar" (Génesis 14:2). Cada ciudad tenía su propio monarca, lo que indica que no eran meras aldeas sino centros urbanos con organización política independiente, aunque aliadas entre sí.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Los datos geográficos modernos, recopilados en el archivo <em>gnosis-places.json</em> del plugin bc-scripture-map, asignan coordenadas a estas ciudades. Sodoma, Gomorra y Admá comparten la misma ubicación registrada (31.20849° N, 35.449223° E), al sureste del mar Muerto. Zoar (también registrada como Bela) aparece ligeramente desplazada al sur (30.9265° N, 35.419° E). La coincidencia de coordenadas entre las tres primeras sugiere la dificultad de identificar sitios arqueológicos precisos bajo las aguas del mar Muerto, pero también testimonia la cercanía geográfica de estas urbes.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">El valle de Sidim: el escenario común</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>El valle de Sidim es el nexo geográfico que une a las cinco ciudades. El texto de Génesis 14 es explícito: "Todos estos se juntaron en el valle de Sidim, que es el mar Salado" (Génesis 14:3). La identificación del valle de Sidim con el mar Muerto ("mar Salado") indica que en tiempos de Abraham el valle aún no estaba cubierto por las aguas, o al menos no en su totalidad. El mismo capítulo añade un detalle geológico notable: "Y el valle de Sidim estaba lleno de pozos de asfalto" (Génesis 14:10). La presencia de pozos de asfalto (betún) es consistente con la geología de la región del mar Muerto, una zona de intensa actividad de hidrocarburos naturales que aún hoy produce bloques de asfalto que flotan en la superficie del lago.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>La descripción que hace Génesis 13 de la llanura del Jordán antes de la destrucción es igualmente reveladora: "Y alzó Lot sus ojos y vio toda la llanura del Jordán, que toda ella era de riego, antes que destruyese Jehová a Sodoma y a Gomorra, como el huerto de Jehová, como la tierra de Egipto entrando en Zoar" (Génesis 13:10). La comparación con "el huerto de Jehová" (el Edén) y con "la tierra de Egipto" (el delta del Nilo) sugiere una región excepcionalmente fértil, bien irrigada, capaz de sostener una población numerosa y una economía agrícola próspera.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Los reyes de la llanura: organización política</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>El capítulo 14 de Génesis presenta a las cinco ciudades como una confederación de reinos aliados que actúan militarmente bajo una causa común. Durante doce años habían estado sujetas a Quedorlaomer, rey de Elam, hasta que se rebelaron en el decimotercer año (Génesis 14:4). La respuesta de Quedorlaomer y sus aliados —Amrafel de Sinar, Arioc de Elasar y Tidal de naciones— fue una campaña militar que recorrió toda la región al este del Jordán antes de enfrentar a los cinco reyes en el valle de Sidim.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>El hecho de que cinco ciudades vecinas tuvieran reyes independientes sugiere una organización política descentralizada, similar a otras pentápolis del antiguo Oriente Próximo (como las cinco ciudades filisteas: Gaza, Asdod, Ascalón, Gat y Ecrón). Cada rey gobernaba su propia ciudad-estado, pero la alianza militar indica que compartían intereses económicos y estratégicos. La mención de los nombres de los reyes —Bera, Birsa, Sinab y Semeber—, aunque breves, es significativa. La onomástica de estos nombres es semítica occidental, coherente con el contexto cananeo de la región.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>La derrota de los cinco reyes frente a Quedorlaomer y la captura de Lot ponen en movimiento el episodio de la intervención de Abraham, que rescata a su sobrino y a los cautivos (Génesis 14:13-16). Este ciclo narrativo conecta a las ciudades de la llanura no solo con el juicio divino posterior, sino con la historia del pacto abrahámico.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Admá y Zeboim: las ciudades olvidadas</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Dos de las cinco ciudades —Admá y Zeboim— son las menos conocidas, pero su inclusión en los textos proféticos y legales de Israel revela que no eran secundarias en la memoria colectiva. Deuteronomio las incluye en la descripción de la devastación de la región: "Azufre y sal, abrasada toda su tierra; no será sembrada, ni producirá, ni crecerá en ella hierba alguna, como sucedió en la destrucción de Sodoma y de Gomorra, de Adma y de Zeboim, que destruyó Jehová en su furor y en su ira" (Deuteronomio 29:23). La ley mosaica eleva a las cinco ciudades —no solo a las dos principales— como ejemplo máximo del juicio divino contra la rebelión.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>El profeta Oseas, en uno de los pasajes más conmovedores del Antiguo Testamento, utiliza los nombres de Admá y Zeboim para expresar el amor de Dios por Israel: "¿Cómo podré abandonarte, oh Efraín? ¿Te entregaré yo, Israel? ¿Podré yo hacerte como Adma, o ponerte como a Zeboim?" (Oseas 11:8). La referencia es profundamente reveladora: Admá y Zeboim se habían convertido en proverbios de destrucción total, al punto que Dios los usa como el límite de lo que Él está dispuesto a hacer con Su pueblo. El versículo expresa el dolor divino ante la necesidad de castigar, sugiriendo que Dios no destruye por placer sino por necesidad, y que Su corazón se vuelve contra el castigo.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>En el archivo de datos geográficos <em>gnosis-places.json</em>, Admá aparece con el mismo código de referencia que Sodoma y Gomorra (status: "publish"), mientras que Zeboim de la llanura —la mencionada en Génesis 10:19, 14:2,8 y Deuteronomio 29:23— no tiene una entrada diferenciada de la Zeboim de Benjamín mencionada en Nehemías 11:34. Esta ausencia en los sistemas de datos modernos refleja, irónicamente, el mismo olvido que denuncian los profetas.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Zoar: la ciudad que escapó del juicio</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Zoar ocupa un lugar único entre las cinco ciudades. Mencionada también como Bela en Génesis 14:2,8, fue la ciudad que Lot solicitó como refugio cuando los ángeles lo sacaron de Sodoma: "He aquí ahora esta ciudad está cerca para huir allá, la cual es pequeña; déjame escapar ahora allá (¿no es ella pequeña?) y salvaré mi vida" (Génesis 19:20). La petición de Lot —insistiendo en su pequeñez— contrasta con la grandeza de las otras ciudades y sugiere que Zoar era un asentamiento menor.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Dios concedió la petición: "Date prisa, escápate allá; porque nada puedo hacer hasta que hayas llegado allá. Por esto fue llamado el nombre de la ciudad Zoar" (Génesis 19:22). El nombre Zoar significa "pequeña", en relación directa con la descripción de Lot. La ciudad no solo sobrevivió a la destrucción, sino que continuó existiendo como punto de referencia geográfico en textos posteriores. Moisés contempló la tierra prometida "hasta Zoar" (Deuteronomio 34:3), e Isaías y Jeremías la mencionan en sus oráculos contra Moab (Isaías 15:5; Jeremías 48:34).</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Sin embargo, la estadía de Lot en Zoar fue breve: "Pero Lot subió de Zoar y habitó en el monte, y sus dos hijas con él; porque tuvo miedo de quedarse en Zoar; y habitó en una cueva, él y sus dos hijas" (Génesis 19:30). El texto no explica por qué Lot temía quedarse en la ciudad que Dios había perdonado, pero sugiere que la asociación de Zoar con las ciudades de la llanura —aunque librada del fuego— pesaba sobre su conciencia.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>En <em>gnosis-places.json</em>, Zoar aparece con coordenadas distintas a las de las otras ciudades (30.9265° N, 35.419° E), ubicada más al sur del mar Muerto, en la región que corresponde históricamente a la llanura al sur del lago Asfaltites. Esta ubicación concuerda con las referencias de Isaías y Jeremías que la sitúan en territorio moabita.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">La destrucción: juicio sobre la pentápolis</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>La destrucción de las ciudades de la llanura es descrita en términos geológicos y teológicos: "Entonces hizo llover Jehová sobre Sodoma y sobre Gomorra azufre y fuego de parte de Jehová desde los cielos; y destruyó las ciudades y toda aquella llanura, con todos los moradores de aquellas ciudades y el fruto de la tierra" (Génesis 19:24-25). El texto menciona explícitamente a Sodoma y Gomorra como los centros principales, pero la destrucción alcanzó "toda aquella llanura", incluyendo a Admá y Zeboim.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Abraham, al observar la escena al día siguiente, "miró hacia Sodoma y Gomorra, y hacia toda la tierra de aquella llanura; y miró, y he aquí que el humo subía de la tierra como el humo de un horno" (Génesis 19:28). La imagen del horno evoca actividad volcánica o de combustión de hidrocarburos —consistente con la mención de los pozos de asfalto en Génesis 14:10— y ha llevado a numerosos estudiosos a proponer explicaciones geológicas para el cataclismo.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Los profetas posteriores convirtieron a Sodoma y Gomorra en el arquetipo del juicio divino. Isaías: "Babilonia, hermosura de reinos y ornamento de la grandeza de los caldeos, será como Sodoma y Gomorra, a las que trastornó Dios" (Isaías 13:19). Jeremías: "Como sucedió la destrucción de Sodoma y de Gomorra y de sus ciudades vecinas, dice Jehová, no morará allí nadie" (Jeremías 49:18). Amós: "Os trastorné como cuando Dios trastornó a Sodoma y a Gomorra, y fuisteis como tizón escapado del fuego" (Amós 4:11). En el Nuevo Testamento, Jesús mismo utiliza a Sodoma como medida del juicio: "Por tanto, os digo que el castigo será más tolerable para Sodoma en el día del juicio, que para aquella ciudad" (Mateo 10:15; 11:24).</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>En el libro de Apocalipsis, Sodoma y Egipto se convierten en nombres simbólicos para la ciudad donde el Señor fue crucificado (Apocalipsis 11:8), cerrando el arco narrativo que comenzó en Génesis y extendiendo el arquetipo de la ciudad de la llanura hasta el fin de los tiempos.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Conclusión</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Las cinco ciudades de la llanura —Sodoma, Gomorra, Admá, Zeboim y Zoar— constituyen un sistema urbano completo en la narrativa bíblica, no meros escenarios de fondo. Cada una tenía su rey, su territorio y su identidad, aunque compartían un valle común —el valle de Sidim—, una economía basada en la fertilidad de la llanura del Jordán y, finalmente, un juicio que las consumió. Zoar, la menor de todas, sobrevivió como testimonio de que la misericordia puede coexistir con el juicio. Admá y Zeboim, las casi olvidadas, perviven en la memoria profética como el límite del castigo que Dios está dispuesto a aplicar a Su propio pueblo. Leer el relato de la llanura prestando atención a las cinco ciudades —no solo a las dos más famosas— revela una historia más rica, más geográficamente fundamentada y teológicamente más matizada de lo que una lectura superficial permite apreciar.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Fuentes consultadas</h2>
<!-- /wp:heading -->
<!-- wp:list -->
<ul>
<li>La Santa Biblia, Reina-Valera 1960. Edición SUD.</li>
<li>Bible Dictionary, "Sodom", "Gomorrah", "Zoar".</li>
<li>Guía para el Estudio de las Escrituras, "Sodoma", "Gomorra", "Admá", "Zeboim", "Zoar", "Valle de Sidim".</li>
<li>Archivo gnosis-places.json, plugin bc-scripture-map, datos geográficos de ciudades bíblicas, fuente: openbible.co / Theographic.</li>
</ul>
<!-- /wp:list -->';

// Insert
$result = $wpdb->insert('wp_posts', [
    'post_title' => $title,
    'post_name' => $slug,
    'post_content' => $content,
    'post_status' => 'publish',
    'post_type' => 'post',
    'post_author' => 1,
    'post_date' => current_time('mysql'),
    'post_modified' => current_time('mysql'),
]);

if ($result === false) {
    echo "Error creating article: " . $wpdb->last_error . "\n";
    exit(1);
}

$post_id = $wpdb->insert_id;
echo "Article created with ID: $post_id\n";

// Set category to "Sodoma y Gomorra" (cat 23)
$wpdb->insert('wp_term_relationships', [
    'object_id' => $post_id,
    'term_taxonomy_id' => 23,
    'term_order' => 0,
]);
echo "Category assigned.\n";

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
        if ($b['blockName']) {
            $types[$b['blockName']] = ($types[$b['blockName']] ?? 0) + 1;
        }
    }
    foreach ($types as $name => $c) {
        echo "  $name: $c\n";
    }
}

echo "Done. URL: http://localhost:8080/$slug/\n";
