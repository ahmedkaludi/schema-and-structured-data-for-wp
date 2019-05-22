<?php 
Class saswp_output_service{
            
        /**
         * List of hooks used in current class
         */
        public function saswp_service_hooks(){
            
           add_action( 'wp_ajax_saswp_get_custom_meta_fields', array($this, 'saswp_get_custom_meta_fields')); 
           add_action( 'wp_ajax_saswp_get_schema_type_fields', array($this, 'saswp_get_schema_type_fields')); 
           
        }    
               
        /**
         * This function replaces the value of schema's fields with the selected custom meta field
         * @param type $input1
         * @param type $schema_post_id
         * @return type array
         */
        public function saswp_replace_with_custom_fields_value($input1, $schema_post_id){
           
            global $post;
            
            $custom_fields    = esc_sql ( get_post_meta($schema_post_id, 'saswp_custom_fields', true)  );
                        
            if(!empty($custom_fields)){
                
                 $schema_type = get_post_meta( $schema_post_id, 'schema_type', true); 
                 
                 foreach ($custom_fields as $key => $field){
                     
                    if(is_object($post)){
                        
                        $custom_fields[$key] = get_post_meta($post->ID, $field, true);                   
                        
                    } 
                                      
                }  
                
             switch ($schema_type) {
                 
                case 'local_business':
                   
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
                     $input1['url'] =    $custom_fields['local_business_name_url'];
                    }
                    if(isset($custom_fields['local_business_description'])){
                     $input1['description'] =    $custom_fields['local_business_description'];
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
                    if(isset($custom_fields['local_latitude'])){
                     $input1['geo']['latitude'] =    $custom_fields['local_latitude'];
                    }                    
                    if(isset($custom_fields['local_longitude'])){
                     $input1['geo']['longitude'] =    $custom_fields['local_longitude'];
                    }                                           
                    if(isset($custom_fields['local_phone'])){
                     $input1['telephone'] =    $custom_fields['local_phone'];
                    }
                    if(isset($custom_fields['local_website'])){
                     $input1['website'] =    $custom_fields['local_website'];
                    }
                    if(isset($custom_fields['local_business_logo'])){
                     $input1['Publisher']['logo']['url'] =    $custom_fields['local_business_logo'];
                    }
                    if(isset($custom_fields['saswp_dayofweek'])){
                     $input1['openingHours'] =    $custom_fields['saswp_dayofweek'];
                    }
                    if(isset($custom_fields['local_price_range'])){
                     $input1['priceRange'] =    $custom_fields['local_price_range'];
                    }
                    if(isset($custom_fields['local_hasmap'])){
                     $input1['hasMap'] =    $custom_fields['local_hasmap'];
                    }
                    if(isset($custom_fields['local_menu'])){
                     $input1['hasMenu'] =    $custom_fields['local_menu'];
                    }
                                     
                    break;
                
                case 'Blogposting':
                                       
                    if(isset($custom_fields['saswp_blogposting_main_entity_of_page'])){
                     $input1['mainEntityOfPage'] =    $custom_fields['saswp_blogposting_main_entity_of_page'];
                    }
                    if(isset($custom_fields['saswp_blogposting_headline'])){
                     $input1['headline'] =    $custom_fields['saswp_blogposting_headline'];
                    }
                    if(isset($custom_fields['saswp_blogposting_description'])){
                     $input1['description'] =    $custom_fields['saswp_blogposting_description'];
                    }
                    if(isset($custom_fields['saswp_blogposting_name'])){
                     $input1['name'] =    $custom_fields['saswp_blogposting_name'];
                    }
                    if(isset($custom_fields['saswp_blogposting_url'])){
                     $input1['url'] =    $custom_fields['saswp_blogposting_url'];
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
                    if(isset($custom_fields['saswp_blogposting_organization_name'])){
                     $input1['Publisher']['name'] =    $custom_fields['saswp_blogposting_organization_name'];
                    }
                    if(isset($custom_fields['saswp_blogposting_organization_logo'])){
                     $input1['Publisher']['logo']['url'] =    $custom_fields['saswp_blogposting_organization_logo'];
                    }
                    
                    break;
                    
                case 'AudioObject':
                    
                    if(isset($custom_fields['saswp_audio_schema_name'])){
                     $input1['name'] =    $custom_fields['saswp_audio_schema_name'];
                    }
                    if(isset($custom_fields['saswp_audio_schema_description'])){
                     $input1['description'] =    $custom_fields['saswp_audio_schema_description'];
                    }
                    if(isset($custom_fields['saswp_audio_schema_contenturl'])){
                     $input1['contentUrl'] =    $custom_fields['saswp_audio_schema_contenturl'];
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
                     $input1['author']['name'] =    $custom_fields['saswp_audio_author_name'];
                    }                    
                    
                    break;   
                    
                case 'SoftwareApplication':
                    
                    if(isset($custom_fields['saswp_software_schema_name'])){
                     $input1['name'] =    $custom_fields['saswp_software_schema_name'];
                    }
                    if(isset($custom_fields['saswp_software_schema_description'])){
                     $input1['description'] =    $custom_fields['saswp_software_schema_description'];
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
                                                            
                    break;       
                
                case 'NewsArticle':
                                                                  
                    if(isset($custom_fields['saswp_newsarticle_main_entity_of_page'])){
                     $input1['mainEntityOfPage'] =    $custom_fields['saswp_newsarticle_main_entity_of_page'];
                    }
                    if(isset($custom_fields['saswp_newsarticle_URL'])){
                       $input1['url'] =    $custom_fields['saswp_newsarticle_URL']; 
                    }
                    if(isset($custom_fields['saswp_newsarticle_headline'])){
                       $input1['headline'] =    $custom_fields['saswp_newsarticle_headline']; 
                    }
                    if(isset($custom_fields['saswp_newsarticle_date_published'])){
                       $input1['datePublished'] =    $custom_fields['saswp_newsarticle_date_published']; 
                    }
                    if(isset($custom_fields['saswp_newsarticle_date_modified'])){
                       $input1['dateModified'] =    $custom_fields['saswp_newsarticle_date_modified']; 
                    }
                    if(isset($custom_fields['saswp_newsarticle_description'])){
                       $input1['description'] =    $custom_fields['saswp_newsarticle_description'];  
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
                    if(isset($custom_fields['saswp_newsarticle_author_image'])){
                       $input1['author']['Image']['url'] =    $custom_fields['saswp_newsarticle_author_image'];  
                    }
                    if(isset($custom_fields['saswp_newsarticle_organization_name'])){
                       $input1['Publisher']['name'] =    $custom_fields['saswp_newsarticle_organization_name'];  
                    }
                    if(isset($custom_fields['saswp_newsarticle_organization_logo'])){
                       $input1['Publisher']['logo']['url'] =    $custom_fields['saswp_newsarticle_organization_logo'];  
                    }                 
                                        
                    break;
                
                case 'WebPage':
                    
                    if(isset($custom_fields['saswp_webpage_name'])){
                     $input1['name'] =    $custom_fields['saswp_webpage_name'];
                    }
                    if(isset($custom_fields['saswp_webpage_url'])){
                     $input1['url'] =    $custom_fields['saswp_webpage_url'];
                    }
                    if(isset($custom_fields['saswp_webpage_description'])){
                     $input1['description'] =    $custom_fields['saswp_webpage_description'];
                    }
                    
                    if(isset($custom_fields['saswp_webpage_main_entity_of_page'])){
                     $input1['mainEntity']['mainEntityOfPage'] =    $custom_fields['saswp_webpage_main_entity_of_page'];
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
                    
                    if(isset($custom_fields['saswp_webpage_organization_name'])){
                     $input1['mainEntity']['Publisher']['name'] =    $custom_fields['saswp_webpage_organization_name'];
                    }
                    if(isset($custom_fields['saswp_webpage_organization_logo'])){
                     $input1['mainEntity']['Publisher']['logo']['url'] =    $custom_fields['saswp_webpage_organization_logo'];
                    }
                    
                    break;
                
                case 'Article':      
                      
                    if(isset($custom_fields['saswp_article_main_entity_of_page'])){
                     $input1['mainEntityOfPage'] =    $custom_fields['saswp_article_main_entity_of_page'];
                    }
                    if(isset($custom_fields['saswp_article_image'])){
                     $input1['image'] =    $custom_fields['saswp_article_image'];
                    }
                    if(isset($custom_fields['saswp_article_headline'])){
                     $input1['headline'] =    $custom_fields['saswp_article_headline'];
                    }
                    
                    if(isset($custom_fields['saswp_article_description'])){
                     $input1['description'] =    $custom_fields['saswp_article_description'];
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
                    if(isset($custom_fields['saswp_article_organization_name'])){
                     $input1['Publisher']['name'] =    $custom_fields['saswp_article_organization_name'];
                    }
                    if(isset($custom_fields['saswp_article_organization_logo'])){
                     $input1['Publisher']['logo']['url'] =    $custom_fields['saswp_article_organization_logo'];
                    }
                    break;
                    
                case 'Event':      
                      
                    if(isset($custom_fields['saswp_event_schema_name'])){
                     $input1['name'] =    $custom_fields['saswp_event_schema_name'];
                    }
                    if(isset($custom_fields['saswp_event_schema_description'])){
                     $input1['description'] =    $custom_fields['saswp_event_schema_description'];
                    }
                    if(isset($custom_fields['saswp_event_schema_location_name'])){
                     $input1['location']['name'] =    $custom_fields['saswp_event_schema_location_name'];
                    }
                    
                    if(isset($custom_fields['saswp_event_schema_location_streetaddress'])){
                     $input1['location']['address']['streetAddress'] =    $custom_fields['saswp_event_schema_location_streetaddress'];
                    }
                    if(isset($custom_fields['saswp_event_schema_location_locality'])){
                     $input1['location']['address']['addressLocality'] =    $custom_fields['saswp_event_schema_location_locality'];
                    }
                    if(isset($custom_fields['saswp_event_schema_location_region'])){
                     $input1['location']['address']['addressRegion'] =    $custom_fields['saswp_event_schema_location_region'];
                    }
                    
                    if(isset($custom_fields['saswp_event_schema_location_postalcode'])){
                     $input1['location']['address']['postalCode'] =    $custom_fields['saswp_event_schema_location_postalcode'];
                    }
                    if(isset($custom_fields['saswp_event_schema_start_date'])){
                     $input1['startDate'] =    $custom_fields['saswp_event_schema_start_date'];
                    }
                    if(isset($custom_fields['saswp_event_schema_end_date'])){
                     $input1['endDate'] =    $custom_fields['saswp_event_schema_end_date'];
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
                    
                    break;    
                    
                case 'TechArticle':      
                      
                    if(isset($custom_fields['saswp_tech_article_main_entity_of_page'])){
                     $input1['mainEntityOfPage'] =    $custom_fields['saswp_tech_article_main_entity_of_page'];
                    }
                    if(isset($custom_fields['saswp_tech_article_image'])){
                     $input1['image'] =    $custom_fields['saswp_tech_article_image'];
                    }
                    if(isset($custom_fields['saswp_tech_article_headline'])){
                     $input1['headline'] =    $custom_fields['saswp_tech_article_headline'];
                    }
                    
                    if(isset($custom_fields['saswp_tech_article_description'])){
                     $input1['description'] =    $custom_fields['saswp_tech_article_description'];
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
                    if(isset($custom_fields['saswp_tech_article_organization_name'])){
                     $input1['Publisher']['name'] =    $custom_fields['saswp_tech_article_organization_name'];
                    }
                    if(isset($custom_fields['saswp_tech_article_organization_logo'])){
                     $input1['Publisher']['logo']['url'] =    $custom_fields['saswp_tech_article_organization_logo'];
                    }
                    break;   
                    
                case 'Course':      
                      
                    if(isset($custom_fields['saswp_course_name'])){
                     $input1['name'] =    $custom_fields['saswp_course_name'];
                    }
                    if(isset($custom_fields['saswp_course_description'])){
                     $input1['description'] =    $custom_fields['saswp_course_description'];
                    }
                    if(isset($custom_fields['saswp_course_url'])){
                     $input1['url'] =    $custom_fields['saswp_course_url'];
                    }                    
                    if(isset($custom_fields['saswp_course_date_published'])){
                     $input1['datePublished'] =    $custom_fields['saswp_course_date_published'];
                    }
                    if(isset($custom_fields['saswp_course_date_modified'])){
                     $input1['dateModified'] =    $custom_fields['saswp_course_date_modified'];
                    }
                    if(isset($custom_fields['saswp_course_provider_name'])){
                     $input1['provider']['name'] =    $custom_fields['saswp_course_provider_name'];
                    }
                    
                    if(isset($custom_fields['saswp_course_sameas'])){
                     $input1['provider']['sameAs'] =    $custom_fields['saswp_course_sameas'];
                    }
                    
                    break;    
                    
                case 'DiscussionForumPosting':      
                      
                    if(isset($custom_fields['saswp_dfp_headline'])){
                     $input1['headline'] =    $custom_fields['saswp_dfp_headline'];
                    }
                    if(isset($custom_fields['saswp_dfp_description'])){
                     $input1['description'] =    $custom_fields['saswp_dfp_description'];
                    }
                    if(isset($custom_fields['saswp_dfp_url'])){
                     $input1['url'] =    $custom_fields['saswp_dfp_url'];
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
                                        
                    break;        
                
                case 'Recipe':
                    if(isset($custom_fields['saswp_recipe_url'])){
                     $input1['url'] =    $custom_fields['saswp_recipe_url'];
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
                     $input1['description'] =    $custom_fields['saswp_recipe_description'];
                    }
                    if(isset($custom_fields['saswp_recipe_main_entity'])){
                     $input1['mainEntity']['@id'] =    $custom_fields['saswp_recipe_main_entity'];
                    }
                    
                    if(isset($custom_fields['saswp_recipe_author_name'])){
                     $input1['author']['name'] =    $custom_fields['saswp_recipe_author_name'];
                    }
                    if(isset($custom_fields['saswp_recipe_author_image'])){
                     $input1['author']['Image']['url'] =    $custom_fields['saswp_recipe_author_image'];
                    }
                    if(isset($custom_fields['saswp_recipe_organization_name'])){
                     $input1['mainEntity']['Publisher']['name'] =    $custom_fields['saswp_recipe_organization_name'];
                    }
                    
                    if(isset($custom_fields['saswp_recipe_organization_logo'])){
                     $input1['mainEntity']['Publisher']['logo']['url'] =    $custom_fields['saswp_recipe_organization_logo'];
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
                     $input1['recipeIngredient'] =    $custom_fields['saswp_recipe_ingredient'];
                    }
                    if(isset($custom_fields['saswp_recipe_instructions'])){
                     $input1['recipeInstructions'] =    $custom_fields['saswp_recipe_instructions'];
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
                    break;
                
                case 'Product':                                                                                                  
                    if(isset($custom_fields['saswp_product_url'])){
                     $input1['url'] =    $custom_fields['saswp_product_url'];
                    }
                    if(isset($custom_fields['saswp_product_name'])){
                     $input1['name'] =    $custom_fields['saswp_product_name'];
                    }
                    
                    if(isset($custom_fields['saswp_product_brand'])){
                     $input1['brand']['name'] =    $custom_fields['saswp_product_brand'];
                    }
                    
                    if(isset($custom_fields['saswp_product_isbn'])){
                     $input1['isbn'] =    $custom_fields['saswp_product_isbn'];
                    }
                    if(isset($custom_fields['saswp_product_mpn'])){
                     $input1['mpn'] =    $custom_fields['saswp_product_mpn'];
                    }
                    if(isset($custom_fields['saswp_product_gtin8'])){
                     $input1['gtin8'] =    $custom_fields['saswp_product_gtin8'];
                    }                    
                    
                    if(isset($custom_fields['saswp_product_description'])){
                     $input1['description'] =    $custom_fields['saswp_product_description'];
                    }                    
                    if(isset($custom_fields['saswp_product_image'])){
                     $input1['image'] =    $custom_fields['saswp_product_image'];
                    }
                    if(isset($custom_fields['saswp_product_availability'])){
                     $input1['offers']['availability'] =    $custom_fields['saswp_product_availability'];
                    }
                    if(isset($custom_fields['saswp_product_price'])){
                     $input1['offers']['price'] =    $custom_fields['saswp_product_price'];
                    }
                    if(isset($custom_fields['saswp_product_currency'])){
                     $input1['offers']['priceCurrency'] =    $custom_fields['saswp_product_currency'];
                    }
                    if(isset($custom_fields['saswp_product_priceValidUntil'])){
                     $input1['offers']['priceValidUntil'] =    $custom_fields['saswp_product_priceValidUntil'];
                     $input1['offers']['url']             =    $custom_fields['saswp_product_priceValidUntil'];
                    }
                    break;
                
                case 'Service':
                    if(isset($custom_fields['saswp_service_schema_name'])){
                     $input1['name'] =    $custom_fields['saswp_service_schema_name'];
                    }
                    if(isset($custom_fields['saswp_service_schema_type'])){
                     $input1['serviceType'] =    $custom_fields['saswp_service_schema_type'];
                    }
                    if(isset($custom_fields['saswp_service_schema_provider_name'])){
                     $input1['provider']['name'] =    $custom_fields['saswp_service_schema_provider_name'];
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
                    if(isset($custom_fields['saswp_service_schema_price_range'])){
                    $input1['provider']['priceRange'] =    $custom_fields['saswp_service_schema_price_range'];
                    }
                    if(isset($custom_fields['saswp_service_schema_description'])){
                     $input1['description'] =    $custom_fields['saswp_service_schema_description'];
                    }
                    if(isset($custom_fields['saswp_service_schema_area_served'])){
                     $input1['areaServed'] =    $custom_fields['saswp_service_schema_area_served'];
                    }
                    if(isset($custom_fields['saswp_service_schema_service_offer'])){
                     $input1['hasOfferCatalog'] =    $custom_fields['saswp_service_schema_service_offer'];
                    }
                                                          
                    break;
                
                case 'Review':  
                    if(isset($custom_fields['saswp_review_schema_item_type'])){
                     $input1['itemReviewed']['@type'] =    $custom_fields['saswp_review_schema_item_type'];
                    }
                    if(isset($custom_fields['saswp_review_schema_name'])){
                     $input1['itemReviewed']['name'] =    $custom_fields['saswp_review_schema_name'];
                    }
                    if(isset($custom_fields['saswp_review_schema_description'])){
                     $input1['description'] =    $custom_fields['saswp_review_schema_description'];
                    }
                    
                    if(isset($custom_fields['saswp_review_schema_date_published'])){
                     $input1['datePublished'] =    $custom_fields['saswp_review_schema_date_published'];
                    }
                    if(isset($custom_fields['saswp_review_schema_date_modified'])){
                     $input1['dateModified'] =    $custom_fields['saswp_review_schema_date_modified'];
                    }
                    if(isset($custom_fields['saswp_review_schema_image'])){
                     $input1['itemReviewed']['image'] =    $custom_fields['saswp_review_schema_image'];
                    }
                    
                    if(isset($custom_fields['saswp_review_schema_price_range'])){
                     $input1['itemReviewed']['priceRange'] =    $custom_fields['saswp_review_schema_price_range'];
                    }
                    if(isset($custom_fields['saswp_review_schema_street_address'])){
                     $input1['itemReviewed']['address']['streetAddress'] =    $custom_fields['saswp_review_schema_street_address'];
                    }
                    if(isset($custom_fields['saswp_review_schema_locality'])){
                     $input1['itemReviewed']['address']['addressLocality'] =    $custom_fields['saswp_review_schema_locality'];
                    }
                    
                    if(isset($custom_fields['saswp_review_schema_region'])){
                     $input1['itemReviewed']['address']['addressRegion'] =    $custom_fields['saswp_review_schema_region'];
                    }
                    if(isset($custom_fields['saswp_review_schema_postal_code'])){
                     $input1['itemReviewed']['address']['postalCode'] =    $custom_fields['saswp_review_schema_postal_code'];
                    }
                    if(isset($custom_fields['saswp_review_schema_country'])){
                     $input1['itemReviewed']['address']['addressCountry'] =    $custom_fields['saswp_review_schema_country'];
                    }
                    if(isset($custom_fields['saswp_review_schema_telephone'])){
                     $input1['itemReviewed']['telephone'] =    $custom_fields['saswp_review_schema_telephone'];
                    }
                    if(isset($custom_fields['saswp_review_author_name'])){
                     $input1['author']['name'] =    $custom_fields['saswp_review_author_name'];
                    }
                    
                    break;
                
                case 'VideoObject':
                    
                    if(isset($custom_fields['saswp_video_object_url'])){
                     $input1['url'] =    $custom_fields['saswp_video_object_url'];
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
                     $input1['description'] =    $custom_fields['saswp_video_object_description'];
                    }
                    if(isset($custom_fields['saswp_video_object_name'])){
                     $input1['name'] =    $custom_fields['saswp_video_object_name'];
                    }
                    
                    if(isset($custom_fields['saswp_video_object_upload_date'])){
                     $input1['uploadDate'] =    $custom_fields['saswp_video_object_upload_date'];
                    }
                    if(isset($custom_fields['saswp_video_object_thumbnail_url'])){
                     $input1['thumbnailUrl'] =    $custom_fields['saswp_video_object_thumbnail_url'];
                    }                                        
                    if(isset($custom_fields['saswp_video_object_content_url'])){
                     $input1['thumbnailUrl'] =    $custom_fields['saswp_video_object_content_url'];
                    }
                    if(isset($custom_fields['saswp_video_object_embed_url'])){
                     $input1['thumbnailUrl'] =    $custom_fields['saswp_video_object_embed_url'];
                    }                                                            
                    if(isset($custom_fields['saswp_video_object_main_entity_id'])){
                     $input1['mainEntity']['@id'] =    $custom_fields['saswp_video_object_main_entity_id'];
                    }
                    
                    if(isset($custom_fields['saswp_video_object_author_name'])){
                     $input1['author']['name'] =    $custom_fields['saswp_video_object_author_name'];
                    }
                    if(isset($custom_fields['saswp_video_object_author_image'])){
                     $input1['author']['Image']['url'] =    $custom_fields['saswp_video_object_author_image'];
                    }
                    if(isset($custom_fields['saswp_video_object_organization_name'])){
                     $input1['Publisher']['name'] =    $custom_fields['saswp_video_object_organization_name'];
                    }
                    if(isset($custom_fields['saswp_video_object_organization_logo'])){
                     $input1['Publisher']['logo']['url'] =    $custom_fields['saswp_video_object_organization_logo'];
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
                    
                    if(isset($custom_fields['saswp_qa_date_created'])){
                     $input1['mainEntity']['dateCreated'] =    $custom_fields['saswp_qa_date_created'];
                    }
                    if(isset($custom_fields['saswp_qa_question_author_name'])){
                     $input1['mainEntity']['author']['name'] =    $custom_fields['saswp_qa_question_author_name'];
                    }
                    if(isset($custom_fields['saswp_qa_accepted_answer_text'])){
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

                     default:
                         break;
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
            
            $schema_type = isset( $_POST['schema_type'] ) ? sanitize_text_field( $_POST['schema_type'] ) : '';                      
            $meta_fields = $this->saswp_get_all_schema_type_fields($schema_type);             	    
            
            wp_send_json( $meta_fields );            
            
            wp_die();
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
             
             if (class_exists('WC_Product')) {
                 
	     $product = wc_get_product($post_id); 
             
             if(is_object($product)){                                 
                 
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
             
             if($product->get_short_description()){
                 
                 $product_details['product_description'] = $product->get_short_description();
                 
             }else if($product->get_description()){
                 
                 $product_details['product_description'] = $product->get_description();
                 
             }else{
                 
                 $product_details['product_description'] = strip_tags(get_the_excerpt());
                 
             }
                                       
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
                     if(strtolower($attribute['name']) == 'brand'){
                                            
                      $product_details['product_brand'] = $attribute['options'][0];   
                                                                 
                     }
                     
                 }
                 
             }
                          
             $product_image_id  = $product->get_image_id(); 
             
             $image_list = array();
             
             if($product_image_id){
                                                                    
              $image_details = wp_get_attachment_image_src($product_image_id, 'full');
              
              if(!empty($image_details)){
                  
                 $size_array = array('full', 'large', 'medium', 'thumbnail');
                                                   
                 for($i =0; $i< count($size_array); $i++){
                                                    
                    $image_details   = wp_get_attachment_image_src($product_image_id, $size_array[$i]); 

                        if(!empty($image_details)){

                                $image_list['image'][$i]['@type']  = 'ImageObject';
                                $image_list['image'][$i]['url']    = esc_url($image_details[0]);
                                $image_list['image'][$i]['width']  = esc_attr($image_details[1]);
                                $image_list['image'][$i]['height'] = esc_attr($image_details[2]);

                        }
                                                    
                   }
                 
                 }
              
             }
             
             if(!empty($image_list)){
                 
                 $product_details['product_image'] = $image_list;
             }
                               
             if(strtolower( $product->get_stock_status() ) == 'onbackorder'){
                 $product_details['product_availability'] = 'PreOrder';
             }else{
                 $product_details['product_availability'] = $product->get_stock_status();
             }
                          
             $product_details['product_price']        = $product->get_price();
             $product_details['product_sku']          = $product->get_sku();             
             
             if(isset($date_on_sale)){
                 
             $product_details['product_priceValidUntil'] = $date_on_sale->date('Y-m-d G:i:s');    
             
             }else{
                 
             $product_details['product_priceValidUntil'] = get_the_modified_date("Y-m-d\TH:i:s\Z"); 
             
             }       
             
             $product_details['product_currency'] = get_option( 'woocommerce_currency' );             
             
             $reviews_arr = array();
             $reviews     = get_approved_comments( $post_id );
             
             if($reviews){
                 
             foreach($reviews as $review){                 
                 
                 $reviews_arr[] = array(
                     'author'        => $review->comment_author,
                     'datePublished' => $review->comment_date,
                     'description'   => $review->comment_content,
                     'reviewRating'  => get_comment_meta( $review->comment_ID, 'rating', true ),
                 );
                 
             }   
             
             }    
             
             $product_details['product_review_count']   = $product->get_review_count();
             $product_details['product_average_rating'] = $product->get_average_rating();             
             $product_details['product_reviews']        = $reviews_arr;      
             
             }
             
             }                                                                 
             return $product_details;                       
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
            
            $post_meta   = esc_sql ( get_post_meta($post_id, $key='', true)  );                                       
            
            if(isset($post_meta['_post_review_box_breakdowns_score'])){
              $rating_value = bcdiv($post_meta['_post_review_box_breakdowns_score'][0], 20, 2);        
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
                'author'        => get_the_author(),
                'datePublished' => get_the_date("Y-m-d\TH:i:s\Z"),
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
                
                if($post_type =='dwqa-question' && isset($sd_data['saswp-dw-question-answer']) && $sd_data['saswp-dw-question-answer'] ==1 && is_plugin_active('dw-question-answer/dw-question-answer.php')){
                 
                $post_meta      = get_post_meta($post_id, $key='', true);
                
                if(isset($post_meta['_dwqa_best_answer'])){
                    
                    $best_answer_id = $post_meta['_dwqa_best_answer'][0];
                    
                }
                                                                                                                                              
                $dw_qa['@type']       = 'Question';
                $dw_qa['name']        = get_the_title(); 
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
                
                $dw_qa['dateCreated'] = get_the_date("Y-m-d\TH:i:s\Z");                                                   
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
                    
                $qa_page['@context']   = 'http://schema.org';
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
        public function saswp_get_all_schema_type_fields($schema_type, $id =null){
            
            $meta_field = array();
            
            if($schema_type == ''){
                
             $schema_type = get_post_meta( $id, 'schema_type', true);    
             
            }
            
            switch ($schema_type) {
                
                case 'local_business':
                   
                    $meta_field = array(                        
                        'saswp_business_type'        => 'Business Type',
                        'saswp_business_name'        => 'Sub Business Type',                           
                        'local_business_name'        => 'Business Name',                           
                        'local_business_name_url'    => 'URL',
                        'local_business_description' => 'Description',
                        'local_street_address'       => 'Street Address',                            
                        'local_city'                 => 'City',
                        'local_state'                => 'State',
                        'local_postal_code'          => 'Postal Code',
                        'local_latitude'             => 'Latitude',
                        'local_longitude'            => 'Longitude',
                        'local_phone'                => 'Phone',
                        'local_website'              => 'Website',
                        'local_business_logo'        => 'Image', 
                        'saswp_dayofweek'            => 'Operation Days',
                        'local_price_range'          => 'Price Range', 
                        'local_hasmap'               => 'HasMap',
                        'local_menu'                 => 'Memu',
                        );                   
                    break;
                
                case 'Blogposting':
                    
                    $meta_field = array(        
                        
                        'saswp_blogposting_main_entity_of_page' => 'Main Entity Of Page',
                        'saswp_blogposting_headline'            => 'Headline',
                        'saswp_blogposting_description'         => 'Description',                         
                        'saswp_blogposting_name'                => 'Name',
                        'saswp_blogposting_url'                 => 'URL',
                        'saswp_blogposting_date_published'      => 'Date Published',                         
                        'saswp_blogposting_date_modified'       => 'Date Modified',
                        'saswp_blogposting_author_name'         => 'Author Name',
                        'saswp_blogposting_organization_name'   => 'Organization Name', 
                        'saswp_blogposting_organization_logo'   => 'Organization Logo', 
                                                                                                                                            
                        ); 
                   
                    break;
                
                case 'NewsArticle':
                    
                   $meta_field = array(                        
                        'saswp_newsarticle_main_entity_of_page' => 'Main Entity Of Page',
                        'saswp_newsarticle_URL'                 => 'URL',
                        'saswp_newsarticle_headline'            => 'Headline',                         
                        'saswp_newsarticle_date_published'      => 'Date Published',
                        'saswp_newsarticle_date_modified'       => 'Date Modified',
                        'saswp_newsarticle_headline'            => 'Headline',                         
                        'saswp_newsarticle_description'         => 'Description',
                        'saswp_newsarticle_section'             => 'Article Section',
                        'saswp_newsarticle_body'                => 'Article Body',                         
                        'saswp_newsarticle_name'                => 'Name',
                        'saswp_newsarticle_thumbnailurl'        => 'Thumbnail URL',
                        'saswp_newsarticle_timerequired'        => 'Time Required',                         
                        'saswp_newsarticle_main_entity_id'      => 'Main Entity Id',
                        'saswp_newsarticle_author_name'         => 'Author Name',
                        'saswp_newsarticle_author_image'        => 'Author Image',                       
                        'saswp_newsarticle_organization_name'   => 'Organization Name',
                        'saswp_newsarticle_organization_logo'   => 'Organization Logo',                                                                       
                        ); 
                                        
                    break;
                
                case 'WebPage':
                    
                    $meta_field = array(                        
                        'saswp_webpage_name'                => 'Name',
                        'saswp_webpage_url'                 => 'URL',
                        'saswp_webpage_description'         => 'Description',                          
                        'saswp_webpage_main_entity_of_page' => 'Main Entity Of Page',
                        'saswp_webpage_image'               => 'Image',
                        'saswp_webpage_headline'            => 'Headline',                          
                        'saswp_webpage_date_published'      => 'Date Published',
                        'saswp_webpage_date_modified'       => 'Date Modified',
                        'saswp_webpage_author_name'         => 'Author Name',                          
                        'saswp_webpage_organization_name'   => 'Organization Name',
                        'saswp_webpage_organization_logo'   => 'Organization Logo',                          
                        ); 
                    
                    break;
                
                case 'Article':      
                    
                    $meta_field = array(                        
                        'saswp_article_main_entity_of_page' => 'Main Entity Of Page',
                        'saswp_article_image'               => 'Image',
                        'saswp_article_headline'            => 'Headline',                          
                        'saswp_article_description'         => 'Description',
                        'saswp_article_date_published'      => 'Date Published',
                        'saswp_article_date_modified'       => 'Date Modified',                          
                        'saswp_article_author_name'         => 'Author Name',
                        'saswp_article_organization_name'   => 'Organization Name',
                        'saswp_article_organization_logo'   => 'Organization Logo',  
                        
                        );                                        
                    break;
                
                case 'TechArticle':      
                    
                    $meta_field = array(                        
                        'saswp_tech_article_main_entity_of_page' => 'Main Entity Of Page',
                        'saswp_tech_article_image'               => 'Image',
                        'saswp_tech_article_headline'            => 'Headline',                          
                        'saswp_tech_article_description'         => 'Description',
                        'saswp_tech_article_date_published'      => 'Date Published',
                        'saswp_tech_article_date_modified'       => 'Date Modified',                          
                        'saswp_tech_article_author_name'         => 'Author Name',
                        'saswp_tech_article_organization_name'   => 'Organization Name',
                        'saswp_tech_article_organization_logo'   => 'Organization Logo',                          
                        );                                        
                    break;
                
                case 'Course':      
                    
                    $meta_field = array(                        
                        'saswp_course_name'           => 'Name',
                        'saswp_course_description'    => 'Description',
                        'saswp_course_url'            => 'URL',                          
                        'saswp_course_date_published' => 'Date Published',
                        'saswp_course_date_modified'  => 'Date Modified',
                        'saswp_course_provider_name'  => 'Provider Name',                          
                        'saswp_course_sameas'         => 'Provider SameAs',                                                
                        );                                        
                    break;
                
                case 'DiscussionForumPosting':      
                    
                    $meta_field = array(                        
                        'saswp_dfp_headline'           => 'Headline',
                        'saswp_dfp_description'        => 'Description',
                        'saswp_dfp_url'                => 'URL',                          
                        'saswp_dfp_date_published'     => 'Date Published',
                        'saswp_dfp_date_modified'      => 'Date Modified',
                        'saswp_dfp_author_name'        => 'Author Name',                                                                                                  
                        );     
                    
                    break;
                
                case 'Recipe':
                    
                    $meta_field = array(                        
                        'saswp_recipe_url'                  => 'URL',
                        'saswp_recipe_name'                 => 'Name',
                        'saswp_recipe_date_published'       => 'Date Published',                          
                        'saswp_recipe_date_modified'        => 'Date Modified',
                        'saswp_recipe_description'          => 'Description',
                        'saswp_recipe_main_entity'          => 'Main Entity Id',                        
                        'saswp_recipe_author_name'          => 'Author Name',
                        'saswp_recipe_author_image'         => 'Author Image',
                        'saswp_recipe_organization_name'    => 'Organization Name',                        
                        'saswp_recipe_organization_logo'    => 'Organization Logo',
                        'saswp_recipe_preptime'             => 'Prepare Time',
                        'saswp_recipe_cooktime'             => 'Cook Time',                        
                        'saswp_recipe_totaltime'            => 'Total Time',
                        'saswp_recipe_keywords'             => 'Keywords',
                        'saswp_recipe_recipeyield'          => 'Recipe Yield',                        
                        'saswp_recipe_category'             => 'Recipe Category',
                        'saswp_recipe_cuisine'              => 'Recipe Cuisine',
                        'saswp_recipe_nutrition'            => 'Nutrition',                        
                        'saswp_recipe_ingredient'           => 'Recipe Ingredient',
                        'saswp_recipe_instructions'         => 'Recipe Instructions',
                        'saswp_recipe_video_name'           => 'Video Name',                        
                        'saswp_recipe_video_description'    => 'Video Description',
                        'saswp_recipe_video_thumbnailurl'   => 'Video ThumbnailUrl',
                        'saswp_recipe_video_contenturl'     => 'Video ContentUrl',                        
                        'saswp_recipe_video_embedurl'       => 'Video EmbedUrl',
                        'saswp_recipe_video_upload_date'    => 'Video Upload Date',
                        'saswp_recipe_video_duration'       => 'Video Duration',
                    );
                    
                    break;
                
                case 'Product':
                    
                    $meta_field = array(                        
                        'saswp_product_url'         => 'URL',
                        'saswp_product_name'        => 'Name',
                        'saswp_product_description' => 'Description',                                             
                    );       
                    
                     if(is_plugin_active('woocommerce/woocommerce.php')){   
                         
                       $meta_field['saswp_product_image']            = 'Image';
                       $meta_field['saswp_product_availability']     = 'Availability';
                       $meta_field['saswp_product_price']            = 'Price';
                       $meta_field['saswp_product_currency']         = 'Price Currency';                         
                       $meta_field['saswp_product_brand']            = 'Brand';  
                       $meta_field['saswp_product_priceValidUntil']  = 'Price Valid Until';  
                       $meta_field['saswp_product_isbn']             = 'ISBN';  
                       $meta_field['saswp_product_mpn']              = 'MPN';  
                       $meta_field['saswp_product_gtin8']            = 'GTIN 8';  
                    } 
                    
                    break;
                
                case 'Service':
                    
                    $meta_field = array(                        
                        'saswp_service_schema_name'             => 'Name',
                        'saswp_service_schema_type'             => 'Service Type',
                        'saswp_service_schema_provider_name'    => 'Provider Name',
                        'saswp_service_schema_provider_type'    => 'Provider Type',
                        'saswp_service_schema_image'            => 'Image',
                        'saswp_service_schema_locality'         => 'Locality',
                        'saswp_service_schema_postal_code'      => 'Postal Code',
                        'saswp_service_schema_telephone'        => 'Telephone',
                        'saswp_service_schema_price_range'      => 'Price Range',
                        'saswp_service_schema_description'      => 'Description',
                        'saswp_service_schema_area_served'      => 'Area Served (City)',
                        'saswp_service_schema_service_offer'    => 'Service Offer',
                        'saswp_review_schema_country'           => 'Address Country',
                        'saswp_review_schema_telephone'         => 'Telephone',                        
                    );
                   
                    break;
                
                case 'Review':                    
                    $meta_field = array(
                        
                        'saswp_review_schema_item_type'         => 'Item Reviewed Type',
                        'saswp_review_schema_name'              => 'Name',
                        'saswp_review_schema_description'       => 'Description',
                        'saswp_review_schema_date_published'    => 'Date Published',
                        'saswp_review_schema_date_modified'     => 'Date Modified',
                        'saswp_review_schema_image'             => 'Image',
                        'saswp_review_schema_price_range'       => 'Price Range',
                        'saswp_review_schema_street_address'    => 'Street Address',
                        'saswp_review_schema_locality'          => 'Address Locality',
                        'saswp_review_schema_region'            => 'Address Region',
                        'saswp_review_schema_postal_code'       => 'Postal Code',
                        'saswp_review_schema_country'           => 'Address Country',
                        'saswp_review_schema_telephone'         => 'Telephone',
                        'saswp_review_author_name'             => 'Author Name',
                    );
                    break;
                
                case 'VideoObject':
                    
                    $meta_field = array(
                        
                        'saswp_video_object_url'                => 'URL',
                        'saswp_video_object_headline'           => 'Headline',
                        'saswp_video_object_date_published'     => 'Date Published',
                        'saswp_video_object_date_modified'      => 'Date Modified',
                        'saswp_video_object_description'        => 'Description',
                        'saswp_video_object_name'               => 'Name',
                        'saswp_video_object_upload_date'        => 'Upload Date',
                        'saswp_video_object_thumbnail_url'      => 'Thumbnail Url',
                        'saswp_video_object_content_url'        => 'Content URL',
                        'saswp_video_object_embed_url'          => 'Embed Url',
                        'saswp_video_object_main_entity_id'     => 'Main Entity Id',
                        'saswp_video_object_author_name'        => 'Author Name',
                        'saswp_video_object_author_image'       => 'Author Image',
                        'saswp_video_object_organization_name'  => 'Organization Name',
                        'saswp_video_object_organization_logo'  => 'Organization Logo',                                         
                    );
                    
                    break;
                
                case 'AudioObject':
                    
                    $meta_field = array(
                        
                        'saswp_audio_schema_name'           => 'Name',
                        'saswp_audio_schema_description'    => 'Description',
                        'saswp_audio_schema_contenturl'     => 'Content Url',
                        'saswp_audio_schema_duration'       => 'Duration',
                        'saswp_audio_schema_encoding_format'=> 'Encoding Format',
                        'saswp_audio_schema_date_published' => 'Date Published',
                        'saswp_audio_schema_date_modified'  => 'Date Modified',
                        'saswp_audio_schema_author_name'    => 'Author',                        
                    );
                    
                    break;
                
                case 'SoftwareApplication':
                    
                    $meta_field = array(
                        
                        'saswp_software_schema_name'                    => 'Name',
                        'saswp_software_schema_description'             => 'Description',
                        'saswp_software_schema_operating_system'        => 'Operating System',
                        'saswp_software_schema_application_category'    => 'Application Category',
                        'saswp_software_schema_price'                   => 'Price',
                        'saswp_software_schema_price_currency'          => 'Price Currency',                        
                        'saswp_software_schema_date_published'          => 'Date Published',
                        'saswp_software_schema_date_modified'           => 'Date Modified',                        
                    );
                    
                    break;
                
                case 'Event':
                    
                    $meta_field = array(
                        
                        'saswp_event_schema_name'                    => 'Name',
                        'saswp_event_schema_description'             => 'Description',
                        'saswp_event_schema_location_name'           => 'Location Name',
                        'saswp_event_schema_location_streetaddress'  => 'Location Street Address',
                        'saswp_event_schema_location_locality'       => 'Location Locality',
                        'saswp_event_schema_location_region'         => 'Location Region',                        
                        'saswp_event_schema_location_postalcode'     => 'PostalCode',
                        'saswp_event_schema_start_date'              => 'Start Date',                        
                        'saswp_event_schema_end_date'                => 'End Date',
                        'saswp_event_schema_image'                   => 'Image',
                        'saswp_event_schema_performer_name'          => 'Performer Name',
                        'saswp_event_schema_price'                   => 'Price',
                        'saswp_event_schema_price_currency'          => 'Price Currency',
                        'saswp_event_schema_availability'            => 'Availability',
                        'saswp_event_schema_validfrom'               => 'Valid From',
                        'saswp_event_schema_url'                     => 'URL',
                    );
                    
                    break;
                
                case 'qanda':
                    $meta_field = array(
                        
                        'saswp_qa_question_title'               => 'Question Title',
                        'saswp_qa_question_description'         => 'Question Description',
                        'saswp_qa_upvote_count'                 => 'Question Upvote Count',                        
                        'saswp_qa_date_created'                 => 'Question Date Created',
                        'saswp_qa_question_author_name'         => 'Author Name',
                        'saswp_qa_accepted_answer_text'         => 'Accepted Answer Text',
                        'saswp_qa_accepted_answer_date_created' => 'Accepted Answer Date Created',
                        'saswp_qa_accepted_answer_upvote_count' => 'Accepted Answer Upvote Count',
                        'saswp_qa_accepted_answer_url'          => 'Accepted Answer Url',
                        'saswp_qa_accepted_author_name'         => 'Accepted Answer Author Name',
                        'saswp_qa_suggested_answer_text'        => 'Suggested Answer Text',
                        'saswp_qa_suggested_answer_date_created'=> 'Suggested Answer Date Created',                        
                        'saswp_qa_suggested_answer_upvote_count'=> 'Suggested Answer Upvote Count',
                        'saswp_qa_suggested_answer_url'         => 'Suggested Answer Url',
                        'saswp_qa_suggested_author_name'        => 'Suggested Answer Author Name',
                                            
                    );                    
                    break;

                default:
                    break;
            }                      
            return $meta_field;
        }
                        
        /**
         * This function generate the schema markup by passed schema type
         * @global type $sd_data
         * @param type $schema_type
         * @return array
         */
        public function saswp_schema_markup_generator($schema_type){
            
                        global $post;

                        global $sd_data;
            
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
            
                        $author_id      = get_the_author_meta('ID');
                        $image_id 	= get_post_thumbnail_id();
			$image_details 	= wp_get_attachment_image_src($image_id, 'full');                       			
			$date 		= get_the_date("Y-m-d\TH:i:s\Z");
			$modified_date 	= get_the_modified_date("Y-m-d\TH:i:s\Z");
			$aurthor_name 	= '';   
                        
                        if(!$aurthor_name && is_object($post)){
			
                            $author_id    = get_post_field ('post_author', $post->ID);
                            $aurthor_name = get_the_author_meta( 'display_name' , $author_id );                         	
			}
                        
                        
            switch ($schema_type) {
                
                case 'TechArticle':
                    
                    $input1 = array(
					'@context'			=> 'http://schema.org',
					'@type'				=> 'TechArticle',
                                        '@id'				=> get_permalink().'/#techarticle',
                                        'url'				=> get_permalink(),
					'mainEntityOfPage'              => get_permalink(),					
					'headline'			=> get_the_title(),
					'description'                   => strip_tags(strip_shortcodes(get_the_excerpt())),
					'datePublished'                 => esc_html($date),
					'dateModified'                  => esc_html($modified_date),
					'author'			=> array(
							'@type' 	=> 'Person',
							'name'		=> esc_attr($aurthor_name) 
                                                         ),
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
					'@context'			=> 'http://schema.org',
					'@type'				=> 'Article',
                                        '@id'				=> get_permalink().'/#article',
                                        'url'				=> get_permalink(),
					'mainEntityOfPage'              => get_permalink(),					
					'headline'			=> get_the_title(),
					'description'                   => strip_tags(strip_shortcodes(get_the_excerpt())),
					'datePublished'                 => esc_html($date),
					'dateModified'                  => esc_html($modified_date),
					'author'			=> array(
							'@type' 	=> 'Person',
							'name'		=> esc_attr($aurthor_name) 
                                                         ),
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
                    
                    if(empty($image_details[0]) || $image_details[0] === NULL ){
					$image_details[0] = $logo;
                    }
                    
                    $input1 = array(
				'@context'			=> 'http://schema.org',
				'@type'				=> 'WebPage' ,
                                '@id'				=> get_permalink().'/#webpage',
				'name'				=> get_the_title(),
				'url'				=> get_permalink(),
				'description'                   => strip_tags(strip_shortcodes(get_the_excerpt())),
				'mainEntity'                    => array(
						'@type'			=> 'Article',
						'mainEntityOfPage'	=> get_permalink(),
						'image'			=> esc_url($image_details[0]),
						'headline'		=> get_the_title(),
						'description'		=> strip_tags(strip_shortcodes(get_the_excerpt())),
						'datePublished' 	=> esc_html($date),
						'dateModified'		=> esc_html($modified_date),
						'author'			=> array(
								'@type' 	=> 'Person',
								'name'		=> esc_attr($aurthor_name), ),
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

                default:
                    break;
            }
            
            if( !empty($input1) && !isset($input1['image'])){
                                                          
                    $input2 = $this->saswp_get_fetaure_image();
                    if(!empty($input2)){

                      $input1 = array_merge($input1,$input2);                                
                    }                                                                    
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
            
            global $sd_data;
            global $post;
            $input2          = array();
            $image_id 	     = get_post_thumbnail_id();
	    $image_details   = wp_get_attachment_image_src($image_id, 'full'); 
                        
            if( is_array($image_details) ){                                
                                    
                                                                                
                                        if(isset($image_details[1]) && ($image_details[1] < 1200) && function_exists('ampforwp_aq_resize')){
                                            
                                            $width  = array(1280, 640, 300);
                                            $height = array(720, 480, 300);
                                            
                                            for($i = 0; $i<3; $i++){
                                                
                                                $resize_image = ampforwp_aq_resize( $image_details[0], $width[$i], $height[$i], true, false, true );
                                                
                                                if(isset($resize_image[0]) && isset($resize_image[1]) && isset($resize_image[2]) ){
                                                
                                                                                                        
                                                    $input2['image'][$i]['@type']  = 'ImageObject';
                                                    
                                                    if($i == 0){
                                                        
                                                    $input2['image'][$i]['@id']    = get_permalink().'#primaryimage';    
                                                    
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
                                                     
                                                $size_array = array('full', 'large', 'medium', 'thumbnail');
                                                
                                                for($i =0; $i< count($size_array); $i++){
                                                    
                                                    $image_details   = wp_get_attachment_image_src($image_id, $size_array[$i]); 
													
                                                        if(!empty($image_details)){

                                                                $input2['image'][$i]['@type']  = 'ImageObject';
                                                                
                                                                if($i == 0){
                                                        
                                                                $input2['image'][$i]['@id']    = get_permalink().'#primaryimage'; 
                                                                
                                                                }
                                                                
                                                                $input2['image'][$i]['url']    = esc_url($image_details[0]);
                                                                $input2['image'][$i]['width']  = esc_attr($image_details[1]);
                                                                $input2['image'][$i]['height'] = esc_attr($image_details[2]);

                                                        }
                                                    
                                                    
                                                }                                                                                                                                                                                        
                                            
                                        } 
                                        
                                        if(empty($input2)){
                                            
                                                $input2['image']['@type']  = 'ImageObject';
                                                $input2['image']['@id']    = get_permalink().'#primaryimage';
                                                $input2['image']['url']    = esc_url($image_details[0]);
                                                $input2['image']['width']  = esc_attr($image_details[1]);
                                                $input2['image']['height'] = esc_attr($image_details[2]);
                                            
                                        }
                                        
                                                                                                                                                                                                 
                             }
                                                       
                          //Get All the images available on post   
                             
                          $content = get_the_content();   
                          
                          if($content){
                              
                          $regex   = '/<img(.*?)src="(.*?)"(.*?)>/';                          
                          preg_match_all( $regex, $content, $attachments );   
                                                                              
                          $attach_images = array();
                          
                          if(!empty($attachments)){
                              $k = 0;
                              foreach ($attachments[2] as $attachment) {
                                                                    
                                  $attach_details   = saswp_get_attachment_details_by_url($attachment, $post->ID, $k );
                                  
                                  if(!empty($attach_details)){
                                                                            
                                                $attach_images['image'][$k]['@type']  = 'ImageObject';                                                
                                                $attach_images['image'][$k]['url']    = esc_url($attachment);
                                                $attach_images['image'][$k]['width']  = esc_attr($attach_details[0]);
                                                $attach_images['image'][$k]['height'] = esc_attr($attach_details[1]);
                                      
                                  }
                                  
                                  $k++;
                              }
                              
                          }
                          
                          if(!empty($attach_images)){
                              
                              if(isset($input2['image'])){
                              
                                  $merged_arr = array_merge($input2['image'],$attach_images['image']);
                                  $input2['image'] = $merged_arr;
                                  
                              }else{
                                  $input2 = $attach_images;
                              }
                                                            
                          }
                          
                          }
                          
                          if(empty($input2)){
                              
                            if(isset($sd_data['sd_default_image']['url']) && $sd_data['sd_default_image']['url'] !=''){
                                        
                                    $input2['image']['@type']  = 'ImageObject';
                                    $input2['image']['@id']    = get_permalink().'#primaryimage';
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
                
                        global $sd_data;   
                        
                        $publisher    = array();
                        $default_logo = array();
                        $custom_logo  = array();
                                      
                        $logo      = isset($sd_data['sd_logo']) ?  $sd_data['sd_logo']['url']:'';	
			$height    = isset($sd_data['sd_logo']) ?  $sd_data['sd_logo']['height']:'';
			$width     = isset($sd_data['sd_logo']) ?  $sd_data['sd_logo']['width']:'';
                        $site_name = isset($sd_data['sd_name']) && $sd_data['sd_name'] !='' ? $sd_data['sd_name']:get_bloginfo();
                                                                                                                       
                        if($logo =='' && $height =='' && $width ==''){
                            
                            $sizes = array(
					'width'  => 600,
					'height' => 60,
					'crop'   => false,
				); 
                            
                            $custom_logo_id = get_theme_mod( 'custom_logo' );     
                            
                            if($custom_logo_id){
                                
                                $custom_logo    = wp_get_attachment_image_src( $custom_logo_id, $sizes);
                                
                            }
                            
                            if(isset($custom_logo)){
                                
                                $logo           = array_key_exists(0, $custom_logo)? $custom_logo[0]:'';
                                $height         = array_key_exists(1, $custom_logo)? $custom_logo[1]:'';
                                $width          = array_key_exists(2, $custom_logo)? $custom_logo[2]:'';
                            
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
