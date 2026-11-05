<?php
/**
 * Главная страница.
 */

defined( 'ABSPATH' ) || exit;

$hero_eyebrow = get_field( 'hero_eyebrow' );
$hero_title   = get_field( 'hero_title' );
$hero_hours   = get_field( 'hero_hours' );
$hero_photo   = get_field( 'hero_photo' );
$hero_b1      = get_field( 'hero_bubble_1' );
$hero_b2      = get_field( 'hero_bubble_2' );
$hero_chip    = get_field( 'hero_chip' );
$hero_script  = get_field( 'hero_script' );

$svc_kicker = get_field( 'svc_kicker' );
$svc_title  = get_field( 'svc_title' );
$svc_lead   = get_field( 'svc_lead' );

$kinds_kicker = get_field( 'kinds_kicker' );
$kinds_title  = get_field( 'kinds_title' );
$kinds_tabs   = [
	'doors' => [ get_field( 'kinds_tab_1' ), get_field( 'kinds_doors' ) ?: [] ],
	'locks' => [ get_field( 'kinds_tab_2' ), get_field( 'kinds_locks' ) ?: [] ],
];
$brands_label = get_field( 'brands_label' );
$brands       = c24_lines( get_field( 'brands_list' ) );

$safety_photo  = get_field( 'safety_photo' );
$safety_badge  = get_field( 'safety_badge' );
$safety_kicker = get_field( 'safety_kicker' );
$safety_title  = get_field( 'safety_title' );
$safety_quote  = get_field( 'safety_quote' );
$safety_items  = get_field( 'safety_items' ) ?: [];

$about_kicker = get_field( 'about_kicker' );
$about_title  = get_field( 'about_title' );
$about_lead   = get_field( 'about_lead' );
$about_text   = get_field( 'about_text' );
$about_facts  = get_field( 'about_facts' ) ?: [];
$about_photo  = get_field( 'about_photo' );
$about_chip   = get_field( 'about_chip' );

get_header();
?>
<main id="main">

  <!-- ============ 01. HERO ============ -->
  <section class="hero" aria-labelledby="hero-title">
    <img class="hero__leaves" src="<?=esc_url( c24_asset( 'img/leaves.webp' ) );?>" alt="" aria-hidden="true" width="1100" height="825" data-parallax="-14">

    <div class="container hero__head">
      <span class="kicker-bar" aria-hidden="true"></span>
      <?php if ( $hero_eyebrow ) : ?><p class="eyebrow"><?=esc_html( $hero_eyebrow );?></p><?php endif; ?>
      <?php if ( $hero_title ) : ?><h1 class="hero__title" id="hero-title"><?=c24_kses( $hero_title );?></h1><?php endif; ?>

      <div class="hero__actions">
        <?php if ( $hero_hours ) : ?><p class="hero__hours"><span class="icon-circle"><svg aria-hidden="true"><use href="#i-clock"/></svg></span><?=esc_html( $hero_hours );?></p><?php endif; ?>
        <a class="btn btn--lime btn--xl hero__call" href="<?=esc_attr( c24_tel() );?>">
          <svg class="btn__lead-icon" aria-hidden="true"><use href="#i-phone"/></svg>
          <span><?=esc_html( c24_phone() );?></span>
          <svg aria-hidden="true"><use href="#i-arrow"/></svg>
        </a>
        <span class="divider-v" aria-hidden="true"></span>
        <a class="hero__prices" href="#prices"><?=esc_html( c24__( 'Ver precios' ) );?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></a>
        <span class="divider-v" aria-hidden="true"></span>
        <a class="rating" href="#reviews">
          <svg class="rating__g" aria-hidden="true"><use href="#i-google"/></svg>
          <span class="stars" aria-hidden="true"><?=c24_stars( false );?></span>
          <span class="rating__text"><?=esc_html( sprintf( c24__( 'Valoración media %1$s de %2$s opiniones' ), c24_rating(), c24_reviews_count() ) );?></span>
        </a>
      </div>
    </div>

    <div class="container hero__stage">
      <div class="stage" data-stage>
        <img class="stage__orbit" src="<?=esc_url( c24_asset( 'img/orbit.webp' ) );?>" alt="" aria-hidden="true" width="1184" height="1031">
        <div class="stage__disc" aria-hidden="true"></div>
        <div class="stage__cut">
          <?=c24_img( $hero_photo, [ 'class' => 'stage__master', 'sizes' => '(max-width: 760px) 70vw, 640px', 'fetchpriority' => 'high' ] );?>
        </div>
        <?php if ( $hero_b1 ) : ?>
        <figure class="stage__bubble stage__bubble--gug" data-parallax="10">
          <?=c24_img( $hero_b1, [ 'loading' => 'eager' ] );?>
          <i class="stage__dot" aria-hidden="true"></i>
        </figure>
        <?php endif; ?>
        <?php if ( $hero_b2 ) : ?>
        <figure class="stage__bubble stage__bubble--old" data-parallax="16">
          <?=c24_img( $hero_b2 );?>
          <i class="stage__dot" aria-hidden="true"></i>
        </figure>
        <?php endif; ?>
        <img class="stage__lock" src="<?=esc_url( c24_asset( 'img/lock-badge.webp' ) );?>" alt="" aria-hidden="true" width="240" height="240" data-parallax="8">
      </div>

      <?php if ( $hero_chip ) : ?><p class="stage__chip"><svg aria-hidden="true"><use href="#i-pin"/></svg><?=esc_html( $hero_chip );?></p><?php endif; ?>
      <?php if ( $hero_script ) : ?><p class="stage__script" aria-hidden="true"><?=c24_br( $hero_script );?><svg class="stage__stroke" viewBox="0 0 220 24" preserveAspectRatio="none"><path d="M4 18C60 10 140 6 216 4" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round"/></svg></p><?php endif; ?>
    </div>
  </section>

  <!-- ============ 04. УСЛУГИ ============ -->
  <section class="section section--sheet svc" id="services" aria-labelledby="services-title">
    <div class="svc__deco" aria-hidden="true"><i class="svc__ring svc__ring--a"></i><i class="svc__ring svc__ring--b"></i><i class="svc__spark"></i></div>
    <div class="container">
      <header class="svc__head">
        <div class="svc__head-main">
          <?php if ( $svc_kicker ) : ?><p class="kicker kicker--lime"><i aria-hidden="true"></i><?=esc_html( $svc_kicker );?></p><?php endif; ?>
          <?php if ( $svc_title ) : ?><h2 class="h2 svc__title" id="services-title"><?=c24_kses( $svc_title );?></h2><?php endif; ?>
          <?php if ( $svc_lead ) : ?><p class="svc__lead"><?=c24_kses( $svc_lead );?></p><?php endif; ?>
        </div>
      </header>

      <?php get_template_part( 'template-parts/svc-slider', null, [ 'ids' => c24_services(), 'aria' => c24__( 'Servicios: desliza en horizontal' ) ] ); ?>
    </div>
  </section>

  <?php get_template_part( 'template-parts/prices' ); ?>

  <?php get_template_part( 'template-parts/why' ); ?>

  <?php get_template_part( 'template-parts/flow' ); ?>

  <!-- ============ 10. ТИПЫ ДВЕРЕЙ И ЗАМКОВ ============ -->
  <section class="section kinds" id="types" aria-labelledby="types-title">
    <div class="kinds__deco" aria-hidden="true"><i class="kinds__ring kinds__ring--a"></i><i class="kinds__ring kinds__ring--b"></i><i class="kinds__spark"></i></div>
    <div class="container">
      <header class="sec-head kinds__head">
        <div class="sec-head__main">
          <?php if ( $kinds_kicker ) : ?><p class="kicker"><i aria-hidden="true"></i><?=esc_html( $kinds_kicker );?></p><?php endif; ?>
          <?php if ( $kinds_title ) : ?><h2 class="h2" id="types-title"><?=c24_kses( $kinds_title );?></h2><?php endif; ?>
        </div>
        <div class="tabs__list" role="tablist" aria-label="<?=esc_attr( $kinds_kicker );?>" data-tablist>
          <?php $first = true; foreach ( $kinds_tabs as $key => $tab ) : ?>
          <button class="tabs__tab" role="tab" id="tab-<?=$key;?>" aria-selected="<?=$first ? 'true' : 'false';?>" aria-controls="panel-<?=$key;?>"<?=$first ? '' : ' tabindex="-1"';?> type="button"><?=esc_html( $tab[0] );?></button>
          <?php $first = false; endforeach; ?>
        </div>
      </header>

      <?php $first = true; foreach ( $kinds_tabs as $key => $tab ) : ?>
      <div class="tabs__panel" role="tabpanel" id="panel-<?=$key;?>" aria-labelledby="tab-<?=$key;?>" tabindex="0"<?=$first ? '' : ' hidden';?>>
        <ul class="types" role="list">
          <?php foreach ( $tab[1] as $c ) : ?>
          <li class="type-card">
            <span class="type-card__ic"><?=c24_icon( $c['icon'] );?></span>
            <?php if ( $c['title'] ) : ?><h3 class="type-card__title"><?=c24_kses( $c['title'] );?></h3><?php endif; ?>
            <p class="type-card__text"><?=c24_kses( $c['text'] );?></p>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>
      <?php $first = false; endforeach; ?>

      <?php if ( $brands ) : ?>
      <div class="brands">
        <?php if ( $brands_label ) : ?><p class="brands__label"><?=esc_html( $brands_label );?></p><?php endif; ?>
        <ul class="brands__list" role="list">
          <?php foreach ( $brands as $b ) : ?><li><?=esc_html( $b );?></li><?php endforeach; ?>
        </ul>
      </div>
      <?php endif; ?>
    </div>
  </section>

  <!-- ============ 11. БЕЗОПАСНОСТЬ ============ -->
  <section class="section section--paper" id="safety" aria-labelledby="safety-title">
    <div class="container safety">
      <figure class="safety__visual">
        <img class="safety__orbit" src="<?=esc_url( c24_asset( 'img/orbit.webp' ) );?>" alt="" aria-hidden="true" width="1184" height="1031" loading="lazy">
        <?=c24_img( $safety_photo, [ 'class' => 'safety__photo', 'loading' => 'lazy' ] );?>
        <?php if ( $safety_badge ) : ?><figcaption class="safety__badge"><svg aria-hidden="true"><use href="#i-check"/></svg><?=esc_html( $safety_badge );?></figcaption><?php endif; ?>
      </figure>

      <div class="safety__content">
        <?php if ( $safety_kicker ) : ?><p class="kicker"><i aria-hidden="true"></i><?=esc_html( $safety_kicker );?></p><?php endif; ?>
        <?php if ( $safety_title ) : ?><h2 class="h2" id="safety-title"><?=c24_kses( $safety_title );?></h2><?php endif; ?>
        <?php if ( $safety_quote ) : ?><blockquote class="safety__quote"><?=c24_kses( $safety_quote );?></blockquote><?php endif; ?>

        <ul class="safety__list" role="list">
          <?php foreach ( $safety_items as $it ) : ?>
          <li>
            <?php if ( $it['title'] ) : ?><h3><?=c24_kses( $it['title'] );?></h3><?php endif; ?>
            <p><?=c24_kses( $it['text'] );?></p>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </section>

  <?php get_template_part( 'template-parts/reviews' ); ?>

  <!-- ============ 14. О КОМПАНИИ ============ -->
  <section class="section about-sec" id="about" aria-labelledby="about-title">
    <div class="container about">
      <div class="about__content">
        <?php if ( $about_kicker ) : ?><p class="kicker"><i aria-hidden="true"></i><?=esc_html( $about_kicker );?></p><?php endif; ?>
        <?php if ( $about_title ) : ?><h2 class="h2 about__title" id="about-title"><?=c24_kses( $about_title );?></h2><?php endif; ?>
        <?php if ( $about_lead ) : ?><p class="lead"><?=c24_kses( $about_lead );?></p><?php endif; ?>
        <?php if ( $about_text ) : ?><p class="about__text"><?=c24_kses( $about_text );?></p><?php endif; ?>

        <?php if ( $about_facts ) : ?>
        <ul class="facts" role="list">
          <?php foreach ( $about_facts as $f ) : ?>
          <li class="fact"><span class="fact__ic" aria-hidden="true"><?=c24_icon( $f['icon'], '', false );?></span><b class="fact__num"><?=c24_kses( $f['num'] );?></b><span class="fact__text"><?=c24_kses( $f['text'] );?></span></li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>
      </div>

      <figure class="about__visual">
        <span class="about__blob" aria-hidden="true"></span>
        <svg class="about__orbit" viewBox="0 0 100 100" aria-hidden="true" focusable="false">
          <path d="M74 -20C30 -16 2 6 -3 40C-7 76 8 104 40 111C70 118 98 106 118 86"/>
          <path d="M60 124C76 104 94 92 124 88"/>
          <circle class="about__orbit-dot" cx="13.59" cy="5.34" r="1.25"/>
          <circle class="about__orbit-dot about__orbit-dot--pale" cx="94.45" cy="96.03" r="3.1"/>
        </svg>
        <?=c24_img( $about_photo, [ 'class' => 'about__photo', 'loading' => 'lazy' ] );?>
        <?php if ( $about_chip ) : ?><figcaption class="stage__chip about__chip"><svg aria-hidden="true"><use href="#i-pin"/></svg><?=esc_html( $about_chip );?></figcaption><?php endif; ?>
      </figure>
    </div>
  </section>

  <?php get_template_part( 'template-parts/geo' ); ?>

  <?php get_template_part( 'template-parts/longread' ); ?>

  <?php get_template_part( 'template-parts/faq' ); ?>

  <?php get_template_part( 'template-parts/final' ); ?>

</main>
<?php
get_footer();
