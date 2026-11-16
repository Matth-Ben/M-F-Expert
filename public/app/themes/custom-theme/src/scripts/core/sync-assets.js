/**
 * Barba + per-block assets.
 *
 * WordPress only prints a block's CSS/JS on pages that contain the block.
 * Barba swaps the page content without reloading <head>, so when navigating to a page
 * using a block that wasn't on the first page, its assets would be missing.
 * This copies the stylesheets / inline styles / script modules of the next page
 * that are not in the current document yet.
 *
 * @param {string} html Full HTML of the next page (Barba `data.next.html`).
 * @returns {Promise<void>} Resolves when new stylesheets are loaded (avoids a flash).
 */
export function syncAssets(html) {
	const next = new DOMParser().parseFromString(html, 'text/html');
	const pending = [];

	next.querySelectorAll('link[rel="stylesheet"][id]').forEach((link) => {
		if (document.getElementById(link.id)) return;

		const clone = document.createElement('link');
		clone.rel = 'stylesheet';
		clone.id = link.id;
		clone.href = link.href;
		if (link.media) clone.media = link.media;

		pending.push(
			new Promise((resolve) => {
				clone.onload = clone.onerror = resolve;
			})
		);
		document.head.append(clone);
	});

	// Inline block styles (small block CSS is inlined by WP) and global-styles,
	// whose content depends on the blocks present on the page.
	next.querySelectorAll('style[id]').forEach((style) => {
		const current = document.getElementById(style.id);

		if (!current) {
			const clone = document.createElement('style');
			clone.id = style.id;
			clone.textContent = style.textContent;
			document.head.append(clone);
		} else if (current.textContent !== style.textContent && current.textContent.length < style.textContent.length) {
			current.textContent = style.textContent;
		}
	});

	// Block view modules (viewScriptModule). They register in window.wpgBlocks once loaded.
	next.querySelectorAll('script[type="module"][src][id]').forEach((script) => {
		if (document.getElementById(script.id)) return;

		const clone = document.createElement('script');
		clone.type = 'module';
		clone.id = script.id;
		clone.src = script.src;
		document.body.append(clone);
	});

	return Promise.all(pending).then(() => undefined);
}
