<?php
//* Accessible SVG Icons
$icons = array(
	'home'   => '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="20" height="20" viewBox="0 0 20 20" aria-labelledby="gmm-home-link" role="img"><title id="gmm-home-link">Navigate to the Homepage</title><path d="M16 8.5l1.53 1.53-1.060 1.060-6.47-6.47-6.47 6.47-1.060-1.060 7.53-7.53 4 4v-2h2v4zM10 6.040l6 5.99v5.97h-12v-5.97zM12 17v-5h-4v5h4z"></path></svg>',
	'map'    => '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="20" height="20" viewBox="0 0 20 20" aria-labelledby="gmm-address"><title id="gmm-address">Address</title><path d="M13 13.14l1.17-5.94c0.79-0.43 1.33-1.25 1.33-2.2 0-1.38-1.12-2.5-2.5-2.5s-2.5 1.12-2.5 2.5c0 0.95 0.54 1.77 1.33 2.2zM13 3.5c0.83 0 1.5 0.67 1.5 1.5s-0.67 1.5-1.5 1.5-1.5-0.67-1.5-1.5 0.67-1.5 1.5-1.5zM14.72 8.3l3.28-1.33v9l-4.88 2.030-6.12-2.030-5 2v-9l5-2 4.27 1.41 1.73 7.3z"></path></svg>',
	'phone'  => '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="20" height="20" viewBox="0 0 20 20" aria-labelledby="gmm-phone-link"><title id="gmm-phone-link">Call our telephone number</title><path d="M12.060 6l-0.21-0.2c-0.52-0.54-0.43-0.79 0.080-1.3l2.72-2.75c0.81-0.82 0.96-1.21 1.73-0.48l0.21 0.2zM12.59 6.45l4.4-4.4c0.7 0.94 2.34 3.47 1.53 5.34-0.73 1.67-1.090 1.75-2 3-1.85 2.11-4.18 4.37-6 6.070-1.26 0.91-1.31 1.33-3 2-1.8 0.71-4.4-0.89-5.38-1.56l4.4-4.4 1.18 1.62c0.34 0.46 1.2-0.060 1.8-0.66 1.040-1.050 3.18-3.18 4-4.070 0.59-0.59 1.12-1.45 0.66-1.8zM1.57 16.5l-0.21-0.21c-0.68-0.74-0.29-0.9 0.52-1.7l2.74-2.72c0.51-0.49 0.75-0.6 1.27-0.11l0.2 0.21z"></path></svg>',
	'search' => '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="20" height="20" viewBox="0 0 20 20" aria-labelledby="gmm-search-button"><title id="gmm-search-button">Search this site</title><path d="M12.14 4.18c1.87 1.87 2.11 4.75 0.72 6.89 0.12 0.1 0.22 0.21 0.36 0.31 0.2 0.16 0.47 0.36 0.81 0.59 0.34 0.24 0.56 0.39 0.66 0.47 0.42 0.31 0.73 0.57 0.94 0.78 0.32 0.32 0.6 0.65 0.84 1 0.25 0.35 0.44 0.69 0.59 1.040 0.14 0.35 0.21 0.68 0.18 1-0.020 0.32-0.14 0.59-0.36 0.81s-0.49 0.34-0.81 0.36c-0.31 0.020-0.65-0.040-0.99-0.19-0.35-0.14-0.7-0.34-1.040-0.59-0.35-0.24-0.68-0.52-1-0.84-0.21-0.21-0.47-0.52-0.77-0.93-0.1-0.13-0.25-0.35-0.47-0.66-0.22-0.32-0.4-0.57-0.56-0.78-0.16-0.2-0.29-0.35-0.44-0.5-2.070 1.090-4.69 0.76-6.44-0.98-2.14-2.15-2.14-5.64 0-7.78 2.15-2.15 5.63-2.15 7.78 0zM10.73 10.54c1.36-1.37 1.36-3.58 0-4.95-1.37-1.37-3.59-1.37-4.95 0-1.37 1.37-1.37 3.58 0 4.95 1.36 1.37 3.58 1.37 4.95 0z"></path></svg>',
	'menu'   => '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="20" height="20" viewBox="0 0 20 20" aria-labelledby="gmm-menu-toggle"><title id="gmm-menu-toggle">Toggle the menu items</title><path d="M17 7v-2h-14v2h14zM17 11v-2h-14v2h14zM17 15v-2h-14v2h14z"></path></svg>'
);

genesis_markup( array(
	'html5'   => '<div %s>',
	'xhtml'   => '<div class="gmm-bar">',
	'context' => 'gmm-bar',
) );

genesis_structural_wrap( 'menu-mobile' );
?>
		<div class="gmm-menu-icon gmm-home">
			<a class="menu-home-icon" href="<?php echo site_url(); ?>"><?php echo $icons['home']; ?></a>
		</div>

		<?php if ( genesis_get_option( 'show_address', $this->settings_field ) ) :

			$address = genesis_get_option( 'address', $this->settings_field );

			$address = str_replace( ' ', '+', $address );
			$address = str_replace( "\n", ',', $address );

			$address_url = 'https://www.google.com/maps/dir/' . $address;
			?>
			<div class="gmm-menu-icon gmm-address">
				<a class="menu-address-icon" target="_blank" href="<?php echo esc_url( $address_url ); ?>"><?php echo $icons['map']; ?></a>
			</div>

		<?php endif;

		if ( genesis_get_option( 'show_call', $this->settings_field ) ) :
			$number = genesis_get_option( 'phone_number', $this->settings_field );
			$number = str_replace( '-', '', $number );
			?>
			<div class="gmm-menu-icon gmm-call">
				<a class="menu-call-icon" href="tel:<?php echo $number; ?>"><?php echo $icons['phone']; ?></a>
			</div>
		<?php endif;

		if ( genesis_helper_get_option( 'show_search', $this->settings_field, '1' ) ) : ?>
			<div class="gmm-menu-icon gmm-search">
				<button class="menu-search-icon" aria-expanded="false" aria-pressed="false" role="button"><?php echo $icons['search']; ?></button>
			</div>
		<?php endif;

		if ( has_nav_menu( 'mobile' ) ) : ?>
			<div class="gmm-menu-icon gmm-toggle">
				<button class="menu-toggle-icon" aria-expanded="false" aria-pressed="false" role="button"><?php echo $icons['menu']; ?></button>
			</div>
		<?php endif;

		if ( genesis_helper_get_option( 'show_search', $this->settings_field, '1' ) ) : ?>
		<div class="search" id="gmm-search-bar">
			<form method="get" class="search-form" action="<?php echo home_url(); ?>" role="search">
				<input type="search" id="gmm-search-input" name="s" placeholder="<?php _e( 'Enter keywords&hellip;', 'genesis-mobile-menu-bar' ); ?>" />
				<input type="submit" value="<?php _e( 'Search', 'genesis-mobile-menu-bar' ); ?> &raquo;">
			</form>
		</div>
		<?php endif;

		if ( has_nav_menu( 'mobile' ) ) {

			wp_nav_menu( array(
				'theme_location'  => 'mobile',
				'container'       => genesis_html5() ? 'nav' : 'div',
				'container_class' => 'gmm-links',
				'menu_class'      => 'gmm-nav-menu',
				'fallback_cb'     => false,
				'walker'          => new GMM_Walker_Nav()
			));

		} ?>

	<?php
	genesis_structural_wrap( 'mobile-menu', 'close' );
	echo '</div>';

//* Helper function to set the default value if the option hasn't been saved in the database.
function genesis_helper_get_option( $option, $setting, $default ) {

	$value = '';

	if ( is_customize_preview() ) {
		$value = genesis_get_option( $option, $setting, false ) ? genesis_get_option( $option, $setting, false ) : $default;
	} else {
		$value = genesis_get_option( $option, $setting, true ) ? genesis_get_option( $option, $setting, true ) : $default;
	}

	return $value;

}

//* Walker class to rewrite class names
class GMM_Walker_Nav extends Walker_Nav_Menu {
	function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat( "\t", $depth );
		$output .= "\n$indent<ul class=\"gmm-sub-menu\">\n";
	}
}
