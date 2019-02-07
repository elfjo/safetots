(function( $ ){

	'use strict';

	//* Variables
	var gmm      = {},
		html     = $( "html" ),
		body     = $( "body" ),
		menu     = $( ".gmm-links" ),
		mainOpen = false,
		search   = $( "#gmm-search-bar" ),
		buttons  = {
			map:    $( ".gmm-bar .menu-address-icon" ),
			phone:  $( ".gmm-bar .menu-call-icon" ),
			search: $( ".gmm-bar .menu-search-icon" ),
			menu:   $( ".gmm-bar .menu-toggle-icon" )
		},
		svgs     = {
			mainMenu: {
				open: buttons.menu.html(),
				close: '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="20" height="20" viewBox="0 0 20 20" aria-labelledby="gmm-menu-close"><title id="gmm-menu-close">Close the menu items</title><path d="M14.95 6.46l-3.54 3.54 3.54 3.54-1.41 1.41-3.54-3.53-3.53 3.53-1.42-1.42 3.53-3.53-3.53-3.53 1.42-1.42 3.53 3.53 3.54-3.53z"></path></svg>'
			},
			subMenu:  {
				open: '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="20" height="20" viewBox="0 0 20 20" aria-labelledby="gmm-sub-menu-open"><title id="gmm-sub-menu-open">Open this sub menu</title><path d="M17 9v2h-6v6h-2v-6h-6v-2h6v-6h2v6h6z"></path></svg>',
				close: '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="20" height="20" viewBox="0 0 20 20" aria-labelledby="gmm-sub-menu-close"><title id="gmm-sub-menu-close">Close this sub menu</title><path d="M4 9h12v2h-12v-2z"></path></svg>'
			}
		};


	//* Set base JS class
	body.addClass('mobile-menu-js');

	//* Toggle the search bar
	buttons.search.click( function(e) {

		var $this = $(this);

		e.preventDefault();
		search.slideToggle( 250, "swing", function() {
			$('#gmm-search-input').focus();
		});
		_toggleAria( $this, 'aria-pressed' );
		_toggleAria( $this, 'aria-expanded' );

	});

	gmm.init = function() {

		var toggleButtons = {
			menu : buttons.menu,
			submenu : $( '<button />', {
				'class' : 'gmm-sub-menu-toggle',
				'aria-expanded' : false,
				'aria-pressed' : false,
				'role' : 'button'
				})
				.append( svgs.subMenu.open )
		}
		$( '.gmm-links .gmm-sub-menu' ).before( toggleButtons.submenu );
		buttons.menu.each( _addClassID );
		$( window ).on( 'resize.gmm', _doResize ).triggerHandler( 'resize.gmm' );
		buttons.menu.on( 'click.gmm-mainbutton', _mainmenuToggle );
		$( '.gmm-sub-menu-toggle' ).on( 'click.gmm-subbutton', _submenuToggle );
	}

	/**
	 *
	 * Function to add class/ID
	 *
	 */
	function _addClassID() {

	 	var $this = $( this ),
			nav   = $( '.gmm-links' ),
			id    = 'class';
		if ( $( nav ).attr( 'id' ) ) {
			id = 'id';
		}
		$this.attr( 'id', 'gmm-mobile-' + $( nav ).attr( id ) );
	}

	/**
	 *
	 * Function to handle resizing
	 *
	 */
	function _doResize() {
		var buttons = $( 'button[id^="gmm-mobile-"]' ).attr( 'id' );
		if ( typeof buttons === 'undefined' ) {
			return;
		}
		_superfishToggle( buttons );
		_maybeClose( buttons );
	}

	/**
	 *
	 * Action to happen when the main menu button is clicked
	 *
	 */
	function _mainmenuToggle() {
		var $this = $( this );
		_toggleAria( $this, 'aria-pressed' );
		_toggleAria( $this, 'aria-expanded' );
		html.toggleClass( 'gmm-links-visible' );
		menu.slideToggle();

		if ( mainOpen ) {
			$(this).html( svgs.mainMenu.open );
			mainOpen = false;
		} else {
			$(this).html( svgs.mainMenu.close );
			mainOpen = true;
		}
	}

	/**
	 *
	 * Action for submenu toggles
	 *
	 */
	function _submenuToggle() {

		var $this   = $( this ),
			subOpen = false,
			others  = $this.closest( '.menu-item' ).siblings();
		_toggleAria( $this, 'aria-pressed' );
		_toggleAria( $this, 'aria-expanded' );

		if ( $this.next( '.gmm-sub-menu').is( ":visible" ) ) {
			subOpen = true;
		}

		$this.parent().toggleClass('menu-open');
		$this.next(".gmm-sub-menu").slideToggle();

		if ( subOpen ) {
			$this.html( svgs.subMenu.open );
			subOpen = false;
		} else {
			$this.html( svgs.subMenu.close );
			subOpen = true;
		}

		others.parents( 'menu-item' ).removeClass( 'menu-open' );
		others.find( '.gmm-sub-menu-toggle' ).html( svgs.subMenu.open ).attr( 'aria-pressed', 'false' );
		others.find( '.gmm-sub-menu' ).slideUp( 'fast' );

	}

	/**
	 *
	 * Activate/deactivate superfish
	 *
	 */
	function _superfishToggle() {
		if ( typeof $( '.gmm-nav-menu.js-superfish' ).superfish !== 'function' ) {
			return;
		}
		$( '.gmm-nav-menu.js-superfish' ).superfish( 'destroy' );
	}

	function _maybeClose( buttons ) {
		if ( 'none' !== _getDisplayValue( buttons ) ) {
			return;
		}
		$( '.gmm-bar .menu-toggle-icon, .gmm-sub-menu-toggle' )
			.removeClass( 'menu-open' )
			.attr( 'aria-expanded', false )
			.attr( 'aria-pressed', false );
		$( '.gmm-links nav, .gmm-links .sub-menu' )
			.attr( 'style', '' );
	}

	/**
	 * generic function to get the display value of an element
	 * @param  {id} $id ID to check
	 * @return {string}     CSS value of display property
	 */
	function _getDisplayValue( $id ) {
		var element = document.getElementById( $id ),
			style   = window.getComputedStyle( element );
		return style.getPropertyValue( 'display' );
	}

	/**
	 * Toggle aria attributes
	 * @param  {button} $this     passed through
	 * @param  {aria-xx} attribute aria attribute to toggle
	 * @return {bool}           from _ariaReturn
	 */
	function _toggleAria( $this, attribute ) {
		$this.attr( attribute, function( index, value ) {
			return 'false' === value;
		});
	}

	$(document).ready(function () {

		gmm.init();

	});

})(jQuery);
