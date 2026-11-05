<?php
/** Блок «Почему выбирают» (главная, каталог). */
defined( 'ABSPATH' ) || exit;

$kicker = get_field( 'why_kicker' );
$title  = get_field( 'why_title' );
$lead   = get_field( 'why_lead' );
$cards  = get_field( 'why_cards' ) ?: [];
$photo  = get_field( 'why_photo' );
$ptitle = get_field( 'why_photo_title' );
$ptext  = get_field( 'why_photo_text' );
// Раскладка сетки из вёрстки: модификаторы карточек по порядку.
$mods = [ 'a', 'f', 'b', 'c', 'd', 'e' ];
?>
  <section class="section why" id="why" aria-labelledby="why-title">
    <div class="why__deco" aria-hidden="true"><i class="why__ring why__ring--a"></i><i class="why__ring why__ring--b"></i><i class="why__spark"></i></div>
    <div class="container">
      <header class="why__head">
        <?php if ( $kicker ) : ?><p class="kicker kicker--lime"><i aria-hidden="true"></i><?=esc_html( $kicker );?></p><?php endif; ?>
        <?php if ( $title ) : ?><h2 class="h2 why__title" id="why-title"><?=c24_kses( $title );?></h2><?php endif; ?>
        <?php if ( $lead ) : ?><p class="why__lead"><?=c24_kses( $lead );?></p><?php endif; ?>
      </header>

      <div class="why-grid">
        <?php foreach ( $cards as $i => $c ) : ?>
        <article class="why-card why-card--<?=$mods[ $i % 6 ];?>">
          <span class="why-card__ic"><?=c24_icon( $c['icon'] );?></span>
          <?php if ( $c['title'] ) : ?><h3 class="why-card__title"><?=c24_kses( $c['title'] );?></h3><?php endif; ?>
          <p class="why-card__text"><?=c24_kses( $c['text'] );?></p>
        </article>
        <?php endforeach; ?>
        <?php if ( $ptitle ) : ?>
        <article class="why-photo why-photo--guarantee">
          <?=c24_img( $photo, [ 'class' => 'why-photo__img', 'alt' => '', 'aria-hidden' => 'true', 'loading' => 'lazy' ] );?>
          <div class="why-photo__body">
            <span class="why-photo__ic"><svg aria-hidden="true"><use href="#i-shield-check"/></svg></span>
            <h3 class="why-card__title"><?=c24_kses( $ptitle );?></h3>
            <p class="why-card__text"><?=c24_kses( $ptext );?></p>
          </div>
        </article>
        <?php endif; ?>
      </div>
    </div>
  </section>
