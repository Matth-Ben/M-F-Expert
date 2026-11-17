/**
 * `aria-current="page"` on the navigation links that point at the current URL (query string and hash ignored).
 *
 * The first page load gets it from PHP (parts/components/menu.php). This keeps it right
 * - after a Barba navigation: header and footer live outside the Barba container and are not re-rendered;
 * - in the mobile menu panel: its HTML is a REST fragment, fetched once and cached (src/scripts/fragments/).
 */
const NAV_LINKS = 'header nav a[href], footer nav a[href], #panel nav a[href]';

const clean = (url: string): string => url.replace(/[#?].*$/, '').replace(/\/$/, '');

export function syncCurrentLinks(): void {
	const here = clean(window.location.href);

	document.querySelectorAll<HTMLAnchorElement>(NAV_LINKS).forEach((link) => {
		if (clean(link.href) === here) {
			link.setAttribute('aria-current', 'page');
		} else {
			link.removeAttribute('aria-current');
		}
	});
}
