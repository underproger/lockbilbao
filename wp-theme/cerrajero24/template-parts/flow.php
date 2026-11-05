<?php
/** Блок «Этапы работы». $args['line'] — вариант с линией (страница услуги). */
defined( 'ABSPATH' ) || exit;

$kicker = get_field( 'flow_kicker' );
$title  = get_field( 'flow_title' );
$steps  = get_field( 'flow_steps' ) ?: [];
$cta    = get_field( 'flow_cta_title' );
$sub    = get_field( 'flow_cta_sub' );
$btn    = get_field( 'flow_cta_btn' );
?>
  <section class="section flow" id="process" aria-labelledby="process-title">
    <div class="flow__deco" aria-hidden="true"><i class="flow__ring flow__ring--a"></i><i class="flow__ring flow__ring--b"></i><i class="flow__ring flow__ring--c"></i></div>
    <div class="container">
      <header class="flow__head">
        <?php if ( $kicker ) : ?><p class="kicker kicker--lime"><i aria-hidden="true"></i><?=esc_html( $kicker );?></p><?php endif; ?>
        <?php if ( $title ) : ?><h2 class="h2 flow__title" id="process-title"><?=c24_kses( $title );?></h2><?php endif; ?>
      </header>

      <ol class="flow-steps<?=! empty( $args['line'] ) ? ' flow-steps--line' : '';?>">
        <?php foreach ( $steps as $i => $s ) : ?>
        <li class="flow-step">
          <div class="flow-step__top" aria-hidden="true">
            <span class="flow-step__ic"><?=c24_icon( $s['icon'], '', false );?></span>
            <span class="flow-step__num"><?=c24_num( $i );?></span>
          </div>
          <?php if ( $s['title'] ) : ?><h3 class="flow-step__title"><?=c24_kses( $s['title'] );?></h3><?php endif; ?>
          <p class="flow-step__text"><?=c24_kses( $s['text'] );?></p>
        </li>
        <?php endforeach; ?>
      </ol>

      <?php if ( $cta ) : ?>
      <div class="flow-cta">
        <span class="flow-cta__ic" aria-hidden="true"><svg><use href="#i-phone-ring"/></svg></span>
        <div class="flow-cta__text">
          <p class="flow-cta__title"><?=c24_kses( $cta );?></p>
          <?php if ( $sub ) : ?><p class="flow-cta__sub"><?=c24_kses( $sub );?></p><?php endif; ?>
        </div>
        <a class="btn btn--lime btn--lg flow-cta__btn" href="<?=esc_attr( c24_tel() );?>"><?=esc_html( $btn );?> <svg aria-hidden="true"><use href="#i-arrow-right"/></svg></a>
      </div>
      <?php endif; ?>
    </div>
  </section>
