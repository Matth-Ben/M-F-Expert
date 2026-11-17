/**
 * Carte « Toute la famille » — editor view. Same markup & classes as render.php.
 */
import { __ } from '@wordpress/i18n';
import { InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';
import Badge from '../_shared/badge';
import Icon from '../_shared/icon';
import { IconControl } from '../_shared/controls';
import { CLASSES } from './classes';
import metadata from './block.json';

export default function Edit({ attributes, setAttributes, context }) {
	const { icon, title, text, showBadge } = attributes;
	const classes = CLASSES[context['wpg/servicesLayout']] ?? CLASSES.grid;
	const Heading = context['wpg/servicesHeader'] === false ? 'h2' : 'h3';
	const blockProps = useBlockProps({ className: classes.card });

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Card', 'wp-gutenberg')}>
					<IconControl value={icon} icons={metadata.attributes.icon.enum} onChange={(value) => setAttributes({ icon: value })} />
					<ToggleControl
						__nextHasNoMarginBottom
						label={__('Show the “NEW in VIRIAT” badge', 'wp-gutenberg')}
						checked={showBadge}
						onChange={(value) => setAttributes({ showBadge: value })}
					/>
				</PanelBody>
			</InspectorControls>

			<article {...blockProps}>
				<div className="icon-dot size-20 bg-olive-dark text-cream">
					<Icon name={icon} className="size-10" />
				</div>
				<div className={classes.body}>
					<RichText
						tagName={Heading}
						className="script text-[2.5rem] leading-[1.05] text-bordeaux lg:text-[2.75rem]"
						value={title}
						onChange={(value) => setAttributes({ title: value })}
						placeholder={__('Card title…', 'wp-gutenberg')}
						allowedFormats={[]}
					/>
					<RichText
						tagName="p"
						className={classes.text || undefined}
						value={text}
						onChange={(value) => setAttributes({ text: value })}
						placeholder={__('Short text…', 'wp-gutenberg')}
						allowedFormats={['core/bold', 'core/italic', 'core/link']}
					/>
				</div>
				{showBadge && <Badge className={classes.badge} />}
			</article>
		</>
	);
}
