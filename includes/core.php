<?php

namespace BZAlpha\Core;

/**
 * Setup core.
 */
function setup() {
    add_action( 'plugins_loaded', __NAMESPACE__ . '\plugins_loaded', 5 );
}

/**
 * On plugins loaded.
 */
function plugins_loaded() {
    if ( check_plugins() ) {
        add_action( 'admin_notices', __NAMESPACE__ . '\plugins_notice' );
    }

    includes();
    hooks();
}

/**
 * Include files.
 */
function includes() {
    require_once BZALPHA_INC . 'overrides.php';
    require_once BZALPHA_INC . 'post-types.php';
    require_once BZALPHA_INC . 'rest-api/rest-api.php';
}

/**
 * Run hooks.
 */
function hooks() {
	\BZAlpha\Overrides\setup();
	\BZAlpha\Post_Types\setup();
	\BZAlpha\REST_API\setup();
}

/**
 * Check required plugins.
 */
function check_plugins() {
    $plugins = [];

    // JWT Token.
    if ( ! class_exists( 'Jwt_Auth' ) ) {
        $plugins['jwt-authentication-for-wp-rest-api'] = 'JWT Authentication for WP REST API';
    }

    return $plugins;
}

/**
 * Handle plugins notice.
 */
function plugins_notice() { ?>
    <div class="notice notice-warning">
        <p><?php esc_html_e( 'Please install and activate required plugins for BZ Alpha Engine:', 'bzalpha' ); ?> <strong><?php echo join( ', ', check_plugins() ); ?></strong></p>
    </div>
<?php }
