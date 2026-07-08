<?php
/**
 * Create article: "La historia de Lot: del valle fértil a la cueva — biografía de un hombre entre dos mundos"
 * Serie: La historia de Lot (colección: Sodoma y Gomorra)
 * Run: type scripts\create-lot-biography-article.php | docker exec -i wp_bc php -d display_errors=1
 */

require_once '/var/www/html/wp-load.php';

global $wpdb;

$title = 'La historia de Lot: del valle fértil a la cueva — biografía de un hombre entre dos mundos';
$slug = sanitize_title($title);

$content = '<!-- wp:paragraph -->
<p>La historia de Lot es una de las más fascinantes y enigmáticas del Génesis. No es la historia de un héroe de la fe como Abraham ni la de un villano como los hombres de Sodoma. Es la historia de un hombre ordinario que, sin ser malvado, terminó en el lugar equivocado en el momento equivocado, atrapado entre dos mundos sin pertenecer plenamente a ninguno. Su vida traza un arco que va desde las colinas de Canaán, pasando por la prosperidad del valle del Jordán, el escaño de juez en la puerta de Sodoma, hasta una cueva solitaria en el monte. Es, en muchos sentidos, un espejo de la fragilidad humana.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">El sobrino de Abraham: los orígenes de Lot</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Lot era hijo de Harán, el hermano menor de Abraham que murió en Ur de los caldeos (Génesis 11:27-28). Cuando Abraham recibió el llamamiento de dejar su tierra y su parentela para ir a la tierra que Dios le mostraría, Lot lo acompañó (Génesis 12:4-5). Desde el principio, Lot aparece como parte del séquito patriarcal, un joven que sigue a su tío sin haber tenido un llamado propio. Esta condición de "acompañante" —leal pero sin vocación independiente— marcaría su trayectoria.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Juntos viajaron desde Harán hasta Siquem, luego a Bet-el, y finalmente descendieron a Egipto durante una hambruna (Génesis 12:10). En cada etapa, Lot estuvo presente, aprendiendo del hombre del convenio, pero sin haber hecho un convenio propio. Cuando ambos regresaron de Egipto "rico[s] en ganado, en plata y en oro" (Génesis 13:2, 5), la convivencia se volvió insostenible.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">La separación: Abraham y Lot se dividen</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Génesis 13 registra el punto de inflexión: "Hubo contienda entre los pastores del ganado de Abram y los pastores del ganado de Lot" (Génesis 13:7). La riqueza que ambos habían acumulado generó conflicto, y Abraham, en un gesto generoso, ofreció a Lot la primera opción del territorio. No hubo disputa, no hubo reclamo de autoridad patriarcal. Con una nobleza que define su carácter, Abraham dijo: "No haya ahora altercado entre mí y ti. . . porque somos hermanos. ¿No está toda la tierra delante de ti? Yo te ruego que te apartes de mí. Si fueres a la mano izquierda, yo iré a la derecha; y si tú a la derecha, yo iré a la izquierda" (Génesis 13:8-9).</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Este momento es crucial para entender a Lot. Abraham le ofrece la libertad de elegir, y Lot elige. Es la primera —y quizás la única— decisión importante que toma por sí mismo en todo el relato.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">La mirada hacia el valle: Lot elige con los ojos</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>El texto describe la elección de Lot con un lenguaje que revela su motivación interior: "Y alzó Lot sus ojos y vio toda la llanura del Jordán, que toda ella era de riego, antes que destruyese Jehová a Sodoma y a Gomorra, como el huerto de Jehová, como la tierra de Egipto entrando en Zoar" (Génesis 13:10). Lot elige con los ojos: ve fertilidad, agua, prosperidad. Su decisión es racional desde una perspectiva económica: el valle promete riqueza. Pero el versículo 13 añade un detalle ominoso: "Mas los hombres de Sodoma eran malos y pecadores delante de Jehová en gran manera".</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>La advertencia está implícita en el texto, pero Lot no la ve —o no quiere verla. Ha priorizado lo visible sobre lo invisible, lo material sobre lo espiritual. No actúa por malicia, sino por miopía espiritual.</p>
<!-- /wp:paragraph -->

<!-- wp:lds-passage-block/passage {"volume":"ot","book":"genesis","chapter":13,"startVerse":10,"endVerse":13} /-->

<!-- wp:heading -->
<h2 class="wp-block-heading">La tienda hacia Sodoma: el primer paso</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Génesis 13:12 contiene un detalle aparentemente menor pero profundamente revelador: "Lot se asentó en las ciudades de la llanura y fue poniendo sus tiendas hasta Sodoma". La frase hebrea sugiere un movimiento progresivo, un desplazamiento gradual. Lot no se muda directamente a Sodoma; primero pone su tienda en las cercanías, luego un poco más cerca, hasta que la ciudad se convierte en el centro gravitacional de su vida.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>El élder L. Tom Perry (1922–2015), del Cuórum de los Doce Apóstoles, comentó: "La mayoría de los problemas a los que Lot se enfrentó más tarde en la vida, y fueron varios, pueden remontarse a su primera decisión de colocar la puerta de su tienda en dirección a Sodoma". Luego contrastó: "Aunque no lo sé, personalmente creo que la puerta de la tienda de Abraham miraba hacia el altar que él construyó para el Señor" ("El poder de librarse", Conferencia General, abril de 2012).</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>El contraste es pedagógico: Abraham edificó un altar; Lot plantó su tienda "hasta Sodoma". Uno orientó su vida hacia el culto; el otro, hacia la oportunidad económica. La dirección de la puerta de una tienda parece un detalle menor, pero es un marcador del corazón.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Lot en Sodoma: rescate y retorno</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>En Génesis 14, la progresión se ha completado: "Tomaron también a Lot, hijo del hermano de Abram, que moraba en Sodoma" (Génesis 14:12). Lot ya no está en las afueras; vive dentro de la ciudad. Cuando una coalición de reyes invasores captura Sodoma, Lot es tomado prisionero junto con todos sus bienes. Abraham, al enterarse, reúne a sus 318 siervos entrenados, persigue a los invasores y rescata a su sobrino (Génesis 14:14-16).</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Uno esperaría que después de ser rescatado —literalmente de la esclavitud— Lot decidiera mudarse. Pero no. Regresa a Sodoma. La familiaridad ha reemplazado a la cautela. La ciudad se ha convertido en su hogar, su centro de operaciones, su identidad.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">El juez en la puerta: la cima del ascenso social de Lot</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>El detalle más revelador de la historia aparece en Génesis 19:1: "Llegaron, pues, los dos ángeles a Sodoma a la caída de la tarde; y Lot estaba sentado a la puerta de Sodoma". En el mundo antiguo, la puerta de la ciudad no era simplemente una entrada. Era el centro administrativo y judicial: allí se reunían los ancianos, se dirimían los pleitos legales, se cerraban los contratos y se administraba justicia (Rut 4:1-11; Proverbios 31:23). Sentarse a la puerta era ejercer liderazgo cívico.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Lot, el extranjero que llegó como refugiado económico, se ha convertido en parte del establecimiento de Sodoma. Es un juez. Tiene un asiento en el consejo de la ciudad. La ironía es trágica: el hombre que eligió Sodoma por su prosperidad ahora es responsable de administrar justicia en una sociedad que el propio texto describe como "mala y pecadora delante de Jehová en gran manera" (Génesis 13:13).</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Los sodomitas mismos reconocen su rol y se lo reprochan cuando Lot intenta proteger a los ángeles: "Vino este aquí para habitar como extranjero, ¿y habrá de erigirse en juez?" (Génesis 19:9). La acusación revela una tensión profunda: la comunidad lo ve como un forastero que ha asumido un cargo que no le corresponde. Lot ha pasado años construyendo una posición en Sodoma, pero en el momento crucial, los sodomitas le recuerdan que nunca será realmente uno de ellos.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Esta es la tragedia central de Lot: en casa de Abraham era aceptado pero se sentía un dependiente sin identidad propia; en Sodoma encontró identidad y poder, pero nunca fue aceptado como ciudadano de pleno derecho. Quedó atrapado entre dos mundos, sin pertenecer completamente a ninguno.</p>
<!-- /wp:paragraph -->

<!-- wp:lds-passage-block/passage {"volume":"ot","book":"genesis","chapter":19,"startVerse":1,"endVerse":11} /-->

<!-- wp:heading -->
<h2 class="wp-block-heading">La visita de los ángeles y la defensa de los huéspedes</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Cuando los dos ángeles llegan a Sodoma, Lot los reconoce de inmediato y los recibe con la hospitalidad característica del mundo antiguo: insiste en que se queden en su casa, prepara una comida, los protege (Génesis 19:2-3). Cuando la turba rodea la casa exigiendo que los huéspedes salgan, Lot sale a enfrentarlos. La Traducción de José Smith (JST) de Génesis 19:9-15 —que se encuentra en el Apéndice JST de la edición SUD— aclara que la turba exigió no solo a los huéspedes sino también a las hijas de Lot, y que él se negó a entregarlas (JST, Génesis 19:11-14). Lejos de ser un padre que sacrifica a sus hijas, el JST revela a un hombre que dice: "Dios no justificará a su siervo en esto".</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>El apóstol Pedro confirma esta imagen de un Lot justo que, lejos de sentirse cómodo en Sodoma, "afligía cada día su alma justa al ver y oír los hechos inicuos de ellos" (2 Pedro 2:7-8). La vida de Lot en Sodoma no era un paraíso de complacencia pecaminosa, sino un tormento espiritual diario. Era un hombre justo atrapado en una sociedad injusta, y su posición en la puerta le daba una visibilidad privilegiada de la corrupción que lo rodeaba.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">La demora: cuando la ciudad te ha atrapado</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Cuando los ángeles advierten a Lot que la destrucción es inminente, su reacción revela cuán profundamente la ciudad lo ha atrapado: "Entonces Lot salió y habló a sus yernos, los que habían de tomar sus hijas, y dijo: Levantaos, salid de este lugar, porque Jehová va a destruir esta ciudad. Mas pareció a sus yernos como si se burlara" (Génesis 19:14). Sus propios yernos piensan que está bromeando. Su testimonio tiene tan poca credibilidad en su propia casa que nadie lo toma en serio.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Luego viene el momento más condenatorio y a la vez más redentor: "Y al rayar el alba, los ángeles daban prisa a Lot, diciendo: Levántate, toma tu mujer y tus dos hijas que se hallan aquí, para que no perezcas en el castigo de la ciudad. Y deteniéndose él, los asieron aquellos varones de la mano, y a su mujer y a sus dos hijas, por misericordia de Jehová hacia él, y lo sacaron y lo pusieron fuera de la ciudad" (Génesis 19:15-16).</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>"Y deteniéndose él." Lot se queda paralizado. En hebreo, la palabra sugiere demora, vacilación, una pausa cargada de ambivalencia. No es un héroe de la fe que sale confiado; es un hombre que duda incluso cuando el azufre está por caer. Los ángeles tienen que tomarlo de la mano para sacarlo. Es rescatado no por su propia determinación, sino "por misericordia de Jehová hacia él" (Génesis 19:16).</p>
<!-- /wp:paragraph -->

<!-- wp:lds-passage-block/passage {"volume":"ot","book":"genesis","chapter":19,"startVerse":12,"endVerse":22} /-->

<!-- wp:heading -->
<h2 class="wp-block-heading">La negociación por Zoar: un destello de asertividad</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Ya fuera de la ciudad, Lot negocia con los ángeles: "No, señores míos, ahora. . . he aquí esta ciudad está cerca para huir allá. . . . Dejadme escapar allá. . . . ¿No es ella pequeña?" (Génesis 19:18-20). Es interesante que Lot, que se había quedado paralizado cuando los ángeles le ordenaban salir de Sodoma, de repente encuentra voz para negociar su destino cuando se trata de elegir su refugio. Pide ir a Zoar, una ciudad pequeña que promete seguridad sin el aislamiento del monte.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Dios le concede el deseo: Zoar es perdonada por amor a Lot. Pero incluso esta concesión revela la ambivalencia de Lot: sigue queriendo vivir en una ciudad, sigue buscando seguridad en la civilización, aunque la civilización lo haya decepcionado una y otra vez.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">La esposa que miró atrás</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Mientras huían, la esposa de Lot "miró atrás" y se convirtió en una estatua de sal (Génesis 19:26). Jesús mismo usa este episodio como advertencia para quienes dudan en el momento de la decisión final: "Acordaos de la mujer de Lot" (Lucas 17:32). La mujer de Lot no murió por un pecado grave, sino por una mirada: una mirada de añoranza hacia lo que debía haber quedado atrás. Su corazón estaba todavía en Sodoma.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>El episodio plantea una pregunta incómoda: ¿miró la esposa de Lot atrás porque amaba Sodoma, o porque amaba a las hijas y yernos que había dejado atrás? El texto no lo dice, pero la advertencia de Cristo sugiere que la mirada representa una atadura espiritual que impide recibir completamente la liberación de Dios.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">La cueva: el epílogo silencioso</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>El final de Lot es quizás lo más triste de toda la narración: "Después subió Lot de Zoar y habitó en el monte, y sus dos hijas con él; porque tuvo miedo de quedarse en Zoar; y habitó en una cueva, él y sus dos hijas" (Génesis 19:30). Aun Zoar, la ciudad pequeña que él mismo eligió, terminó por parecerle insegura. Lot, que una vez había alzado los ojos para elegir el valle más fértil, ahora sube al monte y se esconde en una cueva.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>De la tienda abierta en la llanura fértil, pasando por la ciudad próspera y el asiento de juez en la puerta, hasta terminar en una cueva en el monte. El arco de la vida de Lot es un arco descendente: de la abundancia a la escasez, del poder al aislamiento, de la civilización a la soledad.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>El epílogo en la cueva es particularmente sombrío. Las hijas de Lot, convencidas de que no hay hombres en la tierra para perpetuar su linaje, embriagan a su padre y conciben hijos de él (Génesis 19:31-36). La Traducción de José Smith califica las acciones de las hijas como "inicuas" (JST, Génesis 19:37, 39) y deja claro que Lot no fue cómplice, sino víctima de un engaño. De esa unión nacieron Moab y Amón, padres de los moabitas y amonitas, naciones que más tarde se convertirían en adversarios de Israel. Sin embargo, de esa línea manchada surgiría Rut la moabita, bisabuela del rey David y antepasada de Jesucristo. Incluso en la tragedia más oscura, la gracia de Dios teje redención.</p>
<!-- /wp:paragraph -->

<!-- wp:lds-passage-block/passage {"volume":"ot","book":"genesis","chapter":19,"startVerse":30,"endVerse":38} /-->

<!-- wp:heading -->
<h2 class="wp-block-heading">Lecciones de la historia de Lot</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>La progresión de Lot no es la de un hombre que decide volverse malo. Es la historia de una erosión espiritual gradual, tan sutil que apenas se nota hasta que es demasiado tarde:</p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul><!-- wp:list-item -->
<li>Primero, una mirada hacia lo que atrae, sin intención de alejarse de Dios.</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li>Luego, se orienta la vida hacia esa atracción (la tienda hacia Sodoma).</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li>Después, se cruza el umbral para vivir dentro.</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li>Más tarde, se participa del sistema, se asciende en él.</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li>Finalmente, cuando llega la advertencia, uno se queda paralizado, incapaz de soltar lo que tanto le costó construir.</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:paragraph -->
<p>Pero la historia de Lot no es solo una advertencia. Es también un testimonio de la misericordia de Dios. Pedro lo llama "justo Lot" (2 Pedro 2:7). Los ángeles lo toman de la mano y lo arrastran fuera de la ciudad no por sus méritos, sino "por misericordia de Jehová hacia él" (Génesis 19:16). Y el mismo versículo añade que Dios lo salvó "acordándose de Abraham" (Génesis 19:29). Lot se salvó, en última instancia, porque alguien más —Abraham— intercedió por él.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>La pregunta que el relato deja en el aire no es si estamos en Sodoma, sino hacia dónde mira la puerta de nuestra tienda. Y si los ángeles vinieran hoy a advertirnos, ¿tendrían que tomarnos de la mano para sacarnos, o saldríamos corriendo?</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Fuentes consultadas</h2>
<!-- /wp:heading -->
<!-- wp:list -->
<ul>
<li>Santa Biblia, Reina-Valera 1960, Génesis 11–19.</li>
<li>Traducción de José Smith, Génesis 19:9-15, 37, 39, en Apéndice JST de la Santa Biblia (edición SUD), Intellectual Reserve, 2013.</li>
<li>2 Pedro 2:7-8, en Santa Biblia, Reina-Valera 1960.</li>
<li>Lucas 17:32, en Santa Biblia, Reina-Valera 1960.</li>
<li>Élder L. Tom Perry, "El poder de librarse", Conferencia General, abril de 2012.</li>
<li>Keil-Delitzsch, Comentario sobre el Antiguo Testamento, "Ezequiel 16", referencia a Sodoma y Lot.</li>
<li>Manual de Seminario del Antiguo Testamento, "Génesis 19", La Iglesia de Jesucristo de los Santos de los Últimos Días, 2014.</li>
<li>Ayudas para el Estudio de las Escrituras (Scripture Helps), "Génesis 18–23", edición SUD.</li>
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

// Set category to "Sin categoría" (term_id: 1 → term_taxonomy_id: 1)
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
    echo "Block names:\n";
    foreach ($parsed as $block) {
        if (!empty($block['blockName'])) {
            echo "  - " . $block['blockName'] . "\n";
        } else {
            echo "  - [CLASSIC / UNKNOWN]\n";
        }
    }
}

echo "Done.\n";
