<?php
/**
 * Site contact details (Réglages > Coordonnées), read with wpg_contact().
 * Used by the header, footer, mobile action bar and contact blocks.
 * The announcement bar has its own back-office page (includes/Admin/Announcement.php).
 *
 * type: text | email | tel | url | textarea. `default` applies until the client saves the page.
 */

return array(
	'phone'        => array(
		'title' => __( 'Phone', 'wp-gutenberg' ),
		'type'  => 'tel',
	),
	'email'        => array(
		'title'   => __( 'Email', 'wp-gutenberg' ),
		'type'    => 'email',
		'default' => 'mf.expert26@gmail.com',
	),
	'address'      => array(
		'title'   => __( 'Address', 'wp-gutenberg' ),
		'type'    => 'textarea',
		'default' => 'Viriat',
	),
	'hours'        => array(
		'title' => __( 'Opening hours', 'wp-gutenberg' ),
		'type'  => 'textarea',
	),
	'access'       => array(
		'title' => __( 'Access & parking', 'wp-gutenberg' ),
		'type'  => 'textarea',
	),
	'chat_url'     => array(
		'title'       => __( 'Chat link', 'wp-gutenberg' ),
		'type'        => 'url',
		'description' => __( 'Target of the “Une question ?” buttons. Defaults to the contact page.', 'wp-gutenberg' ),
	),
);
