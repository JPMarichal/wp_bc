<?php
require_once('/var/www/html/wp-load.php');

$c = '';
$c .= '<p>El proceso mediante el cual el profeta José Smith tradujo el Libro de Mormón entre 1827 y 1829 es uno de los acontecimientos mejor documentados de la historia religiosa moderna, y sin embargo sigue siendo uno de los menos comprendidos en su mecánica precisa. Lejos de ser un proceso intelectual o académico convencional, la traducción se llevó a cabo por el don y el poder de Dios, utilizando instrumentos sagrados preparados desde la antigüedad.</p>';

$c .= '<h2 class="wp-block-heading">Los instrumentos de traducción</h2>';
$c .= '<p>Los testimonios de los testigos presenciales —incluyendo a Emma Smith, Martin Harris, Oliver Cowdery y David Whitmer— detallan que José Smith empleó dos tipos de instrumentos durante las sesiones de traducción. El primero eran los intérpretes nefitas o Urim y Tumim, descritos como dos piedras transparentes encajadas en un arco de metal semejante a unos anteojos antiguos, entregados por el ángel Moroni junto con las planchas de oro. El segundo era una piedra vidente (seer stone) de color marrón oscuro que José había encontrado años antes mientras cavaba un pozo en la propiedad de la familia.</p>';
$c .= '<p>El manual del Seminario de Doctrina y Convenios explica que «relatos históricos posteriores indican que además de usar el Urim y Tumim para traducir el Libro de Mormón, José Smith usó otro instrumento llamado piedra vidente. El profeta había descubierto este instrumento varios años antes.» Oliver Cowdery testificó que, al mirar a través del Urim y Tumim, José «era capaz de leer en inglés los caracteres en egipcio reformado que estaban grabados en las planchas».</p>';

$c .= '<h2 class="wp-block-heading">La mecánica espiritual de la traducción</h2>';
$c .= '<p>Los relatos de quienes presenciaron la transcripción describen que José dictaba el texto palabra por palabra mientras observaba los instrumentos o colocaba la piedra en un sombrero para excluir la luz exterior. El artículo «Seers and Stones: The Translation of the Book of Mormon as Divine Visions of an Old-Time Seer» publicado en Interpreter Journal analiza cómo los relatos de testigos sugieren que el texto aparecía reflejado o iluminado mediante revelación directa, y José lo pronunciaba en voz alta sin consultar libros de referencia ni poseer los manuscritos originales a la vista.</p>';
$c .= '<p>El élder Neal A. Maxwell, del Cuórum de los Doce Apóstoles, ofreció una reflexión significativa: «Muchos de los que leen el Libro de Mormón desean saber más acerca de la forma en la que salió a luz, incluso el proceso de traducción en sí. Lo que ya sabemos es suficiente, pero no sabemos todos los pormenores. Quizá se han retenido los detalles de la traducción porque la intención es que nos sumerjamos en el contenido del libro en vez de preocuparnos excesivamente con el proceso.»</p>';

$c .= '<h2 class="wp-block-heading">La perspectiva de la Restauración</h2>';
$c .= '<p>El ensayo «La traducción del Libro de Mormón» de los Temas del Evangelio confirma que la traducción se realizó «por el don y el poder de Dios», utilizando instrumentos preparados por el Señor. El proceso combinaba instrumentos físicos (los intérpretes y la piedra vidente) con revelación espiritual directa, demostrando que Dios obra mediante medios tangibles para transmitir verdades eternas.</p>';

$c .= '<h2 class="wp-block-heading">Conclusión</h2>';
$c .= '<p>El proceso de traducción del Libro de Mormón refleja la forma en que el Señor interviene en la historia humana. Los testimonios de los testigos presenciales, los documentos históricos conservados en los Joseph Smith Papers y los estudios académicos modernos convergen para confirmar que la traducción fue un proceso milagroso pero real, que produjo uno de los libros más influyentes de la historia religiosa de América.</p>';

$c .= '<h2 class="wp-block-heading">Referencias de las Escrituras</h2>';
$c .= '<figure class="wp-block-table"><table class="bc-forma-t"><thead><tr><th>Concepto</th><th>Referencia</th></tr></thead><tbody>';
$r = [
['Mosíah preparado con instrumentos para traducir anales','Mosíah 28:11-17'],
['El vidente que traducirá el libro sellado','2 Nefi 27:6-12'],
['El profeta que no sabe leer y el libro sellado','Isaías 29:11-12'],
['El hermano de Jared prepara piedras tocadas por el dedo del Señor','Éter 3:1-6'],
['La promesa de dar a conocer las palabras del libro','Éter 4:4-8'],
['La entrega de las planchas y los intérpretes a José Smith','José Smith—Historia 1:34-59'],
['El poder dado a José Smith para traducir','Doctrina y Convenios 20:8-12'],
['Toda la Escritura es inspirada por Dios','2 Timoteo 3:16-17'],
['La palabra del Señor como lámpara','Salmos 119:105'],
['El testimonio unificado de la palabra divina','2 Nefi 29:8'],
];
foreach ($r as $v) $c .= "<tr><td>{$v[0]}</td><td>{$v[1]}</td></tr>";
$c .= '</tbody></table></figure>';

$c .= '<h2 class="wp-block-heading">Fuentes consultadas</h2><ul class="wp-block-list">';
$s = [
['La traducción del Libro de Mormón','https://www.churchofjesuschrist.org/study/manual/gospel-topics-essays/book-of-mormon-translation?lang=spa','Temas del Evangelio — ChurchofJesusChrist.org'],
['Book of Mormon Translation','https://eom.byu.edu/index.php/Book_of_Mormon_Translation_By_Joseph_Smith','Encyclopedia of Mormonism'],
['Traducción del Libro de Mormón','https://es.wikipedia.org/wiki/Traducci%C3%B3n_del_Libro_de_Morm%C3%B3n','Wikipedia en español'],
['Seers and Stones','https://journal.interpreterfoundation.org/seers-and-stones-the-translation-of-the-book-of-mormon-as-divine-visions-of-an-old-time-seer/','Interpreter Journal'],
['The Gold Plates','https://www.josephsmithpapers.org/site/the-gold-plates-and-the-translation-of-the-book-of-mormon','Joseph Smith Papers'],
];
foreach ($s as $v) $c .= "<li><a href=\"{$v[1]}\" target=\"_blank\" rel=\"noopener noreferrer\">{$v[0]} <i class=\"fas fa-external-link-alt\" aria-hidden=\"true\"></i></a> — {$v[2]}</li>";
$c .= '</ul>';

$data = [
'post_title' => 'El proceso de traducción de José Smith',
'post_name' => 'proceso-de-traduccion-jose-smith',
'post_content' => $c,
'post_status' => 'publish',
'post_author' => 1,
'post_excerpt' => 'El proceso mediante el cual José Smith tradujo el Libro de Mormón involucró el uso del Urim y Tumim, la piedra vidente y la asistencia divina directa.',
];
$id = wp_insert_post($data);
if (!$id) { echo "Error\n"; exit(1); }
echo "Post $id (" . strlen($c) . " bytes)\n";
wp_set_object_terms($id, ['libro-de-mormon','jose-smith','traduccion-libro-mormon','planchas-de-oro','testigos'], 'post_tag');
wp_set_object_terms($id, ['mosiah-28','alma-37','mormon-9','eter-3','eter-4','2-nefi-27','jose-historia-1','doctrina-y-convenios-17','doctrina-y-convenios-20'], 'bc_chapter');
wp_set_object_terms($id, ['el-lenguaje-del-libro-de-mormon'], 'collection');
update_post_meta($id, '_series_position', 4);
echo "Done.\n";
