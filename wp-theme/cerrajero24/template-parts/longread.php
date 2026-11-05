<?php
/** SEO-текст «Полезно знать» со сворачиванием. Подзаголовкам H3 автоматически даются id guide-1, guide-2… */
defined( 'ABSPATH' ) || exit;

$kicker = get_field( 'lr_kicker' );
$title  = get_field( 'lr_title' );
$lead   = get_field( 'lr_lead' );
$body   = (string) get_field( 'lr_body' );
if ( ! $title && ! $body ) {
	return;
}
$n    = 0;
$body = preg_replace_callback( '/<h3(\s[^>]*)?>/i', function ( $m ) use ( &$n ) {
	$n++;
	return '<h3 id="guide-' . $n . '">';
}, $body );
?>
  <section class="section longread-sec" id="guide" aria-labelledby="guide-title">
    <div class="container longread" data-more>
      <header class="longread__head">
        <?php if ( $kicker ) : ?><p class="kicker kicker--lime"><i aria-hidden="true"></i><?=esc_html( $kicker );?></p><?php endif; ?>
        <?php if ( $title ) : ?><h2 class="h2 longread__title" id="guide-title"><?=c24_kses( $title );?></h2><?php endif; ?>
      </header>
      <div class="longread__main">
        <div class="longread__body" id="guide-text" data-more-body>
          <?php if ( $lead ) : ?><p class="longread__lead"><?=c24_kses( $lead );?></p><?php endif; ?>
          <?=$body;?>
        </div>
        <button class="longread__more" type="button" aria-expanded="false" aria-controls="guide-text" data-more-btn hidden><span data-more-label><?=esc_html( c24__( 'Mostrar más' ) );?></span><svg aria-hidden="true"><use href="#i-chev-right"/></svg></button>
      </div>
    </div>
  </section>
