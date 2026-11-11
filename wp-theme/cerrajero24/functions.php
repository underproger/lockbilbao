<?php
/**
 * Cerrajero24 — функции темы.
 */

defined( 'ABSPATH' ) || exit;

define( 'C24_VER', '1.0.0' );
define( 'C24_DIR', get_template_directory() );
define( 'C24_URI', get_template_directory_uri() );

// Без SCF сайт не должен падать на фронте: поля просто пустые.
if ( ! function_exists( 'get_field' ) ) {
	function get_field( $selector, $post_id = false, $format = true ) {
		return null;
	}
}

add_action( 'admin_notices', function () {
	$need = [];
	if ( ! class_exists( 'ACF' ) ) {
		$need[] = 'Secure Custom Fields';
	}
	if ( ! function_exists( 'pll_current_language' ) ) {
		$need[] = 'Polylang Pro';
	}
	if ( ! defined( 'WPCF7_VERSION' ) ) {
		$need[] = 'Contact Form 7';
	}
	if ( $need ) {
		echo '<div class="notice notice-warning"><p><b>Cerrajero24:</b> активируйте плагины — ' . esc_html( implode( ', ', $need ) ) . '.</p></div>';
	}
} );

require C24_DIR . '/inc/helpers.php';
require C24_DIR . '/inc/setup.php';
require C24_DIR . '/inc/acf.php';
require C24_DIR . '/inc/polylang.php';
require C24_DIR . '/inc/cf7.php';
require C24_DIR . '/inc/seo.php';
require C24_DIR . '/inc/import.php';
