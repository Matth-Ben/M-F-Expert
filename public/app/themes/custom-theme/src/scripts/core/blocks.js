/**
 * Front-end block runtime.
 *
 * A block's `view.js` (declared as `viewScriptModule` in block.json, so it is only
 * loaded on pages containing the block) registers a mount function:
 *
 *   (window.wpgBlocks ??= []).push(['stats', (el) => { ...; return () => cleanup(); }]);
 *
 * Every element `[data-wpg-block="stats"]` gets mounted once. The optional returned
 * function is called when Barba removes the element from the page.
 * Works whatever the load order between app.js and the block modules.
 */

const definitions = new Map();
const mounted = new Map(); // element => cleanup | null

/**
 * Mount registered blocks found inside `root`.
 *
 * @param {ParentNode} root
 * @param {string}     [only] Restrict to one block name.
 */
export function mountBlocks(root = document, only) {
	for (const [name, setup] of definitions) {
		if (only && only !== name) continue;

		root.querySelectorAll(`[data-wpg-block="${name}"]`).forEach((el) => {
			if (mounted.has(el)) return;

			try {
				const cleanup = setup(el);
				mounted.set(el, typeof cleanup === 'function' ? cleanup : null);
			} catch (error) {
				console.error(`[blocks] "${name}" failed to mount`, error);
			}
		});
	}
}

/**
 * Run cleanups for mounted blocks inside `root` (called before Barba removes a page).
 *
 * @param {ParentNode} root
 */
export function unmountBlocks(root = document) {
	for (const [el, cleanup] of mounted) {
		if (!root.contains(el)) continue;
		cleanup?.();
		mounted.delete(el);
	}
}

/**
 * Replace the global queue by a live registry.
 */
export function initBlocks() {
	const queue = Array.isArray(window.wpgBlocks) ? window.wpgBlocks : [];

	window.wpgBlocks = {
		push([name, setup]) {
			definitions.set(name, setup);
			mountBlocks(document, name);
		},
	};

	queue.forEach((entry) => window.wpgBlocks.push(entry));
}
