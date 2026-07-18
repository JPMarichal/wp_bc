<?php get_header(); ?>

<main id="main" class="bc-error-404">
  <div class="grid-container">
    <div class="bc-error-404-content">
      <span class="bc-error-404-code">404</span>
      <h1 class="bc-error-404-title">Página no encontrada</h1>
      <p class="bc-error-404-desc">La página que buscas no existe o ha sido movida.</p>
      <div class="bc-error-404-actions">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="bc-error-404-btn">
          <i class="fas fa-home"></i> Ir al inicio
        </a>
        <a href="<?php echo esc_url( home_url( '/todos-los-articulos/' ) ); ?>" class="bc-error-404-btn bc-error-404-btn--secondary">
          Ver todos los artículos
        </a>
      </div>
      <div class="bc-error-404-search">
        <?php get_search_form(); ?>
      </div>
    </div>
  </div>
</main>

<?php get_footer(); ?>
