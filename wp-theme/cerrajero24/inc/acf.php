<?php
/**
 * Группы полей SCF (Secure Custom Fields) и страница опций «Контакты».
 * Поля разбиты на вкладки по блокам страницы и выводятся сразу под заголовком.
 */

defined( 'ABSPATH' ) || exit;

/* ---------------- Конструктор полей ---------------- */

/** Базовое поле. Ключ: field_c24_{группа}_{имя}. */
function c24_f( $g, $name, $label, $type = 'text', $extra = [] ) {
	$field = [
		'key'   => 'field_c24_' . $g . '_' . $name,
		'name'  => $name,
		'label' => $label,
		'type'  => $type,
	];
	switch ( $type ) {
		case 'textarea':
			$field += [ 'rows' => 3, 'new_lines' => '' ];
			break;
		case 'wysiwyg':
			$field += [ 'tabs' => 'all', 'toolbar' => 'basic', 'media_upload' => 0, 'delay' => 1 ];
			break;
		case 'image':
			$field += [ 'return_format' => 'id', 'preview_size' => 'medium', 'library' => 'all' ];
			break;
		case 'repeater':
			$field += [ 'layout' => 'table', 'button_label' => 'Добавить строку' ];
			break;
		case 'true_false':
			$field += [ 'ui' => 1 ];
			break;
	}
	return array_merge( $field, $extra );
}

function c24_tab( $g, $name, $label ) {
	return [ 'key' => 'field_c24_' . $g . '_tab_' . $name, 'label' => $label, 'type' => 'tab', 'placement' => 'top' ];
}

/** Повторитель; подполя получают ключи с префиксом повторителя. */
function c24_rep( $g, $name, $label, $subs, $extra = [] ) {
	$sub_fields = [];
	foreach ( $subs as $s ) {
		$s['key']     = 'field_c24_' . $g . '_' . $name . '_' . $s['name'];
		$sub_fields[] = $s;
	}
	return c24_f( $g, $name, $label, 'repeater', array_merge( [ 'sub_fields' => $sub_fields ], $extra ) );
}

/** Подполе повторителя (ключ проставит c24_rep). */
function c24_s( $name, $label, $type = 'text', $extra = [] ) {
	$f = c24_f( 'x', $name, $label, $type, $extra );
	unset( $f['key'] );
	return $f;
}

/** Иконки из спрайта темы — выпадающий список. */
function c24_icon_choices() {
	static $choices = null;
	if ( null === $choices ) {
		$choices = [ '' => '—' ];
		$sprite  = (string) file_get_contents( C24_DIR . '/template-parts/sprite.php' );
		if ( preg_match_all( '/<symbol id="([^"]+)"/', $sprite, $m ) ) {
			foreach ( $m[1] as $id ) {
				$choices[ $id ] = $id;
			}
		}
	}
	return $choices;
}

function c24_s_icon( $name = 'icon', $label = 'Иконка' ) {
	return c24_s( $name, $label, 'select', [ 'choices' => c24_icon_choices(), 'ui' => 0, 'wrapper' => [ 'width' => 14 ] ] );
}

function c24_kicker( $g, $name ) {
	return c24_f( $g, $name, 'Название блока', 'text', [ 'wrapper' => [ 'width' => 30 ] ] );
}

function c24_title( $g, $name, $label = 'Заголовок' ) {
	return c24_f( $g, $name, $label, 'text', [ 'wrapper' => [ 'width' => 70 ], 'instructions' => 'Акцент: <span class="accent">…</span>' ] );
}

/* ---------------- Общие блоки ---------------- */

function c24_fields_prices( $g ) {
	return [
		c24_tab( $g, 'prices', 'Цены' ),
		c24_kicker( $g, 'pr_eyebrow' ),
		c24_title( $g, 'pr_title' ),
		c24_f( $g, 'pr_lead', 'Подзаголовок', 'textarea' ),
		c24_f( $g, 'pr_photo', 'Фото', 'image', [ 'wrapper' => [ 'width' => 50 ] ] ),
		c24_f( $g, 'pr_photo_small', 'Круглое фото', 'image', [ 'wrapper' => [ 'width' => 50 ] ] ),
		c24_f( $g, 'pr_caption', 'Подпись таблицы (для скринридеров)' ),
		c24_f( $g, 'pr_col_1', 'Колонка 1', 'text', [ 'wrapper' => [ 'width' => 50 ] ] ),
		c24_f( $g, 'pr_col_2', 'Колонка 2', 'text', [ 'wrapper' => [ 'width' => 50 ] ] ),
		c24_rep( $g, 'pr_rows', 'Строки таблицы', [ c24_s_icon(), c24_s( 'label', 'Услуга' ), c24_s( 'price', 'Цена «от», €', 'text', [ 'instructions' => 'Число. Пусто — «уточняется», … — цена не утверждена', 'wrapper' => [ 'width' => 22 ] ] ) ] ),
		c24_rep( $g, 'pr_extras', 'Кнопки под таблицей', [ c24_s_icon(), c24_s( 'text', 'Текст (перенос строки — Enter)', 'textarea', [ 'rows' => 2 ] ), c24_s( 'link', 'Ссылка или #якорь' ) ] ),
		c24_rep( $g, 'pr_notes', 'Примечания', [ c24_s( 'text', 'Текст' ) ] ),
		c24_f( $g, 'pr_cta_title', 'Кнопка: заголовок', 'text', [ 'wrapper' => [ 'width' => 50 ] ] ),
		c24_f( $g, 'pr_cta_text', 'Кнопка: текст', 'text', [ 'wrapper' => [ 'width' => 50 ] ] ),
	];
}

function c24_fields_why( $g ) {
	return [
		c24_tab( $g, 'why', 'Преимущества' ),
		c24_kicker( $g, 'why_kicker' ),
		c24_title( $g, 'why_title' ),
		c24_f( $g, 'why_lead', 'Подзаголовок', 'textarea' ),
		c24_rep( $g, 'why_cards', 'Карточки (6 шт. — под сетку макета)', [ c24_s_icon(), c24_s( 'title', 'Заголовок' ), c24_s( 'text', 'Текст', 'textarea' ) ] ),
		c24_f( $g, 'why_photo', 'Карточка с фото: фото', 'image', [ 'wrapper' => [ 'width' => 30 ] ] ),
		c24_f( $g, 'why_photo_title', 'Карточка с фото: заголовок', 'text', [ 'wrapper' => [ 'width' => 30 ] ] ),
		c24_f( $g, 'why_photo_text', 'Карточка с фото: текст', 'textarea', [ 'wrapper' => [ 'width' => 40 ] ] ),
	];
}

function c24_fields_flow( $g ) {
	return [
		c24_tab( $g, 'flow', 'Этапы' ),
		c24_kicker( $g, 'flow_kicker' ),
		c24_title( $g, 'flow_title' ),
		c24_rep( $g, 'flow_steps', 'Этапы (номера ставятся автоматически)', [ c24_s_icon(), c24_s( 'title', 'Заголовок' ), c24_s( 'text', 'Текст', 'textarea' ) ] ),
		c24_f( $g, 'flow_cta_title', 'Плашка: заголовок', 'text', [ 'wrapper' => [ 'width' => 40 ] ] ),
		c24_f( $g, 'flow_cta_sub', 'Плашка: текст', 'text', [ 'wrapper' => [ 'width' => 40 ] ] ),
		c24_f( $g, 'flow_cta_btn', 'Плашка: кнопка', 'text', [ 'wrapper' => [ 'width' => 20 ] ] ),
	];
}

function c24_fields_reviews( $g ) {
	return [
		c24_tab( $g, 'reviews', 'Отзывы' ),
		c24_kicker( $g, 'rv_kicker' ),
		c24_title( $g, 'rv_title' ),
		c24_rep( $g, 'rv_links', 'Ссылки под заголовком', [ c24_s( 'text', 'Текст' ), c24_s( 'url', 'Ссылка (пусто — профиль в Google Maps из «Контактов»)', 'url' ) ] ),
		c24_f( $g, 'rv_aria', 'Название карусели (для скринридеров)' ),
		c24_rep( $g, 'rv_items', 'Отзывы', [
			c24_s( 'name', 'Имя', 'text', [ 'wrapper' => [ 'width' => 14 ] ] ),
			c24_s( 'date', 'Дата', 'text', [ 'wrapper' => [ 'width' => 12 ] ] ),
			c24_s( 'tag', 'Услуга · район', 'text', [ 'wrapper' => [ 'width' => 20 ] ] ),
			c24_s( 'text', 'Текст', 'textarea' ),
			c24_s( 'demo', 'Пример', 'true_false', [ 'instructions' => 'Вместо даты — «Пример отзыва»', 'wrapper' => [ 'width' => 10 ] ] ),
		] ),
		c24_f( $g, 'rv_cta_text', 'Кнопка под отзывами', 'text', [ 'wrapper' => [ 'width' => 60 ] ] ),
		c24_f( $g, 'rv_cta_type', 'Кнопка ведёт', 'select', [ 'choices' => [ 'call' => 'Звонок (зелёная)', 'maps' => 'Google Maps (контурная)' ], 'wrapper' => [ 'width' => 40 ] ] ),
	];
}

function c24_fields_geo( $g ) {
	$fields = [
		c24_tab( $g, 'geo', 'Районы' ),
		c24_kicker( $g, 'geo_kicker' ),
		c24_title( $g, 'geo_title' ),
		c24_f( $g, 'geo_lead', 'Подзаголовок', 'textarea' ),
	];
	foreach ( [ 1, 2 ] as $n ) {
		$fields[] = c24_f( $g, "geo_card_{$n}_title", "Карточка {$n}: заголовок", 'text', [ 'wrapper' => [ 'width' => 70 ] ] );
		$fields[] = c24_f( $g, "geo_card_{$n}_count", "Карточка {$n}: счётчик", 'text', [ 'wrapper' => [ 'width' => 30 ] ] );
		$fields[] = c24_rep( $g, "geo_card_{$n}_areas", "Карточка {$n}: районы", [
			c24_s( 'area', 'Район на карте', 'select', [ 'choices' => c24_area_choices() ] ),
			c24_s( 'label', 'Подпись' ),
		] );
		$fields[] = c24_f( $g, "geo_card_{$n}_note", "Карточка {$n}: примечание" );
	}
	return $fields;
}

function c24_fields_longread( $g ) {
	return [
		c24_tab( $g, 'longread', 'Полезно знать' ),
		c24_kicker( $g, 'lr_kicker' ),
		c24_title( $g, 'lr_title' ),
		c24_f( $g, 'lr_lead', 'Вводный абзац', 'textarea' ),
		c24_f( $g, 'lr_body', 'Текст (подзаголовки — «Заголовок 3»)', 'wysiwyg', [ 'toolbar' => 'full' ] ),
	];
}

function c24_fields_faq( $g ) {
	return [
		c24_tab( $g, 'faq', 'Вопросы' ),
		c24_kicker( $g, 'faq_kicker' ),
		c24_title( $g, 'faq_title' ),
		c24_f( $g, 'faq_help_title', 'Плашка: заголовок', 'text', [ 'wrapper' => [ 'width' => 40 ] ] ),
		c24_f( $g, 'faq_help_text', 'Плашка: текст', 'text', [ 'wrapper' => [ 'width' => 60 ] ] ),
		c24_rep( $g, 'faq_items', 'Вопросы и ответы (попадают в разметку FAQPage)', [ c24_s( 'q', 'Вопрос', 'textarea', [ 'rows' => 2, 'wrapper' => [ 'width' => 30 ] ] ), c24_s( 'a', 'Ответ', 'wysiwyg' ) ], [ 'layout' => 'row' ] ),
	];
}

function c24_fields_final( $g, $with_service = false ) {
	$f = [
		c24_tab( $g, 'final', 'Заявка' ),
		c24_kicker( $g, 'final_kicker' ),
		c24_title( $g, 'final_title' ),
		c24_f( $g, 'final_lead', 'Подзаголовок', 'textarea' ),
		c24_f( $g, 'final_hours', 'Режим работы', 'text', [ 'wrapper' => [ 'width' => 50 ] ] ),
		c24_f( $g, 'final_form_title', 'Заголовок формы', 'text', [ 'wrapper' => [ 'width' => 50 ] ] ),
		c24_rep( $g, 'final_guarantees', 'Гарантии', [ c24_s( 'text', 'Текст' ) ] ),
	];
	if ( $with_service ) {
		$f[] = c24_f( $g, 'form_service', 'Услуга в письме из формы', 'text', [ 'instructions' => 'Скрытое поле: подставляется в заявку' ] );
	}
	return $f;
}

function c24_fields_modal( $g ) {
	return [
		c24_tab( $g, 'modal', 'Окно «Узнать цену»' ),
		c24_f( $g, 'modal_title', 'Заголовок', 'text', [ 'wrapper' => [ 'width' => 50 ], 'instructions' => 'Пусто — как на главной' ] ),
		c24_f( $g, 'modal_file', 'Подпись поля фото', 'text', [ 'wrapper' => [ 'width' => 50 ] ] ),
		c24_f( $g, 'modal_text', 'Текст', 'textarea', [ 'rows' => 2 ] ),
		c24_f( $g, 'modal_options', 'Варианты «Что случилось» (по одному в строке)', 'textarea', [ 'rows' => 8 ] ),
	];
}

function c24_fields_seo( $g, $service = false ) {
	$f = [
		c24_tab( $g, 'seo', 'SEO' ),
		c24_f( $g, 'seo_title', 'Title', 'text', [ 'instructions' => 'Если установлен Yoast SEO и в нём заполнен title — используется он' ] ),
		c24_f( $g, 'seo_desc', 'Meta description', 'textarea', [ 'rows' => 2 ] ),
		c24_f( $g, 'og_desc', 'Описание для соцсетей (og:description)', 'textarea', [ 'rows' => 2 ] ),
	];
	if ( $service ) {
		$f[] = c24_f( $g, 'schema_name', 'Schema.org Service: name', 'text', [ 'wrapper' => [ 'width' => 50 ] ] );
		$f[] = c24_f( $g, 'schema_type', 'Schema.org Service: serviceType', 'text', [ 'wrapper' => [ 'width' => 50 ] ] );
		$f[] = c24_f( $g, 'schema_desc', 'Schema.org Service: description', 'textarea', [ 'rows' => 2 ] );
	}
	return $f;
}

function c24_group( $key, $title, $fields, $location ) {
	acf_add_local_field_group( [
		'key'                   => 'group_c24_' . $key,
		'title'                 => $title,
		'fields'                => $fields,
		'location'              => $location,
		'position'              => 'acf_after_title',
		'style'                 => 'default',
		'label_placement'       => 'top',
		'instruction_placement' => 'field',
		'menu_order'            => 0,
		'active'                => true,
	] );
}

function c24_loc_template( $tpl ) {
	return [ [ [ 'param' => 'page_template', 'operator' => '==', 'value' => $tpl ] ] ];
}

/* ---------------- Регистрация ---------------- */

add_action( 'acf/init', function () {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	if ( function_exists( 'acf_add_options_page' ) ) {
		acf_add_options_page( [
			'page_title' => 'Контакты и настройки сайта',
			'menu_title' => 'Контакты',
			'menu_slug'  => 'c24-contacts',
			'capability' => 'edit_pages',
			'icon_url'   => 'dashicons-phone',
			'position'   => 59,
			'redirect'   => false,
		] );
	}

	/* ---- Опции ---- */
	$g = 'opt';
	c24_group( 'options', 'Контакты', [
		c24_tab( $g, 'contacts', 'Контакты' ),
		c24_f( $g, 'phone', 'Телефон', 'text', [ 'placeholder' => '+34 944 123 456', 'wrapper' => [ 'width' => 33 ] ] ),
		c24_f( $g, 'whatsapp', 'WhatsApp (ссылка)', 'url', [ 'placeholder' => 'https://wa.me/34944123456', 'wrapper' => [ 'width' => 33 ] ] ),
		c24_f( $g, 'email', 'E-mail', 'email', [ 'wrapper' => [ 'width' => 33 ] ] ),
		c24_f( $g, 'nif', 'NIF / CIF', 'text', [ 'wrapper' => [ 'width' => 33 ] ] ),
		c24_tab( $g, 'google', 'Google' ),
		c24_f( $g, 'maps_url', 'Профиль в Google Maps', 'url', [ 'wrapper' => [ 'width' => 50 ] ] ),
		c24_f( $g, 'maps_review_url', 'Ссылка «Оставить отзыв»', 'url', [ 'wrapper' => [ 'width' => 50 ] ] ),
		c24_f( $g, 'rating', 'Рейтинг', 'text', [ 'placeholder' => '5.0', 'wrapper' => [ 'width' => 50 ] ] ),
		c24_f( $g, 'reviews_count', 'Число отзывов', 'text', [ 'placeholder' => '70', 'wrapper' => [ 'width' => 50 ] ] ),
		c24_tab( $g, 'brand', 'Логотип' ),
		c24_f( $g, 'logo', 'Логотип (шапка)', 'image', [ 'wrapper' => [ 'width' => 50 ], 'instructions' => 'Пусто — логотип из темы' ] ),
		c24_f( $g, 'logo_light', 'Логотип светлый (подвал)', 'image', [ 'wrapper' => [ 'width' => 50 ] ] ),
	], [ [ [ 'param' => 'options_page', 'operator' => '==', 'value' => 'c24-contacts' ] ] ] );

	/* ---- Главная ---- */
	$g = 'home';
	c24_group( 'home', 'Главная', array_merge(
		[
			c24_tab( $g, 'hero', 'Первый экран' ),
			c24_kicker( $g, 'hero_eyebrow' ),
			c24_title( $g, 'hero_title', 'Заголовок H1' ),
			c24_f( $g, 'hero_hours', 'Режим работы', 'text', [ 'wrapper' => [ 'width' => 30 ] ] ),
			c24_f( $g, 'hero_chip', 'Плашка с геометкой', 'text', [ 'wrapper' => [ 'width' => 30 ] ] ),
			c24_f( $g, 'hero_script', 'Рукописная подпись', 'textarea', [ 'rows' => 3, 'wrapper' => [ 'width' => 40 ] ] ),
			c24_f( $g, 'hero_photo', 'Фото мастера', 'image', [ 'wrapper' => [ 'width' => 33 ] ] ),
			c24_f( $g, 'hero_bubble_1', 'Круглое фото 1', 'image', [ 'wrapper' => [ 'width' => 33 ] ] ),
			c24_f( $g, 'hero_bubble_2', 'Круглое фото 2', 'image', [ 'wrapper' => [ 'width' => 33 ] ] ),
			c24_tab( $g, 'svc', 'Услуги' ),
			c24_kicker( $g, 'svc_kicker' ),
			c24_title( $g, 'svc_title' ),
			c24_f( $g, 'svc_lead', 'Подзаголовок', 'textarea', [ 'instructions' => 'Карточки — из страниц услуг (вкладка «Карточка»)' ] ),
		],
		c24_fields_prices( $g ),
		c24_fields_why( $g ),
		c24_fields_flow( $g ),
		[
			c24_tab( $g, 'kinds', 'Двери и замки' ),
			c24_kicker( $g, 'kinds_kicker' ),
			c24_title( $g, 'kinds_title' ),
			c24_f( $g, 'kinds_tab_1', 'Вкладка 1', 'text', [ 'wrapper' => [ 'width' => 50 ] ] ),
			c24_f( $g, 'kinds_tab_2', 'Вкладка 2', 'text', [ 'wrapper' => [ 'width' => 50 ] ] ),
			c24_rep( $g, 'kinds_doors', 'Вкладка 1: карточки', [ c24_s_icon(), c24_s( 'title', 'Заголовок (испанский термин: <span lang="es">…</span>)' ), c24_s( 'text', 'Текст', 'textarea' ) ] ),
			c24_rep( $g, 'kinds_locks', 'Вкладка 2: карточки', [ c24_s_icon(), c24_s( 'title', 'Заголовок' ), c24_s( 'text', 'Текст', 'textarea' ) ] ),
			c24_f( $g, 'brands_label', 'Бренды: подпись', 'text', [ 'wrapper' => [ 'width' => 40 ] ] ),
			c24_f( $g, 'brands_list', 'Бренды (по одному в строке)', 'textarea', [ 'rows' => 4, 'wrapper' => [ 'width' => 60 ] ] ),
			c24_tab( $g, 'safety', 'Безопасность' ),
			c24_f( $g, 'safety_photo', 'Фото', 'image', [ 'wrapper' => [ 'width' => 30 ] ] ),
			c24_f( $g, 'safety_badge', 'Подпись на фото', 'text', [ 'wrapper' => [ 'width' => 70 ] ] ),
			c24_kicker( $g, 'safety_kicker' ),
			c24_title( $g, 'safety_title' ),
			c24_f( $g, 'safety_quote', 'Цитата', 'textarea' ),
			c24_rep( $g, 'safety_items', 'Пункты', [ c24_s( 'title', 'Заголовок' ), c24_s( 'text', 'Текст', 'textarea' ) ] ),
		],
		c24_fields_reviews( $g ),
		[
			c24_tab( $g, 'about', 'О компании' ),
			c24_kicker( $g, 'about_kicker' ),
			c24_title( $g, 'about_title' ),
			c24_f( $g, 'about_lead', 'Вводный абзац', 'textarea' ),
			c24_f( $g, 'about_text', 'Текст', 'textarea' ),
			c24_rep( $g, 'about_facts', 'Цифры', [ c24_s_icon(), c24_s( 'num', 'Цифра', 'text', [ 'wrapper' => [ 'width' => 15 ] ] ), c24_s( 'text', 'Подпись' ) ] ),
			c24_f( $g, 'about_photo', 'Фото', 'image', [ 'wrapper' => [ 'width' => 50 ] ] ),
			c24_f( $g, 'about_chip', 'Плашка на фото', 'text', [ 'wrapper' => [ 'width' => 50 ] ] ),
		],
		c24_fields_geo( $g ),
		c24_fields_longread( $g ),
		c24_fields_faq( $g ),
		c24_fields_final( $g ),
		c24_fields_modal( $g ),
		c24_fields_seo( $g )
	), [ [ [ 'param' => 'page_type', 'operator' => '==', 'value' => 'front_page' ] ] ] );

	/* ---- Каталог услуг ---- */
	$g = 'cat';
	c24_group( 'services', 'Каталог услуг', array_merge(
		[
			c24_tab( $g, 'hero', 'Первый экран' ),
			c24_f( $g, 'crumb', 'Название в хлебных крошках', 'text', [ 'wrapper' => [ 'width' => 30 ] ] ),
			c24_title( $g, 'sh_title', 'Заголовок H1' ),
			c24_f( $g, 'sh_lead', 'Подзаголовок', 'textarea' ),
			c24_rep( $g, 'cat_perks', 'Преимущества', [ c24_s( 'text', 'Текст' ) ] ),
			c24_f( $g, 'cat_call_label', 'Карточка звонка: надпись', 'text', [ 'wrapper' => [ 'width' => 40 ] ] ),
			c24_f( $g, 'cat_call_text', 'Карточка звонка: текст', 'textarea', [ 'wrapper' => [ 'width' => 60 ] ] ),
			c24_tab( $g, 'groups', 'Каталог' ),
			c24_f( $g, 'grp_1_kicker', 'Группа «Открытие»: название блока', 'text', [ 'wrapper' => [ 'width' => 30 ], 'instructions' => 'Услуга попадает в группу по полю «Группа в каталоге» на её странице' ] ),
			c24_f( $g, 'grp_1_title', 'Группа «Открытие»: заголовок', 'text', [ 'wrapper' => [ 'width' => 70 ] ] ),
			c24_f( $g, 'grp_1_lead', 'Группа «Открытие»: подзаголовок', 'textarea' ),
			c24_f( $g, 'grp_2_kicker', 'Группа «Ремонт»: название блока', 'text', [ 'wrapper' => [ 'width' => 30 ] ] ),
			c24_f( $g, 'grp_2_title', 'Группа «Ремонт»: заголовок', 'text', [ 'wrapper' => [ 'width' => 70 ] ] ),
			c24_f( $g, 'grp_2_lead', 'Группа «Ремонт»: подзаголовок', 'textarea' ),
			c24_f( $g, 'cat_help_title', 'Карточка «Не нашли услугу»: заголовок', 'text', [ 'wrapper' => [ 'width' => 30 ] ] ),
			c24_f( $g, 'cat_help_text', 'Карточка «Не нашли услугу»: текст', 'textarea', [ 'wrapper' => [ 'width' => 45 ] ] ),
			c24_f( $g, 'cat_help_link', 'Карточка «Не нашли услугу»: ссылка', 'text', [ 'wrapper' => [ 'width' => 25 ] ] ),
			c24_tab( $g, 'routes', 'Подбор услуги' ),
			c24_kicker( $g, 'route_kicker' ),
			c24_title( $g, 'route_title' ),
			c24_f( $g, 'route_lead', 'Подзаголовок', 'textarea', [ 'wrapper' => [ 'width' => 70 ] ] ),
			c24_f( $g, 'route_call', 'Ссылка-звонок', 'text', [ 'wrapper' => [ 'width' => 30 ] ] ),
			c24_rep( $g, 'routes', 'Ситуации', [ c24_s_icon(), c24_s( 'q', 'Ситуация' ), c24_s( 'a', 'Услуга' ), c24_s( 'page', 'Страница', 'post_object', [ 'post_type' => [ 'page' ], 'return_format' => 'id', 'allow_null' => 1 ] ) ] ),
		],
		c24_fields_prices( $g ),
		c24_fields_why( $g ),
		c24_fields_flow( $g ),
		c24_fields_faq( $g ),
		c24_fields_final( $g ),
		c24_fields_modal( $g ),
		c24_fields_seo( $g )
	), c24_loc_template( 'templates/page-services.php' ) );

	/* ---- Страница услуги ---- */
	$g = 'svc';
	c24_group( 'service', 'Услуга', array_merge(
		[
			c24_tab( $g, 'card', 'Карточка' ),
			c24_f( $g, 'crumb', 'Короткое название (крошки, формы)', 'text', [ 'wrapper' => [ 'width' => 34 ] ] ),
			c24_f( $g, 'card_title', 'Название в карточке', 'text', [ 'wrapper' => [ 'width' => 33 ] ] ),
			c24_f( $g, 'card_price', 'Цена «от», €', 'text', [ 'wrapper' => [ 'width' => 16 ] ] ),
			c24_f( $g, 'card_group', 'Группа в каталоге', 'select', [ 'choices' => [ 'opening' => 'Аварийное открытие', 'repair' => 'Ремонт и замена' ], 'wrapper' => [ 'width' => 17 ] ] ),
			c24_f( $g, 'card_icon', 'Иконка услуги', 'image', [ 'wrapper' => [ 'width' => 20 ] ] ),
			c24_f( $g, 'card_text', 'Текст карточки', 'textarea', [ 'rows' => 2, 'wrapper' => [ 'width' => 40 ] ] ),
			c24_f( $g, 'card_text_catalog', 'Текст в каталоге (если отличается)', 'textarea', [ 'rows' => 2, 'wrapper' => [ 'width' => 40 ] ] ),
			c24_f( $g, 'card_tag', 'Метка «Популярная услуга» (широкая карточка в каталоге)', 'text', [ 'wrapper' => [ 'width' => 50 ] ] ),
			c24_f( $g, 'card_tags', 'Теги широкой карточки (по одному в строке)', 'textarea', [ 'rows' => 4, 'wrapper' => [ 'width' => 50 ] ] ),
			c24_tab( $g, 'hero', 'Первый экран' ),
			c24_title( $g, 'sh_title', 'Заголовок H1' ),
			c24_f( $g, 'sh_lead', 'Подзаголовок', 'textarea' ),
			c24_f( $g, 'sh_photo', 'Фото', 'image', [ 'wrapper' => [ 'width' => 33 ] ] ),
			c24_f( $g, 'sh_bubble', 'Круглое фото', 'image', [ 'wrapper' => [ 'width' => 33 ] ] ),
			c24_f( $g, 'sh_chip', 'Плашка с геометкой', 'text', [ 'wrapper' => [ 'width' => 34 ] ] ),
			c24_rep( $g, 'quick_perks', 'Преимущества под баннером', [ c24_s_icon(), c24_s( 'title', 'Заголовок' ), c24_s( 'text', 'Текст' ) ] ),
			c24_f( $g, 'quick_form_title', 'Заголовок формы под баннером' ),
			c24_tab( $g, 'sit', 'Ситуации' ),
			c24_kicker( $g, 'sit_kicker' ),
			c24_title( $g, 'sit_title' ),
			c24_f( $g, 'sit_lead', 'Подзаголовок', 'textarea' ),
			c24_rep( $g, 'sit_cards', 'Карточки', [ c24_s_icon(), c24_s( 'title', 'Заголовок' ), c24_s( 'text', 'Текст', 'textarea' ) ] ),
			c24_f( $g, 'sit_cta_title', 'Плашка: заголовок', 'text', [ 'wrapper' => [ 'width' => 40 ] ] ),
			c24_f( $g, 'sit_cta_sub', 'Плашка: текст', 'text', [ 'wrapper' => [ 'width' => 40 ] ] ),
			c24_f( $g, 'sit_cta_btn', 'Плашка: кнопка', 'text', [ 'wrapper' => [ 'width' => 20 ] ] ),
			c24_tab( $g, 'prices', 'Цены' ),
			c24_kicker( $g, 'sp_kicker' ),
			c24_title( $g, 'sp_title' ),
			c24_f( $g, 'sp_lead', 'Подзаголовок', 'textarea' ),
			c24_f( $g, 'sp_caption', 'Подпись таблицы (для скринридеров)' ),
			c24_f( $g, 'sp_col_1', 'Колонка 1', 'text', [ 'wrapper' => [ 'width' => 50 ] ] ),
			c24_f( $g, 'sp_col_2', 'Колонка 2', 'text', [ 'wrapper' => [ 'width' => 50 ] ] ),
			c24_rep( $g, 'sp_rows', 'Строки таблицы', [ c24_s_icon(), c24_s( 'label', 'Услуга' ), c24_s( 'price', 'Цена «от», €', 'text', [ 'instructions' => 'Число. Пусто — «уточняется», … — цена не утверждена', 'wrapper' => [ 'width' => 22 ] ] ) ] ),
			c24_rep( $g, 'sp_notes', 'Условия справа', [ c24_s( 'title', 'Заголовок', 'text', [ 'wrapper' => [ 'width' => 30 ] ] ), c24_s( 'text', 'Текст', 'textarea' ) ] ),
			c24_f( $g, 'sp_cta_title', 'Кнопка: заголовок', 'text', [ 'wrapper' => [ 'width' => 50 ] ] ),
			c24_f( $g, 'sp_cta_text', 'Кнопка: текст', 'text', [ 'wrapper' => [ 'width' => 50 ] ] ),
			c24_tab( $g, 'calc', 'Из чего цена' ),
			c24_kicker( $g, 'calc_kicker' ),
			c24_title( $g, 'calc_title' ),
			c24_f( $g, 'calc_lead', 'Подзаголовок', 'textarea' ),
			c24_rep( $g, 'calc_items', 'Факторы (номера автоматически)', [ c24_s_icon(), c24_s( 'title', 'Заголовок' ), c24_s( 'text', 'Текст', 'textarea' ) ] ),
			c24_f( $g, 'calc_total_title', 'Итоговая карточка: заголовок', 'text', [ 'wrapper' => [ 'width' => 30 ] ] ),
			c24_f( $g, 'calc_total_text', 'Итоговая карточка: текст', 'textarea', [ 'wrapper' => [ 'width' => 70 ] ] ),
			c24_f( $g, 'calc_note', 'Примечание под блоком', 'textarea' ),
			c24_tab( $g, 'est', 'Вызов мастера' ),
			c24_kicker( $g, 'est_kicker' ),
			c24_title( $g, 'est_title' ),
			c24_f( $g, 'est_lead', 'Текст', 'textarea' ),
			c24_tab( $g, 'types', 'Типы' ),
			c24_kicker( $g, 'types_kicker' ),
			c24_title( $g, 'types_title' ),
			c24_f( $g, 'types_lead', 'Подзаголовок', 'textarea' ),
			c24_rep( $g, 'types_cards', 'Карточки', [ c24_s_icon(), c24_s( 'title', 'Заголовок (испанский термин: <span lang="es">…</span>)' ), c24_s( 'text', 'Текст', 'textarea' ) ] ),
			c24_tab( $g, 'nd', 'Блок с фото' ),
			c24_f( $g, 'nd_id', 'Якорь блока', 'text', [ 'placeholder' => 'no-damage', 'wrapper' => [ 'width' => 20 ] ] ),
			c24_f( $g, 'nd_photo', 'Фото', 'image', [ 'wrapper' => [ 'width' => 30 ] ] ),
			c24_f( $g, 'nd_badge', 'Подпись на фото', 'text', [ 'wrapper' => [ 'width' => 50 ] ] ),
			c24_kicker( $g, 'nd_kicker' ),
			c24_title( $g, 'nd_title' ),
			c24_f( $g, 'nd_lead', 'Подзаголовок', 'textarea' ),
			c24_rep( $g, 'nd_items', 'Пункты', [ c24_s_icon(), c24_s( 'title', 'Заголовок', 'text', [ 'wrapper' => [ 'width' => 26 ] ] ), c24_s( 'text', 'Текст', 'wysiwyg' ) ], [ 'layout' => 'row' ] ),
		],
		c24_fields_flow( $g ),
		[
			c24_tab( $g, 'edge', 'Преимущества' ),
			c24_f( $g, 'edge_photo', 'Фото мастера', 'image', [ 'wrapper' => [ 'width' => 33 ] ] ),
			c24_f( $g, 'edge_city', 'Фото города', 'image', [ 'wrapper' => [ 'width' => 33 ] ] ),
			c24_f( $g, 'edge_chip', 'Плашка на фото', 'text', [ 'wrapper' => [ 'width' => 34 ] ] ),
			c24_kicker( $g, 'edge_kicker' ),
			c24_title( $g, 'edge_title' ),
			c24_rep( $g, 'edge_items', 'Пункты', [ c24_s_icon(), c24_s( 'title', 'Заголовок' ), c24_s( 'text', 'Текст', 'textarea' ) ] ),
		],
		c24_fields_reviews( $g ),
		[
			c24_tab( $g, 'docs', 'Документы' ),
			c24_kicker( $g, 'docs_kicker' ),
			c24_title( $g, 'docs_title' ),
			c24_f( $g, 'docs_card_text', 'Карточка: текст', 'textarea', [ 'wrapper' => [ 'width' => 60 ] ] ),
			c24_f( $g, 'docs_card_label', 'Карточка: «Подходят:»', 'text', [ 'wrapper' => [ 'width' => 40 ] ] ),
			c24_rep( $g, 'docs_chips', 'Документы-метки', [ c24_s( 'text', 'Документ' ), c24_s( 'es', 'Испанский термин', 'true_false', [ 'wrapper' => [ 'width' => 20 ] ] ) ] ),
			c24_rep( $g, 'docs_items', 'Пункты', [ c24_s( 'title', 'Заголовок', 'text', [ 'wrapper' => [ 'width' => 30 ] ] ), c24_s( 'text', 'Текст', 'wysiwyg' ) ], [ 'layout' => 'row' ] ),
		],
		c24_fields_geo( $g ),
		c24_fields_longread( $g ),
		c24_fields_faq( $g ),
		[
			c24_tab( $g, 'more', 'Другие услуги' ),
			c24_kicker( $g, 'more_kicker' ),
			c24_title( $g, 'more_title' ),
		],
		c24_fields_final( $g, true ),
		c24_fields_modal( $g ),
		c24_fields_seo( $g, true )
	), c24_loc_template( 'templates/page-service.php' ) );

	/* ---- Контакты ---- */
	$g = 'ct';
	c24_group( 'contacts', 'Контакты', array_merge(
		[
			c24_tab( $g, 'hero', 'Первый экран' ),
			c24_f( $g, 'crumb', 'Название в хлебных крошках', 'text', [ 'wrapper' => [ 'width' => 30 ] ] ),
			c24_title( $g, 'sh_title', 'Заголовок H1' ),
			c24_f( $g, 'sh_lead', 'Подзаголовок', 'textarea' ),
			c24_f( $g, 'ct_call_label', 'Звонок: надпись', 'text', [ 'wrapper' => [ 'width' => 30 ] ] ),
			c24_f( $g, 'ct_call_text', 'Звонок: текст', 'textarea', [ 'wrapper' => [ 'width' => 70 ] ] ),
			c24_f( $g, 'ct_wa_text', 'WhatsApp: текст', 'textarea', [ 'wrapper' => [ 'width' => 50 ] ] ),
			c24_f( $g, 'ct_mail_text', 'E-mail: текст', 'textarea', [ 'wrapper' => [ 'width' => 50 ] ] ),
			c24_rep( $g, 'ct_facts', 'Факты', [ c24_s_icon(), c24_s( 'title', 'Заголовок' ), c24_s( 'text', 'Текст' ), c24_s( 'link', '#якорь (необязательно)', 'text', [ 'wrapper' => [ 'width' => 18 ] ] ) ] ),
			c24_f( $g, 'ct_form_title', 'Форма: заголовок', 'text', [ 'wrapper' => [ 'width' => 40 ] ] ),
			c24_f( $g, 'ct_form_lead', 'Форма: текст', 'text', [ 'wrapper' => [ 'width' => 60 ] ] ),
			c24_tab( $g, 'brief', 'Что сообщить' ),
			c24_kicker( $g, 'brief_kicker' ),
			c24_title( $g, 'brief_title' ),
			c24_f( $g, 'brief_lead', 'Подзаголовок', 'textarea' ),
			c24_f( $g, 'brief_sos_text', '112: текст', 'textarea', [ 'wrapper' => [ 'width' => 60 ] ] ),
			c24_f( $g, 'brief_sos_label', '112: подпись', 'text', [ 'wrapper' => [ 'width' => 40 ] ] ),
			c24_rep( $g, 'brief_items', 'Пункты', [ c24_s( 'title', 'Заголовок', 'text', [ 'wrapper' => [ 'width' => 26 ] ] ), c24_s( 'text', 'Текст', 'wysiwyg' ) ], [ 'layout' => 'row' ] ),
		],
		c24_fields_geo( $g ),
		[
			c24_tab( $g, 'company', 'Данные компании' ),
			c24_kicker( $g, 'company_kicker' ),
			c24_title( $g, 'company_title' ),
			c24_f( $g, 'company_lead', 'Подзаголовок', 'textarea' ),
			c24_f( $g, 'req_title', 'Реквизиты: заголовок' ),
			c24_rep( $g, 'req_rows', 'Реквизиты', [
				c24_s( 'label', 'Название', 'text', [ 'wrapper' => [ 'width' => 30 ] ] ),
				c24_s( 'type', 'Значение', 'select', [ 'choices' => [ 'text' => 'Текст', 'nif' => 'NIF из «Контактов»', 'phone' => 'Телефон из «Контактов»', 'email' => 'E-mail из «Контактов»' ], 'wrapper' => [ 'width' => 20 ] ] ),
				c24_s( 'value', 'Текст' ),
			] ),
			c24_f( $g, 'google_title', 'Google: заголовок', 'text', [ 'wrapper' => [ 'width' => 50 ] ] ),
			c24_f( $g, 'google_text', 'Google: текст', 'textarea', [ 'wrapper' => [ 'width' => 50 ] ] ),
			c24_f( $g, 'google_btn_1', 'Google: кнопка 1', 'text', [ 'wrapper' => [ 'width' => 50 ] ] ),
			c24_f( $g, 'google_btn_2', 'Google: кнопка 2', 'text', [ 'wrapper' => [ 'width' => 50 ] ] ),
			c24_f( $g, 'legal_title', 'Правовая информация: заголовок' ),
			c24_rep( $g, 'legal_links', 'Правовые документы', [ c24_s( 'text', 'Текст' ), c24_s( 'page', 'Страница', 'post_object', [ 'post_type' => [ 'page' ], 'return_format' => 'id', 'allow_null' => 1 ] ) ] ),
		],
		c24_fields_faq( $g ),
		c24_fields_modal( $g ),
		c24_fields_seo( $g )
	), c24_loc_template( 'templates/page-contacts.php' ) );

	/* ---- Обычные страницы: только SEO ---- */
	c24_group( 'page', 'SEO', c24_fields_seo( 'page' ), [ [
		[ 'param' => 'post_type', 'operator' => '==', 'value' => 'page' ],
		[ 'param' => 'page_template', 'operator' => '==', 'value' => 'default' ],
		[ 'param' => 'page_type', 'operator' => '!=', 'value' => 'front_page' ],
	] ] );
} );
