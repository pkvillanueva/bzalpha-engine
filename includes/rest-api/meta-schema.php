<?php

namespace BZAlpha\REST_API;

/**
 * Meta schema class.
 */
class Meta_Schema {

	/**
	 * Instance arguments.
	 */
	protected $args = [];

	/**
	 * Store schema.
	 */
	protected $schema = [];

	/**
	 * Store namespace.
	 */
	protected $namespace = '';

	/**
	 * Disabling rest object request.
	 */
	public $no_rest_object = false;

	/**
	 * Constructor.
	 */
	public function __construct( $object_type ) {
		if ( post_type_exists( $object_type ) ) {
			$this->args['post_type'] = $object_type;
		} elseif ( taxonomy_exists( $object_type ) ) {
			$this->args['taxonomy'] = $object_type;
		} else {
			$this->args['object_type'] = $object_type;
		}

		$this->namespace   = 'bzalpha/v1';
	}

	/**
	 * Register meta schema.
	 */
	public function register_meta_schema( $schema ) {
		if ( empty( $schema ) ) {
			return;
		}

		foreach ( $schema as $meta_key => $args ) {
			if ( isset( $this->args['post_type'] ) ) {
				register_post_meta( $this->args['post_type'], $meta_key, $args );
			} elseif ( isset( $this->args['taxonomy'] ) ) {
				register_term_meta( $this->args['taxonomy'], $meta_key, $args );
			} elseif ( isset( $this->args['object_type'] ) ) {
				register_meta( $this->args['object_type'], $meta_key, $args );
			}
		}

		$this->schema = $schema;
	}

	/**
	 * Get rest object.
	 */
	public function prepare_rest_object( $value ) {
		if ( empty( $value ) || ! $this->post_exists( $value ) ) {
			return null;
		} elseif ( $this->no_rest_object ) {
			return intval( $value );
		}

		// Disable rest object.
		$this->no_rest_object = true;

		$post_type = str_replace( '_', '-', get_post_type( $value ) );
		$route     = "/{$this->namespace}/{$post_type}/{$value}";
		$response  = rest_do_request( new \WP_REST_Request( 'GET', $route ) );
		$status    = $response->status;
		$data      = $response->data;

		// Enable rest object.
		$this->no_rest_object = false;

		if ( $status !== 200 ) {
			return $value;
		}

		return $data;
	}

	/**
	 * Validate post.
	 */
	protected function post_exists( $id ) {
		return is_string( get_post_status( $id ) );
	}
}
