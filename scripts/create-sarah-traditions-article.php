<?php
/**
 * Article: "¿Sara, hermana o sobrina de Abraham? Dos interpretaciones bíblicas"
 * Collection: La vida de Abraham
 * Run: type scripts\create-sarah-traditions-article.php | docker exec -i wp_bc php -d display_errors=1
 */

require_once '/var/www/html/wp-load.php';

global $wpdb;

mb_internal_encoding('UTF-8');
$wpdb->query("SET NAMES utf8mb4");

$title = '¿Sara, hermana o sobrina de Abraham? Dos interpretaciones bíblicas';
$slug = 'sara-hermana-o-sobrina-de-abraham';

$content = '<!-- wp:paragraph -->
<p>Hay una pregunta que desconcierta a todo lector atento del Génesis: ¿qué parentesco unía realmente a Abraham y Sara? La respuesta parece sencilla a primera vista —eran marido y mujer— pero las Escrituras presentan dos afirmaciones genealógicas que, a simple vista, parecen divergir. Por un lado, Génesis 20:12 dice que Sara era "hija de mi padre, mas no de mi madre". Por otro, el libro de Abraham (Abraham 2:2) la presenta como hija de Harán, es decir, sobrina de Abraham. ¿Son contradictorias? ¿O existe una clave de lectura que las unifica? Este artículo examina ambas declaraciones desde una perspectiva SUD, dando el peso que merece a la revelación moderna.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Lo que dice Génesis 20:12</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>La declaración bíblica se encuentra en Génesis 20, cuando Abraham está en Gerar y explica a Abimelec por qué dijo que Sara era su hermana: "A la verdad es mi hermana, hija de mi padre, mas no de mi madre, y la tomé por mujer" (Génesis 20:12, RV60). Abraham afirma que Sara era hija de su padre Taré, pero de una madre distinta a la suya. Esto la convertía en media hermana.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>El matrimonio entre hermanastros, aunque más tarde fue prohibido en la ley de Moisés (Levítico 18:9, 20:17), no era inusual en la época patriarcal. Sin embargo, esta declaración debe entenderse dentro de su contexto narrativo: Abraham está dando una excusa a un rey extranjero que lo ha confrontado por haberle entregado a Sara. La declaración, aunque literalmente verdadera en algún sentido, no es necesariamente una declaración genealógica completa. Es una explicación pragmática en un momento de tensión, no un censo familiar.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Lo que revela Abraham 2:2</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>El libro de Abraham, recibido por revelación a través de José Smith y ahora parte de la Perla de Gran Precio, ofrece una declaración genealógica mucho más precisa. En Abraham 2:2 leemos: "Salí de la tierra de Ur, de los caldeos, para ir a la tierra de Canaán; y tomé a Lot, hijo de mi hermano, y a Sarai, hija de mi hermano Harán, por mujer".</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Este pasaje es inequívoco: Sara era hija de Harán, hermano de Abraham. Por tanto, era sobrina de Abraham, no media hermana. La revelación moderna no corrige al Génesis —porque el Génesis no está necesariamente equivocado— sino que provee la información genealógica exacta que el texto bíblico, por su naturaleza y propósitos, no había detallado.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Lo notable es que la tradición rabínica antigua —independiente de José Smith— llegó a la misma conclusión. Rashi, el gran comentarista judío del siglo XI, y los Targumim (traducciones arameas de la Biblia) identificaban a Sara con Iscá, la otra hija de Harán mencionada en Génesis 11:29. El Talmud (Sanedrín 69b) afirma explícitamente que Iscá era Sara. Esta convergencia entre la revelación moderna y la erudición rabínica milenaria da un peso extraordinario a la interpretación de que Sara era hija de Harán.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">La convergencia con la tradición rabínica: Sara como Iscá</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>La identificación de Sara con Iscá no es una invención moderna ni una lectura forzada. El Talmud de Babilonia (Sanedrín 69b) lo declara sin ambages, y Rashi lo desarrolla extensamente en su comentario sobre Génesis 11:29. El nombre "Iscá" (ירכּשִׂי) significaría "la que ve" o "la que profetiza", en alusión a la capacidad profética de Sara.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Esta identificación, que la erudición judía ha preservado durante casi dos milenios, coincide exactamente con lo que la revelación moderna restableció en Abraham 2:2. La coincidencia es difícil de explicar si el libro de Abraham fuera una invención del siglo XIX —una época en que estos textos rabínicos no eran de conocimiento común entre los cristianos estadounidenses. La convergencia apunta a que tanto la tradición rabínica como José Smith bebieron de una fuente histórica auténtica que el texto bíblico, por su género literario, no conservó.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Claves de reconciliación desde la perspectiva SUD</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>La pregunta no es si la revelación moderna está en lo correcto —el libro de Abraham es Escritura canónica para los Santos de los Últimos Días— sino cómo entender la declaración de Abraham en Génesis 20:12 a la luz de esa revelación. Existen varias claves hermenéuticas que los eruditos SUD han propuesto, y que no solo reconcilian ambos textos sino que refuerzan la consistencia interna de las Escrituras.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">1. La explicación más sólida: la adopción de nietos en el mundo antiguo</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Si Sara era hija de Harán, entonces era nieta de Taré. Tras la muerte prematura de Harán en Ur de los caldeos (Génesis 11:28), es muy probable que Taré, como cabeza de familia, haya adoptado a Sara como hija propia para asegurar su bienestar y su lugar en el linaje. En el antiguo Cercano Oriente, la adopción de nietos huérfanos por parte del abuelo era una práctica conocida y documentada.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>De esta manera, Sara era simultáneamente nieta biológica de Taré (e hija de Harán) e hija legal de Taré. Abraham podía decir con toda verdad que ella era "hija de mi padre" —en sentido legal— sin que eso contradijera el hecho de que biológicamente era su sobrina. Esta explicación, propuesta por eruditos como E. A. Speiser (Anchor Bible, 1964) y Nahum Sarna (JPS Torah Commentary, 1989), es la que mejor armoniza ambos textos sin forzar ninguno.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">2. El sentido amplio de "hermana" en el egipcio antiguo y el hebreo bíblico</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>En egipcio antiguo, la palabra <em>snt</em> significaba principalmente "hermana" pero también "esposa". Los eruditos Stephen O. Smoot, John Gee, Kerry Muhlestein y Thompson, en su artículo "Did Abraham Lie about His Wife, Sarai?" (<em>BYU Studies</em>, 2022), demuestran que Abraham, al usar este término ambiguo en el contexto egipcio, no estaba diciendo algo falso. Como explica John Gee, especialista en lengua egipcia antigua:</p>
<!-- /wp:paragraph -->

<!-- wp:quote -->
<blockquote class="wp-block-quote"><p>In the Egyptian of Abraham\'s day, there are two words for wife. One (<em>ḥmt</em>) means only \'wife\'; the other (<em>snt</em>) means principally \'sister\' but can also mean \'wife.\' So by using an ambiguous term, Abraham was not saying something that was false.</p><cite>John Gee, <em>An Introduction to the Book of Abraham</em>, RSC/BYU, 2017, p. 102</cite></blockquote>
<!-- /wp:quote -->

<!-- wp:paragraph -->
<p>El artículo de BYU Studies concluye que "for an Egyptian audience, Abram\'s calling Sarai his sister would not have precluded her being his wife." El punto no es que Abraham mintiera usando un eufemismo, sino que empleó el lenguaje con precisión dentro de su contexto cultural y lingüístico.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Por otra parte, en hebreo bíblico la palabra "hermana" (אָחוֹת, <em>ajot</em>) también podía designar a una parienta cercana, como se observa en Cantares 4:9-10, 12; 5:1-2, donde el esposo llama "hermana" a su amada. Este uso más amplio del término —observado por la erudición bíblica en general, independientemente del artículo de BYU Studies— permitía que Abraham llamara "hermana" a Sara sin engaño, pues ella era ciertamente una parienta cercana.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Cuando Abraham llamó "hermana" a Sara, usó el término en su sentido amplio de "parienta cercana" —que ella ciertamente era, tanto por sangre (sobrina) como por adopción (hermana legal). La iniciativa de decir que era su hermana fue de Dios mismo, quien sabía que el uso del término no sería engañoso en el contexto cultural egipcio (Gaye Strathearn, "The Wife/Sister Experience", RSC/BYU).</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">3. La guía de estudio SUD: costumbres de la época</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Las <em>Ayudas para las Escrituras: Antiguo Testamento</em> ofrecen una reconciliación práctica:</p>
<!-- /wp:paragraph -->

<!-- wp:quote -->
<blockquote class="wp-block-quote"><p>Debido a que tanto Abraham como Sara descendían de Taré, es probable que las costumbres de la época hubieran permitido que Abraham se refiriera a Sara como su hermana de forma acertada.</p><cite>Ayudas para las Escrituras: Antiguo Testamento, "Génesis 12–17; Abraham 1–2", 2025</cite></blockquote>
<!-- /wp:quote -->

<!-- wp:paragraph -->
<p>Esta declaración reconoce que la cultura patriarcal utilizaba términos de parentesco de manera más amplia que nuestras categorías modernas, y que dentro de ese marco la declaración de Abraham era correcta.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">4. Abraham 2:22-25 como contexto adicional</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Un detalle que a menudo se pasa por alto es que el propio libro de Abraham explica por qué Abraham pidió a Sara que dijera que era su hermana. En Abraham 2:22-25, el Señor revela a Abraham que los egipcios lo matarían para tomar a Sara, y le instruye: "Por tanto, mira, te ruego que esto hagas: dí que eres mi hermana, para que me vaya bien por causa de ti, y salve mi alma por causa de ti". La iniciativa no fue de Abraham, sino de Dios. El Señor mismo autorizó y dirigió esta declaración, sabiendo que era verdadera dentro del marco cultural y lingüístico pertinente.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Si la declaración hubiera sido una mentira, Dios no la habría ordenado. El hecho de que el Señor instruyera a Abraham a decir "es mi hermana" es la evidencia más poderosa de que, dentro del contexto cultural y legal de la época, la afirmación era verdadera.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Un diagrama de ambas interpretaciones</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>El siguiente diagrama de Mermaid ilustra las dos interpretaciones genealógicas. La línea continua representa la genealogía según Abraham 2:2 y la tradición rabínica (Sara como hija de Harán). La línea punteada representa la declaración de Génesis 20:12 entendida como adopción legal por parte de Taré:</p>
<!-- /wp:paragraph -->

<!-- wp:merpress/mermaidjs -->
<pre class="mermaid">graph TD
    T[Taré] -->|hijo| A[Abraham]
    T -->|adoptó como hija| S1["Sara<br/><em>hija adoptiva de Taré</em>"]
    T -->|hijo| H[Harán]
    T -->|hijo| N[Nacor]
    H -->|hija biológica| S2["Sara<br/><em>hija de Harán</em>"]
    H -->|hijo| L[Lot]
    H -->|hija| M[Milca]
    A -->|esposa| S2

    subgraph leg [Leyenda]
        L1[Línea continua: Genealogía biológica]
        L2[Línea punteada: Vínculo legal/adoptivo]
    end</pre>
<!-- /wp:merpress/mermaidjs -->

<!-- wp:paragraph -->
<p style="font-style:italic;font-size:0.9em">Sara era hija biológica de Harán (según Abraham 2:2 y la tradición rabínica) y, tras la muerte de su padre, fue adoptada legalmente por Taré, su abuelo. Esto la convertía simultáneamente en sobrina de Abraham (por sangre) y hermana legal (por adopción). Ambas declaraciones son verdaderas desde perspectivas distintas.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Implicaciones teológicas</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Esta cuestión trasciende la curiosidad genealógica y tiene implicaciones profundas para la fe:</p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ol class="wp-block-list">
<li><strong>El libro de Abraham como Escritura auténtica:</strong> La convergencia entre Abraham 2:2 y la tradición rabínica antigua —que identifica a Sara con Iscá, hija de Harán— es un testimonio poderoso de la autenticidad histórica del libro de Abraham. José Smith no habría tenido acceso en la década de 1830 a los detalles del Talmud de Babilonia y los comentarios de Rashi que confirmaban esta identificación. La coincidencia apunta a que el libro de Abraham es lo que afirma ser: una traducción inspirada de un registro antiguo auténtico.</li>
<li><strong>La Biblia no es un registro genealógico exhaustivo:</strong> El Génesis no se propone dar una genealogía completa de cada persona. Su propósito es teológico e histórico-narrativo, no censal. La declaración de Abraham en Génesis 20:12 es verdadera en el contexto en que fue dicha, pero no agota la realidad genealógica. La revelación moderna viene a suplir y aclarar lo que el texto bíblico, por su propia naturaleza, no detalla.</li>
<li><strong>La integridad de Abraham:</strong> Si Abraham hubiera mentido, Dios no habría ordenado la declaración. El Señor mismo instruyó a Abraham a decir que Sara era su hermana (Abraham 2:22-25). Esto solo es posible si la declaración era verdadera dentro del marco cultural —y lo era, como parienta cercana y hermana adoptiva. Duane Boyce ("Why Abraham Was Not Wrong to Lie", BYU Studies) añade que, incluso bajo el supuesto más restrictivo, Abraham actuó dentro de su marco ético sin violar la verdad.</li>
<li><strong>La providencia divina en el matrimonio de Abraham y Sara:</strong> Sara fue tanto la esposa escogida por revelación como la madre del convenio. Su parentesco exacto —sobrina por sangre, hermana por adopción— no afecta en nada la legitimidad del matrimonio patriarcal ni el papel central que ella ocupa en la historia de la redención.</li>
</ol>
<!-- /wp:list -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Conclusión</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>La pregunta "¿Sara, hermana o sobrina de Abraham?" encuentra su mejor respuesta cuando se deja hablar a toda la revelación —la antigua y la moderna. El libro de Abraham, confirmado por la tradición rabínica milenaria, revela que Sara era hija de Harán y, por tanto, sobrina de Abraham. La declaración de Génesis 20:12 se entiende mejor como una referencia a la adopción legal de Sara por parte de Taré después de la muerte de Harán, o como el uso del término "hermana" en su sentido semítico amplio de "parienta cercana".</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Lejos de haber contradicción, hay complementariedad. El texto bíblico registró lo que Abraham dijo en un momento de necesidad; la revelación moderna restableció el dato genealógica exacto. Una vez más se cumple la promesa de que Dios revela "línea sobre línea, mandamiento tras mandamiento" (2 Nefi 28:30), y que la Escritura moderna no viene a negar a la antigua sino a esclarecerla y completarla. Sara, hija de Harán, sobrina y esposa de Abraham, sigue siendo —por encima de todo— la madre del convenio y una mujer de fe extraordinaria.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Fuentes consultadas</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul>
<li>Santa Biblia, Reina-Valera 1960, Génesis 11:27-31; 20:12.</li>
<li>Perla de Gran Precio, Abraham 2:2, 22-25.</li>
<li>Gaye Strathearn, <a href="https://rsc.byu.edu/sperry-symposium-classics-old-testament" target="_blank" rel="noopener noreferrer">"The Wife/Sister Experience: Pharaoh\'s Introduction to Jehovah"</a>, en <em>Sperry Symposium Classics: The Old Testament</em>, RSC/BYU, 2005.</li>
<li>John Gee, <a href="https://rsc.byu.edu/book/introduction-book-abraham" target="_blank" rel="noopener noreferrer"><em>An Introduction to the Book of Abraham</em></a>, RSC/BYU, 2017, p. 102.</li>
<li>John Gee, <a href="https://rsc.byu.edu/book/temple-time-eternity" target="_blank" rel="noopener noreferrer">"The Book of Abraham in the Ancient World"</a>, en <em>The Temple in Time and Eternity</em>, RSC/BYU.</li>
<li>Stephen O. Smoot, John Gee, Kerry Muhlestein, y Thompson, <a href="https://byustudies.byu.edu/article/did-abraham-lie-about-his-wife-sarai/" target="_blank" rel="noopener noreferrer">"Did Abraham Lie about His Wife, Sarai?"</a>, <em>BYU Studies</em> 61/4, 2022.</li>
<li>Duane Boyce, <a href="https://byustudies.byu.edu/" target="_blank" rel="noopener noreferrer">"Why Abraham Was Not Wrong to Lie"</a>, <em>BYU Studies</em>.</li>
<li>E. A. Speiser, <em>Genesis: Introduction, Translation, and Notes</em>, Anchor Bible, Doubleday, 1964.</li>
<li>Nahum M. Sarna, <em>Genesis: The JPS Torah Commentary</em>, Jewish Publication Society, 1989.</li>
<li><a href="https://www.churchofjesuschrist.org/study/manual/scripture-helps-old-testament" target="_blank" rel="noopener noreferrer">Ayudas para las Escrituras: Antiguo Testamento, "Génesis 12–17; Abraham 1–2"</a>, 2025.</li>
<li><a href="https://www.churchofjesuschrist.org/study/scriptures/bd" target="_blank" rel="noopener noreferrer">Bible Dictionary</a>, "Sarah".</li>
<li>Rashi, Comentario sobre Génesis 11:29.</li>
<li>Talmud de Babilonia, Sanedrín 69b.</li>
<li>Targumim sobre Génesis 11:29.</li>
</ul>
<!-- /wp:list -->';

// Update existing article via $wpdb
$result = $wpdb->update('wp_posts', [
    'post_content' => $content,
    'post_title' => $title,
    'post_name' => $slug,
    'post_modified' => current_time('mysql'),
], ['ID' => 2658]);

if ($result === false) {
    echo "Error updating article: " . $wpdb->last_error . "\n";
    exit(1);
}

echo "Article (ID 2658) updated successfully.\n";

// Verify blocks
$saved = $wpdb->get_var("SELECT post_content FROM wp_posts WHERE ID = 2658");
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

echo "Done. URL: http://localhost:8080/$slug/\n";
