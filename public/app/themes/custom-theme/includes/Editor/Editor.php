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
		add_filter( 'register_post_type_args', array( $this, 'post_type_template' ), 10, 2 );
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

		// Editor data shared with block edit.js (blocks/_shared/*): sprite URL for <Icon>, theme logo.
		wp_add_inline_script(
			'wp-gutenberg-editor',
			'window.wpgEditor=' . wp_json_encode(
				array(
					'sprite'  => WPG_URI . '/public/sprite.svg',
					'logo'    => wpg_logo_url( 'lg' ),
					'contact' => wpg_contact(),
				)
			) . ';',
			'before'
		);
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
		// Locks set by the theme (e.g. the hero) can't be removed from the editor UI.
		$settings['canLockBlocks']                = false;

		return $settings;
	}

	/**
	 * Default blocks of new posts (config/editor.php > post_type_templates).
	 *
	 * Not locked as a whole (template_lock false): blocks can be added after,
	 * the hero itself is locked through its `lock` attribute default.
	 *
	 * @param array<string, mixed> $args      Post type args.
	 * @param string               $post_type Post type name.
	 * @return array<string, mixed>
	 */
	public function post_type_template( array $args, string $post_type ): array {
		$templates = $this->config()['post_type_templates'] ?? array();

		if ( isset( $templates[ $post_type ] ) ) {
			$args['template']      = $templates[ $post_type ];
			$args['template_lock'] = false;
		}

		return $args;
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
