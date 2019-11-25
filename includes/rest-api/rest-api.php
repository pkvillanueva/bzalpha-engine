<?php

namespace BZAlpha\REST_API;

/**
 * Setup overrides.
 */
function setup() {
    add_filter( 'rest_authentication_errors', __NAMESPACE__ . '\rest_auth' );

    // Include files.
    $rest_includes = BZALPHA_INC . 'rest-api/';
    require_once $rest_includes . 'core/works.php';
    require_once $rest_includes . 'controllers/seaman.php';
    require_once $rest_includes . 'controllers/vessel.php';
    require_once $rest_includes . 'controllers/principal.php';
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
