<?php get_header(); ?>

<main id="main" class="bc-glossary-single">
	<?php while ( have_posts() ) : the_post(); ?>

		<?php bc_render_breadcrumbs( [ 'class' => 'bc-glossary-back-nav' ] ); ?>

		<div class="bc-persona-container">
			<div class="bc-persona-content-area">

				<?php bc_render_share_bar(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class( 'bc-persona-card' ); ?>>
					<div class="bc-persona-layout">
						<?php if ( has_post_thumbnail() ) : ?>
							<div class="bc-persona-photo">
								<?php echo wp_get_attachment_image( get_post_thumbnail_id(), 'bc_quote_photo', false, array(
									'class' => 'bc-persona-img',
									'alt'   => get_the_title(),
								 'decoding' => 'async') ); ?>
							</div>
						<?php endif; ?>

						<div class="bc-persona-info">
							<h1 class="bc-persona-name"><?php the_title(); ?></h1>

							<?php $desc = get_post_meta( get_the_ID(), '_author_description', true ); ?>
							<?php if ( $desc ) : ?>
								<p class="bc-persona-desc"><?php echo esc_html( $desc ); ?></p>
							<?php endif; ?>
						</div>
					</div>
				</article>

				<?php if ( has_excerpt() || get_the_content() ) : ?>
					<section class="bc-persona-biography">
						<?php if ( has_excerpt() ) : ?>
							<div class="bc-persona-summary"><?php echo wp_kses_post( get_the_excerpt() ); ?></div>
						<?php endif; ?>
						<?php if ( get_the_content() ) : ?>
							<div class="bc-persona-biography-body"><?php the_content(); ?></div>
						<?php endif; ?>
					</section>
				<?php endif; ?>

				<?php bc_render_share_bar( 'bottom' ); ?>

			</div>

			<aside class="bc-persona-sidebar">
				<?php if ( ! dynamic_sidebar( 'persona-sidebar' ) ) : ?>
					<?php bc_render_persona_infobox(); ?>
				<?php endif; ?>
			</aside>
		</div>

	<?php if ( comments_open() || get_comments_number() ) : ?>
		<div class="bc-persona-comments">
			<?php comments_template(); ?>
		</div>
	<?php endif; ?>

	<?php endwhile; ?>
</main>

<?php get_footer(); ?>
