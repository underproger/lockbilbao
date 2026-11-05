<?php
/**
 * Polylang Pro: строки интерфейса темы.
 * Все строки c24__( '…' ) из файлов темы автоматически появляются в «Языки → Перевод строк» (группа Cerrajero24).
 * Исходный язык строк — испанский.
 */

defined( 'ABSPATH' ) || exit;

/** Все строки темы: литералы c24__( '…' ) из PHP-файлов + строки, которые передаются переменными. */
function c24_theme_strings() {
	$cache = get_transient( 'c24_strings_' . C24_VER );
	if ( is_array( $cache ) && ! ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ) {
		return $cache;
	}
	$strings = [];
	$files   = array_merge( glob( C24_DIR . '/*.php' ), glob( C24_DIR . '/inc/*.php' ), glob( C24_DIR . '/template-parts/*.php' ), glob( C24_DIR . '/templates/*.php' ) );
	foreach ( $files as $file ) {
		$src = (string) file_get_contents( $file );
		if ( preg_match_all( "/c24__\(\s*'((?:[^'\\\\]|\\\\.)*)'\s*\)/u", $src, $m ) ) {
			foreach ( $m[1] as $s ) {
				$strings[] = stripslashes( $s );
			}
		}
	}
	foreach ( c24_areas() as $g ) {
		$strings[] = $g[0];
	}
	$strings[] = 'Revise los campos del formulario.';
	$strings   = array_values( array_unique( $strings ) );
	set_transient( 'c24_strings_' . C24_VER, $strings, DAY_IN_SECONDS );
	return $strings;
}

add_action( 'init', function () {
	if ( ! function_exists( 'pll_register_string' ) || ! is_admin() ) {
		return;
	}
	foreach ( c24_theme_strings() as $s ) {
		pll_register_string( mb_substr( $s, 0, 60 ), $s, 'Cerrajero24', mb_strlen( $s ) > 70 );
	}
} );
