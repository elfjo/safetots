<?php
/*
Plugin Name: StudioPress Clear Cache plugin
Plugin URI:  https//studiopress.com/
Description: Clear cache on StudioPress infraestructure.
Version:     0.0.0
Author:      Marcos A. Schratzenstaller
Author URI:  https://studiopress.com/
*/
if ( ! defined( 'ABSPATH' ) ) exit;

require_once( plugin_dir_path( __FILE__ ) . "sp-config.php" );

class StudioPressClearCache {
    public $plugin_version = "1.0";
    public $domain = null;
    public $timeout = 30;

    public $endpoint = 'https://rabbitmq.spsites.net:8022/api/1.0/';
	public $endpoint_user = 'sphaddons';
	public $endpoint_pass = '9Eq8pq76P8Br1ZU2LrcK7ky7VB68D965o';

    /**
     * Constructor.
     */
    public function __construct() {
        $this->domain = preg_replace('/https?:\/\//', '', get_site_url() );

        if ( in_array( gethostname(), array( 'spe00-1.spsites.net', 'spe00-1.spsites.net', 'sve00.spsites.net' ) ) ) {
            $this->endpoint      = str_replace( 'rabbitmq', 'rabbitmqtest', $this->endpoint );
            $this->endpoint_user = 'spstaddons';
            $this->endpoint_pass = '48HFalOMI4SsH3BqVWuk2434PBy9sx2FI';

        }

        add_action( 'wp_ajax_sp_clear_cache', array( $this, 'ajax_sp_clear_cache' ) );
        add_action( 'wp_sp_clear_cache', array( $this, 'sp_clear_cache' ) );
        add_action( 'init', array( $this, 'init' ) );

    }

    /**
     * Init function.
     */
    public function init() {
	    add_action( 'admin_enqueue_scripts', array( $this, 'sp_add_styles' ) );
        wp_enqueue_script( 'sp-clear-cache', plugins_url( 'js/sp-clear-cache.js', __FILE__ ), array('jquery'), $this->plugin_version, true );
        add_action( 'admin_bar_menu', array( $this, 'sp_admin_bar' ), 100 );
        add_action( 'publish_post', array( $this, 'ajax_sp_clear_cache' ), 10, 2 );
        add_action( 'publish_page', array( $this, 'ajax_sp_clear_cache' ), 10, 2 );
    }

    /**
     * Ajax to run on clear cache.
     */
    public function ajax_sp_clear_cache() {
        wp_schedule_single_event( time() - 10, 'wp_sp_clear_cache' );

        wp_remote_get( $this->domain . "/wp-cron.php" );
        //wp_die();
        //self::sp_clear_cache();
    }

    public function sp_clear_cache() {
        $job_ids = $this->sp_add_clear_cache_task();

        if ( sizeof( $job_ids ) > 0 ) {

            foreach( $job_ids as $server => $job_id ) {
                if ( ! is_wp_error( $job_id ) ) {
                    $counter = 0;
                    while( $counter < $this->timeout ) {
                        $response = json_decode( $this->sp_get_task_status( $server, $job_id ), true );

                        if ( $response['status'] == "finished" ) {
                            add_action( 'admin_notices', $this->sp_admin_notice( "Cache successfully cleared on " . $server, "ok" ) );
                            break;
                        }

                        $counter++;
                        sleep(1);
                    }

                    if ( $counter >= $this->timeout ) {
                        $this->sp_admin_notice( "There were a problem while trying to clear your cache. Timed out. Please contact StudioPress support.", "fail" );
                        continue;
                    }

                } else {
                    $this->sp_admin_notice( "There were a problem while trying to clear your cache. WP ERROR. Please contact StudioPress support.", "fail" );
                }
            }
        } else {
            $this->sp_admin_notice( "There were a problem while trying to clear your cache. Please contact StudioPress support. Job ID not found.", "fail" );
        }
    }

    /**
     * Get job status from MessageBus.
     *
     * @param string $job_id Job ID.
     * @return array Job status.
     */
    public function sp_get_task_status( $server, $job_id ) {
        $url = $this->endpoint . "request/status?server=" . $server . "&id=" . $job_id;

        $post = wp_remote_post( $url, array(
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode( $this->endpoint_user . ':' . $this->endpoint_pass ),
                )
            )
        );

        return $post['body'];
    }

    /**
     * Add a task on MessageBus.
     */
    public function sp_add_clear_cache_task() {
        $request_ids = array();

        foreach( $this->sp_get_cache_servers( $this->domain ) as $server ) {

            $url = $this->endpoint . "site/clearcache?server=" . $server . "&domain=" . $this->domain;

            $post = wp_remote_post( $url, array(
                'headers' => array(
                    'Authorization' => 'Basic ' . base64_encode( $this->endpoint_user . ':' . $this->endpoint_pass )
                    )
                )
            );

            if ( is_wp_error( $post ) ) {
                $request_ids[] = $post;
                continue;
            }

            $response = json_decode( $post['body'], true );

            if ( isset( $response['status'] ) && $response['status'] == "ok" ) {
                $request_ids[ $server ] = $response['request_id'];
            }
        }

        return $request_ids;
    }

    /**
     * Get the caches servers.
     *
     * @param string $domain Domain name.
     *
     * @return array Cache servers.
     */
    public function sp_get_cache_servers( $domain ) {
        $servers = array();

        //$servers = explode( ',', gethostbyaddr( gethostbyname( $domain ) ) );

        $servers[] = gethostname();

        $servers = array_unique( $servers );

        foreach( $servers as $key => $server ) {
            if ( preg_match( '/.spsites.net/', $server ) ) {
                if ( preg_match( '/spe\d{2,}-\d{1,}.spsites.net/', $server ) ) {
                    $servers[ $key ] = "cse" . preg_replace( '/(spe|-\d{1,})/', '', $server );
                }
            } else {
                unset( $servers[ $key ] );
            }
        }

        return $servers;
    }

    /**
     * Queues custom CSS styles
     */
    public static function sp_add_styles() {
        wp_enqueue_style( 'sp-clear-cache', plugin_dir_url( __FILE__ ) . 'css/style.css', array(), $this->plugin_version );
    }

    /**
     * Creates the menu bar.
     */
    public function sp_admin_bar() {
        global $wp_admin_bar;

        if ( ! is_super_admin() || ! is_admin_bar_showing() || ! current_user_can( 'manage_options') ) {
            return false;
        }

        $admin = ( is_admin() ) ? "" : admin_url();

        $icon = '<div id="sp-cache-icon" class="ab-item svg" style="background-image: url(data:image/svg+xml;base64,PHN2ZyBpZD0iTGF5ZXJfMSIgZGF0YS1uYW1lPSJMYXllciAxIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxNi43NyAxNS43NiI+PGRlZnM+PHN0eWxlPi5jbHMtMXtmaWxsOiM5ZWEzYTg7fTwvc3R5bGU+PC9kZWZzPjx0aXRsZT5zd2VlcGluZy1pY29uPC90aXRsZT48cGF0aCBjbGFzcz0iY2xzLTEiIGQ9Ik0xMC4xLDcuMDVsLS4zLS4zYTEuMDgsMS4wOCwwLDAsMSwwLTEuNTJMMTUuMywxLjE0bDEuNy41M0wxMS42Miw3LjA1QTEuMDgsMS4wOCwwLDAsMSwxMC4xLDcuMDVaIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgtMC4yMyAtMS4xNCkiLz48cGF0aCBjbGFzcz0iY2xzLTEiIGQ9Ik0xMi43MywxMGMuOC0uODIuMjYtMi42OC0uNTYtMy41TDEwLjQsNC43MmMtLjgxLS44MS0yLjcyLTEuNC0zLjU1LS42MVoiIHRyYW5zZm9ybT0idHJhbnNsYXRlKC0wLjIzIC0xLjE0KSIvPjxyZWN0IGNsYXNzPSJjbHMtMSIgeD0iNC43NSIgeT0iNy42OSIgd2lkdGg9IjguMzIiIGhlaWdodD0iMC41IiB0cmFuc2Zvcm09InRyYW5zbGF0ZSg3Ljk5IC01LjExKSByb3RhdGUoNDUpIi8+PHBhdGggY2xhc3M9ImNscy0xIiBkPSJNMTEuMDUsMTIsNS4xNyw2LjA5UzIuODEsOS4xMi4yNSw4LjY1QTYuMjgsNi4yOCwwLDAsMCwxLDEyQzMuMDcsMTIsNS4xNiwxMCw1LjE1LDEwYTYuMDUsNi4wNSwwLDAsMS0zLjU5LDMsOS44MSw5LjgxLDAsMCwwLDEuMDcsMS4zMSwxMSwxMSwwLDAsMCwxLC45LDcuNjYsNy42NiwwLDAsMCwzLjIxLTIuODUsNi4wNiw2LjA2LDAsMCwxLTIuMzQsMy40NCw2LjgsNi44LDAsMCwwLDQsMS4xMUM4LjEzLDE1LjMxLDExLjA1LDEyLDExLjA1LDEyWiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTAuMjMgLTEuMTQpIi8+PGNpcmNsZSBjbGFzcz0iY2xzLTEiIGN4PSIxLjMzIiBjeT0iMy43MiIgcj0iMC42MiIvPjxjaXJjbGUgY2xhc3M9ImNscy0xIiBjeD0iMy4wOCIgY3k9IjEuNDYiIHI9IjEuMTMiLz48L3N2Zz4=);"></div>';

        $items[] = array(
            'id' => 'sp-clear-cache-button',
            'title' => $icon . 'Clear cache',
            'meta' => array ( 'class' => 'sp-clear-cache-button' ),
            'href' => '#',
        );

        foreach( $items as $item ) {
            $wp_admin_bar->add_node( $item );
        }
    }

    /**
     * Build notice to display on Dashboard.
     *
     * @param string $message Message to display.
     * @param string $status Message status used to colorize the message.
     */
    public function sp_admin_notice( $message = null, $status = null ) {
        if ( is_null( $status ) || is_null( $message ) ) {
            return false;
        }

        $status = ( $status == "fail" ) ?  "notice-warning" : "notice-success";
        $message = "<div class=\"notice " . $status . " is-dismissible\"><p> StudioPress Clear Cache - " . $message . "</p></div>";

        echo $message;

        return array( $this, 'sp_admin_notice' );
    }
}

global $sp_clear_cache;
$sp_clear_cache = new StudioPressClearCache();
