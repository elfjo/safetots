<?php
/**
 * Force Genesis JS to load with the async tag.
 *
 * @since 0.9.0
 */
class Genesis_SEO_JS_Async {

	public function __construct() {

		add_filter( 'script_loader_tag', array( $this, 'add_async_attr' ), 10, 2 );

	}

	public function add_async_attr( $tag, $handle ) {

		$handles = array(
			'superfish',
			'superfish-args',
			'superfish-compat',
			'skip-links',
			'drop-down-menu',
			'html5shiv',
		);

		if ( ! in_array( $handle, $handles ) ) {
			return $tag;
		}

		if ( strstr( $tag, 'async' ) ) {
			return $tag;
		}

		return str_replace( ' src', ' async src', $tag );

	}

}
