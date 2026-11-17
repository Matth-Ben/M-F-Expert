/**
 * Hero — editor view. Same markup & classes as render.php (keep them in sync).
 *
 * - `home`: the logo-text H1 is edited in the sidebar (the « & » is rendered at half size,
 *   which RichText can't show), the rest in the canvas.
 * - `page`: breadcrumb preview (page + parent titles), H1 = page title when empty.
 */
import { __ } from '@wordpress/i18n';
import { InspectorControls, RichText, useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import { store as editorStore } from '@wordpress/editor';
import { Fragment } from '@wordpress/element';
import { decodeEntities } from '@wordpress/html-entities';

// `#appeler` is resolved on the front from Réglages > Coordonnées (includes/Core/ContactLinks.php).
const HOME_TEMPLATE = [
	[
		'core/buttons',
		{},
		[
			['core/button', { text: 'Appelez-nous', url: '#appeler' }],
			['core/button', { text: 'Voir nos prestations', className: 'is-style-outline' }],
		],
	],
];

const plain = (html = '') => decodeEntities(html.replace(/<[^>]*>/g, '')).trim();

/** « M&F Expert » with the ampersand at half size (same as render.php). */
const LogoText = ({ text }) =>
	text.split('&').map((part, index) => (
		<Fragment key={index}>
			{index > 0 && <span className="mx-[0.05em] text-[0.5em]">&amp;</span>}
			{part}
		</Fragment>
	));

export default function Edit({ attributes, setAttributes }) {
	const { variant, title, text, subtitle, signature } = attributes;
	const isHome = variant === 'home';

	const { postTitle, parentTitle, siteName } = useSelect((select) => {
		const editor = select(editorStore);
		const parentId = editor?.getEditedPostAttribute('parent');
		const parent = parentId ? select(coreStore).getEntityRecord('postType', editor.getCurrentPostType(), parentId) : null;

		return {
			postTitle: editor?.getEditedPostAttribute('title') ?? '',
			parentTitle: parent?.title?.rendered ? decodeEntities(parent.title.rendered) : '',
			siteName: decodeEntities(select(coreStore).getEntityRecord('root', '__unstableBase')?.name ?? ''),
		};
	}, []);

	const blockProps = useBlockProps({
		className: isHome ? 'wpg-hero wpg-hero--home pt-3.5 pb-14 lg:pt-11' : 'wpg-hero wpg-hero--page pt-1 pb-11 lg:pt-6.5 lg:pb-18',
	});
	// Template only for the home layout: page heroes keep no hidden buttons.
	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'mt-6 lg:mt-8.5' },
		{ allowedBlocks: ['core/buttons'], template: isHome ? HOME_TEMPLATE : undefined }
	);

	const pageTitle = plain(title) || plain(postTitle);
	const isLong = pageTitle.length > 16;

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Layout', 'wp-gutenberg')}>
					<SelectControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={__('Hero type', 'wp-gutenberg')}
						value={variant}
						options={[
							{ label: __('Inner page (breadcrumb, title, intro)', 'wp-gutenberg'), value: 'page' },
							{ label: __('Home page (logo, signature, buttons)', 'wp-gutenberg'), value: 'home' },
						]}
						onChange={(value) => setAttributes({ variant: value })}
					/>
					{isHome && (
						<TextControl
							__next40pxDefaultSize
							__nextHasNoMarginBottom
							label={__('Logo text', 'wp-gutenberg')}
							help={__('Leave empty to use the site name.', 'wp-gutenberg')}
							value={plain(title)}
							placeholder={siteName}
							onChange={(value) => setAttributes({ title: value })}
						/>
					)}
				</PanelBody>
			</InspectorControls>

			{isHome ? (
				<section {...blockProps}>
					<div className="container grid items-center gap-12 lg:grid-cols-[minmax(0,1.08fr)_minmax(0,0.92fr)] lg:gap-16">
						<div className="flex flex-col">
							<h1 className="display whitespace-nowrap">
								<LogoText text={plain(title) || siteName} />
							</h1>
							<RichText
								tagName="p"
								className="mt-1.5 text-[1.9375rem] leading-[1.05] font-extrabold tracking-[-0.04em] text-olive lg:text-[2.875rem]"
								value={subtitle}
								onChange={(value) => setAttributes({ subtitle: value })}
								placeholder={__('Subtitle…', 'wp-gutenberg')}
								allowedFormats={[]}
							/>
							<div className="rule mt-3.5 max-lg:w-14 lg:mt-5.5" />
							<RichText
								tagName="p"
								className="script mt-2.5 text-[1.9375rem] leading-[1.15] text-bordeaux lg:mt-3.5 lg:text-[2.5rem]"
								value={signature}
								onChange={(value) => setAttributes({ signature: value })}
								placeholder={__('Signature…', 'wp-gutenberg')}
								allowedFormats={[]}
							/>
							<div {...innerBlocksProps} />
						</div>

						<div className="flex justify-center">
							<img
								src={window.wpgEditor?.logo}
								alt={__('M&F Expert logo: two children and a comb', 'wp-gutenberg')}
								width="620"
								height="590"
								className="h-auto w-70 lg:w-110"
							/>
						</div>
					</div>
				</section>
			) : (
				<section {...blockProps}>
					<div className="container grid items-center gap-10 lg:grid-cols-[minmax(0,1.3fr)_minmax(0,0.7fr)]">
						<div className="flex flex-col gap-3.5 lg:gap-5">
							<nav aria-label={__('Breadcrumb', 'wp-gutenberg')}>
								<ol className="m-0 flex list-none flex-wrap items-center gap-x-2.5 p-0 text-sm font-semibold text-olive-dark">
									<li>
										<span className="inline-flex min-h-11 items-center underline underline-offset-4">
											{__('Home', 'wp-gutenberg')}
										</span>
									</li>
									{parentTitle && (
										<>
											<li className="text-gold-dark" aria-hidden="true">
												/
											</li>
											<li>
												<span className="inline-flex min-h-11 items-center underline underline-offset-4">{parentTitle}</span>
											</li>
										</>
									)}
									<li className="text-gold-dark" aria-hidden="true">
										/
									</li>
									<li className="font-bold text-bordeaux" aria-current="page">
										{plain(postTitle)}
									</li>
								</ol>
							</nav>

							<RichText
								tagName="h1"
								className={`h1${isLong ? ' max-lg:text-[2.4375rem]' : ''}`}
								value={title}
								onChange={(value) => setAttributes({ title: value })}
								placeholder={plain(postTitle) || __('Page title…', 'wp-gutenberg')}
								allowedFormats={['core/italic']}
							/>

							<div className="rule max-lg:w-14" />

							<RichText
								tagName="p"
								className="lead max-w-160"
								value={text}
								onChange={(value) => setAttributes({ text: value })}
								placeholder={__('Introduction text…', 'wp-gutenberg')}
								allowedFormats={['core/bold', 'core/italic', 'core/link']}
							/>
						</div>

						<div className="hidden justify-center lg:flex">
							<img src={window.wpgEditor?.logo} alt="" width="620" height="590" className="h-auto w-75" />
						</div>
					</div>
				</section>
			)}
		</>
	);
}
