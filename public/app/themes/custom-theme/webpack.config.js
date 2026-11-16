/**
 * @wordpress/scripts config — Gutenberg blocks + editor-only script.
 *
 * Blocks: every `blocks/<name>/block.json` is detected automatically and its
 * `editorScript`, `style`, `viewScriptModule`, `render` files are built into `build/<name>/`.
 *
 * Extra entry: `src/editor/index.js` → `build/_editor/index.js` (block styles, variations, filters).
 */
import defaultConfig from '@wordpress/scripts/config/webpack.config.js';

const [scriptConfig, ...moduleConfigs] = Array.isArray(defaultConfig) ? defaultConfig : [defaultConfig];

const resolveEntry = (entry) => (typeof entry === 'function' ? entry() : entry);

// package.json has "type": "module" (for Vite); allow extensionless imports (`./edit`) in blocks.
const withLooseImports = (config) => ({
	...config,
	module: {
		...config.module,
		rules: [...(config.module?.rules ?? []), { test: /\.m?js$/, resolve: { fullySpecified: false } }],
	},
});

export default [
	withLooseImports({
		...scriptConfig,
		entry: () => ({
			...resolveEntry(scriptConfig.entry),
			'_editor/index': './src/editor/index.js',
		}),
	}),
	...moduleConfigs.map(withLooseImports),
];
