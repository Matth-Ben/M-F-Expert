/**
 * Encart d'information — editor view. Same markup & classes as render.php.
 */
import { __ } from '@wordpress/i18n';
import { InspectorControls, RichText, useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import Icon from '../_shared/icon';
import { IconControl } from '../_shared/controls';
import metadata from './block.json';

const TEMPLATE = [['core/paragraph', { content: '[Modalités de réservation et moyens de paiement à compléter avec M&amp;F Expert.]' }]];

export default function Edit({ attributes, setAttributes }) {
	const { icon, title } = attributes;
	const blockProps = useBlockProps({ className: 'wpg-info-box pb-5 lg:pb-7' });
	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'mt-1 flex flex-col gap-2 text-note' },
		{ allowedBlocks: metadata.allowedBlocks, template: TEMPLATE }
	);

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Box', 'wp-gutenberg')}>
					<IconControl value={icon} icons={metadata.attributes.icon.enum} onChange={(value) => setAttributes({ icon: value })} />
				</PanelBody>
			</InspectorControls>

			<section {...blockProps}>
				<div className="container">
					<div className="flex items-start gap-4 rounded-card bg-cream p-5.5 lg:p-7.5">
						<span className="icon-dot size-12 bg-bordeaux text-cream">
							<Icon name={icon} className="size-6" />
						</span>
						<div className="min-w-0 flex-1">
							<RichText
								tagName="h2"
								className="card-title leading-[1.7]"
								value={title}
								onChange={(value) => setAttributes({ title: value })}
								placeholder={__('Title…', 'wp-gutenberg')}
								allowedFormats={[]}
							/>
							<div {...innerBlocksProps} />
						</div>
					</div>
				</div>
			</section>
		</>
	);
}
