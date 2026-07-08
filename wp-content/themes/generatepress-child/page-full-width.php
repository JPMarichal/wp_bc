<?php
/**
 * Template Name: Full-width (sin sidebar)
 */

get_header(); ?>

<div <?php generate_do_attr( 'content' ); ?>>
  <main <?php generate_do_attr( 'main' ); ?>>
    <?php
    do_action( 'generate_before_main_content' );

    while ( have_posts() ) :
      the_post();
      generate_do_template_part( 'page' );
    endwhile;

    do_action( 'generate_after_main_content' );
    ?>
  </main>
</div>

<?php
generate_construct_sidebars();
get_footer();
