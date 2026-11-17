/**
 * Citation en bandeau (E2) — editor view. Keep markup & classes identical to render.php.
 */
import { __ } from '@wordpress/i18n';
import { RichText, useBlockProps } from '@wordpress/block-editor';

export default function Edit({ attributes, setAttributes }) {
	const { eyebrow, quote, signature } = attributes;
	const blockProps = useBlockProps({ className: 'wpg-quote-band bg-bordeaux py-section-md text-cream' });

	return (
		<section {...blockProps}>
			<div className="container">
				<figure className="mx-auto my-0 flex max-w-[56.25rem] flex-col items-center gap-3.5 text-center lg:gap-5">
					<RichText
						tagName="p"
						className="text-sm font-bold tracking-[0.14em] text-gold-pale uppercase"
						value={eyebrow}
						onChange={(value) => setAttributes({ eyebrow: value })}
						placeholder={__('Eyebrow (optional)…', 'wp-gutenberg')}
						allowedFormats={[]}
					/>

					<blockquote className="quote border-0 pl-0 not-italic">
						<RichText
							tagName="p"
							value={quote}
							onChange={(value) => setAttributes({ quote: value })}
							placeholder={__('Quote…', 'wp-gutenberg')}
							allowedFormats={['core/bold', 'core/italic']}
						/>
					</blockquote>

					<RichText
						tagName="figcaption"
						className="script text-[1.875rem] leading-[1.15] text-gold-pale lg:text-[2.375rem]"
						value={signature}
						onChange={(value) => setAttributes({ signature: value })}
						placeholder={__('Signature (optional)…', 'wp-gutenberg')}
						allowedFormats={[]}
					/>
				</figure>
			</div>
		</section>
	);
}
