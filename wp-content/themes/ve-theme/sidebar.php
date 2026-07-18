<?php
if (!defined('ABSPATH')) {
  exit;
}
?>
<div <?php generate_do_attr('right-sidebar'); ?>>
  <div class="inside-right-sidebar">
    <?php do_action('generate_before_right_sidebar_content'); ?>

    <?php if (is_singular('post')) : ?>
      <?php bc_widget_search(); ?>
      <?php bc_widget_series(); ?>
      <?php bc_widget_recent_posts(); ?>
      <?php bc_widget_recent_comments(); ?>
      <?php bc_widget_tag_cloud(); ?>
    <?php elseif (is_page_template('page-todos-los-articulos.php')) : ?>
      <?php bc_widget_search(); ?>
      <?php bc_widget_tag_cloud(); ?>
    <?php else : ?>
      <?php if (!dynamic_sidebar('sidebar-1')) {
        generate_do_default_sidebar_widgets('right-sidebar');
      } ?>
    <?php endif; ?>

    <?php do_action('generate_after_right_sidebar_content'); ?>
  </div>
</div>
