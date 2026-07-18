<?php

function bc_author_box() {
  if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) {
    return;
  }

  $author_id   = get_the_author_meta( 'ID' );
  $author_name = get_the_author();
  $author_desc = get_the_author_meta( 'description' );

  if ( empty( $author_desc ) ) {
    return;
  }
  ?>
  <div class="bc-author-box">
    <div class="bc-author-box-avatar">
      <?php echo get_avatar( $author_id, 96, '', $author_name, array( 'class' => 'bc-author-box-img' ) ); ?>
    </div>
    <div class="bc-author-box-body">
      <h3 class="bc-author-box-name">
        <a href="<?php echo esc_url( get_author_posts_url( $author_id ) ); ?>"><?php echo esc_html( $author_name ); ?></a>
      </h3>
      <p class="bc-author-box-desc"><?php echo esc_html( $author_desc ); ?></p>
    </div>
  </div>
  <?php
}
add_action( 'generate_after_content', 'bc_author_box', 20 );
