/**
 * Appel à l'action (B5) — editor view. Keep markup & classes identical to render.php.
 */
import { __ } from '@wordpress/i18n';
import { RichText, useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

// `#appeler` / `#chat` are resolved on the front from Réglages > Coordonnées (includes/Core/ContactLinks.php).
const TEMPLATE = [
	[
		'core/buttons',
		{},
		[
			['core/button', { text: 'Appelez-nous', url: '#appeler' }],
			['core/button', { text: 'Poser une question', url: '#chat', className: 'is-style-outline' }],
		],
	],
];

export default function Edit({ attributes, setAttributes }) {
	const blockProps = useBlockProps({ className: 'wpg-cta pb-section-lg' });
	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'flex-none' },
		{ allowedBlocks: ['core/buttons'], template: TEMPLATE }
	);

	return (
		<section {...blockProps}>
			<div className="container">
				<div className="flex flex-col gap-5.5 rounded-cta bg-cream px-6 py-8 lg:flex-row lg:items-center lg:justify-between lg:gap-10 lg:px-16 lg:py-13">
					<div className="flex max-w-155 flex-col gap-2.5">
						<RichText
							tagName="h2"
							className="script text-[2.875rem] text-bordeaux lg:text-[3.875rem]"
							value={attributes.title}
							onChange={(title) => setAttributes({ title })}
							placeholder={__('Title…', 'wp-gutenberg')}
							allowedFormats={[]}
						/>
						<RichText
							tagName="p"
							className="text-base font-semibold text-ink-soft lg:text-lg"
							value={attributes.text}
							onChange={(text) => setAttributes({ text })}
							placeholder={__('Text…', 'wp-gutenberg')}
							allowedFormats={['core/bold', 'core/italic']}
						/>
					</div>
					<div {...innerBlocksProps} />
				</div>
			</div>
		</section>
	);
}
