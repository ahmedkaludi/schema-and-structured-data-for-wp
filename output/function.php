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


/**
 * Function to show all the schema markup in the page head
 * @global type $sd_data
 * @global type json array
 */
function saswp_data_generator() {
    
    
   global $sd_data;
   global $post;
   
   $output                   = '';
   $post_specific_enable     = '';
   $kb_website_output        = '';
   $kb_schema_output         = '';
   $site_navigation          = '';
   
   
   $site_navigation          = saswp_site_navigation_output();     
   $contact_page_output      = saswp_contact_page_output();  	
   $about_page_output        = saswp_about_page_output();      
   $author_output            = saswp_author_output();
   $archive_output           = saswp_archive_output();
   
   $schema_breadcrumb_output = saswp_schema_breadcrumb_output($sd_data);  
   
   if(saswp_remove_warnings($sd_data, 'saswp-yoast', 'saswp_string') != 1){
       
       $kb_website_output        = saswp_kb_website_output();      
       $kb_schema_output         = saswp_kb_schema_output();       
       
   }         
   
   if(is_singular()){
       
       $post_specific_enable  = get_option('modify_schema_post_enable_'.$post->ID);
       
   }
   
   if($post_specific_enable =='enable'){
       
       $schema_output            = saswp_post_specific_schema_output();  
   
   }else{
       
       $schema_output            = saswp_schema_output();    
       
   }   
 
   
   if($schema_output || $schema_breadcrumb_output || $kb_website_output || $archive_output || $author_output || $about_page_output || $contact_page_output){       
      add_filter( 'amp_post_template_metadata', 'saswp_remove_amp_default_structure_data');
   }         
   
	if( (   saswp_remove_warnings($sd_data, 'saswp-for-wordpress', 'saswp_string') =='' 
            ||   1 == saswp_remove_warnings($sd_data, 'saswp-for-wordpress', 'saswp_string') && saswp_non_amp() ) 
            || ( 1 == saswp_remove_warnings($sd_data, 'saswp-for-amp', 'saswp_string') && !saswp_non_amp() ) ) {
								
                        
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
                        if(!empty($site_navigation)){
                        
                        $site_navigation = json_encode($site_navigation);
                            
                        $output .= $site_navigation;   
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
        
        $stroutput = '['. trim($output). ']';
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
                $className     ='paywall';
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

/**
 * Function to remove the undefined index notices
 * @param type $data
 * @param type $index
 * @param type $type
 * @return string
 */
function saswp_remove_warnings($data, $index, $type){     
    	
                if($type == 'saswp_array'){

                        if(isset($data[$index])){
                                return $data[$index][0];
                        }else{
                                return '';
                        }		
                }

		if($type == 'saswp_string'){
	
                        if(isset($data[$index])){
                                return $data[$index];
                        }else{
                                return '';
                        }		
	        }    
}


/**
 * Gets the total word count and expected time to read the article
 * @return type array
 */
function saswp_reading_time_and_word_count() {

    // Predefined words-per-minute rate.
    $words_per_minute = 225;
    $words_per_second = $words_per_minute / 60;

    // Count the words in the content.
    $word_count      = 0;
    $text            = trim( strip_tags( get_the_content() ) );
    $word_count      = substr_count( "$text ", ' ' );

    // How many seconds (total)?
    $seconds = floor( $word_count / $words_per_second );

    return array('word_count' => $word_count, 'timerequired' => $seconds);
}

/**
 * Extracting the value of star ratings plugins on current post
 * @global type $sd_data
 * @param type $id
 * @return type array
 */
function saswp_extract_kk_star_ratings($id){
        
            global $sd_data;    
            
            if(isset($sd_data['saswp-kk-star-raring']) && $sd_data['saswp-kk-star-raring'] == 1){
               
                $best  = get_option('kksr_stars');
                $score = get_post_meta($id, '_kksr_ratings', true) ? ((int) get_post_meta($id, '_kksr_ratings', true)) : 0;
                $votes = get_post_meta($id, '_kksr_casts', true) ? ((int) get_post_meta($id, '_kksr_casts', true)) : 0;
                $avg   = $score && $votes ? round((float)(($score/$votes)*($best/5)), 1) : 0;
                $per   = $score && $votes ? round((float)((($score/$votes)/5)*100), 2) : 0;                
                
                if($votes>0){
                    
                    return compact('best', 'score', 'votes', 'avg', 'per');    
                    
                }else{
                    
                    return array();    
                    
                }
                
            }else{
                
                return array();
                
            }                        
       }

/**
 * Gets all the comments of current post
 * @param type $post_id
 * @return type array
 */       
function saswp_get_comments($post_id){
    
        $comment_count = get_comments_number( $post_id );
        
	if ( $comment_count < 1 ) {
		return array();
	}
        $comments = array();
        
        $count	= apply_filters( 'saswp_do_comments', '10'); // default = 10
        
        $post_comments = get_comments( array( 
                                            'post_id' => $post_id,
                                            'number'  => $count, 
                                            'status'  => 'approve',
                                            'type'    => 'comment' 
                                        ) 
                                    );
        
        if ( count( $post_comments ) ) {
            
		foreach ( $post_comments as $comment ) {
                    
			$comments[] = array (
					'@type'       => 'Comment',
					'dateCreated' => $comment->comment_date,
					'description' => $comment->comment_content,
					'author' => array (
						'@type' => 'Person',
						'name'  => $comment->comment_author,
						'url'   => $comment->comment_author_url,
				),
			);
		}
                
		return apply_filters( 'saswp_filter_comments', $comments );
	}
        
}       

/**
 * Function to enqueue AMP script in head
 * @param type $data
 * @return string
 */
function saswp_structure_data_access_scripts($data){
    
	if ( empty( $data['amp_component_scripts']['amp-access'] ) ) {
		$data['amp_component_scripts']['amp-access'] = 'https://cdn.ampproject.org/v0/amp-access-0.1.js';
	}
	if ( empty( $data['amp_component_scripts']['amp-analytics'] ) ) {
		$data['amp_component_scripts']['amp-analytics'] = "https://cdn.ampproject.org/v0/amp-analytics-0.1.js";
	}
	if ( empty( $data['amp_component_scripts']['amp-mustache'] ) ) {
		$data['amp_component_scripts']['amp-mustache'] = "https://cdn.ampproject.org/v0/amp-mustache-0.1.js";
	}
	return $data;
        
}

function sd_is_blog() {
    
    return ( is_author() || is_category() || is_tag() || is_date() || is_home() || is_single() ) && 'post' == get_post_type();
    
}

/**
 * Function to fetch schema's post meta by its id from database using get_post_meta function
 * @param type $schema_id
 * @param type $schema_key
 * @return type array
 */
function saswp_get_schema_data($schema_id, $schema_key){
    
    $details = array();
    
    if($schema_id && $schema_key){
        
            $details = esc_sql ( get_post_meta($schema_id, $schema_key, true));    
     
    }  
    
    return $details;
}

/**
 * Function generates list items for the breadcrumbs schema markup
 * @global type $sd_data
 * @return array
 */
function saswp_list_items_generator(){
    
		global $sd_data;
		$bc_titles = array();
		$bc_links  = array();
                
                if(isset($sd_data['titles'])){		
			$bc_titles = $sd_data['titles'];
		}
		if(isset($sd_data['links'])){
			$bc_links = $sd_data['links'];
		}	
                
                $j = 1;
                $i = 0;
                $breadcrumbslist = array();
                
        if(is_single()){    
            
			if(isset($bc_titles)){      
                            
				for($i=0;$i<sizeof($bc_titles);$i++){
                                    
					$breadcrumbslist[] = array(
								'@type'			=> 'ListItem',
								'position'		=> $j,
								'item'			=> array(
									'@id'		=> $bc_links[$i],
									'name'		=> $bc_titles[$i],
									),
							          );
                                        
                                        $j++;
                        }
                
                     }
               
}
        if(is_page()){

			for($i=0;$i<sizeof($bc_titles);$i++){
                            
				$breadcrumbslist[] = array(
								'@type'			=> 'ListItem',
								'position'		=> $j,
								'item'			=> array(
									'@id'		=> $bc_links[$i],
									'name'		=> $bc_titles[$i],
									),
							);
                                
		$j++;
                
        }

}
        if(is_archive()){

	for($i=0;$i<sizeof($bc_titles);$i++){
            
				$breadcrumbslist[] = array(
								        '@type'		=> 'ListItem',
								        'position'	=> $j,
								        'item'		=> array(
									'@id'		=> $bc_links[$i],
									'name'		=> $bc_titles[$i],
									),
							);
		$j++;
                
		}
}

       return $breadcrumbslist;
}


/**
 * Remove the default WooCommerce 3 JSON/LD structured data
 * @global type $sd_data
 */
function saswp_remove_woocommerce_default_structured_data() {
        
    global $sd_data;
    
    if(isset($sd_data['saswp-woocommerce']) && $sd_data['saswp-woocommerce'] == 1 && is_plugin_active('woocommerce/woocommerce.php')){
     
        remove_action( 'wp_footer', array( WC()->structured_data, 'output_structured_data' ), 10 ); // This removes structured data from all frontend pages
        remove_action( 'woocommerce_email_order_details', array( WC()->structured_data, 'output_email_structured_data' ), 30 ); // This removes structured data from all Emails sent by WooCommerce
        
        
    }                

}

add_action( 'init', 'saswp_remove_woocommerce_default_structured_data' );