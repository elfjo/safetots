<?php
/**
 * Main Genesis SEO plugin class.
 *
 * @since 0.9.0
 */
class Genesis_SEO_Open_Graph {

	public function __construct() {

		add_action( 'wp_head', array( $this, 'og_tags' ) );
		add_filter( 'user_contactmethods', array( $this, 'contact_methods_filter' ) );

	}

	public function og_tags() {

		if ( is_singular() ) {
			require_once( Genesis_Advanced_SEO()->plugin_dir_path . 'includes/views/frontend/entry-open-graph.php' );
		}

	}

	public function get_custom_field( $field, $post_id = null, $default = '' ) {

		$post_id = empty( $post_id ) ? get_the_ID() : $post_id;

		if ( ! $post_id ) {
			return '';
		}

		$custom_field = get_post_meta( $post_id, $field, true );

		if ( empty ( $custom_field ) ) {
			return $default;
		}

		return is_array( $custom_field ) ? stripslashes_deep( $custom_field ) : stripslashes( wp_kses_decode_entities( $custom_field ) );

	}

	public function get_description( $post )  {

		$description = $this->get_custom_field( '_genesis_description' );

		if ( empty( $description ) ) {

			// Strip tags and shortcodes so the content truncation count is done correctly.
			$content = strip_tags( strip_shortcodes( $post->post_content ), '<script>,<style>' );

			// Remove inline styles / scripts.
			$content = trim( preg_replace( '#<(s(cript|tyle)).*?</\1>#si', '', $content ) );

			// Truncate $content to $max_char.
			$description = genesis_truncate_phrase( $content, 200 );

		}

		return $description;
	}

	public function get_first_category( $post ) {

		$cats = get_the_category();

		if ( isset( $cats[0] ) ) {
			return $cats[0]->name;
		}

		return '';

	}

	public function contact_methods_filter( $contactmethods ) {

		$contactmethods['facebook'] = __( 'Facebook Profile URL', 'genesis-advanced-seo' );

		return $contactmethods;

	}

}
