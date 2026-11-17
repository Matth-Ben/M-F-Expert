/**
 * Editor-only tweaks (built by wp-scripts → build/_editor/index.js).
 *
 * Use this file for project-wide editor rules that don't belong to a single block:
 * block styles, variations, unregistering core options…
 */
import { registerBlockStyle, unregisterBlockStyle, unregisterBlockVariation } from '@wordpress/blocks';
import domReady from '@wordpress/dom-ready';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';
import { createHigherOrderComponent } from '@wordpress/compose';
import { addFilter } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';
import { keepHeroFirst } from './hero-first';

keepHeroFirst();

/**
 * Button icons in the editor (same as the front, see components/button.css): `data-icon` on the Button block
 * wrapper when its link is the `#appeler` / `#chat` token or a tel: link.
 */
const BUTTON_ICONS = { '#appeler': 'phone', '#chat': 'chat' };

/**
 * Button option « Flèche après le texte » (sidebar): toggles the `has-arrow` class (components/button.css),
 * so nobody has to type a CSS class. Stored in the block's className: front and editor render the same.
 */
const ARROW_CLASS = 'has-arrow';

addFilter(
	'editor.BlockEdit',
	'wpg/button-arrow',
	createHigherOrderComponent(
		(BlockEdit) => (props) => {
			if (props.name !== 'core/button') {
				return <BlockEdit {...props} />;
			}

			const classes = (props.attributes.className ?? '').split(/\s+/).filter(Boolean);
			const toggleArrow = (on) => {
				const next = classes.filter((name) => name !== ARROW_CLASS).concat(on ? [ARROW_CLASS] : []);
				props.setAttributes({ className: next.length ? next.join(' ') : undefined });
			};

			return (
				<>
					<BlockEdit {...props} />
					<InspectorControls>
						<PanelBody title={__('Arrow', 'wp-gutenberg')}>
							<ToggleControl
								__nextHasNoMarginBottom
								label={__('Show an arrow after the text', 'wp-gutenberg')}
								help={__('For buttons leading to another page.', 'wp-gutenberg')}
								checked={classes.includes(ARROW_CLASS)}
								onChange={toggleArrow}
							/>
						</PanelBody>
					</InspectorControls>
				</>
			);
		},
		'withArrowOption'
	)
);

addFilter(
	'editor.BlockListBlock',
	'wpg/button-icon',
	createHigherOrderComponent(
		(BlockListBlock) => (props) => {
			const url = props.name === 'core/button' ? props.attributes.url ?? '' : '';
			const icon = BUTTON_ICONS[url] ?? (url.startsWith('tel:') ? 'phone' : null);

			return icon ? <BlockListBlock {...props} wrapperProps={{ ...props.wrapperProps, 'data-icon': icon }} /> : <BlockListBlock {...props} />;
		},
		'withButtonIcon'
	)
);

domReady(() => {
	// Core styles that don't exist in the design system.
	unregisterBlockStyle('core/image', 'rounded');
	unregisterBlockStyle('core/separator', ['wide', 'dots']);
	unregisterBlockStyle('core/quote', 'plain');

	// Embeds we don't want to offer.
	['wordpress', 'tumblr', 'reddit', 'flickr', 'animoto', 'cloudup', 'crowdsignal', 'dailymotion', 'imgur', 'issuu', 'kickstarter', 'mixcloud', 'pocket-casts', 'reverbnation', 'screencast', 'scribd', 'smugmug', 'speaker-deck', 'tiktok', 'ted', 'videopress', 'wolfram-cloud', 'amazon-kindle', 'pinterest', 'wordpress-tv', 'bluesky'].forEach(
		(variation) => unregisterBlockVariation('core/embed', variation)
	);

	// Project styles: the class `is-style-<name>` is added on the block, style it in Tailwind/CSS.
	// Button looks of DS v2 §04 (styles in src/stylesheets/components/button.css): fill (default), outline, light.
	registerBlockStyle('core/button', { name: 'light', label: __('Light (on bordeaux)', 'wp-gutenberg') });
});
