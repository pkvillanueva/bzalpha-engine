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
		add_action( 'rest_api_init', [ $this, 'routes' ] );
	}

	/**
	 * Register routes.
	 */
	public function routes() {
		register_rest_route( 'bzalpha/v1', '/bz-order/bulk', [
			[
				'methods'  => 'POST',
				'callback' => [ $this, 'bulk_order' ],
				'args'     => [
					'vessel'    => [
						'description' => __( 'Set the vessel for orders.' ),
						'type'        => 'integer',
						'required'    => true,
					],
					'positions' => [
						'description' => __( 'Set to create position orders.' ),
						'type'        => 'array',
						'required'    => true,
						'items'       => [
							'type' => 'string',
						],
					],
				]
			]
		] );

		register_rest_route( 'bzalpha/v1', '/bz-order/close', [
			[
				'methods'  => 'POST',
				'callback' => [ $this, 'close_order' ],
				'args'     => [
					'id'              => [
						'description' => __( 'Set order ID to close.' ),
						'type'        => 'integer',
						'required'    => true,
					],
					'end_of_contract' => [
						'description' => __( 'Set end of contract remark.' ),
						'type'        => 'string',
						'required'    => true,
					],
				]
			]
		] );
	}

	/**
	 * Create order.
	 */
	public function bulk_order( $request ) {
		if ( ! function_exists( 'acf' ) ) {
			return $this->error( 'Invalid route.' );
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

		return $data;
	}

	/**
	 * Switch order.
	 */
	public function close_order( $request ) {
		if ( ! function_exists( 'acf' ) ) {
			return $this->error( 'Invalid route.' );
		} elseif ( ! $this->post_exist( $request['id'] ) ) {
			return $this->error( 'Order not found' );
		}

		$order_status = bzalpha_get_field( 'order_status', $request['id'] );

		if ( $order_status !== 'onboard' ) {
			return $this->error( 'Could not close this order.' );
		}

		$seaman = bzalpha_get_field( 'seaman', $request['id'] );

		if ( ! $this->post_exist( $seaman ) ) {
			return $this->error( 'Seaman not found.' );
		}

		$vessel = bzalpha_get_field( 'vessel', $request['id'] );

		if ( ! $this->post_exist( $vessel ) ) {
			return $this->error( 'Vessel not found' );
		}

		// Save current order as seaman experience.
		$this->save_experience(
			$seaman->ID,
			$request['id'],
			$vessel,
			$request['end_of_contract']
		);

		bzalpha_update_field( 'order_status', 'completed', $request['id'] );

		// Try child order.
		$this->maybe_switch_order( $request['id'] );

		return [
			'success' => true,
		];
	}

	/**
	 * Save seaman experience.
	 */
	public function save_experience( $seaman_id, $order_id, $vessel, $end_of_contract ) {
		$data = [
			'end_of_contract' => $end_of_contract,
		];

		$order_map = [
			'date_start' => 'sign_on',
			'date_end'   => 'sign_off',
			'rank'       => 'position',
		];

		$order = bzalpha_get_fields( $order_id );

		// Get data base on map.
		foreach ( $order_map as $key => $target ) {
			if ( isset( $order[ $target ] ) ) {
				$data[ $key ] = $order[ $target ];
			}
		}

		$vessel = array_merge( (array) $vessel, bzalpha_get_fields( $vessel->ID ) );

		$vessel_map = [
			'vessel' => 'post_title',
			'type'   => 'type',
			'flag'   => 'flag',
			'imo'    => 'imo',
			'grt'    => 'grt',
			'dwt'    => 'dwt',
			'hp'     => 'hp',
			'kw'     => 'kw',
			'engine' => 'engine',
		];

		// Get data base on map.
		foreach ( $vessel_map as $key => $target ) {
			if ( isset( $vessel[ $target ] ) ) {
				$data[ $key ] = $vessel[ $target ];
			}
		}

		$principal = get_the_terms( $vessel['ID'], 'principal' );

		if ( $principal && ! is_wp_error( $principal ) ) {
			$principal     = array_shift( $principal );
			$data['owner'] = $principal->name;
		}

		$experiences = bzalpha_get_field( 'experiences', $seaman_id );

		if ( empty( $experiences ) ) {
			$experiences = [ $data ];
		} else {
			array_unshift( $experiences, $data );
		}

		bzalpha_update_field( 'experiences', $experiences, $seaman_id );
	}

	/**
	 * Switch order.
	 */
	public function maybe_switch_order( $order_id ) {
		$child_order = bzalpha_get_field( 'child_order', $order_id );

		// No child order.
		if ( ! $this->post_exist( $child_order ) ) {
			return;
		}

		$order_status = bzalpha_get_field( 'order_status', $child_order->ID );

		// Order is not reserved status.
		if ( $order_status !== 'reserved' ) {
			return;
		}

		$parent_order = bzalpha_get_field( 'parent_order', $child_order->ID );

		// Order not match.
		if ( ! $parent_order || $order_id !== $parent_order->ID ) {
			return;
		}

		bzalpha_update_field( 'order_status', 'onboard', $child_order->ID );
	}

	/**
	 * Validate post existence.
	 */
	public function post_exist( $post ) {
		if ( ! $post ) {
			return false;
		} elseif ( is_object( $post ) && isset( $post->ID ) ) {
			$post = $post->ID;
		} elseif ( is_array( $post ) && isset( $post['ID'] ) ) {
			$post = $post['ID'];
		}

		return get_post_status( $post ) === 'publish';
	}

	/**
	 * Rest error.
	 */
	public function error( $error ) {
		return new \WP_Error( 'invalid_request', $error, [ 'status' => 404 ] );
	}
}

new Works();
