<?php

function bc_og_tags() {
  if ( ! is_singular() ) {
    return;
  }

  $post      = get_queried_object();
  $title     = esc_attr( get_the_title( $post ) );
  $url       = esc_url( get_permalink( $post ) );
  $site_name = esc_attr( get_bloginfo( 'name' ) );

  $excerpt = get_the_excerpt( $post );
  if ( empty( $excerpt ) ) {
    $excerpt = wp_trim_words( strip_shortcodes( get_the_content( $post ) ), 30, '…' );
  }
  $description = esc_attr( $excerpt );

  $image = '';
  if ( has_post_thumbnail( $post ) ) {
    $image = esc_url( get_the_post_thumbnail_url( $post, 'full' ) );
  }
  ?>
<meta property="og:title" content="<?php echo $title; ?>">
<meta property="og:description" content="<?php echo $description; ?>">
<meta property="og:url" content="<?php echo $url; ?>">
<meta property="og:type" content="article">
<meta property="og:site_name" content="<?php echo $site_name; ?>">
<?php if ( $image ) : ?>
<meta property="og:image" content="<?php echo $image; ?>">
<?php endif; ?>
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo $title; ?>">
<meta name="twitter:description" content="<?php echo $description; ?>">
<?php if ( $image ) : ?>
<meta name="twitter:image" content="<?php echo $image; ?>">
<?php endif; ?>
  <?php
}
add_action( 'wp_head', 'bc_og_tags' );
