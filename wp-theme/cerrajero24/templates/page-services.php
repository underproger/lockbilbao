<?php
/**
 * Template Name: Услуги (каталог)
 */

defined( 'ABSPATH' ) || exit;

$sh_title  = get_field( 'sh_title' );
$sh_lead   = get_field( 'sh_lead' );
$cat_perks = get_field( 'cat_perks' ) ?: [];

$call_label = get_field( 'cat_call_label' );
$call_text  = get_field( 'cat_call_text' );

$help_title = get_field( 'cat_help_title' );
$help_text  = get_field( 'cat_help_text' );
$help_link  = get_field( 'cat_help_link' );

$route_kicker = get_field( 'route_kicker' );
$route_title  = get_field( 'route_title' );
$route_lead   = get_field( 'route_lead' );
$route_call   = get_field( 'route_call' );
$routes       = get_field( 'routes' ) ?: [];

// Карточки услуг по группам (поле «Группа в каталоге» на странице услуги).
$groups = [ 'opening' => [], 'repair' => [] ];
foreach ( c24_services() as $id ) {
	$card                       = c24_card( $id );
	$groups[ $card['group'] ][] = $card;
}

get_header();
?>
<main id="main">

  <!-- ============ HERO КАТАЛОГА ============ -->
  <section class="sh sh--catalog" aria-labelledby="cat-title" data-hero>
    <img class="sh__leaves" src="<?=esc_url( c24_asset( 'img/leaves.webp' ) );?>" alt="" aria-hidden="true" width="1100" height="825">
    <div class="sh__rings" aria-hidden="true"><i></i><i></i></div>

    <div class="container">
      <?php get_template_part( 'template-parts/crumbs' ); ?>

      <div class="cat-hero">
        <div class="cat-hero__content">
          <?php if ( $sh_title ) : ?><h1 class="sh__title" id="cat-title"><?=c24_kses( $sh_title );?></h1><?php endif; ?>
          <?php if ( $sh_lead ) : ?><p class="sh__lead"><?=c24_kses( $sh_lead );?></p><?php endif; ?>

          <?php if ( $cat_perks ) : ?>
          <ul class="sh__perks" role="list">
            <?php foreach ( $cat_perks as $p ) : ?>
            <li><svg aria-hidden="true"><use href="#i-check"/></svg><?=c24_kses( $p['text'] );?></li>
            <?php endforeach; ?>
          </ul>
          <?php endif; ?>
        </div>

        <aside class="cat-call" aria-label="<?=esc_attr( c24__( 'Llamada urgente al cerrajero' ) );?>">
          <span class="cat-call__deco" aria-hidden="true"><i></i><i></i><b></b></span>
          <?php if ( $call_label ) : ?><p class="cat-call__label"><span class="pulse" aria-hidden="true"></span><?=esc_html( $call_label );?></p><?php endif; ?>
          <a class="cat-call__phone" href="<?=esc_attr( c24_tel() );?>"><svg aria-hidden="true"><use href="#i-phone"/></svg><?=esc_html( c24_phone() );?></a>
          <?php if ( $call_text ) : ?><p class="cat-call__text"><?=c24_kses( $call_text );?></p><?php endif; ?>
          <div class="cat-call__actions">
            <a class="btn btn--lime" href="<?=esc_attr( c24_tel() );?>"><svg aria-hidden="true"><use href="#i-phone"/></svg><?=esc_html( c24__( 'Llamar' ) );?></a>
            <a class="btn btn--outline-light" href="<?=esc_url( c24_wa() );?>"<?=c24_ext();?>><svg aria-hidden="true"><use href="#i-wa"/></svg>WhatsApp</a>
          </div>
          <a class="cat-call__rating" href="<?=esc_url( c24_home() . '#reviews' );?>">
            <svg aria-hidden="true"><use href="#i-google"/></svg>
            <span class="stars stars--sm" aria-hidden="true"><?=c24_stars( false );?></span>
            <span><?=esc_html( sprintf( c24__( '%1$s · %2$s opiniones en Google' ), c24_rating(), c24_reviews_count() ) );?></span>
          </a>
        </aside>
      </div>
    </div>
  </section>

  <!-- ============ КАТАЛОГ ============ -->
  <section class="section cat" id="services" aria-label="<?=esc_attr( c24__( 'Catálogo de servicios' ) );?>">
    <div class="cat__deco" aria-hidden="true"><i class="cat__ring cat__ring--a"></i><i class="cat__ring cat__ring--b"></i><i class="cat__spark"></i></div>
    <div class="container">

      <?php foreach ( [ 1 => 'opening', 2 => 'repair' ] as $n => $group ) :
        $kicker = get_field( "grp_{$n}_kicker" );
        $title  = get_field( "grp_{$n}_title" );
        $lead   = get_field( "grp_{$n}_lead" );
        ?>
      <div class="cat-group" id="<?=$group;?>">
        <header class="sec-head">
          <div class="sec-head__main">
            <?php if ( $kicker ) : ?><p class="kicker kicker--lime"><i aria-hidden="true"></i><?=esc_html( $kicker );?></p><?php endif; ?>
            <?php if ( $title ) : ?><h2 class="h2"><?=c24_kses( $title );?></h2><?php endif; ?>
          </div>
          <?php if ( $lead ) : ?><p class="sec-head__lead"><?=c24_kses( $lead );?></p><?php endif; ?>
        </header>

        <ul class="cat-grid" role="list">
          <?php foreach ( $groups[ $group ] as $c ) : $wide = (bool) $c['tag']; ?>
          <li class="svc-card<?=$wide ? ' svc-card--wide' : '';?>">
            <div class="svc-card__top" aria-hidden="true"><?php if ( $wide ) : ?><span class="svc-card__tag"><?=esc_html( $c['tag'] );?></span><?php endif; ?><svg class="svc-card__corner"><use href="#i-arrow"/></svg></div>
            <div class="svc-card__icon"><?=c24_img( $c['icon'], $wide ? [] : [ 'loading' => 'lazy' ] );?></div>
            <h3 class="svc-card__title"><a href="<?=esc_url( $c['url'] );?>"><?=c24_kses( $c['title'] );?></a></h3>
            <?php if ( $c['text_cat'] ?: $c['text'] ) : ?><p class="svc-card__text"><?=c24_kses( $c['text_cat'] ?: $c['text'] );?></p><?php endif; ?>
            <?php if ( $wide && $c['tags'] ) : ?>
            <ul class="svc-card__tags" role="list">
              <?php foreach ( $c['tags'] as $t ) : ?><li><?=esc_html( $t );?></li><?php endforeach; ?>
            </ul>
            <?php endif; ?>
            <div class="svc-card__foot">
              <span class="svc-card__price"><?=c24_price( $c['price'] ) ?? esc_html( c24__( 'a consultar' ) );?></span>
              <span class="svc-card__more" aria-hidden="true"><?=esc_html( c24__( 'Más información' ) );?> <i><svg><use href="#i-arrow"/></svg></i></span>
            </div>
          </li>
          <?php endforeach; ?>

          <?php if ( 'opening' === $group && $help_title ) : ?>
          <li class="cat-help">
            <span class="cat-help__deco" aria-hidden="true"><i></i><b></b></span>
            <p class="cat-help__title"><?=c24_kses( $help_title );?></p>
            <?php if ( $help_text ) : ?><p class="cat-help__text"><?=c24_kses( $help_text );?></p><?php endif; ?>
            <div class="cat-help__actions">
              <a class="btn btn--lime" href="<?=esc_attr( c24_tel() );?>"><svg aria-hidden="true"><use href="#i-phone"/></svg><?=esc_html( c24_phone() );?></a>
              <?php if ( $help_link ) : ?><a class="cat-help__link" href="#choose"><?=esc_html( $help_link );?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></a><?php endif; ?>
            </div>
          </li>
          <?php endif; ?>
        </ul>
      </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- ============ КАКАЯ УСЛУГА НУЖНА ============ -->
  <?php if ( $routes ) : ?>
  <section class="section section--paper route-sec" id="choose" aria-labelledby="choose-title">
    <div class="container route-wrap">
      <header class="route-head">
        <?php if ( $route_kicker ) : ?><p class="kicker kicker--lime"><i aria-hidden="true"></i><?=esc_html( $route_kicker );?></p><?php endif; ?>
        <?php if ( $route_title ) : ?><h2 class="h2" id="choose-title"><?=c24_kses( $route_title );?></h2><?php endif; ?>
        <?php if ( $route_lead ) : ?><p class="route-head__lead"><?=c24_kses( $route_lead );?></p><?php endif; ?>
        <?php if ( $route_call ) : ?><a class="link-arrow route-head__call" href="<?=esc_attr( c24_tel() );?>"><?=esc_html( $route_call );?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></a><?php endif; ?>
      </header>

      <ul class="routes" role="list">
        <?php foreach ( $routes as $r ) : ?>
        <li><a class="route" href="<?=esc_url( $r['page'] ? get_permalink( $r['page'] ) : '#services' );?>"><span class="route__ic" aria-hidden="true"><?=c24_icon( $r['icon'], '', false );?></span><span class="route__q"><?=esc_html( $r['q'] );?></span><span class="route__a"><?=esc_html( $r['a'] );?></span><span class="route__go" aria-hidden="true"><svg><use href="#i-arrow-right"/></svg></span></a></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>
  <?php endif; ?>

  <?php get_template_part( 'template-parts/prices' ); ?>

  <?php get_template_part( 'template-parts/why' ); ?>

  <?php get_template_part( 'template-parts/flow' ); ?>

  <?php get_template_part( 'template-parts/faq' ); ?>

  <?php get_template_part( 'template-parts/final' ); ?>

</main>
<?php
get_footer();
