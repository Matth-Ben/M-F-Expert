/**
 * Accroche (B4) — editor view. Keep markup & classes identical to render.php.
 */
import { __ } from '@wordpress/i18n';
import { RichText, useBlockProps } from '@wordpress/block-editor';

const LINES = [
	['hook', 'hook', __('Catchphrase…', 'wp-gutenberg')],
	['text', 'max-w-165 text-card leading-[1.4] font-bold tracking-[-0.02em] text-accent lg:text-3xl', __('Bold text…', 'wp-gutenberg')],
	['signature', 'script text-[2.375rem] leading-[1.15] text-bordeaux lg:text-[3.125rem]', __('Signature…', 'wp-gutenberg')],
];

export default function Edit({ attributes, setAttributes }) {
	const blockProps = useBlockProps({ className: 'wpg-tagline pb-section-lg' });

	return (
		<section {...blockProps}>
			<div className="mx-auto flex max-w-215 flex-col items-center gap-2.5 px-5 text-center lg:gap-3.5 lg:px-10">
				{LINES.map(([key, className, placeholder]) => (
					<RichText
						key={key}
						tagName="p"
						className={className}
						value={attributes[key]}
						onChange={(value) => setAttributes({ [key]: value })}
						placeholder={placeholder}
						allowedFormats={['core/bold', 'core/italic']}
					/>
				))}
			</div>
		</section>
	);
}
