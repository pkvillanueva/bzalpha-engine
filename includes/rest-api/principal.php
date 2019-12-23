<?php

namespace BZAlpha\REST_API;

/**
 * Principal Controller.
 */
class Principal extends Terms_Base {

	/**
	 * Constructor.
	 */
	public function __construct( $taxonomy ) {
		parent::__construct( $taxonomy );
	}

	/**
	 * Get meta schema.
	 */
	public function get_meta_schema() {
		$meta = [];

		$meta['country'] = [
			'single'       => true,
			'type'         => 'string',
			'show_in_rest' => true,
		];

		return $meta;
	}
}
