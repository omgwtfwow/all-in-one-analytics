<?php
//
//  Render tracking snippet in <footer>
class All_In_One_Analytics_Track {

	private $plugin_name;

	private $version;

	/**
	 * All_In_One_Analytics_Track constructor.
	 *
	 * @param $plugin_name
	 * @param $version
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Render the client side js track call
	 */
	function render_track_call() {

		if ( isset( $_SERVER["HTTP_X_REQUESTED_WITH"] ) ) { //Only render these for actual browsers
			return;
		}
//
		$current_user        = wp_get_current_user();
		$user_id             = $current_user->ID;
		$current_post        = get_post();
		$trackable_user      = All_In_One_Analytics::check_trackable_user( $current_user );
		$trackable_post_type = All_In_One_Analytics::check_trackable_post( $current_post );

		if ( $trackable_user === false || $trackable_post_type === false ) {
			//not trackable
			return;
		} else {
			$tracks = All_In_One_Analytics::get_current_tracks();

			foreach ( $tracks as $track ) {

				if ( isset( $track["skip-cookie"] ) && $track["skip-cookie"] == true ) {

					$track = self::add_event_properties( $track, $user_id );
					$track = apply_filters( 'filter_track_call', $track, $user_id );
					//Devs, you can use this filter to change the track calls
					if ( isset( $track['event'] ) ) {
						?>
                        <script type="text/javascript">

							analytics.track(<?php
								echo '"' . All_In_One_Analytics::esc_js_deep( $track['event'] ) . '"';
								?><?php
								if ( ! empty( $track['properties'] ) ) {
									echo ', ' . json_encode( All_In_One_Analytics::esc_js_deep( $track['properties'] ) );
								} else {
									echo ', {}';
								}
								?><?php
								if ( ! empty( $track['options'] ) ) {
									echo ', ' . json_encode( All_In_One_Analytics::esc_js_deep( $track['options'] ) );
								}
								?>);
                        </script>
						<?php

					}


				} else {


					$track = self::add_event_properties( $track, $user_id );
					$track = apply_filters( 'filter_track_call', $track, $user_id );
					//Devs, you can use this filter to change the track calls
					if ( isset( $track['event'] ) ) {
						?>
                        <script type="text/javascript">

							analytics.track(<?php
								echo '"' . All_In_One_Analytics::esc_js_deep( $track['event'] ) . '"';
								?><?php
								if ( ! empty( $track['properties'] ) ) {
									echo ', ' . json_encode( All_In_One_Analytics::esc_js_deep( $track['properties'] ) );
								} else {
									echo ', {}';
								}
								?><?php
								if ( ! empty( $track['options'] ) ) {
									echo ', ' . json_encode( All_In_One_Analytics::esc_js_deep( $track['options'] ) );
								}
								?>);
							if (!Cookies.get('aio_analytics_clear')) {
								Cookies.set('aio_analytics_clear', true);
							}
                        </script>
						<?php

					}


				}

			}
		}
	}


	/**
	 * Add  event properties
	 *
	 * @param $track
	 * @param $user_id
	 *
	 * @return mixed
	 */
	public function add_event_properties( $track, $user_id ) {
		$settings = get_exopite_sof_option( 'all-in-one-analytics' );

		if ( $settings['userid_is_email'] === "yes" && isset( $user_id ) ) {
			$user = get_user_by( 'id', $user_id );
			if ( isset( $user ) && is_object( $user ) ) {
				$track['$user_id'] = (string) $user->user_email;
				if ( $settings["include_user_ids"] === 'yes' ) { // based on user settings
					$track["properties"]["userId"] = (string) $user_id;
					$track["properties"]["email"]  = $user->user_email;
				}
			}
		}

		$track['options']                       = array();
		$track['options']['library']            = array();
		$track['options']['library']['name']    = 'All in One Analytics';
		$track['options']['library']['version'] = 'in8.io';

		return $track;
	}


}
