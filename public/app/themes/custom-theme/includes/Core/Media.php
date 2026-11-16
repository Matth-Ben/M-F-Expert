<?php
/**
 * Image generation settings.
 *
 * @package WPGutenberg
 */

namespace WPGutenberg\Core;

/**
 * Media tweaks.
 */
final class Media {

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register(): void {
		// 82 is WordPress' default; 100 (previous theme) produced very heavy files.
		add_filter( 'wp_editor_set_quality', static fn(): int => 82 );

		// Generate WebP sub-sizes for JPEG/PNG uploads (native srcset then serves WebP).
		add_filter(
			'image_editor_output_format',
			static function ( array $formats ): array {
				$formats['image/jpeg'] = 'image/webp';
				$formats['image/png']  = 'image/webp';
				return $formats;
			}
		);
	}
}
