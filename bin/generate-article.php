<?php
/**
 * Generador universal de artículos para wp_bc
 * Uso: php /var/www/html/bin/generate-article.php <json_file>
 */

if ($argc < 2) {
    echo "Error: Falta la ruta al archivo JSON de configuración del artículo.\n";
    exit(1);
}

$json_path = $argv[1];
if (!file_exists($json_path)) {
    // Intentar ruta relativa al host si se pasa desde fuera
    $json_path_host = 'C:/own/wp_bc/' . ltrim($json_path, '/');
    if (file_exists($json_path_host)) {
        $json_path = $json_path_host;
    } else {
        echo "Error: No se encuentra el archivo JSON: {$argv[1]}\n";
        exit(1);
    }
}

$data = json_decode(file_get_contents($json_path), true);
if (!$data) {
    echo "Error: JSON inválido o vacío.\n";
    exit(1);
}

require_once('/var/www/html/wp-load.php');

$title = $data['title'];
$slug = $data['slug'];
$excerpt = $data['excerpt'] ?? '';
$series = $data['series'] ?? ''; // slug o id de la serie
$position = $data['position'] ?? 1;
$tags = $data['tags'] ?? [];
$chapters = $data['chapters'] ?? []; // slugs o nombres de capítulos
$sections = $data['sections'] ?? []; // array de ['type' => 'heading'|'paragraph'|'list'|'table', 'content' => ...]
$references = $data['references'] ?? []; // array de ['concept' => '...', 'ref' => '...']
$sources = $data['sources'] ?? []; // array de ['title' => '...', 'url' => '...', 'desc' => '...']

$parts = [];

// Construir secciones
foreach ($sections as $sec) {
    switch ($sec['type']) {
        case 'heading':
            $parts[] = '<!-- wp:heading --><h2 class="wp-block-heading">' . esc_html($sec['content'] ?? $sec['title'] ?? '') . '</h2><!-- /wp:heading -->';
            break;
        case 'paragraph':
            $parts[] = '<!-- wp:paragraph --><p>' . $sec['content'] . '</p><!-- /wp:paragraph -->';
            break;
        case 'list':
            $items_html = '';
            foreach ($sec['items'] as $item) {
                $items_html .= '<li>' . $item . '</li>';
            }
            $parts[] = '<!-- wp:list --><ul class="wp-block-list">' . $items_html . '</ul><!-- /wp:list -->';
            break;
    }
}

// Agregar tabla de referencias si existen
if (!empty($references)) {
    $parts[] = '<!-- wp:heading --><h2 class="wp-block-heading">Referencias de las Escrituras</h2><!-- /wp:heading -->';
    $table_html = '<!-- wp:table --><figure class="wp-block-table"><table class="bc-forma-t"><thead><tr><th>Concepto</th><th>Referencia</th></tr></thead><tbody>';
    foreach ($references as $ref) {
        $table_html .= '<tr><td>' . $ref['concept'] . '</td><td>' . $ref['ref'] . '</td></tr>';
    }
    $table_html .= '</tbody></table></figure><!-- /wp:table -->';
    $parts[] = $table_html;
}

// Agregar fuentes consultadas si existen
if (!empty($sources)) {
    $parts[] = '<!-- wp:heading --><h2 class="wp-block-heading">Fuentes consultadas</h2><!-- /wp:heading -->';
    $sources_html = '<!-- wp:list --><ul class="wp-block-list">';
    foreach ($sources as $src) {
        $sources_html .= '<li><a href="' . $src['url'] . '" target="_blank" rel="noopener noreferrer">' . $src['title'] . ' <i class="fas fa-external-link-alt" aria-hidden="true"></i></a> — ' . $src['desc'] . '</li>';
    }
    $sources_html .= '</ul><!-- /wp:list -->';
    $parts[] = $sources_html;
}

$post_content = implode("\n", $parts);

// Verificar si el post ya existe por slug
$existing = get_page_by_path($slug, OBJECT, 'post');
$post_data = array(
    'post_title'   => $title,
    'post_name'    => $slug,
    'post_content' => $post_content,
    'post_status'  => 'publish',
    'post_author'  => 1,
    'post_excerpt' => $excerpt,
);

if ($existing) {
    $post_data['ID'] = $existing->ID;
    $post_id = wp_update_post($post_data);
    echo "Post actualizado (ID: $post_id)\n";
} else {
    $post_id = wp_insert_post($post_data);
    echo "Post creado (ID: $post_id)\n";
}

if (!$post_id || is_wp_error($post_id)) {
    echo "Error al guardar el post.\n";
    exit(1);
}

// Asignar tags (crear si no existen)
if (!empty($tags)) {
    wp_set_object_terms($post_id, $tags, 'post_tag');
}

// Asignar capítulos
if (!empty($chapters)) {
    wp_set_object_terms($post_id, $chapters, 'bc_chapter');
}

// Asignar serie (collection)
if (!empty($series)) {
    wp_set_object_terms($post_id, array($series), 'collection');
}

// Asignar posición en la serie
if (!empty($position)) {
    update_post_meta($post_id, '_series_position', intval($position));
}

echo "Configuración completada exitosamente para el post ID: $post_id\n";
