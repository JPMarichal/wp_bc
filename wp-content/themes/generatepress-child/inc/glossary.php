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

add_action( 'after_setup_theme', function () {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$menu_name = 'Menú Principal';
	$menu      = wp_get_nav_menu_object( $menu_name );

	if ( ! $menu ) {
		$menu_id = wp_create_nav_menu( $menu_name );

		wp_update_nav_menu_item( $menu_id, 0, array(
			'menu-item-title'  => 'Inicio',
			'menu-item-url'    => home_url( '/' ),
			'menu-item-status' => 'publish',
		) );

		$glosarios_id = wp_update_nav_menu_item( $menu_id, 0, array(
			'menu-item-title'  => 'Glosarios',
			'menu-item-url'    => '#',
			'menu-item-status' => 'publish',
		) );

		wp_update_nav_menu_item( $menu_id, 0, array(
			'menu-item-title'    => 'Personas',
			'menu-item-url'      => get_post_type_archive_link( 'bc_quote_author' ),
			'menu-item-status'   => 'publish',
			'menu-item-parent-id' => $glosarios_id,
		) );

		wp_update_nav_menu_item( $menu_id, 0, array(
			'menu-item-title'    => 'Temas',
			'menu-item-url'      => home_url( '/glosario/temas/' ),
			'menu-item-status'   => 'publish',
			'menu-item-parent-id' => $glosarios_id,
		) );

		wp_update_nav_menu_item( $menu_id, 0, array(
			'menu-item-title'    => 'Ubicaciones',
			'menu-item-url'      => home_url( '/ubicaciones/' ),
			'menu-item-status'   => 'publish',
			'menu-item-parent-id' => $glosarios_id,
		) );

		$locations = get_theme_mod( 'nav_menu_locations', array() );
		$locations['primary'] = $menu_id;
		set_theme_mod( 'nav_menu_locations', $locations );
		return;
	}

	$menu_items = wp_get_nav_menu_items( $menu->term_id );
	$glosarios_parent_id = 0;
	$temas_exists = false;
	$ubicaciones_exists = false;

	foreach ( $menu_items as $item ) {
		if ( $item->title === 'Glosarios' && (int) $item->menu_item_parent === 0 ) {
			$glosarios_parent_id = $item->ID;
		}
		if ( $item->title === 'Temas' && (int) $item->menu_item_parent === $glosarios_parent_id ) {
			$temas_exists = true;
		}
		if ( $item->title === 'Ubicaciones' && (int) $item->menu_item_parent === $glosarios_parent_id ) {
			$ubicaciones_exists = true;
		}
	}

	if ( $glosarios_parent_id > 0 && ! $temas_exists ) {
		wp_update_nav_menu_item( $menu->term_id, 0, array(
			'menu-item-title'    => 'Temas',
			'menu-item-url'      => home_url( '/glosario/temas/' ),
			'menu-item-status'   => 'publish',
			'menu-item-parent-id' => $glosarios_parent_id,
		) );
	}

	if ( $glosarios_parent_id > 0 && ! $ubicaciones_exists ) {
		wp_update_nav_menu_item( $menu->term_id, 0, array(
			'menu-item-title'    => 'Ubicaciones',
			'menu-item-url'      => home_url( '/ubicaciones/' ),
			'menu-item-status'   => 'publish',
			'menu-item-parent-id' => $glosarios_parent_id,
		) );
	}
} );
