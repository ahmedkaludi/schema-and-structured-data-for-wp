<?php
function saswp_remove_amp_default_structure_data($metadata){
    return '';
}
add_filter( 'amp_init', 'saswp_structured_data' );
function saswp_structured_data()
{		
	add_action( 'amp_post_template_head' , 'saswp_data_generator' );	
	remove_action( 'amp_post_template_head', 'amp_post_template_add_schemaorg_metadata',99,1);
}
add_action('wp_head', 'saswp_data_generator');
function saswp_data_generator() {
   global $sd_data;
   global $post;
   $output ='';
   $contact_page_output      = saswp_contact_page_output();  	
   $about_page_output        = saswp_about_page_output();     
   $author_output            = saswp_author_output();
   $archive_output           = saswp_archive_output();
   $kb_website_output        = saswp_kb_website_output();   
   $schema_breadcrumb_output = saswp_schema_breadcrumb_output($sd_data);
   
  
   $post_specific_enable = $option = get_option('modify_schema_post_enable_'.$post->ID);
   
   if($post_specific_enable =='enable'){
   $schema_output            = saswp_post_specific_schema_output();      
   }else{
   $schema_output            = saswp_schema_output();    
   }             
   if($schema_output || $schema_breadcrumb_output || $kb_website_output || $archive_output || $author_output || $about_page_output || $contact_page_output){       
      add_filter( 'amp_post_template_metadata', 'saswp_remove_amp_default_structure_data');
   }
      
   $kb_schema_output   = saswp_kb_schema_output();
   
	if( ( saswp_remove_warnings($sd_data, 'saswp-for-wordpress', 'saswp_string') =='' ||  1 == saswp_remove_warnings($sd_data, 'saswp-for-wordpress', 'saswp_string') && saswp_non_amp() ) || ( 1 == saswp_remove_warnings($sd_data, 'saswp-for-amp', 'saswp_string') && !saswp_non_amp() ) ) {
								
                        
                        if(!empty($contact_page_output)){
                          
                        $output .= $contact_page_output; 
                        $output .= ",";
                        $output .= "\n\n";
                        
                        }			                        
                        if(!empty($about_page_output)){
                        
                        $output .= $about_page_output;    
                        $output .= ",";
                        $output .= "\n\n";
                        }                        
                        if(!empty($author_output)){
                           
                        $output .= $author_output; 
                        $output .= ",";
                        $output .= "\n\n";
                        }                                              
                        if(!empty($archive_output)){
                        
                        $output .= $archive_output;   
                        $output .= ",";
                        $output .= "\n\n";
                        }                        
                        if(!empty($kb_website_output)){
                        
                        $output .= $kb_website_output;  
                        $output .= ",";
                        $output .= "\n\n";
                        }                       
                        if(!empty($schema_breadcrumb_output)){
                        
                        $output .= $schema_breadcrumb_output;   
                        $output .= ",";
                        $output .= "\n\n";
                        }                        
                        if(!empty($schema_output)){                            
                        foreach($schema_output as $schema){
                        $schema = json_encode($schema);
                        $output .= $schema; 
                        $output .= ",";
                        $output .= "\n\n";   
                        }                            
                        }                        
                        if(!empty($kb_schema_output)){
                            
                        $output .= $kb_schema_output;
                        $output .= ",";
                        
                        }                       
                        			              		
	}
        $stroutput = '['. $output. ']';
        $filter_string = str_replace(',]', ']',$stroutput);
        
        echo '<!-- Schema & Structured Data For WP v'.esc_attr(SASWP_VERSION).' - -->';
	echo "\n";
        echo '<script type="application/ld+json">'; 
        echo "\n";       
	echo html_entity_decode(esc_html($filter_string));       
        echo "\n";
        echo '</script>';
        echo "\n\n";
}

add_filter('the_content', 'saswp_paywall_data_for_login');
function saswp_paywall_data_for_login($content){
	if( saswp_non_amp() ){
		return $content;
	}
	remove_filter('the_content', 'MeprAppCtrl::page_route', 60);	
	$Conditionals = saswp_get_all_schema_posts();     
        
	if(!$Conditionals){
		return $content;
	}else{
               
                $paywallenable ='';
                $className ='paywall';
                foreach($Conditionals as $schemaConditionals){
                $schema_options = $schemaConditionals['schema_options'];    
               
                if(isset($schema_options['paywall_class_name'])){
                $className = $schema_options['paywall_class_name'];                                 
                }
                if(isset($schema_options['notAccessibleForFree'])){               
                $paywallenable = $schema_options['notAccessibleForFree'];
                break;
                }                
                }                
                if($paywallenable){
		if(strpos($content, '<!--more-->')!==false && !is_user_logged_in()){
			global $wp;
			$redirect =  home_url( $wp->request );
			$breakedContent = explode("<!--more-->", $content);
			$content = $breakedContent[0].'<a href="'.wp_login_url( $redirect ) .'">'.esc_html__( 'Login', 'schema-and-structured-data-for-wp' ).'</a>';
		}elseif(strpos($content, '<!--more-->')!==false && is_user_logged_in()){
			global $wp;
			$redirect =  home_url( $wp->request );
			$breakedContent = explode("<!--more-->", $content);
			$content = $breakedContent[0].'<div class="'.$className.'">'.$breakedContent[1].'</div>';
		}
                }
                
	}
	return $content;
}

add_filter('memberpress_form_update', 'saswp_memberpress_form_update'); 
        
function saswp_memberpress_form_update($form){
	if( !saswp_non_amp() ){
		add_action('amp_post_template_css',function(){
			echo '.amp-mem-login{background-color: #fef5c4;padding: 13px 30px 9px 30px;}';
		},11); 
		global $wp;
		$redirect =  home_url( $wp->request );
		$form = '<a class="amp-mem-login" href="'.wp_login_url( $redirect ) .'">'.esc_html__( 'Login', 'schema-and-structured-data-for-wp' ).'</a>';
	}
	return $form;
}

