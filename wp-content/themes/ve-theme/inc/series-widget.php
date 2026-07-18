<?php

function bc_render_series_widget() {
  if (!is_singular('post')) {
    return;
  }

  if (!class_exists('BCCO_Frontend_Widget')) {
    return;
  }

  global $post;
  $terms = wp_get_post_terms($post->ID, 'collection');
  if (empty($terms) || is_wp_error($terms)) {
    return;
  }

  $series = $terms[0];
  if (!$series || $series->parent <= 0) {
    return;
  }

  $instance = ['title' => ''];
  $args = [
    'before_widget' => '<section class="widget bcco-series-widget inner-padding">',
    'after_widget'  => '</section>',
    'before_title'  => '<h2 class="widget-title">',
    'after_title'   => '</h2>',
  ];

  the_widget('BCCO_Frontend_Widget', $instance, $args);
}
