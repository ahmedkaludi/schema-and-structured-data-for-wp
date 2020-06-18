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

add_action( 'amp_init', 'saswp_schema_markup_hook_on_init' );
add_action( 'init', 'saswp_schema_markup_hook_on_init');
add_action( 'wp', 'saswp_wp_hook_operation',999 );

function saswp_schema_markup_hook_on_init() {
        
        if(!is_admin()){
            
            global $sd_data;
        
            if(isset($sd_data['saswp-markup-footer']) && $sd_data['saswp-markup-footer'] == 1){
                
               add_action( 'wp_footer', 'saswp_schema_markup_output');    
               add_action( 'amp_post_template_footer' , 'saswp_schema_markup_output' );
               add_action( 'better-amp/template/footer', 'saswp_schema_markup_output', 1, 1 );
               add_action( 'amphtml_template_footer', 'saswp_schema_markup_output');
               add_action( 'amp_wp_template_footer', 'saswp_schema_markup_output');
               
            }else{
                
               add_action( 'better-amp/template/head', 'saswp_schema_markup_output', 1, 1 );
               add_action( 'wp_head', 'saswp_schema_markup_output');  
               add_action( 'amp_post_template_head' , 'saswp_schema_markup_output' );
               add_action( 'amphtml_template_head', 'saswp_schema_markup_output');
               add_action( 'amp_wp_template_head', 'saswp_schema_markup_output');
               
            }               
            
            add_action('cooked_amp_head', 'saswp_schema_markup_output');
            
            if(saswp_global_option()){

                remove_action( 'amp_post_template_head', 'amp_post_template_add_schemaorg_metadata',99,1);
                remove_action( 'amp_post_template_footer', 'amp_post_template_add_schemaorg_metadata',99,1);  
                remove_action( 'wp_footer', 'orbital_markup_site'); 
                add_filter( 'amp_schemaorg_metadata', '__return_empty_array' );
                add_filter( 'hunch_schema_markup', '__return_false');                          
                
            }
                                    
            if(class_exists('BSF_AIOSRS_Pro_Markup')){
                
                if(saswp_global_option()){

                    remove_action( 'wp_head', array( BSF_AIOSRS_Pro_Markup::get_instance(), 'schema_markup' ),10);
                    remove_action( 'wp_head', array( BSF_AIOSRS_Pro_Markup::get_instance(), 'global_schemas_markup' ),10);
                    remove_action( 'wp_footer', array( BSF_AIOSRS_Pro_Markup::get_instance(), 'schema_markup' ),10);
                    remove_action( 'wp_footer', array( BSF_AIOSRS_Pro_Markup::get_instance(), 'global_schemas_markup' ),10);

                }                
                
            }
            
            if(isset($sd_data['saswp-wp-recipe-maker']) && $sd_data['saswp-wp-recipe-maker'] == 1){
                if(saswp_global_option()){
                    add_filter( 'wprm_recipe_metadata', '__return_false' );            
                }                
            }
                                    
            if(isset($sd_data['saswp-microdata-cleanup']) && $sd_data['saswp-microdata-cleanup'] == 1){                
                ob_start("saswp_remove_microdata");                
            }
                                                                                                           
        }                       
}

function saswp_wp_hook_operation(){
    
    ob_start('saswp_schema_markup_output_in_buffer');
    
}

function saswp_schema_markup_output_in_buffer($content){
    
    global $saswp_post_reviews, $saswp_elementor_faq, $saswp_divi_faq, $saswp_elementor_howto, $saswp_evo_json_ld;
     
    if(!$saswp_divi_faq){
        $regex = "<script type='text/javascript' src='".SASWP_PLUGIN_URL."modules/divi-builder/scripts/frontend-bundle.min.js?ver=1.0.0'></script>";
        $content = str_replace($regex, '', $content);
    }
     
     if($saswp_post_reviews || $saswp_elementor_faq || $saswp_divi_faq || $saswp_elementor_howto || $saswp_evo_json_ld){
     
            $saswp_json_ld =  saswp_get_all_schema_markup_output();  
     
            if(!empty($saswp_json_ld['saswp_json_ld'])){

                $regex = '/<script type\=\"application\/ld\+json\" class\=\"saswp\-schema\-markup\-output\"\>(.*?)<\/script>/s'; 
                
                $content = preg_replace($regex, $saswp_json_ld['saswp_json_ld'], $content);

            }
         
     }
     
    return $content;
}

function saswp_schema_markup_output(){
    
    $saswp_json_ld =  saswp_get_all_schema_markup_output();    
    
    if(!empty($saswp_json_ld['saswp_json_ld'])){
        
        echo "\n";
        echo "<!-- Schema & Structured Data For WP v".esc_attr(SASWP_VERSION)." - -->";
        echo "\n";
        echo $saswp_json_ld['saswp_json_ld'];
        echo "\n\n";
        
    }
    
    if(!empty($saswp_json_ld['saswp_custom_json_ld'])){
        
        echo "\n";
        echo '<!-- Schema & Structured Data For WP Custom Markup v'.esc_attr(SASWP_VERSION).' - -->';
        echo "\n";
        echo $saswp_json_ld['saswp_custom_json_ld'];
        echo "\n\n";
        
    }
        
}
/**
 * This function collects all the schema markups and show them at one place either header or footer
 * @global type $sd_data
 * @global type json array
 */
function saswp_get_all_schema_markup_output() {
       
        saswp_update_global_post();

        global $sd_data;
        global $post;
        
        $response_html = '';
        $custom_output = '';
       
        $custom_markup            = '';
        $output                   = '';
        $post_specific_enable     = '';
        $schema_output            = array();
        $kb_schema_output         = array(); 
        $item_list                = array();
        $collection_page          = array(); 
        $blog_page                = array();          
        
        $gutenberg_how_to         = array();
        $gutenberg_faq            = array();
        $elementor_faq            = array();
        $elementor_howto          = array();
        $divi_builder_faq         = array();
        $gutenberg_event          = array();
        $gutenberg_job            = array();
        $gutenberg_course         = array();
        
        if(is_singular()){

            $gutenberg_how_to         = saswp_gutenberg_how_to_schema(); 
            $gutenberg_faq            = saswp_gutenberg_faq_schema();        
            $elementor_faq            = saswp_elementor_faq_schema();
            $elementor_howto          = saswp_elementor_howto_schema();
            $divi_builder_faq         = saswp_divi_builder_faq_schema();
            $gutenberg_event          = saswp_gutenberg_event_schema();  
            $gutenberg_job            = saswp_gutenberg_job_schema();
            $gutenberg_course         = saswp_gutenberg_course_schema();

        }

        $taqeem_schema            = saswp_taqyeem_review_rich_snippet(); 
        $schema_for_faqs          = saswp_schema_for_faqs_schema();         
        $woo_cat_schema           = saswp_woocommerce_category_schema();  
        $woo_shop_page            = saswp_woocommerce_shop_page();  
        $site_navigation          = saswp_site_navigation_output();     
        $contact_page_output      = saswp_contact_page_output();  	
        $about_page_output        = saswp_about_page_output();      
        $author_output            = saswp_author_output();
        $archive_output           = saswp_archive_output();        
        $collection_output        = saswp_fetched_reviews_json_ld();
        
        if($archive_output){
            
            if(empty($woo_cat_schema)){
                $item_list            = $archive_output[0];
            }
            
            $collection_page          = isset($archive_output[1]) ? $archive_output[1]: array(); 
            $blog_page                = isset($archive_output[1]) ? $archive_output[2]: array(); 
        }
                     
        $schema_breadcrumb_output = saswp_schema_breadcrumb_output();                      
        $kb_website_output        = saswp_kb_website_output();           
        
        if((is_home() || is_front_page() || ( function_exists('ampforwp_is_home') && ampforwp_is_home())) || isset($sd_data['saswp-defragment']) && $sd_data['saswp-defragment'] == 1 ){
               $kb_schema_output         = saswp_kb_schema_output();
        }
                 
        if(is_singular()){            
            $custom_markup         = get_post_meta($post->ID, 'saswp_custom_schema_field', true);
        }
   
        $schema_output            = saswp_schema_output();      
        
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
                        if(!empty($woo_shop_page['itemlist'])){
                        
                            $output .= saswp_json_print_format($woo_shop_page['itemlist']);   
                            $output .= ",";
                            $output .= "\n\n";
                        }
                        if(!empty($woo_shop_page['collection'])){
                        
                            $output .= saswp_json_print_format($woo_shop_page['collection']);   
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
                        if(!empty($gutenberg_how_to) && is_singular()){
                        
                            $output .= saswp_json_print_format($gutenberg_how_to);   
                            $output .= ",";
                            $output .= "\n\n";
                        }
                        if(!empty($gutenberg_faq)){
                        
                            $output .= saswp_json_print_format($gutenberg_faq);   
                            $output .= ",";
                            $output .= "\n\n";
                        }
                        if(!empty($schema_for_faqs)){
                        
                            $output .= saswp_json_print_format($schema_for_faqs);   
                            $output .= ",";
                            $output .= "\n\n";
                        }
                        if(!empty($taqeem_schema)){
                        
                            $output .= saswp_json_print_format($taqeem_schema);   
                            $output .= ",";
                            $output .= "\n\n";
                        }
                        if(!empty($elementor_faq)){
                        
                            $output .= saswp_json_print_format($elementor_faq);   
                            $output .= ",";
                            $output .= "\n\n";
                        }
                        if(!empty($elementor_howto)){
                        
                            $output .= saswp_json_print_format($elementor_howto);   
                            $output .= ",";
                            $output .= "\n\n";
                        }
                        if(!empty($divi_builder_faq)){
                        
                            $output .= saswp_json_print_format($divi_builder_faq);   
                            $output .= ",";
                            $output .= "\n\n";
                        }
                        if(!empty($gutenberg_course)){
                        
                            $output .= saswp_json_print_format($gutenberg_course);   
                            $output .= ",";
                            $output .= "\n\n";
                        }
                        if(!empty($gutenberg_event)){
                        
                            $output .= saswp_json_print_format($gutenberg_event);   
                            $output .= ",";
                            $output .= "\n\n";
                        }
                        if(!empty($gutenberg_job)){
                        
                            $output .= saswp_json_print_format($gutenberg_job);   
                            $output .= ",";
                            $output .= "\n\n";
                        }
                        if(!empty($collection_output)){
                        
                            $output .= saswp_json_print_format($collection_output);   
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
                        
                        if(isset($sd_data['saswp_kb_type'])){
                           
                            $kb_schema_output['@type'] = $sd_data['saswp_kb_type'];
                            
                            if($sd_data['saswp_kb_type'] == 'Organization'){
                                $kb_schema_output['@type'] = (isset($sd_data['saswp_organization_type']) && !empty($sd_data['saswp_organization_type']) && strpos($sd_data['saswp_organization_type'], 'Organization') !== false ) ? $sd_data['saswp_organization_type'] : 'Organization';
                            }
                                                        
                        }else{
                            $kb_schema_output['@type'] = 'Organization';
                        }
                        
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
                            '@id' => isset($kb_schema_output['@id']) ? $kb_schema_output['@id'] : ''
                            );                            
                        }
                        if($sd_data['saswp_kb_type'] == 'Organization'){                                                                             
                            
                            $soutput['publisher'] = array(
                                '@id' => isset($kb_schema_output['@id']) ? $kb_schema_output['@id'] : ''
                            );
                            
                        }
                        
                    }
                                        
                    $final_output['@context']   = saswp_context_url();

                    $final_output['@graph'][]   = $kb_schema_output;
                    $final_output['@graph'][]   = $kb_website_output;

                    $final_output['@graph'][]   = $webpage;
                   
                    if($schema_breadcrumb_output){
                        $final_output['@graph'][]   = $schema_breadcrumb_output;
                    }
                    
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
                
                        $cus_regex = '/\<script type\=\"application\/ld\+json\"\>/';
                        preg_match( $cus_regex, $custom_markup, $match );
                        
                        if(empty($match)){
                            
                            $custom_output .= '<script type="application/ld+json" class="saswp-custom-schema-markup-output">';                            
                            $custom_output .= $custom_markup;                            
                            $custom_output .= '</script>';
                            
                        }else{
                            
                            $custom_output = $custom_markup;
                            $custom_output = preg_replace($cus_regex, '<script type="application/ld+json" class="saswp-custom-schema-markup-output">', $custom_output);
                            
                        }
                                                                                                                        
                                                                                                                      
            }
                                                			              		
	}
                        
        if($output){
            
            $stroutput = '['. trim($output). ']';
            $filter_string = str_replace(',]', ']',$stroutput);               
            $response_html.= '<script type="application/ld+json" class="saswp-schema-markup-output">'; 
            $response_html.= "\n";       
            $response_html.= $filter_string;       
            $response_html.= "\n";
            $response_html.= '</script>';            
        }
        
        return array('saswp_json_ld' => $response_html, 'saswp_custom_json_ld' => $custom_output);
                
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
    
    global $post;
    // Predefined words-per-minute rate.
    $words_per_minute = 225;
    $words_per_second = $words_per_minute / 60;

    // Count the words in the content.
    $word_count      = 0;
    $text            = trim( strip_tags( @get_the_content() ) );
    
    if(!$text && is_object($post)){
        $text = $post->post_content;
    }    
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
function saswp_extract_taqyeem_ratings(){
        
    global $sd_data, $post;    
    $star_rating = array();
    
    if(isset($sd_data['saswp-taqyeem']) && $sd_data['saswp-taqyeem'] == 1 && function_exists('taqyeem_review_get_rich_snippet')){
       
        $rate  = get_post_meta( $post->ID, 'tie_user_rate', true );
		$count = get_post_meta( $post->ID, 'tie_users_num', true );
         
        if( ! empty( $rate ) && ! empty( $count ) ){

            $totla_users_score = round( $rate/$count, 2 );
			$totla_users_score = ( $totla_users_score > 5 ) ? 5 : $totla_users_score;
           
            $star_rating['@type']        = 'AggregateRating';
            $star_rating['ratingValue']  = $totla_users_score;
            $star_rating['reviewCount']  = $count;                                                                                              
            
        }else{

            $total_score = (int) get_post_meta( $post->ID, 'taq_review_score', true );

            if( ! empty( $total_score ) && $total_score > 0 ){
                $total_score = round( ($total_score*5)/100, 1 );
            }

            $star_rating['@type']        = 'AggregateRating';
            $star_rating['ratingValue']  = $total_score;
            $star_rating['reviewCount']  = 1;                                                                                              

        }
        
    } 
    return $star_rating;                       
}

/**
 * Extracting the value of yet another star rating plugins on current post
 * @global type $sd_data
 * @param type $id
 * @return type array
 */
function saswp_extract_yet_another_stars_rating(){
        
    global $sd_data;    
    $result = array();

    if(isset($sd_data['saswp-yet-another-stars-rating']) && $sd_data['saswp-yet-another-stars-rating'] == 1 && method_exists('YasrDatabaseRatings', 'getVisitorVotes') ){
               
        $visitor_votes  = YasrDatabaseRatings::getVisitorVotes(false);
         
        if( $visitor_votes && ($visitor_votes['sum_votes'] != 0 && $visitor_votes['number_of_votes'] != 0) ){
           
            $average_rating = $visitor_votes['sum_votes'] / $visitor_votes['number_of_votes'];
            $average_rating = round($average_rating, 1);

            $result['@type']       = 'AggregateRating';            
            $result['ratingCount'] = $visitor_votes['number_of_votes'];
            $result['ratingValue'] = $average_rating;  
            $result['bestRating']  = 5;
            $result['worstRating'] = 1;                                                         
            
            return $result;
            
        }else{
            
            return array();    
            
        }
        
    }else{
        
        return array();
        
    }                        
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
    
    global $sd_data;
    
    $comments = array();
    $post_comments = array();   
    
    $is_bbpress = false;
    
    if(isset($sd_data['saswp-bbpress']) && $sd_data['saswp-bbpress'] == 1 && get_post_type($post_id) == 'topic'){
        $is_bbpress = true;
    }
   
    if($is_bbpress){  
                                     
              $replies_query = array(                   
                 'post_type'      => 'reply',                     
              );                
                              
             if ( bbp_has_replies( $replies_query ) ) :
                 
        while ( bbp_replies() ) : bbp_the_reply();

                    $post_comments[] = (object) array(                            
                                    'comment_date'           => get_post_time( DATE_ATOM, false, bbp_get_reply_id(), true ),
                                    'comment_content'        => bbp_get_reply_content(),
                                    'comment_author'         => bbp_get_reply_author(),
                                    'comment_author_url'     => bbp_get_reply_author_url(),
                    );
                                                                                 
            endwhile;
                    wp_reset_postdata();                                                  
                endif;
                                        
    }else{            
                    $post_comments = get_comments( array( 
                                        'post_id' => $post_id,                                            
                                        'status'  => 'approve',
                                        'type'    => 'comment' 
                                    ) 
                                );   
                                
    }                                                                                                                                                                                          
      
    if ( count( $post_comments ) ) {
        
    foreach ( $post_comments as $comment ) {
                
        $comments[] = array (
                '@type'       => 'Comment',
                'dateCreated' => $is_bbpress ? $comment->comment_date : saswp_format_date_time($comment->comment_date),
                'description' => strip_tags($comment->comment_content),
                'author'      => array (
                                                '@type' => 'Person',
                                                'name'  => esc_attr($comment->comment_author),
                                                'url'   => isset($comment->comment_author_url) ? esc_url($comment->comment_author_url): '',
                    ),
        );
    }
            
    return apply_filters( 'saswp_filter_comments', $comments );
}
    
}       
/**
 * Gets all the comments of current post
 * @param type $post_id
 * @return type array
 */       
function saswp_get_comments_with_rating(){
    
        global $sd_data, $post;
        
        $comments      = array();
        $ratings       = array();
        $post_comments = array();   
        $response      = array();
               
        $post_comments = get_comments( array( 
            'post_id' => $post->ID,                                            
            'status'  => 'approve',
            'type'    => 'comment' 
        ) 
      );                                                                                                                                                                              
          
        if ( count( $post_comments ) ) {

        $sumofrating = 0;
        $avg_rating  = 1;
            
		foreach ( $post_comments as $comment ) {                        

            $rating = get_comment_meta($comment->comment_ID, 'review_rating', true);

            if(is_numeric($rating)){

                $sumofrating += $rating;

                $comments[] = array (
					'@type'         => 'Review',
					'datePublished' => saswp_format_date_time($comment->comment_date),
					'description'   => strip_tags($comment->comment_content),
					'author'        => array (
                                            '@type' => 'Person',
                                            'name'  => esc_attr($comment->comment_author),
                                            'url'   => isset($comment->comment_author_url) ? esc_url($comment->comment_author_url): '',
                                    ),
                    'reviewRating'  => array(
                            '@type'	        => 'Rating',
                            'bestRating'	=> '5',
                            'ratingValue'	=> $rating,
                            'worstRating'	=> '1',
               )
            );
            
            if($sumofrating> 0){
                $avg_rating = $sumofrating /  count($comments); 
            }
            
            $ratings =  array(
                    '@type'         => 'AggregateRating',
                    'ratingValue'	=> $avg_rating,
                    'reviewCount'   => count($comments)
            );

            }            			
        }                
                		
    }

    if($comments){
        $response = array('reviews' => $comments, 'ratings' => $ratings);
    }
    
    return apply_filters( 'saswp_filter_comments_with_rating',  $response);        
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
 * Function generates list items for the breadcrumbs schema markup
 * @global type $sd_data
 * @return array
 */
function saswp_list_items_generator(){
    
		global $sd_data;
		$bc_titles = array();
		$bc_links  = array();
                
        if(isset($sd_data['titles']) && !empty($sd_data['titles'])){		
			$bc_titles = $sd_data['titles'];
		}
		if(isset($sd_data['links']) && !empty($sd_data['links'])){
			$bc_links = $sd_data['links'];
        }	        
                
                $j = 1;
                $i = 0;
                $breadcrumbslist = array();
                
        if(is_single()){    

			if(!empty($bc_titles) && !empty($bc_links)){      
                            
				for($i=0;$i<sizeof($bc_titles);$i++){
                                    
                                    if(array_key_exists($i, $bc_links) && array_key_exists($i, $bc_titles)){
                                    
                                        if($bc_links[$i] != '' && $bc_titles[$i] != '' ){

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
               
    }
        if(is_page()){
                        if(!empty($bc_titles) && !empty($bc_links)){
                            
                            for($i=0;$i<sizeof($bc_titles);$i++){
                            
                                if(array_key_exists($i, $bc_links) && array_key_exists($i, $bc_titles)){
        
                                    if($bc_links[$i] !='' && $bc_titles[$i] != ''){

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
			

    }
        if(is_archive()){

         if(!empty($bc_titles) && !empty($bc_links)){
             
             for($i=0;$i<sizeof($bc_titles);$i++){
                 
                    if(array_key_exists($i, $bc_links) && array_key_exists($i, $bc_titles)){
                                               
                        if($bc_links[$i] != '' && $bc_titles[$i] !=''){

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
        $content = preg_replace('/itemscope\=\"(.*?)\"/', "", $content);
        $content = preg_replace("/itemscope\='(.*?)\'/", "", $content);
        $content = preg_replace('/itemscope/', "", $content);
        $content = preg_replace('/hreview-aggregate/', "", $content);
        
        //Clean json markup
        if(isset($sd_data['saswp-aiosp']) && $sd_data['saswp-aiosp'] == 1 ){
            $content = preg_replace('/<script type=\"application\/ld\+json" class=\"aioseop-schema"\>(.*?)<\/script>/', "", $content);
        }
        
        //Clean json markup
        if(isset($sd_data['saswp-wordpress-news']) && $sd_data['saswp-wordpress-news'] == 1 ){
            $content = preg_replace("/<script type\=\'application\/ld\+json\' class\=\'wpnews-schema-graph(.*?)'\>(.*?)<\/script>/s", "", $content);
        }

        
        if(isset($sd_data['saswp-event-on']) && $sd_data['saswp-event-on'] == 1 ){
            $content = preg_replace("/<div class\=\"evo_event_schema\"(.*?)>(.*?)<\/script><\/div>/s", "", $content);
        }

        if(function_exists('review_child_company_reviews_comments') && isset($sd_data['saswp-wp-theme-reviews']) && $sd_data['saswp-wp-theme-reviews'] == 1){

            $regex = '/<\/section>[\s\n]*<script type=\"application\/ld\+json\">(.*?)<\/script>/s';

            $content = preg_replace($regex, '</section>', $content);        
            
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

        if( isset($sd_data['saswp-wordlift']) && $sd_data['saswp-wordlift'] == 1 ) {

            $regex = '/<script type=\"application\/ld\+json" id=\"wl\-jsonld"\>(.*?)<\/script>/';

            preg_match( $regex, $content, $match);
            
            if($match[1] && is_string($match[1])){
                
                $data_decode = json_decode($match[1], true);

                if($data_decode && is_array($data_decode)){

                    if(isset($data_decode[0]['@type']) && $data_decode[0]['@type'] == 'Article'){
                        $content = preg_replace($regex, '', $content);
                    }

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
                    
                    if($image_size[0] < 1200 && $image_size[1] < 720){
                                            
                        $image_details = @saswp_aq_resize( $image_url, 1200, 720, true, false, true );
                    
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
            
            if(isset($metadata['video'])){

                if(!$metadata['video']['description']){
                 $metadata['video']['description'] = saswp_get_the_excerpt();
                }
                if(!$metadata['video']['uploadDate']){
                 $metadata['video']['uploadDate'] = get_the_date('c');
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
                             'author'        => array('@type'=> 'Person', 'name' => $author),
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

function saswp_get_testimonial_pro_data($shortcode_data, $testimo_str){
        
            $reviews       = array();
            $ratings       = array();            
            
            if ( $shortcode_data['display_testimonials_from'] == 'specific_testimonials' && ! empty( $shortcode_data['specific_testimonial'] ) ) {
		    $specific_testimonial_ids = $shortcode_data['specific_testimonial'];
            } else {
                    $specific_testimonial_ids = null;
            }
            
            if ( $shortcode_data['layout'] == 'grid' && $shortcode_data['grid_pagination'] == 'true' || $shortcode_data['layout'] == 'masonry' && $shortcode_data['grid_pagination'] == 'true' || $shortcode_data['layout'] == 'list' && $shortcode_data['grid_pagination'] == 'true' ) {
				if ( is_front_page() ) {
					$paged = ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1;
				} else {
					$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
				}
				$args = array(
					'post_type'      => 'spt_testimonial',
					'orderby'        => $shortcode_data['testimonial_order_by'],
					'order'          => $shortcode_data['testimonial_order'],
					'posts_per_page' => $shortcode_data['number_of_total_testimonials'],
					'post__in'       => $specific_testimonial_ids,
					'paged'          => $paged,
				);
			} else {
				$args = array(
					'post_type'      => 'spt_testimonial',
					'orderby'        => $shortcode_data['testimonial_order_by'],
					'order'          => $shortcode_data['testimonial_order'],
					'posts_per_page' => $shortcode_data['number_of_total_testimonials'],
					'post__in'       => $specific_testimonial_ids,
				);
			}

			if ( $shortcode_data['display_testimonials_from'] == 'category' && ! empty( $shortcode_data['category_list'] ) ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'testimonial_cat',
					'field'    => 'term_id',
					'terms'    => $shortcode_data['category_list'],
					'operator' => $shortcode_data['category_operator'],
				);
			}
            
            
            

            $testimonial = get_posts( $args );
                             
            if(!empty($testimonial)){

                $sumofrating = 0;
                $avg_rating  = 1;

                foreach ($testimonial as $value){

                     $meta_option = get_post_meta($value->ID, 'sp_tpro_meta_options', true);
                     
                     $tpro_rating_star  = $meta_option['tpro_rating']; 
                                          
                     switch ( $tpro_rating_star ) {
                         
                            case 'five_star':
                                    $rating = 5;
                                    break;
                            case 'four_star':
                                    $rating = 4;
                                    break;
                            case 'three_star':
                                    $rating = 3;
                                    break;
                            case 'two_star':
                                    $rating = 2;
                                    break;
                            case 'one_star':
                                    $rating = 1;
                                    break;
                            default:
                                    $rating = 1;
                        }
                     
                     $author       = $meta_option['tpro_name'];  

                     $sumofrating += $rating;

                     $reviews[] = array(
                         '@type'         => 'Review',
                         'author'        => array('@type'=> 'Person', 'name' => $author),
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

function saswp_get_strong_testimonials_data($testimonial){
    
            $reviews = array();
            $ratings = array();
    
            if(!empty($testimonial)){

                $sumofrating = 0;
                $avg_rating  = 1;

                foreach ($testimonial as $value){
                    
                     $rating       = 5; 
                     $author       = get_post_meta($value->ID, $key='client_name', true);
                     
                     $sumofrating += $rating;

                     $reviews[] = array(
                         '@type'         => 'Review',
                         'author'        => array('@type'=> 'Person', 'name' => $author),
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
                         'author'        => array('@type'=> 'Person', 'name' => $author),
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

function saswp_get_strong_testimonials(){
    
    $testimonial = array();
    
    global $post, $sd_data;

     if(isset($sd_data['saswp-strong-testimonials']) && $sd_data['saswp-strong-testimonials'] == 1){
     
        if(is_object($post)){
         
         $pattern = get_shortcode_regex();

        if (   preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches )
            && array_key_exists( 2, $matches ) )
        {
             
           $testimo_str = ''; 
           
           if(in_array( 'testimonial_view', $matches[2] )){
               $testimo_str = 'testimonial_view';
           }
           
        if($testimo_str){
            
            foreach ($matches[0] as $matche){
             
                $mached = rtrim($matche, ']'); 
                $mached = ltrim($mached, '[');
                $mached = trim($mached);
               
                $atts   = shortcode_parse_atts('['.$mached.' ]'); 
                $atts   = array('id' => $atts['id']);
                
               
                $out = shortcode_atts(
			array(),
			$atts,
			'testimonial_view'
		);                                
                
                if(class_exists('Strong_View_Form') && class_exists('Strong_View_Slideshow') && class_exists('Strong_View_Display')){
                                    
                    switch ( $out['mode'] ) {
			case 'form' :
				$view = new Strong_View_Form( $out );
				if ( isset( $_GET['success'] ) ) {
				    $view->success();
				} else {
					$view->build();
				}
				break;
			case 'slideshow' :
				$view = new Strong_View_Slideshow( $out );
		        $view->build();
				break;
			default :
				$view = new Strong_View_Display( $out );
        		$view->build();
		        }                 
                        if(is_object($view)){
                            $testimonial = saswp_get_strong_testimonials_data($view->query->posts);
                        }
                                        
                }
                
            break;
         }
            
        }    
                               
       }
         
      }
      
     }   
         
    return $testimonial;
    
    //tomorrow will do it
    
}

function saswp_get_testomonial_pro(){
    
    $testimonial = array();
    
    global $post, $sd_data;

     if(isset($sd_data['saswp-testimonial-pro']) && $sd_data['saswp-testimonial-pro'] == 1){
     
        if(is_object($post)){
         
         $pattern = get_shortcode_regex();

        if (   preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches )
            && array_key_exists( 2, $matches ) )
        {
             
           $testimo_str = ''; 
           
           if(in_array( 'testimonial_pro', $matches[2] )){
               $testimo_str = 'testimonial_pro';
           }
           
        if($testimo_str){
            
            foreach ($matches[0] as $matche){
            
                $mached = rtrim($matche, ']'); 
                $mached = ltrim($mached, '[');
                $mached = trim($mached);
                $atts   = shortcode_parse_atts('['.$mached.' ]'); 
                
                $shortcode_data = get_post_meta( $atts['id'], 'sp_tpro_shortcode_options', true );
                                                
                if($shortcode_data){
                                
                    $testimonial = saswp_get_testimonial_pro_data($shortcode_data, $testimo_str);
                    
                }
                                                                
            break;
         }
            
        }    
                               
       }
         
      }
      
     }   
         
    return $testimonial;
    
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

function saswp_append_fetched_reviews($input1, $schema_post_id = null){
    
        global $saswp_post_reviews;
        
        $service = new saswp_reviews_service();
        
        if ( $saswp_post_reviews ){
                  
          $rv_markup = saswp_get_reviews_schema_markup(array_unique($saswp_post_reviews, SORT_REGULAR));
      
          $input1 = array_merge($input1, $rv_markup);
        
        }else{
        
          if($schema_post_id){
          
          $attached_col       = get_post_meta($schema_post_id, 'saswp_attached_collection', true);     
          $attached_rv        = get_post_meta($schema_post_id, 'saswp_attahced_reviews', true); 
          $append_reviews     = get_post_meta($schema_post_id, 'saswp_enable_append_reviews', true);
         
         if($append_reviews == 1 && ($attached_rv || $attached_col)){
             
             $total_rv = array();
             
             if($attached_rv && is_array($attached_rv)){

                foreach($attached_rv as $review_id){
                 
                    $attr['id'] =  $review_id;                  
                    $reviews = $service->saswp_get_reviews_list_by_parameters($attr);                                                      
                    $total_rv = array_merge($total_rv, $reviews);    
                   
               }

             }             
             
             if($attached_col && is_array($attached_col)){
                 
                 $total_col_rv = array();
                 
                 foreach($attached_col as $col_id){
                     
                     $collection_data = get_post_meta($col_id, $key='', true);
                     
                     if(isset($collection_data['saswp_platform_ids'][0])){
                        $platform_ids  = unserialize($collection_data['saswp_platform_ids'][0]);                
                        
                        foreach($platform_ids as $key => $val){
                            
                            $reviews_list   = $service->saswp_get_reviews_list_by_parameters(null, $key, $val); 
                            $total_col_rv   = array_merge($total_col_rv, $reviews_list);
                            
                        }
                                                
                     }
                     
                 }
                
                 $total_rv = array_merge($total_rv ,$total_col_rv);
             }
                    
             if($total_rv){
                 
                $rv_markup = saswp_get_reviews_schema_markup(array_unique($total_rv, SORT_REGULAR));
                
                if($rv_markup){
                    
                    if(isset($input1['review'])){

                    $input1['review']          = $rv_markup['review'];
                    $input1['aggregateRating'] = $rv_markup['aggregateRating'];

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

function saswp_get_mainEntity($schema_id){
    
        global $post;
        
        $response  = array();
        
        $item_list_enable     = get_post_meta($schema_id, 'saswp_enable_itemlist_schema', true);
        $item_list_tags       = get_post_meta($schema_id, 'saswp_item_list_tags', true);
        $item_list_custom     = get_post_meta($schema_id, 'saswp_item_list_custom', true); 
        
        if($item_list_enable){
            
            $listitem = array();
            
            if($item_list_tags == 'custom'){
                
                $regex = '/<([0-9a-z]*)\sclass="'.$item_list_custom.'"[^>]*>(.*?)<\/\1>/';
                
                preg_match_all( $regex, $post->post_content, $matches , PREG_SET_ORDER );
                                
                foreach($matches as $match){
                    $listitem[] = $match[2];
                }
                                                
            }else{
                                
                $regex = '/<'.$item_list_tags.'>(.*?)<\/'.$item_list_tags.'>/';
                
                preg_match_all( $regex, $post->post_content, $matches , PREG_SET_ORDER );
                
                if($matches){
                    foreach($matches as $match){
                        $listitem[] = $match[1];
                    }
                }
                
            }
                                    
            if($listitem){
                             
                    $response['@type'] = 'ItemList';
                    $response['itemListElement'] = $listitem;                 
                    $response['itemListOrder'] = 'http://schema.org/ItemListOrderAscending ';
                    $response['name']          = saswp_get_the_title();
                
            }
                                    
        }
                
        return $response;
        
}

function saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options){
            
            if(isset($schema_options['enable_custom_field']) && $schema_options['enable_custom_field'] == 1){

                if(isset($schema_options['saswp_modify_method'])){

                    if($schema_options['saswp_modify_method'] == 'automatic'){

                        $service = new saswp_output_service();
                        $input1 = $service->saswp_replace_with_custom_fields_value($input1, $schema_post_id);                                    
                    }

                    if($schema_options['saswp_modify_method'] == 'manual'){
                        
                        $all_post_meta = get_post_meta($schema_post_id, $key='', true); 
                        
                        switch ($schema_type) {
                            
                            case 'local_business':
                                                               
                                $data          = saswp_local_business_schema_markup($schema_post_id, $schema_post_id, $all_post_meta);
                                $input1        = array_merge($input1, $data);

                                break;
                            
                            case 'HowTo':
                                                                                                   
                                $data          = saswp_howto_schema_markup($schema_post_id, $schema_post_id, $all_post_meta);
                                $input1        = array_merge($input1, $data);
                            
                                break;
                            
                            case 'FAQ':
                                                                                                   
                                $data          = saswp_faq_schema_markup($schema_post_id, $schema_post_id, $all_post_meta);
                                $input1        = array_merge($input1, $data);
                            
                                break;

                            default:
                                break;
                        }
                        
                    }

                }else{
                    $service = new saswp_output_service();
                    $input1 = $service->saswp_replace_with_custom_fields_value($input1, $schema_post_id);                                    
                }

            }
        
    return $input1;
        
}

function saswp_explod_by_semicolon($data){
    
    $response = array();
    
    if($data){
        
        $explod = explode(';', $data);  
                   
        if($explod){

            foreach ($explod as $val){

                $response[] = $val;  

            }

        }         
    }    
    return $response;    
}
function saswp_get_wp_customer_reviews(){

    global $post, $sd_data, $response_rv;
    
    $reviews = array();
    $ratings = array();

    if(!$response_rv && isset($sd_data['saswp-wp-customer-reviews']) && $sd_data['saswp-wp-customer-reviews'] == 1){

        $queryOpts = array(
            'orderby'          => 'date',
            'order'            => 'DESC',        
            'post_type'        => 'wpcr3_review',
            'post_status'      => 'publish',    
            'posts_per_page'   => -1,    
        );

        if ($post->ID != -1) {
			// if $postid is not -1 (all reviews from all posts), need to filter by meta value for post id
			$meta_query = array('relation' => 'AND');
			$meta_query[] = array(
				'key' => "wpcr3_review_post",
				'value' => $post->ID,
				'compare' => '='
			);
			$queryOpts['meta_query'] = $meta_query;
		}
        
        $reviews_post = new WP_Query($queryOpts);    
        
        if($reviews_post->posts){
    
            $sumofrating = 0;
            $avg_rating  = 1;
    
            foreach ($reviews_post->posts as $value){
                
                 $meta = get_post_custom($value->ID);
                 
                 $rating       = $meta['wpcr3_review_rating'][0];              
                 
                 $sumofrating += $rating;
                    
                 $reviews[] = array(
                     '@type'         => 'Review',
                     'author'        => array('@type'=> 'Person', 'name' => $meta['wpcr3_review_name'][0]),
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
    
                $ratings =  array(
                                                '@type'         => 'AggregateRating',
                                                'ratingValue'	=> $avg_rating,
                                                'reviewCount'   => count($reviews)
                );
    
        }
        $response_rv =  array('reviews' => $reviews, 'AggregateRating' => $ratings);

    }    

    return $response_rv;

}
function saswp_get_reviews_wp_theme(){

    global $post, $sd_data, $response_rv;
    
    $reviews = array();
    $ratings = array();

    if(!$response_rv && function_exists('review_child_company_reviews_comments') && isset($sd_data['saswp-wp-theme-reviews']) && $sd_data['saswp-wp-theme-reviews'] == 1){

        $reviews_post     = get_approved_comments( $post->ID );
        
        if($reviews_post){
    
            $sumofrating = 0;
            $avg_rating  = 1;
    
            foreach ($reviews_post as $review){

                $comment_meta = get_comment_meta( $review->comment_ID, 'review', true );
                $comment_meta = explode( ',', $comment_meta );

                $user_overall = 0;
                $user_rates   = 0;
                $counter      = 0;


                $criterias = get_post_meta( get_the_ID(), 'reviews_score' );
                $rate_criterias = array();
                if( !empty( $criterias ) ){
                    foreach( $criterias as $criteria ){
                        $rate_criterias[] = $criteria['review_criteria'];
                    }
                }
                                                                    
                for( $i=0; $i<sizeof($comment_meta); $i++ ){
                    if( !empty( $rate_criterias[$i] ) ){
                        $temp = explode( '|', $comment_meta[$i] );										
                        $user_overall += $temp[1];
                        $user_rates++;

                    }
                }
                
                $user_overall = $user_overall / $user_rates;
                $rating       = round( $user_overall, 1 );                                  
                $sumofrating += round( $user_overall, 1 );
                    
                 $reviews[] = array(
                     '@type'         => 'Review',
                     'author'        => array('@type'=> 'Person', 'name' => $review->comment_author ? $review->comment_author : 'Anonymous'),
                     'datePublished' => saswp_format_date_time($review->comment_date),
                     'description'   => $review->comment_content,
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
    
                $ratings =  array(
                                                '@type'         => 'AggregateRating',
                                                'ratingValue'	=> $avg_rating,
                                                'reviewCount'   => count($reviews)
                );
    
        }else{

            $author_average = get_post_meta( get_the_ID(), 'author_average', true );
            
            $ratings =  array(
                '@type'         => 'AggregateRating',
                'ratingValue'	=> $author_average,
                'reviewCount'   => 1
            );

            $reviews[] = array(
                '@type'         => 'Review',
                'author'        => array('@type'=> 'Person', 'name' => saswp_get_the_author_name()),
                'datePublished' => get_the_date("c"),
                'description'   => saswp_get_the_excerpt(),
                'reviewRating'  => array(
                                   '@type'	        => 'Rating',
                                   'bestRating'	    => '5',
                                   'ratingValue'	=> $author_average,
                                   'worstRating'	=> '1',
                      )
            ); 
            
        }
        
        $response_rv =  array('reviews' => $reviews, 'AggregateRating' => $ratings);

    }    
    
    return $response_rv;

}
add_filter( 'the_content', 'saswp_featured_image_in_feed' );

function saswp_featured_image_in_feed( $content ) {

    global $post, $sd_data;

    if( is_feed() &&  isset($sd_data['saswp-rss-feed-image']) && $sd_data['saswp-rss-feed-image'] == 1 ) {
        if ( has_post_thumbnail( $post->ID ) ){
            $image  = get_the_post_thumbnail( $post->ID, 'full', array( 'style' => 'float:right; margin:0 0 10px 10px;' ) );
            $content = $image . $content;
        }
    }

    return $content;
    
}
function saswp_get_loop_markup($i) {

    global $sd_data;

    $response = array();
    $site_name ='';

    $schema_type        =  $sd_data['saswp_archive_schema_type'];

    if(isset($sd_data['sd_name']) && $sd_data['sd_name'] !=''){
        $site_name = $sd_data['sd_name'];  
    }else{
        $site_name = get_bloginfo();    
    }

    $schema_properties = array();

    $service_object     = new saswp_output_service();
    $logo               = $service_object->saswp_get_publisher(true);   
    $feature_image      = $service_object->saswp_get_fetaure_image();             
                                                                                                      
    $publisher_info['type']           = 'Organization';                                
    $publisher_info['name']           = esc_attr($site_name);
    $publisher_info['logo']['@type']  = 'ImageObject';
    $publisher_info['logo']['url']    = isset($logo['url'])    ? esc_attr($logo['url']):'';
    $publisher_info['logo']['width']  = isset($logo['width'])  ? esc_attr($logo['width']):'';
    $publisher_info['logo']['height'] = isset($logo['height']) ? esc_attr($logo['height']):'';
                                                                                                                                                                    
    $schema_properties['@type']            = esc_attr($schema_type);
    $schema_properties['headline']         = saswp_get_the_title();
    $schema_properties['url']              = get_the_permalink();                                                                                                
    $schema_properties['datePublished']    = get_the_date('c');
    $schema_properties['dateModified']     = get_the_modified_date('c');
    $schema_properties['mainEntityOfPage'] = get_the_permalink();
    $schema_properties['author']           = get_the_author();
    $schema_properties['publisher']        = $publisher_info;                                
      
    if(!empty($feature_image)){                            
        $schema_properties = array_merge($schema_properties, $feature_image);        
    }

    $itemlist_arr = array(
                '@type' 		    => 'ListItem',
                'position' 		    => $i,
                'url' 		        => get_the_permalink(),                
        );    
    $response = array('schema_properties' => $schema_properties, 'itemlist' => $itemlist_arr);

    return $response;
}
