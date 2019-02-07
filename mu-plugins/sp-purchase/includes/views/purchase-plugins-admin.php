<div class="wrap">

<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

<div class="theme-browser">
	<style type="text/css">
	.button.installing:before {
		color:#f56e28;
		content:"\f463";
		-webkit-animation:rotation 2s infinite linear;
		animation:rotation 2s infinite linear;
	}
	.button.installed:before {
		color:#79ba49;
		content:"\f147";
	}
	</style>
	<div class="themes wp-clearfix">

		<?php
		$plugins = array(
			'AffiliateWP' => array(
				'screenshot' => 'https://my.studiopress.com/images/screenshots/affiliatewp.jpg',
				'url'        => 'https://my.studiopress.com/details/affiliatewp.html',
				'path'       => 'affiliate-wp/affiliate-wp.php',
				'slug'       => 'affiliate-wp',
			),
			'Amp' => array(
				'screenshot' => 'https://my.studiopress.com/images/screenshots/amp.jpg',
				'url'        => 'https://my.studiopress.com/details/amp.html',
				'path'       => 'amp/amp.php',
				'slug'       => 'amp',
			),
			'Beaver Builder Lite' => array(
				'screenshot' => 'https://my.studiopress.com/images/screenshots/beaver-builder.jpg',
				'url'        => 'https://my.studiopress.com/details/beaver-builder.html',
				'path'       => 'beaver-builder-lite-version/fl-builder.php',
				'slug'       => 'beaver-builder-lite-version',
			),
			'Design Palette Pro' => array(
				'screenshot' => 'https://my.studiopress.com/images/screenshots/design-palette-pro.jpg',
				'url'        => 'https://my.studiopress.com/details/design-palette-pro.html',
				'path'       => 'genesis-palette-pro/genesis-palette-pro.php',
				'slug'       => 'genesis-palette-pro',
			),
			'Easy Digital Downloads'  => array(
				'screenshot' => 'https://my.studiopress.com/images/screenshots/easy-digital-downloads.jpg',
				'url'        => 'https://my.studiopress.com/details/easy-digital-downloads.html',
				'path'       => 'easy-digital-downloads/easy-digital-downloads.php',
				'slug'       => 'easy-digital-downloads',
			),
			'Ninja Forms' => array(
				'screenshot' => 'https://my.studiopress.com/images/screenshots/ninja-forms.jpg',
				'url'        => 'https://my.studiopress.com/details/ninja-forms.html',
				'path'       => 'ninja-forms/ninja-forms.php',
				'slug'       => 'ninja-forms',
			),
			'OptinMonster' => array(
				'screenshot' => 'https://my.studiopress.com/images/screenshots/optin-monster.jpg',
				'url'        => 'https://my.studiopress.com/details/optin-monster.html',
				'path'       => 'optinmonster/optin-monster-wp-api.php',
				'slug'       => 'optinmonster',
			),
			'Restrict Content Pro' => array(
				'screenshot' => 'https://my.studiopress.com/images/screenshots/restrict-content-pro.jpg',
				'url'        => 'https://my.studiopress.com/details/restrict-content-pro.html',
				'path'       => 'restrict-content-pro/restrict-content-pro.php',
				'slug'       => 'restrict-content-pro',
			),
			'Soliloquy Lite' => array(
				'screenshot' => 'https://my.studiopress.com/images/screenshots/soliloquy.jpg',
				'url'        => 'https://my.studiopress.com/details/soliloquy.html',
				'path'       => 'soliloquy-lite/soliloquy-lite.php',
				'slug'       => 'soliloquy-lite',
			),
			'WooCommerce' => array(
				'screenshot' => 'https://my.studiopress.com/images/screenshots/woocommerce.jpg',
				'url'        => 'https://my.studiopress.com/details/woocommerce.html',
				'path'       => 'woocommerce/woocommerce.php',
				'slug'       => 'woocommerce',
			),
			'WPForms Lite' => array(
				'screenshot' => 'https://my.studiopress.com/images/screenshots/wpforms.jpg',
				'url'        => 'https://my.studiopress.com/details/wpforms.html',
				'path'       => 'wpforms-lite/wpforms.php',
				'slug'       => 'wpforms-lite',
			),
		);

		foreach ( $plugins as $plugin => $details ) {

			$installed = file_exists( WP_PLUGIN_DIR . '/' . $details['path'] );
			$active    = is_plugin_active( $details['path'] );

			$install_button = sprintf(
				'<a class="%s" %s>%s</a>',
				'button sp-install-button',
				'data-slug="' . esc_attr( $details['slug'] ) . '" ' . 'data-path="' . esc_attr( $details['path'] ) . '"',
				__( 'Install', 'sp-purchase' )
			);

			$activate_button = sprintf(
				'<a href="%s" class="%s" %s>%s</a>',
				esc_url( wp_nonce_url( admin_url( 'plugins.php?action=activate&amp;plugin='. $details['path'] .'' ), 'activate-plugin_' . $details['path'] ) ),
				'button button-primary sp-activate-button',
				$installed ? '' : 'style="display: none;"',
				__( 'Activate', 'sp-purchase' )
			);

			$active_button = sprintf( '<a class="%s">%s</a>',
				'button disabled installed',
				__( 'Active', 'sp-purchase' )
			);
			?>

			<div class="theme">
				<div class="theme-screenshot">
					<img src="<?php echo esc_url( $details['screenshot'] ); ?>" />
				</div>
				<div class="theme-id-container">
					<h2 class="theme-name"><?php echo esc_html( $plugin ); ?></h2>
					<div class="theme-actions plugin-<?php echo $details['slug'] ?>" style="opacity: 1;">
						<a href="<?php echo esc_url( $details['url'] ); ?>" class="button" target="_blank">Details</a>
						<?php
						if ( $active ) {
							echo $active_button;
						}
						else if ( $installed ) {
							echo $activate_button;
						}
						else {
							echo $install_button;
							echo $activate_button;
						}
						?>
					</div>
				</div>
			</div>

		<?php } ?>

	</div>

</div>

</div>
