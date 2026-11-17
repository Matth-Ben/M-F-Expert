/**
 * Réassurance (B3) — editor view. Keep markup & classes identical to render.php.
 * A single core/list styled by .check-list--band (shared.css): structure locked, texts editable.
 */
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

const TEMPLATE = [
	[
		'core/list',
		{ className: 'check-list check-list--band' },
		[
			['core/list-item', { content: 'Sans produits chimiques' }],
			['core/list-item', { content: 'Douce pour le cuir chevelu' }],
			['core/list-item', { content: 'Enfants et parents' }],
		],
	],
];

export default function Edit() {
	const blockProps = useBlockProps({ className: 'wpg-reassurance pb-12 lg:pb-18' });
	const innerBlocksProps = useInnerBlocksProps({ className: 'container' }, { template: TEMPLATE, templateLock: 'all' });

	return (
		<section {...blockProps}>
			<div {...innerBlocksProps} />
		</section>
	);
}
