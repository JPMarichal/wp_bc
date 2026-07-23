<?php
require_once('/var/www/html/wp-load.php');
$c = '';
$c .= '<p>Un lector contemporáneo que abre el Libro de Mormón advierte de inmediato una familiaridad lingüística inconfundible con la Biblia del rey Jacobo (King James Version, KJV) publicada en 1611. Frases como «and it came to pass» (y aconteció que), el uso constante de los pronombres arcaicos thou, thee y thy, y los verbos terminados en -eth configuran un ropaje lingüístico profundamente bíblico que ha generado tanto admiración como debate académico sobre su significado.</p>';
$c .= '<h2 class="wp-block-heading">La elección del lenguaje bíblico</h2>';
$c .= '<p>Durante el proceso de traducción en 1828 y 1829, José Smith y sus escribas emplearon el vocabulario y la sintaxis propios de la tradición bíblica en inglés. En la cultura de la frontera estadounidense del siglo XIX, la KJV no era simplemente un libro religioso entre muchos: era la principal fuente de instrucción literaria, moral y espiritual. La adopción de este registro lingüístico para verter un registro sagrado antiguo se alinea con la práctica habitual de los traductores de la época, quienes utilizaban el lenguaje elevado de las Escrituras para conferir solemnidad a un texto revelado.</p>';
$c .= '<p>El artículo «A Look at Some Nonstandard Book of Mormon Grammar» en Interpreter Journal examina cómo el inglés del Libro de Mormón contiene formas gramaticales que no son típicas ni del inglés de la KJV ni del inglés del siglo XIX, sino que reflejan una traducción literal de un texto semítico subyacente. Estas formas «no estándar» constituyen evidencia lingüística de que la traducción no fue una composición libre en inglés sino una versión fiel de un original antiguo.</p>';
$c .= '<h2 class="wp-block-heading">Citas bíblicas y paralelismos textuales</h2>';
$c .= '<p>La presencia de largos pasajes del libro de Isaías (como los capítulos 2 a 14 de 2 Nefi) y del Sermón del Monte (en 3 Nefi 12-14) que coinciden con la KJV ha sido objeto de análisis minucioso por parte de eruditos como Royal Skousen, quien ha demostrado que existen diferencias sistemáticas entre las citas del Libro de Mormón y el texto de la KJV que apuntan a un origen textual independiente.</p>';
$c .= '<h2 class="wp-block-heading">La perspectiva de la Restauración</h2>';
$c .= '<p>La Encyclopedia of Mormonism explica que la semejanza del lenguaje del Libro de Mormón con la KJV «parece natural, ya que en la época del profeta José Smith, la KJV era el libro más leído en Estados Unidos». La publicación del Libro de Mormón en inglés utilizando el lenguaje bíblico tradicional facilitó su recepción y estudio por parte de los buscadores de la verdad del siglo XIX.</p>';
$c .= '<h2 class="wp-block-heading">Conclusión</h2>';
$c .= '<p>El estilo y lenguaje del Libro de Mormón reflejan tanto su contexto de traducción en la América del siglo XIX como su origen como texto semítico antiguo. La combinación de lenguaje bíblico tradicional con formas gramaticales no estándar que apuntan a un original hebreo constituye una característica única que apoya su autenticidad como traducción antigua.</p>';
$c .= '<h2 class="wp-block-heading">Referencias de las Escrituras</h2>';
$c .= '<figure class="wp-block-table"><table class="bc-forma-t"><thead><tr><th>Concepto</th><th>Referencia</th></tr></thead><tbody>';
$r = [
['Citas de Isaías en los escritos nefitas','2 Nefi 12:1-16',''],
['El Sermón del Monte dado a los nefitas','3 Nefi 12:1-16',''],
['Discurso del rey Benjamín','Mosíah 2:9-25',''],
['Testimonio de Mormón sobre el lenguaje del registro','Mormón 9:32-34',''],
['Exhortación final de Moroni','Moroni 10:3-5',''],
['Toda la Escritura es inspirada por Dios','2 Timoteo 3:16-17',''],
['La palabra del Señor como lámpara','Salmos 119:105',''],
['Testimonio unificado de la palabra divina','2 Nefi 29:8',''],
['La predicación apostólica basada en las Escrituras','Hechos 17:2-3',''],
['La palabra de Dios como semilla incorruptible','1 Pedro 1:23-25',''],
];
foreach ($r as $v) $c .= "<tr><td>{$v[0]}</td><td>{$v[1]}</td></tr>";
$c .= '</tbody></table></figure>';
$c .= '<h2 class="wp-block-heading">Fuentes consultadas</h2><ul class="wp-block-list">';
$s = [
['Biblia del rey Jacobo','https://es.wikipedia.org/wiki/Biblia_del_rey_Jacobo','Wikipedia en español'],
['King James Version','https://en.wikipedia.org/wiki/King_James_Version','Wikipedia en inglés'],
['Book of Mormon Language','https://eom.byu.edu/index.php/Book_of_Mormon_Language','Encyclopedia of Mormonism'],
['A Look at Some Nonstandard Book of Mormon Grammar','https://journal.interpreterfoundation.org/a-look-at-some-nonstandard-book-of-mormon-grammar/','Interpreter Journal'],
['Book of Mormon Translation (Gospel Topics)','https://www.churchofjesuschrist.org/study/manual/gospel-topics-essays/book-of-mormon-translation?lang=spa','ChurchofJesusChrist.org'],
];
foreach ($s as $v) $c .= "<li><a href=\"{$v[1]}\" target=\"_blank\" rel=\"noopener noreferrer\">{$v[0]} <i class=\"fas fa-external-link-alt\" aria-hidden=\"true\"></i></a> — {$v[2]}</li>";
$c .= '</ul>';
$data = ['post_title'=>'Estilo y lenguaje de la traducción','post_name'=>'estilo-y-lenguaje-de-la-traduccion','post_content'=>$c,'post_status'=>'publish','post_author'=>1,'post_excerpt'=>'El lenguaje del Libro de Mormón en inglés comparte similitudes estilísticas con la Biblia del rey Jacobo.'];
$id = wp_insert_post($data);
if (!$id) { echo "Error\n"; exit(1); }
echo "Post $id (" . strlen($c) . " bytes)\n";
wp_set_object_terms($id, ['libro-de-mormon','king-james','traduccion-libro-mormon','antiguo-testamento'], 'post_tag');
wp_set_object_terms($id, ['1-nefi-20','2-nefi-12','mosiah-2','alma-7','helaman-5','3-nefi-12','mormon-9','eter-12','moroni-10','isaias-48'], 'bc_chapter');
wp_set_object_terms($id, ['el-lenguaje-del-libro-de-mormon'], 'collection');
update_post_meta($id, '_series_position', 5);
echo "Done.\n";
