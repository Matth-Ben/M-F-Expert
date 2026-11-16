<?php
/**
 * Fragment Loader
 *
 * Handles loading and rendering of PHP template-part fragments with whitelisting and security.
 *
 * @package WPGutenberg
 * 
 */

namespace WPGutenberg\Fragments;

use WP_Error;

/**
 * FragmentLoader
 *
 * Responsible for:
 * - Whitelisting fragment types and keys
 * - Sanitizing props
 * - Rendering template parts (parts/fragments/*)
 * - Caching rendered fragments
 *
 * @package WPGutenberg\Fragments
 */
class FragmentLoader
{

	/**
	 * Whitelist of allowed fragments
	 * Maps type => key => template part slug (relative to the theme root, without .php)
	 *
	 * @var array<string, array<string, string>>
	 */
	private const FRAGMENTS_WHITELIST = array(
		'panel' => array(
			'menu'    => 'parts/fragments/panels/menu',
			'filters' => 'parts/fragments/panels/filters',
		),
	);

	/**
	 * Cache duration for transients (in seconds)
	 * Set to 0 to disable caching
	 *
	 * @var int
	 */
	private const CACHE_DURATION = 0; // Disabled by default for dynamic content

	/**
	 * Allowed props keys with their sanitization callbacks
	 *
	 * @var array<string, callable>
	 */
	private const ALLOWED_PROPS = array(
		'title' => 'sanitize_text_field',
		'message' => 'wp_kses_post',
		'items' => array( self::class, 'sanitize_items' ),
		'type' => 'sanitize_text_field',
		'link' => 'esc_url_raw',
	);

	/**
	 * Load and render a fragment
	 *
	 * @param string $type Fragment type (panel, alert, etc.)
	 * @param string $key Fragment key (menu, filters, etc.)
	 * @param array  $props Optional props to pass to the template.
	 * @return array{ok: bool, html: string, key: string, type: string}|WP_Error
	 */
	public function load(string $type, string $key, array $props = array())
	{
		// Validate type and key
		if (!isset(self::FRAGMENTS_WHITELIST[$type])) {
			return new WP_Error(
				'invalid_fragment_type',
				sprintf('Invalid fragment type: %s', $type),
				array('status' => 400)
			);
		}

		if (!isset(self::FRAGMENTS_WHITELIST[$type][$key])) {
			return new WP_Error(
				'invalid_fragment_key',
				sprintf('Invalid fragment key: %s for type: %s', $key, $type),
				array('status' => 404)
			);
		}

		// Get template path
		$template = self::FRAGMENTS_WHITELIST[$type][$key];

		// Check cache
		$cache_key = $this->get_cache_key($type, $key, $props);
		if (self::CACHE_DURATION > 0) {
			$cached = get_transient($cache_key);
			if (false !== $cached) {
				return $cached;
			}
		}

		// Sanitize props
		$sanitized_props = $this->sanitize_props($props);

		// Build context
		$context = $sanitized_props;

		// Add fragment metadata
		$context['fragment_type'] = $type;
		$context['fragment_key'] = $key;

		// Auto-load primary menu for menu fragment if not provided
		if ('panel' === $type && 'menu' === $key && !isset($sanitized_props['menu_items'])) {
			$menu_items = $this->get_primary_menu_items();
			if (!empty($menu_items)) {
				$context['menu_items'] = $menu_items;
			}
		}

		// Render template
		try {
			$html = wpg_render($template, $context);
		} catch (\Exception $e) {
			return new WP_Error(
				'template_error',
				sprintf('Error rendering template: %s', $e->getMessage()),
				array('status' => 500)
			);
		}

		// Build response
		$response = array(
			'ok' => true,
			'html' => $html,
			'key' => $key,
			'type' => $type,
		);

		// Cache response
		if (self::CACHE_DURATION > 0) {
			set_transient($cache_key, $response, self::CACHE_DURATION);
		}

		return $response;
	}

	/**
	 * Sanitize props according to whitelist
	 *
	 * @param array $props Raw props from request.
	 * @return array Sanitized props
	 */
	private function sanitize_props(array $props): array
	{
		$sanitized = array();

		foreach ($props as $key => $value) {
			if (!isset(self::ALLOWED_PROPS[$key])) {
				continue; // Skip non-whitelisted props
			}

			$callback = self::ALLOWED_PROPS[$key];

			if (is_callable($callback)) {
				$sanitized[$key] = call_user_func($callback, $value);
			}
		}

		return $sanitized;
	}

	/**
	 * Sanitize array of items
	 *
	 * @param mixed $items Items to sanitize.
	 * @return array
	 */
	private static function sanitize_items($items): array
	{
		if (!is_array($items)) {
			return array();
		}

		return array_map(
			function ($item) {
				if (is_array($item)) {
					return array_map('sanitize_text_field', $item);
				}
				return sanitize_text_field($item);
			},
			$items
		);
	}

	/**
	 * Generate cache key
	 *
	 * @param string $type Fragment type.
	 * @param string $key Fragment key.
	 * @param array  $props Props.
	 * @return string
	 */
	private function get_cache_key(string $type, string $key, array $props): string
	{
		$props_hash = md5(wp_json_encode($props));
		return sprintf('wpg_fragment_%s_%s_%s', $type, $key, $props_hash);
	}

	/**
	 * Clear fragment cache
	 *
	 * @param string|null $type Optional type to clear specific fragments.
	 * @param string|null $key Optional key to clear specific fragment.
	 * @return void
	 */
	public function clear_cache(?string $type = null, ?string $key = null): void
	{
		global $wpdb;

		if (null !== $type && null !== $key) {
			// Clear specific fragment
			$pattern = sprintf('_transient_wpg_fragment_%s_%s_%%', $type, $key);
		} elseif (null !== $type) {
			// Clear all fragments of a type
			$pattern = sprintf('_transient_wpg_fragment_%s_%%', $type);
		} else {
			// Clear all fragments
			$pattern = '_transient_wpg_fragment_%';
		}

		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
				$pattern
			)
		);
	}

	/**
	 * Get primary menu items
	 *
	 * @return array
	 */
	private function get_primary_menu_items(): array
	{
		$menu_items = array();

		// Try to get the primary menu
		$locations = get_nav_menu_locations();
		$menu_id = $locations['main'] ?? null;

		if (!$menu_id) {
			return $menu_items;
		}

		$items = wp_get_nav_menu_items($menu_id);

		if (!$items) {
			return $menu_items;
		}

		foreach ($items as $item) {
			$menu_items[] = array(
				'url' => $item->url,
				'title' => $item->title,
			);
		}

		return $menu_items;
	}
}
