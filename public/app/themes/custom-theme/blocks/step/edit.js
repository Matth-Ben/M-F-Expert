/**
 * Étape — editor view. Same markup & classes as render.php.
 */
import { __ } from '@wordpress/i18n';
import { RichText, useBlockProps } from '@wordpress/block-editor';

export default function Edit({ attributes, setAttributes }) {
	const { title, text } = attributes;
	const blockProps = useBlockProps({
		className:
			'wpg-step grid grid-cols-[3.5rem_minmax(0,1fr)] items-start gap-4 lg:flex lg:flex-col lg:items-center lg:gap-3.5 lg:text-center',
	});

	return (
		<li {...blockProps}>
			<span
				className="wpg-step__number relative z-[1] grid size-14 place-items-center rounded-full bg-bordeaux font-serif text-[1.625rem] leading-none text-cream lg:size-18 lg:text-[2rem]"
				aria-hidden="true"
			/>
			<div className="pt-1.5 lg:flex lg:flex-col lg:items-center lg:gap-3.5 lg:pt-0">
				<RichText
					tagName="h3"
					className="script text-[2.125rem] leading-[1.05] text-bordeaux lg:text-[2.5rem]"
					value={title}
					onChange={(value) => setAttributes({ title: value })}
					placeholder={__('Step title…', 'wp-gutenberg')}
					allowedFormats={[]}
				/>
				<RichText
					tagName="p"
					className="mt-1 text-note lg:mt-0 lg:max-w-[15.625rem]"
					value={text}
					onChange={(value) => setAttributes({ text: value })}
					placeholder={__('One sentence describing the step…', 'wp-gutenberg')}
					allowedFormats={['core/bold', 'core/italic', 'core/link']}
				/>
			</div>
		</li>
	);
}
