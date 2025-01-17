<?php 
/**
 * Service Class
 *
 * @author   Magazine3
 * @category Frontend
 * @path  output/service
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

Class SASWP_Output_Service{           
        
        /**
         * List of hooks used in current class
         */
        public function saswp_service_hooks() {
            
           add_action( 'wp_ajax_saswp_get_custom_meta_fields', array($this, 'saswp_get_custom_meta_fields')); 
           add_action( 'wp_ajax_saswp_get_schema_type_fields', array($this, 'saswp_get_schema_type_fields'));            
           add_action( 'wp_ajax_saswp_get_meta_list', array($this, 'saswp_get_meta_list'));            
           add_filter( 'saswp_modify_post_meta_list', array( $this, 'saswp_get_acf_meta_keys' ) );
           add_filter( 'saswp_modify_post_meta_list', array( $this, 'saswp_get_cpt_meta_keys' ) ); // Custom Post Types
           add_filter( 'saswp_modify_custom_fields_group', array( $this, 'saswp_modify_custom_fields_group_clbk' ),10,3 ); // Custom Post Types
           
        }    
        /**
         * Function to get acf meta keys
         * @param type $fields
         * @return type array
         * @since version 1.9.3
         */     
        public function saswp_get_acf_meta_keys($fields){
            
            if ( function_exists( 'acf' ) && class_exists( 'acf' ) ) {

				$post_type = 'acf';
				if ( ( defined( 'ACF_PRO' ) && ACF_PRO ) || ( defined( 'ACF' ) && ACF ) ) {
					$post_type = 'acf-field-group';
				}
				$text_acf_field  = array();
				$image_acf_field = array();
				$args            = array(
					'post_type'      => $post_type,
					'posts_per_page' => -1,
					'post_status'    => 'publish',
				);

				$the_query = new WP_Query( $args );
				if ( $the_query->have_posts() ) :
					while ( $the_query->have_posts() ) :
						$the_query->the_post();

						$post_id = get_the_ID();
						
						$acf_fields = apply_filters( 'acf/field_group/get_fields', array(), $post_id ); // WPCS: XSS OK.						

						if ( 'acf-field-group' == $post_type ) {
							$acf_fields = acf_get_fields( $post_id );
						}

						if ( is_array( $acf_fields ) && ! empty( $acf_fields ) ) {
							foreach ( $acf_fields as $key => $value ) {

								if ( 'image' == $value['type'] ) {
									$image_acf_field[ $value['name'] ] = $value['label'];
								} else {
									$text_acf_field[ $value['name'] ] = $value['label'];
								}
							}
						}
					endwhile;
				endif;
				wp_reset_postdata();

				if ( ! empty( $text_acf_field ) ) {
					$fields['text'][] = array(
						'label'     => __( 'Advanced Custom Fields', 'schema-and-structured-data-for-wp' ),
						'meta-list' => $text_acf_field,
					);
				}

				if ( ! empty( $image_acf_field ) ) {
					$fields['image'][] = array(
						'label'     => __( 'Advanced Custom Fields', 'schema-and-structured-data-for-wp' ),
						'meta-list' => $image_acf_field,
					);
				}
			}

            if ( is_plugin_active('faq-schema-compatibility/faq-schema-compatibility.php') ) {
                $fields['text'][] = array(
                    'label'     => __( 'Repeater Mapping', 'schema-and-structured-data-for-wp' ),
                    'meta-list' => array('saswp_repeater_mapping' => 'Repeater Mapping'),
                );
            }
            
			return $fields;
            
        }
        /**
         * Ajax function to get meta list 
         * @return type json
         */
        public function saswp_get_meta_list() {
            
            if ( ! isset( $_GET['saswp_security_nonce'] ) ){
                return; 
             }
             if ( !wp_verify_nonce( $_GET['saswp_security_nonce'], 'saswp_ajax_check_nonce' ) ){
                return;  
             }
            if(!current_user_can( saswp_current_user_can()) ) {
                die( '-1' );    
            }
            
            $response = array();    
            $mappings_file = SASWP_DIR_NAME . '/core/array-list/meta-list.php';

            if ( file_exists( $mappings_file ) ) {
                $response = include $mappings_file;
            }  
                         
            wp_send_json( $response); 
                        
        }
        
        /**
         * @since version 1.9.1
         * This function replaces the value of schema's fields with the selected custom meta field
         * @param type $input1
         * @param type $schema_post_id
         * @return type array or string
         */        
        public function saswp_get_meta_list_value($key, $field, $schema_post_id, $schema_type){
            
            global $post;
            
            $fixed_image       = get_post_meta($schema_post_id, 'saswp_fixed_image', true) ;            
                        
            $response = null;
            
            switch ($field) {
                case 'blogname':
                    $response   = get_bloginfo();                    
                break;
                case 'blogname':
                    $response   = get_bloginfo();                    
                    break;
                case 'blogdescription':
                    $response  = get_bloginfo('description');                    
                    break;
                case 'site_url':
                    $response = get_site_url();                    
                    break;
                case 'post_title':
                    $response = get_the_title(); 
                    if(empty($response) ) {
                        $response = get_the_title(get_the_ID());
                    }                   
                    break;
                case 'post_content':
                    $response = get_the_content();
                    if($schema_type == 'JobPosting'){
                        $response = strip_shortcodes($response);
                    }else{
					   $response = wp_strip_all_tags(strip_shortcodes($response)); 
                    }                   
                    break;
                case 'post_category':
                    $categories = get_the_category();
                    if($categories){
                        foreach ( $categories as $category){
                            if ( isset( $category->name) ) {
                              $response[] = $category->name;  
                            }
                        }
                        
                    }                                           
                    break;
                case 'post_excerpt':
                    $response = saswp_get_the_excerpt(); 
                    break;
                case 'post_permalink':
                    $response = saswp_get_permalink();
                    break;
                case 'author_name':
                    $response =  get_the_author_meta('first_name').' '.get_the_author_meta('last_name');
                    break;
                case 'author_first_name':
                    $response = get_the_author_meta('first_name'); 
                    break;
                case 'author_last_name':
                    $response = get_the_author_meta('last_name');
                    break;
                case 'post_date':
                    $response = get_the_date("c");
                    break;
                case 'post_modified':
                    $response = get_the_modified_date("c");
                    break;
                case 'manual_text':    
                    
                    $fixed_text        = get_post_meta($schema_post_id, 'saswp_fixed_text', true) ; 

                    if ( isset( $fixed_text[$key]) ) {
                        
                        $explod = explode('.', $fixed_text[$key]);                        
                        $ext    = strtolower(end($explod));           

                        if ($ext == 'jpg' || $ext == 'png' || $ext == 'gif' || $ext == 'jpeg') {
                        
                        $image_details = getimagesize($fixed_text[$key]);
                        
                        if ( is_array( $image_details) ) {
                            $response['@type']  = 'ImageObject';
                            $response['url']    = $fixed_text[$key];
                            $response['width']  = $image_details[0]; 
                            $response['height'] = $image_details[1];
                        }else{
                            $response    = $fixed_text[$key];                  
                        }
                                                
                        }else{
                            $response    = $fixed_text[$key];                    
                        }
                        
                    }
                    
                    break;
                
                case 'taxonomy_term':    
                    
                    $response = null;
                    
                    $taxonomy_term       = get_post_meta( $schema_post_id, 'saswp_taxonomy_term', true) ; 
                                        
                    if($taxonomy_term[$key] == 'all'){
                        
                        $post_taxonomies      = get_post_taxonomies( $post->ID );
                                                
                        if($post_taxonomies){
                            
                            foreach ( $post_taxonomies as $taxonomie ){
                                
                                $terms               = get_the_terms( $post->ID, $taxonomie);
                                
                                if($terms){
                                    foreach ( $terms as $term){
                                        $response .= $term->name.', ';
                                    }    
                                }
                                
                            }
                            
                        }

                        if($response){
                            $response = substr(trim($response), 0, -1); 
                        }

                    }else{
                    
                        if(strpos($key, "global_mapping") == true && $key == "saswp_webpage_reviewed_by"){

                            $terms               = get_the_terms( $post->ID, $taxonomy_term[$key]);

                            if(count($terms) == 1){

                                foreach ( $terms as $term){
                                
                                    $saveas = array();
    
                                    $facebook = get_term_meta($term->term_id, 'author_facebook', true);
                                    $twitter  = get_term_meta($term->term_id, 'author_twitter', true);
                                    $linkedin = get_term_meta($term->term_id, 'author_linkedin', true);
                                    $a_site   = get_term_meta($term->term_id, 'author_site', true);
                                    $img_id   = get_term_meta($term->term_id, 'cfe_author_image_id', true);  
                                                                  
                                    $image_details   = saswp_get_image_by_id($img_id); 
    
                                    if($facebook || $twitter || $linkedin || $a_site){
                                        if($facebook){
                                            $response['custom_fields']['team_facebook'][0] = $facebook;
                                        }
                                        if($twitter){
                                            $response['custom_fields']['team_twitter'][0] = $twitter;
                                        }
                                        if($linkedin){
                                            $response['custom_fields']['team_linkedin'][0] = $linkedin;
                                        }                                        
                                        
                                    }
                                        
                                    $response['name']        = $term->name;
                                    $response['url']         = get_home_url().'/author/'.$term->slug;
                                    $response['description'] = $term->description;
                                    if($image_details){
                                        $response['image'] = $image_details;
                                    }                                    
    
                                }

                            }

                        }elseif(strpos($key, "global_mapping") === true && $key == "saswp_itempage_reviewed_by"){

                            $terms               = get_the_terms( $post->ID, $taxonomy_term[$key]);

                            if(count($terms) == 1){

                                foreach ( $terms as $term){
                                
                                    $saveas = array();
    
                                    $facebook = get_term_meta($term->term_id, 'author_facebook', true);
                                    $twitter  = get_term_meta($term->term_id, 'author_twitter', true);
                                    $linkedin = get_term_meta($term->term_id, 'author_linkedin', true);
                                    $a_site   = get_term_meta($term->term_id, 'author_site', true);
                                    $img_id   = get_term_meta($term->term_id, 'cfe_author_image_id', true);  
                                                                  
                                    $image_details   = saswp_get_image_by_id($img_id); 
    
                                    if($facebook || $twitter || $linkedin || $a_site){
                                        if($facebook){
                                            $response['custom_fields']['team_facebook'][0] = $facebook;
                                        }
                                        if($twitter){
                                            $response['custom_fields']['team_twitter'][0] = $twitter;
                                        }
                                        if($linkedin){
                                            $response['custom_fields']['team_linkedin'][0] = $linkedin;
                                        }                                        
                                        
                                    }
                                        
                                    $response['name']        = $term->name;
                                    $response['url']         = get_home_url().'/author/'.$term->slug;
                                    $response['description'] = $term->description;
                                    if($image_details){
                                        $response['image'] = $image_details;
                                    }                                    
    
                                }

                            }
                        
                        }else{

                            $terms               = get_the_terms( $post->ID, $taxonomy_term[$key]);
                        
                            if($terms){
                                foreach ( $terms as $term){
                                    $response .= $term->name.', ';
                                }    
                            }
                        
                            if($response){
                                $response = substr(trim($response), 0, -1); 
                            }
                        }                        
                    }
                                                                                                    
                    
                                                            
                    break;
                    
                case 'custom_field':
                    
                    $cus_field   = get_post_meta($schema_post_id, 'saswp_custom_meta_field', true);  
                    
                    if(strpos($cus_field[$key], "aioseo_posts_") !== false && function_exists('aioseo') ) {

                        $column_name = str_replace('aioseo_posts_', '', $cus_field[$key]);
                        $metaData    = aioseo()->meta->metaData->getMetaData();
                           
                        switch ($column_name) {

                            case 'title':
                                $response = aioseo()->meta->title->getTitle();   
                                break;
                            
                            case 'description':
                                $response = aioseo()->meta->description->getDescription();   
                                break;
                                    
                            case 'keywords':
                                $response = aioseo()->meta->keywords->getKeywords();  
                                break;                     
                                                        
                            default:

                                if ( isset( $metaData->$column_name) ) {
                                    $response = $metaData->$column_name;
                                }                                
                                
                                break;
                        }
                        

                    }elseif ( strpos( $cus_field[$key], "rank_math_" ) !== false && class_exists('RankMath\Helper') ) {

                        $column_name = str_replace('rank_math_', '', $cus_field[$key]);
                        $term_id        =   '';
                        $term_taxonomy  =   '';
                        if ( is_category() || is_tag() || ( function_exists( 'is_product_category' ) && is_product_category() ) ) {
                            $query_obj  =   get_queried_object();
                            if ( ! empty( $query_obj ) && is_object( $query_obj ) && ! empty( $query_obj->term_id ) ) { 
                                $term_id            =   $query_obj->term_id;
                                $term_taxonomy      =   $query_obj->taxonomy;
                            }
                        }

                        if( ( is_category() || is_tag() || ( function_exists( 'is_product_category' ) && is_product_category() ) ) && $term_id > 0 ) {
                            $response       =   get_term_meta( $term_id, $cus_field[$key], true );
                            $rank_data      =   RankMath\Helper::replace_vars( $response, get_term( $term_id, $term_taxonomy ) );
                            if ( ! empty( $rank_data ) && is_string( $rank_data ) ) {
                                $response   =   $rank_data;
                            }
                        }else{
                            $response       =   get_post_meta($post->ID, $cus_field[$key], true);  
                            $rank_data      =   RankMath\Helper::replace_vars( $response, get_post( $post->ID ) );
                            if ( ! empty( $rank_data ) && is_string( $rank_data ) ) {
                                $response   =   $rank_data;
                            }    
                        }

                    }elseif(strpos($key, "global_mapping") === true && $key == "saswp_webpage_reviewed_by"){
                            
                        if($key == "saswp_webpage_reviewed_by"){
                            $tema_id    = get_post_meta($post->ID, "reviewed_by", true);
                        }else{
                            $tema_id    = get_post_meta($post->ID, $cus_field[$key], true);
                        }
                        if($tema_id && is_numeric($tema_id) ) {
                            
                            $response['@type'] =   "Person"; 
                            $response['name'] = get_the_title($tema_id);
                            $response['url'] = get_permalink($tema_id);
                            $response['description'] =   wp_trim_words(get_post_field('post_content', $tema_id));
                            $response['custom_fields'] = get_post_meta($tema_id);
                            $response['custom_fields']['reviewer_image'] =  get_the_post_thumbnail_url($tema_id);
                           
                        }else{
                            if ( ! empty( $tema_id) ) {
                                $response['@type'] =   "Person"; 
                                $response['name'] = get_the_title($tema_id);
                                $response['url'] = get_permalink($tema_id);
                                $response['description'] =   wp_trim_words(get_post_field('post_content', $tema_id));
                                if ( ! empty( $cus_field[$key]) ) {
                                    $response = get_post_meta($post->ID, $cus_field[$key], true); 
                                } 
                                $response['custom_fields'] = get_post_meta($tema_id); 
                                $response['custom_fields']['reviewer_image'] =  get_the_post_thumbnail_url($tema_id);
                            }
                        }
                  
                    }elseif(strpos($key, "global_mapping") === true && $key == "saswp_itempage_reviewed_by"){
                            
                        if($key == "saswp_itempage_reviewed_by"){
                            $tema_id    = get_post_meta($post->ID, "reviewed_by", true);
                        }else{
                            $tema_id    = get_post_meta($post->ID, $cus_field[$key], true);
                        }
                        if($tema_id && is_numeric($tema_id) ) {
                            
                            $response['@type'] =   "Person"; 
                            $response['name'] = get_the_title($tema_id);
                            $response['url'] = get_permalink($tema_id);
                            $response['description'] =   wp_trim_words(get_post_field('post_content', $tema_id));
                            $response['custom_fields'] = get_post_meta($tema_id);
                            $response['custom_fields']['reviewer_image'] =  get_the_post_thumbnail_url($tema_id);
                           
                        }else{
                            if ( ! empty( $tema_id) ) {
                                $response['@type'] =   "Person"; 
                                $response['name'] = get_the_title($tema_id);
                                $response['url'] = get_permalink($tema_id);
                                $response['description'] =   wp_trim_words(get_post_field('post_content', $tema_id));
                                if ( ! empty( $cus_field[$key]) ) {
                                    $response = get_post_meta($post->ID, $cus_field[$key], true); 
                                } 
                                $response['custom_fields'] = get_post_meta($tema_id); 
                                $response['custom_fields']['reviewer_image'] =  get_the_post_thumbnail_url($tema_id);
                            }
                        }
                  
                    }else{

                        if($post){
                            if(strpos($cus_field[$key], "image") !== false){
                                $response    = get_post_meta($post->ID, $cus_field[$key], true);                         
                                if(is_numeric($response) ) {
                                    $response = saswp_get_image_by_id($response);
                                }else{
                                    $response    = get_post_meta($post->ID, $cus_field[$key], true);     
                                }
                            }else{

                                if($key == 'saswp_faq_main_entity'){

                                    $response = apply_filters('saswp_faq_custom_field_modify', $post->ID, $cus_field[$key]);
                                    																		
								}else{
									$response    = get_post_meta($post->ID, $cus_field[$key], true); 	
								}
                            }
                        }

                        if( ( is_category() || is_tag() ) ) {

                            $query_obj  =   get_queried_object();

                            if( ! empty( $query_obj ) && is_object( $query_obj ) && ! empty( $query_obj->term_id ) ) {

                                $term_id            =   $query_obj->term_id;
                                $term_taxonomy      =   $query_obj->term_id;

                                if ( strpos( $cus_field[$key], "_yoast_wpseo_")  !== false && class_exists( 'WPSEO_Taxonomy_Meta' ) ) {

                                    $column_name = str_replace('_yoast_wpseo_', '', $cus_field[$key]);

                                    switch ( $column_name ) {
                                        case 'focuskw':
                                                $response   =   WPSEO_Taxonomy_Meta::get_term_meta( $term_id, $query_obj->taxonomy, $column_name );
                                            break;

                                    }

                                }

                            } 

                        }

                    }
                                                                                                    
                    break;
                case 'no_image':                    
                
                    $response['no_image']  = true;                    
                    
                    break;
                case 'fixed_image':                    
                    
                    $response['@type']  = 'ImageObject';
                    $response['url']    = $fixed_image[$key]['thumbnail'];
                    $response['width']  = $fixed_image[$key]['width']; 
                    $response['height'] = $fixed_image[$key]['height'];
                    
                    break;
                    
                case 'featured_img':                    
                    $image_id 	        = get_post_thumbnail_id();
                    $response           = saswp_get_image_by_id($image_id);                                        
                    
                    break;
                case 'author_image':
                    $author_image       = array();
                    $author_id          = get_the_author_meta('ID');
                    
                    if ( function_exists( 'get_avatar_data') &&  ! empty( get_option( 'show_avatars' ) ) ) {
                        $author_image	= get_avatar_data($author_id);      
                    }                                                          
                    $response['@type']  = 'ImageObject';
                    $response['url']    = $author_image['url'];
                    $response['width']  = $author_image['height']; 
                    $response['height'] = $author_image['width'];

                    break;
                case 'site_logo':
                    
                    $sizes = array ( 600, 60 ); 

                    $custom_logo_id = get_theme_mod( 'custom_logo' );     

                    if($custom_logo_id){

                        $custom_logo    = wp_get_attachment_image_src( $custom_logo_id, $sizes);

                    }

                    if ( isset( $custom_logo) && is_array($custom_logo) ) {

                         $response['@type']  = 'ImageObject';
                         $response['url']    = array_key_exists(0, $custom_logo)? $custom_logo[0]:'';
                         $response['width']  = array_key_exists(2, $custom_logo)? $custom_logo[2]:''; 
                         $response['height'] = array_key_exists(1, $custom_logo)? $custom_logo[1]:'';
                                              
                    }
                break;
                case 'saswp_repeater_mapping':
                    switch ($schema_type) {
                        case 'FAQ':
                            $field_key = 'faq_repeater_question_'.$schema_post_id;
                            $response = apply_filters('saswp_faq_acf_repeater_mapping', $post->ID, $schema_post_id, $field_key);
                        break;
                    }    
                break;                    
                case 'saswp_schema_template':

                    $response = saswp_get_schema_template_markup( $schema_post_id, $key );

                break;                   
                default:
                    if ( function_exists( 'get_field_object') ) {
                     
                        $acf_obj = get_field_object($field);
                        if(is_archive() ) {
                            $term_id = get_queried_object_id(); // Get the current term's ID
                            $acf_obj = get_field_object($field, 'term_' . $term_id);
                        }
                                            
                        if($acf_obj){

                            if($acf_obj['type'] == 'image'){
                                
                                $image_id           = get_post_meta($post->ID, $field, true );                                
                                $response           = saswp_get_image_by_id($image_id);                    
                                                                                                            
                            }elseif($acf_obj['type'] == 'repeater'){
                                                                                                
                                switch ($schema_type) {

                                    case 'FAQ':
                                                                                                                        
                                        if ( ! empty( $acf_obj['value']) ) {

                                            foreach( $acf_obj['value'] as $value){

                                                $main_entity = array();

                                                $ar_values = array_values($value);
                                                
                                                $main_entity['@type']                   = 'Question';
                                              
                                                if ( ! empty( $ar_values[0]) ) {
                                                    $main_entity['name'] = $ar_values[0]; 
                                                }
                                                $main_entity['acceptedAnswer']['@type'] = 'Answer';
                                                if ( ! empty( $ar_values[1]) ) {
                                                    $main_entity['acceptedAnswer']['text'] = $ar_values[1];
                                                }
                                                if ( ! empty( $ar_values[2]['url']) ) {
                                                    $main_entity['acceptedAnswer']['image'] = $ar_values[2]['url'];
                                                }
                                                
                                                $response [] = $main_entity;                                   
                                               
                                            }
                                            
                                        }

                                        break;

                                        case 'HowTo':
                                                                                        
                                            if(strpos($acf_obj['name'], "tool") !== false){

                                                if ( ! empty( $acf_obj['value']) ) {
    
                                                    foreach( $acf_obj['value'] as $value){
        
                                                        $main_entity = array();
        
                                                        $ar_values = array_values($value);
                                                        
                                                        $main_entity['@type']                   = 'HowToTool';
                                                      
                                                        if ( ! empty( $ar_values[0]) ) {
                                                            $main_entity['name'] = $ar_values[0]; 
                                                        }
                                                        if ( ! empty( $ar_values[1]) ) {
                                                            $main_entity['url'] = $ar_values[1]; 
                                                        }                                                        
                                                        if ( ! empty( $ar_values[2]['url']) ) {
                                                            $main_entity['image'] = $ar_values[2]['url'];
                                                        }
                                                        
                                                        $response [] = $main_entity;                                   
                                                       
                                                    }
                                                    
                                                }

                                            }

                                            if(strpos($acf_obj['name'], "supp") !== false){
                                                
                                                if ( ! empty( $acf_obj['value']) ) {
    
                                                    foreach( $acf_obj['value'] as $value){
        
                                                        $main_entity = array();
        
                                                        $ar_values = array_values($value);
                                                        
                                                        $main_entity['@type']                   = 'HowToSupply';
                                                      
                                                        if ( ! empty( $ar_values[0]) ) {
                                                            $main_entity['name'] = $ar_values[0]; 
                                                        }
                                                        if ( ! empty( $ar_values[1]) ) {
                                                            $main_entity['url'] = $ar_values[1]; 
                                                        }                                                        
                                                        if ( ! empty( $ar_values[2]['url']) ) {
                                                            $main_entity['image'] = $ar_values[2]['url'];
                                                        }
                                                        
                                                        $response [] = $main_entity;                                   
                                                       
                                                    }
                                                    
                                                }

                                            }

                                            if(strpos($acf_obj['name'], "step") !== false){

                                                if ( ! empty( $acf_obj['value']) ) {
    
                                                    foreach( $acf_obj['value'] as $value){
        
                                                        $main_entity = array();
        
                                                        $ar_values = array_values($value);
                                                        
                                                        $main_entity['@type']                   = 'HowToStep';
                                                      
                                                        if ( ! empty( $ar_values[0]) ) {
                                                            $main_entity['name'] = $ar_values[0]; 
                                                        }
                                                        if ( ! empty( $ar_values[1]) ) {
                                                            $main_entity['url'] = $ar_values[1]; 
                                                        }
                                                        if ( ! empty( $ar_values[2]) ) {
                                                            $main_entity['text'] = $ar_values[2]; 
                                                        }                                                        
                                                        if ( ! empty( $ar_values[3]['url']) ) {
                                                            $main_entity['image'] = $ar_values[3]['url'];
                                                        }
                                                        
                                                        $response [] = $main_entity;                                   
                                                       
                                                    }
                                                    
                                                }

                                            }
                                                
                                            break;
                                        
                                    
                                    default:

                                        if ( isset( $acf_obj['value']) ) {
                                            foreach( $acf_obj['value'] as $value){
                                                foreach ( $value as $val){
                                                $response[] = $val;   
                                                }
                                            }
                                        }   

                                        break;
                                }                             
                                                                
                            }else{
                                $response = get_post_meta($post->ID, $field, true );
                            }

                        }else{
                            if(is_object($post) && isset($post->ID) ) {
                                $response = get_post_meta($post->ID, $field, true );
                            }
                        }
                        
                    }else{
                        $response = get_post_meta($post->ID, $field, true );
                    }                    
                    
                    $response = apply_filters('saswp_modify_custom_fields_group', $response, $field, $schema_type);                    
                    
                    break;
            }
            
            return $response;
            
        }
        /**
         * Function to replace schema markup fields value with custom value enter or selected by users while modifying globally
         * @param type $input1
         * @param type $schema_post_id
         * @return type array
         */
        public function saswp_replace_with_custom_fields_value($input1, $schema_post_id){
                                                 
            $custom_fields    = get_post_meta($schema_post_id, 'saswp_meta_list_val', true);            
            $allowed_html     = saswp_expanded_allowed_tags();
            $review_markup    = array();
            $review_response  = array();
            $main_schema_type = '';
                                                          
            if ( ! empty( $custom_fields) ) {

                $schema_type      = get_post_meta( $schema_post_id, 'schema_type', true);     

                foreach ( $custom_fields as $key => $field){
                                                                                                                                         
                    $custom_fields[$key] = $this->saswp_get_meta_list_value($key, $field, $schema_post_id, $schema_type);                                           
                                                           
                }   
                                                                            
                if($schema_type == 'Review' || $schema_type == 'ReviewNewsArticle'){

                    $main_schema_type = $schema_type;                                                                                  
                    $schema_type = get_post_meta($schema_post_id, 'saswp_review_item_reviewed_'.$schema_post_id, true);
                                        
                    if ( isset( $custom_fields['saswp_review_name']) ) {
                        $review_markup['name']                       =    $custom_fields['saswp_review_name'];
                    }
                    if ( isset( $custom_fields['saswp_review_url']) ) {
                        $review_markup['url']                       =    saswp_validate_url($custom_fields['saswp_review_url']);
                    }
                    if ( isset( $custom_fields['saswp_review_description']) ) {
                        $review_markup['description']                =    $custom_fields['saswp_review_description'];
                    }
                    if ( isset( $custom_fields['saswp_review_body']) ) {
                        $review_markup['reviewBody']                 =    $custom_fields['saswp_review_body'];
                    }
                    if ( isset( $custom_fields['saswp_review_rating_value']) ) {
                       $review_markup['reviewRating']['@type']       =   'Rating';                                              
                       $review_markup['reviewRating']['ratingValue'] =    $custom_fields['saswp_review_rating_value'];
                       $review_markup['reviewRating']['bestRating']  =   5;
                       $review_markup['reviewRating']['worstRating'] =   1;
                    }
                    if ( isset( $custom_fields['saswp_review_publisher']) ) {
                       $review_markup['publisher']['@type']          =   'Organization';                                              
                       $review_markup['publisher']['name']           =    $custom_fields['saswp_review_publisher'];                                              
                       if ( isset( $custom_fields['saswp_review_publisher_url']) && saswp_validate_url($custom_fields['saswp_review_publisher_url']) ){
                        $review_markup['publisher']['sameAs'] =    array($custom_fields['saswp_review_publisher_url']);
                       }
                    }                    
                    if ( isset( $custom_fields['saswp_review_author']) ) {

                       $review_markup['author']['@type']             =   'Person'; 
                       
                       if ( isset( $custom_fields['saswp_review_author_type']) ) {
                            $review_markup['author']['@type']             =   $custom_fields['saswp_review_author_type']; 
                       }
                       
                       $review_markup['author']['name']              =    $custom_fields['saswp_review_author'];                                              
                       
                        if ( isset( $custom_fields['saswp_review_author_url']) && saswp_validate_url($custom_fields['saswp_review_author_url']) ){
                            $review_markup['author']['sameAs'] =    array($custom_fields['saswp_review_author_url']);
                        }
                    }
                     if ( isset( $custom_fields['saswp_review_date_published']) ) {

                        if(saswp_validate_date($custom_fields['saswp_review_date_published'], 'Y-m-d\TH:i:sP') ) {
                            $review_markup['datePublished'] =    $custom_fields['saswp_review_date_published'];
                        }else{
                            $review_markup['datePublished'] =    gmdate('c',strtotime($custom_fields['saswp_review_date_published']));
                        }
                       
                    }
                    
                    if ( isset( $custom_fields['saswp_review_date_modified']) ) {

                        if(saswp_validate_date($custom_fields['saswp_review_date_modified'], 'Y-m-d\TH:i:sP') ) {
                            $review_markup['dateModified'] =    $custom_fields['saswp_review_date_modified'];
                        }else{
                            $review_markup['dateModified'] =    gmdate('c',strtotime($custom_fields['saswp_review_date_modified']));
                        }
                       
                    }

                }
                                   
             switch ($schema_type) {
                 
               case 'Book':      
                    if ( isset( $custom_fields['saswp_book_id']) ) {
                        $input1['@id'] =    get_permalink().$custom_fields['saswp_book_id'];
                    }  
                    if ( isset( $custom_fields['saswp_book_name']) ) {
                     $input1['name'] =    $custom_fields['saswp_book_name'];
                    }
                    if ( isset( $custom_fields['saswp_book_description']) ) {
                     $input1['description'] =    wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_book_description'] ));
                    }
                    if ( isset( $custom_fields['saswp_book_url']) ) {
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_book_url']);
                    }
                    if ( isset( $custom_fields['saswp_book_author']) ) {
                     $input1['author']['@type'] =    'Person';   

                     if ( isset( $custom_fields['saswp_book_author_type']) ) {
                        $input1['author']['@type'] =   $custom_fields['saswp_book_author_type'];   
                     }

                     $input1['author']['name']  =    $custom_fields['saswp_book_author'];
                    
                        if ( isset( $custom_fields['saswp_book_author_url']) && saswp_validate_url( $custom_fields['saswp_book_author_url'] ) ){
                            $input1['author']['sameAs'] =    array($custom_fields['saswp_book_author_url']);
                        }
                     
                    }
                    if ( isset( $custom_fields['saswp_book_inlanguage']) ) {
                        $input1['inLanguage'] =    $custom_fields['saswp_book_inlanguage'];
                    }
                    if ( isset( $custom_fields['saswp_book_format']) ) {
                        $input1['bookFormat'] =    $custom_fields['saswp_book_format'];
                    }
                    if ( isset( $custom_fields['saswp_book_isbn']) ) {
                     $input1['isbn'] =    $custom_fields['saswp_book_isbn'];
                    }
                    if ( isset( $custom_fields['saswp_book_publisher']) ) {
                     $input1['publisher'] =    $custom_fields['saswp_book_publisher'];
                    }
                    if ( isset( $custom_fields['saswp_book_no_of_page']) ) {
                     $input1['numberOfPages'] =    $custom_fields['saswp_book_no_of_page'];
                    }
                    if ( isset( $custom_fields['saswp_book_image']) ) {
                     $input1['image']         =    $custom_fields['saswp_book_image'];
                    }
                    if ( isset( $custom_fields['saswp_book_date_published']) ) {                        
                     $input1['datePublished'] =    gmdate('c',strtotime($custom_fields['saswp_book_date_published']));
                    }                    
                    if ( isset( $custom_fields['saswp_book_price_currency']) && isset($custom_fields['saswp_book_price']) ) {
                        $input1['offers']['@type']         = 'Offer';
                        $input1['offers']['availability']  = $custom_fields['saswp_book_availability'];
                        $input1['offers']['price']         = $custom_fields['saswp_book_price'];
                        $input1['offers']['priceCurrency'] = $custom_fields['saswp_book_price_currency'];
                    }                            
                    if ( isset( $custom_fields['saswp_book_rating_value']) && isset($custom_fields['saswp_book_rating_count']) ) {
                        $input1['aggregateRating']['@type']         = 'aggregateRating';
                        $input1['aggregateRating']['worstRating']   =   0;
                        $input1['aggregateRating']['bestRating']    =   5;
                        $input1['aggregateRating']['ratingValue']   = $custom_fields['saswp_book_rating_value'];
                        $input1['aggregateRating']['ratingCount']   = $custom_fields['saswp_book_rating_count'];                                
                    }
                                        
                    break; 
                    
                case 'MusicPlaylist':      
                    if ( isset( $custom_fields['saswp_music_playlist_id']) ) {
                        $input1['@id'] =     get_permalink().$custom_fields['saswp_music_playlist_id'];
                    }
                    if ( isset( $custom_fields['saswp_music_playlist_name']) ) {
                     $input1['name'] =    $custom_fields['saswp_music_playlist_name'];
                    }
                    if ( isset( $custom_fields['saswp_music_playlist_description']) ) {
                     $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_music_playlist_description'] )) ;
                    }
                    if ( isset( $custom_fields['saswp_music_playlist_url']) ) {
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_music_playlist_url']);
                    }
                    
                break;
                
                case 'Movie':      
                    
                    if ( isset( $custom_fields['saswp_movie_id']) ) {
                        $input1['@id'] =     get_permalink().$custom_fields['saswp_movie_id'];
                    }
                    if ( isset( $custom_fields['saswp_movie_name']) ) {
                     $input1['name'] =    $custom_fields['saswp_movie_name'];
                    }
                    if ( isset( $custom_fields['saswp_movie_description']) ) {
                     $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_movie_description'] )) ;
                    }
                    if ( isset( $custom_fields['saswp_movie_url']) ) {
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_movie_url']);
                     $input1['sameAs'] =    saswp_validate_url($custom_fields['saswp_movie_url']);
                    }
                    if ( isset( $custom_fields['saswp_movie_image']) ) {
                     $input1['image'] =    $custom_fields['saswp_movie_image'];
                    }
                    if ( isset( $custom_fields['saswp_movie_date_created']) ) {
                     $input1['dateCreated'] =    $custom_fields['saswp_movie_date_created'];
                    }
                    if ( isset( $custom_fields['saswp_movie_director']) ) {
                     $input1['director']['@type']        = 'Person';
                     $input1['director']['name']          = $custom_fields['saswp_movie_director']; 
                    }
                    if ( isset( $custom_fields['saswp_movie_actor']) ) {
                        $input1['actor']['@type']        = 'Person';
                        $input1['actor']['name']          = $custom_fields['saswp_movie_actor']; 
                    }
                    if ( isset( $custom_fields['saswp_movie_rating_value']) && isset($custom_fields['saswp_movie_rating_count']) ) {
                        $input1['aggregateRating']['@type']         = 'aggregateRating';                        
                        $input1['aggregateRating']['ratingValue']   = $custom_fields['saswp_movie_rating_value'];
                        $input1['aggregateRating']['reviewCount']   = $custom_fields['saswp_movie_rating_count'];                                
                    }
                    
                break;
                
                case 'CreativeWorkSeries':      
                        if ( isset( $custom_fields['saswp_cws_schema_id']) ) {
                            $input1['@id'] =    get_permalink().$custom_fields['saswp_cws_schema_id'];
                        }            
                       if ( isset( $custom_fields['saswp_cws_schema_image']) ) {
                            $input1['image'] =    $custom_fields['saswp_cws_schema_image'];
                       }
                       if ( isset( $custom_fields['saswp_cws_schema_url']) ) {
                            $input1['url'] =    saswp_validate_url($custom_fields['saswp_cws_schema_url']);
                       }                       
                       if ( isset( $custom_fields['saswp_cws_schema_keywords']) ) {
                            $input1['keywords'] =    $custom_fields['saswp_cws_schema_keywords'];
                       }                       
                       if ( isset( $custom_fields['saswp_cws_schema_inlanguage']) ) {
                            $input1['inLanguage'] =    $custom_fields['saswp_cws_schema_inlanguage'];
                       }
                       if ( isset( $custom_fields['saswp_cws_schema_name']) ) {
                            $input1['name'] =    $custom_fields['saswp_cws_schema_name'];
                       }                    
                       if ( isset( $custom_fields['saswp_cws_schema_description']) ) {
                            $input1['description'] =    wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_cws_schema_description'] ));
                       }
                       if ( isset( $custom_fields['saswp_cws_schema_date_published']) ) {
                            $input1['datePublished'] =    $custom_fields['saswp_cws_schema_date_published'];
                       }
                       if ( isset( $custom_fields['saswp_cws_schema_date_modified']) ) {
                            $input1['dateModified'] =    $custom_fields['saswp_cws_schema_date_modified'];
                       }                       
                       if ( isset( $custom_fields['saswp_cws_schema_start_date']) ) {
                             $input1['datePublished'] =    $custom_fields['saswp_cws_schema_start_date'];
                       }
                        if ( isset( $custom_fields['saswp_cws_schema_end_date']) ) {
                            $input1['dateModified'] =    $custom_fields['saswp_cws_schema_end_date'];
                       }
                       if ( isset( $custom_fields['saswp_cws_schema_author_type']) ) {
                            $input1['author']['@type'] =    $custom_fields['saswp_cws_schema_author_type'];
                       }
                       if ( isset( $custom_fields['saswp_cws_schema_author_name']) ) {
                            $input1['author']['name'] =    $custom_fields['saswp_cws_schema_author_name'];
                       }
                       if ( isset( $custom_fields['saswp_cws_schema_author_description']) ) {
                            $input1['author']['description'] =    $custom_fields['saswp_cws_schema_author_description'];
                       }
                       if ( isset( $custom_fields['saswp_cws_schema_author_url']) ) {
                            $input1['author']['url'] =    $custom_fields['saswp_cws_schema_author_url'];
                       }
                       if ( isset( $custom_fields['saswp_cws_schema_organization_logo']) && isset($custom_fields['saswp_cws_schema_organization_name']) ) {
                            $input1['publisher']['@type']       =    'Organization';
                            $input1['publisher']['name']        =    $custom_fields['saswp_cws_schema_organization_name'];
                            $input1['publisher']['logo']        =    $custom_fields['saswp_cws_schema_organization_logo'];
                       }
                                                              
                break; 

                case 'MusicComposition':      
                    
                    if ( isset( $custom_fields['saswp_music_composition_id']) ) {
                        $input1['@id'] =     get_permalink().$custom_fields['saswp_music_composition_id'];
                    }
                    if ( isset( $custom_fields['saswp_music_composition_name']) ) {
                     $input1['name'] =    $custom_fields['saswp_music_composition_name'];
                    }
                    if ( isset( $custom_fields['saswp_music_composition_description']) ) {
                     $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_music_composition_description'] )) ;
                    }
                    if ( isset( $custom_fields['saswp_music_composition_url']) ) {
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_music_composition_url']);
                    }
                    if ( isset( $custom_fields['saswp_music_composition_inlanguage']) ) {
                     $input1['inLanguage'] =    $custom_fields['saswp_music_composition_inlanguage'];
                    }
                    if ( isset( $custom_fields['saswp_music_composition_iswccode']) ) {
                     $input1['iswcCode'] =    $custom_fields['saswp_music_composition_iswccode'];
                    }
                    if ( isset( $custom_fields['saswp_music_composition_image']) ) {
                     $input1['image'] =    $custom_fields['saswp_music_composition_image'];
                    }
                    if ( isset( $custom_fields['saswp_music_composition_lyrics']) ) {
                     $input1['lyrics']['@type'] = 'CreativeWork';
                     $input1['lyrics']['text']  = $custom_fields['saswp_music_composition_lyrics'];
                    }
                    if ( isset( $custom_fields['saswp_music_composition_publisher']) ) {
                     $input1['publisher']['@type'] = 'Organization';
                     $input1['publisher']['name'] = $custom_fields['saswp_music_composition_publisher'];
                    }
                                                              
                    break; 
                
                    case 'PodcastEpisode':      
                        
                        if ( isset( $custom_fields['saswp_podcast_episode_id']) ) {
                            $input1['@id'] =     get_permalink().$custom_fields['saswp_podcast_episode_id'];
                        }
                        if ( isset( $custom_fields['saswp_podcast_episode_name']) ) {
                            $input1['name'] =    $custom_fields['saswp_podcast_episode_name'];
                        }
                        if ( isset( $custom_fields['saswp_podcast_episode_description']) ) {
                            $input1['description'] =    $custom_fields['saswp_podcast_episode_description'];
                        }
                        if ( isset( $custom_fields['saswp_podcast_episode_url']) ) {
                            $input1['url'] =    $custom_fields['saswp_podcast_episode_url'];
                        }
                        if ( isset( $custom_fields['saswp_podcast_episode_image']) ) {
                            $input1['image'] =    $custom_fields['saswp_podcast_episode_image'];
                        }

                        if ( isset( $custom_fields['saswp_podcast_episode_date_published']) ) {
                            $input1['datePublished'] =    $custom_fields['saswp_podcast_episode_date_published'];
                        }
                        if ( isset( $custom_fields['saswp_podcast_episode_date_modified']) ) {
                            $input1['dateModified'] =    $custom_fields['saswp_podcast_episode_date_modified'];
                        }
                        if ( isset( $custom_fields['saswp_podcast_episode_content_url']) ) {

                            $input1['associatedMedia']['@type']      = 'MediaObject';
                            $input1['associatedMedia']['contentUrl'] =    $custom_fields['saswp_podcast_episode_content_url'];

                        }

                        if ( isset( $custom_fields['saswp_podcast_episode_series_name']) ) {
                            $input1['partOfSeries']['@type'] = 'PodcastSeries';
                            $input1['partOfSeries']['name']  =    $custom_fields['saswp_podcast_episode_series_name'];
                        }

                        if ( isset( $custom_fields['saswp_podcast_episode_series_url']) ) {
                            $input1['partOfSeries']['@type'] = 'PodcastSeries';
                            $input1['partOfSeries']['url']  =    $custom_fields['saswp_podcast_episode_series_url'];
                        }

                        if ( isset( $custom_fields['saswp_podcast_episode_timeRequired']) ) {
                            $input1['timeRequired'] =    $custom_fields['saswp_podcast_episode_timeRequired'];
                        }

                    break;

                    case 'PodcastSeason':      
                        if ( isset( $custom_fields['saswp_podcast_season_id']) ) {
                            $input1['@id'] =     get_permalink().$custom_fields['saswp_podcast_season_id'];
                        }
                        if ( isset( $custom_fields['saswp_podcast_season_name']) ) {
                            $input1['name'] =    $custom_fields['saswp_podcast_season_name'];
                        }
                        if ( isset( $custom_fields['saswp_podcast_season_description']) ) {
                            $input1['description'] =    $custom_fields['saswp_podcast_season_description'];
                        }
                        if ( isset( $custom_fields['saswp_podcast_season_url']) ) {
                            $input1['url'] =    $custom_fields['saswp_podcast_season_url'];
                        }
                        if ( isset( $custom_fields['saswp_podcast_season_image']) ) {
                            $input1['image'] =    $custom_fields['saswp_podcast_season_image'];
                        }
                        if ( isset( $custom_fields['saswp_podcast_season_date_published']) ) {
                            $input1['datePublished'] =    $custom_fields['saswp_podcast_season_date_published'];
                        }
                        if ( isset( $custom_fields['saswp_podcast_season_date_modified']) ) {
                            $input1['dateModified'] =    $custom_fields['saswp_podcast_season_date_modified'];
                        }

                        if ( isset( $custom_fields['saswp_podcast_season_number']) ) {
                            $input1['seasonNumber'] =    $custom_fields['saswp_podcast_season_number'];
                        }
                        if ( isset( $custom_fields['saswp_podcast_season_number_of_seasons']) ) {
                            $input1['numberOfEpisodes'] =    $custom_fields['saswp_podcast_season_number_of_seasons'];
                        }
                                                                        
                        if ( isset( $custom_fields['saswp_podcast_season_series_name']) ) {
                            $input1['partOfSeries']['@type'] = 'PodcastSeries';
                            $input1['partOfSeries']['name']  =    $custom_fields['saswp_podcast_season_series_name'];
                        }

                        if ( isset( $custom_fields['saswp_podcast_season_series_url']) ) {
                            $input1['partOfSeries']['@type'] = 'PodcastSeries';
                            $input1['partOfSeries']['url']  =    $custom_fields['saswp_podcast_season_series_url'];
                        }                        

                    break;

                    case 'HotelRoom':      

                        if ( isset( $custom_fields['saswp_hotelroom_hotel_id']) ) {
                            $input1['@id'] =    get_permalink().$custom_fields['saswp_hotelroom_hotel_id'];
                        }                     
                        if ( isset( $custom_fields['saswp_hotelroom_hotel_name']) ) {
                            $input1['name'] =    $custom_fields['saswp_hotelroom_hotel_name'];
                        }
                        if ( isset( $custom_fields['saswp_hotelroom_hotel_image']) ) {
                            $input1['image'] =    $custom_fields['saswp_hotelroom_hotel_image'];
                        }
                        if ( isset( $custom_fields['saswp_hotelroom_hotel_description']) ) {
                            $input1['description'] =    $custom_fields['saswp_hotelroom_hotel_description'];
                        }
                        if ( isset( $custom_fields['saswp_hotelroom_hotel_price_range']) ) {
                            $input1['priceRange'] =    $custom_fields['saswp_hotelroom_hotel_price_range'];
                        }
                        if ( isset( $custom_fields['saswp_hotelroom_hotel_telephone']) ) {
                            $input1['telephone'] =    $custom_fields['saswp_hotelroom_hotel_telephone'];
                        }

                        if ( isset( $custom_fields['saswp_hotelroom_hotel_streetaddress']) ) {
                            $input1['address']['streetAddress'] =    $custom_fields['saswp_hotelroom_hotel_streetaddress'];
                        }                    
                        if ( isset( $custom_fields['saswp_hotelroom_hotel_locality']) ) {
                            $input1['address']['addressLocality'] =    $custom_fields['saswp_hotelroom_hotel_locality'];
                        }
                        if ( isset( $custom_fields['saswp_hotelroom_hotel_region']) ) {
                            $input1['address']['addressRegion'] =    $custom_fields['saswp_hotelroom_hotel_region'];
                        }
                        if ( isset( $custom_fields['saswp_hotelroom_hotel_country']) ) {
                            $input1['address']['addressCountry'] =    $custom_fields['saswp_hotelroom_hotel_country'];
                        }
                        if ( isset( $custom_fields['saswp_hotelroom_hotel_postalcode']) ) {
                            $input1['address']['postalCode'] =    $custom_fields['saswp_hotelroom_hotel_postalcode'];
                        }

                        if ( isset( $custom_fields['saswp_hotelroom_name']) ) {
                            $input1['containsPlace']['@type'] = 'HotelRoom'; 
                            $input1['containsPlace']['name'] =    $custom_fields['saswp_hotelroom_name'];
                        }
                        if ( isset( $custom_fields['saswp_hotelroom_description']) ) {
                            $input1['containsPlace']['@type'] = 'HotelRoom'; 
                            $input1['containsPlace']['description'] =    $custom_fields['saswp_hotelroom_description'];
                        }
                        if ( isset( $custom_fields['saswp_hotelroom_image']) ) {
                            $input1['containsPlace']['@type'] = 'HotelRoom'; 
                            $input1['containsPlace']['image'] =    $custom_fields['saswp_hotelroom_image'];
                        }

                        if ( isset( $custom_fields['saswp_hotelroom_offer_name']) ) {
                            $input1['makesOffer']['@type'] = 'offer'; 
                            $input1['makesOffer']['name'] =    $custom_fields['saswp_hotelroom_offer_name'];
                        }

                        if ( isset( $custom_fields['saswp_hotelroom_offer_description']) ) {
                            $input1['makesOffer']['@type'] = 'offer'; 
                            $input1['makesOffer']['description'] =    $custom_fields['saswp_hotelroom_offer_description'];
                        }

                        if ( isset( $custom_fields['saswp_hotelroom_offer_price']) && isset($custom_fields['saswp_hotelroom_offer_price_currency']) ) {

                            $input1['makesOffer']['@type']                       = 'offer';
                            $input1['makesOffer']['priceSpecification']['@type'] = 'UnitPriceSpecification'; 

                            $input1['makesOffer']['priceSpecification']['priceCurrency']  = $custom_fields['saswp_hotelroom_offer_price_currency']; 
                            $input1['makesOffer']['priceSpecification']['price']          = $custom_fields['saswp_hotelroom_offer_price']; 
                            if ( isset( $custom_fields['saswp_hotelroom_offer_unitcode']) ) {
                                $input1['makesOffer']['priceSpecification']['unitCode']       = $custom_fields['saswp_hotelroom_offer_unitcode']; 
                            }
                            if ( isset( $custom_fields['saswp_hotelroom_offer_validthrough']) ) {
                                $input1['makesOffer']['priceSpecification']['validThrough']   = $custom_fields['saswp_hotelroom_offer_validthrough']; 
                            }
                                                        
                        }


                    break;

                    case 'Audiobook':      
                        
                        if ( isset( $custom_fields['saswp_audiobook_id']) ) {
                            $input1['@id'] =    get_permalink().$custom_fields['saswp_audiobook_id'];
                        }
                        if ( isset( $custom_fields['saswp_audiobook_name']) ) {
                            $input1['name'] =    $custom_fields['saswp_audiobook_name'];
                        }
                        if ( isset( $custom_fields['saswp_audiobook_description']) ) {
                            $input1['description'] =    $custom_fields['saswp_audiobook_description'];
                        }
                        if ( isset( $custom_fields['saswp_audiobook_url']) ) {
                            $input1['url'] =    saswp_validate_url($custom_fields['saswp_audiobook_url']);
                        }
                        if ( isset( $custom_fields['saswp_audiobook_date_published']) ) {
                            $input1['datePublished'] =    $custom_fields['saswp_audiobook_date_published'];
                        }
                        if ( isset( $custom_fields['saswp_audiobook_date_modified']) ) {
                            $input1['dateModified'] =    $custom_fields['saswp_audiobook_date_modified'];
                        }
                        if ( isset( $custom_fields['saswp_audiobook_image']) ) {
                            $input1['image'] =    $custom_fields['saswp_audiobook_image'];
                        }
                        if ( isset( $custom_fields['saswp_audiobook_author_type']) ) {
                            $input1['author']['@type'] =    $custom_fields['saswp_audiobook_author_type'];
                        }
                        if ( isset( $custom_fields['saswp_audiobook_author_name']) ) {
                            $input1['author']['name'] =    $custom_fields['saswp_audiobook_author_name'];
                        }
                        if ( isset( $custom_fields['saswp_audiobook_author_url']) ) {
                            $input1['author']['url'] =    saswp_validate_url($custom_fields['saswp_audiobook_author_url']);
                        }
                        if ( isset( $custom_fields['saswp_audiobook_author_description']) ) {
                            $input1['author']['description'] =    $custom_fields['saswp_audiobook_author_description'];
                        }
                        if ( isset( $custom_fields['saswp_audiobook_author_image']) ) {
                            $input1['author']['image'] =    $custom_fields['saswp_audiobook_author_image'];
                        }
                        if ( isset( $custom_fields['saswp_audiobook_publisher']) ) {
                            $input1['publisher'] =    $custom_fields['saswp_audiobook_publisher'];
                        }
                        if ( isset( $custom_fields['saswp_audiobook_provider']) ) {
                            $input1['provider'] =    $custom_fields['saswp_audiobook_provider'];
                        }
                        if ( isset( $custom_fields['saswp_audiobook_readby']) ) {
                            $input1['readBy'] =    $custom_fields['saswp_audiobook_readby'];
                        }
                        if ( isset( $custom_fields['saswp_audiobook_content_url']) ) {
                            $input1['contentUrl'] =    $custom_fields['saswp_audiobook_content_url'];
                        }
                        if ( isset( $custom_fields['saswp_audiobook_duration']) ) {
                            $input1['duration'] =    $custom_fields['saswp_audiobook_duration'];
                        }
                        if ( isset( $custom_fields['saswp_audiobook_encoding_format']) ) {
                            $input1['encodingFormat'] =    $custom_fields['saswp_audiobook_encoding_format'];
                        }
                        if ( isset( $custom_fields['saswp_audiobook_player_type']) ) {
                            $input1['playerType'] =    $custom_fields['saswp_audiobook_player_type'];
                        }
                        if ( isset( $custom_fields['saswp_audiobook_main_entity_of_page']) ) {
                            $input1['mainEntityOfPage'] =    $custom_fields['saswp_audiobook_main_entity_of_page'];
                        }

                    break;

                    case 'EducationalOccupationalCredential':      
                    
                        if ( isset( $custom_fields['saswp_eoc_id']) ) {
                            $input1['@id'] =    get_permalink().$custom_fields['saswp_eoc_id'];
                        } 
                        if ( isset( $custom_fields['saswp_eoc_additional_type']) ) {
                            $input1['additionalType'] =    $custom_fields['saswp_eoc_additional_type'];
                        }
                        if ( isset( $custom_fields['saswp_eoc_name']) ) {
                            $input1['name'] =    $custom_fields['saswp_eoc_name'];
                        }
                        if ( isset( $custom_fields['saswp_eoc_alt_name']) ) {
                            $input1['alternateName'] =    $custom_fields['saswp_eoc_alt_name'];
                        }
                        if ( isset( $custom_fields['saswp_eoc_description']) ) {
                            $input1['description'] =    $custom_fields['saswp_eoc_description'];
                        }

                        if ( isset( $custom_fields['saswp_eoc_e_lavel_name']) ) {

                            $input1['educationalLevel']['@type']             = 'DefinedTerm'; 
                            $input1['educationalLevel']['name']              =    $custom_fields['saswp_eoc_e_lavel_name'];
                            $input1['educationalLevel']['inDefinedTermSet']  =    $custom_fields['saswp_eoc_e_lavel_definedtermset'];

                        }

                        if ( isset( $custom_fields['saswp_eoc_c_category_name']) ) {

                            $input1['credentialCategory']['@type']             = 'DefinedTerm'; 
                            $input1['credentialCategory']['name']              =    $custom_fields['saswp_eoc_c_category_name'];
                            $input1['credentialCategory']['inDefinedTermSet']  =    $custom_fields['saswp_eoc_c_category_definedtermset'];
                            $input1['credentialCategory']['termCode']          =    $custom_fields['saswp_eoc_c_category_term_code'];
                            
                        }

                        if ( isset( $custom_fields['saswp_eoc_c_required_name']) ) {

                            $input1['competencyRequired']['@type']             = 'DefinedTerm'; 
                            $input1['competencyRequired']['name']              =    $custom_fields['saswp_eoc_c_required_name'];
                            $input1['competencyRequired']['inDefinedTermSet']  =    $custom_fields['saswp_eoc_c_required_definedtermset'];
                            $input1['competencyRequired']['termCode']          =    $custom_fields['saswp_eoc_c_required_term_code'];
                            $input1['competencyRequired']['url']               =    $custom_fields['saswp_eoc_c_required_url'];
                            
                        }

                    break;
                    case 'EducationalOccupationalProgram':      
                        if ( isset( $custom_fields['saswp_eop_id']) ) {
                            $input1['name'] =    get_permalink().$custom_fields['saswp_eop_id'];
                        }
                        if ( isset( $custom_fields['saswp_eop_name']) ) {
                         $input1['name'] =    $custom_fields['saswp_eop_name'];
                        }
                        if ( isset( $custom_fields['saswp_eop_description']) ) {
                         $input1['description'] =  ($custom_fields['saswp_eop_description']);
                        }
                        if ( isset( $custom_fields['saswp_eop_url']) ) {
                         $input1['url'] =  saswp_validate_url($custom_fields['saswp_eop_url']);
                        }
                        if ( isset( $custom_fields['saswp_eop_image']) ) {
                         $input1['image'] =  ($custom_fields['saswp_eop_image']);
                        }
                        if ( isset( $custom_fields['saswp_eop_time_to_complete']) ) {
                         $input1['timeToComplete'] =  ($custom_fields['saswp_eop_time_to_complete']);
                        }
                        if ( isset( $custom_fields['saswp_eop_occupational_category']) ) {
                         $input1['occupationalCategory'] =  ($custom_fields['saswp_eop_occupational_category']);
                        }
                        if ( isset( $custom_fields['saswp_eop_occupational_credential_awarded']) ) {
                         $input1['occupationalCredentialAwarded'] =  ($custom_fields['saswp_eop_occupational_credential_awarded']);
                        }
                        if ( isset( $custom_fields['saswp_eop_program_prerequisites']) ) {
                         $input1['programPrerequisites'] =  ($custom_fields['saswp_eop_program_prerequisites']);
                        }
                        if ( isset( $custom_fields['saswp_eop_application_start_date']) ) {
                         $input1['applicationStartDate'] =  ($custom_fields['saswp_eop_application_start_date']);
                        }
                        if ( isset( $custom_fields['saswp_eop_application_deadline']) ) {
                         $input1['applicationDeadLine'] =  ($custom_fields['saswp_eop_application_deadline']);
                        }
                        if ( isset( $custom_fields['saswp_eop_start_date']) ) {
                         $input1['startDate'] =  ($custom_fields['saswp_eop_start_date']);
                        }
                        if ( isset( $custom_fields['saswp_eop_end_date']) ) {
                         $input1['endDate'] =  ($custom_fields['saswp_eop_end_date']);
                        }
                        if ( isset( $custom_fields['saswp_eop_day_of_week']) ) {
                         $input1['dayOfWeek'] =  ($custom_fields['saswp_eop_day_of_week']);
                        }
                        if ( isset( $custom_fields['saswp_eop_time_of_day']) ) {
                         $input1['timeOfDay'] =  ($custom_fields['saswp_eop_time_of_day']);
                        }
                        if ( isset( $custom_fields['saswp_eop_number_of_credits']) ) {
                         $input1['numberOfCredits'] =  ($custom_fields['saswp_eop_number_of_credits']);
                        }
                        if ( isset( $custom_fields['saswp_eop_typical_credits_per_term']) ) {
                         $input1['typicalCreditsPerterm'] =  ($custom_fields['saswp_eop_typical_credits_per_term']);
                        }
                        if ( isset( $custom_fields['saswp_eop_term_duration']) ) {
                         $input1['termDuration'] =  ($custom_fields['saswp_eop_term_duration']);
                        }
                        if ( isset( $custom_fields['saswp_eop_terms_per_year']) ) {
                         $input1['termPerYear'] =  ($custom_fields['saswp_eop_terms_per_year']);
                        }
                        if ( isset( $custom_fields['saswp_eop_maximum_enrollment']) ) {
                         $input1['maximumEnrollment'] =  ($custom_fields['saswp_eop_maximum_enrollment']);
                        }
                        if ( isset( $custom_fields['saswp_eop_educational_program_mode']) ) {
                         $input1['educationalProgramMode'] =  ($custom_fields['saswp_eop_educational_program_mode']);
                        }
                        if ( isset( $custom_fields['saswp_eop_financial_aid_eligible']) ) {
                         $input1['financialAidEligible'] =  ($custom_fields['saswp_eop_financial_aid_eligible']);
                        }
                        
                        $provider = array();

                        if ( isset( $custom_fields['saswp_eop_provider_name']) ) {
                            $provider = array(
                          '@type'       => 'Organization',
                          'name'        =>  $custom_fields['saswp_eop_provider_name'],
                           'telephone'  =>  $custom_fields['saswp_eop_provider_telephone'],
                            'address'   => array(
                                              '@type'           => 'Postaladdress',
                                              'streetAddress'   => $custom_fields['saswp_eop_provider_street_address'],
                                              'addressLocality' => $custom_fields['saswp_eop_provider_address_locality'],
                                              'addressRegion'   => $custom_fields['saswp_eop_provider_address_region'],
                                              'addressCountry'  => $custom_fields['saswp_eop_provider_address_country'],
                                              'postalCode'      => $custom_fields['saswp_eop_provider_postal_code'],
                                              ),
                                            );
                                            $input1['provider'] = $provider;
                                     }
                    break;  

                    case 'Project':      
                        
                        if ( isset( $custom_fields['saswp_project_id']) ) {
                            $input1['@id'] =    get_permalink().$custom_fields['saswp_project_id'];
                        } 
                        if ( isset( $custom_fields['saswp_project_name']) ) {
                         $input1['name'] =    $custom_fields['saswp_project_name'];
                        }
                        if ( isset( $custom_fields['saswp_project_description']) ) {
                         $input1['description'] =    $custom_fields['saswp_project_description'];
                        }
                        if ( isset( $custom_fields['saswp_project_url']) ) {
                         $input1['url'] =    saswp_validate_url($custom_fields['saswp_project_url']);
                        }                                        
                        if ( isset( $custom_fields['saswp_project_street_address']) ) {
                         $input1['address']['streetAddress'] =    $custom_fields['saswp_project_street_address'];
                        }                    
                        if ( isset( $custom_fields['saswp_project_city']) ) {
                         $input1['address']['addressLocality'] =    $custom_fields['saswp_project_city'];
                        }
                        if ( isset( $custom_fields['saswp_project_state']) ) {
                         $input1['address']['addressRegion'] =    $custom_fields['saswp_project_state'];
                        }
                        if ( isset( $custom_fields['saswp_project_country']) ) {
                         $input1['address']['addressCountry'] =    $custom_fields['saswp_project_country'];
                        }
                        if ( isset( $custom_fields['saswp_project_postal_code']) ) {
                         $input1['address']['postalCode'] =    $custom_fields['saswp_project_postal_code'];
                        }
                        if ( isset( $custom_fields['saswp_project_telephone']) ) {
                         $input1['address']['telephone'] =    $custom_fields['saswp_project_telephone'];
                        }
                        if ( isset( $custom_fields['saswp_project_email']) ) {
                         $input1['address']['email'] =    $custom_fields['saswp_project_email'];
                        }
                        if ( isset( $custom_fields['saswp_project_logo']) ) {
                         $input1['logo'] =    $custom_fields['saswp_project_logo'];
                        }
                        if ( isset( $custom_fields['saswp_project_image']) ) {
                         $input1['image'] =    $custom_fields['saswp_project_image'];
                        }
                        if ( isset( $custom_fields['saswp_project_duns']) ) {
                            $input1['duns'] =    $custom_fields['saswp_project_duns'];
                        }
                        if ( isset( $custom_fields['saswp_project_founder']) ) {
                            $input1['founder'] =    $custom_fields['saswp_project_founder'];
                        }
                        if ( isset( $custom_fields['saswp_project_founding_date']) ) {
                            $input1['foundingDate'] =    $custom_fields['saswp_project_founding_date'];
                        }
                        if ( isset( $custom_fields['saswp_project_qualifications']) ) {
                            $input1['hasCredential'] =    $custom_fields['saswp_project_qualifications'];
                        }
                        if ( isset( $custom_fields['saswp_project_knows_about']) ) {
                            $input1['knowsAbout'] =    $custom_fields['saswp_project_knows_about'];
                        }
                        if ( isset( $custom_fields['saswp_project_member_of']) ) {
                            $input1['memberOf'] =    $custom_fields['saswp_project_member_of'];
                        }
                        if ( isset( $custom_fields['saswp_project_parent_project']) ) {
                            $input1['parentproject'] =    $custom_fields['saswp_project_parent_project'];
                        }
    
                        $sameas = array();
                        if ( isset( $custom_fields['saswp_project_website']) ) {
                            $sameas[] =    $custom_fields['saswp_project_website'];
                        }
                        if ( isset( $custom_fields['saswp_project_facebook']) ) {
                            $sameas[] =    $custom_fields['saswp_project_facebook'];
                        }
                        if ( isset( $custom_fields['saswp_project_twitter']) ) {
                            $sameas[] =    $custom_fields['saswp_project_twitter'];
                        }
                        if ( isset( $custom_fields['saswp_project_linkedin']) ) {
                            $sameas[] =    $custom_fields['saswp_project_linkedin'];
                        }
                        if ( isset( $custom_fields['saswp_project_threads']) ) {
                            $sameas[] =    $custom_fields['saswp_project_threads'];
                        }
                        if ( isset( $custom_fields['saswp_project_mastodon']) ) {
                            $sameas[] =    $custom_fields['saswp_project_mastodon'];
                        }
                        if ( isset( $custom_fields['saswp_project_vibehut']) ) {
                            $sameas[] =    $custom_fields['saswp_project_vibehut'];
                        }
                        if($sameas){
                            $input1['sameAs'] = $sameas;
                        }
    
                        if ( isset( $custom_fields['saswp_project_rating_value']) && isset($custom_fields['saswp_project_rating_count']) ) {
                            $input1['aggregateRating']['@type']       =   'AggregateRating';                                                
                            $input1['aggregateRating']['ratingValue'] =    $custom_fields['saswp_project_rating_value'];
                            $input1['aggregateRating']['ratingCount'] =    $custom_fields['saswp_project_rating_count'];
                        }
                                                                                      
                        break;         

                case 'Organization':      
                    if ( isset( $custom_fields['saswp_organization_id']) ) {

                        $input1['@id'] =    $custom_fields['saswp_organization_id'];
                        if(empty($custom_fields['saswp_organization_id']) ) {
                            unset($input1['@id']);
                        }
                        
                    } 
                    if ( isset( $custom_fields['saswp_organization_name']) ) {
                     $input1['name'] =    $custom_fields['saswp_organization_name'];
                    }
                    if ( isset( $custom_fields['saswp_organization_description']) ) {
                     $input1['description'] =    $custom_fields['saswp_organization_description'];
                    }
                    if ( isset( $custom_fields['saswp_organization_url']) ) {
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_organization_url']);
                    }                                        
                    if ( isset( $custom_fields['saswp_organization_street_address']) ) {
                     $input1['address']['streetAddress'] =    $custom_fields['saswp_organization_street_address'];
                    }                    
                    if ( isset( $custom_fields['saswp_organization_city']) ) {
                     $input1['address']['addressLocality'] =    $custom_fields['saswp_organization_city'];
                    }
                    if ( isset( $custom_fields['saswp_organization_state']) ) {
                     $input1['address']['addressRegion'] =    $custom_fields['saswp_organization_state'];
                    }
                    if ( isset( $custom_fields['saswp_organization_country']) ) {
                     $input1['address']['addressCountry'] =    $custom_fields['saswp_organization_country'];
                    }
                    if ( isset( $custom_fields['saswp_organization_postal_code']) ) {
                     $input1['address']['postalCode'] =    $custom_fields['saswp_organization_postal_code'];
                    }
                    if ( isset( $custom_fields['saswp_organization_telephone']) ) {
                     $input1['address']['telephone'] =    $custom_fields['saswp_organization_telephone'];
                    }
                    if ( isset( $custom_fields['saswp_organization_email']) ) {
                     $input1['address']['email'] =    $custom_fields['saswp_organization_email'];
                    }
                    if ( isset( $custom_fields['saswp_organization_logo']) ) {
                     $input1['logo'] =    $custom_fields['saswp_organization_logo'];
                    }
                    if ( isset( $custom_fields['saswp_organization_image']) && !empty($custom_fields['saswp_organization_image']) ) {                        
                     $input1['image'] =    $custom_fields['saswp_organization_image'];
                    }
                    if ( isset( $custom_fields['saswp_organization_duns']) ) {
                        $input1['duns'] =    $custom_fields['saswp_organization_duns'];
                    }
                    if ( isset( $custom_fields['saswp_organization_founder']) ) {
                        $input1['founder'] =    $custom_fields['saswp_organization_founder'];
                    }
                    if ( isset( $custom_fields['saswp_organization_founding_date']) ) {
                        $input1['foundingDate'] =    $custom_fields['saswp_organization_founding_date'];
                    }
                    if ( isset( $custom_fields['saswp_organization_qualifications']) ) {
                        $input1['hasCredential'] =    $custom_fields['saswp_organization_qualifications'];
                    }
                    if ( isset( $custom_fields['saswp_organization_knows_about']) ) {
                        $input1['knowsAbout'] =    $custom_fields['saswp_organization_knows_about'];
                    }
                    if ( isset( $custom_fields['saswp_organization_member_of']) ) {
                        $input1['memberOf'] =    $custom_fields['saswp_organization_member_of'];
                    }
                    if ( isset( $custom_fields['saswp_organization_parent_organization']) ) {
                        $input1['parentOrganization'] =    $custom_fields['saswp_organization_parent_organization'];
                    }

                    $sameas = array();
                    if ( isset( $custom_fields['saswp_organization_website']) ) {
                        $sameas[] =    $custom_fields['saswp_organization_website'];
                    }
                    if ( isset( $custom_fields['saswp_organization_facebook']) ) {
                        $sameas[] =    $custom_fields['saswp_organization_facebook'];
                    }
                    if ( isset( $custom_fields['saswp_organization_twitter']) ) {
                        $sameas[] =    $custom_fields['saswp_organization_twitter'];
                    }
                    if ( isset( $custom_fields['saswp_organization_linkedin']) ) {
                        $sameas[] =    $custom_fields['saswp_organization_linkedin'];
                    }
                    if ( isset( $custom_fields['saswp_organization_threads']) ) {
                        $sameas[] =    $custom_fields['saswp_organization_threads'];
                    }
                    if ( isset( $custom_fields['saswp_organization_mastodon']) ) {
                        $sameas[] =    $custom_fields['saswp_organization_mastodon'];
                    }
                    if ( isset( $custom_fields['saswp_organization_vibehut']) ) {
                        $sameas[] =    $custom_fields['saswp_organization_vibehut'];
                    }
                    if($sameas){
                        $input1['sameAs'] = $sameas;
                    }

                    if ( isset( $custom_fields['saswp_organization_rating_value']) && isset($custom_fields['saswp_organization_rating_count']) ) {
                        $input1['aggregateRating']['@type']       =   'AggregateRating';                                                
                        $input1['aggregateRating']['ratingValue'] =    $custom_fields['saswp_organization_rating_value'];
                        $input1['aggregateRating']['ratingCount'] =    $custom_fields['saswp_organization_rating_count'];
                    }                    
                                                    
                    break;     
                    
                case 'MusicAlbum':      
                    if ( isset( $custom_fields['saswp_music_album_id']) ) {
                        $input1['@id'] =     get_permalink().$custom_fields['saswp_music_album_id'];
                    }
                    if ( isset( $custom_fields['saswp_music_album_name']) ) {
                     $input1['name'] =    $custom_fields['saswp_music_album_name'];
                    }
                    if ( isset( $custom_fields['saswp_music_album_url']) ) {
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_music_album_url']);
                    }
                    if ( isset( $custom_fields['saswp_music_album_description']) ) {
                     $input1['description'] =    wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_music_album_description'] ));
                    }
                    if ( isset( $custom_fields['saswp_music_album_genre']) ) {
                     $input1['genre'] =    $custom_fields['saswp_music_album_genre'];
                    }
                    if ( isset( $custom_fields['saswp_music_album_image']) ) {
                     $input1['image'] =    $custom_fields['saswp_music_album_image'];
                    }
                    if ( isset( $custom_fields['saswp_music_album_artist']) ) {
                     $input1['byArtist']['@type']     = 'MusicGroup';
                     $input1['byArtist']['name']      = $custom_fields['saswp_music_album_artist'];
                    }       
                    
                    break;     
                    case 'Article':      
                        if ( isset( $custom_fields['saswp_article_id']) ) {
                            $input1['@id'] =    get_permalink().$custom_fields['saswp_article_id'];
                        }
                        if ( isset( $custom_fields['saswp_article_main_entity_of_page']) ) {
                         $input1['mainEntityOfPage'] =    $custom_fields['saswp_article_main_entity_of_page'];
                        }
                        if ( isset( $custom_fields['saswp_article_image']) ) {
                         $input1['image'] =    $custom_fields['saswp_article_image'];
                        }
                        if ( isset( $custom_fields['saswp_article_url']) ) {
                         $input1['url'] =    saswp_validate_url($custom_fields['saswp_article_url']);
                        }
                        if ( isset( $custom_fields['saswp_article_body']) ) {
                            if($custom_fields['saswp_article_body']){
                                $input1['articleBody'] =    $custom_fields['saswp_article_body'];
                            }else{
                                unset($input1['articleBody']);
                            }
                        }
                        if ( isset( $custom_fields['saswp_article_keywords']) ) {
                         $input1['keywords'] =    $custom_fields['saswp_article_keywords'];
                        }
                        if ( isset( $custom_fields['saswp_article_section']) ) {
                         $input1['articleSection'] =    $custom_fields['saswp_article_section'];
                        }
                        if ( isset( $custom_fields['saswp_article_inlanguage']) ) {
                            $input1['inLanguage'] =    $custom_fields['saswp_article_inlanguage'];
                        }
                        if ( isset( $custom_fields['saswp_article_headline']) ) {
                         $input1['headline'] =    $custom_fields['saswp_article_headline'];
                        }                    
                        if ( isset( $custom_fields['saswp_article_description']) ) {
                         $input1['description'] =    wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_article_description'] ));
                        }
                        if ( ! empty( $custom_fields['saswp_article_haspart'] ) && is_array( $custom_fields['saswp_article_haspart'] ) ) {
                            foreach ( $custom_fields['saswp_article_haspart'] as $hp_key => $has_part) {
                                if ( ! empty( $has_part ) && is_array( $has_part ) ) {
                                    $input1['hasPart'][]       =   $has_part;    
                                }
                            } 
                        }
                        if ( ! empty( $custom_fields['saswp_article_ispartof'] ) && is_array( $custom_fields['saswp_article_ispartof'] ) ) {
                            foreach ( $custom_fields['saswp_article_ispartof'] as $ip_key => $is_part) {
                                if ( ! empty( $is_part ) && is_array( $is_part ) ) {
                                    $input1['isPartOf'][]       =   $is_part;    
                                }
                            } 
                        }
                        if ( isset( $custom_fields['saswp_article_date_published']) ) {
                         $input1['datePublished'] =    $custom_fields['saswp_article_date_published'];
                        }
                        if ( isset( $custom_fields['saswp_article_date_modified']) ) {
                         $input1['dateModified'] =    $custom_fields['saswp_article_date_modified'];
                        }
                       
                        if ( ! empty( $custom_fields['saswp_article_editor_global_mapping']) ) {
                            $input1['editor'] = array();
                            if ( ! empty( $custom_fields['saswp_article_editor_global_mapping']) ) {
                                $input1['editor']['@type'] =   "Person";
                            }

                            if ( ! empty( $custom_fields['saswp_article_editor_global_mapping']['name']) ) {
                                $input1['editor']['name'] =    $custom_fields['saswp_article_editor_global_mapping']['name'];
                            }

                            if ( ! empty( $custom_fields['saswp_article_editor_global_mapping']['url']) ) {
                                $input1['editor']['url'] =    $custom_fields['saswp_article_editor_global_mapping']['url'];
                            }

                            if ( ! empty( $custom_fields['saswp_article_editor_global_mapping']['description']) ) {
                                $input1['editor']['description'] =    $custom_fields['saswp_article_editor_global_mapping']['description'];
                            }

                            if ( ! empty( $custom_fields['saswp_article_editor_global_mapping']['custom_fields']['honorificsuffix'][0]) ) {
                                $input1['editor']['honorificSuffix'] =    $custom_fields['saswp_article_editor_global_mapping']['custom_fields']['honorificsuffix'][0];
                            }

                            if ( ! empty( $custom_fields['saswp_article_editor_global_mapping']['custom_fields']['knowsabout'][0]) ) {
                                $input1['editor']['knowsAbout'] =   explode(',', $custom_fields['saswp_article_editor_global_mapping']['custom_fields']['knowsabout'][0]);
                            }

                            $sameas = array();
                            if ( ! empty( $custom_fields['saswp_article_editor_global_mapping']['custom_fields']['team_facebook'][0]) ) {
                                $sameas[] =  $custom_fields['saswp_article_editor_global_mapping']['custom_fields']['team_facebook'][0];
                            }

                            if ( ! empty( $custom_fields['saswp_article_editor_global_mapping']['custom_fields']['team_twitter'][0]) ) {
                                $sameas[] =  $custom_fields['saswp_article_editor_global_mapping']['custom_fields']['team_twitter'][0];
                            }

                            if ( ! empty( $custom_fields['saswp_article_editor_global_mapping']['custom_fields']['team_linkedin'][0]) ) {
                                $sameas[] =   $custom_fields['saswp_article_editor_global_mapping']['custom_fields']['team_linkedin'][0];
                            }

                            if ( ! empty( $custom_fields['saswp_article_editor_global_mapping']['custom_fields']['team_instagram'][0]) ) {
                                $sameas[] =   $custom_fields['saswp_article_editor_global_mapping']['custom_fields']['team_instagram'][0];
                            }

                            if ( ! empty( $custom_fields['saswp_article_editor_global_mapping']['custom_fields']['team_youtube'][0]) ) {
                                $sameas[] =   $custom_fields['saswp_article_editor_global_mapping']['custom_fields']['team_youtube'][0];
                            }
                            if($sameas){
                                $input1['editor']['sameAs'] = $sameas;
                            }

                            if ( ! empty( $custom_fields['saswp_article_editor_global_mapping']['custom_fields']['alumniof'][0]) ) {
                                $str =  $custom_fields['saswp_article_editor_global_mapping']['custom_fields']['alumniof'][0];
                                $itemlist = explode(",", $str);
                                foreach ( $itemlist as $key => $list){
                                    $vnewarr['@type'] = 'Organization';
                                    $vnewarr['Name']   = $list;   
                                    $input1['editor']['alumniOf'][] = $vnewarr;
                                }
                            }
                        }else{
                           
                            if ( isset( $custom_fields['saswp_article_editor_type']) ) {
                                $input1['editor']['@type'] =    $custom_fields['saswp_article_editor_type'];
                            }
                            if ( isset( $custom_fields['saswp_article_editor_name']) ) {
                             $input1['editor']['name'] =    $custom_fields['saswp_article_editor_name'];
                            }
                            if ( isset( $custom_fields['saswp_article_editor_honorific_suffix']) ) {
                                $input1['editor']['honorificSuffix'] =    $custom_fields['saswp_article_editor_honorific_suffix'];
                            }
                            if ( isset( $custom_fields['saswp_article_editor_description']) ) {
                                $input1['editor']['description'] =    $custom_fields['saswp_article_editor_description'];
                            }
                            if ( isset( $custom_fields['saswp_article_editor_url']) ) {
                                $input1['editor']['url'] =    $custom_fields['saswp_article_editor_url'];
                            }
                            if ( isset( $custom_fields['saswp_article_editor_image']) ) {
                                $input1['editor']['Image']['url'] =    $custom_fields['saswp_article_editor_image'];
                            }
                            if ( isset( $custom_fields['saswp_article_editor_jobtitle']) ) {
                                $input1['editor']['JobTitle'] =    $custom_fields['saswp_article_editor_jobtitle'];
                            }
                        }

                        if ( ! empty( $custom_fields['saswp_article_author_global_mapping']) ) {
                            $input1['author'] = array();
                            if ( ! empty( $custom_fields['saswp_article_author_global_mapping']) ) {
                                $input1['author']['@type'] =   "Person";
                            }

                            if ( ! empty( $custom_fields['saswp_article_author_global_mapping']['name']) ) {
                                $input1['author']['name'] =    $custom_fields['saswp_article_author_global_mapping']['name'];
                            }

                            if ( ! empty( $custom_fields['saswp_article_author_global_mapping']['url']) ) {
                                $input1['author']['url'] =    $custom_fields['saswp_article_author_global_mapping']['url'];
                            }

                            if ( ! empty( $custom_fields['saswp_article_author_global_mapping']['description']) ) {
                                $input1['author']['description'] =    $custom_fields['saswp_article_author_global_mapping']['description'];
                            }

                            if ( ! empty( $custom_fields['saswp_article_author_global_mapping']['custom_fields']['honorificsuffix'][0]) ) {
                                $input1['author']['honorificSuffix'] =    $custom_fields['saswp_article_author_global_mapping']['custom_fields']['honorificsuffix'][0];
                            }

                            if ( ! empty( $custom_fields['saswp_article_author_global_mapping']['custom_fields']['knowsabout'][0]) ) {
                                $input1['author']['knowsAbout'] =   explode(',', $custom_fields['saswp_article_author_global_mapping']['custom_fields']['knowsabout'][0]);
                            }

                            $sameas = array();
                            if ( ! empty( $custom_fields['saswp_article_author_global_mapping']['custom_fields']['team_facebook'][0]) ) {
                                $sameas[] =  $custom_fields['saswp_article_author_global_mapping']['custom_fields']['team_facebook'][0];
                            }

                            if ( ! empty( $custom_fields['saswp_article_author_global_mapping']['custom_fields']['team_twitter'][0]) ) {
                                $sameas[] =  $custom_fields['saswp_article_author_global_mapping']['custom_fields']['team_twitter'][0];
                            }

                            if ( ! empty( $custom_fields['saswp_article_author_global_mapping']['custom_fields']['team_linkedin'][0]) ) {
                                $sameas[] =   $custom_fields['saswp_article_author_global_mapping']['custom_fields']['team_linkedin'][0];
                            }

                            if ( ! empty( $custom_fields['saswp_article_author_global_mapping']['custom_fields']['team_instagram'][0]) ) {
                                $sameas[] =   $custom_fields['saswp_article_author_global_mapping']['custom_fields']['team_instagram'][0];
                            }

                            if ( ! empty( $custom_fields['saswp_article_author_global_mapping']['custom_fields']['team_youtube'][0]) ) {
                                $sameas[] =   $custom_fields['saswp_article_author_global_mapping']['custom_fields']['team_youtube'][0];
                            }
                            if($sameas){
                                $input1['author']['sameAs'] = $sameas;
                            }

                            if ( ! empty( $custom_fields['saswp_article_author_global_mapping']['custom_fields']['alumniof'][0]) ) {
                                $str =  $custom_fields['saswp_article_author_global_mapping']['custom_fields']['alumniof'][0];
                                $itemlist = explode(",", $str);
                                foreach ( $itemlist as $key => $list){
                                    $vnewarr['@type'] = 'Organization';
                                    $vnewarr['Name']   = $list;   
                                    $input1['author']['alumniOf'][] = $vnewarr;
                                }
                            }
                        }else{
                           
                            if ( isset( $custom_fields['saswp_article_author_type']) ) {
                                $input1['author']['@type'] =    $custom_fields['saswp_article_author_type'];
                            }
                            if ( isset( $custom_fields['saswp_article_author_name']) ) {
                             $input1['author']['name'] =    $custom_fields['saswp_article_author_name'];
                            }
                            if ( isset( $custom_fields['saswp_article_author_honorific_suffix']) ) {
                                $input1['author']['honorificSuffix'] =    $custom_fields['saswp_article_author_honorific_suffix'];
                            }
                            if ( isset( $custom_fields['saswp_article_author_description']) ) {
                                $input1['author']['description'] =    $custom_fields['saswp_article_author_description'];
                            }
                            if ( isset( $custom_fields['saswp_article_author_url']) ) {
                                $input1['author']['url'] =    $custom_fields['saswp_article_author_url'];
                            }
                            if ( isset( $custom_fields['saswp_article_author_image']) ) {
                                $input1['author']['Image']['url'] =    $custom_fields['saswp_article_author_image'];
                            }
                            if ( isset( $custom_fields['saswp_article_author_social_profile']) && !empty($custom_fields['saswp_article_author_social_profile']) ) {
                                $explode_sp = explode(',', $custom_fields['saswp_article_author_social_profile']);
                                if ( is_array( $explode_sp) ) {
                                    $input1['author']['sameAs'] =    $explode_sp;
                                }
                            }
                            if ( isset( $custom_fields['saswp_article_author_jobtitle']) ) {
                                $input1['author']['JobTitle'] =    $custom_fields['saswp_article_author_jobtitle'];
                            }
                        }

                        if ( isset( $custom_fields['saswp_article_organization_logo']) && isset($custom_fields['saswp_article_organization_name']) ) {
                         $input1['publisher']['@type']       =    'Organization';
                         $input1['publisher']['name']        =    $custom_fields['saswp_article_organization_name'];
                         $input1['publisher']['logo']        =    $custom_fields['saswp_article_organization_logo'];
                        }  

                        if ( ! empty( $custom_fields['saswp_article_reviewedby_global_mapping']) ) {
                            $input1['reviewedBy'] = array();
                            if ( ! empty( $custom_fields['saswp_article_reviewedby_global_mapping']) ) {
                                $input1['reviewedBy']['@type'] =   "Person";
                            }

                            if ( ! empty( $custom_fields['saswp_article_reviewedby_global_mapping']['name']) ) {
                                $input1['reviewedBy']['name'] =    $custom_fields['saswp_article_reviewedby_global_mapping']['name'];
                            }

                            if ( ! empty( $custom_fields['saswp_article_reviewedby_global_mapping']['url']) ) {
                                $input1['reviewedBy']['url'] =    $custom_fields['saswp_article_reviewedby_global_mapping']['url'];
                            }

                            if ( ! empty( $custom_fields['saswp_article_reviewedby_global_mapping']['description']) ) {
                                $input1['reviewedBy']['description'] =    $custom_fields['saswp_article_reviewedby_global_mapping']['description'];
                            }

                            if ( ! empty( $custom_fields['saswp_article_reviewedby_global_mapping']['custom_fields']['honorificsuffix'][0]) ) {
                                $input1['reviewedBy']['honorificSuffix'] =    $custom_fields['saswp_article_reviewedby_global_mapping']['custom_fields']['honorificsuffix'][0];
                            }

                            if ( ! empty( $custom_fields['saswp_article_reviewedby_global_mapping']['custom_fields']['knowsabout'][0]) ) {
                                $input1['reviewedBy']['knowsAbout'] =   explode(',', $custom_fields['saswp_article_reviewedby_global_mapping']['custom_fields']['knowsabout'][0]);
                            }
                            
                            $sameas = array();
                            if ( ! empty( $custom_fields['saswp_article_reviewedby_global_mapping']['custom_fields']['team_facebook'][0]) ) {
                                $sameas[] =    $custom_fields['saswp_article_reviewedby_global_mapping']['custom_fields']['team_facebook'][0];
                            }
                            if ( ! empty( $custom_fields['saswp_article_reviewedby_global_mapping']['custom_fields']['team_twitter'][0]) ) {
                                $sameas[] =    $custom_fields['saswp_article_reviewedby_global_mapping']['custom_fields']['team_twitter'][0];
                            }
                            if ( ! empty( $custom_fields['saswp_article_reviewedby_global_mapping']['custom_fields']['team_linkedin'][0]) ) {
                                $sameas[] =    $custom_fields['saswp_article_reviewedby_global_mapping']['custom_fields']['team_linkedin'][0];
                            }
                            if ( ! empty( $custom_fields['saswp_article_reviewedby_global_mapping']['custom_fields']['team_instagram'][0]) ) {
                                $sameas[] =     $custom_fields['saswp_article_reviewedby_global_mapping']['custom_fields']['team_instagram'][0];
                            }
                            if ( ! empty( $custom_fields['saswp_article_reviewedby_global_mapping']['custom_fields']['team_youtube'][0]) ) {
                                $sameas[] =    $custom_fields['saswp_article_reviewedby_global_mapping']['custom_fields']['team_youtube'][0];
                            }
                            if($sameas){
                                $input1['reviewedBy']['sameAs'] = $sameas;
                            }

                            if ( ! empty( $custom_fields['saswp_article_reviewedby_global_mapping']['custom_fields']['reviewer_image']) ) {
                                $input1['reviewedBy']['image']  = $custom_fields['saswp_article_reviewedby_global_mapping']['custom_fields']['reviewer_image'];
                            }
                        
                            if ( ! empty( $custom_fields['saswp_article_reviewedby_global_mapping']['custom_fields']['alumniof'][0]) ) {
                                $str =  $custom_fields['saswp_article_reviewedby_global_mapping']['custom_fields']['alumniof'][0];
                                $itemlist = explode(",", $str);
                                foreach ( $itemlist as $key => $list){
                                    $vnewarr['@type'] = 'Organization';
                                    $vnewarr['Name']   = $list;   
                                    $input1['reviewedBy']['alumniOf'][] = $vnewarr;
                                }
                            }


                        }else{

                            if ( isset( $custom_fields['saswp_article_reviewedby_type']) ) {
                                $input1['reviewedBy']['@type'] =    $custom_fields['saswp_article_reviewedby_type'];
                            }
                            if ( isset( $custom_fields['saswp_article_reviewedby_name']) ) {
                            $input1['reviewedBy']['name'] =    $custom_fields['saswp_article_reviewedby_name'];
                            }
                            if ( isset( $custom_fields['saswp_article_reviewedby_honorific_suffix']) ) {
                                $input1['reviewedBy']['honorificSuffix'] =    $custom_fields['saswp_article_reviewedby_honorific_suffix'];
                            }
                            if ( isset( $custom_fields['saswp_article_reviewedby_description']) ) {
                            $input1['reviewedBy']['description'] =    $custom_fields['saswp_article_reviewedby_description'];
                            }
                            if ( isset( $custom_fields['saswp_article_reviewedby_url']) ) {
                            $input1['reviewedBy']['url'] =    $custom_fields['saswp_article_reviewedby_url'];
                            }    
                        
                            if ( isset( $custom_fields['saswp_article_alumniof']) ) {
                                $str = $custom_fields['saswp_article_alumniof'];
                                $itemlist = explode(",", $str);
                                foreach ( $itemlist as $key => $list){
                                    $vnewarr['@type'] = 'Organization';
                                    $vnewarr['Name']   = $list;   
                                    $input1['alumniOf'][] = $vnewarr;
                                }
                            }
                            if ( isset( $custom_fields['saswp_article_knowsabout']) ) {                            
                                $input1['knowsAbout'] = explode(',', $custom_fields['saswp_article_knowsabout']);    
                            }  
                        }

                        if ( ! empty( $custom_fields['saswp_article_about']) && isset($custom_fields['saswp_article_about']) ) {         
                            $explode_about = explode(',', $custom_fields['saswp_article_about']);
                            if ( ! empty( $explode_about) ) {
                                $about_arr = array();
                                foreach( $explode_about as $val){
                                    $about_arr[] = array(
                                                '@type' => 'Thing',
                                                'name'  => $val
                                    );
                                }
                                $input1['about'] = $about_arr;
                            }                            
                        }  
                                     
                        break; 

                        case 'ScholarlyArticle': 
                                 
                            if ( isset( $custom_fields['saswp_scholarlyarticle_id']) ) {
                                $input1['@id'] =    get_permalink().'#'.$custom_fields['saswp_scholarlyarticle_id'];
                            }
                            if ( isset( $custom_fields['saswp_scholarlyarticle_main_entity_of_page']) ) {
                             $input1['mainEntityOfPage'] =    $custom_fields['saswp_scholarlyarticle_main_entity_of_page'];
                            }
                            if ( isset( $custom_fields['saswp_scholarlyarticle_image']) ) {
                             $input1['image'] =    $custom_fields['saswp_scholarlyarticle_image'];
                            }
                            if ( isset( $custom_fields['saswp_scholarlyarticle_url']) ) {
                             $input1['url'] =    saswp_validate_url($custom_fields['saswp_scholarlyarticle_url']);
                            }
                            if ( isset( $custom_fields['saswp_scholarlyarticle_body']) ) {
                             $input1['articleBody'] =    $custom_fields['saswp_scholarlyarticle_body'];
                            }
                            if ( isset( $custom_fields['saswp_scholarlyarticle_keywords']) ) {
                             $input1['keywords'] =    $custom_fields['saswp_scholarlyarticle_keywords'];
                            }
                            if ( isset( $custom_fields['saswp_scholarlyarticle_section']) ) {
                             $input1['articleSection'] =    $custom_fields['saswp_scholarlyarticle_section'];
                            }
                            if ( isset( $custom_fields['saswp_scholarlyarticle_inlanguage']) ) {
                                $input1['inLanguage'] =    $custom_fields['saswp_scholarlyarticle_inlanguage'];
                            }
                            if ( isset( $custom_fields['saswp_scholarlyarticle_headline']) ) {
                             $input1['headline'] =    $custom_fields['saswp_scholarlyarticle_headline'];
                            }                    
                            if ( isset( $custom_fields['saswp_scholarlyarticle_description']) ) {
                             $input1['description'] =    wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_scholarlyarticle_description'] ));
                            }
                            if ( isset( $custom_fields['saswp_scholarlyarticle_date_published']) ) {
                             $input1['datePublished'] =    $custom_fields['saswp_scholarlyarticle_date_published'];
                            }
                            if ( isset( $custom_fields['saswp_scholarlyarticle_date_modified']) ) {
                             $input1['dateModified'] =    $custom_fields['saswp_scholarlyarticle_date_modified'];
                            }
                           
                            if ( ! empty( $custom_fields['saswp_scholarlyarticle_editor_global_mapping']) ) {
                                $input1['editor'] = array();
                                if ( ! empty( $custom_fields['saswp_scholarlyarticle_editor_global_mapping']) ) {
                                    $input1['editor']['@type'] =   "Person";
                                }
    
                                if ( ! empty( $custom_fields['saswp_scholarlyarticle_editor_global_mapping']['name']) ) {
                                    $input1['editor']['name'] =    $custom_fields['saswp_scholarlyarticle_editor_global_mapping']['name'];
                                }
    
                                if ( ! empty( $custom_fields['saswp_scholarlyarticle_editor_global_mapping']['url']) ) {
                                    $input1['editor']['url'] =    $custom_fields['saswp_scholarlyarticle_editor_global_mapping']['url'];
                                }
    
                                if ( ! empty( $custom_fields['saswp_scholarlyarticle_editor_global_mapping']['description']) ) {
                                    $input1['editor']['description'] =    $custom_fields['saswp_scholarlyarticle_editor_global_mapping']['description'];
                                }
    
                                if ( ! empty( $custom_fields['saswp_scholarlyarticle_editor_global_mapping']['custom_fields']['honorificsuffix'][0]) ) {
                                    $input1['editor']['honorificSuffix'] =    $custom_fields['saswp_scholarlyarticle_editor_global_mapping']['custom_fields']['honorificsuffix'][0];
                                }
    
                                if ( ! empty( $custom_fields['saswp_scholarlyarticle_editor_global_mapping']['custom_fields']['knowsabout'][0]) ) {
                                    $input1['editor']['knowsAbout'] =   explode(',', $custom_fields['saswp_scholarlyarticle_editor_global_mapping']['custom_fields']['knowsabout'][0]);
                                }
    
                                $sameas = array();
                                if ( ! empty( $custom_fields['saswp_scholarlyarticle_editor_global_mapping']['custom_fields']['team_facebook'][0]) ) {
                                    $sameas[] =  $custom_fields['saswp_scholarlyarticle_editor_global_mapping']['custom_fields']['team_facebook'][0];
                                }
    
                                if ( ! empty( $custom_fields['saswp_scholarlyarticle_editor_global_mapping']['custom_fields']['team_twitter'][0]) ) {
                                    $sameas[] =  $custom_fields['saswp_scholarlyarticle_editor_global_mapping']['custom_fields']['team_twitter'][0];
                                }
    
                                if ( ! empty( $custom_fields['saswp_scholarlyarticle_editor_global_mapping']['custom_fields']['team_linkedin'][0]) ) {
                                    $sameas[] =   $custom_fields['saswp_scholarlyarticle_editor_global_mapping']['custom_fields']['team_linkedin'][0];
                                }
    
                                if ( ! empty( $custom_fields['saswp_ascholarlyarticle_editor_global_mapping']['custom_fields']['team_instagram'][0]) ) {
                                    $sameas[] =   $custom_fields['saswp_scholarlyarticle_editor_global_mapping']['custom_fields']['team_instagram'][0];
                                }
    
                                if ( ! empty( $custom_fields['saswp_scholarlyarticle_editor_global_mapping']['custom_fields']['team_youtube'][0]) ) {
                                    $sameas[] =   $custom_fields['saswp_scholarlyarticle_editor_global_mapping']['custom_fields']['team_youtube'][0];
                                }
                                if($sameas){
                                    $input1['editor']['sameAs'] = $sameas;
                                }
    
                                if ( ! empty( $custom_fields['saswp_scholarlyarticle_editor_global_mapping']['custom_fields']['alumniof'][0]) ) {
                                    $str =  $custom_fields['saswp_scholarlyarticle_editor_global_mapping']['custom_fields']['alumniof'][0];
                                    $itemlist = explode(",", $str);
                                    foreach ( $itemlist as $key => $list){
                                        $vnewarr['@type'] = 'Organization';
                                        $vnewarr['Name']   = $list;   
                                        $input1['editor']['alumniOf'][] = $vnewarr;
                                    }
                                }
                            }else{
                               
                                if ( isset( $custom_fields['saswp_scholarlyarticle_editor_type']) ) {
                                    $input1['editor']['@type'] =    $custom_fields['saswp_scholarlyarticle_editor_type'];
                                }
                                if ( isset( $custom_fields['saswp_scholarlyarticle_editor_name']) ) {
                                 $input1['editor']['name'] =    $custom_fields['saswp_scholarlyarticle_editor_name'];
                                }
                                if ( isset( $custom_fields['saswp_scholarlyarticle_editor_honorific_suffix']) ) {
                                    $input1['editor']['honorificSuffix'] =    $custom_fields['saswp_scholarlyarticle_editor_honorific_suffix'];
                                }
                                if ( isset( $custom_fields['saswp_scholarlyarticle_editor_description']) ) {
                                    $input1['editor']['description'] =    $custom_fields['saswp_scholarlyarticle_editor_description'];
                                }
                                if ( isset( $custom_fields['saswp_scholarlyarticle_editor_url']) ) {
                                    $input1['editor']['url'] =    $custom_fields['saswp_scholarlyarticle_editor_url'];
                                }
                                if ( isset( $custom_fields['saswp_scholarlyarticle_editor_image']) ) {
                                    $input1['editor']['Image']['url'] =    $custom_fields['saswp_scholarlyarticle_editor_image'];
                                }
                                if ( isset( $custom_fields['saswp_scholarlyarticle_editor_jobtitle']) ) {
                                    $input1['editor']['JobTitle'] =    $custom_fields['saswp_scholarlyarticle_editor_jobtitle'];
                                }
                            }
    
                            if ( ! empty( $custom_fields['saswp_scholarlyarticle_author_global_mapping']) ) {
                                $input1['author'] = array();
                                if ( ! empty( $custom_fields['saswp_scholarlyarticle_author_global_mapping']) ) {
                                    $input1['author']['@type'] =   "Person";
                                }
    
                                if ( ! empty( $custom_fields['saswp_scholarlyarticle_author_global_mapping']['name']) ) {
                                    $input1['author']['name'] =    $custom_fields['saswp_scholarlyarticle_author_global_mapping']['name'];
                                }
    
                                if ( ! empty( $custom_fields['saswp_scholarlyarticle_author_global_mapping']['url']) ) {
                                    $input1['author']['url'] =    $custom_fields['saswp_scholarlyarticle_author_global_mapping']['url'];
                                }
    
                                if ( ! empty( $custom_fields['saswp_scholarlyarticle_author_global_mapping']['description']) ) {
                                    $input1['author']['description'] =    $custom_fields['saswp_scholarlyarticle_author_global_mapping']['description'];
                                }
    
                                if ( ! empty( $custom_fields['saswp_scholarlyarticle_author_global_mapping']['custom_fields']['honorificsuffix'][0]) ) {
                                    $input1['author']['honorificSuffix'] =    $custom_fields['saswp_scholarlyarticle_author_global_mapping']['custom_fields']['honorificsuffix'][0];
                                }
    
                                if ( ! empty( $custom_fields['saswp_scholarlyarticle_author_global_mapping']['custom_fields']['knowsabout'][0]) ) {
                                    $input1['author']['knowsAbout'] =   explode(',', $custom_fields['saswp_scholarlyarticle_author_global_mapping']['custom_fields']['knowsabout'][0]);
                                }
    
                                $sameas = array();
                                if ( ! empty( $custom_fields['saswp_scholarlyarticle_author_global_mapping']['custom_fields']['team_facebook'][0]) ) {
                                    $sameas[] =  $custom_fields['saswp_scholarlyarticle_author_global_mapping']['custom_fields']['team_facebook'][0];
                                }
    
                                if ( ! empty( $custom_fields['saswp_scholarlyarticle_author_global_mapping']['custom_fields']['team_twitter'][0]) ) {
                                    $sameas[] =  $custom_fields['saswp_scholarlyarticle_author_global_mapping']['custom_fields']['team_twitter'][0];
                                }
    
                                if ( ! empty( $custom_fields['saswp_scholarlyarticle_author_global_mapping']['custom_fields']['team_linkedin'][0]) ) {
                                    $sameas[] =   $custom_fields['saswp_scholarlyarticle_author_global_mapping']['custom_fields']['team_linkedin'][0];
                                }
    
                                if ( ! empty( $custom_fields['saswp_scholarlyarticle_author_global_mapping']['custom_fields']['team_instagram'][0]) ) {
                                    $sameas[] =   $custom_fields['saswp_scholarlyarticle_author_global_mapping']['custom_fields']['team_instagram'][0];
                                }
    
                                if ( ! empty( $custom_fields['saswp_scholarlyarticle_author_global_mapping']['custom_fields']['team_youtube'][0]) ) {
                                    $sameas[] =   $custom_fields['saswp_scholarlyarticle_author_global_mapping']['custom_fields']['team_youtube'][0];
                                }
                                if($sameas){
                                    $input1['author']['sameAs'] = $sameas;
                                }
    
                                if ( ! empty( $custom_fields['saswp_scholarlyarticle_author_global_mapping']['custom_fields']['alumniof'][0]) ) {
                                    $str =  $custom_fields['saswp_scholarlyarticle_author_global_mapping']['custom_fields']['alumniof'][0];
                                    $itemlist = explode(",", $str);
                                    foreach ( $itemlist as $key => $list){
                                        $vnewarr['@type'] = 'Organization';
                                        $vnewarr['Name']   = $list;   
                                        $input1['author']['alumniOf'][] = $vnewarr;
                                    }
                                }
                            }else{
                               
                                if ( isset( $custom_fields['saswp_scholarlyarticle_author_type']) ) {
                                    $input1['author']['@type'] =    $custom_fields['saswp_scholarlyarticle_author_type'];
                                }
                                if ( isset( $custom_fields['saswp_scholarlyarticle_author_name']) ) {
                                 $input1['author']['name'] =    $custom_fields['saswp_scholarlyarticle_author_name'];
                                }
                                if ( isset( $custom_fields['saswp_scholarlyarticle_author_honorific_suffix']) ) {
                                    $input1['author']['honorificSuffix'] =    $custom_fields['saswp_scholarlyarticle_author_honorific_suffix'];
                                }
                                if ( isset( $custom_fields['saswp_scholarlyarticle_author_description']) ) {
                                    $input1['author']['description'] =    $custom_fields['saswp_scholarlyarticle_author_description'];
                                }
                                if ( isset( $custom_fields['saswp_scholarlyarticle_author_url']) ) {
                                    $input1['author']['url'] =    $custom_fields['saswp_scholarlyarticle_author_url'];
                                }
                                if ( isset( $custom_fields['saswp_scholarlyarticle_author_image']) ) {
                                    $input1['author']['Image']['url'] =    $custom_fields['saswp_scholarlyarticle_author_image'];
                                }
                                if ( isset( $custom_fields['saswp_scholarlyarticle_author_jobtitle']) ) {
                                    $input1['author']['JobTitle'] =    $custom_fields['saswp_scholarlyarticle_author_jobtitle'];
                                }
                            }
    
                            if ( isset( $custom_fields['saswp_scholarlyarticle_organization_logo']) && isset($custom_fields['saswp_scholarlyarticle_organization_name']) ) {
                             $input1['publisher']['@type']       =    'Organization';
                             $input1['publisher']['name']        =    $custom_fields['saswp_scholarlyarticle_organization_name'];
                             $input1['publisher']['logo']        =    $custom_fields['saswp_scholarlyarticle_organization_logo'];
                            }  
    
                            if ( ! empty( $custom_fields['saswp_scholarlyarticle_reviewedby_global_mapping']) ) {
                                $input1['reviewedBy'] = array();
                                if ( ! empty( $custom_fields['saswp_scholarlyarticle_reviewedby_global_mapping']) ) {
                                    $input1['reviewedBy']['@type'] =   "Person";
                                }
    
                                if ( ! empty( $custom_fields['saswp_scholarlyarticle_reviewedby_global_mapping']['name']) ) {
                                    $input1['reviewedBy']['name'] =    $custom_fields['saswp_scholarlyarticle_reviewedby_global_mapping']['name'];
                                }
    
                                if ( ! empty( $custom_fields['saswp_scholarlyarticle_reviewedby_global_mapping']['url']) ) {
                                    $input1['reviewedBy']['url'] =    $custom_fields['saswp_scholarlyarticle_reviewedby_global_mapping']['url'];
                                }
    
                                if ( ! empty( $custom_fields['saswp_scholarlyarticle_reviewedby_global_mapping']['description']) ) {
                                    $input1['reviewedBy']['description'] =    $custom_fields['saswp_scholarlyarticle_reviewedby_global_mapping']['description'];
                                }
    
                                if ( ! empty( $custom_fields['saswp_scholarlyarticle_reviewedby_global_mapping']['custom_fields']['honorificsuffix'][0]) ) {
                                    $input1['reviewedBy']['honorificSuffix'] =    $custom_fields['saswp_scholarlyarticle_reviewedby_global_mapping']['custom_fields']['honorificsuffix'][0];
                                }
    
                                if ( ! empty( $custom_fields['saswp_scholarlyarticle_reviewedby_global_mapping']['custom_fields']['knowsabout'][0]) ) {
                                    $input1['reviewedBy']['knowsAbout'] =   explode(',', $custom_fields['saswp_scholarlyarticle_reviewedby_global_mapping']['custom_fields']['knowsabout'][0]);
                                }
                                
                                $sameas = array();
                                if ( ! empty( $custom_fields['saswp_scholarlyarticle_reviewedby_global_mapping']['custom_fields']['team_facebook'][0]) ) {
                                    $sameas[] =    $custom_fields['saswp_scholarlyarticle_reviewedby_global_mapping']['custom_fields']['team_facebook'][0];
                                }
                                if ( ! empty( $custom_fields['saswp_scholarlyarticle_reviewedby_global_mapping']['custom_fields']['team_twitter'][0]) ) {
                                    $sameas[] =    $custom_fields['saswp_scholarlyarticle_reviewedby_global_mapping']['custom_fields']['team_twitter'][0];
                                }
                                if ( ! empty( $custom_fields['saswp_scholarlyarticle_reviewedby_global_mapping']['custom_fields']['team_linkedin'][0]) ) {
                                    $sameas[] =    $custom_fields['saswp_scholarlyarticle_reviewedby_global_mapping']['custom_fields']['team_linkedin'][0];
                                }
                                if ( ! empty( $custom_fields['saswp_scholarlyarticle_reviewedby_global_mapping']['custom_fields']['team_instagram'][0]) ) {
                                    $sameas[] =     $custom_fields['saswp_scholarlyarticle_reviewedby_global_mapping']['custom_fields']['team_instagram'][0];
                                }
                                if ( ! empty( $custom_fields['saswp_scholarlyarticle_reviewedby_global_mapping']['custom_fields']['team_youtube'][0]) ) {
                                    $sameas[] =    $custom_fields['saswp_scholarlyarticle_reviewedby_global_mapping']['custom_fields']['team_youtube'][0];
                                }
                                if($sameas){
                                    $input1['reviewedBy']['sameAs'] = $sameas;
                                }
    
                                if ( ! empty( $custom_fields['saswp_scholarlyarticle_reviewedby_global_mapping']['custom_fields']['reviewer_image']) ) {
                                    $input1['reviewedBy']['image']  = $custom_fields['saswp_scholarlyarticle_reviewedby_global_mapping']['custom_fields']['reviewer_image'];
                                }
                            
                                if ( ! empty( $custom_fields['saswp_scholarlyarticle_reviewedby_global_mapping']['custom_fields']['alumniof'][0]) ) {
                                    $str =  $custom_fields['saswp_scholarlyarticle_reviewedby_global_mapping']['custom_fields']['alumniof'][0];
                                    $itemlist = explode(",", $str);
                                    foreach ( $itemlist as $key => $list){
                                        $vnewarr['@type'] = 'Organization';
                                        $vnewarr['Name']   = $list;   
                                        $input1['reviewedBy']['alumniOf'][] = $vnewarr;
                                    }
                                }
    
    
                            }else{
    
                                if ( isset( $custom_fields['saswp_scholarlyarticle_reviewedby_type']) ) {
                                    $input1['reviewedBy']['@type'] =    $custom_fields['saswp_scholarlyarticle_reviewedby_type'];
                                }
                                if ( isset( $custom_fields['saswp_scholarlyarticle_reviewedby_name']) ) {
                                $input1['reviewedBy']['name'] =    $custom_fields['saswp_scholarlyarticle_reviewedby_name'];
                                }
                                if ( isset( $custom_fields['saswp_scholarlyarticle_reviewedby_honorific_suffix']) ) {
                                    $input1['reviewedBy']['honorificSuffix'] =    $custom_fields['saswp_scholarlyarticle_reviewedby_honorific_suffix'];
                                }
                                if ( isset( $custom_fields['saswp_scholarlyarticle_reviewedby_description']) ) {
                                $input1['reviewedBy']['description'] =    $custom_fields['saswp_scholarlyarticle_reviewedby_description'];
                                }
                                if ( isset( $custom_fields['saswp_scholarlyarticle_reviewedby_url']) ) {
                                $input1['reviewedBy']['url'] =    $custom_fields['saswp_scholarlyarticle_reviewedby_url'];
                                }    
                            
                                if ( isset( $custom_fields['saswp_scholarlyarticle_alumniof']) ) {
                                    $str = $custom_fields['saswp_scholarlyarticle_alumniof'];
                                    $itemlist = explode(",", $str);
                                    foreach ( $itemlist as $key => $list){
                                        $vnewarr['@type'] = 'Organization';
                                        $vnewarr['Name']   = $list;   
                                        $input1['alumniOf'][] = $vnewarr;
                                    }
                                }
                                if ( isset( $custom_fields['saswp_scholarlyarticle_knowsabout']) ) {                            
                                    $input1['knowsAbout'] = explode(',', $custom_fields['saswp_scholarlyarticle_knowsabout']);    
                                }  
                            }
    
                            if ( ! empty( $custom_fields['saswp_scholarlyarticle_about']) && isset($custom_fields['saswp_scholarlyarticle_about']) ) {         
                                $explode_about = explode(',', $custom_fields['saswp_scholarlyarticle_about']);
                                if ( ! empty( $explode_about) ) {
                                    $about_arr = array();
                                    foreach( $explode_about as $val){
                                        $about_arr[] = array(
                                                    '@type' => 'Thing',
                                                    'name'  => $val
                                        );
                                    }
                                    $input1['about'] = $about_arr;
                                }                            
                            }  
                                         
                            break; 

                    case 'CreativeWork':   
                        if ( isset( $custom_fields['saswp_creativework_id']) ) {
                            $input1['@id'] =    get_permalink().$custom_fields['saswp_creativework_id'];
                        } 
                        if ( isset( $custom_fields['saswp_creativework_main_entity_of_page']) ) {
                            $input1['mainEntityOfPage'] =    $custom_fields['saswp_creativework_main_entity_of_page'];
                        }
                        if ( isset( $custom_fields['saswp_creativework_image']) ) {
                            $input1['image'] =    $custom_fields['saswp_creativework_image'];
                        }
                        if ( isset( $custom_fields['saswp_creativework_url']) ) {
                            $input1['url'] =    saswp_validate_url($custom_fields['saswp_creativework_url']);
                        }
                        if ( isset( $custom_fields['saswp_creativework_body']) ) {
                            if($custom_fields['saswp_creativework_body']){
                                $input1['articleBody'] =    $custom_fields['saswp_creativework_body'];
                            }else{
                                unset($input1['articleBody']);
                            }
                        }
                        if ( isset( $custom_fields['saswp_creativework_keywords']) ) {
                            $input1['keywords'] =    $custom_fields['saswp_creativework_keywords'];
                        }
                        if ( isset( $custom_fields['saswp_creativework_section']) ) {
                            $input1['articleSection'] =    $custom_fields['saswp_creativework_section'];
                        }
                        if ( isset( $custom_fields['saswp_creativework_inlanguage']) ) {
                            $input1['inLanguage'] =    $custom_fields['saswp_creativework_inlanguage'];
                        }
                        if ( isset( $custom_fields['saswp_creativework_headline']) ) {
                            $input1['headline'] =    $custom_fields['saswp_creativework_headline'];
                        }                    
                        if ( isset( $custom_fields['saswp_creativework_description']) ) {
                            $input1['description'] =    wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_creativework_description'] ));
                        }
                        if ( isset( $custom_fields['saswp_creativework_date_published']) ) {
                            $input1['datePublished'] =    $custom_fields['saswp_creativework_date_published'];
                        }
                        if ( isset( $custom_fields['saswp_creativework_date_modified']) ) {
                            $input1['dateModified'] =    $custom_fields['saswp_creativework_date_modified'];
                        }
                        
                        if ( isset( $custom_fields['saswp_creativework_editor_name']) && $custom_fields['saswp_creativework_editor_name'] != '') {
                            $input1['editor'] = array();
                            $input1['editor']['@type'] = 'Person';
                            $input1['editor']['name']  =  $custom_fields['saswp_creativework_editor_name'];
                        }    
                        if ( isset( $custom_fields['saswp_creativework_editor_type']) ) {
                            $input1['editor']['@type'] =    $custom_fields['saswp_creativework_editor_type'];
                        } 
                        if ( isset( $custom_fields['saswp_creativework_editor_honorific_suffix']) && $custom_fields['saswp_creativework_editor_honorific_suffix'] != '') {
                            $input1['editor']['honorificSuffix']  =  $custom_fields['saswp_creativework_editor_honorific_suffix'];
                        }  
                        if ( isset( $custom_fields['saswp_creativework_editor_description']) ) {
                            $input1['editor']['description'] =    $custom_fields['saswp_creativework_editor_description'];
                        }
                        if ( isset( $custom_fields['saswp_creativework_editor_url']) ) {
                            $input1['editor']['url'] =    $custom_fields['saswp_creativework_editor_url'];
                        }
                        if ( isset( $custom_fields['saswp_creativework_editor_image']) ) {
                            $input1['editor']['Image']['url'] =    $custom_fields['saswp_creativework_editor_image'];
                        }
                        if ( ! empty( $custom_fields['saswp_creativework_author_global_mapping']) ) {
                       
                            if ( ! empty( $custom_fields['saswp_creativework_author_global_mapping']) ) {
                                $input1['author']['@type'] =   "Person";
                            }

                            if ( ! empty( $custom_fields['saswp_creativework_author_global_mapping']['name']) ) {
                                $input1['author']['name'] =    $custom_fields['saswp_creativework_author_global_mapping']['name'];
                            }

                            if ( ! empty( $custom_fields['saswp_creativework_author_global_mapping']['url']) ) {
                                $input1['author']['url'] =    $custom_fields['saswp_creativework_author_global_mapping']['url'];
                            }

                            if ( ! empty( $custom_fields['saswp_creativework_author_global_mapping']['description']) ) {
                                $input1['author']['description'] =    $custom_fields['saswp_creativework_author_global_mapping']['description'];
                            }

                            if ( ! empty( $custom_fields['saswp_creativework_author_global_mapping']['custom_fields']['honorificsuffix'][0]) ) {
                                $input1['author']['honorificSuffix'] =    $custom_fields['saswp_creativework_author_global_mapping']['custom_fields']['honorificsuffix'][0];
                            }

                            if ( ! empty( $custom_fields['saswp_creativework_author_global_mapping']['custom_fields']['knowsabout'][0]) ) {
                                $input1['author']['knowsAbout'] =   explode(',', $custom_fields['saswp_creativework_author_global_mapping']['custom_fields']['knowsabout'][0]);
                            }
                          
                            $sameas = array();
                            if ( ! empty( $custom_fields['saswp_creativework_author_global_mapping']['custom_fields']['team_facebook'][0]) ) {
                                $sameas[] =   $custom_fields['saswp_creativework_author_global_mapping']['custom_fields']['team_facebook'][0];
                            }

                            if ( ! empty( $custom_fields['saswp_creativework_author_global_mapping']['custom_fields']['team_twitter'][0]) ) {
                                $sameas[] =   $custom_fields['saswp_creativework_author_global_mapping']['custom_fields']['team_twitter'][0];
                            }

                            if ( ! empty( $custom_fields['saswp_creativework_author_global_mapping']['custom_fields']['team_linkedin'][0]) ) {
                                $sameas[] =   $custom_fields['saswp_creativework_author_global_mapping']['custom_fields']['team_linkedin'][0];
                            }

                            if ( ! empty( $custom_fields['saswp_creativework_author_global_mapping']['custom_fields']['team_instagram'][0]) ) {
                                $sameas[] =   $custom_fields['saswp_creativework_author_global_mapping']['custom_fields']['team_instagram'][0];
                            }

                            if ( ! empty( $custom_fields['saswp_creativework_author_global_mapping']['custom_fields']['team_youtube'][0]) ) {
                                $sameas[] =   $custom_fields['saswp_creativework_author_global_mapping']['custom_fields']['team_youtube'][0];
                            }
                            if($sameas){
                                $input1['author']['sameAs'] = $sameas;
                            } 

                            if ( ! empty( $custom_fields['saswp_creativework_author_global_mapping']['custom_fields']['alumniof'][0]) ) {
                                $str =  $custom_fields['saswp_creativework_author_global_mapping']['custom_fields']['alumniof'][0];
                                $itemlist = explode(",", $str);
                                foreach ( $itemlist as $key => $list){
                                    $vnewarr['@type'] = 'Organization';
                                    $vnewarr['Name']   = $list;   
                                    $input1['author']['alumniOf'][] = $vnewarr;
                                }
                            }
                        
                        }else{
                            
                            if ( isset( $custom_fields['saswp_creativework_author_type']) ) {
                                $input1['author']['@type'] =    $custom_fields['saswp_creativework_author_type'];
                            }
                            if ( isset( $custom_fields['saswp_creativework_author_name']) ) {
                                $input1['author']['name'] =    $custom_fields['saswp_creativework_author_name'];
                            }
                            if ( isset( $custom_fields['saswp_creativework_author_honorific_suffix']) ) {
                                $input1['author']['honorificSuffix'] =    $custom_fields['saswp_creativework_author_honorific_suffix'];
                            }
                            if ( isset( $custom_fields['saswp_creativework_author_description']) ) {
                                $input1['author']['description'] =    $custom_fields['saswp_creativework_author_description'];
                            }
                            if ( isset( $custom_fields['saswp_creativework_author_url']) ) {
                                $input1['author']['url'] =    $custom_fields['saswp_creativework_author_url'];
                            }
                            if ( isset( $custom_fields['saswp_creativework_author_jobtitle']) ) {
                                $input1['author']['JobTitle'] =    $custom_fields['saswp_creativework_author_jobtitle'];
                            }
                            if ( isset( $custom_fields['saswp_creativework_author_image']) ) {
                                $input1['author']['Image']['url'] =    $custom_fields['saswp_creativework_author_image'];  
                            } 
                            if ( isset( $custom_fields['saswp_creativework_knowsabout']) ) {                            
                                $input1['knowsAbout'] = explode(',', $custom_fields['saswp_creativework_knowsabout']);    
                            }
                            if ( isset( $custom_fields['saswp_creativework_size']) ) {                            
                                $input1['size'] = $custom_fields['saswp_creativework_size'];    
                            }
                            if ( isset( $custom_fields['saswp_creativework_license']) ) {                            
                                $input1['license'] = $custom_fields['saswp_creativework_license'];    
                            }
                        }

                        if ( ! empty( $custom_fields['saswp_creativework_about']) && isset($custom_fields['saswp_creativework_about']) ) {         
                            $input1['about']['@type'] = 'Event';                   
                            $input1['about']['name'] = explode(',', $custom_fields['saswp_creativework_about']);    
                        }

                        if ( ! empty( $custom_fields['saswp_creativework_reviewedby_global_mapping']) ) {
                            
                            if ( ! empty( $custom_fields['saswp_creativework_reviewedby_global_mapping']) ) {
                                $input1['reviewedBy']['@type'] =   "Person";
                            }

                            if ( ! empty( $custom_fields['saswp_creativework_reviewedby_global_mapping']['name']) ) {
                                $input1['reviewedBy']['name'] =    $custom_fields['saswp_creativework_reviewedby_global_mapping']['name'];
                            }

                            if ( ! empty( $custom_fields['saswp_creativework_reviewedby_global_mapping']['url']) ) {
                                $input1['reviewedBy']['url'] =    $custom_fields['saswp_creativework_reviewedby_global_mapping']['url'];
                            }

                            if ( ! empty( $custom_fields['saswp_creativework_reviewedby_global_mapping']['description']) ) {
                                $input1['reviewedBy']['description'] =    $custom_fields['saswp_creativework_reviewedby_global_mapping']['description'];
                            }

                            if ( ! empty( $custom_fields['saswp_creativework_reviewedby_global_mapping']['custom_fields']['honorificsuffix'][0]) ) {
                                $input1['reviewedBy']['honorificSuffix'] =    $custom_fields['saswp_creativework_reviewedby_global_mapping']['custom_fields']['honorificsuffix'][0];
                            }

                            if ( ! empty( $custom_fields['saswp_creativework_reviewedby_global_mapping']['custom_fields']['knowsabout'][0]) ) {
                                $input1['reviewedBy']['knowsAbout'] =   explode(',', $custom_fields['saswp_creativework_reviewedby_global_mapping']['custom_fields']['knowsabout'][0]);
                            }

                            if ( ! empty( $custom_fields['saswp_creativework_reviewedby_global_mapping']['custom_fields']['short_intro'][0]) ) {
                                $input1['reviewedBy']['ShortStory'] =   $custom_fields['saswp_creativework_reviewedby_global_mapping']['custom_fields']['short_intro'][0];
                            }

                            $sameas = array();
                            if ( ! empty( $custom_fields['saswp_creativework_reviewedby_global_mapping']['custom_fields']['team_facebook'][0]) ) {
                                $sameas[] =   $custom_fields['saswp_creativework_reviewedby_global_mapping']['custom_fields']['team_facebook'][0];
                            }

                            if ( ! empty( $custom_fields['saswp_creativework_reviewedby_global_mapping']['custom_fields']['team_twitter'][0]) ) {
                                $sameas[] =   $custom_fields['saswp_creativework_reviewedby_global_mapping']['custom_fields']['team_twitter'][0];
                            }

                            if ( ! empty( $custom_fields['saswp_creativework_reviewedby_global_mapping']['custom_fields']['team_linkedin'][0]) ) {
                                $sameas[] =   $custom_fields['saswp_creativework_reviewedby_global_mapping']['custom_fields']['team_linkedin'][0];
                            }

                            if ( ! empty( $custom_fields['saswp_creativework_reviewedby_global_mapping']['custom_fields']['team_instagram'][0]) ) {
                                $sameas[] =   $custom_fields['saswp_creativework_reviewedby_global_mapping']['custom_fields']['team_instagram'][0];
                            }

                            if ( ! empty( $custom_fields['saswp_creativework_reviewedby_global_mapping']['custom_fields']['team_youtube'][0]) ) {
                                $sameas[] =   $custom_fields['saswp_creativework_reviewedby_global_mapping']['custom_fields']['team_youtube'][0];
                            }
                            if($sameas){
                                $input1['reviewedBy']['sameAs'] = $sameas;
                            }

                            if ( ! empty( $custom_fields['saswp_creativework_reviewedby_global_mapping']['custom_fields']['reviewer_image']) ) {
                                $input1['reviewedBy']['image']  = $custom_fields['saswp_creativework_reviewedby_global_mapping']['custom_fields']['reviewer_image'];
                            }

                            if ( ! empty( $custom_fields['saswp_creativework_reviewedby_global_mapping']['custom_fields']['alumniof'][0]) ) {
                                $str =  $custom_fields['saswp_creativework_reviewedby_global_mapping']['custom_fields']['alumniof'][0];
                                $itemlist = explode(",", $str);
                                foreach ( $itemlist as $key => $list){
                                    $vnewarr['@type'] = 'Organization';
                                    $vnewarr['Name']   = $list;   
                                    $input1['reviewedBy']['alumniOf'][] = $vnewarr;
                                }
                            }

                        }else{
                        
                            if ( isset( $custom_fields['saswp_creativework_reviewedby_type']) ) {
                                $input1['reviewedBy']['@type'] =    $custom_fields['saswp_creativework_reviewedby_type'];
                            }
                            if ( isset( $custom_fields['saswp_creativework_reviewedby_name']) ) {
                                $input1['reviewedBy']['name'] =    $custom_fields['saswp_creativework_reviewedby_name'];
                            }
                            if ( isset( $custom_fields['saswp_creativework_reviewedby_honorific_suffix']) ) {
                                $input1['reviewedBy']['honorificSuffix'] =    $custom_fields['saswp_creativework_reviewedby_honorific_suffix'];
                            }
                            if ( isset( $custom_fields['saswp_creativework_reviewedby_description']) ) {
                                $input1['reviewedBy']['description'] =    $custom_fields['saswp_creativework_reviewedby_description'];
                            }
                            if ( isset( $custom_fields['saswp_creativework_reviewedby_url']) ) {
                                $input1['reviewedBy']['url'] =    $custom_fields['saswp_creativework_reviewedby_url'];
                            }
                            if ( isset( $custom_fields['saswp_creativework_alumniof']) ) {
                                $str = $custom_fields['saswp_creativework_alumniof'];
                                $itemlist = explode(",", $str);
                                foreach ( $itemlist as $key => $list){
                                    $vnewarr['@type'] = 'Organization';
                                    $vnewarr['Name']   = $list;   
                                    $input1['alumniOf'][] = $vnewarr;
                                }
                            }
                        }

                        if ( isset( $custom_fields['saswp_creativework_organization_logo']) && isset($custom_fields['saswp_creativework_organization_name']) ) {
                            $input1['publisher']['@type']       =    'Organization';
                            $input1['publisher']['name']        =    $custom_fields['saswp_creativework_organization_name'];
                            $input1['publisher']['logo']        =    $custom_fields['saswp_creativework_organization_logo'];
                        }                    
                        break; 

                    case 'VisualArtwork':      
                        if ( isset( $custom_fields['saswp_visualartwork_id']) ) {
                            $input1['@id'] =    get_permalink().$custom_fields['saswp_visualartwork_id'];
                           }                                         
                        if ( isset( $custom_fields['saswp_visualartwork_url']) ) {
                            $input1['url'] =    saswp_validate_url($custom_fields['saswp_visualartwork_url']);
                        }                                               
                        if ( isset( $custom_fields['saswp_visualartwork_name']) ) {
                            $input1['name'] =    $custom_fields['saswp_visualartwork_name'];
                        }
                        if ( isset( $custom_fields['saswp_visualartwork_alternate_name']) ) {
                            $input1['alternateName'] =    $custom_fields['saswp_visualartwork_alternate_name'];
                        }                    
                        if ( isset( $custom_fields['saswp_visualartwork_description']) ) {
                            $input1['description'] =    wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_visualartwork_description'] ));
                        }
                        if ( isset( $custom_fields['saswp_visualartwork_date_created']) ) {
                            $input1['dateCreated'] =    $custom_fields['saswp_visualartwork_date_created'];
                        } 
                        if ( isset( $custom_fields['saswp_visualartwork_image']) ) {
                            $input1['image'] =    $custom_fields['saswp_visualartwork_image'];
                        }
                        if ( isset( $custom_fields['saswp_visualartwork_artform']) ) {
                            $input1['artform'] =    $custom_fields['saswp_visualartwork_artform'];
                        }
                        if ( isset( $custom_fields['saswp_visualartwork_artedition']) ) {
                            $input1['artEdition'] =    $custom_fields['saswp_visualartwork_artedition'];
                        } 
                        if ( isset( $custom_fields['saswp_visualartwork_artwork_surface']) ) {
                            $input1['artworkSurface'] =    $custom_fields['saswp_visualartwork_artwork_surface'];
                        } 
                        if ( isset( $custom_fields['saswp_visualartwork_artmedium']) ) {                            
                            $input1['artMedium'] = explode(',', $custom_fields['saswp_visualartwork_artmedium']);    
                        }                        
                        if ( isset( $custom_fields['saswp_visualartwork_width']) ) {
                            $input1['width']['@type'] =    'Distance';
                            $input1['width']['name']  =    $custom_fields['saswp_visualartwork_width'];
                        }
                        if ( isset( $custom_fields['saswp_visualartwork_height']) ) {
                            $input1['height']['@type'] =    'Distance';
                            $input1['height']['name']  =    $custom_fields['saswp_visualartwork_height'];
                        }

                        if ( isset( $custom_fields['saswp_visualartwork_author_type']) ) {
                            $input1['creator']['@type'] =    $custom_fields['saswp_visualartwork_author_type'];
                        }
                        if ( isset( $custom_fields['saswp_visualartwork_author_name']) ) {
                            $input1['creator']['name'] =    $custom_fields['saswp_visualartwork_author_name'];
                        }
                        if ( isset( $custom_fields['saswp_visualartwork_author_description']) ) {
                            $input1['creator']['description'] =    $custom_fields['saswp_visualartwork_author_description'];
                        }
                        if ( isset( $custom_fields['saswp_visualartwork_author_url']) ) {
                            $input1['creator']['url'] =    $custom_fields['saswp_visualartwork_author_url'];
                        }
                        if ( isset( $custom_fields['saswp_visualartwork_size']) ) {
                            $input1['size'] =    $custom_fields['saswp_visualartwork_size'];
                        }
                        if ( isset( $custom_fields['saswp_visualartwork_license']) ) {
                            $input1['license'] =    $custom_fields['saswp_visualartwork_license'];
                        }
                         
                        
                        break;         
                case 'Photograph':      
                        if ( isset( $custom_fields['saswp_photograph_id']) ) {
                            $input1['@id'] =    get_permalink().$custom_fields['saswp_photograph_id'];
                        }                  
                       if ( isset( $custom_fields['saswp_photograph_image']) ) {
                        $input1['image'] =    $custom_fields['saswp_photograph_image'];
                       }
                       if ( isset( $custom_fields['saswp_photograph_url']) ) {
                        $input1['url'] =    saswp_validate_url($custom_fields['saswp_photograph_url']);
                       }                       
                       if ( isset( $custom_fields['saswp_photograph_inlanguage']) ) {
                           $input1['inLanguage'] =    $custom_fields['saswp_photograph_inlanguage'];
                       }
                       if ( isset( $custom_fields['saswp_photograph_headline']) ) {
                        $input1['headline'] =    $custom_fields['saswp_photograph_headline'];
                       }                    
                       if ( isset( $custom_fields['saswp_photograph_description']) ) {
                        $input1['description'] =    wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_photograph_description'] ));
                       }
                       if ( isset( $custom_fields['saswp_photograph_date_published']) ) {
                        $input1['datePublished'] =    $custom_fields['saswp_photograph_date_published'];
                       }
                       if ( isset( $custom_fields['saswp_photograph_date_modified']) ) {
                        $input1['dateModified'] =    $custom_fields['saswp_photograph_date_modified'];
                       }     
                       
                    if ( ! empty( $custom_fields['saswp_photograph_author_global_mapping']) ) {
                       
                        if ( ! empty( $custom_fields['saswp_photograph_author_global_mapping']) ) {
                            $input1['author']['@type'] =   "Person";
                        }

                        if ( ! empty( $custom_fields['saswp_photograph_author_global_mapping']['name']) ) {
                            $input1['author']['name'] =    $custom_fields['saswp_photograph_author_global_mapping']['name'];
                        }

                        if ( ! empty( $custom_fields['saswp_photograph_author_global_mapping']['url']) ) {
                            $input1['author']['url'] =    $custom_fields['saswp_photograph_author_global_mapping']['url'];
                        }

                        if ( ! empty( $custom_fields['saswp_photograph_author_global_mapping']['description']) ) {
                            $input1['author']['description'] =    $custom_fields['saswp_photograph_author_global_mapping']['description'];
                        }

                        if ( ! empty( $custom_fields['saswp_photograph_author_global_mapping']['custom_fields']['honorificsuffix'][0]) ) {
                            $input1['author']['honorificSuffix'] =    $custom_fields['saswp_photograph_author_global_mapping']['custom_fields']['honorificsuffix'][0];
                        }

                        if ( ! empty( $custom_fields['saswp_photograph_author_global_mapping']['custom_fields']['knowsabout'][0]) ) {
                            $input1['author']['knowsAbout'] =   explode(',', $custom_fields['saswp_photograph_author_global_mapping']['custom_fields']['knowsabout'][0]);
                        }

                        $sameas = array();
                        if ( ! empty( $custom_fields['saswp_photograph_author_global_mapping']['custom_fields']['team_facebook'][0]) ) {
                            $sameas[] =   $custom_fields['saswp_photograph_author_global_mapping']['custom_fields']['team_facebook'][0];
                        }

                        if ( ! empty( $custom_fields['saswp_photograph_author_global_mapping']['custom_fields']['team_twitter'][0]) ) {
                            $sameas[] =   $custom_fields['saswp_photograph_author_global_mapping']['custom_fields']['team_twitter'][0];
                        }

                        if ( ! empty( $custom_fields['saswp_photograph_author_global_mapping']['custom_fields']['team_linkedin'][0]) ) {
                            $sameas[] =   $custom_fields['saswp_photograph_author_global_mapping']['custom_fields']['team_linkedin'][0];
                        }

                        if ( ! empty( $custom_fields['saswp_photograph_author_global_mapping']['custom_fields']['team_instagram'][0]) ) {
                            $sameas[] =   $custom_fields['saswp_photograph_author_global_mapping']['custom_fields']['team_instagram'][0];
                        }

                        if ( ! empty( $custom_fields['saswp_photograph_author_global_mapping']['custom_fields']['team_youtube'][0]) ) {
                            $sameas[] =   $custom_fields['saswp_photograph_author_global_mapping']['custom_fields']['team_youtube'][0];
                        }
                        if($sameas){
                            $input1['author']['sameAs'] = $sameas;
                        }


                        if ( ! empty( $custom_fields['saswp_photograph_author_global_mapping']['custom_fields']['college_logo'][0]) ) {
                            $input1['author']['image'] = wp_get_attachment_image_url($custom_fields['saswp_photograph_author_global_mapping']['custom_fields']['college_logo'][0]);
                        }

                        if ( ! empty( $custom_fields['saswp_photograph_author_global_mapping']['custom_fields']['alumniof'][0]) ) {
                            $str =  $custom_fields['saswp_photograph_author_global_mapping']['custom_fields']['alumniof'][0];
                            $itemlist = explode(",", $str);
                            foreach ( $itemlist as $key => $list){
                                $vnewarr['@type'] = 'Organization';
                                $vnewarr['Name']   = $list;   
                                $input1['author']['alumniOf'][] = $vnewarr;
                            }
                        }
                    
                    }else{
                        if ( isset( $custom_fields['saswp_photograph_author_type']) ) {
                            $input1['author']['@type'] =    $custom_fields['saswp_photograph_author_type'];
                        }
                        if ( isset( $custom_fields['saswp_photograph_author_name']) ) {
                            $input1['author']['name'] =    $custom_fields['saswp_photograph_author_name'];
                        }
                        if ( isset( $custom_fields['saswp_photograph_author_jobtitle']) ) {
                            $input1['author']['JobTitle'] =    $custom_fields['saswp_photograph_author_jobtitle'];
                        }
                        
                        if ( isset( $custom_fields['saswp_photograph_author_honorific_suffix']) ) {
                            $input1['author']['honorificSuffix'] =    $custom_fields['saswp_photograph_author_honorific_suffix'];
                        }
                        if ( isset( $custom_fields['saswp_photograph_author_description']) ) {
                            $input1['author']['description'] =    $custom_fields['saswp_photograph_author_description'];
                        }
                        if ( isset( $custom_fields['saswp_photograph_author_url']) ) {
                            $input1['author']['url'] =    $custom_fields['saswp_photograph_author_url'];
                        }
                        if ( isset( $custom_fields['saswp_photograph_author_image']) ) {
                            $input1['author']['Image']['url'] =    $custom_fields['saswp_photograph_author_image'];  
                        }  
                        
                    }

                        if ( isset( $custom_fields['saswp_photograph_editor_type']) ) {
                            $input1['editor']['@type'] =    $custom_fields['saswp_photograph_editor_type'];
                        }
                        if ( isset( $custom_fields['saswp_photograph_editor_name']) ) {
                            $input1['editor']['name'] =    $custom_fields['saswp_photograph_editor_name'];
                        }
                        if ( isset( $custom_fields['saswp_photograph_editor_honorific_suffix']) ) {
                            $input1['editor']['honorificSuffix'] =    $custom_fields['saswp_photograph_editor_honorific_suffix'];
                        }
                        if ( isset( $custom_fields['saswp_photograph_editor_description']) ) {
                            $input1['editor']['description'] =    $custom_fields['saswp_photograph_editor_description'];
                        }
                        if ( isset( $custom_fields['saswp_photograph_editor_url']) ) {
                            $input1['editor']['url'] =    $custom_fields['saswp_photograph_editor_url'];
                        }
                        if ( isset( $custom_fields['saswp_photograph_editor_image']) ) {
                            $input1['editor']['Image']['url'] =    $custom_fields['saswp_photograph_editor_image'];  
                        }  


                       if ( isset( $custom_fields['saswp_photograph_organization_logo']) && isset($custom_fields['saswp_photograph_organization_name']) ) {
                        $input1['publisher']['@type']       =    'Organization';
                        $input1['publisher']['name']        =    $custom_fields['saswp_photograph_organization_name'];
                        $input1['publisher']['logo']        =    $custom_fields['saswp_photograph_organization_logo'];
                       } 

                    if ( ! empty( $custom_fields['saswp_photograph_reviewedby_global_mapping']) ) {
                           
                        if ( ! empty( $custom_fields['saswp_photograph_reviewedby_global_mapping']) ) {
                            $input1['reviewedBy']['@type'] =   "Person";
                        }

                        if ( ! empty( $custom_fields['saswp_photograph_reviewedby_global_mapping']['name']) ) {
                            $input1['reviewedBy']['name'] =    $custom_fields['saswp_photograph_reviewedby_global_mapping']['name'];
                        }

                        if ( ! empty( $custom_fields['saswp_photograph_reviewedby_global_mapping']['url']) ) {
                            $input1['reviewedBy']['url'] =    $custom_fields['saswp_photograph_reviewedby_global_mapping']['url'];
                        }

                        if ( ! empty( $custom_fields['saswp_photograph_reviewedby_global_mapping']['description']) ) {
                            $input1['reviewedBy']['description'] =    $custom_fields['saswp_photograph_reviewedby_global_mapping']['description'];
                        }

                        if ( ! empty( $custom_fields['saswp_photograph_reviewedby_global_mapping']['custom_fields']['honorificsuffix'][0]) ) {
                            $input1['reviewedBy']['honorificSuffix'] =    $custom_fields['saswp_photograph_reviewedby_global_mapping']['custom_fields']['honorificsuffix'][0];
                        }

                        if ( ! empty( $custom_fields['saswp_photograph_reviewedby_global_mapping']['custom_fields']['knowsabout'][0]) ) {
                            $input1['reviewedBy']['knowsAbout'] =   explode(',', $custom_fields['saswp_photograph_reviewedby_global_mapping']['custom_fields']['knowsabout'][0]);
                        }

                        $sameas = array();
                        if ( ! empty( $custom_fields['saswp_photograph_reviewedby_global_mapping']['custom_fields']['team_facebook'][0]) ) {
                            $sameas[] =   $custom_fields['saswp_photograph_reviewedby_global_mapping']['custom_fields']['team_facebook'][0];
                        }

                        if ( ! empty( $custom_fields['saswp_photograph_reviewedby_global_mapping']['custom_fields']['team_twitter'][0]) ) {
                            $sameas[] =   $custom_fields['saswp_photograph_reviewedby_global_mapping']['custom_fields']['team_twitter'][0];
                        }

                        if ( ! empty( $custom_fields['saswp_photograph_reviewedby_global_mapping']['custom_fields']['team_linkedin'][0]) ) {
                            $sameas[] =   $custom_fields['saswp_photograph_reviewedby_global_mapping']['custom_fields']['team_linkedin'][0];
                        }

                        if ( ! empty( $custom_fields['saswp_photograph_reviewedby_global_mapping']['custom_fields']['team_instagram'][0]) ) {
                            $sameas[] =   $custom_fields['saswp_photograph_reviewedby_global_mapping']['custom_fields']['team_instagram'][0];
                        }

                        if ( ! empty( $custom_fields['saswp_photograph_reviewedby_global_mapping']['custom_fields']['team_youtube'][0]) ) {
                            $sameas[] =   $custom_fields['saswp_photograph_reviewedby_global_mapping']['custom_fields']['team_youtube'][0];
                        }
                        if($sameas){
                            $input1['reviewedBy']['sameAs'] = $sameas;
                        }

                        if ( ! empty( $custom_fields['saswp_photograph_reviewedby_global_mapping']['custom_fields']['reviewer_image']) ) {
                            $input1['reviewedBy']['image']  = $custom_fields['saswp_photograph_reviewedby_global_mapping']['custom_fields']['reviewer_image'];
                        }

                        if ( ! empty( $custom_fields['saswp_photograph_reviewedby_global_mapping']['custom_fields']['alumniof'][0]) ) {
                            $str =  $custom_fields['saswp_photograph_reviewedby_global_mapping']['custom_fields']['alumniof'][0];
                            $itemlist = explode(",", $str);
                            foreach ( $itemlist as $key => $list){
                                $vnewarr['@type'] = 'Organization';
                                $vnewarr['Name']   = $list;   
                                $input1['reviewedBy']['alumniOf'][] = $vnewarr;
                            }
                        }

                    }else{
                        if ( isset( $custom_fields['saswp_photograph_reviewedby_type']) ) {
                            $input1['reviewedBy']['@type'] =    $custom_fields['saswp_photograph_reviewedby_type'];
                        }
                        if ( isset( $custom_fields['saswp_photograph_reviewedby_name']) ) {
                            $input1['reviewedBy']['name'] =    $custom_fields['saswp_photograph_reviewedby_name'];
                        }
                        if ( isset( $custom_fields['saswp_photograph_reviewedby_honorific_suffix']) ) {
                            $input1['reviewedBy']['honorificSuffix'] =    $custom_fields['saswp_photograph_reviewedby_honorific_suffix'];
                        }
                        if ( isset( $custom_fields['saswp_photograph_reviewedby_description']) ) {
                            $input1['reviewedBy']['description'] =    $custom_fields['saswp_photograph_reviewedby_description'];
                        }
                        if ( isset( $custom_fields['saswp_photograph_reviewedby_url']) ) {
                            $input1['reviewedBy']['url'] =    $custom_fields['saswp_photograph_reviewedby_url'];
                        }
                        if ( isset( $custom_fields['saswp_photograph_knowsabout']) ) {                            
                            $input1['knowsAbout'] = explode(',', $custom_fields['saswp_photograph_knowsabout']);    
                        }  
                        if ( ! empty( $custom_fields['saswp_photograph_about']) && isset($custom_fields['saswp_photograph_about']) ) {         
                            $input1['about']['@type'] = 'Event';                   
                            $input1['about']['name'] = explode(',', $custom_fields['saswp_photograph_about']);    
                        }  
                        if ( isset( $custom_fields['saswp_photograph_alumniof']) ) {
                            $str = $custom_fields['saswp_photograph_alumniof'];
                            $itemlist = explode(",", $str);
                            foreach ( $itemlist as $key => $list){
                                $vnewarr['@type'] = 'Organization';
                                $vnewarr['Name']   = $list;   
                                $input1['alumniOf'][] = $vnewarr;
                            }
                        }
                    }
                    
                    break; 

                    case 'SpecialAnnouncement':      
                        
                        $location = array();
                        
                        if ( isset( $custom_fields['saswp_special_announcement_location_type']) ) {

                            $location = array(
                                '@type' => $custom_fields['saswp_special_announcement_location_type'],
                                'name' => $custom_fields['saswp_special_announcement_location_name'],
                                'image' => $custom_fields['saswp_special_announcement_location_image'],
                                'url' => saswp_validate_url($custom_fields['saswp_special_announcement_location_url']),
                                'telephone' => $custom_fields['saswp_special_announcement_location_telephone'],
                                'priceRange' => $custom_fields['saswp_special_announcement_location_price_range'],
                                'address' => array(
                                            '@type' => 'PostalAddress',
                                            'streetAddress' => $custom_fields['saswp_special_announcement_location_street_address'],
                                            'addressLocality' => $custom_fields['saswp_special_announcement_location_address_locality'],
                                            'addressRegion' => $custom_fields['saswp_special_announcement_location_address_region'],  
                                ),
                            );

                            $input1['announcementLocation'] = $location;
                        }
                        if ( isset( $custom_fields['saswp_special_announcement_id']) ) {
                            $input1['@id'] =    get_permalink().$custom_fields['saswp_special_announcement_id'];
                        }
                        if ( isset( $custom_fields['saswp_special_announcement_category']) ) {
                            $input1['category'] =    $custom_fields['saswp_special_announcement_category'];
                        }
                        if ( isset( $custom_fields['saswp_special_announcement_quarantine_guidelines']) ) {
                         $input1['quarantineGuidelines'] =    $custom_fields['saswp_special_announcement_quarantine_guidelines'];
                        }
                        if ( isset( $custom_fields['saswp_special_announcement_newsupdates_and_guidelines']) ) {
                         $input1['newsUpdatesAndGuidelines'] =    $custom_fields['saswp_special_announcement_newsupdates_and_guidelines'];
                        }
                        if ( isset( $custom_fields['saswp_special_announcement_disease_prevention_info']) ) {
                            $input1['diseasePreventionInfo'] =    $custom_fields['saswp_special_announcement_disease_prevention_info'];
                        }
                        if ( isset( $custom_fields['saswp_special_announcement_image']) ) {
                         $input1['image'] =    $custom_fields['saswp_special_announcement_image'];
                        }
                        if ( isset( $custom_fields['saswp_special_announcement_url']) ) {
                         $input1['url'] =    saswp_validate_url($custom_fields['saswp_special_announcement_url']);
                        }                        
                        if ( isset( $custom_fields['saswp_special_announcement_keywords']) ) {
                         $input1['keywords'] =    $custom_fields['saswp_special_announcement_keywords'];
                        }                        
                        if ( isset( $custom_fields['saswp_special_announcement_name']) ) {
                         $input1['name'] =    $custom_fields['saswp_special_announcement_name'];
                        }                    
                        if ( isset( $custom_fields['saswp_special_announcement_description']) ) {
                         $input1['text'] =    wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_special_announcement_description'] ));
                        }
                        if ( isset( $custom_fields['saswp_special_announcement_date_published']) ) {
                         $input1['datePublished'] =    $custom_fields['saswp_special_announcement_date_published'];
                        }
                        if ( isset( $custom_fields['saswp_special_announcement_date_modified']) ) {
                         $input1['dateModified'] =    $custom_fields['saswp_special_announcement_date_modified'];
                        }
                        if ( isset( $custom_fields['saswp_special_announcement_date_posted']) ) {
                          $input1['datePosted'] =    $custom_fields['saswp_special_announcement_date_posted'];
                        }
                        if ( isset( $custom_fields['saswp_special_announcement_date_expires']) ) {
                         $input1['expires'] =    $custom_fields['saswp_special_announcement_date_expires'];
                        }                    
                        if ( isset( $custom_fields['saswp_special_announcement_author_type']) ) {
                            $input1['author']['@type'] =    $custom_fields['saswp_special_announcement_author_type'];
                        }
                        if ( isset( $custom_fields['saswp_special_announcement_author_name']) ) {
                         $input1['author']['name'] =    $custom_fields['saswp_special_announcement_author_name'];
                        }
                        if ( isset( $custom_fields['saswp_special_announcement_author_description']) ) {
                         $input1['author']['description'] =    $custom_fields['saswp_special_announcement_author_description'];
                        }
                        if ( isset( $custom_fields['saswp_special_announcement_author_url']) ) {
                         $input1['author']['url'] =    $custom_fields['saswp_special_announcement_author_url'];
                        }
                        if ( isset( $custom_fields['saswp_special_announcement_organization_logo']) && isset($custom_fields['saswp_special_announcement_organization_name']) ) {
                         $input1['publisher']['@type']       =    'Organization';
                         $input1['publisher']['name']        =    $custom_fields['saswp_special_announcement_organization_name'];
                         $input1['publisher']['logo']        =    $custom_fields['saswp_special_announcement_organization_logo'];
                        }                    
                        break;     
                    
                case 'HowTo':                          
                    if ( isset( $custom_fields['saswp_howto_schema_id']) ) {
                     $input1['@id'] =    get_permalink().$custom_fields['saswp_howto_schema_id'];
                    }                    
                    if ( isset( $custom_fields['saswp_howto_schema_name']) ) {
                     $input1['name'] =    $custom_fields['saswp_howto_schema_name'];
                    }
                    if ( isset( $custom_fields['saswp_howto_schema_description']) ) {
                     $input1['description'] =    wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_howto_schema_description'] ));
                    }
                    if ( isset( $custom_fields['saswp_howto_ec_schema_currency']) && isset($custom_fields['saswp_howto_ec_schema_value']) ) {
                     $input1['estimatedCost']['@type']    =    'MonetaryAmount';
                     $input1['estimatedCost']['currency'] =    $custom_fields['saswp_howto_ec_schema_currency'];
                     $input1['estimatedCost']['value']    =    $custom_fields['saswp_howto_ec_schema_value'];
                    }
                    
                    if ( isset( $custom_fields['saswp_howto_schema_totaltime']) ) {
                     $input1['totalTime']     =    $custom_fields['saswp_howto_schema_totaltime'];
                    }
                    if ( isset( $custom_fields['saswp_howto_ec_schema_date_published']) ) {
                     $input1['datePublished'] =    $custom_fields['saswp_howto_ec_schema_date_published'];
                    }
                    if ( isset( $custom_fields['saswp_howto_ec_schema_date_modified']) ) {
                     $input1['dateModified'] =    $custom_fields['saswp_howto_ec_schema_date_modified'];
                    }
                    if ( isset( $custom_fields['saswp_howto_schema_image']) ) {
                     $input1['image'] =    $custom_fields['saswp_howto_schema_image'];
                    }

                    if ( isset( $custom_fields['saswp_howto_schema_supplies']) ) {                                                                                                                    
                        $input1['supply'] =    $custom_fields['saswp_howto_schema_supplies'];
                    }
                    if ( isset( $custom_fields['saswp_howto_schema_tools']) ) {                                                                                                                    
                        $input1['tool'] =    $custom_fields['saswp_howto_schema_tools'];
                    }
                    if ( isset( $custom_fields['saswp_howto_schema_steps']) ) {                                                                                                                    
                        $input1['step'] =    $custom_fields['saswp_howto_schema_steps'];
                    }

                    if ( ! empty( $custom_fields['saswp_howto_about']) && isset($custom_fields['saswp_howto_about']) ) {         
                        $explode_about = explode(',', $custom_fields['saswp_howto_about']);
                        if ( ! empty( $explode_about) ) {
                            $about_arr = array();
                            foreach( $explode_about as $val){
                                $about_arr[] = array(
                                            '@type' => 'Thing',
                                            'name'  => $val
                                );
                            }
                            $input1['about'] = $about_arr;
                        }                            
                    }  
                                                            
                    break;     
                                  
                case 'local_business':
                   
                    $business_name    = get_post_meta($schema_post_id, 'saswp_business_name', true);

                    if ( isset( $custom_fields['local_business_id']) ) {
                        $input1['@id'] =    get_permalink().$custom_fields['local_business_id'];
                    }                   
                    if ( isset( $custom_fields['saswp_business_type']) ) {                     
                     $input1['@type'] =    $custom_fields['saswp_business_type'];                     
                    }
                    if ( isset( $custom_fields['saswp_business_name']) ) {
                     $input1['@type'] =    $custom_fields['saswp_business_name'];
                    }
                    if ( isset( $custom_fields['local_business_name']) ) {
                     $input1['name'] =    $custom_fields['local_business_name'];
                    }                    
                    if ( isset( $custom_fields['local_business_name_url']) ) {
                     $input1['url'] =    saswp_validate_url($custom_fields['local_business_name_url']);
                    }
                    if ( isset( $custom_fields['local_business_logo']) ) {
                     $input1['image'] =    $custom_fields['local_business_logo'];
                    }
                    if ( isset( $custom_fields['local_business_description']) ) {
                     $input1['description'] =    wp_strip_all_tags(strip_shortcodes( $custom_fields['local_business_description'] ));
                    }
                    if ( isset( $custom_fields['local_street_address']) ) {
                     $input1['address']['streetAddress'] =    $custom_fields['local_street_address'];
                    }                    
                    if ( isset( $custom_fields['local_city']) ) {
                     $input1['address']['addressLocality'] =    $custom_fields['local_city'];
                    }
                    if ( isset( $custom_fields['local_state']) ) {
                     $input1['address']['addressRegion'] =    $custom_fields['local_state'];
                    }
                    if ( isset( $custom_fields['local_country']) ) {
                     $input1['address']['addressCountry'] =    $custom_fields['local_country'];
                    }
                    if ( isset( $custom_fields['local_postal_code']) ) {
                     $input1['address']['postalCode'] =    $custom_fields['local_postal_code'];
                    }                    
                    if ( isset( $custom_fields['local_latitude']) && isset($custom_fields['local_longitude']) ) {
                        
                     $input1['geo']['@type']     =    'GeoCoordinates';   
                     $input1['geo']['latitude']  =    $custom_fields['local_latitude'];
                     $input1['geo']['longitude'] =    $custom_fields['local_longitude'];
                     
                    }      
                    if ( isset( $custom_fields['local_service_offered_name']) ) {                    
                        $input1['makesOffer']['@type'] = 'Offer';
                        $input1['makesOffer']['@id']   = '#service';
                        $input1['makesOffer']['itemOffered']['@type'] = 'Service';
                        $input1['makesOffer']['itemOffered']['name'] = $custom_fields['local_service_offered_name'];                     
                        if ( isset( $custom_fields['local_service_offered_url']) ) {                                             
                            $input1['makesOffer']['itemOffered']['url']  = $custom_fields['local_service_offered_url']; 
                        }
                        if ( isset( $custom_fields['local_area_served']) ) {
                            $input1['makesOffer']['itemOffered']['areaServed'] = saswp_explode_comma_seprated( $custom_fields['local_area_served'], 'Place' );
                        }
                        
                    }
                    if ( ! empty( $custom_fields['local_makes_offer'] ) && is_array( $custom_fields['local_makes_offer'] ) ) {
                        foreach ( $custom_fields['local_makes_offer'] as $lmo_key => $local_offer) {
                            if ( ! empty( $local_offer ) && is_array( $local_offer ) ) {
                                $make_offer                   =   array();
                                $make_offer['@type']          =   'Offer';
                                $make_offer['@id']            =   '#service'. ( $lmo_key + 1 );
                                $make_offer['itemOffered']    =   $local_offer;

                                $input1['makesOffer'][]       =   $make_offer;    
                            }
                        }   
                    }

                    if ( isset( $custom_fields['local_business_founder']) ) {
                        $input1['founder'] =    saswp_explode_comma_seprated($custom_fields['local_business_founder'], 'Person');
                    }
                    if ( isset( $custom_fields['local_business_employee']) ) {
                        $input1['employee'] =    saswp_explode_comma_seprated($custom_fields['local_business_employee'], 'Person');
                    }                    
                    if ( isset( $custom_fields['local_phone']) ) {
                     $input1['telephone'] =    $custom_fields['local_phone'];
                    }
                    if ( isset( $custom_fields['local_website']) ) {
                     $input1['website'] =    $custom_fields['local_website'];
                    }                    
                    if ( isset( $custom_fields['saswp_dayofweek']) ) {
                     $input1['openingHours'] =    $custom_fields['saswp_dayofweek'];
                    }                    
                    if ( isset( $custom_fields['local_area_served']) ) {                         
                     $input1['areaServed'] =    saswp_explode_comma_seprated($custom_fields['local_area_served'], 'Place');                     
                    }                    
                    if ( isset( $custom_fields['local_price_range']) ) {
                     $input1['priceRange'] =    $custom_fields['local_price_range'];
                    }
                    if ( isset( $custom_fields['local_hasmap']) ) {
                     $input1['hasMap'] =    $custom_fields['local_hasmap'];
                    }
                    if ( isset( $custom_fields['local_serves_cuisine']) ) {
                     $input1['servesCuisine'] =    $custom_fields['local_serves_cuisine'];
                    }                    
                    if ( isset( $custom_fields['local_menu']) ) {
                     $input1['hasMenu'] =    $custom_fields['local_menu'];
                    }
                    if ( isset( $custom_fields['local_additional_type']) ) {
                        $input1['additionalType'] =    $custom_fields['local_additional_type'];
                    }
                    if ( isset( $custom_fields['local_rating_value']) && isset($custom_fields['local_rating_count']) ) {
                       $input1['aggregateRating']['@type']       =   'AggregateRating';
                       $input1['aggregateRating']['worstRating'] =   0;
                       $input1['aggregateRating']['bestRating']  =   5;
                       $input1['aggregateRating']['ratingValue'] =    $custom_fields['local_rating_value'];
                       $input1['aggregateRating']['ratingCount'] =    $custom_fields['local_rating_count'];
                    }
                    if ( isset( $input1['address']) && is_array($input1['address']) ) {
                        if(count($input1['address']) > 0 && !isset($input1['address']['@type']) ) {
                            $input1['address']['@type'] = 'PostalAddress';
                            $new_address_array = array_merge(array_splice($input1['address'], -1), $input1['address']);
                            $input1['address'] = $new_address_array;
                        }
                    }
                    $sameas = array();
                    if ( isset( $custom_fields['local_facebook']) ) {
                        $sameas[] =    $custom_fields['local_facebook'];
                    }
                    if ( isset( $custom_fields['local_twitter']) ) {
                        $sameas[] =    $custom_fields['local_twitter'];
                    }
                    if ( isset( $custom_fields['local_instagram']) ) {
                        $sameas[] =    $custom_fields['local_instagram'];
                    }
                    if ( isset( $custom_fields['local_pinterest']) ) {
                        $sameas[] =    $custom_fields['local_pinterest'];
                    }
                    if ( isset( $custom_fields['local_linkedin']) ) {
                        $sameas[] =    $custom_fields['local_linkedin'];
                    }
                    if ( isset( $custom_fields['local_soundcloud']) ) {
                        $sameas[] =    $custom_fields['local_soundcloud'];
                    }
                    if ( isset( $custom_fields['local_tumblr']) ) {
                        $sameas[] =    $custom_fields['local_tumblr'];
                    }
                    if ( isset( $custom_fields['local_youtube']) ) {
                        $sameas[] =    $custom_fields['local_youtube'];
                    }
                    if ( isset( $custom_fields['local_threads']) ) {
                        $sameas[] =    $custom_fields['local_threads'];
                    }
                    if ( isset( $custom_fields['local_mastodon']) ) {
                        $sameas[] =    $custom_fields['local_mastodon'];
                    }
                    if ( isset( $custom_fields['local_vibehut']) ) {
                        $sameas[] =    $custom_fields['local_vibehut'];
                    }
                    if($sameas){
                        $input1['sameAs'] = $sameas;
                    }               
                    if( $business_name == 'hotel' ) {
                        if( ! empty( $custom_fields['local_checkin_time'] ) ) {
                            $input1['checkinTime'] = $custom_fields['local_checkin_time'];
                        }
                        if( ! empty( $custom_fields['local_checkout_time'] ) ) {
                            $input1['checkoutTime'] = $custom_fields['local_checkout_time'];
                        }
                        if( ! empty( $custom_fields['local_identifier_pvalue'] ) ) {
                            $input1['identifier']               = $custom_fields['local_identifier_pvalue'];
                        }
                    }               
                    break;
                
                case 'Blogposting':
                case 'BlogPosting':
                    if ( isset( $custom_fields['saswp_blogposting_id']) ) {
                        $input1['@id'] =    get_permalink().$custom_fields['saswp_blogposting_id'];
                    }                
                    if ( isset( $custom_fields['saswp_blogposting_main_entity_of_page']) ) {
                     $input1['mainEntityOfPage'] =    $custom_fields['saswp_blogposting_main_entity_of_page'];
                    }
                    if ( isset( $custom_fields['saswp_blogposting_inlanguage']) ) {
                        $input1['inLanguage'] =    $custom_fields['saswp_blogposting_inlanguage'];
                    }
                    if ( isset( $custom_fields['saswp_blogposting_headline']) ) {
                     $input1['headline'] =    $custom_fields['saswp_blogposting_headline'];
                    }
                    if ( isset( $custom_fields['saswp_blogposting_description']) ) {
                     $input1['description'] =    wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_blogposting_description'] ));
                    }
                    if ( isset( $custom_fields['saswp_blogposting_body']) ) {
                        if($custom_fields['saswp_blogposting_body']){
                            $input1['articleBody'] =    $custom_fields['saswp_blogposting_body'];
                        }else{
                            unset($input1['articleBody']);
                        }
                    }                                           
                    if ( isset( $custom_fields['saswp_blogposting_name']) ) {
                     $input1['name'] =    $custom_fields['saswp_blogposting_name'];
                    }
                    if ( isset( $custom_fields['saswp_blogposting_url']) ) {
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_blogposting_url']);
                    }
                    if ( isset( $custom_fields['saswp_blogposting_image']) && $custom_fields['saswp_blogposting_image']){
                     $input1['image'] =    $custom_fields['saswp_blogposting_image'];
                    }
                    if ( isset( $custom_fields['saswp_blogposting_date_published']) ) {
                     $input1['datePublished'] =    $custom_fields['saswp_blogposting_date_published'];
                    }
                    if ( isset( $custom_fields['saswp_blogposting_date_modified']) ) {
                     $input1['dateModified'] =    $custom_fields['saswp_blogposting_date_modified'];
                    }

                    if ( isset( $custom_fields['saswp_blogposting_editor_name']) && $custom_fields['saswp_blogposting_editor_name'] != '') {
                        $input1['editor'] = array();
                        $input1['editor']['@type'] = 'Person';
                        $input1['editor']['name']  =  $custom_fields['saswp_blogposting_editor_name'];
                    }
                    if ( isset( $custom_fields['saswp_blogposting_editor_type']) ) {
                        $input1['editor']['@type'] =    $custom_fields['saswp_blogposting_editor_type'];
                    }
                    if ( isset( $custom_fields['saswp_blogposting_editor_honorific_suffix']) && $custom_fields['saswp_blogposting_editor_honorific_suffix'] != '') {
                        $input1['editor']['honorificSuffix']  =  $custom_fields['saswp_blogposting_editor_honorific_suffix'];
                    }
                    if ( isset( $custom_fields['saswp_blogposting_editor_url']) ) {
                        $input1['editor']['url'] =    $custom_fields['saswp_blogposting_author_url'];
                    }
                    if ( isset( $custom_fields['saswp_blogposting_author_social_profile']) && !empty($custom_fields['saswp_blogposting_author_social_profile']) ) {
                        $explode_sp = explode(',', $custom_fields['saswp_blogposting_author_social_profile']);
                        if ( is_array( $explode_sp) ) {
                            $input1['editor']['sameAs'] =    $explode_sp;
                        }
                    }
                    if ( isset( $custom_fields['saswp_blogposting_editor_description']) ) {
                        $input1['editor']['description'] =    $custom_fields['saswp_blogposting_editor_description'];
                    }
                    if ( isset( $custom_fields['saswp_blogposting_editor_image']) ) {
                        $input1['editor']['Image']['url'] =    $custom_fields['saswp_blogposting_editor_image'];  
                    } 

                    if ( ! empty( $custom_fields['saswp_blogposting_author_global_mapping']) ) {
                       
                        if ( ! empty( $custom_fields['saswp_blogposting_author_global_mapping']) ) {
                            $input1['author']['@type'] =   "Person";
                        }

                        if ( ! empty( $custom_fields['saswp_blogposting_author_global_mapping']['name']) ) {
                            $input1['author']['name'] =    $custom_fields['saswp_blogposting_author_global_mapping']['name'];
                        }

                        if ( ! empty( $custom_fields['saswp_blogposting_author_global_mapping']['url']) ) {
                            $input1['author']['url'] =    $custom_fields['saswp_blogposting_author_global_mapping']['url'];
                        }

                        if ( ! empty( $custom_fields['saswp_blogposting_author_global_mapping']['description']) ) {
                            $input1['author']['description'] =    $custom_fields['saswp_blogposting_author_global_mapping']['description'];
                        }

                        if ( ! empty( $custom_fields['saswp_blogposting_author_global_mapping']['custom_fields']['honorificsuffix'][0]) ) {
                            $input1['author']['honorificsuffix'] =    $custom_fields['saswp_blogposting_author_global_mapping']['custom_fields']['honorificsuffix'][0];
                        }

                        if ( ! empty( $custom_fields['saswp_blogposting_author_global_mapping']['custom_fields']['knowsabout'][0]) ) {
                            $input1['author']['knowsabout'] =   explode(',', $custom_fields['saswp_blogposting_author_global_mapping']['custom_fields']['knowsabout'][0]);
                        }

                        $sameas = array();
                        if ( ! empty( $custom_fields['saswp_blogposting_author_global_mapping']['custom_fields']['team_facebook'][0]) ) {
                            $sameas[] =  $custom_fields['saswp_blogposting_author_global_mapping']['custom_fields']['team_facebook'][0];
                        }

                        if ( ! empty( $custom_fields['saswp_blogposting_author_global_mapping']['custom_fields']['team_twitter'][0]) ) {
                            $sameas[] =  $custom_fields['saswp_blogposting_author_global_mapping']['custom_fields']['team_twitter'][0];
                        }

                        if ( ! empty( $custom_fields['saswp_blogposting_author_global_mapping']['custom_fields']['team_linkedin'][0]) ) {
                            $sameas[] = $custom_fields['saswp_blogposting_author_global_mapping']['custom_fields']['team_linkedin'][0];
                        }

                        if ( ! empty( $custom_fields['saswp_blogposting_author_global_mapping']['custom_fields']['team_instagram'][0]) ) {
                            $sameas[] = $custom_fields['saswp_blogposting_author_global_mapping']['custom_fields']['team_instagram'][0];
                        }

                        if ( ! empty( $custom_fields['saswp_blogposting_author_global_mapping']['custom_fields']['team_youtube'][0]) ) {
                            $sameas[] = $custom_fields['saswp_blogposting_author_global_mapping']['custom_fields']['team_youtube'][0];
                        }
                        if($sameas){
                            $input1['author']['sameAs'] = $sameas;
                        }

                        if ( ! empty( $custom_fields['saswp_blogposting_author_global_mapping']['custom_fields']['alumniof'][0]) ) {
                            $str =  $custom_fields['saswp_blogposting_author_global_mapping']['custom_fields']['alumniof'][0];
                            $itemlist = explode(",", $str);
                            foreach ( $itemlist as $key => $list){
                                $vnewarr['@type'] = 'Organization';
                                $vnewarr['Name']   = $list;   
                                $input1['author']['alumniOf'][] = $vnewarr;
                            }
                        }
                    
                    }else{

                        if ( isset( $custom_fields['saswp_blogposting_author_type']) ) {
                        $input1['author']['@type'] =    $custom_fields['saswp_blogposting_author_type'];
                        }
                        if ( isset( $custom_fields['saswp_blogposting_author_name']) ) {
                        $input1['author']['name'] =    $custom_fields['saswp_blogposting_author_name'];
                        }
                        if ( isset( $custom_fields['saswp_blogposting_author_honorific_suffix']) ) {
                            $input1['author']['honorificSuffix'] =    $custom_fields['saswp_blogposting_author_honorific_suffix'];
                        }
                        if ( isset( $custom_fields['saswp_blogposting_author_url']) ) {
                        $input1['author']['url'] =    $custom_fields['saswp_blogposting_author_url'];
                        }
                        if ( isset( $custom_fields['saswp_blogposting_author_social_profile']) && !empty($custom_fields['saswp_blogposting_author_social_profile']) ) {
                            $explode_sp = explode(',', $custom_fields['saswp_blogposting_author_social_profile']);
                            if ( is_array( $explode_sp) ) {
                                $input1['author']['sameAs'] =    $explode_sp;
                            }
                        }
                        if ( isset( $custom_fields['saswp_blogposting_author_description']) ) {
                        $input1['author']['description'] =    $custom_fields['saswp_blogposting_author_description'];
                        }
                        if ( isset( $custom_fields['saswp_blogposting_author_jobtitle']) ) {
                            $input1['author']['JobTitle'] =    $custom_fields['saswp_blogposting_author_jobtitle'];
                        }
                        if ( isset( $custom_fields['saswp_blogposting_author_image']) ) {
                            $input1['author']['Image']['url'] =    $custom_fields['saswp_blogposting_author_image'];  
                        } 
                    }

                    if ( ! empty( $custom_fields['saswp_blogposting_reviewedby_global_mapping']) ) {
                           
                        if ( ! empty( $custom_fields['saswp_blogposting_reviewedby_global_mapping']) ) {
                            $input1['reviewedBy']['@type'] =   "Person";
                        }

                        if ( ! empty( $custom_fields['saswp_blogposting_reviewedby_global_mapping']['name']) ) {
                            $input1['reviewedBy']['name'] =    $custom_fields['saswp_blogposting_reviewedby_global_mapping']['name'];
                        }

                        if ( ! empty( $custom_fields['saswp_blogposting_reviewedby_global_mapping']['url']) ) {
                            $input1['reviewedBy']['url'] =    $custom_fields['saswp_blogposting_reviewedby_global_mapping']['url'];
                        }

                        if ( ! empty( $custom_fields['saswp_blogposting_reviewedby_global_mapping']['description']) ) {
                            $input1['reviewedBy']['description'] =    $custom_fields['saswp_blogposting_reviewedby_global_mapping']['description'];
                        }
                       
                        if ( ! empty( $custom_fields['saswp_blogposting_reviewedby_global_mapping']['custom_fields']['honorificsuffix'][0]) ) {
                            $input1['reviewedBy']['honorificSuffix'] =    $custom_fields['saswp_blogposting_reviewedby_global_mapping']['custom_fields']['honorificsuffix'][0];
                        }

                        if ( ! empty( $custom_fields['saswp_blogposting_reviewedby_global_mapping']['custom_fields']['knowsabout'][0]) ) {
                            $input1['reviewedBy']['knowsAbout'] =   explode(',', $custom_fields['saswp_blogposting_reviewedby_global_mapping']['custom_fields']['knowsabout'][0]);
                        }

                        $sameas = array();
                        if ( ! empty( $custom_fields['saswp_blogposting_reviewedby_global_mapping']['custom_fields']['team_facebook'][0]) ) {
                            $sameas[] =   $custom_fields['saswp_blogposting_reviewedby_global_mapping']['custom_fields']['team_facebook'][0];
                        }

                        if ( ! empty( $custom_fields['saswp_blogposting_reviewedby_global_mapping']['custom_fields']['team_twitter'][0]) ) {
                            $sameas[] =   $custom_fields['saswp_blogposting_reviewedby_global_mapping']['custom_fields']['team_twitter'][0];
                        }

                        if ( ! empty( $custom_fields['saswp_blogposting_reviewedby_global_mapping']['custom_fields']['team_linkedin'][0]) ) {
                            $sameas[] =   $custom_fields['saswp_blogposting_reviewedby_global_mapping']['custom_fields']['team_linkedin'][0];
                        }

                        if ( ! empty( $custom_fields['saswp_blogposting_reviewedby_global_mapping']['custom_fields']['team_instagram'][0]) ) {
                            $sameas[] =   $custom_fields['saswp_blogposting_reviewedby_global_mapping']['custom_fields']['team_instagram'][0];
                        }

                        if ( ! empty( $custom_fields['saswp_blogposting_reviewedby_global_mapping']['custom_fields']['team_youtube'][0]) ) {
                            $sameas[] =   $custom_fields['saswp_blogposting_reviewedby_global_mapping']['custom_fields']['team_youtube'][0];
                        }
                        if($sameas){
                            $input1['reviewedBy']['sameAs'] = $sameas;
                        }

                        if ( ! empty( $custom_fields['saswp_blogposting_reviewedby_global_mapping']['custom_fields']['reviewer_image']) ) {
                            $input1['reviewedBy']['image']  = $custom_fields['saswp_blogposting_reviewedby_global_mapping']['custom_fields']['reviewer_image'];
                        }

                        if ( ! empty( $custom_fields['saswp_blogposting_reviewedby_global_mapping']['custom_fields']['alumniof'][0]) ) {
                            $str =  $custom_fields['saswp_blogposting_reviewedby_global_mapping']['custom_fields']['alumniof'][0];
                            $itemlist = explode(",", $str);
                            foreach ( $itemlist as $key => $list){
                                $vnewarr['@type'] = 'Organization';
                                $vnewarr['Name']   = $list;   
                                $input1['reviewedBy']['alumniOf'][] = $vnewarr;
                            }
                        }

                    }else{
                        if ( isset( $custom_fields['saswp_blogposting_reviewedby_type']) ) {
                            $input1['reviewedBy']['@type'] =    $custom_fields['saswp_blogposting_reviewedby_type'];
                        }
                        if ( isset( $custom_fields['saswp_blogposting_reviewedby_name']) ) {
                            $input1['reviewedBy']['name'] =    $custom_fields['saswp_blogposting_reviewedby_name'];
                        }
                        if ( isset( $custom_fields['saswp_blogposting_reviewedby_honorific_suffix']) ) {
                            $input1['reviewedBy']['honorificSuffix'] =    $custom_fields['saswp_blogposting_reviewedby_honorific_suffix'];
                        }
                        if ( isset( $custom_fields['saswp_blogposting_reviewedby_url']) ) {
                            $input1['reviewedBy']['url'] =    $custom_fields['saswp_blogposting_reviewedby_url'];
                        }
                        if ( isset( $custom_fields['saswp_blogposting_reviewedby_description']) ) {
                            $input1['reviewedBy']['description'] =    $custom_fields['saswp_blogposting_reviewedby_description'];
                        }
                        if ( isset( $custom_fields['saswp_blogposting_knowsabout']) ) {                            
                            $input1['knowsAbout'] = explode(',', $custom_fields['saswp_blogposting_knowsabout']);    
                        }
                        if ( isset( $custom_fields['saswp_blogposting_alumniof']) ) {
                            $str = $custom_fields['saswp_blogposting_alumniof'];
                            $itemlist = explode(",", $str);
                            foreach ( $itemlist as $key => $list){
                                $vnewarr['@type'] = 'Organization';
                                $vnewarr['Name']   = $list;   
                                $input1['alumniOf'][] = $vnewarr;
                            }
                        }    
                    }

                    if ( ! empty( $custom_fields['saswp_blogposting_about']) && isset($custom_fields['saswp_blogposting_about']) ) {         
                        $input1['about']['@type'] = 'Event';                   
                        $input1['about']['name'] = explode(',', $custom_fields['saswp_blogposting_about']);    
                    }  
                                  
                    if ( isset( $custom_fields['saswp_blogposting_organization_logo']) && isset($custom_fields['saswp_blogposting_organization_name']) ) {
                     $input1['publisher']['@type']       =    'Organization';
                     $input1['publisher']['name']        =    $custom_fields['saswp_blogposting_organization_name'];
                     $input1['publisher']['logo']        =    $custom_fields['saswp_blogposting_organization_logo'];
                    }
                                        
                    break;
                    
                case 'AudioObject':
                    
                    if ( isset( $custom_fields['saswp_audio_schema_id']) ) {
                        $input1['@id'] =    get_permalink().$custom_fields['saswp_audio_schema_id'];
                    }
                    if ( isset( $custom_fields['saswp_audio_schema_name']) ) {
                     $input1['name'] =    $custom_fields['saswp_audio_schema_name'];
                    }
                    if ( isset( $custom_fields['saswp_audio_schema_description']) ) {
                     $input1['description'] =    wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_audio_schema_description'] ));
                    }
                    if ( isset( $custom_fields['saswp_audio_schema_contenturl']) ) {
                     $input1['contentUrl'] =    saswp_validate_url($custom_fields['saswp_audio_schema_contenturl']);
                    }
                    if ( isset( $custom_fields['saswp_audio_schema_duration']) ) {
                     $input1['duration'] =    $custom_fields['saswp_audio_schema_duration'];
                    }
                    if ( isset( $custom_fields['saswp_audio_schema_encoding_format']) ) {
                     $input1['encodingFormat'] =    $custom_fields['saswp_audio_schema_encoding_format'];
                    }
                    
                    if ( isset( $custom_fields['saswp_audio_schema_date_published']) ) {
                     $input1['datePublished'] =    $custom_fields['saswp_audio_schema_date_published'];
                    }
                    if ( isset( $custom_fields['saswp_audio_schema_date_modified']) ) {
                     $input1['dateModified'] =    $custom_fields['saswp_audio_schema_date_modified'];
                    }
                    if ( isset( $custom_fields['saswp_audio_schema_author_type']) ) {
                        $input1['author']['@type'] =    $custom_fields['saswp_audio_schema_author_type'];
                    }
                    if ( isset( $custom_fields['saswp_audio_schema_author_name']) ) {
                     $input1['author']['name'] =    $custom_fields['saswp_audio_schema_author_name'];
                    }
                    if ( isset( $custom_fields['saswp_audio_schema_author_description']) ) {
                     $input1['author']['description'] =    $custom_fields['saswp_audio_schema_author_description'];
                    }
                    if ( isset( $custom_fields['saswp_audio_schema_author_url']) ) {
                     $input1['author']['url'] =    saswp_validate_url($custom_fields['saswp_audio_schema_author_url']);
                    }
                    
                    break;   
                    
                case 'SoftwareApplication':
                    
                    if ( isset( $custom_fields['saswp_software_schema_id']) ) {
                        $input1['@id'] =    get_permalink().$custom_fields['saswp_software_schema_id'];
                    }
                    if ( isset( $custom_fields['saswp_software_schema_name']) ) {
                     $input1['name'] =    $custom_fields['saswp_software_schema_name'];
                    }
                    if ( isset( $custom_fields['saswp_software_schema_description']) ) {
                     $input1['description'] =    wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_software_schema_description'] ));
                    }
                    if ( isset( $custom_fields['saswp_software_schema_image']) ) {
                     $input1['image'] =    $custom_fields['saswp_software_schema_image'];
                    }
                    if ( isset( $custom_fields['saswp_software_schema_operating_system']) ) {
                     $input1['operatingSystem'] =    $custom_fields['saswp_software_schema_operating_system'];
                    }
                    if ( isset( $custom_fields['saswp_software_schema_application_category']) ) {
                     $input1['applicationCategory'] =    $custom_fields['saswp_software_schema_application_category'];
                    }
                    if ( isset( $custom_fields['saswp_software_schema_price']) ) {
                     $input1['offers']['price'] =    $custom_fields['saswp_software_schema_price'];
                    }
                    if ( isset( $custom_fields['saswp_software_schema_price_currency']) ) {
                     $input1['offers']['priceCurrency'] =    $custom_fields['saswp_software_schema_price_currency'];
                    }                    
                    if ( isset( $custom_fields['saswp_software_schema_date_published']) ) {
                     $input1['datePublished'] =    $custom_fields['saswp_software_schema_date_published'];
                    }
                    if ( isset( $custom_fields['saswp_software_schema_date_modified']) ) {
                     $input1['dateModified'] =    $custom_fields['saswp_software_schema_date_modified'];
                    }
                    if ( isset( $custom_fields['saswp_software_schema_rating']) && isset($custom_fields['saswp_software_schema_rating_count']) ) {
                        $input1['aggregateRating']['@type']       =   'AggregateRating';                           
                        $input1['aggregateRating']['ratingValue'] =    $custom_fields['saswp_software_schema_rating'];
                        $input1['aggregateRating']['ratingCount'] =    $custom_fields['saswp_software_schema_rating_count'];
                     }
                                                                                
                    break;    
                    
                    case 'MobileApplication':
                        
                        if ( isset( $custom_fields['saswp_mobile_app_id']) ) {
                            $input1['@id'] =    get_permalink().$custom_fields['saswp_mobile_app_id'];
                           }
                        if ( isset( $custom_fields['saswp_mobile_app_schema_name']) ) {
                         $input1['name'] =    $custom_fields['saswp_mobile_app_schema_name'];
                        }
                        if ( isset( $custom_fields['saswp_mobile_app_schema_description']) ) {
                         $input1['description'] =    wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_mobile_app_schema_description'] ));
                        }
                        if ( isset( $custom_fields['saswp_mobile_app_schema_image']) ) {
                         $input1['image'] =    $custom_fields['saswp_mobile_app_schema_image'];
                        }
                        if ( isset( $custom_fields['saswp_mobile_app_schema_operating_system']) ) {
                         $input1['operatingSystem'] =    $custom_fields['saswp_mobile_app_schema_operating_system'];
                        }
                        if ( isset( $custom_fields['saswp_mobile_app_schema_application_category']) ) {
                         $input1['applicationCategory'] =    $custom_fields['saswp_mobile_app_schema_application_category'];
                        }
                        if ( isset( $custom_fields['saswp_mobile_app_schema_price']) ) {
                         $input1['offers']['price'] =    $custom_fields['saswp_mobile_app_schema_price'];
                        }
                        if ( isset( $custom_fields['saswp_mobile_app_schema_price_currency']) ) {
                         $input1['offers']['priceCurrency'] =    $custom_fields['saswp_mobile_app_schema_price_currency'];
                        }                    
                        if ( isset( $custom_fields['saswp_mobile_app_schema_date_published']) ) {
                         $input1['datePublished'] =    $custom_fields['saswp_mobile_app_schema_date_published'];
                        }
                        if ( isset( $custom_fields['saswp_mobile_app_schema_date_modified']) ) {
                         $input1['dateModified'] =    $custom_fields['saswp_mobile_app_schema_date_modified'];
                        }
                        if ( isset( $custom_fields['saswp_mobile_app_schema_rating_value']) && isset($custom_fields['saswp_mobile_app_schema_rating_count']) ) {
                           $input1['aggregateRating']['@type']       =   'AggregateRating';                           
                           $input1['aggregateRating']['ratingValue'] =    $custom_fields['saswp_mobile_app_schema_rating_value'];
                           $input1['aggregateRating']['ratingCount'] =    $custom_fields['saswp_mobile_app_schema_rating_count'];
                        }
                                                                                    
                        break;       
                
                case 'NewsArticle':
                    
                    if ( isset( $custom_fields['saswp_newsarticle_id']) ) {
                        $input1['@id'] =     get_permalink().$custom_fields['saswp_newsarticle_id'];
                    }
                    if ( isset( $custom_fields['saswp_newsarticle_main_entity_of_page']) ) {
                     $input1['mainEntityOfPage'] =    $custom_fields['saswp_newsarticle_main_entity_of_page'];
                    }
                    if ( isset( $custom_fields['saswp_newsarticle_URL']) ) {
                       $input1['url'] =    saswp_validate_url($custom_fields['saswp_newsarticle_URL']); 
                    }
                    if ( isset( $custom_fields['saswp_newsarticle_image']) ) {
                       $input1['image'] =    $custom_fields['saswp_newsarticle_image']; 
                    }
                    if ( isset( $custom_fields['saswp_newsarticle_inlanguage']) ) {
                        $input1['inLanguage'] =    $custom_fields['saswp_newsarticle_inlanguage']; 
                    }
                    if ( isset( $custom_fields['saswp_newsarticle_headline']) ) {
                       $input1['headline'] =    $custom_fields['saswp_newsarticle_headline']; 
                    }
                    if ( isset( $custom_fields['saswp_newsarticle_alternative_headline']) ) {
                        $input1['alternativeHeadline'] =    $custom_fields['saswp_newsarticle_alternative_headline']; 
                     }
                    if ( isset( $custom_fields['saswp_newsarticle_keywords']) ) {
                       $input1['keywords'] =    $custom_fields['saswp_newsarticle_keywords']; 
                    }
                    if ( isset( $custom_fields['saswp_newsarticle_date_published']) ) {
                       $input1['datePublished'] =    $custom_fields['saswp_newsarticle_date_published']; 
                    }
                    if ( isset( $custom_fields['saswp_newsarticle_date_modified']) ) {
                       $input1['dateModified'] =    $custom_fields['saswp_newsarticle_date_modified']; 
                    }
                    if ( isset( $custom_fields['saswp_newsarticle_description']) ) {
                       $input1['description'] =    wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_newsarticle_description'] ));  
                    }
                    if ( ! empty( $custom_fields['saswp_newsarticle_haspart'] ) && is_array( $custom_fields['saswp_newsarticle_haspart'] ) ) {
                        foreach ( $custom_fields['saswp_newsarticle_haspart'] as $hp_key => $has_part) {
                            if ( ! empty( $has_part ) && is_array( $has_part ) ) {
                                $input1['hasPart'][]       =   $has_part;    
                            }
                        } 
                    }
                    if ( ! empty( $custom_fields['saswp_newsarticle_ispartof'] ) && is_array( $custom_fields['saswp_newsarticle_ispartof'] ) ) {
                        foreach ( $custom_fields['saswp_newsarticle_ispartof'] as $ip_key => $is_part) {
                            if ( ! empty( $is_part ) && is_array( $is_part ) ) {
                                $input1['isPartOf'][]       =   $is_part;    
                            }
                        } 
                    }
                    if ( isset( $custom_fields['saswp_newsarticle_section']) ) {
                       $input1['articleSection'] = $custom_fields['saswp_newsarticle_section'];  
                    }
                    if ( isset( $custom_fields['saswp_newsarticle_body']) ) {
                        if($custom_fields['saswp_newsarticle_body']){
                            $input1['articleBody'] =    $custom_fields['saswp_newsarticle_body'];  
                        }else{
                            unset($input1['articleBody']);
                        }
                    }
                    if ( isset( $custom_fields['saswp_newsarticle_name']) ) {
                       $input1['name'] =    $custom_fields['saswp_newsarticle_name'];  
                    }
                    if ( isset( $custom_fields['saswp_newsarticle_thumbnailurl']) ) {
                       $input1['thumbnailUrl'] =    $custom_fields['saswp_newsarticle_thumbnailurl'];  
                    }
                    if ( isset( $custom_fields['saswp_newsarticle_timerequired']) ) {
                       $input1['timeRequired'] =    $custom_fields['saswp_newsarticle_timerequired'];  
                    }
                    if ( isset( $custom_fields['saswp_newsarticle_main_entity_id']) ) {
                       $input1['mainEntity']['@id'] =    $custom_fields['saswp_newsarticle_main_entity_id'];  
                    }
                    if( empty( $custom_fields['saswp_newsarticle_main_entity_id'] ) ) {
                        unset( $input1['mainEntity'] );
                    }

                    if ( ! empty( $custom_fields['saswp_newsarticle_editor_type']) ) {
                        if ( ! empty( $custom_fields['saswp_newsarticle_editor_name']) && $custom_fields['saswp_newsarticle_editor_name'] != '') {
                            $input1['editor'] = array();
                            $input1['editor']['@type'] = 'Person';
                            $input1['editor']['name']  =  $custom_fields['saswp_newsarticle_editor_name'];
                        }
                        if ( ! empty( $custom_fields['saswp_newsarticle_editor_type']) ) {
                            $input1['editor']['@type'] =    $custom_fields['saswp_newsarticle_editor_type'];
                        }
                        if ( ! empty( $custom_fields['saswp_newsarticle_editor_honorific_suffix']) && $custom_fields['saswp_newsarticle_editor_honorific_suffix'] != '') {
                            $input1['editor']['honorificSuffix']  =  $custom_fields['saswp_newsarticle_editor_honorific_suffix'];
                        }  
                        if ( ! empty( $custom_fields['saswp_newsarticle_editor_description']) ) {
                             $input1['editor']['description'] =    $custom_fields['saswp_newsarticle_editor_description'];
                        }
                        if ( ! empty( $custom_fields['saswp_newsarticle_editor_url']) ) {
                             $input1['editor']['url'] =    $custom_fields['saswp_newsarticle_editor_url'];
                        }
                        if ( ! empty( $custom_fields['saswp_newsarticle_editor_image']) ) {
                            $input1['editor']['Image']['url'] =    $custom_fields['saswp_newsarticle_editor_image'];
                        }
                    }

                    if ( isset( $custom_fields['saswp_newsarticle_author_type']) ) {
                        $input1['author']['@type'] =    $custom_fields['saswp_newsarticle_author_type']; 
                    }
                    if ( isset( $custom_fields['saswp_newsarticle_author_name']) ) {
                        $input1['author']['name'] =    $custom_fields['saswp_newsarticle_author_name']; 
                    }
                    if ( isset( $custom_fields['saswp_newsarticle_author_honorific_suffix']) && $custom_fields['saswp_newsarticle_author_honorific_suffix'] != '') {
                        $input1['author']['honorificSuffix']  =  $custom_fields['saswp_newsarticle_author_honorific_suffix'];
                    }  
                    if ( isset( $custom_fields['saswp_newsarticle_author_description']) ) {
                        $input1['author']['description'] =    $custom_fields['saswp_newsarticle_author_description'];
                    }
                    if ( isset( $custom_fields['saswp_newsarticle_author_url']) ) {
                        $input1['author']['url'] =    saswp_validate_url($custom_fields['saswp_newsarticle_author_url']); 
                    }
                    if ( isset( $custom_fields['saswp_newsarticle_author_image']) ) {
                       $input1['author']['Image']['url'] =    $custom_fields['saswp_newsarticle_author_image'];  
                    }
                    if ( isset( $custom_fields['saswp_newsarticle_author_social_profile']) && !empty($custom_fields['saswp_newsarticle_author_social_profile']) ) {
                        $explode_sp = explode(',', $custom_fields['saswp_newsarticle_author_social_profile']);
                        if ( is_array( $explode_sp) ) {
                            $input1['author']['sameAs'] =    $explode_sp;
                        }
                    }
                    if ( ! empty( $custom_fields['saswp_newsarticle_about']) && isset($custom_fields['saswp_newsarticle_about']) ) {         
                        $input1['about']['@type'] = 'Event';                   
                        $input1['about']['name'] = explode(',', $custom_fields['saswp_newsarticle_about']);    
                    }  
                    if ( isset( $custom_fields['saswp_newsarticle_organization_logo']) && isset($custom_fields['saswp_newsarticle_organization_name']) ) {
                     $input1['publisher']['@type']       =    'Organization';
                     $input1['publisher']['name']        =    $custom_fields['saswp_newsarticle_organization_name'];
                     $input1['publisher']['logo']        =    $custom_fields['saswp_newsarticle_organization_logo'];
                    }
                                        
                    break;
                
                    case 'AnalysisNewsArticle':
                    
                        if ( isset( $custom_fields['saswp_analysisnewsarticle_id']) ) {
                            $input1['@id'] =     get_permalink().$custom_fields['saswp_analysisnewsarticle_id'];
                        }
                        if ( isset( $custom_fields['saswp_analysisnewsarticle_main_entity_of_page']) ) {
                         $input1['mainEntityOfPage'] =    $custom_fields['saswp_analysisnewsarticle_main_entity_of_page'];
                        }
                        if ( isset( $custom_fields['saswp_analysisnewsarticle_URL']) ) {
                           $input1['url'] =    saswp_validate_url($custom_fields['saswp_analysisnewsarticle_URL']); 
                        }
                        if ( isset( $custom_fields['saswp_analysisnewsarticle_inlanguage']) ) {
                            $input1['inLanguage'] =    $custom_fields['saswp_analysisnewsarticle_inlanguage']; 
                        }
                        if ( isset( $custom_fields['saswp_analysisnewsarticle_headline']) ) {
                           $input1['headline'] =    $custom_fields['saswp_analysisnewsarticle_headline']; 
                        }
                        if ( isset( $custom_fields['saswp_analysisnewsarticle_keywords']) ) {
                           $input1['keywords'] =    $custom_fields['saswp_analysisnewsarticle_keywords']; 
                        }
                        if ( isset( $custom_fields['saswp_analysisnewsarticle_date_published']) ) {
                           $input1['datePublished'] =    $custom_fields['saswp_analysisnewsarticle_date_published']; 
                        }
                        if ( isset( $custom_fields['saswp_analysisnewsarticle_date_modified']) ) {
                           $input1['dateModified'] =    $custom_fields['saswp_analysisnewsarticle_date_modified']; 
                        }
                        if ( isset( $custom_fields['saswp_analysisnewsarticle_description']) ) {
                           $input1['description'] =    wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_analysisnewsarticle_description'] ));  
                        }
                        if ( ! empty( $custom_fields['saswp_analysisnewsarticle_haspart'] ) && is_array( $custom_fields['saswp_analysisnewsarticle_haspart'] ) ) {
                            foreach ( $custom_fields['saswp_analysisnewsarticle_haspart'] as $hp_key => $has_part) {
                                if ( ! empty( $has_part ) && is_array( $has_part ) ) {
                                    $input1['hasPart'][]       =   $has_part;    
                                }
                            } 
                        }
                        if ( ! empty( $custom_fields['saswp_analysisnewsarticle_ispartof'] ) && is_array( $custom_fields['saswp_analysisnewsarticle_ispartof'] ) ) {
                            foreach ( $custom_fields['saswp_analysisnewsarticle_ispartof'] as $ip_key => $is_part) {
                                if ( ! empty( $is_part ) && is_array( $is_part ) ) {
                                    $input1['isPartOf'][]       =   $is_part;    
                                }
                            } 
                        }
                        if ( isset( $custom_fields['saswp_analysisnewsarticle_section']) ) {
                           $input1['articleSection'] = $custom_fields['saswp_analysisnewsarticle_section'];  
                        }
                        if ( isset( $custom_fields['saswp_analysisnewsarticle_body']) ) {
                            if($custom_fields['saswp_analysisnewsarticle_body']){
                                $input1['articleBody'] =    $custom_fields['saswp_analysisnewsarticle_body']; 
                            }else{
                                unset($input1['articleBody']);
                            } 
                        }
                        if ( isset( $custom_fields['saswp_analysisnewsarticle_name']) ) {
                           $input1['name'] =    $custom_fields['saswp_analysisnewsarticle_name'];  
                        }
                        if ( isset( $custom_fields['saswp_analysisnewsarticle_thumbnailurl']) ) {
                           $input1['thumbnailUrl'] =    $custom_fields['saswp_analysisnewsarticle_thumbnailurl'];  
                        }
                        if ( isset( $custom_fields['saswp_analysisnewsarticle_timerequired']) ) {
                           $input1['timeRequired'] =    $custom_fields['saswp_analysisnewsarticle_timerequired'];  
                        }
                        if ( isset( $custom_fields['saswp_analysisnewsarticle_main_entity_id']) ) {
                           $input1['mainEntity']['@id'] =    $custom_fields['saswp_analysisnewsarticle_main_entity_id'];  
                        }
    
                        if ( ! empty( $custom_fields['saswp_analysisnewsarticle_editor_type']) ) {
                            if ( ! empty( $custom_fields['saswp_analysisnewsarticle_editor_name']) && $custom_fields['saswp_analysisnewsarticle_editor_name'] != '') {
                                $input1['editor'] = array();
                                $input1['editor']['@type'] = 'Person';
                                $input1['editor']['name']  =  $custom_fields['saswp_analysisnewsarticle_editor_name'];
                            }
                            if ( ! empty( $custom_fields['saswp_analysisnewsarticle_editor_type']) ) {
                                $input1['editor']['@type'] =    $custom_fields['saswp_analysisnewsarticle_editor_type'];
                            }
                            if ( ! empty( $custom_fields['saswp_analysisnewsarticle_editor_honorific_suffix']) && $custom_fields['saswp_analysisnewsarticle_editor_honorific_suffix'] != '') {
                                $input1['editor']['honorificSuffix']  =  $custom_fields['saswp_analysisnewsarticle_editor_honorific_suffix'];
                            }  
                            if ( ! empty( $custom_fields['saswp_analysisnewsarticle_editor_description']) ) {
                                 $input1['editor']['description'] =    $custom_fields['saswp_analysisnewsarticle_editor_description'];
                            }
                            if ( ! empty( $custom_fields['saswp_analysisnewsarticle_editor_url']) ) {
                                 $input1['editor']['url'] =    $custom_fields['saswp_analysisnewsarticle_editor_url'];
                            }
                            if ( ! empty( $custom_fields['saswp_analysisnewsarticle_editor_image']) ) {
                                $input1['editor']['Image']['url'] =    $custom_fields['saswp_analysisnewsarticle_editor_image'];
                            }
                        }
    
                        if ( isset( $custom_fields['saswp_analysisnewsarticle_author_type']) ) {
                            $input1['author']['@type'] =    $custom_fields['saswp_analysisnewsarticle_author_type']; 
                        }
                        if ( isset( $custom_fields['saswp_analysisnewsarticle_author_name']) ) {
                            $input1['author']['name'] =    $custom_fields['saswp_analysisnewsarticle_author_name']; 
                        }
                        if ( isset( $custom_fields['saswp_analysisnewsarticle_author_honorific_suffix']) && $custom_fields['saswp_analysisnewsarticle_author_honorific_suffix'] != '') {
                            $input1['author']['honorificSuffix']  =  $custom_fields['saswp_analysisnewsarticle_author_honorific_suffix'];
                        }  
                        if ( isset( $custom_fields['saswp_analysisnewsarticle_author_description']) ) {
                            $input1['author']['description'] =    $custom_fields['saswp_analysisnewsarticle_author_description'];
                        }
                        if ( isset( $custom_fields['saswp_analysisnewsarticle_author_url']) ) {
                            $input1['author']['url'] =    saswp_validate_url($custom_fields['saswp_analysisnewsarticle_author_url']); 
                        }
                        if ( isset( $custom_fields['saswp_analysisnewsarticle_author_image']) ) {
                           $input1['author']['Image']['url'] =    $custom_fields['saswp_analysisnewsarticle_author_image'];  
                        }
                        if ( ! empty( $custom_fields['saswp_analysisnewsarticle_about']) && isset($custom_fields['saswp_analysisnewsarticle_about']) ) {         
                            $input1['about']['@type'] = 'Event';                   
                            $input1['about']['name'] = explode(',', $custom_fields['saswp_analysisnewsarticle_about']);    
                        }  
                        if ( isset( $custom_fields['saswp_analysisnewsarticle_organization_logo']) && isset($custom_fields['saswp_analysisnewsarticle_organization_name']) ) {
                         $input1['publisher']['@type']       =    'Organization';
                         $input1['publisher']['name']        =    $custom_fields['saswp_analysisnewsarticle_organization_name'];
                         $input1['publisher']['logo']        =    $custom_fields['saswp_analysisnewsarticle_organization_logo'];
                        }
                                            
                        break;

                        case 'AskPublicNewsArticle':
                    
                            if ( isset( $custom_fields['saswp_askpublicnewsarticle_id']) ) {
                                $input1['@id'] =     get_permalink().$custom_fields['saswp_askpublicnewsarticle_id'];
                            }
                            if ( isset( $custom_fields['saswp_askpublicnewsarticle_main_entity_of_page']) ) {
                             $input1['mainEntityOfPage'] =    $custom_fields['saswp_askpublicnewsarticle_main_entity_of_page'];
                            }
                            if ( isset( $custom_fields['saswp_askpublicnewsarticle_URL']) ) {
                               $input1['url'] =    saswp_validate_url($custom_fields['saswp_askpublicnewsarticle_URL']); 
                            }
                            if ( isset( $custom_fields['saswp_askpublicnewsarticle_inlanguage']) ) {
                                $input1['inLanguage'] =    $custom_fields['saswp_askpublicnewsarticle_inlanguage']; 
                            }
                            if ( isset( $custom_fields['saswp_askpublicnewsarticle_headline']) ) {
                               $input1['headline'] =    $custom_fields['saswp_askpublicnewsarticle_headline']; 
                            }
                            if ( isset( $custom_fields['saswp_askpublicnewsarticle_keywords']) ) {
                               $input1['keywords'] =    $custom_fields['saswp_askpublicnewsarticle_keywords']; 
                            }
                            if ( isset( $custom_fields['saswp_askpublicnewsarticle_date_published']) ) {
                               $input1['datePublished'] =    $custom_fields['saswp_askpublicnewsarticle_date_published']; 
                            }
                            if ( isset( $custom_fields['saswp_askpublicnewsarticle_date_modified']) ) {
                               $input1['dateModified'] =    $custom_fields['saswp_askpublicnewsarticle_date_modified']; 
                            }
                            if ( isset( $custom_fields['saswp_askpublicnewsarticle_description']) ) {
                               $input1['description'] =    wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_askpublicnewsarticle_description'] ));  
                            }
                            if ( ! empty( $custom_fields['saswp_askpublicnewsarticle_haspart'] ) && is_array( $custom_fields['saswp_askpublicnewsarticle_haspart'] ) ) {
                                foreach ( $custom_fields['saswp_askpublicnewsarticle_haspart'] as $hp_key => $has_part) {
                                    if ( ! empty( $has_part ) && is_array( $has_part ) ) {
                                        $input1['hasPart'][]       =   $has_part;    
                                    }
                                } 
                            }
                            if ( ! empty( $custom_fields['saswp_askpublicnewsarticle_ispartof'] ) && is_array( $custom_fields['saswp_askpublicnewsarticle_ispartof'] ) ) {
                                foreach ( $custom_fields['saswp_askpublicnewsarticle_ispartof'] as $ip_key => $is_part) {
                                    if ( ! empty( $is_part ) && is_array( $is_part ) ) {
                                        $input1['isPartOf'][]       =   $is_part;    
                                    }
                                } 
                            }
                            if ( isset( $custom_fields['saswp_askpublicnewsarticle_section']) ) {
                               $input1['articleSection'] = $custom_fields['saswp_askpublicnewsarticle_section'];  
                            }
                            if ( isset( $custom_fields['saswp_askpublicnewsarticle_body']) ) {
                                if($custom_fields['saswp_askpublicnewsarticle_body']){
                                    $input1['articleBody'] =    $custom_fields['saswp_askpublicnewsarticle_body']; 
                                }else{
                                    unset($input1['articleBody']);
                                } 
                            }
                            if ( isset( $custom_fields['saswp_askpublicnewsarticle_name']) ) {
                               $input1['name'] =    $custom_fields['saswp_askpublicnewsarticle_name'];  
                            }
                            if ( isset( $custom_fields['saswp_askpublicnewsarticle_thumbnailurl']) ) {
                               $input1['thumbnailUrl'] =    $custom_fields['saswp_askpublicnewsarticle_thumbnailurl'];  
                            }
                            if ( isset( $custom_fields['saswp_askpublicnewsarticle_timerequired']) ) {
                               $input1['timeRequired'] =    $custom_fields['saswp_askpublicnewsarticle_timerequired'];  
                            }
                            if ( isset( $custom_fields['saswp_askpublicnewsarticle_main_entity_id']) ) {
                               $input1['mainEntity']['@id'] =    $custom_fields['saswp_askpublicnewsarticle_main_entity_id'];  
                            }
        
                            if ( ! empty( $custom_fields['saswp_askpublicnewsarticle_editor_type']) ) {
                                if ( ! empty( $custom_fields['saswp_askpublicnewsarticle_editor_name']) && $custom_fields['saswp_askpublicnewsarticle_editor_name'] != '') {
                                    $input1['editor'] = array();
                                    $input1['editor']['@type'] = 'Person';
                                    $input1['editor']['name']  =  $custom_fields['saswp_askpublicnewsarticle_editor_name'];
                                }
                                if ( ! empty( $custom_fields['saswp_askpublicnewsarticle_editor_type']) ) {
                                    $input1['editor']['@type'] =    $custom_fields['saswp_askpublicnewsarticle_editor_type'];
                                }
                                if ( ! empty( $custom_fields['saswp_askpublicnewsarticle_editor_honorific_suffix']) && $custom_fields['saswp_askpublicnewsarticle_editor_honorific_suffix'] != '') {
                                    $input1['editor']['honorificSuffix']  =  $custom_fields['saswp_askpublicnewsarticle_editor_honorific_suffix'];
                                }  
                                if ( ! empty( $custom_fields['saswp_askpublicnewsarticle_editor_description']) ) {
                                     $input1['editor']['description'] =    $custom_fields['saswp_askpublicnewsarticle_editor_description'];
                                }
                                if ( ! empty( $custom_fields['saswp_askpublicnewsarticle_editor_url']) ) {
                                     $input1['editor']['url'] =    $custom_fields['saswp_askpublicnewsarticle_editor_url'];
                                }
                                if ( ! empty( $custom_fields['saswp_askpublicnewsarticle_editor_image']) ) {
                                    $input1['editor']['Image']['url'] =    $custom_fields['saswp_askpublicnewsarticle_editor_image'];
                                }
                            }
        
                            if ( isset( $custom_fields['saswp_askpublicnewsarticle_author_type']) ) {
                                $input1['author']['@type'] =    $custom_fields['saswp_askpublicnewsarticle_author_type']; 
                            }
                            if ( isset( $custom_fields['saswp_askpublicnewsarticle_author_name']) ) {
                                $input1['author']['name'] =    $custom_fields['saswp_askpublicnewsarticle_author_name']; 
                            }
                            if ( isset( $custom_fields['saswp_askpublicnewsarticle_author_honorific_suffix']) && $custom_fields['saswp_askpublicnewsarticle_author_honorific_suffix'] != '') {
                                $input1['author']['honorificSuffix']  =  $custom_fields['saswp_askpublicnewsarticle_author_honorific_suffix'];
                            }  
                            if ( isset( $custom_fields['saswp_askpublicnewsarticle_author_description']) ) {
                                $input1['author']['description'] =    $custom_fields['saswp_askpublicnewsarticle_author_description'];
                            }
                            if ( isset( $custom_fields['saswp_askpublicnewsarticle_author_url']) ) {
                                $input1['author']['url'] =    saswp_validate_url($custom_fields['saswp_askpublicnewsarticle_author_url']); 
                            }
                            if ( isset( $custom_fields['saswp_askpublicnewsarticle_author_image']) ) {
                               $input1['author']['Image']['url'] =    $custom_fields['saswp_askpublicnewsarticle_author_image'];  
                            }
                            if ( ! empty( $custom_fields['saswp_askpublicnewsarticle_about']) && isset($custom_fields['saswp_askpublicnewsarticle_about']) ) {         
                                $input1['about']['@type'] = 'Event';                   
                                $input1['about']['name'] = explode(',', $custom_fields['saswp_askpublicnewsarticle_about']);    
                            }  
                            if ( isset( $custom_fields['saswp_askpublicnewsarticle_organization_logo']) && isset($custom_fields['saswp_askpublicnewsarticle_organization_name']) ) {
                             $input1['publisher']['@type']       =    'Organization';
                             $input1['publisher']['name']        =    $custom_fields['saswp_askpublicnewsarticle_organization_name'];
                             $input1['publisher']['logo']        =    $custom_fields['saswp_askpublicnewsarticle_organization_logo'];
                            }
                                                
                            break;
    
                case 'BackgroundNewsArticle':
        
                    if ( isset( $custom_fields['saswp_backgroundnewsarticle_id']) ) {
                        $input1['@id'] =     get_permalink().$custom_fields['saswp_backgroundnewsarticle_id'];
                    }
                    if ( isset( $custom_fields['saswp_backgroundnewsarticle_main_entity_of_page']) ) {
                        $input1['mainEntityOfPage'] =    $custom_fields['saswp_backgroundnewsarticle_main_entity_of_page'];
                    }
                    if ( isset( $custom_fields['saswp_backgroundnewsarticle_URL']) ) {
                        $input1['url'] =    saswp_validate_url($custom_fields['saswp_backgroundnewsarticle_URL']); 
                    }
                    if ( isset( $custom_fields['saswp_backgroundnewsarticle_inlanguage']) ) {
                        $input1['inLanguage'] =    $custom_fields['saswp_backgroundnewsarticle_inlanguage']; 
                    }
                    if ( isset( $custom_fields['saswp_backgroundnewsarticle_headline']) ) {
                        $input1['headline'] =    $custom_fields['saswp_backgroundnewsarticle_headline']; 
                    }
                    if ( isset( $custom_fields['saswp_backgroundnewsarticle_keywords']) ) {
                        $input1['keywords'] =    $custom_fields['saswp_backgroundnewsarticle_keywords']; 
                    }
                    if ( isset( $custom_fields['saswp_backgroundnewsarticle_date_published']) ) {
                        $input1['datePublished'] =    $custom_fields['saswp_backgroundnewsarticle_date_published']; 
                    }
                    if ( isset( $custom_fields['saswp_backgroundnewsarticle_date_modified']) ) {
                        $input1['dateModified'] =    $custom_fields['saswp_backgroundnewsarticle_date_modified']; 
                    }
                    if ( isset( $custom_fields['saswp_backgroundnewsarticle_description']) ) {
                        $input1['description'] =    wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_backgroundnewsarticle_description'] ));  
                    }
                    if ( ! empty( $custom_fields['saswp_backgroundnewsarticle_haspart'] ) && is_array( $custom_fields['saswp_backgroundnewsarticle_haspart'] ) ) {
                        foreach ( $custom_fields['saswp_backgroundnewsarticle_haspart'] as $hp_key => $has_part) {
                            if ( ! empty( $has_part ) && is_array( $has_part ) ) {
                                $input1['hasPart'][]       =   $has_part;    
                            }
                        } 
                    }
                    if ( ! empty( $custom_fields['saswp_backgroundnewsarticle_ispartof'] ) && is_array( $custom_fields['saswp_backgroundnewsarticle_ispartof'] ) ) {
                        foreach ( $custom_fields['saswp_backgroundnewsarticle_ispartof'] as $ip_key => $is_part) {
                            if ( ! empty( $is_part ) && is_array( $is_part ) ) {
                                $input1['isPartOf'][]       =   $is_part;    
                            }
                        } 
                    }
                    if ( isset( $custom_fields['saswp_backgroundnewsarticle_section']) ) {
                        $input1['articleSection'] = $custom_fields['saswp_backgroundnewsarticle_section'];  
                    }
                    if ( isset( $custom_fields['saswp_backgroundnewsarticle_body']) ) {
                        if($custom_fields['saswp_backgroundnewsarticle_body']){
                            $input1['articleBody'] =    $custom_fields['saswp_backgroundnewsarticle_body'];  
                        }else{
                            unset($input1['articleBody']);
                        }
                    }
                    if ( isset( $custom_fields['saswp_backgroundnewsarticle_name']) ) {
                        $input1['name'] =    $custom_fields['saswp_backgroundnewsarticle_name'];  
                    }
                    if ( isset( $custom_fields['saswp_backgroundnewsarticle_thumbnailurl']) ) {
                        $input1['thumbnailUrl'] =    $custom_fields['saswp_backgroundnewsarticle_thumbnailurl'];  
                    }
                    if ( isset( $custom_fields['saswp_backgroundnewsarticle_timerequired']) ) {
                        $input1['timeRequired'] =    $custom_fields['saswp_backgroundnewsarticle_timerequired'];  
                    }
                    if ( isset( $custom_fields['saswp_backgroundnewsarticle_main_entity_id']) ) {
                        $input1['mainEntity']['@id'] =    $custom_fields['saswp_backgroundnewsarticle_main_entity_id'];  
                    }

                    if ( ! empty( $custom_fields['saswp_backgroundnewsarticle_editor_type']) ) {
                        if ( ! empty( $custom_fields['saswp_backgroundnewsarticle_editor_name']) && $custom_fields['saswp_backgroundnewsarticle_editor_name'] != '') {
                            $input1['editor'] = array();
                            $input1['editor']['@type'] = 'Person';
                            $input1['editor']['name']  =  $custom_fields['saswp_backgroundnewsarticle_editor_name'];
                        }
                        if ( ! empty( $custom_fields['saswp_backgroundnewsarticle_editor_type']) ) {
                            $input1['editor']['@type'] =    $custom_fields['saswp_backgroundnewsarticle_editor_type'];
                        }
                        if ( ! empty( $custom_fields['saswp_backgroundnewsarticle_editor_honorific_suffix']) && $custom_fields['saswp_backgroundnewsarticle_editor_honorific_suffix'] != '') {
                            $input1['editor']['honorificSuffix']  =  $custom_fields['saswp_backgroundnewsarticle_editor_honorific_suffix'];
                        }  
                        if ( ! empty( $custom_fields['saswp_backgroundnewsarticle_editor_description']) ) {
                                $input1['editor']['description'] =    $custom_fields['saswp_backgroundnewsarticle_editor_description'];
                        }
                        if ( ! empty( $custom_fields['saswp_backgroundnewsarticle_editor_url']) ) {
                                $input1['editor']['url'] =    $custom_fields['saswp_backgroundnewsarticle_editor_url'];
                        }
                        if ( ! empty( $custom_fields['saswp_backgroundnewsarticle_editor_image']) ) {
                            $input1['editor']['Image']['url'] =    $custom_fields['saswp_backgroundnewsarticle_editor_image'];
                        }
                    }

                    if ( isset( $custom_fields['saswp_backgroundnewsarticle_author_type']) ) {
                        $input1['author']['@type'] =    $custom_fields['saswp_backgroundnewsarticle_author_type']; 
                    }
                    if ( isset( $custom_fields['saswp_backgroundnewsarticle_author_name']) ) {
                        $input1['author']['name'] =    $custom_fields['saswp_backgroundnewsarticle_author_name']; 
                    }
                    if ( isset( $custom_fields['saswp_backgroundnewsarticle_author_honorific_suffix']) && $custom_fields['saswp_backgroundnewsarticle_author_honorific_suffix'] != '') {
                        $input1['author']['honorificSuffix']  =  $custom_fields['saswp_backgroundnewsarticle_author_honorific_suffix'];
                    }  
                    if ( isset( $custom_fields['saswp_backgroundnewsarticle_author_description']) ) {
                        $input1['author']['description'] =    $custom_fields['saswp_backgroundnewsarticle_author_description'];
                    }
                    if ( isset( $custom_fields['saswp_backgroundnewsarticle_author_url']) ) {
                        $input1['author']['url'] =    saswp_validate_url($custom_fields['saswp_backgroundnewsarticle_author_url']); 
                    }
                    if ( isset( $custom_fields['saswp_backgroundnewsarticle_author_image']) ) {
                        $input1['author']['Image']['url'] =    $custom_fields['saswp_backgroundnewsarticle_author_image'];  
                    }
                    if ( ! empty( $custom_fields['saswp_backgroundnewsarticle_about']) && isset($custom_fields['saswp_backgroundnewsarticle_about']) ) {         
                        $input1['about']['@type'] = 'Event';                   
                        $input1['about']['name'] = explode(',', $custom_fields['saswp_backgroundnewsarticle_about']);    
                    }  
                    if ( isset( $custom_fields['saswp_backgroundnewsarticle_organization_logo']) && isset($custom_fields['saswp_backgroundnewsarticle_organization_name']) ) {
                        $input1['publisher']['@type']       =    'Organization';
                        $input1['publisher']['name']        =    $custom_fields['saswp_backgroundnewsarticle_organization_name'];
                        $input1['publisher']['logo']        =    $custom_fields['saswp_backgroundnewsarticle_organization_logo'];
                    }
                                        
                    break;

                    case 'OpinionNewsArticle':
        
                        if ( isset( $custom_fields['saswp_opinionnewsarticle_id']) ) {
                            $input1['@id'] =     get_permalink().$custom_fields['saswp_opinionnewsarticle_id'];
                        }
                        if ( isset( $custom_fields['saswp_opinionnewsarticle_main_entity_of_page']) ) {
                            $input1['mainEntityOfPage'] =    $custom_fields['saswp_opinionnewsarticle_main_entity_of_page'];
                        }
                        if ( isset( $custom_fields['saswp_opinionnewsarticle_URL']) ) {
                            $input1['url'] =    saswp_validate_url($custom_fields['saswp_opinionnewsarticle_URL']); 
                        }
                        if ( isset( $custom_fields['saswp_opinionnewsarticle_inlanguage']) ) {
                            $input1['inLanguage'] =    $custom_fields['saswp_opinionnewsarticle_inlanguage']; 
                        }
                        if ( isset( $custom_fields['saswp_opinionnewsarticle_headline']) ) {
                            $input1['headline'] =    $custom_fields['saswp_opinionnewsarticle_headline']; 
                        }
                        if ( isset( $custom_fields['saswp_opinionnewsarticle_keywords']) ) {
                            $input1['keywords'] =    $custom_fields['saswp_opinionnewsarticle_keywords']; 
                        }
                        if ( isset( $custom_fields['saswp_opinionnewsarticle_date_published']) ) {
                            $input1['datePublished'] =    $custom_fields['saswp_opinionnewsarticle_date_published']; 
                        }
                        if ( isset( $custom_fields['saswp_opinionnewsarticle_date_modified']) ) {
                            $input1['dateModified'] =    $custom_fields['saswp_opinionnewsarticle_date_modified']; 
                        }
                        if ( isset( $custom_fields['saswp_opinionnewsarticle_description']) ) {
                            $input1['description'] =    wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_opinionnewsarticle_description'] ));  
                        }
                        if ( ! empty( $custom_fields['saswp_opinionnewsarticle_haspart'] ) && is_array( $custom_fields['saswp_opinionnewsarticle_haspart'] ) ) {
                            foreach ( $custom_fields['saswp_opinionnewsarticle_haspart'] as $hp_key => $has_part) {
                                if ( ! empty( $has_part ) && is_array( $has_part ) ) {
                                    $input1['hasPart'][]       =   $has_part;    
                                }
                            } 
                        }
                        if ( ! empty( $custom_fields['saswp_opinionnewsarticle_ispartof'] ) && is_array( $custom_fields['saswp_opinionnewsarticle_ispartof'] ) ) {
                            foreach ( $custom_fields['saswp_opinionnewsarticle_ispartof'] as $ip_key => $is_part) {
                                if ( ! empty( $is_part ) && is_array( $is_part ) ) {
                                    $input1['isPartOf'][]       =   $is_part;    
                                }
                            } 
                        }
                        if ( isset( $custom_fields['saswp_opinionnewsarticle_section']) ) {
                            $input1['articleSection'] = $custom_fields['saswp_opinionnewsarticle_section'];  
                        }
                        if ( isset( $custom_fields['saswp_opinionnewsarticle_body']) ) {
                            if($custom_fields['saswp_opinionnewsarticle_body']){
                                $input1['articleBody'] =    $custom_fields['saswp_opinionnewsarticle_body'];
                            }else{
                                unset($input1['articleBody']);
                            }  
                        }
                        if ( isset( $custom_fields['saswp_opinionnewsarticle_name']) ) {
                            $input1['name'] =    $custom_fields['saswp_opinionnewsarticle_name'];  
                        }
                        if ( isset( $custom_fields['saswp_opinionnewsarticle_thumbnailurl']) ) {
                            $input1['thumbnailUrl'] =    $custom_fields['saswp_opinionnewsarticle_thumbnailurl'];  
                        }
                        if ( isset( $custom_fields['saswp_opinionnewsarticle_timerequired']) ) {
                            $input1['timeRequired'] =    $custom_fields['saswp_opinionnewsarticle_timerequired'];  
                        }
                        if ( isset( $custom_fields['saswp_opinionnewsarticle_main_entity_id']) ) {
                            $input1['mainEntity']['@id'] =    $custom_fields['saswp_opinionnewsarticle_main_entity_id'];  
                        }
    
                        if ( ! empty( $custom_fields['saswp_opinionnewsarticle_editor_type']) ) {
                            if ( ! empty( $custom_fields['saswp_opinionnewsarticle_editor_name']) && $custom_fields['saswp_opinionnewsarticle_editor_name'] != '') {
                                $input1['editor'] = array();
                                $input1['editor']['@type'] = 'Person';
                                $input1['editor']['name']  =  $custom_fields['saswp_opinionnewsarticle_editor_name'];
                            }
                            if ( ! empty( $custom_fields['saswp_opinionnewsarticle_editor_type']) ) {
                                $input1['editor']['@type'] =    $custom_fields['saswp_opinionnewsarticle_editor_type'];
                            }
                            if ( ! empty( $custom_fields['saswp_opinionnewsarticle_editor_honorific_suffix']) && $custom_fields['saswp_opinionnewsarticle_editor_honorific_suffix'] != '') {
                                $input1['editor']['honorificSuffix']  =  $custom_fields['saswp_opinionnewsarticle_editor_honorific_suffix'];
                            }  
                            if ( ! empty( $custom_fields['saswp_opinionnewsarticle_editor_description']) ) {
                                    $input1['editor']['description'] =    $custom_fields['saswp_opinionnewsarticle_editor_description'];
                            }
                            if ( ! empty( $custom_fields['saswp_opinionnewsarticle_editor_url']) ) {
                                    $input1['editor']['url'] =    $custom_fields['saswp_opinionnewsarticle_editor_url'];
                            }
                            if ( ! empty( $custom_fields['saswp_opinionnewsarticle_editor_image']) ) {
                                $input1['editor']['Image']['url'] =    $custom_fields['saswp_opinionnewsarticle_editor_image'];
                            }
                        }
    
                        if ( isset( $custom_fields['saswp_opinionnewsarticle_author_type']) ) {
                            $input1['author']['@type'] =    $custom_fields['saswp_opinionnewsarticle_author_type']; 
                        }
                        if ( isset( $custom_fields['saswp_opinionnewsarticle_author_name']) ) {
                            $input1['author']['name'] =    $custom_fields['saswp_opinionnewsarticle_author_name']; 
                        }
                        if ( isset( $custom_fields['saswp_opinionnewsarticle_author_honorific_suffix']) && $custom_fields['saswp_opinionnewsarticle_author_honorific_suffix'] != '') {
                            $input1['author']['honorificSuffix']  =  $custom_fields['saswp_opinionnewsarticle_author_honorific_suffix'];
                        }  
                        if ( isset( $custom_fields['saswp_opinionnewsarticle_author_description']) ) {
                            $input1['author']['description'] =    $custom_fields['saswp_opinionnewsarticle_author_description'];
                        }
                        if ( isset( $custom_fields['saswp_opinionnewsarticle_author_url']) ) {
                            $input1['author']['url'] =    saswp_validate_url($custom_fields['saswp_opinionnewsarticle_author_url']); 
                        }
                        if ( isset( $custom_fields['saswp_opinionnewsarticle_author_image']) ) {
                            $input1['author']['Image']['url'] =    $custom_fields['saswp_opinionnewsarticle_author_image'];  
                        }
                        if ( ! empty( $custom_fields['saswp_opinionnewsarticle_about']) && isset($custom_fields['saswp_opinionnewsarticle_about']) ) {         
                            $input1['about']['@type'] = 'Event';                   
                            $input1['about']['name'] = explode(',', $custom_fields['saswp_opinionnewsarticle_about']);    
                        }  
                        if ( isset( $custom_fields['saswp_opinionnewsarticle_organization_logo']) && isset($custom_fields['saswp_opinionnewsarticle_organization_name']) ) {
                            $input1['publisher']['@type']       =    'Organization';
                            $input1['publisher']['name']        =    $custom_fields['saswp_opinionnewsarticle_organization_name'];
                            $input1['publisher']['logo']        =    $custom_fields['saswp_opinionnewsarticle_organization_logo'];
                        }
                                            
                        break;

                    case 'ReportageNewsArticle':
    
                        if ( isset( $custom_fields['saswp_reportagenewsarticle_id']) ) {
                            $input1['@id'] =     get_permalink().$custom_fields['saswp_reportagenewsarticle_id'];
                        }
                        if ( isset( $custom_fields['saswp_reportagenewsarticle_main_entity_of_page']) ) {
                            $input1['mainEntityOfPage'] =    $custom_fields['saswp_reportagenewsarticle_main_entity_of_page'];
                        }
                        if ( isset( $custom_fields['saswp_reportagenewsarticle_URL']) ) {
                            $input1['url'] =    saswp_validate_url($custom_fields['saswp_reportagenewsarticle_URL']); 
                        }
                        if ( isset( $custom_fields['saswp_reportagenewsarticle_inlanguage']) ) {
                            $input1['inLanguage'] =    $custom_fields['saswp_reportagenewsarticle_inlanguage']; 
                        }
                        if ( isset( $custom_fields['saswp_reportagenewsarticle_headline']) ) {
                            $input1['headline'] =    $custom_fields['saswp_reportagenewsarticle_headline']; 
                        }
                        if ( isset( $custom_fields['saswp_reportagenewsarticle_keywords']) ) {
                            $input1['keywords'] =    $custom_fields['saswp_reportagenewsarticle_keywords']; 
                        }
                        if ( isset( $custom_fields['saswp_reportagenewsarticle_date_published']) ) {
                            $input1['datePublished'] =    $custom_fields['saswp_reportagenewsarticle_date_published']; 
                        }
                        if ( isset( $custom_fields['saswp_reportagenewsarticle_date_modified']) ) {
                            $input1['dateModified'] =    $custom_fields['saswp_reportagenewsarticle_date_modified']; 
                        }
                        if ( isset( $custom_fields['saswp_reportagenewsarticle_description']) ) {
                            $input1['description'] =    wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_reportagenewsarticle_description'] ));  
                        }
                        if ( ! empty( $custom_fields['saswp_reportagenewsarticle_haspart'] ) && is_array( $custom_fields['saswp_reportagenewsarticle_haspart'] ) ) {
                            foreach ( $custom_fields['saswp_reportagenewsarticle_haspart'] as $hp_key => $has_part) {
                                if ( ! empty( $has_part ) && is_array( $has_part ) ) {
                                    $input1['hasPart'][]       =   $has_part;    
                                }
                            } 
                        }
                        if ( ! empty( $custom_fields['saswp_reportagenewsarticle_ispartof'] ) && is_array( $custom_fields['saswp_reportagenewsarticle_ispartof'] ) ) {
                            foreach ( $custom_fields['saswp_reportagenewsarticle_ispartof'] as $ip_key => $is_part) {
                                if ( ! empty( $is_part ) && is_array( $is_part ) ) {
                                    $input1['isPartOf'][]       =   $is_part;    
                                }
                            } 
                        }
                        if ( isset( $custom_fields['saswp_reportagenewsarticle_section']) ) {
                            $input1['articleSection'] = $custom_fields['saswp_reportagenewsarticle_section'];  
                        }
                        if ( isset( $custom_fields['saswp_reportagenewsarticle_body']) ) {
                            if($custom_fields['saswp_reportagenewsarticle_body']){
                                $input1['articleBody'] =    $custom_fields['saswp_reportagenewsarticle_body'];
                            }else{
                                unset($input1['articleBody']);
                            }  
                        }
                        if ( isset( $custom_fields['saswp_reportagenewsarticle_name']) ) {
                            $input1['name'] =    $custom_fields['saswp_reportagenewsarticle_name'];  
                        }
                        if ( isset( $custom_fields['saswp_reportagenewsarticle_thumbnailurl']) ) {
                            $input1['thumbnailUrl'] =    $custom_fields['saswp_reportagenewsarticle_thumbnailurl'];  
                        }
                        if ( isset( $custom_fields['saswp_reportagenewsarticle_timerequired']) ) {
                            $input1['timeRequired'] =    $custom_fields['saswp_reportagenewsarticle_timerequired'];  
                        }
                        if ( isset( $custom_fields['saswp_reportagenewsarticle_main_entity_id']) ) {
                            $input1['mainEntity']['@id'] =    $custom_fields['saswp_reportagenewsarticle_main_entity_id'];  
                        }
    
                        if ( ! empty( $custom_fields['saswp_reportagenewsarticle_editor_type']) ) {
                            if ( ! empty( $custom_fields['saswp_reportagenewsarticle_editor_name']) && $custom_fields['saswp_reportagenewsarticle_editor_name'] != '') {
                                $input1['editor'] = array();
                                $input1['editor']['@type'] = 'Person';
                                $input1['editor']['name']  =  $custom_fields['saswp_reportagenewsarticle_editor_name'];
                            }
                            if ( ! empty( $custom_fields['saswp_reportagenewsarticle_editor_type']) ) {
                                $input1['editor']['@type'] =    $custom_fields['saswp_reportagenewsarticle_editor_type'];
                            }
                            if ( ! empty( $custom_fields['saswp_reportagenewsarticle_editor_honorific_suffix']) && $custom_fields['saswp_reportagenewsarticle_editor_honorific_suffix'] != '') {
                                $input1['editor']['honorificSuffix']  =  $custom_fields['saswp_reportagenewsarticle_editor_honorific_suffix'];
                            }  
                            if ( ! empty( $custom_fields['saswp_reportagenewsarticle_editor_description']) ) {
                                    $input1['editor']['description'] =    $custom_fields['saswp_reportagenewsarticle_editor_description'];
                            }
                            if ( ! empty( $custom_fields['saswp_reportagenewsarticle_editor_url']) ) {
                                    $input1['editor']['url'] =    $custom_fields['saswp_reportagenewsarticle_editor_url'];
                            }
                            if ( ! empty( $custom_fields['saswp_reportagenewsarticle_editor_image']) ) {
                                $input1['editor']['Image']['url'] =    $custom_fields['saswp_reportagenewsarticle_editor_image'];
                            }
                        }
    
                        if ( isset( $custom_fields['saswp_reportagenewsarticle_author_type']) ) {
                            $input1['author']['@type'] =    $custom_fields['saswp_reportagenewsarticle_author_type']; 
                        }
                        if ( isset( $custom_fields['saswp_reportagenewsarticle_author_name']) ) {
                            $input1['author']['name'] =    $custom_fields['saswp_reportagenewsarticle_author_name']; 
                        }
                        if ( isset( $custom_fields['saswp_reportagenewsarticle_author_honorific_suffix']) && $custom_fields['saswp_reportagenewsarticle_author_honorific_suffix'] != '') {
                            $input1['author']['honorificSuffix']  =  $custom_fields['saswp_reportagenewsarticle_author_honorific_suffix'];
                        }  
                        if ( isset( $custom_fields['saswp_reportagenewsarticle_author_description']) ) {
                            $input1['author']['description'] =    $custom_fields['saswp_reportagenewsarticle_author_description'];
                        }
                        if ( isset( $custom_fields['saswp_reportagenewsarticle_author_url']) ) {
                            $input1['author']['url'] =    saswp_validate_url($custom_fields['saswp_reportagenewsarticle_author_url']); 
                        }
                        if ( isset( $custom_fields['saswp_reportagenewsarticle_author_image']) ) {
                            $input1['author']['Image']['url'] =    $custom_fields['saswp_reportagenewsarticle_author_image'];  
                        }
                        if ( ! empty( $custom_fields['saswp_reportagenewsarticle_about']) && isset($custom_fields['saswp_reportagenewsarticle_about']) ) {         
                            $input1['about']['@type'] = 'Event';                   
                            $input1['about']['name'] = explode(',', $custom_fields['saswp_reportagenewsarticle_about']);    
                        }  
                        if ( isset( $custom_fields['saswp_reportagenewsarticle_organization_logo']) && isset($custom_fields['saswp_reportagenewsarticle_organization_name']) ) {
                            $input1['publisher']['@type']       =    'Organization';
                            $input1['publisher']['name']        =    $custom_fields['saswp_reportagenewsarticle_organization_name'];
                            $input1['publisher']['logo']        =    $custom_fields['saswp_reportagenewsarticle_organization_logo'];
                        }
                                            
                        break;
                
                        case 'ReviewNewsArticle':

                        if ( isset( $custom_fields['saswp_reviewnewsarticle_id']) ) {
                            $input1['@id'] =     get_permalink().$custom_fields['saswp_reviewnewsarticle_id'];
                        }
                        if ( isset( $custom_fields['saswp_reviewnewsarticle_main_entity_of_page']) ) {
                            $input1['mainEntityOfPage'] =    $custom_fields['saswp_reviewnewsarticle_main_entity_of_page'];
                        }
                        if ( isset( $custom_fields['saswp_reviewnewsarticle_URL']) ) {
                            $input1['url'] =    saswp_validate_url($custom_fields['saswp_reviewnewsarticle_URL']); 
                        }
                        if ( isset( $custom_fields['saswp_reviewnewsarticle_inlanguage']) ) {
                            $input1['inLanguage'] =    $custom_fields['saswp_reviewnewsarticle_inlanguage']; 
                        }
                        if ( isset( $custom_fields['saswp_reviewnewsarticle_headline']) ) {
                            $input1['headline'] =    $custom_fields['saswp_reviewnewsarticle_headline']; 
                        }
                        if ( isset( $custom_fields['saswp_reviewnewsarticle_keywords']) ) {
                            $input1['keywords'] =    $custom_fields['saswp_reviewnewsarticle_keywords']; 
                        }
                        if ( isset( $custom_fields['saswp_reviewnewsarticle_date_published']) ) {
                            $input1['datePublished'] =    $custom_fields['saswp_reviewnewsarticle_date_published']; 
                        }
                        if ( isset( $custom_fields['saswp_reviewnewsarticle_date_modified']) ) {
                            $input1['dateModified'] =    $custom_fields['saswp_reviewnewsarticle_date_modified']; 
                        }
                        if ( isset( $custom_fields['saswp_reviewnewsarticle_description']) ) {
                            $input1['description'] =    wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_reviewnewsarticle_description'] ));  
                        }
                        if ( ! empty( $custom_fields['saswp_reviewnewsarticle_haspart'] ) && is_array( $custom_fields['saswp_reviewnewsarticle_haspart'] ) ) {
                            foreach ( $custom_fields['saswp_reviewnewsarticle_haspart'] as $hp_key => $has_part) {
                                if ( ! empty( $has_part ) && is_array( $has_part ) ) {
                                    $input1['hasPart'][]       =   $has_part;    
                                }
                            } 
                        }
                        if ( ! empty( $custom_fields['saswp_reviewnewsarticle_ispartof'] ) && is_array( $custom_fields['saswp_reviewnewsarticle_ispartof'] ) ) {
                            foreach ( $custom_fields['saswp_reviewnewsarticle_ispartof'] as $ip_key => $is_part) {
                                if ( ! empty( $is_part ) && is_array( $is_part ) ) {
                                    $input1['isPartOf'][]       =   $is_part;    
                                }
                            } 
                        }
                        if ( isset( $custom_fields['saswp_reviewnewsarticle_section']) ) {
                            $input1['articleSection'] = $custom_fields['saswp_reviewnewsarticle_section'];  
                        }
                        if ( isset( $custom_fields['saswp_reviewnewsarticle_body']) ) {
                            if($custom_fields['saswp_reviewnewsarticle_body']){
                                $input1['articleBody'] =    $custom_fields['saswp_reviewnewsarticle_body'];
                            }else{
                                unset($input1['articleBody']);
                            }  
                        }
                        if ( isset( $custom_fields['saswp_reviewnewsarticle_name']) ) {
                            $input1['name'] =    $custom_fields['saswp_reviewnewsarticle_name'];  
                        }
                        if ( isset( $custom_fields['saswp_reviewnewsarticle_thumbnailurl']) ) {
                            $input1['thumbnailUrl'] =    $custom_fields['saswp_reviewnewsarticle_thumbnailurl'];  
                        }
                        if ( isset( $custom_fields['saswp_reviewnewsarticle_timerequired']) ) {
                            $input1['timeRequired'] =    $custom_fields['saswp_reviewnewsarticle_timerequired'];  
                        }
                        if ( isset( $custom_fields['saswp_reviewnewsarticle_main_entity_id']) ) {
                            $input1['mainEntity']['@id'] =    $custom_fields['saswp_reviewnewsarticle_main_entity_id'];  
                        }
    
                        if ( ! empty( $custom_fields['saswp_reviewnewsarticle_editor_type']) ) {
                            if ( ! empty( $custom_fields['saswp_reviewnewsarticle_editor_name']) && $custom_fields['saswp_reviewnewsarticle_editor_name'] != '') {
                                $input1['editor'] = array();
                                $input1['editor']['@type'] = 'Person';
                                $input1['editor']['name']  =  $custom_fields['saswp_reviewnewsarticle_editor_name'];
                            }
                            if ( ! empty( $custom_fields['saswp_reviewnewsarticle_editor_type']) ) {
                                $input1['editor']['@type'] =    $custom_fields['saswp_reviewnewsarticle_editor_type'];
                            }
                            if ( ! empty( $custom_fields['saswp_reviewnewsarticle_editor_honorific_suffix']) && $custom_fields['saswp_reviewnewsarticle_editor_honorific_suffix'] != '') {
                                $input1['editor']['honorificSuffix']  =  $custom_fields['saswp_reviewnewsarticle_editor_honorific_suffix'];
                            }  
                            if ( ! empty( $custom_fields['saswp_reviewnewsarticle_editor_description']) ) {
                                    $input1['editor']['description'] =    $custom_fields['saswp_reviewnewsarticle_editor_description'];
                            }
                            if ( ! empty( $custom_fields['saswp_reviewnewsarticle_editor_url']) ) {
                                    $input1['editor']['url'] =    $custom_fields['saswp_reviewnewsarticle_editor_url'];
                            }
                            if ( ! empty( $custom_fields['saswp_reviewnewsarticle_editor_image']) ) {
                                $input1['editor']['Image']['url'] =    $custom_fields['saswp_reviewnewsarticle_editor_image'];
                            }
                        }
    
                        if ( isset( $custom_fields['saswp_reviewnewsarticle_author_type']) ) {
                            $input1['author']['@type'] =    $custom_fields['saswp_reviewnewsarticle_author_type']; 
                        }
                        if ( isset( $custom_fields['saswp_reviewnewsarticle_author_name']) ) {
                            $input1['author']['name'] =    $custom_fields['saswp_reviewnewsarticle_author_name']; 
                        }
                        if ( isset( $custom_fields['saswp_reviewnewsarticle_author_honorific_suffix']) && $custom_fields['saswp_reviewnewsarticle_author_honorific_suffix'] != '') {
                            $input1['author']['honorificSuffix']  =  $custom_fields['saswp_reviewnewsarticle_author_honorific_suffix'];
                        }  
                        if ( isset( $custom_fields['saswp_reviewnewsarticle_author_description']) ) {
                            $input1['author']['description'] =    $custom_fields['saswp_reviewnewsarticle_author_description'];
                        }
                        if ( isset( $custom_fields['saswp_reviewnewsarticle_author_url']) ) {
                            $input1['author']['url'] =    saswp_validate_url($custom_fields['saswp_reviewnewsarticle_author_url']); 
                        }
                        if ( isset( $custom_fields['saswp_reviewnewsarticle_author_image']) ) {
                            $input1['author']['Image']['url'] =    $custom_fields['saswp_reviewnewsarticle_author_image'];  
                        }
                        if ( ! empty( $custom_fields['saswp_reviewnewsarticle_about']) && isset($custom_fields['saswp_reviewnewsarticle_about']) ) {         
                            $input1['about']['@type'] = 'Event';                   
                            $input1['about']['name'] = explode(',', $custom_fields['saswp_reviewnewsarticle_about']);    
                        }  
                        if ( isset( $custom_fields['saswp_reviewnewsarticle_organization_logo']) && isset($custom_fields['saswp_reviewnewsarticle_organization_name']) ) {
                            $input1['publisher']['@type']       =    'Organization';
                            $input1['publisher']['name']        =    $custom_fields['saswp_reviewnewsarticle_organization_name'];
                            $input1['publisher']['logo']        =    $custom_fields['saswp_reviewnewsarticle_organization_logo'];
                        }
                                            
                        break;

                case 'WebPage':
                
                    $sub_schema_type    =   '';
                    if( ! empty( $schema_post_id ) ) {
                        $sub_schema_type    =    get_post_meta( $schema_post_id, 'saswp_webpage_type', true );
                    }

                    if ( isset( $custom_fields['saswp_webpage_id']) ) {
                        $input1['@id'] =    get_permalink().$custom_fields['saswp_webpage_id'];
                    }
                    if ( isset( $custom_fields['saswp_webpage_name']) ) {
                        $input1['name'] =    $custom_fields['saswp_webpage_name'];
                    }
                    if ( isset( $custom_fields['saswp_webpage_url']) ) {
                        $input1['url'] =    saswp_validate_url($custom_fields['saswp_webpage_url']);
                    }
                    if ( isset( $custom_fields['saswp_webpage_description']) ) {
                        $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_webpage_description'] )) ;
                    }
                    if ( isset( $custom_fields['saswp_webpage_inlanguage']) ) {
                        $input1['inLanguage'] =    $custom_fields['saswp_webpage_inlanguage'];
                    }
                    if ( isset( $custom_fields['saswp_webpage_last_reviewed']) ) {
                        $input1['lastReviewed'] =    $custom_fields['saswp_webpage_last_reviewed'];
                    }
                    if ( isset( $custom_fields['saswp_webpage_date_created']) ) {
                        $input1['dateCreated'] =    $custom_fields['saswp_webpage_date_created'];
                    }
                    if ( ! empty( $custom_fields['saswp_webpage_haspart'] ) && is_array( $custom_fields['saswp_webpage_haspart'] ) ) {
                        foreach ( $custom_fields['saswp_webpage_haspart'] as $hp_key => $has_part) {
                            if ( ! empty( $has_part ) && is_array( $has_part ) ) {
                                $input1['hasPart'][]       =   $has_part;    
                            }
                        } 
                    }
                    if ( isset( $custom_fields['saswp_webpage_main_entity_of_page']) ) {
                     $input1['mainEntity']['mainEntityOfPage'] =    saswp_validate_url($custom_fields['saswp_webpage_main_entity_of_page']);
                    }
                    if ( isset( $custom_fields['saswp_webpage_image']) ) {
                     $input1['mainEntity']['image'] =    $custom_fields['saswp_webpage_image'];
                    }
                    if ( isset( $custom_fields['saswp_webpage_headline']) ) {
                     $input1['mainEntity']['headline'] =    $custom_fields['saswp_webpage_headline'];
                    }

                    if ( isset( $custom_fields['saswp_webpage_section']) ) {
                        $input1['mainEntity']['articleSection'] =    $custom_fields['saswp_webpage_section'];
                    }                                        
                    if ( isset( $custom_fields['saswp_webpage_keywords']) ) {
                        $input1['keywords']               =    $custom_fields['saswp_webpage_keywords'];
                        $input1['mainEntity']['keywords'] =    $custom_fields['saswp_webpage_keywords'];
                    }
                    
                    if ( isset( $custom_fields['saswp_webpage_date_published']) ) {
                     $input1['mainEntity']['datePublished'] =    $custom_fields['saswp_webpage_date_published'];
                    }
                    if ( isset( $custom_fields['saswp_webpage_date_modified']) ) {
                     $input1['mainEntity']['dateModified'] =    $custom_fields['saswp_webpage_date_modified'];
                    }
                    if ( isset( $custom_fields['saswp_webpage_author_type']) ) {
                        $input1['mainEntity']['author']['@type'] =    $custom_fields['saswp_webpage_author_type'];
                    }
                    if ( isset( $custom_fields['saswp_webpage_author_name']) ) {
                     $input1['mainEntity']['author']['name'] =    $custom_fields['saswp_webpage_author_name'];
                    }
                    if ( isset( $custom_fields['saswp_webpage_author_url']) ) {
                     $input1['mainEntity']['author']['url'] =    $custom_fields['saswp_webpage_author_url'];
                    }
                    
                    if ( isset( $custom_fields['saswp_webpage_organization_logo']) && isset($custom_fields['saswp_webpage_organization_name']) ) {
                        $input1['mainEntity']['publisher']['@type']              =    'Organization';
                        $input1['mainEntity']['publisher']['logo'] =    $custom_fields['saswp_webpage_organization_logo'];
                        $input1['mainEntity']['publisher']['name'] =    $custom_fields['saswp_webpage_organization_name'];
                    }
                    
                    global $post;
                    if ( ! empty( $custom_fields['saswp_webpage_reviewed_by']) ) {

                        $input1['reviewedBy'] = array();
                        $reviewed_by =  get_post_meta($post->ID, 'article_reviewed_by');

                        if ( ! empty( $reviewed_by) ) {
                            $reviewer_id = $reviewed_by[0];
                            $reviewer_data = get_user_meta ($reviewer_id);

                           
                            $input1['reviewedBy']['@type'] =   "Person";
                        }

                        if ( ! empty( $reviewer_data) ) {

                            if ( ! empty( $reviewer_data['first_name'][0]) ) {
                                $input1['reviewedBy']['name'] =    $reviewer_data['first_name'][0];
                            }

                            if ( ! empty( $reviewer_data['read_more_link'][0]) ) {
                                $input1['reviewedBy']['url'] =    $reviewer_data['read_more_link'][0];
                            }

                            if ( ! empty( $reviewer_data['author_bio'][0]) ) {
                                $input1['reviewedBy']['description'] =    $reviewer_data['author_bio'][0];
                            }

                            if ( ! empty( $reviewer_data['knowsabout'][0]) ) {
                                $input1['reviewedBy']['knowsabout'] =   explode(',',$reviewer_data['knowsabout'][0]);
                            }

                            if ( ! empty( $reviewer_data['honorificsuffix'][0]) ) {
                                $input1['reviewedBy']['honorificSuffix'] =    $reviewer_data['honorificsuffix'][0];
                            }

                            if ( ! empty( $reviewer_data['reviewer_bio'][0]) ) {
                                $input1['reviewedBy']['reviewer_bio'] =    $reviewer_data['reviewer_bio'][0];
                            }

                            $sameas = array();
                            if ( ! empty( $reviewer_data['facebook'][0]) ) {
                                $sameas[] =   $reviewer_data['facebook'][0];
                            }

                            if ( ! empty( $reviewer_data['twitter'][0]) ) {
                                $sameas[] =   $reviewer_data['twitter'][0];
                            }

                            if ( ! empty( $reviewer_data['linkedin'][0]) ) {
                                $sameas[] =   $reviewer_data['linkedin'][0];
                            }

                            if ( ! empty( $reviewer_data['instagram'][0]) ) {
                                $sameas[] =   $reviewer_data['instagram'][0];
                            }

                            if ( ! empty( $reviewer_data['youtube'][0]) ) {
                                $sameas[] =   $reviewer_data['youtube'][0];
                            }
                            if($sameas){
                                $input1['reviewedBy']['sameAs'] = $sameas;
                            }

                            if ( ! empty( $reviewer_data['alumniof'][0]) ) {
                                $str =  $reviewer_data['alumniof'][0];
                                $itemlist = explode(",", $str);
                                foreach ( $itemlist as $key => $list){
                                    $vnewarr['@type'] = 'Organization';
                                    $vnewarr['Name']   = $list;   
                                    $input1['reviewedBy']['alumniOf'][] = $vnewarr;
                                }
                            }

                            if ( ! empty( $reviewer_data['author_image'][0]) ) {
                                $author_image =  wp_get_attachment_image_src($reviewer_data['author_image'][0]);
                                if ( ! empty( $author_image) ) {
                                    $input1['reviewedBy']['image']['@type']  = 'ImageObject';
                                    $input1['reviewedBy']['image']['url']    = $author_image[0];
                                    $input1['reviewedBy']['image']['height'] = $author_image[1];
                                    $input1['reviewedBy']['image']['width']  = $author_image[2];
                                }
                            }

                        }else{

                            if ( ! empty( $custom_fields['saswp_webpage_reviewed_by']) ) {
                                $input1['reviewedBy']['@type'] =   "Person";
                                $input1['reviewedBy']['name'] =    $custom_fields['saswp_webpage_reviewed_by'];
                            }
                            if ( ! empty( $custom_fields['saswp_webpage_reviewed_by']['custom_fields']) ) {
                                $fields_name = $custom_fields['saswp_webpage_reviewed_by']['custom_fields'];
                            }else{
                                $fields_name = "";
                            }
                        
                            if ( ! empty( $custom_fields['saswp_webpage_reviewed_by']['name']) ) {
                                $input1['reviewedBy']['name'] =    $custom_fields['saswp_webpage_reviewed_by']['name'];
                            }
                            
                            if ( ! empty( $custom_fields['saswp_webpage_reviewed_by']['url']) ) {
                                $input1['reviewedBy']['url'] =    $custom_fields['saswp_webpage_reviewed_by']['url'];
                            }
                        
                            if ( ! empty( $custom_fields['saswp_webpage_reviewed_by']['description']) ) {
                                $input1['reviewedBy']['description'] =    $custom_fields['saswp_webpage_reviewed_by']['description'];
                            }
                        
                            if ( ! empty( $fields_name['honorificsuffix'][0]) ) {
                                $input1['reviewedBy']['honorificSuffix'] =    $fields_name['honorificsuffix'][0];
                            }
                        
                            if ( ! empty( $fields_name['knowsabout'][0]) ) {
                                $input1['reviewedBy']['knowsAbout'] =   explode(',',$fields_name['knowsabout'][0]);
                            }

                            if ( ! empty( $fields_name['reviewer_bio'][0]) ) {
                                $input1['reviewedBy']['description'] =    $fields_name['reviewer_bio'][0];
                            }
                        
                            $sameas = array();
                            if ( ! empty( $fields_name['team_facebook'][0]) ) {
                                $sameas[] =   $fields_name['team_facebook'][0];
                            }
                        
                            if ( ! empty( $fields_name['team_twitter'][0]) ) {
                                $sameas[] =   $fields_name['team_twitter'][0];
                            }
                        
                            if ( ! empty( $fields_name['team_linkedin'][0]) ) {
                                $sameas[] =   $fields_name['team_linkedin'][0];
                            }
                        
                            if ( ! empty( $fields_name['team_instagram'][0]) ) {
                                $sameas[] =   $fields_name['team_instagram'][0];
                            }
                        
                            if ( ! empty( $fields_name['team_youtube'][0]) ) {
                                $sameas[] =   $fields_name['team_youtube'][0];
                            }
                            if($sameas){
                                $input1['reviewedBy']['sameAs'] = $sameas;
                            }
                        
                            if ( ! empty( $fields_name['reviewer_image']) ) {
                                $input1['reviewedBy']['image']  = $fields_name['reviewer_image'];
                            }
                        
                            if ( ! empty( $fields_name['alumniof'][0]) ) {
                                $str =  $fields_name['alumniof'][0];
                                $itemlist = explode(",", $str);
                                foreach ( $itemlist as $key => $list){
                                    $vnewarr['@type'] = 'Organization';
                                    $vnewarr['Name']   = $list;   
                                    $input1['reviewedBy']['alumniOf'][] = $vnewarr;
                                }
                            }
                        }

                    }

                    if( isset( $custom_fields['saswp_webpage_reviewed_by'] ) && empty( $custom_fields['saswp_webpage_reviewed_by'] ) ) {
                        unset( $input1['reviewedBy'] );
                    }

                    if( $sub_schema_type == 'none' ) { 
                        unset( $input1['mainEntity'] );
                    }

                    break;

                    case 'ItemPage':
                        if ( isset( $custom_fields['saswp_itempage_id']) ) {
                            $input1['@id'] =    get_permalink().$custom_fields['saswp_itempage_id'];
                        }
                        if ( isset( $custom_fields['saswp_itempage_name']) ) {
                            $input1['name'] =    $custom_fields['saswp_itempage_name'];
                        }
                        if ( isset( $custom_fields['saswp_itempage_url']) ) {
                            $input1['url'] =    saswp_validate_url($custom_fields['saswp_itempage_url']);
                        }
                        if ( isset( $custom_fields['saswp_itempage_description']) ) {
                            $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_itempage_description'] )) ;
                        }
                        if ( isset( $custom_fields['saswp_itempage_inlanguage']) ) {
                            $input1['inLanguage'] =    $custom_fields['saswp_itempage_inlanguage'];
                        }
                        if ( isset( $custom_fields['saswp_itempage_last_reviewed']) ) {
                            $input1['lastReviewed'] =    $custom_fields['saswp_itempage_last_reviewed'];
                        }
                        if ( isset( $custom_fields['saswp_itempage_date_created']) ) {
                            $input1['dateCreated'] =    $custom_fields['saswp_itempage_date_created'];
                        }
                        
                        if ( isset( $custom_fields['saswp_itempage_main_entity_of_page']) ) {
                         $input1['mainEntity']['mainEntityOfPage'] =    saswp_validate_url($custom_fields['saswp_itempage_main_entity_of_page']);
                        }
                        if ( isset( $custom_fields['saswp_itempage_image']) ) {
                         $input1['mainEntity']['image'] =    $custom_fields['saswp_itempage_image'];
                        }
                        if ( isset( $custom_fields['saswp_itempage_headline']) ) {
                         $input1['mainEntity']['headline'] =    $custom_fields['saswp_itempage_headline'];
                        }
    
                        if ( isset( $custom_fields['saswp_itempage_section']) ) {
                            $input1['mainEntity']['articleSection'] =    $custom_fields['saswp_itempage_section'];
                        }                                        
                        if ( isset( $custom_fields['saswp_itempage_keywords']) ) {
                            $input1['mainEntity']['keywords'] =    $custom_fields['saswp_itempage_keywords'];
                        }
                        
                        if ( isset( $custom_fields['saswp_itempage_date_published']) ) {
                         $input1['mainEntity']['datePublished'] =    $custom_fields['saswp_itempage_date_published'];
                        }
                        if ( isset( $custom_fields['saswp_itempage_date_modified']) ) {
                         $input1['mainEntity']['dateModified'] =    $custom_fields['saswp_itempage_date_modified'];
                        }
                        if ( isset( $custom_fields['saswp_itempage_author_type']) ) {
                            $input1['mainEntity']['author']['@type'] =    $custom_fields['saswp_itempage_author_type'];
                        }
                        if ( isset( $custom_fields['saswp_itempage_author_name']) ) {
                         $input1['mainEntity']['author']['name'] =    $custom_fields['saswp_itempage_author_name'];
                        }
                        if ( isset( $custom_fields['saswp_itempage_author_url']) ) {
                         $input1['mainEntity']['author']['url'] =    $custom_fields['saswp_itempage_author_url'];
                        }
                        
                        if ( isset( $custom_fields['saswp_itempage_organization_logo']) && isset($custom_fields['saswp_itempage_organization_name']) ) {
                            $input1['mainEntity']['publisher']['@type']              =    'Organization';
                            $input1['mainEntity']['publisher']['logo'] =    $custom_fields['saswp_itempage_organization_logo'];
                            $input1['mainEntity']['publisher']['name'] =    $custom_fields['saswp_itempage_organization_name'];
                        }
    
                        if ( ! empty( $custom_fields['saswp_itempage_reviewed_by']) ) {
                            $input1['reviewedBy'] = array();
                            if ( ! empty( $custom_fields['saswp_itempage_reviewed_by']) ) {
                                $input1['reviewedBy']['@type'] =   "Person";
                            }
                            if ( ! empty( $custom_fields['saswp_itempage_reviewed_by']['custom_fields']) ) {
                                $fields_name = $custom_fields['saswp_itempage_reviewed_by']['custom_fields'];
                            }else{
                                $fields_name = "";
                            }
                        
                            if ( ! empty( $custom_fields['saswp_itempage_reviewed_by']['name']) ) {
                                $input1['reviewedBy']['name'] =    $custom_fields['saswp_itempage_reviewed_by']['name'];
                            }
                        
                            if ( ! empty( $custom_fields['saswp_itempage_reviewed_by']['url']) ) {
                                $input1['url'] =    $custom_fields['saswp_itempage_reviewed_by']['url'];
                            }
                        
                            if ( ! empty( $custom_fields['saswp_itempage_reviewed_by']['description']) ) {
                                $input1['description'] =    $custom_fields['saswp_itempage_reviewed_by']['description'];
                            }
                        
                            if ( ! empty( $fields_name['honorificsuffix'][0]) ) {
                                $input1['reviewedBy']['honorificSuffix'] =    $fields_name['honorificsuffix'][0];
                            }
                        
                            if ( ! empty( $fields_name['knowsabout'][0]) ) {
                                $input1['reviewedBy']['knowsAbout'] =   explode(',',$fields_name['knowsabout'][0]);
                            }
    
                            if ( ! empty( $fields_name['reviewer_bio'][0]) ) {
                                $input1['reviewedBy']['description'] =    $fields_name['reviewer_bio'][0];
                            }
                        
                            $sameas = array();
                            if ( ! empty( $fields_name['team_facebook'][0]) ) {
                                $sameas[] =   $fields_name['team_facebook'][0];
                            }
                        
                            if ( ! empty( $fields_name['team_twitter'][0]) ) {
                                $sameas[] =   $fields_name['team_twitter'][0];
                            }
                        
                            if ( ! empty( $fields_name['team_linkedin'][0]) ) {
                                $sameas[] =   $fields_name['team_linkedin'][0];
                            }
                        
                            if ( ! empty( $fields_name['team_instagram'][0]) ) {
                                $sameas[] =   $fields_name['team_instagram'][0];
                            }
                        
                            if ( ! empty( $fields_name['team_youtube'][0]) ) {
                                $sameas[] =   $fields_name['team_youtube'][0];
                            }
                            if($sameas){
                                $input1['reviewedBy']['sameAs'] = $sameas;
                            }
                        
                            if ( ! empty( $fields_name['reviewer_image']) ) {
                                $input1['reviewedBy']['image']  = $fields_name['reviewer_image'];
                            }
                        
                            if ( ! empty( $fields_name['alumniof'][0]) ) {
                                $str =  $fields_name['alumniof'][0];
                                $itemlist = explode(",", $str);
                                foreach ( $itemlist as $key => $list){
                                    $vnewarr['@type'] = 'Organization';
                                    $vnewarr['Name']   = $list;   
                                    $input1['reviewedBy']['alumniOf'][] = $vnewarr;
                                }
                            }
                           
                        }
                        
                        break;
                
            case 'MedicalWebPage':
            
                if ( isset( $custom_fields['saswp_medicalwebpage_name']) ) {
                    $input1['name'] =    $custom_fields['saswp_medicalwebpage_name'];
                }
                if ( isset( $custom_fields['saswp_medicalwebpage_url']) ) {
                    $input1['url'] =    saswp_validate_url($custom_fields['saswp_medicalwebpage_url']);
                }
                if ( isset( $custom_fields['saswp_medicalwebpage_description']) ) {
                    $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_medicalwebpage_description'] )) ;
                }

                if ( isset( $custom_fields['saswp_medicalwebpage_reviewed_by']) ) {
                    $input1['reviewedBy'] =    $custom_fields['saswp_medicalwebpage_reviewed_by'];
                }

                if ( isset( $custom_fields['saswp_medicalwebpage_last_reviewed']) ) {
                    $input1['lastReviewed'] =    $custom_fields['saswp_medicalwebpage_last_reviewed'];
                }
                if ( isset( $custom_fields['saswp_medicalwebpage_date_created']) ) {
                    $input1['dateCreated'] =    $custom_fields['saswp_medicalwebpage_date_created'];
                }
                
                if ( isset( $custom_fields['saswp_medicalwebpage_main_entity_of_page']) ) {
                    $input1['mainEntity']['mainEntityOfPage'] =    saswp_validate_url($custom_fields['saswp_medicalwebpage_main_entity_of_page']);
                }
                if ( isset( $custom_fields['saswp_medicalwebpage_image']) ) {
                    $input1['mainEntity']['image'] =    $custom_fields['saswp_medicalwebpage_image'];
                }
                if ( isset( $custom_fields['saswp_medicalwebpage_headline']) ) {
                    $input1['mainEntity']['headline'] =    $custom_fields['saswp_medicalwebpage_headline'];
                }

                if ( isset( $custom_fields['saswp_medicalwebpage_section']) ) {
                    $input1['mainEntity']['articleSection'] =    $custom_fields['saswp_medicalwebpage_section'];
                }                                        
                if ( isset( $custom_fields['saswp_medicalwebpage_keywords']) ) {
                    $input1['mainEntity']['keywords'] =    $custom_fields['saswp_medicalwebpage_keywords'];
                }
                
                if ( isset( $custom_fields['saswp_medicalwebpage_date_published']) ) {
                    $input1['mainEntity']['datePublished'] =    $custom_fields['saswp_medicalwebpage_date_published'];
                }
                if ( isset( $custom_fields['saswp_medicalwebpage_date_modified']) ) {
                    $input1['mainEntity']['dateModified'] =    $custom_fields['saswp_medicalwebpage_date_modified'];
                }
                if ( isset( $custom_fields['saswp_medicalwebpage_author_type']) ) {
                    $input1['mainEntity']['author']['@type'] =    $custom_fields['saswp_medicalwebpage_author_type'];
                }
                if ( isset( $custom_fields['saswp_medicalwebpage_author_name']) ) {
                    $input1['mainEntity']['author']['name'] =    $custom_fields['saswp_medicalwebpage_author_name'];
                }
                if ( isset( $custom_fields['saswp_medicalwebpage_author_url']) ) {
                    $input1['mainEntity']['author']['url'] =    $custom_fields['saswp_medicalwebpage_author_url'];
                }
                
                if ( isset( $custom_fields['saswp_medicalwebpage_organization_logo']) && isset($custom_fields['saswp_medicalwebpage_organization_name']) ) {
                    $input1['mainEntity']['publisher']['@type']              =    'Organization';
                    $input1['mainEntity']['publisher']['logo'] =    $custom_fields['saswp_medicalwebpage_organization_logo'];
                    $input1['mainEntity']['publisher']['name'] =    $custom_fields['saswp_medicalwebpage_organization_name'];
                }
                
                break;
                                                    
                case 'Event':      
                    
                    $phy_location = array();
                    $vir_location = array();
                    
                    if ( isset( $custom_fields['saswp_event_schema_id']) ) {
                        $input1['@id'] =    get_permalink().$custom_fields['saswp_event_schema_id'];
                    }
                    if ( isset( $custom_fields['saswp_event_schema_name']) ) {
                     $input1['name'] =    $custom_fields['saswp_event_schema_name'];
                    }
                    if ( isset( $custom_fields['saswp_event_schema_description']) ) {
                     $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_event_schema_description'] )) ;
                    }
                                       
                    if ( isset( $custom_fields['saswp_event_schema_location_name']) || isset($custom_fields['saswp_event_schema_location_streetaddress']) ) {
                        
                        $phy_location['@type'] = 'Place';   
                        $phy_location['name']  =    $custom_fields['saswp_event_schema_location_name'];

                            if ( isset( $custom_fields['saswp_event_schema_location_streetaddress']) ) {
                                $phy_location['address']['streetAddress'] =    $custom_fields['saswp_event_schema_location_streetaddress'];
                            }                                          
                            if ( isset( $custom_fields['saswp_event_schema_location_locality']) ) {
                                $phy_location['address']['addressLocality'] =    $custom_fields['saswp_event_schema_location_locality'];
                            }
                            if ( isset( $custom_fields['saswp_event_schema_location_region']) ) {
                                $phy_location['address']['addressRegion'] =    $custom_fields['saswp_event_schema_location_region'];
                            }                    
                            if ( isset( $custom_fields['saswp_event_schema_location_postalcode']) ) {
                                $phy_location['address']['postalCode'] =    $custom_fields['saswp_event_schema_location_postalcode'];
                            }
                            if ( isset( $custom_fields['saswp_event_schema_location_country']) ) {
                                $phy_location['address']['addressCountry'] =    $custom_fields['saswp_event_schema_location_country'];
                            }
                            if ( isset( $custom_fields['saswp_event_schema_location_hasmap']) ) {
                             $phy_location['hasMap']  =  $custom_fields['saswp_event_schema_location_hasmap'];
                            }
                    }
                    if ( isset( $custom_fields['saswp_event_schema_virtual_location_name']) || isset($custom_fields['saswp_event_schema_virtual_location_url']) ) {
                            $vir_location['@type'] = 'VirtualLocation';
                            $reviews_arr['name']   = $custom_fields['saswp_event_schema_virtual_location_name'];
                            $vir_location['url']   = $custom_fields['saswp_event_schema_virtual_location_url'];
                    }                                        
                    if($vir_location || $phy_location){
                        $input1['location'] = array($vir_location, $phy_location);
                    }                    

                    if ( isset( $custom_fields['saswp_event_schema_status']) ) {
                        $input1['eventStatus'] = $custom_fields['saswp_event_schema_status'];
                    }
                    if( isset($custom_fields['saswp_event_schema_attendance_mode']) ){
                        $input1['eventAttendanceMode'] = $custom_fields['saswp_event_schema_attendance_mode'];
                    }

                    if ( isset( $custom_fields['saswp_event_schema_previous_start_date']) ) {
                     
                        $time = '';
                        
                        if ( isset( $custom_fields['saswp_event_schema_previous_start_time']) ) {
                            
                           $time =  $custom_fields['saswp_event_schema_previous_start_time'];
                           
                        }
                        
                        $input1['previousStartDate'] =    saswp_format_date_time($custom_fields['saswp_event_schema_previous_start_date'], $time);
                        
                    }

                    if ( isset( $custom_fields['saswp_event_schema_start_date']) ) {
                     
                     $time = '';
                     
                     if ( isset( $custom_fields['saswp_event_schema_start_time']) ) {
                         
                        $time =  $custom_fields['saswp_event_schema_start_time'];
                        
                     }
                     
                     $input1['startDate'] =    saswp_format_date_time($custom_fields['saswp_event_schema_start_date'], $time);
                     
                    }
                    
                    if ( isset( $custom_fields['saswp_event_schema_end_date']) ) {
                     
                     $time = '';
                     
                     if ( isset( $custom_fields['saswp_event_schema_end_time']) ) {
                         
                        $time =  $custom_fields['saswp_event_schema_end_time'];
                        
                     }
                     
                     $input1['endDate'] =    saswp_format_date_time($custom_fields['saswp_event_schema_end_date'], $time);
                     
                    }
                    
                    if ( isset( $custom_fields['saswp_event_schema_image']) ) {
                        $input1['image'] =    $custom_fields['saswp_event_schema_image'];
                    }
                    if ( isset( $custom_fields['saswp_event_schema_performer_name']) ) {
                        $input1['performer']['name'] =    $custom_fields['saswp_event_schema_performer_name'];
                    }

                    if ( isset( $custom_fields['saswp_event_schema_price']) ) {
                        $input1['offers']['@type'] =   'Offer';
                        $input1['offers']['price'] =    $custom_fields['saswp_event_schema_price'];
                    }
                    
                    if ( isset( $custom_fields['saswp_event_schema_high_price']) && isset($custom_fields['saswp_event_schema_low_price']) ) {

                        $input1['offers']['@type'] = 'AggregateOffer';

                        $input1['offers']['highPrice'] = $custom_fields['saswp_event_schema_high_price'];
                        $input1['offers']['lowPrice']  = $custom_fields['saswp_event_schema_low_price'];

                    }
                                        
                    if ( isset( $custom_fields['saswp_event_schema_price_currency']) ) {
                     $input1['offers']['priceCurrency'] =    $custom_fields['saswp_event_schema_price_currency'];
                    }
                    if ( isset( $custom_fields['saswp_event_schema_availability']) ) {
                     $input1['offers']['availability'] =    $custom_fields['saswp_event_schema_availability'];
                    }
                    if ( isset( $custom_fields['saswp_event_schema_validfrom']) ) {
                     $input1['offers']['validFrom'] =    $custom_fields['saswp_event_schema_validfrom'];
                    }
                    if ( isset( $custom_fields['saswp_event_schema_url']) ) {
                     $input1['offers']['url'] =    $custom_fields['saswp_event_schema_url'];
                    }

                    if ( isset( $custom_fields['saswp_event_schema_organizer_name']) || isset($custom_fields['saswp_event_schema_organizer_url']) || isset($custom_fields['saswp_event_schema_organizer_email']) || isset($custom_fields['saswp_event_schema_organizer_phone']) ) {
                        
                        $input1['organizer']['@type'] =    'Organization';

                        if ( isset( $custom_fields['saswp_event_schema_organizer_name']) ) {
                            $input1['organizer']['name']  =    $custom_fields['saswp_event_schema_organizer_name'];
                        }
                        if ( isset( $custom_fields['saswp_event_schema_organizer_url']) ) {
                            $input1['organizer']['url']  =    $custom_fields['saswp_event_schema_organizer_url'];
                        }
                        if ( isset( $custom_fields['saswp_event_schema_organizer_email']) ) {
                            $input1['organizer']['email']  =    $custom_fields['saswp_event_schema_organizer_email'];
                        }
                        if ( isset( $custom_fields['saswp_event_schema_organizer_phone']) ) {
                            $input1['organizer']['telephone']  =    $custom_fields['saswp_event_schema_organizer_phone'];
                        }                        
                    }
                                        
                    break;    
                    
                case 'TechArticle':     
                    
                    if ( isset( $custom_fields['saswp_tech_article_id']) ) {
                        $input1['@id'] =    get_permalink().$custom_fields['saswp_tech_article_id'];
                       }                      
                    if ( isset( $custom_fields['saswp_tech_article_main_entity_of_page']) ) {
                     $input1['mainEntityOfPage'] =    $custom_fields['saswp_tech_article_main_entity_of_page'];
                    }
                    if ( isset( $custom_fields['saswp_tech_article_image']) ) {
                     $input1['image'] =    $custom_fields['saswp_tech_article_image'];
                    }
                    if ( isset( $custom_fields['saswp_tech_article_url']) ) {
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_tech_article_url']);
                    }
                    if ( isset( $custom_fields['saswp_tech_article_body']) ) {
                        if($custom_fields['saswp_tech_article_body']){
                            $input1['articleBody'] =    $custom_fields['saswp_tech_article_body'];
                        }else{
                            unset($input1['articleBody']);
                        }
                    }
                    if ( isset( $custom_fields['saswp_tech_article_keywords']) ) {
                     $input1['keywords'] =    $custom_fields['saswp_tech_article_keywords'];
                    }
                    if ( isset( $custom_fields['saswp_tech_article_section']) ) {
                     $input1['articleSection'] =    $custom_fields['saswp_tech_article_section'];
                    }
                    if ( isset( $custom_fields['saswp_tech_article_inlanguage']) ) {
                        $input1['inLanguage'] =    $custom_fields['saswp_tech_article_inlanguage'];
                    }
                    if ( isset( $custom_fields['saswp_tech_article_headline']) ) {
                     $input1['headline'] =    $custom_fields['saswp_tech_article_headline'];
                    }
                    
                    if ( isset( $custom_fields['saswp_tech_article_description']) ) {
                     $input1['description'] =    wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_tech_article_description'] ));
                    }
                    if ( ! empty( $custom_fields['saswp_tech_article_haspart'] ) && is_array( $custom_fields['saswp_tech_article_haspart'] ) ) {
                        foreach ( $custom_fields['saswp_tech_article_haspart'] as $hp_key => $has_part) {
                            if ( ! empty( $has_part ) && is_array( $has_part ) ) {
                                $input1['hasPart'][]       =   $has_part;    
                            }
                        } 
                    }
                    if ( ! empty( $custom_fields['saswp_tech_article_ispartof'] ) && is_array( $custom_fields['saswp_tech_article_ispartof'] ) ) {
                        foreach ( $custom_fields['saswp_tech_article_ispartof'] as $ip_key => $is_part) {
                            if ( ! empty( $is_part ) && is_array( $is_part ) ) {
                                $input1['isPartOf'][]       =   $is_part;    
                            }
                        } 
                    }
                    if ( isset( $custom_fields['saswp_tech_article_date_published']) ) {
                     $input1['datePublished'] =    $custom_fields['saswp_tech_article_date_published'];
                    }
                    if ( isset( $custom_fields['saswp_tech_article_date_modified']) ) {
                     $input1['dateModified'] =    $custom_fields['saswp_tech_article_date_modified'];
                    }
                    
                    if ( ! empty( $custom_fields['saswp_tech_article_author_global_mapping']) ) {
                       
                        if ( ! empty( $custom_fields['saswp_tech_article_author_global_mapping']) ) {
                            $input1['author']['@type'] =   "Person";
                        }

                        if ( ! empty( $custom_fields['saswp_tech_article_author_global_mapping']['name']) ) {
                            $input1['author']['name'] =    $custom_fields['saswp_tech_article_author_global_mapping']['name'];
                        }

                        if ( ! empty( $custom_fields['saswp_tech_article_author_global_mapping']['url']) ) {
                            $input1['author']['url'] =    $custom_fields['saswp_tech_article_author_global_mapping']['url'];
                        }

                        if ( ! empty( $custom_fields['saswp_tech_article_author_global_mapping']['description']) ) {
                            $input1['author']['description'] =    $custom_fields['saswp_tech_article_author_global_mapping']['description'];
                        }

                        if ( ! empty( $custom_fields['saswp_tech_article_author_global_mapping']['custom_fields']['honorificsuffix'][0]) ) {
                            $input1['author']['honorificSuffix'] =    $custom_fields['saswp_tech_article_author_global_mapping']['custom_fields']['honorificsuffix'][0];
                        }

                        if ( ! empty( $custom_fields['saswp_tech_article_author_global_mapping']['custom_fields']['knowsabout'][0]) ) {
                            $input1['author']['knowsAbout'] =   explode(',', $custom_fields['saswp_tech_article_author_global_mapping']['custom_fields']['knowsabout'][0]);
                        }   

                        $sameas = array();
                        if ( ! empty( $custom_fields['saswp_tech_article_author_global_mapping']['custom_fields']['team_facebook'][0]) ) {
                            $sameas[] =   $custom_fields['saswp_tech_article_author_global_mapping']['custom_fields']['team_facebook'][0];
                        }

                        if ( ! empty( $custom_fields['saswp_tech_article_author_global_mapping']['custom_fields']['team_twitter'][0]) ) {
                            $sameas[] =   $custom_fields['saswp_tech_article_author_global_mapping']['custom_fields']['team_twitter'][0];
                        }

                        if ( ! empty( $custom_fields['saswp_tech_article_author_global_mapping']['custom_fields']['team_linkedin'][0]) ) {
                            $sameas[] =   $custom_fields['saswp_tech_article_author_global_mapping']['custom_fields']['team_linkedin'][0];
                        }

                        if ( ! empty( $custom_fields['saswp_tech_article_author_global_mapping']['custom_fields']['team_instagram'][0]) ) {
                            $sameas[] =   $custom_fields['saswp_tech_article_author_global_mapping']['custom_fields']['team_instagram'][0];
                        }

                        if ( ! empty( $custom_fields['saswp_tech_article_author_global_mapping']['custom_fields']['team_youtube'][0]) ) {
                            $sameas[] =   $custom_fields['saswp_tech_article_author_global_mapping']['custom_fields']['team_youtube'][0];
                        }
                        if($sameas){
                            $input1['author']['sameAs'] = $sameas;
                        }
                        if ( ! empty( $custom_fields['saswp_tech_article_author_global_mapping']['custom_fields']['alumniof'][0]) ) {
                            $str =  $custom_fields['saswp_tech_article_author_global_mapping']['custom_fields']['alumniof'][0];
                            $itemlist = explode(",", $str);
                            foreach ( $itemlist as $key => $list){
                                $vnewarr['@type'] = 'Organization';
                                $vnewarr['Name']   = $list;   
                                $input1['author']['alumniOf'][] = $vnewarr;
                            }
                        }
                    
                    }else{

                        if ( isset( $custom_fields['saswp_tech_article_author_type']) ) {
                            $input1['author']['@type'] =    $custom_fields['saswp_tech_article_author_type'];
                        }
                        if ( isset( $custom_fields['saswp_tech_article_author_name']) ) {
                        $input1['author']['name'] =    $custom_fields['saswp_tech_article_author_name'];
                        }
                        if ( isset( $custom_fields['saswp_tech_article_author_honorific_suffix']) ) {
                            $input1['author']['honorificSuffix'] =    $custom_fields['saswp_tech_article_author_honorific_suffix'];
                        }
                        if ( isset( $custom_fields['saswp_tech_article_author_url']) ) {
                        $input1['author']['url'] =    saswp_validate_url($custom_fields['saswp_tech_article_author_url']);
                        }
                        if ( isset( $custom_fields['saswp_tech_article_author_description']) ) {
                        $input1['author']['description'] =    $custom_fields['saswp_tech_article_author_description'];
                        }
                        if ( isset( $custom_fields['saswp_tech_article_author_jobtitle']) ) {
                            $input1['author']['JobTitle'] =    $custom_fields['saswp_tech_article_author_jobtitle'];
                        }
                        if ( isset( $custom_fields['saswp_tech_article_author_image']) ) {
                            $input1['author']['Image']['url'] =    $custom_fields['saswp_tech_article_author_image'];  
                        }
                        if ( isset( $custom_fields['saswp_tech_article_author_social_profile']) && !empty($custom_fields['saswp_tech_article_author_social_profile']) ) {
                            $explode_sp = explode(',', $custom_fields['saswp_tech_article_author_social_profile']);
                            if ( is_array( $explode_sp) ) {
                                $input1['author']['sameAs'] =    $explode_sp;
                            }
                        }
                    }

                    if ( isset( $custom_fields['saswp_tech_article_editor_name']) && $custom_fields['saswp_tech_article_editor_name'] != '') {
                        $input1['editor'] = array();
                        $input1['editor']['@type'] = 'Person';
                        $input1['editor']['name']  =  $custom_fields['saswp_tech_article_editor_name'];
                    }
                    if ( isset( $custom_fields['saswp_tech_article_editor_type']) ) {
                        $input1['editor']['@type'] =    $custom_fields['saswp_tech_article_editor_type'];
                    }
                    if ( isset( $custom_fields['saswp_tech_article_editor_honorific_suffix']) && $custom_fields['saswp_tech_article_editor_honorific_suffix'] != '') {
                        $input1['editor']['honorificSuffix']  =  $custom_fields['saswp_tech_article_editor_honorific_suffix'];
                    }
                    if ( isset( $custom_fields['saswp_tech_article_editor_url']) ) {
                        $input1['editor']['url'] =    saswp_validate_url($custom_fields['saswp_tech_article_editor_url']);
                    }
                    
                    if ( isset( $custom_fields['saswp_tech_article_editor_description']) ) {
                        $input1['editor']['description'] =    $custom_fields['saswp_tech_article_editor_description'];
                    }
                    if ( isset( $custom_fields['saswp_tech_article_editor_image']) ) {
                        $input1['editor']['Image']['url'] =    $custom_fields['saswp_tech_article_editor_image'];  
                    }
                    if ( isset( $custom_fields['saswp_tech_article_author_social_profile']) && !empty($custom_fields['saswp_tech_article_author_social_profile']) ) {
                        $explode_sp = explode(',', $custom_fields['saswp_tech_article_author_social_profile']);
                        if ( is_array( $explode_sp) ) {
                            $input1['editor']['sameAs'] =    $explode_sp;
                        }
                    }
                    if ( isset( $custom_fields['saswp_tech_article_organization_logo']) && isset($custom_fields['saswp_tech_article_organization_name']) ) {
                     $input1['publisher']['@type']       =    'Organization';
                     $input1['publisher']['name']        =    $custom_fields['saswp_tech_article_organization_name'];
                     $input1['publisher']['logo']        =    $custom_fields['saswp_tech_article_organization_logo'];
                    }
                   
                    if ( ! empty( $custom_fields['saswp_tech_article_about']) && isset($custom_fields['saswp_tech_article_about']) ) {         
                        $input1['about']['@type'] = 'Event';                   
                        $input1['about']['name'] = explode(',', $custom_fields['saswp_tech_article_about']);    
                    } 
                    
                    if ( ! empty( $custom_fields['saswp_tech_article_reviewedby_global_mapping']) ) {
                           
                        if ( ! empty( $custom_fields['saswp_tech_article_reviewedby_global_mapping']) ) {
                            $input1['reviewedBy']['@type'] =   "Person";
                        }

                        if ( ! empty( $custom_fields['saswp_tech_article_reviewedby_global_mapping']['name']) ) {
                            $input1['reviewedBy']['name'] =    $custom_fields['saswp_tech_article_reviewedby_global_mapping']['name'];
                        }

                        if ( ! empty( $custom_fields['saswp_tech_article_reviewedby_global_mapping']['url']) ) {
                            $input1['reviewedBy']['url'] =    $custom_fields['saswp_tech_article_reviewedby_global_mapping']['url'];
                        }
                        if ( ! empty( $custom_fields['saswp_tech_article_reviewedby_global_mapping']['description']) ) {
                            $input1['reviewedBy']['description'] =    $custom_fields['saswp_tech_article_reviewedby_global_mapping']['description'];
                        }

                        if ( ! empty( $custom_fields['saswp_tech_article_reviewedby_global_mapping']['honorificsuffix']['custom_fields'][0]) ) {
                            $input1['reviewedBy']['honorificSuffix'] =    $custom_fields['saswp_tech_article_reviewedby_global_mapping']['custom_fields']['honorificsuffix'][0];
                        }

                        if ( ! empty( $custom_fields['saswp_tech_article_reviewedby_global_mapping']['custom_fields']['knowsabout'][0]) ) {
                            $input1['reviewedBy']['knowsAbout'] =   explode(',', $custom_fields['saswp_tech_article_reviewedby_global_mapping']['custom_fields']['knowsabout'][0]);
                        }

                        $sameas = array();
                        if ( ! empty( $custom_fields['saswp_tech_article_reviewedby_global_mapping']['custom_fields']['team_facebook'][0]) ) {
                            $sameas[] =   $custom_fields['saswp_tech_article_reviewedby_global_mapping']['custom_fields']['team_facebook'][0];
                        }

                        if ( ! empty( $custom_fields['saswp_tech_article_reviewedby_global_mapping']['custom_fields']['team_twitter'][0]) ) {
                            $sameas[] =   $custom_fields['saswp_tech_article_reviewedby_global_mapping']['custom_fields']['team_twitter'][0];
                        }

                        if ( ! empty( $custom_fields['saswp_tech_article_reviewedby_global_mapping']['custom_fields']['team_linkedin'][0]) ) {
                            $sameas[] =   $custom_fields['saswp_tech_article_reviewedby_global_mapping']['custom_fields']['team_linkedin'][0];
                        }

                        if ( ! empty( $custom_fields['saswp_tech_article_reviewedby_global_mapping']['custom_fields']['team_instagram'][0]) ) {
                            $sameas[] =   $custom_fields['saswp_tech_article_reviewedby_global_mapping']['custom_fields']['team_instagram'][0];
                        }

                        if ( ! empty( $custom_fields['saswp_tech_article_reviewedby_global_mapping']['custom_fields']['team_youtube'][0]) ) {
                            $sameas[] =   $custom_fields['saswp_tech_article_reviewedby_global_mapping']['custom_fields']['team_youtube'][0];
                        }
                        if($sameas){
                            $input1['reviewedBy']['sameAs'] = $sameas;
                        }

                        if ( ! empty( $custom_fields['saswp_tech_article_reviewedby_global_mapping']['custom_fields']['reviewer_image']) ) {
                            $input1['reviewedBy']['image']  = $custom_fields['saswp_tech_article_reviewedby_global_mapping']['custom_fields']['reviewer_image'];
                        }

                        if ( ! empty( $custom_fields['saswp_tech_article_reviewedby_global_mapping']['custom_fields']['alumniof'][0]) ) {
                            $str =  $custom_fields['saswp_tech_article_reviewedby_global_mapping']['custom_fields']['alumniof'][0];
                            $itemlist = explode(",", $str);
                            foreach ( $itemlist as $key => $list){
                                $vnewarr['@type'] = 'Organization';
                                $vnewarr['Name']   = $list;   
                                $input1['reviewedBy']['alumniOf'][] = $vnewarr;
                            }
                        }

                    }else{

                        if ( isset( $custom_fields['saswp_tech_article_knowsabout']) ) {                            
                            $input1['knowsAbout'] = explode(',', $custom_fields['saswp_tech_article_knowsabout']);    
                        }
                        if ( isset( $custom_fields['saswp_tech_article_reviewedby_type']) ) {
                            $input1['reviewedBy']['@type'] =    $custom_fields['saswp_tech_article_reviewedby_type'];
                        }
                        if ( isset( $custom_fields['saswp_tech_article_reviewedby_name']) ) {
                        $input1['reviewedBy']['name'] =    $custom_fields['saswp_tech_article_reviewedby_name'];
                        }
                        if ( isset( $custom_fields['saswp_tech_article_reviewedby_honorific_suffix']) ) {
                            $input1['reviewedBy']['honorificSuffix'] =    $custom_fields['saswp_tech_article_reviewedby_honorific_suffix'];
                        }
                        if ( isset( $custom_fields['saswp_tech_article_reviewedby_url']) ) {
                        $input1['reviewedBy']['url'] =    saswp_validate_url($custom_fields['saswp_tech_article_reviewedby_url']);
                        }
                        if ( isset( $custom_fields['saswp_tech_article_reviewedby_description']) ) {
                        $input1['reviewedBy']['description'] =    $custom_fields['saswp_tech_article_reviewedby_description'];
                        }
                        if ( isset( $custom_fields['saswp_tech_article_alumniof']) ) {
                            $str = $custom_fields['saswp_tech_article_alumniof'];
                            $itemlist = explode(",", $str);
                            foreach ( $itemlist as $key => $list){
                                $vnewarr['@type'] = 'Organization';
                                $vnewarr['Name']   = $list;   
                                $input1['alumniOf'][] = $vnewarr;
                            }
                        }
                    }

                    if ( isset( $custom_fields['saswp_tech_article_same_as']) ) {                            
                        $input1['sameAs'] = explode(',', $custom_fields['saswp_tech_article_same_as']);    
                    }

                    break;   
                    
                case 'Course':      
                    if ( isset( $custom_fields['saswp_course_id']) ) {
                        $input1['@id'] =    get_permalink().$custom_fields['saswp_course_id'];
                    }
                    if ( isset( $custom_fields['saswp_course_name']) ) {
                     $input1['name'] =    $custom_fields['saswp_course_name'];
                    }
                    if ( isset( $custom_fields['saswp_course_description']) ) {
                     $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_course_description'] )) ;
                    }
                    if ( isset( $custom_fields['saswp_course_url']) ) {
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_course_url']);
                    }                    
                    if ( isset( $custom_fields['saswp_course_date_published']) ) {
                     $input1['datePublished'] =    $custom_fields['saswp_course_date_published'];
                    }
                    if ( isset( $custom_fields['saswp_course_date_modified']) ) {
                     $input1['dateModified'] =    $custom_fields['saswp_course_date_modified'];
                    }

                    if ( isset( $custom_fields['saswp_course_duration']) ) {
                        $input1['timeRequired'] =    $custom_fields['saswp_course_duration'];
                    }

                    if ( isset( $custom_fields['saswp_course_code']) ) {
                        $input1['courseCode'] =    $custom_fields['saswp_course_code'];
                    }

                    if ( isset( $custom_fields['saswp_course_provider_name']) ) {
                     $input1['provider']['name'] =    $custom_fields['saswp_course_provider_name'];
                    }
                    
                    if ( isset( $custom_fields['saswp_course_sameas']) ) {
                     $input1['provider']['sameAs'] =    $custom_fields['saswp_course_sameas'];
                    }

                    if ( isset( $custom_fields['saswp_course_content_location_name']) || isset($custom_fields['saswp_course_content_location_locality']) || isset($custom_fields['saswp_course_content_location_country']) ) {

                        $input1['contentLocation']['@type']                        =   'Place';
                        $input1['contentLocation']['name']                         =   isset($custom_fields['saswp_course_content_location_name'])?$custom_fields['saswp_course_content_location_name']:'';
                        $input1['contentLocation']['address']['addressLocality']   =   isset($custom_fields['saswp_course_content_location_locality'])?$custom_fields['saswp_course_content_location_locality']:'';
                        $input1['contentLocation']['address']['addressRegion']     =   isset($custom_fields['saswp_course_content_location_region'])?$custom_fields['saswp_course_content_location_region']:'';
                        $input1['contentLocation']['address']['postalCode']        =   isset($custom_fields['saswp_course_content_location_postal_code'])?$custom_fields['saswp_course_content_location_postal_code']:'';
                        $input1['contentLocation']['address']['addressCountry']    =   isset($custom_fields['saswp_course_content_location_country'])?$custom_fields['saswp_course_content_location_country']:'';

                    }
                    
                    if ( isset( $custom_fields['saswp_course_rating']) && isset($custom_fields['saswp_course_review_count']) ) {
                        $input1['aggregateRating']['@type']       =   'AggregateRating';                                                
                        $input1['aggregateRating']['ratingValue'] =    $custom_fields['saswp_course_rating'];
                        $input1['aggregateRating']['ratingCount'] =    $custom_fields['saswp_course_review_count'];
                     }
                     
                    if ( isset( $custom_fields['saswp_course_offer_category']) || isset($custom_fields['saswp_course_offer_price']) || isset($custom_fields['saswp_course_offer_currency']) ) {
                        $input1['offers']['@type'] = 'Offer';
                        if ( isset( $custom_fields['saswp_course_offer_category']) ) {
                            $input1['offers']['category'] = $custom_fields['saswp_course_offer_category'];
                        }
                        if ( isset( $custom_fields['saswp_course_offer_price']) ) {
                            $input1['offers']['price'] = $custom_fields['saswp_course_offer_price'];
                        }
                        if ( isset( $custom_fields['saswp_course_offer_currency']) ) {
                            $input1['offers']['priceCurrency'] = $custom_fields['saswp_course_offer_currency'];
                        }
                     }
                    break;    
                    
                case 'DiscussionForumPosting':      
                    if ( isset( $custom_fields['saswp_dfp_id']) ) {
                        $input1['@id'] =    get_permalink().$custom_fields['saswp_dfp_id'];
                    } 
                    if ( isset( $custom_fields['saswp_dfp_headline']) ) {
                     $input1['headline'] =    $custom_fields['saswp_dfp_headline'];
                    }
                    if ( isset( $custom_fields['saswp_dfp_description']) ) {
                     $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_dfp_description'] )) ;
                    }
                    if ( isset( $custom_fields['saswp_dfp_url']) ) {
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_dfp_url']);
                    }                    
                    if ( isset( $custom_fields['saswp_dfp_date_published']) ) {
                     $input1['datePublished'] =    $custom_fields['saswp_dfp_date_published'];
                    }
                    if ( isset( $custom_fields['saswp_dfp_date_modified']) ) {
                     $input1['dateModified'] =    $custom_fields['saswp_dfp_date_modified'];
                    }
                    if ( isset( $custom_fields['saswp_dfp_author_type']) ) {
                        $input1['author']['@type'] =    $custom_fields['saswp_dfp_author_type'];
                    }
                    if ( isset( $custom_fields['saswp_dfp_author_name']) ) {
                     $input1['author']['name'] =    $custom_fields['saswp_dfp_author_name'];
                    }
                    if ( isset( $custom_fields['saswp_dfp_author_url']) ) {
                     $input1['author']['url'] =    $custom_fields['saswp_dfp_author_url'];
                    }
                    if ( isset( $custom_fields['saswp_dfp_author_description']) ) {
                     $input1['author']['description'] =    $custom_fields['saswp_dfp_author_description'];
                    }
                    
                    if ( isset( $custom_fields['saswp_dfp_main_entity_of_page']) ) {
                     $input1['mainEntityOfPage'] =    $custom_fields['saswp_dfp_main_entity_of_page'];
                    }
                    
                    if ( isset( $custom_fields['saswp_dfp_organization_logo']) && isset($custom_fields['saswp_dfp_organization_name']) ) {
                     $input1['publisher']['@type']       =    'Organization';
                     $input1['publisher']['name']        =    $custom_fields['saswp_dfp_organization_name'];
                     $input1['publisher']['logo']        =    $custom_fields['saswp_dfp_organization_logo'];
                    }                                                            
                    break;        
                
                case 'Recipe':
                    if ( isset( $custom_fields['saswp_recipe_id']) ) {
                        $input1['@id'] =    get_permalink().$custom_fields['saswp_recipe_id'];
                    } 
                    if ( isset( $custom_fields['saswp_recipe_url']) ) {
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_recipe_url']);
                    }
                    if ( isset( $custom_fields['saswp_recipe_name']) ) {
                     $input1['name'] =    $custom_fields['saswp_recipe_name'];
                    }
                    if ( isset( $custom_fields['saswp_recipe_description']) ) {
                        $input1['description'] =  wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_recipe_description'] ))  ;
                    }
                    if ( isset( $custom_fields['saswp_recipe_date_published']) ) {
                     $input1['datePublished'] =    $custom_fields['saswp_recipe_date_published'];
                    }
                    
                    if ( isset( $custom_fields['saswp_recipe_date_modified']) ) {
                     $input1['dateModified'] =    $custom_fields['saswp_recipe_date_modified'];
                    }                    
                    if ( isset( $custom_fields['saswp_recipe_main_entity']) ) {
                     $input1['mainEntity']['@id'] =    $custom_fields['saswp_recipe_main_entity'];
                    }
                    if ( isset( $custom_fields['saswp_recipe_author_type']) ) {
                     $input1['author']['@type'] =    $custom_fields['saswp_recipe_author_type'];
                    }
                    if ( isset( $custom_fields['saswp_recipe_author_name']) ) {
                     $input1['author']['name'] =    $custom_fields['saswp_recipe_author_name'];
                    }
                    if ( isset( $custom_fields['saswp_recipe_author_url']) ) {
                     $input1['author']['url'] =    $custom_fields['saswp_recipe_author_url'];
                    }
                    if ( isset( $custom_fields['saswp_recipe_author_description']) ) {
                     $input1['author']['description'] =    $custom_fields['saswp_recipe_author_description'];
                    }
                    if ( isset( $custom_fields['saswp_recipe_author_image']) ) {
                     $input1['author']['Image']['url'] =    $custom_fields['saswp_recipe_author_image'];
                    }

                    if ( isset( $custom_fields['saswp_recipe_organization_name']) && isset($custom_fields['saswp_recipe_organization_logo']) ) {
                        $input1['mainEntity']['publisher']['@type']       =    'Organization';
                        $input1['mainEntity']['publisher']['logo']        =    $custom_fields['saswp_recipe_organization_logo'];
                        $input1['mainEntity']['publisher']['name']        =    $custom_fields['saswp_recipe_organization_name'];
                    }

                    if ( isset( $custom_fields['saswp_recipe_preptime']) ) {
                     $input1['prepTime'] =    saswp_format_time_to_ISO_8601($custom_fields['saswp_recipe_preptime']);
                    }
                    if ( isset( $custom_fields['saswp_recipe_cooktime']) ) {
                     $input1['cookTime'] =    saswp_format_time_to_ISO_8601($custom_fields['saswp_recipe_cooktime']);
                    }
                    
                    if ( isset( $custom_fields['saswp_recipe_totaltime']) ) {
                     $input1['totalTime'] =    saswp_format_time_to_ISO_8601($custom_fields['saswp_recipe_totaltime']);
                    }
                    if ( isset( $custom_fields['saswp_recipe_keywords']) ) {
                     $input1['keywords'] =    $custom_fields['saswp_recipe_keywords'];
                    }
                    if ( isset( $custom_fields['saswp_recipe_recipeyield']) ) {
                     $input1['recipeYield'] =    $custom_fields['saswp_recipe_recipeyield'];
                    }
                    
                    if ( isset( $custom_fields['saswp_recipe_category']) ) {
                     $input1['recipeCategory'] =    $custom_fields['saswp_recipe_category'];
                    }
                    if ( isset( $custom_fields['saswp_recipe_cuisine']) ) {
                     $input1['recipeCuisine'] =    $custom_fields['saswp_recipe_cuisine'];
                    }
                    if ( isset( $custom_fields['saswp_recipe_nutrition']) ) {
                        $input1['nutrition']['@type']    = 'NutritionInformation';   
                        $input1['nutrition']['calories'] =    $custom_fields['saswp_recipe_nutrition'];
                    }
                    if ( isset( $custom_fields['saswp_recipe_protein']) ) {
                        $input1['nutrition']['@type']    = 'NutritionInformation';   
                        $input1['nutrition']['proteinContent'] =    $custom_fields['saswp_recipe_protein'];
                    }
                    if ( isset( $custom_fields['saswp_recipe_fat']) ) {
                        $input1['nutrition']['@type']    = 'NutritionInformation';   
                        $input1['nutrition']['fatContent'] =    $custom_fields['saswp_recipe_fat'];
                    }
                    if ( isset( $custom_fields['saswp_recipe_fiber']) ) {
                        $input1['nutrition']['@type']    = 'NutritionInformation';   
                        $input1['nutrition']['fiberContent'] =    $custom_fields['saswp_recipe_fiber'];
                    }
                    if ( isset( $custom_fields['saswp_recipe_sodium']) ) {
                        $input1['nutrition']['@type']    = 'NutritionInformation';   
                        $input1['nutrition']['sodiumContent'] =    $custom_fields['saswp_recipe_sodium'];
                    }
                    if ( isset( $custom_fields['saswp_recipe_sugar']) ) {
                        $input1['nutrition']['@type']        = 'NutritionInformation';   
                        $input1['nutrition']['sugarContent'] =    $custom_fields['saswp_recipe_sugar'];
                    }
                    if ( isset( $custom_fields['saswp_recipe_carbohydrate']) ) {
                        $input1['nutrition']['@type']               = 'NutritionInformation';   
                        $input1['nutrition']['carbohydrateContent'] =    $custom_fields['saswp_recipe_carbohydrate'];
                    }                                        
                    if ( isset( $custom_fields['saswp_recipe_cholesterol']) ) {
                        $input1['nutrition']['@type']               = 'NutritionInformation';   
                        $input1['nutrition']['cholesterolContent'] =    $custom_fields['saswp_recipe_cholesterol'];
                    }
                    if ( isset( $custom_fields['saswp_recipe_saturated_fat']) ) {
                        $input1['nutrition']['@type']               = 'NutritionInformation';   
                        $input1['nutrition']['saturatedFatContent'] =    $custom_fields['saswp_recipe_saturated_fat'];
                    }
                    if ( isset( $custom_fields['saswp_recipe_unsaturated_fat']) ) {
                        $input1['nutrition']['@type']               = 'NutritionInformation';   
                        $input1['nutrition']['unsaturatedFatContent'] =    $custom_fields['saswp_recipe_unsaturated_fat'];
                    }
                    if ( isset( $custom_fields['saswp_recipe_trans_fat']) ) {
                        $input1['nutrition']['@type']               = 'NutritionInformation';   
                        $input1['nutrition']['transFatContent'] =    $custom_fields['saswp_recipe_trans_fat'];
                    }
                    if ( isset( $custom_fields['saswp_recipe_serving_size']) ) {
                        $input1['nutrition']['@type']               = 'NutritionInformation';   
                        $input1['nutrition']['servingSize'] =    $custom_fields['saswp_recipe_serving_size'];
                    }
                    if ( isset( $custom_fields['saswp_recipe_ingredient']) ) {  
                      if ( is_array( $custom_fields['saswp_recipe_ingredient']) ) {                   
                          $recipe_ingredient = array();
                            foreach ( $custom_fields['saswp_recipe_ingredient'] as $sci_key => $sci_value) {
                                if ( ! empty( $sci_value) ) {
                                    $recipe_ingredient[] = wp_strip_all_tags($sci_value);       
                                }
                            }
                            if(empty($recipe_ingredient) ) {
                                $recipe_ingredient = $custom_fields['saswp_recipe_ingredient'];
                            }  
                            $input1['recipeIngredient'] =   $recipe_ingredient;                          
                      }else{     
                          $input1['recipeIngredient'] =  saswp_explod_by_semicolon($custom_fields['saswp_recipe_ingredient']);
                      }              
                    }
                    if ( isset( $custom_fields['saswp_recipe_instructions']) ) {  
                        if ( is_array( $custom_fields['saswp_recipe_instructions']) ) {
                            $recipe_instructions = array();
                            foreach ( $custom_fields['saswp_recipe_instructions'] as $sri_key => $sri_value) {
                                if ( ! empty( $sri_value) ) {
                                    $recipe_instructions[] = wp_strip_all_tags($sri_value);       
                                }
                            }
                            if(empty($recipe_instructions) ) {
                                $recipe_instructions = $custom_fields['saswp_recipe_instructions'];
                            }
                            $input1['recipeInstructions'] =    $recipe_instructions;   
                        }else{
                            $input1['recipeInstructions'] =    saswp_explod_by_semicolon($custom_fields['saswp_recipe_instructions']);
                        }                     
                    }
                    if ( isset( $custom_fields['saswp_recipe_video_name']) ) {
                        $input1['video']['@type']   = 'VideoObject';
                        $input1['video']['name'] =    $custom_fields['saswp_recipe_video_name'];
                    }
                    
                    if ( isset( $custom_fields['saswp_recipe_video_description']) ) {
                        $input1['video']['@type']   = 'VideoObject';
                        $input1['video']['description'] =    $custom_fields['saswp_recipe_video_description'];
                    }
                    if ( isset( $custom_fields['saswp_recipe_video_thumbnailurl']) ) {
                        $input1['video']['@type']   = 'VideoObject';   
                        $input1['video']['thumbnailUrl'] =    $custom_fields['saswp_recipe_video_thumbnailurl'];
                    }
                    if ( isset( $custom_fields['saswp_recipe_video_contenturl']) ) {
                        $input1['video']['@type']   = 'VideoObject';
                        $input1['video']['contentUrl'] =    $custom_fields['saswp_recipe_video_contenturl'];
                    }                    
                    if ( isset( $custom_fields['saswp_recipe_video_embedurl']) ) {
                        $input1['video']['@type']   = 'VideoObject';
                        $input1['video']['embedUrl'] =    $custom_fields['saswp_recipe_video_embedurl'];
                    }
                    if ( isset( $custom_fields['saswp_recipe_video_upload_date']) ) {
                        $input1['video']['@type']   = 'VideoObject';
                        $input1['video']['uploadDate'] =    $custom_fields['saswp_recipe_video_upload_date'];
                    }
                    if ( isset( $custom_fields['saswp_recipe_video_duration']) ) {
                        $input1['video']['@type']   = 'VideoObject';
                        $input1['video']['duration'] =    $custom_fields['saswp_recipe_video_duration'];
                    }
                                         
                    if ( isset( $custom_fields['saswp_recipe_schema_rating']) && isset($custom_fields['saswp_recipe_schema_review_count']) ) {
                       $input1['aggregateRating']['@type']       =   'AggregateRating';
                       $input1['aggregateRating']['worstRating'] =   0;
                       $input1['aggregateRating']['bestRating']  =   5;
                       $input1['aggregateRating']['ratingValue'] =    $custom_fields['saswp_recipe_schema_rating'];
                       $input1['aggregateRating']['ratingCount'] =    $custom_fields['saswp_recipe_schema_review_count'];
                    }
                    
                    break;
                
                case 'Product':                                                                                                
                    if ( isset( $custom_fields['saswp_product_schema_id']) ) {
                     $input1['@id'] =    get_permalink().$custom_fields['saswp_product_schema_id'];
                    }
                    if ( isset( $custom_fields['saswp_product_schema_url']) ) {
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_product_schema_url']);
                    }
                    if ( isset( $custom_fields['saswp_product_schema_name']) ) {
                     $input1['name'] =    $custom_fields['saswp_product_schema_name'];
                    }
                    
                    if ( isset( $custom_fields['saswp_product_schema_brand_name']) ) {
                        
                     $input1['brand']['@type'] =    'Brand';
                     $input1['brand']['name']  =    $custom_fields['saswp_product_schema_brand_name'];

                       if ( isset( $custom_fields['saswp_product_schema_brand_url']) ) {
                        $input1['brand']['url'] =    $custom_fields['saswp_product_schema_brand_url'];
                       }
                       if ( isset( $custom_fields['saswp_product_schema_brand_image']) ) {
                        $input1['brand']['image'] =    $custom_fields['saswp_product_schema_brand_image'];
                       }
                       if ( isset( $custom_fields['saswp_product_schema_brand_logo']) ) {
                        $input1['brand']['logo'] =    $custom_fields['saswp_product_schema_brand_logo'];
                       }   

                    }
                    
                    if ( isset( $custom_fields['saswp_product_schema_mpn']) ) {
                     $input1['mpn'] =    $custom_fields['saswp_product_schema_mpn'];
                    }
                    if ( isset( $custom_fields['saswp_product_schema_gtin8']) ) {
                     $input1['gtin8'] =    $custom_fields['saswp_product_schema_gtin8'];
                    }
                    if ( isset( $custom_fields['saswp_product_schema_gtin13']) ) {
                        $input1['gtin13'] =    $custom_fields['saswp_product_schema_gtin13'];
                    }
                    if ( isset( $custom_fields['saswp_product_schema_gtin12']) ) {
                        $input1['gtin12'] =    $custom_fields['saswp_product_schema_gtin12'];
                    }
                    if ( isset( $custom_fields['saswp_product_schema_color']) ) {
                        $input1['color'] =    $custom_fields['saswp_product_schema_color'];
                    } 
                    if ( isset( $custom_fields['saswp_product_additional_type']) ) {
                        $input1['additionalType'] =    $custom_fields['saswp_product_additional_type'];
                    }
                    if ( ! empty( $custom_fields['saswp_product_weight'] ) && ! empty( $custom_fields['saswp_product_weight_unit'] ) ) {
                        $input1['weight']['@type']      =   'QuantitativeValue';    
                        $input1['weight']['value']      =   $custom_fields['saswp_product_weight'];
                        $input1['weight']['unitCode']   =   $custom_fields['saswp_product_weight_unit'];    
                    }                                        
                    if ( isset( $custom_fields['saswp_product_schema_description']) ) {
                     $input1['description'] =  wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_product_schema_description'] ));
                    }                    
                    if ( isset( $custom_fields['saswp_product_schema_image']) ) {
                     $input1['image'] =    $custom_fields['saswp_product_schema_image'];
                    }
                    if ( isset( $custom_fields['saswp_product_schema_availability']) ) {
                     $input1['offers']['availability'] =    $custom_fields['saswp_product_schema_availability'];
                     if ( isset( $custom_fields['saswp_product_schema_url']) ) {
                         $input1['offers']['url']   =    $custom_fields['saswp_product_schema_url'];
                     }
                    }
                    if ( isset( $custom_fields['saswp_product_schema_price']) ) {
                     $input1['offers']['price'] =    $custom_fields['saswp_product_schema_price'];
                     
                     if ( isset( $custom_fields['saswp_product_schema_url']) ) {
                         $input1['offers']['url']   =    $custom_fields['saswp_product_schema_url'];
                     }
                                          
                    }
                    if ( isset( $custom_fields['saswp_product_schema_currency']) ) {
                     $input1['offers']['priceCurrency'] = saswp_modify_currency_code($custom_fields['saswp_product_schema_currency']);
                        if ( isset( $custom_fields['saswp_product_schema_url']) ) {
                            $input1['offers']['url'] =    $custom_fields['saswp_product_schema_url'];
                        }
                    }
                    if ( isset( $custom_fields['saswp_product_schema_vat']) ) {
                        $input1['offers']['priceSpecification']['@type']                 =    'priceSpecification';
                        $input1['offers']['priceSpecification']['valueAddedTaxIncluded'] =    $custom_fields['saswp_product_schema_vat'];
                    }
                    if ( isset( $custom_fields['saswp_product_schema_priceValidUntil']) ) {
                     $input1['offers']['priceValidUntil'] =    $custom_fields['saswp_product_schema_priceValidUntil'];
                     
                    }                   
                    if ( isset( $custom_fields['saswp_product_schema_condition']) ) {
                     $input1['offers']['itemCondition'] =    $custom_fields['saswp_product_schema_condition'];
                    }
                    if ( isset( $custom_fields['saswp_product_schema_sku']) ) {
                     $input1['sku']                    =    $custom_fields['saswp_product_schema_sku'];
                    }
                    if ( isset( $custom_fields['saswp_product_schema_seller']) ) {
                     $input1['offers']['seller']['@type']         =    'Organization';
                     $input1['offers']['seller']['name']          =    $custom_fields['saswp_product_schema_seller'];
                    }

                    if( isset($custom_fields['saswp_product_schema_high_price']) && isset($custom_fields['saswp_product_schema_low_price']) ){

                        $input1['offers']['@type']     = 'AggregateOffer';
                        $input1['offers']['highPrice'] = $custom_fields['saswp_product_schema_high_price'];
                        $input1['offers']['lowPrice']  = $custom_fields['saswp_product_schema_low_price'];

                        if ( isset( $custom_fields['saswp_product_schema_offer_count']) ) {
                            $input1['offers']['offerCount']     = $custom_fields['saswp_product_schema_offer_count'];
                        }
                    }
                    // Changes since version 1.15
                    if((isset($custom_fields['saswp_product_schema_rp_country_code']) && !empty($custom_fields['saswp_product_schema_rp_country_code'])) || (isset($custom_fields['saswp_product_schema_rp_category']) && !empty($custom_fields['saswp_product_schema_rp_category'])) || (isset($custom_fields['saswp_product_schema_rp_return_days']) && !empty($custom_fields['saswp_product_schema_rp_return_days'])) || (isset($custom_fields['saswp_product_schema_rp_return_method']) && !empty($custom_fields['saswp_product_schema_rp_return_method'])) || (isset($custom_fields['saswp_product_schema_rp_return_fees']) && !empty($custom_fields['saswp_product_schema_rp_return_method'])) ) {
                        $input1['offers']['hasMerchantReturnPolicy']['@type'] = 'MerchantReturnPolicy';
                        if ( ! empty( $custom_fields['saswp_product_schema_rp_country_code']) ) {
                            $input1['offers']['hasMerchantReturnPolicy']['applicableCountry'] = esc_attr( $custom_fields['saswp_product_schema_rp_country_code']);
                        }
                        if ( isset( $custom_fields['saswp_product_schema_rp_category']) && !empty($custom_fields['saswp_product_schema_rp_category']) ) {
                            $rp_category = array('MerchantReturnFiniteReturnWindow','MerchantReturnNotPermitted','MerchantReturnUnlimitedWindow','MerchantReturnUnspecified');
                            if(in_array($custom_fields['saswp_product_schema_rp_category'], $rp_category) ) {
                                $input1['offers']['hasMerchantReturnPolicy']['returnPolicyCategory'] = esc_attr( $custom_fields['saswp_product_schema_rp_category']);
                            }
                        }
                        if ( isset( $custom_fields['saswp_product_schema_rp_return_days']) && !empty($custom_fields['saswp_product_schema_rp_return_days']) ) {
                                $input1['offers']['hasMerchantReturnPolicy']['merchantReturnDays'] = esc_attr( $custom_fields['saswp_product_schema_rp_return_days']);
                        }
                        if ( isset( $custom_fields['saswp_product_schema_rp_return_method']) && !empty($custom_fields['saswp_product_schema_rp_return_method']) ) {
                            $rm_category = array('ReturnAtKiosk','ReturnByMail','ReturnInStore');
                            if(in_array($custom_fields['saswp_product_schema_rp_return_method'], $rm_category) ) {
                                $input1['offers']['hasMerchantReturnPolicy']['returnMethod'] = esc_attr( $custom_fields['saswp_product_schema_rp_return_method']);
                            }
                        }
                        if((isset($custom_fields['saswp_product_schema_rsf_name']) && !empty($custom_fields['saswp_product_schema_rsf_name'])) || (isset($custom_fields['saswp_product_schema_rsf_value']) && !empty($custom_fields['saswp_product_schema_rsf_value'])) || (isset($custom_fields['saswp_product_schema_rsf_currency']) && !empty($custom_fields['saswp_product_schema_rsf_currency'])) ) {
                            $input1['offers']['hasMerchantReturnPolicy']['returnShippingFeesAmount']['@type'] = 'MonetaryAmount';
                            if ( isset( $custom_fields['saswp_product_schema_rsf_name']) ) {
                                $input1['offers']['hasMerchantReturnPolicy']['returnShippingFeesAmount']['name'] = esc_attr( $custom_fields['saswp_product_schema_rsf_name']);    
                            }
                            if ( isset( $custom_fields['saswp_product_schema_rsf_value']) ) {
                                $input1['offers']['hasMerchantReturnPolicy']['returnShippingFeesAmount']['value'] = esc_attr( $custom_fields['saswp_product_schema_rsf_value']);    
                            }
                            if ( isset( $custom_fields['saswp_product_schema_rsf_currency']) ) {
                                $input1['offers']['hasMerchantReturnPolicy']['returnShippingFeesAmount']['currency'] = esc_attr( $custom_fields['saswp_product_schema_rsf_currency']);    
                            }    
                            if ( isset( $custom_fields['saswp_product_schema_rp_return_fees']) ) {
                                $rf_category = array('FreeReturn','OriginalShippingFees','RestockingFees','ReturnFeesCustomerResponsibility','ReturnShippingFees');
                                    $input1['offers']['hasMerchantReturnPolicy']['returnFees'] = 'ReturnShippingFees';
                            }
                        }else{
                            if ( isset( $custom_fields['saswp_product_schema_rp_return_fees']) && !empty($custom_fields['saswp_product_schema_rp_return_fees']) ) {
                                $rf_category = array('FreeReturn','OriginalShippingFees','RestockingFees','ReturnFeesCustomerResponsibility','ReturnShippingFees');
                                    $input1['offers']['hasMerchantReturnPolicy']['returnFees'] = esc_attr( $custom_fields['saswp_product_schema_rp_return_fees']);
                            }
                                
                        }
                    }

                    if ( isset( $custom_fields['saswp_product_schema_sr_value']) && !empty($custom_fields['saswp_product_schema_sr_value']) ) {
                        $input1['offers']['shippingDetails']['@type'] = 'OfferShippingDetails';
                        $input1['offers']['shippingDetails']['shippingRate']['@type'] = 'MonetaryAmount';
                        $input1['offers']['shippingDetails']['shippingRate']['value'] = esc_attr( $custom_fields['saswp_product_schema_sr_value']);
                        if ( isset( $custom_fields['saswp_product_schema_sr_currency']) && !empty($custom_fields['saswp_product_schema_sr_currency']) ) {
                            $input1['offers']['shippingDetails']['shippingRate']['currency'] = esc_attr( $custom_fields['saswp_product_schema_sr_currency']);
                        }
                        if((isset($custom_fields['saswp_product_schema_sa_locality']) && !empty($custom_fields['saswp_product_schema_sa_locality'])) || (isset($custom_fields['saswp_product_schema_sa_region']) && !empty($custom_fields['saswp_product_schema_sa_region'])) || (isset($custom_fields['saswp_product_schema_sa_postal_code']) && !empty($custom_fields['saswp_product_schema_sa_postal_code'])) || (isset($custom_fields['saswp_product_schema_sa_address']) && !empty($custom_fields['saswp_product_schema_sa_address'])) || (isset($custom_fields['saswp_product_schema_sa_country']) && !empty($custom_fields['saswp_product_schema_sa_country'])) ) {
                            $input1['offers']['shippingDetails']['shippingDestination']['@type'] = 'DefinedRegion';
                            if ( isset( $custom_fields['saswp_product_schema_sa_locality']) && !empty($custom_fields['saswp_product_schema_sa_locality']) ) {
                                $input1['offers']['shippingDetails']['shippingDestination']['addressLocality'] = esc_attr( $custom_fields['saswp_product_schema_sa_locality']);
                            }
                            if ( isset( $custom_fields['saswp_product_schema_sa_region']) && !empty($custom_fields['saswp_product_schema_sa_region']) ) {
                                $input1['offers']['shippingDetails']['shippingDestination']['addressRegion'] = esc_attr( $custom_fields['saswp_product_schema_sa_region']);
                            }
                            if ( isset( $custom_fields['saswp_product_schema_sa_postal_code']) && !empty($custom_fields['saswp_product_schema_sa_postal_code']) ) {
                                $input1['offers']['shippingDetails']['shippingDestination']['postalCode'] = esc_attr( $custom_fields['saswp_product_schema_sa_postal_code']);
                            }
                            if ( isset( $custom_fields['saswp_product_schema_sa_address']) && !empty($custom_fields['saswp_product_schema_sa_address']) ) {
                                $input1['offers']['shippingDetails']['shippingDestination']['streetAddress'] = esc_attr( $custom_fields['saswp_product_schema_sa_address']);
                            }
                            if ( isset( $custom_fields['saswp_product_schema_sa_country']) && !empty($custom_fields['saswp_product_schema_sa_country']) ) {
                                $input1['offers']['shippingDetails']['shippingDestination']['addressCountry'] = esc_attr( $custom_fields['saswp_product_schema_sa_country']);
                            }
                        }
                        if((isset($custom_fields['saswp_product_schema_sdh_minval']) && !empty($custom_fields['saswp_product_schema_sdh_minval'])) && (isset($custom_fields['saswp_product_schema_sdh_maxval']) && !empty($custom_fields['saswp_product_schema_sdh_maxval'])) && (isset($custom_fields['saswp_product_schema_sdh_unitcode']) && !empty($custom_fields['saswp_product_schema_sdh_unitcode'])) ) {
                            $input1['offers']['shippingDetails']['deliveryTime']['@type'] = 'ShippingDeliveryTime';
                            $input1['offers']['shippingDetails']['deliveryTime']['handlingTime']['@type'] = 'QuantitativeValue';
                            $input1['offers']['shippingDetails']['deliveryTime']['handlingTime']['minValue'] = esc_attr( $custom_fields['saswp_product_schema_sdh_minval']);
                            $input1['offers']['shippingDetails']['deliveryTime']['handlingTime']['maxValue'] = esc_attr( $custom_fields['saswp_product_schema_sdh_maxval']);
                            $input1['offers']['shippingDetails']['deliveryTime']['handlingTime']['unitCode'] = esc_attr( $custom_fields['saswp_product_schema_sdh_unitcode']);
                        }
                        if((isset($custom_fields['saswp_product_schema_sdt_minval']) && !empty($custom_fields['saswp_product_schema_sdt_minval'])) && (isset($custom_fields['saswp_product_schema_sdt_maxval']) && !empty($custom_fields['saswp_product_schema_sdt_maxval'])) && (isset($custom_fields['saswp_product_schema_sdt_unitcode']) && !empty($custom_fields['saswp_product_schema_sdt_unitcode'])) ) {
                            $input1['offers']['shippingDetails']['deliveryTime']['transitTime']['@type'] = 'QuantitativeValue';
                            $input1['offers']['shippingDetails']['deliveryTime']['transitTime']['minValue'] = esc_attr( $custom_fields['saswp_product_schema_sdt_minval']);
                            $input1['offers']['shippingDetails']['deliveryTime']['transitTime']['maxValue'] = esc_attr( $custom_fields['saswp_product_schema_sdt_maxval']);
                            $input1['offers']['shippingDetails']['deliveryTime']['transitTime']['unitCode'] = esc_attr( $custom_fields['saswp_product_schema_sdt_unitcode']);
                        }
                    }

                    if ( isset( $custom_fields['saswp_product_schema_rating']) && isset($custom_fields['saswp_product_schema_review_count']) ) {
                        $input1['aggregateRating']['@type']       = 'aggregateRating';
                        $input1['aggregateRating']['ratingValue'] = $custom_fields['saswp_product_schema_rating'];
                        $input1['aggregateRating']['reviewCount'] = $custom_fields['saswp_product_schema_review_count'];
                    }
                                                            
                    break;
                        
                    case 'ProductGroup':

                        if ( isset( $custom_fields['saswp_product_grp_schema_id']) ) {
                            $input1['@id'] =    get_permalink().$custom_fields['product_grp'];
                        }
                        if ( isset( $custom_fields['saswp_product_grp_schema_url']) ) {
                            $input1['url'] =    saswp_validate_url($custom_fields['saswp_product_grp_schema_url']);
                        }
                        if ( isset( $custom_fields['saswp_product_grp_schema_name']) ) {
                            $input1['name'] =    $custom_fields['saswp_product_grp_schema_name'];
                        }
                        if ( isset( $custom_fields['saswp_product_grp_schema_brand_name']) ) {
                            $input1['brand']['@type'] =    'Brand';
                            $input1['brand']['name']  =    $custom_fields['saswp_product_grp_schema_brand_name'];  
                        }                                        
                        if ( isset( $custom_fields['saswp_product_grp_schema_description']) ) {
                            $input1['description'] =  wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_product_grp_schema_description'] ));
                        }                    
                        if ( isset( $custom_fields['saswp_product_grp_schema_image']) ) {
                            $input1['image'] =    $custom_fields['saswp_product_grp_schema_image'];
                        }
                        if ( isset( $custom_fields['saswp_product_grp_schema_group_id']) ) {
                            $input1['productGroupID'] =    $custom_fields['saswp_product_grp_schema_group_id'];
                        }
                        if ( ! empty( $custom_fields['saswp_product_grp_schema_varies_by']) ) {
                            $explode_varies     =   explode( ',', $custom_fields['saswp_product_grp_schema_varies_by'] );
                            if ( is_array( $explode_varies ) ) {
                                foreach ($explode_varies as $vkey => $varies) {
                                    $input1['variesBy'][] = saswp_context_url() . $varies;           
                                }
                            }                 
                        }
                        if ( isset( $custom_fields['saswp_product_grp_schema_mpn']) ) {
                         $input1['mpn'] =    $custom_fields['saswp_product_grp_schema_mpn'];
                        }
                        if ( isset( $custom_fields['saswp_product_grp_schema_sku']) ) {
                         $input1['sku']                    =    $custom_fields['saswp_product_grp_schema_sku'];
                        }
                        if ( isset( $custom_fields['saswp_product_grp_schema_gtin8']) ) {
                         $input1['gtin8'] =    $custom_fields['saswp_product_grp_schema_gtin8'];
                        }
                        if ( isset( $custom_fields['saswp_product_schema_grp_gtin13']) ) {
                            $input1['gtin13'] =    $custom_fields['saswp_product_grp_schema_gtin13'];
                        }
                        if ( isset( $custom_fields['saswp_product_grp_schema_gtin12']) ) {
                            $input1['gtin12'] =    $custom_fields['saswp_product_grp_schema_gtin12'];
                        }
                        if ( isset( $custom_fields['saswp_product_grp_additional_type']) ) {
                            $input1['additionalType'] =    $custom_fields['saswp_product_grp_additional_type'];
                        }
                        if ( isset( $custom_fields['saswp_product_grp_schema_availability']) ) {
                         $input1['offers']['availability'] =    $custom_fields['saswp_product_grp_schema_availability'];
                         if ( isset( $custom_fields['saswp_product_grp_schema_url']) ) {
                             $input1['offers']['url']   =    $custom_fields['saswp_product_grp_schema_url'];
                         }
                        }
                        if ( isset( $custom_fields['saswp_product_grp_schema_price']) ) {
                         $input1['offers']['price'] =    $custom_fields['saswp_product_grp_schema_price'];
                         
                         if ( isset( $custom_fields['saswp_grp_product_schema_url']) ) {
                             $input1['offers']['url']   =    $custom_fields['saswp_product_grp_schema_url'];
                         }
                                              
                        }
                        if ( isset( $custom_fields['saswp_product_grp_schema_currency']) ) {
                         $input1['offers']['priceCurrency'] = saswp_modify_currency_code($custom_fields['saswp_product_grp_schema_currency']);
                            if ( isset( $custom_fields['saswp_product_grp_schema_url']) ) {
                                $input1['offers']['url'] =    $custom_fields['saswp_product_grp_schema_url'];
                            }
                        }
                        if ( isset( $custom_fields['saswp_product_grp_schema_priceValidUntil']) ) {
                         $input1['offers']['priceValidUntil'] =    $custom_fields['saswp_product_grp_schema_priceValidUntil'];
                         
                        }                   
                        if ( isset( $custom_fields['saswp_product_grp_schema_condition']) ) {
                         $input1['offers']['itemCondition'] =    $custom_fields['saswp_product_grp_schema_condition'];
                        }
                        if ( isset( $custom_fields['saswp_product_grp_schema_seller']) ) {
                         $input1['offers']['seller']['@type']         =    'Organization';
                         $input1['offers']['seller']['name']          =    $custom_fields['saswp_product_grp_schema_seller'];
                        }

                        if( isset($custom_fields['saswp_product_grp_schema_high_price']) && isset($custom_fields['saswp_product_grp_schema_low_price']) ){

                            $input1['offers']['@type']     = 'AggregateOffer';
                            $input1['offers']['highPrice'] = $custom_fields['saswp_product_grp_schema_high_price'];
                            $input1['offers']['lowPrice']  = $custom_fields['saswp_product_grp_schema_low_price'];

                            if ( isset( $custom_fields['saswp_product_schema_offer_count']) ) {
                                $input1['offers']['offerCount']     = $custom_fields['saswp_product_grp_schema_offer_count'];
                            }
                        }
                        // Changes since version 1.15
                        if ( ( isset( $custom_fields['saswp_product_grp_schema_rp_country_code'] ) && 
                            !empty( $custom_fields['saswp_product_grp_schema_rp_country_code'] ) ) || 
                            ( isset( $custom_fields['saswp_product_grp_grp_schema_rp_category'] ) && 
                            !empty( $custom_fields['saswp_product_grp_schema_rp_category'] ) ) || 
                            ( isset( $custom_fields['saswp_product_grp_schema_rp_return_days'] ) && 
                            !empty( $custom_fields['saswp_product_grp_schema_rp_return_days'] ) ) || 
                            ( isset( $custom_fields['saswp_product_grp_grp_schema_rp_return_method'] ) && 
                            !empty( $custom_fields['saswp_product_grp_schema_rp_return_method'] ) ) || 
                            ( isset( $custom_fields['saswp_product_grp_schema_rp_return_fees'] ) && 
                            !empty( $custom_fields['saswp_product_grp_schema_rp_return_method'] ) ) ) {
                            $input1['offers']['hasMerchantReturnPolicy']['@type'] = 'MerchantReturnPolicy';
                            if ( ! empty( $custom_fields['saswp_product_grp_schema_rp_country_code']) ) {
                                $input1['offers']['hasMerchantReturnPolicy']['applicableCountry'] = esc_attr( $custom_fields['saswp_product_grp_schema_rp_country_code']);
                            }
                            if ( isset( $custom_fields['saswp_product_grp_schema_rp_category']) && !empty($custom_fields['saswp_product_grp_schema_rp_category']) ) {
                                $rp_category = array('MerchantReturnFiniteReturnWindow','MerchantReturnNotPermitted','MerchantReturnUnlimitedWindow','MerchantReturnUnspecified');
                                if(in_array($custom_fields['saswp_product_grp_schema_rp_category'], $rp_category) ) {
                                    $input1['offers']['hasMerchantReturnPolicy']['returnPolicyCategory'] = esc_attr( $custom_fields['saswp_product_grp_schema_rp_category']);
                                }
                            }
                            if ( isset( $custom_fields['saswp_product_grp_schema_rp_return_days']) && !empty($custom_fields['saswp_product_grp_schema_rp_return_days']) ) {
                                    $input1['offers']['hasMerchantReturnPolicy']['merchantReturnDays'] = esc_attr( $custom_fields['saswp_product_grp_schema_rp_return_days']);
                            }
                            if ( isset( $custom_fields['saswp_product_grp_schema_rp_return_method']) && !empty($custom_fields['saswp_product_grp_schema_rp_return_method']) ) {
                                $rm_category = array('ReturnAtKiosk','ReturnByMail','ReturnInStore');
                                if(in_array($custom_fields['saswp_product_grp_schema_rp_return_method'], $rm_category) ) {
                                    $input1['offers']['hasMerchantReturnPolicy']['returnMethod'] = esc_attr( $custom_fields['saswp_product_grp_schema_rp_return_method']);
                                }
                            }
                            if((isset($custom_fields['saswp_product_grp_schema_rsf_name']) && !empty($custom_fields['saswp_product_grp_schema_rsf_name'])) || (isset($custom_fields['saswp_product_grp_schema_rsf_value']) && !empty($custom_fields['saswp_product_grp_schema_rsf_value'])) || (isset($custom_fields['saswp_product_grp_schema_rsf_currency']) && !empty($custom_fields['saswp_product_grp_schema_rsf_currency'])) ) {
                                $input1['offers']['hasMerchantReturnPolicy']['returnShippingFeesAmount']['@type'] = 'MonetaryAmount';
                                if ( isset( $custom_fields['saswp_product_grp_schema_rsf_name']) ) {
                                    $input1['offers']['hasMerchantReturnPolicy']['returnShippingFeesAmount']['name'] = esc_attr( $custom_fields['saswp_product_schema_rsf_name']);    
                                }
                                if ( isset( $custom_fields['saswp_product_grp_schema_rsf_value']) ) {
                                    $input1['offers']['hasMerchantReturnPolicy']['returnShippingFeesAmount']['value'] = esc_attr( $custom_fields['saswp_product_grp_schema_rsf_value']);    
                                }
                                if ( isset( $custom_fields['saswp_product_grp_schema_rsf_currency']) ) {
                                    $input1['offers']['hasMerchantReturnPolicy']['returnShippingFeesAmount']['currency'] = esc_attr( $custom_fields['saswp_product_grp_schema_rsf_currency']);    
                                }    
                                if ( isset( $custom_fields['saswp_product_grp_schema_rp_return_fees']) ) {
                                    $rf_category = array('FreeReturn','OriginalShippingFees','RestockingFees','ReturnFeesCustomerResponsibility','ReturnShippingFees');
                                        $input1['offers']['hasMerchantReturnPolicy']['returnFees'] = 'ReturnShippingFees';
                                }
                            }else{
                                if ( isset( $custom_fields['saswp_product_grp_schema_rp_return_fees']) && !empty($custom_fields['saswp_product_grp_schema_rp_return_fees']) ) {
                                    $rf_category = array('FreeReturn','OriginalShippingFees','RestockingFees','ReturnFeesCustomerResponsibility','ReturnShippingFees');
                                        $input1['offers']['hasMerchantReturnPolicy']['returnFees'] = esc_attr( $custom_fields['saswp_product_grp_schema_rp_return_fees']);
                                }
                                    
                            }
                        }

                        if ( isset( $custom_fields['saswp_product_grp_schema_sr_value']) && !empty($custom_fields['saswp_product_grp_schema_sr_value']) ) {
                            $input1['offers']['shippingDetails']['@type'] = 'OfferShippingDetails';
                            $input1['offers']['shippingDetails']['shippingRate']['@type'] = 'MonetaryAmount';
                            $input1['offers']['shippingDetails']['shippingRate']['value'] = esc_attr( $custom_fields['saswp_product_grp_schema_sr_value']);
                            if ( isset( $custom_fields['saswp_product_grp_schema_sr_currency']) && !empty($custom_fields['saswp_product_grp_schema_sr_currency']) ) {
                                $input1['offers']['shippingDetails']['shippingRate']['currency'] = esc_attr( $custom_fields['saswp_product_grp_schema_sr_currency']);
                            }
                            if ( ( isset($custom_fields['saswp_product_grp_schema_sa_locality'] ) && 
                                !empty( $custom_fields['saswp_product_grp_schema_sa_locality'] ) ) || 
                                ( isset( $custom_fields['saswp_product_grp_schema_sa_region'] ) && 
                                !empty( $custom_fields['saswp_product_grp_schema_sa_region'] ) ) || 
                                ( isset( $custom_fields['saswp_product_grp_schema_sa_postal_code'] ) && 
                                !empty( $custom_fields['saswp_product_grp_schema_sa_postal_code'] ) ) || 
                                ( isset( $custom_fields['saswp_product_grp_schema_sa_address'] ) && 
                                !empty( $custom_fields['saswp_product_grp_schema_sa_address'] ) ) || 
                                ( isset( $custom_fields['saswp_product_grp_schema_sa_country'] ) && 
                                !empty( $custom_fields['saswp_product_grp_schema_sa_country'] ) ) ) {
                                $input1['offers']['shippingDetails']['shippingDestination']['@type'] = 'DefinedRegion';
                                if ( isset( $custom_fields['saswp_product_grp_schema_sa_locality']) && !empty($custom_fields['saswp_product_schema_sa_locality']) ) {
                                    $input1['offers']['shippingDetails']['shippingDestination']['addressLocality'] = esc_attr( $custom_fields['saswp_product_grp_schema_sa_locality']);
                                }
                                if ( isset( $custom_fields['saswp_product_grp_schema_sa_region']) && !empty($custom_fields['saswp_product_grp_schema_sa_region']) ) {
                                    $input1['offers']['shippingDetails']['shippingDestination']['addressRegion'] = esc_attr( $custom_fields['saswp_product_grp_schema_sa_region']);
                                }
                                if ( isset( $custom_fields['saswp_product_schema_sa_postal_code']) && !empty($custom_fields['saswp_product_schema_sa_postal_code']) ) {
                                    $input1['offers']['shippingDetails']['shippingDestination']['postalCode'] = esc_attr( $custom_fields['saswp_product_schema_sa_postal_code']);
                                }
                                if ( isset( $custom_fields['saswp_product_grp_schema_sa_address']) && !empty($custom_fields['saswp_product_grp_schema_sa_address']) ) {
                                    $input1['offers']['shippingDetails']['shippingDestination']['streetAddress'] = esc_attr( $custom_fields['saswp_product_grp_schema_sa_address']);
                                }
                                if ( isset( $custom_fields['saswp_product_grp_schema_sa_country']) && !empty($custom_fields['saswp_product_grp_schema_sa_country']) ) {
                                    $input1['offers']['shippingDetails']['shippingDestination']['addressCountry'] = esc_attr( $custom_fields['saswp_product_grp_schema_sa_country']);
                                }
                            }
                            if((isset($custom_fields['saswp_product_grp_schema_sdh_minval']) && !empty($custom_fields['saswp_product_grp_schema_sdh_minval'])) && (isset($custom_fields['saswp_product_grp_schema_sdh_maxval']) && !empty($custom_fields['saswp_product_grp_schema_sdh_maxval'])) && (isset($custom_fields['saswp_product_grp_schema_sdh_unitcode']) && !empty($custom_fields['saswp_product_grp_schema_sdh_unitcode'])) ) {
                                $input1['offers']['shippingDetails']['deliveryTime']['@type'] = 'ShippingDeliveryTime';
                                $input1['offers']['shippingDetails']['deliveryTime']['handlingTime']['@type'] = 'QuantitativeValue';
                                $input1['offers']['shippingDetails']['deliveryTime']['handlingTime']['minValue'] = esc_attr( $custom_fields['saswp_product_grp_schema_sdh_minval']);
                                $input1['offers']['shippingDetails']['deliveryTime']['handlingTime']['maxValue'] = esc_attr( $custom_fields['saswp_product_grp_schema_sdh_maxval']);
                                $input1['offers']['shippingDetails']['deliveryTime']['handlingTime']['unitCode'] = esc_attr( $custom_fields['saswp_product_grp_schema_sdh_unitcode']);
                            }
                            if ( ( isset($custom_fields['saswp_product_grp_schema_sdt_minval'] ) && 
                                !empty($custom_fields['saswp_product_grp_schema_sdt_minval'] ) ) && 
                                ( isset( $custom_fields['saswp_product_grp_schema_sdt_maxval'] ) && 
                                !empty( $custom_fields['saswp_product_grp_schema_sdt_maxval'] ) ) && 
                                ( isset( $custom_fields['saswp_product_grp_schema_sdt_unitcode'] ) && 
                                !empty($custom_fields['saswp_product_grp_schema_sdt_unitcode'] ) ) ) {
                                    $input1['offers']['shippingDetails']['deliveryTime']['transitTime']['@type'] = 'QuantitativeValue';
                                    $input1['offers']['shippingDetails']['deliveryTime']['transitTime']['minValue'] = esc_attr( $custom_fields['saswp_product_grp_schema_sdt_minval']);
                                    $input1['offers']['shippingDetails']['deliveryTime']['transitTime']['maxValue'] = esc_attr( $custom_fields['saswp_product_grp_schema_sdt_maxval']);
                                    $input1['offers']['shippingDetails']['deliveryTime']['transitTime']['unitCode'] = esc_attr( $custom_fields['saswp_product_grp_schema_sdt_unitcode']);
                            }
                        }

                        if ( isset( $custom_fields['saswp_product_grp_srp_schema_rating']) && isset($custom_fields['saswp_product_grp_srp_schema_review_count']) ) {
                            $input1['aggregateRating']['@type']       = 'aggregateRating';
                            $input1['aggregateRating']['ratingValue'] = $custom_fields['saswp_product_grp_srp_schema_rating'];
                            $input1['aggregateRating']['reviewCount'] = $custom_fields['saswp_product_grp_srp_schema_review_count'];
                        }
                        
                    break;

                    case 'Car':
                            if ( isset( $custom_fields['saswp_car_schema_id']) ) {
                                $input1['@id'] =    get_permalink().$custom_fields['saswp_car_schema_id'];
                            }
                            if ( isset( $custom_fields['saswp_car_schema_model']) ) {
                                $input1['model'] =    $custom_fields['saswp_car_schema_model'];
                            }
                            if ( isset( $custom_fields['saswp_car_schema_body_type']) ) {
                                $input1['bodyType'] =    $custom_fields['saswp_car_schema_body_type'];
                            }
                            if ( isset( $custom_fields['saswp_car_schema_fuel_efficiency']) ) {
                                $input1['fuelEfficiency'] =    $custom_fields['saswp_car_schema_fuel_efficiency'];
                            }
                            if ( isset( $custom_fields['saswp_car_schema_seating_capacity']) ) {
                                $input1['seatingCapacity'] =    $custom_fields['saswp_car_schema_seating_capacity'];
                            }
                            if ( isset( $custom_fields['saswp_car_schema_number_of_doors']) ) {
                                $input1['numberOfdoors'] =    $custom_fields['saswp_car_schema_number_of_doors'];
                            }
                            if ( isset( $custom_fields['saswp_car_schema_weight']) ) {
                                $input1['weight'] =    $custom_fields['saswp_car_schema_weight'];
                            }
                            if ( isset( $custom_fields['saswp_car_schema_width']) ) {
                                $input1['width'] =    $custom_fields['saswp_car_schema_width'];
                            }
                            if ( isset( $custom_fields['saswp_car_schema_height']) ) {
                                $input1['height'] =    $custom_fields['saswp_car_schema_height'];
                            }
                            if ( isset( $custom_fields['saswp_car_schema_condition']) ) {
                                $input1['itemCondition'] =    $custom_fields['saswp_car_schema_condition'];
                            }
                            if ( isset( $custom_fields['saswp_car_schema_model_date']) ) {
                                $input1['vehicleModelDate'] =    $custom_fields['saswp_car_schema_model_date'];
                            }
                            if ( isset( $custom_fields['saswp_car_schema_manufacturer']) ) {
                                $input1['manufacturer'] =    $custom_fields['saswp_car_schema_manufacturer'];
                            }
                           if ( isset( $custom_fields['saswp_car_schema_url']) ) {
                                $input1['url'] =    saswp_validate_url($custom_fields['saswp_car_schema_url']);
                           }
                           if ( isset( $custom_fields['saswp_car_schema_name']) ) {
                                $input1['name'] =    $custom_fields['saswp_car_schema_name'];
                           }                           
                           if ( isset( $custom_fields['saswp_car_schema_brand_name']) ) {
                                $input1['brand']['name'] =    $custom_fields['saswp_car_schema_brand_name'];
                           }                           
                           if ( isset( $custom_fields['saswp_car_schema_mpn']) ) {
                                $input1['mpn'] =    $custom_fields['saswp_car_schema_mpn'];
                           }                                                                   
                           if ( isset( $custom_fields['saswp_car_schema_description']) ) {
                                $input1['description'] =  wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_car_schema_description'] ));
                           }                    
                           if ( isset( $custom_fields['saswp_car_schema_image']) ) {
                                $input1['image'] =    $custom_fields['saswp_car_schema_image'];
                           }                           
                           if ( isset( $custom_fields['saswp_car_schema_price']) ) {
                                $input1['offers']['@type'] =    'Offer';                                                       
                                $input1['offers']['price'] =    $custom_fields['saswp_car_schema_price'];                                                                             
                           }
                           if ( isset( $custom_fields['saswp_car_schema_availability']) ) {
                                $input1['offers']['availability']     = $custom_fields['saswp_car_schema_availability'];     
                           }

                           if ( isset( $custom_fields['saswp_car_schema_currency']) ) {
                                $input1['offers']['priceCurrency'] =    $custom_fields['saswp_car_schema_currency'];                            
                           }                           
                           if ( isset( $custom_fields['saswp_car_schema_priceValidUntil']) ) {
                                $input1['offers']['priceValidUntil'] =    $custom_fields['saswp_car_schema_priceValidUntil'];                            
                           }                                              
                           if ( isset( $custom_fields['saswp_car_schema_sku']) ) {
                                $input1['sku']                    =    $custom_fields['saswp_car_schema_sku'];
                           }                                  
                           if( isset($custom_fields['saswp_car_schema_high_price']) && isset($custom_fields['saswp_car_schema_low_price']) ){
       
                               $input1['offers']['@type']            = 'AggregateOffer';
                                if( isset( $custom_fields['saswp_car_schema_availability'] ) ) {
                                    $input1['offers']['availability']     = $custom_fields['saswp_car_schema_availability'];
                                }
                               $input1['offers']['highPrice']        = $custom_fields['saswp_car_schema_high_price'];
                               $input1['offers']['lowPrice']         = $custom_fields['saswp_car_schema_low_price'];
       
                               if ( isset( $custom_fields['saswp_car_schema_offer_count']) ) {
                                   $input1['offers']['offerCount']     = $custom_fields['saswp_car_schema_offer_count'];
                               }
                           }                           
                           if ( isset( $custom_fields['saswp_car_schema_rating']) && isset($custom_fields['saswp_car_schema_rating_count']) ) {
                                $input1['aggregateRating']['@type']       = 'aggregateRating';
                                $input1['aggregateRating']['ratingValue'] = $custom_fields['saswp_car_schema_rating'];
                                $input1['aggregateRating']['reviewCount'] = $custom_fields['saswp_car_schema_rating_count'];
                           }
                                                                
                        break;

                        case 'Vehicle':
                            if ( isset( $custom_fields['saswp_vehicle_schema_id']) ) {
                                $input1['@id'] =    get_permalink().$custom_fields['saswp_vehicle_schema_id'];
                            }
                            if ( isset( $custom_fields['saswp_vehicle_schema_model']) ) {
                                $input1['model'] =    $custom_fields['saswp_vehicle_schema_model'];
                            }
                            if ( isset( $custom_fields['saswp_vehicle_schema_body_type']) ) {
                                $input1['bodyType'] =    $custom_fields['saswp_vehicle_schema_body_type'];
                            }
                            if ( isset( $custom_fields['saswp_vehicle_schema_fuel_efficiency']) ) {
                                $input1['fuelEfficiency'] =    $custom_fields['saswp_vehicle_schema_fuel_efficiency'];
                            }
                            if ( isset( $custom_fields['saswp_vehicle_schema_seating_capacity']) ) {
                                $input1['seatingCapacity'] =    $custom_fields['saswp_vehicle_schema_seating_capacity'];
                            }
                            if ( isset( $custom_fields['saswp_vehicle_schema_number_of_doors']) ) {
                                $input1['numberOfdoors'] =    $custom_fields['saswp_vehicle_schema_number_of_doors'];
                            }
                            if ( isset( $custom_fields['saswp_vehicle_schema_weight']) ) {
                                $input1['weight'] =    $custom_fields['saswp_vehicle_schema_weight'];
                            }
                            if ( isset( $custom_fields['saswp_vehicle_schema_width']) ) {
                                $input1['width'] =    $custom_fields['saswp_vehicle_schema_width'];
                            }
                            if ( isset( $custom_fields['saswp_vehicle_schema_height']) ) {
                                $input1['height'] =    $custom_fields['saswp_vehicle_schema_height'];
                            }
                            if ( isset( $custom_fields['saswp_vehicle_schema_manufacturer']) ) {
                                $input1['manufacturer'] =    $custom_fields['saswp_vehicle_schema_manufacturer'];
                            }
                           if ( isset( $custom_fields['saswp_vehicle_schema_url']) ) {
                                $input1['url'] =    saswp_validate_url($custom_fields['saswp_vehicle_schema_url']);
                           }
                           if ( isset( $custom_fields['saswp_vehicle_schema_name']) ) {
                                $input1['name'] =    $custom_fields['saswp_vehicle_schema_name'];
                           }                           
                           if ( isset( $custom_fields['saswp_vehicle_schema_brand_name']) ) {
                                $input1['brand']['name'] =    $custom_fields['saswp_vehicle_schema_brand_name'];
                           }                           
                           if ( isset( $custom_fields['saswp_vehicle_schema_mpn']) ) {
                                $input1['mpn'] =    $custom_fields['saswp_vehicle_schema_mpn'];
                           }                                                                   
                           if ( isset( $custom_fields['saswp_vehicle_schema_description']) ) {
                                $input1['description'] =  wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_vehicle_schema_description'] ));
                           }                    
                           if ( isset( $custom_fields['saswp_vehicle_schema_image']) ) {
                                $input1['image'] =    $custom_fields['saswp_vehicle_schema_image'];
                           }                           
                           if ( isset( $custom_fields['saswp_vehicle_schema_price']) ) {
                                $input1['offers']['price'] =    $custom_fields['saswp_vehicle_schema_price'];                                                                             
                           }
                           if ( isset( $custom_fields['saswp_vehicle_schema_currency']) ) {
                                $input1['offers']['priceCurrency'] =    $custom_fields['saswp_vehicle_schema_currency'];                            
                           }                           
                           if ( isset( $custom_fields['saswp_vehicle_schema_priceValidUntil']) ) {
                                $input1['offers']['priceValidUntil'] =    $custom_fields['saswp_vehicle_schema_priceValidUntil'];                            
                           }                                              
                           if ( isset( $custom_fields['saswp_vehicle_schema_sku']) ) {
                                $input1['sku']                    =    $custom_fields['saswp_vehicle_schema_sku'];
                           }                                  
                           if( isset($custom_fields['saswp_vehicle_schema_high_price']) && isset($custom_fields['saswp_vehicle_schema_low_price']) ){
       
                               $input1['offers']['@type']     = 'AggregateOffer';
                               $input1['offers']['highPrice'] = $custom_fields['saswp_vehicle_schema_high_price'];
                               $input1['offers']['lowPrice']  = $custom_fields['saswp_vehicle_schema_low_price'];
       
                               if ( isset( $custom_fields['saswp_vehicle_schema_offer_count']) ) {
                                   $input1['offers']['offerCount']     = $custom_fields['saswp_vehicle_schema_offer_count'];
                               }
                           }                           
                           if ( isset( $custom_fields['saswp_vehicle_schema_rating']) && isset($custom_fields['saswp_vehicle_schema_rating_count']) ) {
                                $input1['aggregateRating']['@type']       = 'aggregateRating';
                                $input1['aggregateRating']['ratingValue'] = $custom_fields['saswp_vehicle_schema_rating'];
                                $input1['aggregateRating']['reviewCount'] = $custom_fields['saswp_vehicle_schema_rating_count'];
                           }
                                                                
                        break;

                    case 'RentAction':                                                                                                  
                        if ( isset( $custom_fields['saswp_rent_action_id']) ) {
                            $input1['@id'] =    get_permalink().$custom_fields['saswp_rent_action_id'];
                        } 
                        if ( isset( $custom_fields['saswp_rent_action_agent_name']) ) {
                            $input1['agent']['@type'] =    'Person';
                            $input1['agent']['name']  =    $custom_fields['saswp_rent_action_agent_name'];
                        }
                        if ( isset( $custom_fields['saswp_rent_action_land_lord_name']) ) {
                            $input1['landlord']['@type'] =    'Person';
                            $input1['landlord']['name']  =    $custom_fields['saswp_rent_action_land_lord_name'];
                        }
                        if ( isset( $custom_fields['saswp_rent_action_object_name']) ) {
                            $input1['object']['@type'] =    'Residence';
                            $input1['object']['name']  =    $custom_fields['saswp_rent_action_object_name'];
                        }

                    break;

                    case 'RealEstateListing':
                        if ( isset( $custom_fields['saswp_real_estate_listing_id']) ) {
                            $input1['@id'] =    get_permalink().$custom_fields['saswp_real_estate_listing_id'];
                        }                                                                                                   
                        if ( isset( $custom_fields['saswp_real_estate_listing_date_posted']) ) {
                            $input1['datePosted'] =    $custom_fields['saswp_real_estate_listing_date_posted'];
                        }
                        if ( isset( $custom_fields['saswp_real_estate_listing_name']) ) {
                         $input1['name'] =    $custom_fields['saswp_real_estate_listing_name'];
                        }
                        if ( isset( $custom_fields['saswp_real_estate_listing_url']) ) {
                         $input1['url'] =    saswp_validate_url($custom_fields['saswp_real_estate_listing_url']);
                        }                                                
                        if ( isset( $custom_fields['saswp_real_estate_listing_description']) ) {
                         $input1['description'] =    $custom_fields['saswp_real_estate_listing_description'];
                        }
                        if ( isset( $custom_fields['saswp_real_estate_listing_image']) ) {
                         $input1['image'] =    $custom_fields['saswp_real_estate_listing_image'];
                        }                        
                        if ( isset( $custom_fields['saswp_real_estate_listing_availability']) ) {
                         $input1['offers']['availability'] =    $custom_fields['saswp_real_estate_listing_availability'];                         
                        }
                        if ( isset( $custom_fields['saswp_real_estate_listing_price']) ) {
                         $input1['offers']['price'] =    $custom_fields['saswp_real_estate_listing_price'];                                                                                                
                        }
                        if ( isset( $custom_fields['saswp_real_estate_listing_currency']) ) {
                         $input1['offers']['priceCurrency'] =    $custom_fields['saswp_real_estate_listing_currency'];                         
                        }
                        if ( isset( $custom_fields['saswp_real_estate_listing_validfrom']) ) {
                         $input1['offers']['validfrom'] =    $custom_fields['saswp_real_estate_listing_validfrom'];                         
                        }                                                                                          
                        
                        $location = array();
                        
                        if ( isset( $custom_fields['saswp_real_estate_listing_location_name']) ) {

                            $location['@type']  =   'Place';   
                            $location['name']   =   $custom_fields['saswp_real_estate_listing_location_name'];
                            if ( ! empty( $custom_fields['saswp_real_estate_listing_phone']) ) {
                                $location['telephone']      =   $custom_fields['saswp_real_estate_listing_phone'];
                            }
                            if ( ! empty( $custom_fields['saswp_real_estate_listing_streetaddress']) || !empty($custom_fields['saswp_real_estate_listing_locality']) || !empty($custom_fields['saswp_real_estate_listing_region']) || !empty($custom_fields['saswp_real_estate_listing_country']) || !empty($custom_fields['saswp_real_estate_listing_postalcode']) ) {

                                $location['address']['@type']       =   'PostalAddress';
                                if ( ! empty( $custom_fields['saswp_real_estate_listing_streetaddress']) ) {
                                    $location['address']['streetAddress']     =    $custom_fields['saswp_real_estate_listing_streetaddress'];   
                                }

                                if ( ! empty( $custom_fields['saswp_real_estate_listing_locality']) ) {
                                    $location['address']['addressLocality']   =    $custom_fields['saswp_real_estate_listing_locality'];   
                                }

                                if ( ! empty( $custom_fields['saswp_real_estate_listing_region']) ) {
                                    $location['address']['addressRegion']     =    $custom_fields['saswp_real_estate_listing_region'];   
                                }

                                if ( ! empty( $custom_fields['saswp_real_estate_listing_country']) ) {
                                    $location['address']['addressCountry']   =    $custom_fields['saswp_real_estate_listing_country'];   
                                }

                                if ( ! empty( $custom_fields['saswp_real_estate_listing_postalcode']) ) {
                                    $location['address']['postalCode']   =    $custom_fields['saswp_real_estate_listing_postalcode'];   
                                }
                            }

                            $input1['contentLocation'] = $location;
                        }

                        break;    

                        case 'ApartmentComplex':
                            if ( isset( $custom_fields['saswp_apartment_complex_id']) ) {
                                $input1['@id'] =    get_permalink().$custom_fields['saswp_apartment_complex_id'];
                            }                              
                            if ( isset( $custom_fields['saswp_apartment_complex_name']) ) {
                             $input1['name']        =    $custom_fields['saswp_apartment_complex_name'];
                            }
                            if ( isset( $custom_fields['saswp_apartment_complex_url']) ) {
                             $input1['url']         =    saswp_validate_url($custom_fields['saswp_apartment_complex_url']);
                            }                                                
                            if ( isset( $custom_fields['saswp_apartment_complex_description']) ) {
                             $input1['description']  =    $custom_fields['saswp_apartment_complex_description'];
                            }
                            if ( isset( $custom_fields['saswp_apartment_complex_pets_allowed']) ) {
                             $input1['petsAllowed']  =    $custom_fields['saswp_apartment_complex_pets_allowed'];
                            }
                            if ( isset( $custom_fields['saswp_apartment_complex_image']) ) {
                             $input1['image']        =    $custom_fields['saswp_apartment_complex_image'];
                            }                                                                                                                                              
                            
                            $location = array();
                            
                            if ( isset( $custom_fields['saswp_apartment_complex_streetaddress']) ) {
                                                                                                                                           
                                $location   = array(
                                                '@type' => 'PostalAddress',
                                                'streetAddress'   => $custom_fields['saswp_apartment_complex_streetaddress'],
                                                'addressLocality' => $custom_fields['saswp_apartment_complex_locality'],
                                                'addressRegion'   => $custom_fields['saswp_apartment_complex_region'],
                                                'addressCountry'  => $custom_fields['saswp_apartment_complex_country'],
                                                'postalCode'      => $custom_fields['saswp_apartment_complex_postalcode'],  
                                                'telephone'       => $custom_fields['saswp_apartment_complex_phone'],                                
                                    );
                                
    
                                $input1['address'] = $location;
                            }

                            if ( isset( $custom_fields['saswp_apartment_complex_latitude']) && isset($custom_fields['saswp_apartment_complex_longitude']) ) {
                                $input1['geo']['@type']     =    'GeoCoordinates';   
                                $input1['geo']['latitude']  =    $custom_fields['saswp_apartment_complex_latitude'];
                                $input1['geo']['longitude'] =    $custom_fields['saswp_apartment_complex_longitude'];                     
                            }
    
                            break;    

                        case 'PsychologicalTreatment':                                                                                                  
                            if ( isset( $custom_fields['saswp_psychological_treatment_id']) ) {
                                $input1['@id'] =    get_permalink().$custom_fields['saswp_psychological_treatment_id'];
                            } 
                            if ( isset( $custom_fields['saswp_psychological_treatment_name']) ) {
                                $input1['name'] =    $custom_fields['saswp_psychological_treatment_name'];
                            }                            
                            if ( isset( $custom_fields['saswp_psychological_treatment_url']) ) {
                             $input1['url'] =    saswp_validate_url($custom_fields['saswp_psychological_treatment_url']);
                            }                                                
                            if ( isset( $custom_fields['saswp_psychological_treatment_description']) ) {
                             $input1['description'] =    $custom_fields['saswp_psychological_treatment_description'];
                            }
                            if ( isset( $custom_fields['saswp_psychological_treatment_image']) ) {
                             $input1['image'] =    $custom_fields['saswp_psychological_treatment_image'];
                            }                            
                            if ( isset( $custom_fields['saswp_psychological_treatment_drug']) ) {
                                $input1['drug'] =    $custom_fields['saswp_psychological_treatment_drug'];
                            }
                            if ( isset( $custom_fields['saswp_psychological_treatment_body_location']) ) {
                                $input1['bodyLocation'] =    $custom_fields['saswp_psychological_treatment_body_location'];
                            }
                            if ( isset( $custom_fields['saswp_psychological_treatment_preparation']) ) {
                                $input1['preparation'] =    $custom_fields['saswp_psychological_treatment_preparation'];
                            }
                            if ( isset( $custom_fields['saswp_psychological_treatment_followup']) ) {
                                $input1['followup'] =    $custom_fields['saswp_psychological_treatment_followup'];
                            }
                            if ( isset( $custom_fields['saswp_psychological_treatment_how_performed']) ) {
                                $input1['howPerformed'] =    $custom_fields['saswp_psychological_treatment_how_performed'];
                            }
                            if ( isset( $custom_fields['saswp_psychological_treatment_procedure_type']) ) {
                                $input1['procedureType'] =    $custom_fields['saswp_psychological_treatment_procedure_type'];
                            }
                            if ( isset( $custom_fields['saswp_psychological_treatment_medical_code']) ) {
                                $input1['code'] =    $custom_fields['saswp_psychological_treatment_medical_code'];
                            }
                            if ( isset( $custom_fields['saswp_psychological_treatment_additional_type']) ) {
                                $input1['additionalType'] =    $custom_fields['saswp_psychological_treatment_additional_type'];
                            }
    
                            break;            
                
                case 'Service':
                    if ( isset( $custom_fields['saswp_service_schema_id']) ) {
                        $input1['@id'] =    get_permalink().$custom_fields['saswp_service_schema_id'];
                    }
                    if ( isset( $custom_fields['saswp_service_schema_name']) ) {
                      $input1['name'] =    $custom_fields['saswp_service_schema_name'];
                    }
                    if ( isset( $custom_fields['saswp_service_schema_type']) ) {
                      $input1['serviceType'] =    $custom_fields['saswp_service_schema_type'];
                    }
                    if ( isset( $custom_fields['saswp_service_schema_additional_type']) ) {
                        $input1['additionalType'] =    $custom_fields['saswp_service_schema_additional_type'];
                    }
                    if ( isset( $custom_fields['saswp_service_schema_service_output']) ) {
                      $input1['serviceOutput'] =    $custom_fields['saswp_service_schema_service_output'];
                    }
                    if ( isset( $custom_fields['saswp_service_schema_provider_mobility']) ) {
                        $input1['providerMobility'] =    $custom_fields['saswp_service_schema_provider_mobility'];
                    }
                    if ( isset( $custom_fields['saswp_service_schema_provider_type']) && isset($custom_fields['saswp_service_schema_provider_name']) ) {
                      $input1['provider']['@type'] =    $custom_fields['saswp_service_schema_provider_type'];
                      $input1['provider']['name']  =    $custom_fields['saswp_service_schema_provider_name'];
                      
                        if ( isset( $custom_fields['saswp_service_schema_image']) ) {
                            $input1['provider']['image']    =    $custom_fields['saswp_service_schema_image'];
                        }
                    }                                        
                    if ( isset( $custom_fields['saswp_service_schema_price_range']) ) {
                        $input1['provider']['priceRange'] =    $custom_fields['saswp_service_schema_price_range'];
                    }                    
                    if ( isset( $custom_fields['saswp_service_schema_locality']) ) {
                     $input1['provider']['address']['addressLocality'] =    $custom_fields['saswp_service_schema_locality'];
                    }
                    if ( isset( $custom_fields['saswp_service_schema_postal_code']) ) {
                      $input1['provider']['address']['postalCode'] =    $custom_fields['saswp_service_schema_postal_code'];
                    }
                    if ( isset( $custom_fields['saswp_service_schema_telephone']) ) {
                      $input1['provider']['address']['telephone'] =    $custom_fields['saswp_service_schema_telephone'];
                    }          
                    if ( isset( $custom_fields['saswp_service_schema_rating_value']) && isset($custom_fields['saswp_service_schema_rating_count']) ) {
                        $input1['provider']['aggregateRating']['@type']         = 'aggregateRating';                        
                        $input1['provider']['aggregateRating']['ratingValue']   = $custom_fields['saswp_service_schema_rating_value'];
                        $input1['provider']['aggregateRating']['reviewCount']   = $custom_fields['saswp_service_schema_rating_count'];                                
                    }          
                    if ( isset( $custom_fields['saswp_service_schema_description']) ) {
                      $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_service_schema_description'] ));
                    }
                    if ( isset( $custom_fields['saswp_service_schema_area_served']) ) {
                      $input1['areaServed'] =    saswp_explode_comma_seprated($custom_fields['saswp_service_schema_area_served'], 'Place');
                    }
                    if ( isset( $custom_fields['saswp_service_schema_image']) ) {
                        $input1['image']    =    $custom_fields['saswp_service_schema_image'];
                    }
                    if ( isset( $custom_fields['saswp_service_schema_service_offer']) ) {
                      $input1['hasOfferCatalog'] =    $custom_fields['saswp_service_schema_service_offer'];
                    }
                                                                             
                    break;

                    case 'TaxiService':
                        if ( isset( $custom_fields['saswp_taxi_service_schema_id']) ) {
                            $input1['@id'] =    get_permalink().$custom_fields['saswp_taxi_service_schema_id'];
                        }

                        if ( isset( $custom_fields['saswp_taxi_service_schema_name']) ) {
                          $input1['name'] =    $custom_fields['saswp_taxi_service_schema_name'];
                        }
                        if ( isset( $custom_fields['saswp_taxi_service_schema_type']) ) {
                          $input1['serviceType'] =    $custom_fields['saswp_taxi_service_schema_type'];
                        }
                        if ( isset( $custom_fields['saswp_taxi_service_schema_additional_type']) ) {
                            $input1['additionalType'] =    $custom_fields['saswp_taxi_service_schema_additional_type'];
                        }
                        if ( isset( $custom_fields['saswp_taxi_service_schema_service_output']) ) {
                          $input1['serviceOutput'] =    $custom_fields['saswp_taxi_service_schema_service_output'];
                        }
                        if ( isset( $custom_fields['saswp_taxi_service_schema_provider_type']) && isset($custom_fields['saswp_taxi_service_schema_provider_name']) ) {
                          $input1['provider']['@type'] =    $custom_fields['saswp_taxi_service_schema_provider_type'];
                          $input1['provider']['name']  =    $custom_fields['saswp_taxi_service_schema_provider_name'];
                          
                            if ( isset( $custom_fields['saswp_taxi_service_schema_image']) ) {
                                $input1['provider']['image']    =    $custom_fields['saswp_taxi_service_schema_image'];
                            }
                        }                                        
                        if ( isset( $custom_fields['saswp_taxi_service_schema_price_range']) ) {
                            $input1['provider']['priceRange'] =    $custom_fields['saswp_taxi_service_schema_price_range'];
                        }                    
                        if ( isset( $custom_fields['saswp_taxi_service_schema_locality']) ) {
                         $input1['provider']['address']['addressLocality'] =    $custom_fields['saswp_taxi_service_schema_locality'];
                        }
                        if ( isset( $custom_fields['saswp_taxi_service_schema_postal_code']) ) {
                          $input1['provider']['address']['postalCode'] =    $custom_fields['saswp_taxi_service_schema_postal_code'];
                        }
                        if ( isset( $custom_fields['saswp_taxi_service_schema_telephone']) ) {
                          $input1['provider']['address']['telephone'] =    $custom_fields['saswp_taxi_service_schema_telephone'];
                        }                    
                        if ( isset( $custom_fields['saswp_taxi_service_schema_description']) ) {
                          $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_taxi_service_schema_description'] ));
                        }
                        if ( isset( $custom_fields['saswp_taxi_service_schema_area_served']) ) {
                          $input1['areaServed'] =    saswp_explode_comma_seprated($custom_fields['saswp_taxi_service_schema_area_served'], 'Place');
                        }
                        if ( isset( $custom_fields['saswp_taxi_service_schema_image']) ) {
                            $input1['image']    =    $custom_fields['saswp_taxi_service_schema_image'];
                        }
                        if ( isset( $custom_fields['saswp_taxi_service_schema_service_offer']) ) {
                          $input1['hasOfferCatalog'] =    $custom_fields['saswp_taxi_service_schema_service_offer'];
                        }
                                                                                 
                    break;    
                
                case 'VideoObject':
                    if ( isset( $custom_fields['saswp_video_object_id']) ) {
                        $input1['@id'] =    get_permalink().$custom_fields['saswp_video_object_id'];
                    }
                    if ( isset( $custom_fields['saswp_video_object_url']) ) {
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_video_object_url']);
                    }
                    if ( isset( $custom_fields['saswp_video_object_headline']) ) {
                     $input1['headline'] =    $custom_fields['saswp_video_object_headline'];
                    }
                    if ( isset( $custom_fields['saswp_video_object_date_published']) ) {
                     $input1['datePublished'] =    $custom_fields['saswp_video_object_date_published'];
                    }                    
                    if ( isset( $custom_fields['saswp_video_object_date_modified']) ) {
                     $input1['dateModified'] =    $custom_fields['saswp_video_object_date_modified'];
                    }
                    if ( isset( $custom_fields['saswp_video_object_description']) ) {
                     $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_video_object_description'] ));
                    }
                    if ( isset( $custom_fields['saswp_video_object_transcript']) ) {
                    $input1['transcript'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_video_object_transcript'] ));
                    }
                    if ( isset( $custom_fields['saswp_video_object_name']) ) {
                     $input1['name'] =    $custom_fields['saswp_video_object_name'];
                    }
                    if ( isset( $custom_fields['saswp_video_object_duration']) ) {
                        $input1['duration'] =    $custom_fields['saswp_video_object_duration'];
                    }                    
                    if ( isset( $custom_fields['saswp_video_object_upload_date']) ) {
                     $input1['uploadDate'] =    $custom_fields['saswp_video_object_upload_date'];
                    }
                    if ( isset( $custom_fields['saswp_video_object_thumbnail_url']) ) {

                      if ( is_array( $custom_fields['saswp_video_object_thumbnail_url']) ) {
                        $input1['thumbnailUrl'] =    $custom_fields['saswp_video_object_thumbnail_url']['url'];
                      }  

                      if(is_string($custom_fields['saswp_video_object_thumbnail_url']) ) {
                        $input1['thumbnailUrl'] = $custom_fields['saswp_video_object_thumbnail_url'];
                      }
                                           
                    }                                        
                    if ( isset( $custom_fields['saswp_video_object_content_url']) && wp_http_validate_url($custom_fields['saswp_video_object_content_url']) ){
                     $input1['contentUrl'] =    saswp_validate_url($custom_fields['saswp_video_object_content_url']);
                    }
                    if ( isset( $custom_fields['saswp_video_object_embed_url']) && wp_http_validate_url($custom_fields['saswp_video_object_embed_url']) ) {
                     $input1['embedUrl']   =    saswp_validate_url($custom_fields['saswp_video_object_embed_url']);
                    }
                    
                    if ( ! empty( $custom_fields['saswp_video_object_seek_to_seconds']) && !empty($custom_fields['saswp_video_object_seek_to_video_url']) ) {

                        $input1['potentialAction']['@type']             = 'SeekToAction';
                        $input1['potentialAction']['target']            = $custom_fields['saswp_video_object_seek_to_video_url'].'?t'.$custom_fields['saswp_video_object_seek_to_seconds'];
                        $input1['potentialAction']['startOffset-input'] = 'required name=seek_to_second_number';

                    }                    
                    
                    if ( isset( $custom_fields['saswp_video_object_author_type']) ) {
                        $input1['author']['@type'] =    $custom_fields['saswp_video_object_author_type'];
                    }
                    if ( isset( $custom_fields['saswp_video_object_author_name']) ) {
                     $input1['author']['name'] =    $custom_fields['saswp_video_object_author_name'];
                    }
                    if ( isset( $custom_fields['saswp_video_object_author_url']) ) {
                     $input1['author']['url'] =    saswp_validate_url($custom_fields['saswp_video_object_author_url']);
                    }
                    if ( isset( $custom_fields['saswp_video_object_author_description']) ) {
                     $input1['author']['description'] =    $custom_fields['saswp_video_object_author_description'];
                    }
                    if ( isset( $custom_fields['saswp_video_object_author_image']) ) {
                     $input1['author']['image'] =    $custom_fields['saswp_video_object_author_image'];
                    }                      
                    if ( isset( $custom_fields['saswp_video_object_organization_logo']) && isset($custom_fields['saswp_video_object_organization_name']) ) {
                     $input1['publisher']['@type']       =    'Organization';
                     $input1['publisher']['name']        =    $custom_fields['saswp_video_object_organization_name'];
                     $input1['publisher']['logo']        =    $custom_fields['saswp_video_object_organization_logo'];
                    }
                    
                    break;
                    
                    case 'ImageObject':
                    
                    if ( ! empty( $custom_fields['saswpimage_object_id'] ) ) {
                        $input1['@id'] =    get_permalink().$custom_fields['saswpimage_object_id'];
                    }else if( empty( $custom_fields['saswpimage_object_id'] ) ){
                        unset( $input1['@id'] );
                    } 
                    if ( ! empty( $custom_fields['saswpimage_object_url'] ) ) {
                        if( is_array( $custom_fields['saswpimage_object_url'] ) ) {
                            if( ! empty( $custom_fields['saswpimage_object_url']['url'] ) ) {
                                $input1['url'] =    $custom_fields['saswpimage_object_url']['url'];
                            }
                            if( ! empty( $custom_fields['saswpimage_object_url']['width'] ) ) {
                                $input1['width'] =    $custom_fields['saswpimage_object_url']['width'];
                            }
                            if( ! empty( $custom_fields['saswpimage_object_url']['height'] ) ) {
                                $input1['height'] =    $custom_fields['saswpimage_object_url']['height'];
                            }
                        }else{
                            $input1['url'] =    saswp_validate_url($custom_fields['saswpimage_object_url']);
                        }
                    }else if( empty( $custom_fields['saswpimage_object_url'] ) ){
                        unset( $input1['url'] );
                    }
                    if ( ! empty( $custom_fields['saswpimage_object_image'] ) ) {
                        $input1['image'] =    $custom_fields['saswpimage_object_image'];
                    }                    
                    if ( ! empty( $custom_fields['saswpimage_object_date_published'] ) ) {
                        $input1['datePublished'] =    $custom_fields['saswpimage_object_date_published'];
                    }else if( empty( $custom_fields['saswpimage_object_date_published'] ) ){
                        unset( $input1['datePublished'] );   
                    }                    
                    if ( ! empty( $custom_fields['saswpimage_object_date_modified'] ) ) {
                        $input1['dateModified'] =    $custom_fields['saswpimage_object_date_modified'];
                    }else if( empty( $custom_fields['saswpimage_object_date_modified'] ) ){
                        unset( $input1['dateModified'] );   
                    }
                    if ( ! empty( $custom_fields['saswpimage_object_description'] ) ) {
                        $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswpimage_object_description'] ));
                    }else if( empty( $custom_fields['saswpimage_object_description'] ) ){
                        unset( $input1['description'] );   
                    }
                    if ( ! empty( $custom_fields['saswpimage_object_name'] ) ) {
                        $input1['name'] =    $custom_fields['saswpimage_object_name'];
                    }else if( empty( $custom_fields['saswpimage_object_name'] ) ){
                        unset( $input1['name'] );   
                    }
                    if ( ! empty( $custom_fields['saswpimage_object_license'] ) ) {
                        $input1['license'] =  $custom_fields['saswpimage_object_license'];
                    }
                    if ( ! empty( $custom_fields['saswpimage_object_acquire_license_page'] ) ) {
                        $input1['acquireLicensePage'] =  $custom_fields['saswpimage_object_acquire_license_page'];
                    }                    
                    if ( ! empty( $custom_fields['saswpimage_object_upload_date'] ) ) {
                        $input1['uploadDate'] =    $custom_fields['saswpimage_object_upload_date'];
                    }else if( empty( $custom_fields['saswpimage_object_upload_date'] ) ){
                        unset( $input1['uploadDate'] );   
                    }
                    if ( ! empty( $custom_fields['saswpimage_object_thumbnail_url'] ) ) {
                        $input1['thumbnailUrl'] =    saswp_validate_url($custom_fields['saswpimage_object_thumbnail_url']);
                    }                                        
                    if ( ! empty( $custom_fields['saswpimage_object_content_url'] ) ) {
                        $input1['contentUrl'] =    saswp_validate_url($custom_fields['saswpimage_object_content_url']);
                    }else if( empty( $custom_fields['saswpimage_object_content_url'] ) ){
                        unset( $input1['contentUrl'] );   
                    }
                    if ( ! empty( $custom_fields['saswpimage_object_content_location'] ) ) {
                     $input1['contentLocation'] =    $custom_fields['saswpimage_object_content_location'];
                    }
                    if ( isset( $custom_fields['saswpimage_object_author_type']) ) {
                        $input1['author']['@type'] =    $custom_fields['saswpimage_object_author_type'];
                    }
                    if ( isset( $custom_fields['saswpimage_object_author_name']) ) {
                     $input1['author']['name'] =    $custom_fields['saswpimage_object_author_name'];
                    }
                    if ( isset( $custom_fields['saswpimage_object_author_url']) ) {
                     $input1['author']['url'] =    saswp_validate_url($custom_fields['saswpimage_object_author_url']);
                    }
                    if ( isset( $custom_fields['saswpimage_object_author_description']) ) {
                     $input1['author']['description'] =    $custom_fields['saswpimage_object_author_description'];
                    }
                    if ( isset( $custom_fields['saswpimage_object_author_image']) ) {
                     $input1['author']['image'] =    $custom_fields['saswpimage_object_author_image'];
                    }                      
                    if ( ! empty( $custom_fields['saswpimage_object_organization_logo']) && ! empty($custom_fields['saswpimage_object_organization_name'] ) ) {
                     $input1['publisher']['@type']       =    'Organization';
                     $input1['publisher']['name']        =    $custom_fields['saswpimage_object_organization_name'];
                     $input1['publisher']['logo']        =    $custom_fields['saswpimage_object_organization_logo'];
                    }else if( empty( $custom_fields['saswpimage_object_organization_logo']) && empty($custom_fields['saswpimage_object_organization_name'] ) ){
                        unset( $input1['publisher'] );
                    }
                    if( empty( $custom_fields['saswpimage_object_author_type'] ) ) {
                        unset( $input1['author'] );
                    }
                    
                    break;
                
                case 'qanda':
                    
                    if ( isset( $custom_fields['saswp_qa_question_title']) ) {
                     $input1['mainEntity']['name'] =    $custom_fields['saswp_qa_question_title'];
                    }
                    if ( isset( $custom_fields['saswp_qa_question_description']) ) {
                     $input1['mainEntity']['text'] =    $custom_fields['saswp_qa_question_description'];
                    }
                    if ( isset( $custom_fields['saswp_qa_upvote_count']) ) {
                     $input1['mainEntity']['upvoteCount'] =    $custom_fields['saswp_qa_upvote_count'];
                    }
                    if ( isset( $custom_fields['saswp_qa_answer_count']) ) {
                        $input1['mainEntity']['answerCount'] =    $custom_fields['saswp_qa_answer_count'];
                    }
                    
                    if ( isset( $custom_fields['saswp_qa_date_created']) ) {
                     $input1['mainEntity']['dateCreated'] =    $custom_fields['saswp_qa_date_created'];
                    }
                    if ( isset( $custom_fields['saswp_qa_question_author_name']) ) {
                     $input1['mainEntity']['author']['@type'] =    'Person';

                     if ( isset( $custom_fields['saswp_qa_question_author_type']) ) {
                        $input1['mainEntity']['author']['@type'] =    $custom_fields['saswp_qa_question_author_type'];
                     }

                     $input1['mainEntity']['author']['name'] =    $custom_fields['saswp_qa_question_author_name'];
                     
                     if ( isset( $custom_fields['saswp_qa_question_author_url']) ) {
                        $input1['mainEntity']['author']['url'] =    $custom_fields['saswp_qa_question_author_url'];
                     }
                    }
                    if ( isset( $custom_fields['saswp_qa_accepted_answer_text']) ) {
                     $input1['mainEntity']['acceptedAnswer']['@type'] =    'Answer';   
                     $input1['mainEntity']['acceptedAnswer']['text'] =    $custom_fields['saswp_qa_accepted_answer_text'];
                    }
                    
                    if ( isset( $custom_fields['saswp_qa_accepted_answer_date_created']) ) {
                     $input1['mainEntity']['acceptedAnswer']['dateCreated'] =    $custom_fields['saswp_qa_accepted_answer_date_created'];
                    }
                    if ( isset( $custom_fields['saswp_qa_accepted_answer_upvote_count']) ) {
                     $input1['mainEntity']['acceptedAnswer']['upvoteCount'] =    $custom_fields['saswp_qa_accepted_answer_upvote_count'];
                    }
                    if ( isset( $custom_fields['saswp_qa_accepted_answer_url']) ) {
                     $input1['mainEntity']['acceptedAnswer']['url'] =    $custom_fields['saswp_qa_accepted_answer_url'];
                    }
                    
                    if ( isset( $custom_fields['saswp_qa_accepted_author_name']) ) {
                     $input1['mainEntity']['acceptedAnswer']['author']['name'] =    $custom_fields['saswp_qa_accepted_author_name'];
                    }                                        
                    if ( isset( $custom_fields['saswp_qa_suggested_answer_text']) ) {
                     $input1['mainEntity']['suggestedAnswer']['@type'] =    'Answer';   
                     $input1['mainEntity']['suggestedAnswer']['text'] =    $custom_fields['saswp_qa_suggested_answer_text'];
                    }
                    if ( isset( $custom_fields['saswp_qa_suggested_answer_date_created']) ) {
                     $input1['mainEntity']['suggestedAnswer']['dateCreated'] =    $custom_fields['saswp_qa_suggested_answer_date_created'];
                    }
                    
                    if ( isset( $custom_fields['saswp_qa_suggested_answer_upvote_count']) ) {
                     $input1['mainEntity']['suggestedAnswer']['upvoteCount'] =    $custom_fields['saswp_qa_suggested_answer_upvote_count'];
                    }
                    if ( isset( $custom_fields['saswp_qa_suggested_answer_url']) ) {
                     $input1['mainEntity']['suggestedAnswer']['url'] =    $custom_fields['saswp_qa_suggested_answer_url'];
                    }
                    if ( isset( $custom_fields['saswp_qa_suggested_author_name']) ) {
                     $input1['mainEntity']['suggestedAnswer']['author']['name'] =    $custom_fields['saswp_qa_suggested_author_name'];
                    }
                                        
                    break;
                    
                case 'TVSeries':      
                    
                    if ( isset( $custom_fields['saswp_tvseries_schema_id']) ) {
                        $input1['@id'] =    get_permalink().$custom_fields['saswp_tvseries_schema_id'];
                    } 
                    if ( isset( $custom_fields['saswp_tvseries_schema_name']) ) {
                     $input1['name'] =    $custom_fields['saswp_tvseries_schema_name'];
                    }
                    if ( isset( $custom_fields['saswp_tvseries_schema_description']) ) {
                     $input1['description'] =  wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_tvseries_schema_description'] ));
                    }
                    if ( isset( $custom_fields['saswp_tvseries_schema_image']) ) {
                     $input1['image'] =    $custom_fields['saswp_tvseries_schema_image'];
                    }
                    if ( isset( $custom_fields['saswp_tvseries_schema_author_type']) ) {
                        $input1['author']['@type'] =    $custom_fields['saswp_tvseries_schema_author_type'];
                    }
                    if ( isset( $custom_fields['saswp_tvseries_schema_author_name']) ) {
                     $input1['author']['name'] =    $custom_fields['saswp_tvseries_schema_author_name'];
                    }
                    if ( isset( $custom_fields['saswp_tvseries_schema_duration']) ) {
                     $input1['timeRequired'] =    $custom_fields['saswp_tvseries_schema_duration'];
                    }
                    if ( isset( $custom_fields['saswp_tvseries_schema_url']) ) {
                     $input1['url'] =    $custom_fields['saswp_tvseries_schema_url'];
                    }
                    if ( isset( $custom_fields['saswp_tvseries_schema_nos']) ) {
                     $input1['numberOfSeasons'] =    $custom_fields['saswp_tvseries_schema_nos'];
                    }
                    if ( isset( $custom_fields['saswp_tvseries_schema_noe']) ) {
                     $input1['numberOfEpisodes'] =    $custom_fields['saswp_tvseries_schema_noe'];
                    }
                    if ( isset( $custom_fields['saswp_tvseries_schema_date_published']) ) {
                     $input1['datePublished'] =    $custom_fields['saswp_tvseries_schema_date_published'];
                    }
                    if ( isset( $custom_fields['saswp_tvseries_schema_date_modified']) ) {
                     $input1['dateModified'] =    $custom_fields['saswp_tvseries_schema_date_modified'];
                    }
                    if ( ! empty( $custom_fields['saswp_tvseries_schema_trailer'] ) && is_array( $custom_fields['saswp_tvseries_schema_trailer'] ) ) {
                        foreach ( $custom_fields['saswp_tvseries_schema_trailer'] as $tr_key => $trailer) {
                            if ( ! empty( $trailer ) && is_array( $trailer ) ) {
                                $input1['trailer'][]       =   $trailer;    
                            }
                        } 
                    }
                    if ( ! empty( $custom_fields['saswp_tvseries_schema_subject_of'] ) && is_array( $custom_fields['saswp_tvseries_schema_subject_of'] ) ) {
                        foreach ( $custom_fields['saswp_tvseries_schema_subject_of'] as $so_key => $subject) {
                            if ( ! empty( $subject ) && is_array( $subject ) ) {
                                $input1['subjectOf'][]       =   $subject;    
                            }
                        } 
                    }
                
                break;
                
                case 'TouristAttraction':      
                      
                    if ( isset( $custom_fields['saswp_ta_schema_id']) ) {
                        $input1['@id'] =    get_permalink().$custom_fields['saswp_ta_schema_id'];
                    }
                    if ( isset( $custom_fields['saswp_ta_schema_name']) ) {
                     $input1['name'] =    $custom_fields['saswp_ta_schema_name'];
                    }
                    if ( isset( $custom_fields['saswp_ta_schema_description']) ) {
                     $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_ta_schema_description'] ));
                    }
                    if ( isset( $custom_fields['saswp_ta_schema_image']) ) {
                     $input1['image'] =    $custom_fields['saswp_ta_schema_image'];
                    }
                    if ( isset( $custom_fields['saswp_ta_schema_url']) ) {
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_ta_schema_url']);
                    }
                    if ( isset( $custom_fields['saswp_ta_schema_is_acceesible_free']) ) {
                     $input1['isAccessibleForFree'] =    $custom_fields['saswp_ta_schema_is_acceesible_free'];
                    }
                    if ( isset( $custom_fields['saswp_ta_schema_locality']) ) {
                     $input1['address']['addressLocality'] =    $custom_fields['saswp_ta_schema_locality'];
                    }
                    if ( isset( $custom_fields['saswp_ta_schema_region']) ) {
                     $input1['address']['addressRegion'] =    $custom_fields['saswp_ta_schema_region'];
                    }
                    if ( isset( $custom_fields['saswp_ta_schema_country']) ) {
                     $input1['address']['addressCountry'] =    $custom_fields['saswp_ta_schema_country'];
                    }
                    if ( isset( $custom_fields['saswp_ta_schema_postal_code']) ) {
                     $input1['address']['postalCode'] =    $custom_fields['saswp_ta_schema_postal_code'];
                    }
                    if ( isset( $custom_fields['saswp_ta_schema_latitude']) && isset($custom_fields['saswp_ta_schema_longitude']) ) {                        
                     $input1['geo']['@type']     =    'GeoCoordinates';   
                     $input1['geo']['latitude']  =    $custom_fields['saswp_ta_schema_latitude'];
                     $input1['geo']['longitude'] =    $custom_fields['saswp_ta_schema_longitude'];                     
                    }
                    
                break;
                
                case 'FAQ':   
                    
                    if ( isset( $custom_fields['saswp_faq_id']) ) {
                     $input1['@id'] =     get_permalink().$custom_fields['saswp_faq_id'];
                    }
                    if ( isset( $custom_fields['saswp_faq_headline']) ) {
                     $input1['headline'] =    $custom_fields['saswp_faq_headline'];
                    }
                    if ( isset( $custom_fields['saswp_faq_keywords']) ) {
                     $input1['keywords'] =    $custom_fields['saswp_faq_keywords'];
                    }
                    if ( isset( $custom_fields['saswp_faq_date_created']) ) {
                     $input1['datePublished'] =    $custom_fields['saswp_faq_date_created'];
                    }
                    if ( isset( $custom_fields['saswp_faq_date_published']) ) {
                     $input1['dateModified'] =    $custom_fields['saswp_faq_date_published'];
                    }
                    if ( isset( $custom_fields['saswp_faq_date_modified']) ) {
                     $input1['dateCreated'] =    $custom_fields['saswp_faq_date_modified'];
                    }                    

                    if ( ! empty( $custom_fields['saswp_faq_author_global_mapping']) ) {
                        $input1['author'] = array();
                        if ( ! empty( $custom_fields['saswp_faq_author_global_mapping']) ) {
                            $input1['author']['@type'] =   "Person";
                        }

                        if ( ! empty( $custom_fields['saswp_faq_author_global_mapping']['name']) ) {
                            $input1['author']['name'] =    $custom_fields['saswp_faq_author_global_mapping']['name'];
                        }

                        if ( ! empty( $custom_fields['saswp_faq_author_global_mapping']['url']) ) {
                            $input1['author']['url'] =    $custom_fields['saswp_faq_author_global_mapping']['url'];
                        }

                        if ( ! empty( $custom_fields['saswp_faq_author_global_mapping']['description']) ) {
                            $input1['author']['description'] =    $custom_fields['saswp_faq_author_global_mapping']['description'];
                        }

                        if ( ! empty( $custom_fields['saswp_faq_author_global_mapping']['custom_fields']['honorificsuffix'][0]) ) {
                            $input1['author']['honorificSuffix'] =    $custom_fields['saswp_faq_author_global_mapping']['custom_fields']['honorificsuffix'][0];
                        }

                        if ( ! empty( $custom_fields['saswp_faq_author_global_mapping']['custom_fields']['knowsabout'][0]) ) {
                            $input1['author']['knowsAbout'] =   explode(',', $custom_fields['saswp_faq_author_global_mapping']['custom_fields']['knowsabout'][0]);
                        }

                        $sameas = array();
                        if ( ! empty( $custom_fields['saswp_faq_author_global_mapping']['custom_fields']['team_facebook'][0]) ) {
                            $sameas[] =  $custom_fields['saswp_faq_author_global_mapping']['custom_fields']['team_facebook'][0];
                        }

                        if ( ! empty( $custom_fields['saswp_faq_author_global_mapping']['custom_fields']['team_twitter'][0]) ) {
                            $sameas[] =  $custom_fields['saswp_faq_author_global_mapping']['custom_fields']['team_twitter'][0];
                        }

                        if ( ! empty( $custom_fields['saswp_faq_author_global_mapping']['custom_fields']['team_linkedin'][0]) ) {
                            $sameas[] =   $custom_fields['saswp_faq_author_global_mapping']['custom_fields']['team_linkedin'][0];
                        }

                        if ( ! empty( $custom_fields['saswp_faq_author_global_mapping']['custom_fields']['team_instagram'][0]) ) {
                            $sameas[] =   $custom_fields['saswp_faq_author_global_mapping']['custom_fields']['team_instagram'][0];
                        }

                        if ( ! empty( $custom_fields['saswp_faq_author_global_mapping']['custom_fields']['team_youtube'][0]) ) {
                            $sameas[] =   $custom_fields['saswp_faq_author_global_mapping']['custom_fields']['team_youtube'][0];
                        }
                        if($sameas){
                            $input1['author']['sameAs'] = $sameas;
                        }

                        if ( ! empty( $custom_fields['saswp_faq_author_global_mapping']['custom_fields']['alumniof'][0]) ) {
                            $str =  $custom_fields['saswp_faq_author_global_mapping']['custom_fields']['alumniof'][0];
                            $itemlist = explode(",", $str);
                            foreach ( $itemlist as $key => $list){
                                $vnewarr['@type'] = 'Organization';
                                $vnewarr['Name']   = $list;   
                                $input1['author']['alumniOf'][] = $vnewarr;
                            }
                        }
                    }else{
                       
                        if ( isset( $custom_fields['saswp_faq_author_type']) ) {
                            $input1['author']['@type'] =    $custom_fields['saswp_faq_author_type'];
                        }
                        if ( isset( $custom_fields['saswp_faq_author_name']) ) {
                         $input1['author']['name'] =    $custom_fields['saswp_faq_author_name'];
                        }
                        if ( isset( $custom_fields['saswp_faq_author_honorific_suffix']) ) {
                            $input1['author']['honorificSuffix'] =    $custom_fields['saswp_faq_author_honorific_suffix'];
                        }
                        if ( isset( $custom_fields['saswp_faq_author_description']) ) {
                            $input1['author']['description'] =    $custom_fields['saswp_faq_author_description'];
                        }
                        if ( isset( $custom_fields['saswp_faq_author_url']) ) {
                            $input1['author']['url'] =    $custom_fields['saswp_faq_author_url'];
                        }
                        if ( isset( $custom_fields['saswp_faq_author_image']) ) {
                            $input1['author']['Image']['url'] =    $custom_fields['saswp_faq_author_image'];
                        }
                        if ( isset( $custom_fields['saswp_faq_author_jobtitle']) ) {
                            $input1['author']['JobTitle'] =    $custom_fields['saswp_faq_author_jobtitle'];
                        }
                    }
                    
                    if ( ! empty( $custom_fields['saswp_faq_about']) && isset($custom_fields['saswp_faq_about']) ) {         
                        $explode_about = explode(',', $custom_fields['saswp_faq_about']);
                        if ( ! empty( $explode_about) ) {
                            $about_arr = array();
                            foreach( $explode_about as $val){
                                $about_arr[] = array(
                                            '@type' => 'Thing',
                                            'name'  => $val
                                );
                            }
                            $input1['about'] = $about_arr;
                        }                            
                    }  

                    if ( isset( $custom_fields['saswp_faq_main_entity']) ) {                                                                                                                    
                        $input1['mainEntity'] =    $custom_fields['saswp_faq_main_entity'];
                    }
                                                             
                break;
                
                case 'TouristDestination':      
                      
                    if ( isset( $custom_fields['saswp_td_schema_id']) ) {
                        $input1['@id'] =    get_permalink().$custom_fields['saswp_td_schema_id'];
                    }
                    if ( isset( $custom_fields['saswp_td_schema_name']) ) {
                     $input1['name'] =    $custom_fields['saswp_td_schema_name'];
                    }
                    if ( isset( $custom_fields['saswp_td_schema_description']) ) {
                     $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_td_schema_description'] ));
                    }
                    if ( isset( $custom_fields['saswp_td_schema_image']) ) {
                     $input1['image'] =    $custom_fields['saswp_td_schema_image'];
                    }
                    if ( isset( $custom_fields['saswp_td_schema_url']) ) {
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_td_schema_url']);
                    }
                    if ( isset( $custom_fields['saswp_td_schema_locality']) ) {
                     $input1['address']['addressLocality'] =    $custom_fields['saswp_td_schema_locality'];
                    }
                    if ( isset( $custom_fields['saswp_td_schema_region']) ) {
                     $input1['address']['addressRegion'] =    $custom_fields['saswp_td_schema_region'];
                    }
                    if ( isset( $custom_fields['saswp_td_schema_country']) ) {
                     $input1['address']['addressCountry'] =    $custom_fields['saswp_td_schema_country'];
                    }
                    if ( isset( $custom_fields['saswp_td_schema_postal_code']) ) {
                     $input1['address']['postalCode'] =    $custom_fields['saswp_td_schema_postal_code'];
                    }
                    if ( isset( $custom_fields['saswp_td_schema_latitude']) && isset($custom_fields['saswp_td_schema_longitude']) ) {                        
                     $input1['geo']['@type']     =    'GeoCoordinates';   
                     $input1['geo']['latitude']  =    $custom_fields['saswp_td_schema_latitude'];
                     $input1['geo']['longitude'] =    $custom_fields['saswp_td_schema_longitude'];                     
                    }
                    
                break;
                
                case 'LandmarksOrHistoricalBuildings':      
                    
                    if ( isset( $custom_fields['saswp_lorh_schema_id']) ) {
                        $input1['@id'] =    get_permalink().$custom_fields['saswp_lorh_schema_id'];
                    }
                    if ( isset( $custom_fields['saswp_lorh_schema_name']) ) {
                     $input1['name'] =    $custom_fields['saswp_lorh_schema_name'];
                    }
                    if ( isset( $custom_fields['saswp_lorh_schema_description']) ) {
                     $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_lorh_schema_description'] ));
                    }
                    if ( isset( $custom_fields['saswp_lorh_schema_image']) ) {
                     $input1['image'] =    $custom_fields['saswp_lorh_schema_image'];
                    }
                    if ( isset( $custom_fields['saswp_lorh_schema_url']) ) {
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_lorh_schema_url']);
                    }
                    if ( isset( $custom_fields['saswp_lorh_schema_hasmap']) ) {
                     $input1['hasMap'] =    $custom_fields['saswp_lorh_schema_hasmap'];
                    }
                    if ( isset( $custom_fields['saswp_lorh_schema_is_acceesible_free']) ) {
                     $input1['isAccessibleForFree'] =    $custom_fields['saswp_lorh_schema_is_acceesible_free'];
                    }
                    if ( isset( $custom_fields['saswp_lorh_schema_maximum_a_capacity']) ) {
                     $input1['maximumAttendeeCapacity'] =    $custom_fields['saswp_lorh_schema_maximum_a_capacity'];
                    }
                    if ( isset( $custom_fields['saswp_lorh_schema_locality']) ) {
                     $input1['address']['addressLocality'] =    $custom_fields['saswp_lorh_schema_locality'];
                    }
                    if ( isset( $custom_fields['saswp_lorh_schema_region']) ) {
                     $input1['address']['addressRegion'] =    $custom_fields['saswp_lorh_schema_region'];
                    }
                    if ( isset( $custom_fields['saswp_lorh_schema_country']) ) {
                     $input1['address']['addressCountry'] =    $custom_fields['saswp_lorh_schema_country'];
                    }
                    if ( isset( $custom_fields['saswp_lorh_schema_postal_code']) ) {
                     $input1['address']['postalCode'] =    $custom_fields['saswp_lorh_schema_postal_code'];
                    }
                    if ( isset( $custom_fields['saswp_lorh_schema_latitude']) && isset($custom_fields['saswp_lorh_schema_longitude']) ) {                        
                     $input1['geo']['@type']     =    'GeoCoordinates';   
                     $input1['geo']['latitude']  =    $custom_fields['saswp_lorh_schema_latitude'];
                     $input1['geo']['longitude'] =    $custom_fields['saswp_lorh_schema_longitude'];                     
                    }
                    
                break;
                
                case 'HinduTemple':      
                    if ( isset( $custom_fields['saswp_hindutemple_schema_id']) ) {
                        $input1['@id'] =    get_permalink().$custom_fields['saswp_hindutemple_schema_id'];
                    }
                    if ( isset( $custom_fields['saswp_hindutemple_schema_name']) ) {
                     $input1['name'] =    $custom_fields['saswp_hindutemple_schema_name'];
                    }
                    if ( isset( $custom_fields['saswp_hindutemple_schema_description']) ) {
                     $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_hindutemple_schema_description'] ));
                    }
                    if ( isset( $custom_fields['saswp_hindutemple_schema_image']) ) {
                     $input1['image'] =    $custom_fields['saswp_hindutemple_schema_image'];
                    }
                    if ( isset( $custom_fields['saswp_hindutemple_schema_url']) ) {
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_hindutemple_schema_url']);
                    }
                    if ( isset( $custom_fields['saswp_hindutemple_schema_hasmap']) ) {
                     $input1['hasMap'] =    $custom_fields['saswp_hindutemple_schema_hasmap'];
                    }
                    if ( isset( $custom_fields['saswp_hindutemple_schema_is_accesible_free']) ) {
                     $input1['isAccessibleForFree'] =    $custom_fields['saswp_hindutemple_schema_is_accesible_free'];
                    }
                    if ( isset( $custom_fields['saswp_hindutemple_schema_maximum_a_capacity']) ) {
                     $input1['maximumAttendeeCapacity'] =    $custom_fields['saswp_hindutemple_schema_maximum_a_capacity'];
                    }
                    if ( isset( $custom_fields['saswp_hindutemple_schema_locality']) ) {
                     $input1['address']['addressLocality'] =    $custom_fields['saswp_hindutemple_schema_locality'];
                    }
                    if ( isset( $custom_fields['saswp_hindutemple_schema_region']) ) {
                     $input1['address']['addressRegion'] =    $custom_fields['saswp_hindutemple_schema_region'];
                    }
                    if ( isset( $custom_fields['saswp_hindutemple_schema_country']) ) {
                     $input1['address']['addressCountry'] =    $custom_fields['saswp_hindutemple_schema_country'];
                    }
                    if ( isset( $custom_fields['saswp_hindutemple_schema_postal_code']) ) {
                     $input1['address']['postalCode'] =    $custom_fields['saswp_hindutemple_schema_postal_code'];
                    }
                    if ( isset( $custom_fields['saswp_hindutemple_schema_latitude']) && isset($custom_fields['saswp_hindutemple_schema_longitude']) ) {                        
                     $input1['geo']['@type']     =    'GeoCoordinates';   
                     $input1['geo']['latitude']  =    $custom_fields['saswp_hindutemple_schema_latitude'];
                     $input1['geo']['longitude'] =    $custom_fields['saswp_hindutemple_schema_longitude'];                     
                    }
                    
                break;

                case 'BuddhistTemple':      
                    if ( isset( $custom_fields['saswp_buddhisttemple_schema_id']) ) {
                        $input1['@id'] =    get_permalink().$custom_fields['saswp_buddhisttemple_schema_id'];
                    }                      
                    if ( isset( $custom_fields['saswp_buddhisttemple_schema_name']) ) {
                     $input1['name'] =    $custom_fields['saswp_buddhisttemple_schema_name'];
                    }
                    if ( isset( $custom_fields['saswp_buddhisttemple_schema_description']) ) {
                     $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_buddhisttemple_schema_description'] ));
                    }
                    if ( isset( $custom_fields['saswp_buddhisttemple_schema_image']) ) {
                     $input1['image'] =    $custom_fields['saswp_buddhisttemple_schema_image'];
                    }
                    if ( isset( $custom_fields['saswp_buddhisttemple_schema_url']) ) {
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_buddhisttemple_schema_url']);
                    }
                    if ( isset( $custom_fields['saswp_buddhisttemple_schema_hasmap']) ) {
                     $input1['hasMap'] =    $custom_fields['saswp_buddhisttemple_schema_hasmap'];
                    }
                    if ( isset( $custom_fields['saswp_buddhisttemple_schema_is_accesible_free']) ) {
                     $input1['isAccessibleForFree'] =    $custom_fields['saswp_buddhisttemple_schema_is_accesible_free'];
                    }
                    if ( isset( $custom_fields['saswp_buddhisttemple_schema_maximum_a_capacity']) ) {
                     $input1['maximumAttendeeCapacity'] =    $custom_fields['saswp_buddhisttemple_schema_maximum_a_capacity'];
                    }
                    if ( isset( $custom_fields['saswp_buddhisttemple_schema_locality']) ) {
                     $input1['address']['addressLocality'] =    $custom_fields['saswp_buddhisttemple_schema_locality'];
                    }
                    if ( isset( $custom_fields['saswp_buddhisttemple_schema_region']) ) {
                     $input1['address']['addressRegion'] =    $custom_fields['saswp_buddhisttemple_schema_region'];
                    }
                    if ( isset( $custom_fields['saswp_buddhisttemple_schema_country']) ) {
                     $input1['address']['addressCountry'] =    $custom_fields['saswp_buddhisttemple_schema_country'];
                    }
                    if ( isset( $custom_fields['saswp_buddhisttemple_schema_postal_code']) ) {
                     $input1['address']['postalCode'] =    $custom_fields['saswp_buddhisttemple_schema_postal_code'];
                    }
                    if ( isset( $custom_fields['saswp_buddhisttemple_schema_latitude']) && isset($custom_fields['saswp_buddhisttemple_schema_longitude']) ) {                        
                     $input1['geo']['@type']     =    'GeoCoordinates';   
                     $input1['geo']['latitude']  =    $custom_fields['saswp_buddhisttemple_schema_latitude'];
                     $input1['geo']['longitude'] =    $custom_fields['saswp_buddhisttemple_schema_longitude'];                     
                    }
                    
                break;
                
                case 'Church':      
                    if ( isset( $custom_fields['saswp_church_schema_id']) ) {
                        $input1['@id'] =    get_permalink().$custom_fields['saswp_church_schema_id'];
                    }
                    if ( isset( $custom_fields['saswp_church_schema_name']) ) {
                     $input1['name'] =    $custom_fields['saswp_church_schema_name'];
                    }
                    if ( isset( $custom_fields['saswp_church_schema_description']) ) {
                     $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_church_schema_description'] ));
                    }
                    if ( isset( $custom_fields['saswp_church_schema_image']) ) {
                     $input1['image'] =    $custom_fields['saswp_church_schema_image'];
                    }
                    if ( isset( $custom_fields['saswp_church_schema_url']) ) {
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_church_schema_url']);
                    }
                    if ( isset( $custom_fields['saswp_church_schema_hasmap']) ) {
                     $input1['hasMap'] =    $custom_fields['saswp_church_schema_hasmap'];
                    }
                    if ( isset( $custom_fields['saswp_church_schema_is_accesible_free']) ) {
                     $input1['isAccessibleForFree'] =    $custom_fields['saswp_church_schema_is_accesible_free'];
                    }
                    if ( isset( $custom_fields['saswp_church_schema_maximum_a_capacity']) ) {
                     $input1['maximumAttendeeCapacity'] =    $custom_fields['saswp_church_schema_maximum_a_capacity'];
                    }
                    if ( isset( $custom_fields['saswp_church_schema_locality']) ) {
                     $input1['address']['addressLocality'] =    $custom_fields['saswp_church_schema_locality'];
                    }
                    if ( isset( $custom_fields['saswp_church_schema_region']) ) {
                     $input1['address']['addressRegion'] =    $custom_fields['saswp_church_schema_region'];
                    }
                    if ( isset( $custom_fields['saswp_church_schema_country']) ) {
                     $input1['address']['addressCountry'] =    $custom_fields['saswp_church_schema_country'];
                    }
                    if ( isset( $custom_fields['saswp_church_schema_postal_code']) ) {
                     $input1['address']['postalCode'] =    $custom_fields['saswp_church_schema_postal_code'];
                    }
                    if ( isset( $custom_fields['saswp_church_schema_latitude']) && isset($custom_fields['saswp_church_schema_longitude']) ) {                        
                     $input1['geo']['@type']     =    'GeoCoordinates';   
                     $input1['geo']['latitude']  =    $custom_fields['saswp_church_schema_latitude'];
                     $input1['geo']['longitude'] =    $custom_fields['saswp_church_schema_longitude'];                     
                    }
                    
                break;
                
                case 'Mosque':      
                    if ( isset( $custom_fields['saswp_mosque_schema_id']) ) {
                        $input1['@id'] =    get_permalink().$custom_fields['saswp_mosque_schema_id'];
                    }                      
                    if ( isset( $custom_fields['saswp_mosque_schema_name']) ) {
                     $input1['name'] =    $custom_fields['saswp_mosque_schema_name'];
                    }
                    if ( isset( $custom_fields['saswp_mosque_schema_description']) ) {
                     $input1['description'] =  wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_mosque_schema_description'] ));
                    }
                    if ( isset( $custom_fields['saswp_mosque_schema_image']) ) {
                     $input1['image'] =    $custom_fields['saswp_mosque_schema_image'];
                    }
                    if ( isset( $custom_fields['saswp_mosque_schema_url']) ) {
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_mosque_schema_url']);
                    }
                    if ( isset( $custom_fields['saswp_mosque_schema_hasmap']) ) {
                     $input1['hasMap'] =    $custom_fields['saswp_mosque_schema_hasmap'];
                    }
                    if ( isset( $custom_fields['saswp_mosque_schema_is_accesible_free']) ) {
                     $input1['isAccessibleForFree'] =    $custom_fields['saswp_mosque_schema_is_accesible_free'];
                    }
                    if ( isset( $custom_fields['saswp_mosque_schema_maximum_a_capacity']) ) {
                     $input1['maximumAttendeeCapacity'] =    $custom_fields['saswp_mosque_schema_maximum_a_capacity'];
                    }
                    if ( isset( $custom_fields['saswp_mosque_schema_locality']) ) {
                     $input1['address']['addressLocality'] =    $custom_fields['saswp_mosque_schema_locality'];
                    }
                    if ( isset( $custom_fields['saswp_mosque_schema_region']) ) {
                     $input1['address']['addressRegion'] =    $custom_fields['saswp_mosque_schema_region'];
                    }
                    if ( isset( $custom_fields['saswp_mosque_schema_country']) ) {
                     $input1['address']['addressCountry'] =    $custom_fields['saswp_mosque_schema_country'];
                    }
                    if ( isset( $custom_fields['saswp_mosque_schema_postal_code']) ) {
                     $input1['address']['postalCode'] =    $custom_fields['saswp_mosque_schema_postal_code'];
                    }
                    if ( isset( $custom_fields['saswp_mosque_schema_latitude']) && isset($custom_fields['saswp_mosque_schema_longitude']) ) {                        
                     $input1['geo']['@type']     =    'GeoCoordinates';   
                     $input1['geo']['latitude']  =    $custom_fields['saswp_mosque_schema_latitude'];
                     $input1['geo']['longitude'] =    $custom_fields['saswp_mosque_schema_longitude'];                     
                    }
                    
                break;
                
                case 'Person':      
                    
                    if ( isset( $custom_fields['saswp_person_schema_id']) ) {
                     $input1['@id'] =    get_permalink().$custom_fields['saswp_person_schema_id'];
                    }
                    if ( isset( $custom_fields['saswp_person_schema_name']) ) {
                     $input1['name'] =    $custom_fields['saswp_person_schema_name'];
                    }
                    if ( isset( $custom_fields['saswp_person_schema_family_name']) ) {
                     $input1['familyName'] =    $custom_fields['saswp_person_schema_family_name'];
                    }
                    if ( isset( $custom_fields['saswp_person_schema_spouse']) ) {
                        $input1['spouse']['@type'] = 'Person';
                        $input1['spouse']['name']  =    $custom_fields['saswp_person_schema_spouse'];
                    }
                    if ( isset( $custom_fields['saswp_person_schema_description']) ) {
                     $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_person_schema_description'] ));
                    }
                    if ( isset( $custom_fields['saswp_person_schema_url']) ) {
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_person_schema_url']);
                    }
                    if ( isset( $custom_fields['saswp_person_schema_street_address']) ) {
                     $input1['address']['streetAddress'] =    $custom_fields['saswp_person_schema_street_address'];
                    }
                    if ( isset( $custom_fields['saswp_person_schema_locality']) ) {
                     $input1['address']['addressLocality'] =    $custom_fields['saswp_person_schema_locality'];
                    }
                    if ( isset( $custom_fields['saswp_person_schema_region']) ) {
                     $input1['address']['addressRegion'] =    $custom_fields['saswp_person_schema_region'];
                    }
                    if ( isset( $custom_fields['saswp_person_schema_postal_code']) ) {
                      $input1['address']['postalCode']  =    $custom_fields['saswp_person_schema_postal_code'];
                    }
                    if ( isset( $custom_fields['saswp_person_schema_country']) ) {
                     $input1['address']['addressCountry'] =    $custom_fields['saswp_person_schema_country'];
                    }
                    if ( isset( $custom_fields['saswp_person_schema_b_street_address']) ) {
                        $input1['location']['@type'] = 'Place';
                        $input1['location']['address']['streetAddress'] =    $custom_fields['saswp_person_schema_b_street_address'];
                    }
                    if ( isset( $custom_fields['saswp_person_schema_b_locality']) ) {
                        $input1['location']['@type'] = 'Place';
                        $input1['location']['address']['addressLocality'] =    $custom_fields['saswp_person_schema_b_locality'];
                    }
                    if ( isset( $custom_fields['saswp_person_schema_b_region']) ) {
                        $input1['location']['@type'] = 'Place';
                        $input1['location']['address']['addressRegion'] =    $custom_fields['saswp_person_schema_b_region'];
                    }
                    if ( isset( $custom_fields['saswp_person_schema_b_postal_code']) ) {
                        $input1['location']['@type'] = 'Place';
                        $input1['location']['address']['postalCode']  =    $custom_fields['saswp_person_schema_b_postal_code'];
                    }
                    if ( isset( $custom_fields['saswp_person_schema_b_country']) ) {
                        $input1['location']['@type'] = 'Place';
                        $input1['location']['address']['addressCountry'] =    $custom_fields['saswp_person_schema_b_country'];
                    }
                    if ( isset( $custom_fields['saswp_person_schema_email']) ) {
                     $input1['email'] =    $custom_fields['saswp_person_schema_email'];
                    }
                    if ( isset( $custom_fields['saswp_person_schema_telephone']) ) {
                     $input1['telephone'] =    $custom_fields['saswp_person_schema_telephone'];
                    }
                    if ( isset( $custom_fields['saswp_person_schema_gender']) ) {
                     $input1['gender'] =    $custom_fields['saswp_person_schema_gender'];
                    }
                    if ( isset( $custom_fields['saswp_person_schema_date_of_birth']) ) {
                     $input1['birthDate'] =    $custom_fields['saswp_person_schema_date_of_birth'];
                    }
                    if ( isset( $custom_fields['saswp_person_schema_date_of_death']) ) {
                        $input1['deathDate'] =    $custom_fields['saswp_person_schema_date_of_death'];
                    }                    
                    if ( isset( $custom_fields['saswp_person_schema_nationality']) ) {
                     $input1['nationality'] =    $custom_fields['saswp_person_schema_nationality'];
                    }
                    if ( isset( $custom_fields['saswp_person_schema_image']) ) {
                     $input1['image'] =    $custom_fields['saswp_person_schema_image'];
                    }
                    if ( isset( $custom_fields['saswp_person_schema_job_title']) ) {
                     $input1['jobTitle'] =    $custom_fields['saswp_person_schema_job_title'];
                    }

                    if ( isset( $custom_fields['saswp_person_schema_company']) ) {                        
                        $input1['worksFor']['@type']       = 'Organization';
                        $input1['worksFor']['name']        = $custom_fields['saswp_person_schema_company'];

                        if ( isset( $custom_fields['saswp_person_schema_website']) ) {
                            $input1['worksFor']['url']       = $custom_fields['saswp_person_schema_website'];
                        }
                    }

                    if ( isset( $custom_fields['saswp_person_schema_award']) ) {
                        $input1['award'] =    $custom_fields['saswp_person_schema_award'];
                    }
                    if ( isset( $custom_fields['saswp_person_schema_brand']) ) {
                        $input1['brand'] =    $custom_fields['saswp_person_schema_brand'];
                    }

                    if ( isset( $custom_fields['saswp_person_schema_honorific_prefix']) ) {
                        $input1['honorificPrefix'] =    $custom_fields['saswp_person_schema_honorific_prefix'];
                    }
                    if ( isset( $custom_fields['saswp_person_schema_honorific_suffix']) ) {
                        $input1['honorificSuffix'] =    $custom_fields['saswp_person_schema_honorific_suffix'];
                    }
                    if ( isset( $custom_fields['saswp_person_schema_qualifications']) ) {
                        $input1['hasCredential'] =    $custom_fields['saswp_person_schema_qualifications'];
                    }                    
                    if ( isset( $custom_fields['saswp_person_schema_affiliation']) ) {
                        $input1['affiliation'] =    $custom_fields['saswp_person_schema_affiliation'];
                    }
                    if ( isset( $custom_fields['saswp_person_schema_alumniof']) ) {
                        $input1['alumniOf'] =    $custom_fields['saswp_person_schema_alumniof'];
                    }
                    if ( isset( $custom_fields['saswp_person_schema_occupation_name']) ) {
                        $input1['hasOccupation']['name'] =    $custom_fields['saswp_person_schema_occupation_name'];
                    }
                    if ( isset( $custom_fields['saswp_person_schema_occupation_description']) ) {
                        $input1['hasOccupation']['description'] =    $custom_fields['saswp_person_schema_occupation_description'];
                    }
                    if ( isset( $custom_fields['saswp_person_schema_occupation_city']) ) {
                        $input1['hasOccupation']['occupationLocation']['@type'] = 'City'; 
                        $input1['hasOccupation']['occupationLocation']['name']  =    $custom_fields['saswp_person_schema_occupation_city'];
                    }
                    if ( isset( $custom_fields['saswp_person_schema_estimated_salary']) ) {
                        $input1['hasOccupation']['estimatedSalary']['@type']     =  'MonetaryAmountDistribution';
                        $input1['hasOccupation']['estimatedSalary']['name']      =  'base';
                        $input1['hasOccupation']['estimatedSalary']['currency']  =  $custom_fields['saswp_person_schema_salary_currency'];
                        $input1['hasOccupation']['estimatedSalary']['duration']  =  $custom_fields['saswp_person_schema_salary_duration'];
                        
                        $input1['hasOccupation']['estimatedSalary']['percentile10']  =  $custom_fields['saswp_person_schema_salary_percentile10'];
                        $input1['hasOccupation']['estimatedSalary']['percentile25']  =  $custom_fields['saswp_person_schema_salary_percentile25'];
                        $input1['hasOccupation']['estimatedSalary']['median']        =  $custom_fields['saswp_person_schema_salary_median'];
                        $input1['hasOccupation']['estimatedSalary']['percentile75']  =  $custom_fields['saswp_person_schema_salary_percentile75'];
                        $input1['hasOccupation']['estimatedSalary']['percentile90']  =  $custom_fields['saswp_person_schema_salary_percentile90'];
                    }
                    if ( isset( $custom_fields['saswp_person_schema_salary_last_reviewed']) ) {
                        $input1['hasOccupation']['mainEntityOfPage']['@type']         = 'WebPage'; 
                        $input1['hasOccupation']['mainEntityOfPage']['lastReviewed']  =    $custom_fields['saswp_person_schema_salary_last_reviewed'];
                    }                    

                    $sameas = array();
                    if ( isset( $custom_fields['saswp_person_schema_website']) ) {
                        $sameas[] =    $custom_fields['saswp_person_schema_website'];
                    }
                    if ( isset( $custom_fields['saswp_person_schema_facebook']) ) {
                        $sameas[] =    $custom_fields['saswp_person_schema_facebook'];
                    }
                    if ( isset( $custom_fields['saswp_person_schema_twitter']) ) {
                        $sameas[] =    $custom_fields['saswp_person_schema_twitter'];
                    }
                    if ( isset( $custom_fields['saswp_person_schema_linkedin']) ) {
                        $sameas[] =    $custom_fields['saswp_person_schema_linkedin'];
                    }
                    if ( isset( $custom_fields['saswp_person_schema_youtube']) ) {
                        $sameas[] =    $custom_fields['saswp_person_schema_youtube'];
                    }
                    if ( isset( $custom_fields['saswp_person_schema_instagram']) ) {
                        $sameas[] =    $custom_fields['saswp_person_schema_instagram'];
                    }
                    if ( isset( $custom_fields['saswp_person_schema_snapchat']) ) {
                        $sameas[] =    $custom_fields['saswp_person_schema_snapchat'];
                    }
                    if ( isset( $custom_fields['saswp_person_schema_threads']) ) {
                        $sameas[] =    $custom_fields['saswp_person_schema_threads'];
                    }
                    if ( isset( $custom_fields['saswp_person_schema_mastodon']) ) {
                        $sameas[] =    $custom_fields['saswp_person_schema_mastodon'];
                    }
                    if ( isset( $custom_fields['saswp_person_schema_vibehut']) ) {
                        $sameas[] =    $custom_fields['saswp_person_schema_vibehut'];
                    }
                    if($sameas){
                        $input1['sameAs'] = $sameas;
                    }
                    
                break;
                                
                case 'Apartment':      

                    if ( isset( $custom_fields['saswp_apartment_schema_id']) ) {
                        $input1['@id'] =    get_permalink().$custom_fields['saswp_apartment_schema_id'];
                    }                       
                    if ( isset( $custom_fields['saswp_apartment_schema_name']) ) {
                     $input1['name'] =    $custom_fields['saswp_apartment_schema_name'];
                    }
                    if ( isset( $custom_fields['saswp_apartment_schema_url']) ) {
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_apartment_schema_url']);
                    }
                    if ( isset( $custom_fields['saswp_apartment_schema_image']) ) {
                     $input1['image'] =    $custom_fields['saswp_apartment_schema_image'];
                    }
                    if ( isset( $custom_fields['saswp_apartment_schema_description']) ) {
                     $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_apartment_schema_description'] ));
                    }
                    if ( isset( $custom_fields['saswp_apartment_schema_numberofrooms']) ) {
                     $input1['numberOfRooms'] =    $custom_fields['saswp_apartment_schema_numberofrooms'];
                    }
                    if ( isset( $custom_fields['saswp_apartment_schema_country']) ) {
                     $input1['address']['addressCountry'] =    $custom_fields['saswp_apartment_schema_country'];
                    }
                    if ( isset( $custom_fields['saswp_apartment_schema_locality']) ) {
                     $input1['address']['addressLocality'] =    $custom_fields['saswp_apartment_schema_locality'];
                    }
                    if ( isset( $custom_fields['saswp_apartment_schema_region']) ) {
                     $input1['address']['addressRegion'] =    $custom_fields['saswp_apartment_schema_region'];
                    }
                    if ( isset( $custom_fields['saswp_apartment_schema_postalcode']) ) {
                     $input1['address']['postalCode'] =    $custom_fields['saswp_apartment_schema_postalcode'];
                    }
                    if ( isset( $custom_fields['saswp_apartment_schema_telephone']) ) {
                     $input1['telephone'] =    $custom_fields['saswp_apartment_schema_telephone'];
                    }
                    if ( isset( $custom_fields['saswp_apartment_schema_latitude']) && isset($custom_fields['saswp_apartment_schema_longitude']) ) {                        
                     $input1['geo']['@type']     =    'GeoCoordinates';   
                     $input1['geo']['latitude']  =    $custom_fields['saswp_apartment_schema_latitude'];
                     $input1['geo']['longitude'] =    $custom_fields['saswp_apartment_schema_longitude'];                     
                    }
                    
                break;
                
                case 'House':      
                    if ( isset( $custom_fields['saswp_house_schema_id']) ) {
                        $input1['@id'] =    get_permalink().$custom_fields['saswp_house_schema_id'];
                    }  
                    if ( isset( $custom_fields['saswp_house_schema_name']) ) {
                     $input1['name'] =    $custom_fields['saswp_house_schema_name'];
                    }
                    if ( isset( $custom_fields['saswp_house_schema_url']) ) {
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_house_schema_url']);
                    }
                    if ( isset( $custom_fields['saswp_house_schema_image']) ) {
                     $input1['image'] =    $custom_fields['saswp_house_schema_image'];
                    }
                    if ( isset( $custom_fields['saswp_house_schema_description']) ) {
                     $input1['description'] =  wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_house_schema_description'] ));
                    }
                    if ( isset( $custom_fields['saswp_house_schema_pets_allowed']) ) {
                     $input1['petsAllowed'] =    $custom_fields['saswp_house_schema_pets_allowed'];
                    }
                    if ( isset( $custom_fields['saswp_house_schema_country']) ) {
                     $input1['address']['addressCountry'] =    $custom_fields['saswp_house_schema_country'];
                    }
                    if ( isset( $custom_fields['saswp_house_schema_locality']) ) {
                     $input1['address']['addressLocality'] =    $custom_fields['saswp_house_schema_locality'];
                    }
                    if ( isset( $custom_fields['saswp_house_schema_region']) ) {
                     $input1['address']['addressRegion'] =    $custom_fields['saswp_house_schema_region'];
                    }
                    if ( isset( $custom_fields['saswp_house_schema_postalcode']) ) {
                     $input1['address']['postalCode'] =    $custom_fields['saswp_house_schema_postalcode'];
                    }
                    if ( isset( $custom_fields['saswp_house_schema_telephone']) ) {
                     $input1['telephone'] =    $custom_fields['saswp_house_schema_telephone'];
                    }
                    if ( isset( $custom_fields['saswp_house_schema_hasmap']) ) {
                     $input1['hasMap'] =    $custom_fields['saswp_house_schema_hasmap'];
                    }
                    if ( isset( $custom_fields['saswp_house_schema_floor_size']) ) {
                     $input1['floorSize'] =    $custom_fields['saswp_house_schema_floor_size'];
                    }
                    if ( isset( $custom_fields['saswp_house_schema_no_of_rooms']) ) {
                     $input1['numberOfRooms'] =    $custom_fields['saswp_house_schema_no_of_rooms'];
                    }
                    if ( isset( $custom_fields['saswp_house_schema_latitude']) && isset($custom_fields['saswp_house_schema_longitude']) ) {                        
                     $input1['geo']['@type']     =    'GeoCoordinates';   
                     $input1['geo']['latitude']  =    $custom_fields['saswp_house_schema_latitude'];
                     $input1['geo']['longitude'] =    $custom_fields['saswp_house_schema_longitude'];                     
                    }
                    
                break;
                
                case 'SingleFamilyResidence':      
                    if ( isset( $custom_fields['saswp_sfr_schema_id']) ) {
                        $input1['@id'] =    get_permalink().$custom_fields['saswp_sfr_schema_id'];
                    }                        
                    if ( isset( $custom_fields['saswp_sfr_schema_name']) ) {
                     $input1['name'] =    $custom_fields['saswp_sfr_schema_name'];
                    }
                    if ( isset( $custom_fields['saswp_sfr_schema_url']) ) {
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_sfr_schema_url']);
                    }
                    if ( isset( $custom_fields['saswp_sfr_schema_image']) ) {
                     $input1['image'] =    $custom_fields['saswp_sfr_schema_image'];
                    }
                    if ( isset( $custom_fields['saswp_sfr_schema_description']) ) {
                     $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_sfr_schema_description'] ));
                    }
                    if ( isset( $custom_fields['saswp_sfr_schema_numberofrooms']) ) {
                     $input1['numberOfRooms'] =    $custom_fields['saswp_sfr_schema_numberofrooms'];
                    }
                    if ( isset( $custom_fields['saswp_sfr_schema_pets_allowed']) ) {
                     $input1['petsAllowed'] =    $custom_fields['saswp_sfr_schema_pets_allowed'];
                    }
                    if ( isset( $custom_fields['saswp_sfr_schema_country']) ) {
                     $input1['address']['addressCountry'] =    $custom_fields['saswp_sfr_schema_country'];
                    }
                    if ( isset( $custom_fields['saswp_sfr_schema_locality']) ) {
                     $input1['address']['addressLocality'] =    $custom_fields['saswp_sfr_schema_locality'];
                    }
                    if ( isset( $custom_fields['saswp_sfr_schema_region']) ) {
                     $input1['address']['addressRegion'] =    $custom_fields['saswp_sfr_schema_region'];
                    }
                    if ( isset( $custom_fields['saswp_sfr_schema_postalcode']) ) {
                     $input1['address']['postalCode'] =    $custom_fields['saswp_sfr_schema_postalcode'];
                    }
                    if ( isset( $custom_fields['saswp_sfr_schema_telephone']) ) {
                     $input1['telephone'] =    $custom_fields['saswp_sfr_schema_telephone'];
                    }
                    if ( isset( $custom_fields['saswp_sfr_schema_hasmap']) ) {
                     $input1['hasMap'] =    $custom_fields['saswp_sfr_schema_hasmap'];
                    }
                    if ( isset( $custom_fields['saswp_sfr_schema_floor_size']) ) {
                     $input1['floorSize'] =    $custom_fields['saswp_sfr_schema_floor_size'];
                    }
                    if ( isset( $custom_fields['saswp_sfr_schema_no_of_rooms']) ) {
                     $input1['numberOfRooms'] =    $custom_fields['saswp_sfr_schema_no_of_rooms'];
                    }
                    if ( isset( $custom_fields['saswp_sfr_schema_latitude']) && isset($custom_fields['saswp_sfr_schema_longitude']) ) {                        
                     $input1['geo']['@type']     =    'GeoCoordinates';   
                     $input1['geo']['latitude']  =    $custom_fields['saswp_sfr_schema_latitude'];
                     $input1['geo']['longitude'] =    $custom_fields['saswp_sfr_schema_longitude'];                     
                    }
                    
                break;
                
                case 'VideoGame':      
                      
                    if ( isset( $custom_fields['saswp_vg_schema_id']) ) {
                        $input1['@id'] =    get_permalink().$custom_fields['saswp_vg_schema_id'];
                    }
                    if ( isset( $custom_fields['saswp_vg_schema_name']) ) {
                     $input1['name'] =    $custom_fields['saswp_vg_schema_name'];
                    }
                    if ( isset( $custom_fields['saswp_vg_schema_url']) ) {
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_vg_schema_url']);
                    }
                    if ( isset( $custom_fields['saswp_vg_schema_image']) ) {
                     $input1['image'] =    $custom_fields['saswp_vg_schema_image'];
                    }
                    if ( isset( $custom_fields['saswp_vg_schema_description']) ) {
                     $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_vg_schema_description'] ));
                    }
                    if ( isset( $custom_fields['saswp_vg_schema_operating_system']) ) {
                     $input1['operatingSystem'] =    $custom_fields['saswp_vg_schema_operating_system'];
                    }
                    if ( isset( $custom_fields['saswp_vg_schema_application_category']) ) {
                     $input1['applicationCategory'] =    $custom_fields['saswp_vg_schema_application_category'];
                    }
                    if ( isset( $custom_fields['saswp_vg_schema_author_type']) ) {
                        $input1['author']['@type'] =    $custom_fields['saswp_vg_schema_author_type'];
                    }
                    if ( isset( $custom_fields['saswp_vg_schema_author_name']) ) {
                     $input1['author']['name'] =    $custom_fields['saswp_vg_schema_author_name'];
                    }
                    if ( isset( $custom_fields['saswp_vg_schema_price']) ) {
                     $input1['offers']['price'] =    $custom_fields['saswp_vg_schema_price'];
                    }
                    if ( isset( $custom_fields['saswp_vg_schema_price_currency']) ) {
                     $input1['offers']['priceCurrency'] =    $custom_fields['saswp_vg_schema_price_currency'];
                    }
                    if ( isset( $custom_fields['saswp_vg_schema_price_availability']) ) {
                     $input1['offers']['availability'] =    $custom_fields['saswp_vg_schema_price_availability'];
                    }
                    if ( isset( $custom_fields['saswp_vg_schema_publisher']) ) {
                     $input1['publisher'] =    $custom_fields['saswp_vg_schema_publisher'];
                    }
                    if ( isset( $custom_fields['saswp_vg_schema_genre']) ) {
                     $input1['genre'] =    $custom_fields['saswp_vg_schema_genre'];
                    }
                    if ( isset( $custom_fields['saswp_vg_schema_processor_requirements']) ) {
                     $input1['processorRequirements'] =    $custom_fields['saswp_vg_schema_processor_requirements'];
                    }
                    if ( isset( $custom_fields['saswp_vg_schema_memory_requirements']) ) {
                     $input1['memoryRequirements'] =    $custom_fields['saswp_vg_schema_memory_requirements'];
                    }
                    if ( isset( $custom_fields['saswp_vg_schema_storage_requirements']) ) {
                     $input1['storageRequirements'] =    $custom_fields['saswp_vg_schema_storage_requirements'];
                    }
                    if ( isset( $custom_fields['saswp_vg_schema_game_platform']) ) {
                     $input1['gamePlatform'] =    $custom_fields['saswp_vg_schema_game_platform'];
                    }
                    if ( isset( $custom_fields['saswp_vg_schema_cheat_code']) ) {
                     $input1['cheatCode'] =    $custom_fields['saswp_vg_schema_cheat_code'];
                    }
                    if ( isset( $custom_fields['saswp_vg_schema_file_size']) ) {
                        $input1['fileSize'] =    $custom_fields['saswp_vg_schema_file_size'];
                    }

                    if ( isset( $custom_fields['saswp_vg_schema_rating']) && 
                        isset($custom_fields['saswp_vg_schema_review_count']) ) {

                        if($custom_fields['saswp_vg_schema_rating'] > 5){
                            $input1['aggregateRating']['@type']         = 'aggregateRating';
                            $input1['aggregateRating']['worstRating']   =   0;
                            $input1['aggregateRating']['bestRating']    =   100;
                            $input1['aggregateRating']['ratingValue']   = $custom_fields['saswp_vg_schema_rating'];
                            $input1['aggregateRating']['ratingCount']   = $custom_fields['saswp_vg_schema_review_count'];
                        }else{
                            $input1['aggregateRating']['@type']         = 'aggregateRating';                        
                            $input1['aggregateRating']['ratingValue']   = $custom_fields['saswp_vg_schema_rating'];
                            $input1['aggregateRating']['reviewCount']   = $custom_fields['saswp_vg_schema_review_count'];   
                        }
                    }
                    
                break;
                
                case 'JobPosting':  
                    
                    if ( isset( $custom_fields['saswp_jobposting_schema_id']) ) {
                        $input1['@id'] =    get_permalink().$custom_fields['saswp_jobposting_schema_id'];
                    }
                    if ( isset( $custom_fields['saswp_jobposting_schema_industry']) ) {
                        $input1['industry']             =    $custom_fields['saswp_jobposting_schema_industry'];
                    }if ( isset( $custom_fields['saswp_jobposting_schema_occupational_category']) ) {
                        $input1['occupationalCategory'] =    $custom_fields['saswp_jobposting_schema_occupational_category'];
                    }  
                    if ( isset( $custom_fields['saswp_jobposting_schema_title']) ) {
                     $input1['title'] =    $custom_fields['saswp_jobposting_schema_title'];
                    }
                    if ( isset( $custom_fields['saswp_jobposting_schema_description']) ) {
                     $input1['description'] =    wp_kses($custom_fields['saswp_jobposting_schema_description'], $allowed_html);
                    }
                    if ( isset( $custom_fields['saswp_jobposting_schema_url']) ) {
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_jobposting_schema_url']);
                    }
                    if ( isset( $custom_fields['saswp_jobposting_schema_dateposted']) ) {
                     $input1['datePosted'] =    $custom_fields['saswp_jobposting_schema_dateposted'];
                    }
                    if ( isset( $custom_fields['saswp_jobposting_schema_direct_apply']) ) {
                        $input1['directApply'] =    $custom_fields['saswp_jobposting_schema_direct_apply'];
                    }
                    if ( isset( $custom_fields['saswp_jobposting_schema_validthrough']) && $custom_fields['saswp_jobposting_schema_validthrough'] !='' ){
                     $input1['validThrough'] =    $custom_fields['saswp_jobposting_schema_validthrough'];
                    }
                    if ( isset( $custom_fields['saswp_jobposting_schema_employment_type']) ) {
                     $input1['employmentType'] =    $custom_fields['saswp_jobposting_schema_employment_type'];
                    }
                    if ( isset( $custom_fields['saswp_jobposting_schema_jobimmediatestart']) ) {
                     $input1['jobImmediateStart'] =    $custom_fields['saswp_jobposting_schema_jobimmediatestart'];
                    }
                    if ( isset( $custom_fields['saswp_jobposting_schema_ho_name']) ) {
                     $input1['hiringOrganization']['name'] =    $custom_fields['saswp_jobposting_schema_ho_name'];
                    }
                    if ( isset( $custom_fields['saswp_jobposting_schema_ho_url']) ) {
                     $input1['hiringOrganization']['sameAs'] =    saswp_validate_url($custom_fields['saswp_jobposting_schema_ho_url']);
                    }
                    if ( isset( $custom_fields['saswp_jobposting_schema_ho_logo']) ) {
                     $input1['hiringOrganization']['logo'] =    $custom_fields['saswp_jobposting_schema_ho_logo'];
                    }
                    if ( isset( $custom_fields['saswp_jobposting_schema_applicant_location_requirements']) ) {
                     $input1['applicantLocationRequirements']['@type'] = 'Country';
                     $input1['applicantLocationRequirements']['name']  = $custom_fields['saswp_jobposting_schema_applicant_location_requirements'];
                    }
                    if ( isset( $custom_fields['saswp_jobposting_schema_job_location_type']) ) {
                     $input1['jobLocationType']  = $custom_fields['saswp_jobposting_schema_job_location_type'];
                    }
                    if ( isset( $custom_fields['saswp_jobposting_schema_street_address']) ) {
                     $input1['jobLocation']['address']['streetAddress'] =    $custom_fields['saswp_jobposting_schema_street_address'];
                    }
                    if ( isset( $custom_fields['saswp_jobposting_schema_locality']) ) {
                     $input1['jobLocation']['address']['addressLocality'] =    $custom_fields['saswp_jobposting_schema_locality'];
                    }
                    if ( isset( $custom_fields['saswp_jobposting_schema_region']) ) {
                     $input1['jobLocation']['address']['addressRegion'] =    $custom_fields['saswp_jobposting_schema_region'];
                    }
                    if ( isset( $custom_fields['saswp_jobposting_schema_postalcode']) ) {
                     $input1['jobLocation']['address']['postalCode'] =    $custom_fields['saswp_jobposting_schema_postalcode'];
                    }
                    if ( isset( $custom_fields['saswp_jobposting_schema_country']) ) {
                     $input1['jobLocation']['address']['addressCountry'] =    $custom_fields['saswp_jobposting_schema_country'];
                    }
                    if ( isset( $custom_fields['saswp_jobposting_schema_latitude']) && isset($custom_fields['saswp_jobposting_schema_longitude']) ) {                        
                     $input1['jobLocation']['geo']['@type']     =    'GeoCoordinates';   
                     $input1['jobLocation']['geo']['latitude']  =    $custom_fields['saswp_jobposting_schema_latitude'];
                     $input1['jobLocation']['geo']['longitude'] =    $custom_fields['saswp_jobposting_schema_longitude'];                     
                    }
                    if ( isset( $custom_fields['saswp_jobposting_schema_bs_currency']) ) {
                     $input1['baseSalary']['currency'] =    $custom_fields['saswp_jobposting_schema_bs_currency'];
                    }
                    if ( isset( $custom_fields['saswp_jobposting_schema_bs_value']) ) {
                     $input1['baseSalary']['value']['value'] =    $custom_fields['saswp_jobposting_schema_bs_value'];
                    }
                    if ( isset( $custom_fields['saswp_jobposting_schema_bs_min_value']) ) {
                        $input1['baseSalary']['value']['minValue'] =    $custom_fields['saswp_jobposting_schema_bs_min_value'];
                    }
                    if ( isset( $custom_fields['saswp_jobposting_schema_bs_max_value']) ) {
                        $input1['baseSalary']['value']['maxValue'] =    $custom_fields['saswp_jobposting_schema_bs_max_value'];
                    }
                    if ( isset( $custom_fields['saswp_jobposting_schema_bs_unittext']) ) {
                        $input1['baseSalary']['value']['unitText'] =    $custom_fields['saswp_jobposting_schema_bs_unittext'];
                    }                    
                    if ( isset( $custom_fields['saswp_jobposting_schema_es_currency']) ) {
                        $input1['estimatedSalary']['currency'] =    $custom_fields['saswp_jobposting_schema_es_currency'];
                    }
                    if ( isset( $custom_fields['saswp_jobposting_schema_es_value']) ) {
                        $input1['estimatedSalary']['value']['value'] =    $custom_fields['saswp_jobposting_schema_es_value'];
                    }
                    if ( isset( $custom_fields['saswp_jobposting_schema_es_min_value']) ) {
                        $input1['estimatedSalary']['value']['minValue'] =    $custom_fields['saswp_jobposting_schema_es_min_value'];
                    }
                    if ( isset( $custom_fields['saswp_jobposting_schema_es_max_value']) ) {
                        $input1['estimatedSalary']['value']['maxValue'] =    $custom_fields['saswp_jobposting_schema_es_max_value'];
                    }
                    if ( isset( $custom_fields['saswp_jobposting_schema_es_unittext']) ) {
                    $input1['estimatedSalary']['value']['unitText'] =    $custom_fields['saswp_jobposting_schema_es_unittext'];
                    }                    
                    if ( isset( $custom_fields['saswp_jobposting_schema_validthrough'])  && $custom_fields['saswp_jobposting_schema_validthrough'] !='' && gmdate('Y-m-d',strtotime($custom_fields['saswp_jobposting_schema_validthrough'])) < gmdate('Y-m-d') ){
                        $input1 = array();    
                    }
                    
                break;
                
                case 'Trip':      
                    
                    if ( isset( $custom_fields['saswp_trip_schema_id']) ) {
                        $input1['@id'] =    get_permalink().$custom_fields['saswp_trip_schema_id'];
                    }
                    if ( isset( $custom_fields['saswp_trip_schema_name']) ) {
                     $input1['name'] =    $custom_fields['saswp_trip_schema_name'];
                    }
                    if ( isset( $custom_fields['saswp_trip_schema_description']) ) {
                     $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_trip_schema_description'] ));
                    }
                    if ( isset( $custom_fields['saswp_trip_schema_url']) ) {
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_trip_schema_url']);
                    }
                    if ( isset( $custom_fields['saswp_trip_schema_image']) ) {
                     $input1['image'] =    $custom_fields['saswp_trip_schema_image'];
                    }
                    
                break;

                case 'BoatTrip':      
                        if ( isset( $custom_fields['saswp_boat_trip_schema_id']) ) {
                            $input1['@id'] =    get_permalink().$custom_fields['saswp_boat_trip_schema_id'];
                        }
                       if ( isset( $custom_fields['saswp_boat_trip_schema_name']) ) {
                        $input1['name'] =    $custom_fields['saswp_boat_trip_schema_name'];
                       }
                       if ( isset( $custom_fields['saswp_boat_trip_schema_description']) ) {
                        $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_boat_trip_schema_description'] ));
                       }
                       if ( isset( $custom_fields['saswp_boat_trip_schema_url']) ) {
                        $input1['url'] =    saswp_validate_url($custom_fields['saswp_boat_trip_schema_url']);
                       }
                       if ( isset( $custom_fields['saswp_boat_trip_schema_image']) ) {
                        $input1['image'] =    $custom_fields['saswp_boat_trip_schema_image'];
                       }
                       if ( isset( $custom_fields['saswp_boat_trip_schema_arrival_time']) ) {
                        $input1['arrivalTime']          =    $custom_fields['saswp_boat_trip_schema_arrival_time'];
                       }
                       if ( isset( $custom_fields['saswp_boat_trip_schema_departure_time']) ) {
                        $input1['departureTime']        =    $custom_fields['saswp_boat_trip_schema_departure_time'];
                       }
                       if ( isset( $custom_fields['saswp_boat_trip_schema_arrival_boat_terminal']) ) {
                        $input1['arrivalBoatTerminal']  =    $custom_fields['saswp_boat_trip_schema_arrival_boat_terminal'];
                       }
                       if ( isset( $custom_fields['saswp_boat_trip_schema_departure_boat_terminal']) ) {
                        $input1['departureBoatTerminal'] =    $custom_fields['saswp_boat_trip_schema_departure_boat_terminal'];
                       }
                    
                break;
                
                case 'MedicalCondition':      
                    if ( isset( $custom_fields['saswp_mc_schema_id']) ) {
                        $input1['@id'] =    get_permalink().$custom_fields['saswp_mc_schema_id'];
                    }
                    if ( isset( $custom_fields['saswp_mc_schema_name']) ) {
                     $input1['name'] =    $custom_fields['saswp_mc_schema_name'];
                    }
                    if ( isset( $custom_fields['saswp_mc_schema_alternate_name']) ) {
                     $input1['alternateName'] =    $custom_fields['saswp_mc_schema_alternate_name'];
                    }
                    if ( isset( $custom_fields['saswp_mc_schema_description']) ) {
                     $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_mc_schema_description'] ));
                    }
                    if ( isset( $custom_fields['saswp_mc_schema_image']) ) {
                     $input1['image'] =    $custom_fields['saswp_mc_schema_image'];
                    }
                    if ( isset( $custom_fields['saswp_mc_schema_anatomy_name']) ) {
                     $input1['associatedAnatomy']['name'] =    $custom_fields['saswp_mc_schema_anatomy_name'];
                    }
                    if ( isset( $custom_fields['saswp_mc_schema_medical_code']) ) {
                     $input1['code']['code'] =    $custom_fields['saswp_mc_schema_medical_code'];
                    }
                    if ( isset( $custom_fields['saswp_mc_schema_coding_system']) ) {
                     $input1['code']['codingSystem'] =    $custom_fields['saswp_mc_schema_coding_system'];
                    }
                    if ( isset( $custom_fields['saswp_mc_schema_drug']) ) {
                        $input1['drug'] =    $custom_fields['saswp_mc_schema_drug'];
                    }
                    if( isset($custom_fields['saswp_mc_schema_possible_treatment_name']) || isset($custom_fields['saswp_mc_schema_possible_treatment_performed']) ){
                        $input1['possibleTreatment']['name']         =    $custom_fields['saswp_mc_schema_possible_treatment_name'];
                        $input1['possibleTreatment']['howPerformed'] =    $custom_fields['saswp_mc_schema_possible_treatment_performed'];
                    }
                    if( isset($custom_fields['saswp_mc_schema_primary_prevention_name']) || isset($custom_fields['saswp_mc_schema_primary_prevention_performed']) ){
                        $input1['primaryPrevention']['name']         =    $custom_fields['saswp_mc_schema_primary_prevention_name'];
                        $input1['primaryPrevention']['howPerformed'] =    $custom_fields['saswp_mc_schema_primary_prevention_performed'];
                    }                    
                    
                break;
                
                case 'TouristTrip':
                    if ( isset( $custom_fields['saswp_tt_schema_id']) ) {
                        $input1['@id'] =    get_permalink().$custom_fields['saswp_tt_schema_id'];
                    }
                    if ( isset( $custom_fields['saswp_tt_schema_name']) ) {
                        $input1['name'] =    $custom_fields['saswp_tt_schema_name'];
                    }
                    if ( isset( $custom_fields['saswp_tt_schema_description']) ) {
                        $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_tt_schema_description'] ));
                    }
                    if ( isset( $custom_fields['saswp_tt_schema_ttype']) ) {
                        if(is_string($custom_fields['saswp_tt_schema_ttype']) ) {
                            $explode_type = explode(',', $custom_fields['saswp_tt_schema_ttype']);
                            if ( ! empty( $explode_type) && is_array($explode_type) ) {
                                $input1['touristType'] =   $explode_type;
                            }
                        }
                    }
                    if ( isset( $custom_fields['saswp_tt_schema_son']) || isset($custom_fields['saswp_tt_schema_sou']) ) {
                        $input1['subjectOf']['@type'] =   "CreativeWork";    
                    }
                    if ( isset( $custom_fields['saswp_tt_schema_son']) ) {
                        $input1['subjectOf']['name'] =   $custom_fields['saswp_tt_schema_son'];    
                    }
                    if ( isset( $custom_fields['saswp_tt_schema_sou']) ) {
                        $input1['subjectOf']['url'] =   $custom_fields['saswp_tt_schema_sou'];    
                    }

                break;
                
                case 'VacationRental':
                    if ( isset( $custom_fields['saswp_vr_schema_additional_type']) ) {
                        $input1['additionalType'] = $custom_fields['saswp_vr_schema_additional_type'];
                    }
                    if ( isset( $custom_fields['saswp_vr_schema_brand']) ) {
                        $input1['brand'] = $custom_fields['saswp_vr_schema_brand'];
                    }
                    $input1['containsPlace']['@type'] = 'Accommodation';
                    if ( isset( $custom_fields['saswp_vr_schema_cpat']) ) {
                        $input1['containsPlace']['additionalType'] = $custom_fields['saswp_vr_schema_cpat'];
                    }
                    if ( isset( $custom_fields['saswp_vr_schema_occupancy']) ) {
                        $input1['containsPlace']['occupancy']['@type'] = 'QuantitativeValue';
                        $input1['containsPlace']['occupancy']['value'] = $custom_fields['saswp_vr_schema_occupancy'];
                    }
                    if ( isset( $custom_fields['saswp_vr_schema_floor_value']) || isset($custom_fields['saswp_vr_schema_floor_uc']) ) {
                        $input1['containsPlace']['floorSize']['@type'] = 'QuantitativeValue';   
                        $input1['containsPlace']['floorSize']['value'] = isset($custom_fields['saswp_vr_schema_floor_value'])?$custom_fields['saswp_vr_schema_floor_value']:'';   
                        $input1['containsPlace']['floorSize']['unitCode'] = isset($custom_fields['saswp_vr_schema_floor_uc'])?$custom_fields['saswp_vr_schema_floor_uc']:'';   
                    }
                    if ( isset( $custom_fields['saswp_vr_schema_total_bathrooms']) ) {
                        $input1['containsPlace']['numberOfBathroomsTotal'] = $custom_fields['saswp_vr_schema_total_bathrooms'];
                    }
                    if ( isset( $custom_fields['saswp_vr_schema_total_bedrooms']) ) {
                        $input1['containsPlace']['numberOfBedrooms'] = $custom_fields['saswp_vr_schema_total_bedrooms'];
                    }
                    if ( isset( $custom_fields['saswp_vr_schema_total_rooms']) ) {
                        $input1['containsPlace']['numberOfRooms'] = $custom_fields['saswp_vr_schema_total_rooms'];
                    }
                    if ( isset( $custom_fields['saswp_vr_schema_identifier']) ) {
                        $input1['identifier'] = $custom_fields['saswp_vr_schema_identifier'];
                    }
                    if ( isset( $custom_fields['saswp_vr_schema_latitude']) ) {
                        $input1['latitude'] = $custom_fields['saswp_vr_schema_latitude'];
                    }
                    if ( isset( $custom_fields['saswp_vr_schema_longitude']) ) {
                        $input1['longitude'] = $custom_fields['saswp_vr_schema_longitude'];
                    }
                    if ( isset( $custom_fields['saswp_vr_schema_name']) ) {
                        $input1['name'] = $custom_fields['saswp_vr_schema_name'];
                    }
                    if ( isset( $custom_fields['saswp_vr_schema_country']) ) {
                        $input1['address']['addressCountry'] = $custom_fields['saswp_vr_schema_country'];
                    }
                    if ( isset( $custom_fields['saswp_vr_schema_locality']) ) {
                        $input1['address']['addressLocality'] = $custom_fields['saswp_vr_schema_locality'];
                    }
                    if ( isset( $custom_fields['saswp_vr_schema_region']) ) {
                        $input1['address']['addressRegion'] = $custom_fields['saswp_vr_schema_region'];
                    }
                    if ( isset( $custom_fields['saswp_vr_schema_p_code']) ) {
                        $input1['address']['postalCode'] = $custom_fields['saswp_vr_schema_p_code'];
                    }
                    if ( isset( $custom_fields['saswp_vr_schema_s_address']) ) {
                        if ( ! empty( $custom_fields['saswp_vr_schema_s_address']) && is_array($custom_fields['saswp_vr_schema_s_address']) ) {
                            $address_array = $custom_fields['saswp_vr_schema_s_address'];
                            if ( ! isset( $input1['address']) && !isset($input1['address']['addressCountry']) && isset($address_array['country_short']) && !empty($address_array['country_short']) ) {
                                $input1['address']['addressCountry'] = $address_array['country_short'];
                            }
                            if ( ! isset( $input1['address']['addressLocality']) && isset($address_array['city']) && !empty($address_array['city']) ) {
                                $input1['address']['addressLocality'] = $address_array['city'];
                            }
                            if ( ! isset( $input1['address']['addressRegion']) && isset($address_array['state']) && !empty($address_array['state']) ) {
                                $input1['address']['addressRegion'] = $address_array['state'];
                            }
                            if ( ! isset( $input1['address']['addressRegion']) && isset($address_array['state']) && !empty($address_array['state']) ) {
                                $input1['address']['addressRegion'] = $address_array['state'];
                            }
                            if ( isset( $address_array['address']) && !empty($address_array['address']) ) {
                                $input1['address']['streetAddress'] = $address_array['address'];    
                            }
                            
                            // Latitude and Longitude
                            if ( isset( $address_array['lat']) || isset($address_array['lng']) ) {
                                if ( ! isset( $input1['latitude']) && isset($address_array['lat']) && !empty($address_array['lat']) ) {
                                    $input1['latitude'] = $address_array['lat'];   
                                } 
                                if ( ! isset( $input1['longitude']) && isset($address_array['lng']) && !empty($address_array['lng']) ) {
                                    $input1['longitude'] = $address_array['lng'];   
                                } 
                            }
                        }else{
                            $input1['address']['streetAddress'] = $custom_fields['saswp_vr_schema_s_address'];
                        }
                    }
                    if ( isset( $custom_fields['saswp_vr_schema_rating_value']) ) {
                        $input1['aggregateRating']['ratingValue'] = $custom_fields['saswp_vr_schema_rating_value'];
                    }
                    if ( isset( $custom_fields['saswp_vr_schema_rating_count']) ) {
                        $input1['aggregateRating']['ratingCount'] = $custom_fields['saswp_vr_schema_rating_count'];
                    }
                    if ( isset( $custom_fields['saswp_vr_schema_review_count']) ) {
                        $input1['aggregateRating']['reviewCount'] = $custom_fields['saswp_vr_schema_review_count'];
                    }
                    if ( isset( $custom_fields['saswp_vr_schema_best_rating']) ) {
                        $input1['aggregateRating']['bestRating'] = $custom_fields['saswp_vr_schema_best_rating'];
                    }
                    if ( isset( $custom_fields['saswp_vr_schema_checkin_time']) ) {
                        $input1['checkinTime'] = $custom_fields['saswp_vr_schema_checkin_time'];
                    }
                    if ( isset( $custom_fields['saswp_vr_schema_checkout_time']) ) {
                        $input1['checkoutTime'] = $custom_fields['saswp_vr_schema_checkout_time'];
                    }
                    if ( isset( $custom_fields['saswp_vr_schema_description']) ) {
                        $input1['description'] = $custom_fields['saswp_vr_schema_description'];
                    }
                    if ( isset( $custom_fields['saswp_vr_schema_knows_language']) ) {
                        if ( ! empty( $custom_fields['saswp_vr_schema_knows_language']) ) {
                            $explode_lang = explode(',', $custom_fields['saswp_vr_schema_knows_language']);
                            if ( ! empty( $explode_lang) && is_array($explode_lang) ) {
                                foreach ( $explode_lang as $el_key => $el_value) {
                                    if ( ! empty( $el_value) ) {
                                        $input1['knowsLanguage'] = $el_value;
                                    }
                                }
                            }
                        }
                    }
                break;
                
                case 'LearningResource':
                    if ( isset( $custom_fields['saswp_lr_name']) ) {
                        $input1['name'] = $custom_fields['saswp_lr_name'];
                    }
                    if ( isset( $custom_fields['saswp_lr_description']) ) {
                        $input1['description'] = $custom_fields['saswp_lr_description'];
                    }
                    if ( isset( $custom_fields['saswp_lr_keywords']) ) {
                        $input1['keywords'] = $custom_fields['saswp_lr_keywords'];
                    }
                    if ( isset( $custom_fields['saswp_lr_lrt']) ) {
                        $input1['learningResourceType'] = $custom_fields['saswp_lr_lrt'];
                    }
                    $input1['author'] = saswp_get_author_details();
                    if ( isset( $custom_fields['saswp_lr_inlanguage']) ) {
                        if ( ! empty( $custom_fields['saswp_lr_inlanguage']) && is_string($custom_fields['saswp_lr_inlanguage']) ) {
                            $explode_lang = explode(',', $custom_fields['saswp_lr_inlanguage']);
                            if ( ! empty( $explode_lang) && is_array($explode_lang) ) {
                                foreach ( $explode_lang as $el_key => $el_value) {
                                    $input1['inLanguage'][] = $el_value;
                                }
                            }
                        }
                    }
                    $input1['dateCreated'] = gmdate('Y-m-d', strtotime(get_the_date()));
                    if ( isset( $custom_fields['saswp_lr_date_created']) ) {
                        $input1['dateCreated'] = gmdate('Y-m-d', strtotime($custom_fields['saswp_lr_date_created']));
                    }
                    $input1['dateModified'] = gmdate('Y-m-d', strtotime(get_the_modified_date()));
                    if ( isset( $custom_fields['saswp_lr_date_modified']) ) {
                        $input1['dateModified'] = $custom_fields['saswp_lr_date_modified'];
                    }
                    if ( isset( $custom_fields['saswp_lr_tar']) ) {
                        $input1['typicalAgeRange'] = $custom_fields['saswp_lr_tar'];
                    }
                    if ( isset( $custom_fields['saswp_lr_education_level_name']) || isset($custom_fields['saswp_lr_education_level_url']) || isset($custom_fields['saswp_lr_education_level_term_set']) ) {
                        $input1['educationalLevel']['@type'] = 'DefinedTerm';
                        if ( isset( $custom_fields['saswp_lr_education_level_name']) ) {
                            $input1['educationalLevel']['name'] = $custom_fields['saswp_lr_education_level_name'];
                        }
                        if ( isset( $custom_fields['saswp_lr_education_level_url']) ) {
                            $input1['educationalLevel']['url'] = $custom_fields['saswp_lr_education_level_url'];
                        }
                        if ( isset( $custom_fields['saswp_lr_education_level_term_set']) ) {
                            $input1['educationalLevel']['inDefinedTermSet'] = $custom_fields['saswp_lr_education_level_term_set'];
                        }
                    } 
                    if ( isset( $custom_fields['saswp_lr_time_required']) ) {
                        $input1['timeRequired'] = $custom_fields['saswp_lr_time_required'];
                    }
                    if ( isset( $custom_fields['saswp_lr_license']) ) {
                        $input1['license'] = $custom_fields['saswp_lr_license'];
                    } 
                    if ( isset( $custom_fields['saswp_lr_time_iaff']) ) {
                        $input1['isAccessibleForFree'] = $custom_fields['saswp_lr_time_iaff'];
                    }  
                    if ( ! empty( $custom_fields['saswp_lr_eaef']) || !empty($custom_fields['saswp_lr_eatn']) || !empty($custom_fields['saswp_lr_eatu']) ) {
                        $input1['educationalAlignment']['@type']                    = 'AlignmentObject';
                        $input1['educationalAlignment']['alignmentType']            = 'educationalSubject';
                        if ( ! empty( $custom_fields['saswp_lr_eaef']) ) {
                            $input1['educationalAlignment']['educationalFramework'] = $custom_fields['saswp_lr_eaef']; 
                        }
                        if ( ! empty( $custom_fields['saswp_lr_eatn']) ) {
                            $input1['educationalAlignment']['targetName'] = $custom_fields['saswp_lr_eatn']; 
                        }
                        if ( ! empty( $custom_fields['saswp_lr_eatu']) ) {
                            $input1['educationalAlignment']['targetUrl']  = $custom_fields['saswp_lr_eatu']; 
                        }
                    }
                    if ( ! empty( $custom_fields['saswp_lr_audience']) ) {
                        $input1['audience']['@type'] = 'EducationalAudience';
                        $input1['audience']['educationalRole'] = $custom_fields['saswp_lr_audience'];
                    }  
                break;
               
                     default:
                         break;
                 }    
             
             if($main_schema_type == 'Review' || $main_schema_type == 'ReviewNewsArticle'){
                 
                 $review_response['item_reviewed'] = $input1;
                 $review_response['review']        = $review_markup;
                 
                 return $review_response;
             }    
                 
            }     
            
            return $input1;   
        }

        /**
         * This is a ajax handler to get all the schema type keys 
         * @return type json
         */
        public function saswp_get_schema_type_fields() {
            
             if ( ! isset( $_POST['saswp_security_nonce'] ) ){
                return; 
             }
             if ( !wp_verify_nonce( $_POST['saswp_security_nonce'], 'saswp_ajax_check_nonce' ) ){
                return;  
             }
            if(!current_user_can( saswp_current_user_can()) ) {
                die( '-1' );    
            }
            
            $schema_subtype = isset( $_POST['schema_subtype'] ) ? sanitize_text_field( $_POST['schema_subtype'] ) : ''; 
            $schema_type    = isset( $_POST['schema_type'] ) ? sanitize_text_field( $_POST['schema_type'] ) : '';                      
                      
            if($schema_type == 'Review'){
                
                $meta_fields = $this->saswp_get_all_schema_type_fields($schema_subtype);                                            
                
            }else{
                $meta_fields = $this->saswp_get_all_schema_type_fields($schema_type);  
            }
            
            wp_send_json( $meta_fields );                                   
        }
        
        /**
         * This function gets all the custom meta fields from the wordpress meta fields table
         * @global type $wpdb
         * @return type json
         */
        public function saswp_get_custom_meta_fields() {
            
             if ( ! isset( $_POST['saswp_security_nonce'] ) ){
                return; 
             }
             if ( !wp_verify_nonce( $_POST['saswp_security_nonce'], 'saswp_ajax_check_nonce' ) ){
                return;  
             }
            if(!current_user_can( saswp_current_user_can()) ) {
                die( '-1' );    
            }
            
            $search_string = isset( $_POST['q'] ) ? sanitize_text_field( $_POST['q'] ) : '';                                    
	        $data          = array();
	        $result        = array();
            
            global $wpdb;
            $meta_search_value = '%' . $wpdb->esc_like( trim( $search_string ) ) . '%'; // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	        $saswp_meta_array = $wpdb->get_results( $wpdb->prepare("SELECT DISTINCT meta_key FROM {$wpdb->postmeta} WHERE meta_key LIKE %s", $meta_search_value), ARRAY_A ); 

            if ( isset( $saswp_meta_array ) && ! empty( $saswp_meta_array ) ) {
                
				foreach ( $saswp_meta_array as $value ) {
				
						$data[] = array(
							'id'   => $value['meta_key'],
							'text' => preg_replace( '/^_/', '', esc_html( str_replace( '_', ' ', $value['meta_key'] ) ) ),
						);
					
				}
                                
			}

            //aioseo wp_aioseo_posts support starts here
            $column_names = array();
            $cache_key    = 'saswp_aioseo_posts_cache_key';
            $table_name   = $wpdb->prefix . 'aioseo_posts';
            $columns_des  = wp_cache_get( $cache_key ); 
            if ( false === $columns_des ) {                                		        
                //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery	-- just to check if table exists
		        $table_exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name));
                if($table_exists){
                    //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.NotPrepared -- Reasone Custom table create by aioseo
                    $columns_des  = $wpdb->get_col( "DESC " . $table_name, 0 );
                    wp_cache_set( $cache_key, $columns_des );
                }
                
            }               

            if($columns_des){

                foreach ( $columns_des as $column_name ) {
                    $column_names[] = 'aioseo_posts_'.$column_name;
                }

                foreach ( $column_names as $string ) {
                    
                    $preg_rep = preg_replace( '/^_/', '', esc_html( str_replace( '_', ' ', $string ) ) );

                    if ( strpos( $string, $search_string ) !== false ) {

                        $data[] = array(
							'id'   => $string,
							'text' => $preg_rep
						);                        
                    }

                    if ( strpos( $preg_rep, $search_string ) !== false ) {

                        $data[] = array(
							'id'   => $string,
							'text' => $preg_rep
						);                        
                    }

                }

            }
                        
            //aioseo wp_aioseo_posts support endss here
                        
            if ( is_array( $data ) && ! empty( $data ) ) {
                
				$result[] = array(
					'children' => $data,
				);
                                
			}
                        
            wp_send_json( $result );            
            
            wp_die();
        }
        
        /**
         * This function gets the product details in schema markup from the current product type post create by 
         * WooCommerce ( https://wordpress.org/plugins/woocommerce/ )
         * @param type $post_id
         * @return type array
         */
        public function saswp_woocommerce_product_details($post_id){     
                             
             $product_details = array(); 
             $varible_prices = array();
             
             $post_type = get_post_type($post_id);

             if ( class_exists('WC_Product') && function_exists('wc_get_product') && $post_type == 'product' ) {
                 
             global $woocommerce;
             global $sd_data;
                 
	         $product = wc_get_product($post_id); 
             
             if(is_object($product) ) {   
                                               
               if(is_object($woocommerce) ) {
                             
                        if(method_exists('WC_Product_Simple', 'get_type') ) {

                            if($product->get_type() == 'variable' && class_exists('WC_Product_Variable') ){

                                $product_id_some = $woocommerce->product_factory->get_product();
                                
                                if($product_id_some instanceof WC_Product_Variable) {
                                    
                                    $variations  = $product_id_some->get_available_variations(); 
                                      
                                        if($variations){
        
                                                foreach( $variations as $value){

                                                    $product_variation = wc_get_product( $value['variation_id'] ); 
                                                    $p_inc_tax = wc_get_price_including_tax($product_variation);                                                    
                                                                                                        
                                                    if($p_inc_tax){
                                                        $varible_prices[] = $p_inc_tax; 
                                                    }else{
                                                        $varible_prices[] = $value['display_price']; 
                                                    }                                                       
                                                }
                                        }

                                }
                                    
                            }
                            
                        }                                        
				 				 
                }  
                 
             //product categories starts here
             
             $terms       = get_the_terms( $post_id, 'product_cat' );
             $product_cat = array();

             if($terms){
                foreach( $terms as $val){
                    $product_cat[] = $val->name;
                }
                $product_details['product_category'] = $product_cat;      
             }

             //product categories ends here 
                
             $gtin = get_post_meta($post_id, $key='hwp_product_gtin', true);
             
             if($gtin !='' ) {
                 
             $product_details['product_gtin8'] = $gtin;   
             
             }  
             
             $brand = '';
             $brand = get_post_meta($post_id, $key='hwp_product_brand', true);
             
             if($brand !='' ) {
                 
             $product_details['product_brand'] = $brand;   
             
             }
             
             if($brand == ''){
               
                 $product_details['product_brand'] = get_bloginfo();
                 
             }
                                                   
             $date_on_sale                           = $product->get_date_on_sale_to();                            
             $product_details['product_name']        = $product->get_title();
             
             $product_desc                           = '';
             
             if($product->get_short_description() && $product->get_description() ) {
                 
                 $product_desc = $product->get_short_description().' '.$product->get_description();
                 
             }elseif($product->get_description() ) {
                 
                 $product_desc = $product->get_description();
                 
             }else{
                 
                 $product_desc = get_the_excerpt();
                 
             }
             
             if ( isset( $sd_data['saswp-yoast']) && class_exists('WPSEO_Meta') ) {
                 $product_desc = saswp_get_the_excerpt();                 
             }
             
             $product_desc = do_shortcode($product_desc);

             $product_details['product_description'] = wp_strip_all_tags(strip_shortcodes($product_desc));
             
             if($product->get_attributes() ) {
                 
                 foreach ( $product->get_attributes() as $attribute) {
                     
                     if(strtolower($attribute['name']) == 'isbn'){
                                            
                      $product_details['product_isbn'] = $attribute['options'][0];   
                                                                 
                     }
                     if(strtolower($attribute['name']) == 'mpn'){
                                            
                      $product_details['product_mpn'] = $attribute['options'][0];   
                                                                 
                     }
                     if(strtolower($attribute['name']) == 'gtin8'){
                                            
                      $product_details['product_gtin8'] = $attribute['options'][0];   
                                                                 
                     }
                     if(strtolower($attribute['name']) == 'gtin13'){
                                            
                        $product_details['product_gtin13'] = $attribute['options'][0];   
                                                                   
                    }
                    if(strtolower($attribute['name']) == 'gtin12'){
                                            
                        $product_details['product_gtin12'] = $attribute['options'][0];   
                                                                   
                    }
                     if(strtolower($attribute['name']) == 'brand'){
                                            
                      $product_details['product_brand'] = $attribute['options'][0];   
                                                                 
                     }
                     
                 }
                 
             }
                
             if ( ! isset( $product_details['product_mpn']) ) {
                 $product_details['product_mpn'] = get_the_ID();
             }
                               
             $product_details['product_availability'] = saswp_prepend_schema_org($product->get_stock_status());
             
             $woo_price = $product->get_price();

             if( function_exists('wc_get_price_including_tax')) {
                $woo_price = wc_get_price_including_tax($product);
             } 

             if(method_exists('WC_Product_Simple', 'get_type') ) {
                 
                if($product->get_type() == 'variable'){
                    if( !empty($varible_prices) ){
                        $product_details['product_varible_price']   = $varible_prices;
                    }else{
                        $product_details['product_price']           = $woo_price;    
                    }
                    
                }else{
                    $product_details['product_price']           = $woo_price;
                }

             }else{
                $product_details['product_price']           = $woo_price;
             }
                          
             if($product->get_sku() ) {
                $product_details['product_sku']             = $product->get_sku();             
             }else{
                $product_details['product_sku']             = $post_id;             
             }             
             
             if ( isset( $date_on_sale) ) {
                 
             $product_details['product_priceValidUntil'] = $date_on_sale->date('Y-m-d G:i:s');    
             
             }else{
            
                $mdate = get_the_modified_date("c");
                
                if($mdate){
                    $mdate = strtotime($mdate);                    
                    $product_details['product_priceValidUntil'] = gmdate("c", strtotime("+1 years", $mdate)); 
                }    
                                                          
             }       
             
             $product_details['product_currency'] = get_option( 'woocommerce_currency' );             
             
             $reviews_arr = array();
             $reviews     = get_approved_comments( $post_id );
                                       
             $judge_me_post = null;
             
             if(class_exists('saswp_reviews_platform_markup') && class_exists('JGM_ProductService') ) {
                 
                 $judge_me_post = get_posts( 
                        array(
                            'post_type' 	 => 'saswp_reviews',                                                                                   
                            'posts_per_page'     => -1,   
                            'post_status'        => 'publish',
                            // phpcs:ignore     WordPress.DB.SlowDBQuery.slow_db_query_meta_query
                            'meta_query'  => array(
                                array(
                                'key'     => 'saswp_review_product_id',
                                'value'   => $post_id,
                                'compare' => '==',
                                 )
                            )
                           
                ) );   
                 
             }
                          
             if($judge_me_post){
                                    
               $post_meta = array(                              
                'saswp_reviewer_name' => 'author',
                'saswp_review_rating' => 'reviewRating',
                'saswp_review_date'   => 'datePublished',
                'saswp_review_text'   => 'description',                           		
              );
               
                    $sumofrating = 0;
                   
                    foreach( $judge_me_post as $me_post){
                        
                        $rv = array();
                        
                        foreach( $post_meta as $key => $val){
                  
                               $rv[$val] = get_post_meta($me_post->ID, $key, true );  
                               
                               if($val == 'reviewRating'){
                                   $sumofrating += get_post_meta($me_post->ID, $key, true ); 
                               }
                               
                                   
                        }
                        
                        $reviews_arr[] = $rv;  
                        
                    }
                                        
                    $product_details['product_review_count']   = count($judge_me_post);
                    if($sumofrating > 0){                        
                        $product_details['product_average_rating'] = $sumofrating /  count($judge_me_post);
                    }
                 
             } elseif(class_exists('Woo_stamped_api') && (isset($sd_data['saswp-stamped']) && $sd_data['saswp-stamped'] == 1) ) {

                $stamped_reviews = saswp_get_stamped_reviews($post_id);
                
                if($stamped_reviews){
                    $reviews_arr                               = $stamped_reviews['reviews'];     
                    $product_details['product_review_count']   = $stamped_reviews['total'];
                    $product_details['product_average_rating'] = $stamped_reviews['average'];  
                }


             } elseif( function_exists('wc_yotpo_init') && (isset($sd_data['saswp-yotpo']) && $sd_data['saswp-yotpo'] ==1 ) ){
            
                $yotpo_reviews = saswp_get_yotpo_reviews($post_id);
                
                if($yotpo_reviews){
                    $reviews_arr                               = $yotpo_reviews['reviews'];     
                    $product_details['product_review_count']   = $yotpo_reviews['total'];
                    $product_details['product_average_rating'] = $yotpo_reviews['average'];  
                }

             } elseif ( function_exists('RYVIU') && (isset($sd_data['saswp-ryviu']) && $sd_data['saswp-ryviu'] == 1 ) ) {

                $ryviu_reviews = saswp_get_ryviu_reviews($post_id);
                
                if($ryviu_reviews){
                    $reviews_arr                               = $ryviu_reviews['reviews'];     
                    $product_details['product_review_count']   = $ryviu_reviews['total'];
                    $product_details['product_average_rating'] = $ryviu_reviews['average'];  
                }

             } elseif( $reviews && is_array($reviews) ){

              $sumofrating = 0;
              $avg_rating  = 1;
                                  
             foreach( $reviews as $review){                 
                
                $rating = get_comment_meta( $review->comment_ID, 'rating', true ) ? get_comment_meta( $review->comment_ID, 'rating', true ) : 5;
                if(is_numeric($rating) ) {
                    $sumofrating += floatval($rating);
                }
                
                 $reviews_arr[] = array(
                     'author'        => $review->comment_author ? $review->comment_author : 'Anonymous' ,
                     'datePublished' => $review->comment_date,
                     'description'   => wp_strip_all_tags(strip_shortcodes($review->comment_content)),
                     'reviewRating'  => $rating,
                 );
                 
             }   

             if($sumofrating> 0){
                $avg_rating = $sumofrating /  count($reviews); 
             }
             
             if($product->get_review_count() ) {
                $product_details['product_review_count']   = $product->get_review_count();
             }else{
                $product_details['product_review_count']   = count($reviews);
             }

             if($product->get_average_rating() ) {
                $product_details['product_average_rating'] = $product->get_average_rating();             
             }else{
                $product_details['product_average_rating'] = $avg_rating;             
             }             
             
             }else{
                 
                 if( isset($sd_data['saswp_default_review']) && $sd_data['saswp_default_review'] == 1 && saswp_get_the_author_name() ){
                 
                     $reviews_arr[] = array(
                     'author'        => saswp_get_the_author_name(),
                     'datePublished' => get_the_date("c"),
                     'description'   => saswp_get_the_excerpt(),
                     'reviewRating'  => 5,
                 );
                 
                    $product_details['product_review_count']   = 1;
                    $product_details['product_average_rating'] = 5;  
                     
                 }
                                
             }    
                          
             $product_details['product_reviews']        = $reviews_arr;      
             
             }
             
             }      
                                                                 
             return apply_filters( 'saswp_modify_product_markup_in_service', $product_details );                        
        }
                
        public function saswp_rating_box_rating_markup($post_id){
            
                global $sd_data;

                $response               = array(); 
                $over_all               = '';
                $item_enable            = 0;
                $review_count           = "1";

                $rating_box   = get_post_meta($post_id, 'saswp_review_details', true); 

                if ( isset( $rating_box['saswp-review-item-over-all']) ) {

                    $over_all = $rating_box['saswp-review-item-over-all'];  

                }

                if ( isset( $rating_box['saswp-review-item-enable']) ) {

                    $item_enable =  $rating_box['saswp-review-item-enable'];  

                }  

                if($over_all && $review_count && $item_enable ==1 && isset($sd_data['saswp-review-module']) && $sd_data['saswp-review-module'] ==1){

                   $response =       array(
                                                    "@type"       => "AggregateRating",
                                                    "ratingValue" => $over_all,
                                                    "reviewCount" => $review_count
                                                 ); 

                }
    
            return $response;            
        }

                /**
         * This function gets the review details in schema markup from the current post which has extra theme enabled
         * Extra Theme ( https://www.elegantthemes.com/preview/Extra/ )
         * @global type $sd_data
         * @param type $post_id
         * @return type array
         */
        public function saswp_extra_theme_review_details($post_id){
            
            global $sd_data;
           
            $review_data        = array();
            $rating_value       = 0;
            $post_review_title  = '';
            $post_review_desc   = '';
            
            $post_meta   = get_post_meta($post_id);                                       
            
            if ( isset( $post_meta['_post_review_box_breakdowns_score']) ) {
                
              if ( function_exists( 'bcdiv') ) {
                  $rating_value = bcdiv($post_meta['_post_review_box_breakdowns_score'][0], 20, 2);        
              }  
                                          
            }
            if ( isset( $post_meta['_post_review_box_title']) ) {
              $post_review_title = $post_meta['_post_review_box_title'][0];     
            }
            if ( isset( $post_meta['_post_review_box_summary']) ) {
              $post_review_desc = $post_meta['_post_review_box_summary'][0];        
            }                            
            if($post_review_title && $rating_value>0 &&  (isset($sd_data['saswp-extra']) && $sd_data['saswp-extra'] ==1) && get_template()=='Extra'){
            
            $review_data['aggregateRating'] = array(
                '@type'         => 'AggregateRating',
                'ratingValue'   => $rating_value,
                'reviewCount'   => 1,
            );
            
            $review_data['review'] = array(
                '@type'         => 'Review',
                'author'        => array('@type'=> 'Person', 'name' => get_the_author()),
                'datePublished' => get_the_date("c"),
                'name'          => $post_review_title,
                'reviewBody'    => $post_review_desc,
                'reviewRating' => array(
                            '@type'       => 'Rating',
                            'ratingValue' => $rating_value,
                ),
                
            );
            
           }
           return $review_data;
            
        }
         /**
         * This function gets topic details as an array from bbpress posts
         * DW Question & Answer ( https://wordpress.org/plugins/bbpress/ )
         * @global type $sd_data
         * @param type $post_id
         * @return type array
         */       
        public function saswp_bb_press_topic_details($post_id){
                            
                $dw_qa          = array();
                $qa_page        = array();
                                                                                                                                              
                $dw_qa['@type']       = 'Question';
                $dw_qa['name']        = bbp_get_topic_title($post_id); 
                $dw_qa['upvoteCount'] = bbp_get_topic_reply_count();    
                $dw_qa['text']        = wp_strip_all_tags(bbp_get_topic_content());                                
                $dw_qa['dateCreated'] = date_format(date_create(get_post_time( get_option( 'date_format' ), false, $post_id, true )), "Y-m-d\TH:i:s\Z");
                                                                          
                $dw_qa['author']      = array(
                                                 '@type' => 'Person',
                                                 'name'  =>bbp_get_topic_author($post_id),
                                            ); 
                
                $dw_qa['answerCount'] = bbp_get_topic_reply_count();   
                
                $args = array(
			'post_type'     => 'reply',
			'post_parent'   => $post_id,
			'post_per_page' => '-1',
			'post_status'   => array('publish')
		);
                
                $answer_array = get_posts($args);                
                               
                $suggested_answer = array();
                
                foreach( $answer_array as $answer){
                                       
                        $authorinfo = get_userdata($answer->post_author);  
                        $sa_author = '';
                        if(is_object($authorinfo) && isset($authorinfo->data) && isset($authorinfo->data->user_nicename) ) {
                            $sa_author = $authorinfo->data->user_nicename;
                        }  
                        $suggested_answer_values = array();
                        $suggested_answer_values['@type'] = 'Answer';
                        $suggested_answer_values['upvoteCount'] = 1;
                        $suggested_answer_values['url'] = get_permalink().'#post-'.$answer->ID;
                        $suggested_answer_values['text'] = wp_strip_all_tags($answer->post_content);
                        $suggested_answer_values['dateCreated'] = get_the_date("Y-m-d\TH:i:s\Z", $answer);
                        if ( ! empty( $sa_author) ) {
                            $suggested_answer_values['author'] = array('@type' => 'Person', 'name' => $sa_author);
                        }
                        $suggested_answer[] =  $suggested_answer_values;
                        
                    
                }
                                
                $dw_qa['suggestedAnswer'] = $suggested_answer;
                    
                $qa_page['@context']   = saswp_context_url();
                $qa_page['@type']      = 'QAPage';
                $qa_page['mainEntity'] = $dw_qa;                                                    
                return $qa_page;
        }
        
        /**
         * This function gets all the question and answers in schema markup from the current question type post create by 
         * DW Question & Answer ( https://wordpress.org/plugins/dw-question-answer/ )
         * @global type $sd_data
         * @param type $post_id
         * @return type array
         */
        public function saswp_dw_question_answers_details($post_id){
            
                global $sd_data;
                $dw_qa          = array();
                $qa_page        = array();
                $best_answer_id = '';
                                                
                $post_type = get_post_type($post_id);
                
                if($post_type =='dwqa-question' && isset($sd_data['saswp-dw-question-answer']) && $sd_data['saswp-dw-question-answer'] ==1 && (is_plugin_active('dw-question-answer/dw-question-answer.php') || is_plugin_active('dw-question-answer-pro/dw-question-answer.php')) ){
                 
                $post_meta      = get_post_meta($post_id);
                
                if ( isset( $post_meta['_dwqa_best_answer']) ) {
                    
                    $best_answer_id = $post_meta['_dwqa_best_answer'][0];
                    
                }
                                                                                                                                              
                $dw_qa['@type']       = 'Question';
                $dw_qa['name']        = saswp_get_the_title(); 
                $dw_qa['upvoteCount'] = get_post_meta( $post_id, '_dwqa_votes', true );                                             
                
                $args = array(
                    'p'         => $post_id, // ID of a page, post, or custom type
                    'post_type' => 'dwqa-question'
                  );
                
                $my_posts = new WP_Query($args);
                
                if ( $my_posts->have_posts() ) {
                    
                  while ( $my_posts->have_posts() ) : $my_posts->the_post();                   
                   $dw_qa['text'] = get_the_content();
                  endwhile;
                  
                } 
                
                $dw_qa['dateCreated'] = get_the_date("c");                                                   
                $dw_qa['author']      = array(
                                                 '@type' => 'Person',
                                                 'name'  =>saswp_get_the_author_name(),
                                            ); 
                                                                                    
                $dw_qa['answerCount'] = $post_meta['_dwqa_answers_count'][0];                  
                
                $args = array(
			'post_type'     => 'dwqa-answer',
			'post_parent'   => $post_id,
			'post_per_page' => '-1',
			'post_status'   => array('publish')
		);
                
                $answer_array = get_posts($args);
               
                $accepted_answer  = array();
                $suggested_answer = array();
                
                foreach( $answer_array as $answer){
                    
                    $authorinfo = get_userdata($answer->post_author);
                    $authorname =  'Anonymous';

                    if(is_object($authorinfo) ) {
                        $authorname = $authorinfo->data->user_nicename;
                    }else{
                        $anonymous_name = get_post_meta( $answer->ID, '_dwqa_anonymous_name', true );
                        if($anonymous_name && $anonymous_name !='' ) {
                            $authorname = $anonymous_name;
                        }
                    }
                    
                    if($answer->ID == $best_answer_id){
                        
                        $accepted_answer['@type']       = 'Answer';
                        $accepted_answer['upvoteCount'] = get_post_meta( $answer->ID, '_dwqa_votes', true );
                        $accepted_answer['url']         = get_permalink();
                        $accepted_answer['text']        = wp_strip_all_tags($answer->post_content);
                        $accepted_answer['dateCreated'] = get_the_date("Y-m-d\TH:i:s\Z", $answer);
                        $accepted_answer['author']      = array('@type' => 'Person', 'name' => $authorname);
                        
                    }else{
                        
                        $suggested_answer[] =  array(
                            '@type'       => 'Answer',
                            'upvoteCount' => get_post_meta( $answer->ID, '_dwqa_votes', true ),
                            'url'         => get_permalink(),
                            'text'        => wp_strip_all_tags($answer->post_content),
                            'dateCreated' => get_the_date("Y-m-d\TH:i:s\Z", $answer),
                            'author'      => array('@type' => 'Person', 'name' => $authorname),
                        );
                        
                    }
                }
                
                $dw_qa['acceptedAnswer']  = $accepted_answer;
                $dw_qa['suggestedAnswer'] = $suggested_answer;
                    
                $qa_page['@context']   = saswp_context_url();
                $qa_page['@type']      = 'QAPage';
                $qa_page['mainEntity'] = $dw_qa;                
                }                           
                return $qa_page;
        }
                                
        /**
         * This function returns all the schema field's key by schema type or id
         * @param type $schema_type
         * @param type $id
         * @return string
         */
        public function saswp_get_all_schema_type_fields($schema_type){
            
            $response   = array();
            $meta_field = array();   
                        
            $meta_field = saswp_get_fields_by_schema_type(null, null, $schema_type, 'manual');
            
            // Get post type from post id
            $post_type          =   '';
            if ( ! empty( $_REQUEST['post'] ) ) {
                $post_id        =   intval( $_REQUEST['post'] );
                $post_type      =   get_post_type( $post_id );
            }
            
            if($meta_field){
                                
                foreach ( $meta_field as $field){

                    if ( $post_type == 'saswp_template' && isset( $field['is_template_attr'] ) ) {
                        continue;
                    }
                
                    $key = $field['id'];
                    $key = rtrim($key, '_');
                    $response[$key] = $field['label'];
                    
                }
                                
            }
                                  
            return $response;
        }
                        
        /**
         * This function generate the schema markup by passed schema type
         * @global type $sd_data
         * @param type $schema_type
         * @return array
         */
        public function saswp_schema_markup_generator( $schema_type, $schema_post_id = null ){
            
                        global $post, $sd_data;                                                
                                    
                        $publisher    = array();
                        
                        $publisher    = $this->saswp_get_publisher();                           
                                                                                                                                                
                        $input1         = array();                                                                                    			                  			
                        $date 		    = get_the_date("c");
                        $modified_date 	= get_the_modified_date("c");                        
			                                                
            switch ($schema_type) {
                                                
                case 'Article':   
                case 'ScholarlyArticle':
                case 'TechArticle':  
                case 'Photograph':  
                case 'Blogposting':
                case 'BlogPosting':
                case 'CreativeWork': 
                                         
                    $input1 = array(
					'@context'			=> saswp_context_url(),
					'@type'				=> $schema_type,
                    '@id'				=> saswp_get_permalink().'#'.$schema_type,
                    'url'				=> saswp_get_permalink(),
                    'inLanguage'        => get_bloginfo('language'),
					'mainEntityOfPage'  => saswp_get_permalink(),					
					'headline'			=> saswp_get_the_title(),
					'description'       => saswp_get_the_excerpt(),
                    'articleBody'       => saswp_get_the_content(),
                    'keywords'          => saswp_get_the_tags(),
					'datePublished'     => esc_html( $date),
					'dateModified'      => esc_html( $modified_date),
					'author'			=> saswp_get_main_authors(),//saswp_get_author_details(),
                    'editor'			=> saswp_get_edited_authors()//saswp_get_author_details()
				);

                if($schema_type == 'Photograph'){
                    unset($input1['articleBody']);
                    $image_arr = array();
                                                            
                    $image_arr  = saswp_get_ampforwp_story_images();    
                    
                    $block_data = saswp_get_gutenberg_block_data('core/gallery');
                                    
                    if ( isset( $block_data['attrs']['ids']) && is_array($block_data['attrs']['ids']) && !empty($block_data['attrs']['ids']) ){
                        
                        foreach( $block_data['attrs']['ids'] as $image_id){
                            $image_arr[] = saswp_get_image_by_id($image_id);                            
                        }
                                                                        
                    }
                    
                    if( !empty($image_arr) ){
                        unset($input1['mainEntityOfPage']);
                        $input1['mainEntityOfPage']['@type'] = 'ImageGallery';
                        $input1['mainEntityOfPage']['image'] = $image_arr;                        
                    }

                }

                if ( ! empty( $publisher) ) {
                    $input1 = array_merge($input1, $publisher);   
                }
                
                if ( isset( $sd_data['saswp_comments_schema']) && $sd_data['saswp_comments_schema'] == 1){
                    $input1['comment'] = saswp_get_comments(get_the_ID());
                }

                    break;
                
                    case 'SpecialAnnouncement':                   
                        $input1 = array(
                        '@context'			=> saswp_context_url(),
                        '@type'				=> 'SpecialAnnouncement',
                        // '@id'				=> saswp_get_permalink().'#SpecialAnnouncement',
                        'url'				=> saswp_get_permalink(),
                        'inLanguage'        => get_bloginfo('language'),                        
                        'name'			    => saswp_get_the_title(),                        
                        'text'              => saswp_get_the_excerpt(),                                                                    
                        'keywords'          => saswp_get_the_tags(),
                        'datePublished'     => esc_html( $date),
                        'datePosted'        => esc_html( $date),
                        'dateModified'      => esc_html( $modified_date),
                        'expires'           => esc_html( $modified_date),
                        'author'			=> saswp_get_author_details()                                                                
                    );    

                    if ( ! empty( $publisher) ) {
                        $input1 = array_merge($input1, $publisher);   
                    }
                        break;    
                
                case 'WebPage':

                    $sub_schema_type    =   '';
                    if( ! empty( $schema_post_id ) ) {
                        $sub_schema_type    =    get_post_meta( $schema_post_id, 'saswp_webpage_type', true );

                    }

                    $webp_permalink           =   saswp_get_permalink();
                    $webp_name                =   saswp_get_the_title();
                    $webp_description         =   saswp_get_the_excerpt();
                    $webp_keywords            =   saswp_get_the_tags();    

                    // Check if current page is a tag
                    if ( is_tag() || is_category() ) {
                        $tag_object             =   get_queried_object();
                        if ( ! empty( $tag_object ) && is_object( $tag_object ) && ! empty( $tag_object->term_id ) ) {
                            
                            $tag_id             =   $tag_object->term_id;
                            $webp_permalink     =   get_tag_link( $tag_id );
                            $webp_name          =   $tag_object->name;
                            $webp_description   =   $tag_object->description;
                            $webp_keywords      =   '';
         
                        }
                    }
                    
				    $input1['@context']                         = saswp_context_url();
				    $input1['@type']				            = 'WebPage';
                    $input1['@id']				                = $webp_permalink.'#webpage';
				    $input1['name']				                = $webp_name;
                    $input1['url']				                = $webp_permalink;
                    $input1['lastReviewed']                     = $modified_date;
                    $input1['dateCreated']                      = $date;                
                    $input1['inLanguage']                       = get_bloginfo('language');
				    $input1['description']                      = $webp_description;
                    $input1['keywords']                         = $webp_keywords;

                    // If sub schema type is set then add selected schema type to mainentity
                    if( $sub_schema_type != 'none' ) {
    				    $input1['mainEntity']['@type']              = $sub_schema_type;
                        $input1['mainEntity']['mainEntityOfPage']   = saswp_get_permalink();                      
    					$input1['mainEntity']['headline']		    = saswp_get_the_title();
    					$input1['mainEntity']['description']		= saswp_get_the_excerpt();                        
                        $input1['mainEntity']['keywords']           = $webp_keywords;
    					$input1['mainEntity']['datePublished'] 	    = $date;
    					$input1['mainEntity']['dateModified']		= $modified_date;
    					$input1['mainEntity']['author']			    = saswp_get_author_details();	
                        $input1['mainEntity']['publisher']          = $publisher['publisher'];
                    } else if( empty( $sub_schema_type ) ) {
                        /**
                         * This else condition is for users who have set WebPage schema before the version 1.36
                         * so that it won't affect in the markup 
                         * */
                        $input1['mainEntity']['@type']              = 'Article';
                        $input1['mainEntity']['mainEntityOfPage']   = saswp_get_permalink();                      
                        $input1['mainEntity']['headline']           = saswp_get_the_title();
                        $input1['mainEntity']['description']        = saswp_get_the_excerpt();                        
                        $input1['mainEntity']['keywords']           = $webp_keywords;
                        $input1['mainEntity']['datePublished']      = $date;
                        $input1['mainEntity']['dateModified']       = $modified_date;
                        $input1['mainEntity']['author']             = saswp_get_author_details();
                        $input1['mainEntity']['publisher']          = $publisher['publisher'];
                    } 					                                                                  									
                    if ( ! empty( $publisher) ) {
                        $input1['reviewedBy']               = $publisher['publisher'];     
                        $input1['publisher']                = $publisher['publisher'];     
                    }
                    
                    break;

            case 'ItemPage':
            
                $input1 = array(
                '@context'			=> saswp_context_url(),
                '@type'				=> 'ItemPage' ,
                '@id'				=> saswp_get_permalink().'#ItemPage',
                'name'				=> saswp_get_the_title(),
                'url'				=> saswp_get_permalink(),
                'lastReviewed'      => esc_html( $modified_date),
                'dateCreated'       => esc_html( $date),                
                'inLanguage'                    => get_bloginfo('language'),
                'description'                   => saswp_get_the_excerpt(),
                'mainEntity'                    => array(
                        '@type'			=> 'Article',
                        'mainEntityOfPage'	=> saswp_get_permalink(),						
                        'headline'		=> saswp_get_the_title(),
                        'description'		=> saswp_get_the_excerpt(),                        
                        'keywords'              => saswp_get_the_tags(),
                        'datePublished' 	=> esc_html( $date),
                        'dateModified'		=> esc_html( $modified_date),
                        'author'			=> saswp_get_author_details()						                                               
                    )                    									
                );

                    if ( ! empty( $publisher) ) {
                        $input1['reviewedBy']              = $publisher['publisher'];  
                        $input1['mainEntity']['publisher'] = $publisher['publisher'];   
                    }
                    
                    break;

                case 'MedicalWebPage':
                
                    $input1 = array(
                    '@context'			=> saswp_context_url(),
                    '@type'				=> 'MedicalWebPage' ,
                    '@id'				=> saswp_get_permalink().'#medicalwebpage',
                    'name'				=> saswp_get_the_title(),
                    'url'				=> saswp_get_permalink(),
                    'lastReviewed'      => esc_html( $modified_date),
                    'dateCreated'       => esc_html( $date),                
                    'inLanguage'                    => get_bloginfo('language'),
                    'description'                   => saswp_get_the_excerpt(),
                    'mainEntity'                    => array(
                            '@type'			=> 'Article',
                            'mainEntityOfPage'	=> saswp_get_permalink(),						
                            'headline'		=> saswp_get_the_title(),
                            'description'		=> saswp_get_the_excerpt(),                        
                            'keywords'              => saswp_get_the_tags(),
                            'datePublished' 	=> esc_html( $date),
                            'dateModified'		=> esc_html( $modified_date),
                            'author'			=> saswp_get_author_details()						                                               
                        )                    									
                    );
    
                        if ( ! empty( $publisher) ) {
                            $input1['reviewedBy']              = $publisher['publisher'];  
                            $input1['mainEntity']['publisher'] = $publisher['publisher'];   
                        }
                        
                        break;
                    
                case 'Product':
                case 'ProductGroup':
                case 'SoftwareApplication':
                case 'MobileApplication':
                case 'Book':
                case 'Car':
                case 'Vehicle':    
                                                                    
                        $product_details = $this->saswp_woocommerce_product_details(get_the_ID());  

                        if((isset($sd_data['saswp-woocommerce']) && $sd_data['saswp-woocommerce'] == 1) && !empty($product_details) ) {
                            if ( isset( $product_details['product_description']) && !empty($product_details['product_description']) ) {
                                $product_details['product_description'] = saswp_revalidate_product_description($product_details['product_description']);
                            }
                            $input1 = array(
                            '@context'			=> saswp_context_url(),
                            '@type'				=> $schema_type,
                            '@id'				=> saswp_get_permalink().'#'.$schema_type,     
                            'url'				=> saswp_get_permalink(),
                            'name'                              => saswp_remove_warnings($product_details, 'product_name', 'saswp_string'),
                            'sku'                               => saswp_remove_warnings($product_details, 'product_sku', 'saswp_string'),    
                            'description'                       => saswp_remove_warnings($product_details, 'product_description', 'saswp_string')                                                               
                          );
                            
                          if ( isset( $product_details['product_price']) && $product_details['product_price'] !='' ) {
                                   
                                        $input1['offers'] = array(
                                                        '@type'	        => 'Offer',
                                                        'availability'      => saswp_remove_warnings($product_details, 'product_availability', 'saswp_string'),
                                                        'price'             => saswp_remove_warnings($product_details, 'product_price', 'saswp_string'),
                                                        'priceCurrency'     => saswp_modify_currency_code(saswp_remove_warnings($product_details, 'product_currency', 'saswp_string')),
                                                        'url'               => saswp_get_permalink(),
                                                        'priceValidUntil'   => saswp_remove_warnings($product_details, 'product_priceValidUntil', 'saswp_string')
                                                    );
                                    
							
                            }else{
                                
                            if ( isset( $product_details['product_varible_price']) && $product_details['product_varible_price']){

                                if( isset($sd_data['saswp-single-price-product']) && $sd_data['saswp-single-price-product'] == 1 ){

                                        $price = max($product_details['product_varible_price']);

                                    if ( ! empty( $sd_data['saswp-single-price-type']) && $sd_data['saswp-single-price-type'] == 'low'){
                                        $price = min($product_details['product_varible_price']);
                                    }

                                    
                                        $input1['offers'] = array(
                                            '@type'	        => 'Offer',
                                            'availability'      => saswp_remove_warnings($product_details, 'product_availability', 'saswp_string'),
                                            'price'             => $price,
                                            'priceCurrency'     => saswp_modify_currency_code(saswp_remove_warnings($product_details, 'product_currency', 'saswp_string')),
                                            'url'               => saswp_get_permalink(),
                                            'priceValidUntil'   => saswp_remove_warnings($product_details, 'product_priceValidUntil', 'saswp_string')
                                        );
                                   

                                }else{
                                
                                    $input1['offers']['@type']         = 'AggregateOffer';
                                    $input1['offers']['lowPrice']      = min($product_details['product_varible_price']);
                                    $input1['offers']['highPrice']     = max($product_details['product_varible_price']);
                                    $input1['offers']['priceCurrency'] = saswp_modify_currency_code(saswp_remove_warnings($product_details, 'product_currency', 'saswp_string'));
                                    $input1['offers']['availability']  = saswp_remove_warnings($product_details, 'product_availability', 'saswp_string');
                                    $input1['offers']['offerCount']    = count($product_details['product_varible_price']);

                                }

                            
                            }

                           }                              
                            
                           if($schema_type == 'SoftwareApplication'){
                            $input1['applicationCategory'] = $product_details['product_category'];     
                           }

                          if ( isset( $product_details['product_gtin8']) && $product_details['product_gtin8'] !='' ) {
                            $input1['gtin8'] = esc_attr( $product_details['product_gtin8']);  
                          }
                          if ( isset( $product_details['product_gtin13']) && $product_details['product_gtin13'] !='' ) {
                            $input1['gtin13'] = esc_attr( $product_details['product_gtin13']);  
                          }
                          if ( isset( $product_details['product_gtin12']) && $product_details['product_gtin12'] !='' ) {
                            $input1['gtin12'] = esc_attr( $product_details['product_gtin12']);  
                          }
                          if ( isset( $product_details['product_mpn']) && $product_details['product_mpn'] !='' ) {
                            $input1['mpn'] = esc_attr( $product_details['product_mpn']);  
                          }
                          if ( isset( $product_details['product_isbn']) && $product_details['product_isbn'] !='' ) {
                            $input1['isbn'] = esc_attr( $product_details['product_isbn']);  
                          }
                          if ( isset( $product_details['product_brand']) && $product_details['product_brand'] !='' ) {
                            $input1['brand'] =  array('@type'=>'Brand','name'=> esc_attr( $product_details['product_brand']));  
                          }                                     
                          if ( isset( $product_details['product_review_count']) && $product_details['product_review_count'] >0 && isset($product_details['product_average_rating']) && $product_details['product_average_rating'] >0){
                               $input1['aggregateRating'] =  array(
                                                                '@type'         => 'AggregateRating',
                                                                'ratingValue'	=> esc_attr( $product_details['product_average_rating']),
                                                                'reviewCount'   => (int)esc_attr( $product_details['product_review_count']),       
                               );
                          }                                      
                          if ( ! empty( $product_details['product_reviews']) ) {

                              $reviews = array();

                              foreach ( $product_details['product_reviews'] as $review){

                                  $reviews[] = array(
                                                                '@type'	=> 'Review',
                                                                'author'	=> array('@type' => 'Person', 'name' => $review['author'] ? esc_attr( $review['author']) : 'Anonymous'),
                                                                'datePublished'	=> esc_html( $review['datePublished']),
                                                                'description'	=> $review['description'],  
                                                                'reviewRating'  => array(
                                                                        '@type'	=> 'Rating',
                                                                        'bestRating'	=> '5',
                                                                        'ratingValue'	=> $review['reviewRating'] ? esc_attr( $review['reviewRating']) : '5',
                                                                        'worstRating'	=> '1',
                                                                )  
                                  );

                              }
                              $input1['review'] =  $reviews;
                          }                                                                                                    
                        }else{

                            $input1['@context']              = saswp_context_url();
                            $input1['@type']                 = $schema_type;
                            $input1['@id']                   = saswp_get_permalink().'#'.$schema_type;                                                                                                                                                                                                                                                                                        
                        } 
                        
                        if ( ! isset( $input1['review']) ) {
                           $input1 = saswp_append_fetched_reviews($input1); 
                        }
                                            
                    break;

                default:
                    break;
            }                                    
            return $input1;
            
        }
        
        /**
         * This function returns the featured image for the current post.
         * If featured image is not set than it gets default schema image from MISC settings tab
         * @global type $sd_data
         * @return type array
         */
        public function saswp_get_featured_image() {
            
            global $post, $sd_data, $saswp_featured_image;

            $input2              = array();
            $image_details       = array();
            $image_resize        = false;
            $multiple_size       = false;
            $term_id             = 0;

            if( (isset($sd_data['saswp-image-resizing']) && $sd_data['saswp-image-resizing'] == 1) || !isset($sd_data['saswp-image-resizing']) ) {
                $image_resize = true;
            }

            if( (isset($sd_data['saswp-multiple-size-image']) && $sd_data['saswp-multiple-size-image'] == 1) || !isset($sd_data['saswp-multiple-size-image']) ) {
                $multiple_size = true;
            }

            $image_id 	            = get_post_thumbnail_id();
            
            if(empty($saswp_featured_image[$image_id]) ) {
                                
                $image_alt     = get_post_meta( $image_id, '_wp_attachment_image_alt', true );

                $saswp_featured_image[$image_id]            = wp_get_attachment_image_src($image_id, 'full'); 
                if ( ! empty( $image_alt) ) {           
                    $saswp_featured_image[$image_id]['caption'] = $image_alt;
                }

            }
                
            $image_details = $saswp_featured_image[$image_id];	 
                   
            if( is_array($image_details) && !empty($image_details) ) {                                
                                                                                    
                                        if($image_resize){

                                            if( ( (isset($image_details[1]) && ($image_details[1] < 1200)) || (isset($image_details[2]) && ($image_details[2] < 675)) ) && function_exists('saswp_aq_resize') ) {
                                                
                                                $targetHeight = 1200;
                                                
                                                if( ($image_details[1] > 0) && ($image_details[2] > 0) ){                                            
                                                    $img_ratio    = $image_details[1] / $image_details[2];
                                                    $targetHeight = round ( 1200 / $img_ratio );                                                
                                                }
                                                
                                                if($multiple_size){
                                                    
                                                    $min_val    =   min ( $image_details[1], $targetHeight );

                                                    if($targetHeight < 675){
    
                                                        $width  = array ( 1200, 1200, 1200, $min_val );
                                                        $height = array ( 900, 720, 675, $min_val );
    
                                                    }else{
    
                                                        $width  = array ( 1200, 1200, 1200, $min_val );
                                                        $height = array ( $targetHeight, 900, 675, $min_val );
    
                                                    }
                                                    
                                                }else{
    
                                                    if($targetHeight < 675){
    
                                                        $width  = array(1200);
                                                        $height = array(720);
    
                                                    }else{
    
                                                        $width  = array(1200);
                                                        $height = array($targetHeight);
                                                        
                                                    }
                                                    
                                                }                                                                                        
                                                
                                                for($i = 0; $i < count($width); $i++){
                                                    
                                                    $resize_image = saswp_aq_resize( $image_details[0], $width[$i], $height[$i], true, false, true );
                                                    
                                                    if ( isset( $resize_image[0]) && $resize_image[0] !='' && isset($resize_image[1]) && isset($resize_image[2]) ){
                                                                                                                                                            
                                                        $input2['image'][$i]['@type']  = 'ImageObject';
                                                        
                                                        if($i == 0){                                                        
                                                            $input2['image'][$i]['@id']    = saswp_get_permalink().'#primaryimage';                                                        
                                                        }
                                                        
                                                        $input2['image'][$i]['url']    = esc_url($resize_image[0]);
                                                        $input2['image'][$i]['width']  = esc_attr( $resize_image[1]);
                                                        $input2['image'][$i]['height'] = esc_attr( $resize_image[2]);  

                                                        if ( ! empty( $image_details['caption']) ) {
                                                                $input2['image'][$i]['caption'] = $image_details['caption'];  
                                                        }
                                                        
                                                    }                                                                                                                                                                                                
                                                }
                                                
                                                if ( ! empty( $input2) ) {
                                                    foreach( $input2 as $arr){
                                                        $input2['image'] = array_values($arr);
                                                    }
                                                }
                                                                                                                                                                                                                                
                                            }else{

                                                if ( isset( $image_details[1]) ) {

                                                    if ( $multiple_size ) {

                                                        $width  = array ( $image_details[1], 1200, 1200 );
                                                        $height = array ( $image_details[2], 900, 675 );
                                                        $height_array = array ( 900, 675 );
                                                        $min_val    =   min ( $image_details[1], $image_details[2] );

                                                        if ( $image_details[1] == 1200 && in_array ( $image_details[2], $height_array ) ) {
                                                            
                                                            $width_array    =  array ( 1200, 1200, $min_val );   
                                                            $height_array   =  array ( 675, 900, $min_val );   
                                                            if ( $image_details[2] == 900 ) {
                                                                $height_array   =  array ( 900, 675, $min_val );
                                                            }
                                                            
                                                            $width      =   $width_array;
                                                            $height     =   $height_array;   

                                                        } else {
                                                            $width[]    =   $min_val;
                                                            $height[]   =   $min_val;
                                                        }

                                                    }else{
                                                        $width  = array ( $image_details[1] );
                                                        $height = array ( $image_details[2] );
                                                    }  
                                                                                                   
                                                   for($i = 0; $i < count($width); $i++){
                                                        
                                                            $resize_image = saswp_aq_resize( $image_details[0], $width[$i], $height[$i], true, false, true );
                                                        
                                                            if ( isset( $resize_image[0]) && $resize_image[0] != '' && isset($resize_image[1]) && isset($resize_image[2]) ){
    
                                                                    $input2['image'][$i]['@type']  = 'ImageObject';
                                                                    
                                                                    if($i == 0){
                                                            
                                                                    $input2['image'][$i]['@id']    = saswp_get_permalink().'#primaryimage'; 
                                                                    
                                                                    }
                                                                    
                                                                    $input2['image'][$i]['url']    = esc_url($resize_image[0]);
                                                                    $input2['image'][$i]['width']  = esc_attr( $resize_image[1]);
                                                                    $input2['image'][$i]['height'] = esc_attr( $resize_image[2]);

                                                                    if ( ! empty( $image_details['caption']) ) {
                                                                        $input2['image'][$i]['caption'] = $image_details['caption'];
                                                                    }
    
                                                            }
                                                                                                            
                                                    }
                                                }                                                                                                                                                                                        
                                                
                                            }

                                        }                                                                                         
                                        
                                        if(empty($input2) && isset($image_details[0]) && $image_details[0] !='' && isset($image_details[1]) && isset($image_details[2]) ){
                                            
                                                $input2['image'][0]['@type']  = 'ImageObject';
                                                $input2['image'][0]['@id']    = saswp_get_permalink().'#primaryimage';
                                                $input2['image'][0]['url']    = esc_url($image_details[0]);
                                                $input2['image'][0]['width']  = esc_attr( $image_details[1]);
                                                $input2['image'][0]['height'] = esc_attr( $image_details[2]);

                                                if ( ! empty( $image_details['caption']) ) {
                                                    $input2['image'][0]['caption'] = $image_details['caption'];  
                                                }
                                            
                                        }
                                                                                                                                                                                                                                         
                             }
                                                       
                          //Get All the images available on post   
                           
                          if( (isset($sd_data['saswp-other-images']) && $sd_data['saswp-other-images'] == 1) || !isset($sd_data['saswp-other-images']) || ! empty( $sd_data['saswp-archive-images'] ) ){
                          
                          $content          =   '';
                          $queried_object   =   get_queried_object();
                          if ( ! empty( $sd_data['saswp-archive-images'] ) ) {
                            if (  is_object( $queried_object ) && ! empty( $queried_object->term_id ) && ! empty( $queried_object->description ) ) {
                                $content        =   $queried_object->description;
                                $input2         =   array();
                                $term_id        =   $queried_object->term_id;
                            }  
                          }
                          
                          if ( ( isset( $sd_data['saswp-other-images'] ) && $sd_data['saswp-other-images'] == 1 ) || ! isset( $sd_data['saswp-other-images'] ) ) {
                            $content        =   get_the_content(null, false, $post); 
                          }  
                          
                          if($content){
                              
                          $regex   = '/<img(.*?)src="(.*?)"(.*?)>/';                          
                          @preg_match_all( $regex, $content, $attachments ); 
                                                                                                                                                                                      
                          $attach_images = array();
                          
                          if ( ! empty( $attachments) ) {
                              
                              $attach_details   = saswp_get_attachment_details($attachments[2], $post->ID);
                              
                              $k = 0;
                              
                              foreach ( $attachments[2] as $att_key => $attachment) {
                                                                                                                                       
                                  if ( is_array( $attach_details) && !empty($attach_details) ) {
                                                                            
                                                if( $attachment !='' && saswp_validate_url($attachment) ){
                                                    $attach_images['image'][$k]['@type']  = 'ImageObject';                                                
                                                    $attach_images['image'][$k]['url']    = esc_url($attachment);
                                                    $attach_images['image'][$k]['width']  = isset($attach_details[$k][0]) ? $attach_details[$k][0] : 0;
                                                    $attach_images['image'][$k]['height'] = isset($attach_details[$k][1]) ? $attach_details[$k][1] : 0;
                                                    if ( isset( $attachments[3]) && !empty($attachments[3]) ) {
                                                        if ( is_array( $attachments[3]) ) {
                                                            foreach ( $attachments[3] as $aimg_key => $aimg_value) {
                                                                if($att_key == $aimg_key){
                                                                    $explode_aimg_value = explode('"',$aimg_value);
                                                                    if ( isset( $explode_aimg_value[1]) && !empty($explode_aimg_value[1]) ) {
                                                                        $attach_images['image'][$k]['caption'] = $explode_aimg_value[1];
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                                                                      
                                  }
                                  
                                  $k++;
                              }
                              
                          }
                          
                          if ( ! empty( $attach_images) && is_array($attach_images) ) {
                                                            
                              if ( isset( $input2['image']) ) {
                                                                                                                                     
                                   if ( isset( $attach_images['image']) ) {
                                       $input2['image'] = array_merge($input2['image'], $attach_images['image']);
                                   }
                                                                                                                                   
                              }else{
                                  
                                  if($attach_images &&  isset($attach_images['image']) ) {
                                      
                                      foreach( $attach_images['image'] as $key => $image){
                                               
                                          if($key == 0){
                                              
                                            if($image['width'] < 1200){
                                                
                                                $resized_image = saswp_aq_resize( $image['url'], 1200, 675, true, false, true );                                                                                                
                                                
                                                if ( is_array( $resized_image) && !empty($resized_image) ) {
                                                    
                                                    if ( isset( $resized_image[0]) && $resized_image[0] !='' ) {
                                                        $attach_images['image'][$key]['url']    =   $resized_image[0];
                                                        $attach_images['image'][$key]['width']  =   $resized_image[1];
                                                        $attach_images['image'][$key]['height'] =   $resized_image[2];                                                
                                                    }
                                                                                                        
                                                }
                                                
                                            }                                             
                                            
                                            if ( $term_id > 0 ){
                                                $attach_images['image'][$key]['@id']    =   get_term_link( $term_id ).'#primaryimage';
                                            }else {                                            
                                                $attach_images['image'][$key]['@id']    =   saswp_get_permalink().'#primaryimage';
                                            }                                            
                                          }                                                                                         
                                      }
                                      
                                  }  
                                  
                                  $input2 = $attach_images;
                              }
                                                            
                          }
                          
                          }
                              
                          }   
                          
                          if(empty($input2) ) {
                              
                            if ( isset( $sd_data['sd_default_image']['url']) && $sd_data['sd_default_image']['url'] !='' ) {
                                        
                                    $input2['image']['@type']  = 'ImageObject';
                                    $input2['image']['@id']    = saswp_get_permalink().'#primaryimage';
                                    $input2['image']['url']    = esc_url($sd_data['sd_default_image']['url']);
                                    $input2['image']['width']  = esc_attr( $sd_data['sd_default_image_width']);
                                    $input2['image']['height'] = esc_attr( $sd_data['sd_default_image_height']);                                                                 
                                            
                            }
                                                            
                          }
                                                    
                          return $input2;
        }
        /**
         * This function gets the publisher from schema settings panel 
         * @global type $sd_data
         * @param type $d_logo
         * @return type array
         */
        public function saswp_get_publisher($d_logo = null){
                        
                        global $sd_data, $saswp_custom_logo;  
                                                                        
                        $publisher    = array();
                        $default_logo = array();
                        $custom_logo  = array();
                                      
                        $logo      = isset($sd_data['sd_logo']['url']) ?     $sd_data['sd_logo']['url']:'';	
			            $height    = isset($sd_data['sd_logo']['height']) ?  $sd_data['sd_logo']['height']:'';
			            $width     = isset($sd_data['sd_logo']['width']) ?   $sd_data['sd_logo']['width']:'';
                        $site_name = isset($sd_data['sd_name']) && $sd_data['sd_name'] !='' ? $sd_data['sd_name']:get_bloginfo();
                                                                                                                       
                        if($logo =='' && $height =='' && $width ==''){
                                                                                    
                            if(!$saswp_custom_logo){
                                
                                $custom_logo_id    = get_theme_mod( 'custom_logo' );     
                                $img_details       = wp_get_attachment_image_src( $custom_logo_id, 'full');                                  
                                if ( isset( $img_details[0]) ) {
                                    $img_details = saswp_aq_resize( $img_details[0], 600, 60, true, false, true );
                                }
                                
                                $saswp_custom_logo =  $img_details;                              
                            }   
                                                     
                            $custom_logo = $saswp_custom_logo;                                                                               
                            if ( isset( $custom_logo) && is_array($custom_logo) ) {
                                
                                $logo           = array_key_exists(0, $custom_logo)? $custom_logo[0]:'';                                
                                $width          = array_key_exists(1, $custom_logo)? $custom_logo[1]:'';
                                $height         = array_key_exists(2, $custom_logo)? $custom_logo[2]:'';
                            
                            }
                                                        
                        }                            
                        
                        if($site_name){
                                                    
                            $publisher['publisher']['@type']         = 'Organization';
                            $publisher['publisher']['name']          = esc_attr( $site_name);                            
                            $publisher['publisher']['url']           = get_site_url();
                            if ( isset( $sd_data['sd_url']) && !empty($sd_data['sd_url']) ) {
                                if(filter_var($sd_data['sd_url'], FILTER_VALIDATE_URL) ) {
                                    $publisher['publisher']['url']           = $sd_data['sd_url'];
                                }
                            }
                            
                            if($logo !='' && $height !='' && $width !='' ) {
                                                                             
                            $publisher['publisher']['logo']['@type'] = 'ImageObject';
                            $publisher['publisher']['logo']['url']   = esc_url($logo);
                            $publisher['publisher']['logo']['width'] = esc_attr( $width);
                            $publisher['publisher']['logo']['height']= esc_attr( $height);                        
                             
                            $default_logo['url']    = esc_url($logo);
                            $default_logo['height'] = esc_attr( $height);
                            $default_logo['width']  = esc_attr( $width);
                            
                          }
                                                        
                        }
                                                                          
                        if($d_logo){
                            return $default_logo;
                        }else{
                            return $publisher;
                        }                        
                        
        }

        /**
         * Fetch custom group fields and add it
         * */
        public function saswp_get_cpt_meta_keys($fields)
        {
            $cpt_text_fields = array();
            $cpt_file_fields = array();
            if(class_exists('CPT_Field_Groups') ) {
                $field_groups = cpt_field_groups()->get_registered_groups();

                $field_groups = get_posts(
                    array(
                        'posts_per_page' => -1,
                        'post_type'      => CPT_UI_PREFIX . '_field',
                        'post_status'    => 'publish',
                    )
                );

                if ( ! empty( $field_groups) && is_array($field_groups) ) {
                    foreach ( $field_groups as $grp_key => $grp_value) {
                        $cpt_fields = array();
                        $cpt_fields       = ! empty( get_post_meta( $grp_value->ID, 'fields', true ) ) ? get_post_meta( $grp_value->ID, 'fields',true ) : array();
                        if ( ! empty( $cpt_fields) && is_array($cpt_fields) ) {
                            foreach ( $cpt_fields as $cpt_key => $cpt_value) {
                                if ( ! empty( $cpt_value) && is_array($cpt_value) ) {
                                    if ( isset( $cpt_value['key']) && $cpt_value['label']){
                                        if ( 'file' == $cpt_value['type'] ) {
                                            $cpt_file_fields[$cpt_value['key']] = $cpt_value['label'];
                                        }else{
                                            $cpt_text_fields[$cpt_value['key']] = $cpt_value['label'];    
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                if ( ! empty( $cpt_text_fields ) ) {
                    $fields['text'][] = array(
                        'label'     => __( 'TotalPress Custom Fields', 'schema-and-structured-data-for-wp' ),
                        'meta-list' => $cpt_text_fields,
                    );
                }

                if ( ! empty( $cpt_file_fields ) ) {
                    $fields['image'][] = array(
                        'label'     => __( 'TotalPress Custom Fields', 'schema-and-structured-data-for-wp' ),
                        'meta-list' => $cpt_file_fields,
                    );
                }
            }
            return $fields;
        }

        /**
         * Get custom filed object
         * @since 1.23
         * $field string
         * */
        public function saswp_get_cpt_field_object($field)
        {
            $cpt_field_object = array();
            global $post;
            if(class_exists('CPT_Fields') ) {
                $cpt_field_object = cpt_fields()->get_field_object( $field, \CPT_Field_Groups::SUPPORT_TYPE_CPT, $post->post_type );
                if ( isset( $cpt_field_object['type']) && $cpt_field_object['type'] == 'file'){
                    if ( isset( $cpt_field_object['extra']) && isset($cpt_field_object['extra']['types']) ) {
                        if(empty($cpt_field_object['extra']['types']) || (isset($cpt_field_object['extra']['types'][0]) && $cpt_field_object['extra']['types'][0] == 'image') ) {
                            $cpt_field_object['type'] = 'image';    
                        }
                    }
                }
            }
            return $cpt_field_object;
        }

        public function saswp_modify_custom_fields_group_clbk($response, $field, $schema_type)
        {
            global $post;
            $cpt_response = null;
            if(class_exists('CPT_Field_Groups') ) {
                $cpt_object = $this->saswp_get_cpt_field_object($field);
                $post_meta_value = get_post_meta($post->ID, $field, true );
                if(is_archive() ) {
                    $term_id = get_queried_object_id(); // Get the current term's ID
                    $post_meta_value = get_term_meta($term_id, $field, true);
                }
                if ( ! empty( $cpt_object) && is_array($cpt_object) ) {
                    if ( isset( $cpt_object['type']) && $cpt_object['type'] == 'image'){                             
                        $img_details           = saswp_get_image_by_id($post_meta_value);
                        if ( ! empty( $img_details) && is_array($img_details) && isset($img_details['url']) ) {
                            $cpt_response = $img_details['url'];     
                        }
                    }elseif ( isset( $cpt_object['type']) && $cpt_object['type'] == 'repeater'){

                        switch ($schema_type) {

                            case 'FAQ':                                                                                
                                if ( ! empty( $post_meta_value) && is_array($post_meta_value) && isset($post_meta_value[0]) ) {

                                    foreach( $post_meta_value as $value){
                                        if ( ! empty( $value) && is_array($value) ) {    
                                            $main_entity = array();

                                            $ar_values = array_values($value);
                                            
                                            $main_entity['@type']                   = 'Question';
                                          
                                            if ( ! empty( $ar_values[0]) ) {
                                                $main_entity['name'] = $ar_values[0]; 
                                            }
                                            $main_entity['acceptedAnswer']['@type'] = 'Answer';
                                            if ( ! empty( $ar_values[1]) ) {
                                                $main_entity['acceptedAnswer']['text'] = $ar_values[1];
                                            }
                                            if ( ! empty( $ar_values[2]) ) {
                                                $main_entity['acceptedAnswer']['image'] = $ar_values[2];
                                            }
                                            
                                            $cpt_response [] = $main_entity;  
                                        }                                   
                                    }
                                }

                                break;

                                case 'HowTo':
                                    
                                    if(strpos(strtolower($cpt_object['label']), "tool") !== false){

                                        if ( ! empty( $post_meta_value) && is_array($post_meta_value) && isset($post_meta_value[0]) ) {
    
                                            foreach( $post_meta_value as $value){
                                                if ( ! empty( $value) && is_array($value) ) { 
                                                    $main_entity = array();
    
                                                    $ar_values = array_values($value);
                                                    
                                                    $main_entity['@type']                   = 'HowToTool';
                                                  
                                                    if ( isset( $ar_values[0]) && !empty($ar_values[0]) ) {
                                                        $main_entity['name'] = $ar_values[0]; 
                                                    }
                                                    if( $ar_values[1] && !empty($ar_values[1]) ) {
                                                        $main_entity['url'] = $ar_values[1]; 
                                                    }                                                        
                                                    if ( isset( $ar_values[2]) && !empty($ar_values[2]) ) {
                                                        if ( isset( $cpt_object['extra']) && isset($cpt_object['extra']['fields']) && isset($cpt_object['extra']['fields'][2]) ) {
                                                            $extra_field = $cpt_object['extra']['fields'][2];
                                                            if ( isset( $extra_field['extra']) && isset($extra_field['extra']['types']) && isset($extra_field['extra']['types'][0]) && $extra_field['extra']['types'][0] == 'image' || empty($extra_field['extra']['types'][0]) ) {
                                                                $image_details           = saswp_get_image_by_id($ar_values[2]);
                                                                if ( ! empty( $image_details) && isset($image_details['url']) ) {
                                                                    $main_entity['image'] = $image_details['url'];
                                                                }
                                                            }
                                                        }
                                                    }

                                                    $cpt_response [] = $main_entity;  
                                                }
                                               
                                            }
                                            
                                        }

                                    }
                                    
                                    if(strpos(strtolower($cpt_object['label']), "supp") !== false){
                                        
                                        if ( ! empty( $post_meta_value) && is_array($post_meta_value) && isset($post_meta_value[0]) ) {

                                            foreach( $post_meta_value as $value){
                                                if ( ! empty( $value) && is_array($value) ) {

                                                    $main_entity = array();
    
                                                    $ar_values = array_values($value);
                                                    
                                                    $main_entity['@type']                   = 'HowToSupply';
                                                  
                                                    if ( ! empty( $ar_values[0]) ) {
                                                        $main_entity['name'] = $ar_values[0]; 
                                                    }
                                                    if ( ! empty( $ar_values[1]) ) {
                                                        $main_entity['url'] = $ar_values[1]; 
                                                    }                                                        
                                                    if ( isset( $ar_values[2]) && !empty($ar_values[2]) ) {
                                                        if ( isset( $cpt_object['extra']) && isset($cpt_object['extra']['fields']) && isset($cpt_object['extra']['fields'][2]) ) {
                                                            $extra_field = $cpt_object['extra']['fields'][2];
                                                            if ( isset( $extra_field['extra']) && isset($extra_field['extra']['types']) && isset($extra_field['extra']['types'][0]) && $extra_field['extra']['types'][0] == 'image' || empty($extra_field['extra']['types'][0]) ) {
                                                                $image_details           = saswp_get_image_by_id($ar_values[2]);
                                                                if ( ! empty( $image_details) && isset($image_details['url']) ) {
                                                                    $main_entity['image'] = $image_details['url'];
                                                                }
                                                            }
                                                        }
                                                    }
                                                    
                                                    $cpt_response [] = $main_entity;  
                                                }                                 
                                        
                                            }
                                            
                                        }

                                    }
                            
                                    if(strpos(strtolower($cpt_object['label']), "step") !== false){

                                        if ( ! empty( $post_meta_value) && is_array($post_meta_value) && isset($post_meta_value[0]) ) {

                                            foreach( $post_meta_value as $value){
                                                if ( ! empty( $value) && is_array($value) ) {
                                                    $main_entity = array();
    
                                                    $ar_values = array_values($value);
                                                    
                                                    $main_entity['@type']                   = 'HowToStep';
                                                  
                                                    if ( ! empty( $ar_values[0]) ) {
                                                        $main_entity['name'] = $ar_values[0]; 
                                                    }
                                                    if ( ! empty( $ar_values[1]) ) {
                                                        $main_entity['url'] = $ar_values[1]; 
                                                    }
                                                    if ( ! empty( $ar_values[2]) ) {
                                                        $main_entity['text'] = $ar_values[2]; 
                                                    }                                                        
                                                    if ( isset( $ar_values[3]) && !empty($ar_values[3]) ) {
                                                        if ( isset( $cpt_object['extra']) && isset($cpt_object['extra']['fields']) && isset($cpt_object['extra']['fields'][3]) ) {
                                                            $extra_field = $cpt_object['extra']['fields'][3];
                                                            if ( isset( $extra_field['extra']) && isset($extra_field['extra']['types']) && isset($extra_field['extra']['types'][0]) && $extra_field['extra']['types'][0] == 'image' || empty($extra_field['extra']['types'][0]) ) {
                                                                $image_details           = saswp_get_image_by_id($ar_values[3]);
                                                                if ( ! empty( $image_details) && isset($image_details['url']) ) {
                                                                    $main_entity['image'] = $image_details['url'];
                                                                }
                                                            }
                                                        }
                                                    }
                                                    
                                                    $cpt_response [] = $main_entity;  
                                                }                                 
                                               
                                            }
                                            
                                        }

                                    }

                                    break;
                                
                            
                            default:
                                if ( ! empty( $post_meta_value) && is_array($post_meta_value) && isset($post_meta_value[0]) ) {
                                    foreach( $post_meta_value as $value){
                                        if ( ! empty( $value) && is_array($value) ) { 
                                            foreach ( $value as $val_key => $val) {
                                                $cpt_response .= $val.' '; 
                                            }   
                                        }
                                    }
                                }     
                                break;
                        }
                    }else{ 
                        if(is_archive() ) {
                            $term_id = get_queried_object_id(); // Get the current term's ID
                            $cpt_response = get_term_meta($term_id, $field, true);
                        }else{
                            $cpt_response = get_post_meta($post->ID, $field, true );
                        }       
                    }
                }
            }
            
            if ( ! empty( $cpt_response) ) {
                $response = $cpt_response;
            }
            return $response;
        }
                
}
if (class_exists('SASWP_Output_Service')) {
	    $object = new SASWP_Output_Service();
        $object->saswp_service_hooks();
};