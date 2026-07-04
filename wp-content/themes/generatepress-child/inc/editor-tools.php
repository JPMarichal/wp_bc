<?php

add_action('admin_print_footer_scripts', function () {
  $screen = get_current_screen();
  if (!$screen || 'post' !== $screen->base) {
    return;
  }
  ?>
  <script>
  (function() {
    if (document.querySelector('.bc-new-post-btn')) return;
    var observer = new MutationObserver(function() {
      var toolbar = document.querySelector('.edit-post-header__toolbar');
      if (!toolbar || document.querySelector('.bc-new-post-btn')) return;
      var btn = document.createElement('a');
      btn.className = 'bc-new-post-btn components-button is-secondary';
      btn.href = 'post-new.php';
      btn.title = 'Crear nuevo art\u00edculo';
      btn.style.cssText = 'display:inline-flex;align-items:center;gap:4px;margin-left:8px;height:33px;padding:0 12px;border-radius:3px;font-size:13px;text-decoration:none;white-space:nowrap';
      btn.innerHTML = '<span class="dashicons dashicons-plus-alt2" style="font-size:16px;width:16px;height:16px;"></span> Nuevo Art\u00edculo';
      toolbar.appendChild(btn);
    });
    observer.observe(document.body, { childList: true, subtree: true });
  })();
  </script>
  <?php
});

add_action('wp_before_admin_bar_render', function () {
  global $wp_admin_bar;
  if (!is_admin()) {
    return;
  }
  $screen = get_current_screen();
  if (!$screen || 'post' !== $screen->base) {
    return;
  }
  $wp_admin_bar->add_menu([
    'id'    => 'bc-new-post',
    'title' => '<span class="ab-icon dashicons dashicons-plus-alt2"></span><span class="ab-label">Nuevo Artículo</span>',
    'href'  => admin_url('post-new.php'),
    'meta'  => ['target' => '_blank'],
  ]);
});
