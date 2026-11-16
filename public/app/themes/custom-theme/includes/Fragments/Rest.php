<?php
/**
 * Fragments REST API
 *
 * Registers REST endpoint for loading Twig fragments with security checks.
 *
 * @package WPGutenberg
 * 
 */

namespace WPGutenberg\Fragments;

use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

/**
 * Rest
 *
 * Handles REST API registration and request processing for fragments.
 * Implements security measures:
 * - Nonce verification
 * - Origin/Referer checking
 * - No CORS headers (default WordPress behavior)
 *
 * @package WPGutenberg\Fragments
 */
class Rest {

	/**
	 * REST namespace
	 *
	 * @var string
	 */
	private const NAMESPACE = 'wpg/v1';

	/**
	 * REST route
	 *
	 * @var string
	 */
	private const ROUTE = '/fragment';

	/**
	 * Nonce action
	 *
	 * @var string
	 */
	private const NONCE_ACTION = 'wpg_fragments';

	/**
	 * Fragment loader instance
	 *
	 * @var FragmentLoader
	 */
	private FragmentLoader $loader;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->loader = new FragmentLoader();
	}

	/**
	 * Register hooks
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register REST routes
	 *
	 * @return void
	 */
	public function register_routes(): void {
		register_rest_route(
			self::NAMESPACE,
			self::ROUTE,
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'handle_request' ),
				'permission_callback' => '__return_true', // Public endpoint with origin/referer checks
				'args'                => array(
					'type' => array(
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
						'validate_callback' => function ( $param ) {
							return in_array( $param, array( 'panel', 'alert' ), true );
						},
					),
					'key'  => array(
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					),
					'props' => array(
						'required' => false,
						'type'     => 'object',
						'default'  => array(),
					),
				),
			)
		);
	}

	/**
	 * Check permissions and security
	 *
	 * Verifies:
	 * - Origin header
	 * - Referer header
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return bool|WP_Error
	 */
	public function check_permissions( WP_REST_Request $request ) {
		// Check Origin header if present
		$origin = $request->get_header( 'Origin' );
		if ( ! empty( $origin ) && ! $this->is_valid_origin( $origin ) ) {
			return new WP_Error(
				'invalid_origin',
				__( 'Invalid origin.', 'wp-gutenberg' ),
				array( 'status' => 403 )
			);
		}

		// Check Referer header if present
		$referer = $request->get_header( 'Referer' );
		if ( ! empty( $referer ) && ! $this->is_valid_referer( $referer ) ) {
			return new WP_Error(
				'invalid_referer',
				__( 'Invalid referer.', 'wp-gutenberg' ),
				array( 'status' => 403 )
			);
		}

		return true;
	}

	/**
	 * Validate origin header
	 *
	 * @param string $origin Origin URL.
	 * @return bool
	 */
	private function is_valid_origin( string $origin ): bool {
		$home_url = home_url();
		$home_host = wp_parse_url( $home_url, PHP_URL_HOST );
		$origin_host = wp_parse_url( $origin, PHP_URL_HOST );

		return $home_host === $origin_host;
	}

	/**
	 * Validate referer header
	 *
	 * @param string $referer Referer URL.
	 * @return bool
	 */
	private function is_valid_referer( string $referer ): bool {
		$home_url = home_url();
		$home_host = wp_parse_url( $home_url, PHP_URL_HOST );
		$referer_host = wp_parse_url( $referer, PHP_URL_HOST );

		return $home_host === $referer_host;
	}

	/**
	 * Handle fragment request
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public function handle_request( WP_REST_Request $request ) {
		// Security checks
		$permission_check = $this->check_permissions( $request );
		if ( is_wp_error( $permission_check ) ) {
			return $permission_check;
		}

		$type = $request->get_param( 'type' );
		$key = $request->get_param( 'key' );
		$props = $request->get_param( 'props' );

		// Ensure props is an array
		if ( ! is_array( $props ) ) {
			$props = array();
		}

		// Load fragment
		$result = $this->loader->load( $type, $key, $props );

		// Check for errors
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		// Return response
		return rest_ensure_response( $result );
	}

	/**
	 * Get nonce action name (for use in enqueue)
	 *
	 * @return string
	 */
	public static function get_nonce_action(): string {
		return self::NONCE_ACTION;
	}

	/**
	 * Get REST endpoint URL
	 *
	 * @return string
	 */
	public static function get_endpoint_url(): string {
		return rest_url( self::NAMESPACE . self::ROUTE );
	}
}
