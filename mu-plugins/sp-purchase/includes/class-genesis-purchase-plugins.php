<?php

/**
 * The plugin purchase admin page.
 */
class Genesis_Purchase_Plugins_Admin {

	public $pagehook;

	public $ajax_action = 'genesis_plugin_install';

	public function __construct() {

		$this->page_id = 'genesis-plugins';

		$this->menu_ops = array(
			'submenu' => array(
				'parent_slug' => 'sp-software-monitor',
				'page_title'  => __( 'Recommended Genesis Plugins', 'sp-purchase' ),
				'menu_title'  => __( 'Genesis Plugins', 'sp-purchase' ),
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

		$slug = $_REQUEST['slug'];

		$plugin_install = $this->create_plugin_install_task( $slug );

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
	 * Use WP Plugin Install API to create plugin install task.
	 */
	public function create_plugin_install_task( $slug ) {

		include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' ); //for plugins_api..

		$api = plugins_api( 'plugin_information', array( 'slug' => $slug ) );

		if ( is_wp_error( $api ) ) {
			return false;
		}

		//Included to use the WP Plugin Install API.
		include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );

		$upgrader = new Plugin_Upgrader();
		$upgrader->install( $api->download_link );

		return true;

	}

	public function scripts() {

		wp_enqueue_script( 'genesis-plugin-install', SP_Purchase()->plugin_dir_url . 'assets/js/genesis-plugin-install.js', array( 'jquery' ), SP_Purchase()->plugin_version, true );
		wp_localize_script( 'genesis-plugin-install', $this->ajax_action, array(
			'nonce'           => wp_create_nonce( $this->ajax_action . '_nonce' ),
			'notice_success'  => __( 'Installed', 'sp-purchase' ),
			'notice_failure'  => __( 'Something went wrong. Please try again, or contact support.', 'sp-purchase' ),
			'installing_text' => __( 'Installing...', 'sp-purchase' ),
		) );

	}

	public function admin() {

		require_once( SP_Purchase()->plugin_dir_path . 'includes/views/purchase-genesis-plugins-admin.php' );

	}

}
