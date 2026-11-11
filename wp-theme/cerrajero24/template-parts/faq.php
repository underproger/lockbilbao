<?php
/** Блок FAQ. Разметка FAQPage для schema.org собирается из этих же полей (inc/seo.php). */
defined( 'ABSPATH' ) || exit;

$kicker = c24_field( 'faq_kicker' );
$title  = c24_field( 'faq_title' );
$htitle = c24_field( 'faq_help_title' );
$htext  = c24_field( 'faq_help_text' );
$items  = c24_field( 'faq_items' ) ?: [];
?>
  <section class="section" id="faq" aria-labelledby="faq-title">
    <div class="container faq">
      <div class="faq__aside">
        <?php if ( $kicker ) : ?><p class="kicker"><i aria-hidden="true"></i><?=esc_html( $kicker );?></p><?php endif; ?>
        <?php if ( $title ) : ?><h2 class="h2" id="faq-title"><?=c24_kses( $title );?></h2><?php endif; ?>
        <div class="faq__help">
          <?php if ( $htitle ) : ?><p class="faq__help-title"><?=c24_kses( $htitle );?></p><?php endif; ?>
          <?php if ( $htext ) : ?><p><?=c24_kses( $htext );?></p><?php endif; ?>
          <div class="faq__help-actions">
            <a class="btn btn--lime" href="<?=esc_attr( c24_tel() );?>"><svg aria-hidden="true"><use href="#i-phone"/></svg><?=esc_html( c24__( 'Llamar' ) );?></a>
            <a class="btn btn--ghost" href="<?=esc_url( c24_wa() );?>"<?=c24_ext();?>><svg aria-hidden="true"><use href="#i-wa"/></svg>WhatsApp</a>
          </div>
        </div>
      </div>

      <div class="accordion" data-accordion>
        <?php foreach ( $items as $i => $q ) : ?>
        <details class="qa"<?=0 === $i ? ' open' : '';?>>
          <summary><h3><?=esc_html( $q['q'] );?></h3><svg aria-hidden="true"><use href="#i-plus"/></svg></summary>
          <div class="qa__body"><?=c24_faq_answer( $q['a'] );?></div>
        </details>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
