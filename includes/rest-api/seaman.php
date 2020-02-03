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
		add_filter( 'rest_after_insert_seaman', [ $this, 'insert' ], 10, 2 );
	}

	/**
	 * Register rest fields.
	 */
	public function register_rest_fields() {
		register_rest_field( 'seaman', 'avatar', [
			'schema'       => null,
			'get_callback' => function( $post ) {
				$avatar = get_the_post_thumbnail_url( $post['id'], 'medium' );

				if ( ! $avatar ) {
					return '';
				}

				return $avatar;
			}
		] );

		register_rest_field( 'seaman', 'featured_image', [
			'update_callback' => function( $value, $post, $meta_name ) {
				return set_post_thumbnail( $post->ID, $value );
			}
		] );

		register_rest_field( 'seaman', 'order', [
			'schema'       => null,
			'get_callback' => function( $post ) {
				$meta_query = [
					'relation' => 'AND',
					[
						'key'     => 'seaman',
						'compare' => '=',
						'value'   => $post['id'],
					],
					[
						'relation' => 'OR',
						[
							'key'     => 'status',
							'compare' => '=',
							'value'   => 'processing',
						],
						[
							'key'     => 'status',
							'compare' => '=',
							'value'   => 'onboard',
						],
					]
				];

				$orders = get_posts( [
					'posts_per_page'   => 1,
					'post_type'        => 'bz_order',
					'meta_query'       => $meta_query,
					'orderby'          => 'ID',
					'order'            => 'ASC',
					'suppress_filters' => true,
				] );

				if ( empty( $orders ) ) {
					return [];
				}

				foreach ( $orders as $key => $order ) {
					$orders[ $key ] = [
						'id'   => $order->ID,
						'meta' => [
							'vessel' => get_post_meta( $order->ID, 'vessel', true ),
							'status' => get_post_meta( $order->ID, 'status', true ),
						]
					];
				}

				return array_shift( $orders );
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
	 * Filter after insert.
	 */
	public function insert( $post, $request ) {
		if ( isset( $request['meta']['first_name'] ) && isset( $request['meta']['last_name'] ) && isset( $request['meta']['middle_name'] ) ) {
			wp_update_post( [
				'ID'         => $post->ID,
				'post_title' => "{$request['meta']['last_name']} {$request['meta']['first_name']} {$request['meta']['middle_name']}",
			] );
		}
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

		$meta['tel'] = [
			'single'       => true,
			'type'         => 'string',
			'show_in_rest' => true,
		];

		$meta['phone'] = [
			'single'       => true,
			'type'         => 'string',
			'show_in_rest' => true,
		];

		$meta['phone_2'] = [
			'single'       => true,
			'type'         => 'string',
			'show_in_rest' => true,
		];

		$meta['phone_3'] = [
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

		$meta['overall_size'] = [
			'single'       => true,
			'type'         => 'string',
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
				'prepare_callback' => __NAMESPACE__ . '\prepare_callback_array_items',
				'schema'           => [
					'prepare_items' => [
						'rest_object' => [ 'file' ],
					],
					'type'          => 'array',
					'items'         => [
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
				'prepare_callback' => __NAMESPACE__ . '\prepare_callback_array_items',
				'schema'           => [
					'prepare_items' => [
						'rest_object' => [ 'file' ],
					],
					'type'          => 'array',
					'items'         => [
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
				'prepare_callback' => __NAMESPACE__ . '\prepare_callback_array_items',
				'schema'           => [
					'prepare_items' => [
						'rest_object' => [ 'file' ],
					],
					'type'          => 'array',
					'items'         => [
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
							'owner_country'   => [ 'type' => 'string' ],
							'crewing_agency'  => [ 'type' => 'string' ],
							'engine'          => [ 'type' => 'string' ],
							'year_built'      => [ 'type' => 'string' ],
							'end_of_contract' => [ 'type' => 'string' ],
							'currency'        => [ 'type' => 'string' ],
							'wage'            => [ 'type' => 'integer' ],
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
