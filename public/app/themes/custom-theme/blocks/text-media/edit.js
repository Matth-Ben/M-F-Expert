/**
 * Texte + image (C1 « Bienvenue ») — editor view. Keep markup & classes identical to render.php.
 * The photo comes first on mobile; `mediaPosition` sets its side from lg.
 * Eyebrow and title are optional attributes (empty = not rendered), so older blocks that
 * only used inner blocks still render the same content.
 */
import { __ } from '@wordpress/i18n';
import { BlockControls, InspectorControls, RichText, useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import { ToolbarButton, ToolbarGroup } from '@wordpress/components';
import { MediaCanvasPlaceholder, MediaPanel, mediaUrl, useMedia } from '../_shared/media';

// The button gets the arrow of the mock-up (`has-arrow`, components/button.css).
const TEMPLATE = [
	['core/paragraph', { placeholder: __('Text…', 'wp-gutenberg') }],
	['core/paragraph', { className: 'wpg-signature', placeholder: __('Handwritten signature…', 'wp-gutenberg') }],
	['core/buttons', {}, [['core/button', { className: 'has-arrow' }]]],
];

const layout = (isRight) => ({
	grid: `container grid items-center gap-10 lg:gap-20 ${isRight ? 'lg:grid-cols-[minmax(0,1.05fr)_minmax(0,0.95fr)]' : 'lg:grid-cols-[minmax(0,0.95fr)_minmax(0,1.05fr)]'}`,
	media: isRight ? 'photo-frame photo-frame--text lg:order-last' : 'photo-frame photo-frame--text',
});

export default function Edit({ attributes, setAttributes }) {
	const { mediaId, mediaPosition, eyebrow, title } = attributes;
	const media = useMedia(mediaId);
	const classes = layout(mediaPosition === 'right');

	const blockProps = useBlockProps({ className: 'wpg-text-media pb-section-lg' });
	const innerBlocksProps = useInnerBlocksProps({ className: 'wpg-text-media__content flex flex-col gap-3.5 lg:gap-4.5' }, { template: TEMPLATE });

	return (
		<>
			<BlockControls>
				<ToolbarGroup>
					<ToolbarButton
						icon="image-flip-horizontal"
						label={__('Flip image side', 'wp-gutenberg')}
						onClick={() => setAttributes({ mediaPosition: mediaPosition === 'left' ? 'right' : 'left' })}
					/>
				</ToolbarGroup>
			</BlockControls>

			<InspectorControls>
				<MediaPanel id={mediaId} onChange={(id) => setAttributes({ mediaId: id })} />
			</InspectorControls>

			<section {...blockProps}>
				<div className={classes.grid}>
					<div className={classes.media}>
						{media ? (
							<img src={mediaUrl(media)} alt={media.alt_text ?? ''} />
						) : (
							<div className="photo-frame__placeholder">
								<MediaCanvasPlaceholder onSelect={(id) => setAttributes({ mediaId: id })} />
							</div>
						)}
					</div>
					<div className="flex flex-col gap-3.5 lg:gap-4.5">
						<RichText
							tagName="p"
							className="eyebrow"
							value={eyebrow}
							onChange={(value) => setAttributes({ eyebrow: value })}
							placeholder={__('Eyebrow (optional)…', 'wp-gutenberg')}
							allowedFormats={[]}
						/>
						<RichText
							tagName="h2"
							className="font-serif text-[3.125rem] leading-[1.02] font-normal tracking-[-0.02em] text-bordeaux lg:text-[4.5rem]"
							value={title}
							onChange={(value) => setAttributes({ title: value })}
							placeholder={__('Title (optional)…', 'wp-gutenberg')}
							allowedFormats={['core/italic']}
						/>
						<div {...innerBlocksProps} />
					</div>
				</div>
			</section>
		</>
	);
}
