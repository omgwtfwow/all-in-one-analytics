<?php

class All_In_One_Analytics_Async {

	private $plugin_name;

	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->load_dependencies();
	}

	public function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'async/class-all-in-one-analytics-async-builder.php';
	}

	public function init() {
		$this->async_request = new All_In_One_Analytics_Async_Request();

	}

	public function all_in_one_analytics_async_request( ...$args ) {

		$settings = get_exopite_sof_option( 'all-in-one-analytics' );
		if ( isset( $settings["Zapier"]["zapier_webhook_url"] ) ) {
			$args                = func_get_args();
			$action              = current_action();
			$args['action_hook'] = current_action();
			if ( isset( $_COOKIE['ajs_user_id'] ) ) {
				$cookie_user_id         = stripslashes_deep( $_COOKIE['ajs_user_id'] );
				$cookie_user_id         = trim( $cookie_user_id, '"' );
				$args['cookie_user_id'] = (string) $cookie_user_id;
			}
			//	$args['cookie_user_id'] = (string) trim($cookie_user_id,'"');;

			$this->async_request->data( array( 'action_hook' => $action, 'args' => $args ) );
			$this->async_request->dispatch();
		}

	}
}
class All_In_One_Analytics_Async_Request extends WP_Async_Request {

	protected $action = 'all_in_one_analytics_async_request';

	/**
	 * Task
	 *
	 * @param mixed $item Queue item to iterate over
	 *
	 * @return mixed
	 */
	protected function handle() {

		if ( ! is_wp_error( $_POST ) ) {
			//error_log( print_r( $_POST, true ) );
			$settings = get_exopite_sof_option( 'all-in-one-analytics' );
			$url      = esc_url_raw( $settings["Zapier"]["zapier_webhook_url"] );

			$_POST = json_decode( json_encode( $_POST ), true ); //this flattens it and removes protected values
			$_POST = All_In_One_Analytics::object_to_array( $_POST );

			if ( ! isset( $_POST["action_hook"] ) ) {

				return;

			}

			$action_hook = $_POST["action_hook"];


			$event_user_id = All_In_One_Analytics::get_user_id( $action_hook, $_POST );

			if ( isset( $event_user_id ) ) { //only continue if we have a uid


				if ( All_In_One_Analytics::check_trackable_user( $event_user_id ) && $event_user_id !== "0" ) {
					$event_name       = All_In_One_Analytics::get_event_name( $_POST["action_hook"] );
					$event_properties = All_In_One_Analytics::get_event_properties( $action_hook, $event_user_id, $_POST['args'] );
					$post             = All_In_One_Analytics_Async_Builder::async_build_track( $event_user_id, $event_name, $event_properties );
				}

				if ( $event_user_id == 0 ) {
					//some plugins seem to use userid 0 for some of the stuff they do, so not including events with that user id
					return;
				}


				$cookie = new WP_Http_Cookie( 'XDEBUG_SESSION=PHPSTORM;path=/;' );

				//POST
				$response = wp_remote_post(
					$url, array(
						'method'      => 'POST',
						'timeout'     => 5,
						'redirection' => 5,
						'httpversion' => '1.0',
						'blocking'    => false,
						'headers'     => array(),
						'body'        => $post,
						'cookies'     => array( $cookie )
					)
				);

				//ERROR
				if ( is_wp_error( $response ) ) {
					if ( ! isset( $item["error_log_count"] ) ) {
						$item['error_log_count'] = absint( 0 );
					}
					$error_message = $response->get_error_message();
					$item['error_log_count'] ++;
					error_log( 'Error with webhook/event: ' . $error_message . ' Attempts: ' . $item['error_log_count'] );

				} //SUCCESS
				else {
					error_log( 'Event sent.' );
					error_log( print_r( $post, true ) );
				}
				//	return false;
			} else {
				//error_ log( print_r($_POST, TRUE) );
				error_log( 'No event. No user id' );
				//	return false;
			}
		} else {
			$error_message = $_POST->get_error_message();
			error_log( 'Error. No event:' . $error_message );
			//	return false;
		}
	}

}

//Clean up DB entries - Process
class All_In_One_Analytics_Async_Process {

	private $plugin_name;

	private $version;

	/**
	 * All_In_One_Analytics_Async_Process constructor.
	 *
	 * @param $plugin_name
	 * @param $version
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}


	/**
	 * Instantiate the async process
	 */
	public function init() {
		$this->async_process = new All_In_One_Analytics_Async_Process_Clean_DB();
	}

	/**
	 * Add item to the queue
	 *
	 * @param $item
	 */
	public function all_in_one_analytics_async_process_add( $item ) {
		$this->async_process->push_to_queue( $item );
	}

	/**
	 * Save and dispatch the queue
	 */
	public function all_in_one_analytics_async_process_dispatch() {

		$this->async_process->save()->dispatch();
	}

}
class All_In_One_Analytics_Async_Process_Clean_DB extends WP_Background_Process {

	protected $action = 'all_in_one_analytics_async_process_clean_db';

	/**
	 * Task
	 *
	 *
	 * @param mixed $item to add to queue
	 *
	 * @return mixed
	 */
	protected function task( $item ) {
		global $wpdb;
		$table = $wpdb->prefix . 'all_in_one_analytics';
		$wpdb->delete( $table, array( 'data_id' => $item ) );

		return false;
	}

	/**
	 * Complete
	 *
	 * Override if applicable, but ensure that the below actions are
	 * performed, or, call parent::complete().
	 */
	protected function complete() {
		parent::complete();

		// Show notice to user or perform some other arbitrary task...
	}

}

//Events
class All_In_One_Analytics_Async_Events {

	private $plugin_name;
	private $version;

	/**
	 * All_In_One_Analytics_Async_Events constructor.
	 *
	 * @param $plugin_name
	 * @param $version
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->load_dependencies();
	}

	/**
	 * Leads the builder functions for the server side event
	 */
	public function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'async/class-all-in-one-analytics-async-builder.php';
	}

	/**
	 * Instantiate the async process
	 */
	public function init() {
		$this->async_events = new All_In_One_Analytics_Async_Events_Process();
	}

	/**
	 *  Add events to the queue and dispatch them
	 *
	 * @param mixed ...$args
	 */
	public function all_in_one_analytics_async_events_add( ...$args ) {

		$settings = get_exopite_sof_option( 'all-in-one-analytics' );
		if ( isset( $settings["Zapier"]["zapier_webhook_url"] ) ) {
			$args                = func_get_args();
			$action              = current_action();
			$args['action_hook'] = current_action();
			if ( isset( $_COOKIE['ajs_user_id'] ) ) {
				$cookie_user_id         = stripslashes_deep( $_COOKIE['ajs_user_id'] );
				$cookie_user_id         = trim( $cookie_user_id, '"' );
				$args['cookie_user_id'] = $cookie_user_id;
			}
			//	$args['cookie_user_id'] = (string) trim($cookie_user_id,'"');;
			$item = array( 'action_hook' => $action, 'args' => $args );
			$this->async_events->push_to_queue( $item );
			$this->async_events->save()->dispatch();

		}
	}

	public function all_in_one_analytics_async_events_dispatch() {
		//	$this->async_events->save()->dispatch();
	}
}
class All_In_One_Analytics_Async_Events_Process extends WP_Background_Process {

	protected $action = 'all_in_one_analytics_async_events_process';

	/**
	 * Task
	 *
	 *
	 * @param mixed $item to add to queue
	 *
	 * @return mixed
	 */
	protected function task( $item ) {

		if ( ! isset( $item["action_hook"] ) ) {
			return false;
		}
		$action_hook            = $item["action_hook"];
		$item['action_current'] = current_action();
		$settings               = get_exopite_sof_option( 'all-in-one-analytics' );
		$url                    = esc_url_raw( $settings["Zapier"]["zapier_webhook_url"] );
		$item                   = json_decode( json_encode( $item ), true ); //this flattens it and removes protected values
		$item                   = All_In_One_Analytics::object_to_array( $item );
		$event_user_id          = All_In_One_Analytics::get_user_id( $action_hook, $item );

		if ( isset( $event_user_id ) ) { //only continue if we have a uid

			if ( All_In_One_Analytics::check_trackable_user( $event_user_id ) && $event_user_id !== "0" ) {
				$event_name       = All_In_One_Analytics::get_event_name( $item["action_hook"] );
				$event_properties = All_In_One_Analytics::get_event_properties( $action_hook, $event_user_id, $item );
				$post             = All_In_One_Analytics_Async_Builder::async_build_track( $event_user_id, $event_name, $event_properties );
			}

			if ( $event_user_id == 0 ) {
				//some plugins seem to use userid 0 for some of the stuff they do, so not including events with that user id
				return false;
			}

			//FIXME switch off for production
			$cookie = new WP_Http_Cookie( 'XDEBUG_SESSION=PHPSTORM;path=/;' );

			//POST
			$response = wp_remote_post(
				$url, array(
					'method'      => 'POST',
					'timeout'     => 5,
					'redirection' => 5,
					'httpversion' => '1.0',
					'blocking'    => false,
					'headers'     => array(),
					'body'        => $post,
					'cookies'     => array( $cookie )
				)
			);

			//ERROR
			if ( is_wp_error( $response ) ) {
				if ( ! isset( $item["error_log_count"] ) ) {
					$item['error_log_count'] = absint( 0 );
				}
				$error_message = $response->get_error_message();
				$item['error_log_count'] ++;
				error_log( 'Error with webhook/event: ' . $error_message . ' Attempts: ' . $item['error_log_count'] );

				if ( $item['error_log_count'] < 5 ) {

					return $item;

				}

				if ( $item['error_log_count'] > 5 ) {

					return false;

				}
			} //SUCCESS
			else {
				return false;
			}

			return false;
		} else {

			return false;
		}

	}

	/**
	 * Complete
	 *
	 * Override if applicable, but ensure that the below actions are
	 * performed, or, call parent::complete().
	 */
	protected function complete() {
		parent::complete();

		// Show notice to user or perform some other arbitrary task...
	}

}

