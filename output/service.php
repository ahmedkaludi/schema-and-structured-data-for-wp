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

Class saswp_output_service{           
        
        /**
         * List of hooks used in current class
         */
        public function saswp_service_hooks(){
            
           add_action( 'wp_ajax_saswp_get_custom_meta_fields', array($this, 'saswp_get_custom_meta_fields')); 
           add_action( 'wp_ajax_saswp_get_schema_type_fields', array($this, 'saswp_get_schema_type_fields'));            
           add_action( 'wp_ajax_saswp_get_meta_list', array($this, 'saswp_get_meta_list'));            
           add_filter( 'saswp_modify_post_meta_list', array( $this, 'saswp_get_acf_meta_keys' ) );
           
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

						$post_id = get_the_id();
						
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

			return $fields;
            
        }
        /**
         * Ajax function to get meta list 
         * @return type json
         */
        public function saswp_get_meta_list(){
            
            if ( ! isset( $_GET['saswp_security_nonce'] ) ){
                return; 
             }
             if ( !wp_verify_nonce( $_GET['saswp_security_nonce'], 'saswp_ajax_check_nonce' ) ){
                return;  
             }
            
            $response = array();    
            $mappings_file = SASWP_DIR_NAME . '/core/array-list/meta_list.php';

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
        public function saswp_get_meta_list_value($key, $field, $schema_post_id){
            
            global $post;
            
            $fixed_image       = get_post_meta($schema_post_id, 'saswp_fixed_image', true) ;            
                        
            $response = null;
            
            switch ($field) {
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
                    $response = @get_the_title();                    
                    break;
                case 'post_content':
                    $response = @get_the_content();                        
                    break;
                case 'post_category':
                    $categories = get_the_category();
                    if($categories){
                        foreach ($categories as $category){
                            if(isset($category->name)){
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

                    if(isset($fixed_text[$key])){
                        
                        $explod = @explode('.', $fixed_text[$key]);                        
                        $ext    = @strtolower(end($explod));           

                        if ($ext == 'jpg' || $ext == 'png' || $ext == 'gif' || $ext == 'jpeg') {
                        
                        $image_details = @getimagesize($fixed_text[$key]);
                        
                        if(is_array($image_details)){
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
                    
                    $response = '';
                    
                    $taxonomy_term       = get_post_meta( $schema_post_id, 'saswp_taxonomy_term', true) ; 
                                        
                    if($taxonomy_term[$key] == 'all'){
                        
                        $post_taxonomies      = get_post_taxonomies( $post->ID );
                                                
                        if($post_taxonomies){
                            
                            foreach ($post_taxonomies as $taxonomie ){
                                
                                $terms               = get_the_terms( $post->ID, $taxonomie);
                                
                                if($terms){
                                    foreach ($terms as $term){
                                        $response .= $term->name.', ';
                                    }    
                                }
                                
                            }
                            
                        }                        
                        
                    }else{
                    
                        $terms               = get_the_terms( $post->ID, $taxonomy_term[$key]);
                        
                        if($terms){
                            foreach ($terms as $term){
                                $response .= $term->name.', ';
                            }    
                        }
                        
                    }
                                                                                                    
                    if($response){
                        $response = substr(trim($response), 0, -1); 
                    }
                                                            
                    break;
                    
                case 'custom_field':
                    
                    $cus_field   = get_post_meta($schema_post_id, 'saswp_custom_meta_field', true);                    
                    $response    = get_post_meta($post->ID, $cus_field[$key], true); 
                    
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
                    
                    if(function_exists('get_avatar_data')){
                        $author_image	= get_avatar_data($author_id);      
                    }                                                          
                    $response['@type']  = 'ImageObject';
                    $response['url']    = $author_image['url'];
                    $response['width']  = $author_image['height']; 
                    $response['height'] = $author_image['width'];

                    break;
                case 'site_logo':
                    
                    $sizes = array(
                            'width'  => 600,
                            'height' => 60,
                            'crop'   => false,
                    ); 

                    $custom_logo_id = get_theme_mod( 'custom_logo' );     

                    if($custom_logo_id){

                        $custom_logo    = @wp_get_attachment_image_src( $custom_logo_id, $sizes);

                    }

                    if(isset($custom_logo) && is_array($custom_logo)){

                         $response['@type']  = 'ImageObject';
                         $response['url']    = array_key_exists(0, $custom_logo)? $custom_logo[0]:'';
                         $response['width']  = array_key_exists(2, $custom_logo)? $custom_logo[2]:''; 
                         $response['height'] = array_key_exists(1, $custom_logo)? $custom_logo[1]:'';
                                              
                    }
                break;                    
                default:
                    if(function_exists('get_field_object')){
                     
                        $acf_obj = get_field_object($field);
                                            
                        if($acf_obj){

                            if($acf_obj['type'] == 'image'){
                                
                                $image_id           = get_post_meta($post->ID, $field, true );                                
                                $response           = saswp_get_image_by_id($image_id);                    
                                                                                                            
                            }else if($acf_obj['type'] == 'repeater'){
                                                                                                
                                if(isset($acf_obj['value'])){
                                    foreach($acf_obj['value'] as $value){
                                        foreach ($value as $val){
                                         $response[] = $val;   
                                        }
                                    }
                                }                                
                                                                
                            }else{
                                $response = get_post_meta($post->ID, $field, true );
                            }

                        }else{
                            $response = get_post_meta($post->ID, $field, true );
                        }
                        
                    }else{
                        $response = get_post_meta($post->ID, $field, true );
                    }                    
                    
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
            $review_markup    = array();
            $review_response  = array();
            $main_schema_type = '';
                                                          
            if(!empty($custom_fields)){
                
                foreach ($custom_fields as $key => $field){
                                                                                                                                         
                    $custom_fields[$key] = $this->saswp_get_meta_list_value($key, $field, $schema_post_id);                                           
                                                           
                }   
                
                $schema_type      = get_post_meta( $schema_post_id, 'schema_type', true);                                     
            
                if($schema_type == 'Review'){

                    $main_schema_type = $schema_type;                                                                                  
                    $schema_type = get_post_meta($schema_post_id, 'saswp_review_item_reviewed_'.$schema_post_id, true);
                                        
                    if(isset($custom_fields['saswp_review_name'])){
                        $review_markup['name']                       =    $custom_fields['saswp_review_name'];
                    }
                    if(isset($custom_fields['saswp_review_url'])){
                        $review_markup['url']                       =    saswp_validate_url($custom_fields['saswp_review_url']);
                    }
                    if(isset($custom_fields['saswp_review_description'])){
                        $review_markup['description']                =    $custom_fields['saswp_review_description'];
                    }
                    if(isset($custom_fields['saswp_review_rating_value'])){
                       $review_markup['reviewRating']['@type']       =   'Rating';                                              
                       $review_markup['reviewRating']['ratingValue'] =    $custom_fields['saswp_review_rating_value'];
                       $review_markup['reviewRating']['bestRating']  =   5;
                       $review_markup['reviewRating']['worstRating'] =   1;
                    }
                    if(isset($custom_fields['saswp_review_publisher'])){
                       $review_markup['publisher']['@type']          =   'Organization';                                              
                       $review_markup['publisher']['name']           =    $custom_fields['saswp_review_publisher'];                                              
                       if(isset($custom_fields['saswp_review_publisher_url']) && saswp_validate_url($custom_fields['saswp_review_publisher_url']) ){
                        $review_markup['publisher']['sameAs'] =    array($custom_fields['saswp_review_publisher_url']);
                       }
                    }                    
                    if(isset($custom_fields['saswp_review_author'])){
                       $review_markup['author']['@type']             =   'Person';                                              
                       $review_markup['author']['name']              =    $custom_fields['saswp_review_author'];                                              
                       
                        if(isset($custom_fields['saswp_review_author_url']) && saswp_validate_url($custom_fields['saswp_review_author_url']) ){
                            $review_markup['author']['sameAs'] =    array($custom_fields['saswp_review_author_url']);
                        }
                    }
                     if(isset($custom_fields['saswp_review_date_published'])){

                        if(saswp_validate_date($custom_fields['saswp_review_date_published'], 'Y-m-d\TH:i:sP')){
                            $review_markup['datePublished'] =    $custom_fields['saswp_review_date_published'];
                        }else{
                            $review_markup['datePublished'] =    date('c',strtotime($custom_fields['saswp_review_date_published']));
                        }
                       
                    }
                    
                    if(isset($custom_fields['saswp_review_date_modified'])){

                        if(saswp_validate_date($custom_fields['saswp_review_date_modified'], 'Y-m-d\TH:i:sP')){
                            $review_markup['dateModified'] =    $custom_fields['saswp_review_date_modified'];
                        }else{
                            $review_markup['dateModified'] =    date('c',strtotime($custom_fields['saswp_review_date_modified']));
                        }
                       
                    }

                }
                                   
             switch ($schema_type) {
                 
               case 'Book':      
                      
                    if(isset($custom_fields['saswp_book_name'])){
                     $input1['name'] =    $custom_fields['saswp_book_name'];
                    }
                    if(isset($custom_fields['saswp_book_description'])){
                     $input1['description'] =    wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_book_description'] ));
                    }
                    if(isset($custom_fields['saswp_book_url'])){
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_book_url']);
                    }
                    if(isset($custom_fields['saswp_book_author'])){
                     $input1['author']['@type'] =    'Person';   
                     $input1['author']['name']  =    $custom_fields['saswp_book_author'];
                    
                        if(isset($custom_fields['saswp_book_author_url']) && saswp_validate_url( $custom_fields['saswp_book_author_url'] ) ){
                            $input1['author']['sameAs'] =    array($custom_fields['saswp_book_author_url']);
                        }
                     
                    }
                    if(isset($custom_fields['saswp_book_isbn'])){
                     $input1['isbn'] =    $custom_fields['saswp_book_isbn'];
                    }
                    if(isset($custom_fields['saswp_book_publisher'])){
                     $input1['publisher'] =    $custom_fields['saswp_book_publisher'];
                    }
                    if(isset($custom_fields['saswp_book_no_of_page'])){
                     $input1['numberOfPages'] =    $custom_fields['saswp_book_no_of_page'];
                    }
                    if(isset($custom_fields['saswp_book_image'])){
                     $input1['image']         =    $custom_fields['saswp_book_image'];
                    }
                    if(isset($custom_fields['saswp_book_date_published'])){                        
                     $input1['datePublished'] =    date('c',strtotime($custom_fields['saswp_book_date_published']));
                    }                    
                    if(isset($custom_fields['saswp_book_price_currency']) && isset($custom_fields['saswp_book_price'])){
                        $input1['offers']['@type']         = 'Offer';
                        $input1['offers']['availability']  = $custom_fields['saswp_book_availability'];
                        $input1['offers']['price']         = $custom_fields['saswp_book_price'];
                        $input1['offers']['priceCurrency'] = $custom_fields['saswp_book_price_currency'];
                    }                            
                    if(isset($custom_fields['saswp_book_rating_value']) && isset($custom_fields['saswp_book_rating_count'])){
                        $input1['aggregateRating']['@type']         = 'aggregateRating';
                        $input1['aggregateRating']['worstRating']   =   0;
                        $input1['aggregateRating']['bestRating']    =   5;
                        $input1['aggregateRating']['ratingValue']   = $custom_fields['saswp_book_rating_value'];
                        $input1['aggregateRating']['ratingCount']   = $custom_fields['saswp_book_rating_count'];                                
                    }
                                        
                    break; 
                    
                case 'MusicPlaylist':      
                    
                    if(isset($custom_fields['saswp_music_playlist_name'])){
                     $input1['name'] =    $custom_fields['saswp_music_playlist_name'];
                    }
                    if(isset($custom_fields['saswp_music_playlist_description'])){
                     $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_music_playlist_description'] )) ;
                    }
                    if(isset($custom_fields['saswp_music_playlist_url'])){
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_music_playlist_url']);
                    }
                    
                break;
                
                case 'Movie':      
                    
                    if(isset($custom_fields['saswp_movie_name'])){
                     $input1['name'] =    $custom_fields['saswp_movie_name'];
                    }
                    if(isset($custom_fields['saswp_movie_description'])){
                     $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_movie_description'] )) ;
                    }
                    if(isset($custom_fields['saswp_movie_url'])){
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_movie_url']);
                     $input1['sameAs'] =    saswp_validate_url($custom_fields['saswp_movie_url']);
                    }
                    if(isset($custom_fields['saswp_movie_image'])){
                     $input1['image'] =    $custom_fields['saswp_movie_image'];
                    }
                    if(isset($custom_fields['saswp_movie_date_created'])){
                     $input1['dateCreated'] =    $custom_fields['saswp_movie_date_created'];
                    }
                    if(isset($custom_fields['saswp_movie_director'])){
                     $input1['director']['@type']        = 'Person';
                     $input1['director']['name']          = $custom_fields['saswp_movie_director']; 
                    }
                    if(isset($custom_fields['saswp_movie_rating_value']) && isset($custom_fields['saswp_movie_rating_count'])){
                        $input1['aggregateRating']['@type']         = 'aggregateRating';                        
                        $input1['aggregateRating']['ratingValue']   = $custom_fields['saswp_movie_rating_value'];
                        $input1['aggregateRating']['reviewCount']   = $custom_fields['saswp_movie_rating_count'];                                
                    }
                    
                break;
                
                case 'MusicComposition':      
                    
                    if(isset($custom_fields['saswp_music_composition_name'])){
                     $input1['name'] =    $custom_fields['saswp_music_composition_name'];
                    }
                    if(isset($custom_fields['saswp_music_composition_description'])){
                     $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_music_composition_description'] )) ;
                    }
                    if(isset($custom_fields['saswp_music_composition_url'])){
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_music_composition_url']);
                    }
                    if(isset($custom_fields['saswp_music_composition_inlanguage'])){
                     $input1['inLanguage'] =    $custom_fields['saswp_music_composition_inlanguage'];
                    }
                    if(isset($custom_fields['saswp_music_composition_iswccode'])){
                     $input1['iswcCode'] =    $custom_fields['saswp_music_composition_iswccode'];
                    }
                    if(isset($custom_fields['saswp_music_composition_image'])){
                     $input1['image'] =    $custom_fields['saswp_music_composition_image'];
                    }
                    if(isset($custom_fields['saswp_music_composition_lyrics'])){
                     $input1['lyrics']['@type'] = 'CreativeWork';
                     $input1['lyrics']['text']  = $custom_fields['saswp_music_composition_lyrics'];
                    }
                    if(isset($custom_fields['saswp_music_composition_publisher'])){
                     $input1['publisher']['@type'] = 'Organization';
                     $input1['publisher']['name'] = $custom_fields['saswp_music_composition_publisher'];
                    }
                                                              
                    break; 
                case 'Organization':      
                    
                    if(isset($custom_fields['saswp_organization_name'])){
                     $input1['name'] =    $custom_fields['saswp_organization_name'];
                    }
                    if(isset($custom_fields['saswp_organization_description'])){
                     $input1['description'] =    $custom_fields['saswp_organization_description'];
                    }
                    if(isset($custom_fields['saswp_organization_url'])){
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_organization_url']);
                    }                                        
                    if(isset($custom_fields['saswp_organization_street_address'])){
                     $input1['address']['streetAddress'] =    $custom_fields['saswp_organization_street_address'];
                    }                    
                    if(isset($custom_fields['saswp_organization_city'])){
                     $input1['address']['addressLocality'] =    $custom_fields['saswp_organization_city'];
                    }
                    if(isset($custom_fields['saswp_organization_state'])){
                     $input1['address']['addressRegion'] =    $custom_fields['saswp_organization_state'];
                    }
                    if(isset($custom_fields['saswp_organization_country'])){
                     $input1['address']['addressCountry'] =    $custom_fields['saswp_organization_country'];
                    }
                    if(isset($custom_fields['saswp_organization_postal_code'])){
                     $input1['address']['postalCode'] =    $custom_fields['saswp_organization_postal_code'];
                    }
                    if(isset($custom_fields['saswp_organization_telephone'])){
                     $input1['address']['telephone'] =    $custom_fields['saswp_organization_telephone'];
                    }
                    if(isset($custom_fields['saswp_organization_email'])){
                     $input1['address']['email'] =    $custom_fields['saswp_organization_email'];
                    }
                    if(isset($custom_fields['saswp_organization_logo'])){
                     $input1['logo'] =    $custom_fields['saswp_organization_logo'];
                    }
                    if(isset($custom_fields['saswp_organization_image'])){
                     $input1['image'] =    $custom_fields['saswp_organization_image'];
                    }
                    if(isset($custom_fields['saswp_organization_duns'])){
                        $input1['duns'] =    $custom_fields['saswp_organization_duns'];
                    }
                    if(isset($custom_fields['saswp_organization_founder'])){
                        $input1['founder'] =    $custom_fields['saswp_organization_founder'];
                    }
                    if(isset($custom_fields['saswp_organization_founding_date'])){
                        $input1['foundingDate'] =    $custom_fields['saswp_organization_founding_date'];
                    }
                    if(isset($custom_fields['saswp_organization_qualifications'])){
                        $input1['hasCredential'] =    $custom_fields['saswp_organization_qualifications'];
                    }
                    if(isset($custom_fields['saswp_organization_knows_about'])){
                        $input1['knowsAbout'] =    $custom_fields['saswp_organization_knows_about'];
                    }
                    if(isset($custom_fields['saswp_organization_member_of'])){
                        $input1['memberOf'] =    $custom_fields['saswp_organization_member_of'];
                    }
                    if(isset($custom_fields['saswp_organization_parent_organization'])){
                        $input1['parentOrganization'] =    $custom_fields['saswp_organization_parent_organization'];
                    }

                    $sameas = array();
                    if(isset($custom_fields['saswp_organization_website'])){
                        $sameas[] =    $custom_fields['saswp_organization_website'];
                    }
                    if(isset($custom_fields['saswp_organization_facebook'])){
                        $sameas[] =    $custom_fields['saswp_organization_facebook'];
                    }
                    if(isset($custom_fields['saswp_organization_twitter'])){
                        $sameas[] =    $custom_fields['saswp_organization_twitter'];
                    }
                    if(isset($custom_fields['saswp_organization_linkedin'])){
                        $sameas[] =    $custom_fields['saswp_organization_linkedin'];
                    }
                    if($sameas){
                        $input1['sameAs'] = $sameas;
                    }

                    if(isset($custom_fields['saswp_organization_rating_value']) && isset($custom_fields['saswp_organization_rating_count'])){
                        $input1['aggregateRating']['@type']       =   'AggregateRating';                                                
                        $input1['aggregateRating']['ratingValue'] =    $custom_fields['saswp_organization_rating_value'];
                        $input1['aggregateRating']['ratingCount'] =    $custom_fields['saswp_organization_rating_count'];
                    }
                                                                                  
                    break;     
                    
                case 'MusicAlbum':      
                    
                    if(isset($custom_fields['saswp_music_album_name'])){
                     $input1['name'] =    $custom_fields['saswp_music_album_name'];
                    }
                    if(isset($custom_fields['saswp_music_album_url'])){
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_music_album_url']);
                    }
                    if(isset($custom_fields['saswp_music_album_description'])){
                     $input1['description'] =    wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_music_album_description'] ));
                    }
                    if(isset($custom_fields['saswp_music_album_genre'])){
                     $input1['genre'] =    $custom_fields['saswp_music_album_genre'];
                    }
                    if(isset($custom_fields['saswp_music_album_image'])){
                     $input1['image'] =    $custom_fields['saswp_music_album_image'];
                    }
                    if(isset($custom_fields['saswp_music_album_artist'])){
                     $input1['byArtist']['@type']     = 'MusicGroup';
                     $input1['byArtist']['name']      = $custom_fields['saswp_music_album_artist'];
                    }       
                    
                    break;     
                 
                case 'Article':      
                     
                    if(isset($custom_fields['saswp_article_main_entity_of_page'])){
                     $input1['mainEntityOfPage'] =    $custom_fields['saswp_article_main_entity_of_page'];
                    }
                    if(isset($custom_fields['saswp_article_image'])){
                     $input1['image'] =    $custom_fields['saswp_article_image'];
                    }
                    if(isset($custom_fields['saswp_article_url'])){
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_article_url']);
                    }
                    if(isset($custom_fields['saswp_article_body'])){
                     $input1['articleBody'] =    $custom_fields['saswp_article_body'];
                    }
                    if(isset($custom_fields['saswp_article_keywords'])){
                     $input1['keywords'] =    $custom_fields['saswp_article_keywords'];
                    }
                    if(isset($custom_fields['saswp_article_section'])){
                     $input1['articleSection'] =    $custom_fields['saswp_article_section'];
                    }
                    if(isset($custom_fields['saswp_article_headline'])){
                     $input1['headline'] =    $custom_fields['saswp_article_headline'];
                    }                    
                    if(isset($custom_fields['saswp_article_description'])){
                     $input1['description'] =    wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_article_description'] ));
                    }
                    if(isset($custom_fields['saswp_article_date_published'])){
                     $input1['datePublished'] =    $custom_fields['saswp_article_date_published'];
                    }
                    if(isset($custom_fields['saswp_article_date_modified'])){
                     $input1['dateModified'] =    $custom_fields['saswp_article_date_modified'];
                    }                    
                    if(isset($custom_fields['saswp_article_author_name'])){
                     $input1['author']['name'] =    $custom_fields['saswp_article_author_name'];
                    }
                    if(isset($custom_fields['saswp_article_author_description'])){
                     $input1['author']['description'] =    $custom_fields['saswp_article_author_description'];
                    }
                    if(isset($custom_fields['saswp_article_author_url'])){
                     $input1['author']['url'] =    $custom_fields['saswp_article_author_url'];
                    }
                    if(isset($custom_fields['saswp_article_organization_logo']) && isset($custom_fields['saswp_article_organization_name'])){
                     $input1['Publisher']['@type']       =    'Organization';
                     $input1['Publisher']['name']        =    $custom_fields['saswp_article_organization_name'];
                     $input1['Publisher']['logo']        =    $custom_fields['saswp_article_organization_logo'];
                    }                    
                    break; 
                    case 'SpecialAnnouncement':      
                        
                        $location = array();
                        
                        if(isset($custom_fields['saswp_special_announcement_location_type'])){

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
                        if(isset($custom_fields['saswp_special_announcement_category'])){
                            $input1['category'] =    $custom_fields['saswp_special_announcement_category'];
                        }
                        if(isset($custom_fields['saswp_special_announcement_quarantine_guidelines'])){
                         $input1['quarantineGuidelines'] =    $custom_fields['saswp_special_announcement_quarantine_guidelines'];
                        }
                        if(isset($custom_fields['saswp_special_announcement_newsupdates_and_guidelines'])){
                         $input1['newsUpdatesAndGuidelines'] =    $custom_fields['saswp_special_announcement_newsupdates_and_guidelines'];
                        }
                        if(isset($custom_fields['saswp_special_announcement_disease_prevention_info'])){
                            $input1['diseasePreventionInfo'] =    $custom_fields['saswp_special_announcement_disease_prevention_info'];
                        }
                        if(isset($custom_fields['saswp_special_announcement_image'])){
                         $input1['image'] =    $custom_fields['saswp_special_announcement_image'];
                        }
                        if(isset($custom_fields['saswp_special_announcement_url'])){
                         $input1['url'] =    saswp_validate_url($custom_fields['saswp_special_announcement_url']);
                        }                        
                        if(isset($custom_fields['saswp_special_announcement_keywords'])){
                         $input1['keywords'] =    $custom_fields['saswp_special_announcement_keywords'];
                        }                        
                        if(isset($custom_fields['saswp_special_announcement_name'])){
                         $input1['name'] =    $custom_fields['saswp_special_announcement_name'];
                        }                    
                        if(isset($custom_fields['saswp_special_announcement_description'])){
                         $input1['text'] =    wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_special_announcement_description'] ));
                        }
                        if(isset($custom_fields['saswp_special_announcement_date_published'])){
                         $input1['datePublished'] =    $custom_fields['saswp_special_announcement_date_published'];
                        }
                        if(isset($custom_fields['saswp_special_announcement_date_modified'])){
                         $input1['dateModified'] =    $custom_fields['saswp_special_announcement_date_modified'];
                        }
                        if(isset($custom_fields['saswp_special_announcement_date_posted'])){
                          $input1['datePosted'] =    $custom_fields['saswp_special_announcement_date_posted'];
                        }
                        if(isset($custom_fields['saswp_special_announcement_date_expires'])){
                         $input1['expires'] =    $custom_fields['saswp_special_announcement_date_expires'];
                        }                    
                        if(isset($custom_fields['saswp_special_announcement_author_name'])){
                         $input1['author']['name'] =    $custom_fields['saswp_special_announcement_author_name'];
                        }
                        if(isset($custom_fields['saswp_special_announcement_author_description'])){
                         $input1['author']['description'] =    $custom_fields['saswp_special_announcement_author_description'];
                        }
                        if(isset($custom_fields['saswp_special_announcement_author_url'])){
                         $input1['author']['url'] =    $custom_fields['saswp_special_announcement_author_url'];
                        }
                        if(isset($custom_fields['saswp_special_announcement_organization_logo']) && isset($custom_fields['saswp_special_announcement_organization_name'])){
                         $input1['Publisher']['@type']       =    'Organization';
                         $input1['Publisher']['name']        =    $custom_fields['saswp_special_announcement_organization_name'];
                         $input1['Publisher']['logo']        =    $custom_fields['saswp_special_announcement_organization_logo'];
                        }                    
                        break;     
                    
                case 'HowTo':                          
                    if(isset($custom_fields['saswp_howto_schema_id'])){
                     $input1['@id'] =    $custom_fields['saswp_howto_schema_id'];
                    }                    
                    if(isset($custom_fields['saswp_howto_schema_name'])){
                     $input1['name'] =    $custom_fields['saswp_howto_schema_name'];
                    }
                    if(isset($custom_fields['saswp_howto_schema_description'])){
                     $input1['description'] =    wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_howto_schema_description'] ));
                    }
                    if(isset($custom_fields['saswp_howto_ec_schema_currency']) && isset($custom_fields['saswp_howto_ec_schema_value'])){
                     $input1['estimatedCost']['@type']    =    'MonetaryAmount';
                     $input1['estimatedCost']['currency'] =    $custom_fields['saswp_howto_ec_schema_currency'];
                     $input1['estimatedCost']['value']    =    $custom_fields['saswp_howto_ec_schema_value'];
                    }
                    
                    if(isset($custom_fields['saswp_howto_schema_totaltime'])){
                     $input1['totalTime']     =    $custom_fields['saswp_howto_schema_totaltime'];
                    }
                    if(isset($custom_fields['saswp_howto_ec_schema_date_published'])){
                     $input1['datePublished'] =    $custom_fields['saswp_howto_ec_schema_date_published'];
                    }
                    if(isset($custom_fields['saswp_howto_ec_schema_date_modified'])){
                     $input1['dateModified'] =    $custom_fields['saswp_howto_ec_schema_date_modified'];
                    }
                    if(isset($custom_fields['saswp_howto_schema_image'])){
                     $input1['image'] =    $custom_fields['saswp_howto_schema_image'];
                    }
                                                            
                    break;     
                                  
                case 'local_business':
                   
                    if(isset($custom_fields['local_business_id'])){
                        $input1['@id'] =    $custom_fields['local_business_id'];
                    }                   
                    if(isset($custom_fields['saswp_business_type'])){                     
                     $input1['@type'] =    $custom_fields['saswp_business_type'];                     
                    }
                    if(isset($custom_fields['saswp_business_name'])){
                     $input1['@type'] =    $custom_fields['saswp_business_name'];
                    }
                    if(isset($custom_fields['local_business_name'])){
                     $input1['name'] =    $custom_fields['local_business_name'];
                    }                    
                    if(isset($custom_fields['local_business_name_url'])){
                     $input1['url'] =    saswp_validate_url($custom_fields['local_business_name_url']);
                    }
                    if(isset($custom_fields['local_business_logo'])){
                     $input1['image'] =    $custom_fields['local_business_logo'];
                    }
                    if(isset($custom_fields['local_business_description'])){
                     $input1['description'] =    wp_strip_all_tags(strip_shortcodes( $custom_fields['local_business_description'] ));
                    }
                    if(isset($custom_fields['local_street_address'])){
                     $input1['address']['streetAddress'] =    $custom_fields['local_street_address'];
                    }                    
                    if(isset($custom_fields['local_city'])){
                     $input1['address']['addressLocality'] =    $custom_fields['local_city'];
                    }
                    if(isset($custom_fields['local_state'])){
                     $input1['address']['addressRegion'] =    $custom_fields['local_state'];
                    }
                    if(isset($custom_fields['local_postal_code'])){
                     $input1['address']['postalCode'] =    $custom_fields['local_postal_code'];
                    }                    
                    if(isset($custom_fields['local_latitude']) && isset($custom_fields['local_longitude'])){
                        
                     $input1['geo']['@type']     =    'GeoCoordinates';   
                     $input1['geo']['latitude']  =    $custom_fields['local_latitude'];
                     $input1['geo']['longitude'] =    $custom_fields['local_longitude'];
                     
                    }                    
                                                               
                    if(isset($custom_fields['local_phone'])){
                     $input1['telephone'] =    $custom_fields['local_phone'];
                    }
                    if(isset($custom_fields['local_website'])){
                     $input1['website'] =    $custom_fields['local_website'];
                    }                    
                    if(isset($custom_fields['saswp_dayofweek'])){
                     $input1['openingHours'] =    $custom_fields['saswp_dayofweek'];
                    }                    
                    if(isset($custom_fields['local_area_served'])){                         
                     $input1['areaServed'] =    $custom_fields['local_area_served'];                     
                    }                    
                    if(isset($custom_fields['local_price_range'])){
                     $input1['priceRange'] =    $custom_fields['local_price_range'];
                    }
                    if(isset($custom_fields['local_hasmap'])){
                     $input1['hasMap'] =    $custom_fields['local_hasmap'];
                    }
                    if(isset($custom_fields['local_serves_cuisine'])){
                     $input1['servesCuisine'] =    $custom_fields['local_serves_cuisine'];
                    }                    
                    if(isset($custom_fields['local_menu'])){
                     $input1['hasMenu'] =    $custom_fields['local_menu'];
                    }
                    if(isset($custom_fields['local_additional_type'])){
                        $input1['additionalType'] =    $custom_fields['local_additional_type'];
                    }
                    if(isset($custom_fields['local_rating_value']) && isset($custom_fields['local_rating_count'])){
                       $input1['aggregateRating']['@type']       =   'AggregateRating';
                       $input1['aggregateRating']['worstRating'] =   0;
                       $input1['aggregateRating']['bestRating']  =   5;
                       $input1['aggregateRating']['ratingValue'] =    $custom_fields['local_rating_value'];
                       $input1['aggregateRating']['ratingCount'] =    $custom_fields['local_rating_count'];
                    }
                                     
                    break;
                
                case 'Blogposting':
                case 'BlogPosting':
                                       
                    if(isset($custom_fields['saswp_blogposting_main_entity_of_page'])){
                     $input1['mainEntityOfPage'] =    $custom_fields['saswp_blogposting_main_entity_of_page'];
                    }
                    if(isset($custom_fields['saswp_blogposting_headline'])){
                     $input1['headline'] =    $custom_fields['saswp_blogposting_headline'];
                    }
                    if(isset($custom_fields['saswp_blogposting_description'])){
                     $input1['description'] =    wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_blogposting_description'] ));
                    }
                    if(isset($custom_fields['saswp_blogposting_name'])){
                     $input1['name'] =    $custom_fields['saswp_blogposting_name'];
                    }
                    if(isset($custom_fields['saswp_blogposting_url'])){
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_blogposting_url']);
                    }
                    if(isset($custom_fields['saswp_blogposting_date_published'])){
                     $input1['datePublished'] =    $custom_fields['saswp_blogposting_date_published'];
                    }
                    if(isset($custom_fields['saswp_blogposting_date_modified'])){
                     $input1['dateModified'] =    $custom_fields['saswp_blogposting_date_modified'];
                    }
                    if(isset($custom_fields['saswp_blogposting_author_name'])){
                     $input1['author']['name'] =    $custom_fields['saswp_blogposting_author_name'];
                    }
                    if(isset($custom_fields['saswp_blogposting_author_url'])){
                     $input1['author']['url'] =    $custom_fields['saswp_blogposting_author_url'];
                    }
                    if(isset($custom_fields['saswp_blogposting_author_description'])){
                     $input1['author']['description'] =    $custom_fields['saswp_blogposting_author_description'];
                    }
                                        
                    if(isset($custom_fields['saswp_blogposting_organization_logo']) && isset($custom_fields['saswp_blogposting_organization_name'])){
                     $input1['Publisher']['@type']       =    'Organization';
                     $input1['Publisher']['name']        =    $custom_fields['saswp_blogposting_organization_name'];
                     $input1['Publisher']['logo']        =    $custom_fields['saswp_blogposting_organization_logo'];
                    }
                    
                    
                    break;
                    
                case 'AudioObject':
                    
                    if(isset($custom_fields['saswp_audio_schema_name'])){
                     $input1['name'] =    $custom_fields['saswp_audio_schema_name'];
                    }
                    if(isset($custom_fields['saswp_audio_schema_description'])){
                     $input1['description'] =    wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_audio_schema_description'] ));
                    }
                    if(isset($custom_fields['saswp_audio_schema_contenturl'])){
                     $input1['contentUrl'] =    saswp_validate_url($custom_fields['saswp_audio_schema_contenturl']);
                    }
                    if(isset($custom_fields['saswp_audio_schema_duration'])){
                     $input1['duration'] =    $custom_fields['saswp_audio_schema_duration'];
                    }
                    if(isset($custom_fields['saswp_audio_schema_encoding_format'])){
                     $input1['encodingFormat'] =    $custom_fields['saswp_audio_schema_encoding_format'];
                    }
                    
                    if(isset($custom_fields['saswp_audio_schema_date_published'])){
                     $input1['datePublished'] =    $custom_fields['saswp_audio_schema_date_published'];
                    }
                    if(isset($custom_fields['saswp_audio_schema_date_modified'])){
                     $input1['dateModified'] =    $custom_fields['saswp_audio_schema_date_modified'];
                    }
                    if(isset($custom_fields['saswp_audio_schema_author_name'])){
                     $input1['author']['name'] =    $custom_fields['saswp_audio_schema_author_name'];
                    }
                    if(isset($custom_fields['saswp_audio_schema_author_description'])){
                     $input1['author']['description'] =    $custom_fields['saswp_audio_schema_author_description'];
                    }
                    if(isset($custom_fields['saswp_audio_schema_author_url'])){
                     $input1['author']['url'] =    saswp_validate_url($custom_fields['saswp_audio_schema_author_url']);
                    }
                    
                    break;   
                    
                case 'SoftwareApplication':
                    
                    if(isset($custom_fields['saswp_software_schema_name'])){
                     $input1['name'] =    $custom_fields['saswp_software_schema_name'];
                    }
                    if(isset($custom_fields['saswp_software_schema_description'])){
                     $input1['description'] =    wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_software_schema_description'] ));
                    }
                    if(isset($custom_fields['saswp_software_schema_image'])){
                     $input1['image'] =    $custom_fields['saswp_software_schema_image'];
                    }
                    if(isset($custom_fields['saswp_software_schema_operating_system'])){
                     $input1['operatingSystem'] =    $custom_fields['saswp_software_schema_operating_system'];
                    }
                    if(isset($custom_fields['saswp_software_schema_application_category'])){
                     $input1['applicationCategory'] =    $custom_fields['saswp_software_schema_application_category'];
                    }
                    if(isset($custom_fields['saswp_software_schema_price'])){
                     $input1['offers']['price'] =    $custom_fields['saswp_software_schema_price'];
                    }
                    if(isset($custom_fields['saswp_software_schema_price_currency'])){
                     $input1['offers']['priceCurrency'] =    $custom_fields['saswp_software_schema_price_currency'];
                    }                    
                    if(isset($custom_fields['saswp_software_schema_date_published'])){
                     $input1['datePublished'] =    $custom_fields['saswp_software_schema_date_published'];
                    }
                    if(isset($custom_fields['saswp_software_schema_date_modified'])){
                     $input1['dateModified'] =    $custom_fields['saswp_software_schema_date_modified'];
                    }
                    if(isset($custom_fields['saswp_software_schema_rating']) && isset($custom_fields['saswp_software_schema_rating_count'])){
                        $input1['aggregateRating']['@type']       =   'AggregateRating';                           
                        $input1['aggregateRating']['ratingValue'] =    $custom_fields['saswp_software_schema_rating'];
                        $input1['aggregateRating']['ratingCount'] =    $custom_fields['saswp_software_schema_rating_count'];
                     }
                                                                                
                    break;    
                    
                    case 'MobileApplication':
                    
                        if(isset($custom_fields['saswp_mobile_app_schema_name'])){
                         $input1['name'] =    $custom_fields['saswp_mobile_app_schema_name'];
                        }
                        if(isset($custom_fields['saswp_mobile_app_schema_description'])){
                         $input1['description'] =    wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_mobile_app_schema_description'] ));
                        }
                        if(isset($custom_fields['saswp_mobile_app_schema_image'])){
                         $input1['image'] =    $custom_fields['saswp_mobile_app_schema_image'];
                        }
                        if(isset($custom_fields['saswp_mobile_app_schema_operating_system'])){
                         $input1['operatingSystem'] =    $custom_fields['saswp_mobile_app_schema_operating_system'];
                        }
                        if(isset($custom_fields['saswp_mobile_app_schema_application_category'])){
                         $input1['applicationCategory'] =    $custom_fields['saswp_mobile_app_schema_application_category'];
                        }
                        if(isset($custom_fields['saswp_mobile_app_schema_price'])){
                         $input1['offers']['price'] =    $custom_fields['saswp_mobile_app_schema_price'];
                        }
                        if(isset($custom_fields['saswp_mobile_app_schema_price_currency'])){
                         $input1['offers']['priceCurrency'] =    $custom_fields['saswp_mobile_app_schema_price_currency'];
                        }                    
                        if(isset($custom_fields['saswp_mobile_app_schema_date_published'])){
                         $input1['datePublished'] =    $custom_fields['saswp_mobile_app_schema_date_published'];
                        }
                        if(isset($custom_fields['saswp_mobile_app_schema_date_modified'])){
                         $input1['dateModified'] =    $custom_fields['saswp_mobile_app_schema_date_modified'];
                        }
                        if(isset($custom_fields['saswp_mobile_app_schema_rating_value']) && isset($custom_fields['saswp_mobile_app_schema_rating_count'])){
                           $input1['aggregateRating']['@type']       =   'AggregateRating';                           
                           $input1['aggregateRating']['ratingValue'] =    $custom_fields['saswp_mobile_app_schema_rating_value'];
                           $input1['aggregateRating']['ratingCount'] =    $custom_fields['saswp_mobile_app_schema_rating_count'];
                        }
                                                                                    
                        break;       
                
                case 'NewsArticle':
                                                                  
                    if(isset($custom_fields['saswp_newsarticle_main_entity_of_page'])){
                     $input1['mainEntityOfPage'] =    $custom_fields['saswp_newsarticle_main_entity_of_page'];
                    }
                    if(isset($custom_fields['saswp_newsarticle_URL'])){
                       $input1['url'] =    saswp_validate_url($custom_fields['saswp_newsarticle_URL']); 
                    }
                    if(isset($custom_fields['saswp_newsarticle_headline'])){
                       $input1['headline'] =    $custom_fields['saswp_newsarticle_headline']; 
                    }
                    if(isset($custom_fields['saswp_newsarticle_keywords'])){
                       $input1['keywords'] =    $custom_fields['saswp_newsarticle_keywords']; 
                    }
                    if(isset($custom_fields['saswp_newsarticle_date_published'])){
                       $input1['datePublished'] =    $custom_fields['saswp_newsarticle_date_published']; 
                    }
                    if(isset($custom_fields['saswp_newsarticle_date_modified'])){
                       $input1['dateModified'] =    $custom_fields['saswp_newsarticle_date_modified']; 
                    }
                    if(isset($custom_fields['saswp_newsarticle_description'])){
                       $input1['description'] =    wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_newsarticle_description'] ));  
                    }
                    if(isset($custom_fields['saswp_newsarticle_section'])){
                       $input1['articleSection'] = $custom_fields['saswp_newsarticle_section'];  
                    }
                    if(isset($custom_fields['saswp_newsarticle_body'])){
                       $input1['articleBody'] =    $custom_fields['saswp_newsarticle_body'];  
                    }
                    if(isset($custom_fields['saswp_newsarticle_name'])){
                       $input1['name'] =    $custom_fields['saswp_newsarticle_name'];  
                    }
                    if(isset($custom_fields['saswp_newsarticle_thumbnailurl'])){
                       $input1['thumbnailUrl'] =    $custom_fields['saswp_newsarticle_thumbnailurl'];  
                    }
                    if(isset($custom_fields['saswp_newsarticle_timerequired'])){
                       $input1['timeRequired'] =    $custom_fields['saswp_newsarticle_timerequired'];  
                    }
                    if(isset($custom_fields['saswp_newsarticle_main_entity_id'])){
                       $input1['mainEntity']['@id'] =    $custom_fields['saswp_newsarticle_main_entity_id'];  
                    }
                    if(isset($custom_fields['saswp_newsarticle_author_name'])){
                        $input1['author']['name'] =    $custom_fields['saswp_newsarticle_author_name']; 
                    }
                    if(isset($custom_fields['saswp_newsarticle_author_url'])){
                        $input1['author']['url'] =    saswp_validate_url($custom_fields['saswp_newsarticle_author_url']); 
                    }
                    if(isset($custom_fields['saswp_newsarticle_author_image'])){
                       $input1['author']['Image']['url'] =    $custom_fields['saswp_newsarticle_author_image'];  
                    }                    
                    if(isset($custom_fields['saswp_newsarticle_organization_logo']) && isset($custom_fields['saswp_newsarticle_organization_name'])){
                     $input1['Publisher']['@type']       =    'Organization';
                     $input1['Publisher']['name']        =    $custom_fields['saswp_newsarticle_organization_name'];
                     $input1['Publisher']['logo']        =    $custom_fields['saswp_newsarticle_organization_logo'];
                    }
                                        
                    break;
                
                case 'WebPage':
                    
                    if(isset($custom_fields['saswp_webpage_name'])){
                        $input1['name'] =    $custom_fields['saswp_webpage_name'];
                    }
                    if(isset($custom_fields['saswp_webpage_url'])){
                        $input1['url'] =    saswp_validate_url($custom_fields['saswp_webpage_url']);
                    }
                    if(isset($custom_fields['saswp_webpage_description'])){
                        $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_webpage_description'] )) ;
                    }

                    if(isset($custom_fields['saswp_webpage_reviewed_by'])){
                        $input1['reviewedBy'] =    $custom_fields['saswp_webpage_reviewed_by'];
                    }

                    if(isset($custom_fields['saswp_webpage_last_reviewed'])){
                        $input1['lastReviewed'] =    $custom_fields['saswp_webpage_last_reviewed'];
                    }
                    
                    if(isset($custom_fields['saswp_webpage_main_entity_of_page'])){
                     $input1['mainEntity']['mainEntityOfPage'] =    saswp_validate_url($custom_fields['saswp_webpage_main_entity_of_page']);
                    }
                    if(isset($custom_fields['saswp_webpage_image'])){
                     $input1['mainEntity']['image'] =    $custom_fields['saswp_webpage_image'];
                    }
                    if(isset($custom_fields['saswp_webpage_headline'])){
                     $input1['mainEntity']['headline'] =    $custom_fields['saswp_webpage_headline'];
                    }
                    
                    if(isset($custom_fields['saswp_webpage_date_published'])){
                     $input1['mainEntity']['datePublished'] =    $custom_fields['saswp_webpage_date_published'];
                    }
                    if(isset($custom_fields['saswp_webpage_date_modified'])){
                     $input1['mainEntity']['dateModified'] =    $custom_fields['saswp_webpage_date_modified'];
                    }
                    if(isset($custom_fields['saswp_webpage_author_name'])){
                     $input1['mainEntity']['author']['name'] =    $custom_fields['saswp_webpage_author_name'];
                    }
                    if(isset($custom_fields['saswp_webpage_author_url'])){
                     $input1['mainEntity']['author']['url'] =    $custom_fields['saswp_webpage_author_url'];
                    }
                    
                    if(isset($custom_fields['saswp_webpage_organization_logo']) && isset($custom_fields['saswp_webpage_organization_name'])){
                        $input1['mainEntity']['publisher']['@type']              =    'Organization';
                        $input1['mainEntity']['publisher']['logo'] =    $custom_fields['saswp_webpage_organization_logo'];
                        $input1['mainEntity']['publisher']['name'] =    $custom_fields['saswp_webpage_organization_name'];
                    }
                    
                    break;
                                                    
                case 'Event':      
                    
                    $phy_location = array();
                    $vir_location = array();
                    
                    if(isset($custom_fields['saswp_event_schema_name'])){
                     $input1['name'] =    $custom_fields['saswp_event_schema_name'];
                    }
                    if(isset($custom_fields['saswp_event_schema_description'])){
                     $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_event_schema_description'] )) ;
                    }
                                       
                    if(isset($custom_fields['saswp_event_schema_location_name']) || isset($custom_fields['saswp_event_schema_location_streetaddress'])){
                        
                        $phy_location['@type'] = 'Place';   
                        $phy_location['name']  =    $custom_fields['saswp_event_schema_location_name'];

                            if(isset($custom_fields['saswp_event_schema_location_streetaddress'])){
                                $phy_location['address']['streetAddress'] =    $custom_fields['saswp_event_schema_location_streetaddress'];
                            }                                          
                            if(isset($custom_fields['saswp_event_schema_location_locality'])){
                                $phy_location['address']['addressLocality'] =    $custom_fields['saswp_event_schema_location_locality'];
                            }
                            if(isset($custom_fields['saswp_event_schema_location_region'])){
                                $phy_location['address']['addressRegion'] =    $custom_fields['saswp_event_schema_location_region'];
                            }                    
                            if(isset($custom_fields['saswp_event_schema_location_postalcode'])){
                                $phy_location['address']['postalCode'] =    $custom_fields['saswp_event_schema_location_postalcode'];
                            }
                            if(isset($custom_fields['saswp_event_schema_location_country'])){
                                $phy_location['address']['addressCountry'] =    $custom_fields['saswp_event_schema_location_country'];
                            }
                            if(isset($custom_fields['saswp_event_schema_location_hasmap'])){
                             $phy_location['hasMap']  =  $custom_fields['saswp_event_schema_location_hasmap'];
                            }
                    }
                    if(isset($custom_fields['saswp_event_schema_virtual_location_name']) || isset($custom_fields['saswp_event_schema_virtual_location_url'])){
                            $vir_location['@type'] = 'VirtualLocation';
                            $reviews_arr['name']   = $custom_fields['saswp_event_schema_virtual_location_name'];
                            $vir_location['url']   = $custom_fields['saswp_event_schema_virtual_location_url'];
                    }                                        
                    if($vir_location || $phy_location){
                        $input1['location'] = array($vir_location, $phy_location);
                    }                    

                    if(isset($custom_fields['saswp_event_schema_status'])){
                        $input1['eventStatus'] = $custom_fields['saswp_event_schema_status'];
                    }
                    if( isset($custom_fields['saswp_event_schema_attendance_mode']) ){
                        $input1['eventAttendanceMode'] = $custom_fields['saswp_event_schema_attendance_mode'];
                    }

                    if(isset($custom_fields['saswp_event_schema_previous_start_date'])){
                     
                        $time = '';
                        
                        if(isset($custom_fields['saswp_event_schema_previous_start_time'])){
                            
                           $time =  $custom_fields['saswp_event_schema_previous_start_time'];
                           
                        }
                        
                        $input1['previousStartDate'] =    saswp_format_date_time($custom_fields['saswp_event_schema_previous_start_date'], $time);
                        
                    }

                    if(isset($custom_fields['saswp_event_schema_start_date'])){
                     
                     $time = '';
                     
                     if(isset($custom_fields['saswp_event_schema_start_time'])){
                         
                        $time =  $custom_fields['saswp_event_schema_start_time'];
                        
                     }
                     
                     $input1['startDate'] =    saswp_format_date_time($custom_fields['saswp_event_schema_start_date'], $time);
                     
                    }
                    
                    if(isset($custom_fields['saswp_event_schema_end_date'])){
                     
                     $time = '';
                     
                     if(isset($custom_fields['saswp_event_schema_end_time'])){
                         
                        $time =  $custom_fields['saswp_event_schema_end_time'];
                        
                     }
                     
                     $input1['endDate'] =    saswp_format_date_time($custom_fields['saswp_event_schema_end_date'], $time);
                     
                    }
                    
                    if(isset($custom_fields['saswp_event_schema_image'])){
                     $input1['image'] =    $custom_fields['saswp_event_schema_image'];
                    }
                    if(isset($custom_fields['saswp_event_schema_performer_name'])){
                     $input1['performer']['name'] =    $custom_fields['saswp_event_schema_performer_name'];
                    }
                    if(isset($custom_fields['saswp_event_schema_price'])){
                     $input1['offers']['price'] =    $custom_fields['saswp_event_schema_price'];
                    }
                    if(isset($custom_fields['saswp_event_schema_price_currency'])){
                     $input1['offers']['priceCurrency'] =    $custom_fields['saswp_event_schema_price_currency'];
                    }
                    if(isset($custom_fields['saswp_event_schema_availability'])){
                     $input1['offers']['availability'] =    $custom_fields['saswp_event_schema_availability'];
                    }
                    if(isset($custom_fields['saswp_event_schema_validfrom'])){
                     $input1['offers']['validFrom'] =    $custom_fields['saswp_event_schema_validfrom'];
                    }
                    if(isset($custom_fields['saswp_event_schema_url'])){
                     $input1['offers']['url'] =    $custom_fields['saswp_event_schema_url'];
                    }

                    if(isset($custom_fields['saswp_event_schema_organizer_name']) || isset($custom_fields['saswp_event_schema_organizer_url']) || isset($custom_fields['saswp_event_schema_organizer_email']) || isset($custom_fields['saswp_event_schema_organizer_phone'])){
                        
                        $input1['organizer']['@type'] =    'Organization';

                        if(isset($custom_fields['saswp_event_schema_organizer_name'])){
                            $input1['organizer']['name']  =    $custom_fields['saswp_event_schema_organizer_name'];
                        }
                        if(isset($custom_fields['saswp_event_schema_organizer_url'])){
                            $input1['organizer']['url']  =    $custom_fields['saswp_event_schema_organizer_url'];
                        }
                        if(isset($custom_fields['saswp_event_schema_organizer_email'])){
                            $input1['organizer']['email']  =    $custom_fields['saswp_event_schema_organizer_email'];
                        }
                        if(isset($custom_fields['saswp_event_schema_organizer_phone'])){
                            $input1['organizer']['telephone']  =    $custom_fields['saswp_event_schema_organizer_phone'];
                        }                        
                    }
                                        
                    break;    
                    
                case 'TechArticle':      
                      
                    if(isset($custom_fields['saswp_tech_article_main_entity_of_page'])){
                     $input1['mainEntityOfPage'] =    $custom_fields['saswp_tech_article_main_entity_of_page'];
                    }
                    if(isset($custom_fields['saswp_tech_article_image'])){
                     $input1['image'] =    $custom_fields['saswp_tech_article_image'];
                    }
                    if(isset($custom_fields['saswp_tech_article_url'])){
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_tech_article_url']);
                    }
                    if(isset($custom_fields['saswp_tech_article_body'])){
                     $input1['articleBody'] =    $custom_fields['saswp_tech_article_body'];
                    }
                    if(isset($custom_fields['saswp_tech_article_keywords'])){
                     $input1['keywords'] =    $custom_fields['saswp_tech_article_keywords'];
                    }
                    if(isset($custom_fields['saswp_tech_article_section'])){
                     $input1['articleSection'] =    $custom_fields['saswp_tech_article_section'];
                    }
                    if(isset($custom_fields['saswp_tech_article_headline'])){
                     $input1['headline'] =    $custom_fields['saswp_tech_article_headline'];
                    }
                    
                    if(isset($custom_fields['saswp_tech_article_description'])){
                     $input1['description'] =    wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_tech_article_description'] ));
                    }
                    if(isset($custom_fields['saswp_tech_article_date_published'])){
                     $input1['datePublished'] =    $custom_fields['saswp_tech_article_date_published'];
                    }
                    if(isset($custom_fields['saswp_tech_article_date_modified'])){
                     $input1['dateModified'] =    $custom_fields['saswp_tech_article_date_modified'];
                    }
                    
                    if(isset($custom_fields['saswp_tech_article_author_name'])){
                     $input1['author']['name'] =    $custom_fields['saswp_tech_article_author_name'];
                    }
                    if(isset($custom_fields['saswp_tech_article_author_url'])){
                     $input1['author']['url'] =    saswp_validate_url($custom_fields['saswp_tech_article_author_url']);
                    }
                    if(isset($custom_fields['saswp_tech_article_author_description'])){
                     $input1['author']['description'] =    $custom_fields['saswp_tech_article_author_description'];
                    }
                     
                    if(isset($custom_fields['saswp_tech_article_organization_logo']) && isset($custom_fields['saswp_tech_article_organization_name'])){
                     $input1['Publisher']['@type']       =    'Organization';
                     $input1['Publisher']['name']        =    $custom_fields['saswp_tech_article_organization_name'];
                     $input1['Publisher']['logo']        =    $custom_fields['saswp_tech_article_organization_logo'];
                    }
                    break;   
                    
                case 'Course':      
                      
                    if(isset($custom_fields['saswp_course_name'])){
                     $input1['name'] =    $custom_fields['saswp_course_name'];
                    }
                    if(isset($custom_fields['saswp_course_description'])){
                     $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_course_description'] )) ;
                    }
                    if(isset($custom_fields['saswp_course_url'])){
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_course_url']);
                    }                    
                    if(isset($custom_fields['saswp_course_date_published'])){
                     $input1['datePublished'] =    $custom_fields['saswp_course_date_published'];
                    }
                    if(isset($custom_fields['saswp_course_date_modified'])){
                     $input1['dateModified'] =    $custom_fields['saswp_course_date_modified'];
                    }

                    if(isset($custom_fields['saswp_course_duration'])){
                        $input1['timeRequired'] =    $custom_fields['saswp_course_duration'];
                    }

                    if(isset($custom_fields['saswp_course_code'])){
                        $input1['courseCode'] =    $custom_fields['saswp_course_code'];
                    }

                    if(isset($custom_fields['saswp_course_provider_name'])){
                     $input1['provider']['name'] =    $custom_fields['saswp_course_provider_name'];
                    }
                    
                    if(isset($custom_fields['saswp_course_sameas'])){
                     $input1['provider']['sameAs'] =    $custom_fields['saswp_course_sameas'];
                    }

                    if(isset($custom_fields['saswp_course_content_location_name']) || isset($custom_fields['saswp_course_content_location_locality']) || isset($custom_fields['saswp_course_content_location_country'])){

                        $input1['contentLocation']['@type']                        =   'Place';
                        $input1['contentLocation']['name']                         =   $custom_fields['saswp_course_content_location_name'];
                        $input1['contentLocation']['address']['addressLocality']   =   $custom_fields['saswp_course_content_location_locality'];
                        $input1['contentLocation']['address']['addressRegion']     =   $custom_fields['saswp_course_content_location_region'];
                        $input1['contentLocation']['address']['PostalCode']        =   $custom_fields['saswp_course_content_location_postal_code'];
                        $input1['contentLocation']['address']['addressCountry']    =   $custom_fields['saswp_course_content_location_country'];

                    }
                    
                    if(isset($custom_fields['saswp_course_rating']) && isset($custom_fields['saswp_course_review_count'])){
                        $input1['aggregateRating']['@type']       =   'AggregateRating';                                                
                        $input1['aggregateRating']['ratingValue'] =    $custom_fields['saswp_course_rating'];
                        $input1['aggregateRating']['ratingCount'] =    $custom_fields['saswp_course_review_count'];
                     }
                    break;    
                    
                case 'DiscussionForumPosting':      
                      
                    if(isset($custom_fields['saswp_dfp_headline'])){
                     $input1['headline'] =    $custom_fields['saswp_dfp_headline'];
                    }
                    if(isset($custom_fields['saswp_dfp_description'])){
                     $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_dfp_description'] )) ;
                    }
                    if(isset($custom_fields['saswp_dfp_url'])){
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_dfp_url']);
                    }                    
                    if(isset($custom_fields['saswp_dfp_date_published'])){
                     $input1['datePublished'] =    $custom_fields['saswp_dfp_date_published'];
                    }
                    if(isset($custom_fields['saswp_dfp_date_modified'])){
                     $input1['dateModified'] =    $custom_fields['saswp_dfp_date_modified'];
                    }
                    if(isset($custom_fields['saswp_dfp_author_name'])){
                     $input1['author']['name'] =    $custom_fields['saswp_dfp_author_name'];
                    }
                    if(isset($custom_fields['saswp_dfp_author_url'])){
                     $input1['author']['url'] =    $custom_fields['saswp_dfp_author_url'];
                    }
                    if(isset($custom_fields['saswp_dfp_author_description'])){
                     $input1['author']['description'] =    $custom_fields['saswp_dfp_author_description'];
                    }
                    
                    if(isset($custom_fields['saswp_dfp_main_entity_of_page'])){
                     $input1['mainEntityOfPage'] =    $custom_fields['saswp_dfp_main_entity_of_page'];
                    }
                    
                    if(isset($custom_fields['saswp_dfp_organization_logo']) && isset($custom_fields['saswp_dfp_organization_name'])){
                     $input1['Publisher']['@type']       =    'Organization';
                     $input1['Publisher']['name']        =    $custom_fields['saswp_dfp_organization_name'];
                     $input1['Publisher']['logo']        =    $custom_fields['saswp_dfp_organization_logo'];
                    }                                                            
                    break;        
                
                case 'Recipe':
                    if(isset($custom_fields['saswp_recipe_url'])){
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_recipe_url']);
                    }
                    if(isset($custom_fields['saswp_recipe_name'])){
                     $input1['name'] =    $custom_fields['saswp_recipe_name'];
                    }
                    if(isset($custom_fields['saswp_recipe_date_published'])){
                     $input1['datePublished'] =    $custom_fields['saswp_recipe_date_published'];
                    }
                    
                    if(isset($custom_fields['saswp_recipe_date_modified'])){
                     $input1['dateModified'] =    $custom_fields['saswp_recipe_date_modified'];
                    }
                    if(isset($custom_fields['saswp_recipe_description'])){
                     $input1['description'] =  wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_recipe_description'] ))  ;
                    }
                    if(isset($custom_fields['saswp_recipe_main_entity'])){
                     $input1['mainEntity']['@id'] =    $custom_fields['saswp_recipe_main_entity'];
                    }
                    
                    if(isset($custom_fields['saswp_recipe_author_name'])){
                     $input1['author']['name'] =    $custom_fields['saswp_recipe_author_name'];
                    }
                    if(isset($custom_fields['saswp_recipe_author_url'])){
                     $input1['author']['url'] =    $custom_fields['saswp_recipe_author_url'];
                    }
                    if(isset($custom_fields['saswp_recipe_author_description'])){
                     $input1['author']['description'] =    $custom_fields['saswp_recipe_author_description'];
                    }
                    if(isset($custom_fields['saswp_recipe_author_image'])){
                     $input1['author']['Image']['url'] =    $custom_fields['saswp_recipe_author_image'];
                    }

                    if(isset($custom_fields['saswp_recipe_organization_name']) && isset($custom_fields['saswp_recipe_organization_logo'])){
                        $input1['mainEntity']['publisher']['@type']       =    'Organization';
                        $input1['mainEntity']['publisher']['logo']        =    $custom_fields['saswp_recipe_organization_logo'];
                        $input1['mainEntity']['publisher']['name']        =    $custom_fields['saswp_recipe_organization_name'];
                    }

                    if(isset($custom_fields['saswp_recipe_preptime'])){
                     $input1['prepTime'] =    $custom_fields['saswp_recipe_preptime'];
                    }
                    if(isset($custom_fields['saswp_recipe_cooktime'])){
                     $input1['cookTime'] =    $custom_fields['saswp_recipe_cooktime'];
                    }
                    
                    if(isset($custom_fields['saswp_recipe_totaltime'])){
                     $input1['totalTime'] =    $custom_fields['saswp_recipe_totaltime'];
                    }
                    if(isset($custom_fields['saswp_recipe_keywords'])){
                     $input1['keywords'] =    $custom_fields['saswp_recipe_keywords'];
                    }
                    if(isset($custom_fields['saswp_recipe_recipeyield'])){
                     $input1['recipeYield'] =    $custom_fields['saswp_recipe_recipeyield'];
                    }
                    
                    if(isset($custom_fields['saswp_recipe_category'])){
                     $input1['recipeCategory'] =    $custom_fields['saswp_recipe_category'];
                    }
                    if(isset($custom_fields['saswp_recipe_cuisine'])){
                     $input1['recipeCuisine'] =    $custom_fields['saswp_recipe_cuisine'];
                    }
                    if(isset($custom_fields['saswp_recipe_nutrition'])){
                     $input1['nutrition']['calories'] =    $custom_fields['saswp_recipe_nutrition'];
                    }
                    
                    if(isset($custom_fields['saswp_recipe_ingredient'])){  
                        
                      if(is_array($custom_fields['saswp_recipe_ingredient'])){                          
                          $input1['recipeIngredient'] =   $custom_fields['saswp_recipe_ingredient'];                          
                      }else{                        
                          $input1['recipeIngredient'] =    saswp_explod_by_semicolon($custom_fields['saswp_recipe_ingredient']);                          
                      }
                                             
                    }
                    if(isset($custom_fields['saswp_recipe_instructions'])){  
                        if(is_array($custom_fields['saswp_recipe_instructions'])){
                            $input1['recipeInstructions'] =    $custom_fields['saswp_recipe_instructions'];   
                        }else{
                            $input1['recipeInstructions'] =    saswp_explod_by_semicolon($custom_fields['saswp_recipe_instructions']);
                        }                     
                    }
                    if(isset($custom_fields['saswp_recipe_video_name'])){
                     $input1['video']['name'] =    $custom_fields['saswp_recipe_video_name'];
                    }
                    
                    if(isset($custom_fields['saswp_recipe_video_description'])){
                     $input1['video']['description'] =    $custom_fields['saswp_recipe_video_description'];
                    }
                    if(isset($custom_fields['saswp_recipe_video_thumbnailurl'])){
                     $input1['video']['thumbnailUrl'] =    $custom_fields['saswp_recipe_video_thumbnailurl'];
                    }
                    if(isset($custom_fields['saswp_recipe_video_contenturl'])){
                     $input1['video']['contentUrl'] =    $custom_fields['saswp_recipe_video_contenturl'];
                    }                    
                    if(isset($custom_fields['saswp_recipe_video_embedurl'])){
                     $input1['video']['embedUrl'] =    $custom_fields['saswp_recipe_video_embedurl'];
                    }
                    if(isset($custom_fields['saswp_recipe_video_upload_date'])){
                     $input1['video']['uploadDate'] =    $custom_fields['saswp_recipe_video_upload_date'];
                    }
                    if(isset($custom_fields['saswp_recipe_video_duration'])){
                     $input1['video']['duration'] =    $custom_fields['saswp_recipe_video_duration'];
                    } 
                    
                    if(isset($custom_fields['saswp_recipe_rating_value']) && isset($custom_fields['saswp_recipe_rating_count'])){
                       $input1['aggregateRating']['@type']       =   'AggregateRating';
                       $input1['aggregateRating']['worstRating'] =   0;
                       $input1['aggregateRating']['bestRating']  =   5;
                       $input1['aggregateRating']['ratingValue'] =    $custom_fields['saswp_recipe_rating_value'];
                       $input1['aggregateRating']['ratingCount'] =    $custom_fields['saswp_recipe_rating_count'];
                    }
                    
                    break;
                
                case 'Product':                                                                                                  
                    if(isset($custom_fields['saswp_product_schema_url'])){
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_product_schema_url']);
                    }
                    if(isset($custom_fields['saswp_product_schema_name'])){
                     $input1['name'] =    $custom_fields['saswp_product_schema_name'];
                    }
                    
                    if(isset($custom_fields['saswp_product_schema_brand_name'])){
                     $input1['brand']['name'] =    $custom_fields['saswp_product_schema_brand_name'];
                    }
                    
                    if(isset($custom_fields['saswp_product_schema_mpn'])){
                     $input1['mpn'] =    $custom_fields['saswp_product_schema_mpn'];
                    }
                    if(isset($custom_fields['saswp_product_schema_gtin8'])){
                     $input1['gtin8'] =    $custom_fields['saswp_product_schema_gtin8'];
                    }
                    if(isset($custom_fields['saswp_product_schema_gtin13'])){
                        $input1['gtin13'] =    $custom_fields['saswp_product_schema_gtin13'];
                    }
                    if(isset($custom_fields['saswp_product_additional_type'])){
                        $input1['additionalType'] =    $custom_fields['saswp_product_additional_type'];
                    }                                        
                    if(isset($custom_fields['saswp_product_schema_description'])){
                     $input1['description'] =  wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_product_schema_description'] ));
                    }                    
                    if(isset($custom_fields['saswp_product_schema_image'])){
                     $input1['image'] =    $custom_fields['saswp_product_schema_image'];
                    }
                    if(isset($custom_fields['saswp_product_schema_availability'])){
                     $input1['offers']['availability'] =    $custom_fields['saswp_product_schema_availability'];
                     if(isset($custom_fields['saswp_product_schema_url'])){
                         $input1['offers']['url']   =    $custom_fields['saswp_product_schema_url'];
                     }
                    }
                    if(isset($custom_fields['saswp_product_schema_price'])){
                     $input1['offers']['price'] =    $custom_fields['saswp_product_schema_price'];
                     
                     if(isset($custom_fields['saswp_product_schema_url'])){
                         $input1['offers']['url']   =    $custom_fields['saswp_product_schema_url'];
                     }
                                          
                    }
                    if(isset($custom_fields['saswp_product_schema_currency'])){
                     $input1['offers']['priceCurrency'] =    $custom_fields['saswp_product_schema_currency'];
                     $input1['offers']['url'] =    $custom_fields['saswp_product_schema_url'];
                    }
                    if(isset($custom_fields['saswp_product_schema_vat'])){
                        $input1['offers']['priceSpecification']['@type']                 =    'priceSpecification';
                        $input1['offers']['priceSpecification']['valueAddedTaxIncluded'] =    $custom_fields['saswp_product_schema_vat'];
                    }
                    if(isset($custom_fields['saswp_product_schema_priceValidUntil'])){
                     $input1['offers']['priceValidUntil'] =    $custom_fields['saswp_product_schema_priceValidUntil'];
                     
                    }                   
                    if(isset($custom_fields['saswp_product_schema_condition'])){
                     $input1['offers']['itemCondition'] =    $custom_fields['saswp_product_schema_condition'];
                    }
                    if(isset($custom_fields['saswp_product_schema_sku'])){
                     $input1['sku']                    =    $custom_fields['saswp_product_schema_sku'];
                    }
                    if(isset($custom_fields['saswp_product_schema_seller'])){
                     $input1['offers']['seller']['@type']         =    'Organization';
                     $input1['offers']['seller']['name']          =    $custom_fields['saswp_product_schema_seller'];
                    }
                    
                    if(isset($custom_fields['saswp_product_schema_rating']) && isset($custom_fields['saswp_product_schema_review_count'])){
                     $input1['aggregateRating']['@type']       = 'aggregateRating';
                     $input1['aggregateRating']['ratingValue'] = $custom_fields['saswp_product_schema_rating'];
                     $input1['aggregateRating']['reviewCount'] = $custom_fields['saswp_product_schema_review_count'];
                    }
                                                            
                    break;

                    case 'RealEstateListing':                                                                                                  
                        if(isset($custom_fields['saswp_real_estate_listing_date_posted'])){
                            $input1['datePosted'] =    $custom_fields['saswp_real_estate_listing_date_posted'];
                        }
                        if(isset($custom_fields['saswp_real_estate_listing_name'])){
                         $input1['name'] =    $custom_fields['saswp_real_estate_listing_name'];
                        }
                        if(isset($custom_fields['saswp_real_estate_listing_url'])){
                         $input1['url'] =    saswp_validate_url($custom_fields['saswp_real_estate_listing_url']);
                        }                                                
                        if(isset($custom_fields['saswp_real_estate_listing_description'])){
                         $input1['description'] =    $custom_fields['saswp_real_estate_listing_description'];
                        }
                        if(isset($custom_fields['saswp_real_estate_listing_image'])){
                         $input1['image'] =    $custom_fields['saswp_real_estate_listing_image'];
                        }                        
                        if(isset($custom_fields['saswp_real_estate_listing_availability'])){
                         $input1['offers']['availability'] =    $custom_fields['saswp_real_estate_listing_availability'];                         
                        }
                        if(isset($custom_fields['saswp_real_estate_listing_price'])){
                         $input1['offers']['price'] =    $custom_fields['saswp_real_estate_listing_price'];                                                                                                
                        }
                        if(isset($custom_fields['saswp_real_estate_listing_currency'])){
                         $input1['offers']['priceCurrency'] =    $custom_fields['saswp_real_estate_listing_currency'];                         
                        }
                        if(isset($custom_fields['saswp_real_estate_listing_validfrom'])){
                         $input1['offers']['validfrom'] =    $custom_fields['saswp_real_estate_listing_validfrom'];                         
                        }                                                                                          
                        
                        $location = array();
                        
                        if(isset($custom_fields['saswp_real_estate_listing_location_name'])){

                            $location = array(
                                '@type' => 'Place',
                                'name' => $custom_fields['saswp_real_estate_listing_location_name'],                                                               
                                'telephone' => $custom_fields['saswp_real_estate_listing_location_name'],                                
                                'address' => array(
                                            '@type' => 'PostalAddress',
                                            'streetAddress'   => $custom_fields['saswp_real_estate_listing_streetaddress'],
                                            'addressLocality' => $custom_fields['saswp_real_estate_listing_locality'],
                                            'addressRegion'   => $custom_fields['saswp_real_estate_listing_region'],
                                            'addressCountry'   => $custom_fields['saswp_real_estate_listing_country'],
                                            'postalCode'      => $custom_fields['saswp_real_estate_listing_postalcode'],  
                                ),
                            );

                            $input1['contentLocation'] = $location;
                        }

                        break;    

                        case 'PsychologicalTreatment':                                                                                                  

                            if(isset($custom_fields['saswp_psychological_treatment_name'])){
                                $input1['name'] =    $custom_fields['saswp_psychological_treatment_name'];
                            }                            
                            if(isset($custom_fields['saswp_psychological_treatment_url'])){
                             $input1['url'] =    saswp_validate_url($custom_fields['saswp_psychological_treatment_url']);
                            }                                                
                            if(isset($custom_fields['saswp_psychological_treatment_description'])){
                             $input1['description'] =    $custom_fields['saswp_psychological_treatment_description'];
                            }
                            if(isset($custom_fields['saswp_psychological_treatment_image'])){
                             $input1['image'] =    $custom_fields['saswp_psychological_treatment_image'];
                            }                            
                            if(isset($custom_fields['saswp_psychological_treatment_drug'])){
                                $input1['drug'] =    $custom_fields['saswp_psychological_treatment_drug'];
                            }
                            if(isset($custom_fields['saswp_psychological_treatment_body_location'])){
                                $input1['bodyLocation'] =    $custom_fields['saswp_psychological_treatment_body_location'];
                            }
                            if(isset($custom_fields['saswp_psychological_treatment_preparation'])){
                                $input1['preparation'] =    $custom_fields['saswp_psychological_treatment_preparation'];
                            }
                            if(isset($custom_fields['saswp_psychological_treatment_followup'])){
                                $input1['followup'] =    $custom_fields['saswp_psychological_treatment_followup'];
                            }
                            if(isset($custom_fields['saswp_psychological_treatment_how_performed'])){
                                $input1['howPerformed'] =    $custom_fields['saswp_psychological_treatment_how_performed'];
                            }
                            if(isset($custom_fields['saswp_psychological_treatment_procedure_type'])){
                                $input1['procedureType'] =    $custom_fields['saswp_psychological_treatment_procedure_type'];
                            }
                            if(isset($custom_fields['saswp_psychological_treatment_medical_code'])){
                                $input1['code'] =    $custom_fields['saswp_psychological_treatment_medical_code'];
                            }
                            if(isset($custom_fields['saswp_psychological_treatment_additional_type'])){
                                $input1['additionalType'] =    $custom_fields['saswp_psychological_treatment_additional_type'];
                            }
    
                            break;            
                
                case 'Service':
                    if(isset($custom_fields['saswp_service_schema_name'])){
                      $input1['name'] =    $custom_fields['saswp_service_schema_name'];
                    }
                    if(isset($custom_fields['saswp_service_schema_type'])){
                      $input1['serviceType'] =    $custom_fields['saswp_service_schema_type'];
                    }
                    if(isset($custom_fields['saswp_service_schema_provider_type']) && isset($custom_fields['saswp_service_schema_provider_name'])){
                      $input1['provider']['@type'] =    $custom_fields['saswp_service_schema_provider_type'];
                      $input1['provider']['name']  =    $custom_fields['saswp_service_schema_provider_name'];
                    }                                        
                    if(isset($custom_fields['saswp_service_schema_image'])){
                      $input1['provider']['image'] =    $custom_fields['saswp_service_schema_image'];
                    }
                    if(isset($custom_fields['saswp_service_schema_locality'])){
                     $input1['provider']['address']['addressLocality'] =    $custom_fields['saswp_service_schema_locality'];
                    }
                    if(isset($custom_fields['saswp_service_schema_postal_code'])){
                      $input1['provider']['address']['postalCode'] =    $custom_fields['saswp_service_schema_postal_code'];
                    }
                    if(isset($custom_fields['saswp_service_schema_telephone'])){
                      $input1['provider']['address']['telephone'] =    $custom_fields['saswp_service_schema_telephone'];
                    }                    
                    if(isset($custom_fields['saswp_service_schema_description'])){
                      $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_service_schema_description'] ));
                    }
                    if(isset($custom_fields['saswp_service_schema_area_served'])){
                      $input1['areaServed'] =    $custom_fields['saswp_service_schema_area_served'];
                    }
                    if(isset($custom_fields['saswp_service_schema_service_offer'])){
                      $input1['hasOfferCatalog'] =    $custom_fields['saswp_service_schema_service_offer'];
                    }
                                                                             
                    break;
                
                case 'VideoObject':
                    
                    if(isset($custom_fields['saswp_video_object_url'])){
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_video_object_url']);
                    }
                    if(isset($custom_fields['saswp_video_object_headline'])){
                     $input1['headline'] =    $custom_fields['saswp_video_object_headline'];
                    }
                    if(isset($custom_fields['saswp_video_object_date_published'])){
                     $input1['datePublished'] =    $custom_fields['saswp_video_object_date_published'];
                    }                    
                    if(isset($custom_fields['saswp_video_object_date_modified'])){
                     $input1['dateModified'] =    $custom_fields['saswp_video_object_date_modified'];
                    }
                    if(isset($custom_fields['saswp_video_object_description'])){
                     $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_video_object_description'] ));
                    }
                    if(isset($custom_fields['saswp_video_object_transcript'])){
                    $input1['transcript'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_video_object_transcript'] ));
                    }
                    if(isset($custom_fields['saswp_video_object_name'])){
                     $input1['name'] =    $custom_fields['saswp_video_object_name'];
                    }                    
                    if(isset($custom_fields['saswp_video_object_upload_date'])){
                     $input1['uploadDate'] =    $custom_fields['saswp_video_object_upload_date'];
                    }
                    if(isset($custom_fields['saswp_video_object_thumbnail_url'])){
                     $input1['thumbnailUrl'] =    saswp_validate_url($custom_fields['saswp_video_object_thumbnail_url']);
                    }                                        
                    if(isset($custom_fields['saswp_video_object_content_url']) && wp_http_validate_url($custom_fields['saswp_video_object_content_url']) ){
                     $input1['contentUrl'] =    saswp_validate_url($custom_fields['saswp_video_object_content_url']);
                    }
                    if(isset($custom_fields['saswp_video_object_embed_url']) && wp_http_validate_url($custom_fields['saswp_video_object_embed_url'])){
                     $input1['embedUrl']   =    saswp_validate_url($custom_fields['saswp_video_object_embed_url']);
                    }                                                                                                  
                    if(isset($custom_fields['saswp_video_object_author_name'])){
                     $input1['author']['name'] =    $custom_fields['saswp_video_object_author_name'];
                    }
                    if(isset($custom_fields['saswp_video_object_author_url'])){
                     $input1['author']['url'] =    saswp_validate_url($custom_fields['saswp_video_object_author_url']);
                    }
                    if(isset($custom_fields['saswp_video_object_author_description'])){
                     $input1['author']['description'] =    $custom_fields['saswp_video_object_author_description'];
                    }
                    if(isset($custom_fields['saswp_video_object_author_image'])){
                     $input1['author']['image'] =    $custom_fields['saswp_video_object_author_image'];
                    }                      
                    if(isset($custom_fields['saswp_video_object_organization_logo']) && isset($custom_fields['saswp_video_object_organization_name'])){
                     $input1['publisher']['@type']       =    'Organization';
                     $input1['publisher']['name']        =    $custom_fields['saswp_video_object_organization_name'];
                     $input1['publisher']['logo']        =    $custom_fields['saswp_video_object_organization_logo'];
                    }
                    
                    break;
                    
                    case 'ImageObject':
                    
                    if(isset($custom_fields['saswpimage_object_url'])){
                     $input1['url'] =    saswp_validate_url($custom_fields['saswpimage_object_url']);
                    }                    
                    if(isset($custom_fields['saswpimage_object_date_published'])){
                     $input1['datePublished'] =    $custom_fields['saswpimage_object_date_published'];
                    }                    
                    if(isset($custom_fields['saswpimage_object_date_modified'])){
                     $input1['dateModified'] =    $custom_fields['saswpimage_object_date_modified'];
                    }
                    if(isset($custom_fields['saswpimage_object_description'])){
                     $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswpimage_object_description'] ));
                    }
                    if(isset($custom_fields['saswpimage_object_name'])){
                     $input1['name'] =    $custom_fields['saswpimage_object_name'];
                    }                    
                    if(isset($custom_fields['saswpimage_object_upload_date'])){
                     $input1['uploadDate'] =    $custom_fields['saswpimage_object_upload_date'];
                    }
                    if(isset($custom_fields['saswpimage_object_thumbnail_url'])){
                     $input1['thumbnailUrl'] =    saswp_validate_url($custom_fields['saswpimage_object_thumbnail_url']);
                    }                                        
                    if(isset($custom_fields['saswpimage_object_content_url'])){
                     $input1['contentUrl'] =    saswp_validate_url($custom_fields['saswpimage_object_content_url']);
                    }
                    if(isset($custom_fields['saswpimage_object_content_location'])){
                     $input1['contentLocation'] =    $custom_fields['saswpimage_object_content_location'];
                    }
                    if(isset($custom_fields['saswpimage_object_author_name'])){
                     $input1['author']['name'] =    $custom_fields['saswpimage_object_author_name'];
                    }
                    if(isset($custom_fields['saswpimage_object_author_url'])){
                     $input1['author']['url'] =    saswp_validate_url($custom_fields['saswpimage_object_author_url']);
                    }
                    if(isset($custom_fields['saswpimage_object_author_description'])){
                     $input1['author']['description'] =    $custom_fields['saswpimage_object_author_description'];
                    }
                    if(isset($custom_fields['saswpimage_object_author_image'])){
                     $input1['author']['image'] =    $custom_fields['saswpimage_object_author_image'];
                    }                      
                    if(isset($custom_fields['saswpimage_object_organization_logo']) && isset($custom_fields['saswpimage_object_organization_name'])){
                     $input1['publisher']['@type']       =    'Organization';
                     $input1['publisher']['name']        =    $custom_fields['saswpimage_object_organization_name'];
                     $input1['publisher']['logo']        =    $custom_fields['saswpimage_object_organization_logo'];
                    }
                    
                    break;
                
                case 'qanda':
                    
                    if(isset($custom_fields['saswp_qa_question_title'])){
                     $input1['mainEntity']['name'] =    $custom_fields['saswp_qa_question_title'];
                    }
                    if(isset($custom_fields['saswp_qa_question_description'])){
                     $input1['mainEntity']['text'] =    $custom_fields['saswp_qa_question_description'];
                    }
                    if(isset($custom_fields['saswp_qa_upvote_count'])){
                     $input1['mainEntity']['upvoteCount'] =    $custom_fields['saswp_qa_upvote_count'];
                    }
                    if(isset($custom_fields['saswp_qa_answer_count'])){
                        $input1['mainEntity']['answerCount'] =    $custom_fields['saswp_qa_answer_count'];
                    }
                    
                    if(isset($custom_fields['saswp_qa_date_created'])){
                     $input1['mainEntity']['dateCreated'] =    $custom_fields['saswp_qa_date_created'];
                    }
                    if(isset($custom_fields['saswp_qa_question_author_name'])){
                     $input1['mainEntity']['author']['@type'] =    'Person';
                     $input1['mainEntity']['author']['name'] =    $custom_fields['saswp_qa_question_author_name'];
                    }
                    if(isset($custom_fields['saswp_qa_accepted_answer_text'])){
                     $input1['mainEntity']['acceptedAnswer']['@type'] =    'Answer';   
                     $input1['mainEntity']['acceptedAnswer']['text'] =    $custom_fields['saswp_qa_accepted_answer_text'];
                    }
                    
                    if(isset($custom_fields['saswp_qa_accepted_answer_date_created'])){
                     $input1['mainEntity']['acceptedAnswer']['dateCreated'] =    $custom_fields['saswp_qa_accepted_answer_date_created'];
                    }
                    if(isset($custom_fields['saswp_qa_accepted_answer_upvote_count'])){
                     $input1['mainEntity']['acceptedAnswer']['upvoteCount'] =    $custom_fields['saswp_qa_accepted_answer_upvote_count'];
                    }
                    if(isset($custom_fields['saswp_qa_accepted_answer_url'])){
                     $input1['mainEntity']['acceptedAnswer']['url'] =    $custom_fields['saswp_qa_accepted_answer_url'];
                    }
                    
                    if(isset($custom_fields['saswp_qa_accepted_author_name'])){
                     $input1['mainEntity']['acceptedAnswer']['author']['name'] =    $custom_fields['saswp_qa_accepted_author_name'];
                    }                                        
                    if(isset($custom_fields['saswp_qa_suggested_answer_text'])){
                     $input1['mainEntity']['suggestedAnswer']['@type'] =    'Answer';   
                     $input1['mainEntity']['suggestedAnswer']['text'] =    $custom_fields['saswp_qa_suggested_answer_text'];
                    }
                    if(isset($custom_fields['saswp_qa_suggested_answer_date_created'])){
                     $input1['mainEntity']['suggestedAnswer']['dateCreated'] =    $custom_fields['saswp_qa_suggested_answer_date_created'];
                    }
                    
                    if(isset($custom_fields['saswp_qa_suggested_answer_upvote_count'])){
                     $input1['mainEntity']['suggestedAnswer']['upvoteCount'] =    $custom_fields['saswp_qa_suggested_answer_upvote_count'];
                    }
                    if(isset($custom_fields['saswp_qa_suggested_answer_url'])){
                     $input1['mainEntity']['suggestedAnswer']['url'] =    $custom_fields['saswp_qa_suggested_answer_url'];
                    }
                    if(isset($custom_fields['saswp_qa_suggested_author_name'])){
                     $input1['mainEntity']['suggestedAnswer']['author']['name'] =    $custom_fields['saswp_qa_suggested_author_name'];
                    }
                                        
                    break;
                    
                case 'TVSeries':      
                      
                    if(isset($custom_fields['saswp_tvseries_schema_name'])){
                     $input1['name'] =    $custom_fields['saswp_tvseries_schema_name'];
                    }
                    if(isset($custom_fields['saswp_tvseries_schema_description'])){
                     $input1['description'] =  wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_tvseries_schema_description'] ));
                    }
                    if(isset($custom_fields['saswp_tvseries_schema_image'])){
                     $input1['image'] =    $custom_fields['saswp_tvseries_schema_image'];
                    }
                    if(isset($custom_fields['saswp_tvseries_schema_author_name'])){
                     $input1['author']['name'] =    $custom_fields['saswp_tvseries_schema_author_name'];
                    }
                    
                break;
                
                case 'TouristAttraction':      
                      
                    if(isset($custom_fields['saswp_ta_schema_name'])){
                     $input1['name'] =    $custom_fields['saswp_ta_schema_name'];
                    }
                    if(isset($custom_fields['saswp_ta_schema_description'])){
                     $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_ta_schema_description'] ));
                    }
                    if(isset($custom_fields['saswp_ta_schema_image'])){
                     $input1['image'] =    $custom_fields['saswp_ta_schema_image'];
                    }
                    if(isset($custom_fields['saswp_ta_schema_url'])){
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_ta_schema_url']);
                    }
                    if(isset($custom_fields['saswp_ta_schema_is_acceesible_free'])){
                     $input1['isAccessibleForFree'] =    $custom_fields['saswp_ta_schema_is_acceesible_free'];
                    }
                    if(isset($custom_fields['saswp_ta_schema_locality'])){
                     $input1['address']['addressLocality'] =    $custom_fields['saswp_ta_schema_locality'];
                    }
                    if(isset($custom_fields['saswp_ta_schema_region'])){
                     $input1['address']['addressRegion'] =    $custom_fields['saswp_ta_schema_region'];
                    }
                    if(isset($custom_fields['saswp_ta_schema_country'])){
                     $input1['address']['addressCountry'] =    $custom_fields['saswp_ta_schema_country'];
                    }
                    if(isset($custom_fields['saswp_ta_schema_postal_code'])){
                     $input1['address']['PostalCode'] =    $custom_fields['saswp_ta_schema_postal_code'];
                    }
                    if(isset($custom_fields['saswp_ta_schema_latitude']) && isset($custom_fields['saswp_ta_schema_longitude'])){                        
                     $input1['geo']['@type']     =    'GeoCoordinates';   
                     $input1['geo']['latitude']  =    $custom_fields['saswp_ta_schema_latitude'];
                     $input1['geo']['longitude'] =    $custom_fields['saswp_ta_schema_longitude'];                     
                    }
                    
                break;
                
                case 'FAQ':   
                    
                    if(isset($custom_fields['saswp_faq_headline'])){
                     $input1['headline'] =    $custom_fields['saswp_faq_headline'];
                    }
                    if(isset($custom_fields['saswp_faq_keywords'])){
                     $input1['keywords'] =    $custom_fields['saswp_faq_keywords'];
                    }
                    if(isset($custom_fields['saswp_faq_date_created'])){
                     $input1['datePublished'] =    $custom_fields['saswp_faq_date_created'];
                    }
                    if(isset($custom_fields['saswp_faq_date_published'])){
                     $input1['dateModified'] =    $custom_fields['saswp_faq_date_published'];
                    }
                    if(isset($custom_fields['saswp_faq_date_modified'])){
                     $input1['dateCreated'] =    $custom_fields['saswp_faq_date_modified'];
                    }                    
                    if(isset($custom_fields['saswp_faq_author'])){
                       $input1['author']['@type']             =   'Person';                                              
                       $input1['author']['name']              =    $custom_fields['saswp_faq_author'];                                              
                    }
                                                             
                break;
                
                case 'TouristDestination':      
                      
                    if(isset($custom_fields['saswp_td_schema_name'])){
                     $input1['name'] =    $custom_fields['saswp_td_schema_name'];
                    }
                    if(isset($custom_fields['saswp_td_schema_description'])){
                     $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_td_schema_description'] ));
                    }
                    if(isset($custom_fields['saswp_td_schema_image'])){
                     $input1['image'] =    $custom_fields['saswp_td_schema_image'];
                    }
                    if(isset($custom_fields['saswp_td_schema_url'])){
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_td_schema_url']);
                    }
                    if(isset($custom_fields['saswp_td_schema_locality'])){
                     $input1['address']['addressLocality'] =    $custom_fields['saswp_td_schema_locality'];
                    }
                    if(isset($custom_fields['saswp_td_schema_region'])){
                     $input1['address']['addressRegion'] =    $custom_fields['saswp_td_schema_region'];
                    }
                    if(isset($custom_fields['saswp_td_schema_country'])){
                     $input1['address']['addressCountry'] =    $custom_fields['saswp_td_schema_country'];
                    }
                    if(isset($custom_fields['saswp_td_schema_postal_code'])){
                     $input1['address']['PostalCode'] =    $custom_fields['saswp_td_schema_postal_code'];
                    }
                    if(isset($custom_fields['saswp_td_schema_latitude']) && isset($custom_fields['saswp_td_schema_longitude'])){                        
                     $input1['geo']['@type']     =    'GeoCoordinates';   
                     $input1['geo']['latitude']  =    $custom_fields['saswp_td_schema_latitude'];
                     $input1['geo']['longitude'] =    $custom_fields['saswp_td_schema_longitude'];                     
                    }
                    
                break;
                
                case 'LandmarksOrHistoricalBuildings':      
                      
                    if(isset($custom_fields['saswp_lorh_schema_name'])){
                     $input1['name'] =    $custom_fields['saswp_lorh_schema_name'];
                    }
                    if(isset($custom_fields['saswp_lorh_schema_description'])){
                     $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_lorh_schema_description'] ));
                    }
                    if(isset($custom_fields['saswp_lorh_schema_image'])){
                     $input1['image'] =    $custom_fields['saswp_lorh_schema_image'];
                    }
                    if(isset($custom_fields['saswp_lorh_schema_url'])){
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_lorh_schema_url']);
                    }
                    if(isset($custom_fields['saswp_lorh_schema_hasmap'])){
                     $input1['hasMap'] =    $custom_fields['saswp_lorh_schema_hasmap'];
                    }
                    if(isset($custom_fields['saswp_lorh_schema_is_acceesible_free'])){
                     $input1['isAccessibleForFree'] =    $custom_fields['saswp_lorh_schema_is_acceesible_free'];
                    }
                    if(isset($custom_fields['saswp_lorh_schema_maximum_a_capacity'])){
                     $input1['maximumAttendeeCapacity'] =    $custom_fields['saswp_lorh_schema_maximum_a_capacity'];
                    }
                    if(isset($custom_fields['saswp_lorh_schema_locality'])){
                     $input1['address']['addressLocality'] =    $custom_fields['saswp_lorh_schema_locality'];
                    }
                    if(isset($custom_fields['saswp_lorh_schema_region'])){
                     $input1['address']['addressRegion'] =    $custom_fields['saswp_lorh_schema_region'];
                    }
                    if(isset($custom_fields['saswp_lorh_schema_country'])){
                     $input1['address']['addressCountry'] =    $custom_fields['saswp_lorh_schema_country'];
                    }
                    if(isset($custom_fields['saswp_lorh_schema_postal_code'])){
                     $input1['address']['PostalCode'] =    $custom_fields['saswp_lorh_schema_postal_code'];
                    }
                    if(isset($custom_fields['saswp_lorh_schema_latitude']) && isset($custom_fields['saswp_lorh_schema_longitude'])){                        
                     $input1['geo']['@type']     =    'GeoCoordinates';   
                     $input1['geo']['latitude']  =    $custom_fields['saswp_lorh_schema_latitude'];
                     $input1['geo']['longitude'] =    $custom_fields['saswp_lorh_schema_longitude'];                     
                    }
                    
                break;
                
                case 'HinduTemple':      
                      
                    if(isset($custom_fields['saswp_hindutemple_schema_name'])){
                     $input1['name'] =    $custom_fields['saswp_hindutemple_schema_name'];
                    }
                    if(isset($custom_fields['saswp_hindutemple_schema_description'])){
                     $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_hindutemple_schema_description'] ));
                    }
                    if(isset($custom_fields['saswp_hindutemple_schema_image'])){
                     $input1['image'] =    $custom_fields['saswp_hindutemple_schema_image'];
                    }
                    if(isset($custom_fields['saswp_hindutemple_schema_url'])){
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_hindutemple_schema_url']);
                    }
                    if(isset($custom_fields['saswp_hindutemple_schema_hasmap'])){
                     $input1['hasMap'] =    $custom_fields['saswp_hindutemple_schema_hasmap'];
                    }
                    if(isset($custom_fields['saswp_hindutemple_schema_is_accesible_free'])){
                     $input1['isAccessibleForFree'] =    $custom_fields['saswp_hindutemple_schema_is_accesible_free'];
                    }
                    if(isset($custom_fields['saswp_hindutemple_schema_maximum_a_capacity'])){
                     $input1['maximumAttendeeCapacity'] =    $custom_fields['saswp_hindutemple_schema_maximum_a_capacity'];
                    }
                    if(isset($custom_fields['saswp_hindutemple_schema_locality'])){
                     $input1['address']['addressLocality'] =    $custom_fields['saswp_hindutemple_schema_locality'];
                    }
                    if(isset($custom_fields['saswp_hindutemple_schema_region'])){
                     $input1['address']['addressRegion'] =    $custom_fields['saswp_hindutemple_schema_region'];
                    }
                    if(isset($custom_fields['saswp_hindutemple_schema_country'])){
                     $input1['address']['addressCountry'] =    $custom_fields['saswp_hindutemple_schema_country'];
                    }
                    if(isset($custom_fields['saswp_hindutemple_schema_postal_code'])){
                     $input1['address']['PostalCode'] =    $custom_fields['saswp_hindutemple_schema_postal_code'];
                    }
                    if(isset($custom_fields['saswp_hindutemple_schema_latitude']) && isset($custom_fields['saswp_hindutemple_schema_longitude'])){                        
                     $input1['geo']['@type']     =    'GeoCoordinates';   
                     $input1['geo']['latitude']  =    $custom_fields['saswp_hindutemple_schema_latitude'];
                     $input1['geo']['longitude'] =    $custom_fields['saswp_hindutemple_schema_longitude'];                     
                    }
                    
                break;

                case 'BuddhistTemple':      
                      
                    if(isset($custom_fields['saswp_buddhisttemple_schema_name'])){
                     $input1['name'] =    $custom_fields['saswp_buddhisttemple_schema_name'];
                    }
                    if(isset($custom_fields['saswp_buddhisttemple_schema_description'])){
                     $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_buddhisttemple_schema_description'] ));
                    }
                    if(isset($custom_fields['saswp_buddhisttemple_schema_image'])){
                     $input1['image'] =    $custom_fields['saswp_buddhisttemple_schema_image'];
                    }
                    if(isset($custom_fields['saswp_buddhisttemple_schema_url'])){
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_buddhisttemple_schema_url']);
                    }
                    if(isset($custom_fields['saswp_buddhisttemple_schema_hasmap'])){
                     $input1['hasMap'] =    $custom_fields['saswp_buddhisttemple_schema_hasmap'];
                    }
                    if(isset($custom_fields['saswp_buddhisttemple_schema_is_accesible_free'])){
                     $input1['isAccessibleForFree'] =    $custom_fields['saswp_buddhisttemple_schema_is_accesible_free'];
                    }
                    if(isset($custom_fields['saswp_buddhisttemple_schema_maximum_a_capacity'])){
                     $input1['maximumAttendeeCapacity'] =    $custom_fields['saswp_buddhisttemple_schema_maximum_a_capacity'];
                    }
                    if(isset($custom_fields['saswp_buddhisttemple_schema_locality'])){
                     $input1['address']['addressLocality'] =    $custom_fields['saswp_buddhisttemple_schema_locality'];
                    }
                    if(isset($custom_fields['saswp_buddhisttemple_schema_region'])){
                     $input1['address']['addressRegion'] =    $custom_fields['saswp_buddhisttemple_schema_region'];
                    }
                    if(isset($custom_fields['saswp_buddhisttemple_schema_country'])){
                     $input1['address']['addressCountry'] =    $custom_fields['saswp_buddhisttemple_schema_country'];
                    }
                    if(isset($custom_fields['saswp_buddhisttemple_schema_postal_code'])){
                     $input1['address']['PostalCode'] =    $custom_fields['saswp_buddhisttemple_schema_postal_code'];
                    }
                    if(isset($custom_fields['saswp_buddhisttemple_schema_latitude']) && isset($custom_fields['saswp_buddhisttemple_schema_longitude'])){                        
                     $input1['geo']['@type']     =    'GeoCoordinates';   
                     $input1['geo']['latitude']  =    $custom_fields['saswp_buddhisttemple_schema_latitude'];
                     $input1['geo']['longitude'] =    $custom_fields['saswp_buddhisttemple_schema_longitude'];                     
                    }
                    
                break;
                
                case 'Church':      
                      
                    if(isset($custom_fields['saswp_church_schema_name'])){
                     $input1['name'] =    $custom_fields['saswp_church_schema_name'];
                    }
                    if(isset($custom_fields['saswp_church_schema_description'])){
                     $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_church_schema_description'] ));
                    }
                    if(isset($custom_fields['saswp_church_schema_image'])){
                     $input1['image'] =    $custom_fields['saswp_church_schema_image'];
                    }
                    if(isset($custom_fields['saswp_church_schema_url'])){
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_church_schema_url']);
                    }
                    if(isset($custom_fields['saswp_church_schema_hasmap'])){
                     $input1['hasMap'] =    $custom_fields['saswp_church_schema_hasmap'];
                    }
                    if(isset($custom_fields['saswp_church_schema_is_accesible_free'])){
                     $input1['isAccessibleForFree'] =    $custom_fields['saswp_church_schema_is_accesible_free'];
                    }
                    if(isset($custom_fields['saswp_church_schema_maximum_a_capacity'])){
                     $input1['maximumAttendeeCapacity'] =    $custom_fields['saswp_church_schema_maximum_a_capacity'];
                    }
                    if(isset($custom_fields['saswp_church_schema_locality'])){
                     $input1['address']['addressLocality'] =    $custom_fields['saswp_church_schema_locality'];
                    }
                    if(isset($custom_fields['saswp_church_schema_region'])){
                     $input1['address']['addressRegion'] =    $custom_fields['saswp_church_schema_region'];
                    }
                    if(isset($custom_fields['saswp_church_schema_country'])){
                     $input1['address']['addressCountry'] =    $custom_fields['saswp_church_schema_country'];
                    }
                    if(isset($custom_fields['saswp_church_schema_postal_code'])){
                     $input1['address']['PostalCode'] =    $custom_fields['saswp_church_schema_postal_code'];
                    }
                    if(isset($custom_fields['saswp_church_schema_latitude']) && isset($custom_fields['saswp_church_schema_longitude'])){                        
                     $input1['geo']['@type']     =    'GeoCoordinates';   
                     $input1['geo']['latitude']  =    $custom_fields['saswp_church_schema_latitude'];
                     $input1['geo']['longitude'] =    $custom_fields['saswp_church_schema_longitude'];                     
                    }
                    
                break;
                
                case 'Mosque':      
                      
                    if(isset($custom_fields['saswp_mosque_schema_name'])){
                     $input1['name'] =    $custom_fields['saswp_mosque_schema_name'];
                    }
                    if(isset($custom_fields['saswp_mosque_schema_description'])){
                     $input1['description'] =  wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_mosque_schema_description'] ));
                    }
                    if(isset($custom_fields['saswp_mosque_schema_image'])){
                     $input1['image'] =    $custom_fields['saswp_mosque_schema_image'];
                    }
                    if(isset($custom_fields['saswp_mosque_schema_url'])){
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_mosque_schema_url']);
                    }
                    if(isset($custom_fields['saswp_mosque_schema_hasmap'])){
                     $input1['hasMap'] =    $custom_fields['saswp_mosque_schema_hasmap'];
                    }
                    if(isset($custom_fields['saswp_mosque_schema_is_accesible_free'])){
                     $input1['isAccessibleForFree'] =    $custom_fields['saswp_mosque_schema_is_accesible_free'];
                    }
                    if(isset($custom_fields['saswp_mosque_schema_maximum_a_capacity'])){
                     $input1['maximumAttendeeCapacity'] =    $custom_fields['saswp_mosque_schema_maximum_a_capacity'];
                    }
                    if(isset($custom_fields['saswp_mosque_schema_locality'])){
                     $input1['address']['addressLocality'] =    $custom_fields['saswp_mosque_schema_locality'];
                    }
                    if(isset($custom_fields['saswp_mosque_schema_region'])){
                     $input1['address']['addressRegion'] =    $custom_fields['saswp_mosque_schema_region'];
                    }
                    if(isset($custom_fields['saswp_mosque_schema_country'])){
                     $input1['address']['addressCountry'] =    $custom_fields['saswp_mosque_schema_country'];
                    }
                    if(isset($custom_fields['saswp_mosque_schema_postal_code'])){
                     $input1['address']['PostalCode'] =    $custom_fields['saswp_mosque_schema_postal_code'];
                    }
                    if(isset($custom_fields['saswp_mosque_schema_latitude']) && isset($custom_fields['saswp_mosque_schema_longitude'])){                        
                     $input1['geo']['@type']     =    'GeoCoordinates';   
                     $input1['geo']['latitude']  =    $custom_fields['saswp_mosque_schema_latitude'];
                     $input1['geo']['longitude'] =    $custom_fields['saswp_mosque_schema_longitude'];                     
                    }
                    
                break;
                
                case 'Person':      
                      
                    if(isset($custom_fields['saswp_person_schema_name'])){
                     $input1['name'] =    $custom_fields['saswp_person_schema_name'];
                    }
                    if(isset($custom_fields['saswp_person_schema_description'])){
                     $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_person_schema_description'] ));
                    }
                    if(isset($custom_fields['saswp_person_schema_url'])){
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_person_schema_url']);
                    }
                    if(isset($custom_fields['saswp_person_schema_street_address'])){
                     $input1['address']['streetAddress'] =    $custom_fields['saswp_person_schema_street_address'];
                    }
                    if(isset($custom_fields['saswp_person_schema_locality'])){
                     $input1['address']['addressLocality'] =    $custom_fields['saswp_person_schema_locality'];
                    }
                    if(isset($custom_fields['saswp_person_schema_region'])){
                     $input1['address']['addressRegion'] =    $custom_fields['saswp_person_schema_region'];
                    }
                    if(isset($custom_fields['saswp_person_schema_postal_code'])){
                      $input1['address']['PostalCode']  =    $custom_fields['saswp_person_schema_postal_code'];
                    }
                    if(isset($custom_fields['saswp_person_schema_country'])){
                     $input1['address']['addressCountry'] =    $custom_fields['saswp_person_schema_country'];
                    }
                    if(isset($custom_fields['saswp_person_schema_email'])){
                     $input1['email'] =    $custom_fields['saswp_person_schema_email'];
                    }
                    if(isset($custom_fields['saswp_person_schema_telephone'])){
                     $input1['telephone'] =    $custom_fields['saswp_person_schema_telephone'];
                    }
                    if(isset($custom_fields['saswp_person_schema_gender'])){
                     $input1['gender'] =    $custom_fields['saswp_person_schema_gender'];
                    }
                    if(isset($custom_fields['saswp_person_schema_date_of_birth'])){
                     $input1['birthDate'] =    $custom_fields['saswp_person_schema_date_of_birth'];
                    }                    
                    if(isset($custom_fields['saswp_person_schema_nationality'])){
                     $input1['nationality'] =    $custom_fields['saswp_person_schema_nationality'];
                    }
                    if(isset($custom_fields['saswp_person_schema_image'])){
                     $input1['image'] =    $custom_fields['saswp_person_schema_image'];
                    }
                    if(isset($custom_fields['saswp_person_schema_job_title'])){
                     $input1['jobTitle'] =    $custom_fields['saswp_person_schema_job_title'];
                    }
                    if(isset($custom_fields['saswp_person_schema_award'])){
                        $input1['award'] =    $custom_fields['saswp_person_schema_award'];
                    }
                    if(isset($custom_fields['saswp_person_schema_brand'])){
                        $input1['brand'] =    $custom_fields['saswp_person_schema_brand'];
                    }

                    if(isset($custom_fields['saswp_person_schema_honorific_prefix'])){
                        $input1['honorificPrefix'] =    $custom_fields['saswp_person_schema_honorific_prefix'];
                    }
                    if(isset($custom_fields['saswp_person_schema_honorific_suffix'])){
                        $input1['honorificSuffix'] =    $custom_fields['saswp_person_schema_honorific_suffix'];
                    }
                    if(isset($custom_fields['saswp_person_schema_qualifications'])){
                        $input1['hasCredential'] =    $custom_fields['saswp_person_schema_qualifications'];
                    }                    
                    if(isset($custom_fields['saswp_person_schema_affiliation'])){
                        $input1['affiliation'] =    $custom_fields['saswp_person_schema_affiliation'];
                    }
                    if(isset($custom_fields['saswp_person_schema_alumniof'])){
                        $input1['alumniOf'] =    $custom_fields['saswp_person_schema_alumniof'];
                    }
                    if(isset($custom_fields['saswp_person_schema_occupation_name'])){
                        $input1['hasOccupation']['name'] =    $custom_fields['saswp_person_schema_occupation_name'];
                    }
                    if(isset($custom_fields['saswp_person_schema_occupation_description'])){
                        $input1['hasOccupation']['description'] =    $custom_fields['saswp_person_schema_occupation_description'];
                    }
                    if(isset($custom_fields['saswp_person_schema_occupation_city'])){
                        $input1['hasOccupation']['occupationLocation']['@type'] = 'City'; 
                        $input1['hasOccupation']['occupationLocation']['name']  =    $custom_fields['saswp_person_schema_occupation_city'];
                    }
                    if(isset($custom_fields['saswp_person_schema_estimated_salary'])){
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
                    if(isset($custom_fields['saswp_person_schema_salary_last_reviewed'])){
                        $input1['hasOccupation']['mainEntityOfPage']['@type']         = 'WebPage'; 
                        $input1['hasOccupation']['mainEntityOfPage']['lastReviewed']  =    $custom_fields['saswp_person_schema_salary_last_reviewed'];
                    }                    

                    $sameas = array();
                    if(isset($custom_fields['saswp_person_schema_website'])){
                        $sameas[] =    $custom_fields['saswp_person_schema_website'];
                    }
                    if(isset($custom_fields['saswp_person_schema_facebook'])){
                        $sameas[] =    $custom_fields['saswp_person_schema_facebook'];
                    }
                    if(isset($custom_fields['saswp_person_schema_twitter'])){
                        $sameas[] =    $custom_fields['saswp_person_schema_twitter'];
                    }
                    if(isset($custom_fields['saswp_person_schema_linkedin'])){
                        $sameas[] =    $custom_fields['saswp_person_schema_linkedin'];
                    }
                    if($sameas){
                        $input1['sameAs'] = $sameas;
                    }
                    
                break;
                                
                case 'Apartment':      
                      
                    if(isset($custom_fields['saswp_apartment_schema_name'])){
                     $input1['name'] =    $custom_fields['saswp_apartment_schema_name'];
                    }
                    if(isset($custom_fields['saswp_apartment_schema_url'])){
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_apartment_schema_url']);
                    }
                    if(isset($custom_fields['saswp_apartment_schema_image'])){
                     $input1['image'] =    $custom_fields['saswp_apartment_schema_image'];
                    }
                    if(isset($custom_fields['saswp_apartment_schema_description'])){
                     $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_apartment_schema_description'] ));
                    }
                    if(isset($custom_fields['saswp_apartment_schema_numberofrooms'])){
                     $input1['numberOfRooms'] =    $custom_fields['saswp_apartment_schema_numberofrooms'];
                    }
                    if(isset($custom_fields['saswp_apartment_schema_country'])){
                     $input1['address']['addressCountry'] =    $custom_fields['saswp_apartment_schema_country'];
                    }
                    if(isset($custom_fields['saswp_apartment_schema_locality'])){
                     $input1['address']['addressLocality'] =    $custom_fields['saswp_apartment_schema_locality'];
                    }
                    if(isset($custom_fields['saswp_apartment_schema_region'])){
                     $input1['address']['addressRegion'] =    $custom_fields['saswp_apartment_schema_region'];
                    }
                    if(isset($custom_fields['saswp_apartment_schema_postalcode'])){
                     $input1['address']['PostalCode'] =    $custom_fields['saswp_apartment_schema_postalcode'];
                    }
                    if(isset($custom_fields['saswp_apartment_schema_telephone'])){
                     $input1['telephone'] =    $custom_fields['saswp_apartment_schema_telephone'];
                    }
                    if(isset($custom_fields['saswp_apartment_schema_latitude']) && isset($custom_fields['saswp_apartment_schema_longitude'])){                        
                     $input1['geo']['@type']     =    'GeoCoordinates';   
                     $input1['geo']['latitude']  =    $custom_fields['saswp_apartment_schema_latitude'];
                     $input1['geo']['longitude'] =    $custom_fields['saswp_apartment_schema_longitude'];                     
                    }
                    
                break;
                
                case 'House':      
                      
                    if(isset($custom_fields['saswp_house_schema_name'])){
                     $input1['name'] =    $custom_fields['saswp_house_schema_name'];
                    }
                    if(isset($custom_fields['saswp_house_schema_url'])){
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_house_schema_url']);
                    }
                    if(isset($custom_fields['saswp_house_schema_image'])){
                     $input1['image'] =    $custom_fields['saswp_house_schema_image'];
                    }
                    if(isset($custom_fields['saswp_house_schema_description'])){
                     $input1['description'] =  wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_house_schema_description'] ));
                    }
                    if(isset($custom_fields['saswp_house_schema_pets_allowed'])){
                     $input1['petsAllowed'] =    $custom_fields['saswp_house_schema_pets_allowed'];
                    }
                    if(isset($custom_fields['saswp_house_schema_country'])){
                     $input1['address']['addressCountry'] =    $custom_fields['saswp_house_schema_country'];
                    }
                    if(isset($custom_fields['saswp_house_schema_locality'])){
                     $input1['address']['addressLocality'] =    $custom_fields['saswp_house_schema_locality'];
                    }
                    if(isset($custom_fields['saswp_house_schema_region'])){
                     $input1['address']['addressRegion'] =    $custom_fields['saswp_house_schema_region'];
                    }
                    if(isset($custom_fields['saswp_house_schema_postalcode'])){
                     $input1['address']['PostalCode'] =    $custom_fields['saswp_house_schema_postalcode'];
                    }
                    if(isset($custom_fields['saswp_house_schema_telephone'])){
                     $input1['telephone'] =    $custom_fields['saswp_house_schema_telephone'];
                    }
                    if(isset($custom_fields['saswp_house_schema_hasmap'])){
                     $input1['hasMap'] =    $custom_fields['saswp_house_schema_hasmap'];
                    }
                    if(isset($custom_fields['saswp_house_schema_floor_size'])){
                     $input1['floorSize'] =    $custom_fields['saswp_house_schema_floor_size'];
                    }
                    if(isset($custom_fields['saswp_house_schema_no_of_rooms'])){
                     $input1['numberOfRooms'] =    $custom_fields['saswp_house_schema_no_of_rooms'];
                    }
                    if(isset($custom_fields['saswp_house_schema_latitude']) && isset($custom_fields['saswp_house_schema_longitude'])){                        
                     $input1['geo']['@type']     =    'GeoCoordinates';   
                     $input1['geo']['latitude']  =    $custom_fields['saswp_house_schema_latitude'];
                     $input1['geo']['longitude'] =    $custom_fields['saswp_house_schema_longitude'];                     
                    }
                    
                break;
                
                case 'SingleFamilyResidence':      
                      
                    if(isset($custom_fields['saswp_sfr_schema_name'])){
                     $input1['name'] =    $custom_fields['saswp_sfr_schema_name'];
                    }
                    if(isset($custom_fields['saswp_sfr_schema_url'])){
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_sfr_schema_url']);
                    }
                    if(isset($custom_fields['saswp_sfr_schema_image'])){
                     $input1['image'] =    $custom_fields['saswp_sfr_schema_image'];
                    }
                    if(isset($custom_fields['saswp_sfr_schema_description'])){
                     $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_sfr_schema_description'] ));
                    }
                    if(isset($custom_fields['saswp_sfr_schema_numberofrooms'])){
                     $input1['numberOfRooms'] =    $custom_fields['saswp_sfr_schema_numberofrooms'];
                    }
                    if(isset($custom_fields['saswp_sfr_schema_pets_allowed'])){
                     $input1['petsAllowed'] =    $custom_fields['saswp_sfr_schema_pets_allowed'];
                    }
                    if(isset($custom_fields['saswp_sfr_schema_country'])){
                     $input1['address']['addressCountry'] =    $custom_fields['saswp_sfr_schema_country'];
                    }
                    if(isset($custom_fields['saswp_sfr_schema_locality'])){
                     $input1['address']['addressLocality'] =    $custom_fields['saswp_sfr_schema_locality'];
                    }
                    if(isset($custom_fields['saswp_sfr_schema_region'])){
                     $input1['address']['addressRegion'] =    $custom_fields['saswp_sfr_schema_region'];
                    }
                    if(isset($custom_fields['saswp_sfr_schema_postalcode'])){
                     $input1['address']['PostalCode'] =    $custom_fields['saswp_sfr_schema_postalcode'];
                    }
                    if(isset($custom_fields['saswp_sfr_schema_telephone'])){
                     $input1['telephone'] =    $custom_fields['saswp_sfr_schema_telephone'];
                    }
                    if(isset($custom_fields['saswp_sfr_schema_hasmap'])){
                     $input1['hasMap'] =    $custom_fields['saswp_sfr_schema_hasmap'];
                    }
                    if(isset($custom_fields['saswp_sfr_schema_floor_size'])){
                     $input1['floorSize'] =    $custom_fields['saswp_sfr_schema_floor_size'];
                    }
                    if(isset($custom_fields['saswp_sfr_schema_no_of_rooms'])){
                     $input1['numberOfRooms'] =    $custom_fields['saswp_sfr_schema_no_of_rooms'];
                    }
                    if(isset($custom_fields['saswp_sfr_schema_latitude']) && isset($custom_fields['saswp_sfr_schema_longitude'])){                        
                     $input1['geo']['@type']     =    'GeoCoordinates';   
                     $input1['geo']['latitude']  =    $custom_fields['saswp_sfr_schema_latitude'];
                     $input1['geo']['longitude'] =    $custom_fields['saswp_sfr_schema_longitude'];                     
                    }
                    
                break;
                
                case 'VideoGame':      
                      
                    if(isset($custom_fields['saswp_vg_schema_name'])){
                     $input1['name'] =    $custom_fields['saswp_vg_schema_name'];
                    }
                    if(isset($custom_fields['saswp_vg_schema_url'])){
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_vg_schema_url']);
                    }
                    if(isset($custom_fields['saswp_vg_schema_image'])){
                     $input1['image'] =    $custom_fields['saswp_vg_schema_image'];
                    }
                    if(isset($custom_fields['saswp_vg_schema_description'])){
                     $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_vg_schema_description'] ));
                    }
                    if(isset($custom_fields['saswp_vg_schema_operating_system'])){
                     $input1['operatingSystem'] =    $custom_fields['saswp_vg_schema_operating_system'];
                    }
                    if(isset($custom_fields['saswp_vg_schema_application_category'])){
                     $input1['applicationCategory'] =    $custom_fields['saswp_vg_schema_application_category'];
                    }
                    if(isset($custom_fields['saswp_vg_schema_author_name'])){
                     $input1['author']['name'] =    $custom_fields['saswp_vg_schema_author_name'];
                    }
                    if(isset($custom_fields['saswp_vg_schema_price'])){
                     $input1['offers']['price'] =    $custom_fields['saswp_vg_schema_price'];
                    }
                    if(isset($custom_fields['saswp_vg_schema_price_currency'])){
                     $input1['offers']['priceCurrency'] =    $custom_fields['saswp_vg_schema_price_currency'];
                    }
                    if(isset($custom_fields['saswp_vg_schema_price_availability'])){
                     $input1['offers']['availability'] =    $custom_fields['saswp_vg_schema_price_availability'];
                    }
                    if(isset($custom_fields['saswp_vg_schema_publisher'])){
                     $input1['publisher'] =    $custom_fields['saswp_vg_schema_publisher'];
                    }
                    if(isset($custom_fields['saswp_vg_schema_genre'])){
                     $input1['genre'] =    $custom_fields['saswp_vg_schema_genre'];
                    }
                    if(isset($custom_fields['saswp_vg_schema_processor_requirements'])){
                     $input1['processorRequirements'] =    $custom_fields['saswp_vg_schema_processor_requirements'];
                    }
                    if(isset($custom_fields['saswp_vg_schema_memory_requirements'])){
                     $input1['memoryRequirements'] =    $custom_fields['saswp_vg_schema_memory_requirements'];
                    }
                    if(isset($custom_fields['saswp_vg_schema_storage_requirements'])){
                     $input1['storageRequirements'] =    $custom_fields['saswp_vg_schema_storage_requirements'];
                    }
                    if(isset($custom_fields['saswp_vg_schema_game_platform'])){
                     $input1['gamePlatform'] =    $custom_fields['saswp_vg_schema_game_platform'];
                    }
                    if(isset($custom_fields['saswp_vg_schema_cheat_code'])){
                     $input1['cheatCode'] =    $custom_fields['saswp_vg_schema_cheat_code'];
                    }
                    
                break;
                
                case 'JobPosting':      
                      
                    if(isset($custom_fields['saswp_jobposting_schema_title'])){
                     $input1['title'] =    $custom_fields['saswp_jobposting_schema_title'];
                    }
                    if(isset($custom_fields['saswp_jobposting_schema_description'])){
                     $input1['description'] =    wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_jobposting_schema_description'] ));
                    }
                    if(isset($custom_fields['saswp_jobposting_schema_url'])){
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_jobposting_schema_url']);
                    }
                    if(isset($custom_fields['saswp_jobposting_schema_dateposted'])){
                     $input1['datePosted'] =    $custom_fields['saswp_jobposting_schema_dateposted'];
                    }
                    if(isset($custom_fields['saswp_jobposting_schema_validthrough'])){
                     $input1['validThrough'] =    $custom_fields['saswp_jobposting_schema_validthrough'];
                    }
                    if(isset($custom_fields['saswp_jobposting_schema_employment_type'])){
                     $input1['employmentType'] =    $custom_fields['saswp_jobposting_schema_employment_type'];
                    }
                    if(isset($custom_fields['saswp_jobposting_schema_ho_name'])){
                     $input1['hiringOrganization']['name'] =    $custom_fields['saswp_jobposting_schema_ho_name'];
                    }
                    if(isset($custom_fields['saswp_jobposting_schema_ho_url'])){
                     $input1['hiringOrganization']['sameAs'] =    saswp_validate_url($custom_fields['saswp_jobposting_schema_ho_url']);
                    }
                    if(isset($custom_fields['saswp_jobposting_schema_ho_logo'])){
                     $input1['hiringOrganization']['logo'] =    $custom_fields['saswp_jobposting_schema_ho_logo'];
                    }
                    if(isset($custom_fields['saswp_jobposting_schema_street_address'])){
                     $input1['jobLocation']['address']['streetAddress'] =    $custom_fields['saswp_jobposting_schema_street_address'];
                    }
                    if(isset($custom_fields['saswp_jobposting_schema_locality'])){
                     $input1['jobLocation']['address']['addressLocality'] =    $custom_fields['saswp_jobposting_schema_locality'];
                    }
                    if(isset($custom_fields['saswp_jobposting_schema_region'])){
                     $input1['jobLocation']['address']['addressRegion'] =    $custom_fields['saswp_jobposting_schema_region'];
                    }
                    if(isset($custom_fields['saswp_jobposting_schema_postalcode'])){
                     $input1['jobLocation']['address']['PostalCode'] =    $custom_fields['saswp_jobposting_schema_postalcode'];
                    }
                    if(isset($custom_fields['saswp_jobposting_schema_country'])){
                     $input1['jobLocation']['address']['addressCountry'] =    $custom_fields['saswp_jobposting_schema_country'];
                    }
                    if(isset($custom_fields['saswp_jobposting_schema_bs_currency'])){
                     $input1['baseSalary']['currency'] =    $custom_fields['saswp_jobposting_schema_bs_currency'];
                    }
                    if(isset($custom_fields['saswp_jobposting_schema_bs_value'])){
                     $input1['baseSalary']['value']['value'] =    $custom_fields['saswp_jobposting_schema_bs_value'];
                    }
                    if(isset($custom_fields['saswp_jobposting_schema_bs_unittext'])){
                     $input1['baseSalary']['value']['unitText'] =    $custom_fields['saswp_jobposting_schema_bs_unittext'];
                    }                    
                    if(isset($custom_fields['saswp_jobposting_schema_es_currency'])){
                    $input1['estimatedSalary']['currency'] =    $custom_fields['saswp_jobposting_schema_es_currency'];
                    }
                    if(isset($custom_fields['saswp_jobposting_schema_es_value'])){
                    $input1['estimatedSalary']['value']['value'] =    $custom_fields['saswp_jobposting_schema_es_value'];
                    }
                    if(isset($custom_fields['saswp_jobposting_schema_es_unittext'])){
                    $input1['estimatedSalary']['value']['unitText'] =    $custom_fields['saswp_jobposting_schema_es_unittext'];
                    }                    
                    if(isset($custom_fields['saswp_jobposting_schema_validthrough']) && date('Y-m-d',strtotime($custom_fields['saswp_jobposting_schema_validthrough'])) < date('Y-m-d') ){
                        $input1 = array();    
                    }
                    
                break;
                
                case 'Trip':      
                      
                    if(isset($custom_fields['saswp_trip_schema_name'])){
                     $input1['name'] =    $custom_fields['saswp_trip_schema_name'];
                    }
                    if(isset($custom_fields['saswp_trip_schema_description'])){
                     $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_trip_schema_description'] ));
                    }
                    if(isset($custom_fields['saswp_trip_schema_url'])){
                     $input1['url'] =    saswp_validate_url($custom_fields['saswp_trip_schema_url']);
                    }
                    if(isset($custom_fields['saswp_trip_schema_image'])){
                     $input1['image'] =    $custom_fields['saswp_trip_schema_image'];
                    }
                    
                break;
                
                case 'MedicalCondition':      
                      
                    if(isset($custom_fields['saswp_mc_schema_name'])){
                     $input1['name'] =    $custom_fields['saswp_mc_schema_name'];
                    }
                    if(isset($custom_fields['saswp_mc_schema_alternate_name'])){
                     $input1['alternateName'] =    $custom_fields['saswp_mc_schema_alternate_name'];
                    }
                    if(isset($custom_fields['saswp_mc_schema_description'])){
                     $input1['description'] =   wp_strip_all_tags(strip_shortcodes( $custom_fields['saswp_mc_schema_description'] ));
                    }
                    if(isset($custom_fields['saswp_mc_schema_image'])){
                     $input1['image'] =    $custom_fields['saswp_mc_schema_image'];
                    }
                    if(isset($custom_fields['saswp_mc_schema_anatomy_name'])){
                     $input1['associatedAnatomy']['name'] =    $custom_fields['saswp_mc_schema_anatomy_name'];
                    }
                    if(isset($custom_fields['saswp_mc_schema_medical_code'])){
                     $input1['code']['code'] =    $custom_fields['saswp_mc_schema_medical_code'];
                    }
                    if(isset($custom_fields['saswp_mc_schema_coding_system'])){
                     $input1['code']['codingSystem'] =    $custom_fields['saswp_mc_schema_coding_system'];
                    }
                    if(isset($custom_fields['saswp_mc_schema_drug'])){
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
               
                     default:
                         break;
                 }    
             
             if($main_schema_type == 'Review'){
                 
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
        public function saswp_get_schema_type_fields(){
            
             if ( ! isset( $_POST['saswp_security_nonce'] ) ){
                return; 
             }
             if ( !wp_verify_nonce( $_POST['saswp_security_nonce'], 'saswp_ajax_check_nonce' ) ){
                return;  
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
        public function saswp_get_custom_meta_fields(){
            
             if ( ! isset( $_POST['saswp_security_nonce'] ) ){
                return; 
             }
             if ( !wp_verify_nonce( $_POST['saswp_security_nonce'], 'saswp_ajax_check_nonce' ) ){
                return;  
             }
            
            $search_string = isset( $_POST['q'] ) ? sanitize_text_field( $_POST['q'] ) : '';                                    
	    $data          = array();
	    $result        = array();
            
            global $wpdb;
	    $saswp_meta_array = $wpdb->get_results( "SELECT DISTINCT meta_key FROM {$wpdb->postmeta} WHERE meta_key LIKE '%{$search_string}%'", ARRAY_A ); // WPCS: unprepared SQL OK.         
            if ( isset( $saswp_meta_array ) && ! empty( $saswp_meta_array ) ) {
                
				foreach ( $saswp_meta_array as $value ) {
				//	if ( ! in_array( $value['meta_key'], $schema_post_meta_fields ) ) {
						$data[] = array(
							'id'   => $value['meta_key'],
							'text' => preg_replace( '/^_/', '', esc_html( str_replace( '_', ' ', $value['meta_key'] ) ) ),
						);
					//}
				}
                                
			}
                        
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
             
             if (class_exists('WC_Product')) {
                 
             global $woocommerce;
             global $sd_data;
                 
	     $product = wc_get_product($post_id); 
             
             if(is_object($product)){   
                                               
               if(is_object($woocommerce)){
                             
                        if(method_exists('WC_Product_Simple', 'get_type')){

                            if($product->get_type() == 'variable'){

                                $product_id_some = $woocommerce->product_factory->get_product();
    
                                $variations  = $product_id_some->get_available_variations(); 
                                
                                    if($variations){
    
                                            foreach($variations as $value){
    
                                                    $varible_prices[] = $value['display_price']; 
    
                                            }
                                    }
    
                            }
                            
                        }                                        
				 				 
                }  
                 
             $gtin = get_post_meta($post_id, $key='hwp_product_gtin', true);
             
             if($gtin !=''){
                 
             $product_details['product_gtin8'] = $gtin;   
             
             }  
             
             $brand = '';
             $brand = get_post_meta($post_id, $key='hwp_product_brand', true);
             
             if($brand !=''){
                 
             $product_details['product_brand'] = $brand;   
             
             }
             
             if($brand == ''){
               
                 $product_details['product_brand'] = get_bloginfo();
                 
             }
                                                   
             $date_on_sale                           = $product->get_date_on_sale_to();                            
             $product_details['product_name']        = $product->get_title();
             
             $product_desc                           = '';
             
             if($product->get_short_description() && $product->get_description()){
                 
                 $product_desc = $product->get_short_description().' '.$product->get_description();
                 
             }else if($product->get_description()){
                 
                 $product_desc = $product->get_description();
                 
             }else{
                 
                 $product_desc = get_the_excerpt();
                 
             }
             
             if(isset($sd_data['saswp-yoast']) && class_exists('WPSEO_Meta')){
                 $product_desc = saswp_get_the_excerpt();                 
             }
             
             $product_details['product_description'] = wp_strip_all_tags(strip_shortcodes($product_desc));
             
             if($product->get_attributes()){
                 
                 foreach ($product->get_attributes() as $attribute) {
                     
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
                     if(strtolower($attribute['name']) == 'brand'){
                                            
                      $product_details['product_brand'] = $attribute['options'][0];   
                                                                 
                     }
                     
                 }
                 
             }
                
             if(!isset($product_details['product_mpn'])){
                 $product_details['product_mpn'] = get_the_ID();
             }
                               
             if(strtolower( $product->get_stock_status() ) == 'onbackorder'){
                 $product_details['product_availability'] = 'PreOrder';
             }else{
                 $product_details['product_availability'] = $product->get_stock_status();
             }
             
             if(method_exists('WC_Product_Simple', 'get_type')){
                 
                if($product->get_type() == 'variable'){
                    $product_details['product_varible_price']   = $varible_prices;
                }else{
                    $product_details['product_price']           = $product->get_price();
                }

             }else{
                $product_details['product_price']           = $product->get_price();
             }
                          
             $product_details['product_sku']             = $product->get_sku() ? $product->get_sku(): get_the_ID();             
             
             if(isset($date_on_sale)){
                 
             $product_details['product_priceValidUntil'] = $date_on_sale->date('Y-m-d G:i:s');    
             
             }else{
                 
             $product_details['product_priceValidUntil'] = get_the_modified_date("c"); 
             
             }       
             
             $product_details['product_currency'] = get_option( 'woocommerce_currency' );             
             
             $reviews_arr = array();
             $reviews     = get_approved_comments( $post_id );
                                       
             $judge_me_post = null;
             
             if(class_exists('saswp_reviews_platform_markup') && class_exists('JGM_ProductService')){
                 
                 $judge_me_post = get_posts( 
                        array(
                            'post_type' 	 => 'saswp_reviews',                                                                                   
                            'posts_per_page'     => -1,   
                            'post_status'        => 'publish',
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
                   
                    foreach($judge_me_post as $me_post){
                        
                        $rv = array();
                        
                        foreach($post_meta as $key => $val){
                  
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
                 
             }else if( $reviews && is_array($reviews) ){

              $sumofrating = 0;
              $avg_rating  = 1;
                                  
             foreach($reviews as $review){                 
                
                $rating = get_comment_meta( $review->comment_ID, 'rating', true ) ? get_comment_meta( $review->comment_ID, 'rating', true ) : '5';

                $sumofrating += $rating;
                
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
             
             if($product->get_review_count()){
                $product_details['product_review_count']   = $product->get_review_count();
             }else{
                $product_details['product_review_count']   = count($reviews);
             }

             if($product->get_average_rating()){
                $product_details['product_average_rating'] = $product->get_average_rating();             
             }else{
                $product_details['product_average_rating'] = $avg_rating;             
             }             
             
             }else{
                 
                 if(isset($sd_data['saswp_default_review']) && $sd_data['saswp_default_review'] == 1){
                 
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
             return $product_details;                       
        }
        
        
        public function saswp_rating_box_rating_markup($post_id){
            
                global $sd_data;

                $response               = array(); 
                $over_all               = '';
                $item_enable            = 0;
                $review_count           = "1";

                $rating_box   = get_post_meta($post_id, 'saswp_review_details', true); 

                if(isset($rating_box['saswp-review-item-over-all'])){

                    $over_all = $rating_box['saswp-review-item-over-all'];  

                }

                if(isset($rating_box['saswp-review-item-enable'])){

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
            
            $post_meta   = get_post_meta($post_id, $key='', true);                                       
            
            if(isset($post_meta['_post_review_box_breakdowns_score'])){
                
              if(function_exists('bcdiv')){
                  $rating_value = bcdiv($post_meta['_post_review_box_breakdowns_score'][0], 20, 2);        
              }  
                                          
            }
            if(isset($post_meta['_post_review_box_title'])){
              $post_review_title = $post_meta['_post_review_box_title'][0];     
            }
            if(isset($post_meta['_post_review_box_summary'])){
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
                
                foreach($answer_array as $answer){
                                       
                        $authorinfo = get_userdata($answer->post_author);  
                        
                        $suggested_answer[] =  array(
                            '@type'       => 'Answer',
                            'upvoteCount' => 1,
                            'url'         => get_permalink().'#post-'.$answer->ID,
                            'text'        => wp_strip_all_tags($answer->post_content),
                            'dateCreated' => get_the_date("Y-m-d\TH:i:s\Z", $answer),
                            'author'      => array('@type' => 'Person', 'name' => $authorinfo->data->user_nicename),
                        );
                        
                    
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
                 
                $post_meta      = get_post_meta($post_id, $key='', true);
                
                if(isset($post_meta['_dwqa_best_answer'])){
                    
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
                   $dw_qa['text'] = @get_the_content();
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
                
                foreach($answer_array as $answer){
                    
                    $authorinfo = get_userdata($answer->post_author);  
                    
                    if($answer->ID == $best_answer_id){
                        
                        $accepted_answer['@type']       = 'Answer';
                        $accepted_answer['upvoteCount'] = get_post_meta( $answer->ID, '_dwqa_votes', true );
                        $accepted_answer['url']         = get_permalink($answer->ID);
                        $accepted_answer['text']        = wp_strip_all_tags($answer->post_content);
                        $accepted_answer['dateCreated'] = get_the_date("Y-m-d\TH:i:s\Z", $answer);
                        $accepted_answer['author']      = array('@type' => 'Person', 'name' => $authorinfo->data->user_nicename);
                        
                    }else{
                        
                        $suggested_answer[] =  array(
                            '@type'       => 'Answer',
                            'upvoteCount' => get_post_meta( $answer->ID, '_dwqa_votes', true ),
                            'url'         => get_permalink($answer->ID),
                            'text'        => wp_strip_all_tags($answer->post_content),
                            'dateCreated' => get_the_date("Y-m-d\TH:i:s\Z", $answer),
                            'author'      => array('@type' => 'Person', 'name' => $authorinfo->data->user_nicename),
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
                        
            if($meta_field){
                                
                foreach ($meta_field as $field){
                
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
        public function saswp_schema_markup_generator($schema_type){
            
                        global $post, $sd_data;                                                
            
                        $logo         = ''; 
                        $height       = '';
                        $width        = '';
                        $site_name    = '';
                                                
                        $default_logo = $this->saswp_get_publisher(true);
                        
                        if(!empty($default_logo)){
            
                            $logo   = $default_logo['url'];
                            $height = $default_logo['height'];
                            $width  = $default_logo['width'];
            
                        }
                        
                        if(isset($sd_data['sd_name']) && $sd_data['sd_name'] !=''){
                            
                            $site_name = $sd_data['sd_name'];  
                          
                        }else{
                            
                            $site_name = get_bloginfo();    
                            
                        }
                        
                        $input1         = array();                                                                                    			                  			
                        $date 		    = get_the_date("c");
                        $modified_date 	= get_the_modified_date("c");                        
			                                                
            switch ($schema_type) {
                
                case 'TechArticle':
                    
                    $input1 = array(
					'@context'			=> saswp_context_url(),
					'@type'				=> 'TechArticle',
                                        '@id'				=> trailingslashit(saswp_get_permalink()).'#techarticle',
                                        'url'				=> saswp_get_permalink(),
                                        'inLanguage'                    => get_bloginfo('language'),
					'mainEntityOfPage'              => saswp_get_permalink(),					
					'headline'			=> saswp_get_the_title(),
					'description'                   => saswp_get_the_excerpt(),
                                        'articleBody'                   => saswp_get_the_content(),
                                        'keywords'                      => saswp_get_the_tags(),
					'datePublished'                 => esc_html($date),
					'dateModified'                  => esc_html($modified_date),
					'author'			=> saswp_get_author_details(),
					'publisher'			=> array(
						'@type'			=> 'Organization',
						'logo' 			=> array(
							'@type'		=> 'ImageObject',
							'url'		=> esc_url($logo),
							'width'		=> esc_attr($width),
							'height'	=> esc_attr($height),
							),
						'name'			=> esc_attr($site_name),
					),
                                    
				);

                    break;
                
                case 'Article':                   
                    $input1 = array(
					'@context'			=> saswp_context_url(),
					'@type'				=> 'Article',
                                        '@id'				=> trailingslashit(saswp_get_permalink()).'#article',
                                        'url'				=> saswp_get_permalink(),
                                        'inLanguage'                    => get_bloginfo('language'),
					'mainEntityOfPage'              => saswp_get_permalink(),					
					'headline'			=> saswp_get_the_title(),
					'description'                   => saswp_get_the_excerpt(),
                                        'articleBody'                   => saswp_get_the_content(),
                                        'keywords'                      => saswp_get_the_tags(),
					'datePublished'                 => esc_html($date),
					'dateModified'                  => esc_html($modified_date),
					'author'			=> saswp_get_author_details(),
					'publisher'			=> array(
						'@type'			=> 'Organization',
						'logo' 			=> array(
							'@type'		=> 'ImageObject',
							'url'		=> esc_url($logo),
							'width'		=> esc_attr($width),
							'height'	=> esc_attr($height),
							),
						'name'			=> esc_attr($site_name),
					),
                                    
				);

                    break;
                    case 'SpecialAnnouncement':                   
                        $input1 = array(
                        '@context'			=> saswp_context_url(),
                        '@type'				=> 'SpecialAnnouncement',
                        '@id'				=> trailingslashit(saswp_get_permalink()).'#SpecialAnnouncement',
                        'url'				=> saswp_get_permalink(),
                        'inLanguage'        => get_bloginfo('language'),                        
                        'name'			    => saswp_get_the_title(),                        
                        'text'                   => saswp_get_the_excerpt(),                                                                    
                        'keywords'                      => saswp_get_the_tags(),
                        'datePublished'                 => esc_html($date),
                        'datePosted'                    => esc_html($date),
                        'dateModified'                  => esc_html($modified_date),
                        'expires'                  => esc_html($modified_date),
                        'author'			=> saswp_get_author_details(),
                        'publisher'			=> array(
                            '@type'			=> 'Organization',
                            'logo' 			=> array(
                                '@type'		=> 'ImageObject',
                                'url'		=> esc_url($logo),
                                'width'		=> esc_attr($width),
                                'height'	=> esc_attr($height),
                                ),
                            'name'			=> esc_attr($site_name),
                        ),
                                        
                    );    
                        break;    
                
                case 'WebPage':
                    
                 $input1 = array(
				'@context'			=> saswp_context_url(),
				'@type'				=> 'WebPage' ,
                '@id'				=> trailingslashit(saswp_get_permalink()).'#webpage',
				'name'				=> saswp_get_the_title(),
                'url'				=> saswp_get_permalink(),
                'lastReviewed'      => esc_html($modified_date),
                'reviewedBy'        => array(
                    '@type'			=> 'Organization',
                    'logo' 			=> array(
                        '@type'		=> 'ImageObject',
                        'url'		=> esc_url($logo),
                        'width'		=> esc_attr($width),
                        'height'	=> esc_attr($height),
                        ),
                    'name'			=> esc_attr($site_name),
                ), 
                'inLanguage'                    => get_bloginfo('language'),
				'description'                   => saswp_get_the_excerpt(),
				'mainEntity'                    => array(
						'@type'			=> 'Article',
						'mainEntityOfPage'	=> saswp_get_permalink(),						
						'headline'		=> saswp_get_the_title(),
						'description'		=> saswp_get_the_excerpt(),
                                                'articleBody'           => saswp_get_the_content(),
                                                'keywords'              => saswp_get_the_tags(),
						'datePublished' 	=> esc_html($date),
						'dateModified'		=> esc_html($modified_date),
						'author'			=> saswp_get_author_details(),
						'publisher'			=> array(
							'@type'			=> 'Organization',
							'logo' 			=> array(
								'@type'		=> 'ImageObject',
								'url'		=> esc_url($logo),
								'width'		=> esc_attr($width),
								'height'	=> esc_attr($height),
								),
							'name'			=> esc_attr($site_name),
						),
                                               
					),
					
				
				);
                    
                    break;
                    
                case 'Product':
                case 'SoftwareApplication':
                case 'MobileApplication':
                case 'Book':
                                                                        
                        $product_details = $this->saswp_woocommerce_product_details(get_the_ID());  

                        if((isset($sd_data['saswp-woocommerce']) && $sd_data['saswp-woocommerce'] == 1) && !empty($product_details)){

                            $input1 = array(
                            '@context'			        => saswp_context_url(),
                            '@type'				=> $schema_type,
                            '@id'				=> trailingslashit(saswp_get_permalink()).'#'.$schema_type,     
                            'url'				=> trailingslashit(saswp_get_permalink()),
                            'name'                              => saswp_remove_warnings($product_details, 'product_name', 'saswp_string'),
                            'sku'                               => saswp_remove_warnings($product_details, 'product_sku', 'saswp_string'),    
                            'description'                       => saswp_remove_warnings($product_details, 'product_description', 'saswp_string')                                                               
                          );
                            
                          if(isset($product_details['product_price']) && $product_details['product_price'] ){
							
                                    $input1['offers'] = array(
                                                    '@type'	        => 'Offer',
                                                    'availability'      => saswp_remove_warnings($product_details, 'product_availability', 'saswp_string'),
                                                    'price'             => saswp_remove_warnings($product_details, 'product_price', 'saswp_string'),
                                                    'priceCurrency'     => saswp_remove_warnings($product_details, 'product_currency', 'saswp_string'),
                                                    'url'               => trailingslashit(saswp_get_permalink()),
                                                    'priceValidUntil'   => saswp_remove_warnings($product_details, 'product_priceValidUntil', 'saswp_string')
                                                 );

							
                            }else{

                            if(isset($product_details['product_varible_price']) && $product_details['product_varible_price']){

                            $input1['offers']['@type']         = 'AggregateOffer';
                            $input1['offers']['lowPrice']      = min($product_details['product_varible_price']);
                            $input1['offers']['highPrice']     = max($product_details['product_varible_price']);
                            $input1['offers']['priceCurrency'] = saswp_remove_warnings($product_details, 'product_currency', 'saswp_string');
                            $input1['offers']['availability']  = saswp_remove_warnings($product_details, 'product_availability', 'saswp_string');
                            $input1['offers']['offerCount']    = count($product_details['product_varible_price']);

                            }

                           }                              
                            
                          if(isset($product_details['product_gtin8']) && $product_details['product_gtin8'] !=''){
                            $input1['gtin8'] = esc_attr($product_details['product_gtin8']);  
                          }
                          if(isset($product_details['product_gtin13']) && $product_details['product_gtin13'] !=''){
                            $input1['gtin13'] = esc_attr($product_details['product_gtin13']);  
                          }
                          if(isset($product_details['product_mpn']) && $product_details['product_mpn'] !=''){
                            $input1['mpn'] = esc_attr($product_details['product_mpn']);  
                          }
                          if(isset($product_details['product_isbn']) && $product_details['product_isbn'] !=''){
                            $input1['isbn'] = esc_attr($product_details['product_isbn']);  
                          }
                          if(isset($product_details['product_brand']) && $product_details['product_brand'] !=''){
                            $input1['brand'] =  array('@type'=>'Thing','name'=> esc_attr($product_details['product_brand']));  
                          }                                     
                          if(isset($product_details['product_review_count']) && $product_details['product_review_count'] >0 && isset($product_details['product_average_rating']) && $product_details['product_average_rating'] >0){
                               $input1['aggregateRating'] =  array(
                                                                '@type'         => 'AggregateRating',
                                                                'ratingValue'	=> esc_attr($product_details['product_average_rating']),
                                                                'reviewCount'   => (int)esc_attr($product_details['product_review_count']),       
                               );
                          }                                      
                          if(!empty($product_details['product_reviews'])){

                              $reviews = array();

                              foreach ($product_details['product_reviews'] as $review){

                                  $reviews[] = array(
                                                                '@type'	=> 'Review',
                                                                'author'	=> $review['author'] ? esc_attr($review['author']) : 'Anonymous',
                                                                'datePublished'	=> esc_html($review['datePublished']),
                                                                'description'	=> $review['description'],  
                                                                'reviewRating'  => array(
                                                                        '@type'	=> 'Rating',
                                                                        'bestRating'	=> '5',
                                                                        'ratingValue'	=> $review['reviewRating'] ? esc_attr($review['reviewRating']) : '5',
                                                                        'worstRating'	=> '1',
                                                                )  
                                  );

                              }
                              $input1['review'] =  $reviews;
                          }                                                                                                    
                        }else{

                            $input1['@context']              = saswp_context_url();
                            $input1['@type']                 = $schema_type;
                            $input1['@id']                   = trailingslashit(saswp_get_permalink()).'#'.$schema_type;                                                                                                                                                                                                                                                                                        
                        } 
                        
                        if(!isset($input1['review'])){
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
        public function saswp_get_fetaure_image(){
            
            global $post, $sd_data, $saswp_featured_image;

            $input2          = array();             
            $multiple_size   = false;

            if( (isset($sd_data['saswp-multiple-size-image']) && $sd_data['saswp-multiple-size-image'] == 1) || !isset($sd_data['saswp-multiple-size-image'])){
                $multiple_size = true;
            }

            if(!$saswp_featured_image){
                $image_id 	            = get_post_thumbnail_id();
                $saswp_featured_image   = wp_get_attachment_image_src($image_id, 'full');            
            }
	        $image_details = $saswp_featured_image;                       
            if( is_array($image_details) && !empty($image_details)){                                
                                                                                                                    
                                        if( ( (isset($image_details[1]) && ($image_details[1] < 1200)) || (isset($image_details[2]) && ($image_details[2] < 675)) ) && function_exists('saswp_aq_resize')){
                                                
                                            $targetHeight = 1200;
                                            
                                            if( ($image_details[1] > 0) && ($image_details[2] > 0) ){                                            
                                                $img_ratio    = $image_details[1] / $image_details[2];
                                                $targetHeight = 1200 / $img_ratio;                                                
                                            }
                                            
                                            if($multiple_size){
                                                $width  = array(1200, 1200, 1200);
                                                $height = array($targetHeight, 900, 675);
                                            }else{
                                                $width  = array(1200);
                                                $height = array($targetHeight);
                                            }                                                                                        
                                            
                                            for($i = 0; $i < count($width); $i++){
                                                
                                                $resize_image = saswp_aq_resize( $image_details[0], $width[$i], $height[$i], true, false, true );
                                                
                                                if(isset($resize_image[0]) && $resize_image[0] !='' && isset($resize_image[1]) && isset($resize_image[2]) ){
                                                                                                                                                        
                                                    $input2['image'][$i]['@type']  = 'ImageObject';
                                                    
                                                    if($i == 0){                                                        
                                                        $input2['image'][$i]['@id']    = saswp_get_permalink().'#primaryimage';                                                        
                                                    }
                                                    
                                                    $input2['image'][$i]['url']    = esc_url($resize_image[0]);
                                                    $input2['image'][$i]['width']  = esc_attr($resize_image[1]);
                                                    $input2['image'][$i]['height'] = esc_attr($resize_image[2]);  
                                                    
                                                }                                                                                                                                                                                                
                                            }
                                            
                                            if(!empty($input2)){
                                                foreach($input2 as $arr){
                                                    $input2['image'] = array_values($arr);
                                                }
                                            }
                                                                                                                                                                                                                            
                                        }else{
                                                       
                                            if($multiple_size){
                                                $width  = array($image_details[1], 1200, 1200);
                                                $height = array($image_details[2], 900, 675);
                                            }else{
                                                $width  = array($image_details[1]);
                                                $height = array($image_details[2]);
                                            }  
                                                                                               
                                               for($i = 0; $i < count($width); $i++){
                                                    
                                                        $resize_image = saswp_aq_resize( $image_details[0], $width[$i], $height[$i], true, false, true );
													
                                                        if(isset($resize_image[0]) && $resize_image[0] != '' && isset($resize_image[1]) && isset($resize_image[2]) ){

                                                                $input2['image'][$i]['@type']  = 'ImageObject';
                                                                
                                                                if($i == 0){
                                                        
                                                                $input2['image'][$i]['@id']    = saswp_get_permalink().'#primaryimage'; 
                                                                
                                                                }
                                                                
                                                                $input2['image'][$i]['url']    = esc_url($resize_image[0]);
                                                                $input2['image'][$i]['width']  = esc_attr($resize_image[1]);
                                                                $input2['image'][$i]['height'] = esc_attr($resize_image[2]);

                                                        }
                                                                                                        
                                                }                                                                                                                                                                                        
                                            
                                        } 
                                        
                                        if(empty($input2) && isset($image_details[0]) && $image_details[0] !='' && isset($image_details[1]) && isset($image_details[2]) ){
                                            
                                                $input2['image']['@type']  = 'ImageObject';
                                                $input2['image']['@id']    = saswp_get_permalink().'#primaryimage';
                                                $input2['image']['url']    = esc_url($image_details[0]);
                                                $input2['image']['width']  = esc_attr($image_details[1]);
                                                $input2['image']['height'] = esc_attr($image_details[2]);
                                            
                                        }
                                                                                                                                                                                                                                         
                             }
                                                       
                          //Get All the images available on post   
                           
                          if( (isset($sd_data['saswp-other-images']) && $sd_data['saswp-other-images'] == 1) || !isset($sd_data['saswp-other-images']) ){
                          
                          $content = @get_the_content();   
                          
                          if($content){
                              
                          $regex   = '/<img(.*?)src="(.*?)"(.*?)>/';                          
                          @preg_match_all( $regex, $content, $attachments ); 
                                                                                                                                                                                      
                          $attach_images = array();
                          
                          if(!empty($attachments)){
                              
                              $attach_details   = saswp_get_attachment_details($attachments[2], $post->ID);
                              
                              $k = 0;
                              
                              foreach ($attachments[2] as $attachment) {
                                                                                                                                       
                                  if(is_array($attach_details) && !empty($attach_details)){
                                                                            
                                                if($attachment !=''){
                                                    $attach_images['image'][$k]['@type']  = 'ImageObject';                                                
                                                    $attach_images['image'][$k]['url']    = esc_url($attachment);
                                                    $attach_images['image'][$k]['width']  = isset($attach_details[$k][0]) ? $attach_details[$k][0] : 0;
                                                    $attach_images['image'][$k]['height'] = isset($attach_details[$k][1]) ? $attach_details[$k][1] : 0;
                                                }
                                                                                      
                                  }
                                  
                                  $k++;
                              }
                              
                          }
                          
                          if(!empty($attach_images) && is_array($attach_images)){
                                                            
                              if(isset($input2['image'])){
                                                                
                                   $featured_image = $input2['image'];
                                   $content_images = $attach_images['image'];
                                  
                                   if($featured_image && $content_images){
                                       $input2['image'] = array_merge($featured_image, $content_images);
                                   }
                                                                                                                                   
                              }else{
                                  
                                  if($attach_images &&  isset($attach_images['image'])){
                                      
                                      foreach($attach_images['image'] as $key => $image){
                                               
                                          if($key == 0){
                                              
                                            if($image['width'] < 1200){
                                                
                                                $resized_image = saswp_aq_resize( $image['url'], 1200, 675, true, false, true );                                                                                                
                                                
                                                if(is_array($resized_image) && !empty($resized_image)){
                                                    
                                                    if(isset($resized_image[0]) && $resized_image[0] !=''){
                                                        $attach_images['image'][$key]['url']    =   $resized_image[0];
                                                        $attach_images['image'][$key]['width']  =   $resized_image[1];
                                                        $attach_images['image'][$key]['height'] =   $resized_image[2];                                                
                                                    }
                                                                                                        
                                                }
                                                
                                            }                                             
                                            $attach_images['image'][$key]['@id']    =   saswp_get_permalink().'#primaryimage';                                            
                                          }                                                                                         
                                      }
                                      
                                  }  
                                  
                                  $input2 = $attach_images;
                              }
                                                            
                          }
                          
                          }
                              
                          }   
                          
                          if(empty($input2)){
                              
                            if(isset($sd_data['sd_default_image']['url']) && $sd_data['sd_default_image']['url'] !=''){
                                        
                                    $input2['image']['@type']  = 'ImageObject';
                                    $input2['image']['@id']    = saswp_get_permalink().'#primaryimage';
                                    $input2['image']['url']    = esc_url($sd_data['sd_default_image']['url']);
                                    $input2['image']['width']  = esc_attr($sd_data['sd_default_image_width']);
                                    $input2['image']['height'] = esc_attr($sd_data['sd_default_image_height']);                                                                 
                                            
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
                                if(isset($img_details[0])){
                                    $img_details = @saswp_aq_resize( $img_details[0], 600, 60, true, false, true );
                                }
                                
                                $saswp_custom_logo =  $img_details;                              
                            }   
                                                     
                            $custom_logo = $saswp_custom_logo;                                                                               
                            if(isset($custom_logo) && is_array($custom_logo)){
                                
                                $logo           = array_key_exists(0, $custom_logo)? $custom_logo[0]:'';                                
                                $width          = array_key_exists(1, $custom_logo)? $custom_logo[1]:'';
                                $height         = array_key_exists(2, $custom_logo)? $custom_logo[2]:'';
                            
                            }
                                                        
                        }                            
                        
                        if($site_name){
                                                    
                            $publisher['publisher']['@type']         = 'Organization';
                            $publisher['publisher']['name']          = esc_attr($site_name);                            
                            
                            if($logo !='' && $height !='' && $width !=''){
                                                                             
                            $publisher['publisher']['logo']['@type'] = 'ImageObject';
                            $publisher['publisher']['logo']['url']   = esc_url($logo);
                            $publisher['publisher']['logo']['width'] = esc_attr($width);
                            $publisher['publisher']['logo']['height']= esc_attr($height);                        
                             
                            $default_logo['url']    = esc_url($logo);
                            $default_logo['height'] = esc_attr($height);
                            $default_logo['width']  = esc_attr($width);
                            
                          }
                                                        
                        }
                                                                          
                        if($d_logo){
                            return $default_logo;
                        }else{
                            return $publisher;
                        }                        
                        
        }
                
}
if (class_exists('saswp_output_service')) {
	$object = new saswp_output_service();
        $object->saswp_service_hooks();
};