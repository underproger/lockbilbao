<?php
/**
 * Импорт контента: языки Polylang (ES — основной, без префикса в URL; EU; EN), медиа, страницы с шаблонами,
 * связи переводов, меню, переводы строк, форма Contact Form 7 и контакты.
 *
 * Запуск: «Инструменты → Импорт CerrajeroUCR» или WP-CLI: wp c24 import
 * Повторный запуск обновляет те же страницы (ищутся по ключу _c24_key), а не создаёт дубли.
 *
 * Данные: import/pages.json (структура) и import/{es,eu,en}.json (тексты на языке).
 */

defined( 'ABSPATH' ) || exit;

class C24_Import {

	/** @var string[] журнал */
	public $log = [];

	/** @var array язык сайта => файл с текстами (для проверки вёрстки можно подставить другой файл) */
	public $sources = [ 'es' => 'es', 'eu' => 'eu', 'en' => 'en' ];

	private $structure = [];
	private $data      = [];
	private $media     = []; // файл => [ lang => attachment ID ]
	private $pages     = []; // ключ => [ lang => post ID ]
	private $fields    = []; // группа => [ имя => поле ]

	const LANGS = [
		'es' => [ 'locale' => 'es_ES', 'name' => 'Español', 'flag' => 'es', 'order' => 0 ],
		'eu' => [ 'locale' => 'eu', 'name' => 'Euskara', 'flag' => 'basque', 'order' => 1 ],
		'en' => [ 'locale' => 'en_GB', 'name' => 'English', 'flag' => 'gb', 'order' => 2 ],
	];

	public function run() {
		@set_time_limit( 600 );
		if ( ! function_exists( 'PLL' ) || ! function_exists( 'acf_get_fields' ) ) {
			$this->log[] = 'Нужны активные Polylang Pro и Secure Custom Fields.';
			return false;
		}
		$dir             = C24_DIR . '/import/';
		$this->structure = json_decode( (string) file_get_contents( $dir . 'pages.json' ), true );
		foreach ( $this->sources as $lang => $file ) {
			$this->data[ $lang ] = json_decode( (string) file_get_contents( $dir . $file . '.json' ), true );
			if ( ! is_array( $this->data[ $lang ] ) ) {
				$this->log[] = "Не прочитан import/{$file}.json";
				return false;
			}
		}

		$this->languages();
		$this->load_fields();
		$this->import_media();
		$this->import_pages();
		$this->import_fields();
		$this->import_menus();
		$this->import_strings();
		$this->import_form();
		$this->import_options();
		$this->site_basics();

		update_option( 'permalink_structure', '/%postname%/' );
		delete_option( 'rewrite_rules' ); // правила с языковыми префиксами пересоберутся при следующем запросе
		PLL()->model->clean_languages_cache();
		delete_transient( 'c24_strings_' . C24_VER );
		$this->log[] = 'Готово.';
		return true;
	}

	/* ---------------- Языки ---------------- */

	private function languages() {
		$model = PLL()->model;
		if ( ! method_exists( $model, 'add_language' ) && class_exists( 'PLL_Admin_Model' ) ) {
			$pll_opt = get_option( 'polylang', [] );
			$model   = new PLL_Admin_Model( $pll_opt );
		}
		foreach ( self::LANGS as $slug => $l ) {
			if ( $model->get_language( $slug ) ) {
				continue;
			}
			$r = $model->add_language( [
				'name'       => $l['name'],
				'slug'       => $slug,
				'locale'     => $l['locale'],
				'rtl'        => 0,
				'term_group' => $l['order'],
				'flag'       => $l['flag'],
			] );
			$this->log[] = is_wp_error( $r ) ? 'Язык ' . $slug . ': ' . $r->get_error_message() : 'Добавлен язык ' . $l['name'];
		}
		$model->clean_languages_cache();

		if ( method_exists( $model, 'update_default_lang' ) ) {
			$model->update_default_lang( 'es' );
		}
		$opt                  = get_option( 'polylang', [] );
		$opt['default_lang']  = 'es';
		$opt['force_lang']    = 1; // язык в каталоге: /eu/, /en/
		$opt['hide_default']  = 1; // испанский без префикса
		$opt['rewrite']       = 1;
		$opt['redirect_lang'] = 1; // главная на /eu/, /en/ — без названия страницы
		$opt['browser']       = 0;
		$opt['media_support'] = 1;
		update_option( 'polylang', $opt );
		$model->clean_languages_cache();
		PLL()->model->clean_languages_cache();
		if ( function_exists( 'PLL' ) && isset( PLL()->options ) && is_array( PLL()->options ) ) {
			PLL()->options = array_merge( PLL()->options, $opt );
		}
		// Медиа становятся переводимыми сразу, в этом же запуске.
		if ( isset( PLL()->model->options ) && is_array( PLL()->model->options ) ) {
			PLL()->model->options['media_support'] = 1;
		}
		if ( isset( PLL()->model->cache ) && method_exists( PLL()->model->cache, 'clean' ) ) {
			PLL()->model->cache->clean( 'post_types' );
		}
	}

	/* ---------------- Поля SCF ---------------- */

	private function load_fields() {
		foreach ( [ 'home', 'services', 'service', 'contacts', 'page', 'options' ] as $g ) {
			$this->fields[ $g ] = [];
			foreach ( (array) acf_get_fields( 'group_c24_' . $g ) as $f ) {
				if ( 'tab' !== $f['type'] ) {
					$this->fields[ $g ][ $f['name'] ] = $f;
				}
			}
		}
	}

	/* ---------------- Медиа ---------------- */

	private function import_media() {
		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		$files = $this->structure['media'];
		foreach ( $files as $file ) {
			$src = C24_DIR . '/assets/img/' . $file;
			if ( ! file_exists( $src ) ) {
				$this->log[] = 'Нет файла ' . $file;
				continue;
			}
			$es = $this->find_media( $file, 'es' );
			if ( $es && get_post_meta( $es, '_c24_md5', true ) !== md5_file( $src ) && md5_file( (string) get_attached_file( $es ) ) !== md5_file( $src ) ) {
				// Картинка в теме обновилась — загружаем новый файл (новое имя сбрасывает кэш браузеров и CDN).
				$upload = wp_upload_bits( basename( $file ), null, (string) file_get_contents( $src ) );
				if ( empty( $upload['error'] ) ) {
					update_attached_file( $es, $upload['file'] );
					wp_update_attachment_metadata( $es, wp_generate_attachment_metadata( $es, $upload['file'] ) );
					foreach ( array_keys( $this->sources ) as $lang ) {
						$tr = 'es' === $lang ? 0 : $this->find_media( $file, $lang );
						if ( $tr ) {
							update_post_meta( $tr, '_wp_attached_file', get_post_meta( $es, '_wp_attached_file', true ) );
							update_post_meta( $tr, '_wp_attachment_metadata', wp_get_attachment_metadata( $es ) );
						}
					}
					$this->log[] = 'Обновлена картинка ' . $file;
				}
			}
			if ( ! $es ) {
				$upload = wp_upload_bits( basename( $file ), null, (string) file_get_contents( $src ) );
				if ( ! empty( $upload['error'] ) ) {
					$this->log[] = $file . ': ' . $upload['error'];
					continue;
				}
				$type = wp_check_filetype( $upload['file'] );
				$es   = wp_insert_attachment( [
					'post_mime_type' => $type['type'],
					'post_title'     => pathinfo( $file, PATHINFO_FILENAME ),
					'post_status'    => 'inherit',
				], $upload['file'] );
				wp_update_attachment_metadata( $es, wp_generate_attachment_metadata( $es, $upload['file'] ) );
				update_post_meta( $es, '_c24_file', $file );
				pll_set_post_language( $es, 'es' );
			}
			$ids = [ 'es' => $es ];
			foreach ( array_keys( $this->sources ) as $lang ) {
				if ( 'es' === $lang ) {
					continue;
				}
				$tr = $this->find_media( $file, $lang );
				if ( ! $tr ) {
					// Перевод медиа в Polylang — отдельная запись вложения с тем же файлом.
					$tr = wp_insert_attachment( [
						'post_mime_type' => get_post_mime_type( $es ),
						'post_title'     => get_the_title( $es ),
						'post_status'    => 'inherit',
					], get_attached_file( $es ) );
					update_post_meta( $tr, '_wp_attachment_metadata', wp_get_attachment_metadata( $es ) );
					update_post_meta( $tr, '_wp_attached_file', get_post_meta( $es, '_wp_attached_file', true ) );
					update_post_meta( $tr, '_c24_file', $file );
					pll_set_post_language( $tr, $lang );
				}
				$ids[ $lang ] = $tr;
			}
			if ( count( $ids ) > 1 ) {
				pll_save_post_translations( $ids );
			}
			foreach ( $ids as $lang => $id ) {
				$alt = $this->data[ $lang ]['media'][ $file ] ?? '';
				update_post_meta( $id, '_wp_attachment_image_alt', $alt );
			}
			foreach ( $ids as $id ) {
				update_post_meta( $id, '_c24_md5', md5_file( $src ) );
			}
			$this->media[ $file ] = $ids;
		}
		$this->log[] = 'Медиа: ' . count( $this->media );
	}

	private function find_media( $file, $lang ) {
		$q = get_posts( [ 'post_type' => 'attachment', 'post_status' => 'inherit', 'meta_key' => '_c24_file', 'meta_value' => $file, 'lang' => '', 'fields' => 'ids', 'posts_per_page' => -1, 'suppress_filters' => true ] );
		return $this->pick_lang( $q, $lang );
	}

	/* ---------------- Страницы ---------------- */

	private function find_page( $key, $lang ) {
		$q = get_posts( [ 'post_type' => 'page', 'post_status' => 'any', 'meta_key' => '_c24_key', 'meta_value' => $key, 'lang' => '', 'fields' => 'ids', 'posts_per_page' => -1, 'suppress_filters' => true ] );
		return $this->pick_lang( $q, $lang );
	}

	/** Из списка ID — запись нужного языка (фильтр lang в get_posts при первом запуске ещё не работает). */
	private function pick_lang( $ids, $lang ) {
		foreach ( (array) $ids as $id ) {
			if ( pll_get_post_language( (int) $id ) === $lang ) {
				return (int) $id;
			}
		}
		return 0;
	}

	private function import_pages() {
		// Родители раньше детей: порядок в pages.json
		foreach ( $this->structure['pages'] as $order => $p ) {
			$ids = [];
			foreach ( array_keys( $this->sources ) as $lang ) {
				$d = $this->data[ $lang ]['pages'][ $p['key'] ] ?? null;
				if ( ! $d ) {
					continue;
				}
				$parent = ! empty( $p['parent'] ) ? ( $this->pages[ $p['parent'] ][ $lang ] ?? 0 ) : 0;
				$args   = [
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_title'   => $d['title'],
					'post_name'    => $d['slug'],
					'post_parent'  => $parent,
					'menu_order'   => $p['order'] ?? $order,
					'post_content' => isset( $d['content'] ) ? $this->resolve( $d['content'], $lang ) : '',
				];
				$id = $this->find_page( $p['key'], $lang );
				if ( $id ) {
					$args['ID'] = $id;
					wp_update_post( wp_slash( $args ) );
				} else {
					$id = wp_insert_post( wp_slash( $args ) );
				}
				update_post_meta( $id, '_c24_key', $p['key'] );
				update_post_meta( $id, '_wp_page_template', $p['template'] ?? 'default' );
				pll_set_post_language( $id, $lang );
				// Язык уже назначен — повторно сохраняем адрес и родителя (уникальность slug — в пределах языка).
				wp_update_post( [ 'ID' => $id, 'post_name' => $d['slug'], 'post_parent' => $parent ] );
				$ids[ $lang ] = $id;
			}
			if ( count( $ids ) > 1 ) {
				pll_save_post_translations( $ids );
			}
			$this->pages[ $p['key'] ] = $ids;
		}

		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $this->pages['home']['es'] );
		PLL()->model->clean_languages_cache(); // у языков кэшируется ID главной
		$this->log[] = 'Страниц: ' . count( $this->pages ) . ' × ' . count( $this->sources ) . ' языка';
	}

	/* ---------------- Значения полей ---------------- */

	private function group_for( $p ) {
		if ( 'home' === $p['key'] ) {
			return 'home';
		}
		$map = [
			'templates/page-services.php' => 'services',
			'templates/page-service.php'  => 'service',
			'templates/page-contacts.php' => 'contacts',
		];
		return $map[ $p['template'] ?? '' ] ?? 'page';
	}

	private function import_fields() {
		foreach ( $this->structure['pages'] as $p ) {
			$g = $this->group_for( $p );
			foreach ( $this->pages[ $p['key'] ] as $lang => $id ) {
				$values = $this->data[ $lang ]['pages'][ $p['key'] ]['fields'] ?? [];
				foreach ( $values as $name => $value ) {
					if ( empty( $this->fields[ $g ][ $name ] ) ) {
						$this->log[] = "Поле {$name} не найдено в группе {$g}";
						continue;
					}
					update_field( $this->fields[ $g ][ $name ]['key'], $this->resolve( $value, $lang ), $id );
				}
			}
		}
	}

	/**
	 * Подстановки в значениях: img:файл → ID картинки, page:ключ → ID страницы,
	 * {url:ключ} → адрес страницы, {home} → адрес главной (на языке значения).
	 */
	private function resolve( $value, $lang ) {
		if ( is_array( $value ) ) {
			foreach ( $value as $k => $v ) {
				$value[ $k ] = $this->resolve( $v, $lang );
			}
			return $value;
		}
		if ( ! is_string( $value ) ) {
			return $value;
		}
		if ( 0 === strpos( $value, 'img:' ) ) {
			$file = substr( $value, 4 );
			return $this->media[ $file ][ $lang ] ?? ( $this->media[ $file ]['es'] ?? 0 );
		}
		if ( 0 === strpos( $value, 'page:' ) ) {
			return $this->pages[ substr( $value, 5 ) ][ $lang ] ?? 0;
		}
		return preg_replace_callback( '/\{(url:([a-z0-9-]+)|home)\}/', function ( $m ) use ( $lang ) {
			if ( 'home' === $m[1] ) {
				return $this->page_url( 'home', $lang );
			}
			return $this->page_url( $m[2], $lang );
		}, $value );
	}

	private function page_url( $key, $lang ) {
		$id = $this->pages[ $key ][ $lang ] ?? 0;
		if ( ! $id ) {
			return '#';
		}
		if ( 'home' === $key ) {
			return function_exists( 'pll_home_url' ) ? pll_home_url( $lang ) : home_url( '/' );
		}
		$url = get_permalink( $id );
		return $url ?: '#';
	}

	/* ---------------- Меню ---------------- */

	private function import_menus() {
		$theme     = get_option( 'stylesheet' );
		$opt       = get_option( 'polylang', [] );
		$locations = get_theme_mod( 'nav_menu_locations', [] );
		$names     = [ 'menu-header' => 'Шапка', 'menu-mobile' => 'Мобильное меню', 'menu-footer' => 'Подвал' ];
		foreach ( array_keys( $this->sources ) as $lang ) {
			foreach ( $names as $loc => $title ) {
				$items = $this->data[ $lang ]['menus'][ $loc ] ?? [];
				$name  = $title . ' (' . strtoupper( $lang ) . ')';
				$menu  = wp_get_nav_menu_object( $name );
				$mid   = $menu ? (int) $menu->term_id : (int) wp_create_nav_menu( $name );
				foreach ( (array) wp_get_nav_menu_items( $mid ) as $old ) {
					wp_delete_post( $old->ID, true );
				}
				foreach ( $items as $i => $it ) {
					$args = [ 'menu-item-title' => $it['title'], 'menu-item-status' => 'publish', 'menu-item-position' => $i + 1 ];
					if ( 0 === strpos( $it['link'], 'page:' ) ) {
						$pid                           = $this->pages[ substr( $it['link'], 5 ) ][ $lang ] ?? 0;
						$args['menu-item-type']        = 'post_type';
						$args['menu-item-object']      = 'page';
						$args['menu-item-object-id']   = $pid;
					} else {
						$args['menu-item-type'] = 'custom';
						$args['menu-item-url']  = $it['link'];
					}
					wp_update_nav_menu_item( $mid, 0, $args );
				}
				$opt['nav_menus'][ $theme ][ $loc ][ $lang ] = $mid;
				if ( 'es' === $lang ) {
					$locations[ $loc ] = $mid;
				}
			}
		}
		update_option( 'polylang', $opt );
		set_theme_mod( 'nav_menu_locations', $locations );
		$this->log[] = 'Меню: 3 × ' . count( $this->sources );
	}

	/* ---------------- Перевод строк ---------------- */

	private function import_strings() {
		$model = PLL()->model;
		foreach ( array_keys( $this->sources ) as $lang ) {
			$strings  = $this->data[ $lang ]['strings'] ?? [];
			$language = $model->get_language( $lang );
			if ( ! $language || ! $strings ) {
				continue;
			}
			$mo = new PLL_MO();
			$mo->import_from_db( $language );
			foreach ( $strings as $src => $tr ) {
				if ( '' !== (string) $tr ) {
					$mo->add_entry( $mo->make_entry( $src, $tr ) );
				}
			}
			$mo->export_to_db( $language );
			$this->log[] = 'Строк ' . strtoupper( $lang ) . ': ' . count( $strings );
		}
	}

	/* ---------------- Contact Form 7 ---------------- */

	private function import_form() {
		if ( ! class_exists( 'WPCF7_ContactForm' ) ) {
			$this->log[] = 'Contact Form 7 не активен — форма не создана.';
			return;
		}
		$id   = function_exists( 'c24_form_id' ) ? c24_form_id() : 0;
		$form = $id ? wpcf7_contact_form( $id ) : WPCF7_ContactForm::get_template( [ 'title' => 'CerrajeroUCR — заявка' ] );
		$to   = $this->structure['options']['email'] ?? get_option( 'admin_email' );
		if ( $id ) {
			$mail = $form->prop( 'mail' );
			if ( ! empty( $mail['recipient'] ) ) {
				// Адрес получателя могли поменять в админке — заменяем только старую заглушку.
				$to = trim( str_ireplace( 'info@cerrajero24.es', $to, $mail['recipient'] ), ', ' );
			}
		}
		$props = [
			'form' => c24_cf7_form_template(),
			'mail' => [
				'active'             => true,
				'subject'            => 'CerrajeroUCR: [form]',
				'sender'             => 'CerrajeroUCR <wordpress@' . wp_parse_url( home_url(), PHP_URL_HOST ) . '>',
				'recipient'          => $to,
				'body'               => c24_cf7_mail_body(),
				'additional_headers' => '',
				'attachments'        => '[photo]',
				'use_html'           => false,
				'exclude_blank'      => true,
			],
		];
		$form->set_properties( $props );
		$form->set_title( 'CerrajeroUCR — заявка' );
		$saved = $form->save();
		update_post_meta( $saved, '_c24_form', 'lead' );
		update_option( 'c24_form_id', $saved );
		$this->log[] = 'Форма CF7: #' . $saved;
	}

	/* ---------------- Название сайта, стандартный контент ---------------- */

	private function site_basics() {
		$name = (string) get_option( 'blogname' );
		// Пустое, стандартное, старое название или кириллические буквы, похожие на латиницу («Сerrajero»).
		if ( '' === trim( $name ) || in_array( $name, [ 'Cerrajero24', 'My WordPress', 'Мой WordPress' ], true ) || preg_match( '/[\x{0400}-\x{04FF}]/u', $name ) ) {
			update_option( 'blogname', C24_BRAND );
			$this->log[] = 'Название сайта: ' . C24_BRAND;
		}
		// «Sample Page» / «Hello world!», созданные при установке WordPress (если их не редактировали) — в корзину.
		$trash = get_posts( [ 'post_type' => [ 'page', 'post' ], 'post_status' => 'any', 'name' => '', 'lang' => '', 'posts_per_page' => -1, 'suppress_filters' => true, 'post_name__in' => [ 'sample-page', 'hello-world' ] ] );
		foreach ( $trash as $post ) {
			if ( get_post_meta( $post->ID, '_c24_key', true ) ) {
				continue;
			}
			if ( false !== strpos( $post->post_content, 'wp-admin/' ) || false !== stripos( $post->post_content, 'This is an example page' ) || false !== stripos( $post->post_content, 'Welcome to WordPress' ) ) {
				wp_trash_post( $post->ID );
				$this->log[] = 'В корзину: «' . $post->post_title . '»';
			}
		}
	}

	/* ---------------- Контакты ---------------- */

	private function import_options() {
		foreach ( (array) ( $this->structure['options'] ?? [] ) as $name => $value ) {
			$cur = (string) c24_field( $name, 'option' );
			if ( ! empty( $this->fields['options'][ $name ] ) && ( '' === $cur || false !== stripos( $cur, 'cerrajero24' ) ) ) {
				update_field( $this->fields['options'][ $name ]['key'], $value, 'option' );
			}
		}
	}
}

/* ---------------- Запуск ---------------- */

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	/**
	 * Импорт контента CerrajeroUCR.
	 *
	 * ## OPTIONS
	 * [--source=<map>]
	 * : Подмена файлов текстов, например es:ru — загрузить import/ru.json в испанскую версию (проверка вёрстки).
	 */
	WP_CLI::add_command( 'c24 import', function ( $args, $assoc ) {
		$imp = new C24_Import();
		if ( ! empty( $assoc['source'] ) ) {
			foreach ( explode( ',', $assoc['source'] ) as $pair ) {
				list( $lang, $file ) = array_pad( explode( ':', $pair ), 2, '' );
				if ( $lang && $file ) {
					$imp->sources[ $lang ] = $file;
				}
			}
		}
		$ok = $imp->run();
		foreach ( $imp->log as $line ) {
			WP_CLI::log( $line );
		}
		$ok ? WP_CLI::success( 'Импорт завершён' ) : WP_CLI::error( 'Импорт не выполнен' );
	} );
}

add_action( 'admin_menu', function () {
	add_management_page( 'Импорт CerrajeroUCR', 'Импорт CerrajeroUCR', 'manage_options', 'c24-import', function () {
		$log = null;
		if ( isset( $_POST['c24_import'] ) && check_admin_referer( 'c24_import' ) ) {
			$imp = new C24_Import();
			$imp->run();
			$log = $imp->log;
		}
		?>
		<div class="wrap">
			<h1>Импорт CerrajeroUCR</h1>
			<p>Создаёт языки (испанский — основной, баскский, английский), загружает картинки, создаёт и заполняет страницы на трёх языках, меню, переводы строк, форму заявки Contact Form 7.</p>
			<p>Повторный запуск <b>перезаписывает</b> тексты страниц, созданных импортом, значениями из файлов темы.</p>
			<form method="post">
				<?php wp_nonce_field( 'c24_import' ); ?>
				<p><button class="button button-primary" name="c24_import" value="1">Импортировать</button></p>
			</form>
			<?php if ( $log ) : ?>
				<pre><?=esc_html( implode( "\n", $log ) );?></pre>
			<?php endif; ?>
		</div>
		<?php
	} );
} );
