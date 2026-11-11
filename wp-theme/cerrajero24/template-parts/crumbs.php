<?php
defined( 'ABSPATH' ) || exit;
$items = c24_crumbs();
?>
      <nav class="crumbs" aria-label="<?=esc_attr( c24__( 'Ruta de navegación' ) );?>">
        <ol>
          <?php foreach ( $items as $it ) : ?>
          <li><?php if ( $it[1] ) : ?><a href="<?=esc_url( $it[1] );?>"><?=esc_html( $it[0] );?></a><?php else : ?><span aria-current="page"><?=esc_html( $it[0] );?></span><?php endif; ?></li>
          <?php endforeach; ?>
        </ol>
      </nav>
