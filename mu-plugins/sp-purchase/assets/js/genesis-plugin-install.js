jQuery(document).ready(function($) {

	var data = {
		'action': 'genesis_plugin_install',
		'nonce':  genesis_plugin_install.nonce
	};

	$(".genesis-install-button").on( "click", function() {

		data.slug = $(this).attr( 'slug' );
		data.path = $(this).attr( 'path' );

		var button = $(this);
		var button_text = $(this).text();

		// Only run the click if the button is enabled
		if ( $(this).hasClass('disabled') ) {
			return false;
		}

		// Disable the button
		$(this).removeClass('button-primary enabled');
		$(this).addClass('button-secondary installing disabled');
		$(this).text('Installing...');

		//* Main POST
		$.post( ajaxurl, data, function( response ) {

			// If success
			if ( '0' !== response ) {
				$(button).hide();
				$(button).siblings('.sp-activate-button').show();
				return false;
			}

			// Re-enable the button
			$(button).text( button_text );
			$(button).addClass('enabled');
			$(button).removeClass('installing disabled');
			if ( '0' === response ) {
				alert( genesis_plugin_install.notice_failure );
				return false;
			}

			alert( response );
			return false;

		});

		data.slug = null;

	});

});
