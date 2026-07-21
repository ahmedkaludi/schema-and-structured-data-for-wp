/**
 * JS handler for AI Schema Generation in Post Editor
 */
jQuery(document).ready(function($) {
    // Click handler for AI Generation
    $(document).on('click', '#saswp-ai-generate-btn', function(e) {
        e.preventDefault();

        var btn = $(this);
        var feedback = $('#saswp-ai-feedback');
        var textarea = $('#saswp_custom_schema_field');
        var targetType = $('#saswp-ai-type-select').val();
        var postId = $('#post_ID').val();

        if (!postId) {
            feedback.css('color', 'red').text('Error: Could not retrieve Post ID.');
            return;
        }

        // Disable button & show spinner
        btn.prop('disabled', true).text('Generating...');
        feedback.css('color', '#666').text(saswp_ai_params.loading);

        $.ajax({
            url: saswp_ai_params.ajax_url,
            type: 'POST',
            data: {
                action: 'saswp_generate_ai_schema',
                post_id: postId,
                target_type: targetType,
                saswp_security_nonce: saswp_ai_params.nonce
            },
            success: function(response) {
                btn.prop('disabled', false).html('✨ Generate Schema with AI');
                
                if (response.success && response.data.schema) {
                    textarea.val(response.data.schema);
                    feedback.css('color', 'green').text(saswp_ai_params.success);
                    
                    // Ensure Custom Schema is enabled and container is visible in plugin's logic
                    var toggle = $('.saswp-schema-type-toggle[data-schema-id="custom"]');
                    if (toggle.length && toggle.is(':checked')) {
                        toggle.prop('checked', false).trigger('change');
                    } else {
                        textarea.parent().removeClass('saswp_hide');
                    }
                } else {
                    var errMsg = response.data && response.data.error ? response.data.error : 'Unknown error occurred.';
                    feedback.css('color', 'red').text('Error: ' + errMsg);
                }
            },
            error: function() {
                btn.prop('disabled', false).html('✨ Generate Schema with AI');
                feedback.css('color', 'red').text('Error connecting to backend server.');
            }
        });
    });
});
