/**
 * Shared editor helpers for image attributes stored as an attachment ID (`mediaId`).
 *
 * Storing only the ID (never the URL) means the front always renders the current
 * file with native srcset/sizes via wp_get_attachment_image().
 */
import { __ } from '@wordpress/i18n';
import { MediaPlaceholder, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { Button, PanelBody } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';

/**
 * Attachment record for an ID (null while loading / when empty).
 *
 * @param {number|undefined} id
 */
export function useMedia(id) {
	return useSelect((select) => (id ? select(coreStore).getMedia(id, { context: 'view' }) : null), [id]);
}

/**
 * Best URL for the editor preview.
 *
 * @param {Object|null} media
 * @param {string}      size
 */
export function mediaUrl(media, size = 'large') {
	return media?.media_details?.sizes?.[size]?.source_url ?? media?.source_url ?? '';
}

/**
 * Image placeholder displayed in the canvas when no image is selected.
 */
export function MediaCanvasPlaceholder({ onSelect, className }) {
	return (
		<MediaPlaceholder
			className={className}
			icon="format-image"
			labels={{ title: __('Image', 'wp-gutenberg') }}
			accept="image/*"
			allowedTypes={['image']}
			onSelect={(media) => onSelect(media.id)}
		/>
	);
}

/**
 * Sidebar panel: preview + replace/remove.
 */
export function MediaPanel({ id, onChange, title = __('Image', 'wp-gutenberg') }) {
	const media = useMedia(id);

	return (
		<PanelBody title={title}>
			<MediaUploadCheck>
				<MediaUpload
					allowedTypes={['image']}
					value={id}
					onSelect={(selected) => onChange(selected.id)}
					render={({ open }) => (
						<div style={{ display: 'grid', gap: 8 }}>
							{media && <img src={mediaUrl(media, 'medium')} alt="" style={{ width: '100%', height: 'auto' }} />}
							<Button variant="secondary" onClick={open}>
								{id ? __('Replace image', 'wp-gutenberg') : __('Select image', 'wp-gutenberg')}
							</Button>
							{!!id && (
								<Button variant="link" isDestructive onClick={() => onChange(undefined)}>
									{__('Remove image', 'wp-gutenberg')}
								</Button>
							)}
						</div>
					)}
				/>
			</MediaUploadCheck>
		</PanelBody>
	);
}
