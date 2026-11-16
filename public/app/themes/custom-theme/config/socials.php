<?php
/**
 * Social networks — URLs are edited in Settings > Socials.
 *
 * `link` = share URL prefix (optional).
 *
 * @package WPGutenberg
 */

return array(
	'facebook'  => array(
		'title' => __( 'Facebook', 'wp-gutenberg' ),
		'link'  => 'https://www.facebook.com/sharer.php?u=',
	),
	'instagram' => array(
		'title' => __( 'Instagram', 'wp-gutenberg' ),
	),
	'linkedin'  => array(
		'title' => __( 'LinkedIn', 'wp-gutenberg' ),
		'link'  => 'https://www.linkedin.com/sharing/share-offsite/?url=',
	),
	'vimeo'     => array(
		'title' => __( 'Vimeo', 'wp-gutenberg' ),
	),
	'x'         => array(
		'title' => __( 'X (Twitter)', 'wp-gutenberg' ),
		'link'  => 'https://twitter.com/share?url=',
	),
	'youtube'   => array(
		'title' => __( 'YouTube', 'wp-gutenberg' ),
	),
);
