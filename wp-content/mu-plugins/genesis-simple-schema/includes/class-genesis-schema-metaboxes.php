<?php
/**
 * Add metaboxes to entry edit screen.
 *
 * @since 0.9.0
 */
class Genesis_Schema_Metaboxes {

	/**
	 * The custom fields and their default values.
	 *
	 * @since 0.9.0
	 */
	public $custom_field_defaults = array(
		'genesis_schema_type'                  => 'CreativeWork',
		'genesis_schema_image'                 => 0,
		'genesis_schema_publisher_type'        => 'Person',
		'genesis_schema_publisher_author'      => 0,
		'genesis_schema_publisher_name'        => '',
		'genesis_schema_publisher_description' => '',
		'genesis_schema_publisher_image'       => '',
	);

	/**
	 * The custom field sanitization callbacks.
	 *
	 * @since 0.9.0
	 */
	public $custom_field_sanitization = array(
		'genesis_schema_type'                  => array( 'Genesis_Settings_Sanitizer', 'no_html' ),
		'genesis_schema_image'                 => array( 'Genesis_Settings_Sanitizer', 'one_zero' ),
		'genesis_schema_publisher_type'        => array( 'Genesis_Settings_Sanitizer', 'no_html' ),
		'genesis_schema_publisher_author'      => array( 'Genesis_Settings_Sanitizer', 'one_zero' ),
		'genesis_schema_publisher_name'        => array( 'Genesis_Settings_Sanitizer', 'no_html' ),
		'genesis_schema_publisher_description' => array( 'Genesis_Settings_Sanitizer', 'requires_unfiltered_html' ),
		'genesis_schema_publisher_image'       => array( 'Genesis_Settings_Sanitizer', 'url' ),
	);

	/**
	 * The main class constructor.
	 *
	 * @since 0.9.0
	 */
	public function __construct() {

		$this->register_meta();

		add_filter( 'is_protected_meta', array( $this, 'protect_meta' ), 10, 2 );

		add_action( 'add_meta_boxes', array( $this, 'add_metaboxes' ) );
		add_action( 'save_post', array( $this, 'metaboxes_save' ), 1, 2 );

	}

	/**
	 * Register the meta for the post types that support `genesis-simple-schema`.
	 *
	 * @since 0.9.0
	 */
	public function register_meta() {

		foreach ( (array) get_post_types( array( 'public' => true ) ) as $type ) {

			if ( ! post_type_supports( $type, 'genesis-simple-schema' ) ) {
				continue;
			}

			foreach ( (array) $this->custom_field_sanitization as $key => $sanitize_callback ) {
				register_meta( $type, $key, $sanitize_callback );
			}

		}

	}

	/**
	 * Filter function to protect our meta from being edited using the WordPress custom field editor.
	 *
	 * @since 0.9.0
	 */
	public function protect_meta( $protected, $meta_key ) {

		if ( array_key_exists( $meta_key, (array) $this->custom_field_defaults ) ) {
			return true;
		}

		return $protected;

	}

	/**
	 * Add metabox(es) to the post types that support `genesis-simple-schema`.
	 */
	public function add_metaboxes() {

		foreach ( (array) get_post_types( array( 'public' => true ) ) as $type ) {

			if ( post_type_supports( $type, 'genesis-simple-schema' ) ) {
				add_meta_box( 'genesis_schema_box', __( 'Entry Schema', 'genesis-simple-schema' ), array( $this, 'schema_metabox' ), $type, 'normal', 'high' );
			}

		}

	}

	/**
	 * Save the metabox option values as post meta.
	 *
	 * @since 0.9.0
	 */
	public function metaboxes_save( $post_id, $post ) {

		if ( ! post_type_supports( $post->post_type, 'genesis-simple-schema' ) ) {
			return;
		}

		if ( ! isset( $_POST['genesis_simple_schema'] ) ) {
			return;
		}

		$data = wp_parse_args( $_POST['genesis_simple_schema'], $this->custom_field_defaults );

		genesis_save_custom_fields( $data, 'genesis_simple_schema_save', 'genesis_simple_schema_nonce', $post );

	}

	/**
	 * Output the content of the schema metabox.
	 *
	 * 0.9.0
	 */
	public function schema_metabox() {

		require_once( Genesis_Simple_Schema()->plugin_dir_path . '/includes/views/metabox-schema.php' );

	}

	/**
	 * Generate the schema types dropdown.
	 *
	 * @since 0.9.0
	 */
	public function schema_types_dropdown( $args ) {

		$defaults = array(
			'name'     => '',
			'class'    => '',
			'id'       => '',
			'selected' => '',
		);

		$args = wp_parse_args( $args, $defaults );

		$output = sprintf( '<select name="%s" id="%s" class="%s">', $args['name'], $args['id'], $args['class'] );

		foreach ( (array) Genesis_Simple_Schema()->get_supported_schema_types() as $type ) {
			$output .= sprintf( '<option value="%s" %s>%s</option>', esc_html( $type ), selected( $type, $args['selected'], 0 ), esc_html( $type ) );
		}

		$output .= '</select>';

		echo $output;

	}

}
