<?php
/**
 * Modify the Genesis breadcrumb functionality.
 *
 * @since 0.9.0
 */
class Genesis_SEO_Breadcrumb {

	public function __construct() {

		add_filter( 'genesis_post_crumb', array( $this, 'post_crumb' ), 10, 2 );

	}

	public function post_crumb( $crumb, $args ) {

		if ( ! genesis_get_option( 'breadcrumb_single' ) ) {
			return $crumb;
		}

		$title = genesis_get_custom_field( '_genesis_breadcrumb_title' ) ? genesis_get_custom_field( '_genesis_breadcrumb_title' ) : single_post_title( '', false );

		return $cat_crumb . $title;

	}

}
