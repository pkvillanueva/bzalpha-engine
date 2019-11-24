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

		// Custom namespace.
		$this->namespace = 'bzalpha/v1';

		// Custom rest fields.
		$this->register_rest_fields();

		// Filter data before save.
		$this->register_filters();
	}

	/**
	 * Register rest fields.
	 */
	public function register_rest_fields() {
		$meta_fields = [
			// Job
			'job_status',
			'vessel',
			'signed_on',
			'signed_off',
			'rank',
			'min_wage',

			// Personal
			'first_name',
			'last_name',
			'middle_name',
			'birth_date',
			'birth_place',
			'nationality',
			'gender',
			'marital_status',
			'address',
			'city',
			'state',
			'zip',
			'country',
			'tel',
			'phone',
			'skype',
			'email',
			'relatives',
			'hair_color',
			'height',
			'collar_size',
			'eyes_color',
			'shoes_size',
			'weight',
			'waist_size',

			// Others
			'educations',
			'passports',
			'visas',
			'experiences',
			'banks',
			'documents',
		];

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

		foreach ( $meta_fields as $meta_name ) {
			register_rest_field( 'seaman', $meta_name, [
				'schema'       => null,
				'get_callback' => function() use ( $meta_name ) {
					return get_field( $meta_name );
				},
				'update_callback' => function( $value, $post, $meta_name ) {
					/**
					 * Filter values before updating field.
					 */
					$value = apply_filters( "update_seaman_{$meta_name}", $value, $post );

					return update_field( $meta_name, $value, $post->ID );
				}
			] );
		}
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
