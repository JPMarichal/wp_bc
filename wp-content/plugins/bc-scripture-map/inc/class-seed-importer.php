<?php

function bc_scripture_map_import_seed_data() {
	$imported = get_option( 'bc_scripture_map_seed_imported', false );
	if ( $imported ) {
		return;
	}

	bc_scripture_map_import_openbible();
	bc_scripture_map_import_church_history();

	update_option( 'bc_scripture_map_seed_imported', true );
}

function bc_scripture_map_import_openbible() {
	$file = BC_SCRIPTURE_MAP_DIR . 'data/openbible-places.tsv';
	if ( ! file_exists( $file ) ) {
		return;
	}

	$lines = file( $file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
	if ( ! $lines ) {
		return;
	}

	array_shift( $lines );

	$count = 0;
	foreach ( $lines as $line ) {
		$parts = explode( "\t", $line );
		if ( count( $parts ) < 4 ) {
			continue;
		}

		$esv_name  = trim( $parts[0] );
		$kmz_name  = trim( $parts[1] );
		$lat_str   = trim( $parts[2] );
		$lon_str   = trim( $parts[3] );
		$passages  = isset( $parts[4] ) ? trim( $parts[4] ) : '';
		$comment   = isset( $parts[5] ) ? trim( $parts[5] ) : '';

		if ( ! $esv_name || ! $passages ) {
			continue;
		}

		$lat = bc_scripture_map_parse_coord( $lat_str );
		$lng = bc_scripture_map_parse_coord( $lon_str );
		if ( null === $lat || null === $lng ) {
			continue;
		}

		$name = $kmz_name ?: $esv_name;
		$slug = sanitize_title( 'openbible-' . $name . '-' . $esv_name );

		$existing = get_posts( array(
			'post_type'      => 'bc_location',
			'name'           => $slug,
			'posts_per_page' => 1,
			'fields'         => 'ids',
		) );

		if ( ! empty( $existing ) ) {
			continue;
		}

		$refs = bc_scripture_map_parse_passages( $passages );
		$detected_type = bc_scripture_map_detect_type( $comment, $name );

		$post_id = wp_insert_post( array(
			'post_title'  => $name,
			'post_name'   => $slug,
			'post_type'   => 'bc_location',
			'post_status' => 'publish',
		) );

		if ( is_wp_error( $post_id ) ) {
			continue;
		}

		update_post_meta( $post_id, '_bc_loc_lat', $lat );
		update_post_meta( $post_id, '_bc_loc_lng', $lng );
		update_post_meta( $post_id, '_bc_loc_type', $detected_type );
		update_post_meta( $post_id, '_bc_loc_icon', 'default' );
		update_post_meta( $post_id, '_bc_loc_source', 'openbible' );
		update_post_meta( $post_id, '_bc_loc_confidence', 'medium' );
		update_post_meta( $post_id, '_bc_loc_description', $comment );

		if ( ! empty( $refs ) ) {
			update_post_meta( $post_id, '_bc_loc_scriptures', wp_json_encode( $refs ) );
		}

		$count++;
	}
}

function bc_scripture_map_import_church_history() {
	$file = BC_SCRIPTURE_MAP_DIR . 'data/church-history.json';
	if ( ! file_exists( $file ) ) {
		return;
	}

	$json = file_get_contents( $file );
	$sites = json_decode( $json, true );
	if ( ! $sites ) {
		return;
	}

	foreach ( $sites as $site ) {
		$slug = sanitize_title( 'church-history-' . $site['name'] );

		$existing = get_posts( array(
			'post_type'      => 'bc_location',
			'name'           => $slug,
			'posts_per_page' => 1,
			'fields'         => 'ids',
		) );

		if ( ! empty( $existing ) ) {
			continue;
		}

		$post_id = wp_insert_post( array(
			'post_title'   => $site['name'],
			'post_name'    => $slug,
			'post_type'    => 'bc_location',
			'post_status'  => 'publish',
			'post_content' => isset( $site['description'] ) ? $site['description'] : '',
		) );

		if ( is_wp_error( $post_id ) ) {
			continue;
		}

		update_post_meta( $post_id, '_bc_loc_lat', (float) $site['lat'] );
		update_post_meta( $post_id, '_bc_loc_lng', (float) $site['lng'] );
		update_post_meta( $post_id, '_bc_loc_type', isset( $site['type'] ) ? $site['type'] : 'settlement' );
		update_post_meta( $post_id, '_bc_loc_icon', isset( $site['icon'] ) ? $site['icon'] : 'temple' );
		update_post_meta( $post_id, '_bc_loc_source', 'church-history' );
		update_post_meta( $post_id, '_bc_loc_confidence', 'high' );

		if ( isset( $site['scriptures'] ) ) {
			update_post_meta( $post_id, '_bc_loc_scriptures', wp_json_encode( $site['scriptures'] ) );
		}
		if ( isset( $site['dateFrom'] ) ) {
			update_post_meta( $post_id, '_bc_loc_date_from', (int) $site['dateFrom'] );
		}
		if ( isset( $site['dateTo'] ) ) {
			update_post_meta( $post_id, '_bc_loc_date_to', (int) $site['dateTo'] );
		}
	}
}

add_action( 'admin_init', 'bc_scripture_map_import_seed_data' );

function bc_scripture_map_parse_coord( $str ) {
	$str = trim( $str );
	if ( '?' === $str || '' === $str ) {
		return null;
	}
	$str = ltrim( $str, '~' );
	if ( '' === $str ) {
		return null;
	}
	return (float) $str;
}

function bc_scripture_map_parse_passages( $passages ) {
	$refs   = array();
	$parts  = preg_split( '/[,;]\s*/', $passages );
	$parts  = array_slice( $parts, 0, 10 );

	foreach ( $parts as $part ) {
		$refs[] = array( 'ref' => trim( $part ) );
	}

	return $refs;
}

function bc_scripture_map_detect_type( $comment, $name ) {
	$lower = strtolower( $comment . ' ' . $name );
	if ( preg_match( '/\b(sea|water|lake)\b/i', $lower ) ) {
		return 'sea';
	}
	if ( preg_match( '/\b(river|wadi|brook|stream)\b/i', $lower ) ) {
		return 'river';
	}
	if ( preg_match( '/\b(mountain|mount|hill)\b/i', $lower ) ) {
		return 'mountain';
	}
	if ( preg_match( '/\b(region|wilderness|desert|valley|plain)\b/i', $lower ) ) {
		return 'region';
	}
	if ( preg_match( '/\b(island)\b/i', $lower ) ) {
		return 'region';
	}
	return 'city';
}
