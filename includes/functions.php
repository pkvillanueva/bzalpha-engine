<?php
/**
 * Get fields map.
 */
function bzalpha_get_fields_map( $post_type = '' ) {
	global $bzalpha_fields;

	if ( is_numeric( $post_type ) ) {
		$post_type = get_post_type( $post_type );
	} elseif ( empty( $post_type ) ) {
		$post_type = get_post_type( get_the_ID() );
	}

	if ( isset( $bzalpha_fields[ $post_type ] ) ) {
		return $bzalpha_fields[ $post_type ];
	}

	if ( empty( $post_type ) ) {
		return [];
	}

	add_filter( 'acf/location/rule_match', 'bzalpha_acf_rule_match', 10, 3 );

	$groups = acf_get_field_groups( [
		'post_type' => $post_type,
	] );

	remove_filter( 'acf/location/rule_match', 'bzalpha_acf_rule_match', 10 );

	$fields = [];

	if ( $groups ) {
		$fields = acf_get_fields( $groups[0]['key'] );

		if ( $fields ) {
			$fields = array_column( $fields, 'key', 'name' );
			unset( $fields[''] );
		}
	}

	$bzalpha_fields[ $post_type ] = $fields;

	return $fields;
}

/**
 * Get field key by selector name.
 */
function bzalpha_get_field_key( $selector, $post_id = '' ) {
	$fields = bzalpha_get_fields_map( $post_id );

	if ( ! isset( $fields[ $selector ] ) ) {
		return false;
	}

	return $fields[ $selector ];
}

/**
 * Get field value.
 */
function bzalpha_get_field( $selector, $post_id = '' ) {
	if ( empty( $post_id ) ) {
		$post_id = get_the_ID();
	}

	if ( stripos( $selector, 'field_' ) === false ) {
		$field_key = bzalpha_get_field_key( $selector, $post_id );

		if ( ! $field_key ) {
			return null;
		}
	} else {
		$field_key = $selector;
	}

	return get_field( $field_key, $post_id );
}

/**
 * Get field value.
 */
function bzalpha_update_field( $selector, $value, $post_id = '' ) {
	if ( empty( $post_id ) ) {
		$post_id = get_the_ID();
	}

	if ( stripos( $selector, 'field_' ) === false ) {
		$field_key = bzalpha_get_field_key( $selector, $post_id );

		if ( ! $field_key ) {
			return null;
		}
	} else {
		$field_key = $selector;
	}

	return update_field( $field_key, $value, $post_id );
}

/**
 * Filter rule match.
 */
function bzalpha_acf_rule_match( $result, $rule, $screen ) {
	if ( $rule['param'] === 'post_type' && $rule['value'] === $screen['post_type'] ) {
		return true;
	} elseif ( $rule['param'] === 'taxonomy' && $rule['value'] === $screen['post_type'] ) {
		return true;
	}

	return $result;
}

