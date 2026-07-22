<?php

add_action( 'pre_get_posts', function ( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( is_post_type_archive( 'bc_quote_author' ) ) {
		$query->set( 'orderby', 'title' );
		$query->set( 'order', 'ASC' );
		$query->set( 'posts_per_page', -1 );
	}
	if ( is_post_type_archive( 'bc_location' ) ) {
		$query->set( 'orderby', 'title' );
		$query->set( 'order', 'ASC' );
		$query->set( 'posts_per_page', -1 );
		$alias_ids = get_posts( array(
			'post_type'      => 'bc_location',
			'meta_key'       => '_bc_loc_alias_of',
			'fields'         => 'ids',
			'posts_per_page' => -1,
		) );
		$bare_ids = array();
		foreach ( $alias_ids as $id ) {
			if ( empty( get_post_field( 'post_content', $id ) ) ) {
				$bare_ids[] = $id;
			}
		}
		if ( ! empty( $bare_ids ) ) {
			$excluded = $query->get( 'post__not_in', array() );
			$query->set( 'post__not_in', array_merge( $excluded, $bare_ids ) );
		}
	}
} );

