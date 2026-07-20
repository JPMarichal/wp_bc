<?php

remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', 'rest_output_link_wp_head' );
remove_action( 'wp_head', 'wp_oembed_add_discovery' );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'rel_canonical' );

remove_action( 'wp_head', 'wp_oembed_add_discovery' );
remove_action( 'wp_head', 'wp_oembed_add_host_js' );
remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );

add_filter( 'emoji_svg_url', '__return_false' );

add_action( 'wp_enqueue_scripts', function () {
  wp_dequeue_style( 'font-awesome' );
  wp_dequeue_style( 'generate-font-icons' );
  wp_dequeue_style( 'generate-fonts' );
  wp_dequeue_style( 'generate-google-fonts' );
}, 100 );

add_filter( 'wp_robots', function ( $robots ) {
  $robots['max-image-preview'] = 'large';

  if ( is_date() || is_tag() || is_attachment() || is_paged() ) {
    $robots['noindex'] = true;
  }

  return $robots;
} );

function bc_rel_prev_next() {
  if ( ! is_archive() && ! is_home() && ! is_search() ) {
    return;
  }

  global $wp_query;

  if ( $wp_query->max_num_pages <= 1 ) {
    return;
  }

  $paged = get_query_var( 'paged' ) ?: 1;

  if ( $paged > 1 ) {
    $prev = $paged - 1;
    $link = get_pagenum_link( $prev );
    echo '<link rel="prev" href="' . esc_url( $link ) . '">' . "\n";
  }

  if ( $paged < $wp_query->max_num_pages ) {
    $next = $paged + 1;
    $link = get_pagenum_link( $next );
    echo '<link rel="next" href="' . esc_url( $link ) . '">' . "\n";
  }
}
add_action( 'wp_head', 'bc_rel_prev_next' );

remove_action( 'wp_head', 'wp_site_icon', 99 );
add_action( 'wp_head', 'bc_site_icon', 5 );
function bc_site_icon() {
  $url = get_stylesheet_directory_uri() . '/assets/favicon';
  $site_name = esc_attr( get_bloginfo( 'name' ) );
  ?>
<link rel="icon" href="<?php echo $url; ?>/favicon.ico" sizes="any">
<link rel="icon" href="<?php echo $url; ?>/favicon.svg" type="image/svg+xml">
<link rel="icon" href="<?php echo $url; ?>/favicon-16x16.png" sizes="16x16" type="image/png">
<link rel="icon" href="<?php echo $url; ?>/favicon-32x32.png" sizes="32x32" type="image/png">
<link rel="apple-touch-icon" href="<?php echo $url; ?>/apple-touch-icon.png">
<link rel="manifest" href="<?php echo $url; ?>/site.webmanifest">
<meta name="msapplication-TileImage" content="<?php echo $url; ?>/android-chrome-192x192.png">
<meta name="msapplication-TileColor" content="#1a1a2e">
<meta name="theme-color" content="#1a1a2e">
  <?php
}
