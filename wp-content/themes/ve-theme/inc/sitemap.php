<?php

add_filter( 'robots_txt', 'bc_robots_txt', 10, 2 );
function bc_robots_txt( $output, $public ) {
	if ( ! $public ) {
		return "User-agent: *\nDisallow: /\n";
	}

	$lines   = [];
	$lines[] = 'Sitemap: ' . home_url( '/sitemap.xml' );
	$lines[] = '';
	$lines[] = 'User-agent: *';
	$lines[] = 'Disallow: /wp-admin/';
	$lines[] = 'Allow: /wp-admin/admin-ajax.php';
	$lines[] = 'Crawl-delay: 10';
	$lines[] = '';
	$lines[] = 'User-agent: GPTBot';
	$lines[] = 'Disallow: /';
	$lines[] = '';
	$lines[] = 'User-agent: Claude-Web';
	$lines[] = 'Disallow: /';
	$lines[] = '';
	$lines[] = 'Sitemap: ' . home_url( '/sitemap.xml' );

	return implode( "\n", $lines ) . "\n";
}

add_action( 'init', 'bc_sitemap_rewrite' );
function bc_sitemap_rewrite() {
	add_rewrite_rule( '^sitemap\.xml$', 'index.php?bc_sitemap=index', 'top' );
	add_rewrite_rule( '^sitemap-([a-z_-]+)\.xml$', 'index.php?bc_sitemap=$matches[1]', 'top' );
}

add_filter( 'query_vars', function ( $vars ) {
	$vars[] = 'bc_sitemap';
	return $vars;
} );

add_action( 'template_redirect', 'bc_sitemap_template' );
function bc_sitemap_template() {
	$type = get_query_var( 'bc_sitemap' );
	if ( ! $type ) {
		return;
	}

	nocache_headers();
	header( 'Content-Type: application/xml; charset=utf-8' );
	echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";

	if ( 'index' === $type ) {
		bc_sitemap_index();
	} else {
		bc_sitemap_sub( $type );
	}
	exit;
}

function bc_sitemap_index() {
	$types = [
		'post'             => 'Posts',
		'page'             => 'Pages',
		'bc_location'      => 'Ubicaciones',
		'bc_quote_author'  => 'Personas',
	];
	?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	<?php foreach ( $types as $slug => $label ) : ?>
	<sitemap>
		<loc><?php echo esc_url( home_url( '/sitemap-' . $slug . '.xml' ) ); ?></loc>
		<lastmod><?php echo esc_xml( bc_sitemap_lastmod( $slug ) ); ?></lastmod>
	</sitemap>
	<?php endforeach; ?>
</sitemapindex>
	<?php
}

function bc_sitemap_sub( $post_type ) {
	$post_types = [ 'post', 'page', 'bc_location', 'bc_quote_author' ];
	if ( ! in_array( $post_type, $post_types, true ) ) {
		wp_die( 'Invalid sitemap', '', [ 'response' => 404 ] );
	}

	$posts = get_posts( [
		'post_type'      => $post_type,
		'post_status'    => 'publish',
		'posts_per_page' => 50000,
		'orderby'        => 'modified',
		'order'          => 'DESC',
	] );
	?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	<?php foreach ( $posts as $p ) : ?>
	<url>
		<loc><?php echo esc_url( get_permalink( $p ) ); ?></loc>
		<lastmod><?php echo esc_xml( $p->post_modified ); ?></lastmod>
		<priority><?php echo esc_xml( 'page' === $post_type ? '0.8' : '0.6' ); ?></priority>
	</url>
	<?php endforeach; ?>
</urlset>
	<?php
}

function bc_sitemap_lastmod( $post_type ) {
	$post = get_posts( [
		'post_type'      => $post_type,
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'orderby'        => 'modified',
		'order'          => 'DESC',
		'fields'         => 'ids',
	] );
	return ! empty( $post ) ? get_the_modified_time( 'c', $post[0] ) : gmdate( 'c' );
}
