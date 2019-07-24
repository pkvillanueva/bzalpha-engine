<?php

namespace BZAlpha\Overrides;

/**
 * Setup overrides.
 */
function setup() {
    add_filter( 'wp_using_themes', __NAMESPACE__ . '\using_themes' );
}

/**
 * Disable using themes.
 */
function using_themes() {
    return false;
}
