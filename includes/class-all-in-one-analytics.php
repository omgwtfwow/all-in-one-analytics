<?php

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    All_In_One_Analytics
 * @subpackage All_In_One_Analytics/includes
 * @author     Juan Gonzalez <hello@juangonzalez.com.au>
 */
class All_In_One_Analytics {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      All_In_One_Analytics_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'ALL_IN_ONE_ANALYTICS_VERSION' ) ) {
			$this->version = ALL_IN_ONE_ANALYTICS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'all-in-one-analytics';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_async_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-all-in-one-analytics-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-all-in-one-analytics-i18n.php';

		/**
		 * Exopite Simple Options Framework
		 *
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/exopite-simple-options/exopite-simple-options-framework-class.php';

		/**
		 * A class containing a method to encrypt/decrypt data, useful if writing cookies
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-all-in-one-analytics-encrypt.php';

		/**
		 * A class containing a method for async requests
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-all-in-one-analytics-wp-async-request.php';

		/**
		 * A class extending the async requests class to enable background processing jobs
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-all-in-one-analytics-wp-background-process.php';

		/**
		 * A class containing cookie stuff
		 * @link
		 */
		require plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-all-in-one-analytics-cookie.php';

		/**
		 * The class for page calls
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-all-in-one-analytics-page.php';

		/**
		 * The class for identify calls
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-all-in-one-analytics-identify.php';

		/**
		 * The class for track calls
		 */
		require plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-all-in-one-analytics-track.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-all-in-one-analytics-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-all-in-one-analytics-public.php';

		/**
		 * The class responsible for defining all async requests
		 *
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'async/class-all-in-one-analytics-async.php';

		/**
		 * Load the class that helps us load analytics.js asynchronously
		 * @link http://www.ianww.com/blog/2017/08/06/analytics-js-standalone-library/
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-all-in-one-analytics-async-analytics-js.php';

		/**
		 * Load the class that manages cron jobs
		 *
		 * @link https://github.com/DevinVinson/WordPress-Plugin-Boilerplate/pull/495/commits/fdb60d645d2decadb17ed08882c3c4f0c072942d
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-all-in-one-analytics-cron.php';


		$this->loader = new All_In_One_Analytics_Loader();


	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the All_In_One_Analytics_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new All_In_One_Analytics_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new All_In_One_Analytics_Admin( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'init', $plugin_admin, 'create_menu', 999 );
		$this->loader->add_action( All_In_One_Analytics_Cron::ALL_IN_ONE_ANALYTICS_EVENT_HOURLY_HOOK, $plugin_admin, 'run_hourly_event' );

		//	$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$settings = get_exopite_sof_option( 'all-in-one-analytics' );

		$plugin_public = new All_In_One_Analytics_Public( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts', 8 );


		//ASYNC ANALYTICS.JS
		$plugin_async_js = new All_In_One_Analytics_Async_Analytics_Js (
			$this->get_plugin_name(),
			$this->get_version(),
			$this->get_analytics_settings()
		);
		$this->loader->add_action( 'init', $plugin_async_js, 'render_async_analytics_js' );

		//COOKIES
		$plugin_cookie = new All_In_One_Analytics_Cookie (
			$this->get_plugin_name(),
			$this->get_version()
		);
		$this->loader->add_action( 'init', $plugin_cookie, 'enqueue_cookie_js' );
		$this->loader->add_action( 'init', $plugin_cookie, 'clear_cookies' );

		//IDENTIFY CALLS
		$plugin_identify = new All_In_One_Analytics_Identify(
			$this->get_plugin_name(),
			$this->get_version()
		);
		$this->loader->add_action( 'wp_footer', $plugin_identify, 'render_identify_call' );
		$this->loader->add_action( 'admin_footer', $plugin_identify, 'render_identify_call' );
		$this->loader->add_action( 'login_footer', $plugin_identify, 'render_identify_call' );

		//PAGE CALLS
		$plugin_page = new All_In_One_Analytics_Page(
			$this->get_plugin_name(),
			$this->get_version()
		);
		$this->loader->add_action( 'wp_footer', $plugin_page, 'render_page_call' );
		$this->loader->add_action( 'admin_footer', $plugin_page, 'render_page_call' );
		$this->loader->add_action( 'login_footer', $plugin_page, 'render_page_call' );

		//TRACK CALLS
		$plugin_track = new All_In_One_Analytics_Track(
			$this->get_plugin_name(),
			$this->get_version() );
		//$this->loader->add_action( 'wp_footer', $plugin_track, 'render_track_call' );
		$this->loader->add_action( 'wp_footer', $plugin_track, 'render_track_call' );
		$this->loader->add_action( 'admin_footer', $plugin_track, 'render_track_call' );
		$this->loader->add_action( 'login_footer', $plugin_track, 'render_track_call' );

		//CORE EVENTS
		if ( $settings['core_event_settings']['track_comments_fieldset']['track_comments'] == "yes" ) {
			$this->loader->add_action( 'wp_insert_comment', $plugin_public, 'made_comment', 9, 2 );
		}
		if ( $settings["core_event_settings"]['track_logins_fieldset']['track_logins'] == "yes" ) {
			$this->loader->add_action( 'wp_login', $plugin_public, 'logged_in', 9, 2 );
		}
		if ( $settings["core_event_settings"]['track_signups_fieldset']['track_signups'] == "yes" ) {
			$this->loader->add_action( 'user_register', $plugin_public, 'signed_up', 9, 1 );
		}

		//NINJA FORMS
		if ( $settings["form_event_settings"]['track_ninja_forms_fieldset']['track_ninja_forms'] === 'yes' ) {
			$this->loader->add_action( 'ninja_forms_after_submission', $plugin_public, 'completed_form_nf', 9, 1 );
		}

		//GRAVITY FORMS
		if ( $settings["form_event_settings"]['track_gravity_forms_fieldset']['track_gravity_forms'] === 'yes' ) {
			$this->loader->add_action( 'gform_after_submission', $plugin_public, 'completed_form_gf', 9, 2 );
		}

		//WOOCOMMERCE
		if ( $settings["woocommerce_event_settings"]["track_woocommerce_fieldset"]["track_woocommerce"] === 'yes' ) {

			if ( $settings["woocommerce_event_settings"]["track_woocommerce_fieldset"]["woocommerce_events"]["track_add_to_cart_fieldset"]["track_add_to_cart"] == 'yes' ) {
				$this->loader->add_action( 'woocommerce_add_to_cart', $plugin_public, 'product_added_normal', 9, 6 );
				$this->loader->add_action( 'woocommerce_ajax_added_to_cart', $plugin_public, 'product_added_ajax', 9, 1 );
				$this->loader->add_action( 'woocommerce_cart_item_restored', $plugin_public, 'product_readded', 5, 2 );

			}

			if ( $settings["woocommerce_event_settings"]["track_woocommerce_fieldset"]["woocommerce_events"]["track_remove_from_cart_fieldset"]["track_remove_from_cart"] == 'yes' ) {
				$this->loader->add_action( 'woocommerce_remove_cart_item', $plugin_public, 'product_removed', 9, 2 );
			}

			if ( $settings["woocommerce_event_settings"]["track_woocommerce_fieldset"]["woocommerce_events"]["track_initiated_checkout_fieldset"]["track_initiated_checkout"] == 'yes' ) {
				$this->loader->add_action( 'woocommerce_checkout_process', $plugin_public, 'checkout_started', 5 );

			}

			if ( $settings["woocommerce_event_settings"]["track_woocommerce_fieldset"]["woocommerce_events"]["track_order_pending_fieldset"]["track_order_pending"] ) {
				$this->loader->add_action( 'woocommerce_order_status_pending', $plugin_public, 'order_pending', 5, 1 );

			}

			if ( $settings["woocommerce_event_settings"]["track_woocommerce_fieldset"]["woocommerce_events"]["track_order_processing_fieldset"]["track_order_processing"] == 'yes' ) {
				$this->loader->add_action( 'woocommerce_order_status_processing', $plugin_public, 'order_processing', 5, 1 );

			}

			if ( $settings["woocommerce_event_settings"]["track_woocommerce_fieldset"]["woocommerce_events"]["track_order_completed_fieldset"]["track_order_completed"] == 'yes' ) {
				$this->loader->add_action( 'woocommerce_order_status_completed', $plugin_public, 'order_completed', 9, 1 );
			}

			if ( $settings["woocommerce_event_settings"]["track_woocommerce_fieldset"]["woocommerce_events"]["track_order_paid_fieldset"] == 'yes' ) {
				$this->loader->add_action( 'woocommerce_payment_complete', $plugin_public, 'order_paid', 9, 1 );

			}

			if ( $settings["woocommerce_event_settings"]["track_woocommerce_fieldset"]["woocommerce_events"]["track_order_cancelled_fieldset"] ) {
				$this->loader->add_action( 'woocommerce_order_status_cancelled', $plugin_public, 'order_cancelled', 9, 1 );

			}

			if ( $settings["woocommerce_event_settings"]["track_woocommerce_fieldset"]["woocommerce_events"]["track_coupons_fieldset"]["track_coupons"] == 'yes' ) {
				$this->loader->add_action( 'woocommerce_applied_coupon', $plugin_public, 'coupon_added', 9, 1 );

			}

			// TODO order on hold? order refunded? order cancelled?
		}

		//LEARNDASH
		if ( $settings["learndash_event_settings"]["track_learndash_fieldset"]["track_learndash"] == "yes" ) {
			if ( $settings["learndash_event_settings"]["track_learndash_fieldset"]["track_enrollments_fieldset"]["track_enrollments"] ) {
				$this->loader->add_action( 'learndash_update_course_access', $plugin_public, 'enrolled_in_course', 1, 4 );
				$this->loader->add_action( 'ld_group_postdata_updated', $plugin_public, 'enrolled_in_course_via_group', 1, 4 );
			}
			if ( $settings["learndash_event_settings"]["track_learndash_fieldset"]["track_topics_fieldset"]["track_topics"] == "yes" ) {
				$this->loader->add_action( 'learndash_topic_completed', $plugin_public, 'topic_completed', 9, 1 );

			}
			if ( $settings["learndash_event_settings"]["track_learndash_fieldset"]["track_lessons_fieldset"]["track_lessons"] == "yes" ) {
				$this->loader->add_action( 'learndash_lesson_completed', $plugin_public, 'lesson_completed', 9, 1 );

			}
			if ( $settings["learndash_event_settings"]["track_learndash_fieldset"]["track_courses_fieldset"]["track_courses"] == "yes" ) {
				$this->loader->add_action( 'learndash_course_completed', $plugin_public, 'course_completed', 1, 1 );

			}
			if ( $settings["learndash_event_settings"]["track_learndash_fieldset"]["track_quizzes_fieldset"]["track_quizzes"] ) {
				$this->loader->add_action( 'learndash_quiz_completed', $plugin_public, 'quiz_completed', 9, 2 );
			}

			if ( $settings["learndash_event_settings"]["track_learndash_fieldset"]["track_assignments_fieldset"]["track_assignments"] ) {
				$this->loader->add_action( 'learndash_assignment_uploaded', $plugin_public, 'assignment_uploaded', 9, 1 );
			}

		}

	}

	/**
	 * Register all of the hooks related to the server side asynchronous events
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_async_hooks() {
		$settings = get_exopite_sof_option( 'all-in-one-analytics' );

		$plugin_async_events = new All_In_One_Analytics_Async_Events( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'plugins_loaded', $plugin_async_events, 'init' );

		//CORE
		if ( $settings["core_event_settings"]["track_comments_fieldset"]["track_comments"] == "yes" ) {
			$this->loader->add_action( 'wp_insert_comment', $plugin_async_events, 'all_in_one_analytics_async_events_add', 9, 2 );
		}
		if ( $settings["core_event_settings"]['track_logins_fieldset']['track_logins'] == "yes" ) {
			$this->loader->add_action( 'wp_login', $plugin_async_events, 'all_in_one_analytics_async_events_add', 9, 2 );
		}
		if ( $settings["core_event_settings"]['track_signups_fieldset']['track_signups'] == "yes" ) {
			$this->loader->add_action( 'user_register', $plugin_async_events, 'all_in_one_analytics_async_events_add', 9, 1 );
		}

		//FORMS
		if ( $settings["form_event_settings"]['track_ninja_forms_fieldset']['track_ninja_forms'] === 'yes' ) {
			$this->loader->add_action( 'ninja_forms_after_submission', $plugin_async_events, 'all_in_one_analytics_async_events_add', 9, 1 );
		}
		if ( $settings["form_event_settings"]['track_gravity_forms_fieldset']['track_gravity_forms'] === 'yes' ) {
			$this->loader->add_action( 'gform_after_submission', $plugin_async_events, 'all_in_one_analytics_async_events_add', 9, 2 );
		}

		//WOOCOMMERCE
		if ( $settings["woocommerce_event_settings"]['track_woocommerce_fieldset']['track_woocommerce'] === 'yes' ) {

			$this->loader->add_action( 'woocommerce_add_to_cart', $plugin_async_events, 'all_in_one_analytics_async_events_add', 9, 6 );
			$this->loader->add_action( 'woocommerce_remove_cart_item', $plugin_async_events, 'all_in_one_analytics_async_events_add', 9, 2 );
			$this->loader->add_action( 'woocommerce_cart_item_restored', $plugin_async_events, 'all_in_one_analytics_async_events_add', 9, 2 );
			$this->loader->add_action( 'woocommerce_order_status_pending', $plugin_async_events, 'all_in_one_analytics_async_events_add', 9, 1 );
			$this->loader->add_action( 'woocommerce_order_status_processing', $plugin_async_events, 'all_in_one_analytics_async_events_add', 9, 1 );
			$this->loader->add_action( 'woocommerce_order_status_completed', $plugin_async_events, 'all_in_one_analytics_async_events_add', 9, 1 );
			$this->loader->add_action( 'woocommerce_payment_complete', $plugin_async_events, 'all_in_one_analytics_async_events_add', 9, 1 );
			$this->loader->add_action( 'woocommerce_order_status_cancelled', $plugin_async_events, 'all_in_one_analytics_async_events_add', 9, 1 );
			$this->loader->add_action( 'woocommerce_applied_coupon', $plugin_async_events, 'all_in_one_analytics_async_events_add', 9, 1 );

		}

		//LEARNDASH
		if ( $settings["learndash_event_settings"]["track_learndash_fieldset"]["track_learndash"] == "yes" ) {
			if ( $settings["learndash_event_settings"]["track_learndash_fieldset"]["track_enrollments_fieldset"]["track_enrollments"] ) {
				$this->loader->add_action( 'learndash_update_course_access', $plugin_async_events, 'all_in_one_analytics_async_events_add', 9, 4 );
			}
			if ( $settings["learndash_event_settings"]["track_learndash_fieldset"]["track_topics_fieldset"]["track_topics"] == "yes" ) {
				$this->loader->add_action( 'learndash_topic_completed', $plugin_async_events, 'all_in_one_analytics_async_events_add', 9, 1 );

			}
			if ( $settings["learndash_event_settings"]["track_learndash_fieldset"]["track_lessons_fieldset"]["track_lessons"] == "yes" ) {
				$this->loader->add_action( 'learndash_lesson_completed', $plugin_async_events, 'all_in_one_analytics_async_events_add', 9, 1 );

			}
			if ( $settings["learndash_event_settings"]["track_learndash_fieldset"]["track_courses_fieldset"]["track_courses"] == "yes" ) {
				$this->loader->add_action( 'learndash_course_completed', $plugin_async_events, 'all_in_one_analytics_async_events_add', 9, 1 );

			}
			if ( $settings["learndash_event_settings"]["track_learndash_fieldset"]["track_quizzes_fieldset"]["track_quizzes"] ) {
				$this->loader->add_action( 'learndash_quiz_completed', $plugin_async_events, 'all_in_one_analytics_async_events_add', 9, 1 );
			}

		}


		//Clean up DB
		$plugin_async_clean_db = new All_In_One_Analytics_Async_Process( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'plugins_loaded', $plugin_async_clean_db, 'init' );
		$this->loader->add_action( 'add_to_queue', $plugin_async_clean_db, 'all_in_one_analytics_async_process_add', 9, 1 );
		$this->loader->add_action( 'dispatch_queue', $plugin_async_clean_db, 'all_in_one_analytics_async_process_dispatch', 9 );


	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return    string    The name of the plugin.
	 * @since     1.0.0
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return    All_In_One_Analytics_Loader    Orchestrates the hooks of the plugin.
	 * @since     1.0.0
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return    string    The version number of the plugin.
	 * @since     1.0.0
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Create a settings array to pass to async.analytics.js.
	 *
	 * @return    array of settings
	 * @since     1.0.0
	 */
	public function get_analytics_settings() {
		$settings                 = get_exopite_sof_option( 'all-in-one-analytics' );
		$analytics_settings_array = array();
		//GA
		if ( $settings["Google Analytics"]["google_analytics_switcher"] == "yes" && $settings["Google Analytics"]["google_analytics_settings"]["trackingId"] !== "" ) {
			$analytics_settings_array["Google Analytics"]                         = array();
			$analytics_settings_array["Google Analytics"]["trackingId"]           = sanitize_text_field( $settings["Google Analytics"]["google_analytics_settings"]["trackingId"] );
			$analytics_settings_array["Google Analytics"]["anonymizeIp"]          = $settings["Google Analytics"]["google_analytics_settings"]["other_settings"]["anonymizeIp"];
			$analytics_settings_array["Google Analytics"]["useGoogleAmpClientId"] = $settings["Google Analytics"]["google_analytics_settings"]["other_settings"]["useGoogleAmpClientId"];
			$analytics_settings_array["Google Analytics"]["classic"]              = $settings["Google Analytics"]["google_analytics_settings"]["classic_analytics"]["classic"];
			if ( isset( $settings["Google Analytics"]["google_analytics_settings"]["property_trait_mapping"]["groupings"] ) ) {
				if ( is_array( $settings["Google Analytics"]["google_analytics_settings"]["property_trait_mapping"]["groupings"] ) ) {
					$content_groupings = array_merge( $settings["Google Analytics"]["google_analytics_settings"]["property_trait_mapping"]["groupings"] );
					$groupings_array   = Array();
					foreach ( $content_groupings as $current_grouping ) {
						if ( $current_grouping["local_trait"] !== "" ) {
							$key                     = sanitize_text_field( $current_grouping["local_trait"] );
							$value                   = $current_grouping["ga_value"];
							$groupings_array[ $key ] = $value;
						}
						$analytics_settings_array["Google Analytics"]["contentGroupings"] = array_merge( $groupings_array );

					}

				}
			}
			if ( isset( $settings["Google Analytics"]["google_analytics_settings"]["custom_dimensions_metrics"]["dimensions"] ) ) {
				if ( is_array( $settings["Google Analytics"]["google_analytics_settings"]["custom_dimensions_metrics"]["dimensions"] ) ) {
					$custom_dimensions = array_merge( $settings["Google Analytics"]["google_analytics_settings"]["custom_dimensions_metrics"]["dimensions"] );
					$dimensions_array  = Array();
					foreach ( $custom_dimensions as $current_dimension ) {
						if ( $current_dimension["local_dimension"] !== "" ) {
							$key                      = sanitize_text_field( $current_dimension["local_dimension"] );
							$value                    = $current_dimension["ga_dimension"];
							$dimensions_array[ $key ] = $value;
						}
					}
					$analytics_settings_array["Google Analytics"]["dimensions"] = $dimensions_array;
				}
			}
			$analytics_settings_array["Google Analytics"]["domain"] = "auto";
			if ( $settings["Google Analytics"]["google_analytics_settings"]["other_settings"]["domain"] !== "" ) {
				$analytics_settings_array["Google Analytics"]["domain"] = $settings["Google Analytics"]["google_analytics_settings"]["other_settings"]["domain"];
			}
			$analytics_settings_array["Google Analytics"]["doubleClick"]             = $settings["Google Analytics"]["google_analytics_settings"]["reporting"]["doubleClick"];
			$analytics_settings_array["Google Analytics"]["enhancedEcommerce"]       = $settings["Google Analytics"]["google_analytics_settings"]["other_settings"]["enhancedEcommerce"];
			$analytics_settings_array["Google Analytics"]["enhancedLinkAttribution"] = $settings["Google Analytics"]["google_analytics_settings"]["reporting"]["enhancedLinkAttribution"];
			$ignored_referrers                                                       = $settings["Google Analytics"]["google_analytics_settings"]["classic_analytics"]["ignoredReferrers"];
			$ignored_referrers                                                       = preg_replace( '/\s+/', '\n', $ignored_referrers );
			$analytics_settings_array["Google Analytics"]["ignoredReferrers"]        = $ignored_referrers;
			$analytics_settings_array["Google Analytics"]["includeSearch"]           = $settings["Google Analytics"]["google_analytics_settings"]["track_pages"]["includeSearch"];
			if ( isset( $settings["Google Analytics"]["google_analytics_settings"]["custom_dimensions_metrics"]["setAllMappedProps"] ) ) {
				if ( is_array( $settings["Google Analytics"]["google_analytics_settings"]["custom_dimensions_metrics"]["setAllMappedProps"] ) ) {
					$analytics_settings_array["Google Analytics"]["setAllMappedProps"] = $settings["Google Analytics"]["google_analytics_settings"]["custom_dimensions_metrics"]["setAllMappedProps"];
				}
			}
			if ( isset( $settings["Google Analytics"]["google_analytics_settings"]["custom_dimensions_metrics"]["metrics"] ) ) {
				if ( is_array( $settings["Google Analytics"]["google_analytics_settings"]["custom_dimensions_metrics"]["metrics"] ) ) {
					$custom_metrics = array_merge( $settings["Google Analytics"]["google_analytics_settings"]["custom_dimensions_metrics"]["metrics"] );
					$metrics_array  = Array();
					foreach ( $custom_metrics as $current_metric ) {
						if ( $current_metric["local_metric"] !== "" ) {
							$key                   = sanitize_text_field( $current_metric["local_metric"] );
							$value                 = $current_metric["ga_metric"];
							$metrics_array[ $key ] = $value;
						}
					}
					$analytics_settings_array["Google Analytics"]["metrics"] = $metrics_array;
				}
			}
			$analytics_settings_array["Google Analytics"]["nonInteraction"]      = $settings["Google Analytics"]["google_analytics_settings"]["other_settings"]["nonInteraction"];
			$analytics_settings_array["Google Analytics"]["sendUserId"]          = $settings["Google Analytics"]["google_analytics_settings"]["other_settings"]["sendUserId"];
			$analytics_settings_array["Google Analytics"]["siteSpeedSampleRate"] = absint( $settings["Google Analytics"]["google_analytics_settings"]["sampling"]["siteSpeedSampleRate"] );
			$analytics_settings_array["Google Analytics"]["sampleRate"]          = absint( $settings["Google Analytics"]["google_analytics_settings"]["sampling"]["sampleRate"] );
			$analytics_settings_array["Google Analytics"]["trackNamedPages"]     = $settings["Google Analytics"]["google_analytics_settings"]["track_pages"]["trackNamedPages"];
			$analytics_settings_array["Google Analytics"]["optimize"]            = $settings["Google Analytics"]["google_analytics_settings"]["other_settings"]["optimize"];
			$analytics_settings_array["Google Analytics"]["nameTracker"]         = $settings["Google Analytics"]["google_analytics_settings"]["other_settings"]["nameTracker"];
			foreach ( $analytics_settings_array["Google Analytics"] as $key => &$value ) {
				if ( $value === "yes" ) {
					$value = true;
				}
				if ( $value === "no" ) {
					$value = false;
				}
			}
		}

		//FB
		if ( $settings["Facebook Pixel"]["facebook_pixel_switcher"] == "yes" && $settings["Facebook Pixel"]["facebook_pixel_settings"]["pixelId"] !== "" ) {
			$analytics_settings_array["Facebook Pixel"]                           = array();
			$analytics_settings_array["Facebook Pixel"]["pixelId"]                = sanitize_text_field( $settings["Facebook Pixel"]["facebook_pixel_settings"]["pixelId"] );
			$analytics_settings_array["Facebook Pixel"]["automaticConfiguration"] = true;
			$analytics_settings_array["Facebook Pixel"]["valueIdentifier"]        = $settings["Facebook Pixel"]["facebook_pixel_settings"]["connection_settings"]["valueIdentifier"];
			$analytics_settings_array["Facebook Pixel"]["initWithExistingTraits"] = $settings["Facebook Pixel"]["facebook_pixel_settings"]["other_settings"]["initWithExistingTraits"];
			$analytics_settings_array["Facebook Pixel"]["blacklistPiiProperties"] = $settings["Facebook Pixel"]["facebook_pixel_settings"]["connection_settings"]["blacklist_properties"]["blacklistPiiProperties"];
			$analytics_settings_array["Facebook Pixel"]["standardEvents"]         = $settings["Facebook Pixel"]["facebook_pixel_settings"]["connection_settings"]["map_events"]["standardEvents"];
			$analytics_settings_array["Facebook Pixel"]["contentTypes"]           = $settings["Facebook Pixel"]["facebook_pixel_settings"]["connection_settings"]["map_categories"]["contentTypes"];
			$analytics_settings_array["Facebook Pixel"]["legacyEvents"]           = $settings["Facebook Pixel"]["facebook_pixel_settings"]["other_settings"]["map_pixels"]["legacyEvents"];
			/*
			  .option('agent', 'seg')
			  .option('traverse', false)
			*/
			foreach ( $analytics_settings_array["Facebook Pixel"] as $key => &$value ) {
				if ( $value === "yes" ) {
					$value = true;
				}
				if ( $value === "no" ) {
					$value = false;
				}
			}

			if ( isset( $analytics_settings_array["Facebook Pixel"]["blacklistPiiProperties"] ) ) {
				if ( is_array( $analytics_settings_array["Facebook Pixel"]["blacklistPiiProperties"] ) ) {
					foreach ( $analytics_settings_array["Facebook Pixel"]["blacklistPiiProperties"] as &$blacklist ) {
						foreach ( $blacklist as $key => &$value ) {
							if ( $value === "yes" ) {
								$value = true;
							}
							if ( $value === "no" ) {
								$value = false;
							}

						}
					}
				}
			}
		}

		//GTM
		//GTM
		if ( $settings["Google Tag Manager"]["google_tag_manager_switcher"] == "yes" && $settings["Google Tag Manager"]["google_tag_manager_settings"]["containerId"] !== "" ) {
			$analytics_settings_array["Google Tag Manager"]                          = array();
			$analytics_settings_array["Google Tag Manager"]["containerId"]           = sanitize_text_field( $settings["Google Tag Manager"]["google_tag_manager_settings"]["containerId"] );
			$analytics_settings_array["Google Tag Manager"]["environment"]           = sanitize_text_field( $settings["Google Tag Manager"]["google_tag_manager_settings"]["other_settings"]["environment"] );
			$analytics_settings_array["Google Tag Manager"]["trackNamedPages"]       = $settings["Google Tag Manager"]["google_tag_manager_settings"]["other_settings"]["trackNamedPages"];
			$analytics_settings_array["Google Tag Manager"]["trackCategorizedPages"] = $settings["Google Tag Manager"]["google_tag_manager_settings"]["other_settings"]["trackCategorizedPages"];
			foreach ( $analytics_settings_array["Google Tag Manager"] as $key => &$value ) {
				if ( $value === "yes" ) {
					$value = true;
				}
				if ( $value === "no" ) {
					$value = false;
				}
			}
		}

		//AdWords Old
		if ( $settings["Google Ads"]["google_ads_switcher"] == "yes" && $settings["Google Ads"]["google_ads_settings"]["conversionId"] !== "" ) {
			$analytics_settings_array["AdWords"] = array();
			if ( isset ( $settings["Google Ads"]["google_ads_settings"]["conversionId"] ) ) {
				$analytics_settings_array["AdWords"]["conversionId"] = $settings["Google Ads"]["google_ads_settings"]["conversionId"];
			}

			if ( isset ( $settings["Google Ads"]["google_ads_settings"]["google_ads_old_settings"]["pageRemarketing"] ) ) {
				$analytics_settings_array["AdWords"]["pageRemarketing"] = $settings["Google Ads"]["google_ads_settings"]["google_ads_old_settings"]["pageRemarketing"];
			}

			if ( isset ( $settings["Google Ads"]["google_ads_settings"]["google_ads_old_settings"]["event_mappings"]["eventMappings"] ) ) {
				$analytics_settings_array["AdWords"]["eventMappings"] = $settings["Google Ads"]["google_ads_settings"]["google_ads_old_settings"]["event_mappings"]["eventMappings"];
			}
		}

		//AdWords New
		if ( $settings["Google Ads"]["google_ads_switcher"] == "yes" && $settings["Google Ads"]["google_ads_settings"]["google_ads_new_switcher"] == "yes" && $settings["Google Ads"]["google_ads_settings"]["conversionId"] !== "" ) {
			$analytics_settings_array["Google AdWords New"] = array();

			if ( isset ( $settings["Google Ads"]["google_ads_settings"]["conversionIdNew"] ) ) {
				$analytics_settings_array["Google AdWords New"]["conversionId"] = $settings["Google Ads"]["google_ads_settings"]["conversionIdNew"];
			}
			if ( isset ( $settings["Google Ads"]["google_ads_settings"]["google_ads_new_click_conversions"]["clickConversions"] ) ) {
				$analytics_settings_array["Google AdWords New"]["clickConversions"] = $settings["Google Ads"]["google_ads_settings"]["google_ads_new_click_conversions"]["clickConversions"];
			}
			if ( isset ( $settings["Google Ads"]["google_ads_settings"]["google_ads_new_page_conversions"]["pageLoadConversions"] ) ) {
				$analytics_settings_array["Google AdWords New"]["pageLoadConversions"] = $settings["Google Ads"]["google_ads_settings"]["google_ads_new_page_conversions"]["pageLoadConversions"];
			}
			if ( isset ( $settings["Google Ads"]["google_ads_settings"]["google_ads_new_other_settings"]["conversionLinker"] ) ) {
				$analytics_settings_array["Google AdWords New"]["conversionLinker"] = $settings["Google Ads"]["google_ads_settings"]["google_ads_new_other_settings"]["conversionLinker"];
			}
			if ( isset ( $settings["Google Ads"]["google_ads_settings"]["google_ads_new_other_settings"]["defaultPageConversion"] ) ) {
				$analytics_settings_array["Google AdWords New"]["defaultPageConversion"] = $settings["Google Ads"]["google_ads_settings"]["google_ads_new_other_settings"]["defaultPageConversion"];
			}
			if ( isset ( $settings["Google Ads"]["google_ads_settings"]["google_ads_new_other_settings"]["sendPageView"] ) ) {
				$analytics_settings_array["Google AdWords New"]["sendPageView"] = $settings["Google Ads"]["google_ads_settings"]["google_ads_new_other_settings"]["sendPageView"];
			}
		}

		//Zapier doesn't need to be included here since we don't send those to async.analytics.js

		//JSON encode
		$analytics_settings_array = json_encode( $analytics_settings_array );

		//RETURN
		return $analytics_settings_array;
	}

	/**
	 * Checks if user is trackable
	 *
	 * @param $user
	 *
	 * @return boolean true if trackable
	 */
	public static function check_trackable_user( $user ) {

		if ( is_user_logged_in( $user ) && is_object( $user ) ) {
			$user_roles   = ( array ) $user->roles;
			$current_role = $user_roles[0];
			$settings     = get_exopite_sof_option( 'all-in-one-analytics' );
			if ( isset( $settings['ignored_users'] ) ) {
				$excluded_roles = $settings['ignored_users'];
			}
			if ( isset( $excluded_roles ) ) {
				if ( ! in_array( $current_role, $excluded_roles ) ) {
					return true;
				} else { //not trackable
					return false;
				}
			} else {
				return true;
			}
		} else { //logged out
			return true;
		}
	}

	/**
	 * Check if Post is trackable post
	 *
	 * @param $current_post
	 *
	 * @return boolean true if trackable
	 */
	public static function check_trackable_post( $current_post ) {
		$settings = get_exopite_sof_option( 'all-in-one-analytics' );

		if ( $settings["track_wp_admin"] === "no" && is_admin() === true ) {
			return false;
		}

		$post_type = get_post_type( $current_post );

		if ( isset( $settings['ignored_post_types'] ) ) {
			$excluded_post_types = $settings['ignored_post_types'];
		}

		if ( isset( $excluded_post_types ) ) {
			if ( in_array( $post_type, $excluded_post_types ) ) {
				return false;
			}
		}

		if ( isset( $settings['ignored_categories'] ) ) {
			$excluded_categories = $settings['ignored_categories'];
		}
		if ( isset( $excluded_categories ) ) {
			$current_categories = get_the_category();
			foreach ( $current_categories as $category ) {
				if ( in_array( $category->name, $excluded_categories ) ) {
					return false;
				}
			}

		}

		return true;
	}

	/**
	 * Returns the name of the current event being processed
	 *
	 * @param $action
	 *
	 * @return string
	 */
	public static function get_event_name( $action ) {

		$settings         = get_exopite_sof_option( 'all-in-one-analytics' );
		$event_name_array = array(

			//CORE
			"wp_login"                            => "Logged in",
			"wp_insert_comment"                   => "Commented",
			"user_register"                       => "Signed up",

			//FORMS
			"ninja_forms_after_submission"        => "Completed Form",
			"gform_after_submission"              => "Completed Form",

			//WOOCOMMERCE
			"woocommerce_before_single_product"   => "Product Viewed",
			"is_product"                          => "Product Viewed",
			"product_clicked"                     => "Product Clicked", //DIY
			"woocommerce_add_to_cart"             => "Product Added",
			"woocommerce_ajax_added_to_cart"      => "Product Added",
			"woocommerce_remove_cart_item"        => "Product Removed",
			"woocommerce_cart_item_restored"      => "Product Readded",
			"woocommerce_before_cart"             => "Cart Viewed",
			"is_cart"                             => "Cart Viewed",
			"is_checkout"                         => "Checkout Step Viewed",
			//"woocommerce_before_checkout_form"    => "Checkout Step Viewed",
			"woocommerce_checkout_process"        => "Checkout Started",
			"woocommerce_order_status_completed"  => "Order Completed",
			"woocommerce_payment_complete"        => "Order Paid",
			"woocommerce_order_status_pending"    => "Order Pending",
			"woocommerce_order_status_failed"     => "Order Failed",
			"woocommerce_order_status_on-hold"    => "Order On-Hold",
			"woocommerce_order_status_processing" => "Order Processing",
			"woocommerce_order_status_refunded"   => "Order Refunded",
			"woocommerce_order_status_cancelled"  => "Order Cancelled",
			"woocommerce_applied_coupon"          => "Coupon Applied",

			//LEARNDASH
			"learndash_update_course_access"      => "Enrolled",
			"ld_group_postdata_updated"           => "Enrolled",
			"learndash_topic_completed"           => "Topic Completed",
			"learndash_lesson_completed"          => "Lesson Completed",
			"learndash_course_completed"          => "Course Completed",
			"learndash_quiz_completed"            => "Quiz Completed",
			"learndash_assignment_uploaded"       => "Assignment Uploaded",

		);
		$event_name       = $event_name_array[ $action ];

		if ( $event_name == "Logged in" && $settings["core_event_settings"]['track_logins_fieldset']['track_logins_custom_event_label'] !== "" ) {
			$event_name = sanitize_text_field( $settings["core_event_settings"]['track_logins_fieldset']['track_logins_custom_event_label'] );

			return $event_name;

		}

		if ( $event_name == "Signed up" && $settings["core_event_settings"]['track_signups_fieldset']['track_signups_custom_event_label'] !== "" ) {
			$event_name = sanitize_text_field( $settings["core_event_settings"]['track_signups_fieldset']['track_signups_custom_event_label'] );

			return $event_name;

		}

		if ( $event_name == "Commented" && $settings["core_event_settings"]['track_comments_fieldset']['track_comments_custom_event_label'] !== "" ) {
			$event_name = sanitize_text_field( $settings["core_event_settings"]['track_comments_fieldset']['track_comments_custom_event_label'] );

			return $event_name;

		}

		if ( $event_name == "Completed Form" ) {
			$event_name = "Completed Form";

			return $event_name;
		}

		if ( $event_name == "Product Viewed" ) {
			$event_name = "Product Viewed";

			return $event_name;
		}

		if ( $event_name == "Product Added" && $settings["woocommerce_event_settings"]["woocommerce_events_labels"]["woocommerce_events_custom_labels"]["woocommerce_events_product_added"] !== "" ) {
			$event_name = sanitize_text_field( $settings["woocommerce_event_settings"]["woocommerce_events_labels"]["woocommerce_events_custom_labels"]["woocommerce_events_product_added"] );

			return $event_name;

		}

		if ( $event_name == "Product Removed" && $settings["woocommerce_event_settings"]["woocommerce_events_labels"]["woocommerce_events_custom_labels"]["woocommerce_events_product_removed"] !== "" ) {
			$event_name = sanitize_text_field( $settings["woocommerce_event_settings"]["woocommerce_events_labels"]["woocommerce_events_custom_labels"]["woocommerce_events_product_removed"] );

			return $event_name;

		}

		if ( $event_name == "Cart Viewed" && $settings["woocommerce_event_settings"]["woocommerce_events_labels"]["woocommerce_events_custom_labels"]["woocommerce_events_cart_viewed"] !== "" ) {
			$event_name = sanitize_text_field( $settings["woocommerce_event_settings"]["woocommerce_events_labels"]["woocommerce_events_custom_labels"]["woocommerce_events_cart_viewed"] );

			return $event_name;
		}

		if ( $event_name == "Checkout Viewed" ) {
			$event_name = "Checkout Viewed"; //TODO no option to set this in the plugin ui, fix

			return $event_name;
		}

		if ( $event_name == "Order Processing" && $settings["woocommerce_event_settings"]["woocommerce_events_labels"]["woocommerce_events_custom_labels"]["woocommerce_event_order_processing"] !== "" ) {
			$event_name = sanitize_text_field( $settings["woocommerce_event_settings"]["woocommerce_events_labels"]["woocommerce_events_custom_labels"]["woocommerce_event_order_processing"] );

			return $event_name;

		}

		if ( $event_name == "Order Completed" && $settings["woocommerce_event_settings"]["woocommerce_events_labels"]["woocommerce_events_custom_labels"]["woocommerce_event_order_completed"] !== "" ) {
			$event_name = sanitize_text_field( $settings["woocommerce_event_settings"]["woocommerce_events_labels"]["woocommerce_events_custom_labels"]["woocommerce_event_order_completed"] );

			return $event_name;

		}

		if ( $event_name == "Order Paid" && $settings["woocommerce_event_settings"]["woocommerce_events_labels"]["woocommerce_events_custom_labels"]["woocommerce_event_order_paid"] !== "" ) {
			$event_name = sanitize_text_field( $settings["woocommerce_event_settings"]["woocommerce_events_labels"]["woocommerce_events_custom_labels"]["woocommerce_event_order_paid"] );

			return $event_name;

		}

		if ( $event_name == "Order Pending" && $settings["woocommerce_event_settings"]["woocommerce_events_labels"]["woocommerce_events_custom_labels"]["woocommerce_event_order_pending"] !== "" ) {
			$event_name = sanitize_text_field( $settings["woocommerce_event_settings"]["woocommerce_events_labels"]["woocommerce_events_custom_labels"]["woocommerce_event_order_pending"] );

			return $event_name;

		}

		if ( $event_name == "Order Failed" && $settings["woocommerce_event_settings"]["woocommerce_events_labels"]["woocommerce_events_custom_labels"]["woocommerce_event_order_failed"] !== "" ) {
			$event_name = sanitize_text_field( $settings["woocommerce_event_settings"]["woocommerce_events_labels"]["woocommerce_events_custom_labels"]["woocommerce_event_order_failed"] );

			return $event_name;

		}

		if ( $event_name == "Order Refunded" && $settings["woocommerce_event_settings"]["woocommerce_events_labels"]["woocommerce_events_custom_labels"]["woocommerce_event_order_refunded"] !== "" ) {
			$event_name = sanitize_text_field( $settings["woocommerce_event_settings"]["woocommerce_events_labels"]["woocommerce_events_custom_labels"]["woocommerce_event_order_refunded"] );

			return $event_name;

		}

		if ( $event_name == "Order Cancelled" && $settings["woocommerce_event_settings"]["woocommerce_events_labels"]["woocommerce_events_custom_labels"]["woocommerce_event_order_cancelled"] !== "" ) {
			$event_name = sanitize_text_field( $settings["woocommerce_event_settings"]["woocommerce_events_labels"]["woocommerce_events_custom_labels"]["woocommerce_event_order_cancelled"] );

			return $event_name;

		}

		if ( $event_name == "Order On-Hold" && $settings["woocommerce_event_settings"]["woocommerce_events_labels"]["woocommerce_events_custom_labels"]["woocommerce_event_order_on_hold"] !== "" ) {
			$event_name = sanitize_text_field( $settings["woocommerce_event_settings"]["woocommerce_events_labels"]["woocommerce_events_custom_labels"]["woocommerce_event_order_on_hold"] );

			return $event_name;

		}

		if ( $event_name == "Coupon Applied" && $settings["woocommerce_event_settings"]["woocommerce_events_labels"]["woocommerce_events_custom_labels"]["woocommerce_event_order_coupon_applied"] !== "" ) {
			$event_name = sanitize_text_field( $settings["woocommerce_event_settings"]["woocommerce_events_labels"]["woocommerce_events_custom_labels"]["woocommerce_event_order_coupon_applied"] );

			return $event_name;

		}

		if ( $event_name == "Enrolled" && $settings["learndash_event_settings"]["track_learndash_fieldset"]["track_enrollments_fieldset"]["track_enrollments_custom_event_label"] !== "" ) {
			$event_name = sanitize_text_field( $settings["learndash_event_settings"]["track_learndash_fieldset"]["track_enrollments_fieldset"]["track_enrollments_custom_event_label"] );

			return $event_name;
		}

		if ( $event_name == "Topic Completed" && $settings["learndash_event_settings"]["track_learndash_fieldset"]["track_topics_fieldset"]["track_topics_custom_event_label"] !== "" ) {
			$event_name = sanitize_text_field( $settings["learndash_event_settings"]["track_learndash_fieldset"]["track_topics_fieldset"]["track_topics_custom_event_label"] );

			return $event_name;
		}

		if ( $event_name == "Lesson Completed" && $settings["learndash_event_settings"]["track_learndash_fieldset"]["track_lessons_fieldset"]["track_lessons_custom_event_label"] !== "" ) {
			$event_name = sanitize_text_field( $settings["learndash_event_settings"]["track_learndash_fieldset"]["track_lessons_fieldset"]["track_lessons_custom_event_label"] );

			return $event_name;
		}

		if ( $event_name == "Course Completed" && $settings["learndash_event_settings"]["track_learndash_fieldset"]["track_courses_fieldset"]["track_courses_custom_event_label"] !== "" ) {
			$event_name = sanitize_text_field( $settings["learndash_event_settings"]["track_learndash_fieldset"]["track_courses_fieldset"]["track_courses_custom_event_label"] );

			return $event_name;
		}

		if ( $event_name == "Quiz Completed" && $settings["learndash_event_settings"]["track_learndash_fieldset"]["track_quizzes_fieldset"]["track_quizzes_custom_event_label"] !== "" ) {
			$event_name = sanitize_text_field( $settings["learndash_event_settings"]["track_learndash_fieldset"]["track_quizzes_fieldset"]["track_quizzes_custom_event_label"] );

			return $event_name;
		}

		if ( $event_name == "Assignment Uploaded" && $settings["learndash_event_settings"]["track_learndash_fieldset"]["track_assignments_fieldset"]["track_assignments_custom_event_label"] !== "" ) {
			$event_name = sanitize_text_field( $settings["learndash_event_settings"]["track_learndash_fieldset"]["track_assignments__fieldset"]["track_assignments__custom_event_label"] );

			return $event_name;

		}

		return $event_name;
	}

	/**
	 * Also attempts to return the user id of the current event being processed
	 * TO-DO refactor to only use this function below
	 * It's easier to get user id when only dealing with js events, once you go server side you have to address it
	 * for each of the action hooks, like so:
	 *
	 * @param $action_hook
	 * @param $data
	 *
	 * @return string
	 */
	public static function get_user_id( $action_hook, $data ) {
		$settings = get_exopite_sof_option( 'all-in-one-analytics' );

		//SIGNUPS
		if ( $action_hook == "user_register" ) {

			if ( isset( $data["args"][0] ) ) {
				$user_id = $data["args"][0];

				return $user_id;
			}

			if ( isset( $data[0] ) ) {
				$user_id = $data[0];

				return $user_id;
			}


		}

		//LOGINS
		if ( $action_hook == "wp_login" ) {
			//$login, $user

			if ( isset( $data["args"][1]['ID'] ) ) {
				$user_id = $data["args"][1]['ID'];

				return $user_id;
			}

			if ( isset( $data["args"][1]['data']['ID'] ) ) {
				$user_id = $data["args"][1]['data']['ID'];

				return $user_id;
			}

		}

		//COMMENTS
		if ( $action_hook == "wp_insert_comment" ) {

			//args[0]=$comment_id, args[1]=$comment
			if ( isset ( $data["args"][1]["user_id"] ) ) {
				$user_id = $data["args"][1]["user_id"];

				return $user_id;

			} elseif ( isset ( $data["args"][1]["comment_author_email"] ) ) {
				$user_email = $data["args"][1]["comment_author_email"];
				if ( email_exists( $user_email ) ) {
					$user_id = email_exists( $user_email );
				}

				return $user_id;
			}
		}

		//WOOCOMMERCE ORDER
		if ( self::check_ecommerce_order_hook( $action_hook ) ) {
			//data[0] is orderid
			if ( isset ( $data["order_id"] ) ) {

				$user_id = All_In_One_Analytics::get_user_id_from_order( $data["order_id"] );

				return $user_id;


			}
			if ( $data["action_current"] == "wp_ajax_nopriv_wp_all_in_one_analytics_async_events_process" || $data["action_current"] == "wp_ajax_wp_all_in_one_analytics_async_events_process" ) {

				$user_id = All_In_One_Analytics::get_user_id_from_order( $data["args"][0] );

				return $user_id;

			}

			return null;

		}

		if ( $action_hook == 'woocommerce_ajax_added_to_cart' ) {

			$user_id = get_current_user_id();
			if ( $user_id == 0 ) {
				unset( $user_id );
			}

		}

		//NINJA FORMS
		if ( $action_hook == "ninja_forms_after_submission" ) {
			//get from hidden field marked uid
			if ( current_action() == 'wp_footer' ) { //this is reduntant TODO refactor to avoid searching uid twice
				if ( isset( $data["userId"] ) ) {
					$user_id = $data["userId"];

					return $user_id;
				}
			}
			if ( isset( $data["args"][0]["fields_by_key"] ) ) {
				foreach ( $data["args"][0]["fields_by_key"] as $key => $value ) {

					if ( self::starts_with( $key, 'aio_id' ) ) {
						if ( is_email( $value ) ) {
							if ( email_exists( $value ) ) {
								$user_id = email_exists( $value );

								return $user_id;
							}
						}
						if ( is_email( $value ) ) {
							if ( email_exists( $value ) ) {
								$user_id = email_exists( $value );

								return $user_id;
							}

							if ( username_exists( $value ) ) {
								$user_id = username_exists( $value );

								return $user_id;
							}

						}

						if ( $value instanceof WP_User ) {
							$user_id = $value->ID;

							return $user_id;
						}
					}
				}

			}
		}
		//GRAVITY FORMS [0]=$entry [1]= $form
//TODO
		if ( $action_hook == "gform_after_submission" ) {
			$entry                 = $data["args"][0];
			$entry_id              = $entry['id'];
			$form_id               = $data["args"][0]["form_id"];
			$form                  = GFAPI::get_form( $form_id );
			$entry                 = GFAPI::get_entry( $entry_id );
			$properties["form_id"] = $form_id;
			$fields                = $form["fields"];
			foreach ( $fields as $key => $value ) {
				if ( isset( $value->label ) && self::starts_with( $value->label, 'aio_id' ) ) {

					if ( is_email( $value ) ) {
						if ( email_exists( $value ) ) {
							$user_id = email_exists( $value );

							return $user_id;
						}
					}

					if ( is_email( $value ) ) {

						if ( email_exists( $value ) ) {
							$user_id = email_exists( $value );

							return $user_id;
						}

						if ( username_exists( $value ) ) {
							$user_id = username_exists( $value );

							return $user_id;
						}

					}

					if ( $value instanceof WP_User ) {
						$user_id = $value->ID;

						return $user_id;
					}

				}
			}

		}

		//LEARNDASH
		if ( $action_hook == 'learndash_update_course_access' ) {

			if ( isset ( $data["args"][0] ) ) {
				$user_id = $data["args"][0];

				return $user_id;
			}
		}

		if ( $action_hook == "learndash_topic_completed" ) {

			if ( isset( $data["args"][0]["user"]["ID"] ) ) {
				$user_id = $data["args"][0]["user"]["ID"];

				return $user_id;

			}
		}

		if ( $action_hook == "learndash_lesson_completed" ) {

			if ( isset( $data["args"][0]["user"]["ID"] ) ) {
				$user_id = $data["args"][0]["user"]["ID"];

				return $user_id;
			}

		}

		if ( $action_hook == "learndash_course_completed" ) {

			if ( isset( $data["args"][0]["user"]["ID"] ) ) {
				$user_id = $data["args"][0]["user"]["ID"];

				return $user_id;
			}
		}

		if ( $action_hook == "learndash_quiz_completed" ) {

			// wip, but will use cookie for now

		}

		//if none found, look for it in cookies
		if ( ! isset ( $user_id ) && isset( $data['args']['cookie_user_id'] ) ) {
			$cookie_user_id = stripslashes_deep( $data['args']['cookie_user_id'] );
			if ( $cookie_user_id !== "" ) {
				$user_id = trim( $cookie_user_id, '"' );

				return $user_id;
			}
		}

		if ( ! isset ( $user_id ) && isset( $_COOKIE["ajs_user_id"] ) ) {
			$cookie_user_id = stripslashes_deep( $_COOKIE["ajs_user_id"] );
			if ( $cookie_user_id !== "" ) {
				$user_id = trim( $cookie_user_id, '"' );

				return $user_id;
			}
		}

		if ( ! isset ( $user_id ) && is_user_logged_in() ) { //not sure if always
			$user_id = get_current_user_id();

			return $user_id;
		}

		if ( ! isset( $user_id ) ) {
			return null;
		} else {
			return $user_id;
		}

	}

	/**
	 * Returns the properties for an event in a JSON encoded array
	 *
	 * @param $action_hook
	 * @param $user_id
	 * @param $args
	 *
	 * @return array
	 */
	public static function get_event_properties( $action_hook, $user_id, $args ) {
		$settings                     = get_exopite_sof_option( 'all-in-one-analytics' );
		$properties['noninteraction'] = true;


		//$properties = array();
		$user = get_user_by( 'ID', $user_id );

		if ( isset( $user ) && is_object( $user ) ) {
			if ( $settings["include_user_ids"] === 'yes' ) { // based on user settings
				$properties["userId"] = $user_id;
				$properties["email"]  = $user->user_email;
			}
		}

		if ( $action_hook === 'user_register' ) {

			$user = get_user_by( 'ID', $args['args'][0] );
			if ( isset ( $user ) ) {
				$properties['email']           = $user->user_email;
				$properties['display_name']    = $user->display_name;
				$properties['first_name']      = $user->first_name;
				$properties['last_name']       = $user->last_name;
				$properties['nickname']        = $user->nickname;
				$properties['user_nicename']   = $user->user_nicename;
				$properties["user_registered"] = $user->user_registered;
				$properties["createdAt"]       = gmdate( "Y-m-d\TH:i:s\Z" ); //timestamp
				$properties["createdAt"]       = gmdate( "Y-m-d\TH:i:s\Z" ); //timestamp
			}

			if ( isset ( $args["action_current"] ) && current_action() === $args["action_current"] ) {
				$properties = array_filter( $properties );

				return $properties;

			} else {

				$properties = array_filter( $properties );
				$properties = json_encode( array( $properties ) );

				return $properties;
			}

		}

		if ( $action_hook === 'wp_insert_comment' ) {

			if ( current_action() === $action_hook ) {

				foreach ( $args["args"][1] as $key => $value ) {
					$properties[ $key ] = $value;
				}

				$properties["commentId"] = $args["args"][0];

				$properties = array_filter( $properties );

				return $properties;
			}

			return $properties;

		}

		if ( $action_hook === 'wp_login' ) {

			$properties['user_login']      = $args['args'][1]['data']['user_login'];
			$properties['user_nicename']   = $args['args'][1]['data']['user_nicename'];
			$properties['user_email']      = $args['args'][1]['data']['user_email'];
			$properties['user_url']        = $args['args'][1]['data']['user_url'];
			$properties['user_registered'] = $args['args'][1]['data']['user_registered'];
			$properties['user_status']     = $args['args'][1]['data']['user_status'];
			$properties['user_nicename']   = $args['args'][1]['data']['user_nicename'];
			$properties['display_name']    = $args['args'][1]['data']['display_name'];

			$properties = array_filter( $properties );

			return $properties;

		}

		if ( $action_hook === 'ninja_forms_after_submission' ) {

			if ( isset( $args[0]["settings"]["title"] ) ) {
				$properties['form_title'] = $args[0]["settings"]["title"];
			}

			if ( isset( $args["args"][0]["settings"]["title"] ) ) {
				$properties['form_title'] = $args["args"][0]["settings"]["title"];
			}

			//args[0]=$entry args[1]= $form and NF args[0]=form_data object
			//extract values from each active field type and include key => value pairs in the track call
			if ( isset( $args["args"][0]["fields_by_key"] ) ) {
				foreach ( $args["args"][0]["fields_by_key"] as $key => $value ) {
					$properties[ $key ] = $value["value"];
				}
			}

			if ( isset( $args[0]["fields_by_key"] ) ) {
				foreach ( $args[0]["fields_by_key"] as $key => $value ) {
					$properties[ $key ] = $value["value"];
				}
			}

			$properties = array_merge_recursive( $properties );
			$properties = array_filter( $properties );

			return $properties;

		}

		if ( $action_hook === 'gform_after_submission' ) {

			// arg0=$entry arg1= $form
			if ( isset( $args["args"][0] ) ) {
				$entry = $args["args"][0];
				$form  = $args["args"][1];
			}
			if ( isset( $args[0] ) ) {
				$entry = $args[0];
				$form  = $args[1];
			}
			if ( isset( $entry["id"] ) && isset( $entry["form_id"] ) ) {
				$entry_id = $entry["id"];
				$form_id  = $entry["form_id"];
			}

			if ( isset( $form_id ) && isset( $entry_id ) ) {

				$form                           = GFAPI::get_form( $form_id );
				$entry                          = GFAPI::get_entry( $entry_id );
				$fields                         = $form["fields"];
				$properties['form_title']       = $form["title"];
				$properties['form_id']          = $form_id;
				$properties['entry_id']         = $entry_id;
				$properties['entry_source_url'] = $entry["source_url"];

				foreach ( $fields as $field ) {
					//$field_id = $field->id;

					//	$inputs = rgar( $entry, $field->id );

					$properties[ $field->label ] = rgar( $entry, $field->id );

				}

				return array_filter( $properties );

			}
		}

		if ( self::check_ecommerce_hook( $action_hook ) ) {

			if ( self::check_ecommerce_order_hook( $action_hook ) ) {

				$properties = self::get_order_properties_from_order_id( $args["args"][0] );

				return array_filter( $properties );

			}

			if ( $action_hook == 'woocommerce_before_single_product' ) {
				$product_id = get_the_ID();
				$properties = self::get_product_details_from_product_id( $product_id );

				return array_filter( $properties );
			}

			if ( $action_hook === 'woocommerce_add_to_cart' ) {
				//$args['args'][0]=$cart_item_key, $args['args'][1]=$product_id, $args['args'][2]=$quantity, $args['args'][3]=$variation_id,$args['args'][4]=$variation, $args['args'][5]=$cart_item_data
				$product_id        = $args['args'][1];
				$properties        = self::get_product_details_from_product_id( $product_id );
				$properties['url'] = get_permalink( $product_id );
				if ( ! isset( $properties['image_url'] ) ) {
					$properties['image_url'] = wp_get_attachment_image_src( get_post_thumbnail_id( $product_id ), 'single-post-thumbnail' );
				}

				return array_filter( $properties );
			}

			if ( $action_hook === 'woocommerce_ajax_added_to_cart' ) {
				//$args0=$product_id
				$product_id        = $args['args'][0];
				$properties        = self::get_product_details_from_product_id( $product_id );
				$properties['url'] = get_permalink( $product_id );
				if ( ! isset( $properties['image_url'] ) ) {
					$properties['image_url'] = wp_get_attachment_image_src( get_post_thumbnail_id( $product_id ), 'single-post-thumbnail' );
				}

				return array_filter( $properties );
			}

			if ( $action_hook === 'woocommerce_cart_item_restored' ) {
				$removed_cart_item_key      = $args["args"][0];
				$properties['product_id']   = $args['args'][1]["cart_contents"][ $removed_cart_item_key ]["product_id"];
				$properties['quantity']     = $args['args'][1]["cart_contents"][ $removed_cart_item_key ]['quantity'];
				$properties['variation_id'] = $args['args'][1]["cart_contents"][ $removed_cart_item_key ]['variation_id'];

				return array_filter( $properties );
			}

			if ( $action_hook === 'woocommerce_remove_cart_item' ) {

				$removed_cart_item_key      = $args["args"][0];
				$properties['product_id']   = $args["args"][1]["cart_contents"][ $removed_cart_item_key ]["product_id"];
				$properties['quantity']     = $args["args"][1]["cart_contents"][ $removed_cart_item_key ]['quantity'];
				$properties['variation_id'] = $args["args"][1]["cart_contents"][ $removed_cart_item_key ]["variation_id"];

				return array_filter( $properties );


			}

			if ( $action_hook === 'woocommerce_before_cart' ) {

				//na right now

			}

			if ( $action_hook === 'woocommerce_before_checkout_form' ) {

				//na right now

			}

			if ( $action_hook === 'woocommerce_applied_coupon' ) {
				$coupon                      = $args["args"][0];
				$coupon_data                 = new WC_Coupon( $coupon );
				$properties["coupon_id"]     = $coupon_data->get_id();
				$properties["coupon_name"]   = $coupon_data->get_code();
				$properties["coupon_type"]   = $coupon_data->get_discount_type();
				$properties["coupon_amount"] = wc_format_decimal( $coupon_data->get_amount(), 2 );
				$properties["discount"]      = wc_format_decimal( $coupon_data->get_amount(), 2 );

				return array_filter( $properties );

			}

		}

		//LEARNDASH EVENTS
		// TODO these can be triggered by admin in backend so double check all ok
		if ( $action_hook === "learndash_update_course_access" && ! current_user_can( 'edit_others_pages' ) ) {

			$course_post                 = get_post( $args["args"][1] );
			$properties["course_id"]     = $args["args"][1];
			$properties["course_title"]  = $course_post->post_title;
			$properties["course_name"]   = $course_post->post_name;
			$properties["course_author"] = $course_post->post_author;
			$properties["course_date"]   = $course_post->post_date;
			$properties["course_url"]    = $course_post->guid;

			return array_filter( $properties );
		}

		if ( $action_hook === "ld_group_postdata_updated" && ! current_user_can( 'edit_others_pages' ) ) {
			//args	$group_id, $group_leaders, $group_users, $group_courses

			foreach ( $args["args"][3] as $course_id ) {
				foreach ( $args["args"][2] as $user_id ) {
					$user = get_user_by( 'id', $user_id );
					if ( empty( $user->ID ) ) {
						continue;
					}

					$course = get_post( $course_id );
					if ( empty( $course->ID ) ) {
						continue;
					}

					$data = array(
						'user'   => $user->data,
						'course' => $course,
					);

					$course_post                 = get_post( $args["args"][1] );
					$properties["course_id"]     = $args["args"][1];
					$properties["course_title"]  = $course_post->post_title;
					$properties["course_name"]   = $course_post->post_name;
					$properties["course_author"] = $course_post->post_author;
					$properties["course_date"]   = $course_post->post_date;
					$properties["course_url"]    = $course_post->guid;

					return array_filter( $properties );
				}
			}
		}

		if ( $action_hook === "learndash_topic_completed" ) {
			$properties["topic_id"]               = $args["args"][0]["topic"]["ID"];
			$properties["topic_title"]            = $args["args"][0]["topic"]["post_title"];
			$properties["topic_slug"]             = $args["args"][0]["topic"]["post_name"];
			$properties["topic_author"]           = $args["args"][0]["topic"]["post_author"];
			$properties["topic_date_published"]   = $args["args"][0]["topic"]["post_date"];
			$properties["parent_lesson"]          = Array();
			$properties["parent_lesson"]["ID"]    = $args["args"][0]["lesson"]["ID"];
			$properties["parent_lesson"]["title"] = $args["args"][0]["lesson"]["post_title"];
			$properties["parent_lesson"]["slug"]  = $args["args"][0]["lesson"]["post_name"];
			$properties["parent_course"]          = Array();
			$properties["parent_course"]["ID"]    = $args["args"][0]["course"]["ID"];
			$properties["parent_course"]["title"] = $args["args"][0]["course"]["post_title"];
			$properties["parent_course"]["slug"]  = $args["args"][0]["course"]["post_name"];

			return array_filter( $properties );
		}

		if ( $action_hook === "learndash_lesson_completed" ) {

			$properties["lesson_id"]              = $args["args"][0]["lesson"]["ID"];
			$properties["lesson_title"]           = $args["args"][0]["lesson"]["post_title"];
			$properties["lesson_name"]            = $args["args"][0]["lesson"]["post_name"];
			$properties["lesson_author"]          = $args["args"][0]["lesson"]["post_author"];
			$properties["lesson_url"]             = $args["args"][0]["lesson"]["guid"];
			$properties["lesson_date_published"]  = $args["args"][0]["lesson"]["post_date"];
			$properties["parent_course"]["ID"]    = $args["args"][0]["course"]["ID"];
			$properties["parent_course"]["title"] = $args["args"][0]["course"]["post_title"];
			$properties["parent_course"]["name"]  = $args["args"][0]["course"]["post_name"];

			return array_filter( $properties );
		}

		if ( $action_hook === "learndash_course_completed" ) {

			$properties["course_id"]             = $args["args"][0]["course"]["ID"];
			$properties["course_title"]          = $args["args"][0]["course"]["post_title"];
			$properties["course_name"]           = $args["args"][0]["course"]["post_name"];
			$properties["course_author"]         = $args["args"][0]["course"]["post_author"];
			$properties["course_url"]            = $args["args"][0]["course"]["guid"];
			$properties["course_date_published"] = $args["args"][0]["course"]["post_date"];

			return array_filter( $properties );
		}

		if ( $action_hook === "learndash_quiz_completed" ) {

			$properties["quiz_id"]             = $args["args"][0]["quiz"]["ID"];
			$properties["quiz_title"]          = $args["args"][0]["quiz"]["post_title"];
			$properties["quiz_name"]           = $args["args"][0]["quiz"]["post_name"];
			$properties["quiz_author"]         = $args["args"][0]["quiz"]["post_author"];
			$properties["quiz_url"]            = $args["args"][0]["quiz"]["guid"];
			$properties["quiz_date_published"] = $args["args"][0]["quiz"]["post_date"];
			$properties["quiz_score"]          = $args["args"][0]["score"];
			$properties["quiz_count"]          = $args["args"][0]["count"];
			$properties["quiz_pass"]           = $args["args"][0]["pass"];
			$properties["quiz_time"]           = $args["args"][0]["time"];
			$properties["quiz_points"]         = $args["args"][0]["total_points"];
			$properties["quiz_percentage"]     = $args["args"][0]["percentage"];
			$properties["quiz_time_spent"]     = $args["args"][0]["timespent"];
			$properties["quiz_started"]        = $args["args"][0]["started"];
			$properties["quiz_completed"]      = $args["args"][0]["completed"];

			$properties["parent_lesson"]          = Array();
			$properties["parent_lesson"]["ID"]    = $args["args"][0]["lesson"]["ID"];
			$properties["parent_lesson"]["title"] = $args["args"][0]["lesson"]["post_title"];
			$properties["parent_lesson"]["slug"]  = $args["args"][0]["lesson"]["post_name"];
			$properties["parent_course"]          = Array();
			$properties["parent_course"]["ID"]    = $args["args"][0]["course"]["ID"];
			$properties["parent_course"]["title"] = $args["args"][0]["course"]["post_title"];
			$properties["parent_course"]["slug"]  = $args["args"][0]["course"]["post_name"];

			return array_filter( $properties );
		}

		//TODO QUIZZES
		//TODO ASSIGNMENTS
	}

	/**
	 * Current traits
	 *
	 * @param $current_user
	 *
	 * @return array
	 */
	public static function get_user_traits( $current_user ) {
		$settings = get_exopite_sof_option( 'all-in-one-analytics' );

		if ( is_object( $current_user ) && isset ( $current_user->ID ) ) {
			$user_id          = (string) $current_user->ID;
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
				$user_id = (string) $properties['userId'];
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
	 * Creates the array of track calls on every page
	 * Also sends the async action to clean db entries used in event tracking
	 *
	 * @returns array
	 *
	 */
	public static function get_current_tracks() {

		if ( isset( $_SERVER["HTTP_X_REQUESTED_WITH"] ) ) { //Only render these for actual browsers
			$hello = "stop";

			return;
		}


		$settings = get_exopite_sof_option( 'all-in-one-analytics' );
		$track    = array();
		$i        = 0; // an index to form an array of track calls

		// POSTS
		if ( $settings["core_event_settings"]['track_posts_fieldset']['track_posts'] == "yes" ) {
			// A post or a custom post. `is_single` also returns attachments, so
			// we filter those out. The event name is based on the post's type, and is uppercased.
			if ( is_single() && ! is_attachment() ) {
				$track[ $i ]          = array();
				$track[ $i ]['event'] = sprintf( __( 'Viewed %s', 'all_in_one_analytics' ), ucfirst( get_post_type() ) );
				if ( $settings["core_event_settings"]['track_posts_fieldset']['track_posts_custom_event_label'] !== "" ) {
					$track[ $i ]['event'] = $settings["core_event_settings"]['track_posts_fieldset']['track_posts_custom_event_label'];
				}
				$track[ $i ]['skip-cookie'] = true;

				$i ++;

			}
		}

		// PAGES
		if ( $settings["core_event_settings"]['track_pages_fieldset']['track_pages'] == "yes" ) {
			// The front page of their site, whether it's a page or a list of
			// recent blog entries. `is_home` only works if it's not a page,
			if ( is_front_page() ) {
				$track[ $i ] = array(
					'event' => 'Viewed Home Page'
				);
			} // A normal WordPress page.
			else if ( is_page() ) {
				$track[ $i ] = array(
					'event' => sprintf( __( 'Viewed %s Page', 'all-in-one-analytics' ), single_post_title( '', false ) ),
				);
			} // The wordpress login page
			else if ( did_action( 'login_init' ) ) {
				if ( $settings["core_event_settings"]['track_login_page_fieldset']['track_login_page'] === "yes" ) {
					$track[ $i ] = array( 'event' => 'Viewed Login Page' );
					if ( $settings["core_event_settings"]['track_login_page_fieldset']['track_login_page_custom_event_label'] !== "" ) {
						$track[ $i ]['event'] = $settings["core_event_settings"]['track_login_page_fieldset']['track_login_page_custom_event_label'];
					}
				}
			}
			$track[ $i ]['skip-cookie'] = true;
			$i ++;
		}

		// ARCHIVES
		if ( $settings["core_event_settings"]['track_archives_fieldset']['track_archives'] == "yes" ) {
			// An author archive page.
			if ( is_author() ) {
				$author                     = get_queried_object();
				$track[ $i ]                = array(
					'event'      => 'Viewed Author Page',
					'properties' => array(
						'author' => $author->display_name
					)
				);
				$track[ $i ]['skip-cookie'] = true;
				$i ++;
			} // A tag archive page. Use `single_tag_title` to get the name.
			else if ( is_tag() ) {
				$track[ $i ]                = array(
					'event'      => 'Viewed Tag Page',
					'properties' => array(
						'	tag' => single_tag_title( '', false )
					)
				);
				$track[ $i ]['skip-cookie'] = true;
				$i ++;
			} // A category archive page. Use `single_cat_title` to get the name.
			else if ( is_category() ) {
				$track[ $i ]                = array(
					'event'      => 'Viewed Category Page',
					'properties' => array(
						'category' => single_cat_title( '', false )
					)

				);
				$track[ $i ]['skip-cookie'] = true;
				$i ++;
			}

		}

		// SIGNUPS
		if ( $settings["core_event_settings"]['track_signups_fieldset']['track_signups'] == "yes" ) {
			if ( All_In_One_Analytics_Cookie::get_cookie( 'signed_up' ) ) {

				$action      = 'user_register';
				$event_name  = self::get_event_name( $action );
				$properties  = All_In_One_Analytics_Cookie::get_cookie( 'signed_up' );
				$properties  = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'd' );
				$properties  = json_decode( $properties );
				$properties  = json_decode( $properties, true );
				$properties  = All_In_One_Analytics::array_flatten( $properties );
				$user_id     = All_In_One_Analytics::get_user_id( $action, $properties );
				$track[ $i ] = array(
					'userId'     => $user_id,
					'event'      => $event_name,
					'properties' => $properties,
					'http_event' => 'signed_up'
				);

				$i ++;
			}
		}

		// LOGINS
		if ( $settings["core_event_settings"]['track_logins_fieldset']['track_logins'] == "yes" ) {
			if ( All_In_One_Analytics_Cookie::match_cookie( 'logged_in' ) ) {
				$action     = 'wp_login';
				$http_event = 'logged_in';
				$event_name = self::get_event_name( $action );
				$cookies    = All_In_One_Analytics_Cookie::get_every_cookie( $http_event );
				foreach ( $cookies as $cookie => $data ) {
					$properties = self::get_data_from_data_id( $data );
					$properties = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'd' );
					$properties = json_decode( $properties, true );
					$properties = self::object_to_array( $properties );
					$user_id    = self::get_user_id( $action, $properties );

					$track[ $i ] = array(
						'userId'     => $user_id,
						'event'      => $event_name,
						'properties' => $properties,
						'http_event' => $http_event
					);

					$i ++;

					//this adds item to an async queue to remove db entry
					do_action( 'add_to_queue', $data );

				}
			}
		}

		// COMMENTS
		if ( $settings["core_event_settings"]['track_comments_fieldset']['track_comments'] == "yes" ) {
			if ( All_In_One_Analytics_Cookie::match_cookie( 'made_comment' ) ) {
				$action     = 'wp_insert_comment';
				$http_event = 'made_comment';
				$event_name = self::get_event_name( $action );
				$cookies    = All_In_One_Analytics_Cookie::get_every_cookie( $http_event );
				foreach ( $cookies as $cookie => $data ) {
					$properties = self::get_data_from_data_id( $data );
					$properties = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'd' );
					$properties = json_decode( $properties, true );
					$properties = self::object_to_array( $properties );
					$user_id    = self::get_user_id( $action, $properties );

					$track[ $i ] = array(
						'userId'     => $user_id,
						'event'      => $event_name,
						'properties' => $properties,
						'http_event' => $http_event
					);

					$i ++;

					//this adds item to an async queue to remove db entry
					do_action( 'add_to_queue', $data );

				}

			}

		}

		// NINJA FORMS
		if ( $settings["form_event_settings"]['track_ninja_forms_fieldset']['track_ninja_forms'] == "yes" ) {
			if ( All_In_One_Analytics_Cookie::match_cookie( 'completed_form_nf' ) ) {
				$action     = 'ninja_forms_after_submission';
				$http_event = 'completed_form_nf';
				$event_name = self::get_event_name( $action );
				$cookies    = All_In_One_Analytics_Cookie::get_every_cookie( $http_event );
				foreach ( $cookies as $cookie => $data ) {
					$properties = self::get_data_from_data_id( $data );
					$properties = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'd' );
					$properties = json_decode( $properties, true );
					$properties = self::object_to_array( $properties );
					$user_id    = self::get_user_id( $action, $properties );

					$track[ $i ] = array(
						'userId'     => $user_id,
						'event'      => $event_name,
						'properties' => $properties,
						'http_event' => $http_event
					);

					$i ++;

					//this adds item to an async queue to remove db entry
					do_action( 'add_to_queue', $data );
				}
			}
		}

		// GRAVITY FORMS
		if ( $settings["form_event_settings"]['track_gravity_forms_fieldset']['track_gravity_forms'] == "yes" ) {
			if ( All_In_One_Analytics_Cookie::match_cookie( 'completed_form_gf' ) ) {
				$action     = 'gform_after_submission';
				$http_event = 'completed_form_gf';
				$event_name = self::get_event_name( $action );
				$cookies    = All_In_One_Analytics_Cookie::get_every_cookie( $http_event );
				foreach ( $cookies as $cookie => $data ) {
					$properties  = self::get_data_from_data_id( $data );
					$properties  = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'd' );
					$properties  = json_decode( $properties, true );
					$properties  = self::object_to_array( $properties );
					$user_id     = self::get_user_id( $action, $properties );
					$track[ $i ] = array(
						'userId'     => $user_id,
						'event'      => $event_name,
						'properties' => $properties,
						'http_event' => $http_event
					);

					$i ++;

					//this adds item to an async queue to remove db entry
					do_action( 'add_to_queue', $data );

				}
			}
		}

		//WOOCOMMERCE
		if ( $settings["woocommerce_event_settings"]['track_woocommerce_fieldset']['track_woocommerce'] === 'yes' ) {

			if ( class_exists( 'woocommerce' ) ) {


				if ( is_product() ) {
					$action                     = 'is_product';
					$event_name                 = self::get_event_name( $action );
					$properties                 = All_In_One_Analytics::get_product_details_from_product_id( get_the_ID() );
					$track[ $i ]                = array(
						'event'      => $event_name,
						'properties' => $properties,
					);
					$track[ $i ]['skip-cookie'] = true;
					$i ++;

				}


				if ( $settings["woocommerce_event_settings"]["track_woocommerce_fieldset"]["woocommerce_events"]["track_product_clicks_fieldset"] == 'yes'
				     && is_product() && All_In_One_Analytics_Cookie::match_cookie( 'product_clicked' ) ) {
					$action                     = 'product_clicked';
					$event_name                 = self::get_event_name( $action );
					$properties                 = All_In_One_Analytics::get_product_details_from_product_id( get_the_ID() );
					$track[ $i ]                = array(
						'event'      => $event_name,
						'properties' => $properties,
					);
					$track[ $i ]['skip-cookie'] = true;
					$i ++;

				}

				// PRODUCT ADDED
				if ( All_In_One_Analytics_Cookie::match_cookie( 'product_added_normal' ) && $settings["woocommerce_event_settings"]["track_woocommerce_fieldset"]["woocommerce_events"]["track_add_to_cart_fieldset"]["track_add_to_cart"] == 'yes' ) {
					$action     = 'woocommerce_add_to_cart';
					$http_event = 'product_added_normal';
					$event_name = self::get_event_name( $action );
					$cookies    = All_In_One_Analytics_Cookie::get_every_cookie( $http_event );
					foreach ( $cookies as $cookie => $data ) {
						$properties = self::get_data_from_data_id( $data );
						$properties = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'd' );
						$properties = json_decode( $properties, true );
						$properties = self::object_to_array( $properties );
						$user_id    = self::get_user_id( $action, $properties );

						$track[ $i ] = array(
							'userId'     => $user_id,
							'event'      => $event_name,
							'properties' => $properties,
							'http_event' => $http_event
						);

						$i ++;

						//this adds item to an async queue to remove db entry
						do_action( 'add_to_queue', $data );
					}
				}

				if ( All_In_One_Analytics_Cookie::match_cookie( 'product_added_ajax' ) && $settings["woocommerce_event_settings"]["track_woocommerce_fieldset"]["woocommerce_events"]["track_add_to_cart_fieldset"]["track_add_to_cart"] == 'yes' ) {

					$action     = 'woocommerce_ajax_added_to_cart';
					$http_event = 'product_added_ajax';
					$event_name = self::get_event_name( $action );
					$cookies    = All_In_One_Analytics_Cookie::get_every_cookie( $http_event );
					foreach ( $cookies as $cookie => $data ) {
						$properties = self::get_data_from_data_id( $data );
						$properties = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'd' );
						$properties = json_decode( $properties, true );
						$properties = self::object_to_array( $properties );
						$user_id    = self::get_user_id( $action, $properties );

						$track[ $i ] = array(
							'userId'     => $user_id,
							'event'      => $event_name,
							'properties' => $properties,
							'http_event' => $http_event
						);

						$i ++;

						//this adds item to an async queue to remove db entry
						do_action( 'add_to_queue', $data );
					}
				}

				// PRODUCT REMOVED
				if ( All_In_One_Analytics_Cookie::match_cookie( 'product_removed' ) && $settings["woocommerce_event_settings"]["track_woocommerce_fieldset"]["woocommerce_events"]["track_remove_from_cart_fieldset"]["track_remove_from_cart"] == 'yes' ) {
					$action     = 'woocommerce_remove_cart_item';
					$event_name = self::get_event_name( $action );
					$cookies    = All_In_One_Analytics_Cookie::get_every_cookie( 'product_removed' );
					$http_event = 'product_removed';
					foreach ( $cookies as $cookie => $data ) {
						$properties  = self::get_data_from_data_id( $data );
						$properties  = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'd' );
						$properties  = json_decode( $properties, true );
						$properties  = self::object_to_array( $properties );
						$user_id     = self::get_user_id( $action, $properties );
						$track[ $i ] = array(
							'userId'     => $user_id,
							'event'      => $event_name,
							'properties' => $properties,
							'http_event' => $http_event
						);
						$i ++;

						do_action( 'add_to_queue', $data ); //this adds them to an async queue to remove db entry

					}


				}

				// PRODUCT RESTORED = 'woocommerce_cart_item_restored
				if ( All_In_One_Analytics_Cookie::match_cookie( 'product_readded' && $settings["woocommerce_event_settings"]["track_woocommerce_fieldset"]["woocommerce_events"]["track_add_to_cart_fieldset"]["track_add_to_cart"] == 'yes' ) ) {
					$action     = 'woocommerce_cart_item_restored';
					$event_name = self::get_event_name( $action );
					$cookies    = All_In_One_Analytics_Cookie::get_every_cookie( 'product_readded' );
					$http_event = 'product_readded';
					foreach ( $cookies as $cookie => $data ) {
						$properties  = self::get_data_from_data_id( $data );
						$properties  = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'd' );
						$properties  = json_decode( $properties, true );
						$properties  = self::object_to_array( $properties );
						$user_id     = self::get_user_id( $action, $properties );
						$track[ $i ] = array(
							'userId'     => $user_id,
							'event'      => $event_name,
							'properties' => $properties,
							'http_event' => $http_event
						);
						$i ++;
						do_action( 'add_to_queue', $data ); //this adds them to an async queue to remove db entry
					}
				}

				// VIEWED CART
				if ( is_cart() && $settings["woocommerce_event_settings"]["track_woocommerce_fieldset"]["woocommerce_events"]["track_cart_viewed_fieldset"]["track_cart_viewed"] == 'yes' ) {
					$action                     = 'is_cart';
					$event_name                 = self::get_event_name( $action );
					$track[ $i ]                = array(
						'event'      => $event_name,
						'properties' => Array(),
					);
					$track[ $i ]['skip-cookie'] = true;
					$i ++;
				}

				// VIEWED CHECKOUT STEP
				if ( is_checkout() && $settings["woocommerce_event_settings"]["track_woocommerce_fieldset"]["woocommerce_events"]["track_checkout_step_viewed_fieldset"] == 'yes' ) {
					$action                     = 'is_checkout';
					$event_name                 = self::get_event_name( $action );
					$track[ $i ]                = array(
						'event'      => $event_name,
						'properties' => Array(),
					);
					$track[ $i ]['skip-cookie'] = true;
					$i ++;
				}

				if ( All_In_One_Analytics_Cookie::match_cookie( 'checkout_started' ) && $settings["woocommerce_event_settings"]["track_woocommerce_fieldset"]["woocommerce_events"]["track_initiated_checkout_fieldset"] == 'yes' ) {
					$action      = 'woocommerce_checkout_process';
					$http_event  = 'checkout_started';
					$event_name  = self::get_event_name( $action );
					$track[ $i ] = array(
						'event'      => $event_name,
						'properties' => Array(),
						'http_event' => $http_event
					);
					$i ++;

				}

				// ORDER PENDING
				if ( All_In_One_Analytics_Cookie::match_cookie( 'order_pending' ) && $settings["woocommerce_event_settings"]["track_woocommerce_fieldset"]["woocommerce_events"]["track_order_pending_fieldset"]["track_order_pending"] = 'yes' ) {
					$action     = 'woocommerce_order_status_pending';
					$http_event = 'order_pending';
					$event_name = self::get_event_name( $action );
					$cookies    = All_In_One_Analytics_Cookie::get_every_cookie( $http_event );
					foreach ( $cookies as $cookie => $data ) {
						$properties = self::get_data_from_data_id( $data );
						$properties = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'd' );
						$properties = json_decode( $properties, true );
						$properties = self::object_to_array( $properties );
						$user_id    = self::get_user_id( $action, $properties );

						$track[ $i ] = array(
							'userId'     => $user_id,
							'event'      => $event_name,
							'properties' => $properties,
							'http_event' => $http_event
						);

						$i ++;

						do_action( 'add_to_queue', $data ); //this adds them to an async queue to remove db entry

					}
				}

				// ORDER PROCESSING
				if ( All_In_One_Analytics_Cookie::match_cookie( 'order_processing' ) && $settings["woocommerce_event_settings"]["track_woocommerce_fieldset"]["woocommerce_events"]["track_order_processing_fieldset"]["track_order_processing"] == 'yes' ) {
					$action     = 'woocommerce_order_status_processing';
					$http_event = 'order_processing';
					$event_name = self::get_event_name( $action );
					$cookies    = All_In_One_Analytics_Cookie::get_every_cookie( $http_event );
					foreach ( $cookies as $cookie => $data ) {
						$properties = self::get_data_from_data_id( $data );
						$properties = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'd' );
						$properties = json_decode( $properties, true );
						$properties = self::object_to_array( $properties );
						$user_id    = self::get_user_id( $action, $properties );

						$track[ $i ] = array(
							'userId'     => $user_id,
							'event'      => $event_name,
							'properties' => $properties,
							'http_event' => $http_event
						);

						$i ++;

						do_action( 'add_to_queue', $data ); //this adds them to an async queue to remove db entry

					}
				}

				// ORDER COMPLETED
				if ( All_In_One_Analytics_Cookie::match_cookie( 'order_completed' ) && $settings["woocommerce_event_settings"]["track_woocommerce_fieldset"]["woocommerce_events"]["track_order_completed_fieldset"]["track_order_completed"] == 'yes' ) {
					$action     = 'woocommerce_order_status_completed';
					$http_event = 'order_completed';
					$event_name = self::get_event_name( $action );
					$cookies    = All_In_One_Analytics_Cookie::get_every_cookie( $http_event );
					foreach ( $cookies as $cookie => $data ) {
						$properties = self::get_data_from_data_id( $data );
						$properties = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'd' );
						$properties = json_decode( $properties, true );
						$properties = self::object_to_array( $properties );
						$user_id    = self::get_user_id( $action, $properties );

						$track[ $i ] = array(
							'userId'     => $user_id,
							'event'      => $event_name,
							'properties' => $properties,
							'http_event' => $http_event
						);

						$i ++;

						do_action( 'add_to_queue', $data ); //this adds them to an async queue to remove db entry

					}

				}

				// ORDER PAID
				if ( All_In_One_Analytics_Cookie::match_cookie( 'order_paid' ) && $settings["woocommerce_event_settings"]["track_woocommerce_fieldset"]["woocommerce_events"]["track_order_paid_fieldset"]["track_order_paid"] ) {
					$action     = 'woocommerce_payment_complete';
					$http_event = 'order_paid';
					$event_name = self::get_event_name( $action );
					$cookies    = All_In_One_Analytics_Cookie::get_every_cookie( $http_event );
					foreach ( $cookies as $cookie => $data ) {
						$properties = self::get_data_from_data_id( $data );
						$properties = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'd' );
						$properties = json_decode( $properties, true );
						$properties = self::object_to_array( $properties );
						$user_id    = self::get_user_id( $action, $properties );

						$track[ $i ] = array(
							'userId'     => $user_id,
							'event'      => $event_name,
							'properties' => $properties,
							'http_event' => $http_event
						);

						$i ++;

						do_action( 'add_to_queue', $data ); //this adds them to an async queue to remove db entry

					}

				}

				// ORDER CANCELLED
				if ( All_In_One_Analytics_Cookie::match_cookie( 'order_cancelled' ) && $settings["woocommerce_event_settings"]["track_woocommerce_fieldset"]["woocommerce_events"]["track_order_cancelled_fieldset"]["track_order_cancelled"] == 'yes' ) {
					$action     = 'woocommerce_order_status_cancelled';
					$event_name = self::get_event_name( $action );
					$cookies    = All_In_One_Analytics_Cookie::get_every_cookie( 'order_cancelled' );
					$http_event = 'order_cancelled';
					foreach ( $cookies as $cookie => $data ) {
						$properties = self::get_data_from_data_id( $data );
						$properties = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'd' );
						$properties = json_decode( $properties, true );
						$properties = self::object_to_array( $properties );
						$user_id    = self::get_user_id( $action, $properties );

						$track[ $i ] = array(
							'userId'     => $user_id,
							'event'      => $event_name,
							'properties' => $properties,
							'http_event' => $http_event
						);

						$i ++;

						do_action( 'add_to_queue', $data ); //this adds them to an async queue to remove db entry

					}

				}

				//COUPON ADDED
				if ( All_In_One_Analytics_Cookie::match_cookie( 'coupon_added' ) && $settings["woocommerce_event_settings"]["track_woocommerce_fieldset"]["woocommerce_events"]["track_coupons_fieldset"]["track_coupons"] == 'yes' ) {
					$action     = 'woocommerce_applied_coupon';
					$http_event = 'coupon_added';
					$event_name = self::get_event_name( $action );
					$cookies    = All_In_One_Analytics_Cookie::get_every_cookie( $http_event );
					foreach ( $cookies as $cookie => $properties ) {
						$properties = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'd' );
						$properties = json_decode( $properties, true );
						$properties = self::object_to_array( $properties );
						$user_id    = self::get_user_id( $action, $properties );

						$track[ $i ] = array(
							'userId'     => $user_id,
							'event'      => $event_name,
							'properties' => $properties,
							'http_event' => $http_event
						);

						$i ++;

						//	do_action( 'add_to_queue', $data ); //this adds them to an async queue to remove db entry

					}
				}

			}
		}

		//LEARNDASH
		if ( $settings["learndash_event_settings"]["track_learndash_fieldset"]["track_learndash"] === 'yes' ) {

			//ENROLLMENTS
			if ( All_In_One_Analytics_Cookie::match_cookie( 'enrolled_in_course' ) ) {
				$action     = 'learndash_update_course_access';
				$http_event = 'enrolled_in_course';
				$event_name = self::get_event_name( $action );
				$cookies    = All_In_One_Analytics_Cookie::get_every_cookie( $http_event );
				foreach ( $cookies as $cookie => $data ) {
					$properties = self::get_data_from_data_id( $data );
					$properties = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'd' );
					$properties = json_decode( $properties, true );
					$properties = self::object_to_array( $properties );
					$user_id    = self::get_user_id( $action, $properties );

					$track[ $i ] = array(
						'userId'     => $user_id,
						'event'      => $event_name,
						'properties' => $properties,
						'http_event' => $http_event
					);

					$i ++;

					do_action( 'add_to_queue', $data ); //this adds them to an async queue to remove db entry

				}

			}

			if ( All_In_One_Analytics_Cookie::match_cookie( 'enrolled_in_course_via_group' ) ) {
				$action     = 'ld_group_postdata_updated';
				$http_event = 'enrolled_in_course_via_group';
				$event_name = self::get_event_name( $action );
				$cookies    = All_In_One_Analytics_Cookie::get_every_cookie( $http_event );
				foreach ( $cookies as $cookie => $data ) {
					$properties = self::get_data_from_data_id( $data );
					$properties = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'd' );
					$properties = json_decode( $properties, true );
					$properties = self::object_to_array( $properties );
					$user_id    = self::get_user_id( $action, $properties );

					$track[ $i ] = array(
						'userId'     => $user_id,
						'event'      => $event_name,
						'properties' => $properties,
						'http_event' => $http_event
					);

					$i ++;

					do_action( 'add_to_queue', $data ); //this adds them to an async queue to remove db entry

				}

			}

			//TOPICS
			if ( All_In_One_Analytics_Cookie::match_cookie( 'topic_completed' ) ) {
				$action     = 'learndash_topic_completed';
				$http_event = 'topic_completed';
				$event_name = self::get_event_name( $action );
				$cookies    = All_In_One_Analytics_Cookie::get_every_cookie( $http_event );
				foreach ( $cookies as $cookie => $data ) {
					$properties = self::get_data_from_data_id( $data );
					$properties = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'd' );
					$properties = json_decode( $properties, true );
					$properties = self::object_to_array( $properties );
					$user_id    = self::get_user_id( $action, $properties );

					$track[ $i ] = array(
						'userId'     => $user_id,
						'event'      => $event_name,
						'properties' => $properties,
						'http_event' => $http_event
					);

					$i ++;

					do_action( 'add_to_queue', $data ); //this adds them to an async queue to remove db entry

				}

			}

			//LESSONS
			if ( All_In_One_Analytics_Cookie::match_cookie( 'lesson_completed' ) ) {
				$action     = 'learndash_lesson_completed';
				$http_event = 'lesson_completed';
				$event_name = self::get_event_name( $action );
				$cookies    = All_In_One_Analytics_Cookie::get_every_cookie( $http_event );
				foreach ( $cookies as $cookie => $data ) {
					$properties = self::get_data_from_data_id( $data );
					$properties = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'd' );
					$properties = json_decode( $properties, true );
					$properties = self::object_to_array( $properties );
					$user_id    = self::get_user_id( $action, $properties );

					$track[ $i ] = array(
						'userId'     => $user_id,
						'event'      => $event_name,
						'properties' => $properties,
						'http_event' => $http_event
					);

					$i ++;

					do_action( 'add_to_queue', $data ); //this adds them to an async queue to remove db entry

				}

			}

			//COURSES
			if ( All_In_One_Analytics_Cookie::match_cookie( 'course_completed' ) ) {
				$action     = 'learndash_course_completed';
				$http_event = 'course_completed';
				$event_name = self::get_event_name( $action );
				$cookies    = All_In_One_Analytics_Cookie::get_every_cookie( $http_event );
				foreach ( $cookies as $cookie => $data ) {
					$properties = self::get_data_from_data_id( $data );
					$properties = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'd' );
					$properties = json_decode( $properties, true );
					$properties = self::object_to_array( $properties );
					$user_id    = self::get_user_id( $action, $properties );

					$track[ $i ] = array(
						'userId'     => $user_id,
						'event'      => $event_name,
						'properties' => $properties,
						'http_event' => $http_event
					);

					$i ++;

					do_action( 'add_to_queue', $data ); //this adds them to an async queue to remove db entry

				}
			}

			//QUIZZES
			if ( All_In_One_Analytics_Cookie::match_cookie( 'quiz_completed' ) ) {
				$action     = 'learndash_quiz_completed';
				$http_event = 'quiz_completed';
				$event_name = self::get_event_name( $action );
				$cookies    = All_In_One_Analytics_Cookie::get_every_cookie( $http_event );
				foreach ( $cookies as $cookie => $data ) {
					$properties = self::get_data_from_data_id( $data );
					$properties = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'd' );
					$properties = json_decode( $properties, true );
					$properties = self::object_to_array( $properties );
					$user_id    = self::get_user_id( $action, $properties );

					$track[ $i ] = array(
						'userId'     => $user_id,
						'event'      => $event_name,
						'properties' => $properties,
						'http_event' => $http_event
					);

					$i ++;

					//	do_action( 'add_to_queue', $data ); //this adds them to an async queue to remove db entry

				}
				if ( All_In_One_Analytics_Cookie::match_cookie( 'quiz_passed' ) ) {
					$http_event = 'quiz_passed';
					$event_name = 'Quiz Passed';
					$cookies    = All_In_One_Analytics_Cookie::get_every_cookie( $http_event );
					foreach ( $cookies as $cookie => $data ) {
						$properties  = self::get_data_from_data_id( $data );
						$properties  = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'd' );
						$properties  = json_decode( $properties, true );
						$properties  = self::object_to_array( $properties );
						$user_id     = self::get_user_id( $action, $properties );
						$track[ $i ] = array(
							'userId'     => $user_id,
							'event'      => $event_name,
							'properties' => $properties,
							'http_event' => $http_event
						);

						$i ++;

						do_action( 'add_to_queue', $data );
					}
				}

				if ( All_In_One_Analytics_Cookie::match_cookie( 'quiz_failed' ) ) {
					$http_event = 'quiz_failed';
					$event_name = 'Quiz Failed';
					$cookies    = All_In_One_Analytics_Cookie::get_every_cookie( $http_event );
					foreach ( $cookies as $cookie => $data ) {
						$properties  = self::get_data_from_data_id( $data );
						$properties  = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'd' );
						$properties  = json_decode( $properties, true );
						$properties  = self::object_to_array( $properties );
						$user_id     = self::get_user_id( $action, $properties );
						$track[ $i ] = array(
							'userId'     => $user_id,
							'event'      => $event_name,
							'properties' => $properties,
							'http_event' => $http_event
						);

						$i ++;

						do_action( 'add_to_queue', $data );
					}

				}


				//this adds them to an async queue to remove db entry


			}

			//ASSIGNMENTS
			if ( All_In_One_Analytics_Cookie::match_cookie( 'assignment_uploaded' ) ) {
				$action     = 'learndash_assigment_uploaded';
				$http_event = 'assignment_uploaded';
				$event_name = self::get_event_name( $action );
				$cookies    = All_In_One_Analytics_Cookie::get_every_cookie( $http_event );
				foreach ( $cookies as $cookie => $data ) {
					$properties = self::get_data_from_data_id( $data );
					$properties = All_In_One_Analytics_Encrypt::encrypt_decrypt( $properties, 'd' );
					$properties = json_decode( $properties, true );
					$properties = self::object_to_array( $properties );
					$user_id    = self::get_user_id( $action, $properties );

					$track[ $i ] = array(
						'userId'     => $user_id,
						'event'      => $event_name,
						'properties' => $properties,
						'http_event' => $http_event
					);

					$i ++;

					do_action( 'add_to_queue', $data ); //this adds them to an async queue to remove db entry

				}

			}
		}

		if ( ! isset( $track ) ) { // We don't have any track calls
			$track = false;
		}

		do_action( 'dispatch_queue' ); //dispatches the queue to clear events from db

		return $track; // Returns an array of track calls

	}

	/**
	 * Check if it's an ecommerce event hooks
	 *
	 * @param $action_hook
	 *
	 * @return bool
	 */
	public static function check_ecommerce_hook( $action_hook ) {

		$ecommerce_hooks = Array(
			'woocommerce_before_single_product',
			'woocommerce_add_to_cart',
			'woocommerce_ajax_added_to_cart',
			'woocommerce_remove_cart_item',
			'woocommerce_cart_item_restored',
			'woocommerce_before_cart',
			'woocommerce_before_checkout_form',
			'woocommerce_order_status_pending',
			'woocommerce_order_status_processing',
			'woocommerce_order_status_completed',
			'woocommerce_payment_complete',
			'woocommerce_order_status_cancelled',
			'woocommerce_applied_coupon',
			'is_checkout',
			'is_cart',
			'woocommerce_checkout_process'
		);

		return in_array( $action_hook, $ecommerce_hooks );

	}

	/**
	 * Check if it's an ecommerce order event hook
	 *
	 * @param $action_hook
	 *
	 * @return bool
	 */
	public static function check_ecommerce_order_hook( $action_hook ) {
		$order_hooks = [
			"woocommerce_order_status_completed",
			"woocommerce_payment_complete",
			"woocommerce_order_status_pending",
			"woocommerce_order_status_failed",
			"woocommerce_order_status_on-hold",
			"woocommerce_order_status_processing",
			"woocommerce_order_status_refunded",
			"woocommerce_order_status_cancelled",
		];

		return in_array( $action_hook, $order_hooks );
	}

	/**
	 * Get user id from WooCommerce order id
	 *
	 * @param $order_id
	 *
	 * @return false|int|void
	 */
	public static function get_user_id_from_order( $order_id ) {


		$order       = new WC_Order ( $order_id );
		$order_email = $order->get_billing_email();
		if ( email_exists( $order_email ) ) {
			$user_id = email_exists( $order_email );

		} elseif ( username_exists( $order_email ) ) {
			$user    = get_user_by( 'email', $order_email );
			$user_id = $user->ID;
		}

		if ( isset( $user_id ) ) {

			return $user_id;
		}

		return;
	}

	/**
	 * Get product ids in an order from order id
	 *
	 * @param $order_id
	 *
	 * @return array
	 */
	public static function get_product_ids_from_order_id( $order_id ) {
		$order    = new WC_Order( $order_id );
		$products = $order->get_items();

		$product_ids_array       = array();
		$product_ids_array_index = 0;

		foreach ( $products as $product ) {

			$product_id = wc_get_product( $product['product_id'] );

			$product_ids_array[ $product_ids_array_index ] = $product_id;

		}

		return $product_ids_array;

	}

	/**
	 * Get full product details from order id
	 *
	 * @param $order_id
	 *
	 * @return array
	 */
	public static function get_product_array_from_order_id( $order_id ) {

		$order = new WC_Order ( $order_id );
		foreach ( $order->get_items() as $item_id => $item ) {
			// Get an instance of corresponding the WC_Product object
			$product                = $item->get_product();
			$image_url              = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ), 'single-post-thumbnail' );
			$items_data[ $item_id ] = array(
				'product_id'         => $product->get_id(),
				'sku'                => $product->get_name(),
				'category'           => $product->get_category_ids(),
				'name'               => $product->get_name(),
				'price'              => $product->get_price(),
				'regular_price'      => $product->get_regular_price(),
				'sale_price'         => $product->get_sale_price(),
				'on_sale_from'       => $product->get_date_on_sale_from(),
				'on_sale_to'         => $product->get_date_on_sale_to(),
				//'total_sales'        => $product->get_total_sales(),
				'url'                => get_permalink( $product->get_id() ),
				'image_url'          => $image_url,
				'slug'               => $product->get_slug(),
				'date_created'       => $product->get_date_created(),
				'date_modified'      => $product->get_date_modified(),
				'status'             => $product->get_status(),
				'featured'           => $product->get_featured(),
				'catalog_visibility' => $product->get_catalog_visibility(),
				//'description'        => $product->get_description(),
				//'short_description'  => $product->get_short_description(),
				'position'           => $product->get_menu_order(),
				'tax_status'         => $product->get_tax_status(),
				'tax_class'          => $product->get_tax_class(),
				'manage_stock'       => $product->get_manage_stock(),
				'stock_quantity'     => $product->get_stock_quantity(),
				'stock_status'       => $product->get_stock_status(),
				'backorders'         => $product->get_backorders(),
				'sold_individually'  => $product->get_sold_individually(),
				//'purchase_note'      => $product->get_purchase_note(),
				'shipping_class'     => $product->get_shipping_class_id(),
				'weight'             => $product->get_weight(),
				'length'             => $product->get_length(),
				'width'              => $product->get_width(),
				'height'             => $product->get_height(),
				'dimensions'         => $product->get_dimensions(),
				'upsell_ids'         => $product->get_upsell_ids(),
				'cross_sell_ids'     => $product->get_cross_sell_ids(),
				'parent_id'          => $product->get_parent_id(),
				'variations'         => $product->get_attributes(),
				'default_variation'  => $product->get_default_attributes(),
				//'categories'         => $product->get_categories(),
				//'category_ids'       => $product->get_category_ids(),
				'tag_ids'            => $product->get_tag_ids(),
				'downloads'          => $product->get_downloads(),
				'download_expiry'    => $product->get_download_expiry(),
				'downloadable'       => $product->get_download_expiry(),
				'download_limit'     => $product->get_download_limit(),
				'image_id'           => $product->get_image_id(),
				'image'              => $product->get_image(),
				'gallery_image_ids'  => $product->get_gallery_image_ids(),
				'reviews_allowed'    => $product->get_reviews_allowed(),
				'rating_count'       => $product->get_rating_counts(),
				'average_rating'     => $product->get_average_rating(),
				'review_count'       => $product->get_review_count()
			);

			$items_data[ $item_id ] = array_filter( $items_data[ $item_id ] );
		}

		$items_data = array_values( $items_data );

		$properties = array_merge( $items_data );
		$properties = array_filter( $properties );
		$properties = json_encode( $properties );

		return $properties;
	}

	/**
	 * Returns all of the order details from the order id
	 *
	 * @param $order_id
	 *
	 * @return array
	 */
	public static function get_order_properties_from_order_id( $order_id ) {

		$order    = new WC_Order ( $order_id );
		$total    = (double) $order->get_total();
		$tax      = (double) $order->get_total_tax();
		$shipping = (double) $order->get_shipping_total();
		//TODO explain this to users
		$revenue                      = $total - $shipping - $tax;
		$order_properties             = array(
			'order_id'                    => $order_id,
			//affiliation
			'total'                       => $total,
			'revenue'                     => $revenue,
			'shipping'                    => (double) $order->get_shipping_total(),
			'tax'                         => $tax,
			'discount'                    => $order->get_discount_total(),
			'cart_tax'                    => $order->get_cart_tax(),
			'currency'                    => $order->get_currency(),
			'discount_tax'                => $order->get_discount_tax(),
			'fees'                        => $order->get_fees(),
			'shipping_tax'                => $order->get_shipping_tax(),
			'subtotal'                    => $order->get_subtotal(),
			'tax_totals'                  => $order->get_tax_totals(),
			'taxes'                       => $order->get_taxes(),
			'total_refunded'              => $order->get_total_refunded(),
			'total_tax_refunded'          => $order->get_total_tax_refunded(),
			'total_shipping_refunded'     => $order->get_total_shipping_refunded(),
			'item_count_refunded'         => $order->get_item_count_refunded(),
			'total_quantity_refunded'     => $order->get_total_qty_refunded(),
			'remaining_refund_amount'     => $order->get_remaining_refund_amount(),
			'shipping_method'             => $order->get_shipping_method(),
			'shipping_methods'            => $order->get_shipping_methods(),
			'date_created'                => $order->get_date_created(),
			'date_modified'               => $order->get_date_modified(),
			'date_completed'              => $order->get_date_completed(),
			'date_paid'                   => $order->get_date_paid(),
			'customer_id'                 => $order->get_customer_id(),
			'userId'                      => $order->get_user_id(),
			'ip_address'                  => $order->get_customer_ip_address(),
			'customer_user_agent'         => $order->get_customer_user_agent(),
			'created_via'                 => $order->get_created_via(),
			//'customer_note'               => $order->get_customer_note(),
			'billing_first_name'          => $order->get_billing_first_name(),
			'billing_last_name'           => $order->get_billing_last_name(),
			'billing_company'             => $order->get_billing_company(),
			'billing_address_1'           => $order->get_billing_address_1(),
			'billing_address_2'           => $order->get_billing_address_2(),
			'billing_city'                => $order->get_billing_city(),
			'billing_state'               => $order->get_billing_state(),
			'billing_postcode'            => $order->get_billing_postcode(),
			'billing_country'             => $order->get_billing_country(),
			'billing_email'               => $order->get_billing_email(),
			'billing_phone'               => $order->get_billing_phone(),
			'shipping_first_name'         => $order->get_shipping_first_name(),
			'shipping_last_name'          => $order->get_shipping_last_name(),
			'shipping_company'            => $order->get_shipping_company(),
			'shipping_address_1'          => $order->get_shipping_address_1(),
			'shipping_address_2'          => $order->get_shipping_address_2(),
			'shipping_city'               => $order->get_shipping_city(),
			'shipping_state'              => $order->get_shipping_state(),
			'shipping_postcode'           => $order->get_shipping_postcode(),
			'shipping_country'            => $order->get_shipping_country(),
			'shipping_address'            => $order->get_address(),
			'shipping_address_map_url'    => $order->get_shipping_address_map_url(),
			'payment_method'              => $order->get_payment_method(),
			'payment_method_title'        => $order->get_payment_method_title(),
			'transaction_id'              => $order->get_transaction_id(),
			'checkout_payment_url'        => $order->get_checkout_payment_url(),
			'checkout_order_received_url' => $order->get_checkout_order_received_url(),
			'cancel_order_url'            => $order->get_cancel_order_url(),
			'cancel_order_url_raw'        => $order->get_cancel_order_url_raw(),
			//'cancel_order_endpoint'       => $order->get_cancel_endpoint(),
			'view_order_url'              => $order->get_view_order_url(),
			'edit_order_url'              => $order->get_edit_order_url(),

		);
		$order_properties['products'] = self::get_product_array_from_order_id( $order_id );
		$order_properties             = array_filter( $order_properties );

		return $order_properties;
	}

	/**
	 * Get a product's details in an array
	 *
	 * @param $product_id
	 *
	 * @return array
	 */
	public static function get_product_details_from_product_id( $product_id ) {
		// Make a $product object from product ID
		$product   = wc_get_product( $product_id );
		$image_url = wp_get_attachment_image_src( get_post_thumbnail_id( $product_id ), 'single-post-thumbnail' );
		$product->get_meta_data();
		$properties               = array();
		$properties['product_id'] = $product_id;
		$properties['sku']        = $product->get_sku();
		$product_categories       = $product->get_category_ids();
		if ( is_array( $product_categories ) ) {
			foreach ( $product_categories as $key => $value ) {
				if ( $key == 0 ) {
					$properties['product_category'] = $value;
				} else {
					$properties[ 'product_category_' . ( $key + 1 ) ] = $value;
				}
			}
		}
		$properties['name']          = $product->get_name();
		$properties['price']         = $product->get_price();
		$properties['regular_price'] = $product->get_regular_price();
		$properties['sale_price']    = $product->get_sale_price();
		$properties['on_sale_from']  = $product->get_date_on_sale_from();
		$properties['on_sale_to']    = $product->get_date_on_sale_to();
		//$properties['total_sales']        = $product->get_total_sales(); //switching this on prob not a good idea
		$properties['url']                = get_permalink( $product_id );
		$properties['image_url']          = $image_url[0];
		$properties['slug']               = $product->get_slug();
		$properties['date_created']       = $product->get_date_created();
		$properties['date_modified']      = $product->get_date_modified();
		$properties['status']             = $product->get_status();
		$properties['featured']           = $product->get_featured();
		$properties['catalog_visibility'] = $product->get_catalog_visibility();
		//$properties['description']        = $product->get_description(); can be quite long
		//$properties['short_description']  = $product->get_short_description(); can be quite long
		$properties['position']          = $product->get_menu_order();
		$properties['tax_status']        = $product->get_tax_status();
		$properties['tax_class']         = $product->get_tax_class();
		$properties['manage_stock']      = $product->get_manage_stock();
		$properties['stock_quantity']    = $product->get_stock_quantity();
		$properties['stock_status']      = $product->get_stock_status();
		$properties['backorders']        = $product->get_backorders();
		$properties['sold_individually'] = $product->get_sold_individually();
		//$properties['purchase_note']      = $product->get_purchase_note();
		$properties['shipping_class'] = $product->get_shipping_class_id();
		$properties['weight']         = $product->get_weight();
		$properties['length']         = $product->get_length();
		$properties['width']          = $product->get_width();
		$properties['height']         = $product->get_height();
		$properties['dimensions']     = $product->get_dimensions();
		//	$properties['upsell_ids']        = json_encode($product->get_upsell_ids());
		//	$properties['cross_sell_ids']    = json_encode($product->get_cross_sell_ids());
		$properties['parent_id']         = $product->get_parent_id();
		$properties['variations']        = $product->get_attributes();
		$properties['default_variation'] = $product->get_default_attributes();
		//$properties['categories']         = $product->get_categories(); HTML
		//	$properties['category_ids']    = json_encode($product->get_category_ids()); //Array
		//	$properties['tag_ids']         = json_encode($product->get_tag_ids());
		$properties['downloads']       = $product->get_downloads();
		$properties['download_expiry'] = $product->get_download_expiry();
		$properties['downloadable']    = $product->get_downloadable();
		$properties['download_limit']  = $product->get_download_limit();
		$properties['image_id']        = $product->get_image_id();
		//$properties['image']              = $product->get_image(); HTML
		//	$properties['gallery_image_ids'] = json_encode($product->get_gallery_image_ids());
		$properties['reviews_allowed'] = $product->get_reviews_allowed();
		$properties['rating_count']    = $product->get_rating_counts();
		$properties['average_rating']  = $product->get_average_rating();
		$properties['review_count']    = $product->get_review_count();

		//clean and return
		$properties = array_filter( $properties );

		return $properties;

	}

	/**
	 * Helper function, essentially a replica of stripslashes_deep, but for esc_js.
	 *
	 * @param mixed $value Handles arrays, strings and objects that we are trying to escape for JS.
	 *
	 * @return mixed  $value esc_js()'d value.
	 * @since 1.0.0
	 *
	 */
	public static function esc_js_deep( $value ) {
		if ( is_array( $value ) ) {
			$value = array_map( array( __CLASS__, 'esc_js_deep' ), $value );
		} elseif ( is_object( $value ) ) {
			$vars = get_object_vars( $value );
			foreach ( $vars as $key => $data ) {
				$value->{$key} = self::esc_js_deep( $data );
			}
		} elseif ( is_string( $value ) ) {
			$value = esc_js( $value );
		}

		return $value;
	}

	/**
	 * Turns objects to arrays, includes protected values
	 *
	 * @param null $obj
	 *
	 * @return array
	 */
	public static function object_to_array( $obj ) {
		if ( is_object( $obj ) ) {
			$obj = (array) $obj;
		}
		if ( is_array( $obj ) ) {
			$new = array();
			foreach ( $obj as $key => $val ) {
				$new[ $key ] = self::object_to_array( $val );
			}
		} else {
			$new = $obj;
		}

		return $new;
	}

	/**
	 *  Flattens arrays
	 *
	 * @param null $array
	 *
	 * @return array
	 */
	public static function array_flatten( $array ) {
		if ( ! is_array( $array ) ) {
			return;
		}
		$return = array();
		foreach ( $array as $key => $value ) {
			if ( is_array( $value ) ) {
				$return = array_merge( $return, self::array_flatten( $value ) );
			} else {
				$return[ $key ] = $value;
			}
		}

		return $return;
	}

	public static function starts_with( $haystack, $needle ) {
		$length = strlen( $needle );

		return ( substr( $haystack, 0, $length ) === $needle );
	}

	public static function ends_with( $haystack, $needle ) {
		$length = strlen( $needle );
		if ( $length == 0 ) {
			return true;
		}

		return ( substr( $haystack, - $length ) === $needle );
	}

	/**
	 * Prepare and make db query
	 *
	 * @param $data_id
	 *
	 * @return string|null
	 */
	public static function get_data_from_data_id( $data_id ) {
		global $wpdb;

		return $wpdb->get_var( $wpdb->prepare(
			" SELECT data FROM {$wpdb->prefix}all_in_one_analytics WHERE data_id = %d ",
			$data_id
		) );
	}

	/**
	 * Write to db
	 *
	 * @param $data_id string
	 * @param $data mixed
	 * @param string $flag
	 */
	public static function insert_data_into_db( $data_id, $data, $flag = '' ) {

		global $wpdb;
		$table_name = $wpdb->prefix . 'all_in_one_analytics';
		$wpdb->insert(
			$table_name,
			array(
				'time'    => current_time( 'mysql' ),
				'data'    => $data,
				'data_id' => $data_id,
				'flag'    => $flag
			)
		);

	}

}

/*

// TODO should make sure users know people will be registered
// add to UI
	// perform guest user actions for woocommerce

	$random_password = wp_generate_password();
	$user_id         = wp_create_user( $order_email, $random_password, $order_email );
	wc_update_new_customer_past_orders( $user_id ); //attaches any previous orders

}*/
/*
 * Get full product details from product id
 *
 * @return array
 */
/*public static function get_product_details_from_product_id( $product_id ) {
	// Make a $product object from product ID
	$product   = wc_get_product( $product_id );
	$image_url = wp_get_attachment_image_src( get_post_thumbnail_id( $product_id ), 'single-post-thumbnail' );
	$product->get_meta_data();
	$properties = array();
	//	$properties['product_id']         = $product_id;
	//	$properties['sku']                = $product->get_sku();
	//	$properties['category']           = $product->get_category_ids(); //array
	//$properties['name']               = $product->get_name();
	//	$properties['price']              = $product->get_price();
	//	$properties['regular_price']      = $product->get_regular_price();
	//	$properties['sale_price']         = $product->get_sale_price();
	//	$properties['on_sale_from']       = $product->get_date_on_sale_from();
	//	$properties['on_sale_to']         = $product->get_date_on_sale_to();
	//	$properties['total_sales']        = $product->get_total_sales();
	//	$properties['url']                = get_permalink( $product_id );
	//	$properties['image_url']          = $image_url[0];
	//	$properties['slug']               = $product->get_slug();
	//	$properties['date_created']       = $product->get_date_created();
	//	$properties['date_modified']      = $product->get_date_modified();
	//	$properties['status']             = $product->get_status();
	//	$properties['featured']          = $product->get_featured();
	//	$properties['catalog_visibility'] = $product->get_catalog_visibility();
	//	$properties['description']        = $product->get_description();
	//	$properties['short_description']  = $product->get_short_description();
	//	$properties['position']           = $product->get_menu_order();
	//	$properties['tax_status']         = $product->get_tax_status();
	//	$properties['tax_class']          = $product->get_tax_class();
	//	$properties['manage_stock']       = $product->get_manage_stock();
	//	$properties['stock_quantity']     = $product->get_stock_quantity();
	//	$properties['stock_status']       = $product->get_stock_status();
	//	$properties['backorders']         = $product->get_backorders();
	//	$properties['sold_individually']  = $product->get_sold_individually();
	//	$properties['purchase_note']      = $product->get_purchase_note();
	//	$properties['shipping_class']     = $product->get_shipping_class_id();
	//	$properties['weight']             = $product->get_weight();
	//	$properties['length']             = $product->get_length();
	//	$properties['width']              = $product->get_width();
	//	$properties['height']             = $product->get_height();
	//	$properties['dimensions']         = $product->get_dimensions();
	//	$properties['upsell_ids']         = $product->get_upsell_ids();
	//	$properties['cross_sell_ids']     = $product->get_cross_sell_ids();
	//	$properties['parent_id']          = $product->get_parent_id();
	//	$properties['variations']         = $product->get_attributes();
	//	$properties['default_variation']  = $product->get_default_attributes();
	//	$properties['categories']         = $product->get_categories();
	//	$properties['category_ids']       = $product->get_category_ids();
	//	$properties['tag_ids']            = $product->get_tag_ids();
	//	$properties['downloads']          = $product->get_downloads();
	//	$properties['download_expiry']    = $product->get_download_expiry();
	//	$properties['downloadable']       = $product->get_downloadable();
	//	$properties['download_limit']     = $product->get_download_limit();
	//	$properties['image_id']           = $product->get_image_id();
	//	$properties['image']              = $product->get_image();
	//	$properties['gallery_image_ids']  = $product->get_gallery_image_ids();
	//	$properties['reviews_allowed']    = $product->get_reviews_allowed();
	//	$properties['rating_count']       = $product->get_rating_counts();
	//	$properties['average_rating']     = $product->get_average_rating();
	$properties['review_count'] = $product->get_review_count();

	//clean and return
	$properties = array_filter( $properties );

	return $properties;

}*/

