<?php
/**
 * Запасной шаблон.
 */

defined( 'ABSPATH' ) || exit;

get_header();
while ( have_posts() ) :
	the_post();
	?>
<main id="main">

  <section class="sh sh--page" aria-labelledby="page-title">
    <img class="sh__leaves" src="<?=esc_url( c24_asset( 'img/leaves.webp' ) );?>" alt="" aria-hidden="true" width="1100" height="825">
    <div class="sh__rings" aria-hidden="true"><i></i><i></i></div>
    <div class="container">
      <?php get_template_part( 'template-parts/crumbs' ); ?>
      <h1 class="sh__title page__title" id="page-title"><?=esc_html( get_the_title() );?></h1>
    </div>
  </section>

  <section class="section page-sec">
    <div class="container">
      <div class="page__body longread__body">
        <?php the_content(); ?>
      </div>
    </div>
  </section>

</main>
	<?php
endwhile;
get_footer();
