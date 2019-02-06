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
		$themes = array(
			'Academy Pro' => array(
				'screenshot' => 'https://my.studiopress.com/images/screenshots/academy-pro.jpg',
				'url'        => 'https://demo.studiopress.com/academy/',
			),
			'Altitude Pro' => array(
				'screenshot' => 'https://my.studiopress.com/images/screenshots/altitude-pro.jpg',
				'url'        => 'https://demo.studiopress.com/altitude/',
			),
			'Atmosphere Pro' => array(
				'screenshot' => 'https://my.studiopress.com/images/screenshots/atmosphere-pro.jpg',
				'url'        => 'https://demo.studiopress.com/atmosphere/',
			),
			'Author Pro' => array(
				'screenshot' => 'https://my.studiopress.com/images/screenshots/author-pro.jpg',
				'url'        => 'https://demo.studiopress.com/author/',
			),
			'Authority Pro' => array(
				'screenshot' => 'https://my.studiopress.com/images/screenshots/authority-pro.jpg',
				'url'        => 'https://demo.studiopress.com/authority/',
			),
			'Brunch Pro' => array(
				'screenshot' => 'https://my.studiopress.com/images/screenshots/brunch-pro.jpg',
				'url'        => 'https://brunchpro.blog/',
			),
			'Daily Dish Pro' => array(
				'screenshot' => 'https://my.studiopress.com/images/screenshots/daily-dish-pro.jpg',
				'url'        => 'https://demo.studiopress.com/daily-dish/',
			),
			'Digital Pro' => array(
				'screenshot' => 'https://my.studiopress.com/images/screenshots/digital-pro.jpg',
				'url'        => 'https://demo.studiopress.com/digital/',
			),
			'Essence Pro' => array(
				'screenshot' => 'https://my.studiopress.com/images/screenshots/essence-pro.jpg',
				'url'        => 'https://demo.studiopress.com/essence/',
			),
			'Executive Pro' => array(
				'screenshot' => 'https://my.studiopress.com/images/screenshots/executive-pro.jpg',
				'url'        => 'https://demo.studiopress.com/executive/',
			),
			'Foodie Pro' => array(
				'screenshot' => 'https://my.studiopress.com/images/screenshots/foodie-pro.jpg',
				'url'        => 'https://foodiepro.com/',
			),
			'Gallery Pro' => array(
				'screenshot' => 'https://my.studiopress.com/images/screenshots/gallery-pro.jpg',
				'url'        => 'https://gallery.designbybloom.co/',
			),
			'Infinity Pro' => array(
				'screenshot' => 'https://my.studiopress.com/images/screenshots/infinity-pro.jpg',
				'url'        => 'https://demo.studiopress.com/infinity/',
			),
			'Lifestyle Pro' => array(
				'screenshot' => 'https://my.studiopress.com/images/screenshots/lifestyle-pro.jpg',
				'url'        => 'https://demo.studiopress.com/lifestyle/',
			),
			'Magazine Pro' => array(
				'screenshot' => 'https://my.studiopress.com/images/screenshots/magazine-pro.jpg',
				'url'        => 'https://demo.studiopress.com/magazine/',
			),
			'Maker Pro' => array(
				'screenshot' => 'https://my.studiopress.com/images/screenshots/maker-pro.jpg',
				'url'        => 'https://maker.designbybloom.co/',
			),
			'Metro Pro' => array(
				'screenshot' => 'https://my.studiopress.com/images/screenshots/metro-pro.jpg',
				'url'        => 'https://demo.studiopress.com/metro/',
			),
			'Monochrome Pro' => array(
				'screenshot' => 'https://my.studiopress.com/images/screenshots/monochrome-pro.jpg',
				'url'        => 'https://demo.studiopress.com/monochrome/',
			),
			'News Pro' => array(
				'screenshot' => 'https://my.studiopress.com/images/screenshots/news-pro.jpg',
				'url'        => 'https://demo.studiopress.com/news/',
			),
			'Outfitter Pro' => array(
				'screenshot' => 'https://my.studiopress.com/images/screenshots/outfitter-pro.jpg',
				'url'        => 'https://demo.studiopress.com/outfitter/',
			),
			'Parallax Pro' => array(
				'screenshot' => 'https://my.studiopress.com/images/screenshots/parallax-pro.jpg',
				'url'        => 'https://demo.studiopress.com/parallax/',
			),
			'Showcase Pro' => array(
				'screenshot' => 'https://my.studiopress.com/images/screenshots/showcase-pro.jpg',
				'url'        => 'http://demo.jtgrauke.com/showcase/',
			),
			'Smart Passive Income Pro' => array(
				'screenshot' => 'https://my.studiopress.com/images/screenshots/smart-passive-income-pro.jpg',
				'url'        => 'https://demo.studiopress.com/smart-passive-income/',
			),
			'Wellness Pro' => array(
				'screenshot' => 'https://my.studiopress.com/images/screenshots/wellness-pro.jpg',
				'url'        => 'https://demo.studiopress.com/wellness/',
			),
			'Workstation Pro' => array(
				'screenshot' => 'https://my.studiopress.com/images/screenshots/workstation-pro.jpg',
				'url'        => 'https://demo.studiopress.com/workstation/',
			),
		);

		foreach ( $themes as $theme => $details ) {

			$installed = file_exists( get_theme_root() . '/' . sanitize_title_with_dashes( $theme ) . '/style.css' );
			$active    = get_option( 'current_theme' ) == $theme;

			$install_button = sprintf(
				'<a class="%s" %s>%s</a>',
				'button sp-install-button',
				'data-slug="' . esc_attr( sanitize_title_with_dashes( $theme ) ) . '"',
				__( 'Install', 'sp-purchase' )
			);

			$activate_button = sprintf(
				'<a href="%s" class="%s" %s>%s</a>',
				esc_url( wp_nonce_url( admin_url( 'themes.php?action=activate&amp;stylesheet='. sanitize_title_with_dashes( $theme ) ), 'switch-theme_' . sanitize_title_with_dashes( $theme ) ) ),
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
					<h2 class="theme-name"><?php echo esc_html( $theme ); ?></h2>
					<div class="theme-actions" style="opacity: 1;">
						<a href="<?php echo esc_url( $details['url'] ); ?>" class="button" target="_blank">Preview</a>
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
