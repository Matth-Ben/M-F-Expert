/**
 * Editor controls shared by the "prestations" family of blocks
 * (services, service, service-family, prices, price, included, feature, info-box).
 *
 * - IconControl: pick a line icon from the sprite (the list comes from the
 *   attribute `enum` in block.json, so PHP and JS accept the same values).
 * - LinkPanel: URL picker (LinkControl: search a page or paste a URL).
 */
import { __ } from '@wordpress/i18n';
import { LinkControl } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';

const ICON_LABELS = {
	arrow: __('Arrow', 'wp-gutenberg'),
	calendar: __('Calendar', 'wp-gutenberg'),
	car: __('Car', 'wp-gutenberg'),
	chat: __('Chat', 'wp-gutenberg'),
	check: __('Check mark', 'wp-gutenberg'),
	clock: __('Clock', 'wp-gutenberg'),
	heart: __('Heart', 'wp-gutenberg'),
	home: __('House', 'wp-gutenberg'),
	mail: __('Email', 'wp-gutenberg'),
	phone: __('Phone', 'wp-gutenberg'),
	pin: __('Location', 'wp-gutenberg'),
	plus: __('Plus', 'wp-gutenberg'),
	quote: __('Quote', 'wp-gutenberg'),
	search: __('Magnifying glass', 'wp-gutenberg'),
	shield: __('Shield', 'wp-gutenberg'),
	star: __('Star', 'wp-gutenberg'),
	users: __('People', 'wp-gutenberg'),
};

/**
 * @param {{ value: string, icons: string[], onChange: Function }} props
 */
export function IconControl({ value, icons, onChange }) {
	return (
		<SelectControl
			__next40pxDefaultSize
			__nextHasNoMarginBottom
			label={__('Icon', 'wp-gutenberg')}
			value={value}
			options={icons.map((icon) => ({ value: icon, label: ICON_LABELS[icon] ?? icon }))}
			onChange={onChange}
		/>
	);
}

/**
 * @param {{ title: string, url: string, onChange: Function, help?: string }} props
 */
export function LinkPanel({ title, url, onChange, help }) {
	return (
		<PanelBody title={title}>
			{help && <p className="components-base-control__help">{help}</p>}
			<LinkControl
				value={url ? { url } : undefined}
				onChange={(next) => onChange(next?.url ?? '')}
				onRemove={() => onChange('')}
				settings={[]}
			/>
		</PanelBody>
	);
}
