<?php

namespace BZAlpha\REST_API;

/**
 * Media controller.
 */
class Media {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'rest_prepare_attachment', [ $this, 'prepare_post' ], 15 );
	}

	/**
	 * Prepare post.
	 */
	public function prepare_post( $response ) {
		$data = $response->data;

		if ( isset( $data['title']['rendered'] ) ) {
			$data['title'] = $data['title']['rendered'];
		}

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
			'comment_status',
			'description',
			'ping_status',
			'post',
			'caption',
		];

		foreach ( $map_unset as $var ) {
			unset( $data[ $var ] );
		}

		$response->set_data( $data );

		return $response;
	}
}
