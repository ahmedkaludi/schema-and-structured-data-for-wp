function getParameterByName(name, url) {
    if (!url){
    url = window.location.href;    
    } 
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return "";
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}
jQuery(document).ready(function($){   
	$(".saswp-tabs a").click(function(e){
		var href = $(this).attr('href');                
		var currentTab = getParameterByName('tab',href);
		if(!currentTab){
			currentTab = "general";
		}                                                                
		$(this).siblings().removeClass("nav-tab-active");
		$(this).addClass("nav-tab-active");
		$(".form-wrap").find(".saswp-"+currentTab).siblings().hide();
		$(".form-wrap .saswp-"+currentTab).show();
		window.history.pushState("", "", href);
		return false;
	});     
        
        $(".saswp-schame-type-select").change(function(){
            $(".saswp-custom-fields-table").html('');
            var schematype = $  (this).val(); 
            
           $(".saswp-option-table-class tr").each(function(index,value){                
               if(index>0){
                   $(this).hide(); 
                   $(this).find('select').attr('disabled', true);
               }                               
            });              
            if(schematype == 'TechArticle' || schematype == 'Article' || schematype == 'Blogposting' || schematype == 'NewsArticle' || schematype == 'WebPage'){
               
                $(".saswp-enable-speakable").parent().parent().show();
            }else{
                $(".saswp-enable-speakable").parent().parent().hide();
            }
            
            if(schematype == 'local_business'){
             $(".saswp-option-table-class tr").eq(1).show();   
             $(".saswp-business-text-field-tr").show();
             $(".saswp-option-table-class tr").find('select').attr('disabled', false);
            // $("#saswp_dayofweek").attr('disabled', false);
             $('.select-post-type').val('show_globally').trigger('change');             
             }
             if(schematype == 'Service'){            
             $(".saswp-service-text-field-tr").show();
             $(".saswp-option-table-class tr").find('select').attr('disabled', false);
             }
             if(schematype == 'Review'){            
             $(".saswp-review-text-field-tr").show();  
             $(".saswp-option-table-class tr").find('select').attr('disabled', false);
             saswp_item_reviewed_call();
             }
             if(schematype == 'Product'){            
             $(".saswp-product-text-field-tr").show();  
             $(".saswp-option-table-class tr").find('select').attr('disabled', false);
             }
             if(schematype == 'Event'){            
             $(".saswp-event-text-field-tr").show();  
             $(".saswp-option-table-class tr").find('select').attr('disabled', false);
             }
             if(schematype == 'AudioObject'){            
             $(".saswp-audio-text-field-tr").show();               
             }
             if(schematype == 'SoftwareApplication'){            
             $(".saswp-softwareapplication-text-field-tr").show();               
             }
             
              $(".saswp-schem-type-note").addClass('saswp_hide');
             if(schematype == 'qanda'){
              $(".saswp-schem-type-note").removeClass('saswp_hide');   
             }
             
             $(".saswp-job-posting-note").addClass('saswp_hide');
             
             
//             if(schematype == 'JobPosting'){
//              $(".saswp-job-posting-note").removeClass('saswp_hide');   
//             }
             
             saswp_enable_rating_review();
        }); 
        
        $("#saswp_business_type").change(function(){
            var businesstype = $  (this).val(); 
            var schematype = $(".saswp-schame-type-select").val();
            
           $(".saswp-option-table-class tr").each(function(index,value){                
               if(index>1){
                   $(this).hide(); 
                   $(this).find('select').attr('disabled', true);
               }                               
            }); 
            
            if(schematype == 'TechArticle' || schematype == 'Article' || schematype == 'Blogposting' || schematype == 'NewsArticle' || schematype == 'WebPage'){
               
                $(".saswp-enable-speakable").parent().parent().show();
            }else{
                $(".saswp-enable-speakable").parent().parent().hide();
            }
            
            if(schematype == 'local_business'){
            $(".saswp-"+businesstype+'-tr').show(); 
            $(".saswp-business-text-field-tr").show(); 
            $(".saswp-"+businesstype+'-tr').find('select').attr('disabled', false); 
           // $("#saswp_dayofweek").attr('disabled', false);
            } 
             if(schematype == 'Service'){            
             $(".saswp-service-text-field-tr").show();  
             $(".saswp-service-text-field-tr").find('select').attr('disabled', false); 
             }
             if(schematype == 'Product'){            
             $(".saswp-product-text-field-tr").show(); 
             $(".saswp-product-text-field-tr").find('select').attr('disabled', false); 
             }
             if(schematype == 'AudioObject'){            
             $(".saswp-audio-text-field-tr").show();               
             }
             if(schematype == 'SoftwareApplication'){            
             $(".saswp-softwareapplication-text-field-tr").show();               
             }
             
             if(schematype == 'Review'){            
             $(".saswp-review-text-field-tr").show(); 
             $(".saswp-review-text-field-tr").find('select').attr('disabled', false);
             }
             if(schematype == 'Event'){            
             $(".saswp-event-text-field-tr").show(); 
             $(".saswp-event-text-field-tr").find('select').attr('disabled', false);
             }
            saswp_enable_rating_review();
        }).change(); 
        
        
    //Settings page jquery starts here    
 
    
     
    $(".saswp-checkbox").change(function(){
        
                        var id = $(this).attr("id");
                        var plugin_name =  id.replace('-checkbox','');
                        var text = $("#"+plugin_name).next('p').text(); 
                        
                        if ($(this).is(':checked') && text !=='') {              
                              $("#"+plugin_name).next('p').removeClass('saswp_hide');                   
                        }else{
                            
                            if($("#"+plugin_name).next('p').attr('data-id') == 1){
                                $("#"+plugin_name).next('p').text('This feature is only available in pro version');
                            }else{
                                $("#"+plugin_name).next('p').addClass('saswp_hide');
                            }                                                        
                        }                                                                                  
                  switch(id){
                      case 'saswp-for-wordpress-checkbox':  
                          
                          if ($(this).is(':checked')) {              
                            $("#saswp-for-wordpress").val(1);  
                          }else{
                            $("#saswp-for-wordpress").val(0);  
                          }                          
                          break;
                      case 'saswp-facebook-enable-checkbox':  
                          
                          if ($(this).is(':checked')) {              
                            $("#saswp-facebook-enable").val(1); 
                            $("#sd_facebook").show();
                          }else{
                            $("#saswp-facebook-enable").val(0);  
                            $("#sd_facebook").hide();
                          }                          
                          break;   
                      case 'saswp-twitter-enable-checkbox':  
                          
                          if ($(this).is(':checked')) {              
                            $("#saswp-twitter-enable").val(1);
                            $("#sd_twitter").show();
                          }else{
                            $("#saswp-twitter-enable").val(0);  
                            $("#sd_twitter").hide();
                          }                          
                          break;
                      case 'saswp-google-plus-enable-checkbox':  
                          
                          if ($(this).is(':checked')) {              
                            $("#saswp-google-plus-enable").val(1);  
                            $("#sd_google_plus").show();
                          }else{
                            $("#saswp-google-plus-enable").val(0); 
                            $("#sd_google_plus").hide();
                          }                          
                          break;
                      case 'saswp-instagram-enable-checkbox':  
                          
                          if ($(this).is(':checked')) {              
                            $("#saswp-instagram-enable").val(1);  
                            $("#sd_instagram").show();
                          }else{
                            $("#saswp-instagram-enable").val(0);  
                            $("#sd_instagram").hide();
                          }                          
                          break;
                      case 'saswp-youtube-enable-checkbox':  
                          
                          if ($(this).is(':checked')) {
                            $("#sd_youtube").show();  
                            $("#saswp-youtube-enable").val(1);  
                          }else{
                            $("#saswp-youtube-enable").val(0);
                            $("#sd_youtube").hide();
                          }                          
                          break;
                      case 'saswp-linkedin-enable-checkbox':  
                          
                          if ($(this).is(':checked')) {              
                            $("#saswp-linkedin-enable").val(1);  
                            $("#sd_linkedin").show();
                          }else{
                            $("#saswp-linkedin-enable").val(0);
                            $("#sd_linkedin").hide();
                          }                          
                          break; 
                      case 'saswp-pinterest-enable-checkbox':  
                          
                          if ($(this).is(':checked')) {              
                            $("#saswp-pinterest-enable").val(1);  
                            $("#sd_pinterest").show();
                          }else{
                            $("#saswp-pinterest-enable").val(0); 
                            $("#sd_pinterest").hide();
                          }                          
                          break; 
                      case 'saswp-soundcloud-enable-checkbox':  
                          
                          if ($(this).is(':checked')) {              
                            $("#saswp-soundcloud-enable").val(1);  
                            $("#sd_soundcloud").show();
                          }else{
                            $("#saswp-soundcloud-enable").val(0);
                            $("#sd_soundcloud").hide();
                          }                          
                          break; 
                      case 'saswp-tumblr-enable-checkbox':  
                          
                          if ($(this).is(':checked')) {              
                            $("#saswp-tumblr-enable").val(1);  
                            $("#sd_tumblr").show();
                          }else{
                            $("#saswp-tumblr-enable").val(0);  
                            $("#sd_tumblr").hide();
                          }                          
                          break; 
                      case 'saswp-for-amp-checkbox':
                          
                          if ($(this).is(':checked')) {              
                            $("#saswp-for-amp").val(1);  
                          }else{
                            $("#saswp-for-amp").val(0);  
                          }                                           
                      break;
                      case 'saswp_kb_contact_1_checkbox':
                          
                        if ($(this).is(':checked')) {              
                         $("#saswp_kb_contact_1").val(1); 
                         $("#saswp_kb_telephone, #saswp_contact_type").parent().parent('li').removeClass("saswp-display-none");  
                       }else{                                                
                         $("#saswp_kb_contact_1").val(0);  
                         $("#saswp_kb_telephone, #saswp_contact_type").parent().parent('li').addClass("saswp-display-none"); 
                       }
                      break;
                      case 'saswp-logo-dimensions-check':
                          
                        if ($(this).is(':checked')) {              
                           $("#saswp-logo-dimensions").val(1);  
                           $("#saswp-logo-width, #saswp-logo-height").parent().parent('li').show();                           
                         }else{                             
                           $("#saswp-logo-dimensions").val(0);            
                           $("#saswp-logo-width, #saswp-logo-height").parent().parent('li').hide();
                         }
                      break;
                      case 'saswp_archive_schema_checkbox':
                          
                            if ($(this).is(':checked')) {              
                                $("#saswp_archive_schema").val(1);
                                $(".saswp_archive_schema_type_class").parent().parent().show();
                              }else{
                                $("#saswp_archive_schema").val(0);           
                                $(".saswp_archive_schema_type_class").parent().parent().hide();
                              }
                      break;
                      
                      case 'saswp_website_schema_checkbox':
                          
                            if ($(this).is(':checked')) {              
                              $("#saswp_website_schema").val(1);  
                              $("#saswp_search_box_schema").parent().parent().show();  
                            }else{
                              $("#saswp_website_schema").val(0);           
                              $("#saswp_search_box_schema").parent().parent().hide();
                            }
                      break;
                      
                      case 'saswp_search_box_schema_checkbox':
                          
                            if ($(this).is(':checked')) {              
                              $("#saswp_search_box_schema").val(1);             
                            }else{
                              $("#saswp_search_box_schema").val(0);           
                            }
                      break;
                      
                      case 'saswp_breadcrumb_schema_checkbox':
                          
                            if ($(this).is(':checked')) {              
                              $("#saswp_breadcrumb_schema").val(1);             
                            }else{
                              $("#saswp_breadcrumb_schema").val(0);           
                            }
                      break;
                      
                      case 'saswp_site_navigation_menu_checkbox':
                          
                            if ($(this).is(':checked')) {              
                              $("#saswp_site_navigation_menu").val(1);             
                            }else{
                              $("#saswp_site_navigation_menu").val(0);           
                            }
                      break;
                      
                      case 'saswp_comments_schema_checkbox':
                          
                            if ($(this).is(':checked')) {              
                              $("#saswp_comments_schema").val(1);             
                            }else{
                              $("#saswp_comments_schema").val(0);           
                            }
                      break;
                      
                      case 'saswp-compativility-checkbox':
                          
                            if ($(this).is(':checked')) {              
                              $("#saswp-flexmlx-compativility").val(1);             
                            }else{
                              $("#saswp-flexmlx-compativility").val(0);           
                            }
                      break;
                      
                      case 'saswp-review-module-checkbox':
                          
                            if ($(this).is(':checked')) {              
                              $("#saswp-review-module").val(1);             
                            }else{
                              $("#saswp-review-module").val(0);           
                            }
                      break;
                      
                      case 'saswp-kk-star-raring-checkbox':
                          
                            if ($(this).is(':checked')) {              
                              $("#saswp-kk-star-raring").val(1);             
                            }else{
                              $("#saswp-kk-star-raring").val(0);           
                            }
                      break;
                      case 'saswp-woocommerce-checkbox':
                          
                            if ($(this).is(':checked')) {              
                              $("#saswp-woocommerce").val(1);                              
                            }else{
                              $("#saswp-woocommerce").val(0);                                         
                            }
                      break;
                      
                      case 'saswp-extra-checkbox':
                          
                            if ($(this).is(':checked')) {              
                              $("#saswp-extra").val(1);             
                            }else{
                              $("#saswp-extra").val(0);           
                            }
                      break;
                      
                      case 'saswp-dw-question-answer-checkbox':
                          
                            if ($(this).is(':checked')) {              
                              $("#saswp-dw-question-answer").val(1);             
                            }else{
                              $("#saswp-dw-question-answer").val(0);           
                            }
                      break;
                      
                      case 'saswp-wp-job-manager-checkbox':
                          
                            if ($(this).is(':checked')) {              
                              $("#saswp-wp-job-manager").val(1);             
                            }else{
                              $("#saswp-wp-job-manager").val(0);           
                            }
                      break;
                      
                      case 'saswp-yoast-checkbox':
                          
                            if ($(this).is(':checked')) {              
                              $("#saswp-yoast").val(1);             
                            }else{
                              $("#saswp-yoast").val(0);           
                            }
                      break;
                      
                      case 'saswp-tagyeem-checkbox':
                          
                            if ($(this).is(':checked')) {              
                              $("#saswp-tagyeem").val(1);             
                            }else{
                              $("#saswp-tagyeem").val(0);           
                            }
                      break;
                      
                      case 'saswp-the-events-calendar-checkbox':
                          
                            if ($(this).is(':checked')) {              
                              $("#saswp-the-events-calendar").val(1);             
                            }else{
                              $("#saswp-the-events-calendar").val(0);           
                            }
                      break;
                      
                      case 'saswp-the-events-calendar-checkbox':
                          
                            if ($(this).is(':checked')) {              
                              $("#saswp-the-events-calendar").val(1);             
                            }else{
                              $("#saswp-the-events-calendar").val(0);           
                            }
                      break;
                      
                      case 'saswp-woocommerce-booking-checkbox':
                          
                            if ($(this).is(':checked')) {              
                              $("#saswp-woocommerce-booking").val(1);  
                              $("#saswp-woocommerce-booking-main").val(1); 
                            }else{
                              $("#saswp-woocommerce-booking").val(0); 
                              $("#saswp-woocommerce-booking-main").val(0); 
                            }
                      break;
                      
                      case 'saswp-woocommerce-booking-main-checkbox':
                          
                            if ($(this).is(':checked')) {              
                              $("#saswp-woocommerce-booking-main").val(1);  
                              $("#saswp-woocommerce-booking").val(1); 
                            }else{
                              $("#saswp-woocommerce-booking-main").val(0);  
                              $("#saswp-woocommerce-booking").val(0);
                            }
                      break;
                      
                      case 'saswp-woocommerce-membership-checkbox':
                          
                            if ($(this).is(':checked')) {              
                              $("#saswp-woocommerce-membership").val(1);             
                            }else{
                              $("#saswp-woocommerce-membership").val(0);           
                            }
                      break;
                      
                      case 'saswp-defragment-checkbox':
                          
                            if ($(this).is(':checked')) {              
                              $("#saswp-defragment").val(1);             
                            }else{
                              $("#saswp-defragment").val(0);           
                            }
                      break;
                      
                      case 'saswp-cooked-checkbox':
                          
                            if ($(this).is(':checked')) {              
                              $("#saswp-cooked").val(1);             
                            }else{
                              $("#saswp-cooked").val(0);           
                            }
                      break;
                      
                      case 'saswp-flexmlx-compativility-checkbox':
                          
                            if ($(this).is(':checked')) {              
                              $("#saswp-flexmlx-compativility").val(1);             
                            }else{
                              $("#saswp-flexmlx-compativility").val(0);           
                            }
                      break;
                      
                      case 'saswp-google-review-checkbox':
                          
                            if ($(this).is(':checked')) {              
                              $("#saswp-google-review").val(1); 
                               $("#saswp_google_place_api_key").parent().parent().show();
                            }else{
                              $("#saswp-google-review").val(0);           
                               $("#saswp_google_place_api_key").parent().parent().hide();
                            }
                      break;
                      
                      case 'saswp-markup-footer-checkbox':
                          
                            if ($(this).is(':checked')) {              
                              $("#saswp-markup-footer").val(1);                                
                            }else{
                              $("#saswp-markup-footer").val(0);                                          
                            }
                      break;
                      
                      case 'saswp-pretty-print-checkbox':
                          
                            if ($(this).is(':checked')) {              
                              $("#saswp-pretty-print").val(1);                                
                            }else{
                              $("#saswp-pretty-print").val(0);                                          
                            }
                      break;
                      
                      case 'saswp-wppostratings-raring-checkbox':
                          
                            if ($(this).is(':checked')) {              
                              $("#saswp-wppostratings-raring").val(1);                                
                            }else{
                              $("#saswp-wppostratings-raring").val(0);                                          
                            }
                      break;
                      
                      case 'saswp-bbpress-checkbox':
                          
                            if ($(this).is(':checked')) {              
                              $("#saswp-bbpress").val(1);                                
                            }else{
                              $("#saswp-bbpress").val(0);                                          
                            }
                      break;
                      
                      case 'saswp-microdata-cleanup-checkbox':
                          
                            if ($(this).is(':checked')) {              
                              $("#saswp-microdata-cleanup").val(1);                                
                            }else{
                              $("#saswp-microdata-cleanup").val(0);                                          
                            }
                      break;
                      
                      
                      default:
                          break;
                  }
                             
         }).change();
        
          $("#saswp_kb_type").change(function(){
              
          var datatype = $(this).val(); 
          
          $(".saswp_org_fields, .saswp_person_fields").parent().parent().addClass('saswp_hide');
          $(".saswp_kg_logo").parent().parent().parent().addClass('saswp_hide');
          $("#sd-person-image").parent().parent().parent().addClass('saswp_hide');
          
          
          if(datatype == 'Organization'){
              
              $(".saswp_org_fields").parent().parent().removeClass('saswp_hide');
              $(".saswp_person_fields").parent().parent().addClass('saswp_hide');
              $(".saswp_kg_logo").parent().parent().parent().removeClass('saswp_hide');
              $("#sd-person-image").parent().parent().parent().addClass('saswp_hide');
          }
          if(datatype == 'Person'){
              
              $(".saswp_org_fields").parent().parent().addClass('saswp_hide');
              $(".saswp_person_fields").parent().parent().removeClass('saswp_hide');
              $(".saswp_kg_logo").parent().parent().parent().removeClass('saswp_hide');
              $("#sd-person-image").parent().parent().parent().removeClass('saswp_hide');
          }

     }).change(); 
     
     
     $(document).on("click", "input[data-id=media]" ,function(e) {	// Application Icon upload
		e.preventDefault();
                var current = $(this);
                var button = current;
                var id = button.attr('id').replace('_button', '');                
		var saswpMediaUploader = wp.media({
			title: "Application Icon",
			button: {
				text: "Select Icon"
			},
			multiple: false,  // Set this to true to allow multiple files to be selected
                        library:{type : 'image'}
		})
		.on("select", function() {
			var attachment = saswpMediaUploader.state().get('selection').first().toJSON();                            
			
                         $("#"+id).val(attachment.url);
                         $("input[data-id='"+id+"_id']").val(attachment.id);
                         $("input[data-id='"+id+"_height']").val(attachment.height);
                         $("input[data-id='"+id+"_width']").val(attachment.width);
                         $("input[data-id='"+id+"_thumbnail']").val(attachment.url);
                         
                         if(current.attr('id') === 'sd_default_image_button'){
                             
                             $("#sd_default_image_width").val(attachment.width);
                             $("#sd_default_image_height").val(attachment.height);
                        
                         }                         
                         $(".saswp_image_div_"+id).html('<div class="saswp_image_thumbnail"><img class="saswp_image_prev" src="'+attachment.url+'"/><a data-id="'+id+'" href="#" class="saswp_prev_close">X</a></div>');
                        
		})
		.open();
	});
        
        $(document).on("click", ".saswp_prev_close", function(e){
                e.preventDefault();
                
                var id = $(this).attr('data-id');   
                console.log(id);
                $(this).parent().remove();                
                $("#"+id).val('');
                $("input[data-id='"+id+"_id']").val('');
                $("input[data-id='"+id+"_height']").val('');
                $("input[data-id='"+id+"_width']").val('');
                $("input[data-id='"+id+"_thumbnail']").val('');
                
                 if(id === 'sd_default_image'){
                             
                    $("#sd_default_image_width").val('');
                    $("#sd_default_image_height").val('');
                        
                } 
                
                
        });
        
        //Settings page jquery ends here


        $(document).on("change",".saswp-schema-type-toggle", function(e){
               var schema_id = $(this).attr("data-schema-id"); 
               var post_id =   $(this).attr("data-post-id");     
               if($(this).is(':checked')){
               var status = 1;    
               }else{
               var status = 0;    
               }
             $.ajax({
                            type: "POST",    
                            url:ajaxurl,                    
                            dataType: "json",
                            data:{action:"saswp_enable_disable_schema_on_post",status:status, schema_id:schema_id, post_id:post_id, saswp_security_nonce:saswp_localize_data.saswp_security_nonce},
                            success:function(response){                                                     
                            },
                            error: function(response){                    
                            console.log(response);
                            }
                            });                                      

        });


         $(document).on("click",".saswp-reset-data", function(e){
                e.preventDefault();
             
                var saswp_confirm = confirm("Are you sure?");
             
                if(saswp_confirm == true){
                    
                $.ajax({
                            type: "POST",    
                            url:ajaxurl,                    
                            dataType: "json",
                            data:{action:"saswp_reset_all_settings", saswp_security_nonce:saswp_localize_data.saswp_security_nonce},
                            success:function(response){                               
                                setTimeout(function(){ location.reload(); }, 1000);
                            },
                            error: function(response){                    
                            console.log(response);
                            }
                            }); 
                
                }
                                                                 

        });
        
        //Licensing jquery starts here
        $(document).on("click",".saswp_license_activation", function(e){
                e.preventDefault();
                
                var license_status = $(this).attr('license-status');
                var add_on         = $(this).attr('add-on');
                var license_key    = $("#"+add_on+"_addon_license_key").val();
               
            if(license_status && add_on && license_key){
                
                $.ajax({
                            type: "POST",    
                            url:ajaxurl,                    
                            dataType: "json",
                            data:{action:"saswp_license_status_check",license_key:license_key,license_status:license_status, add_on:add_on, saswp_security_nonce:saswp_localize_data.saswp_security_nonce},
                            success:function(response){                               
                               
                               $("#"+add_on+"_addon_license_key_status").val(response['status']);
                                                                
                              if(response['status'] =='active'){  
                               $(".saswp-"+add_on+"-dashicons").addClass('dashicons-yes');
                               $(".saswp-"+add_on+"-dashicons").removeClass('dashicons-no-alt');
                               $(".saswp-"+add_on+"-dashicons").css("color", "green");
                               
                               $(".saswp_license_activation[add-on='" + add_on + "']").attr("license-status", "inactive");
                               $(".saswp_license_activation[add-on='" + add_on + "']").text("Deactivate");
                               
                               $(".saswp_license_status_msg[add-on='" + add_on + "']").text('Activated');
                               
                               $(".saswp_license_status_msg[add-on='" + add_on + "']").css("color", "green");                                
                               $(".saswp_license_status_msg[add-on='" + add_on + "']").text(response['message']);
                                                                                             
                              }else{
                                  
                               $(".saswp-"+add_on+"-dashicons").addClass('dashicons-no-alt');
                               $(".saswp-"+add_on+"-dashicons").removeClass('dashicons-yes');
                               $(".saswp-"+add_on+"-dashicons").css("color", "red");
                               
                               $(".saswp_license_activation[add-on='" + add_on + "']").attr("license-status", "active");
                               $(".saswp_license_activation[add-on='" + add_on + "']").text("Activate");
                               
                               $(".saswp_license_status_msg[add-on='" + add_on + "']").css("color", "red"); 
                               $(".saswp_license_status_msg[add-on='" + add_on + "']").text(response['message']);
                              }
                                                                                          
                            },
                            error: function(response){                    
                                console.log(response);
                            }
                            });
                            
            }

        });
        //Licensing jquery ends here
  //query form send starts here

        $(".saswp-send-query").on("click", function(e){
            e.preventDefault();   
            var message = $("#saswp_query_message").val();              
            if($.trim(message) !=''){
             $.ajax({
                            type: "POST",    
                            url:ajaxurl,                    
                            dataType: "json",
                            data:{action:"saswp_send_query_message", message:message, saswp_security_nonce:saswp_localize_data.saswp_security_nonce},
                            success:function(response){                       
                              if(response['status'] =='t'){
                                $(".saswp-query-success").show();
                                $(".saswp-query-error").hide();
                              }else{
                                  console.log('dd');
                                $(".saswp-query-success").hide();  
                                $(".saswp-query-error").show();
                              }
                            },
                            error: function(response){                    
                            console.log(response);
                            }
                            });   
            }else{
                alert('Please enter the message');
            }                        

        });
        
        //Importer from schema plugin starts here

        $(".saswp-import-plugins").on("click", function(e){
            e.preventDefault();   
            var current_selection = $(this);
            current_selection.addClass('updating-message');
            var plugin_name = $(this).attr('data-id');                      
                         $.get(ajaxurl, 
                             { action:"saswp_import_plugin_data", plugin_name:plugin_name, saswp_security_nonce:saswp_localize_data.saswp_security_nonce},
                              function(response){                                  
                              if(response['status'] =='t'){                                  
                                  $(current_selection).parent().find(".saswp-imported-message").text(response['message']);
                                  $(current_selection).parent().find(".saswp-imported-message").removeClass('saswp-error');
                                   setTimeout(function(){ location.reload(); }, 2000);
                              }else{
                                  $(current_selection).parent().find(".saswp-imported-message").addClass('saswp-error');
                                  $(current_selection).parent().find(".saswp-imported-message").text(response['message']);                                  
                              }  
                              current_selection.removeClass('updating-message');
                             },'json');
        });
        
        
        $(".saswp-feedback-no-thanks").on("click", function(e){
            e.preventDefault();               
                         $.get(ajaxurl, 
                             { action:"saswp_feeback_no_thanks"},
                              function(response){                                  
                              if(response['status'] =='t'){                                  
                                 $(".saswp-feedback-notice").hide();                                 
                              }       		   		
                             },'json');
        });
        
        $(".saswp-feedback-remindme").on("click", function(e){
            e.preventDefault();               
                         $.get(ajaxurl, 
                             { action:"saswp_feeback_remindme"},
                              function(response){                                  
                              if(response['status'] =='t'){                                  
                                 $(".saswp-feedback-notice").hide();                                 
                              }       		   		
                             },'json');
        });
        
         $(document).on("change",'.saswp-local-business-type-select', function(e){
            e.preventDefault();    
                        var current = $(this);    
                        var business_type = $(this).val();
                         $.get(ajaxurl, 
                             { action:"saswp_get_sub_business_ajax", business_type:business_type, saswp_security_nonce:saswp_localize_data.saswp_security_nonce},
                              function(response){    
                                  
                              if(response['status'] =='t'){ 
                                   $(".saswp-local-business-name-select").parents('tr').remove();  
                                var schema_id = current.parents('.saswp-post-specific-wrapper').attr('data-id');                                
                                var html ='<tr><th><label for="saswp_business_name_'+schema_id+'">Sub Business Type</label></th>';
                                    html +='<td><select class="saswp-local-business-name-select" id="saswp_business_name_'+schema_id+'" name="saswp_business_name_'+schema_id+'">';    
                                    $.each(response['result'], function(index, element){
                                        html +='<option value="'+index+'">'+element+'</option>';      
                                    });                                    
                                    html +='</select></td>';    
                                    html +='</tr>'; 
                                    current.parents('.form-table tr:first').after(html);
                              }else{
                                    $(".saswp-local-business-name-select").parents('tr').remove();
                              }       		   		
                             },'json');
        });
        
        
        function saswp_item_reviewed_call(){
            
            $(".saswp-item-reviewed").change(function(e){
            e.preventDefault();
            var schema_type =""; 
            
            if($('select#schema_type option:selected').val()){
               schema_type = $('select#schema_type option:selected').val();    
            }       
            if($(".saswp-tab-links.selected").attr('saswp-schema-type')){
               schema_type = $(".saswp-tab-links.selected").attr('saswp-schema-type');    
            }
            
            if(schema_type === 'Review'){
                
                        var current = $(this);    
                        var item    = $(this).val();
                        var post_id = saswp_localize_data.post_id;
                        var schema_id = $(current).attr('data-id');  
                        var post_specific = $(current).attr('post-specific');  
                         $.get(ajaxurl, 
                             { action:"saswp_get_item_reviewed_fields",schema_id:schema_id,  post_specific:post_specific ,item:item, post_id:post_id, saswp_security_nonce:saswp_localize_data.saswp_security_nonce},
                              function(response){    
                                  
                                $(current).parent().parent().nextAll().remove(".saswp-review-tr");                                    
                                $(current).parent().parent().after(response);    
                                
                             });
                
            }
                        
                             
        }).change();
            
        }
        saswp_item_reviewed_call();
        
        
        
        
        
        function saswpAddTimepicker(){
         $('.saswp-local-schema-time-picker').timepicker({ 'timeFormat': 'H:i:s'});
        }
        $('.saswp-local-schema-time-picker').timepicker({ 'timeFormat': 'H:i:s'});
        
        $(".saswp_custom_schema_post_enable").on("click", function(e){
            
            e.preventDefault();  
            
            var current = $(this);
            current.addClass('updating-message');
            e.preventDefault();                                                    
                         $.get(ajaxurl, 
                             { action:"saswp_custom_schema_post_enable", post_id: saswp_localize_data.post_id,saswp_security_nonce:saswp_localize_data.saswp_security_nonce},
                              function(response){   
                               current.remove();                           
                               $("#post_specific .inside").append(response); 
                               current.removeClass('updating-message');                               
                               $(".saswp-modify_schema_post_enable").remove();                                                                                      
                             });
            
            
           
        });
        
        $(".saswp-modify_schema_post_enable").on("click", function(e){
            var current = $(this);
            current.addClass('updating-message');
            e.preventDefault();                                                    
                         $.get(ajaxurl, 
                             { action:"saswp_modify_schema_post_enable", post_id: saswp_localize_data.post_id,saswp_security_nonce:saswp_localize_data.saswp_security_nonce},
                              function(response){   
                               current.remove();   
                               $(".saswp_custom_schema_post_enable").remove();
                               $("#post_specific .inside").append(response); 
                               current.removeClass('updating-message');
                               saswpAddTimepicker();  
                               saswp_schema_datepicker();
                               saswp_enable_rating_review();
                               saswp_item_reviewed_call();
                             });
                             
        });
        saswp_schema_datepicker();
        function saswp_schema_datepicker(){
        
            $('.saswp-local-schema-datepicker-picker').datepicker({
             dateFormat: "yy-mm-dd",
             minDate: 0
          });
        }
        
        
        
        //Review js starts here
        
        $(document).on("click", ".saswp-add-more-item",function(e){
            e.preventDefault();
            var rows = $('.saswp-review-item-list-table tr').length;
            console.log(rows);
            var html = '<tr class="saswp-review-item-tr">';
                html += '<td>Review Item Feature</td>';
                html += '<td><input type="text" name="saswp-review-item-feature[]"></td>';
                html += '<td>Rating</td>';
                html += '<td><input step="0.1" min="0" max="5" type="number" name="saswp-review-item-star-rating[]"></td>';
                html += '<td><a type="button" class="saswp-remove-review-item button">x</a></td>';
                html += '</tr>';                
                $(".saswp-review-item-list-table").append(html);
                
        });
        
        $(document).on("click", ".saswp-remove-review-item", function(e){
            e.preventDefault();        
            $(this).parent().parent('tr').remove();
       });
        
        $(document).on("focusout", ".saswp-review-item-tr input[type=number]", function(e){
            e.preventDefault();    
            var total_rating = 0;
            var element_count = $(".saswp-review-item-tr input[type=number]").length;            
            $(".saswp-review-item-tr input[type=number]").each(function(index, element){
                if($(element).val() ==''){
                  total_rating += parseFloat(0);  
                }else{
                  total_rating += parseFloat($(element).val());  
                }
                           
            });
            var over_all_rating = total_rating / element_count;
            $("#saswp-review-item-over-all").val(over_all_rating);
       });
       
       $("#saswp-review-location").change(function(){
          var location = $(this).val();
          $(".saswp-review-shortcode").addClass('saswp_hide');
          if(location == 3){  
              $(".saswp-review-shortcode").removeClass('saswp_hide');
          }                                          
        }).change();  
        
        $("#saswp-review-item-enable").change(function(){
                          if ($(this).is(':checked')) {              
                            $(".saswp-review-fields").show();  
                          }else{
                            $(".saswp-review-fields").hide();  
                          }                                        
        }).change();  
        
        $(document).on("click", ".saswp-restore-post-schema", function(e){
                    e.preventDefault(); 
                    var current = $(this);
                    current.addClass('updating-message');
            
                    if($(".saswp-post-specific-schema-ids").val()){
                        var schema_ids = JSON.parse($(".saswp-post-specific-schema-ids").val());                           
                    }
            
                         $.post(ajaxurl, 
                             { action:"saswp_restore_schema", schema_ids:schema_ids,post_id: saswp_localize_data.post_id, saswp_security_nonce:saswp_localize_data.saswp_security_nonce},
                              function(response){                                  
                              if(response['status'] =='t'){                                                                    
                                   setTimeout(function(){ location.reload(); }, 1000);
                              }else{
                                  alert(response['msg']);
                                  setTimeout(function(){ location.reload(); }, 1000);
                              }  
                              current.removeClass('updating-message');
                             },'json');
        });
                
        //Review js ends here
                
        $(document).on("click","div.saswp-tab ul.saswp-tab-nav a", function(e){
            e.preventDefault();
                var attr = $(this).attr('data-id');
                $(".saswp-post-specific-wrapper").hide();            
                $("#"+attr).show();           
                $('div.saswp-tab ul.saswp-tab-nav a').removeClass('selected');
                $('div.saswp-tab ul.saswp-tab-nav li').removeClass('selected');
                $(this).addClass('selected'); 
                $(this).parent().addClass('selected'); 
            saswp_enable_rating_review();
        });
        
        
        $('#saswp-global-tabs a:first').addClass('saswp-global-selected');
        $('.saswp-global-container').hide();
        $('.saswp-global-container:first').show();
        
        $('#saswp-global-tabs a').click(function(){
            var t = $(this).attr('data-id');
            
          if(!$(this).hasClass('saswp-global-selected')){ 
            $('#saswp-global-tabs a').removeClass('saswp-global-selected');           
            $(this).addClass('saswp-global-selected');

            $('.saswp-global-container').hide();
            $('#'+t).show();
         }
        });
        
        //Importer from schema plugin ends here
        
        //custom fields modify schema starts here
        
        //Changing the url of add new schema type 
        $('a[href="'+saswp_localize_data.new_url_selector+'"]').attr( 'href', saswp_localize_data.new_url_href); 
        
        
       $("#saswp_enable_custom_field").change(function(){
            if ($(this).is(':checked')) { 
                $(".saswp-custom-fields-div").show();
            }else{
                $(".saswp-custom-fields-div").hide();
            }
        });
       $(document).on('change','.saswp-custom-fields-name',function(){
           
           $(this).parent().parent('tr').find("td:eq(1)").html('');
           var field_name = $(this).val();
           var html = '';
               html += '<select class="saswp-custom-fields-select2" name="saswp_custom_fields['+field_name+']">';
               html += '</select>'; 
               $(this).parent().parent('tr').find("td:eq(1)").html(html);
               saswpCustomSelect2();           
       } ); 
        
        
       $(document).on("click", '.saswp-skip-button', function(){
           $(this).parent().parent().hide();
       }); 
       
    
        //TvSeries schema starts here
        
        $(document).on("click", ".saswp-tvseries-actor", function(e){
           e.preventDefault();
           
           var schema_id = $(this).attr('data-id');
           var count =  $(".saswp-tvseries-actor-table-div").length;
           var index = $( ".saswp-tvseries-actor-table-div:nth-child("+count+")" ).attr('data-id');
               index = ++index;
               
           if(!index){
               index = 0;
           }
                   
            var html = '';
            
                   html += '<div class="saswp-tvseries-actor-table-div" data-id="'+index+'">'    
                        +  '<a class="saswp-table-close">X</a>'
                        + '<table class="form-table saswp-tvseries-actor-table">'                                                                                           
                        + '<tr>'
                        + '<th>Actor Name</th><td><input style="width:100%" type="text" id="saswp_tvseries_actor_name_'+index+'_'+schema_id+'" name="tvseries_actor_'+schema_id+'['+index+'][saswp_tvseries_actor_name]"></td>'
                        + '</tr>'                        
                        + '</table>'
                        + '</div>';
           if(html){
               $('.saswp-tvseries-actor-section[data-id="'+schema_id+'"]').append(html);
           }
            
           
       });
       
        $(document).on("click", ".saswp-tvseries-season", function(e){
           e.preventDefault();
           
           var schema_id = $(this).attr('data-id');
           var count =  $(".saswp-tvseries-season-table-div").length;
           var index = $( ".saswp-tvseries-season-table-div:nth-child("+count+")" ).attr('data-id');
               index = ++index;
               
           if(!index){
               index = 0;
           }
                   
            var html = '';
            
                   html += '<div class="saswp-tvseries-season-table-div" data-id="'+index+'">'    
                        +  '<a class="saswp-table-close">X</a>'
                        + '<table class="form-table saswp-tvseries-season-table">'                                                                                           
                        + '<tr>'
                        + '<th>Season</th><td><input style="width:100%" type="text" id="saswp_tvseries_season_name_'+index+'_'+schema_id+'" name="tvseries_season_'+schema_id+'['+index+'][saswp_tvseries_season_name]"></td>'
                        + '</tr>'
                        + '<tr>'
                        + '<th>Season Published Date</th><td><input class="saswp-local-schema-datepicker-picker" style="width:100%" type="text" id="saswp_tvseries_season_published_date_'+index+'_'+schema_id+'" name="tvseries_season_'+schema_id+'['+index+'][saswp_tvseries_season_published_date]"></td>'
                        + '</tr>'
                        + '<tr>'
                        + '<th>Number Of Episodes</th><td><input style="width:100%" type="text" id="saswp_tvseries_season_episodes_'+index+'_'+schema_id+'" name="tvseries_season_'+schema_id+'['+index+'][saswp_tvseries_season_episodes]"></td>'
                        + '</tr>'
                        + '</table>'
                        + '</div>';
           if(html){
               
               $('.saswp-tvseries-season-section[data-id="'+schema_id+'"]').append(html);
               saswp_schema_datepicker();
                              
           }
            
           
       });
        
        //TvSeries schema ends here
    
        //Medical condition schema starts here
        
        $(document).on("click", ".saswp-mc-cause", function(e){
           e.preventDefault();
           
           var schema_id = $(this).attr('data-id');
           var count =  $(".saswp-mc-cause-table-div").length;
           var index = $( ".saswp-mc-cause-table-div:nth-child("+count+")" ).attr('data-id');
               index = ++index;
               
           if(!index){
               index = 0;
           }
                   
            var html = '';
            
                   html += '<div class="saswp-mc-cause-table-div" data-id="'+index+'">'    
                        +  '<a class="saswp-table-close">X</a>'
                        + '<table class="form-table saswp-mc-cause-table">'                                                                                           
                        + '<tr>'
                        + '<th>Cause</th><td><input style="width:100%" type="text" id="saswp_mc_cause_name_'+index+'_'+schema_id+'" name="mc_cause_'+schema_id+'['+index+'][saswp_mc_cause_name]"></td>'
                        + '</tr>'                        
                        + '</table>'
                        + '</div>';
           if(html){
               $('.saswp-mc-cause-section[data-id="'+schema_id+'"]').append(html);
           }
            
           
       });
       
        $(document).on("click", ".saswp-mc-symptom", function(e){
           e.preventDefault();
           
           var schema_id = $(this).attr('data-id');
           var count =  $(".saswp-mc-symptom-table-div").length;
           var index = $( ".saswp-mc-symptom-table-div:nth-child("+count+")" ).attr('data-id');
               index = ++index;
               
           if(!index){
               index = 0;
           }
                   
            var html = '';
            
                   html += '<div class="saswp-mc-symptom-table-div" data-id="'+index+'">'
                        +  '<a class="saswp-table-close">X</a>'
                        + '<table class="form-table saswp-mc-symptom-table">'                                                                                           
                        + '<tr>'
                        + '<th>Symptom Name</th><td><input style="width:100%" type="text" id="saswp_mc_symptom_name_'+index+'_'+schema_id+'" name="mc_symptom_'+schema_id+'['+index+'][saswp_mc_symptom_name]"></td>'
                        + '</tr>'                        
                        + '</table>'
                        + '</div>';
           if(html){
               $('.saswp-mc-symptom-section[data-id="'+schema_id+'"]').append(html);
           }
            
           
       });
       
       $(document).on("click", ".saswp-mc-risk_factor", function(e){
           e.preventDefault();
           
           var schema_id = $(this).attr('data-id');
           var count =  $(".saswp-mc-risk_factor-table-div").length;
           var index = $( ".saswp-mc-risk_factor-table-div:nth-child("+count+")" ).attr('data-id');
               index = ++index;
               
           if(!index){
               index = 0;
           }
                   
            var html = '';
            
                   html += '<div class="saswp-mc-risk_factor-table-div" data-id="'+index+'">'
                        +  '<a class="saswp-table-close">X</a>'
                        + '<table class="form-table saswp-mc-risk_factor-table">'                                                                                           
                        + '<tr>'
                        + '<th>Risk Factor Name</th><td><input style="width:100%" type="text" id="saswp_mc_risk_factor_name_'+index+'_'+schema_id+'" name="mc_risk_factor_'+schema_id+'['+index+'][saswp_mc_risk_factor_name]"></td>'
                        + '</tr>'                        
                        + '</table>'
                        + '</div>';
           if(html){
               $('.saswp-mc-risk_factor-section[data-id="'+schema_id+'"]').append(html);
           }
            
           
       });
        
        //Medical condition schema ends here
    
        //How to schema js starts here
        
       $(document).on("click", ".saswp-how-to-supply", function(e){
           e.preventDefault();
           
           var schema_id = $(this).attr('data-id');
           var count =  $(".saswp-how-to-supply-table-div").length;
           var index = $( ".saswp-how-to-supply-table-div:nth-child("+count+")" ).attr('data-id');
               index = ++index;
               
           if(!index){
               index = 0;
           }
                   
            var html = '';
            
                   html += '<div class="saswp-how-to-supply-table-div" data-id="'+index+'">'
                        +  '<a class="saswp-table-close">X</a>'
                        + '<table class="form-table saswp-how-to-supply-table">'                                                                                           
                        + '<tr>'
                        + '<th>Supply Name</th><td><input style="width:100%" type="text" id="saswp_howto_supply_name_'+index+'_'+schema_id+'" name="howto_supply_'+schema_id+'['+index+'][saswp_howto_supply_name]"></td>'
                        + '</tr>'
                        + '<tr>'
                        + '<th>Supply Image</th>'
                        + '<td>'
                        + '<fieldset>'
                        + '<input style="width:80%" type="text" id="saswp_howto_supply_image_'+index+'_'+schema_id+'" name="saswp_howto_supply_image_'+index+'_'+schema_id+'">'
                        + '<input type="hidden" data-id="saswp_howto_supply_image_'+index+'_'+schema_id+'_id" name="howto_supply_'+schema_id+'['+index+'][saswp_howto_supply_image_id]" id="saswp_howto_supply_image_'+index+'_'+schema_id+'_id">'
                        + '<input data-id="media" style="width: 19%" class="button" id="saswp_howto_supply_image_'+index+'_'+schema_id+'_button" name="saswp_howto_supply_image_'+index+'_'+schema_id+'_button" type="button" value="Upload">'
                        + '<div class="saswp_image_div_saswp_howto_supply_image_'+index+'_'+schema_id+'">'                                                
                        + '</div>'
                        + '</fieldset>'
                        + '</td>'
                        + '</tr>'
                        + '</table>'
                        + '</div>';
           if(html){
               $('.saswp-how-to-supply-section[data-id="'+schema_id+'"]').append(html);
           }
            
           
       }); 
        
       $(document).on("click", ".saswp-how-to-tool", function(e){
           e.preventDefault();
           
          var schema_id = $(this).attr('data-id');
          var count =  $(".saswp-how-to-tool-table-div").length;
          var index = $( ".saswp-how-to-tool-table-div:nth-child("+count+")" ).attr('data-id');
               index = ++index;
           
           if(!index){
               index = 0;
           }
                   
            var html = '';
            
                   html += '<div class="saswp-how-to-tool-table-div" data-id="'+index+'">'
                        +  '<a class="saswp-table-close">X</a>'
                        + '<table class="form-table saswp-how-to-tool-table">'                                                                                           
                        + '<tr>'
                        + '<th>Tool Name</th><td><input style="width:100%" type="text" id="saswp_howto_tool_name_'+index+'_'+schema_id+'" name="howto_tool_'+schema_id+'['+index+'][saswp_howto_tool_name]"></td>'
                        + '</tr>'
                        + '<tr>'
                        + '<th>Tool Image</th>'
                        + '<td>'
                        + '<fieldset>'
                        + '<input style="width:80%" type="text" id="saswp_howto_tool_image_'+index+'_'+schema_id+'" name="saswp_howto_tool_image_'+index+'_'+schema_id+'">'
                        + '<input type="hidden" data-id="saswp_howto_tool_image_'+index+'_'+schema_id+'_id" name="howto_tool_'+schema_id+'['+index+'][saswp_howto_tool_image_id]" id="saswp_howto_tool_image_'+index+'_'+schema_id+'_id">'
                        + '<input data-id="media" style="width: 19%" class="button" id="saswp_howto_tool_image_'+index+'_'+schema_id+'_button" name="saswp_howto_tool_image_'+index+'_'+schema_id+'_button" type="button" value="Upload">'
                        + '<div class="saswp_image_div_saswp_howto_tool_image_'+index+'_'+schema_id+'">'                                                
                        + '</div>'
                        + '</fieldset>'
                        + '</td>'
                        + '</tr>'
                        + '</table>'
                        + '</div>';
           if(html){
               $('.saswp-how-to-tool-section[data-id="'+schema_id+'"]').append(html);
           }
            
           
       });
       
       $(document).on("click", ".saswp-how-to-step", function(e){
           e.preventDefault();
                      
          var schema_id = $(this).attr('data-id');
          var count =  $(".saswp-how-to-step-table-div").length;
          var index = $( ".saswp-how-to-step-table-div:nth-child("+count+")" ).attr('data-id');
              index = ++index;
           
           if(!index){
               index = 0;
           }
                   
            var html = '';
            
                 html+='<div class="saswp-how-to-step-table-div" data-id="'+index+'">'
                     + '<a class="saswp-table-close">X</a>'
                     + '<table class="form-table saswp-how-to-step-table">'                                                                                          
                     + '<tr>'
                     + '<th>Step Name</th><td><input style="width:100%" type="text" id="saswp_howto_step_name_'+index+'_'+schema_id+'" name="howto_step_'+schema_id+'['+index+'][saswp_howto_step_name]" ></td>'
                     + '</tr>'
                     + '<tr>'
                     + '<th>HowToDirection Text</th><td><input style="width:100%" type="text" id="saswp_howto_direction_text_'+index+'_'+schema_id+'" name="howto_step_'+schema_id+'['+index+'][saswp_howto_direction_text]"></td>'
                     + '</tr>'
                     + '<tr>'
                     + '<th>HowToTip Text</th><td><input style="width:100%" type="text" id="saswp_howto_tip_text_'+index+'_'+schema_id+'" name="howto_step_'+schema_id+'['+index+'][saswp_howto_tip_text]"></td>'
                     + '</tr>'
                     + '<tr>'
                     + '<th>Step Image</th>'
                     + '<td>'
                     + '<fieldset>'
                     + '<input style="width:80%" type="text" id="saswp_howto_step_image_'+index+'_'+schema_id+'" name="saswp_howto_step_image_'+schema_id+'['+index+']">'
                     + '<input type="hidden" data-id="saswp_howto_step_image_'+index+'_'+schema_id+'_id" name="howto_step_'+schema_id+'['+index+'][saswp_howto_step_image_id]" id="saswp_howto_step_image_'+index+'_'+schema_id+'_id">'
                     + '<input data-id="media" style="width: 19%" class="button" id="saswp_howto_step_image_'+index+'_'+schema_id+'_button" name="saswp_howto_step_image_'+index+'_'+schema_id+'_button" type="button" value="Upload">'
                     + '<div class="saswp_image_div_saswp_howto_step_image_'+index+'_'+schema_id+'">'                                                                                
                     + '</div>'
                     + '</fieldset>'
                     + '</td>'
                     + '</tr>'
                     + '</table>'
                     + '</div>';
             
           if(html){
               $('.saswp-how-to-step-section[data-id="'+schema_id+'"]').append(html);
           }
           
           
       });
       
       $(document).on("click", ".saswp-table-close", function(){
           $(this).parent().remove();
       });
        
       //How to schema js ends here
        
       $(document).on("click", '.saswp-add-custom-fields', function(){          
          var schema_type = $('select#schema_type option:selected').val();
          var post_id = $('#post_ID').val();
          if(schema_type !=''){
              $.ajax({
                            type: "POST",    
                            url:ajaxurl,                    
                            dataType: "json",
                            data:{action:"saswp_get_schema_type_fields",post_id:post_id, schema_type:schema_type, saswp_security_nonce:saswp_localize_data.saswp_security_nonce},
                            success:function(response){    
                                
                             if(response.length !=0){
                                 var i =0;
                                 var name ='';
                                 var html = '<tr>';
                                     html += '<td>';
                                     
                                     html += '<select class="saswp-custom-fields-name">';
                                     $.each(response, function(key,value){ 
                                         if(i==0){
                                           name = key;  
                                         }
                                         html += '<option value="'+key+'">'+value+'</option>';
                                         i++;
                                     });                                     
                                     html += '</select>';
                                     
                                     html += '</td>';
                                     html += '<td>';
                                     html += '<select class="saswp-custom-fields-select2" name="saswp_custom_fields['+name+']">';
                                     html += '</select>';                                     
                                     html += '</td>';
                                     html += '</tr>';
                                     $(".saswp-custom-fields-table").append(html);
                                     saswpCustomSelect2();
                             }
                            },
                            error: function(response){                    
                            console.log(response);
                            }
                            });
          }
       });
       saswpCustomSelect2();
       function saswpCustomSelect2(){          
       if((saswp_app_object.post_type == 'saswp' || saswp_app_object.page_now =='saswp') && saswp_app_object.page_now !='saswp_page_structured_data_options'){
           
           $('.saswp-custom-fields-select2').select2({
  		ajax: {
                        type: "POST",    
    			url: ajaxurl, // AJAX URL is predefined in WordPress admin
    			dataType: 'json',
    			delay: 250, // delay in ms while typing when to perform a AJAX search
    			data: function (params) {
      				return {
                                        saswp_security_nonce: saswp_localize_data.saswp_security_nonce,
        				q: params.term, // search query
        				action: 'saswp_get_custom_meta_fields' // AJAX action for admin-ajax.php
      				};
    			},
    			processResults: function( data ) {
				return {
					results: data
				};
			},
			cache: true
		},
		minimumInputLength: 2 // the minimum of symbols to input before perform a search
	});   
           
       }    
           
       }           
      
     function saswp_enable_rating_review(){
           var schema_type ="";                      
           if($('select#schema_type option:selected').val()){
              schema_type = $('select#schema_type option:selected').val();    
           }       
           if($(".saswp-tab-links.selected").attr('saswp-schema-type')){
              schema_type = $(".saswp-tab-links.selected").attr('saswp-schema-type');    
           }
          
         if(schema_type){
             $(".saswp-enable-rating-review-"+schema_type.toLowerCase()).change(function(){
                               
            if($(this).is(':checked')){
            $(this).parent().parent().siblings('.saswp-rating-review-'+schema_type.toLowerCase()).show();            
             }else{
            $(this).parent().parent().siblings('.saswp-rating-review-'+schema_type.toLowerCase()).hide(); 
             }
         
            }).change();   
         }
               
     }      
     saswp_enable_rating_review();                       
     
        //custom fields modify schema ends here
        
        
        //Google review js starts here        
        
        $('a[href="'+saswp_localize_data.collection_post_add_url+'"]').attr( 'href', saswp_localize_data.collection_post_add_new_url); 
        
        
        
        $(document).on("click", '.saswp_coonect_google_place', function(){          
          
          var place_id   = $("#saswp_google_place_id").val();
          var language   = $("#saswp_language_list").val();
          var google_api = $("#saswp_googel_api").val();
          
          if(place_id !=''){
              $.ajax({
                            type: "POST",    
                            url:ajaxurl,                    
                            dataType: "json",
                            data:{action:"saswp_connect_google_place",place_id:place_id, language:language, google_api:google_api, saswp_security_nonce:saswp_localize_data.saswp_security_nonce},
                            success:function(response){    
                                console.log(response['status']);                             
                            },
                            error: function(response){                    
                                console.log(response);
                            }
                            });
          }
       });
        
        
        //google review js ends here
        
        
        
        
        
        //Adding settings button beside add schema type button on schema type list page       
        
        if ('saswp' == saswp_app_object.post_type && saswp_app_object.page_now == 'edit.php') {
        
         jQuery(jQuery(".wrap a")[0]).after("<a href='"+saswp_app_object.saswp_settings_url+"' id='' class='page-title-action'>Settings</a>");
         
        }
               
      
});
