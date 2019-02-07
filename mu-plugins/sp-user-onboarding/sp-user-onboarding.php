<?php
/*
Plugin Name: StudioPress User Onboarding

Description: A simple user onboarding for new StudioPress Sites customers

Author: Rainmaker Digital, LLC.
Author URI: http://rainmakerdigital.com/

Version: 0.1

Text Domain: sp-user-onboarding
Domain Path: /languages

License: GNU General Public License v2.0 (or later)
License URI: http://www.opensource.org/licenses/gpl-license.php
*/

class SP_User_Onboarding {

	/**
	 * Plugin version
	 */
	public $plugin_version = '0.1';

	/**
	 * The plugin textdomain, for translations.
	 */
	public $plugin_textdomain = 'sp-user-onboarding';

	/**
	 * The url to the plugin directory.
	 */
	public $plugin_dir_url;

	/**
	 * The path to the plugin directory.
	 */
	public $plugin_dir_path;

	/**
	 * Admin menu and settings page.
	 */
	public $admin;

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->plugin_dir_url  = plugin_dir_url( __FILE__ );
		$this->plugin_dir_path = plugin_dir_path( __FILE__ );

	}

	/**
	 * Initialize.
	 */
	public function init() {

		if ( current_user_can( 'install_plugins' ) && ( defined('IS_SYNTHESIS') && ! IS_SYNTHESIS ) ) {

			// First visit?
			if( ( ! get_option( 'sp-onboarding-hide' ) ) || isset( $_GET['sp-onboarding'] ) ) {

				// Save option to not show this popup again
				add_option( 'sp-onboarding-hide' , 1 );

				// Load stylesheet and JS scripts
				add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts_enqueue' ) );

			}

		}

	}

	/**
	 * Load stylesheet and JS scripts in the admin.
	 */
	function admin_scripts_enqueue() {

		echo '<script>var spOnboradingWPVersion = \'' . get_bloginfo('version') . '\';</script>';

		wp_enqueue_style( 'sp-user-onboarding-styles',  plugin_dir_url( __FILE__ ) . 'css/style.css' );
		wp_enqueue_script( 'sp-user-onboarding-scripts',  plugin_dir_url( __FILE__ ) . 'js/scripts.js', array( 'jquery' ) );

	}

}

/**
 * Helper function to retrieve the static object without using globals.
 */
function SP_User_Onboarding() {

	static $object;

	if ( null == $object ) {
		$object = new SP_User_Onboarding;
	}

	return $object;
}
/**
 * Initialize the object on	`plugins_loaded`.
 */
add_action( 'plugins_loaded', array( SP_User_Onboarding(), 'init' ) );
