<?php

add_filter('body_class', function ($classes) {
  if (!is_category() && !is_tag() && !is_tax()) {
    return $classes;
  }

  $layout = isset($_GET['layout']) && 'list' === $_GET['layout'] ? 'list' : 'grid';
  $classes[] = 'bc-archive-layout--' . $layout;

  return $classes;
});
