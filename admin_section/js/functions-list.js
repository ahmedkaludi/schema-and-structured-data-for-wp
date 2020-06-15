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
             
               date_time = {
                   time : date_string.toLocaleTimeString(),
                   date : date_string.toLocaleDateString()
               };
           }else{
              date_time = {
                   time : '',
                   date : ''
               };
           }
           
           return date_time;
           
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
        
            jQuery('.saswp-reviews-datepicker-picker').datepicker({
             dateFormat: "yy-mm-dd"            
          });
        }
        
       function saswp_schema_datepicker(){
        
            jQuery('.saswp-datepicker-picker').datepicker({
             dateFormat: "yy-mm-dd",             
          });
          
        }
        
       function saswp_schema_timepicker(){
         jQuery('.saswp-timepicker').timepicker({ 'timeFormat': 'H:i:s'});
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
        
        if(schema_type === 'Review'){
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

                                if(schema_type == 'Review'){
                                    schema_subtype = jQuery('select.saswp-item-reivewed-list option:selected').val();
                                }
          
                                 var html = '<tr>';                                                                                                                            
                                     html += '<td>';                                     
                                     html += '<select class="saswp-custom-fields-name">';
                                     
                                     if(schema_type == 'Review'){
                                       html += '<optgroup label="Review">';
                                       html += '<option value="saswp_review_name">Review Name</option>';    
                                       html += '<option value="saswp_review_description">Review Description</option>';                                              
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
                                     
                                     if(schema_type == 'Review'){
                                         html += '</optgroup>'; 
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

                                           if(html){
                                               jQuery('.saswp-'+div_type+'-section[data-id="'+schema_id+'"]').append(html);
                                               saswp_schema_datepicker();
                                               saswp_schema_timepicker();
                                               current_fly.removeClass('updating-message');
                                           }

                                    },'json');
                                
                            }else{
                                
                              var html = saswp_fields_html_generator(index, schema_id, fields_type, div_type, saswp_meta_fields[fields_type]);

                               if(html){
                                   jQuery('.saswp-'+div_type+'-section[data-id="'+schema_id+'"]').append(html);
                                   saswp_schema_datepicker();
                                   saswp_schema_timepicker();
                                   current_fly.removeClass('updating-message');
                               }
                                                                
                            }
                            
                             
       }
       
       function saswp_fields_html_generator(index, schema_id, fields_type, div_type, schema_fields){
            
            var html = '';
            
            html += '<div class="saswp-'+div_type+'-table-div saswp-dynamic-properties" data-id="'+index+'">'
                        +  '<a class="saswp-table-close">X</a>'
                        + '<table class="form-table saswp-'+div_type+'-table">' 
                
            jQuery.each(schema_fields, function(eachindex, element){
                                
                var meta_class = "";
                
                if(element.name.indexOf('published_date') > -1 || element.name.indexOf('date_created') > -1 || element.name.indexOf('created_date') > -1 || element.name.indexOf('modified_date') > -1 || element.name.indexOf('date_published') > -1 || element.name.indexOf('date_modified') > -1){
                    meta_class = "saswp-datepicker-picker";   
                }
                
                switch(element.type) {
                    
                    case "number":
                    case "text":
                      
                        html += '<tr>'
                        + '<th>'+element.label+'</th><td><input class="'+meta_class+'" style="width:100%" type="'+element.type+'" id="'+element.name+'_'+index+'_'+schema_id+'" name="'+fields_type+schema_id+'['+index+']['+element.name+']"></td>'
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
        
       function saswp_create_total_collection(){
           
           var platform_list = '';
           
           saswp_total_collection = []; 
                      
           for(var key in saswp_collection){
                                             
               if(saswp_collection[key]){
                   
                   jQuery.each(saswp_collection[key], function(index, value){                                               
                        saswp_total_collection.push(value);
                   
                    });
                                                         
                   platform_list += saswp_function_added_platform(key, saswp_collection[key].length );
               }
               
           }                
                jQuery(".saswp-platform-added-list").html('');                
                jQuery(".saswp-platform-added-list").append(platform_list);   
                                 
       } 
        
       function saswp_create_rating_html_by_value(rating_val){
                
                
                var starating = '';
        
                     starating += '<div class="saswp-rvw-str">';

                    for(var j=0; j<5; j++){  

                          if(rating_val >j){

                                var explod = rating_val.split('.');

                                if(explod[1]){

                                    if(j < explod[0]){

                                        starating +='<span class="str-ic"></span>';   

                                    }else{

                                        starating +='<span class="half-str"></span>';   

                                    }                                           
                                }else{

                                    starating +='<span class="str-ic"></span>';    

                                }

                          } else{
                                starating +='<span class="df-clr"></span>';   
                          }                                                                                                                                
                        }
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
            
            platform_list += '<div class="cancel-btn">';
            platform_list += '<span>'+jQuery("#saswp-plaftorm-list option[value="+key+"]").text()+'</span><span>('+rvcount+')</span>';
            platform_list += '<input type="hidden" name="saswp_platform_ids['+key+']" value="'+rvcount+'">';
            platform_list += '<a platform-id="'+key+'" class="button button-default saswp-remove-platform"></a>';
            platform_list += '</div>';
            
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
		
		setWidth();
		setTransform();
		makeDots();
                
                jQuery(document).ready( function(){
					setWidth();
					setTransform();
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
            
       function saswp_review_desing_for_slider(value){
                      
                            var date_str = saswp_convert_datetostring(value.saswp_review_date); 
                        
                            var html = '';
           
                                html += '<div class="saswp-r2-sli">';
                                html += '<div class="saswp-r2-b">';                                
                                html += '<div class="saswp-r2-q">';
                                html += '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" width="95.333px" height="95.332px" viewBox="0 0 95.333 95.332" style="enable-background:new 0 0 95.333 95.332;" xml:space="preserve"><path d="M30.512,43.939c-2.348-0.676-4.696-1.019-6.98-1.019c-3.527,0-6.47,0.806-8.752,1.793    c2.2-8.054,7.485-21.951,18.013-23.516c0.975-0.145,1.774-0.85,2.04-1.799l2.301-8.23c0.194-0.696,0.079-1.441-0.318-2.045    s-1.035-1.007-1.75-1.105c-0.777-0.106-1.569-0.16-2.354-0.16c-12.637,0-25.152,13.19-30.433,32.076    c-3.1,11.08-4.009,27.738,3.627,38.223c4.273,5.867,10.507,9,18.529,9.313c0.033,0.001,0.065,0.002,0.098,0.002    c9.898,0,18.675-6.666,21.345-16.209c1.595-5.705,0.874-11.688-2.032-16.851C40.971,49.307,36.236,45.586,30.512,43.939z"></path><path d="M92.471,54.413c-2.875-5.106-7.61-8.827-13.334-10.474c-2.348-0.676-4.696-1.019-6.979-1.019    c-3.527,0-6.471,0.806-8.753,1.793c2.2-8.054,7.485-21.951,18.014-23.516c0.975-0.145,1.773-0.85,2.04-1.799l2.301-8.23    c0.194-0.696,0.079-1.441-0.318-2.045c-0.396-0.604-1.034-1.007-1.75-1.105c-0.776-0.106-1.568-0.16-2.354-0.16    c-12.637,0-25.152,13.19-30.434,32.076c-3.099,11.08-4.008,27.738,3.629,38.225c4.272,5.866,10.507,9,18.528,9.312    c0.033,0.001,0.065,0.002,0.099,0.002c9.897,0,18.675-6.666,21.345-16.209C96.098,65.559,95.376,59.575,92.471,54.413z"></path></svg>';
                                html += '</div>';
                                html += '<div class="saswp-rc-cnt">';
                                html += '<p>';
                                html += value.saswp_review_text;
                                html += '</p>';
                                html += '</div>';
                                html += '<div class="saswp-r2-strs">';
                                html += '<span class="saswp-r2-s">';
                                html += saswp_create_rating_html_by_value(value.saswp_review_rating);
                                html += '</span>';
                                html += '</div>';
                                html += '</div>';
                                html += '<div class="saswp-rc">';
                                html += '<div class="saswp-rc-a">';
                                html += '<img src="'+value.saswp_reviewer_image+'"/>';
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
       
       function saswp_create_collection_slider(slider, arrow, dots){
                                
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
                                
                                html += saswp_review_desing_for_slider(value);
                                
                                html += '</div>';
                            
                          });
                            
                         }   
                         
                    if(slider == 'carousel'){
                             
                            var chunkarr = saswpChunkArray(saswp_total_collection, 3);
                            
                            if(chunkarr){
                                                                                                                
                            jQuery.each(chunkarr, function(p_index, p_value){
                                                                
                                html += '<div class="saswp-si">';
                                                                    
                                jQuery.each(p_value, function(index, value){
                                   
                                    html += saswp_review_desing_for_slider(value);
                                                                                               
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
            
       function saswp_create_collection_badge(){
                
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
                        
                        jQuery.each(saswp_collection[key], function(index, value){
                            platform_icon = value.saswp_review_platform_icon;
                            platform_name = value.saswp_review_platform_name;

                            if(platform_name == 'Self'){
                                platform_name = saswp_localize_data.trans_self;
                            }

                            sum_of_rating += parseFloat(value.saswp_review_rating);
                            review_count++;
                        });  
                        if(sum_of_rating > 0){
                        
                            average_rating = sum_of_rating / review_count;
                            
                        }
                        
                        if(saswp_collection[key]){
                            
                            html += '<li>';                       
                      html += '<a href="#">'; 

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
                          html += '<span class="saswp-stars">';
                           html += saswp_create_rating_html_by_value(average_rating.toString()); 
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
            
       function saswp_create_collection_popup(){
           
                var html          = '';                
                var html_list     = '';
                
                if(saswp_total_collection.length > 0){
                        
                        var review_count   = 0;                        
                        var sum_of_rating  = 0;
                        var average_rating = 1;
                            
                        jQuery.each(saswp_total_collection, function(index, value){
                            
                            platform_icon = value.saswp_review_platform_icon;
                            sum_of_rating += parseFloat(value.saswp_review_rating);
                            review_count++;
                            
                            var date_str = saswp_convert_datetostring(value.saswp_review_date); 
                            
                            html_list += '<li>';
                            html_list += '<div class="saswp-r4-b">';
                            html_list += '<span class="saswp-r4-str">';
                            html_list += saswp_create_rating_html_by_value(value.saswp_review_rating);
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
                        html += '<span>';
                        html += saswp_create_rating_html_by_value(average_rating.toString());
                        html += '</span>';
                        html += '<span class="saswp-r4-rnm">'+average_rating.toFixed(1)+' from '+review_count+' reviews</span>';                    
                        html += '</div>';

                        html += '<div class="saswp-onclick-show">';
                        html += '<span>Ratings and reviews</span>';                    
                        html += '<span class="saswp-mines"></span>';                    
                        html += '</div>';

                        html += '</div>';
                        html += '<div id="saswp-reviews-cntn">';
                        html += '<div class="saswp-r4-info">';
                        html += '<ul>';

                        html += '<li class="saswp-r4-r">';
                        html += '<span>';
                        html += saswp_create_rating_html_by_value(average_rating.toString());
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
            
       function saswp_create_collection_fomo(fomo_inverval, fomo_visibility){
                
                var html = '';                
                                                                                                            
                if(saswp_total_collection.length > 0){

                 jQuery.each(saswp_total_collection, function(index, value){

                    var date_str = saswp_convert_datetostring(value.saswp_review_date); 

                    html += '<div id="'+index+'" class="saswp-r5">';
                    html += '<div class="saswp-r5-r">';                            
                    html += '<div class="saswp-r5-lg">';
                    html += '<span>';
                    html += '<img height="70" width="70" src="'+value.saswp_review_platform_icon+'"/>';
                    html += '</span>';
                    html += '</div>';                            
                    html += '<div class="saswp-r5-rng">';
                      html += saswp_create_rating_html_by_value(value.saswp_review_rating);
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
               
               var html = '<input type="hidden" name="saswp_total_reviews" value="'+JSON.stringify(saswp_total_reviews)+'">';
               
               jQuery(".saswp-total-reviews-list").html('');                
               jQuery(".saswp-total-reviews-list").append(html); 
           }
                      
       }
       
       function saswp_create_collection_grid(cols, pagination, perpage, offset, nextpage){
                
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
                            
                            html += '<li>';                       
                            html += '<div class="saswp-rc">';
                            html += '<div class="saswp-rc-a">';
                            html += '<div class="saswp-r1-aimg">';
                            html += '<img src="'+value.saswp_reviewer_image+'" width="56" height="56"/>';
                            html += '</div>';
                            html += '<div class="saswp-rc-nm">';
                            html += '<a href="#">'+value.saswp_reviewer_name+'</a>';
                            html += saswp_create_rating_html_by_value(value.saswp_review_rating);  

                            if(date_str.date){
                              html += '<span class="saswp-rc-dt">'+date_str.date+'</span>';

                            }
                            
                            html += '</div>';
                            html += '</div>';
                            
                            html += '<div class="saswp-rc-lg">';
                            html += '<img src="'+value.saswp_review_platform_icon+'"/>';
                            html += '</div>';
                            
                            html += '</div>';
                            html +='<div class="saswp-rc-cnt">';
                            html += '<p>'+value.saswp_review_text+'</p>';
                            html += '</div>';
                            html += '</li>';
                                                                                  
                        });
                                                      
                    }
                                                                                                                                        
                    html += '</ul>';
               
                    if(page_count > 0 && pagination){
               
                        html += '<div class="saswp-grid-pagination">';                    
                        html += '<a class="saswp-grid-page" data-id="1" href="#">&laquo;</a>'; 
                        
                        for(var i=1; i <= page_count; i++){
                            
                            if(i == saswp_grid_page){
                                html += '<a class="active saswp-grid-page" data-id="'+i+'" href="#">'+i+'</a>';    
                            }else{
                                html += '<a class="saswp-grid-page" data-id="'+i+'" href="#">'+i+'</a>';    
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
            
       function saswp_create_collection_by_design(design, cols, slider, arrow, dots, fomo_inverval, fomo_visibility, pagination, perpage, offset, nextpage){
                                                              
                switch(design) {
                    
                    case "grid":
                        
                         saswp_create_collection_grid(cols, pagination, perpage, offset, nextpage);
                        
                        break;
                        
                    case 'gallery':
                        
                         saswp_create_collection_slider(slider, arrow, dots);
                        
                        break;
                    
                    case 'badge':
                        
                         saswp_create_collection_badge();
                        
                        break;
                        
                    case 'popup':
                        
                         saswp_create_collection_popup();
                        
                        break;
                    
                    case 'fomo':
                        
                         saswp_create_collection_fomo(fomo_inverval, fomo_visibility);
                        
                        break;
                                                                
                }                           
                
            } 
            
       function saswp_on_collection_design_change(){
           
                var sorting             = jQuery(".saswp-collection-sorting").val();
                var design              = jQuery(".saswp-collection-desing").val();                                   
                var cols                = jQuery("#saswp-collection-cols").val();
                var slider              = jQuery(".saswp-slider-type").val();
                
                var fomo_inverval       = jQuery("#saswp_fomo_interval").val();                
                var perpage             = parseInt(jQuery("#saswp-coll-per-page").val());
                
                var pagination          = false;
                var offset              = 0;
                var nextpage            = perpage;
                
                if(jQuery("#saswp-coll-pagination").is(":checked")){                    
                    pagination          = true;                          
                    var data_id         = saswp_grid_page;                     
                    if(data_id > 0){                        
                        nextpage            = data_id * perpage;                
                    }                    
                    offset              = nextpage - perpage;
                    
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
                                                
                saswp_create_total_collection();                 
                saswp_collection_sorting(sorting);  
                saswp_collection_total_reviews_id();
                saswp_create_collection_by_design(design, cols, slider, arrow, dots, fomo_inverval, fomo_inverval, pagination, perpage, offset, nextpage);                                                
           
       }  
       
       function saswp_get_collection_data(rvcount, platform_id, current = null){
           
            jQuery.get(ajaxurl, 
                             { action:"saswp_add_to_collection", rvcount:rvcount, platform_id:platform_id, saswp_security_nonce:saswp_localize_data.saswp_security_nonce},
                             
                              function(response){                                  
                    
                              if(response['status']){   
                                      
                                        var res_json = response['message'];
                                                                            
                                        saswp_collection[platform_id] = res_json;
                                      
                                        saswp_collection[platform_id] = jQuery.extend(saswp_collection[platform_id], res_json);
                                                                                                                                                                                                                                                                                                       
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