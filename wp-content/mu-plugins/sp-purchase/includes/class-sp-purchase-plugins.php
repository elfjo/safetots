<?php

/**
 * The plugin purchase admin page.
 */
class SP_Purchase_Plugins_Admin {

	public $pagehook;

	public $ajax_action = 'sp_plugin_install';

	public $endpoint = 'https://rabbitmq.spsites.net:8022/api/1.0/site/addspplugin?server=%s&domain=%s&plugin=%s';

	public $endpoint_user = 'sphaddons';

	public $endpoint_pass = '9Eq8pq76P8Br1ZU2LrcK7ky7VB68D965o';

	public function __construct() {

		// Test server params
		if ( in_array( gethostname(), array( 'spe00-1.spsites.net', 'spe00-1.spsites.net', 'sve00.spsites.net' ) ) ) {

			$this->endpoint      = str_replace( 'rabbitmq', 'rabbitmqtest', $this->endpoint );
			$this->endpoint_user = 'spstaddons';
			$this->endpoint_pass = '48HFalOMI4SsH3BqVWuk2434PBy9sx2FI';

		}

		$this->page_id = 'sp-plugins';

		$this->menu_ops = array(
			'submenu' => array(
				'parent_slug' => 'sp-software-monitor',
				'page_title'  => __( 'Recommended Partner Plugins', 'sp-purchase' ),
				'menu_title'  => __( 'Partner Plugins', 'sp-purchase' ),
			),
		);

		add_action( 'admin_menu', array( $this, 'create' ) );

		$this->ajax_setup();

	}

	public function create() {

		$menu = wp_parse_args(
			$this->menu_ops['submenu'],
			array(
				'parent_slug' => '',
				'page_title'  => '',
				'menu_title'  => '',
				'capability'  => 'edit_theme_options',
			)
		);

		$this->pagehook = add_submenu_page( $menu['parent_slug'], $menu['page_title'], $menu['menu_title'], $menu['capability'], $this->page_id, array( $this, 'admin' ) );

		add_action( "load-{$this->pagehook}", array( $this, 'scripts' ) );

	}

	public function ajax_setup() {

		add_action( 'wp_ajax_' . $this->ajax_action, array( $this, 'ajax_action_callback' ) );

	}

	public function ajax_action_callback() {

		if ( check_ajax_referer( $this->ajax_action, 'nonce', false ) ) {
			die( '0' );
		}

		$plugin_install = $this->create_plugin_install_task( $_REQUEST['slug'] );

		// If task was queued successfully...
		if ( $plugin_install ) {

			// Check status up to 5 times
			$i = 1;
			while ( $i < 5 ) {

				sleep( 1 );

				if ( file_exists( WP_PLUGIN_DIR . '/' . $_REQUEST['path'] ) ) {
					die( '1' );
				}

				$i++;

			}

		}

		die( '0' );

	}

	/**
	 * Use API to create plugin install task. Returns false if error, task ID if success.
	 */
	public function create_plugin_install_task( $plugin ) {

		$domain = wp_parse_url( home_url() );

		if ( is_array( $domain ) ) {
			$domain = ( isset( $domain['path'] ) ) ? $domain['host'] . $domain['path'] : $domain['host'];
		}

		$post_url = sprintf(
			$this->endpoint,
			//'spe00-1.spsites.net',
			gethostname(), // Server
			$this->strip_www( $domain ), // Domain
			$plugin // Theme/plugin slug
		);

		$post = wp_remote_post( $post_url, array(
			'headers' => array(
				'Authorization' => 'Basic ' . base64_encode( $this->endpoint_user . ':' . $this->endpoint_pass ),
			),
		) );

		if ( is_wp_error( $post ) ) {
			return false;
		}

		$response = json_decode( $post['body'], true );

		if ( 'ok' == $response['status'] ) {
			return $response['request_id'];
		}

	}

	public function scripts() {

		wp_enqueue_script( 'sp-plugin-install', SP_Purchase()->plugin_dir_url . 'assets/js/sp-plugin-install.js', array( 'jquery' ), SP_Purchase()->plugin_version, true );
		wp_localize_script( 'sp-plugin-install', $this->ajax_action, array(
			'nonce'           => wp_create_nonce( $this->ajax_action . '_nonce' ),
			'notice_success'  => __( 'Installed', 'sp-purchase' ),
			'notice_failure'  => __( 'Something went wrong. Please try again, or contact support.', 'sp-purchase' ),
			'installing_text' => __( 'Installing...', 'sp-purchase' ),
		) );

	}

	public function admin() {

		require_once( SP_Purchase()->plugin_dir_path . 'includes/views/purchase-plugins-admin.php' );

	}

	public function strip_www( $domain ) {

		return preg_replace( '#^www\.(.+\.)#i', '$1', $domain );

	}

}
