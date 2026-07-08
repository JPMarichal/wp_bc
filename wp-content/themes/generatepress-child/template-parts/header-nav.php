<div class="bc-header-nav">
  <div class="grid-container">
    <button class="bc-nav-toggle" aria-label="Abrir menú">
      <span></span><span></span><span></span>
    </button>
    <div class="bc-offcanvas-overlay"></div>
    <div class="bc-offcanvas">
      <button class="bc-offcanvas-close" aria-label="Cerrar menú">
        <i class="fas fa-times"></i>
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
</div>
