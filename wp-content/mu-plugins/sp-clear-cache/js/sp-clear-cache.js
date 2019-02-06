jQuery(document).ready(function($) {
    var button = $('#wp-admin-bar-sp-clear-cache-button.sp-clear-cache-button');

    button.on( 'click', function() {
        var wpbody = $('#wpbody-content div.wrap h1').first();
        $('<div class="notice notice-success is-dismissible"><p>Success! Job Submitted.<br/>In some cases, clearing the cache can take up to 15 seconds, but it is usually much quicker!</p></div>').insertAfter(wpbody);

        var data = {
            'action':  'sp_clear_cache',
        };

        $.post( ajaxurl, data, function(response) {
            var wpbody = $('#wpbody-content div.wrap h1').first();
            $(response).insertAfter(wpbody);
        });

        return true;
    });
});
