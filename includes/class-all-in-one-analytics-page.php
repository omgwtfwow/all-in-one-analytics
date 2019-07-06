<?php

class All_In_One_Analytics_Page {

	private $plugin_name;

	private $version;


	/**
	 * All_In_One_Analytics_Page constructor.
	 *
	 * @param $plugin_name
	 * @param $version
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Render the js page call
	 */
	function render_page_call() {

		$current_user   = wp_get_current_user();
		$current_post   = get_post();
		$trackable_user = All_In_One_Analytics::check_trackable_user( $current_user );
		$trackable_post = All_In_One_Analytics::check_trackable_post( $current_post );
		if ( $trackable_user === false || $trackable_post === false ) {
			//not trackable or not logged in

			return;
		}

		?>

        <script type="text/javascript">
			analytics.page();
        </script>
		<?php

	}

}
