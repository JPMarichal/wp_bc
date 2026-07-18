<?php

function bc_og_meta_title() {
  if ( is_singular() ) {
    $post = get_queried_object();
    return esc_attr( is_singular( 'bc_quote_author' ) ? bc_persona_biography_title( $post->ID ) : get_the_title( $post ) );
  }
  return esc_attr( wp_get_document_title() );
}

function bc_og_meta_url() {
  if ( is_singular() ) {
    return esc_url( get_permalink( get_queried_object() ) );
  }
  if ( is_search() ) {
    return esc_url( home_url( '/?s=' . get_search_query( false ) ) );
  }
  return esc_url( home_url( add_query_arg( [] ) ) );
}

function bc_og_meta_description() {
  if ( is_singular() ) {
    $post    = get_queried_object();
    $excerpt = get_the_excerpt( $post );
    if ( empty( $excerpt ) ) {
      $excerpt = wp_trim_words( strip_shortcodes( get_the_content( $post ) ), 30, '…' );
    }
    return esc_attr( $excerpt );
  }

  if ( is_category() || is_tag() || is_tax() ) {
    $term = get_queried_object();
    if ( ! empty( $term->description ) ) {
      return esc_attr( wp_trim_words( $term->description, 30, '…' ) );
    }
  }

  if ( is_home() || is_front_page() ) {
    $desc = get_bloginfo( 'description' );
    if ( ! empty( $desc ) ) {
      return esc_attr( $desc );
    }
  }

  if ( is_search() ) {
    return esc_attr( sprintf( __( 'Resultados de búsqueda para: %s' ), get_search_query( false ) ) );
  }

  if ( is_post_type_archive( 'bc_location' ) ) {
    return esc_attr__( 'Glosario de ubicaciones bíblicas del Nuevo Mundo.' );
  }

  if ( is_post_type_archive( 'bc_quote_author' ) ) {
    return esc_attr__( 'Glosario de personas de las Escrituras.' );
  }

  if ( is_404() ) {
    return esc_attr__( 'Página no encontrada.' );
  }

  return esc_attr( wp_trim_words( get_bloginfo( 'description' ), 30, '…' ) );
}

function bc_og_meta_image_data() {
  $image = '';
  $w = '';
  $h = '';
  $alt = '';

  if ( is_singular() && has_post_thumbnail() ) {
    $post    = get_queried_object();
    $image   = esc_url( get_the_post_thumbnail_url( $post, 'bc-hero' ) );
    $src     = wp_get_attachment_image_src( get_post_thumbnail_id( $post ), 'bc-hero' );
    if ( $src ) {
      $w = $src[1];
      $h = $src[2];
    }
    $alt_text = get_post_meta( get_post_thumbnail_id( $post ), '_wp_attachment_image_alt', true );
    if ( $alt_text ) {
      $alt = esc_attr( $alt_text );
    }
  }

  if ( empty( $image ) ) {
    $logo_id = get_theme_mod( 'custom_logo' );
    if ( $logo_id ) {
      $image = esc_url( wp_get_attachment_image_url( $logo_id, 'full' ) );
      $src   = wp_get_attachment_image_src( $logo_id, 'full' );
      if ( $src ) {
        $w = $src[1];
        $h = $src[2];
      }
      $alt = esc_attr( get_bloginfo( 'name' ) );
    } else {
      $icon_id = get_option( 'site_icon' );
      if ( $icon_id ) {
        $image = esc_url( wp_get_attachment_image_url( $icon_id, 'full' ) );
        $src   = wp_get_attachment_image_src( $icon_id, 'full' );
        if ( $src ) {
          $w = $src[1];
          $h = $src[2];
        }
        $alt = esc_attr( get_bloginfo( 'name' ) );
      }
    }
  }

  return [ $image, $w, $h, $alt ];
}

function bc_og_tags() {
  $title       = bc_og_meta_title();
  $url         = bc_og_meta_url();
  $description = bc_og_meta_description();
  $site_name   = esc_attr( get_bloginfo( 'name' ) );

  list( $image, $image_w, $image_h, $image_alt ) = bc_og_meta_image_data();

  $og_type = is_singular( 'post' ) ? 'article' : 'website';

  $twitter_site    = esc_attr( apply_filters( 'bc_twitter_site', '' ) );

  $author_twitter = '';
  if ( is_singular() ) {
    $author_twitter = get_the_author_meta( 'twitter', get_queried_object()->post_author );
  }
  $twitter_creator = esc_attr( apply_filters( 'bc_twitter_creator', $author_twitter ) );
  ?>
<meta name="description" content="<?php echo $description; ?>">
<meta property="og:title" content="<?php echo $title; ?>">
<meta property="og:description" content="<?php echo $description; ?>">
<meta property="og:url" content="<?php echo $url; ?>">
<meta property="og:type" content="<?php echo $og_type; ?>">
<meta property="og:site_name" content="<?php echo $site_name; ?>">
<meta property="og:locale" content="<?php echo esc_attr( get_locale() ); ?>">
<?php if ( is_singular() ) : $post = get_queried_object(); ?>
<meta property="article:published_time" content="<?php echo esc_attr( get_the_date( 'c', $post ) ); ?>">
<meta property="article:modified_time" content="<?php echo esc_attr( get_the_modified_date( 'c', $post ) ); ?>">
<?php
$tags = get_the_tags( $post );
if ( $tags ) {
  foreach ( $tags as $tag ) {
    echo '<meta property="article:tag" content="' . esc_attr( $tag->name ) . '">' . "\n";
  }
}
$cats = get_the_category( $post );
if ( $cats ) {
  echo '<meta property="article:section" content="' . esc_attr( $cats[0]->name ) . '">' . "\n";
}
endif;
?>
<?php if ( $image ) : ?>
<meta property="og:image" content="<?php echo $image; ?>">
<meta property="og:image:width" content="<?php echo $image_w; ?>">
<meta property="og:image:height" content="<?php echo $image_h; ?>">
<?php if ( $image_alt ) : ?>
<meta property="og:image:alt" content="<?php echo $image_alt; ?>">
<?php endif; ?>
<meta name="thumbnail" content="<?php echo $image; ?>">
<?php endif; ?>
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo $title; ?>">
<meta name="twitter:description" content="<?php echo $description; ?>">
<?php if ( $twitter_site ) : ?>
<meta name="twitter:site" content="<?php echo $twitter_site; ?>">
<?php endif; ?>
<?php if ( $twitter_creator ) : ?>
<meta name="twitter:creator" content="<?php echo $twitter_creator; ?>">
<?php endif; ?>
<?php if ( $image ) : ?>
<meta name="twitter:image" content="<?php echo $image; ?>">
<?php endif; ?>
  <?php
}
add_action( 'wp_head', 'bc_og_tags', 1 );

function bc_user_twitter_field( $user ) {
  $value = esc_attr( get_user_meta( $user->ID, 'twitter', true ) );
  ?>
  <h3><?php _e( 'Redes Sociales' ); ?></h3>
  <table class="form-table">
    <tr>
      <th><label for="twitter"><?php _e( 'Twitter / X Username' ); ?></label></th>
      <td>
        <input type="text" name="twitter" id="twitter" value="<?php echo $value; ?>" class="regular-text" placeholder="@usuario">
        <p class="description"><?php _e( 'Sin incluir la URL, solo el @username.' ); ?></p>
      </td>
    </tr>
  </table>
  <?php
}
add_action( 'show_user_profile', 'bc_user_twitter_field' );
add_action( 'edit_user_profile', 'bc_user_twitter_field' );

function bc_save_user_twitter_field( $user_id ) {
  if ( ! current_user_can( 'edit_user', $user_id ) ) {
    return;
  }
  update_user_meta( $user_id, 'twitter', sanitize_text_field( $_POST['twitter'] ?? '' ) );
}
add_action( 'personal_options_update', 'bc_save_user_twitter_field' );
add_action( 'edit_user_profile_update', 'bc_save_user_twitter_field' );
