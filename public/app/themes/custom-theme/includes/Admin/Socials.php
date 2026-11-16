<?php
/**
 * Settings > Socials page (native Settings API, no ACF).
 *
 * @package WPGutenberg
 */

namespace WPGutenberg\Admin;

/**
 * Social URLs settings.
 */
final class Socials {

	public const OPTION = 'socials';

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'admin_menu', array( $this, 'add_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Add the options page.
	 *
	 * @return void
	 */
	public function add_page(): void {
		add_options_page(
			__( 'Socials', 'wp-gutenberg' ),
			__( 'Socials', 'wp-gutenberg' ),
			'manage_options',
			self::OPTION,
			array( $this, 'render_page' )
		);
	}

	/**
	 * Register setting + one URL field per network.
	 *
	 * @return void
	 */
	public function register_settings(): void {
		register_setting( self::OPTION, self::OPTION, array( 'sanitize_callback' => array( $this, 'sanitize' ) ) );
		add_settings_section( self::OPTION, '', '__return_empty_string', self::OPTION );

		$values = (array) get_option( self::OPTION, array() );

		foreach ( wpg_socials_config() as $id => $network ) {
			add_settings_field(
				$id,
				$network['title'],
				static function () use ( $id, $values ): void {
					printf(
						'<input type="url" class="regular-text" name="%1$s[%2$s]" value="%3$s" placeholder="https://">',
						esc_attr( self::OPTION ),
						esc_attr( $id ),
						esc_attr( $values[ $id ] ?? '' )
					);
				},
				self::OPTION,
				self::OPTION
			);
		}
	}

	/**
	 * Keep only known networks, as URLs.
	 *
	 * @param mixed $input Raw input.
	 * @return array<string, string>
	 */
	public function sanitize( $input ): array {
		$clean = array();

		foreach ( array_keys( wpg_socials_config() ) as $id ) {
			if ( ! empty( $input[ $id ] ) ) {
				$clean[ $id ] = esc_url_raw( $input[ $id ] );
			}
		}

		return $clean;
	}

	/**
	 * Page markup.
	 *
	 * @return void
	 */
	public function render_page(): void {
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( self::OPTION );
				do_settings_sections( self::OPTION );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}
}
