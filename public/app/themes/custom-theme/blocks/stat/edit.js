import { __ } from '@wordpress/i18n';
import { InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, TextControl, __experimentalNumberControl as NumberControl } from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
	const { value, prefix, suffix, label } = attributes;
	const blockProps = useBlockProps({ className: 'flex flex-col gap-2' });

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Number', 'wp-gutenberg')}>
					<NumberControl
						__next40pxDefaultSize
						label={__('Value', 'wp-gutenberg')}
						value={value}
						onChange={(next) => setAttributes({ value: Number(next) || 0 })}
					/>
					<TextControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={__('Prefix', 'wp-gutenberg')}
						value={prefix}
						onChange={(next) => setAttributes({ prefix: next })}
					/>
					<TextControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={__('Suffix', 'wp-gutenberg')}
						value={suffix}
						onChange={(next) => setAttributes({ suffix: next })}
					/>
				</PanelBody>
			</InspectorControls>

			<div {...blockProps}>
				<p className="h1">
					{prefix}
					{value.toLocaleString()}
					{suffix}
				</p>
				<RichText
					tagName="p"
					value={label}
					onChange={(next) => setAttributes({ label: next })}
					placeholder={__('Label…', 'wp-gutenberg')}
					allowedFormats={[]}
				/>
			</div>
		</>
	);
}
