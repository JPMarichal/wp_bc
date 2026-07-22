<?php
/**
 * Template Name: Colecciones
 */

add_filter( 'generate_sidebar_layout', function () {
	return 'no-sidebar';
} );

define( 'BC_COLL_ICON', 'fa-layer-group' );

add_action( 'wp_head', function () { ?>
<style>
.bc-coll-archive .site-content { padding: 0; width: 100%; }
.bc-coll-archive .grid-container {
	padding-left: 20px;
	padding-right: 20px;
}
@media (min-width: 1200px) {
	.bc-coll-archive .grid-container {
		padding-left: 40px;
		padding-right: 40px;
	}
}

/* Header */
.bc-coll-header { margin-bottom: 2rem; }
.bc-coll-title { margin-bottom: .25rem; }
.bc-coll-intro {
	background: #f0f7ee;
	border-left: 4px solid #2d5a27;
	border-radius: 6px;
	color: #3a3a3a;
	font-size: .92rem;
	line-height: 1.6;
	margin: 1rem 0 1.5rem;
	padding: 1rem 1.25rem;
}
.bc-coll-intro strong { color: #2d5a27; }
.bc-coll-intro p { margin: 0; }

.bc-coll-count {
	color: #666;
	font-size: .9rem;
	margin-bottom: .75rem;
}

/* Search */
.bc-coll-search-wrap { margin-bottom: 1.5rem; }
.bc-coll-search {
	border: 1px solid #d5cec4;
	border-radius: 6px;
	font-size: .95rem;
	max-width: 400px;
	padding: .55rem .75rem;
	width: 100%;
}
.bc-coll-search:focus {
	border-color: #2d5a27;
	box-shadow: 0 0 0 2px rgba(45,90,39,.15);
	outline: none;
}

/* Card grid */
.bc-coll-card {
	border: 1px solid #e0d9ce;
	border-radius: 10px;
	transition: box-shadow .2s, border-color .2s, transform .15s;
}
.bc-coll-card:hover {
	border-color: #2d5a27;
	box-shadow: 0 4px 16px rgba(45,90,39,.12);
	transform: translateY(-2px);
}
.bc-coll-card a {
	align-items: center;
	color: inherit;
	display: flex;
	gap: .65rem;
	padding: .65rem .75rem;
	text-decoration: none;
}

/* Icon */
.bc-coll-card-icon {
	align-items: center;
	background: #f0f7ee;
	border-radius: 50%;
	color: #2d5a27;
	display: flex;
	flex-shrink: 0;
	font-size: .9rem;
	height: 2.1rem;
	justify-content: center;
	width: 2.1rem;
}

/* Text block */
.bc-coll-card-text { min-width: 0; }

/* Card title */
.bc-coll-card-title {
	font-size: .88rem;
	font-weight: 600;
	line-height: 1.3;
	margin-bottom: .15rem;
}

/* Card meta */
.bc-coll-card-meta {
	color: #888;
	font-size: .78rem;
	line-height: 1.3;
}

/* Empty state */
.bc-coll-empty {
	background: #f9f6f0;
	border-radius: 8px;
	padding: 2rem;
	text-align: center;
}
.bc-coll-empty p { margin: 0; }

@media (max-width: 575.98px) {
	.bc-coll-title { font-size: 1.3rem; }
	.bc-coll-card a { padding: .55rem .65rem; gap: .55rem; }
	.bc-coll-card-icon { height: 1.8rem; width: 1.8rem; font-size: .78rem; }
	.bc-coll-card-title { font-size: .82rem; }
}
</style>
<?php }, 20 );

// --- Build data ---
$collections = get_terms( array(
	'taxonomy'   => 'collection',
	'hide_empty' => false,
	'parent'     => 0,
	'orderby'    => 'term_id',
	'order'      => 'ASC',
) );

$cards = array();
$total_series = 0;
$total_articles = 0;

if ( ! empty( $collections ) && ! is_wp_error( $collections ) ) {
	foreach ( $collections as $collection ) {
		$series = get_terms( array(
			'taxonomy'   => 'collection',
			'hide_empty' => true,
			'parent'     => $collection->term_id,
			'orderby'    => 'term_id',
			'order'      => 'ASC',
		) );
		if ( empty( $series ) || is_wp_error( $series ) ) {
			continue;
		}
		$coll_articles = 0;
		foreach ( $series as $s ) {
			$coll_articles += (int) $s->count;
		}
		$cards[] = array(
			'id'             => $collection->term_id,
			'slug'           => $collection->slug,
			'name'           => $collection->name,
			'url'            => get_term_link( $collection ),
			'series_count'   => count( $series ),
			'articles_count' => $coll_articles,
		);
		$total_series += count( $series );
		$total_articles += $coll_articles;
	}
}

get_header(); ?>

<div class="bc-coll-archive">
	<div class="grid-container">

		<header class="bc-coll-header">
			<h1 class="bc-coll-title">Colecciones</h1>

			<div class="bc-coll-intro">
				<p>
					Las <strong>colecciones</strong> agrupan series de artículos en torno a un tema general.
					Cada colección contiene una o más <strong>series</strong>, y cada serie reúne varios
					<strong>artículos</strong> que exploran un aspecto específico del tema.
				</p>
			</div>

			<p class="bc-coll-count">
				<?php printf( _n( '%d colección · %d serie · %d artículos', '%d colecciones · %d series · %d artículos', count( $cards ), 've-theme' ), count( $cards ), $total_series, $total_articles ); ?>
			</p>

			<div class="bc-coll-search-wrap">
				<input type="text" id="bc-coll-search" class="bc-coll-search" placeholder="🔍 Buscar colección…" autocomplete="off">
			</div>
		</header>

		<?php if ( ! empty( $cards ) ) : ?>

			<div id="bc-coll-list" class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3">
				<?php foreach ( $cards as $c ) : ?>
					<div class="col bc-coll-card-col" data-search="<?php echo esc_attr( mb_strtolower( $c['name'] ) ); ?>">
						<div class="bc-coll-card h-100">
							<a href="<?php echo esc_url( $c['url'] ); ?>">
								<div class="bc-coll-card-icon"><i class="fa-solid <?php echo BC_COLL_ICON; ?>"></i></div>
								<div class="bc-coll-card-text">
									<div class="bc-coll-card-title"><?php echo esc_html( $c['name'] ); ?></div>
									<div class="bc-coll-card-meta">
										<?php printf( _n( '%d serie', '%d series', $c['series_count'], 've-theme' ), $c['series_count'] ); ?>
										·
										<?php printf( _n( '%d artículo', '%d artículos', $c['articles_count'], 've-theme' ), $c['articles_count'] ); ?>
									</div>
								</div>
							</a>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

		<?php else : ?>
			<div class="bc-coll-empty">
				<p>Aún no hay colecciones.</p>
			</div>
		<?php endif; ?>

	</div>
</div>

<script>
(function() {
	var search = document.getElementById('bc-coll-search');
	if (!search) return;

	var cols = document.querySelectorAll('.bc-coll-card-col');
	var noResults = document.createElement('div');
	noResults.className = 'bc-coll-empty';
	noResults.style.display = 'none';
	noResults.innerHTML = '<p>No se encontraron colecciones con ese nombre.</p>';
	document.getElementById('bc-coll-list').after(noResults);

	search.addEventListener('input', function() {
		var q = this.value.toLowerCase().trim();
		var visible = 0;

		cols.forEach(function(col) {
			var name = col.getAttribute('data-search') || '';
			var match = q === '' || name.indexOf(q) !== -1;
			col.style.display = match ? '' : 'none';
			if (match) visible++;
		});

		noResults.style.display = visible === 0 ? '' : 'none';
	});
})();
</script>

<?php get_footer(); ?>
