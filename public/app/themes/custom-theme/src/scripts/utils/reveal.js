/**
 * Scroll reveal: content fades in and rises slightly as it enters the viewport (front only).
 *
 * - First page load: only what is below the fold is prepared, so nothing visible ever blinks
 *   and the hero (LCP) is painted at once.
 * - After a Barba navigation the new page is behind the curtain: everything is prepared and the
 *   visible part plays while the curtain lifts.
 * - Items are the building blocks of each section (title, text, buttons, image…); the items of a
 *   grid or list (cards, steps, questions) are revealed one by one, staggered per batch on screen.
 * - Nothing is hidden without JS or with prefers-reduced-motion. Classes are removed once played,
 *   so the elements' own transitions (hover) keep working.
 */
const STAGGER = 0.08; // s between items revealed together
const MAX_STAGGER = 6;
const LEAF = 'p, h1, h2, h3, h4, a, button, img, figure, form, details, blockquote, nav, .wp-block-buttons, .btn, .badge-new';

let observer;

const reduced = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

const isVisible = (el) => el.getClientRects().length > 0 && getComputedStyle(el).position !== 'absolute';

/** Grid / list / row of several items: reveal its children one by one. */
const isGroup = (el) => {
	if (el.children.length < 2 || el.matches(LEAF)) return false;
	const style = getComputedStyle(el);
	return el.matches('ul, ol') || style.display === 'grid' || (style.display === 'flex' && !style.flexDirection.startsWith('column'));
};

/** Plain wrapper (container, column…): look inside. */
const isWrapper = (el) =>
	el.children.length > 0 &&
	!el.matches(LEAF) &&
	![...el.childNodes].some((node) => node.nodeType === Node.TEXT_NODE && node.textContent.trim());

const collect = (root, items, depth = 0) => {
	for (const el of root.children) {
		if (!isVisible(el)) continue;

		if (depth < 4 && isGroup(el)) {
			[...el.children].filter(isVisible).forEach((child) => items.push(child));
		} else if (depth < 4 && isWrapper(el)) {
			collect(el, items, depth + 1);
		} else {
			items.push(el);
		}
	}
};

const settle = (el) => {
	el.classList.remove('reveal', 'is-revealed');
	el.style.removeProperty('--reveal-delay');
};

const onIntersect = (entries) => {
	entries
		.filter((entry) => entry.isIntersecting)
		.map((entry) => entry.target)
		.sort((a, b) => (a.compareDocumentPosition(b) & Node.DOCUMENT_POSITION_FOLLOWING ? -1 : 1))
		.forEach((el, index) => {
			observer?.unobserve(el);
			el.style.setProperty('--reveal-delay', `${Math.min(index, MAX_STAGGER) * STAGGER}s`);
			el.classList.add('is-revealed');

			const done = (event) => {
				if (event && (event.target !== el || event.propertyName !== 'opacity')) return;
				el.removeEventListener('transitionend', done);
				settle(el);
			};
			el.addEventListener('transitionend', done);
			setTimeout(done, 2000); // Safety net (tab in background, transition interrupted).
		});
};

/** Stop observing (Barba leave). Elements not yet revealed are left as they are: the page is going away. */
export function destroyReveal() {
	observer?.disconnect();
	observer = null;
}

/**
 * @param {ParentNode} scope         Page container.
 * @param {Object}     [options]
 * @param {boolean}    [options.all] Also prepare what is already on screen (content hidden behind the curtain).
 */
export function initReveal(scope = document, { all = false } = {}) {
	destroyReveal();
	if (reduced() || !('IntersectionObserver' in window)) return;

	const content = scope.querySelector('.entry-content');
	if (!content) return;

	const items = [];
	collect(content, items);

	observer = new IntersectionObserver(onIntersect, { rootMargin: '0px 0px -8% 0px', threshold: 0.1 });

	const fold = window.innerHeight;
	items.forEach((el) => {
		if (!all && el.getBoundingClientRect().top < fold) return; // Already visible: never hide it.
		el.classList.add('reveal');
		observer.observe(el);
	});
}
