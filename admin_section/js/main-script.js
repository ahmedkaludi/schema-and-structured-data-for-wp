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
    $(".ampforwp-pwa-colorpicker").wpColorPicker();	// Color picker
	$(".ampforwp-pwa-icon-upload").click(function(e) {	// Application Icon upload
		e.preventDefault();
		var amppwaMediaUploader = wp.media({
			title: 'Application Icon',
			button: {
				text: 'Select Icon'
			},
			multiple: false  // Set this to true to allow multiple files to be selected
		})
		.on("select", function() {
			var attachment = amppwaMediaUploader.state().get('selection').first().toJSON();
			$(".amppwa-icon").val(attachment.url);
		})
		.open();
	});
	$(".amppwa-splash-icon-upload").click(function(e) {	// Splash Screen Icon upload
		e.preventDefault();
		var amppwaMediaUploader = wp.media({
			title: 'Splash Screen Icon',
			button: {
				text: 'Select Icon'
			},
			multiple: false  // Set this to true to allow multiple files to be selected
		})
		.on("select", function() {
			var attachment = amppwaMediaUploader.state().get('selection').first().toJSON();
			$(".amppwa-splash-icon").val(attachment.url);
		})
		.open();
	});

	$(".amppwa-tabs a").click(function(e){
		var href = $(this).attr('href');                
		var currentTab = getParameterByName('tab',href);
		if(!currentTab){
			currentTab = "dashboard";
		}
		$(this).siblings().removeClass("nav-tab-active");
		$(this).addClass("nav-tab-active");
		$(".form-wrap").find(".saswp-"+currentTab).siblings().hide();
		$(".form-wrap .saswp-"+currentTab).show();
		window.history.pushState("", "", href);
		return false;
	});                
    //Settings page jquery starts here            
    $(".checkbox-input").change(function(){
          var id = $(this).attr("id");         
                  switch(id){
                      case 'sd-for-wordpress-checkbox':  
                          
                          if ($(this).is(':checked')) {              
                            $("#sd-for-wordpress").val(1);  
                          }else{
                            $("#sd-for-wordpress").val(0);  
                          }                          
                          break;
                      case 'sd-for-ampforwp-checkbox':
                          
                          if ($(this).is(':checked')) {              
                            $("#sd-for-ampforwp").val(1);  
                          }else{
                            $("#sd-for-ampforwp").val(0);  
                          }
                      break;
                      case 'sd-for-ampforwp-with-scheme-checkbox':
                          
                        if ($(this).is(':checked')) {              
                          $("#sd-for-ampforwp-with-scheme-app").val(1);
                          $("#sd-for-ampforwp").parent().parent('li').hide();  
                        }else{
                          $("#sd-for-ampforwp-with-scheme-app").val(0);
                          $("#sd-for-ampforwp").parent().parent('li').show();
                        }
                      break;
                      case 'sd_kb_contact_1_checkbox':
                          
                        if ($(this).is(':checked')) {              
                         $("#sd_kb_contact_1").val(1); 
                         $("#sd_kb_telephone, #sd_contact_type").parent().parent('li').show(); 
                       }else{
                         $("#sd_kb_contact_1").val(0);  
                         $("#sd_kb_telephone, #sd_contact_type").parent().parent('li').hide(); 
                       }
                      break;
                      case 'sd-logo-dimensions-ampforwp-check':
                          
                        if ($(this).is(':checked')) {              
                           $("#sd-logo-dimensions-ampforwp").val(1);  
                           $("#sd-logo-width-ampforwp, #sd-logo-height-ampforwp").parent().parent('li').show();
                         }else{
                           $("#sd-logo-dimensions-ampforwp").val(0);            
                           $("#sd-logo-width-ampforwp, #sd-logo-height-ampforwp").parent().parent('li').hide();
                         }
                      break;
                      case 'archive_schema_checkbox':
                          
                            if ($(this).is(':checked')) {              
                                $("#archive_schema").val(1);             
                              }else{
                                $("#archive_schema").val(0);           
                              }
                      break;
                      case 'breadcrumb_schema_checkbox':
                          
                            if ($(this).is(':checked')) {              
                              $("#breadcrumb_schema").val(1);             
                            }else{
                              $("#breadcrumb_schema").val(0);           
                            }
                      break;                                           
                      default:
                          break;
                  }
                             
         }).change();
        
         $("#sd_kb_type").change(function(){
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
			title: 'Application Icon',
			button: {
				text: 'Select Icon'
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
