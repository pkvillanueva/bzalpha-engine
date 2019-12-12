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
		register_rest_route( 'bzalpha/v1', '/bz-order/bulk', [
			[
				'methods'  => 'POST',
				'callback' => [ $this, 'create_bulk_order' ],
			]
		] );
	}

	/**
	 * Create order.
	 */
	public function create_bulk_order( $request ) {
		if ( ! function_exists( 'acf' ) ) {
			return new \WP_Error( 'invalid_route', 'Invalid route.', [ 'status' => 404 ] );
		}

		if ( empty( $request['vessel'] ) || ! is_array( $request['positions' ] ) || empty( $request['positions'] ) || empty( $request['sign_on'] ) ) {
			return new \WP_Error( 'invalid_params', 'Invalid params.', [ 'status' => 404 ] );
		}

		$data = [];

		$meta_fields = [
			'vessel',
			'wage',
			'currency',
			'port',
			'uniform',
			'sign_on',
			'deadline',
			'contract_plus',
			'contract_minus',
			'remark',
		];

		foreach ( $request['positions'] as $position ) {
			$post_id = wp_insert_post( [
				'post_status' => 'publish',
				'post_type'   => 'bz_order',
			] );

			if ( ! $post_id || is_wp_error( $post_id ) ) {
				continue;
			}

			foreach ( $meta_fields as $meta ) {
				if ( isset( $request[ $meta ] ) ) {
					update_field( $meta, $request[ $meta ], $post_id );
				}
			}

			// Set initial status.
			update_field( 'order_status', 'pending', $post_id );

			// Set position.
			update_field( 'position', $position, $post_id );

			// Arrange order title.
			wp_update_post( [
				'ID'         => $post_id,
				'post_title' => "Order #{$post_id} [{$position}]"
			] );

			$data[] = get_post( $post_id );
		}

		return new \WP_REST_Response( $data, 200 );
	}
}

new Works();
