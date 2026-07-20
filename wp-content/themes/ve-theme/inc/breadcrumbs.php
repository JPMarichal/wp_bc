<?php

function bc_render_breadcrumbs( $args = [] ) {
  if ( ! is_singular( 'post' ) && ! is_singular( 'bc_location' ) && ! is_singular( 'bc_quote_author' ) ) {
    return;
  }

  $post = get_queried_object();
  $post_type = get_post_type( $post );

  $classes = isset( $args['class'] ) ? $args['class'] : 'bc-breadcrumbs-wrap';

  echo '<div class="' . esc_attr( $classes ) . '">';
  echo '<nav class="bc-breadcrumbs" aria-label="Breadcrumb">';
  echo '<a href="' . esc_url( home_url( '/' ) ) . '"><i class="fas fa-home"></i> ' . esc_html( get_bloginfo( 'name' ) ) . '</a>';

  if ( 'bc_location' === $post_type ) {
    echo '<span class="bc-breadcrumbs-sep"><i class="fas fa-chevron-right"></i></span>';
    echo '<a href="' . esc_url( get_post_type_archive_link( 'bc_location' ) ) . '">Glosario de ubicaciones</a>';
  } elseif ( 'bc_quote_author' === $post_type ) {
    echo '<span class="bc-breadcrumbs-sep"><i class="fas fa-chevron-right"></i></span>';
    echo '<a href="' . esc_url( get_post_type_archive_link( 'bc_quote_author' ) ) . '">Glosario de personas</a>';
  } elseif ( 'post' === $post_type ) {
    $collection_terms = wp_get_post_terms( $post->ID, 'collection' );
    if ( ! empty( $collection_terms ) && ! is_wp_error( $collection_terms ) ) {
      $series = null;
      foreach ( $collection_terms as $t ) {
        if ( $t->parent > 0 ) {
          $series = $t;
          break;
        }
      }
      if ( $series ) {
        $collection = get_term( $series->parent );
        if ( $collection && ! is_wp_error( $collection ) ) {
          echo '<span class="bc-breadcrumbs-sep"><i class="fas fa-chevron-right"></i></span>';
          echo '<a href="' . esc_url( get_term_link( $collection ) ) . '">' . esc_html( $collection->name ) . '</a>';
          echo '<span class="bc-breadcrumbs-sep"><i class="fas fa-chevron-right"></i></span>';
          echo '<a href="' . esc_url( get_term_link( $series ) ) . '">' . esc_html( $series->name ) . '</a>';
          echo '<span class="bc-breadcrumbs-sep"><i class="fas fa-chevron-right"></i></span>';
          echo '<span class="bc-breadcrumbs-current">' . esc_html( get_the_title( $post ) ) . '</span>';
          echo '</nav>';
          echo '</div>';
          return;
        }
      }
    }

    $cats = get_the_category( $post );
    if ( ! empty( $cats ) ) {
      $term = $cats[0];
      echo '<span class="bc-breadcrumbs-sep"><i class="fas fa-chevron-right"></i></span>';
      echo '<a href="' . esc_url( get_category_link( $term ) ) . '">' . esc_html( $term->name ) . '</a>';
    }
  }

  echo '<span class="bc-breadcrumbs-sep"><i class="fas fa-chevron-right"></i></span>';
  echo '<span class="bc-breadcrumbs-current">' . esc_html( get_the_title( $post ) ) . '</span>';
  echo '</nav>';
  echo '</div>';
}
add_action( 'generate_before_content', 'bc_render_breadcrumbs', 5 );
