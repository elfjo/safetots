<?php
/*
Plugin Name: Genesis Mobile Menu Bar
Plugin URI: https://github.com/copyblogger/genesis-mobile-menu-bar

Description: Creates mobile menu location, and outputs a fixed bar on the front end to display. Requires the Genesis Framework.

Author: StudioPress
Author URI: http://www.studiopress.com/

Version: 0.9.2

Text Domain: genesis-mobile-menu-bar
Domain Path: /languages

License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/


class Genesis_Mobile_Menu_Bar {

	/**
	 * Plugin Version
	 */
	var $plugin_version = '0.9.1';

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
	public $plugin_textdomain = 'genesis-mobile-menu-bar';

	/**
	 * Plugin Directory
	 */
	var $plugin_dir_path;

	/**
	 * Plugin URL
	 */
	var $plugin_dir_url;

	/**
	 * Customizer object.
	 *
	 * @since 0.9.0
	 */
	public $customizer;

	/**
	 * Settings field.
	 *
	 * @since 0.9.0
	 *
	 * @var string Settings field.
	 */
	public $settings_field = 'genesis-mobile-menu-bar-settings';

	/**
	 * Debugging flag
	 *
	 * @since 0.9.0
	 */
	public $debug = false;

	/**
	 * Constructor.
	 *
	 * @since 0.9.0
	 */
	public function __construct() {

		$this->plugin_dir_path = plugin_dir_path( __FILE__ );
		$this->plugin_dir_url  = plugin_dir_url( __FILE__ );

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$this->debug = true;
		}

	}


	/**
	 * Initialize.
	 *
	 * @since 0.9.1
	 */
	public function init() {

		$this->load_plugin_textdomain();

		//add_action( 'admin_notices', array( $this, 'requirements_notice' ) );
		add_action( 'genesis_setup'  , array( $this, 'genesis_mobile_menu_bar_init' ) );

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

			$message = sprintf( __( '%s requires WordPress %s and <a href="%s" target="_blank">Genesis %s</a>, or greater. To use this plugin, please upgrade/install the latest version of WordPress and Genesis.', 'genesis-mobile-menu-bar' ), $plugin['Name'], $this->min_wp_version, 'http://my.studiopress.com/?download_id=91046d629e74d525b3f2978e404e7ffa', $this->min_genesis_version );
			echo '<div class="notice notice-warning"><p>' . $message . '</p></div>';

		}

	}

	function genesis_mobile_menu_bar_init() {

		// Do not run unless minimum version requirements are met.
		if ( ! $this->meets_min_reqs() ) {
			return;
		}

		//* Load customizer class
		add_action( 'init', array( $this, 'customizer' ), 15 );

		//* Load Dummy link to customizer
		add_action( 'admin_menu', array( $this, 'add_submenu_page' ), 11 );

		//* Register menu
		add_action( 'after_setup_theme', array( $this, 'register_menu' ), 9 );

		//* Mobile menu classes for the body element
		add_filter( 'body_class', array( $this, 'body_class_genesis_mobile_menu' ) );

		//* Mobile menu output
		add_action( 'genesis_after', array( $this, 'get_mobile_menu' ), 20 );

		//* Register JS/CSS
		add_action( 'wp_enqueue_scripts', array( $this, 'register_styles_scripts' ) );

		//* Enqueue JS/CSS
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ), 99 );

		//* Add conditional skip link
		add_filter( 'genesis_skip_links_output', array( $this, 'add_skip_links' ) );

		//* Modify menu bar id
		add_filter( 'genesis_attr_gmm-bar', array( $this, 'modify_menu_bar_id' ) );

	}

	/**
	 * Load the plugin textdomain, for translation.
	 *
	 * @since 0.9.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'genesis-mobile-menu-bar', false, plugin_basename( dirname( __FILE__ ) ) . '/languages/' );
	}

	public function customizer() {

		if ( ! is_customize_preview() ) {
			return;
		}

		require_once( $this->plugin_dir_path . 'includes/class-genesis-mobile-menu-bar-customizer.php' );
		$this->customizer = new Genesis_Mobile_Menu_Bar_Customizer;

	}

	public function add_submenu_page() {

		global $submenu;

		$submenu['sp-software-monitor'][] = array(
			__( 'Mobile Menu Bar', 'genesis-mobile-menu-bar' ),
			'customize',
			esc_url( admin_url( 'customize.php?autofocus[control]=enable_mobile_menu_bar' ) ),
		);

	}

	/**
	 * Register the mobile menu location
	 *
	 * @since 0.9.0
	 */
	public function register_menu() {

		$menus       = get_theme_support( 'genesis-menus' );
		$mobile_menu = array( 'mobile' => __( 'Mobile Menu', 'genesis-mobile-menu-bar' ) );

		if ( ! $menus ) {
			add_theme_support( 'genesis-menus', $mobile_menu );
		} else {
			add_theme_support( 'genesis-menus', array_merge( $menus[0], $mobile_menu ) );
		}

	}

	/**
	 * Add body classes for genesis-mobile-menu element manipulation.
	 *
	 * @since 0.9.0
	 *
	 * @param array $classes Existing classes.
	 *
	 * @return array Ammended $classes.
	 *
	 */
	function body_class_genesis_mobile_menu( $classes ) {

		if ( wp_is_mobile() ) {
			$theme = wp_get_theme();

			$classes[] = 'gmm-active';

			$classes[] = $theme->stylesheet;
		}

		return $classes;

	}

	/**
	 * Function to call the mobile menu.
	 *
	 * @since 0.9.0
	 */
	public function get_mobile_menu() {

		if ( ! genesis_get_option( 'enable_mobile_menu', $this->settings_field ) || ! wp_is_mobile() ) {
			return;
		}

		include( $this->plugin_dir_path . 'includes/views/menu.php' );

	}

	/**
	 * Register the styles and scripts.
	 *
	 * @since 0.9.0
	 */
	public function register_styles_scripts() {

		$version = $this->debug ? time() : $this->plugin_version;

		wp_register_script( 'genesis-mobile-menu', $this->plugin_dir_url . 'includes/js/genesis-mobile-menu.js', array( 'jquery' ), $version, true );
		wp_register_style( 'genesis-mobile-menu', $this->plugin_dir_url . 'includes/css/gmm-styles.css', array(), $version );

	}

	/**
	 * Enqueue the styles and scripts.
	 *
	 * @since 0.9.0
	 */
	public function enqueue_styles_scripts() {

		if ( ! genesis_get_option( 'enable_mobile_menu', $this->settings_field ) || ! wp_is_mobile() ) {
			return;
		}

		wp_enqueue_script( 'genesis-mobile-menu' );
		wp_enqueue_style( 'genesis-mobile-menu' );

		wp_add_inline_style( 'genesis-mobile-menu', $this->get_inline_css() );

	}

	public function get_inline_css() {

		/**
		 *
		 * Helpfer function for outputting accessible contrast
		 *
		 */
		function gmm_color_contrast( $color ) {

			$hexcolor = str_replace( '#', '', $color );

			$red   = hexdec( substr( $hexcolor, 0, 2 ) );
			$green = hexdec( substr( $hexcolor, 2, 2 ) );
			$blue  = hexdec( substr( $hexcolor, 4, 2 ) );

			$luminosity = ( ( $red * 0.2126 ) + ( $green * 0.7152 ) + ( $blue * 0.0722 ) );

			return ( $luminosity > 128 ) ? '#111111' : '#ffffff';

		}

		/**
		 *
		 * Calculate the color brightness.
		 *
		 */
		function gmm_color_brightness( $color, $op, $change ) {

			$hexcolor = str_replace( '#', '', $color );
			$red      = hexdec( substr( $hexcolor, 0, 2 ) );
			$green    = hexdec( substr( $hexcolor, 2, 2 ) );
			$blue     = hexdec( substr( $hexcolor, 4, 2 ) );

			$luminosity = ( ( $red * 0.2126 ) + ( $green * 0.7152 ) + ( $blue * 0.0722 ) );

			// Force darken if brighten color is the same as color inputted.
			if ( $luminosity > 128 && $op === '+' ) {

				$op = '-';

			}

			if ( '+' !== $op && isset( $op ) ) {
				$red   = max( 0, min( 255, $red - $change ) );
				$green = max( 0, min( 255, $green - $change ) );
				$blue  = max( 0, min( 255, $blue - $change ) );
			} else {
				$red   = max( 0, min( 255, $red + $change ) );
				$green = max( 0, min( 255, $green + $change ) );
				$blue  = max( 0, min( 255, $blue + $change ) );
			}

			$newhex = '#';
			$newhex .= strlen( dechex( $red ) ) === 1 ? '0'.dechex( $red ) : dechex( $red );
			$newhex .= strlen( dechex( $green ) ) === 1 ? '0'.dechex( $green ) : dechex( $green );
			$newhex .= strlen( dechex( $blue ) ) === 1 ? '0'.dechex( $blue ) : dechex( $blue );

			$hexcolor = '#' . $hexcolor;

			return $newhex;

		}

		/**
		 *
		 * Get the defined background color if it exists
		 *
		 */
		$color = genesis_get_option( 'menu_color', $this->settings_field );

		/**
		 *
		 * Add the CSS
		 *
		 */
		$css = '
			.gmm-bar,
			.gmm-links {
				background-color: ' . $color . '
			}

			.gmm-menu-icon a,
			.gmm-menu-icon button,
			.gmm-bar .current-menu-item > a,
			.gmm-bar .gmm-sub-menu .current-menu-item > a:hover,
			.gmm-bar a,
			.gmm-bar a:hover,
			.mobile-menu-js .gmm-nav-menu .menu-item-has-children .gmm-sub-menu-toggle,
			.mobile-menu-js .gmm-nav-menu .menu-item-has-children .gmm-sub-menu-toggle:hover,
			.mobile-menu-js .gmm-nav-menu .menu-item-has-children .gmm-sub-menu-toggle:focus,
			.mobile-menu-js .gmm-nav-menu a,
			.gmm-nav-menu .menu-toggle-close,
			.gmm-nav-menu .menu-toggle-close:hover,
			.gmm-nav-menu .menu-toggle-close:focus,
			.gmm-bar .search input,
			.gmm-menu-icon > a:before,
			.gmm-menu-icon > button:before,
			.menu-toggle-close:before {
				color: ' . gmm_color_contrast( $color ) . '
			}

			.gmm-menu-icon svg,
			.gmm-sub-menu-toggle svg {
				fill: ' . gmm_color_contrast( $color ) . '
			}

			.gmm-bar .search input[type="search"]::-moz-placeholder {
				color: ' . gmm_color_contrast( $color ) . '
			}

			.gmm-bar .search input[type="search"]::-webkit-input-placeholder {
				color: ' . gmm_color_contrast( $color ) . '
			}

			.mobile-menu-js .gmm-nav-menu > .menu-item:first-of-type {
				border-top-color: ' . gmm_color_brightness( $color, '+', 10 ) . '
			}

			.mobile-menu-js .gmm-nav-menu a {
				border-bottom-color: ' . gmm_color_brightness( $color, '+', 10 ) . '
			}

			.mobile-menu-js .gmm-nav-menu a:focus,
			.mobile-menu-js .gmm-nav-menu a:hover {
				background-color: ' . gmm_color_brightness( $color, '+', 10 ) . '
			}

		';

		return $css;

	}

	/**
	 * Modify the menu bar attributes
	 *
	 * @since 0.9.0
	 */
	public function modify_menu_bar_id( $attributes ) {

		$attributes['id'] = 'gmm-menu-bar';

		return $attributes;

	}

	/**
	 * Add skip links to menu bar
	 *
	 * @since 0.9.0
	 */
	public function add_skip_links( $links ) {

		if ( wp_is_mobile() ) {
			$links['gmm-menu-bar'] = __( 'Skip to mobile menu bar', 'genesis-mobile-menu-bar' );
		}

		return $links;

	}


}

/**
 * Helper function to retrieve the static object without using globals.
 *
 * @since 0.9.0
 */
function Genesis_Mobile_Menu_Bar() {

	static $object;

	if ( null == $object ) {
		$object = new Genesis_Mobile_Menu_Bar;
	}

	return $object;

}

/**
 * Initialize the object on `plugins_loaded`.
 */
add_action( 'plugins_loaded', array( Genesis_Mobile_Menu_Bar(), 'init' ) );
