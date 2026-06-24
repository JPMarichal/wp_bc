<?php

define( 'BC_IMPORT_BATCH_SIZE', 50 );

add_action( 'admin_notices', 'bc_scripture_map_import_notice' );
add_action( 'admin_post_bc_import_batch', 'bc_scripture_map_handle_import_batch' );
add_action( 'admin_post_bc_import_reset', 'bc_scripture_map_handle_import_reset' );

function bc_scripture_map_import_notice() {
	$screen = get_current_screen();
	if ( ! $screen || 'plugins' !== $screen->parent_base ) {
		return;
	}

	$ob_total  = bc_scripture_map_count_tsv();
	$ch_total  = bc_scripture_map_count_json();
	$ob_offset = (int) get_option( 'bc_scripture_map_ob_offset', 0 );
	$ch_offset = (int) get_option( 'bc_scripture_map_ch_offset', 0 );

	$remaining = ( $ob_total - $ob_offset ) + ( $ch_total - $ch_offset );

	if ( $remaining <= 0 ) {
		echo '<div class="notice notice-success"><p>';
		echo '✅ <strong>bc-scripture-map:</strong> Todas las ubicaciones importadas. ';
		echo '<a href="' . esc_url( admin_url( 'admin-post.php?action=bc_import_reset' ) ) . '">Reimportar</a>';
		echo '</p></div>';
		return;
	}

	$ob_done = min( $ob_offset, $ob_total );
	$ch_done = min( $ch_offset, $ch_total );

	echo '<div class="notice notice-warning is-dismissible"><p>';
	echo '🗺️ <strong>bc-scripture-map:</strong> Importando ubicaciones… ';
	echo "OpenBible: {$ob_done}/{$ob_total} | Iglesia: {$ch_done}/{$ch_total} | Restan: {$remaining}. ";
	echo '<a class="button button-primary" href="' . esc_url( admin_url( 'admin-post.php?action=bc_import_batch' ) ) . '">Continuar importación</a>';
	echo '</p></div>';
}

function bc_scripture_map_handle_import_batch() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Permiso denegado' );
	}

	$ob_total  = bc_scripture_map_count_tsv();
	$ob_offset = (int) get_option( 'bc_scripture_map_ob_offset', 0 );
	$ch_total  = bc_scripture_map_count_json();
	$ch_offset = (int) get_option( 'bc_scripture_map_ch_offset', 0 );

	if ( $ob_offset < $ob_total ) {
		$imported = bc_scripture_map_import_openbible_batch( $ob_offset, BC_IMPORT_BATCH_SIZE );
		update_option( 'bc_scripture_map_ob_offset', $ob_offset + $imported, false );
	} elseif ( $ch_offset < $ch_total ) {
		$imported = bc_scripture_map_import_church_history_batch( $ch_offset, BC_IMPORT_BATCH_SIZE );
		update_option( 'bc_scripture_map_ch_offset', $ch_offset + $imported, false );
	}

	wp_safe_redirect( wp_get_referer() ?: admin_url() );
	exit;
}

function bc_scripture_map_handle_import_reset() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Permiso denegado' );
	}

	delete_option( 'bc_scripture_map_ob_offset' );
	delete_option( 'bc_scripture_map_ch_offset' );

	$posts = get_posts( array(
		'post_type'      => 'bc_location',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	) );
	foreach ( $posts as $id ) {
		wp_delete_post( $id, true );
	}

	wp_safe_redirect( wp_get_referer() ?: admin_url() );
	exit;
}

function bc_scripture_map_count_tsv() {
	$file = BC_SCRIPTURE_MAP_DIR . 'data/openbible-places.tsv';
	if ( ! file_exists( $file ) ) {
		return 0;
	}
	$lines = file( $file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
	return $lines ? max( 0, count( $lines ) - 1 ) : 0;
}

function bc_scripture_map_count_json() {
	$file = BC_SCRIPTURE_MAP_DIR . 'data/church-history.json';
	if ( ! file_exists( $file ) ) {
		return 0;
	}
	$json = file_get_contents( $file );
	$data = json_decode( $json, true );
	return is_array( $data ) ? count( $data ) : 0;
}

function bc_scripture_map_import_openbible_batch( $offset, $limit ) {
	$file = BC_SCRIPTURE_MAP_DIR . 'data/openbible-places.tsv';
	if ( ! file_exists( $file ) ) {
		return 0;
	}

	$lines = file( $file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
	if ( ! $lines ) {
		return 0;
	}

	array_shift( $lines );

	$chunk  = array_slice( $lines, $offset, $limit );
	$count  = 0;

	foreach ( $chunk as $line ) {
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

		$lat = (float) $lat_str;
		$lng = (float) $lon_str;

		$name = $kmz_name ?: $esv_name;
		$slug = sanitize_title( 'openbible-' . $name . '-' . $esv_name );

		$existing = get_posts( array(
			'post_type'      => 'bc_location',
			'name'           => $slug,
			'posts_per_page' => 1,
			'fields'         => 'ids',
		) );

		if ( ! empty( $existing ) ) {
			$count++;
			continue;
		}

		$refs          = bc_scripture_map_parse_passages( $passages );
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

	return $count;
}

function bc_scripture_map_import_church_history_batch( $offset, $limit ) {
	$file = BC_SCRIPTURE_MAP_DIR . 'data/church-history.json';
	if ( ! file_exists( $file ) ) {
		return 0;
	}

	$json = file_get_contents( $file );
	$sites = json_decode( $json, true );
	if ( ! $sites ) {
		return 0;
	}

	$chunk = array_slice( $sites, $offset, $limit );
	$count = 0;

	foreach ( $chunk as $site ) {
		$slug = sanitize_title( 'church-history-' . $site['name'] );

		$existing = get_posts( array(
			'post_type'      => 'bc_location',
			'name'           => $slug,
			'posts_per_page' => 1,
			'fields'         => 'ids',
		) );

		if ( ! empty( $existing ) ) {
			$count++;
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

		$count++;
	}

	return $count;
}

function bc_scripture_map_parse_passages( $passages ) {
	$refs  = array();
	$parts = preg_split( '/[,;]\s*/', $passages );
	$parts = array_slice( $parts, 0, 10 );

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
