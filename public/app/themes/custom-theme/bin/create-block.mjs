#!/usr/bin/env node
/**
 * Scaffold a new block in blocks/<slug>/.
 *
 *   pnpm block:new testimonial
 *   pnpm block:new testimonial --title="Témoignage" --inner --view
 *
 * --inner  the block contains other blocks (InnerBlocks)
 * --view   adds a front-end view.js (loaded only on pages using the block)
 *
 * Then run `pnpm dev` (or `pnpm build`): the block is compiled and registered automatically.
 */
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const args = process.argv.slice(2);
const slug = args.find((arg) => !arg.startsWith('--'));
const flag = (name) => args.includes(`--${name}`);
const option = (name) => args.find((arg) => arg.startsWith(`--${name}=`))?.split('=').slice(1).join('=');

if (!slug || !/^[a-z][a-z0-9-]*$/.test(slug)) {
	console.error('Usage: pnpm block:new <slug> [--title="Title"] [--inner] [--view]\n<slug> must be kebab-case (e.g. "testimonial").');
	process.exit(1);
}

const dir = path.join(root, 'blocks', slug);
if (fs.existsSync(dir)) {
	console.error(`blocks/${slug} already exists.`);
	process.exit(1);
}

const inner = flag('inner');
const view = flag('view');
const title = option('title') ?? slug.replace(/-/g, ' ').replace(/^\w/, (c) => c.toUpperCase());
const cssClass = `wpg-${slug}`;

const blockJson = {
	$schema: 'https://schemas.wp.org/trunk/block.json',
	apiVersion: 3,
	name: `wpg/${slug}`,
	version: '1.0.0',
	title,
	category: 'wpg',
	icon: 'block-default',
	description: '',
	textdomain: 'wp-gutenberg',
	attributes: {
		title: { type: 'string', default: '' },
		align: { type: 'string', default: 'full' },
	},
	supports: {
		html: false,
		align: ['full'],
		anchor: true,
		color: { background: true, text: true, link: false },
	},
	editorScript: 'file:./index.js',
	style: 'file:./style-index.css',
	...(view ? { viewScriptModule: 'file:./view.js' } : {}),
	render: 'file:./render.php',
};

const files = {
	'block.json': JSON.stringify(blockJson, null, '\t') + '\n',

	'index.js': `import { registerBlockType } from '@wordpress/blocks';
${inner ? "import { InnerBlocks } from '@wordpress/block-editor';\n" : ''}import metadata from './block.json';
import Edit from './edit';
import './style.css';

registerBlockType(metadata.name, {
	edit: Edit,
	// Dynamic block: the front markup comes from render.php.
	save: () => ${inner ? '<InnerBlocks.Content />' : 'null'},
});
`,

	'edit.js': `/**
 * ${title} — editor view. Keep markup & classes identical to render.php.
 */
import { __ } from '@wordpress/i18n';
import { InspectorControls, RichText, useBlockProps${inner ? ', useInnerBlocksProps' : ''} } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
	const blockProps = useBlockProps({ className: '${cssClass} py-section-md' });
${inner ? "	const innerBlocksProps = useInnerBlocksProps({ className: 'mt-6 space-y-6' }, { template: [['core/paragraph']] });\n" : ''}
	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Settings', 'wp-gutenberg')}>{/* Controls (ToggleControl, SelectControl…) */}</PanelBody>
			</InspectorControls>

			<section {...blockProps}>
				<div className="container">
					<RichText
						tagName="h2"
						className="h1"
						value={attributes.title}
						onChange={(title) => setAttributes({ title })}
						placeholder={__('Title…', 'wp-gutenberg')}
					/>
${inner ? '					<div {...innerBlocksProps} />\n' : ''}				</div>
			</section>
		</>
	);
}
`,

	'render.php': `<?php
/**
 * ${title} — front-end markup (mirror of edit.js).
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Inner blocks HTML.
 * @var WP_Block             $block      Block instance.
 *
 * @package WPGutenberg
 */

$wrapper = get_block_wrapper_attributes(
	array(
		'class' => '${cssClass} py-section-md',${view ? `\n\t\t'data-wpg-block' => '${slug}',` : ''}
	)
);
?>
<section <?php echo $wrapper; // phpcs:ignore ?>>
	<div class="container">
		<?php if ( ! empty( $attributes['title'] ) ) : ?>
			<h2 class="h1"><?php echo wp_kses_post( $attributes['title'] ); ?></h2>
		<?php endif; ?>
${inner ? `		<div class="mt-6 space-y-6"><?php echo $content; // phpcs:ignore ?></div>\n` : ''}	</div>
</section>
`,

	'style.css': `/* ${title} — loaded only on pages using this block (front + editor). Prefer Tailwind classes. */
.${cssClass} {
}
`,
};

if (view) {
	files['view.js'] = `/**
 * ${title} — front-end behaviour (loaded only on pages using the block).
 * setup(el) runs for each [data-wpg-block="${slug}"], also after Barba navigations.
 */
function setup(el) {
	// ...
	return () => {
		// Cleanup (listeners, observers) when the page is left.
	};
}

(window.wpgBlocks ??= []).push(['${slug}', setup]);
`;
}

fs.mkdirSync(dir, { recursive: true });
for (const [name, content] of Object.entries(files)) {
	fs.writeFileSync(path.join(dir, name), content);
}

console.log(`✔ blocks/${slug} created (${Object.keys(files).join(', ')})`);
console.log('  Run `pnpm dev` or `pnpm build` — the block is registered automatically as wpg/' + slug + '.');
