<?php

/**
 * All In One Analytics :)
 *
 * @link              https://in8.io
 * @since             1.0.0
 * @package           All_In_One_Analytics
 *
 * @wordpress-plugin
 * Plugin Name:       All in One Analytics
 * Plugin URI:        https://github.com/omgwtfwow/all-in-one-analytics
 * Description:       All your analytics stuff sorted.
 * Version:           1.0.1
 * Author:            Juan González
 * Author URI:        https://www.juangonzalez.com.au
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       all-in-one-analytics
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


define( 'ALL_IN_ONE_ANALYTICS_PLUGIN_NAME', 'all-in-one-analytics' );

/**
 * Store plugin base dir, for easier access later from other classes.
 * (eg. Include, pubic or admin)
 */
define( 'ALL_IN_ONE_ANALYTICS_BASE_DIR', plugin_dir_path( __FILE__ ) );

/**
 */
define( 'ALL_IN_ONE_ANALYTICS_VERSION', '1.0.1' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-all-in-one-analytics-activator.php
 */
function activate_all_in_one_analytics() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-all-in-one-analytics-activator.php';
	All_In_One_Analytics_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-all-in-one-analytics-deactivator.php
 */
function deactivate_all_in_one_analytics() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-all-in-one-analytics-deactivator.php';
	All_In_One_Analytics_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_all_in_one_analytics' );
register_deactivation_hook( __FILE__, 'deactivate_all_in_one_analytics' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-all-in-one-analytics.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_all_in_one_analytics() {

	$plugin = new All_In_One_Analytics();
	$plugin->run();

}

run_all_in_one_analytics();