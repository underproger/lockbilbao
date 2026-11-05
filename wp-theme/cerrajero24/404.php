<?php
/**
 * Страница «не найдено». Тексты — «Языки → Перевод строк».
 */

defined( 'ABSPATH' ) || exit;

$door     = c24_page_id( 'svc-puertas' );
$door_c   = $door ? c24_card( $door ) : null;
$services = count( c24_services() );

get_header();
?>
<main id="main">

  <section class="sh nf" aria-labelledby="nf-title">
    <img class="sh__leaves" src="<?=esc_url( c24_asset( 'img/leaves.webp' ) );?>" alt="" aria-hidden="true" width="1100" height="825">
    <div class="sh__rings" aria-hidden="true"><i></i><i></i></div>

    <div class="container">
      <div class="nf__grid">
        <div class="nf__content">
          <p class="kicker kicker--lime"><i aria-hidden="true"></i><?=esc_html( c24__( 'Error 404' ) );?></p>
          <h1 class="nf__title" id="nf-title"><?=c24_kses( c24__( 'Página no&nbsp;encontrada' ) );?></h1>
          <p class="nf__lead"><?=esc_html( c24__( 'Parece que el enlace está desactualizado o hay un error en la dirección. Vaya a la página de inicio o al catálogo de servicios. Y si no puede entrar en casa ahora mismo, llame: el cerrajero de guardia atiende las 24 horas.' ) );?></p>

          <div class="nf__actions">
            <a class="btn btn--dark btn--lg" href="<?=esc_url( c24_home() );?>"><?=esc_html( c24__( 'Ir al inicio' ) );?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></a>
            <a class="btn btn--ghost btn--lg" href="<?=esc_url( c24_page_url( 'services' ) );?>"><?=esc_html( c24__( 'Todos los servicios' ) );?></a>
          </div>

          <a class="nf__call" href="<?=esc_attr( c24_tel() );?>">
            <span class="nf__call-ic" aria-hidden="true"><svg><use href="#i-phone"/></svg></span>
            <span class="nf__call-txt"><small><span class="pulse" aria-hidden="true"></span><?=esc_html( c24__( 'Llamada urgente · 24/7' ) );?></small><b><?=c24_phone_nbsp();?></b></span>
          </a>
        </div>

        <div class="nf__visual" aria-hidden="true">
          <img class="nf__orbit" src="<?=esc_url( c24_asset( 'img/orbit.webp' ) );?>" alt="" width="1184" height="1031">
          <p class="nf__code"><span>4</span><img src="<?=esc_url( c24_asset( 'img/lock-badge.webp' ) );?>" alt="" width="240" height="240"><span>4</span></p>
          <p class="nf__chip"><svg><use href="#i-key"/></svg><?=esc_html( c24__( 'Hemos perdido la llave de esta página' ) );?></p>
        </div>
      </div>

      <nav class="nf__links" aria-labelledby="nf-links-title">
        <p class="nf__links-title" id="nf-links-title"><?=esc_html( c24__( 'Quizá buscaba' ) );?></p>
        <ul class="routes nf__routes" role="list">
          <?php if ( $door_c ) : ?>
          <li>
            <a class="route" href="<?=esc_url( $door_c['url'] );?>">
              <span class="route__ic" aria-hidden="true"><svg><use href="#i-door"/></svg></span>
              <span class="route__q"><?=c24_kses( $door_c['title'] );?></span>
              <span class="route__a"><?=str_replace( '%s', esc_html( $door_c['price'] ) . '&nbsp;€', esc_html( c24__( 'desde %s' ) ) );?>, 24/7</span>
              <span class="route__go" aria-hidden="true"><svg><use href="#i-arrow-right"/></svg></span>
            </a>
          </li>
          <?php endif; ?>
          <li>
            <a class="route" href="<?=esc_url( c24_page_url( 'services' ) );?>">
              <span class="route__ic" aria-hidden="true"><svg><use href="#i-key"/></svg></span>
              <span class="route__q"><?=esc_html( c24__( 'Todos los servicios' ) );?></span>
              <span class="route__a"><?=esc_html( sprintf( c24__( '%s servicios' ), $services ) );?></span>
              <span class="route__go" aria-hidden="true"><svg><use href="#i-arrow-right"/></svg></span>
            </a>
          </li>
          <li>
            <a class="route" href="<?=esc_url( c24_home() . '#prices' );?>">
              <span class="route__ic" aria-hidden="true"><svg><use href="#i-doc-euro"/></svg></span>
              <span class="route__q"><?=esc_html( c24__( 'Precios' ) );?></span>
              <span class="route__a"><?=esc_html( c24__( 'Tarifas de los trabajos' ) );?></span>
              <span class="route__go" aria-hidden="true"><svg><use href="#i-arrow-right"/></svg></span>
            </a>
          </li>
          <li>
            <a class="route" href="<?=esc_url( c24_page_url( 'contacts' ) );?>">
              <span class="route__ic" aria-hidden="true"><svg><use href="#i-phone-ring"/></svg></span>
              <span class="route__q"><?=esc_html( c24__( 'Contacto' ) );?></span>
              <span class="route__a"><?=esc_html( c24__( 'Teléfono y WhatsApp' ) );?></span>
              <span class="route__go" aria-hidden="true"><svg><use href="#i-arrow-right"/></svg></span>
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </section>

</main>
<?php
get_footer();
