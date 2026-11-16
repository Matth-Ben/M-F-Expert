import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import metadata from './block.json';

const TEMPLATE = [
	['wpg/stat', { value: 25, label: 'ans d’expérience' }],
	['wpg/stat', { value: 300, suffix: '+', label: 'projets livrés' }],
	['wpg/stat', { value: 98, suffix: '%', label: 'clients satisfaits' }],
];

function Edit() {
	const blockProps = useBlockProps({ className: 'py-section-md' });
	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'container grid gap-10 sm:grid-cols-2 lg:grid-cols-3' },
		{ template: TEMPLATE, orientation: 'horizontal' }
	);

	return (
		<section {...blockProps}>
			<div {...innerBlocksProps} />
		</section>
	);
}

registerBlockType(metadata.name, {
	edit: Edit,
	save: () => <InnerBlocks.Content />,
});
