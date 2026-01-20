jQuery(document).ready(function($) {
    $('.saswp-or-dismiss').on('click', function(e) {
        e.preventDefault();

        $(this).closest('.saswp-omnireview-banner').slideUp();

    });
});