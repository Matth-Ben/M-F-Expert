/**
 * Header Scroll Animation (Headroom)
 * 
 * Behavior:
 * - 0-100vh: Header is absolute (standard flow).
 * - >100vh: Header becomes fixed (Headroom effect).
 * - Scroll Up (<100vh): Header reverts to absolute.
 */
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

// Register GSAP plugin
gsap.registerPlugin(ScrollTrigger);

let scrollTriggerInstance;

/**
 * Initialize header scroll animation
 */
export const initHeaderScroll = () => {
    // Cleanup existing instance
    cleanup();

    const header = document.querySelector('.headroom');
    if (!header) return;

    // Initial State: Absolute, Top
    gsap.set(header, {
        position: 'absolute',
        top: 0,
        yPercent: 0,
        backgroundColor: 'rgba(255, 255, 255, 0.9)' // Default
    });

    let isFixed = false;

    scrollTriggerInstance = ScrollTrigger.create({
        start: 0,
        end: "max",
        onUpdate: (self) => {
            const scrollY = self.scroll();
            const viewportHeight = window.innerHeight;

            // ZONE 1: 0 - 100vh
            if (scrollY < viewportHeight) {
                if (isFixed) {
                    // We just re-entered the top zone from below
                    isFixed = false;

                    // Animate out upwards before resetting
                    gsap.to(header, {
                        yPercent: -100,
                        duration: 0.3,
                        ease: "power3.out",
                        overwrite: true,
                        onComplete: () => {
                            // Reset to absolute once hidden
                            gsap.set(header, {
                                position: 'absolute',
                                top: 0,
                                yPercent: 0
                            });
                        }
                    });
                } else {
                    // Ensure absolute if not animating
                    if (!gsap.isTweening(header) && header.style.position !== 'absolute') {
                        gsap.set(header, {
                            position: 'absolute',
                            top: 0,
                            yPercent: 0
                        });
                    }
                }
                return;
            }

            // ZONE 2: > 100vh
            if (!isFixed) {
                // Entering Fixed Zone
                isFixed = true;

                // Immediately set to fixed but hidden (yPercent -100)
                // This prevents it from flashing visible at the top before animating out if scrolling down
                gsap.set(header, {
                    position: 'fixed',
                    top: 0,
                    yPercent: -100
                });
            }

            // Headroom Logic in Zone 2
            if (self.direction === 1) { // Scrolling Down
                gsap.to(header, {
                    yPercent: -100,
                    duration: 0.3,
                    ease: "power3.out",
                    overwrite: true
                });
            } else { // Scrolling Up
                gsap.to(header, {
                    yPercent: 0,
                    duration: 0.3,
                    ease: "power3.out",
                    overwrite: true
                });
            }
        }
    });
};

/**
 * Cleanup
 */
function cleanup() {
    if (scrollTriggerInstance) {
        scrollTriggerInstance.kill();
        scrollTriggerInstance = null;
    }

    // Reset header styles if needed
    const header = document.querySelector('.site-header');
    if (header) {
        gsap.set(header, { clearProps: "all" });
    }
}

export const cleanupHeaderScroll = cleanup;

// Make available globally
if (typeof window !== 'undefined') {
    window.initHeaderScroll = initHeaderScroll;
}
