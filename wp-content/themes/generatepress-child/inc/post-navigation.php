<?php

function bc_post_navigation() {
  if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) {
    return;
  }

  $prev = get_previous_post();
  $next = get_next_post();

  if ( ! $prev && ! $next ) {
    return;
  }
  ?>
  <nav class="bc-post-nav" aria-label="Navegación entre artículos">
    <div class="bc-post-nav-links">
      <div class="bc-post-nav-prev">
        <?php if ( $prev ) : ?>
          <a href="<?php echo esc_url( get_permalink( $prev ) ); ?>" class="bc-post-nav-link">
            <span class="bc-post-nav-direction"><i class="fas fa-arrow-left"></i> Anterior</span>
            <span class="bc-post-nav-title"><?php echo esc_html( get_the_title( $prev ) ); ?></span>
          </a>
        <?php endif; ?>
      </div>
      <div class="bc-post-nav-next">
        <?php if ( $next ) : ?>
          <a href="<?php echo esc_url( get_permalink( $next ) ); ?>" class="bc-post-nav-link">
            <span class="bc-post-nav-direction">Siguiente <i class="fas fa-arrow-right"></i></span>
            <span class="bc-post-nav-title"><?php echo esc_html( get_the_title( $next ) ); ?></span>
          </a>
        <?php endif; ?>
      </div>
    </div>
  </nav>
  <?php
}
add_action( 'generate_after_content', 'bc_post_navigation', 15 );
