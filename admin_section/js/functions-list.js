       var saswp_meta_list        = [];
       var saswp_meta_fields      = [];
       var saswp_meta_list_fields = []; 
       var saswp_taxonomy_term    = []; 
       var saswp_collection       = [];
       var saswp_total_collection = [];
       var saswp_total_reviews     = [];
       var saswp_coll_json        = null; 
       var saswp_grid_page        = 1; 
       
       function saswp_convert_datetostring(date_str){
           var date_time = {};

           if(date_str){
               
             var date_string = new Date(date_str); 
             var d = date_string.getDate();
             var m =  date_string.getMonth();
                 m += 1;  // JavaScript months are 0-11
             var y = date_string.getFullYear();

             var formated_date = date_string.toLocaleDateString();

             var date_format = jQuery(".saswp-collection-date-format").val();
             
            
             if(date_format && date_format == 'Y-m-d'){
                formated_date = y + "-" + m + "-" + d;
             }
             if(date_format && date_format == 'd-m-Y'){
                formated_date = d + "-" + m + "-" + y;
             }

             if(date_format && date_format == 'days'){
                formated_date = saswp_get_day_ago(date_string);
                
             }

               date_time = {
                   time : date_string.toLocaleTimeString(),
                   date : formated_date
               };
           }else{
              date_time = {
                   time : '',
                   date : ''
               };
           }
           
           return date_time;
           
       };

        function saswp_get_day_ago(date) {
            if (typeof date !== 'object') {
            date = new Date(date);
            }

            var seconds = Math.floor((new Date() - date) / 1000);
            var intervalType;

            var interval = Math.floor(seconds / 31536000);
            if (interval >= 1) {
            intervalType = 'year';
            } else {
            interval = Math.floor(seconds / 2592000);
            if (interval >= 1) {
                intervalType = 'month';
            } else {
                interval = Math.floor(seconds / 86400);
                if (interval >= 1) {
                intervalType = 'day';
                } else {
                interval = Math.floor(seconds / 3600);
                if (interval >= 1) {
                    intervalType = "hour";
                } else {
                    interval = Math.floor(seconds / 60);
                    if (interval >= 1) {
                    intervalType = "minute";
                    } else {
                    interval = seconds;
                    intervalType = "second";
                    }
                }
                }
            }
            }

            if (interval > 1 || interval === 0) {
            intervalType += 's';
            }      
            return interval + ' ' + intervalType+' ago';
        };
          
       
       function saswp_taxonomy_term_html(taxonomy, field_name){
           
            var html ='';
                html += '<td>';
                html += '<select name="saswp_taxonomy_term['+field_name+']">';
                jQuery.each(taxonomy, function(key, value){
                         html += '<option value="'+key+'">'+value+'</option>';
                }); 
                html += '</select>';   
                html += '</td>';              
                html += '<td><a class="button button-default saswp-rmv-modify_row">X</a></td>';
                          
                return html;
           
       }
       
       function saswp_enable_rating_review(){
           var schema_type = "";                      
           if(jQuery('select#schema_type option:selected').val()){
              schema_type = jQuery('select#schema_type option:selected').val();    
           }       
           if(jQuery(".saswp-tab-links.selected").attr('saswp-schema-type')){
              schema_type = jQuery(".saswp-tab-links.selected").attr('saswp-schema-type');    
           }
          
         if(schema_type){
             jQuery(".saswp-enable-rating-review-"+schema_type.toLowerCase()).change(function(){
                               
            if(jQuery(this).is(':checked')){
            jQuery(this).parent().parent().siblings('.saswp-rating-review-'+schema_type.toLowerCase()).show();            
             }else{
            jQuery(this).parent().parent().siblings('.saswp-rating-review-'+schema_type.toLowerCase()).hide(); 
             }          
             saswp_enable_rating_automate();   
            }).change();  

            jQuery("#saswp_schema_type_product_pros_enable_pros").change(function(){ 
                if(jQuery(this).is(':checked')){
                jQuery('.thepros_main_section_outer').show();
                // jQuery('.saswp-product_pros-section-main, .saswp-product_cons-section-main').show();
                }else{
                    jQuery('.thepros_main_section_outer').hide();
                    // jQuery('.saswp-product_pros-section-main, .saswp-product_cons-section-main').hide();
                }            
            }).change(); 
            
            // jQuery("#saswp_schema_type_product_pros_enable_cons").change(function(){ 
            //     if(jQuery(this).is(':checked')){
            //     jQuery('.saswp-product_cons-section-main').show();
            //     }else{
            //         jQuery('.saswp-product_cons-section-main').hide();
            //     }            
            // }).change(); 
         }
               
        }

        function saswp_enable_rating_automate(){
            var schema_type = "";                      
            if(jQuery('select#schema_type option:selected').val()){
               schema_type = jQuery('select#schema_type option:selected').val();    
            }       
            if(jQuery(".saswp-tab-links.selected").attr('saswp-schema-type')){
               schema_type = jQuery(".saswp-tab-links.selected").attr('saswp-schema-type');    
            }
           
          if(schema_type){
              jQuery(".saswp-enable-rating-automate-"+schema_type.toLowerCase()).change(function(){
                                
             if(jQuery(this).is(':checked') && jQuery(this).is(":visible")){
                jQuery(this).parent().parent().next().show();            
                jQuery(this).parent().parent().next().next().hide();
                jQuery(this).parent().parent().next().next().next().hide();
              }
              else{
                jQuery(this).parent().parent().next().hide();     
                
                if(jQuery(".saswp-enable-rating-review-"+schema_type.toLowerCase()).is(":checked")){
                    jQuery(this).parent().parent().next().next().show();
                    jQuery(this).parent().parent().next().next().next().show();
                }
                                
              }
          
             }).change();   
          }
                
         }
     
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
       
       function saswpCustomSelect2(){          
       if((saswp_localize_data.post_type == 'saswp' || saswp_localize_data.page_now =='saswp') && saswp_localize_data.page_now !='saswp_page_structured_data_options' && jQuery('.saswp-custom-fields-select2').length ){
           
           jQuery('.saswp-custom-fields-select2').select2({
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

       function saswp_reviews_datepicker(){

            if(jQuery(".saswp-reviews-datepicker-picker").length > 0){
                jQuery('.saswp-reviews-datepicker-picker').datepicker({
                    dateFormat: "yy-mm-dd"            
                 });
            }
                    
        }
        
       function saswp_schema_datepicker(){

            if(jQuery(".saswp-datepicker-picker").length > 0){
                jQuery('.saswp-datepicker-picker').datepicker({
                    dateFormat: "yy-mm-dd",             
                });
            }
                              
        }
        
       function saswp_schema_timepicker(){
            if(jQuery(".saswp-timepicker").length > 0){
                jQuery('.saswp-timepicker').timepicker({ 'timeFormat': 'H:i:s'});       
            }         
        }
        
        
       function saswp_item_reviewed_ajax(schema_type, current, manual = null){
                               
                    var item          = current.val();
                    var post_id       = saswp_localize_data.post_id;
                    var schema_id     = jQuery(current).attr('data-id');  
                    var post_specific = jQuery(current).attr('post-specific');                     
                    var modify_this   = jQuery(".saswp_modify_this_schema_hidden_"+schema_id).val();                     
                    
                    if(manual == null){
                        append_id     = jQuery("#saswp_specific_"+schema_id);
                    }else{
                        append_id     = jQuery(".saswp-manual-modification");
                    }
                    
                     jQuery.get(ajaxurl, 
                         { action:"saswp_get_item_reviewed_fields",modify_this:modify_this, schema_type:schema_type,schema_id:schema_id,  post_specific:post_specific ,item:item, post_id:post_id, saswp_security_nonce:saswp_localize_data.saswp_security_nonce},
                          function(response){    
                            
                            jQuery(append_id).find(".saswp-table-create-onajax").remove();   
                            var onload_class = jQuery(append_id).find(".saswp-table-create-onload");
                            
                            jQuery.each(onload_class, function(key, val){
                                if(key != 0){
                                    jQuery(this).remove();
                                }
                                
                            });
                            jQuery(append_id).append(response);
                            saswp_schema_datepicker();
                            saswp_schema_timepicker();

                         });
           
       } 
        
       function saswp_item_reviewed_call(){

        jQuery(".saswp-item-reviewed").change(function(e){
        e.preventDefault();
        var schema_type = ""; 

        if(jQuery('select#schema_type option:selected').val()){
           schema_type = jQuery('select#schema_type option:selected').val();    
        }       
        if(jQuery(".saswp-tab-links.selected").attr('saswp-schema-type')){
           schema_type = jQuery(".saswp-tab-links.selected").attr('saswp-schema-type');    
        }
        
        if(schema_type === 'Review' || schema_type === 'ReviewNewsArticle'){
            var current = jQuery(this);
            saswp_item_reviewed_ajax(schema_type, current);
                    
        }


    }).change();

    }
    
       function saswp_compatibliy_notes(current, id){
        
                var plugin_name =  id.replace('-checkbox','');
                var text = jQuery("#"+plugin_name).next('p').text(); 

                if (current.is(':checked') && text !=='') {              
                      jQuery("#"+plugin_name).next('p').removeClass('saswp_hide');                   
                }else{
                    if(jQuery("#"+plugin_name).next('p').attr('data-id') == 1){
                        jQuery("#"+plugin_name).next('p').text('This feature is only available in pro version');
                    }else{
                        jQuery("#"+plugin_name).next('p').addClass('saswp_hide');
                    }                                                        
                }        
        }

       function saswp_meta_list_html(current_fly, response, fields, f_name, id, tr){
                      
                        var field_name = f_name;
                        if(field_name == null){                            
                            field_name = Object.keys(fields)[0];                            
                        }                        
                        var re_html = '';   
                            re_html += '<select class="saswp-custom-meta-list" name="saswp_meta_list_val['+field_name+']">';
                          jQuery.each(response, function(key,value){ 

                               re_html += '<optgroup label="'+value['label']+'">';   

                               jQuery.each(value['meta-list'], function(key, value){
                                   re_html += '<option value="'+key+'">'+value+'</option>';
                               });                                                                                  
                               re_html += '</optgroup>';

                           });                                     
                           re_html += '</select>';
                                            
                      if(fields){ 
                                                    
                                var schema_type    = jQuery('select#schema_type option:selected').val();
                                var schema_subtype = '';

                                if(schema_type == 'Review' || schema_type == 'ReviewNewsArticle'){
                                    schema_subtype = jQuery('select.saswp-item-reivewed-list option:selected').val();
                                }
          
                                 var html = '<tr>';                                                                                                                            
                                     html += '<td>';                                     
                                     html += '<select class="saswp-custom-fields-name">';
                                     if(schema_type == 'ReviewNewsArticle'){
                                        html += '<optgroup label="ReviewNewsArticle">';
                                     }
                                     if(schema_type == 'Review'){
                                       html += '<optgroup label="Review">';
                                       html += '<option value="saswp_review_name">Review Name</option>';    
                                       html += '<option value="saswp_review_description">Review Description</option>';                                              
                                       html += '<option value="saswp_review_body">Review Body</option>';                                              
                                       html += '<option value="saswp_review_author">Review Author</option>';
                                       html += '<option value="saswp_review_author_url">Review Author Profile URL</option>';
                                       html += '<option value="saswp_review_publisher">Review Publisher</option>';
                                       html += '<option value="saswp_review_publisher_url">Review Publisher URL</option>';    
                                       html += '<option value="saswp_review_rating_value">Review Rating Value</option>';
                                       html += '<option value="saswp_review_date_published">Review Published Date</option>';
                                       html += '<option value="saswp_review_date_modified">Review Modified Date</option>';
                                       html += '<option value="saswp_review_url">Review URL</option>';
                                       html += '</optgroup>'; 
                                      
                                     }
                                     
                                     if(schema_type == 'Review'){
                                       html += '<optgroup label="'+schema_subtype+'">';   
                                     }
                                     
                                     jQuery.each(fields, function(key,value){                                         
                                       html += '<option value="'+key+'">'+value+'</option>';                                       
                                     });
                                     
                                     if(schema_type == 'Review' || schema_type == 'ReviewNewsArticle'){
                                         html += '</optgroup>'; 
                                     }

                                     let reviewSubSchema = '';
                                     if(schema_type == 'ReviewNewsArticle'){
                                        let post_id = jQuery('#post_ID').val();
                                        jQuery.ajax({
                                            type: "POST",    
                                            url:ajaxurl,                    
                                            dataType: "json",
                                            async: false,
                                            data:{action:"saswp_get_schema_type_fields",post_id:post_id, schema_type:'Review',schema_subtype:schema_subtype, saswp_security_nonce:saswp_localize_data.saswp_security_nonce},
                                            success:function(schemaResponse){  
                                                html += '<optgroup label="'+schema_subtype+'">';
                                                if(schemaResponse){
                                                    jQuery.each(schemaResponse, function(resKey,resValue){                                        
                                                       html += '<option value="'+resKey+'">'+resValue+'</option>';                                      
                                                     });     
                                                } 
                                                reviewSubSchema += '</optgroup>';                                                                                                          
                                            },
                                            error: function(schemaResponse){                    
                                            console.log(schemaResponse);
                                            }
                                        });
                                     }
            
                                    html += '</select>';                                     
                                    html += '</td>';                                                                                                                                                                                                       
                                    html += '<td>';                                                                       
                                    html += re_html;
                                    html += '</td>';  
                                    html += '<td></td><td><a class="button button-default saswp-rmv-modify_row">X</a></td>';
                                    html += '</tr>';
                                    jQuery(".saswp-custom-fields-table").append(html); 
                                    if(current_fly != null){
                                        current_fly.removeClass('updating-message');
                                    }
                                    
                                                                                                                     
                      }else{
                          jQuery(id).html(re_html);     
                          if(current_fly != null){
                                        current_fly.removeClass('updating-message');
                          }
                      }                                                          
           
       } 

       function saswp_get_meta_list(current_fly, type, fields, id, fields_name, tr){                           
                            if (!saswp_meta_list[type]) {
                                
                                jQuery.get(ajaxurl, 
                                    { action:"saswp_get_meta_list", saswp_security_nonce:saswp_localize_data.saswp_security_nonce},
                                     function(response){                                  
                                               saswp_meta_list[type] = response[type];                                                                                                                             
                                               saswp_meta_list_html(current_fly, saswp_meta_list[type], fields, fields_name, id, tr);
                                          
                                    },'json');
                                
                            }else{
                                saswp_meta_list_html(current_fly, saswp_meta_list[type], fields, fields_name, id, tr);
                            }
                                                                                     
       }
             
       function saswp_get_post_specific_schema_fields(current_fly, index, meta_name, div_type, schema_id, fields_type, schema_type){
                            
                            if (!saswp_meta_fields[fields_type]) {
                                
                                jQuery.get(ajaxurl, 
                                    { action:"saswp_get_schema_dynamic_fields_ajax",schema_id:schema_id,schema_type:schema_type, meta_name:meta_name, saswp_security_nonce:saswp_localize_data.saswp_security_nonce},
                                     function(response){                                  
                                         saswp_meta_fields[fields_type] = response;                                         
                                         var html = saswp_fields_html_generator(index, schema_id, fields_type, div_type, response);
                                         if(meta_name == 'faq_repeater_question'){
                                            html = saswp_acf_repeaters_html_generator(index, schema_id, fields_type, div_type, response);
                                         }

                                           if(html){
                                               jQuery('.saswp-'+div_type+'-section[data-id="'+schema_id+'"]').append(html);
                                               saswp_schema_datepicker();
                                               saswp_schema_timepicker();
                                               current_fly.removeClass('updating-message');
                                           }

                                    },'json');
                                
                            }else{
                                
                              var html = saswp_fields_html_generator(index, schema_id, fields_type, div_type, saswp_meta_fields[fields_type]);
                              if(meta_name == 'faq_repeater_question'){
                                    html = saswp_acf_repeaters_html_generator(index, schema_id, fields_type, div_type, saswp_meta_fields[fields_type]);
                              }
                               if(html){
                                   jQuery('.saswp-'+div_type+'-section[data-id="'+schema_id+'"]').append(html);
                                   saswp_schema_datepicker();
                                   saswp_schema_timepicker();
                                   current_fly.removeClass('updating-message');
                               }
                                                                
                            }
                            
                             
       }
       
       function saswp_fields_html_generator(index, schema_id, fields_type, div_type, schema_fields){
          
            $newclosebtn = '';
            otherRepeatorClose = '';
            $reviewtitle = index+1
            if(fields_type == 'product_pros_' || fields_type == 'product_cons_'){
                $newclosebtn = '<td class="saswp-table-close-new-td"><a class="saswp-table-close-new">X</a></td>';
               
            }else{
                otherRepeatorClose = '<a class="saswp-table-close">X</a>';
            }
            $addRevewTitle = '';
            if(fields_type == 'product_reviews_'){
                $addRevewTitle = '<h3 style="float: left;">Review '+$reviewtitle+'</h3>';
            }

            var html = '';
            
            html += '<div class="saswp-'+div_type+'-table-div saswp-dynamic-properties" data-id="'+index+'">'
                        + $addRevewTitle
                        +  otherRepeatorClose
                        + '<table class="form-table saswp-'+div_type+'-table">' 
                
            jQuery.each(schema_fields, function(eachindex, element){
                                
                var meta_class = "";
                
                if(element.name.indexOf('published_date') > -1 || element.name.indexOf('date_created') > -1 || element.name.indexOf('created_date') > -1 || element.name.indexOf('modified_date') > -1 || element.name.indexOf('date_published') > -1 || element.name.indexOf('date_modified') > -1){
                    meta_class = "saswp-datepicker-picker";   
                }
                
                switch(element.type) {
                    
                    case "number":
                    case "text":
                    case "date":
                      
                        html += '<tr>'
                        + '<th>'+element.label+'</th><td><input class="'+meta_class+'" style="width:100%" type="'+element.type+'" id="'+element.name+'_'+index+'_'+schema_id+'" name="'+fields_type+schema_id+'['+index+']['+element.name+']"></td>'                        
                        +$newclosebtn
                        + '</tr>';                        
                      
                      break;
                      
                    case "textarea":
                      
                        html += '<tr>'
                        + '<th>'+element.label+'</th><td><textarea style="width: 100%" id="'+element.name+'_'+index+'_'+schema_id+'" name="'+fields_type+schema_id+'['+index+']['+element.name+']" rows="5"></textarea></td>'
                        + '</tr>';                        
                      
                      break;
                     case "select":
                        
                        var options_html = "";                        
                        jQuery.each(element.options, function(opt_index, opt_element){                            
                            options_html += '<option value="'+opt_index+'">'+opt_element+'</option>';
                        });
                        
                         html += '<tr>'
                        + '<th>'+element.label+'</th>'
                        + '<td>'
                        
                        + '<select id="'+element.name+'_'+index+'_'+schema_id+'" name="'+fields_type+schema_id+'['+index+']['+element.name+']">'
                        + options_html
                        + '</select>'
                        
                        + '</td>'
                        + '</tr>';
                         
                     break;
                      
                    case "media":
                        
                        html += '<tr>'
                        + '<th>'+element.label+'</th>'
                        + '<td>'
                        + '<fieldset>'
                        + '<input style="width:80%" type="text" id="'+element.name+'_'+index+'_'+schema_id+'" name="'+element.name+'_'+index+'_'+schema_id+'">'
                        + '<input type="hidden" data-id="'+element.name+'_'+index+'_'+schema_id+'_id" name="'+fields_type+schema_id+'['+index+']['+element.name+'_id]" id="'+element.name+'_'+index+'_'+schema_id+'_id">'
                        + '<input data-id="media" style="width: 19%" class="button" id="'+element.name+'_'+index+'_'+schema_id+'_button" name="'+element.name+'_'+index+'_'+schema_id+'_button" type="button" value="Upload">'
                        + '<div class="saswp_image_div_'+element.name+'_'+index+'_'+schema_id+'">'                                                
                        + '</div>'
                        + '</fieldset>'
                        + '</td>'
                        + '</tr>';
                      
                      break;
                    default:
                      // code block
                  }
                                                                                            
            });                                                             
            html += '</table>'
                    + '</div>';
                     
            return html;
            
        }
        
       function saswp_create_total_collection( s_rating_enable, s_rating_val ){
           
           var platform_list = '';
           
           saswp_total_collection = []; 
                      
           for(var key in saswp_collection){
                                             
               if(saswp_collection[key]){
                   
                   jQuery.each(saswp_collection[key], function(index, value){      
                    
                        if(rmv_boolean){
                            value.is_remove = true; 
                        }else{
                            value.is_remove = false; 
                        }
                    
                        if(s_rating_enable){

                            if( value.saswp_review_rating >= s_rating_val ){
                                saswp_total_collection.push(value);
                            }

                        }else{
                            saswp_total_collection.push(value);
                        }                                                
                        
                    });
                                                         
                   platform_list += saswp_function_added_platform(key, saswp_collection[key].length );
               }
               
           }                
                jQuery(".saswp-platform-added-list").html('');                
                jQuery(".saswp-platform-added-list").append(platform_list);   
                                 
       } 
        
       function saswp_create_rating_html_by_value(rating_val,color,review_id){
               

                var starating = '';
        
                    starating += '<div class="saswp-rvw-str">';
                       starating += '<div class="saswp-rvw-str">';
                  
                    for(var j=0; j<5; j++){  
                       
                          if(rating_val >j){

                                var explod = rating_val.split('.');

                                if(explod[1]){

                                    if(j < explod[0]){
                                        var a = (Math.floor(Math.random() * 10000) + 10000).toString().substring(1);
                                        starating +='<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="18px" viewBox="0 0 32 32"><defs><linearGradient id="grad'+review_id+a+'"><stop offset="100%" class="saswp_star" stop-color='+color+' /><stop offset="100%" stop-color="grey"/></linearGradient></defs><path fill="url(#grad'+review_id+a+')" d="M20.388,10.918L32,12.118l-8.735,7.749L25.914,31.4l-9.893-6.088L6.127,31.4l2.695-11.533L0,12.118 l11.547-1.2L16.026,0.6L20.388,10.918z"/></svg>';

                                    }else{
                                        var b = (Math.floor(Math.random() * 10000) + 10000).toString().substring(1);
                                      starating +='<span class="saswp_half_star_color"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="18px" viewBox="0 0 32 32"><defs><linearGradient id="grad'+review_id+b+'"><stop offset="50%" class="saswp_star" stop-color='+color+' /><stop offset="50%" stop-color="grey"/></linearGradient></defs><path fill="url(#grad'+review_id+b+')" d="M20.388,10.918L32,12.118l-8.735,7.749L25.914,31.4l-9.893-6.088L6.127,31.4l2.695-11.533L0,12.118 l11.547-1.2L16.026,0.6L20.388,10.918z"/></svg></span>';
                                      
                                    }                                           
                                }else{
                                    var c = (Math.floor(Math.random() * 10000) + 10000).toString().substring(1);
                                    starating +='<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="18px" viewBox="0 0 32 32"><defs><linearGradient id="grad'+review_id+c+'"><stop offset="100%" class="saswp_star" stop-color='+color+' /><stop offset="100%" stop-color="grey"/></linearGradient></defs><path fill="url(#grad'+review_id+c+')" d="M20.388,10.918L32,12.118l-8.735,7.749L25.914,31.4l-9.893-6.088L6.127,31.4l2.695-11.533L0,12.118 l11.547-1.2L16.026,0.6L20.388,10.918z"/></svg>';
                                }

                          } else{  
                               var d = (Math.floor(Math.random() * 10000) + 10000).toString().substring(1);
                               starating +='<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="18px" viewBox="0 0 32 32"><defs><linearGradient id="grad1'+review_id+d+'"><stop offset="100%" stop-color="grey" /><stop offset="100%" stop-color="grey"/></linearGradient></defs><path fill="url(#grad1'+review_id+d+')" d="M20.388,10.918L32,12.118l-8.735,7.749L25.914,31.4l-9.893-6.088L6.127,31.4l2.695-11.533L0,12.118 l11.547-1.2L16.026,0.6L20.388,10.918z"/></svg>';
                              
                          }                                                                                                                                
                        }

                        starating += '</div>';
                    starating += '</div>';

                    return starating;
                
                
            }   
       
       function saswpChunkArray(myArray, chunk_size){
                
                    var contentArray = JSON.parse(JSON.stringify(myArray));
                    var results = [];
                    while (contentArray.length) {
                        results.push(contentArray.splice(0, chunk_size));
                    }

                    return results;
            }
       
       function saswp_function_added_platform(key, rvcount){
           
            var platform_list = '';
            
            if(rvcount > 0){
                
                platform_list += '<div class="cancel-btn">';
                platform_list += '<span>'+jQuery("#saswp-plaftorm-list option[value="+key+"]").text()+'</span><span>('+rvcount+')</span>';
                platform_list += '<input type="hidden" name="saswp_platform_ids['+key+']" value="'+rvcount+'">';
                platform_list += '<a platform-id="'+key+'" class="button button-default saswp-remove-platform"></a>';
                platform_list += '</div>';
            }
                        
            return platform_list;
                                   
       }
       
       function saswpCollectionSlider(){
	
                jQuery(".saswp-cs").each( function(){
		
		var $slider = jQuery(this),
				$itemscontainer = $slider.find(".saswp-sic");
		
		if ($itemscontainer.find(".saswp-si.saswp-active").length == 0){
			$itemscontainer.find(".saswp-si").first().addClass("saswp-active");
		}
		
		function setWidth(){
			var totalWidth = 0
			
			jQuery($itemscontainer).find(".saswp-si").each( function(){
				totalWidth += jQuery(this).outerWidth();
			});
			
			$itemscontainer.width(totalWidth);
			
		}
		function setTransform(){
			
                        if(jQuery(".saswp-si.saswp-active").length > 0){
                        
                            var $activeItem = $itemscontainer.find(".saswp-si.saswp-active"),
                                            activeItemOffset = $activeItem.offset().left,
                                            itemsContainerOffset = $itemscontainer.offset().left,
                                            totalOffset = activeItemOffset - itemsContainerOffset;

                            $itemscontainer.css({"transform": "translate( -"+totalOffset+"px, 0px)"})
                            
                        }
                        						
		}
		function nextSlide(){
			var activeItem = $itemscontainer.find(".saswp-si.saswp-active"),
					activeItemIndex = activeItem.index(),
					sliderItemTotal = $itemscontainer.find(".saswp-si").length,
					nextSlide = 0;
			
			if (activeItemIndex + 1 > sliderItemTotal - 1){
				nextSlide = 0;
			}else{
				nextSlide = activeItemIndex + 1
			}
			
			var nextSlideSelect = $itemscontainer.find(".saswp-si").eq(nextSlide),
					itemContainerOffset = $itemscontainer.offset().left,
					totalOffset = nextSlideSelect.offset().left - itemContainerOffset
			
			$itemscontainer.find(".saswp-si.saswp-active").removeClass("saswp-active");
			nextSlideSelect.addClass("saswp-active");
			$slider.find(".saswp-slider-dots").find(".saswp-dot").removeClass("saswp-active")
			$slider.find(".saswp-slider-dots").find(".saswp-dot").eq(nextSlide).addClass("saswp-active");
			$itemscontainer.css({"transform": "translate( -"+totalOffset+"px, 0px)"})
			
		}
		function prevSlide(){
			var activeItem = $itemscontainer.find(".saswp-si.saswp-active"),
					activeItemIndex = activeItem.index(),
					sliderItemTotal = $itemscontainer.find(".saswp-si").length,
					nextSlide = 0;
			
			if (activeItemIndex - 1 < 0){
				nextSlide = sliderItemTotal - 1;
			}else{
				nextSlide = activeItemIndex - 1;
			}
			
			var nextSlideSelect = $itemscontainer.find(".saswp-si").eq(nextSlide),
					itemContainerOffset = $itemscontainer.offset().left,
					totalOffset = nextSlideSelect.offset().left - itemContainerOffset
			
			$itemscontainer.find(".saswp-si.saswp-active").removeClass("saswp-active");
			nextSlideSelect.addClass("saswp-active");
			$slider.find(".saswp-slider-dots").find(".saswp-dot").removeClass("saswp-active")
			$slider.find(".saswp-slider-dots").find(".saswp-dot").eq(nextSlide).addClass("saswp-active");
			$itemscontainer.css({"transform": "translate( -"+totalOffset+"px, 0px)"})
			
		}
		function makeDots(){
			var activeItem = $itemscontainer.find(".saswp-si.saswp-active"),
					activeItemIndex = activeItem.index(),
					sliderItemTotal = $itemscontainer.find(".saswp-si").length;
			
			for (i = 0; i < sliderItemTotal; i++){
				$slider.find(".saswp-slider-dots").append("<div class='saswp-dot'></div>")
			}
			
			$slider.find(".saswp-slider-dots").find(".saswp-dot").eq(activeItemIndex).addClass("saswp-active")
			
		}

        function saswpGallerySlider(){
            let collectionDesign = jQuery('.saswp-collection-desing').val();
            if(collectionDesign == 'gallery'){
                if(jQuery('#saswp_gallery_slide_auto').is(':checked')){
                    jQuery('.saswp-collection-interval-wrapper').removeClass('saswp_hide');
                    saswpAutoSlide();
                }else{
                    jQuery('.saswp-collection-interval-wrapper').addClass('saswp_hide');
                }
            }else{
                jQuery('.saswp-collection-interval-wrapper').addClass('saswp_hide');
            }

            jQuery('#saswp_gallery_slide_auto, .saswp-collection-desing').on('change', function(e){
                let collectionDesign = jQuery('.saswp-collection-desing').val();
                if(collectionDesign == 'gallery'){
                    if(jQuery('#saswp_gallery_slide_auto').is(':checked')){
                        jQuery('.saswp-collection-interval-wrapper').removeClass('saswp_hide');
                        saswpAutoSlide();
                    }else{
                        jQuery('.saswp-collection-interval-wrapper').addClass('saswp_hide');
                    }
                }else{
                    jQuery('.saswp-collection-interval-wrapper').addClass('saswp_hide');
                }
            });
        }

        function saswpAutoSlide(){
            let sliderInterval = jQuery('#saswp_collection_gallery_interval').val();
            if(typeof sliderInterval != 'undefined'){
                setInterval(nextSlide, sliderInterval);
            }
        }


		
		setWidth();
		setTransform();
		makeDots();
                
                jQuery(document).ready( function(){
					setWidth();
					setTransform();
                    saswpGallerySlider();
		});
		
		jQuery(window).resize( function(){
					setWidth();
					setTransform();
		});
		
		var nextBtn = $slider.find(".saswp-slider-controls").find(".saswp-slider-next-btn"),
				prevBtn = $slider.find(".saswp-slider-controls").find(".saswp-slider-prev-btn");
		
		nextBtn.on('click', function(e){
			e.preventDefault();
			nextSlide();
		});
		
		prevBtn.on('click', function(e){
			e.preventDefault();
			prevSlide();
		});
		
		$slider.find(".saswp-slider-dots").find(".saswp-dot").on('click', function(e){
			
			var dotIndex = jQuery(this).index(),
			totalOffset = $itemscontainer.find(".saswp-si").eq(dotIndex).offset().left - $itemscontainer.offset().left;
					
			$itemscontainer.find(".saswp-si.saswp-active").removeClass("saswp-active");
			$itemscontainer.find(".saswp-si").eq(dotIndex).addClass("saswp-active");
			$slider.find(".saswp-slider-dots").find(".saswp-dot").removeClass("saswp-active");
			jQuery(this).addClass("saswp-active");
			
			$itemscontainer.css({"transform": "translate( -"+totalOffset+"px, 0px)"})
			
		});
		
	});
	
               }     
            
       function saswp_review_desing_for_slider(value, saswp_collection_gallery_img_hide,color,collectionImg=null,slider=null){
       
                            var date_str = saswp_convert_datetostring(value.saswp_review_date); 
                            if(value.saswp_is_date_in_days != '' && value.saswp_is_date_in_days == 'days'){
                                date_str.date = value.saswp_review_date;
                            }
                            var html = '';
           
                                html += '<div class="saswp-r2-sli">';
                                html += '<div class="saswp-r2-b">';                                
                                html += '<div class="saswp-r2-q">';
                                html += '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" width="95.333px" height="95.332px" viewBox="0 0 95.333 95.332" style="enable-background:new 0 0 95.333 95.332;" xml:space="preserve"><path d="M30.512,43.939c-2.348-0.676-4.696-1.019-6.98-1.019c-3.527,0-6.47,0.806-8.752,1.793    c2.2-8.054,7.485-21.951,18.013-23.516c0.975-0.145,1.774-0.85,2.04-1.799l2.301-8.23c0.194-0.696,0.079-1.441-0.318-2.045    s-1.035-1.007-1.75-1.105c-0.777-0.106-1.569-0.16-2.354-0.16c-12.637,0-25.152,13.19-30.433,32.076    c-3.1,11.08-4.009,27.738,3.627,38.223c4.273,5.867,10.507,9,18.529,9.313c0.033,0.001,0.065,0.002,0.098,0.002    c9.898,0,18.675-6.666,21.345-16.209c1.595-5.705,0.874-11.688-2.032-16.851C40.971,49.307,36.236,45.586,30.512,43.939z"></path><path d="M92.471,54.413c-2.875-5.106-7.61-8.827-13.334-10.474c-2.348-0.676-4.696-1.019-6.979-1.019    c-3.527,0-6.471,0.806-8.753,1.793c2.2-8.054,7.485-21.951,18.014-23.516c0.975-0.145,1.773-0.85,2.04-1.799l2.301-8.23    c0.194-0.696,0.079-1.441-0.318-2.045c-0.396-0.604-1.034-1.007-1.75-1.105c-0.776-0.106-1.568-0.16-2.354-0.16    c-12.637,0-25.152,13.19-30.434,32.076c-3.099,11.08-4.008,27.738,3.629,38.225c4.272,5.866,10.507,9,18.528,9.312    c0.033,0.001,0.065,0.002,0.099,0.002c9.897,0,18.675-6.666,21.345-16.209C96.098,65.559,95.376,59.575,92.471,54.413z"></path></svg>';
                                html += '</div>';
                                if(jQuery('#saswp-collection-gallery-readmore-desc').is(':checked')){
                                    if(slider == 'slider'){
                                        html += '<div class="saswp-rc-cnt" style="height: 80px;">';
                                    }else{
                                        html += '<div class="saswp-rc-cnt" style="height: 120px;">';
                                    }
                                }else{
                                    html += '<div class="saswp-rc-cnt">';
                                }
                                html += '<p>';
                                let reviewText = value.saswp_review_text;
                                if(jQuery('#saswp-collection-gallery-readmore-desc').is(':checked')){
                                    if(slider == 'slider'){
                                        reviewText = saswpAddReadmoreToReviewext(reviewText, 40);
                                    }else{
                                        reviewText = saswpAddReadmoreToReviewext(reviewText, 20);
                                    }
                                }
                                html += reviewText;
                                html += '</p>';
                                html += '</div>';
                                html += '<div class="saswp-r2-strs">';
                                html += '<span class="saswp-r2-s">';
                                html += saswp_create_rating_html_by_value(value.saswp_review_rating,color,value.saswp_review_id);
                                html += '</span>';
                                html += '</div>';
                                html += '</div>';
                                html += '<div class="saswp-rc">';
                                html += '<div class="saswp-rc-a">';
                                if(saswp_collection_gallery_img_hide !=1){
                                    let isDefaultImg = 0;
                                    let revCollImg = value.saswp_reviewer_image;
                                    if(revCollImg.length > 20){
                                        let isDefault = revCollImg.includes('default_user');
                                        if(!isDefault){
                                            isDefaultImg = 1;
                                        }
                                    }
                                    if(isDefaultImg == 0){
                                        revCollImg = collectionImg; 
                                    }

                                    html += '<img src="'+revCollImg+'" data-is-default-img="'+isDefaultImg+'"/>';
                                }
                                html += '<div class="saswp-rc-nm">';
                                html += '<a href="#">'+value.saswp_reviewer_name+'</a>';
                                html += '<span class="saswp-rc-dt">'+date_str.date+'</span>';
                                html += '</div>';
                                html += '<div class="saswp-rc-lg">';
                                html += '<img src="'+value.saswp_review_platform_icon+'"/>';
                                html += '</div>';
                                html += '</div>';
                                html += '</div>';
                                html += '</div>';
                                
                                return html;
                      
       }    
       
       function saswp_create_collection_slider(slider, arrow, dots, saswp_collection_gallery_img_hide,color,collectionImg=null){
                                
                var html = '';                               
                if(saswp_total_collection.length > 0){
                    
                    if(slider == 'slider'){
                      html += '<div class="saswp-cst">';
                    }else{
                      html += '<div class="saswp-cct">';
                    }
                    
                    html += '<div class="saswp-cs">';
                    html += '<div class="saswp-sic">';
                              
                         if(slider == 'slider'){
                            
                            jQuery.each(saswp_total_collection, function(index, value){
                                                        
                                html += '<div class="saswp-si">';
                                
                                html += saswp_review_desing_for_slider(value, saswp_collection_gallery_img_hide,color,collectionImg,slider);
                                
                                html += '</div>';
                            
                          });
                            
                         }   
                         
                    if(slider == 'carousel'){
                             
                            var chunkarr = saswpChunkArray(saswp_total_collection, 3);
                            
                            if(chunkarr){
                                                                                                                
                            jQuery.each(chunkarr, function(p_index, p_value){
                                             
                                html += '<div class="saswp-si">';
                                                                    
                                jQuery.each(p_value, function(index, value){
                                   
                                    html += saswp_review_desing_for_slider(value, saswp_collection_gallery_img_hide,color,collectionImg,slider);
                                                                                               
                                });
                                
                                html += '</div>';   
                                
                            });
                                
                            }
                                                       
                          }                                                                                     
                    
                    html += '</div>';
                    
                                        
                    if(arrow){
                        html += '<div class="saswp-slider-controls">';    
                        html += '<a href="#" class="saswp-slider-prev-btn"></a>';
                        html += '<a href="#" class="saswp-slider-next-btn"></a>';
                        html += '</div>';
                    }
                    
                    if(dots){
                    
                    html += '<div class="saswp-slider-dots">';
                    html += '</div>';
                        
                    }
                    
                    html += '</div>';
                    html += '</div>';
                                         
                                        
                }
                
                     jQuery(".saswp-collection-preview").html('');                    
                     jQuery(".saswp-collection-preview").append(html);                                                                                 
                     saswpCollectionSlider();
                                                                
            }
            
       function saswp_create_collection_badge(color){
                
                var html = '';                
                                
                if(saswp_total_collection.length > 0){

                  
                    
                    html += '<div class="saswp-rd3-warp">';
                    html += '<ul>';
                                        
                    for (var key in saswp_collection) {
                          
                        var platform_icon  = '';
                        var platform_name  = '';
                        var review_count   = 0;                        
                        var sum_of_rating  = 0;
                        var average_rating = 1;
                        var source_url     = '';
                      
                        jQuery.each(saswp_collection[key], function(index, value){
                          
                            if(value.saswp_review_location_id){
                                source_url    = value.saswp_review_location_id;
                            }else{
                                source_url    = value.saswp_review_link;
                            }
                            platform_icon = value.saswp_review_platform_icon;
                            platform_name = value.saswp_review_platform_name;
                            review_id = value.saswp_review_id
                           
                            if(platform_name == 'Self'){
                                platform_name = saswp_localize_data.trans_self;
                            }
                            if(platform_name == 'ProductReview' && value.saswp_review_location_id != ''){
                                source_url    = 'https://www.productreview.com.au/listings/'+value.saswp_review_location_id;
                            }

                            sum_of_rating += parseFloat(value.saswp_review_rating);
                            review_count++;
                        });  
                        if(sum_of_rating > 0){
                        
                            average_rating = sum_of_rating / review_count;
                            
                        }
                        
                        if(saswp_collection[key]){
                            
                            html += '<li>';
                      if(!jQuery('#saswp-collection-badge-souce-link').is(':not(:checked)')){                       
                        html += '<a target="_blank" href="'+source_url+'">'; 
                      }

                        html += '<div class="saswp-r3-lg">';
                          html += '<span>';
                           html += '<img src="'+platform_icon+'"/>';
                          html += '</span>';
                          html += '<span class="saswp-r3-tlt">'+platform_name+'</span">';                          
                        html += '</div>';

                      html += '<div class="saswp-r3-rtng">';

                        html += '<div class="saswp-r3-rtxt">';
                          html += '<span class="saswp-r3-num">';
                            html += average_rating.toFixed(1);
                          html += '</span>';
                          html += '<span class="saswp-stars saswp-badge">';
                           html += saswp_create_rating_html_by_value(average_rating.toString(),color,review_id); 
                          html += '</span>';
                        html += '</div>';

                        html += '<span class="saswp-r3-brv">';
                        html += saswp_localize_data.trans_based_on +' '+ review_count+' '+saswp_localize_data.trans_reviews;
                        html += '</span>';

                      html += '</div>';
                      html += '</a>';
                      html += '</li>';                            
                                                      
                            
                        }
                                                                                                
                    }
                    
                    html += '</ul>';
                    html += '</div>';
                                         
                }
                
                 jQuery(".saswp-collection-preview").html('');                    
                 jQuery(".saswp-collection-preview").append(html); 
                                                                  
            }
            
       function saswp_create_collection_popup(color){
           
                var html          = '';                
                var html_list     = '';
                
                if(saswp_total_collection.length > 0){
                        
                        var review_count   = 0;                        
                        var sum_of_rating  = 0;
                        var average_rating = 1;
                            
                        jQuery.each(saswp_total_collection, function(index, value){
                            
                            platform_icon = value.saswp_review_platform_icon;
                            review_id = value.saswp_review_id;
                            sum_of_rating += parseFloat(value.saswp_review_rating);
                            review_count++;
                            
                            var date_str = saswp_convert_datetostring(value.saswp_review_date); 
                            if(value.saswp_is_date_in_days != '' && value.saswp_is_date_in_days == 'days'){
                                date_str.date = value.saswp_review_date;
                            }
                            html_list += '<li>';
                            html_list += '<div class="saswp-r4-b">';
                            html_list += '<span class="saswp-r4-str saswp-popup">';
                            html_list += saswp_create_rating_html_by_value(value.saswp_review_rating,color,value.saswp_review_id);
                            html_list += '</span>';
                            html_list += '<span class="saswp-r4-tx">'+date_str.date+'</span>';
                            html_list += '</div>';
                            
                            html_list += '<div class="saswp-r4-cnt">';
                            html_list += '<h3>'+value.saswp_reviewer_name+'</h3>';
                            html_list += '<p>'+value.saswp_review_text+'</p>';
                            html_list += '</div>';
                            
                            html_list += '</li>';
                                                                                  
                        });
                       
                        if(sum_of_rating > 0){
                        
                            average_rating = sum_of_rating / review_count;
                            
                        }                                                                                                                
                    
                    if(review_count > 0){
                        
                        html += '<div id="saswp-sticky-review">';
                        html += '<div class="saswp-open-class saswp-popup-btn">';
                        html += '<div class="saswp-opn-cls-btn">';

                        html += '<div class="saswp-onclick-hide">';
                        html += '<span class="saswp-popup2">';
                        html += saswp_create_rating_html_by_value(average_rating.toString(),color,review_id);
                        html += '</span>';
                        html += '<span class="saswp-r4-rnm">'+average_rating.toFixed(1)+' from '+review_count+' reviews</span>';                    
                        html += '</div>';

                        html += '<div class="saswp-onclick-show" style="display: none;">';
                        html += '<span>Ratings and reviews</span>';                    
                        html += '<span class="saswp-mines"></span>';                    
                        html += '</div>';

                        html += '</div>';
                        html += '<div id="saswp-reviews-cntn">';
                        html += '<div class="saswp-r4-info">';
                        html += '<ul>';

                        html += '<li class="saswp-r4-r">';
                        html += '<span>';
                        html += saswp_create_rating_html_by_value(average_rating.toString(),color,review_id);
                        html += '</span>';
                        html += '<span class="saswp-r4-rnm">'+average_rating.toFixed(1)+' from '+review_count+' reviews</span>';                    
                        html += '</li>';                                        
                        html += html_list;
                        html += '</ul>';                    
                        html += '</div>';
                        html += '</div>';
                        html += '</div>';
                        html += '</div>';
                                                
                    }
                                           
                }
                
                    jQuery(".saswp-collection-preview").html('');                    
                    jQuery(".saswp-collection-preview").append(html); 
                
            }            
            
       function saswp_create_collection_fomo(fomo_inverval, fomo_visibility,color){
                
                var html = '';                
                                                                                                            
                if(saswp_total_collection.length > 0){

                 jQuery.each(saswp_total_collection, function(index, value){

                    var date_str = saswp_convert_datetostring(value.saswp_review_date); 
                    if(value.saswp_is_date_in_days != '' && value.saswp_is_date_in_days == 'days'){
                        date_str.date = value.saswp_review_date;
                    }
                    html += '<div id="'+index+'" class="saswp-r5">';
                    html += '<div class="saswp-r5-r">';                            
                    html += '<div class="saswp-r5-lg">';
                    html += '<span>';
                    html += '<img height="70" width="70" src="'+value.saswp_review_platform_icon+'"/>';
                    html += '</span>';
                    html += '</div>';                            
                    html += '<div class="saswp-r5-rng">';
                      html += saswp_create_rating_html_by_value(value.saswp_review_rating,color,value.saswp_review_id);
                     html +='<div class="saswp-r5-txrng">';
                     html +='<span>'+value.saswp_review_rating+' Stars</span>';
                     html +='<span>by</span>';
                     html +='<span>'+value.saswp_reviewer_name+'</span>';
                     html +='</div>';
                     html += '<span class="saswp-r5-dt">'+date_str.date+'</span>';
                    html += '</div>';                            
                    html += '</div>';
                    html += '</div>';                   

                });
			                                                                              
                }
                
                 jQuery(".saswp-collection-preview").html('');                    
                 jQuery(".saswp-collection-preview").append(html);
                 
                 saswp_fomo_slide(fomo_inverval, fomo_visibility);
                
            }        
            
       function saswp_fomo_slide(fomo_inverval, fomo_visibility){
                
            var elem = jQuery('.saswp-collection-preview .saswp-r5');
            var l = elem.length;
            var i = 0;
                        
            function saswp_fomo_loop() {
                
                elem.eq(i % l).fadeIn(6000, function() {
                    elem.eq(i % l).fadeOut(3000, saswp_fomo_loop);
                    i++;
                });
            }

            saswp_fomo_loop();
            
            } 
                        
       function saswp_collection_sorting(sorting_type){
             
           if(saswp_total_collection.length > 0){
               
               switch(sorting_type){
                    
                case 'lowest':
                        
                        saswp_total_collection.sort(function(a, b) {
                          return a.saswp_review_rating - b.saswp_review_rating;
                        });   
                                                
                    break;
                    
                case 'highest':
                            
                        saswp_total_collection.sort(function(a, b) {
                          return a.saswp_review_rating - b.saswp_review_rating;
                        });   
                        saswp_total_collection.reverse();
                        break;
                        
               case 'newest':
               case 'recent':
                            
                        saswp_total_collection.sort(function(a, b) {
                          var dateA = new Date(a.saswp_review_date), dateB = new Date(b.saswp_review_date);  
                          return dateA - dateB;
                        });   
                        saswp_total_collection.reverse();                                   
                                                                                          
                    break;
                    
               case 'oldest':
                   
                        saswp_total_collection.sort(function(a, b) {
                          var dateA = new Date(a.saswp_review_date), dateB = new Date(b.saswp_review_date);  
                          return dateA - dateB;
                        });   
                                                                                                                                
                    break; 
                
                case 'random':
                            
                        saswp_total_collection.sort(function(a, b) {
                          return 0.5 - Math.random();
                        });   
                                                                                          
                    break;
                    
                }
               
           }
                      
       }
       
       function saswp_collection_total_reviews_id(){
           
           if(saswp_total_collection.length > 0){
               
               saswp_total_reviews    = [];
               
               jQuery.each(saswp_total_collection, function(index, value){
                   
                   saswp_total_reviews.push(value.saswp_review_id);
                   
               });
               
               var html = '<input type="hidden" id="saswp_total_reviews_list" name="saswp_total_reviews" value="'+JSON.stringify(saswp_total_reviews)+'">';
               
               jQuery(".saswp-total-reviews-list").html('');                
               jQuery(".saswp-total-reviews-list").append(html); 
           }
                      
       }

      
       function saswp_create_collection_grid(cols, pagination, perpage, offset, nextpage, saswp_coll_hide_col_r_img,color,collectionImg){
                
                var html          = '';                
                var grid_cols     = '';
                var page_count    = 0;
              
                if(saswp_total_collection.length > 0){
                    
                    page_count = Math.ceil(saswp_total_collection.length / perpage);
                    
                    html += '<div class="saswp-r1">';
                    
                    for(var i=1; i <= cols; i++){
                        grid_cols +=' 1fr'; 
                    }    

                      if(cols.length > 3){
                      html += '<ul style="grid-template-columns:'+grid_cols+';overflow-y: scroll;">'; 
                      }else{
                      html += '<ul style="grid-template-columns:'+grid_cols+';overflow-y:hidden;">';     
                      }
                    
                                                                                                                                                                                                                          
                    if(saswp_total_collection){
                            
                           var grid_col = saswp_total_collection;    

                           if(pagination && perpage > 0){
                               
                               grid_col = grid_col.slice(offset, nextpage);
                               
                           }                                                        
                           jQuery.each(grid_col, function(index, value){
                           
                            var date_str = saswp_convert_datetostring(value.saswp_review_date); 
                           
                            // if(value.saswp_is_date_in_days != '' && value.saswp_is_date_in_days == 'days'){
                            //     date_str.date = value.saswp_review_date;
                            // }
                            html += '<li>';                       
                            html += '<div class="saswp-rc">';
                            html += '<div class="saswp-rc-a">';
                            if(saswp_coll_hide_col_r_img != 1){
                                let isDefaultImg = 0;
                                let revCollImg = value.saswp_reviewer_image;
                                if(revCollImg.length > 20){
                                    let isDefault = revCollImg.includes('default_user');
                                    if(!isDefault){
                                        isDefaultImg = 1;
                                    }
                                }
                                if(isDefaultImg == 0){
                                    revCollImg = collectionImg; 
                                }
                                html += '<div class="saswp-r1-aimg">';
                                html += '<img src="'+revCollImg+'" width="56" height="56" data-is-default-img="'+isDefaultImg+'"/>';
                                html += '</div>';
                            }                            
                            html += '<div class="saswp-rc-nm">';
                            html += '<a href="#">'+value.saswp_reviewer_name+'</a>';
                            html += saswp_create_rating_html_by_value(value.saswp_review_rating,color,value.saswp_review_id);  

                            if(date_str.date){
                              html += '<span class="saswp-rc-dt">'+date_str.date+'</span>';

                            }
                            
                            html += '</div>';
                            html += '</div>';
                            
                            html += '<div class="saswp-rc-lg">';
                            html += '<img src="'+value.saswp_review_platform_icon+'"/>';
                            html += '</div>';
                            
                            html += '</div>';
                            if(jQuery('#saswp-collection-readmore-desc').is(':checked')){
                                html += '<div class="saswp-rc-cnt" style="height: auto;">';
                            }else{
                                html += '<div class="saswp-rc-cnt" style="height: 80px;">';
                            }
                            let reviewText = value.saswp_review_text;
                            if(jQuery('#saswp-collection-readmore-desc').is(':checked')){
                                reviewText = saswpAddReadmoreToReviewext(reviewText, 20);
                            }
                            html += '<p>'+reviewText+'</p>';
                            html += '</div>';

                            if(value.is_remove){
                                html += '<span platform-id="'+value.saswp_review_platform+'" data-id="'+value.saswp_review_id+'" class="dashicons dashicons-remove saswp-remove-coll-rv"></span>';
                            }
                            
                            html += '</li>';
                                                                                  
                        });
                                                      
                    }
                                                                                                                                        
                    html += '</ul>';
               
                    if(page_count > 0 && pagination){
               
                        html += '<div class="saswp-grid-pagination">';                    
                        html += '<a class="saswp-grid-page" data-id="1" href="#">&laquo;</a>'; 
                        
                        var min = (parseInt(saswp_grid_page) - 3);
                        var max = (parseInt(saswp_grid_page) + 3);

                        for(var i=1; i <= page_count; i++){

                            var hide_class = 'saswp_hide';
                            
                            if (i > min && i < max){				
                                hide_class = '';
                            }
                            
                            if(i == saswp_grid_page){
                                html += '<a class="active saswp-grid-page '+hide_class+'" data-id="'+i+'" href="#">'+i+'</a>';    
                            }else{
                                html += '<a class="saswp-grid-page '+hide_class+'" data-id="'+i+'" href="#">'+i+'</a>';    
                            }
                            
                        }      
                        
                        html += '<a class="saswp-grid-page" data-id="'+page_count+'" href="#">&raquo;</a>';                                     
                        
                        html += '</div>';                        
                        
                    }
                                                    
                    html += '</div>';
                                                                                
                }
                    jQuery(".saswp-collection-preview").html('');                    
                    jQuery(".saswp-collection-preview").append(html);
                   
                                                                                                
            }     
            
       function saswp_create_collection_by_design(design, cols, slider, arrow, dots, fomo_inverval, fomo_visibility, pagination, perpage, offset, nextpage, saswp_coll_hide_col_r_img, saswp_collection_gallery_img_hide,color,collectionImg){
                                                              
                switch(design) {
                    
                    case "grid":
                        
                         saswp_create_collection_grid(cols, pagination, perpage, offset, nextpage, saswp_coll_hide_col_r_img,color,collectionImg);
                        
                        break;
                        
                    case 'gallery':
                        
                         saswp_create_collection_slider(slider, arrow, dots, saswp_collection_gallery_img_hide,color,collectionImg);
                        
                        break;
                    
                    case 'badge':
                        
                         saswp_create_collection_badge(color);
                        
                        break;
                        
                    case 'popup':
                        
                         saswp_create_collection_popup(color);
                        
                        break;
                    
                    case 'fomo':
                        
                         saswp_create_collection_fomo(fomo_inverval, fomo_visibility,color);
                        
                        break;
                                                                
                }                           
                
            } 
            
       function saswp_on_collection_design_change(){
           
                var sorting             = jQuery(".saswp-collection-sorting").val();
                var s_rating_val        = jQuery("#saswp_collection_specific_rating_sel").val();
                var design              = jQuery(".saswp-collection-desing").val();                                   
                var cols                = jQuery("#saswp-collection-cols").val();
                var slider              = jQuery(".saswp-slider-type").val();
                var color               = jQuery(".saswpforwp-colorpicker").val();
                var collectionImg       = jQuery("#saswp_collection_image_thumbnail").val();
                
                var fomo_inverval       = jQuery("#saswp_fomo_interval").val();                
                var perpage             = parseInt(jQuery("#saswp-coll-per-page").val());
                
                var pagination          = false;
                var offset              = 0;
                var nextpage            = perpage;
                var s_rating_enable     = false;
                var saswp_coll_hide_col_r_img = false;
                var saswp_collection_gallery_img_hide = false;
                
                if(jQuery("#saswp-coll-hide_col_r_img").is(":checked")){
                    saswp_coll_hide_col_r_img = 1;
                }
                if(jQuery("#saswp_collection_gallery_img_hide").is(":checked")){
                    saswp_collection_gallery_img_hide = 1;
                }
                if(jQuery("#saswp-coll-pagination").is(":checked")){                    
                    pagination          = true;                          
                    var data_id         = saswp_grid_page;                     
                    if(data_id > 0){                        
                        nextpage            = data_id * perpage;                
                    }                    
                    offset              = nextpage - perpage;
                    
                }

                if(jQuery("#saswp_collection_specific_rating").is(":checked")){
                    s_rating_enable          = true;                                                                  
                }
                
                //var fomo_visibility     = jQuery("#saswp_fomo_visibility").val();
                
                if(jQuery("#saswp_gallery_arrow").is(':checked')){
                    var arrow          = true;
                }else{
                    var arrow          = false;
                }
                
                if(jQuery("#saswp_gallery_dots").is(':checked')){
                    var dots            = true;
                }else{
                    var dots            = false;
                }

                jQuery('#saswp_stars_color_picker').wpColorPicker({
                  
                    change: function (event, ui) {
                        var element = event.target;
                        var color = ui.color.toString();
                        jQuery(".saswp_star_color, .saswp_half_star_color").attr("style", "color:"  + color);
                        jQuery('.saswp_star').attr('stop-color',color);
                    },
                });

                if(jQuery('#saswp_review_custom_chk_box').is(':checked')){
                    jQuery('#saswp-review-cccc').show();
                }

                saswp_create_total_collection( s_rating_enable, s_rating_val );                 
                saswp_collection_sorting(sorting);  
                saswp_collection_total_reviews_id();
                saswp_create_collection_by_design(design, cols, slider, arrow, dots, fomo_inverval, fomo_inverval, pagination, perpage, offset, nextpage, saswp_coll_hide_col_r_img, saswp_collection_gallery_img_hide,color,collectionImg);                                                
           
       }  
       
       function saswp_get_collection_data(rvcount, platform_id, current = null, review_id =  null, reviews_ids = null, platformPlace='all'){
           
            jQuery.get(ajaxurl, 
                             { action:"saswp_add_to_collection", rvcount:rvcount, reviews_ids:reviews_ids, review_id:review_id, platform_id:platform_id,platform_place:platformPlace, saswp_security_nonce:saswp_localize_data.saswp_security_nonce},
                             
                              function(response){                                  
                    
                              if(response['status']){   
                                                                      
                                jQuery.each(response['message'], function(index, value){

                                    var id = JSON.parse(value.saswp_review_platform);
                                    var narr =  [];
                                    narr.push(value);

                                    if(typeof(saswp_collection[id]) == 'undefined'){
                                        saswp_collection[id] = narr;
                                    }else{
                                        var result = [...new Set([...saswp_collection[id], ...narr])];
                                        saswp_collection[id] = result;
                                    }
                                    
                                 });

                                saswp_on_collection_design_change();
                                                                            
                              }
                              
                              if(current){
                                  current.removeClass('updating-message');    
                              }                              
                              
                             },'json');
           
       }
       
       
function saswpIsEmail(email) {
        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return regex.test(email);
}
function saswp_select2(){

    var $select2 = jQuery('.saswp-select2');
    
    if($select2.length > 0){

         jQuery($select2).each(function(i, obj) {
         var currentP = jQuery(this);  
         var $defaultResults = jQuery('option[value]:not([selected])', currentP);   
         var defaultResults = [];

        $defaultResults.each(function () {
            var $option = jQuery(this);
            defaultResults.push({
                id: $option.attr('value'),
                text: $option.text()
            });
        });

        var type = currentP.attr('data-type');
        var ajaxnewurl = ajaxurl + '?action=saswp_get_select2_data&saswp_security_nonce='+saswp_localize_data.saswp_security_nonce+'&type='+type;

   currentP.select2({           
        ajax: {             
            url: ajaxnewurl,
            delay: 250, 
            cache: false
        },            
       minimumInputLength: 2, 
       minimumResultsForSearch : 50,
       dataAdapter: jQuery.fn.select2.amd.require('select2/data/extended-ajax'),
       defaultResults: defaultResults      
     });
 
    });

    }                    
    
}

function saswp_get_platform_place_list(getPlatformId) {
    let platformPlaceOpt = '<option value="all" selected>All</option>';
    if(getPlatformId > 0){
        jQuery.get(ajaxurl, 
        { 
            action:"saswp_get_platform_place_list", platform_id:getPlatformId, saswp_security_nonce:saswp_localize_data.saswp_security_nonce
        },
        function(response){                                  
          if(response['status']){   
            if(response['message'] && jQuery.type(response['message']) == 'object'){
                jQuery.each(response['message'], function(index, value){
                    platformPlaceOpt += '<option value="'+value+'">'+value+'</option>';
                    jQuery('#saswp-review-platform-places').html(platformPlaceOpt);
                });
            }else{
                jQuery('#saswp-review-platform-places').html('<option value="all">All</option>'); 
            }                                       
          }else{
            jQuery('#saswp-review-platform-places').html('<option>Pleace Not Found</option>');
          }
        },'json');
    }
}

/**
 * @since 1.22
 * Function to display metalist options
 * Solution to ticket id #2026
 * */
function saswp_acf_repeaters_html_generator(index, schema_id, fields_type, div_type, schema_fields){
          
    $newclosebtn = '';
    otherRepeatorClose = '';
    $reviewtitle = index+1

    $addRevewTitle = '';

    var html = '';
    otherRepeatorClose = '<a class="saswp-table-close">X</a>';
    html += '<div class="saswp-'+div_type+'-table-div saswp-dynamic-propertie" data-id="'+index+'">'
                + $addRevewTitle
                +  otherRepeatorClose
                + '<table class="form-table saswp-'+div_type+'-table">' 

    if (!saswp_meta_list['text']) {
        jQuery.ajax({
            type: 'GET',
            url: ajaxurl,
            dataType: 'json',
            async: false,
            data: {action:"saswp_get_meta_list", saswp_security_nonce:saswp_localize_data.saswp_security_nonce},
            success: function(response){
                saswp_meta_list['text'] = response['text'];    
            } 
        }); 
    }
        
    jQuery.each(schema_fields, function(eachindex, element){
                        
        var meta_class = "";
                
        var options_html = "";          
        options_html = '';     
        if(saswp_meta_list['text']){         
            jQuery.each(saswp_meta_list['text'], function(key,value){ 
               if(value['label'] != 'Repeater Mapping'){ 
                   options_html += '<optgroup label="'+value['label']+'">';   

                   jQuery.each(value['meta-list'], function(key, value){
                       options_html += '<option value="'+key+'">'+value+'</option>';
                   });                                                                                  
                   options_html += '</optgroup>';
                }

            });
        }
        
         html += '<tr>'
        + '<th class="saswp-p-l-10">'+element.label+'</th>'
        + '<td>'
        
        + '<select id="'+element.name+'_'+index+'_'+schema_id+'" name="'+fields_type+schema_id+'['+index+']['+element.name+']">'
        + options_html
        + '</select>'
        
        + '</td>'
        + '</tr>';
                                                                                    
    });                                                             
    html += '</table>'
            + '</div>';
             
    return html;
    
}
// End of function for ticket id #2026

jQuery(document).on('click', '#saswp_reset_collection_image', function(e){
    let defaultImg = jQuery(this).attr('data-img');
    jQuery('#saswp_collection_reviewer_image').attr('src', defaultImg);
    jQuery('#saswp_collection_image_thumbnail').val(defaultImg);
    jQuery('.saswp_image_prev').attr('src', defaultImg);
    jQuery('.saswp-r1-aimg').each(function(i, e){
        let defaultImgFlag = jQuery(this).children('img').attr('data-is-default-img');
        if(defaultImgFlag == 0){
            jQuery(this).children('img').attr('src', defaultImg);
        }

    });

    jQuery('.saswp-rc-a').each(function(i, e){
        let defaultImgFlag = jQuery(this).children('img').attr('data-is-default-img');
        if(defaultImgFlag == 0){
            jQuery(this).children('img').attr('src', defaultImg);
        }

    });
    
});

// Remove height of review card
jQuery(document).on('change', '#saswp-collection-readmore-desc, #saswp-collection-gallery-readmore-desc', function(e){
    let readId = jQuery(this).attr('id');
    if(jQuery(this).is(':checked')){
        // jQuery('.saswp-rc-cnt').css('height', 'auto');

        jQuery.each(jQuery('.saswp-rc-cnt p'), function(e){
            let reviewText = jQuery(this).text();
            if(reviewText.length > 0){
                if(readId == 'saswp-collection-readmore-desc'){
                    reviewText = saswpAddReadmoreToReviewext(reviewText, 20);
                    jQuery(this).html(reviewText);
                }
                if(readId == 'saswp-collection-gallery-readmore-desc'){
                    if(jQuery('#saswp_collection_gallery_type').val() == 'slider'){
                        reviewText = saswpAddReadmoreToReviewext(reviewText, 40);
                        jQuery(this).html(reviewText);
                    }else{
                        reviewText = saswpAddReadmoreToReviewext(reviewText, 20);
                        // jQuery('.saswp-rc-cnt').css('height', '00px !important');
                        jQuery(this).html(reviewText);    
                    }    
                }
            }
        });

    }else{
        // jQuery('.saswp-rc-cnt').css('height', 'auto');
        if(readId == 'saswp-collection-readmore-desc'){
            // jQuery('.saswp-rc-cnt').css('height', '80px !important');
        }else if(readId == 'saswp-collection-gallery-readmore-desc'){
            // jQuery('.saswp-rc-cnt').css('height', '120px !important');
        }
    }
});

function saswpAddReadmoreToReviewext(reviewText, wordLimit) {
    reviewText = jQuery.trim(reviewText);
    if(reviewText.length > 0){
        let splitText = reviewText.split(" ");
        if(splitText.length > wordLimit){
            let wcnt = 1;
            let briefText = readMoreText = '';
            briefText = '<span class="saswp-breaf-review-text">';
            readMoreText = '<span class="saswp-more-review-text" style="display: none;">';
            jQuery.each(splitText, function(i, e){
                if(wcnt <= wordLimit){
                    briefText += e  + " ";
                }else{
                    readMoreText += e + " ";
                }
                wcnt++;    
            });
            briefText += '<a href="#" class="saswp-read-more">Read More</a> </span>';
            readMoreText += '</span>';
            reviewText = briefText + readMoreText;
        }
    }
    return reviewText;
}

// Expand review text on click on Read More
jQuery(document).on('click', '.saswp-read-more', function(e){
    e.preventDefault();
    jQuery(this).parent().next().show();
    jQuery('.saswp-rc-cnt').css('height', 'auto');
    jQuery(this).remove();
    let divHeight = [];
    jQuery.each(jQuery('.saswp-r2-b'), function(e1){
        divHeight.push(jQuery(this).height());
    }); 
    let maxHight = Math.max.apply(null, divHeight);
    jQuery('.saswp-r2-b').height(maxHight);
});