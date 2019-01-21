<?php 
Class saswp_output_service{
        public function __construct() {       
            
	}
        public function saswp_service_hooks(){
           add_action( 'wp_ajax_saswp_get_custom_meta_fields', array($this, 'saswp_get_custom_meta_fields')); 
           add_action( 'wp_ajax_saswp_get_schema_type_fields', array($this, 'saswp_get_schema_type_fields')); 
        }    
        
        
        public function saswp_replace_with_custom_fields_value($input1, $schema_post_id){
           
            $custom_fields    = esc_sql ( get_post_meta($schema_post_id, 'saswp_custom_fields', true)  );
            
            if(!empty($custom_fields)){
                 $schema_type = get_post_meta( $schema_post_id, 'schema_type', true); 
                 
                 foreach ($custom_fields as $key => $field){
                     
                   $custom_fields[$key] = get_post_meta($schema_post_id, $field, true);
                   
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


        public function saswp_get_schema_type_fields(){
            
             if ( ! isset( $_POST['saswp_security_nonce'] ) ){
                return; 
             }
             if ( !wp_verify_nonce( $_POST['saswp_security_nonce'], 'saswp_ajax_check_nonce' ) ){
                return;  
             }
            
            $schema_type = isset( $_POST['schema_type'] ) ? sanitize_text_field( $_POST['schema_type'] ) : '';
            $post_id = isset( $_POST['post_id'] ) ? sanitize_text_field( $_POST['post_id'] ) : '';            
            $meta_fields = $this->saswp_get_all_schema_type_fields($schema_type);             	    
            
            wp_send_json( $meta_fields );            
            
            wp_die();
        }
        
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
        public function saswp_woocommerce_product_details($post_id){     
                 
            
             $product_details = array();                
             if (class_exists('WC_Product')) {
	     $product = wc_get_product($post_id);             
             if(is_object($product)){                                 
             $gtin = get_post_meta($post_id, $key='hwp_product_gtin', true);
             if($gtin !=''){
             $product_details['product_gtin8'] = $gtin;   
             }             
             $brand = get_post_meta($post_id, $key='hwp_product_brand', true);
             if($brand !=''){
             $product_details['product_brand'] = $brand;   
             }
             $date_on_sale =    $product->get_date_on_sale_to();                            
             $product_details['product_name'] = $product->get_title();
             $product_details['product_description'] = $product->get_description();
             $product_details['product_image'] = $product->get_image();
             $product_details['product_availability'] = $product->get_stock_status();
             $product_details['product_price'] = $product->get_price();
             $product_details['product_sku'] = $product->get_sku();             
             if(isset($date_on_sale)){
             $product_details['product_priceValidUntil'] = $date_on_sale->date('Y-m-d G:i:s');    
             }                          
             $product_details['product_currency'] = get_option( 'woocommerce_currency' );             
             
             $reviews_arr = array();
             $reviews = get_approved_comments( $post_id );
             if($reviews){
             foreach($reviews as $review){                 
                 $reviews_arr[] = array(
                     'author' => $review->comment_author,
                     'datePublished' => $review->comment_date,
                     'description' => $review->comment_content,
                     'reviewRating' => get_comment_meta( $review->comment_ID, 'rating', true ),
                 );
             }    
             }                          
             $product_details['product_review_count'] = $product->get_review_count();
             $product_details['product_average_rating'] = $product->get_average_rating();             
             $product_details['product_reviews'] = $reviews_arr;      
             }
             }                                                                 
             return $product_details;                       
        }
        
        public function saswp_extra_theme_review_details($post_id){
            global $sd_data;
           
            $review_data = array();
            $rating_value =0;
            $post_review_title ='';
            $post_review_desc ='';
            
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
                '@type' => 'AggregateRating',
                'ratingValue' => $rating_value,
                'reviewCount' => 1,
            );
            
            $review_data['review'] = array(
                '@type' => 'Review',
                'author' => get_the_author(),
                'datePublished' => get_the_date("Y-m-d\TH:i:s\Z"),
                'name' => $post_review_title,
                'reviewBody' => $post_review_desc,
                'reviewRating' => array(
                    '@type' => 'Rating',
                    'ratingValue' => $rating_value,
                ),
                
            );
            
           }
           return $review_data;
            
        }
        
        public function saswp_dw_question_answers_details($post_id){
                global $sd_data;
                $dw_qa = array();
                $qa_page = array();
               
                $post_type = get_post_type($post_id);
                if($post_type =='dwqa-question' && isset($sd_data['saswp-dw-question-answer']) && $sd_data['saswp-dw-question-answer'] ==1 ){
                 
                $post_meta = get_post_meta($post_id, $key='', true);
                $best_answer_id = $post_meta['_dwqa_best_answer'][0];
                                               
                $userid = get_post_field( 'post_author', $post_id );
                $userinfo = get_userdata($userid);
                               
                $dw_qa['@type'] = 'Question';
                $dw_qa['name'] = get_the_title(); 
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
                $dw_qa['author'] = array('@type' => 'Person','name' =>$userinfo->data->user_nicename);   
                $dw_qa['answerCount'] = $post_meta['_dwqa_answers_count'][0];                  
                
                $args = array(
			'post_type' => 'dwqa-answer',
			'post_parent' => $post_id,
			'post_per_page' => '-1',
			'post_status' => array('publish')
		);
                
                $answer_array = get_posts($args);
               
                $accepted_answer = array();
                $suggested_answer = array();
                foreach($answer_array as $answer){
                    $authorinfo = get_userdata($answer->post_author);                    
                    if($answer->ID == $best_answer_id){
                        $accepted_answer['@type'] = 'Answer';
                        $accepted_answer['upvoteCount'] = get_post_meta( $answer->ID, '_dwqa_votes', true );
                        $accepted_answer['url'] = get_permalink($answer->ID);
                        $accepted_answer['text'] = $answer->post_content;
                        $accepted_answer['dateCreated'] = get_the_date("Y-m-d\TH:i:s\Z", $answer);
                        $accepted_answer['author'] = array('@type' => 'Person', 'name' => $authorinfo->data->user_nicename);
                    }else{
                        $suggested_answer[] =  array(
                            '@type' => 'Answer',
                            'upvoteCount' => get_post_meta( $answer->ID, '_dwqa_votes', true ),
                            'url' => get_permalink($answer->ID),
                            'text' => $answer->post_content,
                            'dateCreated' => get_the_date("Y-m-d\TH:i:s\Z", $answer),
                            'author' => array('@type' => 'Person', 'name' => $authorinfo->data->user_nicename),
                        );
                    }
                }
                $dw_qa['acceptedAnswer'] = $accepted_answer;
                $dw_qa['suggestedAnswer'] = $suggested_answer;
                    
                $qa_page['@context'] = 'http://schema.org';
                $qa_page['@type'] = 'QAPage';
                $qa_page['mainEntity'] = $dw_qa;
                
                }                
                return $qa_page;
        }
        
        public function saswp_get_all_schema_type_fields($schema_type, $id =null){
            $meta_field = array();
            if($schema_type ==''){
             $schema_type = get_post_meta( $id, 'schema_type', true);    
            }    
            switch ($schema_type) {
                
                case 'local_business':
                   
                    $meta_field = array(                        
                        'saswp_business_type' => 'Business Type',
                        'saswp_business_name' => 'Sub Business Type',                           
                        'local_business_name' => 'Business Name',                           
                        'local_business_name_url' => 'URL',
                        'local_business_description' => 'Description',
                        'local_street_address' => 'Street Address',                            
                        'local_city' => 'City',
                        'local_state' => 'State',
                        'local_postal_code' => 'Postal Code',                         
                        'local_phone' => 'Phone',
                        'local_website' => 'Website',
                        'local_business_logo' => 'Image', 
                        'saswp_dayofweek' => 'Operation Days',
                        'local_price_range' => 'Price Range',                                                                                                                                             
                        );                   
                    break;
                
                case 'Blogposting':
                    $meta_field = array(                        
                        'saswp_blogposting_main_entity_of_page' => 'Main Entity Of Page',
                        'saswp_blogposting_headline' => 'Headline',
                        'saswp_blogposting_description' => 'Description', 
                        
                        'saswp_blogposting_name' => 'Name',
                        'saswp_blogposting_url' => 'URL',
                        'saswp_blogposting_date_published' => 'Date Published', 
                        
                        'saswp_blogposting_date_modified' => 'Date Modified',
                        'saswp_blogposting_author_name' => 'Author Name',
                        'saswp_blogposting_organization_name' => 'Organization Name', 
                        'saswp_blogposting_organization_logo' => 'Organization Logo', 
                                                                                                                                            
                        ); 
                   
                    break;
                
                case 'NewsArticle':
                   $meta_field = array(                        
                        'saswp_newsarticle_main_entity_of_page' => 'Main Entity Of Page',
                        'saswp_newsarticle_URL' => 'URL',
                        'saswp_newsarticle_headline' => 'Headline',  
                       
                        'saswp_newsarticle_date_published' => 'Date Published',
                        'saswp_newsarticle_date_modified' => 'Date Modified',
                        'saswp_newsarticle_headline' => 'Headline',  
                       
                        'saswp_newsarticle_description' => 'Description',
                        'saswp_newsarticle_section' => 'Article Section',
                        'saswp_newsarticle_body' => 'Article Body',  
                       
                        'saswp_newsarticle_name' => 'Name',
                        'saswp_newsarticle_thumbnailurl' => 'Thumbnail URL',
                        'saswp_newsarticle_timerequired' => 'Time Required',  
                       
                        'saswp_newsarticle_main_entity_id' => 'Main Entity Id',
                        'saswp_newsarticle_author_name' => 'Author Name',
                        'saswp_newsarticle_author_image' => 'Author Image',
                       
                        'saswp_newsarticle_organization_name' => 'Organization Name',
                        'saswp_newsarticle_organization_logo' => 'Organization Logo',                                                                       
                        ); 
                                        
                    break;
                
                case 'WebPage':
                    $meta_field = array(                        
                        'saswp_webpage_name' => 'Name',
                        'saswp_webpage_url' => 'URL',
                        'saswp_webpage_description' => 'Description',  
                        
                        'saswp_webpage_main_entity_of_page' => 'Main Entity Of Page',
                        'saswp_webpage_image' => 'Image',
                        'saswp_webpage_headline' => 'Headline',  
                        
                        'saswp_webpage_date_published' => 'Date Published',
                        'saswp_webpage_date_modified' => 'Date Modified',
                        'saswp_webpage_author_name' => 'Author Name',  
                        
                        'saswp_webpage_organization_name' => 'Organization Name',
                        'saswp_webpage_organization_logo' => 'Organization Logo',                          
                        ); 
                    
                    break;
                
                case 'Article':      
                    $meta_field = array(                        
                        'saswp_article_main_entity_of_page' => 'Main Entity Of Page',
                        'saswp_article_image' => 'Image',
                        'saswp_article_headline' => 'Headline',  
                        
                        'saswp_article_description' => 'Description',
                        'saswp_article_date_published' => 'Date Published',
                        'saswp_article_date_modified' => 'Date Modified',  
                        
                        'saswp_article_author_name' => 'Author Name',
                        'saswp_article_organization_name' => 'Organization Name',
                        'saswp_article_organization_logo' => 'Organization Logo',  
                        
                        );                                        
                    break;
                
                case 'Recipe':
                    $meta_field = array(                        
                        'saswp_recipe_url' => 'URL',
                        'saswp_recipe_name' => 'Name',
                        'saswp_recipe_date_published' => 'Date Published',  
                        
                        'saswp_recipe_date_modified' => 'Date Modified',
                        'saswp_recipe_description' => 'Description',
                        'saswp_recipe_main_entity' => 'Main Entity Id',
                        
                        'saswp_recipe_author_name' => 'Author Name',
                        'saswp_recipe_author_image' => 'Author Image',
                        'saswp_recipe_organization_name' => 'Organization Name',
                        
                        'saswp_recipe_organization_logo' => 'Organization Logo',
                        'saswp_recipe_preptime' => 'Prepare Time',
                        'saswp_recipe_cooktime' => 'Cook Time',
                        
                        'saswp_recipe_totaltime' => 'Total Time',
                        'saswp_recipe_keywords' => 'Keywords',
                        'saswp_recipe_recipeyield' => 'Recipe Yield',
                        
                        'saswp_recipe_category' => 'Recipe Category',
                        'saswp_recipe_cuisine' => 'Recipe Cuisine',
                        'saswp_recipe_nutrition' => 'Nutrition',
                        
                        'saswp_recipe_ingredient' => 'Recipe Ingredient',
                        'saswp_recipe_instructions' => 'Recipe Instructions',
                        'saswp_recipe_video_name' => 'Video Name',
                        
                        'saswp_recipe_video_description' => 'Video Description',
                        'saswp_recipe_video_thumbnailurl' => 'Video ThumbnailUrl',
                        'saswp_recipe_video_contenturl' => 'Video ContentUrl',
                        
                        'saswp_recipe_video_embedurl' => 'Video EmbedUrl',
                        'saswp_recipe_video_upload_date' => 'Video Upload Date',
                        'saswp_recipe_video_duration' => 'Video Duration',
                    );
                    
                    break;
                
                case 'Product':
                    
                    $meta_field = array(                        
                        'saswp_product_url' => 'URL',
                        'saswp_product_name' => 'Name',
                        'saswp_product_description' => 'Description',                                             
                    );                   
                     if(is_plugin_active('woocommerce/woocommerce.php')){                                                               
                       $meta_field['saswp_product_image']  = 'Image';
                       $meta_field['saswp_product_availability']  = 'Availability';
                       $meta_field['saswp_product_price']  = 'Price';
                       $meta_field['saswp_product_currency']  = 'Price Currency';  
                       
                       $meta_field['saswp_product_brand']  = 'Brand';  
                       $meta_field['saswp_product_priceValidUntil']  = 'Price Valid Until';  
                       $meta_field['saswp_product_isbn']  = 'ISBN';  
                       $meta_field['saswp_product_mpn']  = 'MPN';  
                       $meta_field['saswp_product_gtin8']  = 'GTIN 8';  
                    }                   
                    break;
                
                case 'Service':
                    
                    $meta_field = array(                        
                        'saswp_service_schema_name' => 'Name',
                        'saswp_service_schema_type' => 'Service Type',
                        'saswp_service_schema_provider_name' => 'Provider Name',
                        'saswp_service_schema_provider_type' => 'Provider Type',
                        'saswp_service_schema_image' => 'Image',
                        'saswp_service_schema_locality' => 'Locality',
                        'saswp_service_schema_postal_code' => 'Postal Code',
                        'saswp_service_schema_telephone' => 'Telephone',
                        'saswp_service_schema_price_range' => 'Price Range',
                        'saswp_service_schema_description' => 'Description',
                        'saswp_service_schema_area_served' => 'Area Served (City)',
                        'saswp_service_schema_service_offer' => 'Service Offer',
                        'saswp_review_schema_country' => 'Address Country',
                        'saswp_review_schema_telephone' => 'Telephone',                        
                    );
                   
                    break;
                
                case 'Review':                    
                    $meta_field = array(
                        
                        'saswp_review_schema_item_type' => 'Item Reviewed Type',
                        'saswp_review_schema_name' => 'Name',
                        'saswp_review_schema_description' => 'Description',
                        'saswp_review_schema_date_published' => 'Date Published',
                        'saswp_review_schema_date_modified' => 'Date Modified',
                        'saswp_review_schema_image' => 'Image',
                        'saswp_review_schema_price_range' => 'Price Range',
                        'saswp_review_schema_street_address' => 'Street Address',
                        'saswp_review_schema_locality' => 'Address Locality',
                        'saswp_review_schema_region' => 'Address Region',
                        'saswp_review_schema_postal_code' => 'Postal Code',
                        'saswp_review_schema_country' => 'Address Country',
                        'saswp_review_schema_telephone' => 'Telephone',                        
                    );
                    break;
                
                case 'VideoObject':
                    
                    $meta_field = array(
                        
                        'saswp_video_object_url' => 'URL',
                        'saswp_video_object_headline' => 'Headline',
                        'saswp_video_object_date_published' => 'Date Published',
                        'saswp_video_object_date_modified' => 'Date Modified',
                        'saswp_video_object_description' => 'Description',
                        'saswp_video_object_name' => 'Name',
                        'saswp_video_object_upload_date' => 'Upload Date',
                        'saswp_video_object_thumbnail_url' => 'Thumbnail Url',                        
                        'saswp_video_object_main_entity_id' => 'Main Entity Id',
                        'saswp_video_object_author_name' => 'Author Name',
                        'saswp_video_object_author_image' => 'Author Image',
                        'saswp_video_object_organization_name' => 'Organization Name',
                        'saswp_video_object_organization_logo' => 'Organization Logo',                                         
                    );
                    
                    break;
                
                case 'AudioObject':
                    
                    $meta_field = array(
                        
                        'saswp_audio_schema_name' => 'Name',
                        'saswp_audio_schema_description' => 'Description',
                        'saswp_audio_schema_contenturl' => 'Content Url',
                        'saswp_audio_schema_duration' => 'Duration',
                        'saswp_audio_schema_encoding_format' => 'Encoding Format',
                        'saswp_audio_schema_date_published' => 'Date Published',
                        'saswp_audio_schema_date_modified' => 'Date Modified',
                        'saswp_audio_schema_author_name' => 'Author',                        
                    );
                    
                    break;
                
                case 'qanda':
                    $meta_field = array(
                        
                        'saswp_qa_question_title' => 'Question Title',
                        'saswp_qa_question_description' => 'Question Description',
                        'saswp_qa_upvote_count' => 'Question Upvote Count',                        
                        'saswp_qa_date_created' => 'Question Date Created',
                        'saswp_qa_question_author_name' => 'Author Name',
                        'saswp_qa_accepted_answer_text' => 'Accepted Answer Text',
                        'saswp_qa_accepted_answer_date_created' => 'Accepted Answer Date Created',
                        'saswp_qa_accepted_answer_upvote_count' => 'Accepted Answer Upvote Count',
                        'saswp_qa_accepted_answer_url' => 'Accepted Answer Url',
                        'saswp_qa_accepted_author_name' => 'Accepted Answer Author Name',
                        'saswp_qa_suggested_answer_text' => 'Suggested Answer Text',
                        'saswp_qa_suggested_answer_date_created' => 'Suggested Answer Date Created',                        
                        'saswp_qa_suggested_answer_upvote_count' => 'Suggested Answer Upvote Count',
                        'saswp_qa_suggested_answer_url' => 'Suggested Answer Url',
                        'saswp_qa_suggested_author_name' => 'Suggested Answer Author Name',
                                            
                    );                    
                    break;

                default:
                    break;
            }                      
            return $meta_field;
        }
        
        
        public function saswp_schema_markup_generator($schema_type){
            
                 global $sd_data;
                        $logo      =''; 
                        $height    ='';
                        $width     ='';
                        $site_name ='';
                        
                        $input1 = array();
            
                        $author_id = get_the_author_meta('ID');
                        $image_id 	= get_post_thumbnail_id();
			$image_details 	= wp_get_attachment_image_src($image_id, 'full');                       
			$author_details	= get_avatar_data($author_id);
			$date 		= get_the_date("Y-m-d\TH:i:s\Z");
			$modified_date 	= get_the_modified_date("Y-m-d\TH:i:s\Z");
			$aurthor_name 	= get_the_author();
            
                        
                        if(isset($sd_data['sd_logo'])){
                            
                            $logo = $sd_data['sd_logo']['url']; 
                        }


                        if(isset($sd_data['sd_name']) && $sd_data['sd_name'] !=''){
                          $site_name = $sd_data['sd_name'];  
                        }else{
                          $site_name = get_bloginfo();    
                        }    
                        
                        if('' != $logo && !empty($logo)){
                            
                              $height = $sd_data['sd_logo']['height'];  
                              $width = $sd_data['sd_logo']['width'];
                              
                           }else{            
                               $sizes = array(
                                                           'width'  => 600,
                                                           'height' => 60,
                                                           'crop'   => false,
                                              );   
                               
                               $custom_logo_id = get_theme_mod( 'custom_logo' );           
                               $custom_logo    = wp_get_attachment_image_src( $custom_logo_id, $sizes); 
                               $logo   = $custom_logo[0];
                               $height = $custom_logo[1];
                               $width  = $custom_logo[2];            
                           } 
                        
                        
            
            switch ($schema_type) {
                
                case 'Article':
                    $input1 = array(
					'@context'			=> 'http://schema.org',
					'@type'				=> 'Article',
					'mainEntityOfPage'              => get_permalink(),
					'image'  => array(
                                                                            '@type'		=>'ImageObject',
                                                                            'url'		=> $image_details[0],
                                                                            'height'            => $image_details[1],
                                                                            'width'		=> $image_details[2],                                                                            
                                                                            ),
					'headline'			=> get_the_title(),
					'description'                   => get_the_excerpt(),
					'datePublished'                 => $date,
					'dateModified'                  => $modified_date,
					'author'			=> array(
							'@type' 	=> 'Person',
							'name'		=> $aurthor_name 
                                                         ),
					'Publisher'			=> array(
						'@type'			=> 'Organization',
						'logo' 			=> array(
							'@type'		=> 'ImageObject',
							'url'		=> $logo,
							'width'		=> $width,
							'height'	=> $height,
							),
						'name'			=> $site_name,
					),
                                    
				);

                    break;

                default:
                    break;
            }
            
            return $input1;
            
        }
        
        
}
if (class_exists('saswp_output_service')) {
	$object = new saswp_output_service();
        $object->saswp_service_hooks();
};
