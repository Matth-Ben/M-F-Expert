/**
 * Stats — front-end behaviour (script module, loaded only on pages using the block).
 *
 * Pattern for any interactive block:
 *   1. render.php adds `data-wpg-block="<name>"` on the root element
 *   2. this file pushes [name, setup] into window.wpgBlocks
 *   3. setup(el) runs once per element (also after Barba navigations) and may return a cleanup.
 */
const DURATION = 1600;
const easeOutCubic = (t) => 1 - Math.pow(1 - t, 3);

function count(el) {
	const target = Number(el.dataset.countTo) || 0;
	const format = new Intl.NumberFormat(document.documentElement.lang || undefined);
	const start = performance.now();

	const tick = (now) => {
		const progress = Math.min((now - start) / DURATION, 1);
		el.textContent = format.format(Math.round(target * easeOutCubic(progress)));
		if (progress < 1) requestAnimationFrame(tick);
	};

	requestAnimationFrame(tick);
}

function setup(root) {
	if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

	const counters = root.querySelectorAll('[data-count-to]');
	const observer = new IntersectionObserver(
		(entries) => {
			entries.forEach((entry) => {
				if (!entry.isIntersecting) return;
				observer.unobserve(entry.target);
				count(entry.target);
			});
		},
		{ threshold: 0.5 }
	);

	counters.forEach((counter) => {
		counter.textContent = '0';
		observer.observe(counter);
	});

	return () => observer.disconnect();
}

(window.wpgBlocks ??= []).push(['stats', setup]);
