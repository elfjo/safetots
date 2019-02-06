<?php
if ( ! has_post_thumbnail() ) {
	return;
}

$image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
?>
<span <?php echo genesis_attr( 'entry-image-object' ); ?>>
<meta itemprop="url" content="<?php echo esc_url( $image[0] ); ?>">
<meta itemprop="width" content="<?php echo esc_attr( $image[1] ); ?>">
<meta itemprop="height" content="<?php echo esc_attr( $image[2] ); ?>">
</span>
