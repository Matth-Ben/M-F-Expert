/**
 * Carte de prestation — editor view. Same markup & classes as render.php.
 * Key points: a core/list with the class "check-list check-list--on-dark".
 */
import { __ } from '@wordpress/i18n';
import { InspectorControls, RichText, useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import Icon from '../_shared/icon';
import { IconControl, LinkPanel } from '../_shared/controls';
import metadata from './block.json';

const TEMPLATE = [
	['core/paragraph', { placeholder: __('Short description of the service…', 'wp-gutenberg') }],
	['core/list', { className: 'check-list check-list--on-dark' }, [['core/list-item', { placeholder: __('Key point…', 'wp-gutenberg') }]]],
];

export default function Edit({ attributes, setAttributes, context }) {
	const { icon, title, linkText, linkUrl } = attributes;
	const Heading = context['wpg/servicesHeader'] === false ? 'h2' : 'h3';
	const blockProps = useBlockProps({
		className: 'flex flex-col gap-4 rounded-panel bg-bordeaux px-6.5 py-7.5 text-cream shadow-card lg:px-9 lg:py-10',
	});
	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'flex flex-col gap-4' },
		{ allowedBlocks: metadata.allowedBlocks, template: TEMPLATE }
	);

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Card', 'wp-gutenberg')}>
					<IconControl value={icon} icons={metadata.attributes.icon.enum} onChange={(value) => setAttributes({ icon: value })} />
				</PanelBody>
				<LinkPanel
					title={__('Card link', 'wp-gutenberg')}
					help={__('The link is hidden on the site while no URL is set.', 'wp-gutenberg')}
					url={linkUrl}
					onChange={(url) => setAttributes({ linkUrl: url })}
				/>
			</InspectorControls>

			<article {...blockProps}>
				<div className="icon-dot size-20 bg-olive text-cream">
					<Icon name={icon} className="size-10" />
				</div>
				<RichText
					tagName={Heading}
					className="script text-[2.5rem] leading-[1.05] text-cream lg:text-[2.75rem]"
					value={title}
					onChange={(value) => setAttributes({ title: value })}
					placeholder={__('Service title…', 'wp-gutenberg')}
					allowedFormats={[]}
				/>
				<div {...innerBlocksProps} />
				<span className="link-arrow link-arrow--light mt-auto text-md text-gold-light">
					<RichText
						tagName="span"
						value={linkText}
						onChange={(value) => setAttributes({ linkText: value })}
						placeholder={__('Link text…', 'wp-gutenberg')}
						allowedFormats={[]}
					/>
					<Icon name="arrow" />
				</span>
			</article>
		</>
	);
}
