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
                             
                             $input1 = saswp_person_schema_markup($schema_id, $schema_post_id, $all_post_meta);
                            
                         }         
                        
                         if( 'Trip' === $schema_type){
                             
                            $input1 = saswp_trip_schema_markup($schema_id, $schema_post_id, $all_post_meta);
                            
                         }   
                            
                         if( 'FAQ' === $schema_type){
                                                                                                                                                                        
                             $input1 = saswp_faq_schema_markup($schema_id, $schema_post_id, $all_post_meta);
                            
                          }  
                          
                          if( 'MusicPlaylist' === $schema_type){
                                                                                                                                                                        
                            $input1 = saswp_music_playlist_schema_markup($schema_id, $schema_post_id, $all_post_meta);
                            
                            $input1 = saswp_append_fetched_reviews($input1, $schema_post_id);
                            
                          }     
                          
                          if( 'MusicAlbum' === $schema_type){
                                                                                                                                                                        
                            $input1 = saswp_music_album_schema_markup($schema_id, $schema_post_id, $all_post_meta);
                            
                            $input1 = saswp_append_fetched_reviews($input1, $schema_post_id);
                            
                          }
                            
                          if( 'JobPosting' === $schema_type){
                             
                            $input1 = saswp_job_posting_schema_markup($schema_id, $schema_post_id, $all_post_meta);
                                                        
                          }      
                        
                          if( 'Mosque' === $schema_type){
                             
                              $input1 = saswp_mosque_schema_markup($schema_id, $schema_post_id, $all_post_meta);
                                                                                   
                          }  
                        
                         if( 'Church' === $schema_type){
                             
                             $input1 = saswp_church_schema_markup($schema_id, $schema_post_id, $all_post_meta);
                                                                                   
                            }   
                        
                         if( 'HinduTemple' === $schema_type){
                             
                             $input1 = saswp_hindu_temple_schema_markup($schema_id, $schema_post_id, $all_post_meta);
                             
                         }   
                        
                         if( 'LandmarksOrHistoricalBuildings' === $schema_type){
                             
                            $input1 = saswp_lorh_schema_markup($schema_id, $schema_post_id, $all_post_meta);
                                                                                   
                         } 
                         
                         if( 'Book' === $schema_type){
                             
                                $input1 = saswp_book_schema_markup($schema_id, $schema_post_id, $all_post_meta);
                            
                                $input1 = saswp_append_fetched_reviews($input1, $schema_post_id);
                            
                            }    
                        
                         if( 'TouristAttraction' === $schema_type){
                             
                             $input1 = saswp_tourist_attraction_schema_markup($schema_id, $schema_post_id, $all_post_meta);
                                                                                   
                         }
                         
                         if( 'TouristDestination' === $schema_type){
                             
                             $input1 = saswp_tourist_destination_schema_markup($schema_id, $schema_post_id, $all_post_meta);
                            
                         }   
                        
                         if( 'Apartment' === $schema_type){
                             
                            $input1 = saswp_apartment_schema_markup($schema_id, $schema_post_id, $all_post_meta);
                            
                         }
                                                                                                                                            
                         if( 'House' === $schema_type){
                             
                             $input1 = saswp_house_schema_makrup($schema_id, $schema_post_id, $all_post_meta);
                            
                         }  
                            
                         if( 'SingleFamilyResidence' === $schema_type){
                             
                            $input1 = saswp_single_family_residence_schema_markup($schema_id, $schema_post_id, $all_post_meta);
                            
                         }     
                                                
                         if( 'HowTo' === $schema_type){
                             
                             $input1 = saswp_howto_schema_markup($schema_id, $schema_post_id, $all_post_meta);
                             
                             $input1 = saswp_append_fetched_reviews($input1, $schema_post_id);
                                                       
                            }
                            
                         if( 'TVSeries' === $schema_type){
                             
                            $input1 = saswp_tv_series_schema_markup($schema_id, $schema_post_id, $all_post_meta);
                                                                 
                            $input1 = saswp_append_fetched_reviews($input1, $schema_post_id);
                            
                         }   
                            
                         if( 'MedicalCondition' === $schema_type){
                                                         
                            $input1 = saswp_medical_condition_schema_markup($schema_id, $schema_post_id, $all_post_meta);
                                                                                                                                                                                                   
                         }
                            
                         if( 'VideoGame' === $schema_type){
                                                         
                            $input1 = saswp_video_game_schema_markup($schema_id, $schema_post_id, $all_post_meta);
                                                                                                                                            
                            $input1 = saswp_append_fetched_reviews($input1, $schema_post_id);
                                                                                                                                                                                                                               
                         }   
                        
                         if( 'qanda' === $schema_type){      
                             
                             $input1 = saswp_qanda_schema_markup($schema_id, $schema_post_id, $all_post_meta);                            
			 }   
                         
                         if( 'DataFeed' === $schema_type){
                                  
                             $input1 = saswp_data_feed_schema_markup($schema_id, $schema_post_id, $all_post_meta);                            
                                                            
                        }
                        
                         if( 'Event' === $schema_type){
                       
                                $input1 = saswp_event_schema_markup($schema_id, $schema_post_id, $all_post_meta);
                                                                                                                                                
                                if(!empty($aggregateRating)){
                                    $input1['aggregateRating'] = $aggregateRating;
                                }                                
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }                               
                                
                                $input1 = saswp_append_fetched_reviews($input1, $schema_post_id);
                                
                        }
                        
                         if( 'DiscussionForumPosting' === $schema_type){
                            
                            $input1 = saswp_dfp_schema_markup($schema_id, $schema_post_id, $all_post_meta);                            
                        }
                        
                         if( 'Course' === $schema_type){
                         
                                $input1 = saswp_course_schema_markup($schema_id, $schema_post_id, $all_post_meta);

                                if(!empty($aggregateRating)){
                                    
                                    $input1['aggregateRating'] = $aggregateRating;
                                    
                                }                                
                                if(!empty($extra_theme_review)){
                                    
                                   $input1 = array_merge($input1, $extra_theme_review);
                                   
                                }                               
                                                            
                        }
                                                
                         if( 'Blogposting' === $schema_type){
                    		
                                $input1 = saswp_blogposting_schema_markup($schema_id, $schema_post_id, $all_post_meta);                            
                        
                               if(!empty($aggregateRating)){
                                    $input1['aggregateRating'] = $aggregateRating;
                                }                                
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }
                     }
                     
                         if( 'AudioObject' === $schema_type){
                    		    
                               $input1 = saswp_audio_object_schema_markup($schema_id, $schema_post_id, $all_post_meta);                            
			
                               if(!empty($aggregateRating)){
                                    $input1['aggregateRating'] = $aggregateRating;
                                }                                
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }
                                $input1 = saswp_append_fetched_reviews($input1, $schema_post_id);
                        } 
                        
                         if( 'SoftwareApplication' === $schema_type){
                                                                            
                                $input1 = saswp_software_app_schema_markup($schema_id, $schema_post_id, $all_post_meta);
                                                        
                               if(!empty($aggregateRating)){
                                    $input1['aggregateRating'] = $aggregateRating;
                                }                                
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }
                                $input1 = saswp_append_fetched_reviews($input1, $schema_post_id);
                        }
			
			 if( 'WebPage' === $schema_type){
                             
				$input1 = saswp_webpage_schema_markup($schema_id, $schema_post_id, $all_post_meta);                            
                            
                                if(!empty($aggregateRating)){
                                    
                                    $input1['mainEntity']['aggregateRating'] = $aggregateRating;
                                    
                                }
                                
                                if(!empty($extra_theme_review)){
                                    
                                   $input1 = array_merge($input1, $extra_theme_review);
                                   
                                }
			}
			
			 if( 'Article' === $schema_type ){
                             
                                $input1 = saswp_article_schema_markup($schema_id, $schema_post_id, $all_post_meta);    
                               
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
                             
                                $input1 = saswp_tech_article_schema_markup($schema_id, $schema_post_id, $all_post_meta);                            
                                
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
                             
                                $input1 = saswp_recipe_schema_markup($schema_id, $schema_post_id, $all_post_meta);
                                                                                
                                if(!empty($aggregateRating)){
                                    $input1['aggregateRating'] = $aggregateRating;
                                }                                
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }
                                $input1 = saswp_append_fetched_reviews($input1, $schema_post_id);
			}
						
			 if( 'Product' === $schema_type){				

                                       $input1 = saswp_product_schema_markup($schema_id, $schema_post_id, $all_post_meta);
                                                                                                                                                                     
                                        if(!empty($aggregateRating)){
                                            $input1['aggregateRating'] = $aggregateRating;
                                        }                                        
                                        if(!empty($extra_theme_review)){
                                           $input1 = array_merge($input1, $extra_theme_review);
                                        }  
                                                                                                                 
                                        $input1 = saswp_append_fetched_reviews($input1, $schema_post_id);
			}
                        
                         if( 'NewsArticle' === $schema_type ){  
                             
                                $input1 = saswp_news_article_schema_markup($schema_id, $schema_post_id, $all_post_meta);                            
                                
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
				
                                   $input1 = saswp_video_object_schema_markup($schema_id, $schema_post_id, $all_post_meta);   

                                   if(!empty($aggregateRating)){
                                       $input1['aggregateRating'] = $aggregateRating;
                                   }                                                
                                   if(!empty($extra_theme_review)){
                                       $input1 = array_merge($input1, $extra_theme_review);
                                   }
                                   $input1 = saswp_append_fetched_reviews($input1, $schema_post_id);
			}
                                
                         if( 'ImageObject' === $schema_type){
				                                                
                                $input1 = saswp_image_object_schema_markup($schema_id, $schema_post_id, $all_post_meta);   

                                if(!empty($aggregateRating)){
                                    $input1['aggregateRating'] = $aggregateRating;
                                }                                                
                                if(!empty($extra_theme_review)){
                                    $input1 = array_merge($input1, $extra_theme_review);
                                }
                                $input1 = saswp_append_fetched_reviews($input1, $schema_post_id);
                                
                        }       
                        
                         if( 'Service' === $schema_type ){  
                             
                               $input1 = saswp_service_schema_markup($schema_id, $schema_post_id, $all_post_meta);    
                                                                              
                         }     
                         
                         if( 'Review' === $schema_type ){                              
                             
                                 $input1 = saswp_review_schema_markup($schema_id, $schema_post_id, $all_post_meta);    
                                                                                                                                                                                                                                                   
			}    
                                
                         if( 'local_business' === $schema_type){
                             
                                    $input1 = saswp_local_business_schema_markup($schema_id, $schema_post_id, $all_post_meta);
                                                                                                         
                                    if(!empty($aggregateRating)){
                                       $input1['aggregateRating'] = $aggregateRating;
                                    }                                    
                                    if(!empty($extra_theme_review)){
                                       $input1 = array_merge($input1, $extra_theme_review);
                                    }
                                    
                                    $input1 = saswp_append_fetched_reviews($input1, $schema_post_id);                                                              
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