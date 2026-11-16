/**
 * Global front-end entry (Vite).
 *
 * Block-specific JS does NOT go here: put it in blocks/<name>/view.js
 * (loaded only on pages that use the block) and register it in window.wpgBlocks.
 */

// Let Vite process every image so PHP can reference them via wpg_asset().
import.meta.glob('../img/**/*');

import { initBlocks } from './core/blocks';
import { initFragments } from './fragments/index';
import { initGridDebugger } from './utils/grid-debugger';
import { initBarba } from './utils/barba';
import { initLenis } from './utils/lenis';
import { initTextAnimations } from './utils/text-animations';
import { initParallax } from './utils/parallax';
import { initHeaderScroll } from './utils/header-scroll';

initBlocks();
initFragments();
initGridDebugger();
initBarba();
initLenis();
initTextAnimations();
initParallax();
initHeaderScroll();
