<?php
add_action('wp_head', 'saswp_data_generator');
function saswp_data_generator() {
   global $sd_data;	        
   
   $output ='';
   $contact_page_output =   saswp_contact_page_output();  	
   $about_page_output   =  saswp_about_page_output();
   $author_output       = saswp_author_output();
   $archive_output      = saswp_archive_output();
   $kb_website_output   = saswp_kb_website_output();
   $schema_breadcrumb_output = saswp_schema_breadcrumb_output($sd_data);
   $schema_output       = saswp_schema_output();
   $kb_schema_output    = saswp_kb_schema_output();
   
	if( (  1 == $sd_data['saswp-for-wordpress'] && saswp_non_amp() ) || ( 1 == $sd_data['saswp-for-amp'] && !saswp_non_amp() ) ) {
		
			
			$output .= '<!-- Schema And Structured Data For WP v'.SASWP_VERSION.' - -->';
			$output .= "\n";
                        $output .= '<script type="application/ld+json">'; 
                        $output .= "\n\n";
                        if(!empty($contact_page_output)){
                        $output .= "//Contact page Schema\n";    
                        $output .= $contact_page_output; 
                        $output .= "\n\n";
                        }			                        
                        if(!empty($about_page_output)){
                        $output .= "//About page Schema\n"; 
                        $output .= $about_page_output;    
                        $output .= "\n\n";
                        }                        
                        if(!empty($author_output)){
                        $output .= "//Author Schema\n";     
                        $output .= $author_output; 
                        $output .= "\n\n";
                        }
                      
                        if(!empty($archive_output)){
                        $output .= "//Archive Schema\n";     
                        $output .= $archive_output;   
                        $output .= "\n\n";
                        }                        
                        if(!empty($kb_website_output)){
                        $output .= "//Website Schema\n";     
                        $output .= $kb_website_output;  
                        $output .= "\n\n";
                        }                       
                        if(!empty($schema_breadcrumb_output)){
                        $output .= "//Breadcrumbs navigation Schema\n";     
                        $output .= $schema_breadcrumb_output;   
                        $output .= "\n\n";
                        }
                        
                        if(!empty($schema_output)){
                        $output .= "// Type Schmea\n";         
                        $output .= $schema_output; 
                        $output .= "\n\n";
                        }
                        
                        if(!empty($kb_schema_output)){
                        $output .= "//Organization Schema\n";    
                        $output .= $kb_schema_output;  
                         $output .= "\n\n";
                        }                       
                        $output .= '</script>';
			$output .= "\n\n";
		
	}
	echo $output;
}
add_action('wp', function(){
	if( saswp_non_amp() ){
		return;
	}
	remove_filter( 'the_content', 'prefix_insert_post_ads' );
});

add_filter('the_content', 'saswp_paywall_data_for_login');
function saswp_paywall_data_for_login($content){
	if( saswp_non_amp() ){
		return $content;
	}
	remove_filter('the_content', 'MeprAppCtrl::page_route', 60);
	
	$schemaConditionals = saswp_get_all_schema_posts();
	if(!$schemaConditionals){
		return $content;
	}else{
		$schema_options = $schemaConditionals['schema_options'];		
		if($schema_options['paywall_class_name']!=''){
			$className = $schema_options['paywall_class_name'];
		}
		if(strpos($content, '<!--more-->')!==false && !is_user_logged_in()){
			global $wp;
			$redirect =  home_url( $wp->request );
			$breakedContent = explode("<!--more-->", $content);
			$content = $breakedContent[0].'<a href="'.wp_login_url( $redirect ) .'">Login</a>';
		}elseif(strpos($content, '<!--more-->')!==false && is_user_logged_in()){
			global $wp;
			$redirect =  home_url( $wp->request );
			$breakedContent = explode("<!--more-->", $content);
			$content = $breakedContent[0].'<div class="'.$className.'">'.$breakedContent[1].'</div>';
		}
	}
	return $content;
}
add_filter('memberpress_form_update', function($form){
	if( !saswp_non_amp() ){
		add_action('amp_post_template_css',function(){
			echo '.amp-mem-login{background-color: #fef5c4;padding: 13px 30px 9px 30px;}';
		},11); 
		global $wp;
		$redirect =  home_url( $wp->request );
		$form = '<a class="amp-mem-login" href="'.wp_login_url( $redirect ) .'">Login</a>';
	}
	return $form;
});