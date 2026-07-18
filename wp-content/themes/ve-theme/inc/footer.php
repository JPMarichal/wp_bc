<?php

add_action( 'widgets_init', function () {
  register_sidebar( array(
    'id'            => 'footer-col-1',
    'name'          => 'Footer Columna 1',
    'description'   => 'Primera columna del footer.',
    'before_widget' => '<div class="bc-footer-widget %2$s">',
    'after_widget'  => '</div>',
    'before_title'  => '<h4 class="bc-footer-widget-title">',
    'after_title'   => '</h4>',
  ) );
  register_sidebar( array(
    'id'            => 'footer-col-2',
    'name'          => 'Footer Columna 2',
    'description'   => 'Segunda columna del footer.',
    'before_widget' => '<div class="bc-footer-widget %2$s">',
    'after_widget'  => '</div>',
    'before_title'  => '<h4 class="bc-footer-widget-title">',
    'after_title'   => '</h4>',
  ) );
  register_sidebar( array(
    'id'            => 'footer-col-3',
    'name'          => 'Footer Columna 3',
    'description'   => 'Tercera columna del footer.',
    'before_widget' => '<div class="bc-footer-widget %2$s">',
    'after_widget'  => '</div>',
    'before_title'  => '<h4 class="bc-footer-widget-title">',
    'after_title'   => '</h4>',
  ) );
} );
