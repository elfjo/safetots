<?php
$image = genesis_get_custom_field( 'genesis_schema_publisher_image' );

if ( ! $image || ! filter_var( $image, FILTER_VALIDATE_URL ) ) {
	return;
}

$image = esc_url_raw( $image );

$image_size = getimagesize( esc_url_raw( genesis_get_custom_field( 'genesis_schema_publisher_image' ) ) );
?>
<div <?php echo genesis_attr( 'entry-publisher-object' ); ?>>
<h2 itemprop="name"><?php echo esc_html( genesis_get_custom_field( 'genesis_schema_publisher_name' ) ); ?></h2>
<p itemprop="description">
<span itemprop="<?php echo 'Person' == genesis_get_custom_field( 'genesis_schema_publisher_type' ) ? 'image' : 'logo'; ?>" itemscope itemtype="http://schema.org/ImageObject">
<img src="<?php echo esc_url( genesis_get_custom_field( 'genesis_schema_publisher_image' ) ); ?>" class="alignleft" />
<meta itemprop="url" content="<?php echo esc_url( genesis_get_custom_field( 'genesis_schema_publisher_image' ) ); ?>" />
<meta itemprop="width" content="<?php echo esc_attr( $image_size[0] ); ?>" />
<meta itemprop="height" content="<?php echo esc_attr( $image_size[1] ); ?>" />
</span>
<?php genesis_custom_field( 'genesis_schema_publisher_description' ); ?>
</p>
</div>
