<?php

class All_In_One_Analytics_Async_Analytics_Js {

	private $plugin_name;

	private $version;

	private $tracking_settings;

	/**
	 * All_In_One_Analytics_Async_Analytics_Js constructor.
	 *
	 * @param $plugin_name
	 * @param $version
	 * @param $tracking_settings
	 */
	public function __construct( $plugin_name, $version, $tracking_settings ) {
		$this->plugin_name       = $plugin_name;
		$this->version           = $version;
		$this->tracking_settings = $tracking_settings;
	}


	/**
	 * Render async js needed
	 */
	function render_async_analytics_js() {

		//	$tracking_settings_array = All_In_One_Analytics::get_analytics_settings();
		$analytics_url = plugin_dir_url( __FILE__ ) . 'js/analytics/analytics.min.js';
		wp_enqueue_script( 'async.analytics.js', plugin_dir_url( __FILE__ ) . 'js/analytics/async.analytics.js', array( 'jquery' ), '1', false );
		wp_localize_script( 'async.analytics.js', 'settingsAIO', array(
			'init'         => ( 'init' ),
			'analyticsUrl' => $analytics_url,
			'settings'     => $this->tracking_settings,
		) );
	}
}