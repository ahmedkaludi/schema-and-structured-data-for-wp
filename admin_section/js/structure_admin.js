var clone = function(){
		jQuery(".structured-clone").off("click").click(function(){
			var selectrow = jQuery(document).find("#call_html_template_sdwp").html();
			nextId = jQuery(this).parents("tbody").find("tr").length;
			selectrow = selectrow.replace(/\[0\]/g, "["+nextId+"]");
			console.log(selectrow);
			jQuery(this).parents("tr").after(selectrow);removeHtml();clone();
		});
	}
	var removeHtml = function(){
		jQuery(".structured-delete").off("click").click(function(){
			if(jQuery(this).parents("tbody").find("tr").length>1){
				jQuery(this).parents("tr").remove();
			}
		});
	}
jQuery(document).ready(function($){
	var selectrow = $("#amp_sdwp_select").find("table.widefat tr").html();
	$("body").append("<script type='template/html' id='call_html_template_sdwp'><tr class='toclone cloneya'>"+selectrow+"</tr>");
	clone();
	removeHtml();
	$(document).on("change", ".select-post-type", function(){
		var parent = $(this).parents('tr').find(".insert-ajax-select");
		var selectedValue = $(this).val();
		var currentFiledNumber = $(this).attr("class").split(" ")[2];
                var saswp_call_nonce = $("#saswp_select_name_nonce").val();
		
		parent.find(".ajax-output").remove();
		parent.find(".ajax-output-child").remove();
		parent.find(".spinner").attr("style","visibility:visible");
		parent.children(".spinner").addClass("show");
		var ajaxURL = amp_sdwp_field_data.ajax_url;
		//ajax call
		$.ajax({
        url : ajaxURL,
        method : "POST",
        data: { 
          action: "create_ajax_select_sdwp", 
          id: selectedValue,
          number : currentFiledNumber,
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
		var mainSelectedValue = jQuery(".select-post-type").val();
		if(mainSelectedValue=="ef_taxonomy"){
			parentSelector = jQuery(this).parents("td").find(".insert-ajax-select");
			var selectedValue = jQuery(this).val();
			var currentFiledNumber = jQuery(this).attr("name").split("[")[1].replace("]",'');
                        var saswp_call_nonce = $("#saswp_select_name_nonce").val();
			
			parentSelector.find(".ajax-output-child").remove();
			parentSelector.find(".spinner").attr("style","visibility:visible");
			parentSelector.children(".spinner").addClass("show");
			
			var ajaxURL = amp_sdwp_field_data.ajax_url;
			//ajax call
			jQuery.ajax({
	        url : ajaxURL,
	        method : "POST",
	        data: { 
	          action: "create_ajax_select_sdwp_taxonomy", 
	          id: selectedValue,
	          number : currentFiledNumber,
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

