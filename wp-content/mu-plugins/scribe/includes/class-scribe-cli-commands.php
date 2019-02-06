<?php

// If we're running from WP CLI, add our CLI commands.
if ( defined( 'WP_CLI' ) && WP_CLI ) {
    class Scribe_Commands extends WP_CLI_Command {
        /**
         * @synopsis <add> --name=<required> --value=<required>
         * @subcommand settings
         */
        function settings( $args, $assoc_args ) {
            $settings = get_option( '_scribe_settings' );
            $name = $assoc_args['name'];
            $value = $assoc_args['value'];

            if ( isset( $settings[ $name ] ) && $settings[ $name ] == $value ) {
                WP_CLI::success( 'The value of ' . $name . 'is already set to ' . $value );
                exit(0);
            }

            $settings[ $name ] = $value;

            if ( update_option( '_scribe_settings', $settings ) ) {
                WP_CLI::success( 'The option has been successfully added.' );
            } else {
                WP_CLI::error( 'No update has been applied.' );
            }
        }
    }

    WP_CLI::add_command( 'scribe', 'Scribe_Commands' );
}
