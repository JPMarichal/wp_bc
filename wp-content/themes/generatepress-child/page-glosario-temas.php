<?php
/**
 * Template Name: Glosario de Temas
 */

// Force no sidebar for this page
add_filter('generate_sidebar_layout', function ($layout) {
  return 'no-sidebar';
});

// Inline styles for the filter UX (not in compiled CSS yet)
add_action('wp_head', function () { ?>
  <style>
  body.page-template-page-glosario-temas .site.grid-container,
  .bc-glossary-archive .grid-container {
    max-width: 100%;
  }
  body.page-template-page-glosario-temas .site-content {
    padding: 0;
    width: 100%;
  }
  body.page-template-page-glosario-temas .bc-glossary-archive {
    width: 100%;
  }
  .bc-glossary-archive .grid-container {
    padding-left: 20px;
    padding-right: 20px;
  }
  @media (min-width: 1200px) {
    .bc-glossary-archive .grid-container {
      padding-left: 40px;
      padding-right: 40px;
    }
  }

  .bc-glossary-letter-group--hidden { display: none; }

  .bc-glossary-archive .bc-glossary-header { margin-bottom: 1.5rem; }
  .bc-glossary-archive .bc-glossary-title { margin-bottom: .25rem; }

  .bc-glossary-archive .bc-glossary-filters { margin-bottom: 1rem; }
  .bc-glossary-archive .bc-filter-input {
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 1rem;
    max-width: 400px;
    padding: .5rem .75rem;
    width: 100%;
  }
  .bc-glossary-archive .bc-filter-input:focus {
    border-color: #2e7d32;
    outline: none;
    box-shadow: 0 0 0 2px rgba(46,125,50,.2);
  }

  .bc-glossary-archive .bc-glossary-nav {
    border-bottom: 2px solid #ddd;
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
  }
  .bc-glossary-archive .bc-glossary-nav-link {
    align-items: center;
    background: #2e7d32;
    border: 1px solid #2e7d32;
    border-radius: 4px;
    color: #fff;
    cursor: pointer;
    display: inline-flex;
    font-size: .85rem;
    font-weight: 600;
    height: 2.2rem;
    justify-content: center;
    line-height: 1;
    padding: 0;
    text-decoration: none;
    transition: background .15s, border-color .15s, color .15s;
    width: 2.2rem;
  }
  .bc-glossary-archive .bc-glossary-nav-link:hover {
    background: #388e3c;
    border-color: #388e3c;
    color: #fff;
  }
  .bc-glossary-archive .bc-glossary-nav-link.active,
  .bc-glossary-archive .bc-glossary-nav-link.active:hover {
    background: #e65100;
    border-color: #e65100;
    color: #fff;
  }
  .bc-glossary-archive .bc-glossary-nav-link.active:hover {
    background: #f57c00;
    border-color: #f57c00;
  }

  .bc-glossary-archive .bc-glossary-no-results {
    background: #f9f6f0;
    border-radius: 6px;
    margin-bottom: 1.5rem;
    padding: 1.5rem;
    text-align: center;
  }
  .bc-glossary-archive .bc-glossary-no-results p { margin: 0; }

  .bc-glossary-archive .bc-glossary-letter-group .bc-glossary-letter-heading {
    border-bottom: 2px solid #2e7d32;
    color: #2e7d32;
    font-size: 1.4rem;
    margin-bottom: .75rem;
    padding-bottom: .25rem;
  }

  .bc-glossary-archive .bc-glossary-letter-group .bc-glossary-entry {
    margin-bottom: .5rem;
  }
  .bc-glossary-archive .bc-glossary-letter-group .bc-glossary-entry-link,
  .bc-glossary-archive .bc-glossary-letter-group .bc-glossary-entry-link:link,
  .bc-glossary-archive .bc-glossary-letter-group .bc-glossary-entry-link:visited {
    color: #3a3a3a;
    display: inline-block;
    font-size: .95rem;
    padding: .25rem 0;
    text-decoration: none;
    transition: color .15s;
  }
  .bc-glossary-archive .bc-glossary-letter-group .bc-glossary-entry-link:hover,
  .bc-glossary-archive .bc-glossary-letter-group .bc-glossary-entry-link:focus {
    color: #e65100;
    text-decoration: underline;
  }
  .bc-glossary-archive .bc-glossary-letter-group .bc-glossary-count {
    color: #888;
    font-size: .82em;
  }

  @media (max-width: 575.98px) {
    .bc-glossary-archive .bc-glossary-nav { gap: 3px; }
    .bc-glossary-archive .bc-glossary-nav-link { font-size: .78rem; height: 1.9rem; width: 1.9rem; }
    .bc-glossary-archive .bc-glossary-letter-group .bc-glossary-entry-link { font-size: .88rem; }
  }
  .bc-glossary-archive .grid-container {
    padding-left: 20px;
    padding-right: 20px;
  }
  @media (min-width: 1200px) {
    .bc-glossary-archive .grid-container {
      padding-left: 40px;
      padding-right: 40px;
    }
  }

  .bc-glossary-letter-group--hidden { display: none; }

  .bc-glossary-header { margin-bottom: 1.5rem; }
  .bc-glossary-title { margin-bottom: .25rem; }

  .bc-glossary-filters { margin-bottom: 1rem; }
  .bc-filter-input {
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 1rem;
    max-width: 400px;
    padding: .5rem .75rem;
    width: 100%;
  }
  .bc-filter-input:focus {
    border-color: #2d5a27;
    outline: none;
    box-shadow: 0 0 0 2px rgba(45,90,39,.2);
  }

  .bc-glossary-nav {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    margin-bottom: 1.5rem;
  }
  .bc-glossary-nav-link {
    align-items: center;
    background: #2d5a27;
    border: 1px solid #2d5a27;
    border-radius: 4px;
    color: #f5f0eb;
    cursor: pointer;
    display: inline-flex;
    font-size: .85em;
    font-weight: 600;
    height: 2.2rem;
    justify-content: center;
    line-height: 1;
    padding: 0;
    text-decoration: none;
    transition: background .15s, border-color .15s, color .15s;
    width: 2.2rem;
  }
  .bc-glossary-nav-link:hover {
    background: #3d7a37;
    border-color: #3d7a37;
    color: #fff;
  }
  .bc-glossary-nav-link.active {
    background: #4a3728;
    border-color: #4a3728;
    color: #fff;
  }
  .bc-glossary-nav-link.active:hover {
    background: #5c4736;
    border-color: #5c4736;
  }

  .bc-glossary-no-results {
    background: #f9f6f0;
    border-radius: 6px;
    margin-bottom: 1.5rem;
    padding: 1.5rem;
    text-align: center;
  }
  .bc-glossary-no-results p { margin: 0; }

  .bc-glossary-letter-heading {
    border-bottom: 2px solid #2d5a27;
    color: #2d5a27;
    font-size: 1.4rem;
    margin-bottom: .75rem;
    padding-bottom: .25rem;
  }

  .bc-glossary-entry { margin-bottom: .5rem; }
  .bc-glossary-entry-link,
  .bc-glossary-entry-link:link,
  .bc-glossary-entry-link:visited {
    color: #3a3a3a;
    display: inline-block;
    font-size: .95rem;
    padding: .25rem 0;
    text-decoration: none;
    transition: color .15s;
  }
  .bc-glossary-entry-link:hover,
  .bc-glossary-entry-link:focus,
  .bc-glossary-entry-link:active {
    color: #2d5a27;
    text-decoration: underline;
  }
  .bc-glossary-count {
    color: #888;
    font-size: .82em;
  }
  .bc-glossary-count-label {
    font-size: .85rem;
    color: #888;
    margin: 0 0 .75rem;
  }

  @media (max-width: 575.98px) {
    .bc-glossary-nav { gap: 3px; }
    .bc-glossary-nav-link { font-size: .78em; height: 1.9rem; width: 1.9rem; }
    .bc-glossary-entry-link { font-size: .88rem; }
  }
  </style>
<?php }, 20);

get_header(); ?>

<main id="main" class="bc-glossary-archive">
  <div class="grid-container">
    <header class="bc-glossary-header">
      <h1 class="bc-glossary-title">Glosario de Temas</h1>
    </header>

    <?php
    $all_tags = get_tags([
      'hide_empty' => false,
      'orderby' => 'name',
      'order' => 'ASC',
    ]);

    if (!empty($all_tags)) :
      $entries = [];
      foreach ($all_tags as $t) {
        $first = strtoupper(mb_substr($t->name, 0, 1));
        if (!preg_match('/[A-ZÁÉÍÓÚÑ]/u', $first)) {
          $first = '#';
        }
        $entries[$first][] = $t;
      }
      ksort($entries);
      if (isset($entries['#'])) {
        $hashtag = $entries['#'];
        unset($entries['#']);
        $entries['#'] = $hashtag;
      }

      $keys = array_keys($entries);
      $first_letter = !empty($keys) ? $keys[0] : 'A';
      $total = count($all_tags);
      ?>

      <p class="bc-glossary-count-label"><?php echo (int) $total; ?> temas</p>

      <div class="bc-glossary-filters">
        <input type="text" id="bc-filter-search" class="bc-filter-input" placeholder="🔍 Buscar tema…" autocomplete="off">
      </div>

      <nav class="bc-glossary-nav" aria-label="Filtrar por letra">
        <?php foreach ($keys as $letter) : ?>
          <button type="button"
            data-letter="<?php echo esc_attr($letter); ?>"
            class="bc-glossary-nav-link<?php echo $letter === $first_letter ? ' active' : ''; ?>">
            <?php echo esc_html($letter); ?>
          </button>
        <?php endforeach; ?>
        <button type="button" data-letter="all" class="bc-glossary-nav-link">Todas</button>
      </nav>

      <div id="bc-glossary-no-results" class="bc-glossary-no-results" style="display:none;">
        <p>No se encontraron temas con ese nombre.</p>
      </div>

      <?php foreach ($entries as $letter => $items) : ?>
        <section id="glosario-letra-<?php echo esc_attr($letter); ?>"
          class="bc-glossary-letter-group<?php echo $letter === $first_letter ? '' : ' bc-glossary-letter-group--hidden'; ?>"
          data-letter="<?php echo esc_attr($letter); ?>">
          <h2 class="bc-glossary-letter-heading"><?php echo esc_html($letter); ?></h2>
          <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 row-cols-xxl-6">
            <?php foreach ($items as $t) : ?>
              <div class="col bc-glossary-entry"
                data-name="<?php echo esc_attr(mb_strtolower($t->name)); ?>">
                <a href="<?php echo esc_url(get_tag_link($t)); ?>" class="bc-glossary-entry-link">
                  <?php echo esc_html($t->name); ?>
                  <span class="bc-glossary-count">(<?php echo esc_html($t->count); ?>)</span>
                </a>
              </div>
            <?php endforeach; ?>
          </div>
        </section>
      <?php endforeach; ?>

      <script>
      (function() {
        var search = document.getElementById('bc-filter-search');
        var groups = document.querySelectorAll('.bc-glossary-letter-group');
        var navLinks = document.querySelectorAll('.bc-glossary-nav-link');
        var noResults = document.getElementById('bc-glossary-no-results');
        var currentLetter = <?php echo json_encode($first_letter); ?>;
        var isSearching = false;

        function showLetter(letter) {
          currentLetter = letter;
          isSearching = false;

          navLinks.forEach(function(l) {
            var ltr = l.getAttribute('data-letter');
            l.classList.toggle('active', ltr === letter);
          });

          groups.forEach(function(g) {
            var show = letter === 'all' || g.getAttribute('data-letter') === letter;
            g.classList.toggle('bc-glossary-letter-group--hidden', !show);
          });

          noResults.style.display = 'none';
        }

        function filterBySearch() {
          var sv = search.value.toLowerCase().trim();
          isSearching = sv.length > 0;
          var totalVisible = 0;

          navLinks.forEach(function(l) {
            l.classList.toggle('active', false);
          });

          groups.forEach(function(g) {
            var entries = g.querySelectorAll('.bc-glossary-entry');
            var groupVisible = false;

            entries.forEach(function(e) {
              var show = e.getAttribute('data-name').indexOf(sv) !== -1;
              e.style.display = show ? '' : 'none';
              if (show) groupVisible = true;
            });

            g.classList.toggle('bc-glossary-letter-group--hidden', !groupVisible);
            if (groupVisible) totalVisible++;
          });

          noResults.style.display = totalVisible === 0 ? '' : 'none';
        }

        navLinks.forEach(function(link) {
          link.addEventListener('click', function() {
            if (isSearching) {
              search.value = '';
            }
            showLetter(this.getAttribute('data-letter'));
          });
        });

        search.addEventListener('input', function() {
          if (this.value.toLowerCase().trim().length > 0) {
            filterBySearch();
          } else if (isSearching) {
            showLetter(currentLetter);
          }
        });
      })();
      </script>

    <?php else : ?>
      <p>No hay temas registrados.</p>
    <?php endif; ?>
  </div>
</main>

<?php get_footer(); ?>
