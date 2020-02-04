<?php
/**
 * Plugin Name: BZ Alpha Engine
 * Plugin URI:  http://app.bzalpha.com
 * Description: Plugin for BZ Alpha custom application.
 * Version:     0.1.0
 * Author:      BZ Alpha
 * Author URI:  https://bzalpha.com
 * Text Domain: bzalpha
 */

defined( 'ABSPATH' ) || exit;

// Define constants.
define( 'BZALPHA_VERSION', '0.1.0' );
define( 'BZALPHA_URL', plugin_dir_url( __FILE__ ) );
define( 'BZALPHA_DIR', plugin_dir_path( __FILE__ ) );
define( 'BZALPHA_INC', BZALPHA_DIR . 'includes/' );

// Include files.
require_once BZALPHA_DIR . 'vendor/autoload.php';
require_once BZALPHA_INC . 'core.php';

// Setup plugin.
BZAlpha\Core\setup();
