/**
 * Notre différence (C2) — editor view. Keep markup & classes identical to render.php.
 *
 * Inner blocks = optional core/buttons + one core/list (.check-list--cards). Their wrapper is
 * `display: contents` so both become grid items: the buttons stay in the left column, the list
 * takes the right column on desktop (style.css).
 */
import { __ } from '@wordpress/i18n';
import { RichText, useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

const TEMPLATE = [
	['core/buttons', {}, [['core/button', { text: 'Qui sommes-nous ?', className: 'is-style-light has-arrow' }]]],
	[
		'core/list',
		{ className: 'check-list check-list--cards' },
		[
			['core/list-item', { content: 'Une expertise reconnue' }],
			['core/list-item', { content: 'Une prise en charge personnalisée' }],
			['core/list-item', { content: 'Un accompagnement complet' }],
			['core/list-item', { content: 'Une pédagogie claire pour les familles' }],
		],
	],
];

export default function Edit({ attributes, setAttributes }) {
	const blockProps = useBlockProps({ className: 'wpg-difference bg-bordeaux py-section-md text-cream' });
	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'contents' },
		{ allowedBlocks: ['core/buttons', 'core/list'], template: TEMPLATE, renderAppender: false }
	);

	return (
		<section {...blockProps}>
			<div className="wpg-difference__grid container">
				<RichText
					tagName="h2"
					className="wpg-difference__title h2 !text-cream"
					value={attributes.title}
					onChange={(title) => setAttributes({ title })}
					placeholder={__('Title…', 'wp-gutenberg')}
					allowedFormats={[]}
				/>
				<RichText
					tagName="p"
					className="wpg-difference__box rounded-box border border-cream/35 px-6 py-5 text-base [&_strong]:text-gold-pale"
					value={attributes.text}
					onChange={(text) => setAttributes({ text })}
					placeholder={__('Text (bold = key word in gold)…', 'wp-gutenberg')}
					allowedFormats={['core/bold', 'core/italic', 'core/link']}
				/>
				<div {...innerBlocksProps} />
			</div>
		</section>
	);
}
