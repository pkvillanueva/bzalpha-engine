<?php

namespace BZAlpha\REST_API\Controllers;

/**
 * Seaman Controller.
 */
class Seaman extends \WP_REST_Posts_Controller {

	/**
	 * Constructor.
	 */
	public function __construct( $post_type ) {
		parent::__construct( $post_type );
		$this->namespace = 'bzalpha/v1';

		// Register actions.
		$this->register_rest_fields();
		$this->register_filters();
	}

	/**
	 * Register rest fields.
	 */
	public function register_rest_fields() {
		register_rest_field( 'seaman', 'avatar', [
			'schema'       => null,
			'get_callback' => function() {
				return wp_get_attachment_image_url( get_post_thumbnail_id(), 'medium' );
			}
		] );

		register_rest_field( 'seaman', 'featured_image', [
			'update_callback' => function( $value, $post, $meta_name ) {
				return set_post_thumbnail( $post->ID, $value );
			}
		] );
	}

	/**
	 * Register filters.
	 */
	public function register_filters() {
		add_filter( 'rest_seaman_query', [ $this, '_search_query' ], 10, 2 );
	}

	/**
	 * Filter search query.
	 */
	public function _search_query( $args, $request ) {
		if ( isset( $request['job_status'] ) ) {
			$args['meta_key'] = 'job_status';
			$args['meta_value'] = $request['job_status'];
		}

		return $args;
	}
}
