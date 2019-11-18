<?php

namespace BZAlpha\REST_API\Controllers;

/**
 * Principal Controller.
 */
class Principal extends \WP_REST_Terms_Controller {

	/**
	 * Constructor.
	 */
	public function __construct( $taxonomy ) {
		parent::__construct( $taxonomy );

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
			'country',
		];

		foreach ( $meta_fields as $meta_name ) {
			register_rest_field( 'principal', $meta_name, [
				'schema'       => null,
				'get_callback' => function( $taxonomy ) use ( $meta_name ) {
					return get_field( $meta_name, $taxonomy['taxonomy'] . '_' . $taxonomy['id'] );
				},
				'update_callback' => function( $value, $taxonomy, $meta_name ) {
					/**
					 * Filter values before updating field.
					 */
					$value = apply_filters( "update_principal_{$meta_name}", $value, $taxonomy );

					return update_field( $meta_name, $value, $taxonomy );
				}
			] );
		}
	}
}
