/**
 * Prestations — editor view. Same markup & classes as render.php.
 */
import { __ } from '@wordpress/i18n';
import { InspectorControls, RichText, useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import { PanelBody, SelectControl, ToggleControl } from '@wordpress/components';
import Icon from '../_shared/icon';
import { LinkPanel } from '../_shared/controls';

const TEMPLATE = [
	[
		'wpg/service',
		{ icon: 'search', title: 'Solution anti-poux', linkText: 'Voir les tarifs', linkUrl: '' },
		[
			[
				'core/paragraph',
				{
					content:
						'Une méthode manuelle et mécanique pour le traitement des poux et lentes, sans produits chimiques. Une solution douce et respectueuse du cuir chevelu.',
				},
			],
		],
	],
	[
		'wpg/service',
		{ icon: 'home', title: 'Protéger votre maison', linkText: 'Nous contacter', linkUrl: '' },
		[
			[
				'core/paragraph',
				{
					content:
						'Du matériel de nettoyage pour traiter notre environnement est disponible à la location chez M&amp;F Expert, pour un intérieur sain.',
				},
			],
		],
	],
	[
		'wpg/service-family',
		{
			icon: 'users',
			title: 'Toute la famille',
			text: 'Votre espace anti-poux accueille tous les membres de la famille, enfants et parents.',
		},
	],
];

export const GRID_CLASS = {
	grid: 'grid items-stretch gap-4.5 lg:grid-cols-[minmax(0,1.1fr)_minmax(0,1.1fr)_minmax(0,0.8fr)] lg:gap-6',
	detailed: 'grid gap-4.5 lg:grid-cols-2 lg:gap-6',
};

export default function Edit({ attributes, setAttributes }) {
	const { layout, showHeader, eyebrow, title, linkText, linkUrl } = attributes;
	const blockProps = useBlockProps({ className: 'wpg-services pb-section-lg' });
	const innerBlocksProps = useInnerBlocksProps(
		{ className: GRID_CLASS[layout] ?? GRID_CLASS.grid },
		{ allowedBlocks: ['wpg/service', 'wpg/service-family'], template: TEMPLATE, orientation: 'horizontal' }
	);

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Settings', 'wp-gutenberg')}>
					<SelectControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={__('Layout', 'wp-gutenberg')}
						value={layout}
						options={[
							{ value: 'grid', label: __('Grid of three cards (home page)', 'wp-gutenberg') },
							{ value: 'detailed', label: __('Detailed cards, family card full width', 'wp-gutenberg') },
						]}
						onChange={(value) => setAttributes({ layout: value })}
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						label={__('Show the section header', 'wp-gutenberg')}
						help={__('Without a header, card titles become level 2 headings.', 'wp-gutenberg')}
						checked={showHeader}
						onChange={(value) => setAttributes({ showHeader: value })}
					/>
				</PanelBody>
				{showHeader && (
					<LinkPanel
						title={__('Header link', 'wp-gutenberg')}
						help={__('The link is hidden on the site while no URL is set.', 'wp-gutenberg')}
						url={linkUrl}
						onChange={(url) => setAttributes({ linkUrl: url })}
					/>
				)}
			</InspectorControls>

			<section {...blockProps}>
				<div className="container flex flex-col gap-6 lg:gap-9">
					{showHeader && (
						<div className="flex flex-wrap items-end justify-between gap-x-6 gap-y-4">
							<div className="flex flex-col gap-3">
								<RichText
									tagName="p"
									className="eyebrow"
									value={eyebrow}
									onChange={(value) => setAttributes({ eyebrow: value })}
									placeholder={__('Eyebrow…', 'wp-gutenberg')}
									allowedFormats={[]}
								/>
								<RichText
									tagName="h2"
									className="h2"
									value={title}
									onChange={(value) => setAttributes({ title: value })}
									placeholder={__('Title…', 'wp-gutenberg')}
									allowedFormats={['core/italic']}
								/>
							</div>
							<span className="link-arrow text-md text-bordeaux">
								<RichText
									tagName="span"
									value={linkText}
									onChange={(value) => setAttributes({ linkText: value })}
									placeholder={__('Link text…', 'wp-gutenberg')}
									allowedFormats={[]}
								/>
								<Icon name="arrow" />
							</span>
						</div>
					)}

					<div {...innerBlocksProps} />
				</div>
			</section>
		</>
	);
}
