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
 * List of schema type who do not support aggregateRating directly
 */
$without_aggregate = array(
        'Apartment',
        'House',
        'SingleFamilyResidence',
        'Article',
        'Blogposting',
        'DiscussionForumPosting',
        'DataFeed',
        'FAQ',
        'NewsArticle',
        'qanda',        
        'TechArticle',
        'WebPage',
        'JobPosting',
        'Service',
        'Trip',
        'MedicalCondition',
        'TouristAttraction',
        'TouristDestination',
        'LandmarksOrHistoricalBuildings',
        'HinduTemple',
        'Church',
        'Mosque',
        'Person'
);

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
                        'sameAs'		=> isset($sd_data['saswp_social_links']) ? $sd_data['saswp_social_links'] : array(),                                        		
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
			'@context'		=> saswp_context_url(),
			'@type'			=> esc_attr($sd_data['saswp_kb_type']),
                        '@id'                   => $site_url.'#Person',
			'name'			=> esc_attr($sd_data['sd-person-name']),
                        'jobTitle'	        => esc_attr($sd_data['sd-person-job-title']),
			'url'			=> esc_url($sd_data['sd-person-url']),
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
        
        $all_schema_output = array();
        $recipe_json       = array();
        
        foreach($Conditionals as $schemaConditionals){
        
        $schema_options = array();    
            
        if(isset($schemaConditionals['schema_options'])){
            $schema_options = $schemaConditionals['schema_options'];
        }   
        	        
	$schema_type      = saswp_remove_warnings($schemaConditionals, 'schema_type', 'saswp_string');         
        $schema_post_id   = saswp_remove_warnings($schemaConditionals, 'post_id', 'saswp_string');        
           
        $input1         = array();
        $logo           = ''; 
        $height         = '';
        $width          = '';        
        $site_name      = get_bloginfo();    
        
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
        }                                                                   
				   		                                                                                           		
			$image_id 	= get_post_thumbnail_id();
			$image_details 	= wp_get_attachment_image_src($image_id, 'full');						
			$date 		= get_the_date("c");
			$modified_date 	= get_the_modified_date("c");
			$author_name 	= get_the_author();
                        $author_id      = get_the_author_meta('ID');   
                        
                        if(!$author_name){
				
                        $author_id    = get_post_field('post_author', $schema_post_id);
		        $author_name = get_the_author_meta( 'display_name' , $author_id ); 
                        
			}
                                                
                        $saswp_review_details   = get_post_meta(get_the_ID(), 'saswp_review_details', true); 
                        
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
                        
                        if( 'FAQ' === $schema_type){
                                                                                    
                            $input1['@context']                     = saswp_context_url();
                            $input1['@type']                        = 'FAQPage';
                            $input1['@id']                          = trailingslashit(saswp_get_permalink()).'#FAQPage';                             
                            $input1['headline']                     = saswp_get_the_title();
                            $input1['keywords']                     = saswp_get_the_tags();
                            $input1['datePublished']                = esc_html($date);
                            $input1['dateModified']                 = esc_html($modified_date);
                            $input1['dateCreated']                  = esc_html($date);
                            $input1['author']                       = saswp_get_author_details();											                            
                            
                            
                            $input1 = apply_filters('saswp_modify_faq_schema_output', $input1 );
                            
                            $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                            
                                                                                                                                                                                                                                                           
                         }
                       
                        if( 'VideoGame' === $schema_type){
                                                                                    
                            $input1['@context']                     = saswp_context_url();
                            $input1['@type']                        = 'VideoGame';
                            $input1['@id']                          = trailingslashit(saswp_get_permalink()).'#VideoGame';                             
                            $input1['author']['@type']              = 'Organization';                                                        
                            $input1['offers']['@type']              = 'Offer';   
                            
                            $input1 = saswp_append_fetched_reviews($input1, $schema_post_id);
                            
                            $input1 = apply_filters('saswp_modify_video_game_schema_output', $input1 );
                            
                            $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                                                                                                                                                                                                               
                            }
                        
                        if( 'MedicalCondition' === $schema_type){
                            
                            $input1['@context']                     = saswp_context_url();
                            $input1['@type']                        = 'MedicalCondition';
                            $input1['@id']                          = trailingslashit(saswp_get_permalink()).'#MedicalCondition';                                                                                                             
                            $input1['associatedAnatomy']['@type']   = 'AnatomicalStructure';                                                                                    
                            $input1['code']['@type']                = 'MedicalCode';
                            
                            $input1 = apply_filters('saswp_modify_medical_condition_schema_output', $input1 );
                            
                            $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                                                                                                                                                                                                               
                            }
                        
                        if( 'TVSeries' === $schema_type){
                                                        
                            $input1['@context']              = saswp_context_url();
                            $input1['@type']                 = 'TVSeries';
                            $input1['@id']                   = trailingslashit(saswp_get_permalink()).'#TVSeries';                                                                                                                                
                            $input1['author']['@type']       = 'Person';                            
                             
                            $input1 = saswp_append_fetched_reviews($input1, $schema_post_id);
                            
                            $input1 = apply_filters('saswp_modify_tvseries_schema_output', $input1 );
                            
                            $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
                            }
                        
                        if( 'HowTo' === $schema_type){
                                                         
                            $input1['@context']              = saswp_context_url();
                            $input1['@type']                 = 'HowTo';
                            $input1['@id']                   = trailingslashit(saswp_get_permalink()).'#HowTo';                                                                                                                  
                            $input1['estimatedCost']['@type']   = 'MonetaryAmount';  
                            
                            $input1 = saswp_append_fetched_reviews($input1, $schema_post_id);
                            
                            $input1 = apply_filters('saswp_modify_howto_schema_output', $input1 );
                            
                            $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                            
                            }
                        
                        if( 'Trip' === $schema_type){
                                                                                   
                            $input1['@context']              = saswp_context_url();
                            $input1['@type']                 = 'Trip';
                            $input1['@id']                   = trailingslashit(saswp_get_permalink()).'#Trip';    
                            
                            $input1 = apply_filters('saswp_modify_trip_schema_output', $input1 );
                            
                            $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                            
                           }
                        
                        if( 'SingleFamilyResidence' === $schema_type){
                                                                                                                                            
                            $input1['@context']              = saswp_context_url();
                            $input1['@type']                 = 'SingleFamilyResidence';
                            $input1['@id']                   = trailingslashit(saswp_get_permalink()).'#SingleFamilyResidence';                            
                            $input1['address']['@type']      = 'PostalAddress';
                                                        
                            $input1 = apply_filters('saswp_modify_apartment_schema_sfr', $input1 );
                            
                            $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                            
                            }
                        
                        if( 'House' === $schema_type){
                                                                            
                            $input1['@context']              = saswp_context_url();
                            $input1['@type']                 = 'House';
                            $input1['@id']                   = trailingslashit(saswp_get_permalink()).'#House';
                            $input1['address']['@type']      = 'PostalAddress';
                            
                            $input1 = apply_filters('saswp_modify_apartment_schema_house', $input1 );
                            
                            $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                                                                      
                            }
                            
                        if( 'Apartment' === $schema_type){
                                                                                   
                            $input1['@context']              = saswp_context_url();
                            $input1['@type']                 = 'Apartment';
                            $input1['@id']                   = trailingslashit(saswp_get_permalink()).'#Apartment';                                                                                                                                                                            
                            $input1['address']['@type']      = 'PostalAddress';    
                            
                            $input1 = apply_filters('saswp_modify_apartment_schema_output', $input1 );
                            
                            $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                           
                            }
                            
                        if( 'MusicPlaylist' === $schema_type){
                                                                                                                                                                        
                            $input1['@context']              = saswp_context_url();
                            $input1['@type']                 = 'MusicPlaylist';
                            $input1['@id']                   = trailingslashit(get_permalink()).'#MusicPlaylist'; 
                            
                            $input1 = saswp_append_fetched_reviews($input1, $schema_post_id);
                            
                            $input1 = apply_filters('saswp_modify_music_playlist_schema_output', $input1 );
                            
                            $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                                                                    
                          }
                          
                          if( 'Book' === $schema_type){
                                                                                                                                                                        
                            $input1['@context']              = saswp_context_url();
                            $input1['@type']                 = 'Book';
                            $input1['@id']                   = trailingslashit(get_permalink()).'#Book'; 
                            
                            $service = new saswp_output_service();
                            $woo_markp = $service->saswp_schema_markup_generator($schema_type);
                                
                            if($woo_markp){
                                $input1 = array_merge($input1, $woo_markp);
                            }

                            unset($input1['brand'], $input1['mpn'], $input1['sku'],$input1['gtin8']);
                            
                            $input1 = saswp_append_fetched_reviews($input1, $schema_post_id);
                            
                            $input1 = apply_filters('saswp_modify_music_playlist_schema_output', $input1 );
                            
                            $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                                                                    
                          }
                                                    
                          if( 'MusicAlbum' === $schema_type){
                                                                                                                                                                        
                            $input1['@context']              = saswp_context_url();
                            $input1['@type']                 = 'MusicAlbum';
                            $input1['@id']                   = trailingslashit(get_permalink()).'#MusicAlbum';  
                            
                            $input1 = saswp_append_fetched_reviews($input1, $schema_post_id);
                            $input1 = apply_filters('saswp_modify_music_album_schema_output', $input1 );
                            
                            $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                                                                                                                            
                          }    
                                                    
                        if( 'TouristDestination' === $schema_type){
                                                                                                                
                            $input1['@context']              = saswp_context_url();
                            $input1['@type']                 = 'TouristDestination';
                            $input1['@id']                   = trailingslashit(saswp_get_permalink()).'#TouristDestination';                                                                                   
                            $input1['address']['@type']             = 'PostalAddress';
                            
                            $input1 = apply_filters('saswp_modify_tourist_destination_schema_output', $input1 );
                            
                            $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                                        
                            }
                        
                        if( 'TouristAttraction' === $schema_type){
                                                   
                            $input1['@context']              = saswp_context_url();
                            $input1['@type']                 = 'TouristAttraction';
                            $input1['@id']                   = trailingslashit(saswp_get_permalink()).'#TouristAttraction';                              
                            $input1['address']['@type']      = 'PostalAddress';   
                            
                            $input1 = apply_filters('saswp_modify_tourist_attraction_schema_output', $input1 );
                            
                            $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                                                                   
                            }
                        
                        if( 'LandmarksOrHistoricalBuildings' === $schema_type){
                                                   
                            $input1['@context']              = saswp_context_url();
                            $input1['@type']                 = 'LandmarksOrHistoricalBuildings';
                            $input1['@id']                   = trailingslashit(saswp_get_permalink()).'#LandmarksOrHistoricalBuildings';                                                        
                            $input1['address']['@type']      = 'PostalAddress';   
                            
                            $input1 = apply_filters('saswp_modify_lorh_schema_output', $input1 );
                            
                            $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                                        
                            }
                        
                        if( 'HinduTemple' === $schema_type){
                                                                                   
                            $input1['@context']              = saswp_context_url();
                            $input1['@type']                 = 'HinduTemple';
                            $input1['@id']                   = trailingslashit(saswp_get_permalink()).'#HinduTemple';
                            $input1['address']['@type']             = 'PostalAddress';  
                            
                            $input1 = apply_filters('saswp_modify_hindu_temple_schema_output', $input1 );
                            
                            $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                                                                   
                           }
                        
                        if( 'Church' === $schema_type){
                                                                                  
                            $input1['@context']              = saswp_context_url();
                            $input1['@type']                 = 'Church';
                            $input1['@id']                   = trailingslashit(saswp_get_permalink()).'#Church';                            
                            $input1['address']['@type']      = 'PostalAddress';
                            
                            $input1 = apply_filters('saswp_modify_church_schema_output', $input1 );
                            
                            $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                                    
                            }
                        
                        if( 'Mosque' === $schema_type){
                                                                                                                
                            $input1['@context']              = saswp_context_url();
                            $input1['@type']                 = 'Mosque';
                            $input1['@id']                   = trailingslashit(saswp_get_permalink()).'#Mosque';                            
                            $input1['address']['@type']      = 'PostalAddress';  
                            
                            $input1 = apply_filters('saswp_modify_mosque_schema_output', $input1 );
                            
                            $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                                                                   
                            }
                        
                        if( 'JobPosting' === $schema_type){
                                                                                   
                            $input1['@context']                        = saswp_context_url();
                            $input1['@type']                           = 'JobPosting';
                            $input1['@id']                             = trailingslashit(saswp_get_permalink()).'#JobPosting';                                                          
                            $input1['hiringOrganization']['@type']     = 'Organization';                                                                                                                
                            $input1['jobLocation']['@type']            = 'Place';
                            $input1['jobLocation']['address']['@type'] = 'PostalAddress';                                                                                   
                            $input1['baseSalary']['@type']             = 'MonetaryAmount';                            
                            $input1['baseSalary']['value']['@type']    = 'QuantitativeValue';     
                            
                            $input1 = apply_filters('saswp_modify_jobposting_schema_output', $input1 );
                            
                            $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                                        
                            }
                        
                        if( 'Person' === $schema_type){
                                                                                                                                                                     
                            $input1['@context']              = saswp_context_url();
                            $input1['@type']                 = 'Person';
                            $input1['@id']                   = trailingslashit(saswp_get_permalink()).'#Person';                                                        
                            $input1['address']['@type']      = 'PostalAddress';             
                            
                            $input1 = apply_filters('saswp_modify_person_schema_output', $input1 );
                            
                            $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                            
                         }
                        
                        if( 'Course' === $schema_type){
                            
                        $description = saswp_get_the_excerpt();

                        if(!$description){
                            $description = get_bloginfo('description');
                        }
                         
                        $input1 = array(
			'@context'			=> saswp_context_url(),
			'@type'				=> $schema_type ,
                        '@id'				=> trailingslashit(saswp_get_permalink()).'#course',    
			'name'			        => saswp_get_the_title(),
			'description'                   => $description,			
			'url'				=> trailingslashit(saswp_get_permalink()),
			'datePublished'                 => esc_html($date),
			'dateModified'                  => esc_html($modified_date),
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
                                if(isset($sd_data['saswp_comments_schema']) && $sd_data['saswp_comments_schema'] ==1){
                                   $input1['comment'] = saswp_get_comments(get_the_ID());
                                }
                            
                                $input1 = apply_filters('saswp_modify_course_schema_output', $input1 );
                                
                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                        }
                                                
                        if( 'DiscussionForumPosting' === $schema_type){
                                                     
                            if(isset($sd_data['saswp-bbpress']) && $sd_data['saswp-bbpress'] == 1 && is_plugin_active('bbpress/bbpress.php')){                                                                                                                                                                                            
                                
                                $input1 = array(
                                '@context'			=> saswp_context_url(),
                                '@type'				=> 'DiscussionForumPosting' ,
                                '@id'				=> bbp_get_topic_permalink().'#discussionforumposting',
                                'mainEntityOfPage'              => bbp_get_topic_permalink(), 
                                'headline'			=> bbp_get_topic_title(get_the_ID()),
                                'description'                   => saswp_get_the_excerpt(),
                                "articleSection"                => bbp_get_forum_title(),
                                "articleBody"                   => saswp_get_the_content(),    
                                'url'				=> bbp_get_topic_permalink(),
                                'datePublished'                 => saswp_format_date_time(bbp_get_topic_post_date()),
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
                                '@id'				=> trailingslashit(saswp_get_permalink()).'#blogposting',    			
                                'url'				=> trailingslashit(saswp_get_permalink()),
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
                        }
                        
                        if( 'Blogposting' === $schema_type){
                         
                        $input1 = array(
			'@context'			=> saswp_context_url(),
			'@type'				=> 'BlogPosting' ,
                        '@id'				=> trailingslashit(saswp_get_permalink()).'#blogposting',    
                        'url'				=> trailingslashit(saswp_get_permalink()),
                        'inLanguage'                    => get_bloginfo('language'),    
			'mainEntityOfPage'              => trailingslashit(saswp_get_permalink()),
			'headline'			=> saswp_get_the_title(),
			'description'                   => saswp_get_the_excerpt(),
                        'articleBody'                   => saswp_get_the_content(), 
                        'keywords'                      => saswp_get_the_tags(),    
			'name'				=> saswp_get_the_title(),			
			'datePublished'                 => esc_html($date),
			'dateModified'                  => esc_html($modified_date),
			'author'			=> saswp_get_author_details()											
                        );
                        
                                $mainentity = saswp_get_mainEntity($schema_post_id);
                                
                                if($mainentity){
                                   $input1['mainEntity'] = $mainentity;                                     
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
                            
                                $input1 = apply_filters('saswp_modify_blogposting_schema_output', $input1 ); 
                                
                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                        }
                        
                        if( 'AudioObject' === $schema_type){
                                                                                                     
                            $input1 = array(
                            '@context'			=> saswp_context_url(),
                            '@type'				=> $schema_type ,	
                            '@id'				=> trailingslashit(saswp_get_permalink()).'#audioobject',     			
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
                        }
                        
                        if( 'Event' === $schema_type){
                            
                                $event_type         = get_post_meta($schema_post_id, 'saswp_event_type', true);  
                            
                                $input1['@context'] =  saswp_context_url();
                                $input1['@type']    =  $event_type ? $event_type : $schema_type;
                                $input1['@id']      =  trailingslashit(saswp_get_permalink()).'#event';
                                                                                       
                                if(!empty($aggregateRating)){
                                    $input1['aggregateRating'] = $aggregateRating;
                                }                                
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }                                                                                                
                                $input1 = saswp_append_fetched_reviews($input1, $schema_post_id);
                                                                                            
                                $input1 = apply_filters('saswp_modify_event_schema_output', $input1 );
                                
                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                        
                        }
                        
                        if( 'SoftwareApplication' === $schema_type){
                                                                                                           
                                $input1 = array(
                                '@context'			=> saswp_context_url(),
                                '@type'				=> $schema_type ,
                                '@id'				=> trailingslashit(saswp_get_permalink()).'#softwareapplication',         						                        
                                'datePublished'                 => esc_html($date),
                                'dateModified'                  => esc_html($modified_date),
                                'author'			=> saswp_get_author_details()			
                                );
                        
                                $service   = new saswp_output_service();
                                $woo_markp = $service->saswp_schema_markup_generator($schema_type);
                                
                                if($woo_markp){
                                    $input1 = array_merge($input1, $woo_markp);
                                }
                                                                
                                unset($input1['brand'], $input1['mpn'], $input1['sku'],$input1['gtin8']);
                                
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
                        }
			
			if( 'WebPage' === $schema_type){                            				
                                
                                $service = new saswp_output_service();
                                $input1 = $service->saswp_schema_markup_generator($schema_type);
				                                
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
			}	
		
			if( 'Article' === $schema_type ){
                            
                                $service = new saswp_output_service();
                                $input1 = $service->saswp_schema_markup_generator($schema_type);
                                
                                $mainentity = saswp_get_mainEntity($schema_post_id);
                                
                                if($mainentity){
                                   $input1['mainEntity'] = $mainentity;                                     
                                }
				                                
                                if(isset($sd_data['saswp_comments_schema']) && $sd_data['saswp_comments_schema'] ==1){
                                    $input1['comment'] = saswp_get_comments(get_the_ID());
                                }                                                                
                                $input1 = apply_filters('saswp_modify_article_schema_output', $input1 );  
                                
                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
			}
                        
                        if( 'TechArticle' === $schema_type ){
                                
                                $service = new saswp_output_service();
                                $input1 = $service->saswp_schema_markup_generator($schema_type);
                                
                                $mainentity = saswp_get_mainEntity($schema_post_id);
                                
                                if($mainentity){
                                   $input1['mainEntity'] = $mainentity;                                     
                                }
				                                
                                if(isset($sd_data['saswp_comments_schema']) && $sd_data['saswp_comments_schema'] ==1){
                                    $input1['comment'] = saswp_get_comments(get_the_ID());
                                }
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }
                                
                                $input1 = apply_filters('saswp_modify_tech_article_schema_output', $input1 );
                                
                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
			}
		      
			if( 'Recipe' === $schema_type){
                        
                            if(isset($sd_data['saswp-wp-recipe-maker']) && $sd_data['saswp-wp-recipe-maker'] == 1){
                                                              
                                $recipe_ids = saswp_get_ids_from_content_by_type('wp_recipe_maker');
                                                                
                                if($recipe_ids){

                                    foreach($recipe_ids as $recipe){

                                        if(class_exists('WPRM_Recipe_Manager')){
                                            $recipe_arr    = WPRM_Recipe_Manager::get_recipe( $recipe );
                                            $recipe_json[] = saswp_wp_recipe_schema_json($recipe_arr);                                            
                                        }

                                    }  
                                    
                                 }
                                
                                 
                            }else{
                                
                               if(empty($image_details[0]) || $image_details[0] === NULL ){
					$image_details[0] = $sd_data['sd_logo']['url'];
				}
                                
				$input1 = array(
                                    '@context'			=> saswp_context_url(),
                                    '@type'				=> $schema_type ,
                                    '@id'				=> trailingslashit(saswp_get_permalink()).'#recipe',    
                                    'url'				=> trailingslashit(saswp_get_permalink()),
                                    'name'			        => saswp_get_the_title(),
                                    'datePublished'                 => esc_html($date),
                                    'dateModified'                  => esc_html($modified_date),
                                    'description'                   => saswp_get_the_excerpt(),
                                    'keywords'                      => saswp_get_the_tags(), 
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
                                                                                                
                            }
                            				                                
                               $input1 = apply_filters('saswp_modify_recipe_schema_output', $input1 );
                               
                               $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
			}
                       
                        if( 'qanda' === $schema_type){
                                                        
                            if(isset($sd_data['saswp-dw-question-answer']) && $sd_data['saswp-dw-question-answer'] ==1){
                            
                                $service_object = new saswp_output_service();
                                $input1  = $service_object->saswp_dw_question_answers_details(get_the_ID()); 
                                
                            }

                            if(isset($sd_data['saswp-bbpress']) && $sd_data['saswp-bbpress'] ==1){
                            
                                $service_object = new saswp_output_service();
                                $input1  = $service_object->saswp_bb_press_topic_details(get_the_ID()); 
                                
                            }
                                                                                                                                                                        
                            
                            $input1 = apply_filters('saswp_modify_qanda_schema_output', $input1 );
                            
                            $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
			}
                                                                      
			if( 'Product' === $schema_type){
                            		                                                                
                                $service = new saswp_output_service();
                                $input1 = $service->saswp_schema_markup_generator($schema_type);
                                  
                                $input1 = saswp_append_fetched_reviews($input1, $schema_post_id);
                                                                                                
                                $input1 = apply_filters('saswp_modify_product_schema_output', $input1 );
                                
                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
			}
                        
                        if( 'NewsArticle' === $schema_type ){                              
                            
                            $category_detail = get_the_category(get_the_ID());//$post->ID
                            $article_section = '';
                            
                            foreach($category_detail as $cd){
                                
                                $article_section =  $cd->cat_name;
                            
                            }
                                $word_count = saswp_reading_time_and_word_count();
				$input1 = array(
					'@context'			=> saswp_context_url(),
					'@type'				=> $schema_type ,
                                        '@id'				=> trailingslashit(saswp_get_permalink()).'#newsarticle',
					'url'				=> trailingslashit(saswp_get_permalink()),
					'headline'			=> saswp_get_the_title(),
                                        'mainEntityOfPage'	        => get_the_permalink(),            
					'datePublished'                 => esc_html($date),
					'dateModified'                  => esc_html($modified_date),
					'description'                   => saswp_get_the_excerpt(),
                                        'articleSection'                => $article_section,            
                                        'articleBody'                   => saswp_get_the_content(), 
                                        'keywords'                      => saswp_get_the_tags(),
					'name'				=> saswp_get_the_title(), 					
					'thumbnailUrl'                  => saswp_remove_warnings($image_details, 0, 'saswp_string'),
                                        'wordCount'                     => saswp_remove_warnings($word_count, 'word_count', 'saswp_string'),
                                        'timeRequired'                  => saswp_remove_warnings($word_count, 'timerequired', 'saswp_string'),            
					'mainEntity'                    => array(
                                                                            '@type' => 'WebPage',
                                                                            '@id'   => trailingslashit(saswp_get_permalink()),
						), 
					'author'			=> saswp_get_author_details()					                                                    
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
                                                                
                                if(!empty($aggregateRating)){
                                    $input1['aggregateRating'] = $aggregateRating;
                                }                                
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }
                                
                                $input1 = apply_filters('saswp_modify_news_article_schema_output', $input1 );
                                
                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
				}
                                                
                        if( 'Service' === $schema_type ){  
                                                                                                 
				$input1['@context'] =  saswp_context_url();
                                $input1['@type']    =  $schema_type;
                                $input1['@id']      =  trailingslashit(saswp_get_permalink()).'#service';
                                                                                                
                                if(!empty($aggregateRating)){
                                    $input1['aggregateRating'] = $aggregateRating;
                                }                                
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }
                                
                                $input1 = saswp_append_fetched_reviews($input1, $schema_post_id);
                                                                                                
                                $input1 = apply_filters('saswp_modify_service_schema_output', $input1 );
                                
                                $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
                                
				}  
                        
                        if('Review' === $schema_type){
                                                            
                                $service = new saswp_output_service();
                                $review_markup = $service->saswp_replace_with_custom_fields_value($input1, $schema_post_id);                                
                                $item_reviewed = get_post_meta($schema_post_id, 'saswp_review_item_reviewed_'.$schema_post_id, true);
                                
                                if($item_reviewed == 'local_business'){
                                    $item_reviewed = 'LocalBusiness';
                                }
                                
                                $input1['@context']               =  saswp_context_url();
                                $input1['@type']                  =  'Review';
                                $input1['@id']                    =  trailingslashit(saswp_get_permalink()).'#Review';
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
                                                                                     
                                if(isset($sd_data['saswp-tagyeem']) && $sd_data['saswp-tagyeem'] == 1 && (is_plugin_active('taqyeem/taqyeem.php') || get_template() != 'jannah') ){                                                                                                      
                           
                                   remove_action( 'TieLabs/after_post_entry',  'tie_article_schemas' );

                                   $input1 = array(
                                           '@context'       => saswp_context_url(),
                                           '@type'          => 'Review',
                                           '@id'	     => trailingslashit(saswp_get_permalink()).'#review',
                                           'dateCreated'    => esc_html($date),
                                           'datePublished'  => esc_html($date),
                                           'dateModified'   => esc_html($modified_date),
                                           'headline'       => saswp_get_the_title(),
                                           'name'           => saswp_get_the_title(),
                                           'keywords'       => tie_get_plain_terms( get_the_ID(), 'post_tag' ),
                                           'url'            => trailingslashit(saswp_get_permalink()),
                                           'description'    => saswp_get_the_excerpt(),
                                           'articleBody'    => saswp_get_the_content(),
                                           'copyrightYear'  => get_the_time( 'Y' ),                                                                                                           
                                           'author'	     => saswp_get_author_details()                                                        

                                           );

                                           $total_score = (int) get_post_meta( get_the_ID(), 'taq_review_score', true );

                                           if( ! empty( $total_score ) && $total_score > 0 ){

                                               $total_score = round( ($total_score*5)/100, 1 );

                                           }

                                           $input1['itemReviewed'] = array(
                                                   '@type' => 'Organization',
                                                   'name'  => saswp_get_the_title(),
                                           );

                                           $input1['reviewRating'] = array(
                                               '@type'       => 'Rating',
                                               'worstRating' => 1,
                                               'bestRating'  => 5,
                                               'ratingValue' => esc_attr($total_score),
                                               'description' => get_post_meta( get_the_ID(), 'taq_review_summary', true ),
                                            );    

                                }
                                
                                $input1 = apply_filters('saswp_modify_service_schema_output', $input1 );
                                                        
                        }        
                                			
			if( 'VideoObject' === $schema_type){
                            
                                            if(empty($image_details[0]) || $image_details[0] === NULL ){

                                                    if(isset($sd_data['sd_logo'])){
                                                        $image_details[0] = $sd_data['sd_logo']['url'];
                                                    }

                                            }				
                                                $description = saswp_get_the_excerpt();

                                                if(!$description){
                                                    $description = get_bloginfo('description');
                                                }                                                                                                                        
						$input1 = array(
						'@context'			=> saswp_context_url(),
						'@type'				=> 'VideoObject',
                                                '@id'                           => trailingslashit(saswp_get_permalink()).'#videoobject',        
						'url'				=> trailingslashit(saswp_get_permalink()),
						'headline'			=> saswp_get_the_title(),
						'datePublished'                 => esc_html($date),
						'dateModified'                  => esc_html($modified_date),
						'description'                   => $description,
						'name'				=> saswp_get_the_title(),
						'uploadDate'                    => esc_html($date),
						'thumbnailUrl'                  => isset($image_details[0]) ? esc_url($image_details[0]):'',						
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
                                                 $input1 = apply_filters('saswp_modify_video_object_schema_output', $input1 );
                                                 
                                                 $input1 = saswp_get_modified_markup($input1, $schema_type, $schema_post_id, $schema_options);
					                                        
				}
                                
                        if( 'ImageObject' === $schema_type){
                                                                        				
                                                $description = saswp_get_the_excerpt();

                                                if(!$description){
                                                    $description = get_bloginfo('description');
                                                }                                                                                                                        
						$input1 = array(
						'@context'			=> saswp_context_url(),
						'@type'				=> 'ImageObject',
                                                '@id'                           => trailingslashit(saswp_get_permalink()).'#imageobject',        
						'url'				=> trailingslashit(saswp_get_permalink()),						                                                
						'datePublished'                 => esc_html($date),
						'dateModified'                  => esc_html($modified_date),
                                                'name'				=> saswp_get_the_title(),
						'description'                   => $description,						
                                                'contentUrl'			=> trailingslashit(saswp_get_permalink()),						
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
					                                        
				}        
                        
                        if( 'local_business' === $schema_type){
                            
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
                                    '@id'                               => trailingslashit(saswp_get_permalink()).'#'. strtolower(esc_attr($local_business)),                                            
                                    'url'				=> trailingslashit(saswp_get_permalink()),								
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
                        
                        global $without_aggregate;
                        
                        if(!in_array($schema_type, $without_aggregate)){ 
                                                     
                            
                                    if($schema_type == 'Review'){
                                        
                                        //kk star rating 
                            
                                    $kkstar_aggregateRating = saswp_extract_kk_star_ratings();
                                
                                    if(!empty($kkstar_aggregateRating)){
                                        $input1['itemReviewed']['aggregateRating'] = $kkstar_aggregateRating; 
                                    }

                                    //wp post-rating star rating 

                                    $wp_post_rating_ar = saswp_extract_wp_post_ratings();

                                    if(!empty($wp_post_rating_ar)){
                                        $input1['itemReviewed']['aggregateRating'] = $wp_post_rating_ar; 
                                    }
                                        
                                    }else{
                                    
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
                                      
                                    //BNE Testomonials
                                    $bne_testomonials = saswp_get_bne_testomonials();   
                                                                        
                                    if($bne_testomonials){
                                        
                                          $input1 = array_merge($input1,$bne_testomonials['rating']);
                                          
                                          if(isset($input1['review'])){
                                              $input1 = array_merge($input1['review'],$bne_testomonials['reviews']);
                                          }else{
                                              $input1['review'] = $bne_testomonials['reviews'];
                                          }
                                          
                                    }
                                    
                                    //Easy Testomonials
                                    $testomonials = saswp_get_easy_testomonials();   
                                    
                                    if($testomonials){
                                        
                                          $input1 = array_merge($input1,$testomonials['rating']);
                                          
                                          if(isset($input1['review'])){
                                              $input1 = array_merge($input1['review'],$testomonials['reviews']);
                                          }else{
                                              $input1['review'] = $testomonials['reviews'];
                                          }
                                          
                                    }

                                    // Testomonial Pro
                                    $testomonial_pro = saswp_get_testomonial_pro();   
                                    
                                    if($testomonial_pro){
                                        
                                          $input1 = array_merge($input1,$testomonial_pro['rating']);
                                          
                                          if(isset($input1['review'])){
                                              $input1 = array_merge($input1['review'],$testomonial_pro['reviews']);
                                          }else{
                                              $input1['review'] = $testomonial_pro['reviews'];
                                          }
                                          
                                    }
                                    
                                    
                                    // Testomonial Pro
                                    $strong_testimonials = saswp_get_strong_testimonials();   
                                    
                                    if($strong_testimonials){
                                        
                                          $input1 = array_merge($input1,$strong_testimonials['rating']);
                                          
                                          if(isset($input1['review'])){
                                              $input1 = array_merge($input1['review'],$strong_testimonials['reviews']);
                                          }else{
                                              $input1['review'] = $strong_testimonials['reviews'];
                                          }
                                          
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
               
        }   
                
        if($recipe_json){
            foreach($recipe_json as $json){
                array_push($all_schema_output, $json);
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
                $input['@id']             =  trailingslashit($sd_data['breadcrumb_url']).'#breadcrumb';
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
                 
                $site_url  = get_home_url();
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
                        $input['potentialAction']['target']      = esc_url($site_url).'/?s={search_term_string}';
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
    
    if ( function_exists('is_product_category') && is_product_category()) {
            		
                $list_item     = array();
                $term          = get_queried_object();
                $service       = new saswp_output_service();                
		$category_loop = new WP_Query( $query_string );
                
                $current_url = saswp_get_current_url();
                
                $i = 1;
                
		if ( $category_loop->have_posts() ):
			while( $category_loop->have_posts() ): $category_loop->the_post();
                
                        $category_posts = array();
                        $category_posts['@type']       = 'ListItem';
                        $category_posts['position']    = $i;
			$category_posts['item']        = $service->saswp_schema_markup_generator('Product');
                        
                        $feature_image           = $service->saswp_get_fetaure_image();
                        $category_posts['item']  = array_merge( $category_posts['item'], $feature_image);
                        
                        if(saswp_has_slash($current_url)){
                            $category_posts['item']['url'] =  trailingslashit(saswp_get_category_link($term->term_id)). "#product_".$i;    
                        }else{
                            $category_posts['item']['url'] =  saswp_remove_slash(saswp_get_category_link($term->term_id)). "#product_".$i;    
                        }
                                                
                        unset($category_posts['item']['@id']);
                        unset($category_posts['item']['@context']);
                        $list_item[] = $category_posts;
                        
                        $i++;
	        endwhile;

		wp_reset_postdata();
                 
                $item_list_schema = array();
                
                if($list_item){                    
                    $item_list_schema['@context']        = saswp_context_url();
                    $item_list_schema['@type']           = 'ItemList';                                      
                    $item_list_schema['url']             = get_category_link($term->term_id);
                    $item_list_schema['itemListElement'] = $list_item;
                }
                                                                                
		return $item_list_schema;
                
	endif;
        
	}
            
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
        $output = array();
        
        if(isset($sd_data['sd_name']) && $sd_data['sd_name'] !=''){
          $site_name = $sd_data['sd_name'];  
        }else{
          $site_name = get_bloginfo();    
        } 	
        	
	if(isset($sd_data['saswp_archive_schema']) && $sd_data['saswp_archive_schema'] == 1){
            
        $schema_type        =  $sd_data['saswp_archive_schema_type'];   
            
        $service_object     = new saswp_output_service();
        $logo               = $service_object->saswp_get_publisher(true);    
            					
	if ( is_category() || is_tax() ) {
            
		$category_posts = array();
                $item_list      = array();                
                
                $i = 1;
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
                                        $archive_image['url']    = isset($sd_data['sd_default_image']['url']) ? esc_url($sd_data['sd_default_image']['url']):'';
                                        $archive_image['width']  = esc_attr($sd_data['sd_default_image_width']);
                                        $archive_image['height'] = esc_attr($sd_data['sd_default_image_height']);                                  
                                    }
                                                                        
                                }
                                                                
                                $publisher_info['type']  = 'Organization';                                
                                $publisher_info['name']  = esc_attr($site_name);
                                $publisher_info['logo']['@type']  = 'ImageObject';
                                $publisher_info['logo']['url']    = isset($logo['url'])    ? esc_attr($logo['url']):'';
                                $publisher_info['logo']['width']  = isset($logo['width'])  ? esc_attr($logo['width']):'';
                                $publisher_info['logo']['height'] = isset($logo['height']) ? esc_attr($logo['height']):'';
                                                                                                                                								                               
                                $schema_properties['@type']            = esc_attr($schema_type);
                                $schema_properties['headline']         = saswp_get_the_title();
                                $schema_properties['url']              = get_the_permalink();                                                                                                
                                $schema_properties['datePublished']    = get_the_date('c');
                                $schema_properties['dateModified']     = get_the_modified_date('c');
                                $schema_properties['mainEntityOfPage'] = get_the_permalink();
                                $schema_properties['author']           = get_the_author();
                                $schema_properties['publisher']        = $publisher_info;                                
                                                                                                
                                if(!empty($archive_image['url'])){                                
                                    $schema_properties['image']            = $archive_image;                                    
                                }
                                                                                                
                                $category_posts[] =  $schema_properties;
                                                                                                                                                                                                
                                $item_list[] = array(
                                         '@type' 		=> 'ListItem',
                                         'position' 		=> $i,
                                         'url' 		        => get_the_permalink(),
                                         
                                );
                                
				$i++;
	        endwhile;

		wp_reset_postdata();
                
		$category 		= get_queried_object(); 		
		
                if(is_object($category)){
                    
                $category_id 		= intval($category->term_id); 
                $category_link 		= get_category_link( $category_id );
		$category_link          = get_term_link( $category_id);
                $category_headline 	= single_cat_title( '', false ) . __(' Category', 'schema-wp');	
                
		$collection_page = array(       		
				'@context' 		=> saswp_context_url(),
				'@type' 		=> "CollectionPage",
                                '@id' 		        => trailingslashit(esc_url($category_link)).'#CollectionPage',
				'headline' 		=> esc_attr($category_headline),
				'description' 	        => strip_tags(term_description($category_id)),
				'url'		 	=> esc_url($category_link),				
				'hasPart' 		=> $category_posts
       		);
                
                $blog_page = array(       		
				'@context' 		=> saswp_context_url(),
				'@type' 		=> "Blog",
                                '@id' 		        => trailingslashit(esc_url($category_link)).'#Blog',
				'headline' 		=> esc_attr($category_headline),
				'description' 	        => strip_tags(term_description($category_id)),
				'url'		 	=> esc_url($category_link),				
				'blogPost' 		=> $category_posts
       		);
                                
                $item_list_schema['@context']        = saswp_context_url();
                $item_list_schema['@type']           = 'ItemList';
                $item_list_schema['itemListElement'] = $item_list;
                                
                if($schema_type == 'BlogPosting'){
                    $output = array($item_list_schema, $collection_page, $blog_page);
                }else{
                    $output = array($item_list_schema, $collection_page, array());
                }
                    
                }
                
	endif;				
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
				"@context" 	   => saswp_context_url(),
				"@type"		   => "AboutPage",
				"mainEntityOfPage" => array(
                                                            "@type"           => "WebPage",
                                                            "@id"             => trailingslashit(saswp_get_permalink()),
						),
				"url"		   => trailingslashit(saswp_get_permalink()),
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
        
	if(isset($sd_data['sd_contact_page']) && $sd_data['sd_contact_page'] == get_the_ID()){
                        
                        $service_object     = new saswp_output_service();
                        $feature_image      = $service_object->saswp_get_fetaure_image();
                        $publisher          = $service_object->saswp_get_publisher();
                        			
			$input = array(
                            
				"@context" 	    => saswp_context_url(),
				"@type"		    => "ContactPage",
				"mainEntityOfPage"  => array(
							"@type" => "WebPage",
							"@id" 	=> trailingslashit(saswp_get_permalink()),
							),
				"url"		   => trailingslashit(saswp_get_permalink()),
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
                $menuItems = get_transient('saswp_nav_menu');
                
                if(!$menuItems){
                    $menuItems = wp_get_nav_menu_items($menu_id);
                }
                
                $menu_name = wp_get_nav_menu_object($menu_id);
                                             
                if($menuItems){
                   
                        foreach($menuItems as $items){
                 
                              $navObj[] = array(
                                     "@context"  => saswp_context_url(),
                                     "@type"     => "SiteNavigationElement",
                                     "@id"       => trailingslashit(get_home_url()).'#'.$menu_name->name,
                                     "name"      => wp_strip_all_tags($items->title),
                                     "url"       => esc_url($items->url)
                              );

                        }                                                                                                                                                                                   
                    }
             
                    if($navObj){

                        $input['@context'] = saswp_context_url(); 
                        $input['@graph']   = $navObj; 

                    }
                    
            }                                                
                                        
    return apply_filters('saswp_modify_sitenavigation_output', $input);
}      

function saswp_gutenberg_how_to_schema(){
                        
            global $post;
            $input1 = array();
            
            if(function_exists('parse_blocks') && is_object($post)){
           
                
            $blocks = parse_blocks($post->post_content);
            
                if($blocks){

                foreach ($blocks as $parse_blocks){

                if(isset($parse_blocks['blockName']) && $parse_blocks['blockName'] === 'saswp/how-to-block'){
                    
                $service_object     = new saswp_output_service();   
                $feature_image      = $service_object->saswp_get_fetaure_image();                  
                                       
                $input1['@context']              = saswp_context_url();
                $input1['@type']                 = 'HowTo';
                $input1['@id']                   = trailingslashit(get_permalink()).'#HowTo';
                $input1['name']                  = saswp_get_the_title();                
                $input1['datePublished']         = get_the_date("c");
                $input1['dateModified']          = get_the_modified_date("c");
                
                if(!empty($feature_image)){
                            
                    $input1 = array_merge($input1, $feature_image);   
                         
                }                
                
                if(array_key_exists('description', $parse_blocks['attrs'])){
                    $input1['description']           = $parse_blocks['attrs']['description'];
                }
                
                $supply     = array();
                $supply_arr = array();
                
                if(array_key_exists('materials', $parse_blocks['attrs'])){
                    $supply = $parse_blocks['attrs']['materials'];
                }
                
                if(!empty($supply)){

                    foreach($supply as $val){

                        $supply_data = array();

                        if($val['name']){
                            $supply_data['@type'] = 'HowToSupply';
                            $supply_data['name']  = $val['name'];                            
                        }

                       $supply_arr[] =  $supply_data;
                    }
                   $input1['supply'] = $supply_arr;
                }
                                
                $tool     = array();
                $tool_arr = array();
                
                if(array_key_exists('tools', $parse_blocks['attrs'])){
                    $tool = $parse_blocks['attrs']['tools'];
                }
                
                if(!empty($tool)){

                    foreach($tool as $val){

                        $supply_data = array();

                        if($val['name']){
                            $supply_data['@type'] = 'HowToTool';
                            $supply_data['name']  = $val['name'];                            
                        }

                       $tool_arr[] =  $supply_data;
                    }
                   $input1['tool'] = $tool_arr;
                }
                                
                $step     = array();
                $step_arr = array(); 
                
                if(array_key_exists('items', $parse_blocks['attrs'])){
                    $step = $parse_blocks['attrs']['items'];
                }                                                           
                if(!empty($step)){

                    foreach($step as $key => $val){

                        $supply_data = array();
                        $direction   = array();
                        $tip         = array();

                       if($val['title'] || $val['description']){

                            if($val['title']){
                            $direction['@type']     = 'HowToDirection';
                            $direction['text']      = $val['title'];
                        }

                        if($val['description']){

                            $tip['@type']           = 'HowToTip';
                            $tip['text']            = $val['description'];

                        }

                        $supply_data['@type']   = 'HowToStep';
                        $supply_data['url']     = trailingslashit(get_permalink()).'#step'.++$key;
                        $supply_data['name']    = $val['title'];    

                        if(isset($direction['text']) || isset($tip['text'])){
                            $supply_data['itemListElement']  = array($direction, $tip);
                        }

                        if(isset($val['imageId']) && $val['imageId'] !=''){

                                    $image_details   = wp_get_attachment_image_src($val['imageId']);                                                 
                                    $supply_data['image']['@type']  = 'ImageObject';                                                
                                    $supply_data['image']['url']    = esc_url($image_details[0]);
                                    $supply_data['image']['width']  = esc_attr($image_details[1]);
                                    $supply_data['image']['height'] = esc_attr($image_details[2]);

                        }

                        $step_arr[] =  $supply_data;

                       }

                    }

                   $input1['step'] = $step_arr;

                }  
                
                 if(isset($parse_blocks['attrs']['days']) || $parse_blocks['attrs']['hours'] || $parse_blocks['attrs']['minutes']){
                     
                             $input1['totalTime'] = 'P'. 
                             ((isset($parse_blocks['attrs']['days']) && $parse_blocks['attrs']['days'] !='') ? esc_attr($parse_blocks['attrs']['days']).'DT':''). 
                             ((isset($parse_blocks['attrs']['hours']) && $parse_blocks['attrs']['hours'] !='') ? esc_attr($parse_blocks['attrs']['hours']).'H':''). 
                             ((isset($parse_blocks['attrs']['minutes']) && $parse_blocks['attrs']['minutes'] !='') ? esc_attr($parse_blocks['attrs']['minutes']).'M':''); 
                             
                 }   

                }

               }

                }
                
            }                       
            return $input1;
    
}


function saswp_gutenberg_faq_schema(){
                        
            global $post;
            $input1 = array();
                        
            if(function_exists('parse_blocks') && is_object($post)){
                
                $blocks = parse_blocks($post->post_content);

                if($blocks){

                foreach ($blocks as $parse_blocks){

                if(isset($parse_blocks['blockName']) && $parse_blocks['blockName'] === 'saswp/faq-block'){

                                $input1['@context']              = saswp_context_url();
                                $input1['@type']                 = 'FAQPage';
                                $input1['@id']                   = trailingslashit(get_permalink()).'#FAQPage';                            

                                $faq_question_arr = array();

                                if(!empty($parse_blocks['attrs']['items'])){

                                    foreach($parse_blocks['attrs']['items'] as $val){

                                        $supply_data = array();
                                        $supply_data['@type']                   = 'Question';
                                        $supply_data['name']                    = $val['title'];
                                        $supply_data['acceptedAnswer']['@type'] = 'Answer';
                                        $supply_data['acceptedAnswer']['text']  = $val['description'];

                                         if(isset($val['imageId']) && $val['imageId'] !=''){

                                            $image_details   = wp_get_attachment_image_src($val['imageId']);                                                 
                                            $supply_data['image']['@type']  = 'ImageObject';                                                
                                            $supply_data['image']['url']    = esc_url($image_details[0]);
                                            $supply_data['image']['width']  = esc_attr($image_details[1]);
                                            $supply_data['image']['height'] = esc_attr($image_details[2]);

                                          }

                                       $faq_question_arr[] =  $supply_data;
                                    }
                                   $input1['mainEntity'] = $faq_question_arr;
                                }

                          }

                     }

                }
                
            }

            return $input1;
    
}