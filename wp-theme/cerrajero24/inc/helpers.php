<?php
/**
 * Вспомогательные функции темы.
 */

defined( 'ABSPATH' ) || exit;

/* ---------------- Языки и строки ---------------- */

/** Перевод статичной строки интерфейса (Polylang → «Языки → Перевод строк»). */
function c24__( $s ) {
	return function_exists( 'pll__' ) ? pll__( $s ) : $s;
}

function c24_lang() {
	$l = function_exists( 'pll_current_language' ) ? pll_current_language() : '';
	return $l ?: 'es';
}

/* ---------------- Контакты (страница опций «Контакты») ---------------- */

function c24_opt( $name ) {
	return c24_field( $name, 'option' );
}

function c24_phone() {
	return trim( (string) c24_opt( 'phone' ) ) ?: '+34 944 123 456';
}

function c24_phone_nbsp() {
	return str_replace( ' ', '&nbsp;', esc_html( c24_phone() ) );
}

function c24_tel() {
	return 'tel:' . preg_replace( '/[^\d+]/', '', c24_phone() );
}

function c24_wa() {
	$wa = trim( (string) c24_opt( 'whatsapp' ) );
	return $wa ?: 'https://wa.me/' . preg_replace( '/\D/', '', c24_phone() );
}

function c24_email() {
	return trim( (string) c24_opt( 'email' ) ) ?: 'info@cerrajeroucr.es';
}

function c24_maps() {
	return trim( (string) c24_opt( 'maps_url' ) ) ?: 'https://www.google.com/maps/search/?api=1&query=CerrajeroUCR';
}

function c24_maps_review() {
	return trim( (string) c24_opt( 'maps_review_url' ) ) ?: c24_maps();
}

function c24_rating() {
	return trim( (string) c24_opt( 'rating' ) ) ?: '5.0';
}

function c24_reviews_count() {
	return trim( (string) c24_opt( 'reviews_count' ) ) ?: '70';
}

function c24_nif() {
	return trim( (string) c24_opt( 'nif' ) );
}

/* ---------------- Разметка ---------------- */

function c24_asset( $path ) {
	return C24_URI . '/assets/' . ltrim( $path, '/' );
}

/** Иконка из спрайта. $hidden — ставить ли aria-hidden на сам <svg>. */
function c24_icon( $id, $class = '', $hidden = true ) {
	if ( ! $id ) {
		return '';
	}
	$attr = $class ? ' class="' . esc_attr( $class ) . '"' : '';
	$attr .= $hidden ? ' aria-hidden="true"' : '';
	return '<svg' . $attr . '><use href="#' . esc_attr( $id ) . '"/></svg>';
}

/** Пять звёзд. $inner_hidden — aria-hidden на каждой звезде (когда обёртка role="img"). */
function c24_stars( $inner_hidden = true ) {
	return str_repeat( c24_icon( 'i-star', '', $inner_hidden ), 5 );
}

/**
 * Картинка из медиатеки (полный размер) с атрибутами.
 * loading не передан — атрибут не выводится (как в вёрстке).
 */
function c24_img( $id, $attrs = [] ) {
	$id = (int) $id;
	if ( ! $id ) {
		return '';
	}
	if ( ! array_key_exists( 'loading', $attrs ) ) {
		$attrs['loading'] = false;
	}
	$attrs['decoding'] = false;
	if ( ! array_key_exists( 'fetchpriority', $attrs ) ) {
		$attrs['fetchpriority'] = false;
	}
	$html = wp_get_attachment_image( $id, 'full', false, $attrs );
	// Служебные классы WP вёрстке не нужны.
	return str_replace( ' class="attachment-full size-full"', '', $html );
}

function c24_img_url( $id ) {
	$id = (int) $id;
	return $id ? (string) wp_get_attachment_image_url( $id, 'full' ) : '';
}

/** Текст из поля с разрешённой простой разметкой (акцент, перенос, ссылка). */
function c24_kses( $s ) {
	static $allowed = [
		'span'   => [ 'class' => true, 'lang' => true ],
		'b'      => [ 'class' => true ],
		'strong' => [],
		'em'     => [],
		'i'      => [],
		'br'     => [],
		'small'  => [],
		'a'      => [ 'href' => true, 'target' => true, 'rel' => true, 'class' => true ],
	];
	return wp_kses( (string) $s, $allowed );
}

/** Строки textarea → массив непустых строк. */
function c24_lines( $text ) {
	return array_values( array_filter( array_map( 'trim', preg_split( '/\R/u', (string) $text ) ), 'strlen' ) );
}

/** Многострочный текст: переносы строк → <br>. */
function c24_br( $text ) {
	return preg_replace( '/\R/u', '<br>', c24_kses( trim( (string) $text ) ) );
}

/** Номер 01, 02… из индекса цикла. */
function c24_num( $i ) {
	return sprintf( '%02d', $i + 1 );
}

/**
 * Цена «от 45 €». Пусто — null (выводится «уточняется»), «…» — цена ещё не утверждена.
 */
function c24_price( $value, $prefix = true ) {
	$value = trim( (string) $value );
	if ( '' === $value ) {
		return null;
	}
	$b = ( '…' === $value || '...' === $value ) ? '<b class="is-tbd-price">…&nbsp;€</b>' : '<b>' . esc_html( $value ) . '&nbsp;€</b>';
	if ( ! $prefix ) {
		return $b;
	}
	// «desde %s» → «от 45 €» / «from 45 €» / «45 €-tik aurrera»
	return str_replace( '%s', $b, esc_html( c24__( 'desde %s' ) ) );
}

/* ---------------- Страницы ---------------- */

/** ID страницы по ключу импорта (_c24_key) на нужном языке. */
function c24_page_id( $key, $lang = null ) {
	static $cache = [];
	if ( ! array_key_exists( $key, $cache ) ) {
		$ids           = get_posts( [
			'post_type'        => 'page',
			'post_status'      => 'publish',
			'meta_key'         => '_c24_key',
			'meta_value'       => $key,
			'fields'           => 'ids',
			'posts_per_page'   => -1,
			'lang'             => '',
			'suppress_filters' => false,
		] );
		$cache[ $key ] = $ids;
	}
	$ids  = $cache[ $key ];
	$lang = $lang ?: c24_lang();
	if ( ! $ids ) {
		return 0;
	}
	if ( function_exists( 'pll_get_post' ) ) {
		foreach ( $ids as $id ) {
			$tr = pll_get_post( $id, $lang );
			if ( $tr ) {
				return (int) $tr;
			}
		}
	}
	return (int) $ids[0];
}

function c24_page_url( $key, $lang = null ) {
	$id = c24_page_id( $key, $lang );
	return $id ? get_permalink( $id ) : c24_home();
}

function c24_home() {
	return function_exists( 'pll_home_url' ) ? pll_home_url() : home_url( '/' );
}

/** Ключ шаблона текущей страницы: front, services, service, contacts, 404, page. */
function c24_template_key() {
	if ( is_404() ) {
		return '404';
	}
	if ( is_front_page() ) {
		return 'front';
	}
	$tpl = is_page() ? get_page_template_slug() : '';
	$map = [
		'templates/page-services.php' => 'services',
		'templates/page-service.php'  => 'service',
		'templates/page-contacts.php' => 'contacts',
	];
	return $map[ $tpl ] ?? 'page';
}

/** Якоря, которые есть на текущей странице (для пунктов меню «#prices» и т.п.). */
function c24_local_anchors() {
	$map = [
		'front'    => [ 'services', 'prices', 'why', 'process', 'types', 'safety', 'reviews', 'about', 'areas', 'guide', 'faq', 'request' ],
		'services' => [ 'services', 'opening', 'repair', 'choose', 'prices', 'why', 'process', 'faq', 'request' ],
		'service'  => [ 'situations', 'prices', 'call', 'types', 'process', 'why', 'reviews', 'documents', 'areas', 'guide', 'faq', 'more', 'request' ],
		'contacts' => [ 'brief', 'areas', 'company', 'faq' ],
	];
	$list = $map[ c24_template_key() ] ?? [];
	if ( 'service' === c24_template_key() ) {
		$list[] = c24_field( 'nd_id' ) ?: 'no-damage';
	}
	return $list;
}

/** Ссылка на якорь: на этой странице — «#id», иначе — на главную. */
function c24_href( $anchor ) {
	$anchor = ltrim( $anchor, '#' );
	return in_array( $anchor, c24_local_anchors(), true ) ? '#' . $anchor : c24_home() . '#' . $anchor;
}

/** Внешняя ссылка: target/rel. */
function c24_ext() {
	return ' target="_blank" rel="noopener"';
}

/** Страницы услуг (дети страницы «Услуги») текущего языка. */
function c24_services( $exclude = 0 ) {
	$parent = c24_page_id( 'services' );
	if ( ! $parent ) {
		return [];
	}
	return get_posts( [
		'post_type'      => 'page',
		'post_parent'    => $parent,
		'post_status'    => 'publish',
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'exclude'        => $exclude ? [ $exclude ] : [],
	] );
}

/** Данные карточки услуги (вкладка «Карточка» на странице услуги). */
function c24_card( $id ) {
	return [
		'url'      => get_permalink( $id ),
		'title'    => c24_field( 'card_title', $id ) ?: get_the_title( $id ),
		'text'     => c24_field( 'card_text', $id ),
		'price'    => c24_field( 'card_price', $id ),
		'icon'     => c24_field( 'card_icon', $id ),
		'group'    => c24_field( 'card_group', $id ) ?: 'opening',
		'tag'      => c24_field( 'card_tag', $id ),
		'tags'     => c24_lines( c24_field( 'card_tags', $id ) ),
		'text_cat' => c24_field( 'card_text_catalog', $id ),
	];
}

/* ---------------- Районы и зоны выезда ---------------- */

/**
 * Районы Бильбао и муниципалитеты Гран-Бильбао.
 * Названия — официальные (кастильский / баскский), группы переводятся через Polylang.
 * [id, ES, EU, зона]
 */
function c24_areas() {
	return [
		[ 'Distritos de Bilbao', [
			[ 'abando', 'Abando', 'Abando', 1 ],
			[ 'deusto', 'Deusto', 'Deustu', 1 ],
			[ 'ibaiondo', 'Ibaiondo (Casco Viejo)', 'Ibaiondo (Alde Zaharra)', 1 ],
			[ 'begona', 'Begoña', 'Begoña', 1 ],
			[ 'uribarri', 'Uribarri', 'Uribarri', 1 ],
			[ 'otxarkoaga', 'Otxarkoaga-Txurdinaga', 'Otxarkoaga-Txurdinaga', 1 ],
			[ 'errekalde', 'Errekalde', 'Errekalde', 1 ],
			[ 'basurto', 'Basurto-Zorroza', 'Basurto-Zorrotza', 1 ],
		] ],
		[ 'Margen Izquierda', [
			[ 'barakaldo', 'Barakaldo', 'Barakaldo', 2 ],
			[ 'sestao', 'Sestao', 'Sestao', 2 ],
			[ 'portugalete', 'Portugalete', 'Portugalete', 2 ],
			[ 'santurtzi', 'Santurtzi', 'Santurtzi', 2 ],
			[ 'alonsotegi', 'Alonsotegi', 'Alonsotegi', 2 ],
		] ],
		[ 'Margen Derecha', [
			[ 'getxo', 'Getxo', 'Getxo', 2 ],
			[ 'leioa', 'Leioa', 'Leioa', 2 ],
			[ 'erandio', 'Erandio', 'Erandio', 2 ],
			[ 'berango', 'Berango', 'Berango', 3 ],
		] ],
		[ 'Zona Minera', [
			[ 'trapagaran', 'Valle de Trápaga', 'Trapagaran', 2 ],
			[ 'ortuella', 'Ortuella', 'Ortuella', 3 ],
			[ 'abanto', 'Abanto y Ciérvana', 'Abanto-Zierbena', 3 ],
			[ 'zierbena', 'Zierbena', 'Zierbena', 3 ],
			[ 'muskiz', 'Muskiz', 'Muskiz', 3 ],
		] ],
		[ 'Valle de Asua', [
			[ 'sondika', 'Sondika', 'Sondika', 2 ],
			[ 'derio', 'Derio', 'Derio', 2 ],
			[ 'loiu', 'Loiu', 'Loiu', 2 ],
			[ 'zamudio', 'Zamudio', 'Zamudio', 2 ],
			[ 'lezama', 'Lezama', 'Lezama', 3 ],
			[ 'larrabetzu', 'Larrabetzu', 'Larrabetzu', 3 ],
		] ],
		[ 'Alto Nervión', [
			[ 'basauri', 'Basauri', 'Basauri', 2 ],
			[ 'galdakao', 'Galdakao', 'Galdakao', 2 ],
			[ 'etxebarri', 'Etxebarri', 'Etxebarri', 2 ],
			[ 'arrigorriaga', 'Arrigorriaga', 'Arrigorriaga', 2 ],
			[ 'zaratamo', 'Zaratamo', 'Zaratamo', 3 ],
		] ],
	];
}

/** Варианты для выпадающего списка поля «Район» в SCF. */
function c24_area_choices() {
	$out = [];
	foreach ( c24_areas() as $g ) {
		foreach ( $g[1] as $a ) {
			$out[ $a[0] ] = $a[1];
		}
	}
	return $out;
}

/** Данные для main.js: телефон, отправка формы, районы, строки. */
/**
 * Имя скрытого поля антиспам-плагина WP Armour (Honeypot). Формы темы отправляются через REST API CF7,
 * поэтому поле добавляет main.js — иначе WP Armour считает каждую заявку спамом.
 */
function c24_honeypot_field() {
	if ( ! in_array( 'honeypot/wp-armour.php', (array) get_option( 'active_plugins', [] ), true ) ) {
		return '';
	}
	return (string) get_option( 'wpa_field_name', '' );
}

function c24_js_data() {
	$lang  = c24_lang();
	$areas = [];
	foreach ( c24_areas() as $g ) {
		$items = [];
		foreach ( $g[1] as $a ) {
			$items[] = [ $a[0], 'eu' === $lang ? $a[2] : $a[1], $a[3] ];
		}
		$areas[] = [ 'group' => c24__( $g[0] ), 'items' => $items ];
	}
	$form_id = function_exists( 'c24_form_id' ) ? c24_form_id() : 0;

	return [
		'phone'     => c24_phone(),
		'phoneHref' => c24_tel(),
		'endpoint'  => $form_id ? rest_url( 'contact-form-7/v1/contact-forms/' . $form_id . '/feedback' ) : '',
		'form'      => $form_id ? [
			'_wpcf7'                => $form_id,
			'_wpcf7_version'        => defined( 'WPCF7_VERSION' ) ? WPCF7_VERSION : '',
			'_wpcf7_locale'         => get_locale(),
			'_wpcf7_unit_tag'       => 'wpcf7-f' . $form_id . '-p' . (int) get_queried_object_id() . '-o1',
			'_wpcf7_container_post' => (int) get_queried_object_id(),
		] : null,
		'honeypot'  => c24_honeypot_field(),
		'areas'     => $areas,
		'zones'     => [
			1 => [ 'title' => c24__( 'Damos servicio — centro de nuestra guardia' ), 'text' => c24__( 'El operador le indicará el precio del desplazamiento y el tiempo aproximado de llegada al llamar.' ) ],
			2 => [ 'title' => c24__( 'Damos servicio' ), 'text' => c24__( 'El operador le indicará el precio del desplazamiento y el tiempo de llegada al llamar.' ) ],
			3 => [ 'title' => c24__( 'Damos servicio' ), 'text' => c24__( 'El precio del desplazamiento depende de la distancia: consúltelo, junto con el tiempo de llegada, al llamar.' ) ],
		],
		'i18n'      => [
			'menuOpen'  => c24__( 'Abrir menú' ),
			'menuClose' => c24__( 'Cerrar menú' ),
			'reviewOf'  => c24__( 'Opinión %1$s de %2$s' ),
			'phoneEmpty' => c24__( 'Indique su número de teléfono para que el cerrajero pueda llamarle.' ),
			'phonePlus' => c24__( 'Elija el prefijo del país en la lista e introduzca el número sin él.' ),
			'phoneEs'   => c24__( 'Un número español tiene 9 cifras y empieza por 6, 7, 8 o 9.' ),
			'phoneLen'  => c24__( 'Revise el número: debe tener entre 6 y 14 cifras.' ),
			'fileBig'   => c24__( 'La foto pesa más de 10 MB: elija una imagen más pequeña.' ),
			'consent'   => c24__( 'Marque la casilla de aceptación de la política de privacidad.' ),
			'sent'      => c24__( 'Solicitud enviada. El operador le llamará en unos minutos.' ),
			'failed'    => c24__( 'No se ha podido enviar la solicitud. Llámenos:' ),
			'more'      => c24__( 'Mostrar más' ),
			'less'      => c24__( 'Mostrar menos' ),
		],
	];
}

/** Preload главной картинки первого экрана. */
function c24_hero_preload() {
	$id = 0;
	if ( is_front_page() ) {
		$id = (int) c24_field( 'hero_photo' );
	} elseif ( 'service' === c24_template_key() ) {
		$id = (int) c24_field( 'sh_photo' );
	}
	if ( ! $id ) {
		return;
	}
	$src    = wp_get_attachment_image_url( $id, 'full' );
	$srcset = wp_get_attachment_image_srcset( $id, 'full' );
	$sizes  = is_front_page() ? '(max-width: 760px) 70vw, 640px' : '(max-width: 760px) 80vw, 580px';
	echo '<link rel="preload" as="image" href="' . esc_url( $src ) . '"' . ( $srcset ? ' imagesrcset="' . esc_attr( $srcset ) . '" imagesizes="' . esc_attr( $sizes ) . '"' : '' ) . ' fetchpriority="high">' . "\n";
}

/* ---------------- Меню, языки, хлебные крошки ---------------- */

/** Пункты меню локации: [ [text, href, current], … ]. Ссылки «#якорь» ведут на якорь этой страницы или главной. */
function c24_menu_items( $location ) {
	$locations = get_nav_menu_locations();
	if ( empty( $locations[ $location ] ) ) {
		return [];
	}
	$items   = wp_get_nav_menu_items( $locations[ $location ] ) ?: [];
	$current = (int) get_queried_object_id();
	$out     = [];
	foreach ( $items as $it ) {
		if ( $it->menu_item_parent ) {
			continue;
		}
		$href = $it->url;
		$cur  = false;
		if ( 'custom' === $it->type && 0 === strpos( (string) $it->url, '#' ) ) {
			$href = c24_href( $it->url );
		} elseif ( 'post_type' === $it->type ) {
			$id   = (int) $it->object_id;
			$cur  = $id === $current && ! is_404();
			$href = get_permalink( $id );
			if ( $id === c24_page_id( 'services' ) && in_array( 'services', c24_local_anchors(), true ) ) {
				$href = '#services';
				$cur  = false;
			}
		}
		$out[] = [ $it->title, $href, $cur ];
	}
	return $out;
}

function c24_menu( $location ) {
	foreach ( c24_menu_items( $location ) as $it ) {
		echo '<li><a href="' . esc_url( $it[1] ) . '"' . ( $it[2] ? ' aria-current="page"' : '' ) . '>' . c24_kses( $it[0] ) . '</a></li>' . "\n";
	}
}

/** Языки для переключателя. */
function c24_languages() {
	if ( ! function_exists( 'pll_the_languages' ) ) {
		return [];
	}
	$langs = pll_the_languages( [ 'raw' => 1, 'hide_if_empty' => 0, 'hide_if_no_translation' => 0 ] );
	return is_array( $langs ) ? $langs : [];
}

/** Хлебные крошки: Главная → (Услуги) → текущая страница. */
function c24_crumbs() {
	$id    = (int) get_queried_object_id();
	$items = [ [ c24__( 'Inicio' ), c24_home() ] ];
	$ancestors = array_reverse( get_post_ancestors( $id ) );
	foreach ( $ancestors as $a ) {
		$items[] = [ c24_field( 'crumb', $a ) ?: get_the_title( $a ), get_permalink( $a ) ];
	}
	$items[] = [ c24_field( 'crumb', $id ) ?: get_the_title( $id ), '' ];
	return $items;
}

/** Ответ FAQ / текст из WYSIWYG: если абзацев нет — оборачиваем. */
function c24_faq_answer( $html ) {
	$html = trim( (string) $html );
	return ( '' !== $html && false === stripos( $html, '<p' ) ) ? wpautop( $html ) : $html;
}
