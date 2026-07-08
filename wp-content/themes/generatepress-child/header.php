<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?> <?php generate_do_microdata( 'body' ); ?>>
  <?php
  do_action( 'wp_body_open' );
  do_action( 'generate_before_header' );
  ?>

  <header class="bc-header">
    <?php get_template_part( 'template-parts/header-brand' ); ?>
    <?php get_template_part( 'template-parts/header-nav' ); ?>
  </header>

  <?php do_action( 'generate_after_header' ); ?>

  <div <?php generate_do_attr( 'page' ); ?>>
    <?php do_action( 'generate_inside_site_container' ); ?>
    <div <?php generate_do_attr( 'site-content' ); ?>>
      <?php do_action( 'generate_inside_container' ); ?>
