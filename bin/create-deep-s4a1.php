<?php
require_once('/var/www/html/wp-load.php');

$title = 'El egipcio reformado: qué sabemos y qué implica';
$slug = 'el-egipcio-reformado-que-sabemos';
$excerpt = 'Mormón 9:32-33 menciona que las planchas del Libro de Mormón fueron escritas en un sistema de escritura llamado egipcio reformado. Este artículo examina las evidencias históricas, arqueológicas y lingüísticas disponibles, las teorías académicas sobre su naturaleza y las perspectivas desde la erudición de la Restauración.';

$sections = [];

// INTRO
$sections[] = [
    'type' => 'paragraph',
    'content' => 'Mormón 9:32–33 contiene una de las afirmaciones lingüísticas más singulares de toda la literatura religiosa: «Y he aquí, hemos escrito estos anales según nuestro conocimiento, en los caracteres que entre nosotros se llaman egipcio reformado; y los hemos transmitido y alterado conforme a nuestra manera de hablar. Y si nuestras planchas hubiesen sido suficientemente amplias, habríamos escrito en hebreo; pero también hemos alterado el hebreo; y si hubiésemos podido escribir en hebreo, he aquí, no habríais tenido ninguna imperfección en nuestros anales». Esta declaración, escrita por Moroni hacia el año 421 d. C., abre un campo de investigación que intersecta la arqueología del Cercano Oriente, la epigrafía semítica, la historia de la escritura y la crítica textual del movimiento de la Restauración.'
];

$sections[] = [
    'type' => 'heading',
    'content' => 'Las menciones del egipcio en el Libro de Mormón'
];

$sections[] = [
    'type' => 'paragraph',
    'content' => 'El término «egipcio reformado» aparece una sola vez en el canon de la Iglesia de Jesucristo de los Santos de los Últimos Días, precisamente en Mormón 9:32. Sin embargo, el concepto de que los profetas nefitas utilizaran la lengua egipcia para sus registros sagrados se encuentra ya en el primer versículo del libro: Nefi declara que escribe «en la lengua de mi padre, la cual consiste en el aprendizaje de los judíos y el idioma de los egipcios» (1 Nefi 1:2). Cuatrocientos setenta años después, el rey Benjamín, monarca justo nefita, enseñó a sus hijos «el idioma de los egipcios», señalando que Lehi había sido «instruido en el idioma de los egipcios» y que esta tradición lingüística se transmitió de generación en generación (Mosíah 1:2–4).'
];

$sections[] = [
    'type' => 'paragraph',
    'content' => 'La amplitud temporal entre estas referencias es notable: unos mil años separan a Lehi (c. 600 a. C.) de Moroni (c. 421 d. C.). Durante ese milenio, tanto el hebreo como el egipcio experimentaron transformaciones lingüísticas significativas. Como señala el manual del Instituto «Libro de Mormón: Manual del alumno», Moroni reconoció explícitamente que tanto el egipcio como el hebreo se habían «alterado» en boca de los nefitas, y que el factor determinante para usar el egipcio reformado era la falta de espacio en las planchas de metal —el egipcio, siendo un sistema con signos bi-consonánticos y tri-consonánticos, ocupaba menos espacio que el hebreo en caracteres paleohebreos.'
];

// SECCIÓN 2
$sections[] = [
    'type' => 'heading',
    'content' => 'El debate académico: ¿qué era exactamente el egipcio reformado?'
];

$sections[] = [
    'type' => 'paragraph',
    'content' => 'No existe un consenso académico definitivo entre los eruditos de la Restauración sobre qué significa exactamente la afirmación de Nefi de que su registro «consiste en el aprendizaje de los judíos y el idioma de los egipcios». La Encyclopedia of Mormonism, en su artículo «Book of Mormon Language» escrito por Brian D. Stubbs, resume las principales hipótesis: (1) que los registros nefitas estaban escritos en hebreo pero utilizando caracteres egipcios modificados; (2) que estaban escritos en lengua egipcia (no solo caracteres) y que el hebreo era la lengua hablada pero no la escrita; y (3) que existía una combinación variable de ambos idiomas según la época y el escriba.'
];

$sections[] = [
    'type' => 'paragraph',
    'content' => 'Un estudio más reciente publicado en Interpreter: A Journal of Mormon Scripture, titulado «Learning Nephi\'s Language: Creating a Context for 1 Nephi 1:2» (aprendiendo la lengua de Nefi: creando un contexto para 1 Nefi 1:2), examina en detalle el estado de la cuestión. El estudio identifica al menos cuatro posturas académicas entre los especialistas SUD: (a) que Nefi escribía en hebreo pero con caracteres del egipcio hierático-palestino, una forma abreviada de la escritura hierática que se utilizaba en Palestina en el siglo VII a. C. (postura defendida por John A. Tvedtnes y Stephen D. Ricks); (b) que Nefi escribía en lengua egipcia pero adaptada para expresar conceptos hebreos (John L. Sorenson); (c) que Nefi escribía en egipcio siguiendo los métodos de las escuelas de escribas israelitas que combinaban ambas tradiciones (Hugh Nibley); y (d) que Lehi pudo haber desarrollado un sistema de escritura único que fusionaba elementos de ambas lenguas de una manera particular.'
];

$sections[] = [
    'type' => 'paragraph',
    'content' => 'El artículo «Inspired by a Better Cause» (Inspirado por una mejor causa), también en Interpreter Journal, señala que «no existe un consenso académico SUD actual sobre si esto se refiere a la lengua egipcia contemporánea (entonces) o a una de las diversas escrituras (ej., jeroglíficos, hierática, hierática incisa, hierática anormal o demótica) en las que se escribía el egipcio en esa época y que a veces se utilizaban en los reinos de Israel y Judá». Esta declaración es importante porque reconoce la complejidad real del problema: no se trata de una sola respuesta simple.'
];

// SECCIÓN 3
$sections[] = [
    'type' => 'heading',
    'content' => 'El contexto arqueológico: el hierático palestino'
];

$sections[] = [
    'type' => 'paragraph',
    'content' => 'Uno de los avances más significativos para comprender el egipcio reformado proviene de la arqueología del Cercano Oriente. Se ha identificado una tradición de escritura conocida como «hierático palestino» —una adaptación local de la escritura hierática egipcia utilizada en Palestina durante la Edad del Hierro (aproximadamente 1200–586 a. C.). Esta escritura aparece en ostraca (fragmentos de cerámica) y papiros encontrados en sitios arqueológicos de Israel y Judá, y se utilizaba principalmente para registros administrativos, contables y epistolares.'
];

$sections[] = [
    'type' => 'paragraph',
    'content' => 'El artículo «Looking Again at the Anthon Transcripts» (Reexaminando los documentos Anthon), publicado en Interpreter Journal, señala que «se sabe por la arqueología que existía una tradición de escribas que utilizaba el hierático egipcio en Israel durante la época de Nefi. Este hierático palestino es actualmente el candidato más plausible para una escritura egipcia que Nefi habría utilizado para hacer su propio registro». Esta evidencia arqueológica proporciona un contexto concreto y verificable para la afirmación de Nefi de que escribía utilizando «el idioma de los egipcios».'
];

$sections[] = [
    'type' => 'paragraph',
    'content' => 'El artículo «The Last Nephite Scribes» (Los últimos escribas nefitas) va más allá y postula que Lehi y Nefi eran «escribas josefitos altamente entrenados» asociados con una escuela de escribas oficial en Jerusalén que «preservaba tradiciones alternativas de historia y registros proféticos josefitos en lengua egipcia». Según esta hipótesis, los nefitas no aprendieron egipcio de manera improvisada, sino que provenían de una tradición escribal establecida que combinaba el aprendizaje hebreo con el egipcio.'
];

// SECCIÓN 4
$sections[] = [
    'type' => 'heading',
    'content' => 'La Transcripción Anthon y los caracteres'
];

$sections[] = [
    'type' => 'paragraph',
    'content' => 'Uno de los pocos artefactos físicos relacionados con el egipcio reformado es el llamado «documento de los Caractères» (Caractors document), una hoja de papel que contiene caracteres copiados de las planchas de oro y que Martin Harris llevó a consultar con eruditos de Nueva York en 1828. El documento, que mide aproximadamente 20 × 8 cm, contiene líneas de caracteres escritos de izquierda a derecha que no corresponden a ningún sistema de escritura conocido.'
];

$sections[] = [
    'type' => 'paragraph',
    'content' => 'El ensayo de los Temas del Evangelio «Las consultas de Martin Harris con los eruditos» narra cómo Harris visitó primero al profesor Charles Anthon de la Universidad de Columbia. Según el relato de Harris, Anthon declaró que los caracteres eran auténticos y que la traducción era correcta, pero al enterarse de que las planchas habían sido reveladas por un ángel, rompió su certificación y se negó a seguir ayudando. Anthon, por su parte, dio una versión diferente: afirmó que Harris le había mostrado lo que parecía ser un engaño y que se negó a participar.'
];

$sections[] = [
    'type' => 'paragraph',
    'content' => 'Investigaciones recientes de los Joseph Smith Papers han arrojado nueva luz sobre este episodio. El análisis de la caligrafía del documento «Caractors» sugiere que no fue escrito por José Smith en 1828 sino por John Whitmer en 1829, lo que indica que el documento conservado es probablemente una copia de un facsímil anterior. El artículo «Looking Again at the Anthon Transcripts» sugiere además que Harris pudo haber llevado más de un documento a Anthon, lo que explicaría las discrepancias entre los relatos.'
];

// SECCIÓN 5
$sections[] = [
    'type' => 'heading',
    'content' => 'Hipótesis lingüísticas: entre el hebreo y el egipcio'
];

$sections[] = [
    'type' => 'paragraph',
    'content' => 'La Encyclopedia of Mormonism presenta un análisis lingüístico detallado. Señala que en hebreo antiguo, tanto el hebreo como el egipcio se escribían solo con consonantes. Sin embargo, a diferencia del hebreo, el egipcio poseía signos bi-consonánticos e incluso tri-consonánticos, lo que permitía una escritura más compacta —exactamente la razón que Moroni aduce para usar el egipcio reformado en lugar del hebreo. El artículo sugiere que «si los caracteres egipcios se alteraron a medida que la lengua viva cambiaba, entonces los nefitas probablemente estaban usando esos caracteres para escribir su lengua hablada, que era en gran medida hebreo».'
];

$sections[] = [
    'type' => 'paragraph',
    'content' => 'La hipótesis del hebreo escrito en caracteres egipcios encuentra apoyo adicional en el estudio de nombres nefitas. El nombre «Nefi» (Nephi) ha sido analizado por varios eruditos como una forma del egipcio nfr (bueno, bello, perfecto). John Gee, en «A Note on the Name Nephi», y Matthew L. Bowen, en «Internal Textual Evidence for the Egyptian Origin of Nephi\'s Name», han argumentado que Nefi es una forma del egipcio nfr con la pronunciación tardía nefi, lo que sugiere que los nefitas no solo usaban caracteres egipcios sino que también daban nombres de origen egipcio.'
];

// SECCIÓN 6
$sections[] = [
    'type' => 'heading',
    'content' => 'La perspectiva de la Restauración'
];

$sections[] = [
    'type' => 'paragraph',
    'content' => 'Desde la doctrina de la Restauración, el egipcio reformado es un testimonio de la realidad histórica y material del Libro de Mormón. El ensayo «La traducción del Libro de Mormón» de los Temas del Evangelio explica que José Smith tradujo «por el don y el poder de Dios» utilizando instrumentos sagrados como el Urim y Tumim y la piedra vidente. Oliver Cowdery testificó que, al mirar a través del Urim y Tumim, José «era capaz de leer en inglés los caracteres en egipcio reformado que estaban grabados en las planchas».'
];

$sections[] = [
    'type' => 'paragraph',
    'content' => 'El Élder Neal A. Maxwell, del Cuórum de los Doce Apóstoles, ofreció una reflexión significativa sobre el proceso de traducción: «Muchos de los que leen el Libro de Mormón desean saber (y se entiende que lo deseen) más acerca de la forma en la que salió a luz, incluso el proceso de traducción en sí… lo que ya sabemos de la salida a luz del Libro de Mormón es suficiente, pero no sabemos todos los pormenores… Quizá… se han retenido los detalles de la traducción porque la intención es que nos sumerjamos en el contenido del libro en vez de preocuparnos excesivamente con el proceso por medio del cual se recibió».'
];

$sections[] = [
    'type' => 'paragraph',
    'content' => 'La evidencia arqueológica del hierático palestino, los análisis lingüísticos de los nombres nefitas, el estudio de las escuelas de escribas del antiguo Israel y las múltiples hipótesis académicas sobre el egipcio reformado convergen en una conclusión: lejos de ser una afirmación aislada e inexplicable, la declaración de Moroni sobre el egipcio reformado se inserta coherentemente en lo que hoy sabemos sobre las prácticas de escritura en el Cercano Oriente antiguo.'
];

// SECCIÓN 7
$sections[] = [
    'type' => 'heading',
    'content' => 'Conclusión'
];

$sections[] = [
    'type' => 'paragraph',
    'content' => 'El egipcio reformado mencionado en Mormón 9 no es una anomalía lingüística fantástica, sino un indicio de la compleja realidad multicultural y multilingüe del antiguo Cercano Oriente. La investigación arqueológica del hierático palestino, los estudios de las escuelas de escribas israelitas, el análisis de los nombres nefitas y la crítica textual de los manuscritos del Libro de Mormón convergen para sugerir que estamos ante un fenómeno histórico lingüístico real: un sistema de escritura que combinaba caracteres egipcios con una lengua semítica subyacente, transmitido y transformado a lo largo de un milenio de historia nefita.'
];

$sections[] = [
    'type' => 'paragraph',
    'content' => 'Para los miembros de la Iglesia de Jesucristo de los Santos de los Últimos Días, el estudio del egipcio reformado no es un ejercicio de erudición abstracta, sino una ventana a la fidelidad de los profetas nefitas que preservaron el registro sagrado a través de los siglos, y un testimonio de que el Señor preparó los medios tanto para su preservación como para su traducción en los últimos días.'
];

// Build content
$parts = [];
foreach ($sections as $sec) {
    switch ($sec['type']) {
        case 'heading':
            $parts[] = '<!-- wp:heading --><h2 class="wp-block-heading">' . $sec['content'] . '</h2><!-- /wp:heading -->';
            break;
        case 'paragraph':
            $parts[] = '<!-- wp:paragraph --><p>' . $sec['content'] . '</p><!-- /wp:paragraph -->';
            break;
    }
}

// References table
$parts[] = '<!-- wp:heading --><h2 class="wp-block-heading">Referencias de las Escrituras</h2><!-- /wp:heading -->';
$parts[] = '<!-- wp:table --><figure class="wp-block-table"><table class="bc-forma-t"><thead><tr><th>Concepto</th><th>Referencia</th></tr></thead><tbody>';
$refs = [
    ['concept' => 'Anales escritos en caracteres de egipcio reformado', 'ref' => 'Mormón 9:31–34'],
    ['concept' => 'Nefi escribe en la lengua de los egipcios', 'ref' => '1 Nefi 1:2–3'],
    ['concept' => 'El rey Benjamín enseña el idioma de los egipcios a sus hijos', 'ref' => 'Mosíah 1:2–4'],
    ['concept' => 'Lehi podía leer las planchas de bronce por conocer el egipcio', 'ref' => 'Mosíah 1:4'],
    ['concept' => 'Mosíah tradujo los anales jareditas mediante intérpretes', 'ref' => 'Mosíah 28:11–17'],
    ['concept' => 'Los anales preservados con esmero de generación en generación', 'ref' => 'Alma 37:1–5'],
    ['concept' => 'Las planchas de Nefi como anales sagrados', 'ref' => '1 Nefi 19:1–6'],
    ['concept' => 'La fidelidad de los profetas en la transmisión del registro', 'ref' => 'Palabras de Mormón 1:1–11'],
    ['concept' => 'Moroni habla de la debilidad humana en la escritura', 'ref' => 'Éter 12:23–27'],
    ['concept' => 'El cumplimiento de la profecía sobre la salida a luz del registro', 'ref' => '3 Nefi 5:12–18'],
    ['concept' => 'El testimonio de los testigos de las planchas de oro', 'ref' => 'José Smith—Historia 1:59–65'],
    ['concept' => 'Toda la Escritura es inspirada por Dios', 'ref' => '2 Timoteo 3:16–17'],
];
foreach ($refs as $r) {
    $parts[] = '<tr><td>' . $r['concept'] . '</td><td>' . $r['ref'] . '</td></tr>';
}
$parts[] = '</tbody></table></figure><!-- /wp:table -->';

// Sources
$parts[] = '<!-- wp:heading --><h2 class="wp-block-heading">Fuentes consultadas</h2><!-- /wp:heading -->';
$parts[] = '<!-- wp:list --><ul class="wp-block-list">';
$sources = [
    ['title' => 'Book of Mormon Language', 'url' => 'https://eom.byu.edu/index.php/Book_of_Mormon_Language', 'desc' => 'Encyclopedia of Mormonism'],
    ['title' => 'La traducción del Libro de Mormón', 'url' => 'https://www.churchofjesuschrist.org/study/manual/gospel-topics-essays/book-of-mormon-translation?lang=spa', 'desc' => 'Temas del Evangelio — ChurchofJesusChrist.org'],
    ['title' => 'Las consultas de Martin Harris con los eruditos', 'url' => 'https://www.churchofjesuschrist.org/study/manual/gospel-topics-essays/martin-harriss-consultations-with-scholars?lang=spa', 'desc' => 'Temas del Evangelio — ChurchofJesusChrist.org'],
    ['title' => 'Egipcio reformado', 'url' => 'https://es.wikipedia.org/wiki/Egipcio_reformado', 'desc' => 'Wikipedia en español'],
    ['title' => 'Learning Nephi\'s Language: Creating a Context for 1 Nephi 1:2', 'url' => 'https://journal.interpreterfoundation.org/learning-nephis-language-creating-a-context-for-1-nephi-12/', 'desc' => 'Interpreter: A Journal of Mormon Scripture'],
    ['title' => 'Looking Again at the Anthon Transcripts', 'url' => 'https://journal.interpreterfoundation.org/looking-again-at-the-anthon-transcripts/', 'desc' => 'Interpreter: A Journal of Mormon Scripture'],
    ['title' => 'The Last Nephite Scribes', 'url' => 'https://journal.interpreterfoundation.org/the-last-nephite-scribes/', 'desc' => 'Interpreter: A Journal of Mormon Scripture'],
    ['title' => 'The Gold Plates and the Translation of the Book of Mormon', 'url' => 'https://www.josephsmithpapers.org/site/the-gold-plates-and-the-translation-of-the-book-of-mormon', 'desc' => 'The Joseph Smith Papers'],
];
foreach ($sources as $s) {
    $parts[] = '<li><a href="' . $s['url'] . '" target="_blank" rel="noopener noreferrer">' . $s['title'] . ' <i class="fas fa-external-link-alt" aria-hidden="true"></i></a> — ' . $s['desc'] . '</li>';
}
$parts[] = '</ul><!-- /wp:list -->';

$post_content = implode("\n", $parts);

$post_data = array(
    'post_title'   => $title,
    'post_name'    => $slug,
    'post_content' => $post_content,
    'post_status'  => 'publish',
    'post_author'  => 1,
    'post_excerpt' => $excerpt,
);

$post_id = wp_insert_post($post_data);
if (!$post_id || is_wp_error($post_id)) {
    echo "Error: " . ($post_id instanceof WP_Error ? $post_id->get_error_message() : 'unknown') . "\n";
    exit(1);
}
echo "Post created with ID: $post_id\n";

// Set tags
wp_set_object_terms($post_id, array('libro-de-mormon', 'planchas-de-oro', 'escritura', 'mormon', 'antiguo-testamento'), 'post_tag');

// Set chapters by slug
wp_set_object_terms($post_id, array('mormon-9', '1-nefi-1', 'mosiah-1', 'mormon-7', 'alma-37', 'omni-1', '3-nefi-5', '3-nefi-1', 'palabras-de-mormon-1', 'eter-12', 'mosiah-28', 'jose-historia-1'), 'bc_chapter');

// Set series
wp_set_object_terms($post_id, array('el-lenguaje-del-libro-de-mormon'), 'collection');

// Set series position
update_post_meta($post_id, '_series_position', 1);

echo "Setup completed successfully for post ID: $post_id\n";
echo "Content length: " . strlen($post_content) . " bytes\n";
