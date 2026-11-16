/**
 * Grid & Spacing Debugger
 * Toggles a visual grid overlay and spacing overlays when Shift+G is pressed.
 * Only works in debug mode.
 */

export const initGridDebugger = () => {
    // Check if wpg object and debug flag exist
    if (typeof window.wpg === 'undefined' || !window.wpg.debug) {
        return;
    }

    let isVisible = false;
    // Default columns: 12 for desktop, 8 for mobile (< 768px)
    let columnCount = window.innerWidth < 768 ? 8 : 12;

    // --- Helpers ---
    const getWrapper = () => document.querySelector('.wpg-debugger-wrapper');

    const createWrapper = () => {
        const wrapper = document.createElement('div');
        wrapper.className = 'wpg-debugger-wrapper';
        wrapper.style.position = 'absolute';
        wrapper.style.top = '0';
        wrapper.style.left = '0';
        wrapper.style.width = '100%';
        wrapper.style.height = '100%';
        wrapper.style.zIndex = '9999';
        wrapper.style.pointerEvents = 'none';
        wrapper.style.overflow = 'hidden'; // Prevent horizontal scrollbar issues
        return wrapper;
    };

    // --- UI Controls ---
    const createControls = () => {
        const controls = document.createElement('div');
        controls.className = 'grid-debugger-controls';
        Object.assign(controls.style, {
            position: 'fixed',
            bottom: '20px',
            right: '20px',
            zIndex: '10000',
            backgroundColor: 'white',
            border: '1px solid #ccc',
            borderRadius: '4px',
            padding: '8px',
            display: 'flex',
            gap: '8px',
            alignItems: 'center',
            boxShadow: '0 2px 10px rgba(0,0,0,0.1)',
            pointerEvents: 'auto', // Enable interaction
            fontFamily: 'sans-serif',
            fontSize: '14px'
        });

        const label = document.createElement('span');
        label.innerText = `Cols: ${columnCount}`;

        const decreaseBtn = document.createElement('button');
        decreaseBtn.innerText = '-';
        Object.assign(decreaseBtn.style, {
            cursor: 'pointer',
            padding: '2px 8px',
            border: '1px solid #ddd',
            borderRadius: '4px',
            backgroundColor: '#f5f5f5'
        });
        decreaseBtn.onclick = () => {
            if (columnCount > 1) {
                columnCount--;
                label.innerText = `Cols: ${columnCount}`;
                refreshDebugger();
            }
        };

        const increaseBtn = document.createElement('button');
        increaseBtn.innerText = '+';
        Object.assign(increaseBtn.style, {
            cursor: 'pointer',
            padding: '2px 8px',
            border: '1px solid #ddd',
            borderRadius: '4px',
            backgroundColor: '#f5f5f5'
        });
        increaseBtn.onclick = () => {
            columnCount++;
            label.innerText = `Cols: ${columnCount}`;
            refreshDebugger();
        };

        controls.appendChild(decreaseBtn);
        controls.appendChild(label);
        controls.appendChild(increaseBtn);

        return controls;
    };

    // --- Grid Logic ---
    const createGridLayer = () => {
        const gridLayer = document.createElement('div');
        gridLayer.className = 'grid-debugger-overlay';
        
        // Create inner container and columns
        const container = document.createElement('div');
        container.className = 'grid-debugger-container';
        
        // Dynamic grid columns
        container.style.gridTemplateColumns = `repeat(${columnCount}, 1fr)`;
        
        for (let i = 0; i < columnCount; i++) {
            const col = document.createElement('div');
            col.className = 'grid-debugger-col';
            container.appendChild(col);
        }
        
        gridLayer.appendChild(container);
        return gridLayer;
    };

    // --- Spacing Logic ---
    const createSpacingLayer = () => {
        const spacingLayer = document.createElement('div');
        spacingLayer.className = 'spacing-debugger-layer';
        spacingLayer.style.position = 'absolute';
        spacingLayer.style.top = '0';
        spacingLayer.style.left = '0';
        spacingLayer.style.width = '100%';
        spacingLayer.style.height = '100%';
        
        // Populate
        // Only target the main content area to avoid clutter
        const main = document.querySelector('main') || document.body;
        const elements = [main];
        
        for (let el of elements) {
            // Filter elements
            if (el.closest('.wpg-debugger-wrapper') || 
                ['SCRIPT', 'STYLE', 'LINK', 'META', 'HEAD', 'TITLE'].includes(el.tagName)) {
                continue;
            }

            const style = window.getComputedStyle(el);
            const paddingTop = parseFloat(style.paddingTop) ? parseFloat(style.paddingTop) : 24;
            const paddingBottom = parseFloat(style.paddingBottom) ? parseFloat(style.paddingBottom) : 24;

            if (paddingTop > 0 || paddingBottom > 0) {
                const rect = el.getBoundingClientRect();
                if (rect.height === 0) continue;

                if (paddingTop > 0) {
                    const overlay = document.createElement('div');
                    overlay.style.position = 'absolute';
                    overlay.style.left = '0';
                    overlay.style.width = '100%';
                    overlay.style.height = `${paddingTop}px`;
                    overlay.style.backgroundColor = 'rgba(255, 0, 255, 0.25)'; // Purple
                    overlay.style.top = '0'; // Absolute positioning implies scrollY included
                    spacingLayer.appendChild(overlay);
                }
                
                if (paddingBottom > 0) {
                    const overlay = document.createElement('div');
                    overlay.style.position = 'absolute';
                    overlay.style.left = '0';
                    overlay.style.width = '100%';
                    overlay.style.height = `${paddingBottom}px`;
                    overlay.style.backgroundColor = 'rgba(0, 255, 255, 0.25)'; // Cyan
                    overlay.style.bottom = '0';
                    spacingLayer.appendChild(overlay);
                }
            }
        }
        
        return spacingLayer;
    };

    // --- Main Toggle ---
    const refreshDebugger = () => {
        if (!isVisible) return;
        
        const wrapper = getWrapper();
        if (wrapper) {
            wrapper.innerHTML = '';
            wrapper.appendChild(createGridLayer());
            wrapper.appendChild(createSpacingLayer());
            wrapper.appendChild(createControls());
        }
    };

    const toggleDebugger = () => {
        isVisible = !isVisible;
        let wrapper = getWrapper();
        
        if (isVisible) {
            if (!wrapper) {
                wrapper = createWrapper();
                document.body.appendChild(wrapper);
            }
            
            refreshDebugger();
            
            // Update on resize
            window.addEventListener('resize', updateDebounced);
            document.addEventListener('scroll', updateDebounced);
            
        } else {
            if (wrapper) {
                wrapper.remove();
            }
            window.removeEventListener('resize', updateDebounced);
            document.removeEventListener('scroll', updateDebounced);
        }
    };

    // Debounce for performance and mobile breakpoint handling
    let timeout;
    const updateDebounced = () => {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            if (isVisible) {
                // Update column count based on viewport if needed?
                // The user asked "avoir la possibilité de changer", not auto-reset.
                // But generally "responsive" implies auto-change.
                // However, if user manually changed it, we shouldn't overwrite?
                // Let's implement simple responsive logic: if breakpoint changes, reset to default? 
                // Or just keep the current count. The user said "by default 8 on mobile".
                // Let's check if we crossed the breakpoint.
                
                // For now, let's just refresh layout. The column count persists unless manually changed or reload.
                refreshDebugger();
            }
        }, 100);
    };

    // Listen for Shift + G
    document.addEventListener('keydown', (e) => {
        if (e.shiftKey && e.key.toLowerCase() === 'g') {
            toggleDebugger();
        }
    });

    console.log('Grid & Spacing Debugger initialized. Press Shift+G to toggle.');
};
