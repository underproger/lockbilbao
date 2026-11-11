<?php
/**
 * Template Name: Контакты
 */

defined( 'ABSPATH' ) || exit;

$sh_title = c24_field( 'sh_title' );
$sh_lead  = c24_field( 'sh_lead' );

$call_label = c24_field( 'ct_call_label' );
$call_text  = c24_field( 'ct_call_text' );
$wa_text    = c24_field( 'ct_wa_text' );
$mail_text  = c24_field( 'ct_mail_text' );
$facts      = c24_field( 'ct_facts' ) ?: [];

$brief_kicker = c24_field( 'brief_kicker' );
$brief_title  = c24_field( 'brief_title' );
$brief_lead   = c24_field( 'brief_lead' );
$brief_sos    = c24_field( 'brief_sos_text' );
$brief_sos_l  = c24_field( 'brief_sos_label' );
$brief_items  = c24_field( 'brief_items' ) ?: [];

$company_kicker = c24_field( 'company_kicker' );
$company_title  = c24_field( 'company_title' );
$company_lead   = c24_field( 'company_lead' );
$req_title      = c24_field( 'req_title' );
$req_rows       = c24_field( 'req_rows' ) ?: [];
$google_title   = c24_field( 'google_title' );
$google_text    = c24_field( 'google_text' );
$google_btn_1   = c24_field( 'google_btn_1' );
$google_btn_2   = c24_field( 'google_btn_2' );
$legal_title    = c24_field( 'legal_title' );
$legal          = c24_field( 'legal_links' ) ?: [];

get_header();
?>
<main id="main">

  <!-- ============ 01. HERO КОНТАКТОВ ============ -->
  <section class="sh sh--contact" aria-labelledby="ct-title" data-hero>
    <img class="sh__leaves" src="<?=esc_url( c24_asset( 'img/leaves.webp' ) );?>" alt="" aria-hidden="true" width="1100" height="825">
    <div class="sh__rings" aria-hidden="true"><i></i><i></i></div>

    <div class="container">
      <?php get_template_part( 'template-parts/crumbs' ); ?>

      <div class="ct-hero">
        <div class="ct-hero__content">
          <?php if ( $sh_title ) : ?><h1 class="sh__title" id="ct-title"><?=c24_kses( $sh_title );?></h1><?php endif; ?>
          <?php if ( $sh_lead ) : ?><p class="sh__lead"><?=c24_kses( $sh_lead );?></p><?php endif; ?>

          <ul class="ct-channels" role="list">
            <li class="ct-ch ct-ch--call">
              <span class="ct-ch__deco" aria-hidden="true"><i></i><b></b></span>
              <?php if ( $call_label ) : ?><p class="ct-ch__label"><span class="pulse" aria-hidden="true"></span><?=esc_html( $call_label );?></p><?php endif; ?>
              <a class="ct-ch__phone" href="<?=esc_attr( c24_tel() );?>"><?=c24_phone_nbsp();?></a>
              <?php if ( $call_text ) : ?><p class="ct-ch__text"><?=c24_kses( $call_text );?></p><?php endif; ?>
              <a class="btn btn--lime ct-ch__btn" href="<?=esc_attr( c24_tel() );?>"><svg aria-hidden="true"><use href="#i-phone"/></svg><?=esc_html( c24__( 'Llamar' ) );?></a>
            </li>
            <li class="ct-ch">
              <span class="ct-ch__ic" aria-hidden="true"><svg><use href="#i-wa"/></svg></span>
              <p class="ct-ch__title">WhatsApp</p>
              <?php if ( $wa_text ) : ?><p class="ct-ch__text"><?=c24_kses( $wa_text );?></p><?php endif; ?>
              <a class="ct-ch__link" href="<?=esc_url( c24_wa() );?>"<?=c24_ext();?>><?=esc_html( c24__( 'Escribir por WhatsApp' ) );?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></a>
            </li>
            <li class="ct-ch">
              <span class="ct-ch__ic" aria-hidden="true"><svg><use href="#i-mail"/></svg></span>
              <p class="ct-ch__title">E-mail</p>
              <?php if ( $mail_text ) : ?><p class="ct-ch__text"><?=c24_kses( $mail_text );?></p><?php endif; ?>
              <a class="ct-ch__link" href="mailto:<?=esc_attr( c24_email() );?>"><?=esc_html( c24_email() );?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></a>
            </li>
          </ul>

          <?php if ( $facts ) : ?>
          <dl class="ct-facts">
            <?php foreach ( $facts as $f ) : ?>
            <div>
              <dt><?=c24_icon( $f['icon'] );?><?=esc_html( $f['title'] );?></dt>
              <dd><?=$f['link'] ? '<a href="' . esc_url( c24_href( $f['link'] ) ) . '">' . c24_kses( $f['text'] ) . '</a>' : c24_kses( $f['text'] );?></dd>
            </div>
            <?php endforeach; ?>
          </dl>
          <?php endif; ?>
        </div>

<?=c24_form( 'contact', [ 'title' => c24_field( 'ct_form_title' ), 'lead' => c24_field( 'ct_form_lead' ) ] );?>
      </div>
    </div>
  </section>

  <!-- ============ 02. ЧТО СООБЩИТЬ ДИСПЕТЧЕРУ ============ -->
  <section class="section section--paper docs-sec brief-sec" id="brief" aria-labelledby="brief-title">
    <div class="container docs">
      <div class="docs__aside">
        <?php if ( $brief_kicker ) : ?><p class="kicker kicker--lime"><i aria-hidden="true"></i><?=esc_html( $brief_kicker );?></p><?php endif; ?>
        <?php if ( $brief_title ) : ?><h2 class="h2" id="brief-title"><?=c24_kses( $brief_title );?></h2><?php endif; ?>
        <?php if ( $brief_lead ) : ?><p class="brief__lead"><?=c24_kses( $brief_lead );?></p><?php endif; ?>
        <?php if ( $brief_sos ) : ?>
        <div class="docs__card brief__sos">
          <span class="docs__ic" aria-hidden="true"><svg><use href="#i-warning"/></svg></span>
          <p class="docs__card-text"><?=c24_kses( $brief_sos );?></p>
          <p class="brief__sos-num"><b>112</b><span><?=c24_kses( $brief_sos_l );?></span></p>
        </div>
        <?php endif; ?>
      </div>

      <ol class="docs__list brief__list" role="list">
        <?php foreach ( $brief_items as $it ) : ?>
        <li class="docs-item">
          <h3 class="docs-item__title"><?=c24_kses( $it['title'] );?></h3>
          <?=c24_faq_answer( $it['text'] );?>
        </li>
        <?php endforeach; ?>
      </ol>
    </div>
  </section>

  <?php get_template_part( 'template-parts/geo' ); ?>

  <!-- ============ 04. ДАННЫЕ КОМПАНИИ ============ -->
  <section class="section section--paper ct-company" id="company" aria-labelledby="company-title">
    <div class="container">
      <header class="sec-head">
        <div class="sec-head__main">
          <?php if ( $company_kicker ) : ?><p class="kicker"><i aria-hidden="true"></i><?=esc_html( $company_kicker );?></p><?php endif; ?>
          <?php if ( $company_title ) : ?><h2 class="h2" id="company-title"><?=c24_kses( $company_title );?></h2><?php endif; ?>
        </div>
        <?php if ( $company_lead ) : ?><p class="sec-head__lead"><?=c24_kses( $company_lead );?></p><?php endif; ?>
      </header>

      <div class="ct-company__grid">
        <div class="ct-req">
          <?php if ( $req_title ) : ?><p class="ct-req__title"><?=esc_html( $req_title );?></p><?php endif; ?>
          <dl class="ct-req__list">
            <?php foreach ( $req_rows as $r ) :
              $value = $r['value'];
              $class = '';
              switch ( $r['type'] ) {
                case 'nif':
                  $value = c24_nif() ? esc_html( c24_nif() ) : '—';
                  $class = c24_nif() ? '' : ' class="is-tbd"';
                  break;
                case 'phone':
                  $value = '<a href="' . esc_attr( c24_tel() ) . '">' . c24_phone_nbsp() . '</a>';
                  break;
                case 'email':
                  $value = '<a href="mailto:' . esc_attr( c24_email() ) . '">' . esc_html( c24_email() ) . '</a>';
                  break;
                default:
                  $value = c24_kses( $value );
              }
              ?>
            <div><dt><?=esc_html( $r['label'] );?></dt><dd<?=$class;?>><?=$value;?></dd></div>
            <?php endforeach; ?>
          </dl>
        </div>

        <div class="ct-google">
          <span class="ct-google__deco" aria-hidden="true"><i></i><i></i><b></b></span>
          <p class="ct-google__head"><svg class="ct-google__logo" aria-hidden="true"><use href="#i-google"/></svg><?=c24_kses( $google_title );?></p>
          <p class="ct-google__rating"><b><?=esc_html( c24_rating() );?></b><span class="stars" aria-hidden="true"><?=c24_stars( false );?></span><span><?=esc_html( sprintf( c24__( '%s opiniones' ), c24_reviews_count() ) );?></span></p>
          <?php if ( $google_text ) : ?><p class="ct-google__text"><?=c24_kses( $google_text );?></p><?php endif; ?>
          <div class="ct-google__actions">
            <a class="btn btn--lime" href="<?=esc_url( c24_maps() );?>"<?=c24_ext();?>><svg aria-hidden="true"><use href="#i-pin"/></svg><?=esc_html( $google_btn_1 );?></a>
            <a class="btn btn--outline-light" href="<?=esc_url( c24_maps_review() );?>"<?=c24_ext();?>><svg aria-hidden="true"><use href="#i-star"/></svg><?=esc_html( $google_btn_2 );?></a>
          </div>
        </div>

        <?php if ( $legal ) : ?>
        <nav class="ct-legal" aria-labelledby="legal-title">
          <p class="ct-legal__title" id="legal-title"><?=esc_html( $legal_title );?></p>
          <ul role="list">
            <?php foreach ( $legal as $l ) : ?>
            <li><a href="<?=esc_url( $l['page'] ? get_permalink( $l['page'] ) : '#' );?>"><svg aria-hidden="true"><use href="#i-doc"/></svg><span><?=c24_kses( $l['text'] );?></span><svg class="ct-legal__go" aria-hidden="true"><use href="#i-arrow"/></svg></a></li>
            <?php endforeach; ?>
          </ul>
        </nav>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <?php get_template_part( 'template-parts/faq' ); ?>

</main>
<?php
get_footer();
