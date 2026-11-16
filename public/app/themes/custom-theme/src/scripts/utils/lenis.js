/**
 * Lenis Smooth Scroll
 * 
 * Provides smooth scrolling for the application.
 */
import Lenis from 'lenis';
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

let lenis;

export const initLenis = () => {
    lenis = new Lenis({
        lerp: 0.1,
        wheelMultiplier: 1,
        gestureOrientation: 'vertical',
        normalizeWheel: true,
        smoothTouch: false,
    });

    // Sync specific GSAP animations if needed (uncomment if ScrollTrigger is used)
    lenis.on('scroll', ScrollTrigger.update);
    gsap.ticker.add((time) => {
        lenis.raf(time * 1000);
    });
    gsap.ticker.lagSmoothing(0);

    // Main animation loop
    // function raf(time) {
    //    lenis.raf(time);
    //    requestAnimationFrame(raf);
    // }

    // requestAnimationFrame(raf);

    console.log('Lenis Smooth Scroll initialized.');

    window.lenis = lenis;

    return lenis;
};

export const getLenis = () => lenis;

export const resetScroll = (immediate = true) => {
    if (lenis) {
        lenis.scrollTo(0, { immediate: immediate });
    } else {
        window.scrollTo(0, 0);
    }
};

export const stopScroll = () => lenis && lenis.stop();
export const startScroll = () => lenis && lenis.start();
