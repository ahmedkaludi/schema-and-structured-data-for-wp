/**
 * SASWP AI Integration – Settings Page JS
 *
 * @package schema-and-structured-data-for-wp
 * @path    modules/ai-integration/js/saswp-ai-settings.js
 * @version 1.0
 *
 * Handles all interactivity on the AI Integration settings tab:
 *  - Provider row switching (Gemini / OpenAI)
 *  - Sub-tab navigation
 *  - Checkbox <-> hidden-input sync
 *  - "Fetch Models" AJAX button
 */
jQuery( document ).ready( function ( $ ) {

    /* ------------------------------------------------------------------
     * 1. Provider switching - show/hide Gemini or OpenAI rows
     * ------------------------------------------------------------------ */
    function saswpAiSwitchProvider() {
        var selected = $( '#saswp_ai_provider' ).val();
        $( 'li:has(.saswp-ai-row.gemini)' ).toggle( selected === 'gemini' );
        $( 'li:has(.saswp-ai-row.openai)' ).toggle( selected === 'openai' );
    }
    $( '#saswp_ai_provider' ).on( 'change', saswpAiSwitchProvider );
    saswpAiSwitchProvider();

    /* ------------------------------------------------------------------
     * 2. Sub-tab navigation (matches tools-tab behaviour)
     * ------------------------------------------------------------------ */
    $( document ).on( 'click', '.saswp-ai-tab-nav[data-div-id]', function ( e ) {
        e.preventDefault();
        var divId = $( this ).attr( 'data-div-id' );
        $( '.saswp-ai-tab-nav' ).removeClass( 'saswp-global-selected' );
        $( this ).addClass( 'saswp-global-selected' );
        $( '.saswp-ai-tab-content' ).addClass( 'saswp_hide' );
        $( '#' + divId ).removeClass( 'saswp_hide' );
    } );

    /* ------------------------------------------------------------------
     * 3. Checkbox <-> hidden-input sync
     *    Checkboxes have a companion hidden <input> that carries the real
     *    submitted value (1 or 0) because unchecked checkboxes are omitted
     *    from POST data by browsers.
     * ------------------------------------------------------------------ */
    var saswpAiCheckboxMap = {
        'saswp-ai-enable-checkbox'   : 'saswp_ai_enable',
        'saswp-ai-auto-gen-checkbox' : 'saswp_ai_auto_gen',
        'saswp-ai-overwrite-checkbox': 'saswp_ai_overwrite'
    };

    function saswpAiSyncCheckbox( checkbox ) {
        var id       = $( checkbox ).attr( 'id' );
        var hiddenId = saswpAiCheckboxMap[ id ];
        if ( hiddenId ) {
            $( '#' + hiddenId ).val( $( checkbox ).is( ':checked' ) ? 1 : 0 );
        }
    }

    // Initialise hidden inputs from checkbox state on page load
    $.each( saswpAiCheckboxMap, function ( checkId ) {
        saswpAiSyncCheckbox( $( '#' + checkId ) );
    } );

    // Keep syncing on every change
    $( document ).on(
        'change',
        '#saswp-ai-enable-checkbox, #saswp-ai-auto-gen-checkbox, #saswp-ai-overwrite-checkbox',
        function () {
            saswpAiSyncCheckbox( this );
        }
    );

    /* ------------------------------------------------------------------
     * 4. "Fetch Models" buttons - append then handle clicks
     * ------------------------------------------------------------------ */
    if ( $( '#saswp_ai_gemini_model' ).length && ! $( '#saswp-fetch-gemini-models' ).length ) {
        $( '#saswp_ai_gemini_model' ).after(
            ' <button type="button" id="saswp-fetch-gemini-models"' +
            ' class="button button-secondary saswp-fetch-models-btn"' +
            ' data-provider="gemini"' +
            ' data-key-id="saswp_ai_gemini_key"' +
            ' data-model-id="saswp_ai_gemini_model">' +
            '<span class="dashicons dashicons-update"></span> Fetch Models</button>' +
            '<span class="saswp-fetch-status" id="saswp-fetch-status-gemini"></span>'
        );
    }

    if ( $( '#saswp_ai_openai_model' ).length && ! $( '#saswp-fetch-openai-models' ).length ) {
        $( '#saswp_ai_openai_model' ).after(
            ' <button type="button" id="saswp-fetch-openai-models"' +
            ' class="button button-secondary saswp-fetch-models-btn"' +
            ' data-provider="openai"' +
            ' data-key-id="saswp_ai_openai_key"' +
            ' data-model-id="saswp_ai_openai_model">' +
            '<span class="dashicons dashicons-update"></span> Fetch Models</button>' +
            '<span class="saswp-fetch-status" id="saswp-fetch-status-openai"></span>'
        );
    }

    $( document ).on( 'click', '.saswp-fetch-models-btn', function ( e ) {
        e.preventDefault();

        var btn      = $( this );
        var provider = btn.attr( 'data-provider' );
        var keyId    = btn.attr( 'data-key-id' );
        var modelId  = btn.attr( 'data-model-id' );
        var apiKey   = $( '#' + keyId ).val();
        var statusEl = $( '#saswp-fetch-status-' + provider );

        if ( ! apiKey ) {
            statusEl.css( 'color', '#d63638' ).text( 'Please enter an API Key first.' );
            return;
        }

        btn.prop( 'disabled', true );
        statusEl.css( 'color', '#666' ).text( 'Fetching models from API...' );

        $.ajax( {
            url  : saswp_ai_settings_params.ajax_url,
            type : 'POST',
            data : {
                action               : 'saswp_fetch_ai_models',
                saswp_security_nonce : saswp_ai_settings_params.nonce,
                provider             : provider,
                api_key              : apiKey
            },
            success: function ( response ) {
                btn.prop( 'disabled', false );
                if ( response.success && response.data.models ) {
                    var modelSelect = $( '#' + modelId );
                    var currentVal  = modelSelect.val();
                    modelSelect.empty();

                    $.each( response.data.models, function ( i, m ) {
                        modelSelect.append( $( '<option>', { value: m.id, text: m.name } ) );
                    } );

                    if ( currentVal && modelSelect.find( 'option[value="' + currentVal + '"]' ).length ) {
                        modelSelect.val( currentVal );
                    }
                    statusEl.css( 'color', '#00a32a' )
                            .text( 'Successfully loaded ' + response.data.models.length + ' models!' );
                } else {
                    var err = ( response.data && response.data.error )
                        ? response.data.error
                        : 'Failed to fetch models.';
                    statusEl.css( 'color', '#d63638' ).text( err );
                }
            },
            error: function () {
                btn.prop( 'disabled', false );
                statusEl.css( 'color', '#d63638' ).text( 'Network error while fetching models.' );
            }
        } );
    } );

} );
