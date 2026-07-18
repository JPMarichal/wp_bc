<?php
if (!defined('ABSPATH')) {
  exit;
}

$current_term = get_queried_object();
$is_collection = $current_term && $current_term->parent === 0;

echo "\n<!-- TAXONOMY-COLLECTION TEMPLATE LOADED: term=" . ($current_term ? $current_term->slug . ' parent=' . $current_term->parent : 'null') . " is_collection=" . ($is_collection ? 'true' : 'false') . " -->\n";

get_header();
?>

<div <?php generate_do_attr('content'); ?>>
  <main <?php generate_do_attr('main'); ?>>
    <?php do_action('generate_before_main_content'); ?>

    <?php if (have_posts()) : ?>

      <?php do_action('generate_archive_title'); ?>

      <?php if ($is_collection) :
        $series = get_terms([
          'taxonomy'   => 'collection',
          'hide_empty' => true,
          'parent'     => $current_term->term_id,
          'orderby'    => 'name',
          'order'      => 'ASC',
        ]);

        if (!empty($series) && !is_wp_error($series)) : ?>
          <div class="bc-collection-series">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3">
              <?php foreach ($series as $s) :
                $series_link = get_term_link($s);
                $count = $s->count;
              ?>
                <div class="col">
                  <a href="<?php echo esc_url($series_link); ?>" class="bc-series-card">
                    <div class="bc-series-card-body">
                      <div class="bc-series-card-icon">
                        <i class="fas fa-book-open"></i>
                      </div>
                      <h3 class="bc-series-card-title"><?php echo esc_html($s->name); ?></h3>
                      <p class="bc-series-card-count">
                        <?php printf(_n('%d artículo', '%d artículos', $count, 've-theme'), $count); ?>
                      </p>
                    </div>
                  </a>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>
      <?php endif; ?>

      <?php do_action('generate_before_loop', 'archive'); ?>

      <div class="bc-collection-posts">
        <?php while (have_posts()) : the_post();
          generate_do_template_part('archive');
        endwhile; ?>
      </div>

      <?php do_action('generate_after_loop', 'archive'); ?>

    <?php else :
      generate_do_template_part('none');
    endif; ?>

    <?php do_action('generate_after_main_content'); ?>
  </main>
</div>

<?php
do_action('generate_after_primary_content_area');
generate_construct_sidebars();
get_footer();
