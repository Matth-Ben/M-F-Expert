<?php
/**
 * Editor configuration — edit per project.
 *
 * Custom `wpg/*` blocks are always allowed automatically.
 *
 * @package WPGutenberg
 */

return array(
	// Core blocks the client may use.
	'core_blocks'   => array(
		'core/paragraph',
		'core/heading',
		'core/list',
		'core/list-item',
		'core/quote',
		'core/image',
		'core/gallery',
		'core/buttons',
		'core/button',
		'core/columns',
		'core/column',
		'core/group',
		'core/separator',
		'core/spacer',
		'core/embed',
		'core/video',
		'core/file',
		'core/table',
		'core/details',
		'core/shortcode',
		'core/block', // Synced patterns.
	),

	// Initial content of new posts, per post type (blocks: [ name, attributes, inner blocks ]).
	// The hero is also kept first on pages by src/editor/hero-first.js.
	'post_type_templates' => array(
		'page' => array(
			array( 'wpg/hero' ),
		),
	),

	// Third-party blocks (SEOPress free: FAQ with schema, HTML sitemap).
	'plugin_blocks' => array(
		'wpseopress/faq-block',
		'wpseopress/sitemap',
	),
);
