jQuery(function( $ ){

	// Function to automatically set the dimensions of .video to viewport width and height.
	function video_dimensions() {
		// WordPress admin bar height
		var barHeight = $('#wpadminbar').outerHeight();

		// Viewport height
		var windowHeight = window.innerHeight;

		// Viewport height minus WordPress admin bar height
		var newHeight = windowHeight - barHeight;

		$( '.video' ).css({'height': newHeight + 'px'});
		$( '.video' ).css({'width': $(window).width()});
	}

	// http://stackoverflow.com/a/1974797/778809
	// Bind to the resize event of the window object
	$(window).on("resize", function () {
		video_dimensions();
		// Invoke the resize event immediately
	}).resize();

});
