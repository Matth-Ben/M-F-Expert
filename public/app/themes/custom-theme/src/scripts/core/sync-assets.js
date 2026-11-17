/**
 * Barba + per-block assets.
 *
 * WordPress only prints a block's CSS/JS on pages that contain the block, and some inline styles
 * depend on the page (global styles of the blocks present, layout `wp-container-*` rules…).
 * Barba swaps the page content without reloading <head>, so this copies what the next page needs:
 * - stylesheets and inline styles that are not in the document yet;
 * - for an inline style that exists with other content: the next page's rules are ADDED next to it
 *   (never replaced: the rules of the pages already seen stay valid and are harmless);
 * - block view modules (viewScriptModule).
 *
 * Must be called once the next page has been fetched (Barba `beforeEnter`: `data.next.html`).
 *
 * @param {string|undefined} html Full HTML of the next page.
 * @returns {Promise<void>} Resolves when new stylesheets are loaded (avoids a flash of unstyled content).
 */
const appended = new Map(); // style id => Set of CSS texts already added for that id

export function syncAssets(html) {
	if (!html) {
		return Promise.resolve();
	}

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

	next.querySelectorAll('style[id]').forEach((style) => {
		const css = style.textContent;
		const current = document.getElementById(style.id);

		if (!current) {
			const clone = document.createElement('style');
			clone.id = style.id;
			clone.textContent = css;
			document.head.append(clone);
			return;
		}

		const seen = appended.get(style.id) ?? new Set();
		if (!css.trim() || current.textContent === css || seen.has(css)) return;

		seen.add(css);
		appended.set(style.id, seen);

		const extra = document.createElement('style');
		extra.dataset.syncOf = style.id;
		extra.textContent = css;
		current.after(extra); // Same place in the cascade as the original.
	});

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
