<?php

/**
 * The theme purchase admin page.
 */
class SP_Purchase_Themes_Admin {

	public $pagehook;

	public $ajax_action = 'sp_theme_install';

	public $endpoint = 'https://rabbitmq.spsites.net:8022/api/1.0/site/addsptheme?server=%s&domain=%s&theme=%s';

	public $endpoint_user = 'sphaddons';

	public $endpoint_pass = '9Eq8pq76P8Br1ZU2LrcK7ky7VB68D965o';

	public function __construct() {

		if ( IS_SYNTHESIS ) {
			$this->endpoint_user = 'wsynthaddons';
			$this->endpoint = str_replace( '.spsites.', '.wsynth.', $this->endpoint );
		}

		// Test server params
		if ( in_array( gethostname(), array( 'spe00-1.spsites.net', 'spe00-1.spsites.net', 'sve00.spsites.net', 'rmqbus01.wsynth.net', 'wpversions.wsynth.net' ) ) ) {
			$this->endpoint        = str_replace( 'rabbitmq', 'rabbitmqtest', $this->endpoint );
			$this->endpoint_user   = ( IS_SYNTHESIS ) ? 'wsynthstaddons' : 'spstaddons';
			$this->endpoint_pass   = '48HFalOMI4SsH3BqVWuk2434PBy9sx2FI';
		}

		$this->page_id = 'sp-themes';

		$this->menu_ops = array(
			'submenu' => array(
				'parent_slug' => 'sp-software-monitor',
				'page_title'  => __( 'StudioPress Themes', 'sp-purchase' ),
				'menu_title'  => __( 'StudioPress Themes', 'sp-purchase' ),
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

		$theme_install = $this->create_theme_install_task( $_REQUEST['slug'] );

		// If task was queued successfully...
		if ( $theme_install ) {

			// Check status up to 3 times
			$i = 1;
			while ( $i < 10 ) {

				sleep( 1 );

				if ( file_exists( get_theme_root() . '/' . sanitize_title_with_dashes( $_REQUEST['slug'] ) . '/style.css' ) ) {
					die( '1' );
				}

				$i++;

			}

		}

		die( '0' );

	}

	/**
	 * Use API to create theme install task. Returns false if error, task ID if success.
	 */
	public function create_theme_install_task( $theme ) {

		$domain = wp_parse_url( home_url() );

		if ( is_array( $domain ) ) {
			$domain = ( isset( $domain['path'] ) ) ? $domain['host'] . $domain['path'] : $domain['host'];
		}

		$post_url = sprintf(
			$this->endpoint,
			gethostname(), // Server
			$this->strip_www( $domain ), // Domain
			$theme
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

		add_thickbox();
		wp_enqueue_script( 'theme-preview' );

		wp_enqueue_script( 'sp-theme-install', SP_Purchase()->plugin_dir_url . 'assets/js/sp-theme-install.js', array( 'jquery' ), SP_Purchase()->plugin_version, true );
		wp_localize_script( 'sp-theme-install', $this->ajax_action, array(
			'nonce'           => wp_create_nonce( $this->ajax_action . '_nonce' ),
			'notice_success'  => __( 'Installed', 'sp-purchase' ),
			'notice_failure'  => __( 'Something went wrong. Please try again, or contact support.', 'sp-purchase' ),
			'installing_text' => __( 'Installing...', 'sp-purchase' ),
		) );

	}

	public function admin() {

		require_once( SP_Purchase()->plugin_dir_path . 'includes/views/purchase-themes-admin.php' );

	}

	public function strip_www( $domain ) {

		return preg_replace( '#^www\.(.+\.)#i', '$1', $domain );

	}

}
