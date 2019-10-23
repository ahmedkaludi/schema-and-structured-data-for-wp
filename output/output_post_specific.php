<?php
/**
 * Output Page
 *
 * @author   Magazine3
 * @category Frontend
 * @path  output_post_specific/output_post_specific
 * @version 1.0
 */
if (! defined('ABSPATH') ) exit;

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
	$schema_type    = get_post_meta($schema_id, 'schema_type', true);        
        $schema_post_id = $post->ID;  
	$all_post_meta  = get_post_meta($schema_post_id, $key='', true);     
	
	if(is_singular() && (isset($schema_enable[$schema_id]) && $schema_enable[$schema_id] == 1 )){
		
                        $saswp_review_details = get_post_meta(get_the_ID(), 'saswp_review_details', true); 
                        
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
            
                        
                         if( 'Person' === $schema_type){
                             
                            $image = get_post_meta( get_the_ID(), 'saswp_trip_schema_image_'.$schema_id.'_detail',true); 
                                                                                                            
                            $input1['@context']              = saswp_context_url();
                            $input1['@type']                 = 'Person';
                            $input1['@id']                   = trailingslashit(get_permalink()).'#Person';
                            $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_person_schema_url_'.$schema_id, 'saswp_array');                            
                            $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_person_schema_name_'.$schema_id, 'saswp_array');                                                        
                            $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_person_schema_description_'.$schema_id, 'saswp_array');                                                        
                            $input1['gender']                = saswp_remove_warnings($all_post_meta, 'saswp_person_schema_gender_'.$schema_id, 'saswp_array');                                                        
                            $input1['birthDate']             = isset($all_post_meta['saswp_person_schema_date_of_birth_'.$schema_id][0])&& $all_post_meta['saswp_person_schema_date_of_birth_'.$schema_id][0] !='' ? date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_person_schema_date_of_birth_'.$schema_id][0])):'';
                            $input1['nationality']           = saswp_remove_warnings($all_post_meta, 'saswp_person_schema_nationality_'.$schema_id, 'saswp_array');                                                        
                            $input1['jobTitle']              = saswp_remove_warnings($all_post_meta, 'saswp_person_schema_job_title_'.$schema_id, 'saswp_array');                                                        
                            
                            $input1['address']['@type']             = 'PostalAddress';
                            $input1['address']['streetAddress']     = saswp_remove_warnings($all_post_meta, 'saswp_person_schema_street_address_'.$schema_id, 'saswp_array');
                            $input1['address']['addressCountry']    = saswp_remove_warnings($all_post_meta, 'saswp_person_schema_country_'.$schema_id, 'saswp_array');
                            $input1['address']['addressLocality']   = saswp_remove_warnings($all_post_meta, 'saswp_person_schema_locality_'.$schema_id, 'saswp_array');
                            $input1['address']['addressRegion']     = saswp_remove_warnings($all_post_meta, 'saswp_person_schema_region_'.$schema_id, 'saswp_array');
                            $input1['address']['PostalCode']        = saswp_remove_warnings($all_post_meta, 'saswp_person_schema_postal_code_'.$schema_id, 'saswp_array');
                            
                            $input1['telephone']                    = saswp_remove_warnings($all_post_meta, 'saswp_person_schema_telephone_'.$schema_id, 'saswp_array');                                                        
                            $input1['email']                        = saswp_remove_warnings($all_post_meta, 'saswp_person_schema_email_'.$schema_id, 'saswp_array');                                                        
                                                                                      
                            if(!(empty($image))){

                            $input1['image']['@type']        = 'ImageObject';
                            $input1['image']['url']          = isset($image['thumbnail']) ? esc_url($image['thumbnail']):'';
                            $input1['image']['height']       = isset($image['width'])     ? esc_attr($image['width'])   :'';
                            $input1['image']['width']        = isset($image['height'])    ? esc_attr($image['height'])  :'';

                            }
                            
                         }         
                        
                         if( 'Trip' === $schema_type){
                             
                            $howto_image = get_post_meta( get_the_ID(), 'saswp_trip_schema_image_'.$schema_id.'_detail',true); 
                            
                                                                                   
                            $input1['@context']              = saswp_context_url();
                            $input1['@type']                 = 'Trip';
                            $input1['@id']                   = trailingslashit(get_permalink()).'#Trip';
                            $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_trip_schema_url_'.$schema_id, 'saswp_array');                            
                            $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_trip_schema_name_'.$schema_id, 'saswp_array');                            
                            $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_trip_schema_description_'.$schema_id, 'saswp_array');                            
                                                                                      
                            if(!(empty($howto_image))){
                             
                            $input1['image']['@type']        = 'ImageObject';
                            $input1['image']['url']          = isset($howto_image['thumbnail']) ? esc_url($howto_image['thumbnail']):'';
                            $input1['image']['height']       = isset($howto_image['width'])     ? esc_attr($howto_image['width'])   :'';
                            $input1['image']['width']        = isset($howto_image['height'])    ? esc_attr($howto_image['height'])  :'';
                                
                            }
                                                       
                            $itinerary  = get_post_meta($schema_post_id, 'trip_itinerary_'.$schema_id, true);
                            
                            $itinerary_arr = array();
                            
                            if(!empty($itinerary)){
                                
                                foreach($itinerary as $val){
                                   
                                    $supply_data = array();
                                    $supply_data['@type']        = $val['saswp_trip_itinerary_type'];
                                    $supply_data['name']         = $val['saswp_trip_itinerary_name'];
                                    $supply_data['description']  = $val['saswp_trip_itinerary_description'];
                                    $supply_data['url']          = $val['saswp_trip_itinerary_url'];
                                    
                                    
                                   $itinerary_arr[] =  $supply_data;
                                }
                               $input1['itinerary'] = $itinerary_arr;
                            }
                            
                            }   
                            
                         if( 'FAQ' === $schema_type){
                                                                                                                                                                        
                            $input1['@context']              = saswp_context_url();
                            $input1['@type']                 = 'FAQPage';
                            $input1['@id']                   = trailingslashit(get_permalink()).'#FAQPage';  
                            
                            $input1['headline']              = saswp_remove_warnings($all_post_meta, 'saswp_faq_headline_'.$schema_id, 'saswp_array');                                                        
                            $input1['keywords']              = saswp_remove_warnings($all_post_meta, 'saswp_faq_keywords_'.$schema_id, 'saswp_array');                                                        
                            $input1['datePublished']         = isset($all_post_meta['saswp_faq_date_published_'.$schema_id][0])&& $all_post_meta['saswp_faq_date_published_'.$schema_id][0] !='' ? date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_faq_date_published_'.$schema_id][0])):'';
                            $input1['dateModified']          = isset($all_post_meta['saswp_faq_date_modified_'.$schema_id][0])&& $all_post_meta['saswp_faq_date_modified_'.$schema_id][0] !='' ? date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_faq_date_modified_'.$schema_id][0])):'';
                            $input1['dateCreated']           = isset($all_post_meta['saswp_faq_date_created_'.$schema_id][0])&& $all_post_meta['saswp_faq_date_created_'.$schema_id][0] !='' ? date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_faq_date_created_'.$schema_id][0])):'';
                            
                            if(isset($all_post_meta['saswp_faq_author_'.$schema_id][0])){
                                $input1['author']['@type']       = 'Person';
                                $input1['author']['name']        = $all_post_meta['saswp_faq_author_'.$schema_id][0];
                            }
                            
                            $faq_question  = get_post_meta($schema_post_id, 'faq_question_'.$schema_id, true);
                            
                            $faq_question_arr = array();
                            
                            if(!empty($faq_question)){
                                
                                foreach($faq_question as $val){
                                   
                                    $supply_data = array();
                                    $supply_data['@type']                   = 'Question';
                                    $supply_data['name']                    = $val['saswp_faq_question_name'];
                                    $supply_data['acceptedAnswer']['@type'] = 'Answer';
                                    $supply_data['acceptedAnswer']['text']  = $val['saswp_faq_question_answer'];
                                                                        
                                   $faq_question_arr[] =  $supply_data;
                                }
                               $input1['mainEntity'] = $faq_question_arr;
                            }
                            
                          }  
                          
                          if( 'MusicPlaylist' === $schema_type){
                                                                                                                                                                        
                            $input1['@context']              = saswp_context_url();
                            $input1['@type']                 = 'MusicPlaylist';
                            $input1['@id']                   = trailingslashit(get_permalink()).'#MusicPlaylist';                            
                            $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_music_playlist_url_'.$schema_id, 'saswp_array');                                
                            $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_music_playlist_name_'.$schema_id, 'saswp_array');                            
                            $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_music_playlist_description_'.$schema_id, 'saswp_array');                                
                            
                            $faq_question  = get_post_meta($schema_post_id, 'music_playlist_track_'.$schema_id, true);
                            
                            
                            $faq_question_arr = array();
                            
                            if(!empty($faq_question)){
                                
                                $input1['numTracks'] = count($faq_question);
                                
                                foreach($faq_question as $val){
                                   
                                    $supply_data = array();
                                    $supply_data['@type']                   = 'MusicRecording';
                                    $supply_data['byArtist']                = $val['saswp_music_playlist_track_artist'];
                                    $supply_data['duration']                = $val['saswp_music_playlist_track_duration'];
                                    $supply_data['inAlbum']                 = $val['saswp_music_playlist_track_inalbum'];
                                    $supply_data['name']                    = $val['saswp_music_playlist_track_name'];
                                    $supply_data['url']                     = $val['saswp_music_playlist_track_url'];
                                                                                                                                                
                                   $faq_question_arr[] =  $supply_data;
                                }
                               $input1['track'] = $faq_question_arr;
                            }
                            
                            $input1 = saswp_append_fetched_reviews($input1);
                            
                          }     
                          
                          if( 'MusicAlbum' === $schema_type){
                                                                                                                                                                        
                            $input1['@context']              = saswp_context_url();
                            $input1['@type']                 = 'MusicAlbum';
                            $input1['@id']                   = trailingslashit(get_permalink()).'#MusicAlbum';                            
                            $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_music_album_url_'.$schema_id, 'saswp_array');                                
                            $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_music_album_name_'.$schema_id, 'saswp_array');                            
                            $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_music_album_description_'.$schema_id, 'saswp_array');                                
                            $input1['genre']                 = saswp_remove_warnings($all_post_meta, 'saswp_music_album_genre_'.$schema_id, 'saswp_array');                                                            
                            
                            
                            if(isset($all_post_meta['saswp_music_album_artist_'.$schema_id][0])){
                                
                                $input1['byArtist']['@type']     = 'MusicGroup';
                                $input1['byArtist']['name']      = $all_post_meta['saswp_music_album_artist_'.$schema_id][0];
                                
                            }
                            
                            $howto_image = get_post_meta( get_the_ID(), 'saswp_music_album_image_'.$schema_id.'_detail',true); 
                                                         
                            if(!(empty($howto_image))){
                             
                            $input1['image']['@type']        = 'ImageObject';
                            $input1['image']['url']          = isset($howto_image['thumbnail']) ? esc_url($howto_image['thumbnail']):'';
                            $input1['image']['height']       = isset($howto_image['width'])     ? esc_attr($howto_image['width'])   :'';
                            $input1['image']['width']        = isset($howto_image['height'])    ? esc_attr($howto_image['height'])  :'';
                                
                            }
                            
                            $faq_question  = get_post_meta($schema_post_id, 'music_album_track_'.$schema_id, true);
                            
                            $faq_question_arr = array();
                            
                            if(!empty($faq_question)){
                                
                                $input1['numTracks'] = count($faq_question);
                                
                                foreach($faq_question as $val){
                                   
                                    $supply_data = array();
                                    $supply_data['@type']                   = 'MusicRecording';                                    
                                    $supply_data['duration']                = $val['saswp_music_album_track_duration'];                                    
                                    $supply_data['name']                    = $val['saswp_music_album_track_name'];
                                    $supply_data['url']                     = $val['saswp_music_album_track_url'];                                                                                                                                                
                                   $faq_question_arr[] =  $supply_data;
                                }
                               $input1['track'] = $faq_question_arr;
                            }
                            
                            $input1 = saswp_append_fetched_reviews($input1);
                            
                          }
                            
                          if( 'JobPosting' === $schema_type){
                             
                            $howto_image = get_post_meta( get_the_ID(), 'saswp_jobposting_schema_ho_logo_'.$schema_id.'_detail',true); 
                                                                                                               
                            $input1['@context']              = saswp_context_url();
                            $input1['@type']                 = 'JobPosting';
                            $input1['@id']                   = trailingslashit(get_permalink()).'#JobPosting';
                            $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_url_'.$schema_id, 'saswp_array');                            
                            $input1['title']                 = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_title_'.$schema_id, 'saswp_array');                            
                            $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_description_'.$schema_id, 'saswp_array');
                            $input1['datePosted']            = isset($all_post_meta['saswp_jobposting_schema_dateposted_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_jobposting_schema_dateposted_'.$schema_id][0])):'';                            
                            $input1['validThrough']          = isset($all_post_meta['saswp_jobposting_schema_validthrough_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_jobposting_schema_validthrough_'.$schema_id][0])):'';                            
                            $input1['employmentType']        = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_employment_type_'.$schema_id, 'saswp_array');
                              
                            $input1['hiringOrganization']['@type']     = 'Organization';
                            $input1['hiringOrganization']['name']      = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_ho_name_'.$schema_id, 'saswp_array');
                            $input1['hiringOrganization']['sameAs']    = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_ho_url_'.$schema_id, 'saswp_array');
                            
                            if(!(empty($howto_image))){
                             
                            $input1['hiringOrganization']['logo']['@type']        = 'ImageObject';
                            $input1['hiringOrganization']['logo']['url']          = isset($howto_image['thumbnail']) ? esc_url($howto_image['thumbnail']):'';
                            $input1['hiringOrganization']['logo']['height']       = isset($howto_image['width'])     ? esc_attr($howto_image['width'])   :'';
                            $input1['hiringOrganization']['logo']['width']        = isset($howto_image['height'])    ? esc_attr($howto_image['height'])  :'';
                                
                            }
                            
                            $input1['jobLocation']['@type']                        = 'Place';
                            $input1['jobLocation']['address']['@type']             = 'PostalAddress';                            
                            $input1['jobLocation']['address']['streetAddress']     = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_street_address_'.$schema_id, 'saswp_array');
                            $input1['jobLocation']['address']['addressLocality']   = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_locality_'.$schema_id, 'saswp_array');
                            $input1['jobLocation']['address']['addressRegion']     = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_region_'.$schema_id, 'saswp_array');
                            $input1['jobLocation']['address']['addressCountry']    = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_country_'.$schema_id, 'saswp_array');
                            $input1['jobLocation']['address']['PostalCode']        = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_postalcode_'.$schema_id, 'saswp_array');

                            
                            $input1['baseSalary']['@type']             = 'MonetaryAmount';
                            $input1['baseSalary']['currency']          = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_bs_currency_'.$schema_id, 'saswp_array');
                            $input1['baseSalary']['value']['@type']    = 'QuantitativeValue';
                            $input1['baseSalary']['value']['value']    = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_bs_value_'.$schema_id, 'saswp_array');
                            $input1['baseSalary']['value']['unitText'] = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_bs_unittext_'.$schema_id, 'saswp_array');
                                                        
                            }      
                        
                          if( 'Mosque' === $schema_type){
                             
                            $howto_image = get_post_meta( get_the_ID(), 'saswp_mosque_schema_image_'.$schema_id.'_detail',true); 
                            
                                                                                   
                            $input1['@context']              = saswp_context_url();
                            $input1['@type']                 = 'Mosque';
                            $input1['@id']                   = trailingslashit(get_permalink()).'#Mosque';
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
                            
                                                                                   
                            $input1['@context']              = saswp_context_url();
                            $input1['@type']                 = 'Church';
                            $input1['@id']                   = trailingslashit(get_permalink()).'#Church';
                            $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_church_schema_url_'.$schema_id, 'saswp_array');                            
                            $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_church_schema_name_'.$schema_id, 'saswp_array');                            
                            $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_church_schema_description_'.$schema_id, 'saswp_array');
                              
                            if(!(empty($howto_image))){
                             
                            $input1['image']['@type']        = 'ImageObject';
                            $input1['image']['url']          = isset($howto_image['thumbnail']) ? esc_url($howto_image['thumbnail']):'';
                            $input1['image']['height']       = isset($howto_image['width'])     ? esc_attr($howto_image['width'])   :'';
                            $input1['image']['width']        = isset($howto_image['height'])    ? esc_attr($howto_image['height'])  :'';
                                
                            }  
                            
                            $input1['isAccessibleForFree']        = saswp_remove_warnings($all_post_meta, 'saswp_church_schema_is_acceesible_free_'.$schema_id, 'saswp_array');                           
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
                            
                                                                                   
                            $input1['@context']              = saswp_context_url();
                            $input1['@type']                 = 'HinduTemple';
                            $input1['@id']                   = trailingslashit(get_permalink()).'#HinduTemple';
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
                            
                                                                                   
                            $input1['@context']              = saswp_context_url();
                            $input1['@type']                 = 'LandmarksOrHistoricalBuildings';
                            $input1['@id']                   = trailingslashit(get_permalink()).'#LandmarksOrHistoricalBuildings';
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
                         
                         if( 'Book' === $schema_type){
                             
                            $howto_image = get_post_meta( get_the_ID(), 'saswp_book_image_'.$schema_id.'_detail',true); 
                                                                                                               
                            $input1['@context']              = saswp_context_url();
                            $input1['@type']                 = 'Book';
                            $input1['@id']                   = trailingslashit(get_permalink()).'#Book';
                            $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_book_url_'.$schema_id, 'saswp_array');                            
                            $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_book_name_'.$schema_id, 'saswp_array');                            
                            $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_book_description_'.$schema_id, 'saswp_array');
                              
                            if(!(empty($howto_image))){
                             
                            $input1['image']['@type']        = 'ImageObject';
                            $input1['image']['url']          = isset($howto_image['thumbnail']) ? esc_url($howto_image['thumbnail']):'';
                            $input1['image']['height']       = isset($howto_image['width'])     ? esc_attr($howto_image['width'])   :'';
                            $input1['image']['width']        = isset($howto_image['height'])    ? esc_attr($howto_image['height'])  :'';
                                
                            }  
                            
                            $input1['author']               = saswp_remove_warnings($all_post_meta, 'saswp_book_author_'.$schema_id, 'saswp_array');                        
                            $input1['datePublished']        = isset($all_post_meta['saswp_book_date_published_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_book_date_published_'.$schema_id][0])):'';
                            $input1['isbn']                 = saswp_remove_warnings($all_post_meta, 'saswp_book_isbn_'.$schema_id, 'saswp_array');                          
                            $input1['numberOfPages']        = saswp_remove_warnings($all_post_meta, 'saswp_book_no_of_page_'.$schema_id, 'saswp_array');                          
                            $input1['publisher']            = saswp_remove_warnings($all_post_meta, 'saswp_book_publisher_'.$schema_id, 'saswp_array');                          
                                                                                     
                            if(isset($all_post_meta['saswp_book_price_'.$schema_id]) && isset($all_post_meta['saswp_book_price_currency_'.$schema_id])){
                                $input1['offers']['@type']         = 'Offer';
                                $input1['offers']['availability']  = saswp_remove_warnings($all_post_meta, 'saswp_book_availability_'.$schema_id, 'saswp_array');
                                $input1['offers']['price']         = $all_post_meta['saswp_book_price_'.$schema_id];
                                $input1['offers']['priceCurrency'] = $all_post_meta['saswp_book_price_currency_'.$schema_id];
                            }
                            
                            if(isset($all_post_meta['saswp_book_enable_rating_'.$schema_id]) && isset($all_post_meta['saswp_book_rating_value_'.$schema_id]) && isset($all_post_meta['saswp_book_rating_count_'.$schema_id])){
                                $input1['aggregateRating']['@type']         = 'aggregateRating';
                                $input1['aggregateRating']['ratingValue']   = $all_post_meta['saswp_book_rating_value_'.$schema_id];
                                $input1['aggregateRating']['ratingCount']   = $all_post_meta['saswp_book_rating_count_'.$schema_id];                                
                            }
                            
                                $input1 = saswp_append_fetched_reviews($input1);
                            
                            }    
                        
                         if( 'TouristAttraction' === $schema_type){
                             
                            $howto_image = get_post_meta( get_the_ID(), 'saswp_ta_schema_image_'.$schema_id.'_detail',true); 
                            
                                                                                   
                            $input1['@context']              = saswp_context_url();
                            $input1['@type']                 = 'TouristAttraction';
                            $input1['@id']                   = trailingslashit(get_permalink()).'#TouristAttraction';
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
                            
                            $input1['address']['@type']             = 'PostalAddress';
                            $input1['address']['addressCountry']    = saswp_remove_warnings($all_post_meta, 'saswp_ta_schema_address_country_'.$schema_id, 'saswp_array');
                            $input1['address']['addressLocality']   = saswp_remove_warnings($all_post_meta, 'saswp_ta_schema_address_locality_'.$schema_id, 'saswp_array');
                            $input1['address']['addressRegion']     = saswp_remove_warnings($all_post_meta, 'saswp_ta_schema_address_region_'.$schema_id, 'saswp_array');
                            $input1['address']['PostalCode']        = saswp_remove_warnings($all_post_meta, 'saswp_ta_schema_address_postal_code_'.$schema_id, 'saswp_array');
                                                                                   
                            }
                         
                         if( 'TouristDestination' === $schema_type){
                             
                            $howto_image = get_post_meta( get_the_ID(), 'saswp_td_schema_image_'.$schema_id.'_detail',true); 
                                                                                                               
                            $input1['@context']              = saswp_context_url();
                            $input1['@type']                 = 'TouristDestination';
                            $input1['@id']                   = trailingslashit(get_permalink()).'#TouristDestination';
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
                                                                                                               
                            $input1['@context']              = saswp_context_url();
                            $input1['@type']                 = 'Apartment';
                            $input1['@id']                   = trailingslashit(get_permalink()).'#Apartment';
                            $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_apartment_schema_url_'.$schema_id, 'saswp_array');                            
                            $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_apartment_schema_name_'.$schema_id, 'saswp_array');                            
                            $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_apartment_schema_description_'.$schema_id, 'saswp_array');
                            $input1['floorSize']             = saswp_remove_warnings($all_post_meta, 'saswp_apartment_schema_floor_size_'.$schema_id, 'saswp_array');
                              
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
                            $input1['address']['PostalCode']        = saswp_remove_warnings($all_post_meta, 'saswp_apartment_schema_postalcode_'.$schema_id, 'saswp_array');
                            
                            $input1['telephone']                    = saswp_remove_warnings($all_post_meta, 'saswp_apartment_schema_telephone_'.$schema_id, 'saswp_array');
                            
                            if(isset($all_post_meta['saswp_apartment_schema_latitude_'.$schema_id][0]) && isset($all_post_meta['saswp_apartment_schema_longitude_'.$schema_id][0])){
                            
                                    $input1['geo']['@type']     = 'GeoCoordinates';
                                    $input1['geo']['latitude']  = $all_post_meta['saswp_apartment_schema_latitude_'.$schema_id][0];
                                    $input1['geo']['longitude'] = $all_post_meta['saswp_apartment_schema_longitude_'.$schema_id][0];
                                    
                            }
                            
                            $itinerary  = get_post_meta($schema_post_id, 'apartment_amenities_'.$schema_id, true);
                            
                            $itinerary_arr = array();
                            
                            if(!empty($itinerary)){
                                
                                foreach($itinerary as $val){
                                   
                                    $supply_data = array();
                                    $supply_data['@type']        = 'LocationFeatureSpecification';
                                    $supply_data['name']         = $val['saswp_apartment_amenities_name'];                                                                        
                                                                        
                                   $itinerary_arr[] =  $supply_data;
                                }
                                
                                $input1['amenityFeature'] = $itinerary_arr;
                            }
                            
                            $add_property     = get_post_meta($schema_post_id, 'additional_property_'.$schema_id, true);
                            
                            $add_property_arr = array();
                            
                            if(!empty($add_property)){
                                
                                foreach($add_property as $val){
                                   
                                    $supply_data = array();
                                    $supply_data['@type']                                                  = 'PropertyValue';
                                    $supply_data['name']                                                   = $val['saswp_apartment_additional_property_name'];
                                    $supply_data[$val['saswp_apartment_additional_property_code_type']]    = $val['saswp_apartment_additional_property_code_value'];
                                    $supply_data['value']                                                  = $val['saswp_apartment_additional_property_value'];
                                                                        
                                   $add_property_arr[] =  $supply_data;
                                }
                                
                                $input1['additionalProperty'] = $add_property_arr;
                            }
                            
                         }
                                                                                                                                            
                         if( 'House' === $schema_type){
                             
                            $howto_image = get_post_meta( get_the_ID(), 'saswp_house_schema_image_'.$schema_id.'_detail',true); 
                                                                                                               
                            $input1['@context']              = saswp_context_url();
                            $input1['@type']                 = 'House';
                            $input1['@id']                   = trailingslashit(get_permalink()).'#House';
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
                            $input1['address']['PostalCode']        = saswp_remove_warnings($all_post_meta, 'saswp_house_schema_postalcode_'.$schema_id, 'saswp_array');
                            
                            $input1['telephone']                    = saswp_remove_warnings($all_post_meta, 'saswp_house_schema_telephone_'.$schema_id, 'saswp_array');
                            
                            $input1['hasMap']                       = saswp_remove_warnings($all_post_meta, 'saswp_house_schema_hasmap_'.$schema_id, 'saswp_array');
                            $input1['floorSize']                    = saswp_remove_warnings($all_post_meta, 'saswp_house_schema_floor_size_'.$schema_id, 'saswp_array');
                            $input1['numberOfRooms']                = saswp_remove_warnings($all_post_meta, 'saswp_house_schema_no_of_rooms_'.$schema_id, 'saswp_array');
                            
                            }  
                            
                         if( 'SingleFamilyResidence' === $schema_type){
                             
                            $howto_image = get_post_meta( get_the_ID(), 'saswp_sfr_schema_image_'.$schema_id.'_detail',true);                            
                                                                                   
                            $input1['@context']              = saswp_context_url();
                            $input1['@type']                 = 'SingleFamilyResidence';
                            $input1['@id']                   = trailingslashit(get_permalink()).'#SingleFamilyResidence';
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
                            $input1['address']['PostalCode']        = saswp_remove_warnings($all_post_meta, 'saswp_sfr_schema_postalcode_'.$schema_id, 'saswp_array');
                            
                            $input1['telephone']                    = saswp_remove_warnings($all_post_meta, 'saswp_sfr_schema_telephone_'.$schema_id, 'saswp_array');
                            $input1['hasMap']                       = saswp_remove_warnings($all_post_meta, 'saswp_sfr_schema_hasmap_'.$schema_id, 'saswp_array');
                            $input1['floorSize']                    = saswp_remove_warnings($all_post_meta, 'saswp_sfr_schema_floor_size_'.$schema_id, 'saswp_array');
                            $input1['numberOfRooms']                = saswp_remove_warnings($all_post_meta, 'saswp_sfr_schema_no_of_rooms_'.$schema_id, 'saswp_array');
                            
                            }     
                                                
                         if( 'HowTo' === $schema_type){
                             
                            $howto_image = get_post_meta( get_the_ID(), 'saswp_howto_schema_image_'.$schema_id.'_detail',true); 
                                                         
                            $tool    = get_post_meta($schema_post_id, 'howto_tool_'.$schema_id, true);              
                            $step    = get_post_meta($schema_post_id, 'howto_step_'.$schema_id, true);              
                            $supply  = get_post_meta($schema_post_id, 'howto_supply_'.$schema_id, true);              
                                                        
                            $input1['@context']              = saswp_context_url();
                            $input1['@type']                 = 'HowTo';
                            $input1['@id']                   = trailingslashit(get_permalink()).'#HowTo';
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
                            
                            if(saswp_remove_warnings($all_post_meta, 'saswp_howto_ec_schema_currency_'.$schema_id, 'saswp_array') !='' && saswp_remove_warnings($all_post_meta, 'saswp_howto_ec_schema_value_'.$schema_id, 'saswp_array') !='')
                            {
                                $input1['estimatedCost']['@type']   = 'MonetaryAmount';
                                $input1['estimatedCost']['currency']= saswp_remove_warnings($all_post_meta, 'saswp_howto_ec_schema_currency_'.$schema_id, 'saswp_array');
                                $input1['estimatedCost']['value']   = saswp_remove_warnings($all_post_meta, 'saswp_howto_ec_schema_value_'.$schema_id, 'saswp_array');
                            }
                            
                                                        
                            $supply_arr = array();
                            if(!empty($supply)){
                                
                                foreach($supply as $val){
                                   
                                    $supply_data = array();
                                    
                                    if($val['saswp_howto_supply_name'] || $val['saswp_howto_supply_url']){
                                        $supply_data['@type'] = 'HowToSupply';
                                        $supply_data['name']  = $val['saswp_howto_supply_name'];
                                        $supply_data['url']   = $val['saswp_howto_supply_url'];
                                    }
                                    
                                    
                                    
                                    if(isset($val['saswp_howto_supply_image_id']) && $val['saswp_howto_supply_image_id'] !=''){
                                        
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
                                    
                                    if($val['saswp_howto_tool_name'] || $val['saswp_howto_tool_url']){
                                        $supply_data['@type'] = 'HowToTool';
                                        $supply_data['name'] = $val['saswp_howto_tool_name'];
                                        $supply_data['url']  = $val['saswp_howto_tool_url'];
                                    }
                                                                                                            
                                    if(isset($val['saswp_howto_tool_image_id']) && $val['saswp_howto_tool_image_id'] !=''){
                                        
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
                                    
                                    if($val['saswp_howto_direction_text']){
                                        $direction['@type']     = 'HowToDirection';
                                        $direction['text']      = $val['saswp_howto_direction_text'];
                                    }
                                    
                                    if($val['saswp_howto_tip_text']){
                                        
                                        $tip['@type']           = 'HowToTip';
                                        $tip['text']            = $val['saswp_howto_tip_text'];
                                        
                                    }
                                    
                                    $supply_data['@type']   = 'HowToStep';
                                    $supply_data['url']     = trailingslashit(get_permalink()).'#step'.++$key;
                                    $supply_data['name']    = $val['saswp_howto_step_name'];    
                                    
                                    if($direction['text'] ||  $tip['text']){
                                        $supply_data['itemListElement']  = array($direction, $tip);
                                    }
                                                                                                            
                                    if(isset($val['saswp_howto_step_image_id']) && $val['saswp_howto_step_image_id'] !=''){
                                        
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
                             $input1 = saswp_append_fetched_reviews($input1);
                                                       
                            }
                            
                         if( 'TVSeries' === $schema_type){
                             
                            $howto_image = get_post_meta( get_the_ID(), 'saswp_tvseries_schema_image_'.$schema_id.'_detail',true); 
                            
                             
                            $actor     = get_post_meta($schema_post_id, 'tvseries_actor_'.$schema_id, true);              
                            $season    = get_post_meta($schema_post_id, 'tvseries_season_'.$schema_id, true);                                          
                                                        
                            $input1['@context']              = saswp_context_url();
                            $input1['@type']                 = 'TVSeries';
                            $input1['@id']                   = trailingslashit(get_permalink()).'#TVSeries';                                            
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
                                                                 
                            $input1 = saswp_append_fetched_reviews($input1);
                            
                            }   
                            
                         if( 'MedicalCondition' === $schema_type){
                                                         
                            $howto_image = get_post_meta( get_the_ID(), 'saswp_mc_schema_image_'.$schema_id.'_detail',true);  
                             
                            $cause       = get_post_meta($schema_post_id, 'mc_cause_'.$schema_id, true);              
                            $symptom     = get_post_meta($schema_post_id, 'mc_symptom_'.$schema_id, true);              
                            $riskfactro  = get_post_meta($schema_post_id, 'mc_risk_factor_'.$schema_id, true);              
                                                        
                            $input1['@context']                     = saswp_context_url();
                            $input1['@type']                        = 'MedicalCondition';
                            $input1['@id']                          = trailingslashit(get_permalink()).'#MedicalCondition';
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
                             
                                                                                    
                            $input1['@context']                     = saswp_context_url();
                            $input1['@type']                        = 'VideoGame';
                            $input1['@id']                          = trailingslashit(get_permalink()).'#VideoGame'; 
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
                                                        
                            $input1 = saswp_append_fetched_reviews($input1);
                                                                                                                                                                                                                               
                            }   
                        
                         if( 'qanda' === $schema_type){      
                             
                            if(trim(saswp_remove_warnings($all_post_meta, 'saswp_qa_question_title_'.$schema_id, 'saswp_array')) ==''){
                                
                                $service_object = new saswp_output_service();
                                $input1  = $service_object->saswp_dw_question_answers_details(get_the_ID());  
                                
                            }else{
                                
                                $input1 = array(
                                    '@context'		  => saswp_context_url(),
                                    '@type'		  => 'QAPage',
                                    '@id'                 => trailingslashit(get_permalink()).'#qapage',
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
                         
                        if( 'DataFeed' === $schema_type){
                                               
                                                    
                        $input1 = array(
                            '@context'			=> saswp_context_url(),
                            '@type'		        => 'DataFeed' ,
                            '@id'                       => trailingslashit(get_permalink()).'#DataFeed',    
                            'name'			=> saswp_remove_warnings($all_post_meta, 'saswp_data_feed_schema_name_'.$schema_id, 'saswp_array'),
                            'description'               => saswp_remove_warnings($all_post_meta, 'saswp_data_feed_schema_description_'.$schema_id, 'saswp_array'),									                                             
                            'dateModified'              => isset($all_post_meta['saswp_data_feed_schema_date_modified_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_data_feed_schema_date_modified_'.$schema_id][0])):'',                               
                            'license'                   => saswp_remove_warnings($all_post_meta, 'saswp_data_feed_schema_license_'.$schema_id, 'saswp_array'),									                                             
                        );
                                        
                        
                                $performer  = get_post_meta($schema_post_id, 'feed_element_'.$schema_id, true);
                            
                                $performer_arr = array();
                            
                                if(!empty($performer)){

                                    foreach($performer as $val){

                                        $supply_data = array();
                                        $supply_data['@type']        = 'DataFeedItem';
                                        $supply_data['dateCreated']  = isset($val['saswp_feed_element_date_created'])?date('Y-m-d\TH:i:s\Z',strtotime($val['saswp_feed_element_date_created'])):'';  
                                        $supply_data['item']  = array(
                                           '@type'    => 'Person',
                                           'name'     => $val['saswp_feed_element_name'],
                                           'email'    => $val['saswp_feed_element_email'],
                                        );
                                                                                                                   
                                        $performer_arr[] =  $supply_data;
                                    }
                                    
                                   $input1['dataFeedElement'] = $performer_arr;
                                   
                                }                                                       
                                                            
                        }
                        
                         if( 'Event' === $schema_type){
                       
                        $event_image = get_post_meta( get_the_ID(), 'saswp_event_schema_image_'.$schema_id.'_detail',true); 
                                                    
                        $input1 = array(
			'@context'			=> saswp_context_url(),
			'@type'				=> (isset($all_post_meta['saswp_event_schema_type_'.$schema_id][0]) && $all_post_meta['saswp_event_schema_type_'.$schema_id][0] !='') ? $all_post_meta['saswp_event_schema_type_'.$schema_id][0] : 'Event' ,
                        '@id'                           => trailingslashit(get_permalink()).'#event',    
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
                                        )                         
                            );
                                        
                        
                                $performer  = get_post_meta($schema_post_id, 'performer_'.$schema_id, true);
                            
                                $performer_arr = array();
                            
                                if(!empty($performer)){

                                    foreach($performer as $val){

                                        $supply_data = array();
                                        $supply_data['@type']        = $val['saswp_event_performer_type'];
                                        $supply_data['name']         = $val['saswp_event_performer_name'];                                    
                                        $supply_data['url']          = $val['saswp_event_performer_url'];

                                        $performer_arr[] =  $supply_data;
                                    }
                                    
                                   $input1['performer'] = $performer_arr;
                                   
                                }
                        
                                                                                                                        
                                if(!empty($aggregateRating)){
                                    $input1['aggregateRating'] = $aggregateRating;
                                }                                
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }                               
                                
                                $input1 = saswp_append_fetched_reviews($input1);
                                
                        }
                        
                         if( 'DiscussionForumPosting' === $schema_type){
                            
                            $event_image = get_post_meta( get_the_ID(), 'saswp_dfp_image_'.$schema_id.'_detail',true);  
                            $slogo = get_post_meta( get_the_ID(), 'saswp_dfp_organization_logo_'.$schema_id.'_detail',true); 
                            $input1 = array(
                                '@context'			=> saswp_context_url(),
                                '@type'				=> 'DiscussionForumPosting' ,
                                '@id'				=> saswp_remove_warnings($all_post_meta, 'saswp_dfp_url_'.$schema_id, 'saswp_array').'#blogposting',    			
                                'mainEntityOfPage'		=> saswp_remove_warnings($all_post_meta, 'saswp_dfp_main_entity_of_page_'.$schema_id, 'saswp_array'),    			
                                'headline'			=> saswp_remove_warnings($all_post_meta, 'saswp_dfp_headline_'.$schema_id, 'saswp_array'),
                                'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_dfp_description_'.$schema_id, 'saswp_array'),			
                                'url'				=> saswp_remove_warnings($all_post_meta, 'saswp_dfp_url_'.$schema_id, 'saswp_array'),
                                'image'                         => array(
                                                                    '@type'		=>'ImageObject',
                                                                    'url'		=>  isset($event_image['thumbnail']) ? esc_url($event_image['thumbnail']):'' ,
                                                                    'width'		=>  isset($event_image['width'])     ? esc_attr($event_image['width'])   :'' ,
                                                                    'height'            =>  isset($event_image['height'])    ? esc_attr($event_image['height'])  :'' ,
                                                                ), 
                                'datePublished'                 => isset($all_post_meta['saswp_dfp_date_published_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_dfp_date_published_'.$schema_id][0])):'',
                                'dateModified'                  => isset($all_post_meta['saswp_dfp_date_modified_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_dfp_date_modified_'.$schema_id][0])):'',
                                'author'			=> array(
                                                                    '@type' 	        => 'Person',
                                                                    'name'		=> saswp_remove_warnings($all_post_meta, 'saswp_dfp_author_name_'.$schema_id, 'saswp_array'),
                                                                    'description'	=> saswp_remove_warnings($all_post_meta, 'saswp_dfp_author_description_'.$schema_id, 'saswp_array') 
                                                                ),
                                'publisher'			=> array(
						'@type'			=> 'Organization',
						'logo' 			=> array(
							'@type'		=> 'ImageObject',
							'url'		=> saswp_remove_warnings($all_post_meta, 'saswp_dfp_organization_logo_'.$schema_id, 'saswp_array'),
							'width'		=> saswp_remove_warnings($slogo, 'width', 'saswp_string'),
							'height'	=> saswp_remove_warnings($slogo, 'height', 'saswp_string'),
							),
						'name'			=> saswp_remove_warnings($all_post_meta, 'saswp_dfp_organization_name_'.$schema_id, 'saswp_array'),
					),
                                    );
                        }
                        
                         if( 'Course' === $schema_type){
                         
                                $input1 = array(
                                '@context'			=> saswp_context_url(),
                                '@type'				=> 'Course' ,	
                                '@id'                           => trailingslashit(get_permalink()).'#course',    
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
			'@context'			=> saswp_context_url(),
			'@type'				=> 'Blogposting' ,
                        '@id'                           => trailingslashit(get_permalink()).'#Blogposting',  
			'mainEntityOfPage'              => saswp_remove_warnings($all_post_meta, 'saswp_blogposting_main_entity_of_page_'.$schema_id, 'saswp_array'),
			'headline'			=> saswp_remove_warnings($all_post_meta, 'saswp_blogposting_headline_'.$schema_id, 'saswp_array'),
			'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_blogposting_description_'.$schema_id, 'saswp_array'),
                        'keywords'                      => saswp_remove_warnings($all_post_meta, 'saswp_blogposting_keywords_'.$schema_id, 'saswp_array'),
			'name'				=> saswp_remove_warnings($all_post_meta, 'saswp_blogposting_name_'.$schema_id, 'saswp_array'),
			'url'				=> saswp_remove_warnings($all_post_meta, 'saswp_blogposting_url_'.$schema_id, 'saswp_array'),
			'datePublished'                 => isset($all_post_meta['saswp_blogposting_date_published_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_blogposting_date_published_'.$schema_id][0])):'',
			'dateModified'                  => isset($all_post_meta['saswp_blogposting_date_modified_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_blogposting_date_modified_'.$schema_id][0])):'',
			'author'			=> array(
					'@type' 	=> 'Person',
					'name'		=> saswp_remove_warnings($all_post_meta, 'saswp_blogposting_author_name_'.$schema_id, 'saswp_array'),
                                        'description'	=> saswp_remove_warnings($all_post_meta, 'saswp_blogposting_author_description_'.$schema_id, 'saswp_array')
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
                                if(saswp_remove_warnings($all_post_meta, 'saswp_blogposting_enable_rating_'.$schema_id, 'saswp_array') == 1 && saswp_remove_warnings($all_post_meta, 'saswp_blogposting_rating_'.$schema_id, 'saswp_array') && saswp_remove_warnings($all_post_meta, 'saswp_blogposting_review_count_'.$schema_id, 'saswp_array')){   
                                                
                                    $input1['aggregateRating'] = array(
                                                    "@type"       => "AggregateRating",
                                                    "ratingValue" => saswp_remove_warnings($all_post_meta, 'saswp_blogposting_rating_'.$schema_id, 'saswp_array'),
                                                    "reviewCount" => saswp_remove_warnings($all_post_meta, 'saswp_blogposting_review_count_'.$schema_id, 'saswp_array')
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
			'@context'			=> saswp_context_url(),
			'@type'				=> $schema_type,
                        '@id'                           => trailingslashit(get_permalink()).'#audioobject',    
			'name'			        => saswp_remove_warnings($all_post_meta, 'saswp_audio_schema_name_'.$schema_id, 'saswp_array'),
			'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_audio_schema_description_'.$schema_id, 'saswp_array'),
			'contentUrl'		        => saswp_remove_warnings($all_post_meta, 'saswp_audio_schema_contenturl_'.$schema_id, 'saswp_array'),
			'duration'		        => saswp_remove_warnings($all_post_meta, 'saswp_audio_schema_duration_'.$schema_id, 'saswp_array'),
                        'encodingFormat'		=> saswp_remove_warnings($all_post_meta, 'saswp_audio_schema_encoding_format_'.$schema_id, 'saswp_array'),
			'datePublished'                 => isset($all_post_meta['saswp_audio_schema_date_published_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_audio_schema_date_published_'.$schema_id][0])):'',
			'dateModified'                  => isset($all_post_meta['saswp_audio_schema_date_modified_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_audio_schema_date_modified_'.$schema_id][0])):'',
			'author'			=> array(
					'@type' 	=> 'Person',
					'name'		=> saswp_remove_warnings($all_post_meta, 'saswp_audio_schema_author_name_'.$schema_id, 'saswp_array'),
                                        'description'	=> saswp_remove_warnings($all_post_meta, 'saswp_audio_schema_author_description_'.$schema_id, 'saswp_array')
                                    ),
			
			   );
                               if(!empty($aggregateRating)){
                                    $input1['aggregateRating'] = $aggregateRating;
                                }                                
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }
                                $input1 = saswp_append_fetched_reviews($input1);
                        } 
                        
                         if( 'SoftwareApplication' === $schema_type){
                                                                            
			$input1 = array(
			'@context'			=> saswp_context_url(),
			'@type'				=> 'SoftwareApplication',
                        '@id'                           => trailingslashit(get_permalink()).'#softwareapplication',     
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
                        
                           $soft_image = get_post_meta( get_the_ID(), 'saswp_software_schema_image_'.$schema_id.'_detail',true); 

                           if(!(empty($soft_image))){
                             
                                $input1['image']['@type']        = 'ImageObject';
                                $input1['image']['url']          = isset($soft_image['thumbnail']) ? esc_url($soft_image['thumbnail']):'';
                                $input1['image']['height']       = isset($soft_image['width'])     ? esc_attr($soft_image['width'])   :'';
                                $input1['image']['width']        = isset($soft_image['height'])    ? esc_attr($soft_image['height'])  :'';
                                
                            }
                        
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
                                $input1 = saswp_append_fetched_reviews($input1);
                        }
			
			 if( 'WebPage' === $schema_type){
                             
				$slogo = get_post_meta( get_the_ID(), 'saswp_webpage_organization_logo_'.$schema_id.'_detail',true);
				$input1 = array(
				'@context'			=> saswp_context_url(),
				'@type'				=> 'WebPage' ,
                                '@id'                           => trailingslashit(get_permalink()).'#webpage',     
				'name'				=> saswp_remove_warnings($all_post_meta, 'saswp_webpage_name_'.$schema_id, 'saswp_array'),
				'url'				=> saswp_remove_warnings($all_post_meta, 'saswp_webpage_url_'.$schema_id, 'saswp_array'),
				'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_webpage_description_'.$schema_id, 'saswp_array'),
				'mainEntity'                    => array(
						'@type'			=> 'Article',
						'mainEntityOfPage'	=> saswp_remove_warnings($all_post_meta, 'saswp_webpage_main_entity_of_page_'.$schema_id, 'saswp_array'),
						'image'			=> saswp_remove_warnings($all_post_meta, 'saswp_webpage_image_'.$schema_id, 'saswp_array'),
						'headline'		=> saswp_remove_warnings($all_post_meta, 'saswp_webpage_headline_'.$schema_id, 'saswp_array'),
						'description'		=> saswp_remove_warnings($all_post_meta, 'saswp_webpage_description_'.$schema_id, 'saswp_array'),
                                                'keywords'		=> saswp_remove_warnings($all_post_meta, 'saswp_webpage_keywords_'.$schema_id, 'saswp_array'),
						'datePublished' 	=> isset($all_post_meta['saswp_webpage_date_published_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_webpage_date_published_'.$schema_id][0])):'',
						'dateModified'		=> isset($all_post_meta['saswp_webpage_date_modified_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_webpage_date_modified_'.$schema_id][0])):'',
						'author'			=> array(
								'@type' 	=> 'Person',
								'name'		=> saswp_remove_warnings($all_post_meta, 'saswp_webpage_author_name_'.$schema_id, 'saswp_array'), 
                                                                'description'	=> saswp_remove_warnings($all_post_meta, 'saswp_webpage_author_description_'.$schema_id, 'saswp_array'), 
                                                    ),
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
					'@context'			=> saswp_context_url(),
					'@type'				=> 'Article',
                                        '@id'                           => trailingslashit(get_permalink()).'#article',
					'mainEntityOfPage'              => saswp_remove_warnings($all_post_meta, 'saswp_article_main_entity_of_page_'.$schema_id, 'saswp_array'),
					'image'				=> saswp_remove_warnings($all_post_meta, 'saswp_article_image_'.$schema_id, 'saswp_array'),
					'headline'			=> saswp_remove_warnings($all_post_meta, 'saswp_article_headline_'.$schema_id, 'saswp_array'),
					'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_article_description_'.$schema_id, 'saswp_array'),
                                        'keywords'		        => saswp_remove_warnings($all_post_meta, 'saswp_article_keywords_'.$schema_id, 'saswp_array'),
					'datePublished'                 => isset($all_post_meta['saswp_article_date_published_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_article_date_published_'.$schema_id][0])):'',
					'dateModified'                  => isset($all_post_meta['saswp_article_date_modified_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_article_date_modified_'.$schema_id][0])):'',
					'author'			=> array(
							'@type' 	=> 'Person',
							'name'		=> saswp_remove_warnings($all_post_meta, 'saswp_article_author_name_'.$schema_id, 'saswp_array'),
                                                        'description'   => saswp_remove_warnings($all_post_meta, 'saswp_article_author_description_'.$schema_id, 'saswp_array') 
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
                               
                               if(saswp_remove_warnings($all_post_meta, 'saswp_article_enable_rating_'.$schema_id, 'saswp_array') == 1 && saswp_remove_warnings($all_post_meta, 'saswp_article_rating_'.$schema_id, 'saswp_array') && saswp_remove_warnings($all_post_meta, 'saswp_article_review_count_'.$schema_id, 'saswp_array')){   
                                                
                                    $input1['aggregateRating'] = array(
                                                    "@type"       => "AggregateRating",
                                                    "ratingValue" => saswp_remove_warnings($all_post_meta, 'saswp_article_rating_'.$schema_id, 'saswp_array'),
                                                    "reviewCount" => saswp_remove_warnings($all_post_meta, 'saswp_article_review_count_'.$schema_id, 'saswp_array')
                                                );                                       
                                }
                                
                                if(!empty($extra_theme_review)){
                                    
                                   $input1 = array_merge($input1, $extra_theme_review);
                                   
                                }
			}
                        
                         if( 'TechArticle' === $schema_type ){
                             
                             $slogo = get_post_meta( get_the_ID(), 'saswp_tech_article_organization_logo_'.$schema_id.'_detail',true);
                             
				$input1 = array(
					'@context'			=> saswp_context_url(),
					'@type'				=> 'TechArticle',
                                        '@id'                           => trailingslashit(get_permalink()).'#techarticle',
					'mainEntityOfPage'              => saswp_remove_warnings($all_post_meta, 'saswp_tech_article_main_entity_of_page_'.$schema_id, 'saswp_array'),
					'image'				=> saswp_remove_warnings($all_post_meta, 'saswp_tech_article_image_'.$schema_id, 'saswp_array'),
					'headline'			=> saswp_remove_warnings($all_post_meta, 'saswp_tech_article_headline_'.$schema_id, 'saswp_array'),
					'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_tech_article_description_'.$schema_id, 'saswp_array'),
                                        'keywords'		        => saswp_remove_warnings($all_post_meta, 'saswp_tech_article_keywords_'.$schema_id, 'saswp_array'),
					'datePublished'                 => isset($all_post_meta['saswp_tech_article_date_published_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_tech_article_date_published_'.$schema_id][0])):'',
					'dateModified'                  => isset($all_post_meta['saswp_tech_article_date_modified_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_tech_article_date_modified_'.$schema_id][0])):'',
					'author'			=> array(
							'@type' 	=> 'Person',
							'name'		=> saswp_remove_warnings($all_post_meta, 'saswp_tech_article_author_name_'.$schema_id, 'saswp_array'),
                                                        'description'	=> saswp_remove_warnings($all_post_meta, 'saswp_tech_article_author_description_'.$schema_id, 'saswp_array') 
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
                                
                                if(saswp_remove_warnings($all_post_meta, 'saswp_tech_article_enable_rating_'.$schema_id, 'saswp_array') == 1 && saswp_remove_warnings($all_post_meta, 'saswp_tech_article_rating_'.$schema_id, 'saswp_array') && saswp_remove_warnings($all_post_meta, 'saswp_tech_article_review_count_'.$schema_id, 'saswp_array')){   
                                                
                                    $input1['aggregateRating'] = array(
                                                    "@type"       => "AggregateRating",
                                                    "ratingValue" => saswp_remove_warnings($all_post_meta, 'saswp_tech_article_rating_'.$schema_id, 'saswp_array'),
                                                    "reviewCount" => saswp_remove_warnings($all_post_meta, 'saswp_tech_article_review_count_'.$schema_id, 'saswp_array')
                                                );                                       
                                }
                                
                                
                                if(!empty($extra_theme_review)){
                                    
                                   $input1 = array_merge($input1, $extra_theme_review);
                                   
                                }
			}
	
			 if( 'Recipe' === $schema_type){
                             
                                $recipe_logo    = get_post_meta( get_the_ID(), 'saswp_recipe_organization_logo_'.$schema_id.'_detail',true);
                                $recipe_image   = get_post_meta( get_the_ID(), 'saswp_recipe_image_'.$schema_id.'_detail',true);                                                                           
                                $recipe_author_image   = get_post_meta( get_the_ID(), 'saswp_recipe_author_image_'.$schema_id.'_detail',true);
                                
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
				'@context'			=> saswp_context_url(),
				'@type'				=> $schema_type ,
                                '@id'                           => trailingslashit(get_permalink()).'#recipe',    
				'url'				=> saswp_remove_warnings($all_post_meta, 'saswp_recipe_url_'.$schema_id, 'saswp_array'),
                                'name'			        => saswp_remove_warnings($all_post_meta, 'saswp_recipe_name_'.$schema_id, 'saswp_array'),
                                'image'                         =>array(
                                 '@type'		=> 'ImageObject',
                                    'url'		=> saswp_remove_warnings( $recipe_image, 'thumbnail', 'saswp_string'),
                                'width'		=> saswp_remove_warnings( $recipe_image, 'width', 'saswp_string'),
                                 'height'    => saswp_remove_warnings( $recipe_image , 'height', 'saswp_string'),
                             ),
                                'author'			=> array(
								'@type' 	=> 'Person',
								'name'		=> saswp_remove_warnings($all_post_meta, 'saswp_recipe_author_name_'.$schema_id, 'saswp_array'),
                                                                'description'	=> saswp_remove_warnings($all_post_meta, 'saswp_recipe_author_description_'.$schema_id, 'saswp_array'),
								'Image'		=> array(
									'@type'			=> 'ImageObject',
									'url'			=> saswp_remove_warnings($all_post_meta, 'saswp_recipe_author_image_'.$schema_id, 'saswp_array'),
									'height'		=> saswp_remove_warnings($recipe_author_image, 'height', 'saswp_string'),
									'width'			=> saswp_remove_warnings($recipe_author_image, 'width', 'saswp_string')
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
                                
                                if(saswp_remove_warnings($all_post_meta, 'saswp_recipe_video_name_'.$schema_id, 'saswp_array') !='' && saswp_remove_warnings($all_post_meta, 'saswp_recipe_video_thumbnailurl_'.$schema_id, 'saswp_array') !='' && saswp_remove_warnings($all_post_meta, 'saswp_recipe_video_description_'.$schema_id, 'saswp_array') !=''){
                                    
                                    $input1['video']['@type']        = 'VideoObject';
                                    $input1['video']['name']         = saswp_remove_warnings($all_post_meta, 'saswp_recipe_video_name_'.$schema_id, 'saswp_array');
                                    $input1['video']['description']  = saswp_remove_warnings($all_post_meta, 'saswp_recipe_video_description_'.$schema_id, 'saswp_array');
                                    $input1['video']['thumbnailUrl'] = saswp_remove_warnings($all_post_meta, 'saswp_recipe_video_thumbnailurl_'.$schema_id, 'saswp_array');
                                    $input1['video']['contentUrl']   = saswp_remove_warnings($all_post_meta, 'saswp_recipe_video_contenturl_'.$schema_id, 'saswp_array');
                                    $input1['video']['embedUrl']     = saswp_remove_warnings($all_post_meta, 'saswp_recipe_video_embedurl_'.$schema_id, 'saswp_array');
                                    $input1['video']['uploadDate']   = isset($all_post_meta['saswp_recipe_video_upload_date_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_recipe_video_upload_date_'.$schema_id][0])):'';
                                    $input1['video']['duration']     = saswp_remove_warnings($all_post_meta, 'saswp_recipe_video_duration_'.$schema_id, 'saswp_array');
                                }
                                                                
                                if(saswp_remove_warnings($all_post_meta, 'saswp_recipe_schema_enable_rating_'.$schema_id, 'saswp_array') == 1 && saswp_remove_warnings($all_post_meta, 'saswp_recipe_schema_rating_'.$schema_id, 'saswp_array') && saswp_remove_warnings($all_post_meta, 'saswp_recipe_schema_review_count_'.$schema_id, 'saswp_array')){   
                                                
                                    $input1['aggregateRating'] = array(
                                                    "@type"       => "AggregateRating",
                                                    "ratingValue" => saswp_remove_warnings($all_post_meta, 'saswp_recipe_schema_rating_'.$schema_id, 'saswp_array'),
                                                    "reviewCount" => saswp_remove_warnings($all_post_meta, 'saswp_recipe_schema_review_count_'.$schema_id, 'saswp_array')
                                                );                                       
                                }
                   
                                if(!empty($aggregateRating)){
                                    $input1['aggregateRating'] = $aggregateRating;
                                }                                
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }
                                $input1 = saswp_append_fetched_reviews($input1);
			}
						
			 if( 'Product' === $schema_type){				

                                        $product_image = get_post_meta( get_the_ID(), 'saswp_product_schema_image_'.$schema_id.'_detail',true);                                                                           
                                        $input1 = array(
                                        '@context'			=> saswp_context_url(),
                                        '@type'				=> 'Product',
                                        '@id'                           => trailingslashit(get_permalink()).'#product',    
                                        'url'				=> trailingslashit(get_permalink()),
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
                                                                                'url'             => trailingslashit(get_permalink()),
                                                                                'priceValidUntil' => isset($all_post_meta['saswp_product_schema_priceValidUntil_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_product_schema_priceValidUntil_'.$schema_id][0])):'',
                                                                                ), 
                                        'brand'                         => array('@type' => 'Thing',
                                                                                 'name'  => saswp_remove_warnings($all_post_meta, 'saswp_product_schema_brand_name_'.$schema_id, 'saswp_array'),
                                                                                )    
                                        ); 
                                        
                                        if(isset($all_post_meta['saswp_product_schema_seller_'.$schema_id])){
                                            $input1['offers']['seller']['@type']   = 'Organization';
                                            $input1['offers']['seller']['name']    = esc_attr($all_post_meta['saswp_product_schema_seller_'.$schema_id][0]);  
                                        }                                        
                                        if(isset($all_post_meta['saswp_product_schema_gtin8_'.$schema_id])){
                                            $input1['gtin8'] = esc_attr($all_post_meta['saswp_product_schema_gtin8_'.$schema_id][0]);  
                                        }
                                        if(isset($all_post_meta['saswp_product_schema_mpn_'.$schema_id])){
                                          $input1['mpn'] = esc_attr($all_post_meta['saswp_product_schema_mpn_'.$schema_id][0]);  
                                        }
                                                                                   
                                        if(saswp_remove_warnings($all_post_meta, 'saswp_product_schema_enable_rating_'.$schema_id, 'saswp_array') == 1 && saswp_remove_warnings($all_post_meta, 'saswp_product_schema_rating_'.$schema_id, 'saswp_array') && saswp_remove_warnings($all_post_meta, 'saswp_product_schema_review_count_'.$schema_id, 'saswp_array')){   
                                 
                                          $input1['aggregateRating'] = array(
                                                            "@type"       => "AggregateRating",
                                                            "ratingValue" => saswp_remove_warnings($all_post_meta, 'saswp_product_schema_rating_'.$schema_id, 'saswp_array'),
                                                            "reviewCount" => saswp_remove_warnings($all_post_meta, 'saswp_product_schema_review_count_'.$schema_id, 'saswp_array')
                                                         );                                       
                                         }
                                             
                                         
                                        $itinerary  = get_post_meta($schema_post_id, 'product_reviews_'.$schema_id, true);
                            
                                        $itinerary_arr = array();

                                        if(!empty($itinerary)){

                                         foreach($itinerary as $review){

                                                
                                          $review_fields = array();
                                          
                                          $review_fields['@type']         = 'Review';
                                          $review_fields['author']        = esc_attr($review['saswp_product_reviews_reviewer_name']);
                                          $review_fields['datePublished'] = esc_html($review['saswp_product_reviews_created_date']);
                                          $review_fields['description']   = esc_textarea($review['saswp_product_reviews_text']);
                                                                                    
                                          if(is_int($review['saswp_product_reviews_reviewer_rating'])){
                                              
                                                $review_fields['reviewRating']['@type']   = 'Rating';
                                                $review_fields['reviewRating']['bestRating']   = '5';
                                                $review_fields['reviewRating']['ratingValue']   = esc_attr($review['saswp_product_reviews_reviewer_rating']);
                                                $review_fields['reviewRating']['worstRating']   = '1';
                                          
                                          }
                                                                                                                                                                        
                                          $itinerary_arr[] = $review_fields;
                                            }
                                           $input1['review'] = $itinerary_arr;
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
                                                                                          
                                          $review_fields = array();
                                          
                                          $review_fields['@type']         = 'Review';
                                          $review_fields['author']        = esc_attr($review['author']);
                                          $review_fields['datePublished'] = esc_html($review['datePublished']);
                                          $review_fields['description']   = $review['description'];
                                                                                    
                                          if(isset($review['reviewRating']) && $review['reviewRating'] !=''){
                                              
                                                $review_fields['reviewRating']['@type']   = 'Rating';
                                                $review_fields['reviewRating']['bestRating']   = '5';
                                                $review_fields['reviewRating']['ratingValue']   = esc_attr($review['reviewRating']);
                                                $review_fields['reviewRating']['worstRating']   = '1';
                                          
                                          }
                                                                                                                                                                        
                                          $reviews[] = $review_fields;
                                          
                                      }
                                         $input1['review'] =  $reviews;
                                } 
                                $input1 = saswp_append_fetched_reviews($input1);
			}
                        
                         if( 'NewsArticle' === $schema_type ){  
                             
                                $slogo = get_post_meta( get_the_ID(), 'saswp_newsarticle_organization_logo_'.$schema_id.'_detail',true);
                                $author_image = get_post_meta( get_the_ID(), 'saswp_newsarticle_author_image_'.$schema_id.'_detail',true);
                             
				        $input1 = array(
					'@context'			=> saswp_context_url(),
					'@type'				=> 'NewsArticle' ,
                                        '@id'                           => trailingslashit(get_permalink()).'#newsarticle',    
					'mainEntityOfPage'              => saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_main_entity_of_page_'.$schema_id, 'saswp_array'),
					'url'				=> saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_URL_'.$schema_id, 'saswp_array'),
                                        'image'				=> saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_image_'.$schema_id, 'saswp_array'),
					'headline'			=> saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_headline_'.$schema_id, 'saswp_array'),
					'datePublished'                 => isset($all_post_meta['saswp_newsarticle_date_published_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_newsarticle_date_published_'.$schema_id][0])):'',
					'dateModified'                  => isset($all_post_meta['saswp_newsarticle_date_modified_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_newsarticle_date_modified_'.$schema_id][0])):'',
					'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_description_'.$schema_id, 'saswp_array'),
                                        'keywords'		        => saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_keywords_'.$schema_id, 'saswp_array'),
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
                                                        'description'			=> saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_author_description_'.$schema_id, 'saswp_array'),
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
                                                if(saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_enable_rating_'.$schema_id, 'saswp_array') == 1 && saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_rating_'.$schema_id, 'saswp_array') && saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_review_count_'.$schema_id, 'saswp_array')){   
                                                
                                                      $input1['aggregateRating'] = array(
                                                                "@type"       => "AggregateRating",
                                                                "ratingValue" => saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_rating_'.$schema_id, 'saswp_array'),
                                                                "reviewCount" => saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_review_count_'.$schema_id, 'saswp_array')
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
						'@context'			=> saswp_context_url(),
						'@type'				=> 'VideoObject',
                                                '@id'                           => trailingslashit(get_permalink()).'#videoobject',    
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
                                                                'description'		        => saswp_remove_warnings($all_post_meta, 'saswp_video_object_author_description_'.$schema_id, 'saswp_array'),
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
                                                $input1 = saswp_append_fetched_reviews($input1);
				}
                        
                         if( 'Service' === $schema_type ){  
                             
                                $area_served_str = saswp_remove_warnings($all_post_meta, 'saswp_service_schema_area_served_'.$schema_id, 'saswp_array');
                                $area_served_arr = explode(',', $area_served_str);
                                                                
                                $service_offer_str = saswp_remove_warnings($all_post_meta, 'saswp_service_schema_service_offer_'.$schema_id, 'saswp_array');
                                $service_offer_arr = explode(',', $service_offer_str);
                                
                                $input1['@context']    = saswp_context_url();
                                $input1['@type']       = $schema_type;
                                $input1['@id']         = trailingslashit(get_permalink()).'#service';
                                $input1['name']        = saswp_remove_warnings($all_post_meta, 'saswp_service_schema_name_'.$schema_id, 'saswp_array');
                                $input1['serviceType'] = saswp_remove_warnings($all_post_meta, 'saswp_service_schema_type_'.$schema_id, 'saswp_array');
                                
                                if(isset($all_post_meta['saswp_service_schema_provider_type_'.$schema_id][0])){
                                
                                     $input1['provider']['@type']                      = $all_post_meta['saswp_service_schema_provider_type_'.$schema_id][0];
                                     $input1['provider']['name']                       = $all_post_meta['saswp_service_schema_provider_name_'.$schema_id][0];                                    
                                     $input1['provider']['image']                      = saswp_remove_warnings($all_post_meta, 'saswp_service_schema_image_'.$schema_id, 'saswp_array');
                                     $input1['provider']['priceRange']                 = saswp_remove_warnings($all_post_meta, 'saswp_service_schema_price_range_'.$schema_id, 'saswp_array');
                                     $input1['provider']['address']['@type']           = 'PostalAddress';
                                     $input1['provider']['address']['addressLocality'] = saswp_remove_warnings($all_post_meta, 'saswp_service_schema_locality_'.$schema_id, 'saswp_array');
                                     $input1['provider']['address']['postalCode']      = saswp_remove_warnings($all_post_meta, 'saswp_service_schema_postal_code_'.$schema_id, 'saswp_array');
                                     $input1['provider']['address']['telephone']       = saswp_remove_warnings($all_post_meta, 'saswp_service_schema_telephone_'.$schema_id, 'saswp_array');
                                     
                                }
                                
                                $input1['description'] = saswp_remove_warnings($all_post_meta, 'saswp_service_schema_description_'.$schema_id, 'saswp_array');
                                				
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
                                $input1 = saswp_append_fetched_reviews($input1);
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
                                
				$input1['@context']                     = saswp_context_url();
                                $input1['@type']                        = 'Review';
                                $input1['@id']                          = trailingslashit(get_permalink()).'#review';                                                           
                                $input1['url']                          = trailingslashit(get_permalink());                                
                                $input1['datePublished']                = get_the_date("c");
                                $input1['dateModified']                 = get_the_modified_date("c");
                                
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
                                    $input1['reviewBody']               = saswp_get_the_excerpt();
                                    $input1['description']              = saswp_get_the_excerpt();
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
                                    
                                        $input1['author']['sameAs']   = trailingslashit(get_permalink());
                                        
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
                                
                                if(array_key_exists($business_type, $post_specific_obj->_local_sub_business)){
                                
                                    $check_business_type = $post_specific_obj->_local_sub_business[$business_type];
                                
                                    if(!empty($check_business_type)){

                                     $business_sub_name = saswp_remove_warnings($all_post_meta, 'saswp_business_name_'.$schema_id, 'saswp_array');   

                                    }
                                    
                                }
                                                                                                
                                if($business_sub_name){
                                    
                                $local_business = $business_sub_name; 
                                
                                }else if($business_type){
                                    
                                $local_business = $business_type;        
                                
                                }else{
                                    
                                $local_business = 'LocalBusiness';  
                                
                                }   
                                
                                $local_image = get_post_meta( get_the_ID(), 'local_business_logo_'.$schema_id.'_detail',true);
                                
				$input1 = array(
				'@context'			=> saswp_context_url(),
				'@type'				=> $local_business ,
                                '@id'                           => ((isset($all_post_meta['saswp_business_id_'.$schema_id][0]) && $all_post_meta['saswp_business_id_'.$schema_id][0] !='') ? $all_post_meta['saswp_business_id_'.$schema_id][0] : trailingslashit(get_permalink()).'#'.strtolower($local_business)),        
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
                                    
                                    //social fields starts here
                                    
                                    $local_social = array();
                                    
                                    if(isset($all_post_meta['local_facebook_'.$schema_id][0]) && $all_post_meta['local_facebook_'.$schema_id][0] !=''){
                                      $local_social[] = esc_url($all_post_meta['local_facebook_'.$schema_id][0]);   
                                    }
                                    if(isset($all_post_meta['local_twitter_'.$schema_id][0]) && $all_post_meta['local_twitter_'.$schema_id][0] !=''){
                                      $local_social[] = esc_url($all_post_meta['local_twitter_'.$schema_id][0]);   
                                    }
                                    if(isset($all_post_meta['local_instagram_'.$schema_id][0]) && $all_post_meta['local_instagram_'.$schema_id][0] !=''){
                                      $local_social[] = esc_url($all_post_meta['local_instagram_'.$schema_id][0]);   
                                    }
                                    if(isset($all_post_meta['local_pinterest_'.$schema_id][0]) && $all_post_meta['local_pinterest_'.$schema_id][0] !=''){
                                      $local_social[] = esc_url($all_post_meta['local_pinterest_'.$schema_id][0]);   
                                    }
                                    if(isset($all_post_meta['local_linkedin_'.$schema_id][0]) && $all_post_meta['local_linkedin_'.$schema_id][0] !=''){
                                      $local_social[] = esc_url($all_post_meta['local_linkedin_'.$schema_id][0]);   
                                    }
                                    if(isset($all_post_meta['local_soundcloud_'.$schema_id][0]) && $all_post_meta['local_soundcloud_'.$schema_id][0] !=''){
                                      $local_social[] = esc_url($all_post_meta['local_soundcloud_'.$schema_id][0]);   
                                    }
                                    if(isset($all_post_meta['local_tumblr_'.$schema_id][0]) && $all_post_meta['local_tumblr_'.$schema_id][0] !=''){
                                      $local_social[] = esc_url($all_post_meta['local_tumblr_'.$schema_id][0]);   
                                    }
                                    if(isset($all_post_meta['local_youtube_'.$schema_id][0]) && $all_post_meta['local_youtube_'.$schema_id][0] !=''){
                                      $local_social[] = esc_url($all_post_meta['local_youtube_'.$schema_id][0]);   
                                    }
                                    
                                    if(!empty($local_social)){
                                      $input1['sameAs'] =  $local_social; 
                                    }
                                    //social fields ends here
                                                                                                            
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
                                    
                                    $input1 = saswp_append_fetched_reviews($input1);                                                              
			}
                        
                        global $without_aggregate;
                        
                        if(!in_array($schema_type, $without_aggregate)){
                            
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