/**
 * Argument inclus — editor view. Same markup & classes as render.php.
 */
import { __ } from '@wordpress/i18n';
import { InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import Icon from '../_shared/icon';
import { IconControl } from '../_shared/controls';
import metadata from './block.json';

export default function Edit({ attributes, setAttributes }) {
	const { icon, title, text } = attributes;
	const blockProps = useBlockProps({ className: 'flex flex-col gap-2.5 rounded-card bg-cream p-5.5 text-ink lg:p-7' });

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Card', 'wp-gutenberg')}>
					<IconControl value={icon} icons={metadata.attributes.icon.enum} onChange={(value) => setAttributes({ icon: value })} />
				</PanelBody>
			</InspectorControls>

			<li {...blockProps}>
				<span className="icon-dot size-14 bg-olive-dark text-cream">
					<Icon name={icon} className="size-7" />
				</span>
				<RichText
					tagName="h3"
					className="mt-1 card-title"
					value={title}
					onChange={(value) => setAttributes({ title: value })}
					placeholder={__('Card title…', 'wp-gutenberg')}
					allowedFormats={[]}
				/>
				<RichText
					tagName="p"
					className="text-note"
					value={text}
					onChange={(value) => setAttributes({ text: value })}
					placeholder={__('Short text…', 'wp-gutenberg')}
					allowedFormats={['core/bold', 'core/italic', 'core/link']}
				/>
			</li>
		</>
	);
}
