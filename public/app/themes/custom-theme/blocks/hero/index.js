import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks } from '@wordpress/block-editor';
import metadata from './block.json';
import Edit from './edit';

registerBlockType(metadata.name, {
	edit: Edit,
	// Dynamic block: markup comes from render.php. Only inner blocks are saved.
	save: () => <InnerBlocks.Content />,
});
