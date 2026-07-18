<?php

add_action('init', function () {
  $labels = [
    'name'              => __('Capítulos de las Escrituras', 'bc'),
    'singular_name'     => __('Capítulo', 'bc'),
    'search_items'      => __('Buscar capítulos', 'bc'),
    'all_items'         => __('Todos los capítulos', 'bc'),
    'parent_item'       => __('Libro', 'bc'),
    'parent_item_colon' => __('Libro:', 'bc'),
    'edit_item'         => __('Editar capítulo', 'bc'),
    'update_item'       => __('Actualizar capítulo', 'bc'),
    'add_new_item'      => __('Añadir nuevo capítulo', 'bc'),
    'new_item_name'     => __('Nuevo capítulo', 'bc'),
    'menu_name'         => __('Capítulos', 'bc'),
  ];

  register_taxonomy('bc_chapter', ['post', 'bc_quote_author'], [
    'labels'            => $labels,
    'hierarchical'      => true,
    'public'            => true,
    'show_ui'           => true,
    'show_admin_column' => true,
    'show_in_menu'      => true,
    'show_in_nav_menus' => false,
    'show_in_rest'      => true,
    'rest_base'         => 'bc-chapters',
    'rewrite'           => false,
    'query_var'         => 'bc_chapter',
    'capabilities'      => [
      'manage_terms' => 'manage_categories',
      'edit_terms'   => 'manage_categories',
      'delete_terms' => 'manage_categories',
      'assign_terms' => 'edit_posts',
    ],
  ]);
});

add_action('add_meta_boxes', function () {
  foreach (['post', 'bc_quote_author'] as $pt) {
    add_meta_box(
      'bc_chapter_metabox',
      __('Capítulos de las Escrituras', 'bc'),
      'bc_chapter_metabox',
      $pt,
      'side',
      'default'
    );
  }
});

function bc_chapter_metabox($post, $box) {
  $terms = wp_get_post_terms($post->ID, 'bc_chapter');
  $selected = [];
  foreach ($terms as $t) {
    $parent = $t->parent ? get_term($t->parent)->name : '';
    $selected[] = ['id' => $t->term_id, 'name' => $t->name, 'book' => $parent];
  }
  ?>
  <div id="bc-chapter-metabox">
    <p class="bc-chapter-metabox-hint"><i class="dashicons dashicons-search"></i> Escribe el libro o número de capítulo para buscar</p>
    <input type="text" id="bc-chapter-search" class="widefat" placeholder="Ej: Génesis 19, DyC 133, Lucas…" autocomplete="off">
    <div id="bc-chapter-selected"><?php
      foreach ($selected as $s) {
        $label = $s['book'] ? "{$s['name']} ({$s['book']})" : $s['name'];
        echo '<span class="bc-chapter-chip" data-id="' . esc_attr($s['id']) . '">' . esc_html($label) . '<button type="button" class="bc-chapter-remove">&times;</button></span>';
      }
    ?></div>
    <input type="hidden" name="bc_chapter_terms" id="bc-chapter-input" value="<?php echo esc_attr(implode(',', wp_list_pluck($selected, 'id'))); ?>">
  </div>
  <?php
}

/**
 * 3. Frontend: Scripture references section on single posts.
 * Shows linked chapters grouped by book.
 */
add_action('generate_after_entry_content', function () {
  if (!is_singular('post')) return;

  $terms = wp_get_post_terms(get_the_ID(), 'bc_chapter');
  if (empty($terms) || is_wp_error($terms)) return;

  $by_book = [];
  foreach ($terms as $t) {
    if ($t->parent > 0) {
      $book = get_term($t->parent);
      if ($book && !is_wp_error($book)) {
        $by_book[$book->name][] = $t;
      }
    }
  }
  if (empty($by_book)) return;

  if (count($by_book) === 1) {
    $single_title = __('Capítulo referenciado', 'bc');
  } else {
    $single_title = __('Capítulos referenciados', 'bc');
  }

  ksort($by_book);
  echo '<div class="bc-chapter-refs">';
  echo '<h3 class="bc-chapter-refs-title"><i class="fas fa-book-bible"></i> ' . esc_html($single_title) . '</h3>';
  echo '<ul class="bc-chapter-refs-list">';
  foreach ($by_book as $book_name => $chaps) {
    foreach ($chaps as $chap) {
      echo '<li><a href="' . esc_url(get_term_link($chap)) . '" class="bc-chapter-refs-link">'
        . '<i class="fas fa-bookmark"></i> ' . esc_html($chap->name) . '</a></li>';
    }
  }
  echo '</ul></div>';
}, 5);

/**
 * 4. SEO interlinking: posts sharing the same bc_chapter.
 * Groups related posts by the chapter they share with the current post.
 */
add_action('generate_after_entry_content', function () {
  if (!is_singular('post') || !in_the_loop() || !is_main_query()) return;

  $post_id = get_the_ID();
  $chapters = wp_get_post_terms($post_id, 'bc_chapter');
  if (empty($chapters) || is_wp_error($chapters)) return;

  $groups = [];
  foreach ($chapters as $t) {
    if ($t->parent <= 0) continue;

    $related = new WP_Query([
      'tax_query' => [[
        'taxonomy' => 'bc_chapter',
        'field'    => 'term_id',
        'terms'    => $t->term_id,
      ]],
      'post__not_in'       => [$post_id],
      'posts_per_page'     => 4,
      'ignore_sticky_posts' => true,
      'no_found_rows'      => true,
    ]);

    if ($related->have_posts()) {
      $groups[$t->name] = $related;
    }
  }
  if (empty($groups)) return;
  $group_count = count($groups);

  echo '<div class="bc-chapter-posts" data-bc-chapter-count="' . $group_count . '">';

  if ($group_count === 1) {
    $chap_name = array_key_first($groups);
    echo '<h3 class="bc-chapter-posts-title"><i class="fas fa-layer-group"></i> ' . sprintf(__('Más sobre %s', 'bc'), esc_html($chap_name)) . '</h3>';
    $query = $groups[$chap_name];
    echo '<div class="bc-chapter-posts-grid">';
    while ($query->have_posts()) {
      $query->the_post();
      echo '<a href="' . esc_url(get_permalink()) . '" class="bc-chapter-post-link">'
        . esc_html(get_the_title()) . '</a>';
    }
    wp_reset_postdata();
    echo '</div>';

  } elseif ($group_count <= 6) {
    echo '<h3 class="bc-chapter-posts-title"><i class="fas fa-layer-group"></i> ' . __('Más sobre estos capítulos', 'bc') . '</h3>';
    echo '<ul class="nav nav-tabs bc-chapter-tabs" role="tablist">';
    $first = true;
    foreach ($groups as $chap_name => $query) {
      $tab_id = 'tab-' . sanitize_title($chap_name);
      echo '<li class="nav-item" role="presentation">';
      echo '<button class="nav-link' . ($first ? ' active' : '') . '" id="' . $tab_id . '-tab" data-bs-toggle="tab" data-bs-target="#' . $tab_id . '" type="button" role="tab" aria-controls="' . $tab_id . '" aria-selected="' . ($first ? 'true' : 'false') . '">'
        . esc_html($chap_name) . '</button>';
      echo '</li>';
      $first = false;
    }
    echo '</ul>';
    echo '<div class="tab-content bc-chapter-tab-content">';
    $first = true;
    foreach ($groups as $chap_name => $query) {
      $tab_id = 'tab-' . sanitize_title($chap_name);
      echo '<div class="tab-pane fade' . ($first ? ' show active' : '') . '" id="' . $tab_id . '" role="tabpanel" aria-labelledby="' . $tab_id . '-tab">';
      echo '<div class="bc-chapter-posts-grid">';
      while ($query->have_posts()) {
        $query->the_post();
        echo '<a href="' . esc_url(get_permalink()) . '" class="bc-chapter-post-link">'
          . esc_html(get_the_title()) . '</a>';
      }
      wp_reset_postdata();
      echo '</div></div>';
      $first = false;
    }
    echo '</div>';

  } else {
    echo '<h3 class="bc-chapter-posts-title"><i class="fas fa-layer-group"></i> ' . sprintf(__('Más sobre estos capítulos (%d)', 'bc'), $group_count) . '</h3>';
    echo '<select class="form-select bc-chapter-select" aria-label="' . esc_attr__('Seleccionar capítulo', 'bc') . '">';
    echo '<option value="">— ' . esc_attr__('Elige un capítulo', 'bc') . ' —</option>';
    foreach ($groups as $chap_name => $query) {
      $opt_id = 'opt-' . sanitize_title($chap_name);
      echo '<option value="' . $opt_id . '">' . esc_html($chap_name) . '</option>';
    }
    echo '</select>';
    echo '<div class="bc-chapter-dropdown-panes">';
    foreach ($groups as $chap_name => $query) {
      $opt_id = 'opt-' . sanitize_title($chap_name);
      echo '<div class="bc-chapter-dropdown-pane" id="' . $opt_id . '">';
      echo '<div class="bc-chapter-posts-grid">';
      while ($query->have_posts()) {
        $query->the_post();
        echo '<a href="' . esc_url(get_permalink()) . '" class="bc-chapter-post-link">'
          . esc_html(get_the_title()) . '</a>';
      }
      wp_reset_postdata();
      echo '</div></div>';
    }
    echo '</div>';
    ?>
    <script>
    (function() {
      var select = document.querySelector('.bc-chapter-select');
      if (!select) return;
      var panes = select.parentNode.querySelectorAll('.bc-chapter-dropdown-pane');
      if (panes.length) { panes[0].style.display = 'block'; }
      select.addEventListener('change', function() {
        panes.forEach(function(p) { p.style.display = 'none'; });
        var target = document.getElementById(this.value);
        if (target) target.style.display = 'block';
      });
    })();
    </script>
    <?php
  }

  echo '</div>';
}, 10);

add_action('save_post', function ($post_id) {
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
  if (!isset($_POST['bc_chapter_terms'])) return;
  if (!current_user_can('edit_post', $post_id)) return;

  $ids = array_filter(array_map('intval', explode(',', $_POST['bc_chapter_terms'])));
  $post_type = get_post_type($post_id);
  $supported = ['post', 'bc_quote_author'];
  if (!in_array($post_type, $supported, true)) return;

  wp_set_object_terms($post_id, $ids, 'bc_chapter');
});

add_action('wp_ajax_bc_chapter_search', function () {
  $search = isset($_GET['q']) ? sanitize_text_field($_GET['q']) : '';
  if (strlen($search) < 2) {
    wp_send_json([]);
  }

  $terms = get_terms([
    'taxonomy'   => 'bc_chapter',
    'hide_empty' => false,
    'name__like' => $search,
    'number'     => 20,
    'orderby'    => 'name',
    'order'      => 'ASC',
  ]);

  $results = [];
  foreach ($terms as $t) {
    $book = $t->parent ? get_term($t->parent)->name : '';
    $label = $book ? "{$t->name} ({$book})" : $t->name;
    $results[] = [
      'id'    => $t->term_id,
      'label' => $label,
      'value' => $t->name,
      'book'  => $book,
    ];
  }

  wp_send_json($results);
});

add_action('rest_api_init', function () {
  register_rest_field(['post', 'bc_quote_author'], 'bc_chapter_terms', [
    'get_callback' => function ($post) {
      $terms = wp_get_post_terms($post['id'], 'bc_chapter');
      if (empty($terms) || is_wp_error($terms)) return [];
      $data = [];
      foreach ($terms as $t) {
        $parent = $t->parent ? get_term($t->parent)->name : '';
        $data[] = [
          'id'   => $t->term_id,
          'name' => $t->name,
          'slug' => $t->slug,
          'book' => $parent,
          'link' => get_term_link($t),
        ];
      }
      return $data;
    },
    'schema' => [
      'type'  => 'array',
      'items' => [
        'type'       => 'object',
        'properties' => [
          'id'   => ['type' => 'integer'],
          'name' => ['type' => 'string'],
          'slug' => ['type' => 'string'],
          'book' => ['type' => 'string'],
          'link' => ['type' => 'string'],
        ],
      ],
    ],
  ]);
});

add_action('admin_enqueue_scripts', function ($hook) {
  if (!in_array($hook, ['post.php', 'post-new.php'], true)) return;

  $screen = get_current_screen();
  if (!$screen || !in_array($screen->post_type, ['post', 'bc_quote_author'], true)) return;

  wp_enqueue_script('jquery-ui-autocomplete');
  wp_enqueue_script('bc-chapter-admin', get_stylesheet_directory_uri() . '/assets/admin-chapter.js', ['jquery', 'jquery-ui-autocomplete', 'wp-data', 'wp-dom-ready', 'wp-edit-post'], '1.0', true);
  wp_localize_script('bc-chapter-admin', 'bcChapter', [
    'ajaxUrl' => admin_url('admin-ajax.php'),
    'nonce'   => wp_create_nonce('bc_chapter_search'),
  ]);

  wp_enqueue_style('bc-chapter-admin', get_stylesheet_directory_uri() . '/assets/admin-chapter.css', [], '1.0');
});

add_filter('term_link', function ($url, $term, $taxonomy) {
  if ($taxonomy !== 'bc_chapter') {
    return $url;
  }
  $slug = $term->slug;
  if ($term->parent > 0) {
    return home_url("/capitulo/{$slug}/");
  }
  return home_url("/libro/{$slug}/");
}, 10, 3);

add_filter('rewrite_rules_array', function ($rules) {
  $new = [];
  $chapters = get_terms([
    'taxonomy'   => 'bc_chapter',
    'hide_empty' => false,
    'fields'     => 'all',
  ]);

  foreach ($chapters as $term) {
    $slug = preg_quote($term->slug, '/');
    if ($term->parent > 0) {
      $new["capitulo/{$slug}/?$"] = "index.php?bc_chapter={$term->slug}";
    } else {
      $new["libro/{$slug}/?$"] = "index.php?bc_chapter={$term->slug}";
    }
  }

  $new['capitulo/([^/]+)/?$'] = 'index.php?bc_chapter=$matches[1]';
  $new['libro/([^/]+)/?$'] = 'index.php?bc_chapter=$matches[1]';

  return $new + $rules;
});

add_action('created_bc_chapter', 'bc_flush_on_term_change');
add_action('edited_bc_chapter', 'bc_flush_on_term_change');
add_action('delete_bc_chapter', 'bc_flush_on_term_change');
function bc_flush_on_term_change() {
  add_action('shutdown', function () {
    $GLOBALS['wp_rewrite']->flush_rules();
  });
}
