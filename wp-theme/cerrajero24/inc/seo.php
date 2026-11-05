<?php
/**
 * SEO: title, description, Open Graph, geo-метки и разметка schema.org из полей страниц.
 * Если активен Yoast SEO — title/description/OG выводит он (наши поля подставляются, если в Yoast пусто).
 * hreflang выводит Polylang.
 */

defined( 'ABSPATH' ) || exit;

function c24_has_seo_plugin() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' );
}

function c24_seo_post_id() {
	return ( is_singular() || is_front_page() ) ? (int) get_queried_object_id() : 0;
}

function c24_seo_title() {
	if ( is_404() ) {
		return c24__( 'Página no encontrada — Cerrajero24' );
	}
	$id = c24_seo_post_id();
	$t  = $id ? get_field( 'seo_title', $id ) : '';
	return $t ?: '';
}

function c24_seo_desc() {
	if ( is_404() ) {
		return c24__( 'Esta página no existe en la web de Cerrajero24. Vaya al inicio o al catálogo de servicios, o llame: el cerrajero atiende 24/7.' );
	}
	$id = c24_seo_post_id();
	return $id ? (string) get_field( 'seo_desc', $id ) : '';
}

/* ---------------- Без SEO-плагина ---------------- */

add_filter( 'pre_get_document_title', function ( $title ) {
	if ( c24_has_seo_plugin() ) {
		return $title;
	}
	return c24_seo_title() ?: $title;
}, 20 );

add_action( 'wp_head', function () {
	$id   = c24_seo_post_id();
	$desc = c24_seo_desc();
	if ( ! c24_has_seo_plugin() ) {
		if ( $desc ) {
			echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
		}
		echo '<meta name="robots" content="' . ( is_404() ? 'noindex, follow' : 'index, follow, max-image-preview:large' ) . '">' . "\n";
		if ( $id ) {
			$title = c24_seo_title() ?: wp_get_document_title();
			$og    = get_field( 'og_desc', $id ) ?: $desc;
			$locale = str_replace( '-', '_', get_locale() );
			echo '<meta property="og:type" content="website">' . "\n";
			echo '<meta property="og:locale" content="' . esc_attr( $locale ) . '">' . "\n";
			echo '<meta property="og:site_name" content="Cerrajero24">' . "\n";
			echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
			if ( $og ) {
				echo '<meta property="og:description" content="' . esc_attr( $og ) . '">' . "\n";
			}
			echo '<meta property="og:url" content="' . esc_url( get_permalink( $id ) ) . '">' . "\n";
			echo '<meta property="og:image" content="' . esc_url( c24_asset( 'img/og-image.jpg' ) ) . '">' . "\n";
			echo '<meta property="og:image:width" content="1200">' . "\n";
			echo '<meta property="og:image:height" content="630">' . "\n";
			echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
		}
	}
	echo '<meta name="geo.region" content="ES-BI">' . "\n";
	echo '<meta name="geo.placename" content="Bilbao">' . "\n";
	echo '<meta name="geo.position" content="43.2630;-2.9350">' . "\n";
	echo '<meta name="ICBM" content="43.2630, -2.9350">' . "\n";
}, 1 );

// Пустые поля Yoast — берём наши.
add_filter( 'wpseo_title', function ( $t ) {
	$id = c24_seo_post_id();
	return ( $id && ! get_post_meta( $id, '_yoast_wpseo_title', true ) && c24_seo_title() ) ? c24_seo_title() : $t;
} );
add_filter( 'wpseo_metadesc', function ( $d ) {
	return $d ?: c24_seo_desc();
} );

/* ---------------- schema.org ---------------- */

function c24_schema_business( $full = false ) {
	$home = c24_home();
	$b    = [
		'@type'     => 'Locksmith',
		'@id'       => home_url( '/#business' ),
		'name'      => 'Cerrajero24',
		'url'       => $home,
		'telephone' => preg_replace( '/[^\d+]/', '', c24_phone() ),
		'image'     => c24_asset( 'img/og-image.jpg' ),
		'address'   => [ '@type' => 'PostalAddress', 'addressLocality' => 'Bilbao', 'addressRegion' => 'Bizkaia', 'addressCountry' => 'ES' ],
	];
	if ( ! $full ) {
		return $b;
	}
	$b['logo']                      = c24_asset( 'img/logo.png' );
	$b['email']                     = c24_email();
	$b['description']               = c24__( 'Apertura urgente, reparación, cambio e instalación de cerraduras en Bilbao y Gran Bilbao.' );
	$b['priceRange']                = '€€';
	$b['currenciesAccepted']        = 'EUR';
	$b['paymentAccepted']           = 'Cash, Credit Card, Bizum';
	$b['geo']                       = [ '@type' => 'GeoCoordinates', 'latitude' => 43.2630, 'longitude' => -2.9350 ];
	$b['openingHoursSpecification'] = [ c24_schema_hours() ];
	$b['areaServed']                = [];
	foreach ( array_slice( c24_areas(), 1 ) as $g ) {
		foreach ( $g[1] as $a ) {
			$b['areaServed'][] = [ '@type' => 'City', 'name' => $a[1] ];
		}
	}
	array_unshift( $b['areaServed'], [ '@type' => 'City', 'name' => 'Bilbao' ] );
	$offers = [];
	foreach ( c24_services() as $sid ) {
		$c      = c24_card( $sid );
		$offer  = [ '@type' => 'Offer', 'itemOffered' => [ '@type' => 'Service', 'name' => wp_strip_all_tags( $c['title'] ), 'url' => $c['url'] ] ];
		if ( is_numeric( $c['price'] ) ) {
			$offer['priceSpecification'] = [ '@type' => 'PriceSpecification', 'minPrice' => (float) $c['price'], 'priceCurrency' => 'EUR' ];
		}
		$offers[] = $offer;
	}
	if ( $offers ) {
		$b['hasOfferCatalog'] = [ '@type' => 'OfferCatalog', 'name' => c24__( 'Servicios de cerrajería urgente' ), 'itemListElement' => $offers ];
	}
	return $b;
}

function c24_schema_hours() {
	return [
		'@type'     => 'OpeningHoursSpecification',
		'dayOfWeek' => [ 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' ],
		'opens'     => '00:00',
		'closes'    => '23:59',
	];
}

function c24_schema_text( $html ) {
	return trim( preg_replace( '/\s+/u', ' ', html_entity_decode( wp_strip_all_tags( (string) $html ), ENT_QUOTES, 'UTF-8' ) ) );
}

add_action( 'wp_head', function () {
	if ( is_404() || ! ( is_page() || is_front_page() ) ) {
		return;
	}
	$id   = (int) get_queried_object_id();
	$url  = get_permalink( $id );
	$lang = str_replace( '_', '-', get_locale() );
	$tpl  = c24_template_key();
	$name = c24_seo_title() ?: get_the_title( $id );

	$graph = [ c24_schema_business( in_array( $tpl, [ 'front', 'contacts' ], true ) ) ];

	if ( 'front' === $tpl ) {
		$graph[] = [ '@type' => 'WebSite', '@id' => home_url( '/#website' ), 'url' => c24_home(), 'name' => 'Cerrajero24', 'inLanguage' => $lang ];
		$graph[] = [ '@type' => 'WebPage', '@id' => $url . '#webpage', 'url' => $url, 'name' => $name, 'isPartOf' => [ '@id' => home_url( '/#website' ) ], 'about' => [ '@id' => home_url( '/#business' ) ], 'inLanguage' => $lang ];
	} else {
		$crumbs = [];
		foreach ( c24_crumbs() as $i => $c ) {
			$crumbs[] = [ '@type' => 'ListItem', 'position' => $i + 1, 'name' => $c[0], 'item' => $c[1] ?: $url ];
		}
		$graph[] = [ '@type' => 'BreadcrumbList', '@id' => $url . '#breadcrumbs', 'itemListElement' => $crumbs ];
		$page    = [ '@type' => 'WebPage', '@id' => $url . '#webpage', 'url' => $url, 'name' => $name, 'breadcrumb' => [ '@id' => $url . '#breadcrumbs' ], 'inLanguage' => $lang ];

		if ( 'service' === $tpl ) {
			$graph[]       = [
				'@type'          => 'Service',
				'@id'            => $url . '#service',
				'name'           => get_field( 'schema_name', $id ) ?: wp_strip_all_tags( (string) get_field( 'sh_title', $id ) ),
				'serviceType'    => get_field( 'schema_type', $id ) ?: get_field( 'crumb', $id ),
				'description'    => get_field( 'schema_desc', $id ) ?: c24_seo_desc(),
				'provider'       => [ '@id' => home_url( '/#business' ) ],
				'areaServed'     => [ [ '@type' => 'City', 'name' => 'Bilbao' ], [ '@type' => 'AdministrativeArea', 'name' => 'Gran Bilbao' ] ],
				'hoursAvailable' => c24_schema_hours(),
			];
			$page['about'] = [ '@id' => $url . '#service' ];
		} elseif ( 'services' === $tpl ) {
			$items = [];
			foreach ( c24_services() as $i => $sid ) {
				$items[] = [ '@type' => 'ListItem', 'position' => $i + 1, 'name' => wp_strip_all_tags( c24_card( $sid )['title'] ), 'url' => get_permalink( $sid ) ];
			}
			$page['@type']      = 'CollectionPage';
			$page['mainEntity'] = [ '@type' => 'ItemList', 'itemListElement' => $items ];
		} elseif ( 'contacts' === $tpl ) {
			$page['@type'] = 'ContactPage';
			$page['about'] = [ '@id' => home_url( '/#business' ) ];
		}
		$graph[] = $page;
	}

	echo '<script type="application/ld+json">' . wp_json_encode( [ '@context' => 'https://schema.org', '@graph' => $graph ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";

	$faq = get_field( 'faq_items', $id ) ?: [];
	if ( $faq ) {
		$q = [];
		foreach ( $faq as $it ) {
			$q[] = [ '@type' => 'Question', 'name' => c24_schema_text( $it['q'] ), 'acceptedAnswer' => [ '@type' => 'Answer', 'text' => c24_schema_text( $it['a'] ) ] ];
		}
		echo '<script type="application/ld+json">' . wp_json_encode( [ '@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $q ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
	}
}, 30 );
