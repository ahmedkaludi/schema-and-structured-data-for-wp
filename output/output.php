<?php
/**
 * Output Page
 *
 * @author   Magazine3
 * @category Frontend
 * @path  output/output
 * @version 1.0
 */
if (! defined('ABSPATH') ) exit;
/**
 * Function generates knowledge graph schema
 * @global type $sd_data
 * @return type json
 */
function saswp_kb_schema_output() {
    
	    global $sd_data;   
        $input     = array();    
        $site_url  = get_home_url();
	
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
                        '@context'		=> saswp_context_url(),
                        '@type'			=> (isset($sd_data['saswp_organization_type']) && $sd_data['saswp_organization_type'] !='')? $sd_data['saswp_organization_type']:'Organization',
                        '@id'                   => $site_url.'#Organization',
                        'name'			=> saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string'),
                        'url'			=> saswp_remove_warnings($sd_data, 'sd_url', 'saswp_string'),
                        'sameAs'		=> isset($sd_data['saswp_social_links']) ? $sd_data['saswp_social_links'] : array()
		);

                if(!empty($sd_data['sd_legal_name'])){
                    $input['legalName'] = $sd_data['sd_legal_name'];
                }
                
                if($logo !='' && $width !='' && $height !=''){
                    
                    $input['logo']['@type']  = 'ImageObject';
                    $input['logo']['url']    = esc_url($logo);
                    $input['logo']['width']  = esc_attr($width);
                    $input['logo']['height'] = esc_attr($height);
                 
                }
		                    
		$input = array_merge($input, $contact_info);
                
                $reviews = saswp_fetched_reviews_schema_markup();
                
                if($reviews){
                    $input  = array_merge($input, $reviews); 
                }
                        		                
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
			'@context'		=> saswp_context_url(),
			'@type'			=> esc_attr($sd_data['saswp_kb_type']),
                        '@id'                   => $site_url.'#Person',
			'name'			=> isset($sd_data['sd-person-name']) ? esc_attr($sd_data['sd-person-name']) : '',
                        'jobTitle'	        => isset($sd_data['sd-person-job-title']) ? esc_attr($sd_data['sd-person-job-title']) : '',
			'url'			=> isset($sd_data['sd-person-url']) ? esc_url($sd_data['sd-person-url']) : '',
                        'sameAs'		=> isset($sd_data['saswp_social_links']) ? $sd_data['saswp_social_links'] : array(),                                        		
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
        $service_object     = new saswp_output_service();
        
        $all_schema_output  = array();        
                        
        $image_id 	        = get_post_thumbnail_id();									
        $date 		        = get_the_date("c");
        $modified_date 	    = get_the_modified_date("c");        
        $modify_option      = get_option('modify_schema_post_enable_'.get_the_ID()); 
        $schema_enable      = saswp_get_post_meta(saswp_get_the_ID(), 'saswp_enable_disable_schema', true); 
        $all_post_meta      = saswp_get_post_meta(saswp_get_the_ID());                
        $publisher          = $service_object->saswp_get_publisher();
        $extra_theme_review = $service_object->saswp_extra_theme_review_details(saswp_get_the_ID());
        $aggregateRating    = $service_object->saswp_rating_box_rating_markup(saswp_get_the_ID());
        
        
        foreach($Conditionals as $schemaConditionals){
        
                        $schema_options = array();    

                        if(isset($schemaConditionals['schema_options'])){
                            $schema_options = $schemaConditionals['schema_options'];
                        }   

                        $schema_type        = saswp_remove_warnings($schemaConditionals, 'schema_type', 'saswp_string');         
                        $schema_post_id     = saswp_remove_warnings($schemaConditionals, 'post_id', 'saswp_string');        
                        $enable_videoobject = get_post_meta($schema_post_id, 'saswp_enable_videoobject', true);
                        $enable_faqschema   = get_post_meta($schema_post_id, 'saswp_enable_faq_schema', true);
                       

                        $input1         = array();
                                                                                                                                                                   				   		                                                                                           		                        			                                                                                              
                        $modified_schema    = saswp_get_post_meta(saswp_get_the_ID(), 'saswp_modify_this_schema_'.$schema_post_id, true);
                                                                            
                        if($modify_option == 'enable' && (isset($schema_enable[$schema_post_id]) && $schema_enable[$schema_post_id] == 1)){
                     
                            $modified_schema = 1;  
                         
                        }
                        
                        if( (isset($schema_enable[$schema_post_id]) && $schema_enable[$schema_post_id] == 0 ) || 
                                ($modify_option == 'enable' && !isset($schema_enable[$schema_post_id]))  
                                ){
                                    continue;
                        }
                        
                        switch ($schema_type) {
                            
                            case 'ItemList':
                                                                                                                                                
                                $input1['@context']                     = saswp_context_url();
                                $input1['@type']                        = 'ItemList';  
                                $input1['url']                          = saswp_get_permalink();  

                                $input1 = apply_filters('saswp_modify_itemlist_schema_output', $input1 );
                                
                                $input1 = saswp_itemlist_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                                                                                                                                                                                                                              
                            break;
                            
                            case 'FAQ':
                                                                                                                                                
                                $input1['@context']                     = saswp_context_url();
                                $input1['@type']                        = 'FAQPage';
                                $input1['@id']                          = saswp_get_permalink().'#FAQPage';                             
                                $input1['headline']                     = saswp_get_the_title();
                                $input1['keywords']                     = saswp_get_the_tags();
                                $input1['datePublished']                = esc_html($date);
                                $input1['dateModified']                 = esc_html($modified_date);
                                $input1['dateCreated']                  = esc_html($date);
                                $input1['author']                       = saswp_get_author_details();											                            
                                if(isset($all_post_meta['saswp_faq_id_'.$schema_post_id]) && empty($all_post_meta['saswp_faq_id_'.$schema_post_id][0])){
                                    unset($input1['@id']);
                                }
                                if(isset($all_post_meta['saswp_faq_headline_'.$schema_post_id]) && empty($all_post_meta['saswp_faq_headline_'.$schema_post_id][0])){
                                    unset($input1['headline']);
                                }
                                if(isset($all_post_meta['saswp_faq_keywords_'.$schema_post_id]) && empty($all_post_meta['saswp_faq_keywords_'.$schema_post_id][0])){
                                    unset($input1['keywords']);
                                }
                                if(isset($all_post_meta['saswp_faq_date_published_'.$schema_post_id]) && empty($all_post_meta['saswp_faq_date_published_'.$schema_post_id][0])){
                                    unset($input1['datePublished']);
                                }
                                if(isset($all_post_meta['saswp_faq_date_modified_'.$schema_post_id]) && empty($all_post_meta['saswp_faq_date_modified_'.$schema_post_id][0])){
                                    unset($input1['dateModified']);
                                }
                                if(isset($all_post_meta['saswp_faq_date_created_'.$schema_post_id]) && empty($all_post_meta['saswp_faq_date_created_'.$schema_post_id][0])){
                                    unset($input1['dateCreated']);
                                }


                                $input1 = apply_filters('saswp_modify_faq_schema_output', $input1 );

                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_faq_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }

                                if(isset($enable_faqschema) && $enable_faqschema == 1){
                                    if(empty($input1['mainEntity'])){
                                        $input1 = array();    
                                    }
                                }                                                                                                                       
                            break;
                        
                            case 'VideoGame':
                                                                                    
                                $input1['@context']                     = saswp_context_url();
                                $input1['@type']                        = 'VideoGame';
                                $input1['@id']                          = saswp_get_permalink().'#VideoGame';                             
                                $input1['author']['@type']              = 'Organization';                                                        
                                $input1['offers']['@type']              = 'Offer';   

                                $input1 = saswp_append_fetched_reviews($input1, $schema_post_id);

                                $input1 = apply_filters('saswp_modify_video_game_schema_output', $input1 );

                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_video_game_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }
                                
                            break;
                            
                            case 'MedicalCondition':
                            
                                $input1['@context']                     = saswp_context_url();
                                $input1['@type']                        = 'MedicalCondition';
                                $input1['@id']                          = saswp_get_permalink().'#MedicalCondition';                                                                                                             
                                $input1['associatedAnatomy']['@type']   = 'AnatomicalStructure';                                                                                    
                                $input1['code']['@type']                = 'MedicalCode';

                                $input1 = apply_filters('saswp_modify_medical_condition_schema_output', $input1 );

                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_medical_condition_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }
                                
                            break;
                        
                            case 'TVSeries':
                                                                                                                        
                                $input1['@context']              = saswp_context_url();
                                $input1['@type']                 = 'TVSeries';
                                $input1['@id']                   = saswp_get_permalink().'#TVSeries';                                                                                                                                
                                $input1['author']['@type']       = 'Person';                            

                                $input1 = saswp_append_fetched_reviews($input1, $schema_post_id);

                                $input1 = apply_filters('saswp_modify_tvseries_schema_output', $input1 );

                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_tv_series_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }
                                                                                            
                            break;
                        
                            case 'Movie':
                                                         
                                $input1['@context']              = saswp_context_url();
                                $input1['@type']                 = 'Movie';
                                $input1['@id']                   = saswp_get_permalink().'#Movie';                                                                                                                                              

                                $input1 = saswp_append_fetched_reviews($input1, $schema_post_id);

                                $input1 = apply_filters('saswp_modify_movie_schema_output', $input1 );

                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_movie_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }
                                
                            break;
                        
                            case 'HowTo':
                                                         
                                $input1['@context']              = saswp_context_url();
                                $input1['@type']                 = 'HowTo';
                                $input1['@id']                   = saswp_get_permalink().'#HowTo';                                                                                                                                                    
                                $input1['name']                  = saswp_get_the_title();
                                $input1['description']           = saswp_get_the_excerpt();

                                $input1 = saswp_append_fetched_reviews($input1, $schema_post_id);

                                $input1 = apply_filters('saswp_modify_howto_schema_output', $input1 );

                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_howto_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }
                                
                            break;
                        
                            case 'Trip':
                                                                                   
                                $input1['@context']              = saswp_context_url();
                                $input1['@type']                 = 'Trip';
                                $input1['@id']                   = saswp_get_permalink().'#Trip';    

                                $input1 = apply_filters('saswp_modify_trip_schema_output', $input1 );

                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_trip_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }
                            
                            break;

                            case 'BoatTrip':
                                                                                   
                                $input1['@context']              = saswp_context_url();
                                $input1['@type']                 = 'BoatTrip';
                                $input1['@id']                   = saswp_get_permalink().'#BoatTrip';    

                                $input1 = apply_filters('saswp_modify_boat_trip_schema_output', $input1 );

                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_boat_trip_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }
                            
                            break;
                        
                            case 'SingleFamilyResidence':
                                                                                                                                            
                                $input1['@context']              = saswp_context_url();
                                $input1['@type']                 = 'SingleFamilyResidence';
                                $input1['@id']                   = saswp_get_permalink().'#SingleFamilyResidence';                            
                                $input1['address']['@type']      = 'PostalAddress';

                                $input1 = apply_filters('saswp_modify_apartment_schema_sfr', $input1 );

                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_single_family_residence_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }
                            
                            break;
                        
                            case 'House':
                                                                            
                                $input1['@context']              = saswp_context_url();
                                $input1['@type']                 = 'House';
                                $input1['@id']                   = saswp_get_permalink().'#House';
                                $input1['address']['@type']      = 'PostalAddress';

                                $input1 = apply_filters('saswp_modify_apartment_schema_house', $input1 );

                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_house_schema_makrup($schema_post_id, get_the_ID(), $all_post_meta);
                                }
                                
                            break;
                        
                            case 'Apartment':
                                                                                   
                                $input1['@context']              = saswp_context_url();
                                $input1['@type']                 = 'Apartment';
                                $input1['@id']                   = saswp_get_permalink().'#Apartment';                                                                                                                                                                            
                                $input1['address']['@type']      = 'PostalAddress';    

                                $input1 = apply_filters('saswp_modify_apartment_schema_output', $input1 );

                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_apartment_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }
                           
                            break;
                        
                            case 'MusicPlaylist':
                                                                                                                                                                        
                                $input1['@context']              = saswp_context_url();
                                $input1['@type']                 = 'MusicPlaylist';
                                $input1['@id']                   = get_permalink().'#MusicPlaylist'; 

                                $input1 = saswp_append_fetched_reviews($input1, $schema_post_id);

                                $input1 = apply_filters('saswp_modify_music_playlist_schema_output', $input1 );

                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_music_playlist_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }
                                
                            break;
                        
                            case 'MusicComposition':
                                                                                                                                                                                                                                       
                                $input1['@context']              = saswp_context_url();
                                $input1['@type']                 = 'MusicComposition';
                                $input1['@id']                   = get_permalink().'#MusicComposition'; 
                                $input1['inLanguage']            = get_bloginfo('language');
                                $input1['datePublished']         = esc_html($date);                 

                                $input1 = saswp_append_fetched_reviews($input1, $schema_post_id);

                                $input1 = apply_filters('saswp_modify_music_composition_schema_output', $input1 );

                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_music_composition_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }
                                                                                                                                              
                            break;
                        
                            case 'Book':
                                                                                                                                                                        
                                $input1['@context']              = saswp_context_url();
                                $input1['@type']                 = 'Book';
                                $input1['@id']                   = get_permalink().'#Book'; 
                                
                                 $woo_markp = $service_object->saswp_schema_markup_generator($schema_type);

                                if($woo_markp){
                                    $input1 = array_merge($input1, $woo_markp);
                                }

                                unset($input1['brand'], $input1['mpn'], $input1['sku'],$input1['gtin8'], $input1['gtin13'], $input1['gtin12']);

                                $input1 = saswp_append_fetched_reviews($input1, $schema_post_id);

                                $input1 = apply_filters('saswp_modify_book_schema_output', $input1 );

                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_book_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }
                                
                            break;
                        
                            case 'MusicAlbum':
                                                                                                                                                                        
                                $input1['@context']              = saswp_context_url();
                                $input1['@type']                 = 'MusicAlbum';
                                $input1['@id']                   = get_permalink().'#MusicAlbum';  

                                $input1 = saswp_append_fetched_reviews($input1, $schema_post_id);
                                $input1 = apply_filters('saswp_modify_music_album_schema_output', $input1 );

                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_music_album_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }
                                
                            break;
                        
                            case 'TouristDestination':
                                                                                                                
                                $input1['@context']              = saswp_context_url();
                                $input1['@type']                 = 'TouristDestination';
                                $input1['@id']                   = saswp_get_permalink().'#TouristDestination';                                                                                   
                                $input1['address']['@type']      = 'PostalAddress';

                                $input1 = apply_filters('saswp_modify_tourist_destination_schema_output', $input1 );

                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_tourist_destination_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }
                                
                            break;
                        
                            case 'TouristAttraction':
                                                   
                                $input1['@context']              = saswp_context_url();
                                $input1['@type']                 = 'TouristAttraction';
                                $input1['@id']                   = saswp_get_permalink().'#TouristAttraction';                              
                                $input1['address']['@type']      = 'PostalAddress';   

                                $input1 = apply_filters('saswp_modify_tourist_attraction_schema_output', $input1 );

                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_tourist_attraction_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }
                                
                            break;
                        
                            case 'LandmarksOrHistoricalBuildings':
                                                   
                                $input1['@context']              = saswp_context_url();
                                $input1['@type']                 = 'LandmarksOrHistoricalBuildings';
                                $input1['@id']                   = saswp_get_permalink().'#LandmarksOrHistoricalBuildings';                                                        
                                $input1['address']['@type']      = 'PostalAddress';   

                                $input1 = apply_filters('saswp_modify_lorh_schema_output', $input1 );

                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_lorh_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }
                                
                            break;
                            
                            case 'BuddhistTemple':
                                                                                   
                                $input1['@context']              = saswp_context_url();
                                $input1['@type']                 = 'BuddhistTemple';
                                $input1['@id']                   = saswp_get_permalink().'#BuddhistTemple';
                                $input1['address']['@type']      = 'PostalAddress';  

                                $input1 = apply_filters('saswp_modify_buddhist_temple_schema_output', $input1 );

                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_buddhist_temple_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }
                                
                            break;

                            case 'HinduTemple':
                                                                                   
                                $input1['@context']              = saswp_context_url();
                                $input1['@type']                 = 'HinduTemple';
                                $input1['@id']                   = saswp_get_permalink().'#HinduTemple';
                                $input1['address']['@type']             = 'PostalAddress';  

                                $input1 = apply_filters('saswp_modify_hindu_temple_schema_output', $input1 );

                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_hindu_temple_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }
                                
                            break;
                        
                            case 'Church':
                                                                                  
                                $input1['@context']              = saswp_context_url();
                                $input1['@type']                 = 'Church';
                                $input1['@id']                   = saswp_get_permalink().'#Church';                            
                                $input1['address']['@type']      = 'PostalAddress';

                                $input1 = apply_filters('saswp_modify_church_schema_output', $input1 );

                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_church_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }
                                
                            break;
                        
                            case 'Mosque':
                                                                                                                
                                $input1['@context']              = saswp_context_url();
                                $input1['@type']                 = 'Mosque';
                                $input1['@id']                   = saswp_get_permalink().'#Mosque';                            
                                $input1['address']['@type']      = 'PostalAddress';  

                                $input1 = apply_filters('saswp_modify_mosque_schema_output', $input1 );

                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_mosque_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }
                                
                            break;
                        
                            case 'JobPosting':
                                                                                   
                                $input1['@context']                        = saswp_context_url();
                                $input1['@type']                           = 'JobPosting';
                                $input1['@id']                             = saswp_get_permalink().'#JobPosting';                                                          
                                $input1['datePosted']                      = esc_html($date);                                                                                                                                                
                                $input1['hiringOrganization']['@type']     = 'Organization'; 
                                $input1['hiringOrganization']['name']      = (isset($sd_data['sd_name']) && $sd_data['sd_name'] !='' )? $sd_data['sd_name'] : get_bloginfo(); 
                                $input1['jobLocation']['@type']            = 'Place';
                                $input1['jobLocation']['address']['@type'] = 'PostalAddress';                                                                                   
                                $input1['baseSalary']['@type']             = 'MonetaryAmount';                            
                                $input1['baseSalary']['value']['@type']    = 'QuantitativeValue'; 
                                $input1['estimatedSalary']['@type']        = 'MonetaryAmount';                            
                                $input1['estimatedSalary']['value']['@type']    = 'QuantitativeValue';     

                                $input1 = apply_filters('saswp_modify_jobposting_schema_output', $input1 );

                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_job_posting_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }
                                
                            break;
                        
                            case 'Person':

                                $author_id = get_the_author_meta( 'ID' );

                                $input1['@context']              = saswp_context_url();                                                               
                                $input1                          = array_merge($input1, saswp_get_author_details());                                                                                                                                                               
                                $input1['url']                   = get_the_author_meta('user_url', $author_id);  
                                
                                $input1['address']['@type']      = 'PostalAddress';             

                                $input1 = apply_filters('saswp_modify_person_schema_output', $input1 );

                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_person_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }
                            
                            break;
                        
                            case 'Course':
                                
                                $input1 = array(
                                '@context'			=> saswp_context_url(),
                                '@type'				=> $schema_type ,
                                '@id'				=> saswp_get_permalink().'#course',    
                                'name'			    => saswp_get_the_title(),
                                'description'       => saswp_get_the_excerpt(),			
                                'url'				=> saswp_get_permalink(),
                                'datePublished'     => esc_html($date),
                                'dateModified'      => esc_html($modified_date),
                                'author'			=> saswp_get_author_details(),    
                                'provider'			=> array(
                                                                    '@type' 	        => 'Organization',
                                                                    'name'		=> get_bloginfo(),
                                                                    'sameAs'		=> get_home_url() 
                                                                )											
                                    );

                                        if(!empty($aggregateRating)){
                                            $input1['aggregateRating'] = $aggregateRating;
                                        }                                
                                        if(!empty($extra_theme_review)){
                                           $input1 = array_merge($input1, $extra_theme_review);
                                        }   
                                        $input1 = saswp_append_fetched_reviews($input1);                            
                                        if(isset($sd_data['saswp_comments_schema']) && $sd_data['saswp_comments_schema'] ==1){
                                           $input1['comment'] = saswp_get_comments(get_the_ID());
                                        }

                                        $input1 = apply_filters('saswp_modify_course_schema_output', $input1 );

                                        $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                        
                                        if($modified_schema == 1){
                                    
                                          $input1 = saswp_course_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                        }
                        
                                
                            break;
                        
                            case 'DiscussionForumPosting':
                                                     
                            if(isset($sd_data['saswp-bbpress']) && $sd_data['saswp-bbpress'] == 1 && is_plugin_active('bbpress/bbpress.php')){                                                                                                                                                                                            
                                
                                $headline = bbp_get_topic_title(get_the_ID());

                                if (strlen($headline) > 110){
                                    $headline = substr($headline, 0, 106) . ' ...';
                                }

                                $input1 = array(
                                '@context'			=> saswp_context_url(),
                                '@type'				=> 'DiscussionForumPosting' ,
                                '@id'				=> bbp_get_topic_permalink().'#discussionforumposting',
                                'mainEntityOfPage'              => bbp_get_topic_permalink(), 
                                'headline'			=> $headline,
                                'description'                   => saswp_get_the_excerpt(),
                                "articleSection"                => bbp_get_forum_title(),
                                "articleBody"                   => saswp_get_the_content(),    
                                'url'				=> bbp_get_topic_permalink(),
                                'datePublished'                 => get_post_time( DATE_ATOM, false, get_the_ID(), true ),
                                'dateModified'                  => esc_html($modified_date),
                                'author'			=> saswp_get_author_details(),                                    
                                'interactionStatistic'          => array(
                                                                    '@type'                     => 'InteractionCounter',
                                                                    'interactionType'		=> saswp_context_url().'/CommentAction',
                                                                    'userInteractionCount'      => bbp_get_topic_reply_count(),
                                        )    
                                );
                                
                            }else{
                                
                                $input1 = array(
                                '@context'			=> saswp_context_url(),
                                '@type'				=> 'DiscussionForumPosting' ,
                                '@id'				=> saswp_get_permalink().'#DiscussionForumPosting',    			
                                'url'				=> saswp_get_permalink(),
                                'mainEntityOfPage'              => saswp_get_permalink(),       
                                'headline'			=> saswp_get_the_title(),
                                'description'                   => saswp_get_the_excerpt(),			                                
                                'datePublished'                 => esc_html($date),
                                'dateModified'                  => esc_html($modified_date),
                                'author'			=> saswp_get_author_details()											
                                );
                                
                            }                                                                                                    
                                if(!empty($publisher)){

                                     $input1 = array_merge($input1, $publisher);   

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
                                
                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_dfp_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }
                        
                                
                            break;
                        
                            case 'Blogposting':
                            case 'BlogPosting':
                                
                                    $input1 = $service_object->saswp_schema_markup_generator($schema_type);
                            
                                    $mainentity = saswp_get_mainEntity($schema_post_id);

                                    if($mainentity){
                                        $input1['mainEntity'] = $mainentity;                                     
                                    }
                                                                                                        
                                    $input1 = apply_filters('saswp_modify_blogposting_schema_output', $input1 ); 

                                    $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                    
                                    if($modified_schema == 1){
                                
                                        $input1 = saswp_blogposting_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                    }                        
                                
                            break;

                            case 'Car':
                                    
                                $input1 = array(
                                '@context'			=> saswp_context_url(),
                                '@type'				=> ['Product', 'Car'],                                                                  
                                'url'				=> saswp_get_current_url(),                                                                                       
                                'description'       => saswp_get_the_excerpt(),                                                                    
                                'name'				=> saswp_get_the_title(),			                                
                                );
                                                                                                         
                                if(isset($sd_data['saswp_comments_schema']) && $sd_data['saswp_comments_schema'] == 1){
                                    $input1['comment'] = saswp_get_comments(get_the_ID());
                                }
                                
                                $input1 = saswp_append_fetched_reviews($input1, $schema_post_id);

                                $input1 = apply_filters('saswp_modify_car_schema_output', $input1 ); 

                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                            
                                    $input1 = saswp_car_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);

                                }
                                                            
                            break;

                            case 'Vehicle':
                                    
                                $input1 = array(
                                '@context'			=> saswp_context_url(),
                                '@type'				=> ['Product', 'Vehicle'],                                                                  
                                'url'				=> saswp_get_current_url(),                                                                                       
                                'description'       => saswp_get_the_excerpt(),                                                                    
                                'name'				=> saswp_get_the_title(),			                                
                                );
                                                                                                         
                                if(isset($sd_data['saswp_comments_schema']) && $sd_data['saswp_comments_schema'] == 1){
                                    $input1['comment'] = saswp_get_comments(get_the_ID());
                                }
                                
                                $input1 = saswp_append_fetched_reviews($input1, $schema_post_id);

                                $input1 = apply_filters('saswp_modify_vehicle_schema_output', $input1 ); 

                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                            
                                    $input1 = saswp_vehicle_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);

                                }
                                                            
                            break;

                            case 'CreativeWorkSeries':                                
                                    
                                    $input1 = array(
                                    '@context'			=> saswp_context_url(),
                                    '@type'				=> 'CreativeWorkSeries',
                                    '@id'				=> saswp_get_current_url().'#CreativeWorkSeries',    
                                    'url'				=> saswp_get_current_url(),
                                    'inLanguage'        => get_bloginfo('language'),                                                                            
                                    'description'       => saswp_get_the_excerpt(),                                    
                                    'keywords'          => saswp_get_the_tags(),    
                                    'name'				=> saswp_get_the_title(),			
                                    'datePublished'     => esc_html($date),
                                    'dateModified'      => esc_html($modified_date),
                                    'author'			=> saswp_get_author_details()											
                                    );
                                               
                                    if(!empty($publisher)){
                                            $input1 = array_merge($input1, $publisher);   
                                    }                              
                                    if(isset($sd_data['saswp_comments_schema']) && $sd_data['saswp_comments_schema'] == 1){
                                        $input1['comment'] = saswp_get_comments(get_the_ID());
                                    }
                                    
                                    $input1 = saswp_append_fetched_reviews($input1, $schema_post_id);

                                    $input1 = apply_filters('saswp_modify_creative_work_series_schema_output', $input1 ); 

                                    $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                    
                                    if($modified_schema == 1){
                                
                                        $input1 = saswp_creative_work_series_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);

                                    }
                                                                
                                break;

                                case 'EducationalOccupationalCredential':                                
                                    
                                    $input1 = array(
                                    '@context'			=> saswp_context_url(),
                                    '@type'				=> 'EducationalOccupationalCredential',
                                    '@id'				=> saswp_get_permalink().'#EducationalOccupationalCredential',    
                                    'url'				=> saswp_get_permalink(),                                                                                    
                                    'description'       => saswp_get_the_excerpt(),                                                                        
                                    'name'				=> saswp_get_the_title()			                                                                                                            
                                    );                                                                                                                                                                                        
                                                                                                            
                                    $input1 = apply_filters('saswp_modify_educational_occupational_credential_schema_output', $input1 ); 

                                    $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                    
                                    if($modified_schema == 1){
                                
                                        $input1 = saswp_educational_occupational_credential_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);

                                    }
                                                                
                                break; 
                                
                                case 'Audiobook':                                
                                    
                                    $input1 = array(
                                    '@context'			=> saswp_context_url(),
                                    '@type'				=> 'Audiobook',
                                    '@id'				=> saswp_get_permalink().'#Audiobook',    
                                    'url'				=> saswp_get_permalink(),                                                                                    
                                    'description'       => saswp_get_the_excerpt(),                                                                        
                                    'name'				=> saswp_get_the_title(),
                                    'datePublished'     => esc_html($date),
                                    'dateModified'      => esc_html($modified_date),
                                    'author'			=> saswp_get_author_details()						                                                                                                            
                                    );                                                                                                                                                                                        
                                                                                                            
                                    $input1 = apply_filters('saswp_modify_audiobook_schema_output', $input1 ); 

                                    $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                    
                                    if($modified_schema == 1){
                                
                                        $input1 = saswp_audiobook_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);

                                    }
                                                                
                                break; 

                                case 'PodcastEpisode':                                
                                    
                                    $input1 = array(
                                    '@context'			=> saswp_context_url(),
                                    '@type'				=> 'PodcastEpisode',
                                    '@id'				=> saswp_get_permalink().'#PodcastEpisode',    
                                    'url'				=> saswp_get_permalink(),                                                                                    
                                    'description'       => saswp_get_the_excerpt(),                                                                        
                                    'name'				=> saswp_get_the_title(),
                                    'datePublished'     => esc_html($date),
                                    'dateModified'      => esc_html($modified_date),                                    
                                    );                                                                                                                                                                                        
                                                                                                            
                                    $input1 = apply_filters('saswp_modify_podcast_episode_schema_output', $input1 ); 

                                    $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                    
                                    if($modified_schema == 1){
                                
                                        $input1 = saswp_podcast_episode_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);

                                    }
                                                                
                                break; 

                                case 'HotelRoom':                                
                                    
                                    $input1 = array(
                                    '@context'			=> saswp_context_url(),
                                    '@type'				=> 'Hotel',
                                    '@id'				=> saswp_get_permalink().'#Hotel',    
                                    'url'				=> saswp_get_permalink(),                                                                                    
                                    'description'       => saswp_get_the_excerpt(),                                                                        
                                    'name'				=> saswp_get_the_title()                                    
                                    );                                                                                                                                                                                        
                                                                                                            
                                    $input1 = apply_filters('saswp_modify_hotel_room_schema_output', $input1 ); 

                                    $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                    
                                    if($modified_schema == 1){
                                
                                        $input1 = saswp_hotel_room_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);

                                    }
                                                                
                                break; 

                                case 'PodcastSeason':                                
                                    
                                    $input1 = array(
                                    '@context'			=> saswp_context_url(),
                                    '@type'				=> 'PodcastSeason',
                                    '@id'				=> saswp_get_permalink().'#PodcastSeason',    
                                    'url'				=> saswp_get_permalink(),                                                                                    
                                    'description'       => saswp_get_the_excerpt(),                                                                        
                                    'name'				=> saswp_get_the_title(),
                                    'datePublished'     => esc_html($date),
                                    'dateModified'      => esc_html($modified_date),                                    
                                    );                                                                                                                                                                                        
                                                                                                            
                                    $input1 = apply_filters('saswp_modify_podcast_season_schema_output', $input1 ); 

                                    $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                    
                                    if($modified_schema == 1){
                                
                                        $input1 = saswp_podcast_season_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);

                                    }
                                                                
                                break; 

                                case 'Project':                                
                                    
                                    $input1 = array(
                                    '@context'			=> saswp_context_url(),
                                    '@type'				=> 'Project',
                                    '@id'				=> saswp_get_permalink().'#Project',    
                                    'url'				=> saswp_get_permalink(),                                                                                    
                                    'description'       => saswp_get_the_excerpt(),                                                                        
                                    'name'				=> saswp_get_the_title()			                                                                                                            
                                    );                                                                                                                                                                                        
                                                                        
                                    $input1 = saswp_append_fetched_reviews($input1, $schema_post_id);

                                    $input1 = apply_filters('saswp_modify_project_schema_output', $input1 ); 

                                    $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                    
                                    if($modified_schema == 1){
                                
                                        $input1 = saswp_project_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);

                                    }
                                                                
                                break;    

                                case 'Organization':                                
                                    $organization_type = get_post_meta($schema_post_id, 'saswp_schema_organization_type', true);
                                    if(empty($organization_type)){
                                        $organization_type = 'Organization';
                                    }
                                    $input1 = saswp_kb_schema_output();                                    
                                    if($input1['@type'] == 'Person'){
                                        $input1 = array();
                                        $input1 = array(
                                            '@context'			=> saswp_context_url(),
                                            '@type'				=> $organization_type,
                                            '@id'				=> saswp_get_current_url().'#Organization',    
                                            'url'				=> saswp_get_current_url(),                                                                                    
                                            'description'       => saswp_get_the_excerpt(),                                                                        
                                            'name'				=> saswp_get_the_title()			                                                                                                            
                                        );
                                    }else{
                                        $input1['@type'] = $organization_type;
                                    }
                                                                                                                                                                                                                                                                                                                                        
                                    $input1 = saswp_append_fetched_reviews($input1, $schema_post_id);

                                    $input1 = apply_filters('saswp_modify_organization_schema_output', $input1 ); 

                                    $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                    
                                    if($modified_schema == 1){
                                
                                        $input1 = saswp_organization_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);

                                    }
                                                                                   
                                break;    
                        
                            case 'AudioObject':
                                                                                                     
                                $input1 = array(
                                '@context'			=> saswp_context_url(),
                                '@type'				=> $schema_type ,	
                                '@id'				=> saswp_get_permalink().'#audioobject',     			
                                'datePublished'                 => esc_html($date),
                                'dateModified'                  => esc_html($modified_date),
                                'author'			=> saswp_get_author_details()			
                                );
                                    if(!empty($publisher)){

                                         $input1 = array_merge($input1, $publisher);   

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

                                    $input1 = saswp_append_fetched_reviews($input1, $schema_post_id);


                                    $input1 = apply_filters('saswp_modify_audio_object_schema_output', $input1 );

                                    $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                    
                                    if($modified_schema == 1){
                                    
                                     $input1 = saswp_audio_object_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                    }
                        
                                
                            break;
                        
                            case 'EducationalOccupationalProgram':
                                                                                           
                                $input1['@context'] =  saswp_context_url();
                                $input1['@type']    =  'EducationalOccupationalProgram';
                                $input1['@id']      =  saswp_get_permalink().'#EducationalOccupationalProgram';
                                $input1['url']		= saswp_get_permalink();
                                                                                                                                                                                                                   
                                $input1 = apply_filters('saswp_modify_eop_schema_output', $input1 );
                                
                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_eop_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }
                        
                            break;

                            case 'Event':
                                
                                $event_type         = get_post_meta($schema_post_id, 'saswp_event_type', true);  
                            
                                $input1['@context'] =  saswp_context_url();
                                $input1['@type']    =  $event_type ? $event_type : $schema_type;
                                $input1['@id']      =  saswp_get_permalink().'#event';
                                $input1['url']		= saswp_get_permalink();
                                                                                       
                                if(!empty($aggregateRating)){
                                    $input1['aggregateRating'] = $aggregateRating;
                                }                                
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }                                                                                                
                                $input1 = saswp_append_fetched_reviews($input1, $schema_post_id);
                                                                                            
                                $input1 = apply_filters('saswp_modify_event_schema_output', $input1 );
                                
                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_event_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }
                        
                            break;
                        
                            case 'SoftwareApplication':
                                                                                                           
                                $input1 = array(
                                '@context'			=> saswp_context_url(),
                                '@type'				=> $schema_type ,
                                '@id'				=> saswp_get_permalink().'#softwareapplication',         						                        
                                'datePublished'                 => esc_html($date),
                                'dateModified'                  => esc_html($modified_date),
                                'author'			=> saswp_get_author_details()			
                                );
                                                        
                                $woo_markp = $service_object->saswp_schema_markup_generator($schema_type);
                                
                                if($woo_markp){
                                    $input1 = array_merge($input1, $woo_markp);
                                }
                                                                
                                unset($input1['brand'], $input1['mpn'], $input1['sku'],$input1['gtin8'], $input1['gtin13'], $input1['gtin12']);
                                
                                if(!empty($publisher)){                            
                                     $input1 = array_merge($input1, $publisher);                            
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
                                
                                $input1 = saswp_append_fetched_reviews($input1, $schema_post_id);
                                                                                                
                                $input1 = apply_filters('saswp_modify_software_application_schema_output', $input1 );
                                
                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_software_app_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }
                                                        
                            break;

                            case 'MobileApplication':
                                                                                                           
                                $input1 = array(
                                '@context'			=> saswp_context_url(),
                                '@type'				=> $schema_type ,
                                '@id'				=> saswp_get_permalink().'#MobileApplication',         						                        
                                'datePublished'                 => esc_html($date),
                                'dateModified'                  => esc_html($modified_date),
                                'author'			=> saswp_get_author_details()			
                                );
                                                        
                                $woo_markp = $service_object->saswp_schema_markup_generator($schema_type);
                                
                                if($woo_markp){
                                    $input1 = array_merge($input1, $woo_markp);
                                }
                                                                
                                unset($input1['brand'], $input1['mpn'], $input1['sku'],$input1['gtin8'], $input1['gtin13'], $input1['gtin12']);
                                
                                if(!empty($publisher)){                            
                                     $input1 = array_merge($input1, $publisher);                            
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
                                
                                $input1 = saswp_append_fetched_reviews($input1, $schema_post_id);
                                                                                                
                                $input1 = apply_filters('saswp_modify_mobile_application_schema_output', $input1 );
                                
                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_mobile_app_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }
                                                        
                            break;
                        
                            case 'WebPage':
                                                                
                                $input1 = $service_object->saswp_schema_markup_generator($schema_type);
				               
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
                             
                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_webpage_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }
				
                            break;

                            case 'ItemPage':
                                                                
                                $input1 = $service_object->saswp_schema_markup_generator($schema_type);
				               
                                if(isset($sd_data['saswp_comments_schema']) && $sd_data['saswp_comments_schema'] ==1){
                                    $input1['comment'] = saswp_get_comments(get_the_ID());
                                }                                
                                if(!empty($aggregateRating)){
                                    $input1['mainEntity']['aggregateRating'] = $aggregateRating;
                                }                                
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }
                                
                                $input1 = apply_filters('saswp_modify_itempage_schema_output', $input1 );   
                             
                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_itempage_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }
				
                            break;

                            case 'MedicalWebPage':
                                                                
                                $input1 = $service_object->saswp_schema_markup_generator($schema_type);
				                                
                                if(isset($sd_data['saswp_comments_schema']) && $sd_data['saswp_comments_schema'] ==1){
                                    $input1['comment'] = saswp_get_comments(get_the_ID());
                                }                                
                                if(!empty($aggregateRating)){
                                    $input1['mainEntity']['aggregateRating'] = $aggregateRating;
                                }                                
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }
                                
                                $input1 = apply_filters('saswp_modify_medicalwebpage_schema_output', $input1 );   
                             
                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_medicalwebpage_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }
				
                            break;
                            
                            case 'SpecialAnnouncement':
                                                                
                                $input1 = $service_object->saswp_schema_markup_generator($schema_type);                                                                
				                                
                                if(isset($sd_data['saswp_comments_schema']) && $sd_data['saswp_comments_schema'] ==1){
                                    $input1['comment'] = saswp_get_comments(get_the_ID());
                                }                                                                
                                $input1 = apply_filters('saswp_modify_special_announcement_schema_output', $input1 );  
                                
                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_special_announcement_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }
			                                
                            break;
                            
                            case 'VisualArtwork':
                                                                
                                $input1 = array(
                                    '@context'			=> saswp_context_url(),
                                    '@type'				=> $schema_type ,
                                    '@id'				=> saswp_get_permalink().'#VisualArtwork',     
                                    'url'				=> saswp_get_current_url(),                                                                                    
                                    'description'       => saswp_get_the_excerpt(),                                                                        
                                    'name'				=> saswp_get_the_title(),
                                    'dateCreated'       => esc_html($date),                                    
                                    'creator'			=> saswp_get_author_details()			
                                );
                                                                				                                                                                                                                
                                $input1 = apply_filters('saswp_modify_visualartwork_schema_output', $input1 );  
                                
                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_visualartwork_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }
			                                
                            break;

                            case 'CreativeWork':
                                                                
                                $input1 = $service_object->saswp_schema_markup_generator($schema_type);
                                
                                $mainentity = saswp_get_mainEntity($schema_post_id);
                                
                                if($mainentity){
                                   $input1['mainEntity'] = $mainentity;                                     
                                }
				                                                                                                                                
                                $input1 = apply_filters('saswp_modify_creativework_schema_output', $input1 );  
                                
                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_creativework_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }
			                                
                            break;

                            case 'Article':
                                                                
                                $input1 = $service_object->saswp_schema_markup_generator($schema_type);
                                
                                $mainentity = saswp_get_mainEntity($schema_post_id);
                                
                                if($mainentity){
                                   $input1['mainEntity'] = $mainentity;                                     
                                }
				                                                                                                                                
                                $input1 = apply_filters('saswp_modify_article_schema_output', $input1 );  
                                
                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                   
                                    $input1 = saswp_article_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }
			                                
                            break;

                            case 'ScholarlyArticle':
                                                                
                                $input1 = $service_object->saswp_schema_markup_generator($schema_type);
                                
                                $mainentity = saswp_get_mainEntity($schema_post_id);
                                
                                if($mainentity){
                                   $input1['mainEntity'] = $mainentity;                                     
                                }
				                                                                                                                                
                                $input1 = apply_filters('saswp_modify_scholarlyarticle_schema_output', $input1 );  
                                
                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                   
                                    $input1 = saswp_scholarlyarticle_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }
			                                
                            break;

                            case 'BreadCrumbs': 
                                
                                if(is_single() || is_page() ||is_archive()){
            
                                    $bread_crumb_list =   saswp_list_items_generator();  
                                
                                    if(!empty($bread_crumb_list)){   
                                    
                                        $input1['@context']        =  saswp_context_url();
                                        $input1['@type']           =  'BreadcrumbList';
                                        $input1['@id']             =  $sd_data['breadcrumb_url'].'#breadcrumb';
                                        $input1['itemListElement'] =  $bread_crumb_list;
                                                               
                                        $input1 = apply_filters('saswp_modify_breadcrumb_output', $input1);  
                                 
                                     }
                                       
                                }
                                                                                                                                                                       			                                
                            break;

                            case 'Photograph':
                                                                
                                $input1 = $service_object->saswp_schema_markup_generator($schema_type);
                                                                				                                                                                                                                
                                $input1 = apply_filters('saswp_modify_photograph_schema_output', $input1 );  
                                
                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_photograph_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }
			                                
                            break;
                        
                            case 'TechArticle':
                                                                
                                $input1 = $service_object->saswp_schema_markup_generator($schema_type);
                                
                                $mainentity = saswp_get_mainEntity($schema_post_id);
                                
                                if($mainentity){
                                   $input1['mainEntity'] = $mainentity;                                     
                                }
                                				                
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }
                                
                                $input1 = apply_filters('saswp_modify_tech_article_schema_output', $input1 );
                                
                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_tech_article_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }
			                                
                            break;
                        
                            case 'Recipe':
                                                                                                                                                           
				                $input1 = array(
                                    '@context'			=> saswp_context_url(),
                                    '@type'				=> 'Recipe',
                                    '@id'				=> saswp_get_permalink().'#recipe',    
                                    'url'				=> saswp_get_permalink(),
                                    'name'			    => saswp_get_the_title(),
                                    'datePublished'     => esc_html($date),
                                    'dateModified'      => esc_html($modified_date),
                                    'description'       => saswp_get_the_excerpt(),
                                    'keywords'          => saswp_get_the_tags(), 
                                    'author'			=> saswp_get_author_details(),        								
				                );
                                                                                               
                                if(isset($sd_data['saswp_comments_schema']) && $sd_data['saswp_comments_schema'] ==1){
                                    $input1['comment'] = saswp_get_comments(get_the_ID());
                                }                                 
                                if(!empty($aggregateRating)){
                                    $input1['aggregateRating'] = $aggregateRating;
                                }                                
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }
                                
                               $input1 = saswp_append_fetched_reviews($input1, $schema_post_id);
                                                                                                                                                        				                                
                               $input1 = apply_filters('saswp_modify_recipe_schema_output', $input1 );
                                                         
                               $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                               
                               if($modified_schema == 1){
                                    
                                    $input1 = saswp_recipe_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                               }
			                                
                            break;
                        
                            case 'qanda':

                                $post_type = get_post_type();
                                
                                $input1 = array(
                                    '@context'			=> saswp_context_url(),
                                    '@type'				=> 'QAPage' ,
                                    '@id'				=> saswp_get_permalink().'#QAPage',
                                    'mainEntity'	    => array(
                                        '@type'    => 'Question'
                                    ) ,
                                );
                                                        
                                if(isset($sd_data['saswp-dw-question-answer']) && $sd_data['saswp-dw-question-answer'] ==1 && ($post_type == 'dwqa-question' || $post_type == 'dwqa-answer')){
                                    
                                    $input1  = $service_object->saswp_dw_question_answers_details(get_the_ID()); 

                                }

                                if(isset($sd_data['saswp-bbpress']) && $sd_data['saswp-bbpress'] ==1 && $post_type == 'topic'){
                                    
                                    $input1  = $service_object->saswp_bb_press_topic_details(get_the_ID()); 

                                }

                                $input1 = apply_filters('saswp_modify_qanda_schema_output', $input1 );

                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_qanda_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }
			                                
                            break;

                            case 'RealEstateListing':

                                $input1 = array(
                                    '@context'			=> saswp_context_url(),
                                    '@type'				=> 'RealEstateListing',
                                    '@id'                           => saswp_get_permalink().'#RealEstateListing',        
                                    'url'				=> saswp_get_permalink(),
                                    'name'			=> saswp_get_the_title(),
                                    'datePosted'                 => esc_html($date),                                    
                                    'description'                   => saswp_get_the_excerpt(),                                    
                                    );
                                	                                                                                                                                                                                                                                                                                                  
                                $input1 = apply_filters('saswp_modify_real_estate_listing_schema_output', $input1 );
                                
                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_real_estate_listing_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }
			
                            break;

                            case 'ApartmentComplex':

                                $input1 = array(
                                    '@context'			=> saswp_context_url(),
                                    '@type'				=> 'ApartmentComplex',
                                    '@id'               => saswp_get_permalink().'#ApartmentComplex',        
                                    'url'				=> saswp_get_permalink(),
                                    'name'			    => saswp_get_the_title(),                                    
                                    'description'       => saswp_get_the_excerpt()                                    
                                );
                                	                                                                                                                                                                                                                                                                                                  
                                $input1 = apply_filters('saswp_modify_apartment_complex_schema_output', $input1 );
                                
                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_apartment_complex_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }
			
                            break;

                            case 'RentAction':

                                $input1 = array(
                                    '@context'			=> saswp_context_url(),
                                    '@type'				=> 'RentAction',
                                    '@id'               => saswp_get_permalink().'#RentAction',        
                                    'url'				=> saswp_get_permalink()                                                                                                            
                                    );
                                	                                                                                                                                                                                                                                                                                                  
                                $input1 = apply_filters('saswp_modify_rent_action_schema_output', $input1 );
                                
                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_rent_action_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }
			
                            break;

                            case 'PsychologicalTreatment':

                                $input1 = array(
                                    '@context'			=> saswp_context_url(),
                                    '@type'				=> 'PsychologicalTreatment',
                                    '@id'               => saswp_get_permalink().'#PsychologicalTreatment',        
                                    'url'				=> saswp_get_permalink(),
                                    'name'			    => saswp_get_the_title(),                                    
                                    'description'       => saswp_get_the_excerpt(),                                    
                                    );
                                	                                                                                                                                                                                                                                                                                                  
                                $input1 = apply_filters('saswp_modify_psychological_treatment_schema_output', $input1 );
                                
                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_psychological_treatment_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }
			
                            break;

                            case 'Product':
                              
                                $input1 = $service_object->saswp_schema_markup_generator($schema_type);
                                  
                                $input1 = saswp_append_fetched_reviews($input1, $schema_post_id);
                                                     
                                $input1 = apply_filters('saswp_modify_product_schema_output', $input1 );
                                
                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_product_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }

			
                            break;
                        
                            case 'NewsArticle':
                                                                                            
                                $image_details 	 = wp_get_attachment_image_src($image_id);

                                $category_detail = get_the_category(get_the_ID());//$post->ID
                                $article_section = '';

                                if($category_detail){

                                    foreach($category_detail as $cd){

                                        if(is_object($cd)){
                                            $article_section =  $cd->cat_name;
                                        }                                        
    
                                    }

                                }
                                
                                    $word_count = saswp_reading_time_and_word_count();

                                    $input1 = array(
                                    '@context'			=> saswp_context_url(),
                                    '@type'				=> $schema_type ,
                                    '@id'				=> saswp_get_permalink().'#newsarticle',
                                    'url'				=> saswp_get_permalink(),
                                    'headline'			=> saswp_get_the_title(),
                                    'mainEntityOfPage'	            => get_the_permalink(),            
                                    'datePublished'                 => esc_html($date),
                                    'dateModified'                  => esc_html($modified_date),
                                    'description'                   => saswp_get_the_excerpt(),
                                    'articleSection'                => $article_section,            
                                    'articleBody'                   => saswp_get_the_content(), 
                                    'keywords'                      => saswp_get_the_tags(),
                                    'name'				            => saswp_get_the_title(), 					
                                    'thumbnailUrl'                  => saswp_remove_warnings($image_details, 0, 'saswp_string'),
                                    'wordCount'                     => saswp_remove_warnings($word_count, 'word_count', 'saswp_string'),
                                    'timeRequired'                  => saswp_remove_warnings($word_count, 'timerequired', 'saswp_string'),            
                                    'mainEntity'                    => array(
                                                                                        '@type' => 'WebPage',
                                                                                        '@id'   => saswp_get_permalink(),
                                    ), 
                                    'author'			=> saswp_get_main_authors(),//saswp_get_author_details(),
                                    'editor'            => saswp_get_edited_authors()//saswp_get_author_details()
                                    );
                                        
                                        $mainentity = saswp_get_mainEntity($schema_post_id);
                                        
                                        if($mainentity){
                                         $input1['mainEntity'] = $mainentity;                                     
                                        }
                                        
                                        if(!empty($publisher)){
                                    
                                            $input1 = array_merge($input1, $publisher);   
                                
                                        }                                
                                        if(isset($sd_data['saswp_comments_schema']) && $sd_data['saswp_comments_schema'] ==1){
                                            $input1['comment'] = saswp_get_comments(get_the_ID());
                                        }                
                                                                                                                                                        
                                        $input1 = apply_filters('saswp_modify_news_article_schema_output', $input1 );
                                        
                                        $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                        
                                        if($modified_schema == 1){
                                            
                                            $input1 = saswp_news_article_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                        }
                                        
                            break;

                            case 'AnalysisNewsArticle':
                                                                                            
                                $image_details 	 = wp_get_attachment_image_src($image_id);

                                $category_detail = get_the_category(get_the_ID());//$post->ID
                                $analysis_article_section = '';

                                if($category_detail){

                                    foreach($category_detail as $cd){

                                        if(is_object($cd)){
                                            $analysis_article_section =  $cd->cat_name;
                                        }                                        
    
                                    }

                                }
                                
                                    $word_count = saswp_reading_time_and_word_count();

                                    $input1 = array(
                                    '@context'			=> saswp_context_url(),
                                    '@type'				=> $schema_type ,
                                    '@id'				=> saswp_get_permalink().'#analysisnewsarticle',
                                    'url'				=> saswp_get_permalink(),
                                    'headline'			=> saswp_get_the_title(),
                                    'mainEntityOfPage'	            => get_the_permalink(),            
                                    'datePublished'                 => esc_html($date),
                                    'dateModified'                  => esc_html($modified_date),
                                    'description'                   => saswp_get_the_excerpt(),
                                    'articleSection'                => $analysis_article_section,            
                                    'articleBody'                   => saswp_get_the_content(), 
                                    'keywords'                      => saswp_get_the_tags(),
                                    'name'				            => saswp_get_the_title(), 					
                                    'thumbnailUrl'                  => saswp_remove_warnings($image_details, 0, 'saswp_string'),
                                    'wordCount'                     => saswp_remove_warnings($word_count, 'word_count', 'saswp_string'),
                                    'timeRequired'                  => saswp_remove_warnings($word_count, 'timerequired', 'saswp_string'),            
                                    'mainEntity'                    => array(
                                                                                        '@type' => 'WebPage',
                                                                                        '@id'   => saswp_get_permalink(),
                                    ), 
                                    'author'			=> saswp_get_author_details(),
                                    'editor'            => saswp_get_author_details()
                                    );
                                        
                                        $mainentity = saswp_get_mainEntity($schema_post_id);
                                        
                                        if($mainentity){
                                         $input1['mainEntity'] = $mainentity;                                     
                                        }
                                        
                                        if(!empty($publisher)){
                                    
                                            $input1 = array_merge($input1, $publisher);   
                                
                                        }                                
                                        if(isset($sd_data['saswp_comments_schema']) && $sd_data['saswp_comments_schema'] ==1){
                                            $input1['comment'] = saswp_get_comments(get_the_ID());
                                        }                
                                                                                                                                                        
                                        $input1 = apply_filters('saswp_modify_analysis_newsarticle_schema_output', $input1 );
                                        
                                        $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                        
                                        if($modified_schema == 1){
                                            
                                            $input1 = saswp_analysis_newsarticle_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                        }
                                        
                            break;

                            case 'AskPublicNewsArticle':
                                                                                            
                                $image_details 	 = wp_get_attachment_image_src($image_id);

                                $category_detail = get_the_category(get_the_ID());//$post->ID
                                $askpublic_article_section = '';

                                if($category_detail){

                                    foreach($category_detail as $cd){

                                        if(is_object($cd)){
                                            $askpublic_article_section =  $cd->cat_name;
                                        }                                        
    
                                    }

                                }
                                
                                    $word_count = saswp_reading_time_and_word_count();

                                    $input1 = array(
                                    '@context'			=> saswp_context_url(),
                                    '@type'				=> $schema_type ,
                                    '@id'				=> saswp_get_permalink().'#askpublicnewsarticle',
                                    'url'				=> saswp_get_permalink(),
                                    'headline'			=> saswp_get_the_title(),
                                    'mainEntityOfPage'	            => get_the_permalink(),            
                                    'datePublished'                 => esc_html($date),
                                    'dateModified'                  => esc_html($modified_date),
                                    'description'                   => saswp_get_the_excerpt(),
                                    'articleSection'                => $askpublic_article_section,            
                                    'articleBody'                   => saswp_get_the_content(), 
                                    'keywords'                      => saswp_get_the_tags(),
                                    'name'				            => saswp_get_the_title(), 					
                                    'thumbnailUrl'                  => saswp_remove_warnings($image_details, 0, 'saswp_string'),
                                    'wordCount'                     => saswp_remove_warnings($word_count, 'word_count', 'saswp_string'),
                                    'timeRequired'                  => saswp_remove_warnings($word_count, 'timerequired', 'saswp_string'),            
                                    'mainEntity'                    => array(
                                                                                        '@type' => 'WebPage',
                                                                                        '@id'   => saswp_get_permalink(),
                                    ), 
                                    'author'			=> saswp_get_author_details(),
                                    'editor'            => saswp_get_author_details()
                                    );
                                        
                                        $mainentity = saswp_get_mainEntity($schema_post_id);
                                        
                                        if($mainentity){
                                         $input1['mainEntity'] = $mainentity;                                     
                                        }
                                        
                                        if(!empty($publisher)){
                                    
                                            $input1 = array_merge($input1, $publisher);   
                                
                                        }                                
                                        if(isset($sd_data['saswp_comments_schema']) && $sd_data['saswp_comments_schema'] ==1){
                                            $input1['comment'] = saswp_get_comments(get_the_ID());
                                        }                
                                                                                                                                                        
                                        $input1 = apply_filters('saswp_modify_askpublic_newsarticle_schema_output', $input1 );
                                        
                                        $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                        
                                        if($modified_schema == 1){
                                            
                                            $input1 = saswp_askpublic_newsarticle_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                        }
                                        
                            break;

                            case 'BackgroundNewsArticle':
                                                                                            
                                $image_details 	 = wp_get_attachment_image_src($image_id);

                                $category_detail = get_the_category(get_the_ID());//$post->ID
                                $background_article_section = '';

                                if($category_detail){

                                    foreach($category_detail as $cd){

                                        if(is_object($cd)){
                                            $background_article_section =  $cd->cat_name;
                                        }                                        
    
                                    }

                                }
                                
                                    $word_count = saswp_reading_time_and_word_count();

                                    $input1 = array(
                                    '@context'			=> saswp_context_url(),
                                    '@type'				=> $schema_type ,
                                    '@id'				=> saswp_get_permalink().'#backgroundnewsarticle',
                                    'url'				=> saswp_get_permalink(),
                                    'headline'			=> saswp_get_the_title(),
                                    'mainEntityOfPage'	            => get_the_permalink(),            
                                    'datePublished'                 => esc_html($date),
                                    'dateModified'                  => esc_html($modified_date),
                                    'description'                   => saswp_get_the_excerpt(),
                                    'articleSection'                => $background_article_section,            
                                    'articleBody'                   => saswp_get_the_content(), 
                                    'keywords'                      => saswp_get_the_tags(),
                                    'name'				            => saswp_get_the_title(), 					
                                    'thumbnailUrl'                  => saswp_remove_warnings($image_details, 0, 'saswp_string'),
                                    'wordCount'                     => saswp_remove_warnings($word_count, 'word_count', 'saswp_string'),
                                    'timeRequired'                  => saswp_remove_warnings($word_count, 'timerequired', 'saswp_string'),            
                                    'mainEntity'                    => array(
                                                                                        '@type' => 'WebPage',
                                                                                        '@id'   => saswp_get_permalink(),
                                    ), 
                                    'author'			=> saswp_get_author_details(),
                                    'editor'            => saswp_get_author_details()
                                    );
                                        
                                        $mainentity = saswp_get_mainEntity($schema_post_id);
                                        
                                        if($mainentity){
                                         $input1['mainEntity'] = $mainentity;                                     
                                        }
                                        
                                        if(!empty($publisher)){
                                    
                                            $input1 = array_merge($input1, $publisher);   
                                
                                        }                                
                                        if(isset($sd_data['saswp_comments_schema']) && $sd_data['saswp_comments_schema'] ==1){
                                            $input1['comment'] = saswp_get_comments(get_the_ID());
                                        }                
                                                                                                                                                        
                                        $input1 = apply_filters('saswp_modify_background_newsarticle_schema_output', $input1 );
                                        
                                        $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                        
                                        if($modified_schema == 1){
                                            
                                            $input1 = saswp_background_newsarticle_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                        }
                                        
                            break;

                            case 'OpinionNewsArticle':
                                                                                            
                                $image_details 	 = wp_get_attachment_image_src($image_id);

                                $category_detail = get_the_category(get_the_ID());//$post->ID
                                $background_article_section = '';

                                if($category_detail){

                                    foreach($category_detail as $cd){

                                        if(is_object($cd)){
                                            $background_article_section =  $cd->cat_name;
                                        }                                        
    
                                    }

                                }
                                
                                    $word_count = saswp_reading_time_and_word_count();

                                    $input1 = array(
                                    '@context'			=> saswp_context_url(),
                                    '@type'				=> $schema_type ,
                                    '@id'				=> saswp_get_permalink().'#opinionnewsarticle',
                                    'url'				=> saswp_get_permalink(),
                                    'headline'			=> saswp_get_the_title(),
                                    'mainEntityOfPage'	            => get_the_permalink(),            
                                    'datePublished'                 => esc_html($date),
                                    'dateModified'                  => esc_html($modified_date),
                                    'description'                   => saswp_get_the_excerpt(),
                                    'articleSection'                => $background_article_section,            
                                    'articleBody'                   => saswp_get_the_content(), 
                                    'keywords'                      => saswp_get_the_tags(),
                                    'name'				            => saswp_get_the_title(), 					
                                    'thumbnailUrl'                  => saswp_remove_warnings($image_details, 0, 'saswp_string'),
                                    'wordCount'                     => saswp_remove_warnings($word_count, 'word_count', 'saswp_string'),
                                    'timeRequired'                  => saswp_remove_warnings($word_count, 'timerequired', 'saswp_string'),            
                                    'mainEntity'                    => array(
                                                                                        '@type' => 'WebPage',
                                                                                        '@id'   => saswp_get_permalink(),
                                    ), 
                                    'author'			=> saswp_get_author_details(),
                                    'editor'            => saswp_get_author_details()
                                    );
                                        
                                        $mainentity = saswp_get_mainEntity($schema_post_id);
                                        
                                        if($mainentity){
                                         $input1['mainEntity'] = $mainentity;                                     
                                        }
                                        
                                        if(!empty($publisher)){
                                    
                                            $input1 = array_merge($input1, $publisher);   
                                
                                        }                                
                                        if(isset($sd_data['saswp_comments_schema']) && $sd_data['saswp_comments_schema'] ==1){
                                            $input1['comment'] = saswp_get_comments(get_the_ID());
                                        }                
                                                                                                                                                        
                                        $input1 = apply_filters('saswp_modify_opinion_newsarticle_schema_output', $input1 );
                                        
                                        $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                        
                                        if($modified_schema == 1){
                                            
                                            $input1 = saswp_opinion_newsarticle_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                        }
                                        
                            break;

                            case 'ReportageNewsArticle':
                                                                                            
                                $image_details 	 = wp_get_attachment_image_src($image_id);

                                $category_detail = get_the_category(get_the_ID());//$post->ID
                                $reportage_article_section = '';

                                if($category_detail){

                                    foreach($category_detail as $cd){

                                        if(is_object($cd)){
                                            $reportage_article_section =  $cd->cat_name;
                                        }                                        
    
                                    }

                                }
                                
                                    $word_count = saswp_reading_time_and_word_count();

                                    $input1 = array(
                                    '@context'			=> saswp_context_url(),
                                    '@type'				=> $schema_type ,
                                    '@id'				=> saswp_get_permalink().'#reportagenewsarticle',
                                    'url'				=> saswp_get_permalink(),
                                    'headline'			=> saswp_get_the_title(),
                                    'mainEntityOfPage'	            => get_the_permalink(),            
                                    'datePublished'                 => esc_html($date),
                                    'dateModified'                  => esc_html($modified_date),
                                    'description'                   => saswp_get_the_excerpt(),
                                    'articleSection'                => $reportage_article_section,            
                                    'articleBody'                   => saswp_get_the_content(), 
                                    'keywords'                      => saswp_get_the_tags(),
                                    'name'				            => saswp_get_the_title(), 					
                                    'thumbnailUrl'                  => saswp_remove_warnings($image_details, 0, 'saswp_string'),
                                    'wordCount'                     => saswp_remove_warnings($word_count, 'word_count', 'saswp_string'),
                                    'timeRequired'                  => saswp_remove_warnings($word_count, 'timerequired', 'saswp_string'),            
                                    'mainEntity'                    => array(
                                                                                        '@type' => 'WebPage',
                                                                                        '@id'   => saswp_get_permalink(),
                                    ), 
                                    'author'			=> saswp_get_author_details(),
                                    'editor'            => saswp_get_author_details()
                                    );
                                        
                                        $mainentity = saswp_get_mainEntity($schema_post_id);
                                        
                                        if($mainentity){
                                         $input1['mainEntity'] = $mainentity;                                     
                                        }
                                        
                                        if(!empty($publisher)){
                                    
                                            $input1 = array_merge($input1, $publisher);   
                                
                                        }                                
                                        if(isset($sd_data['saswp_comments_schema']) && $sd_data['saswp_comments_schema'] ==1){
                                            $input1['comment'] = saswp_get_comments(get_the_ID());
                                        }                
                                                                                                                                                        
                                        $input1 = apply_filters('saswp_modify_reportage_newsarticle_schema_output', $input1 );
                                        
                                        $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                        
                                        if($modified_schema == 1){
                                            
                                            $input1 = saswp_reportage_newsarticle_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                        }
                                        
                            break;

                            case 'ReviewNewsArticle':
                                                  
                                $review_markup = $service_object->saswp_replace_with_custom_fields_value($input1, $schema_post_id);                                
                                $item_reviewed = get_post_meta($schema_post_id, 'saswp_review_item_reviewed_'.$schema_post_id, true);                                          
                                $image_details 	 = wp_get_attachment_image_src($image_id);

                                $category_detail = get_the_category(get_the_ID());//$post->ID
                                $reportage_article_section = '';

                                if($category_detail){

                                    foreach($category_detail as $cd){

                                        if(is_object($cd)){
                                            $reportage_article_section =  $cd->cat_name;
                                        }                                        
    
                                    }

                                }
                                
                                    $word_count = saswp_reading_time_and_word_count();

                                    $input1 = array(
                                    '@context'			=> saswp_context_url(),
                                    '@type'				=> $schema_type ,
                                    '@id'				=> saswp_get_permalink().'#reviewnewsarticle',
                                    'url'				=> saswp_get_permalink(),
                                    'headline'			=> saswp_get_the_title(),
                                    'mainEntityOfPage'	            => get_the_permalink(),            
                                    'datePublished'                 => esc_html($date),
                                    'dateModified'                  => esc_html($modified_date),
                                    'description'                   => saswp_get_the_excerpt(),
                                    'articleSection'                => $reportage_article_section,            
                                    'articleBody'                   => saswp_get_the_content(), 
                                    'keywords'                      => saswp_get_the_tags(),
                                    'name'				            => saswp_get_the_title(), 					
                                    'thumbnailUrl'                  => saswp_remove_warnings($image_details, 0, 'saswp_string'),
                                    'wordCount'                     => saswp_remove_warnings($word_count, 'word_count', 'saswp_string'),
                                    'timeRequired'                  => saswp_remove_warnings($word_count, 'timerequired', 'saswp_string'),            
                                    'mainEntity'                    => array(
                                                                                        '@type' => 'WebPage',
                                                                                        '@id'   => saswp_get_permalink(),
                                    ), 
                                    'author'			=> saswp_get_author_details(),
                                    'editor'            => saswp_get_author_details()
                                    );
                                        $input1['itemReviewed']['@type']  =  $item_reviewed;
                                        if(isset($review_markup['item_reviewed'])){                                            
                                            $item_reviewed          = array( '@type' => $item_reviewed) + $review_markup['item_reviewed'];                                        
                                            $input1['itemReviewed'] = $item_reviewed;
                                            
                                        }

                                        $added_reviews = saswp_append_fetched_reviews($input1, $schema_post_id);
                                
                                        if(isset($added_reviews['review'])){
                                            
                                            $input1['itemReviewed']['review']                    = $added_reviews['review'];
                                            $input1['itemReviewed']['aggregateRating']           = $added_reviews['aggregateRating'];
                                        
                                        }
                                        
                                        $mainentity = saswp_get_mainEntity($schema_post_id);
                                        
                                        if($mainentity){
                                         $input1['mainEntity'] = $mainentity;                                     
                                        }
                                        
                                        if(!empty($publisher)){
                                    
                                            $input1 = array_merge($input1, $publisher);   
                                
                                        }                                
                                        if(isset($sd_data['saswp_comments_schema']) && $sd_data['saswp_comments_schema'] ==1){
                                            $input1['comment'] = saswp_get_comments(get_the_ID());
                                        }                
                                                                                                                                                        
                                        $input1 = apply_filters('saswp_modify_review_newsarticle_schema_output', $input1 );
                                        
                                        $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                        
                                        if($modified_schema == 1){
                                            
                                            $input1 = saswp_review_newsarticle_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                        }
                                        
                            break;
                            
                            case 'Service':
                                                                                                 
				                $input1['@context'] =  saswp_context_url();
                                $input1['@type']    =  $schema_type;
                                $input1['@id']      =  saswp_get_permalink().'#service';   
                                                                                                                                                                                                        
                                $input1 = apply_filters('saswp_modify_service_schema_output', $input1 );
                                
                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_service_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }
                                
                            break;

                            case 'TaxiService':
                                                                                                 
				                $input1['@context'] =  saswp_context_url();
                                $input1['@type']    =  $schema_type;
                                $input1['@id']      =  saswp_get_permalink().'#TaxiService';
                                                                                                                                                                                                                                
                                $input1 = apply_filters('saswp_modify_taxi_service_schema_output', $input1 );
                                
                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_taxi_service_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }
                                
                            break;
                            
                            case 'Review':
                                                                                            
                                $review_markup = $service_object->saswp_replace_with_custom_fields_value($input1, $schema_post_id);                                
                                $item_reviewed = get_post_meta($schema_post_id, 'saswp_review_item_reviewed_'.$schema_post_id, true);
                                
                                if($item_reviewed == 'local_business'){
                                    $item_reviewed = 'LocalBusiness';
                                }
                                
                                $input1['@context']               =  saswp_context_url();
                                $input1['@type']                  =  'Review';
                                $input1['@id']                    =  saswp_get_permalink().'#Review';
                                $input1['itemReviewed']['@type']  =  $item_reviewed;                                                                
                                                            
                                if(isset($schema_options['enable_custom_field']) && $schema_options['enable_custom_field'] == 1){
                                                                       
                                    if($review_markup){
                                     
                                        if(isset($review_markup['review'])){
                                            
                                            $input1             =  $input1 + $review_markup['review'];
                                            
                                        }
                                        
                                        if(isset($review_markup['item_reviewed'])){                                            
                                            $item_reviewed          = array( '@type' => $item_reviewed) + $review_markup['item_reviewed'];                                        
                                            $input1['itemReviewed'] = $item_reviewed;
                                            
                                        }
                                        
                                    }                                                                                                                                                                                  
                                } 
                                
                                $added_reviews = saswp_append_fetched_reviews($input1, $schema_post_id);
                                
                                if(isset($added_reviews['review'])){
                                    
                                    $input1['itemReviewed']['review']                    = $added_reviews['review'];
                                    $input1['itemReviewed']['aggregateRating']           = $added_reviews['aggregateRating'];
                                
                                }                                                                                                                     
                                
                                $input1 = apply_filters('saswp_modify_review_schema_output', $input1 );
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_review_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }
                                
                            break;
                        
                            case 'VideoObject':
                                
                                $video_links      = saswp_get_video_metadata();  
                                $description = saswp_get_the_excerpt();

                                if(!$description){
                                    $description = get_bloginfo('description');
                                }  

                                $input1['@context'] = saswp_context_url();
    //                               
                                if(!empty($video_links) && count($video_links) > 1){
                                  
                                    $input1['@type'] = "ItemList";                                                       
                                    $i = 1;
                                    foreach($video_links as $vkey => $v_val){  
                                        $vnewarr = array(
                                            '@type'				            => 'VideoObject',
                                            "position"                      => $vkey+1,
                                            "@id"                           => saswp_get_permalink().'#'.$i++,
                                            'name'				            => isset($v_val['title'])? $v_val['title'] : saswp_get_the_title(),
                                            'datePublished'                 => esc_html($date),
                                            'dateModified'                  => esc_html($modified_date),
                                            'url'				            => isset($v_val['video_url'])?saswp_validate_url($v_val['video_url']):saswp_get_permalink(),
                                            'interactionStatistic'          => array(
                                                "@type" => "InteractionCounter",
                                                "interactionType" => array("@type" => "WatchAction" ),
                                                "userInteractionCount" => isset($v_val['viewCount'])? $v_val['viewCount'] : '0', 
                                                ),    
                                            'thumbnailUrl'                  => isset($v_val['thumbnail_url'])? $v_val['thumbnail_url'] : saswp_get_thumbnail(),
                                            'author'			            => saswp_get_author_details(),
                                        );

                                        if(isset($v_val['video_url'])){                                                                        
                                            $vnewarr['contentUrl']  = saswp_validate_url($v_val['video_url']);                                    
                                        }
                            
                                        if(isset($v_val['video_url'])){                                                                        
                                            $vnewarr['embedUrl']   = saswp_validate_url($v_val['video_url']);                                 
                                        }

                                        if(isset($v_val['uploadDate'])){                                                                        
                                            $vnewarr['uploadDate']   = $v_val['uploadDate'];                                    
                                        }else{
                                            $vnewarr['uploadDate']   = $date;    
                                        }

                                        if(isset($v_val['duration'])){                                                                        
                                            $vnewarr['duration']   = $v_val['duration'];                                    
                                        }

                                        if(isset($v_val['description'])){                                                                        
                                            $vnewarr['description']   = $v_val['description'];                                    
                                        }else{
                                            $vnewarr['description']   = $description;
                                        }
                                        
                                        $input1['itemListElement'][] = $vnewarr;
                                    }
                                }else{
                                   
                                    $input1 = array(
                                        '@context'			            => saswp_context_url(),
                                        '@type'				            => 'VideoObject',
                                        '@id'                           => saswp_get_permalink().'#videoobject',        
                                        'url'				            => saswp_get_permalink(),
                                        'headline'			            => saswp_get_the_title(),
                                        'datePublished'                 => esc_html($date),
                                        'dateModified'                  => esc_html($modified_date),
                                        'description'                   => $description,
                                        'transcript'                    => saswp_get_the_content(),
                                        'name'				            => saswp_get_the_title(),
                                        'uploadDate'                    => esc_html($date),
                                        'thumbnailUrl'                  => isset($video_links[0]['thumbnail_url'])? $video_links[0]['thumbnail_url'] : saswp_get_thumbnail(),
                                        'author'			            => saswp_get_author_details()						                                                                                                      
                                    );
                                    
                                    if(isset($video_links[0]['duration'])){                                                                        
                                        $input1['duration']   = $video_links[0]['duration'];                                    
                                    }
                                    if(isset($video_links[0]['video_url'])){
                                        
                                        $input1['contentUrl'] = saswp_validate_url($video_links[0]['video_url']);
                                        $input1['embedUrl']   = saswp_validate_url($video_links[0]['video_url']);
                                        
                                    }
                                    
                                    if(!empty($publisher)){

                                        $input1 = array_merge($input1, $publisher);   

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

                                    $input1 = saswp_append_fetched_reviews($input1, $schema_post_id);
                                   
                                }

                                $input1 = apply_filters('saswp_modify_video_object_schema_output', $input1 );

                                    $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                    
                                    if($modified_schema == 1){
                                        
                                        $input1 = saswp_video_object_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                    }                                

                                    if(isset($enable_videoobject) && $enable_videoobject == 1){
                                        
                                        if(isset($schema_options['enable_custom_field']) && $schema_options['enable_custom_field'] == 1){
                                            if(empty($input1['contentUrl']) && empty($input1['embedUrl'])){
                                                $input1 = array();
                                            }                                            
                                        }else{
                                            if(empty($video_links) && count($video_links) == 0 ){
                                                $input1 = array();
                                            }
                                        }

                                    }
                                 

                            break;
                        
                            case 'ImageObject':
                                     				
                                $description = saswp_get_the_excerpt();

                                if(!$description){
                                    $description = get_bloginfo('description');
                                }                                                                                                                        
                                $input1 = array(
                                '@context'			=> saswp_context_url(),
                                '@type'				=> 'ImageObject',
                                '@id'                           => saswp_get_permalink().'#imageobject',        
                                'url'				=> saswp_get_permalink(),						                                                
                                'datePublished'                 => esc_html($date),
                                'dateModified'                  => esc_html($modified_date),
                                'name'				=> saswp_get_the_title(),
                                'description'                   => $description,						
                                'contentUrl'			=> saswp_get_permalink(),						
                                'uploadDate'                    => esc_html($date),						
                                'author'			=> saswp_get_author_details()						                                                                                                      
                                );
                                 if(!empty($publisher)){

                                    $input1 = array_merge($input1, $publisher);   

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

                                $input1 = saswp_append_fetched_reviews($input1, $schema_post_id);
                                $input1 = apply_filters('saswp_modify_image_object_schema_output', $input1 );

                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_image_object_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }
				
                            break;
                        
                            case 'local_business':
                                
                                $business_type    = get_post_meta($schema_post_id, 'saswp_business_type', true);                                 
                                $business_name    = get_post_meta($schema_post_id, 'saswp_business_name', true);                                                                
                                                                                                
                                if($business_name){
                                    
                                    $local_business = $business_name;    
                                
                                }else if($business_type){
                                    
                                    $local_business = $business_type;        
                                
                                }else{
                                    $local_business = 'LocalBusiness';
                                } 
                                
                                $input1 = array(
                                                    '@context'                          => saswp_context_url(),
                                                    '@type'				=> esc_attr($local_business),
                                                    '@id'                               => saswp_get_permalink().'#'. strtolower(esc_attr($local_business)),                                            
                                                    'url'				=> saswp_get_permalink(),	
                                                    'name'				=> get_bloginfo( 'name' )							
                                );  
                                                                             
                                    if(!empty($aggregateRating)){
                                    $input1['aggregateRating'] = $aggregateRating;
                                    }                                    
                                    if(!empty($extra_theme_review)){
                                    $input1 = array_merge($input1, $extra_theme_review);
                                    }
                                    
                                    $input1 = saswp_append_fetched_reviews($input1, $schema_post_id);
                                                                                                            
                                    $input1 = apply_filters('saswp_modify_local_business_schema_output', $input1 );
                                    
                                    $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                    
                                    if($modified_schema == 1){
                                    
                                      $input1 = saswp_local_business_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);

                                    }

                                    $input1['@type'] = $local_business;
                                
                            break;
                            
                            case 'TouristTrip':
                                                   
                                $input1['@context']              = saswp_context_url();
                                $input1['@type']                 = 'TouristTrip';
                                $input1['@id']                   = saswp_get_permalink().'#TouristTrip';                                

                                $input1 = apply_filters('saswp_modify_tourist_trip_schema_output', $input1 );

                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_tourist_trip_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }

                            break;
                            
                            case 'VacationRental':

                                $input1['@context']              = saswp_context_url();
                                $input1['@type']                 = 'VacationRental';
                                $input1['@id']                   = saswp_get_permalink().'#VacationRental';                                

                                $input1 = apply_filters('saswp_modify_vacation_rental_schema_output', $input1 );

                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_vacation_rental_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }

                            break;
                            
                            case 'LearningResource':

                                $input1['@context']              = saswp_context_url();
                                $input1['@type']                 = 'LearningResource';
                                $input1['@id']                   = saswp_get_permalink().'#LearningResource';                                
                                $input1['url']                   = saswp_get_permalink();  

                                $thumbnail_id = get_post_thumbnail_id(get_the_ID());
                                $thumbnail_url = wp_get_attachment_url($thumbnail_id);
                                if(!empty($thumbnail_url) && is_string($thumbnail_url)){
                                    $image_details                   = saswp_get_image_by_url($thumbnail_url);
                                    if(!empty($image_details) && is_array($image_details)){
                                        $input1['image']         = $image_details;
                                    }
                                }    

                                $thumbnail_details   = wp_get_attachment_image_src($image_id, 'thumbnail');
                                if(is_array($thumbnail_details) && isset($thumbnail_details[0])){
                                    $image_details                   = saswp_get_image_by_url($thumbnail_details[0]);
                                    if(!empty($image_details) && is_array($image_details)){
                                        $input1['thumbnail']     = $image_details;
                                    } 
                                    $input1['thumbnailUrl']  = saswp_remove_warnings($thumbnail_details, 0, 'saswp_string');
                                }                          

                                $input1 = apply_filters('saswp_modify_learning_resource_schema_output', $input1 );

                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                                if($modified_schema == 1){
                                    
                                    $input1 = saswp_learning_resource_schema_markup($schema_post_id, get_the_ID(), $all_post_meta);
                                }

                            break;
                            
                            default:
                                break;
                           
                        }
                        
                        //Speakable schema
                        
                        if($schema_type == 'TechArticle' || $schema_type == 'Article' || $schema_type == 'Blogposting' || $schema_type == 'BlogPosting' || $schema_type == 'NewsArticle' || $schema_type == 'WebPage'){
                                           
                              $speakable_status = get_post_meta($schema_post_id, 'saswp_enable_speakable_schema', true);
                            
                              if($speakable_status){
                            
                                  $input1['speakable']['@type'] = 'SpeakableSpecification';
                                  $input1['speakable']['xpath'] = array(
                                         "/html/head/title",
                                         "/html/head/meta[@name='description']/@content"
                                    );
                                  
                              }
                                                       
                        }
                        
                        global $without_aggregate;
                        
                        if(!in_array($schema_type, $without_aggregate) && !empty($input1) ){ 
                                                     
                                
                                    if($schema_type == 'Review' || $schema_type == 'ReviewNewsArticle'){

                                    //Ratency Rating 
                            
                                    $ratency = saswp_ratency_rating_box_rating();
                                
                                    if(!empty($ratency)){
                                        $input1['itemReviewed']['aggregateRating'] = $ratency; 
                                    }
                                        
                                    //kk star rating 
                            
                                    $yasr = saswp_extract_yet_another_stars_rating();
                                
                                    if(!empty($yasr)){
                                        $input1['itemReviewed']['aggregateRating'] = $yasr; 
                                    }   
                                        
                                      //Taqyeem 
                                      
                                      $taqyeem_rating = saswp_extract_taqyeem_ratings();

                                      if(!empty($taqyeem_rating)){
                                        $input1['itemReviewed']['aggregateRating'] = $taqyeem_rating; 
                                      }
                                    //Rating Form 
                            
                                    $ratingform = saswp_extract_ratingform();
                                
                                    if(!empty($ratingform)){
                                        $input1['itemReviewed']['aggregateRating'] = $ratingform; 
                                    }  
                                    //kk star rating 
                            
                                    $kkstar_aggregateRating = saswp_extract_kk_star_ratings();
                                
                                    if(!empty($kkstar_aggregateRating)){
                                        $input1['itemReviewed']['aggregateRating'] = $kkstar_aggregateRating; 
                                    }

                                    //Rate My post
                            
                                    $rmp_aggregateRating = saswp_extract_rmp_ratings();
                                
                                    if(!empty($rmp_aggregateRating)){
                                        $input1['itemReviewed']['aggregateRating'] = $rmp_aggregateRating; 
                                    }

                                    //Comments – wpDiscuz 
                                    $wpdiscuz_aggregateRating = saswp_extract_wpdiscuz();

                                    if(!empty($wpdiscuz_aggregateRating)){
                                        $input1['aggregateRating'] = $wpdiscuz_aggregateRating; 
                                    }
                                    
                                    //wp post-rating star rating 

                                    $wp_post_rating_ar = saswp_extract_wp_post_ratings();

                                    if(!empty($wp_post_rating_ar)){
                                        $input1['itemReviewed']['aggregateRating'] = $wp_post_rating_ar; 
                                    }

                                    // WP Customer Reviews starts here
                                    $wp_customer_rv = saswp_get_wp_customer_reviews();                                    
                                    
                                    if($wp_customer_rv){                                        
                                        $input1['itemReviewed']['aggregateRating'] = $wp_customer_rv['AggregateRating'];
                                        $input1['itemReviewed']['review'] = $wp_customer_rv['reviews'];                                                                                                                              
                                    }
                                    // WP Customer Reviews ends here

                                    //Reviews wp theme starts here
                                        
                                    $reviews_wp_theme = saswp_get_reviews_wp_theme();                                    
                                        
                                    if($reviews_wp_theme){                                        
                                        $input1['itemReviewed']['aggregateRating'] = $reviews_wp_theme['AggregateRating'];
                                        $input1['itemReviewed']['review']          = $reviews_wp_theme['reviews'];                                                                                                                              
                                    }
                                    //Reviews wp theme ends here

                                    //High priority reivew which is on post itself by stars rating

                                    if(saswp_check_stars_rating() || saswp_check_starsrating_status()){

                                        $stars_rating = saswp_get_comments_with_rating();

                                        if($stars_rating) {
                                            $input1['itemReviewed']['aggregateRating'] = $stars_rating['ratings'];
                                            $input1['itemReviewed']['review']          = $stars_rating['reviews'];                                                                                                                              
                                        }
                                    }                                    
                                        
                                    }else{                                                                            

                                        //Ratency Rating 
                            
                                        $ratency = saswp_ratency_rating_box_rating();
                                    
                                        if(!empty($ratency)){
                                            $input1['aggregateRating'] = $ratency; 
                                        }

                                        //yet another star rating
                            
                                        $yasr = saswp_extract_yet_another_stars_rating();
                                    
                                        if(!empty($yasr)){
                                            $input1['aggregateRating'] = $yasr; 
                                        }

                                        //Taqyeem 
                                      
                                        $taqyeem_rating = saswp_extract_taqyeem_ratings();

                                        if(!empty($taqyeem_rating)){
                                            $input1['aggregateRating'] = $taqyeem_rating; 
                                        }

                                        //Rating Form 
                                        $ratingform = saswp_extract_ratingform();

                                        if(!empty($ratingform)){
                                            $input1['aggregateRating'] = $ratingform; 
                                        }

                                        //kk star rating 
                                        $kkstar_aggregateRating = saswp_extract_kk_star_ratings();

                                        if(!empty($kkstar_aggregateRating)){
                                            $input1['aggregateRating'] = $kkstar_aggregateRating; 
                                        }

                                        //Rate My Post rating 
                                        $rmp_aggregateRating = saswp_extract_rmp_ratings();

                                        if(!empty($rmp_aggregateRating)){
                                            $input1['aggregateRating'] = $rmp_aggregateRating; 
                                        }

                                        //Comments – wpDiscuz 
                                        $wpdiscuz_aggregateRating = saswp_extract_wpdiscuz();
                                        
                                        if(!empty($wpdiscuz_aggregateRating)){
                                            $input1['aggregateRating'] = $wpdiscuz_aggregateRating; 
                                        }

                                        //wp post-rating star rating 

                                        $wp_post_rating_ar = saswp_extract_wp_post_ratings();

                                        if(!empty($wp_post_rating_ar)){
                                            $input1['aggregateRating'] = $wp_post_rating_ar; 
                                        }

                                        // WP Customer Reviews starts here
                                        $wp_customer_rv = saswp_get_wp_customer_reviews();                                    
                                        
                                        if($wp_customer_rv){                                        
                                            $input1['aggregateRating'] = $wp_customer_rv['AggregateRating'];
                                            $input1['review'] = $wp_customer_rv['reviews'];                                                                                                                              
                                        }
                                        // WP Customer Reviews ends here

                                        //Reviews wp theme starts here
                                        
                                        $reviews_wp_theme = saswp_get_reviews_wp_theme();                                    
                                        
                                        if($reviews_wp_theme){                                        
                                            $input1['aggregateRating'] = $reviews_wp_theme['AggregateRating'];
                                            $input1['review']          = $reviews_wp_theme['reviews'];                                                                                                                              
                                        }
                                        //Reviews wp theme ends here
                                        
                                        //High priority reivew which is on post itself by stars rating

                                        if(saswp_check_stars_rating() || saswp_check_starsrating_status()){

                                            $stars_rating = saswp_get_comments_with_rating();

                                            if($stars_rating) {
                                                $input1['aggregateRating'] = $stars_rating['ratings'];
                                                $input1['review']          = $stars_rating['reviews'];                                                                                                                              
                                            }
                                        }
                                        
                                    }

                                    //Elementor Testomonials
                                    $ele_testomonials = saswp_get_elementor_testomonials();   
                                    
                                    if($ele_testomonials){
                                        
                                          $input1 = array_merge($input1,$ele_testomonials['rating']);
                                          
                                          if(isset($input1['review'])){
                                              $input1['review'] = array_merge($input1['review'],$ele_testomonials['reviews']);
                                          }else{
                                              $input1['review'] = $ele_testomonials['reviews'];
                                          }
                                          
                                    }
                                    
                                    //BNE Testomonials
                                    $bne_testomonials = saswp_get_bne_testomonials();   
                                                                        
                                    if($bne_testomonials){
                                        
                                          $input1 = array_merge($input1,$bne_testomonials['rating']);
                                          
                                          if(isset($input1['review'])){
                                              $input1['review'] = array_merge($input1['review'],$bne_testomonials['reviews']);
                                          }else{
                                              $input1['review'] = $bne_testomonials['reviews'];
                                          }
                                          
                                    }
                                    
                                    //Easy Testomonials
                                    $testomonials = saswp_get_easy_testomonials();   
                                    
                                    if($testomonials){
                                        
                                          $input1 = array_merge($input1,$testomonials['rating']);
                                          
                                          if(isset($input1['review'])){
                                            $input1['review'] = array_merge($input1['review'],$testomonials['reviews']);
                                          }else{
                                              $input1['review'] = $testomonials['reviews'];
                                          }
                                          
                                    }

                                    // Testomonial Pro
                                    $testomonial_pro = saswp_get_testomonial_pro();   
                                    
                                    if($testomonial_pro){
                                        
                                          $input1 = array_merge($input1,$testomonial_pro['rating']);
                                          
                                          if(isset($input1['review'])){
                                            $input1['review'] = array_merge($input1['review'],$testomonial_pro['reviews']);
                                          }else{
                                              $input1['review'] = $testomonial_pro['reviews'];
                                          }
                                          
                                    }
                                    
                                    
                                    // Testomonial Pro
                                    $strong_testimonials = saswp_get_strong_testimonials();   
                                    
                                    if($strong_testimonials){
                                        
                                          $input1 = array_merge($input1,$strong_testimonials['rating']);
                                          
                                          if(isset($input1['review'])){
                                            $input1['review'] = array_merge($input1['review'],$strong_testimonials['reviews']);
                                          }else{
                                              $input1['review'] = $strong_testimonials['reviews'];
                                          }
                                          
                                    }
                                    
                                    // Business Review Bundle
                                    $brb_reviews = saswp_get_brb_reviews();   
                                    
                                    if($brb_reviews){
                                        
                                          $input1 = array_merge($input1,$brb_reviews['rating']);
                                          
                                          if(isset($input1['review'])){
                                              $input1['review'] = array_merge($input1['review'],$brb_reviews['reviews']);
                                          }else{
                                              $input1['review'] = $brb_reviews['reviews'];
                                          }
                                          
                                    }
                        
                                    $input1 = apply_filters('saswp_modify_reviews_schema', $input1);
                        }                                                
                                
                        //Check for Featured Image
                        
                        if($schema_type == 'WebPage'){

                            if( !empty($input1) && !isset($input1['mainEntity']['image'])){
                                                          
                                $input2             = $service_object->saswp_get_featured_image();
                                
                                if(!empty($input2)){
                                    $input1['mainEntity'] = apply_filters('saswp_modify_featured_image', array_merge($input1['mainEntity'],$input2));
                                }                                                                    
                           }
                           if(isset($input1['mainEntity']['image']['no_image']) && $input1['mainEntity']['image']['no_image'] == 1){
                                unset($input1['image']);
                           }
                           if(isset($input1['mainEntity']['logo']['no_image']) && $input1['mainEntity']['logo']['no_image'] == 1){
                                unset($input1['logo']);
                           }

                        }else{
                            
                            if( !empty($input1) && !isset($input1['image'])){
                                                              
                                $input2             = $service_object->saswp_get_featured_image();
                                
                                if(!empty($input2)){
                                    
                                  $input1 = apply_filters('saswp_modify_featured_image', array_merge($input1,$input2) ); 
                                    
                                }                                                                    
                           }
                           
                           if(isset($input1['image']['no_image']) && $input1['image']['no_image'] == 1){
                                unset($input1['image']);
                           }
                           if(isset($input1['logo']['no_image']) && $input1['logo']['no_image'] == 1){
                                unset($input1['logo']);
                           }
                           
                        } 
                        
                        if($schema_type ==  'BreadCrumbs'){
                            unset($input1['image']);
                            unset($input1['review']);
                            unset($input1['aggregateRating']);                            
                            unset($input1['publisher']);
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
            
                $input['@context']        =  saswp_context_url();
                $input['@type']           =  'BreadcrumbList';
                $input['@id']             =  $sd_data['breadcrumb_url'].'#breadcrumb';
                $input['itemListElement'] =  $bread_crumb_list;
                                       
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
                 
                if( function_exists('pll_home_url') ) {
                    $site_url  = pll_home_url();
                }else{
                    $site_url  = get_home_url();
                }
                
		        $site_name = get_bloginfo();
                
                if($site_url && $site_name){
                 
                    $input['@context']    = saswp_context_url();
                    $input['@type']       = 'WebSite';
                    $input['@id']         = $site_url.'#website';
                    $input['headline']    = $site_name;
                    $input['name']        = $site_name;
                    $input['description'] = saswp_get_blog_desc();
                    $input['url']         = $site_url;
                                                             
                    if(isset($sd_data['saswp_search_box_schema']) && $sd_data['saswp_search_box_schema'] == 1 || !isset($sd_data['saswp_search_box_schema'])){
                        
                        $input['potentialAction']['@type']       = 'SearchAction';
                        $input['potentialAction']['target']      = esc_url($site_url).'?s={search_term_string}';
                        $input['potentialAction']['query-input'] = 'required name=search_term_string';
                        
                    }
                  }                                        
                }                		                		
	
	return apply_filters('saswp_modify_website_output', $input);       
}	

/**
 * Function to get woocommerce archive schema list
 * @global type $query_string
 * @global type $sd_data
 * @return type
 * since version: 1.9.7
 */
function saswp_woocommerce_category_schema(){
    
    global $query_string, $sd_data; 
    
    if ( function_exists('is_product_category') && is_product_category() &&  ( (isset($sd_data['saswp_woocommerce_archive']) && $sd_data['saswp_woocommerce_archive'] == 1) || !isset($sd_data['saswp_woocommerce_archive'])) ) {
            		
                $list_item     = array();
                $term          = get_queried_object();
                $service       = new saswp_output_service();                
		        $category_loop = new WP_Query( $query_string );
                
                $current_url = saswp_get_current_url();
                
                $i = 1;

                $schema_id_array = array();
                $schema_id_array = json_decode(get_transient('saswp_transient_schema_ids'), true); 
                if(!$schema_id_array){
                   $schema_id_array = saswp_get_saved_schema_ids();
                } 
                $product_schema_id = '';
                if(is_array($schema_id_array) && count($schema_id_array) > 0){
                    foreach ($schema_id_array as $sid_key => $sid_value) {
                        $schema_post_meta = get_post_meta($sid_value);
                        if(isset($schema_post_meta['schema_type']) && isset($schema_post_meta['schema_type'][0])){
                            if($schema_post_meta['schema_type'][0] == 'Product'){
                                $product_schema_id = $sid_value; 
                                break;     
                            }
                        }
                    }
                }
                
		if ( $category_loop->have_posts() ):
			while( $category_loop->have_posts() ): $category_loop->the_post();
                
                        $category_posts = array();
                        $category_posts['@type']       = 'ListItem';
                        $category_posts['position']    = $i;
                        if(isset($sd_data['saswp_woocommerce_archive_list_type']) && $sd_data['saswp_woocommerce_archive_list_type'] == 'ItemList'){
                            $category_posts['url'] = saswp_get_permalink();
                        }else{
    			            $category_posts['item']        = $service->saswp_schema_markup_generator('Product');
                            if(!empty($product_schema_id)){
                                $category_posts['item'] = saswp_append_fetched_reviews($category_posts['item'], $product_schema_id);
                                $schema_options = get_post_meta( $product_schema_id, 'schema_options', true);
                                $modified_schema    = get_post_meta(get_the_ID(), 'saswp_modify_this_schema_'.$product_schema_id, true);
                                $category_posts['item'] = saswp_get_modified_markup($category_posts['item'], 'Product', $product_schema_id, $schema_options);
                                if($modified_schema == 1){
                                    $schema_post_meta      = get_post_meta(get_the_ID(), null, null);;
                                    $category_posts['item'] = saswp_product_schema_markup($product_schema_id, get_the_ID(), $schema_post_meta);
                                }
                            }
                            $feature_image           = $service->saswp_get_featured_image();
                            $category_posts['item']  = array_merge( $category_posts['item'], $feature_image);
                            
                            if(saswp_has_slash($current_url)){
                                $category_posts['item']['url'] =  saswp_get_category_link($term->term_id). "#product_".$i;    
                            }else{
                                $category_posts['item']['url'] =  saswp_remove_slash(saswp_get_category_link($term->term_id)). "#product_".$i;    
                            }
                                                    
                            unset($category_posts['item']['@id']);
                            unset($category_posts['item']['@context']);
                        }
                        $list_item[] = $category_posts;
                            
                        $i++;
	        endwhile;

		wp_reset_postdata();
                 
                $item_list_schema = array();
                
                if($list_item){                    
                    
                    $item_list_schema['@context']        = saswp_context_url();
                    $item_list_schema['@type']           = 'ItemList';    
                    $item_list_schema['@id']             = saswp_get_category_link($term->term_id).'#ItemList';    

                    if(saswp_has_slash($current_url)){
                        $item_list_schema['url'] =  saswp_get_category_link($term->term_id);    
                    }else{                        
                        $item_list_schema['url'] =  saswp_remove_slash(saswp_get_category_link($term->term_id));    
                    }
                    
                    $item_list_schema['itemListElement'] = $list_item;
                }
                                                                                
		return $item_list_schema;
                
	endif;
        
	}
            
}

function saswp_woocommerce_shop_page(){
    
    global $sd_data;

    $collection     = array();
    $itemlist_arr   = array();
        
    if(function_exists('is_shop') && function_exists('woocommerce_get_loop_display_mode') && is_shop() && (  (isset( $sd_data['saswp_woocommerce_archive']) && $sd_data['saswp_woocommerce_archive'] == 1) || !isset( $sd_data['saswp_woocommerce_archive']) ) ){
        
        $display_type = woocommerce_get_loop_display_mode();
        $parent_id    = is_product_category() ? get_queried_object_id() : 0;
        
        if($display_type == 'subcategories' || $display_type == 'both'){
            
            $list = array();
                                                
            $product_categories = woocommerce_get_product_subcategories( $parent_id );
            
            if($product_categories){
                                                            
                foreach($product_categories as $cat){
                
                    $list[] = array(
                      '@type'    => 'ItemPage',                          
                      'url'      => saswp_get_category_link($cat->term_id),
                    );
                    
                }
                
            }
            
            if($list){
                
                $collection['@context'] = saswp_context_url();
                $collection['@type']    = 'CollectionPage';
                $collection['mainEntity']['@type']           = 'ItemList';
                $collection['mainEntity']['itemListElement'] = $list;
                
            }
            
        }
        
        if($display_type == 'products' || $display_type == 'both'){
            
            $item_list = array();
                                 
                    $i = 1;
                    if ( have_posts() ) :
                        while ( have_posts() ) :
                                  the_post();
                                   
                                       $item_list[] = array(
                                         '@type' 		=> 'ListItem',
                                         'position' 		=> $i,
                                         'url' 		        => get_the_permalink()
                                        );
                                   $i++; 
                        endwhile;
                    endif;
                                                            
                if($item_list){
                    $item_list_res['@context']        = saswp_context_url();
                    $item_list_res['@type']           = 'ItemList';
                    $item_list_res['itemListElement'] = $item_list;
                    $itemlist_arr = $item_list_res;
                }
            
        }
                        
    }
            
    return array('itemlist' => $itemlist_arr, 'collection' => $collection);
    
}

function saswp_taxonomy_schema_output(){

    $input1 = array();    

    if( is_category() || is_tag() || is_tax() || ( function_exists('is_product_category') && is_product_category() ) ){

        $term_id = get_queried_object_id();
        $input1  = get_term_meta( $term_id, 'saswp_custom_schema_field', true );        

    }    

    return $input1;

}

/**
 * Function generates archive page schema markup in the form of CollectionPage schema type
 * @global type $query_string
 * @global type $sd_data
 * @return type json
 */
function saswp_archive_output(){
    
	global $query_string, $sd_data, $wp_query;   
                
    $output = array();
    $category_posts   = array();
    $item_list        = array();  
    $collection_page  = array();
    $blog_page        = array();   
    $item_list_schema = array();    
    $product_cat      = false;

    if( function_exists('is_product_category') && is_product_category() ){
        $product_cat      = true;
    }
    
	if(isset($sd_data['saswp_archive_schema']) && $sd_data['saswp_archive_schema'] == 1){
                    
	    if ( ( is_category() || is_tag() || is_tax()) && !$product_cat ) {
            		                                   
                $i = 1;
                $category_loop = new WP_Query( $query_string );                
                
                if ( $category_loop->have_posts() ):
                    while( $category_loop->have_posts() ): $category_loop->the_post();
                                                       
                                        $result            = saswp_get_loop_markup($i);
                                        $category_posts[]  =  $result['schema_properties'];                                                                                                                                                                                                                                              
                                        
                        $i++;
                    endwhile;
                endif;				
                wp_reset_postdata();                                                
                
		        $category 		= get_queried_object(); 		
		
                if(is_object($category)){
                    
                $category_id 		= intval($category->term_id); 
                $category_link 		= get_category_link( $category_id );
		        $category_link      = get_term_link( $category_id);
                $category_headline 	= single_cat_title( '', false ) . __(' Category', 'schema-wp');	
                
                if($category_posts){
                    
                    $collection_page = array(       		
                        '@context' 		=> saswp_context_url(),
                        '@type' 		=> "CollectionPage",
                        '@id' 		    => esc_url($category_link).'#CollectionPage',
                        'headline' 		=> esc_attr($category_headline),
                        'description' 	=> strip_tags(get_term($category_id)->description),
                        'url'		 	=> esc_url($category_link),				
                        'hasPart' 		=> $category_posts
                    );

                    // Changes since version 1.15
                    if(isset($sd_data['saswp_archive_list_type']) && $sd_data['saswp_archive_list_type'] == 'DetailedItemList'){
                        if(!empty($category_posts) && isset($category_posts[0])){
                            $collection_page = array();
                            $collection_page['@context']    = saswp_context_url();
                            $collection_page['@type']       = 'ItemList';
                            $pos_cnt = 1;
                            foreach ($category_posts as $cat_key => $cat_value) {
                                $collection_page['itemListElement'][$cat_key]['@type'] = 'ListItem';
                                $collection_page['itemListElement'][$cat_key]['position'] = $pos_cnt;
                                $collection_page['itemListElement'][$cat_key]['item'] = $cat_value;
                                $pos_cnt++;
                            }
                        }
                    }
                    // Changes end
                    // Changes since version 1.20
                    if(isset($sd_data['saswp_archive_list_type']) && $sd_data['saswp_archive_list_type'] == 'ItemList'){
                        if(!empty($category_posts) && isset($category_posts[0])){
                            $collection_page = array();
                            $collection_page['@context']    = saswp_context_url();
                            $collection_page['@type']       = 'ItemList';
                            $pos_cnt = 1;
                            foreach ($category_posts as $cat_key => $cat_value) {
                                $collection_page['itemListElement'][$cat_key]['@type'] = 'ListItem';
                                $collection_page['itemListElement'][$cat_key]['position'] = $pos_cnt;
                                $collection_page['itemListElement'][$cat_key]['url'] = $cat_value['url'];
                                $pos_cnt++;
                            }
                        }
                    }
                    // Changes end

                    $blog_page = array(       		
                        '@context' 		=> saswp_context_url(),
                        '@type' 		=> "Blog",
                        '@id' 		    => esc_url($category_link).'#Blog',
                        'headline' 		=> esc_attr($category_headline),
                        'description' 	=> strip_tags(get_term($category_id)->description),
                        'url'		 	=> esc_url($category_link),				
                        'blogPost' 		=> $category_posts
                    );

                }                               
           }
        }   

        $homepage = false;
        
        if(saswp_non_amp()){
            
            if( is_home() && !is_front_page() ){
                $homepage = true;
            }
        }else{
            if( (function_exists('ampforwp_is_home') && ampforwp_is_home()) && (function_exists('ampforwp_is_front_page') && !ampforwp_is_front_page()) ){            
                $homepage = true;
            }
        }
        
        if( $homepage ){
            
            $home_query_string = array(
                'posts_per_page' => 10
            );
            
            if($wp_query->query_vars['posts_per_page']){
                $home_query_string = array(
                    'posts_per_page' => $wp_query->query_vars['posts_per_page']
                );  
            }
            
            $homepage_loop = new WP_Query( $home_query_string );                

            $i = 1;
            if ( $homepage_loop->have_posts() ):
                while( $homepage_loop->have_posts() ): $homepage_loop->the_post();
                                                   
                                    $result            = saswp_get_loop_markup($i);
                                    $category_posts[]  =  $result['schema_properties'];                                                                                                                                                                                                        
                                    $item_list[]       = $result['itemlist'];
                                    
                    $i++;
                endwhile;
            endif;					
            wp_reset_postdata(); 

                if($category_posts){

                    $blog_page = array(       		
                        '@context' 		=> saswp_context_url(),
                        '@type' 		=> "Blog",
                        '@id' 		    => get_site_url().'#Blog',				
                        'url'		 	=> get_site_url(),				
                        'blogPost' 		=> $category_posts
                       );
                }            

            } 
                if($item_list){

                    $item_list_schema['@context']        = saswp_context_url();
                    $item_list_schema['@type']           = 'ItemList';
                    $item_list_schema['itemListElement'] = $item_list;

                }                
                                                       
                if(isset($sd_data['saswp_archive_schema_type']) && $sd_data['saswp_archive_schema_type'] == 'BlogPosting'){
                    $output = array($item_list_schema, array(), $blog_page);
                }else{
                    $output = array($item_list_schema, $collection_page, array());
                }
                                                		    
	}         
        return apply_filters('saswp_modify_archive_output', $output);
}

/**
 * Function generates author schema markup
 * @global type $post
 * @global type $sd_data
 * @return type json
 */ 
function saswp_author_output(){
    
	global $post, $sd_data;   
           $post_id = null;
           $input   = array();

	if(isset($sd_data['saswp_archive_schema']) && $sd_data['saswp_archive_schema'] == 1){
            
        if(is_object($post)){
        
            $post_id = $post->ID;
            
        }    
            	
	if(is_author() && $post_id){
		// Get author from post content
		$post_content	= get_post($post_id);                
		$post_author	= get_userdata($post_content->post_author);		

        if(is_object($post_author) && isset($post_author->ID)){

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
            $sd_tumblr 	    = esc_attr( stripslashes( get_the_author_meta( 'tumblr', $post_author->ID ) ) );
            
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
        $page_ids      = array();
        $page_ids[]    = get_the_ID();        
		
        $page_ids      = apply_filters( 'saswp_modify_about_page_ids', $page_ids);
                                        
	if((isset($sd_data['sd_about_page'])) && in_array($sd_data['sd_about_page'], $page_ids) ){   
            
                        $service_object     = new saswp_output_service();
                        $feature_image      = $service_object->saswp_get_featured_image();
                        $publisher          = $service_object->saswp_get_publisher();
                        
			$input = array(
				"@context" 	   => saswp_context_url(),
				"@type"		   => "AboutPage",
				"mainEntityOfPage" => array(
                                                            "@type"           => "WebPage",
                                                            "@id"             => saswp_get_permalink(),
						),
				"url"		   => saswp_get_permalink(),
				"headline"	   => saswp_get_the_title(),								
				'description'	   => saswp_get_the_excerpt(),
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
        $page_ids      = array();
        $page_ids[]    = get_the_ID();
        
        $page_ids      = apply_filters( 'saswp_modify_contact_page_ids', $page_ids);
        
	if(isset($sd_data['sd_contact_page']) && in_array($sd_data['sd_contact_page'], $page_ids ) ){
                        
                        $service_object     = new saswp_output_service();
                        $feature_image      = $service_object->saswp_get_featured_image();
                        $publisher          = $service_object->saswp_get_publisher();
                        			
			$input = array(
                            
				"@context" 	    => saswp_context_url(),
				"@type"		    => "ContactPage",
				"mainEntityOfPage"  => array(
							"@type" => "WebPage",
							"@id" 	=> saswp_get_permalink(),
							),
				"url"		   => saswp_get_permalink(),
				"headline"	   => saswp_get_the_title(),								
				'description'	   => saswp_get_the_excerpt(),
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

    $navObj = array();          
                               
    if(isset($sd_data['saswp_site_navigation_menu'])){
        
        $menu_id   = $sd_data['saswp_site_navigation_menu'];

        $menu_id    = apply_filters('saswp_modify_menu_id', $menu_id);
        
        $menuItems = get_transient('saswp_nav_menu'.$menu_id);
                
        if(!$menuItems){
            $menuItems = wp_get_nav_menu_items($menu_id);
            set_transient('saswp_nav_menu'.$menu_id, $menuItems);
        }
        
        $menu_name = wp_get_nav_menu_object($menu_id);
        if(!empty($menu_name) && !empty($menu_name->name)){
			$menu_name = $menu_name->name;
		}else{
			$menu_name = "";
		}

        $current_post_language = apply_filters( 'wpml_post_language_details', NULL);
                                     
        if(!empty($menuItems)){
           
                foreach($menuItems as $items){

                        if(isset($items->type) && $items->type == 'wpml_ls_menu_item'){
                            if(isset($items->attr_title) && !empty($items->attr_title)){
                                $menu_title = $items->attr_title;
                                if(!is_wp_error($current_post_language) && isset($current_post_language['display_name'])){
                                    $selected_language = $current_post_language['display_name'];
                                    if(strpos($selected_language, $menu_title) === false){
                                        continue;
                                    }
                                }
                            }
                        }
                        
                      $utm_response = saswp_remove_utm_parameters_from_url($items->url);
                      if($utm_response['flag'] == 0){
                          $navObj[] = array(
                                 "@context"  => saswp_context_url(),
                                 "@type"     => "SiteNavigationElement",
                                 "@id"       => get_home_url().'#'.$menu_name,
                                 "name"      => wp_strip_all_tags($items->title),
                                 "url"       => esc_url($utm_response['url'])
                          );
                        }

                }                                                                                                                                                                                   
            }
     
            if($navObj){

                $input['@context'] = saswp_context_url(); 
                $input['@graph']   = $navObj; 

            }
            
    }                                                
                                
    return apply_filters('saswp_modify_sitenavigation_output', $input);
}  

function saswp_fetched_reviews_json_ld(){
    
    global $sd_data;
    $input1 = array();
    
    if(!(is_home() || is_front_page() || ( function_exists('ampforwp_is_home') && ampforwp_is_home())) || (isset($sd_data['saswp_kb_type']) && $sd_data['saswp_kb_type'] == 'Person') ){
    
        $json_ld = saswp_fetched_reviews_schema_markup();
    
        if($json_ld){

            $input1['@context'] = saswp_context_url();
            $input1['@type']    = (isset($sd_data['saswp_organization_type']) && $sd_data['saswp_organization_type'] !='' && (isset($sd_data['saswp_kb_type']) && $sd_data['saswp_kb_type'] == 'Organization' )  )? $sd_data['saswp_organization_type'] : 'Organization';
            $input1['name']     = (isset($sd_data['sd_name']) && $sd_data['sd_name'] !='' )? $sd_data['sd_name'] : get_bloginfo();

            $input1  = array_merge($input1, $json_ld); 

        }
        
    }
    
    return $input1;
    
}

function saswp_fetched_user_custom_schema(){
    
    global $sd_data;
    $author_id      = get_the_author_meta('ID');	     
    if($author_id){
            return get_user_meta($author_id, 'saswp_user_custom_schema_field', true);
    }else{
        return '';
    }    
}

function saswp_fetched_reviews_schema_markup(){
        
                    global $saswp_post_reviews, $with_aggregate;
                  
                    $input1 = array();
                  
                    if($saswp_post_reviews){
                        
                        $added_type = array();
                        
                        $all_schema = saswp_get_all_schema_posts();
                        
                        if(is_array($all_schema)){
                            $added_type =  array_column($all_schema, 'schema_type');   
                        }

                        $status = false;
                        
                        foreach($with_aggregate as $value){
                            
                            if(in_array($value, $added_type)){
                                
                                $status = true;                                                                
                                break;
                                
                            }
                            
                        }
                        
                        if(!$status){
                            $input1 = saswp_get_reviews_schema_markup(array_unique($saswp_post_reviews, SORT_REGULAR));                                                                                                                       
                        }
                                                
                    }
                  
                return $input1;
}

/**
 * Remove UTM parameters from URL
 * @since 1.25
 * @date 15-12-2023
 * @param $url  String
 * @return $response  Array
 * */
function saswp_remove_utm_parameters_from_url($url = '')
{
    $response = array(); 
    $response['flag'] = 0;
    $response['url'] = $url;
    $url = esc_url($url);
    if(!empty($url)){
        if($url == '#'){
            $response['flag'] = 1;  
          }else{
            if(is_string($url) && !empty($url)){
                $explode_url = explode('?', $url);
                if(!empty($explode_url) && is_array($explode_url)){
                    if(isset($explode_url[1]) && !empty($explode_url[1])){
                        if(is_string($explode_url[1])){
                            $host_url = $explode_url[0];
                            $explode_qs = explode('&', $explode_url[1]);
                            if(!empty($explode_qs) && is_array($explode_qs)){
                                $valid_qs = array();
                                $valid_utm_code = array('utm_source','utm_medium','utm_campaign','utm_term','utm_content');
                                foreach ($explode_qs as $qs_key => $qs_value) {
                                    $utm_flag = 0;
                                    foreach ($valid_utm_code as $vuc_key => $vuc_value) {
                                        if(strpos($qs_value, $vuc_value) !== false){
                                            $utm_flag = 1;
                                        }
                                    }
                                    if($utm_flag == 0){
                                        $valid_qs[] = $qs_value;   
                                    }
                                }
                                if(!empty($valid_qs)){
                                    $valid_qs = implode('&', $valid_qs);
                                    $response['url'] = $host_url.'?'.$valid_qs;
                                }else{
                                    $response['url'] = $host_url;    
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    return $response;
}