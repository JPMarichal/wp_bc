<?php

function bc_scripture_map_register_location_cpt() {
	$labels = array(
		'name'               => __( 'Ubicaciones', 'bc-scripture-map' ),
		'singular_name'      => __( 'Ubicación', 'bc-scripture-map' ),
		'add_new'            => __( 'Añadir Nueva', 'bc-scripture-map' ),
		'add_new_item'       => __( 'Añadir Nueva Ubicación', 'bc-scripture-map' ),
		'edit_item'          => __( 'Editar Ubicación', 'bc-scripture-map' ),
		'view_item'          => __( 'Ver Ubicación', 'bc-scripture-map' ),
		'view_items'         => __( 'Ver Ubicaciones', 'bc-scripture-map' ),
		'search_items'       => __( 'Buscar Ubicaciones', 'bc-scripture-map' ),
		'not_found'          => __( 'No se encontraron ubicaciones', 'bc-scripture-map' ),
		'not_found_in_trash' => __( 'No hay ubicaciones en la papelera', 'bc-scripture-map' ),
		'all_items'          => __( 'Todas las Ubicaciones', 'bc-scripture-map' ),
		'archives'           => __( 'Glosario de Ubicaciones', 'bc-scripture-map' ),
		'items_list'         => __( 'Lista de ubicaciones', 'bc-scripture-map' ),
		'item_published'     => __( 'Ubicación publicada.', 'bc-scripture-map' ),
		'item_updated'       => __( 'Ubicación actualizada.', 'bc-scripture-map' ),
	);

	register_post_type( 'bc_location', array(
		'labels'          => $labels,
		'public'          => true,
		'has_archive'     => 'ubicaciones',
		'rewrite'         => array( 'slug' => 'ubicacion' ),
		'publicly_queryable' => true,
		'show_ui'         => true,
		'show_in_menu'    => true,
		'show_in_nav_menus' => true,
		'menu_icon'       => 'dashicons-location-alt',
		'supports'        => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
		'show_in_rest'    => true,
		'rest_base'       => 'bc-locations',
		'menu_position'   => 26,
	) );

	$metas = array(
		'_bc_loc_lat'          => array( 'type' => 'number', 'description' => 'Latitud' ),
		'_bc_loc_lng'          => array( 'type' => 'number', 'description' => 'Longitud' ),
		'_bc_loc_type'         => array( 'type' => 'string', 'description' => 'Tipo: city, region, wilderness, sea, river, mountain, settlement, landmark' ),
		'_bc_loc_icon'         => array( 'type' => 'string', 'description' => 'Icono del marcador' ),
		'_bc_loc_scriptures'   => array( 'type' => 'string', 'description' => 'Referencias escriturales (JSON)' ),
		'_bc_loc_description'  => array( 'type' => 'string', 'description' => 'Descripción breve' ),
		'_bc_loc_date_from'    => array( 'type' => 'integer', 'description' => 'Año inicial (aprox.)' ),
		'_bc_loc_date_to'      => array( 'type' => 'integer', 'description' => 'Año final (aprox.)' ),
		'_bc_loc_source'       => array( 'type' => 'string', 'description' => 'Fuente: openbible, church-history, manual' ),
		'_bc_loc_confidence'   => array( 'type' => 'string', 'description' => 'Confianza: high, medium, low' ),
		'_bc_loc_order'        => array( 'type' => 'integer', 'description' => 'Orden cronológico' ),
		'_bc_loc_name_en'      => array( 'type' => 'string', 'description' => 'Nombre en inglés' ),
		'_bc_loc_disambiguation' => array( 'type' => 'string', 'description' => 'Desambiguación: contexto para homónimos (ej: Siria, Pisidia, América)' ),
		'_bc_loc_alt_names'      => array( 'type' => 'string', 'description' => 'Nombres alternativos (JSON array de strings: ["Jebús","Salem"])' ),
		'_bc_loc_alias_of'       => array( 'type' => 'integer', 'description' => 'ID de la ubicación principal si esta es un nombre alternativo de otra (ej: Jebús → Jerusalén)' ),
	);

	foreach ( $metas as $key => $args ) {
		$default = ( $args['type'] === 'integer' || $args['type'] === 'number' ) ? 0 : '';
		register_post_meta( 'bc_location', $key, array(
			'type'         => $args['type'],
			'description'  => $args['description'],
			'single'       => true,
			'default'      => $default,
			'show_in_rest' => true,
		) );
	}
}
add_action( 'init', 'bc_scripture_map_register_location_cpt' );
