<?php

namespace BZAlpha\REST_API;

/**
 * BZ_Order Controller.
 */
class BZ_Order extends Posts_Base {

	/**
	 * Constructor.
	 */
	public function __construct( $post_type ) {
		parent::__construct( $post_type );

		add_filter( 'rest_bz_order_query', [ $this, 'query' ], 10, 2 );
		add_filter( 'rest_after_insert_bz_order', [ $this, 'insert' ], 10, 2 );
		add_filter( 'rest_delete_bz_order', [ $this, 'delete' ], 10, 2 );
	}

	/**
	 * Filter search query.
	 */
	public function query( $args, $request ) {
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
					[
						'key'     => 'status',
						'compare' => '=',
						'value'   => 'reserved',
					],
				]
			];
		}

		return $args;
	}

	/**
	 * Filter after insert.
	 */
	public function insert( $post, $request ) {
		if ( isset( $request['meta']['position'] ) ) {
			$position = $request['meta']['position'];

			wp_update_post( [
				'ID'         => $post->ID,
				'post_title' => "Order #{$post->ID} [{$position}]",
			] );
		}

		if ( isset( $request['meta']['parent_order'] ) ) {
			update_post_meta( $request['meta']['parent_order'], 'child_order', $post->ID );
			update_post_meta( $request['meta']['parent_order'], 'candidates', [] );
			update_post_meta( $post->ID, 'parent_order', $request['meta']['parent_order'] );
		}
	}

	/**
	 * Filter delete.
	 */
	public function delete( $post, $response ) {
		if ( $response['meta']['child_order'] ) {
			if ( 'reserved' === get_post_meta( $response['meta']['child_order'], 'status', true ) ) {
				wp_trash_post( $response['meta']['child_order'] );
			}
		}
	}

	/**
	 * Get meta schema.
	 */
	public function get_meta_schema() {
		$meta = [];

		$meta['status'] = [
			'single'       => true,
			'type'         => 'string',
			'show_in_rest' => true,
		];

		$meta['deadline'] = [
			'single'       => true,
			'type'         => 'string',
			'show_in_rest' => true,
		];

		$meta['position'] = [
			'single'       => true,
			'type'         => 'string',
			'show_in_rest' => true,
		];

		$meta['sign_on'] = [
			'single'       => true,
			'type'         => 'string',
			'show_in_rest' => true,
		];

		$meta['sign_off'] = [
			'single'       => true,
			'type'         => 'string',
			'show_in_rest' => true,
		];

		$meta['port'] = [
			'single'       => true,
			'type'         => 'string',
			'show_in_rest' => true,
		];

		$meta['return_port'] = [
			'single'       => true,
			'type'         => 'string',
			'show_in_rest' => true,
		];

		$meta['remark'] = [
			'single'       => true,
			'type'         => 'string',
			'show_in_rest' => true,
		];

		$meta['wage'] = [
			'single'       => true,
			'type'         => 'integer',
			'show_in_rest' => true,
		];

		$meta['currency'] = [
			'single'       => true,
			'type'         => 'string',
			'show_in_rest' => true,
		];

		$meta['contract_plus'] = [
			'single'       => true,
			'type'         => 'integer',
			'show_in_rest' => true,
		];

		$meta['contract_minus'] = [
			'single'       => true,
			'type'         => 'integer',
			'show_in_rest' => true,
		];

		$meta['uniform'] = [
			'single'       => true,
			'type'         => 'boolean',
			'show_in_rest' => true,
		];

		$meta['vessel'] = [
			'single'       => true,
			'type'         => 'integer',
			'show_in_rest' => [
				'prepare_callback' => __NAMESPACE__ . '\prepare_callback_rest_object',
			],
		];

		$meta['seaman'] = [
			'single'       => true,
			'type'         => 'integer',
			'show_in_rest' => [
				'prepare_callback' => __NAMESPACE__ . '\prepare_callback_rest_object',
			],
		];

		$meta['child_order'] = [
			'single'       => true,
			'type'         => 'integer',
			'show_in_rest' => true,
		];

		$meta['parent_order'] = [
			'single'       => true,
			'type'         => 'integer',
			'show_in_rest' => true,
		];

		$meta['candidates'] = [
			'single'       => true,
			'type'         => 'array',
			'show_in_rest' => [
				'prepare_callback' => __NAMESPACE__ . '\prepare_callback_array_items',
				'schema'           => [
					'prepare_items' => [
						'rest_object' => [ 'seaman' ],
					],
					'type'          => 'array',
					'items'         => [
						'type'       => 'object',
						'properties' => [
							'timestamp' => [ 'type' => 'string' ],
							'status'    => [ 'type' => 'string' ],
							'remark'    => [ 'type' => 'string' ],
							'type'      => [ 'type' => 'string' ],
							'seaman'    => [ 'type' => 'integer' ],
						],
					],
				],
			]
		];

		return $meta;
	}
}
