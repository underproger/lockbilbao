<?php
/**
 * Формы заявок.
 *
 * Разметка форм — в теме (1:1 со статикой), отправка — через REST Contact Form 7:
 * письма, получатели и тексты писем настраиваются в «Контактные формы → Cerrajero24 — заявка».
 * Подписи полей переводятся в «Языки → Перевод строк».
 */

defined( 'ABSPATH' ) || exit;

// Свои стили и скрипты CF7 не нужны: форма рисуется темой, отправку делает main.js.
add_filter( 'wpcf7_load_js', '__return_false' );
add_filter( 'wpcf7_load_css', '__return_false' );
add_filter( 'wpcf7_autop_or_not', '__return_false' );

/** ID формы CF7, в которую уходят все заявки сайта. */
function c24_form_id() {
	$id = (int) get_option( 'c24_form_id' );
	if ( $id && 'wpcf7_contact_form' === get_post_type( $id ) ) {
		return $id;
	}
	$found = get_posts( [ 'post_type' => 'wpcf7_contact_form', 'meta_key' => '_c24_form', 'meta_value' => 'lead', 'fields' => 'ids', 'posts_per_page' => 1, 'lang' => '' ] );
	return $found ? (int) $found[0] : 0;
}

/** Шаблон формы CF7 (поля, которые принимает REST). */
function c24_cf7_form_template() {
	return implode( "\n", [
		'[text form]',
		'[text service]',
		'[text name]',
		'[select dial "+34" "+33" "+351" "+44" "+49" "+39" "+48" "+40" "+380"]',
		'[tel* phone]',
		'[text phone_full]',
		'[text address]',
		'[text problem]',
		'[file photo limit:10mb filetypes:jpg|jpeg|png|webp|heic|heif|gif]',
		'[acceptance consent optional]',
		'[text page]',
		'[text utm_source] [text utm_medium] [text utm_campaign] [text utm_term] [text utm_content]',
		'[submit "Enviar"]',
	] );
}

function c24_cf7_mail_body() {
	return implode( "\n", [
		'Formulario: [form]',
		'Servicio: [service]',
		'Nombre: [name]',
		'Teléfono: [phone_full]',
		'Dirección / zona: [address]',
		'Qué ha pasado: [problem]',
		'Página: [page]',
		'UTM: [utm_source] / [utm_medium] / [utm_campaign] / [utm_term] / [utm_content]',
		'',
		'-- ',
		'[_site_title] · [_date] [_time]',
	] );
}

/* ---------------- Разметка форм ---------------- */

function c24_dial_select() {
	$codes = [ 'ES' => '+34', 'FR' => '+33', 'PT' => '+351', 'GB' => '+44', 'DE' => '+49', 'IT' => '+39', 'PL' => '+48', 'RO' => '+40', 'UA' => '+380' ];
	$html  = '<select class="phone-input__code" name="dial" aria-label="' . esc_attr( c24__( 'Prefijo del país' ) ) . '" data-dial>' . "\n";
	foreach ( $codes as $cc => $code ) {
		$html .= '                  <option value="' . $code . '"' . ( '+34' === $code ? ' selected' : '' ) . '>' . $cc . ' ' . $code . '</option>' . "\n";
	}
	return $html . '                </select>';
}

function c24_phone_field( $id, $placeholder = '600 123 456', $with_error = true ) {
	ob_start(); ?>
              <div class="phone-input">
                <?=c24_dial_select();?>
                <input class="input" id="<?=esc_attr( $id );?>" name="phone" type="tel" inputmode="tel" autocomplete="tel" placeholder="<?=esc_attr( $placeholder );?>" required aria-describedby="<?=esc_attr( $id );?>-err">
              </div>
<?php if ( $with_error ) : ?>
              <p class="field__error" id="<?=esc_attr( $id );?>-err" hidden></p>
<?php endif;
	return ob_get_clean();
}

function c24_privacy_link() {
	return '<a href="' . esc_url( c24_page_url( 'privacy' ) ) . '">' . esc_html( c24__( 'la política de privacidad' ) ) . '</a>';
}

/** Название формы для письма: «Страница — форма». */
function c24_form_label( $what ) {
	$id   = (int) get_queried_object_id();
	$page = is_front_page() ? c24__( 'Inicio' ) : ( $id ? ( c24_field( 'crumb', $id ) ?: get_the_title( $id ) ) : '404' );
	return $page . ' — ' . $what;
}

/**
 * Форма заявки.
 * $type: quick (под баннером услуги), est (вызов мастера), final (финальная), contact (контакты), modal.
 */
function c24_form( $type, $args = [] ) {
	$note = sprintf( esc_html( c24__( 'Al pulsar el botón, acepta %s.' ) ), c24_privacy_link() );
	$name_field = function ( $id ) {
		return '<div class="field">' . "\n" .
			'            <label for="' . $id . '">' . esc_html( c24__( 'Nombre' ) ) . ' <span class="field__opt">' . esc_html( c24__( 'opcional' ) ) . '</span></label>' . "\n" .
			'            <input class="input" id="' . $id . '" name="name" type="text" autocomplete="name">' . "\n" .
			'          </div>';
	};
	$btn = '<button class="btn btn--lime btn--block btn--lg" type="submit">' . esc_html( c24__( 'Llamar al cerrajero' ) ) . ' <svg aria-hidden="true"><use href="#i-arrow"/></svg></button>';
	$status = '<p class="form-status" role="status" aria-live="polite" data-form-status></p>';
	ob_start();

	if ( 'quick' === $type ) : ?>
        <form class="sh-form quick__form lead-form" action="#" method="post" novalidate data-lead-form>
          <input type="hidden" name="form" value="<?=esc_attr( c24_form_label( c24__( 'primera pantalla' ) ) );?>">
          <?php if ( ! empty( $args['title'] ) ) : ?><p class="sh-form__title"><?=c24_kses( $args['title'] );?></p><?php endif; ?>
          <div class="sh-form__row">
            <div class="field sh-form__phone">
              <label class="visually-hidden" for="h-phone"><?=esc_html( c24__( 'Teléfono' ) );?></label>
<?=c24_phone_field( 'h-phone', c24__( 'Teléfono' ), false );?>
            </div>
            <div class="field sh-form__addr">
              <label class="visually-hidden" for="h-addr"><?=esc_html( c24__( 'Barrio o dirección (opcional)' ) );?></label>
              <input class="input" id="h-addr" name="address" type="text" autocomplete="address-line1" placeholder="<?=esc_attr( c24__( 'Barrio o dirección' ) );?>">
            </div>
            <button class="btn btn--lime sh-form__btn" type="submit"><?=esc_html( c24__( 'Llamar al cerrajero' ) );?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></button>
          </div>
          <p class="field__error" id="h-phone-err" hidden></p>
          <p class="sh-form__note"><?=$note;?></p>
          <?=$status;?>
        </form>
	<?php elseif ( 'est' === $type ) : ?>
        <form class="est__form lead-form" action="#" method="post" novalidate data-lead-form>
          <input type="hidden" name="form" value="<?=esc_attr( c24_form_label( c24__( 'llamar al cerrajero' ) ) );?>">
          <?=$name_field( 'e-name' );?>
          <div class="field">
            <label for="e-phone"><?=esc_html( c24__( 'Teléfono' ) );?></label>
<?=c24_phone_field( 'e-phone' );?>
          </div>
          <?=$btn;?>
          <p class="form-card__note"><?=$note;?></p>
          <?=$status;?>
          <div class="est__alt">
            <a class="est__alt-link" href="<?=esc_attr( c24_tel() );?>"><svg aria-hidden="true"><use href="#i-phone"/></svg><?=esc_html( c24__( 'Urgente:' ) );?> <?=esc_html( c24_phone() );?></a>
          </div>
        </form>
	<?php elseif ( 'final' === $type ) : ?>
      <form class="form-card lead-form" action="#" method="post" novalidate data-lead-form>
        <input type="hidden" name="form" value="<?=esc_attr( c24_form_label( c24__( 'formulario final' ) ) );?>">
        <?php if ( ! empty( $args['service'] ) ) : ?><input type="hidden" name="service" value="<?=esc_attr( $args['service'] );?>"><?php endif; ?>
        <?php if ( ! empty( $args['title'] ) ) : ?><p class="form-card__title"><?=c24_kses( $args['title'] );?></p><?php endif; ?>
        <?=$name_field( 'f-name' );?>
        <div class="field">
          <label for="f-phone"><?=esc_html( c24__( 'Teléfono' ) );?></label>
<?=c24_phone_field( 'f-phone' );?>
        </div>
        <?=$btn;?>
        <p class="form-card__note"><?=$note;?></p>
        <?=$status;?>
      </form>
	<?php elseif ( 'contact' === $type ) : ?>
        <form class="form-card lead-form ct-form" action="#" method="post" novalidate data-lead-form>
          <input type="hidden" name="form" value="<?=esc_attr( c24_form_label( c24__( 'solicitud de servicio' ) ) );?>">
          <div class="ct-form__head">
            <?php if ( ! empty( $args['title'] ) ) : ?><p class="form-card__title"><?=c24_kses( $args['title'] );?></p><?php endif; ?>
            <?php if ( ! empty( $args['lead'] ) ) : ?><p class="ct-form__lead"><?=c24_kses( $args['lead'] );?></p><?php endif; ?>
          </div>
          <?=$name_field( 'c-name' );?>
          <div class="field">
            <label for="c-phone"><?=esc_html( c24__( 'Teléfono' ) );?></label>
<?=c24_phone_field( 'c-phone' );?>
          </div>
          <?=$btn;?>
          <p class="form-card__note"><?=$note;?></p>
          <?=$status;?>
        </form>
	<?php elseif ( 'modal' === $type ) :
		$m = c24_modal_data(); ?>
  <form class="lead-form modal__form" action="#" method="post" novalidate data-lead-form>
    <input type="hidden" name="form" value="<?=esc_attr( c24_form_label( c24__( 'ventana «Consultar precio»' ) ) );?>">
    <button class="modal__close" type="button" aria-label="<?=esc_attr( c24__( 'Cerrar' ) );?>" data-modal-close><svg aria-hidden="true"><use href="#i-close"/></svg></button>
    <p class="modal__title" id="price-modal-title"><?=c24_kses( $m['title'] );?></p>
    <p class="modal__text"><?=c24_kses( $m['text'] );?></p>
    <div class="field">
      <label for="m-phone"><?=esc_html( c24__( 'Teléfono' ) );?></label>
<?=c24_phone_field( 'm-phone' );?>
    </div>
    <?php if ( $m['options'] ) : ?>
    <div class="field">
      <label for="m-service"><?=esc_html( c24__( 'Qué ha pasado' ) );?></label>
      <select class="select" id="m-service" name="problem">
        <?php foreach ( $m['options'] as $o ) : ?>
        <option value="<?=esc_attr( $o );?>"><?=esc_html( $o );?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <?php endif; ?>
    <div class="field">
      <label class="file" for="m-photo">
        <span class="file__icon"><svg aria-hidden="true"><use href="#i-camera"/></svg></span>
        <span class="file__text"><b data-file-name><?=esc_html( $m['file'] );?></b><small><?=esc_html( c24__( 'Cámara o galería, hasta 10 MB' ) );?></small></span>
      </label>
      <input class="visually-hidden" id="m-photo" name="photo" type="file" accept="image/*" data-file>
    </div>
    <label class="consent">
      <input type="checkbox" name="consent" required>
      <span><?=sprintf( esc_html( c24__( 'Acepto %s' ) ), c24_privacy_link() );?></span>
    </label>
    <button class="btn btn--lime btn--block btn--lg" type="submit"><?=esc_html( c24__( 'Consultar precio' ) );?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></button>
    <?=$status;?>
  </form>
	<?php endif;

	return ob_get_clean();
}

/** Данные модалки «Узнать цену»: поля страницы, иначе — главной. */
function c24_modal_data() {
	$id    = is_404() ? 0 : (int) get_queried_object_id();
	$front = (int) get_option( 'page_on_front' );
	if ( function_exists( 'pll_get_post' ) && $front ) {
		$front = (int) pll_get_post( $front ) ?: $front;
	}
	$get = function ( $name ) use ( $id, $front ) {
		$v = $id ? c24_field( $name, $id ) : '';
		return $v ?: c24_field( $name, $front );
	};
	return [
		'title'   => $get( 'modal_title' ) ?: c24__( 'Consultar precio' ),
		'text'    => $get( 'modal_text' ),
		'file'    => $get( 'modal_file' ) ?: c24__( 'Foto de la cerradura' ),
		'options' => c24_lines( $get( 'modal_options' ) ),
	];
}

/* ---------------- REST: язык и ответы ---------------- */

// Ответ CF7 по REST — на языке страницы, с которой отправили форму.
add_filter( 'wpcf7_display_message', function ( $message, $status ) {
	$map = [
		'mail_sent_ok'     => 'Solicitud enviada. El operador le llamará en unos minutos.',
		'mail_sent_ng'     => 'No se ha podido enviar la solicitud. Llámenos:',
		'validation_error' => 'Revise los campos del formulario.',
		'spam'             => 'No se ha podido enviar la solicitud. Llámenos:',
	];
	return isset( $map[ $status ] ) ? c24__( $map[ $status ] ) : $message;
}, 10, 2 );

add_action( 'rest_api_init', function () {
	if ( empty( $_POST['_wpcf7_locale'] ) || ! function_exists( 'PLL' ) || ! function_exists( 'pll_languages_list' ) ) {
		return;
	}
	$locale = sanitize_text_field( wp_unslash( $_POST['_wpcf7_locale'] ) );
	$lang   = PLL()->model->get_language( $locale );
	if ( $lang ) {
		PLL()->curlang = $lang;
	}
}, 1 );
