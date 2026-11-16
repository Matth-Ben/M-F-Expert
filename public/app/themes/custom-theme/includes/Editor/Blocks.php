<?php
/**
 * Custom blocks auto-registration.
 *
 * Every folder in `blocks/` containing a block.json is compiled by
 * @wordpress/scripts into `build/<block>/` and registered here automatically.
 * Nothing to declare in PHP when you add a block.
 *
 * @package WPGutenberg
 */

namespace WPGutenberg\Editor;

/**
 * Registers blocks from the build folder.
 */
final class Blocks {

	public const CATEGORY = 'wpg';

	private const BUILD_DIR = '/build';

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'init', array( $this, 'register_blocks' ) );
		add_filter( 'block_categories_all', array( $this, 'register_category' ) );
	}

	/**
	 * Register every compiled block.
	 *
	 * Uses the PHP manifest generated at build time (WP 6.8+), which avoids
	 * reading/parsing every block.json on each request.
	 *
	 * @return void
	 */
	public function register_blocks(): void {
		$build    = WPG_DIR . self::BUILD_DIR;
		$manifest = $build . '/blocks-manifest.php';

		if ( is_file( $manifest ) && function_exists( 'wp_register_block_types_from_metadata_collection' ) ) {
			wp_register_block_types_from_metadata_collection( $build, $manifest );
			return;
		}

		foreach ( glob( $build . '/*/block.json' ) ?: array() as $block_json ) {
			register_block_type( dirname( $block_json ) );
		}
	}

	/**
	 * Put our blocks in their own inserter category, first.
	 *
	 * @param array<int, array<string, mixed>> $categories Existing categories.
	 * @return array<int, array<string, mixed>>
	 */
	public function register_category( array $categories ): array {
		array_unshift(
			$categories,
			array(
				'slug'  => self::CATEGORY,
				'title' => __( 'Site blocks', 'wp-gutenberg' ),
				'icon'  => null,
			)
		);

		return $categories;
	}
}
