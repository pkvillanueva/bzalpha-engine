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
	}
}
