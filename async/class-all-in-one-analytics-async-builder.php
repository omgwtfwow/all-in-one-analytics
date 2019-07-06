<?php

/**
 *
 * Turns events into structured payloads for async requests
 *
 * @package    All_In_One_Analytics
 * @subpackage All_In_One_Analytics/async
 */
class All_In_One_Analytics_Async_Builder {

	private $plugin_name;

	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	public static function async_build_track( $user_id, $event_name, $event_properties ) {
		$settings = get_exopite_sof_option( 'all-in-one-analytics' );
		//$event_properties    = json_decode( $event_properties, true );
		//$event_properties    = All_In_One_Analytics::array_flatten( $event_properties );
		$track               = Array();
		$track["type"]       = 'track';
		$track["userId"]     = $user_id;
		$track["event"]      = $event_name;
		$track["properties"] = $event_properties;
		$track["context"]    = self::async_build_context();
		$track["timestamp"]  = self::async_build_timestamp( current_time( 'timestamp' ) );
		$track["messageId"]  = self::async_build_message_id();

		return $track;
	}

	public function async_build_identify( $user_id, ...$args ) {

	}

	public static function async_build_message_id() {
		return sprintf( "%04x%04x-%04x-%04x-%04x-%04x%04x%04x",
			mt_rand( 0, 0xffff ),
			mt_rand( 0, 0xffff ),
			mt_rand( 0, 0xffff ),
			mt_rand( 0, 0x0fff ) | 0x4000,
			mt_rand( 0, 0x3fff ) | 0x8000,
			mt_rand( 0, 0xffff ),
			mt_rand( 0, 0xffff ),
			mt_rand( 0, 0xffff )
		);
	}

	public static function async_build_timestamp( $ts ) {
		// time()
		if ( null == $ts || ! $ts ) {
			$ts = time();
		}
		if ( false !== filter_var( $ts, FILTER_VALIDATE_INT ) ) {
			return date( "c", (int) $ts );
		}
		// anything else try to strtotime the date.
		if ( false === filter_var( $ts, FILTER_VALIDATE_FLOAT ) ) {
			if ( is_string( $ts ) ) {
				return date( "c", strtotime( $ts ) );
			}

			return date( "c" );
		}
		// fix for floatval casting in send.php
		$parts = explode( ".", (string) $ts );
		if ( ! isset( $parts[1] ) ) {
			return date( "c", (int) $parts[0] );
		}
		// microtime(true)
		$sec  = (int) $parts[0];
		$usec = (int) $parts[1];
		$fmt  = sprintf( "Y-m-d\\TH:i:s%sP", $usec );

		return date( $fmt, (int) $sec );
	}

	public static function async_build_context() {
		$context["library"]["name"]    = 'All In One Analytics';
		$context["library"]["version"] = ALL_IN_ONE_ANALYTICS_VERSION;

		return $context;
	}

}