<div class="bc-header-brand">
  <div class="grid-container">
    <div class="bc-header-row">
      <div class="bc-header-logo">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="bc-logo"><?php bloginfo( 'name' ); ?></a>
      </div>
      <div class="bc-header-tools">
        <div class="bc-header-search">
          <button class="bc-search-toggle" aria-label="Buscar">
            <i class="fas fa-search"></i>
          </button>
          <div class="bc-search-form-wrapper">
            <?php get_search_form(); ?>
          </div>
        </div>
        <div class="bc-header-user">
          <?php if ( is_user_logged_in() ) : ?>
            <?php $current_user = wp_get_current_user(); ?>
            <div class="bc-user-dropdown">
              <button class="bc-user-toggle">
                <?php echo get_avatar( $current_user->ID, 32, '', $current_user->display_name, array( 'class' => 'bc-user-avatar' ) ); ?>
                <i class="fas fa-chevron-down"></i>
              </button>
              <ul class="bc-user-submenu">
                <li><a href="<?php echo esc_url( admin_url() ); ?>">Dashboard</a></li>
                <li><a href="<?php echo esc_url( admin_url( 'profile.php' ) ); ?>">Perfil</a></li>
                <li><a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>">Cerrar sesión</a></li>
              </ul>
            </div>
          <?php else : ?>
            <a href="<?php echo esc_url( wp_login_url() ); ?>" class="bc-login-btn">
              <i class="fas fa-user"></i> Iniciar sesión
            </a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
