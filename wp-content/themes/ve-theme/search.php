<?php get_header(); ?>

<main id="main" class="bc-search-archive">
  <div class="grid-container">
    <header class="bc-search-header">
      <h1 class="bc-search-title">
        <?php printf( 'Resultados para: %s', get_search_query() ); ?>
      </h1>
      <div class="bc-search-form-wrap">
        <?php get_search_form(); ?>
      </div>
    </header>

    <?php if ( have_posts() ) : ?>
      <div class="bc-search-count">
        <?php
        global $wp_query;
        printf( '%d artículo(s) encontrado(s).', $wp_query->found_posts );
        ?>
      </div>
      <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
        <?php while ( have_posts() ) : the_post(); ?>
          <div class="col">
            <article class="bc-cat-card">
              <a href="<?php the_permalink(); ?>" class="bc-cat-card-link">
                <?php if ( has_post_thumbnail() ) : ?>
                  <div class="bc-cat-card-img-wrap">
                    <?php echo wp_get_attachment_image( get_post_thumbnail_id(), 'medium', false, array( 'class' => 'bc-cat-card-img', 'alt' => the_title_attribute( array( 'echo' => false ) ), 'title' => the_title_attribute( array( 'echo' => false ) ), 'loading' => 'lazy' , 'decoding' => 'async') ); ?>
                  </div>
                <?php endif; ?>
                <div class="bc-cat-card-body">
                  <h2 class="bc-cat-card-title"><?php the_title(); ?></h2>
                  <p class="bc-cat-card-excerpt"><?php echo wp_trim_words( get_the_excerpt() ?: get_the_content(), 20, '…' ); ?></p>
                  <span class="bc-cat-card-date"><?php echo get_the_date( 'j M, Y' ); ?></span>
                </div>
              </a>
            </article>
          </div>
        <?php endwhile; ?>
      </div>

      <nav class="bc-cat-pagination">
        <?php echo paginate_links(); ?>
      </nav>

    <?php else : ?>
      <div class="bc-search-no-results">
        <p>No se encontraron artículos que coincidan con tu búsqueda.</p>
        <p>Intenta con otras palabras clave.</p>
      </div>
    <?php endif; ?>
  </div>
</main>

<?php get_footer(); ?>
