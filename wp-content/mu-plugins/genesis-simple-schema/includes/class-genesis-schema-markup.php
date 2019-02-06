<?php

class Genesis_Schema_Markup {

	public function __construct() {

		add_filter( 'genesis_attr_entry', array( $this, 'entry_attributes' ), 999 );
		add_filter( 'genesis_attr_entry-image-object', array( $this, 'entry_image_object_attributes' ), 999 );
		add_filter( 'genesis_attr_entry-publisher-object', array( $this, 'entry_publisher_object_attributes' ), 999 );

		add_action( 'genesis_entry_content', array( $this, 'entry_image_object' ), 8 );
		add_action( 'genesis_entry_content', array( $this, 'entry_publisher_object' ), 20 );
		add_action( 'genesis_entry_content', array( $this, 'entry_modified_date_meta' ), 20 );
		add_action( 'genesis_entry_content', array( $this, 'entry_mainentityofpage_meta' ), 20 );

	}

	public function entry_attributes( $attributes ) {

		if ( ! is_main_query() && ! genesis_is_blog_template() ) {
			return $attributes;
		}

		$schema_type = genesis_get_custom_field( 'genesis_schema_type' );

		if ( ! $schema_type ) {
			return $attributes;
		}

		$attributes['itemscope'] = true;
		$attributes['itemtype']  = 'http://schema.org/' . $schema_type;

		return $attributes;

	}

	public function entry_image_object_attributes() {

		$attributes['itemprop']  = 'image';
		$attributes['itemscope'] = true;
		$attributes['itemtype']  = 'http://schema.org/ImageObject';

		return $attributes;

	}

	public function entry_publisher_object_attributes() {

		$attributes['itemprop']  = 'publisher';
		$attributes['itemscope'] = true;
		$attributes['itemtype']  = 'http://schema.org/' . genesis_get_custom_field( 'genesis_schema_publisher_type' );

		return $attributes;

	}

	public function entry_image_object() {

		if ( has_post_thumbnail() ) {
			require_once( Genesis_Simple_Schema()->plugin_dir_path . '/includes/views/entry-image-object.php' );
		}

	}

	public function entry_publisher_object() {

		if ( genesis_get_custom_field( 'genesis_schema_publisher_type' ) ) {
			require_once( Genesis_Simple_Schema()->plugin_dir_path . '/includes/views/entry-publisher-object.php' );
		}

	}

	public function entry_modified_date_meta() {

		printf( '<meta itemprop="dateModified" content="%s" />', get_the_modified_time( 'c' ) );

	}

	public function entry_mainentityofpage_meta() {

		printf( '<meta itemprop="mainEntityOfPage" content="%s" />', esc_url( get_permalink() ) );

	}

}
