<?php
/** Финальный блок с формой заявки. */
defined( 'ABSPATH' ) || exit;

$kicker = get_field( 'final_kicker' );
$title  = get_field( 'final_title' );
$lead   = get_field( 'final_lead' );
$hours  = get_field( 'final_hours' );
$items  = get_field( 'final_guarantees' ) ?: [];
?>
  <section class="final" id="request" aria-labelledby="request-title">
    <img class="final__orbit" src="<?=esc_url( c24_asset( 'img/orbit.webp' ) );?>" alt="" aria-hidden="true" width="1184" height="1031" loading="lazy">
    <div class="container final__inner">
      <div class="final__content">
        <?php if ( $kicker ) : ?><p class="kicker kicker--light"><i aria-hidden="true"></i><?=esc_html( $kicker );?></p><?php endif; ?>
        <?php if ( $title ) : ?><h2 class="h2 h2--light" id="request-title"><?=c24_kses( $title );?></h2><?php endif; ?>
        <?php if ( $lead ) : ?><p class="final__lead"><?=c24_kses( $lead );?></p><?php endif; ?>

        <div class="final__contact">
          <a class="final__phone" href="<?=esc_attr( c24_tel() );?>"><svg aria-hidden="true"><use href="#i-phone"/></svg><?=esc_html( c24_phone() );?></a>
          <?php if ( $hours ) : ?><p class="final__hours"><span class="pulse" aria-hidden="true"></span><?=esc_html( $hours );?></p><?php endif; ?>
          <a class="btn btn--outline-light" href="<?=esc_url( c24_wa() );?>"<?=c24_ext();?>><svg aria-hidden="true"><use href="#i-wa"/></svg><?=esc_html( c24__( 'Escribir por WhatsApp' ) );?></a>
        </div>

        <?php if ( $items ) : ?>
        <ul class="final__guarantees" role="list">
          <?php foreach ( $items as $it ) : ?>
          <li><svg aria-hidden="true"><use href="#i-check"/></svg><?=c24_kses( $it['text'] );?></li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>
      </div>

<?=c24_form( 'final', [ 'title' => get_field( 'final_form_title' ), 'service' => get_field( 'form_service' ) ] );?>
    </div>
  </section>
