<?php

/**
 * Class All_In_One_Analytics_Identify
 */
class All_In_One_Analytics_Identify {

	private $plugin_name;

	private $version;

	private $user_id;


	/**
	 * All_In_One_Analytics_Identify constructor.
	 *
	 * @param $plugin_name
	 * @param $version
	 * @param $user_id
	 */
	public function __construct( $plugin_name, $version, $user_id ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->user_id     = $user_id;
	}

	function render_identify_call() {
		// try to get traits
		$current_user = get_user_by( 'id', $this->user_id );
		$settings     = get_exopite_sof_option( 'all-in-one-analytics' );
		$traits       = self::get_user_traits( $current_user );
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
					echo '"' . All_In_One_Analytics::esc_js_deep( $user_id ) . '"';
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
						echo All_In_One_Analytics::esc_js_deep( (string) $user_id );
						?>");
                </script><?php
			}
		}
	}

	/**
	 * Current traits
	 *
	 * @param $current_user
	 *
	 * @return array
	 */
	public function get_user_traits() {
		$traits       = Array();
		$settings     = get_exopite_sof_option( 'all-in-one-analytics' );
		$current_user = get_user_by( 'id', $this->user_id );

		if ( is_object( $current_user ) && isset ( $current_user->ID ) ) {
			$user_id          = $current_user->ID;
			$traits['userId'] = $user_id;
		}

		// if no user, add custom user traits if there is a 'signed up' cookie
		//  useful since people can sign up without being authenticated automatically
		if ( All_In_One_Analytics_Cookie::get_cookie( 'signed_up' ) ) {
			$properties = All_In_One_Analytics_Cookie::get_cookie( 'signed_up' );
			$properties = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'd' );
			$properties = json_decode( $properties );
			$properties = json_decode( $properties, true );
			$properties = All_In_One_Analytics::array_flatten( $properties );

			if ( isset( $properties['userId'] ) ) {
				$user_id = $properties['userId'];
				foreach ( $properties as $key => $value ) {
					$traits[ $key ] = $value;
				}
			}
			$traits['userId'] = $user_id;

		}

		// add custom user traits if user id is found
		if ( isset( $settings['custom_user_traits'] ) && isset( $traits['userId'] ) ) {
			if ( $traits['userId'] !== 0 ) {
				$custom_traits = $settings['custom_user_traits'];
				foreach ( $custom_traits as $custom_trait ) {
					$trait_label            = (string) $custom_trait["custom_user_traits_label"];
					$trait_key              = $custom_trait["custom_user_traits_key"];
					$trait_value            = get_user_meta( $traits['userId'], $trait_key, true );
					$traits[ $trait_label ] = $trait_value;
				}
			}
		}

		// Clean out empty traits and apply filter before sending it back.
		if ( isset( $traits ) ) {
			if ( isset( $traits['userId'] ) ) {

				$current_user = get_user_by( 'id', $traits['userId'] );
			}
			$traits = array_filter( $traits );

			//Devs, you can use this filter to modify the traits in the identify call
			return apply_filters( 'filter_user_traits', $traits, $current_user );

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

		if ( All_In_One_Analytics_Cookie::get_cookie( 'completed_form' ) ) {

			$properties = All_In_One_Analytics_Cookie::get_cookie( 'completed_form' );
			$properties = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'd' );
			$properties = json_decode( $properties );
			$properties = json_decode( json_encode( $properties ), true );

			if ( isset( $properties['userId'] ) ) {
				$traits['userId'] = $properties['userId'];
			}

		}

		/*	if ( All_In_One_Analytics_Cookie::get_cookie( 'completed_form' ) ) {
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
