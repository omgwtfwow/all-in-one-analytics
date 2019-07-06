<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.juangonzalez.com.au
 * @since      1.0.0
 *
 * @package    All_In_One_Analytics
 * @subpackage All_In_One_Analytics/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    All_In_One_Analytics
 * @subpackage All_In_One_Analytics/includes
 * @author     Juan Gonzalez <hello@juangonzalez.com.au>
 */
class All_In_One_Analytics_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'all-in-one-analytics',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}


}
