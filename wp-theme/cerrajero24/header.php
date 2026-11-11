<?php
defined( 'ABSPATH' ) || exit;

$logo      = (int) c24_opt( 'logo' );
$langs     = c24_languages();
$lang_cur  = null;
foreach ( $langs as $l ) {
	if ( ! empty( $l['current_lang'] ) ) {
		$lang_cur = $l;
	}
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<?php wp_head(); ?>
</head>
<body<?=( $c = get_body_class() ) ? ' class="' . esc_attr( implode( ' ', $c ) ) . '"' : '';?>>
<?php wp_body_open(); ?>

<?php get_template_part( 'template-parts/sprite' ); ?>

<a class="skip-link" href="#main"><?=esc_html( c24__( 'Ir al contenido' ) );?></a>

<header class="header" data-header>
  <div class="container header__inner">
    <a class="logo" href="<?=esc_url( c24_home() );?>" aria-label="<?=esc_attr( c24__( 'Cerrajero24 — ir a la página de inicio' ) );?>">
      <?php if ( $logo ) : ?><?=c24_img( $logo, [ 'alt' => 'Cerrajero24' ] );?><?php else : ?><img src="<?=esc_url( c24_asset( 'img/logo.webp' ) );?>" alt="Cerrajero24" width="715" height="129"><?php endif; ?>
    </a>

    <nav class="nav" aria-label="<?=esc_attr( c24__( 'Menú principal' ) );?>">
      <ul class="nav__list">
        <?php c24_menu( 'menu-header' ); ?>
      </ul>
    </nav>

    <div class="header__actions">
      <span class="header__247" title="<?=esc_attr( c24__( 'Trabajamos las 24 horas' ) );?>">24/7</span>
      <?php if ( count( $langs ) > 1 && $lang_cur ) : ?>
      <details class="lang" data-lang>
        <summary class="lang__current" aria-label="<?=esc_attr( c24__( 'Idioma' ) . ': ' . $lang_cur['name'] );?>"><?=esc_html( strtoupper( $lang_cur['slug'] ) );?><svg aria-hidden="true"><use href="#i-chev-right"/></svg></summary>
        <ul class="lang__list" role="list">
          <?php foreach ( $langs as $l ) : ?>
          <li><a href="<?=esc_url( $l['url'] );?>" hreflang="<?=esc_attr( $l['slug'] );?>" lang="<?=esc_attr( $l['slug'] );?>"<?=! empty( $l['current_lang'] ) ? ' aria-current="true"' : '';?>><b><?=esc_html( strtoupper( $l['slug'] ) );?></b><?=esc_html( $l['name'] );?></a></li>
          <?php endforeach; ?>
        </ul>
      </details>
      <?php endif; ?>
      <a class="btn btn--lime btn--sm" href="<?=esc_attr( c24_tel() );?>"><?=esc_html( c24__( 'Llamada urgente' ) );?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></a>
    </div>

    <button class="burger" type="button" aria-expanded="false" aria-controls="mobile-menu" aria-label="<?=esc_attr( c24__( 'Abrir menú' ) );?>" data-burger>
      <span></span><span></span><span></span>
    </button>
  </div>
</header>

<div class="mobile-menu" id="mobile-menu" data-mobile-menu hidden>
  <nav aria-label="<?=esc_attr( c24__( 'Menú móvil' ) );?>">
    <ul class="mobile-menu__list">
      <?php c24_menu( 'menu-mobile' ); ?>
    </ul>
  </nav>
  <div class="mobile-menu__foot">
    <?php if ( count( $langs ) > 1 ) : ?>
    <ul class="mobile-menu__langs" role="list" aria-label="<?=esc_attr( c24__( 'Idioma' ) );?>">
      <?php foreach ( $langs as $l ) : ?>
      <li><a href="<?=esc_url( $l['url'] );?>" hreflang="<?=esc_attr( $l['slug'] );?>" lang="<?=esc_attr( $l['slug'] );?>"<?=! empty( $l['current_lang'] ) ? ' aria-current="true"' : '';?>><?=esc_html( $l['name'] );?></a></li>
      <?php endforeach; ?>
    </ul>
    <?php endif; ?>
    <a class="btn btn--lime btn--block btn--lg" href="<?=esc_attr( c24_tel() );?>"><svg aria-hidden="true"><use href="#i-phone"/></svg> <?=esc_html( c24_phone() );?></a>
    <p class="mobile-menu__note"><?=esc_html( c24__( 'Las 24 horas, todos los días · Bilbao y Gran Bilbao' ) );?></p>
  </div>
</div>
