/**
 * Global TypeScript declarations for the WP Gutenberg theme.
 */

type BlockSetup = (el: HTMLElement) => void | (() => void);

declare global {
	interface Window {
		/** Printed inline by includes/Core/Assets.php. */
		wpg: {
			homeUrl: string;
			apiUrl: string;
			textDomain: string;
			debug: boolean;
		};
		/** Fragments REST config (includes/Core/Assets.php). */
		WPG_FRAGMENTS: {
			endpoint: string;
			nonce: string;
		};
		/** Block view modules register here (see src/scripts/core/blocks.js). */
		wpgBlocks: Array<[string, BlockSetup]> | { push(entry: [string, BlockSetup]): void };
	}
}

export {};
