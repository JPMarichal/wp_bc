<?php

get_header(); ?>

<div <?php generate_do_attr( 'content' ); ?>>
  <main <?php generate_do_attr( 'main' ); ?>>
    <?php do_action( 'generate_before_main_content' ); ?>

    <?php
    $term = get_queried_object();
    $description = term_description();
    ?>

    <header class="bc-tax-header">
      <div class="grid-container">
        <h1 class="bc-tax-title"><?php single_term_title(); ?></h1>
        <?php if ( $description ) : ?>
          <div class="bc-tax-desc"><?php echo $description; ?></div>
        <?php endif; ?>
      </div>
    </header>

    <?php if ( have_posts() ) : ?>
      <div class="bc-tax-posts">
        <?php while ( have_posts() ) :
          the_post();
          generate_do_template_part( 'archive' );
        endwhile; ?>
      </div>

      <div class="bc-tax-pagination">
        <?php echo paginate_links(); ?>
      </div>
    <?php else : ?>
      <p class="bc-tax-empty">No hay artículos en esta categoría.</p>
    <?php endif; ?>

    <?php do_action( 'generate_after_main_content' ); ?>
  </main>
</div>

<?php
do_action( 'generate_after_primary_content_area' );
generate_construct_sidebars();
get_footer();
