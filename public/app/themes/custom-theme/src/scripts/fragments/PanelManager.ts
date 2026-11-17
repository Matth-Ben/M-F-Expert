/**
 * Panel Manager
 *
 * Accessible modal side panel (WAI-ARIA APG "Dialog (Modal)" pattern):
 * - `<div role="dialog" aria-modal="true">`, named by the fragment's first heading
 * - the rest of the page is made `inert` while open (focus can't leave the panel)
 * - Escape / close button / overlay close it, focus goes back to the trigger
 * - trigger buttons get `aria-expanded` / `aria-controls`
 * - closed without returning focus when Barba navigates away (closeForNavigation)
 *
 * Content is loaded as a REST fragment (see includes/Fragments/).
 */

import { loadFragment } from './fragmentLoader';
import { startScroll, stopScroll } from '../utils/lenis';
import { syncCurrentLinks } from '../utils/current-links';

export interface PanelElements {
	panel: HTMLElement;
	overlay: HTMLElement;
	content: HTMLElement;
}

const PANEL_ID = 'panel';
const FOCUSABLE = 'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';

/** Translated strings printed by includes/Core/Assets.php. */
const t = (key: 'closePanel' | 'panelLabel' | 'panelError'): string => window.wpg?.i18n?.[key] ?? key;

export class PanelManager {
	private elements: PanelElements | null = null;
	private currentPanelKey: string | null = null;
	private isOpen = false;
	private trigger: HTMLElement | null = null;
	private inertElements: HTMLElement[] = [];

	constructor() {
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', () => this.setupEventListeners());
		} else {
			this.setupEventListeners();
		}
	}

	/**
	 * Create the panel shell on first use.
	 */
	private createPanelShell(): PanelElements {
		if (this.elements) {
			return this.elements;
		}

		const overlay = document.createElement('div');
		overlay.className = 'panel-overlay';
		overlay.setAttribute('aria-hidden', 'true');

		const panel = document.createElement('div');
		panel.id = PANEL_ID;
		panel.className = 'panel';
		panel.setAttribute('role', 'dialog');
		panel.setAttribute('aria-modal', 'true');
		panel.setAttribute('aria-label', t('panelLabel'));
		panel.inert = true;

		const header = document.createElement('div');
		header.className = 'panel__header';

		const closeButton = document.createElement('button');
		closeButton.type = 'button';
		closeButton.className = 'panel__close';
		closeButton.setAttribute('data-panel-close', '');
		closeButton.setAttribute('aria-label', t('closePanel'));

		const closeIcon = document.createElement('span');
		closeIcon.setAttribute('aria-hidden', 'true');
		closeIcon.textContent = '×';
		closeButton.appendChild(closeIcon);
		header.appendChild(closeButton);

		const content = document.createElement('div');
		content.className = 'panel__content';

		panel.append(header, content);
		document.body.append(overlay, panel);

		this.elements = { panel, overlay, content };
		return this.elements;
	}

	private setupEventListeners(): void {
		document.addEventListener('click', (event) => {
			const target = event.target as HTMLElement;

			const trigger = target.closest<HTMLElement>('[data-panel-open]');
			if (trigger?.dataset.panelOpen) {
				event.preventDefault();
				this.open(trigger.dataset.panelOpen, trigger);
				return;
			}

			if (target.closest('[data-panel-close]') || (this.elements && target === this.elements.overlay)) {
				event.preventDefault();
				this.close();
			}
		});

		document.addEventListener('keydown', (event) => {
			if (event.key === 'Escape' && this.isOpen) {
				event.preventDefault();
				this.close();
			}
		});
	}

	/**
	 * Open a panel and load its fragment.
	 */
	async open(key: string, trigger: HTMLElement | null = null): Promise<void> {
		const { content } = this.createPanelShell();

		this.trigger = trigger ?? (document.activeElement as HTMLElement | null);

		try {
			const response = await loadFragment({ type: 'panel', key });

			if (!response.ok || !response.html) {
				throw new Error('Invalid fragment response');
			}

			content.innerHTML = response.html;
		} catch (error) {
			console.error('[PanelManager] Error opening panel:', error);
			content.innerHTML = '';
			const message = document.createElement('p');
			message.className = 'panel-error';
			message.setAttribute('role', 'alert');
			message.textContent = t('panelError');
			content.append(message);
		}

		this.currentPanelKey = key;
		this.labelFromHeading();
		// The fragment HTML is cached: mark the current page link each time it is inserted.
		syncCurrentLinks();
		this.show();
	}

	/**
	 * Close the panel and give focus back to the element that opened it.
	 */
	close(): void {
		if (!this.isOpen) return;

		this.hide();
		// No scroll: the trigger lives in the sticky header, it is already on screen.
		this.trigger?.focus({ preventScroll: true });
		this.trigger = null;
	}

	/**
	 * Close immediately at the start of a page transition, without moving focus
	 * (the trigger may be leaving the page; Barba handles focus on the new page).
	 */
	closeForNavigation(): void {
		if (!this.isOpen) return;

		this.hide();
		this.trigger = null;
	}

	/**
	 * Name the dialog after the fragment's first heading (fallback: generic label).
	 */
	private labelFromHeading(): void {
		if (!this.elements) return;

		const { panel, content } = this.elements;
		const heading = content.querySelector<HTMLElement>('h1, h2, h3');

		if (heading) {
			heading.id ||= `${PANEL_ID}-title`;
			panel.setAttribute('aria-labelledby', heading.id);
			panel.removeAttribute('aria-label');
		} else {
			panel.removeAttribute('aria-labelledby');
			panel.setAttribute('aria-label', t('panelLabel'));
		}
	}

	private show(): void {
		if (!this.elements) return;

		const { panel, overlay } = this.elements;

		// Everything except the panel becomes inert: focus and screen readers stay in the dialog.
		this.inertElements = Array.from(document.body.children).filter(
			(el): el is HTMLElement => el instanceof HTMLElement && el !== panel && el !== overlay && !el.inert
		);
		this.inertElements.forEach((el) => (el.inert = true));

		panel.inert = false;
		panel.classList.add('is-open');
		overlay.classList.add('is-visible');
		this.isOpen = true;
		this.setExpanded(true);
		stopScroll();

		// First focusable element of the fragment, else the close button.
		const inContent = FOCUSABLE.split(', ').map((selector) => `.panel__content ${selector}`).join(', ');
		const target = panel.querySelector<HTMLElement>(inContent) ?? panel.querySelector<HTMLElement>(FOCUSABLE);
		target?.focus();
	}

	private hide(): void {
		if (!this.elements) return;

		const { panel, overlay } = this.elements;

		this.inertElements.forEach((el) => (el.inert = false));
		this.inertElements = [];

		panel.inert = true;
		panel.classList.remove('is-open');
		overlay.classList.remove('is-visible');
		this.isOpen = false;
		this.currentPanelKey = null;
		this.setExpanded(false);
		startScroll();
	}

	/**
	 * Keep every trigger's aria-expanded in sync (header is outside the Barba container).
	 */
	private setExpanded(expanded: boolean): void {
		document.querySelectorAll<HTMLElement>('[data-panel-open]').forEach((trigger) => {
			trigger.setAttribute('aria-controls', PANEL_ID);
			trigger.setAttribute('aria-expanded', String(expanded && trigger.dataset.panelOpen === this.currentPanelKey));
		});
	}

	isOpened(): boolean {
		return this.isOpen;
	}

	getCurrentKey(): string | null {
		return this.currentPanelKey;
	}
}

let panelManagerInstance: PanelManager | null = null;

export function initPanelManager(): PanelManager {
	if (!panelManagerInstance) {
		panelManagerInstance = new PanelManager();
	}
	return panelManagerInstance;
}

export function getPanelManager(): PanelManager {
	if (!panelManagerInstance) {
		throw new Error('PanelManager not initialized. Call initPanelManager() first.');
	}
	return panelManagerInstance;
}

/** Safe accessor for other modules (Barba): null when fragments are disabled. */
export function getPanelManagerIfReady(): PanelManager | null {
	return panelManagerInstance;
}
