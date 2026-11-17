/**
 * Line icon for the editor — twin of wpg_icon() (same class names).
 * The editor canvas has no inline sprite, so the icon references public/sprite.svg by URL
 * (window.wpgEditor is printed by includes/Editor/Editor.php).
 *
 * @param {{ name: string, className?: string }} props
 */
export default function Icon({ name, className = '' }) {
	const sprite = window.wpgEditor?.sprite ?? '';

	return (
		<svg className={`${name} ${className}`.trim()} aria-hidden="true" focusable="false">
			<use href={`${sprite}#${name}`} />
		</svg>
	);
}
