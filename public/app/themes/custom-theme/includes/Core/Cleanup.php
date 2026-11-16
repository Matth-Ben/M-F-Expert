<?php
/**
 * Removes front-end bloat WordPress outputs by default.
 *
 * @package WPGutenberg
 */

namespace WPGutenberg\Core;

/**
 * Head / assets cleanup.
 */
final class Cleanup {

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register(): void {
		// Emojis.
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );

		// Useless head links.
		remove_action( 'wp_head', 'wp_generator' );
		remove_action( 'wp_head', 'wlwmanifest_link' );
		remove_action( 'wp_head', 'rsd_link' );
		remove_action( 'wp_head', 'wp_shortlink_wp_head' );

		// Only load the CSS of blocks actually present on the page (core + custom).
		add_filter( 'should_load_separate_core_block_assets', '__return_true' );

		// Classic-theme compat stylesheet is useless with theme.json.
		add_action(
			'wp_enqueue_scripts',
			static function (): void {
				wp_dequeue_style( 'classic-theme-styles' );
			},
			20
		);
	}
}
