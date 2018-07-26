function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}


jQuery(document).ready(function($){
    $('.ampforwp-pwa-colorpicker').wpColorPicker();	// Color picker
	$('.ampforwp-pwa-icon-upload').click(function(e) {	// Application Icon upload
		e.preventDefault();
		var amppwaMediaUploader = wp.media({
			title: 'Application Icon',
			button: {
				text: 'Select Icon'
			},
			multiple: false  // Set this to true to allow multiple files to be selected
		})
		.on('select', function() {
			var attachment = amppwaMediaUploader.state().get('selection').first().toJSON();
			$('.amppwa-icon').val(attachment.url);
		})
		.open();
	});
	$('.amppwa-splash-icon-upload').click(function(e) {	// Splash Screen Icon upload
		e.preventDefault();
		var amppwaMediaUploader = wp.media({
			title: 'Splash Screen Icon',
			button: {
				text: 'Select Icon'
			},
			multiple: false  // Set this to true to allow multiple files to be selected
		})
		.on('select', function() {
			var attachment = amppwaMediaUploader.state().get('selection').first().toJSON();
			$('.amppwa-splash-icon').val(attachment.url);
		})
		.open();
	});

	$('.amppwa-tabs a').click(function(e){
		var href = $(this).attr('href');                
		var currentTab = getParameterByName('tab',href);
		if(!currentTab){
			currentTab = 'dashboard';
		}
		$(this).siblings().removeClass('nav-tab-active');
		$(this).addClass('nav-tab-active');
		$('.form-wrap').find('.saswp-'+currentTab).siblings().hide();
		$('.form-wrap .saswp-'+currentTab).show();
		window.history.pushState("", "", href);

		return false;
	});
        
     $("#sd-for-wordpress-checkbox").change(function(){
        
         if ($(this).is(':checked')) {              
           $("#sd-for-wordpress").val(1);  
         }else{
           $("#sd-for-wordpress").val(0);  
         }
     });  
     $("#sd-for-ampforwp-checkbox").change(function(){
        
         if ($(this).is(':checked')) {              
           $("#sd-for-ampforwp").val(1);  
         }else{
           $("#sd-for-ampforwp").val(0);  
         }
     }); 
     $("#sd-for-ampforwp-with-scheme-app").change(function(){
        
         if ($(this).is(':checked')) {              
           
           $("#sd-for-ampforwp").parent().parent('tr').hide();  
         }else{
           
           $("#sd-for-ampforwp").parent().parent('tr').show();
         }
     }).change();
     $("#sd_kb_contact_1_checkbox").change(function(){
        
         if ($(this).is(':checked')) {              
           $("#sd_kb_contact_1").val(1); 
           $(".contact-details-fields").show();  
         }else{
           $("#sd_kb_contact_1").val(0);  
           $(".contact-details-fields").hide(); 
         }
     });
     $("#sd-logo-dimensions-ampforwp-checkbox").change(function(){
        
         if ($(this).is(':checked')) {              
           $("#sd-logo-dimensions-ampforwp").val(1);  
           $(".saswp-custom-logo-size").show();
         }else{
           $("#sd-logo-dimensions-ampforwp").val(0);            
           $(".saswp-custom-logo-size").hide();
         }
     });
     
     $("#archive_schema-checkbox").change(function(){
        
         if ($(this).is(':checked')) {              
           $("#archive_schema").val(1);             
         }else{
           $("#archive_schema").val(0);           
         }
     });
     $("#breadcrumb_schema-checkbox").change(function(){
        
         if ($(this).is(':checked')) {              
           $("#breadcrumb_schema").val(1);             
         }else{
           $("#breadcrumb_schema").val(0);           
         }
     });
     $("#sd_kb_type-select").change(function(){
        var datatype = $(this).val();
        if(datatype ==="Organization"){
            $('.organization-div').show();
            $('.person-div').hide();
        }
        if(datatype ==="Person"){
            $('.organization-div').hide();
            $('.person-div').show();    
        }
        
         
     });
     
     $('.saswp-pwa-icon-upload').click(function(e) {	// Application Icon upload
		e.preventDefault();
		var saswpMediaUploader = wp.media({
			title: 'Application Icon',
			button: {
				text: 'Select Icon'
			},
			multiple: false  // Set this to true to allow multiple files to be selected
		})
		.on('select', function() {
			var attachment = saswpMediaUploader.state().get('selection').first().toJSON();
			$('.saswp-icon').val(attachment.url);
		})
		.open();
	});
        $('.saswp-person-image-upload').click(function(e) {	// Application Icon upload
		e.preventDefault();
		var saswpMediaUploader = wp.media({
			title: 'Person Image',
			button: {
				text: 'Select Image'
			},
			multiple: false  // Set this to true to allow multiple files to be selected
		})
		.on('select', function() {
			var attachment = saswpMediaUploader.state().get('selection').first().toJSON();
			$('.saswp-image').val(attachment.url);
		})
		.open();
	});
        $('.saswp-data-logo-ampforwp-upload').click(function(e) {	// Application Icon upload
		e.preventDefault();
		var saswpMediaUploader = wp.media({
			title: 'Logo',
			button: {
				text: 'Select Logo'
			},
			multiple: false  // Set this to true to allow multiple files to be selected
		})
		.on('select', function() {
			var attachment = saswpMediaUploader.state().get('selection').first().toJSON();
			$('.saswp-logo-ampforwp').val(attachment.url);
		})
		.open();
	});
        $('.saswp-sd_default_image-upload').click(function(e) {	// Application Icon upload
		e.preventDefault();
		var saswpMediaUploader = wp.media({
			title: 'Default Image',
			button: {
				text: 'Select Image'
			},
			multiple: false  // Set this to true to allow multiple files to be selected
		})
		.on('select', function() {
			var attachment = saswpMediaUploader.state().get('selection').first().toJSON();
			$('.saswp-sd_default_image').val(attachment.url);
		})
		.open();
	});
        $('.saswp-sd_default_video_thumbnail-upload').click(function(e) {	// Application Icon upload
		e.preventDefault();
		var saswpMediaUploader = wp.media({
			title: 'Default Image',
			button: {
				text: 'Select Image'
			},
			multiple: false  // Set this to true to allow multiple files to be selected
		})
		.on('select', function() {
			var attachment = saswpMediaUploader.state().get('selection').first().toJSON();
			$('.saswp-sd_default_video_thumbnail').val(attachment.url);
		})
		.open();
	});
        
});
