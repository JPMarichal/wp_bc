<?php

add_action('init', function () {
  global $wp_taxonomies;
  if (!isset($wp_taxonomies['post_tag'])) return;

  $labels = &$wp_taxonomies['post_tag']->labels;
  $labels->name = 'Temas';
  $labels->singular_name = 'Tema';
  $labels->search_items = 'Buscar temas';
  $labels->popular_items = 'Temas populares';
  $labels->all_items = 'Todos los temas';
  $labels->parent_item = null;
  $labels->parent_item_colon = null;
  $labels->edit_item = 'Editar tema';
  $labels->view_item = 'Ver tema';
  $labels->update_item = 'Actualizar tema';
  $labels->add_new_item = 'Añadir nuevo tema';
  $labels->new_item_name = 'Nombre del tema';
  $labels->separate_items_with_commas = 'Separa los temas con comas';
  $labels->add_or_remove_items = 'Añadir o eliminar temas';
  $labels->choose_from_most_used = 'Elegir de los temas más usados';
  $labels->not_found = 'No se encontraron temas.';
  $labels->no_terms = 'No hay temas';
  $labels->items_list_navigation = 'Navegación de temas';
  $labels->items_list = 'Lista de temas';
  $labels->back_to_items = 'Volver a todos los temas';
  $labels->name_admin_bar = 'Tema';
  $labels->archives = 'Todos los temas';
  $labels->filter_by_item = 'Filtrar por tema';
  $labels->most_used = 'Temas más usados';

  $wp_taxonomies['post_tag']->label = 'Temas';
  $wp_taxonomies['post_tag']->menu_name = 'Temas';
});
