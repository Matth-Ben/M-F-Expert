/**
 * Toute la famille (C3) — editor view. Keep markup & classes identical to render.php.
 */
import { __ } from '@wordpress/i18n';
import { InspectorControls, RichText, useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';
import Badge from '../_shared/badge';
import { MediaCanvasPlaceholder, MediaPanel, mediaUrl, useMedia } from '../_shared/media';

const TEMPLATE = [
	['core/paragraph', { content: 'Votre espace anti-poux accueille tous les membres de la famille, enfants et parents.' }],
	[
		'core/list',
		{ className: 'check-list' },
		[
			['core/list-item', { content: 'Sans produits chimiques' }],
			['core/list-item', { content: 'Douce pour le cuir chevelu' }],
			['core/list-item', { content: 'Enfants et parents' }],
		],
	],
];

export default function Edit({ attributes, setAttributes }) {
	const { mediaId, showBadge, eyebrow, title } = attributes;
	const media = useMedia(mediaId);

	const blockProps = useBlockProps({ className: 'wpg-family bg-cream py-section-lg' });
	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'flex flex-col gap-3.5 lg:gap-4.5 [&_p]:max-w-135 [&_ul]:mt-1.5' },
		{ allowedBlocks: ['core/paragraph', 'core/list'], template: TEMPLATE }
	);

	return (
		<>
			<InspectorControls>
				<MediaPanel id={mediaId} onChange={(id) => setAttributes({ mediaId: id })} />
				<PanelBody title={__('Badge', 'wp-gutenberg')}>
					<ToggleControl
						__nextHasNoMarginBottom
						label={__('Show the « NEW in VIRIAT » badge', 'wp-gutenberg')}
						checked={showBadge}
						onChange={(value) => setAttributes({ showBadge: value })}
					/>
				</PanelBody>
			</InspectorControls>

			<section {...blockProps}>
				<div className="container grid items-center gap-11 lg:grid-cols-[minmax(0,0.9fr)_minmax(0,1.1fr)] lg:gap-22">
					<div className="relative">
						<div className="photo-frame">
							{media ? (
								<img className="object-[36%_40%]" src={mediaUrl(media)} alt={media.alt_text ?? ''} />
							) : (
								<div className="photo-frame__placeholder">
									<MediaCanvasPlaceholder onSelect={(id) => setAttributes({ mediaId: id })} />
								</div>
							)}
						</div>
						{showBadge && <Badge className="absolute -top-6 -right-2 lg:-top-9.5 lg:-right-8.5" />}
					</div>

					<div className="flex flex-col gap-3.5 lg:gap-4.5">
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
							className="script text-[3.25rem] text-bordeaux lg:text-[4.25rem]"
							value={title}
							onChange={(value) => setAttributes({ title: value })}
							placeholder={__('Title…', 'wp-gutenberg')}
							allowedFormats={[]}
						/>
						<div {...innerBlocksProps} />
					</div>
				</div>
			</section>
		</>
	);
}
