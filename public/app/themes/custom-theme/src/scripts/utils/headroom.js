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
	header.addEventListener('focusin', pinHeader);
	update();
}
