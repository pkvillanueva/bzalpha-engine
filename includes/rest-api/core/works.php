<?php

namespace BZAlpha\REST_API;

/**
 * Works Controller.
 */
class Works {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	/**
	 * Register routes.
	 */
	public function register_routes() {
		register_rest_route( 'bzalpha/v1', '/pool', [
			[
				'methods'  => 'POST',
				'callback' => [ $this, 'create_pool' ],
			]
		] );
	}

	/**
	 * Create pool.
	 */
	public function create_pool( $request ) {
		if ( ! function_exists( 'acf' ) ) {
			return new \WP_Error( 'invalid_route', 'Invalid route.', [ 'status' => 404 ] );
		}

		if ( empty( $request['vessel'] ) || empty( $request['pool'] ) || empty( $request['sign_on'] ) || empty( $request['sign_off'] ) ) {
			return new \WP_Error( 'invalid_params', 'Invalid params.', [ 'status' => 404 ] );
		}

		// Update vessel pool.
		update_field( 'pool', $request['pool'], $request['vessel'] );

		// Update each seaman status.
		foreach ( $request['pool'] as $seaman ) {
			$seaman = intval( $seaman );

			update_field( 'job_status', 'onboard', $seaman );
			update_field( 'vessel', intval( $request['vessel'] ), $seaman );
			update_field( 'sign_on', $request['sign_on'], $seaman );
			update_field( 'sign_off', $request['sign_off'], $seaman );
		}
	}
}

new Works();
