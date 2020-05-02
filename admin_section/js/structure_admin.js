var clone = function(){
		jQuery(".structured-clone").off("click").click(function(){
                        var group_index = jQuery(this).closest(".saswp-placement-group").attr('data-id');                                                
			var selectrow = jQuery(document).find("#call_html_template_sdwp").html();                        
			nextId = jQuery(this).parents("tbody").find("tr").length;			
                        selectrow = selectrow.replace(/\[0\]/g, "["+nextId+"]"); 
                        selectrow = selectrow.replace(/\[group-0\]/g, "[group-"+group_index+"]"); 			
			jQuery(this).parents("tr").after(selectrow);removeHtml();clone();
		});
	}
	var removeHtml = function(){
		jQuery(".structured-delete").off("click").click(function(){
                      var class_count = jQuery(".saswp-placement-group").length;  
                      
                      if(class_count==1){
                       if(jQuery(this).parents("tbody").find("tr").length>1){
				jQuery(this).parents("tr").remove();
			}   
                      }else{
                         if(jQuery(this).parents("tbody").find("tr").length == 1){
				jQuery(this).parents(".saswp-placement-group").remove();
			} else{
                                jQuery(this).parents("tr").remove();
                        }
                      }
			
		});
	}
jQuery(document).ready(function($){            
                
        $('#saswp-dayofweek-opens-time').timepicker({ 'timeFormat': 'H:i:s'});
        $('#saswp-dayofweek-closes-time').timepicker({ 'timeFormat': 'H:i:s'});
                               
        $(".saswp-placement-or-group").on("click", function(e){
            e.preventDefault();
            var group_index ='';
            var group_index = $(".saswp-placement-group").length;             
                        
          var selectrow = jQuery(document).find("#call_html_template_sdwp").html();
              selectrow = selectrow.replace(/\[group-0\]/g, "[group-"+group_index+"]");
          var placement_group_html = '';
              placement_group_html +='<table class="widefat saswp-placement-table" style="border:0px;">';
              placement_group_html += selectrow; 
              placement_group_html +='</table>';  
                              
          var html='';  
              html +='<div class="saswp-placement-group" name="data_group_array['+group_index+']" data-id="'+group_index+'">';
              html +='<span style="margin-left:10px;font-weight:600">Or</span>';
              html +=placement_group_html;
              html +='</div>';                
           $(".saswp-placement-group[data-id="+(group_index-1)+"]").after(html); 
           group_index++;
           clone();
           removeHtml();
        });
    
	var selectrow = $("#saswp_amp_select").find("table.widefat tr").html();
	$("body").append("<script type='template/html' id='call_html_template_sdwp'><tr class='toclone cloneya'>"+selectrow+"</tr>");
	clone();
	removeHtml();
	$(document).on("change", ".select-post-type", function(){
                var current_change = $(this);
		var parent = $(this).parents('tr').find(".insert-ajax-select");
		var selectedValue = $(this).val();
                var tdindex = [1,2,3,4]; 
                if(selectedValue !='show_globally'){
                    
                $.each(tdindex, function(i,e){  
                    $(current_change).closest('tr').find('td').eq(e).show();  
                 });    
                
		var currentFiledNumber = $(this).attr("class").split(" ")[2];
                var saswp_call_nonce = jQuery("#saswp_select_name_nonce").val();
		
		parent.find(".ajax-output").remove();
		parent.find(".ajax-output-child").remove();
		parent.find(".spinner").attr("style","visibility:visible");
		parent.children(".spinner").addClass("show");
		var ajaxURL = saswp_app_object.ajax_url;
                var group_index = jQuery(this).closest(".saswp-placement-group").attr('data-id'); 
		//ajax call
        $.ajax({
        url : ajaxURL,
        method : "POST",
        data: { 
          action: "create_ajax_select_sdwp", 
          id: selectedValue,
          number : currentFiledNumber,
          group_number : group_index,
          saswp_call_nonce : saswp_call_nonce
        },
        beforeSend: function(){ 
        },
        success: function(data){ 
        	// This code is added twice " withThis.find('.ajax-output').remove(); "
      			parent.find(".ajax-output").remove();
      			parent.children(".spinner").removeClass("show");
      			parent.find(".spinner").attr("style","visibility:hidden").hide();
      			parent.append(data);
      			taxonomyDataCall();
        },
        error: function(data){
          console.log("Failed Ajax Request");
          console.log(data);
        }
      }); 
                }else{
                    $.each(tdindex, function(i,e){   
             $(current_change).closest('tr').find('td').eq(e).hide(); 
            
            
            });
                }
	});
	taxonomyDataCall();
	$("#notAccessibleForFree").click(function(){
		if($(this).is(':checked')){
			$("#paywall_class_name").parents("tr").show();
			$("#isAccessibleForFree").parents("tr").show();
		}else{
			$("#paywall_class_name").parents("tr").hide();
			$("#isAccessibleForFree").parents("tr").hide();
		}
	})
	
});//jQuery(document) closed
function taxonomyDataCall(){
	jQuery('select.ajax-output').change(function(){
				
		var mainSelectedValue = jQuery(this).closest("tr").find('.select-post-type').val();
		
		if(mainSelectedValue=="ef_taxonomy"){
			parentSelector = jQuery(this).parents("td").find(".insert-ajax-select");
			var selectedValue = jQuery(this).val();			
			var currentFiledNumber = jQuery(this).attr("name").split("[")[1].replace("]",'');
                        var saswp_call_nonce = jQuery("#saswp_select_name_nonce").val();
			
			parentSelector.find(".ajax-output-child").remove();
			parentSelector.find(".spinner").attr("style","visibility:visible");
			parentSelector.children(".spinner").addClass("show");
			
			var ajaxURL = saswp_app_object.ajax_url;
                         var group_index = jQuery(this).closest(".saswp-placement-group").attr('data-id'); 
			//ajax call
			jQuery.ajax({
	        url : ajaxURL,
	        method : "POST",
	        data: { 
	          action: "create_ajax_select_sdwp_taxonomy", 
	          id: selectedValue,
	          number : currentFiledNumber,
                  group_number : group_index,
                  saswp_call_nonce: saswp_call_nonce
	        },
	        beforeSend: function(){ 
	        },
	        success: function(data){ 
	        	// This code is added twice " withThis.find('.ajax-output').remove(); "
	      			parentSelector.find(".ajax-output-child").remove();
	      			parentSelector.children(".spinner").removeClass("show");
	      			parentSelector.find(".spinner").attr("style","visibility:hidden").hide();
	      			parentSelector.append(data);
	      			taxonomyDataCall();
	        },
	        error: function(data){
	          console.log("Failed Ajax Request");
	          console.log(data);
	        }
	      }); 
		}
	});
}

