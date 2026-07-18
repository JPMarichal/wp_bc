<?php
/**
 * Template: Single Ubicación (bc_location)
 */

// Force full-width layout (no theme sidebar)
add_filter('generate_sidebar_layout', function ($layout) {
  return 'no-sidebar';
});

add_action('wp_head', function () { ?>
  <style>
  html body.single-bc_location,
  html body.single-bc_location .container,
  html body.single-bc_location #page {
    max-width: 100% !important;
    width: 100% !important;
    margin-left: 0 !important;
    margin-right: 0 !important;
    padding-left: 0 !important;
    padding-right: 0 !important;
  }
  html body.single-bc_location .site-content {
    display: block !important;
  }
  body.single-bc_location {
    background: #f5f4f1;
  }
  .bc-glossary-single {
    margin-top: 0;
    padding-top: 0;
  }
  .bc-location-container {
    display: grid;
    grid-template-columns: 1fr;
    gap: 2rem;
    padding: 0 8px 2.5rem;
  }
  @media (min-width: 768px) {
    .bc-location-container {
      grid-template-columns: 1fr 300px;
      padding: 0 12px;
    }
  }
  @media (min-width: 1200px) {
    .bc-location-container { padding: 0 16px; }
  }
  .bc-location-content-area { min-width: 0; }
  .bc-location-sidebar { min-width: 0; }
  .bc-location-hero {
    background: linear-gradient(135deg, #1e3a5f 0%, #2a4f7a 100%);
    padding: 1.5rem 0;
    margin-bottom: 1.25rem;
  }
  .bc-location-hero-inner {
    padding: 0 16px;
  }
  @media (min-width: 768px) { .bc-location-hero-inner { padding: 0 24px; } }
  @media (min-width: 1200px) { .bc-location-hero-inner { padding: 0 32px; } }
  .bc-location-title {
    font-family: Merriweather, Georgia, "Times New Roman", serif;
    font-size: 2rem;
    font-weight: 700;
    margin: 0 0 .5rem;
    color: #fff;
    line-height: 1.25;
  }
  @media (min-width: 768px) { .bc-location-title { font-size: 2.4rem; } }
  .bc-location-hero-meta {
    display: flex;
    flex-wrap: wrap;
    gap: .5rem;
    align-items: center;
  }
  .bc-location-type-badge {
    display: inline-block;
    background: rgba(255,255,255,.15);
    color: rgba(255,255,255,.9);
    font-size: .82rem;
    font-weight: 600;
    padding: .25rem .65rem;
    border-radius: 4px;
    text-transform: lowercase;
  }
  .bc-location-type-badge i { margin-right: .3em; }
  .bc-location-card { }
  .bc-location-image { margin: 1.25rem 0; }
  .bc-location-image img {
    border-radius: 8px;
    width: 100%;
    height: auto;
  }
  .bc-location-content {
    background: #fff;
    border: 1px solid #e0ddd5;
    border-radius: 8px;
    padding: 2rem;
    color: #333;
    line-height: 1.8;
    font-size: 1rem;
    box-shadow: 0 1px 4px rgba(0,0,0,.06);
  }
  .bc-infobox {
    background: #f9f8f6;
    border: 1px solid #e0ddd5;
    border-radius: 8px;
    padding: 1.25rem;
    box-shadow: 0 1px 4px rgba(0,0,0,.06);
  }
  @media (min-width: 768px) { .bc-infobox { position: sticky; top: 1rem; } }
  .bc-infobox-title {
    font-family: Merriweather, Georgia, "Times New Roman", serif;
    font-size: .9rem;
    font-weight: 700;
    text-align: center;
    color: #1e3a5f;
    margin: 0 0 .75rem;
    padding-bottom: .5rem;
    border-bottom: 1px solid #e0ddd5;
  }
  .bc-infobox-list { list-style: none; margin: 0; padding: 0; }
  .bc-infobox-list li {
    display: flex;
    align-items: flex-start;
    gap: .5rem;
    padding: .45rem 0;
    border-bottom: 1px solid #e0ddd5;
    font-size: .85rem;
    color: #444;
    line-height: 1.4;
  }
  .bc-infobox-list li:last-child { border-bottom: none; }
  .bc-infobox-list li i {
    width: 1rem; color: #1e3a5f;
    margin-top: .15rem; flex-shrink: 0; text-align: center;
  }
  .bc-infobox-list li span { flex: 1; min-width: 0; }
  .bc-infobox-confidence-high { color: #2e7d32; font-weight: 600; }
  .bc-infobox-confidence-medium { color: #e65100; font-weight: 600; }
  .bc-infobox-confidence-low { color: #c62828; font-weight: 600; }
  .bc-infobox-relevancia-high { color: #1e3a5f; font-weight: 600; }
  .bc-infobox-relevancia-medium { color: #6d4c41; font-weight: 600; }
  .bc-infobox-relevancia-low { color: #888; font-weight: 600; }
  .bc-location-key-facts {
    display: flex;
    flex-wrap: wrap;
    gap: .5rem;
    align-items: center;
    margin-bottom: 1rem;
    padding: .6rem .8rem;
    background: rgba(30, 58, 95, .04);
    border-left: 3px solid #1e3a5f;
    border-radius: 0 4px 4px 0;
    font-size: .85rem;
    color: #444;
  }
  .bc-key-fact {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
  }
  .bc-key-fact i {
    color: #1e3a5f;
    font-size: .8rem;
  }
  .bc-faq details {
    background: #f9f8f6;
    border: 1px solid #e0ddd5;
    border-radius: 6px;
    padding: .6rem .8rem;
    margin-bottom: .5rem;
  }
  .bc-faq summary {
    font-weight: 600;
    color: #1e3a5f;
    cursor: pointer;
  }
  .bc-faq p {
    margin: .5rem 0 0;
    color: #444;
  }
  .bc-infobox-coords { font-size: .8rem; color: #888; word-break: break-all; }
  .bc-infobox-coords a { color: #1e3a5f; text-decoration: none; }
  .bc-infobox-coords a:hover { text-decoration: underline; }
  .bc-glossary-back-nav {
    padding: 0 8px;
  }
  @media (min-width: 768px) { .bc-glossary-back-nav { padding: 0 12px; } }
  @media (min-width: 1200px) { .bc-glossary-back-nav { padding: 0 16px; } }
  .bc-glossary-back-nav a { color: #1e3a5f; text-decoration: none; font-weight: 600; }
  .bc-glossary-back-nav a:hover { text-decoration: underline; }
  .bc-location-disambig {
    display: inline-block;
    font-size: .82rem;
    font-style: italic;
    color: rgba(255,255,255,.8);
    background: rgba(255,255,255,.12);
    padding: .2rem .6rem;
    border-radius: 4px;
  }
  .bc-location-alt-badge {
    display: inline-block;
    font-size: .78rem;
    color: rgba(255,255,255,.7);
  }
  .bc-location-alt-badge a {
    color: rgba(255,255,255,.85);
    text-decoration: underline;
    text-decoration-color: rgba(255,255,255,.25);
    text-underline-offset: 2px;
  }
  .bc-location-alt-badge a:hover {
    color: #fff;
    text-decoration-color: rgba(255,255,255,.6);
  }
  .bc-alt-sep {
    margin: 0 .4em;
    color: rgba(255,255,255,.3);
  }
  .bc-location-alias-of {
    padding: .6rem 1rem;
    background: #fff;
    border: 1px solid #e0ddd5;
    border-left: 3px solid #1e3a5f;
    border-radius: 4px;
    font-size: .88rem;
    color: #555;
    box-shadow: 0 1px 3px rgba(0,0,0,.06);
  }
  .bc-location-alias-of a {
    color: #1e3a5f;
    font-weight: 600;
    text-decoration: none;
  }
  .bc-location-alias-of a:hover {
    text-decoration: underline;
  }
  .bc-alias-of-icon {
    margin-right: .3em;
  }
  .bc-infobox-alt-sep {
    color: #bbb;
  }
  .bc-section-constrained {
    margin: 0 0 1.25rem;
    padding: 0 8px;
  }
  @media (min-width: 768px) { .bc-section-constrained { padding: 0 12px; } }
  @media (min-width: 1200px) { .bc-section-constrained { padding: 0 16px; } }
  .bc-location-other-meanings {
    padding: 1rem 1.25rem;
    background: #fff;
    border: 1px solid #e0ddd5;
    border-left: 3px solid #1e3a5f;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0,0,0,.06);
  }
  .bc-other-meanings-heading {
    font-family: Merriweather, Georgia, "Times New Roman", serif;
    font-size: .78rem;
    font-weight: 700;
    color: #1e3a5f;
    margin: 0 0 .15rem;
    text-transform: uppercase;
    letter-spacing: .06em;
  }
  .bc-other-meanings-intro { font-size: .85rem; color: #555; margin: 0 0 .35rem; }
  .bc-other-meanings-list { list-style: none; margin: 0; padding: 0; }
  .bc-other-meanings-list li { display: inline; }
  .bc-other-meanings-list li:not(:last-child)::after { content: " \b7 "; color: #bbb; }
  .bc-other-meanings-list a { color: #1e3a5f; font-weight: 500; text-decoration: none; font-size: .88rem; }
  .bc-other-meanings-list a:hover { text-decoration: underline; }
  .bc-other-meanings-disambig { color: #888; font-size: .82rem; font-style: italic; }
  .bc-location-map-inline { margin: 1.75rem 0; }
  .bc-forma-t {
    width: 100%;
    border-collapse: collapse;
    margin: 1rem 0;
    font-size: .9rem;
  }
  .bc-forma-t thead th {
    background: #1e3a5f;
    color: #fff;
    padding: .6rem .75rem;
    text-align: left;
    font-weight: 700;
    font-size: .82rem;
    text-transform: uppercase;
    letter-spacing: .05em;
  }
  .bc-forma-t thead th:last-child {
    width: 35%;
  }
  .bc-forma-t tbody td {
    padding: .6rem .75rem;
    border-bottom: 1px solid #eee;
    vertical-align: top;
  }
  .bc-forma-t tbody tr:nth-child(even) td {
    background: #f8f7f4;
  }
  .bc-forma-t tbody td:first-child {
    font-weight: 500;
    color: #1e3a5f;
  }
  .bc-forma-t tbody td:last-child {
    color: #666;
    white-space: nowrap;
  }
  </style>
<?php }, 25);

get_header(); ?>

<main id="main" class="bc-glossary-single">
  <?php while ( have_posts() ) : the_post();

    $pid        = get_the_ID();
    $lat        = get_post_meta( $pid, '_bc_loc_lat', true );
    $lng        = get_post_meta( $pid, '_bc_loc_lng', true );
    $type       = get_post_meta( $pid, '_bc_loc_type', true );
    $name_en    = get_post_meta( $pid, '_bc_loc_name_en', true );
    $desc       = get_post_meta( $pid, '_bc_loc_description', true );
    $scriptures = get_post_meta( $pid, '_bc_loc_scriptures', true );
    $date_from  = get_post_meta( $pid, '_bc_loc_date_from', true );
    $date_to    = get_post_meta( $pid, '_bc_loc_date_to', true );
    $source     = get_post_meta( $pid, '_bc_loc_source', true );
    $confidence = get_post_meta( $pid, '_bc_loc_confidence', true );
    $disambiguation = get_post_meta( $pid, '_bc_loc_disambiguation', true );
    $alt_names_raw  = get_post_meta( $pid, '_bc_loc_alt_names', true );
    $alt_names      = is_array( $alt_names_raw ) ? $alt_names_raw : ( $alt_names_raw ? json_decode( $alt_names_raw, true ) : array() );
    $alias_of       = get_post_meta( $pid, '_bc_loc_alias_of', true );

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
    $confidence_labels = array(
      'high'   => 'Alta',
      'medium' => 'Media',
      'low'    => 'Baja',
    );
    $refs = $scriptures ? ( is_array( $scriptures ) ? $scriptures : json_decode( $scriptures, true ) ) : array();
    $ref_count = is_array( $refs ) ? count( $refs ) : 0;

    $high_relevance_ids = include get_theme_file_path( '/inc/high-relevance-locations.php' );
    $relevancia = 1; // Baja por defecto
    if ( in_array( $pid, $high_relevance_ids, true ) ) {
      $relevancia = 3; // Alta
    } else {
      $t = $type;
      $rc = $ref_count;
      if ( $rc >= 6 ) {
        $relevancia = 3;
      } elseif ( $rc >= 3 && in_array( $t, array( 'region', 'sea', 'river', 'mountain', 'wilderness' ), true ) ) {
        $relevancia = 3;
      } elseif ( $rc >= 2 && in_array( $t, array( 'city', 'settlement', 'landmark' ), true ) ) {
        $relevancia = 2;
      } elseif ( $rc >= 1 && in_array( $t, array( 'region', 'sea', 'river', 'mountain', 'wilderness', 'valley' ), true ) ) {
        $relevancia = 2;
      } elseif ( $rc >= 1 && $t === 'landmark' ) {
        $relevancia = 2;
      }
    }

    $relevancia_labels = array(
      1 => 'Baja',
      2 => 'Media',
      3 => 'Alta',
    );

    $en_to_es = array(
      'Acts' => 'Hechos', 'Genesis' => 'Génesis', 'Exodus' => 'Éxodo',
      'Leviticus' => 'Levítico', 'Numbers' => 'Números', 'Deuteronomy' => 'Deuteronomio',
      'Joshua' => 'Josué', 'Judges' => 'Jueces', 'Ruth' => 'Rut',
      'Samuel' => 'Samuel', 'Kings' => 'Reyes', 'Chronicles' => 'Crónicas',
      'Ezra' => 'Esdras', 'Nehemiah' => 'Nehemías', 'Esther' => 'Ester',
      'Job' => 'Job', 'Psalms' => 'Salmos', 'Proverbs' => 'Proverbios',
      'Ecclesiastes' => 'Eclesiastés', 'Song of Solomon' => 'Cantares',
      'Isaiah' => 'Isaías', 'Jeremiah' => 'Jeremías',
      'Lamentations' => 'Lamentaciones', 'Ezekiel' => 'Ezequiel',
      'Daniel' => 'Daniel', 'Hosea' => 'Oseas', 'Joel' => 'Joel',
      'Amos' => 'Amós', 'Obadiah' => 'Abdías', 'Jonah' => 'Jonás',
      'Micah' => 'Miqueas', 'Nahum' => 'Nahúm', 'Habakkuk' => 'Habacuc',
      'Zephaniah' => 'Sofonías', 'Haggai' => 'Hageo', 'Zechariah' => 'Zacarías',
      'Malachi' => 'Malaquías', 'Matthew' => 'Mateo', 'Mark' => 'Marcos',
      'Luke' => 'Lucas', 'John' => 'Juan', 'Romans' => 'Romanos',
      'Corinthians' => 'Corintios', 'Galatians' => 'Gálatas',
      'Ephesians' => 'Efesios', 'Philippians' => 'Filipenses',
      'Colossians' => 'Colosenses', 'Thessalonians' => 'Tesalonicenses',
      'Timothy' => 'Timoteo', 'Titus' => 'Tito', 'Philemon' => 'Filemón',
      'Hebrews' => 'Hebreos', 'James' => 'Santiago', 'Peter' => 'Pedro',
      'Jude' => 'Judas', 'Revelation' => 'Apocalipsis',
    );
    $translate_ref = function ( $ref ) use ( $en_to_es ) {
      $ref = is_string( $ref ) ? $ref : ( $ref['ref'] ?? '' );
      return strtr( $ref, $en_to_es );
    };
  ?>  

  <?php
  global $wpdb;
  $title = get_the_title();
  $homonimos = $wpdb->get_results( $wpdb->prepare(
    "SELECT ID, post_title, post_name FROM {$wpdb->posts}
     WHERE post_type = 'bc_location' AND post_status = 'publish'
     AND post_title = %s AND ID != %d LIMIT 10",
    $title, $pid
  ) );

  $alt_rendered = array();
  if ( $alt_names ) {
    foreach ( $alt_names as $name ) {
      $alt_id = $wpdb->get_var( $wpdb->prepare(
        "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'bc_location' AND post_status = 'publish' AND post_title = %s AND ID != %d LIMIT 2",
        $name, $pid
      ) );
      if ( $alt_id ) {
        $alt_rendered[] = '<a href="' . esc_url( get_permalink( $alt_id ) ) . '">' . esc_html( $name ) . '</a>';
      } else {
        $alt_rendered[] = esc_html( $name );
      }
    }
  }
  ?>

  <nav class="bc-glossary-back-nav">
    <a href="<?php echo esc_url( get_post_type_archive_link( 'bc_location' ) ); ?>">&larr; Volver al glosario</a>
  </nav>

  <?php if ( $alias_of ) :
    $alias_title = get_the_title( $alias_of );
    $alias_url   = get_permalink( $alias_of );
    if ( $alias_title && $alias_url ) : ?>
    <div class="bc-section-constrained">
      <div class="bc-location-alias-of">
        <span class="bc-alias-of-icon">&larr;</span>
        Nombre alternativo de <a href="<?php echo esc_url( $alias_url ); ?>"><?php echo esc_html( $alias_title ); ?></a>
      </div>
    </div>
  <?php endif; endif; ?>

  <div class="bc-location-hero">
    <div class="bc-location-hero-inner">
      <h1 class="bc-location-title" itemprop="name"><?php the_title(); ?></h1>
      <div class="bc-location-hero-meta">
        <?php if ( $disambiguation ) : ?>
          <span class="bc-location-disambig"><?php echo esc_html( $disambiguation ); ?></span>
        <?php endif; ?>
        <?php if ( $type && isset( $type_labels[ $type ] ) ) : ?>
          <span class="bc-location-type-badge"><i class="fas <?php echo esc_attr( $type_icons[ $type ] ?? 'fa-map-marker-alt' ); ?>"></i><?php echo esc_html( $type_labels[ $type ] ); ?></span>
        <?php endif; ?>
        <?php if ( $alt_names ) : ?>
          <span class="bc-location-alt-badge">
            <?php foreach ( $alt_rendered as $i => $html ) :
              if ( $i > 0 ) : ?><span class="bc-alt-sep">·</span><?php endif;
              echo $html;
            endforeach; ?>
          </span>
        <?php endif; ?>
        <?php if ( $name_en ) : ?>
          <meta itemprop="alternateName" content="<?php echo esc_attr( $name_en ); ?>">
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="bc-location-key-facts">
    <span class="bc-key-fact">
      <i class="fas fa-map-marker-alt"></i>
      <?php echo esc_html( $type_labels[ $type ] ?? 'Ubicación' ); ?>
    </span>
    <?php if ( $name_en ) : ?>
      <span class="bc-key-fact">
        <i class="fas fa-globe"></i>
        <?php echo esc_html( $name_en ); ?>
      </span>
    <?php endif; ?>
    <span class="bc-key-fact">
      <i class="fas fa-star"></i>
      Relevancia: <?php echo esc_html( $relevancia_labels[ $relevancia ] ); ?>
    </span>
    <span class="bc-key-fact">
      <i class="fas fa-book-open"></i>
      <?php echo esc_html( $ref_count ); ?> referencia<?php echo $ref_count === 1 ? '' : 's'; ?> escritural<?php echo $ref_count === 1 ? '' : 'es'; ?>
    </span>
  </div>

  <?php if ( $homonimos ) : ?>
  <div class="bc-section-constrained">
    <div class="bc-location-other-meanings">
      <h2 class="bc-other-meanings-heading">Otras acepciones</h2>
      <p class="bc-other-meanings-intro">El término &laquo;<?php echo esc_html( $title ); ?>&raquo; también designa a:</p>
      <ul class="bc-other-meanings-list">
        <?php foreach ( $homonimos as $h ) :
          $h_disambig = get_post_meta( $h->ID, '_bc_loc_disambiguation', true );
          $h_permalink = get_permalink( $h->ID );
        ?>
          <li>
            <a href="<?php echo esc_url( $h_permalink ); ?>"><?php echo esc_html( $h->post_title ); ?></a>
            <?php if ( $h_disambig ) : ?>
              <span class="bc-other-meanings-disambig">(<?php echo esc_html( $h_disambig ); ?>)</span>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
  <?php endif; ?>

    <div class="bc-location-container" itemscope itemtype="https://schema.org/Place">
      <div class="bc-location-content-area">

        <article class="bc-location-card">

          <?php if ( has_post_thumbnail() ) : ?>
            <div class="bc-location-image" itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
              <?php the_post_thumbnail( 'large', array( 'class' => '', 'alt' => get_the_title(), 'decoding' => 'async' ) ); ?>
              <meta itemprop="url" content="<?php echo esc_url( get_the_post_thumbnail_url( $pid, 'large' ) ); ?>">
            </div>
          <?php endif; ?>

          <?php
          $raw_content = get_the_content();
          if ( $raw_content ) :
            $content = apply_filters( 'the_content', $raw_content );
            $first_h2 = strpos( $content, '<h2' );
            if ( false !== $first_h2 ) {
              $intro   = substr( $content, 0, $first_h2 );
              $rest    = substr( $content, $first_h2 );
            } else {
              $intro   = $content;
              $rest    = '';
            }
          ?>
            <div class="bc-location-content" itemprop="description">
              <?php echo $intro; ?>
              <?php if ( $lat && $lng ) : ?>
                <div class="bc-location-map-inline"><?php echo bc_scripture_map_render_single( $pid ); ?></div>
              <?php endif; ?>
              <?php echo $rest; ?>
              <?php if ( $relevancia >= 2 ) :
                $faq_raw = get_post_meta( $pid, '_bc_loc_faq', true );
                $faqs = is_array( $faq_raw ) ? $faq_raw : ( $faq_raw ? json_decode( $faq_raw, true ) : array() );
                if ( ! empty( $faqs ) ) :
                  $faq_jsonld = array( '@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => array() );
                ?>
                <div class="bc-faq" itemscope itemtype="https://schema.org/FAQPage">
                  <h2>Preguntas frecuentes</h2>
                  <?php foreach ( $faqs as $faq ) :
                    $faq_jsonld['mainEntity'][] = array( '@type' => 'Question', 'name' => $faq['q'], 'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $faq['a'] ) );
                  ?>
                    <details itemprop="mainEntity" itemscope itemtype="https://schema.org/Question">
                      <summary itemprop="name"><?php echo esc_html( $faq['q'] ); ?></summary>
                      <div itemscope itemtype="https://schema.org/Answer" itemprop="acceptedAnswer">
                        <p itemprop="text"><?php echo esc_html( $faq['a'] ); ?></p>
                      </div>
                    </details>
                  <?php endforeach; ?>
                </div>
                <script type="application/ld+json">
                <?php echo json_encode( $faq_jsonld, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ); ?>
                </script>
              <?php endif; ?>
            </div>
          <?php endif; ?>



          <?php if ( $lat && $lng ) : ?>
            <meta itemprop="geo" itemscope itemtype="https://schema.org/GeoCoordinates">
            <meta itemprop="latitude" content="<?php echo esc_attr( $lat ); ?>">
            <meta itemprop="longitude" content="<?php echo esc_attr( $lng ); ?>">
          <?php endif; ?>
        </article>
        <?php endif; ?> <!-- cierre de $raw_content -->

      </div>

      <aside class="bc-location-sidebar">
        <div class="bc-infobox">
          <h3 class="bc-infobox-title">Información</h3>
          <ul class="bc-infobox-list">
            <?php if ( $type && isset( $type_labels[ $type ] ) ) : ?>
              <li>
                <i class="fas <?php echo esc_attr( $type_icons[ $type ] ?? 'fa-map-marker-alt' ); ?>"></i>
                <span><?php echo esc_html( $type_labels[ $type ] ); ?></span>
              </li>
            <?php endif; ?>
            <?php if ( $alt_names ) : ?>
              <li>
                <i class="fas fa-tag"></i>
                <span>También conocido como:
                  <?php foreach ( $alt_rendered as $i => $html ) :
                    if ( $i > 0 ) : ?><span class="bc-infobox-alt-sep">, </span><?php endif;
                    echo $html;
                  endforeach; ?>
                </span>
              </li>
            <?php endif; ?>
            <?php if ( $relevancia && isset( $relevancia_labels[ $relevancia ] ) ) : ?>
              <li>
                <i class="fas fa-star"></i>
                <span>Relevancia: <span class="bc-infobox-relevancia-<?php echo esc_attr( $relevancia === 3 ? 'high' : ( $relevancia === 2 ? 'medium' : 'low' ) ); ?>"><?php echo esc_html( $relevancia_labels[ $relevancia ] ); ?></span></span>
              </li>
            <?php endif; ?>
            <?php if ( $confidence && isset( $confidence_labels[ $confidence ] ) ) : ?>
              <li>
                <i class="fas fa-check-circle"></i>
                <span>Confianza: <span class="bc-infobox-confidence-<?php echo esc_attr( $confidence ); ?>"><?php echo esc_html( $confidence_labels[ $confidence ] ); ?></span></span>
              </li>
            <?php endif; ?>
            <?php if ( $source ) : ?>
              <li>
                <i class="fas fa-database"></i>
                <span>Fuente: <?php echo esc_html( $source ); ?></span>
              </li>
            <?php endif; ?>
            <?php if ( $date_from ) : ?>
              <li>
                <i class="fas fa-calendar-alt"></i>
                <span>~<?php echo (int) $date_from; ?><?php echo $date_to ? '–' . (int) $date_to : ''; ?></span>
              </li>
            <?php endif; ?>
            <?php if ( $lat && $lng ) : ?>
              <li>
                <i class="fas fa-map-pin"></i>
                <span class="bc-infobox-coords"><?php echo esc_html( $lat ); ?>, <?php echo esc_html( $lng ); ?>
                  <br>
                   <a href="https://www.google.com/maps?q=<?php echo esc_attr( $lat ); ?>,<?php echo esc_attr( $lng ); ?>" target="_blank" rel="noopener noreferrer">Ver en Google Maps →</a>
                </span>
              </li>
            <?php endif; ?>
            <?php if ( ! empty( $refs ) ) : ?>
              <li>
                <i class="fas fa-book-open"></i>
                <span>Referencia de ejemplo: <em><?php echo esc_html( $translate_ref( $refs[0] ) ); ?></em></span>
              </li>
            <?php endif; ?>
          </ul>
        </div>
      </aside>
    </div>

  <?php endwhile; ?>
</main>

<?php get_footer(); ?>
