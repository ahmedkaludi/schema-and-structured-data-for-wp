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
                $item_list                = $archive_output[0];
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
        $content = preg_replace("/itemscope[\n|\s|]*itemtype=(\"?)http(s?):\/\/schema.org\/(Person|Mosque|SearchAction|Church|HinduTemple|LandmarksOrHistoricalBuildings|TouristDestination|TouristAttraction|Place|LocalBusiness|MedicalCondition|VideoObject|AudioObject|Trip|Service|JobPosting|VideoGame|Game|TechArticle|SoftwareApplication|TVSeries|Recipe|Review|HowTo|DiscussionForumPosting|Course|SingleFamilyResidence|House|Apartment|EventPosting|Event|Article|BlogPosting|Blog|BreadcrumbList|AggregateRating|WebPage|Person|Organization|NewsArticle|Product|CreativeWork|ImageObject|UserComments|WPHeader|WPSideBar|WPFooter|WPAdBlock|SiteNavigationElement)(\"?)/", "", $content);
        $content = preg_replace("/itemscope[\n|\s|]*itemtype=(\'?)http(s?):\/\/schema.org\/(Person|Mosque|SearchAction|Church|HinduTemple|LandmarksOrHistoricalBuildings|TouristDestination|TouristAttraction|Place|LocalBusiness|MedicalCondition|VideoObject|AudioObject|Trip|Service|JobPosting|VideoGame|Game|TechArticle|SoftwareApplication|TVSeries|Recipe|Review|HowTo|DiscussionForumPosting|Course|SingleFamilyResidence|House|Apartment|EventPosting|Event|Article|BlogPosting|Blog|BreadcrumbList|AggregateRating|WebPage|Person|Organization|NewsArticle|Product|CreativeWork|ImageObject|UserComments|WPHeader|WPSideBar|WPFooter|WPAdBlock|SiteNavigationElement)(\'?)/", "", $content);
        $content = preg_replace("/itemscope=(\"?)itemscope(\"?) itemtype=(\"?)http(s?):\/\/schema.org\/(Person|Mosque|SearchAction|Church|HinduTemple|LandmarksOrHistoricalBuildings|TouristDestination|TouristAttraction|Place|LocalBusiness|MedicalCondition|VideoObject|AudioObject|Trip|Service|JobPosting|VideoGame|Game|TechArticle|SoftwareApplication|TVSeries|Recipe|Review|HowTo|DiscussionForumPosting|Course|SingleFamilyResidence|House|Apartment|EventPosting|Event|Article|BlogPosting|Blog|BreadcrumbList|AggregateRating|WebPage|Person|Organization|NewsArticle|Product|CreativeWork|ImageObject|UserComments|WPHeader|WPSideBar|WPFooter|WPAdBlock|SiteNavigationElement)(\"?)/", "", $content);    
        $content = preg_replace("/itemscope=(\"?)itemprop(\"?) itemType=(\"?)http(s?):\/\/schema.org\/(Person|Mosque|SearchAction|Church|HinduTemple|LandmarksOrHistoricalBuildings|TouristDestination|TouristAttraction|Place|LocalBusiness|MedicalCondition|VideoObject|AudioObject|Trip|Service|JobPosting|VideoGame|Game|TechArticle|SoftwareApplication|TVSeries|Recipe|Review|HowTo|DiscussionForumPosting|Course|SingleFamilyResidence|House|Apartment|EventPosting|Event|Article|BlogPosting|Blog|BreadcrumbList|AggregateRating|WebPage|Person|Organization|NewsArticle|Product|CreativeWork|ImageObject|UserComments|WPHeader|WPSideBar|WPFooter|WPAdBlock|SiteNavigationElement)(\"?)/", "", $content);    
        $content = preg_replace("/itemscope itemprop=\"(.*?)\" itemType=(\"?)http(s?):\/\/schema.org\/(Person|Mosque|SearchAction|Church|HinduTemple|LandmarksOrHistoricalBuildings|TouristDestination|TouristAttraction|Place|LocalBusiness|MedicalCondition|VideoObject|AudioObject|Trip|Service|JobPosting|VideoGame|Game|TechArticle|SoftwareApplication|TVSeries|Recipe|Review|HowTo|DiscussionForumPosting|Course|SingleFamilyResidence|House|Apartment|EventPosting|Event|Article|BlogPosting|Blog|BreadcrumbList|AggregateRating|WebPage|Person|Organization|NewsArticle|Product|CreativeWork|ImageObject|UserComments|WPHeader|WPSideBar|WPFooter|WPAdBlock|SiteNavigationElement)(\"?)/", "", $content);           
        $content = preg_replace("/itemprop='logo' itemscope itemtype='https:\/\/schema.org\/ImageObject'/", "", $content);
        $content = preg_replace('/itemprop="logo" itemscope="" itemtype="https:\/\/schema.org\/ImageObject"/', "", $content);
        
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
            $attr = shortcode_parse_atts('['.$mached.' ]');  
            
            $reviews = $service->saswp_get_reviews_by_attr($attr);   
            
            if($reviews){
             
                $rv_markup = $service->saswp_get_reviews_schema_markup($reviews);
            
                if(isset($input1['review'])){

                    $input1['review'] = array_merge($input1['review'], $rv_markup['review']);

                }else{
                   $input1 = array_merge($input1, $rv_markup);
                }
                
            }            
            
        }   
        
    }
    
    
   }   
    return $input1;
}