<?php

namespace BZAlpha\REST_API;

/**
 * Seaman Controller.
 */
class Seaman extends Posts_Base {

	/**
	 * Constructor.
	 */
	public function __construct( $post_type ) {
		parent::__construct( $post_type );

		$this->register_rest_fields();
		add_filter( 'rest_seaman_query', [ $this, 'query' ], 10, 2 );
	}

	/**
	 * Register rest fields.
	 */
	public function register_rest_fields() {
		register_rest_field( 'seaman', 'avatar', [
			'schema'       => null,
			'get_callback' => function() {
				return wp_get_attachment_image_url( get_post_thumbnail_id(), 'medium' );
			}
		] );

		register_rest_field( 'seaman', 'featured_image', [
			'update_callback' => function( $value, $post, $meta_name ) {
				return set_post_thumbnail( $post->ID, $value );
			}
		] );
	}

	/**
	 * Filter search query.
	 */
	public function query( $args, $request ) {
		if ( isset( $request['job_status'] ) ) {
			$args['meta_key'] = 'job_status';
			$args['meta_value'] = $request['job_status'];
		}

		return $args;
	}

	/**
	 * Get meta schema.
	 */
	public function get_meta_schema() {
		$meta = [];

		$meta['first_name'] = [
			'single'       => true,
			'type'         => 'string',
			'show_in_rest' => true,
		];

		$meta['middle_name'] = [
			'single'       => true,
			'type'         => 'string',
			'show_in_rest' => true,
		];

		$meta['last_name'] = [
			'single'       => true,
			'type'         => 'string',
			'show_in_rest' => true,
		];

		$meta['birth_date'] = [
			'single'       => true,
			'type'         => 'string',
			'show_in_rest' => true,
		];

		$meta['birth_place'] = [
			'single'       => true,
			'type'         => 'string',
			'show_in_rest' => true,
		];

		$meta['nationality'] = [
			'single'       => true,
			'type'         => 'string',
			'show_in_rest' => true,
		];

		$meta['gender'] = [
			'single'       => true,
			'type'         => 'string',
			'show_in_rest' => true,
		];

		$meta['marital_status'] = [
			'single'       => true,
			'type'         => 'string',
			'show_in_rest' => true,
		];

		$meta['address'] = [
			'single'       => true,
			'type'         => 'string',
			'show_in_rest' => true,
		];

		$meta['city'] = [
			'single'       => true,
			'type'         => 'string',
			'show_in_rest' => true,
		];

		$meta['state'] = [
			'single'       => true,
			'type'         => 'string',
			'show_in_rest' => true,
		];

		$meta['zip'] = [
			'single'       => true,
			'type'         => 'string',
			'show_in_rest' => true,
		];

		$meta['country'] = [
			'single'       => true,
			'type'         => 'string',
			'show_in_rest' => true,
		];

		$meta['telephone'] = [
			'single'       => true,
			'type'         => 'string',
			'show_in_rest' => true,
		];

		$meta['phone'] = [
			'single'       => true,
			'type'         => 'string',
			'show_in_rest' => true,
		];

		$meta['skype'] = [
			'single'       => true,
			'type'         => 'string',
			'show_in_rest' => true,
		];

		$meta['email'] = [
			'single'       => true,
			'type'         => 'string',
			'show_in_rest' => true,
		];

		$meta['date_available'] = [
			'single'       => true,
			'type'         => 'string',
			'show_in_rest' => true,
		];

		$meta['rank'] = [
			'single'       => true,
			'type'         => 'string',
			'show_in_rest' => true,
		];

		$meta['min_wage'] = [
			'single'       => true,
			'type'         => 'integer',
			'show_in_rest' => true,
		];

		$meta['hair_color'] = [
			'single'       => true,
			'type'         => 'string',
			'show_in_rest' => true,
		];

		$meta['height'] = [
			'single'       => true,
			'type'         => 'integer',
			'show_in_rest' => true,
		];

		$meta['collar_size'] = [
			'single'       => true,
			'type'         => 'integer',
			'show_in_rest' => true,
		];

		$meta['eyes_color'] = [
			'single'       => true,
			'type'         => 'string',
			'show_in_rest' => true,
		];

		$meta['shoes_size'] = [
			'single'       => true,
			'type'         => 'integer',
			'show_in_rest' => true,
		];

		$meta['weight'] = [
			'single'       => true,
			'type'         => 'integer',
			'show_in_rest' => true,
		];

		$meta['waist_size'] = [
			'single'       => true,
			'type'         => 'integer',
			'show_in_rest' => true,
		];

		$meta['educations'] = [
			'single'       => true,
			'type'         => 'array',
			'show_in_rest' => [
				'schema' => [
					'type'  => 'array',
					'items' => [
						'type'       => 'object',
						'properties' => [
							'level'  => [ 'type' => 'string' ],
							'school' => [ 'type' => 'string' ],
							'from'   => [ 'type' => 'string' ],
							'to'     => [ 'type' => 'string' ],
						],
					],
				],
			],
		];

		$meta['passports'] = [
			'single'       => true,
			'type'         => 'array',
			'show_in_rest' => [
				'schema' => [
					'type'  => 'array',
					'items' => [
						'type'       => 'object',
						'properties' => [
							'type'       => [ 'type' => 'string' ],
							'num'        => [ 'type' => 'string' ],
							'issue_date' => [ 'type' => 'string' ],
							'valid_till' => [ 'type' => 'string' ],
							'issued_by'  => [ 'type' => 'string' ],
							'file'       => [ 'type' => 'integer' ],
						],
					],
				],
			],
		];

		$meta['visas'] = [
			'single'       => true,
			'type'         => 'array',
			'show_in_rest' => [
				'schema' => [
					'type'  => 'array',
					'items' => [
						'type'       => 'object',
						'properties' => [
							'type'       => [ 'type' => 'string' ],
							'num'        => [ 'type' => 'string' ],
							'issue_date' => [ 'type' => 'string' ],
							'valid_till' => [ 'type' => 'string' ],
							'issued_by'  => [ 'type' => 'string' ],
							'file'       => [ 'type' => 'integer' ],
						],
					],
				],
			],
		];

		$meta['relatives'] = [
			'single'       => true,
			'type'         => 'array',
			'show_in_rest' => [
				'schema' => [
					'type'  => 'array',
					'items' => [
						'type'       => 'object',
						'properties' => [
							'first_name' => [ 'type' => 'string' ],
							'last_name'  => [ 'type' => 'string' ],
							'contact'    => [ 'type' => 'string' ],
							'kin'        => [ 'type' => 'string' ],
						],
					],
				],
			],
		];

		$meta['licenses'] = [
			'single'       => true,
			'type'         => 'array',
			'show_in_rest' => [
				'schema' => [
					'type'  => 'array',
					'items' => [
						'type'       => 'object',
						'properties' => [
							'name'        => [ 'type' => 'string' ],
							'num'         => [ 'type' => 'string' ],
							'issue_date'  => [ 'type' => 'string' ],
							'valid_until' => [ 'type' => 'string' ],
							'issued_by'   => [ 'type' => 'string' ],
							'file'        => [ 'type' => 'integer' ],
						],
					],
				],
			],
		];

		$meta['experiences'] = [
			'single'       => true,
			'type'         => 'array',
			'show_in_rest' => [
				'schema' => [
					'type'  => 'array',
					'items' => [
						'type'       => 'object',
						'properties' => [
							'date_start'      => [ 'type' => 'string' ],
							'date_end'        => [ 'type' => 'string' ],
							'rank'            => [ 'type' => 'string' ],
							'vessel'          => [ 'type' => 'string' ],
							'type'            => [ 'type' => 'string' ],
							'flag'            => [ 'type' => 'string' ],
							'owner'           => [ 'type' => 'string' ],
							'engine'          => [ 'type' => 'string' ],
							'end_of_contract' => [ 'type' => 'string' ],
							'grt'             => [ 'type' => 'integer' ],
							'dwt'             => [ 'type' => 'integer' ],
							'imo'             => [ 'type' => 'integer' ],
							'hp'              => [ 'type' => 'integer' ],
							'kw'              => [ 'type' => 'integer' ],
						],
					],
				],
			],
		];

		return $meta;
	}
}