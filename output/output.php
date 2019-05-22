<?php
if (! defined('ABSPATH') ) exit;
/**
 * Function generates knowledge graph schema
 * @global type $sd_data
 * @return type json
 */
function saswp_kb_schema_output() {
    
	global $sd_data;   
        $input     = array();    
        $site_url  = get_site_url();
	// Social profile
	$sd_social_profile = array();

	$sd_facebook = array();
        
	if(isset($sd_data['sd_facebook']) && !empty($sd_data['sd_facebook']) && isset($sd_data['saswp-facebook-enable']) &&  $sd_data['saswp-facebook-enable'] ==1){
		$sd_facebook[] = $sd_data['sd_facebook'];
		$sd_social_profile[] = $sd_facebook;
	}
	$sd_twitter = array();
	if(isset($sd_data['sd_twitter']) && !empty($sd_data['sd_twitter']) && isset($sd_data['saswp-twitter-enable']) &&  $sd_data['saswp-twitter-enable'] ==1 ){
		$sd_twitter[] = $sd_data['sd_twitter'];
		$sd_social_profile[] = $sd_twitter;
	}
	
	$sd_instagram = array();
	if(isset($sd_data['sd_instagram']) && !empty($sd_data['sd_instagram']) && isset($sd_data['saswp-instagram-enable']) &&  $sd_data['saswp-instagram-enable'] ==1 ){
		$sd_instagram[] = $sd_data['sd_instagram'];
		$sd_social_profile[] = $sd_instagram;
        }

	$sd_youtube = array();
	if(isset($sd_data['sd_youtube']) && !empty($sd_data['sd_youtube']) && isset($sd_data['saswp-youtube-enable']) &&  $sd_data['saswp-youtube-enable'] ==1){
		$sd_youtube[] = $sd_data['sd_youtube'];
		$sd_social_profile[] = $sd_youtube;
	}

	$sd_linkedin = array();
	if(isset($sd_data['sd_linkedin']) && !empty($sd_data['sd_linkedin']) && isset($sd_data['saswp-linkedin-enable']) &&  $sd_data['saswp-linkedin-enable'] ==1 ){
		$sd_linkedin[] = $sd_data['sd_linkedin'];
		$sd_social_profile[] = $sd_linkedin;
	}

	$sd_pinterest = array();
	if(isset($sd_data['sd_pinterest']) && !empty($sd_data['sd_pinterest']) && isset($sd_data['saswp-pinterest-enable']) &&  $sd_data['saswp-pinterest-enable'] ==1){
		$sd_pinterest[] = $sd_data['sd_pinterest'];
		$sd_social_profile[] = $sd_pinterest;
	}

	$sd_soundcloud = array();
	if(isset($sd_data['sd_soundcloud']) && !empty($sd_data['sd_soundcloud']) && isset($sd_data['saswp-soundcloud-enable']) &&  $sd_data['saswp-soundcloud-enable'] ==1){
		$sd_soundcloud[] = $sd_data['sd_soundcloud'];
		$sd_social_profile[] = $sd_soundcloud;
	}

	$sd_tumblr = array();
	if(isset($sd_data['sd_tumblr']) && !empty($sd_data['sd_tumblr']) && isset($sd_data['saswp-tumblr-enable']) &&  $sd_data['saswp-tumblr-enable'] ==1){
		$sd_tumblr[] = $sd_data['sd_tumblr'];
		$sd_social_profile[] = $sd_tumblr;
	}

	$platform = array();
        
	foreach ($sd_social_profile as $key => $value) {
		$platform[] = $value; 
	}
	
	// Organization Schema 

	if ( saswp_remove_warnings($sd_data, 'saswp_kb_type', 'saswp_string')  ==  'Organization' ) {
            
                $logo          = '';
                $height        = '';
                $width         = '';
                $contact_info  = array();
                
                $service_object     = new saswp_output_service();
                $default_logo       = $service_object->saswp_get_publisher(true);
                
                if(!empty($default_logo)){
                 
                $logo   = $default_logo['url'];	
                $height = $default_logo['height'];
                $width  = $default_logo['width'];    
                
                }
                                		
                $contact_info = array();
                
                
		$contact_1   = saswp_remove_warnings($sd_data, 'saswp_contact_type', 'saswp_string');
		$telephone_1 = saswp_remove_warnings($sd_data, 'saswp_kb_telephone', 'saswp_string');
                $contact_url = saswp_remove_warnings($sd_data, 'saswp_kb_contact_url', 'saswp_string');
                                		
		
                if($contact_1 && ($telephone_1 || $contact_url)){
                
                    $contact_info = array(
                    
	 		'contactPoint' => array(
                                        '@type'        => 'ContactPoint',
                                        'contactType'  => esc_attr($contact_1),
                                        'telephone'    => esc_attr($telephone_1),
                                        'url'          => esc_attr($contact_url),
			)
                    
                    );
                    
                }
	 	

		$input = array(
                        '@context'		=>'http://schema.org',
                        '@type'			=> 'Organization',
                        '@id'                   => $site_url.'/#Organization',
                        'name'			=> saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string'),
                        'url'			=> saswp_remove_warnings($sd_data, 'sd_url', 'saswp_string'),
                        'sameAs'		=> $platform,                                        		
		);
                
                if($logo !='' && $width !='' && $height !=''){
                    
                    $input['logo']['@type']  = 'ImageObject';
                    $input['logo']['url']    = esc_url($logo);
                    $input['logo']['width']  = esc_attr($width);
                    $input['logo']['height'] = esc_attr($height);
                 
                }
		                    
		$input = array_merge($input, $contact_info);
                        		                
}				
		// Person

	if ( saswp_remove_warnings($sd_data, 'saswp_kb_type', 'saswp_string')  ==  'Person' ) {
            
               $image  = ''; 
               $height = '';
               $width  = '';
               
               if(isset($sd_data['sd-person-image'])){
                   
                   $image  = $sd_data['sd-person-image']['url'];
		   $height = $sd_data['sd-person-image']['height'];
		   $width  = $sd_data['sd-person-image']['width'];
                   
               }
		
		if( '' ==  $image && empty($image) && isset($sd_data['sd_default_image'])){
			$image = $sd_data['sd_default_image']['url'];
		}
		
		if( '' ==  $height && empty($height) && isset($sd_data['sd_default_image_height'])){
			$height = $sd_data['sd_default_image_height'];
		}
		
		if( '' ==  $width && empty($width) && isset($sd_data['sd_default_image_width'])){
			$width = $sd_data['sd_default_image_width'];
		}
	
		$input = array(
			'@context'		=> 'http://schema.org',
			'@type'			=> esc_attr($sd_data['saswp_kb_type']),
			'name'			=> esc_attr($sd_data['sd-person-name']),
			'url'			=> esc_url($sd_data['sd-person-url']),
			'image' 		=> array(
                                                        '@type'	 => 'ImageObject',
                                                        'url'	 => esc_url($image),
                                                        'width'	 => esc_attr($width),
                                                        'height' => esc_attr($height),
                                                    ),
			'telephone'		=> esc_attr($sd_data['sd-person-phone-number']),
			);
	}
        
        
	return apply_filters('saswp_modify_organization_output', $input);	             
}

/**
 * Function generates json markup for the all added schema type in the list
 * @global type $sd_data
 * @return type json
 */
function saswp_schema_output() {     
    
	global $sd_data;

	$Conditionals = saswp_get_all_schema_posts();           
        
	if(!$Conditionals){
		return ;
	}
        
        $all_schema_output = array();
        
        foreach($Conditionals as $schemaConditionals){
        
        $schema_options = array();    
            
        if(isset($schemaConditionals['schema_options'])){
            $schema_options = $schemaConditionals['schema_options'];
        }   
        	        
	$schema_type      = saswp_remove_warnings($schemaConditionals, 'schema_type', 'saswp_string');         
        $schema_post_id   = saswp_remove_warnings($schemaConditionals, 'post_id', 'saswp_string');        
           
        
        $logo           = ''; 
        $height         = '';
        $width          = '';
        $site_name      = '';
        
        $service_object     = new saswp_output_service();
        $default_logo       = $service_object->saswp_get_publisher(true);
        $publisher          = $service_object->saswp_get_publisher();
        
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
	
		// Generate author id
	   		$author_id      = get_the_author_meta('ID');

		
			$image_id 	= get_post_thumbnail_id();
			$image_details 	= wp_get_attachment_image_src($image_id, 'full');			
			$author_details	= get_avatar_data($author_id);
			$date 		= get_the_date("Y-m-d\TH:i:s\Z");
			$modified_date 	= get_the_modified_date("Y-m-d\TH:i:s\Z");
			$aurthor_name 	= get_the_author();
                        
                        if(!$aurthor_name){
				
                        $author_id    = get_post_field ('post_author', $schema_post_id);
		        $aurthor_name = get_the_author_meta( 'display_name' , $author_id ); 
                        
			}
                        
                        
                        $saswp_review_details   = esc_sql ( get_post_meta(get_the_ID(), 'saswp_review_details', true)); 
                        
                        $aggregateRating        = array();                                                
                        $saswp_over_all_rating  = '';
                        
                        if(isset($saswp_review_details['saswp-review-item-over-all'])){
                            
                        $saswp_over_all_rating = $saswp_review_details['saswp-review-item-over-all'];  
                        
                        }
                        
                        $saswp_review_item_enable = 0;
                        
                        if(isset($saswp_review_details['saswp-review-item-enable'])){
                            
                        $saswp_review_item_enable =  $saswp_review_details['saswp-review-item-enable'];  
                         
                        }  
                        
                        $saswp_review_count = "1";
                       
                        
                        if($saswp_over_all_rating && $saswp_review_count && $saswp_review_item_enable ==1 && isset($sd_data['saswp-review-module']) && $sd_data['saswp-review-module'] ==1){
                            
                           $aggregateRating =       array(
                                                            "@type"       => "AggregateRating",
                                                            "ratingValue" => $saswp_over_all_rating,
                                                            "reviewCount" => $saswp_review_count
                                                         ); 
                           
                        }
                                                                        
                        $service_object     = new saswp_output_service();
                        
                        $extra_theme_review = array();                        
                        $extra_theme_review = $service_object->saswp_extra_theme_review_details(get_the_ID());
                        
                       
                        if( $schema_type == 'VideoGame'            || 
                            $schema_type == 'HowTo'                ||
                            $schema_type == 'TVSeries'             ||
                            $schema_type == 'MedicalCondition'     ||
                            $schema_type == 'Apartment'            ||   
                            $schema_type == 'House'                ||
                            $schema_type == 'TouristDestination'   ||
                            $schema_type == 'TouristAttraction'    ||                                
                            $schema_type == 'LandmarksOrHistoricalBuildings' ||
                            $schema_type == 'HinduTemple'          ||
                            $schema_type == 'Church'               ||
                            $schema_type == 'Mosque'               ||                                    
                            $schema_type == 'SingleFamilyResidence' ) {
                               
                                    $input1 = array();
                        }
                        
                        if( 'Course' === $schema_type){
                            
                        $description = strip_tags(strip_shortcodes(get_the_excerpt()));

                        if(!$description){
                            $description = get_bloginfo('description');
                        }
                         
                        $input1 = array(
			'@context'			=> 'http://schema.org',
			'@type'				=> $schema_type ,
                        '@id'				=> get_permalink().'/#course',    
			'name'			        => get_the_title(),
			'description'                   => $description,			
			'url'				=> get_permalink(),
			'datePublished'                 => esc_html($date),
			'dateModified'                  => esc_html($modified_date),
			'provider'			=> array(
                                                            '@type' 	        => 'Organization',
                                                            'name'		=> get_bloginfo(),
                                                            'sameAs'		=> get_home_url() 
                                                        )											
                            );
                                                                 
                                if(isset($schema_options['enable_custom_field']) && $schema_options['enable_custom_field'] ==1){                                   
                                    $service = new saswp_output_service();
                                    $input1 = $service->saswp_replace_with_custom_fields_value($input1, $schema_post_id);
                                }
                                if(!empty($aggregateRating)){
                                    $input1['aggregateRating'] = $aggregateRating;
                                }                                
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }                               
                                if(isset($sd_data['saswp_comments_schema']) && $sd_data['saswp_comments_schema'] ==1){
                                   $input1['comment'] = saswp_get_comments(get_the_ID());
                                }
                            
                            $input1 = apply_filters('saswp_modify_course_schema_output', $input1 );    
                        }
                                                
                        if( 'DiscussionForumPosting' === $schema_type){
                                                     
                            if(isset($sd_data['saswp-bbpress']) && $sd_data['saswp-bbpress'] == 1 && is_plugin_active('bbpress/bbpress.php')){                                                                                                                                                                                            
                                $input1 = array(
                                '@context'			=> 'http://schema.org',
                                '@type'				=> 'DiscussionForumPosting' ,
                                '@id'				=> bbp_get_topic_permalink().'/#discussionforumposting',    			
                                'headline'			=> bbp_get_topic_title(get_the_ID()),
                                'description'                   => wp_strip_all_tags(strip_shortcodes(get_the_excerpt())),
                                "articleSection"                => bbp_get_forum_title(),
                                "articleBody"                   => wp_strip_all_tags(strip_shortcodes(get_the_content())),    
                                'url'				=> bbp_get_topic_permalink(),
                                'datePublished'                 => esc_html($date),
                                'dateModified'                  => esc_html($modified_date),
                                'author'			=> array(
                                                                    '@type' 	        => 'Person',
                                                                    'name'		=> esc_attr($aurthor_name) 
                                                                ),                                    
                                'interactionStatistic'          => array(
                                                                    '@type'                     => 'InteractionCounter',
                                                                    'interactionType'		=> 'http://schema.org/CommentAction',
                                                                    'userInteractionCount'      => bbp_get_topic_reply_count(),
                                        )    
                                );
                                
                            }else{
                                
                                $input1 = array(
                                '@context'			=> 'http://schema.org',
                                '@type'				=> 'DiscussionForumPosting' ,
                                '@id'				=> get_permalink().'/#blogposting',    			
                                'headline'			=> get_the_title(),
                                'description'                   => strip_tags(strip_shortcodes(get_the_excerpt())),			
                                'url'				=> get_permalink(),
                                'datePublished'                 => esc_html($date),
                                'dateModified'                  => esc_html($modified_date),
                                'author'			=> array(
                                                                    '@type' 	        => 'Person',
                                                                    'name'		=> esc_attr($aurthor_name) 
                                                                )											
                                    );
                                
                            }                                                                                                    
                                if(!empty($publisher)){

                                     $input1 = array_merge($input1, $publisher);   

                                 }
                                 
                                if(isset($schema_options['enable_custom_field']) && $schema_options['enable_custom_field'] ==1){                                   
                                    $service = new saswp_output_service();
                                    $input1 = $service->saswp_replace_with_custom_fields_value($input1, $schema_post_id);
                                }
                                if(!empty($aggregateRating)){
                                    $input1['aggregateRating'] = $aggregateRating;
                                }                                
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }                               
                                if(isset($sd_data['saswp_comments_schema']) && $sd_data['saswp_comments_schema'] == 1){
                                   $input1['comment'] = saswp_get_comments(get_the_ID());
                                }
                            
                                $input1 = apply_filters('saswp_modify_d_forum_posting_schema_output', $input1 ); 
                        }
                        
                        if( 'Blogposting' === $schema_type){
                         
                        $input1 = array(
			'@context'			=> 'http://schema.org',
			'@type'				=> 'BlogPosting' ,
                        '@id'				=> get_permalink().'/#blogposting',    
			'mainEntityOfPage'              => get_permalink(),
			'headline'			=> get_the_title(),
			'description'                   => strip_tags(strip_shortcodes(get_the_excerpt())),
			'name'				=> get_the_title(),
			'url'				=> get_permalink(),
			'datePublished'                 => esc_html($date),
			'dateModified'                  => esc_html($modified_date),
			'author'			=> array(
                                                            '@type' 	        => 'Person',
                                                            'name'		=> esc_attr($aurthor_name) 
                                                        )											
                            );
                                if(!empty($publisher)){
                            
                                     $input1 = array_merge($input1, $publisher);   
                         
                                 }
                                 
                                if(isset($schema_options['enable_custom_field']) && $schema_options['enable_custom_field'] ==1){                                   
                                    $service = new saswp_output_service();
                                    $input1 = $service->saswp_replace_with_custom_fields_value($input1, $schema_post_id);
                                }
                                if(!empty($aggregateRating)){
                                    $input1['aggregateRating'] = $aggregateRating;
                                }                                
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }                               
                                if(isset($sd_data['saswp_comments_schema']) && $sd_data['saswp_comments_schema'] == 1){
                                   $input1['comment'] = saswp_get_comments(get_the_ID());
                                }
                            
                                $input1 = apply_filters('saswp_modify_blogposting_schema_output', $input1 ); 
                        }
                        
                        if( 'AudioObject' === $schema_type){
                       
                        $schema_data = saswp_get_schema_data($schema_post_id, 'saswp_audio_schema_details');                                 
                            
                        $input1 = array(
			'@context'			=> 'http://schema.org',
			'@type'				=> $schema_type ,	
                        '@id'				=> get_permalink().'/#audioobject',     
			'name'			        => saswp_remove_warnings($schema_data, 'saswp_audio_schema_name', 'saswp_string'),
			'description'                   => saswp_remove_warnings($schema_data, 'saswp_audio_schema_description', 'saswp_string'),			
			'contentUrl'		        => saswp_remove_warnings($schema_data, 'saswp_audio_schema_contenturl', 'saswp_string'),
                        'duration'                      => saswp_remove_warnings($schema_data, 'saswp_audio_schema_duration', 'saswp_string'),	
                        'encodingFormat'                => saswp_remove_warnings($schema_data, 'saswp_audio_schema_encoding_format', 'saswp_string'),	   
			'datePublished'                 => esc_html($date),
			'dateModified'                  => esc_html($modified_date),
			'author'			=> array(
                                                            '@type'  => 'Person',
                                                            'name'   => esc_attr($aurthor_name)
                                        ),			
                            );
                                if(!empty($publisher)){
                            
                                     $input1 = array_merge($input1, $publisher);   
                         
                                }
                                 
                                if(isset($schema_options['enable_custom_field']) && $schema_options['enable_custom_field'] ==1){                                   
                                    $service = new saswp_output_service();
                                    $input1 = $service->saswp_replace_with_custom_fields_value($input1, $schema_post_id);
                                }
                                if(!empty($aggregateRating)){
                                    $input1['aggregateRating'] = $aggregateRating;
                                }                                
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }                               
                                if(isset($sd_data['saswp_comments_schema']) && $sd_data['saswp_comments_schema'] ==1){
                                            $input1['comment'] = saswp_get_comments(get_the_ID());
                                }   
                                
                                $input1 = apply_filters('saswp_modify_audio_object_schema_output', $input1 );
                        }
                        
                        if( 'Event' === $schema_type){
                       
                            
                        if(!saswp_non_amp() && is_plugin_active('the-events-calendar/the-events-calendar.php') && isset($sd_data['saswp-the-events-calendar']) && $sd_data['saswp-the-events-calendar'] == 1  ){
                            
                            $input1            = Tribe__Events__JSON_LD__Event::instance()->get_data();  
                            
                            if(!empty($input1)){
                                
                                $input1            = array_values( $input1 );
                                $input1            = json_encode($input1);
                                $input1            = json_decode($input1, true); 
                                $input1            = $input1[0];
                            }                                                                                    
                                                       
                        }else{
                           
                        if ( isset($sd_data['saswp-the-events-calendar']) && $sd_data['saswp-the-events-calendar'] == 0 ) {
                                
                        $schema_data = saswp_get_schema_data($schema_post_id, 'saswp_event_schema_details');                                 
                            
                        $input1 = array(
			'@context'			=> 'http://schema.org',
			'@type'				=> $schema_type ,
                        '@id'				=> get_permalink().'/#event',      
			'name'			        => saswp_remove_warnings($schema_data, 'saswp_event_schema_name', 'saswp_string'),
			'description'                   => saswp_remove_warnings($schema_data, 'saswp_event_schema_description', 'saswp_string'),						                            
                        'startDate'		        => isset($schema_data['saswp_event_schema_start_date']) && $schema_data['saswp_event_schema_start_date'] !='' ? date('Y-m-d\TH:i:s\Z',strtotime($schema_data['saswp_event_schema_start_date'])):'',
                        'endDate'                       => isset($schema_data['saswp_event_schema_end_date'])   && $schema_data['saswp_event_schema_end_date']   !='' ? date('Y-m-d\TH:i:s\Z',strtotime($schema_data['saswp_event_schema_end_date'])):'',                                                        
                        'image'                         => array(
                                                                    '@type'		=>'ImageObject',
                                                                    'url'		=>  isset($schema_data['saswp_event_schema_image']) ? esc_url($schema_data['saswp_event_schema_image']['url']):'' ,
                                                                    'width'		=>  isset($schema_data['saswp_event_schema_image']) ? esc_attr($schema_data['saswp_event_schema_image']['width']):'' ,
                                                                    'height'            =>  isset($schema_data['saswp_event_schema_image']) ? esc_attr($schema_data['saswp_event_schema_image']['height']):'' ,
                                                                ),                                
			'location'			=> array(
                                                            '@type'   => 'Place',
                                                            'name'    => saswp_remove_warnings($schema_data, 'saswp_event_schema_location_name', 'saswp_string'),
                                                            'address' => array(
                                                                 '@type'           => 'PostalAddress',
                                                                 'streetAddress'   => saswp_remove_warnings($schema_data, 'saswp_event_schema_location_streetaddress', 'saswp_string'),
                                                                 'addressLocality' => saswp_remove_warnings($schema_data, 'saswp_event_schema_location_locality', 'saswp_string'),
                                                                 'postalCode'      => saswp_remove_warnings($schema_data, 'saswp_event_schema_location_postalcode', 'saswp_string'),
                                                                 'addressRegion'   => saswp_remove_warnings($schema_data, 'saswp_event_schema_location_region', 'saswp_string'),                                                     
                                                            )    
                                        ),
                        'offers'			=> array(
                                                            '@type'           => 'Offer',
                                                            'url'             => saswp_remove_warnings($schema_data, 'saswp_event_schema_url', 'saswp_string'),	                        
                                                            'price'           => saswp_remove_warnings($schema_data, 'saswp_event_schema_price', 'saswp_string'),
                                                            'priceCurrency'   => saswp_remove_warnings($schema_data, 'saswp_event_schema_price_currency', 'saswp_string'),
                                                            'availability'    => saswp_remove_warnings($schema_data, 'saswp_event_schema_availability', 'saswp_string'),                                                           
                                                            'validFrom'       => isset($schema_data['saswp_event_schema_validfrom'])   && $schema_data['saswp_event_schema_validfrom']   !='' ? date('Y-m-d\TH:i:s\Z',strtotime($schema_data['saswp_event_schema_validfrom'])):'',                                                        
                                        ),
                        'performer'			=> array(
                                                            '@type'  => 'PerformingGroup',
                                                            'name'   => saswp_remove_warnings($schema_data, 'saswp_event_schema_performer_name', 'saswp_string'),	                        
                                        ),    
                            );
                               
                                if(isset($schema_options['enable_custom_field']) && $schema_options['enable_custom_field'] ==1){                                   
                                    $service = new saswp_output_service();
                                    $input1 = $service->saswp_replace_with_custom_fields_value($input1, $schema_post_id);
                                }
                                if(!empty($aggregateRating)){
                                    $input1['aggregateRating'] = $aggregateRating;
                                }                                
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }                               
                                if(isset($sd_data['saswp_comments_schema']) && $sd_data['saswp_comments_schema'] ==1){
                                   $input1['comment'] = saswp_get_comments(get_the_ID());
                                }                            
                                
                            } 
                            
                        }    
                            
                        $input1 = apply_filters('saswp_modify_event_schema_output', $input1 );
                        
                        }
                        
                        if( 'SoftwareApplication' === $schema_type){
                       
                        $schema_data = saswp_get_schema_data($schema_post_id, 'saswp_software_schema_details');                                 
                            
                        $input1 = array(
			'@context'			=> 'http://schema.org',
			'@type'				=> $schema_type ,
                        '@id'				=> get_permalink().'/#softwareapplication',         
			'name'			        => saswp_remove_warnings($schema_data, 'saswp_software_schema_name', 'saswp_string'),
			'description'                   => saswp_remove_warnings($schema_data, 'saswp_software_schema_description', 'saswp_string'),			
			'operatingSystem'		=> saswp_remove_warnings($schema_data, 'saswp_software_schema_operating_system', 'saswp_string'),
                        'applicationCategory'           => saswp_remove_warnings($schema_data, 'saswp_software_schema_application_category', 'saswp_string'),	                        
                        'offers'                        => array(
                                                            '@type'         => 'Offer',
                                                            'price'         => saswp_remove_warnings($schema_data, 'saswp_software_schema_price', 'saswp_string'),	                         
                                                            'priceCurrency' => saswp_remove_warnings($schema_data, 'saswp_software_schema_price_currency', 'saswp_string'),	                         
                                                         ),        
			'datePublished'                 => esc_html($date),
			'dateModified'                  => esc_html($modified_date),
			'author'			=> array(
                                                            '@type'  => 'Person',
                                                            'name'   => esc_attr($aurthor_name)
                                        ),			
                        );
                        
                                if(isset($schema_data['saswp_software_schema_rating']) && $schema_data['saswp_software_schema_rating'] >0 && isset($schema_data['saswp_software_schema_rating_count']) && $schema_data['saswp_software_schema_rating_count'] >0 && $schema_data['saswp_software_schema_enable_rating'] == 1){
                                       $input1['aggregateRating'] =  array(
                                                                        '@type'         => 'AggregateRating',
                                                                        'ratingValue'	=> esc_attr($schema_data['saswp_software_schema_rating']),
                                                                        'ratingCount'   => (int)esc_attr($schema_data['saswp_software_schema_rating_count']),       
                                       );
                                  }
                                  
                                if(!empty($publisher)){
                            
                                     $input1 = array_merge($input1, $publisher);   
                         
                                 }
                                if(isset($schema_options['enable_custom_field']) && $schema_options['enable_custom_field'] ==1){                                   
                                    $service = new saswp_output_service();
                                    $input1 = $service->saswp_replace_with_custom_fields_value($input1, $schema_post_id);
                                }
                                if(!empty($aggregateRating)){
                                    $input1['aggregateRating'] = $aggregateRating;
                                }                                
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }                               
                                if(isset($sd_data['saswp_comments_schema']) && $sd_data['saswp_comments_schema'] ==1){
                                            $input1['comment'] = saswp_get_comments(get_the_ID());
                                }    
                                
                                $input1 = apply_filters('saswp_modify_software_application_schema_output', $input1 );
                        }
			
			if( 'WebPage' === $schema_type){                            				
                                
                                $service = new saswp_output_service();
                                $input1 = $service->saswp_schema_markup_generator($schema_type);
				
                                if(isset($schema_options['enable_custom_field']) && $schema_options['enable_custom_field'] ==1){
                                    $service = new saswp_output_service();
                                    $input1 = $service->saswp_replace_with_custom_fields_value($input1, $schema_post_id);
                                }
                                if(isset($sd_data['saswp_comments_schema']) && $sd_data['saswp_comments_schema'] ==1){
                                    $input1['comment'] = saswp_get_comments(get_the_ID());
                                }                                
                                if(!empty($aggregateRating)){
                                    $input1['mainEntity']['aggregateRating'] = $aggregateRating;
                                }                                
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }
                                
                             $input1 = apply_filters('saswp_modify_webpage_schema_output', $input1 );   
			}	
		
			if( 'Article' === $schema_type ){
                            
                                $service = new saswp_output_service();
                                $input1 = $service->saswp_schema_markup_generator($schema_type);
				
                                if(isset($schema_options['enable_custom_field']) && $schema_options['enable_custom_field'] ==1){
                                    $service = new saswp_output_service();
                                    $input1 = $service->saswp_replace_with_custom_fields_value($input1, $schema_post_id);
                                }
                                if(isset($sd_data['saswp_comments_schema']) && $sd_data['saswp_comments_schema'] ==1){
                                    $input1['comment'] = saswp_get_comments(get_the_ID());
                                }
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }
                                
                                $input1 = apply_filters('saswp_modify_article_schema_output', $input1 );  
			}
                        
                        if( 'TechArticle' === $schema_type ){
                                
                                $service = new saswp_output_service();
                                $input1 = $service->saswp_schema_markup_generator($schema_type);
				
                                if(isset($schema_options['enable_custom_field']) && $schema_options['enable_custom_field'] == 1){
                                    $service = new saswp_output_service();
                                    $input1 = $service->saswp_replace_with_custom_fields_value($input1, $schema_post_id);
                                }
                                if(isset($sd_data['saswp_comments_schema']) && $sd_data['saswp_comments_schema'] ==1){
                                    $input1['comment'] = saswp_get_comments(get_the_ID());
                                }
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }
                                
                                $input1 = apply_filters('saswp_modify_tech_article_schema_output', $input1 );
			}
		      
			if( 'Recipe' === $schema_type){
                            
				if(empty($image_details[0]) || $image_details[0] === NULL ){
					$image_details[0] = $sd_data['sd_logo']['url'];
				}
                                
				$input1 = array(
				'@context'			=> 'http://schema.org',
				'@type'				=> $schema_type ,
                                '@id'				=> get_permalink().'/#recipe',    
				'url'				=> get_permalink(),
				'name'			        => get_the_title(),
				'datePublished'                 => esc_html($date),
				'dateModified'                  => esc_html($modified_date),
				'description'                   => strip_tags(strip_shortcodes(get_the_excerpt())),
				'mainEntity'                    => array(
						'@type'				=> 'WebPage',
						'@id'				=> get_permalink(),
						'author'			=> array(
								'@type' 	=> 'Person',
								'name'		=> esc_attr($aurthor_name),
								'image'		=> array(
									'@type'			=> 'ImageObject',
									'url'			=> saswp_remove_warnings($author_details, 'url', 'saswp_string'),
									'height'		=> saswp_remove_warnings($author_details, 'height', 'saswp_string'),
									'width'			=> saswp_remove_warnings($author_details, 'width', 'saswp_string')
								),
							),						
                                                
                                    
					),                                        					
				
				);
                                
                                if(!empty($publisher)){
                            
                                     $input1['mainEntity'] = array_merge($input1['mainEntity'], $publisher);   
                         
                                 }
                                if(isset($schema_options['enable_custom_field']) && $schema_options['enable_custom_field'] ==1){
                                    $service = new saswp_output_service();
                                    $input1 = $service->saswp_replace_with_custom_fields_value($input1, $schema_post_id);
                                }
                                if(isset($sd_data['saswp_comments_schema']) && $sd_data['saswp_comments_schema'] ==1){
                                    $input1['comment'] = saswp_get_comments(get_the_ID());
                                }                                 
                                if(!empty($aggregateRating)){
                                    $input1['aggregateRating'] = $aggregateRating;
                                }                                
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }
                                
                                $input1 = apply_filters('saswp_modify_recipe_schema_output', $input1 );
			}
                       
                        if( 'qanda' === $schema_type){
                            
                            $service_object = new saswp_output_service();
                            $input1  = $service_object->saswp_dw_question_answers_details(get_the_ID()); 
                            
                            if(isset($schema_options['enable_custom_field']) && $schema_options['enable_custom_field'] ==1){
                                    $service = new saswp_output_service();
                                    $input1 = $service->saswp_replace_with_custom_fields_value($input1, $schema_post_id);
                            }
                            
                            $input1 = apply_filters('saswp_modify_qanda_schema_output', $input1 );
			}
                                                                      
			if( 'Product' === $schema_type){
                            		                                                                
                                $service = new saswp_output_service();
                                $product_details = $service->saswp_woocommerce_product_details(get_the_ID());  
                               
                                if((isset($sd_data['saswp-woocommerce']) && $sd_data['saswp-woocommerce'] == 1) && !empty($product_details)){
                                    
                                    $input1 = array(
                                    '@context'			=> 'http://schema.org',
                                    '@type'				=> 'Product',
                                    '@id'				=> get_permalink().'/#product',     
                                    'url'				=> get_permalink(),
                                    'name'                              => saswp_remove_warnings($product_details, 'product_name', 'saswp_string'),
                                    'sku'                               => saswp_remove_warnings($product_details, 'product_sku', 'saswp_string'),    
                                    'description'                       => saswp_remove_warnings($product_details, 'product_description', 'saswp_string'),                                    
                                    'offers'                            => array(
                                                                                '@type'	=> 'Offer',
                                                                                'availability'      => saswp_remove_warnings($product_details, 'product_availability', 'saswp_string'),
                                                                                'price'             => saswp_remove_warnings($product_details, 'product_price', 'saswp_string'),
                                                                                'priceCurrency'     => saswp_remove_warnings($product_details, 'product_currency', 'saswp_string'),
                                                                                'url'               => get_permalink(),
                                                                                'priceValidUntil'   => saswp_remove_warnings($product_details, 'product_priceValidUntil', 'saswp_string'),
                                                                             ),
                                        
				  );
                                    
                                  if(isset($product_details['product_image'])){
                                    $input1 = array_merge($input1, $product_details['product_image']);
                                  }  
                                    
                                  if(isset($product_details['product_gtin8']) && $product_details['product_gtin8'] !=''){
                                    $input1['gtin8'] = esc_attr($product_details['product_gtin8']);  
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
                                                                        'author'	=> esc_attr($review['author']),
                                                                        'datePublished'	=> esc_html($review['datePublished']),
                                                                        'description'	=> $review['description'],  
                                                                        'reviewRating'  => array(
                                                                                '@type'	=> 'Rating',
                                                                                'bestRating'	=> '5',
                                                                                'ratingValue'	=> esc_attr($review['reviewRating']),
                                                                                'worstRating'	=> '1',
                                                                        )  
                                          );
                                          
                                      }
                                      $input1['review'] =  $reviews;
                                  }
                                  
                                if(isset($schema_options['enable_custom_field']) && $schema_options['enable_custom_field'] ==1){
                                    
                                    $service = new saswp_output_service();
                                    $input1 = $service->saswp_replace_with_custom_fields_value($input1, $schema_post_id);
                                    
                                }                                  
                                }else{
                                    
                                $input1 = array();
                                    
                                }                                                                
                                
                                $input1 = apply_filters('saswp_modify_product_schema_output', $input1 );
			}
                        
                        if( 'NewsArticle' === $schema_type ){                              
                            
                            $category_detail = get_the_category(get_the_ID());//$post->ID
                            $article_section = '';
                            
                            foreach($category_detail as $cd){
                                
                                $article_section =  $cd->cat_name;
                            
                            }
                                $word_count = saswp_reading_time_and_word_count();
				$input1 = array(
					'@context'			=> 'http://schema.org',
					'@type'				=> $schema_type ,
                                        '@id'				=> get_permalink().'/#newsarticle',
					'url'				=> get_permalink(),
					'headline'			=> get_the_title(),
                                        'mainEntityOfPage'	        => get_the_permalink(),            
					'datePublished'                 => esc_html($date),
					'dateModified'                  => esc_html($modified_date),
					'description'                   => strip_tags(strip_shortcodes(get_the_excerpt())),
                                        'articleSection'                => $article_section,            
                                        'articleBody'                   => strip_tags(strip_shortcodes(get_the_excerpt())),            
					'name'				=> get_the_title(), 					
					'thumbnailUrl'                  => saswp_remove_warnings($image_details, 0, 'saswp_string'),
                                        'wordCount'                     => saswp_remove_warnings($word_count, 'word_count', 'saswp_string'),
                                        'timeRequired'                  => saswp_remove_warnings($word_count, 'timerequired', 'saswp_string'),            
					'mainEntity'                    => array(
                                                                            '@type' => 'WebPage',
                                                                            '@id'   => get_permalink(),
						), 
					'author'			=> array(
							'@type' 			=> 'Person',
							'name'				=> esc_attr($aurthor_name),
							'Image'				=> array(
							'@type'				=> 'ImageObject',
							'url'				=> saswp_remove_warnings($author_details, 'url', 'saswp_string'),
							'height'			=> saswp_remove_warnings($author_details, 'height', 'saswp_string'),
							'width'				=> saswp_remove_warnings($author_details, 'width', 'saswp_string')
										)
							)					                                                    
					);
                                if(!empty($publisher)){
                            
                                     $input1 = array_merge($input1, $publisher);   
                         
                                 }
                                if(isset($schema_options['enable_custom_field']) && $schema_options['enable_custom_field'] ==1){
                                    $service = new saswp_output_service();
                                    $input1 = $service->saswp_replace_with_custom_fields_value($input1, $schema_post_id);
                                }
                                if(isset($sd_data['saswp_comments_schema']) && $sd_data['saswp_comments_schema'] ==1){
                                    $input1['comment'] = saswp_get_comments(get_the_ID());
                                }                
                                                                
                                if(!empty($aggregateRating)){
                                    $input1['aggregateRating'] = $aggregateRating;
                                }                                
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }
                                
                                $input1 = apply_filters('saswp_modify_news_article_schema_output', $input1 );
				}
                                                
                        if( 'Service' === $schema_type ){  
                                 
                                $schema_data       = saswp_get_schema_data($schema_post_id, 'saswp_service_schema_details');                                
                                
                                $area_served_str   = saswp_remove_warnings($schema_data, 'saswp_service_schema_area_served', 'saswp_string');
                                $area_served_arr   = explode(',', $area_served_str);
                                                                
                                $service_offer_str = saswp_remove_warnings($schema_data, 'saswp_service_schema_service_offer', 'saswp_string');
                                $service_offer_arr = explode(',', $service_offer_str);
                                
				$input1 = array(
					'@context'			=> 'http://schema.org',
					'@type'				=> $schema_type ,
                                        '@id'				=> get_permalink().'/#service',
                                        'name'				=> saswp_remove_warnings($schema_data, 'saswp_service_schema_name', 'saswp_string'), 
					'serviceType'                   => saswp_remove_warnings($schema_data, 'saswp_service_schema_type', 'saswp_string'),
					'provider'                      => array(
                                                                        '@type' => saswp_remove_warnings($schema_data, 'saswp_service_schema_provider_type', 'saswp_string'),
                                                                        'name'  => saswp_remove_warnings($schema_data, 'saswp_service_schema_provider_name', 'saswp_string'),                                                                        
                                                                        'image'  => array(
                                                                            '@type'		=> 'ImageObject',
                                                                            'url'		=> isset($schema_data['saswp_service_schema_image']) ? esc_url($schema_data['saswp_service_schema_image']['url']):'' ,
                                                                            'width'		=> isset($schema_data['saswp_service_schema_image']) ? esc_attr($schema_data['saswp_service_schema_image']['width']):'' ,
                                                                            'height'            => isset($schema_data['saswp_service_schema_image']) ? esc_attr($schema_data['saswp_service_schema_image']['height']):'' ,
                                                                            ),
                                                                        '@id'   => get_permalink(),
                                                                        'address' => array(
                                                                            '@type'           => 'PostalAddress',
                                                                            'addressLocality' => saswp_remove_warnings($schema_data, 'saswp_service_schema_locality', 'saswp_string'),
                                                                            'postalCode'      => saswp_remove_warnings($schema_data, 'saswp_service_schema_postal_code', 'saswp_string'),  
                                                                            'telephone'       => saswp_remove_warnings($schema_data, 'saswp_service_schema_telephone', 'saswp_string')
                                                                        ),
                                                                        'priceRange'         => saswp_remove_warnings($schema_data, 'saswp_service_schema_price_range', 'saswp_string'),                                                                        
                                                                        ),                                        										                                                                     
					'description'                   => saswp_remove_warnings($schema_data, 'saswp_service_schema_description', 'saswp_string'),
                                        ); 
                                        $areaServed = array();
                                        
                                        foreach($area_served_arr as $area){
                                            
                                            $areaServed[] = array(
                                                '@type' => 'City',
                                                'name'  => $area
                                            );
                                            
                                        }
                                        $serviceOffer = array();
                                        
                                        foreach($service_offer_arr as $offer){
                                            
                                            $serviceOffer[] = array(
                                                '@type' => 'Offer',
                                                'name'  => $offer
                                            );
                                            
                                        }
                                        
                                       $input1['areaServed'] = $areaServed;
                                       
                                       $input1['hasOfferCatalog'] = array(
                                           '@type'            => 'OfferCatalog',
                                            'name'            => $schema_data['saswp_service_schema_name'],
                                            'itemListElement' => $serviceOffer
                                       );
                                
                                if(isset($schema_options['enable_custom_field']) && $schema_options['enable_custom_field'] == 1){
                                    
                                    $service = new saswp_output_service();
                                    $input1 = $service->saswp_replace_with_custom_fields_value($input1, $schema_post_id);
                                    
                                } 
                                
                                if(isset($schema_data['saswp_service_schema_enable_rating'])){
                                 
                                  $input1['aggregateRating'] = array(
                                                            "@type"       => "AggregateRating",
                                                            "ratingValue" => saswp_remove_warnings($schema_data, 'saswp_service_schema_rating', 'saswp_string'),
                                                            "reviewCount" => saswp_remove_warnings($schema_data, 'saswp_service_schema_review_count', 'saswp_string')
                                                         );                                       
                                }
                                
                                if(!empty($aggregateRating)){
                                    $input1['aggregateRating'] = $aggregateRating;
                                }                                
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }
                                
                                $input1 = apply_filters('saswp_modify_service_schema_output', $input1 );
				}  
                        
                        if( 'Review' === $schema_type ){  
                                 
                        
                         if(isset($sd_data['saswp-tagyeem']) && $sd_data['saswp-tagyeem'] == 1 && (is_plugin_active('taqyeem/taqyeem.php') || get_template() != 'jannah') ){                                                                                                      
                           
                             remove_action( 'TieLabs/after_post_entry',  'tie_article_schemas' );
                             
                            $input1 = array(
                                    '@context'       => 'http://schema.org',
                                    '@type'          => 'Review',
                                    '@id'	     => get_permalink().'/#review',
                                    'dateCreated'    => esc_html($date),
                                    'datePublished'  => esc_html($date),
                                    'dateModified'   => esc_html($modified_date),
                                    'headline'       => get_the_title(),
                                    'name'           => get_the_title(),
                                    'keywords'       => tie_get_plain_terms( get_the_ID(), 'post_tag' ),
                                    'url'            => get_permalink(),
                                    'description'    => strip_tags(strip_shortcodes(get_the_excerpt())),
                                    'copyrightYear'  => get_the_time( 'Y' ),                                                                                                           
                                    'author'	     => array(
                                                            '@type' 	=> 'Person',
                                                            'name'		=> esc_attr($aurthor_name),
                                                            'image'		=> array(
                                                                    '@type'			=> 'ImageObject',
                                                                    'url'			=> saswp_remove_warnings($author_details, 'url', 'saswp_string'),
                                                                    'height'                    => saswp_remove_warnings($author_details, 'height', 'saswp_string'),
                                                                    'width'			=> saswp_remove_warnings($author_details, 'width', 'saswp_string')
                                                            ),
							),                                                        
                                
                                    );
                                    
                                    $total_score = (int) get_post_meta( get_the_ID(), 'taq_review_score', true );
                                    
                                    if( ! empty( $total_score ) && $total_score > 0 ){
                                        
                                        $total_score = round( ($total_score*5)/100, 1 );
                                    
                                    }
                                    
                                    $input1['itemReviewed'] = array(
                                            '@type' => 'Thing',
                                            'name'  => get_the_title(),
                                    );

                                    $input1['reviewRating'] = array(
                                        '@type'       => 'Rating',
                                        'worstRating' => 1,
                                        'bestRating'  => 5,
                                        'ratingValue' => esc_attr($total_score),
                                        'description' => get_post_meta( get_the_ID(), 'taq_review_summary', true ),
                                     );    
                                                                                   
                         } else {
                             
                             $schema_data = saswp_get_schema_data($schema_post_id, 'saswp_review_schema_details');  
                                                        
                            if(isset($schema_data['saswp_review_schema_item_type'])){
                            
                                
                                $review_author = $aurthor_name;
                                
                                if(isset($schema_data['saswp_review_schema_author'])){
                                    
                                 $review_author = $schema_data['saswp_review_schema_author'];   
                                 
                                }
                                                                
                                $input1['@context']                     = 'http://schema.org';
                                $input1['@type']                        = esc_attr($schema_type);
                                $input1['url']                          = get_permalink();                                
                                $input1['datePublished']                = esc_html($date);
                                $input1['dateModified']                 = esc_html($modified_date);
                                
                                if($review_author){
                                    
                                $input1['author']['@type']              = 'Person';      
                                $input1['author']['name']               = esc_attr($review_author);
                                
                                if(isset($schema_data['saswp_review_schema_author_sameas'])){
                                    
                                 $input1['author']['sameAs']               = esc_url($schema_data['saswp_review_schema_author_sameas']);   
                                 
                                }                                
                                
                                }
                                
                                 if(!empty($publisher)){
                            
                                     $input1 = array_merge($input1, $publisher);   
                         
                                 }
                                 
                                if(isset($schema_data['saswp_review_schema_description'])){
                                    
                                    $input1['reviewBody']               = $schema_data['saswp_review_schema_description'];
                                    $input1['description']              = $schema_data['saswp_review_schema_description'];
                                }else {
                                    $input1['reviewBody']               = strip_tags(strip_shortcodes(get_the_excerpt()));
                                    $input1['description']              = strip_tags(strip_shortcodes(get_the_excerpt()));
                                }
                                
                                if(isset($schema_data['saswp_review_schema_item_type'])){
                                    $input1['itemReviewed']['@type'] = esc_attr($schema_data['saswp_review_schema_item_type']);   
                                }
                                if(isset($schema_data['saswp_review_schema_name'])){
                                    $input1['itemReviewed']['name'] = esc_attr($schema_data['saswp_review_schema_name']);
                                }
                                if(isset($schema_data['saswp_review_schema_url'])){
                                    $input1['itemReviewed']['url'] = esc_url($schema_data['saswp_review_schema_url']);
                                }                                                                                               
                                if(isset($schema_data['saswp_review_schema_price_range'])){                                    
                                    $input1['itemReviewed']['priceRange']     = esc_attr($schema_data['saswp_review_schema_price_range']);                                    
                                }                                
                                if(isset($schema_data['saswp_review_schema_telephone'])){                                    
                                    $input1['itemReviewed']['telephone']     = esc_attr($schema_data['saswp_review_schema_telephone']);                                    
                                }                                
                                if(isset($schema_data['saswp_review_schema_servescuisine'])){                                    
                                    $input1['itemReviewed']['servesCuisine']     = esc_attr($schema_data['saswp_review_schema_servescuisine']);                                    
                                }
                                if(isset($schema_data['saswp_review_schema_menu'])){                                    
                                    $input1['itemReviewed']['hasMenu']     = esc_url($schema_data['saswp_review_schema_menu']);                                    
                                }                                
                                if(isset($schema_data['saswp_review_schema_itemreviewed_sameas'])){                                    
                                    $input1['itemReviewed']['sameAs']   = esc_url($schema_data['saswp_review_schema_itemreviewed_sameas']);                                    
                                }
                                
                                
                                if(isset($schema_data['saswp_review_schema_director'])){
                                    
                                 $input1['itemReviewed']['director']   = esc_attr($schema_data['saswp_review_schema_director']);   
                                 
                                }
                                if(isset($schema_data['saswp_review_schema_date_created'])){
                                    
                                 $input1['itemReviewed']['dateCreated']   = date_format(date_create($schema_data['saswp_review_schema_date_created']), "Y-m-d\TH:i:s\Z");   
                                 
                                }
                                
                                
                                if(isset($schema_data['saswp_review_schema_image'])){
                                    
                                $input1['itemReviewed']['image']['@type'] = 'ImageObject';
                                $input1['itemReviewed']['image']['url']   = isset($schema_data['saswp_review_schema_image']) ? esc_url($schema_data['saswp_review_schema_image']) : '';
                                $input1['itemReviewed']['image']['width'] = isset($schema_data['saswp_review_schema_image_detail']) ? esc_attr($schema_data['saswp_review_schema_image_detail']['width']) : '';
                                $input1['itemReviewed']['image']['height']= isset($schema_data['saswp_review_schema_image_detail']) ? esc_attr($schema_data['saswp_review_schema_image_detail']['height']) : '';
                                    
                                }
                                                                                                                                                                                                
                            if(saswp_remove_warnings($schema_data, 'saswp_review_schema_street_address', 'saswp_string') !='' || saswp_remove_warnings($schema_data, 'saswp_review_schema_locality', 'saswp_string') !=''){
                                    
                                $input1['itemReviewed']['address']['@type']           = 'PostalAddress';
                                $input1['itemReviewed']['address']['streetAddress']   = saswp_remove_warnings($schema_data, 'saswp_review_schema_street_address', 'saswp_string');
                                $input1['itemReviewed']['address']['addressLocality'] = saswp_remove_warnings($schema_data, 'saswp_review_schema_locality', 'saswp_string');
                                $input1['itemReviewed']['address']['addressRegion']   = saswp_remove_warnings($schema_data, 'saswp_review_schema_region', 'saswp_string');
                                $input1['itemReviewed']['address']['postalCode']      = saswp_remove_warnings($schema_data, 'saswp_review_schema_postal_code', 'saswp_string');
                                $input1['itemReviewed']['address']['addressCountry']  = saswp_remove_warnings($schema_data, 'saswp_review_schema_country', 'saswp_string');
                                                                    
                                }                                
                             
                            $service = new saswp_output_service();
                            
                                
                            switch ($schema_data['saswp_review_schema_item_type']) {
                                
                                case 'Article':
                                    
                                    $markup = $service->saswp_schema_markup_generator($schema_data['saswp_review_schema_item_type']);                                    
                                    $input1['itemReviewed'] = $markup;
                                    
                                    break;
                                case 'Adultentertainment':
                                    $input1 = $input1;
                                    break;
                                case 'Blog':
                                    $input1 = $input1;
                                    break;
                                case 'Book':
                                    
                                    if(isset($schema_data['saswp_review_schema_isbn'])){
                                        
                                        $input1['itemReviewed']['isbn'] = $schema_data['saswp_review_schema_isbn'];
                                                
                                    }
                                    if($review_author)   {
                                        
                                    $input1['itemReviewed']['author']['@type']              = 'Person';      
                                    $input1['itemReviewed']['author']['name']               = esc_attr($review_author);
                                    $input1['itemReviewed']['author']['sameAs']             = esc_url($schema_data['saswp_review_schema_author_sameas']);   
                                    
                                    }  
                                                                        
                                    break;
                                case 'Casino':
                                    break;
                                case 'Diet':
                                    break;
                                case 'Episode':
                                    break;
                                case 'ExercisePlan':
                                    break;
                                case 'Game':
                                    break;
                                case 'Movie':                                                                       
                                    
                                    if($review_author){
                                    
                                        $input1['author']['sameAs']   = get_permalink();
                                        
                                    }
                                    
                                    
                                    break;
                                case 'MusicPlaylist':
                                    break;
                                case 'MusicRecording':
                                    break;
                                case 'Photograph':
                                    break;
                                case 'Recipe':
                                    break;
                                case 'Restaurant':
                                    break;
                                case 'Series':
                                    break;
                                case 'SoftwareApplication':
                                    break;
                                case 'VisualArtwork':
                                    break;
                                case 'WebPage': 
                                    
                                    $markup = $service->saswp_schema_markup_generator($schema_data['saswp_review_schema_item_type']);                                                                       
                                    $input1['itemReviewed'] = $markup;
                                    
                                    break;
                                case 'WebSite':
                                    break;


                                default:
                                    $input1 = $input1;
                                 break;
                            }
                                
                               if(isset($schema_data['saswp_review_schema_enable_rating']) && saswp_remove_warnings($schema_data, 'saswp_review_schema_rating', 'saswp_string') !='' && saswp_remove_warnings($schema_data, 'saswp_review_schema_review_count', 'saswp_string') !=''){
                                 
                                  $input1['reviewRating'] = array(
                                                            "@type"        => "Rating",
                                                            "ratingValue"  => saswp_remove_warnings($schema_data, 'saswp_review_schema_rating', 'saswp_string'),
                                                            "bestRating"   => saswp_remove_warnings($schema_data, 'saswp_review_schema_review_count', 'saswp_string'),                                                            
                                                         );                                       
                                }
                                
                                if(isset($schema_options['enable_custom_field']) && $schema_options['enable_custom_field'] ==1){
                                    
                                    $service = new saswp_output_service();
                                    $input1 = $service->saswp_replace_with_custom_fields_value($input1, $schema_post_id);
                                    
                                }
                            
                                
                            }
                             
                         }  
                              
                         $input1 = apply_filters('saswp_modify_review_schema_output', $input1 );
		        }          
                                			
			if( 'VideoObject' === $schema_type){
                            
                                            if(empty($image_details[0]) || $image_details[0] === NULL ){

                                                    if(isset($sd_data['sd_logo'])){
                                                        $image_details[0] = $sd_data['sd_logo']['url'];
                                                    }

                                            }				
                                                $description = strip_tags(strip_shortcodes(get_the_excerpt()));

                                                if(!$description){
                                                    $description = get_bloginfo('description');
                                                }                                                                                                                        
						$input1 = array(
						'@context'			=> 'http://schema.org',
						'@type'				=> 'VideoObject',
                                                '@id'                           => get_permalink().'/#videoobject',        
						'url'				=> get_permalink(),
						'headline'			=> get_the_title(),
						'datePublished'                 => esc_html($date),
						'dateModified'                  => esc_html($modified_date),
						'description'                   => $description,
						'name'				=> get_the_title(),
						'uploadDate'                    => esc_html($date),
						'thumbnailUrl'                  => isset($image_details[0]) ? esc_url($image_details[0]):'',
						'mainEntity'                    => array(
								'@type'				=> 'WebPage',
								'@id'				=> get_permalink(),
								), 
						'author'			=> array(
								'@type' 			=> 'Person',
								'name'				=> esc_attr($aurthor_name),
								'image'				=> array(
								'@type'				=> 'ImageObject',
								'url'				=> saswp_remove_warnings($author_details, 'url', 'saswp_string'),
								'height'			=> saswp_remove_warnings($author_details, 'height', 'saswp_string'),
								'width'				=> saswp_remove_warnings($author_details, 'width', 'saswp_string')
								),
							)						                                                                                                      
						);
                                                 if(!empty($publisher)){
                            
                                                    $input1 = array_merge($input1, $publisher);   
                         
                                                 }
                                                if(isset($schema_options['enable_custom_field']) && $schema_options['enable_custom_field'] ==1){
                                                    $service = new saswp_output_service();
                                                    $input1 = $service->saswp_replace_with_custom_fields_value($input1, $schema_post_id);
                                                }
                                                if(isset($sd_data['saswp_comments_schema']) && $sd_data['saswp_comments_schema'] ==1){
                                                 $input1['comment'] = saswp_get_comments(get_the_ID());
                                                }                                                
                                                if(!empty($aggregateRating)){
                                                       $input1['aggregateRating'] = $aggregateRating;
                                                 }                                               
                                                if(!empty($extra_theme_review)){
                                                  $input1 = array_merge($input1, $extra_theme_review);
                                                 }
					
                                        $input1 = apply_filters('saswp_modify_video_object_schema_output', $input1 );
				}
                        
                        if( 'local_business' === $schema_type){
                            
                                $business_type    = esc_sql ( get_post_meta($schema_post_id, 'saswp_business_type', true)  );                                 
                                $business_name    = esc_sql ( get_post_meta($schema_post_id, 'saswp_business_name', true)  );                                 
                                $business_details = esc_sql ( get_post_meta($schema_post_id, 'saswp_local_business_details', true)  );                                                                                                
                                $dayoftheweek     = get_post_meta($schema_post_id, 'saswp_dayofweek', true); 
                                $dayoftheweek     = explode( "\r\n", $dayoftheweek);                               
                                
                                if($business_name){
                                    
                                $local_business = $business_name;    
                                
                                }else{
                                    
                                $local_business = $business_type;        
                                
                                } 
                                
				$input1 = array(
				'@context'			=> 'http://schema.org',
				'@type'				=> esc_attr($local_business),
                                '@id'                           => get_permalink().'/#'. strtolower(esc_attr($local_business)),            
                                'name'                          => saswp_remove_warnings($business_details, 'local_business_name', 'saswp_string'),                                   
				'url'				=> get_permalink(),				
				'description'                   => strip_tags(strip_shortcodes(get_the_excerpt())),
                                'image'                         => array(
                                                                        '@type'		=> 'ImageObject',
                                                                        'url'		=> isset($business_details['local_business_logo']) ? esc_url($business_details['local_business_logo']['url']):'',
                                                                        'width'		=> isset($business_details['local_business_logo']) ? esc_attr($business_details['local_business_logo']['width']):'',
                                                                        'height'	=> isset($business_details['local_business_logo']) ? esc_attr($business_details['local_business_logo']['height']):'',    
                                                                ),    
				'address'                       => array(
                                                                "@type"           => "PostalAddress",
                                                                "streetAddress"   => saswp_remove_warnings($business_details, 'local_street_address', 'saswp_string'),
                                                                "addressLocality" => saswp_remove_warnings($business_details, 'local_city', 'saswp_string'),
                                                                "addressRegion"   => saswp_remove_warnings($business_details, 'local_state', 'saswp_string'),
                                                                "postalCode"      => saswp_remove_warnings($business_details, 'local_postal_code', 'saswp_string'),                                                                                                                                  
                                                                 ),	
				'telephone'                   => saswp_remove_warnings($business_details, 'local_phone', 'saswp_string'),
                                'openingHours'                => $dayoftheweek,
                                
				);  
                                    if(isset($schema_options['enable_custom_field']) && $schema_options['enable_custom_field'] == 1){
                                        
                                        $service = new saswp_output_service();
                                        $input1 = $service->saswp_replace_with_custom_fields_value($input1, $schema_post_id);
                                    
                                    }     
                                    
                                    
                                    if(isset($business_details['local_enable_rating'])
                                            && saswp_remove_warnings($business_details, 'local_rating', 'saswp_string') !=''
                                            && saswp_remove_warnings($business_details, 'local_review_count', 'saswp_string') !=''
                                            ){
                                        
                                                                         
                                    $input1['aggregateRating'] = array(
                                                            "@type"       => "AggregateRating",
                                                            "ratingValue" => saswp_remove_warnings($business_details, 'local_rating', 'saswp_string'),
                                                            "reviewCount" => saswp_remove_warnings($business_details, 'local_review_count', 'saswp_string')
                                                         );                                       
                                     }
                                    
                                    
                                    if(!empty($aggregateRating)){
                                    $input1['aggregateRating'] = $aggregateRating;
                                    }                                    
                                    if(!empty($extra_theme_review)){
                                    $input1 = array_merge($input1, $extra_theme_review);
                                    }
                                    if(isset($business_details['local_price_range'])){
                                      $input1['priceRange'] = esc_attr($business_details['local_price_range']);   
                                    }
                                    
                                    if(isset($business_details['local_accepts_reservations'])){
                                      $input1['acceptsReservations'] = esc_attr($business_details['local_accepts_reservations']);   
                                    }
                                    
                                    if(isset($business_details['local_serves_cuisine'])){
                                      $input1['servesCuisine'] = esc_attr($business_details['local_serves_cuisine']);   
                                    }
                                    
                                    if(isset($business_details['local_menu'])){
                                      $input1['hasMenu'] = esc_url($business_details['local_menu']);   
                                    }
                                    if(isset($business_details['local_hasmap'])){
                                      $input1['hasMap'] = esc_url($business_details['local_hasmap']);   
                                    }
                                    
                                    
                                    if(isset($business_details['local_latitude']) && isset($business_details['local_longitude'])){
                                         
                                        $input1['geo']['@type']     = 'GeoCoordinates';
                                        $input1['geo']['latitude']  = $business_details['local_latitude'];
                                        $input1['geo']['longitude'] = $business_details['local_longitude'];
                                    }
                                    
                                    
                                    $input1 = apply_filters('saswp_modify_local_business_schema_output', $input1 );
			}
                        
                        
                        //Speakable schema
                        
                        if($schema_type == 'TechArticle' || $schema_type == 'Article' || $schema_type == 'Blogposting' || $schema_type == 'NewsArticle' || $schema_type == 'WebPage'){
                                           
                              $speakable_status = get_post_meta($schema_post_id, 'saswp_enable_speakable_schema', true);
                            
                              if($speakable_status){
                            
                                  $input1['speakable']['@type'] = 'SpeakableSpecification';
                                  $input1['speakable']['xpath'] = array(
                                         "/html/head/title",
                                         "/html/head/meta[@name='description']/@content"
                                    );
                                  
                              }
                            
                           
                        }
                        
                        if($schema_type !='Review' || (isset($sd_data['saswp-the-events-calendar']) && $sd_data['saswp-the-events-calendar'] == 0) || (isset($sd_data['saswp-woocommerce']) && $sd_data['saswp-woocommerce'] == 0)){
                            
                            //kk star rating 
                        
                                $kkstar_aggregateRating = saswp_extract_kk_star_ratings();
                                
                                if(!empty($kkstar_aggregateRating)){
                                    $input1['aggregateRating'] = $kkstar_aggregateRating; 
                                }

                                //wp post-rating star rating 

                                $wp_post_rating_ar = saswp_extract_wp_post_ratings();

                                if(!empty($wp_post_rating_ar)){
                                    $input1['aggregateRating'] = $wp_post_rating_ar; 
                                }                            
                            
                        }                                                
                                
                        //Check for Featured Image
                        
                         if( !empty($input1) && !isset($input1['image'])){
                             
                             $service_object     = new saswp_output_service();
                             $input2             = $service_object->saswp_get_fetaure_image();
                             
                             if(!empty($input2)){
                                 
                               $input1 = array_merge($input1,$input2); 
                               
                             }                                                                    
                        }
               
		if(isset($schema_options['notAccessibleForFree']) && $schema_options['notAccessibleForFree'] == 1){

			add_filter( 'amp_post_template_data', 'saswp_structure_data_access_scripts');			
                        
			$paywall_class_name  = $schema_options['paywall_class_name'];
			$isAccessibleForFree = isset($schema_options['isAccessibleForFree'])? $schema_options['isAccessibleForFree']: False;

			if($paywall_class_name != ""){
                            
				if(strpos($paywall_class_name, ".") == -1){
                                    
					$paywall_class_name = ".".$paywall_class_name;
                                        
				}
                                
				$paywallData = array("isAccessibleForFree"=> $isAccessibleForFree,
                                                     "hasPart"=>array(
                                                                "@type"               => "WebPageElement",
                                                                "isAccessibleForFree" => esc_attr($isAccessibleForFree),
                                                                "cssSelector"         => '.'.esc_attr($paywall_class_name)
                                                              )
                                                          );
                                
				$input1 = array_merge($input1,$paywallData);
			}
		} 
                
                $input1 = apply_filters('saswp_modify_woocommerce_membership_schema', $input1);
                
                if(!empty($input1)){
                    $all_schema_output[] = $input1;		                    
                }                
	//}
        }   
                
        return apply_filters('saswp_modify_schema_output', $all_schema_output);
}

/**
 * Function generates json markup for the all added schema type in the current post metabox
 * @global type $post
 * @global type $sd_data
 * @return type json
 */
function saswp_post_specific_schema_output() {
    
	global $post;
        global $sd_data;   
        
        
        $logo      = ''; 
        $height    = '';
        $width     = '';
        $site_name = '';
                
        $service_object     = new saswp_output_service();
        $default_logo       = $service_object->saswp_get_publisher(true);
        
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
        
        
        $all_schema_output = array();
        $all_schemas       = json_decode(get_transient('saswp_transient_schema_ids'), true); 
        
        if(!$all_schemas){
        
            $all_schemas = saswp_get_saved_schema_ids();
        
        }
        
        $schema_enable     = get_post_meta($post->ID, 'saswp_enable_disable_schema', true);
       
        if($all_schemas){
            
        foreach($all_schemas as $schema){
            
        $input1 = array(); 
        
        $schema_id      = $schema;   	
	$schema_type    = esc_sql ( get_post_meta($schema_id, 'schema_type', true)  );        
        $schema_post_id = $post->ID;  
	$all_post_meta  = esc_sql ( get_post_meta($schema_post_id, $key='', true)  );     
	
	if(is_singular() && isset($schema_enable[$schema_id]) && $schema_enable[$schema_id] == 1 ){
		
                        $saswp_review_details = esc_sql ( get_post_meta(get_the_ID(), 'saswp_review_details', true)); 
                        
                        $aggregateRating        = array();                        
                        $saswp_over_all_rating  ='';
                        
                        if(isset($saswp_review_details['saswp-review-item-over-all'])){
                            
                        $saswp_over_all_rating = $saswp_review_details['saswp-review-item-over-all'];    
                        
                        }                
                        $saswp_review_item_enable = 0;
                        
                        if(isset($saswp_review_details['saswp-review-item-enable'])){
                            
                         $saswp_review_item_enable =  $saswp_review_details['saswp-review-item-enable'];  
                         
                        } 
                        $saswp_review_count = "1";                            
                        
                        
                        if($saswp_over_all_rating && $saswp_review_count && $saswp_review_item_enable ==1 && isset($sd_data['saswp-review-module']) && $sd_data['saswp-review-module'] ==1){
                            
                           $aggregateRating =       array(
                                                            "@type"       => "AggregateRating",
                                                            "ratingValue" => esc_attr($saswp_over_all_rating),
                                                            "reviewCount" => esc_attr($saswp_review_count)
                                                         );                            
                        }
                        
                        $extra_theme_review = array();
                        $service_object     = new saswp_output_service();
                        $extra_theme_review = $service_object->saswp_extra_theme_review_details(get_the_ID());
            
                         if( 'Mosque' === $schema_type){
                             
                            $howto_image = get_post_meta( get_the_ID(), 'saswp_mosque_schema_image_'.$schema_id.'_detail',true); 
                            
                                                                                   
                            $input1['@context']              = 'http://schema.org';
                            $input1['@type']                 = 'Mosque';
                            $input1['@id']                   = get_permalink().'/#Mosque';
                            $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_mosque_schema_url_'.$schema_id, 'saswp_array');                            
                            $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_mosque_schema_name_'.$schema_id, 'saswp_array');                            
                            $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_mosque_schema_description_'.$schema_id, 'saswp_array');
                              
                            if(!(empty($howto_image))){
                             
                            $input1['image']['@type']        = 'ImageObject';
                            $input1['image']['url']          = isset($howto_image['thumbnail']) ? esc_url($howto_image['thumbnail']):'';
                            $input1['image']['height']       = isset($howto_image['width'])     ? esc_attr($howto_image['width'])   :'';
                            $input1['image']['width']        = isset($howto_image['height'])    ? esc_attr($howto_image['height'])  :'';
                                
                            }  
                            
                            $input1['isAccessibleForFree']        = saswp_remove_warnings($all_post_meta, 'saswp_mosque_schema_is_accesible_free_'.$schema_id, 'saswp_array');                            
                            $input1['maximumAttendeeCapacity']    = saswp_remove_warnings($all_post_meta, 'saswp_mosque_schema_maximum_a_capacity_'.$schema_id, 'saswp_array');
                            $input1['hasMap']                     = saswp_remove_warnings($all_post_meta, 'saswp_mosque_schema_hasmap_'.$schema_id, 'saswp_array');
                            
                            $input1['address']['@type']             = 'PostalAddress';
                            $input1['address']['addressCountry']    = saswp_remove_warnings($all_post_meta, 'saswp_mosque_schema_country_'.$schema_id, 'saswp_array');
                            $input1['address']['addressLocality']   = saswp_remove_warnings($all_post_meta, 'saswp_mosque_schema_locality_'.$schema_id, 'saswp_array');
                            $input1['address']['addressRegion']     = saswp_remove_warnings($all_post_meta, 'saswp_mosque_schema_region_'.$schema_id, 'saswp_array');
                            $input1['address']['PostalCode']        = saswp_remove_warnings($all_post_meta, 'saswp_mosque_schema_postal_code_'.$schema_id, 'saswp_array');
                                                                                   
                            }  
                        
                         if( 'Church' === $schema_type){
                             
                            $howto_image = get_post_meta( get_the_ID(), 'saswp_church_schema_image_'.$schema_id.'_detail',true); 
                            
                                                                                   
                            $input1['@context']              = 'http://schema.org';
                            $input1['@type']                 = 'Church';
                            $input1['@id']                   = get_permalink().'/#Church';
                            $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_church_schema_url_'.$schema_id, 'saswp_array');                            
                            $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_church_schema_name_'.$schema_id, 'saswp_array');                            
                            $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_church_schema_description_'.$schema_id, 'saswp_array');
                              
                            if(!(empty($howto_image))){
                             
                            $input1['image']['@type']        = 'ImageObject';
                            $input1['image']['url']          = isset($howto_image['thumbnail']) ? esc_url($howto_image['thumbnail']):'';
                            $input1['image']['height']       = isset($howto_image['width'])     ? esc_attr($howto_image['width'])   :'';
                            $input1['image']['width']        = isset($howto_image['height'])    ? esc_attr($howto_image['height'])  :'';
                                
                            }  
                            
                            $input1['isAccessibleForFree']    = saswp_remove_warnings($all_post_meta, 'saswp_church_schema_is_acceesible_free_'.$schema_id, 'saswp_array');                           
                            $input1['maximumAttendeeCapacity']    = saswp_remove_warnings($all_post_meta, 'saswp_church_schema_maximum_a_capacity_'.$schema_id, 'saswp_array');
                            $input1['hasMap']                     = saswp_remove_warnings($all_post_meta, 'saswp_church_schema_hasmap_'.$schema_id, 'saswp_array');
                            
                            $input1['address']['@type']             = 'PostalAddress';
                            $input1['address']['addressCountry']    = saswp_remove_warnings($all_post_meta, 'saswp_church_schema_country_'.$schema_id, 'saswp_array');
                            $input1['address']['addressLocality']   = saswp_remove_warnings($all_post_meta, 'saswp_church_schema_locality_'.$schema_id, 'saswp_array');
                            $input1['address']['addressRegion']     = saswp_remove_warnings($all_post_meta, 'saswp_church_schema_region_'.$schema_id, 'saswp_array');
                            $input1['address']['PostalCode']        = saswp_remove_warnings($all_post_meta, 'saswp_church_schema_postal_code_'.$schema_id, 'saswp_array');
                                                                                   
                            }   
                        
                         if( 'HinduTemple' === $schema_type){
                             
                            $howto_image = get_post_meta( get_the_ID(), 'saswp_hindutemple_schema_image_'.$schema_id.'_detail',true); 
                            
                                                                                   
                            $input1['@context']              = 'http://schema.org';
                            $input1['@type']                 = 'HinduTemple';
                            $input1['@id']                   = get_permalink().'/#HinduTemple';
                            $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_hindutemple_schema_url_'.$schema_id, 'saswp_array');                            
                            $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_hindutemple_schema_name_'.$schema_id, 'saswp_array');                            
                            $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_hindutemple_schema_description_'.$schema_id, 'saswp_array');
                              
                            if(!(empty($howto_image))){
                             
                            $input1['image']['@type']        = 'ImageObject';
                            $input1['image']['url']          = isset($howto_image['thumbnail']) ? esc_url($howto_image['thumbnail']):'';
                            $input1['image']['height']       = isset($howto_image['width'])     ? esc_attr($howto_image['width'])   :'';
                            $input1['image']['width']        = isset($howto_image['height'])    ? esc_attr($howto_image['height'])  :'';
                                
                            }  
                                                        
                            $input1['isAccessibleForFree']        = saswp_remove_warnings($all_post_meta, 'saswp_hindutemple_schema_is_accesible_free_'.$schema_id, 'saswp_array');                           
                            $input1['maximumAttendeeCapacity']    = saswp_remove_warnings($all_post_meta, 'saswp_hindutemple_schema_maximum_a_capacity_'.$schema_id, 'saswp_array');
                            $input1['hasMap']                     = saswp_remove_warnings($all_post_meta, 'saswp_hindutemple_schema_hasmap_'.$schema_id, 'saswp_array');
                            
                            $input1['address']['@type']             = 'PostalAddress';
                            $input1['address']['addressCountry']    = saswp_remove_warnings($all_post_meta, 'saswp_hindutemple_schema_country_'.$schema_id, 'saswp_array');
                            $input1['address']['addressLocality']   = saswp_remove_warnings($all_post_meta, 'saswp_hindutemple_schema_locality_'.$schema_id, 'saswp_array');
                            $input1['address']['addressRegion']     = saswp_remove_warnings($all_post_meta, 'saswp_hindutemple_schema_region_'.$schema_id, 'saswp_array');
                            $input1['address']['PostalCode']        = saswp_remove_warnings($all_post_meta, 'saswp_hindutemple_schema_postal_code_'.$schema_id, 'saswp_array');
                                                                                   
                            }   
                        
                         if( 'LandmarksOrHistoricalBuildings' === $schema_type){
                             
                            $howto_image = get_post_meta( get_the_ID(), 'saswp_lorh_schema_image_'.$schema_id.'_detail',true); 
                            
                                                                                   
                            $input1['@context']              = 'http://schema.org';
                            $input1['@type']                 = 'LandmarksOrHistoricalBuildings';
                            $input1['@id']                   = get_permalink().'/#LandmarksOrHistoricalBuildings';
                            $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_lorh_schema_url_'.$schema_id, 'saswp_array');                            
                            $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_lorh_schema_name_'.$schema_id, 'saswp_array');                            
                            $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_lorh_schema_description_'.$schema_id, 'saswp_array');
                              
                            if(!(empty($howto_image))){
                             
                            $input1['image']['@type']        = 'ImageObject';
                            $input1['image']['url']          = isset($howto_image['thumbnail']) ? esc_url($howto_image['thumbnail']):'';
                            $input1['image']['height']       = isset($howto_image['width'])     ? esc_attr($howto_image['width'])   :'';
                            $input1['image']['width']        = isset($howto_image['height'])    ? esc_attr($howto_image['height'])  :'';
                                
                            }  
                            
                            $input1['hasMap']                     = saswp_remove_warnings($all_post_meta, 'saswp_lorh_schema_hasmap_'.$schema_id, 'saswp_array');                        
                            $input1['isAccessibleForFree']        = saswp_remove_warnings($all_post_meta, 'saswp_lorh_schema_is_acceesible_free_'.$schema_id, 'saswp_array');
                            $input1['maximumAttendeeCapacity']    = saswp_remove_warnings($all_post_meta, 'saswp_lorh_schema_maximum_a_capacity_'.$schema_id, 'saswp_array');                          
                            
                            $input1['address']['@type']             = 'PostalAddress';
                            $input1['address']['addressCountry']    = saswp_remove_warnings($all_post_meta, 'saswp_lorh_schema_address_country_'.$schema_id, 'saswp_array');
                            $input1['address']['addressLocality']   = saswp_remove_warnings($all_post_meta, 'saswp_lorh_schema_address_locality_'.$schema_id, 'saswp_array');
                            $input1['address']['addressRegion']     = saswp_remove_warnings($all_post_meta, 'saswp_lorh_schema_address_region_'.$schema_id, 'saswp_array');
                            $input1['address']['PostalCode']        = saswp_remove_warnings($all_post_meta, 'saswp_lorh_schema_address_postal_code_'.$schema_id, 'saswp_array');
                                                                                   
                            }   
                        
                         if( 'TouristAttraction' === $schema_type){
                             
                            $howto_image = get_post_meta( get_the_ID(), 'saswp_ta_schema_image_'.$schema_id.'_detail',true); 
                            
                                                                                   
                            $input1['@context']              = 'http://schema.org';
                            $input1['@type']                 = 'TouristAttraction';
                            $input1['@id']                   = get_permalink().'/#TouristAttraction';
                            $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_ta_schema_url_'.$schema_id, 'saswp_array');                            
                            $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_ta_schema_name_'.$schema_id, 'saswp_array');                            
                            $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_ta_schema_description_'.$schema_id, 'saswp_array');
                              
                            if(!(empty($howto_image))){
                             
                            $input1['image']['@type']        = 'ImageObject';
                            $input1['image']['url']          = isset($howto_image['thumbnail']) ? esc_url($howto_image['thumbnail']):'';
                            $input1['image']['height']       = isset($howto_image['width'])     ? esc_attr($howto_image['width'])   :'';
                            $input1['image']['width']        = isset($howto_image['height'])    ? esc_attr($howto_image['height'])  :'';
                                
                            }  
                            
                            $input1['isAccessibleForFree']    = saswp_remove_warnings($all_post_meta, 'saswp_ta_schema_is_acceesible_free_'.$schema_id, 'saswp_array');
//                            $input1['openingHours']           = saswp_remove_warnings($all_post_meta, 'saswp_ta_schema_opening_hours_'.$schema_id, 'saswp_array');
//                            $input1['currenciesAccepted']     = saswp_remove_warnings($all_post_meta, 'saswp_ta_schema_currencies_accepted_'.$schema_id, 'saswp_array');
//                            $input1['paymentAccepted']        = saswp_remove_warnings($all_post_meta, 'saswp_ta_schema_payment_accepted_'.$schema_id, 'saswp_array');
                            
                            $input1['address']['@type']             = 'PostalAddress';
                            $input1['address']['addressCountry']    = saswp_remove_warnings($all_post_meta, 'saswp_ta_schema_address_country_'.$schema_id, 'saswp_array');
                            $input1['address']['addressLocality']   = saswp_remove_warnings($all_post_meta, 'saswp_ta_schema_address_locality_'.$schema_id, 'saswp_array');
                            $input1['address']['addressRegion']     = saswp_remove_warnings($all_post_meta, 'saswp_ta_schema_address_region_'.$schema_id, 'saswp_array');
                            $input1['address']['PostalCode']        = saswp_remove_warnings($all_post_meta, 'saswp_ta_schema_address_postal_code_'.$schema_id, 'saswp_array');
                                                                                   
                            }
                         
                         if( 'TouristDestination' === $schema_type){
                             
                            $howto_image = get_post_meta( get_the_ID(), 'saswp_td_schema_image_'.$schema_id.'_detail',true); 
                            
                                                                                   
                            $input1['@context']              = 'http://schema.org';
                            $input1['@type']                 = 'TouristDestination';
                            $input1['@id']                   = get_permalink().'/#TouristDestination';
                            $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_td_schema_url_'.$schema_id, 'saswp_array');                            
                            $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_td_schema_name_'.$schema_id, 'saswp_array');                            
                            $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_td_schema_description_'.$schema_id, 'saswp_array');
                              
                            if(!(empty($howto_image))){
                             
                            $input1['image']['@type']        = 'ImageObject';
                            $input1['image']['url']          = isset($howto_image['thumbnail']) ? esc_url($howto_image['thumbnail']):'';
                            $input1['image']['height']       = isset($howto_image['width'])     ? esc_attr($howto_image['width'])   :'';
                            $input1['image']['width']        = isset($howto_image['height'])    ? esc_attr($howto_image['height'])  :'';
                                
                            }  
                                                        
                            
                            $input1['address']['@type']             = 'PostalAddress';
                            $input1['address']['addressCountry']    = saswp_remove_warnings($all_post_meta, 'saswp_td_schema_address_country_'.$schema_id, 'saswp_array');
                            $input1['address']['addressLocality']   = saswp_remove_warnings($all_post_meta, 'saswp_td_schema_address_locality_'.$schema_id, 'saswp_array');
                            $input1['address']['addressRegion']     = saswp_remove_warnings($all_post_meta, 'saswp_td_schema_address_region_'.$schema_id, 'saswp_array');
                            $input1['address']['PostalCode'] = saswp_remove_warnings($all_post_meta, 'saswp_td_schema_address_postal_code_'.$schema_id, 'saswp_array');                                                       
                            
                            }   
                        
                         if( 'Apartment' === $schema_type){
                             
                            $howto_image = get_post_meta( get_the_ID(), 'saswp_apartment_schema_image_'.$schema_id.'_detail',true); 
                            
                                                                                   
                            $input1['@context']              = 'http://schema.org';
                            $input1['@type']                 = 'Apartment';
                            $input1['@id']                   = get_permalink().'/#Apartment';
                            $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_apartment_schema_url_'.$schema_id, 'saswp_array');                            
                            $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_apartment_schema_name_'.$schema_id, 'saswp_array');                            
                            $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_apartment_schema_description_'.$schema_id, 'saswp_array');
                              
                            if(!(empty($howto_image))){
                             
                            $input1['image']['@type']        = 'ImageObject';
                            $input1['image']['url']          = isset($howto_image['thumbnail']) ? esc_url($howto_image['thumbnail']):'';
                            $input1['image']['height']       = isset($howto_image['width'])     ? esc_attr($howto_image['width'])   :'';
                            $input1['image']['width']        = isset($howto_image['height'])    ? esc_attr($howto_image['height'])  :'';
                                
                            }  
                            
                            $input1['numberOfRooms']           = saswp_remove_warnings($all_post_meta, 'saswp_apartment_schema_numberofrooms_'.$schema_id, 'saswp_array');
                            
                            $input1['address']['@type']             = 'PostalAddress';
                            $input1['address']['addressCountry']    = saswp_remove_warnings($all_post_meta, 'saswp_apartment_schema_country_'.$schema_id, 'saswp_array');
                            $input1['address']['addressLocality']   = saswp_remove_warnings($all_post_meta, 'saswp_apartment_schema_locality_'.$schema_id, 'saswp_array');
                            $input1['address']['addressRegion']     = saswp_remove_warnings($all_post_meta, 'saswp_apartment_schema_region_'.$schema_id, 'saswp_array');
                            $input1['address']['PostalCode'] = saswp_remove_warnings($all_post_meta, 'saswp_apartment_schema_postalcode_'.$schema_id, 'saswp_array');
                            
                            $input1['telephone']           = saswp_remove_warnings($all_post_meta, 'saswp_apartment_schema_telephone_'.$schema_id, 'saswp_array');
                            
                            }
                            
                         if( 'House' === $schema_type){
                             
                            $howto_image = get_post_meta( get_the_ID(), 'saswp_house_schema_image_'.$schema_id.'_detail',true); 
                            
                                                                                   
                            $input1['@context']              = 'http://schema.org';
                            $input1['@type']                 = 'House';
                            $input1['@id']                   = get_permalink().'/#House';
                            $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_house_schema_url_'.$schema_id, 'saswp_array');                            
                            $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_house_schema_name_'.$schema_id, 'saswp_array');                            
                            $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_house_schema_description_'.$schema_id, 'saswp_array');
                              
                            if(!(empty($howto_image))){
                             
                            $input1['image']['@type']        = 'ImageObject';
                            $input1['image']['url']          = isset($howto_image['thumbnail']) ? esc_url($howto_image['thumbnail']):'';
                            $input1['image']['height']       = isset($howto_image['width'])     ? esc_attr($howto_image['width'])   :'';
                            $input1['image']['width']        = isset($howto_image['height'])    ? esc_attr($howto_image['height'])  :'';
                                
                            }  
                            
                            $input1['petsAllowed']                  = saswp_remove_warnings($all_post_meta, 'saswp_house_schema_pets_allowed_'.$schema_id, 'saswp_array');
                            
                            $input1['address']['@type']             = 'PostalAddress';
                            $input1['address']['addressCountry']    = saswp_remove_warnings($all_post_meta, 'saswp_house_schema_country_'.$schema_id, 'saswp_array');
                            $input1['address']['addressLocality']   = saswp_remove_warnings($all_post_meta, 'saswp_house_schema_locality_'.$schema_id, 'saswp_array');
                            $input1['address']['addressRegion']     = saswp_remove_warnings($all_post_meta, 'saswp_house_schema_region_'.$schema_id, 'saswp_array');
                            $input1['address']['PostalCode'] = saswp_remove_warnings($all_post_meta, 'saswp_house_schema_postalcode_'.$schema_id, 'saswp_array');
                            
                            $input1['telephone']                    = saswp_remove_warnings($all_post_meta, 'saswp_house_schema_telephone_'.$schema_id, 'saswp_array');
                            
                            }  
                            
                         if( 'SingleFamilyResidence' === $schema_type){
                             
                            $howto_image = get_post_meta( get_the_ID(), 'saswp_sfr_schema_image_'.$schema_id.'_detail',true);                            
                                                                                   
                            $input1['@context']              = 'http://schema.org';
                            $input1['@type']                 = 'SingleFamilyResidence';
                            $input1['@id']                   = get_permalink().'/#SingleFamilyResidence';
                            $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_sfr_schema_url_'.$schema_id, 'saswp_array');                            
                            $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_sfr_schema_name_'.$schema_id, 'saswp_array');                            
                            $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_sfr_schema_description_'.$schema_id, 'saswp_array');
                              
                            if(!(empty($howto_image))){
                             
                            $input1['image']['@type']        = 'ImageObject';
                            $input1['image']['url']          = isset($howto_image['thumbnail']) ? esc_url($howto_image['thumbnail']):'';
                            $input1['image']['height']       = isset($howto_image['width'])     ? esc_attr($howto_image['width'])   :'';
                            $input1['image']['width']        = isset($howto_image['height'])    ? esc_attr($howto_image['height'])  :'';
                                
                            }  
                            $input1['numberOfRooms']                = saswp_remove_warnings($all_post_meta, 'saswp_sfr_schema_numberofrooms_'.$schema_id, 'saswp_array');
                            $input1['petsAllowed']                  = saswp_remove_warnings($all_post_meta, 'saswp_sfr_schema_pets_allowed_'.$schema_id, 'saswp_array');
                            
                            $input1['address']['@type']             = 'PostalAddress';
                            $input1['address']['addressCountry']    = saswp_remove_warnings($all_post_meta, 'saswp_sfr_schema_country_'.$schema_id, 'saswp_array');
                            $input1['address']['addressLocality']   = saswp_remove_warnings($all_post_meta, 'saswp_sfr_schema_locality_'.$schema_id, 'saswp_array');
                            $input1['address']['addressRegion']     = saswp_remove_warnings($all_post_meta, 'saswp_sfr_schema_region_'.$schema_id, 'saswp_array');
                            $input1['address']['PostalCode'] = saswp_remove_warnings($all_post_meta, 'saswp_sfr_schema_postalcode_'.$schema_id, 'saswp_array');
                            
                            $input1['telephone']                    = saswp_remove_warnings($all_post_meta, 'saswp_sfr_schema_telephone_'.$schema_id, 'saswp_array');
                            
                            }     
                                                
                         if( 'HowTo' === $schema_type){
                             
                            $howto_image = get_post_meta( get_the_ID(), 'saswp_howto_schema_image_'.$schema_id.'_detail',true); 
                            
                             
                            $tool    = esc_sql ( get_post_meta($schema_post_id, 'howto_tool_'.$schema_id, true)  );              
                            $step    = esc_sql ( get_post_meta($schema_post_id, 'howto_step_'.$schema_id, true)  );              
                            $supply  = esc_sql ( get_post_meta($schema_post_id, 'howto_supply_'.$schema_id, true)  );              
                            
                            
                            $input1['@context']              = 'http://schema.org';
                            $input1['@type']                 = 'HowTo';
                            $input1['@id']                   = get_permalink().'/#HowTo';
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
                            
                            $input1['estimatedCost']['@type']   = 'MonetaryAmount';
                            $input1['estimatedCost']['currency']= saswp_remove_warnings($all_post_meta, 'saswp_howto_ec_schema_currency_'.$schema_id, 'saswp_array');
                            $input1['estimatedCost']['value']   = saswp_remove_warnings($all_post_meta, 'saswp_howto_ec_schema_value_'.$schema_id, 'saswp_array');
                                                        
                            $supply_arr = array();
                            if(!empty($supply)){
                                
                                foreach($supply as $val){
                                   
                                    $supply_data = array();
                                    $supply_data['@type'] = 'HowToSupply';
                                    $supply_data['name'] = $val['saswp_howto_supply_name'];
                                    
                                    if(isset($val['saswp_howto_supply_image_id'])){
                                        
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
                                    $supply_data['@type'] = 'HowToTool';
                                    $supply_data['name'] = $val['saswp_howto_tool_name'];
                                    
                                    if(isset($val['saswp_howto_tool_image_id'])){
                                        
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
                                    
                                    $direction['@type']     = 'HowToDirection';
                                    $direction['text']      = $val['saswp_howto_direction_text'];
                                    
                                    $tip['@type']           = 'HowToTip';
                                    $tip['text']            = $val['saswp_howto_tip_text'];
                                    
                                    
                                    $supply_data['@type']   = 'HowToStep';
                                    $supply_data['url']     = get_permalink().'#step'.++$key;
                                    $supply_data['name']    = $val['saswp_howto_step_name'];                                                                                                            
                                    $supply_data['itemListElement']  = array($direction, $tip);
                                    
                                    if(isset($val['saswp_howto_step_image_id'])){
                                        
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
                                                       
                            }
                            
                         if( 'TVSeries' === $schema_type){
                             
                            $howto_image = get_post_meta( get_the_ID(), 'saswp_tvseries_schema_image_'.$schema_id.'_detail',true); 
                            
                             
                            $actor     = esc_sql ( get_post_meta($schema_post_id, 'tvseries_actor_'.$schema_id, true)  );              
                            $season    = esc_sql ( get_post_meta($schema_post_id, 'tvseries_season_'.$schema_id, true)  );                                          
                                                        
                            $input1['@context']              = 'http://schema.org';
                            $input1['@type']                 = 'TVSeries';
                            $input1['@id']                   = get_permalink().'/#TVSeries';                                            
                            $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_tvseries_schema_name_'.$schema_id, 'saswp_array');                            			    
                            $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_tvseries_schema_description_'.$schema_id, 'saswp_array');
                              
                            if(!(empty($howto_image))){
                             
                            $input1['image']['@type']        = 'ImageObject';
                            $input1['image']['url']          = isset($howto_image['thumbnail']) ? esc_url($howto_image['thumbnail']):'';
                            $input1['image']['height']       = isset($howto_image['width'])     ? esc_attr($howto_image['width'])   :'';
                            $input1['image']['width']        = isset($howto_image['height'])    ? esc_attr($howto_image['height'])  :'';
                                
                            }                            
                            
                            $input1['author']['@type']       = 'Person';
                            $input1['author']['name']        = saswp_remove_warnings($all_post_meta, 'saswp_tvseries_schema_author_name_'.$schema_id, 'saswp_array');                            
                                                        
                            $supply_arr = array();
                            if(!empty($actor)){
                                
                                foreach($actor as $val){
                                   
                                    $supply_data = array();
                                    $supply_data['@type'] = 'Person';
                                    $supply_data['name']  = $val['saswp_tvseries_actor_name'];
                                    
                                    $supply_arr[] =  $supply_data;
                                }
                               $input1['actor'] = $supply_arr;
                            }
                                                        
                            $tool_arr = array();
                            if(!empty($season)){
                                
                                foreach($season as $val){
                                   
                                    $supply_data = array();
                                    $supply_data['@type']            = 'TVSeason';
                                    $supply_data['datePublished']    = $val['saswp_tvseries_season_published_date'];
                                    $supply_data['name']             = $val['saswp_tvseries_season_name'];
                                    $supply_data['numberOfEpisodes'] = $val['saswp_tvseries_season_episodes'];
                                                                        
                                    $tool_arr[] =  $supply_data;
                                }
                               $input1['containsSeason'] = $tool_arr;
                            }
                                                                                                              
                            }   
                            
                         if( 'MedicalCondition' === $schema_type){
                                                         
                            $howto_image = get_post_meta( get_the_ID(), 'saswp_mc_schema_image_'.$schema_id.'_detail',true);  
                             
                            $cause       = esc_sql ( get_post_meta($schema_post_id, 'mc_cause_'.$schema_id, true));              
                            $symptom     = esc_sql ( get_post_meta($schema_post_id, 'mc_symptom_'.$schema_id, true));              
                            $riskfactro  = esc_sql ( get_post_meta($schema_post_id, 'mc_risk_factor_'.$schema_id, true));              
                            
                            
                            $input1['@context']                     = 'http://schema.org';
                            $input1['@type']                        = 'MedicalCondition';
                            $input1['@id']                          = get_permalink().'/#MedicalCondition';
                            $input1['name']                         = saswp_remove_warnings($all_post_meta, 'saswp_mc_schema_name_'.$schema_id, 'saswp_array');
                            $input1['alternateName']                = saswp_remove_warnings($all_post_meta, 'saswp_mc_schema_alternate_name_'.$schema_id, 'saswp_array');                            
                            $input1['description']                  = saswp_remove_warnings($all_post_meta, 'saswp_mc_schema_description_'.$schema_id, 'saswp_array');
                            
                            if(!(empty($howto_image))){
                             
                            $input1['image']['@type']        = 'ImageObject';
                            $input1['image']['url']          = isset($howto_image['thumbnail']) ? esc_url($howto_image['thumbnail']):'';
                            $input1['image']['height']       = isset($howto_image['width'])     ? esc_attr($howto_image['width'])   :'';
                            $input1['image']['width']        = isset($howto_image['height'])    ? esc_attr($howto_image['height'])  :'';
                                
                            }
                                                                                                             
                            $input1['associatedAnatomy']['@type']   = 'AnatomicalStructure';
                            $input1['associatedAnatomy']['name']    = saswp_remove_warnings($all_post_meta, 'saswp_mc_schema_anatomy_name_'.$schema_id, 'saswp_array');                            
                                                        
                            $input1['code']['@type']                = 'MedicalCode';
                            $input1['code']['code']                 = saswp_remove_warnings($all_post_meta, 'saswp_mc_schema_medical_code_'.$schema_id, 'saswp_array');                            
                            $input1['code']['codingSystem']         = saswp_remove_warnings($all_post_meta, 'saswp_mc_schema_coding_system_'.$schema_id, 'saswp_array');                            
                                                        
                            $cause_arr = array();
                            if(!empty($cause)){
                                
                                foreach($cause as $val){
                                   
                                    $supply_data = array();
                                    $supply_data['@type'] = 'MedicalCause';
                                    $supply_data['name'] = $val['saswp_mc_cause_name'];
                                    
                                   $cause_arr[] =  $supply_data;
                                }
                               $input1['cause'] = $cause_arr;
                            }
                            
                            $symptom_arr = array();
                            if(!empty($symptom)){
                                
                                foreach($symptom as $val){
                                   
                                    $supply_data = array();
                                    $supply_data['@type'] = 'MedicalSymptom';
                                    $supply_data['name'] = $val['saswp_mc_symptom_name'];
                                    
                                   $symptom_arr[] =  $supply_data;
                                }
                               $input1['signOrSymptom'] = $symptom_arr;
                            }
                            
                            $riskfactor_arr = array();
                            if(!empty($riskfactro)){
                                
                                foreach($riskfactro as $val){
                                   
                                    $supply_data = array();
                                    $supply_data['@type'] = 'MedicalRiskFactor';
                                    $supply_data['name'] = $val['saswp_mc_risk_factor_name'];
                                    
                                   $riskfactor_arr[] =  $supply_data;
                                }
                               $input1['riskFactor'] = $riskfactor_arr;
                            }
                                                                                                                                                                                                   
                            }
                            
                         if( 'VideoGame' === $schema_type){
                                                         
                            $howto_image = get_post_meta( get_the_ID(), 'saswp_vg_schema_image_'.$schema_id.'_detail',true);  
                             
                                                                                    
                            $input1['@context']                     = 'http://schema.org';
                            $input1['@type']                        = 'VideoGame';
                            $input1['@id']                          = get_permalink().'/#VideoGame'; 
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
                                                        
                            
                                                                                                                                                                                                                               
                            }   
                        
                         if( 'qanda' === $schema_type){      
                             
                            if(trim(saswp_remove_warnings($all_post_meta, 'saswp_qa_question_title_'.$schema_id, 'saswp_array')) ==''){
                                
                                $service_object = new saswp_output_service();
                                $input1  = $service_object->saswp_dw_question_answers_details(get_the_ID());  
                                
                            }else{
                                
                                $input1 = array(
                                    '@context'		  => 'http://schema.org',
                                    '@type'		  => 'QAPage',
                                    '@id'                 => get_permalink().'/#qapage',
                                    'mainEntity'          => array(
                                            '@type'		  => 'Question' ,
                                            'name'		  => saswp_remove_warnings($all_post_meta, 'saswp_qa_question_title_'.$schema_id, 'saswp_array'),
                                            'text'		  => saswp_remove_warnings($all_post_meta, 'saswp_qa_question_description_'.$schema_id, 'saswp_array'),
                                            'upvoteCount'         => saswp_remove_warnings($all_post_meta, 'saswp_qa_upvote_count_'.$schema_id, 'saswp_array'),
                                            'dateCreated'         => isset($all_post_meta['saswp_qa_date_created_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_qa_date_created_'.$schema_id][0])):'',
                                            'author'              => array('@type' => 'Person','name' =>saswp_remove_warnings($all_post_meta, 'saswp_qa_question_author_name_'.$schema_id, 'saswp_array')) ,
                                            'answerCount'         => 2 ,
                                            'acceptedAnswer'         => array(
                                                            '@type'       => 'Answer',
                                                            'upvoteCount' => saswp_remove_warnings($all_post_meta, 'saswp_qa_accepted_answer_upvote_count_'.$schema_id, 'saswp_array'),
                                                            'url'         => saswp_remove_warnings($all_post_meta, 'saswp_qa_accepted_answer_url_'.$schema_id, 'saswp_array'),
                                                            'text'        => saswp_remove_warnings($all_post_meta, 'saswp_qa_accepted_answer_text_'.$schema_id, 'saswp_array'),
                                                            'dateCreated' => isset($all_post_meta['saswp_qa_accepted_answer_date_created_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_qa_accepted_answer_date_created_'.$schema_id][0])):'',
                                                            'author'      => array('@type' => 'Person', 'name' => saswp_remove_warnings($all_post_meta, 'saswp_qa_accepted_author_name_'.$schema_id, 'saswp_array')),
                                            ) ,
                                            'suggestedAnswer'         => array(
                                                            '@type'       => 'Answer',
                                                            'upvoteCount' => saswp_remove_warnings($all_post_meta, 'saswp_qa_suggested_answer_upvote_count_'.$schema_id, 'saswp_array'),
                                                            'url'         => saswp_remove_warnings($all_post_meta, 'saswp_qa_suggested_answer_url_'.$schema_id, 'saswp_array'),
                                                            'text'        => saswp_remove_warnings($all_post_meta, 'saswp_qa_suggested_answer_text_'.$schema_id, 'saswp_array'),
                                                            'dateCreated' => isset($all_post_meta['saswp_qa_suggested_answer_date_created_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_qa_suggested_answer_date_created_'.$schema_id][0])):'',
                                                            'author'      => array('@type' => 'Person', 'name' => saswp_remove_warnings($all_post_meta, 'saswp_qa_suggested_author_name_'.$schema_id, 'saswp_array')),
                                            ) ,                                            
                                    )
                                );
                            }                                
			}   
                                                
                         if( 'Event' === $schema_type){
                       
                        $event_image = get_post_meta( get_the_ID(), 'saswp_event_schema_image_'.$schema_id.'_detail',true); 
                                                    
                        $input1 = array(
			'@context'			=> 'http://schema.org',
			'@type'				=> 'Event' ,
                        '@id'                           => get_permalink().'/#event',    
			'name'			        => saswp_remove_warnings($all_post_meta, 'saswp_event_schema_name_'.$schema_id, 'saswp_array'),
			'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_event_schema_description_'.$schema_id, 'saswp_array'),			
			'startDate'		        => isset($all_post_meta['saswp_event_schema_start_date_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_event_schema_start_date_'.$schema_id][0])):'',
                        'endDate'                       => isset($all_post_meta['saswp_event_schema_end_date_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_event_schema_end_date_'.$schema_id][0])):'',
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
                                        ),
                        'performer'			=> array(
                                                            '@type'  => 'PerformingGroup',
                                                            'name'   => saswp_remove_warnings($all_post_meta, 'saswp_event_schema_performer_name_'.$schema_id, 'saswp_array'),	                        
                                        ),    
                            );
                                                               
                                if(!empty($aggregateRating)){
                                    $input1['aggregateRating'] = $aggregateRating;
                                }                                
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }                               
                                                            
                        }
                        
                         if( 'DiscussionForumPosting' === $schema_type){
                            
                            $input1 = array(
                                '@context'			=> 'http://schema.org',
                                '@type'				=> 'DiscussionForumPosting' ,
                                '@id'				=> saswp_remove_warnings($all_post_meta, 'saswp_dfp_url_'.$schema_id, 'saswp_array').'/#blogposting',    			
                                'headline'			=> saswp_remove_warnings($all_post_meta, 'saswp_dfp_headline_'.$schema_id, 'saswp_array'),
                                'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_dfp_description_'.$schema_id, 'saswp_array'),			
                                'url'				=> saswp_remove_warnings($all_post_meta, 'saswp_dfp_url_'.$schema_id, 'saswp_array'),
                                'datePublished'                 => isset($all_post_meta['saswp_dfp_date_published_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_dfp_date_published_'.$schema_id][0])):'',
                                'dateModified'                  => isset($all_post_meta['saswp_dfp_date_modified_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_dfp_date_modified_'.$schema_id][0])):'',
                                'author'			=> array(
                                                                    '@type' 	        => 'Person',
                                                                    'name'		=> saswp_remove_warnings($all_post_meta, 'saswp_dfp_author_name_'.$schema_id, 'saswp_array') 
                                                                )											
                                    );
                        }
                        
                         if( 'Course' === $schema_type){
                         
                                $input1 = array(
                                '@context'			=> 'http://schema.org',
                                '@type'				=> 'Course' ,	
                                '@id'                           => get_permalink().'/#course',    
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

                                if(!empty($aggregateRating)){
                                    
                                    $input1['aggregateRating'] = $aggregateRating;
                                    
                                }                                
                                if(!empty($extra_theme_review)){
                                    
                                   $input1 = array_merge($input1, $extra_theme_review);
                                   
                                }                               
                                                            
                        }
                                                
                         if( 'Blogposting' === $schema_type){
                    		
                        $slogo = get_post_meta( get_the_ID(), 'saswp_blogposting_organization_logo_'.$schema_id.'_detail',true);                                 
			$input1 = array(
			'@context'			=> 'http://schema.org',
			'@type'				=> 'Blogposting' ,
                        '@id'                           => get_permalink().'/#Blogposting',  
			'mainEntityOfPage'              => saswp_remove_warnings($all_post_meta, 'saswp_blogposting_main_entity_of_page_'.$schema_id, 'saswp_array'),
			'headline'			=> saswp_remove_warnings($all_post_meta, 'saswp_blogposting_headline_'.$schema_id, 'saswp_array'),
			'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_blogposting_description_'.$schema_id, 'saswp_array'),
			'name'				=> saswp_remove_warnings($all_post_meta, 'saswp_blogposting_name_'.$schema_id, 'saswp_array'),
			'url'				=> saswp_remove_warnings($all_post_meta, 'saswp_blogposting_url_'.$schema_id, 'saswp_array'),
			'datePublished'                 => isset($all_post_meta['saswp_blogposting_date_published_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_blogposting_date_published_'.$schema_id][0])):'',
			'dateModified'                  => isset($all_post_meta['saswp_blogposting_date_modified_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_blogposting_date_modified_'.$schema_id][0])):'',
			'author'			=> array(
					'@type' 	=> 'Person',
					'name'		=> saswp_remove_warnings($all_post_meta, 'saswp_blogposting_author_name_'.$schema_id, 'saswp_array')
                                                            ),
			'publisher'			=> array(
				'@type'			=> 'Organization',
				'logo' 			=> array(
					'@type'		=> 'ImageObject',
					'url'		=> saswp_remove_warnings($all_post_meta, 'saswp_blogposting_organization_logo_'.$schema_id, 'saswp_array'),
					'width'		=> saswp_remove_warnings($slogo, 'width', 'saswp_string'),
					'height'	=> saswp_remove_warnings($slogo, 'height', 'saswp_string'),
					),
				'name'			=> saswp_remove_warnings($all_post_meta, 'saswp_blogposting_organization_name_'.$schema_id, 'saswp_array'),
				),
			);
                        
                        
                                 
                            if(isset($all_post_meta['saswp_blogposting_speakable_'.$schema_id]) && $all_post_meta['saswp_blogposting_speakable_'.$schema_id][0] == 1 ){
                               
                                $input1['speakable']['@type'] = 'SpeakableSpecification';
                                $input1['speakable']['xpath'] = array(
                                     "/html/head/title",
                                     "/html/head/meta[@name='description']/@content"
                                );

                            }
                        
                               if(!empty($aggregateRating)){
                                    $input1['aggregateRating'] = $aggregateRating;
                                }                                
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }
                     }
                     
                         if( 'AudioObject' === $schema_type){
                    		                                                    
			$input1 = array(
			'@context'			=> 'http://schema.org',
			'@type'				=> $schema_type,
                        '@id'                           => get_permalink().'/#audioobject',    
			'name'			        => saswp_remove_warnings($all_post_meta, 'saswp_audio_schema_name_'.$schema_id, 'saswp_array'),
			'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_audio_schema_description_'.$schema_id, 'saswp_array'),
			'contentUrl'		        => saswp_remove_warnings($all_post_meta, 'saswp_audio_schema_contenturl_'.$schema_id, 'saswp_array'),
			'duration'		        => saswp_remove_warnings($all_post_meta, 'saswp_audio_schema_duration_'.$schema_id, 'saswp_array'),
                        'encodingFormat'		=> saswp_remove_warnings($all_post_meta, 'saswp_audio_schema_encoding_format_'.$schema_id, 'saswp_array'),
			'datePublished'                 => isset($all_post_meta['saswp_audio_schema_date_published_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_audio_schema_date_published_'.$schema_id][0])):'',
			'dateModified'                  => isset($all_post_meta['saswp_audio_schema_date_modified_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_audio_schema_date_modified_'.$schema_id][0])):'',
			'author'			=> array(
					'@type' 	=> 'Person',
					'name'		=> saswp_remove_warnings($all_post_meta, 'saswp_audio_schema_author_name_'.$schema_id, 'saswp_array')
                                                            ),
			
			   );
                               if(!empty($aggregateRating)){
                                    $input1['aggregateRating'] = $aggregateRating;
                                }                                
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }
                        } 
                        
                         if( 'SoftwareApplication' === $schema_type){
                    		                                                    
			$input1 = array(
			'@context'			=> 'http://schema.org',
			'@type'				=> 'SoftwareApplication',
                        '@id'                           => get_permalink().'/#softwareapplication',     
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
                        
                                if(saswp_remove_warnings($all_post_meta, 'saswp_software_schema_enable_rating_'.$schema_id, 'saswp_array') == 1){   
                                 
                                          $input1['aggregateRating'] = array(
                                                            "@type"       => "AggregateRating",
                                                            "ratingValue" => saswp_remove_warnings($all_post_meta, 'saswp_software_schema_rating_'.$schema_id, 'saswp_array'),
                                                            "reviewCount" => saswp_remove_warnings($all_post_meta, 'saswp_software_schema_rating_count_'.$schema_id, 'saswp_array')
                                                         );                                       
                                }
                        
                               if(!empty($aggregateRating)){
                                    $input1['aggregateRating'] = $aggregateRating;
                                }                                
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }
                        }
			
			 if( 'WebPage' === $schema_type){
                             
				$slogo = get_post_meta( get_the_ID(), 'saswp_webpage_organization_logo_'.$schema_id.'_detail',true);
				$input1 = array(
				'@context'			=> 'http://schema.org',
				'@type'				=> 'WebPage' ,
                                '@id'                           => get_permalink().'/#webpage',     
				'name'				=> saswp_remove_warnings($all_post_meta, 'saswp_webpage_name_'.$schema_id, 'saswp_array'),
				'url'				=> saswp_remove_warnings($all_post_meta, 'saswp_webpage_url_'.$schema_id, 'saswp_array'),
				'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_webpage_description_'.$schema_id, 'saswp_array'),
				'mainEntity'                    => array(
						'@type'			=> 'Article',
						'mainEntityOfPage'	=> saswp_remove_warnings($all_post_meta, 'saswp_webpage_main_entity_of_page_'.$schema_id, 'saswp_array'),
						'image'			=> saswp_remove_warnings($all_post_meta, 'saswp_webpage_image_'.$schema_id, 'saswp_array'),
						'headline'		=> saswp_remove_warnings($all_post_meta, 'saswp_webpage_headline_'.$schema_id, 'saswp_array'),
						'description'		=> saswp_remove_warnings($all_post_meta, 'saswp_webpage_description_'.$schema_id, 'saswp_array'),
						'datePublished' 	=> isset($all_post_meta['saswp_webpage_date_published_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_webpage_date_published_'.$schema_id][0])):'',
						'dateModified'		=> isset($all_post_meta['saswp_webpage_date_modified_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_webpage_date_modified_'.$schema_id][0])):'',
						'author'			=> array(
								'@type' 	=> 'Person',
								'name'		=> saswp_remove_warnings($all_post_meta, 'saswp_webpage_author_name_'.$schema_id, 'saswp_array'), ),
						'publisher'			=> array(
							'@type'			=> 'Organization',
							'logo' 			=> array(
								'@type'		=> 'ImageObject',
								'url'		=> saswp_remove_warnings($all_post_meta, 'saswp_webpage_organization_logo_'.$schema_id, 'saswp_array'),
								'width'		=> saswp_remove_warnings($slogo, 'width', 'saswp_string'),
								'height'	=> saswp_remove_warnings($slogo, 'height', 'saswp_string'),
								),
							'name'			=> saswp_remove_warnings($all_post_meta, 'saswp_webpage_organization_name_'.$schema_id, 'saswp_array'),
						),
					),
					
				
				);
                                
                                
                                if(isset($all_post_meta['saswp_webpage_speakable_'.$schema_id]) && $all_post_meta['saswp_webpage_speakable_'.$schema_id][0] == 1){

                                    $input1['speakable']['@type'] = 'SpeakableSpecification';
                                    $input1['speakable']['xpath'] = array(
                                         "/html/head/title",
                                         "/html/head/meta[@name='description']/@content"
                                    );

                                }
                            
                                if(!empty($aggregateRating)){
                                    
                                    $input1['mainEntity']['aggregateRating'] = $aggregateRating;
                                    
                                }
                                
                                if(!empty($extra_theme_review)){
                                    
                                   $input1 = array_merge($input1, $extra_theme_review);
                                   
                                }
			}
			
			 if( 'Article' === $schema_type ){
                             
                             $slogo = get_post_meta( get_the_ID(), 'saswp_article_organization_logo_'.$schema_id.'_detail',true);
                             
				$input1 = array(
					'@context'			=> 'http://schema.org',
					'@type'				=> 'Article',
                                        '@id'                           => get_permalink().'/#article',
					'mainEntityOfPage'              => saswp_remove_warnings($all_post_meta, 'saswp_article_main_entity_of_page_'.$schema_id, 'saswp_array'),
					'image'				=> saswp_remove_warnings($all_post_meta, 'saswp_article_image_'.$schema_id, 'saswp_array'),
					'headline'			=> saswp_remove_warnings($all_post_meta, 'saswp_article_headline_'.$schema_id, 'saswp_array'),
					'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_article_description_'.$schema_id, 'saswp_array'),
					'datePublished'                 => isset($all_post_meta['saswp_article_date_published_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_article_date_published_'.$schema_id][0])):'',
					'dateModified'                  => isset($all_post_meta['saswp_article_date_modified_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_article_date_modified_'.$schema_id][0])):'',
					'author'			=> array(
							'@type' 	=> 'Person',
							'name'		=> saswp_remove_warnings($all_post_meta, 'saswp_article_author_name_'.$schema_id, 'saswp_array') 
                                                         ),
					'publisher'			=> array(
						'@type'			=> 'Organization',
						'logo' 			=> array(
							'@type'		=> 'ImageObject',
							'url'		=> saswp_remove_warnings($all_post_meta, 'saswp_article_organization_logo_'.$schema_id, 'saswp_array'),
							'width'		=> saswp_remove_warnings($slogo, 'width', 'saswp_string'),
							'height'	=> saswp_remove_warnings($slogo, 'height', 'saswp_string'),
							),
						'name'			=> saswp_remove_warnings($all_post_meta, 'saswp_article_organization_name_'.$schema_id, 'saswp_array'),
					),
                                    
				);
                                
                                if(isset($all_post_meta['saswp_article_speakable_'.$schema_id]) && $all_post_meta['saswp_article_speakable_'.$schema_id][0] == 1){

                                $input1['speakable']['@type'] = 'SpeakableSpecification';
                                $input1['speakable']['xpath'] = array(
                                     "/html/head/title",
                                     "/html/head/meta[@name='description']/@content"
                                );

                               }
                                
                                if(!empty($extra_theme_review)){
                                    
                                   $input1 = array_merge($input1, $extra_theme_review);
                                   
                                }
			}
                        
                         if( 'TechArticle' === $schema_type ){
                             
                             $slogo = get_post_meta( get_the_ID(), 'saswp_tech_article_organization_logo_'.$schema_id.'_detail',true);
                             
				$input1 = array(
					'@context'			=> 'http://schema.org',
					'@type'				=> 'TechArticle',
                                        '@id'                           => get_permalink().'/#techarticle',
					'mainEntityOfPage'              => saswp_remove_warnings($all_post_meta, 'saswp_tech_article_main_entity_of_page_'.$schema_id, 'saswp_array'),
					'image'				=> saswp_remove_warnings($all_post_meta, 'saswp_tech_article_image_'.$schema_id, 'saswp_array'),
					'headline'			=> saswp_remove_warnings($all_post_meta, 'saswp_tech_article_headline_'.$schema_id, 'saswp_array'),
					'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_tech_article_description_'.$schema_id, 'saswp_array'),
					'datePublished'                 => isset($all_post_meta['saswp_tech_article_date_published_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_tech_article_date_published_'.$schema_id][0])):'',
					'dateModified'                  => isset($all_post_meta['saswp_tech_article_date_modified_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_tech_article_date_modified_'.$schema_id][0])):'',
					'author'			=> array(
							'@type' 	=> 'Person',
							'name'		=> saswp_remove_warnings($all_post_meta, 'saswp_tech_article_author_name_'.$schema_id, 'saswp_array') 
                                                         ),
					'publisher'			=> array(
						'@type'			=> 'Organization',
						'logo' 			=> array(
							'@type'		=> 'ImageObject',
							'url'		=> saswp_remove_warnings($all_post_meta, 'saswp_tech_article_organization_logo_'.$schema_id, 'saswp_array'),
							'width'		=> saswp_remove_warnings($slogo, 'width', 'saswp_string'),
							'height'	=> saswp_remove_warnings($slogo, 'height', 'saswp_string'),
							),
						'name'			=> saswp_remove_warnings($all_post_meta, 'saswp_tech_article_organization_name_'.$schema_id, 'saswp_array'),
					),
                                    
				); 
                                
                                if(isset($all_post_meta['saswp_tech_article_speakable_'.$schema_id]) && $all_post_meta['saswp_tech_article_speakable_'.$schema_id][0] == 1){

                                $input1['speakable']['@type'] = 'SpeakableSpecification';
                                $input1['speakable']['xpath'] = array(
                                     "/html/head/title",
                                     "/html/head/meta[@name='description']/@content"
                                );

                                }
                                if(!empty($extra_theme_review)){
                                    
                                   $input1 = array_merge($input1, $extra_theme_review);
                                   
                                }
			}
	
			 if( 'Recipe' === $schema_type){
                             
				$recipe_logo    = get_post_meta( get_the_ID(), 'saswp_article_organization_logo_'.$schema_id.'_detail',true);
                                $recipe_image   = get_post_meta( get_the_ID(), 'saswp_recipe_author_image_'.$schema_id.'_detail',true);
                                
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
				'@context'			=> 'http://schema.org',
				'@type'				=> $schema_type ,
                                '@id'                           => get_permalink().'/#recipe',    
				'url'				=> saswp_remove_warnings($all_post_meta, 'saswp_recipe_url_'.$schema_id, 'saswp_array'),
				'name'			        => saswp_remove_warnings($all_post_meta, 'saswp_recipe_name_'.$schema_id, 'saswp_array'),
                                'author'			=> array(
								'@type' 	=> 'Person',
								'name'		=> saswp_remove_warnings($all_post_meta, 'saswp_recipe_author_name_'.$schema_id, 'saswp_array'),
								'Image'		=> array(
									'@type'			=> 'ImageObject',
									'url'			=> saswp_remove_warnings($all_post_meta, 'saswp_recipe_author_image_'.$schema_id, 'saswp_array'),
									'height'		=> saswp_remove_warnings($recipe_image, 'height', 'saswp_string'),
									'width'			=> saswp_remove_warnings($recipe_image, 'width', 'saswp_string')
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
                                'video'                          => array(
                                                                        'name'         => saswp_remove_warnings($all_post_meta, 'saswp_recipe_video_name_'.$schema_id, 'saswp_array'),
                                                                        'description'  => saswp_remove_warnings($all_post_meta, 'saswp_recipe_video_description_'.$schema_id, 'saswp_array'),
                                                                        'thumbnailUrl' => saswp_remove_warnings($all_post_meta, 'saswp_recipe_video_thumbnailurl_'.$schema_id, 'saswp_array'),  
                                                                        'contentUrl'   => saswp_remove_warnings($all_post_meta, 'saswp_recipe_video_contenturl_'.$schema_id, 'saswp_array'),  
                                                                        'embedUrl'     => saswp_remove_warnings($all_post_meta, 'saswp_recipe_video_embedurl_'.$schema_id, 'saswp_array'),  
                                                                        'uploadDate'   => isset($all_post_meta['saswp_recipe_video_upload_date_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_recipe_video_upload_date_'.$schema_id][0])):'',
                                                                        'duration'     => saswp_remove_warnings($all_post_meta, 'saswp_recipe_video_duration_'.$schema_id, 'saswp_array'),                                                                 
                                                                 ),                                                                                                             
                                    
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
                                if(!empty($aggregateRating)){
                                    $input1['aggregateRating'] = $aggregateRating;
                                }                                
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }
			}
						
			 if( 'Product' === $schema_type){				

                                        $product_image = get_post_meta( get_the_ID(), 'saswp_product_schema_image_'.$schema_id.'_detail',true);                                                                           
                                        $input1 = array(
                                        '@context'			=> 'http://schema.org',
                                        '@type'				=> 'Product',
                                        '@id'                           => get_permalink().'/#product',    
                                        'url'				=> get_permalink(),
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
                                                                                'url'             => get_permalink(),
                                                                                'priceValidUntil' => isset($all_post_meta['saswp_product_schema_priceValidUntil_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_product_schema_priceValidUntil_'.$schema_id][0])):'',
                                                                                ), 
                                        'brand'                         => array('@type' => 'Thing',
                                                                                 'name'  => saswp_remove_warnings($all_post_meta, 'saswp_product_schema_brand_name_'.$schema_id, 'saswp_array'),
                                                                                )    
                                        ); 
                                        
                                        if(isset($all_post_meta['saswp_product_schema_gtin8_'.$schema_id])){
                                            $input1['gtin8'] = esc_attr($all_post_meta['saswp_product_schema_gtin8_'.$schema_id][0]);  
                                        }
                                        if(isset($all_post_meta['saswp_product_schema_mpn_'.$schema_id])){
                                          $input1['mpn'] = esc_attr($all_post_meta['saswp_product_schema_mpn_'.$schema_id][0]);  
                                        }
                                        if(isset($all_post_meta['saswp_product_schema_isbn_'.$schema_id])){
                                          $input1['isbn'] = esc_attr($all_post_meta['saswp_product_schema_isbn_'.$schema_id][0]);  
                                        }   
                                        
                                        if(saswp_remove_warnings($all_post_meta, 'saswp_product_schema_enable_rating_'.$schema_id, 'saswp_array') == 1 && saswp_remove_warnings($all_post_meta, 'saswp_product_schema_rating_'.$schema_id, 'saswp_array') && saswp_remove_warnings($all_post_meta, 'saswp_product_schema_review_count_'.$schema_id, 'saswp_array')){   
                                 
                                          $input1['aggregateRating'] = array(
                                                            "@type"       => "AggregateRating",
                                                            "ratingValue" => saswp_remove_warnings($all_post_meta, 'saswp_product_schema_rating_'.$schema_id, 'saswp_array'),
                                                            "reviewCount" => saswp_remove_warnings($all_post_meta, 'saswp_product_schema_review_count_'.$schema_id, 'saswp_array')
                                                         );                                       
                                         }
                                                                                
                                        if(!empty($aggregateRating)){
                                            $input1['aggregateRating'] = $aggregateRating;
                                        }                                        
                                        if(!empty($extra_theme_review)){
                                           $input1 = array_merge($input1, $extra_theme_review);
                                        }  
                                        
                                        $service = new saswp_output_service();
                                        $product_details = $service->saswp_woocommerce_product_details(get_the_ID());  


                                        if(!empty($product_details['product_reviews'])){
                                      
                                        $reviews = array();
                                      
                                         foreach ($product_details['product_reviews'] as $review){
                                          
                                          $reviews[] = array(
                                                                        '@type'	=> 'Review',
                                                                        'author'	=> esc_attr($review['author']),
                                                                        'datePublished'	=> esc_html($review['datePublished']),
                                                                        'description'	=> $review['description'],  
                                                                        'reviewRating'  => array(
                                                                                '@type'	=> 'Rating',
                                                                                'bestRating'	=> '5',
                                                                                'ratingValue'	=> esc_attr($review['reviewRating']),
                                                                                'worstRating'	=> '1',
                                                                        )  
                                          );
                                          
                                      }
                                      $input1['review'] =  $reviews;
                                  }
                                        
                                        
			}
                        
                         if( 'NewsArticle' === $schema_type ){  
                             
                                $slogo = get_post_meta( get_the_ID(), 'saswp_newsarticle_organization_logo_'.$schema_id.'_detail',true);
                                $author_image = get_post_meta( get_the_ID(), 'saswp_newsarticle_author_image_'.$schema_id.'_detail',true);
                             
				        $input1 = array(
					'@context'			=> 'http://schema.org',
					'@type'				=> 'NewsArticle' ,
                                        '@id'                           => get_permalink().'/#newsarticle',    
					'mainEntityOfPage'              => saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_main_entity_of_page_'.$schema_id, 'saswp_array'),
					'url'				=> saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_URL_'.$schema_id, 'saswp_array'),
					'headline'			=> saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_headline_'.$schema_id, 'saswp_array'),
					'datePublished'                 => isset($all_post_meta['saswp_newsarticle_date_published_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_newsarticle_date_published_'.$schema_id][0])):'',
					'dateModified'                  => isset($all_post_meta['saswp_newsarticle_date_modified_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_newsarticle_date_modified_'.$schema_id][0])):'',
					'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_description_'.$schema_id, 'saswp_array'),
                                        'articleSection'                => saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_section_'.$schema_id, 'saswp_array'),
                                        'articleBody'                   => saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_body_'.$schema_id, 'saswp_array'),     
					'name'				=> saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_name_'.$schema_id, 'saswp_array'), 					
					'thumbnailUrl'                  => saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_thumbnailurl_'.$schema_id, 'saswp_array'),
                                        'wordCount'                     => saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_word_count_'.$schema_id, 'saswp_array'),
                                        'timeRequired'                  => saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_timerequired_'.$schema_id, 'saswp_array'),    
					'mainEntity'                    => array(
                                                                            '@type' => 'WebPage',
                                                                            '@id'   => saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_main_entity_id_'.$schema_id, 'saswp_array'),
						), 
					'author'			=> array(
							'@type' 			=> 'Person',
							'name'				=> saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_author_name_'.$schema_id, 'saswp_array'),
							'Image'				=> array(
							'@type'				=> 'ImageObject',
							'url'				=> saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_author_image_'.$schema_id, 'saswp_array'),
							'height'			=> saswp_remove_warnings($author_image, 'height', 'saswp_string'),
							'width'				=> saswp_remove_warnings($author_image, 'width', 'saswp_string')
										),
							),
					'publisher'			=> array(
							'@type'				=> 'Organization',
							'logo' 				=> array(
							'@type'				=> 'ImageObject',
							'url'				=> saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_organization_logo_'.$schema_id, 'saswp_array'),
							'width'				=> saswp_remove_warnings($slogo, 'width', 'saswp_string'),
							'height'			=> saswp_remove_warnings($slogo, 'height', 'saswp_string'),
										),
							'name'				=> saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_organization_name_'.$schema_id, 'saswp_array'),
							),
					);
                                        
                                            if(isset($all_post_meta['saswp_newsarticle_speakable_'.$schema_id]) && $all_post_meta['saswp_newsarticle_speakable_'.$schema_id][0] == 1){

                                                $input1['speakable']['@type'] = 'SpeakableSpecification';
                                                $input1['speakable']['xpath'] = array(
                                                     "/html/head/title",
                                                     "/html/head/meta[@name='description']/@content"
                                                );

                                             }
                                                if(!empty($aggregateRating)){
                                                    $input1['aggregateRating'] = $aggregateRating;
                                                }                                                
				}
			
			 if( 'VideoObject' === $schema_type){
				
                                $slogo = get_post_meta( get_the_ID(), 'saswp_video_object_organization_logo_'.$schema_id.'_detail',true);
                                $author_image = get_post_meta( get_the_ID(), 'saswp_video_object_author_image_'.$schema_id.'_detail',true);
                             
						$input1 = array(
						'@context'			=> 'http://schema.org',
						'@type'				=> 'VideoObject',
                                                '@id'                           => get_permalink().'/#videoobject',    
						'url'				=> saswp_remove_warnings($all_post_meta, 'saswp_video_object_url_'.$schema_id, 'saswp_array'),
						'headline'			=> saswp_remove_warnings($all_post_meta, 'saswp_video_object_headline_'.$schema_id, 'saswp_array'),
						'datePublished'                 => isset($all_post_meta['saswp_video_object_date_published_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_video_object_date_published_'.$schema_id][0])):'',
						'dateModified'                  => isset($all_post_meta['saswp_video_object_date_modified_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_video_object_date_modified_'.$schema_id][0])):'',
						'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_video_object_description_'.$schema_id, 'saswp_array'),
						'name'				=> saswp_remove_warnings($all_post_meta, 'saswp_video_object_name_'.$schema_id, 'saswp_array'),
						'uploadDate'                    => isset($all_post_meta['saswp_video_object_upload_date_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_video_object_upload_date_'.$schema_id][0])):'',
						'thumbnailUrl'                  => saswp_remove_warnings($all_post_meta, 'saswp_video_object_thumbnail_url_'.$schema_id, 'saswp_array'),
                                                'contentUrl'                    => saswp_remove_warnings($all_post_meta, 'saswp_video_object_content_url_'.$schema_id, 'saswp_array'),
                                                'embedUrl'                      => saswp_remove_warnings($all_post_meta, 'saswp_video_object_embed_url_'.$schema_id, 'saswp_array'),
						'mainEntity'                    => array(
								'@type'				=> 'WebPage',
								'@id'				=> saswp_remove_warnings($all_post_meta, 'saswp_video_object_main_entity_id_'.$schema_id, 'saswp_array'),
								), 
						'author'			=> array(
								'@type' 			=> 'Person',
								'name'				=> saswp_remove_warnings($all_post_meta, 'saswp_video_object_author_name_'.$schema_id, 'saswp_array'),
								'Image'				=> array(
								'@type'				=> 'ImageObject',
								'url'				=> saswp_remove_warnings($all_post_meta, 'saswp_video_object_author_image_'.$schema_id, 'saswp_array'),
								'height'			=> saswp_remove_warnings($author_image, 'height', 'saswp_string'),
								'width'				=> saswp_remove_warnings($author_image, 'width', 'saswp_string')
								),
							),
						'publisher'			=> array(
								'@type'				=> 'Organization',
								'logo' 				=> array(
								'@type'				=> 'ImageObject',
								'url'				=> saswp_remove_warnings($all_post_meta, 'saswp_video_object_organization_logo_'.$schema_id, 'saswp_array'),
								'width'				=> saswp_remove_warnings($slogo, 'width', 'saswp_string'),
								'height'			=> saswp_remove_warnings($slogo, 'height', 'saswp_string'),
										),
								'name'                          => saswp_remove_warnings($all_post_meta, 'saswp_video_object_organization_name_'.$schema_id, 'saswp_array'),
							),
						);
                                                if(!empty($aggregateRating)){
                                                    $input1['aggregateRating'] = $aggregateRating;
                                                }                                                
                                                if(!empty($extra_theme_review)){
                                                    $input1 = array_merge($input1, $extra_theme_review);
                                                }
					
				}
                        
                         if( 'Service' === $schema_type ){  
                             
                                $area_served_str = saswp_remove_warnings($all_post_meta, 'saswp_service_schema_area_served_'.$schema_id, 'saswp_array');
                                $area_served_arr = explode(',', $area_served_str);
                                                                
                                $service_offer_str = saswp_remove_warnings($all_post_meta, 'saswp_service_schema_service_offer_'.$schema_id, 'saswp_array');
                                $service_offer_arr = explode(',', $service_offer_str);
                                
				$input1 = array(
					'@context'			=> 'http://schema.org',
					'@type'				=> $schema_type ,
                                        '@id'                           => get_permalink().'/#service',    
                                        'name'				=> saswp_remove_warnings($all_post_meta, 'saswp_service_schema_name_'.$schema_id, 'saswp_array'), 
					'serviceType'                   => saswp_remove_warnings($all_post_meta, 'saswp_service_schema_type_'.$schema_id, 'saswp_array'),
					'provider'                      => array(
                                                                        '@type' => saswp_remove_warnings($all_post_meta, 'saswp_service_schema_provider_type_'.$schema_id, 'saswp_array'),
                                                                        'name'  => saswp_remove_warnings($all_post_meta, 'saswp_service_schema_provider_name_'.$schema_id, 'saswp_array'),
                                                                        'image' => saswp_remove_warnings($all_post_meta, 'saswp_service_schema_image_'.$schema_id, 'saswp_array'),
                                                                        '@id'   => get_permalink(),
                                                                        'address' => array(
                                                                            '@type'           => 'PostalAddress',
                                                                            'addressLocality' => saswp_remove_warnings($all_post_meta, 'saswp_service_schema_locality_'.$schema_id, 'saswp_array'),
                                                                            'postalCode'      => saswp_remove_warnings($all_post_meta, 'saswp_service_schema_postal_code_'.$schema_id, 'saswp_array'),  
                                                                            'telephone'       => saswp_remove_warnings($all_post_meta, 'saswp_service_schema_telephone_'.$schema_id, 'saswp_array')
                                                                        ),
                                                                        'priceRange'         => saswp_remove_warnings($all_post_meta, 'saswp_service_schema_price_range_'.$schema_id, 'saswp_array'),                                                                        
                                                                        ),                                        										                                                                     
					'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_service_schema_description_'.$schema_id, 'saswp_array'),
                                        ); 
                                        $areaServed = array();
                                        foreach($area_served_arr as $area){
                                            $areaServed[] = array(
                                                '@type' => 'City',
                                                'name'  => $area
                                            );
                                        }
                                        $serviceOffer = array();
                                        foreach($service_offer_arr as $offer){
                                            $serviceOffer[] = array(
                                                '@type' => 'Offer',
                                                'name'  => $offer
                                            );
                                        }
                                       $input1['areaServed'] = $areaServed;
                                       $input1['hasOfferCatalog'] = array(
                                           '@type'            => 'OfferCatalog',
                                            'name'            => saswp_remove_warnings($all_post_meta, 'saswp_service_schema_name_'.$schema_id, 'saswp_array'),
                                            'itemListElement' => $serviceOffer
                                       );
                                
                                if(saswp_remove_warnings($all_post_meta, 'saswp_service_schema_enable_rating_'.$schema_id, 'saswp_array') == 1){                                                                        
                                          $input1['aggregateRating'] = array(
                                                            "@type"       => "AggregateRating",
                                                            "ratingValue" => saswp_remove_warnings($all_post_meta, 'saswp_service_schema_rating_'.$schema_id, 'saswp_array'),
                                                            "reviewCount" => saswp_remove_warnings($all_post_meta, 'saswp_service_schema_review_count_'.$schema_id, 'saswp_array')
                                                         );                                       
                                         }       
                                if(!empty($aggregateRating)){
                                    $input1['aggregateRating'] = $aggregateRating;
                                }                                
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }
                         }     
                         
                         if( 'Review' === $schema_type ){   
                             
                                 if(isset($sd_data['saswp-tagyeem']) && $sd_data['saswp-tagyeem'] == 1 && (is_plugin_active('taqyeem/taqyeem.php') || get_template() != 'jannah')){
                                     
                                     remove_action( 'TieLabs/after_post_entry',  'tie_article_schemas' );
                                     
                                 }                                                                                                                                                              
                             
                                $service = new saswp_output_service();
                                
                                $review_author = get_the_author();
                                
                                if(isset($all_post_meta['saswp_review_schema_author_'.$schema_id])){
                                    
                                   $review_author = $all_post_meta['saswp_review_schema_author_'.$schema_id][0];  
                                   
                                }
                                
				$input1['@context']                     = 'http://schema.org';
                                $input1['@type']                        = 'Review';
                                $input1['@id']                          = get_permalink().'/#review';                                                           
                                $input1['url']                          = get_permalink();                                
                                $input1['datePublished']                = get_the_date("Y-m-d\TH:i:s\Z");
                                $input1['dateModified']                 = get_the_modified_date("Y-m-d\TH:i:s\Z");
                                
                                if($review_author){
                                    
                                $input1['author']['@type']              = 'Person';      
                                $input1['author']['name']               = esc_attr($review_author);
                                
                                if(isset($all_post_meta['saswp_review_schema_author_sameas_'.$schema_id])){
                                    
                                 $input1['author']['sameAs']            = esc_url($all_post_meta['saswp_review_schema_author_sameas_'.$schema_id][0]);   
                                 
                                }                                
                                
                                }
                                
                                if($site_name && $logo && $width && $height){
                                    
                                $input1['publisher']['@type']           = 'Organization';
                                $input1['publisher']['name']            = esc_attr($site_name);
                                $input1['publisher']['logo']['@type']   = 'ImageObject';
                                $input1['publisher']['logo']['url']     = esc_url($logo);
                                $input1['publisher']['logo']['width']   = esc_attr($width);
                                $input1['publisher']['logo']['height']  = esc_attr($height);       
                                    
                                }
                                                                
                                if(isset($all_post_meta['saswp_review_schema_description_'.$schema_id])){
                                    
                                    $input1['reviewBody']               = $all_post_meta['saswp_review_schema_description_'.$schema_id][0];
                                    $input1['description']              = $all_post_meta['saswp_review_schema_description_'.$schema_id][0];
                                }else {
                                    $input1['reviewBody']               = strip_tags(strip_shortcodes(get_the_excerpt()));
                                    $input1['description']              = strip_tags(strip_shortcodes(get_the_excerpt()));
                                }
                                
                                if(isset($all_post_meta['saswp_review_schema_item_type_'.$schema_id])){
                                   $input1['itemReviewed']['@type'] = esc_attr($all_post_meta['saswp_review_schema_item_type_'.$schema_id][0]);   
                                }
                                if(isset($all_post_meta['saswp_review_schema_name_'.$schema_id])){
                                   $input1['itemReviewed']['name'] = esc_attr($all_post_meta['saswp_review_schema_name_'.$schema_id][0]);
                                }
                                if(isset($all_post_meta['saswp_review_schema_url_'.$schema_id])){
                                    $input1['itemReviewed']['url'] = esc_url($all_post_meta['saswp_review_schema_url_'.$schema_id][0]);
                                }
                                                                                               
                                if(isset($all_post_meta['saswp_review_schema_price_range_'.$schema_id])){
                                    
                                    $input1['itemReviewed']['priceRange']     = esc_attr($all_post_meta['saswp_review_schema_price_range_'.$schema_id][0]);
                                    
                                }
                                
                                if(isset($all_post_meta['saswp_review_schema_telephone_'.$schema_id])){
                                    
                                    $input1['itemReviewed']['telephone']     = esc_attr($all_post_meta['saswp_review_schema_telephone_'.$schema_id][0]);
                                    
                                }
                                
                                if(isset($all_post_meta['saswp_review_schema_servescuisine_'.$schema_id])){
                                    
                                    $input1['itemReviewed']['servesCuisine']     = esc_attr($all_post_meta['saswp_review_schema_servescuisine_'.$schema_id][0]);
                                    
                                }
                                
                                if(isset($all_post_meta['saswp_review_schema_menu_'.$schema_id])){
                                    
                                    $input1['itemReviewed']['hasMenu']     = esc_url($all_post_meta['saswp_review_schema_menu_'.$schema_id][0]);
                                    
                                }
                                
                                if(isset($all_post_meta['saswp_review_schema_itemreviewed_sameas_'.$schema_id])){
                                    
                                 $input1['itemReviewed']['sameAs']   = esc_url($all_post_meta['saswp_review_schema_itemreviewed_sameas_'.$schema_id][0]);   
                                 
                                }
                                
                                
                                if(isset($all_post_meta['saswp_review_schema_director_'.$schema_id])){
                                    
                                 $input1['itemReviewed']['director']   = esc_attr($all_post_meta['saswp_review_schema_director_'.$schema_id][0]);   
                                 
                                }
                                if(isset($all_post_meta['saswp_review_schema_date_created_'.$schema_id])){
                                    
                                 $input1['itemReviewed']['dateCreated']   = isset($all_post_meta['saswp_review_schema_date_created_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_review_schema_date_created_'.$schema_id][0])):'';
                                 
                                }
                                
                                $review_image   = get_post_meta( get_the_ID(), 'saswp_review_schema_image_'.$schema_id.'_detail',true);
                                
                                if(!empty($review_image)){
                                                                                                                                              
                                    $input1['itemReviewed']['image']['@type']  = 'ImageObject';
                                    $input1['itemReviewed']['image']['url']    = esc_url($review_image['thumbnail']);
                                    $input1['itemReviewed']['image']['width']  = esc_attr($review_image['width']);
                                    $input1['itemReviewed']['image']['height'] = esc_attr($review_image['height']);
                                    
                                }
                                                                
                                if(saswp_remove_warnings($all_post_meta, 'saswp_review_schema_street_address_'.$schema_id, 'saswp_array') !='' || saswp_remove_warnings($all_post_meta, 'saswp_review_schema_locality_'.$schema_id, 'saswp_array') !=''){
                                   
                                    
                                    $input1['itemReviewed']['address']['@type']           = 'PostalAddress';
                                    $input1['itemReviewed']['address']['streetAddress']   = saswp_remove_warnings($all_post_meta, 'saswp_review_schema_street_address_'.$schema_id, 'saswp_array');
                                    $input1['itemReviewed']['address']['addressLocality'] = saswp_remove_warnings($all_post_meta, 'saswp_review_schema_locality_'.$schema_id, 'saswp_array');
                                    $input1['itemReviewed']['address']['addressRegion']   = saswp_remove_warnings($all_post_meta, 'saswp_review_schema_region_'.$schema_id, 'saswp_array');
                                    $input1['itemReviewed']['address']['postalCode']      = saswp_remove_warnings($all_post_meta, 'saswp_review_schema_postal_code_'.$schema_id, 'saswp_array');
                                    $input1['itemReviewed']['address']['addressCountry']  = saswp_remove_warnings($all_post_meta, 'saswp_review_schema_country_'.$schema_id, 'saswp_array');
                                                                        
                                }
                                                                                                                                   
                                switch ($all_post_meta['saswp_review_schema_item_type_'.$schema_id][0]) {
                                
                                case 'Article':
                                    
                                    $markup = $service->saswp_schema_markup_generator($all_post_meta['saswp_review_schema_item_type_'.$schema_id][0]);                                    
                                    $input1['itemReviewed'] = $markup;
                                    
                                    break;
                                case 'Adultentertainment':
                                    $input1 = $input1;
                                    break;
                                case 'Blog':
                                    $input1 = $input1;
                                    break;
                                case 'Book':
                                    
                                    if(isset($all_post_meta['saswp_review_schema_isbn_'.$schema_id])){
                                        
                                        $input1['itemReviewed']['isbn'] = $all_post_meta['saswp_review_schema_isbn_'.$schema_id];
                                                
                                    }
                                    if($review_author)   {
                                        
                                        $input1['itemReviewed']['author']['@type']              = 'Person';      
                                        $input1['itemReviewed']['author']['name']               = esc_attr($review_author);
                                        $input1['itemReviewed']['author']['sameAs']             = esc_url($all_post_meta['saswp_review_schema_author_sameas_'.$schema_id][0]);   
                                    
                                    }  
                                                                        
                                    break;
                                case 'Casino':
                                    break;
                                case 'Diet':
                                    break;
                                case 'Episode':
                                    break;
                                case 'ExercisePlan':
                                    break;
                                case 'Game':
                                    break;
                                case 'Movie':                                                                       
                                    
                                    if($review_author){
                                    
                                        $input1['author']['sameAs']   = get_permalink();
                                        
                                    }                                    
                                    
                                    break;
                                case 'MusicPlaylist':
                                    break;
                                case 'MusicRecording':
                                    break;
                                case 'Photograph':
                                    break;
                                case 'Recipe':
                                    break;
                                case 'Restaurant':
                                    break;
                                case 'Series':
                                    break;
                                case 'SoftwareApplication':
                                    break;
                                case 'VisualArtwork':
                                    break;
                                case 'WebPage': 
                                    
                                    $markup = $service->saswp_schema_markup_generator($all_post_meta['saswp_review_schema_item_type_'.$schema_id][0]);                                                                       
                                    $input1['itemReviewed'] = $markup;
                                    
                                    break;
                                case 'WebSite':
                                    break;

                                default:
                                    $input1 = $input1;
                                 break;
                                }                                                                                                                                                                                                
                                
                                if(saswp_remove_warnings($all_post_meta, 'saswp_review_schema_enable_rating_'.$schema_id, 'saswp_array') == 1){   
                                 
                                          $input1['reviewRating'] = array(
                                                            "@type"        => "Rating",
                                                            "ratingValue"  => saswp_remove_warnings($all_post_meta, 'saswp_review_schema_rating_'.$schema_id, 'saswp_array'),
                                                            "bestRating"   => saswp_remove_warnings($all_post_meta, 'saswp_review_schema_review_count_'.$schema_id, 'saswp_array')
                                                         );                                       
                                         }  
                                         unset($input1['aggregateRating']);                                         
                                                                                                
				}    
                                
                         if( 'local_business' === $schema_type){
                             
                                $operation_days      = explode( "rn", esc_html( stripslashes(saswp_remove_warnings($all_post_meta, 'saswp_dayofweek_'.$schema_id, 'saswp_array'))) );;                               
                                $business_sub_name   = '';
                                $business_type       = saswp_remove_warnings($all_post_meta, 'saswp_business_type_'.$schema_id, 'saswp_array'); 
                                $post_specific_obj   = new saswp_post_specific();
                                $check_business_type = $post_specific_obj->saswp_get_sub_business_array($business_type);
                                
                                if(!empty($check_business_type)){
                                    
                                 $business_sub_name = saswp_remove_warnings($all_post_meta, 'saswp_business_name_'.$schema_id, 'saswp_array');   
                                 
                                }
                                
                                if($business_sub_name){
                                    
                                $local_business = $business_sub_name; 
                                
                                }else{
                                    
                                $local_business = $business_type;        
                                
                                }   
                                
                                $local_image = get_post_meta( get_the_ID(), 'local_business_logo_'.$schema_id.'_detail',true);
                                
				$input1 = array(
				'@context'			=> 'http://schema.org',
				'@type'				=> $local_business ,
                                '@id'                           => get_permalink().'/#'. strtolower($local_business),        
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
                                    
                                
                                
                                    if(isset($all_post_meta['local_enable_rating_'.$schema_id]) && saswp_remove_warnings($all_post_meta, 'local_rating_'.$schema_id, 'saswp_array') && saswp_remove_warnings($all_post_meta, 'local_review_count_'.$schema_id, 'saswp_array')){
                                 
                                          $input1['aggregateRating'] = array(
                                                            "@type"       => "AggregateRating",
                                                            "ratingValue" => saswp_remove_warnings($all_post_meta, 'local_rating_'.$schema_id, 'saswp_array'),
                                                            "reviewCount" => saswp_remove_warnings($all_post_meta, 'local_review_count_'.$schema_id, 'saswp_array')
                                                         );                                       
                                         } 
                                
                                    if(!empty($aggregateRating)){
                                       $input1['aggregateRating'] = $aggregateRating;
                                    }                                    
                                    if(!empty($extra_theme_review)){
                                       $input1 = array_merge($input1, $extra_theme_review);
                                    }
                                    if(isset($all_post_meta['local_price_range_'.$schema_id][0])){
                                       $input1['priceRange'] = esc_attr($all_post_meta['local_price_range_'.$schema_id][0]);   
                                    }
                                    
                                    if(isset($all_post_meta['local_accepts_reservations_'.$schema_id][0])){
                                      $input1['acceptsReservations'] = esc_attr($all_post_meta['local_price_accepts_reservations_'.$schema_id][0]);   
                                    }
                                    
                                    if(isset($all_post_meta['local_serves_cuisine_'.$schema_id][0])){
                                      $input1['servesCuisine'] = esc_attr($all_post_meta['local_serves_cuisine_'.$schema_id][0]);   
                                    }
                                    
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
                                    
                                    
                                    
                                    
			}
                        
                        
                         if($schema_type != 'Review'){
                            
                            //kk star rating 
                        
                            $kkstar_aggregateRating = saswp_extract_kk_star_ratings();

                            if(!empty($kkstar_aggregateRating)){
                                $input1['aggregateRating'] = $kkstar_aggregateRating; 
                            }

                            //wp post-rating star rating 

                            $wp_post_rating_ar = saswp_extract_wp_post_ratings();

                            if(!empty($wp_post_rating_ar)){
                                $input1['aggregateRating'] = $wp_post_rating_ar; 
                            }
                            
                            
                        }
                        
                                		                        			                        
                         if( !empty($input1) && !isset($input1['image'])){
                             
                             $service_object     = new saswp_output_service();
                             $input2             = $service_object->saswp_get_fetaure_image();
                             
                             if(!empty($input2)){
                                 
                               $input1 = array_merge($input1,$input2); 
                               
                             }                                                                    
                        }
                        
                            $input1 = apply_filters('saswp_modify_woocommerce_membership_schema', $input1);
                        
                if(!empty($input1)){
                    
                   $all_schema_output[] = $input1;
                   
                }                
             }
           }   
        }
        
        return apply_filters('saswp_modify_schema_output', $all_schema_output);        
}

/**
 * Function generates breadcrumbs schema markup
 * @global type $sd_data
 * @param type $sd_data
 * @return type
 */
function saswp_schema_breadcrumb_output(){
    
	global $sd_data;        
        
	if(isset($sd_data['saswp_breadcrumb_schema']) && $sd_data['saswp_breadcrumb_schema'] == 1){
				       				
        if(is_single() || is_page() ||is_archive()){
            
        $bread_crumb_list =   saswp_list_items_generator();  
        
        if(!empty($bread_crumb_list)){   
            
                $input = array(
					'@context'			=> 'http://schema.org',
					'@type'				=> 'BreadcrumbList' ,
                                        '@id'				=>  get_permalink().'#breadcrumb' ,
					'itemListElement'	        => $bread_crumb_list,
			); 
           
                return apply_filters('saswp_modify_breadcrumb_output', $input);  
         
             }
               
           }         
	
	}
}

/**
 * Function generates website schema markup
 * @return type json
 */
function saswp_kb_website_output(){
    	        
                global $sd_data;
                
                $input = array();
                
                if(isset($sd_data['saswp_website_schema']) && $sd_data['saswp_website_schema'] == 1 || !isset($sd_data['saswp_website_schema'])){
                 
                $site_url  = get_site_url();
		$site_name = get_bloginfo();
                
                if($site_url && $site_name){
                 
                    $input = array(
                            '@context'	  => 'http://schema.org',
                            '@type'		  => 'WebSite',
                            '@id'		  => $site_url.'/#website',
                            'url'		  => $site_url,
                            'name'		  => $site_name,			
			);  
                    
                    if(isset($sd_data['saswp_search_box_schema']) && $sd_data['saswp_search_box_schema'] == 1 || !isset($sd_data['saswp_search_box_schema'])){
                        
                        $input['potentialAction']['@type']       = 'SearchAction';
                        $input['potentialAction']['target']      = esc_url($site_url).'/?s={search_term_string}';
                        $input['potentialAction']['query-input'] = 'required name=search_term_string';
                        
                    }
                  }                                        
                }                		                		
	
	return apply_filters('saswp_modify_website_output', $input);       
}	

/**
 * Function generates archive page schema markup in the form of CollectionPage schema type
 * @global type $query_string
 * @global type $sd_data
 * @return type json
 */
function saswp_archive_output(){
    
	global $query_string, $sd_data;   
        
        $site_name ='';
        
        if(isset($sd_data['sd_name']) && $sd_data['sd_name'] !=''){
          $site_name = $sd_data['sd_name'];  
        }else{
          $site_name = get_bloginfo();    
        } 	
        	
	if(isset($sd_data['saswp_archive_schema']) && $sd_data['saswp_archive_schema'] == 1){
            
        $schema_type        =  $sd_data['saswp_archive_schema_type'];   
            
        $service_object     = new saswp_output_service();
        $logo               = $service_object->saswp_get_publisher(true);    
            
					
	if ( is_category() ) {
            
		$category_posts = array();
		$category_loop = new WP_Query( $query_string );
		if ( $category_loop->have_posts() ):
			while( $category_loop->have_posts() ): $category_loop->the_post();
				$image_id 		= get_post_thumbnail_id();
                                
                                $archive_image = array();
				$image_details 	        = wp_get_attachment_image_src($image_id, 'full');  
                                
                                if(!empty($image_details)){
                                
                                        $archive_image['@type']  = 'ImageObject';
                                        $archive_image['url']    = esc_url($image_details[0]);
                                        $archive_image['width']  = esc_attr($image_details[1]);
                                        $archive_image['height'] = esc_attr($image_details[2]);                                 
                                    
                                }else{
                                    
                                    if(isset($sd_data['sd_default_image'])){
                                        
                                        $archive_image['@type']  = 'ImageObject';
                                        $archive_image['url']    = esc_url($sd_data['sd_default_image']['url']);
                                        $archive_image['width']  = esc_attr($sd_data['sd_default_image_width']);
                                        $archive_image['height'] = esc_attr($sd_data['sd_default_image_height']);                                  
                                    }
                                    
                                    
                                }
                                
				$publisher_info = array(
                                    "type" => "Organization",
                                    "name" => esc_attr($site_name),
                                    "logo" => array(
                                        "@type"     => "ImageObject",
                                        "name"      => esc_attr($site_name),
                                        "width"     => esc_attr($logo['width']),
                                        "height"    => esc_attr($logo['height']),
                                        "url"       => esc_url($logo['url'])
                                     )                                        			        
				);
                                
				$publisher_info['name'] = get_bloginfo('name');
				$publisher_info['id']	= get_the_permalink();
                                
                                $category_posts[] =  array(
                                
                                                    '@type' 		=> esc_attr($schema_type),
                                                    'headline' 		=> get_the_title(),
                                                    'url' 		=> get_the_permalink(),
                                                    'datePublished'     => get_the_date('c'),
                                                    'dateModified'      => get_the_modified_date('c'),
                                                    'mainEntityOfPage'  => get_the_permalink(),
                                                    'author' 		=> get_the_author(),
                                                    'publisher'         => $publisher_info,
                                                    'image' 	        => $archive_image,
                                );
				
	        endwhile;

		wp_reset_postdata();
			
		$category 		= get_the_category(); 		
		$category_id 		= intval($category[0]->term_id); 
                $category_link 		= get_category_link( $category_id );
		$category_link          = get_term_link( $category[0]->term_id , 'category' );
                $category_headline 	= single_cat_title( '', false ) . __(' Category', 'schema-wp');		
		$input = array
       		(
				'@context' 		=> 'http://schema.org/',
				'@type' 		=> "CollectionPage",
				'headline' 		=> esc_attr($category_headline),
				'description' 	        => strip_tags(category_description()),
				'url'		 	=> esc_url($category_link),
				'sameAs' 		=> '',
				'hasPart' 		=> $category_posts
       		);
				return apply_filters('saswp_modify_archive_output', $input);	                                 
	endif;				
	}
	} 
}

/**
 * Function generates author schema markup
 * @global type $post
 * @global type $sd_data
 * @return type json
 */ 
function saswp_author_output(){
    
	global $post, $sd_data;   
        $post_id ='';
        
	if(isset($sd_data['saswp_archive_schema']) && $sd_data['saswp_archive_schema'] == 1){
            
        if(is_object($post)){
        
            $post_id = $post->ID;
            
        }    
            	
	if(is_author() && $post_id){
		// Get author from post content
		$post_content	= get_post($post_id);                
		$post_author	= get_userdata($post_content->post_author);		
		$input = array (
			'@type'	=> 'Person',
			'name'	=> get_the_author_meta('display_name'),
			'url'	=> esc_url( get_author_posts_url( $post_author->ID ) ),

		);

		$sd_website 	= esc_attr( stripslashes( get_the_author_meta( 'user_url', $post_author->ID ) ) );
		$sd_googleplus  = esc_attr( stripslashes( get_the_author_meta( 'googleplus', $post_author->ID ) ) );
		$sd_facebook 	= esc_attr( stripslashes( get_the_author_meta( 'facebook', $post_author->ID) ) );
		$sd_twitter 	= esc_attr( stripslashes( get_the_author_meta( 'twitter', $post_author->ID ) ) );
		$sd_instagram 	= esc_attr( stripslashes( get_the_author_meta( 'instagram', $post_author->ID ) ) );
		$sd_youtube 	= esc_attr( stripslashes( get_the_author_meta( 'youtube', $post_author->ID ) ) );
		$sd_linkedin 	= esc_attr( stripslashes( get_the_author_meta( 'linkedin', $post_author->ID ) ) );
		$sd_pinterest 	= esc_attr( stripslashes( get_the_author_meta( 'pinterest', $post_author->ID ) ) );
		$sd_soundcloud  = esc_attr( stripslashes( get_the_author_meta( 'soundcloud', $post_author->ID ) ) );
		$sd_tumblr 	= esc_attr( stripslashes( get_the_author_meta( 'tumblr', $post_author->ID ) ) );
		
		$sd_sameAs_links = array( $sd_website, $sd_googleplus, $sd_facebook, $sd_twitter, $sd_instagram, $sd_youtube, $sd_linkedin, $sd_pinterest, $sd_soundcloud, $sd_tumblr);
		
		$sd_social = array();
		
		// Remove empty fields
		foreach( $sd_sameAs_links as $sd_sameAs_link ) {
			if ( '' != $sd_sameAs_link ) $sd_social[] = $sd_sameAs_link;
		}
		
		if ( ! empty($sd_social) ) {
			$input["sameAs"] = $sd_social;
		}

		if ( get_the_author_meta( 'description', $post_author->ID ) ) {
			$input['description'] = strip_tags( get_the_author_meta( 'description', $post_author->ID ) );
		}
		return apply_filters('saswp_modify_author_output', $input);
	}
 }
}

/**
 * Function generates about page schema markup
 * @global type $sd_data
 * @return type json
 */
function saswp_about_page_output(){

	global $sd_data;   
        $feature_image = array();
        $publisher     = array();
        
	if((isset($sd_data['sd_about_page'])) && $sd_data['sd_about_page'] == get_the_ID()){   
            
                        $service_object     = new saswp_output_service();
                        $feature_image      = $service_object->saswp_get_fetaure_image();
                        $publisher          = $service_object->saswp_get_publisher();
                        
			$input = array(
				"@context" 	   => "http://schema.org",
				"@type"		   => "AboutPage",
				"mainEntityOfPage" => array(
                                                            "@type"           => "WebPage",
                                                            "@id"             => get_permalink(),
						),
				"url"		   => get_permalink(),
				"headline"	   => get_the_title(),								
				'description'	   => strip_tags(strip_shortcodes(get_the_excerpt())),
			);
                        
			if(!empty($feature_image)){
                            
                         $input = array_merge($input, $feature_image);   
                         
                        }
                        if(!empty($publisher)){
                            
                         $input = array_merge($input, $publisher);   
                         
                        }
			return apply_filters('saswp_modify_about_page_output', $input);                       
	}
	
}

/**
 * Function generates contact page schema markup
 * @global type $sd_data
 * @return type json
 */
function saswp_contact_page_output(){
    
	global $sd_data;	        	        
        $feature_image = array();
        $publisher     = array();
        
	if(isset($sd_data['sd_contact_page']) && $sd_data['sd_contact_page'] == get_the_ID()){
                        
                        $service_object     = new saswp_output_service();
                        $feature_image      = $service_object->saswp_get_fetaure_image();
                        $publisher          = $service_object->saswp_get_publisher();
                        			
			$input = array(
                            
				"@context" 	    => "http://schema.org",
				"@type"		    => "ContactPage",
				"mainEntityOfPage"  => array(
							"@type" => "WebPage",
							"@id" 	=> get_permalink(),
							),
				"url"		   => get_permalink(),
				"headline"	   => get_the_title(),								
				'description'	   => strip_tags(strip_shortcodes(get_the_excerpt())),
			);
                        
                        if(!empty($feature_image)){
                            
                             $input = array_merge($input, $feature_image);   
                         
                        }
                        
                        if(!empty($publisher)){
                            
                            $input = array_merge($input, $publisher);   
                         
                        }
			return apply_filters('saswp_modify_contact_page_output', $input);
                         
	}
	
}

/**
 * SiteNavigation Schema Markup 
 * @global type $sd_data
 * @return type array
 */
function saswp_site_navigation_output(){
            
    global $sd_data;
    $input = array();    
            
    $menuLocations = get_nav_menu_locations();
        
    if(!empty($menuLocations) && (isset($sd_data['saswp_site_navigation_menu']) &&  $sd_data['saswp_site_navigation_menu'] == 1 )  ){
        
        $navObj = array();
        
        foreach($menuLocations as $type => $id){
            
            $menuItems = wp_get_nav_menu_items($id);
            
            if($menuItems){
                
                if(!saswp_non_amp()){
                                     
                    if($type == 'amp-menu' || $type == 'amp-footer-menu'){
                        
                        foreach($menuItems as $items){
                 
                              $navObj[] = array(
                                     "@context"  => "https://schema.org",
                                     "@type"     => "SiteNavigationElement",
                                     "@id"       => trailingslashit(get_home_url()).$type,
                                     "name"      => esc_attr($items->title),
                                     "url"       => esc_url($items->url)
                              );

                        }
                        
                    }                    
                    
                }else{
                    
                    if($type != 'amp-menu'){
                        
                        foreach($menuItems as $items){
                 
                            $navObj[] = array(
                                    "@context"  => "https://schema.org",
                                    "@type"     => "SiteNavigationElement",
                                    "@id"       => trailingslashit(get_home_url()).$type,
                                    "name"      => esc_attr($items->title),
                                    "url"       => esc_url($items->url)
                            );
                    
                         }
                                                
                    }
                    
                }                                                                    
                
            }
                        
        }
              
        if($navObj){
            
            $input['@context'] = 'https://schema.org'; 
            $input['@graph']   = $navObj; 
            
        }
              
    }
        
    return apply_filters('saswp_modify_sitenavigation_output', $input);
}      
