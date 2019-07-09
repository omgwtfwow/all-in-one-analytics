<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @package    All_In_One_Analytics
 * @subpackage All_In_One_Analytics/public
 * @author     Juan Gonzalez <hello@juangonzalez.com.au>
 */


class All_In_One_Analytics_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;


	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		$settings = get_exopite_sof_option( 'all-in-one-analytics' );
		/**
		 * The All_In_One_Analytics_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		$current_user        = wp_get_current_user();
		$current_post        = get_post();
		$trackable_user      = All_In_One_Analytics::check_trackable_user( $current_user );
		$trackable_post_type = All_In_One_Analytics::check_trackable_post( $current_post );

		if ( $trackable_user === true && $trackable_post_type === true ) {

			wp_enqueue_script( 'analytics.js', plugin_dir_url( __FILE__ ) . 'js/analytics/analytics.min.js', array(
				'jquery',
				'async.analytics.js'
			), $this->version, true );

			if ( class_exists( 'woocommerce' ) && $settings["woocommerce_event_settings"]['track_woocommerce_fieldset']['track_woocommerce'] === 'yes' ) {

				wp_enqueue_script( 'all-in-one-analytics-public.js', plugin_dir_url( __FILE__ ) . 'js/all-in-one-analytics-public.js', array(
					'jquery',
					'js.cookie.js'
				), $this->version, true );
				wp_localize_script( 'all-in-one-analytics-public.js', 'detailsAIO', array(
					'post' => $current_post,
				) );

			}
			//	if ( class_exists( 'woocommerce' ) ) {
			//	wc_enqueue_js($javascript);
			//	}

		}

	}

	/**
	 * CORE WORDPRESS EVENTS
	 * Uses All_In_One_Analytics_Cookie::set_cookie() to notify All_In_One_Analytics of core events
	 *                 *
	 */

	public function made_comment( ...$args ) {
		if ( isset( $args[1]->comment_author ) && $args[1]->comment_author == 'WooCommerce' ) {
			//because Woo inserts a comment with order details
			return;
		}

		$action_hook = current_action();
		$args        = func_get_args();
		$args        = array( 'action_hook' => current_action(), 'args' => json_decode( json_encode( $args ), true ) );
		$args        = All_In_One_Analytics::object_to_array( $args );
		$user_id     = All_In_One_Analytics::get_user_id( $action_hook, $args );
		$properties  = All_In_One_Analytics::get_event_properties( $action_hook, $user_id, $args );
		$properties  = json_encode( $properties );
		$properties  = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'e' );
		$data_id     = wp_rand( 1, 1000 ) . str_shuffle( $action_hook );
		All_In_One_Analytics::insert_data_into_db( $data_id, $properties );
		All_In_One_Analytics_Cookie::set_cookie( 'made_comment', $data_id, 0, $data_id );

	}

	public function logged_in( ...$args ) { //user
		$action_hook = current_action();
		$args        = func_get_args();
		$args        = array( 'action_hook' => current_action(), 'args' => json_decode( json_encode( $args ), true ) );
		$args        = All_In_One_Analytics::object_to_array( $args );
		$user_id     = All_In_One_Analytics::get_user_id( $action_hook, $args );
		$properties  = All_In_One_Analytics::get_event_properties( $action_hook, $user_id, $args );
		$properties  = json_encode( $properties );
		$properties  = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'e' );
		$data_id     = wp_rand( 1, 1000 ) . str_shuffle( $action_hook );
		All_In_One_Analytics::insert_data_into_db( $data_id, $properties );
		All_In_One_Analytics_Cookie::set_cookie( 'logged_in', $data_id, 0, $data_id );
	}

	public function signed_up( ...$args ) {
		$action_hook = current_action();
		$args        = func_get_args();
		$args        = array( 'action_hook' => current_action(), 'args' => json_decode( json_encode( $args ), true ) );
		$args        = All_In_One_Analytics::object_to_array( $args );
		$user_id     = All_In_One_Analytics::get_user_id( $action_hook, $args );
		$properties  = All_In_One_Analytics::get_event_properties( $action_hook, $user_id, $args );
		$properties  = json_encode( $properties );
		$properties  = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'e' );
		All_In_One_Analytics_Cookie::set_cookie( 'signed_up', $properties );
	}

	/** FORMS */

	//NINJA FORMS
	public function completed_form_nf( ...$args ) {

		//$args[0]=$cart_item_key,$args[1]=$product_id,$args[2]=$quantity, $args[3]=$variation_id,$args[4]=$variation, $args[5]=$cart_item_data
		$action_hook = current_action();
		$args        = func_get_args();
		$args        = array(
			'action_hook' => current_action(),
			'args'        => json_decode( json_encode( $args ),
				true )
		);
		$args        = All_In_One_Analytics::object_to_array( $args );
		$user_id     = All_In_One_Analytics::get_user_id( $action_hook, $args );
		$properties  = All_In_One_Analytics::get_event_properties( $action_hook, $user_id, $args );
		$properties  = json_encode( $properties );
		$properties  = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'e' );
		$data_id     = wp_rand( 1, 1000 ) . str_shuffle( $action_hook );
		All_In_One_Analytics::insert_data_into_db( $data_id, $properties );
		All_In_One_Analytics_Cookie::set_cookie( 'completed_form_nf', $data_id );

	}

	//GRAVITY FORMS
	public function completed_form_gf( ...$args ) {
		//GF  args[0]=$entry args[1]= $form and NF args[0]=form_data object
		$action_hook = current_action();
		$args        = func_get_args();
		$args        = array(
			'action_hook' => current_action(),
			'args'        => json_decode( json_encode( $args ),
				true )
		);
		$args        = All_In_One_Analytics::object_to_array( $args );
		$user_id     = All_In_One_Analytics::get_user_id( $action_hook, $args );
		$properties  = All_In_One_Analytics::get_event_properties( $action_hook, $user_id, $args );
		$properties  = json_encode( $properties );
		$properties  = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'e' );
		$data_id     = wp_rand( 1, 1000 ) . str_shuffle( $action_hook );
		All_In_One_Analytics::insert_data_into_db( $data_id, $properties );
		All_In_One_Analytics_Cookie::set_cookie( 'completed_form_gf', $data_id );

	}


	/**
	 * ECOMMERCE COOKIES
	 * Uses All_In_One_Analytics_Cookie::set_cookie() to notify All_In_One_Analytics of user ecommerce events
	 *                 *
	 */

	public function product_added_normal( ...$args ) {
		// don't track add to cart from AJAX here
		if ( is_ajax() ) {
			return;
		}
		//$args[0]=$cart_item_key,$args[1]=$product_id,$args[2]=$quantity, $args[3]=$variation_id,$args[4]=$variation, $args[5]=$cart_item_data
		$action_hook = current_action();
		$args        = func_get_args();
		$args        = array(
			'action_hook' => current_action(),
			'args'        => json_decode( json_encode( $args ),
				true )
		);
		$args        = All_In_One_Analytics::object_to_array( $args );
		$user_id     = All_In_One_Analytics::get_user_id( $action_hook, $args );
		$properties  = All_In_One_Analytics::get_event_properties( $action_hook, $user_id, $args );
		$properties  = json_encode( $properties );
		$properties  = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'e' );
		$data_id     = wp_rand( 1, 1000 ) . str_shuffle( $action_hook );
		All_In_One_Analytics::insert_data_into_db( $data_id, $properties );
		All_In_One_Analytics_Cookie::set_cookie( 'product_added_normal', $data_id );

	}

	public function product_added_ajax( ...$args ) {
		//$args[0]=$product_id
		$action_hook = current_action();
		$args        = func_get_args();
		$args        = array(
			'action_hook' => current_action(),
			'args'        => json_decode( json_encode( $args ),
				true )
		);
		$args        = All_In_One_Analytics::object_to_array( $args );
		$user_id     = All_In_One_Analytics::get_user_id( $action_hook, $args );
		$properties  = All_In_One_Analytics::get_event_properties( $action_hook, $user_id, $args );
		$properties  = json_encode( $properties );
		$properties  = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'e' );
		$data_id     = wp_rand( 1, 1000 ) . str_shuffle( $action_hook );
		All_In_One_Analytics::insert_data_into_db( $data_id, $properties );
		All_In_One_Analytics_Cookie::set_cookie( 'product_added_ajax', $data_id );

	}

	public function product_removed( ...$args ) {
		// args $removed_cart_item_key, $cart
		$action_hook = current_action();
		$args        = func_get_args();
		$args        = array( 'action_hook' => current_action(), 'args' => json_decode( json_encode( $args ), true ) );
		$args        = All_In_One_Analytics::object_to_array( $args );
		$user_id     = All_In_One_Analytics::get_user_id( $action_hook, $args );
		$properties  = All_In_One_Analytics::get_event_properties( $action_hook, $user_id, $args );
		$properties  = json_encode( $properties );
		$properties  = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'e' );
		$data_id     = wp_rand( 1, 1000 ) . str_shuffle( $action_hook );
		All_In_One_Analytics::insert_data_into_db( $data_id, $properties );
		All_In_One_Analytics_Cookie::set_cookie( 'product_removed', $data_id, $expiration = 0, $data_id );
	}

	public function product_readded( ...$args ) {
		// args $removed_cart_item_key, $cart
		$action_hook = current_action();
		$args        = func_get_args();
		$args        = array( 'action_hook' => current_action(), 'args' => json_decode( json_encode( $args ), true ) );
		$args        = All_In_One_Analytics::object_to_array( $args );
		$user_id     = All_In_One_Analytics::get_user_id( $action_hook, $args );
		$properties  = All_In_One_Analytics::get_event_properties( $action_hook, $user_id, $args );
		$properties  = json_encode( $properties );
		$properties  = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'e' );
		$data_id     = wp_rand( 1, 1000 ) . str_shuffle( $action_hook );
		All_In_One_Analytics::insert_data_into_db( $data_id, $properties );
		All_In_One_Analytics_Cookie::set_cookie( 'product_readded', $data_id, $expiration = 0, $data_id );
	}

	public function coupon_added( ...$args ) {
		$action_hook = current_action();
		$args        = func_get_args();
		$args        = array( 'action_hook' => current_action(), 'args' => json_decode( json_encode( $args ), true ) );
		$args        = All_In_One_Analytics::object_to_array( $args );
		$user_id     = All_In_One_Analytics::get_user_id( $action_hook, $args );
		$properties  = All_In_One_Analytics::get_event_properties( $action_hook, $user_id, $args );
		$properties  = json_encode( $properties );
		$properties  = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'e' );
		All_In_One_Analytics_Cookie::set_cookie( 'coupon_added', $properties );
	}

	public function checkout_started( ...$args ) {
		$action_hook = current_action();
		$args        = func_get_args();
		$args        = array( 'action_hook' => current_action(), 'args' => json_decode( json_encode( $args ), true ) );
		$args        = All_In_One_Analytics::object_to_array( $args );
		$user_id     = All_In_One_Analytics::get_user_id( $action_hook, $args );
		$properties  = All_In_One_Analytics::get_event_properties( $action_hook, $user_id, $args );
		$properties  = json_encode( $properties );
		$properties  = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'e' );
		All_In_One_Analytics_Cookie::set_cookie( 'checkout_started', $properties );
	}


	public function order_pending( ...$args ) {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			$action_hook = current_action();
			$args        = func_get_args();
			$args        = array(
				'action_hook' => current_action(),
				'args'        => json_decode( json_encode( $args ),
					true )
			);
			$args        = All_In_One_Analytics::object_to_array( $args );
			$user_id     = All_In_One_Analytics::get_user_id( $action_hook, $args );
			$properties  = All_In_One_Analytics::get_event_properties( $action_hook, $user_id, $args );
			$properties  = json_encode( $properties );
			$properties  = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'e' );
			$data_id     = wp_rand( 1, 1000 ) . str_shuffle( $action_hook );
			All_In_One_Analytics::insert_data_into_db( $data_id, $properties );
			All_In_One_Analytics_Cookie::set_cookie( 'order_pending', $data_id );
		}
	}

	public function order_processing( ...$args ) {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			$action_hook = current_action();
			$args        = func_get_args();
			$args        = array(
				'action_hook' => current_action(),
				'args'        => json_decode( json_encode( $args ), true )
			);
			$args        = All_In_One_Analytics::object_to_array( $args );
			$user_id     = All_In_One_Analytics::get_user_id( $action_hook, $args );
			$properties  = All_In_One_Analytics::get_event_properties( $action_hook, $user_id, $args );
			$properties  = json_encode( $properties );
			$properties  = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'e' );
			$data_id     = wp_rand( 1, 1000 ) . str_shuffle( $action_hook );
			All_In_One_Analytics::insert_data_into_db( $data_id, $properties );
			All_In_One_Analytics_Cookie::set_cookie( 'order_processing', $data_id );
		}
	}

	public function order_completed( ...$args ) {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			$action_hook = current_action();
			$args        = func_get_args();
			$args        = array(
				'action_hook' => current_action(),
				'args'        => json_decode( json_encode( $args ), true )
			);
			$args        = All_In_One_Analytics::object_to_array( $args );
			$user_id     = All_In_One_Analytics::get_user_id( $action_hook, $args );
			$properties  = All_In_One_Analytics::get_event_properties( $action_hook, $user_id, $args );
			$properties  = json_encode( $properties );
			$properties  = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'e' );
			$data_id     = wp_rand( 1, 1000 ) . str_shuffle( $action_hook );
			All_In_One_Analytics::insert_data_into_db( $data_id, $properties );
			All_In_One_Analytics_Cookie::set_cookie( 'order_completed', $data_id );
		}
	}

	public function order_paid( ...$args ) {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			$action_hook = current_action();
			$args        = func_get_args();
			$args        = array(
				'action_hook' => current_action(),
				'args'        => json_decode( json_encode( $args ), true )
			);
			$args        = All_In_One_Analytics::object_to_array( $args );
			$user_id     = All_In_One_Analytics::get_user_id( $action_hook, $args );
			$properties  = All_In_One_Analytics::get_event_properties( $action_hook, $user_id, $args );
			$properties  = json_encode( $properties );
			$properties  = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'e' );
			$data_id     = wp_rand( 1, 1000 ) . str_shuffle( $action_hook );
			All_In_One_Analytics::insert_data_into_db( $data_id, $properties );
			All_In_One_Analytics_Cookie::set_cookie( 'order_paid', $data_id );
		}
	}

	public function order_cancelled( ...$args ) {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			$action_hook = current_action();
			$args        = func_get_args();
			$args        = array(
				'action_hook' => current_action(),
				'args'        => json_decode( json_encode( $args ), true )
			);
			$args        = All_In_One_Analytics::object_to_array( $args );
			$user_id     = All_In_One_Analytics::get_user_id( $action_hook, $args );
			$properties  = All_In_One_Analytics::get_event_properties( $action_hook, $user_id, $args );
			$properties  = json_encode( $properties );
			$properties  = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'e' );
			$data_id     = wp_rand( 1, 1000 ) . str_shuffle( $action_hook );
			All_In_One_Analytics::insert_data_into_db( $data_id, $properties );
			All_In_One_Analytics_Cookie::set_cookie( 'order_cancelled', $data_id );
		}
	}


	/**
	 * LEARNDASH COOKIES
	 * Uses All_In_One_Analytics_Cookie::set_cookie() to notify All_In_One_Analytics of user ecommerce events
	 *                 *
	 */

	public function enrolled_in_course( ...$args ) {
		//args	$user_id, $course_id, $access_list, $remove
		$args = func_get_args();

		if ( $args[3] || empty( $args[0] ) || empty( $args[1] ) ) {
			return;
		}

		$user_id   = $args[0];
		$course_id = $args[1];

		$user = get_user_by( "id", $user_id );
		if ( empty( $user->ID ) ) {
			return;
		}
		$course = get_post( $course_id );
		if ( empty( $course->ID ) ) {
			return;
		}

		$action_hook = current_action();
		$args        = array(
			'action_hook' => $action_hook,
			'args'        => json_decode( json_encode( $args ), true )
		);
		$args        = All_In_One_Analytics::object_to_array( $args );
		$user_id     = All_In_One_Analytics::get_user_id( $action_hook, $args );
		$properties  = All_In_One_Analytics::get_event_properties( $action_hook, $user_id, $args );
		$properties  = json_encode( $properties );
		$properties  = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'e' );
		$data_id     = wp_rand( 1, 1000 ) . str_shuffle( $action_hook );
		All_In_One_Analytics::insert_data_into_db( $data_id, $properties );
		All_In_One_Analytics_Cookie::set_cookie( 'enrolled_in_course', $data_id );
	}

	public function enrolled_in_course_via_group( ...$args ) {
		//args	$group_id, $group_leaders, $group_users, $group_courses
		$args        = func_get_args();
		$action_hook = current_action();
		$args        = func_get_args();
		$args        = array(
			'action_hook' => current_action(),
			'args'        => json_decode( json_encode( $args ), true )
		);
		$args        = All_In_One_Analytics::object_to_array( $args );
		$user_id     = All_In_One_Analytics::get_user_id( $action_hook, $args );
		$properties  = All_In_One_Analytics::get_event_properties( $action_hook, $user_id, $args );
		$properties  = json_encode( $properties );
		$properties  = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'e' );
		$data_id     = wp_rand( 1, 1000 ) . str_shuffle( $action_hook );
		All_In_One_Analytics::insert_data_into_db( $data_id, $properties );
		All_In_One_Analytics_Cookie::set_cookie( 'enrolled_in_course_via_group', $data_id );
	}

	public function topic_completed( ...$args ) {
		$action_hook = current_action();
		$args        = func_get_args();
		$args        = array(
			'action_hook' => $action_hook,
			'args'        => json_decode( json_encode( $args ), true )
		);
		$args        = All_In_One_Analytics::object_to_array( $args );
		$user_id     = All_In_One_Analytics::get_user_id( $action_hook, $args );
		$properties  = All_In_One_Analytics::get_event_properties( $action_hook, $user_id, $args );
		$properties  = json_encode( $properties );
		$properties  = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'e' );
		$data_id     = wp_rand( 1, 1000 ) . str_shuffle( $action_hook );
		All_In_One_Analytics::insert_data_into_db( $data_id, $properties );
		All_In_One_Analytics_Cookie::set_cookie( 'topic_completed', $data_id );
	}

	public function lesson_completed( ...$args ) {
		$action_hook = current_action();
		$args        = func_get_args();
		$args        = array(
			'action_hook' => $action_hook,
			'args'        => json_decode( json_encode( $args ), true )
		);
		$args        = All_In_One_Analytics::object_to_array( $args );
		$user_id     = All_In_One_Analytics::get_user_id( $action_hook, $args );
		$properties  = All_In_One_Analytics::get_event_properties( $action_hook, $user_id, $args );
		$properties  = json_encode( $properties );
		$properties  = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'e' );
		$data_id     = wp_rand( 1, 1000 ) . str_shuffle( $action_hook );
		All_In_One_Analytics::insert_data_into_db( $data_id, $properties );
		All_In_One_Analytics_Cookie::set_cookie( 'lesson_completed', $data_id );

	}

	public function course_completed( ...$args ) {
		$action_hook = current_action();
		$args        = func_get_args();
		$args        = array(
			'action_hook' => $action_hook,
			'args'        => json_decode( json_encode( $args ), true )
		);
		$args        = All_In_One_Analytics::object_to_array( $args );
		$user_id     = All_In_One_Analytics::get_user_id( $action_hook, $args );
		$properties  = All_In_One_Analytics::get_event_properties( $action_hook, $user_id, $args );
		$properties  = json_encode( $properties );
		$properties  = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'e' );
		$data_id     = wp_rand( 1, 1000 ) . str_shuffle( $action_hook );
		All_In_One_Analytics::insert_data_into_db( $data_id, $properties );
		//All_In_One_Analytics_Cookie::set_cookie( 'course_completed', $properties, $data_id );
		All_In_One_Analytics_Cookie::set_cookie( 'course_completed', $data_id );
	}

	public function assignment_uploaded( ...$args ) {
		//do_action( 'learndash_assignment_uploaded', $assignment_post_id, $assignment_meta );
		$action_hook = current_action();
		$args        = func_get_args();
		$args        = array(
			'action_hook' => $action_hook,
			'args'        => json_decode( json_encode( $args ), true )
		);
		$args        = All_In_One_Analytics::object_to_array( $args );
		$user_id     = All_In_One_Analytics::get_user_id( $action_hook, $args );
		$properties  = All_In_One_Analytics::get_event_properties( $action_hook, $user_id, $args );
		$properties  = json_encode( $properties );
		$properties  = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'e' );
		$data_id     = wp_rand( 1, 1000 ) . str_shuffle( $action_hook );
		All_In_One_Analytics::insert_data_into_db( $data_id, $properties );
		All_In_One_Analytics_Cookie::set_cookie( 'assignment_uploaded', $properties, $data_id );
	}

	//QUIZZES
	public function quiz_completed( ...$args ) {

		//do_action("learndash_quiz_completed", $quizdata, $current_user); //Hook for completed quiz
		//$quizdata = array( "quiz" => $quiz,
		//  "course" => $course,
		// "questions" => $questions,
		// "score" => $score,
		// "count" => $count,
		// "pass" => $pass,
		// "rank" => '-',
		// "time" => time(),
		// 'pro_quizid' => $quiz_id,
		// 'points' => $points,
		// 'total_points' => $total_points,
		// 'percentage' => $result,
		// 'timespent' => $timespent);
		$args = func_get_args();

		if ( empty( $args[1]->ID ) || empty( $args[0]["quiz"]->ID ) || empty( $args[0]["course"]->ID ) ) {
			return;
		}

		unset( $args[0]["rank"] );
		unset( $args[0]["questions"] );

		$action_hook = current_action();
		$args        = array(
			'action_hook' => $action_hook,
			'args'        => json_decode( json_encode( $args ), true )
		);
		$args        = All_In_One_Analytics::object_to_array( $args );
		$user_id     = All_In_One_Analytics::get_user_id( $action_hook, $args );
		$properties  = All_In_One_Analytics::get_event_properties( $action_hook, $user_id, $args );
		$properties  = json_encode( $properties );
		$properties  = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'e' );
		$data_id     = wp_rand( 1, 1000 ) . str_shuffle( $action_hook );
		All_In_One_Analytics::insert_data_into_db( $data_id, $properties );
		All_In_One_Analytics_Cookie::set_cookie( 'quiz_completed', $data_id );

		if ( $args["args"][0]["pass"] === 1 ) {

			All_In_One_Analytics_Cookie::set_cookie( 'quiz_passed', $data_id );

		}

		if ( $args["args"][0]["pass"] !== 1 ) {

			All_In_One_Analytics_Cookie::set_cookie( 'quiz_failed', $data_id );

		}
	}

	public function quiz_passed( ...$args ) {
		$action_hook = current_action();
		$args        = func_get_args();
		$args        = array(
			'action_hook' => $action_hook,
			'args'        => json_decode( json_encode( $args ), true )
		);
		$args        = All_In_One_Analytics::object_to_array( $args );
		$user_id     = All_In_One_Analytics::get_user_id( $action_hook, $args );
		$properties  = All_In_One_Analytics::get_event_properties( $action_hook, $user_id, $args );
		$properties  = json_encode( $properties );
		$properties  = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'e' );
		$data_id     = wp_rand( 1, 1000 ) . str_shuffle( $action_hook );
		All_In_One_Analytics::insert_data_into_db( $data_id, $properties );
		All_In_One_Analytics_Cookie::set_cookie( 'quiz_completed', $properties, $data_id );
	}

	public function quiz_failed( ...$args ) {
		$action_hook = current_action();
		$args        = func_get_args();
		$args        = array(
			'action_hook' => $action_hook,
			'args'        => json_decode( json_encode( $args ), true )
		);
		$args        = All_In_One_Analytics::object_to_array( $args );
		$user_id     = All_In_One_Analytics::get_user_id( $action_hook, $args );
		$properties  = All_In_One_Analytics::get_event_properties( $action_hook, $user_id, $args );
		$properties  = json_encode( $properties );
		$properties  = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'e' );
		$data_id     = wp_rand( 1, 1000 ) . str_shuffle( $action_hook );
		All_In_One_Analytics::insert_data_into_db( $data_id, $properties );
		All_In_One_Analytics_Cookie::set_cookie( 'quiz_completed', $properties, $data_id );
	}

}
