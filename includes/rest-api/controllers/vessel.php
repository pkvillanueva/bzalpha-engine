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
			'type',
			'flag',
			'imo',
			'mmsi',
			'grt',
			'dwt',
			'hp',
			'kw',
			'engine',
		];

		foreach ( $meta_fields as $meta_name ) {
			register_rest_field( 'vessel', $meta_name, [
				'schema'       => null,
				'get_callback' => function() use ( $meta_name ) {
					return get_field( $meta_name );
				},
				'update_callback' => function( $value, $post, $meta_name ) {
					/**
					 * Filter values before updating field.
					 */
					$value = apply_filters( "bzalpha_update_vessel_{$meta_name}", $value, $post );

					return update_field( $meta_name, $value, $post->ID );
				}
			] );
		}

		register_rest_field( 'vessel', 'principal', [
			'schema'       => [],
			'get_callback' => function( $post ) {
				if ( ! isset( $post['principal'] ) || empty( $post['principal'] ) ) {
					return [];
				}

				$principal = array_map( function( $id ) use ( $post ) {
					$term = get_term( $id, '', ARRAY_A );
					$term['id'] = $id;

					$custom_fields = get_fields( 'principal_' . $id );
					if ( ! empty( $custom_fields ) ) {
						$term = array_merge( $term, $custom_fields );
					}

					return $term;
				}, $post['principal'] );

				return $principal;
			},
		] );
	}
}
