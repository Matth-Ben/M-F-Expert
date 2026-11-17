<?php

namespace WPGutenberg\Admin;

/**
 * Back-office menu « Bandeau d'annonce »: show / hide the bar above the header and edit its text
 * (bold, italic, link). Read with wpg_announcement().
 */
final class Announcement extends OptionsPage {

	public const OPTION = 'announcement';

	public static function fields(): array {
		return array(
			'enabled' => array(
				'title'   => __( 'Display', 'wp-gutenberg' ),
				'type'    => 'checkbox',
				'label'   => __( 'Show the announcement bar on the site', 'wp-gutenberg' ),
				'default' => true,
			),
			'text'    => array(
				'title'       => __( 'Text', 'wp-gutenberg' ),
				'type'        => 'editor',
				'default'     => 'Ouverture du salon le <strong>5 janvier 2027</strong> à Viriat',
				'description' => __( 'One short sentence. Bold, italic and links only; line breaks are ignored.', 'wp-gutenberg' ),
			),
		);
	}

	protected function title(): string {
		return __( 'Announcement bar', 'wp-gutenberg' );
	}

	protected function menu(): array {
		return array(
			'icon'     => 'dashicons-megaphone',
			'position' => 21, // Right after Pages.
		);
	}

	protected function intro(): string {
		return __( 'The bar is displayed above the menu on every page: opening, holidays, exceptional closure…', 'wp-gutenberg' );
	}
}
