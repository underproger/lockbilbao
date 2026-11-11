<?php
/** Блок «Районы выезда» с картой и проверкой района. */
defined( 'ABSPATH' ) || exit;

$kicker = c24_field( 'geo_kicker' );
$title  = c24_field( 'geo_title' );
$lead   = c24_field( 'geo_lead' );
$cards  = [];
foreach ( [ 1, 2 ] as $n ) {
	$cards[] = [
		'title' => c24_field( "geo_card_{$n}_title" ),
		'count' => c24_field( "geo_card_{$n}_count" ),
		'areas' => c24_field( "geo_card_{$n}_areas" ) ?: [],
		'note'  => c24_field( "geo_card_{$n}_note" ),
	];
}
$map_alt = c24__( 'Mapa de la zona de servicio de Cerrajero24: Bilbao y los 26 municipios del Gran Bilbao — Getxo, Santurtzi, Portugalete, Sestao, Barakaldo, Leioa, Erandio, Basauri, Galdakao y otros' );
?>
  <section class="section geo" id="areas" aria-labelledby="areas-title">
    <div class="geo__deco" aria-hidden="true"><i class="geo__ring geo__ring--a"></i><i class="geo__ring geo__ring--b"></i><i class="geo__spark"></i></div>
    <div class="container">
      <header class="sec-head geo__head">
        <div class="sec-head__main">
          <?php if ( $kicker ) : ?><p class="kicker"><i aria-hidden="true"></i><?=esc_html( $kicker );?></p><?php endif; ?>
          <?php if ( $title ) : ?><h2 class="h2 geo__title" id="areas-title"><?=c24_kses( $title );?></h2><?php endif; ?>
        </div>
        <?php if ( $lead ) : ?><p class="sec-head__lead"><?=c24_kses( $lead );?></p><?php endif; ?>
      </header>

      <div class="areas">
        <figure class="areas__map">
          <picture>
            <source media="(max-width: 760px)" srcset="<?=esc_url( c24_asset( 'img/map-gran-bilbao-m.webp' ) );?>">
            <img class="areas__img" src="<?=esc_url( c24_asset( 'img/map-gran-bilbao-900.webp' ) );?>" srcset="<?=esc_url( c24_asset( 'img/map-gran-bilbao-900.webp' ) );?> 900w, <?=esc_url( c24_asset( 'img/map-gran-bilbao.webp' ) );?> 1800w" sizes="(max-width: 1240px) 100vw, 60vw" width="1800" height="1500" loading="lazy" alt="<?=esc_attr( $map_alt );?>">
          </picture>
          <figcaption class="areas__credit">© <a href="https://www.openstreetmap.org/copyright"<?=c24_ext();?>>OpenStreetMap</a> · IGN</figcaption>
        </figure>

        <div class="areas__side">
          <?php foreach ( $cards as $c ) : if ( ! $c['title'] && ! $c['areas'] ) { continue; } ?>
          <div class="area-card">
            <div class="area-card__head">
              <?php if ( $c['title'] ) : ?><h3 class="area-card__title"><?=c24_kses( $c['title'] );?></h3><?php endif; ?>
              <?php if ( $c['count'] ) : ?><span class="area-card__count"><?=esc_html( $c['count'] );?></span><?php endif; ?>
            </div>
            <ul class="pills" role="list">
              <?php foreach ( $c['areas'] as $a ) : ?>
              <li><span class="pill" data-area="<?=esc_attr( $a['area'] );?>"><?=esc_html( $a['label'] );?></span></li>
              <?php endforeach; ?>
            </ul>
            <?php if ( $c['note'] ) : ?><p class="area-card__note"><?=c24_kses( $c['note'] );?></p><?php endif; ?>
          </div>
          <?php endforeach; ?>

          <div class="checker">
            <span class="checker__deco" aria-hidden="true"><i></i><i></i><b></b><b></b></span>
            <div class="checker__top">
              <span class="checker__ic" aria-hidden="true"><svg><use href="#i-pin"/></svg></span>
              <div>
                <label class="checker__label" for="area-check"><?=esc_html( c24__( 'Compruebe su zona' ) );?></label>
                <p class="checker__text" data-checker-result aria-live="polite"><?=esc_html( c24__( 'Elija un barrio de Bilbao o un municipio para saber cómo consultar el tiempo de llegada.' ) );?></p>
              </div>
            </div>
            <div class="checker__row">
              <select class="select checker__select" id="area-check" data-area-select data-area-checker>
                <option value=""><?=esc_html( c24__( 'Barrio o municipio' ) );?></option>
              </select>
              <a class="btn btn--lime checker__btn" href="<?=esc_attr( c24_tel() );?>"><?=esc_html( c24__( 'Consultar el tiempo de llegada' ) );?> <svg aria-hidden="true"><use href="#i-arrow-right"/></svg></a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
