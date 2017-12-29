<?php

use Lib\AdminQuickbar;
use Lib\AdminQuickbarActivator;
use Lib\AdminQuickbarDeactivator;

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.rto.de
 * @since             1.0.0
 * @package           AdminQuickbar
 *
 * @wordpress-plugin
 * Plugin Name:       AdminQuickbar
 * Plugin URI:        https://github.com/RTO-Websites/Wordpress-AdminQuickbar
 * Description:       Adds a quickbar in admin with fast access to all posts/pages
 * Version:           1.0.3
 * Author:            RTO GmbH
 * Author URI:        https://www.rto.de
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       admin-quickbar
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The class responsible for auto loading classes.
 */
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/AdminQuickbarActivator.php
 */
register_activation_hook( __FILE__, array( AdminQuickbarActivator::class, 'activate' ) );

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/AdminQuickbarDeactivator.php
 */
register_deactivation_hook( __FILE__, array( AdminQuickbarDeactivator::class, 'deactivate' ) );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */


if ( is_admin()) {
    AdminQuickbar::run();
}