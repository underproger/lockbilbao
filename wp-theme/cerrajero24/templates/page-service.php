<?php
/**
 * Template Name: Услуга
 */

defined( 'ABSPATH' ) || exit;

$sh_title  = get_field( 'sh_title' );
$sh_lead   = get_field( 'sh_lead' );
$sh_photo  = get_field( 'sh_photo' );
$sh_bubble = get_field( 'sh_bubble' );
$sh_chip   = get_field( 'sh_chip' );

$quick_perks = get_field( 'quick_perks' ) ?: [];

$sit_kicker  = get_field( 'sit_kicker' );
$sit_title   = get_field( 'sit_title' );
$sit_lead    = get_field( 'sit_lead' );
$sit_cards   = get_field( 'sit_cards' ) ?: [];
$sit_cta     = get_field( 'sit_cta_title' );
$sit_cta_sub = get_field( 'sit_cta_sub' );
$sit_cta_btn = get_field( 'sit_cta_btn' );

$sp_kicker   = get_field( 'sp_kicker' );
$sp_title    = get_field( 'sp_title' );
$sp_lead     = get_field( 'sp_lead' );
$sp_caption  = get_field( 'sp_caption' );
$sp_rows     = get_field( 'sp_rows' ) ?: [];
$sp_notes    = get_field( 'sp_notes' ) ?: [];
$sp_cta      = get_field( 'sp_cta_title' );
$sp_cta_text = get_field( 'sp_cta_text' );

$calc_kicker = get_field( 'calc_kicker' );
$calc_title  = get_field( 'calc_title' );
$calc_lead   = get_field( 'calc_lead' );
$calc_items  = get_field( 'calc_items' ) ?: [];
$calc_total  = get_field( 'calc_total_title' );
$calc_ttext  = get_field( 'calc_total_text' );
$calc_note   = get_field( 'calc_note' );

$est_kicker = get_field( 'est_kicker' );
$est_title  = get_field( 'est_title' );
$est_lead   = get_field( 'est_lead' );

$types_kicker = get_field( 'types_kicker' );
$types_title  = get_field( 'types_title' );
$types_lead   = get_field( 'types_lead' );
$types_cards  = get_field( 'types_cards' ) ?: [];

$nd_id     = get_field( 'nd_id' ) ?: 'no-damage';
$nd_photo  = get_field( 'nd_photo' );
$nd_badge  = get_field( 'nd_badge' );
$nd_kicker = get_field( 'nd_kicker' );
$nd_title  = get_field( 'nd_title' );
$nd_lead   = get_field( 'nd_lead' );
$nd_items  = get_field( 'nd_items' ) ?: [];

$edge_photo  = get_field( 'edge_photo' );
$edge_city   = get_field( 'edge_city' );
$edge_chip   = get_field( 'edge_chip' );
$edge_kicker = get_field( 'edge_kicker' );
$edge_title  = get_field( 'edge_title' );
$edge_items  = get_field( 'edge_items' ) ?: [];

$docs_kicker = get_field( 'docs_kicker' );
$docs_title  = get_field( 'docs_title' );
$docs_card   = get_field( 'docs_card_text' );
$docs_label  = get_field( 'docs_card_label' );
$docs_chips  = get_field( 'docs_chips' ) ?: [];
$docs_items  = get_field( 'docs_items' ) ?: [];

$more_kicker = get_field( 'more_kicker' );
$more_title  = get_field( 'more_title' );

get_header();
?>
<main id="main">

  <!-- ============ 02. HERO УСЛУГИ ============ -->
  <section class="sh sh--with-bar" aria-labelledby="sh-title" data-hero>
    <img class="sh__leaves" src="<?=esc_url( c24_asset( 'img/leaves.webp' ) );?>" alt="" aria-hidden="true" width="1100" height="825">
    <div class="sh__rings" aria-hidden="true"><i></i><i></i></div>

    <div class="container">
      <?php get_template_part( 'template-parts/crumbs' ); ?>

      <div class="sh__grid">
        <div class="sh__content">
          <?php if ( $sh_title ) : ?><h1 class="sh__title" id="sh-title"><?=c24_kses( $sh_title );?></h1><?php endif; ?>
          <?php if ( $sh_lead ) : ?><p class="sh__lead"><?=c24_kses( $sh_lead );?></p><?php endif; ?>

          <div class="sh__actions">
            <a class="btn btn--lime btn--xl sh__call" href="<?=esc_attr( c24_tel() );?>">
              <svg class="btn__lead-icon" aria-hidden="true"><use href="#i-phone"/></svg>
              <span><?=esc_html( c24_phone() );?></span>
              <svg aria-hidden="true"><use href="#i-arrow"/></svg>
            </a>
            <a class="hero__prices" href="#prices"><?=esc_html( c24__( 'Ver precios' ) );?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></a>
          </div>

          <a class="rating sh__rating" href="#reviews">
            <svg class="rating__g" aria-hidden="true"><use href="#i-google"/></svg>
            <span class="stars" aria-hidden="true"><?=c24_stars( false );?></span>
            <span class="rating__text"><?=esc_html( sprintf( c24__( 'Valoración media %1$s de %2$s opiniones' ), c24_rating(), c24_reviews_count() ) );?></span>
          </a>
        </div>

        <div class="sh__visual">
          <div class="stage">
            <img class="stage__orbit" src="<?=esc_url( c24_asset( 'img/orbit.webp' ) );?>" alt="" aria-hidden="true" width="1184" height="1031">
            <div class="stage__disc" aria-hidden="true"></div>
            <div class="stage__cut">
              <?=c24_img( $sh_photo, [ 'class' => 'stage__photo', 'sizes' => '(max-width: 760px) 80vw, 580px', 'fetchpriority' => 'high' ] );?>
            </div>
            <?php if ( $sh_bubble ) : ?>
            <figure class="stage__bubble stage__bubble--gug stage__bubble--svc">
              <?=c24_img( $sh_bubble );?>
              <i class="stage__dot" aria-hidden="true"></i>
            </figure>
            <?php endif; ?>
            <img class="stage__lock" src="<?=esc_url( c24_asset( 'img/lock-badge.webp' ) );?>" alt="" aria-hidden="true" width="240" height="240">
            <p class="sh__badge"><svg aria-hidden="true"><use href="#i-clock"/></svg><span><?=esc_html( c24__( 'Trabajamos' ) );?></span><b>24/7</b></p>
          </div>
          <?php if ( $sh_chip ) : ?><p class="sh__chip"><svg aria-hidden="true"><use href="#i-pin"/></svg><?=esc_html( $sh_chip );?></p><?php endif; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- ============ 02a. ПРЕИМУЩЕСТВА И ЗАЯВКА ============ -->
  <section class="quick" aria-label="<?=esc_attr( c24__( 'Solicitud rápida de cerrajero' ) );?>">
    <div class="container">
      <div class="quick__card">
        <span class="quick__deco" aria-hidden="true"><i></i><i></i><b></b></span>
        <?php if ( $quick_perks ) : ?>
        <ul class="quick__perks" role="list">
          <?php foreach ( $quick_perks as $p ) : ?>
          <li><span class="quick__ic" aria-hidden="true"><?=c24_icon( $p['icon'], '', false );?></span><span class="quick__txt"><b><?=c24_kses( $p['title'] );?></b><?=c24_kses( $p['text'] );?></span></li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>

<?=c24_form( 'quick', [ 'title' => get_field( 'quick_form_title' ) ] );?>
      </div>
    </div>
  </section>

  <!-- ============ 03. СИТУАЦИИ ============ -->
  <section class="section sit" id="situations" aria-labelledby="sit-title">
    <div class="sit__deco" aria-hidden="true"><i class="sit__ring sit__ring--a"></i><i class="sit__ring sit__ring--b"></i></div>
    <div class="container">
      <header class="sec-head svc-head">
        <div class="sec-head__main">
          <?php if ( $sit_kicker ) : ?><p class="kicker kicker--lime"><i aria-hidden="true"></i><?=esc_html( $sit_kicker );?></p><?php endif; ?>
          <?php if ( $sit_title ) : ?><h2 class="h2" id="sit-title"><?=c24_kses( $sit_title );?></h2><?php endif; ?>
        </div>
        <?php if ( $sit_lead ) : ?><p class="sec-head__lead"><?=c24_kses( $sit_lead );?></p><?php endif; ?>
      </header>

      <ul class="sit-grid" role="list">
        <?php foreach ( $sit_cards as $c ) : ?>
        <li class="sit-card">
          <span class="sit-card__ic" aria-hidden="true"><?=c24_icon( $c['icon'], '', false );?></span>
          <?php if ( $c['title'] ) : ?><h3 class="sit-card__title"><?=c24_kses( $c['title'] );?></h3><?php endif; ?>
          <p class="sit-card__text"><?=c24_kses( $c['text'] );?></p>
        </li>
        <?php endforeach; ?>
      </ul>

      <?php if ( $sit_cta ) : ?>
      <div class="flow-cta sit__cta">
        <span class="flow-cta__ic" aria-hidden="true"><svg><use href="#i-phone-ring"/></svg></span>
        <div class="flow-cta__text">
          <p class="flow-cta__title"><?=c24_kses( $sit_cta );?></p>
          <?php if ( $sit_cta_sub ) : ?><p class="flow-cta__sub"><?=c24_kses( $sit_cta_sub );?></p><?php endif; ?>
        </div>
        <a class="btn btn--lime btn--lg flow-cta__btn" href="<?=esc_attr( c24_tel() );?>"><?=esc_html( $sit_cta_btn );?> <svg aria-hidden="true"><use href="#i-arrow-right"/></svg></a>
      </div>
      <?php endif; ?>
    </div>
  </section>

  <!-- ============ 04. ЦЕНЫ ============ -->
  <section class="section section--paper sp" id="prices" aria-labelledby="prices-title">
    <img class="pr__leaves" src="<?=esc_url( c24_asset( 'img/leaves.webp' ) );?>" alt="" aria-hidden="true" width="1100" height="825" loading="lazy">
    <div class="container">
      <header class="sec-head">
        <div class="sec-head__main">
          <?php if ( $sp_kicker ) : ?><p class="kicker kicker--lime"><i aria-hidden="true"></i><?=esc_html( $sp_kicker );?></p><?php endif; ?>
          <?php if ( $sp_title ) : ?><h2 class="h2" id="prices-title"><?=c24_kses( $sp_title );?></h2><?php endif; ?>
        </div>
        <?php if ( $sp_lead ) : ?><p class="sec-head__lead"><?=c24_kses( $sp_lead );?></p><?php endif; ?>
      </header>

      <div class="sp__grid">
        <div class="pr__card sp__card">
          <table class="pr-table">
            <?php if ( $sp_caption ) : ?><caption class="visually-hidden"><?=esc_html( $sp_caption );?></caption><?php endif; ?>
            <thead>
              <tr><th scope="col"><?=esc_html( get_field( 'sp_col_1' ) );?></th><th scope="col"><?=esc_html( get_field( 'sp_col_2' ) );?></th></tr>
            </thead>
            <tbody>
              <?php foreach ( $sp_rows as $r ) : $p = c24_price( $r['price'] ); ?>
              <tr><th scope="row"><?=c24_icon( $r['icon'] );?><?=esc_html( $r['label'] );?></th><?php if ( null !== $p ) : ?><td><?=$p;?></td><?php else : ?><td class="is-tbd"><?=esc_html( c24__( 'a consultar' ) );?></td><?php endif; ?></tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <div class="sp__aside">
          <?php if ( $sp_notes ) : ?>
          <dl class="sp-notes">
            <?php foreach ( $sp_notes as $n ) : ?>
            <div>
              <dt><?=c24_kses( $n['title'] );?></dt>
              <dd><?=c24_kses( $n['text'] );?></dd>
            </div>
            <?php endforeach; ?>
          </dl>
          <?php endif; ?>

          <button class="pr-cta sp__cta" type="button" data-modal-open="price-modal">
            <span class="pr-cta__ic"><svg aria-hidden="true"><use href="#i-phone"/></svg></span>
            <span class="pr-cta__text"><b><?=esc_html( $sp_cta );?></b><span><?=esc_html( $sp_cta_text );?></span></span>
            <svg class="pr-cta__chev" aria-hidden="true"><use href="#i-chev-right"/></svg>
          </button>
        </div>
      </div>
    </div>
  </section>

  <!-- ============ 05. ИЗ ЧЕГО СКЛАДЫВАЕТСЯ ЦЕНА ============ -->
  <section class="section calc-sec" aria-labelledby="factors-title">
    <div class="calc-sec__deco" aria-hidden="true"><i></i><i></i><b></b></div>
    <div class="container">
      <header class="sec-head">
        <div class="sec-head__main">
          <?php if ( $calc_kicker ) : ?><p class="kicker kicker--lime"><i aria-hidden="true"></i><?=esc_html( $calc_kicker );?></p><?php endif; ?>
          <?php if ( $calc_title ) : ?><h2 class="h2" id="factors-title"><?=c24_kses( $calc_title );?></h2><?php endif; ?>
        </div>
        <?php if ( $calc_lead ) : ?><p class="sec-head__lead"><?=c24_kses( $calc_lead );?></p><?php endif; ?>
      </header>

      <ul class="calc" role="list">
        <?php foreach ( $calc_items as $i => $c ) : ?>
        <li class="calc-item">
          <span class="calc-item__num" aria-hidden="true"><?=c24_num( $i );?></span>
          <span class="calc-item__ic" aria-hidden="true"><?=c24_icon( $c['icon'], '', false );?></span>
          <?php if ( $c['title'] ) : ?><h3 class="calc-item__title"><?=c24_kses( $c['title'] );?></h3><?php endif; ?>
          <p class="calc-item__text"><?=c24_kses( $c['text'] );?></p>
        </li>
        <?php endforeach; ?>
        <?php if ( $calc_total ) : ?>
        <li class="calc-item calc-item--total">
          <span class="calc-item__deco" aria-hidden="true"><i></i><b></b></span>
          <span class="calc-item__ic" aria-hidden="true"><svg><use href="#i-doc-euro"/></svg></span>
          <h3 class="calc-item__title"><?=c24_kses( $calc_total );?></h3>
          <p class="calc-item__text"><?=c24_kses( $calc_ttext );?></p>
        </li>
        <?php endif; ?>
      </ul>
      <?php if ( $calc_note ) : ?><p class="calc-sec__note"><svg aria-hidden="true"><use href="#i-check"/></svg><?=c24_kses( $calc_note );?></p><?php endif; ?>
    </div>
  </section>

  <!-- ============ 06. ВЫЗОВ МАСТЕРА ============ -->
  <section class="section est-sec" id="call" aria-labelledby="est-title">
    <div class="container">
      <div class="est">
        <span class="est__deco" aria-hidden="true"><i></i><i></i><b></b></span>

        <div class="est__intro">
          <?php if ( $est_kicker ) : ?><p class="kicker kicker--light"><i aria-hidden="true"></i><?=esc_html( $est_kicker );?></p><?php endif; ?>
          <?php if ( $est_title ) : ?><h2 class="h2 h2--light est__title" id="est-title"><?=c24_kses( $est_title );?></h2><?php endif; ?>
          <?php if ( $est_lead ) : ?><p class="est__lead"><?=c24_kses( $est_lead );?></p><?php endif; ?>
        </div>

<?=c24_form( 'est' );?>
      </div>
    </div>
  </section>

  <!-- ============ 07. ТИПЫ ============ -->
  <section class="section kinds" id="types" aria-labelledby="types-title">
    <div class="kinds__deco" aria-hidden="true"><i class="kinds__ring kinds__ring--a"></i><i class="kinds__ring kinds__ring--b"></i><i class="kinds__spark"></i></div>
    <div class="container">
      <header class="sec-head">
        <div class="sec-head__main">
          <?php if ( $types_kicker ) : ?><p class="kicker"><i aria-hidden="true"></i><?=esc_html( $types_kicker );?></p><?php endif; ?>
          <?php if ( $types_title ) : ?><h2 class="h2" id="types-title"><?=c24_kses( $types_title );?></h2><?php endif; ?>
        </div>
        <?php if ( $types_lead ) : ?><p class="sec-head__lead"><?=c24_kses( $types_lead );?></p><?php endif; ?>
      </header>

      <ul class="types types--3" role="list">
        <?php foreach ( $types_cards as $c ) : ?>
        <li class="type-card">
          <span class="type-card__ic"><?=c24_icon( $c['icon'] );?></span>
          <?php if ( $c['title'] ) : ?><h3 class="type-card__title"><?=c24_kses( $c['title'] );?></h3><?php endif; ?>
          <p class="type-card__text"><?=c24_kses( $c['text'] );?></p>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>

  <!-- ============ 08. БЕЗ ПОВРЕЖДЕНИЙ ============ -->
  <section class="section section--paper nd" id="<?=esc_attr( $nd_id );?>" aria-labelledby="nd-title">
    <div class="container nd__grid">
      <figure class="nd__visual nd__visual--cover">
        <?=c24_img( $nd_photo, [ 'class' => 'nd__cover', 'loading' => 'lazy' ] );?>
        <?php if ( $nd_badge ) : ?><figcaption class="nd__badge"><svg aria-hidden="true"><use href="#i-check"/></svg><?=esc_html( $nd_badge );?></figcaption><?php endif; ?>
      </figure>

      <div class="nd__content">
        <?php if ( $nd_kicker ) : ?><p class="kicker kicker--lime"><i aria-hidden="true"></i><?=esc_html( $nd_kicker );?></p><?php endif; ?>
        <?php if ( $nd_title ) : ?><h2 class="h2" id="nd-title"><?=c24_kses( $nd_title );?></h2><?php endif; ?>
        <?php if ( $nd_lead ) : ?><p class="nd__lead"><?=c24_kses( $nd_lead );?></p><?php endif; ?>

        <div class="nd__list">
          <?php foreach ( $nd_items as $i => $it ) : $n = $i + 1; ?>
          <section class="nd-item" aria-labelledby="nd-<?=$n;?>">
            <span class="nd-item__ic" aria-hidden="true"><?=c24_icon( $it['icon'], '', false );?></span>
            <h3 class="nd-item__title" id="nd-<?=$n;?>"><?=c24_kses( $it['title'] );?></h3>
            <?=c24_faq_answer( $it['text'] );?>
          </section>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>

  <?php get_template_part( 'template-parts/flow', null, [ 'line' => true ] ); ?>

  <!-- ============ 10. ПРЕИМУЩЕСТВА ============ -->
  <section class="section edge-sec" id="why" aria-labelledby="edge-title">
    <div class="container edge">
      <div class="edge__media">
        <figure class="edge__photo edge__photo--main edge__photo--cover">
          <?=c24_img( $edge_photo, [ 'loading' => 'lazy' ] );?>
        </figure>
        <figure class="edge__photo edge__photo--city">
          <?=c24_img( $edge_city, [ 'loading' => 'lazy' ] );?>
          <?php if ( $edge_chip ) : ?><figcaption class="why-photo__chip"><svg aria-hidden="true"><use href="#i-pin"/></svg><?=esc_html( $edge_chip );?></figcaption><?php endif; ?>
        </figure>
      </div>

      <div class="edge__content">
        <?php if ( $edge_kicker ) : ?><p class="kicker kicker--lime"><i aria-hidden="true"></i><?=esc_html( $edge_kicker );?></p><?php endif; ?>
        <?php if ( $edge_title ) : ?><h2 class="h2" id="edge-title"><?=c24_kses( $edge_title );?></h2><?php endif; ?>

        <ul class="edge-list" role="list">
          <?php foreach ( $edge_items as $it ) : ?>
          <li class="edge-item">
            <span class="edge-item__ic" aria-hidden="true"><?=c24_icon( $it['icon'], '', false );?></span>
            <?php if ( $it['title'] ) : ?><h3 class="edge-item__title"><?=c24_kses( $it['title'] );?></h3><?php endif; ?>
            <p class="edge-item__text"><?=c24_kses( $it['text'] );?></p>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </section>

  <?php get_template_part( 'template-parts/reviews' ); ?>

  <!-- ============ 14. ДОКУМЕНТЫ ============ -->
  <section class="section docs-sec" id="documents" aria-labelledby="docs-title">
    <div class="container docs">
      <div class="docs__aside">
        <?php if ( $docs_kicker ) : ?><p class="kicker kicker--lime"><i aria-hidden="true"></i><?=esc_html( $docs_kicker );?></p><?php endif; ?>
        <?php if ( $docs_title ) : ?><h2 class="h2" id="docs-title"><?=c24_kses( $docs_title );?></h2><?php endif; ?>
        <div class="docs__card">
          <span class="docs__ic" aria-hidden="true"><svg><use href="#i-doc"/></svg></span>
          <?php if ( $docs_card ) : ?><p class="docs__card-text"><?=c24_kses( $docs_card );?></p><?php endif; ?>
          <?php if ( $docs_label ) : ?><p class="docs__card-label"><?=esc_html( $docs_label );?></p><?php endif; ?>
          <?php if ( $docs_chips ) : ?>
          <ul class="docs__chips" role="list">
            <?php foreach ( $docs_chips as $chip ) : ?><li<?=! empty( $chip['es'] ) && 'es' !== c24_lang() ? ' lang="es"' : '';?>><?=esc_html( $chip['text'] );?></li><?php endforeach; ?>
          </ul>
          <?php endif; ?>
        </div>
      </div>

      <div class="docs__list">
        <?php foreach ( $docs_items as $i => $d ) : $n = $i + 1; ?>
        <section class="docs-item" aria-labelledby="doc-<?=$n;?>">
          <h3 class="docs-item__title" id="doc-<?=$n;?>"><?=c24_kses( $d['title'] );?></h3>
          <?=c24_faq_answer( $d['text'] );?>
        </section>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <?php get_template_part( 'template-parts/geo' ); ?>

  <?php get_template_part( 'template-parts/longread' ); ?>

  <?php get_template_part( 'template-parts/faq' ); ?>

  <!-- ============ 18. ДРУГИЕ УСЛУГИ ============ -->
  <section class="section section--paper more-sec" id="more" aria-labelledby="more-title">
    <div class="container">
      <header class="sec-head">
        <div class="sec-head__main">
          <?php if ( $more_kicker ) : ?><p class="kicker kicker--lime"><i aria-hidden="true"></i><?=esc_html( $more_kicker );?></p><?php endif; ?>
          <?php if ( $more_title ) : ?><h2 class="h2" id="more-title"><?=c24_kses( $more_title );?></h2><?php endif; ?>
        </div>
        <a class="link-arrow sec-head__link" href="<?=esc_url( c24_page_url( 'services' ) );?>"><?=esc_html( c24__( 'Todos los servicios' ) );?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></a>
      </header>

      <?php get_template_part( 'template-parts/svc-slider', null, [ 'ids' => c24_services( get_the_ID() ), 'aria' => c24__( 'Otros servicios: desliza en horizontal' ) ] ); ?>
    </div>
  </section>

  <?php get_template_part( 'template-parts/final' ); ?>

</main>
<?php
get_footer();
