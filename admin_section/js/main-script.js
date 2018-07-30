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
    //Settings page jquery starts here            
    $(".saswp-checkbox").change(function(){
          var id = $(this).attr("id");         
                  switch(id){
                      case 'saswp-for-wordpress-checkbox':  
                          
                          if ($(this).is(':checked')) {              
                            $("#saswp-for-wordpress").val(1);  
                          }else{
                            $("#saswp-for-wordpress").val(0);  
                          }                          
                          break;
                      case 'saswp-for-amp-checkbox':
                          
                          if ($(this).is(':checked')) {              
                            $("#saswp-for-amp").val(1);  
                          }else{
                            $("#saswp-for-amp").val(0);  
                          }
                      break;
                      case 'saswp-for-amp-with-scheme-checkbox':
                          
                        if ($(this).is(':checked')) {              
                          $("#saswp-for-amp-with-scheme-app").val(1);
                          $("#saswp-for-amp").parent().parent('li').hide();  
                        }else{
                          $("#saswp-for-amp-with-scheme-app").val(0);
                          $("#saswp-for-amp").parent().parent('li').show();
                        }
                      break;
                      case 'saswp_kb_contact_1_checkbox':
                          
                        if ($(this).is(':checked')) {              
                         $("#saswp_kb_contact_1").val(1); 
                         $("#saswp_kb_telephone, #saswp_contact_type").parent().parent('li').show(); 
                       }else{
                         $("#saswp_kb_contact_1").val(0);  
                         $("#saswp_kb_telephone, #saswp_contact_type").parent().parent('li').hide(); 
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
                              }else{
                                $("#saswp_archive_schema").val(0);           
                              }
                      break;
                      case 'saswp_breadcrumb_schema_checkbox':
                          
                            if ($(this).is(':checked')) {              
                              $("#saswp_breadcrumb_schema").val(1);             
                            }else{
                              $("#saswp_breadcrumb_schema").val(0);           
                            }
                      break;                                           
                      default:
                          break;
                  }
                             
         }).change();
        
         $("#saswp_kb_type").change(function(){
          var datatype = $(this).val();        
          for(var i=1;i<=12;i++){
            if(datatype ==="Person"){
             if(i<8){
                $( ".saswp-knowledge-base li:eq('"+i+"')" ).hide();          
              }else{
                $( ".saswp-knowledge-base li:eq('"+i+"')" ).show();            
              }    
            }else{
              if(i<8){
                $( ".saswp-knowledge-base li:eq('"+i+"')" ).show();          
              }else{
                $( ".saswp-knowledge-base li:eq('"+i+"')" ).hide();            
              }    
            }
                       
          }                                           
     }).change();     
     $("input[data-id=media]").click(function(e) {	// Application Icon upload
		e.preventDefault();
                var button = $(this);
                var id = button.attr('id').replace('_button', '');                
		var saswpMediaUploader = wp.media({
			title: "Application Icon",
			button: {
				text: "Select Icon"
			},
			multiple: false  // Set this to true to allow multiple files to be selected
		})
		.on("select", function() {
			var attachment = saswpMediaUploader.state().get('selection').first().toJSON();                            
			
                         $("#"+id).val(attachment.url);
                         $("input[data-id='"+id+"_id']").val(attachment.id);
                         $("input[data-id='"+id+"_height']").val(attachment.height);
                         $("input[data-id='"+id+"_width']").val(attachment.width);
                         $("input[data-id='"+id+"_thumbnail']").val(attachment.url);
		})
		.open();
	});
        //Settings page jquery ends here
});
