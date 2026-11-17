<?php

namespace WPGutenberg\Core;

use WP_HTML_Tag_Processor;

/**
 * Contact link tokens for core/button: the client types `#appeler` or `#chat` as the button URL
 * and the front resolves it from Réglages > Coordonnées (wpg_phone_url() / wpg_chat_url()),
 * so the « Appelez-nous » / « Poser une question » buttons follow the number set once in the admin.
 * `data-icon` adds the matching pictogram (components/button.css).
 */
final class ContactLinks {

	public const TOKENS = array(
		'#appeler' => 'phone',
		'#chat'    => 'chat',
	);

	public function register(): void {
		add_filter( 'render_block_core/button', array( $this, 'resolve' ) );
	}

	/**
	 * URL for a token (#appeler → wpg_phone_url(), #chat → wpg_chat_url()), or the URL unchanged.
	 * Also used for the header / footer buttons set in the back-office (wpg_link_url()).
	 */
	public static function url( string $href ): string {
		return match ( self::TOKENS[ $href ] ?? null ) {
			'phone' => wpg_phone_url(),
			'chat'  => wpg_chat_url(),
			default => $href,
		};
	}

	public function resolve( string $html ): string {
		if ( ! str_contains( $html, 'href="#' ) ) {
			return $html;
		}

		$tags = new WP_HTML_Tag_Processor( $html );

		while ( $tags->next_tag( 'a' ) ) {
			$href = (string) $tags->get_attribute( 'href' );

			if ( isset( self::TOKENS[ $href ] ) ) {
				$tags->set_attribute( 'href', self::url( $href ) );
				$tags->set_attribute( 'data-icon', self::TOKENS[ $href ] );
			}
		}

		return $tags->get_updated_html();
	}
}
