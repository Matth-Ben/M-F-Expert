<?php
/**
 * Contact form of the wpg/contact block (F1): no plugin, no JS.
 *
 * The form POSTs to admin-post.php (action `wpg_contact`). The handler checks the nonce and a honeypot,
 * validates on the server, sends the message with wp_mail() to the site email (Réglages > Coordonnées,
 * fallback admin_email) with Reply-To = the visitor, then redirects (303) back to the page:
 * `?contact=sent#wpg-contact-status` or `?contact=error&contact_token=…#wpg-contact-status`.
 * The fragment targets the result message, which is focusable: the browser focuses it on load, so screen
 * readers read it (with `autofocus` as a fallback, see render.php), and the page title is prefixed.
 * On error, the field errors and the typed values are kept 10 minutes in a transient (key in the URL)
 * so render.php can show them again; read them with ContactForm::state().
 *
 * @package WPGutenberg
 */

namespace WPGutenberg\Forms;

/**
 * Handles the contact form submission.
 */
final class ContactForm {

	public const ACTION = 'wpg_contact';
	public const NONCE  = '_wpg_contact_nonce';
	public const ANCHOR = 'contact-form';

	/** Id of the result message (redirect target, receives the focus). */
	public const STATUS_ANCHOR = 'wpg-contact-status';

	/** Honeypot field name: hidden from people, filled by bots. */
	public const HONEYPOT = 'website';

	/** Submitted fields and their max length. */
	private const FIELDS = array(
		'name'    => 100,
		'email'   => 200,
		'phone'   => 30,
		'message' => 5000,
	);

	/**
	 * Hooks only.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'admin_post_nopriv_' . self::ACTION, array( $this, 'handle' ) );
		add_action( 'admin_post_' . self::ACTION, array( $this, 'handle' ) );
		// Both filters: an SEO plugin may short-circuit the title with `pre_get_document_title`.
		add_filter( 'pre_get_document_title', array( $this, 'title_status' ), PHP_INT_MAX );
		add_filter( 'document_title', array( $this, 'title_status' ), PHP_INT_MAX );
	}

	/**
	 * Prefix the document title with the result of the submission: the title is the first thing
	 * a screen reader announces when the page loads (WCAG 3.3.1 / 4.1.3).
	 *
	 * @param string $title Document title ('' from `pre_get_document_title` when WordPress builds it).
	 * @return string
	 */
	public function title_status( string $title ): string {
		$status = isset( $_GET['contact'] ) ? sanitize_key( wp_unslash( $_GET['contact'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display only.

		if ( '' === $title || ! in_array( $status, array( 'sent', 'error' ), true ) || ! is_singular() || ! has_block( 'wpg/contact', get_queried_object() ) ) {
			return $title;
		}

		return 'sent' === $status
			/* translators: %s: page title */
			? sprintf( __( 'Message sent: %s', 'wp-gutenberg' ), $title )
			/* translators: %s: error message, or page title (contact form result) */
			: sprintf( __( 'Error: %s', 'wp-gutenberg' ), $title );
	}

	/**
	 * Process a submission, then redirect back to the form.
	 *
	 * @return void
	 */
	public function handle(): void {
		$back = $this->back_url();

		if ( ! isset( $_POST[ self::NONCE ] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST[ self::NONCE ] ) ), self::ACTION ) ) {
			$this->redirect( $back, 'error', array( 'reason' => 'expired' ) );
		}

		// Bots fill every field: pretend it worked, send nothing.
		if ( ! empty( $_POST[ self::HONEYPOT ] ) ) {
			$this->redirect( $back, 'sent' );
		}

		$values = $this->values();
		$errors = $this->validate( $values );

		if ( $errors ) {
			$this->redirect( $back, 'error', array( 'reason' => 'invalid', 'errors' => $errors, 'values' => $values ) );
		}

		if ( ! $this->send( $values ) ) {
			$this->redirect( $back, 'error', array( 'reason' => 'mail', 'values' => $values ) );
		}

		$this->redirect( $back, 'sent' );
	}

	/**
	 * State of the form for the current request (read by blocks/contact/render.php).
	 *
	 * @return array{status: string, reason: string, errors: array<string, string>, values: array<string, string>}
	 */
	public static function state(): array {
		$state = array(
			'status' => '',
			'reason' => '',
			'errors' => array(),
			'values' => array_fill_keys( array_keys( self::FIELDS ), '' ),
		);

		$status = isset( $_GET['contact'] ) ? sanitize_key( wp_unslash( $_GET['contact'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display only.

		if ( 'sent' === $status ) {
			$state['status'] = 'sent';
		} elseif ( 'error' === $status ) {
			$state['status'] = 'error';
			$token           = isset( $_GET['contact_token'] ) ? sanitize_key( wp_unslash( $_GET['contact_token'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$saved           = $token ? get_transient( 'wpg_contact_' . $token ) : false;

			if ( is_array( $saved ) ) {
				$state['reason'] = (string) ( $saved['reason'] ?? '' );
				$state['errors'] = array_map( 'strval', (array) ( $saved['errors'] ?? array() ) );
				$state['values'] = array_merge( $state['values'], array_map( 'strval', (array) ( $saved['values'] ?? array() ) ) );
			}
		}

		return $state;
	}

	/**
	 * Sanitised submitted values.
	 *
	 * @return array<string, string>
	 */
	private function values(): array {
		$values = array();

		foreach ( self::FIELDS as $field => $max ) {
			$raw              = isset( $_POST[ $field ] ) ? (string) wp_unslash( $_POST[ $field ] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- nonce checked in handle(), sanitised below.
			$clean            = 'message' === $field ? sanitize_textarea_field( $raw ) : sanitize_text_field( $raw );
			$values[ $field ] = mb_substr( trim( $clean ), 0, $max );
		}

		return $values;
	}

	/**
	 * Server-side validation (the browser validation can be bypassed).
	 *
	 * @param array<string, string> $values Sanitised values.
	 * @return array<string, string> Error message per field.
	 */
	private function validate( array $values ): array {
		$errors = array();

		if ( '' === $values['name'] ) {
			$errors['name'] = __( 'Please enter your name.', 'wp-gutenberg' );
		}

		if ( ! is_email( $values['email'] ) ) {
			$errors['email'] = __( 'Please enter a valid email address (e.g. name@example.com).', 'wp-gutenberg' );
		}

		if ( '' !== $values['phone'] && ! preg_match( '/^\+?[0-9 .()\-]{6,25}$/', $values['phone'] ) ) {
			$errors['phone'] = __( 'Please enter a valid phone number, or leave this field empty.', 'wp-gutenberg' );
		}

		if ( '' === $values['message'] ) {
			$errors['message'] = __( 'Please enter your message.', 'wp-gutenberg' );
		}

		return $errors;
	}

	/**
	 * Email the message to the site.
	 *
	 * @param array<string, string> $values Valid values.
	 * @return bool
	 */
	private function send( array $values ): bool {
		$to = wpg_contact()['email'];
		if ( ! is_email( $to ) ) {
			$to = (string) get_option( 'admin_email' );
		}

		$site = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
		$name = str_replace( array( "\r", "\n", '"', '<', '>' ), '', $values['name'] );

		/* translators: 1: site name, 2: visitor name */
		$subject = sprintf( __( '[%1$s] New message from %2$s', 'wp-gutenberg' ), $site, $name );

		$body = implode(
			"\n",
			array(
				/* translators: %s: visitor name */
				sprintf( __( 'Name: %s', 'wp-gutenberg' ), $name ),
				/* translators: %s: visitor email */
				sprintf( __( 'Email: %s', 'wp-gutenberg' ), $values['email'] ),
				/* translators: %s: visitor phone */
				sprintf( __( 'Phone: %s', 'wp-gutenberg' ), '' !== $values['phone'] ? $values['phone'] : '—' ),
				'',
				$values['message'],
				'',
				'—',
				/* translators: %s: page URL */
				sprintf( __( 'Sent from the contact form: %s', 'wp-gutenberg' ), remove_query_arg( array( 'contact', 'contact_token' ), $this->back_url() ) ),
			)
		);

		return wp_mail( $to, $subject, $body, array( sprintf( 'Reply-To: "%s" <%s>', $name, $values['email'] ) ) );
	}

	/**
	 * Page of the form (referer sent by wp_nonce_field()), without the previous status.
	 *
	 * @return string
	 */
	private function back_url(): string {
		$referer = wp_get_referer();
		$url     = $referer ? wp_validate_redirect( $referer, home_url( '/' ) ) : home_url( '/' );

		return remove_query_arg( array( 'contact', 'contact_token' ), $url );
	}

	/**
	 * 303 back to the form, with the status (and the errors in a short-lived transient).
	 *
	 * @param string               $url    Page of the form.
	 * @param string               $status sent | error.
	 * @param array<string, mixed> $data   Errors / values to show again.
	 * @return never
	 */
	private function redirect( string $url, string $status, array $data = array() ): never {
		$args = array( 'contact' => $status );

		if ( $data ) {
			$token = strtolower( wp_generate_password( 16, false ) );
			set_transient( 'wpg_contact_' . $token, $data, 10 * MINUTE_IN_SECONDS );
			$args['contact_token'] = $token;
		}

		wp_safe_redirect( add_query_arg( $args, $url ) . '#' . self::STATUS_ANCHOR, 303 );
		exit;
	}
}
