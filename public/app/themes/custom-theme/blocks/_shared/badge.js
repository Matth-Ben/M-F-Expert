/**
 * « NOUVEAU sur VIRIAT » badge — editor twin of parts/components/badge.php (same markup & classes).
 */
import { __ } from '@wordpress/i18n';

export default function Badge({ className = '' }) {
	return (
		<div className={`badge-new ${className}`}>
			<span className="badge-new__star" aria-hidden="true" />
			<span className="badge-new__star" aria-hidden="true" />
			<span className="badge-new__star" aria-hidden="true" />
			<span className="badge-new__disc" aria-hidden="true" />
			<p className="badge-new__text">
				<span className="badge-new__new">{__('NEW', 'wp-gutenberg')}</span>{' '}
				<span className="badge-new__on">{__('in', 'wp-gutenberg')}</span>{' '}
				<strong className="badge-new__place">{__('VIRIAT', 'wp-gutenberg')}</strong>
			</p>
		</div>
	);
}
