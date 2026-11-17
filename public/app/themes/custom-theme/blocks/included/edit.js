/**
 * Inclus dans chaque prestation — editor view. Same markup & classes as render.php.
 */
import { __ } from '@wordpress/i18n';
import { RichText, useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

const TEMPLATE = [
	['wpg/feature', { icon: 'shield', title: 'Sans produits chimiques', text: 'Une méthode manuelle et mécanique, douce pour le cuir chevelu.' }],
	['wpg/feature', { icon: 'users', title: 'Pour toute la famille', text: 'Enfants et parents sont accueillis dans le même espace.' }],
	['wpg/feature', { icon: 'heart', title: 'Un protocole à la maison', text: 'Des explications détaillées pour prolonger le résultat.' }],
];

export default function Edit({ attributes, setAttributes }) {
	const blockProps = useBlockProps({ className: 'wpg-included bg-bordeaux py-section-md text-cream' });
	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'm-0 grid list-none gap-3.5 p-0 md:grid-cols-3 lg:gap-4.5', role: 'list' },
		{ allowedBlocks: ['wpg/feature'], template: TEMPLATE, orientation: 'horizontal' }
	);

	return (
		<section {...blockProps}>
			<div className="container flex flex-col gap-6 lg:gap-9">
				<RichText
					tagName="h2"
					className="h2 !text-cream"
					value={attributes.title}
					onChange={(value) => setAttributes({ title: value })}
					placeholder={__('Title…', 'wp-gutenberg')}
					allowedFormats={['core/italic']}
				/>
				<ul {...innerBlocksProps} />
			</div>
		</section>
	);
}
