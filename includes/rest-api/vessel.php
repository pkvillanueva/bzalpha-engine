<?php

namespace BZAlpha\REST_API;

/**
 * Vessel Controller.
 */
class Vessel extends Posts_Base {

	/**
	 * Constructor.
	 */
	public function __construct( $post_type ) {
		parent::__construct( $post_type );
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
							'key'     => 'status',
							'compare' => '=',
							'value'   => 'pending',
						],
						[
							'key'     => 'status',
							'compare' => '=',
							'value'   => 'processing',
						],
						[
							'key'     => 'status',
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
						'id'   => $order->ID,
						'meta' => [
							'position' => get_post_meta( $order->ID, 'position', true ),
							'status'   => get_post_meta( $order->ID, 'status', true ),
							'sign_off' => get_post_meta( $order->ID, 'sign_off', true ),
						]
					];
				}

				return $orders;
			}
		] );
	}

	/**
	 * Get meta schema.
	 */
	public function get_meta_schema() {
		$meta = [];

		$meta['type'] = [
			'single'       => true,
			'type'         => 'string',
			'show_in_rest' => true,
		];

		$meta['flag'] = [
			'single'       => true,
			'type'         => 'string',
			'show_in_rest' => true,
		];

		$meta['year_built'] = [
			'single'       => true,
			'type'         => 'string',
			'show_in_rest' => true,
		];

		$meta['imo'] = [
			'single'       => true,
			'type'         => 'integer',
			'show_in_rest' => true,
		];

		$meta['grt'] = [
			'single'       => true,
			'type'         => 'integer',
			'show_in_rest' => true,
		];

		$meta['dwt'] = [
			'single'       => true,
			'type'         => 'integer',
			'show_in_rest' => true,
		];

		$meta['hp'] = [
			'single'       => true,
			'type'         => 'integer',
			'show_in_rest' => true,
		];

		$meta['kw'] = [
			'single'       => true,
			'type'         => 'integer',
			'show_in_rest' => true,
		];

		$meta['engine'] = [
			'single'       => true,
			'type'         => 'string',
			'show_in_rest' => true,
		];

		return $meta;
	}
}
