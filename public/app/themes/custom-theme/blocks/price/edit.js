/**
 * Tarif — editor view. Same markup & classes as render.php.
 */
import { __ } from '@wordpress/i18n';
import { InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import Icon from '../_shared/icon';
import { IconControl } from '../_shared/controls';
import { CLASSES } from './classes';
import metadata from './block.json';

export default function Edit({ attributes, setAttributes, context }) {
	const { icon, price, name, description } = attributes;
	const classes = CLASSES[context['wpg/pricesLayout']] ?? CLASSES.cards;
	const Heading = context['wpg/pricesHeader'] === false ? 'h2' : 'h3';
	const blockProps = useBlockProps({ className: classes.item });

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Price', 'wp-gutenberg')}>
					<IconControl value={icon} icons={metadata.attributes.icon.enum} onChange={(value) => setAttributes({ icon: value })} />
				</PanelBody>
			</InspectorControls>

			<li {...blockProps}>
				<span className={classes.icon}>
					<Icon name={icon} className="size-6.5" />
				</span>
				<div className={classes.text}>
					<RichText
						tagName={Heading}
						className={classes.name}
						value={name}
						onChange={(value) => setAttributes({ name: value })}
						placeholder={__('Service name…', 'wp-gutenberg')}
						allowedFormats={[]}
					/>
					<RichText
						tagName="p"
						className={classes.description}
						value={description}
						onChange={(value) => setAttributes({ description: value })}
						placeholder={__('Short description…', 'wp-gutenberg')}
						allowedFormats={['core/bold', 'core/italic']}
					/>
				</div>
				<RichText
					tagName="p"
					className={classes.price}
					value={price}
					onChange={(value) => setAttributes({ price: value })}
					placeholder={__('Price…', 'wp-gutenberg')}
					allowedFormats={[]}
				/>
			</li>
		</>
	);
}
