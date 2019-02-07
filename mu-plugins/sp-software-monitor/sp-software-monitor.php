<?php
/*
 * Plugin Name: Synthesis Software Monitor
 * Version: 1.2.4
 * Description: Monitors Synthesis accounts for inactive plugins and themes and warns users of the security risk. Also provides basic snapshot/backup/restore functionality
 * Plugin Author: CopyBlogger Media
 * Plugin URL: http://websynthesis.com
 */
class Synthesis_Software_Monitor {

    const VERSION = '1.2.10';

    // Options
    const PLUGIN_SNAPSHOT_OPTION_NAME = 'synthesis-plugin-snapshots-option';
    const THEME_SNAPSHOT_OPTION_NAME = 'synthesis-theme-snapshots-option';
    const IGNORED_PLUGINS_OPTION_NAME = 'synthesis-plugin-monitor-ignored-plugins';
    const IGNORED_THEMES_OPTION_NAME = 'synthesis-theme-monitor-ignored-plugins';
    const DISABLE_OPTION_NAME = 'synthesis-plugin-snapshots-disable';

    // Plugin snapshot settings
    const SNAPSHOT_PLUGIN_NAME = 'name';
    const SNAPSHOT_PLUGIN_VERSION = 'version';
    const SNAPSHOT_PLUGIN_SLUG = 'slug';
    const SNAPSHOT_PLUGIN_ACTIVE = 'active';
    const SNAPSHOT_LIMIT = 10;

    // Plugin snapshot settings
    const SNAPSHOT_THEME_NAME = 'name';
    const SNAPSHOT_THEME_VERSION = 'version';
    const SNAPSHOT_THEME_SLUG = 'slug';
    const SNAPSHOT_THEME_ACTIVE = 'active';


    // Synthesis Scribe Constants
    const SCRIBE_API_KEY = 'synthesis_scribe_api_key';
    const SCRIBE_KEY_URL = 'http://websynthesis.com/scribe-key/';
    const SCRIBE_STRIP_KEY = true;
    const SCRIBE_KEY_LENGTH = 32;

    //WordPress core allow_major_auto_core_updates
    const SP_WP_CORE_UPDATE_OPTION_NAME = 'sp-wp-core-update';
    public static $sp_wp_core_update_available = false;

    /**
     * SSL sitewide option name
     *
     * @since 1.2.10
     *
     * @var string
     */
    const SP_HAS_SSL_OPTION_NAME = 'sp_has_ssl';

    private static $inactive_plugins = null;
    private static $current_plugins = null;
    private static $inactive_themes = null;
    private static $current_themes = null;

    /*
     * Kick things off
     */
    public static function start() {
        require_once( plugin_dir_path( __FILE__ ) . 'lib/synthesis-db-backup.php' );
        require_once( plugin_dir_path( __FILE__ ) . 'lib/synthesis-s3-settings.php' );

        global $wp_version;
        self::$sp_wp_core_update_available = ( substr( preg_replace( '/\./', '', $wp_version ), 0, 2 ) > 37 ) ? true : false;

        if ( ! is_multisite() && ! get_option( self::DISABLE_OPTION_NAME, false ) ) {
            add_action( 'wp_loaded', array( __CLASS__, 'sp_check_redirect' ) );
            add_action( 'admin_enqueue_scripts', array( __CLASS__, 'add_styles' ) );
            add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ) );
            add_action( 'admin_notices', array( __CLASS__, 'inactive_plugin_notifications' ) );
            add_action( 'wp_ajax_take_plugin_snapshot', array( __CLASS__, 'ajax_take_plugin_snapshot' ) );
            add_action( 'wp_ajax_delete_plugin_snapshot', array( __CLASS__, 'ajax_delete_plugin_snapshot' ) );
            add_action( 'wp_ajax_take_theme_snapshot', array( __CLASS__, 'ajax_take_theme_snapshot' ) );
            add_action( 'wp_ajax_delete_theme_snapshot', array( __CLASS__, 'ajax_delete_theme_snapshot' ) );
            add_action( 'wp_ajax_make_database_backup', array( __CLASS__, 'ajax_make_database_backup' ) );
            add_action( 'wp_ajax_get_database_backup_data', array( __CLASS__, 'ajax_get_database_backup_data' ) );
            add_action( 'wp_ajax_save_s3_backup_settings', array( __CLASS__, 'ajax_save_s3_backup_settings' ) );
            add_action( 'wp_ajax_save_synthesis_scribe_api_key', array( __CLASS__, 'ajax_save_synthesis_scribe_api_key' ) );
            add_action( 'wp_ajax_restore_table_backup', array( __CLASS__, 'ajax_restore_table_backup' ) );
            add_action( 'wp_ajax_cancel_table_restore', array( __CLASS__, 'ajax_cancel_table_restore' ) );

            if ( self::$sp_wp_core_update_available ) {
                add_action( 'wp_ajax_change_wp_core_updates', array( __CLASS__, 'ajax_change_wp_core_updates' ) );
            }

            add_action( 'wp_ajax_change_sp_https_option', array( __CLASS__, 'ajax_change_sp_https_option' ) );

            add_action( 'wp_loaded', array( __CLASS__, 'get_memory_usage' ) );
            add_action( 'wp_loaded', array( __CLASS__, 'sp_wp_core_update_settings' ) );
        }
    }

    /**
     * Checks if it needs to redirect to https.
     *
     * @since 1.2.10
     */
    public static function sp_check_redirect() {
        if ( '1' === get_option( self::SP_HAS_SSL_OPTION_NAME ) ) {
            self::sp_https_redirect();
        }
    }

    /**
     * Redirect function.
     *
     * @since 1.2.10
     */
    public static function sp_https_redirect() {
        if ( is_ssl() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || defined( 'WP_CLI' ) ) {
            return;
        }

        wp_redirect( 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], 301 );

        exit();
    }

    public static function ajax_change_wp_core_updates() {
        if ( isset( $_POST['value'] ) ) {
            $value = $_POST['value'];
        }

        if ( has_filter( 'allow_minor_auto_core_updates', '__return_true' ) ) {
            remove_filter( 'allow_minor_auto_core_updates', '__return_true' );
        }

        if ( has_filter( 'allow_major_auto_core_updates', '__return_true' ) ) {
            remove_filter( 'allow_major_auto_core_updates', '__return_true' );
        }

        if ( has_filter( 'automatic_updater_disabled', '__return_true' ) ) {
            remove_filter( 'automatic_updater_disabled', '__return_true' );
        }

        switch ( $value ) {
            case 'major':
                add_filter( 'allow_major_auto_core_updates', '__return_true' );
                update_option( self::SP_WP_CORE_UPDATE_OPTION_NAME, 'major' );
                break;
            case 'disabled':
                add_filter( 'automatic_updater_disabled', '__return_true' );
                update_option( self::SP_WP_CORE_UPDATE_OPTION_NAME, 'disabled' );
                break;
            default:
                add_filter( 'allow_minor_auto_core_updates', '__return_true' );
                update_option( self::SP_WP_CORE_UPDATE_OPTION_NAME, 'minor' );
                break;
        }

        die( _e( "<br />The WordPress core update method has successfully updated to " . ucfirst( $value ) . "." ) );
    }

    /**
     * AJAX function to update the SSL sitewide option.
     *
     * @since 1.2.10
     */
    public static function ajax_change_sp_https_option() {
        if ( isset( $_POST['value'] ) ) {
            $value = $_POST['value'];
        }

        $value = ( 'on' == $value ) ? 1 : 0;

        update_option( self::SP_HAS_SSL_OPTION_NAME, $value );

        $status = ( 1  === $value ) ? 'activated' : 'deactivated';

        die( _e( sprintf( __( '<br />Sitewide https has been %1$s on your site.' ), $status ) ) );
    }

    /**
     * Handles an ajax request to save the Synthesis Scribe API key
     */
    public static function ajax_save_synthesis_scribe_api_key() {
        if ( ! current_user_can( 'install_plugins' ) ) {
            die( json_encode( array( 'error' => __( 'You do not have permission to do that' ) ) ) );
        }
        $key = $_REQUEST['key'];
        if ( empty( $key ) ) {
            die( json_encode( array( 'error' => __( 'You must enter an API key' ) ) ) );
        }

        $key_parts = explode( '-', $key );
        if ( 2 != count( $key_parts ) ) {
            die( json_encode( array( 'error' => __( 'You must enter a valid API key' ) ) ) );
        }
        $key_guid = $key_parts[1];
        while ( strlen( $key_guid ) > self::SCRIBE_KEY_LENGTH ) {
            $key_guid = substr( $key_guid, 1 );
        }
        $key_parts[1] = $key_guid;
        $key = implode( '-', $key_parts );

        update_option( self::SCRIBE_API_KEY, $key );
        die( json_encode( array( 'success' => true ) ) );
    }

    /**
     * Handles an ajax request to store a snapshot of currently installed themes
     */
    public static function ajax_take_theme_snapshot() {
        if ( current_user_can( 'install_themes' ) ) {
            $all_themes = wp_get_themes();

            $current_snapshots = get_option( self::THEME_SNAPSHOT_OPTION_NAME, array() );

            $new_snapshot = array(
                'Date'    => current_time( 'timestamp' ),
                'Themes' => array()
            );

            // Pare down the essentials for the snapshot
            foreach ( $all_themes as $slug => $theme ) {
                $new_snapshot['Themes'][] = array(
                    self::SNAPSHOT_THEME_NAME    => $theme->name,
                    self::SNAPSHOT_THEME_VERSION => $theme->version,
                    self::SNAPSHOT_THEME_ACTIVE  => ( wp_get_theme()->name == $theme->name ) ? true : false,
                    self::SNAPSHOT_THEME_SLUG    => $slug,
                );
            }

            array_unshift( $current_snapshots, $new_snapshot );

            while ( count( $current_snapshots ) > self::SNAPSHOT_LIMIT ) {
                array_pop( $current_snapshots );
            }

            update_option( self::THEME_SNAPSHOT_OPTION_NAME, $current_snapshots );
            self::theme_snapshots_markup();
            die();
        } else {
            die( 'You do not have permission to take plugin snapshots' );
        }
    }

    /**
     * Handle an ajax request to delete a specific theme snapshot
     */
    public static function ajax_delete_theme_snapshot() {
        if ( current_user_can( 'install_themes' ) ) {
            $snapshot_id = $_POST['snapshot_id'];
            $snapshots = get_option( self::THEME_SNAPSHOT_OPTION_NAME, array() );
            $new_snapshots = array();
            foreach ( $snapshots as $snapshot ) {
                if ( $snapshot['Date'] != $snapshot_id ) {
                    $new_snapshots[] = $snapshot;
                }
            }
            update_option( self::THEME_SNAPSHOT_OPTION_NAME, $new_snapshots );
            echo 'success';
            die();
        } else {
            die( 'You do not have permission to delete plugin snapshots' );
        }
    }

    /**
     * Handles an ajax request to store a snapshot of currently installed plugins
     */
    public static function ajax_take_plugin_snapshot() {
        if ( current_user_can( 'install_plugins' ) ) {
            $all_plugins = self::get_all_plugins();
            $current_snapshots = get_option( self::PLUGIN_SNAPSHOT_OPTION_NAME, array() );

            $new_snapshot = array(
                'Date'    => current_time( 'timestamp' ),
                'Plugins' => array()
            );

            // Pare down the essentials for the snapshot
            foreach ( $all_plugins as $slug => $plugin ) {
                $new_snapshot['Plugins'][] = array(
                    self::SNAPSHOT_PLUGIN_NAME    => $plugin['Name'],
                    self::SNAPSHOT_PLUGIN_VERSION => $plugin['Version'],
                    self::SNAPSHOT_PLUGIN_ACTIVE  => $plugin['Active'],
                    self::SNAPSHOT_PLUGIN_SLUG    => $slug,
                );
            }

            array_unshift( $current_snapshots, $new_snapshot );

            while ( count( $current_snapshots ) > self::SNAPSHOT_LIMIT ) {
                array_pop( $current_snapshots );
            }

            update_option( self::PLUGIN_SNAPSHOT_OPTION_NAME, $current_snapshots );
            self::plugin_snapshots_markup();
            die();
        } else {
            die( 'You do not have permission to take plugin snapshots' );
        }
    }

    /**
     * Handle an ajax request to delete a specific plugin snapshot
     */
    public static function ajax_delete_plugin_snapshot() {
        if ( current_user_can( 'install_plugins' ) ) {
            $snapshot_id = $_POST['snapshot_id'];
            $snapshots = get_option( self::PLUGIN_SNAPSHOT_OPTION_NAME, array() );
            $new_snapshots = array();
            foreach ( $snapshots as $snapshot ) {
                if ( $snapshot['Date'] != $snapshot_id ) {
                    $new_snapshots[] = $snapshot;
                }
            }
            update_option( self::PLUGIN_SNAPSHOT_OPTION_NAME, $new_snapshots );
            echo 'success';
            die();
        } else {
            die( 'You do not have permission to delete plugin snapshots' );
        }
    }

    public static function ajax_make_database_backup() {
        if ( !current_user_can( 'install_plugins' ) || get_option( 'sng_level' ) ) {
            die();
        }

        // Make sure we're not running a backup.
        if ( Synthesis_DB_Backup::is_backup_running() ) {
            // There's already a snapshot running, send a response to the user and exit.

            $response = array(
                'failed'  => true,
                'message' => "There is already a backup running. Please wait for status to appear.",
            );
        } else {
            // Start a backup

            $exclude_tables = Synthesis_DB_Backup::get_default_excluded_tables();

            // Backup all tables except our default.
            $snapshot = Synthesis_DB_Backup::export_database( $exclude_tables );

            self::save_db_snapshot( $snapshot );

            // Quick backup. Let the frontend know we didn't fail.
            $response = array(
                'failed'  => false,
                'message' => "",
            );
        }

        die( json_encode( $response ) );
    }

    public static function ajax_restore_table_backup() {
        if ( !current_user_can( 'install_plugins' ) ) {
            die();
        }

        // Expect the ID of the backup and the name of the table
        if ( empty( $_REQUEST['backup_id'] ) || empty( $_REQUEST['table_name'] ) ) {
            json_encode( array( 'error' => __( 'Backup ID and table name expected but not provided' ) ) );
            die();
        }

        $backup_id = $_REQUEST['backup_id'];
        $table_name = $_REQUEST['table_name'];

        $errors = array();
        Synthesis_DB_Backup::import_table( $backup_id, $table_name, $errors );

        if ( empty( $errors ) ) {
            // Quick restore. Let the frontend know we didn't fail.
            $response = array(
                'failed'  => false,
                'message' => "",
            );
        } else {
            $response = array(
                'failed' => true,
                'errors' => $errors
            );
        }

        echo json_encode( $response );
        die();
    }

    public static function ajax_cancel_table_restore() {
        if ( !current_user_can( 'install_plugins' ) ) {
            die();
        }

        // Expect the ID of the backup and the name of the table
        if ( empty( $_REQUEST['backup_id'] ) || empty( $_REQUEST['table_name'] ) ) {
            json_encode( array( 'error' => __( 'Backup ID and table name expected but not provided' ) ) );
            die();
        }

        $backup_id = $_REQUEST['backup_id'];
        $table_name = $_REQUEST['table_name'];

        Synthesis_DB_Backup::halt_table_restore( $backup_id, $table_name );

        echo json_encode( array( 'success' => true ) );
        die();
    }

    public static function ajax_get_database_backup_data() {
        // Returns the ID of any currently running backup
        $most_recent_id = Synthesis_DB_Backup::is_backup_running();
        $is_restore_running = Synthesis_DB_Backup::is_restore_running();

        $is_running = !empty( $most_recent_id );

        // Get the previous ID if the snapshot isn't running
        if ( !$is_running ) {
            $snapshot = self::get_db_snapshot();
            if ( isset( $snapshot['id'] ) ) {
                $most_recent_id = $snapshot['id'];
            }
        }

        ob_start();
        self::db_snapshots_markup( $most_recent_id );
        $markup = ob_get_clean();

        $response = array(
            'restore_running' => $is_restore_running,
            'running'         => $is_running,
            'markup'          => $markup,
        );

        if ( !$is_running && !empty( $most_recent_id ) ) {
            $status = Synthesis_DB_Backup::load_backup_status_from_id( $most_recent_id );
            $snapshot_info = $status->get_snapshot_info();
            $response['url'] = $snapshot_info['url'];
        }

        die( json_encode( $response ) );
    }

    public static function ajax_save_s3_backup_settings() {
        $errors = Synthesis_S3_Settings::save_s3_backup_settings( $_POST );
        Synthesis_S3_Settings::s3_settings_markup( $errors );
        die();
    }

    /**
     * Registers an administration menu
     */
    public static function admin_menu() {
        if ( ! get_option( 'sng_level' ) ) {

            $menu_title = "StudioPress";
            $svg_icon = 'data:image/svg+xml;base64,PHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJzcC1pY29uIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB4PSIwcHgiIHk9IjBweCINCgkgdmlld0JveD0iMCAwIDIwIDIwIiBzdHlsZT0iZmlsbDojODI4NzhjIiB4bWw6c3BhY2U9InByZXNlcnZlIj4NCgk8c3R5bGU+LnNwLWljb257ZmlsbDogIzgyODc4Yzt9PC9zdHlsZT4NCgk8cGF0aCBjbGFzcz0ic3AtaWNvbiIgZD0iTTMsM2g2LjV2MTRIM1YzeiBNMTAuNSwzSDE3djYuNWgtNi41VjN6IE0xMC41LDEwLjVIMTdWMTdoLTYuNVYxMC41eiIvPg0KPC9zdmc+';

            $page = add_menu_page( __( 'StudioPress Software Monitor' ), __( $menu_title ), 'install_plugins', 'sp-software-monitor', array( __CLASS__, 'software_monitor_page' ), $svg_icon, 2 );

            add_submenu_page( 'sp-software-monitor', __( 'StudioPress Software Monitor' ), __( 'Site Tools' ), 'install_plugins', 'sp-software-monitor', array( __CLASS__, 'software_monitor_page' ) );

            /* Using registered $page handle to hook script load */
            add_action( 'admin_print_scripts-' . $page, array( __CLASS__, 'software_monitor_admin_scripts' ) );

        }
    }

    public static function software_monitor_admin_scripts() {
        wp_enqueue_script( 'software-monitor-admin-scripts', plugin_dir_url( __FILE__ ) . 'js/software-monitor.js', array( 'jquery' ), self::VERSION );
        wp_localize_script(
            'software-monitor-admin-scripts',
            'SynthesisSoftwareMonitor',
            array(
                'ajaxUrl'         => admin_url( "admin-ajax.php" ),
                'backupDirectory' => Synthesis_DB_Backup::get_backup_url(),
                'statusFile'      => 'status.json',
            )
        );
    }

    /**
     * Check if domain has SSL installed.
     *
     * @since 1.2.10
     *
     * @param string $domain Domain name to check.
     *
     * @return boolean True if SSL is installed on domain.
     */
    public static function sp_has_ssl( $domain ) {
        $response = wp_remote_head( 'https://' . $domain, array( 'timeout' => 5 ) );

        if ( is_wp_error( $response ) ) {
            return false;
        }

        if ( ! isset( $response['response']['code'] ) || ! preg_match( '/(200|301|302)/', $response['response']['code'] ) ) {
            return false;
        }

        return true;
    }

    /**
     * Outputs the admin page for Software Monitor
     */
    public static function software_monitor_page() {
        // View inputs
        $s3_backup = Synthesis_S3_Settings::get_s3_backup_settings();
            include( plugin_dir_path( __FILE__ ) . "views/sp-software-monitor-admin.php" );
    }

    public static function theme_monitor_markup() {

        print "<p>";
            $nondefault_themes = self::get_nondefault_inactive_themes();
            if ( count( $nondefault_themes ) > 0 ) {
                _e( sprintf( 'You have %d inactive theme(s). You should delete them to keep your site secure.', count( $nondefault_themes ) ) );
            } else {
                _e( 'All is well. You have no inactive themes!' );
            }
        print "</p>";

    }

    public static function wp_core_updates_markup() {
        $options = array(
            "disabled" => false,
            "minor" => false,
            "major" => false
        );

        $value = get_option( self::SP_WP_CORE_UPDATE_OPTION_NAME );

        if ( is_null( $value ) || empty( $value ) ) {
            $value = "minor";
        }

        if ( defined( 'WP_AUTO_UPDATE_CORE' ) ) {
            if ( ! WP_AUTO_UPDATE_CORE || ! self::$sp_wp_core_update_available ) {
                $value = 'disabled';
            } else {
                $value = ( WP_AUTO_UPDATE_CORE === 'minor' ) ? 'minor' : 'major';
            }

            update_option( self::SP_WP_CORE_UPDATE_OPTION_NAME, $value );
        }

        if ( defined( 'AUTOMATIC_UPDATER_DISABLED' ) ) {
            $value = 'disabled';
            update_option( self::SP_WP_CORE_UPDATE_OPTION_NAME, $value );
        }

        $options[ $value ] = true;

        ?>
        <form action="" method="post">
                <?php
                if ( defined( 'WP_AUTO_UPDATE_CORE' ) || defined( 'AUTOMATIC_UPDATER_DISABLED' ) || ! self::$sp_wp_core_update_available ) {
                    print "<select class=\"select\" id=\"wp-core-updates-selector\" disabled=\"true\">";
                } else {
                    print "<select class=\"select\" id=\"wp-core-updates-selector\">";
                }

                foreach( $options as $key => $value ) {
                    $selected = ( $value ) ? " selected" : "";
                    print "<option value=\"" . $key . "\" " . $selected . ">" . ucfirst( $key ) . "</option>";
                }
                ?>
            </select>
        </form>
        <div id="sp-wp-core-update-message">
        </div>
        <div id="sp-wp-core-update-info-message">
            <?php _e( '<br />Major will enable all core updates for WordPress. Minor, also known as "Security Releases," will enable point releases for your current version of WordPress. You can manually check for new versions by clicking "Check Again" <a href="/wp-admin/update-core.php">here.</a>' ); ?>
        </div>
        <?php
        if ( defined( 'WP_AUTO_UPDATE_CORE' ) || defined( 'AUTOMATIC_UPDATER_DISABLED' ) ) {
            print _e( "<p>Auto update is currently being managed by your site's wp-config.php file. Please remove WP_AUTO_UPDATE_CORE or AUTOMATIC_UPDATER_DISABLED to be able to use this feature.</p>" );
        } elseif ( ! self::$sp_wp_core_update_available ) {
            print _e( "<p>Auto update control is currently unavailable since your WordPress version is not supported. Please update your WordPress to use this feature.</p>" );
        }
    }

    /**
     * StudioPress HTTPS sitewide Markup.
     *
     * @since 1.2.10
     */
    public static function sp_has_ssl_markup() {
        $checkbox_status = '';

        if ( ! self::sp_has_ssl( $_SERVER['SERVER_NAME'] ) ) {
            $checkbox_status = 'no_ssl';
        }

        if ( preg_match( '/^https/', home_url() ) ) {
            $checkbox_status = 'https_enabled_on_siteurl';
        }

        $value = get_option( self::SP_HAS_SSL_OPTION_NAME );

        if ( is_null( $value ) || empty( $value ) ) {
            $value = '0';
        }

        ?>
        <form action="" method="post">
                <?php
                if ( '1' == $value ) {
                    print "<input type=\"checkbox\" name=\"https\" class=\"checkbox\" id=\"sp-has-ssl-checkbox\" checked/ " . ( ( ! empty( $checkbox_status ) ) ? 'disabled' : '' ) . " /><label for=\"sp-has-ssl-checkbox\"> Enable HTTPS Sitewide</label>";
                } else {
                    print "<input type=\"checkbox\" name=\"https\" class=\"checkbox\" id=\"sp-has-ssl-checkbox\" "  . ( ( ! empty( $checkbox_status ) ) ? 'disabled' : '' ) .  "/><label for=\"sp-has-ssl-checkbox\">Enable HTTPS Sitewide</label>";
                }
                ?>
        </form>
        <div id="sp-has-ssl-message">
            <?php if ( $checkbox_status == 'no_ssl' ) { ?>
                <p>A SSL certificate must be installed to enable HTTPS. Learn how to <a href="https://my.studiopress.com/documentation/ssl-certificates/ssl-options/activate-ssl-for-your-studiopress-site/" target="_blank">Activate SSL for Your StudioPress Site</a></p>
            <?php } elseif ( $checkbox_status == 'https_enabled_on_siteurl' ) { ?>
                <p>The WordPress Address (URL) and Site Address (URL) must use the HTTP version of your domain before HTTPS can be enabled on your site. Learn how to <a href="https://my.studiopress.com/documentation/usage/wordpress-features/edit-the-wordpress-address-url-and-site-address-url/" target="_blank">Edit the WordPress Address URL and Site Address URL</a>.</p>
            <?php } ?>
        </div>
        <div id="sp-has-ssl-info-message">
            <?php _e( '<br /> If enabled, your site traffic will be redirected to <i><strong>https</strong></i> by default.<br /><br />See <i><a href="https://my.studiopress.com/documentation/ssl-certificates/ssl-options/fix-insecure-content-on-your-site/" target="_blank">Fix Insecure Content on Your Site</a></i> for information on troubleshooting and fixing insecure content on your site.' ); ?>
        </div>
        <?php
    }

    /**
     * Outputs all existing theme snapshots with display markup
     */
    public static function theme_snapshots_markup() {
        $snapshots = get_option( self::THEME_SNAPSHOT_OPTION_NAME, array() );
        if ( empty( $snapshots ) ): ?>
            <div><?php _e( 'You don\'t currently have any saved snapshot reports.' ); ?></div>
        <?php else: ?>
            <?php foreach ( $snapshots as $snapshot ): ?>
                <div class="smash-panel">
                    <?php // Header for the collapsing panel ?>
                    <div class="collapsible">
                        <span><?php printf( __( '<strong>Snapshot taken:</strong> %s' ), date( 'Y M d, h:i a', $snapshot['Date'] ) ); ?></span>
                        <span class="float-right"><a href="#" class="delete-snapshot" data-snapshot-id="<?php echo esc_attr( $snapshot['Date'] ); ?>">&#215;</a></span>
                        <span class="float-right plugin-count"><?php printf( __( 'Themes Count: %s' ), count( $snapshot['Themes'] ) ); ?></span>
                    </div>
                    <div>
                        <table>
                            <thead>
                            <tr align="left">
                                <th><?php _e( 'Name' ); ?></th>
                                <th><?php _e( 'Version' ); ?></th>
                                <th><?php _e( 'Slug' ); ?></th>
                                <th><?php _e( 'Status' ); ?></th>
                            </tr>
                            </thead>
                            <?php

                            foreach ( $snapshot['Themes'] as $plugin_data ): ?>
                                <tr>
                                    <td><?php echo $plugin_data[self::SNAPSHOT_THEME_NAME]; ?></td>
                                    <td><?php echo $plugin_data[self::SNAPSHOT_THEME_VERSION]; ?></td>
                                    <td><?php echo $plugin_data[self::SNAPSHOT_THEME_SLUG]; ?></td>
                                    <td><?php echo $plugin_data[self::SNAPSHOT_THEME_ACTIVE] ? 'Active' : 'Inactive'; ?></td>
                                </tr>
                            <?php
                            endforeach;
                            ?>
                        </table>
                    </div>
                </div>
            <?php
            endforeach;
        endif;
    }


    /**
     * Outputs all existing plugin snapshots with display markup
     */
    public static function plugin_snapshots_markup() {
        $snapshots = get_option( self::PLUGIN_SNAPSHOT_OPTION_NAME, array() );
        if ( empty( $snapshots ) ): ?>
            <div><?php _e( 'You don\'t currently have any saved snapshot reports.' ); ?></div>
        <?php else: ?>
            <?php foreach ( $snapshots as $snapshot ): ?>
                <div class="smash-panel">
                    <?php // Header for the collapsing panel ?>
                    <div class="collapsible">
                        <span><?php printf( __( '<strong>Snapshot taken:</strong> %s' ), date( 'Y M d, h:i a', $snapshot['Date'] ) ); ?></span>
                        <span class="float-right"><a href="#" class="delete-snapshot" data-snapshot-id="<?php echo esc_attr( $snapshot['Date'] ); ?>">&#215;</a></span>
                        <span class="float-right plugin-count"><?php printf( __( 'Plugin Count: %s' ), count( $snapshot['Plugins'] ) ); ?></span>
                    </div>
                    <div>
                        <table>
                            <thead>
                            <tr align="left">
                                <th><?php _e( 'Name' ); ?></th>
                                <th><?php _e( 'Version' ); ?></th>
                                <th><?php _e( 'Slug' ); ?></th>
                                <th><?php _e( 'Status' ); ?></th>
                            </tr>
                            </thead>
                            <?php

                            foreach ( $snapshot['Plugins'] as $plugin_data ): ?>
                                <tr>
                                    <td><?php echo $plugin_data[self::SNAPSHOT_PLUGIN_NAME]; ?></td>
                                    <td><?php echo $plugin_data[self::SNAPSHOT_PLUGIN_VERSION]; ?></td>
                                    <td><?php echo $plugin_data[self::SNAPSHOT_PLUGIN_SLUG]; ?></td>
                                    <td><?php echo $plugin_data[self::SNAPSHOT_PLUGIN_ACTIVE] ? 'Active' : 'Inactive'; ?></td>
                                </tr>
                            <?php
                            endforeach;
                            ?>
                        </table>
                    </div>
                </div>
            <?php
            endforeach;
        endif;
    }

    public static function db_snapshots_markup( $snapshot_id = false ) {
        // Get the previous ID if the snapshot isn't running
        if ( empty( $snapshot_id ) ) {
            $most_recent_id = Synthesis_DB_Backup::is_backup_running();

            $is_running = !empty( $most_recent_id );

            if ( $is_running ) {
                $snapshot_id = $most_recent_id;
            } else {
                $snapshot_id = self::get_db_snapshot();
                if ( !isset( $snapshot_id['id'] ) ) {
                    echo '<p>';
                    _e( 'You haven\'t taken any snapshots' );
                    echo '</p>';
                    return;
                }

                $snapshot_id = $snapshot_id['id'];
            }

        }
        $backup_status = Synthesis_DB_Backup::load_backup_status_from_id( $snapshot_id );
        $restore_status = Synthesis_DB_Backup::load_restore_status_from_id( $snapshot_id );
        if ( !$backup_status->is_loaded_from_file() ) {
            echo '<p>';
            _e( 'Unable to get backup status. The backup files appear to be missing.' );
            echo '</p>';
            return;
        }

        // TODO: Make this a friendly message
        $status_message = $backup_status->get_status_code();

        // Get the list of tables.
        $table_names = $backup_status->get_table_names();

        // Calculate totals
        $total_rows = 0;
        $total_processed = 0;
        foreach ( $table_names as $table_name ) {
            if ( Synthesis_Backup_Status::TABLE_STATUS_SKIPPED != $backup_status->get_table_status_code( $table_name ) ) {
                $total_rows += $backup_status->get_table_row_count( $table_name, 0 );
                $total_processed += $backup_status->get_table_current_row( $table_name, 0 );
            }
        }
        $percent_total = ( $total_processed / $total_rows ) * 100;
        $format_string = '%d / %d (%.2f%%)';

        $backup_title = '';
        if ( 'finished' == $status_message ) {
            $length_string = self::get_duration_string( $backup_status->get_start(), $backup_status->get_end() );
            $backup_title = sprintf( 'Snapshot finished at %s (total time: %s)', date( 'h:i a \o\n M d Y', $backup_status->get_end() ), $length_string );
        } elseif ( 'started' == $status_message ) {
            $length_string = self::get_duration_string( $backup_status['start'], current_time( 'timestamp' ) );
            $backup_title = sprintf( 'Snapshot started at %s (%s ago)', date( 'h:i a \o\n M d Y', $backup_status->get_start() ), $length_string );
        } elseif ( 'cancelled' == $status_message ) {
            $backup_title = "Snapshot cancelled";
        }

        echo '<div class="smash-panel"><div id="db-backup-header" class="collapsible">';
        echo '<span id="db-backup-message">' . $backup_title . '</span>';
        echo '<span id="db-backup-status" style="float: right">' . sprintf( $format_string, $total_processed, $total_rows, $percent_total ) . '</span></div>';
        echo '<div><table class="db-backup-details" style="width: 100%"><thead><tr>';
        echo '<th>' . __( 'Table Name' ) . '</th>';
        echo '<th>' . __( 'Status' ) . '</th>';
        echo '<th style="text-align: right">' . __( 'Backup Status' ) . '</th>';
        echo '<th>' . __( 'Restore' ) . '</th>';
        echo '<th>' . __( 'Restore Status' ) . '</th>';
        echo '</tr></thead>';

        //echo $status;

        // Old backup versions don't support web-based restore. Defaults to false if not present.
        $able_to_restore = $backup_status->get_version();

        foreach ( $table_names as $table_name ) {
            $current_row = $backup_status->get_table_current_row( $table_name, 0 );
            $row_count = $backup_status->get_table_row_count( $table_name, 0 );
            $table_status_code = $backup_status->get_table_status_code( $table_name );

            if ( Synthesis_Backup_Status::TABLE_STATUS_SKIPPED == $table_status_code ) {
                $backup_progress = sprintf(
                    '%s / %s (%s)',
                    $current_row,
                    $row_count,
                    "skipped" );
            } else {
                $percentDone = 0 == $row_count ? 100 : ( $current_row / $row_count ) * 100;
                $backup_progress = sprintf(
                    $format_string,
                    $current_row,
                    $row_count,
                    $percentDone
                );
            }

            $table_restore_status = isset( $restore_status[$table_name] ) ? $restore_status[$table_name] : array();

            if ( !$able_to_restore ) {
                $restore_link = '';
            } else if ( empty( $table_restore_status ) || 'nothing' == $table_restore_status['status'] || 'restored' == $table_restore_status['status'] || 'cancelled' == $table_restore_status['status'] ) {
                $restore_link = '<a class="restore-table" href="#" data-table-name="' . esc_attr( $table_name ) . '" data-backup-id="' . esc_attr( $snapshot_id ) . '">' . __( 'restore' ) . '</a>';
            } else {
                $restore_link = '<a class="cancel-restore" href="#" data-table-name="' . esc_attr( $table_name ) . '" data-backup-id="' . esc_attr( $snapshot_id ) . '">' . __( 'cancel' ) . '</a>';
            }

            if ( isset( $table_restore_status['status'] ) && 'restored' == $table_restore_status['status'] ) {
                $restore_progress = sprintf( 'Restored at %s', date( 'h:i a \o\n M d Y', $table_restore_status['last_update'] ) );
            } elseif ( isset( $table_restore_status['status'] ) && 'cancelled' == $table_restore_status['status'] ) {
                $restore_progress = 'Cancelled';
            } elseif ( !empty( $table_restore_status['current_row'] ) ) {
                $restore_percent = 0 == $table_restore_status['rows'] ? 100 : ( $table_restore_status['current_row'] / $table_restore_status['rows'] ) * 100;
                $restore_progress = sprintf( $format_string, $table_restore_status['current_row'], $table_restore_status['rows'], $restore_percent );
            } else {
                $restore_progress = 'N/A';
            }

            echo '<tr><td>' . $table_name . '</td>';
            echo '<td style="text-align: center">' . $table_status_code . '</td>';
            echo '<td style="text-align: right">' . $backup_progress . '</td>';
            echo '<td style="text-align: center">' . $restore_link . '<span class="spinner backup-restore-spinner"></span></td>';
            echo '<td style="text-align: center">' . $restore_progress . '</td></tr>';
        }
        echo '</table>';

        echo '<a href="#" class="restore-all-tables">Restore All Tables</a> | ';
        echo '<a href="#" class="cancel-all-restores">Cancel All Restores</a>';

        echo '</div></div>';


        /*
            //    Last completed snapshot info
            $db_snapshot = self::get_db_snapshot();
            if ( $db_snapshot ) {
                $snapshot_date = date( 'Y M d, h:i a', $db_snapshot['timestamp'] );
                $snapshot_url = $db_snapshot['url'];

                return __( sprintf( 'Last snapshot taken: %s - <a href="%s">Download</a>', $snapshot_date, esc_url( $snapshot_url ) ) );
            } else {
                return __( 'You haven\'t created a database snapshot yet' );
            }
        */
    }

    /**
     * Outputs a warning to site administrators if there are inactive themes/plugins installed
     */
    public static function inactive_plugin_notifications() {

        if ( !current_user_can( 'manage_options' ) || get_option( 'sng_level' ) )
            return;

        $inactive_plugins = self::get_inactive_plugins();
        $nondefault_inactive_plugin_slugs = self::get_nondefault_inactive_plugins();

        $inactive_themes = self::get_inactive_themes();
        $nondefault_inactive_theme_slugs = self::get_nondefault_inactive_themes();

        $warning_message = '';
        if ( !empty( $nondefault_inactive_plugin_slugs ) && !empty( $nondefault_inactive_theme_slugs ) ) {
            $warning_message = sprintf( __( '%d inactive plugin(s) and %d inactive theme(s)' ),
                count( $inactive_plugins ), count( $inactive_themes ) );
        } elseif ( !empty( $nondefault_inactive_plugin_slugs ) ) {
            $warning_message = sprintf( __( '%d inactive plugin(s)' ), count( $inactive_plugins ) );
        } elseif ( !empty( $nondefault_inactive_theme_slugs ) ) {
            $warning_message = sprintf( __( '%d inactive theme(s)' ), count( $inactive_themes ) );
        }
    }

    public static function bw_quota_notification() {
        if ( current_user_can( 'install_plugins' ) ) {
            $disk_usage = self::get_resource_usage();
            if ( null != $disk_usage ) {
                $used = ( isset( $disk_usage->bandwidth_used ) ) ? $disk_usage->bandwidth_used : null;
                $quota = ( isset( $disk_usage->bw_quota ) ) ? $disk_usage->bw_quota : null;
                if ( null != $quota )
                    $percent = intval ( ( $used / $quota ) * 100 );
                if ( isset( $percent ) ) {
                    if ( $percent >= 100 ) {
                    ?>
                    <div class="error">
                        <p>
                            <?php
                            printf( __( 'You have reached <strong>%d%% (%dGb)</strong> of your bandwidth usage limit of <strong>%dGb</strong>.<br />' .
                            'Please contact customer support as you could incur overage charges on your next bill.' ), $percent, $used, $quota );
                            ?>
                        </p>
                    </div><?php
                    }
                }
            }
        }
    }

    public static function disk_quota_notification() {
        if ( current_user_can( 'install_plugins' ) ) {
            $disk_usage = self::get_resource_usage();
                if ( null != $disk_usage ) {
                    $used = $disk_usage->disk_used;
                    $quota = $disk_usage->soft_quota;
                    if ( null != $quota )
                        $percent = intval ( ( $used / $quota ) * 100 );
                    if ( $percent >= 100 ) {
                    ?>
                    <div class="error">
                    <p>
                        <?php
                        printf( __( 'You have reached <strong>%d%% (%dMb)</strong> of your disk usage limit of <strong>%dMb</strong>.<br />' .
                        'You may lose the ability to upload files if you continue increasing your disk usage.' ), $percent, $used, $quota );
                        ?>
                    </p>
                    </div><?php
                }
            }
        }
    }

    /**
     * Populates and returns a list of inactive plugins
     *
     * @return array A list of inactive plugins
     */
    static function get_inactive_plugins() {
        // Make sure we have populated our plugin information
        self::populate_all_plugins();
        return self::$inactive_plugins;
    }

    /**
     * Gets a list of nondefault inactive plugins
     *
     * @return array A list of nondefault inactive plugins
     */
    static function get_nondefault_inactive_plugins() {
        $inactive_plugins = self::get_inactive_plugins();
        $default_plugins = array( 'akismet/akismet.php', 'hello.php', 'hello-dolly/hello.php' );
        $ignored_plugins = get_option( self::IGNORED_PLUGINS_OPTION_NAME, '' );
        $ignored_plugins = preg_split( '/[\s,]/', $ignored_plugins );
        $nondefault_inactive_plugin_slugs = array_diff( array_keys( $inactive_plugins ), $default_plugins );
        $nondefault_inactive_plugin_slugs = array_diff( $nondefault_inactive_plugin_slugs, $ignored_plugins );
        return $nondefault_inactive_plugin_slugs;
    }

    /**
     * Populates and returns a list of all installed plugins
     *
     * @return array A list of all installed plugins
     */
    static function get_all_plugins() {
        // Make sure we have populated our plugin information
        self::populate_all_plugins();
        return self::$current_plugins;
    }

    /**
     * @static
     * Ensure that all our plugin variables are populated
     * @param $use_cache boolean If false, values will be rewritten
     */
    private static function populate_all_plugins( $use_cache = true ) {
        // Make sure we have the plugin functions
        require_once( ABSPATH . 'wp-admin/includes/admin.php' );

        if ( is_null( self::$current_plugins ) || !$use_cache ) {
            self::$current_plugins = get_plugins();
        }

        if ( is_null( self::$inactive_plugins ) || !$use_cache ) {
            $inactive_plugins = array();
            foreach ( self::$current_plugins as $path => $plugin ) {
                if ( is_plugin_inactive( $path ) ) {

                    // Construct a list of only inactive plugins
                    $inactive_plugins[$path] = $plugin;

                    // Update this plugin's status to inactive
                    self::$current_plugins[$path]['Active'] = false;
                } else {
                    // Update this plugin's status to active
                    self::$current_plugins[$path]['Active'] = true;
                }
            }
            self::$inactive_plugins = $inactive_plugins;
        }
    }

    /**
     * Populates and returns a list of inactive themes
     *
     * @return array A list of inactive themes
     */
    private static function get_inactive_themes() {
        self::populate_all_themes();
        return self::$inactive_themes;
    }

    /**
     * Gets a list of nondefault inactive themes
     *
     * @return array A list of nondefault inactive themes
     */
    private static function get_nondefault_inactive_themes() {
        $inactive_themes = self::get_inactive_themes();
        $default_themes = array( 'classic', 'default', 'twentyten', 'twentyeleven', 'twentytwelve', 'twentythirteen', 'twentyfourteen', 'breeze','canvas', 'thesis', 'sommerce', 'gonzo', 'minimum', 'bp-default', 'delegate', 'detube', 'maxx-wp', 'thematic', 'pagelines', 'suffusion', 'wishlistproducts', 'WPWishlist_Bonus01-txt', 'hybrid', 'lifestyle-parent', 'mobileboro', 'omgunified', 'whitelight', 'justlanded', 'required-foundation', 'Builder', 'superstore', 'organic_shop', 'highwind', 'prototype', 'hottopix', 'jumpstart', 'flatline', 'catch-evolution-pro', 'bayside', 'Avada', 'responsive', 'zine', 'Lucid', 'enfold', 'flare', 'wpex-thunder', 'Nexus', 'bouncy-wp', 'rcibase', 'thunder', 'spectrum', 'qskin', 'optimizePressTheme', 'flatsome', 'mobile', 'wizard', 'dante', 'atahualpa', 'x', 'wt_tera', 'buddyboss', 'problog-codebase', 'legenda', 'hustle', 'jobify', 'canvas', 'expressivo', 'carrington-business', 'Divi', 'kleo', 'bucket', 'multinews', 'surfarama', 'eighties', 'simplemag', 'Newspaper', 'sheeva', 'function', 'salbii', 'dt-the7', 'ghbase', 'wp_santorini5-v1.3', 'porto', 'truemag', 'marketify', 'u-design', 'shopkeeper', 'medicenter', 'tsl', 'WCM010005', 'rttheme19', 'smart-mag', 'joyn', 'theretailer', 'voice', 'salient', 'engine', 'dunamis', 'reviver', 'jollyany', 'storefront', 'the-feed-theme', 'dms', 'pinspro', 'iblogpro6', 'thereview', 'volatyl', 'presso', 'upfront', 'newsroom14', 'corpus', 'themebase-two', 'fearless', 'tourpackage', 'jay', 'magazine-premium', 'incrediblewp', 'the-review', 'zippy-courses-theme', 'admag', 'Extra', 'listable', 'OneThemeMobile', 'valenti', 'happychild', 'sahifa' );

        if ( is_child_theme() ) {
            $default_themes[] = get_template();
        }

        $ignored_themes = get_option( self::IGNORED_THEMES_OPTION_NAME );
        $ignored_themes = preg_split( '/[\s,]/', $ignored_themes );

        $nondefault_inactive_theme_slugs = array_diff( array_keys( $inactive_themes ), $default_themes );
        $nondefault_inactive_theme_slugs = array_diff( $nondefault_inactive_theme_slugs, $ignored_themes );

        return $nondefault_inactive_theme_slugs;
    }

    /**
     * @static
     * Ensure that all theme variables are populated
     * @param $use_cache boolean If false, values will be rewritten
     */
    private static function populate_all_themes( $use_cache = true ) {
        if ( is_null( self::$current_themes ) || $use_cache ) {
            self::$current_themes = wp_get_themes();
        }

        if ( is_null( self::$inactive_themes ) || ! $use_cache ) {
            $current_theme = wp_get_theme()->get_stylesheet();
            self::$inactive_themes = self::$current_themes;
            unset( self::$inactive_themes[$current_theme] );
        }
    }

    /**
     * Save info about the last completed snapshot into the options table.
     *
     * @param $snapshot array The snapshot to save
     */
    private static function save_db_snapshot( $snapshot ) {
        update_option( Synthesis_DB_Backup::DB_SNAPSHOT_OPTION_NAME, $snapshot );
    }

    /**
     * Get the path to the last completed database snapshot. Returns false if none exists
     *
     * @return array|bool The  the database snapshot, or false if none exists
     */
    private static function get_db_snapshot() {
        $snapshot = get_option( Synthesis_DB_Backup::DB_SNAPSHOT_OPTION_NAME, false );
        return $snapshot;
    }

    /**
     * Queues synthesis styles on the Software Monitor admin page
     *
     * @param $hook string the name of the page being loaded
     */
    public static function add_styles( $hook ) {
        if ( $hook == 'toplevel_page_sp-software-monitor' || $hook == 'studiopress_page_sp-site-tools' ) {
            wp_enqueue_style( 'synthesis-management', plugin_dir_url( __FILE__ ) . 'css/synthesis.css', array(), self::VERSION );
        }
    }

    public static function bytesToSize($bytes, $precision = 2)
        {
            $kilobyte = 1024;
            $megabyte = $kilobyte * 1024;
            $gigabyte = $megabyte * 1024;
            $terabyte = $gigabyte * 1024;

            if (($bytes >= 0) && ($bytes < $kilobyte)) {
                return $bytes . ' B';

            } elseif (($bytes >= $kilobyte) && ($bytes < $megabyte)) {
                return round($bytes / $kilobyte, $precision) . ' KB';

            } elseif (($bytes >= $megabyte) && ($bytes < $gigabyte)) {
                return round($bytes / $megabyte, $precision) . ' MB';

            } elseif (($bytes >= $gigabyte) && ($bytes < $terabyte)) {
                return round($bytes / $gigabyte, $precision) . ' GB';

            } elseif ($bytes >= $terabyte) {
                return round($bytes / $terabyte, $precision) . ' TB';
            } else {
                return $bytes . ' B';
            }
        }

    public static function get_duration_string( $start, $end ) {
        $length = $end - $start;
        $length_hours = floor( $length / 3600 );
        $length_minutes = floor( ( $length - ( $length_hours * 3600 ) ) / 60 );
        $length_seconds = $length - ( $length_hours * 3600 ) - ( $length_minutes * 60 );

        $length_hours_string = $length_hours ? sprintf( _n( '%d hour', '%d hours', $length_hours ), $length_hours ) : '';
        $length_minutes_string = $length_minutes || $length_hours ? sprintf( _n( '%d minute', '%d minutes', $length_minutes ), $length_minutes ) : '';
        $length_seconds_string = sprintf( _n( '%d second', '%d seconds', $length_seconds ), $length_seconds );

        $length_string = trim( "$length_hours_string $length_minutes_string $length_seconds_string" );
        return $length_string;
    }

    public static function get_memory_usage() {
        if ( !is_admin() && !get_transient( 'synthesis_memory_check' ) ) {
            $memusage = memory_get_usage();
            $memusage = self::bytesToSize( $memusage );
            set_transient ( 'synthesis_memory_check', $memusage, 12 * HOUR_IN_SECONDS );
        }
    }

    /**
     * Check WordPress core update methods and set it based on UI.
     */
    public static function sp_wp_core_update_settings() {
        if ( ! defined( 'WP_AUTO_UPDATE_CORE' ) && ! defined( 'AUTOMATIC_UPDATER_DISABLED' ) ) {
            switch ( get_option( self::SP_WP_CORE_UPDATE_OPTION_NAME ) ) {
                case 'major':
                    add_filter( 'allow_major_auto_core_updates', '__return_true' );
                    break;
                case 'disabled':
                    add_filter( 'automatic_updater_disabled', '__return_true' );
                    break;
                default:
                    add_filter( 'allow_minor_auto_core_updates', '__return_true' );
                    break;
            }
        }
    }


    public static function plugin_monitor_markup() {
        $inactive_plugins = array();
        $outdated_plugins = array();

        $current = get_site_transient( 'update_plugins' );

        if ( is_admin() ) {
            foreach( get_plugins() as $path => $plugin ) {
                if ( is_plugin_inactive( $path ) ) {
                    $inactive_plugins[] = $plugin['Name'];

                    if ( isset( $current->response[ $path ] ) ) {
                        $outdated_plugins[ $path ] = $plugin;
                    }

                }
            }

            if ( sizeof( $inactive_plugins ) > 0 ) {
                echo '<p>';
                _e( sprintf( "You have %d inactive plugin(s). You should delete them to keep your site secure.", count( $inactive_plugins) ) );
                echo '</p>';
            }

            if ( sizeof( $outdated_plugins ) > 0 ) {
                echo '</p>';
                _e( sprintf( "You have %d outdated plugins(s). You should update them to keep your site secure.", count( $outdated_plugins ) ) );
                echo '</p>';
                echo '<table><tbody>';
                foreach( $outdated_plugins as $path => $plugin ) {
                    wp_plugin_update_row( $path, $plugin );
                }
                echo '</tbody></table>';
            }

            if ( sizeof( $inactive_plugins ) == 0 && sizeof( $outdated_plugins ) ==0 ) {
                _e( 'All is well. You have no inactive or outdated plugins!' );
            }

            print "</p>";
        }
    }
}

Synthesis_Software_Monitor::start();
