var strict;

jQuery(document).ready(function ($) {
    /**
     * DEACTIVATION FEEDBACK FORM
     */
    // show overlay when clicked on "deactivate"
    saswp_deactivate_link = $('.wp-admin.plugins-php tr[data-slug="schema-and-structured-data-for-wp"] .row-actions .deactivate a');
    saswp_deactivate_link_url = saswp_deactivate_link.attr('href');

    saswp_deactivate_link.click(function (e) {
        e.preventDefault();
        
        // only show feedback form once per 30 days
        var c_value = saswp_admin_get_cookie("saswp_hide_deactivate_feedback");

        if (c_value === undefined) {
            $('#saswp-reloaded-feedback-overlay').show();
        } else {
            // click on the link
            window.location.href = saswp_deactivate_link_url;
        }
    });
    // show text fields
    $('#saswp-reloaded-feedback-content input[type="radio"]').click(function () {
        // show text field if there is one
        var inputValue = $(this).attr("value");
        var targetBox = $("." + inputValue);
        $(".mb-box").not(targetBox).hide();
        $(targetBox).show();
    });
    // send form or close it
    $('#saswp-reloaded-feedback-content .button').click(function (e) {
        e.preventDefault();
        // set cookie for 30 days
        var exdate = new Date();
        exdate.setSeconds(exdate.getSeconds() + 2592000);
        document.cookie = "saswp_hide_deactivate_feedback=1; expires=" + exdate.toUTCString() + "; path=/";

        $('#saswp-reloaded-feedback-overlay').hide();
        if ('saswp-reloaded-feedback-submit' === this.id) {
            // Send form data
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                dataType: 'json',
                data: {
                    action: 'saswp_send_feedback',
                    data: $('#saswp-reloaded-feedback-content form').serialize()
                },
                complete: function (MLHttpRequest, textStatus, errorThrown) {
                    // deactivate the plugin and close the popup
                    $('#saswp-reloaded-feedback-overlay').remove();
                    window.location.href = saswp_deactivate_link_url;

                }
            });
        } else {
            $('#saswp-reloaded-feedback-overlay').remove();
            window.location.href = saswp_deactivate_link_url;
        }
    });
    // close form without doing anything
    $('.saswp-for-wp-feedback-not-deactivate').click(function (e) {
        $('#saswp-reloaded-feedback-overlay').hide();
    });
    
    function saswp_admin_get_cookie (name) {
    var i, x, y, saswp_cookies = document.cookie.split( ";" );
    for (i = 0; i < saswp_cookies.length; i++)
    {
        x = saswp_cookies[i].substr( 0, saswp_cookies[i].indexOf( "=" ) );
        y = saswp_cookies[i].substr( saswp_cookies[i].indexOf( "=" ) + 1 );
        x = x.replace( /^\s+|\s+$/g, "" );
        if (x === name)
        {
            return unescape( y );
        }
    }
}

}); // document ready