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
				]
			]
		] );
	}

	/**
	 * Create order.
	 */
	public function bulk_order( $request ) {
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
			$meta_input = [
				'status'   => 'pending',
				'position' => $position,
			];

			foreach ( $meta_fields as $meta ) {
				if ( isset( $request[ $meta ] ) ) {
					$meta_input[ $meta ] = $request[ $meta ];
				}
			}

			$post_id = wp_insert_post( [
				'post_status' => 'publish',
				'post_type'   => 'bz_order',
				'meta_input'  => $meta_input,
			] );

			if ( ! $post_id || is_wp_error( $post_id ) ) {
				continue;
			}

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
		if ( ! post_exists( $request['id'] ) ) {
			return $this->error( 'Order not found' );
		}

		$status = get_post_meta( $request['id'], 'status', true );

		if ( $status !== 'onboard' ) {
			return $this->error( 'Could not close this order.' );
		}

		$seaman_id = intval( get_post_meta( $request['id'], 'seaman', true ) );

		if ( ! post_exists( $seaman_id ) ) {
			return $this->error( 'Seaman not found.' );
		}

		$vessel_id = intval( get_post_meta( $request['id'], 'vessel', true ) );

		if ( ! post_exists( $vessel_id ) ) {
			return $this->error( 'Vessel not found' );
		}

		// Save current order as seaman experience.
		$this->save_experience(
			$seaman_id,
			$request['id'],
			$vessel_id,
			$request['end_of_contract']
		);

		update_post_meta( $request['id'], 'status', 'completed' );

		// Try child order.
		$this->maybe_switch_order( $request['id'] );

		return [
			'success' => true,
		];
	}

	/**
	 * Save seaman experience.
	 */
	public function save_experience( $seaman_id, $order_id, $vessel_id, $end_of_contract ) {
		$data = [
			'crewing_agency'  => 'BZ Alpha Navigation',
			'end_of_contract' => $end_of_contract,
		];

		$order_map = [
			'date_start' => 'sign_on',
			'date_end'   => 'sign_off',
			'rank'       => 'position',
			'wage'       => 'wage',
			'currency'   => 'currency',
		];

		// Get data base on map.
		foreach ( $order_map as $key => $meta_key ) {
			$data[ $key ] = get_post_meta( $order_id, $meta_key, true );
		}

		$data['vessel'] = get_the_title( $vessel_id );

		$vessel_map = [
			'type'       => 'type',
			'flag'       => 'flag',
			'year_built' => 'year_built',
			'imo'        => 'imo',
			'grt'        => 'grt',
			'dwt'        => 'dwt',
			'hp'         => 'hp',
			'kw'         => 'kw',
			'engine'     => 'engine',
		];

		// Get data base on map.
		foreach ( $vessel_map as $key => $meta_key ) {
			$data[ $key ] = get_post_meta( $vessel_id, $meta_key, true );
		}

		$principal = get_the_terms( $vessel_id, 'principal' );

		if ( $principal && ! is_wp_error( $principal ) ) {
			$principal             = array_shift( $principal );
			$data['owner']         = $principal->name;
			$data['owner_country'] = get_term_meta( $principal->term_id, 'country', true );
		}

		$experiences = get_post_meta( $seaman_id, 'experiences', true );

		if ( empty( $experiences ) || ! is_array( $experiences ) ) {
			$experiences = [ $data ];
		} else {
			array_unshift( $experiences, $data );
		}

		update_post_meta( $seaman_id, 'experiences', $experiences );
	}

	/**
	 * Switch order.
	 */
	public function maybe_switch_order( $order_id ) {
		$child_order_id = intval( get_post_meta( $order_id, 'child_order', true ) );

		// No child order.
		if ( ! post_exists( $child_order_id ) ) {
			return;
		}

		$status = get_post_meta( $child_order_id, 'status', true );

		// Order is not reserved status.
		if ( $status !== 'reserved' ) {
			return;
		}

		$parent_order_id = intval( get_post_meta( $child_order_id, 'parent_order', true ) );

		// Order not match.
		if ( ! $parent_order_id || $order_id !== $parent_order_id ) {
			return;
		}

		update_post_meta( $child_order_id, 'status', 'onboard' );
	}

	/**
	 * Rest error.
	 */
	public function error( $error ) {
		return new \WP_Error( 'invalid_request', $error, [ 'status' => 404 ] );
	}
}
