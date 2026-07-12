<?php
/**
 * Plugin Name: Mapa de Escrituras
 * Description: Mapa interactivo con relieve 3D para ubicaciones de las Escrituras (Biblia, DyC, Perla de Gran Precio).
 * Version: 1.0.0
 * Requires at least: 6.1
 * Requires PHP: 7.4
 * License: GPL-2.0-or-later
 * Text Domain: bc-scripture-map
 */

defined( 'ABSPATH' ) || exit;

define( 'BC_SCRIPTURE_MAP_VERSION', '1.0.0' );
define( 'BC_SCRIPTURE_MAP_DIR', plugin_dir_path( __FILE__ ) );
define( 'BC_SCRIPTURE_MAP_URL', plugin_dir_url( __FILE__ ) );

require_once BC_SCRIPTURE_MAP_DIR . 'inc/class-location-cpt.php';
require_once BC_SCRIPTURE_MAP_DIR . 'inc/class-rest.php';
require_once BC_SCRIPTURE_MAP_DIR . 'inc/class-seed-importer.php';

function bc_scripture_map_block_init() {
	register_block_type( BC_SCRIPTURE_MAP_DIR . 'block.json', array(
		'render_callback' => 'bc_scripture_map_render',
	) );
}
add_action( 'init', 'bc_scripture_map_block_init' );

function bc_scripture_map_render( $attributes ) {
	$location_ids = isset( $attributes['locationIds'] ) ? array_map( 'intval', $attributes['locationIds'] ) : array();
	$map_title    = isset( $attributes['mapTitle'] ) ? esc_attr( $attributes['mapTitle'] ) : '';

	$locations = array();
	if ( ! empty( $location_ids ) ) {
		$posts = get_posts( array(
			'post_type'      => 'bc_location',
			'post__in'       => $location_ids,
			'posts_per_page' => count( $location_ids ),
			'orderby'        => 'post__in',
		) );

		foreach ( $posts as $post ) {
			$locations[] = array(
				'id'            => $post->ID,
				'title'         => get_the_title( $post ),
				'lat'           => (float) get_post_meta( $post->ID, '_bc_loc_lat', true ),
				'lng'           => (float) get_post_meta( $post->ID, '_bc_loc_lng', true ),
				'type'          => get_post_meta( $post->ID, '_bc_loc_type', true ),
				'icon'          => get_post_meta( $post->ID, '_bc_loc_icon', true ),
				'scriptures'    => json_decode( get_post_meta( $post->ID, '_bc_loc_scriptures', true ), true ) ?: array(),
				'description'   => get_post_meta( $post->ID, '_bc_loc_description', true ),
				'dateFrom'      => (int) get_post_meta( $post->ID, '_bc_loc_date_from', true ),
				'dateTo'        => (int) get_post_meta( $post->ID, '_bc_loc_date_to', true ),
				'source'        => get_post_meta( $post->ID, '_bc_loc_source', true ),
			);
		}
	}

	$wrapper_attributes = get_block_wrapper_attributes( array(
		'class' => 'bc-scripture-map-container',
	) );

	$data = array(
		'centerLng'    => $attributes['centerLng'],
		'centerLat'    => $attributes['centerLat'],
		'zoom'         => $attributes['zoom'],
		'pitch'        => $attributes['pitch'],
		'bearing'      => $attributes['bearing'],
		'exaggeration' => $attributes['exaggeration'],
		'height'       => $attributes['height'],
		'locations'    => $locations,
		'routes'       => $attributes['routes'],
		'regions'      => $attributes['regions'],
		'showLabels'   => $attributes['showLabels'],
		'tileProvider' => $attributes['tileProvider'],
		'mapTitle'     => $map_title,
	);

	$data_json = wp_json_encode( $data );

	$title_html = $map_title ? sprintf( '<h3 class="bc-scripture-map-title">%s</h3>', esc_html( $map_title ) ) : '';

	return sprintf(
		'<div %s>%s<div class="bc-scripture-map-inner" data-map="%s" style="height:%dpx"></div></div>',
		$wrapper_attributes,
		$title_html,
		esc_attr( $data_json ),
		(int) $attributes['height']
	);
}

function bc_scripture_map_enqueue_frontend() {
	if ( is_admin() ) {
		return;
	}
	if ( has_block( 'bc/scripture-map' ) || is_singular( 'bc_location' ) ) {
		$asset = include BC_SCRIPTURE_MAP_DIR . 'build/frontend.asset.php';
		wp_enqueue_style(
			'maplibre-gl',
			'https://unpkg.com/maplibre-gl@4.7.1/dist/maplibre-gl.css',
			array(),
			'4.7.1'
		);
		wp_enqueue_script(
			'bc-scripture-map-frontend',
			BC_SCRIPTURE_MAP_URL . 'build/frontend.js',
			$asset['dependencies'],
			$asset['version'],
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'bc_scripture_map_enqueue_frontend' );

function bc_scripture_map_render_single( $post_id ) {
	$lat = get_post_meta( $post_id, '_bc_loc_lat', true );
	$lng = get_post_meta( $post_id, '_bc_loc_lng', true );
	if ( ! $lat || ! $lng ) {
		return '';
	}

	$location = array(
		'id'    => $post_id,
		'title' => get_the_title( $post_id ),
		'lat'   => (float) $lat,
		'lng'   => (float) $lng,
		'type'  => get_post_meta( $post_id, '_bc_loc_type', true ),
	);

	$type_zoom = array(
		'city'       => 8,
		'region'     => 7,
		'wilderness' => 8,
		'sea'        => 7,
		'river'      => 9,
		'mountain'   => 8,
		'settlement' => 9,
		'landmark'   => 9,
	);
	$location_type = get_post_meta( $post_id, '_bc_loc_type', true );
	$zoom = isset( $type_zoom[ $location_type ] ) ? $type_zoom[ $location_type ] : 10;

	$data = array(
		'centerLng'    => (float) $lng,
		'centerLat'    => (float) $lat,
		'zoom'         => $zoom,
		'pitch'        => 50,
		'bearing'      => 0,
		'exaggeration' => 1.5,
		'height'       => 400,
		'locations'    => array( $location ),
		'routes'       => array(),
		'regions'      => array(),
		'showLabels'   => true,
		'tileProvider' => 'satellite',
		'mapTitle'     => '',
	);

	return sprintf(
		'<div class="bc-location-map"><div class="bc-scripture-map-inner" data-map="%s" style="height:400px;border-radius:8px;overflow:hidden"></div></div>',
		esc_attr( wp_json_encode( $data ) )
	);
}

function bc_scripture_map_activation() {
	bc_scripture_map_register_location_cpt();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'bc_scripture_map_activation' );

function bc_scripture_map_deactivation() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'bc_scripture_map_deactivation' );
