/**
 * Tarifs — editor view. Same markup & classes as render.php.
 */
import { __ } from '@wordpress/i18n';
import { InspectorControls, RichText, useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import { PanelBody, SelectControl, ToggleControl } from '@wordpress/components';
import Icon from '../_shared/icon';
import { LinkPanel } from '../_shared/controls';
import { CLASSES } from './classes';

const TEMPLATE = [
	['wpg/price', { icon: 'search', name: 'Diagnostic &amp; traitement', description: 'Poux et lentes, méthode manuelle et mécanique' }],
	['wpg/price', { icon: 'check', name: 'Suivi &amp; contrôle', description: 'Pour s’assurer d’un résultat durable' }],
	['wpg/price', { icon: 'heart', name: 'Conseils personnalisés', description: 'Prévenir une nouvelle infestation' }],
	['wpg/price', { icon: 'home', name: 'Location de matériel de nettoyage', description: 'Pour traiter votre intérieur' }],
];

export default function Edit({ attributes, setAttributes }) {
	const { layout, showHeader, title, intro, note, buttonText, buttonUrl } = attributes;
	const classes = CLASSES[layout] ?? CLASSES.cards;
	const isPhone = !buttonUrl;

	const blockProps = useBlockProps({ className: 'wpg-prices pb-section-lg' });
	const innerBlocksProps = useInnerBlocksProps(
		{ className: classes.list, role: 'list' },
		{ allowedBlocks: ['wpg/price'], template: TEMPLATE, orientation: 'horizontal' }
	);

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Settings', 'wp-gutenberg')}>
					<SelectControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={__('Layout', 'wp-gutenberg')}
						value={layout}
						options={[
							{ value: 'cards', label: __('Cards (home page)', 'wp-gutenberg') },
							{ value: 'list', label: __('List on two columns', 'wp-gutenberg') },
						]}
						onChange={(value) => setAttributes({ layout: value })}
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						label={__('Show the section header', 'wp-gutenberg')}
						help={__('Without a header, price names become level 2 headings.', 'wp-gutenberg')}
						checked={showHeader}
						onChange={(value) => setAttributes({ showHeader: value })}
					/>
				</PanelBody>
				<LinkPanel
					title={__('Button link', 'wp-gutenberg')}
					help={__('Leave empty to call the phone number set in Settings > Contact details.', 'wp-gutenberg')}
					url={buttonUrl}
					onChange={(url) => setAttributes({ buttonUrl: url })}
				/>
			</InspectorControls>

			<section {...blockProps}>
				<div className={classes.stack}>
					{showHeader && (
						<div className="grid gap-2.5 lg:grid-cols-[minmax(0,0.8fr)_minmax(0,1.2fr)] lg:items-end lg:gap-10">
							<RichText
								tagName="h2"
								className="script text-[3.25rem] text-bordeaux lg:text-[4.25rem]"
								value={title}
								onChange={(value) => setAttributes({ title: value })}
								placeholder={__('Title…', 'wp-gutenberg')}
								allowedFormats={[]}
							/>
							<RichText
								tagName="p"
								className="max-w-[35rem] lg:justify-self-end"
								value={intro}
								onChange={(value) => setAttributes({ intro: value })}
								placeholder={__('Introduction text…', 'wp-gutenberg')}
								allowedFormats={['core/bold', 'core/italic', 'core/link']}
							/>
						</div>
					)}

					<ul {...innerBlocksProps} />

					<div className="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
						<RichText
							tagName="p"
							className="text-sm font-semibold text-gold-dark"
							value={note}
							onChange={(value) => setAttributes({ note: value })}
							placeholder={__('Note (optional)…', 'wp-gutenberg')}
							allowedFormats={['core/bold', 'core/italic', 'core/link']}
						/>
						<span className="btn btn--primary btn--block-mobile text-cream max-lg:px-2.5 max-lg:text-md lg:ml-auto">
							{isPhone && <Icon name="phone" />}
							<RichText
								tagName="span"
								value={buttonText}
								onChange={(value) => setAttributes({ buttonText: value })}
								placeholder={__('Button text…', 'wp-gutenberg')}
								allowedFormats={[]}
							/>
						</span>
					</div>
				</div>
			</section>
		</>
	);
}
