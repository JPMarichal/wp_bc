<?php

add_filter('manage_posts_columns', function ($columns) {
  $new = [];
  foreach ($columns as $key => $label) {
    if ('cb' === $key) {
      $new[$key] = $label;
      $new['bc_thumb'] = '';
    } elseif ('title' === $key) {
      $new[$key] = $label;
    } else {
      $new[$key] = $label;
    }
  }
  return $new;
}, 5);

add_action('manage_posts_custom_column', function ($column, $post_id) {
  if ('bc_thumb' === $column && has_post_thumbnail($post_id)) {
    echo wp_get_attachment_image(get_post_thumbnail_id($post_id), 'bc-tiny', false, ['style' => 'width:36px;height:36px;border-radius:3px;object-fit:cover;vertical-align:middle']);
  }
}, 10, 2);

add_filter('manage_edit-post_sortable_columns', function ($columns) {
  $columns['bc_thumb'] = 'bc_thumb';
  return $columns;
});

add_action('admin_head', function () {
  $screen = get_current_screen();
  if (!$screen || 'edit-post' !== $screen->id) {
    return;
  }
  ?>
  <style>
    .column-bc_thumb { width: 42px; }
    .column-bc_thumb::before { content: "\f128"; font: 400 16px dashicons; line-height: 1; vertical-align: middle; }
  </style>
  <?php
});
