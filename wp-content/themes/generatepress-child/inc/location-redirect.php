<?php

add_action('template_redirect', function () {
  if (!is_singular('bc_location')) {
    return;
  }

  $alias_of = get_post_meta(get_the_ID(), '_bc_loc_alias_of', true);
  if (!$alias_of) {
    return;
  }

  $canonical = get_post((int) $alias_of);
  if (!$canonical || $canonical->post_status !== 'publish') {
    return;
  }

  wp_redirect(get_permalink($canonical), 301);
  exit;
});
