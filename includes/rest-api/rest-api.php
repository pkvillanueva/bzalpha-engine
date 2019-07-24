<?php

namespace BZAlpha\REST_API;

/**
 * Setup overrides.
 */
function setup() {
    add_filter( 'rest_authentication_errors', __NAMESPACE__ . '\rest_auth' );

    // Include files.
    $rest_includes = BZALPHA_INC . 'rest-api/';
    require_once $rest_includes . 'controllers/seaman.php';
}

/**
 * Limit REST access.
 */
function rest_auth( $result ) {
    if ( ! empty( $result ) ) {
        return $result;
    }

    // Only allow JWT auth routes publicly.
    $route = untrailingslashit( $GLOBALS['wp']->query_vars['rest_route'] );
    if ( ! is_user_logged_in() && 0 !== strpos( $route, '/jwt-auth/v1' ) ) {
        return new \WP_Error( 'rest_forbidden', __( 'Sorry, you are not allowed to do that.', 'bzalpha' ), [ 'status' => rest_authorization_required_code() ] );
    }

    return $result;
};
