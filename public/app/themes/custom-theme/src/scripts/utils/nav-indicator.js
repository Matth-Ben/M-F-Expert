/**
 * Sliding underline under the current header link (`.site-nav`): on navigation it glides from the
 * old page's link to the new one instead of jumping. `aria-current="page"` stays the source of truth
 * (src/scripts/utils/current-links.ts); without JS the static underline of components/site.css applies.
 */
let list;
let wrap;
let indicator;

const clean = (url) => url.replace(/[#?].*$/, '').replace(/\/$/, '');

const place = (link, animate = true) => {
	if (!indicator) return;

	if (!link || !link.offsetWidth) {
		indicator.style.opacity = '0';
		return;
	}

	const box = link.getBoundingClientRect();
	const ref = wrap.getBoundingClientRect();

	indicator.classList.toggle('is-instant', !animate);
	indicator.style.width = `${box.width}px`;
	indicator.style.transform = `translateX(${box.left - ref.left}px)`;
	indicator.style.opacity = '1';

	if (!animate) {
		void indicator.offsetWidth; // Apply the position before re-enabling transitions.
		indicator.classList.remove('is-instant');
	}
};

/** Move to the header link pointing at `href` (called as soon as a navigation starts). */
export const moveNavIndicator = (href) => {
	const link = [...(list?.querySelectorAll('a[href]') ?? [])].find((a) => clean(a.href) === clean(href));
	place(link);
};

/** Settle on the link carrying aria-current (after a navigation, on resize). */
export const syncNavIndicator = (animate = true) => place(list?.querySelector('a[aria-current="page"]'), animate);

export function initNavIndicator() {
	list = document.querySelector('header .site-nav');
	wrap = list?.parentElement;
	if (!list || !wrap) return;

	indicator = document.createElement('span');
	indicator.className = 'site-nav__indicator';
	indicator.setAttribute('aria-hidden', 'true');

	wrap.classList.add('site-nav-wrap');
	list.classList.add('has-indicator');
	wrap.append(indicator);

	syncNavIndicator(false);
	document.fonts?.ready.then(() => syncNavIndicator(false));
	new ResizeObserver(() => syncNavIndicator(false)).observe(list);
}
