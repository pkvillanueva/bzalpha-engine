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
		$this->namespace = 'bzalpha/v1';

		// Register actions.
		add_filter( 'rest_bz_order_query', [ $this, 'hook_query' ], 10, 2 );
		add_filter( 'rest_after_insert_bz_order', [ $this, 'hook_insert' ], 10, 2 );
		add_filter( 'rest_delete_bz_order', [ $this, 'hook_delete' ], 10, 2 );
	}

	/**
	 * Filter search query.
	 */
	public function hook_query( $args, $request ) {
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
	public function hook_insert( $post, $request ) {
		if ( isset( $request['position'] ) ) {
			$position = $request['position'];

			wp_update_post( [
				'ID'         => $post->ID,
				'post_title' => "Order #{$post->ID} [{$position}]",
			] );
		}

		// 		update_field( 'child_order', $post->ID, $value );
		// 		update_field( 'candidates', [], $value );
		// 		return update_field( $meta_name, $value, $post->ID );
	}

	/**
	 * Filter delete.
	 */
	public function hook_delete( $post, $response ) {
		if ( ! isset( $response->data ) ) {
			return;
		}

		$data = $response->data;

		if ( $data['child_order'] ) {
			$child_id = intval( $data['child_order']['id'] );
			wp_trash_post( $child_id );
		}

		if ( $data['parent_order'] ) {
			$parent_id = intval( $data['parent_order'] );
			bzalpha_update_field( 'child_order', null, $parent_id );
		}
	}
}
