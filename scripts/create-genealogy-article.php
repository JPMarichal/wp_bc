<?php
/**
 * Article: "El parentesco entre Israel, Moab y Amón: una genealogía desde Lot"
 */
require_once '/var/www/html/wp-load.php';

global $wpdb;

$title = 'El parentesco entre Israel, Moab y Amón: una genealogía desde Lot';
$slug = sanitize_title($title);

$content = '<!-- wp:paragraph -->
<p>La relación entre Israel y sus vecinos Moab y Amón fue compleja: a veces hostil, a veces pacífica, siempre marcada por un origen común que pocos lectores de la Biblia reconocen a primera vista. Moab y Amón no eran naciones extranjeras sin conexión con Israel; eran primos hermanos de los israelitas, descendientes del mismo tronco familiar que Abraham. Comprender este parentesco ilumina no solo los conflictos fronterizos del Antiguo Testamento, sino también la historia extraordinaria de Rut la moabita y su inclusión en la genealogía del Mesías.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">La familia de Térah: el tronco común</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Todo comienza con Térah, el patriarca de cuyo linaje surgirían tanto la nación del convenio como sus vecinos del este del Jordán. El libro de Génesis registra: "Térah vivió setenta años, y engendró a Abram, a Nacor y a Harán" (Génesis 11:26). De estos tres hijos, Harán —el menor— murió prematuramente en Ur de los Caldeos, pero dejó un hijo: Lot (Génesis 11:27-28). Abraham (entonces Abram) y Nacor continuaron sus respectivas líneas.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>El árbol genealógico completo revela la cercanía del parentesco:</p>
<!-- /wp:paragraph -->

<!-- wp:merpress/mermaidjs {"align":"center"} -->
<pre class="mermaid">graph TD
    T[Térah] --> A[Abraham]
    T --> N[Nacor]
    T --> H[Harán]
    H --> L[Lot]
    A --> I[Isaac]
    L --> M[Moab<br/>&#40;hijo mayor&#41;]
    L --> B[Ben-Ammi<br/>&#40;hijo menor&#41;]
    I --> J[Jacob / Israel]
    J --> T1[12 tribus de Israel]
    M --> MO[Moa bitas]
    B --> AM[Amonitas]
    T1 --> JU[Judá]
    JU --> D[David]
    D --> JES[Jesucristo]</pre>
<!-- /wp:merpress/mermaidjs -->

<!-- wp:paragraph -->
<p>Como muestra el diagrama, Abraham y Lot eran tío y sobrino. Isaac y Moab eran primos hermanos. Jacob (Israel) y los hijos de Moab y Amón eran primos en segundo grado. Esta cercanía genética explica por qué la Torá trata a Moab y Amón de una manera distinta a otras naciones: no eran paganos lejanos, sino familia rebelde.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">La línea de Abraham: el convenio</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Dios llamó a Abraham para ser padre de una nación escogida, y el convenio pasó a Isaac y luego a Jacob, cuyo nombre fue cambiado a Israel (Génesis 32:28). De Jacob nacieron doce hijos que dieron origen a las doce tribus. Judá, uno de ellos, sería el linaje del que nacería David y, siglos después, el Mesías (Génesis 49:10).</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Esta línea del convenio es la que recorre toda la Biblia, pero no es la única rama. El mismo Abraham tuvo otros hijos: Ismael (de Agar) y los hijos de Cetura (Génesis 25:1-4). Sin embargo, el pariente más cercano a Abraham en términos genealógicos —antes del nacimiento de Isaac— era su sobrino Lot.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">La línea de Lot: Moab y Amón</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Después de la destrucción de Sodoma y Gomorra, las dos hijas de Lot —creyéndose los últimos seres humanos sobre la tierra— embriagaron a su padre y concibieron de él. El hijo mayor fue llamado Moab ("del padre"), y el menor Ben-Ammí ("hijo de mi pueblo"). De ellos surgieron dos naciones: los moabitas y los amonitas (Génesis 19:30-38).</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>La cercanía del parentesco entre estos pueblos e Israel se refleja en la propia forma en que la Torá se refiere a ellos. En Deuteronomio, el Señor dio instrucciones específicas: "No aborrecerás al edomita, porque es tu hermano; no aborrecerás al egipcio, porque extranjero fuiste en su tierra" (Deuteronomio 23:7). Pero el versículo anterior dice: "No entrará moabita ni amonita en la congregación de Jehová, ni hasta la décima generación" (Deuteronomio 23:3). La distinción es reveladora: Israel debía tratar a los edomitas como "hermanos" (por ser descendientes de Esaú, hijo de Isaac), pero a moabitas y amonitas se les negaba la incorporación plena a la comunidad del convenio. A pesar del origen común, su separación del pacto abrahámico era completa.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Historia compartida entre primos</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>La historia de Israel, Moab y Amón es la historia de parientes que pelearon durante siglos. Durante el Éxodo, Moab se negó a dar paso a Israel y contrató a Balaam para maldecirlo (Números 22-24). En el período de los Jueces, el rey moabita Eglón oprimió a Israel (Jueces 3:12-14), y más tarde Jefté luchó contra los amonitas (Jueces 11). Durante la monarquía, David sometió a ambos pueblos (2 Samuel 8:2, 12:26-31), y Salomón tomó esposas moabitas y amonitas que lo indujeron a la idolatría (1 Reyes 11:1-8). Los profetas pronunciaron juicios contra ellos (Isaías 15-16; Jeremías 48-49; Ezequiel 25:1-11; Amós 1:13-15; Sofonías 2:8-11), y sin embargo, el mismo Jeremías que anuncia juicio también promete restauración: "Pero haré que los cautivos de Moab vuelvan en los postreros días" (Jeremías 48:47).</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>El parentesco explica la tensión: no se lucha con tanta intensidad contra un extraño; solo los hermanos pelean así. La Torá misma reconoce el vínculo al llamar a Lot "el hijo del hermano de Abram" (Génesis 14:12), manteniendo viva la memoria del parentesco a lo largo de las generaciones.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Rut la moabita: cuando la familia vuelve a casa</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>El punto culminante de esta historia de parentesco roto y restauración es Rut. Una mujer moabita —descendiente de Lot— elige libremente unirse al pueblo de Israel. Su declaración es uno de los pronunciamientos de fe más sublimes de las Escrituras: "Tu pueblo será mi pueblo, y tu Dios mi Dios" (Rut 1:16).</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Rut se casa con Booz, un pariente de la tribu de Judá, y de esa unión nace Obed, abuelo del rey David (Rut 4:17). La genealogía de Jesucristo en Mateo 1 incluye a Rut (Mateo 1:5), convirtiéndola en una de las pocas mujeres mencionadas en el linaje mesiánico. Una descendiente de Lot —de la rama excluida del convenio— se convierte en eslabón directo del Mesías.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>El parentesco entre Israel, Moab y Amón no es una curiosidad genealógica, sino una lección teológica: la gracia de Dios trasciende cualquier barrera de linaje, historia o exclusión legal. Lo que comenzó en una cueva, con dos mujeres desesperadas que creían ser las últimas sobrevivientes de la humanidad, termina en Belén, con una moabita fiel que se convierte en antepasada del Salvador del mundo.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Conclusión</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Cuando el lector de la Biblia encuentra a moabitas y amonitas en las páginas del Antiguo Testamento, no está leyendo sobre pueblos extranjeros remotos. Está leyendo sobre parientes —primos de los israelitas— que tomaron un camino diferente, que rechazaron el convenio que Abraham había recibido, pero que nunca dejaron de estar conectados por la sangre. La historia de Rut demuestra que ese parentesco, aunque roto por siglos de hostilidad, podía restaurarse mediante la fe. No hay barrera genealógica que la gracia no pueda cruzar.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Fuentes consultadas</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul>
<li>La Santa Biblia, Reina-Valera 1960. Edición SUD.</li>
<li>Bible Dictionary, "Abraham", "Lot", "Moab", "Ammon", "Ruth".</li>
<li>Kerry Muhlestein, "Ruth, Redemption, Covenant, and Christ", en <em>The Gospel of Jesus Christ in the Old Testament</em>, Deseret Book, 2009.</li>
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

$post_id = $wpdb->insert_id;
echo "Article created with ID: $post_id\n";

// Set category to Sodoma y Gomorra (cat 23)
$wpdb->insert('wp_term_relationships', [
    'object_id' => $post_id,
    'term_taxonomy_id' => 23,
    'term_order' => 0,
]);
echo "Category assigned.\n";

// Verify
$saved = $wpdb->get_var("SELECT post_content FROM wp_posts WHERE ID = $post_id");
echo "has_blocks: " . (has_blocks($saved) ? 'YES' : 'NO') . "\n";
$parsed = parse_blocks($saved);
echo "Blocks: " . count($parsed) . "\n";

// Count block types
$types = [];
foreach ($parsed as $b) {
    if ($b['blockName']) $types[$b['blockName']] = ($types[$b['blockName']] ?? 0) + 1;
}
foreach ($types as $name => $count) {
    echo "  $name: $count\n";
}

echo "Done. URL: http://localhost:8080/$slug/\n";
