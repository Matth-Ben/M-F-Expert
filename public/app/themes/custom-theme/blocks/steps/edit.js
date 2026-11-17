/**
 * Méthode en étapes — editor view. Same markup & classes as render.php.
 */
import { __ } from '@wordpress/i18n';
import { RichText, useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

const TEMPLATE = [
	['wpg/step', { title: 'Diagnostiquer', text: 'Nous examinons le cuir chevelu pour confirmer la présence de poux et de lentes.' }],
	['wpg/step', { title: 'Traiter', text: 'Une méthode manuelle et mécanique, sans produits chimiques.' }],
	['wpg/step', { title: 'Contrôler', text: 'Un contrôle pour s’assurer d’un résultat durable.' }],
	['wpg/step', { title: 'Accompagner', text: 'Des explications détaillées et un protocole à suivre à la maison.' }],
];

export default function Edit({ attributes, setAttributes }) {
	const { title, intro } = attributes;
	const blockProps = useBlockProps({ className: 'wpg-steps bg-cream py-section-md' });
	const innerBlocksProps = useInnerBlocksProps(
		{
			className: 'wpg-steps__list m-0 flex list-none flex-col gap-7 p-0 lg:grid lg:auto-cols-fr lg:grid-flow-col lg:gap-6',
			role: 'list',
		},
		{ allowedBlocks: ['wpg/step'], template: TEMPLATE, orientation: 'horizontal' }
	);

	return (
		<section {...blockProps}>
			<div className="container flex flex-col gap-8 lg:gap-13">
				<div className="flex flex-col gap-2.5 lg:items-center lg:text-center">
					<RichText
						tagName="h2"
						className="h2"
						value={title}
						onChange={(value) => setAttributes({ title: value })}
						placeholder={__('Title…', 'wp-gutenberg')}
						allowedFormats={['core/italic']}
					/>
					<RichText
						tagName="p"
						className="max-w-[35rem]"
						value={intro}
						onChange={(value) => setAttributes({ intro: value })}
						placeholder={__('Introduction text…', 'wp-gutenberg')}
						allowedFormats={['core/bold', 'core/italic', 'core/link']}
					/>
				</div>

				<ol {...innerBlocksProps} />
			</div>
		</section>
	);
}
