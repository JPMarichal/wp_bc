<?php

add_action('init', function () {
  if (!function_exists('register_block_pattern')) {
    return;
  }

  register_block_pattern('bc/hero-simple', [
    'title'       => 'BC Hero Simple',
    'description' => 'Hero con título, subtítulo y botón CTA',
    'content'     => '<!-- wp:cover {"overlayColor":"jardin-500","minHeight":60,"minHeightUnit":"vh","align":"full"} -->
<div class="wp-block-cover align-full" style="min-height:60vh"><span aria-hidden="true" class="wp-block-cover__background has-jardin-500-background-color has-background-dim-100 has-background-dim"></span><div class="wp-block-cover__inner-container"><!-- wp:heading {"textAlign":"center","level":1,"style":{"typography":{"fontSize":"2.5rem"}},"textColor":"white"} -->
<h1 class="wp-block-heading has-text-align-center has-white-color has-text-color" style="font-size:2.5rem">Título Principal</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"1.125rem"}},"textColor":"white"} -->
<p class="has-text-align-center has-white-color has-text-color" style="font-size:1.125rem">Subtítulo o descripción breve del contenido de esta página.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"white","textColor":"jardin-500"} -->
<div class="wp-block-button"><a class="wp-block-button__link has-jardin-500-color has-white-background-color has-text-color has-background wp-element-button">Comenzar</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div></div>
<!-- /wp:cover -->',
    'categories'  => ['banners'],
  ]);

  register_block_pattern('bc/sidebar-content', [
    'title'       => 'BC Contenido + Sidebar',
    'description' => 'Dos columnas: contenido 2/3 y sidebar 1/3',
    'content'     => '<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column {"width":"66.66%"} -->
<div class="wp-block-column" style="flex-basis:66.66%"><!-- wp:heading -->
<h2 class="wp-block-heading">Título de la sección</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Contenido principal de la página. Aquí va el texto informativo, los artículos destacados o cualquier contenido que desees mostrar.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Puedes agregar más párrafos, imágenes o cualquier bloque de Gutenberg en esta columna.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"33.33%"} -->
<div class="wp-block-column" style="flex-basis:33.33%"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Sidebar</h3>
<!-- /wp:heading -->

<!-- wp:list -->
<ul><!-- wp:list-item -->
<li>Enlace 1</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Enlace 2</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Enlace 3</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->',
    'categories'  => ['columns'],
  ]);

  register_block_pattern('bc/card-grid', [
    'title'       => 'BC Grid de Tarjetas',
    'description' => 'Tres tarjetas en fila con ícono, título y descripción',
    'content'     => '<!-- wp:group {"align":"wide"} -->
<div class="wp-block-group alignwide"><!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"2.5rem"}}} -->
<p class="has-text-align-center" style="font-size:2.5rem">📖</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"textAlign":"center","level":3} -->
<h3 class="wp-block-heading has-text-align-center">Título Uno</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Descripción breve de la primera tarjeta. Explica el concepto o tema principal.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"2.5rem"}}} -->
<p class="has-text-align-center" style="font-size:2.5rem">🌟</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"textAlign":"center","level":3} -->
<h3 class="wp-block-heading has-text-align-center">Título Dos</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Descripción breve de la segunda tarjeta. Puede ser un tema relacionado.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"2.5rem"}}} -->
<p class="has-text-align-center" style="font-size:2.5rem">🌿</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"textAlign":"center","level":3} -->
<h3 class="wp-block-heading has-text-align-center">Título Tres</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Descripción breve de la tercera tarjeta. Completa el grupo de tres.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->',
    'categories'  => ['columns'],
  ]);

  register_block_pattern('bc/quote-featured', [
    'title'       => 'BC Cita Destacada',
    'description' => 'Cita destacada con autor y fondo de acento',
    'content'     => '<!-- wp:group {"style":{"color":{"background":"#f5f2ec"},"spacing":{"padding":{"top":"2em","bottom":"2em","left":"2em","right":"2em"}}},"borderRadius":"6px"} -->
<div class="wp-block-group has-background" style="background-color:#f5f2ec;padding-top:2em;padding-right:2em;padding-bottom:2em;padding-left:2em;border-radius:6px"><!-- wp:quote -->
<blockquote class="wp-block-quote"><!-- wp:paragraph -->
<p>"Esta es una cita destacada que resume una idea importante o una enseñanza clave del artículo."</p>
<!-- /wp:paragraph --><cite>— Nombre del Autor</cite></blockquote>
<!-- /wp:quote --></div>
<!-- /wp:group -->',
    'categories'  => ['text'],
  ]);

  register_block_pattern('bc/faq-group', [
    'title'       => 'BC Preguntas Frecuentes',
    'description' => 'Grupo de preguntas y respuestas apilables',
    'content'     => '<!-- wp:group {"align":"wide"} -->
<div class="wp-block-group alignwide"><!-- wp:heading -->
<h2 class="wp-block-heading">Preguntas Frecuentes</h2>
<!-- /wp:heading -->

<!-- wp:details -->
<details class="wp-block-details"><summary>¿Cuál es la primera pregunta?</summary><!-- wp:paragraph -->
<p>Respuesta a la primera pregunta. Explica con claridad y proporciona contexto adicional si es necesario.</p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->

<!-- wp:details -->
<details class="wp-block-details"><summary>¿Cuál es la segunda pregunta?</summary><!-- wp:paragraph -->
<p>Respuesta a la segunda pregunta. Mantén un tono consistente con el resto del contenido.</p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->

<!-- wp:details -->
<details class="wp-block-details"><summary>¿Cuál es la tercera pregunta?</summary><!-- wp:paragraph -->
<p>Respuesta a la tercera pregunta. Puedes incluir referencias o enlaces a artículos relacionados.</p>
<!-- /wp:paragraph --></details>
<!-- /wp:details --></div>
<!-- /wp:group -->',
    'categories'  => ['text'],
  ]);

  register_block_pattern('bc/table-of-contents', [
    'title'       => 'BC Tabla de Contenidos',
    'description' => 'TOC visual con enlaces a secciones del artículo',
    'content'     => '<!-- wp:group {"style":{"color":{"background":"#faf8f5"},"spacing":{"padding":{"top":"1.5em","bottom":"1.5em","left":"1.5em","right":"1.5em"}}},"borderRadius":"6px"} -->
<div class="wp-block-group has-background" style="background-color:#faf8f5;padding-top:1.5em;padding-right:1.5em;padding-bottom:1.5em;padding-left:1.5em;border-radius:6px"><!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Contenido</h2>
<!-- /wp:heading -->

<!-- wp:list {"ordered":true} -->
<ol><!-- wp:list-item -->
<li><a href="#seccion-1">Introducción</a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="#seccion-2">Desarrollo del tema</a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="#seccion-3">Análisis y reflexión</a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="#seccion-4">Conclusión</a></li>
<!-- /wp:list-item --></ol>
<!-- /wp:list --></div>
<!-- /wp:group -->',
    'categories'  => ['text'],
  ]);
});
