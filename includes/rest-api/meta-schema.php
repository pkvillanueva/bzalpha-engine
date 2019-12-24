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
	 * Constructor.
	 */
	public function __construct( $object_type, $schema = [] ) {
		if ( post_type_exists( $object_type ) ) {
			$this->args['post_type'] = $object_type;
		} elseif ( taxonomy_exists( $object_type ) ) {
			$this->args['taxonomy'] = $object_type;
		} else {
			$this->args['object_type'] = $object_type;
		}

		$this->schema = $schema;
		$this->register_meta_schema();
	}

	/**
	 * Register meta schema.
	 */
	protected function register_meta_schema() {
		if ( empty( $this->schema ) ) {
			return;
		}

		foreach ( $this->schema as $meta_key => $args ) {
			if ( isset( $this->args['post_type'] ) ) {
				register_post_meta( $this->args['post_type'], $meta_key, $args );
			} elseif ( isset( $this->args['taxonomy'] ) ) {
				register_term_meta( $this->args['taxonomy'], $meta_key, $args );
			} elseif ( isset( $this->args['object_type'] ) ) {
				register_meta( $this->args['object_type'], $meta_key, $args );
			}
		}
	}
}
