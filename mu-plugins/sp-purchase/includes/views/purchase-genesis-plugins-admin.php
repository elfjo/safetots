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
	.theme-name svg {
		position: absolute;
		left: 2px;
		top: 7px;
		width: 35px;
	}

	.theme-browser .theme .theme-name.genesis-svg {
		padding-left: 38px;
	}

	</style>
	<div class="themes wp-clearfix">

		<?php

		$genesissvg = '<svg focusable="false" tabindex="0" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 150 150" style="enable-background:new 0 0 150 150;" xml:space="preserve"><linearGradient id="SVGID_1_" gradientUnits="userSpaceOnUse" x1="87.5474" y1="652.8679" x2="62.2423" y2="729.6869" gradientTransform="matrix(1 0 0 1 0 -616.4)"><stop offset="0" style="stop-color:#263238"></stop><stop offset="1" style="stop-color:#131719"></stop> </linearGradient><path class="st0" d="M123.2,79.5c-0.8,2.3-1.7,4.2-2.9,6c1.2-4.4,1.7-9,1.4-13.9c-1.7-23-20.9-41.2-44.5-41.2 c-14.7,0-27.9,7.2-35.9,18.2c-0.4,0.7-1,1.3-1.4,2.1c-2.7,4.3-4.6,8.9-5.6,13.7c-0.1-4,0.4-8.4,1.7-12.9c-4.1,0.5-7.5,1.5-10.1,3 l0,0c-2.3,1.3-4,2.9-4.9,4.8l0,0c-0.1,0.3-0.2,0.5-0.3,0.9c-0.1,0.3-0.2,0.8-0.2,1.1c0,0.1,0,0.2,0,0.3c0,0.2-0.1,0.5-0.1,0.8 c0,0.1,0,0.2,0,0.3c0,0.2,0,0.5,0,0.8c0,0.1,0,0.2,0,0.3c0,0.2,0.1,0.5,0.1,0.8c0,0.1,0.1,0.2,0.1,0.3c0.1,0.3,0.1,0.5,0.2,0.9 c0,0.1,0.1,0.2,0.1,0.3c0.1,0.3,0.2,0.5,0.3,0.9c0,0.1,0.1,0.2,0.1,0.3c0.1,0.3,0.3,0.7,0.4,1c0,0.1,0.1,0.2,0.1,0.2 c0.2,0.3,0.3,0.7,0.5,1c0,0.1,0.1,0.1,0.1,0.2c0.2,0.3,0.4,0.7,0.8,1.1c0,0.1,0.1,0.1,0.1,0.2c0.2,0.3,0.5,0.8,0.9,1.1l0.1,0.1 c0.3,0.4,0.7,0.8,1,1.2l0.1,0.1c0.3,0.4,0.8,0.8,1.1,1.2c0,0,0,0,0.1,0.1c0.4,0.4,0.9,0.9,1.3,1.2l0,0c0.4,0.4,1,0.9,1.4,1.3l0,0 c0.5,0.4,1,0.9,1.5,1.3l0,0c0.5,0.4,1.1,0.9,1.6,1.3c8.6,6.5,21,12.8,35.6,17.4c2.6,0.9,5.3,1.6,7.8,2.3c-16.2-2.2-29.7-5-42.2-13 c5.1,19.1,22.4,33.1,43.1,33.1c14.5,0,27.3-6.9,35.5-17.7c8.5-1.1,14.2-4,15.7-8.8C129.8,89.1,127.7,84.3,123.2,79.5z M72.5,78.4 c0.3,1,0.9,1.8,1.4,2.5c0.7,0.7,1.3,1.2,2.2,1.5c0.9,0.3,1.7,0.5,2.7,0.5c0.8,0,1.4-0.1,2.1-0.2c0.5-0.1,1.1-0.3,1.6-0.5v-3.6h-2.4 c-0.3,0-0.7-0.1-0.9-0.3c-0.2-0.2-0.3-0.4-0.3-0.7v-3h8.7v10c-0.7,0.4-1.2,0.9-1.8,1.2c-0.7,0.3-1.3,0.5-2.1,0.8 c-0.8,0.2-1.5,0.3-2.4,0.4c-0.9,0.1-1.7,0.1-2.7,0.1c-1.7,0-3.4-0.3-4.8-0.9c-1.5-0.7-2.7-1.4-3.8-2.5s-2-2.4-2.5-3.8 c-0.7-1.5-0.9-3-0.9-4.8c0-1.7,0.3-3.4,0.9-4.9c0.5-1.5,1.4-2.7,2.5-3.8s2.4-2,3.9-2.5c1.5-0.7,3.3-0.9,5.2-0.9c1,0,2,0.1,2.8,0.2 c0.9,0.2,1.6,0.4,2.4,0.7c0.8,0.3,1.4,0.7,2.1,1c0.7,0.4,1.2,0.9,1.6,1.3l-1.6,2.4c-0.1,0.2-0.3,0.4-0.5,0.5 c-0.2,0.1-0.4,0.2-0.7,0.2c-0.3,0-0.7-0.1-1-0.3c-0.4-0.2-0.9-0.4-1.2-0.7c-0.4-0.2-0.8-0.3-1.2-0.4c-0.4-0.1-0.9-0.2-1.3-0.2 c-0.4,0-1-0.1-1.5-0.1c-1.1,0-2,0.2-2.8,0.5c-0.9,0.3-1.5,0.9-2.1,1.5c-0.5,0.7-1,1.4-1.3,2.4c-0.3,1-0.4,2-0.4,3.1 C72,76.2,72.2,77.4,72.5,78.4z"></path></svg>';

		$plugins = array(
			'<span class="screen-reader-text">Genesis</span>Simple Edits' => array(
				'svg'        => $genesissvg,
				'screenshot' => 'https://my.studiopress.com/images/screenshots/sps-edits.jpg',
				'url'        => 'https://wordpress.org/plugins/genesis-simple-edits/',
				'path'       => 'genesis-simple-edits/plugin.php',
				'slug'       => 'genesis-simple-edits',
			),
			'Simple Social Icons' => array(
				'svg'        => '',
				'screenshot' => 'https://my.studiopress.com/images/screenshots/sps-icons.jpg',
				'url'        => 'https://wordpress.org/plugins/simple-social-icons/',
				'path'       => 'simple-social-icons/simple-social-icons.php',
				'slug'       => 'simple-social-icons',
			),
			'<span class="screen-reader-text">Genesis</span>Simple FAQ' => array(
				'svg'        => $genesissvg,
				'screenshot' => 'https://my.studiopress.com/images/screenshots/sps-faq.jpg',
				'url'        => 'https://wordpress.org/plugins/genesis-simple-faq/',
				'path'       => 'genesis-simple-faq/genesis-simple-faq.php',
				'slug'       => 'genesis-simple-faq',
			),
			'<span class="screen-reader-text">Genesis</span>Simple Hooks' => array(
				'svg'        => $genesissvg,
				'screenshot' => 'https://my.studiopress.com/images/screenshots/sps-hooks.jpg',
				'url'        => 'https://wordpress.org/plugins/genesis-simple-hooks/',
				'path'       => 'genesis-simple-hooks/plugin.php',
				'slug'       => 'genesis-simple-hooks',
			),
			'<span class="screen-reader-text">Genesis</span>Simple Share' => array(
				'svg'        => $genesissvg,
				'screenshot' => 'https://my.studiopress.com/images/screenshots/sps-share.jpg',
				'url'        => 'https://wordpress.org/plugins/genesis-simple-share/',
				'path'       => 'genesis-simple-share/plugin.php',
				'slug'       => 'genesis-simple-share',
			),
			'<span class="screen-reader-text">Genesis</span>Connect for WooCommerce' => array(
				'svg'        => $genesissvg,
				'screenshot' => 'https://my.studiopress.com/images/screenshots/sps-connect.jpg',
				'url'        => 'https://wordpress.org/plugins/genesis-connect-woocommerce/',
				'path'       => 'genesis-connect-woocommerce/genesis-connect-woocommerce.php',
				'slug'       => 'genesis-connect-woocommerce',
			),
			'<span class="screen-reader-text">Genesis</span>Portfolio Pro' => array(
				'svg'        => $genesissvg,
				'screenshot' => 'https://my.studiopress.com/images/screenshots/sps-portfolio.jpg',
				'url'        => 'https://wordpress.org/plugins/genesis-portfolio-pro/',
				'path'       => 'genesis-portfolio-pro/plugin.php',
				'slug'       => 'genesis-portfolio-pro',
			),
			'<span class="screen-reader-text">Genesis</span>Tabs' => array(
				'svg'        => $genesissvg,
				'screenshot' => 'https://my.studiopress.com/images/screenshots/sps-tabs.jpg',
				'url'        => 'https://wordpress.org/plugins/genesis-tabs/',
				'path'       => 'genesis-tabs/plugin.php',
				'slug'       => 'genesis-tabs',
			),
			'<span class="screen-reader-text">Genesis</span>Author Pro' => array(
				'svg'        => $genesissvg,
				'screenshot' => 'https://my.studiopress.com/images/screenshots/sps-author-pro.jpg',
				'url'        => 'https://wordpress.org/plugins/genesis-author-pro/',
				'path'       => 'genesis-author-pro/plugin.php',
				'slug'       => 'genesis-author-pro',
			),
			'Simple URLs' => array(
				'svg'        => '',
				'screenshot' => 'https://my.studiopress.com/images/screenshots/sps-urls.jpg',
				'url'        => 'https://wordpress.org/plugins/simple-urls/',
				'path'       => 'simple-urls/plugin.php',
				'slug'       => 'simple-urls',
			),
			'<span class="screen-reader-text">Genesis</span>Simple Menus' => array(
				'svg'        => $genesissvg,
				'screenshot' => 'https://my.studiopress.com/images/screenshots/sps-menus.jpg',
				'url'        => 'https://wordpress.org/plugins/genesis-simple-menus/',
				'path'       => 'genesis-simple-menus/simple-menu.php',
				'slug'       => 'genesis-simple-menus',
			),
			'<span class="screen-reader-text">Genesis</span>Responsive Slider' => array(
				'svg'        => $genesissvg,
				'screenshot' => 'https://my.studiopress.com/images/screenshots/sps-responsive-slider.jpg',
				'url'        => 'https://wordpress.org/plugins/genesis-responsive-slider/',
				'path'       => 'genesis-responsive-slider/genesis-responsive-slider.php',
				'slug'       => 'genesis-responsive-slider',
			),

		);

		foreach ( $plugins as $plugin => $details ) {

			$installed = file_exists( WP_PLUGIN_DIR . '/' . $details['path'] );
			$active    = is_plugin_active( $details['path'] );

			$install_button = sprintf(
				'<a class="%s" %s>%s</a>',
				'button genesis-install-button',
				'slug="' . esc_attr( $details['slug'] ) . '" . path="' . esc_attr( $details['path'] ) . '"',
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
				<div class="theme-id-container"> <?php
					if ( !empty( $details['svg'] ) ) { ?>
						<h2 class="theme-name genesis-svg"><?php echo $details['svg'] ?><?php echo wp_kses_post( $plugin ); ?></h2>
					<?php } else { ?>
						<h2 class="theme-name"><?php echo $details['svg'] ?><?php echo wp_kses_post( $plugin ); ?></h2>
					<?php } ?>
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
