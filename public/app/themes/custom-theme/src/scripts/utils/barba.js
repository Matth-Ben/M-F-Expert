/**
 * Page transitions (Barba.js + GSAP).
 *
 * A beige curtain covers the content and the footer (never the header, nor the fixed contact bar):
 * the old page fades behind it, the new one is swapped, styled and scrolled while hidden, then the
 * curtain lifts and the new content rises in (src/scripts/utils/reveal.js). The header underline glides
 * to the new link as soon as the navigation starts. Back / forward restore the previous scroll position.
 * prefers-reduced-motion: instant swap.
 */
import barba from '@barba/core';
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { getLenis, resetScroll } from './lenis';
import { mountBlocks, unmountBlocks } from '../core/blocks';
import { syncAssets } from '../core/sync-assets';
import { getPanelManagerIfReady } from '../fragments/PanelManager';
import { syncCurrentLinks } from './current-links';
import { pinHeader } from './headroom';
import { moveNavIndicator, syncNavIndicator } from './nav-indicator';
import { destroyReveal, initReveal } from './reveal';

const reduced = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const scrollPositions = new Map(); // URL => scrollY, for back / forward

let curtain;

/** The curtain, created once. A loader appears only if the next page is slow to come. */
const getCurtain = () => {
	if (!curtain) {
		curtain = document.createElement('div');
		curtain.className = 'page-curtain';
		curtain.setAttribute('aria-hidden', 'true');
		curtain.innerHTML = '<span class="page-curtain__loader"><i></i><i></i><i></i></span>';
		document.body.append(curtain);
	}
	return curtain;
};

const scrollToY = (y) => {
	const lenis = getLenis();
	if (lenis) {
		lenis.resize();
		lenis.scrollTo(y, { immediate: true, force: true });
	} else {
		window.scrollTo(0, y);
	}
};

/**
 * After a Barba navigation the focus would stay on the clicked link (header) or fall back to <body>:
 * move it to the new page's <h1> (fallback: <main>), so keyboard and screen reader users start
 * at the new content and hear its title (WCAG 2.4.3). No ring: base.css hides it on these targets.
 */
const focusNewPage = (container) => {
	const target = container.querySelector('h1') ?? document.getElementById('content');
	if (!target) return;

	if (!target.hasAttribute('tabindex')) {
		target.setAttribute('tabindex', '-1');
	}
	target.focus({ preventScroll: true });
};

/** Fetch internal pages on hover / focus / touch, so the transition starts with the page ready. */
const prefetchOnIntent = () => {
	const done = new Set();
	const prefetch = (event) => {
		const link = event.target.closest?.('a[href]');
		if (!link || done.has(link.href) || typeof barba.prefetch !== 'function') return;
		if (link.origin !== location.origin || link.target || link.hasAttribute('download') || link.pathname.includes('/wp-') || link.hash && link.pathname === location.pathname) return;
		done.add(link.href);
		barba.prefetch(link.href);
	};
	['mouseover', 'focusin', 'touchstart'].forEach((type) => document.addEventListener(type, prefetch, { passive: true }));
};

export const initBarba = () => {
	// Scroll positions are restored by this file (back / forward), never by the browser: with « auto » it would
	// jump on the OLD page as soon as Back is pressed. GSAP ScrollTrigger (Lenis, parallax) remembers the value
	// it found at load and writes it back on every refresh: set it through ScrollTrigger so « manual » sticks.
	gsap.registerPlugin(ScrollTrigger);
	ScrollTrigger.clearScrollMemory('manual');

	barba.init({
		cacheFirstPage: true,
		debug: false,
		preventRunning: true,
		timeout: 8000,
		// Never hijack admin links (admin bar, edit links…).
		prevent: ({ el }) => Boolean(el.closest('#wpadminbar')) || el.href.includes('/wp-admin') || el.href.includes('/wp-login'),
		transitions: [
			{
				name: 'curtain',
				leave(data) {
					// A navigation link inside the panel: close it first, or the new page would stay inert.
					getPanelManagerIfReady()?.closeForNavigation();
					scrollPositions.set(data.current.url.href, window.scrollY);
					document.documentElement.classList.add('is-navigating');
					document.getElementById('content')?.setAttribute('aria-busy', 'true');
					pinHeader();
					moveNavIndicator(data.next.url.href);
					destroyReveal();
					unmountBlocks(data.current.container);

					return gsap.to(getCurtain(), { autoAlpha: 1, duration: reduced() ? 0 : 0.35, ease: 'power2.inOut' });
				},
				async beforeEnter(data) {
					// Barba (async mode) adds the new container next to the old one and removes the old one only
					// after `enter`: take the old page out of the flow now, so scrolling and the curtain lifting
					// only ever deal with the new page.
					data.current.container.style.display = 'none';

					// The next page is fetched now: copy the CSS / scripts its blocks need, before showing it.
					await syncAssets(data.next.html);

					// History navigation: Barba sets the trigger to 'back', 'forward' or 'popstate' (a link gives its element).
					const back = ['back', 'forward', 'popstate'].includes(data.trigger);
					if (back && scrollPositions.has(data.next.url.href)) {
						scrollToY(scrollPositions.get(data.next.url.href));
					} else {
						resetScroll();
					}
				},
				enter(data) {
					mountBlocks(data.next.container);
					initReveal(data.next.container, { all: true });

					return gsap.to(getCurtain(), { autoAlpha: 0, duration: reduced() ? 0 : 0.45, ease: 'power2.out' });
				},
				after(data) {
					document.documentElement.classList.remove('is-navigating');
					document.getElementById('content')?.removeAttribute('aria-busy');

					if (typeof window.initFragments === 'function') {
						window.initFragments();
					}
					if (typeof window.initTextAnimations === 'function') {
						window.initTextAnimations();
					}
					if (typeof window.initParallax === 'function') {
						window.initParallax();
					}

					// Header/footer live outside the Barba container: refresh their « current page » link.
					syncCurrentLinks();
					syncNavIndicator();

					focusNewPage(data.next.container);
				},
			},
		],
	});

	prefetchOnIntent();
};
