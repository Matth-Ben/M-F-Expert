/**
 * Membre de l'équipe — editor view. Same markup & classes as render.php.
 * Click the round placeholder (or use the sidebar) to choose the portrait.
 */
import { __ } from '@wordpress/i18n';
import { InspectorControls, MediaUpload, MediaUploadCheck, RichText, useBlockProps } from '@wordpress/block-editor';
import { MediaPanel, mediaUrl, useMedia } from '../_shared/media';

function Placeholder() {
	return (
		<div className="grid size-[9.375rem] place-items-center rounded-full border-2 border-dashed border-gold bg-beige text-gold-dark lg:size-[10.625rem]">
			<svg
				className="size-[2.125rem] fill-none stroke-current"
				viewBox="0 0 24 24"
				strokeWidth="1.8"
				strokeLinecap="round"
				strokeLinejoin="round"
				aria-hidden="true"
				focusable="false"
			>
				<rect x="3" y="3" width="18" height="18" rx="2" />
				<circle cx="9" cy="9" r="2" />
				<path d="m21 15-5-5L5 21" />
			</svg>
		</div>
	);
}

export default function Edit({ attributes, setAttributes }) {
	const { mediaId, name, role, bio } = attributes;
	const media = useMedia(mediaId);
	const blockProps = useBlockProps({ className: 'flex flex-col items-center gap-2.5 rounded-card bg-cream p-6 text-center' });

	return (
		<>
			<InspectorControls>
				<MediaPanel id={mediaId} onChange={(id) => setAttributes({ mediaId: id })} title={__('Portrait', 'wp-gutenberg')} />
			</InspectorControls>

			<div {...blockProps}>
				<MediaUploadCheck fallback={<Placeholder />}>
					<MediaUpload
						allowedTypes={['image']}
						value={mediaId}
						onSelect={(selected) => setAttributes({ mediaId: selected.id })}
						render={({ open }) => (
							<button
								type="button"
								className="rounded-full"
								onClick={open}
								aria-label={mediaId ? __('Replace portrait', 'wp-gutenberg') : __('Select portrait', 'wp-gutenberg')}
							>
								{media ? (
									<img
										className="size-[9.375rem] rounded-full object-cover lg:size-[10.625rem]"
										src={mediaUrl(media, 'medium')}
										alt=""
									/>
								) : (
									<Placeholder />
								)}
							</button>
						)}
					/>
				</MediaUploadCheck>

				<RichText
					tagName="h3"
					className="font-serif text-[1.625rem] leading-[1.7] font-normal tracking-normal text-bordeaux"
					value={name}
					onChange={(value) => setAttributes({ name: value })}
					placeholder={__('First and last name…', 'wp-gutenberg')}
					allowedFormats={[]}
				/>
				<RichText
					tagName="p"
					className="text-md font-semibold text-olive-dark"
					value={role}
					onChange={(value) => setAttributes({ role: value })}
					placeholder={__('Role…', 'wp-gutenberg')}
					allowedFormats={[]}
				/>
				<RichText
					tagName="p"
					className="text-md"
					value={bio}
					onChange={(value) => setAttributes({ bio: value })}
					placeholder={__('A few lines of introduction…', 'wp-gutenberg')}
					allowedFormats={['core/bold', 'core/italic', 'core/link']}
				/>
			</div>
		</>
	);
}
