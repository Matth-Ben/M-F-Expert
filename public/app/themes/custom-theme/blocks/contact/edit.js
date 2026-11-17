/**
 * Contact complet (F1) — editor view. Keep markup & classes identical to render.php.
 * Contact details come from Réglages > Coordonnées (window.wpgEditor.contact, same values as wpg_contact()).
 * The form is inert here (read-only fields, no submission); links have no href.
 */
import { __ } from '@wordpress/i18n';
import { InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, Placeholder, TextControl } from '@wordpress/components';
import Icon from '../_shared/icon';

const FIELD_CLASS = 'min-h-12 w-full scroll-mt-12 rounded-2xl border-2 border-field bg-white px-4 py-3 text-base leading-[1.1875rem] text-ink aria-invalid:border-bordeaux';

/** Accept the embed URL or the whole <iframe> code copied from OpenStreetMap / Google Maps. */
const embedSrc = (value) => value.match(/src=["']([^"']+)["']/)?.[1] ?? value.trim();

export default function Edit({ attributes, setAttributes }) {
	const { formTitle, consent, detailsTitle, chatTitle, chatText, chatLabel, mapUrl } = attributes;
	const contact = window.wpgEditor?.contact ?? {};
	const mapSrc = mapUrl.startsWith('https://') ? mapUrl : '';

	const details = [
		['phone', __('Phone', 'wp-gutenberg'), contact.phone, true],
		['mail', __('Email', 'wp-gutenberg'), contact.email, true],
		['pin', __('Address', 'wp-gutenberg'), contact.address, false],
		['clock', __('Opening hours', 'wp-gutenberg'), contact.hours, false],
		['car', __('Access & parking', 'wp-gutenberg'), contact.access, false],
	].filter(([, , value]) => value?.trim());

	const fields = [
		['name', __('Your name', 'wp-gutenberg'), 'text', 'name', true],
		['email', __('Your email', 'wp-gutenberg'), 'email', 'email', true],
		['phone', __('Your phone (optional)', 'wp-gutenberg'), 'tel', 'tel', false],
		['message', __('Your message', 'wp-gutenberg'), 'textarea', 'off', true],
	];

	const blockProps = useBlockProps({ className: 'wpg-contact pb-section-lg' });

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Map', 'wp-gutenberg')}>
					<TextControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						type="url"
						label={__('Map embed URL', 'wp-gutenberg')}
						help={__('OpenStreetMap or Google Maps: Share > Embed a map, then paste the link (or the whole <iframe> code). Must start with https://. Leave empty to hide the map.', 'wp-gutenberg')}
						value={mapUrl}
						onChange={(value) => setAttributes({ mapUrl: embedSrc(value) })}
					/>
				</PanelBody>
				<PanelBody title={__('Contact details', 'wp-gutenberg')} initialOpen={false}>
					<p>{__('Phone, email, address, opening hours and access are edited in Settings > Contact details.', 'wp-gutenberg')}</p>
				</PanelBody>
			</InspectorControls>

			<section {...blockProps}>
				<div className="container flex flex-col gap-5 lg:gap-8">
					<div className="grid grid-cols-1 gap-5 lg:grid-cols-[minmax(0,1.05fr)_minmax(0,0.95fr)] lg:grid-rows-[1fr_auto] lg:gap-x-8 lg:gap-y-6">
						<div className="flex flex-col gap-5 rounded-panel bg-bordeaux p-7 text-cream shadow-card lg:col-start-2 lg:row-start-1 lg:gap-6.5 lg:p-10">
							<RichText
								tagName="h2"
								className="script text-[2.5rem] text-gold-pale lg:text-[2.875rem]"
								value={detailsTitle}
								onChange={(value) => setAttributes({ detailsTitle: value })}
								placeholder={__('Title…', 'wp-gutenberg')}
								allowedFormats={[]}
							/>

							{details.length > 0 && (
								<ul className="flex list-none flex-col gap-4 p-0 lg:gap-4.5" role="list">
									{details.map(([icon, label, value, isLink]) => (
										<li key={icon} className="flex items-center gap-4">
											<span className="icon-dot size-12 bg-cream text-bordeaux">
												<Icon name={icon} className="size-[22px]" />
											</span>
											<div className="leading-[1.45]">
												<strong className="block">{label}</strong>
												{isLink ? (
													<a className="text-note wrap-anywhere underline underline-offset-4">{value}</a>
												) : (
													<span className="text-note whitespace-pre-line">{value}</span>
												)}
											</div>
										</li>
									))}
								</ul>
							)}

							{contact.phone?.trim() && (
								<a className="btn btn--light min-h-[3.375rem] w-full text-bordeaux">
									<Icon name="phone" />
									{__('Call us', 'wp-gutenberg')}
								</a>
							)}
						</div>

						<form
							className="flex scroll-mt-6 flex-col gap-4 rounded-panel bg-cream p-6 lg:col-start-1 lg:row-span-2 lg:row-start-1 lg:p-10"
							onSubmit={(event) => event.preventDefault()}
						>
							<RichText
								tagName="h2"
								className="script text-[2.375rem] text-bordeaux lg:text-[2.75rem]"
								value={formTitle}
								onChange={(value) => setAttributes({ formTitle: value })}
								placeholder={__('Title…', 'wp-gutenberg')}
								allowedFormats={[]}
							/>

							{fields.map(([name, label, type, autoComplete, required]) => (
								<div key={name} className="flex flex-col gap-1.5">
									<label className="text-[0.90625rem] font-bold text-bordeaux" htmlFor={`wpg-contact-${name}`}>
										{label}
									</label>
									{type === 'textarea' ? (
										<textarea
											id={`wpg-contact-${name}`}
											className={`${FIELD_CLASS} resize-y`}
											rows={5}
											autoComplete={autoComplete}
											aria-required={required}
											readOnly
											tabIndex={-1}
										/>
									) : (
										<input
											id={`wpg-contact-${name}`}
											type={type}
											className={FIELD_CLASS}
											autoComplete={autoComplete}
											aria-required={required}
											readOnly
											tabIndex={-1}
										/>
									)}
								</div>
							))}

							<RichText
								tagName="p"
								className="text-xs text-ink-soft"
								value={consent}
								onChange={(value) => setAttributes({ consent: value })}
								placeholder={__('Privacy notice (GDPR)…', 'wp-gutenberg')}
								allowedFormats={['core/link', 'core/bold']}
							/>

							<button className="btn btn--primary self-start" type="button" tabIndex={-1}>
								<Icon name="mail" />
								{__('Send the message', 'wp-gutenberg')}
							</button>
						</form>

						<div className="flex flex-col items-stretch gap-4 rounded-panel bg-cream px-6 py-6.5 lg:col-start-2 lg:row-start-2 lg:px-8 xl:flex-row xl:items-center xl:justify-between xl:gap-5">
							<div className="flex flex-col gap-1.5">
								<RichText
									tagName="h2"
									className="script text-[2.5rem] text-bordeaux"
									value={chatTitle}
									onChange={(value) => setAttributes({ chatTitle: value })}
									placeholder={__('Title…', 'wp-gutenberg')}
									allowedFormats={[]}
								/>
								<RichText
									tagName="p"
									className="text-md leading-normal text-ink-soft"
									value={chatText}
									onChange={(value) => setAttributes({ chatText: value })}
									placeholder={__('Text…', 'wp-gutenberg')}
								/>
							</div>
							<a className="btn btn--primary btn--sm btn--block-mobile flex-none py-3.5 text-cream">
								<Icon name="chat" />
								<RichText
									tagName="span"
									value={chatLabel}
									onChange={(value) => setAttributes({ chatLabel: value })}
									placeholder={__('Button label…', 'wp-gutenberg')}
									allowedFormats={[]}
								/>
							</a>
						</div>
					</div>

					{mapSrc ? (
						<div className="h-60 overflow-hidden rounded-panel bg-cream lg:h-80">
							<iframe
								className="pointer-events-none size-full border-0"
								src={mapSrc}
								title={__('Map: how to find the salon', 'wp-gutenberg')}
								loading="lazy"
								referrerPolicy="no-referrer-when-downgrade"
							/>
						</div>
					) : (
						<Placeholder
							icon="location"
							label={__('Map', 'wp-gutenberg')}
							instructions={__('Paste the map embed URL in the block settings (sidebar). Nothing is shown on the site while it is empty.', 'wp-gutenberg')}
						/>
					)}
				</div>
			</section>
		</>
	);
}
