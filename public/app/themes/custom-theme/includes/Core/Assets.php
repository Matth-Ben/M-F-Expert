<?php
/**
 * Front-end assets.
 *
 * Global CSS (Tailwind) + global JS (Barba, Lenis, GSAP…) come from Vite.
 * Block-specific CSS/JS is declared in each block.json and loaded by WordPress
 * only on pages that contain the block.
 *
 * @package WPGutenberg
 */

namespace WPGutenberg\Core;

use WPGutenberg\Fragments\Rest;

/**
 * Front-end enqueue.
 */
final class Assets {

	public const HANDLE = 'wp-gutenberg';

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
		add_filter( 'language_attributes', array( $this, 'html_classes' ) );
	}

	/**
	 * Enqueue global front-end assets.
	 *
	 * @return void
	 */
	public function enqueue(): void {
		$css = Vite::asset( 'src/stylesheets/styles.css' );
		if ( $css ) {
			wp_enqueue_style( self::HANDLE, $css, array(), null );
		}

		$client = Vite::client();
		if ( $client ) {
			wp_enqueue_script_module( 'vite-client', $client, array(), null );
		}

		$js = Vite::asset( 'src/scripts/app.js' );
		if ( $js ) {
			wp_enqueue_script_module( self::HANDLE, $js, array(), null );
		}

		// Tiny inline bootstrap: js/touch classes + config shared with JS.
		wp_register_script( self::HANDLE . '-config', false, array(), null, array( 'in_footer' => false ) );
		wp_enqueue_script( self::HANDLE . '-config' );
		wp_add_inline_script(
			self::HANDLE . '-config',
			sprintf(
				'(function(d,w){var c=d.documentElement.classList;c.replace("no-js","js");if("ontouchstart"in w||navigator.maxTouchPoints>0){c.replace("no-touch","touch")}})(document,window);window.wpg=%s;window.WPG_FRAGMENTS=%s;',
				wp_json_encode(
					array(
						'homeUrl'    => home_url( '/' ),
						'apiUrl'     => rest_url(),
						'textDomain' => WPG_TEXT_DOMAIN,
						'debug'      => defined( 'WP_DEBUG' ) && WP_DEBUG,
					)
				),
				wp_json_encode(
					array(
						'endpoint' => Rest::get_endpoint_url(),
						'nonce'    => wp_create_nonce( 'wp_rest' ),
					)
				)
			)
		);
	}

	/**
	 * Add default classes on <html> (swapped by the inline script above).
	 *
	 * @param string $output language_attributes() output.
	 * @return string
	 */
	public function html_classes( string $output ): string {
		if ( is_admin() ) {
			return $output;
		}

		return $output . ' class="' . esc_attr( wpg_html_class( 'no-js no-touch antialiased' ) ) . '"';
	}
}
