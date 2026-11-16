/**
 * Parallax Animations
 * 
 * Applies parallax effect to elements using GSAP ScrollTrigger
 */
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

// Register GSAP plugin
gsap.registerPlugin(ScrollTrigger);

// Store active animations for cleanup
const activeAnimations = [];

/**
 * Initialize parallax animations
 */
export const initParallax = () => {
    // Clean up previous animations
    cleanup();

    // Find all elements with data-parallax attribute
    const elements = document.querySelectorAll('[data-parallax]');

    if (elements.length === 0) {
        return;
    }

    elements.forEach(element => {
        const speed = parseFloat(element.dataset.speed) || 0.5;
        const trigger = element.dataset.trigger || element; // Can be a selector string

        // Create the parallax animation
        // We use yPercent for more relative and responsive movement
        const animation = gsap.fromTo(element,
            {
                yPercent: -10 * speed
            },
            {
                yPercent: 10 * speed,
                ease: "none",
                scrollTrigger: {
                    trigger: trigger,
                    start: "top bottom",
                    end: "bottom top",
                    scrub: 0, // Instant scrub, let Lenis handle the smoothing
                }
            }
        );

        // Store for cleanup
        activeAnimations.push(animation);
    });
};

/**
 * Cleanup animations
 */
function cleanup() {
    // Kill all active animations
    activeAnimations.forEach(animation => {
        if (animation.scrollTrigger) {
            animation.scrollTrigger.kill();
        }
        animation.kill();
    });
    activeAnimations.length = 0;
}

// Export cleanup for external use
export const cleanupParallax = cleanup;

// Make available globally for Barba.js
if (typeof window !== 'undefined') {
    window.initParallax = initParallax;
}
