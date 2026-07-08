<?php

add_action( 'after_setup_theme', function () {
  add_image_size( 'bc-hero', 1600, 0, false );
  add_image_size( 'bc-featured', 1200, 630, true );
  add_image_size( 'bc-card', 600, 400, true );
  add_image_size( 'bc-card-vertical', 400, 500, true );
  add_image_size( 'bc-list-thumb', 120, 120, true );
  add_image_size( 'bc-sidebar-thumb', 80, 80, true );
  add_image_size( 'bc-tiny', 40, 40, true );
});

add_action('wp', function () {
  remove_action('generate_before_content', 'generate_featured_page_header_inside_single', 10);
  remove_filter('generate_inside_post_meta_item_output', 'generate_do_post_meta_prefix', 10);
}, 11);

add_filter('generate_header_entry_meta_items', function ($items) {
  return ['date', 'author', 'categories', 'comments-link'];
});

add_filter('generate_footer_entry_meta_items', function ($items) {
  return array_diff($items, ['categories', 'tags', 'comments-link']);
});

add_filter('generate_inside_post_meta_item_output', function ($output, $item) {
  $icons = [
    'author'        => '<i class="fas fa-user"></i> ',
    'date'          => '<i class="fas fa-calendar-alt"></i> ',
    'categories'    => '<i class="fas fa-folder"></i> ',
    'tags'          => '<i class="fas fa-tags"></i> ',
    'comments-link' => '<i class="fas fa-comment"></i> ',
  ];
  return $icons[$item] ?? $output;
}, 10, 2);
