/**
 * Headroom header (parts/site-header.php): the navigation stays at hand while scrolling.
 *
 * - sticky (motion.css): the announcement bar scrolls away, the navigation row sticks to the top;
 * - hidden while scrolling down, back as soon as the visitor scrolls up, focuses it or navigates;
 * - `is-scrolled`: background + shadow once the page has moved, so the content never shows through.
 *
 * Exposes two CSS variables: `--announcement-h` (header) and `--header-h` (<html>). The latter feeds
 * `scroll-padding-top` so a focused element or an anchor target never ends up under the header (WCAG 2.4.11).
 */
import { getLenis } from './lenis';

const TOLERANCE = 8; // px of scroll before changing state (ignores trackpad jitter)

let header;
let announcement;
let lastY = 0;
let frame = 0;

const measure = () => {
	const bar = announcement?.offsetHeight ?? 0;
	header.style.setProperty('--announcement-h', `${bar}px`);
	document.documentElement.style.setProperty('--header-h', `${header.offsetHeight - bar}px`);
};

const update = () => {
	frame = 0;
	const y = Math.max(window.scrollY, 0);

	header.classList.toggle('is-scrolled', y > (announcement?.offsetHeight ?? 0) + 2);

	if (Math.abs(y - lastY) < TOLERANCE) return;

	const down = y > lastY;
	lastY = y;

	if (down && y > header.offsetHeight * 2 && !header.contains(document.activeElement)) {
		header.classList.add('is-unpinned');
	} else if (!down) {
		header.classList.remove('is-unpinned');
	}
};

/**
 * Keyboard focus never hidden by the fixed mobile bar « Appeler / Chat » (.quick-bar, WCAG 2.4.11):
 * browsers do not scroll an element that is already partly on screen, so `scroll-padding-bottom` is not enough.
 */
const scrollToImmediate = (y) => {
	const lenis = getLenis();
	if (lenis) {
		lenis.scrollTo(y, { immediate: true, force: true });
	} else {
		window.scrollTo(0, y);
	}
};

/**
 * Focus entering the header while it is hidden (Tab, Shift+Tab, focus given back by the menu panel):
 * the browser scrolls the page to reach the hidden link. Show the header and put the page back
 * where it was, in the same frame (before paint, so nothing jumps).
 */
const onHeaderFocus = () => {
	const wasHidden = header.classList.contains('is-unpinned');
	pinHeader();
	if (!wasHidden) return;

	const y = lastY; // Last position seen before the focus scroll.
	requestAnimationFrame(() => {
		if (Math.abs(window.scrollY - y) > 1) {
			scrollToImmediate(y);
			lastY = y;
		}
	});
};

const keepFocusAboveBar = (event) => {
	const bar = document.querySelector('.quick-bar');
	const el = event.target;
	if (!bar?.offsetHeight || !(el instanceof Element) || bar.contains(el) || header?.contains(el)) return;

	const box = el.getBoundingClientRect();
	const limit = bar.getBoundingClientRect().top - 16;
	if (box.bottom <= limit || box.bottom <= 0) return;

	// Scroll just enough, without pushing the top of a tall element (textarea) out of view.
	const delta = Math.min(box.bottom - limit, Math.max(box.top - 16, 0));
	if (delta <= 0) return;

	scrollToImmediate(window.scrollY + delta);
};

/** Show the header (navigation, focus inside it). */
export const pinHeader = () => header?.classList.remove('is-unpinned');

export function initHeadroom() {
	header = document.querySelector('.site-header');
	if (!header) return;

	const first = header.firstElementChild;
	announcement = first && !first.classList.contains('container') ? first : null;

	header.classList.add('is-headroom');
	measure();
	new ResizeObserver(measure).observe(header);

	window.addEventListener(
		'scroll',
		() => {
			frame ||= requestAnimationFrame(update);
		},
		{ passive: true }
	);
	header.addEventListener('focusin', onHeaderFocus);
	document.addEventListener('focusin', keepFocusAboveBar);
	update();
}
