<!-- Genesis Advanced SEO Open Graph -->
<?php

global $post;
$image = wp_get_attachment_metadata( get_post_thumbnail_id( $post ) );

$og_tags = array(
	'og:site_name'           => esc_attr( get_bloginfo( 'name' ) ),
	'og:locale'              => esc_attr( get_locale() ),
	'og:type'                => 'article',
	'article:author'         => get_user_meta( $post->post_author, 'facebook', true ),
	'article:section'        => esc_attr( $this->get_first_category( $post ) ),
	'article:published_time' => esc_attr( get_the_time( 'Y-m-d H:i:s' ) ),
	'article:modified_time'  => esc_attr( get_the_modified_time( 'Y-m-d H:i:s' ) ),
	'og:updated_time'        => esc_attr( get_the_modified_time( 'Y-m-d H:i:s' ) ),
	'og:title'               => esc_attr( $this->get_custom_field( '_genesis_title', null, get_the_title( $post ) ) ),
	'og:description'         => esc_attr( $this->get_description( $post ) ),
	'og:url'                 => esc_url( genesis_canonical_url() ),
	'og:image:url'           => $image ? esc_url( get_the_post_thumbnail_url( $post, 'full' ) ) : '',
	'og:image:width'         => $image ? esc_attr( $image['width'] ) : '',
	'og:image:height'        => $image ? esc_attr( $image['height'] ) : '',
	'twitter:card'           => 'summary',
	'twitter:title'          => esc_attr( $this->get_custom_field( '_genesis_title', null, get_the_title( $post ) ) ),
	'twitter:description'    => esc_attr( $this->get_description( $post ) ),
	'twitter:image'          => $image ? esc_url( get_the_post_thumbnail_url( $post, 'full' ) ) : '',
);

foreach ( $og_tags as $property => $content ) {
	if ( $content ) {
		printf( '<meta property="%s" content="%s" />' . "\n", $property, $content );
	}
}
?>
<!-- End Genesis Advanced SEO Open Graph -->
