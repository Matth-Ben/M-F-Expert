<?php

namespace WPGutenberg\Admin;

/**
 * Back-office menu « En-tête »: logo (image + text) and the header button. Read with wpg_header().
 * The logo image also replaces the large emblem of the page headers (wpg_logo_url()).
 * The menu links are managed in Appearance > Menus.
 */
final class Header extends OptionsPage {

	public const OPTION = 'header';

	public static function fields(): array {
		return array_merge(
			array(
				'logo_id'   => array(
					'section'     => __( 'Logo', 'wp-gutenberg' ),
					'title'       => __( 'Logo image', 'wp-gutenberg' ),
					'type'        => 'image',
					'description' => __( 'Replaces the emblem (two children and a comb) in the header and in the large logos of the page headers. Transparent PNG or WebP, at least 600 px wide.', 'wp-gutenberg' ),
				),
				'logo_text' => array(
					'title'       => __( 'Text next to the logo', 'wp-gutenberg' ),
					'type'        => 'text',
					'default'     => wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ),
					'description' => __( 'Leave empty to show the image only.', 'wp-gutenberg' ),
				),
			),
			self::button_fields( __( 'Button', 'wp-gutenberg' ), __( 'Show the button in the header (desktop)', 'wp-gutenberg' ) )
		);
	}

	protected function title(): string {
		return __( 'Header', 'wp-gutenberg' );
	}

	protected function menu(): array {
		return array(
			'icon'     => 'dashicons-table-row-before',
			'position' => 22,
		);
	}

	protected function intro(): string {
		return sprintf(
			/* translators: %s: URL of Appearance > Menus. */
			__( 'The menu links are edited in <a href="%s">Appearance > Menus</a>.', 'wp-gutenberg' ),
			esc_url( admin_url( 'nav-menus.php' ) )
		);
	}
}
