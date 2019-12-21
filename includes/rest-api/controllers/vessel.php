<?php

namespace BZAlpha\REST_API\Controllers;

/**
 * Vessel Controller.
 */
class Vessel extends \WP_REST_Posts_Controller {

	/**
	 * Constructor.
	 */
	public function __construct( $post_type ) {
		parent::__construct( $post_type );
		$this->namespace = 'bzalpha/v1';

		// Register actions.
		$this->register_rest_fields();
	}

	/**
	 * Register rest fields.
	 */
	public function register_rest_fields() {
		register_rest_field( 'vessel', 'principal', [
			'schema'       => null,
			'get_callback' => function( $post ) {
				if ( ! isset( $post['principal'] ) || empty( $post['principal'] ) ) {
					return [];
				}

				$principal = array_map( function( $id ) use ( $post ) {
					$term = get_term( $id, '', ARRAY_A );

					return [
						'id'   => $id,
						'name' => $term['name'],
					];
				}, $post['principal'] );

				return $principal;
			},
		] );

		register_rest_field( 'vessel', 'orders', [
			'schema'       => null,
			'get_callback' => function( $post ) {
				$meta_query = [
					'relation' => 'AND',
					[
						'key'     => 'vessel',
						'compare' => '=',
						'value'   => $post['id'],
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

				$orders = get_posts( [
					'posts_per_page'   => -1,
					'post_type'        => 'bz_order',
					'meta_query'       => $meta_query,
					'orderby'          => 'ID',
					'order'            => 'DESC',
					'suppress_filters' => true,
				] );

				if ( empty( $orders ) ) {
					return [];
				}

				foreach ( $orders as $key => $order ) {
					$orders[ $key ] = [
						'id'           => $order->ID,
						'position'     => bzalpha_get_field( 'position', $order->ID ),
						'order_status' => bzalpha_get_field( 'order_status', $order->ID ),
						'sign_off'     => bzalpha_get_field( 'sign_off', $order->ID ),
					];
				}

				return $orders;
			}
		] );
	}
}
