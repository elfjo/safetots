<?php
/**
 * Genesis SEO Entry Metaboxes class.
 *
 * @since 0.9.0
 */
class Genesis_SEO_Entry_Metaboxes {

	public function __construct() {

		add_action( 'admin_menu', array( $this, 'register_metaboxes' ) );
		add_action( 'save_post', array( $this, 'seo_metabox_save' ), 1, 2 );

	}

	public function register_metaboxes() {

		foreach ( (array) get_post_types( array( 'public' => true ) ) as $type ) {
			if ( post_type_supports( $type, 'genesis-seo' ) ) {
				add_meta_box( 'genesis_seo_inpost_metabox', __( 'SEO Settings', 'genesis-seo' ), array( $this, 'seo_metabox' ), $type, 'normal', 'high' );
			}
		}

	}

	public function seo_metabox( $post ) {

		wp_nonce_field( 'genesis_inpost_seo_save', 'genesis_inpost_seo_nonce' );
		require_once( Genesis_Advanced_SEO()->plugin_dir_path . 'includes/views/metaboxes/entry-metabox-seo.php' );

	}

	public function seo_metabox_save( $post_id, $post ) {

		if ( ! isset( $_POST['genesis_seo'] ) ) {
			return;
		}

		//* Merge user submitted options with fallback defaults
		$data = wp_parse_args( $_POST['genesis_seo'], array(
			'_genesis_title'            => '',
			'_genesis_breadcrumb_title' => '',
			'_genesis_description'      => '',
			'_genesis_keywords'         => '',
			'_genesis_canonical_uri'    => '',
			'redirect'                  => '',
			'_genesis_noindex'          => 0,
			'_genesis_nofollow'         => 0,
			'_genesis_noarchive'        => 0,
		) );

		//* Sanitize the title, description, and tags
		foreach ( (array) $data as $key => $value ) {
			if ( in_array( $key, array( '_genesis_title', '_genesis_description', '_genesis_keywords' ) ) ) {
				$data[ $key ] = strip_tags( $value );
			}
		}

		genesis_save_custom_fields( $data, 'genesis_inpost_seo_save', 'genesis_inpost_seo_nonce', $post );

	}

	public function get_custom_field( $field, $post_id = null, $default = '' ) {

		$value = genesis_get_custom_field( $field, $post_id );

		if ( empty ( $value ) ) {
			return $default;
		}

		return $value;

	}

}
