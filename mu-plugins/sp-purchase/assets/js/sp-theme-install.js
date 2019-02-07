jQuery(document).ready(function($) {

	var data = {
		'action': 'sp_theme_install',
		'nonce':  sp_theme_install.nonce
	};

	$(".sp-install-button").on( "click", function() {

		data.slug = $(this).data( 'slug' );

		var button = $(this);
		var button_text = $(this).text();

		// Only run the click if the button is enabled
		if ( $(this).hasClass('disabled') ) {
			return false;
		}

		// Disable the button
		$(this).removeClass('enabled');
		$(this).addClass('installing disabled');
		$(this).text('Installing...');

		//* Main POST
		$.post( ajaxurl, data, function( response ) {

			// If success
			if ( '1' === response ) {
				$(button).hide();
				$(button).siblings('.sp-activate-button').show();
				return false;
			}

			// Re-enable the button
			$(button).text( button_text );
			$(button).addClass('enabled');
			$(button).removeClass('installing disabled');
			if ( '0' === response ) {
				alert( sp_theme_install.notice_failure );
				return false;
			}

			alert( response );
			return false;

		});

		data.slug = null;

	});

});
