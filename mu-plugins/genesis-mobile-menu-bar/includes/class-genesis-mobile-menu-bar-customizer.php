<?php

class Genesis_Mobile_Menu_Bar_Customizer extends Genesis_Customizer_Base {

	/**
	 * Settings field.
	 *
	 * @since 0.9.0
	 *
	 * @var string Settings field.
	 */
	public $settings_field;

	/**
	 * Constructor Method.
	 *
	 * @since 0.9.0
	 */
	public function __construct() {

		$this->settings_field = Genesis_Mobile_Menu_Bar()->settings_field;

		parent::__construct();

	}

	/**
	 * Register new Customizer elements.
	 *
	 * Actual registration of settings and controls are handled in private methods.
	 *
	 * @since 0.9.0
	 *
	 * @param WP_Customize_Manager $wp_customize WP_Customize_Manager instance.
	 */
	public function register( $wp_customize ) {

		$this->sections( $wp_customize );
		$this->settings( $wp_customize );
		$this->controls( $wp_customize );

		#wp_die( current_filter() );

	}

	/**
	 * Register the customizer sections
	 *
	 * @since 0.9.0
	 *
	 * @param WP_Customize_Manager $wp_customize WP_Customize_Manager instance.
	 */
	private function sections( $wp_customize ) {

		$wp_customize->add_section(
			'genesis_mobile_menu_bar',
			apply_filters( 'genesis_mobile_menu_bar_section_args', array(
				'title'    => __( 'Mobile Menu Bar', 'genesis-mobile-menu-bar' ),
				'priority' => '101',
			) )
		);

	}

	/**
	 * Register the settings.
	 *
	 * @since 0.9.0
	 *
	 * @param WP_Customize_Manager $wp_customize WP_Customize_Manager instance.
	 */
	private function settings( $wp_customize ) {

		$settings = array(
			'enable_mobile_menu' => 1,
			'home_logo'          => '',
			'menu_color'         => '#111111',
			'show_search'        => 1,
			'show_call'          => 1,
			'show_address'       => 1,
			'phone_number'       => '',
			'address'            => '',
			'assigned_mobile_menu' => '',
		);

		// Push defaults to database
		add_option( $this->settings_field, $settings );

		foreach ( $settings as $setting => $default ) {
			$wp_customize->add_setting(
				$this->get_field_name( $setting ),
				array(
					'default' => $default,
					'type'    => 'option',
				)
			);
		}

	}

	/**
	 * Register the controls.
	 *
	 * @since 0.9.0
	 *
	 * @param WP_Customize_Manager $wp_customize WP_Customize_Manager instance.
	 */
	private function controls( $wp_customize ) {

		//* Toggle the menu on/off.
		$wp_customize->add_control(
			'enable_mobile_menu_bar',
			array(
				'label'    => __( 'Enable Mobile Menu Bar?', 'genesis-mobile-menu-bar' ),
				'section'  => 'genesis_mobile_menu_bar',
				'settings' => $this->get_field_name( 'enable_mobile_menu' ),
				'type'     => 'checkbox',
			)
		);

		//* Choose menu color.
		$wp_customize->add_control( new WP_Customize_Color_Control(
			$wp_customize,
			'menu_color',
			array(
				'label'   => __( 'Menu Bar Background Color', 'genesis-mobile-menu-bar' ),
				'section' => 'genesis_mobile_menu_bar',
				'settings'   => $this->get_field_name( 'menu_color' ),
			)
		) );

		//* Show search button?
		$wp_customize->add_control(
			'show_search',
			array(
				'label'    => __( 'Enable Search Button?', 'genesis-mobile-menu-bar' ),
				'section'  => 'genesis_mobile_menu_bar',
				'settings' => $this->get_field_name( 'show_search' ),
				'type'     => 'checkbox',
			)
		);

		//* Show search button?
		$wp_customize->add_control(
			'show_call',
			array(
				'label'    => __( 'Enable Call Button?', 'genesis-mobile-menu-bar' ),
				'section'  => 'genesis_mobile_menu_bar',
				'settings' => $this->get_field_name( 'show_call' ),
				'type'     => 'checkbox',
			)
		);

		//* Telephone Number
		$wp_customize->add_control(
			'phone_number',
			array(
				'label'    => __( 'Telephone Number:', 'genesis-mobile-menu-bar' ),
				'section'  => 'genesis_mobile_menu_bar',
				'settings' => $this->get_field_name( 'phone_number' ),
				'type'     => 'tel',
			)
		);
		/**/

		//* Show search button?
		$wp_customize->add_control(
			'show_address',
			array(
				'label'    => __( 'Enable Address Button?', 'genesis-mobile-menu-bar' ),
				'section'  => 'genesis_mobile_menu_bar',
				'settings' => $this->get_field_name( 'show_address' ),
				'type'     => 'checkbox',
			)
		);

		//* Address
		$wp_customize->add_control(
			'address',
			array(
				'label'    => __( 'Address:', 'genesis-mobile-menu-bar' ),
				'section'  => 'genesis_mobile_menu_bar',
				'settings' => $this->get_field_name( 'address' ),
				'type'     => 'textarea',
			)
		);

		// Send to the Menu Panel
		$wp_customize->add_control( new WP_Customize_Button(
			$wp_customize,
			'assign-mobile-menu',
			array(
				'label'       => __( 'Manage Menu Links', 'genesis-mobile-menu-bar' ),
				'description' => __( 'Select a custom menu, or create a new menu, to display in the Mobile Menu Bar.', 'genesis-mobile-menu-bar' ),
				'section'     => 'genesis_mobile_menu_bar',
				'settings'    => $this->get_field_name( 'assigned_mobile_menu' ),
				'type'        => 'button',
			)
		));

	}

}

// Create custom button to use as link.
if( class_exists( 'WP_Customize_Control' ) ):
	class WP_Customize_Button extends WP_Customize_Control {
		public $type = 'button';

		public function render_content() {
		?>
			<label>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
				<a class="button button-primary" href="javascript:wp.customize.control( 'nav_menu_locations[mobile]' ).focus()"><?php echo __( 'Select a Menu', 'genesis-mobile-menu-bar' ); ?></a>
				<a class="button" href="javascript:wp.customize.panel( 'nav_menus' ).focus()"><?php echo __( 'Create a New Menu', 'genesis-mobile-menu-bar' ); ?></a>
			</label>
		<?php
		}
	}
endif;
