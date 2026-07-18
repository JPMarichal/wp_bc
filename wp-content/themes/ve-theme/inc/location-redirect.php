<?php

add_action('template_redirect', function () {
  if (!is_singular('bc_location')) {
    return;
  }

  $post_id = get_the_ID();
  $alias_of = get_post_meta($post_id, '_bc_loc_alias_of', true);
  if (!$alias_of) {
    return;
  }

  $content = get_post_field('post_content', $post_id);
  if (!empty($content)) {
    return;
  }

  $canonical = get_post((int) $alias_of);
  if (!$canonical || $canonical->post_status !== 'publish') {
    return;
  }

  wp_redirect(get_permalink($canonical), 301);
  exit;
});
