<?php

namespace BZAlpha\Post_Types;

/**
 * Setup post types.
 */
function setup() {
    add_action( 'init', __NAMESPACE__ . '\register_seaman' );
    add_action( 'init', __NAMESPACE__ . '\register_vessel' );
	add_action( 'init', __NAMESPACE__ . '\register_bz_order' );
	add_action( 'init', __NAMESPACE__ . '\register_principal' );
}

/**
 * Seaman post type.
 */
function register_seaman() {
    $labels = [
		'name'               => _x( 'Seaman', 'post type general name', 'bzalpha' ),
		'singular_name'      => _x( 'Seaman', 'post type singular name', 'bzalpha' ),
		'menu_name'          => _x( 'Seaman', 'admin menu', 'bzalpha' ),
		'name_admin_bar'     => _x( 'Seaman', 'add new on admin bar', 'bzalpha' ),
		'add_new'            => _x( 'Add New', 'seaman', 'bzalpha' ),
		'add_new_item'       => __( 'Add New Seaman', 'bzalpha' ),
		'new_item'           => __( 'New Seaman', 'bzalpha' ),
		'edit_item'          => __( 'Edit Seaman', 'bzalpha' ),
		'view_item'          => __( 'View Seaman', 'bzalpha' ),
		'all_items'          => __( 'All Seaman', 'bzalpha' ),
		'search_items'       => __( 'Search Seaman', 'bzalpha' ),
		'parent_item_colon'  => __( 'Parent Seaman:', 'bzalpha' ),
		'not_found'          => __( 'No seaman found.', 'bzalpha' ),
		'not_found_in_trash' => __( 'No seaman found in Trash.', 'bzalpha' )
    ];

	$args = [
		'labels'                => $labels,
		'description'           => __( 'Description.', 'bzalpha' ),
		'public'                => false,
		'publicly_queryable'    => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'query_var'             => true,
		'rewrite'               => [ 'slug' => 'seaman' ],
		'capability_type'       => 'post',
		'has_archive'           => true,
		'hierarchical'          => false,
		'menu_position'         => null,
		'supports'              => [ 'title', 'author', 'thumbnail', 'custom-fields' ],
		'menu_icon'             => 'dashicons-businessperson',
		'show_in_rest'          => true,
		'rest_controller_class' => '\BZAlpha\REST_API\Seaman',
    ];

	register_post_type( 'seaman', $args );
}

/**
 * Vessel post type.
 */
function register_vessel() {
	$labels = [
		'name'               => _x( 'Vessels', 'post type general name', 'bzalpha' ),
		'singular_name'      => _x( 'Vessel', 'post type singular name', 'bzalpha' ),
		'menu_name'          => _x( 'Vessels', 'admin menu', 'bzalpha' ),
		'name_admin_bar'     => _x( 'Vessel', 'add new on admin bar', 'bzalpha' ),
		'add_new'            => _x( 'Add New', 'vessel', 'bzalpha' ),
		'add_new_item'       => __( 'Add New Vessel', 'bzalpha' ),
		'new_item'           => __( 'New Vessel', 'bzalpha' ),
		'edit_item'          => __( 'Edit Vessel', 'bzalpha' ),
		'view_item'          => __( 'View Vessel', 'bzalpha' ),
		'all_items'          => __( 'All Vessels', 'bzalpha' ),
		'search_items'       => __( 'Search Vessels', 'bzalpha' ),
		'parent_item_colon'  => __( 'Parent Vessels:', 'bzalpha' ),
		'not_found'          => __( 'No vessels found.', 'bzalpha' ),
		'not_found_in_trash' => __( 'No vessels found in Trash.', 'bzalpha' )
	];

	$args = [
		'labels'                => $labels,
		'description'           => __( 'Description.', 'bzalpha' ),
		'public'                => true,
		'publicly_queryable'    => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'query_var'             => true,
		'rewrite'               => [ 'slug' => 'vessel' ],
		'capability_type'       => 'post',
		'has_archive'           => true,
		'hierarchical'          => false,
		'menu_position'         => null,
		'supports'              => [ 'title', 'author', 'custom-fields' ],
		'show_in_rest'          => true,
		'rest_controller_class' => '\BZAlpha\REST_API\Vessel',
	];

	register_post_type( 'vessel', $args );
}

/**
 * Principal taxonomy.
 */
function register_principal() {
	$tax_labels = [
        'name'                       => _x( 'Principals', 'taxonomy general name', 'bzalpha' ),
        'singular_name'              => _x( 'Principal', 'taxonomy singular name', 'bzalpha' ),
        'search_items'               => __( 'Search Principals', 'bzalpha' ),
        'popular_items'              => __( 'Popular Principals', 'bzalpha' ),
        'all_items'                  => __( 'All Principals', 'bzalpha' ),
        'parent_item'                => null,
        'parent_item_colon'          => null,
        'edit_item'                  => __( 'Edit Principal', 'bzalpha' ),
        'update_item'                => __( 'Update Principal', 'bzalpha' ),
        'add_new_item'               => __( 'Add New Principal', 'bzalpha' ),
        'new_item_name'              => __( 'New Principal Name', 'bzalpha' ),
        'separate_items_with_commas' => __( 'Separate principals with commas', 'bzalpha' ),
        'add_or_remove_items'        => __( 'Add or remove principals', 'bzalpha' ),
        'choose_from_most_used'      => __( 'Choose from the most used principals', 'bzalpha' ),
        'not_found'                  => __( 'No principals found.', 'bzalpha' ),
        'menu_name'                  => __( 'Principals', 'bzalpha' ),
	];

    $tax_args = [
        'hierarchical'          => false,
        'labels'                => $tax_labels,
        'show_ui'               => true,
        'show_admin_column'     => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var'             => true,
		'rewrite'               => [ 'slug' => 'principal' ],
		'show_in_rest'          => true,
		'rest_controller_class' => '\BZAlpha\REST_API\Principal',
	];

    register_taxonomy( 'principal', 'vessel', $tax_args );
}

/**
 * Order post type.
 */
function register_bz_order() {
	$labels = [
		'name'               => _x( 'Orders', 'post type general name', 'bzalpha' ),
		'singular_name'      => _x( 'Order', 'post type singular name', 'bzalpha' ),
		'menu_name'          => _x( 'Orders', 'admin menu', 'bzalpha' ),
		'name_admin_bar'     => _x( 'Order', 'add new on admin bar', 'bzalpha' ),
		'add_new'            => _x( 'Add New', 'order', 'bzalpha' ),
		'add_new_item'       => __( 'Add New Order', 'bzalpha' ),
		'new_item'           => __( 'New Order', 'bzalpha' ),
		'edit_item'          => __( 'Edit Order', 'bzalpha' ),
		'view_item'          => __( 'View Order', 'bzalpha' ),
		'all_items'          => __( 'All Orders', 'bzalpha' ),
		'search_items'       => __( 'Search Orders', 'bzalpha' ),
		'parent_item_colon'  => __( 'Parent Orders:', 'bzalpha' ),
		'not_found'          => __( 'No orders found.', 'bzalpha' ),
		'not_found_in_trash' => __( 'No orders found in Trash.', 'bzalpha' )
	];

	$args = [
		'labels'                => $labels,
		'description'           => __( 'Description.', 'bzalpha' ),
		'public'                => true,
		'publicly_queryable'    => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'query_var'             => true,
		'rewrite'               => [ 'slug' => 'bz-order' ],
		'capability_type'       => 'post',
		'has_archive'           => true,
		'hierarchical'          => false,
		'menu_position'         => null,
		'supports'              => [ 'title', 'author', 'custom-fields' ],
		'show_in_rest'          => true,
		'rest_base'             => 'bz-order',
		'rest_controller_class' => '\BZAlpha\REST_API\BZ_Order',
	];

	register_post_type( 'bz_order', $args );
}
