<?php
/**
 * Template Name: Glosario de Ubicaciones (archive)
 */

add_action('wp_head', function () { ?>
  <style>
  .bc-glossary-entry-link .bc-glossary-entry-type {
    color: #888;
    font-size: .88em;
  }
  .bc-glossary-entry-link .bc-glossary-entry-type::before {
    content: " (";
  }
  .bc-glossary-entry-link .bc-glossary-entry-type::after {
    content: ")";
  }
  .bc-glossary-entry-icon {
    margin-right: .3em;
    width: 1.1em;
    text-align: center;
  }
  .bc-filter-select {
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 1rem;
    margin-left: .5rem;
    padding: .5rem .75rem;
  }
  .bc-filter-select:focus {
    border-color: #2e7d32;
    outline: none;
    box-shadow: 0 0 0 2px rgba(46,125,50,.2);
  }
  @media (max-width: 575.98px) {
    .bc-filter-select { margin-left: 0; margin-top: .5rem; width: 100%; }
  }

  .bc-glossary-count-label {
    font-size: .85rem;
    color: #888;
    margin: 0 0 .75rem;
  }
  .bc-glossary-entry-disambig {
    color: #999;
    font-size: .82em;
    font-style: italic;
  }
  .bc-glossary-entry-disambig::before {
    content: " (";
  }
  .bc-glossary-entry-disambig::after {
    content: ")";
  }
  .bc-glossary-letter-group--hidden { display: none; }
  </style>
<?php }, 20);

get_header(); ?>

<main id="main" class="bc-glossary-archive">
  <div class="grid-container">
    <header class="bc-glossary-header">
      <h1 class="bc-glossary-title">Glosario de Ubicaciones</h1>
    </header>

    <?php if ( have_posts() ) : ?>

      <?php
      $type_labels = array(
        'city'       => 'Ciudad',
        'region'     => 'Región',
        'wilderness' => 'Desierto',
        'sea'        => 'Mar / Lago',
        'river'      => 'Río',
        'mountain'   => 'Montaña',
        'settlement' => 'Asentamiento',
        'landmark'   => 'Lugar emblemático',
      );

      $type_icons = array(
        'city'       => 'fa-city',
        'region'     => 'fa-globe',
        'wilderness' => 'fa-tree',
        'sea'        => 'fa-water',
        'river'      => 'fa-water',
        'mountain'   => 'fa-mountain',
        'settlement' => 'fa-home',
        'landmark'   => 'fa-flag',
      );

      $entries = array();
      while ( have_posts() ) {
        the_post();
        $pid   = get_the_ID();
        $first = strtoupper( remove_accents( mb_substr( get_the_title(), 0, 1 ) ) );
        if ( ! preg_match( '/[A-ZÑ]/u', $first ) ) {
          $first = '#';
        }
        $type      = get_post_meta( $pid, '_bc_loc_type', true );
      $disambig  = get_post_meta( $pid, '_bc_loc_disambiguation', true );
        $entries[ $first ][] = array(
          'pid'           => $pid,
          'title'         => get_the_title(),
          'permalink'     => get_permalink(),
          'type'          => $type,
          'type_label'    => isset( $type_labels[ $type ] ) ? $type_labels[ $type ] : $type,
          'icon'          => isset( $type_icons[ $type ] ) ? $type_icons[ $type ] : 'fa-map-marker-alt',
          'disambiguation'=> $disambig,
        );
      }
      ksort( $entries );
      if ( isset( $entries['#'] ) ) {
        $hashtag = $entries['#'];
        unset( $entries['#'] );
        $entries['#'] = $hashtag;
      }

      $keys = array_keys( $entries );
      $first_letter = ! empty( $keys ) ? $keys[0] : 'A';
      $total = array_sum( array_map( 'count', $entries ) );
      ?>

      <p class="bc-glossary-count-label"><?php echo (int) $total; ?> ubicaciones</p>

      <div class="bc-glossary-filters">
        <input type="text" id="bc-filter-search" class="bc-filter-input" placeholder="🔍 Buscar ubicación…" autocomplete="off">
        <select id="bc-filter-type" class="bc-filter-select">
          <option value="">Tipo: Todos</option>
          <?php foreach ( $type_labels as $key => $label ) : ?>
            <option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <nav class="bc-glossary-nav" aria-label="Filtrar por letra">
        <?php foreach ( $keys as $letter ) : ?>
          <button type="button"
            data-letter="<?php echo esc_attr( $letter ); ?>"
            class="bc-glossary-nav-link<?php echo $letter === $first_letter ? ' active' : ''; ?>">
            <?php echo esc_html( $letter ); ?>
          </button>
        <?php endforeach; ?>
        <button type="button" data-letter="all" class="bc-glossary-nav-link">Todas</button>
      </nav>

      <div id="bc-glossary-no-results" class="bc-glossary-no-results" style="display:none;">
        <p>No se encontraron ubicaciones con los filtros seleccionados.</p>
      </div>

      <?php foreach ( $entries as $letter => $items ) : ?>
        <section id="glosario-letra-<?php echo esc_attr( $letter ); ?>"
          class="bc-glossary-letter-group<?php echo $letter === $first_letter ? '' : ' bc-glossary-letter-group--hidden'; ?>"
          data-letter="<?php echo esc_attr( $letter ); ?>">
          <h2 class="bc-glossary-letter-heading"><?php echo esc_html( $letter ); ?></h2>
          <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3">
            <?php foreach ( $items as $item ) : ?>
              <div class="col bc-glossary-entry"
                data-name="<?php echo esc_attr( mb_strtolower( $item['title'] ) ); ?>"
                data-type="<?php echo esc_attr( $item['type'] ); ?>">
                <a href="<?php echo esc_url( $item['permalink'] ); ?>" class="bc-glossary-entry-link">
                  <i class="fas <?php echo esc_attr( $item['icon'] ); ?> bc-glossary-entry-icon"></i>
                  <?php echo esc_html( $item['title'] ); ?>
                  <?php if ( $item['disambiguation'] ) : ?>
                    <span class="bc-glossary-entry-disambig"><?php echo esc_html( $item['disambiguation'] ); ?></span>
                  <?php endif; ?>
                  <?php if ( $item['type_label'] ) : ?>
                    <span class="bc-glossary-entry-type"><?php echo esc_html( $item['type_label'] ); ?></span>
                  <?php endif; ?>
                </a>
              </div>
            <?php endforeach; ?>
          </div>
        </section>
      <?php endforeach; ?>

      <script>
      (function() {
        var search = document.getElementById('bc-filter-search');
        var type   = document.getElementById('bc-filter-type');
        var groups = document.querySelectorAll('.bc-glossary-letter-group');
        var navLinks = document.querySelectorAll('.bc-glossary-nav-link');
        var noResults = document.getElementById('bc-glossary-no-results');
        var currentLetter = <?php echo json_encode( $first_letter ); ?>;
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
          var tv = type.value;
          isSearching = sv.length > 0 || tv.length > 0;
          var totalVisible = 0;

          navLinks.forEach(function(l) {
            l.classList.toggle('active', false);
          });

          groups.forEach(function(g) {
            var entries = g.querySelectorAll('.bc-glossary-entry');
            var groupVisible = false;

            entries.forEach(function(e) {
              var show = true;
              if (sv && e.getAttribute('data-name').indexOf(sv) === -1) show = false;
              if (tv && e.getAttribute('data-type') !== tv) show = false;
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
              type.value = '';
            }
            showLetter(this.getAttribute('data-letter'));
          });
        });

        function onFilterChange() {
          var sv = search.value.toLowerCase().trim();
          var tv = type.value;
          if (sv.length > 0 || tv.length > 0) {
            filterBySearch();
          } else if (isSearching) {
            showLetter(currentLetter);
          }
        }

        search.addEventListener('input', onFilterChange);
        type.addEventListener('change', onFilterChange);
      })();
      </script>

    <?php else : ?>
      <p><?php esc_html_e( 'No se encontraron ubicaciones.', 've-theme' ); ?></p>
    <?php endif; ?>
  </div>
</main>

<?php get_footer(); ?>
