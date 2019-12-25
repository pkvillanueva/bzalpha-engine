<?php

namespace BZAlpha\REST_API;

/**
 * Prepare callback rest object.
 */
function prepare_callback_rest_object( $object_id ) {
	global $_bzalpha_no_rest_object;
	$object_id = intval( $object_id );

	if ( empty( $object_id ) || ! post_exists( $object_id ) ) {
		return null;
	} elseif ( $_bzalpha_no_rest_object ) {
		return $object_id;
	}

	// Disable rest object.
	$_bzalpha_no_rest_object = true;

	$post_type = str_replace( '_', '-', get_post_type( $object_id ) );

	if ( $post_type === 'attachment' ) {
		$route = "/wp/v2/media/{$object_id}";
	} else {
		$route = "/bzalpha/v1/{$post_type}/{$object_id}";
	}

	$response  = rest_do_request( new \WP_REST_Request( 'GET', $route ) );
	$status    = $response->status;
	$data      = $response->data;

	// Enable rest object.
	$_bzalpha_no_rest_object = false;

	if ( $status !== 200 ) {
		return $object_id;
	}

	return $data;
}

/**
 * Prepare callback array items.
 */
function prepare_callback_array_items( $values, $request, $args ) {
	if ( ! is_array( $values ) ) {
		return $values;
	}

	if ( ! isset( $args['schema']['prepare_items'] ) ) {
		return $values;
	}

	if ( isset( $args['schema']['prepare_items']['rest_object'] ) ) {
		foreach ( $args['schema']['prepare_items']['rest_object'] as $key ) {
			foreach ( $values as $index => $value ) {
				if ( isset( $value[ $key ] ) ) {
					$values[ $index ][ $key ] = prepare_callback_rest_object( $value[ $key ] );
				}
			}
		}
	}

	return $values;
}

/**
 * Validate post.
 */
function post_exists( $id ) {
	return get_post_status( $id ) === 'publish';
}
