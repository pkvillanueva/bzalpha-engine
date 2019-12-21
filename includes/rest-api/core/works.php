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
				'callback' => [ $this, 'rest_bulk' ],
				'args'     => [
					'vessel' => [
						'description' => __( 'Set the vessel for orders.' ),
						'type'        => 'integer',
						'required'    => true,
					],
					'positions' => [
						'description' => __( 'Set to create position orders.' ),
						'type'        => 'array',
						'required'    => true,
					],
				]
			]
		] );

		register_rest_route( 'bzalpha/v1', '/bz-order/close', [
			[
				'methods'  => 'POST',
				'callback' => [ $this, 'rest_close' ],
				'args'     => [
					'id' => [
						'description' => __( 'Set order ID to close.' ),
						'type'        => 'integer',
						'required'    => true,
					],
				]
			]
		] );
	}

	/**
	 * Create order.
	 */
	public function rest_bulk( $request ) {
		if ( ! function_exists( 'acf' ) ) {
			return new \WP_Error( 'invalid_route', 'Invalid route.', [ 'status' => 404 ] );
		}

		$data = [];

		$meta_fields = [
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
					bzalpha_update_field( $meta, $request[ $meta ], $post_id );
				}
			}

			bzalpha_update_field( 'vessel', $request['vessel'], $post_id );
			bzalpha_update_field( 'order_status', 'pending', $post_id );
			bzalpha_update_field( 'position', $position, $post_id );

			wp_update_post( [
				'ID'         => $post_id,
				'post_title' => "Order #{$post_id} [{$position}]",
			] );

			$data[] = get_post( $post_id );
		}

		return new \WP_REST_Response( $data, 200 );
	}

	/**
	 * Switch order.
	 */
	public function rest_switch( $request ) {
		if ( ! function_exists( 'acf' ) ) {
			return new \WP_Error( 'invalid_route', 'Invalid route.', [ 'status' => 404 ] );
		} elseif ( empty( $request['id'] ) ) {
			return new \WP_Error( 'invalid_params', 'Invalid params.', [ 'status' => 404 ] );
		} elseif ( get_post_status( $request['id'] ) !== 'publish' ) {
			return new \WP_Error( 'invalid_request', 'Order not found.', [ 'status' => 404 ] );
		}

		$order_id = intval( $request['id'] );

		$child_order = bzalpha_update_field( 'child_order', $order_id );
		if ( ! $child_order || get_post_status( $child_order ) !== 'publish' ) {
			return new \WP_Error( 'invalid_request', 'Child order not found.', [ 'status' => 404 ] );
		}

		$seaman = bzalpha_update_field( 'seaman', $order_id );
		if ( ! $seaman || get_post_status( $seaman ) !== 'publish' ) {
			return new \WP_Error( 'invalid_request', 'Seaman not found.', [ 'status' => 404 ] );
		}

		$vessel = bzalpha_update_field( 'seaman', $order_id );
		if ( ! $vessel || get_post_status( $vessel ) !== 'publish' ) {
			return new \WP_Error( 'invalid_request', 'Vessel not found.', [ 'status' => 404 ] );
		}

		// Complete the order.
		bzalpha_update_field( 'order_status', 'completed', $request['id'] );

		// Replace the current order by the child one.
		bzalpha_update_field( 'order_status', 'onboard', $child_order );

		// Get child order fields.
		$fields = get_fields( $child_order );

		return $child_order;
	}
}

new Works();
