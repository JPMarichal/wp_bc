<?php
/**
 * ve-theme functions and definitions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GENERATE_VERSION', '3.6.1' );

if ( ! function_exists( 'generate_setup' ) ) {
	add_action( 'after_setup_theme', 'generate_setup' );
	function generate_setup() {
		load_theme_textdomain( 'generatepress' );

		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'post-formats', array( 'aside', 'image', 'video', 'quote', 'link', 'status' ) );
		add_theme_support( 'woocommerce' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'script', 'style' ) );
		add_theme_support( 'customize-selective-refresh-widgets' );
		add_theme_support( 'align-wide' );
		add_theme_support( 'responsive-embeds' );

		$color_palette = generate_get_editor_color_palette();

		if ( ! empty( $color_palette ) ) {
			add_theme_support( 'editor-color-palette', $color_palette );
		}

		add_theme_support(
			'custom-logo',
			array(
				'height'      => 70,
				'width'       => 350,
				'flex-height' => true,
				'flex-width'  => true,
			)
		);

		register_nav_menus(
			array(
				'primary' => __( 'Primary Menu', 'generatepress' ),
			)
		);

		global $content_width;
		if ( ! isset( $content_width ) ) {
			$content_width = 1200;
		}

		add_theme_support( 'editor-styles' );

		$editor_styles = apply_filters(
			'generate_editor_styles',
			array(
				'assets/css/admin/block-editor.css',
			)
		);

		add_editor_style( $editor_styles );
	}
}

$theme_dir = get_template_directory();

require $theme_dir . '/inc/theme-functions.php';
require $theme_dir . '/inc/defaults.php';
require $theme_dir . '/inc/class-css.php';
require $theme_dir . '/inc/css-output.php';
require $theme_dir . '/inc/general.php';
require $theme_dir . '/inc/customizer.php';
require $theme_dir . '/inc/markup.php';
require $theme_dir . '/inc/typography.php';
require $theme_dir . '/inc/plugin-compat.php';
require $theme_dir . '/inc/block-editor.php';
require $theme_dir . '/inc/class-typography.php';
require $theme_dir . '/inc/class-typography-migration.php';
require $theme_dir . '/inc/class-html-attributes.php';
require $theme_dir . '/inc/class-theme-update.php';
require $theme_dir . '/inc/class-rest.php';
require $theme_dir . '/inc/deprecated.php';

if ( is_admin() ) {
	require $theme_dir . '/inc/meta-box.php';
	require $theme_dir . '/inc/class-dashboard.php';
}

require $theme_dir . '/inc/structure/archives.php';
require $theme_dir . '/inc/structure/comments.php';
require $theme_dir . '/inc/structure/featured-images.php';
require $theme_dir . '/inc/structure/footer.php';
require $theme_dir . '/inc/structure/header.php';
require $theme_dir . '/inc/structure/navigation.php';
require $theme_dir . '/inc/structure/post-meta.php';
require $theme_dir . '/inc/structure/sidebars.php';
require $theme_dir . '/inc/structure/search-modal.php';

require_once __DIR__ . '/inc/cleanup.php';
require_once __DIR__ . '/inc/enqueue.php';
require_once __DIR__ . '/inc/performance.php';
require_once __DIR__ . '/inc/setup.php';
require_once __DIR__ . '/inc/og-tags.php';
require_once __DIR__ . '/inc/auto-links.php';
require_once __DIR__ . '/inc/breadcrumbs.php';
require_once __DIR__ . '/inc/related-posts.php';
require_once __DIR__ . '/inc/schema.php';
require_once __DIR__ . '/inc/share-bar.php';
require_once __DIR__ . '/inc/toc.php';
require_once __DIR__ . '/inc/glossary.php';
require_once __DIR__ . '/inc/persona.php';
require_once __DIR__ . '/inc/media.php';
require_once __DIR__ . '/inc/front-page.php';
require_once __DIR__ . '/inc/editor-tools.php';
require_once __DIR__ . '/inc/series-widget.php';
require_once __DIR__ . '/inc/taxonomy-temas.php';
require_once __DIR__ . '/inc/sidebar-widgets.php';
require_once __DIR__ . '/inc/scripture-taxonomy.php';
require_once __DIR__ . '/inc/post-navigation.php';
require_once __DIR__ . '/inc/author-box.php';
require_once __DIR__ . '/inc/layout.php';
require_once __DIR__ . '/inc/sidebar-metabox.php';
require_once __DIR__ . '/inc/footer.php';
require_once __DIR__ . '/inc/block-styles.php';
require_once __DIR__ . '/inc/admin-columns.php';
require_once __DIR__ . '/inc/archive-layout.php';
require_once __DIR__ . '/inc/location-redirect.php';
require_once __DIR__ . '/inc/header.php';
require_once __DIR__ . '/inc/block-patterns.php';
require_once __DIR__ . '/inc/external-links.php';
