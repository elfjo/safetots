<?php
/*
Plugin Name: StudioPress Affiliates

Description: Filter affiliate links and IDs to inject StudioPress affiliate links.
Author: Rainmaker Digital, LLC.
Author URI: http://rainmakerdigital.com/

Version: 0.9.0

Text Domain: sp-affiliates
Domain Path: /languages

License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*

/**
 * The main class.
 *
 * @since 0.9.0
 */
class SP_Affiliates {

	/**
	 * Plugin version
	 */
	public $plugin_version = '0.9.0';

	/**
	 * The plugin textdomain, for translations.
	 */
	public $plugin_textdomain = 'sp-affiliates';

	/**
	 * The url to the plugin directory.
	 */
	public $plugin_dir_url;

	/**
	 * The path to the plugin directory.
	 */
	public $plugin_dir_path;

	/**
	 * Rainmaker Digital's shareasale ID.
	 */
	public $sas_id = '1422621';

	/**
	 * Get shareasale ID.
	 */
	public function get_sas_id() {
		return $this->sas_id;
	}

	/**
	 * Initialize.
	 *
	 * @since 0.9.0
	 */
	public function init() {

		$this->plugin_dir_url	= plugin_dir_url( __FILE__ );
		$this->plugin_dir_path  = plugin_dir_path( __FILE__ );

		// WPForms Affiliate ID
		if ( ! defined( 'WPFORMS_SHAREASALE_ID' ) ) {
			define( 'WPFORMS_SHAREASALE_ID', $this->sas_id );
		}

		// Soliloquy Affiliate ID
		if ( ! defined( 'SOLILOQUY_SHAREASALE_ID' ) ) {
			define( 'SOLILOQUY_SHAREASALE_ID', $this->sas_id );
		}

		// OptinMonster Affiliate ID
		if ( ! defined( 'OPTINMONSTER_SAS_ID' ) ) {
			define( 'OPTINMONSTER_SAS_ID', $this->sas_id );
		}

		$this->filters();

	}

	public function filters() {

		// Beaver Builder affiliate link
		add_filter( 'fl_builder_store_url', array( $this, 'beaver_builder_affiliate_link' ) );

		// Ninja Forms affiliate ID
		add_filter( 'ninja_forms_affiliate_id', array( $this, 'get_sas_id' ) );

	}

	public function beaver_builder_affiliate_link( $url ) {

		return 'https://www.wpbeaverbuilder.com/?fla=1101';

	}

}

/**
 * Helper function to retrieve the static object without using globals.
 *
 * @since 0.9.0
 */
function SP_Affiliates() {

	static $object;

	if ( null == $object ) {
		$object = new SP_Affiliates;
	}

	return $object;

}

/**
 * Initialize the object on	`plugins_loaded`.
 */
add_action( 'plugins_loaded', array( SP_Affiliates(), 'init' ) );
