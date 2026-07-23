<?php
require_once('/var/www/html/wp-load.php');
$c = '';
$c .= '<p>El quiasmo (del griego chi, letra X) es una figura literaria en la cual una serie de ideas o palabras se presentan en un orden determinado y luego se repiten en orden inverso, siguiendo un patrón A-B-C-D-D\'-C\'-B\'-A\'. El punto central contiene el mensaje teológico principal. Esta técnica retórica era característica de la literatura semítica antigua, especialmente de la poesía hebrea clásica.</p>';
$c .= '<p>La Encyclopedia of Mormonism, en su artículo «Book of Mormon Authorship», señala que los estudios estructurales han identificado el quiasmo como una forma literaria artística que aparece tanto en la Biblia como en el Libro de Mormón. Aunque puede aparecer en casi cualquier literatura, fue prevalente en la literatura bíblica del antiguo Cercano Oriente.</p>';
$c .= '<h2 class="wp-block-heading">El quiasmo de Alma 36</h2>';
$c .= '<p>El ejemplo más célebre se encuentra en Alma 36, donde Alma relata su conversión espiritual. El capítulo está estructurado como un quiasmo simétrico cuyo punto central gira en torno a Jesucristo y Su expiación. John W. Welch descubrió este quiasmo en 1967 mientras estudiaba en Oxford, y publicó su análisis en 1969 en BYU Studies.</p>';
$c .= '<p>Sin embargo, el quiasmo de Alma 36 no ha estado exento de crítica. El artículo «Rethinking Alma 36» en Interpreter Journal señala que las críticas han cuestionado si realmente cumple los requisitos de los quiasmos bíblicos clásicos. El artículo «Asymmetry in Chiasms» responde que la asimetría parcial no es necesariamente una debilidad, y que quiasmos bíblicos reconocidos también presentan asimetrías similares.</p>';
$c .= '<h2 class="wp-block-heading">Otras figuras literarias</h2>';
$c .= '<p>Además del quiasmo, el Libro de Mormón contiene abundantes ejemplos de paralelismo sinonímico y antitético. Donald W. Parry publicó en 1992 la obra «Poetic Parallelisms in the Book of Mormon», reformateando el texto completo del Libro de Mormón para revelar sus estructuras poéticas subyacentes.</p>';
$c .= '<p>El paralelismo sinonímico —donde una idea se repite con palabras diferentes— aparece abundantemente en el Libro de Mormón. El inclusio —un marco literario donde una sección comienza y termina con la misma frase— se encuentra en múltiples discursos proféticos.</p>';
$c .= '<h2 class="wp-block-heading">Referencias de las Escrituras</h2>';
$c .= '<figure class="wp-block-table"><table class="bc-forma-t"><thead><tr><th>Concepto</th><th>Referencia</th></tr></thead><tbody>';
$r = [
['El quiasmo de la conversión de Alma','Alma 36:1-30'],
['Paralelismos sobre justicia y misericordia','Alma 41:13-15'],
['Estructuras poéticas del rey Benjamín','Mosíah 5:1-15'],
['La visita de Cristo a los nefitas','3 Nefi 11:1-17'],
['Testimonio de Mormón sobre el lenguaje','Mormón 9:32-34'],
['Exhortación final de Moroni','Moroni 10:3-5'],
['Toda la Escritura es inspirada por Dios','2 Timoteo 3:16-17'],
['La palabra del Señor como lámpara','Salmos 119:105'],
['Testimonio unificado de la palabra divina','2 Nefi 29:8'],
];
foreach ($r as $v) $c .= "<tr><td>{$v[0]}</td><td>{$v[1]}</td></tr>";
$c .= '</tbody></table></figure>';
$c .= '<h2 class="wp-block-heading">Fuentes consultadas</h2><ul class="wp-block-list">';
$s = [
['Quiasmo','https://es.wikipedia.org/wiki/Quiasmo','Wikipedia en español'],
['Book of Mormon Authorship','https://eom.byu.edu/index.php/Book_of_Mormon_Authorship','Encyclopedia of Mormonism'],
['Book of Mormon Language','https://eom.byu.edu/index.php/Book_of_Mormon_Language','Encyclopedia of Mormonism'],
['Rethinking Alma 36','https://journal.interpreterfoundation.org/rethinking-alma-36/','Interpreter Journal'],
['Asymmetry in Chiasms','https://journal.interpreterfoundation.org/asymmetry-in-chiasms-with-a-note-about-deuteronomy-8-and-alma-36/','Interpreter Journal'],
['Celebrating Welch','https://journal.interpreterfoundation.org/celebrating-the-work-of-john-w-welch/','Interpreter Journal'],
];
foreach ($s as $v) $c .= "<li><a href=\"{$v[1]}\" target=\"_blank\" rel=\"noopener noreferrer\">{$v[0]} <i class=\"fas fa-external-link-alt\" aria-hidden=\"true\"></i></a> — {$v[2]}</li>";
$c .= '</ul>';

$data = [
'post_title' => 'El quiasmo y otras figuras literarias en el Libro de Mormón',
'post_name' => 'el-quiasmo-y-otras-figuras-literarias',
'post_content' => $c,
'post_status' => 'publish',
'post_author' => 1,
'post_excerpt' => 'El descubrimiento de quiasmos complejos y otras estructuras poéticas orientales en el Libro de Mormón revela la sofisticación literaria de sus textos.',
];
$id = wp_insert_post($data);
echo "Post created: $id (" . strlen($c) . " bytes)\n";
wp_set_object_terms($id, ['libro-de-mormon','hebreo','antiguo-testamento','escritura','quiasmo'], 'post_tag');
wp_set_object_terms($id, ['mosiah-5','alma-36','alma-41','helaman-5','3-nefi-11','mormon-9','eter-12','moroni-10','isaias-40','salmos-119'], 'bc_chapter');
wp_set_object_terms($id, ['el-lenguaje-del-libro-de-mormon'], 'collection');
update_post_meta($id, '_series_position', 3);
echo "Done.\n";
