<?php


/**
 *  Managing cookie related stuff here
 *  Used wp-cookie-manager source but modified it
 * @link https://github.com/wpscholar/wp-cookie-manager
 *
 */

class All_In_One_Analytics_Cookie {

	private $plugin_name;

	private $version;

	/**
	 * All_In_One_Analytics_Cookie constructor.
	 *
	 * @param $plugin_name
	 * @param $version
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	public function enqueue_cookie_js() {

		wp_enqueue_script( 'js.cookie.js', plugin_dir_url( __FILE__ ) . 'js/js.cookie.js', array(), '2.2.0', false );

	}

	/**
	 * Set a cookie
	 *
	 * @param string $name The cookie name.
	 * @param string $value The cookie value.
	 * @param int $expiration A Unix timestamp representing the expiration (use time() plus seconds until expiration). Defaults to 0, which will cause the cookie to expire at the end of the user's browsing session.
	 * @param string $cookie_id A unique cookie ID
	 */
	public static function set_cookie( $name, $value, $expiration = 0, $cookie_id = '' ) {
		$length = mb_strlen( json_encode( $_COOKIE ) );
		if ( $length > 3093 ) {
			return;
		}

		$secure = ( 'https' === parse_url( home_url(), PHP_URL_SCHEME ) );
		setcookie( 'aio_analytics_' . $name . '_' . COOKIEHASH . '_' . $cookie_id, $value, $expiration, COOKIEPATH, COOKIE_DOMAIN, $secure );
	}

	/**
	 * Check if a cookie exists
	 *
	 * @param string $name
	 * @param string $cookie_id
	 *
	 * @return bool Whether or not the cookie exists.
	 */
	public static function check_cookie( $name, $cookie_id = ' ' ) {
		if ( isset( $cookie_id ) ) {

			return isset( $_COOKIE[ 'aio_analytics_' . $name . '_' . COOKIEHASH . '_' . $cookie_id ] );
		} else {
			return isset( $_COOKIE[ 'aio_analytics_' . $name . '_' . COOKIEHASH . '_' ] );
		}

	}

	/**
	 * Get a cookie
	 *
	 * @param string $name The cookie name.
	 * @param mixed $default The default value to return if the cookie doesn't exist (defaults to null).
	 * @param string $cookie_id
	 *
	 * @return mixed Returns the value or the default if the cookie doesn't exist.
	 */
	public static function get_cookie( $name, $cookie_id = '' ) {

		if ( isset( $cookie_id ) ) {
			if ( self::check_cookie( $name, $cookie_id ) ) {
				return $_COOKIE[ 'aio_analytics_' . $name . '_' . COOKIEHASH . '_' . $cookie_id ];
			}
		} else {
			if ( self::check_cookie( $name ) ) {
				return $_COOKIE[ 'aio_analytics_' . $name . '_' . COOKIEHASH . '_' ];
			}
		}

	}

	/**
	 * Delete a cookie
	 *
	 * @param string $name The name of the cookie to delete.
	 * @param string $cookie_id
	 */
	public static function delete_matching_cookies( $name ) {

		$expiration = time() - HOUR_IN_SECONDS;
		$new_value  = '';

		foreach ( $_COOKIE as $cookie => $value ) {
			if ( strpos( $cookie, "aio_analytics_" . $name ) !== false ) {

				setcookie( $cookie, $new_value, $expiration, COOKIEPATH, COOKIE_DOMAIN );


			}
		}

	}

	public static function match_cookie( $name ) {

		foreach ( $_COOKIE as $cookie => $value ) {
			if ( strpos( $cookie, "aio_analytics_" . $name ) !== false ) {
				return true;
			}
		}

		return false;


	}

	/**
	 * Clear cookies
	 *
	 * @param array of cookies
	 *
	 * @return array
	 */
	public static function get_every_cookie( $name ) {
		$cookies_array = Array();
		foreach ( $_COOKIE as $cookie => $value ) {
			if ( strpos( $cookie, "aio_analytics_" . $name ) !== false ) {
				$cookies_array[ $cookie ] = $value;
			}
		}

		return $cookies_array;
	}

	/**
	 * Clear cookies
	 *
	 * @param array of cookies
	 */
	public static function clear_cookies() {


		if ( isset( $_COOKIE['aio_analytics_clear'] ) ) {
			$value      = "";
			$expiration = time() - HOUR_IN_SECONDS;
			$path       = "/wordpress";

			setcookie( "aio_analytics_clear", $value, $expiration, $path, COOKIE_DOMAIN );
			unset( $_COOKIE["aio_analytics_clear"] );
			$path = "/wordpress/";
			setcookie( "aio_analytics_clear", $value, $expiration, $path, COOKIE_DOMAIN );
			unset( $_COOKIE["aio_analytics_clear"] );
			$path = "/";
			setcookie( "aio_analytics_clear", $value, $expiration, $path, COOKIE_DOMAIN );
			unset( $_COOKIE["aio_analytics_clear"] );
			setcookie( "aio_analytics_clear", $value, $expiration, COOKIEPATH, COOKIE_DOMAIN );
			unset( $_COOKIE["aio_analytics_clear"] );

			foreach ( $_COOKIE as $cookie => $value ) {
				if ( strpos( $cookie, "aio_analytics_" ) !== false ) {
					setcookie( $cookie, $value, $expiration, COOKIEPATH, COOKIE_DOMAIN );
					unset( $_COOKIE[ $cookie ] );
				}

			}
		}
	}

	/** Clear db */
	public static function clear_db() {
		global $wpdb;

		$table_name = $wpdb->prefix . 'all_in_one_analytics';
		$wpdb->query( "DELETE  FROM {$table_name} WHERE flag = 'true'" );

	}

}

