<?php
/**
 * Поддержка темы, меню, подключение стилей и скриптов, <head>.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', [ 'script', 'style', 'gallery', 'caption' ] );

	register_nav_menus( [
		'menu-header' => 'Шапка — основное меню',
		'menu-mobile' => 'Мобильное меню',
		'menu-footer' => 'Подвал — правовые документы',
	] );
} );

// Страницы редактируются полями SCF — блочный редактор не нужен.
add_filter( 'use_block_editor_for_post_type', function ( $use, $type ) {
	return 'page' === $type ? false : $use;
}, 10, 2 );

// У страниц с шаблонами весь текст в полях — прячем пустой редактор.
add_action( 'admin_init', function () {
	$id = isset( $_GET['post'] ) ? (int) $_GET['post'] : 0;
	if ( ! $id ) {
		return;
	}
	$tpl = get_page_template_slug( $id );
	if ( (int) get_option( 'page_on_front' ) === $id || in_array( $tpl, [ 'templates/page-services.php', 'templates/page-service.php', 'templates/page-contacts.php' ], true ) ) {
		remove_post_type_support( 'page', 'editor' );
	}
} );

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'c24-style', C24_URI . '/assets/css/style.css', [], C24_VER );
	if ( ! is_front_page() ) {
		wp_enqueue_style( 'c24-service', C24_URI . '/assets/css/service.css', [ 'c24-style' ], C24_VER );
	}
	wp_enqueue_style( 'c24-wp', C24_URI . '/assets/css/wp.css', [ 'c24-style' ], C24_VER );

	wp_enqueue_script( 'c24-main', C24_URI . '/assets/js/main.js', [], C24_VER, [ 'strategy' => 'defer', 'in_footer' => false ] );
	wp_localize_script( 'c24-main', 'C24', c24_js_data() );

	// Блочные стили не используются.
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'global-styles' );
	wp_dequeue_style( 'classic-theme-styles' );
}, 20 );

add_action( 'wp_head', function () {
	foreach ( [ 'inter-tight-latin-wght-normal', 'inter-latin-wght-normal' ] as $font ) {
		echo '<link rel="preload" as="font" type="font/woff2" href="' . esc_url( C24_URI . '/assets/fonts/' . $font . '.woff2' ) . '" crossorigin>' . "\n";
	}
	c24_hero_preload();
	echo '<meta name="theme-color" content="#F6F6F2">' . "\n";
	if ( ! has_site_icon() ) {
		echo '<link rel="icon" type="image/png" sizes="32x32" href="' . esc_url( c24_asset( 'img/favicon-32.png' ) ) . '">' . "\n";
		echo '<link rel="icon" type="image/png" sizes="192x192" href="' . esc_url( c24_asset( 'img/favicon-192.png' ) ) . '">' . "\n";
		echo '<link rel="apple-touch-icon" href="' . esc_url( c24_asset( 'img/apple-touch-icon.png' ) ) . '">' . "\n";
	}
}, 2 );

// Чистим <head>.
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', 'rest_output_link_wp_head' );
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
add_filter( 'wp_img_tag_add_auto_sizes', '__return_false' );

// Классы на <body> не нужны вёрстке — оставляем только служебные.
add_filter( 'body_class', function ( $classes ) {
	return array_values( array_intersect( $classes, [ 'admin-bar', 'logged-in' ] ) );
} );
