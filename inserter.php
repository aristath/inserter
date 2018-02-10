<?php
/**
 * Plugin Name:   Inserter
 * Plugin URI:    http://aristath.github.io/inserter
 * Description:   Create custom underscore.js templates and inject them in your pages
 * Author:        Aristeides Stathopoulos
 * Author URI:    http://aristath.github.io
 * Version:       1.0
 * Text Domain:   inserter
 *
 * GitHub Plugin URI: aristath/inserter
 * GitHub Plugin URI: https://github.com/aristath/inserter
 *
 * @package     Inserter
 * @category    Core
 * @author      Aristeides Stathopoulos
 * @copyright   Copyright (c) 2017, Aristeides Stathopoulos
 * @license     http://opensource.org/licenses/https://opensource.org/licenses/MIT
 * @since       1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Include files.
require_once wp_normalize_path( dirname( __FILE__ ) . '/inc/class-inserter.php' );
require_once wp_normalize_path( dirname( __FILE__ ) . '/inc/class-inserter-admin.php' );

// Add the post-type.
new Inserter_Admin();

/**
 * Returns an instance of the Inserter class.
 *
 * @since 1.0
 * @return Inserter
 */
function inserter() {
	global $inserter;

	// Check if inserter has been instantiated or not.
	if ( ! $inserter ) {

		// Instantiate the Inserter class and assign it to the $inserter global var.
		$inserter = new Inserter();

		// Set the root library path.
		$inserter->set_path( dirname( __FILE__ ) );
	}
	return $inserter;
}

// Init Inserter.
inserter();
