<?php

add_action( 'after_setup_theme', function () {
  add_image_size( 'bc-hero', 1600, 0, false );
});

add_action( 'init', function () {
  remove_action( 'generate_credits', 'generate_add_footer_info' );
  add_action( 'generate_credits', function () {
    printf(
      '<span class="copyright">&copy; %s %s</span>',
      date( 'Y' ),
      get_bloginfo( 'name' )
    );
  }, 10 );
});

add_action('wp', function () {
  remove_action('generate_before_content', 'generate_featured_page_header_inside_single', 10);
  remove_filter('generate_inside_post_meta_item_output', 'generate_do_post_meta_prefix', 10);
}, 11);

add_filter('generate_header_entry_meta_items', function ($items) {
  return ['date', 'author', 'categories', 'tags', 'comments-link'];
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
