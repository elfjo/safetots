<?php
/*
Plugin Name: Genesis Simple Schema
Plugin URI: https://github.com/copyblogger/genesis-simple-schema

Description: A simple way to enable advanced Schema control in Genesis

Author: StudioPress
Author URI: http://www.studiopress.com/

Version: 0.9.1

Text Domain: genesis-simple-schema
Domain Path: /languages/

License: GNU General Public License v2.0 (or later)
License URI: http://www.opensource.org/licenses/gpl-license.php
*/


class Genesis_Simple_Schema {

	/**
	 * Plugin version.
	 */
	public $plugin_version = '0.9.1';

	/**
	 * Minimum WordPress version
	 */
	public $min_wp_version = '4.8';

	/**
	 * Minimum Genesis version
	 */
	public $min_genesis_version = '2.5';

	/**
	 * The plugin textdomain, for translations.
	 */
	public $plugin_textdomain = 'genesis-simple-schema';

	/**
	 * Plugin directory path.
	 */
	public $plugin_dir_path;

	/**
	 * Plugin directory URL.
	 */
	public $plugin_dir_url;

	/**
	 * Genesis Simple Schema Admin object.
	 *
	 * @since 0.9.0
	 */
	public $admin;

	/**
	 * Genesis Simple Schema Markup object.
	 *
	 * @since 0.9.0
	 */
	public $markup;

	/**
	 * Genesis Simple Schema Metaboxes object.
	 *
	 * @since 0.9.0
	 */
	public $metaboxes;

	/**
	 * Supported schema types.
	 *
	 * 0.9.0
	 */
	public $supported_schema_types = array(
		'CreativeWork',
		'Article',
		'BlogPosting',
	);

	public function __construct() {

		$this->plugin_dir_url  = plugin_dir_url( __FILE__ );
		$this->plugin_dir_path = plugin_dir_path( __FILE__ );

	}

	/**
	 * Initialize, hook and execute class methods.
	 *
	 * @since 0.9.0
	 */
	public function init() {

		$this->load_plugin_textdomain();

		$this->post_type_support();

		//add_action( 'admin_notices', array( $this, 'requirements_notice' ) );
		add_action( 'genesis_setup', array( $this, 'instantiate' ) );

	}

	/**
	 * Load the plugin textdomain, for translation.
	 *
	 * @since 0.9.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( $this->plugin_textdomain, false, plugin_basename( dirname( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Checks to see if Genesis and WordPress versions meet mimumum requirements to run this plugin.
	 *
	 * @since 0.9.1
	 */
	public function meets_min_reqs() {

		// Minimum Genesis version requirement
		if ( ! defined( 'PARENT_THEME_VERSION' ) || ! version_compare( PARENT_THEME_VERSION, $this->min_genesis_version, '>=' ) ) {
			return false;
		}

		global $wp_version;

		// Minimum WordPress version requirement
		if ( ! $wp_version || ! version_compare( $wp_version, $this->min_wp_version, '>=' ) ) {
			return false;
		}

		return true;

	}

	/**
	 * Show admin notice if minimum requirements aren't met.
	 *
	 * @since 0.9.1
	 */
	public function requirements_notice() {

		if ( ! $this->meets_min_reqs() ) {

			$plugin = get_plugin_data( __FILE__ );

			$message = sprintf( __( '%s requires WordPress %s and <a href="%s" target="_blank">Genesis %s</a>, or greater. To use this plugin, please upgrade/install the latest version of WordPress and Genesis.', 'genesis-simple-schema' ), $plugin['Name'], $this->min_wp_version, 'http://my.studiopress.com/?download_id=91046d629e74d525b3f2978e404e7ffa', $this->min_genesis_version );
			echo '<div class="notice notice-warning"><p>' . $message . '</p></div>';

		}

	}

	/**
	 * Add necessary post type support.
	 *
	 * @since 0.9.0
	 */
	public function post_type_support() {

		add_post_type_support( 'post', 'genesis-simple-schema' );

	}

	/**
	 * Create the objects, assign to variables as part of the main class object.
	 *
	 * @since 0.9.0
	 */
	public function instantiate() {

		// Do nothing if minimum version requirements not met.
		if ( ! $this->meets_min_reqs() ) {
			return;
		}

		#require_once( $this->plugin_dir . 'includes/class-genesis-schema-admin.php' );
		#$this->admin = new Genesis_Schema_Admin;

		require_once( $this->plugin_dir_path . 'includes/class-genesis-schema-markup.php' );
		$this->markup = new Genesis_Schema_Markup;

		require_once( $this->plugin_dir_path . 'includes/class-genesis-schema-metaboxes.php' );
		$this->metaboxes = new Genesis_Schema_Metaboxes;

	}

	/**
	 * Return an array of supported schema types.
	 *
	 * @since 0.9.0
	 */
	public function get_supported_schema_types() {

		return $this->supported_schema_types;

	}

}

add_action( 'plugins_loaded', array( Genesis_Simple_Schema(), 'init' ) );
function Genesis_Simple_Schema() {

	static $_genesis_simple_schema = null;

	if ( null == $_genesis_simple_schema ) {
		$_genesis_simple_schema = new Genesis_Simple_Schema;
	}

	return $_genesis_simple_schema;

}
