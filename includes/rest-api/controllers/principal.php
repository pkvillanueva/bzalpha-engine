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
		$this->namespace = 'bzalpha/v1';
	}
}
