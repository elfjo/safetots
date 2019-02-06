jQuery(function( $ ){
	
	var elOverlay = '<div id="sp-user-onboarding-overlay"></div>';
		
	var elMain = '<div id="sp-user-onboarding-popup"><div class="wrap">' +
	'<button class="sp-user-onboarding-popup-btn-close dashicons dashicons-no-alt">Close</button>' +
	'<div class="sp-user-onboarding-step-1">' +
	'<h1>Welcome to your new StudioPress Site!</h1>' +
	'<p>As promised, we’ve set up the basics (and then some!):</p>' +
	'<ul>' +
	'<li>WordPress ' + spOnboradingWPVersion + ' installed</li>' +
	'<li>Daily backups activated</li>' +
	'<li>Genesis Framework installed</li>' +
	'<li>Sucuri monitoring system activated</li>' +
	'<li>Automatic updates activated</li>' +
	'<li>StudioPress Site Tools installed</li>' +
	'</ul>' +
	'<p>... and <strong>over 20 StudioPress Themes</strong> and <strong>over 10 partner plugins</strong> are ready for you to use.</p>' +
	'<button class="sp-user-onboarding-popup-btn-next">Next &rarr;</button>' +
	'</div>' +
	'<div class="sp-user-onboarding-step-2">' +
	'<h1>What’s next for your Site?</h1>' +
	'<p>Now that the backbone of your WordPress site is ready, it’s your turn to customize it and add content. Here are some steps you can take to get started:</p>' +
	'<ul>' +
	'<li><a href="/wp-admin/admin.php?page=sp-themes" target="_blank"><strong>Choose one of the included themes</strong></a></li>' +
	'<li><a href="/wp-admin/theme-install.php?browse=featured" target="_blank">Upload your own theme</a></li>' +
	'<li><a href="/wp-admin/admin.php?page=sp-plugins" target="_blank">Install and activate partner plugins</a></li>' +
	'<li><a href="https://my.studiopress.com/general-help/video-tutorials/" target="_blank">Learn about WordPress with our video tutorials</a></li>' +
	'<li><a href="https://my.studiopress.com/studiopress-site/" target="_blank">Visit our Sites knowledge base</a></li>' +
	'</ul>' +
	'<p>Remember, if you need any help, <a href="https://my.studiopress.com/support/" target="_blank">we’re just a click away</a>!</p>' +
	'<button class="sp-user-onboarding-popup-btn-dashboard">Take me to the dashboard &rarr;</button>' +
	'</div>' +
	'</div></div>';
	
	$('body').prepend(elOverlay);
	$('body').prepend(elMain);
	
	$('#sp-user-onboarding-overlay, .sp-user-onboarding-popup-btn-close, .sp-user-onboarding-popup-btn-dashboard').click(function(){
		
		$('#sp-user-onboarding-overlay').fadeOut();
		$('#sp-user-onboarding-popup').fadeOut();
		
	});
	
	$('.sp-user-onboarding-popup-btn-next').click(function(){
		
		$('.sp-user-onboarding-step-1').hide();
		$('.sp-user-onboarding-step-2').fadeIn();
		
	});
	
});