<?php

namespace WPGutenberg\Admin;

/**
 * Settings > Coordonnées: contact details shared by header, footer and blocks (fields in config/contact.php).
 * Read with wpg_contact().
 */
final class Contact extends OptionsPage {

	public const OPTION = 'contact';

	public static function fields(): array {
		return wpg_contact_config();
	}

	protected function title(): string {
		return __( 'Contact details', 'wp-gutenberg' );
	}

	protected function menu(): array {
		return array( 'parent' => 'options-general.php' );
	}
}
