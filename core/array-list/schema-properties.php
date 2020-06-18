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

                $current_user       = wp_get_current_user();
                $author_desc        = get_the_author_meta( 'user_description' );
                $author_url         = get_the_author_meta( 'user_url' );                

                if(function_exists('get_avatar_data')){
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
                           'label' => 'URL',
                            'id' => 'local_business_name_url_'.$schema_id,
                            'type' => 'text',
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
                        $meta_field[] =   array(
                            'label' => 'Aggregate Rating',
                            'id' => 'local_enable_rating_'.$schema_id,
                            'type' => 'checkbox',                          
                        );
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
                            'label' => 'Main Entity Of Page',
                            'id' => 'saswp_blogposting_main_entity_of_page_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink()
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
                            'default' => get_the_excerpt()
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
                            'label' => 'Author Name',
                            'id' => 'saswp_blogposting_author_name_'.$schema_id,
                            'type' => 'text',
                            'default' => is_object($current_user) ? $current_user->display_name : ''
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
                            'label' => 'Headline',
                            'id' => 'saswp_newsarticle_headline_'.$schema_id,
                            'type' => 'text',
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
                            'default' => get_the_excerpt()
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
                            'default' => is_object($post) ? wp_strip_all_tags(strip_shortcodes($post->post_content)) : ''
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
                
                case 'WebPage':
                    $meta_field = array(
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
                            'default' => get_the_excerpt()
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
                
                    case 'Article':                                        
                        $meta_field = array(
                        array(
                                'label' => 'Main Entity Of Page',
                                'id' => 'saswp_article_main_entity_of_page_'.$schema_id,
                                'type' => 'text',
                                'default' => get_permalink()
                        ),
                        array(
                                'label'   => 'URL',
                                'id'      => 'saswp_article_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => get_permalink(),
                        ),    
                        array(
                                'label' => 'Image',
                                'id' => 'saswp_article_image_'.$schema_id,
                                'type' => 'media'                            
                        ),
                        array(
                                'label' => 'Headline',
                                'id' => 'saswp_article_headline_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_title()
                        ),
                        array(
                                'label' => 'Description',
                                'id' => 'saswp_article_description_'.$schema_id,
                                'type' => 'textarea',
                                'default' => get_the_excerpt()
                        ),
                        array(
                                'label'   => 'Article Section',
                                'id'      => 'saswp_article_section_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => get_the_excerpt()
                        ),    
                        array(
                                'label'   => 'Article Body',
                                'id'      => 'saswp_article_body_'.$schema_id,
                                'type'    => 'textarea',
                                'default' => is_object($post) ? wp_strip_all_tags(strip_shortcodes($post->post_content)) : ''
                        ),    
                        array(
                                'label' => 'Keywords',
                                'id' => 'saswp_article_keywords_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_get_the_tags()
                        ),    
                        array(
                                'label' => 'Date Published',
                                'id' => 'saswp_article_date_published_'.$schema_id,
                                'type' => 'text',
                                'default' => get_the_date("Y-m-d")
                        ), 
                        array(
                                'label' => 'Date Modified',
                                'id' => 'saswp_article_date_modified_'.$schema_id,
                                'type' => 'text',
                                'default' => get_the_modified_date("Y-m-d")
                        ),
                        array(
                                'label' => 'Author Name',
                                'id' => 'saswp_article_author_name_'.$schema_id,
                                'type' => 'text',
                                'default' => is_object($current_user) ? $current_user->display_name : ''
                        ),
                        array(
                                'label' => 'Author Description',
                                'id' => 'saswp_article_author_description_'.$schema_id,
                                'type' => 'textarea',
                                'default' => $author_desc
                        ),
                        array(
                                'label'   => 'Author URL',
                                'id'      => 'saswp_article_author_url_'.$schema_id,
                                'type'    => 'text',
                                'default' => $author_url
                        ),    
                        array(
                                'label' => 'Organization Name',
                                'id' => 'saswp_article_organization_name_'.$schema_id,
                                'type' => 'text',
                                'default' => saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                        ),
                        array(
                                'label' => 'Organization Logo',
                                'id'    => 'saswp_article_organization_logo_'.$schema_id,
                                'type'  => 'media',
                                'default' => isset($sd_data['sd_logo']['url']) ? $sd_data['sd_logo']['url']:''
                        ),
                        array(
                            'label' => 'Speakable',
                            'id' => 'saswp_article_speakable_'.$schema_id,
                            'type' => 'checkbox',
    
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
                                            'label' => 'Name',
                                            'id' => 'saswp_special_announcement_name_'.$schema_id,
                                            'type' => 'text',
                                            'default' => saswp_get_the_title()
                                    ),
                                    array(
                                            'label' => 'Description',
                                            'id' => 'saswp_special_announcement_description_'.$schema_id,
                                            'type' => 'textarea',
                                            'default' => get_the_excerpt()
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
                            'label' => 'Headline',
                            'id' => 'saswp_tech_article_headline_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_get_the_title()
                    ),
                    array(
                            'label' => 'Description',
                            'id' => 'saswp_tech_article_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => get_the_excerpt()
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
                            'label' => 'Author Name',
                            'id' => 'saswp_tech_article_author_name_'.$schema_id,
                            'type' => 'text',
                            'default' => is_object($current_user) ? $current_user->display_name : ''
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
                        'label' => 'Speakable',
                        'id' => 'saswp_tech_article_speakable_'.$schema_id,
                        'type' => 'checkbox',

                    )                        
                    );
                    break;
                
                case 'Course':                                        
                    $meta_field = array(
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
                            'default' => get_the_excerpt()
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
                            'label' => 'Description',
                            'id' => 'saswp_recipe_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => get_the_excerpt()
                    ),
                    array(
                            'label' => 'Main Entity Id',
                            'id' => 'saswp_recipe_main_entity_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink()
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
                            'label' => 'Nutrition',
                            'id' => 'saswp_recipe_nutrition_'.$schema_id,
                            'type' => 'text',
                            'attributes' => array(
                                'placeholder' => '270 calories'
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
                            'label' => 'Recipe Instructions',
                            'id' => 'saswp_recipe_instructions_'.$schema_id,
                            'type' => 'textarea',
                            'attributes' => array(
                                'placeholder' => 'Preheat the oven to 350 degrees F. Grease and flour a 9x9 inch pan; large bowl, combine flour, sugar, baking powder, and salt. pan.;'
                            ),
                            'note' => 'Note: Separate Instructions step by semicolon ( ; )'  
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
                            'type' => 'text', 
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
                                'default' => get_the_excerpt()
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
                            'label'   => 'Price',
                            'id'      => 'saswp_product_schema_price_'.$schema_id,
                            'type'    => 'text',
                            'default' => saswp_remove_warnings($product_details, 'product_price', 'saswp_string')
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
                                     'InStock'           => 'In Stock',
                                     'OutOfStock'        => 'Out Of Stock',
                                     'Discontinued'      => 'Discontinued',
                                     'PreOrder'          => 'Pre Order', 
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
                            'label' => 'Name',
                            'id' => 'saswp_service_schema_name_'.$schema_id,
                            'type' => 'text',                    
                    ),
                    array(
                            'label' => 'URL',
                            'id' => 'saswp_service_schema_url_'.$schema_id,
                            'type' => 'text',                    
                    ),    
                    array(
                            'label' => 'Service Type',
                            'id' => 'saswp_service_schema_type_'.$schema_id,
                            'type' => 'text',                            
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
                                     'GovernmentOrganization'       => 'Government Organization',
                                     'LocalBusiness'                => 'Local Business',
                                     'MedicalOrganization'          => 'Medical Organization',  
                                     'NGO'                          => 'NGO', 
                                     'PerformingGroup'              => 'Performing Group', 
                                     'SportsOrganization'           => 'Sports Organization',
                            ),                           
                    ),    
                    array(
                            'label' => 'Image',
                            'id' => 'saswp_service_schema_image_'.$schema_id,
                            'type' => 'media',                            
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
                            'default' => get_the_excerpt()                         
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
                            'label' => 'Name',
                            'id' => 'saswp_audio_schema_name_'.$schema_id,
                            'type' => 'text',                           
                    ),
                    array(
                            'label' => 'Description',
                            'id' => 'saswp_audio_schema_description_'.$schema_id,
                            'type' => 'textarea',                            
                    ),
                    array(
                            'label' => 'Content Url',
                            'id' => 'saswp_audio_schema_contenturl_'.$schema_id,
                            'type' => 'text',                            
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
                            'label' => 'Author Name',
                            'id' => 'saswp_audio_schema_author_name_'.$schema_id,
                            'type' => 'text',                            
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

                    $video_links      = saswp_get_video_links();                        

                    $meta_field = array(
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
                            'default' => get_the_excerpt()
                    ),
                    array(
                            'label'   => 'Transcript',
                            'id'      => 'saswp_video_object_transcript_'.$schema_id,
                            'type'    => 'textarea',
                            'default' => is_object($post) ? wp_strip_all_tags(strip_shortcodes($post->post_content)) : ''
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
                            'type' => 'text'                            
                    ),    
                    array(
                            'label'   => 'Embed Url',
                            'id'      => 'saswp_video_object_embed_url_'.$schema_id,
                            'type'    => 'text',
                            'default' => isset($video_links[0]) ? $video_links[0] : get_permalink()                            
                    ),    
                    array(
                            'label'   => 'Main Entity Id',
                            'id'      => 'saswp_video_object_main_entity_id_'.$schema_id,
                            'type'    => 'text',
                            'default' => get_permalink()
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
                            'default' => get_the_excerpt()
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
                            'type' => 'text',                           
                    ),
                    array(
                            'label' => 'Question Date Created',
                            'id' => 'saswp_qa_date_created_'.$schema_id,
                            'type' => 'text',                           
                    ),    
                    array(
                            'label' => 'Author Name',
                            'id' => 'saswp_qa_question_author_name_'.$schema_id,
                            'type' => 'text',                           
                    ),  
                    array(
                        'label' => 'Answer Count',
                        'id'    => 'saswp_qa_answer_count_'.$schema_id,
                        'type'  => 'text',                           
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
                    )
                   );
                    break;
                
                case 'MedicalCondition':
                    
                    $meta_field = array(
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
                            'label' => 'Number of Reviews',
                            'id' => 'saswp_vg_schema_review_count_'.$schema_id,
                            'type' => 'text',                           
                        ),    
                        
                   );
                    break;
                
                case 'TVSeries':
                    
                    $meta_field = array(
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
                            'label'      => 'Valid Through',
                            'id'         => 'saswp_jobposting_schema_validthrough_'.$schema_id,
                            'type'       => 'text',                             
                    ),
                    array(
                            'label'      => 'Employment Type',
                            'id'         => 'saswp_jobposting_schema_employment_type_'.$schema_id,
                            'type'       => 'select', 
                            'options'    => array(
                                'Full-Time'  => 'Full-Time',
                                'Part-Time'  => 'Part-Time',
                                'Contractor' => 'Contractor',       
                            )
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
                            'label'      => 'Street Address',
                            'id'         => 'saswp_jobposting_schema_street_address_'.$schema_id,
                            'type'       => 'text',                             
                    ),
                    array(
                            'label'      => 'Address Locality',
                            'id'         => 'saswp_jobposting_schema_locality_'.$schema_id,
                            'type'       => 'text',                             
                    ),
                    array(
                            'label'      => 'Address Region',
                            'id'         => 'saswp_jobposting_schema_region_'.$schema_id,
                            'type'       => 'text',                             
                    ),
                    array(
                            'label'      => 'Address Postal Code',
                            'id'         => 'saswp_jobposting_schema_postalcode_'.$schema_id,
                            'type'       => 'text',                             
                    ),
                    array(
                            'label'      => 'Address Country',
                            'id'         => 'saswp_jobposting_schema_country_'.$schema_id,
                            'type'       => 'text',                             
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

                    case 'FAQ':
                    
                        $meta_field = array(
                        array(
                                'label'      => 'Headline',
                                'id'         => 'saswp_faq_headline_'.$schema_id,
                                'type'       => 'text',
                                'default'    => get_the_title()                             
                        ),
                        array(
                                'label'      => 'Tags',
                                'id'         => 'saswp_faq_keywords_'.$schema_id,
                                'type'       => 'text',
                                'default'    => saswp_get_the_tags()                            
                        ),
                        array(
                                'label'      => 'Author',
                                'id'         => 'saswp_faq_author_'.$schema_id,
                                'type'       => 'text',
                                'default' => is_object($current_user) ? $current_user->display_name : ''                            
                        ),    
                        array(
                                'label'      => 'DateCreated',
                                'id'         => 'saswp_faq_date_created_'.$schema_id,
                                'type'       => 'text',
                                'default'    => get_the_date("Y-m-d")                            
                        ),
                        array(
                                'label'      => 'DatePublished',
                                'id'         => 'saswp_faq_date_published_'.$schema_id,
                                'type'       => 'text',
                                'default'    => get_the_date("Y-m-d")                            
                        ),
                        array(
                                'label'      => 'DateModified',
                                'id'         => 'saswp_faq_date_modified_'.$schema_id,
                                'type'       => 'text',
                                'default'    => get_the_modified_date("Y-m-d")                            
                        )                                                    
                       );                                                                 
                       
                        break;
                                
                case 'Person':
                    
                    $meta_field = array(
                    array(
                            'label'      => 'Name',
                            'id'         => 'saswp_person_schema_name_'.$schema_id,
                            'type'       => 'text',                           
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
                            'label'      => 'Company',
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
                        'label'      => 'Honorific Prefix',
                        'id'         => 'saswp_person_schema_honorific_prefix_'.$schema_id,
                        'type'       => 'text',                            
                    ),
                    array(
                        'label'      => 'Honorific Suffix',
                        'id'         => 'saswp_person_schema_honorific_suffix_'.$schema_id,
                        'type'       => 'text',                            
                    ),                                                                        
                   );
                    break;

                    case 'CreativeWorkSeries':
                    
                        $meta_field = array(
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
                                'type'       => 'text',                           
                        ),
                        array(
                                'label'      => 'Keywords',
                                'id'         => 'saswp_cws_schema_keywords_'.$schema_id,
                                'type'       => 'text',                           
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
                            'type'       => 'text',                           
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
                
                case 'Movie':
                    
                    $meta_field = array(                        
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
                                
                default:
                    break;
            } 
            
            return $meta_field;
	}