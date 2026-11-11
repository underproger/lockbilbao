<?php
/** Блок «Отзывы» (главная, страница услуги). */
defined( 'ABSPATH' ) || exit;

$kicker   = c24_field( 'rv_kicker' );
$title    = c24_field( 'rv_title' );
$links    = c24_field( 'rv_links' ) ?: [];
$items    = c24_field( 'rv_items' ) ?: [];
$aria     = c24_field( 'rv_aria' );
$cta      = c24_field( 'rv_cta_text' );
$cta_maps = 'maps' === c24_field( 'rv_cta_type' );
?>
  <section class="section rv" id="reviews" aria-labelledby="reviews-title">
    <div class="rv__deco" aria-hidden="true"><i class="rv__ring rv__ring--a"></i><i class="rv__ring rv__ring--b"></i><i class="rv__spark rv__spark--a"></i><i class="rv__spark rv__spark--b"></i></div>
    <div class="container">
      <header class="sec-head rv__head">
        <div class="sec-head__main">
          <?php if ( $kicker ) : ?><p class="kicker"><i aria-hidden="true"></i><?=esc_html( $kicker );?></p><?php endif; ?>
          <?php if ( $title ) : ?><h2 class="h2" id="reviews-title"><?=c24_kses( $title );?></h2><?php endif; ?>
        </div>
        <div class="rv__aside">
          <div class="g-score">
            <svg class="g-score__logo" aria-hidden="true"><use href="#i-google"/></svg>
            <div>
              <p class="g-score__value"><?=esc_html( c24_rating() );?> <span class="stars" role="img" aria-label="<?=esc_attr( sprintf( c24__( '%s de 5' ), '5' ) );?>"><?=c24_stars();?></span></p>
              <p class="g-score__count"><?=esc_html( sprintf( c24__( '%s opiniones en Google' ), c24_reviews_count() ) );?></p>
            </div>
          </div>
        </div>
      </header>
      <?php if ( $links ) : ?>
      <div class="rv__links">
        <?php foreach ( $links as $l ) : ?>
        <a class="link-arrow" href="<?=esc_url( $l['url'] ?: c24_maps() );?>"<?=c24_ext();?>><?=esc_html( $l['text'] );?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>

    <div class="rv-slider" role="region" aria-roledescription="<?=esc_attr( c24__( 'carrusel' ) );?>" aria-label="<?=esc_attr( $aria ?: c24__( 'Opiniones de clientes' ) );?>" data-rv>
      <button class="rv-slider__nav rv-slider__nav--prev" type="button" aria-label="<?=esc_attr( c24__( 'Opinión anterior' ) );?>" data-rv-prev><svg aria-hidden="true"><use href="#i-chev-left"/></svg></button>
      <ul class="rv-track" role="list" tabindex="0" aria-label="<?=esc_attr( c24__( 'Desplácese por las opiniones con las flechas' ) );?>" data-rv-track>
        <?php foreach ( $items as $r ) : $demo = ! empty( $r['demo'] ); ?>
        <li class="review<?=$demo ? ' review--demo' : '';?>" data-rv-slide>
          <div class="review__head"><span class="review__avatar" aria-hidden="true"><?=esc_html( mb_strtoupper( mb_substr( (string) $r['name'], 0, 1 ) ) );?></span><div><p class="review__name"><?=esc_html( $r['name'] );?></p><p class="review__date"><?=esc_html( $demo ? c24__( 'Opinión de ejemplo' ) : $r['date'] );?></p></div><svg class="review__g" aria-hidden="true"><use href="#i-google"/></svg></div>
          <span class="stars stars--sm" role="img" aria-label="<?=esc_attr( sprintf( c24__( 'Valoración: %s de 5' ), '5' ) );?>"><?=c24_stars();?></span>
          <?php if ( $r['tag'] ) : ?><p class="review__tag"><?=esc_html( $r['tag'] );?></p><?php endif; ?>
          <p class="review__text"><?=esc_html( $r['text'] );?></p>
        </li>
        <?php endforeach; ?>
      </ul>
      <button class="rv-slider__nav rv-slider__nav--next" type="button" aria-label="<?=esc_attr( c24__( 'Opinión siguiente' ) );?>" data-rv-next><svg aria-hidden="true"><use href="#i-chev-right"/></svg></button>
    </div>

    <div class="container">
      <div class="rv-dots" data-rv-dots></div>
      <?php if ( $cta ) : ?>
      <div class="center-cta rv__cta">
        <?php if ( $cta_maps ) : ?>
        <a class="btn btn--ghost btn--lg" href="<?=esc_url( c24_maps() );?>"<?=c24_ext();?>><?=esc_html( $cta );?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></a>
        <?php else : ?>
        <a class="btn btn--lime btn--lg" href="<?=esc_attr( c24_tel() );?>"><?=esc_html( $cta );?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></a>
        <?php endif; ?>
      </div>
      <?php endif; ?>
    </div>
  </section>
