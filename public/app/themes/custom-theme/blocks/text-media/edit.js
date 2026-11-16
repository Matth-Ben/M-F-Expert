import { __ } from '@wordpress/i18n';
import { BlockControls, InspectorControls, useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import { ToolbarButton, ToolbarGroup } from '@wordpress/components';
import { MediaCanvasPlaceholder, MediaPanel, mediaUrl, useMedia } from '../_shared/media';

const TEMPLATE = [
	['core/heading', { level: 2, placeholder: __('Title', 'wp-gutenberg') }],
	['core/paragraph', { placeholder: __('Text…', 'wp-gutenberg') }],
	['core/buttons'],
];

export default function Edit({ attributes, setAttributes }) {
	const { mediaId, mediaPosition } = attributes;
	const media = useMedia(mediaId);

	const blockProps = useBlockProps({ className: 'py-section-md' });
	const innerBlocksProps = useInnerBlocksProps({ className: 'space-y-6' }, { template: TEMPLATE });

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
				<div className="container grid items-center gap-10 md:grid-cols-2">
					<div className={mediaPosition === 'right' ? 'md:order-last' : undefined}>
						{media ? (
							<img className="aspect-[4/3] w-full object-cover" src={mediaUrl(media)} alt="" />
						) : (
							<MediaCanvasPlaceholder onSelect={(id) => setAttributes({ mediaId: id })} />
						)}
					</div>
					<div {...innerBlocksProps} />
				</div>
			</section>
		</>
	);
}
