/**
 * Barba.js Page Transitions with GSAP
 * Handles smooth page transitions with GSAP animations
 */

import barba from '@barba/core';
import gsap from 'gsap';
import { resetScroll } from './lenis';
import { mountBlocks, unmountBlocks } from '../core/blocks';
import { syncAssets } from '../core/sync-assets';

export const initBarba = () => {
	barba.init({
		cacheFirstPage: true,
		debug: false,
		preventRunning: true,
		timeout: 5000,
		// Never hijack admin links (admin bar, edit links…).
		prevent: ({ el }) => Boolean(el.closest('#wpadminbar')) || el.href.includes('/wp-admin') || el.href.includes('/wp-login'),
		transitions: [
			{
				name: 'opacity-transition',
				leave(data) {
					unmountBlocks(data.current.container);

					const wrapper = data.current.container.parentNode;
					wrapper.style.height = `${wrapper.offsetHeight}px`;

					return Promise.all([
						syncAssets(data.next.html),
						gsap.to(data.current.container, {
							opacity: 0,
							duration: 0.3,
							ease: 'power3.out',
							onStart: () => {
								gsap.set(data.current.container, {
									position: 'absolute',
									top: 0,
									left: 0,
									width: '100%',
								});
							},
						}),
					]);
				},
				enter(data) {
					// Unlock the wrapper height so it can adapt to the new content
					const wrapper = data.next.container.parentNode;
					wrapper.style.height = '';

					// Reset scroll via Lenis
					resetScroll();

					mountBlocks(data.next.container);

					return gsap.from(data.next.container, {
						opacity: 0,
						duration: 0.3,
						ease: 'power3.out',
						clearProps: 'all',
					});
				},
				after() {
					if (typeof window.initFragments === 'function') {
						window.initFragments();
					}

					// Re-initialize text animations
					if (typeof window.initTextAnimations === 'function') {
						window.initTextAnimations();
					}

					// Re-initialize parallax animations
					if (typeof window.initParallax === 'function') {
						window.initParallax();
					}

					// Re-initialize header scroll
					if (typeof window.initHeaderScroll === 'function') {
						window.initHeaderScroll();
					}
				},
			},
		],
	});
};
