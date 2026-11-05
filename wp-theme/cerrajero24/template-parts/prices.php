<?php
/** Блок «Цены» (главная, каталог услуг). */
defined( 'ABSPATH' ) || exit;

$eyebrow = get_field( 'pr_eyebrow' );
$title   = get_field( 'pr_title' );
$lead    = get_field( 'pr_lead' );
$photo   = get_field( 'pr_photo' );
$gug     = get_field( 'pr_photo_small' );
$caption = get_field( 'pr_caption' );
$rows    = get_field( 'pr_rows' ) ?: [];
$extras  = get_field( 'pr_extras' ) ?: [];
$notes   = get_field( 'pr_notes' ) ?: [];
$cta     = get_field( 'pr_cta_title' );
$cta_txt = get_field( 'pr_cta_text' );
?>
  <section class="section section--paper pr" id="prices" aria-labelledby="prices-title">
    <img class="pr__leaves" src="<?=esc_url( c24_asset( 'img/leaves.webp' ) );?>" alt="" aria-hidden="true" width="1100" height="825" loading="lazy">
    <div class="pr__rings" aria-hidden="true"><i></i><i></i></div>
    <div class="container">
      <header class="pr__head">
        <span class="kicker-bar" aria-hidden="true"></span>
        <?php if ( $eyebrow ) : ?><p class="eyebrow pr__eyebrow"><?=esc_html( $eyebrow );?></p><?php endif; ?>
        <?php if ( $title ) : ?><h2 class="h2 pr__title" id="prices-title"><?=c24_kses( $title );?></h2><?php endif; ?>
        <?php if ( $lead ) : ?><p class="pr__lead"><?=c24_kses( $lead );?></p><?php endif; ?>
      </header>

      <div class="pr__grid">
        <figure class="pr__visual">
          <i class="pr__orbit" aria-hidden="true"></i>
          <i class="pr__dot pr__dot--a" aria-hidden="true"></i>
          <i class="pr__dot pr__dot--b" aria-hidden="true"></i>
          <div class="pr__photo"><?=c24_img( $photo, [ 'loading' => 'lazy' ] );?></div>
          <img class="pr__lock" src="<?=esc_url( c24_asset( 'img/lock-badge.webp' ) );?>" alt="" aria-hidden="true" width="240" height="240" loading="lazy">
          <p class="pr__hours"><svg aria-hidden="true"><use href="#i-clock"/></svg><span><?=esc_html( c24__( 'Trabajamos' ) );?></span><b>24/7</b></p>
          <?php if ( $gug ) : ?><div class="pr__gug"><?=c24_img( $gug, [ 'loading' => 'lazy' ] );?></div><?php endif; ?>
        </figure>

        <div class="pr__card">
          <table class="pr-table">
            <?php if ( $caption ) : ?><caption class="visually-hidden"><?=esc_html( $caption );?></caption><?php endif; ?>
            <thead>
              <tr><th scope="col"><?=esc_html( get_field( 'pr_col_1' ) );?></th><th scope="col"><?=esc_html( get_field( 'pr_col_2' ) );?></th></tr>
            </thead>
            <tbody>
              <?php foreach ( $rows as $r ) : $p = c24_price( $r['price'] ); ?>
              <tr><th scope="row"><?=c24_icon( $r['icon'] );?><?=esc_html( $r['label'] );?></th><?php if ( null !== $p ) : ?><td><?=$p;?></td><?php else : ?><td class="is-tbd"><?=esc_html( c24__( 'a consultar' ) );?></td><?php endif; ?></tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <?php if ( $extras ) : ?>
          <ul class="pr-extras" role="list" aria-label="<?=esc_attr( c24__( 'Otros servicios: precio a consultar' ) );?>">
            <?php foreach ( $extras as $x ) : ?>
            <li><a href="<?=esc_url( 0 === strpos( (string) $x['link'], '#' ) ? c24_href( $x['link'] ) : $x['link'] );?>"><span class="pr-extras__ic"><?=c24_icon( $x['icon'] );?></span><span><?=c24_br( $x['text'] );?></span></a></li>
            <?php endforeach; ?>
          </ul>
          <?php endif; ?>
        </div>

        <?php if ( $notes ) : ?>
        <ul class="pr-notes" role="list">
          <?php foreach ( $notes as $n ) : ?>
          <li><svg aria-hidden="true"><use href="#i-check"/></svg><?=c24_kses( $n['text'] );?></li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>

        <button class="pr-cta" type="button" data-modal-open="price-modal">
          <span class="pr-cta__ic"><svg aria-hidden="true"><use href="#i-phone"/></svg></span>
          <span class="pr-cta__text"><b><?=esc_html( $cta );?></b><span><?=esc_html( $cta_txt );?></span></span>
          <svg class="pr-cta__chev" aria-hidden="true"><use href="#i-chev-right"/></svg>
        </button>
      </div>
    </div>
  </section>
