<?php
if (!defined('ABSPATH')) {
  exit;
}

$term = get_queried_object();
$is_book = $term && $term->parent === 0;

get_header();
?>

<div <?php generate_do_attr('content'); ?>>
  <main <?php generate_do_attr('main'); ?>>
    <?php do_action('generate_before_main_content'); ?>

    <?php if ($is_book) :

      $chapters = get_terms([
        'taxonomy'   => 'bc_chapter',
        'hide_empty' => false,
        'parent'     => $term->term_id,
        'orderby'    => 'name',
        'order'      => 'ASC',
      ]);
      ?>

      <div class="bc-chapter-archive bc-chapter-archive--book">
        <nav class="bc-chapter-breadcrumbs" aria-label="Breadcrumb">
          <a href="<?php echo esc_url(home_url('/')); ?>"><i class="fas fa-home"></i> <?php echo esc_html(get_bloginfo('name')); ?></a>
          <span class="bc-chapter-breadcrumbs-sep"><i class="fas fa-chevron-right"></i></span>
          <span class="bc-chapter-breadcrumbs-current"><?php echo esc_html($term->name); ?></span>
        </nav>

        <h1 class="bc-chapter-archive-title"><?php printf(__('Libro de %s', 'bc'), esc_html($term->name)); ?></h1>

        <?php if (!empty($chapters) && !is_wp_error($chapters)) : ?>
          <div class="bc-chapter-grid">
            <?php foreach ($chapters as $chap) :
              $count = $chap->count;
            ?>
              <a href="<?php echo esc_url(get_term_link($chap)); ?>" class="bc-chapter-card">
                <span class="bc-chapter-card-num"><?php echo esc_html($chap->name); ?></span>
                <span class="bc-chapter-card-count">
                  <?php printf(_n('%d artículo', '%d artículos', $count, 'bc'), $count); ?>
                </span>
              </a>
            <?php endforeach; ?>
          </div>
        <?php else : ?>
          <p><?php _e('No hay capítulos registrados para este libro.', 'bc'); ?></p>
        <?php endif; ?>
      </div>

    <?php else :

      $parent_book = $term && $term->parent > 0 ? get_term($term->parent) : null;
      $ref = $term->name;

      $siblings = [];
      $prev = null;
      $next = null;
      if ($parent_book) {
        $siblings = get_terms([
          'taxonomy'   => 'bc_chapter',
          'hide_empty' => false,
          'parent'     => $parent_book->term_id,
          'orderby'    => 'name',
          'order'      => 'ASC',
        ]);
        if (!empty($siblings) && !is_wp_error($siblings)) {
          $current_index = null;
          foreach ($siblings as $i => $s) {
            if ($s->term_id === $term->term_id) {
              $current_index = $i;
              break;
            }
          }
          if ($current_index !== null) {
            if ($current_index > 0) {
              $prev = $siblings[$current_index - 1];
            }
            if ($current_index < count($siblings) - 1) {
              $next = $siblings[$current_index + 1];
            }
          }
        }
      }

      $chapter_items = new WP_Query([
        'tax_query' => [[
          'taxonomy' => 'bc_chapter',
          'field'    => 'term_id',
          'terms'    => $term->term_id,
        ]],
        'post_type'      => ['post', 'bc_quote_author'],
        'posts_per_page' => -1,
        'no_found_rows'  => true,
      ]);

      $posts_list = [];
      $personas_list = [];
      if ($chapter_items->have_posts()) {
        while ($chapter_items->have_posts()) {
          $chapter_items->the_post();
          $item = [
            'id'      => get_the_ID(),
            'title'   => get_the_title(),
            'excerpt' => has_excerpt() ? wp_trim_words(get_the_excerpt(), 20, '…') : '',
            'thumb'   => has_post_thumbnail() ? get_the_post_thumbnail_url(get_the_ID(), 'bc_quote_photo') : '',
            'url'     => get_permalink(),
          ];
          if (get_post_type() === 'bc_quote_author') {
            $personas_list[] = $item;
          } else {
            $posts_list[] = $item;
          }
        }
        wp_reset_postdata();
      }
      ?>

      <div class="bc-chapter-archive bc-chapter-archive--chapter">
        <nav class="bc-chapter-breadcrumbs" aria-label="Breadcrumb">
          <a href="<?php echo esc_url(home_url('/')); ?>"><i class="fas fa-home"></i> <?php echo esc_html(get_bloginfo('name')); ?></a>
          <?php if ($parent_book) : ?>
            <span class="bc-chapter-breadcrumbs-sep"><i class="fas fa-chevron-right"></i></span>
            <a href="<?php echo esc_url(get_term_link($parent_book)); ?>"><?php echo esc_html($parent_book->name); ?></a>
          <?php endif; ?>
          <span class="bc-chapter-breadcrumbs-sep"><i class="fas fa-chevron-right"></i></span>
          <span class="bc-chapter-breadcrumbs-current"><?php echo esc_html($ref); ?></span>
        </nav>

        <h1 class="bc-chapter-archive-title"><?php echo esc_html($ref); ?></h1>

        <?php if ($prev || $next) : ?>
          <nav class="bc-chapter-nav" aria-label="<?php esc_attr_e('Navegación entre capítulos', 'bc'); ?>">
            <?php if ($prev) : ?>
              <a href="<?php echo esc_url(get_term_link($prev)); ?>" class="bc-chapter-nav-link bc-chapter-nav-link--prev">
                <i class="fas fa-chevron-left"></i>
                <span><?php echo esc_html($prev->name); ?></span>
              </a>
            <?php endif; ?>
            <?php if ($next) : ?>
              <a href="<?php echo esc_url(get_term_link($next)); ?>" class="bc-chapter-nav-link bc-chapter-nav-link--next">
                <span><?php echo esc_html($next->name); ?></span>
                <i class="fas fa-chevron-right"></i>
              </a>
            <?php endif; ?>
          </nav>
        <?php endif; ?>

        <?php if (!empty($posts_list)) : ?>
          <section class="bc-chapter-section">
            <h2 class="bc-chapter-section-title"><?php _e('Artículos', 'bc'); ?></h2>
            <div class="bc-chapter-post-list">
              <?php foreach ($posts_list as $p) : ?>
                <article class="bc-chapter-post-item">
                  <a href="<?php echo esc_url($p['url']); ?>" class="bc-chapter-post-link"><?php echo esc_html($p['title']); ?></a>
                  <?php if ($p['excerpt']) : ?>
                    <p class="bc-chapter-post-excerpt"><?php echo esc_html($p['excerpt']); ?></p>
                  <?php endif; ?>
                </article>
              <?php endforeach; ?>
            </div>
          </section>
        <?php endif; ?>

        <?php if (!empty($personas_list)) : ?>
          <section class="bc-chapter-section">
            <h2 class="bc-chapter-section-title"><?php _e('Personas relacionadas', 'bc'); ?></h2>
            <div class="bc-chapter-persona-list">
              <?php foreach ($personas_list as $p) : ?>
                <article class="bc-chapter-persona-item">
                  <a href="<?php echo esc_url($p['url']); ?>" class="bc-chapter-persona-link">
                    <?php if ($p['thumb']) : ?>
                      <img src="<?php echo esc_url($p['thumb']); ?>" alt="" class="bc-chapter-persona-thumb">
                    <?php endif; ?>
                    <span class="bc-chapter-persona-name"><?php echo esc_html($p['title']); ?></span>
                  </a>
                </article>
              <?php endforeach; ?>
            </div>
          </section>
        <?php endif; ?>

        <?php if (empty($posts_list) && empty($personas_list)) : ?>
          <div class="bc-chapter-empty">
            <p><?php _e('Todavía no hay contenido asociado a este capítulo.', 'bc'); ?></p>
          </div>
        <?php endif; ?>
      </div>

    <?php endif; ?>

    <?php do_action('generate_after_main_content'); ?>
  </main>
</div>

<?php
do_action('generate_after_primary_content_area');
generate_construct_sidebars();
get_footer();
