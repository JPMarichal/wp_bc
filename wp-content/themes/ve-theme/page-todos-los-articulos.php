<?php
/**
 * Template Name: Todos los artículos
 */

get_header(); ?>

<div <?php generate_do_attr('content'); ?>>
  <main <?php generate_do_attr('main'); ?>>
    <?php do_action('generate_before_main_content'); ?>

    <?php
    $paged = get_query_var('paged') ? get_query_var('paged') : 1;
    $query = new WP_Query([
      'post_type'      => 'post',
      'posts_per_page' => 25,
      'paged'          => $paged,
      'orderby'        => 'date',
      'order'          => 'DESC',
    ]);
    ?>

    <h1 class="bc-all-posts-title">Todos los artículos</h1>

    <?php if ($query->have_posts()) : ?>
      <div class="bc-all-posts-list">
        <?php while ($query->have_posts()) : $query->the_post(); ?>
          <article <?php post_class('bc-all-posts-item'); ?>>
            <?php if (has_post_thumbnail()) : ?>
              <a href="<?php the_permalink(); ?>" class="bc-all-posts-item-thumb-link">
                <?php echo wp_get_attachment_image( get_post_thumbnail_id(), 'bc-list-thumb', false, array( 'class' => 'bc-all-posts-item-thumb', 'loading' => 'lazy' , 'decoding' => 'async') ); ?>
              </a>
            <?php endif; ?>
            <div class="bc-all-posts-item-body">
              <h2 class="bc-all-posts-item-title">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
              </h2>
              <div class="bc-all-posts-item-meta">
                <?php
                $cats = get_the_category();
                if (!empty($cats)) {
                  echo '<span class="bc-all-posts-item-cat"><i class="fas fa-folder"></i> ' . esc_html($cats[0]->name) . '</span>';
                }
                $tags = get_the_tags();
                if (!empty($tags)) {
                  $tag_links = array_map(function ($t) {
                    return '<a href="' . esc_url(get_tag_link($t)) . '" class="bc-all-posts-item-tag-link">' . esc_html($t->name) . '</a>';
                  }, $tags);
                  echo '<span class="bc-all-posts-item-tags"><i class="fas fa-tags"></i> ' . implode(', ', $tag_links) . '</span>';
                }
                $comments_num = get_comments_number();
                if ($comments_num > 0) {
                  echo '<span class="bc-all-posts-item-comments"><i class="fas fa-comment"></i> ' . esc_html($comments_num) . '</span>';
                }
                ?>
              </div>
              <div class="bc-all-posts-item-excerpt">
                <?php the_excerpt(); ?>
              </div>
            </div>
          </article>
        <?php endwhile; wp_reset_postdata(); ?>
      </div>

      <div class="bc-all-posts-pagination">
        <?php
        echo paginate_links([
          'total'   => $query->max_num_pages,
          'current' => $paged,
          'mid_size' => 2,
          'prev_text' => '&laquo; Anterior',
          'next_text' => 'Siguiente &raquo;',
        ]);
        ?>
      </div>

    <?php else : ?>
      <p class="bc-all-posts-empty">No hay artículos aún.</p>
    <?php endif; ?>

    <?php do_action('generate_after_main_content'); ?>
  </main>
</div>

<?php
generate_construct_sidebars();
get_footer();
