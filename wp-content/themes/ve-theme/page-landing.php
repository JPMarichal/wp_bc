<?php
/**
 * Template Name: Landing page
 */

get_header(); ?>

<div <?php generate_do_attr( 'content' ); ?>>
  <main <?php generate_do_attr( 'main' ); ?>>
    <?php
    do_action( 'generate_before_main_content' );

    while ( have_posts() ) :
      the_post();
      ?>
      <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <div class="inside-article">
          <?php if ( has_post_thumbnail() ) : ?>
            <div class="bc-landing-hero">
              <?php echo wp_get_attachment_image( get_post_thumbnail_id(), 'full', false, array( 'class' => 'bc-landing-hero-img', 'fetchpriority' => 'high' , 'decoding' => 'async') ); ?>
              <div class="bc-landing-hero-overlay">
                <div class="grid-container">
                  <h1 class="bc-landing-hero-title"><?php the_title(); ?></h1>
                  <?php if ( has_excerpt() ) : ?>
                    <p class="bc-landing-hero-subtitle"><?php echo get_the_excerpt(); ?></p>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php else : ?>
            <header class="entry-header">
              <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
            </header>
          <?php endif; ?>

          <div class="entry-content">
            <?php the_content(); ?>
          </div>
        </div>
      </article>
      <?php
    endwhile;

    do_action( 'generate_after_main_content' );
    ?>
  </main>
</div>

<?php
generate_construct_sidebars();
get_footer();
