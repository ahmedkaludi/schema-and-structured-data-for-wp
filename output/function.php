<?php
/**
 * Function Page
 *
 * @author   Magazine3
 * @category Frontend
 * @path  output/function
 * @Version 1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

add_filter( 'amp_init', 'saswp_schema_markup_hook_on_init' );
add_action( 'init', 'saswp_schema_markup_hook_on_init');

function saswp_schema_markup_hook_on_init() {
        
        if(!is_admin()){
            
            global $sd_data;
        
            if(isset($sd_data['saswp-markup-footer']) && $sd_data['saswp-markup-footer'] == 1){
               add_action( 'wp_footer', 'saswp_schema_markup_output');    
               add_action( 'amp_post_template_footer' , 'saswp_schema_markup_output' );
            }else{
               add_action('wp_head', 'saswp_schema_markup_output');  
               add_action( 'amp_post_template_head' , 'saswp_schema_markup_output' );
            }               
            
            add_action('cooked_amp_head', 'saswp_schema_markup_output');
                                    
            remove_action( 'amp_post_template_head', 'amp_post_template_add_schemaorg_metadata',99,1);
            remove_action( 'amp_post_template_footer', 'amp_post_template_add_schemaorg_metadata',99,1);  
            remove_action('wp_footer', 'orbital_markup_site');            
            add_filter('hunch_schema_markup', '__return_false');              
                        
            if(class_exists('BSF_AIOSRS_Pro_Markup')){
                
                remove_action( 'wp_head', array( BSF_AIOSRS_Pro_Markup::get_instance(), 'schema_markup' ),10);
                remove_action( 'wp_head', array( BSF_AIOSRS_Pro_Markup::get_instance(), 'global_schemas_markup' ),10);
                remove_action( 'wp_footer', array( BSF_AIOSRS_Pro_Markup::get_instance(), 'schema_markup' ),10);
                remove_action( 'wp_footer', array( BSF_AIOSRS_Pro_Markup::get_instance(), 'global_schemas_markup' ),10);
                
            }
            
            if(isset($sd_data['saswp-wp-recipe-maker']) && $sd_data['saswp-wp-recipe-maker'] == 1){
                add_filter( 'wprm_recipe_metadata', '__return_false' );            
            }
                                    
            if(isset($sd_data['saswp-microdata-cleanup']) && $sd_data['saswp-microdata-cleanup'] == 1){                
                ob_start("saswp_remove_microdata");                
            }
                                                                                                           
        }                       
}

/**
 * This function collects all the schema markups and show them at one place either header or footer
 * @global type $sd_data
 * @global type json array
 */
function saswp_schema_markup_output() {
       
        global $sd_data;
        global $post;
       
        $custom_markup            = '';
        $output                   = '';
        $post_specific_enable     = '';
        $schema_output            = array();
        $kb_schema_output         = array(); 
        $item_list                = array();
        $collection_page          = array(); 
        $blog_page                = array();          
                
        $gutenberg_how_to         = saswp_gutenberg_how_to_schema(); 
        $gutenberg_faq            = saswp_gutenberg_faq_schema();        
        $woo_cat_schema           = saswp_woocommerce_category_schema();  
        $site_navigation          = saswp_site_navigation_output();     
        $contact_page_output      = saswp_contact_page_output();  	
        $about_page_output        = saswp_about_page_output();      
        $author_output            = saswp_author_output();
        $archive_output           = saswp_archive_output();
        
        if($archive_output){
            
            if(empty($woo_cat_schema)){
                $item_list            = $archive_output[0];
            }
            
            $collection_page          = $archive_output[1]; 
            $blog_page                = $archive_output[2]; 
        }
                     
        $schema_breadcrumb_output = saswp_schema_breadcrumb_output();                      
        $kb_website_output        = saswp_kb_website_output();      
        
        if((is_home() || is_front_page() || ( function_exists('ampforwp_is_home') && ampforwp_is_home())) || isset($sd_data['saswp-defragment']) && $sd_data['saswp-defragment'] == 1 ){
               $kb_schema_output         = saswp_kb_schema_output();
        }
                 
        if(is_singular()){

            $post_specific_enable  = get_option('modify_schema_post_enable_'.esc_attr($post->ID));
            $custom_markup         = get_post_meta($post->ID, 'saswp_custom_schema_field', true);

        }
   
        if($post_specific_enable =='enable'){

            $schema_output            = saswp_post_specific_schema_output();  

        }else{
                       
            $schema_output            = saswp_schema_output();              
                       
        }                   
	if(saswp_global_option()) {
		                                    
                        if(!empty($contact_page_output)){
                          
                            $output .= saswp_json_print_format($contact_page_output); 
                            $output .= ",";
                            $output .= "\n\n";                        
                        }			                        
                        if(!empty($about_page_output)){
                        
                            $output .= saswp_json_print_format($about_page_output);    
                            $output .= ",";
                            $output .= "\n\n";
                        }                        
                        if(!empty($author_output)){
                           
                            $output .= saswp_json_print_format($author_output); 
                            $output .= ",";
                            $output .= "\n\n";
                        }                                              
                        if(!empty($collection_page)){
                        
                            $output .= saswp_json_print_format($collection_page);   
                            $output .= ",";
                            $output .= "\n\n";
                        }
                        if(!empty($item_list)){
                        
                            $output .= saswp_json_print_format($item_list);   
                            $output .= ",";
                            $output .= "\n\n";
                        }
                        if(!empty($woo_cat_schema)){
                        
                            $output .= saswp_json_print_format($woo_cat_schema);   
                            $output .= ",";
                            $output .= "\n\n";
                        }
                        if(!empty($gutenberg_how_to)){
                        
                            $output .= saswp_json_print_format($gutenberg_how_to);   
                            $output .= ",";
                            $output .= "\n\n";
                        }
                        if(!empty($gutenberg_faq)){
                        
                            $output .= saswp_json_print_format($gutenberg_faq);   
                            $output .= ",";
                            $output .= "\n\n";
                        }
                        if(!empty($blog_page)){
                        
                            $output .= saswp_json_print_format($blog_page);   
                            $output .= ",";
                            $output .= "\n\n";
                        }
                                    
            if(isset($sd_data['saswp-defragment']) && $sd_data['saswp-defragment'] == 1){
            
                $output_schema_type_id = array();
                
                if(!empty($schema_output)){
                    
                foreach($schema_output as $soutput){
            
                    $output_schema_type_id[] = $soutput['@type'];
                    
                    if($soutput['@type'] == 'BlogPosting'|| $soutput['@type'] == 'Article' || $soutput['@type'] == 'TechArticle' || $soutput['@type'] == 'NewsArticle'){
                        
                    
                    $final_output = array();
                    $object   = new saswp_output_service();
                    $webpage  = $object->saswp_schema_markup_generator('WebPage');
                    
                        unset($soutput['@context']);                   
                        unset($schema_breadcrumb_output['@context']);
                        unset($webpage['mainEntity']);
                        unset($kb_schema_output['@context']);                        
                        unset($kb_website_output['@context']);
                        
                        $kb_schema_output['@type'] = 'Organization';    
                    
                     if($webpage){
                    
                         $soutput['isPartOf'] = array(
                            '@id' => $webpage['@id']
                        );
                         
                         $webpage['primaryImageOfPage'] = array(
                             '@id' => saswp_get_permalink().'#primaryimage'
                         );
                         
                         if(array_key_exists('@graph', $site_navigation)){                             
                             unset($site_navigation['@context']);                                                       
                             $webpage['mainContentOfPage'] = array($site_navigation['@graph']);
                         }                         
                         
                     }       
                                        
                    $soutput['mainEntityOfPage'] = $webpage['@id'];
                                        
                    if($kb_website_output){
                    
                        $webpage['isPartOf'] = array(
                        '@id' => $kb_website_output['@id']
                        );
                        
                    }
                                        
                    if($schema_breadcrumb_output){
                        $webpage['breadcrumb'] = array(
                        '@id' => $schema_breadcrumb_output['@id']
                    );
                    }
                    
                    if($kb_schema_output){
                    
                        if($kb_website_output){
                            
                            $kb_website_output['publisher'] = array(
                            '@id' => $kb_schema_output['@id']
                            );                            
                        }
                        
                        $soutput['publisher'] = array(
                            '@id' => $kb_schema_output['@id']
                        );
                        
                    }
                                        
                    $final_output['@context']   = saswp_context_url();

                    $final_output['@graph'][]   = $kb_schema_output;
                    $final_output['@graph'][]   = $kb_website_output;

                    $final_output['@graph'][]   = $webpage;
                    
                    $final_output['@graph'][]   = $schema_breadcrumb_output;
                    
                    $final_output['@graph'][]   = $soutput;
                        
                    $schema = saswp_json_print_format($final_output);
                    $output .= $schema; 
                    $output .= ",";
                    $output .= "\n\n";     
                    
                    }else{
                        
                        $schema = saswp_json_print_format($soutput);
                        $output .= $schema; 
                        $output .= ",";
                        $output .= "\n\n"; 
                        
                    }
                                                                                                          
            }
            }                                 
                if(in_array('BlogPosting', $output_schema_type_id) || in_array('Article', $output_schema_type_id) || in_array('TechArticle', $output_schema_type_id) || in_array('NewsArticle', $output_schema_type_id) ){                                                                                            
                }else{
                    if(!empty($kb_website_output)){
                        
                            $output .= saswp_json_print_format($kb_website_output);  
                            $output .= ",";
                            $output .= "\n\n";
                        }
                    if(!empty($schema_breadcrumb_output)){
                        
                            $output .= saswp_json_print_format($schema_breadcrumb_output);   
                            $output .= ",";
                            $output .= "\n\n";
                        }
                    if(!empty($kb_schema_output)){
                            
                            $output .= saswp_json_print_format($kb_schema_output);
                            $output .= ",";                        
                        }   
                }
            
                
            }else{
                          
                        if(!empty($site_navigation)){
                                                                            
                            $output .= saswp_json_print_format($site_navigation);   
                            $output .= ",";
                            $output .= "\n\n";                        
                        }
                        
                        if(!empty($kb_website_output)){
                        
                            $output .= saswp_json_print_format($kb_website_output);  
                            $output .= ",";
                            $output .= "\n\n";
                        }                         
                        if(!empty($schema_breadcrumb_output)){
                        
                            $output .= saswp_json_print_format($schema_breadcrumb_output);   
                            $output .= ",";
                            $output .= "\n\n";
                        }                        
                        if(!empty($schema_output)){ 
                            
                            foreach($schema_output as $schema){
                                
                                $schema = saswp_json_print_format($schema);
                                $output .= $schema; 
                                $output .= ",";
                                $output .= "\n\n";   
                                
                            }                            
                        }                        
                        if(!empty($kb_schema_output)){
                            
                            $output .= saswp_json_print_format($kb_schema_output);
                            $output .= ",";                        
                        }       
                
            }
                        
            if($custom_markup){                                     
                        $result = json_decode($custom_markup);
                    
                        if($result != false){
                        
                            echo "\n";
                            echo '<!-- Schema & Structured Data For WP Custom Markup v'.esc_attr(SASWP_VERSION).' - -->';
                            echo "\n";
                            echo '<script type="application/ld+json" class="saswp-schema-markup-output">'; 
                            echo "\n";       
                            echo $custom_markup;       
                            echo "\n";
                            echo '</script>';
                            echo "\n\n";
                            
                        }
                                                
                                                                      
            }
            
                                    			              		
	}
                        
        if($output){
            
            $stroutput = '['. trim($output). ']';
            $filter_string = str_replace(',]', ']',$stroutput);   
            echo "\n";
            echo '<!-- Schema & Structured Data For WP v'.esc_attr(SASWP_VERSION).' - -->';
            echo "\n";
            echo '<script type="application/ld+json" class="saswp-schema-markup-output">'; 
            echo "\n";       
            echo $filter_string;       
            echo "\n";
            echo '</script>';
            echo "\n\n";
        }
                
}

add_filter('the_content', 'saswp_paywall_data_for_login');

function saswp_paywall_data_for_login($content){
    
        global $wp;
        
	if( saswp_non_amp() ){
            
		return $content;
                
	}
        
	remove_filter('the_content', 'MeprAppCtrl::page_route', 60);	
	$Conditionals = saswp_get_all_schema_posts();     
        
	if(!$Conditionals){
		return $content;
	}else{
               
                $paywallenable = '';
                $className     = 'paywall';
                
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
                    			
			$redirect       =  home_url( $wp->request );
			$breakedContent = explode("<!--more-->", $content);
			$content        = $breakedContent[0].'<a href="'.esc_url(wp_login_url( $redirect )) .'">'.esc_html__( 'Login', 'schema-and-structured-data-for-wp' ).'</a>';
                        
		}elseif(strpos($content, '<!--more-->')!==false && is_user_logged_in()){
                    			
			$redirect       =  home_url( $wp->request );
			$breakedContent = explode("<!--more-->", $content);
			$content        = $breakedContent[0].'<div class="'.esc_attr($className).'">'.$breakedContent[1].'</div>';
                        
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
		$form = '<a class="amp-mem-login" href="'.esc_url(wp_login_url( $redirect )) .'">'.esc_html__( 'Login', 'schema-and-structured-data-for-wp' ).'</a>';
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
                                return esc_attr($data[$index][0]);
                        }else{
                                return '';
                        }		
                }

		if($type == 'saswp_string'){
	
                        if(isset($data[$index])){
                                return esc_attr($data[$index]);
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
    $text            = trim( strip_tags( @get_the_content() ) );
    $word_count      = substr_count( "$text ", ' ' );

    // How many seconds (total)?
    $seconds = floor( $word_count / $words_per_second );

    return array('word_count' => esc_attr($word_count), 'timerequired' => esc_attr($seconds));
}

/**
 * Extracting the value of star ratings plugins on current post
 * @global type $sd_data
 * @param type $id
 * @return type array
 */
function saswp_extract_kk_star_ratings(){
        
            global $sd_data;    
            $kk_star_rating = array();
            if(isset($sd_data['saswp-kk-star-raring']) && $sd_data['saswp-kk-star-raring'] == 1 && is_plugin_active('kk-star-ratings/index.php')){
               
                $best  = get_option('kksr_stars');
                $score = get_post_meta(get_the_ID(), '_kksr_ratings', true) ? ((int) get_post_meta(get_the_ID(), '_kksr_ratings', true)) : 0;
                $votes = get_post_meta(get_the_ID(), '_kksr_casts', true) ? ((int) get_post_meta(get_the_ID(), '_kksr_casts', true)) : 0;
                $avg   = $score && $votes ? round((float)(($score/$votes)*($best/5)), 1) : 0;                               
                 
                if($votes>0){
                   
                    $kk_star_rating['@type']       = 'AggregateRating';
                    $kk_star_rating['bestRating']  = $best;
                    $kk_star_rating['ratingCount'] = $votes;
                    $kk_star_rating['ratingValue'] = $avg;                                                           
                    
                    return $kk_star_rating;
                    
                }else{
                    
                    return array();    
                    
                }
                
            }else{
                
                return array();
                
            }                        
       }
       
/**
 * Extracting the value of wp-post-rating ratings plugins on current post
 * @global type $sd_data
 * @param type $id
 * @return type array
 */
function saswp_extract_wp_post_ratings(){
        
            global $sd_data;    
            
            $wp_post_rating_ar = array();
            
            if(isset($sd_data['saswp-wppostratings-raring']) && $sd_data['saswp-wppostratings-raring'] == 1 && is_plugin_active('wp-postratings/wp-postratings.php')){
               
                $best   = (int) get_option( 'postratings_max' );
                $avg   = get_post_meta(get_the_ID(), 'ratings_average', true);
                $votes = get_post_meta(get_the_ID(), 'ratings_users', true);                
                
                if($votes>0){
                    
                    $wp_post_rating_ar['@type']       = 'AggregateRating';
                    $wp_post_rating_ar['bestRating']  = $best;
                    $wp_post_rating_ar['ratingCount'] = $votes;
                    $wp_post_rating_ar['ratingValue'] = $avg;                                                           
                    
                    return $wp_post_rating_ar;
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
					'dateCreated' => esc_html($comment->comment_date),
					'description' => esc_attr($comment->comment_content),
					'author'      => array (
                                                    '@type' => 'Person',
                                                    'name'  => esc_attr($comment->comment_author),
                                                    'url'   => esc_url($comment->comment_author_url),
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

/**
 * Function to fetch schema's post meta by its id from database using get_post_meta function
 * @param type $schema_id
 * @param type $schema_key
 * @return type array
 */
function saswp_get_schema_data($schema_id, $schema_key){
    
    $details = array();
    
    if($schema_id && $schema_key){
        
            $details =  get_post_meta($schema_id, $schema_key, true);    
     
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

			if(!empty($bc_titles) && !empty($bc_links)){      
                            
				for($i=0;$i<sizeof($bc_titles);$i++){
                                    
                                    if(array_key_exists($i, $bc_links) && array_key_exists($i, $bc_titles)){
                                    
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
               
}
        if(is_page()){
                        if(!empty($bc_titles) && !empty($bc_links)){
                            
                            for($i=0;$i<sizeof($bc_titles);$i++){
                            
                                if(array_key_exists($i, $bc_links) && array_key_exists($i, $bc_titles)){
                                 
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
			

}
        if(is_archive()){

         if(!empty($bc_titles) && !empty($bc_links)){
             
             for($i=0;$i<sizeof($bc_titles);$i++){
                 
                    if(array_key_exists($i, $bc_links) && array_key_exists($i, $bc_titles)){
                                               
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
                          
         }               	
}
        
       return $breadcrumbslist;
}

/**
 * Function to format json output
 * @global type $sd_data
 * @param type $output_array
 * @return type json 
 */
function saswp_json_print_format($output_array){
    
    global $sd_data;
    
    if(isset($sd_data['saswp-pretty-print']) && $sd_data['saswp-pretty-print'] == 1){
        return wp_json_encode($output_array, JSON_PRETTY_PRINT);
    }else{
        return wp_json_encode($output_array);
    }
        
}

/**
 * @since 1.8.2
 * It removes all the microdata from the post or page
 * @param type $content
 * @return type string
 */
function saswp_remove_microdata($content){
    
    global $sd_data;
    
    if(saswp_global_option()){
        //Clean MicroData
        $content = preg_replace("/itemtype=(\"?)http(s?):\/\/schema.org\/(Person|Mosque|SearchAction|Church|HinduTemple|LandmarksOrHistoricalBuildings|TouristDestination|TouristAttraction|Place|LocalBusiness|MedicalCondition|VideoObject|AudioObject|Trip|Service|JobPosting|VideoGame|Game|TechArticle|SoftwareApplication|TVSeries|Recipe|Review|HowTo|DiscussionForumPosting|Course|SingleFamilyResidence|House|Apartment|EventPosting|Event|Article|BlogPosting|Blog|BreadcrumbList|AggregateRating|WebPage|Person|Organization|NewsArticle|Product|CreativeWork|ImageObject|UserComments|WPHeader|WPSideBar|WPFooter|WPAdBlock|SiteNavigationElement|Rating|worstRating|ratingValue|bestRating)(\"?)/", "", $content);
        $content = preg_replace("/itemscope[\n|\s|]*itemtype=(\"?)http(s?):\/\/schema.org\/(Person|Mosque|SearchAction|Church|HinduTemple|LandmarksOrHistoricalBuildings|TouristDestination|TouristAttraction|Place|LocalBusiness|MedicalCondition|VideoObject|AudioObject|Trip|Service|JobPosting|VideoGame|Game|TechArticle|SoftwareApplication|TVSeries|Recipe|Review|HowTo|DiscussionForumPosting|Course|SingleFamilyResidence|House|Apartment|EventPosting|Event|Article|BlogPosting|Blog|BreadcrumbList|AggregateRating|WebPage|Person|Organization|NewsArticle|Product|CreativeWork|ImageObject|UserComments|WPHeader|WPSideBar|WPFooter|WPAdBlock|SiteNavigationElement|Rating|worstRating|ratingValue|bestRating)(\"?)/", "", $content);
        $content = preg_replace("/itemscope[\n|\s|]*itemtype=(\'?)http(s?):\/\/schema.org\/(Person|Mosque|SearchAction|Church|HinduTemple|LandmarksOrHistoricalBuildings|TouristDestination|TouristAttraction|Place|LocalBusiness|MedicalCondition|VideoObject|AudioObject|Trip|Service|JobPosting|VideoGame|Game|TechArticle|SoftwareApplication|TVSeries|Recipe|Review|HowTo|DiscussionForumPosting|Course|SingleFamilyResidence|House|Apartment|EventPosting|Event|Article|BlogPosting|Blog|BreadcrumbList|AggregateRating|WebPage|Person|Organization|NewsArticle|Product|CreativeWork|ImageObject|UserComments|WPHeader|WPSideBar|WPFooter|WPAdBlock|SiteNavigationElement|Rating|worstRating|ratingValue|bestRating)(\'?)/", "", $content);
        $content = preg_replace("/itemscope=(\"?)itemscope(\"?) itemtype=(\"?)http(s?):\/\/schema.org\/(Person|Mosque|SearchAction|Church|HinduTemple|LandmarksOrHistoricalBuildings|TouristDestination|TouristAttraction|Place|LocalBusiness|MedicalCondition|VideoObject|AudioObject|Trip|Service|JobPosting|VideoGame|Game|TechArticle|SoftwareApplication|TVSeries|Recipe|Review|HowTo|DiscussionForumPosting|Course|SingleFamilyResidence|House|Apartment|EventPosting|Event|Article|BlogPosting|Blog|BreadcrumbList|AggregateRating|WebPage|Person|Organization|NewsArticle|Product|CreativeWork|ImageObject|UserComments|WPHeader|WPSideBar|WPFooter|WPAdBlock|SiteNavigationElement|Rating|worstRating|ratingValue|bestRating)(\"?)/", "", $content);    
        $content = preg_replace("/itemscope=(\"?)itemprop(\"?) itemType=(\"?)http(s?):\/\/schema.org\/(Person|Mosque|SearchAction|Church|HinduTemple|LandmarksOrHistoricalBuildings|TouristDestination|TouristAttraction|Place|LocalBusiness|MedicalCondition|VideoObject|AudioObject|Trip|Service|JobPosting|VideoGame|Game|TechArticle|SoftwareApplication|TVSeries|Recipe|Review|HowTo|DiscussionForumPosting|Course|SingleFamilyResidence|House|Apartment|EventPosting|Event|Article|BlogPosting|Blog|BreadcrumbList|AggregateRating|WebPage|Person|Organization|NewsArticle|Product|CreativeWork|ImageObject|UserComments|WPHeader|WPSideBar|WPFooter|WPAdBlock|SiteNavigationElement|Rating|worstRating|ratingValue|bestRating)(\"?)/", "", $content);    
        $content = preg_replace("/itemscope itemprop=\"(.*?)\" itemType=(\"?)http(s?):\/\/schema.org\/(Person|Mosque|SearchAction|Church|HinduTemple|LandmarksOrHistoricalBuildings|TouristDestination|TouristAttraction|Place|LocalBusiness|MedicalCondition|VideoObject|AudioObject|Trip|Service|JobPosting|VideoGame|Game|TechArticle|SoftwareApplication|TVSeries|Recipe|Review|HowTo|DiscussionForumPosting|Course|SingleFamilyResidence|House|Apartment|EventPosting|Event|Article|BlogPosting|Blog|BreadcrumbList|AggregateRating|WebPage|Person|Organization|NewsArticle|Product|CreativeWork|ImageObject|UserComments|WPHeader|WPSideBar|WPFooter|WPAdBlock|SiteNavigationElement|Rating|worstRating|ratingValue|bestRating)(\"?)/", "", $content);           
        $content = preg_replace("/itemprop='logo' itemscope itemtype='https:\/\/schema.org\/ImageObject'/", "", $content);
        $content = preg_replace('/itemprop="logo" itemscope="" itemtype="https:\/\/schema.org\/ImageObject"/', "", $content);
        $content = preg_replace('/itemprop=\"(worstRating|ratingValue|bestRating|aggregateRating|ratingCount|reviewBody|review|name|datePublished|author|reviewRating)\"/', "", $content);
        $content = preg_replace('/itemscope/', "", $content);
        
        //Clean json markup
        if(isset($sd_data['saswp-aiosp']) && $sd_data['saswp-aiosp'] == 1 ){
            $content = preg_replace('/<script type=\"application\/ld\+json" class=\"aioseop-schema"\>(.*?)<\/script>/', "", $content);
        }
        
        if(isset($sd_data['saswp-wp-ultimate-recipe']) && $sd_data['saswp-wp-ultimate-recipe'] == 1 ){
         
            $regex = '/<script type=\"application\/ld\+json\">(.*?)<\/script>[\s\n]*<div id=\"wpurp\-container\-recipe\-([0-9]+)\"/';
        
            preg_match( $regex, $content, $match );

            if(isset($match[2])){
                
                $recipe_id = $match[2];

                $content = preg_replace($regex, '<div id="wpurp-container-recipe-'.$recipe_id.'"', $content);        
            
            }
                                    
        }
        
        if(isset($sd_data['saswp-zip-recipes']) && $sd_data['saswp-zip-recipes'] == 1 ){
            
            $regex = '/class=\"zlrecipe\-container\-border\"(.*?)>[\s\n]*<script type=\"application\/ld\+json\">(.*?)<\/script\>/sm';
            preg_match_all( $regex, $content, $matches , PREG_SET_ORDER );
            
            if($matches){
                
                foreach($matches as $match){
                    
                    $content = preg_replace($regex, 'class="zlrecipe-container-border" '.$match[1].'>', $content);   
                    
                }
                
            }
            
        }
        
    }             
    
    return $content;
}

/**
 * This is a global option to hide and show all the features of this plugin.
 * @global type $sd_data
 * @return boolean
 *
 */
function saswp_global_option(){
    
            global $sd_data;
            
            if( (   saswp_remove_warnings($sd_data, 'saswp-for-wordpress', 'saswp_string') =='' 
            ||   1 == saswp_remove_warnings($sd_data, 'saswp-for-wordpress', 'saswp_string') && saswp_non_amp() ) 
            || ( 1 == saswp_remove_warnings($sd_data, 'saswp-for-amp', 'saswp_string') && !saswp_non_amp() ) ) {
        
                return true;
        
            }else{
            
                return false;
                
            }  
            
}
/**
 * Function to get post tags as a comma separated string.
 * @global type $post
 * @return string
 * @since version 1.9
 */
function saswp_get_the_tags(){

    global $post;
    $tag_str = '';
    
    if(is_object($post)){
        
      $tags = get_the_tags($post->ID);
      
      if($tags){
          
          foreach($tags as $tag){
              
            $tag_str .= $tag->name.', '; 
              
          }
          
      }
        
        
    }    
    return $tag_str;
    
}

/**
 * Function to get shorcode ids from content by shortcode typ
 * @global type $post
 * @param type $type
 * @return type
 * @since version 1.9.3
 */
function saswp_get_ids_from_content_by_type($type){
        
    global $post;
    
    if(is_object($post)){
     
        $content = $post->post_content;    

        switch ($type) {

            case 'wp_recipe_maker':

                  // Gutenberg.
                    $gutenberg_matches = array();
                    $gutenberg_patern = '/<!--\s+wp:(wp\-recipe\-maker\/recipe)(\s+(\{.*?\}))?\s+(\/)?-->/';
                    preg_match_all( $gutenberg_patern, $content, $matches );

                    if ( isset( $matches[3] ) ) {
                            foreach ( $matches[3] as $block_attributes_json ) {
                                    if ( ! empty( $block_attributes_json ) ) {
                                            $attributes = json_decode( $block_attributes_json, true );
                                            if ( ! is_null( $attributes ) ) {
                                                    if ( isset( $attributes['id'] ) ) {
                                                            $gutenberg_matches[] = intval( $attributes['id'] );
                                                    }
                                            }
                                    }
                            }
                    }

                    // Classic Editor.
                    preg_match_all( '/<!--WPRM Recipe (\d+)-->.+?<!--End WPRM Recipe-->/ms', $content, $matches );
                    $classic_matches = isset( $matches[1] ) ? array_map( 'intval', $matches[1] ) : array();

                    return $gutenberg_matches + $classic_matches;  
                    

            default:
                break;
        }
        
    }
             
}
/**
 * Function to get recipe schema markup from wp_recipe_maker
 * @param type $recipe
 * @return array
 * @since version 1.9.3
 */
function saswp_wp_recipe_schema_json($recipe){
            
            if ( 'food' === $recipe->type() ) {
                    $metadata = WPRM_Metadata::get_food_metadata( $recipe );
            } elseif ( 'howto' === $recipe->type() ) {
                    $metadata = WPRM_Metadata::get_howto_metadata( $recipe );
            } else {
                    $metadata = array();
            } 
            
            if(isset($metadata['image']) && is_array($metadata['image'])){
                
                $image_list = array();
                
                foreach($metadata['image'] as $image_url){
                    
                    $image_size    = @getimagesize($image_url);
                    
                    if($image_size[0] < 1280 && $image_size[1] < 720){
                                            
                        $image_details = @saswp_aq_resize( $image_url, 1280, 720, true, false, true );
                    
                            if($image_details){

                                $image['@type']  = 'ImageObject';
                                $image['url']    = esc_url($image_details[0]);
                                $image['width']  = esc_attr($image_details[1]);
                                $image['height'] = esc_attr($image_details[2]); 

                                $image_list[] = $image;
                            }
                                                
                    }                                        
                    
                }
                
                if($image_list){
                    $metadata['image'] =  $image_list;
                }
               
            }
                        
        return $metadata;
}

function saswp_get_testimonial_data($atts, $matche){
                      
                $reviews       = array();
                $ratings       = array();                
                $testimonial   = array();
                
                switch ($matche) {

                    case 'single_testimonial':
                        
                         $arg  = array(  
                                               'post_type'      => 'testimonial',
                                               'post_status'    => 'publish', 
                                               'post__in'       => array($atts['id']), 
                                    );    
                        
                           
                       
                        break;
                    case 'random_testimonial':
                        
                           $arg  = array(  
                                      'post_type'                 => 'testimonial',
                                      'post_status'               => 'publish', 
                                      'posts_per_page'            => $atts['count'],                                      
                                      'orderby'                   => 'rand',
                           );    
                        
                        break;
                   
                    case 'testimonials':
                    case 'testimonials_cycle':
                    case 'testimonials_grid':
                        
                        $arg  = array(  
                                      'post_type'                 => 'testimonial',
                                      'post_status'               => 'publish', 
                                      'posts_per_page'            => $atts['count'],                                                                            
                           );

                        break;
                    
                }
                
                $testimonial = get_posts( $arg);  
                 
                if(!empty($testimonial)){
                    
                    $sumofrating = 0;
                    $avg_rating  = 1;
                    
                    foreach ($testimonial as $value){
                                
                         $rating       = get_post_meta($value->ID, $key='_ikcf_rating', true); 
                         $author       = get_post_meta($value->ID, $key='_ikcf_client', true); 
                         
                         $sumofrating += $rating;
                             
                         $reviews[] = array(
                             '@type'         => 'Review',
                             'author'        => $author,
                             'datePublished' => saswp_format_date_time($value->post_date),
                             'description'   => $value->post_content,
                             'reviewRating'  => array(
                                                '@type'	        => 'Rating',
                                                'bestRating'	=> '5',
                                                'ratingValue'	=> $rating,
                                                'worstRating'	=> '1',
                                   )
                         ); 
                         
                        }
                    
                        if($sumofrating> 0){
                          $avg_rating = $sumofrating /  count($reviews); 
                        }

                        $ratings['aggregateRating'] =  array(
                                                        '@type'         => 'AggregateRating',
                                                        'ratingValue'	=> $avg_rating,
                                                        'reviewCount'   => count($testimonial)
                        );
                    
                }
                                               
                return array('reviews' => $reviews, 'rating' => $ratings);
}

function saswp_get_easy_testomonials(){
    
    $testimonial = array();
    
    global $post, $sd_data;

     if(isset($sd_data['saswp-easy-testimonials']) && $sd_data['saswp-easy-testimonials'] == 1){
     
        if(is_object($post)){
         
         $pattern = get_shortcode_regex();

        if (   preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches )
            && array_key_exists( 2, $matches ) )
        {
             
           $testimo_str = ''; 
           
           if(in_array( 'single_testimonial', $matches[2] )){
               $testimo_str = 'single_testimonial';
           }elseif(in_array( 'random_testimonial', $matches[2] )){
               $testimo_str = 'random_testimonial';
           }elseif(in_array( 'testimonials', $matches[2] )){
               $testimo_str = 'testimonials';
           }elseif(in_array( 'testimonials_cycle', $matches[2] )){
               $testimo_str = 'testimonials_cycle';
           }elseif(in_array( 'testimonials_grid', $matches[2] )){
               $testimo_str = 'testimonials_grid';
           }
            
        if($testimo_str){
            
            foreach ($matches[0] as $matche){
            
                $mached = rtrim($matche, ']'); 
                $mached = ltrim($mached, '[');
                $mached = trim($mached);
                $atts   = shortcode_parse_atts('['.$mached.' ]');  
                
                $testimonial = saswp_get_testimonial_data($atts, $testimo_str);
                                
            break;
         }
            
        }    
                               
       }
         
      }
      
     }   
         
    return $testimonial;
    
}

function saswp_get_bne_testimonials_data($atts, $testimo_str){
        
            $reviews       = array();
            $ratings       = array();            
            $arg  = array(  
                'post_type' 		=>	'bne_testimonials',		
		'order'			=> 	$atts['order'],
		'orderby' 		=> 	$atts['orderby'],
		'posts_per_page'	=> 	$atts['limit'],
             );    

            $testimonial = get_posts( $arg); 
                             
            if(!empty($testimonial)){

                $sumofrating = 0;
                $avg_rating  = 1;

                foreach ($testimonial as $value){

                     $rating       = get_post_meta($value->ID, $key='rating', true); 
                     $author       = get_post_meta($value->ID, $key='tagline', true); 

                     $sumofrating += $rating;

                     $reviews[] = array(
                         '@type'         => 'Review',
                         'author'        => $author,
                         'datePublished' => saswp_format_date_time($value->post_date),
                         'description'   => $value->post_content,
                         'reviewRating'  => array(
                                            '@type'	        => 'Rating',
                                            'bestRating'	=> '5',
                                            'ratingValue'	=> $rating,
                                            'worstRating'	=> '1',
                               )
                     ); 

                    }

                    if($sumofrating> 0){
                      $avg_rating = $sumofrating /  count($reviews); 
                    }

                    $ratings['aggregateRating'] =  array(
                                                    '@type'         => 'AggregateRating',
                                                    'ratingValue'	=> $avg_rating,
                                                    'reviewCount'   => count($testimonial)
                    );

            }

            return array('reviews' => $reviews, 'rating' => $ratings);
    
}
function saswp_get_bne_testomonials(){
    
    $testimonial = array();
    
    global $post, $sd_data;

     if(isset($sd_data['saswp-bne-testimonials']) && $sd_data['saswp-bne-testimonials'] == 1){
     
        if(is_object($post)){
         
         $pattern = get_shortcode_regex();

        if (   preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches )
            && array_key_exists( 2, $matches ) )
        {
             
           $testimo_str = ''; 
           
           if(in_array( 'bne_testimonials', $matches[2] )){
               $testimo_str = 'bne_testimonials';
           }
            
        if($testimo_str){
            
            foreach ($matches[0] as $matche){
            
                $mached = rtrim($matche, ']'); 
                $mached = ltrim($mached, '[');
                $mached = trim($mached);
                $atts   = shortcode_parse_atts('['.$mached.' ]'); 
                
                $id = get_post_meta( $atts['custom'], '_bne_testimonials_sg_shortcode', true );
                
                if($id){
                    
                    $atts   = shortcode_parse_atts($id); 
                                
                    $testimonial = saswp_get_bne_testimonials_data($atts, $testimo_str);
                    
                }
                                                                
            break;
         }
            
        }    
                               
       }
         
      }
      
     }   
         
    return $testimonial;
    
}

function saswp_append_fetched_reviews($input1){
    
    global $post;
    
    if(is_object($post)){
    
        $pattern = get_shortcode_regex();

        if (   preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches )
            && array_key_exists( 2, $matches )
            && in_array( 'saswp-reviews', $matches[2] ) )
        {
       
        $service = new saswp_reviews_service();    
            
        foreach ($matches[0] as $matche){
            
            $mached = rtrim($matche, ']'); 
            $mached = ltrim($mached, '[');
            $mached = trim($mached);
            $attr   = shortcode_parse_atts('['.$mached.' ]');  
            
            $reviews = $service->saswp_get_reviews_list_by_parameters($attr);   
            
            if($reviews){
             
                $rv_markup = $service->saswp_get_reviews_schema_markup($reviews);
                
                if($rv_markup){
                    
                    if(isset($input1['review'])){

                    $input1['review'] = array_merge($input1['review'], $rv_markup['review']);

                    }else{
                       $input1 = array_merge($input1, $rv_markup);
                    }
                    
                }
                
            }            
            
        }   
        
    }
    
    
   }   
    return $input1;
}

function saswp_book_schema_markup($schema_id, $schema_post_id, $all_post_meta){
 
            $input1 = array();
    
            $howto_image = get_post_meta( get_the_ID(), 'saswp_book_image_'.$schema_id.'_detail',true); 

            $input1['@context']              = saswp_context_url();
            $input1['@type']                 = 'Book';
            $input1['@id']                   = trailingslashit(get_permalink()).'#Book';
            $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_book_url_'.$schema_id, 'saswp_array');                            
            $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_book_name_'.$schema_id, 'saswp_array');                            
            $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_book_description_'.$schema_id, 'saswp_array');

            if(!(empty($howto_image))){

            $input1['image']['@type']        = 'ImageObject';
            $input1['image']['url']          = isset($howto_image['thumbnail']) ? esc_url($howto_image['thumbnail']):'';
            $input1['image']['height']       = isset($howto_image['width'])     ? esc_attr($howto_image['width'])   :'';
            $input1['image']['width']        = isset($howto_image['height'])    ? esc_attr($howto_image['height'])  :'';

            }  

            if($all_post_meta['saswp_book_author_'.$schema_id][0]){
                $input1['author']['@type']   = 'Person';
                $input1['author']['name']    = $all_post_meta['saswp_book_author_'.$schema_id][0];
                
                if($all_post_meta['saswp_book_author_url_'.$schema_id][0]){                    
                    $input1['author']['sameAs']    = $all_post_meta['saswp_book_author_url_'.$schema_id][0];
                }
                
            }
                                    
            $input1['datePublished']        = isset($all_post_meta['saswp_book_date_published_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_book_date_published_'.$schema_id][0])):'';
            $input1['isbn']                 = saswp_remove_warnings($all_post_meta, 'saswp_book_isbn_'.$schema_id, 'saswp_array');                          
            $input1['numberOfPages']        = saswp_remove_warnings($all_post_meta, 'saswp_book_no_of_page_'.$schema_id, 'saswp_array');                          
            $input1['publisher']            = saswp_remove_warnings($all_post_meta, 'saswp_book_publisher_'.$schema_id, 'saswp_array');                          

            if(isset($all_post_meta['saswp_book_price_'.$schema_id]) && isset($all_post_meta['saswp_book_price_currency_'.$schema_id])){
                $input1['offers']['@type']         = 'Offer';
                $input1['offers']['availability']  = saswp_remove_warnings($all_post_meta, 'saswp_book_availability_'.$schema_id, 'saswp_array');
                $input1['offers']['price']         = $all_post_meta['saswp_book_price_'.$schema_id];
                $input1['offers']['priceCurrency'] = $all_post_meta['saswp_book_price_currency_'.$schema_id];
            }

            if(isset($all_post_meta['saswp_book_enable_rating_'.$schema_id]) && isset($all_post_meta['saswp_book_rating_value_'.$schema_id]) && isset($all_post_meta['saswp_book_rating_count_'.$schema_id])){
                $input1['aggregateRating']['@type']         = 'aggregateRating';
                $input1['aggregateRating']['ratingValue']   = $all_post_meta['saswp_book_rating_value_'.$schema_id];
                $input1['aggregateRating']['ratingCount']   = $all_post_meta['saswp_book_rating_count_'.$schema_id];                                
            }
        
    return $input1;
}

function saswp_howto_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
        $input1 = array();
        $howto_image = get_post_meta( get_the_ID(), 'saswp_howto_schema_image_'.$schema_id.'_detail',true); 

        $tool    = get_post_meta($schema_post_id, 'howto_tool_'.$schema_id, true);              
        $step    = get_post_meta($schema_post_id, 'howto_step_'.$schema_id, true);              
        $supply  = get_post_meta($schema_post_id, 'howto_supply_'.$schema_id, true);              

        $input1['@context']              = saswp_context_url();
        $input1['@type']                 = 'HowTo';
        $input1['@id']                   = trailingslashit(get_permalink()).'#HowTo';
        $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_howto_schema_name_'.$schema_id, 'saswp_array');
        $input1['datePublished']         = isset($all_post_meta['saswp_howto_ec_schema_date_published_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_howto_ec_schema_date_published_'.$schema_id][0])):'';
        $input1['dateModified']          = isset($all_post_meta['saswp_howto_ec_schema_date_modified_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_howto_ec_schema_date_modified_'.$schema_id][0])):'';
        $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_howto_schema_description_'.$schema_id, 'saswp_array');

        if(!(empty($howto_image))){

        $input1['image']['@type']        = 'ImageObject';
        $input1['image']['url']          = isset($howto_image['thumbnail']) ? esc_url($howto_image['thumbnail']):'';
        $input1['image']['height']       = isset($howto_image['width'])     ? esc_attr($howto_image['width'])   :'';
        $input1['image']['width']        = isset($howto_image['height'])    ? esc_attr($howto_image['height'])  :'';

        }                            

        if(saswp_remove_warnings($all_post_meta, 'saswp_howto_ec_schema_currency_'.$schema_id, 'saswp_array') !='' && saswp_remove_warnings($all_post_meta, 'saswp_howto_ec_schema_value_'.$schema_id, 'saswp_array') !='')
        {
            $input1['estimatedCost']['@type']   = 'MonetaryAmount';
            $input1['estimatedCost']['currency']= saswp_remove_warnings($all_post_meta, 'saswp_howto_ec_schema_currency_'.$schema_id, 'saswp_array');
            $input1['estimatedCost']['value']   = saswp_remove_warnings($all_post_meta, 'saswp_howto_ec_schema_value_'.$schema_id, 'saswp_array');
        }


        $supply_arr = array();
        if(!empty($supply)){

            foreach($supply as $val){

                $supply_data = array();

                if($val['saswp_howto_supply_name'] || $val['saswp_howto_supply_url']){
                    $supply_data['@type'] = 'HowToSupply';
                    $supply_data['name']  = $val['saswp_howto_supply_name'];
                    $supply_data['url']   = $val['saswp_howto_supply_url'];
                }



                if(isset($val['saswp_howto_supply_image_id']) && $val['saswp_howto_supply_image_id'] !=''){

                            $image_details   = wp_get_attachment_image_src($val['saswp_howto_supply_image_id']); 

                            $supply_data['image']['@type']  = 'ImageObject';                                                
                            $supply_data['image']['url']    = esc_url($image_details[0]);
                            $supply_data['image']['width']  = esc_attr($image_details[1]);
                            $supply_data['image']['height'] = esc_attr($image_details[2]);



                }
               $supply_arr[] =  $supply_data;
            }
           $input1['supply'] = $supply_arr;
        }

        $tool_arr = array();
        if(!empty($tool)){

            foreach($tool as $val){

                $supply_data = array();

                if($val['saswp_howto_tool_name'] || $val['saswp_howto_tool_url']){
                    $supply_data['@type'] = 'HowToTool';
                    $supply_data['name'] = $val['saswp_howto_tool_name'];
                    $supply_data['url']  = $val['saswp_howto_tool_url'];
                }

                if(isset($val['saswp_howto_tool_image_id']) && $val['saswp_howto_tool_image_id'] !=''){

                    $image_details   = wp_get_attachment_image_src($val['saswp_howto_tool_image_id']); 

                            $supply_data['image']['@type']  = 'ImageObject';                                                
                            $supply_data['image']['url']    = esc_url($image_details[0]);
                            $supply_data['image']['width']  = esc_attr($image_details[1]);
                            $supply_data['image']['height'] = esc_attr($image_details[2]);



                }
               $tool_arr[] =  $supply_data;
            }
           $input1['tool'] = $tool_arr;
        }

        //step

        $step_arr = array();                            
        if(!empty($step)){

            foreach($step as $key => $val){

                $supply_data = array();
                $direction   = array();
                $tip         = array();

                if($val['saswp_howto_direction_text']){
                    $direction['@type']     = 'HowToDirection';
                    $direction['text']      = $val['saswp_howto_direction_text'];
                }

                if($val['saswp_howto_tip_text']){

                    $tip['@type']           = 'HowToTip';
                    $tip['text']            = $val['saswp_howto_tip_text'];

                }

                $supply_data['@type']   = 'HowToStep';
                $supply_data['url']     = trailingslashit(get_permalink()).'#step'.++$key;
                $supply_data['name']    = $val['saswp_howto_step_name'];    

                if($direction['text'] ||  $tip['text']){
                    $supply_data['itemListElement']  = array($direction, $tip);
                }

                if(isset($val['saswp_howto_step_image_id']) && $val['saswp_howto_step_image_id'] !=''){

                            $image_details   = wp_get_attachment_image_src($val['saswp_howto_step_image_id']);                                                 
                            $supply_data['image']['@type']  = 'ImageObject';                                                
                            $supply_data['image']['url']    = esc_url($image_details[0]);
                            $supply_data['image']['width']  = esc_attr($image_details[1]);
                            $supply_data['image']['height'] = esc_attr($image_details[2]);

                }

               $step_arr[] =  $supply_data;

            }

           $input1['step'] = $step_arr;

        }

         $input1['totalTime'] = saswp_remove_warnings($all_post_meta, 'saswp_howto_schema_totaltime_'.$schema_id, 'saswp_array');
                             
    return $input1;
}

function saswp_event_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
            $input1 = array();
    
            $event_image = get_post_meta( get_the_ID(), 'saswp_event_schema_image_'.$schema_id.'_detail',true); 

            $input1 = array(
            '@context'			=> saswp_context_url(),
            '@type'				=> (isset($all_post_meta['saswp_event_schema_type_'.$schema_id][0]) && $all_post_meta['saswp_event_schema_type_'.$schema_id][0] !='') ? $all_post_meta['saswp_event_schema_type_'.$schema_id][0] : 'Event' ,
            '@id'                           => trailingslashit(get_permalink()).'#event',    
            'name'			        => saswp_remove_warnings($all_post_meta, 'saswp_event_schema_name_'.$schema_id, 'saswp_array'),
            'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_event_schema_description_'.$schema_id, 'saswp_array'),			                
            'image'                         => array(
                                                        '@type'		=>'ImageObject',
                                                        'url'		=>  isset($event_image['thumbnail']) ? esc_url($event_image['thumbnail']):'' ,
                                                        'width'		=>  isset($event_image['width'])     ? esc_attr($event_image['width'])   :'' ,
                                                        'height'            =>  isset($event_image['height'])    ? esc_attr($event_image['height'])  :'' ,
                                                    ),                                
            'location'			=> array(
                                                '@type'   => 'Place',
                                                'name'    => saswp_remove_warnings($all_post_meta, 'saswp_event_schema_location_name_'.$schema_id, 'saswp_array'),
                                                'address' => array(
                                                     '@type'           => 'PostalAddress',
                                                     'streetAddress'   => saswp_remove_warnings($all_post_meta, 'saswp_event_schema_location_streetaddress_'.$schema_id, 'saswp_array'),
                                                     'addressLocality' => saswp_remove_warnings($all_post_meta, 'saswp_event_schema_location_locality_'.$schema_id, 'saswp_array'),
                                                     'postalCode'      => saswp_remove_warnings($all_post_meta, 'saswp_event_schema_location_postalcode_'.$schema_id, 'saswp_array'),
                                                     'addressRegion'   => saswp_remove_warnings($all_post_meta, 'saswp_event_schema_location_region_'.$schema_id, 'saswp_array'),                                                     
                                                )    
                            ),
            'offers'			=> array(
                                                '@type'           => 'Offer',
                                                'url'             => saswp_remove_warnings($all_post_meta, 'saswp_event_schema_url_'.$schema_id, 'saswp_array'),	                        
                                                'price'           => saswp_remove_warnings($all_post_meta, 'saswp_event_schema_price_'.$schema_id, 'saswp_array'),
                                                'priceCurrency'   => saswp_remove_warnings($all_post_meta, 'saswp_event_schema_price_currency_'.$schema_id, 'saswp_array'),
                                                'availability'    => saswp_remove_warnings($all_post_meta, 'saswp_event_schema_availability_'.$schema_id, 'saswp_array'),
                                                'validFrom'       => isset($all_post_meta['saswp_event_schema_validfrom_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_event_schema_validfrom_'.$schema_id][0])):'',
                            )                         
                );
            
                
                if(isset($all_post_meta['saswp_event_schema_start_date_'.$schema_id][0])){
                    
                    $date = $time = '';
                    
                    $date = $all_post_meta['saswp_event_schema_start_date_'.$schema_id][0];
                    
                    if(isset($all_post_meta['saswp_event_schema_start_time_'.$schema_id][0])){
                        $time  = $all_post_meta['saswp_event_schema_start_time_'.$schema_id][0];    
                    }
                    
                    $input1['startDate']        = saswp_format_date_time($date, $time);
                    
                }
                
                if(isset($all_post_meta['saswp_event_schema_end_date_'.$schema_id][0])){
                    
                    $date = $time = '';
                    
                    $date = $all_post_meta['saswp_event_schema_end_date_'.$schema_id][0];
                    
                    if(isset($all_post_meta['saswp_event_schema_end_time_'.$schema_id][0])){
                        $time  = $all_post_meta['saswp_event_schema_end_time_'.$schema_id][0];    
                    }
                    
                    $input1['endDate']        = saswp_format_date_time($date, $time);
                    
                }


                    $performer  = get_post_meta($schema_post_id, 'performer_'.$schema_id, true);

                    $performer_arr = array();

                    if(!empty($performer)){

                        foreach($performer as $val){

                            $supply_data = array();
                            $supply_data['@type']        = $val['saswp_event_performer_type'];
                            $supply_data['name']         = $val['saswp_event_performer_name'];                                    
                            $supply_data['url']          = $val['saswp_event_performer_url'];

                            $performer_arr[] =  $supply_data;
                        }

                       $input1['performer'] = $performer_arr;

                    }
    
    
    return $input1;
    
    
}

function saswp_course_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
    
    $input1 = array(
             '@context'			=> saswp_context_url(),
             '@type'				=> 'Course' ,	
             '@id'                           => trailingslashit(get_permalink()).'#course',    
             'name'			        => saswp_remove_warnings($all_post_meta, 'saswp_course_name_'.$schema_id, 'saswp_array'),
             'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_course_description_'.$schema_id, 'saswp_array'),			
             'url'				=> saswp_remove_warnings($all_post_meta, 'saswp_course_url_'.$schema_id, 'saswp_array'),
             'datePublished'                 => isset($all_post_meta['saswp_course_date_published_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_course_date_published_'.$schema_id][0])):'',
             'dateModified'                  => isset($all_post_meta['saswp_course_date_modified_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_course_date_modified_'.$schema_id][0])):'',
             'provider'			=> array(
                                                 '@type' 	        => 'Organization',
                                                 'name'		=> saswp_remove_warnings($all_post_meta, 'saswp_course_provider_name_'.$schema_id, 'saswp_array'),
                                                 'sameAs'		=> saswp_remove_warnings($all_post_meta, 'saswp_course_sameas_'.$schema_id, 'saswp_array') 
                                             )											
                 );
    
    return $input1;
    
}

function saswp_software_app_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
    
    
    $input1 = array(
             '@context'			=> saswp_context_url(),
             '@type'				=> 'SoftwareApplication',
             '@id'                           => trailingslashit(get_permalink()).'#softwareapplication',     
             'name'			        => saswp_remove_warnings($all_post_meta, 'saswp_software_schema_name_'.$schema_id, 'saswp_array'),
             'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_software_schema_description_'.$schema_id, 'saswp_array'),
             'operatingSystem'		=> saswp_remove_warnings($all_post_meta, 'saswp_software_schema_operating_system_'.$schema_id, 'saswp_array'),
             'applicationCategory'		=> saswp_remove_warnings($all_post_meta, 'saswp_software_schema_application_category_'.$schema_id, 'saswp_array'),                        
             'offers'                        => array(
                                                 '@type'         => 'Offer',
                                                 'price'         => saswp_remove_warnings($all_post_meta, 'saswp_software_schema_price_'.$schema_id, 'saswp_array'),	                         
                                                 'priceCurrency' => saswp_remove_warnings($all_post_meta, 'saswp_software_schema_price_currency_'.$schema_id, 'saswp_array'),	                         
                                              ),
             'datePublished'                 => isset($all_post_meta['saswp_software_schema_date_published_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_software_schema_date_published_'.$schema_id][0])):'',
             'dateModified'                  => isset($all_post_meta['saswp_software_schema_date_modified_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_software_schema_date_modified_'.$schema_id][0])):'',

                );

                $soft_image = get_post_meta( get_the_ID(), 'saswp_software_schema_image_'.$schema_id.'_detail',true); 

                if(!(empty($soft_image))){

                     $input1['image']['@type']        = 'ImageObject';
                     $input1['image']['url']          = isset($soft_image['thumbnail']) ? esc_url($soft_image['thumbnail']):'';
                     $input1['image']['height']       = isset($soft_image['width'])     ? esc_attr($soft_image['width'])   :'';
                     $input1['image']['width']        = isset($soft_image['height'])    ? esc_attr($soft_image['height'])  :'';

                 }
                 
                 if(saswp_remove_warnings($all_post_meta, 'saswp_software_schema_enable_rating_'.$schema_id, 'saswp_array') == 1){   
                                 
                                          $input1['aggregateRating'] = array(
                                                            "@type"       => "AggregateRating",
                                                            "ratingValue" => saswp_remove_warnings($all_post_meta, 'saswp_software_schema_rating_'.$schema_id, 'saswp_array'),
                                                            "reviewCount" => saswp_remove_warnings($all_post_meta, 'saswp_software_schema_rating_count_'.$schema_id, 'saswp_array')
                                                         );                                       
                }
    
    
    return $input1;
    
}

function saswp_recipe_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
            $input1 = array();

            $recipe_logo    = get_post_meta( get_the_ID(), 'saswp_recipe_organization_logo_'.$schema_id.'_detail',true);
            $recipe_image   = get_post_meta( get_the_ID(), 'saswp_recipe_image_'.$schema_id.'_detail',true);                                                                           
            $recipe_author_image   = get_post_meta( get_the_ID(), 'saswp_recipe_author_image_'.$schema_id.'_detail',true);

            $ingredient     = array();
            $instruction    = array();

            if(isset($all_post_meta['saswp_recipe_ingredient_'.$schema_id])){

                $explod = explode(';', $all_post_meta['saswp_recipe_ingredient_'.$schema_id][0]);  

                if($explod){

                    foreach ($explod as $val){

                        $ingredient[] = $val;  

                    }

                }



            }

            if(isset($all_post_meta['saswp_recipe_instructions_'.$schema_id])){

                $explod = explode(';', $all_post_meta['saswp_recipe_instructions_'.$schema_id][0]);  

                if($explod){

                    foreach ($explod as $val){

                        $instruction[] = array(
                                                   '@type'  => "HowToStep",
                                                   'text'   => $val,                                                                                                                            
                                                   );  

                  }

                }                                       

            }

            $input1 = array(
            '@context'			=> saswp_context_url(),
            '@type'				=> 'Recipe' ,
            '@id'                           => trailingslashit(get_permalink()).'#recipe',    
            'url'				=> saswp_remove_warnings($all_post_meta, 'saswp_recipe_url_'.$schema_id, 'saswp_array'),
            'name'			        => saswp_remove_warnings($all_post_meta, 'saswp_recipe_name_'.$schema_id, 'saswp_array'),
            'image'                         =>array(
             '@type'		=> 'ImageObject',
                'url'		=> saswp_remove_warnings( $recipe_image, 'thumbnail', 'saswp_string'),
            'width'		=> saswp_remove_warnings( $recipe_image, 'width', 'saswp_string'),
             'height'    => saswp_remove_warnings( $recipe_image , 'height', 'saswp_string'),
         ),
            'author'			=> array(
                                            '@type' 	=> 'Person',
                                            'name'		=> saswp_remove_warnings($all_post_meta, 'saswp_recipe_author_name_'.$schema_id, 'saswp_array'),
                                            'description'	=> saswp_remove_warnings($all_post_meta, 'saswp_recipe_author_description_'.$schema_id, 'saswp_array'),
                                            'Image'		=> array(
                                                    '@type'			=> 'ImageObject',
                                                    'url'			=> saswp_remove_warnings($all_post_meta, 'saswp_recipe_author_image_'.$schema_id, 'saswp_array'),
                                                    'height'		=> saswp_remove_warnings($recipe_author_image, 'height', 'saswp_string'),
                                                    'width'			=> saswp_remove_warnings($recipe_author_image, 'width', 'saswp_string')
                                            ),
                                    ),


            'prepTime'                       => saswp_remove_warnings($all_post_meta, 'saswp_recipe_preptime_'.$schema_id, 'saswp_array'),  
            'cookTime'                       => saswp_remove_warnings($all_post_meta, 'saswp_recipe_cooktime_'.$schema_id, 'saswp_array'),  
            'totalTime'                      => saswp_remove_warnings($all_post_meta, 'saswp_recipe_totaltime_'.$schema_id, 'saswp_array'),  
            'keywords'                       => saswp_remove_warnings($all_post_meta, 'saswp_recipe_keywords_'.$schema_id, 'saswp_array'),  
            'recipeYield'                    => saswp_remove_warnings($all_post_meta, 'saswp_recipe_recipeyield_'.$schema_id, 'saswp_array'),  
            'recipeCategory'                 => saswp_remove_warnings($all_post_meta, 'saswp_recipe_category_'.$schema_id, 'saswp_array'),
            'recipeCuisine'                  => saswp_remove_warnings($all_post_meta, 'saswp_recipe_cuisine_'.$schema_id, 'saswp_array'),  
            'nutrition'                      => array(
                                                '@type'  => "NutritionInformation",
                                                'calories'  => saswp_remove_warnings($all_post_meta, 'saswp_recipe_nutrition_'.$schema_id, 'saswp_array'),                                                                 
                                             ), 
            'recipeIngredient'               => $ingredient, 
            'recipeInstructions'             => $instruction,                                                                                                                                                                                 
            'datePublished'                 => isset($all_post_meta['saswp_recipe_date_published_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_recipe_date_published_'.$schema_id][0])):'',
            'dateModified'                  => isset($all_post_meta['saswp_recipe_date_modified_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_recipe_date_modified_'.$schema_id][0])):'',
            'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_recipe_description_'.$schema_id, 'saswp_array'),
            'mainEntity'                    => array(
                            '@type'				=> 'WebPage',
                            '@id'				=> saswp_remove_warnings($all_post_meta, 'saswp_recipe_main_entity_'.$schema_id, 'saswp_array'),						
                            'publisher'			=> array(
                                    '@type'			=> 'Organization',
                                    'logo' 			=> array(
                                            '@type'		=> 'ImageObject',
                                            'url'		=> saswp_remove_warnings($all_post_meta, 'saswp_recipe_organization_logo_'.$schema_id, 'saswp_array'),
                                            'width'		=> saswp_remove_warnings($recipe_logo, 'width', 'saswp_string'),
                                            'height'	=> saswp_remove_warnings($recipe_logo, 'height', 'saswp_string'),
                                            ),
                                    'name'			=> saswp_remove_warnings($all_post_meta, 'saswp_recipe_organization_name_'.$schema_id, 'saswp_array'),
                            ),
                    ),


            );

            if(saswp_remove_warnings($all_post_meta, 'saswp_recipe_video_name_'.$schema_id, 'saswp_array') !='' && saswp_remove_warnings($all_post_meta, 'saswp_recipe_video_thumbnailurl_'.$schema_id, 'saswp_array') !='' && saswp_remove_warnings($all_post_meta, 'saswp_recipe_video_description_'.$schema_id, 'saswp_array') !=''){

                $input1['video']['@type']        = 'VideoObject';
                $input1['video']['name']         = saswp_remove_warnings($all_post_meta, 'saswp_recipe_video_name_'.$schema_id, 'saswp_array');
                $input1['video']['description']  = saswp_remove_warnings($all_post_meta, 'saswp_recipe_video_description_'.$schema_id, 'saswp_array');
                $input1['video']['thumbnailUrl'] = saswp_remove_warnings($all_post_meta, 'saswp_recipe_video_thumbnailurl_'.$schema_id, 'saswp_array');
                $input1['video']['contentUrl']   = saswp_remove_warnings($all_post_meta, 'saswp_recipe_video_contenturl_'.$schema_id, 'saswp_array');
                $input1['video']['embedUrl']     = saswp_remove_warnings($all_post_meta, 'saswp_recipe_video_embedurl_'.$schema_id, 'saswp_array');
                $input1['video']['uploadDate']   = isset($all_post_meta['saswp_recipe_video_upload_date_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_recipe_video_upload_date_'.$schema_id][0])):'';
                $input1['video']['duration']     = saswp_remove_warnings($all_post_meta, 'saswp_recipe_video_duration_'.$schema_id, 'saswp_array');
            } 
            
            if(saswp_remove_warnings($all_post_meta, 'saswp_recipe_schema_enable_rating_'.$schema_id, 'saswp_array') == 1 && saswp_remove_warnings($all_post_meta, 'saswp_recipe_schema_rating_'.$schema_id, 'saswp_array') && saswp_remove_warnings($all_post_meta, 'saswp_recipe_schema_review_count_'.$schema_id, 'saswp_array')){   
                                                
                                    $input1['aggregateRating'] = array(
                                                    "@type"       => "AggregateRating",
                                                    "ratingValue" => saswp_remove_warnings($all_post_meta, 'saswp_recipe_schema_rating_'.$schema_id, 'saswp_array'),
                                                    "reviewCount" => saswp_remove_warnings($all_post_meta, 'saswp_recipe_schema_review_count_'.$schema_id, 'saswp_array')
                                                );                                       
                                }
    
    return $input1;
    
}

function saswp_product_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
    
            $product_image = get_post_meta( get_the_ID(), 'saswp_product_schema_image_'.$schema_id.'_detail',true);                                                                           
            $input1 = array(
            '@context'			=> saswp_context_url(),
            '@type'				=> 'Product',
            '@id'                           => trailingslashit(get_permalink()).'#product',    
            'url'				=> trailingslashit(get_permalink()),
            'name'                          => saswp_remove_warnings($all_post_meta, 'saswp_product_schema_name_'.$schema_id, 'saswp_array'),
            'sku'                           => saswp_remove_warnings($all_post_meta, 'saswp_product_schema_sku_'.$schema_id, 'saswp_array'),
            'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_product_schema_description_'.$schema_id, 'saswp_array'),													
            'image'                         =>array(
                                                        '@type'		=> 'ImageObject',
                                                        'url'		=> saswp_remove_warnings($product_image, 'thumbnail', 'saswp_string'),
                                                        'width'		=> saswp_remove_warnings($product_image, 'width', 'saswp_string'),
                                                        'height'            => saswp_remove_warnings($product_image, 'height', 'saswp_string'),
                                                        ),
            'offers'                        => array(
                                                    '@type'	          => 'Offer',
                                                    'availability'	  => saswp_remove_warnings($all_post_meta, 'saswp_product_schema_availability_'.$schema_id, 'saswp_array'),													
                                                    'itemCondition'   => saswp_remove_warnings($all_post_meta, 'saswp_product_schema_condition_'.$schema_id, 'saswp_array'),
                                                    'price' 	  => saswp_remove_warnings($all_post_meta, 'saswp_product_schema_price_'.$schema_id, 'saswp_array'),
                                                    'priceCurrency'	  => saswp_remove_warnings($all_post_meta, 'saswp_product_schema_currency_'.$schema_id, 'saswp_array'),
                                                    'url'             => trailingslashit(get_permalink()),
                                                    'priceValidUntil' => isset($all_post_meta['saswp_product_schema_priceValidUntil_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_product_schema_priceValidUntil_'.$schema_id][0])):'',
                                                    ), 
            'brand'                         => array('@type' => 'Thing',
                                                     'name'  => saswp_remove_warnings($all_post_meta, 'saswp_product_schema_brand_name_'.$schema_id, 'saswp_array'),
                                                    )    
            ); 

            if(isset($all_post_meta['saswp_product_schema_seller_'.$schema_id])){
                $input1['offers']['seller']['@type']   = 'Organization';
                $input1['offers']['seller']['name']    = esc_attr($all_post_meta['saswp_product_schema_seller_'.$schema_id][0]);  
            }                                        
            if(isset($all_post_meta['saswp_product_schema_gtin8_'.$schema_id])){
                $input1['gtin8'] = esc_attr($all_post_meta['saswp_product_schema_gtin8_'.$schema_id][0]);  
            }
            if(isset($all_post_meta['saswp_product_schema_mpn_'.$schema_id])){
              $input1['mpn'] = esc_attr($all_post_meta['saswp_product_schema_mpn_'.$schema_id][0]);  
            }
            
            if(saswp_remove_warnings($all_post_meta, 'saswp_product_schema_enable_rating_'.$schema_id, 'saswp_array') == 1 && saswp_remove_warnings($all_post_meta, 'saswp_product_schema_rating_'.$schema_id, 'saswp_array') && saswp_remove_warnings($all_post_meta, 'saswp_product_schema_review_count_'.$schema_id, 'saswp_array')){   
                                 
                                          $input1['aggregateRating'] = array(
                                                            "@type"       => "AggregateRating",
                                                            "ratingValue" => saswp_remove_warnings($all_post_meta, 'saswp_product_schema_rating_'.$schema_id, 'saswp_array'),
                                                            "reviewCount" => saswp_remove_warnings($all_post_meta, 'saswp_product_schema_review_count_'.$schema_id, 'saswp_array')
                                                         );                                       
                                         }
                                             
                                         
                                        $itinerary  = get_post_meta($schema_post_id, 'product_reviews_'.$schema_id, true);
                            
                                        $itinerary_arr = array();

                                        if(!empty($itinerary)){

                                         foreach($itinerary as $review){
                                                
                                          $review_fields = array();
                                          
                                          $review_fields['@type']         = 'Review';
                                          $review_fields['author']        = esc_attr($review['saswp_product_reviews_reviewer_name']);
                                          $review_fields['datePublished'] = esc_html($review['saswp_product_reviews_created_date']);
                                          $review_fields['description']   = esc_textarea($review['saswp_product_reviews_text']);
                                                                                    
                                          if(is_int($review['saswp_product_reviews_reviewer_rating'])){
                                              
                                                $review_fields['reviewRating']['@type']   = 'Rating';
                                                $review_fields['reviewRating']['bestRating']   = '5';
                                                $review_fields['reviewRating']['ratingValue']   = esc_attr($review['saswp_product_reviews_reviewer_rating']);
                                                $review_fields['reviewRating']['worstRating']   = '1';
                                          
                                          }
                                                                                                                                                                        
                                          $itinerary_arr[] = $review_fields;
                                            }
                                           $input1['review'] = $itinerary_arr;
                                        }
                                        
                                        $service = new saswp_output_service();
                                        $product_details = $service->saswp_woocommerce_product_details(get_the_ID());  


                                        if(!empty($product_details['product_reviews'])){
                                      
                                        $reviews = array();
                                      
                                         foreach ($product_details['product_reviews'] as $review){
                                                                                          
                                          $review_fields = array();
                                          
                                          $review_fields['@type']         = 'Review';
                                          $review_fields['author']        = esc_attr($review['author']);
                                          $review_fields['datePublished'] = esc_html($review['datePublished']);
                                          $review_fields['description']   = $review['description'];
                                                                                    
                                          if(isset($review['reviewRating']) && $review['reviewRating'] !=''){
                                              
                                                $review_fields['reviewRating']['@type']   = 'Rating';
                                                $review_fields['reviewRating']['bestRating']   = '5';
                                                $review_fields['reviewRating']['ratingValue']   = esc_attr($review['reviewRating']);
                                                $review_fields['reviewRating']['worstRating']   = '1';
                                          
                                          }
                                                                                                                                                                        
                                          $reviews[] = $review_fields;
                                          
                                      }
                                         $input1['review'] =  $reviews;
                                }
    
    return $input1;
    
}

function saswp_local_business_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
            $input1 = array();

            $operation_days      = explode( "rn", esc_html( stripslashes(saswp_remove_warnings($all_post_meta, 'saswp_dayofweek_'.$schema_id, 'saswp_array'))) );;                               
            $business_sub_name   = '';
            $business_type       = saswp_remove_warnings($all_post_meta, 'saswp_business_type_'.$schema_id, 'saswp_array'); 
            $post_specific_obj   = new saswp_post_specific();

            if(array_key_exists($business_type, $post_specific_obj->_local_sub_business)){

                $check_business_type = $post_specific_obj->_local_sub_business[$business_type];

                if(!empty($check_business_type)){

                 $business_sub_name = saswp_remove_warnings($all_post_meta, 'saswp_business_name_'.$schema_id, 'saswp_array');   

                }

            }

            if($business_sub_name){

            $local_business = $business_sub_name; 

            }else if($business_type){

            $local_business = $business_type;        

            }else{

            $local_business = 'LocalBusiness';  

            }   

            $local_image = get_post_meta( get_the_ID(), 'local_business_logo_'.$schema_id.'_detail',true);

            $input1 = array(
            '@context'			=> saswp_context_url(),
            '@type'				=> $local_business ,
            '@id'                           => ((isset($all_post_meta['saswp_business_id_'.$schema_id][0]) && $all_post_meta['saswp_business_id_'.$schema_id][0] !='') ? $all_post_meta['saswp_business_id_'.$schema_id][0] : trailingslashit(get_permalink()).'#'.strtolower($local_business)),        
            'name'                          => saswp_remove_warnings($all_post_meta, 'local_business_name_'.$schema_id, 'saswp_array'),                                   
            'url'				=> saswp_remove_warnings($all_post_meta, 'local_business_name_url_'.$schema_id, 'saswp_array'),				
            'description'                   => saswp_remove_warnings($all_post_meta, 'local_business_description_'.$schema_id, 'saswp_array'),				
            'image' 			=> array(
                                                '@type'		=> 'ImageObject',
                                                'url'		=> saswp_remove_warnings($local_image, 'thumbnail', 'saswp_string'),
                                                'width'		=> saswp_remove_warnings($local_image, 'width', 'saswp_string'),
                                                'height'            => saswp_remove_warnings($local_image, 'height', 'saswp_string'),
                                            ),    
            'address'                       => array(
                                            "@type"           => "PostalAddress",
                                            "streetAddress"   => saswp_remove_warnings($all_post_meta, 'local_street_address_'.$schema_id, 'saswp_array'),
                                            "addressLocality" => saswp_remove_warnings($all_post_meta, 'local_city_'.$schema_id, 'saswp_array'),
                                            "addressRegion"   => saswp_remove_warnings($all_post_meta, 'local_state_'.$schema_id, 'saswp_array'),
                                            "postalCode"      => saswp_remove_warnings($all_post_meta, 'local_postal_code_'.$schema_id, 'saswp_array'),                                                                                                                                  
                                             ),	
            'telephone'                   => saswp_remove_warnings($all_post_meta, 'local_phone_'.$schema_id, 'saswp_array'),
            'openingHours'                => $operation_days,                                                                                                     
            );
            
                if(isset($all_post_meta['local_price_range_'.$schema_id][0])){
                   $input1['priceRange'] = esc_attr($all_post_meta['local_price_range_'.$schema_id][0]);   
                }

                if(isset($all_post_meta['local_accepts_reservations_'.$schema_id][0])){
                  $input1['acceptsReservations'] = esc_attr($all_post_meta['local_price_accepts_reservations_'.$schema_id][0]);   
                }

                if(isset($all_post_meta['local_serves_cuisine_'.$schema_id][0])){
                  $input1['servesCuisine'] = esc_attr($all_post_meta['local_serves_cuisine_'.$schema_id][0]);   
                }
                
                if(isset($all_post_meta['local_area_served_'.$schema_id][0])){
                    
                  $area_served = explode(',', $all_post_meta['local_area_served_'.$schema_id][0]);
                  
                  $input1['areaServed'] = $area_served;   
                  
                }
               
                //social fields starts here

                $local_social = array();

                if(isset($all_post_meta['local_facebook_'.$schema_id][0]) && $all_post_meta['local_facebook_'.$schema_id][0] !=''){
                  $local_social[] = esc_url($all_post_meta['local_facebook_'.$schema_id][0]);   
                }
                if(isset($all_post_meta['local_twitter_'.$schema_id][0]) && $all_post_meta['local_twitter_'.$schema_id][0] !=''){
                  $local_social[] = esc_url($all_post_meta['local_twitter_'.$schema_id][0]);   
                }
                if(isset($all_post_meta['local_instagram_'.$schema_id][0]) && $all_post_meta['local_instagram_'.$schema_id][0] !=''){
                  $local_social[] = esc_url($all_post_meta['local_instagram_'.$schema_id][0]);   
                }
                if(isset($all_post_meta['local_pinterest_'.$schema_id][0]) && $all_post_meta['local_pinterest_'.$schema_id][0] !=''){
                  $local_social[] = esc_url($all_post_meta['local_pinterest_'.$schema_id][0]);   
                }
                if(isset($all_post_meta['local_linkedin_'.$schema_id][0]) && $all_post_meta['local_linkedin_'.$schema_id][0] !=''){
                  $local_social[] = esc_url($all_post_meta['local_linkedin_'.$schema_id][0]);   
                }
                if(isset($all_post_meta['local_soundcloud_'.$schema_id][0]) && $all_post_meta['local_soundcloud_'.$schema_id][0] !=''){
                  $local_social[] = esc_url($all_post_meta['local_soundcloud_'.$schema_id][0]);   
                }
                if(isset($all_post_meta['local_tumblr_'.$schema_id][0]) && $all_post_meta['local_tumblr_'.$schema_id][0] !=''){
                  $local_social[] = esc_url($all_post_meta['local_tumblr_'.$schema_id][0]);   
                }
                if(isset($all_post_meta['local_youtube_'.$schema_id][0]) && $all_post_meta['local_youtube_'.$schema_id][0] !=''){
                  $local_social[] = esc_url($all_post_meta['local_youtube_'.$schema_id][0]);   
                }

                if(!empty($local_social)){
                  $input1['sameAs'] =  $local_social; 
                }
                //social fields ends here

                if(isset($all_post_meta['local_menu_'.$schema_id][0])){
                  $input1['hasMenu'] = esc_url($all_post_meta['local_menu_'.$schema_id][0]);   
                }

                if(isset($all_post_meta['local_hasmap_'.$schema_id][0])){
                  $input1['hasMap'] = esc_url($all_post_meta['local_hasmap_'.$schema_id][0]);   
                }

                if(isset($all_post_meta['local_latitude_'.$schema_id][0]) && isset($all_post_meta['local_longitude_'.$schema_id][0])){

                    $input1['geo']['@type']     = 'GeoCoordinates';
                    $input1['geo']['latitude']  = $all_post_meta['local_latitude_'.$schema_id][0];
                    $input1['geo']['longitude'] = $all_post_meta['local_longitude_'.$schema_id][0];

                }
    
    return $input1;
}

function saswp_video_game_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
            $input1 = array();

            $howto_image = get_post_meta( get_the_ID(), 'saswp_vg_schema_image_'.$schema_id.'_detail',true);  

            $input1['@context']                     = saswp_context_url();
            $input1['@type']                        = 'VideoGame';
            $input1['@id']                          = trailingslashit(get_permalink()).'#VideoGame'; 
            $input1['name']                         = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_name_'.$schema_id, 'saswp_array');
            $input1['url']                          = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_url_'.$schema_id, 'saswp_array');                            
            $input1['description']                  = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_description_'.$schema_id, 'saswp_array');

            if(!(empty($howto_image))){

            $input1['image']['@type']        = 'ImageObject';
            $input1['image']['url']          = isset($howto_image['thumbnail']) ? esc_url($howto_image['thumbnail']):'';
            $input1['image']['height']       = isset($howto_image['width'])     ? esc_attr($howto_image['width'])   :'';
            $input1['image']['width']        = isset($howto_image['height'])    ? esc_attr($howto_image['height'])  :'';

            }

            $input1['operatingSystem']  = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_operating_system_'.$schema_id, 'saswp_array');
            $input1['applicationCategory']  = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_application_category_'.$schema_id, 'saswp_array');

            $input1['author']['@type']  = 'Organization';
            $input1['author']['name']   = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_author_name_'.$schema_id, 'saswp_array');

            $input1['offers']['@type']  = 'Offer';                            
            $input1['offers']['price']  = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_price_'.$schema_id, 'saswp_array');
            $input1['offers']['priceCurrency']  = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_price_currency_'.$schema_id, 'saswp_array');
            $input1['offers']['availability']  = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_price_availability_'.$schema_id, 'saswp_array');


            $input1['publisher'] = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_publisher_'.$schema_id, 'saswp_array');
            $input1['genre'] = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_genre_'.$schema_id, 'saswp_array');
            $input1['processorRequirements'] = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_processor_requirements_'.$schema_id, 'saswp_array');
            $input1['memoryRequirements'] = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_memory_requirements_'.$schema_id, 'saswp_array');
            $input1['storageRequirements'] = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_storage_requirements_'.$schema_id, 'saswp_array');
            $input1['gamePlatform'] = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_game_platform_'.$schema_id, 'saswp_array');
            $input1['cheatCode'] = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_cheat_code_'.$schema_id, 'saswp_array');
            
            if( saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_enable_rating_'.$schema_id, 'saswp_array') == 1 && saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_rating_'.$schema_id, 'saswp_array') && saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_review_count_'.$schema_id, 'saswp_array')){
                            
                            $input1['aggregateRating']['@type']       = 'AggregateRating';
                            $input1['aggregateRating']['ratingValue'] = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_rating_'.$schema_id, 'saswp_array');
                            $input1['aggregateRating']['ratingCount'] = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_review_count_'.$schema_id, 'saswp_array');
                                
            }
    
    return $input1;
}

function saswp_music_playlist_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
            $input1 = array();

            $input1['@context']              = saswp_context_url();
            $input1['@type']                 = 'MusicPlaylist';
            $input1['@id']                   = trailingslashit(get_permalink()).'#MusicPlaylist';                            
            $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_music_playlist_url_'.$schema_id, 'saswp_array');                                
            $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_music_playlist_name_'.$schema_id, 'saswp_array');                            
            $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_music_playlist_description_'.$schema_id, 'saswp_array');                                

            $faq_question  = get_post_meta($schema_post_id, 'music_playlist_track_'.$schema_id, true);

            $faq_question_arr = array();

            if(!empty($faq_question)){

                $input1['numTracks'] = count($faq_question);

                foreach($faq_question as $val){

                    $supply_data = array();
                    $supply_data['@type']                   = 'MusicRecording';
                    $supply_data['byArtist']                = $val['saswp_music_playlist_track_artist'];
                    $supply_data['duration']                = $val['saswp_music_playlist_track_duration'];
                    $supply_data['inAlbum']                 = $val['saswp_music_playlist_track_inalbum'];
                    $supply_data['name']                    = $val['saswp_music_playlist_track_name'];
                    $supply_data['url']                     = $val['saswp_music_playlist_track_url'];

                   $faq_question_arr[] =  $supply_data;
                }
               $input1['track'] = $faq_question_arr;
            }
    
    return $input1;
}
