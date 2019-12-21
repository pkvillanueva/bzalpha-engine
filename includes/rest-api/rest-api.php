<?php

namespace BZAlpha\REST_API;

/**
 * Setup overrides.
 */
function setup() {
	add_filter( 'rest_authentication_errors', __NAMESPACE__ . '\rest_auth' );
	add_action( 'rest_api_init', __NAMESPACE__ . '\rest_fields', 10 );

    // Include files.
    $rest_includes = BZALPHA_INC . 'rest-api/';
    require_once $rest_includes . 'core/works.php';
    require_once $rest_includes . 'controllers/seaman.php';
    require_once $rest_includes . 'controllers/vessel.php';
    require_once $rest_includes . 'controllers/principal.php';
    require_once $rest_includes . 'controllers/bz-order.php';
}

/**
 * Limit REST access.
 */
function rest_auth( $result ) {
    if ( ! empty( $result ) ) {
        return $result;
	}

	$route = untrailingslashit( $GLOBALS['wp']->query_vars['rest_route'] );

	// Only allow JWT auth routes publicly.
	if ( false !== strpos( $route, '/jwt-auth' ) ) {
		return $result;
	}

	if ( $_SERVER['REQUEST_METHOD'] === 'GET' && ! is_user_logged_in() ) {
		return new \WP_Error( 'rest_forbidden', __( 'Sorry, you are not allowed to do that.', 'bzalpha' ), [ 'status' => rest_authorization_required_code() ] );
	}

    return $result;
};

/**
 * Set forbidden.
 */
function rest_forbidden() {
	return new \WP_Error( 'rest_forbidden', __( 'Sorry, you are not allowed to do that.', 'bzalpha' ), [ 'status' => rest_authorization_required_code() ] );
}

/**
 * Register rest fields in post types.
 */
function rest_fields() {
	if ( ! function_exists( 'acf' ) ) {
		return;
	}

	$post_types = [
		'bz_order',
		'vessel',
		'seaman',
	];

	foreach ( $post_types as $post_type ) {
		$fields = \bzalpha_get_fields_map( $post_type );

		if ( $fields ) {
			foreach ( $fields as $meta_name => $field_key ) {
				register_rest_field( $post_type, $meta_name, [
					'schema'       => null,
					'get_callback' => function() use ( $field_key ) {
						return \bzalpha_get_field( $field_key );
					},
					'update_callback' => function( $value, $post ) use ( $field_key ) {
						return \bzalpha_update_field( $field_key, $value, $post->ID );
					}
				] );
			}
		}
	}

	$taxonomies = [
		'principal',
	];

	foreach ( $taxonomies as $tax_name ) {
		$fields = \bzalpha_get_fields_map( $tax_name );

		if ( $fields ) {
			foreach ( $fields as $meta_name => $field_key ) {
				register_rest_field( $tax_name, $meta_name, [
					'schema'       => null,
					'get_callback' => function( $taxonomy ) use ( $field_key ) {
						return \bzalpha_get_field( $field_key, $taxonomy['taxonomy'] . '_' . $taxonomy['id'] );
					},
					'update_callback' => function( $value, $taxonomy ) use ( $field_key ) {
						return \bzalpha_update_field( $field_key, $value, $taxonomy );
					}
				] );
			}
		}
	}
}
