<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://www.juangonzalez.com.au
 * @since      1.0.0
 *
 * @package    All_In_One_Analytics
 * @subpackage All_In_One_Analytics/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    All_In_One_Analytics
 * @subpackage All_In_One_Analytics/includes
 * @author     Juan Gonzalez <hello@juangonzalez.com.au>
 */
class All_In_One_Analytics_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {

		require_once plugin_dir_path( __FILE__ ) . 'class-all-in-one-analytics-cron.php';
		All_In_One_Analytics_Cron::unschedule();


	}

}
