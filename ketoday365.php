<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://myplan.ketoday365.com/
 * @since             1.0.0
 * @package           Ketoday365
 *
 * @wordpress-plugin
 * Plugin Name:       Ketoday365 - Headless
 * Plugin URI:        https://myplan.ketoday365.com/
 * Description:       Wordpress Headless for React App
 * Version:           1.0.0
 * Author:            Ketoday365
 * Author URI:        https://myplan.ketoday365.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ketoday365
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'KETODAY365_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ketoday365-activator.php
 */
function activate_ketoday365() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ketoday365-activator.php';
	Ketoday365_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ketoday365-deactivator.php
 */
function deactivate_ketoday365() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ketoday365-deactivator.php';
	Ketoday365_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ketoday365' );
register_deactivation_hook( __FILE__, 'deactivate_ketoday365' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ketoday365.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ketoday365() {

	$plugin = new Ketoday365();
	$plugin->run();

}
run_ketoday365();
