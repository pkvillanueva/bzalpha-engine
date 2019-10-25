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
	}

	/**
	 * Register rest fields.
	 */
	public function register_rest_fields() {
		$meta_fields = [
			'first_name',
			'last_name',
			'middle_name',
			'birth_date',
			'birth_place',
			'nationality',
			'address',
			'city',
			'state',
			'zip',
			'country',
			'tel',
			'phone',
			'skype',
			'job_status',
			'branch',
			'rank',
			'prev_rank',
			'min_wage',
			'relatives',
			'hair_color',
			'height',
			'collar_size',
			'eyes_color',
			'shoes_size',
			'weight',
			'waist_size',
			'contacts',
			'educations',
			'passports',
			'visas',
			'experiences',
			'banks',
			'refs',
			'documents',
		];

		register_rest_field( 'seaman', 'avatar', [
			'schema'       => null,
			'get_callback' => function() {
				return wp_get_attachment_image_url( get_post_thumbnail_id(), 'thumbnail' );
			}
		] );

		foreach ( $meta_fields as $meta_name ) {
			register_rest_field( 'seaman', $meta_name, [
				'schema'       => null,
				'get_callback' => function() use ( $meta_name ) {
					return get_field( $meta_name );
				},
				'update_callback' => function( $value, $post, $meta_name ) {
					return update_field( $meta_name, $value, $post->ID );
				}
			] );
		}
	}
}
