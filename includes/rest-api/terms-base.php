<?php

namespace BZAlpha\REST_API;

/**
 * Terms Base Controller.
 */
class Terms_Base extends \WP_REST_Terms_Controller {

	/**
	 * Meta schema class instance.
	 */
	public $meta_schema;

	/**
	 * Constructor.
	 */
	public function __construct( $taxonomy ) {
		parent::__construct( $taxonomy );
		$this->namespace   = 'bzalpha/v1';
		$this->meta_schema = new Meta_Schema( $taxonomy, $this->get_meta_schema() );

		add_filter( "rest_prepare_{$this->taxonomy}", [ $this, 'prepare_term' ] );
	}

	/**
	 * Get meta schema.
	 */
	public function get_meta_schema() {
		return [];
	}

	/**
	 * Prepare term.
	 */
	public function prepare_term( $response ) {
		$data = $response->data;

		$data['id'] = $response->data['id'];

		$map_unset = [
			'description',
			'link',
			'slug',
			'taxonomy',
		];

		foreach ( $map_unset as $var ) {
			unset( $data[ $var ] );
		}

		return $data;
	}
}
