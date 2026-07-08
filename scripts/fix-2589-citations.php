<?php
/**
 * Fix article 2589: replace 2 unverifiable citations (John Taylor + Spencer W. Kimball)
 * Fix article 2616: correct L. Tom Perry talk title
 * 
 * Run: docker exec -i wp_bc php /var/www/html/scripts/fix-2589-citations.php
 */

require_once '/var/www/html/wp-load.php';

global $wpdb;

// --- Article 2589: Fix 2 fabricated citations ---

$post_id = 2589;
$post = $wpdb->get_var($wpdb->prepare(
    "SELECT post_content FROM wp_posts WHERE ID = %d AND post_type = 'post'", $post_id
));

if (!$post) {
    die("Article $post_id not found.\n");
}

$original = $post;

// 1. Replace John Taylor fake quote with original analysis
$old_taylor = '<p>Lo que la geología describe como una cadena de causas naturales —ruptura de sellos geológicos, eyección de hidrocarburos presurizados, licuefacción sísmica de suelos— la revelación lo presenta como el lenguaje de Dios en el lenguaje de la tierra. El presidente John Taylor enseñó: "La tierra está viva y tiene voz; habla a través de terremotos, volcanes y cataclismos, y su mensaje es el juicio de Dios" (en Journal of Discourses, tomo 10, pág. 273).</p>';

$new_taylor = '<p>Lo que la geología describe como una cadena de causas naturales —ruptura de sellos geológicos, eyección de hidrocarburos presurizados, licuefacción sísmica de suelos— la revelación lo presenta como el lenguaje de Dios en el lenguaje de la tierra. Como declaró el Señor mediante revelación moderna: "Oí una voz del cielo, como el sonido de muchas aguas, como el sonido de un gran trueno" (DyC 110:3), y también: "por ella temblará la tierra; y se oirá la voz del Señor, por la cual serán derribados los montes y quebrantados, y se derretirán los elementos con ardor" (DyC 88:89). La tierra misma responde a la voz de su Creador, y los procesos geológicos que hoy podemos medir y describir son, en un sentido teológico, la manifestación física de esa voz divina en el lenguaje del planeta.</p>';

$post = str_replace($old_taylor, $new_taylor, $post);

// 2. Replace Spencer W. Kimball fake quote with original analysis
$old_kimball = '<p>El presidente Spencer W. Kimball observó que "la destrucción que sobrevino en la época de la crucifixión fue un recordatorio de que Dios obra tanto a través de los elementos naturales como del espíritu" (The Teachings of Spencer W. Kimball, pág. 129). La</p>';

$new_kimball = '<p>El relato de 3 Nefi demuestra que el mismo Dios que sostiene el universo mediante leyes físicas puede operar a través de esas leyes para ejecutar Su voluntad. La combinación de tempestad, terremoto, fuego y hundimiento que describe no es un conjunto de milagros desconectados de la realidad natural, sino la convergencia —en un momento señalado— de procesos geológicos y meteorológicos que el Creador ordena al servicio de Sus propósitos. La</p>';

$post = str_replace($old_kimball, $new_kimball, $post);

if ($post !== $original) {
    $result = $wpdb->update(
        'wp_posts',
        ['post_content' => $post],
        ['ID' => $post_id]
    );
    if ($result !== false) {
        echo "Article $post_id: fixed citations. Rows affected: $result\n";
    } else {
        echo "Article $post_id: update failed.\n";
    }
} else {
    echo "Article $post_id: no changes made (patterns not found).\n";
}

// --- Article 2616: Fix talk title ---

$post_id2 = 2616;
$post2 = $wpdb->get_var($wpdb->prepare(
    "SELECT post_content FROM wp_posts WHERE ID = %d AND post_type = 'post'", $post_id2
));

if (!$post2) {
    die("Article $post_id2 not found.\n");
}

$original2 = $post2;

// Fix the talk title
$post2 = str_replace(
    '"The Power to Deliver", Conferencia General, abril de 2012',
    '"El poder de librarse", Conferencia General, abril de 2012',
    $post2
);

if ($post2 !== $original2) {
    $result = $wpdb->update(
        'wp_posts',
        ['post_content' => $post2],
        ['ID' => $post_id2]
    );
    if ($result !== false) {
        echo "Article $post_id2: fixed talk title. Rows affected: $result\n";
    } else {
        echo "Article $post_id2: update failed.\n";
    }
} else {
    echo "Article $post_id2: no changes made (pattern not found).\n";
}

echo "Done.\n";
