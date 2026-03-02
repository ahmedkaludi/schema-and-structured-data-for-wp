<?php 
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
        /**
         * Function to get the fields of a particular schema type as an array
         * @global type $post
         * @global type $sd_data
         * @param type $schema_id
         * @return array
         * @since version 1.0.4
         */
function saswp_get_fields_by_schema_type( $schema_id = null, $condition = null, $review_type = null, $manual = null ) {  
            
            global $post;
            global $sd_data;  
            
            $business_type = $current_user = $author_desc = $author_url = $post_id = '';
            $author_details     = array();

            if($schema_id !=null ){
                $schema_id = intval($schema_id);
            }
            
            if($review_type){
                $schema_type = $review_type;
            }else{
                $schema_type        = get_post_meta($schema_id, 'schema_type', true); 
            }
            
            if($manual == null){
            
                if(is_object($post) ) {
                        $post_id = $post->ID; 
                }
                $current_user       = null;

                if( function_exists('wp_get_current_user') ){
                        // $current_user       = wp_get_current_user();
                        $aid = get_the_author_meta('ID');
                        $a_display_name = get_the_author_meta('display_name');
                        $current_user['ID'] = $aid;
                        $current_user['display_name'] = $a_display_name;
                        $current_user = wp_json_encode($current_user);
                        $current_user = json_decode($current_user);
                }
                
                $author_desc        = get_the_author_meta( 'user_description' );
                $author_url         = get_the_author_meta( 'user_url' ); 

                $author_id = get_post_field( 'post_author', get_the_ID() );
                if(empty($author_desc) ) {
                    $author_desc = get_the_author_meta('user_description',$author_id);
                }
                if(empty($author_url) ) {
                    $author_url = get_the_author_meta('user_url',$author_id);
                }               

                if ( function_exists( 'get_avatar_data') && is_object($current_user) &&  ! empty( get_option( 'show_avatars' ) ) ) {
                    $author_details	= get_avatar_data($current_user->ID);           
                }

                $business_type      = get_post_meta($schema_id, 'saswp_business_type', true);             
                $business_name      = get_post_meta($schema_id, 'saswp_business_name', true); 
                $saswp_business_type_key   = 'saswp_business_type_'.$schema_id;
                $saved_business_type       = get_post_meta( $post_id, $saswp_business_type_key, true );
                $saved_saswp_business_name = get_post_meta( $post_id, 'saswp_business_name_'.$schema_id, true );    

                if($saved_business_type){
                  $business_type = $saved_business_type;
                }
                if($saved_saswp_business_name){
                  $business_name = $saved_saswp_business_name;
                }
                
            }
            
            $meta_field = array();
            
            switch ($schema_type) {
                
                case 'local_business':
                    
                    $sub_business_options = array();     
                                        
                     $mappings_local_sub = SASWP_DIR_NAME . '/core/array-list/local-sub-business.php';
                     $local_sub_business = include $mappings_local_sub;
                    
                    if($condition !=null){
                                                
                        if ( ! empty( $local_sub_business) && array_key_exists($business_type, $local_sub_business) ) {
                        
                        $sub_business_options = array(
                             'label'     => esc_html__( 'Sub Business Type', 'schema-and-structured-data-for-wp' ),
                             'id'        => 'saswp_business_name_'.$schema_id,
                             'type'      => 'select',
                             'options'   => $local_sub_business[$business_type],
                             'default'   => $business_name  
                        ); 

                    }
                        
                        
                    }else{
                        
                       if ( ! empty( $local_sub_business) && array_key_exists($business_type, $local_sub_business) ) {
                        
                       $sub_business_options = array(
                            'label'     => esc_html__( 'Sub Business Type', 'schema-and-structured-data-for-wp' ),
                            'id'        => 'saswp_business_name_'.$schema_id,
                            'type'      => 'select',
                            'options'   => $local_sub_business[$business_type],
                            'default'   => $business_name  
                       ); 
                       
                    }
                        
                    }
                                        
                    $meta_field[] = array(
                            'label'   => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'local_business_id_'.$schema_id,
                            'type'    => 'text',
                            'default' => 'LocalBusiness'                            
                      
                    );
                    
                    if($manual == null){
                        
                        $meta_field[] = array(
                            'label'   => esc_html__( 'Business Type', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_business_type_'.$schema_id,
                            'type'    => 'select',
                            'default' => $business_type,
                            'options' => $local_sub_business['all_business_type']
                        );
                        $meta_field[] = $sub_business_options;
                        
                    }
                                        
                        $meta_field[] = array(
                            'label'   => esc_html__( 'Business Name', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'local_business_name_'.$schema_id,
                            'type'    => 'text', 
                                                        
                        );
                    
                        $meta_field[] = array(
                            'label'    => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'local_business_name_url_'.$schema_id,
                            'type'    => 'text',
                            'default' => get_permalink()                            
                        );
                    
                        $meta_field[] = array(
                           'label' => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                            'id' => 'local_business_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => is_object($post) ? $post->post_excerpt : ''                            
                        );                                            
                        $meta_field[] = array(
                            'label' => esc_html__( 'Street Address', 'schema-and-structured-data-for-wp' ),
                            'id' => 'local_street_address_'.$schema_id,
                            'type' => 'text',                                   
                        );
                       
                        $meta_field[] = array(
                            'label' => esc_html__( 'City', 'schema-and-structured-data-for-wp' ),
                            'id' => 'local_city_'.$schema_id,
                            'type' => 'text',                        
                        );
                       
                        $meta_field[] = array(
                             'label' => esc_html__( 'State', 'schema-and-structured-data-for-wp' ),
                            'id' => 'local_state_'.$schema_id,
                            'type' => 'text',                      
                        );
                        
                        $meta_field[] = array(
                            'label' => esc_html__( 'Country', 'schema-and-structured-data-for-wp' ),
                            'id' => 'local_country_'.$schema_id,
                            'type' => 'text',                                   
                        );

                        $meta_field[] = array(
                              'label' => esc_html__( 'Postal Code', 'schema-and-structured-data-for-wp' ),
                            'id' => 'local_postal_code_'.$schema_id,
                            'type' => 'text',                     
                        );
                        
                        $meta_field[] = array(
                            'label' => esc_html__( 'Latitude', 'schema-and-structured-data-for-wp' ),
                            'id' => 'local_latitude_'.$schema_id,
                            'type' => 'text',                         
                        );
                        
                        $meta_field[] = array(
                                'label' => esc_html__( 'Longitude', 'schema-and-structured-data-for-wp' ),
                            'id' => 'local_longitude_'.$schema_id,
                            'type' => 'text',                         
                        );
                        
                        $meta_field[] = array(
                              'label' => esc_html__( 'Phone', 'schema-and-structured-data-for-wp' ),
                            'id' => 'local_phone_'.$schema_id,
                            'type' => 'text',                     
                        );
                        
                         $meta_field[] = array(
                              'label' => esc_html__( 'Website', 'schema-and-structured-data-for-wp' ),
                            'id' => 'local_website_'.$schema_id,
                            'type' => 'text',                      
                        );
                        
                        $meta_field[] = array(
                             'label' => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                            'id' => 'local_business_logo_'.$schema_id,
                            'type' => 'media',                      
                        );
                        
                        $meta_field[] = array(
                             'label' => esc_html__( 'Operation Days', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_dayofweek_'.$schema_id,
                            'type' => 'textarea',
                            'attributes' => array(
                                'placeholder' => 'Mo-Sa 11:00-14:30&#10;Mo-Th 17:00-21:30&#10;Fr-Sa 17:00-22:00'
                            ),
                            'note' => 'Note: Enter one operation days per line without comma.'                   
                        );
                        
                        $meta_field[] = array(
                              'label' => esc_html__( 'Area Served', 'schema-and-structured-data-for-wp' ),
                            'id'    => 'local_area_served_'.$schema_id,
                            'type'  => 'textarea',
                            'attributes' => array(
                                'placeholder' => 'Example, Example2'
                            ),
                            'note' => 'Note: Separate area served list by comma ( , )'                     
                        );                        
                        $meta_field[] = array(
                               'label' => esc_html__( 'Service Offered Name', 'schema-and-structured-data-for-wp' ),
                               'id' => 'local_service_offered_name_'.$schema_id,
                               'type' => 'text',                            
                        );
                        $meta_field[] = array(
                                'label' => esc_html__( 'Service Offered URL', 'schema-and-structured-data-for-wp' ),
                                'id' => 'local_service_offered_url_'.$schema_id,
                                'type' => 'text',                            
                        );                           
                        $meta_field[] = array(
                             'label' => esc_html__( 'Price Range', 'schema-and-structured-data-for-wp' ),
                            'id' => 'local_price_range_'.$schema_id,
                            'type' => 'text',                            
                        );                       
                        $meta_field[] = array(
                            'label' => esc_html__( 'Menu', 'schema-and-structured-data-for-wp' ),
                            'id' => 'local_menu_'.$schema_id,
                            'type' => 'text',                            
                         );
                        $meta_field[] =  array(
                            'label' => esc_html__( 'HasMap', 'schema-and-structured-data-for-wp' ),
                            'id' => 'local_hasmap_'.$schema_id,
                            'type' => 'text',                            
                         );
                        $meta_field[] =   array(
                            'label' => esc_html__( 'Serves Cuisine', 'schema-and-structured-data-for-wp' ),
                            'id' => 'local_serves_cuisine_'.$schema_id,
                            'type' => 'text',                            
                         );
                        $meta_field[] =   array(
                                'label' => esc_html__( 'Additional Type', 'schema-and-structured-data-for-wp' ),
                                'id'    => 'local_additional_type_'.$schema_id,
                                'type'  => 'text',                            
                        );
                        $meta_field[] = array(
                                'label'   => esc_html__( 'Founder', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'local_business_founder_'.$schema_id,
                                'type'    => 'textarea',  
                                'note'    => 'Note: If There are more than one founder, Separate founder list by comma ( , )'                                 
                       );
                       $meta_field[] = array(
                               'label'   => esc_html__( 'Employee', 'schema-and-structured-data-for-wp' ),
                               'id'      => 'local_business_employee_'.$schema_id,
                               'type'    => 'textarea',
                               'note'    => 'Note: If There are more than one employee. Separate employee list by comma ( , )'                                   
                       );  
                       $meta_field[] = array(
                               'label'   => esc_html__( 'Hospital Affiliation Name', 'schema-and-structured-data-for-wp' ),
                               'id'      => 'local_business_han_'.$schema_id,
                               'type'    => 'text',                                   
                       );                                                
                       $meta_field[] = array(
                               'label'   => esc_html__( 'Hospital Affiliation URL', 'schema-and-structured-data-for-wp' ),
                               'id'      => 'local_business_hau_'.$schema_id,
                               'type'    => 'text',                                   
                       );  
                       $meta_field[] = array(
                               'label'   => esc_html__( 'Medical Specialty', 'schema-and-structured-data-for-wp' ),
                               'id'      => 'local_business_medical_speciality_'.$schema_id,
                               'type'    => 'text',   
                               'note'    => 'Note: If There are more than one medical speciality. Separate medical speciality list by comma ( , )',                        
                       );                                               
                       $meta_field[] = array(
                               'label'   => esc_html__( 'Occupational Category', 'schema-and-structured-data-for-wp' ),
                               'id'      => 'local_business_occupational_category_'.$schema_id,
                               'type'    => 'text',                        
                       );   
                       $meta_field[] = array(
                               'label'   => esc_html__( 'USNPI', 'schema-and-structured-data-for-wp' ),
                               'id'      => 'local_business_usnpi_'.$schema_id,
                               'type'    => 'text',                        
                       );                                                
                        $meta_field[] =   array(
                            'label' => esc_html__( 'Facebook', 'schema-and-structured-data-for-wp' ),
                            'id' => 'local_facebook_'.$schema_id,
                            'type' => 'text',                            
                         );
                        $meta_field[] =  array(
                            'label' => esc_html__( 'Twitter', 'schema-and-structured-data-for-wp' ),
                            'id' => 'local_twitter_'.$schema_id,
                            'type' => 'text',                            
                        );
                        $meta_field[] =   array(
                            'label' => esc_html__( 'Instagram', 'schema-and-structured-data-for-wp' ),
                            'id' => 'local_instagram_'.$schema_id,
                            'type' => 'text',                            
                        );
                        $meta_field[] =   array(
                            'label' => esc_html__( 'Pinterest', 'schema-and-structured-data-for-wp' ),
                            'id' => 'local_pinterest_'.$schema_id,
                            'type' => 'text',                            
                        );
                        $meta_field[] =   array(
                            'label' => esc_html__( 'Linkedin', 'schema-and-structured-data-for-wp' ),
                            'id' => 'local_linkedin_'.$schema_id,
                            'type' => 'text',                            
                        );
                        $meta_field[] =  array(
                            'label' => esc_html__( 'Soundcloud', 'schema-and-structured-data-for-wp' ),
                            'id' => 'local_soundcloud_'.$schema_id,
                            'type' => 'text',                            
                        );
                        $meta_field[] =   array(
                            'label' => esc_html__( 'Tumblr', 'schema-and-structured-data-for-wp' ),
                            'id' => 'local_tumblr_'.$schema_id,
                            'type' => 'text',                            
                        );
                        $meta_field[] =  array(
                            'label' => esc_html__( 'Youtube', 'schema-and-structured-data-for-wp' ),
                            'id' => 'local_youtube_'.$schema_id,
                            'type' => 'text',                            
                        );
                        $meta_field[] =  array(
                            'label' => esc_html__( 'Threads', 'schema-and-structured-data-for-wp' ),
                            'id' => 'local_threads_'.$schema_id,
                            'type' => 'text',                            
                        );
                        $meta_field[] =  array(
                            'label' => esc_html__( 'Mastodon', 'schema-and-structured-data-for-wp' ),
                            'id' => 'local_mastodon_'.$schema_id,
                            'type' => 'text',                            
                        );
                        $meta_field[] =  array(
                            'label' => esc_html__( 'Vibehut', 'schema-and-structured-data-for-wp' ),
                            'id' => 'local_vibehut_'.$schema_id,
                            'type' => 'text',                            
                        );                                                                                                                        
                        $meta_field[] =   array(
                            'label' => esc_html__( 'Aggregate Rating', 'schema-and-structured-data-for-wp' ),
                            'id' => 'local_enable_rating_'.$schema_id,
                            'type' => 'checkbox',                          
                        );
                        
                        $meta_field = apply_filters('saswp_modify_local_business_properties', $meta_field, $schema_id);
                        
                        $meta_field[] =   array(
                            'label' => esc_html__( 'Rating', 'schema-and-structured-data-for-wp' ),
                            'id' => 'local_rating_'.$schema_id,
                            'type' => 'text',                            
                        );
                        $meta_field[] =   array(
                            'label' => esc_html__( 'Number of Reviews', 'schema-and-structured-data-for-wp' ),
                            'id' => 'local_review_count_'.$schema_id,
                            'type' => 'text',                            
                        );
                        $meta_field[] =   array(
                            'label' => esc_html__( 'Check-in Time', 'schema-and-structured-data-for-wp' ),
                            'id' => 'local_checkin_time_'.$schema_id,
                            'type' => 'text',                            
                        );
                        $meta_field[] =   array(
                            'label' => esc_html__( 'Check-out Time', 'schema-and-structured-data-for-wp' ),
                            'id' => 'local_checkout_time_'.$schema_id,
                            'type' => 'text',                            
                        );
                        $meta_field[] =   array(
                            'label' => esc_html__( 'Identifier', 'schema-and-structured-data-for-wp' ),
                            'id' => 'local_identifier_pvalue_'.$schema_id,
                            'type' => 'text',                            
                        );
                        $meta_field[] =   array(
                            'label' => esc_html__( 'MakesOffer', 'schema-and-structured-data-for-wp' ),
                            'id' => 'local_makes_offer_'.$schema_id,
                            'type' => 'text',                            
                            'is_template_attr' => 'yes',                            
                        );
                                           
                    break;
                
                case 'Blogposting':
                case 'BlogPosting':        
                    $meta_field = array(
                        array(
                        'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_blogposting_id_'.$schema_id,
                        'type'       => 'text',
                        'default'    => 'BlogPosting'   
                        ),
                    array(
                        'label' => esc_html__( 'Main Entity Of Page', 'schema-and-structured-data-for-wp' ),
                        'id' => 'saswp_blogposting_main_entity_of_page_'.$schema_id,
                        'type' => 'text',
                        'default' => get_permalink()
                    ),
                    array(
                        'label'   => esc_html__( 'inLanguage', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_blogposting_inlanguage_'.$schema_id,
                        'type'    => 'text',
                        'default' => get_bloginfo('language'),
                   ),
                    array(
                            'label' => esc_html__( 'Headline', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_blogposting_headline_'.$schema_id,
                            'type' => 'text',
                            'default'=> saswp_get_the_title()
                    ),
                    array(
                            'label' => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_blogposting_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => saswp_strip_all_tags(get_the_excerpt())
                    ),                        
                    array(
                        'label'   => esc_html__( 'Article Body', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_blogposting_body_'.$schema_id,
                        'type'    => 'textarea',
                        'default' => is_object($post) ? saswp_strip_all_tags($post->post_content) : ''
                    ),
                    array(
                            'label' => esc_html__( 'Keywords', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_blogposting_keywords_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_get_the_tags()
                    ),    
                    array(
                            'label' => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_blogposting_name_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_get_the_title()
                    ), 
                    array(
                            'label' => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_blogposting_url_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink()
                    ),
                    array(
                        'label'   => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_blogposting_image_'.$schema_id,
                        'type'    => 'media'                        
                    ),
                    array(
                            'label' => esc_html__( 'Date Published', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_blogposting_date_published_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_date("Y-m-d")
                    ), 
                    array(
                            'label' => esc_html__( 'Date Modified', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_blogposting_date_modified_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_modified_date("Y-m-d")
                    ),     
                    array(
                        'label'   => esc_html__( 'Author', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_blogposting_author_global_mapping_'.$schema_id,
                        'type'    => 'global_mapping'
                    ),
                    array(
                        'label'   => esc_html__( 'Author Type', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_blogposting_author_type_'.$schema_id,
                        'type'    => 'select',
                        'options' => array(
                                'Person'           => 'Person',
                                'Organization'     => 'Organization',                        
                       )                        
                   ),
                    array(
                            'label' => esc_html__( 'Author Name', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_blogposting_author_name_'.$schema_id,
                            'type' => 'text',
                            'default' => is_object($current_user) ? $current_user->display_name : ''
                    ),
                    array(
                        'label'   => esc_html__( 'Author HonorificSuffix', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_blogposting_author_honorific_suffix_'.$schema_id,
                        'type'    => 'text',
                        'attributes' => array(
                                'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                         ),
                    ),
                    array(
                            'label' => esc_html__( 'Author Description', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_blogposting_author_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => $author_desc
                    ),
                    array(
                            'label'   => esc_html__( 'Author URL', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_blogposting_author_url_'.$schema_id,
                            'type'    => 'text',
                            'default' => $author_url
                    ),  
                    array(
                        'label' => esc_html__( 'Author Image URL', 'schema-and-structured-data-for-wp' ),
                        'id' => 'saswp_blogposting_author_image_'.$schema_id,
                        'type' => 'media',
                        'default' => isset($author_details['url']) ? $author_details['url']: ''
                    ),
                    array(
                            'label' => esc_html__( 'Author Social Profile', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_blogposting_author_social_profile_'.$schema_id,
                            'type' => 'textarea',
                            'default' => '',
                            'note'    => 'Note: If There are more than one social profiles, Separate them by comma ( , )',
                    ),
                    array(
                        'label'   => esc_html__( 'JobTitle', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_blogposting_author_jobtitle_'.$schema_id,
                        'type'    => 'text',
                        'default' => '',
                        'attributes' => array(
                                'placeholder' => 'eg: Editor in Chief'
                         ),
                     ),
                     array(
                        'label'   => esc_html__( 'ReviewedBy', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_blogposting_reviewedby_global_mapping_'.$schema_id,
                        'type'    => 'global_mapping'
                    ),
                    array(
                        'label'   => esc_html__( 'ReviewedBy Type', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_blogposting_reviewedby_type_'.$schema_id,
                        'type'    => 'select',
                        'options' => array(
                                ''                 => 'Select',
                                'Person'           => 'Person',
                                'Organization'     => 'Organization',                        
                       )                        
                    ),
                    array(
                            'label' => esc_html__( 'ReviewedBy Name', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_blogposting_reviewedby_name_'.$schema_id,
                            'type' => 'text',
                            'default' => is_object($current_user) ? $current_user->display_name : ''
                    ),
                    array(
                        'label'   => esc_html__( 'ReviewedBy HonorificSuffix', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_blogposting_reviewedby_honorific_suffix_'.$schema_id,
                        'type'    => 'text',
                        'attributes' => array(
                                'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                         ),
                    ),
                    array(
                        'label' => esc_html__( 'ReviewedBy Description', 'schema-and-structured-data-for-wp' ),
                        'id' => 'saswp_blogposting_reviewedby_description_'.$schema_id,
                        'type' => 'textarea',
                        'default' => $author_desc
                    ),
                    array(
                        'label'   => esc_html__( 'ReviewedBy URL', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_blogposting_reviewedby_url_'.$schema_id,
                        'type'    => 'text',
                        'default' => $author_url
                    ),

                    array(
                        'label'   => esc_html__( 'Editor Type', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_blogposting_editor_type_'.$schema_id,
                        'type'    => 'select',
                        'options' => array(
                                ""                => "Select",
                                'Person'           => 'Person',
                                'Organization'     => 'Organization',                        
                        )
                    ),
                    array(
                        'label'   => esc_html__( 'Editor Name', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_blogposting_editor_name_'.$schema_id,
                        'type'    => 'text',
                        'default' => is_object($current_user) ? $current_user->display_name : ''
                    ),  
                    array(
                        'label'   => esc_html__( 'Editor HonorificSuffix', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_blogposting_editor_honorific_suffix_'.$schema_id,
                        'type'    => 'text',
                        'attributes' => array(
                                'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                         ),
                    ),
                    array(
                        'label'   => esc_html__( 'Editor Description', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_blogposting_editor_description_'.$schema_id,
                        'type'    => 'textarea',
                        'default' => $author_desc
                    ),
                    array(
                        'label'   => esc_html__( 'Editor URL', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_blogposting_editor_url_'.$schema_id,
                        'type'    => 'text',
                        'default' => $author_url
                    ),
                    array(
                        'label' => esc_html__( 'Editor Image URL', 'schema-and-structured-data-for-wp' ),
                        'id' => 'saswp_blogposting_editor_image_'.$schema_id,
                        'type' => 'media',
                        'default' => isset($author_details['url']) ? $author_details['url']: ''
                    ),


                    array(
                            'label' => esc_html__( 'Organization Name', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_blogposting_organization_name_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                   ),
                     array(
                            'label' => esc_html__( 'Organization Name', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_blogposting_organization_logo_'.$schema_id,
                            'type' => 'media',
                            'default' => isset($sd_data['sd_logo']) ? $sd_data['sd_logo']['url'] : ''
                    ),
                    array(
                        'label'   => esc_html__( 'About', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_blogposting_about_'.$schema_id,
                        'type'    => 'textarea',
                        'default' => '',
                        'note'    => 'Note: If There are more than one About, Separate About list by comma ( , )',
                        'attributes' => array(
                                'placeholder' => 'eg: Apple is March 21 Announcements'
                        ),
                    ),  
                    array(
                        'label'   => esc_html__( 'AlumniOf', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_blogposting_alumniof_'.$schema_id,
                        'type'    => 'textarea',
                        'default' => '',
                        'note'    => 'Note: If There are more than one AlumniOf, Separate AlumniOf list by comma ( , )',
                        'attributes' => array(
                                'placeholder' => 'eg: City University of New York-Herbert H. Lehman College, Southern New Hampshire University'
                        ),
                    ),  
                    array(
                        'label'   => esc_html__( 'knowsAbout', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_blogposting_knowsabout_'.$schema_id,
                        'type'    => 'textarea',
                        'default' => '',
                        'note'    => 'Note: If There are more than one knows about, Separate knows about list by comma ( , )'       
                    ),
                    array(
                        'label' => esc_html__( 'Speakable', 'schema-and-structured-data-for-wp' ),
                        'id' => 'saswp_blogposting_speakable_'.$schema_id,
                        'type' => 'checkbox',

                    ),
                    array(
                        'label'   => esc_html__( 'Citation', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_blogposting_citation_'.$schema_id,
                        'type'    => 'text',
                        'is_template_attr' => 'yes',
                     )                        
                    );
                    break;
                
                case 'NewsArticle':
                    
                    $category_detail=get_the_category(get_the_ID());//$post->ID
                    $article_section = '';
                    
                    foreach( $category_detail as $cd){
                        
                    $article_section =  $cd->cat_name;
                    
                    }
                    $word_count = saswp_reading_time_and_word_count();
                    
                    $meta_field = array(
                        array(
                                'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_newsarticle_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'newsarticle'   
                        ),  
                    array(
                            'label' => esc_html__( 'Main Entity Of Page', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_newsarticle_main_entity_of_page_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink()
                    ),
                    array(
                            'label' => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_newsarticle_URL_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink(),
                    ),
                    array(
                            'label' => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_newsarticle_image_'.$schema_id,
                            'type' => 'media',                            
                    ),    
                    array(
                        'label'   => esc_html__( 'inLanguage', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_newsarticle_inlanguage_'.$schema_id,
                        'type'    => 'text',
                        'default' => get_bloginfo('language'),
                    ),
                    array(
                            'label' => esc_html__( 'Headline', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_newsarticle_headline_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_get_the_title(),
                    ),
                    array(
                        'label'   => esc_html__( 'Alternative Headline', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_newsarticle_alternative_headline_'.$schema_id,
                        'type'    => 'text',
                        'default' => saswp_get_the_title(),
                    ),
                    array(
                            'label' => esc_html__( 'Date Published', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_newsarticle_date_published_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_date("Y-m-d")
                    ),
                    array(
                            'label' => esc_html__( 'Date Modified', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_newsarticle_date_modified_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_modified_date("Y-m-d")
                    ),
                    array(
                            'label'      => esc_html__( 'Date Created', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_newsarticle_date_created_'.$schema_id,
                            'type'       => 'text',
                            'default'    => get_the_date("Y-m-d")
                    ),
                     array(
                            'label' => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_newsarticle_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => saswp_strip_all_tags(get_the_excerpt())
                    ),
                    array(
                            'label'   => esc_html__( 'hasPart', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_newsarticle_haspart_'.$schema_id,
                            'type'    => 'text',
                            'is_template_attr' => 'yes',
                    ),
                    array(
                            'label'   => esc_html__( 'isPartOf', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_newsarticle_ispartof_'.$schema_id,
                            'type'    => 'text',
                            'is_template_attr' => 'yes',
                    ),
                    array(
                            'label' => esc_html__( 'Keywords', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_newsarticle_keywords_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_get_the_tags()
                    ),    
                     array(
                            'label' => esc_html__( 'Article Section', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_newsarticle_section_'.$schema_id,
                            'type' => 'text',
                            'default' => $article_section
                    ),
                    array(
                            'label' => esc_html__( 'Article Body', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_newsarticle_body_'.$schema_id,
                            'type' => 'textarea',
                            'default' => is_object($post) ? saswp_strip_all_tags($post->post_content) : ''
                    ),
                     array(
                            'label' => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_newsarticle_name_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_get_the_title()
                    ), 
                     array(
                            'label' => esc_html__( 'Thumbnail URL', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_newsarticle_thumbnailurl_'.$schema_id,
                            'type' => 'text'                            
                    ),
                    array(
                            'label' => esc_html__( 'Word Count', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_newsarticle_word_count_'.$schema_id,
                            'type' => 'text',
                            'default' => $word_count['word_count']
                    ),
                    array(
                            'label' => esc_html__( 'Time Required', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_newsarticle_timerequired_'.$schema_id,
                            'type' => 'text',
                            'default' => $word_count['timerequired']
                    ),    
                    array(
                            'label' => esc_html__( 'Main Entity Id', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_newsarticle_main_entity_id_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink()
                    ),
                    array(
                        'label'   => esc_html__( 'Author Type', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_newsarticle_author_type_'.$schema_id,
                        'type'    => 'select',
                        'options' => array(
                                'Person'           => 'Person',
                                'Organization'     => 'Organization',                        
                       )
                    ),
                    array(
                            'label' => esc_html__( 'Author Name', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_newsarticle_author_name_'.$schema_id,
                            'type' => 'text',
                            'default' => is_object($current_user) ?  $current_user->display_name : ''
                    ),
                    array(
                            'label' => esc_html__( 'Author Description', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_newsarticle_author_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => $author_desc
                    ), 
                    array(
                            'label'   => esc_html__( 'Author URL', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_newsarticle_author_url_'.$schema_id,
                            'type'    => 'text',
                            'default' => $author_url
                    ),    
                    array(
                            'label' => esc_html__( 'Author Image', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_newsarticle_author_image_'.$schema_id,
                            'type' => 'media',
                            'default' => isset($author_details['url']) ? $author_details['url']: ''
                    ),
                    array(
                            'label' => esc_html__( 'Author Social Profile', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_newsarticle_author_social_profile_'.$schema_id,
                            'type' => 'textarea',
                            'default' => '',
                            'note'    => 'Note: If There are more than one social profiles, Separate them by comma ( , )',
                    ),

                    array(
                        'label'   => esc_html__( 'Editor Type', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_newsarticle_editor_type_'.$schema_id,
                        'type'    => 'select',
                        'options' => array(
                                ""                => "Select",
                                'Person'           => 'Person',
                                'Organization'     => 'Organization',                        
                        )
                    ),
                    array(
                        'label'   => esc_html__( 'Editor Name', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_newsarticle_editor_name_'.$schema_id,
                        'type'    => 'text',
                        'default' => is_object($current_user) ? $current_user->display_name : ''
                    ),
                    array(
                        'label'   => esc_html__( 'Editor HonorificSuffix', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_newsarticle_editor_honorific_suffix_'.$schema_id,
                        'type'    => 'text',
                        'attributes' => array(
                                'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                         ),
                    ), 
                    array(
                        'label'   => esc_html__( 'Editor Description', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_newsarticle_editor_description_'.$schema_id,
                        'type'    => 'textarea',
                        'default' => $author_desc
                    ),
                    array(
                        'label'   => esc_html__( 'Editor URL', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_newsarticle_editor_url_'.$schema_id,
                        'type'    => 'text',
                        'default' => $author_url
                    ),
                    array(
                        'label' => esc_html__( 'Editor Image URL', 'schema-and-structured-data-for-wp' ),
                        'id' => 'saswp_newsarticle_editor_image_'.$schema_id,
                        'type' => 'media',
                        'default' => isset($author_details['url']) ? $author_details['url']: ''
                    ),
                    array(
                        'label'   => esc_html__( 'About', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_newsarticle_about_'.$schema_id,
                        'type'    => 'textarea',
                        'default' => '',
                        'note'    => 'Note: If There are more than one About, Separate About list by comma ( , )',
                        'attributes' => array(
                                'placeholder' => 'eg: Apple is March 21 Announcements'
                        ),
                    ), 
                    array(
                            'label' => esc_html__( 'Organization Name', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_newsarticle_organization_name_'.$schema_id,
                            'type' => 'text',
                            'default'=> saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                    ),
                    array(
                            'label' => esc_html__( 'Organization Logo', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_newsarticle_organization_logo_'.$schema_id,
                            'type' => 'media',
                            'default' => isset($sd_data['sd_logo'])? $sd_data['sd_logo']['url']:''
                    ),                         
                    array(
                        'label' => esc_html__( 'Speakable', 'schema-and-structured-data-for-wp' ),
                        'id' => 'saswp_newsarticle_speakable_'.$schema_id,
                        'type' => 'checkbox',

                    ),
                    array(
                        'label'      => esc_html__( 'Publisher', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_newsarticle_publisher_'.$schema_id,
                        'type'       => 'text',
                        'is_template_attr' => 'yes',
                    ),
                    array(
                            'label' => esc_html__( 'Associated Media', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_newsarticle_associated_image_'.$schema_id,
                            'type' => 'media',                            
                    ),
                    array(
                        'label'   => esc_html__( 'Content Location Name', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_newsarticle_content_location_name_'.$schema_id,
                        'type'    => 'text'                        
                    ),                   
                    array(
                        'label'   => esc_html__( 'Content Location Locality', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_newsarticle_content_location_locality_'.$schema_id,
                        'type'    => 'text'                        
                    ),
                    array(
                        'label'   => esc_html__( 'Content Location Region', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_newsarticle_content_location_region_'.$schema_id,
                        'type'    => 'text'                        
                    ),
                    array(
                        'label'   => esc_html__( 'Content Location Country', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_newsarticle_content_location_country_'.$schema_id,
                        'type'    => 'text'                        
                    ),
                    array(
                        'label'   => esc_html__( 'Content Location Postal Code', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_newsarticle_content_location_postal_code_'.$schema_id,
                        'type'    => 'text'                        
                    ),                        
                    );
                    break;

                    case 'AnalysisNewsArticle':
                    
                        $category_detail=get_the_category(get_the_ID());//$post->ID
                        $article_section = '';
                        
                        foreach( $category_detail as $cd){
                            
                        $article_section =  $cd->cat_name;
                        
                        }
                        $word_count = saswp_reading_time_and_word_count();
                        
                        $meta_field = array(
                            array(
                                    'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                    'id'         => 'saswp_analysisnewsarticle_id_'.$schema_id,
                                    'type'       => 'text',
                                    'default'    => 'analysisnewsarticle'   
                            ),  
                        array(
                                'label' => esc_html__( 'Main Entity Of Page', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_analysisnewsarticle_main_entity_of_page_'.$schema_id,
                                'type' => 'text',
                                'default' => get_permalink()
                        ),
                        array(
                                'label' => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_analysisnewsarticle_URL_'.$schema_id,
                                'type' => 'text',
                                'default' => get_permalink(),
                        ),
                        array(
                                'label' => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_analysisnewsarticle_image_'.$schema_id,
                                'type' => 'media',                            
                        ),    
                        array(
                            'label'   => esc_html__( 'inLanguage', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_analysisnewsarticle_inlanguage_'.$schema_id,
                            'type'    => 'text',
                            'default' => get_bloginfo('language'),
                        ),
                        array(
                                'label' => esc_html__( 'Headline', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_analysisnewsarticle_headline_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_title(),
                        ),
                        array(
                                'label' => esc_html__( 'Date Published', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_analysisnewsarticle_date_published_'.$schema_id,
                                'type' => 'text',
                                'default' => get_the_date("Y-m-d")
                        ),
                        array(
                                'label' => esc_html__( 'Date Modified', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_analysisnewsarticle_date_modified_'.$schema_id,
                                'type' => 'text',
                                'default' => get_the_modified_date("Y-m-d")
                        ),
                         array(
                                'label' => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_analysisnewsarticle_description_'.$schema_id,
                                'type' => 'textarea',
                                'default' => saswp_strip_all_tags(get_the_excerpt())
                        ),
                        array(
                                'label'   => esc_html__( 'hasPart', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_analysisnewsarticle_haspart_'.$schema_id,
                                'type'    => 'text',
                                'is_template_attr' => 'yes',
                        ),
                        array(
                                'label'   => esc_html__( 'isPartOf', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_analysisnewsarticle_ispartof_'.$schema_id,
                                'type'    => 'text',
                                'is_template_attr' => 'yes',
                        ), 
                        array(
                                'label' => esc_html__( 'Keywords', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_analysisnewsarticle_keywords_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_tags()
                        ),    
                         array(
                                'label' => esc_html__( 'Article Section', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_analysisnewsarticle_section_'.$schema_id,
                                'type' => 'text',
                                'default' => $article_section
                        ),
                        array(
                                'label' => esc_html__( 'Article Body', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_analysisnewsarticle_body_'.$schema_id,
                                'type' => 'textarea',
                                'default' => is_object($post) ? saswp_strip_all_tags($post->post_content) : ''
                        ),
                         array(
                                'label' => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_analysisnewsarticle_name_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_title()
                        ), 
                         array(
                                'label' => esc_html__( 'Thumbnail URL', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_analysisnewsarticle_thumbnailurl_'.$schema_id,
                                'type' => 'text'                            
                        ),
                        array(
                                'label' => esc_html__( 'Word Count', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_analysisnewsarticle_word_count_'.$schema_id,
                                'type' => 'text',
                                'default' => $word_count['word_count']
                        ),
                        array(
                                'label' => esc_html__( 'Time Required', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_analysisnewsarticle_timerequired_'.$schema_id,
                                'type' => 'text',
                                'default' => $word_count['timerequired']
                        ),    
                        array(
                                'label' => esc_html__( 'Main Entity Id', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_analysisnewsarticle_main_entity_id_'.$schema_id,
                                'type' => 'text',
                                'default' => get_permalink()
                        ),
                        array(
                            'label'   => esc_html__( 'Author Type', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_analysisnewsarticle_author_type_'.$schema_id,
                            'type'    => 'select',
                            'options' => array(
                                    'Person'           => 'Person',
                                    'Organization'     => 'Organization',                        
                           )
                        ),
                        array(
                                'label' => esc_html__( 'Author Name', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_analysisnewsarticle_author_name_'.$schema_id,
                                'type' => 'text',
                                'default' => is_object($current_user) ?  $current_user->display_name : ''
                        ),
                        array(
                                'label' => esc_html__( 'Author Description', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_analysisnewsarticle_author_description_'.$schema_id,
                                'type' => 'textarea',
                                'default' => $author_desc
                        ), 
                        array(
                                'label'   => esc_html__( 'Author URL', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_analysisnewsarticle_author_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => $author_url
                        ),    
                        array(
                                'label' => esc_html__( 'Author Image', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_analysisnewsarticle_author_image_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($author_details['url']) ? $author_details['url']: ''
                        ),
    
                        array(
                            'label'   => esc_html__( 'Editor Type', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_analysisnewsarticle_editor_type_'.$schema_id,
                            'type'    => 'select',
                            'options' => array(
                                    ""                => "Select",
                                    'Person'           => 'Person',
                                    'Organization'     => 'Organization',                        
                            )
                        ),
                        array(
                            'label'   => esc_html__( 'Editor Name', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_analysisnewsarticle_editor_name_'.$schema_id,
                            'type'    => 'text',
                            'default' => is_object($current_user) ? $current_user->display_name : ''
                        ),
                        array(
                            'label'   => esc_html__( 'Editor HonorificSuffix', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_analysisnewsarticle_editor_honorific_suffix_'.$schema_id,
                            'type'    => 'text',
                            'attributes' => array(
                                    'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                             ),
                        ), 
                        array(
                            'label'   => esc_html__( 'Editor Description', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_analysisnewsarticle_editor_description_'.$schema_id,
                            'type'    => 'textarea',
                            'default' => $author_desc
                        ),
                        array(
                            'label'   => esc_html__( 'Editor URL', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_analysisnewsarticle_editor_url_'.$schema_id,
                            'type'    => 'text',
                            'default' => $author_url
                        ),
                        array(
                            'label' => esc_html__( 'Editor Image URL', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_analysisnewsarticle_editor_image_'.$schema_id,
                            'type' => 'media',
                            'default' => isset($author_details['url']) ? $author_details['url']: ''
                        ),
                        array(
                            'label'   => esc_html__( 'About', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_analysisnewsarticle_about_'.$schema_id,
                            'type'    => 'textarea',
                            'default' => '',
                            'note'    => 'Note: If There are more than one About, Separate About list by comma ( , )',
                            'attributes' => array(
                                    'placeholder' => 'eg: Apple is March 21 Announcements'
                            ),
                        ), 
                        array(
                                'label' => esc_html__( 'Organization Name', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_analysisnewsarticle_organization_name_'.$schema_id,
                                'type' => 'text',
                                'default'=> saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                        ),
                        array(
                                'label' => esc_html__( 'Organization Logo', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_analysisnewsarticle_organization_logo_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($sd_data['sd_logo'])? $sd_data['sd_logo']['url']:''
                        ),                         
                        array(
                            'label' => esc_html__( 'Speakable', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_analysisnewsarticle_speakable_'.$schema_id,
                            'type' => 'checkbox',
    
                        )                        
                        );
                        break;

                        case 'AskPublicNewsArticle':
                    
                        $category_detail=get_the_category(get_the_ID());//$post->ID
                        $article_section = '';
                        
                        foreach( $category_detail as $cd){
                                
                        $article_section =  $cd->cat_name;
                        
                        }
                        $word_count = saswp_reading_time_and_word_count();
                        
                        $meta_field = array(
                                array(
                                        'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_askpublicnewsarticle_id_'.$schema_id,
                                        'type'       => 'text',
                                        'default'    => 'askpublicnewsarticle'   
                                ),  
                        array(
                                'label' => esc_html__( 'Main Entity Of Page', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_askpublicnewsarticle_main_entity_of_page_'.$schema_id,
                                'type' => 'text',
                                'default' => get_permalink()
                        ),
                        array(
                                'label' => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_askpublicnewsarticle_URL_'.$schema_id,
                                'type' => 'text',
                                'default' => get_permalink(),
                        ),
                        array(
                                'label' => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_askpublicnewsarticle_image_'.$schema_id,
                                'type' => 'media',                            
                        ),    
                        array(
                                'label'   => esc_html__( 'inLanguage', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_askpublicnewsarticle_inlanguage_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_bloginfo('language'),
                        ),
                        array(
                                'label' => esc_html__( 'Headline', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_askpublicnewsarticle_headline_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_title(),
                        ),
                        array(
                                'label' => esc_html__( 'Date Published', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_askpublicnewsarticle_date_published_'.$schema_id,
                                'type' => 'text',
                                'default' => get_the_date("Y-m-d")
                        ),
                        array(
                                'label' => esc_html__( 'Date Modified', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_askpublicnewsarticle_date_modified_'.$schema_id,
                                'type' => 'text',
                                'default' => get_the_modified_date("Y-m-d")
                        ),
                                array(
                                'label' => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_askpublicnewsarticle_description_'.$schema_id,
                                'type' => 'textarea',
                                'default' => saswp_strip_all_tags(get_the_excerpt())
                        ),
                        array(
                                'label'   => esc_html__( 'hasPart', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_askpublicnewsarticle_haspart_'.$schema_id,
                                'type'    => 'text',
                                'is_template_attr' => 'yes',
                        ),
                        array(
                                'label'   => esc_html__( 'isPartOf', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_askpublicnewsarticle_ispartof_'.$schema_id,
                                'type'    => 'text',
                                'is_template_attr' => 'yes',
                        ),
                        array(
                                'label' => esc_html__( 'Keywords', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_askpublicnewsarticle_keywords_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_tags()
                        ),    
                                array(
                                'label' => esc_html__( 'Article Section', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_askpublicnewsarticle_section_'.$schema_id,
                                'type' => 'text',
                                'default' => $article_section
                        ),
                        array(
                                'label' => esc_html__( 'Article Body', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_askpublicnewsarticle_body_'.$schema_id,
                                'type' => 'textarea',
                                'default' => is_object($post) ? saswp_strip_all_tags($post->post_content) : ''
                        ),
                                array(
                                'label' => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_askpublicnewsarticle_name_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_title()
                        ), 
                                array(
                                'label' => esc_html__( 'Thumbnail URL', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_askpublicnewsarticle_thumbnailurl_'.$schema_id,
                                'type' => 'text'                            
                        ),
                        array(
                                'label' => esc_html__( 'Word Count', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_askpublicnewsarticle_word_count_'.$schema_id,
                                'type' => 'text',
                                'default' => $word_count['word_count']
                        ),
                        array(
                                'label' => esc_html__( 'Time Required', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_askpublicnewsarticle_timerequired_'.$schema_id,
                                'type' => 'text',
                                'default' => $word_count['timerequired']
                        ),    
                        array(
                                'label' => esc_html__( 'Main Entity Id', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_askpublicnewsarticle_main_entity_id_'.$schema_id,
                                'type' => 'text',
                                'default' => get_permalink()
                        ),
                        array(
                                'label'   => esc_html__( 'Author Type', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_askpublicnewsarticle_author_type_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                        'Person'           => 'Person',
                                        'Organization'     => 'Organization',                        
                                )
                        ),
                        array(
                                'label' => esc_html__( 'Author Name', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_askpublicnewsarticle_author_name_'.$schema_id,
                                'type' => 'text',
                                'default' => is_object($current_user) ?  $current_user->display_name : ''
                        ),
                        array(
                                'label' => esc_html__( 'Author Description', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_askpublicnewsarticle_author_description_'.$schema_id,
                                'type' => 'textarea',
                                'default' => $author_desc
                        ), 
                        array(
                                'label'   => esc_html__( 'Author URL', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_askpublicnewsarticle_author_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => $author_url
                        ),    
                        array(
                                'label' => esc_html__( 'Author Image', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_askpublicnewsarticle_author_image_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($author_details['url']) ? $author_details['url']: ''
                        ),
        
                        array(
                                'label'   => esc_html__( 'Editor Type', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_askpublicnewsarticle_editor_type_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                        ""                => "Select",
                                        'Person'           => 'Person',
                                        'Organization'     => 'Organization',                        
                                )
                        ),
                        array(
                                'label'   => esc_html__( 'Editor Name', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_askpublicnewsarticle_editor_name_'.$schema_id,
                                'type'    => 'text',
                                'default' => is_object($current_user) ? $current_user->display_name : ''
                        ),
                        array(
                                'label'   => esc_html__( 'Editor HonorificSuffix', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_askpublicnewsarticle_editor_honorific_suffix_'.$schema_id,
                                'type'    => 'text',
                                'attributes' => array(
                                        'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                                ),
                        ), 
                        array(
                                'label'   => esc_html__( 'Editor Description', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_askpublicnewsarticle_editor_description_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => $author_desc
                        ),
                        array(
                                'label'   => esc_html__( 'Editor URL', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_askpublicnewsarticle_editor_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => $author_url
                        ),
                        array(
                                'label' => esc_html__( 'Editor Image URL', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_askpublicnewsarticle_editor_image_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($author_details['url']) ? $author_details['url']: ''
                        ),
                        array(
                                'label'   => esc_html__( 'About', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_askpublicnewsarticle_about_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => '',
                                'note'    => 'Note: If There are more than one About, Separate About list by comma ( , )',
                                'attributes' => array(
                                        'placeholder' => 'eg: Apple is March 21 Announcements'
                                ),
                        ), 
                        array(
                                'label' => esc_html__( 'Organization Name', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_askpublicnewsarticle_organization_name_'.$schema_id,
                                'type' => 'text',
                                'default'=> saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                        ),
                        array(
                                'label' => esc_html__( 'Organization Logo', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_askpublicnewsarticle_organization_logo_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($sd_data['sd_logo'])? $sd_data['sd_logo']['url']:''
                        ),                         
                        array(
                                'label' => esc_html__( 'Speakable', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_askpublicnewsarticle_speakable_'.$schema_id,
                                'type' => 'checkbox',
        
                        )                        
                        );
                        break;

                        case 'BackgroundNewsArticle':
                
                        $category_detail=get_the_category(get_the_ID());//$post->ID
                        $article_section = '';
                        
                        foreach( $category_detail as $cd){
                                
                        $article_section =  $cd->cat_name;
                        
                        }
                        $word_count = saswp_reading_time_and_word_count();
                        
                        $meta_field = array(
                                array(
                                        'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_backgroundnewsarticle_id_'.$schema_id,
                                        'type'       => 'text',
                                        'default'    => 'backgroundnewsarticle'   
                                ),  
                        array(
                                'label' => esc_html__( 'Main Entity Of Page', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_backgroundnewsarticle_main_entity_of_page_'.$schema_id,
                                'type' => 'text',
                                'default' => get_permalink()
                        ),
                        array(
                                'label' => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_backgroundnewsarticle_URL_'.$schema_id,
                                'type' => 'text',
                                'default' => get_permalink(),
                        ),
                        array(
                                'label' => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_backgroundnewsarticle_image_'.$schema_id,
                                'type' => 'media',                            
                        ),    
                        array(
                                'label'   => esc_html__( 'inLanguage', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_backgroundnewsarticle_inlanguage_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_bloginfo('language'),
                        ),
                        array(
                                'label' => esc_html__( 'Headline', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_backgroundnewsarticle_headline_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_title(),
                        ),
                        array(
                                'label' => esc_html__( 'Date Published', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_backgroundnewsarticle_date_published_'.$schema_id,
                                'type' => 'text',
                                'default' => get_the_date("Y-m-d")
                        ),
                        array(
                                'label' => esc_html__( 'Date Modified', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_backgroundnewsarticle_date_modified_'.$schema_id,
                                'type' => 'text',
                                'default' => get_the_modified_date("Y-m-d")
                        ),
                                array(
                                'label' => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_backgroundnewsarticle_description_'.$schema_id,
                                'type' => 'textarea',
                                'default' => saswp_strip_all_tags(get_the_excerpt())
                        ),
                        array(
                                'label'   => esc_html__( 'hasPart', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_backgroundnewsarticle_haspart_'.$schema_id,
                                'type'    => 'text',
                                'is_template_attr' => 'yes',
                        ),
                        array(
                                'label'   => esc_html__( 'isPartOf', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_backgroundnewsarticle_ispartof_'.$schema_id,
                                'type'    => 'text',
                                'is_template_attr' => 'yes',
                        ),
                        array(
                                'label' => esc_html__( 'Keywords', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_backgroundnewsarticle_keywords_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_tags()
                        ),    
                                array(
                                'label' => esc_html__( 'Article Section', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_backgroundnewsarticle_section_'.$schema_id,
                                'type' => 'text',
                                'default' => $article_section
                        ),
                        array(
                                'label' => esc_html__( 'Article Body', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_backgroundnewsarticle_body_'.$schema_id,
                                'type' => 'textarea',
                                'default' => is_object($post) ? saswp_strip_all_tags($post->post_content) : ''
                        ),
                                array(
                                'label' => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_backgroundnewsarticle_name_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_title()
                        ), 
                                array(
                                'label' => esc_html__( 'Thumbnail URL', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_backgroundnewsarticle_thumbnailurl_'.$schema_id,
                                'type' => 'text'                            
                        ),
                        array(
                                'label' => esc_html__( 'Word Count', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_backgroundnewsarticle_word_count_'.$schema_id,
                                'type' => 'text',
                                'default' => $word_count['word_count']
                        ),
                        array(
                                'label' => esc_html__( 'Time Required', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_backgroundnewsarticle_timerequired_'.$schema_id,
                                'type' => 'text',
                                'default' => $word_count['timerequired']
                        ),    
                        array(
                                'label' => esc_html__( 'Main Entity Id', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_backgroundnewsarticle_main_entity_id_'.$schema_id,
                                'type' => 'text',
                                'default' => get_permalink()
                        ),
                        array(
                                'label'   => esc_html__( 'Author Type', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_backgroundnewsarticle_author_type_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                        'Person'           => 'Person',
                                        'Organization'     => 'Organization',                        
                                )
                        ),
                        array(
                                'label' => esc_html__( 'Author Name', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_backgroundnewsarticle_author_name_'.$schema_id,
                                'type' => 'text',
                                'default' => is_object($current_user) ?  $current_user->display_name : ''
                        ),
                        array(
                                'label' => esc_html__( 'Author Description', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_backgroundnewsarticle_author_description_'.$schema_id,
                                'type' => 'textarea',
                                'default' => $author_desc
                        ), 
                        array(
                                'label'   => esc_html__( 'Author URL', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_backgroundnewsarticle_author_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => $author_url
                        ),    
                        array(
                                'label' => esc_html__( 'Author Image', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_backgroundnewsarticle_author_image_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($author_details['url']) ? $author_details['url']: ''
                        ),
        
                        array(
                                'label'   => esc_html__( 'Editor Type', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_backgroundnewsarticle_editor_type_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                        ""                => "Select",
                                        'Person'           => 'Person',
                                        'Organization'     => 'Organization',                        
                                )
                        ),
                        array(
                                'label'   => esc_html__( 'Editor Name', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_backgroundnewsarticle_editor_name_'.$schema_id,
                                'type'    => 'text',
                                'default' => is_object($current_user) ? $current_user->display_name : ''
                        ),
                        array(
                                'label'   => esc_html__( 'Editor HonorificSuffix', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_backgroundnewsarticle_editor_honorific_suffix_'.$schema_id,
                                'type'    => 'text',
                                'attributes' => array(
                                        'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                                ),
                        ), 
                        array(
                                'label'   => esc_html__( 'Editor Description', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_backgroundnewsarticle_editor_description_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => $author_desc
                        ),
                        array(
                                'label'   => esc_html__( 'Editor URL', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_backgroundnewsarticle_editor_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => $author_url
                        ),
                        array(
                                'label' => esc_html__( 'Editor Image URL', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_backgroundnewsarticle_editor_image_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($author_details['url']) ? $author_details['url']: ''
                        ),
                        array(
                                'label'   => esc_html__( 'About', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_backgroundnewsarticle_about_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => '',
                                'note'    => 'Note: If There are more than one About, Separate About list by comma ( , )',
                                'attributes' => array(
                                        'placeholder' => 'eg: Apple is March 21 Announcements'
                                ),
                        ), 
                        array(
                                'label' => esc_html__( 'Organization Name', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_backgroundnewsarticle_organization_name_'.$schema_id,
                                'type' => 'text',
                                'default'=> saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                        ),
                        array(
                                'label' => esc_html__( 'Organization Logo', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_backgroundnewsarticle_organization_logo_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($sd_data['sd_logo'])? $sd_data['sd_logo']['url']:''
                        ),                         
                        array(
                                'label' => esc_html__( 'Speakable', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_backgroundnewsarticle_speakable_'.$schema_id,
                                'type' => 'checkbox',
        
                        )                        
                        );
                        break;

                        case 'OpinionNewsArticle':
                
                        $category_detail=get_the_category(get_the_ID());//$post->ID
                        $article_section = '';
                        
                        foreach( $category_detail as $cd){
                                
                        $article_section =  $cd->cat_name;
                        
                        }
                        $word_count = saswp_reading_time_and_word_count();
                        
                        $meta_field = array(
                                array(
                                        'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_opinionnewsarticle_id_'.$schema_id,
                                        'type'       => 'text',
                                        'default'    => 'opinionnewsarticle'   
                                ),  
                        array(
                                'label' => esc_html__( 'Main Entity Of Page', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_opinionnewsarticle_main_entity_of_page_'.$schema_id,
                                'type' => 'text',
                                'default' => get_permalink()
                        ),
                        array(
                                'label' => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_opinionnewsarticle_URL_'.$schema_id,
                                'type' => 'text',
                                'default' => get_permalink(),
                        ),
                        array(
                                'label' => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_opinionnewsarticle_image_'.$schema_id,
                                'type' => 'media',                            
                        ),    
                        array(
                                'label'   => esc_html__( 'inLanguage', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_opinionnewsarticle_inlanguage_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_bloginfo('language'),
                        ),
                        array(
                                'label' => esc_html__( 'Headline', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_opinionnewsarticle_headline_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_title(),
                        ),
                        array(
                                'label' => esc_html__( 'Date Published', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_opinionnewsarticle_date_published_'.$schema_id,
                                'type' => 'text',
                                'default' => get_the_date("Y-m-d")
                        ),
                        array(
                                'label' => esc_html__( 'Date Modified', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_opinionnewsarticle_date_modified_'.$schema_id,
                                'type' => 'text',
                                'default' => get_the_modified_date("Y-m-d")
                        ),
                                array(
                                'label' => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_opinionnewsarticle_description_'.$schema_id,
                                'type' => 'textarea',
                                'default' => saswp_strip_all_tags(get_the_excerpt())
                        ),
                        array(
                                'label'   => esc_html__( 'hasPart', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_opinionnewsarticle_haspart_'.$schema_id,
                                'type'    => 'text',
                                'is_template_attr' => 'yes',
                        ),
                        array(
                                'label'   => esc_html__( 'isPartOf', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_opinionnewsarticle_ispartof_'.$schema_id,
                                'type'    => 'text',
                                'is_template_attr' => 'yes',
                        ),
                        array(
                                'label' => esc_html__( 'Keywords', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_opinionnewsarticle_keywords_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_tags()
                        ),    
                                array(
                                'label' => esc_html__( 'Article Section', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_opinionnewsarticle_section_'.$schema_id,
                                'type' => 'text',
                                'default' => $article_section
                        ),
                        array(
                                'label' => esc_html__( 'Article Body', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_opinionnewsarticle_body_'.$schema_id,
                                'type' => 'textarea',
                                'default' => is_object($post) ? saswp_strip_all_tags($post->post_content) : ''
                        ),
                                array(
                                'label' => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_opinionnewsarticle_name_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_title()
                        ), 
                                array(
                                'label' => esc_html__( 'Thumbnail URL', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_opinionnewsarticle_thumbnailurl_'.$schema_id,
                                'type' => 'text'                            
                        ),
                        array(
                                'label' => esc_html__( 'Word Count', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_opinionnewsarticle_word_count_'.$schema_id,
                                'type' => 'text',
                                'default' => $word_count['word_count']
                        ),
                        array(
                                'label' => esc_html__( 'Time Required', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_opinionnewsarticle_timerequired_'.$schema_id,
                                'type' => 'text',
                                'default' => $word_count['timerequired']
                        ),    
                        array(
                                'label' => esc_html__( 'Main Entity Id', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_opinionnewsarticle_main_entity_id_'.$schema_id,
                                'type' => 'text',
                                'default' => get_permalink()
                        ),
                        array(
                                'label'   => esc_html__( 'Author Type', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_opinionnewsarticle_author_type_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                        'Person'           => 'Person',
                                        'Organization'     => 'Organization',                        
                                )
                        ),
                        array(
                                'label' => esc_html__( 'Author Name', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_opinionnewsarticle_author_name_'.$schema_id,
                                'type' => 'text',
                                'default' => is_object($current_user) ?  $current_user->display_name : ''
                        ),
                        array(
                                'label' => esc_html__( 'Author Description', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_opinionnewsarticle_author_description_'.$schema_id,
                                'type' => 'textarea',
                                'default' => $author_desc
                        ), 
                        array(
                                'label'   => esc_html__( 'Author URL', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_opinionnewsarticle_author_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => $author_url
                        ),    
                        array(
                                'label' => esc_html__( 'Author Image', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_opinionnewsarticle_author_image_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($author_details['url']) ? $author_details['url']: ''
                        ),
        
                        array(
                                'label'   => esc_html__( 'Editor Type', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_opinionnewsarticle_editor_type_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                        ""                => "Select",
                                        'Person'           => 'Person',
                                        'Organization'     => 'Organization',                        
                                )
                        ),
                        array(
                                'label'   => esc_html__( 'Editor Name', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_opinionnewsarticle_editor_name_'.$schema_id,
                                'type'    => 'text',
                                'default' => is_object($current_user) ? $current_user->display_name : ''
                        ),
                        array(
                                'label'   => esc_html__( 'Editor HonorificSuffix', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_opinionnewsarticle_editor_honorific_suffix_'.$schema_id,
                                'type'    => 'text',
                                'attributes' => array(
                                        'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                                ),
                        ), 
                        array(
                                'label'   => esc_html__( 'Editor Description', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_opinionnewsarticle_editor_description_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => $author_desc
                        ),
                        array(
                                'label'   => esc_html__( 'Editor URL', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_opinionnewsarticle_editor_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => $author_url
                        ),
                        array(
                                'label' => esc_html__( 'Editor Image URL', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_opinionnewsarticle_editor_image_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($author_details['url']) ? $author_details['url']: ''
                        ),
                        array(
                                'label'   => esc_html__( 'About', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_opinionnewsarticle_about_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => '',
                                'note'    => 'Note: If There are more than one About, Separate About list by comma ( , )',
                                'attributes' => array(
                                        'placeholder' => 'eg: Apple is March 21 Announcements'
                                ),
                        ), 
                        array(
                                'label' => esc_html__( 'Organization Name', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_opinionnewsarticle_organization_name_'.$schema_id,
                                'type' => 'text',
                                'default'=> saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                        ),
                        array(
                                'label' => esc_html__( 'Organization Logo', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_opinionnewsarticle_organization_logo_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($sd_data['sd_logo'])? $sd_data['sd_logo']['url']:''
                        ),                         
                        array(
                                'label' => esc_html__( 'Speakable', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_opinionnewsarticle_speakable_'.$schema_id,
                                'type' => 'checkbox',
        
                        )                        
                        );
                        break;

                        case 'ReportageNewsArticle':
                
                        $category_detail=get_the_category(get_the_ID());//$post->ID
                        $article_section = '';
                        
                        foreach( $category_detail as $cd){
                                
                        $article_section =  $cd->cat_name;
                        
                        }
                        $word_count = saswp_reading_time_and_word_count();
                        
                        $meta_field = array(
                                array(
                                        'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_reportagenewsarticle_id_'.$schema_id,
                                        'type'       => 'text',
                                        'default'    => 'reportagenewsarticle'   
                                ),  
                        array(
                                'label' => esc_html__( 'Main Entity Of Page', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_reportagenewsarticle_main_entity_of_page_'.$schema_id,
                                'type' => 'text',
                                'default' => get_permalink()
                        ),
                        array(
                                'label' => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_reportagenewsarticle_URL_'.$schema_id,
                                'type' => 'text',
                                'default' => get_permalink(),
                        ),
                        array(
                                'label' => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_reportagenewsarticle_image_'.$schema_id,
                                'type' => 'media',                            
                        ),    
                        array(
                                'label'   => esc_html__( 'inLanguage', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_reportagenewsarticle_inlanguage_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_bloginfo('language'),
                        ),
                        array(
                                'label' => esc_html__( 'Headline', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_reportagenewsarticle_headline_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_title(),
                        ),
                        array(
                                'label' => esc_html__( 'Date Published', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_reportagenewsarticle_date_published_'.$schema_id,
                                'type' => 'text',
                                'default' => get_the_date("Y-m-d")
                        ),
                        array(
                                'label' => esc_html__( 'Date Modified', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_reportagenewsarticle_date_modified_'.$schema_id,
                                'type' => 'text',
                                'default' => get_the_modified_date("Y-m-d")
                        ),
                                array(
                                'label' => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_reportagenewsarticle_description_'.$schema_id,
                                'type' => 'textarea',
                                'default' => saswp_strip_all_tags(get_the_excerpt())
                        ),
                        array(
                                'label'   => esc_html__( 'hasPart', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_reportagenewsarticle_haspart_'.$schema_id,
                                'type'    => 'text',
                                'is_template_attr' => 'yes',
                        ),
                        array(
                                'label'   => esc_html__( 'isPartOf', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_reportagenewsarticle_ispartof_'.$schema_id,
                                'type'    => 'text',
                                'is_template_attr' => 'yes',
                        ),
                        array(
                                'label' => esc_html__( 'Keywords', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_reportagenewsarticle_keywords_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_tags()
                        ),    
                                array(
                                'label' => esc_html__( 'Article Section', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_reportagenewsarticle_section_'.$schema_id,
                                'type' => 'text',
                                'default' => $article_section
                        ),
                        array(
                                'label' => esc_html__( 'Article Body', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_reportagenewsarticle_body_'.$schema_id,
                                'type' => 'textarea',
                                'default' => is_object($post) ? saswp_strip_all_tags($post->post_content) : ''
                        ),
                                array(
                                'label' => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_reportagenewsarticle_name_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_title()
                        ), 
                                array(
                                'label' => esc_html__( 'Thumbnail URL', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_reportagenewsarticle_thumbnailurl_'.$schema_id,
                                'type' => 'text'                            
                        ),
                        array(
                                'label' => esc_html__( 'Word Count', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_reportagenewsarticle_word_count_'.$schema_id,
                                'type' => 'text',
                                'default' => $word_count['word_count']
                        ),
                        array(
                                'label' => esc_html__( 'Time Required', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_reportagenewsarticle_timerequired_'.$schema_id,
                                'type' => 'text',
                                'default' => $word_count['timerequired']
                        ),    
                        array(
                                'label' => esc_html__( 'Main Entity Id', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_reportagenewsarticle_main_entity_id_'.$schema_id,
                                'type' => 'text',
                                'default' => get_permalink()
                        ),
                        array(
                                'label'   => esc_html__( 'Author Type', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_reportagenewsarticle_author_type_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                        'Person'           => 'Person',
                                        'Organization'     => 'Organization',                        
                                )
                        ),
                        array(
                                'label' => esc_html__( 'Author Name', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_reportagenewsarticle_author_name_'.$schema_id,
                                'type' => 'text',
                                'default' => is_object($current_user) ?  $current_user->display_name : ''
                        ),
                        array(
                                'label' => esc_html__( 'Author Description', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_reportagenewsarticle_author_description_'.$schema_id,
                                'type' => 'textarea',
                                'default' => $author_desc
                        ), 
                        array(
                                'label'   => esc_html__( 'Author URL', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_reportagenewsarticle_author_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => $author_url
                        ),    
                        array(
                                'label' => esc_html__( 'Author Image', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_reportagenewsarticle_author_image_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($author_details['url']) ? $author_details['url']: ''
                        ),
        
                        array(
                                'label'   => esc_html__( 'Editor Type', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_reportagenewsarticle_editor_type_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                        ""                => "Select",
                                        'Person'           => 'Person',
                                        'Organization'     => 'Organization',                        
                                )
                        ),
                        array(
                                'label'   => esc_html__( 'Editor Name', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_reportagenewsarticle_editor_name_'.$schema_id,
                                'type'    => 'text',
                                'default' => is_object($current_user) ? $current_user->display_name : ''
                        ),
                        array(
                                'label'   => esc_html__( 'Editor HonorificSuffix', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_reportagenewsarticle_editor_honorific_suffix_'.$schema_id,
                                'type'    => 'text',
                                'attributes' => array(
                                        'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                                ),
                        ), 
                        array(
                                'label'   => esc_html__( 'Editor Description', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_reportagenewsarticle_editor_description_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => $author_desc
                        ),
                        array(
                                'label'   => esc_html__( 'Editor URL', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_reportagenewsarticle_editor_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => $author_url
                        ),
                        array(
                                'label' => esc_html__( 'Editor Image URL', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_reportagenewsarticle_editor_image_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($author_details['url']) ? $author_details['url']: ''
                        ),
                        array(
                                'label'   => esc_html__( 'About', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_reportagenewsarticle_about_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => '',
                                'note'    => 'Note: If There are more than one About, Separate About list by comma ( , )',
                                'attributes' => array(
                                        'placeholder' => 'eg: Apple is March 21 Announcements'
                                ),
                        ), 
                        array(
                                'label' => esc_html__( 'Organization Name', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_reportagenewsarticle_organization_name_'.$schema_id,
                                'type' => 'text',
                                'default'=> saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                        ),
                        array(
                                'label' => esc_html__( 'Organization Logo', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_reportagenewsarticle_organization_logo_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($sd_data['sd_logo'])? $sd_data['sd_logo']['url']:''
                        ),                         
                        array(
                                'label' => esc_html__( 'Speakable', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_reportagenewsarticle_speakable_'.$schema_id,
                                'type' => 'checkbox',
        
                        )                        
                        );
                        break;

                        case 'ReviewNewsArticle':
        
                        $category_detail=get_the_category(get_the_ID());//$post->ID
                        $article_section = '';
                        
                        foreach( $category_detail as $cd){
                                
                        $article_section =  $cd->cat_name;
                        
                        }
                        $word_count = saswp_reading_time_and_word_count();
                        
                        $meta_field = array(
                                array(
                                        'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_reviewnewsarticle_id_'.$schema_id,
                                        'type'       => 'text',
                                        'default'    => 'reviewnewsarticle'   
                                ),  
                        array(
                                'label' => esc_html__( 'Main Entity Of Page', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_reviewnewsarticle_main_entity_of_page_'.$schema_id,
                                'type' => 'text',
                                'default' => get_permalink()
                        ),
                        array(
                                'label' => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_reviewnewsarticle_URL_'.$schema_id,
                                'type' => 'text',
                                'default' => get_permalink(),
                        ),
                        array(
                                'label' => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_reviewnewsarticle_image_'.$schema_id,
                                'type' => 'media',                            
                        ),    
                        array(
                                'label'   => esc_html__( 'inLanguage', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_reviewnewsarticle_inlanguage_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_bloginfo('language'),
                        ),
                        array(
                                'label' => esc_html__( 'Headline', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_reviewnewsarticle_headline_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_title(),
                        ),
                        array(
                                'label' => esc_html__( 'Date Published', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_reviewnewsarticle_date_published_'.$schema_id,
                                'type' => 'text',
                                'default' => get_the_date("Y-m-d")
                        ),
                        array(
                                'label' => esc_html__( 'Date Modified', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_reviewnewsarticle_date_modified_'.$schema_id,
                                'type' => 'text',
                                'default' => get_the_modified_date("Y-m-d")
                        ),
                                array(
                                'label' => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_reviewnewsarticle_description_'.$schema_id,
                                'type' => 'textarea',
                                'default' => saswp_strip_all_tags(get_the_excerpt())
                        ),
                        array(
                                'label'   => esc_html__( 'hasPart', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_reviewnewsarticle_haspart_'.$schema_id,
                                'type'    => 'text',
                                'is_template_attr' => 'yes',
                        ),
                        array(
                                'label'   => esc_html__( 'isPartOf', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_reviewnewsarticle_ispartof_'.$schema_id,
                                'type'    => 'text',
                                'is_template_attr' => 'yes',
                        ),
                        array(
                                'label' => esc_html__( 'Keywords', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_reviewnewsarticle_keywords_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_tags()
                        ),    
                                array(
                                'label' => esc_html__( 'Article Section', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_reviewnewsarticle_section_'.$schema_id,
                                'type' => 'text',
                                'default' => $article_section
                        ),
                        array(
                                'label' => esc_html__( 'Article Body', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_reviewnewsarticle_body_'.$schema_id,
                                'type' => 'textarea',
                                'default' => is_object($post) ? saswp_strip_all_tags($post->post_content) : ''
                        ),
                                array(
                                'label' => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_reviewnewsarticle_name_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_title()
                        ), 
                                array(
                                'label' => esc_html__( 'Thumbnail URL', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_reviewnewsarticle_thumbnailurl_'.$schema_id,
                                'type' => 'text'                            
                        ),
                        array(
                                'label' => esc_html__( 'Word Count', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_reviewnewsarticle_word_count_'.$schema_id,
                                'type' => 'text',
                                'default' => $word_count['word_count']
                        ),
                        array(
                                'label' => esc_html__( 'Time Required', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_reviewnewsarticle_timerequired_'.$schema_id,
                                'type' => 'text',
                                'default' => $word_count['timerequired']
                        ),    
                        array(
                                'label' => esc_html__( 'Main Entity Id', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_reviewnewsarticle_main_entity_id_'.$schema_id,
                                'type' => 'text',
                                'default' => get_permalink()
                        ),
                        array(
                                'label'   => esc_html__( 'Author Type', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_reviewnewsarticle_author_type_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                        'Person'           => 'Person',
                                        'Organization'     => 'Organization',                        
                                )
                        ),
                        array(
                                'label' => esc_html__( 'Author Name', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_reviewnewsarticle_author_name_'.$schema_id,
                                'type' => 'text',
                                'default' => is_object($current_user) ?  $current_user->display_name : ''
                        ),
                        array(
                                'label' => esc_html__( 'Author Description', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_reviewnewsarticle_author_description_'.$schema_id,
                                'type' => 'textarea',
                                'default' => $author_desc
                        ), 
                        array(
                                'label'   => esc_html__( 'Author URL', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_reviewnewsarticle_author_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => $author_url
                        ),    
                        array(
                                'label' => esc_html__( 'Author Image', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_reviewnewsarticle_author_image_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($author_details['url']) ? $author_details['url']: ''
                        ),
        
                        array(
                                'label'   => esc_html__( 'Editor Type', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_reviewnewsarticle_editor_type_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                        ""                => "Select",
                                        'Person'           => 'Person',
                                        'Organization'     => 'Organization',                        
                                )
                        ),
                        array(
                                'label'   => esc_html__( 'Editor Name', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_reviewnewsarticle_editor_name_'.$schema_id,
                                'type'    => 'text',
                                'default' => is_object($current_user) ? $current_user->display_name : ''
                        ),
                        array(
                                'label'   => esc_html__( 'Editor HonorificSuffix', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_reviewnewsarticle_editor_honorific_suffix_'.$schema_id,
                                'type'    => 'text',
                                'attributes' => array(
                                        'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                                ),
                        ), 
                        array(
                                'label'   => esc_html__( 'Editor Description', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_reviewnewsarticle_editor_description_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => $author_desc
                        ),
                        array(
                                'label'   => esc_html__( 'Editor URL', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_reviewnewsarticle_editor_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => $author_url
                        ),
                        array(
                                'label' => esc_html__( 'Editor Image URL', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_reviewnewsarticle_editor_image_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($author_details['url']) ? $author_details['url']: ''
                        ),
                        array(
                                'label'   => esc_html__( 'About', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_reviewnewsarticle_about_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => '',
                                'note'    => 'Note: If There are more than one About, Separate About list by comma ( , )',
                                'attributes' => array(
                                        'placeholder' => 'eg: Apple is March 21 Announcements'
                                ),
                        ), 
                        array(
                                'label' => esc_html__( 'Organization Name', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_reviewnewsarticle_organization_name_'.$schema_id,
                                'type' => 'text',
                                'default'=> saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                        ),
                        array(
                                'label' => esc_html__( 'Organization Logo', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_reviewnewsarticle_organization_logo_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($sd_data['sd_logo'])? $sd_data['sd_logo']['url']:''
                        ),                         
                        array(
                                'label' => esc_html__( 'Speakable', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_reviewnewsarticle_speakable_'.$schema_id,
                                'type' => 'checkbox',
        
                        ),                        
                        );
                        if($manual == null){
                         
                            $meta_field[] = array(
                            'label'   => esc_html__( 'Item Reviewed Type', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_review_item_reviewed_'.$schema_id,
                            'type'    => 'select',
                            'options' => array(
                                        'Book'                  => 'Book',                             
                                        'Course'                => 'Course',                             
                                        'Event'                 => 'Event',                              
                                        'HowTo'                 => 'HowTo',   
                                        'local_business'        => 'LocalBusiness',                                 
                                        'MusicPlaylist'         => 'Music Playlist',
                                        'Movie'                 => 'Movie',
                                        'Organization'          => 'Organization', 
                                        'Product'               => 'Product',                                
                                        'Recipe'                => 'Recipe',                             
                                        'SoftwareApplication'   => 'SoftwareApplication',
                                        'MobileApplication'     => 'MobileApplication',
                                        'VideoGame'             => 'VideoGame', 
                            )                                                        
                         );
                                                        
                        }
                        break;
                
                case 'WebPage':
                    $meta_field = array(
                        array(
                                'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_webpage_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'WebPage'   
                        ), 
                    array(
                            'label' => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_webpage_name_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_get_the_title()
                    ),
                    array(
                            'label' => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_webpage_url_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink()
                    ),
                    array(
                            'label' => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_webpage_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => saswp_strip_all_tags(get_the_excerpt())
                    ),
                    array(
                        'label'   => esc_html__( 'inLanguage', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_webpage_inlanguage_'.$schema_id,
                        'type'    => 'text',
                        'default' => get_bloginfo('language'),
                    ),
                    array(
                        'label'   => esc_html__( 'Webpage Section', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_webpage_section_'.$schema_id,
                        'type'    => 'textarea',
                        'default' => saswp_strip_all_tags(get_the_excerpt())
                    ),                           
                    array(
                            'label' => esc_html__( 'Keywords', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_webpage_keywords_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_get_the_tags()
                    ),
                    array(
                            'label' => esc_html__( 'Main Entity Of Page', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_webpage_main_entity_of_page_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink()
                    ), 
                    array(
                            'label' => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_webpage_image_'.$schema_id,
                            'type' => 'media',                            
                    ), 
                    array(
                            'label' => esc_html__( 'Headline', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_webpage_headline_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_get_the_title(),
                    ),
                    array(
                            'label' => esc_html__( 'hasPart', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_webpage_haspart_'.$schema_id,
                            'type' => 'text',
                            'is_template_attr' => 'yes',
                    ),
                    array(
                        'label'   => esc_html__( 'Date Created', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_webpage_date_created_'.$schema_id,
                        'type'    => 'text',
                        'default' => get_the_modified_date("Y-m-d")
                   ),
                    array(
                            'label' => esc_html__( 'Date Published', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_webpage_date_published_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_date("Y-m-d")
                    ),
                    array(
                            'label' => esc_html__( 'Date Modified', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_webpage_date_modified_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_modified_date("Y-m-d")
                    ),
                    array(
                        'label'   => esc_html__( 'Last Reviewed', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_webpage_last_reviewed_'.$schema_id,
                        'type'    => 'text',
                        'default' => get_the_modified_date("Y-m-d")
                    ),
                     array(
                        'label'   => esc_html__( 'Reviewed By', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_webpage_reviewed_by_'.$schema_id,
                        'type'    => 'text',
                        'default' => saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                      ),
                    array(
                        'label'   => esc_html__( 'Author Type', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_webpage_author_type_'.$schema_id,
                        'type'    => 'select',
                        'options' => array(
                                'Person'           => 'Person',
                                'Organization'     => 'Organization',                        
                       )
                    ),
                    array(
                            'label' => esc_html__( 'Author Name', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_webpage_author_name_'.$schema_id,
                            'type' => 'text',
                            'default' => is_object($current_user) ? $current_user->display_name : ''
                    ),
                    array(
                            'label' => esc_html__( 'Author Description', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_webpage_author_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => $author_desc
                    ), 
                    array(
                            'label'   => esc_html__( 'Author URL', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_webpage_author_url_'.$schema_id,
                            'type'    => 'text',
                            'default' => $author_url
                    ),    
                    array(
                            'label' => esc_html__( 'Organization Name', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_webpage_organization_name_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                    ), 
                     array(
                            'label' => esc_html__( 'Organization Logo', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_webpage_organization_logo_'.$schema_id,
                            'type' => 'media',
                            'default' => isset($sd_data['sd_logo']) ? $sd_data['sd_logo']['url']:''
                    ),
                    array(
                        'label' => esc_html__( 'Speakable', 'schema-and-structured-data-for-wp' ),
                        'id' => 'saswp_webpage_speakable_'.$schema_id,
                        'type' => 'checkbox',

                    ),    
                    array(
                        'label' => esc_html__( 'Specialty', 'schema-and-structured-data-for-wp' ),
                        'id' => 'saswp_webpage_specialty_'.$schema_id,
                        'type' => 'text',

                    ),
                    array(
                        'label' => esc_html__( 'Main Content Of Page', 'schema-and-structured-data-for-wp' ),
                        'id' => 'saswp_webpage_mcop_'.$schema_id,
                        'type' => 'text',

                    ), 
                    array(
                        'label' => esc_html__( 'Same As', 'schema-and-structured-data-for-wp' ),
                        'id'    => 'saswp_webpage_same_as_'.$schema_id,
                        'type'  => 'textarea',
                        'attributes' => array(
                            'placeholder' => 'Example, Example2'
                        ),
                        'note' => 'Note: Separate same as list by comma ( , )'                     
                    ),   
                    );
                    break;

                case 'ItemPage':
                        $meta_field = array(
                                array(
                                        'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_itempage_id_'.$schema_id,
                                        'type'       => 'text',
                                        'default'    => 'ItemPage'   
                                ), 
                        array(
                                'label' => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_itempage_name_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_title()
                        ),
                        array(
                                'label' => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_itempage_url_'.$schema_id,
                                'type' => 'text',
                                'default' => get_permalink()
                        ),
                        array(
                                'label' => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_itempage_description_'.$schema_id,
                                'type' => 'textarea',
                                'default' => saswp_strip_all_tags(get_the_excerpt())
                        ),
                        array(
                                'label'   => esc_html__( 'inLanguage', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_itempage_inlanguage_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_bloginfo('language'),
                        ),
                        array(
                                'label'   => esc_html__( 'ItemPage Section', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_itempage_section_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => saswp_strip_all_tags(get_the_excerpt())
                        ),                           
                        array(
                                'label' => esc_html__( 'Keywords', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_itempage_keywords_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_tags()
                        ),
                        array(
                                'label' => esc_html__( 'Main Entity Of Page', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_itempage_main_entity_of_page_'.$schema_id,
                                'type' => 'text',
                                'default' => get_permalink()
                        ), 
                        array(
                                'label' => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_itempage_image_'.$schema_id,
                                'type' => 'media',                            
                        ), 
                        array(
                                'label' => esc_html__( 'Headline', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_itempage_headline_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_title(),
                        ),
                        array(
                                'label'   => esc_html__( 'Date Created', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_itempage_date_created_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_the_modified_date("Y-m-d")
                        ),
                        array(
                                'label' => esc_html__( 'Date Published', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_itempage_date_published_'.$schema_id,
                                'type' => 'text',
                                'default' => get_the_date("Y-m-d")
                        ),
                        array(
                                'label' => esc_html__( 'Date Modified', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_itempage_date_modified_'.$schema_id,
                                'type' => 'text',
                                'default' => get_the_modified_date("Y-m-d")
                        ),
                        array(
                                'label'   => esc_html__( 'Last Reviewed', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_itempage_last_reviewed_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_the_modified_date("Y-m-d")
                        ),
                                array(
                                'label'   => esc_html__( 'Reviewed By', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_itempage_reviewed_by_'.$schema_id,
                                'type'    => 'text',
                                'default' => saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                                ),
                        array(
                                'label'   => esc_html__( 'Author Type', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_itempage_author_type_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                        'Person'           => 'Person',
                                        'Organization'     => 'Organization',                        
                                )
                        ),
                        array(
                                'label' => esc_html__( 'Author Name', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_itempage_author_name_'.$schema_id,
                                'type' => 'text',
                                'default' => is_object($current_user) ? $current_user->display_name : ''
                        ),
                        array(
                                'label' => esc_html__( 'Author Description', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_itempage_author_description_'.$schema_id,
                                'type' => 'textarea',
                                'default' => $author_desc
                        ), 
                        array(
                                'label'   => esc_html__( 'Author URL', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_itempage_author_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => $author_url
                        ),    
                        array(
                                'label' => esc_html__( 'Organization Name', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_itempage_organization_name_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                        ), 
                                array(
                                'label' => esc_html__( 'Organization Logo', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_itempage_organization_logo_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($sd_data['sd_logo']) ? $sd_data['sd_logo']['url']:''
                        ),
                        array(
                                'label' => esc_html__( 'Speakable', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_itempage_speakable_'.$schema_id,
                                'type' => 'checkbox',

                        )    
                );
                break;

                case 'MedicalWebPage':
                    $meta_field = array(
                        array(
                                'label' => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_medicalwebpage_name_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_title()
                        ),
                        array(
                                'label' => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_medicalwebpage_url_'.$schema_id,
                                'type' => 'text',
                                'default' => get_permalink()
                        ),
                        array(
                                'label' => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_medicalwebpage_description_'.$schema_id,
                                'type' => 'textarea',
                                'default' => saswp_strip_all_tags(get_the_excerpt())
                        ),
                        array(
                                'label'   => esc_html__( 'MedicalWebPage Section', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_medicalwebpage_section_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => saswp_strip_all_tags(get_the_excerpt())
                        ),                           
                        array(
                                'label' => esc_html__( 'Keywords', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_medicalwebpage_keywords_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_tags()
                        ),
                        array(
                                'label' => esc_html__( 'Main Entity Of Page', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_medicalwebpage_main_entity_of_page_'.$schema_id,
                                'type' => 'text',
                                'default' => get_permalink()
                        ), 
                        array(
                                'label' => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_medicalwebpage_image_'.$schema_id,
                                'type' => 'media',                            
                        ), 
                        array(
                                'label' => esc_html__( 'Headline', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_medicalwebpage_headline_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_title(),
                        ),
                        array(
                                'label'   => esc_html__( 'Date Created', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_medicalwebpage_date_created_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_the_modified_date("Y-m-d")
                        ),
                        array(
                                'label' => esc_html__( 'Date Published', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_medicalwebpage_date_published_'.$schema_id,
                                'type' => 'text',
                                'default' => get_the_date("Y-m-d")
                        ),
                        array(
                                'label' => esc_html__( 'Date Modified', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_medicalwebpage_date_modified_'.$schema_id,
                                'type' => 'text',
                                'default' => get_the_modified_date("Y-m-d")
                        ),
                        array(
                                'label'   => esc_html__( 'Last Reviewed', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_medicalwebpage_last_reviewed_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_the_modified_date("Y-m-d")
                        ),
                        array(
                                'label'   => esc_html__( 'Reviewed By', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_medicalwebpage_reviewed_by_'.$schema_id,
                                'type'    => 'text',
                                'default' => saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                        ),
                        array(
                                'label'   => esc_html__( 'Author Type', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_medicalwebpage_author_type_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                        'Person'           => 'Person',
                                        'Organization'     => 'Organization',                        
                                )
                        ),
                        array(
                                'label' => esc_html__( 'Author Name', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_medicalwebpage_author_name_'.$schema_id,
                                'type' => 'text',
                                'default' => is_object($current_user) ? $current_user->display_name : ''
                        ),
                        array(
                                'label' => esc_html__( 'Author Description', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_medicalwebpage_author_description_'.$schema_id,
                                'type' => 'textarea',
                                'default' => $author_desc
                        ), 
                        array(
                                'label'   => esc_html__( 'Author URL', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_medicalwebpage_author_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => $author_url
                        ),    
                        array(
                                'label' => esc_html__( 'Organization Name', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_medicalwebpage_organization_name_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                        ), 
                        array(
                                'label' => esc_html__( 'Organization Logo', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_medicalwebpage_organization_logo_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($sd_data['sd_logo']) ? $sd_data['sd_logo']['url']:''
                        ),
                        array(
                                'label' => esc_html__( 'Speakable', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_medicalwebpage_speakable_'.$schema_id,
                                'type' => 'checkbox',

                        )    
                    );
                break;

                    case 'Photograph':                                        
                        $meta_field = array( 
                        array(
                                'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_photograph_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'Photograph'   
                        ),
			array(
                                'label' => esc_html__( 'Headline', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_photograph_headline_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_title()
                        ),						
                        array(
                                'label'   => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_photograph_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_permalink(),
                        ),    
                        array(
                                'label' => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_photograph_image_'.$schema_id,
                                'type' => 'media'                            
                        ),
                        array(
                                'label'   => esc_html__( 'inLanguage', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_photograph_inlanguage_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_bloginfo('language'),
                        ),                        
                        array(
                                'label' => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_photograph_description_'.$schema_id,
                                'type' => 'textarea',
                                'default' => saswp_strip_all_tags(get_the_excerpt())
                        ),                                                    
                        array(
                                'label' => esc_html__( 'Date Published', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_photograph_date_published_'.$schema_id,
                                'type' => 'text',
                                'default' => get_the_date("Y-m-d")
                        ), 
                        array(
                                'label' => esc_html__( 'Date Modified', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_photograph_date_modified_'.$schema_id,
                                'type' => 'text',
                                'default' => get_the_modified_date("Y-m-d")
                        ),
                        array(
                                'label'   => esc_html__( 'Author', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_photograph_author_global_mapping_'.$schema_id,
                                'type'    => 'global_mapping'
                        ),
                        array(
                                'label'   => esc_html__( 'Author Type', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_photograph_author_type_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                        'Person'           => 'Person',
                                        'Organization'     => 'Organization',                        
                                )
                        ),
                        array(
                                'label' => esc_html__( 'Author Name', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_photograph_author_name_'.$schema_id,
                                'type' => 'text',
                                'default' => is_object($current_user) ? $current_user->display_name : ''
                        ),
                        array(
                                'label'   => esc_html__( 'Author HonorificSuffix', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_photograph_author_honorific_suffix_'.$schema_id,
                                'type'    => 'text',
                                'attributes' => array(
                                        'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                                 ),
                        ),
                        array(
                                'label' => esc_html__( 'Author Description', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_photograph_author_description_'.$schema_id,
                                'type' => 'textarea',
                                'default' => $author_desc
                        ),
                        array(
                                'label'   => esc_html__( 'Author URL', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_photograph_author_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => $author_url
                        ),   
                        array(
                                'label' => esc_html__( 'Author Image URL', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_photograph_author_image_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($author_details['url']) ? $author_details['url']: ''
                        ), 
                        array(
                                'label'   => esc_html__( 'JobTitle', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_photograph_author_jobtitle_'.$schema_id,
                                'type'    => 'text',
                                'default' => '',
                                'attributes' => array(
                                        'placeholder' => 'eg: Editor in Chief'
                                 ),
                        ),

                        array(
                                'label'   => esc_html__( 'Editor Type', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_photograph_editor_type_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                         ""               => "Select",
                                        'Person'           => 'Person',
                                        'Organization'     => 'Organization',                        
                                )
                        ),
                        array(
                                'label' => esc_html__( 'Editor Name', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_photograph_editor_name_'.$schema_id,
                                'type' => 'text',
                                'default' => is_object($current_user) ? $current_user->display_name : ''
                        ),
                        array(
                                'label'   => esc_html__( 'Editor HonorificSuffix', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_photograph_editor_honorific_suffix_'.$schema_id,
                                'type'    => 'text',
                                'attributes' => array(
                                        'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                                 ),
                        ),
                        array(
                                'label'   => esc_html__( 'Editor Description', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_photograph_editor_description_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => $author_desc
                        ),
                        array(
                                'label'   => 'Editor URL',
                                'id'      => 'saswp_photograph_editor_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => $author_url
                        ),
                        array(
                                'label' => esc_html__( 'Editor Image URL', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_photograph_editor_image_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($author_details['url']) ? $author_details['url']: ''
                        ),
                        array(
                                'label'   => esc_html__( 'ReviewedBy', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_photograph_reviewedby_global_mapping_'.$schema_id,
                                'type'    => 'global_mapping'
                        ),
                        array(
                                'label'   => esc_html__( 'ReviewedBy Type', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_photograph_reviewedby_type_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                         ""                => "Select",
                                        'Person'           => 'Person',
                                        'Organization'     => 'Organization',                        
                                )
                        ),
                        array(
                                'label' => esc_html__( 'ReviewedBy Name', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_photograph_reviewedby_name_'.$schema_id,
                                'type' => 'text',
                                'default' => is_object($current_user) ? $current_user->display_name : ''
                        ),
                        array(
                                'label'   => esc_html__( 'ReviewedBy HonorificSuffix', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_photograph_reviewedby_honorific_suffix_'.$schema_id,
                                'type'    => 'text',
                                'attributes' => array(
                                        'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                                 ),
                        ),
                        array(
                                'label' => esc_html__( 'ReviewedBy Description', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_photograph_reviewedby_description_'.$schema_id,
                                'type' => 'textarea',
                                'default' => $author_desc
                        ),
                        array(
                                'label'   => esc_html__( 'ReviewedBy URL', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_photograph_reviewedby_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => $author_url
                        ),  
                        array(
                                'label' => esc_html__( 'Organization Name', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_photograph_organization_name_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                        ),
                        array(
                                'label' => esc_html__( 'Organization Logo', 'schema-and-structured-data-for-wp' ),
                                'id'    => 'saswp_photograph_organization_logo_'.$schema_id,
                                'type'  => 'media',
                                'default' => isset($sd_data['sd_logo']['url']) ? $sd_data['sd_logo']['url']:''
                        ),
                        array(
                                'label'   => esc_html__( 'About', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_photograph_about_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => '',
                                'note'    => 'Note: If There are more than one About, Separate About list by comma ( , )',
                                'attributes' => array(
                                        'placeholder' => 'eg: Apple is March 21 Announcements'
                                ),
                        ),  
                        array(
                                'label'   => esc_html__( 'AlumniOf', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_photograph_alumniof_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => '',
                                'note'    => 'Note: If There are more than one AlumniOf, Separate AlumniOf list by comma ( , )',
                                'attributes' => array(
                                        'placeholder' => 'eg: City University of New York-Herbert H. Lehman College, Southern New Hampshire University'
                                ),
                        ),   
                        array(
                                'label'   => esc_html__( 'knowsAbout', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_photograph_knowsabout_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => '',
                                'note'    => 'Note: If There are more than one knows about, Separate knows about list by comma ( , )'       
                        ),
                        array(
                                'label'   => esc_html__( 'ReviewedBy', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_photograph_reviewedby_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                        'Person'           => 'Person',
                                        'Organization'     => 'Organization',                        
                                )
                        )                                        
                        );
                        break;
                
                    case 'Article':                                        
                        $meta_field = array(
                        array(
                                'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_article_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'Article'   
                        ),
                        array(
                                'label'   => esc_html__( 'Main Entity Of Page', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_article_main_entity_of_page_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_permalink()
                        ),
                        array(
                                'label'   => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_article_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_permalink(),
                        ),    
                        array(
                                'label'   => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_article_image_'.$schema_id,
                                'type'    => 'media'                            
                        ),
                        array(
                                'label'   => esc_html__( 'inLanguage', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_article_inlanguage_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_bloginfo('language'),
                        ),
                        array(
                                'label'   => esc_html__( 'Headline', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_article_headline_'.$schema_id,
                                'type'    => 'text',
                                'default' => saswp_get_the_title()
                        ),
                        array(
                                'label'   => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_article_description_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => saswp_strip_all_tags(get_the_excerpt())
                        ),
                        array(
                                'label'   => esc_html__( 'hasPart', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_article_haspart_'.$schema_id,
                                'type'    => 'text',
                                'is_template_attr' => 'yes',
                        ),
                        array(
                                'label'   => esc_html__( 'isPartOf', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_article_ispartof_'.$schema_id,
                                'type'    => 'text',
                                'is_template_attr' => 'yes',
                        ),
                        array(
                                'label'   => esc_html__( 'Article Section', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_article_section_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => saswp_strip_all_tags(get_the_excerpt())
                        ),    
                        array(
                                'label'   => esc_html__( 'Article Body', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_article_body_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => is_object($post) ? saswp_strip_all_tags($post->post_content) : ''
                        ),    
                        array(
                                'label'   => esc_html__( 'Keywords', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_article_keywords_'.$schema_id,
                                'type'    => 'text',
                                'default' => saswp_get_the_tags()
                        ),    
                        array(
                                'label'   => esc_html__( 'Date Published', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_article_date_published_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_the_date("Y-m-d")
                        ), 
                        array(
                                'label'   => esc_html__( 'Date Modified', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_article_date_modified_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_the_modified_date("Y-m-d")
                        ),
                        array(
                                'label'   => esc_html__( 'Author', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_article_author_global_mapping_'.$schema_id,
                                'type'    => 'global_mapping'
                        ),
                        array(
                                'label'   => esc_html__( 'Author Type', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_article_author_type_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                        'Person'           => 'Person',
                                        'Organization'     => 'Organization',                        
                                )
                        ),
                        array(
                                'label'   => esc_html__( 'Author Name', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_article_author_name_'.$schema_id,
                                'type'    => 'text',
                                'default' => is_object($current_user) ? $current_user->display_name : ''
                        ),
                        array(
                                'label'   => esc_html__( 'Author HonorificSuffix', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_article_author_honorific_suffix_'.$schema_id,
                                'type'    => 'text',
                                'attributes' => array(
                                        'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                                 ),
                        ),
                        array(
                                'label'   => esc_html__( 'Author Description', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_article_author_description_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => $author_desc
                        ),
                        array(
                                'label'   => esc_html__( 'Author URL', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_article_author_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => $author_url
                        ),
                        array(
                                'label' => esc_html__( 'Author Image', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_article_author_image_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($author_details['url']) ? $author_details['url']: ''
                        ),
                        array(
                                'label' => esc_html__( 'Author Social Profile', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_article_author_social_profile_'.$schema_id,
                                'type' => 'textarea',
                                'default' => '',
                                'note'    => 'Note: If There are more than one social profiles, Separate them by comma ( , )',
                        ),
                        array(
                                'label'   => esc_html__( 'JobTitle', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_article_author_jobtitle_'.$schema_id,
                                'type'    => 'text',
                                'default' => '',
                                'attributes' => array(
                                        'placeholder' => 'eg: Editor in Chief'
                                 ),
                        ),
                        array(
                                'label'   => esc_html__( 'ReviewedBy', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_article_reviewedby_global_mapping_'.$schema_id,
                                'type'    => 'global_mapping'
                        ),
                        array(
                                'label'   => esc_html__( 'ReviewedBy Type', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_article_reviewedby_type_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                        ""                => "Select",
                                        'Person'           => 'Person',
                                        'Organization'     => 'Organization',                        
                                )
                        ),
                        array(
                                'label'   => esc_html__( 'ReviewedBy Name', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_article_reviewedby_name_'.$schema_id,
                                'type'    => 'text',
                                'default' => is_object($current_user) ? $current_user->display_name : ''
                        ),
                        array(
                                'label'   => esc_html__( 'ReviewedBy HonorificSuffix', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_article_reviewedby_honorific_suffix_'.$schema_id,
                                'type'    => 'text',
                                'attributes' => array(
                                        'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                                 ),
                        ),
                        array(
                                'label'   => esc_html__( 'ReviewedBy Description', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_article_reviewedby_description_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => $author_desc
                        ),
                        array(
                                'label'   => esc_html__( 'ReviewedBy URL', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_article_reviewedby_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => $author_url
                        ),
                        array(
                                'label'   => esc_html__( 'Editor', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_article_editor_global_mapping_'.$schema_id,
                                'type'    => 'global_mapping'
                        ),
                        array(
                                'label'   => esc_html__( 'Editor Type', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_article_editor_type_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                        ""                => "Select",
                                        'Person'           => 'Person',
                                        'Organization'     => 'Organization',                        
                                )
                        ),
                        array(
                                'label'   => esc_html__( 'Editor Name', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_article_editor_name_'.$schema_id,
                                'type'    => 'text',
                                'default' => is_object($current_user) ? $current_user->display_name : ''
                        ),
                        array(
                                'label'   => esc_html__( 'Editor HonorificSuffix', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_article_editor_honorific_suffix_'.$schema_id,
                                'type'    => 'text',
                                'attributes' => array(
                                        'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                                 ),
                        ), 
                        array(
                                'label'   => esc_html__( 'Editor Description', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_article_editor_description_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => $author_desc
                        ),
                        array(
                                'label'   => esc_html__( 'Editor URL', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_article_editor_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => $author_url
                        ),
                        array(
                                'label' => esc_html__( 'Editor Image URL', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_article_editor_image_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($author_details['url']) ? $author_details['url']: ''
                        ),
                        array(
                                'label'   => esc_html__( 'Organization Name', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_article_organization_name_'.$schema_id,
                                'type'    => 'text',
                                'default' => saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                        ),
                        array(
                                'label'   => esc_html__( 'Organization Logo', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_article_organization_logo_'.$schema_id,
                                'type'    => 'media',
                                'default' => isset($sd_data['sd_logo']['url']) ? $sd_data['sd_logo']['url']:''
                        ),
                        array(
                                'label'   => esc_html__( 'About', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_article_about_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => '',
                                'note'    => 'Note: If There are more than one About, Separate About list by comma ( , )',
                                'attributes' => array(
                                        'placeholder' => 'eg: Apple is March 21 Announcements'
                                ),
                        ),  
                        array(
                                'label'   => esc_html__( 'AlumniOf', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_article_alumniof_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => '',
                                'note'    => 'Note: If There are more than one AlumniOf, Separate AlumniOf list by comma ( , )',
                                'attributes' => array(
                                        'placeholder' => 'eg: City University of New York-Herbert H. Lehman College, Southern New Hampshire University'
                                ),
                        ),  
                        array(
                                'label'   => esc_html__( 'knowsAbout', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_article_knowsabout_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => '',
                                'note'    => 'Note: If There are more than one knows about, Separate knows about list by comma ( , )',
                                'attributes' => array(
                                        'placeholder' => 'eg: Financial services, negotiation, CRM, Project Management, Mentoring, Learning & Development'
                                 ),   
                        ),
                        array(
                                'label'   => esc_html__( 'Speakable', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_article_speakable_'.$schema_id,
                                'type'    => 'checkbox',
                        )
                        );
                        break;

                    case 'ScholarlyArticle':                                        
                        $meta_field = array(
                        array(
                                'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_scholarlyarticle_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'ScholarlyArticle'   
                        ),
                        array(
                                'label'   => esc_html__( 'Main Entity Of Page', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_scholarlyarticle_main_entity_of_page_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_permalink()
                        ),
                        array(
                                'label'   => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_scholarlyarticle_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_permalink(),
                        ),    
                        array(
                                'label'   => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_scholarlyarticle_image_'.$schema_id,
                                'type'    => 'media'                            
                        ),
                        array(
                                'label'   => esc_html__( 'inLanguage', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_scholarlyarticle_inlanguage_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_bloginfo('language'),
                        ),
                        array(
                                'label'   => esc_html__( 'Headline', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_scholarlyarticle_headline_'.$schema_id,
                                'type'    => 'text',
                                'default' => saswp_get_the_title()
                        ),
                        array(
                                'label'   => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_scholarlyarticle_description_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => saswp_strip_all_tags(get_the_excerpt())
                        ),
                        array(
                                'label'   => 'ScholarlyArticle Section',
                                'id'      => 'saswp_scholarlyarticle_section_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => saswp_strip_all_tags(get_the_excerpt())
                        ),    
                        array(
                                'label'   => esc_html__( 'ScholarlyArticle Body', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_scholarlyarticle_body_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => is_object($post) ? saswp_strip_all_tags($post->post_content) : ''
                        ),    
                        array(
                                'label'   => esc_html__( 'Keywords', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_scholarlyarticle_keywords_'.$schema_id,
                                'type'    => 'text',
                                'default' => saswp_get_the_tags()
                        ),    
                        array(
                                'label'   => esc_html__( 'Date Published', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_scholarlyarticle_date_published_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_the_date("Y-m-d")
                        ), 
                        array(
                                'label'   => esc_html__( 'Date Modified', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_scholarlyarticle_date_modified_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_the_modified_date("Y-m-d")
                        ),
                        array(
                                'label'   => esc_html__( 'Author', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_scholarlyarticle_author_global_mapping_'.$schema_id,
                                'type'    => 'global_mapping'
                        ),
                        array(
                                'label'   => esc_html__( 'Author Type', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_scholarlyarticle_author_type_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                        'Person'           => 'Person',
                                        'Organization'     => 'Organization',                        
                                )
                        ),
                        array(
                                'label'   => esc_html__( 'Author Name', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_scholarlyarticle_author_name_'.$schema_id,
                                'type'    => 'text',
                                'default' => is_object($current_user) ? $current_user->display_name : ''
                        ),
                        array(
                                'label'   => esc_html__( 'Author HonorificSuffix', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_scholarlyarticle_author_honorific_suffix_'.$schema_id,
                                'type'    => 'text',
                                'attributes' => array(
                                        'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                                        ),
                        ),
                        array(
                                'label'   => esc_html__( 'Author Description', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_scholarlyarticle_author_description_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => $author_desc
                        ),
                        array(
                                'label'   => esc_html__( 'Author URL', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_scholarlyarticle_author_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => $author_url
                        ),
                        array(
                                'label' => esc_html__( 'Author Image', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_scholarlyarticle_author_image_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($author_details['url']) ? $author_details['url']: ''
                        ),
                        array(
                                'label'   => esc_html__( 'JobTitle', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_scholarlyarticle_author_jobtitle_'.$schema_id,
                                'type'    => 'text',
                                'default' => '',
                                'attributes' => array(
                                        'placeholder' => 'eg: Editor in Chief'
                                        ),
                        ),
                        array(
                                'label'   => esc_html__( 'ReviewedBy', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_scholarlyarticle_reviewedby_global_mapping_'.$schema_id,
                                'type'    => 'global_mapping'
                        ),
                        array(
                                'label'   => esc_html__( 'ReviewedBy Type', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_scholarlyarticle_reviewedby_type_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                        ""                => "Select",
                                        'Person'           => 'Person',
                                        'Organization'     => 'Organization',                        
                                )
                        ),
                        array(
                                'label'   => esc_html__( 'ReviewedBy Name', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_scholarlyarticle_reviewedby_name_'.$schema_id,
                                'type'    => 'text',
                                'default' => is_object($current_user) ? $current_user->display_name : ''
                        ),
                        array(
                                'label'   => esc_html__( 'ReviewedBy HonorificSuffix', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_scholarlyarticle_reviewedby_honorific_suffix_'.$schema_id,
                                'type'    => 'text',
                                'attributes' => array(
                                        'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                                        ),
                        ),
                        array(
                                'label'   => esc_html__( 'ReviewedBy Description', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_scholarlyarticle_reviewedby_description_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => $author_desc
                        ),
                        array(
                                'label'   => esc_html__( 'ReviewedBy URL', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_scholarlyarticle_reviewedby_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => $author_url
                        ),
                        array(
                                'label'   => esc_html__( 'Editor', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_scholarlyarticle_editor_global_mapping_'.$schema_id,
                                'type'    => 'global_mapping'
                        ),
                        array(
                                'label'   => esc_html__( 'Editor Type', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_scholarlyarticle_editor_type_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                        ""                => "Select",
                                        'Person'           => 'Person',
                                        'Organization'     => 'Organization',                        
                                )
                        ),
                        array(
                                'label'   => esc_html__( 'Editor Name', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_scholarlyarticle_editor_name_'.$schema_id,
                                'type'    => 'text',
                                'default' => is_object($current_user) ? $current_user->display_name : ''
                        ),
                        array(
                                'label'   => esc_html__( 'Editor HonorificSuffix', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_scholarlyarticle_editor_honorific_suffix_'.$schema_id,
                                'type'    => 'text',
                                'attributes' => array(
                                        'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                                        ),
                        ), 
                        array(
                                'label'   => esc_html__( 'Editor Description', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_scholarlyarticle_editor_description_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => $author_desc
                        ),
                        array(
                                'label'   => esc_html__( 'Editor URL', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_scholarlyarticle_editor_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => $author_url
                        ),
                        array(
                                'label' => esc_html__( 'Editor Image URL', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_scholarlyarticle_editor_image_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($author_details['url']) ? $author_details['url']: ''
                        ),
                        array(
                                'label'   => esc_html__( 'Organization Name', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_scholarlyarticle_organization_name_'.$schema_id,
                                'type'    => 'text',
                                'default' => saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                        ),
                        array(
                                'label'   => esc_html__( 'Organization Logo', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_scholarlyarticle_organization_logo_'.$schema_id,
                                'type'    => 'media',
                                'default' => isset($sd_data['sd_logo']['url']) ? $sd_data['sd_logo']['url']:''
                        ),
                        array(
                                'label'   => esc_html__( 'About', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_scholarlyarticle_about_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => '',
                                'note'    => 'Note: If There are more than one About, Separate About list by comma ( , )',
                                'attributes' => array(
                                        'placeholder' => 'eg: Apple is March 21 Announcements'
                                ),
                        ),  
                        array(
                                'label'   => esc_html__( 'AlumniOf', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_scholarlyarticle_alumniof_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => '',
                                'note'    => 'Note: If There are more than one AlumniOf, Separate AlumniOf list by comma ( , )',
                                'attributes' => array(
                                        'placeholder' => 'eg: City University of New York-Herbert H. Lehman College, Southern New Hampshire University'
                                ),
                        ),  
                        array(
                                'label'   => esc_html__( 'knowsAbout', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_scholarlyarticle_knowsabout_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => '',
                                'note'    => 'Note: If There are more than one knows about, Separate knows about list by comma ( , )',
                                'attributes' => array(
                                        'placeholder' => 'eg: Financial services, negotiation, CRM, Project Management, Mentoring, Learning & Development'
                                        ),   
                        ),
                        array(
                                'label'   => esc_html__( 'Speakable', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_scholarlyarticle_speakable_'.$schema_id,
                                'type'    => 'checkbox',
                        )
                        );
                        break;

                        case 'VisualArtwork':                                        
                                $meta_field = array(
                                        array(
                                                'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                                'id'         => 'saswp_visualartwork_id_'.$schema_id,
                                                'type'       => 'text',
                                                'default'    => 'VisualArtwork'   
                                        ),                               
                                array(
                                        'label'   => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_visualartwork_url_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => get_permalink(),
                                ),
                                array(
                                        'label'   => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_visualartwork_name_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => get_permalink(),
                                ), 
                                array(
                                        'label'   => 'Alternate Name',
                                        'id'      => 'saswp_visualartwork_alternate_name_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => get_permalink(),
                                ),                                                                                                    
                                array(
                                        'label'   => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_visualartwork_description_'.$schema_id,
                                        'type'    => 'textarea',
                                        'default' => saswp_strip_all_tags(get_the_excerpt())
                                ),
                                array(
                                        'label'   => esc_html__( 'Art form', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_visualartwork_artform_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => '',
                                ),
                                array(
                                        'label'   => esc_html__( 'Art Edition', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_visualartwork_artedition_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => '',
                                ),
                                array(
                                        'label'   => esc_html__( 'Art Work Surface', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_visualartwork_artwork_surface_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => '',
                                ),
                                array(
                                        'label'   => esc_html__( 'Width', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_visualartwork_width_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => '',
                                ),
                                array(
                                        'label'   => esc_html__( 'Height', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_visualartwork_height_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => '',
                                ),
                                array(
                                        'label'   => esc_html__( 'Art Medium', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_visualartwork_artmedium_'.$schema_id,
                                        'type'    => 'textarea',
                                        'default' => '',
                                        'note'    => 'Note: If There are more than one artmedium, Separate artmedium list by comma ( , )'                                 
                                ),
                                array(
                                        'label'   => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_visualartwork_image_'.$schema_id,
                                        'type'    => 'media'                            
                                ),                                                                        
                                array(
                                        'label'   => esc_html__( 'Date Created', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_visualartwork_date_created_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => get_the_date("Y-m-d")
                                ),                                 
                                array(
                                        'label'   => esc_html__( 'Creator Type', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_visualartwork_author_type_'.$schema_id,
                                        'type'    => 'select',
                                        'options' => array(
                                                'Person'           => 'Person',
                                                'Organization'     => 'Organization',                        
                                        )
                                ),
                                array(
                                        'label'   => esc_html__( 'Creator Name', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_visualartwork_author_name_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => is_object($current_user) ? $current_user->display_name : ''
                                ),
                                array(
                                        'label'   => esc_html__( 'Creator Description', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_visualartwork_author_description_'.$schema_id,
                                        'type'    => 'textarea',
                                        'default' => $author_desc
                                ),
                                array(
                                        'label'   => esc_html__( 'Creator URL', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_visualartwork_author_url_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => $author_url
                                ),
                                array(
                                        'label'   => esc_html__( 'Size', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_visualartwork_size_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => ''
                                ),
                                array(
                                        'label'   => esc_html__( 'License', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_visualartwork_license_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => ''
                                )                                                                                       
                                );
                                break;

                        case 'EducationalOccupationalProgram':                                        
                                $meta_field = array(          
                                array(
                                        'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_eop_id_'.$schema_id,
                                        'type'       => 'text',
                                        'default'    => 'EducationalOccupationalProgram'   
                                ),                      
                                array(
                                        'label'   => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_eop_name_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => saswp_strip_all_tags(get_the_excerpt())
                                ),
                                array(
                                        'label'   => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_eop_description_'.$schema_id,
                                        'type'    => 'textarea',
                                        'default' => saswp_strip_all_tags(get_the_excerpt())
                                ),
                                array(
                                        'label'   => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_eop_url_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => get_permalink(),
                                ),    
                                array(
                                        'label'   => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_eop_image_'.$schema_id,
                                        'type'    => 'media'                            
                                ),
                                array(
                                        'label'   => esc_html__( 'Time To Complete', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_eop_time_to_complete_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => 'P2Y'
                                         ),
                                        'default' => ''
                                ),
                                array(
                                        'label'   => esc_html__( 'Occupational Category', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_eop_occupational_category_'.$schema_id,
                                        'type'    => 'textarea',
                                        'attributes' => array(
                                                'placeholder' => '15-1111, 15-1121, 15-1122, 15-1131'
                                         ),
                                        'default' => ''
                                ),
                                array(
                                        'label'   => esc_html__( 'Occupational Credential Awarded', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_eop_occupational_credential_awarded_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => 'Associate Degree'
                                         ),
                                        'default' => ''
                                ),
                                array(
                                        'label'   => esc_html__( 'Program Prerequisites', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_eop_program_prerequisites_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => 'HighSchool'
                                         ),
                                        'default' => ''
                                ),
                                array(
                                        'label'   => esc_html__( 'Application StartDate', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_eop_application_start_date_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => '2019-05-14'
                                         ),
                                        'default' => ''
                                ),
                                array(
                                        'label'   => esc_html__( 'Application Deadline', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_eop_application_deadline_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => '2019-09-14'
                                         ),
                                        'default' => ''
                                ),
                                array(
                                        'label'   => esc_html__( 'Start Date', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_eop_start_date_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => '2019-10-01'
                                         ),
                                        'default' => ''
                                ),
                                array(
                                        'label'   => esc_html__( 'End Date', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_eop_end_date_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => '2021-10-01'
                                         ),
                                        'default' => ''
                                ),
                                array(
                                        'label'   => esc_html__( 'Day Of Week', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_eop_day_of_week_'.$schema_id,
                                        'type'    => 'textarea',
                                        'attributes' => array(
                                                'placeholder' => 'Wednesday, Thursday'
                                         ),
                                        'note' => 'Note: Separate it by comma ( , )' ,
                                        'default' => ''
                                ),
                                array(
                                        'label'   => esc_html__( 'Time Of Day', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_eop_time_of_day_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => 'Morning'
                                         ),                                        
                                        'default' => ''
                                ),
                                array(
                                        'label'   => esc_html__( 'Number Of Credits', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_eop_number_of_credits_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => '30'
                                         ),                                        
                                        'default' => ''
                                ),
                                array(
                                        'label'   => esc_html__('Typical Credits PerTerm', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_eop_typical_credits_per_term_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => '12'
                                         ),                                        
                                        'default' => ''
                                ),
                                array(
                                        'label'   => esc_html__( 'Term Duration', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_eop_term_duration_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => 'P4M'
                                         ),                                        
                                        'default' => ''
                                ),
                                array(
                                        'label'   => esc_html__( 'Terms PerYear', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_eop_terms_per_year_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => '2'
                                         ),                                        
                                        'default' => ''
                                ),
                                array(
                                        'label'   => esc_html__( 'Maximum Enrollment', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_eop_maximum_enrollment_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => '30'
                                         ),                                        
                                        'default' => ''
                                ),
                                array(
                                        'label'   => esc_html__( 'Educational Program Mode', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_eop_educational_program_mode_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => 'IN_PERSON'
                                         ),                                        
                                        'default' => ''
                                ),
                                array(
                                        'label'   => esc_html__( 'Financial Aid Eligible', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_eop_financial_aid_eligible_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => 'PUBLIC_AID'
                                         ),                                        
                                        'default' => ''
                                ), 
                                array(
                                        'label'   => esc_html__( 'Provider Name', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_eop_provider_name_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => 'ACME Community College'
                                         ),
                                        'default' => ''
                                ),
                                array(
                                        'label'   => esc_html__( 'Provider Street Address', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_eop_provider_street_address_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => '123 Main Street'
                                         ),
                                        'default' => ''
                                ),
                                array(
                                        'label'   => esc_html__( 'Provider Address Locality', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_eop_provider_address_locality_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => 'Boston'
                                         ),
                                        'default' => ''
                                ),
                                array(
                                        'label'   => esc_html__( 'Provider Address Region', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_eop_provider_address_region_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => 'MA'
                                         ),
                                        'default' => ''
                                ),
                                array(
                                        'label'   => esc_html__( 'Provider Address Country', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_eop_provider_address_country_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => 'US'
                                         ),
                                        'default' => ''
                                ),
                                array(
                                        'label'   => esc_html__( 'Provider postalCode', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_eop_provider_postal_code_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => '02134'
                                         ),
                                        'default' => ''
                                ),
                                array(
                                        'label'   => esc_html__( 'Provider Telephone', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_eop_provider_telephone_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => '+1-555-123-4567'
                                         ),
                                        'default' => ''
                                )
                                );
                        break;  

                        case 'CreativeWork':                                        
                                $meta_field = array(
                                array(
                                'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_creativework_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'CreativeWork'   
                                ),
                                array(
                                        'label'   => esc_html__( 'Main Entity Of Page', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_creativework_main_entity_of_page_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => get_permalink()
                                ),
                                array(
                                        'label'   => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_creativework_url_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => get_permalink(),
                                ),    
                                array(
                                        'label'   => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_creativework_image_'.$schema_id,
                                        'type'    => 'media'                            
                                ),
                                array(
                                        'label'   => esc_html__( 'inLanguage', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_creativework_inlanguage_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => get_bloginfo('language'),
                                ),
                                array(
                                        'label'   => esc_html__( 'Headline', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_creativework_headline_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => saswp_get_the_title()
                                ),
                                array(
                                        'label'   => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_creativework_description_'.$schema_id,
                                        'type'    => 'textarea',
                                        'default' => saswp_strip_all_tags(get_the_excerpt())
                                ),
                                array(
                                        'label'   => esc_html__( 'Article Section', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_creativework_section_'.$schema_id,
                                        'type'    => 'textarea',
                                        'default' => saswp_strip_all_tags(get_the_excerpt())
                                ),      
                                array(
                                        'label'   => esc_html__( 'Keywords', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_creativework_keywords_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => saswp_get_the_tags()
                                ),    
                                array(
                                        'label'   => esc_html__( 'Date Published', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_creativework_date_published_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => get_the_date("Y-m-d")
                                ), 
                                array(
                                        'label'   => esc_html__( 'Date Modified', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_creativework_date_modified_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => get_the_modified_date("Y-m-d")
                                ),
                                array(
                                        'label'   => esc_html__( 'Author', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_creativework_author_global_mapping_'.$schema_id,
                                        'type'    => 'global_mapping'
                                ),
                                array(
                                        'label'   => esc_html__( 'Author Type', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_creativework_author_type_'.$schema_id,
                                        'type'    => 'select',
                                        'options' => array(
                                                'Person'           => 'Person',
                                                'Organization'     => 'Organization',                        
                                        )
                                ),
                                array(
                                        'label'   => esc_html__( 'Author Name', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_creativework_author_name_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => is_object($current_user) ? $current_user->display_name : ''
                                ),
                                array(
                                        'label'   => esc_html__( 'Author HonorificSuffix', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_creativework_author_honorific_suffix_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                                         ),
                                ),
                                array(
                                        'label'   => esc_html__( 'Author Description', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_creativework_author_description_'.$schema_id,
                                        'type'    => 'textarea',
                                        'default' => $author_desc
                                ),
                                array(
                                        'label'   => esc_html__( 'Author URL', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_creativework_author_url_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => $author_url
                                ),
                                array(
                                        'label' => esc_html__( 'Author Image', 'schema-and-structured-data-for-wp' ),
                                        'id' => 'saswp_creativework_author_image_'.$schema_id,
                                        'type' => 'media',
                                        'default' => isset($author_details['url']) ? $author_details['url']: ''
                                ),
                                array(
                                        'label'   => esc_html__( 'JobTitle', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_creativework_author_jobtitle_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => '',
                                        'attributes' => array(
                                                'placeholder' => 'eg: Editor in Chief'
                                         ),
                                ),
                                array(
                                        'label'   => esc_html__( 'ReviewedBy', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_creativework_reviewedby_global_mapping_'.$schema_id,
                                        'type'    => 'global_mapping'
                                ),
                                array(
                                        'label'   => esc_html__( 'ReviewedBy Type', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_creativework_reviewedby_type_'.$schema_id,
                                        'type'    => 'select',
                                        'options' => array(
                                                ""                => "Select",
                                                'Person'           => 'Person',
                                                'Organization'     => 'Organization',                        
                                        )
                                ),
                                array(
                                        'label'   => esc_html__( 'ReviewedBy Name', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_creativework_reviewedby_name_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => is_object($current_user) ? $current_user->display_name : ''
                                ),
                                array(
                                        'label'   => esc_html__( 'ReviewedBy HonorificSuffix', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_creativework_reviewedby_honorific_suffix_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                                         ),
                                ),
                                array(
                                        'label'   => esc_html__( 'ReviewedBy Description', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_creativework_reviewedby_description_'.$schema_id,
                                        'type'    => 'textarea',
                                        'default' => $author_desc
                                ),
                                array(
                                        'label'   => esc_html__( 'ReviewedBy URL', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_creativework_reviewedby_url_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => $author_url
                                ),

                                array(
                                        'label'   => esc_html__( 'Editor Type', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_creativework_editor_type_'.$schema_id,
                                        'type'    => 'select',
                                        'options' => array(
                                                 ""                => "Select",
                                                'Person'           => 'Person',
                                                'Organization'     => 'Organization',                        
                                        )
                                ),
                                array(
                                        'label'   => esc_html__( 'Editor Name', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_creativework_editor_name_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => is_object($current_user) ? $current_user->display_name : ''
                                ),    
                                array(
                                        'label'   => esc_html__( 'Editor HonorificSuffix', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_creativework_editor_honorific_suffix_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                                         ),
                                ),
                                array(
                                        'label'   => esc_html__( 'Editor Description', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_creativework_editor_description_'.$schema_id,
                                        'type'    => 'textarea',
                                        'default' => $author_desc
                                ),
                                array(
                                        'label'   => esc_html__( 'Editor URL', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_creativework_editor_url_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => $author_url
                                ),
                                array(
                                        'label' => esc_html__( 'Editor Image URL', 'schema-and-structured-data-for-wp' ),
                                        'id' => 'saswp_creativework_editor_image_'.$schema_id,
                                        'type' => 'media',
                                        'default' => isset($author_details['url']) ? $author_details['url']: ''
                                ),

                                array(
                                        'label'   => esc_html__( 'Organization Name', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_creativework_organization_name_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                                ),
                                array(
                                        'label'   => esc_html__( 'Organization Logo', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_creativework_organization_logo_'.$schema_id,
                                        'type'    => 'media',
                                        'default' => isset($sd_data['sd_logo']['url']) ? $sd_data['sd_logo']['url']:''
                                ),
                                array(
                                        'label'   => esc_html__( 'About', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_creativework_about_'.$schema_id,
                                        'type'    => 'textarea',
                                        'default' => '',
                                        'note'    => 'Note: If There are more than one About, Separate About list by comma ( , )',
                                        'attributes' => array(
                                                'placeholder' => 'eg: Apple is March 21 Announcements'
                                        ),
                                ),  
                                array(
                                        'label'   => esc_html__( 'AlumniOf', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_creativework_alumniof_'.$schema_id,
                                        'type'    => 'textarea',
                                        'default' => '',
                                        'note'    => 'Note: If There are more than one AlumniOf, Separate AlumniOf list by comma ( , )',
                                        'attributes' => array(
                                                'placeholder' => 'eg: City University of New York-Herbert H. Lehman College, Southern New Hampshire University'
                                        ),
                                ),    
                                array(
                                        'label'   => esc_html__( 'knowsAbout', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_creativework_knowsabout_'.$schema_id,
                                        'type'    => 'textarea',
                                        'default' => '',
                                        'note'    => 'Note: If There are more than one knows about, Separate knows about list by comma ( , )'       
                                ),
                                array(
                                        'label'   => esc_html__( 'Size', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_creativework_size_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => ''
                                ),
                                array(
                                        'label'   => esc_html__( 'License', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_creativework_license_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => ''
                                )                                                     
                                );
                        break;        

                        case 'SpecialAnnouncement':    
                                
                                $category_detail =get_the_category(get_the_ID());//$post->ID
                                $article_section = '';
                                
                                if($category_detail){

                                        foreach( $category_detail as $cd){
                                        
                                                $article_section =  $cd->cat_name;
                                        
                                        }

                                }                                

                                $meta_field = array( 
                                array(
                                        'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_special_announcement_id_'.$schema_id,
                                        'type'       => 'text',
                                        'default'    => 'SpecialAnnouncement'   
                                ),
                                    array(
                                            'label' => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                                            'id' => 'saswp_special_announcement_name_'.$schema_id,
                                            'type' => 'text',
                                            'default' => saswp_get_the_title()
                                    ),
                                    array(
                                            'label' => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                                            'id' => 'saswp_special_announcement_description_'.$schema_id,
                                            'type' => 'textarea',
                                            'default' => saswp_strip_all_tags(get_the_excerpt())
                                    ),
                                    array(
                                        'label' => esc_html__( 'Quarantine Guidelines', 'schema-and-structured-data-for-wp' ),
                                        'id' => 'saswp_special_announcement_quarantine_guidelines_'.$schema_id,
                                        'type' => 'text',
                                        'default' => get_permalink()
                                   ),
                                   array(
                                        'label' => esc_html__( 'NewsUpdates And Guidelines', 'schema-and-structured-data-for-wp' ),
                                        'id' => 'saswp_special_announcement_newsupdates_and_guidelines_'.$schema_id,
                                        'type' => 'text',
                                        'default' => get_permalink()
                                   ),     
                                   array(
                                        'label' => esc_html__( 'Disease Prevention Info', 'schema-and-structured-data-for-wp' ),
                                        'id' => 'saswp_special_announcement_disease_prevention_info_'.$schema_id,
                                        'type' => 'text',
                                        'default' => get_permalink()
                                   ),                        
                                    array(
                                            'label' => esc_html__( 'Keywords', 'schema-and-structured-data-for-wp' ),
                                            'id' => 'saswp_special_announcement_keywords_'.$schema_id,
                                            'type' => 'text',
                                            'default' => saswp_get_the_tags()
                                    ),
                                    array(
                                        'label' => esc_html__( 'Category', 'schema-and-structured-data-for-wp' ),
                                        'id'    => 'saswp_special_announcement_category_'.$schema_id,
                                        'type'  => 'text',
                                        'default' => get_permalink()
                                    ),
                                    array(
                                        'label' => esc_html__( 'Date Posted', 'schema-and-structured-data-for-wp' ),
                                        'id' => 'saswp_special_announcement_date_posted_'.$schema_id,
                                        'type' => 'text',
                                        'default' => get_the_date("Y-m-d")
                                    ),
                                    array(
                                        'label'   => esc_html__( 'Date Expires', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_special_announcement_date_expires_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => get_the_date("Y-m-d")
                                    ),    
                                    array(
                                            'label' => esc_html__( 'Date Published', 'schema-and-structured-data-for-wp' ),
                                            'id' => 'saswp_special_announcement_date_published_'.$schema_id,
                                            'type' => 'text',
                                            'default' => get_the_date("Y-m-d")
                                    ), 
                                    array(
                                            'label' => esc_html__( 'Date Modified', 'schema-and-structured-data-for-wp' ),
                                            'id' => 'saswp_special_announcement_date_modified_'.$schema_id,
                                            'type' => 'text',
                                            'default' => get_the_modified_date("Y-m-d")
                                    ),                           
                                array(
                                        'label'   => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_special_announcement_url_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => get_permalink(),
                                ),    
                                array(
                                        'label' => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                                        'id' => 'saswp_special_announcement_image_'.$schema_id,
                                        'type' => 'media'                            
                                ),                    
                                array(
                                        'label'   => esc_html__( 'Author Type', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_special_announcement_author_type_'.$schema_id,
                                        'type'    => 'select',
                                        'options' => array(
                                                'Person'           => 'Person',
                                                'Organization'     => 'Organization',                        
                                )
                                ),
                                array(
                                        'label' => esc_html__( 'Author Name', 'schema-and-structured-data-for-wp' ),
                                        'id' => 'saswp_special_announcement_author_name_'.$schema_id,
                                        'type' => 'text',
                                        'default' => is_object($current_user) ? $current_user->display_name : ''
                                ),
                                array(
                                        'label' => esc_html__( 'Author Description', 'schema-and-structured-data-for-wp' ),
                                        'id' => 'saswp_special_announcement_author_description_'.$schema_id,
                                        'type' => 'textarea',
                                        'default' => $author_desc
                                ),
                                array(
                                        'label'   => 'Author URL',
                                        'id'      => 'saswp_special_announcement_author_url_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => $author_url
                                ),    
                                array(
                                        'label' => esc_html__( 'Author URL', 'schema-and-structured-data-for-wp' ),
                                        'id' => 'saswp_special_announcement_organization_name_'.$schema_id,
                                        'type' => 'text',
                                        'default' => saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                                ),
                                array(
                                        'label' => esc_html__( 'Organization Logo', 'schema-and-structured-data-for-wp' ),
                                        'id'    => 'saswp_special_announcement_organization_logo_'.$schema_id,
                                        'type'  => 'media',
                                        'default' => isset($sd_data['sd_logo']['url']) ? $sd_data['sd_logo']['url']:''
                                ),
                                array(
                                        'label' => esc_html__( 'Announcement Location Type', 'schema-and-structured-data-for-wp' ),
                                        'id'    => 'saswp_special_announcement_location_type_'.$schema_id,
                                        'type'  => 'select',
                                        'options' => array(
                                                'CovidTestingFacility'  => 'CovidTestingFacility',
                                                'School'                => 'School',                                                
                                        )
                                ), 
                                array(
                                        'label' => esc_html__('Announcement Location Name', 'schema-and-structured-data-for-wp' ),
                                        'id'    => 'saswp_special_announcement_location_name_'.$schema_id,
                                        'type'  => 'text'                                        
                                ),
                                array(
                                        'label' => esc_html__( 'Announcement Location Street Address', 'schema-and-structured-data-for-wp' ),
                                        'id'    => 'saswp_special_announcement_location_street_address_'.$schema_id,
                                        'type'  => 'text'                                        
                                ),
                                array(
                                        'label' => esc_html__( 'Announcement Location Address Locality', 'schema-and-structured-data-for-wp' ),
                                        'id'    => 'saswp_special_announcement_location_address_locality_'.$schema_id,
                                        'type'  => 'text'                                        
                                ),
                                array(
                                        'label' => esc_html__( 'Announcement Location Address Region', 'schema-and-structured-data-for-wp' ),
                                        'id'    => 'saswp_special_announcement_location_address_region_'.$schema_id,
                                        'type'  => 'text'                                        
                                ),
                                array(
                                        'label' => esc_html__( 'Announcement Location Telephone', 'schema-and-structured-data-for-wp' ),
                                        'id'    => 'saswp_special_announcement_location_telephone_'.$schema_id,
                                        'type'  => 'text'                                        
                                ), 
                                array(
                                        'label' => esc_html__( 'Announcement Location URL', 'schema-and-structured-data-for-wp' ),
                                        'id'    => 'saswp_special_announcement_location_url_'.$schema_id,
                                        'type'  => 'text'                                        
                                ),
                                array(
                                        'label' => esc_html__( 'Announcement Location Image', 'schema-and-structured-data-for-wp' ),
                                        'id'    => 'saswp_special_announcement_location_image_'.$schema_id,
                                        'type'  => 'media'                                        
                                ), 
                                array(
                                        'label' => esc_html__( 'Announcement Location PriceRange', 'schema-and-structured-data-for-wp' ),
                                        'id'    => 'saswp_special_announcement_location_price_range_'.$schema_id,
                                        'type'  => 'text'                                        
                                )                                            
                                );
                                break;
                
                case 'Event':
                    
                    $event_type        = get_post_meta($schema_id, 'saswp_event_type', true);                         
                        
                    $meta_field = array(
                        array(
                            'label'   => esc_html__( 'Type', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_event_schema_type_'.$schema_id,
                            'type'    => 'select', 
                            'default' => $event_type,                          
                            'options' => array(
                                ''                 => 'Select Type (Optional)',
                                'BusinessEvent'    => 'BusinessEvent',
                                'ChildrensEvent'   => 'ChildrensEvent',
                                'ComedyEvent'      => 'ComedyEvent',
                                'CourseInstance'   => 'CourseInstance',
                                'DanceEvent'       => 'DanceEvent',
                                'DeliveryEvent'    => 'DeliveryEvent',
                                'EducationEvent'   => 'EducationEvent',
                                'EventSeries'      => 'EventSeries',
                                'ExhibitionEvent'  => 'ExhibitionEvent',
                                'Festival'         => 'Festival',
                                'FoodEvent'        => 'FoodEvent',
                                'LiteraryEvent'    => 'LiteraryEvent',
                                'MusicEvent'       => 'MusicEvent',
                                'PublicationEvent' => 'PublicationEvent',
                                'SaleEvent'        => 'SaleEvent',
                                'ScreeningEvent'   => 'ScreeningEvent',
                                'SocialEvent'      => 'SocialEvent',
                                'SportsEvent'      => 'SportsEvent',
                                'TheaterEvent'     => 'TheaterEvent',
                                'VisualArtsEvent'  => 'VisualArtsEvent'
                            ) 
                        ),                        
                        array(
                                'label'   => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_event_schema_id_'.$schema_id,
                                'type'    => 'text'                                
                        ),
                        array(
                                'label' => esc_html__( 'Event Status', 'schema-and-structured-data-for-wp' ),
                                'id'    => 'saswp_event_schema_status_'.$schema_id,
                                'type'  => 'select',
                                'options' => array(
                                        ''                      => 'Select Status',
                                        'EventScheduled'        => 'EventScheduled',                                        
                                        'EventPostponed'        => 'Postponed',
                                        'EventRescheduled'      => 'Rescheduled',
                                        'EventMovedOnline'      => 'MovedOnline', 
                                        'EventCancelled'        => 'Cancelled'
                                )                                 
                        ),
                        array(
                                'label' => esc_html__( 'Attendance Mode', 'schema-and-structured-data-for-wp' ),
                                'id'    => 'saswp_event_schema_attendance_mode_'.$schema_id,
                                'type'  => 'select',
                                'options' => array(
                                        ''                              => 'Select Attendance Mode',        
                                        'OfflineEventAttendanceMode'    => 'Offline',
                                        'OnlineEventAttendanceMode'     => 'Online', 
                                        'MixedEventAttendanceMode'      => 'Mixed',                                        
                                )                                 
                        ),    
                        array(
                                'label' => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_event_schema_name_'.$schema_id,
                                'type' => 'text',                                
                        ),
                        array(
                                'label' => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_event_schema_description_'.$schema_id,
                                'type' => 'textarea',                                
                        ),
                        array(
                                'label' => esc_html__( 'Virtual Location Name', 'schema-and-structured-data-for-wp' ),
                                'id'    => 'saswp_event_schema_virtual_location_name_'.$schema_id,
                                'type'  => 'text',                                
                        ),
                        array(
                                'label' => esc_html__( 'Virtual Location URL', 'schema-and-structured-data-for-wp' ),
                                'id'    => 'saswp_event_schema_virtual_location_url_'.$schema_id,
                                'type'  => 'text',                                
                        ),
                        array(
                                'label' => esc_html__( 'Location Name', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_event_schema_location_name_'.$schema_id,
                                'type' => 'text',                                
                        ),
                        array(
                                'label' => esc_html__( 'Location Street Address', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_event_schema_location_streetaddress_'.$schema_id,
                                'type' => 'text',                                
                        ),
                        array(
                                'label' => esc_html__( 'Location Locality', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_event_schema_location_locality_'.$schema_id,
                                'type' => 'text',                                
                        ),
                        array(
                                'label' => esc_html__( 'Location Region', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_event_schema_location_region_'.$schema_id,
                                'type' => 'text',                                
                        ),
                        array(
                                'label' => esc_html__( 'Location PostalCode', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_event_schema_location_postalcode_'.$schema_id,
                                'type' => 'text',                                
                        ),
                        array(
                                'label' => esc_html__( 'Location Country', 'schema-and-structured-data-for-wp' ),
                                'id'    => 'saswp_event_schema_location_country_'.$schema_id,
                                'type'  => 'text',                                
                        ),
                        array(
                                'label' => esc_html__( 'Previous Start Date', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_event_schema_previous_start_date_'.$schema_id,
                                'type' => 'text',                                
                        ),
                        array(
                                'label' => esc_html__('Start Date', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_event_schema_start_date_'.$schema_id,
                                'type' => 'text',                                
                        ),
                        array(
                                'label' => esc_html__( 'Start Time', 'schema-and-structured-data-for-wp' ),
                                'id'    => 'saswp_event_schema_start_time_'.$schema_id,
                                'type'  => 'text',                                
                        ),
                        array(
                                'label' => esc_html__( 'End Date', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_event_schema_end_date_'.$schema_id,
                                'type' => 'text',                                
                        ),
                        array(
                                'label' => esc_html__( 'End Time', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_event_schema_end_time_'.$schema_id,
                                'type' => 'text',                                
                        ),                        
                        array(
                                'label'   => esc_html__( 'Schedule Repeat Frequency', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_event_schema_schedule_repeat_frequency_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                        ''      => 'Choose',
                                        'P1W'   => 'Weekly',
                                        'P1M'   => 'Monthly',
                                        'P1D'   => 'EveryDay',                                        
                               )                                
                        ),
                        array(
                                'label' => esc_html__( 'Schedule byDay', 'schema-and-structured-data-for-wp' ),
                                'id'    => 'saswp_event_schema_schedule_by_day_'.$schema_id,
                                'type'  => 'textarea',
                                'attributes' => array(
                                        'placeholder' => 'Monday, Wednesday'
                                 ),
                                'note' => 'Note: Separate it by comma ( , )'                                  
                        ),
                        array(
                                'label' => esc_html__( 'Schedule byMonthDay', 'schema-and-structured-data-for-wp' ),
                                'id'    => 'saswp_event_schema_schedule_by_month_day_'.$schema_id,
                                'type'  => 'text',
                                'attributes' => array(
                                        'placeholder' => '1, 13, 24'
                                 )                                                                 
                        ),
                        array(
                                'label'  => esc_html__( 'Schedule Timezone', 'schema-and-structured-data-for-wp' ),
                                'id'     => 'saswp_event_schema_schedule_timezone_'.$schema_id,
                                'type'   => 'text',
                                'attributes' => array(
                                        'placeholder' => 'Europe/London'
                                 ),                                
                        ),
                        array(
                                'label' => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_event_schema_image_'.$schema_id,
                                'type' => 'media',                                
                        ),                        
                        array(
                                'label' => esc_html__( 'Price', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_event_schema_price_'.$schema_id,
                                'type' => 'number',                                
                        ),
                        array(
                                'label' => esc_html__( 'High Price', 'schema-and-structured-data-for-wp' ),
                                'id'    => 'saswp_event_schema_high_price_'.$schema_id,
                                'type'  => 'number',                                
                        ),
                        array(
                                'label' => esc_html__( 'Low Price', 'schema-and-structured-data-for-wp' ),
                                'id'    => 'saswp_event_schema_low_price_'.$schema_id,
                                'type'  => 'number',                                
                        ),
                        array(
                                'label' => esc_html__( 'Price Currency', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_event_schema_price_currency_'.$schema_id,
                                'type' => 'text',                                
                        ),
                        array(
                            'label'   => esc_html__( 'Availability', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_event_schema_availability_'.$schema_id,
                            'type'    => 'select',                           
                            'options' => array(
                                     'InStock'           => 'In Stock',
                                     'OutOfStock'        => 'Out Of Stock',
                                     'Discontinued'      => 'Discontinued',
                                     'PreOrder'          => 'Pre Order', 
                            ) 
                       ),
                        array(
                                'label' => esc_html__( 'Valid From', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_event_schema_validfrom_'.$schema_id,
                                'type' => 'text',                                
                        ),
                        array(
                                'label' => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_event_schema_url_'.$schema_id,
                                'type' => 'text',                                
                        ),                        
                        array(
                                'label' => esc_html__( 'Organization Name', 'schema-and-structured-data-for-wp' ),
                                'id'    => 'saswp_event_schema_organizer_name_'.$schema_id,
                                'type'  => 'text',                                
                        ),
                        array(
                                'label' => esc_html__( 'Organization URL', 'schema-and-structured-data-for-wp' ),
                                'id'    => 'saswp_event_schema_organizer_url_'.$schema_id,
                                'type'  => 'text',                                
                        ),
                        array(
                                'label' => esc_html__( 'Organization Phone', 'schema-and-structured-data-for-wp' ),
                                'id'    => 'saswp_event_schema_organizer_phone_'.$schema_id,
                                'type'  => 'text',                                
                        ),
                        array(
                                'label' => esc_html__( 'Organization Email', 'schema-and-structured-data-for-wp' ),
                                'id'    => 'saswp_event_schema_organizer_email_'.$schema_id,
                                'type'  => 'text',                                
                        ),
                        array(
                                'label' => esc_html__( 'Performer Name', 'schema-and-structured-data-for-wp' ),
                                'id'    => 'saswp_event_schema_performer_name_'.$schema_id,
                                'type'  => 'text',                                
                        ),
                    );
                    break;
                
                case 'TechArticle':                                        
                    $meta_field = array(
                        array(
                                'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),                                'id'         => 'saswp_tech_article_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'TechArticle'   
                        ),
                    array(
                            'label' => esc_html__( 'Main Entity Of Page', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_tech_article_main_entity_of_page_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink()
                    ),
                    array(
                            'label' => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_tech_article_image_'.$schema_id,
                            'type' => 'media',                            
                    ),
                    array(
                        'label'   => esc_html__( 'inLanguage', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_tech_article_inlanguage_'.$schema_id,
                        'type'    => 'text',
                        'default' => get_bloginfo('language'),
                   ),
                    array(
                            'label' => esc_html__( 'Headline', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_tech_article_headline_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_get_the_title()
                    ),
                    array(
                            'label' => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_tech_article_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => saswp_strip_all_tags(get_the_excerpt())
                    ) ,
                    array(
                            'label'   => esc_html__( 'hasPart', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_tech_article_haspart_'.$schema_id,
                            'type'    => 'text',
                            'is_template_attr' => 'yes',
                    ),
                    array(
                            'label'   => esc_html__( 'isPartOf', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_tech_article_ispartof_'.$schema_id,
                            'type'    => 'text',
                            'is_template_attr' => 'yes',
                    ), 
                    array(
                            'label' => esc_html__( 'Keywords', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_tech_article_keywords_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_get_the_tags()
                    ),     
                    array(
                            'label' => esc_html__( 'Date Published', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_tech_article_date_published_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_date("Y-m-d")
                    ), 
                    array(
                            'label' => esc_html__( 'Date Modified', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_tech_article_date_modified_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_modified_date("Y-m-d")
                    ),
                    array(
                        'label'   => esc_html__( 'Author', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_tech_article_author_global_mapping_'.$schema_id,
                        'type'    => 'global_mapping'
                    ),
                    array(
                        'label'   => esc_html__( 'Author Type', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_tech_article_author_type_'.$schema_id,
                        'type'    => 'select',
                        'options' => array(
                                'Person'           => 'Person',
                                'Organization'     => 'Organization',                        
                       )
                    ),
                    array(
                            'label' => esc_html__( 'Author Name', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_tech_article_author_name_'.$schema_id,
                            'type' => 'text',
                            'default' => is_object($current_user) ? $current_user->display_name : ''
                    ),
                    array(
                        'label'   => esc_html__( 'Author HonorificSuffix', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_tech_article_author_honorific_suffix_'.$schema_id,
                        'type'    => 'text',
                        'attributes' => array(
                                'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                         ),
                    ),
                    array(
                            'label' => esc_html__( 'Author Description', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_tech_article_author_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => $author_desc
                    ), 
                    array(
                            'label'   => esc_html__( 'Author URL', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_tech_article_author_url_'.$schema_id,
                            'type'    => 'text',
                            'default' => $author_url
                    ),  
                    array(
                        'label' => esc_html__( 'Author Image URL', 'schema-and-structured-data-for-wp' ),
                        'id' => 'saswp_tech_article_author_image_'.$schema_id,
                        'type' => 'media',
                        'default' => isset($author_details['url']) ? $author_details['url']: ''
                    ),
                    array(
                            'label' => esc_html__( 'Author Social Profile', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_tech_article_author_social_profile_'.$schema_id,
                            'type' => 'textarea',
                            'default' => '',
                            'note'    => 'Note: If There are more than one social profiles, Separate them by comma ( , )',
                    ),
                    array(
                        'label'   => esc_html__( 'JobTitle', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_tech_article_author_jobtitle_'.$schema_id,
                        'type'    => 'text',
                        'default' => '',
                        'attributes' => array(
                                'placeholder' => 'eg: Editor in Chief'
                         ),
                    ),
                    array(
                        'label'   => esc_html__( 'ReviewedBy', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_tech_article_reviewedby_global_mapping_'.$schema_id,
                        'type'    => 'global_mapping'
                    ),
                    array(
                        'label'   => esc_html__( 'ReviewedBy Type', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_tech_article_reviewedby_type_'.$schema_id,
                        'type'    => 'select',
                        'options' => array(
                                ""                => "Select",
                                'Person'           => 'Person',
                                'Organization'     => 'Organization',                        
                       )
                    ),
                    array(
                            'label' => esc_html__( 'ReviewedBy Name', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_tech_article_reviewedby_name_'.$schema_id,
                            'type' => 'text',
                            'default' => is_object($current_user) ? $current_user->display_name : ''
                    ),
                    array(
                        'label'   => esc_html__( 'ReviewedBy HonorificSuffix', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_tech_article_reviewedby_honorific_suffix_'.$schema_id,
                        'type'    => 'text',
                        'attributes' => array(
                                'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                         ),
                    ),
                    array(
                            'label' => esc_html__( 'ReviewedBy Description', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_tech_article_reviewedby_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => $author_desc
                    ), 
                    array(
                            'label'   => esc_html__( 'ReviewedBy URL', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_tech_article_reviewedby_url_'.$schema_id,
                            'type'    => 'text',
                            'default' => $author_url
                    ),
                    array(
                        'label'   => esc_html__( 'Editor Type', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_tech_article_editor_type_'.$schema_id,
                        'type'    => 'select',
                        'options' => array(
                                 ""                => "Select",
                                'Person'           => 'Person',
                                'Organization'     => 'Organization',                        
                        )
                     ),
                    array(
                        'label'   => esc_html__( 'Editor Name', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_tech_article_editor_name_'.$schema_id,
                        'type'    => 'text',
                        'default' => is_object($current_user) ? $current_user->display_name : ''
                    ),
                    array(
                        'label'   => esc_html__( 'Editor HonorificSuffix', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_tech_article_editor_honorific_suffix_'.$schema_id,
                        'type'    => 'text',
                        'attributes' => array(
                                'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                         ),
                    ), 
                    array(
                        'label'   => esc_html__( 'Editor Description', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_tech_article_editor_description_'.$schema_id,
                        'type'    => 'textarea',
                        'default' => $author_desc
                    ),
                    array(
                        'label'   => esc_html__( 'Editor URL', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_tech_article_editor_url_'.$schema_id,
                        'type'    => 'text',
                        'default' => $author_url
                     ),
                     array(
                        'label' => esc_html__( 'Editor Image URL', 'schema-and-structured-data-for-wp' ),
                        'id' => 'saswp_tech_article_editor_image_'.$schema_id,
                        'type' => 'media',
                        'default' => isset($author_details['url']) ? $author_details['url']: ''
                    ),
                    array(
                            'label' => esc_html__( 'Organization Name', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_tech_article_organization_name_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                    ),
                    array(
                            'label' => esc_html__( 'Organization Logo', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_tech_article_organization_logo_'.$schema_id,
                            'type' => 'media',
                            'default' => isset($sd_data['sd_logo']) ? $sd_data['sd_logo']['url']:''
                    ),
                    array(
                        'label'   => esc_html__( 'About', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_tech_article_about_'.$schema_id,
                        'type'    => 'textarea',
                        'default' => '',
                        'note'    => 'Note: If There are more than one About, Separate About list by comma ( , )',
                        'attributes' => array(
                                'placeholder' => 'eg: Apple is March 21 Announcements'
                        ),
                    ), 
                    array(
                        'label'   => esc_html__( 'AlumniOf', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_tech_article_alumniof_'.$schema_id,
                        'type'    => 'textarea',
                        'default' => '',
                        'note'    => 'Note: If There are more than one AlumniOf, Separate AlumniOf list by comma ( , )',
                        'attributes' => array(
                                'placeholder' => 'eg: City University of New York-Herbert H. Lehman College, Southern New Hampshire University'
                        ),
                    ),  
                    array(
                        'label'   => esc_html__( 'knowsAbout', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_tech_article_knowsabout_'.$schema_id,
                        'type'    => 'textarea',
                        'default' => '',
                        'note'    => 'Note: If There are more than one knows about, Separate knows about list by comma ( , )'       
                    ),
                    array(
                            'label' => esc_html__( 'Same As', 'schema-and-structured-data-for-wp' ),
                            'id'    => 'saswp_tech_article_same_as_'.$schema_id,
                            'type'  => 'textarea',
                            'attributes' => array(
                                'placeholder' => 'Example, Example2'
                            ),
                            'note' => 'Note: Separate same as list by comma ( , )'                     
                        ),                        
                    array(
                            'label' => esc_html__( 'Speakable', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_tech_article_speakable_'.$schema_id,
                            'type' => 'checkbox'
                    )
                    );
                    break;
                
                case 'Course':                                        
                    $meta_field = array(
                   array(
                           'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                           'id'         => 'saswp_course_id_'.$schema_id,
                           'type'       => 'text',
                           'default'    => 'Course'   
                        ),
                    array(
                            'label'   => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_course_name_'.$schema_id,
                            'type'    => 'text',
                            'default' => saswp_get_the_title()
                    ),
                    array(
                            'label'   => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_course_description_'.$schema_id,
                            'type'    => 'textarea',
                            'default' => is_object($post) ? $post->post_excerpt : ''
                    ),
                    array(
                        'label'   => esc_html__( 'Duration', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_course_duration_'.$schema_id,
                        'type'    => 'text'                        
                   ),
                   array(
                        'label'   => esc_html__( 'Course Code', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_course_code_'.$schema_id,
                        'type'    => 'text'                        
                   ),
                   array(
                        'label'   => esc_html__( 'Content Location Name', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_course_content_location_name_'.$schema_id,
                        'type'    => 'text'                        
                   ),                   
                   array(
                        'label'   => esc_html__( 'Content Location Locality', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_course_content_location_locality_'.$schema_id,
                        'type'    => 'text'                        
                   ),
                   array(
                        'label'   => esc_html__( 'Content Location Region', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_course_content_location_region_'.$schema_id,
                        'type'    => 'text'                        
                   ),
                   array(
                        'label'   => esc_html__( 'Content Location Country', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_course_content_location_country_'.$schema_id,
                        'type'    => 'text'                        
                   ),
                   array(
                        'label'   => esc_html__( 'Content Location Postal Code', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_course_content_location_postal_code_'.$schema_id,
                        'type'    => 'text'                        
                   ),
                    array(
                            'label'   => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_course_url_'.$schema_id,
                            'type'    => 'text',
                            'default' => get_permalink()
                    ),                     
                    array(
                            'label'   => esc_html__( 'Date Published', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_course_date_published_'.$schema_id,
                            'type'    => 'text',
                            'default' => get_the_date("Y-m-d")
                    ), 
                    array(
                            'label'   => esc_html__( 'Date Modified', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_course_date_modified_'.$schema_id,
                            'type'    => 'text',
                            'default' => get_the_modified_date("Y-m-d")
                    ),                    
                    array(
                            'label'   => esc_html__( 'Provider Name', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_course_provider_name_'.$schema_id,
                            'type'    => 'text',
                            'default' => get_bloginfo()
                    ),
                    array(
                            'label'   => esc_html__( 'Provider SameAs', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_course_sameas_'.$schema_id,
                            'type'    => 'text',
                            'default' => get_home_url() 
                    ),
                    array(
                            'label'   => esc_html__( 'Offer Category', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_course_offer_category_'.$schema_id,
                            'type'    => 'text',
                    ),
                    array(
                            'label'   => esc_html__( 'Offer Price', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_course_offer_price_'.$schema_id,
                            'type'    => 'number',
                    ),
                    array(
                            'label'   => esc_html__( 'Offer Currency', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_course_offer_currency_'.$schema_id,
                            'type'    => 'text',
                    ),
                    array(
                            'label' => esc_html__( 'Aggregate Rating', 'schema-and-structured-data-for-wp' ),
                            'id'    => 'saswp_course_enable_rating_'.$schema_id,
                            'type'  => 'checkbox',                            
                    ),
                    array(
                            'label' => esc_html__( 'Rating', 'schema-and-structured-data-for-wp' ),
                            'id'    => 'saswp_course_rating_'.$schema_id,
                            'type'  => 'text',                            
                    ),
                    array(
                            'label' => esc_html__( 'Number of Reviews', 'schema-and-structured-data-for-wp' ),
                            'id'    => 'saswp_course_review_count_'.$schema_id,
                            'type'  => 'text',                            
                    )                                                     
                    );
                    break;
                
                case 'DiscussionForumPosting':                                        
                    $meta_field = array(
                   array(
                        'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_dfp_id_'.$schema_id,
                        'type'       => 'text',
                        'default'    => 'DiscussionForumPosting'   
                        ),
                    array(
                            'label'   => esc_html__( 'mainEntityOfPage', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_dfp_main_entity_of_page_'.$schema_id,
                            'type'    => 'text',
                            'default' => get_permalink()
                    ),    
                    array(
                            'label' => esc_html__( 'Headline', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_dfp_headline_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_get_the_title()
                    ),
                    array(
                            'label' => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_dfp_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => saswp_strip_all_tags(get_the_excerpt())
                    ) ,    
                    array(
                            'label' => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_dfp_url_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink()
                    ),
                    array(
                            'label' => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_dfp_image_'.$schema_id,
                            'type' => 'media',                            
                    ),    
                    array(
                            'label' => esc_html__( 'Date Published', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_dfp_date_published_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_date("Y-m-d")
                    ), 
                    array(
                            'label' => esc_html__( 'Date Modified', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_dfp_date_modified_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_modified_date("Y-m-d")
                    ),
                    array(
                        'label'   => esc_html__( 'Author Type', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_dfp_author_type_'.$schema_id,
                        'type'    => 'select',
                        'options' => array(
                                'Person'           => 'Person',
                                'Organization'     => 'Organization',                        
                       )
                    ),
                    array(
                            'label' => esc_html__( 'Author Name', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_dfp_author_name_'.$schema_id,
                            'type' => 'text',
                            'default' => is_object($current_user) ? $current_user->display_name : ''
                    ),
                    array(
                            'label'   => esc_html__( 'Author Description', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_dfp_author_description_'.$schema_id,
                            'type'    => 'textarea',
                            'default' => $author_desc
                    ),  
                    array(
                            'label'   => esc_html__( 'Author URL', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_dfp_author_url_'.$schema_id,
                            'type'    => 'text',
                            'default' => $author_url
                    ),    
                    array(
                            'label'   => esc_html__( 'Organization Name', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_dfp_organization_name_'.$schema_id,
                            'type'    => 'text',
                            'default' => saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                    ),
                    array(
                            'label'   => esc_html__( 'Organization Logo', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_dfp_organization_logo_'.$schema_id,
                            'type'    => 'media',
                            'default' => isset($sd_data['sd_logo']['url']) ? $sd_data['sd_logo']['url'] : ''
                    ),    
                        
                    );
                    break;
                
                case 'Recipe':
                    $meta_field = array(
                        array(
                                'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_recipe_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'recipe'   
                        ),
                    array(
                            'label' => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_recipe_url_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink(),
                    ),
                    array(
                            'label' => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_recipe_name_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_get_the_title()
                    ),
                    array(
                        'label' => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                        'id' => 'saswp_recipe_description_'.$schema_id,
                        'type' => 'textarea',
                        'default' => saswp_strip_all_tags(get_the_excerpt())
                    ),
                    array(
                        'label' => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                        'id' => 'saswp_recipe_image_'.$schema_id,
                        'type' => 'media'                        
                   ),
                    array(
                            'label' => esc_html__( 'Date Published', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_recipe_date_published_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_date("Y-m-d")
                    ),
                    array(
                            'label' => esc_html__( 'Date Modified', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_recipe_date_modified_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_modified_date("Y-m-d")
                    ),                    
                    array(
                            'label' => esc_html__( 'Main Entity Id', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_recipe_main_entity_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink()
                    ),
                    array(
                        'label'   => esc_html__( 'Author Type', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_recipe_author_type_'.$schema_id,
                        'type'    => 'select',
                        'options' => array(
                                'Person'           => 'Person',
                                'Organization'     => 'Organization',                        
                       )
                    ),
                    array(
                            'label' => esc_html__( 'Author Name', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_recipe_author_name_'.$schema_id,
                            'type' => 'text',
                            'default' => is_object($current_user) ? $current_user->display_name : ''
                    ),
                    array(
                            'label' => esc_html__( 'Author Description', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_recipe_author_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => $author_desc
                    ),
                    array(
                            'label'   => esc_html__( 'Author URL', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_recipe_author_url_'.$schema_id,
                            'type'    => 'text',
                            'default' => $author_url
                    ),    
                    array(
                            'label' => esc_html__( 'Author Image', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_recipe_author_image_'.$schema_id,
                            'type' => 'media',
                            'default' => isset($author_details['url']) ? $author_details['url'] : ''
                    ),
                    array(
                            'label' => esc_html__( 'Organization Name', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_recipe_organization_name_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                    ),
                    array(
                            'label' => esc_html__( 'Organization Logo', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_recipe_organization_logo_'.$schema_id,
                            'type' => 'media',
                            'default' => isset($sd_data['sd_logo']['url']) ? $sd_data['sd_logo']['url'] : ''
                    ),                                                                                            
                    array(
                            'label' => esc_html__( 'Prepare Time', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_recipe_preptime_'.$schema_id,
                            'type' => 'text', 
                            'attributes' => array(
                                'placeholder' => 'PT20M'
                            ),
                    ),    
                    array(
                            'label' => esc_html__( 'Cook Time', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_recipe_cooktime_'.$schema_id,
                            'type' => 'text', 
                            'attributes' => array(
                                'placeholder' => 'PT30M'
                            ),
                    ),
                    array(
                            'label' => esc_html__( 'Total Time', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_recipe_totaltime_'.$schema_id,
                            'type' => 'text', 
                            'attributes' => array(
                                'placeholder' => 'PT50M'
                            ),
                    ),    
                    array(
                            'label' => esc_html__( 'Keywords', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_recipe_keywords_'.$schema_id,
                            'type' => 'text',  
                            'attributes' => array(
                                'placeholder' => 'cake for a party, coffee'
                            ),
                    ),    
                    array(
                            'label' => esc_html__( 'Recipe Yield', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_recipe_recipeyield_'.$schema_id,
                            'type' => 'text', 
                            'attributes' => array(
                                'placeholder' => '10 servings'
                            ),
                    ),    
                    array(
                            'label' => esc_html__( 'Recipe Category', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_recipe_category_'.$schema_id,
                            'type' => 'text',
                            'attributes' => array(
                                'placeholder' => 'Dessert'
                            ),
                    ),
                    array(
                            'label' => esc_html__( 'Recipe Cuisine', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_recipe_cuisine_'.$schema_id,
                            'type' => 'text', 
                            'attributes' => array(
                                'placeholder' => 'American'
                            ),
                    ),    
                    array(
                            'label' => esc_html__( 'Calories', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_recipe_nutrition_'.$schema_id,
                            'type' => 'text',
                            'attributes' => array(
                                'placeholder' => '270 calories'
                            ),
                    ),                    
                    array(
                        'label'         => esc_html__( 'Protein', 'schema-and-structured-data-for-wp' ),
                        'id'            => 'saswp_recipe_protein_'.$schema_id,
                        'type'          => 'text',
                        'attributes' => array(
                            'placeholder' => '270 grams'
                        ),
                    ),
                    array(
                        'label'         => esc_html__( 'FAT', 'schema-and-structured-data-for-wp' ),
                        'id'            => 'saswp_recipe_fat_'.$schema_id,
                        'type'          => 'text',
                        'attributes'    => array(
                            'placeholder' => '270 grams'
                        ),
                    ),
                    array(
                        'label'   => esc_html__( 'Fiber', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_recipe_fiber_'.$schema_id,
                        'type'    => 'text',
                        'attributes' => array(
                            'placeholder' => '270 grams'
                        ),
                    ),
                    array(
                        'label'         => esc_html__( 'Sodium', 'schema-and-structured-data-for-wp' ),
                        'id'            => 'saswp_recipe_sodium_'.$schema_id,
                        'type'          => 'text',
                        'attributes' => array(
                            'placeholder' => '270 grams'
                        ),
                    ),
                    array(
                        'label'   => esc_html__( 'Sugar', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_recipe_sugar_'.$schema_id,
                        'type'    => 'text',
                        'attributes' => array(
                            'placeholder' => '270 grams'
                        ),
                    ),
                    array(
                        'label' => esc_html__( 'Carbohydrate', 'schema-and-structured-data-for-wp' ),
                        'id'    => 'saswp_recipe_carbohydrate_'.$schema_id,
                        'type'  => 'text',
                        'attributes' => array(
                            'placeholder' => '270 grams'
                        ),
                    ),
                    array(
                        'label' => esc_html__( 'Cholesterol', 'schema-and-structured-data-for-wp' ),
                        'id'    => 'saswp_recipe_cholesterol_'.$schema_id,
                        'type'  => 'text',
                        'attributes' => array(
                            'placeholder' => '270 grams'
                        ),
                    ),
                    array(
                        'label' => esc_html__( 'Saturated Fat', 'schema-and-structured-data-for-wp' ),
                        'id'    => 'saswp_recipe_saturated_fat_'.$schema_id,
                        'type'  => 'text',
                        'attributes' => array(
                            'placeholder' => '270 grams'
                        ),
                    ),
                    array(
                        'label' => esc_html__( 'Unsaturated Fat', 'schema-and-structured-data-for-wp' ),
                        'id'    => 'saswp_recipe_unsaturated_fat_'.$schema_id,
                        'type'  => 'text',
                        'attributes' => array(
                            'placeholder' => '270 grams'
                        ),
                    ),
                    array(
                        'label' => esc_html__( 'Trans Fat', 'schema-and-structured-data-for-wp' ),
                        'id'    => 'saswp_recipe_trans_fat_'.$schema_id,
                        'type'  => 'text',
                        'attributes' => array(
                            'placeholder' => '270 grams'
                        ),
                    ),
                    array(
                        'label' => esc_html__( 'Serving Size', 'schema-and-structured-data-for-wp' ),
                        'id'    => 'saswp_recipe_serving_size_'.$schema_id,
                        'type'  => 'text',
                        'attributes' => array(
                            'placeholder' => '370 grams'
                        ),
                    ),
                    array(
                            'label' => esc_html__( 'Recipe Ingredient', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_recipe_ingredient_'.$schema_id,
                            'type' => 'textarea',
                            'attributes' => array(
                                'placeholder' => '2 cups of flour; 3/4 cup white sugar;'
                            ),
                            'note' => 'Note: Separate Ingredient list by semicolon ( ; )'  
                    ),                     
                    array(
                            'label' => esc_html__( 'Video Name', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_recipe_video_name_'.$schema_id,
                            'type' => 'text', 
                            'attributes' => array(
                                'placeholder' => 'Video Name'
                            ),
                    ),                    
                    array(
                            'label' => esc_html__( 'Video Description', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_recipe_video_description_'.$schema_id,
                            'type' => 'textarea', 
                            'attributes' => array(
                                'placeholder' => 'Video Description'
                            ),
                    ),
                    array(
                            'label' => esc_html__( 'Video ThumbnailUrl', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_recipe_video_thumbnailurl_'.$schema_id,
                            'type' => 'media',
                            
                    ),
                    array(
                            'label' => esc_html__( 'Video ContentUrl', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_recipe_video_contenturl_'.$schema_id,
                            'type' => 'text',                            
                            'attributes' => array(
                                'placeholder' => 'http://www.example.com/video123.mp4'
                            ),
                    ),
                    array(
                            'label' => esc_html__( 'Video EmbedUrl', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_recipe_video_embedurl_'.$schema_id,
                            'type' => 'text', 
                            'attributes' => array(
                                'placeholder' => 'http://www.example.com/videoplayer?video=123'
                            ),
                    ),
                    array(
                            'label' => esc_html__( 'Video Upload Date', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_recipe_video_upload_date_'.$schema_id,
                            'type' => 'text', 
                            'attributes' => array(
                                'placeholder' => '2018-12-18'
                            ),
                    ),
                    array(
                            'label' => esc_html__( 'Video Duration', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_recipe_video_duration_'.$schema_id,
                            'type' => 'text',
                            'attributes' => array(
                                'placeholder' => 'PT1M33S'
                            ),
                    ), 
                    array(
                        'label' => esc_html__( 'Recipe Instructions', 'schema-and-structured-data-for-wp' ),
                        'id' => 'saswp_recipe_instructions_'.$schema_id,
                        'type' => 'textarea',
                        'attributes' => array(
                            'placeholder' => 'Preheat the oven to 350 degrees F. Grease and flour a 9x9 inch pan; large bowl, combine flour, sugar, baking powder, and salt. pan.;'
                        ),
                        'note' => 'Note: Separate Instructions step by semicolon ( ; ). If you want to add images. Use below repeater "Add Recipe Instructions"'  
                   ),   
                    array(
                        'label' => esc_html__( 'Aggregate Rating', 'schema-and-structured-data-for-wp' ),
                        'id' => 'saswp_recipe_schema_enable_rating_'.$schema_id,
                        'type' => 'checkbox',                            
                    ),
                    array(
                        'label' => esc_html__( 'Rating', 'schema-and-structured-data-for-wp' ),
                        'id' => 'saswp_recipe_schema_rating_'.$schema_id,
                        'type' => 'text',                            
                    ),
                    array(
                        'label' => esc_html__( 'Number of Reviews', 'schema-and-structured-data-for-wp' ),
                        'id' => 'saswp_recipe_schema_review_count_'.$schema_id,
                        'type' => 'text',                            
                    )

                    );
                    break;

                    case 'PsychologicalTreatment':                                                                                                            
                        
                        $meta_field = array(
                        array(
                                'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_psychological_treatment_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'PsychologicalTreatment'   
                        ), 
                        array(
                                'label'   => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_psychological_treatment_name_'.$schema_id,
                                'type'    => 'text'                                                 
                        ),
                        array(
                                'label'   => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_psychological_treatment_description_'.$schema_id,
                                'type'    => 'textarea'                                                 
                        ),
                        array(
                                'label'   => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_psychological_treatment_url_'.$schema_id,
                                'type'    => 'text'                                                 
                        ),
                        array(
                                'label'   => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_psychological_treatment_image_'.$schema_id,
                                'type'    => 'media'                                                 
                        ),
                        array(
                                'label'   => esc_html__( 'Drug', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_psychological_treatment_drug_'.$schema_id,
                                'type'    => 'text'                                                 
                        ),    
                        array(
                                'label'   => esc_html__( 'Body Location', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_psychological_treatment_body_location_'.$schema_id,
                                'type'    => 'text'                                                 
                        ),
                        array(
                                'label'   => esc_html__( 'Preparation', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_psychological_treatment_preparation_'.$schema_id,
                                'type'    => 'text'                                                 
                        ),
                        array(
                                'label'   => esc_html__( 'Followup', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_psychological_treatment_followup_'.$schema_id,
                                'type'    => 'text'                                                 
                        ),
                        array(
                                'label'   => esc_html__( 'How Performed', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_psychological_treatment_how_performed_'.$schema_id,
                                'type'    => 'text'                                                 
                        ),
                        array(
                                'label'   => esc_html__( 'Procedure Type', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_psychological_treatment_procedure_type_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                        'Surgical'           => 'Surgical',
                                        'Noninvasive'        => 'Noninvasive',
                                        'Percutaneous'       => 'Percutaneous'                                        
                               )                                                         
                        ) ,
                        array(
                                'label'   => esc_html__( 'MedicalCode', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_psychological_treatment_medical_code_'.$schema_id,
                                'type'    => 'text'                                                 
                        ), 
                        array(
                                'label'   => esc_html__( 'Additional Type', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_psychological_treatment_additional_type_'.$schema_id,
                                'type'    => 'text'                                                 
                        ),                              
                            
                        );
                        
                        break;

                    case 'RealEstateListing':                                                                                                            
                        
                        $meta_field = array(
                        array(
                                'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_real_estate_listing_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'RealEstateListing'   
                        ),
                        array(
                                'label'   => esc_html__( 'Date Posted', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_real_estate_listing_date_posted_'.$schema_id,
                                'type'    => 'text', 
                                'default' => get_the_date("Y-m-d")                                
                        ),    
                        array(
                                'label'   => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_real_estate_listing_name_'.$schema_id,
                                'type'    => 'text', 
                                'default' => saswp_get_the_title()                                
                        ),
                        array(
                                'label'   => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_real_estate_listing_url_'.$schema_id,
                                'type'    => 'text',     
                                'default' => get_permalink()
                        ),    
                        array(
                                'label'   => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_real_estate_listing_description_'.$schema_id,
                                'type'    => 'textarea', 
                                'default' => saswp_strip_all_tags(get_the_excerpt())
                        ), 
                        array(
                                'label'    => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                                'id'       => 'saswp_real_estate_listing_image_'.$schema_id,
                                'type'     => 'media',                           
                         ),                        
                            array(
                                'label'   => esc_html__( 'Price', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_real_estate_listing_price_'.$schema_id,
                                'type'    => 'text'                                
                         ),
                         array(
                                'label'   => esc_html__( 'Currency', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_real_estate_listing_currency_'.$schema_id,
                                'type'    => 'text'                                
                          ),
                            array(
                                'label'   => esc_html__( 'Price Valid From', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_real_estate_listing_validfrom_'.$schema_id,
                                'type'    => 'text'                                
                           ),                            
                            array(
                                'label'   => esc_html__( 'Availability', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_real_estate_listing_availability_'.$schema_id,
                                'type'    => 'select',                            
                                'options' => array(
                                         'InStock'           => 'In Stock',
                                         'OutOfStock'        => 'Out Of Stock',
                                         'Discontinued'      => 'Discontinued',
                                         'PreOrder'          => 'Pre Order', 
                                )                                
                                ), 
                                array(
                                        'label' => esc_html__( 'Availability', 'schema-and-structured-data-for-wp' ),
                                        'id' => 'saswp_real_estate_listing_location_name_'.$schema_id,
                                        'type' => 'text',                                
                                ),
                                array(
                                        'label' => esc_html__( 'Location Street Address', 'schema-and-structured-data-for-wp' ),
                                        'id' => 'saswp_real_estate_listing_streetaddress_'.$schema_id,
                                        'type' => 'text',                                
                                ),
                                array(
                                        'label' => esc_html__( 'Location Locality', 'schema-and-structured-data-for-wp' ),
                                        'id' => 'saswp_real_estate_listing_locality_'.$schema_id,
                                        'type' => 'text',                                
                                ),
                                array(
                                        'label' => esc_html__( 'Location Region', 'schema-and-structured-data-for-wp' ),
                                        'id' => 'saswp_real_estate_listing_region_'.$schema_id,
                                        'type' => 'text',                                
                                ),
                                array(
                                        'label' => esc_html__( 'Location PostalCode', 'schema-and-structured-data-for-wp' ),
                                        'id' => 'saswp_real_estate_listing_postalcode_'.$schema_id,
                                        'type' => 'text',                                
                                ),
                                array(
                                        'label' => esc_html__( 'Location Country', 'schema-and-structured-data-for-wp' ),
                                        'id'    => 'saswp_real_estate_listing_country_'.$schema_id,
                                        'type'  => 'text',                                
                                ),
                                array(
                                        'label' => esc_html__( 'Location Phone', 'schema-and-structured-data-for-wp' ),
                                        'id'    => 'saswp_real_estate_listing_phone_'.$schema_id,
                                        'type'  => 'text',                                
                                )                                                                                
                            
                        );
                        
                        break;
                        
                        case 'RentAction':                                                                                                            
                        
                                $meta_field = array(
                                        array(
                                                'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                                'id'         => 'saswp_rent_action_id_'.$schema_id,
                                                'type'       => 'text',
                                                'default'    => 'RentAction'   
                                        ), 
                                        array(
                                                'label'   => esc_html__( 'Agent Name', 'schema-and-structured-data-for-wp' ),
                                                'id'      => 'saswp_rent_action_agent_name_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        ),
                                        array(
                                                'label'   => esc_html__( 'Land Lord Name', 'schema-and-structured-data-for-wp' ),
                                                'id'      => 'saswp_rent_action_land_lord_name_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        ),
                                        array(
                                                'label'   => esc_html__( 'Object Name', 'schema-and-structured-data-for-wp' ),
                                                'id'      => 'saswp_rent_action_object_name_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        )    
                                                                    
                                );
                                
                                break;  

                        case 'Audiobook':                                                                                                            
                        
                                $meta_field = array(
                                        array(
                                                'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                                'id'         => 'saswp_audiobook_id_'.$schema_id,
                                                'type'       => 'text',
                                                'default'    => 'Audiobook'   
                                        ),
                                        array(
                                                'label'   => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                                                'id'      => 'saswp_audiobook_name_'.$schema_id,
                                                'type'    => 'text',
                                                'default' => get_the_title()                                                                
                                        ),
                                        array(
                                                'label'   => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                                                'id'      => 'saswp_audiobook_description_'.$schema_id,
                                                'type'    => 'textarea',
                                                'default' => saswp_strip_all_tags(get_the_excerpt())                                                                  
                                        ),
                                        array(
                                                'label'   => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                                                'id'      => 'saswp_audiobook_url_'.$schema_id,
                                                'type'    => 'text', 
                                                'default' => get_permalink()                                                               
                                        ),
                                        array(
                                                'label'   => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                                                'id'      => 'saswp_audiobook_image_'.$schema_id,
                                                'type'    => 'media'                                                                
                                        ),
                                        array(
                                                'label'      => esc_html__( 'DatePublished', 'schema-and-structured-data-for-wp' ),
                                                'id'         => 'saswp_audiobook_date_published_'.$schema_id,
                                                'type'       => 'text',
                                                'default'    => get_the_date("Y-m-d")                            
                                        ),
                                        array(
                                                'label'      => esc_html__( 'DateModified', 'schema-and-structured-data-for-wp' ),
                                                'id'         => 'saswp_audiobook_date_modified_'.$schema_id,
                                                'type'       => 'text',
                                                'default'    => get_the_modified_date("Y-m-d")                            
                                        ),
                                        array(
                                                'label'   => esc_html__( 'Author Type', 'schema-and-structured-data-for-wp' ),
                                                'id'      => 'saswp_audiobook_author_type_'.$schema_id,
                                                'type'    => 'select',
                                                'options' => array(
                                                        'Person'           => 'Person',
                                                        'Organization'     => 'Organization',                        
                                                )
                                        ),
                                        array(
                                                'label'   => esc_html__( 'Author Name', 'schema-and-structured-data-for-wp' ),
                                                'id'      => 'saswp_audiobook_author_name_'.$schema_id,
                                                'type'    => 'text',
                                                'default' => is_object($current_user) ? $current_user->display_name : ''    
                                        ),
                                        array(
                                                'label'   => esc_html__( 'Author Description', 'schema-and-structured-data-for-wp' ),
                                                'id'      => 'saswp_audiobook_author_description_'.$schema_id,
                                                'type'    => 'textarea',
                                                'default' => $author_desc
                                        ), 
                                        array(
                                                'label'   => esc_html__( 'Author URL', 'schema-and-structured-data-for-wp' ),
                                                'id'      => 'saswp_audiobook_author_url_'.$schema_id,
                                                'type'    => 'text',
                                                'default' => $author_url
                                        ),    
                                        array(
                                                'label'   => esc_html__( 'Author Image', 'schema-and-structured-data-for-wp' ),
                                                'id'      => 'saswp_audiobook_author_image_'.$schema_id,
                                                'type'    => 'media',
                                                'default' => isset($author_details['url']) ? $author_details['url'] : ''   
                                        ),                                   
                                        array(
                                                'label'      => esc_html__( 'Publisher', 'schema-and-structured-data-for-wp' ),
                                                'id'         => 'saswp_audiobook_publisher_'.$schema_id,
                                                'type'       => 'text',                           
                                        ),
                                        array(
                                                'label'      => esc_html__( 'Provider', 'schema-and-structured-data-for-wp' ),
                                                'id'         => 'saswp_audiobook_provider_'.$schema_id,
                                                'type'       => 'text',                           
                                        ),
                                        array(
                                                'label'      => esc_html__( 'Read By', 'schema-and-structured-data-for-wp' ),
                                                'id'         => 'saswp_audiobook_readby_'.$schema_id,
                                                'type'       => 'text',                           
                                        ),                                                                                
                                        array(
                                                'label'      => esc_html__( 'Content URL', 'schema-and-structured-data-for-wp' ),
                                                'id'         => 'saswp_audiobook_content_url_'.$schema_id,
                                                'type'       => 'text',                           
                                        ),
                                        array(
                                                'label'      => esc_html__( 'Duration', 'schema-and-structured-data-for-wp' ),
                                                'id'         => 'saswp_audiobook_duration_'.$schema_id,
                                                'type'       => 'text',                           
                                        ),
                                        array(
                                                'label'      => esc_html__( 'Encoding Format', 'schema-and-structured-data-for-wp' ),
                                                'id'         => 'saswp_audiobook_encoding_format_'.$schema_id,
                                                'type'       => 'text',                           
                                        ),
                                        array(
                                                'label'      => esc_html__( 'Player Type', 'schema-and-structured-data-for-wp' ),
                                                'id'         => 'saswp_audiobook_player_type_'.$schema_id,
                                                'type'       => 'text',                           
                                        ),
                                        array(
                                                'label'      => esc_html__( 'Main Entity Of Page', 'schema-and-structured-data-for-wp' ),
                                                'id'         => 'saswp_audiobook_main_entity_of_page_'.$schema_id,
                                                'type'       => 'text',                           
                                        )
                                );
                                        
                        break;  

                        case 'HotelRoom':                                                                                                            
                 
                                $meta_field = array(
                                        array(
                                                'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                                'id'         => 'saswp_hotelroom_hotel_id_'.$schema_id,
                                                'type'       => 'text',
                                                'default'    => 'HotelRoom'   
                                        ),
                                        array(
                                                'label'   => esc_html__( 'Hotel Name', 'schema-and-structured-data-for-wp' ),
                                                'id'      => 'saswp_hotelroom_hotel_name_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        ),
                                        array(
                                                'label'   => esc_html__( 'Hotel Image', 'schema-and-structured-data-for-wp' ),
                                                'id'      => 'saswp_hotelroom_hotel_image_'.$schema_id,
                                                'type'    => 'media'                                                                
                                        ),
                                        array(
                                                'label'   => esc_html__( 'Hotel Description', 'schema-and-structured-data-for-wp' ),
                                                'id'      => 'saswp_hotelroom_hotel_description_'.$schema_id,
                                                'type'    => 'textarea'                                                                
                                        ),
                                        array(
                                                'label'   => esc_html__( 'Hotel Price Range', 'schema-and-structured-data-for-wp' ),
                                                'id'      => 'saswp_hotelroom_hotel_price_range_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        ),
                                        array(
                                                'label'   => esc_html__( 'Hotel Telephone', 'schema-and-structured-data-for-wp' ),
                                                'id'      => 'saswp_hotelroom_hotel_telephone_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        ),
                                        array(
                                                'label' => esc_html__( 'Hotel Street Address', 'schema-and-structured-data-for-wp' ),
                                                'id' => 'saswp_hotelroom_hotel_streetaddress_'.$schema_id,
                                                'type' => 'text',                                
                                        ),
                                        array(
                                                'label' => esc_html__( 'Hotel Locality', 'schema-and-structured-data-for-wp' ),
                                                'id'    => 'saswp_hotelroom_hotel_locality_'.$schema_id,
                                                'type'  => 'text',                                
                                        ),
                                        array(
                                                'label' => esc_html__( 'Hotel Region', 'schema-and-structured-data-for-wp' ),
                                                'id'    => 'saswp_hotelroom_hotel_region_'.$schema_id,
                                                'type'  => 'text',                                
                                        ),
                                        array(
                                                'label' => esc_html__( 'Hotel PostalCode', 'schema-and-structured-data-for-wp' ),
                                                'id'    => 'saswp_hotelroom_hotel_postalcode_'.$schema_id,
                                                'type'  => 'text',                                
                                        ),
                                        array(
                                                'label' => esc_html__( 'Hotel Country', 'schema-and-structured-data-for-wp' ),
                                                'id'    => 'saswp_hotelroom_hotel_country_'.$schema_id,
                                                'type'  => 'text',                                
                                        ),
                                        array(
                                                'label' => esc_html__( 'Hotel Room Name', 'schema-and-structured-data-for-wp' ),
                                                'id'    => 'saswp_hotelroom_name_'.$schema_id,
                                                'type'  => 'text',                                
                                        ),
                                        array(
                                                'label' => esc_html__( 'Hotel Room Description', 'schema-and-structured-data-for-wp' ),
                                                'id'    => 'saswp_hotelroom_description_'.$schema_id,
                                                'type'  => 'textarea',                                
                                        ),
                                        array(
                                                'label' => esc_html__( 'Hotel Room Image', 'schema-and-structured-data-for-wp' ),
                                                'id'    => 'saswp_hotelroom_image_'.$schema_id,
                                                'type'  => 'media',                                
                                        ),
                                        array(
                                                'label' => esc_html__( 'Hotel Room Offer name', 'schema-and-structured-data-for-wp' ),
                                                'id'    => 'saswp_hotelroom_offer_name_'.$schema_id,
                                                'type'  => 'text',                                
                                        ),
                                        array(
                                                'label' => esc_html__( 'Hotel Room Offer Terms & Condition', 'schema-and-structured-data-for-wp' ),
                                                'id'    => 'saswp_hotelroom_offer_description_'.$schema_id,
                                                'type'  => 'textarea',                                
                                        ),
                                        array(
                                                'label' => esc_html__( 'Hotel Room Offer Price', 'schema-and-structured-data-for-wp' ),
                                                'id'    => 'saswp_hotelroom_offer_price_'.$schema_id,
                                                'type'  => 'text',                                
                                        ),
                                        array(
                                                'label' => esc_html__( 'Hotel Room Offer Price Currency', 'schema-and-structured-data-for-wp' ),
                                                'id'    => 'saswp_hotelroom_offer_price_currency_'.$schema_id,
                                                'type'  => 'text',                                
                                        ),
                                        array(
                                                'label' => esc_html__( 'Hotel Room Offer Price UnitCode', 'schema-and-structured-data-for-wp' ),
                                                'id'    => 'saswp_hotelroom_offer_unitcode_'.$schema_id,
                                                'type'  => 'text',                                
                                        ),
                                        array(
                                                'label' => esc_html__( 'Hotel Room Offer Price Valid Through', 'schema-and-structured-data-for-wp' ),
                                                'id'    => 'saswp_hotelroom_offer_validthrough_'.$schema_id,
                                                'type'  => 'text',                                
                                        ),
                                );
                        break;

                        case 'PodcastEpisode':                                                                                                            
                
                                $meta_field = array(
                                        array(
                                                'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                                'id'         => 'saswp_podcast_episode_id_'.$schema_id,
                                                'type'       => 'text',
                                                'default'    => 'PodcastEpisode'   
                                        ),
                                        array(
                                                'label'   => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                                                'id'      => 'saswp_podcast_episode_name_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        ),
                                        array(
                                                'label'   => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                                                'id'      => 'saswp_podcast_episode_description_'.$schema_id,
                                                'type'    => 'textarea'                                                                
                                        ),
                                        array(
                                                'label'   => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                                                'id'      => 'saswp_podcast_episode_url_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        ),
                                        array(
                                                'label'   => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                                                'id'      => 'saswp_podcast_episode_image_'.$schema_id,
                                                'type'    => 'media'                                                                
                                        ),
                                        array(
                                                'label'      => esc_html__( 'DatePublished', 'schema-and-structured-data-for-wp' ),
                                                'id'         => 'saswp_podcast_episode_date_published_'.$schema_id,
                                                'type'       => 'text',
                                                'default'    => get_the_date("Y-m-d")                            
                                        ),
                                        array(
                                                'label'      => esc_html__( 'DateModified', 'schema-and-structured-data-for-wp' ),
                                                'id'         => 'saswp_podcast_episode_date_modified_'.$schema_id,
                                                'type'       => 'text',
                                                'default'    => get_the_modified_date("Y-m-d")                            
                                        ),
                                        array(
                                                'label'      => esc_html__( 'Time Required', 'schema-and-structured-data-for-wp' ),
                                                'id'         => 'saswp_podcast_episode_timeRequired_'.$schema_id,
                                                'type'       => 'text'                                                         
                                        ),
                                        array(
                                                'label'      => esc_html__( 'Content URL', 'schema-and-structured-data-for-wp' ),
                                                'id'         => 'saswp_podcast_episode_content_url_'.$schema_id,
                                                'type'       => 'text'                                                         
                                        ),
                                        array(
                                                'label'      => esc_html__( 'PodcastSeries Name', 'schema-and-structured-data-for-wp' ),
                                                'id'         => 'saswp_podcast_episode_series_name_'.$schema_id,
                                                'type'       => 'text'                                                         
                                        ),
                                        array(
                                                'label'      => esc_html__( 'PodcastSeries URL', 'schema-and-structured-data-for-wp' ),
                                                'id'         => 'saswp_podcast_episode_series_url_'.$schema_id,
                                                'type'       => 'text'                                                         
                                        )                                                                                             
                                );
                                
                        break;                  

                        case 'PodcastSeason':                                                                                                            
                
                                $meta_field = array(
                                        array(
                                                'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                                'id'         => 'saswp_podcast_season_id_'.$schema_id,
                                                'type'       => 'text',
                                                'default'    => 'PodcastEpisode'   
                                        ),
                                        array(
                                                'label'   => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                                                'id'      => 'saswp_podcast_season_name_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        ),
                                        array(
                                                'label'   => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                                                'id'      => 'saswp_podcast_season_description_'.$schema_id,
                                                'type'    => 'textarea'                                                                
                                        ),
                                        array(
                                                'label'   => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                                                'id'      => 'saswp_podcast_season_url_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        ),
                                        array(
                                                'label'   => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                                                'id'      => 'saswp_podcast_season_image_'.$schema_id,
                                                'type'    => 'media'                                                                
                                        ),
                                        array(
                                                'label'      => esc_html__( 'DatePublished', 'schema-and-structured-data-for-wp' ),
                                                'id'         => 'saswp_podcast_season_date_published_'.$schema_id,
                                                'type'       => 'text',
                                                'default'    => get_the_date("Y-m-d")                            
                                        ),
                                        array(
                                                'label'      => esc_html__( 'DateModified', 'schema-and-structured-data-for-wp' ),
                                                'id'         => 'saswp_podcast_season_date_modified_'.$schema_id,
                                                'type'       => 'text',
                                                'default'    => get_the_modified_date("Y-m-d")                            
                                        ),
                                        array(
                                                'label'      => esc_html__( 'Season Number', 'schema-and-structured-data-for-wp' ),
                                                'id'         => 'saswp_podcast_season_number_'.$schema_id,
                                                'type'       => 'text'                                                         
                                        ),
                                        array(
                                                'label'      => esc_html__( 'Number of seasons', 'schema-and-structured-data-for-wp' ),
                                                'id'         => 'saswp_podcast_season_number_of_seasons_'.$schema_id,
                                                'type'       => 'text'                                                         
                                        ),                                        
                                        array(
                                                'label'      => esc_html__( 'PodcastSeries Name', 'schema-and-structured-data-for-wp' ),
                                                'id'         => 'saswp_podcast_season_series_name_'.$schema_id,
                                                'type'       => 'text'                                                         
                                        ),
                                        array(
                                                'label'      => esc_html__( 'PodcastSeries URL', 'schema-and-structured-data-for-wp' ),
                                                'id'         => 'saswp_podcast_season_series_url_'.$schema_id,
                                                'type'       => 'text'                                                         
                                        )                                                                                             
                                );
                                
                        break;                  

                        case 'EducationalOccupationalCredential':                                                                                                            
                        
                                $meta_field = array(
                                        array(
                                                'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                                'id'         => 'saswp_eoc_id_'.$schema_id,
                                                'type'       => 'text',
                                                'default'    => 'EducationalOccupationalCredential'   
                                        ),
                                        array(
                                                'label'   => esc_html__( 'Additional Type', 'schema-and-structured-data-for-wp' ),
                                                'id'      => 'saswp_eoc_additional_type_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        ),
                                        array(
                                                'label'   => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                                                'id'      => 'saswp_eoc_name_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        ),
                                        array(
                                                'label'   => esc_html__( 'Alternative Name', 'schema-and-structured-data-for-wp' ),
                                                'id'      => 'saswp_eoc_alt_name_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        ),
                                        array(
                                                'label'   => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                                                'id'      => 'saswp_eoc_description_'.$schema_id,
                                                'type'    => 'textarea'                                                                
                                        ),                                        
                                        
                                        array(
                                                'label'   => esc_html__( 'Educational Level Name', 'schema-and-structured-data-for-wp' ),
                                                'id'      => 'saswp_eoc_e_lavel_name_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        ),
                                        array(
                                                'label'   => esc_html__( 'Educational Level DefinedTermSet', 'schema-and-structured-data-for-wp' ),
                                                'id'      => 'saswp_eoc_e_lavel_definedtermset_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        ),

                                        array(
                                                'label'   => esc_html__( 'Credential Category Name', 'schema-and-structured-data-for-wp' ),
                                                'id'      => 'saswp_eoc_c_category_name_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        ),
                                        array(
                                                'label'   => esc_html__( 'Credential Category Term Code', 'schema-and-structured-data-for-wp' ),
                                                'id'      => 'saswp_eoc_c_category_term_code_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        ),
                                        array(
                                                'label'   => esc_html__( 'Credential Category DefinedTermSet', 'schema-and-structured-data-for-wp' ),
                                                'id'      => 'saswp_eoc_c_category_definedtermset_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        ),

                                        array(
                                                'label'   => esc_html__( 'Competency Required Name', 'schema-and-structured-data-for-wp' ),
                                                'id'      => 'saswp_eoc_c_required_name_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        ),
                                        array(
                                                'label'   => esc_html__( 'Competency Required Term Code', 'schema-and-structured-data-for-wp' ),
                                                'id'      => 'saswp_eoc_c_required_term_code_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        ),
                                        array(
                                                'label'   => esc_html__( 'Competency Required DefinedTermSet', 'schema-and-structured-data-for-wp' ),
                                                'id'      => 'saswp_eoc_c_required_definedtermset_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        ),
                                        array(
                                                'label'   => esc_html__( 'Competency Required URL', 'schema-and-structured-data-for-wp' ),
                                                'id'      => 'saswp_eoc_c_required_url_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        )                                        

                                );
                                
                        break;          
                                
                        case 'ApartmentComplex':                                                                                                            
                
                                $meta_field = array(
                                        array(
                                                'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                                'id'         => 'saswp_apartment_complex_id_'.$schema_id,
                                                'type'       => 'text',
                                                'default'    => 'ApartmentComplex'   
                                        ),
                                        array(
                                                'label'   => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                                                'id'      => 'saswp_apartment_complex_name_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        ),
                                        array(
                                                'label'   => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                                                'id'      => 'saswp_apartment_complex_description_'.$schema_id,
                                                'type'    => 'textarea'                                                                
                                        ),
                                        array(
                                                'label'   => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                                                'id'      => 'saswp_apartment_complex_url_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        ),
                                        array(
                                                'label'   => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                                                'id'      => 'saswp_apartment_complex_image_'.$schema_id,
                                                'type'    => 'media'                                                                
                                        ),
                                        array(
                                                'label'   => esc_html__( 'Number Of Bedrooms', 'schema-and-structured-data-for-wp' ),
                                                'id'      => 'saswp_apartment_complex_no_of_bedrooms_'.$schema_id,
                                                'type'    => 'number'                                                                
                                        ),
                                        array(
                                                'label'   => esc_html__( 'Pets Allowed', 'schema-and-structured-data-for-wp' ),
                                                'id'      => 'saswp_apartment_complex_pets_allowed_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        ),                                        
                                        array(
                                                'label' => esc_html__( 'Location Street Address', 'schema-and-structured-data-for-wp' ),
                                                'id' => 'saswp_apartment_complex_streetaddress_'.$schema_id,
                                                'type' => 'text',                                
                                        ),
                                        array(
                                                'label' => esc_html__( 'Location Locality', 'schema-and-structured-data-for-wp' ),
                                                'id' => 'saswp_apartment_complex_locality_'.$schema_id,
                                                'type' => 'text',                                
                                        ),
                                        array(
                                                'label' => esc_html__( 'Location Region', 'schema-and-structured-data-for-wp' ),
                                                'id' => 'saswp_apartment_complex_region_'.$schema_id,
                                                'type' => 'text',                                
                                        ),
                                        array(
                                                'label' => esc_html__( 'Location PostalCode', 'schema-and-structured-data-for-wp' ),
                                                'id' => 'saswp_apartment_complex_postalcode_'.$schema_id,
                                                'type' => 'text',                                
                                        ),
                                        array(
                                                'label' => esc_html__( 'Location Country', 'schema-and-structured-data-for-wp' ),
                                                'id'    => 'saswp_apartment_complex_country_'.$schema_id,
                                                'type'  => 'text',                                
                                        ),
                                        array(
                                                'label' => esc_html__( 'Location Phone', 'schema-and-structured-data-for-wp' ),
                                                'id'    => 'saswp_apartment_complex_phone_'.$schema_id,
                                                'type'  => 'text',                                
                                        ),
                                        array(
                                                'label'      => esc_html__( 'GeoCoordinates Latitude', 'schema-and-structured-data-for-wp' ),
                                                'id'         => 'saswp_apartment_complex_latitude_'.$schema_id,
                                                'type'       => 'text',
                                                'attributes' => array(
                                                    'placeholder' => '17.412'
                                                ), 
                                        ),
                                        array(
                                                'label'      => esc_html__( 'GeoCoordinates Longitude', 'schema-and-structured-data-for-wp' ),
                                                'id'         => 'saswp_apartment_complex_longitude_'.$schema_id,
                                                'type'       => 'text',
                                                'attributes' => array(
                                                    'placeholder' => '78.433'
                                                ),
                                        )                                                                                                                   
                                );
                                
                        break;          

                case 'Product':                
                    
                    $product_details = array();
                    
                    if($manual == null && $post_id){
                    
                        $service = new SASWP_Output_Service();
                        $product_details = $service->saswp_woocommerce_product_details($post_id);     
                        
                    }
                     
                    $meta_field = array(                        
                    array(
                            'label'   => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_product_schema_name_'.$schema_id,
                            'type'    => 'text',     
                            'default' => saswp_remove_warnings($product_details, 'product_name', 'saswp_string')
                    ),
                    array(
                            'label'   => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_product_schema_id_'.$schema_id,
                            'type'    => 'text'                        
                    ),
                    array(
                            'label'   => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_product_schema_url_'.$schema_id,
                            'type'    => 'text',     
                            'default' => get_permalink()
                    ),    
                    array(
                            'label'   => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_product_schema_description_'.$schema_id,
                            'type'    => 'textarea', 
                            'default' => saswp_remove_warnings($product_details, 'product_description', 'saswp_string')
                    ), 
                        array(
                            'label'    => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                            'id'       => 'saswp_product_schema_image_'.$schema_id,
                            'type'     => 'media',                           
                        ),
                        array(
                                'label'    => esc_html__( 'Brand Name', 'schema-and-structured-data-for-wp' ),
                                'id'       => 'saswp_product_schema_brand_name_'.$schema_id,
                                'type'     => 'text',
                                'default' => saswp_remove_warnings($product_details, 'product_brand', 'saswp_string')
                        ),
                        array(
                                'label'    => esc_html__( 'Brand URL', 'schema-and-structured-data-for-wp' ),
                                'id'       => 'saswp_product_schema_brand_url_'.$schema_id,
                                'type'     => 'text'                                 
                        ),
                        array(
                                'label'    => esc_html__( 'Brand Image', 'schema-and-structured-data-for-wp' ),
                                'id'       => 'saswp_product_schema_brand_image_'.$schema_id,
                                'type'     => 'media'                               
                        ),
                        array(
                                'label'    => esc_html__( 'Brand Logo', 'schema-and-structured-data-for-wp' ),
                                'id'       => 'saswp_product_schema_brand_logo_'.$schema_id,
                                'type'     => 'media'                               
                        ),                        
                        array(
                                'label'   => esc_html__( 'Price', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_product_schema_price_'.$schema_id,
                                'type'    => 'text',
                                'default' => saswp_remove_warnings($product_details, 'product_price', 'saswp_string')
                       ),
                        array(
                                'label'   => esc_html__( 'High Price', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_product_schema_high_price_'.$schema_id,
                                'type'    => 'text',
                                'default' => saswp_remove_warnings($product_details, 'product_price', 'saswp_string')
                        ),
                        array(
                                'label'   => esc_html__( 'Low Price', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_product_schema_low_price_'.$schema_id,
                                'type'    => 'text',
                                'default' => saswp_remove_warnings($product_details, 'product_price', 'saswp_string')
                        ),
                        array(
                                'label'   => esc_html__( 'Offer Count', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_product_schema_offer_count_'.$schema_id,
                                'type'    => 'text',                                
                        ),
                        array(
                                'label'   => esc_html__( 'Offer URL', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_product_schema_offer_url_'.$schema_id,
                                'type'    => 'text',                                
                        ),
                        array(
                            'label'   => esc_html__( 'Price Valid Until', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_product_schema_priceValidUntil_'.$schema_id,
                            'type'    => 'text',
                            'default' => saswp_remove_warnings($product_details, 'product_priceValidUntil', 'saswp_string')    
                       ),
                        array(
                            'label'   => esc_html__( 'Currency', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_product_schema_currency_'.$schema_id,
                            'type'    => 'text', 
                            'default' => saswp_remove_warnings($product_details, 'product_currency', 'saswp_string')    
                       ),
                       array(
                        'label'   => esc_html__( 'VAT', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_product_schema_vat_'.$schema_id,
                        'type'    => 'text', 
                        'default' => saswp_remove_warnings($product_details, 'product_vat', 'saswp_string')    
                   ),
                        array(
                            'label'   => esc_html__( 'Availability', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_product_schema_availability_'.$schema_id,
                            'type'    => 'select',                            
                            'options' => array(
                                     'BackOrder'           => 'Back Order',
                                     'Discontinued'           => 'Discontinued',
                                     'Discontinued'      => 'Discontinued',
                                     'InStoreOnly'           => 'In Store Only',
                                     'InStock'           => 'In Stock',
                                     'LimitedAvailability'           => 'Limited Availability',
                                     'OnlineOnly'           => 'Online Only',
                                     'OutOfStock'        => 'Out Of Stock',
                                     'PreOrder'          => 'Pre Order', 
                                     'PreSale'          => 'Pre Sale', 
                            ),
                            'default' => saswp_remove_warnings($product_details, 'product_availability', 'saswp_string')
                     ),
                        array(
                            'label'   => esc_html__( 'Condition', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_product_schema_condition_'.$schema_id,
                            'type'    => 'select',                            
                            'options' => array(
                                     'NewCondition'              => 'New',
                                     'UsedCondition'             => 'Used',
                                     'RefurbishedCondition'      => 'Refurbished',
                                     'DamagedCondition'          => 'Damaged',   
                            ),
                     ),
                        array(
                            'label'   => esc_html__( 'SKU', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_product_schema_sku_'.$schema_id,
                            'type'    => 'text', 
                            'default' => saswp_remove_warnings($product_details, 'product_sku', 'saswp_string')    
                      ),
                        array(
                            'label'   =>    esc_html__( 'MPN', 'schema-and-structured-data-for-wp' ),                             'type'    => 'text',

                            'id'      => 'saswp_product_schema_mpn_'.$schema_id,
                            'type'    => 'text',
                            'note'    => 'OR',                            
                            'default' => saswp_remove_warnings($product_details, 'product_mpn', 'saswp_string')
                       ),                       
                        array(
                            'label'   =>    esc_html__( 'GTIN8', 'schema-and-structured-data-for-wp' ),                             'type'    => 'text',

                            'id'      => 'saswp_product_schema_gtin8_'.$schema_id,
                            'type'    => 'text',  
                            'note'    => 'OR',  
                            'default' => saswp_remove_warnings($product_details, 'product_gtin8', 'saswp_string')    
                       ),
                        array(
                                'label'   =>    esc_html__( 'GTIN13', 'schema-and-structured-data-for-wp' ),                             'type'    => 'text',

                                'id'      => 'saswp_product_schema_gtin13_'.$schema_id,
                                'type'    => 'text',  
                                'default' => saswp_remove_warnings($product_details, 'product_gtin13', 'saswp_string')    
                        ),
                        array(
                                'label'   =>    esc_html__( 'GTIN12', 'schema-and-structured-data-for-wp' ),                             'type'    => 'text',

                                'id'      => 'saswp_product_schema_gtin12_'.$schema_id,
                                'type'    => 'text',  
                                'default' => saswp_remove_warnings($product_details, 'product_gtin12', 'saswp_string')    
                        ),
                        array(
                                'label'   =>    esc_html__( 'Color', 'schema-and-structured-data-for-wp' ),                             'type'    => 'text',

                                'id'      => 'saswp_product_schema_color_'.$schema_id,
                                'type'    => 'text'                                
                        ),
                        array(
                            'label' =>    esc_html__( 'Seller Organization', 'schema-and-structured-data-for-wp' ),                             'type'    => 'text',

                            'id'    => 'saswp_product_schema_seller_'.$schema_id,
                            'type'  => 'text',                             
                       ),
                       array(
                                'label'      =>    esc_html__( 'Seller Street Address', 'schema-and-structured-data-for-wp' ),                             'type'    => 'text',

                                'id'         => 'saswp_product_schema_seller_street_address_'.$schema_id,
                                'type'       => 'text',                             
                        ),
                        array(
                                'label'      =>    esc_html__( 'Seller Locality', 'schema-and-structured-data-for-wp' ),                             'type'    => 'text',

                                'id'         => 'saswp_product_schema_seller_locality_'.$schema_id,
                                'type'       => 'text',                             
                        ),
                        array(
                                'label'      =>    esc_html__( 'Seller Region', 'schema-and-structured-data-for-wp' ),                             'type'    => 'text',

                                'id'         => 'saswp_product_schema_seller_region_'.$schema_id,
                                'type'       => 'text',                             
                        ),
                        array(
                                'label'      =>    esc_html__( 'Seller Postal Code', 'schema-and-structured-data-for-wp' ),                             'type'    => 'text',

                                'id'         => 'saswp_product_schema_seller_postalcode_'.$schema_id,
                                'type'       => 'text',                             
                        ),
                        array(
                                'label'      =>    esc_html__( 'Seller Country', 'schema-and-structured-data-for-wp' ),                             'type'    => 'text',

                                'id'         => 'saswp_product_schema_seller_country_'.$schema_id,
                                'type'       => 'text',                             
                        ),
                        array(
                                'label'      =>    esc_html__( 'Seller Telephone', 'schema-and-structured-data-for-wp' ),                             'type'    => 'text',

                                'id'         => 'saswp_product_schema_seller_telephone_'.$schema_id,
                                'type'       => 'text',                             
                        ), 
                       array(
                        'label' =>    esc_html__( 'Additional Type', 'schema-and-structured-data-for-wp' ),                             'type'    => 'text',

                        'id'    => 'saswp_product_additional_type_'.$schema_id,
                        'type'  => 'text',                             
                       ),
                       array(
                        'label' =>    esc_html__( 'Product Weight', 'schema-and-structured-data-for-wp' ),                             'type'    => 'text',

                        'id'    => 'saswp_product_weight_'.$schema_id,
                        'type'  => 'text',                             
                       ),
                       array(
                        'label' =>    esc_html__( 'Product Weight Unit', 'schema-and-structured-data-for-wp' ),                             'type'    => 'text',

                        'id'    => 'saswp_product_weight_unit_'.$schema_id,
                        'type'  => 'text',                             
                       ),
                       array(
                            'label'   =>    esc_html__( 'Return Policy Applicable Country Code', 'schema-and-structured-data-for-wp' ),                             'type'    => 'text',

                            'id'      => 'saswp_product_schema_rp_country_code_'.$schema_id,
                            'type'    => 'text',
                            'attributes' => array(
                                'placeholder' => 'US'
                            ),
                        ),
                        array(
                            'label'   =>    esc_html__( 'Return Policy Category', 'schema-and-structured-data-for-wp' ),                             'type'    => 'text',

                            'id'      => 'saswp_product_schema_rp_category_'.$schema_id,
                            'type'    => 'select',
                            'options' => array(
                                     ''                                                 => 'Select Return Policy Category',
                                     'MerchantReturnFiniteReturnWindow'                 => 'MerchantReturnFiniteReturnWindow',
                                     'MerchantReturnNotPermitted'                       => 'MerchantReturnNotPermitted',
                                     'MerchantReturnUnlimitedWindow'                    => 'MerchantReturnUnlimitedWindow',
                                     'MerchantReturnUnspecified'                        => 'MerchantReturnUnspecified',
                            )
                        ),
                        array(
                            'label'   =>    esc_html__( 'Return Policy Merchant Return Days', 'schema-and-structured-data-for-wp' ),                             'type'    => 'text',

                            'id'      => 'saswp_product_schema_rp_return_days_'.$schema_id,
                            'type'    => 'number',
                            'attributes' => array(
                                'placeholder' => '5'
                            ),
                        ),
                        array(
                            'label'   =>    esc_html__( 'Return Policy Return Method', 'schema-and-structured-data-for-wp' ),                             'type'    => 'text',

                            'id'      => 'saswp_product_schema_rp_return_method_'.$schema_id,
                            'type'    => 'select',
                            'options' => array(
                                    ''                  => 'Select Return Policy Method',
                                    'ReturnAtKiosk'     => 'ReturnAtKiosk',
                                    'ReturnByMail'      => 'ReturnByMail',
                                    'ReturnInStore'     => 'ReturnInStore',
                            )
                        ),
                        array(
                            'label'   =>    esc_html__( 'Return Policy Return Fees', 'schema-and-structured-data-for-wp' ),                             'type'    => 'text',

                            'id'      => 'saswp_product_schema_rp_return_fees_'.$schema_id,
                            'type'    => 'select',
                            'options' => array(
                                    ''                                  => 'Select Return Policy Fees',
                                    'FreeReturn'                        => 'FreeReturn',
                                    'ReturnFeesCustomerResponsibility'  => 'ReturnFeesCustomerResponsibility',
                                    'ReturnShippingFees'                => 'ReturnShippingFees',
                            )
                        ),
                        array(
                            'label'   =>    esc_html__( 'Return Policy Refund Type', 'schema-and-structured-data-for-wp' ),                             'type'    => 'text',

                            'id'      => 'saswp_product_schema_rp_refund_type_'.$schema_id,
                            'type'    => 'select',
                            'options' => array(
                                    ''                                  => 'Select Refune Type',
                                    'FullRefund'                        => 'FullRefund',
                                    'ExchangeRefund'                    => 'ExchangeRefund',
                                    'StoreCreditRefund'                 => 'StoreCreditRefund',
                            )
                        ),
                        array(
                            'label'   =>    esc_html__( 'Shipping Rate Value', 'schema-and-structured-data-for-wp' ),                             'type'    => 'text',

                            'id'      => 'saswp_product_schema_sr_value_'.$schema_id,
                            'type'    => 'text',
                            'attributes' => array(
                                'placeholder' => '3.8'
                            ),
                        ),
                        array(
                            'label'   =>    esc_html__( 'Shipping Rate Currency', 'schema-and-structured-data-for-wp' ),                             'type'    => 'text',

                            'id'      => 'saswp_product_schema_sr_currency_'.$schema_id,
                            'type'    => 'text',
                            'attributes' => array(
                                'placeholder' => 'USD'
                            ),
                        ),
                        array(
                            'label'   =>    esc_html__( 'Shipping Destination Locality', 'schema-and-structured-data-for-wp' ),                             'type'    => 'text',

                            'id'      => 'saswp_product_schema_sa_locality_'.$schema_id,
                            'type'    => 'text',
                            'attributes' => array(
                                'placeholder' => 'New York'
                            ),                        
                        ),
                        array(
                            'label'   =>    esc_html__( 'Shipping Destination Region', 'schema-and-structured-data-for-wp' ),                             'type'    => 'text',

                            'id'      => 'saswp_product_schema_sa_region_'.$schema_id,
                            'type'    => 'text', 
                            'attributes' => array(
                                'placeholder' => 'NY'
                            ),                       
                        ),
                        array(
                            'label'   => esc_html__( 'Shipping Destination Postal Code', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_product_schema_sa_postal_code_'.$schema_id,
                            'type'    => 'text',  
                            'attributes' => array(
                                'placeholder' => '10019'
                            ),                      
                        ),
                        array(
                            'label'   => esc_html__( 'Shipping Destination Street Address', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_product_schema_sa_address_'.$schema_id,
                            'type'    => 'textarea', 
                            'attributes' => array(
                                'placeholder' => '148 W 51st St'
                            ),                       
                        ),
                        array(
                            'label'   => esc_html__( 'Shipping Destination Country', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_product_schema_sa_country_'.$schema_id,
                            'type'    => 'text', 
                            'attributes' => array(
                                'placeholder' => 'US'
                            ),                       
                        ),
                        array(
                            'label'   => esc_html__( 'Shipping Handling Time Min Value', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_product_schema_sdh_minval_'.$schema_id,
                            'type'    => 'number',
                            'attributes' => array(
                                'placeholder' => '0'
                            ),                        
                        ),
                        array(
                            'label'   => esc_html__( 'Shipping Handling Time Max Value', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_product_schema_sdh_maxval_'.$schema_id,
                            'type'    => 'number',
                            'attributes' => array(
                                'placeholder' => '1'
                            ),                        
                        ),
                        array(
                            'label'   => esc_html__( 'Shipping Handling Time Unit Code', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_product_schema_sdh_unitcode_'.$schema_id,
                            'type'    => 'text',     
                            'note'    => 'Note: Enter unit code as DAY',
                            'attributes' => array(
                                'placeholder' => 'DAY'
                            ),                 
                        ),
                        array(
                            'label'   => esc_html__( 'Shipping Transit Time Min Value', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_product_schema_sdt_minval_'.$schema_id,
                            'type'    => 'number', 
                            'attributes' => array(
                                'placeholder' => '2'
                            ),                       
                        ),
                        array(
                            'label'   => esc_html__( 'Shipping Transit Time Max Value', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_product_schema_sdt_maxval_'.$schema_id,
                            'type'    => 'number',  
                            'attributes' => array(
                                'placeholder' => '5'
                            ),                      
                        ),
                        array(
                            'label'   => esc_html__( 'Shipping Transit Time Unit Code', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_product_schema_sdt_unitcode_'.$schema_id,
                            'type'    => 'text',     
                            'note'    => 'Note: Enter unit code as DAY',  
                            'attributes' => array(
                                'placeholder' => 'DAY'
                            ),                 
                        ),
                        array(
                            'label'   => esc_html__( 'Return Shipping Fees Name', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_product_schema_rsf_name_'.$schema_id,
                            'type'    => 'text'                       
                        ),
                        array(
                            'label'   => esc_html__( 'Return Shipping Fees Value', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_product_schema_rsf_value_'.$schema_id,
                            'type'    => 'number', 
                            'attributes' => array(
                                'placeholder' => '100'
                            ),                       
                        ),
                        array(
                            'label'   => esc_html__( 'Return Shipping Fees Currency', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_product_schema_rsf_currency_'.$schema_id,
                            'type'    => 'text', 
                            'attributes' => array(
                                'placeholder' => 'USD'
                            ),                       
                        ),
                        array(
                            'label' => esc_html__( 'Aggregate Rating', 'schema-and-structured-data-for-wp' ),
                            'id'    => 'saswp_product_schema_enable_rating_'.$schema_id,
                            'type'  => 'checkbox',                            
                        ),                       
                        array(
                            'label'   => esc_html__( 'Rating', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_product_schema_rating_'.$schema_id,
                            'type'    => 'text',
                            'default' => saswp_remove_warnings($product_details, 'product_average_rating', 'saswp_string')
                        ),
                        array(
                            'label'   => esc_html__( 'Number of Reviews', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_product_schema_review_count_'.$schema_id,
                            'type'    => 'text',
                            'default' => saswp_remove_warnings($product_details, 'product_review_count', 'saswp_string')
                        ),
                        array(
                            'label'   => esc_html__( 'Award', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_product_schema_award_'.$schema_id,
                            'type'    => 'text',                            
                        ),
                    );
                    
                    break;
                    
                case 'ProductGroup':

                        $product_details = array();
                    
                        if($manual == null && $post_id){
                        
                            $service = new SASWP_Output_Service();
                            $product_details = $service->saswp_woocommerce_product_details($post_id);     
                            
                        }

                        $meta_field = array(                        
                            array(
                                    'label'   => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                                    'id'      => 'saswp_product_grp_schema_name_'.$schema_id,
                                    'type'    => 'text',     
                                    'default' => saswp_remove_warnings($product_details, 'product_name', 'saswp_string')
                            ),
                            array(
                                    'label'   => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                    'id'      => 'saswp_product_grp_schema_id_'.$schema_id,
                                    'type'    => 'text'                        
                            ),
                            array(
                                    'label'   => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                                    'id'      => 'saswp_product_grp_schema_url_'.$schema_id,
                                    'type'    => 'text',     
                                    'default' => get_permalink()
                            ),    
                            array(
                                    'label'   => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                                    'id'      => 'saswp_product_grp_schema_description_'.$schema_id,
                                    'type'    => 'textarea', 
                                    'default' => saswp_remove_warnings($product_details, 'product_description', 'saswp_string')
                            ), 
                            array(
                                    'label'    => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                                    'id'       => 'saswp_product_grp_schema_image_'.$schema_id,
                                    'type'     => 'media',                           
                            ),
                            array(
                                    'label'    => esc_html__( 'Brand Name', 'schema-and-structured-data-for-wp' ),
                                    'id'       => 'saswp_product_grp_schema_brand_name_'.$schema_id,
                                    'type'     => 'text',
                                    'default' => saswp_remove_warnings($product_details, 'product_brand', 'saswp_string')
                            ),
                            array(
                                    'label'    => esc_html__( 'Product Group ID', 'schema-and-structured-data-for-wp' ),
                                    'id'       => 'saswp_product_grp_schema_group_id_'.$schema_id,
                                    'type'     => 'text',
                            ),
                            array(
                                    'label'    => esc_html__( 'Varies By', 'schema-and-structured-data-for-wp' ),
                                    'id'       => 'saswp_product_grp_schema_varies_by_'.$schema_id,
                                    'type'     => 'text',
                                    'note'     => 'Note: Enter all the varies name in comma separated',
                            ),
                            array(
                                    'label'   => esc_html__( 'Price', 'schema-and-structured-data-for-wp' ),
                                    'id'      => 'saswp_product_grp_schema_price_'.$schema_id,
                                    'type'    => 'text',
                                    'default' => saswp_remove_warnings($product_details, 'product_price', 'saswp_string')
                            ),
                            array(
                                    'label'   => esc_html__( 'High Price', 'schema-and-structured-data-for-wp' ),
                                    'id'      => 'saswp_product_grp_schema_high_price_'.$schema_id,
                                    'type'    => 'text',
                                    'default' => saswp_remove_warnings($product_details, 'product_price', 'saswp_string')
                            ),
                            array(
                                    'label'   => esc_html__( 'Low Price', 'schema-and-structured-data-for-wp' ),
                                    'id'      => 'saswp_product_grp_schema_low_price_'.$schema_id,
                                    'type'    => 'text',
                                    'default' => saswp_remove_warnings($product_details, 'product_price', 'saswp_string')
                            ),
                            array(
                                    'label'   => esc_html__( 'Offer Count', 'schema-and-structured-data-for-wp' ),
                                    'id'      => 'saswp_product_grp_schema_offer_count_'.$schema_id,
                                    'type'    => 'text',                                
                            ),
                            array(
                                'label'   => esc_html__( 'Price Valid Until', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_product_grp_schema_priceValidUntil_'.$schema_id,
                                'type'    => 'text',
                                'default' => saswp_remove_warnings($product_details, 'product_priceValidUntil', 'saswp_string')    
                            ),
                            array(
                                'label'   => esc_html__( 'Currency', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_product_grp_schema_currency_'.$schema_id,
                                'type'    => 'text', 
                                'default' => saswp_remove_warnings($product_details, 'product_currency', 'saswp_string')    
                            ),
                            array(
                                'label'   => esc_html__( 'Availability', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_product_grp_schema_availability_'.$schema_id,
                                'type'    => 'select',                            
                                'options' => array(
                                         'InStock'              => 'In Stock',
                                         'BackOrder'            => 'Back Order',
                                         'Discontinued'         => 'Discontinued',
                                         'Discontinued'         => 'Discontinued',
                                         'InStoreOnly'          => 'In Store Only',
                                         'LimitedAvailability'  => 'Limited Availability',
                                         'OnlineOnly'           => 'Online Only',
                                         'OutOfStock'           => 'Out Of Stock',
                                         'PreOrder'             => 'Pre Order', 
                                         'PreSale'              => 'Pre Sale', 
                                ),
                                'default' => saswp_remove_warnings($product_details, 'product_availability', 'saswp_string')
                            ),
                            array(
                                'label'   => esc_html__( 'Condition', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_product_grp_schema_condition_'.$schema_id,
                                'type'    => 'select',                            
                                'options' => array(
                                         'NewCondition'              => 'New',
                                         'UsedCondition'             => 'Used',
                                         'RefurbishedCondition'      => 'Refurbished',
                                         'DamagedCondition'          => 'Damaged',   
                                ),
                            ),
                            array(
                                'label'   => esc_html__( 'SKU', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_product_grp_schema_sku_'.$schema_id,
                                'type'    => 'text', 
                                'default' => saswp_remove_warnings($product_details, 'product_sku', 'saswp_string')    
                            ),
                            array(
                                'label'   => esc_html__( 'MPN', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_product_grp_schema_mpn_'.$schema_id,
                                'type'    => 'text',
                                'note'    => 'OR',                            
                                'default' => saswp_remove_warnings($product_details, 'product_mpn', 'saswp_string')
                            ),                       
                            array(
                                'label'   => esc_html__( 'GTIN8', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_product_grp_schema_gtin8_'.$schema_id,
                                'type'    => 'text',  
                                'note'    => 'OR',  
                                'default' => saswp_remove_warnings($product_details, 'product_gtin8', 'saswp_string')    
                            ),
                            array(
                                    'label'   => esc_html__( 'GTIN13', 'schema-and-structured-data-for-wp' ),
                                    'id'      => 'saswp_product_grp_schema_gtin13_'.$schema_id,
                                    'type'    => 'text',  
                                    'default' => saswp_remove_warnings($product_details, 'product_gtin13', 'saswp_string')    
                            ),
                            array(
                                    'label'   => esc_html__( 'GTIN12', 'schema-and-structured-data-for-wp' ),
                                    'id'      => 'saswp_product_grp_schema_gtin12_'.$schema_id,
                                    'type'    => 'text',  
                                    'default' => saswp_remove_warnings($product_details, 'product_gtin12', 'saswp_string')    
                            ),
                            array(
                                'label' => esc_html__( 'Seller Organization', 'schema-and-structured-data-for-wp' ),
                                'id'    => 'saswp_product_grp_schema_seller_'.$schema_id,
                                'type'  => 'text',                             
                            ),
                            array(
                                'label' => esc_html__( 'Additional Type', 'schema-and-structured-data-for-wp' ),
                                'id'    => 'saswp_product_grp_additional_type_'.$schema_id,
                                'type'  => 'text',                             
                            ),
                            array(
                                'label'   => esc_html__( 'Return Policy Applicable Country Code', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_product_grp_schema_rp_country_code_'.$schema_id,
                                'type'    => 'text',
                                'attributes' => array(
                                    'placeholder' => 'US'
                                ),
                            ),
                            array(
                                'label'   => esc_html__( 'Return Policy Category', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_product_grp_schema_rp_category_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                         ''                                                 => 'Select Return Policy Category',
                                         'MerchantReturnFiniteReturnWindow'                 => 'MerchantReturnFiniteReturnWindow',
                                         'MerchantReturnNotPermitted'                       => 'MerchantReturnNotPermitted',
                                         'MerchantReturnUnlimitedWindow'                    => 'MerchantReturnUnlimitedWindow',
                                         'MerchantReturnUnspecified'                        => 'MerchantReturnUnspecified',
                                )
                            ),
                            array(
                                'label'   => esc_html__( 'Return Policy Merchant Return Days', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_product_grp_schema_rp_return_days_'.$schema_id,
                                'type'    => 'number',
                                'attributes' => array(
                                    'placeholder' => '5'
                                ),
                            ),
                            array(
                                'label'   => esc_html__( 'Return Policy Return Method', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_product_grp_schema_rp_return_method_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                        ''                  => 'Select Return Policy Method',
                                        'ReturnAtKiosk'     => 'ReturnAtKiosk',
                                        'ReturnByMail'      => 'ReturnByMail',
                                        'ReturnInStore'     => 'ReturnInStore',
                                )
                            ),
                            array(
                                'label'   => esc_html__( 'Return Policy Return Fees', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_product_grp_schema_rp_return_fees_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                        ''                                  => 'Select Return Policy Fees',
                                        'FreeReturn'                        => 'FreeReturn',
                                        'ReturnFeesCustomerResponsibility'  => 'ReturnFeesCustomerResponsibility',
                                        'ReturnShippingFees'                => 'ReturnShippingFees',
                                )
                            ),
                            array(
                                'label'   => esc_html__( 'Shipping Rate Value', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_product_grp_schema_sr_value_'.$schema_id,
                                'type'    => 'text',
                                'attributes' => array(
                                    'placeholder' => '3.8'
                                ),
                            ),
                            array(
                                'label'   => esc_html__( 'Shipping Rate Currency', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_product_grp_schema_sr_currency_'.$schema_id,
                                'type'    => 'text',
                                'attributes' => array(
                                    'placeholder' => 'USD'
                                ),
                            ),
                            array(
                                'label'   => esc_html__( 'Shipping Destination Locality', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_product_grp_schema_sa_locality_'.$schema_id,
                                'type'    => 'text',
                                'attributes' => array(
                                    'placeholder' => 'New York'
                                ),                        
                            ),
                            array(
                                'label'   => esc_html__( 'Shipping Destination Region', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_product_grp_schema_sa_region_'.$schema_id,
                                'type'    => 'text', 
                                'attributes' => array(
                                    'placeholder' => 'NY'
                                ),                       
                            ),
                            array(
                                'label'   => esc_html__( 'Shipping Destination Postal Code', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_product_grp_schema_sa_postal_code_'.$schema_id,
                                'type'    => 'text',  
                                'attributes' => array(
                                    'placeholder' => '10019'
                                ),                      
                            ),
                            array(
                                'label'   => esc_html__( 'Shipping Destination Street Address', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_product_grp_schema_sa_address_'.$schema_id,
                                'type'    => 'textarea', 
                                'attributes' => array(
                                    'placeholder' => '148 W 51st St'
                                ),                       
                            ),
                            array(
                                'label'   => esc_html__( 'Shipping Destination Country', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_product_grp_schema_sa_country_'.$schema_id,
                                'type'    => 'text', 
                                'attributes' => array(
                                    'placeholder' => 'US'
                                ),                       
                            ),
                            array(
                                'label'   => esc_html__( 'Shipping Handling Time Min Value', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_product_grp_schema_sdh_minval_'.$schema_id,
                                'type'    => 'number',
                                'attributes' => array(
                                    'placeholder' => '0'
                                ),                        
                            ),
                            array(
                                'label'   => esc_html__( 'Shipping Handling Time Max Value', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_product_grp_schema_sdh_maxval_'.$schema_id,
                                'type'    => 'number',
                                'attributes' => array(
                                    'placeholder' => '1'
                                ),                        
                            ),
                            array(
                                'label'   => esc_html__( 'Shipping Handling Time Unit Code', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_product_grp_schema_sdh_unitcode_'.$schema_id,
                                'type'    => 'text',     
                                'note'    => 'Note: Enter unit code as DAY',
                                'attributes' => array(
                                    'placeholder' => 'DAY'
                                ),                 
                            ),
                            array(
                                'label'   => esc_html__( 'Shipping Transit Time Min Value', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_product_grp_schema_sdt_minval_'.$schema_id,
                                'type'    => 'number', 
                                'attributes' => array(
                                    'placeholder' => '2'
                                ),                       
                            ),
                            array(
                                'label'   => esc_html__( 'Shipping Transit Time Max Value', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_product_grp_schema_sdt_maxval_'.$schema_id,
                                'type'    => 'number',  
                                'attributes' => array(
                                    'placeholder' => '5'
                                ),                      
                            ),
                            array(
                                'label'   => esc_html__( 'Shipping Transit Time Unit Code', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_product_grp_schema_sdt_unitcode_'.$schema_id,
                                'type'    => 'text',     
                                'note'    => 'Note: Enter unit code as DAY',  
                                'attributes' => array(
                                    'placeholder' => 'DAY'
                                ),                 
                            ),
                            array(
                                'label' => esc_html__( 'Aggregate Rating', 'schema-and-structured-data-for-wp' ),
                                'id'    => 'saswp_product_grp_srp_schema_enable_rating_'.$schema_id,
                                'type'  => 'checkbox',                            
                            ),                       
                            array(
                                'label'   => esc_html__( 'Rating', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_product_grp_srp_schema_rating_'.$schema_id,
                                'type'    => 'text',
                                'default' => saswp_remove_warnings($product_details, 'product_average_rating', 'saswp_string')
                            ),
                            array(
                                'label'   => esc_html__( 'Number of Reviews', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_product_grp_srp_schema_review_count_'.$schema_id,
                                'type'    => 'text',
                                'default' => saswp_remove_warnings($product_details, 'product_review_count', 'saswp_string')
                            ),
                        );

                    break;
                
                case 'Service':
                    
                    $meta_field = array(
                        array(
                                'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_service_schema_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'Service'   
                        ),
                    array(
                            'label' => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                            'id'    => 'saswp_service_schema_name_'.$schema_id,
                            'type'  => 'text',                    
                    ),
                    array(
                            'label' => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                            'id'    => 'saswp_service_schema_url_'.$schema_id,
                            'type'  => 'text',                    
                    ),    
                    array(
                        'label' => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                        'id' => 'saswp_service_schema_image_'.$schema_id,
                        'type' => 'media',                            
                     ),
                    array(
                            'label' => esc_html__( 'Service Type', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_service_schema_type_'.$schema_id,
                            'type' => 'text',                            
                    ),
                    array(
                        'label' => esc_html__( 'Provider Mobility', 'schema-and-structured-data-for-wp' ),
                        'id'    => 'saswp_service_schema_provider_mobility_'.$schema_id,
                        'type'  => 'text',                            
                    ),
                    array(
                            'label' => esc_html__( 'Provider Name', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_service_schema_provider_name_'.$schema_id,
                            'type' => 'text',                           
                    ),
                    array(
                            'label' => esc_html__( 'Provider Type', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_service_schema_provider_type_'.$schema_id,
                            'type' => 'select',
                            'options' => array(
                                     'Airline'                      => 'Airline',
                                     'Corporation'                  => 'Corporation',
                                     'EducationalOrganization'      => 'Educational Organization',
                                     'School'                       => 'School',
                                     'GovernmentOrganization'       => 'Government Organization',
                                     'LocalBusiness'                => 'Local Business',
                                     'MedicalOrganization'          => 'Medical Organization',  
                                     'NGO'                          => 'NGO', 
                                     'PerformingGroup'              => 'Performing Group', 
                                     'SportsOrganization'           => 'Sports Organization',
                            ),                           
                    ),                        
                    array(
                            'label'      => esc_html__( 'Street Address', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_service_schema_street_address_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label' => esc_html__( 'Locality', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_service_schema_locality_'.$schema_id,
                            'type' => 'text',                            
                    ),
                    array(
                            'label'      => esc_html__( 'Region', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_service_schema_region_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label' => esc_html__( 'Postal Code', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_service_schema_postal_code_'.$schema_id,
                            'type' => 'text',                           
                    ),
                    array(
                            'label' => esc_html__( 'Address Country', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_service_schema_country_'.$schema_id,
                            'type' => 'text',                           
                    ),    
                    array(
                            'label' => esc_html__( 'Telephone', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_service_schema_telephone_'.$schema_id,
                            'type' => 'text',                            
                    ), 
                    array(
                        'label' => esc_html__( 'Price Range', 'schema-and-structured-data-for-wp' ),
                        'id'    => 'saswp_service_schema_price_range_'.$schema_id,
                        'type'  => 'text',                            
                    ),                    
                    array(
                            'label' => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_service_schema_description_'.$schema_id,
                            'type' => 'textarea',                           
                    ),
                    array(
                            'label' => esc_html__( 'Area Served (City)', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_service_schema_area_served_'.$schema_id,
                            'type' => 'textarea',                           
                            'note'   => 'Note: Enter all the City name in comma separated',
                            'attributes' => array(
                                'placeholder' => 'New York, Los Angeles'
                            ),
                    ),
                    array(
                            'label' => esc_html__( 'Service Offer', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_service_schema_service_offer_'.$schema_id,
                            'type' => 'textarea',                           
                            'note'   => 'Note: Enter all the service offer in comma separated',
                            'attributes' => array(
                                'placeholder' => 'Apartment light cleaning, carpet cleaning'
                            )                                                        
                        ),
                        array(
                                'label' => esc_html__( 'Additional Type', 'schema-and-structured-data-for-wp' ),
                                'id'    => 'saswp_service_schema_additional_type_'.$schema_id,
                                'type'  => 'text',                           
                        ),
                        array(
                                'label' => esc_html__( 'Service Output', 'schema-and-structured-data-for-wp' ),
                                'id'    => 'saswp_service_schema_service_output_'.$schema_id,
                                'type'  => 'text',                           
                        ),                                                
                        array(
                                'label' => esc_html__( 'Aggregate Rating', 'schema-and-structured-data-for-wp' ),
                                'id'    => 'saswp_service_schema_enable_rating_'.$schema_id,
                                'type'  => 'checkbox',                           
                            ),
                        array(
                                'label' => esc_html__( 'Rating', 'schema-and-structured-data-for-wp' ),
                                'id'    => 'saswp_service_schema_rating_value_'.$schema_id,
                                'type'  => 'text',                           
                            ),
                        array(
                                'label' => esc_html__( 'Rating Count', 'schema-and-structured-data-for-wp' ),
                                'id'    => 'saswp_service_schema_rating_count_'.$schema_id,
                                'type'  => 'text',                            
                        ),
                        array(
                                'label' => esc_html__( 'Award', 'schema-and-structured-data-for-wp' ),
                                'id'    => 'saswp_service_schema_award_'.$schema_id,
                                'type'  => 'text',                            
                        ),
                            
                    );
                    break;

                    case 'TaxiService':
                    
                        $meta_field = array(
                        array(
                                'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_taxi_service_schema_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'TaxiService'   
                        ),
                        array(
                                'label' => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                                'id'    => 'saswp_taxi_service_schema_name_'.$schema_id,
                                'type'  => 'text',                    
                        ),
                        array(
                                'label' => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                                'id'    => 'saswp_taxi_service_schema_url_'.$schema_id,
                                'type'  => 'text',                    
                        ),    
                        array(
                            'label' => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_taxi_service_schema_image_'.$schema_id,
                            'type' => 'media',                            
                         ),
                        array(
                                'label' => esc_html__( 'Service Type', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_taxi_service_schema_type_'.$schema_id,
                                'type' => 'text',                            
                        ),
                        array(
                                'label' => esc_html__( 'Provider Name', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_taxi_service_schema_provider_name_'.$schema_id,
                                'type' => 'text',                           
                        ),
                        array(
                                'label' => esc_html__( 'Provider Type', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_taxi_service_schema_provider_type_'.$schema_id,
                                'type' => 'select',
                                'options' => array(
                                         'Airline'                      => 'Airline',
                                         'Corporation'                  => 'Corporation',
                                         'EducationalOrganization'      => 'Educational Organization',
                                         'School'                       => 'School',
                                         'GovernmentOrganization'       => 'Government Organization',
                                         'LocalBusiness'                => 'Local Business',
                                         'MedicalOrganization'          => 'Medical Organization',  
                                         'NGO'                          => 'NGO', 
                                         'PerformingGroup'              => 'Performing Group', 
                                         'SportsOrganization'           => 'Sports Organization',
                                ),                           
                        ),                        
                        array(
                                'label' => esc_html__( 'Locality', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_taxi_service_schema_locality_'.$schema_id,
                                'type' => 'text',                            
                        ),
                        array(
                                'label' => esc_html__( 'Postal Code', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_taxi_service_schema_postal_code_'.$schema_id,
                                'type' => 'text',                           
                        ),
                        array(
                                'label' => esc_html__( 'Address Country', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_taxi_service_schema_country_'.$schema_id,
                                'type' => 'text',                           
                        ),    
                        array(
                                'label' => esc_html__( 'Telephone', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_taxi_service_schema_telephone_'.$schema_id,
                                'type' => 'text',                            
                        ), 
                        array(
                            'label' => esc_html__( 'Price Range', 'schema-and-structured-data-for-wp' ),
                            'id'    => 'saswp_taxi_service_schema_price_range_'.$schema_id,
                            'type'  => 'text',                            
                        ),                    
                        array(
                                'label' => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_taxi_service_schema_description_'.$schema_id,
                                'type' => 'textarea',                           
                        ),
                        array(
                                'label' => esc_html__( 'Area Served (City)', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_taxi_service_schema_area_served_'.$schema_id,
                                'type' => 'textarea',                           
                                'note'   => 'Note: Enter all the City name in comma separated',
                                'attributes' => array(
                                    'placeholder' => 'New York, Los Angeles'
                                ),
                        ),
                        array(
                                'label' => esc_html__( 'Service Offer', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_taxi_service_schema_service_offer_'.$schema_id,
                                'type' => 'textarea',                           
                                'note'   => 'Note: Enter all the service offer in comma separated',
                                'attributes' => array(
                                    'placeholder' => 'Apartment light cleaning, carpet cleaning'
                                )                                                        
                            ),
                            array(
                                    'label' => esc_html__( 'Additional Type', 'schema-and-structured-data-for-wp' ),
                                    'id'    => 'saswp_taxi_service_schema_additional_type_'.$schema_id,
                                    'type'  => 'text',                           
                            ),
                            array(
                                    'label' => esc_html__( 'Service Output', 'schema-and-structured-data-for-wp' ),
                                    'id'    => 'saswp_taxi_service_schema_service_output_'.$schema_id,
                                    'type'  => 'text',                           
                            )                        
                        );
                        break;    
                
                case 'Review':
                        $review_item_type = get_post_meta($schema_id, 'saswp_review_item_reviewed_'.$schema_id, true);                
                        $meta_field[] = array(
                            'label' => esc_html__( 'Review Name', 'schema-and-structured-data-for-wp' ),
                            'id'    => 'saswp_review_name_'.$schema_id,
                            'type'  => 'text',              
                            'default' => get_the_title()             
                        );
                        $meta_field[] = array(
                            'label' => esc_html__( 'Review Description', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_review_description_'.$schema_id,
                            'type' => 'textarea',                           
                            'default' => saswp_strip_all_tags(get_the_excerpt())                         
                        );
                        $meta_field[] = array(
                                'label' => esc_html__( 'Review Body', 'schema-and-structured-data-for-wp' ),
                                'id'    => 'saswp_review_body_'.$schema_id,
                                'type'   => 'textarea',                           
                                'default' => saswp_strip_all_tags(get_the_excerpt())                         
                        );                        
                        $meta_field[] = array(
                            'label' => esc_html__( 'Review Author', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_review_author_'.$schema_id,
                            'type' => 'text',                            
                            'default' => is_object($current_user) ?  $current_user->display_name : ''
                        );
                        $meta_field[] = array(
                            'label' => esc_html__( 'Review Author URL', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_review_author_url_'.$schema_id,
                            'type' => 'text',
                            'default' => $author_url                           
                        );
                        $meta_field[] = array(
                            'label' => esc_html__( 'Review Publisher', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_review_publisher_'.$schema_id,
                            'type' => 'text',   
                            'default'=> saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')                        
                        );
                        $meta_field[] = array(
                                'label' => esc_html__( 'Review Publisher URL', 'schema-and-structured-data-for-wp' ),
                                'id'    => 'saswp_review_publisher_url'.$schema_id,
                                'type'  => 'text',                           
                                'default' => get_home_url() 
                            );
                        $meta_field[] = array(
                            'label' => esc_html__( 'Review Published Date', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_review_date_published_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_date("Y-m-d")                           
                        );
                        $meta_field[] = array(
                                'label' => esc_html__( 'Review Modified Date', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_review_date_modified_'.$schema_id,
                                'type' => 'text',
                                'default' => get_the_modified_date("Y-m-d")                           
                            );
                        $meta_field[] = array(
                            'label' => esc_html__( 'Review URL', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_review_url_'.$schema_id,
                            'type' => 'text',               
                            'default' => get_permalink()             
                        ); 
                        $meta_field[] = array(
                            'label' => esc_html__( 'Review Rating', 'schema-and-structured-data-for-wp' ),
                            'id'    => 'saswp_review_enable_rating_'.$schema_id,
                            'type'  => 'checkbox',                           
                        );
                        $meta_field[] = array(
                            'label' => esc_html__( 'Rating Value', 'schema-and-structured-data-for-wp' ),
                            'id'    => 'saswp_review_rating_'.$schema_id,
                            'type'  => 'text',                            
                        );
                        $meta_field[] = array(
                            'label' => esc_html__( 'Best Rating', 'schema-and-structured-data-for-wp' ),
                            'id'    => 'saswp_review_review_count_'.$schema_id,
                            'type'  => 'text',                            
                        );
                        $meta_field[] = array(
                                'label' => esc_html__( 'Worst Rating', 'schema-and-structured-data-for-wp' ),
                                'id'    => 'saswp_review_worst_count_'.$schema_id,
                                'type'  => 'text',                            
                        );
                        
                        if($manual == null){
                         
                            $meta_field[] = array(
                            'label'   => esc_html__( 'Item Reviewed Type', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_review_item_reviewed_'.$schema_id,
                            'type'    => 'select',
                            'options' => array(
                                        'Book'                  => 'Book',                             
                                        'Course'                => 'Course',                             
                                        'Event'                 => 'Event',                              
                                        'HowTo'                 => 'HowTo',   
                                        'local_business'        => 'LocalBusiness',                                 
                                        'MusicPlaylist'         => 'Music Playlist',
                                        'Movie'                 => 'Movie',
                                        'Organization'          => 'Organization', 
                                        'Product'               => 'Product',                                
                                        'Recipe'                => 'Recipe',                             
                                        'SoftwareApplication'   => 'SoftwareApplication',
                                        'MobileApplication'     => 'MobileApplication',
                                        'VideoGame'             => 'VideoGame', 
                            ),
                            'default' => $review_item_type,                                                        
                         );
                                                        
                        }                                                                   
                                                                                
                    break;

                case 'CriticReview':
                                        
                        $meta_field[] = array(
                            'label' => esc_html__( 'Review Name', 'schema-and-structured-data-for-wp' ),
                            'id'    => 'saswp_review_name_'.$schema_id,
                            'type'  => 'text',              
                            'default' => get_the_title()             
                        );
                        $meta_field[] = array(
                            'label' => esc_html__( 'Review Description', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_review_description_'.$schema_id,
                            'type' => 'textarea',                           
                            'default' => saswp_strip_all_tags(get_the_excerpt())                         
                        );
                        $meta_field[] = array(
                                'label' => esc_html__( 'Review Body', 'schema-and-structured-data-for-wp' ),
                                'id'    => 'saswp_review_body_'.$schema_id,
                                'type'   => 'textarea',                           
                                'default' => saswp_strip_all_tags(get_the_excerpt())                         
                        );                        
                        $meta_field[] = array(
                            'label' => esc_html__( 'Review Author', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_review_author_'.$schema_id,
                            'type' => 'text',                            
                            'default' => is_object($current_user) ?  $current_user->display_name : ''
                        );
                        $meta_field[] = array(
                            'label' => esc_html__( 'Review Author URL', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_review_author_url_'.$schema_id,
                            'type' => 'text',
                            'default' => $author_url                           
                        );
                        $meta_field[] = array(
                            'label' => esc_html__( 'Review Publisher', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_review_publisher_'.$schema_id,
                            'type' => 'text',   
                            'default'=> saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')                        
                        );
                        $meta_field[] = array(
                                'label' => esc_html__( 'Review Publisher URL', 'schema-and-structured-data-for-wp' ),
                                'id'    => 'saswp_review_publisher_url'.$schema_id,
                                'type'  => 'text',                           
                                'default' => get_home_url() 
                            );
                        $meta_field[] = array(
                            'label' => esc_html__( 'Review Published Date', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_review_date_published_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_date("Y-m-d")                           
                        );
                        $meta_field[] = array(
                                'label' => esc_html__( 'Review Modified Date', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_review_date_modified_'.$schema_id,
                                'type' => 'text',
                                'default' => get_the_modified_date("Y-m-d")                           
                            );
                        $meta_field[] = array(
                            'label' => esc_html__( 'Review URL', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_review_url_'.$schema_id,
                            'type' => 'text',               
                            'default' => get_permalink()             
                        ); 
                        $meta_field[] = array(
                            'label' => esc_html__( 'Review Rating', 'schema-and-structured-data-for-wp' ),
                            'id'    => 'saswp_review_enable_rating_'.$schema_id,
                            'type'  => 'checkbox',                           
                        );
                        $meta_field[] = array(
                            'label' => esc_html__( 'Rating Value', 'schema-and-structured-data-for-wp' ),
                            'id'    => 'saswp_review_rating_'.$schema_id,
                            'type'  => 'text',                            
                        );
                        $meta_field[] = array(
                            'label' => esc_html__( 'Best Rating', 'schema-and-structured-data-for-wp' ),
                            'id'    => 'saswp_review_review_count_'.$schema_id,
                            'type'  => 'text',                            
                        );
                        $meta_field[] = array(
                                'label' => esc_html__( 'Worst Rating', 'schema-and-structured-data-for-wp' ),
                                'id'    => 'saswp_review_worst_count_'.$schema_id,
                                'type'  => 'text',                            
                        );
                        
                        if($manual == null){
                         
                            $meta_field[] = array(
                            'label'   => esc_html__( 'Item Reviewed Type', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_review_item_reviewed_'.$schema_id,
                            'type'    => 'select',
                            'options' => array(
                                        'Book'                  => 'Book',                             
                                        'Course'                => 'Course',                             
                                        'Event'                 => 'Event',                              
                                        'HowTo'                 => 'HowTo',   
                                        'local_business'        => 'LocalBusiness',                                 
                                        'MusicPlaylist'         => 'Music Playlist',
                                        'Movie'                 => 'Movie',
                                        'Organization'          => 'Organization', 
                                        'Product'               => 'Product',                                
                                        'Recipe'                => 'Recipe',                             
                                        'SoftwareApplication'   => 'SoftwareApplication',
                                        'MobileApplication'     => 'MobileApplication',
                                        'VideoGame'             => 'VideoGame', 
                            )                                                        
                         );
                                                        
                        }                                                                   
                                                                                
                    break;
                
                case 'AudioObject':
                                         
                    $meta_field = array(
                        array(
                                'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_audio_schema_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'AudioObject'   
                        ), 
                    array(
                            'label' => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_audio_schema_name_'.$schema_id,
                            'type' => 'text', 
                            'default'=> saswp_get_the_title()                          
                    ),
                    array(
                            'label' => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_audio_schema_description_'.$schema_id,
                            'type' => 'textarea',            
                            'default' => saswp_strip_all_tags(get_the_excerpt())                
                    ),
                    array(
                            'label' => esc_html__( 'Content URL', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_audio_schema_contenturl_'.$schema_id,
                            'type' => 'text',  
                            'default' => get_permalink()                          
                    ),
                   array(
                            'label' => esc_html__( 'Duration', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_audio_schema_duration_'.$schema_id,
                            'type' => 'text',                            
                    ),
                     array(
                            'label' => esc_html__( 'Encoding Format', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_audio_schema_encoding_format_'.$schema_id,
                            'type' => 'text',                           
                    ),                           
                    array(
                            'label' => esc_html__( 'Date Published', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_audio_schema_date_published_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_date("Y-m-d")
                    ),
                    array(
                            'label' => esc_html__( 'Date Modified', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_audio_schema_date_modified_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_modified_date("Y-m-d")
                    ),
                    array(
                        'label'   => esc_html__( 'Author Type', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_audio_schema_author_type_'.$schema_id,
                        'type'    => 'select',
                        'options' => array(
                                'Person'           => 'Person',
                                'Organization'     => 'Organization',                        
                       )
                    ),
                    array(
                            'label' => esc_html__( 'Author Name', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_audio_schema_author_name_'.$schema_id,
                            'type' => 'text',  
                            'default' => is_object($current_user) ? $current_user->display_name : ''                          
                    ),
                    array(
                            'label'   => esc_html__( 'Author Description', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_audio_schema_author_description_'.$schema_id,
                            'type'    => 'textarea',
                            'default' => $author_desc
                    ),
                    array(
                            'label'   => esc_html__( 'Author URL', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_audio_schema_author_url_'.$schema_id,
                            'type'    => 'text',
                            'default' => $author_url
                    )                                                  
                    );
                    break;
                
                case 'SoftwareApplication':
                                        
                    $meta_field = array(
                        array(
                                'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_software_schema_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'SoftwareApplication'   
                        ),
                    array(
                            'label' => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_software_schema_name_'.$schema_id,
                            'type' => 'text',                           
                    ),
                    array(
                            'label' => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_software_schema_description_'.$schema_id,
                            'type' => 'textarea',                            
                    ),
                    array(
                            'label' => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                            'id'    => 'saswp_software_schema_image_'.$schema_id,
                            'type'  => 'media',                            
                    ),    
                    array(
                            'label' => esc_html__( 'Operating System', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_software_schema_operating_system_'.$schema_id,
                            'type' => 'text',                            
                    ),
                    array(
                            'label' => esc_html__( 'Application Category', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_software_schema_application_category_'.$schema_id,
                            'type' => 'text',                            
                    ),
                    array(
                            'label' => esc_html__( 'Price', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_software_schema_price_'.$schema_id,
                            'type' => 'text',                            
                    ),
                    array(
                            'label' => esc_html__( 'Price Currency', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_software_schema_price_currency_'.$schema_id,
                            'type' => 'text',                           
                    ),                            
                    array(
                            'label' => esc_html__( 'Date Published', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_software_schema_date_published_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_date("Y-m-d")
                    ),
                    array(
                            'label' => esc_html__( 'Date Modified', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_software_schema_date_modified_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_modified_date("Y-m-d")
                    ),
                    array(
                            'label' => esc_html__( 'Aggregate Rating', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_software_schema_enable_rating_'.$schema_id,
                            'type' => 'checkbox',                           
                        ),
                    array(
                            'label' => esc_html__( 'Rating', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_software_schema_rating_'.$schema_id,
                            'type' => 'text',                           
                        ),
                    array(
                            'label' => esc_html__( 'Rating Count', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_software_schema_rating_count_'.$schema_id,
                            'type' => 'text',                            
                        ),    
                    );
                    break;

                    case 'MobileApplication':
                                        
                        $meta_field = array(
                        array(
                                'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_mobile_app_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'MobileApplication'   
                        ),
                        
                        array(
                                'label' => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_mobile_app_schema_name_'.$schema_id,
                                'type' => 'text',                           
                        ),
                        array(
                                'label' => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_mobile_app_schema_description_'.$schema_id,
                                'type' => 'textarea',                            
                        ),
                        array(
                                'label' => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                                'id'    => 'saswp_mobile_app_schema_image_'.$schema_id,
                                'type'  => 'media',                            
                        ),    
                        array(
                                'label' => esc_html__( 'Operating System', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_mobile_app_schema_operating_system_'.$schema_id,
                                'type' => 'text',                            
                        ),
                        array(
                                'label' => esc_html__( 'Application Category', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_mobile_app_schema_application_category_'.$schema_id,
                                'type' => 'text',                            
                        ),
                        array(
                                'label' => esc_html__( 'Price', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_mobile_app_schema_price_'.$schema_id,
                                'type' => 'text',                            
                        ),
                        array(
                                'label' => esc_html__( 'Price Currency', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_mobile_app_schema_price_currency_'.$schema_id,
                                'type' => 'text',                           
                        ),                            
                        array(
                                'label' => esc_html__( 'Date Published', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_mobile_app_schema_date_published_'.$schema_id,
                                'type' => 'text',
                                'default' => get_the_date("Y-m-d")
                        ),
                        array(
                                'label' => esc_html__( 'Date Modified', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_mobile_app_schema_date_modified_'.$schema_id,
                                'type' => 'text',
                                'default' => get_the_modified_date("Y-m-d")
                        ),
                        array(
                                'label' => esc_html__( 'Aggregate Rating', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_mobile_app_schema_enable_rating_'.$schema_id,
                                'type' => 'checkbox',                           
                            ),
                        array(
                                'label' => esc_html__( 'Rating', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_mobile_app_schema_rating_value_'.$schema_id,
                                'type' => 'text',                           
                            ),
                        array(
                                'label' => esc_html__( 'Rating Count', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_mobile_app_schema_rating_count_'.$schema_id,
                                'type' => 'text',                            
                            ),    
                        );
                        break;    
                
                case 'VideoObject':

                    $video_links      = saswp_get_video_metadata();                        

                    $meta_field = array(
                        array(
                                'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_video_object_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'VideoObject'   
                        ),
                    array(
                            'label' => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_video_object_url_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink()
                    ),
                    array(
                            'label' => esc_html__( 'Headline', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_video_object_headline_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_get_the_title()
                    ),
                    array(
                            'label' => esc_html__( 'Date Published', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_video_object_date_published_'.$schema_id,
                            'type' => 'text',
                             'default' => get_the_date("Y-m-d")
                    ), 
                    array(
                            'label' => esc_html__( 'Date date Modified', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_video_object_date_modified_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_modified_date("Y-m-d")
                    ),
                    array(
                            'label'   => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_video_object_description_'.$schema_id,
                            'type'    => 'textarea',
                            'default' => saswp_strip_all_tags(get_the_excerpt())
                    ),
                    array(
                            'label'   => esc_html__( 'Transcript', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_video_object_transcript_'.$schema_id,
                            'type'    => 'textarea',
                            'default' => is_object($post) ? saswp_strip_all_tags($post->post_content) : ''
                    ),
                    array(
                            'label'   => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_video_object_name_'.$schema_id,
                            'type'    => 'text',
                            'default' => saswp_get_the_title()
                    ),
                    array(
                            'label' => esc_html__( 'Upload Date', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_video_object_upload_date_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_date("Y-m-d")
                    ),
                    array(
                            'label' => esc_html__( 'Thumbnail Url', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_video_object_thumbnail_url_'.$schema_id,
                            'type' => 'media',                            
                    ),
                    array(
                            'label' => esc_html__( 'Content Url', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_video_object_content_url_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink()
                    ),
                    array(
                            'label' => esc_html__( 'Duration', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_video_object_duration_'.$schema_id,
                            'type' => 'text',
                            'attributes' => array(
                                'placeholder' => 'PT1H10M54S'
                            )                                                         
                    ),    
                    array(
                            'label'   => esc_html__( 'Embed Url', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_video_object_embed_url_'.$schema_id,
                            'type'    => 'text',
                            'default' => isset($video_links[0]['video_url']) ? $video_links[0]['video_url'] : get_permalink()                            
                    ),
                    array(
                        'label'   => esc_html__( 'Seek To Video URL', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_video_object_seek_to_video_url_'.$schema_id,
                        'type'    => 'text'                        
                    ),    
                    array(
                        'label'   => esc_html__( 'Seek To Second Number', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_video_object_seek_to_seconds_'.$schema_id,
                        'type'    => 'number'                        
                    ),    
                    array(
                            'label'   => esc_html__( 'Main Entity Id', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_video_object_main_entity_id_'.$schema_id,
                            'type'    => 'text',
                            'default' => get_permalink()
                    ),
                    array(
                            'label'   => esc_html__( 'Main Entity of page', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_video_object_main_entity_of_page_'.$schema_id,
                            'type'    => 'text',
                            'default' => get_permalink(),
                            'is_template_attr' => 'yes',
                    ),
                    array(
                        'label'   => esc_html__( 'Author Type', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_video_object_author_type_'.$schema_id,
                        'type'    => 'select',
                        'options' => array(
                                'Person'           => 'Person',
                                'Organization'     => 'Organization',                        
                       )
                    ),
                    array(
                            'label'   => esc_html__( 'Author Name', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_video_object_author_name_'.$schema_id,
                            'type'    => 'text',
                            'default' => is_object($current_user) ? $current_user->display_name : ''    
                    ),
                    array(
                            'label'   => esc_html__( 'Author Description', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_video_object_author_description_'.$schema_id,
                            'type'    => 'textarea',
                            'default' => $author_desc
                    ), 
                    array(
                            'label'   => esc_html__( 'Author URL', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_video_object_author_url_'.$schema_id,
                            'type'    => 'text',
                            'default' => $author_url
                    ),    
                    array(
                            'label'   => esc_html__( 'Author Image', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_video_object_author_image_'.$schema_id,
                            'type'    => 'media',
                            'default' => isset($author_details['url']) ? $author_details['url'] : ''   
                    ),
                    array(
                            'label'   => esc_html__( 'Organization Name', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_video_object_organization_name_'.$schema_id,
                            'type'    => 'text',
                            'default' => isset($sd_data['sd_name']) ? $sd_data['sd_name'] : ''
                    ),
                    array(
                            'label'   => esc_html__( 'Organization Logo', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_video_object_organization_logo_'.$schema_id,
                            'type'    => 'media',
                            'default' => isset($sd_data['sd_logo']['url']) ? $sd_data['sd_logo']['url'] : ''
                    ),    
                   );
                    break;
                
                case 'ImageObject':
                    $meta_field = array(
                    array(
                            'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswpimage_object_id_'.$schema_id,
                            'type'       => 'text',
                            'default'    => 'ImageObject'   
                    ),
                    array(
                            'label' => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswpimage_object_url_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink()
                    ),
                    array(
                            'label'   => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswpimage_object_image_'.$schema_id,
                            'type'    => 'media'                            
                    ),                    
                    array(
                            'label' => esc_html__( 'Date Published', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswpimage_object_date_published_'.$schema_id,
                            'type' => 'text',
                             'default' => get_the_date("Y-m-d")
                    ), 
                    array(
                            'label' => esc_html__( 'Date date Modified', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswpimage_object_date_modified_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_modified_date("Y-m-d")
                    ),
                    array(
                            'label' => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswpimage_object_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => saswp_strip_all_tags(get_the_excerpt())
                    ),
                    array(
                            'label' => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswpimage_object_name_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_get_the_title()
                    ),
                    array(
                            'label' => esc_html__( 'Upload Date', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswpimage_object_upload_date_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_date("Y-m-d")
                    ),                    
                    array(
                            'label' => esc_html__( 'Content Url', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswpimage_object_content_url_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink()
                    ),
                    array(
                            'label' => esc_html__( 'Content Location', 'schema-and-structured-data-for-wp' ),
                            'id'    => 'saswpimage_object_content_location_'.$schema_id,
                            'type'  => 'text'                            
                    ),
                    array(
                            'label' => esc_html__( 'Acquire License Page', 'schema-and-structured-data-for-wp' ),
                            'id'    => 'saswpimage_object_acquire_license_page_'.$schema_id,
                            'type'  => 'text'                            
                    ),
                    array(
                        'label'   => esc_html__( 'Author Type', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswpimage_object_author_type_'.$schema_id,
                        'type'    => 'select',
                        'options' => array(
                                'Person'           => 'Person',
                                'Organization'     => 'Organization',                        
                       )
                    ),    
                    array(
                            'label' => esc_html__( 'Author Name', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswpimage_object_author_name_'.$schema_id,
                            'type' => 'text',
                            'default' => is_object($current_user) ? $current_user->display_name : ''    
                    ),
                    array(
                            'label' => esc_html__( 'Author Description', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswpimage_object_author_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => $author_desc
                    ),
                    array(
                            'label'   => esc_html__( 'Author URL', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswpimage_object_author_url_'.$schema_id,
                            'type'    => 'text',
                            'default' => $author_url
                    ),    
                    array(
                            'label'   => esc_html__( 'Author Image', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswpimage_object_author_image_'.$schema_id,
                            'type'    => 'media',
                            'default' => isset($author_details['url']) ? $author_details['url'] : ''   
                    ),
                    array(
                        'label'   => esc_html__( 'License','schema-and-structured-data-for-wp' ),
                        'id'      => 'saswpimage_object_license_'.$schema_id,
                        'type'    => 'text',                        
                    ),
                    array(
                            'label'   => esc_html__( 'Organization Name', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswpimage_object_organization_name_'.$schema_id,
                            'type'    => 'text',
                            'default' => isset($sd_data['sd_name']) ? $sd_data['sd_name'] : ''
                    ),
                    array(
                            'label'   => esc_html__( 'Organization Logo', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswpimage_object_organization_logo_'.$schema_id,
                            'type'    => 'media',
                            'default' => isset($sd_data['sd_logo']['url']) ? $sd_data['sd_logo']['url'] : ''
                    ),    
                   );
                    break;
                
                case 'qanda':
                    
                    $meta_field = array(
                    array(
                            'label' => esc_html__( 'Question Title', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_qa_question_title_'.$schema_id,
                            'type' => 'text',                           
                    ),
                    array(
                            'label' => esc_html__( 'Question Description', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_qa_question_description_'.$schema_id,
                            'type' => 'text',                           
                    ),                    
                    array(
                            'label' => esc_html__( 'Question Upvote Count', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_qa_upvote_count_'.$schema_id,
                            'type' => 'number',                           
                    ),
                    array(
                            'label' => esc_html__( 'Question Date Created', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_qa_date_created_'.$schema_id,
                            'type' => 'text',                           
                    ),    
                    array(
                            'label' => esc_html__( 'Author Type', 'schema-and-structured-data-for-wp' ),
                            'id'    => 'saswp_qa_question_author_type_'.$schema_id,
                            'type'  => 'select',
                            'options' => array(
                                    'Person'       => 'Person',
                                    'Organization' => 'Organization'
                            )                           
                    ),
                    array(
                            'label' => esc_html__( 'Author Name', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_qa_question_author_name_'.$schema_id,
                            'type' => 'text',                           
                    ),  
                    array(
                            'label'      => esc_html__( 'Author URL', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_qa_question_author_url_'.$schema_id,
                            'type'       => 'text',
                            'default'    => $author_url
                    ),      
                    array(
                        'label' => esc_html__( 'Answer Count', 'schema-and-structured-data-for-wp' ),
                        'id'    => 'saswp_qa_answer_count_'.$schema_id,
                        'type'  => 'number',                           
                    )                                            
                        
                   );
                    break;
                
                case 'HowTo':
                    
                    $meta_field = array(
                    array(
                            'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_howto_schema_id_'.$schema_id,
                            'type'       => 'text',
                            'default'    => 'HowTo'   
                    ),    
                    array(
                            'label'      => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_howto_schema_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                    ),
                    array(
                            'label'      => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_howto_schema_description_'.$schema_id,
                            'type'       => 'textarea',                            
                    ), 
                    array(
                            'label'      => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_howto_schema_image_'.$schema_id,
                            'type'       => 'media',                            
                    ),     
                    array(
                            'label'      => esc_html__( 'Estimated Cost Currency', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_howto_ec_schema_currency_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'USD'
                            ), 
                    ),
                    array(
                            'label'      => esc_html__( 'Estimated Cost Value', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_howto_ec_schema_value_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => '20'
                            ), 
                    ),
                    array(
                            'label'      => esc_html__( 'Total Time', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_howto_schema_totaltime_'.$schema_id,
                            'type'       => 'text', 
                            'attributes' => array(
                                'placeholder' => 'PT30M'
                            ), 
                    ),
                     array(
                            'label'      => esc_html__( 'Date Published', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_howto_ec_schema_date_published_'.$schema_id,
                            'type'       => 'text', 
                            
                    ),
                    array(
                                'label'      => esc_html__( 'Date Modified', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_howto_ec_schema_date_modified_'.$schema_id,
                                'type'       => 'text',                             
                    ),
                       
                        array(
                                'label'      => esc_html__( 'Video Name', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_howto_schema_video_name_'.$schema_id,
                                'type'       => 'text',
                                'attributes' => array(
                                        'placeholder' => 'Build a Trivia Game for the Google Assistant with No Code'
                                    ),                             
                        ),
                        array(
                                'label'      => esc_html__( 'Video Description','schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_howto_schema_video_description_'.$schema_id,
                                'type'       => 'textarea',
                                'attributes' => array(
                                        'placeholder' => 'Learn how to create a Trivia action for Assistant within minutes.'
                                    ),                             
                        ),
                        array(
                                'label'      => esc_html__( 'Video Thumbnail URL', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_howto_schema_video_thumbnail_url_'.$schema_id,
                                'type'       => 'text',
                                'attributes' => array(
                                        'placeholder' => 'https://example.com/photos/photo.jpg'
                                    ),                             
                        ),
                        array(
                                'label'      => esc_html__( 'Video Content URL', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_howto_schema_video_content_url_'.$schema_id,
                                'type'       => 'text', 
                                'attributes' => array(
                                        'placeholder' => 'https://www.youtube.com/watch?v=4AOI1tZrgMI'
                                    ),                            
                        ),
                        array(
                                'label'      => esc_html__( 'Video Embed URL', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_howto_schema_video_embed_url_'.$schema_id,
                                'type'       => 'text',
                                'attributes' => array(
                                        'placeholder' => 'https://www.youtube.com/embed/4AOI1tZrgMI'
                                    ),                             
                        ),
                        array(
                                'label'      => esc_html__( 'Video Upload Date', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_howto_schema_video_upload_date_'.$schema_id,
                                'type'       => 'text',  
                                'attributes' => array(
                                        'placeholder' => '2019-01-05'
                                    ),                           
                        ),
                        array(
                                'label'      => esc_html__( 'Video Duration', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_howto_schema_video_duration_'.$schema_id,
                                'type'       => 'text', 
                                'attributes' => array(
                                        'placeholder' => 'P1MT10S'
                                    ),                            
                        ),

                    array(
                        'label'      => esc_html__( 'Supplies', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_howto_schema_supplies_'.$schema_id,
                        'type'       => 'repeater'                                                     
                    ),
                    array(
                        'label'      => esc_html__( 'Tools', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_howto_schema_tools_'.$schema_id,
                        'type'       => 'repeater'                                                     
                    ),
                    array(
                        'label'      => esc_html__( 'Steps', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_howto_schema_steps_'.$schema_id,
                        'type'       => 'repeater'                                                     
                    ),
                    array(
                        'label'   => esc_html__( 'About', 'schema-and-structured-data-for-wp' ),
                        'id'      => 'saswp_howto_about_'.$schema_id,
                        'type'    => 'textarea',
                        'default' => '',
                        'note'    => 'Note: If There are more than one About, Separate About list by comma ( , )',
                        'attributes' => array(
                                'placeholder' => 'eg: Apple is March 21 Announcements'
                        ),
                   )
                   );
                    break;
                
                case 'MedicalCondition':
                    
                    $meta_field = array(
                        array(
                                'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_mc_schema_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'MedicalCondition'   
                        ),
                    array(
                            'label'      => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_mc_schema_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                    ),
                    array(
                            'label'      => esc_html__( 'Alternate Name', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_mc_schema_alternate_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Alternate Name'
                            ), 
                    ),    
                    array(
                            'label'      => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_mc_schema_description_'.$schema_id,
                            'type'       => 'textarea',                            
                    ),
                    array(
                            'label'      => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_mc_schema_image_'.$schema_id,
                            'type'       => 'media',                            
                    ),                             
                    array(
                            'label'      => esc_html__( 'Associated Anatomy Name', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_mc_schema_anatomy_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                    ),
                    array(
                            'label'      => esc_html__( 'Medical Code', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_mc_schema_medical_code_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => '413'
                            ), 
                    ),
                    array(
                            'label'      => esc_html__( 'Coding System', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_mc_schema_coding_system_'.$schema_id,
                            'type'       => 'text', 
                            'attributes' => array(
                                'placeholder' => 'ICD-9'
                            ), 
                    ),
                     array(
                            'label'      => esc_html__( 'Diagnosis Name', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_mc_schema_diagnosis_name_'.$schema_id,
                            'type'       => 'text', 
                            
                     ),
                     array(
                        'label'      => esc_html__( 'Drug', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_mc_schema_drug_'.$schema_id,
                        'type'       => 'text', 
                        
                     ),
                     array(
                        'label'      => esc_html__( 'Primary Prevention Name', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_mc_schema_primary_prevention_name_'.$schema_id,
                        'type'       => 'text', 
                        
                     ),
                     array(
                        'label'      => esc_html__( 'Primary Prevention Performed', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_mc_schema_primary_prevention_performed_'.$schema_id,
                        'type'       => 'textarea',                         
                     ),
                     array(
                        'label'      => esc_html__( 'Possible Treatment Name', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_mc_schema_possible_treatment_name_'.$schema_id,
                        'type'       => 'text', 
                        
                     ),
                     array(
                        'label'      => esc_html__( 'Possible Treatment Performed', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_mc_schema_possible_treatment_performed_'.$schema_id,
                        'type'       => 'textarea',                         
                     )                                          
                   );
                    break;
                
                case 'VideoGame':
                    
                    $meta_field = array(
                        array(
                                'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_vg_schema_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'VideoGame'   
                        ),
                    array(
                            'label'      => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_vg_schema_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                    ),
                    array(
                            'label'      => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_vg_schema_url_'.$schema_id,
                            'type'       => 'text',
                            
                    ),
                    array(
                            'label'      => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_vg_schema_image_'.$schema_id,
                            'type'       => 'media',
                            
                    ),
                    array(
                            'label'      => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_vg_schema_description_'.$schema_id,
                            'type'       => 'textarea',
                            
                    ),
                    array(
                            'label'      => esc_html__( 'Operating System', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_vg_schema_operating_system_'.$schema_id,
                            'type'       => 'text',
                            
                    ),
                    array(
                            'label'      => esc_html__( 'Application Category', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_vg_schema_application_category_'.$schema_id,
                            'type'       => 'text',
                            
                    ),
                    array(
                        'label'      => esc_html__( 'Author Type', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_vg_schema_author_type_'.$schema_id,
                        'type'    => 'select',
                        'options' => array(
                                'Person'           => 'Person',
                                'Organization'     => 'Organization',                        
                       )
                        
                    ),
                    array(
                            'label'      => esc_html__( 'Author Name', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_vg_schema_author_name_'.$schema_id,
                            'type'       => 'text',
                            
                    ),
                    array(
                            'label'      => esc_html__( 'Price', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_vg_schema_price_'.$schema_id,
                            'type'       => 'text',
                            
                    ),
                    array(
                            'label'      => esc_html__( 'Price Currency', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_vg_schema_price_currency_'.$schema_id,
                            'type'       => 'text',
                            
                    ),    
                    array(
                            'label'   => esc_html__( 'Availability', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_vg_schema_price_availability_'.$schema_id,
                            'type'    => 'select',                            
                            'options' => array(
                                     ''                  => 'Select',
                                     'InStock'           => 'In Stock',
                                     'OutOfStock'        => 'Out Of Stock',
                                     'Discontinued'      => 'Discontinued',
                                     'PreOrder'          => 'Pre Order', 
                            ) 
                       ), 
                    array(
                            'label'      => esc_html__( 'Publisher', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_vg_schema_publisher_'.$schema_id,
                            'type'       => 'text',
                            
                    ),
                    array(
                            'label'      => esc_html__( 'Publisher', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_vg_schema_genre_'.$schema_id,
                            'type'       => 'text',
                            
                    ),
                    array(
                            'label'      => esc_html__( 'Processor Requirements', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_vg_schema_processor_requirements_'.$schema_id,
                            'type'       => 'text',
                            
                    ),
                    array(
                            'label'      => esc_html__( 'Memory Requirements', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_vg_schema_memory_requirements_'.$schema_id,
                            'type'       => 'text',
                            
                    ),
                    array(
                            'label'      => esc_html__( 'Storage Requirements', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_vg_schema_storage_requirements_'.$schema_id,
                            'type'       => 'text',
                            
                    ),
                    array(
                            'label'      => esc_html__( 'Game Platform', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_vg_schema_game_platform_'.$schema_id,
                            'type'       => 'text',
                            
                    ),
                    array(
                            'label'      => esc_html__( 'Cheat Code', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_vg_schema_cheat_code_'.$schema_id,
                            'type'       => 'text',
                            
                    ),
                    array(
                        'label'      => esc_html__( 'File Size', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_vg_schema_file_size_'.$schema_id,
                        'type'       => 'text'                        
                    ),
                    array(
                            'label' => esc_html__( 'Aggregate Rating', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_vg_schema_enable_rating_'.$schema_id,
                            'type' => 'checkbox',                          
                        ),
                        array(
                            'label' => esc_html__( 'Rating','schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_vg_schema_rating_'.$schema_id,
                            'type' => 'text',                           
                        ),
                        array(
                            'label' => esc_html__( 'Rating Count', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_vg_schema_review_count_'.$schema_id,
                            'type' => 'text',                           
                        ),    
                        
                   );
                    break;
                
                case 'TVSeries':
                    
                    $meta_field = array(
                        array(
                                'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_tvseries_schema_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'TVSeries'   
                        ),
                    array(
                            'label'      => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_tvseries_schema_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                    ),
                    array(
                            'label'      => esc_html__( 'Genre', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_tvseries_schema_genre_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                    ),
                     array(
                            'label'      => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_tvseries_schema_image_'.$schema_id,
                            'type'       => 'media'                            
                    ),
                    array(
                        'label'      => esc_html__( 'Author Type', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_tvseries_schema_author_type_'.$schema_id,
                        'type'       => 'select',
                        'options'   => array(
                                'Person'           => 'Person',
                                'Organization'     => 'Organization',                        
                       )
                    ),
                    array(
                            'label'      => esc_html__( 'Author Name', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_tvseries_schema_author_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Author Name'
                            ), 
                    ),    
                    array(
                            'label'      => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_tvseries_schema_description_'.$schema_id,
                            'type'       => 'textarea'                            
                    ),
                    array(
                            'label'      => esc_html__( 'Duration', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_tvseries_schema_duration_'.$schema_id,
                            'type'       => 'text'                            
                    ),
                    array(
                            'label'      => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_tvseries_schema_url_'.$schema_id,
                            'type'       => 'text'                            
                    ),
                    array(
                            'label'      => esc_html__( 'Number Of Seasons', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_tvseries_schema_nos_'.$schema_id,
                            'type'       => 'number'                            
                    ),
                    array(
                            'label'      => esc_html__( 'Number Of Episodes', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_tvseries_schema_noe_'.$schema_id,
                            'type'       => 'number'                            
                    ),
                    array(
                            'label'      => esc_html__( 'Date Published', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_tvseries_schema_date_published_'.$schema_id,
                            'type'       => 'text',
                            'default'    => get_the_date("Y-m-d")
                    ), 
                    array(
                            'label'      => esc_html__( 'Date Modified', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_tvseries_schema_date_modified_'.$schema_id,
                            'type'       => 'text',
                            'default'    => get_the_modified_date("Y-m-d")
                    ),
                    array(
                            'label'   => esc_html__( 'Trailer', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_tvseries_schema_trailer_'.$schema_id,
                            'type'    => 'text',
                            'is_template_attr' => 'yes',
                    ),
                    array(
                            'label'   => esc_html__( 'Subject Of', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_tvseries_schema_subject_of_'.$schema_id,
                            'type'    => 'text',
                            'is_template_attr' => 'yes',
                    ),
                    array(
                            'label' => esc_html__( 'Aggregate Rating', 'schema-and-structured-data-for-wp' ),
                            'id'    => 'saswp_tvseries_schema_enable_rating_'.$schema_id,
                            'type'  => 'checkbox',                            
                    ),
                    array(
                            'label' => esc_html__( 'Rating', 'schema-and-structured-data-for-wp' ),
                            'id'    => 'saswp_tvseries_schema_rating_value_'.$schema_id,
                            'type'  => 'text',                            
                    ),
                    array(
                            'label' => esc_html__( 'Number of Reviews', 'schema-and-structured-data-for-wp' ),
                            'id'    => 'saswp_tvseries_schema_review_count_'.$schema_id,
                            'type'  => 'text',                            
                    )  
                        
                   );
                    break;
                
                case 'Apartment':
                    
                    $meta_field = array(
                        array(
                                'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_apartment_schema_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'Apartment'   
                        ),
                    array(
                            'label'      => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_apartment_schema_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                    ),
                    array(
                            'label'      => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_apartment_schema_url_'.$schema_id,
                            'type'       => 'text',
                            'default'    => get_permalink()
                    ),
                    array(
                            'label'      => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_apartment_schema_image_'.$schema_id,
                            'type'       => 'media',
                            'default'    => get_permalink()
                    ),    
                    array(
                            'label'      => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_apartment_schema_description_'.$schema_id,
                            'type'       => 'textarea',
                            'attributes' => array(
                                'placeholder' => 'Description'
                            ), 
                    ),
                    array(
                            'label'      => esc_html__( 'Number Of Rooms', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_apartment_schema_numberofrooms_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => '5'
                            ), 
                    ),
                    array(
                            'label'      => esc_html__( 'Floor Size', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_apartment_schema_floor_size_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => '140 Sq.Ft'
                            ), 
                    ),    
                    array(
                            'label'      => esc_html__( 'Country', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_apartment_schema_country_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => esc_html__( 'Locality', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_apartment_schema_locality_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => esc_html__( 'Region', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_apartment_schema_region_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => esc_html__( 'Postal Code', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_apartment_schema_postalcode_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => esc_html__( 'Telephone', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_apartment_schema_telephone_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => esc_html__( 'GeoCoordinates Latitude', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_apartment_schema_latitude_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => '17.412'
                            ), 
                    ),
                    array(
                            'label'      => esc_html__( 'GeoCoordinates Longitude', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_apartment_schema_longitude_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => '78.433'
                            ),
                    ),    
                                              
                   );
                    break;
                
                case 'House':
                    
                    $meta_field = array(
                        array(
                                'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_house_schema_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'House'   
                        ),
                    array(
                            'label'      => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_house_schema_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                    ),
                    array(
                            'label'      => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_house_schema_url_'.$schema_id,
                            'type'       => 'text',
                            'default'    => get_permalink()
                    ),
                    array(
                            'label'      => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_house_schema_image_'.$schema_id,
                            'type'       => 'media',                            
                    ),
                    array(
                            'label'      => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_house_schema_description_'.$schema_id,
                            'type'       => 'textarea',
                            'attributes' => array(
                                'placeholder' => 'Description'
                            ), 
                    ),
                     array(
                            'label'      => esc_html__( 'Pets Allowed', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_house_schema_pets_allowed_'.$schema_id,
                            'type'       => 'select',
                            'options' => array(
                                     'yes'       => 'Yes',
                                     'no'        => 'No'                                                                          
                            ) 
                    ),
                    array(
                            'label'      => esc_html__( 'Country', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_house_schema_country_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => esc_html__( 'Locality', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_house_schema_locality_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => esc_html__( 'Region', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_house_schema_region_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => esc_html__( 'Postal Code', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_house_schema_postalcode_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => esc_html__( 'Telephone', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_house_schema_telephone_'.$schema_id,
                            'type'       => 'text',                            
                    ),                      
                    array(
                            'label'      => esc_html__( 'Has Map', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_house_schema_hasmap_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => esc_html__( 'Floor Size', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_house_schema_floor_size_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                    array(
                            'label'      => esc_html__( 'Number of Rooms', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_house_schema_no_of_rooms_'.$schema_id,
                            'type'       => 'text',                            
                    )                                                 
                   );
                    break;   
                
                case 'SingleFamilyResidence':
                    
                    $meta_field = array(
                        array(
                                'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_sfr_schema_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'SingleFamilyResidence'   
                        ),
                    array(
                            'label'      => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_sfr_schema_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                    ),
                    array(
                            'label'      => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_sfr_schema_url_'.$schema_id,
                            'type'       => 'text',
                            'default'    => get_permalink()
                    ),
                    array(
                            'label'      => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_sfr_schema_image_'.$schema_id,
                            'type'       => 'media',                            
                    ),
                    array(
                            'label'      => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_sfr_schema_description_'.$schema_id,
                            'type'       => 'textarea',
                            'attributes' => array(
                                'placeholder' => 'Description'
                            ), 
                    ),
                    array(
                            'label'      => esc_html__( 'Number Of Rooms', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_sfr_schema_numberofrooms_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => '5'
                            ), 
                    ),    
                     array(
                            'label'      => esc_html__( 'Pets Allowed', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_sfr_schema_pets_allowed_'.$schema_id,
                            'type'       => 'select',
                            'options' => array(
                                     'yes'       => 'Yes',
                                     'no'        => 'No'                                                                          
                            ) 
                    ),
                    array(
                            'label'      => esc_html__( 'Country', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_sfr_schema_country_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => esc_html__( 'Locality', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_sfr_schema_locality_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => esc_html__( 'Region', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_sfr_schema_region_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => esc_html__( 'Postal Code', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_sfr_schema_postalcode_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => esc_html__( 'Telephone', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_sfr_schema_telephone_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => esc_html__( 'Has Map', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_sfr_schema_hasmap_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => esc_html__( 'Floor Size', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_sfr_schema_floor_size_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                    array(
                            'label'      => esc_html__( 'Number of Rooms', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_sfr_schema_no_of_rooms_'.$schema_id,
                            'type'       => 'text',                            
                    )    
                                              
                   );
                    break;
                
                case 'TouristAttraction':
                    
                    $meta_field = array(
                        array(
                                'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_ta_schema_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'TouristAttraction'   
                        ),
                    array(
                            'label'      => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_ta_schema_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                    ),
                    array(
                            'label'      => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_ta_schema_description_'.$schema_id,
                            'type'       => 'textarea',
                            'attributes' => array(
                                'placeholder' => 'Description'
                            ), 
                    ),
                    array(
                            'label'      => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_ta_schema_image_'.$schema_id,
                            'type'       => 'media',                            
                    ),    
                    array(
                            'label'      => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_ta_schema_url_'.$schema_id,
                            'type'       => 'text',
                            'default'    => get_permalink()
                    ),
                    array(
                            'label'      => esc_html__( 'Is Accessible For Free', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_ta_schema_is_acceesible_free_'.$schema_id,
                            'type'       => 'select',
                            'options' => array(
                                'true' => 'True',
                                'false' => 'False',
                            ),
                    ),
                    array(
                            'label'      => esc_html__( 'Address Locality', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_ta_schema_locality_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => esc_html__( 'Address Region', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_ta_schema_region_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                    array(
                            'label'      => esc_html__( 'Address Country', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_ta_schema_country_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => esc_html__( 'Address PostalCode', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_ta_schema_postal_code_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => esc_html__( 'Latitude', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_ta_schema_latitude_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => esc_html__( 'Longitude', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_ta_schema_longitude_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                                              
                   );
                    break;
                
                case 'TouristDestination':
                     
                    $meta_field = array(
                        array(
                                'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_td_schema_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'TouristDestination'   
                        ),
                    array(
                            'label'      => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_td_schema_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                    ),
                    array(
                            'label'      => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_td_schema_description_'.$schema_id,
                            'type'       => 'textarea',
                            'attributes' => array(
                                'placeholder' => 'Description'
                            ), 
                    ),
                    array(
                            'label'      => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_td_schema_image_'.$schema_id,
                            'type'       => 'media',                            
                    ),    
                    array(
                            'label'      => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_td_schema_url_'.$schema_id,
                            'type'       => 'text',
                            'default'    => get_permalink()
                    ),                                                                                
                    array(
                            'label'      => esc_html__( 'Address Locality', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_td_schema_locality_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => esc_html__( 'Address Region', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_td_schema_region_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                    array(
                            'label'      => esc_html__( 'Address Country', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_td_schema_country_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => esc_html__( 'Address PostalCode', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_td_schema_postal_code_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => esc_html__( 'Latitude', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_td_schema_latitude_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => esc_html__( 'Longitude', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_td_schema_longitude_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                                              
                   );
                    break;
                
                case 'LandmarksOrHistoricalBuildings':
                    
                    $meta_field = array(
                        array(
                                'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_lorh_schema_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'LandmarksOrHistoricalBuildings'   
                        ),
                    array(
                            'label'      => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_lorh_schema_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                    ),
                    array(
                            'label'      => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_lorh_schema_description_'.$schema_id,
                            'type'       => 'textarea',
                            'attributes' => array(
                                'placeholder' => 'Description'
                            ), 
                    ),
                    array(
                            'label'      => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_lorh_schema_image_'.$schema_id,
                            'type'       => 'media',                            
                    ),    
                    array(
                            'label'      => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_lorh_schema_url_'.$schema_id,
                            'type'       => 'text',
                            'default'    => get_permalink()
                    ), 
                    array(
                            'label'      => esc_html__( 'Has Map', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_lorh_schema_hasmap_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => esc_html__( 'Is Accessible For Free', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_lorh_schema_is_acceesible_free_'.$schema_id,
                            'type'       => 'select',
                            'options'    => array(
                                    'true'   => 'True',
                                    'false'  => 'False',
                            )
                    ),
                    array(
                            'label'      => esc_html__( 'Maximum Attendee Capacity', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_lorh_schema_maximum_a_capacity_'.$schema_id,
                            'type'       => 'number',                            
                    ),    
                    array(
                            'label'      => esc_html__( 'Address Locality', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_lorh_schema_locality_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => esc_html__( 'Address Region', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_lorh_schema_region_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                    array(
                            'label'      => esc_html__( 'Address Country', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_lorh_schema_country_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => esc_html__( 'Address PostalCode', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_lorh_schema_postal_code_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => esc_html__( 'Latitude', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_lorh_schema_latitude_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => esc_html__( 'Longitude', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_lorh_schema_longitude_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                                              
                   );
                    break;
                
                case 'HinduTemple':
                    
                    $meta_field = array(
                        array(
                                'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_hindutemple_schema_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'hindutemple'   
                        ),
                    array(
                            'label'      => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_hindutemple_schema_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                    ),
                    array(
                            'label'      => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_hindutemple_schema_description_'.$schema_id,
                            'type'       => 'textarea',
                            'attributes' => array(
                                'placeholder' => 'Description'
                            ), 
                    ),
                    array(
                            'label'      => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_hindutemple_schema_image_'.$schema_id,
                            'type'       => 'media',                            
                    ),    
                    array(
                            'label'      => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_hindutemple_schema_url_'.$schema_id,
                            'type'       => 'text',
                            'default'    => get_permalink()
                    ),  
                    array(
                            'label'      => esc_html__( 'Has Map', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_hindutemple_schema_hasmap_'.$schema_id,
                            'type'       => 'text',                            
                    ),                      
                    array(
                            'label'      => esc_html__( 'Is Accessible For Free', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_hindutemple_schema_is_accesible_free_'.$schema_id,
                            'type'       => 'select',
                            'options'    => array(
                                    'true'   => 'True',
                                    'false'  => 'False',
                            )
                    ),
                    array(
                            'label'      => esc_html__( 'Maximum Attendee Capacity', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_hindutemple_schema_maximum_a_capacity_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                    array(
                            'label'      => esc_html__( 'Address Locality', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_hindutemple_schema_locality_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => esc_html__( 'Address Region', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_hindutemple_schema_region_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                    array(
                            'label'      => esc_html__( 'Address Country', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_hindutemple_schema_country_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => esc_html__( 'Address PostalCode', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_hindutemple_schema_postal_code_'.$schema_id,
                            'type'       => 'text',                            
                    ), 
                    array(
                            'label'      => esc_html__( 'Latitude', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_hindutemple_schema_latitude_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => esc_html__( 'Longitude', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_hindutemple_schema_longitude_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                                              
                   );
                    break;

                    case 'BuddhistTemple':
                    
                        $meta_field = array(
                        array(
                                'label'      => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_buddhisttemple_schema_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'BuddhistTemple'   
                        ),
                        array(
                                'label'      => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_buddhisttemple_schema_name_'.$schema_id,
                                'type'       => 'text',
                                'attributes' => array(
                                    'placeholder' => 'Name'
                                ), 
                        ),
                        array(
                                'label'      => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_buddhisttemple_schema_description_'.$schema_id,
                                'type'       => 'textarea',
                                'attributes' => array(
                                    'placeholder' => 'Description'
                                ), 
                        ),
                        array(
                                'label'      => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_buddhisttemple_schema_image_'.$schema_id,
                                'type'       => 'media',                            
                        ),    
                        array(
                                'label'      => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_buddhisttemple_schema_url_'.$schema_id,
                                'type'       => 'text',
                                'default'    => get_permalink()
                        ),  
                        array(
                                'label'      => esc_html__( 'Has Map', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_buddhisttemple_schema_hasmap_'.$schema_id,
                                'type'       => 'text',                            
                        ),                      
                        array(
                                'label'      => esc_html__( 'Is Accessible For Free', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_buddhisttemple_schema_is_accesible_free_'.$schema_id,
                                'type'       => 'select',
                                'options'    => array(
                                        'true'   => 'True',
                                        'false'  => 'False',
                                )
                        ),
                        array(
                                'label'      => esc_html__( 'Maximum Attendee Capacity', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_buddhisttemple_schema_maximum_a_capacity_'.$schema_id,
                                'type'       => 'text',                            
                        ),    
                        array(
                                'label'      => esc_html__( 'Address Locality', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_buddhisttemple_schema_locality_'.$schema_id,
                                'type'       => 'text',                            
                        ),
                        array(
                                'label'      => esc_html__( 'Address Region', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_buddhisttemple_schema_region_'.$schema_id,
                                'type'       => 'text',                            
                        ),    
                        array(
                                'label'      => esc_html__( 'Address Country', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_buddhisttemple_schema_country_'.$schema_id,
                                'type'       => 'text',                            
                        ),
                        array(
                                'label'      => esc_html__( 'Address PostalCode', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_buddhisttemple_schema_postal_code_'.$schema_id,
                                'type'       => 'text',                            
                        ), 
                        array(
                                'label'      => esc_html__( 'Latitude', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_buddhisttemple_schema_latitude_'.$schema_id,
                                'type'       => 'text',                            
                        ),
                        array(
                                'label'      => esc_html__( 'Longitude', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_buddhisttemple_schema_longitude_'.$schema_id,
                                'type'       => 'text',                            
                        ),    
                                                  
                       );
                        break;    
                
                case 'Church':
                    
                    $meta_field = array(
                        array(
                                'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_church_schema_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'church'   
                        ),
                    array(
                            'label'      => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_church_schema_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                    ),
                    array(
                            'label'      => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_church_schema_description_'.$schema_id,
                            'type'       => 'textarea',
                            'attributes' => array(
                                'placeholder' => 'Description'
                            ), 
                    ),
                    array(
                            'label'      => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_church_schema_image_'.$schema_id,
                            'type'       => 'media',                            
                    ),    
                    array(
                            'label'      => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_church_schema_url_'.$schema_id,
                            'type'       => 'text',
                            'default'    => get_permalink()
                    ),  
                    array(
                            'label'      => esc_html__( 'Has Map', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_church_schema_hasmap_'.$schema_id,
                            'type'       => 'text',                            
                    ),                      
                    array(
                            'label'      => esc_html__( 'Is Accessible For Free', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_church_schema_is_accesible_free_'.$schema_id,
                            'type'       => 'select',
                            'options'    => array(
                                    'true'   => 'True',
                                    'false'  => 'False',
                            )
                    ),
                    array(
                            'label'      => esc_html__( 'Maximum Attendee Capacity', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_church_schema_maximum_a_capacity_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                    array(
                            'label'      => esc_html__( 'Address Locality', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_church_schema_locality_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => esc_html__( 'Address Region', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_church_schema_region_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                    array(
                            'label'      => esc_html__( 'Address Country', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_church_schema_country_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => esc_html__( 'Address PostalCode', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_church_schema_postal_code_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => esc_html__( 'Latitude', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_church_schema_latitude_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => esc_html__( 'Longitude', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_church_schema_longitude_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                                              
                   );
                    break;
                
                case 'Mosque':
                    
                    $meta_field = array(
                        array(
                                'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_mosque_schema_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'Mosque'   
                        ),
                    array(
                            'label'      => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_mosque_schema_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                    ),
                    array(
                            'label'      => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_mosque_schema_description_'.$schema_id,
                            'type'       => 'textarea',
                            'attributes' => array(
                                'placeholder' => 'Description'
                            ), 
                    ),
                    array(
                            'label'      => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_mosque_schema_image_'.$schema_id,
                            'type'       => 'media',                            
                    ),    
                    array(
                            'label'      => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_mosque_schema_url_'.$schema_id,
                            'type'       => 'text',
                            'default'    => get_permalink()
                    ),
                    array(
                            'label'      => esc_html__( 'Has Map', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_mosque_schema_hasmap_'.$schema_id,
                            'type'       => 'text',                            
                    ),                      
                    array(
                            'label'      => esc_html__( 'Is Accessible For Free', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_mosque_schema_is_accesible_free_'.$schema_id,
                            'type'       => 'select',
                            'options'    => array(
                                    'true'   => 'True',
                                    'false'  => 'False',
                            )
                    ),
                    array(
                            'label'      => esc_html__( 'Maximum Attendee Capacity', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_mosque_schema_maximum_a_capacity_'.$schema_id,
                            'type'       => 'text',                            
                    ),  
                    array(
                            'label'      => esc_html__( 'Address Locality', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_mosque_schema_locality_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                    array(
                            'label'      => esc_html__( 'Address Region', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_mosque_schema_region_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                    array(
                            'label'      => esc_html__( 'Address Country', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_mosque_schema_country_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => esc_html__( 'Address PostalCode', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_mosque_schema_postal_code_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => esc_html__( 'Latitude', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_mosque_schema_latitude_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => esc_html__( 'Longitude', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_mosque_schema_longitude_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                                              
                   );
                    break;
                
                case 'JobPosting':
                    
                    $meta_field = array(
                        array(
                                'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_jobposting_schema_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'JobPosting'   
                        ),
                    array(
                            'label'      => esc_html__( 'Title', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_jobposting_schema_title_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Title'
                            ), 
                    ),
                    array(
                            'label'      => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_jobposting_schema_description_'.$schema_id,
                            'type'       => 'textarea',
                            'attributes' => array(
                                'placeholder' => 'Description'
                            ), 
                    ),
                    array(
                            'label'      => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_jobposting_schema_url_'.$schema_id,
                            'type'       => 'text',
                            'default'    => get_permalink()
                    ),     
                    array(
                            'label'      => esc_html__( 'Date Posted', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_jobposting_schema_dateposted_'.$schema_id,
                            'type'       => 'text',                             
                    ),
                    array(
                        'label'      => esc_html__( 'Direct Apply', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_jobposting_schema_direct_apply_'.$schema_id,
                        'type'       => 'text',
                        'default'    => true   
                    ),
                    array(
                            'label'      => esc_html__( 'Valid Through', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_jobposting_schema_validthrough_'.$schema_id,
                            'type'       => 'text',                             
                    ),
                    array(
                            'label'      => esc_html__( 'Employment Type', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_jobposting_schema_employment_type_'.$schema_id,
                            'type'       => 'multiselect', 
                            'options'    => array(
                                'FULL_TIME'  => 'FULL_TIME',
                                'PART_TIME'  => 'PART_TIME',
                                'CONTRACTOR' => 'CONTRACTOR',                                
                                'TEMPORARY'  => 'TEMPORARY',
                                'INTERN'     => 'INTERN',
                                'VOLUNTEER'  => 'VOLUNTEER',
                                'PER_DIEM'   => 'PER_DIEM',
                                'OTHER'      => 'OTHER',
                            )
                        ), 
                    array(
                                'label'      => esc_html__( 'Industry', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_jobposting_schema_industry_'.$schema_id,
                                'type'       => 'text',                             
                        ),
                    array(
                                'label'      => esc_html__( 'Occupational Category', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_jobposting_schema_occupational_category_'.$schema_id,
                                'type'       => 'text',                             
                        ),
                     array(
                                'label'      => esc_html__( 'Job Immediate Start', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_jobposting_schema_jobimmediatestart_'.$schema_id,
                                'type'       => 'text',                             
                       ),
                    array(
                            'label'      => esc_html__( 'Hiring Organization Name', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_jobposting_schema_ho_name_'.$schema_id,
                            'type'       => 'text',                             
                    ),
                    array(
                            'label'      => esc_html__( 'Hiring Organization URL', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_jobposting_schema_ho_url_'.$schema_id,
                            'type'       => 'text',                             
                    ),
                    array(
                            'label'      => esc_html__( 'Hiring Organization Logo', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_jobposting_schema_ho_logo_'.$schema_id,
                            'type'       => 'media',                             
                    ),
                    array(
                        'label'      => esc_html__( 'Applicants can apply from ( Country ) ', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_jobposting_schema_applicant_location_requirements_'.$schema_id,
                        'type'       => 'text',                             
                    ),
                    array(
                        'label'      => esc_html__( 'Incentive Compensation', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_jobposting_schema_incentive_compensation_'.$schema_id,
                        'type'       => 'text',                             
                    ),
                    array(
                        'label'      => esc_html__( 'Job Benefits', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_jobposting_schema_job_benefits_'.$schema_id,
                        'type'       => 'text',                             
                    ),
                    array(
                        'label'      => esc_html__( 'Job Location Type', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_jobposting_schema_job_location_type_'.$schema_id,
                        'type'       => 'text',                             
                    ),
                    array(
                            'label'      => esc_html__( 'Job Location Street Address', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_jobposting_schema_street_address_'.$schema_id,
                            'type'       => 'text',                             
                    ),
                    array(
                            'label'      => esc_html__( 'Job Location Locality', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_jobposting_schema_locality_'.$schema_id,
                            'type'       => 'text',                             
                    ),
                    array(
                            'label'      => esc_html__( 'Job Location Region', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_jobposting_schema_region_'.$schema_id,
                            'type'       => 'text',                             
                    ),
                    array(
                            'label'      => esc_html__( 'Job Location Postal Code', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_jobposting_schema_postalcode_'.$schema_id,
                            'type'       => 'text',                             
                    ),
                    array(
                            'label'      => esc_html__( 'Job Location Country', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_jobposting_schema_country_'.$schema_id,
                            'type'       => 'text',                             
                    ),
                    array(
                                'label'      => esc_html__( 'Job Location GeoCoordinates Latitude','schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_jobposting_schema_latitude_'.$schema_id,
                                'type'       => 'text',
                                'attributes' => array(
                                        'placeholder' => '17.412'
                                ), 
                     ),
                     array(
                                'label'      => esc_html__( 'Job Location GeoCoordinates Longitude', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_jobposting_schema_longitude_'.$schema_id,
                                'type'       => 'text',
                                'attributes' => array(
                                        'placeholder' => '78.433'
                                ),
                        ),
                    array(
                            'label'      => esc_html__( 'Base Salary Currency', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_jobposting_schema_bs_currency_'.$schema_id,
                            'type'       => 'text', 
                            'attributes' => array(
                                'placeholder' => 'USD'
                            )
                    ),
                    array(
                            'label'      => esc_html__( 'Base Salary Value', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_jobposting_schema_bs_value_'.$schema_id,
                            'type'       => 'text', 
                            'attributes' => array(
                                'placeholder' => '40.00'
                            )
                    ),
                    array(
                        'label'      => esc_html__( 'Base Salary Min Value', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_jobposting_schema_bs_min_value_'.$schema_id,
                        'type'       => 'text', 
                        'attributes' => array(
                            'placeholder' => '20.00'
                        )
                ),
                array(
                        'label'      => esc_html__( 'Base Salary Max Value', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_jobposting_schema_bs_max_value_'.$schema_id,
                        'type'       => 'text', 
                        'attributes' => array(
                            'placeholder' => '100.00'
                        )
                ),
                    array(
                            'label'      => esc_html__( 'Base Salary Unit Text', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_jobposting_schema_bs_unittext_'.$schema_id,
                            'type'       => 'text', 
                            'attributes' => array(
                                'placeholder' => 'Hour'
                            )
                    ), 
                        array(
                                'label'      => esc_html__( 'Estimated Salary Currency', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_jobposting_schema_es_currency_'.$schema_id,
                                'type'       => 'text', 
                                'attributes' => array(
                                'placeholder' => 'USD'
                                )
                        ),
                        array(
                                'label'      => esc_html__( 'Estimated Salary Value','schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_jobposting_schema_es_value_'.$schema_id,
                                'type'       => 'text', 
                                'attributes' => array(
                                'placeholder' => '40.00'
                                )
                        ),
                        array(
                                'label'      => esc_html__( 'Estimated Salary Min Value', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_jobposting_schema_es_min_value_'.$schema_id,
                                'type'       => 'text', 
                                'attributes' => array(
                                'placeholder' => '20.00'
                                )
                        ),
                        array(
                                'label'      => esc_html__( 'Estimated Salary Max Value', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_jobposting_schema_es_max_value_'.$schema_id,
                                'type'       => 'text', 
                                'attributes' => array(
                                'placeholder' => '100.00'
                                )
                        ),
                        array(
                                'label'      => esc_html__( 'Estimated Salary Unit Text', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_jobposting_schema_es_unittext_'.$schema_id,
                                'type'       => 'text', 
                                'attributes' => array(
                                'placeholder' => 'Hour'
                                )
                        ),
                        array(
                                'label'      => esc_html__( 'Education Requirements', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_jobposting_schema_edu_credential_'.$schema_id,
                                'type'       => 'text',
                                'attributes' => array(
                                'placeholder' => 'bachelor degree'
                                )
                        ),
                        array(
                                'label'      => esc_html__( 'Experience Requirements', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_jobposting_schema_exp_months_'.$schema_id,
                                'type'       => 'text',
                                'attributes' => array(
                                'placeholder' => '60'
                                )
                        )

                   );
                    break;
               
                case 'Trip':
                    
                    $meta_field = array( 
                        array(
                                'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_trip_schema_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'Trip'   
                        ),
                    array(
                            'label'      => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_trip_schema_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                    ),
                    array(
                            'label'      => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_trip_schema_description_'.$schema_id,
                            'type'       => 'textarea',
                            'attributes' => array(
                                'placeholder' => 'Description'
                            )
                    ),
                    array(
                            'label'      => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_trip_schema_url_'.$schema_id,
                            'type'       => 'text',
                            'default'    => get_permalink() 
                    ),
                    array(
                            'label'      => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_trip_schema_image_'.$schema_id,
                            'type'       => 'media'                            
                    )    
                        
                        
                   );

                    break;

                    case 'BoatTrip':
                    
                        $meta_field = array(
                        array(
                                'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_boat_trip_schema_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'BoatTrip'   
                        ),
                        array(
                                'label'      => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_boat_trip_schema_name_'.$schema_id,
                                'type'       => 'text',
                                'attributes' => array(
                                    'placeholder' => 'Name'
                                ), 
                        ),
                        array(
                                'label'      => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_boat_trip_schema_description_'.$schema_id,
                                'type'       => 'textarea',
                                'attributes' => array(
                                    'placeholder' => 'Description'
                                )
                        ),
                        array(
                                'label'      => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_boat_trip_schema_url_'.$schema_id,
                                'type'       => 'text',
                                'default'    => get_permalink() 
                        ),
                        array(
                                'label'      => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_boat_trip_schema_image_'.$schema_id,
                                'type'       => 'media'                            
                        ),
                        array(
                                'label'      => esc_html__( 'Arrival Time', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_boat_trip_schema_arrival_time_'.$schema_id,
                                'type'       => 'text'                            
                        ),
                        array(
                                'label'      => esc_html__( 'Departure Time', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_boat_trip_schema_departure_time_'.$schema_id,
                                'type'       => 'text'                            
                        ),
                        array(
                                'label'      => esc_html__( 'Arrival Boat Terminal', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_boat_trip_schema_arrival_boat_terminal_'.$schema_id,
                                'type'       => 'text'                            
                        ),
                        array(
                                'label'      => esc_html__( 'Departure Boat Terminal', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_boat_trip_schema_departure_boat_terminal_'.$schema_id,
                                'type'       => 'text'                            
                        )                                                        
                       );
    
                    break;

                    case 'FAQ':
                        $faq_post_meta_data = get_post_meta(get_the_ID());
                        $faq_post_meta_id = 'FAQ';
                        if ( isset( $faq_post_meta_data['saswp_faq_id_'.$schema_id]) ) {
                            $faq_post_meta_id = $faq_post_meta_data['saswp_faq_id_'.$schema_id][0];
                        }
                        $faq_post_meta_headline = get_the_title();
                        if ( isset( $faq_post_meta_data['saswp_faq_headline_'.$schema_id]) ) {
                            $faq_post_meta_headline = $faq_post_meta_data['saswp_faq_headline_'.$schema_id][0];
                        }
                        $faq_post_meta_tags = saswp_get_the_tags();
                        if ( isset( $faq_post_meta_data['saswp_faq_keywords_'.$schema_id]) ) {
                            $faq_post_meta_tags = $faq_post_meta_data['saswp_faq_keywords_'.$schema_id][0];
                        }
                        $faq_post_meta_atype = '';
                        if ( isset( $faq_post_meta_data['saswp_faq_author_type_'.$schema_id]) ) {
                            $faq_post_meta_atype = $faq_post_meta_data['saswp_faq_author_type_'.$schema_id][0];
                        }
                        $faq_post_meta_aname = is_object($current_user) ? $current_user->display_name : '';
                        if ( isset( $faq_post_meta_data['saswp_faq_author_name_'.$schema_id]) ) {
                            $faq_post_meta_aname = $faq_post_meta_data['saswp_faq_author_name_'.$schema_id][0];
                        }
                        $faq_post_meta_adesc = $author_desc;
                        if ( isset( $faq_post_meta_data['saswp_faq_author_description_'.$schema_id]) ) {
                            $faq_post_meta_adesc = $faq_post_meta_data['saswp_faq_author_description_'.$schema_id][0];
                        }
                        $faq_post_meta_aurl = $author_url;
                        if ( isset( $faq_post_meta_data['saswp_faq_author_url_'.$schema_id]) ) {
                            $faq_post_meta_aurl = $faq_post_meta_data['saswp_faq_author_url_'.$schema_id][0];
                        }
                        $faq_post_meta_aiurl = isset($author_details['url']) ? $author_details['url']: '';
                        if ( isset( $faq_post_meta_data['saswp_faq_author_image_'.$schema_id]) ) {
                            $faq_post_meta_aiurl = $faq_post_meta_data['saswp_faq_author_image_'.$schema_id][0];
                        }
                        $faq_post_meta_dcreated = get_the_date("Y-m-d");
                        if ( isset( $faq_post_meta_data['saswp_faq_date_created_'.$schema_id]) ) {
                            $faq_post_meta_dcreated = $faq_post_meta_data['saswp_faq_date_created_'.$schema_id][0];
                        }
                        $faq_post_meta_dpublished = get_the_date("Y-m-d");
                        if ( isset( $faq_post_meta_data['saswp_faq_date_published_'.$schema_id]) ) {
                            $faq_post_meta_dpublished = $faq_post_meta_data['saswp_faq_date_published_'.$schema_id][0];
                        }
                        $faq_post_meta_dmodified = get_the_modified_date("Y-m-d");
                        if ( isset( $faq_post_meta_data['saswp_faq_date_modified_'.$schema_id]) ) {
                            $faq_post_meta_dmodified = $faq_post_meta_data['saswp_faq_date_modified_'.$schema_id][0];
                        }
                        $faq_post_meta_about = '';
                        if ( isset( $faq_post_meta_data['saswp_faq_about_'.$schema_id]) ) {
                            $faq_post_meta_about = $faq_post_meta_data['saswp_faq_about_'.$schema_id][0];
                        }

                        $meta_field = array(
                        array(
                                'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_faq_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => $faq_post_meta_id   
                                ),
                        array(
                                'label'      => esc_html__( 'Headline', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_faq_headline_'.$schema_id,
                                'type'       => 'text',
                                'default'    => $faq_post_meta_headline                             
                        ),
                        array(
                                'label'      => esc_html__( 'Tags', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_faq_keywords_'.$schema_id,
                                'type'       => 'text',
                                'default'    => $faq_post_meta_tags                            
                        ),
                        array(
                                'label'   => esc_html__( 'Author', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_faq_author_global_mapping_'.$schema_id,
                                'type'    => 'global_mapping'
                        ),
                        array(
                                'label'      => esc_html__( 'Author Type', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_faq_author_type_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                        ''           => 'Select Author Type',
                                        'Person'           => 'Person',
                                        'Organization'     => 'Organization',                        
                                ),
                                'default' => $faq_post_meta_atype
                        ),
                        array(
                                'label'      => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_faq_author_name_'.$schema_id,
                                'type'       => 'text',
                                'default' => $faq_post_meta_aname                            
                        ),    
                        array(
                                'label'   => esc_html__( 'Author HonorificSuffix', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_faq_author_honorific_suffix_'.$schema_id,
                                'type'    => 'text',
                                'attributes' => array(
                                        'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                                 ),
                        ),
                        array(
                                'label'   => esc_html__( 'Author Description', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_faq_author_description_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => $faq_post_meta_adesc
                        ),
                        array(
                                'label'   => esc_html__( 'Author URL', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_faq_author_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => $faq_post_meta_aurl
                        ),
                        array(
                                'label' => esc_html__( 'Author Image URL', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_faq_author_image_'.$schema_id,
                                'type' => 'media',
                                'default' => $faq_post_meta_aiurl
                        ),
                        array(
                                'label'      => esc_html__( 'DateCreated', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_faq_date_created_'.$schema_id,
                                'type'       => 'text',
                                'default'    => $faq_post_meta_dcreated                            
                        ),
                        array(
                                'label'      => esc_html__( 'DatePublished', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_faq_date_published_'.$schema_id,
                                'type'       => 'text',
                                'default'    => $faq_post_meta_dpublished                            
                        ),
                        array(
                                'label'      => esc_html__( 'DateModified', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_faq_date_modified_'.$schema_id,
                                'type'       => 'text',
                                'default'    => $faq_post_meta_dmodified                            
                        ),
                        array(
                                'label'      => esc_html__( 'MainEntity (Questions & Answers) ', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_faq_main_entity_'.$schema_id,
                                'type'       => 'repeater'                                                     
                        ),
                        array(
                                'label'   => esc_html__( 'About', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_faq_about_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => $faq_post_meta_about,
                                'note'    => 'Note: If There are more than one About, Separate About list by comma ( , )',
                                'attributes' => array(
                                        'placeholder' => 'eg: Apple is March 21 Announcements'
                                ),
                        )                                                  
                       );                                                                 
                       
                        break;
                                
                case 'Person':
                    
                    $meta_field = array(
                    array(
                           'label'   => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                           'id'      => 'saswp_person_schema_id_'.$schema_id,
                           'type'    => 'text'                                
                    ),   
                    array(
                        'label'      => esc_html__( 'Honorific Prefix', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_honorific_prefix_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => esc_html__( 'Honorific Suffix', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_honorific_suffix_'.$schema_id,
                        'type'       => 'text',                            
                    ),     
                    array(
                            'label'      => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_person_schema_name_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => esc_html__( 'Alternate Name', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_person_schema_alternate_name_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                           'label'      => esc_html__( 'Additional Name', 'schema-and-structured-data-for-wp' ),
                           'id'         => 'saswp_person_schema_additional_name_'.$schema_id,
                           'type'       => 'text',                           
                    ),
                    array(
                        'label'      => esc_html__( 'Given Name', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_given_name_'.$schema_id,
                        'type'       => 'text',                           
                    ),
                    array(
                            'label'      => esc_html__( 'Family Name', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_person_schema_family_name_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                        'label'      => esc_html__( 'Spouse', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_spouse_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => esc_html__( 'Parent', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_parent_'.$schema_id,
                        'type'       => 'textarea',
                        'attributes' => array(
                                'placeholder' => 'Johannes Xoo, Amanda Xoo'
                         ),
                        'note' => 'Note: Separate it by comma ( , )' ,                            
                    ),
                    array(
                        'label'      => esc_html__( 'Sibling', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_sibling_'.$schema_id,
                        'type'       => 'textarea',
                        'attributes' => array(
                                'placeholder' => 'Dima Xoo, Amanda Xoo'
                         ),
                        'note' => 'Note: Separate it by comma ( , )' ,                            
                    ),
                    array(
                        'label'      => esc_html__( 'Colleague', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_colleague_'.$schema_id,
                        'type'       => 'textarea',
                        'attributes' => array(
                                'placeholder' => 'Bill Gates, Jeff Bezos'
                         ),
                        'note' => 'Note: Separate it by comma ( , )' ,                            
                    ),
                    array(
                            'label'      => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_person_schema_description_'.$schema_id,
                            'type'       => 'textarea',                           
                    ),    
                    array(
                            'label'      => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_person_schema_url_'.$schema_id,
                            'type'       => 'text',
                            'default'    => get_permalink()
                    ),
                    array(
                        'label'      => esc_html__( 'Main Entity Of Page', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_main_entity_of_page_'.$schema_id,
                        'type'       => 'text',
                        'default'    => get_permalink()
                    ),    
                    array(
                            'label'      => esc_html__( 'Street Address', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_person_schema_street_address_'.$schema_id,
                            'type'       => 'text',
                           
                    ),
                    array(
                            'label'      => esc_html__( 'Locality', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_person_schema_locality_'.$schema_id,
                            'type'       => 'text',
                           
                    ),
                    array(
                            'label'      => esc_html__( 'Region', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_person_schema_region_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => esc_html__( 'Postal Code', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_person_schema_postal_code_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => esc_html__( 'Country', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_person_schema_country_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => esc_html__( 'Email', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_person_schema_email_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => esc_html__( 'Telephone', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_person_schema_telephone_'.$schema_id,
                            'type'       => 'text',                           
                    ),    
                    array(
                            'label'      => esc_html__( 'Gender', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_person_schema_gender_'.$schema_id,
                            'type'       => 'select',
                            'options'    => array(
                                    'Male'   => 'Male',
                                    'Female' => 'Female',    
                            )
                    ),
                        array(
                            'label'      => esc_html__( 'Date Of Birth', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_person_schema_date_of_birth_'.$schema_id,
                            'type'       => 'text',                            
                        ),
                        array(
                            'label'      => esc_html__( 'Street Address', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_person_schema_b_street_address_'.$schema_id,
                            'type'       => 'text',                       
                        ),
                        array(
                                'label'      => esc_html__( 'Birth Place Locality', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_person_schema_b_locality_'.$schema_id,
                                'type'       => 'text',
                        
                        ),
                        array(
                                'label'      => esc_html__( 'Birth Place Region', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_person_schema_b_region_'.$schema_id,
                                'type'       => 'text',                           
                        ),
                        array(
                                'label'      => esc_html__( 'Birth Place Postal Code', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_person_schema_b_postal_code_'.$schema_id,
                                'type'       => 'text',                           
                        ),
                        array(
                                'label'      => esc_html__( 'Birth Place Country', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_person_schema_b_country_'.$schema_id,
                                'type'       => 'text',                           
                        ),
                    array(
                           'label'      => esc_html__( 'Date of death', 'schema-and-structured-data-for-wp' ),
                           'id'         => 'saswp_person_schema_date_of_death_'.$schema_id,
                           'type'       => 'text',                            
                    ),
                    array(
                            'label'      => esc_html__( 'Member Of', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_person_schema_member_of_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => esc_html__( 'Nationality', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_person_schema_nationality_'.$schema_id,
                            'type'       => 'text',                            
                    ),                    
                    array(
                            'label'      => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_person_schema_image_'.$schema_id,
                            'type'       => 'media',                            
                    ),
                    array(
                            'label'      => esc_html__( 'Job Title', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_person_schema_job_title_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => esc_html__( 'Company ( Works For )', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_person_schema_company_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => esc_html__( 'Website', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_person_schema_website_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                        'label'      => esc_html__( 'Facebook', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_facebook_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => esc_html__( 'Youtube', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_youtube_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => esc_html__( 'Twitter', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_twitter_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => esc_html__( 'LinkedIn', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_linkedin_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => esc_html__( 'Instagram', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_instagram_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => esc_html__( 'Snapchat', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_snapchat_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => esc_html__( 'Thread', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_threads_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => esc_html__( 'Mastodon', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_mastodon_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => esc_html__( 'Vibehut', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_vibehut_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => esc_html__( 'Sponsor', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_sponsor_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => esc_html__( 'Affiliation', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_affiliation_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => esc_html__( 'Alumni Of', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_alumniof_'.$schema_id,
                        'type'       => 'text',                            
                    ), 
                    array(
                        'label'      => esc_html__( 'Award', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_award_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => esc_html__( 'Seeks', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_seeks_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => esc_html__( 'Knows', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_knows_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => esc_html__( 'Owns', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_owns_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => esc_html__( 'Brand', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_brand_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => esc_html__( 'Qualifications', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_qualifications_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => esc_html__( 'Occupation Name', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_occupation_name_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => esc_html__( 'Occupation Description', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_occupation_description_'.$schema_id,
                        'type'       => 'textarea',                            
                    ),
                    array(
                        'label'      => esc_html__( 'Estimated Salary', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_estimated_salary_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => esc_html__( 'Salary Currency', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_salary_currency_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => esc_html__( 'Salary Duration', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_salary_duration_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => esc_html__( 'Salary Median','schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_salary_median_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => esc_html__( 'Salary Percentile10', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_salary_percentile10_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => esc_html__( 'Salary Percentile25', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_salary_percentile25_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => esc_html__( 'Salary Percentile75', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_salary_percentile75_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => esc_html__( 'Salary Percentile90', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_salary_percentile90_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => esc_html__( 'Salary Last Reviewed', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_salary_last_reviewed_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => esc_html__( 'Occupation City', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_occupation_city_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => esc_html__( 'performerIn Name', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_performerin_name_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => esc_html__( 'performerIn Location Name', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_performerin_location_name_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => esc_html__( 'performerIn Location Locality', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_performerin_location_locality_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => esc_html__( 'performerIn Location Postal Code', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_performerin_location_postal_code_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => esc_html__( 'performerIn Location Street Address', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_performerin_location_street_address_'.$schema_id,
                        'type'       => 'text',                            
                    ),

                    array(
                        'label'      => esc_html__( 'performerIn Offers Name', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_performerin_offers_name_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => esc_html__( 'performerIn Offers Availability','schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_performerin_offers_availability_'.$schema_id,
                        'type'       => 'select',
                        'options' => array(
                                ''                  => 'Select',
                                'InStock'           => 'In Stock',
                                'OutOfStock'        => 'Out Of Stock',
                                'Discontinued'      => 'Discontinued',
                                'PreOrder'          => 'Pre Order', 
                       )                             
                    ),
                    array(
                        'label'      => esc_html__( 'performerIn Offers Price', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_performerin_offers_price_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => esc_html__( 'performerIn Offers Currency', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_performerin_offers_currency_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => esc_html__( 'performerIn Offers Valid From', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_performerin_offers_valid_from_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => esc_html__( 'performerIn Offers URL', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_performerin_offers_url_'.$schema_id,
                        'type'       => 'text',                            
                    ),

                    array(
                        'label'      => esc_html__( 'performerIn Start Date', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_performerin_start_date_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => esc_html__( 'performerIn End Date', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_performerin_end_date_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => esc_html__( 'performerIn Description', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_performerin_description_'.$schema_id,
                        'type'       => 'textarea',                            
                    ),
                    array(
                        'label'      => esc_html__( 'performerIn Image', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_performerin_image_'.$schema_id,
                        'type'       => 'media',                            
                    ),
                    array(
                        'label'      => esc_html__( 'performerIn Performer', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_person_schema_performerin_performer_'.$schema_id,
                        'type'       => 'textarea',
                        'attributes' => array(
                                'placeholder' => 'Bill Gates, Jeff Bezos'
                         ),
                        'note' => 'Note: Separate it by comma ( , )' ,                             
                    )                    
                   );
                    break;

                    case 'Car':

                        $meta_field = array(
                                array(
                                        'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_car_schema_id_'.$schema_id,
                                        'type'       => 'text',
                                        'default'    => 'Car'   
                                ),
                                array(
                                        'label'      => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_car_schema_name_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_car_schema_description_'.$schema_id,
                                        'type'       => 'textarea',                           
                                ),
                                array(
                                        'label'      => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_car_schema_url_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => esc_html__( 'Model', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_car_schema_model_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_car_schema_image_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => esc_html__( 'Body Type', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_car_schema_body_type_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => esc_html__( 'Fuel Type', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_car_schema_fuel_type_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => esc_html__( 'Fuel Efficiency', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_car_schema_fuel_efficiency_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => esc_html__( 'Seating Capacity','schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_car_schema_seating_capacity_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => esc_html__( 'Number Of Doors', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_car_schema_number_of_doors_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => esc_html__( 'Weight', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_car_schema_weight_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => esc_html__( 'Width', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_car_schema_width_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => esc_html__( 'Height', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_car_schema_height_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => esc_html__( 'SKU', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_car_schema_sku_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => esc_html__( 'MPN', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_car_schema_mpn_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => esc_html__( 'Brank', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_car_schema_brand_name'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => esc_html__( 'Item Condition', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_car_schema_condition_'.$schema_id,
                                        'type'       => 'select',
                                        'options'    => array(
                                            'NewCondition'      => 'NewCondition',
                                            'UsedCondition'     => 'UsedCondition',                        
                                        ),                           
                                ),
                                array(
                                        'label'      => esc_html__( 'Model Year', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_car_schema_model_date_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => esc_html__( 'Manufacturer', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_car_schema_manufacturer_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                   array(
                                        'label'   => esc_html__( 'Price', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_car_schema_price_'.$schema_id,
                                        'type'    => 'text',                                        
                                   ),
                                    array(
                                        'label'   => esc_html__( 'High Price', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_car_schema_high_price_'.$schema_id,
                                        'type'    => 'text'                                            
                                    ),
                                    array(
                                        'label'   => esc_html__( 'Low Price', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_car_schema_low_price_'.$schema_id,
                                        'type'    => 'text'                                            
                                    ),
                                    array(
                                        'label'   => esc_html__( 'Offer Count', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_car_schema_offer_count_'.$schema_id,
                                        'type'    => 'text'
                                    ),
                                    array(
                                        'label'   => esc_html__( 'Offer Count', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_car_schema_priceValidUntil_'.$schema_id,
                                        'type'    => 'text'                                        
                                   ),
                                    array(
                                        'label'   => esc_html__( 'Offer Count', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_car_schema_currency_'.$schema_id,
                                        'type'    => 'text'                                        
                                   ),
                                array(
                                        'label'      => esc_html__( 'Offer Count', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_car_schema_availability_'.$schema_id,
                                        'type'       => 'select',
                                        'options'    => array(
                                            'InStock'           => 'InStock',
                                            'OutOfStock'        => 'OutOfStock',                        
                                        ),                           
                                ),
                                array(
                                        'label' => esc_html__( 'Aggregate Rating', 'schema-and-structured-data-for-wp' ),
                                        'id'    => 'saswp_car_schema_enable_rating_'.$schema_id,
                                        'type'  => 'checkbox',                            
                                ),
                                array(
                                        'label'   => esc_html__( 'Rating', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_car_schema_rating_value_'.$schema_id,
                                        'type'    => 'text',                                        
                                ),
                                array(
                                        'label'   => esc_html__( 'Rating Count', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_car_schema_rating_count_'.$schema_id,
                                        'type'    => 'text',                            
                                )                                    
                               );

                    break;

                    case 'Vehicle':
 
                        $meta_field = array(
                                array(
                                        'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_vehicle_schema_id_'.$schema_id,
                                        'type'       => 'text',
                                        'default'    => 'Vehicle'   
                                ),
                                array(
                                        'label'      => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_vehicle_schema_name_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_vehicle_schema_description_'.$schema_id,
                                        'type'       => 'textarea',                           
                                ),
                                array(
                                        'label'      => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_vehicle_schema_url_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => esc_html__( 'Model', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_vehicle_schema_model_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_vehicle_schema_image_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => esc_html__( 'Body Type', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_vehicle_schema_body_type_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => esc_html__( 'Fuel Type', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_vehicle_schema_fuel_type_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => esc_html__( 'Fuel Efficiency', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_vehicle_schema_fuel_efficiency_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => esc_html__( 'Seating Capacity', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_vehicle_schema_seating_capacity_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => esc_html__( 'Number of Doors', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_vehicle_schema_number_of_doors_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => esc_html__( 'Weight', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_vehicle_schema_weight_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => esc_html__( 'Width', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_vehicle_schema_width_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => esc_html__( 'Height', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_vehicle_schema_height_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => esc_html__( 'SKU', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_vehicle_schema_sku_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => esc_html__( 'MPN', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_vehicle_schema_mpn_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => esc_html__( 'Brand', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_vehicle_schema_brand_name'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => esc_html__( 'Manufacturer', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_vehicle_schema_manufacturer_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => esc_html__( 'Identification Number', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_vehicle_schema_identification_no_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => esc_html__( 'Color', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_vehicle_schema_color_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => esc_html__( 'Interior Type', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_vehicle_schema_interior_type_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => esc_html__( 'Interior Color', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_vehicle_schema_interior_color_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => esc_html__( 'Transmission', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_vehicle_schema_transmission_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => esc_html__( 'Vehicle Configuration', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_vehicle_schema_config_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => esc_html__( 'Drive Wheel Configuration', 'schema-and-structured-data-for-wp' ),
                                        'id'         => 'saswp_vehicle_schema_wheel_config_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                               array(
                                    'label'   => esc_html__( 'Price', 'schema-and-structured-data-for-wp' ),
                                    'id'      => 'saswp_vehicle_schema_price_'.$schema_id,
                                    'type'    => 'text',                                        
                               ),
                                array(
                                    'label'   => esc_html__( 'High Price', 'schema-and-structured-data-for-wp' ),
                                    'id'      => 'saswp_vehicle_schema_high_price_'.$schema_id,
                                    'type'    => 'text'                                            
                                ),
                                array(
                                    'label'   => esc_html__( 'Low Price', 'schema-and-structured-data-for-wp' ),
                                    'id'      => 'saswp_vehicle_schema_low_price_'.$schema_id,
                                    'type'    => 'text'                                            
                                ),
                                array(
                                    'label'   => esc_html__( 'Offer Count', 'schema-and-structured-data-for-wp' ),
                                    'id'      => 'saswp_vehicle_schema_offer_count_'.$schema_id,
                                    'type'    => 'text'
                                ),
                                array(
                                    'label'   => esc_html__( 'Price Valid Until', 'schema-and-structured-data-for-wp' ),
                                    'id'      => 'saswp_vehicle_schema_priceValidUntil_'.$schema_id,
                                    'type'    => 'text'                                        
                               ),
                                array(
                                    'label'   => esc_html__( 'Currency', 'schema-and-structured-data-for-wp' ),
                                    'id'      => 'saswp_vehicle_schema_currency_'.$schema_id,
                                    'type'    => 'text'                                        
                               ),
                                array(
                                        'label' => esc_html__( 'Aggregate Rating', 'schema-and-structured-data-for-wp' ),
                                        'id'    => 'saswp_vehicle_schema_enable_rating_'.$schema_id,
                                        'type'  => 'checkbox',                            
                                ),
                                array(
                                        'label'   => esc_html__( 'Rating', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_vehicle_schema_rating_value_'.$schema_id,
                                        'type'    => 'text',                                        
                                ),
                                array(
                                        'label'   => esc_html__( 'Rating Count', 'schema-and-structured-data-for-wp' ),
                                        'id'      => 'saswp_vehicle_schema_rating_count_'.$schema_id,
                                        'type'    => 'text',                            
                                )                                    
                               );

                    break;
                    
                    case 'CreativeWorkSeries':
                    
                        $meta_field = array(
                        array(
                               'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                               'id'         => 'saswp_cws_schema_id_'.$schema_id,
                               'type'       => 'text',
                               'default'    => 'CreativeWorkSeries'   
                        ),
                        array(
                                'label'      => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_cws_schema_name_'.$schema_id,
                                'type'       => 'text',                           
                        ),
                        array(
                                'label'      => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_cws_schema_url_'.$schema_id,
                                'type'       => 'text',                           
                        ),
                        array(
                                'label'      => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_cws_schema_description_'.$schema_id,
                                'type'       => 'textarea',                           
                        ),
                        array(
                                'label'      => esc_html__( 'Keywords', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_cws_schema_keywords_'.$schema_id,
                                'type'       => 'text',                           
                        ),
                        array(
                                'label'      => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_cws_schema_image_'.$schema_id,
                                'type'       => 'media',                           
                        ),
                        array(
                                'label'      => esc_html__( 'Start Date', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_cws_schema_start_date_'.$schema_id,
                                'type'       => 'text',                           
                        ),
                        array(
                                'label'      => esc_html__( 'End Date', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_cws_schema_end_date_'.$schema_id,
                                'type'       => 'text',                           
                        ),
                        array(
                                'label'      => esc_html__( 'Date Published', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_cws_schema_date_published_'.$schema_id,
                                'type'       => 'text',
                                'default'    => get_the_date("Y-m-d")
                        ), 
                        array(
                                'label'      => esc_html__( 'Date Modified', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_cws_schema_date_modified_'.$schema_id,
                                'type'       => 'text',
                                'default'    => get_the_modified_date("Y-m-d")
                        ), 
                        array(
                                'label'      => esc_html__( 'In Language', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_cws_schema_inlanguage_'.$schema_id,
                                'type'       => 'text',       
                                'attributes' => array(
                                        'placeholder' => 'English'
                                ),                    
                        ),    
                        array(
                                'label'      => esc_html__( 'Author Type', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_cws_schema_author_type_'.$schema_id,
                                'type'       => 'select',
                                'options'    => array(
                                        'Person'           => 'Person',
                                        'Organization'     => 'Organization',                        
                        )
                        ),
                        array(
                                'label'      => esc_html__( 'Author Name', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_cws_schema_author_name_'.$schema_id,
                                'type'       => 'text',
                                'default'    => is_object($current_user) ? $current_user->display_name : ''
                        ),
                        array(
                                'label'      => esc_html__( 'Author Description', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_cws_schema_author_description_'.$schema_id,
                                'type'       => 'textarea',
                                'default'    => $author_desc
                        ),
                        array(
                                'label'      => esc_html__( 'Author URL', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_cws_schema_author_url_'.$schema_id,
                                'type'       => 'text',
                                'default'    => $author_url
                        ),    
                        array(
                                'label'      => esc_html__( 'Organization Name', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_cws_schema_organization_name_'.$schema_id,
                                'type'       => 'text',
                                'default'    => saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                       ),
                         array(
                                'label'      => esc_html__( 'Organization Logo', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_cws_schema_organization_logo_'.$schema_id,
                                'type'       => 'media',
                                'default'    => isset($sd_data['sd_logo']) ? $sd_data['sd_logo']['url'] : ''
                        )    
                       );
                        break;

                case 'DataFeed':
                    
                    $meta_field = array(
                  
                    array(
                            'label'      => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_data_feed_schema_name_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_data_feed_schema_description_'.$schema_id,
                            'type'       => 'textarea',                           
                    ),
                    array(
                            'label'      => esc_html__( 'DateModified', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_data_feed_schema_date_modified_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => esc_html__( 'License', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_data_feed_schema_license_'.$schema_id,
                            'type'       => 'text',                           
                    )    
                   );
                    break;
                
                case 'MusicPlaylist':
                    
                    $meta_field = array(
                   array(
                           'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                           'id'         => 'saswp_music_playlist_id_'.$schema_id,
                           'type'       => 'text',
                           'default'    => 'MusicPlaylist'   
                        ),
                    array(
                            'label'      => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_music_playlist_name_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_music_playlist_description_'.$schema_id,
                            'type'       => 'textarea',                           
                    ), 
                    array(
                            'label'      => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_music_playlist_url_'.$schema_id,
                            'type'       => 'text',                           
                    )                            
                   );
                    break;
                
                case 'MusicAlbum':
                    
                    $meta_field = array(
                        array(
                                'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_music_album_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'MusicPlaylist'   
                             ),
                    array(
                            'label'      => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_music_album_name_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_music_album_description_'.$schema_id,
                            'type'       => 'textarea',                           
                    ),
                    array(
                            'label'      => esc_html__( 'Genre', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_music_album_genre_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_music_album_image_'.$schema_id,
                            'type'       => 'media',                           
                    ),
                    array(
                            'label'      => esc_html__( 'Artist', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_music_album_artist_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_music_album_url_'.$schema_id,
                            'type'       => 'text',                           
                    )    
                        
                   );
                    break;
                
                case 'Book':
                    
                    $meta_field = array(
                   array(
                            'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_book_id_'.$schema_id,
                            'type'       => 'text',
                            'default'    => 'Book'   
                        ),
                    array(
                            'label'      => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_book_name_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_book_description_'.$schema_id,
                            'type'       => 'textarea',                           
                    ),
                    array(
                            'label'      => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_book_url_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_book_image_'.$schema_id,
                            'type'       => 'media',                           
                    ),
                    array(
                        'label'      => esc_html__( 'Author Type', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_book_author_type_'.$schema_id,
                        'type'       => 'select',
                        'options'    => array(
                                'Person'           => 'Person',
                                'Organization'     => 'Organization',                        
                       )           
                    ),
                    array(
                            'label'      => esc_html__( 'Author', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_book_author_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => esc_html__( 'Author Profile URL', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_book_author_url_'.$schema_id,
                            'type'       => 'text',                           
                    ),    
                    array(
                            'label'      => esc_html__( 'ISBN', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_book_isbn_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => esc_html__( 'Number Of Page', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_book_no_of_page_'.$schema_id,
                            'type'       => 'text',                           
                    ),    
                    array(
                        'label'      => esc_html__( 'Book Format', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_book_format_'.$schema_id,
                        'type'       => 'select',                           
                        'options'    => array(
                                     'AudiobookFormat'   => 'AudiobookFormat',
                                     'EBook'             => 'EBook',
                                     'GraphicNovel'      => 'GraphicNovel',
                                     'Hardcover'         => 'Hardcover',
                                     'Paperback'         => 'Paperback' 
                        )                         
                    ),    
                    array(
                        'label'      => esc_html__( 'In Language', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_book_inlanguage_'.$schema_id,
                        'type'       => 'text',       
                        'attributes' => array(
                                'placeholder' => 'English'
                        ),                    
                    ),    
                    array(
                            'label'      => esc_html__( 'Publisher', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_book_publisher_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => esc_html__( 'Published Date', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_book_date_published_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'   => esc_html__( 'Availability', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_book_availability_'.$schema_id,
                            'type'    => 'select',                           
                            'options' => array(
                                     'InStock'           => 'In Stock',
                                     'OutOfStock'        => 'Out Of Stock',
                                     'Discontinued'      => 'Discontinued',
                                     'PreOrder'          => 'Pre Order', 
                            ) 
                       ), 
                    array(
                            'label'      => esc_html__( 'Price', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_book_price_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => esc_html__( 'Price Currency', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_book_price_currency_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label' => esc_html__( 'Aggregate Rating', 'schema-and-structured-data-for-wp' ),
                            'id'    => 'saswp_book_enable_rating_'.$schema_id,
                            'type'  => 'checkbox',                            
                    ),
                    array(
                            'label'   => esc_html__( 'Rating', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_book_rating_value_'.$schema_id,
                            'type'    => 'text',
                            
                    ),
                    array(
                            'label'   => esc_html__( 'Rating Count', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_book_rating_count_'.$schema_id,
                            'type'    => 'text',                            
                    ),
                    array(
                            'label'   => esc_html__( 'Award', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_book_award_'.$schema_id,
                            'type'    => 'text',                            
                    ),                                                                            
                   );
                    break;
                
                case 'MusicComposition':
                    
                    $meta_field = array(
                        array(
                                'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_music_composition_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'MusicComposition'   
                                ),
                    array(
                            'label'      => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_music_composition_name_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_music_composition_description_'.$schema_id,
                            'type'       => 'textarea',                           
                    ),
                    array(
                            'label'      => esc_html__( 'Lyrics', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_music_composition_lyrics_'.$schema_id,
                            'type'       => 'textarea',                           
                    ),    
                    array(
                            'label'      => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_music_composition_url_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => esc_html__( 'ISWC Code', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_music_composition_iswccode_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_music_composition_image_'.$schema_id,
                            'type'       => 'media',                           
                    ),
                    array(
                            'label'      => esc_html__( 'inLanguage', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_music_composition_inlanguage_'.$schema_id,
                            'type'       => 'text',                           
                    ),                         
                    array(
                            'label'      => esc_html__( 'Publisher', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_music_composition_publisher_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                     array(
                            'label'     => esc_html__( 'Date Published', 'schema-and-structured-data-for-wp' ),
                            'id'        => 'saswp_music_composition_date_published_'.$schema_id,
                            'type'      => 'text',
                            'default'   => get_the_date("Y-m-d")
                    ),    
                   );
                    break;
                
                case 'Organization':
                    
                    $meta_field = array(    
                        array(
                                'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_organization_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'Organization'   
                        ),                    
                    array(
                            'label'      => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_organization_name_'.$schema_id,
                            'type'       => 'text',                           
                        ),
                        array(
                            'label'      => esc_html__( 'Legal Name', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_organization_legal_name_'.$schema_id,
                            'type'       => 'text',                           
                        ),
                    array(
                            'label'      => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_organization_description_'.$schema_id,
                            'type'       => 'textarea',                           
                        ),    
                    array(
                            'label'      => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_organization_url_'.$schema_id,
                            'type'       => 'text',                           
                        ), 
                    array(
                           'label'      => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                           'id'         => 'saswp_organization_image_'.$schema_id,
                           'type'       => 'media',                           
                        ),
                    array(
                            'label'      => esc_html__( 'Logo', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_organization_logo_'.$schema_id,
                            'type'       => 'media',                           
                        ), 
                    array(
                            'label'      => esc_html__( 'Street Address', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_organization_street_address_'.$schema_id,
                            'type'       => 'text',                           
                        ),
                        array(
                            'label'      => esc_html__( 'City', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_organization_city_'.$schema_id,
                            'type'       => 'text',                           
                        ),
                        array(
                            'label'      => esc_html__( 'State', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_organization_state_'.$schema_id,
                            'type'       => 'text',                           
                        ),
                        array(
                            'label'      => esc_html__( 'Currency', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_organization_country_'.$schema_id,
                            'type'       => 'text',                           
                        ),
                        array(
                            'label'      => esc_html__( 'Postal Code', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_organization_postal_code_'.$schema_id,
                            'type'       => 'text',                           
                        ),
                        array(
                                'label'      => esc_html__( 'Email', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_organization_email_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                        array(
                            'label'      => esc_html__( 'Telephone', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_organization_telephone_'.$schema_id,
                            'type'       => 'text',                           
                        ),
                        array(
                                'label'      => esc_html__( 'Website', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_organization_website_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                            array(
                                'label'      => esc_html__( 'Facebook', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_organization_facebook_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                            array(
                                'label'      => esc_html__( 'Twitter', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_organization_twitter_'.$schema_id,
                                'type'       => 'text',                           
                           ),
                           array(
                                'label'      => esc_html__( 'LinkedIn', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_organization_linkedin_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                            array(
                                'label'      => esc_html__( 'ContactPoint Type', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_organization_contact_point_type_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                            array(
                                'label'      => esc_html__( 'ContactPoint Telephone', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_organization_contact_point_telephone_'.$schema_id,
                                'type'       => 'text',                           
                            ),                            
                           array(
                                'label'      => esc_html__( 'Threads', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_organization_threads_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                           array(
                                'label'      => esc_html__( 'Mastodon', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_organization_mastodon_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                           array(
                                'label'      => esc_html__( 'Vibehut', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_organization_vibehut_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                            array(
                                'label'      => esc_html__( 'Founder', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_organization_founder_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                            array(
                                'label'      => esc_html__( 'Founding Date', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_organization_founding_date_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                            array(
                                'label'      => esc_html__( 'Dun & Bradstreet DUNS', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_organization_duns_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                            array(
                                'label'      => esc_html__( 'Qualifications ( Credential Awarded)', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_organization_qualifications_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                            array(
                                'label'      => esc_html__( 'Knows About', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_organization_knows_about_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                            array(
                                'label'      => esc_html__( 'Member Of', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_organization_member_of_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                            array(
                                'label'      => esc_html__( 'Parent Organization', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_organization_parent_organization_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                            array(
                                'label'      => esc_html__( 'Aggregate Rating', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_organization_enable_rating_'.$schema_id,
                                'type'       => 'checkbox',                            
                            ),
                            array(
                                'label'      => esc_html__( 'Rating', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_organization_rating_value_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                            array(
                                'label'      => esc_html__( 'Rating Count', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_organization_rating_count_'.$schema_id,
                                'type'       => 'text',                            
                            ),
                            array(
                                'label'      => esc_html__( 'Publishing Principles', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_organization_publishing_principles_'.$schema_id,
                                'type'       => 'text',
                            ),
                            array(
                                'label'      => esc_html__( 'Corrections Policy', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_organization_corrections_policy_'.$schema_id,
                                'type'       => 'text',
                            ),
                            array(
                                'label'      => esc_html__( 'Ethics Policy', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_organization_ethics_policy_'.$schema_id,
                                'type'       => 'text',
                            ),
                            array(
                                'label'      => esc_html__( 'Diversity Policy', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_organization_diversity_policy_'.$schema_id,
                                'type'       => 'text',
                            ),
                            array(
                                'label'      => esc_html__( 'Verification FactChecking Policy', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_organization_vfc_policy_'.$schema_id,
                                'type'       => 'text',
                            ),
                            array(
                                'label'      => esc_html__( 'Actionable Feedback Policy', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_organization_af_policy_'.$schema_id,
                                'type'       => 'text',
                            ),
                            array(
                                'label'      => esc_html__( 'Unnamed Sources Policy', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_organization_uns_policy_'.$schema_id,
                                'type'       => 'text',
                            ),
                            array(
                                'label'      => esc_html__( 'Mission Coverage Priorities Policy', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_organization_mcp_policy_'.$schema_id,
                                'type'       => 'text',
                            ),
                            array(
                                'label'      => esc_html__( 'Mester Head', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_organization_masthead_'.$schema_id,
                                'type'       => 'text',
                            ), 
                            array(
                                'label'      => esc_html__( 'Award', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_organization_award_'.$schema_id,
                                'type'       => 'text',                            
                            ),   
                                                                                        
                   );
                    break;
                
                    case 'Project':
                    
                        $meta_field = array(   
                        array(
                                'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_project_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'Project'   
                        ),                      
                        array(
                                'label'      => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_project_name_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                        array(
                                'label'      => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_project_description_'.$schema_id,
                                'type'       => 'textarea',                           
                            ),    
                        array(
                                'label'      => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_project_url_'.$schema_id,
                                'type'       => 'text',                           
                            ), 
                        array(
                               'label'      => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                               'id'         => 'saswp_project_image_'.$schema_id,
                               'type'       => 'media',                           
                            ),
                        array(
                                'label'      => esc_html__( 'Logo', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_project_logo_'.$schema_id,
                                'type'       => 'media',                           
                            ), 
                        array(
                                'label'      => esc_html__( 'Street Address', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_project_street_address_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                            array(
                                'label'      => esc_html__( 'City', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_project_city_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                            array(
                                'label'      => esc_html__( 'State', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_project_state_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                            array(
                                'label'      => esc_html__( 'Country', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_project_country_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                            array(
                                'label'      => esc_html__( 'Postal Code', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_project_postal_code_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                            array(
                                    'label'      => esc_html__( 'Email', 'schema-and-structured-data-for-wp' ),
                                    'id'         => 'saswp_project_email_'.$schema_id,
                                    'type'       => 'text',                           
                                ),
                            array(
                                'label'      => esc_html__( 'Telephone', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_project_telephone_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                            array(
                                    'label'      => esc_html__( 'Website', 'schema-and-structured-data-for-wp' ),
                                    'id'         => 'saswp_project_website_'.$schema_id,
                                    'type'       => 'text',                           
                                ),
                                array(
                                    'label'      => esc_html__( 'Facebook', 'schema-and-structured-data-for-wp' ),
                                    'id'         => 'saswp_project_facebook_'.$schema_id,
                                    'type'       => 'text',                           
                                ),
                                array(
                                    'label'      => esc_html__( 'Twitter', 'schema-and-structured-data-for-wp' ),
                                    'id'         => 'saswp_project_twitter_'.$schema_id,
                                    'type'       => 'text',                           
                               ),
                               array(
                                    'label'      => esc_html__( 'LinkedIn', 'schema-and-structured-data-for-wp' ),
                                    'id'         => 'saswp_project_linkedin_'.$schema_id,
                                    'type'       => 'text',                           
                                ),
                               array(
                                    'label'      => esc_html__( 'Thread', 'schema-and-structured-data-for-wp' ),
                                    'id'         => 'saswp_project_threads_'.$schema_id,
                                    'type'       => 'text',                           
                                ),
                               array(
                                    'label'      => esc_html__( 'Mastodon', 'schema-and-structured-data-for-wp' ),
                                    'id'         => 'saswp_project_mastodon_'.$schema_id,
                                    'type'       => 'text',                           
                                ),
                               array(
                                    'label'      => esc_html__( 'Vibehut', 'schema-and-structured-data-for-wp' ),
                                    'id'         => 'saswp_project_vibehut_'.$schema_id,
                                    'type'       => 'text',                           
                                ),
                                array(
                                    'label'      => esc_html__( 'Founder', 'schema-and-structured-data-for-wp' ),
                                    'id'         => 'saswp_project_founder_'.$schema_id,
                                    'type'       => 'text',                           
                                ),
                                array(
                                    'label'      => esc_html__( 'Founding Date', 'schema-and-structured-data-for-wp' ),
                                    'id'         => 'saswp_project_founding_date_'.$schema_id,
                                    'type'       => 'text',                           
                                ),
                                array(
                                    'label'      => esc_html__( 'Dun & Bradstreet DUNS', 'schema-and-structured-data-for-wp' ),
                                    'id'         => 'saswp_project_duns_'.$schema_id,
                                    'type'       => 'text',                           
                                ),
                                array(
                                    'label'      => esc_html__( 'Qualifications ( Credential Awarded)', 'schema-and-structured-data-for-wp' ),
                                    'id'         => 'saswp_project_qualifications_'.$schema_id,
                                    'type'       => 'text',                           
                                ),
                                array(
                                    'label'      => esc_html__( 'Knows About', 'schema-and-structured-data-for-wp' ),
                                    'id'         => 'saswp_project_knows_about_'.$schema_id,
                                    'type'       => 'text',                           
                                ),
                                array(
                                    'label'      => esc_html__( 'Member Of', 'schema-and-structured-data-for-wp' ),
                                    'id'         => 'saswp_project_member_of_'.$schema_id,
                                    'type'       => 'text',                           
                                ),
                                array(
                                    'label'      => esc_html__( 'Parent project', 'schema-and-structured-data-for-wp' ),
                                    'id'         => 'saswp_project_parent_project_'.$schema_id,
                                    'type'       => 'text',                           
                                ),
                            array(
                                'label'      => esc_html__( 'Aggregate Rating', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_project_enable_rating_'.$schema_id,
                                'type'       => 'checkbox',                            
                            ),
                            array(
                                'label'      => esc_html__( 'Rating', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_project_rating_value_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                            array(
                                'label'      => esc_html__( 'Rating Count', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_project_rating_count_'.$schema_id,
                                'type'       => 'text',                            
                            ),    
                                                                                            
                       );
                        break;
                
                case 'Movie':
                    
                    $meta_field = array(          
                        array(
                                'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_movie_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'movie'   
                        ),              
                        array(
                            'label'      => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_movie_name_'.$schema_id,
                            'type'       => 'text',                           
                        ),
                        array(
                            'label'      => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_movie_description_'.$schema_id,
                            'type'       => 'textarea',                           
                        ),
                        array(
                            'label'      => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_movie_url_'.$schema_id,
                            'type'       => 'text',                           
                        ),
                        array(
                            'label'      => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_movie_image_'.$schema_id,
                            'type'       => 'media',                           
                        ),
                        array(
                            'label'      => esc_html__( 'Date Created', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_movie_date_created_'.$schema_id,
                            'type'       => 'text',                           
                        ),
                        array(
                            'label'      => esc_html__( 'Director', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_movie_director_'.$schema_id,
                            'type'       => 'text',                           
                        ),
                        array(
                                'label'      => esc_html__( 'Actor', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_movie_actor_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                        array(
                            'label'      => esc_html__( 'Aggregate Rating', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_movie_enable_rating_'.$schema_id,
                            'type'       => 'checkbox',                            
                        ),
                        array(
                            'label'      => esc_html__( 'Rating', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_movie_rating_value_'.$schema_id,
                            'type'       => 'text',                           
                        ),
                        array(
                            'label'      => esc_html__( 'Rating Count', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_movie_rating_count_'.$schema_id,
                            'type'       => 'text',                            
                        )                                                                                         
                   );
                    break;
                
                case 'TouristTrip':
                    $meta_field = array(
                        array(
                            'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_tt_schema_id_'.$schema_id,
                            'type'       => 'text',
                            'default'    => 'TouristTrip'   
                        ),
                        array(
                            'label'      => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_tt_schema_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                        ),
                        array(
                            'label'      => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_tt_schema_description_'.$schema_id,
                            'type'       => 'textarea',
                            'attributes' => array(
                                'placeholder' => 'Description'
                            ), 
                        ),
                        array(
                            'label'      => esc_html__( 'Tourist Type', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_tt_schema_ttype_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Tourist Type'
                            ) 
                        ),
                        array(
                            'label'      => esc_html__( 'Subject Of Name', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_tt_schema_son_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Subject Of Name'
                            ), 
                        ),
                        array(
                            'label'      => esc_html__( 'Subject Of URL', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_tt_schema_sou_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Subject Of URL'
                            ), 
                        ),
                    );
                    break;
                    
                    case 'VacationRental':
                    $meta_field = array(
                        array(
                            'label'   => esc_html__( 'Additional Type', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_vr_schema_additional_type_'.$schema_id,
                            'type'    => 'select',
                            'options' => array(
                                    'Apartment'             => 'Apartment',
                                    'Bungalow'              => 'Bungalow',                        
                                    'Cabin'                 => 'Cabin',                        
                                    'Chalet'                => 'Chalet',                        
                                    'Cottage'               => 'Cottage',                        
                                    'Gite'                  => 'Gite',                        
                                    'HolidayVillageRental'  => 'HolidayVillageRental',                        
                                    'House'                 => 'House',                        
                                    'Villa'                 => 'Villa',                        
                                    'VacationRental'        => 'VacationRental'                        
                            )
                        ),
                        array(
                            'label'      => esc_html__( 'Brand', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_vr_schema_brand_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Brand ID'
                            )
                        ),
                        array(
                            'label'      => esc_html__( 'Contains Place Additional Type', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_vr_schema_cpat_'.$schema_id,
                            'type'       => 'select',
                            'options' => array(
                                'EntirePlace' => 'EntirePlace',
                                'PrivateRoom' => 'PrivateRoom',
                                'SharedRoom' => 'SharedRoom'
                            )
                        ),
                        array(
                            'label'      => esc_html__( 'Occupancy', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_vr_schema_occupancy_'.$schema_id,
                            'type'       => 'number',
                            'attributes' => array(
                                'placeholder' => '4'
                            )
                        ),
                        array(
                            'label'      => esc_html__( 'Floor Size Value', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_vr_schema_floor_value_'.$schema_id,
                            'type'       => 'number',
                            'attributes' => array(
                                'placeholder' => '75'
                            )
                        ),
                        array(
                            'label'      => esc_html__( 'Floor Size Unit Code', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_vr_schema_floor_uc_'.$schema_id,
                            'type'       => 'select',
                            'options' => array(
                                'FTK' => 'FTK',
                                'SQFT' => 'SQFT',
                                'MTK' => 'MTK',
                                'SQM' => 'SQM'
                            )
                        ),
                        array(
                            'label'      => esc_html__( 'Total Bathrooms', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_vr_schema_total_bathrooms_'.$schema_id,
                            'type'       => 'number',
                            'attributes' => array(
                                'placeholder' => '1'
                            )
                        ),
                        array(
                            'label'      => esc_html__( 'Number Of Bedrooms', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_vr_schema_total_bedrooms_'.$schema_id,
                            'type'       => 'number',
                            'attributes' => array(
                                'placeholder' => '3'
                            )
                        ),
                        array(
                            'label'      => esc_html__( 'Number Of Rooms', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_vr_schema_total_rooms_'.$schema_id,
                            'type'       => 'number',
                            'attributes' => array(
                                'placeholder' => '5'
                            )
                        ),
                        array(
                            'label'      => esc_html__( 'Identifier', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_vr_schema_identifier_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Enter Identifier'
                            )
                        ),
                        array(
                            'label'      => esc_html__( 'Latitude', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_vr_schema_latitude_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Enter Latitude'
                            )
                        ),
                        array(
                            'label'      => esc_html__( 'longitude', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_vr_schema_longitude_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Enter Longitude'
                            )
                        ),
                        array(
                            'label'      => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_vr_schema_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Enter Name'
                            )
                        ),
                        array(
                            'label'      => esc_html__( 'Address Country', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_vr_schema_country_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'US'
                            )
                        ),
                        array(
                            'label'      => esc_html__( 'Address Locality', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_vr_schema_locality_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Mountain View'
                            )
                        ),
                        array(
                            'label'      => esc_html__( 'Address Region', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_vr_schema_region_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'California'
                            )
                        ),
                        array(
                            'label'      => esc_html__( 'Postal Code', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_vr_schema_p_code_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => '94043'
                            )
                        ),
                        array(
                            'label'      => esc_html__( 'Street Address', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_vr_schema_s_address_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => '1600 Amphitheatre Pkwy'
                            )
                        ),
                        array(
                                'label'    => esc_html__( 'Checkin Time', 'schema-and-structured-data-for-wp' ),
                                'id'       => 'saswp_vr_schema_checkin_time_'.$schema_id,
                                'type'     => 'text',
                                'attributes' => array(
                                    'placeholder' => '18:00:00+08:00'
                                )                               
                            ),
                        array(
                                'label'    => esc_html__( 'Checkout Time', 'schema-and-structured-data-for-wp' ),
                                'id'       => 'saswp_vr_schema_checkout_time_'.$schema_id,
                                'type'     => 'text',
                                'attributes' => array(
                                    'placeholder' => '11:00:00+08:00'
                                )                              
                            ),
                        array(
                                'label'    => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                                'id'       => 'saswp_vr_schema_description_'.$schema_id,
                                'type'     => 'text'                             
                            ),
                        array(
                                'label'    => esc_html__( 'Knows Language', 'schema-and-structured-data-for-wp' ),
                                'id'       => 'saswp_vr_schema_knows_language_'.$schema_id,
                                'type'     => 'text'                             
                            ),
                        array(
                                'label' => esc_html__( 'Aggregate Rating', 'schema-and-structured-data-for-wp' ),
                                'id'    => 'saswp_vr_schema_enable_rating_'.$schema_id,
                                'type'  => 'checkbox',                           
                            ),
                        array(
                                'label' => esc_html__( 'Rating Value', 'schema-and-structured-data-for-wp' ),
                                'id'    => 'saswp_vr_schema_rating_value_'.$schema_id,
                                'type'  => 'text',                           
                            ),
                        array(
                                'label' => esc_html__( 'Rating Count', 'schema-and-structured-data-for-wp' ),
                                'id'    => 'saswp_vr_schema_rating_count_'.$schema_id,
                                'type'  => 'text',                            
                            ),
                        array(
                                'label' => esc_html__( 'Review Count', 'schema-and-structured-data-for-wp' ),
                                'id'    => 'saswp_vr_schema_review_count_'.$schema_id,
                                'type'  => 'text',                            
                            ),
                        array(
                                'label' => esc_html__( 'Best rating', 'schema-and-structured-data-for-wp' ),
                                'id'    => 'saswp_vr_schema_best_rating_'.$schema_id,
                                'type'  => 'text',                            
                            )
                    );
                    break;
                    
                    case 'LearningResource':
                    $meta_field = array(
                        array(
                               'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                               'id'         => 'saswp_lr_id_'.$schema_id,
                               'type'       => 'text',
                               'default'    => 'LearningResource'   
                            ),
                        array(
                                'label'   => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_lr_name_'.$schema_id,
                                'type'    => 'text',
                                'default' => saswp_get_the_title()
                            ),
                        array(
                                'label'   => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_lr_description_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => is_object($post) ? $post->post_excerpt : ''
                            ),
                        array(
                                'label' => esc_html__( 'Keywords', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_lr_keywords_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_tags()
                            ),
                        array(
                                'label' => esc_html__( 'Learning Resource Type', 'schema-and-structured-data-for-wp' ),
                                'id' => 'saswp_lr_lrt_'.$schema_id,
                                'type' => 'text'
                            ),
                        array(
                                'label'   => esc_html__( 'In Language', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_lr_inlanguage_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_bloginfo('language'),
                                'note' => 'Note: If there are ore than one language, separate language list by comma ( , )' 
                            ),
                        array(
                                'label'      => esc_html__( 'Date Created', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_lr_date_created_'.$schema_id,
                                'type'       => 'text'                          
                            ),
                        array(
                                'label'      => esc_html__( 'Date Modified', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_lr_date_modified_'.$schema_id,
                                'type'       => 'text'                         
                            ),
                        array(
                                'label'      => esc_html__( 'Typical Age Range', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_lr_tar_'.$schema_id,
                                'type'       => 'text'                           
                            ),
                        array(
                                'label'   => esc_html__( 'Educational Level Name', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_lr_education_level_name_'.$schema_id,
                                'type'    => 'text'
                            ),
                        array(
                                'label'   => esc_html__( 'Educational Level URL', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_lr_education_level_url_'.$schema_id,
                                'type'    => 'text'
                            ),
                        array(
                                'label'   => esc_html__( 'Educational Level Term Set', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_lr_education_level_term_set_'.$schema_id,
                                'type'    => 'text'
                            ),
                        array(
                                'label'   => esc_html__( 'Time Required', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_lr_time_required_'.$schema_id,
                                'type'    => 'text'
                            ),
                        array(
                                'label'   => esc_html__( 'License', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_lr_license_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                    'All rights reserved'                                                   => 'All rights reserved',
                                    'CC BY (Attribution)'                                                   => 'CC BY (Attribution)',
                                    'CC BY-SA (Attribution - Share alike)'                                  => 'CC BY-SA (Attribution - Share alike)',
                                    'CC BY-ND (Attribution - No derivative works)'                          => 'CC BY-ND (Attribution - No derivative works)',
                                    'CC BY-NC (Attribution - No commercial use)'                            => 'CC BY-NC (Attribution - No commercial use)',
                                    'CC BY-NC-SA (Attribution - No commercial use - Share alike)'           => 'CC BY-NC-SA (Attribution - No commercial use - Share alike)',
                                    'CC BY-NC-ND (Attribution - No commercial use - No derivatives works)'  => 'CC BY-NC-ND (Attribution - No commercial use - No derivatives works)',
                                    'GNU General Public License (GPL)'                                      => 'GNU General Public License (GPL)',
                                    'GNU Free Documentation License (GFDL)'                                 => 'GNU Free Documentation License (GFDL)',
                                    'Public domain'                                                         => 'Public domain',
                                    'Other'                                                                 => 'Other'
                                )
                            ),
                        array(
                                'label'   => esc_html__( 'Is Accessible For Free', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_lr_time_iaff_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                    'True' => 'Yes',
                                    'False' => 'No'
                                )
                            ),
                        array(
                                'label'     => esc_html__( 'Educational Framework', 'schema-and-structured-data-for-wp' ),
                                'id'        => 'saswp_lr_eaef_'.$schema_id,
                                'type'      => 'text'
                            ),
                        array(
                                'label'     => esc_html__( 'Target Name', 'schema-and-structured-data-for-wp' ),
                                'id'        => 'saswp_lr_eatn_'.$schema_id,
                                'type'      => 'text'
                            ),
                        array(
                                'label'     => esc_html__( 'Target URL', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_lr_eatu_'.$schema_id,
                                'type'      => 'text'
                            ),
                        array(
                                'label'     => esc_html__( 'Audience', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_lr_audience_'.$schema_id,
                                'type'      => 'text'
                            )

                        );
                    break;
                    
                    case 'LiveBlogPosting':

                    $meta_field = array(
                         array(
                               'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                               'id'         => 'saswp_lbp_id_'.$schema_id,
                               'type'       => 'text',
                               'default'    => 'LiveBlogPosting',   
                            ),
                        array(
                                'label'   => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_lbp_name_'.$schema_id,
                                'type'    => 'text',
                                'default' => saswp_get_the_title()
                            ),
                        array(
                                'label'   => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_lbp_description_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => is_object($post) ? $post->post_excerpt : ''
                            ),
                        array(
                                'label'   => esc_html__( 'About', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_lbp_about_'.$schema_id,
                                'type'    => 'text',
                                'is_template_attr' => 'yes',
                            ),
                        array(
                            'label' => esc_html__( 'Headline', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_lbp_headline_'.$schema_id,
                            'type' => 'text',
                            'default'=> saswp_get_the_title()
                        ),
                        array(
                                'label'   => esc_html__( 'Place Name', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_lbp_place_'.$schema_id,
                                'type'    => 'text',
                            ),
                        array(
                                'label'   => esc_html__( 'Street Address', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_lbp_street_address_'.$schema_id,
                                'type'    => 'text',
                            ),
                        array(
                                'label'   => esc_html__( 'Locality', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_lbp_locality_'.$schema_id,
                                'type'    => 'text',
                            ),
                        array(
                                'label'   => esc_html__( 'Postal Code', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_lbp_postal_code_'.$schema_id,
                                'type'    => 'text',
                            ),
                        array(
                                'label'   => esc_html__( 'Region', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_lbp_region_'.$schema_id,
                                'type'    => 'text',
                            ),
                        array(
                                'label'   => esc_html__( 'Country', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_lbp_country_'.$schema_id,
                                'type'    => 'text',
                            ),
                        array(
                                'label'   => esc_html__( 'Start Date', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_lbp_start_date_'.$schema_id,
                                'type'    => 'text',
                                'attributes' => array(
                                    'placeholder' => 'YYYY-MM-DD'
                                ),
                            ),
                        array(
                                'label'   => esc_html__( 'Coverage Start Date', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_lbp_coverage_start_date_'.$schema_id,
                                'type'    => 'text',
                                'attributes' => array(
                                    'placeholder' => 'YYYY-MM-DD'
                                ),
                            ),
                        array(
                                'label'   => esc_html__( 'Coverage Start Time', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_lbp_coverage_start_time_'.$schema_id,
                                'type'    => 'text',
                            ),
                        array(
                                'label'   => esc_html__( 'Coverage End Date', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_lbp_coverage_end_date_'.$schema_id,
                                'type'    => 'text',
                                'attributes' => array(
                                    'placeholder' => 'YYYY-MM-DD'
                                ),
                            ),
                        array(
                                'label'   => esc_html__( 'Coverage End Time', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_lbp_coverage_end_time_'.$schema_id,
                                'type'    => 'text',
                            ),
                        array(
                                'label'   => esc_html__( 'Live Blog Update', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_lbp_live_blog_update_'.$schema_id,
                                'type'    => 'text',
                                'is_template_attr' => 'yes', 
                            ),
                    );

                    break;

                    case 'ImageGallery':

                    $meta_field = array(
                        array(
                               'label'    => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                               'id'       => 'saswp_img_gallery_id_'.$schema_id,
                               'type'     => 'text',
                               'default'  => 'ImageGallery',   
                            ),
                        array(
                                'label'   => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_img_gallery_name_'.$schema_id,
                                'type'    => 'text',
                                'default' => saswp_get_the_title()
                            ),
                        array(
                                'label'   => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_img_gallery_description_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => is_object($post) ? $post->post_excerpt : ''
                            ),
                        array(
                                'label'   => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_img_gallery_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_permalink()
                            ),
                        array(
                                'label'   => esc_html__( 'Date Published', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_img_gallery_date_published_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_the_date("Y-m-d")
                            ), 
                        array(
                                'label'   => esc_html__( 'Date Modified', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_img_gallery_date_modified_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_the_modified_date("Y-m-d")
                            ),
                    );

                    break;

                    case 'MediaGallery':

                    $meta_field = array(
                        array(
                               'label'    => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                               'id'       => 'saswp_media_gallery_id_'.$schema_id,
                               'type'     => 'text',
                               'default'  => 'MediaGallery',   
                            ),
                        array(
                                'label'   => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_media_gallery_name_'.$schema_id,
                                'type'    => 'text',
                                'default' => saswp_get_the_title()
                            ),
                        array(
                                'label'   => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_media_gallery_description_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => is_object($post) ? $post->post_excerpt : ''
                            ),
                        array(
                                'label'   => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_media_gallery_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_permalink()
                            ),
                        array(
                                'label'   => esc_html__( 'Date Published', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_media_gallery_date_published_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_the_date("Y-m-d")
                            ), 
                        array(
                                'label'   => esc_html__( 'Date Modified', 'schema-and-structured-data-for-wp' ),
                                'id'      => 'saswp_media_gallery_date_modified_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_the_modified_date("Y-m-d")
                            ),
                    );

                    break;

                    case 'ProfilePage':
                    
                    $meta_field = array(
                        array(
                               'label'   => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                               'id'      => 'saswp_profile_page_schema_id_'.$schema_id,
                               'type'    => 'text'                                
                        ),   
                        array(
                            'label'      => esc_html__( 'Honorific Prefix', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_honorific_prefix_'.$schema_id,
                            'type'       => 'text',                            
                        ),
                        array(
                            'label'      => esc_html__( 'Honorific Suffix', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_honorific_suffix_'.$schema_id,
                            'type'       => 'text',                            
                        ),     
                        array(
                                'label'      => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_profile_page_schema_name_'.$schema_id,
                                'type'       => 'text',                           
                        ),
                        array(
                                'label'      => esc_html__( 'Alternate Name', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_profile_page_schema_alternate_name_'.$schema_id,
                                'type'       => 'text',                           
                        ),
                        array(
                               'label'      => esc_html__( 'Additional Name', 'schema-and-structured-data-for-wp' ),
                               'id'         => 'saswp_profile_page_schema_additional_name_'.$schema_id,
                               'type'       => 'text',                           
                        ),
                        array(
                            'label'      => esc_html__( 'Given Name', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_given_name_'.$schema_id,
                            'type'       => 'text',                           
                        ),
                        array(
                                'label'      => esc_html__( 'Family Name', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_profile_page_schema_family_name_'.$schema_id,
                                'type'       => 'text',                           
                        ),
                        array(
                            'label'      => esc_html__( 'Spouse', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_spouse_'.$schema_id,
                            'type'       => 'text',                            
                        ),
                        array(
                            'label'      => esc_html__( 'Parent', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_parent_'.$schema_id,
                            'type'       => 'textarea',
                            'attributes' => array(
                                    'placeholder' => 'Johannes Xoo, Amanda Xoo'
                             ),
                            'note' => 'Note: Separate it by comma ( , )' ,                            
                        ),
                        array(
                            'label'      => esc_html__( 'Sibling', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_sibling_'.$schema_id,
                            'type'       => 'textarea',
                            'attributes' => array(
                                    'placeholder' => 'Dima Xoo, Amanda Xoo'
                             ),
                            'note' => 'Note: Separate it by comma ( , )' ,                            
                        ),
                        array(
                            'label'      => esc_html__( 'Colleague', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_colleague_'.$schema_id,
                            'type'       => 'textarea',
                            'attributes' => array(
                                    'placeholder' => 'Bill Gates, Jeff Bezos'
                             ),
                            'note' => 'Note: Separate it by comma ( , )' ,                            
                        ),
                        array(
                                'label'      => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_profile_page_schema_description_'.$schema_id,
                                'type'       => 'textarea',                           
                        ),    
                        array(
                                'label'      => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_profile_page_schema_url_'.$schema_id,
                                'type'       => 'text',
                                'default'    => get_permalink()
                        ),   
                        array(
                                'label'      => esc_html__( 'Locality', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_profile_page_schema_locality_'.$schema_id,
                                'type'       => 'text',
                               
                        ),
                        array(
                                'label'      => esc_html__( 'Region', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_profile_page_schema_region_'.$schema_id,
                                'type'       => 'text',                           
                        ),
                        array(
                                'label'      => esc_html__( 'Postal Code', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_profile_page_schema_postal_code_'.$schema_id,
                                'type'       => 'text',                           
                        ),
                        array(
                                'label'      => esc_html__( 'Country', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_profile_page_schema_country_'.$schema_id,
                                'type'       => 'text',                           
                        ),
                        array(
                                'label'      => esc_html__( 'Email', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_profile_page_schema_email_'.$schema_id,
                                'type'       => 'text',                           
                        ),
                        array(
                                'label'      => esc_html__( 'Telephone', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_profile_page_schema_telephone_'.$schema_id,
                                'type'       => 'text',                           
                        ),    
                        array(
                                'label'      => esc_html__( 'Gender', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_profile_page_schema_gender_'.$schema_id,
                                'type'       => 'select',
                                'options'    => array(
                                        'Male'   => 'Male',
                                        'Female' => 'Female',    
                                )
                        ),
                        array(
                            'label'      => esc_html__( 'Date Of Birth', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_date_of_birth_'.$schema_id,
                            'type'       => 'text',                            
                        ),
                        array(
                               'label'      => esc_html__( 'Date of death', 'schema-and-structured-data-for-wp' ),
                               'id'         => 'saswp_profile_page_schema_date_of_death_'.$schema_id,
                               'type'       => 'text',                            
                        ),
                        array(
                                'label'      => esc_html__( 'Member Of', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_profile_page_schema_member_of_'.$schema_id,
                                'type'       => 'text',                            
                        ),
                        array(
                                'label'      => esc_html__( 'Nationality', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_profile_page_schema_nationality_'.$schema_id,
                                'type'       => 'text',                            
                        ),                    
                        array(
                                'label'      => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_profile_page_schema_image_'.$schema_id,
                                'type'       => 'media',                            
                        ),
                        array(
                                'label'      => esc_html__( 'Job Title', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_profile_page_schema_job_title_'.$schema_id,
                                'type'       => 'text',                            
                        ),
                        array(
                                'label'      => esc_html__( 'Company ( Works For )', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_profile_page_schema_company_'.$schema_id,
                                'type'       => 'text',                            
                        ),
                        array(
                                'label'      => esc_html__( 'Website', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_profile_page_schema_website_'.$schema_id,
                                'type'       => 'text',                            
                        ),
                        array(
                            'label'      => esc_html__( 'Facebook', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_facebook_'.$schema_id,
                            'type'       => 'text',                            
                        ),
                        array(
                            'label'      => esc_html__( 'Youtube', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_youtube_'.$schema_id,
                            'type'       => 'text',                            
                        ),
                        array(
                            'label'      => esc_html__( 'X (Twitter)', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_twitter_'.$schema_id,
                            'type'       => 'text',                            
                        ),
                        array(
                            'label'      => esc_html__( 'LinkedIn', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_linkedin_'.$schema_id,
                            'type'       => 'text',                            
                        ),
                        array(
                            'label'      => esc_html__( 'Instagram', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_instagram_'.$schema_id,
                            'type'       => 'text',                            
                        ),
                        array(
                            'label'      => esc_html__( 'Snapchat', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_snapchat_'.$schema_id,
                            'type'       => 'text',                            
                        ),
                        array(
                            'label'      => esc_html__( 'Thread', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_threads_'.$schema_id,
                            'type'       => 'text',                            
                        ),
                        array(
                            'label'      => esc_html__( 'Mastodon', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_mastodon_'.$schema_id,
                            'type'       => 'text',                            
                        ),
                        array(
                            'label'      => esc_html__( 'Vibehut', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_vibehut_'.$schema_id,
                            'type'       => 'text',                            
                        ),
                        array(
                            'label'      => esc_html__( 'Sponsor', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_sponsor_'.$schema_id,
                            'type'       => 'text',                            
                        ),
                        array(
                            'label'      => esc_html__( 'Affiliation', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_affiliation_'.$schema_id,
                            'type'       => 'text',                            
                        ),
                        array(
                            'label'      => esc_html__( 'Alumni Of', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_alumniof_'.$schema_id,
                            'type'       => 'text',                            
                        ), 
                        array(
                            'label'      => esc_html__( 'Award', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_award_'.$schema_id,
                            'type'       => 'text',                            
                        ),
                        array(
                            'label'      => esc_html__( 'Seeks', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_seeks_'.$schema_id,
                            'type'       => 'text',                            
                        ),
                        array(
                            'label'      => esc_html__( 'Knows', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_knows_'.$schema_id,
                            'type'       => 'text',                            
                        ),
                        array(
                            'label'      => esc_html__( 'Owns', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_owns_'.$schema_id,
                            'type'       => 'text',                            
                        ),
                        array(
                            'label'      => esc_html__( 'Brand', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_brand_'.$schema_id,
                            'type'       => 'text',                            
                        ),
                        array(
                            'label'      => esc_html__( 'Qualifications', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_qualifications_'.$schema_id,
                            'type'       => 'text',                            
                        ),
                        array(
                            'label'      => esc_html__( 'Occupation Name', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_occupation_name_'.$schema_id,
                            'type'       => 'text',                            
                        ),
                        array(
                            'label'      => esc_html__( 'Occupation Description', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_occupation_description_'.$schema_id,
                            'type'       => 'textarea',                            
                        ),
                        array(
                            'label'      => esc_html__( 'Estimated Salary', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_estimated_salary_'.$schema_id,
                            'type'       => 'text',                            
                        ),
                        array(
                            'label'      => esc_html__( 'Salary Currency', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_salary_currency_'.$schema_id,
                            'type'       => 'text',                            
                        ),
                        array(
                            'label'      => esc_html__( 'Salary Duration', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_salary_duration_'.$schema_id,
                            'type'       => 'text',                            
                        ),
                        array(
                            'label'      => esc_html__( 'Salary Median', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_salary_median_'.$schema_id,
                            'type'       => 'text',                            
                        ),
                        array(
                            'label'      => esc_html__( 'Salary Percentile10', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_salary_percentile10_'.$schema_id,
                            'type'       => 'text',                            
                        ),
                        array(
                            'label'      => esc_html__( 'Salary Percentile25', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_salary_percentile25_'.$schema_id,
                            'type'       => 'text',                            
                        ),
                        array(
                            'label'      => esc_html__( 'Salary Percentile75', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_salary_percentile75_'.$schema_id,
                            'type'       => 'text',                            
                        ),
                        array(
                            'label'      => esc_html__( 'Salary Percentile90', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_salary_percentile90_'.$schema_id,
                            'type'       => 'text',                            
                        ),
                        array(
                            'label'      => esc_html__( 'Salary Last Reviewed', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_salary_last_reviewed_'.$schema_id,
                            'type'       => 'text',                            
                        ),
                        array(
                            'label'      => esc_html__( 'Occupation City', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_occupation_city_'.$schema_id,
                            'type'       => 'text',                            
                        ),
                        array(
                            'label'      => esc_html__( 'performerIn Name', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_performerin_name_'.$schema_id,
                            'type'       => 'text',                            
                        ),
                        array(
                            'label'      => esc_html__( 'performerIn Location Name', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_performerin_location_name_'.$schema_id,
                            'type'       => 'text',                            
                        ),
                        array(
                            'label'      => esc_html__( 'performerIn Location Locality', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_performerin_location_locality_'.$schema_id,
                            'type'       => 'text',                            
                        ),
                        array(
                            'label'      => esc_html__( 'performerIn Location Postal Code', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_performerin_location_postal_code_'.$schema_id,
                            'type'       => 'text',                            
                        ),
                        array(
                            'label'      => esc_html__( 'performerIn Location Street Address', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_performerin_location_street_address_'.$schema_id,
                            'type'       => 'text',                            
                        ),

                        array(
                            'label'      => esc_html__( 'performerIn Offers Name', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_performerin_offers_name_'.$schema_id,
                            'type'       => 'text',                            
                        ),
                        array(
                            'label'      => esc_html__( 'performerIn Offers Availability', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_performerin_offers_availability_'.$schema_id,
                            'type'       => 'select',
                            'options' => array(
                                    ''                  => 'Select',
                                    'InStock'           => 'In Stock',
                                    'OutOfStock'        => 'Out Of Stock',
                                    'Discontinued'      => 'Discontinued',
                                    'PreOrder'          => 'Pre Order', 
                           )                             
                        ),
                        array(
                            'label'      => esc_html__( 'performerIn Offers Price', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_performerin_offers_price_'.$schema_id,
                            'type'       => 'text',                            
                        ),
                        array(
                            'label'      => esc_html__( 'performerIn Offers Currency', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_performerin_offers_currency_'.$schema_id,
                            'type'       => 'text',                            
                        ),
                        array(
                            'label'      => esc_html__( 'performerIn Offers Valid From', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_performerin_offers_valid_from_'.$schema_id,
                            'type'       => 'text',                            
                        ),
                        array(
                            'label'      => esc_html__( 'performerIn Offers URL', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_performerin_offers_url_'.$schema_id,
                            'type'       => 'text',                            
                        ),

                        array(
                            'label'      => esc_html__( 'performerIn Start Date', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_person_schema_performerin_start_date_'.$schema_id,
                            'type'       => 'text',                            
                        ),
                        array(
                            'label'      => esc_html__( esc_html__( 'Name', 'schema-and-structured-data-for-wp' ), 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_performerin_end_date_'.$schema_id,
                            'type'       => 'text',                            
                        ),
                        array(
                            'label'      => esc_html__( 'performerIn Description', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_performerin_description_'.$schema_id,
                            'type'       => 'textarea',                            
                        ),
                        array(
                            'label'      => esc_html__( 'performerIn Image', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_performerin_image_'.$schema_id,
                            'type'       => 'media',                            
                        ),
                        array(
                            'label'      => esc_html__( 'performerIn Performer', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_profile_page_schema_performerin_performer_'.$schema_id,
                            'type'       => 'textarea',
                            'attributes' => array(
                                    'placeholder' => 'Bill Gates, Jeff Bezos'
                             ),
                            'note' => 'Note: Separate it by comma ( , )' ,                             
                        )                    
                   );
                    break;

                case 'Place':
                    
                    $meta_field = array(
                            array(
                                    'label' => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                                    'id' => 'saswp_place_schema_name_'.$schema_id,
                                    'type' => 'text',                                
                            ),
                            array(
                                    'label' => esc_html__( 'Street Address', 'schema-and-structured-data-for-wp' ),
                                    'id' => 'saswp_place_schema_streetaddress_'.$schema_id,
                                    'type' => 'text',                                
                            ),
                            array(
                                    'label' => esc_html__( 'Locality', 'schema-and-structured-data-for-wp' ),
                                    'id' => 'saswp_place_schema_locality_'.$schema_id,
                                    'type' => 'text',                                
                            ),
                            array(
                                    'label' => esc_html__( 'Region', 'schema-and-structured-data-for-wp' ),
                                    'id' => 'saswp_place_schema_region_'.$schema_id,
                                    'type' => 'text',                                
                            ),
                            array(
                                    'label' => esc_html__( 'PostalCode', 'schema-and-structured-data-for-wp' ),
                                    'id' => 'saswp_place_schema_postalcode_'.$schema_id,
                                    'type' => 'text',                                
                            ),
                            array(
                                    'label' => esc_html__( 'Country', 'schema-and-structured-data-for-wp' ),
                                    'id'    => 'saswp_place_schema_country_'.$schema_id,
                                    'type'  => 'text',                                
                            ),
                        );
                break;

                case 'Game':
                    
                    $meta_field = array(
                        array(
                                'label'      => esc_html__( 'ID', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_game_schema_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'Game'   
                        ),
                    array(
                            'label'      => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_game_schema_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                    ),
                    array(
                            'label'      => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_game_schema_url_'.$schema_id,
                            'type'       => 'text',
                            
                    ),
                    array(
                            'label'      => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_game_schema_image_'.$schema_id,
                            'type'       => 'media',
                            
                    ),
                    array(
                            'label'      => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_game_schema_description_'.$schema_id,
                            'type'       => 'textarea',
                            
                    ),
                    array(
                            'label'      => esc_html__( 'Game Items', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_game_schema_game_items_'.$schema_id,
                            'type'       => 'textarea',
                            'attributes' => array(
                                'placeholder' => 'Example, Example2'
                            ),
                            'note'       => 'Note: Separate more than one game items by comma ( , )'
                            
                    ),
                    array(
                            'label'      => esc_html__( 'Genre', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_game_schema_genre_'.$schema_id,
                            'type'       => 'textarea',
                            'attributes' => array(
                                'placeholder' => 'Example, Example2'
                            ),
                            'note'       => 'Note: Separate more than one genre by comma ( , )'
                            
                    ),
                    array(
                            'label'      => esc_html__( 'Min Players', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_game_schema_min_players_'.$schema_id,
                            'type'       => 'number',
                            
                    ),
                    array(
                            'label'      => esc_html__( 'Max Players', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_game_schema_max_players_'.$schema_id,
                            'type'       => 'number',
                            
                    ),
                    array(
                            'label'      => esc_html__( 'Audience Min Age', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_game_schema_aud_min_age_'.$schema_id,
                            'type'       => 'number',
                            
                    ),
                    array(
                            'label'      => esc_html__( 'Copyright Holder', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_game_schema_copyright_'.$schema_id,
                            'type'       => 'text',
                            
                    ),
                    array(
                        'label'      => esc_html__( 'Author Type', 'schema-and-structured-data-for-wp' ),
                        'id'         => 'saswp_game_schema_author_type_'.$schema_id,
                        'type'    => 'select',
                        'options' => array(
                                'Person'           => 'Person',
                                'Organization'     => 'Organization',                        
                       )
                        
                    ),
                    array(
                            'label'      => esc_html__( 'Author Name', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_game_schema_author_name_'.$schema_id,
                            'type'       => 'text',
                            
                    ),
                    array(
                            'label'      => esc_html__( 'Price', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_game_schema_price_'.$schema_id,
                            'type'       => 'text',
                            
                    ),
                    array(
                            'label'      => esc_html__( 'Price Currency', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_game_schema_price_currency_'.$schema_id,
                            'type'       => 'text',
                            
                    ),    
                    array(
                            'label'   => esc_html__( 'Availability', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_game_schema_price_availability_'.$schema_id,
                            'type'    => 'select',                            
                            'options' => array(
                                     ''                  => 'Select',
                                     'InStock'           => 'In Stock',
                                     'OutOfStock'        => 'Out Of Stock',
                                     'Discontinued'      => 'Discontinued',
                                     'PreOrder'          => 'Pre Order', 
                            ) 
                       ), 
                    array(
                            'label'      => esc_html__( 'Publisher', 'schema-and-structured-data-for-wp' ),
                            'id'         => 'saswp_game_schema_publisher_'.$schema_id,
                            'type'       => 'text',
                            
                    ),
                    array(
                            'label' => esc_html__( 'Aggregate Rating', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_game_schema_enable_rating_'.$schema_id,
                            'type' => 'checkbox',                          
                        ),
                        array(
                            'label' => esc_html__( 'Rating','schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_game_schema_rating_'.$schema_id,
                            'type' => 'text',                           
                        ),
                        array(
                            'label' => esc_html__( 'Rating Count', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_game_schema_review_count_'.$schema_id,
                            'type' => 'text',                           
                        ),    
                        
                   );
                    break;
                    
                case 'Certification':
                    $meta_field = array(
                        array(
                            'label'     => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                            'id'        => 'saswp_certification_name_'.$schema_id,
                            'type'      => 'text',
                            'default'   => saswp_get_the_title()
                        ),
                        array(
                            'label'     => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
                            'id'        => 'saswp_certification_description_'.$schema_id,
                            'type'      => 'textarea',
                            'default'   => saswp_strip_all_tags(get_the_excerpt())
                        ),
                        array(
                            'label'     => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                            'id'        => 'saswp_certification_url_'.$schema_id,
                            'type'      => 'text',
                            'default'   => get_permalink()
                        ),
                        array(
                            'label'     => esc_html__( 'Issued By Name', 'schema-and-structured-data-for-wp' ),
                            'id'        => 'saswp_certification_issue_name_'.$schema_id,
                            'type'      => 'textarea',
                            'attributes'=> array(
                                'placeholder' => 'Example, Example2'
                            ),
                            'note'      => 'Note: Separate more than one names by comma ( , )'
                        ),
                        array(
                            'label'     => esc_html__( 'Issued By URL', 'schema-and-structured-data-for-wp' ),
                            'id'        => 'saswp_certification_issue_url_'.$schema_id,
                            'type'      => 'text',
                        ),
                        array(
                            'label'     =>  esc_html__( 'Certification Status', 'schema-and-structured-data-for-wp' ),
                            'id'        => 'saswp_certification_status_'.$schema_id,
                            'type'      => 'select',
                            'options'   => array(
                                'CertificationActive'   => 'CertificationActive',
                                'CertificationInactive' => 'CertificationInactive', 
                            ),                       
                        ),
                        array(
                            'label'   => esc_html__( 'Certification Status', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_certification_date_expires_'.$schema_id,
                            'type'    => 'text',
                            'default' => get_the_date("Y-m-d")
                        ),    
                        array(
                            'label' => esc_html__( 'Date Published', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_certification_date_published_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_date("Y-m-d")
                        ),
                        array(
                            'label' => esc_html__( 'Valid From', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_certification_date_valid_from_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_date("Y-m-d")
                        ),
                        array(
                            'label' => esc_html__( 'Audit Date', 'schema-and-structured-data-for-wp' ),
                            'id' => 'saswp_certification_date_audit_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_date("Y-m-d")
                        ),
                        array(
                            'label'     => esc_html__( 'Valid In Name', 'schema-and-structured-data-for-wp' ),
                            'id'        => 'saswp_certification_validin_name_'.$schema_id,
                            'type'      => 'text',
                        ),
                        array(
                            'label'     => esc_html__( 'Valid In Country', 'schema-and-structured-data-for-wp' ),
                            'id'        => 'saswp_certification_validin_country_'.$schema_id,
                            'type'      => 'textarea',
                            'attributes'=> array(
                                'placeholder' => 'Country1, Country2'
                            ),
                            'note'      => 'Note: Separate more than one countryies by comma ( , )'
                        ),
                        array(
                            'label'    => esc_html__( 'Logo', 'schema-and-structured-data-for-wp' ),
                            'id'       => 'saswp_certification_logo_'.$schema_id,
                            'type'     => 'media'                               
                        ),
                        array(
                            'label'    => esc_html__( 'Identification', 'schema-and-structured-data-for-wp' ),
                            'id'       => 'saswp_certification_identification_'.$schema_id,
                            'type'     => 'text'                               
                        ),
                        array(
                            'label'    => esc_html__( 'Rating Value', 'schema-and-structured-data-for-wp' ),
                            'id'       => 'saswp_certification_rating_value_'.$schema_id,
                            'type'     => 'text'                               
                        ),
                        array(
                            'label'    => esc_html__( 'Best Rating', 'schema-and-structured-data-for-wp' ),
                            'id'       => 'saswp_certification_best_rating_'.$schema_id,
                            'type'     => 'text'                               
                        ),
                        array(
                            'label'    => esc_html__( 'Worst Rating', 'schema-and-structured-data-for-wp' ),
                            'id'       => 'saswp_certification_worst_rating_'.$schema_id,
                            'type'     => 'text'                               
                        ),
                        array(
                            'label'    => esc_html__( 'Measurement Name', 'schema-and-structured-data-for-wp' ),
                            'id'       => 'saswp_certification_measurement_name_'.$schema_id,
                            'type'     => 'text'                               
                        ),
                        array(
                            'label'    => esc_html__( 'Measurement Reference', 'schema-and-structured-data-for-wp' ),
                            'id'       => 'saswp_certification_measurement_reference_'.$schema_id,
                            'type'     => 'text'                               
                        ),
                        array(
                            'label'    => esc_html__( 'Measurement Value', 'schema-and-structured-data-for-wp' ),
                            'id'       => 'saswp_certification_measurement_value_'.$schema_id,
                            'type'     => 'text'                               
                        ),
                    );
                    break;

                    case 'Guide':
                        $meta_field = array(
                            array(
                                'label'     => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                                'id'        => 'saswp_guide_name_'.$schema_id,
                                'type'      => 'text',
                                'default'   => saswp_get_the_title(),
                            ),
                            array(
                                'label'     => esc_html__( 'About', 'schema-and-structured-data-for-wp' ),
                                'id'        => 'saswp_guide_about_'.$schema_id,
                                'type'      => 'text',
                                'default'   => '',
                            ),
                            array(
                                'label'     => esc_html__( 'Text', 'schema-and-structured-data-for-wp' ),
                                'id'        => 'saswp_guide_text_'.$schema_id,
                                'type'      => 'text',
                                'default'   => saswp_strip_all_tags(get_the_excerpt()),
                            ),
                            array(
                                'label'     => esc_html__( 'Review Aspect', 'schema-and-structured-data-for-wp' ),
                                'id'        => 'saswp_guide_review_aspect_'.$schema_id,
                                'type'      => 'text',
                                'note'      => 'Note: Enter all the review aspects in comma separated',
                            ),
                        );
                    break;

                    case 'WebSite':
                    $meta_field = array(
                        array(
                            'label'   => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_website_name_'.$schema_id,
                            'type'    => 'text',
                            'default' => get_bloginfo( 'name' ),
                        ),
                        array(
                            'label'   => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_website_url_'.$schema_id,
                            'type'    => 'text',
                            'default' => home_url(),
                        ),
                        array(
                            'label'   => esc_html__( 'Search URL Template', 'schema-and-structured-data-for-wp' ),
                            'id'      => 'saswp_website_search_target_url_'.$schema_id,
                            'type'    => 'text',
                            'default' => home_url( '/?s={search_term_string}' ),
                            'note'    => 'Example: https://www.siasat.com/?s=&q={search_term_string}',
                        ),
                    );
                    break;

                    case 'SportsTeam':
                        $sports_team_post_meta_data = get_post_meta(get_the_ID());                        
                        
                        $sports_team_name = get_the_title();
                        if ( isset( $sports_team_post_meta_data['saswp_sports_team_name_'.$schema_id]) ) {
                            $sports_team_name = $sports_team_post_meta_data['saswp_sports_team_name_'.$schema_id][0];
                        }                        
                        
                        $sports_team_sport = '';
                        if ( isset( $sports_team_post_meta_data['saswp_sports_team_sport_'.$schema_id]) ) {
                            $sports_team_sport = $sports_team_post_meta_data['saswp_sports_team_sport_'.$schema_id][0];
                        }
                        
                        $sports_team_coach_name = '';
                        if ( isset( $sports_team_post_meta_data['saswp_sports_team_coach_name_'.$schema_id]) ) {
                            $sports_team_coach_name = $sports_team_post_meta_data['saswp_sports_team_coach_name_'.$schema_id][0];
                        }

                        $meta_field = array(
                            array(
                                'label'      => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_sports_team_name_'.$schema_id,
                                'type'       => 'text',
                                'default'    => $sports_team_name   
                            ),
                            array(
                                'label'      => esc_html__( 'Sport', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_sports_team_sport_'.$schema_id,
                                'type'       => 'text',
                                'default'    => $sports_team_sport,
                                'attributes' => array(
                                'placeholder' => 'eg: American Football'
                                ),                        
                            ),
                            array(
                                'label'      => esc_html__( 'Coach Name', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'saswp_sports_team_coach_name_'.$schema_id,
                                'type'       => 'text',
                                'default'    => $sports_team_coach_name,
                                'attributes' => array(
                                'placeholder' => 'eg: Pete Carroll'
                                ),                           
                            ),
                            array(
                                'label'      => esc_html__( 'Member Of (Organizations)', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'sports_team_member_of_'.$schema_id,
                                'type'       => 'repeater'                                                      
                            ),
                            array(
                                'label'      => esc_html__( 'Athlete', 'schema-and-structured-data-for-wp' ),
                                'id'         => 'sports_team_athlete_'.$schema_id,
                                'type'       => 'repeater'                                                      
                            )                                                                              
                        );                                                              
                        
                        break;

                default:
                    break;
            } 
            
            return $meta_field;
	}