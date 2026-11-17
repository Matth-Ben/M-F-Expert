/**
 * Questions fréquentes (E3) — editor view. Keep markup & classes identical to render.php.
 * The accordion only accepts core/details (question = summary, answer = paragraphs/lists).
 */
import { __ } from '@wordpress/i18n';
import { RichText, useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import Icon from '../_shared/icon';

const question = (summary, answer = '[Réponse à rédiger avec M&amp;F Expert]') => [
	'core/details',
	{ summary },
	[['core/paragraph', { content: answer }]],
];

/** Questions of the home page mock-up (C2). The Conseils page pattern adds a sixth one. */
const TEMPLATE = [
	question('Comment se déroule une séance ?'),
	question('Comment reconnaître les poux et les lentes ?'),
	question(
		'Que faire après le traitement ?',
		'Chaque famille reçoit des explications détaillées et un protocole à suivre à la maison après le traitement.'
	),
	question('Quel protocole suivre à la maison ?'),
	question('Comment prévenir une réinfestation ?'),
];

export default function Edit({ attributes, setAttributes }) {
	const { eyebrow, title, text, chatLabel, chatLabelMobile } = attributes;

	const blockProps = useBlockProps({ className: 'wpg-faq pb-section-lg' });
	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'wpg-faq__list flex flex-col gap-3 lg:col-start-2 lg:row-span-2 lg:row-start-1' },
		{ allowedBlocks: ['core/details'], template: TEMPLATE, defaultBlock: question(''), directInsert: true }
	);

	return (
		<section {...blockProps}>
			<div className="container grid items-start gap-y-4 lg:grid-cols-[minmax(0,0.75fr)_minmax(0,1.25fr)] lg:grid-rows-[auto_1fr] lg:gap-x-16">
				<div className="flex flex-col gap-3 max-lg:mb-1.5 lg:gap-4">
					<RichText
						tagName="p"
						className="eyebrow"
						value={eyebrow}
						onChange={(value) => setAttributes({ eyebrow: value })}
						placeholder={__('Eyebrow (optional)…', 'wp-gutenberg')}
						allowedFormats={[]}
					/>
					<RichText
						tagName="h2"
						className="h2"
						value={title}
						onChange={(value) => setAttributes({ title: value })}
						placeholder={__('Title…', 'wp-gutenberg')}
					/>
					<RichText
						tagName="p"
						className="max-w-[25rem]"
						value={text}
						onChange={(value) => setAttributes({ text: value })}
						placeholder={__('Text…', 'wp-gutenberg')}
					/>
				</div>

				<div {...innerBlocksProps} />

				<div className="lg:col-start-1 lg:row-start-2">
					<a className="btn btn--primary btn--sm btn--block-mobile py-3.5 text-cream">
						<Icon name="chat" />
						{/* Short label below lg (mobile mock-up), long label on desktop: edit each in its own viewport preview. */}
						<RichText
							tagName="span"
							className="lg:hidden"
							value={chatLabelMobile}
							onChange={(value) => setAttributes({ chatLabelMobile: value })}
							placeholder={__('Chat button label (mobile)…', 'wp-gutenberg')}
							allowedFormats={[]}
						/>
						<RichText
							tagName="span"
							className="max-lg:hidden"
							value={chatLabel}
							onChange={(value) => setAttributes({ chatLabel: value })}
							placeholder={__('Chat button label…', 'wp-gutenberg')}
							allowedFormats={[]}
						/>
					</a>
				</div>
			</div>
		</section>
	);
}
