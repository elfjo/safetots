<?php
wp_nonce_field( 'genesis_simple_schema_save', 'genesis_simple_schema_nonce' );

$schema_types = array(
	'CreativeWork',
	'Article',
	'BlogPosting',
);
?>

<table class="form-table">
<tbody>

	<tr valign="top">
		<th scope="row"><label for=""><?php _e( 'Schema Type', 'genesis-simple-schema' ); ?></label></th>
		<td>
			<?php
			$this->schema_types_dropdown( array(
				'name'     => 'genesis_simple_schema[genesis_schema_type]',
				'class'    => '',
				'id'       => '',
				'selected' => genesis_get_custom_field( 'genesis_schema_type' ) ,
			) );
			?>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for=""><?php _e( 'Publisher Type', 'genesis-simple-schema' ); ?></label></th>
		<td>
			<p>
			<select name="genesis_simple_schema[genesis_schema_publisher_type]">
				<option value="Person" <?php selected( 'Person', genesis_get_custom_field( 'genesis_schema_publisher_type' ) ); ?>><?php _e( 'Person', 'genesis-simple-schema' ); ?></option>
				<option value="Organization" <?php selected( 'Organization', genesis_get_custom_field( 'genesis_schema_publisher_type' ) ); ?>><?php _e( 'Organization', 'genesis-simple-schema' ); ?></option>
			</select>
			</p>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for=""><?php _e( 'Publisher Information', 'genesis-simple-schema' ); ?></label></th>
		<td>
			<p>
			<span class="small"><?php _e( 'Name:' ); ?></span><br />
			<input class="large-text" type="text" name="genesis_simple_schema[genesis_schema_publisher_name]" id="" value="<?php echo esc_attr( genesis_get_custom_field( 'genesis_schema_publisher_name' ) ); ?>" />
			</p>
			<p>
			<?php _e( 'Description:' ); ?><br />
			<textarea class="widefat" name="genesis_simple_schema[genesis_schema_publisher_description]" id=""><?php echo esc_textarea( genesis_get_custom_field( 'genesis_schema_publisher_description' ) ); ?></textarea>
			</p>
			<p>
			<?php _e( 'Image/Logo URL:' ); ?><br />
			<input class="large-text" type="text" name="genesis_simple_schema[genesis_schema_publisher_image]" id="" value="<?php echo esc_attr( genesis_get_custom_field( 'genesis_schema_publisher_image' ) ); ?>" />
			</p>
		</td>
	</tr>
</tbody>
</table>
