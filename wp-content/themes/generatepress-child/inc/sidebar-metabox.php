<?php

add_action( 'add_meta_boxes', function () {
  add_meta_box(
    'bc_sidebar_position',
    'Posición del sidebar',
    'bc_sidebar_metabox_html',
    [ 'post', 'page' ],
    'side'
  );
} );

function bc_sidebar_metabox_html( $post ) {
  $value = get_post_meta( $post->ID, '_bc_sidebar_position', true ) ?: 'right';
  wp_nonce_field( 'bc_sidebar_save', 'bc_sidebar_nonce' );
  ?>
  <select name="bc_sidebar_position" style="width:100%">
    <option value="right" <?php selected( $value, 'right' ); ?>>Sidebar derecha</option>
    <option value="left"  <?php selected( $value, 'left' );  ?>>Sidebar izquierda</option>
    <option value="none"  <?php selected( $value, 'none' );  ?>>Sin sidebar</option>
  </select>
  <?php
}

add_action( 'save_post', function ( $post_id ) {
  if ( ! isset( $_POST['bc_sidebar_nonce'] ) ) {
    return;
  }
  if ( ! wp_verify_nonce( $_POST['bc_sidebar_nonce'], 'bc_sidebar_save' ) ) {
    return;
  }
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
    return;
  }
  if ( ! current_user_can( 'edit_post', $post_id ) ) {
    return;
  }
  if ( isset( $_POST['bc_sidebar_position'] ) ) {
    update_post_meta( $post_id, '_bc_sidebar_position', sanitize_key( $_POST['bc_sidebar_position'] ) );
  }
} );
