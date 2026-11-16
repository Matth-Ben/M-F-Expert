/**
 * Keeps the hero as the very first block of every page.
 *
 * The hero is locked (`lock: { move, remove }`, see blocks/hero/block.json), but a lock
 * doesn't prevent inserting another block ABOVE it. This store subscriber:
 * - moves any root block placed before the hero to just after it;
 * - adds a hero to existing pages that don't have one yet.
 * A locked block can't be moved programmatically either, so we move the others.
 */
import { createBlock } from '@wordpress/blocks';
import { dispatch, select, subscribe } from '@wordpress/data';
import { store as blockEditorStore } from '@wordpress/block-editor';
import { store as editorStore } from '@wordpress/editor';

const HERO = 'wpg/hero';
const POST_TYPES = ['page'];

let busy = false;

function enforce() {
	if (busy) return;

	const editor = select(editorStore);
	if (!editor?.__unstableIsEditorReady?.() || !POST_TYPES.includes(editor.getCurrentPostType())) return;

	const blockEditor = select(blockEditorStore);
	const order = blockEditor.getBlockOrder('');

	// Fast path, runs on every store change.
	if (order.length && blockEditor.getBlockName(order[0]) === HERO) return;

	busy = true;
	try {
		const heroIndex = order.findIndex((clientId) => blockEditor.getBlockName(clientId) === HERO);

		if (heroIndex > 0) {
			// Blocks inserted above the hero go right below it.
			dispatch(blockEditorStore).moveBlocksToPosition(order.slice(0, heroIndex), '', '', heroIndex);
		} else if (heroIndex === -1 && blockEditor.getBlocksByName(HERO).length === 0) {
			// Existing page without hero (a hero nested in another block is left alone).
			dispatch(blockEditorStore).insertBlocks(createBlock(HERO), 0, '', false);
		}
	} finally {
		busy = false;
	}
}

export function keepHeroFirst() {
	// Global subscription: the editor becomes "ready" in core/editor, not in the block store.
	subscribe(enforce);
}
