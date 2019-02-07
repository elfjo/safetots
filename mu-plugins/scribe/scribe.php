<?php

/**
* Plugin Name: Synthesis Managed Scribe
* Version: 1.0.0
* Description: Loads a managed implementation of Scribe
* Plugin Author: CopyBlogger Media
* Plugin URL: http://websynthesis.com
*/

if ( ! class_exists( 'Synthesis_Scribe_Loader' ) ) :

    class Synthesis_Scribe_Loader {
        /**
        * The Synthesis provided Scribe API key.
        *
        * @since 1.0.0
        *
        * @var string Scribe API key
        */
        private $api_key = false;

        /**
        * CSS selectors for hiding research buttons in post edit screen.
        *
        * @since 1.0.0
        *
        * @var array CSS selectors
        */
        private $button_selectors = array();

        function __construct() {
            add_action( 'plugins_loaded', array( $this, 'load_scribe' ) );
        }

        function load_scribe() {

            if ( class_exists( 'Scribe_SEO' ) || class_exists( 'Scribe_Data' ) ) {
                return;
            }

            // ensure the constants are set for our folders
            define( 'SCRIBE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
            define( 'SCRIBE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

            // substitute synthesis managed settings
            add_filter( 'pre_option__ecordia_settings', '__return_zero' );
            add_filter( 'option__scribe_settings', array( $this, 'scribe_settings_filter' ), 1 );
            add_filter( 'pre_update_option__scribe_settings', array( $this, 'update_scribe_settings' ), 1, 2 );

            define( 'SCRIBE_IS_MANAGED', true );

            if ( is_file( SCRIBE_PLUGIN_DIR . 'scribe-class.php' ) ) {
                require_once( SCRIBE_PLUGIN_DIR . 'scribe-class.php' );
            }

            add_action( 'admin_footer-post.php', array( $this, 'admin_footer' ) );
            add_action( 'admin_footer-post-new.php', array( $this, 'admin_footer' ) );
            add_filter( 'http_response', array( $this, 'cache_account_info' ), 10, 3 );

        }

        function scribe_settings_filter( $default ) {
            $this->api_key = get_site_option( 'synthesis_scribe_api_key' );

            if ( ! is_array( $default ) ) {
                $default = array();
            }

            $default['api-key'] = $this->api_key;

            return $default;

        }

        function update_scribe_settings( $newvalue, $oldvalue ) {

            // remove our filter so we get the last saved value
            remove_filter( 'option__scribe_settings', array( $this, 'scribe_settings_filter' ), 1 );
            $settings = get_option( '_scribe_settings' );

            add_filter( 'option__scribe_settings', array( $this, 'scribe_settings_filter' ), 1 );

            return $newvalue;

        }

        function cache_account_info( $response, $args, $url ) {

            $account_path = 'membership/user/detail/';

            if ( strpos( $url, $account_path ) === false ) {
                return $response;
            }

            $url = wp_parse_args( $url );
            $apikey = current( $url );

            if ( $apikey != get_site_option( 'synthesis_scribe_api_key' ) ) {
                return $response;
            }

            if ( is_wp_error( $response ) || ! isset( $response['body'] ) ) {
                return $response;
            }

            $body = str_replace( '-INF', 0, $response['body'] );
            $object = @Scribe_API::urldecode_json_decode( $body );

            if ( null !== $object ) {
                update_site_option( 'synthesis_scribe_account', $object );
            }

            return $response;
        }

        function admin_footer() {

            if ( empty( $this->button_selectors ) ) {
                return;
            }

            $selectors = implode( ', ', $this->button_selectors );
            ?>
            <script type="text/javascript">
            //<!--
            jQuery(document).ready(function(){
            jQuery('<?php echo esc_js( $selectors ); ?>').hide().siblings('.scribe-out-of-evals').show();
            });
            //-->
            </script>
            <?php
        }
}

new Synthesis_Scribe_Loader;

endif; // Synthesis_Scribe_Loader
