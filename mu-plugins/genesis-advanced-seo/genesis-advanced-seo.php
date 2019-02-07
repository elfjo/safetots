<?php
/*
Plugin Name: Genesis Advanced SEO
Plugin URI: https://github.com/copyblogger/genesis-advanced-seo

Description: A plugin for the Genesis Framework to add advanced SEO features.

Author: StudioPress
Author URI: http://www.studiopress.com/

Version: 0.9.1

Text Domain: genesis-advanced-seo
Domain Path: /languages

License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

/**
 * Main Genesis Advanced SEO plugin class.
 *
 * @since 0.9.0
 */
class Genesis_Advanced_SEO {

	/**
	 * Plugin version
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
	public $plugin_textdomain = 'genesis-advanced-seo';

	/**
	 * The url to the plugin directory.
	 */
	public $plugin_dir_url;

	/**
	 * The path to the plugin directory.
	 */
	public $plugin_dir_path;

	/**
	 * The object for the settings page.
	 */
	public $admin;

	/**
	 * The object for the entry metaboxes.
	 */
	public $entry_metaboxes;

	/**
	 * The object for handling Open Graph.
	 */
	public $open_graph;

	/**
	 * The object for creating the Sitemap.
	 */
	public $sitemap;

	/**
	 * The object for generating robots.txt.
	 */
	public $robots;

	/**
	 * The object for ascyning JS files.
	 */
	public $js_async;

	/**
	 * Initialize.
	 *
	 * @since 0.9.0
	 */
	public function init() {

		$this->plugin_dir_url  = plugin_dir_url( __FILE__ );
		$this->plugin_dir_path = plugin_dir_path( __FILE__ );

		//add_action( 'admin_notices', array( $this, 'requirements_notice' ) );

		add_action( 'genesis_setup', array( $this, 'genesis_compat' ) );
		add_action( 'genesis_setup', array( $this, 'instantiate' ) );

	}

	/**
	 * Show admin notice if minimum requirements aren't met.
	 *
	 * @since 0.9.0
	 */
	public function requirements_notice() {

		if ( ! defined( 'PARENT_THEME_VERSION' ) || ! version_compare( PARENT_THEME_VERSION, $this->min_genesis_version, '>=' ) ) {

			$plugin = get_plugin_data( __FILE__ );

			$action = defined( 'PARENT_THEME_VERSION' ) ? __( 'upgrade to', 'genesis-advanced-seo' ) : __( 'install and activate', 'genesis-advanced-seo' );

			$message = sprintf( __( '%s requires WordPress %s and <a href="%s" target="_blank">Genesis %s</a>, or greater. Please %s the latest version of Genesis to use this plugin.', 'genesis-advanced-seo' ), $plugin['Name'], $this->min_wp_version, 'http://my.studiopress.com/?download_id=91046d629e74d525b3f2978e404e7ffa', $this->min_genesis_version, $action );
			echo '<div class="notice notice-warning"><p>' . $message . '</p></div>';

		}

	}

	/**
	 * Genesis compatibility method.
	 *
	 * Ensures compatibility with the included SEO features of the Genesis framework.
	 *
	 * @since 0.9.0
	 */
	public function genesis_compat() {

		// Stop if Genesis minimum version requirement isn't met
		if ( version_compare( PARENT_THEME_VERSION, $this->min_genesis_version, '<' ) ) {
			return;
		}

		// Stop of SEO plugin detected
		if ( genesis_detect_seo_plugins() ) {
			return;
		}

		remove_theme_support( 'genesis-seo-settings-menu' );
		remove_action( 'admin_menu', 'genesis_add_inpost_seo_box' );
		remove_action( 'save_post', 'genesis_inpost_seo_save', 1, 2 );

	}

	public function instantiate() {

		// Stop if Genesis minimum version requirement isn't met
		if ( version_compare( PARENT_THEME_VERSION, $this->min_genesis_version, '<' ) ) {
			return;
		}

		// Stop of SEO plugin detected
		if ( genesis_detect_seo_plugins() ) {
			return;
		}

		add_action( 'genesis_admin_menu', array( $this, 'seo_settings' ) );
		add_action( 'admin_menu', array( $this, 'add_seo_settings_menu' ), 11 );

		require_once( $this->plugin_dir_path . 'includes/class-genesis-seo-entry-metaboxes.php' );
		$this->entry_metaboxes = new Genesis_SEO_Entry_Metaboxes;

		require_once( $this->plugin_dir_path . 'includes/class-genesis-seo-breadcrumb.php' );
		$this->entry_metaboxes = new Genesis_SEO_Breadcrumb;

		require_once( $this->plugin_dir_path . 'includes/class-genesis-seo-open-graph.php' );
		$this->open_graph = new Genesis_SEO_Open_Graph;

		require_once( $this->plugin_dir_path . 'includes/class-genesis-seo-sitemap.php' );
		$this->sitemap = new Genesis_SEO_Sitemap;

		require_once( $this->plugin_dir_path . 'includes/class-genesis-seo-permalink.php' );
		$this->permalink = new Genesis_SEO_Permalink;

		require_once( $this->plugin_dir_path . 'includes/class-genesis-seo-robots.php' );
		$this->robots = new Genesis_SEO_Robots;

		require_once( $this->plugin_dir_path . 'includes/class-genesis-seo-js-async.php' );
		$this->js_ascyn = new Genesis_SEO_JS_Async;

		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			require_once( $this->plugin_dir_path . 'includes/class-genesis-seo-cli-command.php' );
			WP_CLI::add_command( 'genesis-seo', 'Genesis_SEO_CLI_Command' );
		}

	}

	public function seo_settings() {

		$this->admin = new Genesis_Admin_SEO_Settings;

	}

	public function add_seo_settings_menu() {

		global $submenu;

		$submenu['sp-software-monitor'][] = array(
			__( 'SEO Settings', 'genesis-advanced-seo' ),
			'edit_theme_options',
			esc_url( admin_url( 'admin.php?page=seo-settings' ) ),
		);

	}

	public function move_seo_settings_menu() {

		global $menu, $submenu;

		foreach ( (array) $submenu['genesis'] as $key => $value ) {
			if ( 'seo-settings' == $value[2] ) {
				$submenu['sp-software-monitor'][] = $submenu['genesis'][ $key ];
				unset( $submenu['genesis'][ $key ] );
			}
		}

	}

}

add_action( 'plugins_loaded', array( Genesis_Advanced_SEO(), 'init' ) );
function Genesis_Advanced_SEO() {

	static $_genesis_advanced_seo;

	if ( null == $_genesis_advanced_seo ) {
		$_genesis_advanced_seo = new Genesis_Advanced_SEO;
	}

	return $_genesis_advanced_seo;

}
