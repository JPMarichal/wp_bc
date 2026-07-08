<div class="bc-header-nav">
  <div class="grid-container">
    <button class="bc-nav-toggle" aria-label="Menú">
      <span></span><span></span><span></span>
    </button>
    <?php
    wp_nav_menu( array(
      'theme_location' => 'primary',
      'menu_class'     => 'bc-nav-menu',
      'container'      => false,
      'fallback_cb'    => false,
    ) );
    ?>
  </div>
</div>
