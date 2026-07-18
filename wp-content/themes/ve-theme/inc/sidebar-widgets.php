<?php

add_action('widgets_init', function () {
  unregister_sidebar('sidebar-1');
  register_sidebar([
    'name'          => __('Right Sidebar', 'generatepress'),
    'id'            => 'sidebar-1',
    'description'   => __('Appears on posts and pages except the front page.', 'generatepress'),
    'before_widget' => '<section id="%1$s" class="bc-widget %2$s">',
    'after_widget'  => '</section>',
    'before_title'  => '<h3 class="bc-widget-title">',
    'after_title'   => '</h3>',
  ]);
}, 11);

function bc_widget_wrap_start($title, $icon, $class = '') {
  echo '<section class="bc-widget' . ($class ? ' ' . $class : '') . '">';
  echo '<h3 class="bc-widget-title"><i class="fas fa-' . $icon . '"></i> ' . esc_html($title) . '</h3>';
  echo '<div class="bc-widget-content">';
}

function bc_widget_wrap_end() {
  echo '</div></section>';
}

function bc_widget_search() {
  bc_widget_wrap_start(__('Buscar', 'bc'), 'search');
  get_search_form();
  bc_widget_wrap_end();
}

function bc_widget_series() {
  if (!class_exists('BCCO_Frontend_Widget')) return;

  global $post;
  $terms = wp_get_post_terms($post->ID, 'collection');
  if (empty($terms) || is_wp_error($terms)) return;

  $series = $terms[0];
  if (!$series || $series->parent <= 0) return;

  $instance = ['title' => ''];
  $args = [
    'before_widget' => '<div class="bc-widget-content">',
    'after_widget'  => '</div>',
    'before_title'  => '<h3 class="bc-widget-title">',
    'after_title'   => '</h3>',
  ];
  echo '<section class="bc-widget bc-widget--series">';
  the_widget('BCCO_Frontend_Widget', $instance, $args);
  echo '</section>';
}

function bc_widget_recent_posts() {
  $recent = wp_get_recent_posts([
    'numberposts' => 5,
    'post_status' => 'publish',
  ]);
  if (empty($recent)) return;

  bc_widget_wrap_start(__('Artículos recientes', 'bc'), 'clock');
  echo '<ul class="bc-widget-rp">';
  foreach ($recent as $r) {
    $thumb = has_post_thumbnail($r['ID'])
      ? get_the_post_thumbnail($r['ID'], 'bc-sidebar-thumb', ['class' => 'bc-widget-rp-thumb', 'loading' => 'lazy'])
      : '';
    echo '<li class="bc-widget-rp-item">';
    echo '<a href="' . esc_url(get_permalink($r['ID'])) . '" class="bc-widget-rp-link">';
    if ($thumb) {
      echo $thumb;
    }
    echo '<span class="bc-widget-rp-text">';
    echo '<span class="bc-widget-rp-title">' . esc_html($r['post_title']) . '</span>';
    echo '</span>';
    echo '</a></li>';
  }
  echo '</ul>';
  echo '<a href="' . esc_url(home_url('/todos-los-articulos/')) . '" class="bc-widget-view-all">Ver todos los artículos →</a>';
  bc_widget_wrap_end();
}

function bc_widget_recent_comments() {
  $comments = get_comments([
    'number' => 5,
    'status' => 'approve',
    'post_status' => 'publish',
  ]);
  if (empty($comments)) return;

  bc_widget_wrap_start(__('Comentarios recientes', 'bc'), 'comment');
  echo '<ul class="bc-widget-list bc-widget-list--comments">';
  foreach ($comments as $c) {
    $title = wp_trim_words($c->comment_content, 8, '…');
    echo '<li><a href="' . esc_url(get_comment_link($c)) . '">' . esc_html($title) . '</a></li>';
  }
  echo '</ul>';
  bc_widget_wrap_end();
}

function bc_widget_tag_cloud() {
  global $post;

  if (is_singular('post') && is_a($post, 'WP_Post')) {
    $tags = wp_get_post_tags($post->ID);
    $title = __('Temas de este artículo', 'bc');
  } else {
    $tags = get_tags([
      'number' => 20,
      'orderby' => 'count',
      'order' => 'DESC',
    ]);
    $title = __('Temas', 'bc');
  }
  if (empty($tags)) return;

  bc_widget_wrap_start($title, 'tags');
  echo '<div class="bc-widget-tags">';
  foreach ($tags as $t) {
    echo '<a href="' . esc_url(get_tag_link($t)) . '" class="bc-widget-tag">' . esc_html($t->name) . '</a>';
  }
  echo '</div>';
  echo '<a href="' . esc_url(home_url('/glosario/temas/')) . '" class="bc-widget-view-all">Ver todos los temas →</a>';
  bc_widget_wrap_end();
}
