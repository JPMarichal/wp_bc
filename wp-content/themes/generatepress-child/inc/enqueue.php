<?php

function bc_compiled_css_handle() {
  return 'generatepress-child';
}

function bc_enqueue_compiled_styles() {
  $compiled_uri  = get_stylesheet_directory_uri() . '/style-compiled.css';
  $compiled_path = get_stylesheet_directory() . '/style-compiled.css';
  $version = file_exists($compiled_path) ? md5_file($compiled_path) : '1.0';

  wp_enqueue_style(
    bc_compiled_css_handle(),
    $compiled_uri,
    ['generate-style'],
    $version
  );

  wp_enqueue_style(
    'bc-fonts',
    get_stylesheet_directory_uri() . '/fonts/bc-fonts.css',
    [],
    '1.0'
  );
}
add_action('wp_enqueue_scripts', 'bc_enqueue_compiled_styles', 20);

add_filter('style_loader_tag', function ($html, $handle) {
  if (bc_compiled_css_handle() === $handle) {
    $html = str_replace(
      "media='all'",
      "media='print' onload=\"this.media='all'\"",
      $html
    );
  }
  return $html;
}, 10, 2);

function bc_enqueue_global_assets() {
  wp_enqueue_style(
    'fontawesome',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css',
    [],
    '6.7.2'
  );

  wp_enqueue_style(
    'bootstrap',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
    [],
    '5.3.3'
  );

  wp_enqueue_script(
    'bootstrap-bundle',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
    [],
    '5.3.3',
    true
  );
}
add_action('wp_enqueue_scripts', 'bc_enqueue_global_assets');

add_filter('style_loader_tag', function ($html, $handle) {
  if (in_array($handle, ['fontawesome', 'bootstrap'], true)) {
    $html = str_replace(
      "media='all'",
      "media='print' onload=\"this.media='all'\"",
      $html
    );
  }
  return $html;
}, 11, 2);

function bc_inline_critical_css() {
  if ( ! is_singular() ) {
    return;
  }
  ?>
<style id="bc-critical-css">
body {
  margin: 0;
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
}
.grid-container {
  max-width: 100%;
  padding-left: 20px;
  padding-right: 20px;
}
@media (min-width: 768px) {
  .grid-container {
    padding-left: 30px;
    padding-right: 30px;
  }
}
@media (min-width: 1024px) {
  .grid-container {
    padding-left: 40px;
    padding-right: 40px;
  }
}
.bc-breadcrumbs-wrap {
  background: #f5f3ef;
  border: 1px solid #e8e4db;
  border-bottom: none;
  padding-left: 20px;
}
.bc-breadcrumbs-wrap .bc-breadcrumbs {
  padding: 6px 0;
}
.bc-breadcrumbs-wrap .bc-breadcrumbs i {
  font-size: 0.75em;
}
.bc-breadcrumbs-wrap a {
  color: #8a7a6b;
  text-decoration: none;
}
.bc-breadcrumbs-wrap a:hover {
  color: #4a3728;
  text-decoration: underline;
}
.bc-breadcrumbs-sep {
  margin: 0 4px;
  color: #c4b8a8;
}
.bc-breadcrumbs-current {
  color: #4a3728;
}
@media (min-width: 768px) {
  .bc-breadcrumbs-wrap {
    padding-left: 30px;
  }
}
@media (min-width: 1024px) {
  .bc-breadcrumbs-wrap {
    padding-left: 40px;
  }
}
.page-share-bar {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 10px;
  background: #f5f3ef;
  border: 1px solid #ddd;
  padding: 6px 20px;
  margin-bottom: 2px;
}
.page-hero {
  position: relative;
  max-height: 500px;
  overflow: hidden;
  margin-bottom: 2px;
}
.page-hero .page-hero-image {
  width: 100%;
  height: 500px;
  object-fit: cover;
  display: block;
}
.page-hero .page-hero-content {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  z-index: 1;
  display: flex;
  flex-direction: column;
  gap: 4px;
  pointer-events: none;
}
.page-hero .page-hero-title-bar {
  width: 100%;
  background: rgba(0,0,0,0.7);
  padding: 6px 20px;
}
.page-hero .page-hero-meta-bar {
  width: 100%;
  background: rgba(0,0,0,0.35);
  padding: 4px 20px;
  pointer-events: auto;
}
.page-hero .entry-title {
  font-family: 'Merriweather', Georgia, 'Times New Roman', serif;
  color: #fff;
  font-size: 2.5em;
  margin: 0;
  line-height: 1.2;
  font-weight: 700;
}
@media (max-width: 767px) {
  .page-hero {
    max-height: 300px;
  }
  .page-hero .page-hero-image {
    height: 300px;
  }
  .page-hero .page-hero-title-bar {
    padding: 4px 20px;
  }
  .page-hero .page-hero-meta-bar {
    padding: 2px 20px;
  }
  .page-hero .entry-title {
    font-size: 1.3em;
  }
}
@media (min-width: 768px) {
  .page-hero .page-hero-title-bar {
    padding-left: 30px;
    padding-right: 30px;
  }
  .page-hero .page-hero-meta-bar {
    padding-left: 30px;
    padding-right: 30px;
  }
  .page-share-bar {
    padding: 6px 30px;
  }
}
@media (min-width: 1024px) {
  .page-hero .page-hero-title-bar {
    padding-left: 40px;
    padding-right: 40px;
  }
  .page-hero .page-hero-meta-bar {
    padding-left: 40px;
    padding-right: 40px;
  }
  .page-share-bar {
    padding: 6px 40px;
  }
}
<?php if ( is_singular( 'bc_quote_author' ) ) : ?>
.bc-glossary-single {
  padding: 2rem 0;
}
.bc-glossary-back-nav {
  margin-bottom: 1rem;
  padding: 0 20px;
}
@media (min-width: 768px) {
  .bc-glossary-back-nav {
    padding: 0 30px;
  }
}
@media (min-width: 1024px) {
  .bc-glossary-back-nav {
    padding: 0 40px;
  }
}
.bc-persona-container {
  display: grid;
  grid-template-columns: 1fr;
  gap: 2rem;
  max-width: 100%;
  padding: 0 20px;
}
@media (min-width: 768px) {
  .bc-persona-container {
    grid-template-columns: 1fr 320px;
    padding: 0 30px;
  }
}
@media (min-width: 1200px) {
  .bc-persona-container {
    padding: 0 40px;
  }
}
.bc-persona-photo .bc-persona-img {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid #fff;
  box-shadow: 0 2px 8px rgba(0,0,0,0.12);
}
.bc-persona-biography {
  margin-top: 1.5rem;
  background: #fff;
  border: 1px solid #e0ddd5;
  border-radius: 8px;
  padding: 2rem;
  color: #333;
  line-height: 1.8;
  font-size: 1rem;
}
.bc-persona-summary {
  border: 1px solid #d4cdc0;
  border-radius: 6px;
  padding: 1rem 1.25rem;
  margin-bottom: 1.5rem;
  background: #faf9f7;
  font-family: 'Lora', Georgia, 'Times New Roman', serif;
  font-style: italic;
  font-weight: 400;
}
.bc-persona-summary p {
  margin: 0;
  font-size: 1.05rem;
  color: #444;
  line-height: 1.7;
}
<?php endif; ?>
</style>
  <?php
}
add_action('wp_head', 'bc_inline_critical_css', 0);
