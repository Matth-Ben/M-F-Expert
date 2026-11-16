/**
 * Panel Manager
 * 
 * Manages panel opening/closing without animations.
 * Handles loading fragments and injecting them into the panel shell.
 * Creates the panel DOM structure on first use.
 */

import { loadFragment } from './fragmentLoader';

export interface PanelElements {
	panel: HTMLElement;
	overlay: HTMLElement;
	content: HTMLElement;
}

export class PanelManager {
	private elements: PanelElements | null = null;
	private currentPanelKey: string | null = null;
	private isOpen: boolean = false;

	constructor() {
		this.init();
	}

	/**
	 * Initialize panel manager
	 */
	private init(): void {
		// Wait for DOM
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', () => this.setup());
		} else {
			this.setup();
		}
	}

	/**
	 * Setup event listeners (panel created on first use)
	 */
	private setup(): void {
		this.setupEventListeners();
	}

	/**
	 * Create panel shell in DOM
	 */
	private createPanelShell(): void {
		if (this.elements) {
			return; // Already created
		}

		// Create overlay
		const overlay = document.createElement('div');
		overlay.id = 'panelOverlay';
		overlay.className = 'panel-overlay';
		overlay.setAttribute('aria-hidden', 'true');

		// Create panel
		const panel = document.createElement('aside');
		panel.id = 'panel';
		panel.className = 'panel';
		panel.setAttribute('aria-hidden', 'true');
		panel.setAttribute('role', 'dialog');
		panel.setAttribute('aria-modal', 'true');

		// Create panel header
		const header = document.createElement('div');
		header.className = 'panel__header';

		const closeButton = document.createElement('button');
		closeButton.type = 'button';
		closeButton.setAttribute('data-panel-close', '');
		closeButton.className = 'panel__close';
		closeButton.setAttribute('aria-label', 'Close panel');
		
		const closeIcon = document.createElement('span');
		closeIcon.setAttribute('aria-hidden', 'true');
		closeIcon.textContent = '×';
		closeButton.appendChild(closeIcon);

		header.appendChild(closeButton);

		// Create panel content
		const content = document.createElement('div');
		content.id = 'panelContent';
		content.className = 'panel__content';

		// Assemble
		panel.appendChild(header);
		panel.appendChild(content);

		// Append to body
		document.body.appendChild(overlay);
		document.body.appendChild(panel);

		// Store elements
		this.elements = { panel, overlay, content };
	}

	/**
	 * Setup event listeners
	 */
	private setupEventListeners(): void {
		// Listen for panel open triggers
		document.addEventListener('click', (e) => {
			const target = e.target as HTMLElement;
			const trigger = target.closest('[data-panel-open]') as HTMLElement;

			if (trigger) {
				e.preventDefault();
				const panelKey = trigger.dataset.panelOpen;
				if (panelKey) {
					this.open(panelKey);
				}
			}
		});

		// Listen for panel close triggers
		document.addEventListener('click', (e) => {
			const target = e.target as HTMLElement;
			
			// Close button
			if (target.closest('[data-panel-close]')) {
				e.preventDefault();
				this.close();
				return;
			}

			// Overlay click
			if (this.elements && target === this.elements.overlay) {
				e.preventDefault();
				this.close();
			}
		});

		// ESC key to close
		document.addEventListener('keydown', (e) => {
			if (e.key === 'Escape' && this.isOpen) {
				this.close();
			}
		});
	}

	/**
	 * Open a panel
	 */
	async open(key: string): Promise<void> {
		// Create panel shell on first use
		if (!this.elements) {
			this.createPanelShell();
		}

		if (!this.elements) {
			console.error('[PanelManager] Failed to create panel elements');
			return;
		}

		try {
			// Load fragment
			const response = await loadFragment({
				type: 'panel',
				key,
			});

			if (!response.ok || !response.html) {
				throw new Error('Invalid fragment response');
			}

			// Inject HTML
			this.elements.content.innerHTML = response.html;

			// Update state
			this.currentPanelKey = key;
			this.isOpen = true;

			// Show panel
			this.showPanel();

		} catch (error) {
			console.error('[PanelManager] Error opening panel:', error);
			// Optionally show error message to user
			this.elements.content.innerHTML = `
				<div class="panel-error">
					<p>Unable to load panel content.</p>
				</div>
			`;
			this.showPanel();
		}
	}

	/**
	 * Close the panel
	 */
	close(): void {
		if (!this.elements || !this.isOpen) {
			return;
		}

		// Hide panel
		this.hidePanel();

		// Update state
		this.isOpen = false;
		this.currentPanelKey = null;
	}

	/**
	 * Show panel (no animation)
	 */
	private showPanel(): void {
		if (!this.elements) return;

		const { panel, overlay } = this.elements;

		// Remove hidden classes/attributes
		panel.classList.add('is-open');
		overlay.classList.add('is-visible');
		panel.setAttribute('aria-hidden', 'false');
		
		// Trap focus
		const firstFocusable = panel.querySelector<HTMLElement>(
			'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
		);
		firstFocusable?.focus();

		// Prevent body scroll
		document.body.style.overflow = 'hidden';
	}

	/**
	 * Hide panel (no animation)
	 */
	private hidePanel(): void {
		if (!this.elements) return;

		const { panel, overlay } = this.elements;

		// Add hidden classes/attributes
		panel.classList.remove('is-open');
		overlay.classList.remove('is-visible');
		panel.setAttribute('aria-hidden', 'true');

		// Restore body scroll
		document.body.style.overflow = '';
	}

	/**
	 * Check if panel is open
	 */
	isOpened(): boolean {
		return this.isOpen;
	}

	/**
	 * Get current panel key
	 */
	getCurrentKey(): string | null {
		return this.currentPanelKey;
	}
}

// Global instance
let panelManagerInstance: PanelManager | null = null;

/**
 * Initialize the panel manager
 */
export function initPanelManager(): PanelManager {
	if (!panelManagerInstance) {
		panelManagerInstance = new PanelManager();
	}
	return panelManagerInstance;
}

/**
 * Get the panel manager instance
 */
export function getPanelManager(): PanelManager {
	if (!panelManagerInstance) {
		throw new Error('PanelManager not initialized. Call initPanelManager() first.');
	}
	return panelManagerInstance;
}
