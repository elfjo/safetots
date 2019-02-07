<?php

/**
 * Manage Genesis SEO Commands
 */
class Genesis_SEO_CLI_Command extends WP_CLI_Command {

	/**
	 * Generate a robots.txt file based on SEO settings.
	 *
	 * ## EXAMPLES
	 *
	 *  $ wp genesis-seo robots
	 *  Success: robots.txt file generated successfully.
	 *
	 * @subcommand robots
	 *
	 * @since 0.9.0
	 *
	 * @param array $args       Positional arguments.
	 * @param array $assoc_args Stores all the arguments defined like --key=value or --flag or --no-flag.
	 */
	public function robots( $args, $assoc_args ) {

		Genesis_Advanced_SEO()->robots->write_file();

		WP_CLI::success( __( 'robots.txt file generated successfully.', 'genesis-advanced-seo' ) );

	}

	/**
	 * Generate a sitemap.xml file.
	 *
	 * ## EXAMPLES
	 *
	 *  $ wp genesis-seo sitemap
	 *  Success: sitemap.xml file generated successfully.
	 *
	 * @subcommand sitemap
	 *
	 * @since 0.9.0
	 *
	 * @param array $args       Positional arguments.
	 * @param array $assoc_args Stores all the arguments defined like --key=value or --flag or --no-flag.
	 */
	public function sitemap( $args, $assoc_args ) {

		Genesis_Advanced_SEO()->sitemap->generate_sitemap();

		WP_CLI::success( __( 'sitemap.xml file generated successfully.', 'genesis-advanced-seo' ) );

	}

}
