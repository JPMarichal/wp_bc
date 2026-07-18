<footer class="bc-footer">
  <div class="grid-container">
    <?php if ( is_active_sidebar( 'footer-col-1' ) || is_active_sidebar( 'footer-col-2' ) || is_active_sidebar( 'footer-col-3' ) ) : ?>
      <div class="row g-4 bc-footer-widgets">
        <div class="col-md-4"><?php dynamic_sidebar( 'footer-col-1' ); ?></div>
        <div class="col-md-4"><?php dynamic_sidebar( 'footer-col-2' ); ?></div>
        <div class="col-md-4"><?php dynamic_sidebar( 'footer-col-3' ); ?></div>
      </div>
    <?php endif; ?>
    <div class="bc-footer-bottom">
      <span class="bc-footer-copyright">
        &copy; <?php echo date( 'Y' ); ?> Juan Pablo Marichal Catalán
      </span>
      <p class="bc-footer-disclaimer">
        Este sitio no es un sitio oficial de La Iglesia de Jesucristo de los Santos de los Últimos Días.
        Se ha hecho todo esfuerzo para conformar su contenido a la doctrina y prácticas de la Iglesia.
      </p>
    </div>
  </div>
</footer>
