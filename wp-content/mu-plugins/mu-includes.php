<?php
/**
 * The main MU plugin loader.
 */

if ( ! defined( 'IS_SP_SITES' ) ) {
    if ( preg_match( '/s(pe\d{2,}-|ve)\d{1,}.spsites.net/', gethostname() ) ) {
        define( 'IS_SP_SITES', true );
    } else {
        define( 'IS_SP_SITES', false );
    }
}

if ( ! defined( 'IS_SYNTHESIS' ) ) {
    if ( preg_match( '/\.wsynth\.net/', gethostname() ) ) {
        define( 'IS_SYNTHESIS', true );
    } else {
        define( 'IS_SYNTHESIS', false );
    }
}

if ( preg_match( '/spe\d{2,}-\d{1,}.spsites.net/', gethostname() ) ) {
    define( 'IS_SP_SHARED', true );
} else {
    define( 'IS_SP_SHARED', false );
}


if ( ! IS_SP_SITES && ! IS_SYNTHESIS ) {
    return false;
}

/**
  * The constants below are still here just to avoid a massive search and replace.
  */

if ( ! defined( 'SYNTHESIS_SITE_PLUGIN_DIR' ) ) {
    define( 'SYNTHESIS_SITE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) . '' );
}

if ( ! defined( 'SYNTHESIS_SITE_PLUGIN_URL' ) ) {
    define( 'SYNTHESIS_SITE_PLUGIN_URL', plugin_dir_url( __FILE__ ) . '' );
}

if ( ! defined( 'SYNTHESIS_CHILD_PLUGIN_DIR' ) ) {
    define( 'SYNTHESIS_CHILD_PLUGIN_DIR', SYNTHESIS_SITE_PLUGIN_DIR . '' );
}

if ( ! defined( 'SYNTHESIS_SITE_TOOLS_DIR' ) ) {
    define( 'SYNTHESIS_SITE_TOOLS_DIR', SYNTHESIS_SITE_PLUGIN_DIR . '' );
}

if ( ! defined( 'SYNTHESIS_CHILD_PLUGIN_INCLUDES_DIR' ) ) {
    define( 'SYNTHESIS_CHILD_PLUGIN_INCLUDES_DIR', SYNTHESIS_CHILD_PLUGIN_DIR . 'includes/' );
}

if ( ! defined( 'SYNTHESIS_SITE_PLUGIN_JS_URL' ) ) {
    define( 'SYNTHESIS_SITE_PLUGIN_JS_URL', SYNTHESIS_SITE_PLUGIN_URL . 'js/' );
}

/* Trick to manipulate the submenu position */
$plugins = array( 0 => '' );
$disallowed_plugins = array();

if ( ! IS_SP_SITES ) {
    $disallowed_plugins[] = "sp-clear-cache";
}

/**
 * Automatically load all the plugins inside mu-plugins
 */

foreach ( array( plugin_dir_path( __FILE__ ) ) as $plugin_dir ) {

    if ( ! is_dir( $plugin_dir ) || ! $dh = opendir( $plugin_dir ) )
        continue;

    while ( ( $plugin_dir = readdir( $dh ) ) !== false ) {

            if ( in_array( $plugin_dir, $disallowed_plugins ) ) {
                continue;
            }

            $plugin = plugin_dir_path( __FILE__ ) . $plugin_dir . "/" . $plugin_dir . ".php";

            if ( is_dir( plugin_dir_path( __FILE__ ) . $plugin_dir ) && ( ! preg_match( '/\.{1,2}/', $plugin_dir ) ) ) {
                if ( is_file( $plugin ) ) {
                    if ( preg_match( '/sp-software-monitor/', $plugin ) ) {
                        $plugins[0] = $plugin;
                        continue;
                    }

                    $plugins[] = $plugin;
                }
            }
    }

    closedir( $dh );
}

foreach ( $plugins as $plugin ) {
	require_once( $plugin );
}

add_filter( 'custom_menu_order', 'security_submenu_order' );

function security_submenu_order( $menu_ord ) {
    global $submenu;

    $sp_menus = $submenu['sp-software-monitor'];

    $key = null;

    foreach( (array) $sp_menus as $i => $menu ) {
        if ( array_search( 'security', $menu ) ) {
            $key = $i;
        }
    }

    if ( ! is_null( $key ) ) {
        $sp_menus[] = $sp_menus[ $key ];
        unset( $sp_menus[ $key ] );

        $submenu['sp-software-monitor'] = $sp_menus;
    }

    return $menu_ord;
}
