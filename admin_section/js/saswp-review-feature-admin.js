jQuery(document).ready(function($){
        
        if ( $('#saswp-rf-page-criteria-single').is(':checked') ) {
                $('#saswp-rf-page-multi-criteria-wrapper').hide();
        }
        if ( ! $('#saswp-rf-page-settings-pros-cons').is(':checked') ) {
                $('#saswp-rf-page-settings-pros-cons-limit-wrapper').hide();
        }
        if ( ! $('#saswp-rf-page-settings-filter').is(':checked') ) {
                $('#saswp-rf-page-settings-filters-opt').hide();
        }
        if ( ! $('#saswp-rf-page-settings-anonymus-review').is(':checked') ) {
                $('#saswp-rf-page-settings-author-wrapper').hide();
                $('#saswp-rf-page-settings-email-wrapper').hide();
        }

        $('.saswp-rf-page-select2').select2();

        $(document).on('click', '.saswp-rf-tab-nav a', function(e){
                e.preventDefault();

                let href = $(this).attr('href');                
                
                $('.saswp-rf-page-tab-content-wrapper').addClass('saswp_hide');
                $('.saswp-rf-tab-nav .nav-tab').removeClass('nav-tab-active');
                $(this).addClass('nav-tab-active');
                $(href).removeClass('saswp_hide');
        });

        $(document).on('click', '#saswp-rf-page-field-criteria label', function(e){
                $('#saswp-rf-page-field-criteria label input').removeAttr('checked');

                let findRadio   =       $(this).find('.saswp-rf-page-criteria-radio');
                findRadio.prop('checked', true);
                let radioVal    =       findRadio.val();
                if ( radioVal == 'multi' ) {
                        $('#saswp-rf-page-multi-criteria-wrapper').show();
                }else{
                        $('#saswp-rf-page-multi-criteria-wrapper').hide();
                }
        });

        $(document).on('click', '#saswp-rf-page-field-summary-layout label', function(e){
                $('#saswp-rf-page-field-summary-layout label input').removeAttr('checked');
                $(this).find('.saswp-rf-page-summary-layout-radio').prop('checked', true);
        });

        $(document).on('click', '#saswp-rf-page-field-review-layout label', function(e){
                $('#saswp-rf-page-field-review-layout label input').removeAttr('checked');
                $(this).find('.saswp-rf-page-review-layout-radio').prop('checked', true);
        });

        $(document).on('click', '.saswp-rf-page-remove-multiple-criteria', function(e){
                e.preventDefault();
                $(this).parent().remove();
        });

        $(document).on('click', '#saswp-rf-page-add-multi-criteria', function(e) {
                e.preventDefault();
                let criteriaLabels =    $('#saswp-rf-page-multi-criteria').find('label');
                let labelCount     =    criteriaLabels.length;
                let fieldId        =    'saswp-rf-multi-criteria-' + labelCount;   

                let labelField     =    '';
                labelField         +=    `<label for="${fieldId}" class="ui-sortable-handle">`;
                labelField         +=    `<input type="text" id="${fieldId}" name="sd_data[saswp-rf-page-criteria-multiple][]"><i class="saswp-rf-page-remove-multiple-criteria dashicons dashicons-dismiss"></i>`;
                labelField         +=    `</label>`;

                $('#saswp-rf-page-multi-criteria').append(labelField);

        });

        $(document).on('change', '#saswp-rf-page-settings-pros-cons', function(e){
            if ( $(this).is(':checked') ) {
                $('#saswp-rf-page-settings-pros-cons-limit-wrapper').show();
            }else{
                $('#saswp-rf-page-settings-pros-cons-limit-wrapper').hide();
            }    
        });

        $(document).on('change', '#saswp-rf-page-settings-filter', function(e){
            if ( $(this).is(':checked') ) {
                $('#saswp-rf-page-settings-filters-opt').show();
            }else{
                $('#saswp-rf-page-settings-filters-opt').hide();
            }    
        }); 

        $(document).on('change', '#saswp-rf-page-settings-anonymus-review', function(e){
            if ( $(this).is(':checked') ) {
                $('#saswp-rf-page-settings-author-wrapper').show();
                $('#saswp-rf-page-settings-email-wrapper').show();
            }else{
                $('#saswp-rf-page-settings-author-wrapper').hide();
                $('#saswp-rf-page-settings-email-wrapper').hide();
            }    
        }); 
});