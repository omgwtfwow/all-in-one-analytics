<?php

/**
 * The admin-specific functionality of the plugin.
 *
 *
 * @package    All_In_One_Analytics
 * @subpackage All_In_One_Analytics/admin
 * @author     Juan Gonzalez <hello@juangonzalez.com.au>
 */
class All_In_One_Analytics_Admin {

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
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Define and instantiate options menu
	 *
	 */
	public function create_menu() {
		/*
		 * Create a submenu page under Plugins.
		 * Framework also add "Settings" to your plugin in plugins list.
		 */

		$parent         = 'plugins.php';
		$settings_link  = 'plugins.php?page=plugin-name';
		$config_submenu = array(
			'type'            => 'menu',
			// Required, menu or metabox
			'id'              => $this->plugin_name,
			// Required, meta box id, unique per page, to save: get_option( id )
			'menu'            => $parent,
			// Required, sub page to your options page
			'submenu'         => true,
			// Required for submenu
			'settings-link'   => $settings_link,
			'title'           => esc_html__( 'All in One Analytics & Tracking', 'plugin-name' ),
			//The name of this page
			'capability'      => 'manage_options',
			// The capability needed to view the page
			'plugin_basename' => plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_name . '.php' ),
			'tabbed'          => true
		);

		//Get extra data needed for setting menu
		global $wp_roles;
		$roles           = $wp_roles->get_names();
		$categories      = Array();
		$categories_list = get_categories();
		//TODO finish the category filter
		foreach ( $categories_list as $category ) {
			array_push( $categories, $category->name );
		}
		$trait_options = array(
			'First Name',
			'Last Name',
			'Email',
			'Username',
			'Display Name',
			'Signup Date',
			'URL',
			'Bio',
			'ID'
		);
		$args          = array(
			'public'   => true,
			'_builtin' => false
		);
		$post_types    = get_post_types( $args, 'names', 'and' );

		//EVENT SOURCES
		$fields[] = array(
			'name'   => 'Events',
			'title'  => 'Event Sources',
			'icon'   => 'dashicons-admin-plugins',
			'fields' => array(

				//CORE EVENTS
				array(
					'id'      => 'core_event_settings',
					'type'    => 'group',
					'options' => array(
						'cols'        => 2,
						'group_title' => esc_html__( 'WordPress Event Settings', 'plugin-name' ) . '<br><small>' . esc_html__( 'Signed up, logged in, commented, etc...', 'plugin-name' ) . '</small>',
					),
					'fields'  => array(
						array(
							'type'        => 'fieldset',
							'id'          => 'track_signups_fieldset',
							'title'       => 'Track sign ups',
							'default'     => 'yes',
							'description' => 'Trigger an event when people sign up',
							'options'     => array(
								'cols' => 1
							),
							'fields'      => array(
								array(
									'id'      => 'track_signups',
									'type'    => 'switcher',
									'default' => 'yes',
									'prepend' => 'Track sign ups?'
								),
								array(
									'id'         => 'track_signups_custom_event_label',
									'type'       => 'text',
									'dependency' => array(
										'track_signups',
										'==',
										'true'
									),
									'prepend'    => 'Event Name',
									'attributes' => array(
										'placeholder' => 'Signed up'
									)
								)
							)
						),
						array(
							'type'        => 'fieldset',
							'id'          => 'track_logins_fieldset',
							'title'       => 'Track log ins',
							'description' => 'Triggers an event when people log in.',
							'options'     => array(
								'cols' => 1
							),
							'fields'      => array(
								array(
									'id'      => 'track_logins',
									'type'    => 'switcher',
									'default' => 'yes',
									'prepend' => 'Track log ins?'
								),
								array(
									'id'         => 'track_logins_custom_event_label',
									'type'       => 'text',
									'dependency' => array(
										'track_logins',
										'==',
										'true'
									),
									'prepend'    => 'Event Name',
									'attributes' => array(
										'placeholder' => 'Logged in'
									)
								)
							)
						),
						array(
							'type'        => 'fieldset',
							'id'          => 'track_comments_fieldset',
							'title'       => 'Track comments',
							'description' => 'Trigger an event when people leave a comment',
							'options'     => array(
								'cols' => 1
							),
							'fields'      => array(
								array(
									'id'      => 'track_comments',
									'type'    => 'switcher',
									'default' => 'yes',
									'prepend' => 'Track comments?'
								),
								array(
									'id'         => 'track_comments_custom_event_label',
									'type'       => 'text',
									'dependency' => array(
										'track_comments',
										'==',
										'true'
									),
									'prepend'    => 'Event Name',
									'attributes' => array(
										'placeholder' => 'Commented'
									)
								)
							)
						),
						array(
							'type'        => 'fieldset',
							'id'          => 'track_posts_fieldset',
							'title'       => 'Track posts',
							'description' => 'Trigger an event when people view a post. You do not normally need these because the data is also in the page calls',
							'options'     => array(
								'cols' => 1
							),
							'fields'      => array(
								array(
									'id'      => 'track_posts',
									'type'    => 'switcher',
									'default' => 'no',
									'prepend' => 'Track posts?'
								),
								array(
									'id'         => 'track_posts_custom_event_label',
									'type'       => 'text',
									'dependency' => array(
										'track_posts',
										'==',
										'true'
									),
									'prepend'    => 'Event Name',
									'attributes' => array(
										'placeholder' => 'Viewed post'
									)
								)
							)
						),
						array(
							'type'        => 'fieldset',
							'id'          => 'track_archives_fieldset',
							'title'       => 'Track archives',
							'description' => 'Trigger an event when people view an archive page, like the categories or tags lists.',
							'options'     => array(
								'cols' => 1
							),
							'fields'      => array(
								array(
									'id'      => 'track_archives',
									'type'    => 'switcher',
									'default' => 'no',
									'prepend' => 'Trigger an event when people view archives? ie,  Blog page, category pages, etc..'
								),
								array(
									'id'         => 'track_archives_custom_event_label',
									'type'       => 'text',
									'dependency' => array(
										'track_archives',
										'==',
										'true'
									),
									'prepend'    => 'Event Name',
									'attributes' => array(
										'placeholder' => 'Viewed an archive'
									)
								)
							)
						),
						array(
							'type'        => 'fieldset',
							'id'          => 'track_login_page_fieldset',
							'title'       => 'Track "Log in" page',
							//'default'     => 'no',
							'description' => 'Trigger an event when people view an archive page, like the categories or tags lists.',
							'options'     => array(
								'cols' => 1
							),
							'fields'      => array(
								array(
									'id'      => 'track_login_page',
									'type'    => 'switcher',
									'prepend' => 'Track log ins?'
								),
								array(
									'id'         => 'track_login_page_custom_event_label',
									'type'       => 'text',
									'dependency' => array(
										'track_login_page',
										'==',
										'true'
									),
									'prepend'    => 'Event Name',
									'attributes' => array(
										'placeholder' => 'Viewed log in page"'
									)
								)
							)
						),
						array(
							'type'        => 'fieldset',
							'id'          => 'track_pages_fieldset',
							'title'       => 'Track pages',
							'description' => 'Trigger an event when people view a post. You do not normally need these because the data is also in the page calls',
							'options'     => array(
								'cols' => 1
							),
							'fields'      => array(
								array(
									'id'      => 'track_pages',
									'type'    => 'switcher',
									'default' => 'no',
									'prepend' => 'Trigger custom event for viewed pages? (Viewed "Page Name")'
								)
							)
						)
					)
				),

				//FORMS
				array(
					'id'         => 'form_event_settings',
					'type'       => 'group',
					'options'    => array(
						'cols'        => 4,
						'group_title' => esc_html__( 'Form Event Settings', 'plugin-name' ) . '<br><small>' . esc_html__( 'Ninja Forms, Gravity Forms, Etc...', 'plugin-name' ) . '</small>',

					),
					'attributes' => array(
						'style' => 'width: 600px;',
					),
					'fields'     => array(
						array(
							'type'       => 'content',
							'wrap_class' => 'no-border-bottom',
							// for all fieds
							'title'      => 'Tracking Forms',
							//'content' => 'In order for the forms to be tracked server-side (ie, for Zapier) a user id must be included.',
							'content'    => esc_html__( 'In order for the forms to be tracked server-side (ie, for Zapier) a user id must be included', 'plugin-name' ) . '<br>' . esc_html__( 'This happens automatically for logged-in users, but you can also include a field with the label aio-id and put the user id there.', 'plugin-name' ) . '',
							//	'before' => 'Before Text',
							//	'after'  => 'After Text',
						),

						//NINJA FORMS
						array(
							'type'        => 'fieldset',
							'id'          => 'track_ninja_forms_fieldset',
							'title'       => 'Track Ninja Forms',
							'description' => 'Trigger events when people complete any Ninja Form',
							'options'     => array(
								'cols' => 3
							),
							'fields'      => array(
								array(
									'id'   => 'track_ninja_forms',
									'type' => 'switcher'
								)
							)
						),
						//GRAVITY FORMS
						array(
							'type'        => 'fieldset',
							'id'          => 'track_gravity_forms_fieldset',
							'title'       => 'Track Gravity Forms',
							'description' => 'Trigger events when people complete any Gravity Form',
							'options'     => array(
								'cols' => 3
							),
							'fields'      => array(
								array(
									'id'   => 'track_gravity_forms',
									'type' => 'switcher'
								)
							)
						),
						//OTHER SETTINGS
						//TODO test
						/*	array(
								'id'          => 'forms_trigger_identify_calls',
								'type'        => 'switcher',
								'default'     => 'no',
								'title'       => 'Update user traits when forms are submitted?',
								'description' => 'ie, if a form has user info first name, last name, email, etc... we will make an identify call. Can be useful but can also cause unexpected issues.'
							)*/
					)
				),

				//WooCommerce
				array(
					'id'      => 'woocommerce_event_settings',
					'type'    => 'group',
					'class'   => 'track_woocommerce_fieldset_2',
					'options' => array(
						'cols'        => 1,
						'group_title' => esc_html__( 'WooCommerce Event Settings', 'plugin-name' ) . '<br><small>' . esc_html__( 'Purchase, Add To Cart, Viewed Product, etc...', 'plugin-name' ) . '</small>',

					),
					'fields'  => array(
						array(
							'type'        => 'fieldset',
							'id'          => 'track_woocommerce_fieldset',
							'title'       => 'Track WooCommerce',
							'description' => 'Trigger events when people complete Woocommerce actions',
							'options'     => array(
								'cols' => 1
							),
							'fields'      => array(
								array(
									'id'   => 'track_woocommerce',
									'type' => 'switcher'
								),
								array(
									'id'      => 'hidden_1',
									'type'    => 'hidden',
									'default' => 'hidden',
								),
								array(
									'id'         => 'woocommerce_events',
									'type'       => 'accordion',
									'dependency' => array(
										'track_woocommerce',
										'==',
										'true'
									),
									'options'    => array(
										'allow_all_open' => true,
										'cols'           => 2
									),
									'sections'   => array(

										//CART & PRODUCT EVENTs
										array(
											'options' => array(
												'icon'   => 'fa fa-star',
												'title'  => 'Product and Cart Events',
												'closed' => true,
												'cols'   => 2
											),
											'fields'  => array(
												//Product clicks
												array(
													'type'       => 'fieldset',
													'dependency' => array(
														'track_woocommerce',
														'==',
														'true'
													),
													'id'         => 'track_product_clicks_fieldset',
													'title'      => 'Track product clicks',
													'options'    => array(
														'cols' => 1
													),
													'fields'     => array(
														array(
															'id'      => 'track_product_clicks',
															'type'    => 'switcher',
															'default' => 'no',
															'prepend' => 'Track product clicks?'
														),
														array(
															'id'         => 'track_product_clicks_custom_event_label',
															'type'       => 'text',
															'dependency' => array(
																'track_product_clicks',
																'==',
																'true'
															),
															'prepend'    => 'Event Name',
															'attributes' => array(
																'placeholder' => 'Product Clicked'
															)
														)
													)
												),
												//Add to cart
												array(
													'type'       => 'fieldset',
													'dependency' => array(
														'track_woocommerce',
														'==',
														'true'
													),
													'id'         => 'track_add_to_cart_fieldset',
													'title'      => 'Track add to cart',
													'options'    => array(
														'cols' => 1
													),
													'fields'     => array(
														array(
															'id'      => 'track_add_to_cart',
															'type'    => 'switcher',
															'default' => 'yes',
															'prepend' => 'Track add to cart?'
														),
														array(
															'id'         => 'track_add_to_cart_custom_event_label',
															'type'       => 'text',
															'dependency' => array(
																'track_add_to_cart',
																'==',
																'true'
															),
															'prepend'    => 'Event Name',
															'attributes' => array(
																'placeholder' => 'Product Added'
															)
														)
													)
												),
												//Removed from cart
												array(
													'type'       => 'fieldset',
													'dependency' => array(
														'track_woocommerce',
														'==',
														'true'
													),
													'id'         => 'track_remove_from_cart_fieldset',
													'title'      => 'Track products removed from cart',
													'options'    => array(
														'cols' => 1
													),
													'fields'     => array(
														array(
															'id'      => 'track_remove_from_cart',
															'type'    => 'switcher',
															'default' => 'yes',
															'prepend' => 'Track remove from cart?'
														),
														array(
															'id'         => 'track_remove_from_cart_custom_event_label',
															'type'       => 'text',
															'dependency' => array(
																'track_remove_from_cart',
																'==',
																'true'
															),
															'prepend'    => 'Event Name',
															'attributes' => array(
																'placeholder' => 'Product Removed'
															)
														)
													)
												),
												//Viewed cart
												array(
													'type'       => 'fieldset',
													'dependency' => array(
														'track_woocommerce',
														'==',
														'true'
													),
													'id'         => 'track_cart_viewed_fieldset',
													'title'      => 'Track cart viewed',
													'options'    => array(
														'cols' => 1
													),
													'fields'     => array(
														array(
															'id'      => 'track_cart_viewed',
															'type'    => 'switcher',
															'default' => 'yes',
															'prepend' => 'Track cart viewed?'
														),
														array(
															'id'         => 'track_cart_viewed_custom_event_label',
															'type'       => 'text',
															'dependency' => array(
																'track_cart_viewed',
																'==',
																'true'
															),
															'prepend'    => 'Event Name',
															'attributes' => array(
																'placeholder' => 'Viewed Cart'
															)
														)
													)
												),
												//Applied Coupon
												array(
													'type'       => 'fieldset',
													'dependency' => array(
														'track_woocommerce',
														'==',
														'true'
													),
													'id'         => 'track_coupons_fieldset',
													'title'      => 'Track coupons',
													'options'    => array(
														'cols' => 1
													),
													'fields'     => array(
														array(
															'id'      => 'track_coupons',
															'type'    => 'switcher',
															'default' => 'yes',
															'prepend' => 'Track coupons?'
														),
														array(
															'id'         => 'track_coupons_custom_event_label',
															'type'       => 'text',
															'dependency' => array(
																'track_coupons',
																'==',
																'true'
															),
															'prepend'    => 'Event Name',
															'attributes' => array(
																'placeholder' => 'Coupon Applied'
															)
														)
													)
												),
											)

										),

										//CHECKOUT EVENTS
										array(
											'options' => array(
												'icon'   => 'fa fa-star',
												'title'  => 'Checkout Events',
												'closed' => true,
												'cols'   => 2
											),
											'fields'  => array(
												//Checkout started
												array(
													'type'       => 'fieldset',
													'dependency' => array(
														'track_woocommerce',
														'==',
														'true'
													),
													'id'         => 'track_initiated_checkout_fieldset',
													'title'      => 'Track checkout started',
													'options'    => array(
														'cols' => 1
													),
													'fields'     => array(
														array(
															'id'      => 'track_initiated_checkout',
															'type'    => 'switcher',
															'default' => 'no',
															'prepend' => 'Track checkout started?'
														),
														array(
															'id'         => 'track_initiated_checkout_custom_event_label',
															'type'       => 'text',
															'dependency' => array(
																'track_initiated_checkout',
																'==',
																'true'
															),
															'prepend'    => 'Event Name',
															'attributes' => array(
																'placeholder' => 'Checkout Started'
															)
														)
													)
												),
												//Checkout step viewed
												array(
													'type'       => 'fieldset',
													'dependency' => array(
														'track_woocommerce',
														'==',
														'true'
													),
													'id'         => 'track_checkout_step_viewed_fieldset',
													'title'      => 'Track checkout step viewed',
													'options'    => array(
														'cols' => 1
													),
													'fields'     => array(
														array(
															'id'      => 'track_checkout_step_viewed',
															'type'    => 'switcher',
															'default' => 'yes',
															'prepend' => 'Track checkout step viewed?'
														),
														array(
															'id'         => 'track_checkout_step_viewed_custom_event_label',
															'type'       => 'text',
															'dependency' => array(
																'track_checkout_step_viewed',
																'==',
																'true'
															),
															'prepend'    => 'Event Name',
															'attributes' => array(
																'placeholder' => 'Checkout Step Viewed'
															)
														)
													)
												),
												//Checkout step completed
												array(
													'type'       => 'fieldset',
													'dependency' => array(
														'track_woocommerce',
														'==',
														'true'
													),
													'id'         => 'track_checkout_step_completed_fieldset',
													'title'      => 'Track checkout step completed',
													'options'    => array(
														'cols' => 1
													),
													'fields'     => array(
														array(
															'id'      => 'track_checkout_step_completed',
															'type'    => 'switcher',
															'default' => 'yes',
															'prepend' => 'Track check out steps?'
														),
														array(
															'id'         => 'track_checkout_step_completed_custom_event_label',
															'type'       => 'text',
															'dependency' => array(
																'track_checkout_step_completed',
																'==',
																'true'
															),
															'prepend'    => 'Event Name',
															'attributes' => array(
																'placeholder' => 'Checkout Step Completed'
															)
														)
													)
												),
											)

										),

										//ORDER EVENTS
										array(
											'options' => array(
												'icon'   => 'fa fa-star',
												'title'  => 'Order Events',
												'closed' => true,
												'cols'   => 2
											),
											'fields'  => array(
												//Order Processing
												array(
													'type'       => 'fieldset',
													'dependency' => array(
														'track_woocommerce',
														'==',
														'true'
													),
													'id'         => 'track_order_processing_fieldset',
													'title'      => 'Track order processing',
													'options'    => array(
														'cols' => 1
													),
													'fields'     => array(
														array(
															'id'      => 'track_order_processing',
															'type'    => 'switcher',
															'default' => 'no',
															'prepend' => 'Track order processing?'
														),
														array(
															'id'         => 'track_order_processing_custom_event_label',
															'type'       => 'text',
															'dependency' => array(
																'track_order_processing',
																'==',
																'true'
															),
															'prepend'    => 'Event Name',
															'attributes' => array(
																'placeholder' => 'Order Processing'
															)
														)
													)
												),
												//Order Pending
												array(
													'type'       => 'fieldset',
													'dependency' => array(
														'track_woocommerce',
														'==',
														'true'
													),
													'id'         => 'track_order_pending_fieldset',
													'title'      => 'Track order pending',
													'options'    => array(
														'cols' => 1
													),
													'fields'     => array(
														array(
															'id'      => 'track_order_pending',
															'type'    => 'switcher',
															'default' => 'no',
															'prepend' => 'Track order pending?'
														),
														array(
															'id'         => 'track_order_pending_custom_event_label',
															'type'       => 'text',
															'dependency' => array(
																'track_order_pending',
																'==',
																'true'
															),
															'prepend'    => 'Event Name',
															'attributes' => array(
																'placeholder' => 'Order Pending'
															)
														)
													)
												),
												//Order Failed
												array(
													'type'       => 'fieldset',
													'dependency' => array(
														'track_woocommerce',
														'==',
														'true'
													),
													'id'         => 'track_order_failed_fieldset',
													'title'      => 'Track order failed',
													'options'    => array(
														'cols' => 1
													),
													'fields'     => array(
														array(
															'id'      => 'track_order_failed',
															'type'    => 'switcher',
															'default' => 'no',
															'prepend' => 'Track order failed?'
														),
														array(
															'id'         => 'track_order_failed_custom_event_label',
															'type'       => 'text',
															'dependency' => array(
																'track_order_failed',
																'==',
																'true'
															),
															'prepend'    => 'Event Name',
															'attributes' => array(
																'placeholder' => 'Order Failed'
															)
														)
													)
												),
												//Order On-Hold
												array(
													'type'       => 'fieldset',
													'dependency' => array(
														'track_woocommerce',
														'==',
														'true'
													),
													'id'         => 'track_order_on_hold_fieldset',
													'title'      => 'Track order on-hold',
													'options'    => array(
														'cols' => 1
													),
													'fields'     => array(
														array(
															'id'      => 'track_order_on_hold',
															'type'    => 'switcher',
															'default' => 'no',
															'prepend' => 'Track order on-hold?'
														),
														array(
															'id'         => 'track_track_order_on_hold_custom_event_label',
															'type'       => 'text',
															'dependency' => array(
																'track_order_on_hold',
																'==',
																'true'
															),
															'prepend'    => 'Event Name',
															'attributes' => array(
																'placeholder' => 'Order On-hold'
															)
														)
													)
												),
												//Order Paid
												array(
													'type'       => 'fieldset',
													'dependency' => array(
														'track_woocommerce',
														'==',
														'true'
													),
													'id'         => 'track_order_paid_fieldset',
													'title'      => 'Track order paid',
													'options'    => array(
														'cols' => 1
													),
													'fields'     => array(
														array(
															'id'      => 'track_order_paid',
															'type'    => 'switcher',
															'default' => 'yes',
															'prepend' => 'Track order paid?'
														),
														array(
															'id'         => 'track_order_paid_custom_event_label',
															'type'       => 'text',
															'dependency' => array(
																'track_order_paid',
																'==',
																'true'
															),
															'prepend'    => 'Event Name',
															'attributes' => array(
																'placeholder' => 'Order Paid'
															)
														)
													)
												),
												//Order Completed
												array(
													'type'       => 'fieldset',
													'dependency' => array(
														'track_woocommerce',
														'==',
														'true'
													),
													'id'         => 'track_order_completed_fieldset',
													'title'      => 'Track order completed',
													'options'    => array(
														'cols' => 1
													),
													'fields'     => array(
														array(
															'id'      => 'track_order_completed',
															'type'    => 'switcher',
															'default' => 'yes',
															'prepend' => 'Track order paid?'
														),
														array(
															'id'         => 'track_order_completed_custom_event_label',
															'type'       => 'text',
															'dependency' => array(
																'track_order_completed',
																'==',
																'true'
															),
															'prepend'    => 'Event Name',
															'attributes' => array(
																'placeholder' => 'Order Completed'
															)
														)
													)
												),
												//Order Refunded
												array(
													'type'       => 'fieldset',
													'dependency' => array(
														'track_woocommerce',
														'==',
														'true'
													),
													'id'         => 'track_order_refunded_fieldset',
													'title'      => 'Track order refunded',
													'options'    => array(
														'cols' => 1
													),
													'fields'     => array(
														array(
															'id'      => 'track_order_refunded',
															'type'    => 'switcher',
															'default' => 'no',
															'prepend' => 'Track order refunded?'
														),
														array(
															'id'         => 'track_order_refunded_custom_event_label',
															'type'       => 'text',
															'dependency' => array(
																'track_order_refunded',
																'==',
																'true'
															),
															'prepend'    => 'Event Name',
															'attributes' => array(
																'placeholder' => 'Order Refunded'
															)
														)
													)
												),
												//Order Cancelled
												array(
													'type'       => 'fieldset',
													'dependency' => array(
														'track_woocommerce',
														'==',
														'true'
													),
													'id'         => 'track_order_cancelled_fieldset',
													'title'      => 'Track order cancelled',
													'options'    => array(
														'cols' => 1
													),
													'fields'     => array(
														array(
															'id'      => 'track_order_cancelled',
															'type'    => 'switcher',
															'default' => 'yes',
															'prepend' => 'Track order cancelled?'
														),
														array(
															'id'         => 'track_order_cancelled_custom_event_label',
															'type'       => 'text',
															'dependency' => array(
																'track_order_cancelled',
																'==',
																'true'
															),
															'prepend'    => 'Event Name',
															'attributes' => array(
																'placeholder' => 'Order Cancelled'
															)
														)
													)
												),
											)

										)

									)
								)

							)
						),
						array(
							'type'        => 'fieldset',
							'id'          => 'track_woocommerce_meta_fieldset',
							'title'       => 'Add WooCommerce user meta to identify calls',
							'description' => 'Add WooCommerce user meta to identify calls',
							'options'     => array(
								'cols' => 3
							),
							'fields'      => array(
								array(
									'id'   => 'track_woocommerce_meta',
									'type' => 'switcher'
								)
							)
						),
					)
				),

				//LearnDash
				array(
					'id'      => 'learndash_event_settings',
					'type'    => 'group',
					'class'   => 'track_learndash_fieldset_2',
					'options' => array(
						'cols'        => 3,
						'group_title' => esc_html__( 'LearnDash Event Settings', 'plugin-name' ) . '<br><small>' . esc_html__( 'Enrolled, completed lesson, completed quiz, etc..', 'plugin-name' ) . '</small>',

					),
					'fields'  => array(
						array(
							'type'        => 'fieldset',
							'id'          => 'track_learndash_fieldset',
							'title'       => 'Track LearnDash',
							'description' => 'Trigger events when people complete LearnDash actions',
							'options'     => array(
								'cols' => 2
							),
							'fields'      =>
								array(
									array(
										'id'   => 'track_learndash',
										'type' => 'switcher'
									),
									array(
										'id'      => 'hidden_1',
										'type'    => 'hidden',
										'default' => 'hidden',
									),
									array(
										'type'       => 'content',
										'dependency' => array(
											'track_learndash',
											'==',
											'true'
										),
										'wrap_class' => 'no-border-bottom', // for all fieds
										'title'      => 'LearnDash Events',
										//	'content' => 'LLLLLLL',
										//	'before' => 'Before Text',
										//	'after'  => 'After Text',
									),
									array(
										'id'      => 'hidden_2',
										'type'    => 'hidden',
										'default' => 'hidden',
									),
									array(
										'type'       => 'fieldset',
										'dependency' => array(
											'track_learndash',
											'==',
											'true'
										),
										'id'         => 'track_enrollments_fieldset',
										'title'      => 'Track enrollments',
										'options'    => array(
											'cols' => 1
										),
										'fields'     => array(
											array(
												'id'      => 'track_enrollments',
												'type'    => 'switcher',
												'default' => 'yes',
												'prepend' => 'Track enrollments?'
											),
											array(
												'id'         => 'track_enrollments_custom_event_label',
												'type'       => 'text',
												'dependency' => array(
													'track_enrollments',
													'==',
													'true'
												),
												'prepend'    => 'Event Name',
												'attributes' => array(
													'placeholder' => 'Enrolled'
												)
											)
										)
									),
									array(
										'type'       => 'fieldset',
										'dependency' => array(
											'track_learndash',
											'==',
											'true'
										),
										'id'         => 'track_topics_fieldset',
										'title'      => 'Track topics',
										'options'    => array(
											'cols' => 1
										),
										'fields'     => array(
											array(
												'id'      => 'track_topics',
												'type'    => 'switcher',
												'default' => 'yes',
												'prepend' => 'Track topics?'
											),
											array(
												'id'         => 'track_topics_custom_event_label',
												'type'       => 'text',
												'dependency' => array(
													'track_topics',
													'==',
													'true'
												),
												'prepend'    => 'Event Name',
												'attributes' => array(
													'placeholder' => 'Topic Completed'
												)
											)
										)
									),
									array(
										'type'       => 'fieldset',
										'dependency' => array(
											'track_learndash',
											'==',
											'true'
										),
										'id'         => 'track_lessons_fieldset',
										'title'      => 'Track lessons',
										'options'    => array(
											'cols' => 1
										),
										'fields'     => array(
											array(
												'id'      => 'track_lessons',
												'type'    => 'switcher',
												'default' => 'yes',
												'prepend' => 'Track lessons?'
											),
											array(
												'id'         => 'track_lessons_custom_event_label',
												'type'       => 'text',
												'dependency' => array(
													'track_lessons',
													'==',
													'true'
												),
												'prepend'    => 'Event Name',
												'attributes' => array(
													'placeholder' => 'Lesson Completed'
												)
											)
										)
									),
									array(
										'type'       => 'fieldset',
										'dependency' => array(
											'track_learndash',
											'==',
											'true'
										),
										'id'         => 'track_courses_fieldset',
										'title'      => 'Track courses',
										'options'    => array(
											'cols' => 1
										),
										'fields'     => array(
											array(
												'id'      => 'track_courses',
												'type'    => 'switcher',
												'default' => 'no',
												'prepend' => 'Track courses?'
											),
											array(
												'id'         => 'track_courses_custom_event_label',
												'type'       => 'text',
												'dependency' => array(
													'track_courses',
													'==',
													'true'
												),
												'prepend'    => 'Event Name',
												'attributes' => array(
													'placeholder' => 'Course Completed'
												)
											)
										)
									),
									array(
										'type'       => 'fieldset',
										'dependency' => array(
											'track_learndash',
											'==',
											'true'
										),
										'id'         => 'track_quizzes_fieldset',
										'title'      => 'Track quizzes',
										'options'    => array(
											'cols' => 1
										),
										'fields'     => array(
											array(
												'id'      => 'track_quizzes',
												'type'    => 'switcher',
												'default' => 'no',
												'prepend' => 'Trigger events on quizzes? '
											),
											array(
												'id'         => 'track_quizzes_custom_event_label',
												'type'       => 'text',
												'dependency' => array(
													'track_quizzes',
													'==',
													'true'
												),
												'prepend'    => 'Event Name',
												'attributes' => array(
													'placeholder' => 'Quiz Completed'
												)
											)
										)
									),
									array(
										'type'       => 'fieldset',
										'dependency' => array(
											'track_learndash',
											'==',
											'true'
										),
										'id'         => 'track_assignments_fieldset',
										'title'      => 'Track assignments',
										'options'    => array(
											'cols' => 1
										),
										'fields'     => array(
											array(
												'id'      => 'track_assignments',
												'type'    => 'switcher',
												'default' => 'no',
												'prepend' => 'Trigger events on assignment uploads? '
											),
											array(
												'id'         => 'track_assignments_custom_event_label',
												'type'       => 'text',
												'dependency' => array(
													'track_assignments',
													'==',
													'true'
												),
												'prepend'    => 'Event Name',
												'attributes' => array(
													'placeholder' => 'Assignment Uploaded'
												)
											)
										)
									)
								),
						),

					)
				),

				//Gamipress WIP

				//Video WIP

				//GLOBAL OPTIONS
				array(
					'id'          => 'include_user_ids',
					'type'        => 'switcher',
					'title'       => 'Add userId and email as properties to each event.',
					'default'     => 'no',
					'description' => 'Some email tools require this in order to attribute events to each user properly.'
				),
				array(
					'type'        => 'fieldset',
					'id'          => 'event_identifier_fieldset',
					'title'       => 'Append a label to server side event names',
					'description' => 'A way to tag server side event names. Can be useful when you\'re combining them with client side events in other tools.',
					'options'     => array(
						'cols' => 2
					),
					'fields'      => array(
						array(
							'id'      => 'use_event_identifier',
							'type'    => 'switcher',
							'prepend' => 'Label server side events?'
						),
						array(
							'id'         => 'use_event_identifier_label',
							'type'       => 'text',
							'default'    => ' - Server Side',
							'attributes' => array(
								'placeholder' => ' - Server Side'
							)
						)
					)
				)
			)
		);
		//DATA DESTINATIONS
		$fields[] = array(
			'id'     => 'destinations',
			'type'   => 'group',
			'title'  => 'Data Destinations',
			'icon'   => 'dashicons-networking',
			'fields' => array(
				//GA
				array(
					'type'    => 'fieldset',
					'id'      => 'Google Analytics',
					'title'   => 'Google Analytics',
					'options' => array(
						'cols' => 1
					),
					'fields'  => array(
						array(
							'id'   => 'google_analytics_switcher',
							'type' => 'switcher'
						),
						array(
							'id'         => 'google_analytics_settings',
							'type'       => 'group',
							'dependency' => array(
								'google_analytics_switcher',
								'==',
								'true'
							), // check for true/false by field id
							'options'    => array(
								'cols'        => 1,
								'group_title' => 'Google Analytics Settings'
							),
							'fields'     => array(
								array(
									'id'         => 'trackingId',
									'type'       => 'text',
									'title'      => 'Website Tracking ID',
									'attributes' => array(
										'style' => 'width: 150px;',
									),
								),
								array(
									'id'      => 'reporting',
									'type'    => 'group',
									'options' => array(
										'cols'        => 2,
										'group_title' => 'Reporting'
									),
									'fields'  => array(
										array(
											'id'      => 'doubleClick',
											'type'    => 'switcher',
											'default' => 'yes',
											'title'   => 'Remarketing, Display Ads and Demographic Reports.'
										),
										array(
											'id'      => 'enhancedLinkAttribution',
											'type'    => 'switcher',
											'default' => 'yes',
											'title'   => 'Enable Enhanced Link Attribution'
										),
									),
								),
								array(
									'id'      => 'classic_analytics',
									'type'    => 'group',
									'options' => array(
										'cols'        => 2,
										'group_title' => 'Classic Analytics'
									),
									'fields'  => array(
										array(
											'id'      => 'classic',
											'type'    => 'switcher',
											'default' => 'no',
											'title'   => 'Use Classic Analytics on Your Site'
										),
										array(
											'id'         => 'ignoredReferrers',
											'type'       => 'textarea',
											'title'      => 'Ignored Refferers',
											'default'    => ' ',
											'attributes' => array(
												'placeholder' => 'one per line'
											),
										),
									),
								),
								array(
									'id'      => 'track_pages',
									'type'    => 'group',
									'options' => array(
										'cols'        => 2,
										'group_title' => 'Track Pages'
									),
									'fields'  => array(
										array(
											'id'      => 'includeSearch',
											'type'    => 'switcher',
											'default' => 'no',
											'title'   => 'Include the Querystring in Page Views'
										),
										array(
											'id'      => 'trackCategorizedPages',
											'type'    => 'switcher',
											'default' => 'no',
											'title'   => 'Track Categorized Pages'
										),
										array(
											'id'      => 'trackNamedPages',
											'type'    => 'switcher',
											'default' => 'no',
											'title'   => 'Track Named Pages'
										),
									),
								),
								array(
									'id'      => 'sampling',
									'type'    => 'group',
									'options' => array(
										'cols'        => 2,
										'group_title' => 'Sampling'
									),
									'fields'  => array(
										array(
											'id'      => 'sampleRate',
											'type'    => 'text',
											'default' => '100',
											'title'   => 'Sample Rate'
										),
										array(
											'id'      => 'siteSpeedSampleRate',
											'type'    => 'text',
											'default' => '1',
											'title'   => 'Site Speed Sample Rate'
										),
									),
								),
								array(
									'id'      => 'custom_dimensions_metrics',
									'type'    => 'group',
									'options' => array(
										'cols'        => 2,
										'group_title' => 'Custom Dimensions & Metrics'
									),
									'fields'  => array(
										array(
											'type'    => 'group',
											'id'      => 'dimensions',
											'title'   => esc_html__( 'Custom Dimensions', 'plugin-name' ),
											'options' => array(
												'repeater'     => true,
												'accordion'    => true,
												'button_title' => esc_html__( 'Add dimension', 'plugin-name' ),
												'group_title'  => esc_html__( '', 'plugin-name' )
											),
											'fields'  => array(
												array(
													'id'         => 'local_dimension',
													'type'       => 'text',
													'default'    => '',
													'attributes' => array(
														'data-title'  => 'title',
														'placeholder' => esc_html__( 'Trait or property name', 'plugin-name' ),
													),
												),
												array(
													'id'         => 'ga_dimension',
													'type'       => 'number',
													'before'     => 'dimension',
													'default'    => '0',
													'min'        => '0',
													'max'        => '200',
													'step'       => '1',
													'attributes' => array(
														'placeholder' => esc_html__( 0, 'plugin-name' ),
													),
												),
											),
										),
										array(
											'type'    => 'group',
											'id'      => 'metrics',
											'title'   => esc_html__( 'Custom Metrics', 'plugin-name' ),
											//'description' => esc_html__( 'Custom Metrics desc', 'plugin-name' ),
											'options' => array(
												'repeater'     => true,
												'accordion'    => true,
												'button_title' => esc_html__( 'Add metric', 'plugin-name' ),
												'group_title'  => esc_html__( '', 'plugin-name' )
											),
											'fields'  => array(
												array(
													'id'         => 'local_metric',
													'type'       => 'text',
													'default'    => ' ',
													'attributes' => array(
														'data-title'  => 'title',
														'placeholder' => esc_html__( 'Numerical trait or property name', 'plugin-name' ),
													),
												),
												array(
													'id'      => 'ga_metric',
													'type'    => 'number',
													'before'  => 'metric',
													'default' => '0',
													'min'     => '0',
													'max'     => '200',
													'step'    => '1',
												),
											),
										),
										array(
											'id'      => 'setAllMappedProps',
											'type'    => 'switcher',
											'default' => 'no',
											'title'   => 'Set Custom Dimensions & Metrics to the Page'
										),
									),

								),
								array(
									'id'      => 'property_trait_mapping',
									'type'    => 'group',
									'options' => array(
										'cols'        => 2,
										'group_title' => 'Property & Trait Mapping'
									),
									'fields'  => array(
										array(
											'type'    => 'group',
											'id'      => 'groupings',
											'title'   => esc_html__( 'Content Groupings', 'plugin-name' ),
											'options' => array(
												'repeater'     => true,
												'accordion'    => true,
												'button_title' => esc_html__( 'Add grouping', 'plugin-name' ),
												'group_title'  => esc_html__( '', 'plugin-name' )
											),
											'fields'  => array(
												array(
													'id'         => 'local_trait',
													'type'       => 'text',
													'default'    => ' ',
													'attributes' => array(
														'data-title'  => 'title',
														'placeholder' => esc_html__( 'Trait or property name', 'plugin-name' ),
													),
												),
												array(
													'id'      => 'ga_value',
													'type'    => 'number',
													'before'  => 'contentGroup',
													'default' => '0',
													'min'     => '0',
													'max'     => '200',
													'step'    => '1',
												),
											),
										),
										array(
											'type'    => 'group',
											'id'      => 'traits_protocol_params',
											'default' => ' ',
											'title'   => esc_html__( 'Map Traits or Properties to Measurement Protocol Params', 'plugin-name' ),
											//'description' => esc_html__( 'Custom Metrics desc', 'plugin-name' ),
											'options' => array(
												'repeater'     => true,
												'accordion'    => true,
												'button_title' => esc_html__( 'Add mapping', 'plugin-name' ),
												'group_title'  => esc_html__( '', 'plugin-name' )
											),
											'fields'  => array(
												array(
													'id'         => 'local_metric_ga',
													'type'       => 'text',
													'default'    => ' ',
													'attributes' => array(
														'data-title'  => 'title',
														'placeholder' => esc_html__( 'Numerical trait or property name', 'plugin-name' ),
													),
												),
												array(
													'id'             => 'protocol',
													'type'           => 'select',
													'title'          => 'select',
													'options'        => array(
														'plt'   => 'plt',
														'pdt'   => 'pdt',
														'gclid' => 'gclid',
													),
													'default_option' => 'Select protocol',
													'default'        => '',
													'attributes'     => array(
														'style' => 'width: 200px; height: 125px;',
													),
													'class'          => 'chosen',
												),
											),
										),
									),

								),
								array(
									'id'      => 'other_settings',
									'type'    => 'group',
									'options' => array(
										'cols'        => 2,
										'group_title' => 'Other Settings'
									),
									'fields'  => array(
										array(
											'id'      => 'anonymizeIp',
											'type'    => 'switcher',
											'default' => 'no',
											'title'   => 'Anonymize IP Addresses'
										),
										array(
											'id'      => 'domain',
											'type'    => 'text',
											'default' => '',
											'title'   => 'Cookie Domain Name'
										),
										array(
											'id'      => 'enhancedEcommerce',
											'type'    => 'switcher',
											'default' => 'no',
											'title'   => 'Enable Enhanced Ecommerce'
										),
										array(
											'id'      => 'nameTracker',
											'type'    => 'switcher',
											'default' => 'no',
											'title'   => 'Name Tracker'
										),
										array(
											'id'      => 'nonInteraction',
											'type'    => 'switcher',
											'default' => 'no',
											'title'   => 'Add the non-interaction flag to all events'
										),
										array(
											'id'      => 'optimize',
											'type'    => 'text',
											'default' => '',
											'title'   => 'Optimize Container ID'
										),
										array(
											'id'      => 'sendUserId',
											'type'    => 'switcher',
											'default' => 'no',
											'title'   => 'Send User-ID to GA'
										),
										array(
											'id'      => 'useGoogleAmpClientId',
											'type'    => 'switcher',
											'default' => 'no',
											'title'   => 'Use Google AMP Client ID'
										),
										array(
											'id'      => 'WIP_uncaugh_exceptions',
											'type'    => 'switcher',
											'default' => 'no',
											'title'   => 'Send Uncaught Exceptions to GA (Mobile)'
										),
									),
								),
							)
						)
					)
				),
				//FB
				array(
					'type'    => 'fieldset',
					'id'      => 'Facebook Pixel',
					'title'   => 'Facebook Pixel',
					'options' => array(
						'cols' => 1
					),
					'fields'  => array(
						array(
							'id'   => 'facebook_pixel_switcher',
							'type' => 'switcher'
						),
						array(
							'id'         => 'facebook_pixel_settings',
							'type'       => 'group',
							'dependency' => array(
								'facebook_pixel_switcher',
								'==',
								'true'
							), // check for true/false by field id
							'options'    => array(
								'cols'        => 1,
								'group_title' => 'Facebook Pixel Settings'
							),
							'fields'     => array(
								array(
									'id'         => 'pixelId',
									'type'       => 'text',
									'default'    => '',
									'title'      => 'Facebook Pixel ID',
									'attributes' => array(
										'style' => 'width: 150px;',
									),
								),
								array(
									'id'      => 'connection_settings',
									'type'    => 'group',
									'options' => array(
										'cols'        => 2,
										'group_title' => 'Connection Settings'
									),
									'fields'  => array(
										array(
											'id'      => 'blacklist_properties',
											'type'    => 'group',
											'default' => '',
											'options' => array(
												'cols'        => 2,
												'group_title' => 'Blacklist or Hash PII Properties'
											),
											'fields'  => array(
												array(
													'type'    => 'group',
													'id'      => 'blacklistPiiProperties',
													'title'   => esc_html__( 'Blacklisted/Hashed Properties', 'plugin-name' ),
													'default' => '',
													'options' => array(
														'repeater'     => true,
														'accordion'    => true,
														'button_title' => esc_html__( 'Add property', 'plugin-name' ),
														'group_title'  => esc_html__( '', 'plugin-name' )
													),
													'fields'  => array(
														array(
															'id'         => 'local_property',
															'type'       => 'text',
															'default'    => '',
															'attributes' => array(
																'data-title'  => 'title',
																'placeholder' => esc_html__( 'Trait/property name', 'plugin-name' ),
															),
														),
														array(
															'id'      => 'hash',
															'type'    => 'switcher',
															'default' => 'no',
															'title'   => 'Hash instead of blacklist?'
														),
													),

												),

											),

										),
										array(
											'id'      => 'map_categories',
											'type'    => 'group',
											'options' => array(
												'cols'        => 2,
												'group_title' => 'Map Categories to FB Content Types'
											),
											'fields'  => array(
												array(
													'type'    => 'group',
													'id'      => 'contentTypes',
													'title'   => esc_html__( 'Custom Mappings', 'plugin-name' ),
													'options' => array(
														'repeater'     => true,
														'accordion'    => true,
														'button_title' => esc_html__( 'Add mapping', 'plugin-name' ),
														'group_title'  => esc_html__( '', 'plugin-name' )
													),
													'fields'  => array(
														array(
															'id'         => 'local_category',
															'type'       => 'text',
															'default'    => '',
															'attributes' => array(
																'data-title'  => 'title',
																'placeholder' => esc_html__( 'Your Category', 'plugin-name' ),
															),
														),
														array(
															'id'         => 'fb_category',
															'type'       => 'text',
															'default'    => '',
															'attributes' => array(
																'placeholder' => esc_html__( 'Facebook Content Type', 'plugin-name' ),
															),
														),
													),
												),
											),

										),
										array(
											'id'      => 'map_events',
											'type'    => 'group',
											'options' => array(
												'cols'        => 2,
												'group_title' => 'Map events to Facebook Standard Events'
											),
											'fields'  => array(
												array(
													'type'    => 'group',
													'id'      => 'standardEvents',
													'title'   => esc_html__( 'Custom Mappings', 'plugin-name' ),
													'options' => array(
														'repeater'     => true,
														'accordion'    => true,
														'button_title' => esc_html__( 'Add mapping', 'plugin-name' ),
														'group_title'  => esc_html__( '', 'plugin-name' )
													),
													'fields'  => array(
														array(
															'id'         => 'local_event',
															'type'       => 'text',
															'title'      => 'Your custom event',
															'default'    => '',
															'attributes' => array(
																'data-title'  => 'title',
																'placeholder' => esc_html__( 'Your Event', 'plugin-name' ),
															),
														),
														array(
															'id'             => 'fb_event',
															'title'          => 'Facebook Standard Event',
															'type'           => 'select',
															'default_option' => 'Select',
															'default'        => '',
															'options'        => array(
																'ViewContent'          => 'ViewContent',
																'Search'               => 'Search',
																'AddToCart'            => 'AddToCart',
																'AddToWishlist'        => 'AddToWishlist',
																'AddPaymentInfo'       => 'AddPaymentInfo',
																'Purchase'             => 'Purchase',
																'Lead'                 => 'Lead',
																'CompleteRegistration' => 'CompleteRegistration',
																'Contact'              => 'Contact',
																'CustomizeProduct'     => 'CustomizeProduct',
																'Donate'               => 'Donate',
																'FindLocation'         => 'FindLocation',
																'Schedule'             => 'Schedule',
																'StartTrial'           => 'StartTrial',
																'SubmitApplication'    => 'SubmitApplication',
																'Subscribe'            => 'Subscribe',
															),

														),
													),
												),
											),

										),
										array(
											'id'             => 'valueIdentifier',
											'title'          => 'Value Field Identifier',
											'type'           => 'select',
											'default_option' => 'properties.value',
											'default'        => '',
											'options'        => array(
												'value' => 'properties.value',
												'price' => 'properties.price'
											),

										),
									),
								),
								array(
									'id'      => 'other_settings',
									'type'    => 'group',
									'options' => array(
										'cols'        => 2,
										'group_title' => 'Other Settings'
									),
									'fields'  => array(
										array(
											'id'      => 'initWithExistingTraits',
											'type'    => 'switcher',
											'default' => 'yes',
											'title'   => 'Enable Advanced Matching'

										),
										array(
											'id'      => 'map_pixels',
											'type'    => 'group',
											'options' => array(
												'cols'        => 2,
												'group_title' => 'Legacy Conversion Pixel IDs'
											),
											'fields'  => array(
												array(
													'type'    => 'group',
													'id'      => 'legacyEvents',
													'title'   => esc_html__( 'Legacy Pixel Mappings', 'plugin-name' ),
													'options' => array(
														'repeater'     => true,
														'accordion'    => true,
														'button_title' => esc_html__( 'Add mapping', 'plugin-name' ),
														'group_title'  => esc_html__( '', 'plugin-name' )
													),
													'fields'  => array(
														array(
															'id'         => 'legacy_pixel',
															'type'       => 'text',
															'title'      => 'Legacy Pixel',
															'default'    => '',
															'attributes' => array(
																'data-title'  => 'title',
																'placeholder' => esc_html__( 'Legacy Pixel', 'plugin-name' ),
															),
														),
														array(
															'id'         => 'new_pixel',
															'type'       => 'text',
															'title'      => 'New Pixel',
															'default'    => '',
															'attributes' => array(
																'data-title'  => 'title',
																'placeholder' => esc_html__( 'New Pixel', 'plugin-name' ),
															),
														),

													),
												),
											),

										),
										array(
											'id'      => 'whitelist_properties',
											'type'    => 'group',
											'options' => array(
												'cols'        => 2,
												'group_title' => 'Whitelist Properties'
											),
											'fields'  => array(
												array(
													'type'    => 'group',
													'id'      => 'whitelistPiiProperties',
													'title'   => esc_html__( 'Whitelisted Properties', 'plugin-name' ),
													'options' => array(
														'repeater'     => true,
														'accordion'    => true,
														'button_title' => esc_html__( 'Add property', 'plugin-name' ),
														'group_title'  => esc_html__( '', 'plugin-name' )
													),
													'fields'  => array(
														array(
															'id'         => 'local_property',
															'type'       => 'text',
															'default'    => '',
															'attributes' => array(
																'data-title'  => 'title',
																'placeholder' => esc_html__( 'Trait/property name', 'plugin-name' ),
															),
														),
													),
												),
											),
										),
									),
								),
							)
						)
					)
				),
				//GTM
				array(
					'type'    => 'fieldset',
					'id'      => 'Google Tag Manager',
					'title'   => 'Google Tag Manager',
					'options' => array(
						'cols' => 1
					),
					'fields'  => array(
						array(
							'id'   => 'google_tag_manager_switcher',
							'type' => 'switcher'
						),
						array(
							'id'         => 'google_tag_manager_settings',
							'type'       => 'group',
							'dependency' => array(
								'google_tag_manager_switcher',
								'==',
								'true'
							), // check for true/false by field id
							'options'    => array(
								'cols'        => 1,
								'group_title' => 'Google Tag Manager Settings'
							),
							'fields'     => array(
								array(
									'id'      => 'containerId',
									'type'    => 'text',
									'default' => '',
									'title'   => 'Container ID'
								),
								array(
									'id'      => 'other_settings',
									'type'    => 'group',
									'options' => array(
										'cols'        => 2,
										'group_title' => 'Settings'
									),
									'fields'  => array(
										array(
											'id'      => 'environment',
											'type'    => 'text',
											'default' => '',
											'title'   => 'Environment'
										),
										array(
											'id'    => 'trackNamedPages',
											'type'  => 'switcher',
											'title' => 'Track named pages?'
										),
										array(
											'id'    => 'trackCategorizedPages',
											'type'  => 'switcher',
											'title' => 'Track categorized pages?'
										)
									),
								)
							)
						)
					)
				),
				//AdWords
				array(
					'type'    => 'fieldset',
					'id'      => 'Google Ads',
					'title'   => 'Google Ads',
					'options' => array(
						'cols' => 1
					),
					'fields'  => array(
						array(
							'id'   => 'google_ads_switcher',
							'type' => 'switcher'
						),
						array(
							'id'         => 'google_ads_settings',
							'type'       => 'group',
							'dependency' => array(
								'google_ads_switcher',
								'==',
								'true'
							),
							// check for true/false by field id
							'options'    => array(
								'cols'        => 1,
								'group_title' => 'Google Ads Settings'
							),
							'fields'     => array(
								//Old experience
								array(
									'id'         => 'conversionId',
									'type'       => 'text',
									'default'    => '',
									'title'      => 'Conversion ID',
									'dependency' => array( 'google_ads_new_switcher', '==', 'false' ),
									'attributes' => array(
										'style' => 'width: 150px;',
									)
								),
								array(
									'id'         => 'google_ads_old_settings',
									'type'       => 'group',
									'options'    => array(
										'accordion'    => true,
										'button_title' => esc_html__( 'Add new', 'plugin-name' ),
										'group_title'  => esc_html__( 'Accordion Title', 'plugin-name' ),
										'closed'       => false,
									),
									'dependency' => array( 'google_ads_new_switcher', '==', 'false' ),
									'options'    => array(
										'cols'        => 2,
										'group_title' => 'Google Ads Settings'
									),
									'fields'     => array(
										array(
											'id'         => 'linkId',
											'type'       => 'text',
											'default'    => '',
											'title'      => 'Link ID',
											'attributes' => array(
												'style' => 'width: 150px;',
											),
										),
										array(
											'id'    => 'pageRemarketing',
											'type'  => 'switcher',
											'title' => 'Page Remarketing'
										),
										array(
											'id'    => 'google_ads_old_page_attribution',
											'type'  => 'switcher',
											'title' => 'Track Attribution Data'
										),
										array(
											'id'      => 'event_mappings',
											'type'    => 'group',
											'default' => '',
											'options' => array(
												'cols'        => 2,
												'group_title' => 'Event Mappings'
											),
											'fields'  => array(
												array(
													'type'    => 'group',
													'id'      => 'eventMappings',
													'title'   => esc_html__( 'Mapped Events', 'plugin-name' ),
													'default' => '',
													'options' => array(
														'repeater'     => true,
														'accordion'    => true,
														'button_title' => esc_html__( 'Add event', 'plugin-name' ),
														'group_title'  => esc_html__( '', 'plugin-name' )
													),
													'fields'  => array(
														array(
															'id'         => 'eventName',
															'type'       => 'text',
															'title'      => esc_html__( 'Event Name', 'plugin-name' ),
															'default'    => '',
															'attributes' => array(
																'data-title' => 'title',
															),
														),
														array(
															'id'      => 'label',
															'type'    => 'text',
															'title'   => esc_html__( 'Event Label', 'plugin-name' ),
															'default' => '',
														),
														array(
															'id'      => 'conversionId',
															'title'   => esc_html__( 'Conversion ID', 'plugin-name' ),
															'type'    => 'text',
															'default' => '',
														),
														array(
															'id'      => 'remarketing',
															'type'    => 'switcher',
															'default' => 'no',
															'title'   => 'Send remarketing tag?'
														),
													),
												),

											),

										)
									)
								),
								//Newexperience
								array(
									'id'      => 'google_ads_new_switcher',
									'type'    => 'switcher',
									'default' => 'no',
									'title'   => 'New Google Ads Experience'
								),
								array(
									'id'         => 'conversionIdNew',
									'type'       => 'text',
									'dependency' => array(
										'google_ads_new_switcher',
										'==',
										'true'
									),
									'default'    => '',
									'title'      => 'Conversion ID',
									'attributes' => array(
										'style' => 'width: 150px;',
									)

								),
								array(
									'id'         => 'google_ads_new_click_conversions',
									'type'       => 'group',
									'dependency' => array(
										'google_ads_new_switcher',
										'==',
										'true'
									),
									'default'    => '',
									'options'    => array(
										'cols'        => 2,
										'group_title' => 'Click Conversions'
									),
									'fields'     => array(
										array(
											'type'    => 'group',
											'id'      => 'clickConversions',
											'title'   => esc_html__( 'Click Conversions', 'plugin-name' ),
											'default' => '',
											'options' => array(
												'repeater'     => true,
												'accordion'    => true,
												'button_title' => esc_html__( 'Add event', 'plugin-name' ),
												'group_title'  => esc_html__( '', 'plugin-name' )
											),
											'fields'  => array(
												array(
													'id'         => 'event',
													'type'       => 'text',
													'title'      => esc_html__( 'Event Name', 'plugin-name' ),
													'default'    => '',
													'attributes' => array(
														'data-title' => 'title',
													),
												),
												array(
													'id'      => 'id',
													'type'    => 'text',
													'title'   => esc_html__( 'Event Label', 'plugin-name' ),
													'default' => '',
												),
												array(
													'id'      => 'accountId',
													'title'   => esc_html__( 'Conversion ID', 'plugin-name' ),
													'type'    => 'text',
													'default' => '',
												),
											),
										),

									),

								),
								array(
									'id'         => 'google_ads_new_page_conversions',
									'type'       => 'group',
									'dependency' => array(
										'google_ads_new_switcher',
										'==',
										'true'
									),
									'default'    => '',
									'options'    => array(
										'cols'        => 2,
										'group_title' => 'Page Load Conversions'
									),
									'fields'     => array(
										array(
											'type'    => 'group',
											'id'      => 'pageLoadConversions',
											'title'   => esc_html__( 'Page Conversions', 'plugin-name' ),
											'default' => '',
											'options' => array(
												'repeater'     => true,
												'accordion'    => true,
												'button_title' => esc_html__( 'Add event', 'plugin-name' ),
												'group_title'  => esc_html__( '', 'plugin-name' )
											),
											'fields'  => array(
												array(
													'id'         => 'event',
													'type'       => 'text',
													'title'      => esc_html__( 'Event Name', 'plugin-name' ),
													'default'    => '',
													'attributes' => array(
														'data-title' => 'title',
													),
												),
												array(
													'id'      => 'id',
													'type'    => 'text',
													'title'   => esc_html__( 'Event Label', 'plugin-name' ),
													'default' => '',
												),
												array(
													'id'      => 'accountId',
													'title'   => esc_html__( 'Conversion ID', 'plugin-name' ),
													'type'    => 'text',
													'default' => '',
												),

											),
										),
									),

								),
								array(
									'id'         => 'google_ads_new_other_settings',
									'type'       => 'group',
									'options'    => array(
										'accordion'    => true,
										'button_title' => esc_html__( 'Add new', 'plugin-name' ),
										'group_title'  => esc_html__( 'Accordion Title', 'plugin-name' ),
										'closed'       => false,
									),
									'dependency' => array(
										'google_ads_new_switcher',
										'==',
										'true'
									),
									'options'    => array(
										'cols'        => 2,
										'group_title' => 'Other Settings'
									),
									'fields'     => array(

										array(
											'id'    => 'conversionLinker',
											'type'  => 'switcher',
											'title' => 'Conversion Linker'
										),
										array(
											'id'         => 'defaultPageConversion',
											'type'       => 'text',
											'default'    => '',
											'title'      => 'Default Page Conversion',
											'attributes' => array(
												'style' => 'width: 150px;',
											),
										),
										array(
											'id'    => 'sendPageView',
											'type'  => 'switcher',
											'title' => 'Send Page View'
										)
									)
								),
							)
						)
					)
				),
				//Zapier
				array(
					'type'    => 'fieldset',
					'id'      => 'Zapier',
					'title'   => 'Zapier',
					'options' => array(
						'cols' => 1
					),
					'fields'  => array(
						array(
							'id'   => 'zapier_switcher',
							'type' => 'switcher'
						),
						array(
							'id'         => 'zapier_webhook_url',
							'type'       => 'text',
							'title'      => 'Webhook URL',
							'dependency' => array(
								'zapier_switcher',
								'==',
								'true'
							)
						)
					)
				),
			)
		);
		//USER LEVEL TRACKING
		$fields[] = array(
			'name'        => 'Identify',
			'title'       => 'User Level Tracking',
			'icon'        => 'dashicons-groups',
			'attributes'  => array(
				'cols' => 2
			),
			'description' => 'Identify Calls',
			'fields'      => array(
				/*				array(
									'id'          => 'userid_is_email',
									'type'        => 'switcher',
									'title'       => 'Use email as the User ID instead of the WordPress user ID. Not recommended, not best practice. ',
									'description' => ''
								),*/
				array(
					'id'          => 'included_user_traits',
					'type'        => 'tap_list',
					'title'       => 'Select user traits',
					'description' => 'Select the user traits you want to add to your identify calls.',
					'options'     => $trait_options
				),
				array(
					'id'         => 'chosen_class',
					'type'       => 'select',
					'title'      => 'Advanced',
					'attributes' => array(
						'multiple' => 'multiple',
						'style'    => 'display:none;'
					),
					'class'      => 'chosen',
					'style'      => 'display:none;'
				),
				array(
					'type'    => 'group',
					'id'      => 'custom_user_traits',
					'title'   => esc_html__( 'Custom user traits', 'plugin-name' ),
					'options' => array(
						'repeater'     => true,
						'accordion'    => true,
						'button_title' => esc_html__( 'Add new', 'plugin-name' ),
						'group_title'  => esc_html__( 'Accordion Title', 'plugin-name' ),
						'limit'        => 50,
						'sortable'     => false,
						'mode'         => 'compact'

					),
					'fields'  => array(

						array(
							'id'         => 'custom_user_traits_label',
							'type'       => 'text',
							'prepend'    => 'Trait name',
							'attributes' => array(
								// mark this field az title, on type this will change group item title
								'data-title'  => 'title',
								'placeholder' => esc_html__( 'Trait label', 'plugin-name' )
							),
							'class'      => 'chosen'
						),
						array(
							'id'         => 'custom_user_traits_key',
							'type'       => 'text',
							'prepend'    => 'Meta key',
							'class'      => 'chosen',
							'attributes' => array(
								// mark this field az title, on type this will change group item title
								'data-title'  => 'title',
								'placeholder' => esc_html__( 'Custom field meta key', 'plugin-name' )

							)
						)
					)
				),
				array(
					'id'          => 'use_alias',
					'type'        => 'hidden',
					'title'       => 'Use Alias calls, for Mixpanel for example',
					'description' => ''
				)
			)
		);
		//FILTERING
		$fields[] = array(
			'name'        => 'Filtering',
			'title'       => 'Filtering',
			'icon'        => 'dashicons-filter',
			'description' => 'Filtering your tracking',
			'fields'      => array(
				array(
					'id'      => 'track_wp_admin',
					'type'    => 'switcher',
					'title'   => 'Track wp-admin area?',
					'default' => 'no'
				),
				array(
					'id'          => 'ignored_users',
					'type'        => 'tap_list',
					'title'       => 'User roles to ignore',
					'description' => 'These users won\'t be tracked',
					'options'     => $roles
				),
				array(
					'id'          => 'ignored_categories',
					'type'        => 'tap_list',
					'title'       => 'Categories to ignore',
					'description' => 'Post categories to ignore',
					'options'     => $categories
				),
				array(
					'id'          => 'ignored_post_types',
					'type'        => 'tap_list',
					'title'       => 'Custom post types to ignore',
					'description' => 'Custom post types to ignore',
					'options'     => $post_types
				)
			)
		);
		//Instantiation
		$options_panel = new Exopite_Simple_Options_Framework( $config_submenu, $fields );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/all-in-one-analytics-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

//		$current_user            = wp_get_current_user();
		//	$current_post            = get_post();
		//	$trackable_user          = All_In_One_Analytics::check_trackable_user( $current_user );
		//	$trackable_post_type     = All_In_One_Analytics::check_trackable_post( $current_post );
//		$tracking_settings_array = All_In_One_Analytics::get_analytics_settings();
//		$analytics_url           = plugin_dir_url( __FILE__ ) . 'js/analytics/analytics.min.js';
//		if ( $trackable_user === true && $trackable_post_type === true ) {
		//		wp_enqueue_script( 'js.cookie.js', plugin_dir_url( __FILE__ ) . 'js/js.cookie.js', array( 'jquery' ), $this->version, false );
		/*			wp_enqueue_script( 'async.analytics.js', plugin_dir_url( __FILE__ ) . 'js/analytics/async.analytics.js', array( 'jquery' ), $this->version, false );
					wp_localize_script( 'async.analytics.js', 'settingsAIO', array(
						'init'         => ( 'init' ),
						'analyticsUrl' => $analytics_url,
						'settings'     => $tracking_settings_array,
					) );*/
		//		wp_enqueue_script( 'analytics.js', plugin_dir_url( __FILE__ ) . 'js/analytics/analytics.min.js', array( 'jquery' ), $this->version, false );
//			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/all-in-one-analytics-admin.js', array( 'jquery' ), $this->version, true );
//		}


	}

	/**
	 * Example daily event.
	 */
	public function run_hourly_event() {
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		// Check for orphaned entries in the database and delete them
		// Anything there more than 30 mins old should be ok to delete

		global $wpdb;
		$table   = $wpdb->prefix . 'all_in_one_analytics';
		$sql     = "SELECT ID FROM `" . $wpdb->prefix . "all_in_one_analytics` WHERE time  < (NOW() - INTERVAL 30 MINUTE)";
		$results = $wpdb->get_results( $sql, ARRAY_A );
		foreach ( $results as $key => $value ) {
			$id = $value["ID"];
			$wpdb->delete( $table, array( 'id' => $id ) );
		}
	}

}