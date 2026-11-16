/**
 * Hero — editor view. Same markup & classes as render.php (keep them in sync),
 * minus front-only behaviours (parallax, text split).
 */
import { __ } from '@wordpress/i18n';
import { InspectorControls, RichText, useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { store as editorStore } from '@wordpress/editor';
import { MediaPanel, mediaUrl, useMedia } from '../_shared/media';

const TEMPLATE = [['core/buttons', {}, [['core/button', { text: __('Learn more', 'wp-gutenberg') }]]]];

export default function Edit({ attributes, setAttributes }) {
	const { title, text, mediaId, parallax, animateTitle } = attributes;
	const media = useMedia(mediaId);
	// Empty title = page title on the front (render.php): show it as the placeholder.
	const postTitle = useSelect((select) => select(editorStore)?.getEditedPostAttribute('title'), []);

	const blockProps = useBlockProps({
		className: 'wpg-hero relative flex items-end overflow-hidden bg-black text-white',
	});
	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'mt-8' },
		{ allowedBlocks: ['core/buttons'], template: TEMPLATE }
	);

	return (
		<>
			<InspectorControls>
				<MediaPanel id={mediaId} onChange={(id) => setAttributes({ mediaId: id })} />
				<PanelBody title={__('Animations', 'wp-gutenberg')}>
					<ToggleControl
						__nextHasNoMarginBottom
						label={__('Image parallax', 'wp-gutenberg')}
						checked={parallax}
						onChange={(value) => setAttributes({ parallax: value })}
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						label={__('Animated title', 'wp-gutenberg')}
						checked={animateTitle}
						onChange={(value) => setAttributes({ animateTitle: value })}
					/>
				</PanelBody>
			</InspectorControls>

			<section {...blockProps}>
				{media && (
					<div className="absolute inset-0">
						<img className="size-full object-cover" src={mediaUrl(media, 'full')} alt="" />
						<div className="absolute inset-0 bg-black/40" />
					</div>
				)}

				<div className="container relative py-section-md">
					<RichText
						tagName="h1"
						className="h1 max-w-4xl"
						value={title}
						onChange={(value) => setAttributes({ title: value })}
						placeholder={postTitle || __('Hero title…', 'wp-gutenberg')}
						allowedFormats={['core/bold', 'core/italic']}
					/>
					<RichText
						tagName="p"
						className="mt-6 max-w-2xl text-lg"
						value={text}
						onChange={(value) => setAttributes({ text: value })}
						placeholder={__('Introduction text…', 'wp-gutenberg')}
						allowedFormats={['core/bold', 'core/italic', 'core/link']}
					/>
					<div {...innerBlocksProps} />
				</div>
			</section>
		</>
	);
}
