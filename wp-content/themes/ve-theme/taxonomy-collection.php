<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_term = get_queried_object();
$is_collection = $current_term && (int) $current_term->parent === 0;

// Breadcrumb data
if ( $current_term && ! $is_collection ) {
	$collection_term = get_term( (int) $current_term->parent, 'collection' );
} else {
	$collection_term = $current_term;
}

// All series in this collection for navigation
$all_series = array();
if ( $collection_term ) {
	$all_series = get_terms( array(
		'taxonomy'   => 'collection',
		'hide_empty' => true,
		'parent'     => $collection_term->term_id,
		'orderby'    => 'term_id',
		'order'      => 'ASC',
	) );
	if ( is_wp_error( $all_series ) ) {
		$all_series = array();
	}
}

// Find current series index for prev/next
$current_index = -1;
if ( ! $is_collection && ! empty( $all_series ) ) {
	foreach ( $all_series as $i => $s ) {
		if ( (int) $s->term_id === (int) $current_term->term_id ) {
			$current_index = $i;
			break;
		}
	}
}
$prev_series = $current_index > 0 ? $all_series[ $current_index - 1 ] : null;
$next_series = $current_index >= 0 && $current_index < count( $all_series ) - 1 ? $all_series[ $current_index + 1 ] : null;

add_filter( 'generate_sidebar_layout', function ( $layout ) {
	return 'no-sidebar';
} );

add_action( 'wp_head', function () { ?>
<style>
.bc-collection-archive .site.grid-container,
.bc-collection-archive .grid-container {
	max-width: 100%;
}
.bc-collection-archive .site-content {
	padding: 0;
	width: 100%;
}
.bc-collection-archive .grid-container {
	padding-left: 20px;
	padding-right: 20px;
}
@media (min-width: 1200px) {
	.bc-collection-archive .grid-container {
		padding-left: 40px;
		padding-right: 40px;
	}
}

.bc-collection-archive .page-header { margin-bottom: 0; }

.bc-coll-breadcrumb {
	font-size: .85rem;
	margin-bottom: .5rem;
}
.bc-coll-breadcrumb a,
.bc-coll-breadcrumb a:visited {
	color: #2d5a27;
	text-decoration: underline;
}
.bc-coll-breadcrumb a:hover {
	color: #e65100;
}
.bc-coll-breadcrumb .bc-coll-breadcrumb-sep {
	color: #aaa;
	margin: 0 .35rem;
}
.bc-coll-breadcrumb .bc-coll-breadcrumb-current {
	color: #666;
}

.bc-coll-header { margin-bottom: 2rem; }
.bc-coll-title {
	color: #2d5a27;
	font-size: 1.6rem;
	margin-bottom: .25rem;
}

/* Series navigation strip */
.bc-series-nav {
	background: #f9f6f0;
	border: 1px solid #e0d9ce;
	border-radius: 8px;
	margin-bottom: 1.5rem;
	padding: 1rem;
}
.bc-series-nav-label {
	color: #2d5a27;
	font-size: .85rem;
	font-weight: 600;
	margin-bottom: .5rem;
	text-transform: uppercase;
	letter-spacing: .03em;
}
.bc-series-nav-list {
	display: flex;
	flex-wrap: wrap;
	gap: .35rem;
	list-style: none;
	margin: 0;
	padding: 0;
}
.bc-series-nav-item a,
.bc-series-nav-item a:visited {
	background: #fff;
	border: 1px solid #d5cec4;
	border-radius: 5px;
	color: #3a3a3a;
	display: inline-block;
	font-size: .85rem;
	padding: .35rem .65rem;
	text-decoration: none;
	transition: background .15s, border-color .15s, color .15s;
}
.bc-series-nav-item a:hover {
	background: #2d5a27;
	border-color: #2d5a27;
	color: #fff;
}
.bc-series-nav-item--current a,
.bc-series-nav-item--current a:visited {
	background: #2d5a27;
	border-color: #2d5a27;
	color: #fff;
	font-weight: 600;
}
.bc-series-nav-item--current a:hover {
	background: #3d7a37;
	border-color: #3d7a37;
}

/* Series grid cards (collections) */
.bc-series-card,
.bc-series-card:link,
.bc-series-card:visited {
	background: #f9f6f0;
	border: 1px solid #e0d9ce;
	border-radius: 8px;
	color: #3a3a3a;
	display: block;
	padding: 1.25rem;
	text-decoration: none;
	transition: background .15s, border-color .15s, box-shadow .15s;
	height: 100%;
	width: 100%;
	cursor: pointer;
	font-family: inherit;
	font-size: inherit;
	text-align: left;
}
.bc-series-card:hover,
.bc-series-card:focus {
	background: #fff;
	border-color: #2d5a27;
	box-shadow: 0 2px 12px rgba(45,90,39,.12);
	color: #3a3a3a;
}
.bc-series-card-body { display: flex; flex-direction: column; gap: .4rem; }
.bc-series-card-icon {
	color: #2d5a27;
	font-size: 1.5rem;
	line-height: 1;
}
.bc-series-card-title {
	font-size: 1.05rem;
	font-weight: 600;
	line-height: 1.3;
	margin: 0;
}
.bc-series-card-count {
	color: #888;
	font-size: .85rem;
	margin: 0;
}

/* Posts list */
.bc-coll-posts-title {
	border-bottom: 2px solid #2d5a27;
	color: #2d5a27;
	font-size: 1.15rem;
	margin: 2rem 0 1rem;
	padding-bottom: .35rem;
}

.bc-coll-posts-list { display: flex; flex-direction: column; gap: .65rem; }

.bc-coll-post-item {
	align-items: flex-start;
	border: 1px solid #e0d9ce;
	border-radius: 8px;
	display: flex;
	gap: .75rem;
	padding: .75rem;
	transition: border-color .15s, box-shadow .15s;
}
.bc-coll-post-item:hover {
	border-color: #2d5a27;
	box-shadow: 0 2px 10px rgba(45,90,39,.08);
}

.bc-coll-post-thumb-link { flex-shrink: 0; }
.bc-coll-post-thumb {
	border-radius: 6px;
	height: auto;
	object-fit: cover;
	width: 80px;
}

.bc-coll-post-body { min-width: 0; flex: 1; }
.bc-coll-post-title {
	font-size: .92rem;
	font-weight: 600;
	line-height: 1.3;
	margin: 0 0 .25rem;
}
.bc-coll-post-title a,
.bc-coll-post-title a:visited {
	color: #3a3a3a;
	text-decoration: none;
}
.bc-coll-post-title a:hover { color: #2d5a27; text-decoration: underline; }

.bc-coll-post-body :where(p) {
	color: #666;
	font-size: .82rem;
	line-height: 1.45;
	margin: 0 0 .35rem;
}

.bc-coll-post-meta { font-size: .78rem; color: #888; }
.bc-coll-post-tag,
.bc-coll-post-tag:visited {
	color: #2d5a27;
	text-decoration: underline;
}
.bc-coll-post-tag:hover { color: #e65100; }

/* Pagination */
.bc-coll-pagination {
	margin: 1.5rem 0;
	text-align: center;
}
.bc-coll-pagination .page-numbers {
	background: #f9f6f0;
	border: 1px solid #e0d9ce;
	border-radius: 5px;
	color: #3a3a3a;
	display: inline-block;
	font-size: .88rem;
	padding: .35rem .65rem;
	text-decoration: none;
	transition: background .15s, border-color .15s, color .15s;
}
.bc-coll-pagination .page-numbers:hover {
	background: #2d5a27;
	border-color: #2d5a27;
	color: #fff;
}
.bc-coll-pagination .page-numbers.current {
	background: #2d5a27;
	border-color: #2d5a27;
	color: #fff;
	font-weight: 600;
}
.bc-coll-pagination .page-numbers.dots {
	background: transparent;
	border-color: transparent;
}

/* Prev/next series navigation */
.bc-series-prev-next {
	border-top: 1px solid #e0d9ce;
	display: flex;
	gap: 1rem;
	justify-content: space-between;
	margin-top: 2rem;
	padding-top: 1rem;
}
.bc-series-prev-next a,
.bc-series-prev-next a:visited {
	color: #2d5a27;
	font-size: .9rem;
	text-decoration: underline;
}
.bc-series-prev-next a:hover {
	color: #e65100;
}

@media (max-width: 575.98px) {
	.bc-coll-title { font-size: 1.3rem; }
	.bc-series-card { padding: 1rem; }
	.bc-series-card-title { font-size: .95rem; }
	.bc-series-nav-item a { font-size: .8rem; padding: .25rem .5rem; }
}

/* Selected card state */
.bc-series-card--selected,
.bc-series-card--selected:link,
.bc-series-card--selected:visited {
	background: #2d5a27;
	border-color: #2d5a27;
	color: #fff;
	box-shadow: 0 2px 12px rgba(45,90,39,.25);
}
.bc-series-card--selected .bc-series-card-icon,
.bc-series-card--selected .bc-series-card-title,
.bc-series-card--selected .bc-series-card-count {
	color: #fff;
}
.bc-series-card--selected:hover,
.bc-series-card--selected:focus {
	background: #3d7a37;
	border-color: #3d7a37;
	color: #fff;
}
/* Reset filter button */
.bc-series-filter-reset {
	background: transparent;
	border: 2px dashed #d5cec4;
	border-radius: 8px;
	color: #888;
	cursor: pointer;
	display: flex;
	flex-direction: column;
	align-items: center;
	justify-content: center;
	padding: 1.25rem;
	height: 100%;
	text-align: center;
	transition: background .15s, border-color .15s, color .15s;
	font-size: .85rem;
}
.bc-series-filter-reset:hover {
	border-color: #2d5a27;
	color: #2d5a27;
}
.bc-series-filter-reset-icon {
	font-size: 1.5rem;
	margin-bottom: .25rem;
}
/* Active filter indicator */
.bc-series-active {
	background: #f0f7ef;
	border: 1px solid #2d5a27;
	border-radius: 6px;
	display: none;
	margin-top: .75rem;
	padding: .5rem .75rem;
	font-size: .85rem;
	color: #2d5a27;
	align-items: center;
	gap: .5rem;
}
.bc-series-active .bc-series-active-close {
	color: #2d5a27;
	cursor: pointer;
	font-weight: 700;
	margin-left: auto;
	text-decoration: none;
}
.bc-series-active .bc-series-active-close:hover {
	color: #e65100;
}
</style>
<?php }, 20 );

get_header(); ?>

<div class="bc-collection-archive">
	<div class="grid-container">

		<nav class="bc-coll-breadcrumb" aria-label="Breadcrumb">
			<a href="<?php echo esc_url( home_url( '/colecciones/' ) ); ?>">Colecciones</a>
			<span class="bc-coll-breadcrumb-sep">›</span>
			<?php if ( $collection_term ) : ?>
				<a href="<?php echo esc_url( get_term_link( $collection_term ) ); ?>">
					<?php echo esc_html( $collection_term->name ); ?>
				</a>
				<?php if ( ! $is_collection ) : ?>
					<span class="bc-coll-breadcrumb-sep">›</span>
					<span class="bc-coll-breadcrumb-current"><?php echo esc_html( $current_term->name ); ?></span>
				<?php else : ?>
					<span class="bc-coll-breadcrumb-sep bc-coll-breadcrumb-sep--filterable" style="display:none;">›</span>
					<span class="bc-coll-breadcrumb-current bc-coll-breadcrumb-series" style="display:none;"></span>
				<?php endif; ?>
			<?php else : ?>
				<span class="bc-coll-breadcrumb-current"><?php echo esc_html( $current_term->name ); ?></span>
			<?php endif; ?>
		</nav>

		<header class="bc-coll-header">
			<h1 class="bc-coll-title"><?php echo esc_html( $current_term->name ); ?></h1>
		</header>

		<?php if ( $is_collection ) : ?>

			<?php if ( ! empty( $all_series ) ) : ?>
				<div class="bc-series-filter-cards">
					<div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 row-cols-xxl-5 g-3">
						<?php foreach ( $all_series as $s ) : ?>
							<div class="col">
								<button type="button" class="bc-series-card bc-series-filter-trigger" data-series-id="<?php echo (int) $s->term_id; ?>" aria-pressed="false">
									<div class="bc-series-card-body">
										<div class="bc-series-card-icon">
											<i class="fas fa-book-open"></i>
										</div>
										<h2 class="bc-series-card-title"><?php echo esc_html( $s->name ); ?></h2>
										<p class="bc-series-card-count">
											<?php printf( _n( '%d artículo', '%d artículos', $s->count, 've-theme' ), $s->count ); ?>
										</p>
									</div>
								</button>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>

			<div class="bc-series-active" role="status">
				<span><i class="fas fa-filter"></i> <span class="bc-series-active-label"></span></span>
				<a class="bc-series-active-close" role="button" tabindex="0" aria-label="Quitar filtro">&times;</a>
			</div>

		<?php else : ?>

			<?php if ( ! empty( $all_series ) && count( $all_series ) > 1 ) : ?>
				<nav class="bc-series-nav" aria-label="Series en esta colección">
					<p class="bc-series-nav-label">Series en <?php echo esc_html( $collection_term->name ); ?></p>
					<ul class="bc-series-nav-list">
						<?php foreach ( $all_series as $s ) :
							$is_current = (int) $s->term_id === (int) $current_term->term_id; ?>
							<li class="bc-series-nav-item<?php echo $is_current ? ' bc-series-nav-item--current' : ''; ?>">
								<a href="<?php echo esc_url( get_term_link( $s ) ); ?>">
									<?php echo esc_html( $s->name ); ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</nav>
			<?php endif; ?>

		<?php endif; ?>

		<?php if ( have_posts() ) : ?>

			<h2 class="bc-coll-posts-title">
				<?php if ( $is_collection ) : ?>
					<span class="bc-coll-posts-title-all"><?php printf( __( 'Todos los artículos de la colección %s', 've-theme' ), $current_term->name ); ?></span>
					<span class="bc-coll-posts-title-filtered" style="display:none;"><?php printf( __( 'Artículos de la serie %s', 've-theme' ), '<span class="bc-coll-series-name"></span>' ); ?></span>
				<?php else : ?>
					<?php _e( 'Artículos', 've-theme' ); ?>
				<?php endif; ?>
			</h2>

			<div class="bc-coll-posts-list">
				<?php while ( have_posts() ) : the_post();
					$post_series = wp_get_post_terms( get_the_ID(), 'collection', array( 'fields' => 'ids' ) );
					$post_series_ids = ! empty( $post_series ) && ! is_wp_error( $post_series ) ? $post_series : array();
					$post_series_ids = array_map( 'intval', $post_series_ids );
				?>
					<article <?php post_class( 'bc-coll-post-item' ); ?> data-series-ids="<?php echo esc_attr( implode( ',', $post_series_ids ) ); ?>">
						<?php if ( has_post_thumbnail() ) : ?>
							<a href="<?php the_permalink(); ?>" class="bc-coll-post-thumb-link">
								<?php echo wp_get_attachment_image( get_post_thumbnail_id(), 'thumbnail', false, array( 'class' => 'bc-coll-post-thumb', 'loading' => 'lazy', 'decoding' => 'async' ) ); ?>
							</a>
						<?php endif; ?>
						<div class="bc-coll-post-body">
							<h3 class="bc-coll-post-title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h3>
							<?php the_excerpt(); ?>
							<div class="bc-coll-post-meta">
								<?php
								$tags = get_the_tags();
								if ( ! empty( $tags ) ) {
									$tag_links = array_map( function ( $t ) {
										return '<a href="' . esc_url( get_tag_link( $t ) ) . '" class="bc-coll-post-tag">' . esc_html( $t->name ) . '</a>';
									}, $tags );
									echo '<span class="bc-coll-post-tags"><i class="fas fa-tags"></i> ' . implode( ', ', $tag_links ) . '</span>';
								}
								?>
							</div>
						</div>
					</article>
				<?php endwhile; ?>
			</div>

			<?php if ( $wp_query->max_num_pages > 1 ) : ?>
			<div class="bc-coll-pagination">
				<?php
				echo paginate_links( array(
					'total'     => $wp_query->max_num_pages,
					'current'   => max( 1, get_query_var( 'paged' ) ),
					'mid_size'  => 2,
					'prev_text' => '&laquo; Anterior',
					'next_text' => 'Siguiente &raquo;',
				) );
				?>
			</div>
			<?php endif; ?>

		<?php endif; ?>

		<?php if ( ! $is_collection && ( $prev_series || $next_series ) ) : ?>
			<nav class="bc-series-prev-next">
				<div>
					<?php if ( $prev_series ) : ?>
						<a href="<?php echo esc_url( get_term_link( $prev_series ) ); ?>">
							<i class="fas fa-arrow-left"></i> <?php echo esc_html( $prev_series->name ); ?>
						</a>
					<?php endif; ?>
				</div>
				<div style="text-align:right;">
					<?php if ( $next_series ) : ?>
						<a href="<?php echo esc_url( get_term_link( $next_series ) ); ?>">
							<?php echo esc_html( $next_series->name ); ?> <i class="fas fa-arrow-right"></i>
						</a>
					<?php endif; ?>
				</div>
			</nav>
		<?php endif; ?>

	</div>
</div>

<?php if ( $is_collection && ! empty( $all_series ) ) : ?>
<script>
(function() {
	var cards = document.querySelectorAll('.bc-series-filter-trigger');
	var posts = document.querySelectorAll('.bc-coll-post-item');
	var titleAll = document.querySelector('.bc-coll-posts-title-all');
	var titleFiltered = document.querySelector('.bc-coll-posts-title-filtered');
	var seriesName = document.querySelector('.bc-coll-series-name');
	var activeBar = document.querySelector('.bc-series-active');
	var activeLabel = document.querySelector('.bc-series-active-label');
	var activeClose = document.querySelector('.bc-series-active-close');
	var breadcrumbSep = document.querySelector('.bc-coll-breadcrumb-sep--filterable');
	var breadcrumbSeries = document.querySelector('.bc-coll-breadcrumb-series');
	var currentSeriesId = null;

	function filterPosts(seriesId) {
		var showAll = seriesId === null;
		for (var i = 0; i < posts.length; i++) {
			var post = posts[i];
			var ids = post.getAttribute('data-series-ids');
			if (showAll) {
				post.style.display = '';
			} else {
				var match = ids && (',' + ids + ',').indexOf(',' + seriesId + ',') !== -1;
				post.style.display = match ? '' : 'none';
			}
		}
	}

	function updateCards(seriesId) {
		for (var i = 0; i < cards.length; i++) {
			var card = cards[i];
			var sid = parseInt(card.getAttribute('data-series-id'), 10);
			if (sid === seriesId) {
				card.classList.add('bc-series-card--selected');
				card.setAttribute('aria-pressed', 'true');
			} else {
				card.classList.remove('bc-series-card--selected');
				card.setAttribute('aria-pressed', 'false');
			}
		}
	}

	function updateTitle(seriesId) {
		if (seriesId === null) {
			titleAll.style.display = '';
			titleFiltered.style.display = 'none';
		} else {
			for (var i = 0; i < cards.length; i++) {
				if (parseInt(cards[i].getAttribute('data-series-id'), 10) === seriesId) {
					seriesName.textContent = cards[i].querySelector('.bc-series-card-title').textContent;
					break;
				}
			}
			titleAll.style.display = 'none';
			titleFiltered.style.display = '';
		}
	}

	function updateActiveBar(seriesId) {
		if (seriesId === null) {
			activeBar.style.display = 'none';
			if (breadcrumbSep) breadcrumbSep.style.display = 'none';
			if (breadcrumbSeries) breadcrumbSeries.style.display = 'none';
		} else {
			var name = '';
			for (var i = 0; i < cards.length; i++) {
				if (parseInt(cards[i].getAttribute('data-series-id'), 10) === seriesId) {
					name = cards[i].querySelector('.bc-series-card-title').textContent;
					break;
				}
			}
			activeLabel.textContent = 'Filtrando: ' + name;
			activeBar.style.display = 'flex';
			if (breadcrumbSep) breadcrumbSep.style.display = '';
			if (breadcrumbSeries) {
				breadcrumbSeries.textContent = name;
				breadcrumbSeries.style.display = '';
			}
		}
	}

	function applyFilter(seriesId) {
		currentSeriesId = seriesId;
		updateCards(seriesId);
		updateTitle(seriesId);
		updateActiveBar(seriesId);
		filterPosts(seriesId);
	}

	function resetFilter() {
		applyFilter(null);
	}

	for (var i = 0; i < cards.length; i++) {
		cards[i].addEventListener('click', function() {
			var sid = parseInt(this.getAttribute('data-series-id'), 10);
			if (sid === currentSeriesId) {
				resetFilter();
			} else {
				applyFilter(sid);
			}
		});
	}

	if (activeClose) {
		activeClose.addEventListener('click', resetFilter);
		activeClose.addEventListener('keydown', function(e) {
			if (e.key === 'Enter' || e.key === ' ') {
				e.preventDefault();
				resetFilter();
			}
		});
	}
})();
</script>
<?php endif; ?>
<?php
get_footer();
