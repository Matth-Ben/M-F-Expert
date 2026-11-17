<?php
/**
 * « NOUVEAU sur VIRIAT » badge (decorative star + bordeaux disc). Editor twin: blocks/_shared/badge.js.
 * Text stays on the bordeaux disc for contrast (DS v2 §09); its size follows the badge size (shared.css).
 * Pass a positioning (and size) class in $args['class'].
 *
 * @var array{class?: string} $args
 */
?>
<div class="badge-new <?php echo esc_attr( $args['class'] ?? '' ); ?>">
	<span class="badge-new__star" aria-hidden="true"></span>
	<span class="badge-new__star" aria-hidden="true"></span>
	<span class="badge-new__star" aria-hidden="true"></span>
	<span class="badge-new__disc" aria-hidden="true"></span>
	<p class="badge-new__text"><span class="badge-new__new"><?php esc_html_e( 'NEW', 'wp-gutenberg' ); ?></span> <span class="badge-new__on"><?php esc_html_e( 'in', 'wp-gutenberg' ); ?></span> <strong class="badge-new__place"><?php esc_html_e( 'VIRIAT', 'wp-gutenberg' ); ?></strong></p>
</div>
