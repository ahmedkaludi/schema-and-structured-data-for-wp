<?php
if (! defined('ABSPATH') ) exit;

function saswp_kb_schema_output() {
	global $sd_data;        	
	// social profile
	$sd_social_profile = array();

	$sd_facebook = array();
	if(isset($sd_data['sd_facebook']) && !empty($sd_data['sd_facebook'])){
		$sd_facebook[] = $sd_data['sd_facebook'];
		$sd_social_profile[] = $sd_facebook;
	}
	$sd_twitter = array();
	if(isset($sd_data['sd_twitter']) && !empty($sd_data['sd_twitter'])){
		$sd_twitter[] = $sd_data['sd_twitter'];
		$sd_social_profile[] = $sd_twitter;
	}

	$sd_google_plus = array();
	if(isset($sd_data['sd_google_plus']) && !empty($sd_data['sd_google_plus'])){
		$sd_google_plus[] = $sd_data['sd_google_plus'];	
		$sd_social_profile[] = $sd_google_plus;
	}
	$sd_instagram = array();
	if(isset($sd_data['sd_instagram']) && !empty($sd_data['sd_instagram'])){
		$sd_instagram[] = $sd_data['sd_instagram'];
		$sd_social_profile[] = $sd_instagram;
		}

	$sd_youtube = array();
	if(isset($sd_data['sd_youtube']) && !empty($sd_data['sd_youtube'])){
		$sd_youtube[] = $sd_data['sd_youtube'];
		$sd_social_profile[] = $sd_youtube;
	}

	$sd_linkedin = array();
	if(isset($sd_data['sd_linkedin']) && !empty($sd_data['sd_linkedin'])){
		$sd_linkedin[] = $sd_data['sd_linkedin'];
		$sd_social_profile[] = $sd_linkedin;
	}

	$sd_pinterest = array();
	if(isset($sd_data['sd_pinterest']) && !empty($sd_data['sd_pinterest'])){
		$sd_pinterest[] = $sd_data['sd_pinterest'];
		$sd_social_profile[] = $sd_pinterest;
	}

	$sd_soundcloud = array();
	if(isset($sd_data['sd_soundcloud']) && !empty($sd_data['sd_soundcloud'])){
		$sd_soundcloud[] = $sd_data['sd_soundcloud'];
		$sd_social_profile[] = $sd_soundcloud;
		}

	$sd_tumblr = array();
	if(isset($sd_data['sd_tumblr']) && !empty($sd_data['sd_tumblr'])){
		$sd_tumblr[] = $sd_data['sd_tumblr'];
		$sd_social_profile[] = $sd_tumblr;
		}

	$platform = array();
	foreach ($sd_social_profile as $key => $value) {
		$platform[] = $value; 
	}
	
	// Organization Schema 


	if ( $sd_data['saswp_kb_type']  ==  'Organization' ) {
		$logo = $sd_data['sd_logo']['url'];
		$contact_1 = $sd_data['saswp_contact_type'];
		$telephone_1 = $sd_data['saswp_kb_telephone'];
		$height = $sd_data['sd_logo']['height'];
		$width = $sd_data['sd_logo']['width'];

		if( '' ==  $logo && empty($logo) && isset($sd_data['sd_default_image'])){
			$logo = $sd_data['sd_default_image']['url'];
		}
		
		if( '' ==  $height && empty($height) && isset($sd_data['sd_default_image_height'])){
			$height = $sd_data['sd_default_image_height'];
		}
		
		if( '' ==  $width && empty($width) && isset($sd_data['sd_default_image_width'])){
			$width = $sd_data['sd_default_image_width'];
		}

		if( '' ==  $contact_1 && empty($contact_1) && isset($sd_data['saswp_contact_type'])){
			$contact_1 = $sd_data['saswp_contact_type'];
		}

		if( '' ==  $telephone_1 && empty($telephone_1) && isset($sd_data['saswp_kb_telephone'])){
			$telephone_1 = $sd_data['saswp_kb_telephone'];
		}

		// Contact Information
	 	$contact_info = array();
	 	$contact_info = array(
	 		'contactPoint' => array(
		      	'@type'    => 'ContactPoint',
		      	'contactType'  => $contact_1,
		      	'telephone'    => $telephone_1,
			)
 		);

		$input = array(
		'@context'		=>'http://schema.org',
		'@type'			=> $sd_data['saswp_kb_type'],
		'name'			=> $sd_data['sd_name'],
		'url'			=> $sd_data['sd_url'],
		'sameAs'		=> $platform,
		'logo' 			=> array(
					'@type'		=> 'ImageObject',
					'url'		=> $logo,
					'width'		=> $width,
					'height'	=> $height,
					), 		
		);

		if ( isset($sd_data['saswp_kb_contact_1'] ) && $sd_data['saswp_kb_contact_1'] ) {
			$input = array_merge($input, $contact_info);
		}
}				
		// Person

	if ( $sd_data['saswp_kb_type']  ==  'Person' ) {
		$image = $sd_data['sd-person-image']['url'];
		$height = $sd_data['sd-person-image']['height'];
		$width = $sd_data['sd-person-image']['width'];
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
			'@context'		=>'http://schema.org',
			'@type'			=> $sd_data['saswp_kb_type'],
			'name'			=> $sd_data['sd-person-name'],
			'url'			=> $sd_data['sd-person-url'],
			'Image' 			=> array(
					'@type'		=> 'ImageObject',
					'url'		=> $image,
					'width'		=> $width,
					'height'	=> $height,
					),
			'telephone'		=> $sd_data['sd-person-phone-number'],
			);
	}
	return json_encode($input);	             
}

function sd_is_blog() {
    return ( is_author() || is_category() || is_tag() || is_date() || is_home() || is_single() ) && 'post' == get_post_type();
}

function saswp_schema_output() {        
	global $sd_data;

	$Conditionals = saswp_get_all_schema_posts();         
        
	if(!$Conditionals){
		return ;
	}	        
        $all_schema_output = array();
        foreach($Conditionals as $schemaConditionals){
           
	$schema_options = $schemaConditionals['schema_options'];
	$schema_type = $schemaConditionals['schema_type'];         
        $schema_post_id = $schemaConditionals['post_id'];  
	$logo = $sd_data['sd_logo']['url'];        
		if( '' == $logo && empty($logo) && isset($sd_data['sd_default_image'])){
			$logo = $sd_data['sd_default_image']['url'];
		}
		$height = $sd_data['sd_logo']['height'];
		if( '' == $height && empty($height) && isset($sd_data['sd_default_image_height'])){
			$height = $sd_data['sd_default_image_height'];
		}
		$width = $sd_data['sd_logo']['width'];
		if( '' == $width && empty($width) && isset($sd_data['sd_default_image_width'])){
			$width = $sd_data['sd_default_image_width'];
		}
	if(is_singular()){
		// Generate author id
	   		$author_id = get_the_author_meta('ID');

		// Blogposting Schema 
			$image_id 	= get_post_thumbnail_id();
			$image_details 	= wp_get_attachment_image_src($image_id, 'full');			
			$author_details	= get_avatar_data($author_id);
			$date 		= get_the_date("Y-m-d\TH:i:s\Z");
			$modified_date 	= get_the_modified_date("Y-m-d\TH:i:s\Z");
			$aurthor_name 	= get_the_author();
                        
                        $saswp_review_details = esc_sql ( get_post_meta(get_the_ID(), 'saswp_review_details', true)); 
                        $aggregateRating = array();
                        $kkstar_aggregateRating = array();
                        $saswp_over_all_rating ='';
                        if(isset($saswp_review_details['saswp-review-item-over-all'])){
                        $saswp_over_all_rating = $saswp_review_details['saswp-review-item-over-all'];    
                        } 
                        $saswp_review_item_enable = 0;
                        if(isset($saswp_review_details['saswp-review-item-enable'])){
                         $saswp_review_item_enable =  $saswp_review_details['saswp-review-item-enable'];  
                        }  
                        $saswp_review_count = "1";
                       
                        
                        if($saswp_over_all_rating && $saswp_review_count && $saswp_review_item_enable ==1){
                           $aggregateRating =       array(
                                                            "@type"=> "AggregateRating",
                                                            "ratingValue" => $saswp_over_all_rating,
                                                            "reviewCount" => $saswp_review_count
                                                         ); 
                        }
                        
                        $kkstar_rating_data = saswp_extract_kk_star_ratings(get_the_ID());                        
                        if(!empty($kkstar_rating_data)){
                            $kkstar_aggregateRating =       array(
                                                            "@type"=> "AggregateRating",
                                                            "bestRating" => $kkstar_rating_data['best'],
                                                            "ratingCount" => $kkstar_rating_data['votes'],
                                                            "ratingValue" => $kkstar_rating_data['avg']
                                                         );     
                        }
                        $extra_theme_review = array();
                        $service_object = new saswp_output_service();
                        $extra_theme_review = $service_object->saswp_extra_theme_review_details(get_the_ID());
                       
                        
			if(is_page()){
				$schema_type = $schema_type; //$sd_data['sd_page_type'];
			}
			if(is_single()){
				$schema_type = $schema_type; //$sd_data['sd_post_type'];
			}
			if(is_front_page()){
				$schema_type = $schema_type; // $sd_data['sd_page_type'];
			}
			$input1 = array(
			'@context'			=> 'http://schema.org',
			'@type'				=> $schema_type ,

			'mainEntityOfPage'              => get_permalink(),
			'headline'			=> get_the_title(),
			'description'                   => get_the_excerpt(),
			'name'				=> get_the_title(),
			'url'				=> get_permalink(),
			'datePublished'                 => $date,
			'dateModified'                  => $modified_date,
			'author'			=> array(
					'@type' 	=> 'Person',
					'name'		=> $aurthor_name, ),
			'Publisher'			=> array(
				'@type'			=> 'Organization',
				'logo' 			=> array(
					'@type'		=> 'ImageObject',
					'url'		=> $logo,
					'width'		=> $width,
					'height'	=> $height,
					),
				'name'			=> $sd_data['sd_name'],
				),
			);
                                if(!empty($aggregateRating)){
                                    $input1['aggregateRating'] = $aggregateRating;
                                }
                                if(!empty($kkstar_aggregateRating)){
                                   $input1['aggregateRating'] = $kkstar_aggregateRating;  
                                }
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }                               
                        if(isset($sd_data['saswp_comments_schema']) && $sd_data['saswp_comments_schema'] ==1){
                                    $input1['comment'] = saswp_get_comments(get_the_ID());
                        }
			// For WebPage
			if( 'WebPage' === $schema_type){
                            
				if(empty($image_details[0]) || $image_details[0] === NULL ){
					$image_details[0] = $logo;
				}

				$input1 = array(
				'@context'			=> 'http://schema.org',
				'@type'				=> $schema_type ,
				'name'				=> get_the_title(),
				'url'				=> get_permalink(),
				'description'                   => get_the_excerpt(),
				'mainEntity'                    => array(
						'@type'			=> 'Article',
						'mainEntityOfPage'	=> get_permalink(),
						'image'			=> $image_details[0],
						'headline'		=> get_the_title(),
						'description'		=> get_the_excerpt(),
						'datePublished' 	=> $date,
						'dateModified'		=> $modified_date,
						'author'			=> array(
								'@type' 	=> 'Person',
								'name'		=> $aurthor_name, ),
						'Publisher'			=> array(
							'@type'			=> 'Organization',
							'logo' 			=> array(
								'@type'		=> 'ImageObject',
								'url'		=> $logo,
								'width'		=> $width,
								'height'	=> $height,
								),
							'name'			=> $sd_data['sd_name'],
						),
                                               
					),
					
				
				);
                                if(isset($sd_data['saswp_comments_schema']) && $sd_data['saswp_comments_schema'] ==1){
                                    $input1['comment'] = saswp_get_comments(get_the_ID());
                                }                                
                                if(!empty($aggregateRating)){
                                    $input1['mainEntity']['aggregateRating'] = $aggregateRating;
                                }
                                if(!empty($kkstar_aggregateRating)){
                                   $input1['mainEntity']['aggregateRating'] = $kkstar_aggregateRating;  
                                }
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }
			}

		// For Article
		
			if( 'Article' === $schema_type ){
                            
				$input1 = array(
					'@context'			=> 'http://schema.org',
					'@type'				=> 'Article',
					'mainEntityOfPage'              => get_permalink(),
					'image'				=> $image_details[0],
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
							'url'		=> $sd_data['sd_logo']['url'],
							'width'		=> $sd_data['sd_logo']['width'],
							'height'	=> $sd_data['sd_logo']['height'],
							),
						'name'			=> $sd_data['sd_name'],
					),
                                    
				);
                                if(isset($sd_data['saswp_comments_schema']) && $sd_data['saswp_comments_schema'] ==1){
                                    $input1['comment'] = saswp_get_comments(get_the_ID());
                                }
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }
			}

		       // Recipe
			if( 'Recipe' === $schema_type){
                            
				if(empty($image_details[0]) || $image_details[0] === NULL ){
					$image_details[0] = $sd_data['sd_logo']['url'];
				}
				$input1 = array(
				'@context'			=> 'http://schema.org',
				'@type'				=> $schema_type ,
				'url'				=> get_permalink(),
				'name'			        => get_the_title(),
				'datePublished'                 => $date,
				'dateModified'                  => $modified_date,
				'description'                   => get_the_excerpt(),
				'mainEntity'                    => array(
						'@type'				=> 'WebPage',
						'@id'				=> get_permalink(),
						'author'			=> array(
								'@type' 	=> 'Person',
								'name'		=> $aurthor_name,
								'Image'		=> array(
									'@type'			=> 'ImageObject',
									'url'			=> $author_details['url'],
									'height'		=> $author_details['height'],
									'width'			=> $author_details['width']
								),
							),
						'Publisher'			=> array(
							'@type'			=> 'Organization',
							'logo' 			=> array(
								'@type'		=> 'ImageObject',
								'url'		=> $sd_data['sd_logo']['url'],
								'width'		=> $sd_data['sd_logo']['width'],
								'height'	=> $sd_data['sd_logo']['height'],
								),
							'name'			=> $sd_data['sd_name'],
						),
                                                
                                    
					),                                        					
				
				);
                                if(isset($sd_data['saswp_comments_schema']) && $sd_data['saswp_comments_schema'] ==1){
                                    $input1['comment'] = saswp_get_comments(get_the_ID());
                                }                                 
                                if(!empty($aggregateRating)){
                                    $input1['aggregateRating'] = $aggregateRating;
                                }
                                if(!empty($kkstar_aggregateRating)){
                                   $input1['aggregateRating'] = $kkstar_aggregateRating;  
                                }
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }
			}
                       
                        if( 'qanda' === $schema_type){
                            $service_object = new saswp_output_service();
                            $input1  = $service_object->saswp_dw_question_answers_details(get_the_ID());                               
			}
			// Product
			
			if(  'Product' === $schema_type){
                            				
				$input1 = array(
				'@context'			=> 'http://schema.org',
				'@type'				=> $schema_type ,
				'url'				=> get_permalink(),
				'name'                          => get_the_title(),
				'description'                   => get_the_excerpt(),													
				);                                  
                                if(!empty($aggregateRating)){
                                    $input1['aggregateRating'] = $aggregateRating;
                                }
                                if(!empty($kkstar_aggregateRating)){
                                   $input1['aggregateRating'] = $kkstar_aggregateRating;  
                                }
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }
                                
                                $service = new saswp_output_service();
                                $product_details = $service->saswp_woocommerce_product_details(get_the_ID());  
                               
                                if((isset($sd_data['saswp-woocommerce']) && $sd_data['saswp-woocommerce'] ==1) && !empty($product_details)){
                                    
                                    $input1 = array(
                                    '@context'			=> 'http://schema.org',
                                    '@type'				=> 'Product',
                                    'url'				=> get_permalink(),
                                    'name'                              => $product_details['product_name'],
                                    'description'                       => $product_details['product_description'],
                                    'image'                             => $product_details['product_image'],    
                                    'offers'                            => array(
                                                                        '@type'	=> 'Offer',
                                                                        'availability'	=> $product_details['product_availability'],
                                                                        'price'	=> $product_details['product_price'],
                                                                        'priceCurrency'	=> $product_details['product_currency'],
                                                                             ),
                                        
				  ); 
                                     
                                  if(isset($product_details['product_review_count']) && isset($product_details['product_average_rating'])){
                                       $input1['aggregateRating'] =  array(
                                                                        '@type'         => 'AggregateRating',
                                                                        'ratingValue'	=> $product_details['product_average_rating'],
                                                                        'reviewCount'   => (int)$product_details['product_review_count'],       
                                       );
                                  }                                      
                                  if(!empty($product_details['product_reviews'])){
                                      $reviews = array();
                                      foreach ($product_details['product_reviews'] as $review){
                                          $reviews[] = array(
                                                                        '@type'	=> 'Review',
                                                                        'author'	=> $review['author'],
                                                                        'datePublished'	=> $review['datePublished'],
                                                                        'description'	=> $review['description'],  
                                                                        'reviewRating'  => array(
                                                                                '@type'	=> 'Rating',
                                                                                'bestRating'	=> '5',
                                                                                'ratingValue'	=> $review['reviewRating'],
                                                                                'worstRating'	=> '1',
                                                                        )  
                                          );
                                      }
                                      $input1['review'] =  $reviews;
                                  } 
                                }
			}
                        
                        if( 'NewsArticle' === $schema_type ){                              
                            $category_detail=get_the_category(get_the_ID());//$post->ID
                            $article_section = '';
                            foreach($category_detail as $cd){
                            $article_section =  $cd->cat_name;
                            }
                            $word_count = saswp_reading_time_and_word_count();
				$input1 = array(
					'@context'			=> 'http://schema.org',
					'@type'				=> $schema_type ,										
					'url'				=> get_permalink(),
					'headline'			=> get_the_title(),
                                        'mainEntityOfPage'	        => get_the_permalink(),            
					'datePublished'                 => $date,
					'dateModified'                  => $modified_date,
					'description'                   => get_the_excerpt(),
                                        'articleSection'                => $article_section,            
                                        'articleBody'                   => get_the_excerpt(),            
					'name'				=> get_the_title(), 					
					'thumbnailUrl'                  => $image_details[0],
                                        'wordCount'                     => $word_count['word_count'],
                                        'timeRequired'                  => $word_count['timerequired'],            
					'mainEntity'                    => array(
                                                                            '@type' => 'WebPage',
                                                                            '@id'   => get_permalink(),
						), 
					'author'			=> array(
							'@type' 			=> 'Person',
							'name'				=> $aurthor_name,
							'Image'				=> array(
							'@type'				=> 'ImageObject',
							'url'				=> $author_details['url'],
							'height'			=> $author_details['height'],
							'width'				=> $author_details['width']
										),
							),
					'Publisher'			=> array(
							'@type'				=> 'Organization',
							'logo' 				=> array(
							'@type'				=> 'ImageObject',
							'url'				=> $sd_data['sd_logo']['url'],
							'width'				=> $sd_data['sd_logo']['width'],
							'height'			=> $sd_data['sd_logo']['height'],
										),
							'name'				=> $sd_data['sd_name'],
							),                                                     
					);
                                
                                if(isset($sd_data['saswp_comments_schema']) && $sd_data['saswp_comments_schema'] ==1){
                                    $input1['comment'] = saswp_get_comments(get_the_ID());
                                }                
                                                                
                                if(!empty($aggregateRating)){
                                    $input1['aggregateRating'] = $aggregateRating;
                                }
                                if(!empty($kkstar_aggregateRating)){
                                   $input1['aggregateRating'] = $kkstar_aggregateRating;  
                                }
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }
				}
                        
                        
                        if( 'Service' === $schema_type ){  
                                 
                                $schema_data = saswp_get_schema_data($schema_post_id, 'saswp_service_schema_details');                                
                                
                                $area_served_str = $schema_data['saswp_service_schema_area_served'];
                                $area_served_arr = explode(',', $area_served_str);
                                                                
                                $service_offer_str = $schema_data['saswp_service_schema_service_offer'];
                                $service_offer_arr = explode(',', $service_offer_str);
                                
				$input1 = array(
					'@context'			=> 'http://schema.org',
					'@type'				=> $schema_type ,
                                        'name'				=> $schema_data['saswp_service_schema_name'], 
					'serviceType'                   => $schema_data['saswp_service_schema_type'],
					'provider'                      => array(
                                                                        '@type' => 'LocalBusiness',
                                                                        'name'  => $schema_data['saswp_service_schema_provider_name'],
                                                                        'image' => $schema_data['saswp_service_schema_image']['url'],
                                                                        '@id'   => get_permalink(),
                                                                        'address' => array(
                                                                            '@type' => 'PostalAddress',
                                                                            'addressLocality' => $schema_data['saswp_service_schema_locality'],
                                                                            'postalCode'      => $schema_data['saswp_service_schema_postal_code'],  
                                                                            'telephone'       => $schema_data['saswp_service_schema_telephone']
                                                                        ),
                                                                        'priceRange'         => $schema_data['saswp_service_schema_price_range'],                                                                        
                                                                        ),                                        										                                                                     
					'description'                   => $schema_data['saswp_service_schema_description'],
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
                                           '@type' => 'OfferCatalog',
                                            'name'  => $schema_data['saswp_service_schema_name'],
                                            'itemListElement' => $serviceOffer
                                       );
                                                                       
                                if(!empty($aggregateRating)){
                                    $input1['aggregateRating'] = $aggregateRating;
                                }
                                if(!empty($kkstar_aggregateRating)){
                                   $input1['aggregateRating'] = $kkstar_aggregateRating;  
                                }
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }
				}        

			// VideoObject
			if( 'VideoObject' === $schema_type){
                            
				if(empty($image_details[0]) || $image_details[0] === NULL ){
					$image_details[0] = $sd_data['sd_logo']['url'];
				}												
					
						$input1 = array(
						'@context'			=> 'http://schema.org',
						'@type'				=> $schema_type,
						'url'				=> get_permalink(),
						'headline'			=> get_the_title(),
						'datePublished'                 => $date,
						'dateModified'                  => $modified_date,
						'description'                   => get_the_excerpt(),
						'name'				=> get_the_title(),
						'uploadDate'                    => $date,
						'thumbnailUrl'                  => $image_details[0],
						'mainEntity'                    => array(
								'@type'				=> 'WebPage',
								'@id'				=> get_permalink(),
								), 
						'author'			=> array(
								'@type' 			=> 'Person',
								'name'				=> $aurthor_name,
								'Image'				=> array(
								'@type'				=> 'ImageObject',
								'url'				=> $author_details['url'],
								'height'			=> $author_details['height'],
								'width'				=> $author_details['width']
								),
							),
						'Publisher'			=> array(
								'@type'				=> 'Organization',
								'logo' 				=> array(
								'@type'				=> 'ImageObject',
								'url'				=> $sd_data['sd_logo']['url'],
								'width'				=> $sd_data['sd_logo']['width'],
								'height'			=> $sd_data['sd_logo']['height'],
										),
								'name'			=> $sd_data['sd_name'],
							),                                                    
                                                    
						);
                                                if(isset($sd_data['saswp_comments_schema']) && $sd_data['saswp_comments_schema'] ==1){
                                                 $input1['comment'] = saswp_get_comments(get_the_ID());
                                                }                                                
                                                if(!empty($aggregateRating)){
                                                       $input1['aggregateRating'] = $aggregateRating;
                                               }
                                               if(!empty($kkstar_aggregateRating)){
                                                 $input1['aggregateRating'] = $kkstar_aggregateRating;  
                                                }
                                                if(!empty($extra_theme_review)){
                                                  $input1 = array_merge($input1, $extra_theme_review);
                                                 }
					
				}
                        
                        if( 'local_business' === $schema_type){
                            
                                $business_type    = esc_sql ( get_post_meta($schema_post_id, 'saswp_business_type', true)  );                                 
                                $business_name    = esc_sql ( get_post_meta($schema_post_id, 'saswp_business_name', true)  );                                 
                                $business_details = esc_sql ( get_post_meta($schema_post_id, 'saswp_local_business_details', true)  );                                                                                                
				if(empty($image_details[0]) || $image_details[0] === NULL ){
					$image_details[0] = $logo;
				}
                                if(isset($business_details['local_business_logo'])){
                                    unset($image_details);  
                                    $image_details[0] = $business_details['local_business_logo']['url'];
                                    $image_details[1] = $business_details['local_business_logo']['width'];
                                    $image_details[2] = $business_details['local_business_logo']['height'];
                                }
                                if($business_name){
                                $local_business = $business_name;    
                                }else{
                                $local_business = $business_type;        
                                }                                
				$input1 = array(
				'@context'			=> 'http://schema.org',
				'@type'				=> $local_business ,
                                'name'                          => $business_details['local_business_name'],                                   
				'url'				=> get_permalink(),				
				'description'                   => get_the_excerpt(),				
				'address'                       => array(
                                                                "@type"          => "PostalAddress",
                                                                "streetAddress"  => $business_details['local_street_address'],
                                                                "addressLocality"=> $business_details['local_city'],
                                                                "addressRegion"  => $business_details['local_state'],
                                                                "postalCode"     => $business_details['local_postal_code'],                                                                                                                                  
                                                                 ),	
				'telephone'                   => $business_details['local_phone'],
                                'openingHoursSpecification'   => array(                                                                
                                                                 '@type' => 'OpeningHoursSpecification',
                                                                 'dayOfWeek'  => $business_details['saswp_dayofweek'],                                                                       
                                                                 'opens' => $business_details['local_opens_time'],
                                                                 'closes'=> $business_details['local_closes_time'],
                                                                ),
                                
				);                                       
                                    if(!empty($aggregateRating)){
                                    $input1['aggregateRating'] = $aggregateRating;
                                    }
                                    if(!empty($kkstar_aggregateRating)){
                                    $input1['aggregateRating'] = $kkstar_aggregateRating;  
                                    } 
                                    if(!empty($extra_theme_review)){
                                    $input1 = array_merge($input1, $extra_theme_review);
                                    }
                                    if(isset($business_details['local_price_range'])){
                                      $input1['priceRange'] = $business_details['local_price_range'];   
                                    }
                                    
                                    if(isset($business_details['local_accepts_reservations'])){
                                      $input1['acceptsReservations'] = $business_details['local_accepts_reservations'];   
                                    }
                                    
                                    if(isset($business_details['local_serves_cuisine'])){
                                      $input1['servesCuisine'] = $business_details['local_serves_cuisine'];   
                                    }
                                    
                                    if(isset($business_details['local_menu'])){
                                      $input1['menu'] = $business_details['local_menu'];   
                                    }                                                          
			}
                                
		//Check for Featured Image
                         if( !empty($input1)){
                        
                            if( is_array($image_details) ){
                                    if(isset($image_details[1]) ){
                                                    $width = $image_details[1];	
                                            }
                                            if(isset($image_details[2])){
                                                    $height = $image_details[2];
                                            }
                            $input2  = array(
                                                    'image'		=>array(
                                                                            '@type'		=>'ImageObject',
                                                                            'url'		=>$image_details[0],
                                                                            'width'		=>$width,
                                                                            'height'	=>$height,
                                                                            ),
                                    );
                            $input = array_merge($input1,$input2);
                         }
                            else{			
				$input2  = array(
				                'image'		=>array(
									'@type'		=>'ImageObject',
									'url'		=> $sd_data['sd_logo']['url'],
                                	'width'		=> $sd_data['sd_logo']['width'],
                                	'height'	=> $sd_data['sd_logo']['height'],
                               		 ),
				);
				$input = array_merge($input1,$input2);
		}
                
                        }
                
		if(isset($schema_options['notAccessibleForFree'])==1){

			add_filter( 'amp_post_template_data', 'saswp_structure_data_access_scripts');			
			$paywall_class_name = $schema_options['paywall_class_name'];
			$isAccessibleForFree = isset($schema_options['isAccessibleForFree'])? $schema_options['isAccessibleForFree']: False;

			if($paywall_class_name!=""){
				if(strpos($paywall_class_name, ".")==-1){
					$paywall_class_name = ".".$paywall_class_name;
				}
				$paywallData = array("isAccessibleForFree"=> $isAccessibleForFree,
									  "hasPart"=>array(
										    "@type"=> "WebPageElement",
										    "isAccessibleForFree"=> $isAccessibleForFree,
										    "cssSelector" => $paywall_class_name
										    )
										);
				$input = array_merge($input,$paywallData);
			}
		}  
                
                if(!empty($input)){
                $all_schema_output[] = $input;		                    
                }                
	}
        }              
        return $all_schema_output;	
}

function saswp_post_specific_schema_output() {
	global $post;
        global $sd_data;   
        $all_schemas = get_posts(
                    array(
                            'post_type' 	 => 'saswp',
                            'posts_per_page' => -1,   
                            'post_status' => 'publish',
                    )
                 );            
        $all_schema_output = array();
        foreach($all_schemas as $schema){
        $schema_id = $schema->ID;   	
	$schema_type = esc_sql ( get_post_meta($schema_id, 'schema_type', true)  );        
        $schema_post_id = $post->ID;  
	$all_post_meta = esc_sql ( get_post_meta($schema_post_id, $key='', true)  );     
	
	if(is_singular()){
		
                        $saswp_review_details = esc_sql ( get_post_meta(get_the_ID(), 'saswp_review_details', true)); 
                        $aggregateRating = array();
                         
                        if(isset($saswp_review_details['saswp-review-item-over-all'])){
                        $saswp_over_all_rating = $saswp_review_details['saswp-review-item-over-all'];    
                        }                
                        $saswp_review_item_enable = 0;
                        if(isset($saswp_review_details['saswp-review-item-enable'])){
                         $saswp_review_item_enable =  $saswp_review_details['saswp-review-item-enable'];  
                        } 
                        $saswp_review_count = "1";                            
                        
                        if($saswp_over_all_rating && $saswp_review_count && $saswp_review_item_enable ==1){
                           $aggregateRating =       array(
                                                            "@type"=> "AggregateRating",
                                                            "ratingValue" => $saswp_over_all_rating,
                                                            "reviewCount" => $saswp_review_count
                                                         ); 
                        }
                        $kkstar_rating_data = saswp_extract_kk_star_ratings(get_the_ID());                       
                        if(!empty($kkstar_rating_data)){
                            $kkstar_aggregateRating =       array(
                                                            "@type"=> "AggregateRating",
                                                            "bestRating" => $kkstar_rating_data['best'],
                                                            "ratingCount" => $kkstar_rating_data['votes'],
                                                            "ratingValue" => $kkstar_rating_data['avg']
                                                         );     
                        }
                        $extra_theme_review = array();
                        $service_object = new saswp_output_service();
                        $extra_theme_review = $service_object->saswp_extra_theme_review_details(get_the_ID());
            
                        
                        
                        if( 'qanda' === $schema_type){           
                           // print_r($all_post_meta['saswp_qa_question_title_'.$schema_id][0]);die;
                            if(trim($all_post_meta['saswp_qa_question_title_'.$schema_id][0]) ==''){
                                $service_object = new saswp_output_service();
                                $input1  = $service_object->saswp_dw_question_answers_details(get_the_ID());                               
                            }else{
                                $input1 = array(
                                    '@context'		  => 'http://schema.org',
                                    '@type'		  => 'QAPage' ,
                                    'mainEntity'         => array(
                                            '@type'		  => 'Question' ,
                                            'name'		  => $all_post_meta['saswp_qa_question_title_'.$schema_id][0],
                                            'text'		  => $all_post_meta['saswp_qa_question_description_'.$schema_id][0],
                                            'upvoteCount'         => $all_post_meta['saswp_qa_upvote_count_'.$schema_id][0] ,
                                            'dateCreated'         => $all_post_meta['saswp_qa_date_created_'.$schema_id][0],
                                            'author'         => array('@type' => 'Person','name' =>$all_post_meta['saswp_qa_question_author_name_'.$schema_id][0]) ,
                                            'answerCount'         => 2 ,
                                            'acceptedAnswer'         => array(
                                                        '@type' => 'Answer',
                                                        'upvoteCount' => $all_post_meta['saswp_qa_accepted_answer_upvote_count_'.$schema_id][0],
                                                        'url' => $all_post_meta['saswp_qa_accepted_answer_url_'.$schema_id][0],
                                                        'text' => $all_post_meta['saswp_qa_accepted_answer_text_'.$schema_id][0],
                                                        'dateCreated' => $all_post_meta['saswp_qa_accepted_answer_date_created_'.$schema_id][0],
                                                        'author' => array('@type' => 'Person', 'name' => $all_post_meta['saswp_qa_accepted_author_name_'.$schema_id][0]),
                                            ) ,
                                            'suggestedAnswer'         => array(
                                                        '@type' => 'Answer',
                                                        'upvoteCount' => $all_post_meta['saswp_qa_suggested_answer_upvote_count_'.$schema_id][0],
                                                        'url' => $all_post_meta['saswp_qa_suggested_answer_url_'.$schema_id][0],
                                                        'text' => $all_post_meta['saswp_qa_suggested_answer_text_'.$schema_id][0],
                                                        'dateCreated' => $all_post_meta['saswp_qa_suggested_answer_date_created_'.$schema_id][0],
                                                        'author' => array('@type' => 'Person', 'name' => $all_post_meta['saswp_qa_suggested_author_name_'.$schema_id][0]),
                                            ) ,                                            
                                    )
                                );
                            }                                
			}                            
                         if( 'Blogposting' === $schema_type){
                    // Blogposting Schema 									
			$input1 = array(
			'@context'			=> 'http://schema.org',
			'@type'				=> $schema_type ,

			'mainEntityOfPage'              => $all_post_meta['saswp_blogposting_main_entity_of_page_'.$schema_id][0],
			'headline'			=> $all_post_meta['saswp_blogposting_headline_'.$schema_id][0],
			'description'                   => $all_post_meta['saswp_blogposting_description'.$schema_id][0],
			'name'				=> $all_post_meta['saswp_blogposting_name_'.$schema_id][0],
			'url'				=> $all_post_meta['saswp_blogposting_url_'.$schema_id][0],
			'datePublished'                 => $all_post_meta['saswp_blogposting_date_published_'.$schema_id][0],
			'dateModified'                  => $all_post_meta['saswp_blogposting_date_modified_'.$schema_id][0],
			'author'			=> array(
					'@type' 	=> 'Person',
					'name'		=> $all_post_meta['saswp_blogposting_author_name_'.$schema_id][0], ),
			'Publisher'			=> array(
				'@type'			=> 'Organization',
				'logo' 			=> array(
					'@type'		=> 'ImageObject',
					'url'		=> $all_post_meta['saswp_blogposting_organization_logo_'.$schema_id][0],
					'width'		=> $all_post_meta['saswp_blogposting_organization_logo_'.$schema_id.'_width'][0],
					'height'	=> $all_post_meta['saswp_blogposting_organization_logo_'.$schema_id.'_height'][0],
					),
				'name'			=> $all_post_meta['saswp_blogposting_organization_name_'.$schema_id][0],
				),
			);
                               if(!empty($aggregateRating)){
                                    $input1['aggregateRating'] = $aggregateRating;
                                }
                                if(!empty($kkstar_aggregateRating)){
                                   $input1['aggregateRating'] = $kkstar_aggregateRating;  
                                }
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }
                     }
			
			 if( 'WebPage' === $schema_type){
				
				$input1 = array(
				'@context'			=> 'http://schema.org',
				'@type'				=> $schema_type ,
				'name'				=> $all_post_meta['saswp_webpage_name_'.$schema_id][0],
				'url'				=> $all_post_meta['saswp_webpage_url_'.$schema_id][0],
				'description'                   => $all_post_meta['saswp_webpage_description_'.$schema_id][0],
				'mainEntity'                    => array(
						'@type'			=> 'Article',
						'mainEntityOfPage'	=> $all_post_meta['saswp_webpage_main_entity_of_page_'.$schema_id][0],
						'image'			=> $all_post_meta['saswp_webpage_image_'.$schema_id][0],
						'headline'		=> $all_post_meta['saswp_webpage_headline_'.$schema_id][0],
						'description'		=> $all_post_meta['saswp_webpage_description_'.$schema_id][0],
						'datePublished' 	=> $all_post_meta['saswp_webpage_date_published_'.$schema_id][0],
						'dateModified'		=> $all_post_meta['saswp_webpage_date_modified_'.$schema_id][0],
						'author'			=> array(
								'@type' 	=> 'Person',
								'name'		=> $all_post_meta['saswp_webpage_author_name_'.$schema_id][0], ),
						'Publisher'			=> array(
							'@type'			=> 'Organization',
							'logo' 			=> array(
								'@type'		=> 'ImageObject',
								'url'		=> $all_post_meta['saswp_webpage_organization_logo_'.$schema_id][0],
								'width'		=> $all_post_meta['saswp_webpage_organization_logo_'.$schema_id.'_width'][0],
								'height'	=> $all_post_meta['saswp_webpage_organization_logo_'.$schema_id.'_height'][0],
								),
							'name'			=> $all_post_meta['saswp_webpage_organization_name_'.$schema_id][0],
						),
					),
					
				
				);
                                if(!empty($aggregateRating)){
                                    $input1['mainEntity']['aggregateRating'] = $aggregateRating;
                                }
                                if(!empty($kkstar_aggregateRating)){
                                   $input1['mainEntity']['aggregateRating'] = $kkstar_aggregateRating;  
                                }
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }
			}
			
			 if( 'Article' === $schema_type ){
				$input1 = array(
					'@context'			=> 'http://schema.org',
					'@type'				=> 'Article',
					'mainEntityOfPage'              => $all_post_meta['saswp_article_main_entity_of_page_'.$schema_id][0],
					'image'				=> $all_post_meta['saswp_article_image_'.$schema_id][0],
					'headline'			=> $all_post_meta['saswp_article_headline_'.$schema_id][0],
					'description'                   => $all_post_meta['saswp_article_description_'.$schema_id][0],
					'datePublished'                 => $all_post_meta['saswp_article_date_published_'.$schema_id][0],
					'dateModified'                  => $all_post_meta['saswp_article_date_modified_'.$schema_id][0],
					'author'			=> array(
							'@type' 	=> 'Person',
							'name'		=> $all_post_meta['saswp_article_author_name_'.$schema_id][0] 
                                                         ),
					'Publisher'			=> array(
						'@type'			=> 'Organization',
						'logo' 			=> array(
							'@type'		=> 'ImageObject',
							'url'		=> $all_post_meta['saswp_article_organization_logo_'.$schema_id][0],
							'width'		=> $all_post_meta['saswp_article_organization_logo_'.$schema_id.'_width'][0],
							'height'	=> $all_post_meta['saswp_article_organization_logo_'.$schema_id.'_height'][0],
							),
						'name'			=> $all_post_meta['saswp_article_organization_name_'.$schema_id][0],
					),
                                    
				);
                                if(!empty($kkstar_aggregateRating)){
                                   $input1['aggregateRating'] = $kkstar_aggregateRating;  
                                }
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }
			}
	
			 if( 'Recipe' === $schema_type){
				
                                $ingredient = array();
                                $instruction = array();
                                
                                if(isset($all_post_meta['saswp_recipe_ingredient_'.$schema_id])){
                                    $explod = explode(';', $all_post_meta['saswp_recipe_ingredient_'.$schema_id][0]);                                    
                                    foreach ($explod as $val){
                                      $ingredient[] = $val;  
                                    }                                   
                                }
                                
                                if(isset($all_post_meta['saswp_recipe_instructions_'.$schema_id])){
                                    $explod = explode(';', $all_post_meta['saswp_recipe_instructions_'.$schema_id][0]);                                    
                                    foreach ($explod as $val){
                                      $instruction[] = array(
                                                                 '@type'  => "HowToStep",
                                                                 'text'  => $val,                                                                                                                            
                                                                 );  
                                    }                                   
                                }
                                                             
				$input1 = array(
				'@context'			=> 'http://schema.org',
				'@type'				=> $schema_type ,
				'url'				=> $all_post_meta['saswp_recipe_url_'.$schema_id][0],
				'name'			        => $all_post_meta['saswp_recipe_name_'.$schema_id][0],
                                'author'			=> array(
								'@type' 	=> 'Person',
								'name'		=> $all_post_meta['saswp_recipe_author_name_'.$schema_id][0],
								'Image'		=> array(
									'@type'			=> 'ImageObject',
									'url'			=> $all_post_meta['saswp_recipe_author_image_'.$schema_id][0],
									'height'		=> $all_post_meta['saswp_recipe_author_image_'.$schema_id.'_height'][0],
									'width'			=> $all_post_meta['saswp_recipe_author_image_'.$schema_id.'_width'][0]
								),
							),
                                                                        
                                    
                                'prepTime'                       => $all_post_meta['saswp_recipe_preptime_'.$schema_id][0],  
                                'cookTime'                       => $all_post_meta['saswp_recipe_cooktime_'.$schema_id][0],  
                                'totalTime'                      => $all_post_meta['saswp_recipe_totaltime_'.$schema_id][0],  
                                'keywords'                       => $all_post_meta['saswp_recipe_keywords_'.$schema_id][0],  
                                'recipeYield'                    => $all_post_meta['saswp_recipe_recipeyield_'.$schema_id][0],  
                                'recipeCategory'                 => $all_post_meta['saswp_recipe_category_'.$schema_id][0],
                                'recipeCuisine'                  => $all_post_meta['saswp_recipe_cuisine_'.$schema_id][0],  
                                'nutrition'                      => array(
                                                                 '@type'  => "NutritionInformation",
                                                                 'calories'  => $all_post_meta['saswp_recipe_nutrition_'.$schema_id][0],                                                                 
                                                                 ), 
                                'recipeIngredient'               => $ingredient, 
                                'recipeInstructions'             => $instruction,  
                                'video'                          => array(
                                                                        'name'  => $all_post_meta['saswp_recipe_video_name_'.$schema_id][0],
                                                                        'description'  => $all_post_meta['saswp_recipe_video_description_'.$schema_id][0],
                                                                        'thumbnailUrl'  => $all_post_meta['saswp_recipe_video_thumbnailurl_'.$schema_id][0],  
                                                                        'contentUrl'  => $all_post_meta['saswp_recipe_video_contenturl_'.$schema_id][0],  
                                                                        'embedUrl'  => $all_post_meta['saswp_recipe_video_embedurl_'.$schema_id][0],  
                                                                        'uploadDate'  => $all_post_meta['saswp_recipe_video_upload_date_'.$schema_id][0],
                                                                        'duration'  => $all_post_meta['saswp_recipe_video_duration_'.$schema_id][0],                                                                 
                                                                 ),                                                                                                             
                                    
				'datePublished'                 => $all_post_meta['saswp_recipe_date_published_'.$schema_id][0],
				'dateModified'                  => $all_post_meta['saswp_recipe_date_modified_'.$schema_id][0],
				'description'                   => $all_post_meta['saswp_recipe_description_'.$schema_id][0],
				'mainEntity'                    => array(
						'@type'				=> 'WebPage',
						'@id'				=> $all_post_meta['saswp_recipe_main_entity_'.$schema_id][0],						
						'Publisher'			=> array(
							'@type'			=> 'Organization',
							'logo' 			=> array(
								'@type'		=> 'ImageObject',
								'url'		=> $all_post_meta['saswp_recipe_organization_logo_'.$schema_id][0],
								'width'		=> $all_post_meta['saswp_recipe_organization_logo_'.$schema_id.'_width'][0],
								'height'	=> $all_post_meta['saswp_recipe_organization_logo_'.$schema_id.'_height'][0],
								),
							'name'			=> $all_post_meta['saswp_recipe_organization_name_'.$schema_id][0],
						),
					),
					
				
				);
                                if(!empty($aggregateRating)){
                                    $input1['aggregateRating'] = $aggregateRating;
                                }
                                if(!empty($kkstar_aggregateRating)){
                                   $input1['aggregateRating'] = $kkstar_aggregateRating;  
                                }
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }
			}
						
			 if( 'Product' === $schema_type){
				
				$input1 = array(
				'@context'			=> 'http://schema.org',
				'@type'				=> $schema_type ,
				'url'				=> $all_post_meta['saswp_product_url_'.$schema_id][0],
				'name'                          => $all_post_meta['saswp_product_name_'.$schema_id][0],
				'description'                   => $all_post_meta['saswp_product_description_'.$schema_id][0],									
				
				);
                                if(!empty($aggregateRating)){
                                    $input1['aggregateRating'] = $aggregateRating;
                                }
                                if(!empty($kkstar_aggregateRating)){
                                   $input1['aggregateRating'] = $kkstar_aggregateRating;  
                                }
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }
                                $service = new saswp_output_service();
                                $product_details = $service->saswp_woocommerce_product_details($post->ID);  
                               
                                if((isset($sd_data['saswp-woocommerce']) && $sd_data['saswp-woocommerce'] ==1) && !empty($product_details)){                                    
                                    $input1 = array(
                                    '@context'			        => 'http://schema.org',
                                    '@type'				=> 'Product',
                                    'url'				=> $all_post_meta['saswp_product_url_'.$schema_id][0],
                                    'name'                              => $all_post_meta['saswp_product_name_'.$schema_id][0],
                                    'description'                       => $all_post_meta['saswp_product_description_'.$schema_id][0],
                                    'image'                             => $all_post_meta['saswp_product_image_'.$schema_id][0],    
                                    'offers'                            => array(
                                                                        '@type'	=> 'Offer',
                                                                        'availability'	=> $all_post_meta['saswp_product_availability_'.$schema_id][0],
                                                                        'price'	=> $all_post_meta['saswp_product_price_'.$schema_id][0],
                                                                        'priceCurrency'	=> $all_post_meta['saswp_product_currency_'.$schema_id][0],
                                                                             ),
                                        
				  ); 
                                     
                                  if(isset($product_details['product_review_count']) && isset($product_details['product_average_rating'])){
                                       $input1['aggregateRating'] =  array(
                                                                        '@type'         => 'AggregateRating',
                                                                        'ratingValue'	=> $product_details['product_average_rating'],
                                                                        'reviewCount'   => (int)$product_details['product_review_count'],       
                                       );
                                  }                                      
                                  if(!empty($product_details['product_reviews'])){
                                      $reviews = array();
                                      foreach ($product_details['product_reviews'] as $review){
                                          $reviews[] = array(
                                                                        '@type'	=> 'Review',
                                                                        'author'	=> $review['author'],
                                                                        'datePublished'	=> $review['datePublished'],
                                                                        'description'	=> $review['description'],  
                                                                        'reviewRating'  => array(
                                                                                '@type'	=> 'Rating',
                                                                                'bestRating'	=> '5',
                                                                                'ratingValue'	=> $review['reviewRating'],
                                                                                'worstRating'	=> '1',
                                                                        )  
                                          );
                                      }
                                      $input1['review'] =  $reviews;
                                  } 
                                }
			}
                        
                         if( 'NewsArticle' === $schema_type ){  
                             
				        $input1 = array(
					'@context'			=> 'http://schema.org',
					'@type'				=> $schema_type ,					
					'mainEntityOfPage'              => $all_post_meta['saswp_newsarticle_main_entity_of_page_'.$schema_id][0],
					'url'				=> $all_post_meta['saswp_newsarticle_URL_'.$schema_id][0],
					'headline'			=> $all_post_meta['saswp_newsarticle_headline_'.$schema_id][0],
					'datePublished'                 => $all_post_meta['saswp_newsarticle_date_published_'.$schema_id][0],
					'dateModified'                  => $all_post_meta['saswp_newsarticle_date_modified_'.$schema_id][0],
					'description'                   => $all_post_meta['saswp_newsarticle_description_'.$schema_id][0],
                                        'articleSection'                => $all_post_meta['saswp_newsarticle_section_'.$schema_id][0],
                                        'articleBody'                   => $all_post_meta['saswp_newsarticle_body_'.$schema_id][0],     
					'name'				=> $all_post_meta['saswp_newsarticle_name_'.$schema_id][0], 					
					'thumbnailUrl'                  => $all_post_meta['saswp_newsarticle_thumbnailurl_'.$schema_id][0],
                                        'wordCount'                     => $all_post_meta['saswp_newsarticle_word_count_'.$schema_id][0],
                                        'timeRequired'                  => $all_post_meta['saswp_newsarticle_timerequired_'.$schema_id][0],    
					'mainEntity'                    => array(
                                                                            '@type' => 'WebPage',
                                                                            '@id'   => $all_post_meta['saswp_newsarticle_main_entity_id_'.$schema_id][0],
						), 
					'author'			=> array(
							'@type' 			=> 'Person',
							'name'				=> $all_post_meta['saswp_newsarticle_author_name_'.$schema_id][0],
							'Image'				=> array(
							'@type'				=> 'ImageObject',
							'url'				=> $all_post_meta['saswp_newsarticle_author_image_'.$schema_id][0],
							'height'			=> $all_post_meta['saswp_newsarticle_author_image_'.$schema_id.'_height'][0],
							'width'				=> $all_post_meta['saswp_newsarticle_author_image_'.$schema_id.'_width'][0]
										),
							),
					'Publisher'			=> array(
							'@type'				=> 'Organization',
							'logo' 				=> array(
							'@type'				=> 'ImageObject',
							'url'				=> $all_post_meta['saswp_newsarticle_organization_logo_'.$schema_id][0],
							'width'				=> $all_post_meta['saswp_newsarticle_organization_logo_'.$schema_id.'_width'][0],
							'height'			=> $all_post_meta['saswp_newsarticle_organization_logo_'.$schema_id.'_height'][0],
										),
							'name'				=> $all_post_meta['saswp_newsarticle_organization_name_'.$schema_id][0],
							),
					);
                                                if(!empty($aggregateRating)){
                                                $input1['aggregateRating'] = $aggregateRating;
                                                }
                                                if(!empty($kkstar_aggregateRating)){
                                                   $input1['aggregateRating'] = $kkstar_aggregateRating;  
                                                }
				}
			
			 if( 'VideoObject' === $schema_type){
																					
						$input1 = array(
						'@context'			=> 'http://schema.org',
						'@type'				=> $schema_type,
						'url'				=> $all_post_meta['saswp_video_object_url_'.$schema_id][0],
						'headline'			=> $all_post_meta['saswp_video_object_headline_'.$schema_id][0],
						'datePublished'                 => $all_post_meta['saswp_video_object_date_published_'.$schema_id][0],
						'dateModified'                  => $all_post_meta['saswp_video_object_date_modified_'.$schema_id][0],
						'description'                   => $all_post_meta['saswp_video_object_description_'.$schema_id][0],
						'name'				=> $all_post_meta['saswp_video_object_name_'.$schema_id][0],
						'uploadDate'                    => $all_post_meta['saswp_video_object_upload_date_'.$schema_id][0],
						'thumbnailUrl'                  => $all_post_meta['saswp_video_object_thumbnail_url_'.$schema_id][0],
						'mainEntity'                    => array(
								'@type'				=> 'WebPage',
								'@id'				=> $all_post_meta['saswp_video_object_main_entity_id_'.$schema_id][0],
								), 
						'author'			=> array(
								'@type' 			=> 'Person',
								'name'				=> $all_post_meta['saswp_video_object_author_name_'.$schema_id][0],
								'Image'				=> array(
								'@type'				=> 'ImageObject',
								'url'				=> $all_post_meta['saswp_video_object_author_image_'.$schema_id][0],
								'height'			=> $all_post_meta['saswp_video_object_author_image_'.$schema_id.'_height'][0],
								'width'				=> $all_post_meta['saswp_video_object_author_image_'.$schema_id.'_width'][0]
								),
							),
						'Publisher'			=> array(
								'@type'				=> 'Organization',
								'logo' 				=> array(
								'@type'				=> 'ImageObject',
								'url'				=> $all_post_meta['saswp_video_object_organization_logo_'.$schema_id][0],
								'width'				=> $all_post_meta['saswp_video_object_organization_logo_'.$schema_id.'_width'][0],
								'height'			=> $all_post_meta['saswp_video_object_organization_logo_'.$schema_id.'_height'][0],
										),
								'name'                          => $all_post_meta['saswp_video_object_organization_name_'.$schema_id][0],
							),
						);
                                                if(!empty($aggregateRating)){
                                                    $input1['aggregateRating'] = $aggregateRating;
                                                }
                                                if(!empty($kkstar_aggregateRating)){
                                                   $input1['aggregateRating'] = $kkstar_aggregateRating;  
                                                } 
                                                if(!empty($extra_theme_review)){
                                                    $input1 = array_merge($input1, $extra_theme_review);
                                                }
					
				}
                        
                         if( 'Service' === $schema_type ){                                                   
                                $area_served_str = $all_post_meta['saswp_service_schema_area_served_'.$schema_id][0];
                                $area_served_arr = explode(',', $area_served_str);
                                                                
                                $service_offer_str = $all_post_meta['saswp_service_schema_service_offer_'.$schema_id][0];
                                $service_offer_arr = explode(',', $service_offer_str);
                                
				$input1 = array(
					'@context'			=> 'http://schema.org',
					'@type'				=> $schema_type ,
                                        'name'				=> $all_post_meta['saswp_service_schema_name_'.$schema_id][0], 
					'serviceType'                   => $all_post_meta['saswp_service_schema_type_'.$schema_id][0],
					'provider'                      => array(
                                                                        '@type' => 'LocalBusiness',
                                                                        'name'  => $all_post_meta['saswp_service_schema_provider_name_'.$schema_id][0],
                                                                        'image' => $all_post_meta['saswp_service_schema_image_'.$schema_id][0],
                                                                        '@id'   => get_permalink(),
                                                                        'address' => array(
                                                                            '@type' => 'PostalAddress',
                                                                            'addressLocality' => $all_post_meta['saswp_service_schema_locality_'.$schema_id][0],
                                                                            'postalCode'      => $all_post_meta['saswp_service_schema_postal_code_'.$schema_id][0],  
                                                                            'telephone'       => $all_post_meta['saswp_service_schema_telephone_'.$schema_id][0]
                                                                        ),
                                                                        'priceRange'         => $all_post_meta['saswp_service_schema_price_range_'.$schema_id][0],                                                                        
                                                                        ),                                        										                                                                     
					'description'                   => $all_post_meta['saswp_service_schema_description_'.$schema_id][0],
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
                                           '@type' => 'OfferCatalog',
                                            'name'  => $schema_data['saswp_service_schema_name'],
                                            'itemListElement' => $serviceOffer
                                       );
                                    										                                                    					                                                                                                                               
                                if(!empty($aggregateRating)){
                                    $input1['aggregateRating'] = $aggregateRating;
                                }
                                if(!empty($kkstar_aggregateRating)){
                                   $input1['aggregateRating'] = $kkstar_aggregateRating;  
                                }
                                if(!empty($extra_theme_review)){
                                   $input1 = array_merge($input1, $extra_theme_review);
                                }
                         }       
                                
                         if( 'local_business' === $schema_type){
                            
                                $business_sub_name ='';
                                $business_type = $all_post_meta['saswp_business_type_'.$schema_id][0]; 
                                $post_specific_obj = new saswp_post_specific();
                                $check_business_type = $post_specific_obj->saswp_get_sub_business_array($business_type);
                                if(!empty($check_business_type)){
                                 $business_sub_name = $all_post_meta['saswp_business_name_'.$schema_id][0];   
                                }                                
                                if($business_sub_name){
                                $local_business = $business_sub_name;    
                                }else{
                                $local_business = $business_type;        
                                }                                
				$input1 = array(
				'@context'			=> 'http://schema.org',
				'@type'				=> $local_business ,
                                'name'                          => $all_post_meta['local_business_name_'.$schema_id][0],                                   
				'url'				=> $all_post_meta['local_business_name_url_'.$schema_id][0],				
				'description'                   => $all_post_meta['local_business_description_'.$schema_id][0],				
				'address'                       => array(
                                                                "@type"          => "PostalAddress",
                                                                "streetAddress"  => $all_post_meta['local_street_address_'.$schema_id][0],
                                                                "addressLocality"=> $all_post_meta['local_city_'.$schema_id][0],
                                                                "addressRegion"  => $all_post_meta['local_state_'.$schema_id][0],
                                                                "postalCode"     => $all_post_meta['local_postal_code_'.$schema_id][0],                                                                                                                                  
                                                                 ),	
				'telephone'                   => $all_post_meta['local_phone_'.$schema_id][0],
                                'openingHoursSpecification'   => array(                                                                
                                                                 '@type' => 'OpeningHoursSpecification',
                                                                 'dayOfWeek'  => $all_post_meta['saswp_dayofweek_'.$schema_id][0],                                                                       
                                                                 'opens' => $all_post_meta['local_opens_time_'.$schema_id][0],
                                                                 'closes'=> $all_post_meta['local_closes_time_'.$schema_id][0],
                                                                ),                                                                                                     
				);
                                    
                                    if(!empty($aggregateRating)){
                                    $input1['aggregateRating'] = $aggregateRating;
                                    }
                                    if(!empty($kkstar_aggregateRating)){
                                       $input1['aggregateRating'] = $kkstar_aggregateRating;  
                                    }
                                    if(!empty($extra_theme_review)){
                                       $input1 = array_merge($input1, $extra_theme_review);
                                    }
                                    if(isset($all_post_meta['local_price_range_'.$schema_id][0])){
                                      $input1['priceRange'] = $all_post_meta['local_price_range_'.$schema_id][0];   
                                    }
                                    
                                    if(isset($all_post_meta['local_accepts_reservations_'.$schema_id][0])){
                                      $input1['acceptsReservations'] = $all_post_meta['local_price_accepts_reservations_'.$schema_id][0];   
                                    }
                                    
                                    if(isset($all_post_meta['local_serves_cuisine_'.$schema_id][0])){
                                      $input1['servesCuisine'] = $all_post_meta['local_price_serves_cuisine_'.$schema_id][0];   
                                    }
                                    
                                    if(isset($all_post_meta['local_menu_'.$schema_id][0])){
                                      $input1['menu'] = $all_post_meta['local_menu_'.$schema_id][0];   
                                    }                                                          
			}
                                		
                        $image_id 	= get_post_thumbnail_id();
			$image_details 	= wp_get_attachment_image_src($image_id, 'full');
			global $sd_data;
                        
                        if(!empty($input1)){
                        
			if( is_array($image_details) ){
				if(isset($image_details[1]) ){
						$width = $image_details[1];	
					}
					if(isset($image_details[2])){
						$height = $image_details[2];
					}
			$input2  = array(
				                'image'		=>array(
									'@type'		=>'ImageObject',
									'url'		=>$image_details[0],
									'width'		=>$width,
									'height'	=>$height,
									),
				);
			$input = array_merge($input1,$input2);
		     }else{			
				$input2  = array(
				                'image'		=>array(
									'@type'		=>'ImageObject',
									'url'		=> $sd_data['sd_logo']['url'],
                                	'width'		=> $sd_data['sd_logo']['width'],
                                	'height'	=> $sd_data['sd_logo']['height'],
                               		 ),
				);
				$input = array_merge($input1,$input2);
		}
                        
                        }               
                if(!empty($input)){
                   $all_schema_output[] = $input;		                 
                }                
	}
        }              
        return $all_schema_output;	
}

function saswp_structure_data_access_scripts($data){
	if ( empty( $data['amp_component_scripts']['amp-access'] ) ) {
		$data['amp_component_scripts']['amp-access'] = 'https://cdn.ampproject.org/v0/amp-access-0.1.js';
	}
	if ( empty( $data['amp_component_scripts']['amp-analytics'] ) ) {
		$data['amp_component_scripts']['amp-analytics'] = "https://cdn.ampproject.org/v0/amp-analytics-0.1.js";
	}
	if ( empty( $data['amp_component_scripts']['amp-mustache'] ) ) {
		$data['amp_component_scripts']['amp-mustache'] = "https://cdn.ampproject.org/v0/amp-mustache-0.1.js";
	}
	return $data;
}

function saswp_list_items_generator(){
		global $sd_data;
		$bc_titles = array();
		$bc_links = array();
                if(isset($sd_data['titles'])){		
			$bc_titles = $sd_data['titles'];
		}
		if(isset($sd_data['links'])){
			$bc_links = $sd_data['links'];
		}	
                
                $j=1;
                $i = 0;
        $breadcrumbslist = array();
        if(is_single()){    
            
			if(isset($bc_titles)){                           
				for($i=0;$i<sizeof($bc_titles);$i++){
					$breadcrumbslist[] = array(
								'@type'			=> 'ListItem',
								'position'		=> $j,
								'item'			=> array(
									'@id'		=> $bc_links[$i],
									'name'		=> $bc_titles[$i],
									),
							          );
		$j++;
		}}
               
}
        if(is_page()){

			for($i=0;$i<sizeof($bc_titles);$i++){
				$breadcrumbslist[] = array(
								'@type'			=> 'ListItem',
								'position'		=> $j,
								'item'			=> array(
									'@id'		=> $bc_links[$i],
									'name'		=> $bc_titles[$i],
									),
							);
		$j++;
		}

}
        if(is_archive()){

	for($i=0;$i<sizeof($bc_titles);$i++){
				$breadcrumbslist[] = array(
								        '@type'		=> 'ListItem',
								        'position'	=> $j,
								        'item'		=> array(
									'@id'		=> $bc_links[$i],
									'name'		=> $bc_titles[$i],
									),
							);
		$j++;
		}
}

       return $breadcrumbslist;
}

function saswp_schema_breadcrumb_output($sd_data){
	global $sd_data;        
        
	if(isset($sd_data['saswp_breadcrumb_schema']) && $sd_data['saswp_breadcrumb_schema'] == 1){
				       				
        if(is_single() || is_page() ||is_archive()){
        $bread_crumb_list =   saswp_list_items_generator();        
        if(!empty($bread_crumb_list)){            
           $input = array(
					'@context'			=> 'http://schema.org',
					'@type'				=> 'BreadcrumbList' ,
					'itemListElement'	        =>$bread_crumb_list,
			);    
         return json_encode($input);           	    
        }
               
        }         
	
	}
}

function saswp_kb_website_output(){
	global $sd_data;	
		$site_url = get_site_url();
		$site_name = get_bloginfo();
		$input = array(
			'@context'		=>'http://schema.org',
			'@type'			=> 'WebSite',
			'id'			=> '#website',
			'url'			=> $site_url,
			'name'			=> $site_name,
			 'potentialAction' => array(
				'@type'			=> 'SearchAction',
				'target'		=> $site_url.'/?s={search_term_string}',
				'query-input'	        => 'required name=search_term_string',
			 	)
			);
	
	return json_encode($input);        
}	
// For Archive 
function saswp_archive_output(){
	global $query_string, $sd_data;
	
	if(isset($sd_data['saswp_archive_schema']) && $sd_data['saswp_archive_schema'] == 1){
					
	if ( is_category() ) {
		$category_posts = array();
		$category_loop = new WP_Query( $query_string );
		if ( $category_loop->have_posts() ):
			while( $category_loop->have_posts() ): $category_loop->the_post();
				$image_id 		= get_post_thumbnail_id();
				$image_details 	= wp_get_attachment_image_src($image_id, 'full');
				$publisher_info = array(
				"type" => "Organization",
			        "name" => $sd_data['sd_name'],
                                "logo" => array(
                                    "@type" => "ImageObject",
                                    "name" => $sd_data['sd_name'],
                                    "width" => $sd_data['sd_logo']['width'],
                                    "height"=> $sd_data['sd_logo']['height'],
                                    "url"=> $sd_data['sd_logo']['url']
                                )                                        			        
				);
				$publisher_info['name'] = get_bloginfo('name');
				$publisher_info['id']	= get_the_permalink();
	            $category_posts[] =  array
	            (
					'@type' 			=> 'BlogPosting',
					'headline' 			=> get_the_title(),
					'url' 				=> get_the_permalink(),
					'datePublished'     => get_the_date('c'),
					'dateModified'      => get_the_modified_date('c'),
					'mainEntityOfPage'  => get_the_permalink(),
					'author' 			=> get_the_author(),
					'publisher'         => $publisher_info,
					'image' 			=> $image_details[0],
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
				'headline' 		=> $category_headline,
				'description' 	        => strip_tags(category_description()),
				'url'		 	=> $category_link,
				'sameAs' 		=> '',
				'hasPart' 		=> $category_posts
       		);
				return json_encode($input);	                                 
	endif;				
	}
	} 
}

// For Author 
function saswp_author_output()
{
	global $post, $sd_data;        
	if(isset($sd_data['saswp_archive_schema']) && $sd_data['saswp_archive_schema'] == 1){
	$post_id = $post->ID;
	if(is_author()){
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
		return json_encode($input);                 
	}
 }
}

// For About Page
function saswp_about_page_output()
{
	global $sd_data;        
	$image_id 		= get_post_thumbnail_id();              
	$image_details 	= wp_get_attachment_image_src($image_id, 'full');        
	if(isset($image_details['url'])){
				$image_url		= $image_details['url'];
			}
	$about_page = $sd_data['sd_about_page'];        
        
	if((isset($sd_data['sd_about_page'])) && $sd_data['sd_about_page'] == get_the_ID()){            
			$logo = $sd_data['sd_logo']['url'];	
			$height = $sd_data['sd_logo']['height'];
			$width = $sd_data['sd_logo']['width'];

				if( '' ==  $image_details && empty($image_details) && isset($sd_data['sd_default_image'])){
					$image_url = $sd_data['sd_default_image']['url'];
				}
				
				if( '' ==  $image_details && empty($image_details) && isset($sd_data['sd_default_image_height'])){
					$height = $sd_data['sd_default_image_height'];
				}
				
				if( '' ==  $image_details && empty($image_details) && isset($sd_data['sd_default_image_width'])){
					$width = $sd_data['sd_default_image_width'];
				}
			$input = array(
				"@context" 	 	=> "http://schema.org",
				"@type"			=> "AboutPage",
				"mainEntityOfPage" => array(
                                                 "@type" => "WebPage",
                                                 "@id"   => get_permalink(),
											    ),
				"url"			=> $about_page,
				"headline"		=> get_the_title(),
				"image"			=> array(
										"@type"		=> "ImageObject",
                                        "url"		=> $image_url,
                                        "width"		=> $width,
										"height"	=> $height,
							),
				'Publisher'		=> array(
                                         '@type'		=> 'Organization',
                                          'logo' 		=> array(
												'@type'		=> 'ImageObject',
												'url'		=> $logo,
												'width'		=> $width,
												'height'	=> $height,
												),
				'name'			=> $sd_data['sd_name'],
						),
				'description'		=> get_the_excerpt(),
			);
			
			return json_encode($input);                        
	}
	
}

// For Contact Page
function saswp_contact_page_output()
{
	global $sd_data;
	$image_id 		= get_post_thumbnail_id();
	$image_details 	        = wp_get_attachment_image_src($image_id, 'full');
	if(isset($image_details['url'])){
				$image_url		= $image_details['url'];
			}
	$contact_page = $sd_data['sd_contact_page'];
	if(isset($sd_data['sd_contact_page']) && $sd_data['sd_contact_page'] == get_the_ID()){

			$logo = $sd_data['sd_logo']['url'];	
			$height = $sd_data['sd_logo']['height'];
			$width = $sd_data['sd_logo']['width'];

				if( '' ==  $image_details && empty($image_details) && isset($sd_data['sd_default_image'])){
					$image_url = $sd_data['sd_default_image']['url'];
				}
				
				if( '' ==  $image_details && empty($image_details) && isset($sd_data['sd_default_image_height'])){
					$height = $sd_data['sd_default_image_height'];
				}
				
				if( '' ==  $image_details && empty($image_details) && isset($sd_data['sd_default_image_width'])){
					$width = $sd_data['sd_default_image_width'];
				}
			$input = array(
				"@context" 	    => "http://schema.org",
				"@type"		    => "ContactPage",
				"mainEntityOfPage"  => array(
							"@type" => "WebPage",
							"@id" 	=> get_permalink(),
							),
				"url"				=> $contact_page,
				"headline"			=> get_the_title(),
				"image"		    => array(
							"@type"		=> "ImageObject",
                            "url"		=> $image_url,
                            "width"		=> $width,
							"height"	=> $height,
							),
				'Publisher'	    => array(
				'@type'		    => 'Organization',
				                        'logo' => array(
							'@type'		=> 'ImageObject',
							'url'		=> $logo,
							'width'		=> $width,
							'height'	=> $height,
							),
				'name'		    => $sd_data['sd_name'],
						),
				'description'	    => get_the_excerpt(),
			);
			
			return json_encode($input);
                         
	}
	
}


function saswp_get_comments($post_id){
    
        $comment_count = get_comments_number( $post_id );
	if ( $comment_count < 1 ) {
		return array();
	}
        $comments = array();
        
        $count	= apply_filters( 'saswp_do_comments', '10'); // default = 10
        
        $post_comments = get_comments( array( 
                                            'post_id' => $post_id,
                                            'number' => $count, 
                                            'status' => 'approve',
                                            'type' => 'comment' 
                                        ) 
                                    );
        
        if ( count( $post_comments ) ) {
		foreach ( $post_comments as $comment ) {
			$comments[] = array (
					'@type' => 'Comment',
					'dateCreated' => $comment->comment_date,
					'description' => $comment->comment_content,
					'author' => array (
						'@type' => 'Person',
						'name' => $comment->comment_author,
						'url' => $comment->comment_author_url,
				),
			);
		}
		return apply_filters( 'saswp_filter_comments', $comments );
	}
        
}

function saswp_get_schema_data($schema_id, $schema_key){
    $details = array();
    if($schema_id && $schema_key){
     $details = esc_sql ( get_post_meta($schema_id, $schema_key, true));    
    }  
    return $details;
}

function saswp_extract_kk_star_ratings($id)
        {
            global $sd_data;    
            
            if(isset($sd_data['saswp-kk-star-raring']) && $sd_data['saswp-kk-star-raring'] ==1){
               
                $best = get_option('kksr_stars');
                $score = get_post_meta($id, '_kksr_ratings', true) ? ((int) get_post_meta($id, '_kksr_ratings', true)) : 0;
                $votes = get_post_meta($id, '_kksr_casts', true) ? ((int) get_post_meta($id, '_kksr_casts', true)) : 0;
                $avg = $score && $votes ? round((float)(($score/$votes)*($best/5)), 1) : 0;
                $per = $score && $votes ? round((float)((($score/$votes)/5)*100), 2) : 0;                
                if($votes>0){
                return compact('best', 'score', 'votes', 'avg', 'per');    
                }else{
                return array();    
                }
                
            }else{
                return array();
            }                        
       }
       
function saswp_reading_time_and_word_count() {

    // Predefined words-per-minute rate.
    $words_per_minute = 225;
    $words_per_second = $words_per_minute / 60;

    // Count the words in the content.
    $word_count      =0;
    $text            = trim( strip_tags( get_the_content() ) );
    $word_count      = substr_count( "$text ", ' ' );

    // How many seconds (total)?
    $seconds = floor( $word_count / $words_per_second );

    return array('word_count' => $word_count, 'timerequired' => $seconds);
}
