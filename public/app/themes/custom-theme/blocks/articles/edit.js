/**
 * Articles de conseils (E1) — editor view. Keep markup & classes identical to render.php.
 * Posts come from the REST API (same query as render.php: latest published posts, optional category).
 */
import { __ } from '@wordpress/i18n';
import { InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { Notice, PanelBody, RangeControl, SelectControl, Spinner } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import { decodeEntities } from '@wordpress/html-entities';
import Icon from '../_shared/icon';

/** Same rule as render.php: skip the default « Non classé » category. */
const DEFAULT_CATEGORY_SLUGS = ['uncategorized', 'non-classe'];

const stripTags = (html = '') => html.replace(/<[^>]*>/g, '').trim();

const trimWords = (text, count = 24) => {
	const words = text.split(/\s+/).filter(Boolean);
	return words.length > count ? `${words.slice(0, count).join(' ')}…` : words.join(' ');
};

function Card({ post }) {
	const media = post._embedded?.['wp:featuredmedia']?.[0];
	const image = media?.media_details?.sizes?.medium_large?.source_url ?? media?.source_url;
	const category = (post._embedded?.['wp:term']?.[0] ?? []).find((term) => !DEFAULT_CATEGORY_SLUGS.includes(term.slug));
	const excerpt = trimWords(decodeEntities(stripTags(post.excerpt?.rendered)));

	return (
		<li className="relative flex flex-col rounded-card bg-cream">
			<div className="h-[9.375rem] overflow-hidden rounded-t-card bg-rose/20 lg:h-45">
				{image ? (
					<img className="size-full object-cover" src={image} alt="" />
				) : (
					<div className="grid size-full place-items-center">
						<img className="h-20 w-auto opacity-60" src={window.wpgEditor?.logo} alt="" width="84" height="80" />
					</div>
				)}
			</div>

			<div className="flex flex-1 flex-col items-start gap-2.5 px-6 pt-5.5 pb-6.5">
				{category && (
					<span className="rounded-full bg-bordeaux px-3 py-1 text-xs font-bold tracking-[0.06em] text-cream uppercase">
						{decodeEntities(category.name)}
					</span>
				)}

				<h3 className="card-title leading-[1.3]">
					<a className="underline-offset-4 after:absolute after:inset-0 after:rounded-card hover:underline focus-visible:outline-none focus-visible:after:outline-3 focus-visible:after:outline-offset-2 focus-visible:after:outline-gold-dark">
						{decodeEntities(post.title?.rendered ?? '')}
					</a>
				</h3>

				{excerpt && <p className="text-md">{excerpt}</p>}

				<span className="link-arrow mt-auto text-md" aria-hidden="true">
					{__('Read the article', 'wp-gutenberg')}
					<Icon name="arrow" />
				</span>
			</div>
		</li>
	);
}

export default function Edit({ attributes, setAttributes, isSelected }) {
	const { eyebrow, title, count, categoryId } = attributes;

	const { posts, categories } = useSelect(
		(select) => {
			const { getEntityRecords } = select(coreStore);
			const query = { per_page: count, status: 'publish', _embed: 'wp:featuredmedia,wp:term' };
			if (categoryId) {
				query.categories = [categoryId];
			}

			return {
				posts: getEntityRecords('postType', 'post', query),
				categories: getEntityRecords('taxonomy', 'category', { per_page: -1, hide_empty: true }) ?? [],
			};
		},
		[count, categoryId]
	);

	const blockProps = useBlockProps({ className: 'wpg-articles pb-section-lg' });

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Articles', 'wp-gutenberg')}>
					<RangeControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={__('Number of articles', 'wp-gutenberg')}
						value={count}
						min={1}
						max={12}
						onChange={(value) => setAttributes({ count: value ?? 3 })}
					/>
					<SelectControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={__('Category', 'wp-gutenberg')}
						value={String(categoryId)}
						options={[
							{ label: __('All categories', 'wp-gutenberg'), value: '0' },
							...categories.map((term) => ({ label: decodeEntities(term.name), value: String(term.id) })),
						]}
						onChange={(value) => setAttributes({ categoryId: Number(value) })}
					/>
				</PanelBody>
			</InspectorControls>

			<section {...blockProps}>
				<div className="container flex flex-col gap-5.5 lg:gap-8">
					<div className="flex flex-col gap-3">
						{(eyebrow || isSelected) && (
							<RichText
								tagName="p"
								className="eyebrow"
								value={eyebrow}
								onChange={(value) => setAttributes({ eyebrow: value })}
								placeholder={__('Eyebrow (optional)…', 'wp-gutenberg')}
							/>
						)}
						<RichText
							tagName="h2"
							className="h2"
							value={title}
							onChange={(value) => setAttributes({ title: value })}
							placeholder={__('Title…', 'wp-gutenberg')}
						/>
					</div>

					{posts === null && <Spinner />}

					{posts?.length === 0 && (
						<Notice status="info" isDismissible={false}>
							{__('No published article yet: this block stays hidden on the site until one is published.', 'wp-gutenberg')}
						</Notice>
					)}

					{posts?.length > 0 && (
						<ul className="grid list-none gap-5 p-0 sm:grid-cols-2 lg:grid-cols-3" role="list">
							{posts.map((post) => (
								<Card key={post.id} post={post} />
							))}
						</ul>
					)}
				</div>
			</section>
		</>
	);
}
