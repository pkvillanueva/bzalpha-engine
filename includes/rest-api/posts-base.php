<?php

namespace BZAlpha\REST_API;

/**
 * Posts Base Controller.
 */
class Posts_Base extends \WP_REST_Posts_Controller {

	/**
	 * Meta schema class instance.
	 */
	public $meta_schema;

	/**
	 * Constructor.
	 */
	public function __construct( $post_type ) {
		parent::__construct( $post_type );
		$this->namespace   = 'bzalpha/v1';
		$this->meta_schema = new Meta_Schema( $post_type, $this->get_meta_schema() );

		add_filter( "rest_prepare_{$this->post_type}", [ $this, 'prepare_post' ] );
	}

	/**
	 * Get meta schema.
	 */
	public function get_meta_schema() {
		return [];
	}

	/**
	 * Prepare post.
	 */
	public function prepare_post( $response ) {
		$data = $response->data;

		$data['title'] = $response->data['title']['rendered'];

		$map_unset = [
			'date',
			'date_gmt',
			'guid',
			'modified',
			'modified_gmt',
			'slug',
			'link',
			'type',
			'link',
			'author',
			'template',
			'status',
			'permalink_template',
			'generated_slug',
			'password',
		];

		foreach ( $map_unset as $var ) {
			unset( $data[ $var ] );
		}

		return $data;
	}
}
