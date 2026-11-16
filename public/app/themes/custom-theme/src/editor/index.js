/**
 * Editor-only tweaks (built by wp-scripts → build/_editor/index.js).
 *
 * Use this file for project-wide editor rules that don't belong to a single block:
 * block styles, variations, unregistering core options…
 */
import { registerBlockStyle, unregisterBlockStyle, unregisterBlockVariation } from '@wordpress/blocks';
import domReady from '@wordpress/dom-ready';
import { __ } from '@wordpress/i18n';
import { keepHeroFirst } from './hero-first';

keepHeroFirst();

domReady(() => {
	// Core styles that don't exist in the design system.
	unregisterBlockStyle('core/button', 'outline');
	unregisterBlockStyle('core/image', 'rounded');
	unregisterBlockStyle('core/separator', ['wide', 'dots']);
	unregisterBlockStyle('core/quote', 'plain');

	// Embeds we don't want to offer.
	['wordpress', 'tumblr', 'reddit', 'flickr', 'animoto', 'cloudup', 'crowdsignal', 'dailymotion', 'imgur', 'issuu', 'kickstarter', 'mixcloud', 'pocket-casts', 'reverbnation', 'screencast', 'scribd', 'smugmug', 'speaker-deck', 'tiktok', 'ted', 'videopress', 'wolfram-cloud', 'amazon-kindle', 'pinterest', 'wordpress-tv', 'bluesky'].forEach(
		(variation) => unregisterBlockVariation('core/embed', variation)
	);

	// Project styles: the class `is-style-<name>` is added on the block, style it in Tailwind/CSS.
	registerBlockStyle('core/button', { name: 'arrow', label: __('Arrow', 'wp-gutenberg') });
});
