<?php
/**
 * A very simple sitemap generation tool. Generates a sitemap that includes homepage, posts, pages, and products.
 *
 * @since 0.9.0
 */
class Genesis_SEO_Sitemap {

	/**
	 * The location of the sitemap file.
	 */
	public $sitemap_file;

	public function __construct() {

		/**
		 * Set the location of the sitemap file.
		 */
		$this->sitemap_file = ABSPATH . 'sitemap.xml';

	}

	/**
	 * Generate the sitemap file. Whenever this is executed, the file will be (re)generated.
	 *
	 * @since 0.9.0
	 */
	public function generate_sitemap() {

		$this->write_file( $this->get_sitemap_content() );

	}

	/**
	 * Get the content of the sitemap file.
	 *
	 * @since 0.9.0
	 */
	public function get_sitemap_content() {

		$sitemap  = '<?xml version="1.0" encoding="UTF-8"?>';
		$sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		$sitemap .= sprintf( '<url><loc>%s</loc></url>', esc_url( home_url( '/' ) ) );
		$sitemap .= $this->get_sitemap_urls();
		$sitemap .= '</urlset>';

		return $sitemap;

	}

	/**
	 * Get all the sitemap URLs and format them for use.
	 *
	 * @since 0.9.0
	 */
	public function get_sitemap_urls() {

		$urls = '';

		$posts = get_posts( array(
			'post_type'      => array( 'post', 'page', 'product' ),
			'posts_per_page' => 50000,
			'order'          => 'DESC',
			'orderby'        => 'modified',
			'meta_query'     => array(
				array(
					'key' => '_genesis_noindex',
					'compare' => 'NOT EXISTS',
				),
			),
		) );

		if ( $posts ) {
			foreach ( $posts as $post ) {
				$urls .= $this->sitemap_url_sprintf( get_permalink( $post ), get_post_modified_time( 'Y-m-d\TH:i:s', 0, $post ) );
			}
		}

		return $urls;

	}

	/**
	 * A sprintf shortcut for properly marked up URLs.
	 *
	 * @since 0.9.0
	 */
	public function sitemap_url_sprintf( $loc, $lastmod = '' ) {

		return sprintf( '<url><loc>%s</loc><lastmod>%s</lastmod></url>', $loc, $lastmod );

	}

	/**
	 * Create (if necessary) and write sitemap content to file.
	 *
	 * @since 0.9.0
	 */
	public function write_file( $content ) {

		$fp = fopen( $this->sitemap_file, 'w' );
		fwrite( $fp, $content );
		fclose( $fp );

	}

}
