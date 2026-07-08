<?php
/**
 * Create article: "¿Quién fue Lot? — Genealogía, contexto y estructura de su historia"
 * Serie: La historia de Lot (colección: Sodoma y Gomorra)
 * Primer artículo de la serie (order 0), sirve como introducción biográfica
 * Run: type scripts\create-lot-intro-article.php | docker exec -i wp_bc php -d display_errors=1
 */

require_once '/var/www/html/wp-load.php';

global $wpdb;

$title = '¿Quién fue Lot? — Genealogía, contexto y estructura de su historia';
$slug = sanitize_title($title);

$content = '<!-- wp:paragraph -->
<p>Lot es uno de esos personajes bíblicos que todo el mundo cree conocer pero que pocos pueden situar correctamente en el mapa de las Escrituras. Aparece en los capítulos 11 al 19 del Génesis, y su nombre vuelve a mencionarse en el Nuevo Testamento como ejemplo tanto de advertencia como de justicia. Pero ¿quién era realmente Lot? ¿De dónde venía? ¿Cómo se relacionaba con Abraham? Este artículo ofrece una visión panorámica de su identidad, su genealogía y la estructura de su historia, como antesala a los artículos más detallados de esta serie.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Genealogía de Lot</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>La familia de Lot se remonta a Sem, hijo de Noé, a través de la línea de Taré. El siguiente diagrama de Mermaid muestra las relaciones genealógicas clave de Lot con los patriarcas que lo rodean. Las líneas punteadas indican vínculos matrimoniales que conectan las dos ramas de la familia.</p>
<!-- /wp:paragraph -->

<!-- wp:merpress/mermaidjs -->
<div class="wp-block-merpress-mermaidjs diagram-source-mermaid"><pre class="mermaid">graph TD
T[Taré] --> A[Abraham]
T --> N[Nacor]
T --> H[Harán]
H --> L[Lot]
H --> M[Milca]
N --> B[Betuel]
B --> LA[Labán]
B --> RE[Rebeca]
RE --> ISA[Isaac]
L --> MO[Moab]
L --> BM[Ben-amí]
A --> ISA
</pre></div>
<!-- /wp:merpress/mermaidjs -->

<!-- wp:paragraph -->
<p>Lot era hijo de Harán, el hermano menor de Abraham que murió en Ur de los caldeos, en la tierra de su nacimiento, antes que su padre Taré (Génesis 11:27-28). La muerte temprana de Harán es un dato crucial para entender la dinámica familiar: Lot, junto con su hermana Milca, quedaron huérfanos de padre. Milca se casó con su tío Nacor (Génesis 11:29), y Lot pasó a ser parte del séquito de su tío Abraham. Como señala el comentarista Keil-Delitzsch, "para ese período en la vida de esta familia, que comienza con la migración de Ur, Lot representa la rama de su padre en la familia". La relación especial entre Abraham y Lot —que no habría sido la misma de no haber muerto Harán— hace de Lot el representante de toda una línea genealógica.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Un detalle importante: el parentesco con Sara</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>El libro de Abraham (Abraham 2:2) identifica a Sara como hija de Harán, lo que la convertiría en sobrina de Abraham. Sin embargo, Génesis 20:12 la presenta como media hermana de Abraham («es hija de mi padre, mas no de mi madre»). La tradición judía antigua (Rashi, Targumim) identificaba a Sara con Iscá (Génesis 11:29), haciéndola también hija de Harán. En el egipcio antiguo, el término para «hermana» (snt) podía significar también «esposa», lo que da contexto a la declaración de Abraham. Para un análisis detallado de estas dos tradiciones y su reconciliación, véase el artículo <a href="http://localhost:8080/sara-hermana-o-sobrina-de-abraham/">¿Sara, hermana o sobrina de Abraham? Dos tradiciones bíblicas</a>.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>En cualquier caso, la familia de Taré se entreteje de manera compleja: Abraham se casó con su sobrina Sara (hija de Harán, según Abraham 2:2), y Nacor se casó con su sobrina Milca (también hija de Harán). Lot, el hijo de Harán que quedó sin padre, creció en este núcleo familiar donde los lazos de sangre y matrimonio unían estrechamente a todos sus miembros.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">La relación con Abraham: sobrino, compañero y heredero</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Cuando Abraham recibió el llamamiento de dejar Ur de los caldeos para dirigirse a la tierra de Canaán, Lot lo acompañó (Génesis 12:4-5; Abraham 2:2-4). No era un siervo ni un extraño: era familia. Y más importante aún, en ese momento Lot era el único heredero varón de la línea de Taré aparte del propio Abraham, que aún no tenía hijos. Es probable que Abraham viera en Lot a un heredero potencial, alguien que continuaría su legado si él moría sin descendencia.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>La presencia de Lot en el viaje no fue accidental. El texto de Abraham 2:2-4 lo incluye explícitamente entre los que partieron de Ur, y el hecho de que el convenio abrahámico se extendiera a su descendencia (Génesis 12:2-3) creaba una expectativa natural de que Lot sería parte de ese futuro. Por eso la separación posterior en Génesis 13 es tan significativa: cuando Abraham y Lot se dividen, no es meramente una disputa de pastores, sino una bifurcación en el camino del convenio. Lot elige el valle del Jordán, y Abraham se queda en Canaán. A partir de ese momento, las promesas del convenio siguen el linaje de Abraham, no el de Lot.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Sin embargo, Abraham nunca abandonó a Lot. Cuando Lot fue capturado en la guerra de los reyes (Génesis 14), Abraham armó a sus 318 siervos y fue a rescatarlo. Cuando supo que Dios destruiría Sodoma, intercedió repetidamente por los justos (Génesis 18:23-33). Y el texto dice explícitamente que Dios salvó a Lot "acordándose de Abraham" (Génesis 19:29). La relación entre ambos fue, hasta el final, un vínculo de lealtad y de gracia divina en favor del justo intercesor.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Estructura de la historia de Lot</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>La narrativa de Lot en el Génesis se despliega en seis episodios principales que trazan un arco completo desde su juventud al lado de Abraham hasta su final solitario en una cueva:</p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ol class="wp-block-list">
<li><strong>Orígenes y migración</strong> (Génesis 11:27-12:5): Lot nace en Ur, hijo de Harán. Tras la muerte de su padre, emigra con Abraham a Canaán y luego a Egipto.</li>
<li><strong>Separación de Abraham</strong> (Génesis 13): La disputa entre pastores lleva a la separación. Lot elige el valle del Jordán y pone su tienda hacia Sodoma.</li>
<li><strong>Rescate de Lot</strong> (Génesis 14): Lot es capturado en la guerra y Abraham lo rescata. Después de ser liberado, Lot regresa a Sodoma.</li>
<li><strong>Intercesión de Abraham</strong> (Génesis 18): Abraham negocia con Dios por los justos de Sodoma; la intercesión implícitamente incluye a Lot.</li>
<li><strong>Destrucción de Sodoma</strong> (Génesis 19:1-29): Los ángeles visitan a Lot, la turba rodea su casa, los ángeles lo sacan de la ciudad, su esposa mira atrás. Lot termina en Zoar y luego en una cueva.</li>
<li><strong>Epílogo en la cueva</strong> (Génesis 19:30-38): Las hijas de Lot conciben de su padre, dando origen a los moabitas y amonitas.</li>
</ol>
<!-- /wp:list -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Lot en las Escrituras</h2>
<!-- /wp:heading -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Antiguo Testamento</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>En el Antiguo Testamento, Lot aparece exclusivamente en los capítulos 11 al 19 del Génesis. Fuera de ese bloque, no se le menciona directamente en los libros históricos o proféticos, aunque sus descendientes —los moabitas y los amonitas— aparecen con frecuencia como vecinos y, a menudo, adversarios de Israel (Deuteronomio 2:9, 19; Jueces 3:12-30; 1 Samuel 11; 2 Samuel 12:26-31).</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Nuevo Testamento</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Lot es mencionado tres veces en el Nuevo Testamento, cada una con un énfasis distinto:</p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul class="wp-block-list">
<li><strong>Lucas 17:28-32</strong> — Jesús menciona los "días de Lot" como ejemplo de la repentina destrucción que vendrá sobre los que viven sin pensar en Dios, y advierte: "Acordaos de la mujer de Lot".</li>
<li><strong>2 Pedro 2:7-8</strong> — Pedro se refiere a Lot como "el justo Lot, abrumado por la conducta licenciosa de los malvados", que "afligía cada día su alma justa al ver y oír los hechos inicuos de ellos". Esta es la descripción más positiva de Lot en toda la Biblia.</li>
<li><strong>Lucas 17:29</strong> — Mención contextual de la destrucción de Sodoma.</li>
</ul>
<!-- /wp:list -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Perla de Gran Precio</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>El libro de Abraham (Abraham 2:2-4) confirma la genealogía de Lot y añade el detalle de que Sara era hija de Harán. La Traducción de José Smith de Génesis 19 (Apéndice JST) también expande el relato de la defensa de Lot ante la turba en Sodoma.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Conclusión</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Lot fue, ante todo, un hombre de familia: huérfano de padre, criado a la sombra de un tío extraordinario, bendecido por la proximidad del convenio pero sin hacerlo plenamente suyo. Su historia es la de un hombre que caminó entre dos mundos —el del pacto abrahámico y el de la civilización de Sodoma— sin echar raíces firmes en ninguno. Los artículos siguientes de esta serie exploran en detalle cada etapa de su fascinante y trágica trayectoria.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Fuentes consultadas</h2>
<!-- /wp:heading -->
<!-- wp:list -->
<ul>
<li>Santa Biblia, Reina-Valera 1960, Génesis 11–19.</li>
<li>Perla de Gran Precio, Abraham 2:2-4.</li>
<li>Traducción de José Smith, Génesis 19:9-15, en Apéndice JST de la Santa Biblia (edición SUD), Intellectual Reserve, 2013.</li>
<li>2 Pedro 2:7-8; Lucas 17:28-32, en Santa Biblia, Reina-Valera 1960.</li>
<li>Guía para el Estudio de las Escrituras, "Lot", "Harán", "Abraham", edición SUD.</li>
<li>Keil-Delitzsch, Comentario sobre el Antiguo Testamento, "Génesis 11:27-31".</li>
</ul>
<!-- /wp:list -->';

// Insert article
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

// Set category to "Sin categoría" (term_id: 1)
$wpdb->insert('wp_term_relationships', [
    'object_id' => $post_id,
    'term_taxonomy_id' => 1,
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
    // Check for MerPress block
    $has_mermaid = false;
    foreach ($parsed as $b) {
        if ($b['blockName'] === 'merpress/mermaidjs') {
            $has_mermaid = true;
        }
    }
    echo "Has merpress/mermaidjs: " . ($has_mermaid ? "YES" : "NO") . "\n";
}

echo "Done.\n";
