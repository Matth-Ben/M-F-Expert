<?php
/**
 * Vite integration (dev server + production manifest).
 *
 * Works on the front-end AND in the block editor, so the editor canvas
 * receives exactly the same CSS as the site.
 *
 * @package WPGutenberg
 */

namespace WPGutenberg\Core;

/**
 * Resolves Vite asset URLs.
 */
final class Vite {

	private const BUILD_DIR = 'dist';

	/**
	 * Lazily computed state.
	 *
	 * @var array{hot: bool, server: string, manifest: array<string, array<string, mixed>>}|null
	 */
	private static ?array $state = null;

	/**
	 * Whether the Vite dev server is running (`dist/hot` exists).
	 *
	 * @return bool
	 */
	public static function is_hot(): bool {
		return self::state()['hot'];
	}

	/**
	 * URL of the Vite client (dev only), or null in production.
	 *
	 * @return string|null
	 */
	public static function client(): ?string {
		return self::is_hot() ? self::state()['server'] . '/@vite/client' : null;
	}

	/**
	 * Public URL of a source asset, e.g. `src/stylesheets/styles.css`.
	 *
	 * Returns an empty string (and logs in debug) when the asset is not in the manifest,
	 * so a missing build never takes the site down.
	 *
	 * @param string $asset Source path relative to the theme root.
	 * @return string
	 */
	public static function asset( string $asset ): string {
		$state = self::state();
		$asset = ltrim( $asset, '/' );

		if ( $state['hot'] ) {
			return $state['server'] . '/' . $asset;
		}

		if ( ! isset( $state['manifest'][ $asset ]['file'] ) ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( sprintf( '[wp-gutenberg] Unknown Vite asset "%s". Did you run `pnpm build`?', $asset ) ); // phpcs:ignore
			}
			return '';
		}

		return WPG_URI . '/' . self::BUILD_DIR . '/' . $state['manifest'][ $asset ]['file'];
	}

	/**
	 * Read hot file / manifest once per request.
	 *
	 * @return array{hot: bool, server: string, manifest: array<string, array<string, mixed>>}
	 */
	private static function state(): array {
		if ( null !== self::$state ) {
			return self::$state;
		}

		$dir      = WPG_DIR . '/' . self::BUILD_DIR;
		$hot_file = $dir . '/hot';

		if ( is_file( $hot_file ) ) {
			return self::$state = array(
				'hot'      => true,
				'server'   => rtrim( trim( (string) file_get_contents( $hot_file ) ), '/' ), // phpcs:ignore
				'manifest' => array(),
			);
		}

		$manifest_file = $dir . '/.vite/manifest.json';
		$manifest      = is_file( $manifest_file ) ? json_decode( (string) file_get_contents( $manifest_file ), true ) : array(); // phpcs:ignore

		return self::$state = array(
			'hot'      => false,
			'server'   => '',
			'manifest' => is_array( $manifest ) ? $manifest : array(),
		);
	}
}
