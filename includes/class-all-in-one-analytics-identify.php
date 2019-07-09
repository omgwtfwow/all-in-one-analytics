<?php

class All_In_One_Analytics_Identify {

	private $plugin_name;

	private $version;


	/**
	 * All_In_One_Analytics_Identify constructor.
	 *
	 * @param $plugin_name
	 * @param $version
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	function render_identify_call() {

		$settings = get_exopite_sof_option( 'all-in-one-analytics' );
		// check if trackable
		$current_post   = get_post();
		$current_user   = wp_get_current_user();
		$trackable_post = All_In_One_Analytics::check_trackable_post( $current_post );
		$trackable_user = All_In_One_Analytics::check_trackable_user( $current_user );

		if ( $trackable_user === false || $trackable_post === false ) {
			//not trackable or not logged in
			return;
		}
		// try to get traits
		$traits = All_In_One_Analytics::get_user_traits( $current_user );
		if ( $traits == null ) {
			$traits = array();
		}
		if ( isset( $traits['userId'] ) ) {
			$user_id = All_In_One_Analytics::esc_js_deep( $traits['userId'] );
		}
		// try to get user id from cookies
		$traits = self::add_user_traits( $traits, $current_user );
		if ( isset( $traits['userId'] ) ) {
			$user_id = All_In_One_Analytics::esc_js_deep( $traits['userId'] );
		}
		// get user id format
		if ( isset( $user_id ) && $user_id !== 0 ) { // only continue if there's a user id
			if ( $settings['userid_is_email'] === "yes" ) {
				$user    = get_user_by( 'id', $user_id );
				$user_id = $user->user_email;
			}
			?>
            <script type="text/javascript">
				analytics.identify( <?php
					echo '' . All_In_One_Analytics::esc_js_deep( $user_id ) . '';
					?><?php
					if ( ! empty( $traits ) ) {
						echo ', ' . json_encode( All_In_One_Analytics::esc_js_deep( $traits ) );
					} else {
						echo ', {}';
					}
					?><?php
					if ( ! empty( $options ) ) {
						echo ', ' . json_encode( All_In_One_Analytics::esc_js_deep( $options ) );
					}
					?>);
            </script>
			<?php
			if ( $settings['use_alias'] === "yes" ) {
				?>
                <script type="text/javascript">
					analytics.alias("<?php
						echo All_In_One_Analytics::esc_js_deep( $user_id );
						?>");
                </script><?php
			}
		}
	}

	/**
	 * Adds extra user traits according to cookies and settings
	 * Also used to find a user id when the user has a cookie but isn't auth yet
	 *
	 * @return array modified array of $traits
	 */
	function add_user_traits( $traits, $user ) {

		$settings = get_exopite_sof_option( 'all-in-one-analytics' );

		if ( All_In_One_Analytics_Cookie::get_cookie( 'signed_up' ) ) {
			$properties                = All_In_One_Analytics_Cookie::get_cookie( 'signed_up' );
			$properties                = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'd' );
			$properties                = json_decode( $properties );
			$properties                = json_decode( $properties, true );
			$user                      = get_user_by( 'ID', $properties[0]['userId'] );
			$traits['userId']          = $user->ID;
			$traits['email']           = $user->user_email;
			$traits['display_name']    = $user->display_name;
			$traits['first_name']      = $user->first_name;
			$traits['last_name']       = $user->last_name;
			$traits['nickname']        = $user->nickname;
			$traits['user_nicename']   = $user->user_nicename;
			$traits["user_registered"] = $user->user_registered;
			$traits["createdAt"]       = gmdate( "Y-m-d\TH:i:s\Z" ); //timestamp
			$traits                    = array_filter( $traits );
		}

		if ( All_In_One_Analytics_Cookie::get_cookie( 'completed_form_nf' ) ) {

			$properties = All_In_One_Analytics_Cookie::get_cookie( 'completed_form_nf' );
			$properties = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'd' );
			$properties = json_decode( $properties );
			$properties = json_decode( json_encode( $properties ), true );

			if ( isset( $properties['userId'] ) ) {
				$traits['userId'] = $properties['userId'];
			}

		}

		/*	if ( All_In_One_Analytics_Cookie::get_cookie( 'completed_form_nf' ) ) {
				if ( isset( $properties['userId'] ) ) {
				// $user_id = $properties['userId'];
				// TODO add here how it can update user meta

				}

			}*/

		//this is for the custom meta key functionality
		if ( isset( $settings['custom_user_traits'] ) && isset( $traits['userId'] ) ) {
			$custom_traits = $settings['custom_user_traits'];
			foreach ( $custom_traits as $custom_trait ) {
				$trait_label            = $custom_trait["custom_user_traits_label"];
				$trait_key              = $custom_trait["custom_user_traits_key"];
				$trait_value            = get_user_meta( $user->ID, $trait_key, true );
				$traits[ $trait_label ] = $trait_value;
			}

		}

		return array_filter( $traits );
	}
}
