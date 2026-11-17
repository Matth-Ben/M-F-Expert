<?php

namespace WPGutenberg\Admin;

/**
 * Base of the theme's back-office settings pages (Coordonnées, Bandeau d'annonce, En-tête, Pied de page).
 *
 * A page = one option (array) described by fields(): `id => [ title, type, default, description, label, options, section ]`.
 * Types: text, textarea, email, tel, url, link (URL or token #appeler / #chat), checkbox, select, image (attachment ID),
 * editor (one line: bold, italic, link).
 * Values are read with static::values() (defaults until the page is saved) through the wpg_* helpers.
 */
abstract class OptionsPage {

	/** Option name, also used as settings group and page slug. */
	public const OPTION = '';

	/** Markup allowed in `editor` fields (a single line: no paragraphs). */
	public const INLINE_HTML = array(
		'strong' => array(),
		'em'     => array(),
		'a'      => array(
			'href'   => true,
			'target' => true,
			'rel'    => true,
		),
	);

	private string $hook = '';

	/**
	 * Field definitions (see class doc).
	 *
	 * @return array<string, array<string, mixed>>
	 */
	abstract public static function fields(): array;

	/** Page and menu title. */
	abstract protected function title(): string;

	/**
	 * Menu placement: `[ 'parent' => 'options-general.php' ]` for a submenu,
	 * or `[ 'icon' => 'dashicons-…', 'position' => 21 ]` for a top-level entry.
	 *
	 * @return array<string, mixed>
	 */
	abstract protected function menu(): array;

	/**
	 * Fields of a call-to-action button (header, footer): display, text, link, icon.
	 *
	 * @param string $section Section title ('' = stay in the current section).
	 * @param string $label   Checkbox label.
	 * @return array<string, array<string, mixed>>
	 */
	protected static function button_fields( string $section, string $label ): array {
		return array(
			'button_enabled' => array(
				'section' => $section,
				'title'   => __( 'Button', 'wp-gutenberg' ),
				'type'    => 'checkbox',
				'label'   => $label,
				'default' => true,
			),
			'button_label'   => array(
				'title'   => __( 'Button text', 'wp-gutenberg' ),
				'type'    => 'text',
				'default' => 'Appelez-nous',
			),
			'button_url'     => array(
				'title'       => __( 'Button link', 'wp-gutenberg' ),
				'type'        => 'link',
				'default'     => '#appeler',
				'description' => __( '<code>#appeler</code>: phone number of Settings > Contact details · <code>#chat</code>: chat link · or a page address (<code>/contact/</code>) or a full URL (<code>https://…</code>).', 'wp-gutenberg' ),
			),
			'button_icon'    => array(
				'title'   => __( 'Button icon', 'wp-gutenberg' ),
				'type'    => 'select',
				'default' => 'phone',
				'options' => array(
					'phone'    => __( 'Phone', 'wp-gutenberg' ),
					'chat'     => __( 'Chat', 'wp-gutenberg' ),
					'mail'     => __( 'Email', 'wp-gutenberg' ),
					'calendar' => __( 'Calendar', 'wp-gutenberg' ),
					'pin'      => __( 'Location', 'wp-gutenberg' ),
					'none'     => __( 'No icon', 'wp-gutenberg' ),
				),
			),
		);
	}

	/** Optional introduction printed under the page title (links allowed). */
	protected function intro(): string {
		return '';
	}

	public function register(): void {
		add_action( 'admin_menu', array( $this, 'add_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	public function add_page(): void {
		$menu = $this->menu();

		$this->hook = (string) ( isset( $menu['parent'] )
			? add_submenu_page( $menu['parent'], $this->title(), $this->title(), 'manage_options', static::OPTION, array( $this, 'render_page' ) )
			: add_menu_page( $this->title(), $this->title(), 'manage_options', static::OPTION, array( $this, 'render_page' ), $menu['icon'] ?? '', $menu['position'] ?? null ) );
	}

	public function register_settings(): void {
		register_setting( static::OPTION, static::OPTION, array( 'sanitize_callback' => array( $this, 'sanitize' ) ) );
	}

	/**
	 * Media library for `image` fields.
	 *
	 * @param string $hook Current admin page.
	 */
	public function enqueue( string $hook ): void {
		if ( $hook === $this->hook && self::has_type( 'image' ) ) {
			wp_enqueue_media();
		}
	}

	/**
	 * Saved values merged with the defaults (defaults only until the page is saved once).
	 *
	 * @return array<string, mixed>
	 */
	public static function values(): array {
		$saved  = get_option( static::OPTION, null );
		$values = array();

		foreach ( static::fields() as $id => $field ) {
			$default       = $field['default'] ?? ( 'checkbox' === $field['type'] ? false : ( 'image' === $field['type'] ? 0 : '' ) );
			$values[ $id ] = is_array( $saved ) && array_key_exists( $id, $saved ) ? $saved[ $id ] : $default;
		}

		return $values;
	}

	/**
	 * @param mixed $input Submitted value.
	 * @return array<string, mixed>
	 */
	public function sanitize( $input ): array {
		$input = is_array( $input ) ? $input : array();
		$clean = array();

		foreach ( static::fields() as $id => $field ) {
			$value = $input[ $id ] ?? '';

			$clean[ $id ] = match ( $field['type'] ) {
				'checkbox' => ! empty( $value ),
				'image'    => wp_attachment_is_image( absint( $value ) ) ? absint( $value ) : 0,
				'select'   => array_key_exists( (string) $value, $field['options'] ) ? (string) $value : (string) ( $field['default'] ?? '' ),
				'email'    => sanitize_email( (string) $value ),
				'url'      => esc_url_raw( (string) $value ),
				'link'     => preg_match( '/^#[\w-]+$/', (string) $value ) ? (string) $value : esc_url_raw( (string) $value ),
				'textarea' => sanitize_textarea_field( (string) $value ),
				'editor'   => self::sanitize_inline( (string) $value ),
				default    => sanitize_text_field( (string) $value ),
			};
		}

		return $clean;
	}

	/**
	 * One line of light HTML: <script>/<style> removed with their content, whitelist, whitespace collapsed.
	 */
	private static function sanitize_inline( string $html ): string {
		$html = preg_replace( '@<(script|style)[^>]*?>.*?</\1>@si', '', $html );
		$html = wp_kses( $html, self::INLINE_HTML );

		return trim( preg_replace( '/\s+/u', ' ', str_replace( '&nbsp;', ' ', $html ) ) );
	}

	private static function has_type( string $type ): bool {
		return in_array( $type, array_column( static::fields(), 'type' ), true );
	}

	public function render_page(): void {
		$values  = static::values();
		$section = null;
		?>
		<div class="wrap">
			<h1><?php echo esc_html( $this->title() ); ?></h1>
			<?php if ( $this->intro() ) : ?>
				<p><?php echo wp_kses( $this->intro(), array( 'a' => array( 'href' => true ) ) ); ?></p>
			<?php endif; ?>

			<form method="post" action="options.php">
				<?php settings_fields( static::OPTION ); ?>
				<table class="form-table" role="presentation">
					<?php foreach ( static::fields() as $id => $field ) : ?>
						<?php if ( ! empty( $field['section'] ) && $field['section'] !== $section ) : ?>
							<?php $section = $field['section']; ?>
							</table>
							<h2 class="title"><?php echo esc_html( $section ); ?></h2>
							<table class="form-table" role="presentation">
						<?php endif; ?>
						<tr>
							<th scope="row">
								<?php if ( in_array( $field['type'], array( 'checkbox', 'image' ), true ) ) : ?>
									<?php echo esc_html( $field['title'] ); ?>
								<?php else : ?>
									<label for="<?php echo esc_attr( static::OPTION . '_' . $id ); ?>"><?php echo esc_html( $field['title'] ); ?></label>
								<?php endif; ?>
							</th>
							<td>
								<?php $this->render_field( $id, $field, $values[ $id ] ); ?>
								<?php if ( ! empty( $field['description'] ) ) : ?>
									<p class="description" id="<?php echo esc_attr( static::OPTION . '_' . $id . '_description' ); ?>"><?php echo wp_kses( $field['description'], array( 'a' => array( 'href' => true ), 'code' => array() ) ); ?></p>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
		if ( self::has_type( 'image' ) ) {
			$this->image_script();
		}
	}

	/**
	 * @param string               $id    Field id.
	 * @param array<string, mixed> $field Definition.
	 * @param mixed                $value Current value.
	 */
	private function render_field( string $id, array $field, $value ): void {
		$name     = sprintf( '%s[%s]', static::OPTION, $id );
		$html_id  = static::OPTION . '_' . $id;
		$describe = empty( $field['description'] ) ? '' : sprintf( ' aria-describedby="%s_description"', esc_attr( $html_id ) );

		switch ( $field['type'] ) {
			case 'checkbox':
				printf(
					'<label for="%1$s"><input type="checkbox" id="%1$s" name="%2$s" value="1"%3$s%4$s> %5$s</label>',
					esc_attr( $html_id ),
					esc_attr( $name ),
					checked( (bool) $value, true, false ),
					$describe, // phpcs:ignore -- escaped above.
					esc_html( $field['label'] ?? $field['title'] )
				);
				break;

			case 'textarea':
				printf( '<textarea class="large-text" rows="2" id="%1$s" name="%2$s"%3$s>%4$s</textarea>', esc_attr( $html_id ), esc_attr( $name ), $describe, esc_textarea( (string) $value ) ); // phpcs:ignore
				break;

			case 'select':
				printf( '<select id="%1$s" name="%2$s"%3$s>', esc_attr( $html_id ), esc_attr( $name ), $describe ); // phpcs:ignore
				foreach ( $field['options'] as $option => $label ) {
					printf( '<option value="%1$s"%2$s>%3$s</option>', esc_attr( (string) $option ), selected( (string) $value, (string) $option, false ), esc_html( $label ) );
				}
				echo '</select>';
				break;

			case 'image':
				$image = $value ? (string) wp_get_attachment_image_url( (int) $value, 'medium' ) : '';
				?>
				<div data-wpg-image data-title="<?php echo esc_attr( $field['title'] ); ?>" data-button="<?php esc_attr_e( 'Use this image', 'wp-gutenberg' ); ?>">
					<input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ? (string) $value : '' ); ?>">
					<img src="<?php echo esc_url( $image ); ?>" alt="" style="display:block;max-width:180px;max-height:120px;margin-bottom:8px;padding:8px;background:#EBE2D8"<?php echo $image ? '' : ' hidden'; ?>>
					<button type="button" class="button" data-select<?php echo $describe; // phpcs:ignore -- escaped above. ?>><?php esc_html_e( 'Choose an image', 'wp-gutenberg' ); ?></button>
					<button type="button" class="button-link button-link-delete" data-remove<?php echo $value ? '' : ' hidden'; ?>><?php esc_html_e( 'Use the default image', 'wp-gutenberg' ); ?></button>
				</div>
				<?php
				break;

			case 'editor':
				wp_editor(
					(string) $value,
					$html_id,
					array(
						'textarea_name' => $name,
						'textarea_rows' => 2,
						'media_buttons' => false,
						'teeny'         => true,
						'wpautop'       => false,
						'quicktags'     => false,
						'tinymce'       => array(
							'toolbar1'          => 'bold,italic,link,unlink',
							'toolbar2'          => '',
							'forced_root_block' => false,
							'height'            => 80,
						),
					)
				);
				break;

			default:
				$type = in_array( $field['type'], array( 'email', 'tel', 'url' ), true ) ? $field['type'] : 'text'; // `link` accepts #appeler: no type="url".
				printf( '<input type="%1$s" class="regular-text" id="%2$s" name="%3$s" value="%4$s"%5$s>', esc_attr( $type ), esc_attr( $html_id ), esc_attr( $name ), esc_attr( (string) $value ), $describe ); // phpcs:ignore
		}
	}

	/**
	 * Media modal for `image` fields (vanilla JS on top of wp.media).
	 */
	private function image_script(): void {
		?>
		<script>
			document.querySelectorAll('[data-wpg-image]').forEach((field) => {
				const input = field.querySelector('input[type="hidden"]');
				const preview = field.querySelector('img');
				const remove = field.querySelector('[data-remove]');
				let frame;

				field.querySelector('[data-select]').addEventListener('click', () => {
					frame ??= wp.media({
						title: field.dataset.title,
						button: { text: field.dataset.button },
						library: { type: 'image' },
						multiple: false,
					}).on('select', () => {
						const image = frame.state().get('selection').first().toJSON();
						input.value = image.id;
						preview.src = image.sizes?.medium?.url ?? image.url;
						preview.hidden = false;
						remove.hidden = false;
					});
					frame.open();
				});

				remove.addEventListener('click', () => {
					input.value = '';
					preview.hidden = true;
					remove.hidden = true;
				});
			});
		</script>
		<?php
	}
}
