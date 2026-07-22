<?php
require_once('/var/www/html/wp-load.php');

$title = 'Manuscritos y transmisión textual';
$slug = 'manuscritos-y-transmision-textual';

$parts = [];

// Intro
$parts[] = '<!-- wp:paragraph --><p>Ninguno de los autógrafos —los escritos originales de puño y letra de los profetas y apóstoles— ha sobrevivido hasta nuestros días. Lo que poseemos son millares de copias manuscritas realizadas a lo largo de los siglos por escribas dedicados que reprodujeron el texto sagrado con esmero y devoción. La ciencia de la crítica textual estudia estos manuscritos para reconstruir, con la mayor precisión posible, el texto original de la Biblia. Comprender la historia de estos testimonios escritos es apreciar tanto la fragilidad de los materiales antiguos como la asombrosa fidelidad con la que la palabra de Dios ha sido preservada y transmitida a lo largo de las generaciones.</p><!-- /wp:paragraph -->';

// Section 1
$parts[] = '<!-- wp:heading --><h2 class="wp-block-heading">La transmisión del Antiguo Testamento: de los rollos a los masoretas</h2><!-- /wp:heading -->';
$parts[] = '<!-- wp:paragraph --><p>El texto del Antiguo Testamento fue compuesto, copiado y transmitido en hebreo (y en parte en arameo) durante más un milenio antes de la invención de la imprenta. Durante el periodo del Primer y Segundo Templo, los escribas copiaban los textos en rollos de cuero o papiro. Entre los hallazgos arqueológicos más importantes para conocer esta fase de transmisión se encuentran los Rollos del Mar Muerto, descubiertos en las cuevas de Qumrán a partir de 1947. Estos manuscritos, que datan desde el siglo III a. C. hasta el siglo I d. C., incluyeron fragmentos de todos los libros del Antiguo Testamento (excepto Ester) y demostraron que el texto hebreo se había conservado con un grado de precisión extraordinario durante un milenio de copias manuales.</p><!-- /wp:paragraph -->';
$parts[] = '<!-- wp:paragraph --><p>A partir del siglo VI d. C., un grupo de eruditos judíos conocidos como los masoretas (del hebreo <em>masorah</em>, tradición) asumieron la tarea de estandarizar y preservar el texto consonántico del Antiguo Testamento. Dado que el alfabeto hebreo antiguo escribía únicamente las consonantes, existía el riesgo de perder la pronunciación tradicional y la correcta lectura del texto. Los masoretas desarrollaron un sistema complejo de vocales escritas (puntos y rayas colocados alrededor de las consonantes), marcas de acentuación y notas marginales exhaustivas para asegurar que futuras copias no sufrieran alteraciones. Su obra culminó en manuscritos monumentales como el Códice de Alepo (c. 920 d. C.) y el Códice de Leningrado (1008 d. C.), que forman la base de las ediciones impresas modernas del texto hebreo (como la <em>Biblia Hebraica Stuttgartensia</em>).</p><!-- /wp:paragraph -->';
$parts[] = '<!-- wp:paragraph --><p>Un testimonio crucial en la transmisión del Antiguo Testamento es la Septuaginta (LXX), la traducción al griego realizada en Alejandría entre los siglos III y II a. C. La Septuaginta refleja en muchos pasajes un texto consonántico hebreo más antiguo que el que utilizaron los masoretas siglos después, lo que la convierte en una herramienta indispensable para la crítica textual bíblica.</p><!-- /wp:paragraph -->';

// Section 2
$parts[] = '<!-- wp:heading --><h2 class="wp-block-heading">La transmisión del Nuevo Testamento: papiros, unciales y minúsculos</h2><!-- /wp:heading -->';
$parts[] = '<!-- wp:paragraph --><p>La transmisión del Nuevo Testamento es uno de los fenómenos documentales mejor atestiguados de la antigüedad grecorromana. Existen hoy más de 5,800 manuscritos griegos del Nuevo Testamento, además de millares de copias en otras lenguas antiguas (siríaco, copto, latín, etíope, armenio) y más de un millón de citas patrísticas en los escritos de los Padres de la Iglesia. Los eruditos clasifican estos manuscritos en varias categorías principales:</p><!-- /wp:paragraph -->';
$parts[] = '<!-- wp:list --><ul class="wp-block-list"><li><strong>Papiros</strong>: son los testimonios más antiguos que se conservan del Nuevo Testamento. Escritos en hojas de papiro (un material frágil elaborado con la planta del mismo nombre), datan desde el siglo II hasta el siglo IV d. C. Entre los más destacados se encuentran el <abbr title="Papyrus 52">P52</abbr> (que contiene fragmentos del Evangelio de Juan y fecha de c. 125–150 d. C.) y la colección de los papiros Bodmer y Chester Beatty, que incluyen evangelios tempranos y epístolas paulinas.</li><li><strong>Manuscritos unciales</strong>: escritos en pergamino (piel de animal tratada) con letras mayúsculas grandes y cuidadas. Datan desde el siglo IV hasta el siglo IX d. C. Los dos unciales más célebres son el <em>Códice Sinaiticus</em> (s. IV), descubierto en el monasterio de Santa Catalina en el Sinaí, y el <em>Códice Vaticanus</em> (s. IV), resguardado en la Biblioteca Vaticana desde el siglo XV. Ambos contienen la mayor parte de la Septuaginta y del Nuevo Testamento.</li><li><strong>Manuscritos minúsculos</strong>: escritos en pergamino (y más tarde en papel) utilizando una letra cursiva más pequeña y rápida. Surgieron a partir del siglo IX y representan la gran mayoría (más de 2,800) de las copias manuscritas del Nuevo Testamento.</li></ul><!-- /wp:list -->';

// Section 3
$parts[] = '<!-- wp:heading --><h2 class="wp-block-heading">La ciencia de la crítica textual: principios y método</h2><!-- /wp:heading -->';
$parts[] = '<!-- wp:paragraph --><p>Con miles de manuscritos disponibles, y dado que ningún copista era infalible, existen numerosas variantes textuales (diferencias entre una copia y otra). La inmensa mayoría de estas variantes son errores menores e involuntarios de los copistas: omisiones de palabras por saltos de la vista (homoioteleuton), errores ortográficos, confusiones de letras similares o adiciones de notas marginales que terminaron integrándose en el cuerpo del texto. Sin embargo, algunas variantes son deliberadas, introducidas por escribas que intentaban armonizar pasajes paralelos o aclarar dificultades teológicas.</p><!-- /wp:paragraph -->';
$parts[] = '<!-- wp:paragraph --><p>Para evaluar estas variantes y determinar cuál lectura se remonta al original, los críticos textuales aplican dos principios fundamentales:</p><!-- /wp:paragraph -->';
$parts[] = '<!-- wp:list --><ul class="wp-block-list"><li><strong>Evidencia externa</strong>: se evalúa la antigüedad, la distribución geográfica y el peso de los manuscritos que apoyan cada variante. Los manuscritos más antiguos (papiros y unciales tempranos) y los testimonios independientes de diferentes regiones tienen mayor peso.</li><li><strong>Evidencia interna</strong>: se analizan las tendencias psicológicas y estilísticas de los copistas. Se aplica la máxima de que <em>la lectura más difícil suele ser la original</em> (un copista tiende a simplificar o aclarar un texto difícil, rara vez a complicarlo), y que <em>la lectura más breve suele ser la original</em> (los copistas tienden a añadir explicaciones o glosas, rara vez a omitir texto).</li></ul><!-- /wp:list -->';
$parts[] = '<!-- wp:paragraph --><p>El resultado de este riguroso trabajo científico es el texto crítico moderno (como el <em>Novum Testamentum Graece</em> de Nestle-Aland), que proporciona a los traductores el texto griego más cercano posible a los autógrafos originales.</p><!-- /wp:paragraph -->';

// Section 4
$parts[] = '<!-- wp:heading --><h2 class="wp-block-heading">La perspectiva de la Restauración sobre la transmisión textual</h2><!-- /wp:heading -->';
$parts[] = '<!-- wp:paragraph --><p>La Iglesia de Jesucristo de los Santos de los Últimos Días valora profundamente el trabajo de preservación realizado por los escribas y eruditos a lo largo de los siglos, reconociendo que la Biblia ha llegado hasta nosotros con una integridad doctrinal asombrosa. Al mismo tiempo, la doctrina de la Restauración enseña que el texto bíblico sufrió pérdidas y corrupciones durante los siglos posteriores a la muerte de los apóstoles, cuando la autoridad del sacerdocio se perdió y los copistas humanos introdujeron alteraciones doctrinales y omisiones (véase 1 Nefi 13:26–29).</p><!-- /wp:paragraph -->';
$parts[] = '<!-- wp:paragraph --><p>Esta comprensión inspiró la labor de José Smith en la Traducción Inspirada de la Biblia (TJS), iniciada en 1830. A través de la revelación divina, el profeta restauró verdades perdidas, clarificó pasajes oscuros y corrigió errores históricos y doctrinales que se habían deslizado en el texto a través de siglos de copias manuscritas. Para los santos de los últimos días, los descubrimientos arqueológicos (como Qumrán o los papiros antiguos) y la revelación moderna se complementan: la ciencia textual confirma la fidelidad física de la transmisión manuscrita, mientras que la revelación restaura la plenitud del mensaje original.</p><!-- /wp:paragraph -->';

// Conclusion
$parts[] = '<!-- wp:heading --><h2 class="wp-block-heading">Conclusión</h2><!-- /wp:heading -->';
$parts[] = '<!-- wp:paragraph --><p>La historia de los manuscritos y la transmisión textual de la Biblia es un testimonio de la dedicación humana y de la providencia divina. Desde los escribas de Qumrán y los masoretas en sus sinagogas hasta los monjes en los scriptoria medievales y los eruditos modernos con sus papiros fragmentarios, innumerables manos han preservado el hilo conductor de la revelación escrita. Gracias a su labor, hoy podemos leer las palabras exactas que inspiraron a los profetas y apóstoles, y escuchar la voz de Dios a través de las edades.</p><!-- /wp:paragraph -->';

// References table
$parts[] = '<!-- wp:heading --><h2 class="wp-block-heading">Referencias de las Escrituras</h2><!-- /wp:heading -->';
$parts[] = '<!-- wp:table --><figure class="wp-block-table"><table class="bc-forma-t"><thead><tr><th>Concepto</th><th>Referencia</th></tr></thead><tbody>';
$parts[] = '<tr><td>El registro escrito preservado por mandamiento divino</td><td>1 Nefi 19:1–6</td></tr>';
$parts[] = '<tr><td>La pérdida de partes claras y preciosas del libro del Cordero</td><td>1 Nefi 13:26–29</td></tr>';
$parts[] = '<tr><td>La venida de la Biblia y el Libro de Mormón como testigos unidos</td><td>2 Nefi 3:12</td></tr>';
$parts[] = '<tr><td>La palabra de Dios escrita no pasará</td><td>Mateo 24:35</td></tr>';
$parts[] = '<tr><td>Toda la Escritura es inspirada por Dios</td><td>2 Timoteo 3:16–17</td></tr>';
$parts[] = '<tr><td>Los escritos sagrados confiados a los antiguos</td><td>Romanos 3:1–2</td></tr>';
$parts[] = '<tr><td>La fidelidad en la conservación de los registros sagrados</td><td>Alma 37:1–5</td></tr>';
$parts[] = '<tr><td>La profecía sobre los registros que saldrán a luz</td><td>Isaías 29:11–12</td></tr>';
$parts[] = '<tr><td>La palabra de Dios como semilla incorruptible</td><td>1 Pedro 1:23–25</td></tr>';
$parts[] = '<tr><td>El cuidado en la transmisión del registro sagrado</td><td>Mosíah 1:3–6</td></tr>';
$parts[] = '</tbody></table></figure><!-- /wp:table -->';

// Sources
$parts[] = '<!-- wp:heading --><h2 class="wp-block-heading">Fuentes consultadas</h2><!-- /wp:heading -->';
$parts[] = '<!-- wp:list --><ul class="wp-block-list">';
$parts[] = '<li><a href="https://es.wikipedia.org/wiki/Cr%C3%ADtica_textual_%28biblia%29" target="_blank" rel="noopener noreferrer">Crítica textual (Biblia) <i class="fas fa-external-link-alt" aria-hidden="true"></i></a> — Wikipedia en español</li>';
$parts[] = '<li><a href="https://www.britannica.com/topic/biblical-literature/The-text-and-versions-of-the-Old-Testament" target="_blank" rel="noopener noreferrer">The Text and Versions of the Bible <i class="fas fa-external-link-alt" aria-hidden="true"></i></a> — Encyclopaedia Britannica</li>';
$parts[] = '<li><a href="https://rsc.byu.edu/archaeological-discoveries-and-scripture/dead-sea-scrolls-and-bible" target="_blank" rel="noopener noreferrer">The Dead Sea Scrolls and the Bible <i class="fas fa-external-link-alt" aria-hidden="true"></i></a> — BYU Religious Studies Center</li>';
$parts[] = '<li><a href="https://eom.byu.edu/index.php/Bible_Manuscripts_and_Translations" target="_blank" rel="noopener noreferrer">Bible Manuscripts and Translations <i class="fas fa-external-link-alt" aria-hidden="true"></i></a> — Encyclopedia of Mormonism</li>';
$parts[] = '<li><a href="https://www.churchofjesuschrist.org/study/manual/old-testament-student-manual/the-dead-sea-scrolls?lang=spa" target="_blank" rel="noopener noreferrer">Los manuscritos del Mar Muerto <i class="fas fa-external-link-alt" aria-hidden="true"></i></a> — ChurchofJesusChrist.org</li>';
$parts[] = '</ul><!-- /wp:list -->';

$post_content = implode("\n", $parts);

$post_data = array(
    'post_title'   => $title,
    'post_name'    => $slug,
    'post_content' => $post_content,
    'post_status'  => 'publish',
    'post_author'  => 1,
    'post_excerpt' => '¿Cómo llegó la Biblia hasta nosotros? Un recorrido por la historia de la transmisión textual, desde los rollos de Qumrán y los masoretas hasta los papiros y códices del Nuevo Testamento, examinando la ciencia de la crítica textual y la perspectiva de la Restauración.',
);

$post_id = wp_insert_post($post_data);
if (!$post_id || is_wp_error($post_id)) {
    echo "Error: " . ($post_id instanceof WP_Error ? $post_id->get_error_message() : 'unknown') . "\n";
    exit(1);
}
echo "Post created with ID: $post_id\n";

// Set tags
wp_set_object_terms($post_id, array('escritura', 'antiguo-testamento', 'nuevo-testamento', 'traduccion-biblica', 'historia-de-la-biblia'), 'post_tag');

// Set chapters by slug (1-nefi-19, 1-nefi-13, 2-nefi-3, mateo-24, 2-timoteo-3, romanos-3, alma-37, isaias-29, 1-pedro-1, mosiah-1)
wp_set_object_terms($post_id, array('1-nefi-19', '1-nefi-13', '2-nefi-3', 'mateo-24', '2-timoteo-3', 'romanos-3', 'alma-37', 'isaias-29', '1-pedro-1', 'mosiah-1'), 'bc_chapter');

// Set series
wp_set_object_terms($post_id, array('los-idiomas-originales-de-la-biblia'), 'collection');

// Set series position
update_post_meta($post_id, '_series_position', 4);

echo "Setup completed successfully for post ID: $post_id\n";
