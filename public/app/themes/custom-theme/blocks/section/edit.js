/**
 * Section — colors & padding use native block supports (block.json `supports`):
 * WordPress builds the UI from theme.json presets and adds classes/styles itself,
 * both in the editor (useBlockProps) and on the front (get_block_wrapper_attributes).
 */
import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';

export const innerClass = (width) => (width === 'narrow' ? 'mx-auto w-full max-w-3xl px-5' : 'container') + ' space-y-6';

export default function Edit({ attributes, setAttributes }) {
	const blockProps = useBlockProps();
	const innerBlocksProps = useInnerBlocksProps(
		{ className: innerClass(attributes.width) },
		{ template: [['core/heading', { level: 2 }], ['core/paragraph']] }
	);

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Layout', 'wp-gutenberg')}>
					<SelectControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={__('Content width', 'wp-gutenberg')}
						value={attributes.width}
						options={[
							{ label: __('Container', 'wp-gutenberg'), value: 'container' },
							{ label: __('Narrow', 'wp-gutenberg'), value: 'narrow' },
						]}
						onChange={(width) => setAttributes({ width })}
					/>
				</PanelBody>
			</InspectorControls>

			<section {...blockProps}>
				<div {...innerBlocksProps} />
			</section>
		</>
	);
}
