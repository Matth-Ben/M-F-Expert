<?php

namespace WPGutenberg\Admin;

/**
 * Back-office menu « Pied de page »: tagline, button, contact details, social networks, copyright.
 * Read with wpg_footer() (and wpg_socials() for the networks listed in config/socials.php).
 */
final class Footer extends OptionsPage {

	public const OPTION = 'footer';

	public static function fields(): array {
		$socials = array();
		foreach ( wpg_socials_config() as $id => $network ) {
			$socials[ $id ] = array(
				'title'       => $network['title'],
				'type'        => 'url',
				'description' => __( 'Address of the page. Leave empty to hide the icon.', 'wp-gutenberg' ),
			);
		}
		if ( $socials ) {
			$socials = array(
				'social_title' => array(
					'section' => __( 'Social networks', 'wp-gutenberg' ),
					'title'   => __( 'Title', 'wp-gutenberg' ),
					'type'    => 'text',
					'default' => 'Suivez-nous',
				),
			) + $socials;
		}

		return array_merge(
			array(
				'tagline' => array(
					'section'     => __( 'Tagline and button', 'wp-gutenberg' ),
					'title'       => __( 'Tagline', 'wp-gutenberg' ),
					'type'        => 'textarea',
					'default'     => 'On diagnostique, on traite, on contrôle et on vous accompagne.',
					'description' => __( 'Handwritten sentence at the top of the footer. Leave empty to hide it.', 'wp-gutenberg' ),
				),
			),
			self::button_fields( '', __( 'Show the button in the footer', 'wp-gutenberg' ) ),
			array(
				'show_details' => array(
					'section'     => __( 'Contact details', 'wp-gutenberg' ),
					'title'       => __( 'Display', 'wp-gutenberg' ),
					'type'        => 'checkbox',
					'label'       => __( 'Show the address, opening hours, access and email', 'wp-gutenberg' ),
					'default'     => true,
					'description' => sprintf(
						/* translators: %s: URL of Settings > Contact details. */
						__( 'They are edited in <a href="%s">Settings > Contact details</a>.', 'wp-gutenberg' ),
						esc_url( admin_url( 'options-general.php?page=' . Contact::OPTION ) )
					),
				),
			),
			$socials,
			array(
				'copyright' => array(
					'section'     => __( 'Bottom line', 'wp-gutenberg' ),
					'title'       => __( 'Copyright', 'wp-gutenberg' ),
					'type'        => 'text',
					'default'     => wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ),
					'description' => sprintf(
						/* translators: %s: URL of Appearance > Menus. */
						__( 'Shown after « © year ». The legal links come from the menu placed in « Legals Menu » (<a href="%s">Appearance > Menus</a>).', 'wp-gutenberg' ),
						esc_url( admin_url( 'nav-menus.php' ) )
					),
				),
			)
		);
	}

	protected function title(): string {
		return __( 'Footer', 'wp-gutenberg' );
	}

	protected function menu(): array {
		return array(
			'icon'     => 'dashicons-table-row-after',
			'position' => 23,
		);
	}
}
