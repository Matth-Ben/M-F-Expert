<?php
/**
 * Block editor configuration.
 *
 * - loads the real front-end CSS inside the editor canvas (true WYSIWYG)
 * - restricts the inserter to the blocks listed in config/editor.php
 * - removes distractions (remote patterns, Openverse, block directory)
 *
 * @package WPGutenberg
 */

namespace WPGutenberg\Editor;

use WPGutenberg\Core\Vite;
use WP_Block_Editor_Context;

/**
 * Editor setup.
 */
final class Editor {

	/**
	 * Project config (config/editor.php).
	 *
	 * @var array<string, mixed>|null
	 */
	private ?array $config = null;

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'enqueue_block_assets', array( $this, 'enqueue_canvas_assets' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_script' ) );
		add_filter( 'allowed_block_types_all', array( $this, 'allowed_blocks' ), 10, 2 );
		add_filter( 'block_editor_settings_all', array( $this, 'editor_settings' ) );
		add_filter( 'should_load_remote_block_patterns', '__return_false' );
		remove_action( 'enqueue_block_editor_assets', 'wp_enqueue_editor_block_directory_assets' );
	}

	/**
	 * Inject the site stylesheet into the editor iframe.
	 *
	 * `enqueue_block_assets` fires on the front too, so we bail there:
	 * the front already gets the same file from Core\Assets.
	 *
	 * @return void
	 */
	public function enqueue_canvas_assets(): void {
		if ( ! is_admin() ) {
			return;
		}

		$css = Vite::asset( 'src/stylesheets/styles.css' );
		if ( $css ) {
			wp_enqueue_style( 'wp-gutenberg-canvas', $css, array(), null );
		}

		$editor_css = Vite::asset( 'src/editor/editor.css' );
		if ( $editor_css ) {
			wp_enqueue_style( 'wp-gutenberg-canvas-editor', $editor_css, array( 'wp-gutenberg-canvas' ), null );
		}
	}

	/**
	 * Editor-only JS (block styles/variations filters), built by wp-scripts.
	 *
	 * @return void
	 */
	public function enqueue_editor_script(): void {
		$asset_file = WPG_DIR . '/build/_editor/index.asset.php';

		if ( ! is_file( $asset_file ) ) {
			return;
		}

		$asset = require $asset_file;

		wp_enqueue_script(
			'wp-gutenberg-editor',
			WPG_URI . '/build/_editor/index.js',
			$asset['dependencies'],
			$asset['version'],
			array( 'in_footer' => true )
		);
		wp_set_script_translations( 'wp-gutenberg-editor', WPG_TEXT_DOMAIN );
	}

	/**
	 * Whitelist: allowed core blocks + every `wpg/*` block + extra plugin blocks.
	 *
	 * @param bool|string[]           $allowed Current value.
	 * @param WP_Block_Editor_Context $context Editor context.
	 * @return bool|string[]
	 */
	public function allowed_blocks( $allowed, WP_Block_Editor_Context $context ) {
		if ( empty( $context->post ) ) {
			return $allowed; // Widgets / site editor: leave untouched.
		}

		$config = $this->config();
		$custom = array_filter(
			array_keys( \WP_Block_Type_Registry::get_instance()->get_all_registered() ),
			static fn( string $name ): bool => str_starts_with( $name, Blocks::CATEGORY . '/' )
		);

		return array_values(
			array_unique( array_merge( $config['core_blocks'], $custom, $config['plugin_blocks'] ) )
		);
	}

	/**
	 * Editor UI settings.
	 *
	 * @param array<string, mixed> $settings Editor settings.
	 * @return array<string, mixed>
	 */
	public function editor_settings( array $settings ): array {
		$settings['enableOpenverseMediaCategory'] = false;
		$settings['codeEditingEnabled']           = current_user_can( 'manage_options' );

		return $settings;
	}

	/**
	 * Lazy-load config/editor.php.
	 *
	 * @return array<string, mixed>
	 */
	private function config(): array {
		return $this->config ??= require WPG_DIR . '/config/editor.php';
	}
}
