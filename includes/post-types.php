<?php

namespace BZAlpha\Post_Types;

/**
 * Setup post types.
 */
function setup() {
    add_action( 'init', __NAMESPACE__ . '\register_seaman' );
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
		'supports'              => [ 'title', 'author', 'thumbnail' ],
		'menu_icon'             => 'dashicons-businessperson',
		'show_in_rest'          => true,
		'rest_controller_class' => '\BZAlpha\REST_API\Controllers\Seaman',
    ];

	register_post_type( 'seaman', $args );
}
