<?php
defined( 'ABSPATH' ) || exit;

$logo_light = (int) c24_opt( 'logo_light' );
$nif        = c24_nif();
?>
<footer class="footer" id="contacts">
  <div class="container footer__grid">
    <div class="footer__brand">
      <a class="logo logo--light" href="<?=esc_url( c24_home() );?>" aria-label="<?=esc_attr( c24__( 'Cerrajero24 — ir a la página de inicio' ) );?>"><?php if ( $logo_light ) : ?><?=c24_img( $logo_light, [ 'alt' => 'Cerrajero24' ] );?><?php else : ?><img src="<?=esc_url( c24_asset( 'img/logo-light.png' ) );?>" alt="Cerrajero24" width="715" height="129"><?php endif; ?></a>
      <p><?=esc_html( c24__( 'Apertura urgente, reparación, cambio e instalación de cerraduras en Bilbao y Gran Bilbao.' ) );?></p>
    </div>

    <div class="footer__call">
      <p class="footer__hours"><span class="pulse" aria-hidden="true"></span><?=esc_html( c24__( 'Las 24 horas, 7 días a la semana' ) );?></p>
      <a class="footer__phone" href="<?=esc_attr( c24_tel() );?>"><?=esc_html( c24_phone() );?></a>
    </div>

    <ul class="footer__contacts" role="list">
      <li><a href="<?=esc_url( c24_wa() );?>"<?=c24_ext();?>><svg aria-hidden="true"><use href="#i-wa"/></svg>WhatsApp</a></li>
      <li><a href="mailto:<?=esc_attr( c24_email() );?>"><svg aria-hidden="true"><use href="#i-mail"/></svg><?=esc_html( c24_email() );?></a></li>
      <li><a href="<?=esc_url( c24_maps() );?>"<?=c24_ext();?>><svg aria-hidden="true"><use href="#i-pin"/></svg><?=esc_html( c24__( 'Estamos en Google Maps' ) );?></a></li>
    </ul>
  </div>

  <div class="container footer__bottom">
    <p>© <span data-year><?=esc_html( wp_date( 'Y' ) );?></span> Cerrajero24 · NIF: <?=esc_html( $nif ?: '—' );?> · <?=esc_html( c24__( 'Bilbao, Bizkaia, España' ) );?></p>
    <ul role="list">
      <?php c24_menu( 'menu-footer' ); ?>
    </ul>
  </div>
</footer>

<div class="mobile-bar" data-mobile-bar>
  <a class="btn btn--lime" href="<?=esc_attr( c24_tel() );?>"><svg aria-hidden="true"><use href="#i-phone"/></svg><?=esc_html( c24__( 'Llamar' ) );?></a>
  <button class="btn btn--dark" type="button" data-modal-open="price-modal"><?=esc_html( c24__( 'Consultar precio' ) );?></button>
</div>

<div class="fab" data-fab>
  <button class="fab__top" type="button" aria-label="<?=esc_attr( c24__( 'Volver arriba' ) );?>" data-to-top><svg aria-hidden="true"><use href="#i-chev-right"/></svg></button>
  <a class="fab__wa" href="<?=esc_url( c24_wa() );?>"<?=c24_ext();?> aria-label="<?=esc_attr( c24__( 'Escribir por WhatsApp' ) );?>"><svg aria-hidden="true"><use href="#i-wa"/></svg><span class="fab__tip" aria-hidden="true"><?=esc_html( c24__( 'Escribir por WhatsApp' ) );?></span></a>
</div>

<?php get_template_part( 'template-parts/modal' ); ?>

<?php wp_footer(); ?>
</body>
</html>
