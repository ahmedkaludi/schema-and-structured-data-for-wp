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
            
                if(is_object($post)){
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
                if(empty($author_desc)){
                    $author_desc = get_the_author_meta('user_description',$author_id);
                }
                if(empty($author_url)){
                    $author_url = get_the_author_meta('user_url',$author_id);
                }               

                if(function_exists('get_avatar_data') && is_object($current_user) ){
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
                                                
                        if(!empty($local_sub_business)){
                        
                        $sub_business_options = array(
                             'label'     => 'Sub Business Type',
                             'id'        => 'saswp_business_name_'.$schema_id,
                             'type'      => 'select',
                             'options'   => $local_sub_business[$business_type],
                             'default'   => $business_name  
                        ); 

                    }
                        
                        
                    }else{
                        
                       if(!empty($local_sub_business) && array_key_exists($business_type, $local_sub_business)){
                        
                       $sub_business_options = array(
                            'label'     => 'Sub Business Type',
                            'id'        => 'saswp_business_name_'.$schema_id,
                            'type'      => 'select',
                            'options'   => $local_sub_business[$business_type],
                            'default'   => $business_name  
                       ); 
                       
                    }
                        
                    }
                                        
                    $meta_field[] = array(
                            'label'   => 'ID',
                            'id'      => 'local_business_id_'.$schema_id,
                            'type'    => 'text',
                            'default' => 'LocalBusiness'                            
                      
                    );
                    
                    if($manual == null){
                        
                        $meta_field[] = array(
                            'label'   => 'Business Type',
                            'id'      => 'saswp_business_type_'.$schema_id,
                            'type'    => 'select',
                            'default' => $business_type,
                            'options' => $local_sub_business['all_business_type']
                        );
                        $meta_field[] = $sub_business_options;
                        
                    }
                                        
                        $meta_field[] = array(
                            'label'   => 'Business Name',
                            'id'      => 'local_business_name_'.$schema_id,
                            'type'    => 'text', 
                                                        
                        );
                    
                        $meta_field[] = array(
                            'label'    => 'URL',
                            'id'      => 'local_business_name_url_'.$schema_id,
                            'type'    => 'text',
                            'default' => get_permalink()                            
                        );
                    
                        $meta_field[] = array(
                           'label' => 'Description',
                            'id' => 'local_business_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => is_object($post) ? $post->post_excerpt : ''                            
                        );                                            
                        $meta_field[] = array(
                            'label' => 'Street Address',
                            'id' => 'local_street_address_'.$schema_id,
                            'type' => 'text',                                   
                        );
                       
                        $meta_field[] = array(
                            'label' => 'City',
                            'id' => 'local_city_'.$schema_id,
                            'type' => 'text',                        
                        );
                       
                        $meta_field[] = array(
                             'label' => 'State',
                            'id' => 'local_state_'.$schema_id,
                            'type' => 'text',                      
                        );
                        
                        $meta_field[] = array(
                            'label' => 'Country',
                            'id' => 'local_country_'.$schema_id,
                            'type' => 'text',                                   
                        );

                        $meta_field[] = array(
                              'label' => 'Postal Code',
                            'id' => 'local_postal_code_'.$schema_id,
                            'type' => 'text',                     
                        );
                        
                        $meta_field[] = array(
                            'label' => 'Latitude',
                            'id' => 'local_latitude_'.$schema_id,
                            'type' => 'text',                         
                        );
                        
                        $meta_field[] = array(
                                'label' => 'Longitude',
                            'id' => 'local_longitude_'.$schema_id,
                            'type' => 'text',                         
                        );
                        
                        $meta_field[] = array(
                              'label' => 'Phone',
                            'id' => 'local_phone_'.$schema_id,
                            'type' => 'text',                     
                        );
                        
                         $meta_field[] = array(
                              'label' => 'Website',
                            'id' => 'local_website_'.$schema_id,
                            'type' => 'text',                      
                        );
                        
                        $meta_field[] = array(
                             'label' => 'Image',
                            'id' => 'local_business_logo_'.$schema_id,
                            'type' => 'media',                      
                        );
                        
                        $meta_field[] = array(
                             'label' => 'Operation Days',
                            'id' => 'saswp_dayofweek_'.$schema_id,
                            'type' => 'textarea',
                            'attributes' => array(
                                'placeholder' => 'Mo-Sa 11:00-14:30&#10;Mo-Th 17:00-21:30&#10;Fr-Sa 17:00-22:00'
                            ),
                            'note' => 'Note: Enter one operation days per line without comma.'                   
                        );
                        
                        $meta_field[] = array(
                              'label' => 'Area Served',
                            'id'    => 'local_area_served_'.$schema_id,
                            'type'  => 'textarea',
                            'attributes' => array(
                                'placeholder' => 'Example, Example2'
                            ),
                            'note' => 'Note: Separate area served list by comma ( , )'                     
                        );                        
                        $meta_field[] = array(
                               'label' => 'Service Offered Name',
                               'id' => 'local_service_offered_name_'.$schema_id,
                               'type' => 'text',                            
                        );
                        $meta_field[] = array(
                                'label' => 'Service Offered URL',
                                'id' => 'local_service_offered_url_'.$schema_id,
                                'type' => 'text',                            
                        );                           
                        $meta_field[] = array(
                             'label' => 'Price Range',
                            'id' => 'local_price_range_'.$schema_id,
                            'type' => 'text',                            
                        );                       
                        $meta_field[] = array(
                            'label' => 'Menu',
                            'id' => 'local_menu_'.$schema_id,
                            'type' => 'text',                            
                         );
                        $meta_field[] =  array(
                            'label' => 'HasMap',
                            'id' => 'local_hasmap_'.$schema_id,
                            'type' => 'text',                            
                         );
                        $meta_field[] =   array(
                            'label' => 'Serves Cuisine',
                            'id' => 'local_serves_cuisine_'.$schema_id,
                            'type' => 'text',                            
                         );
                        $meta_field[] =   array(
                                'label' => 'Additional Type',
                                'id'    => 'local_additional_type_'.$schema_id,
                                'type'  => 'text',                            
                        );
                        $meta_field[] = array(
                                'label'   => 'Founder',
                                'id'      => 'local_business_founder_'.$schema_id,
                                'type'    => 'textarea',  
                                'note'    => 'Note: If There are more than one founder, Separate founder list by comma ( , )'                                 
                       );
                       $meta_field[] = array(
                               'label'   => 'Employee',
                               'id'      => 'local_business_employee_'.$schema_id,
                               'type'    => 'textarea',
                               'note'    => 'Note: If There are more than one employee. Separate employee list by comma ( , )'                                   
                       );                                                
                        $meta_field[] =   array(
                            'label' => 'Facebook',
                            'id' => 'local_facebook_'.$schema_id,
                            'type' => 'text',                            
                         );
                        $meta_field[] =  array(
                            'label' => 'Twitter',
                            'id' => 'local_twitter_'.$schema_id,
                            'type' => 'text',                            
                        );
                        $meta_field[] =   array(
                            'label' => 'Instagram',
                            'id' => 'local_instagram_'.$schema_id,
                            'type' => 'text',                            
                        );
                        $meta_field[] =   array(
                            'label' => 'Pinterest',
                            'id' => 'local_pinterest_'.$schema_id,
                            'type' => 'text',                            
                        );
                        $meta_field[] =   array(
                            'label' => 'Linkedin',
                            'id' => 'local_linkedin_'.$schema_id,
                            'type' => 'text',                            
                        );
                        $meta_field[] =  array(
                            'label' => 'Soundcloud',
                            'id' => 'local_soundcloud_'.$schema_id,
                            'type' => 'text',                            
                        );
                        $meta_field[] =   array(
                            'label' => 'Tumblr',
                            'id' => 'local_tumblr_'.$schema_id,
                            'type' => 'text',                            
                        );
                        $meta_field[] =  array(
                            'label' => 'Youtube',
                            'id' => 'local_youtube_'.$schema_id,
                            'type' => 'text',                            
                        );
                        $meta_field[] =  array(
                            'label' => 'Threads',
                            'id' => 'local_threads_'.$schema_id,
                            'type' => 'text',                            
                        );
                        $meta_field[] =  array(
                            'label' => 'Mastodon',
                            'id' => 'local_mastodon_'.$schema_id,
                            'type' => 'text',                            
                        );
                        $meta_field[] =  array(
                            'label' => 'Vibehut',
                            'id' => 'local_vibehut_'.$schema_id,
                            'type' => 'text',                            
                        );                                                                                                                        
                        $meta_field[] =   array(
                            'label' => 'Aggregate Rating',
                            'id' => 'local_enable_rating_'.$schema_id,
                            'type' => 'checkbox',                          
                        );
                        
                        $meta_field = apply_filters('saswp_modify_local_business_properties', $meta_field, $schema_id);
                        
                        $meta_field[] =   array(
                            'label' => 'Rating',
                            'id' => 'local_rating_'.$schema_id,
                            'type' => 'text',                            
                        );
                        $meta_field[] =   array(
                            'label' => 'Number of Reviews',
                            'id' => 'local_review_count_'.$schema_id,
                            'type' => 'text',                            
                        );
                                           
                    break;
                
                case 'Blogposting':
                case 'BlogPosting':        
                    $meta_field = array(
                        array(
                        'label'      => 'ID',
                        'id'         => 'saswp_blogposting_id_'.$schema_id,
                        'type'       => 'text',
                        'default'    => 'BlogPosting'   
                        ),
                    array(
                        'label' => 'Main Entity Of Page',
                        'id' => 'saswp_blogposting_main_entity_of_page_'.$schema_id,
                        'type' => 'text',
                        'default' => get_permalink()
                    ),
                    array(
                        'label'   => 'inLanguage',
                        'id'      => 'saswp_blogposting_inlanguage_'.$schema_id,
                        'type'    => 'text',
                        'default' => get_bloginfo('language'),
                   ),
                    array(
                            'label' => 'Headline',
                            'id' => 'saswp_blogposting_headline_'.$schema_id,
                            'type' => 'text',
                            'default'=> saswp_get_the_title()
                    ),
                    array(
                            'label' => 'Description',
                            'id' => 'saswp_blogposting_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => saswp_strip_all_tags(get_the_excerpt())
                    ),                        
                    array(
                        'label'   => 'Article Body',
                        'id'      => 'saswp_blogposting_body_'.$schema_id,
                        'type'    => 'textarea',
                        'default' => is_object($post) ? saswp_strip_all_tags($post->post_content) : ''
                    ),
                    array(
                            'label' => 'Keywords',
                            'id' => 'saswp_blogposting_keywords_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_get_the_tags()
                    ),    
                    array(
                            'label' => 'Name',
                            'id' => 'saswp_blogposting_name_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_get_the_title()
                    ), 
                    array(
                            'label' => 'URL',
                            'id' => 'saswp_blogposting_url_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink()
                    ),
                    array(
                        'label'   => 'Image',
                        'id'      => 'saswp_blogposting_image_'.$schema_id,
                        'type'    => 'media'                        
                    ),
                    array(
                            'label' => 'Date Published',
                            'id' => 'saswp_blogposting_date_published_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_date("Y-m-d")
                    ), 
                    array(
                            'label' => 'Date Modified',
                            'id' => 'saswp_blogposting_date_modified_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_modified_date("Y-m-d")
                    ),     
                    array(
                        'label'   => 'Author',
                        'id'      => 'saswp_blogposting_author_global_mapping_'.$schema_id,
                        'type'    => 'global_mapping'
                    ),
                    array(
                        'label'   => 'Author Type',
                        'id'      => 'saswp_blogposting_author_type_'.$schema_id,
                        'type'    => 'select',
                        'options' => array(
                                'Person'           => 'Person',
                                'Organization'     => 'Organization',                        
                       )                        
                   ),
                    array(
                            'label' => 'Author Name',
                            'id' => 'saswp_blogposting_author_name_'.$schema_id,
                            'type' => 'text',
                            'default' => is_object($current_user) ? $current_user->display_name : ''
                    ),
                    array(
                        'label'   => 'Author HonorificSuffix',
                        'id'      => 'saswp_blogposting_author_honorific_suffix_'.$schema_id,
                        'type'    => 'text',
                        'attributes' => array(
                                'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                         ),
                    ),
                    array(
                            'label' => 'Author Description',
                            'id' => 'saswp_blogposting_author_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => $author_desc
                    ),
                    array(
                            'label'   => 'Author URL',
                            'id'      => 'saswp_blogposting_author_url_'.$schema_id,
                            'type'    => 'text',
                            'default' => $author_url
                    ),  
                    array(
                        'label' => 'Author Image URL',
                        'id' => 'saswp_blogposting_author_image_'.$schema_id,
                        'type' => 'media',
                        'default' => isset($author_details['url']) ? $author_details['url']: ''
                    ),
                    array(
                            'label' => 'Author Social Profile',
                            'id' => 'saswp_blogposting_author_social_profile_'.$schema_id,
                            'type' => 'textarea',
                            'default' => '',
                            'note'    => 'Note: If There are more than one social profiles, Separate them by comma ( , )',
                    ),
                    array(
                        'label'   => 'JobTitle',
                        'id'      => 'saswp_blogposting_author_jobtitle_'.$schema_id,
                        'type'    => 'text',
                        'default' => '',
                        'attributes' => array(
                                'placeholder' => 'eg: Editor in Chief'
                         ),
                     ),
                     array(
                        'label'   => 'ReviewedBy',
                        'id'      => 'saswp_blogposting_reviewedby_global_mapping_'.$schema_id,
                        'type'    => 'global_mapping'
                    ),
                    array(
                        'label'   => 'ReviewedBy Type',
                        'id'      => 'saswp_blogposting_reviewedby_type_'.$schema_id,
                        'type'    => 'select',
                        'options' => array(
                                ''                 => 'Select',
                                'Person'           => 'Person',
                                'Organization'     => 'Organization',                        
                       )                        
                    ),
                    array(
                            'label' => 'ReviewedBy Name',
                            'id' => 'saswp_blogposting_reviewedby_name_'.$schema_id,
                            'type' => 'text',
                            'default' => is_object($current_user) ? $current_user->display_name : ''
                    ),
                    array(
                        'label'   => 'ReviewedBy HonorificSuffix',
                        'id'      => 'saswp_blogposting_reviewedby_honorific_suffix_'.$schema_id,
                        'type'    => 'text',
                        'attributes' => array(
                                'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                         ),
                    ),
                    array(
                        'label' => 'ReviewedBy Description',
                        'id' => 'saswp_blogposting_reviewedby_description_'.$schema_id,
                        'type' => 'textarea',
                        'default' => $author_desc
                    ),
                    array(
                        'label'   => 'ReviewedBy URL',
                        'id'      => 'saswp_blogposting_reviewedby_url_'.$schema_id,
                        'type'    => 'text',
                        'default' => $author_url
                    ),

                    array(
                        'label'   => 'Editor Type',
                        'id'      => 'saswp_blogposting_editor_type_'.$schema_id,
                        'type'    => 'select',
                        'options' => array(
                                ""                => "Select",
                                'Person'           => 'Person',
                                'Organization'     => 'Organization',                        
                        )
                    ),
                    array(
                        'label'   => 'Editor Name',
                        'id'      => 'saswp_blogposting_editor_name_'.$schema_id,
                        'type'    => 'text',
                        'default' => is_object($current_user) ? $current_user->display_name : ''
                    ),  
                    array(
                        'label'   => 'Editor HonorificSuffix',
                        'id'      => 'saswp_blogposting_editor_honorific_suffix_'.$schema_id,
                        'type'    => 'text',
                        'attributes' => array(
                                'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                         ),
                    ),
                    array(
                        'label'   => 'Editor Description',
                        'id'      => 'saswp_blogposting_editor_description_'.$schema_id,
                        'type'    => 'textarea',
                        'default' => $author_desc
                    ),
                    array(
                        'label'   => 'Editor URL',
                        'id'      => 'saswp_blogposting_editor_url_'.$schema_id,
                        'type'    => 'text',
                        'default' => $author_url
                    ),
                    array(
                        'label' => 'Editor Image URL',
                        'id' => 'saswp_blogposting_editor_image_'.$schema_id,
                        'type' => 'media',
                        'default' => isset($author_details['url']) ? $author_details['url']: ''
                    ),


                    array(
                            'label' => 'Organization Name',
                            'id' => 'saswp_blogposting_organization_name_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                   ),
                     array(
                            'label' => 'Organization Logo',
                            'id' => 'saswp_blogposting_organization_logo_'.$schema_id,
                            'type' => 'media',
                            'default' => isset($sd_data['sd_logo']) ? $sd_data['sd_logo']['url'] : ''
                    ),
                    array(
                        'label'   => 'About',
                        'id'      => 'saswp_blogposting_about_'.$schema_id,
                        'type'    => 'textarea',
                        'default' => '',
                        'note'    => 'Note: If There are more than one About, Separate About list by comma ( , )',
                        'attributes' => array(
                                'placeholder' => 'eg: Apple is March 21 Announcements'
                        ),
                    ),  
                    array(
                        'label'   => 'AlumniOf',
                        'id'      => 'saswp_blogposting_alumniof_'.$schema_id,
                        'type'    => 'textarea',
                        'default' => '',
                        'note'    => 'Note: If There are more than one AlumniOf, Separate AlumniOf list by comma ( , )',
                        'attributes' => array(
                                'placeholder' => 'eg: City University of New York-Herbert H. Lehman College, Southern New Hampshire University'
                        ),
                    ),  
                    array(
                        'label'   => 'knowsAbout',
                        'id'      => 'saswp_blogposting_knowsabout_'.$schema_id,
                        'type'    => 'textarea',
                        'default' => '',
                        'note'    => 'Note: If There are more than one knows about, Separate knows about list by comma ( , )'       
                    ),
                    array(
                        'label' => 'Speakable',
                        'id' => 'saswp_blogposting_speakable_'.$schema_id,
                        'type' => 'checkbox',

                    )                        
                    );
                    break;
                
                case 'NewsArticle':
                    
                    $category_detail=get_the_category(get_the_ID());//$post->ID
                    $article_section = '';
                    
                    foreach($category_detail as $cd){
                        
                    $article_section =  $cd->cat_name;
                    
                    }
                    $word_count = saswp_reading_time_and_word_count();
                    
                    $meta_field = array(
                        array(
                                'label'      => 'ID',
                                'id'         => 'saswp_newsarticle_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'newsarticle'   
                        ),  
                    array(
                            'label' => 'Main Entity Of Page',
                            'id' => 'saswp_newsarticle_main_entity_of_page_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink()
                    ),
                    array(
                            'label' => 'URL',
                            'id' => 'saswp_newsarticle_URL_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink(),
                    ),
                    array(
                            'label' => 'Image',
                            'id' => 'saswp_newsarticle_image_'.$schema_id,
                            'type' => 'media',                            
                    ),    
                    array(
                        'label'   => 'inLanguage',
                        'id'      => 'saswp_newsarticle_inlanguage_'.$schema_id,
                        'type'    => 'text',
                        'default' => get_bloginfo('language'),
                    ),
                    array(
                            'label' => 'Headline',
                            'id' => 'saswp_newsarticle_headline_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_get_the_title(),
                    ),
                    array(
                        'label'   => 'Alternative Headline',
                        'id'      => 'saswp_newsarticle_alternative_headline_'.$schema_id,
                        'type'    => 'text',
                        'default' => saswp_get_the_title(),
                    ),
                    array(
                            'label' => 'Date Published',
                            'id' => 'saswp_newsarticle_date_published_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_date("Y-m-d")
                    ),
                    array(
                            'label' => 'Date Modified',
                            'id' => 'saswp_newsarticle_date_modified_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_modified_date("Y-m-d")
                    ),
                     array(
                            'label' => 'Description',
                            'id' => 'saswp_newsarticle_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => saswp_strip_all_tags(get_the_excerpt())
                    ),
                    array(
                            'label' => 'Keywords',
                            'id' => 'saswp_newsarticle_keywords_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_get_the_tags()
                    ),    
                     array(
                            'label' => 'Article Section',
                            'id' => 'saswp_newsarticle_section_'.$schema_id,
                            'type' => 'text',
                            'default' => $article_section
                    ),
                    array(
                            'label' => 'Article Body',
                            'id' => 'saswp_newsarticle_body_'.$schema_id,
                            'type' => 'textarea',
                            'default' => is_object($post) ? saswp_strip_all_tags($post->post_content) : ''
                    ),
                     array(
                            'label' => 'Name',
                            'id' => 'saswp_newsarticle_name_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_get_the_title()
                    ), 
                     array(
                            'label' => 'Thumbnail URL',
                            'id' => 'saswp_newsarticle_thumbnailurl_'.$schema_id,
                            'type' => 'text'                            
                    ),
                    array(
                            'label' => 'Word Count',
                            'id' => 'saswp_newsarticle_word_count_'.$schema_id,
                            'type' => 'text',
                            'default' => $word_count['word_count']
                    ),
                    array(
                            'label' => 'Time Required',
                            'id' => 'saswp_newsarticle_timerequired_'.$schema_id,
                            'type' => 'text',
                            'default' => $word_count['timerequired']
                    ),    
                    array(
                            'label' => 'Main Entity Id',
                            'id' => 'saswp_newsarticle_main_entity_id_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink()
                    ),
                    array(
                        'label'   => 'Author Type',
                        'id'      => 'saswp_newsarticle_author_type_'.$schema_id,
                        'type'    => 'select',
                        'options' => array(
                                'Person'           => 'Person',
                                'Organization'     => 'Organization',                        
                       )
                    ),
                    array(
                            'label' => 'Author Name',
                            'id' => 'saswp_newsarticle_author_name_'.$schema_id,
                            'type' => 'text',
                            'default' => is_object($current_user) ?  $current_user->display_name : ''
                    ),
                    array(
                            'label' => 'Author Description',
                            'id' => 'saswp_newsarticle_author_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => $author_desc
                    ), 
                    array(
                            'label'   => 'Author URL',
                            'id'      => 'saswp_newsarticle_author_url_'.$schema_id,
                            'type'    => 'text',
                            'default' => $author_url
                    ),    
                    array(
                            'label' => 'Author Image',
                            'id' => 'saswp_newsarticle_author_image_'.$schema_id,
                            'type' => 'media',
                            'default' => isset($author_details['url']) ? $author_details['url']: ''
                    ),
                    array(
                            'label' => 'Author Social Profile',
                            'id' => 'saswp_newsarticle_author_social_profile_'.$schema_id,
                            'type' => 'textarea',
                            'default' => '',
                            'note'    => 'Note: If There are more than one social profiles, Separate them by comma ( , )',
                    ),

                    array(
                        'label'   => 'Editor Type',
                        'id'      => 'saswp_newsarticle_editor_type_'.$schema_id,
                        'type'    => 'select',
                        'options' => array(
                                ""                => "Select",
                                'Person'           => 'Person',
                                'Organization'     => 'Organization',                        
                        )
                    ),
                    array(
                        'label'   => 'Editor Name',
                        'id'      => 'saswp_newsarticle_editor_name_'.$schema_id,
                        'type'    => 'text',
                        'default' => is_object($current_user) ? $current_user->display_name : ''
                    ),
                    array(
                        'label'   => 'Editor HonorificSuffix',
                        'id'      => 'saswp_newsarticle_editor_honorific_suffix_'.$schema_id,
                        'type'    => 'text',
                        'attributes' => array(
                                'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                         ),
                    ), 
                    array(
                        'label'   => 'Editor Description',
                        'id'      => 'saswp_newsarticle_editor_description_'.$schema_id,
                        'type'    => 'textarea',
                        'default' => $author_desc
                    ),
                    array(
                        'label'   => 'Editor URL',
                        'id'      => 'saswp_newsarticle_editor_url_'.$schema_id,
                        'type'    => 'text',
                        'default' => $author_url
                    ),
                    array(
                        'label' => 'Editor Image URL',
                        'id' => 'saswp_newsarticle_editor_image_'.$schema_id,
                        'type' => 'media',
                        'default' => isset($author_details['url']) ? $author_details['url']: ''
                    ),
                    array(
                        'label'   => 'About',
                        'id'      => 'saswp_newsarticle_about_'.$schema_id,
                        'type'    => 'textarea',
                        'default' => '',
                        'note'    => 'Note: If There are more than one About, Separate About list by comma ( , )',
                        'attributes' => array(
                                'placeholder' => 'eg: Apple is March 21 Announcements'
                        ),
                    ), 
                    array(
                            'label' => 'Organization Name',
                            'id' => 'saswp_newsarticle_organization_name_'.$schema_id,
                            'type' => 'text',
                            'default'=> saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                    ),
                    array(
                            'label' => 'Organization Logo',
                            'id' => 'saswp_newsarticle_organization_logo_'.$schema_id,
                            'type' => 'media',
                            'default' => isset($sd_data['sd_logo'])? $sd_data['sd_logo']['url']:''
                    ),                         
                    array(
                        'label' => 'Speakable',
                        'id' => 'saswp_newsarticle_speakable_'.$schema_id,
                        'type' => 'checkbox',

                    )                        
                    );
                    break;

                    case 'AnalysisNewsArticle':
                    
                        $category_detail=get_the_category(get_the_ID());//$post->ID
                        $article_section = '';
                        
                        foreach($category_detail as $cd){
                            
                        $article_section =  $cd->cat_name;
                        
                        }
                        $word_count = saswp_reading_time_and_word_count();
                        
                        $meta_field = array(
                            array(
                                    'label'      => 'ID',
                                    'id'         => 'saswp_analysisnewsarticle_id_'.$schema_id,
                                    'type'       => 'text',
                                    'default'    => 'analysisnewsarticle'   
                            ),  
                        array(
                                'label' => 'Main Entity Of Page',
                                'id' => 'saswp_analysisnewsarticle_main_entity_of_page_'.$schema_id,
                                'type' => 'text',
                                'default' => get_permalink()
                        ),
                        array(
                                'label' => 'URL',
                                'id' => 'saswp_analysisnewsarticle_URL_'.$schema_id,
                                'type' => 'text',
                                'default' => get_permalink(),
                        ),
                        array(
                                'label' => 'Image',
                                'id' => 'saswp_analysisnewsarticle_image_'.$schema_id,
                                'type' => 'media',                            
                        ),    
                        array(
                            'label'   => 'inLanguage',
                            'id'      => 'saswp_analysisnewsarticle_inlanguage_'.$schema_id,
                            'type'    => 'text',
                            'default' => get_bloginfo('language'),
                        ),
                        array(
                                'label' => 'Headline',
                                'id' => 'saswp_analysisnewsarticle_headline_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_title(),
                        ),
                        array(
                                'label' => 'Date Published',
                                'id' => 'saswp_analysisnewsarticle_date_published_'.$schema_id,
                                'type' => 'text',
                                'default' => get_the_date("Y-m-d")
                        ),
                        array(
                                'label' => 'Date Modified',
                                'id' => 'saswp_analysisnewsarticle_date_modified_'.$schema_id,
                                'type' => 'text',
                                'default' => get_the_modified_date("Y-m-d")
                        ),
                         array(
                                'label' => 'Description',
                                'id' => 'saswp_analysisnewsarticle_description_'.$schema_id,
                                'type' => 'textarea',
                                'default' => saswp_strip_all_tags(get_the_excerpt())
                        ),
                        array(
                                'label' => 'Keywords',
                                'id' => 'saswp_analysisnewsarticle_keywords_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_tags()
                        ),    
                         array(
                                'label' => 'Article Section',
                                'id' => 'saswp_analysisnewsarticle_section_'.$schema_id,
                                'type' => 'text',
                                'default' => $article_section
                        ),
                        array(
                                'label' => 'Article Body',
                                'id' => 'saswp_analysisnewsarticle_body_'.$schema_id,
                                'type' => 'textarea',
                                'default' => is_object($post) ? saswp_strip_all_tags($post->post_content) : ''
                        ),
                         array(
                                'label' => 'Name',
                                'id' => 'saswp_analysisnewsarticle_name_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_title()
                        ), 
                         array(
                                'label' => 'Thumbnail URL',
                                'id' => 'saswp_analysisnewsarticle_thumbnailurl_'.$schema_id,
                                'type' => 'text'                            
                        ),
                        array(
                                'label' => 'Word Count',
                                'id' => 'saswp_analysisnewsarticle_word_count_'.$schema_id,
                                'type' => 'text',
                                'default' => $word_count['word_count']
                        ),
                        array(
                                'label' => 'Time Required',
                                'id' => 'saswp_analysisnewsarticle_timerequired_'.$schema_id,
                                'type' => 'text',
                                'default' => $word_count['timerequired']
                        ),    
                        array(
                                'label' => 'Main Entity Id',
                                'id' => 'saswp_analysisnewsarticle_main_entity_id_'.$schema_id,
                                'type' => 'text',
                                'default' => get_permalink()
                        ),
                        array(
                            'label'   => 'Author Type',
                            'id'      => 'saswp_analysisnewsarticle_author_type_'.$schema_id,
                            'type'    => 'select',
                            'options' => array(
                                    'Person'           => 'Person',
                                    'Organization'     => 'Organization',                        
                           )
                        ),
                        array(
                                'label' => 'Author Name',
                                'id' => 'saswp_analysisnewsarticle_author_name_'.$schema_id,
                                'type' => 'text',
                                'default' => is_object($current_user) ?  $current_user->display_name : ''
                        ),
                        array(
                                'label' => 'Author Description',
                                'id' => 'saswp_analysisnewsarticle_author_description_'.$schema_id,
                                'type' => 'textarea',
                                'default' => $author_desc
                        ), 
                        array(
                                'label'   => 'Author URL',
                                'id'      => 'saswp_analysisnewsarticle_author_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => $author_url
                        ),    
                        array(
                                'label' => 'Author Image',
                                'id' => 'saswp_analysisnewsarticle_author_image_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($author_details['url']) ? $author_details['url']: ''
                        ),
    
                        array(
                            'label'   => 'Editor Type',
                            'id'      => 'saswp_analysisnewsarticle_editor_type_'.$schema_id,
                            'type'    => 'select',
                            'options' => array(
                                    ""                => "Select",
                                    'Person'           => 'Person',
                                    'Organization'     => 'Organization',                        
                            )
                        ),
                        array(
                            'label'   => 'Editor Name',
                            'id'      => 'saswp_analysisnewsarticle_editor_name_'.$schema_id,
                            'type'    => 'text',
                            'default' => is_object($current_user) ? $current_user->display_name : ''
                        ),
                        array(
                            'label'   => 'Editor HonorificSuffix',
                            'id'      => 'saswp_analysisnewsarticle_editor_honorific_suffix_'.$schema_id,
                            'type'    => 'text',
                            'attributes' => array(
                                    'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                             ),
                        ), 
                        array(
                            'label'   => 'Editor Description',
                            'id'      => 'saswp_analysisnewsarticle_editor_description_'.$schema_id,
                            'type'    => 'textarea',
                            'default' => $author_desc
                        ),
                        array(
                            'label'   => 'Editor URL',
                            'id'      => 'saswp_analysisnewsarticle_editor_url_'.$schema_id,
                            'type'    => 'text',
                            'default' => $author_url
                        ),
                        array(
                            'label' => 'Editor Image URL',
                            'id' => 'saswp_analysisnewsarticle_editor_image_'.$schema_id,
                            'type' => 'media',
                            'default' => isset($author_details['url']) ? $author_details['url']: ''
                        ),
                        array(
                            'label'   => 'About',
                            'id'      => 'saswp_analysisnewsarticle_about_'.$schema_id,
                            'type'    => 'textarea',
                            'default' => '',
                            'note'    => 'Note: If There are more than one About, Separate About list by comma ( , )',
                            'attributes' => array(
                                    'placeholder' => 'eg: Apple is March 21 Announcements'
                            ),
                        ), 
                        array(
                                'label' => 'Organization Name',
                                'id' => 'saswp_analysisnewsarticle_organization_name_'.$schema_id,
                                'type' => 'text',
                                'default'=> saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                        ),
                        array(
                                'label' => 'Organization Logo',
                                'id' => 'saswp_analysisnewsarticle_organization_logo_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($sd_data['sd_logo'])? $sd_data['sd_logo']['url']:''
                        ),                         
                        array(
                            'label' => 'Speakable',
                            'id' => 'saswp_analysisnewsarticle_speakable_'.$schema_id,
                            'type' => 'checkbox',
    
                        )                        
                        );
                        break;

                        case 'AskPublicNewsArticle':
                    
                        $category_detail=get_the_category(get_the_ID());//$post->ID
                        $article_section = '';
                        
                        foreach($category_detail as $cd){
                                
                        $article_section =  $cd->cat_name;
                        
                        }
                        $word_count = saswp_reading_time_and_word_count();
                        
                        $meta_field = array(
                                array(
                                        'label'      => 'ID',
                                        'id'         => 'saswp_askpublicnewsarticle_id_'.$schema_id,
                                        'type'       => 'text',
                                        'default'    => 'askpublicnewsarticle'   
                                ),  
                        array(
                                'label' => 'Main Entity Of Page',
                                'id' => 'saswp_askpublicnewsarticle_main_entity_of_page_'.$schema_id,
                                'type' => 'text',
                                'default' => get_permalink()
                        ),
                        array(
                                'label' => 'URL',
                                'id' => 'saswp_askpublicnewsarticle_URL_'.$schema_id,
                                'type' => 'text',
                                'default' => get_permalink(),
                        ),
                        array(
                                'label' => 'Image',
                                'id' => 'saswp_askpublicnewsarticle_image_'.$schema_id,
                                'type' => 'media',                            
                        ),    
                        array(
                                'label'   => 'inLanguage',
                                'id'      => 'saswp_askpublicnewsarticle_inlanguage_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_bloginfo('language'),
                        ),
                        array(
                                'label' => 'Headline',
                                'id' => 'saswp_askpublicnewsarticle_headline_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_title(),
                        ),
                        array(
                                'label' => 'Date Published',
                                'id' => 'saswp_askpublicnewsarticle_date_published_'.$schema_id,
                                'type' => 'text',
                                'default' => get_the_date("Y-m-d")
                        ),
                        array(
                                'label' => 'Date Modified',
                                'id' => 'saswp_askpublicnewsarticle_date_modified_'.$schema_id,
                                'type' => 'text',
                                'default' => get_the_modified_date("Y-m-d")
                        ),
                                array(
                                'label' => 'Description',
                                'id' => 'saswp_askpublicnewsarticle_description_'.$schema_id,
                                'type' => 'textarea',
                                'default' => saswp_strip_all_tags(get_the_excerpt())
                        ),
                        array(
                                'label' => 'Keywords',
                                'id' => 'saswp_askpublicnewsarticle_keywords_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_tags()
                        ),    
                                array(
                                'label' => 'Article Section',
                                'id' => 'saswp_askpublicnewsarticle_section_'.$schema_id,
                                'type' => 'text',
                                'default' => $article_section
                        ),
                        array(
                                'label' => 'Article Body',
                                'id' => 'saswp_askpublicnewsarticle_body_'.$schema_id,
                                'type' => 'textarea',
                                'default' => is_object($post) ? saswp_strip_all_tags($post->post_content) : ''
                        ),
                                array(
                                'label' => 'Name',
                                'id' => 'saswp_askpublicnewsarticle_name_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_title()
                        ), 
                                array(
                                'label' => 'Thumbnail URL',
                                'id' => 'saswp_askpublicnewsarticle_thumbnailurl_'.$schema_id,
                                'type' => 'text'                            
                        ),
                        array(
                                'label' => 'Word Count',
                                'id' => 'saswp_askpublicnewsarticle_word_count_'.$schema_id,
                                'type' => 'text',
                                'default' => $word_count['word_count']
                        ),
                        array(
                                'label' => 'Time Required',
                                'id' => 'saswp_askpublicnewsarticle_timerequired_'.$schema_id,
                                'type' => 'text',
                                'default' => $word_count['timerequired']
                        ),    
                        array(
                                'label' => 'Main Entity Id',
                                'id' => 'saswp_askpublicnewsarticle_main_entity_id_'.$schema_id,
                                'type' => 'text',
                                'default' => get_permalink()
                        ),
                        array(
                                'label'   => 'Author Type',
                                'id'      => 'saswp_askpublicnewsarticle_author_type_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                        'Person'           => 'Person',
                                        'Organization'     => 'Organization',                        
                                )
                        ),
                        array(
                                'label' => 'Author Name',
                                'id' => 'saswp_askpublicnewsarticle_author_name_'.$schema_id,
                                'type' => 'text',
                                'default' => is_object($current_user) ?  $current_user->display_name : ''
                        ),
                        array(
                                'label' => 'Author Description',
                                'id' => 'saswp_askpublicnewsarticle_author_description_'.$schema_id,
                                'type' => 'textarea',
                                'default' => $author_desc
                        ), 
                        array(
                                'label'   => 'Author URL',
                                'id'      => 'saswp_askpublicnewsarticle_author_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => $author_url
                        ),    
                        array(
                                'label' => 'Author Image',
                                'id' => 'saswp_askpublicnewsarticle_author_image_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($author_details['url']) ? $author_details['url']: ''
                        ),
        
                        array(
                                'label'   => 'Editor Type',
                                'id'      => 'saswp_askpublicnewsarticle_editor_type_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                        ""                => "Select",
                                        'Person'           => 'Person',
                                        'Organization'     => 'Organization',                        
                                )
                        ),
                        array(
                                'label'   => 'Editor Name',
                                'id'      => 'saswp_askpublicnewsarticle_editor_name_'.$schema_id,
                                'type'    => 'text',
                                'default' => is_object($current_user) ? $current_user->display_name : ''
                        ),
                        array(
                                'label'   => 'Editor HonorificSuffix',
                                'id'      => 'saswp_askpublicnewsarticle_editor_honorific_suffix_'.$schema_id,
                                'type'    => 'text',
                                'attributes' => array(
                                        'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                                ),
                        ), 
                        array(
                                'label'   => 'Editor Description',
                                'id'      => 'saswp_askpublicnewsarticle_editor_description_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => $author_desc
                        ),
                        array(
                                'label'   => 'Editor URL',
                                'id'      => 'saswp_askpublicnewsarticle_editor_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => $author_url
                        ),
                        array(
                                'label' => 'Editor Image URL',
                                'id' => 'saswp_askpublicnewsarticle_editor_image_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($author_details['url']) ? $author_details['url']: ''
                        ),
                        array(
                                'label'   => 'About',
                                'id'      => 'saswp_askpublicnewsarticle_about_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => '',
                                'note'    => 'Note: If There are more than one About, Separate About list by comma ( , )',
                                'attributes' => array(
                                        'placeholder' => 'eg: Apple is March 21 Announcements'
                                ),
                        ), 
                        array(
                                'label' => 'Organization Name',
                                'id' => 'saswp_askpublicnewsarticle_organization_name_'.$schema_id,
                                'type' => 'text',
                                'default'=> saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                        ),
                        array(
                                'label' => 'Organization Logo',
                                'id' => 'saswp_askpublicnewsarticle_organization_logo_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($sd_data['sd_logo'])? $sd_data['sd_logo']['url']:''
                        ),                         
                        array(
                                'label' => 'Speakable',
                                'id' => 'saswp_askpublicnewsarticle_speakable_'.$schema_id,
                                'type' => 'checkbox',
        
                        )                        
                        );
                        break;

                        case 'BackgroundNewsArticle':
                
                        $category_detail=get_the_category(get_the_ID());//$post->ID
                        $article_section = '';
                        
                        foreach($category_detail as $cd){
                                
                        $article_section =  $cd->cat_name;
                        
                        }
                        $word_count = saswp_reading_time_and_word_count();
                        
                        $meta_field = array(
                                array(
                                        'label'      => 'ID',
                                        'id'         => 'saswp_backgroundnewsarticle_id_'.$schema_id,
                                        'type'       => 'text',
                                        'default'    => 'backgroundnewsarticle'   
                                ),  
                        array(
                                'label' => 'Main Entity Of Page',
                                'id' => 'saswp_backgroundnewsarticle_main_entity_of_page_'.$schema_id,
                                'type' => 'text',
                                'default' => get_permalink()
                        ),
                        array(
                                'label' => 'URL',
                                'id' => 'saswp_backgroundnewsarticle_URL_'.$schema_id,
                                'type' => 'text',
                                'default' => get_permalink(),
                        ),
                        array(
                                'label' => 'Image',
                                'id' => 'saswp_backgroundnewsarticle_image_'.$schema_id,
                                'type' => 'media',                            
                        ),    
                        array(
                                'label'   => 'inLanguage',
                                'id'      => 'saswp_backgroundnewsarticle_inlanguage_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_bloginfo('language'),
                        ),
                        array(
                                'label' => 'Headline',
                                'id' => 'saswp_backgroundnewsarticle_headline_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_title(),
                        ),
                        array(
                                'label' => 'Date Published',
                                'id' => 'saswp_backgroundnewsarticle_date_published_'.$schema_id,
                                'type' => 'text',
                                'default' => get_the_date("Y-m-d")
                        ),
                        array(
                                'label' => 'Date Modified',
                                'id' => 'saswp_backgroundnewsarticle_date_modified_'.$schema_id,
                                'type' => 'text',
                                'default' => get_the_modified_date("Y-m-d")
                        ),
                                array(
                                'label' => 'Description',
                                'id' => 'saswp_backgroundnewsarticle_description_'.$schema_id,
                                'type' => 'textarea',
                                'default' => saswp_strip_all_tags(get_the_excerpt())
                        ),
                        array(
                                'label' => 'Keywords',
                                'id' => 'saswp_backgroundnewsarticle_keywords_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_tags()
                        ),    
                                array(
                                'label' => 'Article Section',
                                'id' => 'saswp_backgroundnewsarticle_section_'.$schema_id,
                                'type' => 'text',
                                'default' => $article_section
                        ),
                        array(
                                'label' => 'Article Body',
                                'id' => 'saswp_backgroundnewsarticle_body_'.$schema_id,
                                'type' => 'textarea',
                                'default' => is_object($post) ? saswp_strip_all_tags($post->post_content) : ''
                        ),
                                array(
                                'label' => 'Name',
                                'id' => 'saswp_backgroundnewsarticle_name_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_title()
                        ), 
                                array(
                                'label' => 'Thumbnail URL',
                                'id' => 'saswp_backgroundnewsarticle_thumbnailurl_'.$schema_id,
                                'type' => 'text'                            
                        ),
                        array(
                                'label' => 'Word Count',
                                'id' => 'saswp_backgroundnewsarticle_word_count_'.$schema_id,
                                'type' => 'text',
                                'default' => $word_count['word_count']
                        ),
                        array(
                                'label' => 'Time Required',
                                'id' => 'saswp_backgroundnewsarticle_timerequired_'.$schema_id,
                                'type' => 'text',
                                'default' => $word_count['timerequired']
                        ),    
                        array(
                                'label' => 'Main Entity Id',
                                'id' => 'saswp_backgroundnewsarticle_main_entity_id_'.$schema_id,
                                'type' => 'text',
                                'default' => get_permalink()
                        ),
                        array(
                                'label'   => 'Author Type',
                                'id'      => 'saswp_backgroundnewsarticle_author_type_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                        'Person'           => 'Person',
                                        'Organization'     => 'Organization',                        
                                )
                        ),
                        array(
                                'label' => 'Author Name',
                                'id' => 'saswp_backgroundnewsarticle_author_name_'.$schema_id,
                                'type' => 'text',
                                'default' => is_object($current_user) ?  $current_user->display_name : ''
                        ),
                        array(
                                'label' => 'Author Description',
                                'id' => 'saswp_backgroundnewsarticle_author_description_'.$schema_id,
                                'type' => 'textarea',
                                'default' => $author_desc
                        ), 
                        array(
                                'label'   => 'Author URL',
                                'id'      => 'saswp_backgroundnewsarticle_author_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => $author_url
                        ),    
                        array(
                                'label' => 'Author Image',
                                'id' => 'saswp_backgroundnewsarticle_author_image_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($author_details['url']) ? $author_details['url']: ''
                        ),
        
                        array(
                                'label'   => 'Editor Type',
                                'id'      => 'saswp_backgroundnewsarticle_editor_type_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                        ""                => "Select",
                                        'Person'           => 'Person',
                                        'Organization'     => 'Organization',                        
                                )
                        ),
                        array(
                                'label'   => 'Editor Name',
                                'id'      => 'saswp_backgroundnewsarticle_editor_name_'.$schema_id,
                                'type'    => 'text',
                                'default' => is_object($current_user) ? $current_user->display_name : ''
                        ),
                        array(
                                'label'   => 'Editor HonorificSuffix',
                                'id'      => 'saswp_backgroundnewsarticle_editor_honorific_suffix_'.$schema_id,
                                'type'    => 'text',
                                'attributes' => array(
                                        'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                                ),
                        ), 
                        array(
                                'label'   => 'Editor Description',
                                'id'      => 'saswp_backgroundnewsarticle_editor_description_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => $author_desc
                        ),
                        array(
                                'label'   => 'Editor URL',
                                'id'      => 'saswp_backgroundnewsarticle_editor_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => $author_url
                        ),
                        array(
                                'label' => 'Editor Image URL',
                                'id' => 'saswp_backgroundnewsarticle_editor_image_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($author_details['url']) ? $author_details['url']: ''
                        ),
                        array(
                                'label'   => 'About',
                                'id'      => 'saswp_backgroundnewsarticle_about_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => '',
                                'note'    => 'Note: If There are more than one About, Separate About list by comma ( , )',
                                'attributes' => array(
                                        'placeholder' => 'eg: Apple is March 21 Announcements'
                                ),
                        ), 
                        array(
                                'label' => 'Organization Name',
                                'id' => 'saswp_backgroundnewsarticle_organization_name_'.$schema_id,
                                'type' => 'text',
                                'default'=> saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                        ),
                        array(
                                'label' => 'Organization Logo',
                                'id' => 'saswp_backgroundnewsarticle_organization_logo_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($sd_data['sd_logo'])? $sd_data['sd_logo']['url']:''
                        ),                         
                        array(
                                'label' => 'Speakable',
                                'id' => 'saswp_backgroundnewsarticle_speakable_'.$schema_id,
                                'type' => 'checkbox',
        
                        )                        
                        );
                        break;

                        case 'OpinionNewsArticle':
                
                        $category_detail=get_the_category(get_the_ID());//$post->ID
                        $article_section = '';
                        
                        foreach($category_detail as $cd){
                                
                        $article_section =  $cd->cat_name;
                        
                        }
                        $word_count = saswp_reading_time_and_word_count();
                        
                        $meta_field = array(
                                array(
                                        'label'      => 'ID',
                                        'id'         => 'saswp_opinionnewsarticle_id_'.$schema_id,
                                        'type'       => 'text',
                                        'default'    => 'opinionnewsarticle'   
                                ),  
                        array(
                                'label' => 'Main Entity Of Page',
                                'id' => 'saswp_opinionnewsarticle_main_entity_of_page_'.$schema_id,
                                'type' => 'text',
                                'default' => get_permalink()
                        ),
                        array(
                                'label' => 'URL',
                                'id' => 'saswp_opinionnewsarticle_URL_'.$schema_id,
                                'type' => 'text',
                                'default' => get_permalink(),
                        ),
                        array(
                                'label' => 'Image',
                                'id' => 'saswp_opinionnewsarticle_image_'.$schema_id,
                                'type' => 'media',                            
                        ),    
                        array(
                                'label'   => 'inLanguage',
                                'id'      => 'saswp_opinionnewsarticle_inlanguage_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_bloginfo('language'),
                        ),
                        array(
                                'label' => 'Headline',
                                'id' => 'saswp_opinionnewsarticle_headline_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_title(),
                        ),
                        array(
                                'label' => 'Date Published',
                                'id' => 'saswp_opinionnewsarticle_date_published_'.$schema_id,
                                'type' => 'text',
                                'default' => get_the_date("Y-m-d")
                        ),
                        array(
                                'label' => 'Date Modified',
                                'id' => 'saswp_opinionnewsarticle_date_modified_'.$schema_id,
                                'type' => 'text',
                                'default' => get_the_modified_date("Y-m-d")
                        ),
                                array(
                                'label' => 'Description',
                                'id' => 'saswp_opinionnewsarticle_description_'.$schema_id,
                                'type' => 'textarea',
                                'default' => saswp_strip_all_tags(get_the_excerpt())
                        ),
                        array(
                                'label' => 'Keywords',
                                'id' => 'saswp_opinionnewsarticle_keywords_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_tags()
                        ),    
                                array(
                                'label' => 'Article Section',
                                'id' => 'saswp_opinionnewsarticle_section_'.$schema_id,
                                'type' => 'text',
                                'default' => $article_section
                        ),
                        array(
                                'label' => 'Article Body',
                                'id' => 'saswp_opinionnewsarticle_body_'.$schema_id,
                                'type' => 'textarea',
                                'default' => is_object($post) ? saswp_strip_all_tags($post->post_content) : ''
                        ),
                                array(
                                'label' => 'Name',
                                'id' => 'saswp_opinionnewsarticle_name_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_title()
                        ), 
                                array(
                                'label' => 'Thumbnail URL',
                                'id' => 'saswp_opinionnewsarticle_thumbnailurl_'.$schema_id,
                                'type' => 'text'                            
                        ),
                        array(
                                'label' => 'Word Count',
                                'id' => 'saswp_opinionnewsarticle_word_count_'.$schema_id,
                                'type' => 'text',
                                'default' => $word_count['word_count']
                        ),
                        array(
                                'label' => 'Time Required',
                                'id' => 'saswp_opinionnewsarticle_timerequired_'.$schema_id,
                                'type' => 'text',
                                'default' => $word_count['timerequired']
                        ),    
                        array(
                                'label' => 'Main Entity Id',
                                'id' => 'saswp_opinionnewsarticle_main_entity_id_'.$schema_id,
                                'type' => 'text',
                                'default' => get_permalink()
                        ),
                        array(
                                'label'   => 'Author Type',
                                'id'      => 'saswp_opinionnewsarticle_author_type_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                        'Person'           => 'Person',
                                        'Organization'     => 'Organization',                        
                                )
                        ),
                        array(
                                'label' => 'Author Name',
                                'id' => 'saswp_opinionnewsarticle_author_name_'.$schema_id,
                                'type' => 'text',
                                'default' => is_object($current_user) ?  $current_user->display_name : ''
                        ),
                        array(
                                'label' => 'Author Description',
                                'id' => 'saswp_opinionnewsarticle_author_description_'.$schema_id,
                                'type' => 'textarea',
                                'default' => $author_desc
                        ), 
                        array(
                                'label'   => 'Author URL',
                                'id'      => 'saswp_opinionnewsarticle_author_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => $author_url
                        ),    
                        array(
                                'label' => 'Author Image',
                                'id' => 'saswp_opinionnewsarticle_author_image_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($author_details['url']) ? $author_details['url']: ''
                        ),
        
                        array(
                                'label'   => 'Editor Type',
                                'id'      => 'saswp_opinionnewsarticle_editor_type_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                        ""                => "Select",
                                        'Person'           => 'Person',
                                        'Organization'     => 'Organization',                        
                                )
                        ),
                        array(
                                'label'   => 'Editor Name',
                                'id'      => 'saswp_opinionnewsarticle_editor_name_'.$schema_id,
                                'type'    => 'text',
                                'default' => is_object($current_user) ? $current_user->display_name : ''
                        ),
                        array(
                                'label'   => 'Editor HonorificSuffix',
                                'id'      => 'saswp_opinionnewsarticle_editor_honorific_suffix_'.$schema_id,
                                'type'    => 'text',
                                'attributes' => array(
                                        'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                                ),
                        ), 
                        array(
                                'label'   => 'Editor Description',
                                'id'      => 'saswp_opinionnewsarticle_editor_description_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => $author_desc
                        ),
                        array(
                                'label'   => 'Editor URL',
                                'id'      => 'saswp_opinionnewsarticle_editor_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => $author_url
                        ),
                        array(
                                'label' => 'Editor Image URL',
                                'id' => 'saswp_opinionnewsarticle_editor_image_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($author_details['url']) ? $author_details['url']: ''
                        ),
                        array(
                                'label'   => 'About',
                                'id'      => 'saswp_opinionnewsarticle_about_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => '',
                                'note'    => 'Note: If There are more than one About, Separate About list by comma ( , )',
                                'attributes' => array(
                                        'placeholder' => 'eg: Apple is March 21 Announcements'
                                ),
                        ), 
                        array(
                                'label' => 'Organization Name',
                                'id' => 'saswp_opinionnewsarticle_organization_name_'.$schema_id,
                                'type' => 'text',
                                'default'=> saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                        ),
                        array(
                                'label' => 'Organization Logo',
                                'id' => 'saswp_opinionnewsarticle_organization_logo_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($sd_data['sd_logo'])? $sd_data['sd_logo']['url']:''
                        ),                         
                        array(
                                'label' => 'Speakable',
                                'id' => 'saswp_opinionnewsarticle_speakable_'.$schema_id,
                                'type' => 'checkbox',
        
                        )                        
                        );
                        break;

                        case 'ReportageNewsArticle':
                
                        $category_detail=get_the_category(get_the_ID());//$post->ID
                        $article_section = '';
                        
                        foreach($category_detail as $cd){
                                
                        $article_section =  $cd->cat_name;
                        
                        }
                        $word_count = saswp_reading_time_and_word_count();
                        
                        $meta_field = array(
                                array(
                                        'label'      => 'ID',
                                        'id'         => 'saswp_reportagenewsarticle_id_'.$schema_id,
                                        'type'       => 'text',
                                        'default'    => 'reportagenewsarticle'   
                                ),  
                        array(
                                'label' => 'Main Entity Of Page',
                                'id' => 'saswp_reportagenewsarticle_main_entity_of_page_'.$schema_id,
                                'type' => 'text',
                                'default' => get_permalink()
                        ),
                        array(
                                'label' => 'URL',
                                'id' => 'saswp_reportagenewsarticle_URL_'.$schema_id,
                                'type' => 'text',
                                'default' => get_permalink(),
                        ),
                        array(
                                'label' => 'Image',
                                'id' => 'saswp_reportagenewsarticle_image_'.$schema_id,
                                'type' => 'media',                            
                        ),    
                        array(
                                'label'   => 'inLanguage',
                                'id'      => 'saswp_reportagenewsarticle_inlanguage_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_bloginfo('language'),
                        ),
                        array(
                                'label' => 'Headline',
                                'id' => 'saswp_reportagenewsarticle_headline_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_title(),
                        ),
                        array(
                                'label' => 'Date Published',
                                'id' => 'saswp_reportagenewsarticle_date_published_'.$schema_id,
                                'type' => 'text',
                                'default' => get_the_date("Y-m-d")
                        ),
                        array(
                                'label' => 'Date Modified',
                                'id' => 'saswp_reportagenewsarticle_date_modified_'.$schema_id,
                                'type' => 'text',
                                'default' => get_the_modified_date("Y-m-d")
                        ),
                                array(
                                'label' => 'Description',
                                'id' => 'saswp_reportagenewsarticle_description_'.$schema_id,
                                'type' => 'textarea',
                                'default' => saswp_strip_all_tags(get_the_excerpt())
                        ),
                        array(
                                'label' => 'Keywords',
                                'id' => 'saswp_reportagenewsarticle_keywords_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_tags()
                        ),    
                                array(
                                'label' => 'Article Section',
                                'id' => 'saswp_reportagenewsarticle_section_'.$schema_id,
                                'type' => 'text',
                                'default' => $article_section
                        ),
                        array(
                                'label' => 'Article Body',
                                'id' => 'saswp_reportagenewsarticle_body_'.$schema_id,
                                'type' => 'textarea',
                                'default' => is_object($post) ? saswp_strip_all_tags($post->post_content) : ''
                        ),
                                array(
                                'label' => 'Name',
                                'id' => 'saswp_reportagenewsarticle_name_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_title()
                        ), 
                                array(
                                'label' => 'Thumbnail URL',
                                'id' => 'saswp_reportagenewsarticle_thumbnailurl_'.$schema_id,
                                'type' => 'text'                            
                        ),
                        array(
                                'label' => 'Word Count',
                                'id' => 'saswp_reportagenewsarticle_word_count_'.$schema_id,
                                'type' => 'text',
                                'default' => $word_count['word_count']
                        ),
                        array(
                                'label' => 'Time Required',
                                'id' => 'saswp_reportagenewsarticle_timerequired_'.$schema_id,
                                'type' => 'text',
                                'default' => $word_count['timerequired']
                        ),    
                        array(
                                'label' => 'Main Entity Id',
                                'id' => 'saswp_reportagenewsarticle_main_entity_id_'.$schema_id,
                                'type' => 'text',
                                'default' => get_permalink()
                        ),
                        array(
                                'label'   => 'Author Type',
                                'id'      => 'saswp_reportagenewsarticle_author_type_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                        'Person'           => 'Person',
                                        'Organization'     => 'Organization',                        
                                )
                        ),
                        array(
                                'label' => 'Author Name',
                                'id' => 'saswp_reportagenewsarticle_author_name_'.$schema_id,
                                'type' => 'text',
                                'default' => is_object($current_user) ?  $current_user->display_name : ''
                        ),
                        array(
                                'label' => 'Author Description',
                                'id' => 'saswp_reportagenewsarticle_author_description_'.$schema_id,
                                'type' => 'textarea',
                                'default' => $author_desc
                        ), 
                        array(
                                'label'   => 'Author URL',
                                'id'      => 'saswp_reportagenewsarticle_author_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => $author_url
                        ),    
                        array(
                                'label' => 'Author Image',
                                'id' => 'saswp_reportagenewsarticle_author_image_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($author_details['url']) ? $author_details['url']: ''
                        ),
        
                        array(
                                'label'   => 'Editor Type',
                                'id'      => 'saswp_reportagenewsarticle_editor_type_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                        ""                => "Select",
                                        'Person'           => 'Person',
                                        'Organization'     => 'Organization',                        
                                )
                        ),
                        array(
                                'label'   => 'Editor Name',
                                'id'      => 'saswp_reportagenewsarticle_editor_name_'.$schema_id,
                                'type'    => 'text',
                                'default' => is_object($current_user) ? $current_user->display_name : ''
                        ),
                        array(
                                'label'   => 'Editor HonorificSuffix',
                                'id'      => 'saswp_reportagenewsarticle_editor_honorific_suffix_'.$schema_id,
                                'type'    => 'text',
                                'attributes' => array(
                                        'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                                ),
                        ), 
                        array(
                                'label'   => 'Editor Description',
                                'id'      => 'saswp_reportagenewsarticle_editor_description_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => $author_desc
                        ),
                        array(
                                'label'   => 'Editor URL',
                                'id'      => 'saswp_reportagenewsarticle_editor_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => $author_url
                        ),
                        array(
                                'label' => 'Editor Image URL',
                                'id' => 'saswp_reportagenewsarticle_editor_image_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($author_details['url']) ? $author_details['url']: ''
                        ),
                        array(
                                'label'   => 'About',
                                'id'      => 'saswp_reportagenewsarticle_about_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => '',
                                'note'    => 'Note: If There are more than one About, Separate About list by comma ( , )',
                                'attributes' => array(
                                        'placeholder' => 'eg: Apple is March 21 Announcements'
                                ),
                        ), 
                        array(
                                'label' => 'Organization Name',
                                'id' => 'saswp_reportagenewsarticle_organization_name_'.$schema_id,
                                'type' => 'text',
                                'default'=> saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                        ),
                        array(
                                'label' => 'Organization Logo',
                                'id' => 'saswp_reportagenewsarticle_organization_logo_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($sd_data['sd_logo'])? $sd_data['sd_logo']['url']:''
                        ),                         
                        array(
                                'label' => 'Speakable',
                                'id' => 'saswp_reportagenewsarticle_speakable_'.$schema_id,
                                'type' => 'checkbox',
        
                        )                        
                        );
                        break;

                        case 'ReviewNewsArticle':
        
                        $category_detail=get_the_category(get_the_ID());//$post->ID
                        $article_section = '';
                        
                        foreach($category_detail as $cd){
                                
                        $article_section =  $cd->cat_name;
                        
                        }
                        $word_count = saswp_reading_time_and_word_count();
                        
                        $meta_field = array(
                                array(
                                        'label'      => 'ID',
                                        'id'         => 'saswp_reviewnewsarticle_id_'.$schema_id,
                                        'type'       => 'text',
                                        'default'    => 'reviewnewsarticle'   
                                ),  
                        array(
                                'label' => 'Main Entity Of Page',
                                'id' => 'saswp_reviewnewsarticle_main_entity_of_page_'.$schema_id,
                                'type' => 'text',
                                'default' => get_permalink()
                        ),
                        array(
                                'label' => 'URL',
                                'id' => 'saswp_reviewnewsarticle_URL_'.$schema_id,
                                'type' => 'text',
                                'default' => get_permalink(),
                        ),
                        array(
                                'label' => 'Image',
                                'id' => 'saswp_reviewnewsarticle_image_'.$schema_id,
                                'type' => 'media',                            
                        ),    
                        array(
                                'label'   => 'inLanguage',
                                'id'      => 'saswp_reviewnewsarticle_inlanguage_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_bloginfo('language'),
                        ),
                        array(
                                'label' => 'Headline',
                                'id' => 'saswp_reviewnewsarticle_headline_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_title(),
                        ),
                        array(
                                'label' => 'Date Published',
                                'id' => 'saswp_reviewnewsarticle_date_published_'.$schema_id,
                                'type' => 'text',
                                'default' => get_the_date("Y-m-d")
                        ),
                        array(
                                'label' => 'Date Modified',
                                'id' => 'saswp_reviewnewsarticle_date_modified_'.$schema_id,
                                'type' => 'text',
                                'default' => get_the_modified_date("Y-m-d")
                        ),
                                array(
                                'label' => 'Description',
                                'id' => 'saswp_reviewnewsarticle_description_'.$schema_id,
                                'type' => 'textarea',
                                'default' => saswp_strip_all_tags(get_the_excerpt())
                        ),
                        array(
                                'label' => 'Keywords',
                                'id' => 'saswp_reviewnewsarticle_keywords_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_tags()
                        ),    
                                array(
                                'label' => 'Article Section',
                                'id' => 'saswp_reviewnewsarticle_section_'.$schema_id,
                                'type' => 'text',
                                'default' => $article_section
                        ),
                        array(
                                'label' => 'Article Body',
                                'id' => 'saswp_reviewnewsarticle_body_'.$schema_id,
                                'type' => 'textarea',
                                'default' => is_object($post) ? saswp_strip_all_tags($post->post_content) : ''
                        ),
                                array(
                                'label' => 'Name',
                                'id' => 'saswp_reviewnewsarticle_name_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_title()
                        ), 
                                array(
                                'label' => 'Thumbnail URL',
                                'id' => 'saswp_reviewnewsarticle_thumbnailurl_'.$schema_id,
                                'type' => 'text'                            
                        ),
                        array(
                                'label' => 'Word Count',
                                'id' => 'saswp_reviewnewsarticle_word_count_'.$schema_id,
                                'type' => 'text',
                                'default' => $word_count['word_count']
                        ),
                        array(
                                'label' => 'Time Required',
                                'id' => 'saswp_reviewnewsarticle_timerequired_'.$schema_id,
                                'type' => 'text',
                                'default' => $word_count['timerequired']
                        ),    
                        array(
                                'label' => 'Main Entity Id',
                                'id' => 'saswp_reviewnewsarticle_main_entity_id_'.$schema_id,
                                'type' => 'text',
                                'default' => get_permalink()
                        ),
                        array(
                                'label'   => 'Author Type',
                                'id'      => 'saswp_reviewnewsarticle_author_type_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                        'Person'           => 'Person',
                                        'Organization'     => 'Organization',                        
                                )
                        ),
                        array(
                                'label' => 'Author Name',
                                'id' => 'saswp_reviewnewsarticle_author_name_'.$schema_id,
                                'type' => 'text',
                                'default' => is_object($current_user) ?  $current_user->display_name : ''
                        ),
                        array(
                                'label' => 'Author Description',
                                'id' => 'saswp_reviewnewsarticle_author_description_'.$schema_id,
                                'type' => 'textarea',
                                'default' => $author_desc
                        ), 
                        array(
                                'label'   => 'Author URL',
                                'id'      => 'saswp_reviewnewsarticle_author_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => $author_url
                        ),    
                        array(
                                'label' => 'Author Image',
                                'id' => 'saswp_reviewnewsarticle_author_image_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($author_details['url']) ? $author_details['url']: ''
                        ),
        
                        array(
                                'label'   => 'Editor Type',
                                'id'      => 'saswp_reviewnewsarticle_editor_type_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                        ""                => "Select",
                                        'Person'           => 'Person',
                                        'Organization'     => 'Organization',                        
                                )
                        ),
                        array(
                                'label'   => 'Editor Name',
                                'id'      => 'saswp_reviewnewsarticle_editor_name_'.$schema_id,
                                'type'    => 'text',
                                'default' => is_object($current_user) ? $current_user->display_name : ''
                        ),
                        array(
                                'label'   => 'Editor HonorificSuffix',
                                'id'      => 'saswp_reviewnewsarticle_editor_honorific_suffix_'.$schema_id,
                                'type'    => 'text',
                                'attributes' => array(
                                        'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                                ),
                        ), 
                        array(
                                'label'   => 'Editor Description',
                                'id'      => 'saswp_reviewnewsarticle_editor_description_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => $author_desc
                        ),
                        array(
                                'label'   => 'Editor URL',
                                'id'      => 'saswp_reviewnewsarticle_editor_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => $author_url
                        ),
                        array(
                                'label' => 'Editor Image URL',
                                'id' => 'saswp_reviewnewsarticle_editor_image_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($author_details['url']) ? $author_details['url']: ''
                        ),
                        array(
                                'label'   => 'About',
                                'id'      => 'saswp_reviewnewsarticle_about_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => '',
                                'note'    => 'Note: If There are more than one About, Separate About list by comma ( , )',
                                'attributes' => array(
                                        'placeholder' => 'eg: Apple is March 21 Announcements'
                                ),
                        ), 
                        array(
                                'label' => 'Organization Name',
                                'id' => 'saswp_reviewnewsarticle_organization_name_'.$schema_id,
                                'type' => 'text',
                                'default'=> saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                        ),
                        array(
                                'label' => 'Organization Logo',
                                'id' => 'saswp_reviewnewsarticle_organization_logo_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($sd_data['sd_logo'])? $sd_data['sd_logo']['url']:''
                        ),                         
                        array(
                                'label' => 'Speakable',
                                'id' => 'saswp_reviewnewsarticle_speakable_'.$schema_id,
                                'type' => 'checkbox',
        
                        ),                        
                        );
                        if($manual == null){
                         
                            $meta_field[] = array(
                            'label'   => 'Item Reviewed Type',
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
                                'label'      => 'ID',
                                'id'         => 'saswp_webpage_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'WebPage'   
                        ), 
                    array(
                            'label' => 'Name',
                            'id' => 'saswp_webpage_name_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_get_the_title()
                    ),
                    array(
                            'label' => 'URL',
                            'id' => 'saswp_webpage_url_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink()
                    ),
                    array(
                            'label' => 'Description',
                            'id' => 'saswp_webpage_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => saswp_strip_all_tags(get_the_excerpt())
                    ),
                    array(
                        'label'   => 'inLanguage',
                        'id'      => 'saswp_webpage_inlanguage_'.$schema_id,
                        'type'    => 'text',
                        'default' => get_bloginfo('language'),
                    ),
                    array(
                        'label'   => 'Webpage Section',
                        'id'      => 'saswp_webpage_section_'.$schema_id,
                        'type'    => 'textarea',
                        'default' => saswp_strip_all_tags(get_the_excerpt())
                    ),                           
                    array(
                            'label' => 'Keywords',
                            'id' => 'saswp_webpage_keywords_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_get_the_tags()
                    ),
                    array(
                            'label' => 'Main Entity Of Page',
                            'id' => 'saswp_webpage_main_entity_of_page_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink()
                    ), 
                    array(
                            'label' => 'Image',
                            'id' => 'saswp_webpage_image_'.$schema_id,
                            'type' => 'media',                            
                    ), 
                    array(
                            'label' => 'Headline',
                            'id' => 'saswp_webpage_headline_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_get_the_title(),
                    ),
                    array(
                        'label'   => 'Date Created',
                        'id'      => 'saswp_webpage_date_created_'.$schema_id,
                        'type'    => 'text',
                        'default' => get_the_modified_date("Y-m-d")
                   ),
                    array(
                            'label' => 'Date Published',
                            'id' => 'saswp_webpage_date_published_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_date("Y-m-d")
                    ),
                    array(
                            'label' => 'Date Modified',
                            'id' => 'saswp_webpage_date_modified_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_modified_date("Y-m-d")
                    ),
                    array(
                        'label'   => 'Last Reviewed',
                        'id'      => 'saswp_webpage_last_reviewed_'.$schema_id,
                        'type'    => 'text',
                        'default' => get_the_modified_date("Y-m-d")
                    ),
                     array(
                        'label'   => 'Reviewed By',
                        'id'      => 'saswp_webpage_reviewed_by_'.$schema_id,
                        'type'    => 'text',
                        'default' => saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                      ),
                    array(
                        'label'   => 'Author Type',
                        'id'      => 'saswp_webpage_author_type_'.$schema_id,
                        'type'    => 'select',
                        'options' => array(
                                'Person'           => 'Person',
                                'Organization'     => 'Organization',                        
                       )
                    ),
                    array(
                            'label' => 'Author Name',
                            'id' => 'saswp_webpage_author_name_'.$schema_id,
                            'type' => 'text',
                            'default' => is_object($current_user) ? $current_user->display_name : ''
                    ),
                    array(
                            'label' => 'Author Description',
                            'id' => 'saswp_webpage_author_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => $author_desc
                    ), 
                    array(
                            'label'   => 'Author URL',
                            'id'      => 'saswp_webpage_author_url_'.$schema_id,
                            'type'    => 'text',
                            'default' => $author_url
                    ),    
                    array(
                            'label' => 'Organization Name',
                            'id' => 'saswp_webpage_organization_name_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                    ), 
                     array(
                            'label' => 'Organization Logo',
                            'id' => 'saswp_webpage_organization_logo_'.$schema_id,
                            'type' => 'media',
                            'default' => isset($sd_data['sd_logo']) ? $sd_data['sd_logo']['url']:''
                    ),
                    array(
                        'label' => 'Speakable',
                        'id' => 'saswp_webpage_speakable_'.$schema_id,
                        'type' => 'checkbox',

                    )    
                    );
                    break;

                case 'ItemPage':
                        $meta_field = array(
                                array(
                                        'label'      => 'ID',
                                        'id'         => 'saswp_itempage_id_'.$schema_id,
                                        'type'       => 'text',
                                        'default'    => 'ItemPage'   
                                ), 
                        array(
                                'label' => 'Name',
                                'id' => 'saswp_itempage_name_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_title()
                        ),
                        array(
                                'label' => 'URL',
                                'id' => 'saswp_itempage_url_'.$schema_id,
                                'type' => 'text',
                                'default' => get_permalink()
                        ),
                        array(
                                'label' => 'Description',
                                'id' => 'saswp_itempage_description_'.$schema_id,
                                'type' => 'textarea',
                                'default' => saswp_strip_all_tags(get_the_excerpt())
                        ),
                        array(
                                'label'   => 'inLanguage',
                                'id'      => 'saswp_itempage_inlanguage_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_bloginfo('language'),
                        ),
                        array(
                                'label'   => 'ItemPage Section',
                                'id'      => 'saswp_itempage_section_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => saswp_strip_all_tags(get_the_excerpt())
                        ),                           
                        array(
                                'label' => 'Keywords',
                                'id' => 'saswp_itempage_keywords_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_tags()
                        ),
                        array(
                                'label' => 'Main Entity Of Page',
                                'id' => 'saswp_itempage_main_entity_of_page_'.$schema_id,
                                'type' => 'text',
                                'default' => get_permalink()
                        ), 
                        array(
                                'label' => 'Image',
                                'id' => 'saswp_itempage_image_'.$schema_id,
                                'type' => 'media',                            
                        ), 
                        array(
                                'label' => 'Headline',
                                'id' => 'saswp_itempage_headline_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_title(),
                        ),
                        array(
                                'label'   => 'Date Created',
                                'id'      => 'saswp_itempage_date_created_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_the_modified_date("Y-m-d")
                        ),
                        array(
                                'label' => 'Date Published',
                                'id' => 'saswp_itempage_date_published_'.$schema_id,
                                'type' => 'text',
                                'default' => get_the_date("Y-m-d")
                        ),
                        array(
                                'label' => 'Date Modified',
                                'id' => 'saswp_itempage_date_modified_'.$schema_id,
                                'type' => 'text',
                                'default' => get_the_modified_date("Y-m-d")
                        ),
                        array(
                                'label'   => 'Last Reviewed',
                                'id'      => 'saswp_itempage_last_reviewed_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_the_modified_date("Y-m-d")
                        ),
                                array(
                                'label'   => 'Reviewed By',
                                'id'      => 'saswp_itempage_reviewed_by_'.$schema_id,
                                'type'    => 'text',
                                'default' => saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                                ),
                        array(
                                'label'   => 'Author Type',
                                'id'      => 'saswp_itempage_author_type_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                        'Person'           => 'Person',
                                        'Organization'     => 'Organization',                        
                                )
                        ),
                        array(
                                'label' => 'Author Name',
                                'id' => 'saswp_itempage_author_name_'.$schema_id,
                                'type' => 'text',
                                'default' => is_object($current_user) ? $current_user->display_name : ''
                        ),
                        array(
                                'label' => 'Author Description',
                                'id' => 'saswp_itempage_author_description_'.$schema_id,
                                'type' => 'textarea',
                                'default' => $author_desc
                        ), 
                        array(
                                'label'   => 'Author URL',
                                'id'      => 'saswp_itempage_author_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => $author_url
                        ),    
                        array(
                                'label' => 'Organization Name',
                                'id' => 'saswp_itempage_organization_name_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                        ), 
                                array(
                                'label' => 'Organization Logo',
                                'id' => 'saswp_itempage_organization_logo_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($sd_data['sd_logo']) ? $sd_data['sd_logo']['url']:''
                        ),
                        array(
                                'label' => 'Speakable',
                                'id' => 'saswp_itempage_speakable_'.$schema_id,
                                'type' => 'checkbox',

                        )    
                );
                break;

                case 'MedicalWebPage':
                    $meta_field = array(
                        array(
                                'label' => 'Name',
                                'id' => 'saswp_medicalwebpage_name_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_title()
                        ),
                        array(
                                'label' => 'URL',
                                'id' => 'saswp_medicalwebpage_url_'.$schema_id,
                                'type' => 'text',
                                'default' => get_permalink()
                        ),
                        array(
                                'label' => 'Description',
                                'id' => 'saswp_medicalwebpage_description_'.$schema_id,
                                'type' => 'textarea',
                                'default' => saswp_strip_all_tags(get_the_excerpt())
                        ),
                        array(
                                'label'   => 'MedicalWebPage Section',
                                'id'      => 'saswp_medicalwebpage_section_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => saswp_strip_all_tags(get_the_excerpt())
                        ),                           
                        array(
                                'label' => 'Keywords',
                                'id' => 'saswp_medicalwebpage_keywords_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_tags()
                        ),
                        array(
                                'label' => 'Main Entity Of Page',
                                'id' => 'saswp_medicalwebpage_main_entity_of_page_'.$schema_id,
                                'type' => 'text',
                                'default' => get_permalink()
                        ), 
                        array(
                                'label' => 'Image',
                                'id' => 'saswp_medicalwebpage_image_'.$schema_id,
                                'type' => 'media',                            
                        ), 
                        array(
                                'label' => 'Headline',
                                'id' => 'saswp_medicalwebpage_headline_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_title(),
                        ),
                        array(
                                'label'   => 'Date Created',
                                'id'      => 'saswp_medicalwebpage_date_created_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_the_modified_date("Y-m-d")
                        ),
                        array(
                                'label' => 'Date Published',
                                'id' => 'saswp_medicalwebpage_date_published_'.$schema_id,
                                'type' => 'text',
                                'default' => get_the_date("Y-m-d")
                        ),
                        array(
                                'label' => 'Date Modified',
                                'id' => 'saswp_medicalwebpage_date_modified_'.$schema_id,
                                'type' => 'text',
                                'default' => get_the_modified_date("Y-m-d")
                        ),
                        array(
                                'label'   => 'Last Reviewed',
                                'id'      => 'saswp_medicalwebpage_last_reviewed_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_the_modified_date("Y-m-d")
                        ),
                        array(
                                'label'   => 'Reviewed By',
                                'id'      => 'saswp_medicalwebpage_reviewed_by_'.$schema_id,
                                'type'    => 'text',
                                'default' => saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                        ),
                        array(
                                'label'   => 'Author Type',
                                'id'      => 'saswp_medicalwebpage_author_type_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                        'Person'           => 'Person',
                                        'Organization'     => 'Organization',                        
                                )
                        ),
                        array(
                                'label' => 'Author Name',
                                'id' => 'saswp_medicalwebpage_author_name_'.$schema_id,
                                'type' => 'text',
                                'default' => is_object($current_user) ? $current_user->display_name : ''
                        ),
                        array(
                                'label' => 'Author Description',
                                'id' => 'saswp_medicalwebpage_author_description_'.$schema_id,
                                'type' => 'textarea',
                                'default' => $author_desc
                        ), 
                        array(
                                'label'   => 'Author URL',
                                'id'      => 'saswp_medicalwebpage_author_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => $author_url
                        ),    
                        array(
                                'label' => 'Organization Name',
                                'id' => 'saswp_medicalwebpage_organization_name_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                        ), 
                        array(
                                'label' => 'Organization Logo',
                                'id' => 'saswp_medicalwebpage_organization_logo_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($sd_data['sd_logo']) ? $sd_data['sd_logo']['url']:''
                        ),
                        array(
                                'label' => 'Speakable',
                                'id' => 'saswp_medicalwebpage_speakable_'.$schema_id,
                                'type' => 'checkbox',

                        )    
                    );
                break;

                    case 'Photograph':                                        
                        $meta_field = array( 
                        array(
                                'label'      => 'ID',
                                'id'         => 'saswp_photograph_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'Photograph'   
                        ),
			array(
                                'label' => 'Headline',
                                'id' => 'saswp_photograph_headline_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_title()
                        ),						
                        array(
                                'label'   => 'URL',
                                'id'      => 'saswp_photograph_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_permalink(),
                        ),    
                        array(
                                'label' => 'Image',
                                'id' => 'saswp_photograph_image_'.$schema_id,
                                'type' => 'media'                            
                        ),
                        array(
                                'label'   => 'inLanguage',
                                'id'      => 'saswp_photograph_inlanguage_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_bloginfo('language'),
                        ),                        
                        array(
                                'label' => 'Description',
                                'id' => 'saswp_photograph_description_'.$schema_id,
                                'type' => 'textarea',
                                'default' => saswp_strip_all_tags(get_the_excerpt())
                        ),                                                    
                        array(
                                'label' => 'Date Published',
                                'id' => 'saswp_photograph_date_published_'.$schema_id,
                                'type' => 'text',
                                'default' => get_the_date("Y-m-d")
                        ), 
                        array(
                                'label' => 'Date Modified',
                                'id' => 'saswp_photograph_date_modified_'.$schema_id,
                                'type' => 'text',
                                'default' => get_the_modified_date("Y-m-d")
                        ),
                        array(
                                'label'   => 'Author',
                                'id'      => 'saswp_photograph_author_global_mapping_'.$schema_id,
                                'type'    => 'global_mapping'
                        ),
                        array(
                                'label'   => 'Author Type',
                                'id'      => 'saswp_photograph_author_type_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                        'Person'           => 'Person',
                                        'Organization'     => 'Organization',                        
                                )
                        ),
                        array(
                                'label' => 'Author Name',
                                'id' => 'saswp_photograph_author_name_'.$schema_id,
                                'type' => 'text',
                                'default' => is_object($current_user) ? $current_user->display_name : ''
                        ),
                        array(
                                'label'   => 'Author HonorificSuffix',
                                'id'      => 'saswp_photograph_author_honorific_suffix_'.$schema_id,
                                'type'    => 'text',
                                'attributes' => array(
                                        'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                                 ),
                        ),
                        array(
                                'label' => 'Author Description',
                                'id' => 'saswp_photograph_author_description_'.$schema_id,
                                'type' => 'textarea',
                                'default' => $author_desc
                        ),
                        array(
                                'label'   => 'Author URL',
                                'id'      => 'saswp_photograph_author_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => $author_url
                        ),   
                        array(
                                'label' => 'Author Image URL',
                                'id' => 'saswp_photograph_author_image_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($author_details['url']) ? $author_details['url']: ''
                        ), 
                        array(
                                'label'   => 'JobTitle',
                                'id'      => 'saswp_photograph_author_jobtitle_'.$schema_id,
                                'type'    => 'text',
                                'default' => '',
                                'attributes' => array(
                                        'placeholder' => 'eg: Editor in Chief'
                                 ),
                        ),

                        array(
                                'label'   => 'Editor Type',
                                'id'      => 'saswp_photograph_editor_type_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                         ""               => "Select",
                                        'Person'           => 'Person',
                                        'Organization'     => 'Organization',                        
                                )
                        ),
                        array(
                                'label' => 'Editor Name',
                                'id' => 'saswp_photograph_editor_name_'.$schema_id,
                                'type' => 'text',
                                'default' => is_object($current_user) ? $current_user->display_name : ''
                        ),
                        array(
                                'label'   => 'Editor HonorificSuffix',
                                'id'      => 'saswp_photograph_editor_honorific_suffix_'.$schema_id,
                                'type'    => 'text',
                                'attributes' => array(
                                        'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                                 ),
                        ),
                        array(
                                'label'   => 'Editor Description',
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
                                'label' => 'Editor Image URL',
                                'id' => 'saswp_photograph_editor_image_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($author_details['url']) ? $author_details['url']: ''
                        ),
                        array(
                                'label'   => 'ReviewedBy',
                                'id'      => 'saswp_photograph_reviewedby_global_mapping_'.$schema_id,
                                'type'    => 'global_mapping'
                        ),
                        array(
                                'label'   => 'ReviewedBy Type',
                                'id'      => 'saswp_photograph_reviewedby_type_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                         ""                => "Select",
                                        'Person'           => 'Person',
                                        'Organization'     => 'Organization',                        
                                )
                        ),
                        array(
                                'label' => 'ReviewedBy Name',
                                'id' => 'saswp_photograph_reviewedby_name_'.$schema_id,
                                'type' => 'text',
                                'default' => is_object($current_user) ? $current_user->display_name : ''
                        ),
                        array(
                                'label'   => 'ReviewedBy HonorificSuffix',
                                'id'      => 'saswp_photograph_reviewedby_honorific_suffix_'.$schema_id,
                                'type'    => 'text',
                                'attributes' => array(
                                        'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                                 ),
                        ),
                        array(
                                'label' => 'ReviewedBy Description',
                                'id' => 'saswp_photograph_reviewedby_description_'.$schema_id,
                                'type' => 'textarea',
                                'default' => $author_desc
                        ),
                        array(
                                'label'   => 'ReviewedBy URL',
                                'id'      => 'saswp_photograph_reviewedby_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => $author_url
                        ),  
                        array(
                                'label' => 'Organization Name',
                                'id' => 'saswp_photograph_organization_name_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                        ),
                        array(
                                'label' => 'Organization Logo',
                                'id'    => 'saswp_photograph_organization_logo_'.$schema_id,
                                'type'  => 'media',
                                'default' => isset($sd_data['sd_logo']['url']) ? $sd_data['sd_logo']['url']:''
                        ),
                        array(
                                'label'   => 'About',
                                'id'      => 'saswp_photograph_about_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => '',
                                'note'    => 'Note: If There are more than one About, Separate About list by comma ( , )',
                                'attributes' => array(
                                        'placeholder' => 'eg: Apple is March 21 Announcements'
                                ),
                        ),  
                        array(
                                'label'   => 'AlumniOf',
                                'id'      => 'saswp_photograph_alumniof_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => '',
                                'note'    => 'Note: If There are more than one AlumniOf, Separate AlumniOf list by comma ( , )',
                                'attributes' => array(
                                        'placeholder' => 'eg: City University of New York-Herbert H. Lehman College, Southern New Hampshire University'
                                ),
                        ),   
                        array(
                                'label'   => 'knowsAbout',
                                'id'      => 'saswp_photograph_knowsabout_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => '',
                                'note'    => 'Note: If There are more than one knows about, Separate knows about list by comma ( , )'       
                        ),
                        array(
                                'label'   => 'ReviewedBy',
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
                                'label'      => 'ID',
                                'id'         => 'saswp_article_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'Article'   
                        ),
                        array(
                                'label'   => 'Main Entity Of Page',
                                'id'      => 'saswp_article_main_entity_of_page_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_permalink()
                        ),
                        array(
                                'label'   => 'URL',
                                'id'      => 'saswp_article_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_permalink(),
                        ),    
                        array(
                                'label'   => 'Image',
                                'id'      => 'saswp_article_image_'.$schema_id,
                                'type'    => 'media'                            
                        ),
                        array(
                                'label'   => 'inLanguage',
                                'id'      => 'saswp_article_inlanguage_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_bloginfo('language'),
                        ),
                        array(
                                'label'   => 'Headline',
                                'id'      => 'saswp_article_headline_'.$schema_id,
                                'type'    => 'text',
                                'default' => saswp_get_the_title()
                        ),
                        array(
                                'label'   => 'Description',
                                'id'      => 'saswp_article_description_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => saswp_strip_all_tags(get_the_excerpt())
                        ),
                        array(
                                'label'   => 'Article Section',
                                'id'      => 'saswp_article_section_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => saswp_strip_all_tags(get_the_excerpt())
                        ),    
                        array(
                                'label'   => 'Article Body',
                                'id'      => 'saswp_article_body_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => is_object($post) ? saswp_strip_all_tags($post->post_content) : ''
                        ),    
                        array(
                                'label'   => 'Keywords',
                                'id'      => 'saswp_article_keywords_'.$schema_id,
                                'type'    => 'text',
                                'default' => saswp_get_the_tags()
                        ),    
                        array(
                                'label'   => 'Date Published',
                                'id'      => 'saswp_article_date_published_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_the_date("Y-m-d")
                        ), 
                        array(
                                'label'   => 'Date Modified',
                                'id'      => 'saswp_article_date_modified_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_the_modified_date("Y-m-d")
                        ),
                        array(
                                'label'   => 'Author',
                                'id'      => 'saswp_article_author_global_mapping_'.$schema_id,
                                'type'    => 'global_mapping'
                        ),
                        array(
                                'label'   => 'Author Type',
                                'id'      => 'saswp_article_author_type_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                        'Person'           => 'Person',
                                        'Organization'     => 'Organization',                        
                                )
                        ),
                        array(
                                'label'   => 'Author Name',
                                'id'      => 'saswp_article_author_name_'.$schema_id,
                                'type'    => 'text',
                                'default' => is_object($current_user) ? $current_user->display_name : ''
                        ),
                        array(
                                'label'   => 'Author HonorificSuffix',
                                'id'      => 'saswp_article_author_honorific_suffix_'.$schema_id,
                                'type'    => 'text',
                                'attributes' => array(
                                        'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                                 ),
                        ),
                        array(
                                'label'   => 'Author Description',
                                'id'      => 'saswp_article_author_description_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => $author_desc
                        ),
                        array(
                                'label'   => 'Author URL',
                                'id'      => 'saswp_article_author_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => $author_url
                        ),
                        array(
                                'label' => 'Author Image URL',
                                'id' => 'saswp_article_author_image_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($author_details['url']) ? $author_details['url']: ''
                        ),
                        array(
                                'label' => 'Author Social Profile',
                                'id' => 'saswp_article_author_social_profile_'.$schema_id,
                                'type' => 'textarea',
                                'default' => '',
                                'note'    => 'Note: If There are more than one social profiles, Separate them by comma ( , )',
                        ),
                        array(
                                'label'   => 'JobTitle',
                                'id'      => 'saswp_article_author_jobtitle_'.$schema_id,
                                'type'    => 'text',
                                'default' => '',
                                'attributes' => array(
                                        'placeholder' => 'eg: Editor in Chief'
                                 ),
                        ),
                        array(
                                'label'   => 'ReviewedBy',
                                'id'      => 'saswp_article_reviewedby_global_mapping_'.$schema_id,
                                'type'    => 'global_mapping'
                        ),
                        array(
                                'label'   => 'ReviewedBy Type',
                                'id'      => 'saswp_article_reviewedby_type_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                        ""                => "Select",
                                        'Person'           => 'Person',
                                        'Organization'     => 'Organization',                        
                                )
                        ),
                        array(
                                'label'   => 'ReviewedBy Name',
                                'id'      => 'saswp_article_reviewedby_name_'.$schema_id,
                                'type'    => 'text',
                                'default' => is_object($current_user) ? $current_user->display_name : ''
                        ),
                        array(
                                'label'   => 'ReviewedBy HonorificSuffix',
                                'id'      => 'saswp_article_reviewedby_honorific_suffix_'.$schema_id,
                                'type'    => 'text',
                                'attributes' => array(
                                        'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                                 ),
                        ),
                        array(
                                'label'   => 'ReviewedBy Description',
                                'id'      => 'saswp_article_reviewedby_description_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => $author_desc
                        ),
                        array(
                                'label'   => 'ReviewedBy URL',
                                'id'      => 'saswp_article_reviewedby_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => $author_url
                        ),
                        array(
                                'label'   => 'Editor',
                                'id'      => 'saswp_article_editor_global_mapping_'.$schema_id,
                                'type'    => 'global_mapping'
                        ),
                        array(
                                'label'   => 'Editor Type',
                                'id'      => 'saswp_article_editor_type_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                        ""                => "Select",
                                        'Person'           => 'Person',
                                        'Organization'     => 'Organization',                        
                                )
                        ),
                        array(
                                'label'   => 'Editor Name',
                                'id'      => 'saswp_article_editor_name_'.$schema_id,
                                'type'    => 'text',
                                'default' => is_object($current_user) ? $current_user->display_name : ''
                        ),
                        array(
                                'label'   => 'Editor HonorificSuffix',
                                'id'      => 'saswp_article_editor_honorific_suffix_'.$schema_id,
                                'type'    => 'text',
                                'attributes' => array(
                                        'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                                 ),
                        ), 
                        array(
                                'label'   => 'Editor Description',
                                'id'      => 'saswp_article_editor_description_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => $author_desc
                        ),
                        array(
                                'label'   => 'Editor URL',
                                'id'      => 'saswp_article_editor_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => $author_url
                        ),
                        array(
                                'label' => 'Editor Image URL',
                                'id' => 'saswp_article_editor_image_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($author_details['url']) ? $author_details['url']: ''
                        ),
                        array(
                                'label'   => 'Organization Name',
                                'id'      => 'saswp_article_organization_name_'.$schema_id,
                                'type'    => 'text',
                                'default' => saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                        ),
                        array(
                                'label'   => 'Organization Logo',
                                'id'      => 'saswp_article_organization_logo_'.$schema_id,
                                'type'    => 'media',
                                'default' => isset($sd_data['sd_logo']['url']) ? $sd_data['sd_logo']['url']:''
                        ),
                        array(
                                'label'   => 'About',
                                'id'      => 'saswp_article_about_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => '',
                                'note'    => 'Note: If There are more than one About, Separate About list by comma ( , )',
                                'attributes' => array(
                                        'placeholder' => 'eg: Apple is March 21 Announcements'
                                ),
                        ),  
                        array(
                                'label'   => 'AlumniOf',
                                'id'      => 'saswp_article_alumniof_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => '',
                                'note'    => 'Note: If There are more than one AlumniOf, Separate AlumniOf list by comma ( , )',
                                'attributes' => array(
                                        'placeholder' => 'eg: City University of New York-Herbert H. Lehman College, Southern New Hampshire University'
                                ),
                        ),  
                        array(
                                'label'   => 'knowsAbout',
                                'id'      => 'saswp_article_knowsabout_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => '',
                                'note'    => 'Note: If There are more than one knows about, Separate knows about list by comma ( , )',
                                'attributes' => array(
                                        'placeholder' => 'eg: Financial services, negotiation, CRM, Project Management, Mentoring, Learning & Development'
                                 ),   
                        ),
                        array(
                                'label'   => 'Speakable',
                                'id'      => 'saswp_article_speakable_'.$schema_id,
                                'type'    => 'checkbox',
                        )
                        );
                        break;

                    case 'ScholarlyArticle':                                        
                        $meta_field = array(
                        array(
                                'label'      => 'ID',
                                'id'         => 'saswp_scholarlyarticle_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'ScholarlyArticle'   
                        ),
                        array(
                                'label'   => 'Main Entity Of Page',
                                'id'      => 'saswp_scholarlyarticle_main_entity_of_page_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_permalink()
                        ),
                        array(
                                'label'   => 'URL',
                                'id'      => 'saswp_scholarlyarticle_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_permalink(),
                        ),    
                        array(
                                'label'   => 'Image',
                                'id'      => 'saswp_scholarlyarticle_image_'.$schema_id,
                                'type'    => 'media'                            
                        ),
                        array(
                                'label'   => 'inLanguage',
                                'id'      => 'saswp_scholarlyarticle_inlanguage_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_bloginfo('language'),
                        ),
                        array(
                                'label'   => 'Headline',
                                'id'      => 'saswp_scholarlyarticle_headline_'.$schema_id,
                                'type'    => 'text',
                                'default' => saswp_get_the_title()
                        ),
                        array(
                                'label'   => 'Description',
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
                                'label'   => 'ScholarlyArticle Body',
                                'id'      => 'saswp_scholarlyarticle_body_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => is_object($post) ? saswp_strip_all_tags($post->post_content) : ''
                        ),    
                        array(
                                'label'   => 'Keywords',
                                'id'      => 'saswp_scholarlyarticle_keywords_'.$schema_id,
                                'type'    => 'text',
                                'default' => saswp_get_the_tags()
                        ),    
                        array(
                                'label'   => 'Date Published',
                                'id'      => 'saswp_scholarlyarticle_date_published_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_the_date("Y-m-d")
                        ), 
                        array(
                                'label'   => 'Date Modified',
                                'id'      => 'saswp_scholarlyarticle_date_modified_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_the_modified_date("Y-m-d")
                        ),
                        array(
                                'label'   => 'Author',
                                'id'      => 'saswp_scholarlyarticle_author_global_mapping_'.$schema_id,
                                'type'    => 'global_mapping'
                        ),
                        array(
                                'label'   => 'Author Type',
                                'id'      => 'saswp_scholarlyarticle_author_type_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                        'Person'           => 'Person',
                                        'Organization'     => 'Organization',                        
                                )
                        ),
                        array(
                                'label'   => 'Author Name',
                                'id'      => 'saswp_scholarlyarticle_author_name_'.$schema_id,
                                'type'    => 'text',
                                'default' => is_object($current_user) ? $current_user->display_name : ''
                        ),
                        array(
                                'label'   => 'Author HonorificSuffix',
                                'id'      => 'saswp_scholarlyarticle_author_honorific_suffix_'.$schema_id,
                                'type'    => 'text',
                                'attributes' => array(
                                        'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                                        ),
                        ),
                        array(
                                'label'   => 'Author Description',
                                'id'      => 'saswp_scholarlyarticle_author_description_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => $author_desc
                        ),
                        array(
                                'label'   => 'Author URL',
                                'id'      => 'saswp_scholarlyarticle_author_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => $author_url
                        ),
                        array(
                                'label' => 'Author Image URL',
                                'id' => 'saswp_scholarlyarticle_author_image_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($author_details['url']) ? $author_details['url']: ''
                        ),
                        array(
                                'label'   => 'JobTitle',
                                'id'      => 'saswp_scholarlyarticle_author_jobtitle_'.$schema_id,
                                'type'    => 'text',
                                'default' => '',
                                'attributes' => array(
                                        'placeholder' => 'eg: Editor in Chief'
                                        ),
                        ),
                        array(
                                'label'   => 'ReviewedBy',
                                'id'      => 'saswp_scholarlyarticle_reviewedby_global_mapping_'.$schema_id,
                                'type'    => 'global_mapping'
                        ),
                        array(
                                'label'   => 'ReviewedBy Type',
                                'id'      => 'saswp_scholarlyarticle_reviewedby_type_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                        ""                => "Select",
                                        'Person'           => 'Person',
                                        'Organization'     => 'Organization',                        
                                )
                        ),
                        array(
                                'label'   => 'ReviewedBy Name',
                                'id'      => 'saswp_scholarlyarticle_reviewedby_name_'.$schema_id,
                                'type'    => 'text',
                                'default' => is_object($current_user) ? $current_user->display_name : ''
                        ),
                        array(
                                'label'   => 'ReviewedBy HonorificSuffix',
                                'id'      => 'saswp_scholarlyarticle_reviewedby_honorific_suffix_'.$schema_id,
                                'type'    => 'text',
                                'attributes' => array(
                                        'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                                        ),
                        ),
                        array(
                                'label'   => 'ReviewedBy Description',
                                'id'      => 'saswp_scholarlyarticle_reviewedby_description_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => $author_desc
                        ),
                        array(
                                'label'   => 'ReviewedBy URL',
                                'id'      => 'saswp_scholarlyarticle_reviewedby_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => $author_url
                        ),
                        array(
                                'label'   => 'Editor',
                                'id'      => 'saswp_scholarlyarticle_editor_global_mapping_'.$schema_id,
                                'type'    => 'global_mapping'
                        ),
                        array(
                                'label'   => 'Editor Type',
                                'id'      => 'saswp_scholarlyarticle_editor_type_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                        ""                => "Select",
                                        'Person'           => 'Person',
                                        'Organization'     => 'Organization',                        
                                )
                        ),
                        array(
                                'label'   => 'Editor Name',
                                'id'      => 'saswp_scholarlyarticle_editor_name_'.$schema_id,
                                'type'    => 'text',
                                'default' => is_object($current_user) ? $current_user->display_name : ''
                        ),
                        array(
                                'label'   => 'Editor HonorificSuffix',
                                'id'      => 'saswp_scholarlyarticle_editor_honorific_suffix_'.$schema_id,
                                'type'    => 'text',
                                'attributes' => array(
                                        'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                                        ),
                        ), 
                        array(
                                'label'   => 'Editor Description',
                                'id'      => 'saswp_scholarlyarticle_editor_description_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => $author_desc
                        ),
                        array(
                                'label'   => 'Editor URL',
                                'id'      => 'saswp_scholarlyarticle_editor_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => $author_url
                        ),
                        array(
                                'label' => 'Editor Image URL',
                                'id' => 'saswp_scholarlyarticle_editor_image_'.$schema_id,
                                'type' => 'media',
                                'default' => isset($author_details['url']) ? $author_details['url']: ''
                        ),
                        array(
                                'label'   => 'Organization Name',
                                'id'      => 'saswp_scholarlyarticle_organization_name_'.$schema_id,
                                'type'    => 'text',
                                'default' => saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                        ),
                        array(
                                'label'   => 'Organization Logo',
                                'id'      => 'saswp_scholarlyarticle_organization_logo_'.$schema_id,
                                'type'    => 'media',
                                'default' => isset($sd_data['sd_logo']['url']) ? $sd_data['sd_logo']['url']:''
                        ),
                        array(
                                'label'   => 'About',
                                'id'      => 'saswp_scholarlyarticle_about_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => '',
                                'note'    => 'Note: If There are more than one About, Separate About list by comma ( , )',
                                'attributes' => array(
                                        'placeholder' => 'eg: Apple is March 21 Announcements'
                                ),
                        ),  
                        array(
                                'label'   => 'AlumniOf',
                                'id'      => 'saswp_scholarlyarticle_alumniof_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => '',
                                'note'    => 'Note: If There are more than one AlumniOf, Separate AlumniOf list by comma ( , )',
                                'attributes' => array(
                                        'placeholder' => 'eg: City University of New York-Herbert H. Lehman College, Southern New Hampshire University'
                                ),
                        ),  
                        array(
                                'label'   => 'knowsAbout',
                                'id'      => 'saswp_scholarlyarticle_knowsabout_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => '',
                                'note'    => 'Note: If There are more than one knows about, Separate knows about list by comma ( , )',
                                'attributes' => array(
                                        'placeholder' => 'eg: Financial services, negotiation, CRM, Project Management, Mentoring, Learning & Development'
                                        ),   
                        ),
                        array(
                                'label'   => 'Speakable',
                                'id'      => 'saswp_scholarlyarticle_speakable_'.$schema_id,
                                'type'    => 'checkbox',
                        )
                        );
                        break;

                        case 'VisualArtwork':                                        
                                $meta_field = array(
                                        array(
                                                'label'      => 'ID',
                                                'id'         => 'saswp_visualartwork_id_'.$schema_id,
                                                'type'       => 'text',
                                                'default'    => 'VisualArtwork'   
                                        ),                               
                                array(
                                        'label'   => 'URL',
                                        'id'      => 'saswp_visualartwork_url_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => get_permalink(),
                                ),
                                array(
                                        'label'   => 'Name',
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
                                        'label'   => 'Description',
                                        'id'      => 'saswp_visualartwork_description_'.$schema_id,
                                        'type'    => 'textarea',
                                        'default' => saswp_strip_all_tags(get_the_excerpt())
                                ),
                                array(
                                        'label'   => 'Art form',
                                        'id'      => 'saswp_visualartwork_artform_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => '',
                                ),
                                array(
                                        'label'   => 'Art Edition',
                                        'id'      => 'saswp_visualartwork_artedition_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => '',
                                ),
                                array(
                                        'label'   => 'Art Work Surface',
                                        'id'      => 'saswp_visualartwork_artwork_surface_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => '',
                                ),
                                array(
                                        'label'   => 'Width',
                                        'id'      => 'saswp_visualartwork_width_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => '',
                                ),
                                array(
                                        'label'   => 'Height',
                                        'id'      => 'saswp_visualartwork_height_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => '',
                                ),
                                array(
                                        'label'   => 'Art Medium',
                                        'id'      => 'saswp_visualartwork_artmedium_'.$schema_id,
                                        'type'    => 'textarea',
                                        'default' => '',
                                        'note'    => 'Note: If There are more than one artmedium, Separate artmedium list by comma ( , )'                                 
                                ),
                                array(
                                        'label'   => 'Image',
                                        'id'      => 'saswp_visualartwork_image_'.$schema_id,
                                        'type'    => 'media'                            
                                ),                                                                        
                                array(
                                        'label'   => 'Date Created',
                                        'id'      => 'saswp_visualartwork_date_created_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => get_the_date("Y-m-d")
                                ),                                 
                                array(
                                        'label'   => 'Creator Type',
                                        'id'      => 'saswp_visualartwork_author_type_'.$schema_id,
                                        'type'    => 'select',
                                        'options' => array(
                                                'Person'           => 'Person',
                                                'Organization'     => 'Organization',                        
                                        )
                                ),
                                array(
                                        'label'   => 'Creator Name',
                                        'id'      => 'saswp_visualartwork_author_name_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => is_object($current_user) ? $current_user->display_name : ''
                                ),
                                array(
                                        'label'   => 'Creator Description',
                                        'id'      => 'saswp_visualartwork_author_description_'.$schema_id,
                                        'type'    => 'textarea',
                                        'default' => $author_desc
                                ),
                                array(
                                        'label'   => 'Creator URL',
                                        'id'      => 'saswp_visualartwork_author_url_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => $author_url
                                ),
                                array(
                                        'label'   => 'Size',
                                        'id'      => 'saswp_visualartwork_size_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => ''
                                ),
                                array(
                                        'label'   => 'License',
                                        'id'      => 'saswp_visualartwork_license_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => ''
                                )                                                                                       
                                );
                                break;

                        case 'EducationalOccupationalProgram':                                        
                                $meta_field = array(          
                                array(
                                        'label'      => 'ID',
                                        'id'         => 'saswp_eop_id_'.$schema_id,
                                        'type'       => 'text',
                                        'default'    => 'EducationalOccupationalProgram'   
                                ),                      
                                array(
                                        'label'   => 'Name',
                                        'id'      => 'saswp_eop_name_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => saswp_strip_all_tags(get_the_excerpt())
                                ),
                                array(
                                        'label'   => 'Description',
                                        'id'      => 'saswp_eop_description_'.$schema_id,
                                        'type'    => 'textarea',
                                        'default' => saswp_strip_all_tags(get_the_excerpt())
                                ),
                                array(
                                        'label'   => 'URL',
                                        'id'      => 'saswp_eop_url_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => get_permalink(),
                                ),    
                                array(
                                        'label'   => 'Image',
                                        'id'      => 'saswp_eop_image_'.$schema_id,
                                        'type'    => 'media'                            
                                ),
                                array(
                                        'label'   => 'Time To Complete',
                                        'id'      => 'saswp_eop_time_to_complete_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => 'P2Y'
                                         ),
                                        'default' => ''
                                ),
                                array(
                                        'label'   => 'Occupational Category',
                                        'id'      => 'saswp_eop_occupational_category_'.$schema_id,
                                        'type'    => 'textarea',
                                        'attributes' => array(
                                                'placeholder' => '15-1111, 15-1121, 15-1122, 15-1131'
                                         ),
                                        'default' => ''
                                ),
                                array(
                                        'label'   => 'Occupational Credential Awarded',
                                        'id'      => 'saswp_eop_occupational_credential_awarded_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => 'Associate Degree'
                                         ),
                                        'default' => ''
                                ),
                                array(
                                        'label'   => 'Program Prerequisites',
                                        'id'      => 'saswp_eop_program_prerequisites_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => 'HighSchool'
                                         ),
                                        'default' => ''
                                ),
                                array(
                                        'label'   => 'Application StartDate',
                                        'id'      => 'saswp_eop_application_start_date_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => '2019-05-14'
                                         ),
                                        'default' => ''
                                ),
                                array(
                                        'label'   => 'Application Deadline',
                                        'id'      => 'saswp_eop_application_deadline_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => '2019-09-14'
                                         ),
                                        'default' => ''
                                ),
                                array(
                                        'label'   => 'Start Date',
                                        'id'      => 'saswp_eop_start_date_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => '2019-10-01'
                                         ),
                                        'default' => ''
                                ),
                                array(
                                        'label'   => 'End Date',
                                        'id'      => 'saswp_eop_end_date_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => '2021-10-01'
                                         ),
                                        'default' => ''
                                ),
                                array(
                                        'label'   => 'Day Of Week',
                                        'id'      => 'saswp_eop_day_of_week_'.$schema_id,
                                        'type'    => 'textarea',
                                        'attributes' => array(
                                                'placeholder' => 'Wednesday, Thursday'
                                         ),
                                        'note' => 'Note: Separate it by comma ( , )' ,
                                        'default' => ''
                                ),
                                array(
                                        'label'   => 'Time Of Day',
                                        'id'      => 'saswp_eop_time_of_day_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => 'Morning'
                                         ),                                        
                                        'default' => ''
                                ),
                                array(
                                        'label'   => 'Number Of Credits',
                                        'id'      => 'saswp_eop_number_of_credits_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => '30'
                                         ),                                        
                                        'default' => ''
                                ),
                                array(
                                        'label'   => 'Typical Credits PerTerm',
                                        'id'      => 'saswp_eop_typical_credits_per_term_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => '12'
                                         ),                                        
                                        'default' => ''
                                ),
                                array(
                                        'label'   => 'Term Duration',
                                        'id'      => 'saswp_eop_term_duration_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => 'P4M'
                                         ),                                        
                                        'default' => ''
                                ),
                                array(
                                        'label'   => 'Terms PerYear',
                                        'id'      => 'saswp_eop_terms_per_year_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => '2'
                                         ),                                        
                                        'default' => ''
                                ),
                                array(
                                        'label'   => 'Maximum Enrollment',
                                        'id'      => 'saswp_eop_maximum_enrollment_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => '30'
                                         ),                                        
                                        'default' => ''
                                ),
                                array(
                                        'label'   => 'Educational Program Mode',
                                        'id'      => 'saswp_eop_educational_program_mode_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => 'IN_PERSON'
                                         ),                                        
                                        'default' => ''
                                ),
                                array(
                                        'label'   => 'Financial Aid Eligible',
                                        'id'      => 'saswp_eop_financial_aid_eligible_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => 'PUBLIC_AID'
                                         ),                                        
                                        'default' => ''
                                ), 
                                array(
                                        'label'   => 'Provider Name',
                                        'id'      => 'saswp_eop_provider_name_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => 'ACME Community College'
                                         ),
                                        'default' => ''
                                ),
                                array(
                                        'label'   => 'Provider Street Address',
                                        'id'      => 'saswp_eop_provider_street_address_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => '123 Main Street'
                                         ),
                                        'default' => ''
                                ),
                                array(
                                        'label'   => 'Provider Address Locality',
                                        'id'      => 'saswp_eop_provider_address_locality_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => 'Boston'
                                         ),
                                        'default' => ''
                                ),
                                array(
                                        'label'   => 'Provider Address Region',
                                        'id'      => 'saswp_eop_provider_address_region_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => 'MA'
                                         ),
                                        'default' => ''
                                ),
                                array(
                                        'label'   => 'Provider Address Country',
                                        'id'      => 'saswp_eop_provider_address_country_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => 'US'
                                         ),
                                        'default' => ''
                                ),
                                array(
                                        'label'   => 'Provider postalCode',
                                        'id'      => 'saswp_eop_provider_postal_code_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => '02134'
                                         ),
                                        'default' => ''
                                ),
                                array(
                                        'label'   => 'Provider Telephone',
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
                                'label'      => 'ID',
                                'id'         => 'saswp_creativework_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'CreativeWork'   
                                ),
                                array(
                                        'label'   => 'Main Entity Of Page',
                                        'id'      => 'saswp_creativework_main_entity_of_page_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => get_permalink()
                                ),
                                array(
                                        'label'   => 'URL',
                                        'id'      => 'saswp_creativework_url_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => get_permalink(),
                                ),    
                                array(
                                        'label'   => 'Image',
                                        'id'      => 'saswp_creativework_image_'.$schema_id,
                                        'type'    => 'media'                            
                                ),
                                array(
                                        'label'   => 'inLanguage',
                                        'id'      => 'saswp_creativework_inlanguage_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => get_bloginfo('language'),
                                ),
                                array(
                                        'label'   => 'Headline',
                                        'id'      => 'saswp_creativework_headline_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => saswp_get_the_title()
                                ),
                                array(
                                        'label'   => 'Description',
                                        'id'      => 'saswp_creativework_description_'.$schema_id,
                                        'type'    => 'textarea',
                                        'default' => saswp_strip_all_tags(get_the_excerpt())
                                ),
                                array(
                                        'label'   => 'Article Section',
                                        'id'      => 'saswp_creativework_section_'.$schema_id,
                                        'type'    => 'textarea',
                                        'default' => saswp_strip_all_tags(get_the_excerpt())
                                ),    
                                array(
                                        'label'   => 'Article Body',
                                        'id'      => 'saswp_creativework_body_'.$schema_id,
                                        'type'    => 'textarea',
                                        'default' => is_object($post) ? saswp_strip_all_tags($post->post_content) : ''
                                ),    
                                array(
                                        'label'   => 'Keywords',
                                        'id'      => 'saswp_creativework_keywords_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => saswp_get_the_tags()
                                ),    
                                array(
                                        'label'   => 'Date Published',
                                        'id'      => 'saswp_creativework_date_published_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => get_the_date("Y-m-d")
                                ), 
                                array(
                                        'label'   => 'Date Modified',
                                        'id'      => 'saswp_creativework_date_modified_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => get_the_modified_date("Y-m-d")
                                ),
                                array(
                                        'label'   => 'Author',
                                        'id'      => 'saswp_creativework_author_global_mapping_'.$schema_id,
                                        'type'    => 'global_mapping'
                                ),
                                array(
                                        'label'   => 'Author Type',
                                        'id'      => 'saswp_creativework_author_type_'.$schema_id,
                                        'type'    => 'select',
                                        'options' => array(
                                                'Person'           => 'Person',
                                                'Organization'     => 'Organization',                        
                                        )
                                ),
                                array(
                                        'label'   => 'Author Name',
                                        'id'      => 'saswp_creativework_author_name_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => is_object($current_user) ? $current_user->display_name : ''
                                ),
                                array(
                                        'label'   => 'Author HonorificSuffix',
                                        'id'      => 'saswp_creativework_author_honorific_suffix_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                                         ),
                                ),
                                array(
                                        'label'   => 'Author Description',
                                        'id'      => 'saswp_creativework_author_description_'.$schema_id,
                                        'type'    => 'textarea',
                                        'default' => $author_desc
                                ),
                                array(
                                        'label'   => 'Author URL',
                                        'id'      => 'saswp_creativework_author_url_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => $author_url
                                ),
                                array(
                                        'label' => 'Author Image URL',
                                        'id' => 'saswp_creativework_author_image_'.$schema_id,
                                        'type' => 'media',
                                        'default' => isset($author_details['url']) ? $author_details['url']: ''
                                ),
                                array(
                                        'label'   => 'JobTitle',
                                        'id'      => 'saswp_creativework_author_jobtitle_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => '',
                                        'attributes' => array(
                                                'placeholder' => 'eg: Editor in Chief'
                                         ),
                                ),
                                array(
                                        'label'   => 'ReviewedBy',
                                        'id'      => 'saswp_creativework_reviewedby_global_mapping_'.$schema_id,
                                        'type'    => 'global_mapping'
                                ),
                                array(
                                        'label'   => 'ReviewedBy Type',
                                        'id'      => 'saswp_creativework_reviewedby_type_'.$schema_id,
                                        'type'    => 'select',
                                        'options' => array(
                                                ""                => "Select",
                                                'Person'           => 'Person',
                                                'Organization'     => 'Organization',                        
                                        )
                                ),
                                array(
                                        'label'   => 'ReviewedBy Name',
                                        'id'      => 'saswp_creativework_reviewedby_name_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => is_object($current_user) ? $current_user->display_name : ''
                                ),
                                array(
                                        'label'   => 'ReviewedBy HonorificSuffix',
                                        'id'      => 'saswp_creativework_reviewedby_honorific_suffix_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                                         ),
                                ),
                                array(
                                        'label'   => 'ReviewedBy Description',
                                        'id'      => 'saswp_creativework_reviewedby_description_'.$schema_id,
                                        'type'    => 'textarea',
                                        'default' => $author_desc
                                ),
                                array(
                                        'label'   => 'ReviewedBy URL',
                                        'id'      => 'saswp_creativework_reviewedby_url_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => $author_url
                                ),

                                array(
                                        'label'   => 'Editor Type',
                                        'id'      => 'saswp_creativework_editor_type_'.$schema_id,
                                        'type'    => 'select',
                                        'options' => array(
                                                 ""                => "Select",
                                                'Person'           => 'Person',
                                                'Organization'     => 'Organization',                        
                                        )
                                ),
                                array(
                                        'label'   => 'Editor Name',
                                        'id'      => 'saswp_creativework_editor_name_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => is_object($current_user) ? $current_user->display_name : ''
                                ),    
                                array(
                                        'label'   => 'Editor HonorificSuffix',
                                        'id'      => 'saswp_creativework_editor_honorific_suffix_'.$schema_id,
                                        'type'    => 'text',
                                        'attributes' => array(
                                                'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                                         ),
                                ),
                                array(
                                        'label'   => 'Editor Description',
                                        'id'      => 'saswp_creativework_editor_description_'.$schema_id,
                                        'type'    => 'textarea',
                                        'default' => $author_desc
                                ),
                                array(
                                        'label'   => 'Editor URL',
                                        'id'      => 'saswp_creativework_editor_url_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => $author_url
                                ),
                                array(
                                        'label' => 'Editor Image URL',
                                        'id' => 'saswp_creativework_editor_image_'.$schema_id,
                                        'type' => 'media',
                                        'default' => isset($author_details['url']) ? $author_details['url']: ''
                                ),

                                array(
                                        'label'   => 'Organization Name',
                                        'id'      => 'saswp_creativework_organization_name_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                                ),
                                array(
                                        'label'   => 'Organization Logo',
                                        'id'      => 'saswp_creativework_organization_logo_'.$schema_id,
                                        'type'    => 'media',
                                        'default' => isset($sd_data['sd_logo']['url']) ? $sd_data['sd_logo']['url']:''
                                ),
                                array(
                                        'label'   => 'About',
                                        'id'      => 'saswp_creativework_about_'.$schema_id,
                                        'type'    => 'textarea',
                                        'default' => '',
                                        'note'    => 'Note: If There are more than one About, Separate About list by comma ( , )',
                                        'attributes' => array(
                                                'placeholder' => 'eg: Apple is March 21 Announcements'
                                        ),
                                ),  
                                array(
                                        'label'   => 'AlumniOf',
                                        'id'      => 'saswp_creativework_alumniof_'.$schema_id,
                                        'type'    => 'textarea',
                                        'default' => '',
                                        'note'    => 'Note: If There are more than one AlumniOf, Separate AlumniOf list by comma ( , )',
                                        'attributes' => array(
                                                'placeholder' => 'eg: City University of New York-Herbert H. Lehman College, Southern New Hampshire University'
                                        ),
                                ),    
                                array(
                                        'label'   => 'knowsAbout',
                                        'id'      => 'saswp_creativework_knowsabout_'.$schema_id,
                                        'type'    => 'textarea',
                                        'default' => '',
                                        'note'    => 'Note: If There are more than one knows about, Separate knows about list by comma ( , )'       
                                ),
                                array(
                                        'label'   => 'Size',
                                        'id'      => 'saswp_creativework_size_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => ''
                                ),
                                array(
                                        'label'   => 'License',
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

                                        foreach($category_detail as $cd){
                                        
                                                $article_section =  $cd->cat_name;
                                        
                                        }

                                }                                

                                $meta_field = array( 
                                array(
                                        'label'      => 'ID',
                                        'id'         => 'saswp_special_announcement_id_'.$schema_id,
                                        'type'       => 'text',
                                        'default'    => 'SpecialAnnouncement'   
                                ),
                                    array(
                                            'label' => 'Name',
                                            'id' => 'saswp_special_announcement_name_'.$schema_id,
                                            'type' => 'text',
                                            'default' => saswp_get_the_title()
                                    ),
                                    array(
                                            'label' => 'Description',
                                            'id' => 'saswp_special_announcement_description_'.$schema_id,
                                            'type' => 'textarea',
                                            'default' => saswp_strip_all_tags(get_the_excerpt())
                                    ),
                                    array(
                                        'label' => 'Quarantine Guidelines',
                                        'id' => 'saswp_special_announcement_quarantine_guidelines_'.$schema_id,
                                        'type' => 'text',
                                        'default' => get_permalink()
                                   ),
                                   array(
                                        'label' => 'NewsUpdates And Guidelines',
                                        'id' => 'saswp_special_announcement_newsupdates_and_guidelines_'.$schema_id,
                                        'type' => 'text',
                                        'default' => get_permalink()
                                   ),     
                                   array(
                                        'label' => 'Disease Prevention Info',
                                        'id' => 'saswp_special_announcement_disease_prevention_info_'.$schema_id,
                                        'type' => 'text',
                                        'default' => get_permalink()
                                   ),                        
                                    array(
                                            'label' => 'Keywords',
                                            'id' => 'saswp_special_announcement_keywords_'.$schema_id,
                                            'type' => 'text',
                                            'default' => saswp_get_the_tags()
                                    ),
                                    array(
                                        'label' => 'Category',
                                        'id'    => 'saswp_special_announcement_category_'.$schema_id,
                                        'type'  => 'text',
                                        'default' => get_permalink()
                                    ),
                                    array(
                                        'label' => 'Date Posted',
                                        'id' => 'saswp_special_announcement_date_posted_'.$schema_id,
                                        'type' => 'text',
                                        'default' => get_the_date("Y-m-d")
                                    ),
                                    array(
                                        'label'   => 'Date Expires',
                                        'id'      => 'saswp_special_announcement_date_expires_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => get_the_date("Y-m-d")
                                    ),    
                                    array(
                                            'label' => 'Date Published',
                                            'id' => 'saswp_special_announcement_date_published_'.$schema_id,
                                            'type' => 'text',
                                            'default' => get_the_date("Y-m-d")
                                    ), 
                                    array(
                                            'label' => 'Date Modified',
                                            'id' => 'saswp_special_announcement_date_modified_'.$schema_id,
                                            'type' => 'text',
                                            'default' => get_the_modified_date("Y-m-d")
                                    ),                           
                                array(
                                        'label'   => 'URL',
                                        'id'      => 'saswp_special_announcement_url_'.$schema_id,
                                        'type'    => 'text',
                                        'default' => get_permalink(),
                                ),    
                                array(
                                        'label' => 'Image',
                                        'id' => 'saswp_special_announcement_image_'.$schema_id,
                                        'type' => 'media'                            
                                ),                    
                                array(
                                        'label'   => 'Author Type',
                                        'id'      => 'saswp_special_announcement_author_type_'.$schema_id,
                                        'type'    => 'select',
                                        'options' => array(
                                                'Person'           => 'Person',
                                                'Organization'     => 'Organization',                        
                                )
                                ),
                                array(
                                        'label' => 'Author Name',
                                        'id' => 'saswp_special_announcement_author_name_'.$schema_id,
                                        'type' => 'text',
                                        'default' => is_object($current_user) ? $current_user->display_name : ''
                                ),
                                array(
                                        'label' => 'Author Description',
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
                                        'label' => 'Organization Name',
                                        'id' => 'saswp_special_announcement_organization_name_'.$schema_id,
                                        'type' => 'text',
                                        'default' => saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                                ),
                                array(
                                        'label' => 'Organization Logo',
                                        'id'    => 'saswp_special_announcement_organization_logo_'.$schema_id,
                                        'type'  => 'media',
                                        'default' => isset($sd_data['sd_logo']['url']) ? $sd_data['sd_logo']['url']:''
                                ),
                                array(
                                        'label' => 'Announcement Location Type',
                                        'id'    => 'saswp_special_announcement_location_type_'.$schema_id,
                                        'type'  => 'select',
                                        'options' => array(
                                                'CovidTestingFacility'  => 'CovidTestingFacility',
                                                'School'                => 'School',                                                
                                        )
                                ), 
                                array(
                                        'label' => 'Announcement Location Name',
                                        'id'    => 'saswp_special_announcement_location_name_'.$schema_id,
                                        'type'  => 'text'                                        
                                ),
                                array(
                                        'label' => 'Announcement Location Street Address',
                                        'id'    => 'saswp_special_announcement_location_street_address_'.$schema_id,
                                        'type'  => 'text'                                        
                                ),
                                array(
                                        'label' => 'Announcement Location Address Locality',
                                        'id'    => 'saswp_special_announcement_location_address_locality_'.$schema_id,
                                        'type'  => 'text'                                        
                                ),
                                array(
                                        'label' => 'Announcement Location Address Region',
                                        'id'    => 'saswp_special_announcement_location_address_region_'.$schema_id,
                                        'type'  => 'text'                                        
                                ),
                                array(
                                        'label' => 'Announcement Location Telephone',
                                        'id'    => 'saswp_special_announcement_location_telephone_'.$schema_id,
                                        'type'  => 'text'                                        
                                ), 
                                array(
                                        'label' => 'Announcement Location URL',
                                        'id'    => 'saswp_special_announcement_location_url_'.$schema_id,
                                        'type'  => 'text'                                        
                                ),
                                array(
                                        'label' => 'Announcement Location Image',
                                        'id'    => 'saswp_special_announcement_location_image_'.$schema_id,
                                        'type'  => 'media'                                        
                                ), 
                                array(
                                        'label' => 'Announcement Location PriceRange',
                                        'id'    => 'saswp_special_announcement_location_price_range_'.$schema_id,
                                        'type'  => 'text'                                        
                                )                                            
                                );
                                break;
                
                case 'Event':
                    
                    $event_type        = get_post_meta($schema_id, 'saswp_event_type', true);                         
                        
                    $meta_field = array(
                        array(
                            'label'   => 'Type',
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
                                'label'   => 'ID',
                                'id'      => 'saswp_event_schema_id_'.$schema_id,
                                'type'    => 'text'                                
                        ),
                        array(
                                'label' => 'Event Status',
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
                                'label' => 'Attendance Mode',
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
                                'label' => 'Name',
                                'id' => 'saswp_event_schema_name_'.$schema_id,
                                'type' => 'text',                                
                        ),
                        array(
                                'label' => 'Description',
                                'id' => 'saswp_event_schema_description_'.$schema_id,
                                'type' => 'textarea',                                
                        ),
                        array(
                                'label' => 'Virtual Location Name',
                                'id'    => 'saswp_event_schema_virtual_location_name_'.$schema_id,
                                'type'  => 'text',                                
                        ),
                        array(
                                'label' => 'Virtual Location URL',
                                'id'    => 'saswp_event_schema_virtual_location_url_'.$schema_id,
                                'type'  => 'text',                                
                        ),
                        array(
                                'label' => 'Location Name',
                                'id' => 'saswp_event_schema_location_name_'.$schema_id,
                                'type' => 'text',                                
                        ),
                        array(
                                'label' => 'Location Street Address',
                                'id' => 'saswp_event_schema_location_streetaddress_'.$schema_id,
                                'type' => 'text',                                
                        ),
                        array(
                                'label' => 'Location Locality',
                                'id' => 'saswp_event_schema_location_locality_'.$schema_id,
                                'type' => 'text',                                
                        ),
                        array(
                                'label' => 'Location Region',
                                'id' => 'saswp_event_schema_location_region_'.$schema_id,
                                'type' => 'text',                                
                        ),
                        array(
                                'label' => 'Location PostalCode',
                                'id' => 'saswp_event_schema_location_postalcode_'.$schema_id,
                                'type' => 'text',                                
                        ),
                        array(
                                'label' => 'Location Country',
                                'id'    => 'saswp_event_schema_location_country_'.$schema_id,
                                'type'  => 'text',                                
                        ),
                        array(
                                'label' => 'Previous Start Date',
                                'id' => 'saswp_event_schema_previous_start_date_'.$schema_id,
                                'type' => 'text',                                
                        ),
                        array(
                                'label' => 'Start Date',
                                'id' => 'saswp_event_schema_start_date_'.$schema_id,
                                'type' => 'text',                                
                        ),
                        array(
                                'label' => 'Start Time',
                                'id'    => 'saswp_event_schema_start_time_'.$schema_id,
                                'type'  => 'text',                                
                        ),
                        array(
                                'label' => 'End Date',
                                'id' => 'saswp_event_schema_end_date_'.$schema_id,
                                'type' => 'text',                                
                        ),
                        array(
                                'label' => 'End Time',
                                'id' => 'saswp_event_schema_end_time_'.$schema_id,
                                'type' => 'text',                                
                        ),                        
                        array(
                                'label'   => 'Schedule Repeat Frequency',
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
                                'label' => 'Schedule byDay',
                                'id'    => 'saswp_event_schema_schedule_by_day_'.$schema_id,
                                'type'  => 'textarea',
                                'attributes' => array(
                                        'placeholder' => 'Monday, Wednesday'
                                 ),
                                'note' => 'Note: Separate it by comma ( , )'                                  
                        ),
                        array(
                                'label' => 'Schedule byMonthDay',
                                'id'    => 'saswp_event_schema_schedule_by_month_day_'.$schema_id,
                                'type'  => 'text',
                                'attributes' => array(
                                        'placeholder' => '1, 13, 24'
                                 )                                                                 
                        ),
                        array(
                                'label'  => 'Schedule Timezone',
                                'id'     => 'saswp_event_schema_schedule_timezone_'.$schema_id,
                                'type'   => 'text',
                                'attributes' => array(
                                        'placeholder' => 'Europe/London'
                                 ),                                
                        ),
                        array(
                                'label' => 'Image',
                                'id' => 'saswp_event_schema_image_'.$schema_id,
                                'type' => 'media',                                
                        ),                        
                        array(
                                'label' => 'Price',
                                'id' => 'saswp_event_schema_price_'.$schema_id,
                                'type' => 'number',                                
                        ),
                        array(
                                'label' => 'High Price',
                                'id'    => 'saswp_event_schema_high_price_'.$schema_id,
                                'type'  => 'number',                                
                        ),
                        array(
                                'label' => 'Low Price',
                                'id'    => 'saswp_event_schema_low_price_'.$schema_id,
                                'type'  => 'number',                                
                        ),
                        array(
                                'label' => 'Price Currency',
                                'id' => 'saswp_event_schema_price_currency_'.$schema_id,
                                'type' => 'text',                                
                        ),
                        array(
                            'label'   => 'Availability',
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
                                'label' => 'Valid From',
                                'id' => 'saswp_event_schema_validfrom_'.$schema_id,
                                'type' => 'text',                                
                        ),
                        array(
                                'label' => 'URL',
                                'id' => 'saswp_event_schema_url_'.$schema_id,
                                'type' => 'text',                                
                        ),                        
                        array(
                                'label' => 'Organizer Name',
                                'id'    => 'saswp_event_schema_organizer_name_'.$schema_id,
                                'type'  => 'text',                                
                        ),
                        array(
                                'label' => 'Organizer URL',
                                'id'    => 'saswp_event_schema_organizer_url_'.$schema_id,
                                'type'  => 'text',                                
                        ),
                        array(
                                'label' => 'Organizer Phone',
                                'id'    => 'saswp_event_schema_organizer_phone_'.$schema_id,
                                'type'  => 'text',                                
                        ),
                        array(
                                'label' => 'Organizer Email',
                                'id'    => 'saswp_event_schema_organizer_email_'.$schema_id,
                                'type'  => 'text',                                
                        ),
                        array(
                                'label' => 'Performer Name',
                                'id'    => 'saswp_event_schema_performer_name_'.$schema_id,
                                'type'  => 'text',                                
                        ),
                    );
                    break;
                
                case 'TechArticle':                                        
                    $meta_field = array(
                        array(
                                'label'      => 'ID',
                                'id'         => 'saswp_tech_article_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'TechArticle'   
                        ),
                    array(
                            'label' => 'Main Entity Of Page',
                            'id' => 'saswp_tech_article_main_entity_of_page_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink()
                    ),
                    array(
                            'label' => 'Image',
                            'id' => 'saswp_tech_article_image_'.$schema_id,
                            'type' => 'media',                            
                    ),
                    array(
                        'label'   => 'inLanguage',
                        'id'      => 'saswp_tech_article_inlanguage_'.$schema_id,
                        'type'    => 'text',
                        'default' => get_bloginfo('language'),
                   ),
                    array(
                            'label' => 'Headline',
                            'id' => 'saswp_tech_article_headline_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_get_the_title()
                    ),
                    array(
                            'label' => 'Description',
                            'id' => 'saswp_tech_article_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => saswp_strip_all_tags(get_the_excerpt())
                    ) , 
                    array(
                            'label' => 'Keywords',
                            'id' => 'saswp_tech_article_keywords_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_get_the_tags()
                    ),     
                    array(
                            'label' => 'Date Published',
                            'id' => 'saswp_tech_article_date_published_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_date("Y-m-d")
                    ), 
                    array(
                            'label' => 'Date Modified',
                            'id' => 'saswp_tech_article_date_modified_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_modified_date("Y-m-d")
                    ),
                    array(
                        'label'   => 'Author',
                        'id'      => 'saswp_tech_article_author_global_mapping_'.$schema_id,
                        'type'    => 'global_mapping'
                    ),
                    array(
                        'label'   => 'Author Type',
                        'id'      => 'saswp_tech_article_author_type_'.$schema_id,
                        'type'    => 'select',
                        'options' => array(
                                'Person'           => 'Person',
                                'Organization'     => 'Organization',                        
                       )
                    ),
                    array(
                            'label' => 'Author Name',
                            'id' => 'saswp_tech_article_author_name_'.$schema_id,
                            'type' => 'text',
                            'default' => is_object($current_user) ? $current_user->display_name : ''
                    ),
                    array(
                        'label'   => 'Author HonorificSuffix',
                        'id'      => 'saswp_tech_article_author_honorific_suffix_'.$schema_id,
                        'type'    => 'text',
                        'attributes' => array(
                                'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                         ),
                    ),
                    array(
                            'label' => 'Author Description',
                            'id' => 'saswp_tech_article_author_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => $author_desc
                    ), 
                    array(
                            'label'   => 'Author URL',
                            'id'      => 'saswp_tech_article_author_url_'.$schema_id,
                            'type'    => 'text',
                            'default' => $author_url
                    ),  
                    array(
                        'label' => 'Author Image URL',
                        'id' => 'saswp_tech_article_author_image_'.$schema_id,
                        'type' => 'media',
                        'default' => isset($author_details['url']) ? $author_details['url']: ''
                    ),
                    array(
                            'label' => 'Author Social Profile',
                            'id' => 'saswp_tech_article_author_social_profile_'.$schema_id,
                            'type' => 'textarea',
                            'default' => '',
                            'note'    => 'Note: If There are more than one social profiles, Separate them by comma ( , )',
                    ),
                    array(
                        'label'   => 'JobTitle',
                        'id'      => 'saswp_tech_article_author_jobtitle_'.$schema_id,
                        'type'    => 'text',
                        'default' => '',
                        'attributes' => array(
                                'placeholder' => 'eg: Editor in Chief'
                         ),
                    ),
                    array(
                        'label'   => 'ReviewedBy',
                        'id'      => 'saswp_tech_article_reviewedby_global_mapping_'.$schema_id,
                        'type'    => 'global_mapping'
                    ),
                    array(
                        'label'   => 'ReviewedBy Type',
                        'id'      => 'saswp_tech_article_reviewedby_type_'.$schema_id,
                        'type'    => 'select',
                        'options' => array(
                                ""                => "Select",
                                'Person'           => 'Person',
                                'Organization'     => 'Organization',                        
                       )
                    ),
                    array(
                            'label' => 'ReviewedBy Name',
                            'id' => 'saswp_tech_article_reviewedby_name_'.$schema_id,
                            'type' => 'text',
                            'default' => is_object($current_user) ? $current_user->display_name : ''
                    ),
                    array(
                        'label'   => 'ReviewedBy HonorificSuffix',
                        'id'      => 'saswp_tech_article_reviewedby_honorific_suffix_'.$schema_id,
                        'type'    => 'text',
                        'attributes' => array(
                                'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                         ),
                    ),
                    array(
                            'label' => 'ReviewedBy Description',
                            'id' => 'saswp_tech_article_reviewedby_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => $author_desc
                    ), 
                    array(
                            'label'   => 'ReviewedBy URL',
                            'id'      => 'saswp_tech_article_reviewedby_url_'.$schema_id,
                            'type'    => 'text',
                            'default' => $author_url
                    ),
                    array(
                        'label'   => 'Editor Type',
                        'id'      => 'saswp_tech_article_editor_type_'.$schema_id,
                        'type'    => 'select',
                        'options' => array(
                                 ""                => "Select",
                                'Person'           => 'Person',
                                'Organization'     => 'Organization',                        
                        )
                     ),
                    array(
                        'label'   => 'Editor Name',
                        'id'      => 'saswp_tech_article_editor_name_'.$schema_id,
                        'type'    => 'text',
                        'default' => is_object($current_user) ? $current_user->display_name : ''
                    ),
                    array(
                        'label'   => 'Editor HonorificSuffix',
                        'id'      => 'saswp_tech_article_editor_honorific_suffix_'.$schema_id,
                        'type'    => 'text',
                        'attributes' => array(
                                'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                         ),
                    ), 
                    array(
                        'label'   => 'Editor Description',
                        'id'      => 'saswp_tech_article_editor_description_'.$schema_id,
                        'type'    => 'textarea',
                        'default' => $author_desc
                    ),
                    array(
                        'label'   => 'Editor URL',
                        'id'      => 'saswp_tech_article_editor_url_'.$schema_id,
                        'type'    => 'text',
                        'default' => $author_url
                     ),
                     array(
                        'label' => 'Editor Image URL',
                        'id' => 'saswp_tech_article_editor_image_'.$schema_id,
                        'type' => 'media',
                        'default' => isset($author_details['url']) ? $author_details['url']: ''
                    ),
                    array(
                            'label' => 'Organization Name',
                            'id' => 'saswp_tech_article_organization_name_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                    ),
                    array(
                            'label' => 'Organization Logo',
                            'id' => 'saswp_tech_article_organization_logo_'.$schema_id,
                            'type' => 'media',
                            'default' => isset($sd_data['sd_logo']) ? $sd_data['sd_logo']['url']:''
                    ),
                    array(
                        'label'   => 'About',
                        'id'      => 'saswp_tech_article_about_'.$schema_id,
                        'type'    => 'textarea',
                        'default' => '',
                        'note'    => 'Note: If There are more than one About, Separate About list by comma ( , )',
                        'attributes' => array(
                                'placeholder' => 'eg: Apple is March 21 Announcements'
                        ),
                    ), 
                    array(
                        'label'   => 'AlumniOf',
                        'id'      => 'saswp_tech_article_alumniof_'.$schema_id,
                        'type'    => 'textarea',
                        'default' => '',
                        'note'    => 'Note: If There are more than one AlumniOf, Separate AlumniOf list by comma ( , )',
                        'attributes' => array(
                                'placeholder' => 'eg: City University of New York-Herbert H. Lehman College, Southern New Hampshire University'
                        ),
                    ),  
                    array(
                        'label'   => 'knowsAbout',
                        'id'      => 'saswp_tech_article_knowsabout_'.$schema_id,
                        'type'    => 'textarea',
                        'default' => '',
                        'note'    => 'Note: If There are more than one knows about, Separate knows about list by comma ( , )'       
                    ),
                    array(
                            'label' => 'Same As',
                            'id'    => 'saswp_tech_article_same_as_'.$schema_id,
                            'type'  => 'textarea',
                            'attributes' => array(
                                'placeholder' => 'Example, Example2'
                            ),
                            'note' => 'Note: Separate same as list by comma ( , )'                     
                        ),                        
                    array(
                            'label' => 'Speakable',
                            'id' => 'saswp_tech_article_speakable_'.$schema_id,
                            'type' => 'checkbox'
                    )
                    );
                    break;
                
                case 'Course':                                        
                    $meta_field = array(
                   array(
                           'label'      => 'ID',
                           'id'         => 'saswp_course_id_'.$schema_id,
                           'type'       => 'text',
                           'default'    => 'Course'   
                        ),
                    array(
                            'label'   => 'Name',
                            'id'      => 'saswp_course_name_'.$schema_id,
                            'type'    => 'text',
                            'default' => saswp_get_the_title()
                    ),
                    array(
                            'label'   => 'Description',
                            'id'      => 'saswp_course_description_'.$schema_id,
                            'type'    => 'textarea',
                            'default' => is_object($post) ? $post->post_excerpt : ''
                    ),
                    array(
                        'label'   => 'Duration',
                        'id'      => 'saswp_course_duration_'.$schema_id,
                        'type'    => 'text'                        
                   ),
                   array(
                        'label'   => 'Course Code',
                        'id'      => 'saswp_course_code_'.$schema_id,
                        'type'    => 'text'                        
                   ),
                   array(
                        'label'   => 'Content Location Name',
                        'id'      => 'saswp_course_content_location_name_'.$schema_id,
                        'type'    => 'text'                        
                   ),                   
                   array(
                        'label'   => 'Content Location Locality',
                        'id'      => 'saswp_course_content_location_locality_'.$schema_id,
                        'type'    => 'text'                        
                   ),
                   array(
                        'label'   => 'Content Location Region',
                        'id'      => 'saswp_course_content_location_region_'.$schema_id,
                        'type'    => 'text'                        
                   ),
                   array(
                        'label'   => 'Content Location Country',
                        'id'      => 'saswp_course_content_location_country_'.$schema_id,
                        'type'    => 'text'                        
                   ),
                   array(
                        'label'   => 'Content Location Postal Code',
                        'id'      => 'saswp_course_content_location_postal_code_'.$schema_id,
                        'type'    => 'text'                        
                   ),
                    array(
                            'label'   => 'URL',
                            'id'      => 'saswp_course_url_'.$schema_id,
                            'type'    => 'text',
                            'default' => get_permalink()
                    ),                     
                    array(
                            'label'   => 'Date Published',
                            'id'      => 'saswp_course_date_published_'.$schema_id,
                            'type'    => 'text',
                            'default' => get_the_date("Y-m-d")
                    ), 
                    array(
                            'label'   => 'Date Modified',
                            'id'      => 'saswp_course_date_modified_'.$schema_id,
                            'type'    => 'text',
                            'default' => get_the_modified_date("Y-m-d")
                    ),                    
                    array(
                            'label'   => 'Provider Name',
                            'id'      => 'saswp_course_provider_name_'.$schema_id,
                            'type'    => 'text',
                            'default' => get_bloginfo()
                    ),
                    array(
                            'label'   => 'Provider SameAs',
                            'id'      => 'saswp_course_sameas_'.$schema_id,
                            'type'    => 'text',
                            'default' => get_home_url() 
                    ),
                    array(
                            'label'   => 'Offer Category',
                            'id'      => 'saswp_course_offer_category_'.$schema_id,
                            'type'    => 'text',
                    ),
                    array(
                            'label'   => 'Offer Price',
                            'id'      => 'saswp_course_offer_price_'.$schema_id,
                            'type'    => 'number',
                    ),
                    array(
                            'label'   => 'Offer Currency',
                            'id'      => 'saswp_course_offer_currency_'.$schema_id,
                            'type'    => 'text',
                    ),
                    array(
                            'label' => 'Aggregate Rating',
                            'id'    => 'saswp_course_enable_rating_'.$schema_id,
                            'type'  => 'checkbox',                            
                    ),
                    array(
                            'label' => 'Rating',
                            'id'    => 'saswp_course_rating_'.$schema_id,
                            'type'  => 'text',                            
                    ),
                    array(
                            'label' => 'Number of Reviews',
                            'id'    => 'saswp_course_review_count_'.$schema_id,
                            'type'  => 'text',                            
                    )                                                     
                    );
                    break;
                
                case 'DiscussionForumPosting':                                        
                    $meta_field = array(
                   array(
                        'label'      => 'ID',
                        'id'         => 'saswp_dfp_id_'.$schema_id,
                        'type'       => 'text',
                        'default'    => 'DiscussionForumPosting'   
                        ),
                    array(
                            'label'   => 'mainEntityOfPage',
                            'id'      => 'saswp_dfp_main_entity_of_page_'.$schema_id,
                            'type'    => 'text',
                            'default' => get_permalink()
                    ),    
                    array(
                            'label' => 'Headline',
                            'id' => 'saswp_dfp_headline_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_get_the_title()
                    ),
                    array(
                            'label' => 'Description',
                            'id' => 'saswp_dfp_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => saswp_strip_all_tags(get_the_excerpt())
                    ) ,    
                    array(
                            'label' => 'URL',
                            'id' => 'saswp_dfp_url_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink()
                    ),
                    array(
                            'label' => 'Image',
                            'id' => 'saswp_dfp_image_'.$schema_id,
                            'type' => 'media',                            
                    ),    
                    array(
                            'label' => 'Date Published',
                            'id' => 'saswp_dfp_date_published_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_date("Y-m-d")
                    ), 
                    array(
                            'label' => 'Date Modified',
                            'id' => 'saswp_dfp_date_modified_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_modified_date("Y-m-d")
                    ),
                    array(
                        'label'   => 'Author Type',
                        'id'      => 'saswp_dfp_author_type_'.$schema_id,
                        'type'    => 'select',
                        'options' => array(
                                'Person'           => 'Person',
                                'Organization'     => 'Organization',                        
                       )
                    ),
                    array(
                            'label' => 'Author Name',
                            'id' => 'saswp_dfp_author_name_'.$schema_id,
                            'type' => 'text',
                            'default' => is_object($current_user) ? $current_user->display_name : ''
                    ),
                    array(
                            'label'   => 'Author Description',
                            'id'      => 'saswp_dfp_author_description_'.$schema_id,
                            'type'    => 'textarea',
                            'default' => $author_desc
                    ),  
                    array(
                            'label'   => 'Author URL',
                            'id'      => 'saswp_dfp_author_url_'.$schema_id,
                            'type'    => 'text',
                            'default' => $author_url
                    ),    
                    array(
                            'label'   => 'Organization Name',
                            'id'      => 'saswp_dfp_organization_name_'.$schema_id,
                            'type'    => 'text',
                            'default' => saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                    ),
                    array(
                            'label'   => 'Organization Logo',
                            'id'      => 'saswp_dfp_organization_logo_'.$schema_id,
                            'type'    => 'media',
                            'default' => isset($sd_data['sd_logo']['url']) ? $sd_data['sd_logo']['url'] : ''
                    ),    
                        
                    );
                    break;
                
                case 'Recipe':
                    $meta_field = array(
                        array(
                                'label'      => 'ID',
                                'id'         => 'saswp_recipe_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'recipe'   
                        ),
                    array(
                            'label' => 'URL',
                            'id' => 'saswp_recipe_url_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink(),
                    ),
                    array(
                            'label' => 'Name',
                            'id' => 'saswp_recipe_name_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_get_the_title()
                    ),
                    array(
                        'label' => 'Description',
                        'id' => 'saswp_recipe_description_'.$schema_id,
                        'type' => 'textarea',
                        'default' => saswp_strip_all_tags(get_the_excerpt())
                    ),
                    array(
                        'label' => 'Image',
                        'id' => 'saswp_recipe_image_'.$schema_id,
                        'type' => 'media'                        
                   ),
                    array(
                            'label' => 'Date Published',
                            'id' => 'saswp_recipe_date_published_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_date("Y-m-d")
                    ),
                    array(
                            'label' => 'Date Modified',
                            'id' => 'saswp_recipe_date_modified_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_modified_date("Y-m-d")
                    ),                    
                    array(
                            'label' => 'Main Entity Id',
                            'id' => 'saswp_recipe_main_entity_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink()
                    ),
                    array(
                        'label'   => 'Author Type',
                        'id'      => 'saswp_recipe_author_type_'.$schema_id,
                        'type'    => 'select',
                        'options' => array(
                                'Person'           => 'Person',
                                'Organization'     => 'Organization',                        
                       )
                    ),
                    array(
                            'label' => 'Author Name',
                            'id' => 'saswp_recipe_author_name_'.$schema_id,
                            'type' => 'text',
                            'default' => is_object($current_user) ? $current_user->display_name : ''
                    ),
                    array(
                            'label' => 'Author Description',
                            'id' => 'saswp_recipe_author_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => $author_desc
                    ),
                    array(
                            'label'   => 'Author URL',
                            'id'      => 'saswp_recipe_author_url_'.$schema_id,
                            'type'    => 'text',
                            'default' => $author_url
                    ),    
                    array(
                            'label' => 'Author Image',
                            'id' => 'saswp_recipe_author_image_'.$schema_id,
                            'type' => 'media',
                            'default' => isset($author_details['url']) ? $author_details['url'] : ''
                    ),
                    array(
                            'label' => 'Organization Name',
                            'id' => 'saswp_recipe_organization_name_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                    ),
                    array(
                            'label' => 'Organization Logo',
                            'id' => 'saswp_recipe_organization_logo_'.$schema_id,
                            'type' => 'media',
                            'default' => isset($sd_data['sd_logo']['url']) ? $sd_data['sd_logo']['url'] : ''
                    ),                                                                                            
                    array(
                            'label' => 'Prepare Time',
                            'id' => 'saswp_recipe_preptime_'.$schema_id,
                            'type' => 'text', 
                            'attributes' => array(
                                'placeholder' => 'PT20M'
                            ),
                    ),    
                    array(
                            'label' => 'Cook Time',
                            'id' => 'saswp_recipe_cooktime_'.$schema_id,
                            'type' => 'text', 
                            'attributes' => array(
                                'placeholder' => 'PT30M'
                            ),
                    ),
                    array(
                            'label' => 'Total Time',
                            'id' => 'saswp_recipe_totaltime_'.$schema_id,
                            'type' => 'text', 
                            'attributes' => array(
                                'placeholder' => 'PT50M'
                            ),
                    ),    
                    array(
                            'label' => 'Keywords',
                            'id' => 'saswp_recipe_keywords_'.$schema_id,
                            'type' => 'text',  
                            'attributes' => array(
                                'placeholder' => 'cake for a party, coffee'
                            ),
                    ),    
                    array(
                            'label' => 'Recipe Yield',
                            'id' => 'saswp_recipe_recipeyield_'.$schema_id,
                            'type' => 'text', 
                            'attributes' => array(
                                'placeholder' => '10 servings'
                            ),
                    ),    
                    array(
                            'label' => 'Recipe Category',
                            'id' => 'saswp_recipe_category_'.$schema_id,
                            'type' => 'text',
                            'attributes' => array(
                                'placeholder' => 'Dessert'
                            ),
                    ),
                    array(
                            'label' => 'Recipe Cuisine',
                            'id' => 'saswp_recipe_cuisine_'.$schema_id,
                            'type' => 'text', 
                            'attributes' => array(
                                'placeholder' => 'American'
                            ),
                    ),    
                    array(
                            'label' => 'Calories',
                            'id' => 'saswp_recipe_nutrition_'.$schema_id,
                            'type' => 'text',
                            'attributes' => array(
                                'placeholder' => '270 calories'
                            ),
                    ),                    
                    array(
                        'label'         => 'Protein',
                        'id'            => 'saswp_recipe_protein_'.$schema_id,
                        'type'          => 'text',
                        'attributes' => array(
                            'placeholder' => '270 grams'
                        ),
                    ),
                    array(
                        'label'         => 'Fat',
                        'id'            => 'saswp_recipe_fat_'.$schema_id,
                        'type'          => 'text',
                        'attributes'    => array(
                            'placeholder' => '270 grams'
                        ),
                    ),
                    array(
                        'label'   => 'Fiber',
                        'id'      => 'saswp_recipe_fiber_'.$schema_id,
                        'type'    => 'text',
                        'attributes' => array(
                            'placeholder' => '270 grams'
                        ),
                    ),
                    array(
                        'label'         => 'Sodium',
                        'id'            => 'saswp_recipe_sodium_'.$schema_id,
                        'type'          => 'text',
                        'attributes' => array(
                            'placeholder' => '270 grams'
                        ),
                    ),
                    array(
                        'label'   => 'Sugar',
                        'id'      => 'saswp_recipe_sugar_'.$schema_id,
                        'type'    => 'text',
                        'attributes' => array(
                            'placeholder' => '270 grams'
                        ),
                    ),
                    array(
                        'label' => 'Carbohydrate',
                        'id'    => 'saswp_recipe_carbohydrate_'.$schema_id,
                        'type'  => 'text',
                        'attributes' => array(
                            'placeholder' => '270 grams'
                        ),
                    ),
                    array(
                        'label' => 'Cholesterol',
                        'id'    => 'saswp_recipe_cholesterol_'.$schema_id,
                        'type'  => 'text',
                        'attributes' => array(
                            'placeholder' => '270 grams'
                        ),
                    ),
                    array(
                        'label' => 'Saturated Fat',
                        'id'    => 'saswp_recipe_saturated_fat_'.$schema_id,
                        'type'  => 'text',
                        'attributes' => array(
                            'placeholder' => '270 grams'
                        ),
                    ),
                    array(
                        'label' => 'Unsaturated Fat',
                        'id'    => 'saswp_recipe_unsaturated_fat_'.$schema_id,
                        'type'  => 'text',
                        'attributes' => array(
                            'placeholder' => '270 grams'
                        ),
                    ),
                    array(
                        'label' => 'Trans Fat',
                        'id'    => 'saswp_recipe_trans_fat_'.$schema_id,
                        'type'  => 'text',
                        'attributes' => array(
                            'placeholder' => '270 grams'
                        ),
                    ),
                    array(
                        'label' => 'Serving Size',
                        'id'    => 'saswp_recipe_serving_size_'.$schema_id,
                        'type'  => 'text',
                        'attributes' => array(
                            'placeholder' => '370 grams'
                        ),
                    ),
                    array(
                            'label' => 'Recipe Ingredient',
                            'id' => 'saswp_recipe_ingredient_'.$schema_id,
                            'type' => 'textarea',
                            'attributes' => array(
                                'placeholder' => '2 cups of flour; 3/4 cup white sugar;'
                            ),
                            'note' => 'Note: Separate Ingredient list by semicolon ( ; )'  
                    ),                     
                    array(
                            'label' => 'Video Name',
                            'id' => 'saswp_recipe_video_name_'.$schema_id,
                            'type' => 'text', 
                            'attributes' => array(
                                'placeholder' => 'Video Name'
                            ),
                    ),                    
                    array(
                            'label' => 'Video Description',
                            'id' => 'saswp_recipe_video_description_'.$schema_id,
                            'type' => 'textarea', 
                            'attributes' => array(
                                'placeholder' => 'Video Description'
                            ),
                    ),
                    array(
                            'label' => 'Video ThumbnailUrl',
                            'id' => 'saswp_recipe_video_thumbnailurl_'.$schema_id,
                            'type' => 'media',
                            
                    ),
                    array(
                            'label' => 'Video ContentUrl',
                            'id' => 'saswp_recipe_video_contenturl_'.$schema_id,
                            'type' => 'text',                            
                            'attributes' => array(
                                'placeholder' => 'http://www.example.com/video123.mp4'
                            ),
                    ),
                    array(
                            'label' => 'Video EmbedUrl',
                            'id' => 'saswp_recipe_video_embedurl_'.$schema_id,
                            'type' => 'text', 
                            'attributes' => array(
                                'placeholder' => 'http://www.example.com/videoplayer?video=123'
                            ),
                    ),
                    array(
                            'label' => 'Video Upload Date',
                            'id' => 'saswp_recipe_video_upload_date_'.$schema_id,
                            'type' => 'text', 
                            'attributes' => array(
                                'placeholder' => '2018-12-18'
                            ),
                    ),
                    array(
                            'label' => 'Video Duration',
                            'id' => 'saswp_recipe_video_duration_'.$schema_id,
                            'type' => 'text',
                            'attributes' => array(
                                'placeholder' => 'PT1M33S'
                            ),
                    ), 
                    array(
                        'label' => 'Recipe Instructions',
                        'id' => 'saswp_recipe_instructions_'.$schema_id,
                        'type' => 'textarea',
                        'attributes' => array(
                            'placeholder' => 'Preheat the oven to 350 degrees F. Grease and flour a 9x9 inch pan; large bowl, combine flour, sugar, baking powder, and salt. pan.;'
                        ),
                        'note' => 'Note: Separate Instructions step by semicolon ( ; ). If you want to add images. Use below repeater "Add Recipe Instructions"'  
                   ),   
                    array(
                        'label' => 'Aggregate Rating',
                        'id' => 'saswp_recipe_schema_enable_rating_'.$schema_id,
                        'type' => 'checkbox',                            
                    ),
                    array(
                        'label' => 'Rating',
                        'id' => 'saswp_recipe_schema_rating_'.$schema_id,
                        'type' => 'text',                            
                    ),
                    array(
                        'label' => 'Number of Reviews',
                        'id' => 'saswp_recipe_schema_review_count_'.$schema_id,
                        'type' => 'text',                            
                    )

                    );
                    break;

                    case 'PsychologicalTreatment':                                                                                                            
                        
                        $meta_field = array(
                        array(
                                'label'      => 'ID',
                                'id'         => 'saswp_psychological_treatment_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'PsychologicalTreatment'   
                        ), 
                        array(
                                'label'   => 'Name',
                                'id'      => 'saswp_psychological_treatment_name_'.$schema_id,
                                'type'    => 'text'                                                 
                        ),
                        array(
                                'label'   => 'Description',
                                'id'      => 'saswp_psychological_treatment_description_'.$schema_id,
                                'type'    => 'textarea'                                                 
                        ),
                        array(
                                'label'   => 'URL',
                                'id'      => 'saswp_psychological_treatment_url_'.$schema_id,
                                'type'    => 'text'                                                 
                        ),
                        array(
                                'label'   => 'Image',
                                'id'      => 'saswp_psychological_treatment_image_'.$schema_id,
                                'type'    => 'media'                                                 
                        ),
                        array(
                                'label'   => 'Drug',
                                'id'      => 'saswp_psychological_treatment_drug_'.$schema_id,
                                'type'    => 'text'                                                 
                        ),    
                        array(
                                'label'   => 'Body Location',
                                'id'      => 'saswp_psychological_treatment_body_location_'.$schema_id,
                                'type'    => 'text'                                                 
                        ),
                        array(
                                'label'   => 'Preparation',
                                'id'      => 'saswp_psychological_treatment_preparation_'.$schema_id,
                                'type'    => 'text'                                                 
                        ),
                        array(
                                'label'   => 'Followup',
                                'id'      => 'saswp_psychological_treatment_followup_'.$schema_id,
                                'type'    => 'text'                                                 
                        ),
                        array(
                                'label'   => 'How Performed',
                                'id'      => 'saswp_psychological_treatment_how_performed_'.$schema_id,
                                'type'    => 'text'                                                 
                        ),
                        array(
                                'label'   => 'Procedure Type',
                                'id'      => 'saswp_psychological_treatment_procedure_type_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                        'Surgical'           => 'Surgical',
                                        'Noninvasive'        => 'Noninvasive',
                                        'Percutaneous'       => 'Percutaneous'                                        
                               )                                                         
                        ) ,
                        array(
                                'label'   => 'MedicalCode',
                                'id'      => 'saswp_psychological_treatment_medical_code_'.$schema_id,
                                'type'    => 'text'                                                 
                        ), 
                        array(
                                'label'   => 'Additional Type',
                                'id'      => 'saswp_psychological_treatment_additional_type_'.$schema_id,
                                'type'    => 'text'                                                 
                        ),                              
                            
                        );
                        
                        break;

                    case 'RealEstateListing':                                                                                                            
                        
                        $meta_field = array(
                        array(
                                'label'      => 'ID',
                                'id'         => 'saswp_real_estate_listing_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'RealEstateListing'   
                        ),
                        array(
                                'label'   => 'Date Posted',
                                'id'      => 'saswp_real_estate_listing_date_posted_'.$schema_id,
                                'type'    => 'text', 
                                'default' => get_the_date("Y-m-d")                                
                        ),    
                        array(
                                'label'   => 'Name',
                                'id'      => 'saswp_real_estate_listing_name_'.$schema_id,
                                'type'    => 'text', 
                                'default' => saswp_get_the_title()                                
                        ),
                        array(
                                'label'   => 'URL',
                                'id'      => 'saswp_real_estate_listing_url_'.$schema_id,
                                'type'    => 'text',     
                                'default' => get_permalink()
                        ),    
                        array(
                                'label'   => 'Description',
                                'id'      => 'saswp_real_estate_listing_description_'.$schema_id,
                                'type'    => 'textarea', 
                                'default' => saswp_strip_all_tags(get_the_excerpt())
                        ), 
                        array(
                                'label'    => 'Image',
                                'id'       => 'saswp_real_estate_listing_image_'.$schema_id,
                                'type'     => 'media',                           
                         ),                        
                            array(
                                'label'   => 'Price',
                                'id'      => 'saswp_real_estate_listing_price_'.$schema_id,
                                'type'    => 'text'                                
                         ),
                         array(
                                'label'   => 'Currency',
                                'id'      => 'saswp_real_estate_listing_currency_'.$schema_id,
                                'type'    => 'text'                                
                          ),
                            array(
                                'label'   => 'Price Valid From',
                                'id'      => 'saswp_real_estate_listing_validfrom_'.$schema_id,
                                'type'    => 'text'                                
                           ),                            
                            array(
                                'label'   => 'Availability',
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
                                        'label' => 'Location Name',
                                        'id' => 'saswp_real_estate_listing_location_name_'.$schema_id,
                                        'type' => 'text',                                
                                ),
                                array(
                                        'label' => 'Location Street Address',
                                        'id' => 'saswp_real_estate_listing_streetaddress_'.$schema_id,
                                        'type' => 'text',                                
                                ),
                                array(
                                        'label' => 'Location Locality',
                                        'id' => 'saswp_real_estate_listing_locality_'.$schema_id,
                                        'type' => 'text',                                
                                ),
                                array(
                                        'label' => 'Location Region',
                                        'id' => 'saswp_real_estate_listing_region_'.$schema_id,
                                        'type' => 'text',                                
                                ),
                                array(
                                        'label' => 'Location PostalCode',
                                        'id' => 'saswp_real_estate_listing_postalcode_'.$schema_id,
                                        'type' => 'text',                                
                                ),
                                array(
                                        'label' => 'Location Country',
                                        'id'    => 'saswp_real_estate_listing_country_'.$schema_id,
                                        'type'  => 'text',                                
                                ),
                                array(
                                        'label' => 'Location Phone',
                                        'id'    => 'saswp_real_estate_listing_phone_'.$schema_id,
                                        'type'  => 'text',                                
                                )                                                                                
                            
                        );
                        
                        break;
                        
                        case 'RentAction':                                                                                                            
                        
                                $meta_field = array(
                                        array(
                                                'label'      => 'ID',
                                                'id'         => 'saswp_rent_action_id_'.$schema_id,
                                                'type'       => 'text',
                                                'default'    => 'RentAction'   
                                        ), 
                                        array(
                                                'label'   => 'Agent Name',
                                                'id'      => 'saswp_rent_action_agent_name_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        ),
                                        array(
                                                'label'   => 'Land Lord Name',
                                                'id'      => 'saswp_rent_action_land_lord_name_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        ),
                                        array(
                                                'label'   => 'Object Name',
                                                'id'      => 'saswp_rent_action_object_name_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        )    
                                                                    
                                );
                                
                                break;  

                        case 'Audiobook':                                                                                                            
                        
                                $meta_field = array(
                                        array(
                                                'label'      => 'ID',
                                                'id'         => 'saswp_audiobook_id_'.$schema_id,
                                                'type'       => 'text',
                                                'default'    => 'Audiobook'   
                                        ),
                                        array(
                                                'label'   => 'Name',
                                                'id'      => 'saswp_audiobook_name_'.$schema_id,
                                                'type'    => 'text',
                                                'default' => get_the_title()                                                                
                                        ),
                                        array(
                                                'label'   => 'Description',
                                                'id'      => 'saswp_audiobook_description_'.$schema_id,
                                                'type'    => 'textarea',
                                                'default' => saswp_strip_all_tags(get_the_excerpt())                                                                  
                                        ),
                                        array(
                                                'label'   => 'URL',
                                                'id'      => 'saswp_audiobook_url_'.$schema_id,
                                                'type'    => 'text', 
                                                'default' => get_permalink()                                                               
                                        ),
                                        array(
                                                'label'   => 'Image',
                                                'id'      => 'saswp_audiobook_image_'.$schema_id,
                                                'type'    => 'media'                                                                
                                        ),
                                        array(
                                                'label'      => 'DatePublished',
                                                'id'         => 'saswp_audiobook_date_published_'.$schema_id,
                                                'type'       => 'text',
                                                'default'    => get_the_date("Y-m-d")                            
                                        ),
                                        array(
                                                'label'      => 'DateModified',
                                                'id'         => 'saswp_audiobook_date_modified_'.$schema_id,
                                                'type'       => 'text',
                                                'default'    => get_the_modified_date("Y-m-d")                            
                                        ),
                                        array(
                                                'label'   => 'Author Type',
                                                'id'      => 'saswp_audiobook_author_type_'.$schema_id,
                                                'type'    => 'select',
                                                'options' => array(
                                                        'Person'           => 'Person',
                                                        'Organization'     => 'Organization',                        
                                                )
                                        ),
                                        array(
                                                'label'   => 'Author Name',
                                                'id'      => 'saswp_audiobook_author_name_'.$schema_id,
                                                'type'    => 'text',
                                                'default' => is_object($current_user) ? $current_user->display_name : ''    
                                        ),
                                        array(
                                                'label'   => 'Author Description',
                                                'id'      => 'saswp_audiobook_author_description_'.$schema_id,
                                                'type'    => 'textarea',
                                                'default' => $author_desc
                                        ), 
                                        array(
                                                'label'   => 'Author URL',
                                                'id'      => 'saswp_audiobook_author_url_'.$schema_id,
                                                'type'    => 'text',
                                                'default' => $author_url
                                        ),    
                                        array(
                                                'label'   => 'Author Image',
                                                'id'      => 'saswp_audiobook_author_image_'.$schema_id,
                                                'type'    => 'media',
                                                'default' => isset($author_details['url']) ? $author_details['url'] : ''   
                                        ),                                   
                                        array(
                                                'label'      => 'Publisher',
                                                'id'         => 'saswp_audiobook_publisher_'.$schema_id,
                                                'type'       => 'text',                           
                                        ),
                                        array(
                                                'label'      => 'Provider',
                                                'id'         => 'saswp_audiobook_provider_'.$schema_id,
                                                'type'       => 'text',                           
                                        ),
                                        array(
                                                'label'      => 'Read By',
                                                'id'         => 'saswp_audiobook_readby_'.$schema_id,
                                                'type'       => 'text',                           
                                        ),                                                                                
                                        array(
                                                'label'      => 'Content URL',
                                                'id'         => 'saswp_audiobook_content_url_'.$schema_id,
                                                'type'       => 'text',                           
                                        ),
                                        array(
                                                'label'      => 'Duration',
                                                'id'         => 'saswp_audiobook_duration_'.$schema_id,
                                                'type'       => 'text',                           
                                        ),
                                        array(
                                                'label'      => 'Encoding Format',
                                                'id'         => 'saswp_audiobook_encoding_format_'.$schema_id,
                                                'type'       => 'text',                           
                                        ),
                                        array(
                                                'label'      => 'Player Type',
                                                'id'         => 'saswp_audiobook_player_type_'.$schema_id,
                                                'type'       => 'text',                           
                                        ),
                                        array(
                                                'label'      => 'Main Entity Of Page',
                                                'id'         => 'saswp_audiobook_main_entity_of_page_'.$schema_id,
                                                'type'       => 'text',                           
                                        )
                                );
                                        
                        break;  

                        case 'HotelRoom':                                                                                                            
                 
                                $meta_field = array(
                                        array(
                                                'label'      => 'ID',
                                                'id'         => 'saswp_hotelroom_hotel_id_'.$schema_id,
                                                'type'       => 'text',
                                                'default'    => 'HotelRoom'   
                                        ),
                                        array(
                                                'label'   => 'Hotel Name',
                                                'id'      => 'saswp_hotelroom_hotel_name_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        ),
                                        array(
                                                'label'   => 'Hotel Image',
                                                'id'      => 'saswp_hotelroom_hotel_image_'.$schema_id,
                                                'type'    => 'media'                                                                
                                        ),
                                        array(
                                                'label'   => 'Hotel Description',
                                                'id'      => 'saswp_hotelroom_hotel_description_'.$schema_id,
                                                'type'    => 'textarea'                                                                
                                        ),
                                        array(
                                                'label'   => 'Hotel Price Range',
                                                'id'      => 'saswp_hotelroom_hotel_price_range_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        ),
                                        array(
                                                'label'   => 'Hotel Telephone',
                                                'id'      => 'saswp_hotelroom_hotel_telephone_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        ),
                                        array(
                                                'label' => 'Hotel Street Address',
                                                'id' => 'saswp_hotelroom_hotel_streetaddress_'.$schema_id,
                                                'type' => 'text',                                
                                        ),
                                        array(
                                                'label' => 'Hotel Locality',
                                                'id'    => 'saswp_hotelroom_hotel_locality_'.$schema_id,
                                                'type'  => 'text',                                
                                        ),
                                        array(
                                                'label' => 'Hotel Region',
                                                'id'    => 'saswp_hotelroom_hotel_region_'.$schema_id,
                                                'type'  => 'text',                                
                                        ),
                                        array(
                                                'label' => 'Hotel PostalCode',
                                                'id'    => 'saswp_hotelroom_hotel_postalcode_'.$schema_id,
                                                'type'  => 'text',                                
                                        ),
                                        array(
                                                'label' => 'Hotel Country',
                                                'id'    => 'saswp_hotelroom_hotel_country_'.$schema_id,
                                                'type'  => 'text',                                
                                        ),
                                        array(
                                                'label' => 'Hotel Room Name',
                                                'id'    => 'saswp_hotelroom_name_'.$schema_id,
                                                'type'  => 'text',                                
                                        ),
                                        array(
                                                'label' => 'Hotel Room Description',
                                                'id'    => 'saswp_hotelroom_description_'.$schema_id,
                                                'type'  => 'textarea',                                
                                        ),
                                        array(
                                                'label' => 'Hotel Room Image',
                                                'id'    => 'saswp_hotelroom_image_'.$schema_id,
                                                'type'  => 'media',                                
                                        ),
                                        array(
                                                'label' => 'Hotel Room Offer name',
                                                'id'    => 'saswp_hotelroom_offer_name_'.$schema_id,
                                                'type'  => 'text',                                
                                        ),
                                        array(
                                                'label' => 'Hotel Room Offer Terms & Condition',
                                                'id'    => 'saswp_hotelroom_offer_description_'.$schema_id,
                                                'type'  => 'textarea',                                
                                        ),
                                        array(
                                                'label' => 'Hotel Room Offer Price',
                                                'id'    => 'saswp_hotelroom_offer_price_'.$schema_id,
                                                'type'  => 'text',                                
                                        ),
                                        array(
                                                'label' => 'Hotel Room Offer Price Currency',
                                                'id'    => 'saswp_hotelroom_offer_price_currency_'.$schema_id,
                                                'type'  => 'text',                                
                                        ),
                                        array(
                                                'label' => 'Hotel Room Offer Price UnitCode',
                                                'id'    => 'saswp_hotelroom_offer_unitcode_'.$schema_id,
                                                'type'  => 'text',                                
                                        ),
                                        array(
                                                'label' => 'Hotel Room Offer Price Valid Through',
                                                'id'    => 'saswp_hotelroom_offer_validthrough_'.$schema_id,
                                                'type'  => 'text',                                
                                        ),
                                );
                        break;

                        case 'PodcastEpisode':                                                                                                            
                
                                $meta_field = array(
                                        array(
                                                'label'      => 'ID',
                                                'id'         => 'saswp_podcast_episode_id_'.$schema_id,
                                                'type'       => 'text',
                                                'default'    => 'PodcastEpisode'   
                                        ),
                                        array(
                                                'label'   => 'Name',
                                                'id'      => 'saswp_podcast_episode_name_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        ),
                                        array(
                                                'label'   => 'Description',
                                                'id'      => 'saswp_podcast_episode_description_'.$schema_id,
                                                'type'    => 'textarea'                                                                
                                        ),
                                        array(
                                                'label'   => 'URL',
                                                'id'      => 'saswp_podcast_episode_url_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        ),
                                        array(
                                                'label'   => 'Image',
                                                'id'      => 'saswp_podcast_episode_image_'.$schema_id,
                                                'type'    => 'media'                                                                
                                        ),
                                        array(
                                                'label'      => 'DatePublished',
                                                'id'         => 'saswp_podcast_episode_date_published_'.$schema_id,
                                                'type'       => 'text',
                                                'default'    => get_the_date("Y-m-d")                            
                                        ),
                                        array(
                                                'label'      => 'DateModified',
                                                'id'         => 'saswp_podcast_episode_date_modified_'.$schema_id,
                                                'type'       => 'text',
                                                'default'    => get_the_modified_date("Y-m-d")                            
                                        ),
                                        array(
                                                'label'      => 'Time Required',
                                                'id'         => 'saswp_podcast_episode_timeRequired_'.$schema_id,
                                                'type'       => 'text'                                                         
                                        ),
                                        array(
                                                'label'      => 'Content URL',
                                                'id'         => 'saswp_podcast_episode_content_url_'.$schema_id,
                                                'type'       => 'text'                                                         
                                        ),
                                        array(
                                                'label'      => 'PodcastSeries Name',
                                                'id'         => 'saswp_podcast_episode_series_name_'.$schema_id,
                                                'type'       => 'text'                                                         
                                        ),
                                        array(
                                                'label'      => 'PodcastSeries URL',
                                                'id'         => 'saswp_podcast_episode_series_url_'.$schema_id,
                                                'type'       => 'text'                                                         
                                        )                                                                                             
                                );
                                
                        break;                  

                        case 'PodcastSeason':                                                                                                            
                
                                $meta_field = array(
                                        array(
                                                'label'      => 'ID',
                                                'id'         => 'saswp_podcast_season_id_'.$schema_id,
                                                'type'       => 'text',
                                                'default'    => 'PodcastEpisode'   
                                        ),
                                        array(
                                                'label'   => 'Name',
                                                'id'      => 'saswp_podcast_season_name_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        ),
                                        array(
                                                'label'   => 'Description',
                                                'id'      => 'saswp_podcast_season_description_'.$schema_id,
                                                'type'    => 'textarea'                                                                
                                        ),
                                        array(
                                                'label'   => 'URL',
                                                'id'      => 'saswp_podcast_season_url_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        ),
                                        array(
                                                'label'   => 'Image',
                                                'id'      => 'saswp_podcast_season_image_'.$schema_id,
                                                'type'    => 'media'                                                                
                                        ),
                                        array(
                                                'label'      => 'DatePublished',
                                                'id'         => 'saswp_podcast_season_date_published_'.$schema_id,
                                                'type'       => 'text',
                                                'default'    => get_the_date("Y-m-d")                            
                                        ),
                                        array(
                                                'label'      => 'DateModified',
                                                'id'         => 'saswp_podcast_season_date_modified_'.$schema_id,
                                                'type'       => 'text',
                                                'default'    => get_the_modified_date("Y-m-d")                            
                                        ),
                                        array(
                                                'label'      => 'Season Number',
                                                'id'         => 'saswp_podcast_season_number_'.$schema_id,
                                                'type'       => 'text'                                                         
                                        ),
                                        array(
                                                'label'      => 'Number of seasons',
                                                'id'         => 'saswp_podcast_season_number_of_seasons_'.$schema_id,
                                                'type'       => 'text'                                                         
                                        ),                                        
                                        array(
                                                'label'      => 'PodcastSeries Name',
                                                'id'         => 'saswp_podcast_season_series_name_'.$schema_id,
                                                'type'       => 'text'                                                         
                                        ),
                                        array(
                                                'label'      => 'PodcastSeries URL',
                                                'id'         => 'saswp_podcast_season_series_url_'.$schema_id,
                                                'type'       => 'text'                                                         
                                        )                                                                                             
                                );
                                
                        break;                  

                        case 'EducationalOccupationalCredential':                                                                                                            
                        
                                $meta_field = array(
                                        array(
                                                'label'      => 'ID',
                                                'id'         => 'saswp_eoc_id_'.$schema_id,
                                                'type'       => 'text',
                                                'default'    => 'EducationalOccupationalCredential'   
                                        ),
                                        array(
                                                'label'   => 'Additional Type',
                                                'id'      => 'saswp_eoc_additional_type_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        ),
                                        array(
                                                'label'   => 'Name',
                                                'id'      => 'saswp_eoc_name_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        ),
                                        array(
                                                'label'   => 'Alternative Name',
                                                'id'      => 'saswp_eoc_alt_name_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        ),
                                        array(
                                                'label'   => 'Description',
                                                'id'      => 'saswp_eoc_description_'.$schema_id,
                                                'type'    => 'textarea'                                                                
                                        ),                                        
                                        
                                        array(
                                                'label'   => 'Educational Level Name',
                                                'id'      => 'saswp_eoc_e_lavel_name_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        ),
                                        array(
                                                'label'   => 'Educational Level DefinedTermSet',
                                                'id'      => 'saswp_eoc_e_lavel_definedtermset_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        ),

                                        array(
                                                'label'   => 'Credential Category Name',
                                                'id'      => 'saswp_eoc_c_category_name_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        ),
                                        array(
                                                'label'   => 'Credential Category Term Code',
                                                'id'      => 'saswp_eoc_c_category_term_code_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        ),
                                        array(
                                                'label'   => 'Credential Category DefinedTermSet',
                                                'id'      => 'saswp_eoc_c_category_definedtermset_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        ),

                                        array(
                                                'label'   => 'Competency Required Name',
                                                'id'      => 'saswp_eoc_c_required_name_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        ),
                                        array(
                                                'label'   => 'Competency Required Term Code',
                                                'id'      => 'saswp_eoc_c_required_term_code_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        ),
                                        array(
                                                'label'   => 'Competency Required DefinedTermSet',
                                                'id'      => 'saswp_eoc_c_required_definedtermset_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        ),
                                        array(
                                                'label'   => 'Competency Required URL',
                                                'id'      => 'saswp_eoc_c_required_url_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        )                                        

                                );
                                
                        break;          
                                
                        case 'ApartmentComplex':                                                                                                            
                
                                $meta_field = array(
                                        array(
                                                'label'      => 'ID',
                                                'id'         => 'saswp_apartment_complex_id_'.$schema_id,
                                                'type'       => 'text',
                                                'default'    => 'ApartmentComplex'   
                                        ),
                                        array(
                                                'label'   => 'Name',
                                                'id'      => 'saswp_apartment_complex_name_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        ),
                                        array(
                                                'label'   => 'Description',
                                                'id'      => 'saswp_apartment_complex_description_'.$schema_id,
                                                'type'    => 'textarea'                                                                
                                        ),
                                        array(
                                                'label'   => 'URL',
                                                'id'      => 'saswp_apartment_complex_url_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        ),
                                        array(
                                                'label'   => 'Image',
                                                'id'      => 'saswp_apartment_complex_image_'.$schema_id,
                                                'type'    => 'media'                                                                
                                        ),
                                        array(
                                                'label'   => 'Number Of Bedrooms',
                                                'id'      => 'saswp_apartment_complex_no_of_bedrooms_'.$schema_id,
                                                'type'    => 'number'                                                                
                                        ),
                                        array(
                                                'label'   => 'Pets Allowed',
                                                'id'      => 'saswp_apartment_complex_pets_allowed_'.$schema_id,
                                                'type'    => 'text'                                                                
                                        ),                                        
                                        array(
                                                'label' => 'Location Street Address',
                                                'id' => 'saswp_apartment_complex_streetaddress_'.$schema_id,
                                                'type' => 'text',                                
                                        ),
                                        array(
                                                'label' => 'Location Locality',
                                                'id' => 'saswp_apartment_complex_locality_'.$schema_id,
                                                'type' => 'text',                                
                                        ),
                                        array(
                                                'label' => 'Location Region',
                                                'id' => 'saswp_apartment_complex_region_'.$schema_id,
                                                'type' => 'text',                                
                                        ),
                                        array(
                                                'label' => 'Location PostalCode',
                                                'id' => 'saswp_apartment_complex_postalcode_'.$schema_id,
                                                'type' => 'text',                                
                                        ),
                                        array(
                                                'label' => 'Location Country',
                                                'id'    => 'saswp_apartment_complex_country_'.$schema_id,
                                                'type'  => 'text',                                
                                        ),
                                        array(
                                                'label' => 'Location Phone',
                                                'id'    => 'saswp_apartment_complex_phone_'.$schema_id,
                                                'type'  => 'text',                                
                                        ),
                                        array(
                                                'label'      => 'GeoCoordinates Latitude',
                                                'id'         => 'saswp_apartment_complex_latitude_'.$schema_id,
                                                'type'       => 'text',
                                                'attributes' => array(
                                                    'placeholder' => '17.412'
                                                ), 
                                        ),
                                        array(
                                                'label'      => 'GeoCoordinates Longitude',
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
                    
                        $service = new saswp_output_service();
                        $product_details = $service->saswp_woocommerce_product_details($post_id);     
                        
                    }
                     
                    $meta_field = array(                        
                    array(
                            'label'   => 'Name',
                            'id'      => 'saswp_product_schema_name_'.$schema_id,
                            'type'    => 'text',     
                            'default' => saswp_remove_warnings($product_details, 'product_name', 'saswp_string')
                    ),
                    array(
                            'label'   => 'ID',
                            'id'      => 'saswp_product_schema_id_'.$schema_id,
                            'type'    => 'text'                        
                    ),
                    array(
                            'label'   => 'URL',
                            'id'      => 'saswp_product_schema_url_'.$schema_id,
                            'type'    => 'text',     
                            'default' => get_permalink()
                    ),    
                    array(
                            'label'   => 'Description',
                            'id'      => 'saswp_product_schema_description_'.$schema_id,
                            'type'    => 'textarea', 
                            'default' => saswp_remove_warnings($product_details, 'product_description', 'saswp_string')
                    ), 
                        array(
                            'label'    => 'Image',
                            'id'       => 'saswp_product_schema_image_'.$schema_id,
                            'type'     => 'media',                           
                        ),
                        array(
                                'label'    => 'Brand Name',
                                'id'       => 'saswp_product_schema_brand_name_'.$schema_id,
                                'type'     => 'text',
                                'default' => saswp_remove_warnings($product_details, 'product_brand', 'saswp_string')
                        ),
                        array(
                                'label'    => 'Brand URL',
                                'id'       => 'saswp_product_schema_brand_url_'.$schema_id,
                                'type'     => 'text'                                 
                        ),
                        array(
                                'label'    => 'Brand Image',
                                'id'       => 'saswp_product_schema_brand_image_'.$schema_id,
                                'type'     => 'media'                               
                        ),
                        array(
                                'label'    => 'Brand Logo',
                                'id'       => 'saswp_product_schema_brand_logo_'.$schema_id,
                                'type'     => 'media'                               
                        ),                        
                        array(
                                'label'   => 'Price',
                                'id'      => 'saswp_product_schema_price_'.$schema_id,
                                'type'    => 'text',
                                'default' => saswp_remove_warnings($product_details, 'product_price', 'saswp_string')
                       ),
                        array(
                                'label'   => 'High Price',
                                'id'      => 'saswp_product_schema_high_price_'.$schema_id,
                                'type'    => 'text',
                                'default' => saswp_remove_warnings($product_details, 'product_price', 'saswp_string')
                        ),
                        array(
                                'label'   => 'Low Price',
                                'id'      => 'saswp_product_schema_low_price_'.$schema_id,
                                'type'    => 'text',
                                'default' => saswp_remove_warnings($product_details, 'product_price', 'saswp_string')
                        ),
                        array(
                                'label'   => 'Offer Count',
                                'id'      => 'saswp_product_schema_offer_count_'.$schema_id,
                                'type'    => 'text',                                
                        ),
                        array(
                            'label'   => 'Price Valid Until',
                            'id'      => 'saswp_product_schema_priceValidUntil_'.$schema_id,
                            'type'    => 'text',
                            'default' => saswp_remove_warnings($product_details, 'product_priceValidUntil', 'saswp_string')    
                       ),
                        array(
                            'label'   => 'Currency',
                            'id'      => 'saswp_product_schema_currency_'.$schema_id,
                            'type'    => 'text', 
                            'default' => saswp_remove_warnings($product_details, 'product_currency', 'saswp_string')    
                       ),
                       array(
                        'label'   => 'VAT',
                        'id'      => 'saswp_product_schema_vat_'.$schema_id,
                        'type'    => 'text', 
                        'default' => saswp_remove_warnings($product_details, 'product_vat', 'saswp_string')    
                   ),
                        array(
                            'label'   => 'Availability',
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
                            'label'   => 'Condition',
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
                            'label'   => 'SKU',
                            'id'      => 'saswp_product_schema_sku_'.$schema_id,
                            'type'    => 'text', 
                            'default' => saswp_remove_warnings($product_details, 'product_sku', 'saswp_string')    
                      ),
                        array(
                            'label'   => 'MPN',
                            'id'      => 'saswp_product_schema_mpn_'.$schema_id,
                            'type'    => 'text',
                            'note'    => 'OR',                            
                            'default' => saswp_remove_warnings($product_details, 'product_mpn', 'saswp_string')
                       ),                       
                        array(
                            'label'   => 'GTIN8',
                            'id'      => 'saswp_product_schema_gtin8_'.$schema_id,
                            'type'    => 'text',  
                            'note'    => 'OR',  
                            'default' => saswp_remove_warnings($product_details, 'product_gtin8', 'saswp_string')    
                       ),
                        array(
                                'label'   => 'GTIN13',
                                'id'      => 'saswp_product_schema_gtin13_'.$schema_id,
                                'type'    => 'text',  
                                'default' => saswp_remove_warnings($product_details, 'product_gtin13', 'saswp_string')    
                        ),
                        array(
                                'label'   => 'GTIN12',
                                'id'      => 'saswp_product_schema_gtin12_'.$schema_id,
                                'type'    => 'text',  
                                'default' => saswp_remove_warnings($product_details, 'product_gtin12', 'saswp_string')    
                        ),
                        array(
                                'label'   => 'Color',
                                'id'      => 'saswp_product_schema_color_'.$schema_id,
                                'type'    => 'text'                                
                        ),
                        array(
                            'label' => 'Seller Organization',
                            'id'    => 'saswp_product_schema_seller_'.$schema_id,
                            'type'  => 'text',                             
                       ),
                       array(
                        'label' => 'Additional Type',
                        'id'    => 'saswp_product_additional_type_'.$schema_id,
                        'type'  => 'text',                             
                       ),
                       array(
                            'label'   => 'Return Policy Applicable Country Code',
                            'id'      => 'saswp_product_schema_rp_country_code_'.$schema_id,
                            'type'    => 'text',
                            'attributes' => array(
                                'placeholder' => 'US'
                            ),
                        ),
                        array(
                            'label'   => 'Return Policy Category',
                            'id'      => 'saswp_product_schema_rp_category_'.$schema_id,
                            'type'    => 'select',
                            'options' => array(
                                     'MerchantReturnFiniteReturnWindow'                 => 'MerchantReturnFiniteReturnWindow',
                                     'MerchantReturnNotPermitted'                       => 'MerchantReturnNotPermitted',
                                     'MerchantReturnUnlimitedWindow'                    => 'MerchantReturnUnlimitedWindow',
                                     'MerchantReturnUnspecified'                        => 'MerchantReturnUnspecified',
                            )
                        ),
                        array(
                            'label'   => 'Return Policy Merchant Return Days',
                            'id'      => 'saswp_product_schema_rp_return_days_'.$schema_id,
                            'type'    => 'number',
                            'attributes' => array(
                                'placeholder' => '5'
                            ),
                        ),
                        array(
                            'label'   => 'Return Policy Return Method',
                            'id'      => 'saswp_product_schema_rp_return_method_'.$schema_id,
                            'type'    => 'select',
                            'options' => array(
                                    'ReturnAtKiosk'     => 'ReturnAtKiosk',
                                    'ReturnByMail'      => 'ReturnByMail',
                                    'ReturnInStore'     => 'ReturnInStore',
                            )
                        ),
                        array(
                            'label'   => 'Return Policy Return Fees',
                            'id'      => 'saswp_product_schema_rp_return_fees_'.$schema_id,
                            'type'    => 'select',
                            'options' => array(
                                    'FreeReturn'                        => 'FreeReturn',
                            )
                        ),
                        array(
                            'label'   => 'Shipping Rate Value',
                            'id'      => 'saswp_product_schema_sr_value_'.$schema_id,
                            'type'    => 'text',
                            'attributes' => array(
                                'placeholder' => '3.8'
                            ),
                        ),
                        array(
                            'label'   => 'Shipping Rate Currency',
                            'id'      => 'saswp_product_schema_sr_currency_'.$schema_id,
                            'type'    => 'text',
                            'default' => 'USD',
                            'attributes' => array(
                                'placeholder' => 'USD'
                            ),
                        ),
                        array(
                            'label'   => 'Shipping Destination Locality',
                            'id'      => 'saswp_product_schema_sa_locality_'.$schema_id,
                            'type'    => 'text',
                            'attributes' => array(
                                'placeholder' => 'New York'
                            ),                        
                        ),
                        array(
                            'label'   => 'Shipping Destination Region',
                            'id'      => 'saswp_product_schema_sa_region_'.$schema_id,
                            'type'    => 'text', 
                            'attributes' => array(
                                'placeholder' => 'NY'
                            ),                       
                        ),
                        array(
                            'label'   => 'Shipping Destination Postal Code',
                            'id'      => 'saswp_product_schema_sa_postal_code_'.$schema_id,
                            'type'    => 'text',  
                            'attributes' => array(
                                'placeholder' => '10019'
                            ),                      
                        ),
                        array(
                            'label'   => 'Shipping Destination Street Address',
                            'id'      => 'saswp_product_schema_sa_address_'.$schema_id,
                            'type'    => 'textarea', 
                            'attributes' => array(
                                'placeholder' => '148 W 51st St'
                            ),                       
                        ),
                        array(
                            'label'   => 'Shipping Destination Country',
                            'id'      => 'saswp_product_schema_sa_country_'.$schema_id,
                            'type'    => 'text', 
                            'attributes' => array(
                                'placeholder' => 'US'
                            ),                       
                        ),
                        array(
                            'label'   => 'Shipping Handling Time Min Value',
                            'id'      => 'saswp_product_schema_sdh_minval_'.$schema_id,
                            'type'    => 'number',
                            'attributes' => array(
                                'placeholder' => '0'
                            ),                        
                        ),
                        array(
                            'label'   => 'Shipping Handling Time Max Value',
                            'id'      => 'saswp_product_schema_sdh_maxval_'.$schema_id,
                            'type'    => 'number',
                            'attributes' => array(
                                'placeholder' => '1'
                            ),                        
                        ),
                        array(
                            'label'   => 'Shipping Handling Time Unit Code',
                            'id'      => 'saswp_product_schema_sdh_unitcode_'.$schema_id,
                            'type'    => 'text',     
                            'note'    => 'Note: Enter unit code as DAY',
                            'default' => 'DAY', 
                            'attributes' => array(
                                'placeholder' => 'DAY'
                            ),                 
                        ),
                        array(
                            'label'   => 'Shipping Transit Time Min Value',
                            'id'      => 'saswp_product_schema_sdt_minval_'.$schema_id,
                            'type'    => 'number', 
                            'attributes' => array(
                                'placeholder' => '2'
                            ),                       
                        ),
                        array(
                            'label'   => 'Shipping Transit Time Max Value',
                            'id'      => 'saswp_product_schema_sdt_maxval_'.$schema_id,
                            'type'    => 'number',  
                            'attributes' => array(
                                'placeholder' => '5'
                            ),                      
                        ),
                        array(
                            'label'   => 'Shipping Transit Time Unit Code',
                            'id'      => 'saswp_product_schema_sdt_unitcode_'.$schema_id,
                            'type'    => 'text',     
                            'note'    => 'Note: Enter unit code as DAY',
                            'default' => 'DAY',  
                            'attributes' => array(
                                'placeholder' => 'DAY'
                            ),                 
                        ),
                        array(
                            'label'   => 'Return Shipping Fees Name',
                            'id'      => 'saswp_product_schema_rsf_name_'.$schema_id,
                            'type'    => 'text'                       
                        ),
                        array(
                            'label'   => 'Return Shipping Fees Value',
                            'id'      => 'saswp_product_schema_rsf_value_'.$schema_id,
                            'type'    => 'number', 
                            'attributes' => array(
                                'placeholder' => '100'
                            ),                       
                        ),
                        array(
                            'label'   => 'Return Shipping Fees Currency',
                            'id'      => 'saswp_product_schema_rsf_currency_'.$schema_id,
                            'type'    => 'text', 
                            'attributes' => array(
                                'placeholder' => 'USD'
                            ),                       
                        ),
                        array(
                            'label' => 'Aggregate Rating',
                            'id'    => 'saswp_product_schema_enable_rating_'.$schema_id,
                            'type'  => 'checkbox',                            
                        ),                       
                        array(
                            'label'   => 'Rating',
                            'id'      => 'saswp_product_schema_rating_'.$schema_id,
                            'type'    => 'text',
                            'default' => saswp_remove_warnings($product_details, 'product_average_rating', 'saswp_string')
                        ),
                        array(
                            'label'   => 'Number of Reviews',
                            'id'      => 'saswp_product_schema_review_count_'.$schema_id,
                            'type'    => 'text',
                            'default' => saswp_remove_warnings($product_details, 'product_review_count', 'saswp_string')
                        ),
                    );
                    
                    break;
                
                case 'Service':
                    
                    $meta_field = array(
                        array(
                                'label'      => 'ID',
                                'id'         => 'saswp_service_schema_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'Service'   
                        ),
                    array(
                            'label' => 'Name',
                            'id'    => 'saswp_service_schema_name_'.$schema_id,
                            'type'  => 'text',                    
                    ),
                    array(
                            'label' => 'URL',
                            'id'    => 'saswp_service_schema_url_'.$schema_id,
                            'type'  => 'text',                    
                    ),    
                    array(
                        'label' => 'Image',
                        'id' => 'saswp_service_schema_image_'.$schema_id,
                        'type' => 'media',                            
                     ),
                    array(
                            'label' => 'Service Type',
                            'id' => 'saswp_service_schema_type_'.$schema_id,
                            'type' => 'text',                            
                    ),
                    array(
                        'label' => 'Provider Mobility',
                        'id'    => 'saswp_service_schema_provider_mobility_'.$schema_id,
                        'type'  => 'text',                            
                    ),
                    array(
                            'label' => 'Provider Name',
                            'id' => 'saswp_service_schema_provider_name_'.$schema_id,
                            'type' => 'text',                           
                    ),
                    array(
                            'label' => 'Provider Type',
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
                            'label' => 'Locality',
                            'id' => 'saswp_service_schema_locality_'.$schema_id,
                            'type' => 'text',                            
                    ),
                    array(
                            'label' => 'Postal Code',
                            'id' => 'saswp_service_schema_postal_code_'.$schema_id,
                            'type' => 'text',                           
                    ),
                    array(
                            'label' => 'Address Country',
                            'id' => 'saswp_service_schema_country_'.$schema_id,
                            'type' => 'text',                           
                    ),    
                    array(
                            'label' => 'Telephone',
                            'id' => 'saswp_service_schema_telephone_'.$schema_id,
                            'type' => 'text',                            
                    ), 
                    array(
                        'label' => 'Price Range',
                        'id'    => 'saswp_service_schema_price_range_'.$schema_id,
                        'type'  => 'text',                            
                    ),                    
                    array(
                            'label' => 'Description',
                            'id' => 'saswp_service_schema_description_'.$schema_id,
                            'type' => 'textarea',                           
                    ),
                    array(
                            'label' => 'Area Served (City)',
                            'id' => 'saswp_service_schema_area_served_'.$schema_id,
                            'type' => 'textarea',                           
                            'note'   => 'Note: Enter all the City name in comma separated',
                            'attributes' => array(
                                'placeholder' => 'New York, Los Angeles'
                            ),
                    ),
                    array(
                            'label' => 'Service Offer',
                            'id' => 'saswp_service_schema_service_offer_'.$schema_id,
                            'type' => 'textarea',                           
                            'note'   => 'Note: Enter all the service offer in comma separated',
                            'attributes' => array(
                                'placeholder' => 'Apartment light cleaning, carpet cleaning'
                            )                                                        
                        ),
                        array(
                                'label' => 'Additional Type',
                                'id'    => 'saswp_service_schema_additional_type_'.$schema_id,
                                'type'  => 'text',                           
                        ),
                        array(
                                'label' => 'Service Output',
                                'id'    => 'saswp_service_schema_service_output_'.$schema_id,
                                'type'  => 'text',                           
                        ),                                                
                        array(
                                'label' => 'Aggregate Rating',
                                'id'    => 'saswp_service_schema_enable_rating_'.$schema_id,
                                'type'  => 'checkbox',                           
                            ),
                        array(
                                'label' => 'Rating',
                                'id'    => 'saswp_service_schema_rating_value_'.$schema_id,
                                'type'  => 'text',                           
                            ),
                        array(
                                'label' => 'Rating Count',
                                'id'    => 'saswp_service_schema_rating_count_'.$schema_id,
                                'type'  => 'text',                            
                        )
                            
                    );
                    break;

                    case 'TaxiService':
                    
                        $meta_field = array(
                        array(
                                'label'      => 'ID',
                                'id'         => 'saswp_taxi_service_schema_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'TaxiService'   
                        ),
                        array(
                                'label' => 'Name',
                                'id'    => 'saswp_taxi_service_schema_name_'.$schema_id,
                                'type'  => 'text',                    
                        ),
                        array(
                                'label' => 'URL',
                                'id'    => 'saswp_taxi_service_schema_url_'.$schema_id,
                                'type'  => 'text',                    
                        ),    
                        array(
                            'label' => 'Image',
                            'id' => 'saswp_taxi_service_schema_image_'.$schema_id,
                            'type' => 'media',                            
                         ),
                        array(
                                'label' => 'Service Type',
                                'id' => 'saswp_taxi_service_schema_type_'.$schema_id,
                                'type' => 'text',                            
                        ),
                        array(
                                'label' => 'Provider Name',
                                'id' => 'saswp_taxi_service_schema_provider_name_'.$schema_id,
                                'type' => 'text',                           
                        ),
                        array(
                                'label' => 'Provider Type',
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
                                'label' => 'Locality',
                                'id' => 'saswp_taxi_service_schema_locality_'.$schema_id,
                                'type' => 'text',                            
                        ),
                        array(
                                'label' => 'Postal Code',
                                'id' => 'saswp_taxi_service_schema_postal_code_'.$schema_id,
                                'type' => 'text',                           
                        ),
                        array(
                                'label' => 'Address Country',
                                'id' => 'saswp_taxi_service_schema_country_'.$schema_id,
                                'type' => 'text',                           
                        ),    
                        array(
                                'label' => 'Telephone',
                                'id' => 'saswp_taxi_service_schema_telephone_'.$schema_id,
                                'type' => 'text',                            
                        ), 
                        array(
                            'label' => 'Price Range',
                            'id'    => 'saswp_taxi_service_schema_price_range_'.$schema_id,
                            'type'  => 'text',                            
                        ),                    
                        array(
                                'label' => 'Description',
                                'id' => 'saswp_taxi_service_schema_description_'.$schema_id,
                                'type' => 'textarea',                           
                        ),
                        array(
                                'label' => 'Area Served (City)',
                                'id' => 'saswp_taxi_service_schema_area_served_'.$schema_id,
                                'type' => 'textarea',                           
                                'note'   => 'Note: Enter all the City name in comma separated',
                                'attributes' => array(
                                    'placeholder' => 'New York, Los Angeles'
                                ),
                        ),
                        array(
                                'label' => 'Service Offer',
                                'id' => 'saswp_taxi_service_schema_service_offer_'.$schema_id,
                                'type' => 'textarea',                           
                                'note'   => 'Note: Enter all the service offer in comma separated',
                                'attributes' => array(
                                    'placeholder' => 'Apartment light cleaning, carpet cleaning'
                                )                                                        
                            ),
                            array(
                                    'label' => 'Additional Type',
                                    'id'    => 'saswp_taxi_service_schema_additional_type_'.$schema_id,
                                    'type'  => 'text',                           
                            ),
                            array(
                                    'label' => 'Service Output',
                                    'id'    => 'saswp_taxi_service_schema_service_output_'.$schema_id,
                                    'type'  => 'text',                           
                            )                        
                        );
                        break;    
                
                case 'Review':
                                        
                        $meta_field[] = array(
                            'label' => 'Review Name',
                            'id'    => 'saswp_review_name_'.$schema_id,
                            'type'  => 'text',              
                            'default' => get_the_title()             
                        );
                        $meta_field[] = array(
                            'label' => 'Review Description',
                            'id' => 'saswp_review_description_'.$schema_id,
                            'type' => 'textarea',                           
                            'default' => saswp_strip_all_tags(get_the_excerpt())                         
                        );
                        $meta_field[] = array(
                                'label' => 'Review Body',
                                'id'    => 'saswp_review_body_'.$schema_id,
                                'type'   => 'textarea',                           
                                'default' => saswp_strip_all_tags(get_the_excerpt())                         
                        );                        
                        $meta_field[] = array(
                            'label' => 'Review Author',
                            'id' => 'saswp_review_author_'.$schema_id,
                            'type' => 'text',                            
                            'default' => is_object($current_user) ?  $current_user->display_name : ''
                        );
                        $meta_field[] = array(
                            'label' => 'Review Author URL',
                            'id' => 'saswp_review_author_url_'.$schema_id,
                            'type' => 'text',
                            'default' => $author_url                           
                        );
                        $meta_field[] = array(
                            'label' => 'Review Publisher',
                            'id' => 'saswp_review_publisher_'.$schema_id,
                            'type' => 'text',   
                            'default'=> saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')                        
                        );
                        $meta_field[] = array(
                                'label' => 'Review Publisher URL',
                                'id'    => 'saswp_review_publisher_url'.$schema_id,
                                'type'  => 'text',                           
                                'default' => get_home_url() 
                            );
                        $meta_field[] = array(
                            'label' => 'Review Published Date',
                            'id' => 'saswp_review_date_published_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_date("Y-m-d")                           
                        );
                        $meta_field[] = array(
                                'label' => 'Review Modified Date',
                                'id' => 'saswp_review_date_modified_'.$schema_id,
                                'type' => 'text',
                                'default' => get_the_modified_date("Y-m-d")                           
                            );
                        $meta_field[] = array(
                            'label' => 'Review URL',
                            'id' => 'saswp_review_url_'.$schema_id,
                            'type' => 'text',               
                            'default' => get_permalink()             
                        ); 
                        $meta_field[] = array(
                            'label' => 'Review Rating',
                            'id'    => 'saswp_review_enable_rating_'.$schema_id,
                            'type'  => 'checkbox',                           
                        );
                        $meta_field[] = array(
                            'label' => 'Rating Value',
                            'id'    => 'saswp_review_rating_'.$schema_id,
                            'type'  => 'text',                            
                        );
                        $meta_field[] = array(
                            'label' => 'Best Rating',
                            'id'    => 'saswp_review_review_count_'.$schema_id,
                            'type'  => 'text',                            
                        );
                        $meta_field[] = array(
                                'label' => 'Worst Rating',
                                'id'    => 'saswp_review_worst_count_'.$schema_id,
                                'type'  => 'text',                            
                        );
                        
                        if($manual == null){
                         
                            $meta_field[] = array(
                            'label'   => 'Item Reviewed Type',
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
                                'label'      => 'ID',
                                'id'         => 'saswp_audio_schema_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'AudioObject'   
                        ), 
                    array(
                            'label' => 'Name',
                            'id' => 'saswp_audio_schema_name_'.$schema_id,
                            'type' => 'text', 
                            'default'=> saswp_get_the_title()                          
                    ),
                    array(
                            'label' => 'Description',
                            'id' => 'saswp_audio_schema_description_'.$schema_id,
                            'type' => 'textarea',            
                            'default' => saswp_strip_all_tags(get_the_excerpt())                
                    ),
                    array(
                            'label' => 'Content Url',
                            'id' => 'saswp_audio_schema_contenturl_'.$schema_id,
                            'type' => 'text',  
                            'default' => get_permalink()                          
                    ),
                   array(
                            'label' => 'Duration',
                            'id' => 'saswp_audio_schema_duration_'.$schema_id,
                            'type' => 'text',                            
                    ),
                     array(
                            'label' => 'Encoding Format',
                            'id' => 'saswp_audio_schema_encoding_format_'.$schema_id,
                            'type' => 'text',                           
                    ),                           
                    array(
                            'label' => 'Date Published',
                            'id' => 'saswp_audio_schema_date_published_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_date("Y-m-d")
                    ),
                    array(
                            'label' => 'Date Modified',
                            'id' => 'saswp_audio_schema_date_modified_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_modified_date("Y-m-d")
                    ),
                    array(
                        'label'   => 'Author Type',
                        'id'      => 'saswp_audio_schema_author_type_'.$schema_id,
                        'type'    => 'select',
                        'options' => array(
                                'Person'           => 'Person',
                                'Organization'     => 'Organization',                        
                       )
                    ),
                    array(
                            'label' => 'Author Name',
                            'id' => 'saswp_audio_schema_author_name_'.$schema_id,
                            'type' => 'text',  
                            'default' => is_object($current_user) ? $current_user->display_name : ''                          
                    ),
                    array(
                            'label'   => 'Author Description',
                            'id'      => 'saswp_audio_schema_author_description_'.$schema_id,
                            'type'    => 'textarea',
                            'default' => $author_desc
                    ),
                    array(
                            'label'   => 'Author URL',
                            'id'      => 'saswp_audio_schema_author_url_'.$schema_id,
                            'type'    => 'text',
                            'default' => $author_url
                    )                                                  
                    );
                    break;
                
                case 'SoftwareApplication':
                                        
                    $meta_field = array(
                        array(
                                'label'      => 'ID',
                                'id'         => 'saswp_software_schema_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'SoftwareApplication'   
                        ),
                    array(
                            'label' => 'Name',
                            'id' => 'saswp_software_schema_name_'.$schema_id,
                            'type' => 'text',                           
                    ),
                    array(
                            'label' => 'Description',
                            'id' => 'saswp_software_schema_description_'.$schema_id,
                            'type' => 'textarea',                            
                    ),
                    array(
                            'label' => 'Image',
                            'id'    => 'saswp_software_schema_image_'.$schema_id,
                            'type'  => 'media',                            
                    ),    
                    array(
                            'label' => 'Operating System',
                            'id' => 'saswp_software_schema_operating_system_'.$schema_id,
                            'type' => 'text',                            
                    ),
                    array(
                            'label' => 'Application Category',
                            'id' => 'saswp_software_schema_application_category_'.$schema_id,
                            'type' => 'text',                            
                    ),
                    array(
                            'label' => 'Price',
                            'id' => 'saswp_software_schema_price_'.$schema_id,
                            'type' => 'text',                            
                    ),
                    array(
                            'label' => 'Price Currency',
                            'id' => 'saswp_software_schema_price_currency_'.$schema_id,
                            'type' => 'text',                           
                    ),                            
                    array(
                            'label' => 'Date Published',
                            'id' => 'saswp_software_schema_date_published_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_date("Y-m-d")
                    ),
                    array(
                            'label' => 'Date Modified',
                            'id' => 'saswp_software_schema_date_modified_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_modified_date("Y-m-d")
                    ),
                    array(
                            'label' => 'Aggregate Rating',
                            'id' => 'saswp_software_schema_enable_rating_'.$schema_id,
                            'type' => 'checkbox',                           
                        ),
                    array(
                            'label' => 'Rating',
                            'id' => 'saswp_software_schema_rating_'.$schema_id,
                            'type' => 'text',                           
                        ),
                    array(
                            'label' => 'Rating Count',
                            'id' => 'saswp_software_schema_rating_count_'.$schema_id,
                            'type' => 'text',                            
                        ),    
                    );
                    break;

                    case 'MobileApplication':
                                        
                        $meta_field = array(
                        array(
                                'label'      => 'ID',
                                'id'         => 'saswp_mobile_app_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'MobileApplication'   
                        ),
                        
                        array(
                                'label' => 'Name',
                                'id' => 'saswp_mobile_app_schema_name_'.$schema_id,
                                'type' => 'text',                           
                        ),
                        array(
                                'label' => 'Description',
                                'id' => 'saswp_mobile_app_schema_description_'.$schema_id,
                                'type' => 'textarea',                            
                        ),
                        array(
                                'label' => 'Image',
                                'id'    => 'saswp_mobile_app_schema_image_'.$schema_id,
                                'type'  => 'media',                            
                        ),    
                        array(
                                'label' => 'Operating System',
                                'id' => 'saswp_mobile_app_schema_operating_system_'.$schema_id,
                                'type' => 'text',                            
                        ),
                        array(
                                'label' => 'Application Category',
                                'id' => 'saswp_mobile_app_schema_application_category_'.$schema_id,
                                'type' => 'text',                            
                        ),
                        array(
                                'label' => 'Price',
                                'id' => 'saswp_mobile_app_schema_price_'.$schema_id,
                                'type' => 'text',                            
                        ),
                        array(
                                'label' => 'Price Currency',
                                'id' => 'saswp_mobile_app_schema_price_currency_'.$schema_id,
                                'type' => 'text',                           
                        ),                            
                        array(
                                'label' => 'Date Published',
                                'id' => 'saswp_mobile_app_schema_date_published_'.$schema_id,
                                'type' => 'text',
                                'default' => get_the_date("Y-m-d")
                        ),
                        array(
                                'label' => 'Date Modified',
                                'id' => 'saswp_mobile_app_schema_date_modified_'.$schema_id,
                                'type' => 'text',
                                'default' => get_the_modified_date("Y-m-d")
                        ),
                        array(
                                'label' => 'Aggregate Rating',
                                'id' => 'saswp_mobile_app_schema_enable_rating_'.$schema_id,
                                'type' => 'checkbox',                           
                            ),
                        array(
                                'label' => 'Rating',
                                'id' => 'saswp_mobile_app_schema_rating_value_'.$schema_id,
                                'type' => 'text',                           
                            ),
                        array(
                                'label' => 'Rating Count',
                                'id' => 'saswp_mobile_app_schema_rating_count_'.$schema_id,
                                'type' => 'text',                            
                            ),    
                        );
                        break;    
                
                case 'VideoObject':

                    $video_links      = saswp_get_video_metadata();                        

                    $meta_field = array(
                        array(
                                'label'      => 'ID',
                                'id'         => 'saswp_video_object_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'VideoObject'   
                        ),
                    array(
                            'label' => 'URL',
                            'id' => 'saswp_video_object_url_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink()
                    ),
                    array(
                            'label' => 'Headline',
                            'id' => 'saswp_video_object_headline_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_get_the_title()
                    ),
                    array(
                            'label' => 'Date Published',
                            'id' => 'saswp_video_object_date_published_'.$schema_id,
                            'type' => 'text',
                             'default' => get_the_date("Y-m-d")
                    ), 
                    array(
                            'label' => 'Date date Modified',
                            'id' => 'saswp_video_object_date_modified_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_modified_date("Y-m-d")
                    ),
                    array(
                            'label'   => 'Description',
                            'id'      => 'saswp_video_object_description_'.$schema_id,
                            'type'    => 'textarea',
                            'default' => saswp_strip_all_tags(get_the_excerpt())
                    ),
                    array(
                            'label'   => 'Transcript',
                            'id'      => 'saswp_video_object_transcript_'.$schema_id,
                            'type'    => 'textarea',
                            'default' => is_object($post) ? saswp_strip_all_tags($post->post_content) : ''
                    ),
                    array(
                            'label'   => 'Name',
                            'id'      => 'saswp_video_object_name_'.$schema_id,
                            'type'    => 'text',
                            'default' => saswp_get_the_title()
                    ),
                    array(
                            'label' => 'Upload Date',
                            'id' => 'saswp_video_object_upload_date_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_date("Y-m-d")
                    ),
                    array(
                            'label' => 'Thumbnail Url',
                            'id' => 'saswp_video_object_thumbnail_url_'.$schema_id,
                            'type' => 'text',                            
                    ),
                    array(
                            'label' => 'Content Url',
                            'id' => 'saswp_video_object_content_url_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink()
                    ),
                    array(
                            'label' => 'Duration',
                            'id' => 'saswp_video_object_duration_'.$schema_id,
                            'type' => 'text',
                            'attributes' => array(
                                'placeholder' => 'PT1H10M54S'
                            )                                                         
                    ),    
                    array(
                            'label'   => 'Embed Url',
                            'id'      => 'saswp_video_object_embed_url_'.$schema_id,
                            'type'    => 'text',
                            'default' => isset($video_links[0]['video_url']) ? $video_links[0]['video_url'] : get_permalink()                            
                    ),
                    array(
                        'label'   => 'Seek To Video URL',
                        'id'      => 'saswp_video_object_seek_to_video_url_'.$schema_id,
                        'type'    => 'text'                        
                    ),    
                    array(
                        'label'   => 'Seek To Second Number',
                        'id'      => 'saswp_video_object_seek_to_seconds_'.$schema_id,
                        'type'    => 'number'                        
                    ),    
                    array(
                            'label'   => 'Main Entity Id',
                            'id'      => 'saswp_video_object_main_entity_id_'.$schema_id,
                            'type'    => 'text',
                            'default' => get_permalink()
                    ),
                    array(
                        'label'   => 'Author Type',
                        'id'      => 'saswp_video_object_author_type_'.$schema_id,
                        'type'    => 'select',
                        'options' => array(
                                'Person'           => 'Person',
                                'Organization'     => 'Organization',                        
                       )
                    ),
                    array(
                            'label'   => 'Author Name',
                            'id'      => 'saswp_video_object_author_name_'.$schema_id,
                            'type'    => 'text',
                            'default' => is_object($current_user) ? $current_user->display_name : ''    
                    ),
                    array(
                            'label'   => 'Author Description',
                            'id'      => 'saswp_video_object_author_description_'.$schema_id,
                            'type'    => 'textarea',
                            'default' => $author_desc
                    ), 
                    array(
                            'label'   => 'Author URL',
                            'id'      => 'saswp_video_object_author_url_'.$schema_id,
                            'type'    => 'text',
                            'default' => $author_url
                    ),    
                    array(
                            'label'   => 'Author Image',
                            'id'      => 'saswp_video_object_author_image_'.$schema_id,
                            'type'    => 'media',
                            'default' => isset($author_details['url']) ? $author_details['url'] : ''   
                    ),
                    array(
                            'label'   => 'Organization Name',
                            'id'      => 'saswp_video_object_organization_name_'.$schema_id,
                            'type'    => 'text',
                            'default' => isset($sd_data['sd_name']) ? $sd_data['sd_name'] : ''
                    ),
                    array(
                            'label'   => 'Organization Logo',
                            'id'      => 'saswp_video_object_organization_logo_'.$schema_id,
                            'type'    => 'media',
                            'default' => isset($sd_data['sd_logo']['url']) ? $sd_data['sd_logo']['url'] : ''
                    ),    
                   );
                    break;
                
                case 'ImageObject':
                    $meta_field = array(
                    array(
                            'label' => 'URL',
                            'id' => 'saswpimage_object_url_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink()
                    ),                    
                    array(
                            'label' => 'Date Published',
                            'id' => 'saswpimage_object_date_published_'.$schema_id,
                            'type' => 'text',
                             'default' => get_the_date("Y-m-d")
                    ), 
                    array(
                            'label' => 'Date date Modified',
                            'id' => 'saswpimage_object_date_modified_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_modified_date("Y-m-d")
                    ),
                    array(
                            'label' => 'Description',
                            'id' => 'saswpimage_object_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => saswp_strip_all_tags(get_the_excerpt())
                    ),
                    array(
                            'label' => 'Name',
                            'id' => 'saswpimage_object_name_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_get_the_title()
                    ),
                    array(
                            'label' => 'Upload Date',
                            'id' => 'saswpimage_object_upload_date_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_date("Y-m-d")
                    ),                    
                    array(
                            'label' => 'Content Url',
                            'id' => 'saswpimage_object_content_url_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink()
                    ),
                    array(
                            'label' => 'Content Location',
                            'id'    => 'saswpimage_object_content_location_'.$schema_id,
                            'type'  => 'text'                            
                    ),
                    array(
                            'label' => 'Acquire License Page ',
                            'id'    => 'saswpimage_object_acquire_license_page_'.$schema_id,
                            'type'  => 'text'                            
                    ),
                    array(
                        'label'   => 'Author Type',
                        'id'      => 'saswpimage_object_author_type_'.$schema_id,
                        'type'    => 'select',
                        'options' => array(
                                'Person'           => 'Person',
                                'Organization'     => 'Organization',                        
                       )
                    ),    
                    array(
                            'label' => 'Author Name',
                            'id' => 'saswpimage_object_author_name_'.$schema_id,
                            'type' => 'text',
                            'default' => is_object($current_user) ? $current_user->display_name : ''    
                    ),
                    array(
                            'label' => 'Author Description',
                            'id' => 'saswpimage_object_author_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => $author_desc
                    ),
                    array(
                            'label'   => 'Author URL',
                            'id'      => 'saswpimage_object_author_url_'.$schema_id,
                            'type'    => 'text',
                            'default' => $author_url
                    ),    
                    array(
                            'label'   => 'Author Image',
                            'id'      => 'saswpimage_object_author_image_'.$schema_id,
                            'type'    => 'media',
                            'default' => isset($author_details['url']) ? $author_details['url'] : ''   
                    ),
                    array(
                        'label'   => 'License',
                        'id'      => 'saswpimage_object_license_'.$schema_id,
                        'type'    => 'text',                        
                    ),
                    array(
                            'label'   => 'Organization Name',
                            'id'      => 'saswpimage_object_organization_name_'.$schema_id,
                            'type'    => 'text',
                            'default' => isset($sd_data['sd_name']) ? $sd_data['sd_name'] : ''
                    ),
                    array(
                            'label'   => 'Organization Logo',
                            'id'      => 'saswpimage_object_organization_logo_'.$schema_id,
                            'type'    => 'media',
                            'default' => isset($sd_data['sd_logo']['url']) ? $sd_data['sd_logo']['url'] : ''
                    ),    
                   );
                    break;
                
                case 'qanda':
                    
                    $meta_field = array(
                    array(
                            'label' => 'Question Title',
                            'id' => 'saswp_qa_question_title_'.$schema_id,
                            'type' => 'text',                           
                    ),
                    array(
                            'label' => 'Question Description',
                            'id' => 'saswp_qa_question_description_'.$schema_id,
                            'type' => 'text',                           
                    ),                    
                    array(
                            'label' => 'Question Upvote Count',
                            'id' => 'saswp_qa_upvote_count_'.$schema_id,
                            'type' => 'number',                           
                    ),
                    array(
                            'label' => 'Question Date Created',
                            'id' => 'saswp_qa_date_created_'.$schema_id,
                            'type' => 'text',                           
                    ),    
                    array(
                            'label' => 'Author Type',
                            'id'    => 'saswp_qa_question_author_type_'.$schema_id,
                            'type'  => 'select',
                            'options' => array(
                                    'Person'       => 'Person',
                                    'Organization' => 'Organization'
                            )                           
                    ),
                    array(
                            'label' => 'Author Name',
                            'id' => 'saswp_qa_question_author_name_'.$schema_id,
                            'type' => 'text',                           
                    ),  
                    array(
                            'label'      => 'Author URL',
                            'id'         => 'saswp_qa_question_author_url_'.$schema_id,
                            'type'       => 'text',
                            'default'    => $author_url
                    ),      
                    array(
                        'label' => 'Answer Count',
                        'id'    => 'saswp_qa_answer_count_'.$schema_id,
                        'type'  => 'number',                           
                    )                                            
                        
                   );
                    break;
                
                case 'HowTo':
                    
                    $meta_field = array(
                    array(
                            'label'      => 'ID',
                            'id'         => 'saswp_howto_schema_id_'.$schema_id,
                            'type'       => 'text',
                            'default'    => 'HowTo'   
                    ),    
                    array(
                            'label'      => 'Name',
                            'id'         => 'saswp_howto_schema_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                    ),
                    array(
                            'label'      => 'Description',
                            'id'         => 'saswp_howto_schema_description_'.$schema_id,
                            'type'       => 'textarea',                            
                    ), 
                    array(
                            'label'      => 'Image',
                            'id'         => 'saswp_howto_schema_image_'.$schema_id,
                            'type'       => 'media',                            
                    ),     
                    array(
                            'label'      => 'Estimated Cost Currency',
                            'id'         => 'saswp_howto_ec_schema_currency_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'USD'
                            ), 
                    ),
                    array(
                            'label'      => 'Estimated Cost Value',
                            'id'         => 'saswp_howto_ec_schema_value_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => '20'
                            ), 
                    ),
                    array(
                            'label'      => 'Total Time',
                            'id'         => 'saswp_howto_schema_totaltime_'.$schema_id,
                            'type'       => 'text', 
                            'attributes' => array(
                                'placeholder' => 'PT30M'
                            ), 
                    ),
                     array(
                            'label'      => 'Date Published',
                            'id'         => 'saswp_howto_ec_schema_date_published_'.$schema_id,
                            'type'       => 'text', 
                            
                    ),
                    array(
                                'label'      => 'Date Modified',
                                'id'         => 'saswp_howto_ec_schema_date_modified_'.$schema_id,
                                'type'       => 'text',                             
                    ),
                       
                        array(
                                'label'      => 'Video Name',
                                'id'         => 'saswp_howto_schema_video_name_'.$schema_id,
                                'type'       => 'text',
                                'attributes' => array(
                                        'placeholder' => 'Build a Trivia Game for the Google Assistant with No Code'
                                    ),                             
                        ),
                        array(
                                'label'      => 'Video Description',
                                'id'         => 'saswp_howto_schema_video_description_'.$schema_id,
                                'type'       => 'textarea',
                                'attributes' => array(
                                        'placeholder' => 'Learn how to create a Trivia action for Assistant within minutes.'
                                    ),                             
                        ),
                        array(
                                'label'      => 'Video Thumbnail URL',
                                'id'         => 'saswp_howto_schema_video_thumbnail_url_'.$schema_id,
                                'type'       => 'text',
                                'attributes' => array(
                                        'placeholder' => 'https://example.com/photos/photo.jpg'
                                    ),                             
                        ),
                        array(
                                'label'      => 'Video Content URL',
                                'id'         => 'saswp_howto_schema_video_content_url_'.$schema_id,
                                'type'       => 'text', 
                                'attributes' => array(
                                        'placeholder' => 'https://www.youtube.com/watch?v=4AOI1tZrgMI'
                                    ),                            
                        ),
                        array(
                                'label'      => 'Video Embed URL',
                                'id'         => 'saswp_howto_schema_video_embed_url_'.$schema_id,
                                'type'       => 'text',
                                'attributes' => array(
                                        'placeholder' => 'https://www.youtube.com/embed/4AOI1tZrgMI'
                                    ),                             
                        ),
                        array(
                                'label'      => 'Video Upload Date',
                                'id'         => 'saswp_howto_schema_video_upload_date_'.$schema_id,
                                'type'       => 'text',  
                                'attributes' => array(
                                        'placeholder' => '2019-01-05'
                                    ),                           
                        ),
                        array(
                                'label'      => 'Video Duration',
                                'id'         => 'saswp_howto_schema_video_duration_'.$schema_id,
                                'type'       => 'text', 
                                'attributes' => array(
                                        'placeholder' => 'P1MT10S'
                                    ),                            
                        ),

                    array(
                        'label'      => 'Supplies',
                        'id'         => 'saswp_howto_schema_supplies_'.$schema_id,
                        'type'       => 'repeater'                                                     
                    ),
                    array(
                        'label'      => 'Tools',
                        'id'         => 'saswp_howto_schema_tools_'.$schema_id,
                        'type'       => 'repeater'                                                     
                    ),
                    array(
                        'label'      => 'Steps',
                        'id'         => 'saswp_howto_schema_steps_'.$schema_id,
                        'type'       => 'repeater'                                                     
                    ),
                    array(
                        'label'   => 'About',
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
                                'label'      => 'ID',
                                'id'         => 'saswp_mc_schema_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'MedicalCondition'   
                        ),
                    array(
                            'label'      => 'Name',
                            'id'         => 'saswp_mc_schema_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                    ),
                    array(
                            'label'      => 'Alternate Name',
                            'id'         => 'saswp_mc_schema_alternate_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Alternate Name'
                            ), 
                    ),    
                    array(
                            'label'      => 'Description',
                            'id'         => 'saswp_mc_schema_description_'.$schema_id,
                            'type'       => 'textarea',                            
                    ),
                    array(
                            'label'      => 'Image',
                            'id'         => 'saswp_mc_schema_image_'.$schema_id,
                            'type'       => 'media',                            
                    ),                             
                    array(
                            'label'      => 'Associated Anatomy Name',
                            'id'         => 'saswp_mc_schema_anatomy_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                    ),
                    array(
                            'label'      => 'Medical Code',
                            'id'         => 'saswp_mc_schema_medical_code_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => '413'
                            ), 
                    ),
                    array(
                            'label'      => 'Coding System',
                            'id'         => 'saswp_mc_schema_coding_system_'.$schema_id,
                            'type'       => 'text', 
                            'attributes' => array(
                                'placeholder' => 'ICD-9'
                            ), 
                    ),
                     array(
                            'label'      => 'Diagnosis Name',
                            'id'         => 'saswp_mc_schema_diagnosis_name_'.$schema_id,
                            'type'       => 'text', 
                            
                     ),
                     array(
                        'label'      => 'Drug',
                        'id'         => 'saswp_mc_schema_drug_'.$schema_id,
                        'type'       => 'text', 
                        
                     ),
                     array(
                        'label'      => 'Primary Prevention Name',
                        'id'         => 'saswp_mc_schema_primary_prevention_name_'.$schema_id,
                        'type'       => 'text', 
                        
                     ),
                     array(
                        'label'      => 'Primary Prevention Performed',
                        'id'         => 'saswp_mc_schema_primary_prevention_performed_'.$schema_id,
                        'type'       => 'textarea',                         
                     ),
                     array(
                        'label'      => 'Possible Treatment Name',
                        'id'         => 'saswp_mc_schema_possible_treatment_name_'.$schema_id,
                        'type'       => 'text', 
                        
                     ),
                     array(
                        'label'      => 'Possible Treatment Performed',
                        'id'         => 'saswp_mc_schema_possible_treatment_performed_'.$schema_id,
                        'type'       => 'textarea',                         
                     )                                          
                   );
                    break;
                
                case 'VideoGame':
                    
                    $meta_field = array(
                        array(
                                'label'      => 'ID',
                                'id'         => 'saswp_vg_schema_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'VideoGame'   
                        ),
                    array(
                            'label'      => 'Name',
                            'id'         => 'saswp_vg_schema_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                    ),
                    array(
                            'label'      => 'URL',
                            'id'         => 'saswp_vg_schema_url_'.$schema_id,
                            'type'       => 'text',
                            
                    ),
                    array(
                            'label'      => 'Image',
                            'id'         => 'saswp_vg_schema_image_'.$schema_id,
                            'type'       => 'media',
                            
                    ),
                    array(
                            'label'      => 'Description',
                            'id'         => 'saswp_vg_schema_description_'.$schema_id,
                            'type'       => 'textarea',
                            
                    ),
                    array(
                            'label'      => 'Operating System',
                            'id'         => 'saswp_vg_schema_operating_system_'.$schema_id,
                            'type'       => 'text',
                            
                    ),
                    array(
                            'label'      => 'Application Category',
                            'id'         => 'saswp_vg_schema_application_category_'.$schema_id,
                            'type'       => 'text',
                            
                    ),
                    array(
                        'label'      => 'Author Type',
                        'id'         => 'saswp_vg_schema_author_type_'.$schema_id,
                        'type'    => 'select',
                        'options' => array(
                                'Person'           => 'Person',
                                'Organization'     => 'Organization',                        
                       )
                        
                    ),
                    array(
                            'label'      => 'Author Name',
                            'id'         => 'saswp_vg_schema_author_name_'.$schema_id,
                            'type'       => 'text',
                            
                    ),
                    array(
                            'label'      => 'Price',
                            'id'         => 'saswp_vg_schema_price_'.$schema_id,
                            'type'       => 'text',
                            
                    ),
                    array(
                            'label'      => 'Price Currency',
                            'id'         => 'saswp_vg_schema_price_currency_'.$schema_id,
                            'type'       => 'text',
                            
                    ),    
                    array(
                            'label'   => 'Availability',
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
                            'label'      => 'Publisher',
                            'id'         => 'saswp_vg_schema_publisher_'.$schema_id,
                            'type'       => 'text',
                            
                    ),
                    array(
                            'label'      => 'Genre',
                            'id'         => 'saswp_vg_schema_genre_'.$schema_id,
                            'type'       => 'text',
                            
                    ),
                    array(
                            'label'      => 'Processor Requirements',
                            'id'         => 'saswp_vg_schema_processor_requirements_'.$schema_id,
                            'type'       => 'text',
                            
                    ),
                    array(
                            'label'      => 'Memory Requirements',
                            'id'         => 'saswp_vg_schema_memory_requirements_'.$schema_id,
                            'type'       => 'text',
                            
                    ),
                    array(
                            'label'      => 'Storage Requirements',
                            'id'         => 'saswp_vg_schema_storage_requirements_'.$schema_id,
                            'type'       => 'text',
                            
                    ),
                    array(
                            'label'      => 'Game Platform',
                            'id'         => 'saswp_vg_schema_game_platform_'.$schema_id,
                            'type'       => 'text',
                            
                    ),
                    array(
                            'label'      => 'Cheat Code',
                            'id'         => 'saswp_vg_schema_cheat_code_'.$schema_id,
                            'type'       => 'text',
                            
                    ),
                    array(
                        'label'      => 'File Size',
                        'id'         => 'saswp_vg_schema_file_size_'.$schema_id,
                        'type'       => 'text'                        
                    ),
                    array(
                            'label' => 'Aggregate Rating',
                            'id' => 'saswp_vg_schema_enable_rating_'.$schema_id,
                            'type' => 'checkbox',                          
                        ),
                        array(
                            'label' => 'Rating',
                            'id' => 'saswp_vg_schema_rating_'.$schema_id,
                            'type' => 'text',                           
                        ),
                        array(
                            'label' => 'Rating Count',
                            'id' => 'saswp_vg_schema_review_count_'.$schema_id,
                            'type' => 'text',                           
                        ),    
                        
                   );
                    break;
                
                case 'TVSeries':
                    
                    $meta_field = array(
                        array(
                                'label'      => 'ID',
                                'id'         => 'saswp_tvseries_schema_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'RealEstateListing'   
                        ),
                    array(
                            'label'      => 'Name',
                            'id'         => 'saswp_tvseries_schema_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                    ),
                     array(
                            'label'      => 'Image',
                            'id'         => 'saswp_tvseries_schema_image_'.$schema_id,
                            'type'       => 'media'                            
                    ),
                    array(
                        'label'      => 'Author Type',
                        'id'         => 'saswp_tvseries_schema_author_type_'.$schema_id,
                        'type'       => 'select',
                        'options'   => array(
                                'Person'           => 'Person',
                                'Organization'     => 'Organization',                        
                       )
                    ),
                    array(
                            'label'      => 'Author Name',
                            'id'         => 'saswp_tvseries_schema_author_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Author Name'
                            ), 
                    ),    
                    array(
                            'label'      => 'Description',
                            'id'         => 'saswp_tvseries_schema_description_'.$schema_id,
                            'type'       => 'textarea'                            
                    )  
                        
                   );
                    break;
                
                case 'Apartment':
                    
                    $meta_field = array(
                        array(
                                'label'      => 'ID',
                                'id'         => 'saswp_apartment_schema_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'Apartment'   
                        ),
                    array(
                            'label'      => 'Name',
                            'id'         => 'saswp_apartment_schema_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                    ),
                    array(
                            'label'      => 'URL',
                            'id'         => 'saswp_apartment_schema_url_'.$schema_id,
                            'type'       => 'text',
                            'default'    => get_permalink()
                    ),
                    array(
                            'label'      => 'Image',
                            'id'         => 'saswp_apartment_schema_image_'.$schema_id,
                            'type'       => 'media',
                            'default'    => get_permalink()
                    ),    
                    array(
                            'label'      => 'Description',
                            'id'         => 'saswp_apartment_schema_description_'.$schema_id,
                            'type'       => 'textarea',
                            'attributes' => array(
                                'placeholder' => 'Description'
                            ), 
                    ),
                    array(
                            'label'      => 'Number Of Rooms',
                            'id'         => 'saswp_apartment_schema_numberofrooms_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => '5'
                            ), 
                    ),
                    array(
                            'label'      => 'Floor Size',
                            'id'         => 'saswp_apartment_schema_floor_size_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => '140 Sq.Ft'
                            ), 
                    ),    
                    array(
                            'label'      => 'Country',
                            'id'         => 'saswp_apartment_schema_country_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => 'Locality',
                            'id'         => 'saswp_apartment_schema_locality_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Region',
                            'id'         => 'saswp_apartment_schema_region_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Postal Code',
                            'id'         => 'saswp_apartment_schema_postalcode_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Telephone',
                            'id'         => 'saswp_apartment_schema_telephone_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'GeoCoordinates Latitude',
                            'id'         => 'saswp_apartment_schema_latitude_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => '17.412'
                            ), 
                    ),
                    array(
                            'label'      => 'GeoCoordinates Longitude',
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
                                'label'      => 'ID',
                                'id'         => 'saswp_house_schema_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'House'   
                        ),
                    array(
                            'label'      => 'Name',
                            'id'         => 'saswp_house_schema_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                    ),
                    array(
                            'label'      => 'URL',
                            'id'         => 'saswp_house_schema_url_'.$schema_id,
                            'type'       => 'text',
                            'default'    => get_permalink()
                    ),
                    array(
                            'label'      => 'Image',
                            'id'         => 'saswp_house_schema_image_'.$schema_id,
                            'type'       => 'media',                            
                    ),
                    array(
                            'label'      => 'Description',
                            'id'         => 'saswp_house_schema_description_'.$schema_id,
                            'type'       => 'textarea',
                            'attributes' => array(
                                'placeholder' => 'Description'
                            ), 
                    ),
                     array(
                            'label'      => 'Pets Allowed',
                            'id'         => 'saswp_house_schema_pets_allowed_'.$schema_id,
                            'type'       => 'select',
                            'options' => array(
                                     'yes'       => 'Yes',
                                     'no'        => 'No'                                                                          
                            ) 
                    ),
                    array(
                            'label'      => 'Country',
                            'id'         => 'saswp_house_schema_country_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => 'Locality',
                            'id'         => 'saswp_house_schema_locality_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Region',
                            'id'         => 'saswp_house_schema_region_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Postal Code',
                            'id'         => 'saswp_house_schema_postalcode_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Telephone',
                            'id'         => 'saswp_house_schema_telephone_'.$schema_id,
                            'type'       => 'text',                            
                    ),                      
                    array(
                            'label'      => 'Has Map',
                            'id'         => 'saswp_house_schema_hasmap_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Floor Size',
                            'id'         => 'saswp_house_schema_floor_size_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                    array(
                            'label'      => 'Number of Rooms',
                            'id'         => 'saswp_house_schema_no_of_rooms_'.$schema_id,
                            'type'       => 'text',                            
                    )                                                 
                   );
                    break;   
                
                case 'SingleFamilyResidence':
                    
                    $meta_field = array(
                        array(
                                'label'      => 'ID',
                                'id'         => 'saswp_sfr_schema_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'SingleFamilyResidence'   
                        ),
                    array(
                            'label'      => 'Name',
                            'id'         => 'saswp_sfr_schema_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                    ),
                    array(
                            'label'      => 'URL',
                            'id'         => 'saswp_sfr_schema_url_'.$schema_id,
                            'type'       => 'text',
                            'default'    => get_permalink()
                    ),
                    array(
                            'label'      => 'Image',
                            'id'         => 'saswp_sfr_schema_image_'.$schema_id,
                            'type'       => 'media',                            
                    ),
                    array(
                            'label'      => 'Description',
                            'id'         => 'saswp_sfr_schema_description_'.$schema_id,
                            'type'       => 'textarea',
                            'attributes' => array(
                                'placeholder' => 'Description'
                            ), 
                    ),
                    array(
                            'label'      => 'Number Of Rooms',
                            'id'         => 'saswp_sfr_schema_numberofrooms_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => '5'
                            ), 
                    ),    
                     array(
                            'label'      => 'Pets Allowed',
                            'id'         => 'saswp_sfr_schema_pets_allowed_'.$schema_id,
                            'type'       => 'select',
                            'options' => array(
                                     'yes'       => 'Yes',
                                     'no'        => 'No'                                                                          
                            ) 
                    ),
                    array(
                            'label'      => 'Country',
                            'id'         => 'saswp_sfr_schema_country_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => 'Locality',
                            'id'         => 'saswp_sfr_schema_locality_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Region',
                            'id'         => 'saswp_sfr_schema_region_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Postal Code',
                            'id'         => 'saswp_sfr_schema_postalcode_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Telephone',
                            'id'         => 'saswp_sfr_schema_telephone_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Has Map',
                            'id'         => 'saswp_sfr_schema_hasmap_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Floor Size',
                            'id'         => 'saswp_sfr_schema_floor_size_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                    array(
                            'label'      => 'Number of Rooms',
                            'id'         => 'saswp_sfr_schema_no_of_rooms_'.$schema_id,
                            'type'       => 'text',                            
                    )    
                                              
                   );
                    break;
                
                case 'TouristAttraction':
                    
                    $meta_field = array(
                        array(
                                'label'      => 'ID',
                                'id'         => 'saswp_ta_schema_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'TouristAttraction'   
                        ),
                    array(
                            'label'      => 'Name',
                            'id'         => 'saswp_ta_schema_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                    ),
                    array(
                            'label'      => 'Description',
                            'id'         => 'saswp_ta_schema_description_'.$schema_id,
                            'type'       => 'textarea',
                            'attributes' => array(
                                'placeholder' => 'Description'
                            ), 
                    ),
                    array(
                            'label'      => 'Image',
                            'id'         => 'saswp_ta_schema_image_'.$schema_id,
                            'type'       => 'media',                            
                    ),    
                    array(
                            'label'      => 'URL',
                            'id'         => 'saswp_ta_schema_url_'.$schema_id,
                            'type'       => 'text',
                            'default'    => get_permalink()
                    ),
                    array(
                            'label'      => 'Is Accessible For Free',
                            'id'         => 'saswp_ta_schema_is_acceesible_free_'.$schema_id,
                            'type'       => 'select',
                            'options' => array(
                                'true' => 'True',
                                'false' => 'False',
                            ),
                    ),
                    array(
                            'label'      => 'Address Locality',
                            'id'         => 'saswp_ta_schema_locality_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Address Region',
                            'id'         => 'saswp_ta_schema_region_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                    array(
                            'label'      => 'Address Country',
                            'id'         => 'saswp_ta_schema_country_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Address PostalCode',
                            'id'         => 'saswp_ta_schema_postal_code_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Latitude',
                            'id'         => 'saswp_ta_schema_latitude_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Longitude',
                            'id'         => 'saswp_ta_schema_longitude_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                                              
                   );
                    break;
                
                case 'TouristDestination':
                     
                    $meta_field = array(
                        array(
                                'label'      => 'ID',
                                'id'         => 'saswp_td_schema_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'TouristDestination'   
                        ),
                    array(
                            'label'      => 'Name',
                            'id'         => 'saswp_td_schema_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                    ),
                    array(
                            'label'      => 'Description',
                            'id'         => 'saswp_td_schema_description_'.$schema_id,
                            'type'       => 'textarea',
                            'attributes' => array(
                                'placeholder' => 'Description'
                            ), 
                    ),
                    array(
                            'label'      => 'Image',
                            'id'         => 'saswp_td_schema_image_'.$schema_id,
                            'type'       => 'media',                            
                    ),    
                    array(
                            'label'      => 'URL',
                            'id'         => 'saswp_td_schema_url_'.$schema_id,
                            'type'       => 'text',
                            'default'    => get_permalink()
                    ),                                                                                
                    array(
                            'label'      => 'Address Locality',
                            'id'         => 'saswp_td_schema_locality_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Address Region',
                            'id'         => 'saswp_td_schema_region_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                    array(
                            'label'      => 'Address Country',
                            'id'         => 'saswp_td_schema_country_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Address PostalCode',
                            'id'         => 'saswp_td_schema_postal_code_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Latitude',
                            'id'         => 'saswp_td_schema_latitude_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Longitude',
                            'id'         => 'saswp_td_schema_longitude_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                                              
                   );
                    break;
                
                case 'LandmarksOrHistoricalBuildings':
                    
                    $meta_field = array(
                        array(
                                'label'      => 'ID',
                                'id'         => 'saswp_lorh_schema_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'LandmarksOrHistoricalBuildings'   
                        ),
                    array(
                            'label'      => 'Name',
                            'id'         => 'saswp_lorh_schema_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                    ),
                    array(
                            'label'      => 'Description',
                            'id'         => 'saswp_lorh_schema_description_'.$schema_id,
                            'type'       => 'textarea',
                            'attributes' => array(
                                'placeholder' => 'Description'
                            ), 
                    ),
                    array(
                            'label'      => 'Image',
                            'id'         => 'saswp_lorh_schema_image_'.$schema_id,
                            'type'       => 'media',                            
                    ),    
                    array(
                            'label'      => 'URL',
                            'id'         => 'saswp_lorh_schema_url_'.$schema_id,
                            'type'       => 'text',
                            'default'    => get_permalink()
                    ), 
                    array(
                            'label'      => 'Has Map',
                            'id'         => 'saswp_lorh_schema_hasmap_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Is Accessible For Free',
                            'id'         => 'saswp_lorh_schema_is_acceesible_free_'.$schema_id,
                            'type'       => 'select',
                            'options'    => array(
                                    'true'   => 'True',
                                    'false'  => 'False',
                            )
                    ),
                    array(
                            'label'      => 'Maximum Attendee Capacity',
                            'id'         => 'saswp_lorh_schema_maximum_a_capacity_'.$schema_id,
                            'type'       => 'number',                            
                    ),    
                    array(
                            'label'      => 'Address Locality',
                            'id'         => 'saswp_lorh_schema_locality_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Address Region',
                            'id'         => 'saswp_lorh_schema_region_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                    array(
                            'label'      => 'Address Country',
                            'id'         => 'saswp_lorh_schema_country_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Address PostalCode',
                            'id'         => 'saswp_lorh_schema_postal_code_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Latitude',
                            'id'         => 'saswp_lorh_schema_latitude_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Longitude',
                            'id'         => 'saswp_lorh_schema_longitude_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                                              
                   );
                    break;
                
                case 'HinduTemple':
                    
                    $meta_field = array(
                        array(
                                'label'      => 'ID',
                                'id'         => 'saswp_hindutemple_schema_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'hindutemple'   
                        ),
                    array(
                            'label'      => 'Name',
                            'id'         => 'saswp_hindutemple_schema_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                    ),
                    array(
                            'label'      => 'Description',
                            'id'         => 'saswp_hindutemple_schema_description_'.$schema_id,
                            'type'       => 'textarea',
                            'attributes' => array(
                                'placeholder' => 'Description'
                            ), 
                    ),
                    array(
                            'label'      => 'Image',
                            'id'         => 'saswp_hindutemple_schema_image_'.$schema_id,
                            'type'       => 'media',                            
                    ),    
                    array(
                            'label'      => 'URL',
                            'id'         => 'saswp_hindutemple_schema_url_'.$schema_id,
                            'type'       => 'text',
                            'default'    => get_permalink()
                    ),  
                    array(
                            'label'      => 'Has Map',
                            'id'         => 'saswp_hindutemple_schema_hasmap_'.$schema_id,
                            'type'       => 'text',                            
                    ),                      
                    array(
                            'label'      => 'Is Accessible For Free',
                            'id'         => 'saswp_hindutemple_schema_is_accesible_free_'.$schema_id,
                            'type'       => 'select',
                            'options'    => array(
                                    'true'   => 'True',
                                    'false'  => 'False',
                            )
                    ),
                    array(
                            'label'      => 'Maximum Attendee Capacity',
                            'id'         => 'saswp_hindutemple_schema_maximum_a_capacity_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                    array(
                            'label'      => 'Address Locality',
                            'id'         => 'saswp_hindutemple_schema_locality_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Address Region',
                            'id'         => 'saswp_hindutemple_schema_region_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                    array(
                            'label'      => 'Address Country',
                            'id'         => 'saswp_hindutemple_schema_country_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Address PostalCode',
                            'id'         => 'saswp_hindutemple_schema_postal_code_'.$schema_id,
                            'type'       => 'text',                            
                    ), 
                    array(
                            'label'      => 'Latitude',
                            'id'         => 'saswp_hindutemple_schema_latitude_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Longitude',
                            'id'         => 'saswp_hindutemple_schema_longitude_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                                              
                   );
                    break;

                    case 'BuddhistTemple':
                    
                        $meta_field = array(
                        array(
                                'label'      => 'ID',
                                'id'         => 'saswp_buddhisttemple_schema_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'BuddhistTemple'   
                        ),
                        array(
                                'label'      => 'Name',
                                'id'         => 'saswp_buddhisttemple_schema_name_'.$schema_id,
                                'type'       => 'text',
                                'attributes' => array(
                                    'placeholder' => 'Name'
                                ), 
                        ),
                        array(
                                'label'      => 'Description',
                                'id'         => 'saswp_buddhisttemple_schema_description_'.$schema_id,
                                'type'       => 'textarea',
                                'attributes' => array(
                                    'placeholder' => 'Description'
                                ), 
                        ),
                        array(
                                'label'      => 'Image',
                                'id'         => 'saswp_buddhisttemple_schema_image_'.$schema_id,
                                'type'       => 'media',                            
                        ),    
                        array(
                                'label'      => 'URL',
                                'id'         => 'saswp_buddhisttemple_schema_url_'.$schema_id,
                                'type'       => 'text',
                                'default'    => get_permalink()
                        ),  
                        array(
                                'label'      => 'Has Map',
                                'id'         => 'saswp_buddhisttemple_schema_hasmap_'.$schema_id,
                                'type'       => 'text',                            
                        ),                      
                        array(
                                'label'      => 'Is Accessible For Free',
                                'id'         => 'saswp_buddhisttemple_schema_is_accesible_free_'.$schema_id,
                                'type'       => 'select',
                                'options'    => array(
                                        'true'   => 'True',
                                        'false'  => 'False',
                                )
                        ),
                        array(
                                'label'      => 'Maximum Attendee Capacity',
                                'id'         => 'saswp_buddhisttemple_schema_maximum_a_capacity_'.$schema_id,
                                'type'       => 'text',                            
                        ),    
                        array(
                                'label'      => 'Address Locality',
                                'id'         => 'saswp_buddhisttemple_schema_locality_'.$schema_id,
                                'type'       => 'text',                            
                        ),
                        array(
                                'label'      => 'Address Region',
                                'id'         => 'saswp_buddhisttemple_schema_region_'.$schema_id,
                                'type'       => 'text',                            
                        ),    
                        array(
                                'label'      => 'Address Country',
                                'id'         => 'saswp_buddhisttemple_schema_country_'.$schema_id,
                                'type'       => 'text',                            
                        ),
                        array(
                                'label'      => 'Address PostalCode',
                                'id'         => 'saswp_buddhisttemple_schema_postal_code_'.$schema_id,
                                'type'       => 'text',                            
                        ), 
                        array(
                                'label'      => 'Latitude',
                                'id'         => 'saswp_buddhisttemple_schema_latitude_'.$schema_id,
                                'type'       => 'text',                            
                        ),
                        array(
                                'label'      => 'Longitude',
                                'id'         => 'saswp_buddhisttemple_schema_longitude_'.$schema_id,
                                'type'       => 'text',                            
                        ),    
                                                  
                       );
                        break;    
                
                case 'Church':
                    
                    $meta_field = array(
                        array(
                                'label'      => 'ID',
                                'id'         => 'saswp_church_schema_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'church'   
                        ),
                    array(
                            'label'      => 'Name',
                            'id'         => 'saswp_church_schema_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                    ),
                    array(
                            'label'      => 'Description',
                            'id'         => 'saswp_church_schema_description_'.$schema_id,
                            'type'       => 'textarea',
                            'attributes' => array(
                                'placeholder' => 'Description'
                            ), 
                    ),
                    array(
                            'label'      => 'Image',
                            'id'         => 'saswp_church_schema_image_'.$schema_id,
                            'type'       => 'media',                            
                    ),    
                    array(
                            'label'      => 'URL',
                            'id'         => 'saswp_church_schema_url_'.$schema_id,
                            'type'       => 'text',
                            'default'    => get_permalink()
                    ),  
                    array(
                            'label'      => 'Has Map',
                            'id'         => 'saswp_church_schema_hasmap_'.$schema_id,
                            'type'       => 'text',                            
                    ),                      
                    array(
                            'label'      => 'Is Accessible For Free',
                            'id'         => 'saswp_church_schema_is_accesible_free_'.$schema_id,
                            'type'       => 'select',
                            'options'    => array(
                                    'true'   => 'True',
                                    'false'  => 'False',
                            )
                    ),
                    array(
                            'label'      => 'Maximum Attendee Capacity',
                            'id'         => 'saswp_church_schema_maximum_a_capacity_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                    array(
                            'label'      => 'Address Locality',
                            'id'         => 'saswp_church_schema_locality_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Address Region',
                            'id'         => 'saswp_church_schema_region_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                    array(
                            'label'      => 'Address Country',
                            'id'         => 'saswp_church_schema_country_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Address PostalCode',
                            'id'         => 'saswp_church_schema_postal_code_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Latitude',
                            'id'         => 'saswp_church_schema_latitude_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Longitude',
                            'id'         => 'saswp_church_schema_longitude_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                                              
                   );
                    break;
                
                case 'Mosque':
                    
                    $meta_field = array(
                        array(
                                'label'      => 'ID',
                                'id'         => 'saswp_mosque_schema_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'Mosque'   
                        ),
                    array(
                            'label'      => 'Name',
                            'id'         => 'saswp_mosque_schema_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                    ),
                    array(
                            'label'      => 'Description',
                            'id'         => 'saswp_mosque_schema_description_'.$schema_id,
                            'type'       => 'textarea',
                            'attributes' => array(
                                'placeholder' => 'Description'
                            ), 
                    ),
                    array(
                            'label'      => 'Image',
                            'id'         => 'saswp_mosque_schema_image_'.$schema_id,
                            'type'       => 'media',                            
                    ),    
                    array(
                            'label'      => 'URL',
                            'id'         => 'saswp_mosque_schema_url_'.$schema_id,
                            'type'       => 'text',
                            'default'    => get_permalink()
                    ),
                    array(
                            'label'      => 'Has Map',
                            'id'         => 'saswp_mosque_schema_hasmap_'.$schema_id,
                            'type'       => 'text',                            
                    ),                      
                    array(
                            'label'      => 'Is Accessible For Free',
                            'id'         => 'saswp_mosque_schema_is_accesible_free_'.$schema_id,
                            'type'       => 'select',
                            'options'    => array(
                                    'true'   => 'True',
                                    'false'  => 'False',
                            )
                    ),
                    array(
                            'label'      => 'Maximum Attendee Capacity',
                            'id'         => 'saswp_mosque_schema_maximum_a_capacity_'.$schema_id,
                            'type'       => 'text',                            
                    ),  
                    array(
                            'label'      => 'Address Locality',
                            'id'         => 'saswp_mosque_schema_locality_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                    array(
                            'label'      => 'Address Region',
                            'id'         => 'saswp_mosque_schema_region_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                    array(
                            'label'      => 'Address Country',
                            'id'         => 'saswp_mosque_schema_country_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Address PostalCode',
                            'id'         => 'saswp_mosque_schema_postal_code_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Latitude',
                            'id'         => 'saswp_mosque_schema_latitude_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Longitude',
                            'id'         => 'saswp_mosque_schema_longitude_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                                              
                   );
                    break;
                
                case 'JobPosting':
                    
                    $meta_field = array(
                        array(
                                'label'      => 'ID',
                                'id'         => 'saswp_jobposting_schema_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'JobPosting'   
                        ),
                    array(
                            'label'      => 'Title',
                            'id'         => 'saswp_jobposting_schema_title_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Title'
                            ), 
                    ),
                    array(
                            'label'      => 'Description',
                            'id'         => 'saswp_jobposting_schema_description_'.$schema_id,
                            'type'       => 'textarea',
                            'attributes' => array(
                                'placeholder' => 'Description'
                            ), 
                    ),
                    array(
                            'label'      => 'URL',
                            'id'         => 'saswp_jobposting_schema_url_'.$schema_id,
                            'type'       => 'text',
                            'default'    => get_permalink()
                    ),     
                    array(
                            'label'      => 'Date Posted',
                            'id'         => 'saswp_jobposting_schema_dateposted_'.$schema_id,
                            'type'       => 'text',                             
                    ),
                    array(
                        'label'      => 'Direct Apply',
                        'id'         => 'saswp_jobposting_schema_direct_apply_'.$schema_id,
                        'type'       => 'text',
                        'default'    => true   
                    ),
                    array(
                            'label'      => 'Valid Through',
                            'id'         => 'saswp_jobposting_schema_validthrough_'.$schema_id,
                            'type'       => 'text',                             
                    ),
                    array(
                            'label'      => 'Employment Type',
                            'id'         => 'saswp_jobposting_schema_employment_type_'.$schema_id,
                            'type'       => 'select', 
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
                                'label'      => 'Industry',
                                'id'         => 'saswp_jobposting_schema_industry_'.$schema_id,
                                'type'       => 'text',                             
                        ),
                    array(
                                'label'      => 'Occupational Category',
                                'id'         => 'saswp_jobposting_schema_occupational_category_'.$schema_id,
                                'type'       => 'text',                             
                        ),
                     array(
                                'label'      => 'Job Immediate Start',
                                'id'         => 'saswp_jobposting_schema_jobimmediatestart_'.$schema_id,
                                'type'       => 'text',                             
                       ),
                    array(
                            'label'      => 'Hiring Organization Name',
                            'id'         => 'saswp_jobposting_schema_ho_name_'.$schema_id,
                            'type'       => 'text',                             
                    ),
                    array(
                            'label'      => 'Hiring Organization URL',
                            'id'         => 'saswp_jobposting_schema_ho_url_'.$schema_id,
                            'type'       => 'text',                             
                    ),
                    array(
                            'label'      => 'Hiring Organization Logo',
                            'id'         => 'saswp_jobposting_schema_ho_logo_'.$schema_id,
                            'type'       => 'media',                             
                    ),
                    array(
                        'label'      => 'Applicants can apply from ( Country ) ',
                        'id'         => 'saswp_jobposting_schema_applicant_location_requirements_'.$schema_id,
                        'type'       => 'text',                             
                    ),
                    array(
                        'label'      => 'Job Location Type',
                        'id'         => 'saswp_jobposting_schema_job_location_type_'.$schema_id,
                        'type'       => 'text',                             
                    ),
                    array(
                            'label'      => 'Job Location Street Address',
                            'id'         => 'saswp_jobposting_schema_street_address_'.$schema_id,
                            'type'       => 'text',                             
                    ),
                    array(
                            'label'      => 'Job Location Locality',
                            'id'         => 'saswp_jobposting_schema_locality_'.$schema_id,
                            'type'       => 'text',                             
                    ),
                    array(
                            'label'      => 'Job Location Region',
                            'id'         => 'saswp_jobposting_schema_region_'.$schema_id,
                            'type'       => 'text',                             
                    ),
                    array(
                            'label'      => 'Job Location Postal Code',
                            'id'         => 'saswp_jobposting_schema_postalcode_'.$schema_id,
                            'type'       => 'text',                             
                    ),
                    array(
                            'label'      => 'Job Location Country',
                            'id'         => 'saswp_jobposting_schema_country_'.$schema_id,
                            'type'       => 'text',                             
                    ),
                    array(
                                'label'      => 'Job Location GeoCoordinates Latitude',
                                'id'         => 'saswp_jobposting_schema_latitude_'.$schema_id,
                                'type'       => 'text',
                                'attributes' => array(
                                        'placeholder' => '17.412'
                                ), 
                     ),
                     array(
                                'label'      => 'Job Location GeoCoordinates Longitude',
                                'id'         => 'saswp_jobposting_schema_longitude_'.$schema_id,
                                'type'       => 'text',
                                'attributes' => array(
                                        'placeholder' => '78.433'
                                ),
                        ),
                    array(
                            'label'      => 'Base Salary Currency',
                            'id'         => 'saswp_jobposting_schema_bs_currency_'.$schema_id,
                            'type'       => 'text', 
                            'attributes' => array(
                                'placeholder' => 'USD'
                            )
                    ),
                    array(
                            'label'      => 'Base Salary Value',
                            'id'         => 'saswp_jobposting_schema_bs_value_'.$schema_id,
                            'type'       => 'text', 
                            'attributes' => array(
                                'placeholder' => '40.00'
                            )
                    ),
                    array(
                        'label'      => 'Base Salary Min Value',
                        'id'         => 'saswp_jobposting_schema_bs_min_value_'.$schema_id,
                        'type'       => 'text', 
                        'attributes' => array(
                            'placeholder' => '20.00'
                        )
                ),
                array(
                        'label'      => 'Base Salary Max Value',
                        'id'         => 'saswp_jobposting_schema_bs_max_value_'.$schema_id,
                        'type'       => 'text', 
                        'attributes' => array(
                            'placeholder' => '100.00'
                        )
                ),
                    array(
                            'label'      => 'Base Salary Unit Text',
                            'id'         => 'saswp_jobposting_schema_bs_unittext_'.$schema_id,
                            'type'       => 'text', 
                            'attributes' => array(
                                'placeholder' => 'Hour'
                            )
                    ), 
                        array(
                                'label'      => 'Estimated Salary Currency',
                                'id'         => 'saswp_jobposting_schema_es_currency_'.$schema_id,
                                'type'       => 'text', 
                                'attributes' => array(
                                'placeholder' => 'USD'
                                )
                        ),
                        array(
                                'label'      => 'Estimated Salary Value',
                                'id'         => 'saswp_jobposting_schema_es_value_'.$schema_id,
                                'type'       => 'text', 
                                'attributes' => array(
                                'placeholder' => '40.00'
                                )
                        ),
                        array(
                                'label'      => 'Estimated Salary Min Value',
                                'id'         => 'saswp_jobposting_schema_es_min_value_'.$schema_id,
                                'type'       => 'text', 
                                'attributes' => array(
                                'placeholder' => '20.00'
                                )
                        ),
                        array(
                                'label'      => 'Estimated Salary Max Value',
                                'id'         => 'saswp_jobposting_schema_es_max_value_'.$schema_id,
                                'type'       => 'text', 
                                'attributes' => array(
                                'placeholder' => '100.00'
                                )
                        ),
                        array(
                                'label'      => 'Estimated Salary Unit Text',
                                'id'         => 'saswp_jobposting_schema_es_unittext_'.$schema_id,
                                'type'       => 'text', 
                                'attributes' => array(
                                'placeholder' => 'Hour'
                                )
                        )   
                   
                                              
                   );
                    break;
               
                case 'Trip':
                    
                    $meta_field = array( 
                        array(
                                'label'      => 'ID',
                                'id'         => 'saswp_trip_schema_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'Trip'   
                        ),
                    array(
                            'label'      => 'Name',
                            'id'         => 'saswp_trip_schema_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                    ),
                    array(
                            'label'      => 'Description',
                            'id'         => 'saswp_trip_schema_description_'.$schema_id,
                            'type'       => 'textarea',
                            'attributes' => array(
                                'placeholder' => 'Description'
                            )
                    ),
                    array(
                            'label'      => 'URL',
                            'id'         => 'saswp_trip_schema_url_'.$schema_id,
                            'type'       => 'text',
                            'default'    => get_permalink() 
                    ),
                    array(
                            'label'      => 'Image',
                            'id'         => 'saswp_trip_schema_image_'.$schema_id,
                            'type'       => 'media'                            
                    )    
                        
                        
                   );

                    break;

                    case 'BoatTrip':
                    
                        $meta_field = array(
                        array(
                                'label'      => 'ID',
                                'id'         => 'saswp_boat_trip_schema_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'BoatTrip'   
                        ),
                        array(
                                'label'      => 'Name',
                                'id'         => 'saswp_boat_trip_schema_name_'.$schema_id,
                                'type'       => 'text',
                                'attributes' => array(
                                    'placeholder' => 'Name'
                                ), 
                        ),
                        array(
                                'label'      => 'Description',
                                'id'         => 'saswp_boat_trip_schema_description_'.$schema_id,
                                'type'       => 'textarea',
                                'attributes' => array(
                                    'placeholder' => 'Description'
                                )
                        ),
                        array(
                                'label'      => 'URL',
                                'id'         => 'saswp_boat_trip_schema_url_'.$schema_id,
                                'type'       => 'text',
                                'default'    => get_permalink() 
                        ),
                        array(
                                'label'      => 'Image',
                                'id'         => 'saswp_boat_trip_schema_image_'.$schema_id,
                                'type'       => 'media'                            
                        ),
                        array(
                                'label'      => 'Arrival Time',
                                'id'         => 'saswp_boat_trip_schema_arrival_time_'.$schema_id,
                                'type'       => 'text'                            
                        ),
                        array(
                                'label'      => 'Departure Time',
                                'id'         => 'saswp_boat_trip_schema_departure_time_'.$schema_id,
                                'type'       => 'text'                            
                        ),
                        array(
                                'label'      => 'Arrival Boat Terminal',
                                'id'         => 'saswp_boat_trip_schema_arrival_boat_terminal_'.$schema_id,
                                'type'       => 'text'                            
                        ),
                        array(
                                'label'      => 'Departure Boat Terminal',
                                'id'         => 'saswp_boat_trip_schema_departure_boat_terminal_'.$schema_id,
                                'type'       => 'text'                            
                        )                                                        
                       );
    
                    break;

                    case 'FAQ':
                        $faq_post_meta_data = get_post_meta(get_the_ID());
                        $faq_post_meta_id = 'FAQ';
                        if(isset($faq_post_meta_data['saswp_faq_id_'.$schema_id])){
                            $faq_post_meta_id = $faq_post_meta_data['saswp_faq_id_'.$schema_id][0];
                        }
                        $faq_post_meta_headline = get_the_title();
                        if(isset($faq_post_meta_data['saswp_faq_headline_'.$schema_id])){
                            $faq_post_meta_headline = $faq_post_meta_data['saswp_faq_headline_'.$schema_id][0];
                        }
                        $faq_post_meta_tags = saswp_get_the_tags();
                        if(isset($faq_post_meta_data['saswp_faq_keywords_'.$schema_id])){
                            $faq_post_meta_tags = $faq_post_meta_data['saswp_faq_keywords_'.$schema_id][0];
                        }
                        $faq_post_meta_atype = '';
                        if(isset($faq_post_meta_data['saswp_faq_author_type_'.$schema_id])){
                            $faq_post_meta_atype = $faq_post_meta_data['saswp_faq_author_type_'.$schema_id][0];
                        }
                        $faq_post_meta_aname = is_object($current_user) ? $current_user->display_name : '';
                        if(isset($faq_post_meta_data['saswp_faq_author_name_'.$schema_id])){
                            $faq_post_meta_aname = $faq_post_meta_data['saswp_faq_author_name_'.$schema_id][0];
                        }
                        $faq_post_meta_adesc = $author_desc;
                        if(isset($faq_post_meta_data['saswp_faq_author_description_'.$schema_id])){
                            $faq_post_meta_adesc = $faq_post_meta_data['saswp_faq_author_description_'.$schema_id][0];
                        }
                        $faq_post_meta_aurl = $author_url;
                        if(isset($faq_post_meta_data['saswp_faq_author_url_'.$schema_id])){
                            $faq_post_meta_aurl = $faq_post_meta_data['saswp_faq_author_url_'.$schema_id][0];
                        }
                        $faq_post_meta_aiurl = isset($author_details['url']) ? $author_details['url']: '';
                        if(isset($faq_post_meta_data['saswp_faq_author_image_'.$schema_id])){
                            $faq_post_meta_aiurl = $faq_post_meta_data['saswp_faq_author_image_'.$schema_id][0];
                        }
                        $faq_post_meta_dcreated = get_the_date("Y-m-d");
                        if(isset($faq_post_meta_data['saswp_faq_date_created_'.$schema_id])){
                            $faq_post_meta_dcreated = $faq_post_meta_data['saswp_faq_date_created_'.$schema_id][0];
                        }
                        $faq_post_meta_dpublished = get_the_date("Y-m-d");
                        if(isset($faq_post_meta_data['saswp_faq_date_published_'.$schema_id])){
                            $faq_post_meta_dpublished = $faq_post_meta_data['saswp_faq_date_published_'.$schema_id][0];
                        }
                        $faq_post_meta_dmodified = get_the_modified_date("Y-m-d");
                        if(isset($faq_post_meta_data['saswp_faq_date_modified_'.$schema_id])){
                            $faq_post_meta_dmodified = $faq_post_meta_data['saswp_faq_date_modified_'.$schema_id][0];
                        }
                        $faq_post_meta_about = '';
                        if(isset($faq_post_meta_data['saswp_faq_about_'.$schema_id])){
                            $faq_post_meta_about = $faq_post_meta_data['saswp_faq_about_'.$schema_id][0];
                        }

                        $meta_field = array(
                        array(
                                'label'      => 'ID',
                                'id'         => 'saswp_faq_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => $faq_post_meta_id   
                                ),
                        array(
                                'label'      => 'Headline',
                                'id'         => 'saswp_faq_headline_'.$schema_id,
                                'type'       => 'text',
                                'default'    => $faq_post_meta_headline                             
                        ),
                        array(
                                'label'      => 'Tags',
                                'id'         => 'saswp_faq_keywords_'.$schema_id,
                                'type'       => 'text',
                                'default'    => $faq_post_meta_tags                            
                        ),
                        array(
                                'label'   => 'Author',
                                'id'      => 'saswp_faq_author_global_mapping_'.$schema_id,
                                'type'    => 'global_mapping'
                        ),
                        array(
                                'label'      => 'Author Type',
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
                                'label'      => 'Author Name',
                                'id'         => 'saswp_faq_author_name_'.$schema_id,
                                'type'       => 'text',
                                'default' => $faq_post_meta_aname                            
                        ),    
                        array(
                                'label'   => 'Author HonorificSuffix',
                                'id'      => 'saswp_faq_author_honorific_suffix_'.$schema_id,
                                'type'    => 'text',
                                'attributes' => array(
                                        'placeholder' => 'eg: M.D. /PhD/MSCSW.'
                                 ),
                        ),
                        array(
                                'label'   => 'Author Description',
                                'id'      => 'saswp_faq_author_description_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => $faq_post_meta_adesc
                        ),
                        array(
                                'label'   => 'Author URL',
                                'id'      => 'saswp_faq_author_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => $faq_post_meta_aurl
                        ),
                        array(
                                'label' => 'Author Image URL',
                                'id' => 'saswp_faq_author_image_'.$schema_id,
                                'type' => 'media',
                                'default' => $faq_post_meta_aiurl
                        ),
                        array(
                                'label'      => 'DateCreated',
                                'id'         => 'saswp_faq_date_created_'.$schema_id,
                                'type'       => 'text',
                                'default'    => $faq_post_meta_dcreated                            
                        ),
                        array(
                                'label'      => 'DatePublished',
                                'id'         => 'saswp_faq_date_published_'.$schema_id,
                                'type'       => 'text',
                                'default'    => $faq_post_meta_dpublished                            
                        ),
                        array(
                                'label'      => 'DateModified',
                                'id'         => 'saswp_faq_date_modified_'.$schema_id,
                                'type'       => 'text',
                                'default'    => $faq_post_meta_dmodified                            
                        ),
                        array(
                                'label'      => 'MainEntity (Questions & Answers) ',
                                'id'         => 'saswp_faq_main_entity_'.$schema_id,
                                'type'       => 'repeater'                                                     
                        ),
                        array(
                                'label'   => 'About',
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
                           'label'   => 'ID',
                           'id'      => 'saswp_person_schema_id_'.$schema_id,
                           'type'    => 'text'                                
                    ),   
                    array(
                        'label'      => 'Honorific Prefix',
                        'id'         => 'saswp_person_schema_honorific_prefix_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => 'Honorific Suffix',
                        'id'         => 'saswp_person_schema_honorific_suffix_'.$schema_id,
                        'type'       => 'text',                            
                    ),     
                    array(
                            'label'      => 'Name',
                            'id'         => 'saswp_person_schema_name_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => 'Alternate Name',
                            'id'         => 'saswp_person_schema_alternate_name_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                           'label'      => 'Additional Name',
                           'id'         => 'saswp_person_schema_additional_name_'.$schema_id,
                           'type'       => 'text',                           
                    ),
                    array(
                        'label'      => 'Given Name',
                        'id'         => 'saswp_person_schema_given_name_'.$schema_id,
                        'type'       => 'text',                           
                    ),
                    array(
                            'label'      => 'Family Name',
                            'id'         => 'saswp_person_schema_family_name_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                        'label'      => 'Spouse',
                        'id'         => 'saswp_person_schema_spouse_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => 'Parent',
                        'id'         => 'saswp_person_schema_parent_'.$schema_id,
                        'type'       => 'textarea',
                        'attributes' => array(
                                'placeholder' => 'Johannes Xoo, Amanda Xoo'
                         ),
                        'note' => 'Note: Separate it by comma ( , )' ,                            
                    ),
                    array(
                        'label'      => 'Sibling',
                        'id'         => 'saswp_person_schema_sibling_'.$schema_id,
                        'type'       => 'textarea',
                        'attributes' => array(
                                'placeholder' => 'Dima Xoo, Amanda Xoo'
                         ),
                        'note' => 'Note: Separate it by comma ( , )' ,                            
                    ),
                    array(
                        'label'      => 'Colleague',
                        'id'         => 'saswp_person_schema_colleague_'.$schema_id,
                        'type'       => 'textarea',
                        'attributes' => array(
                                'placeholder' => 'Bill Gates, Jeff Bezos'
                         ),
                        'note' => 'Note: Separate it by comma ( , )' ,                            
                    ),
                    array(
                            'label'      => 'Description',
                            'id'         => 'saswp_person_schema_description_'.$schema_id,
                            'type'       => 'textarea',                           
                    ),    
                    array(
                            'label'      => 'URL',
                            'id'         => 'saswp_person_schema_url_'.$schema_id,
                            'type'       => 'text',
                            'default'    => get_permalink()
                    ),
                    array(
                        'label'      => 'Main Entity Of Page',
                        'id'         => 'saswp_person_schema_main_entity_of_page_'.$schema_id,
                        'type'       => 'text',
                        'default'    => get_permalink()
                    ),    
                    array(
                            'label'      => 'Street Address',
                            'id'         => 'saswp_person_schema_street_address_'.$schema_id,
                            'type'       => 'text',
                           
                    ),
                    array(
                            'label'      => 'Locality',
                            'id'         => 'saswp_person_schema_locality_'.$schema_id,
                            'type'       => 'text',
                           
                    ),
                    array(
                            'label'      => 'Region',
                            'id'         => 'saswp_person_schema_region_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => 'Postal Code',
                            'id'         => 'saswp_person_schema_postal_code_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => 'Country',
                            'id'         => 'saswp_person_schema_country_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => 'Email',
                            'id'         => 'saswp_person_schema_email_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => 'Telephone',
                            'id'         => 'saswp_person_schema_telephone_'.$schema_id,
                            'type'       => 'text',                           
                    ),    
                    array(
                            'label'      => 'Gender',
                            'id'         => 'saswp_person_schema_gender_'.$schema_id,
                            'type'       => 'select',
                            'options'    => array(
                                    'Male'   => 'Male',
                                    'Female' => 'Female',    
                            )
                    ),
                        array(
                            'label'      => 'Date Of Birth',
                            'id'         => 'saswp_person_schema_date_of_birth_'.$schema_id,
                            'type'       => 'text',                            
                        ),
                        array(
                            'label'      => 'Street Address',
                            'id'         => 'saswp_person_schema_b_street_address_'.$schema_id,
                            'type'       => 'text',                       
                        ),
                        array(
                                'label'      => 'Birth Place Locality',
                                'id'         => 'saswp_person_schema_b_locality_'.$schema_id,
                                'type'       => 'text',
                        
                        ),
                        array(
                                'label'      => 'Birth Place Region',
                                'id'         => 'saswp_person_schema_b_region_'.$schema_id,
                                'type'       => 'text',                           
                        ),
                        array(
                                'label'      => 'Birth Place Postal Code',
                                'id'         => 'saswp_person_schema_b_postal_code_'.$schema_id,
                                'type'       => 'text',                           
                        ),
                        array(
                                'label'      => 'Birth Place Country',
                                'id'         => 'saswp_person_schema_b_country_'.$schema_id,
                                'type'       => 'text',                           
                        ),
                    array(
                           'label'      => 'Date of death',
                           'id'         => 'saswp_person_schema_date_of_death_'.$schema_id,
                           'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Member Of',
                            'id'         => 'saswp_person_schema_member_of_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Nationality',
                            'id'         => 'saswp_person_schema_nationality_'.$schema_id,
                            'type'       => 'text',                            
                    ),                    
                    array(
                            'label'      => 'Image',
                            'id'         => 'saswp_person_schema_image_'.$schema_id,
                            'type'       => 'media',                            
                    ),
                    array(
                            'label'      => 'Job Title',
                            'id'         => 'saswp_person_schema_job_title_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Company ( Works For )',
                            'id'         => 'saswp_person_schema_company_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Website',
                            'id'         => 'saswp_person_schema_website_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                        'label'      => 'Facebook',
                        'id'         => 'saswp_person_schema_facebook_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => 'Youtube',
                        'id'         => 'saswp_person_schema_youtube_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => 'Twitter',
                        'id'         => 'saswp_person_schema_twitter_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => 'LinkedIn',
                        'id'         => 'saswp_person_schema_linkedin_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => 'Instagram',
                        'id'         => 'saswp_person_schema_instagram_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => 'Snapchat',
                        'id'         => 'saswp_person_schema_snapchat_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => 'Threads',
                        'id'         => 'saswp_person_schema_threads_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => 'Mastodon',
                        'id'         => 'saswp_person_schema_mastodon_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => 'Vibehut',
                        'id'         => 'saswp_person_schema_vibehut_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => 'Sponsor',
                        'id'         => 'saswp_person_schema_sponsor_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => 'Affiliation',
                        'id'         => 'saswp_person_schema_affiliation_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => 'Alumni Of',
                        'id'         => 'saswp_person_schema_alumniof_'.$schema_id,
                        'type'       => 'text',                            
                    ), 
                    array(
                        'label'      => 'Award',
                        'id'         => 'saswp_person_schema_award_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => 'Seeks',
                        'id'         => 'saswp_person_schema_seeks_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => 'Knows',
                        'id'         => 'saswp_person_schema_knows_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => 'Owns',
                        'id'         => 'saswp_person_schema_owns_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => 'Brand',
                        'id'         => 'saswp_person_schema_brand_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => 'Qualifications',
                        'id'         => 'saswp_person_schema_qualifications_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => 'Occupation Name',
                        'id'         => 'saswp_person_schema_occupation_name_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => 'Occupation Description',
                        'id'         => 'saswp_person_schema_occupation_description_'.$schema_id,
                        'type'       => 'textarea',                            
                    ),
                    array(
                        'label'      => 'Estimated Salary',
                        'id'         => 'saswp_person_schema_estimated_salary_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => 'Salary Currency',
                        'id'         => 'saswp_person_schema_salary_currency_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => 'Salary Duration',
                        'id'         => 'saswp_person_schema_salary_duration_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => 'Salary Median',
                        'id'         => 'saswp_person_schema_salary_median_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => 'Salary Percentile10',
                        'id'         => 'saswp_person_schema_salary_percentile10_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => 'Salary Percentile25',
                        'id'         => 'saswp_person_schema_salary_percentile25_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => 'Salary Percentile75',
                        'id'         => 'saswp_person_schema_salary_percentile75_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => 'Salary Percentile90',
                        'id'         => 'saswp_person_schema_salary_percentile90_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => 'Salary Last Reviewed',
                        'id'         => 'saswp_person_schema_salary_last_reviewed_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => 'Occupation City',
                        'id'         => 'saswp_person_schema_occupation_city_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => 'performerIn Name',
                        'id'         => 'saswp_person_schema_performerin_name_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => 'performerIn Location Name',
                        'id'         => 'saswp_person_schema_performerin_location_name_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => 'performerIn Location Locality',
                        'id'         => 'saswp_person_schema_performerin_location_locality_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => 'performerIn Location Postal Code',
                        'id'         => 'saswp_person_schema_performerin_location_postal_code_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => 'performerIn Location Street Address',
                        'id'         => 'saswp_person_schema_performerin_location_street_address_'.$schema_id,
                        'type'       => 'text',                            
                    ),

                    array(
                        'label'      => 'performerIn Offers Name',
                        'id'         => 'saswp_person_schema_performerin_offers_name_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => 'performerIn Offers Availability',
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
                        'label'      => 'performerIn Offers Price',
                        'id'         => 'saswp_person_schema_performerin_offers_price_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => 'performerIn Offers Currency',
                        'id'         => 'saswp_person_schema_performerin_offers_currency_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => 'performerIn Offers Valid From',
                        'id'         => 'saswp_person_schema_performerin_offers_valid_from_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => 'performerIn Offers URL',
                        'id'         => 'saswp_person_schema_performerin_offers_url_'.$schema_id,
                        'type'       => 'text',                            
                    ),

                    array(
                        'label'      => 'performerIn Start Date',
                        'id'         => 'saswp_person_schema_performerin_start_date_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => 'performerIn End Date',
                        'id'         => 'saswp_person_schema_performerin_end_date_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => 'performerIn Description',
                        'id'         => 'saswp_person_schema_performerin_description_'.$schema_id,
                        'type'       => 'textarea',                            
                    ),
                    array(
                        'label'      => 'performerIn Image',
                        'id'         => 'saswp_person_schema_performerin_image_'.$schema_id,
                        'type'       => 'media',                            
                    ),
                    array(
                        'label'      => 'performerIn Performer',
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
                                        'label'      => 'ID',
                                        'id'         => 'saswp_car_schema_id_'.$schema_id,
                                        'type'       => 'text',
                                        'default'    => 'Car'   
                                ),
                                array(
                                        'label'      => 'Name',
                                        'id'         => 'saswp_car_schema_name_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => 'Description',
                                        'id'         => 'saswp_car_schema_description_'.$schema_id,
                                        'type'       => 'textarea',                           
                                ),
                                array(
                                        'label'      => 'URL',
                                        'id'         => 'saswp_car_schema_url_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => 'Model',
                                        'id'         => 'saswp_car_schema_model_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => 'Image',
                                        'id'         => 'saswp_car_schema_image_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => 'Body Type',
                                        'id'         => 'saswp_car_schema_body_type_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => 'Fuel Type',
                                        'id'         => 'saswp_car_schema_fuel_type_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => 'Fuel Efficiency',
                                        'id'         => 'saswp_car_schema_fuel_efficiency_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => 'Seating Capacity',
                                        'id'         => 'saswp_car_schema_seating_capacity_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => 'Number Of Doors',
                                        'id'         => 'saswp_car_schema_number_of_doors_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => 'Weight',
                                        'id'         => 'saswp_car_schema_weight_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => 'Width',
                                        'id'         => 'saswp_car_schema_width_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => 'Height',
                                        'id'         => 'saswp_car_schema_height_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => 'SKU',
                                        'id'         => 'saswp_car_schema_sku_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => 'MPN',
                                        'id'         => 'saswp_car_schema_mpn_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => 'Brand',
                                        'id'         => 'saswp_car_schema_brand_name'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => 'Manufacturer',
                                        'id'         => 'saswp_car_schema_manufacturer_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                   array(
                                        'label'   => 'Price',
                                        'id'      => 'saswp_car_schema_price_'.$schema_id,
                                        'type'    => 'text',                                        
                                   ),
                                    array(
                                        'label'   => 'High Price',
                                        'id'      => 'saswp_car_schema_high_price_'.$schema_id,
                                        'type'    => 'text'                                            
                                    ),
                                    array(
                                        'label'   => 'Low Price',
                                        'id'      => 'saswp_car_schema_low_price_'.$schema_id,
                                        'type'    => 'text'                                            
                                    ),
                                    array(
                                        'label'   => 'Offer Count',
                                        'id'      => 'saswp_car_schema_offer_count_'.$schema_id,
                                        'type'    => 'text'
                                    ),
                                    array(
                                        'label'   => 'Price Valid Until',
                                        'id'      => 'saswp_car_schema_priceValidUntil_'.$schema_id,
                                        'type'    => 'text'                                        
                                   ),
                                    array(
                                        'label'   => 'Currency',
                                        'id'      => 'saswp_car_schema_currency_'.$schema_id,
                                        'type'    => 'text'                                        
                                   ),
                                array(
                                        'label' => 'Aggregate Rating',
                                        'id'    => 'saswp_car_schema_enable_rating_'.$schema_id,
                                        'type'  => 'checkbox',                            
                                ),
                                array(
                                        'label'   => 'Rating',
                                        'id'      => 'saswp_car_schema_rating_value_'.$schema_id,
                                        'type'    => 'text',                                        
                                ),
                                array(
                                        'label'   => 'Rating Count',
                                        'id'      => 'saswp_car_schema_rating_count_'.$schema_id,
                                        'type'    => 'text',                            
                                )                                    
                               );

                    break;

                    case 'Vehicle':
 
                        $meta_field = array(
                                array(
                                        'label'      => 'ID',
                                        'id'         => 'saswp_vehicle_schema_id_'.$schema_id,
                                        'type'       => 'text',
                                        'default'    => 'Vehicle'   
                                ),
                                array(
                                        'label'      => 'Name',
                                        'id'         => 'saswp_vehicle_schema_name_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => 'Description',
                                        'id'         => 'saswp_vehicle_schema_description_'.$schema_id,
                                        'type'       => 'textarea',                           
                                ),
                                array(
                                        'label'      => 'URL',
                                        'id'         => 'saswp_vehicle_schema_url_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => 'Model',
                                        'id'         => 'saswp_vehicle_schema_model_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => 'Image',
                                        'id'         => 'saswp_vehicle_schema_image_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => 'Body Type',
                                        'id'         => 'saswp_vehicle_schema_body_type_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => 'Fuel Type',
                                        'id'         => 'saswp_vehicle_schema_fuel_type_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => 'Fuel Efficiency',
                                        'id'         => 'saswp_vehicle_schema_fuel_efficiency_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => 'Seating Capacity',
                                        'id'         => 'saswp_vehicle_schema_seating_capacity_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => 'Number Of Doors',
                                        'id'         => 'saswp_vehicle_schema_number_of_doors_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => 'Weight',
                                        'id'         => 'saswp_vehicle_schema_weight_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => 'Width',
                                        'id'         => 'saswp_vehicle_schema_width_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => 'Height',
                                        'id'         => 'saswp_vehicle_schema_height_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => 'SKU',
                                        'id'         => 'saswp_vehicle_schema_sku_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => 'MPN',
                                        'id'         => 'saswp_vehicle_schema_mpn_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => 'Brand',
                                        'id'         => 'saswp_vehicle_schema_brand_name'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                array(
                                        'label'      => 'Manufacturer',
                                        'id'         => 'saswp_vehicle_schema_manufacturer_'.$schema_id,
                                        'type'       => 'text',                           
                                ),
                                   array(
                                        'label'   => 'Price',
                                        'id'      => 'saswp_vehicle_schema_price_'.$schema_id,
                                        'type'    => 'text',                                        
                                   ),
                                    array(
                                        'label'   => 'High Price',
                                        'id'      => 'saswp_vehicle_schema_high_price_'.$schema_id,
                                        'type'    => 'text'                                            
                                    ),
                                    array(
                                        'label'   => 'Low Price',
                                        'id'      => 'saswp_vehicle_schema_low_price_'.$schema_id,
                                        'type'    => 'text'                                            
                                    ),
                                    array(
                                        'label'   => 'Offer Count',
                                        'id'      => 'saswp_vehicle_schema_offer_count_'.$schema_id,
                                        'type'    => 'text'
                                    ),
                                    array(
                                        'label'   => 'Price Valid Until',
                                        'id'      => 'saswp_vehicle_schema_priceValidUntil_'.$schema_id,
                                        'type'    => 'text'                                        
                                   ),
                                    array(
                                        'label'   => 'Currency',
                                        'id'      => 'saswp_vehicle_schema_currency_'.$schema_id,
                                        'type'    => 'text'                                        
                                   ),
                                array(
                                        'label' => 'Aggregate Rating',
                                        'id'    => 'saswp_vehicle_schema_enable_rating_'.$schema_id,
                                        'type'  => 'checkbox',                            
                                ),
                                array(
                                        'label'   => 'Rating',
                                        'id'      => 'saswp_vehicle_schema_rating_value_'.$schema_id,
                                        'type'    => 'text',                                        
                                ),
                                array(
                                        'label'   => 'Rating Count',
                                        'id'      => 'saswp_vehicle_schema_rating_count_'.$schema_id,
                                        'type'    => 'text',                            
                                )                                    
                               );

                    break;
                    
                    case 'CreativeWorkSeries':
                    
                        $meta_field = array(
                        array(
                               'label'      => 'ID',
                               'id'         => 'saswp_cws_schema_id_'.$schema_id,
                               'type'       => 'text',
                               'default'    => 'CreativeWorkSeries'   
                        ),
                        array(
                                'label'      => 'Name',
                                'id'         => 'saswp_cws_schema_name_'.$schema_id,
                                'type'       => 'text',                           
                        ),
                        array(
                                'label'      => 'URL',
                                'id'         => 'saswp_cws_schema_url_'.$schema_id,
                                'type'       => 'text',                           
                        ),
                        array(
                                'label'      => 'Description',
                                'id'         => 'saswp_cws_schema_description_'.$schema_id,
                                'type'       => 'textarea',                           
                        ),
                        array(
                                'label'      => 'Keywords',
                                'id'         => 'saswp_cws_schema_keywords_'.$schema_id,
                                'type'       => 'text',                           
                        ),
                        array(
                                'label'      => 'Image',
                                'id'         => 'saswp_cws_schema_image_'.$schema_id,
                                'type'       => 'media',                           
                        ),
                        array(
                                'label'      => 'Start Date',
                                'id'         => 'saswp_cws_schema_start_date_'.$schema_id,
                                'type'       => 'text',                           
                        ),
                        array(
                                'label'      => 'End Date',
                                'id'         => 'saswp_cws_schema_end_date_'.$schema_id,
                                'type'       => 'text',                           
                        ),
                        array(
                                'label'      => 'Date Published',
                                'id'         => 'saswp_cws_schema_date_published_'.$schema_id,
                                'type'       => 'text',
                                'default'    => get_the_date("Y-m-d")
                        ), 
                        array(
                                'label'      => 'Date Modified',
                                'id'         => 'saswp_cws_schema_date_modified_'.$schema_id,
                                'type'       => 'text',
                                'default'    => get_the_modified_date("Y-m-d")
                        ), 
                        array(
                                'label'      => 'In Language',
                                'id'         => 'saswp_cws_schema_inlanguage_'.$schema_id,
                                'type'       => 'text',       
                                'attributes' => array(
                                        'placeholder' => 'English'
                                ),                    
                        ),    
                        array(
                                'label'      => 'Author Type',
                                'id'         => 'saswp_cws_schema_author_type_'.$schema_id,
                                'type'       => 'select',
                                'options'    => array(
                                        'Person'           => 'Person',
                                        'Organization'     => 'Organization',                        
                        )
                        ),
                        array(
                                'label'      => 'Author Name',
                                'id'         => 'saswp_cws_schema_author_name_'.$schema_id,
                                'type'       => 'text',
                                'default'    => is_object($current_user) ? $current_user->display_name : ''
                        ),
                        array(
                                'label'      => 'Author Description',
                                'id'         => 'saswp_cws_schema_author_description_'.$schema_id,
                                'type'       => 'textarea',
                                'default'    => $author_desc
                        ),
                        array(
                                'label'      => 'Author URL',
                                'id'         => 'saswp_cws_schema_author_url_'.$schema_id,
                                'type'       => 'text',
                                'default'    => $author_url
                        ),    
                        array(
                                'label'      => 'Organization Name',
                                'id'         => 'saswp_cws_schema_organization_name_'.$schema_id,
                                'type'       => 'text',
                                'default'    => saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                       ),
                         array(
                                'label'      => 'Organization Logo',
                                'id'         => 'saswp_cws_schema_organization_logo_'.$schema_id,
                                'type'       => 'media',
                                'default'    => isset($sd_data['sd_logo']) ? $sd_data['sd_logo']['url'] : ''
                        )    
                       );
                        break;

                case 'DataFeed':
                    
                    $meta_field = array(
                  
                    array(
                            'label'      => 'Name',
                            'id'         => 'saswp_data_feed_schema_name_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => 'Description',
                            'id'         => 'saswp_data_feed_schema_description_'.$schema_id,
                            'type'       => 'textarea',                           
                    ),
                    array(
                            'label'      => 'DateModified',
                            'id'         => 'saswp_data_feed_schema_date_modified_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => 'License',
                            'id'         => 'saswp_data_feed_schema_license_'.$schema_id,
                            'type'       => 'text',                           
                    )    
                   );
                    break;
                
                case 'MusicPlaylist':
                    
                    $meta_field = array(
                   array(
                           'label'      => 'ID',
                           'id'         => 'saswp_music_playlist_id_'.$schema_id,
                           'type'       => 'text',
                           'default'    => 'MusicPlaylist'   
                        ),
                    array(
                            'label'      => 'Name',
                            'id'         => 'saswp_music_playlist_name_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => 'Description',
                            'id'         => 'saswp_music_playlist_description_'.$schema_id,
                            'type'       => 'textarea',                           
                    ), 
                    array(
                            'label'      => 'URL',
                            'id'         => 'saswp_music_playlist_url_'.$schema_id,
                            'type'       => 'text',                           
                    )                            
                   );
                    break;
                
                case 'MusicAlbum':
                    
                    $meta_field = array(
                        array(
                                'label'      => 'ID',
                                'id'         => 'saswp_music_album_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'MusicPlaylist'   
                             ),
                    array(
                            'label'      => 'Name',
                            'id'         => 'saswp_music_album_name_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => 'Description',
                            'id'         => 'saswp_music_album_description_'.$schema_id,
                            'type'       => 'textarea',                           
                    ),
                    array(
                            'label'      => 'Genre',
                            'id'         => 'saswp_music_album_genre_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => 'Image',
                            'id'         => 'saswp_music_album_image_'.$schema_id,
                            'type'       => 'media',                           
                    ),
                    array(
                            'label'      => 'Artist',
                            'id'         => 'saswp_music_album_artist_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => 'URL',
                            'id'         => 'saswp_music_album_url_'.$schema_id,
                            'type'       => 'text',                           
                    )    
                        
                   );
                    break;
                
                case 'Book':
                    
                    $meta_field = array(
                   array(
                            'label'      => 'ID',
                            'id'         => 'saswp_book_id_'.$schema_id,
                            'type'       => 'text',
                            'default'    => 'Book'   
                        ),
                    array(
                            'label'      => 'Name',
                            'id'         => 'saswp_book_name_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => 'Description',
                            'id'         => 'saswp_book_description_'.$schema_id,
                            'type'       => 'textarea',                           
                    ),
                    array(
                            'label'      => 'URL',
                            'id'         => 'saswp_book_url_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => 'Image',
                            'id'         => 'saswp_book_image_'.$schema_id,
                            'type'       => 'media',                           
                    ),
                    array(
                        'label'      => 'Author Type',
                        'id'         => 'saswp_book_author_type_'.$schema_id,
                        'type'       => 'select',
                        'options'    => array(
                                'Person'           => 'Person',
                                'Organization'     => 'Organization',                        
                       )           
                    ),
                    array(
                            'label'      => 'Author',
                            'id'         => 'saswp_book_author_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => 'Author Profile URL',
                            'id'         => 'saswp_book_author_url_'.$schema_id,
                            'type'       => 'text',                           
                    ),    
                    array(
                            'label'      => 'ISBN',
                            'id'         => 'saswp_book_isbn_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => 'Number Of Page',
                            'id'         => 'saswp_book_no_of_page_'.$schema_id,
                            'type'       => 'text',                           
                    ),    
                    array(
                        'label'      => 'Book Format',
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
                        'label'      => 'In Language',
                        'id'         => 'saswp_book_inlanguage_'.$schema_id,
                        'type'       => 'text',       
                        'attributes' => array(
                                'placeholder' => 'English'
                        ),                    
                    ),    
                    array(
                            'label'      => 'Publisher',
                            'id'         => 'saswp_book_publisher_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => 'Published Date',
                            'id'         => 'saswp_book_date_published_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'   => 'Availability',
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
                            'label'      => 'Price',
                            'id'         => 'saswp_book_price_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => 'Price Currency',
                            'id'         => 'saswp_book_price_currency_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label' => 'Aggregate Rating',
                            'id'    => 'saswp_book_enable_rating_'.$schema_id,
                            'type'  => 'checkbox',                            
                    ),
                    array(
                            'label'   => 'Rating',
                            'id'      => 'saswp_book_rating_value_'.$schema_id,
                            'type'    => 'text',
                            
                    ),
                    array(
                            'label'   => 'Rating Count',
                            'id'      => 'saswp_book_rating_count_'.$schema_id,
                            'type'    => 'text',                            
                    ),                                                                            
                   );
                    break;
                
                case 'MusicComposition':
                    
                    $meta_field = array(
                        array(
                                'label'      => 'ID',
                                'id'         => 'saswp_music_composition_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'MusicComposition'   
                                ),
                    array(
                            'label'      => 'Name',
                            'id'         => 'saswp_music_composition_name_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => 'Description',
                            'id'         => 'saswp_music_composition_description_'.$schema_id,
                            'type'       => 'textarea',                           
                    ),
                    array(
                            'label'      => 'Lyrics',
                            'id'         => 'saswp_music_composition_lyrics_'.$schema_id,
                            'type'       => 'textarea',                           
                    ),    
                    array(
                            'label'      => 'URL',
                            'id'         => 'saswp_music_composition_url_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => 'ISWC Code',
                            'id'         => 'saswp_music_composition_iswccode_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => 'Image',
                            'id'         => 'saswp_music_composition_image_'.$schema_id,
                            'type'       => 'media',                           
                    ),
                    array(
                            'label'      => 'inLanguage',
                            'id'         => 'saswp_music_composition_inlanguage_'.$schema_id,
                            'type'       => 'text',                           
                    ),                         
                    array(
                            'label'      => 'Publisher',
                            'id'         => 'saswp_music_composition_publisher_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                     array(
                            'label'     => 'Date Published',
                            'id'        => 'saswp_music_composition_date_published_'.$schema_id,
                            'type'      => 'text',
                            'default'   => get_the_date("Y-m-d")
                    ),    
                   );
                    break;
                
                case 'Organization':
                    
                    $meta_field = array(    
                        array(
                                'label'      => 'ID',
                                'id'         => 'saswp_organization_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'Organization'   
                        ),                    
                    array(
                            'label'      => 'Name',
                            'id'         => 'saswp_organization_name_'.$schema_id,
                            'type'       => 'text',                           
                        ),
                    array(
                            'label'      => 'Description',
                            'id'         => 'saswp_organization_description_'.$schema_id,
                            'type'       => 'textarea',                           
                        ),    
                    array(
                            'label'      => 'URL',
                            'id'         => 'saswp_organization_url_'.$schema_id,
                            'type'       => 'text',                           
                        ), 
                    array(
                           'label'      => 'Image',
                           'id'         => 'saswp_organization_image_'.$schema_id,
                           'type'       => 'media',                           
                        ),
                    array(
                            'label'      => 'Logo',
                            'id'         => 'saswp_organization_logo_'.$schema_id,
                            'type'       => 'media',                           
                        ), 
                    array(
                            'label'      => 'Street Address',
                            'id'         => 'saswp_organization_street_address_'.$schema_id,
                            'type'       => 'text',                           
                        ),
                        array(
                            'label'      => 'City',
                            'id'         => 'saswp_organization_city_'.$schema_id,
                            'type'       => 'text',                           
                        ),
                        array(
                            'label'      => 'State',
                            'id'         => 'saswp_organization_state_'.$schema_id,
                            'type'       => 'text',                           
                        ),
                        array(
                            'label'      => 'Country',
                            'id'         => 'saswp_organization_country_'.$schema_id,
                            'type'       => 'text',                           
                        ),
                        array(
                            'label'      => 'Postal Code',
                            'id'         => 'saswp_organization_postal_code_'.$schema_id,
                            'type'       => 'text',                           
                        ),
                        array(
                                'label'      => 'Email',
                                'id'         => 'saswp_organization_email_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                        array(
                            'label'      => 'Telephone',
                            'id'         => 'saswp_organization_telephone_'.$schema_id,
                            'type'       => 'text',                           
                        ),
                        array(
                                'label'      => 'Website',
                                'id'         => 'saswp_organization_website_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                            array(
                                'label'      => 'Facebook',
                                'id'         => 'saswp_organization_facebook_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                            array(
                                'label'      => 'Twitter',
                                'id'         => 'saswp_organization_twitter_'.$schema_id,
                                'type'       => 'text',                           
                           ),
                           array(
                                'label'      => 'LinkedIn',
                                'id'         => 'saswp_organization_linkedin_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                           array(
                                'label'      => 'Threads',
                                'id'         => 'saswp_organization_threads_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                           array(
                                'label'      => 'Mastodon',
                                'id'         => 'saswp_organization_mastodon_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                           array(
                                'label'      => 'Vibehut',
                                'id'         => 'saswp_organization_vibehut_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                            array(
                                'label'      => 'Founder',
                                'id'         => 'saswp_organization_founder_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                            array(
                                'label'      => 'Founding Date',
                                'id'         => 'saswp_organization_founding_date_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                            array(
                                'label'      => 'Dun & Bradstreet DUNS',
                                'id'         => 'saswp_organization_duns_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                            array(
                                'label'      => 'Qualifications ( Credential Awarded)',
                                'id'         => 'saswp_organization_qualifications_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                            array(
                                'label'      => 'Knows About',
                                'id'         => 'saswp_organization_knows_about_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                            array(
                                'label'      => 'Member Of',
                                'id'         => 'saswp_organization_member_of_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                            array(
                                'label'      => 'Parent Organization',
                                'id'         => 'saswp_organization_parent_organization_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                        array(
                            'label'      => 'Aggregate Rating',
                            'id'         => 'saswp_organization_enable_rating_'.$schema_id,
                            'type'       => 'checkbox',                            
                        ),
                        array(
                            'label'      => 'Rating',
                            'id'         => 'saswp_organization_rating_value_'.$schema_id,
                            'type'       => 'text',                           
                        ),
                        array(
                            'label'      => 'Rating Count',
                            'id'         => 'saswp_organization_rating_count_'.$schema_id,
                            'type'       => 'text',                            
                        ),    
                                                                                        
                   );
                    break;
                
                    case 'Project':
                    
                        $meta_field = array(   
                        array(
                                'label'      => 'ID',
                                'id'         => 'saswp_project_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'Project'   
                        ),                      
                        array(
                                'label'      => 'Name',
                                'id'         => 'saswp_project_name_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                        array(
                                'label'      => 'Description',
                                'id'         => 'saswp_project_description_'.$schema_id,
                                'type'       => 'textarea',                           
                            ),    
                        array(
                                'label'      => 'URL',
                                'id'         => 'saswp_project_url_'.$schema_id,
                                'type'       => 'text',                           
                            ), 
                        array(
                               'label'      => 'Image',
                               'id'         => 'saswp_project_image_'.$schema_id,
                               'type'       => 'media',                           
                            ),
                        array(
                                'label'      => 'Logo',
                                'id'         => 'saswp_project_logo_'.$schema_id,
                                'type'       => 'media',                           
                            ), 
                        array(
                                'label'      => 'Street Address',
                                'id'         => 'saswp_project_street_address_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                            array(
                                'label'      => 'City',
                                'id'         => 'saswp_project_city_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                            array(
                                'label'      => 'State',
                                'id'         => 'saswp_project_state_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                            array(
                                'label'      => 'Country',
                                'id'         => 'saswp_project_country_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                            array(
                                'label'      => 'Postal Code',
                                'id'         => 'saswp_project_postal_code_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                            array(
                                    'label'      => 'Email',
                                    'id'         => 'saswp_project_email_'.$schema_id,
                                    'type'       => 'text',                           
                                ),
                            array(
                                'label'      => 'Telephone',
                                'id'         => 'saswp_project_telephone_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                            array(
                                    'label'      => 'Website',
                                    'id'         => 'saswp_project_website_'.$schema_id,
                                    'type'       => 'text',                           
                                ),
                                array(
                                    'label'      => 'Facebook',
                                    'id'         => 'saswp_project_facebook_'.$schema_id,
                                    'type'       => 'text',                           
                                ),
                                array(
                                    'label'      => 'Twitter',
                                    'id'         => 'saswp_project_twitter_'.$schema_id,
                                    'type'       => 'text',                           
                               ),
                               array(
                                    'label'      => 'LinkedIn',
                                    'id'         => 'saswp_project_linkedin_'.$schema_id,
                                    'type'       => 'text',                           
                                ),
                               array(
                                    'label'      => 'Threads',
                                    'id'         => 'saswp_project_threads_'.$schema_id,
                                    'type'       => 'text',                           
                                ),
                               array(
                                    'label'      => 'Mastodon',
                                    'id'         => 'saswp_project_mastodon_'.$schema_id,
                                    'type'       => 'text',                           
                                ),
                               array(
                                    'label'      => 'Vibehut',
                                    'id'         => 'saswp_project_vibehut_'.$schema_id,
                                    'type'       => 'text',                           
                                ),
                                array(
                                    'label'      => 'Founder',
                                    'id'         => 'saswp_project_founder_'.$schema_id,
                                    'type'       => 'text',                           
                                ),
                                array(
                                    'label'      => 'Founding Date',
                                    'id'         => 'saswp_project_founding_date_'.$schema_id,
                                    'type'       => 'text',                           
                                ),
                                array(
                                    'label'      => 'Dun & Bradstreet DUNS',
                                    'id'         => 'saswp_project_duns_'.$schema_id,
                                    'type'       => 'text',                           
                                ),
                                array(
                                    'label'      => 'Qualifications ( Credential Awarded)',
                                    'id'         => 'saswp_project_qualifications_'.$schema_id,
                                    'type'       => 'text',                           
                                ),
                                array(
                                    'label'      => 'Knows About',
                                    'id'         => 'saswp_project_knows_about_'.$schema_id,
                                    'type'       => 'text',                           
                                ),
                                array(
                                    'label'      => 'Member Of',
                                    'id'         => 'saswp_project_member_of_'.$schema_id,
                                    'type'       => 'text',                           
                                ),
                                array(
                                    'label'      => 'Parent project',
                                    'id'         => 'saswp_project_parent_project_'.$schema_id,
                                    'type'       => 'text',                           
                                ),
                            array(
                                'label'      => 'Aggregate Rating',
                                'id'         => 'saswp_project_enable_rating_'.$schema_id,
                                'type'       => 'checkbox',                            
                            ),
                            array(
                                'label'      => 'Rating',
                                'id'         => 'saswp_project_rating_value_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                            array(
                                'label'      => 'Rating Count',
                                'id'         => 'saswp_project_rating_count_'.$schema_id,
                                'type'       => 'text',                            
                            ),    
                                                                                            
                       );
                        break;
                
                case 'Movie':
                    
                    $meta_field = array(          
                        array(
                                'label'      => 'ID',
                                'id'         => 'saswp_movie_id_'.$schema_id,
                                'type'       => 'text',
                                'default'    => 'movie'   
                        ),              
                        array(
                            'label'      => 'Name',
                            'id'         => 'saswp_movie_name_'.$schema_id,
                            'type'       => 'text',                           
                        ),
                        array(
                            'label'      => 'Description',
                            'id'         => 'saswp_movie_description_'.$schema_id,
                            'type'       => 'textarea',                           
                        ),
                        array(
                            'label'      => 'URL',
                            'id'         => 'saswp_movie_url_'.$schema_id,
                            'type'       => 'text',                           
                        ),
                        array(
                            'label'      => 'Image',
                            'id'         => 'saswp_movie_image_'.$schema_id,
                            'type'       => 'media',                           
                        ),
                        array(
                            'label'      => 'Date Created',
                            'id'         => 'saswp_movie_date_created_'.$schema_id,
                            'type'       => 'text',                           
                        ),
                        array(
                            'label'      => 'Director',
                            'id'         => 'saswp_movie_director_'.$schema_id,
                            'type'       => 'text',                           
                        ),
                        array(
                                'label'      => 'Actor',
                                'id'         => 'saswp_movie_actor_'.$schema_id,
                                'type'       => 'text',                           
                            ),
                        array(
                            'label'      => 'Aggregate Rating',
                            'id'         => 'saswp_movie_enable_rating_'.$schema_id,
                            'type'       => 'checkbox',                            
                        ),
                        array(
                            'label'      => 'Rating',
                            'id'         => 'saswp_movie_rating_value_'.$schema_id,
                            'type'       => 'text',                           
                        ),
                        array(
                            'label'      => 'Rating Count',
                            'id'         => 'saswp_movie_rating_count_'.$schema_id,
                            'type'       => 'text',                            
                        )                                                                                         
                   );
                    break;
                
                case 'TouristTrip':
                    $meta_field = array(
                        array(
                            'label'      => 'ID',
                            'id'         => 'saswp_tt_schema_id_'.$schema_id,
                            'type'       => 'text',
                            'default'    => 'TouristTrip'   
                        ),
                        array(
                            'label'      => 'Name',
                            'id'         => 'saswp_tt_schema_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                        ),
                        array(
                            'label'      => 'Description',
                            'id'         => 'saswp_tt_schema_description_'.$schema_id,
                            'type'       => 'textarea',
                            'attributes' => array(
                                'placeholder' => 'Description'
                            ), 
                        ),
                        array(
                            'label'      => 'Tourist Type',
                            'id'         => 'saswp_tt_schema_ttype_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Tourist Type'
                            ) 
                        ),
                        array(
                            'label'      => 'Subject Of Name',
                            'id'         => 'saswp_tt_schema_son_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Subject Of Name'
                            ), 
                        ),
                        array(
                            'label'      => 'Subject Of URL',
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
                            'label'   => 'Additional Type',
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
                            'label'      => 'Brand',
                            'id'         => 'saswp_vr_schema_brand_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Brand ID'
                            )
                        ),
                        array(
                            'label'      => 'Contains Place Additional Type',
                            'id'         => 'saswp_vr_schema_cpat_'.$schema_id,
                            'type'       => 'select',
                            'options' => array(
                                'EntirePlace' => 'EntirePlace',
                                'PrivateRoom' => 'PrivateRoom',
                                'SharedRoom' => 'SharedRoom'
                            )
                        ),
                        array(
                            'label'      => 'Occupancy',
                            'id'         => 'saswp_vr_schema_occupancy_'.$schema_id,
                            'type'       => 'number',
                            'attributes' => array(
                                'placeholder' => '4'
                            )
                        ),
                        array(
                            'label'      => 'Floor Size Value',
                            'id'         => 'saswp_vr_schema_floor_value_'.$schema_id,
                            'type'       => 'number',
                            'attributes' => array(
                                'placeholder' => '75'
                            )
                        ),
                        array(
                            'label'      => 'Floor Size Unit Code',
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
                            'label'      => 'Total Bathrooms',
                            'id'         => 'saswp_vr_schema_total_bathrooms_'.$schema_id,
                            'type'       => 'number',
                            'attributes' => array(
                                'placeholder' => '1'
                            )
                        ),
                        array(
                            'label'      => 'Number Of Bedrooms',
                            'id'         => 'saswp_vr_schema_total_bedrooms_'.$schema_id,
                            'type'       => 'number',
                            'attributes' => array(
                                'placeholder' => '3'
                            )
                        ),
                        array(
                            'label'      => 'Number Of Rooms',
                            'id'         => 'saswp_vr_schema_total_rooms_'.$schema_id,
                            'type'       => 'number',
                            'attributes' => array(
                                'placeholder' => '5'
                            )
                        ),
                        array(
                            'label'      => 'Identifier',
                            'id'         => 'saswp_vr_schema_identifier_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Enter Identifier'
                            )
                        ),
                        array(
                            'label'      => 'Latitude',
                            'id'         => 'saswp_vr_schema_latitude_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Enter Latitude'
                            )
                        ),
                        array(
                            'label'      => 'longitude',
                            'id'         => 'saswp_vr_schema_longitude_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Enter Longitude'
                            )
                        ),
                        array(
                            'label'      => 'Name',
                            'id'         => 'saswp_vr_schema_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Enter Name'
                            )
                        ),
                        array(
                            'label'      => 'Address Country',
                            'id'         => 'saswp_vr_schema_country_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'US'
                            )
                        ),
                        array(
                            'label'      => 'Address Locality',
                            'id'         => 'saswp_vr_schema_locality_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Mountain View'
                            )
                        ),
                        array(
                            'label'      => 'Address Region',
                            'id'         => 'saswp_vr_schema_region_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'California'
                            )
                        ),
                        array(
                            'label'      => 'Postal Code',
                            'id'         => 'saswp_vr_schema_p_code_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => '94043'
                            )
                        ),
                        array(
                            'label'      => 'Street Address',
                            'id'         => 'saswp_vr_schema_s_address_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => '1600 Amphitheatre Pkwy'
                            )
                        ),
                        array(
                                'label'    => 'Checkin Time',
                                'id'       => 'saswp_vr_schema_checkin_time_'.$schema_id,
                                'type'     => 'text',
                                'attributes' => array(
                                    'placeholder' => '18:00:00+08:00'
                                )                               
                            ),
                        array(
                                'label'    => 'Checkout Time',
                                'id'       => 'saswp_vr_schema_checkout_time_'.$schema_id,
                                'type'     => 'text',
                                'attributes' => array(
                                    'placeholder' => '11:00:00+08:00'
                                )                              
                            ),
                        array(
                                'label'    => 'Description',
                                'id'       => 'saswp_vr_schema_description_'.$schema_id,
                                'type'     => 'text'                             
                            ),
                        array(
                                'label'    => 'Knows Language',
                                'id'       => 'saswp_vr_schema_knows_language_'.$schema_id,
                                'type'     => 'text'                             
                            ),
                        array(
                                'label' => 'Aggregate Rating',
                                'id'    => 'saswp_vr_schema_enable_rating_'.$schema_id,
                                'type'  => 'checkbox',                           
                            ),
                        array(
                                'label' => 'Rating Value',
                                'id'    => 'saswp_vr_schema_rating_value_'.$schema_id,
                                'type'  => 'text',                           
                            ),
                        array(
                                'label' => 'Rating Count',
                                'id'    => 'saswp_vr_schema_rating_count_'.$schema_id,
                                'type'  => 'text',                            
                            ),
                        array(
                                'label' => 'Review Count',
                                'id'    => 'saswp_vr_schema_review_count_'.$schema_id,
                                'type'  => 'text',                            
                            ),
                        array(
                                'label' => 'Best rating',
                                'id'    => 'saswp_vr_schema_best_rating_'.$schema_id,
                                'type'  => 'text',                            
                            )
                    );
                    break;
                    
                    case 'LearningResource':
                    $meta_field = array(
                        array(
                               'label'      => 'ID',
                               'id'         => 'saswp_lr_id_'.$schema_id,
                               'type'       => 'text',
                               'default'    => 'LearningResource'   
                            ),
                        array(
                                'label'   => 'Name',
                                'id'      => 'saswp_lr_name_'.$schema_id,
                                'type'    => 'text',
                                'default' => saswp_get_the_title()
                            ),
                        array(
                                'label'   => 'Description',
                                'id'      => 'saswp_lr_description_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => is_object($post) ? $post->post_excerpt : ''
                            ),
                        array(
                                'label' => 'Keywords',
                                'id' => 'saswp_lr_keywords_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_tags()
                            ),
                        array(
                                'label' => 'Learning Resource Type',
                                'id' => 'saswp_lr_lrt_'.$schema_id,
                                'type' => 'text'
                            ),
                        array(
                                'label'   => 'In Language',
                                'id'      => 'saswp_lr_inlanguage_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_bloginfo('language'),
                                'note' => 'Note: If there are ore than one language, separate language list by comma ( , )' 
                            ),
                        array(
                                'label'      => 'Date Created',
                                'id'         => 'saswp_lr_date_created_'.$schema_id,
                                'type'       => 'text'                          
                            ),
                        array(
                                'label'      => 'Date Modified',
                                'id'         => 'saswp_lr_date_modified_'.$schema_id,
                                'type'       => 'text'                         
                            ),
                        array(
                                'label'      => 'Typical Age Range',
                                'id'         => 'saswp_lr_tar_'.$schema_id,
                                'type'       => 'text'                           
                            ),
                        array(
                                'label'   => 'Educational Level Name',
                                'id'      => 'saswp_lr_education_level_name_'.$schema_id,
                                'type'    => 'text'
                            ),
                        array(
                                'label'   => 'Educational Level URL',
                                'id'      => 'saswp_lr_education_level_url_'.$schema_id,
                                'type'    => 'text'
                            ),
                        array(
                                'label'   => 'Educational Level Term Set',
                                'id'      => 'saswp_lr_education_level_term_set_'.$schema_id,
                                'type'    => 'text'
                            ),
                        array(
                                'label'   => 'Time Required',
                                'id'      => 'saswp_lr_time_required_'.$schema_id,
                                'type'    => 'text'
                            ),
                        array(
                                'label'   => 'License',
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
                                'label'   => 'Is Accessible For Free',
                                'id'      => 'saswp_lr_time_iaff_'.$schema_id,
                                'type'    => 'select',
                                'options' => array(
                                    'True' => 'Yes',
                                    'False' => 'No'
                                )
                            ),

                        );
                    break;
                                
                default:
                    break;
            } 
            
            return $meta_field;
	}