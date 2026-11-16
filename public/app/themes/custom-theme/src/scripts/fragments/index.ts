/**
 * Fragments Module
 * 
 * Entry point for fragment loading functionality.
 */

import { initFragmentLoader, getFragmentLoader } from './fragmentLoader';
import { initPanelManager, getPanelManager } from './PanelManager';

/**
 * Initialize fragments system
 */
export function initFragments(): void {
	// Check if config is available
	if (typeof window.WPG_FRAGMENTS === 'undefined') {
		console.error('[Fragments] WPG_FRAGMENTS config not found. Fragment system disabled.');
		return;
	}

	try {
		// Initialize fragment loader
		initFragmentLoader(window.WPG_FRAGMENTS);
		console.log('[Fragments] FragmentLoader initialized');

		// Initialize panel manager
		initPanelManager();
		console.log('[Fragments] PanelManager initialized');

	} catch (error) {
		console.error('[Fragments] Initialization error:', error);
	}
}

// Export for external use
export { getFragmentLoader, loadFragment } from './fragmentLoader';
export { getPanelManager } from './PanelManager';

// TypeScript declaration for global config
declare global {
	interface Window {
		WPG_FRAGMENTS: {
			endpoint: string;
			nonce: string;
		};
	}
}
