<?php

add_filter( 'body_class', function ( $classes ) {
  $sidebar = '';

  if ( is_singular() ) {
    $sidebar = get_post_meta( get_the_ID(), '_bc_sidebar_position', true );
  }

  if ( is_page_template( 'page-full-width.php' ) || is_page_template( 'page-todos-los-articulos.php' ) || is_page_template( 'page-landing.php' ) ) {
    $classes[] = 'bc-layout--full';
  } elseif ( is_page_template( 'page-narrow.php' ) ) {
    $classes[] = 'bc-layout--narrow';
  } elseif ( 'none' === $sidebar ) {
    $classes[] = 'bc-layout--no-sidebar';
  } elseif ( 'left' === $sidebar ) {
    $classes[] = 'bc-layout--sidebar-left';
  } else {
    $classes[] = 'bc-layout--sidebar-right';
  }

  return $classes;
} );

add_filter( 'generate_sidebar_layout', function ( $layout ) {
  if ( is_page_template( 'page-todos-los-articulos.php' ) || is_page_template( 'page-landing.php' ) ) {
    return 'no-sidebar';
  }

  if ( is_singular() ) {
    $pos = get_post_meta( get_the_ID(), '_bc_sidebar_position', true );
    if ( 'none' === $pos ) {
      return 'no-sidebar';
    }
  }
  return $layout;
} );
