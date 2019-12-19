<?php

namespace BZAlpha\REST_API\Controllers;

/**
 * BZ_Order Controller.
 */
class BZ_Order extends \WP_REST_Posts_Controller {

	/**
	 * Constructor.
	 */
	public function __construct( $post_type ) {
		parent::__construct( $post_type );

		// Custom namespace.
		$this->namespace = 'bzalpha/v1';

		// Custom rest fields.
		$this->register_rest_fields();

		// Register actions.
		add_filter( 'rest_bz_order_query', [ $this, '_search_query' ], 10, 2 );
		add_filter( 'rest_after_insert_bz_order', [ $this, '_after_insert' ], 10, 2 );
	}

	/**
	 * Register rest fields.
	 */
	public function register_rest_fields() {
		$meta_fields = [
			'order_status',
			'deadline',
			'seaman',
			'position',
			'sign_on',
			'sign_off',
			'port',
			'return_port',
			'remark',
			'wage',
			'currency',
			'vessel',
			'candidates',
			'contract_plus',
			'contract_minus',
			'flight_status',
			'uniform',
		];

		foreach ( $meta_fields as $meta_name ) {
			register_rest_field( 'bz_order', $meta_name, [
				'schema'       => null,
				'get_callback' => function() use ( $meta_name ) {
					return get_field( $meta_name );
				},
				'update_callback' => function( $value, $post, $meta_name ) {
					/**
					 * Filter values before updating field.
					 */
					$value = apply_filters( "bzalpha_update_bz_order_{$meta_name}", $value, $post );

					return update_field( $meta_name, $value, $post->ID );
				}
			] );
		}

		register_rest_field( 'bz_order', 'bind_order', [
			'schema'       => null,
			'get_callback' => function() {
				$bind_order = get_field( 'bind_order' );

				if ( empty( $bind_order ) ) {
					return null;
				}

				return array_merge( get_fields( $bind_order->ID ), [
					'id' => $bind_order->ID,
				] );
			}
		] );
	}

	/**
	 * Filter search query.
	 */
	public function _search_query( $args, $request ) {
		if ( isset( $request['vessel'] ) ) {
			$args['meta_query'] = [
				'relation' => 'AND',
				[
					'key'     => 'vessel',
					'compare' => '=',
					'value'   => $request['vessel'],
				],
				[
					'relation' => 'OR',
					[
						'key'     => 'order_status',
						'compare' => '=',
						'value'   => 'pending',
					],
					[
						'key'     => 'order_status',
						'compare' => '=',
						'value'   => 'processing',
					],
					[
						'key'     => 'order_status',
						'compare' => '=',
						'value'   => 'onboard',
					],
				]
			];
		}

		return $args;
	}

	/**
	 * Filter after insert.
	 */
	public function _after_insert( $post, $request ) {
		if ( isset( $request['position'] ) ) {
			$position = $request['position'];

			wp_update_post( [
				'ID'         => $post->ID,
				'post_title' => "Order #{$post->ID} [{$position}]",
			] );
		}

		if ( isset( $request['parent_order'] ) ) {
			update_field( 'bind_order', $post->ID, intval( $request['parent_order'] ) );
			update_field( 'candidates', [], intval( $request['parent_order'] ) );
		}
	}
}
