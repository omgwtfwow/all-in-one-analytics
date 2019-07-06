<?php


/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    All_In_One_Analytics
 * @subpackage All_In_One_Analytics/includes
 * @author     Juan Gonzalez <hello@juangonzalez.com.au>
 */
class All_In_One_Analytics_Activator {


	/**
	 * Short Description.
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		self::create_db();

		// schedule events (cron jobs)
		require_once plugin_dir_path( __FILE__ ) . 'class-all-in-one-analytics-cron.php';
		All_In_One_Analytics_Cron::schedule();

	}

	public static function create_db() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$table_name      = $wpdb->prefix . 'all_in_one_analytics';

		$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		data_id text NOT NULL,
		data text NOT NULL,
		flag text NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

	}


}
