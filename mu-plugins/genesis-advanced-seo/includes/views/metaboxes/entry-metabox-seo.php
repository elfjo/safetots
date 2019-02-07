<table class="form-table">
<tbody>

	<tr valign="top">
		<th scope="row"><label for="genesis_title"><?php _e( 'Document Title', 'genesis-seo' ); ?> <span title="&lt;title&gt; Tag">[?]<span class="screen-reader-text"> &lt;title&gt; Tag. </span></span></label></th>
		<td>
			<p><input class="large-text" type="text" name="genesis_seo[_genesis_title]" id="genesis_title" value="<?php echo esc_attr( genesis_get_custom_field( '_genesis_title' ) ); ?>" /></p>
			<p><span class="hide-if-no-js description"><?php printf( __( 'Characters Used: %s', 'genesis-seo' ), '<span id="genesis_title_chars">'. mb_strlen( genesis_get_custom_field( '_genesis_title' ) ) .'</span>' ); ?></span></p>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="genesis_breadcrumb_title"><?php _e( 'Breadcrumb Title', 'genesis-seo' ); ?></label></th>
		<td>
			<p><input class="large-text" type="text" name="genesis_seo[_genesis_breadcrumb_title]" id="genesis_breadcrumb_title" value="<?php echo esc_attr( genesis_get_custom_field( '_genesis_breadcrumb_title' ) ); ?>" /></p>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="genesis_description"><?php _e( 'Meta Description', 'genesis-seo' ); ?> <span title="&lt;meta name=&quot;description&quot; /&gt;">[?]<span class="screen-reader-text"> &lt;meta name=&quot;description&quot; /&gt;. </span></span></label></th>
		<td>
			<p><textarea class="widefat" name="genesis_seo[_genesis_description]" id="genesis_description" rows="4" cols="4"><?php echo esc_textarea( genesis_get_custom_field( '_genesis_description' ) ); ?></textarea></p>
			<p><span class="hide-if-no-js description"><?php printf( __( 'Characters Used: %s', 'genesis-seo' ), '<span id="genesis_description_chars">'. mb_strlen( genesis_get_custom_field( '_genesis_description' ) ) .'</span>' ); ?></span></p>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="genesis_keywords"><strong><?php _e( 'Meta Keywords', 'genesis-seo' ); ?></strong> <span title="&lt;meta name=&quot;keywords&quot; /&gt;">[?]<span class="screen-reader-text"> &lt;meta name=&quot;keywords&quot; /&gt;. </span></span></label></th>
		<td><p><input class="large-text" type="text" name="genesis_seo[_genesis_keywords]" id="genesis_keywords" value="<?php echo esc_attr( genesis_get_custom_field( '_genesis_keywords' ) ); ?>" /></p></td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="genesis_canonical"><strong><?php _e( 'Canonical URL', 'genesis-seo' ); ?></strong> <a href="http://www.mattcutts.com/blog/canonical-link-tag/" target="_blank" title="&lt;link rel=&quot;canonical&quot; /&gt;">[?]<span class="screen-reader-text"> &lt;link rel=&quot;canonical&quot; /&gt;. <?php _e( 'Read more about', 'genesis-seo' ); ?> <?php _e( 'Custom Canonical URL', 'genesis-seo' ); ?>. <?php _e( 'Link opens in a new window.', 'genesis-seo' ); ?></span></a></label></th>
		<td><p><input class="large-text" type="text" name="genesis_seo[_genesis_canonical_uri]" id="genesis_canonical" value="<?php echo esc_url( genesis_get_custom_field( '_genesis_canonical_uri' ) ); ?>" /></p></td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="genesis_redirect"><strong><?php _e( 'Custom Redirect URL', 'genesis-seo' ); ?></strong> <a href="http://www.google.com/support/webmasters/bin/answer.py?hl=en&amp;answer=93633" target="_blank" title="301 Redirect">[?]<span class="screen-reader-text"> 301 Redirect. <?php _e( 'Read more about', 'genesis-seo' ); ?> <?php _e( 'Custom Redirect URL', 'genesis-seo' ); ?>. <?php _e( 'Link opens in a new window.', 'genesis-seo' ); ?></span></a></label></th>
		<td><p><input class="large-text" type="text" name="genesis_seo[redirect]" id="genesis_redirect" value="<?php echo esc_url( genesis_get_custom_field( 'redirect' ) ); ?>" /></p></td>
	</tr>

	<tr valign="top">
		<th scope="row"><?php _e( 'Robots Meta Settings', 'genesis-seo' ); ?></th>
		<td>
			<p>
				<label for="genesis_noindex"><input type="checkbox" name="genesis_seo[_genesis_noindex]" id="genesis_noindex" value="1" <?php checked( genesis_get_custom_field( '_genesis_noindex' ) ); ?> />
				<?php printf( __( 'Apply %s to this post/page', 'genesis-seo' ), genesis_code( 'noindex' ) ); ?> <a href="http://yoast.com/articles/robots-meta-tags/" target="_blank">[?]<span class="screen-reader-text"> <?php _e( 'Read more about', 'genesis-seo' ); ?> noindex. <?php _e( 'Link opens in a new window.', 'genesis-seo' ); ?></span></a></label><br />
			</p>
			<p>
				<label for="genesis_nofollow"><input type="checkbox" name="genesis_seo[_genesis_nofollow]" id="genesis_nofollow" value="1" <?php checked( genesis_get_custom_field( '_genesis_nofollow' ) ); ?> />
				<?php printf( __( 'Apply %s to this post/page', 'genesis-seo' ), genesis_code( 'nofollow' ) ); ?> <a href="http://yoast.com/articles/robots-meta-tags/" target="_blank">[?]<span class="screen-reader-text"> <?php _e( 'Read more about', 'genesis-seo' ); ?> nofollow. <?php _e( 'Link opens in a new window.', 'genesis-seo' ); ?></span></a></label><br />
			</p>
			<p>
				<label for="genesis_noarchive"><input type="checkbox" name="genesis_seo[_genesis_noarchive]" id="genesis_noarchive" value="1" <?php checked( genesis_get_custom_field( '_genesis_noarchive' ) ); ?> />
				<?php printf( __( 'Apply %s to this post/page', 'genesis-seo' ), genesis_code( 'noarchive' ) ); ?> <a href="http://yoast.com/articles/robots-meta-tags/" target="_blank">[?]<span class="screen-reader-text"> <?php _e( 'Read more about', 'genesis-seo' ); ?> noarchive. <?php _e( 'Link opens in a new window.', 'genesis-seo' ); ?></span></a></label>
			</p>
		</td>
	</tr>

</tbody>
</table>
