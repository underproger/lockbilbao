<?php
/**
 * Слайдер карточек услуг (главная — все услуги, страница услуги — остальные).
 * $args: ids, aria.
 */
defined( 'ABSPATH' ) || exit;
$ids   = $args['ids'] ?? [];
$total = count( $ids );
if ( ! $total ) {
	return;
}
?>
      <div class="svc-slider" data-slider>
        <ul class="svc-track" role="list" tabindex="0" aria-label="<?=esc_attr( $args['aria'] ?? '' );?>" data-slider-track>
          <?php foreach ( $ids as $i => $id ) : $c = c24_card( $id ); ?>
          <li class="svc-card<?=0 === $i ? ' is-current' : '';?>" data-slide>
            <div class="svc-card__top" aria-hidden="true"><span class="svc-card__num"><?=c24_num( $i );?> / <?=sprintf( '%02d', $total );?></span><svg class="svc-card__corner"><use href="#i-arrow"/></svg></div>
            <div class="svc-card__icon"><?=c24_img( $c['icon'], [ 'loading' => 'lazy', 'draggable' => 'false' ] );?></div>
            <h3 class="svc-card__title"><a draggable="false" href="<?=esc_url( $c['url'] );?>"><?=c24_kses( $c['title'] );?></a></h3>
            <?php if ( $c['text'] ) : ?><p class="svc-card__text"><?=c24_kses( $c['text'] );?></p><?php endif; ?>
            <div class="svc-card__foot">
              <span class="svc-card__price"><?=c24_price( $c['price'] ) ?? esc_html( c24__( 'a consultar' ) );?></span>
              <span class="svc-card__more" aria-hidden="true"><?=esc_html( c24__( 'Más información' ) );?> <i><svg><use href="#i-arrow"/></svg></i></span>
            </div>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div class="svc-nav">
        <div class="svc-progress" aria-hidden="true">
          <b data-slider-current>01</b><span>/ <?=sprintf( '%02d', $total );?></span>
          <i class="svc-progress__bar"><i data-slider-bar></i></i>
          <span class="svc-progress__hint"><?=esc_html( c24__( 'Desliza los servicios' ) );?></span>
        </div>
        <a class="svc-nav__help" href="#request"><?=esc_html( c24__( '¿No encuentra el servicio que necesita? Contacte con el cerrajero' ) );?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></a>
        <div class="svc-nav__btns">
          <button class="svc-btn" type="button" aria-label="<?=esc_attr( c24__( 'Servicio anterior' ) );?>" data-slider-prev><svg aria-hidden="true"><use href="#i-chev-left"/></svg></button>
          <button class="svc-btn svc-btn--next" type="button" aria-label="<?=esc_attr( c24__( 'Servicio siguiente' ) );?>" data-slider-next><svg aria-hidden="true"><use href="#i-chev-right"/></svg></button>
        </div>
      </div>
