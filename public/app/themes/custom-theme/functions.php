<?php
/**
 * WP Gutenberg — theme bootstrap.
 *
 * No Composer, no template engine: a PSR-4 autoloader for `includes/`,
 * a few global helpers, then every service listed in Theme::SERVICES is booted.
 *
 * @package WPGutenberg
 */

defined( 'ABSPATH' ) || exit;

define( 'WPG_DIR', __DIR__ );
define( 'WPG_URI', get_template_directory_uri() );
define( 'WPG_TEXT_DOMAIN', 'wp-gutenberg' );

spl_autoload_register(
	static function ( string $class ): void {
		$prefix = 'WPGutenberg\\';

		if ( ! str_starts_with( $class, $prefix ) ) {
			return;
		}

		$file = WPG_DIR . '/includes/' . str_replace( '\\', '/', substr( $class, strlen( $prefix ) ) ) . '.php';

		if ( is_file( $file ) ) {
			require $file;
		}
	}
);

require WPG_DIR . '/includes/helpers.php';

WPGutenberg\Theme::boot();
