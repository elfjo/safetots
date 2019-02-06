<?php
/*
Plugin Name: StudioPress Theme/Plugin Purchase

Description: Admin UI for purchasing themes and plugins on StudioPress Sites.
Author: Rainmaker Digital, LLC.
Author URI: http://rainmakerdigital.com/

Version: 0.9.2

Text Domain: sp-purchase
Domain Path: /languages

License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

/**
 * The main class.
 *
 * @since 0.9.0
 */
class SP_Purchase {

	/**
	 * Plugin version
	 */
	public $plugin_version = '0.9.2';

	/**
	 * The plugin textdomain, for translations.
	 */
	public $plugin_textdomain = 'sp-purchase';

	/**
	 * The url to the plugin directory.
	 */
	public $plugin_dir_url;

	/**
	 * The path to the plugin directory.
	 */
	public $plugin_dir_path;

	/**
	 * Themes Admin
	 */
	public $admin_themes;

	/**
	 * Plugins Admin
	 */
	public $admin_plugins;

	/**
	 * Genesis Plugins Admin
	 */
	public $admin_genesis_plugins;

	/**
	 * Initialize.
	 *
	 * @since 0.9.0
	 */
	public function init() {

		$this->plugin_dir_url	= plugin_dir_url( __FILE__ );
		$this->plugin_dir_path = plugin_dir_path( __FILE__ );

		$this->load_plugin_textdomain();
		$this->instantiate();

	}

	/**
	 * Load the plugin textdomain, for translation.
	 *
	 * @since 0.9.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( $this->plugin_textdomain, false, $this->plugin_dir_path . 'languages/' );
	}

	/**
	 * Include the class file, instantiate the classes, create objects.
	 *
	 * @since 0.9.0
	 */
	public function instantiate() {

		$this->admin_instantiate();

	}

	/**
	 * The Genesis admin page API requires hooking to `genesis_menu`.
	 */
	public function admin_instantiate() {

		if ( preg_match ( '/\/genesis\//', trailingslashit( get_template_directory() ) ) && ( ( defined( 'IS_SP_SITES' ) && IS_SP_SITES ) || ( defined( 'IS_SYNTHESIS' ) && IS_SYNTHESIS ) ) ) {
			require_once( $this->plugin_dir_path . 'includes/class-genesis-purchase-plugins.php' );
			$this->admin_genesis_plugins = new Genesis_Purchase_Plugins_Admin;
		}

		if ( false !== array_search('genesis', array_keys( wp_get_themes() ) ) ) {
			require_once( $this->plugin_dir_path . 'includes/class-sp-purchase-themes.php' );
			$this->admin_themes = new SP_Purchase_Themes_Admin;
		}

		if ( defined( 'IS_SP_SITES' ) && IS_SP_SITES ) {
			require_once( $this->plugin_dir_path . 'includes/class-sp-purchase-plugins.php' );
			$this->admin_plugins = new SP_Purchase_Plugins_Admin;
		}

	}

}

/**
 * Helper function to retrieve the static object without using globals.
 *
 * @since 0.9.0
 */
function SP_Purchase() {

	static $object;

	if ( null == $object ) {
		$object = new SP_Purchase;
	}

	return $object;

}

/**
 * Initialize the object on	`plugins_loaded`.
 */
add_action( 'plugins_loaded', array( SP_Purchase(), 'init' ) );
