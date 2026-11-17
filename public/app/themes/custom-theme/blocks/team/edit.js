/**
 * Équipe — editor view. Same markup & classes as render.php.
 */
import { __ } from '@wordpress/i18n';
import { RichText, useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

const MEMBER = {
	name: '[Prénom Nom]',
	role: '[Rôle · fondatrice]',
	bio: '[Quelques lignes de présentation à rédiger avec M&F Expert.]',
};
const TEMPLATE = [
	['wpg/team-member', MEMBER],
	['wpg/team-member', MEMBER],
];

export default function Edit({ attributes, setAttributes }) {
	const blockProps = useBlockProps({ className: 'wpg-team pb-section-lg' });
	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'grid gap-5 md:grid-cols-2 lg:max-w-[53.75rem]' },
		{ allowedBlocks: ['wpg/team-member'], template: TEMPLATE, orientation: 'horizontal' }
	);

	return (
		<section {...blockProps}>
			<div className="container flex flex-col gap-5.5 lg:gap-8">
				<RichText
					tagName="h2"
					className="h2"
					value={attributes.title}
					onChange={(value) => setAttributes({ title: value })}
					placeholder={__('Title…', 'wp-gutenberg')}
					allowedFormats={['core/italic']}
				/>
				<div {...innerBlocksProps} />
			</div>
		</section>
	);
}
