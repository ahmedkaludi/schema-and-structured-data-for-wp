<?php 
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

return array( 'schema_type_element' => array( 
                        'ItemList' => array(
                               'itemlist_item'       => 'itemlist_item',                                                     
                        ),
                        'CollectionPage' => array(
                               'collection_page_item'       => 'collection_page_item',                                                     
                        ),
                        'MusicComposition' => array(
                               'music_composer'       => 'music_composer',                                                     
                        ),                        
                        'Movie' => array(
                               'movie_actor'       => 'movie_actor',                                                     
                        ),
                        'Article' => array(
                               'article_items' => 'article_items',                                                
                        ),
						'ScholarlyArticle' => array(
							'scholarlyarticle_items' => 'scholarlyarticle_items',                                                
					    ),
                        'ImageObject' => array(
                               'image_object_exif_data' => 'image_object_exif_data',                                                
                        ),
                        'BlogPosting' => array(
                               'blogposting_items' => 'blogposting_items',                                                
                        ),
                        'NewsArticle' => array(
                                'newsarticle_items' => 'newsarticle_items',                                                
                                    ),
						'AnalysisNewsArticle' => array(
							'analysisnewsarticle_items' => 'analysisnewsarticle_items',                                                
								),
						'AskPublicNewsArticle' => array(
							'askpublicnewsarticle_items' => 'askpublicnewsarticle_items',                                                
								),
						'BackgroundNewsArticle' => array(
							'backgroundnewsarticle_items' => 'backgroundnewsarticle_items',                                                
								),
						'OpinionNewsArticle' => array(
							'opinionnewsarticle_items' => 'opinionnewsarticle_items',                                                
								),
						'ReportageNewsArticle' => array(
							'reportagenewsarticle_items' => 'reportagenewsarticle_items',                                                
								),
						'ReviewNewsArticle' => array(
							'reviewnewsarticle_items' => 'reviewnewsarticle_items',                                                
								),
                        'TechArticle' => array(
                               'tech_article_items' => 'tech_article_items',                                                
                        ),
                        'Product' => array(
                               'product_reviews' => 'product_reviews',
							   'product_pros' => 'product_pros',
							   'product_cons' => 'product_cons',                                                
                        ),   
                        'ProductGroup' => array(
                        	'product_group_has_varient'	=>	'product_group_has_varient',
                        ),
			   'SoftwareApplication' => array(
				'product_reviews' => 'product_reviews',
				'product_pros' => 'product_pros',
				'product_cons' => 'product_cons',
			   ),                     
                        'DataFeed' => array(
                               'feed_element' => 'feed_element',                                                
                        ),
                        'FAQ' => array(
                               'faq-question' => 'faq_question',                                                
                        ),
                        'Event' => array(
							   'event_schedule'=> 'event_schedule',
							   'performer'     => 'performer', 
							   'organizer'     => 'organizer',                                                                                                
						),
						'EducationalOccupationalProgram' => array(
							'eopidentifier'     => 'eopidentifier', 
							'eopoffer'          => 'eopoffer',                                                
					    ),
						'JobPosting' => array(
							'joblocation'     => 'joblocation'							                                             
					    ),
						'qanda' => array(
							'accepted_answer'  => 'accepted_answer',
							'suggested_answer' => 'suggested_answer' 							
					 	),
                        'HowTo' => array(
                               'how-to-supply' => 'howto_supply', 
                               'how-to-tool'   => 'howto_tool', 
                               'how-to-step'   => 'howto_step', 
						),
						'Recipe' => array(
							'recipe-instructions' => 'recipe_instructions'
					    ),
						'SpecialAnnouncement' => array(
							'announcement-location' => 'announcement_location', 														
					    ),
                        'MusicPlaylist' => array(
                               'music-playlist-track' => 'music_playlist_track',                                                               
                        ),
                        'MusicAlbum' => array(
                               'music-album-track' => 'music_album_track',                                                               
                        ),
                        'Apartment' => array(
                               'apartment-amenities' => 'apartment_amenities',
                               'additional-property' => 'additional_property',
                                                              
                        ),
                        'MedicalCondition' => array(                               
                               'mc-symptom'     => 'mc_symptom', 
                               'mc-risk_factor' => 'mc_risk_factor'
                        ),
                        'TVSeries' => array(
                               'tvseries-actor'  => 'tvseries_actor',
                               'tvseries-character'  => 'tvseries_character',
                               'tvseries-season' => 'tvseries_season', 
                        ),
                        'Trip' => array(
                               'trip-itinerary'  => 'trip_itinerary'
						),
						'BoatTrip' => array(
							'boat-trip-itinerary'  => 'boat_trip_itinerary'
					    ),
			   'TouristTrip' => array(
					'tourist-trip-itinerary'  => 'tourist_trip_itinerary'
			    ),
			    'Course' => array(
					'course-instance'  => 'course_instance'
			    ),
			    'VacationRental' => array(
					'vacation-rental-porperty-images'  => 'vacation_rental_property_images',
					'vacation-rental-bed-details'  	=> 'vacation_rental_bed_details',
					'vacation-rental-amenity-feature'  => 'vacation_rental_amenity_feature',
					'vacation-rental-review-rating'  	=> 'vacation_rental_review_rating'
			    ),
			    'LearningResource' => array(
					'learning-resource-educational-alignment'  => 'learning_resource_educational_alignment',
			    ),
			    'LiveBlogPosting' => array(
					'live-blog-update'  => 'live_blog_update',
			    ),
			    'MediaGallery' => array(
					'media-gallery-associated-media'  => 'media_gallery_associated_media',
			    ),
			    'ImageGallery' => array(
					'image-gallery-collections'  => 'image_gallery_collections',
			    ),
			    'Vehicle' => array(
					'vehicle-engine'  => 'vehicle_engine',
			    ),
			    'local_business' => array(
                               'available_service' => 'available_service',                                                
                          ),
			      'SportsTeam' => array(
	                    'sports-team-member-of' => 'sports_team_member_of',
	                    'sports-team-athlete'   => 'sports_team_athlete'	                    
	                  ),

						                                                                          
                    ),
    'meta_name' => array(
		'eopidentifier' => array(                    
			array(
				'label'     => esc_html__( 'Property ID', 'schema-and-structured-data-for-wp' ),
				'name'      => 'saswp_eopidentifier_property_id',
				'type'      => 'text',                        
			),
			array(
				'label'     => esc_html__( 'Value', 'schema-and-structured-data-for-wp' ),
				'name'      => 'saswp_eopidentifier_property_value',
				'type'      => 'text',                        
			) 
		),
		'eopoffer' => array(                    
			array(
				'label'     => esc_html__( 'Category', 'schema-and-structured-data-for-wp' ),
				'name'      => 'saswp_eopoffer_category',
				'type'      => 'text',                        
			),
			array(
				'label'     => esc_html__( 'Price', 'schema-and-structured-data-for-wp' ),
				'name'      => 'saswp_eopoffer_price',
				'type'      => 'text',                        
			),
			array(
				'label'     => esc_html__( 'Price Currency', 'schema-and-structured-data-for-wp' ),
				'name'      => 'saswp_eopoffer_price_currency',
				'type'      => 'text',                        
			)
			 
		),
                     'movie_actor' => array(                    
                        array(
                            'label'     => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
                            'name'      => 'saswp_movie_actor_name',
                            'type'      => 'text',                        
                        ),
                        array(
                            'label'     => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
                            'name'      => 'saswp_movie_actor_url',
                            'type'      => 'text',                        
                        ) 
                    ),
                    'music_composer' => array(                    
                    array(
			'label'     => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_music_composition_composer_name',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => esc_html__( 'URL', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_music_composition_composer_url',
			'type'      => 'text',                        
		    )    
                    ),
                    'article_items' => array(                    
                    array(
			'label'     => esc_html__( 'Item Name', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_article_items_name',
			'type'      => 'text',                        
		    )                     
                    ),
                    'image_object_exif_data' => array(                    
                    array(
			'label'     => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswpimage_object_exif_data_name',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => esc_html__( 'Value', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswpimage_object_exif_data_value',
			'type'      => 'text',                        
		    )    
                    ),
                    'newsarticle_items' => array(                    
                    array(
			'label'     => esc_html__( 'Item Name', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_newsarticle_items_name',
			'type'      => 'text',                        
		    )                     
                    ),
					'analysisnewsarticle_items' => array(                    
						array(
				'label'     => esc_html__( 'Item Name', 'schema-and-structured-data-for-wp' ),
				'name'      => 'saswp_analysisnewsarticle_items_name',
				'type'      => 'text',                        
				)                     
						),
					'askpublicnewsarticle_items' => array(                    
						array(
				'label'     => esc_html__( 'Item Name', 'schema-and-structured-data-for-wp' ),
				'name'      => 'saswp_askpublicnewsarticle_items_name',
				'type'      => 'text',                        
				)                     
						),
						'backgroundnewsarticle_items' => array(                    
							array(
					'label'     => esc_html__( 'Item Name', 'schema-and-structured-data-for-wp' ),
					'name'      => 'saswp_backgroundnewsarticle_items_name',
					'type'      => 'text',                        
					)                     
							),
						'opinionnewsarticle_items' => array(                    
							array(
					'label'     => esc_html__( 'Item Name', 'schema-and-structured-data-for-wp' ),
					'name'      => 'saswp_opinionnewsarticle_items_name',
					'type'      => 'text',                        
					)                     
							),
						'reportagenewsarticle_items' => array(                    
							array(
					'label'     => esc_html__( 'Item Name', 'schema-and-structured-data-for-wp' ),
					'name'      => 'saswp_reportagenewsarticle_items_name',
					'type'      => 'text',                        
					)                     
							),
					'reviewnewsarticle_items' => array(                    
						array(
				'label'     => esc_html__( 'Item Name', 'schema-and-structured-data-for-wp' ),
				'name'      => 'saswp_reviewnewsarticle_items_name',
				'type'      => 'text',                        
				)                     
						),
                    'tech_article_items' => array(                    
                    array(
			'label'     => esc_html__( 'Item Name', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_tech_article_items_name',
			'type'      => 'text',                        
		    )                     
                    ),
                    'blogposting_items' => array(                    
                    array(
			'label'     => esc_html__( 'Item Name', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_blogposting_items_name',
			'type'      => 'text',                        
		    )                     
                    ),
                    'product_reviews' => array(                    
                    array(
			'label'     => esc_html__( 'Item Name', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_product_reviews_reviewer_name',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => esc_html__( 'Rating', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_product_reviews_reviewer_rating',
			'type'      => 'number',                        
		    ),
                    array(
			'label'     => esc_html__( 'Text', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_product_reviews_text',
			'type'      => 'textarea',                        
		    ),
                    array(
			'label'     => esc_html__( 'Created Date', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_product_reviews_created_date',
			'type'      => 'text',                        
		    )    
                    ),
			'product_pros' => array(                    
				array(
					'label'     => esc_html__( 'Pros Text', 'schema-and-structured-data-for-wp' ),
					'name'      => 'saswp_product_pros_title',
					'type'      => 'text',                        
				),
			),
			'product_cons' => array(                    
				array(
					'label'     => esc_html__( 'Cons Text', 'schema-and-structured-data-for-wp' ),
					'name'      => 'saswp_product_cons_title',
					'type'      => 'text',                        
				),
			),                
			'product_group_has_varient' => array(
				array(
					'label'     => esc_html__( 'SKU', 'schema-and-structured-data-for-wp' ),
					'name'      => 'saswp_product_grp_sku',
					'type'      => 'text',                        
				),
				array(
					'label'     => esc_html__( 'GTIN14', 'schema-and-structured-data-for-wp' ),
					'name'      => 'saswp_product_grp_gtin14',
					'type'      => 'text',                        
				),
				array(
					'label'     => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
					'name'      => 'saswp_product_grp_img',
					'type'      => 'media',                        
				),
				array(
					'label'     => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
					'name'      => 'saswp_product_grp_name',
					'type'      => 'text',                        
				),
				array(
					'label'     => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
					'name'      => 'saswp_product_grp_description',
					'type'      => 'textarea',                        
				),
				array(
					'label'     => esc_html__( 'Offer URL', 'schema-and-structured-data-for-wp' ),
					'name'      => 'saswp_product_grp_offer_url',
					'type'      => 'text',                        
				),
				array(
					'label'     => esc_html__( 'Offer Currency', 'schema-and-structured-data-for-wp' ),
					'name'      => 'saswp_product_grp_offer_currency',
					'type'      => 'text',                        
				),
				array(
					'label'     => esc_html__( 'Offer Price', 'schema-and-structured-data-for-wp' ),
					'name'      => 'saswp_product_grp_offer_price',
					'type'      => 'number',                        
				),
				array(
	                            'label'     => esc_html__( 'Price Valid Until', 'schema-and-structured-data-for-wp' ),
	                            'name'      => 'saswp_product_grp_schema_priceValidUntil',
	                            'type'      => 'text',    
                       	),
				array(
					'label'     => esc_html__( 'Offer Item Condition', 'schema-and-structured-data-for-wp' ),
					'name'      => 'saswp_product_grp_offer_icondition',
					'type'      => 'select',                        
					'options'   => array(
						'NewCondition'		=> 'New Condition',
						'RefurbishedCondition'	=> 'Refurbished Condition',
						'UsedCondition'		=> 'Used Condition',
					),                        
				),
				array(
					'label'     => esc_html__( 'Offer Availability', 'schema-and-structured-data-for-wp' ),
					'name'      => 'saswp_product_grp_offer_avail',
					'type'      => 'select',                            
	                            'options'   => array(
	                                   'InStock'           		=> 'In Stock',
	                                   'BackOrder'           	=> 'Back Order',
	                                   'Discontinued'           	=> 'Discontinued',
	                                   'Discontinued'      		=> 'Discontinued',
	                                   'InStoreOnly'           	=> 'In Store Only',
	                                   'LimitedAvailability'     	=> 'Limited Availability',
	                                   'OnlineOnly'           	=> 'Online Only',
	                                   'OutOfStock'        		=> 'Out Of Stock',
	                                   'PreOrder'          		=> 'Pre Order', 
	                                   'PreSale'          		=> 'Pre Sale', 
	                                   'SoldOut'          		=> 'Sold Out', 
	                            ),                      
				),
			),                
                    'feed_element' => array(                    
                    array(
			'label'     => esc_html__( 'Date Created', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_feed_element_date_created',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => esc_html__( 'Feed Element Name', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_feed_element_name',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => esc_html__( 'Feed Element email', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_feed_element_email',
			'type'      => 'text',                        
		    )    
                    ),
                    'performer' => array(                    
            	        array(
							'label'     => esc_html__( 'Performer Type', 'schema-and-structured-data-for-wp' ),
							'name'      => 'saswp_event_performer_type',
							'type'      => 'select',
										'options'   => array(                                                                                              
												'Person'           => 'Person',
												'Organization'     => 'Organization',
												'MusicGroup'       => 'MusicGroup'
										)
							),
									array(
							'label'     => esc_html__( 'Performer Name', 'schema-and-structured-data-for-wp' ),
							'name'      => 'saswp_event_performer_name',
							'type'      => 'text',                        
							),
									array(
							'label'     => esc_html__( 'Performer URL', 'schema-and-structured-data-for-wp' ),
							'name'      => 'saswp_event_performer_url',
							'type'      => 'text',                        
							)                                                            
					),
					
					'organizer' => array(                    						
							array(
								'label'     => esc_html__( 'Organizer Name', 'schema-and-structured-data-for-wp' ),
								'name'      => 'saswp_event_organizer_name',
								'type'      => 'text',                        
							),
							array(
								'label'     => esc_html__( 'Organizer URL','schema-and-structured-data-for-wp' ),
								'name'      => 'saswp_event_organizer_url',
								'type'      => 'text',                        
							),
							array(
								'label'     => esc_html__( 'Organizer Phone', 'schema-and-structured-data-for-wp' ),
								'name'      => 'saswp_event_organizer_phone',
								'type'      => 'text',                        
							),
							array(
								'label'     => esc_html__( 'Organizer Email', 'schema-and-structured-data-for-wp' ),
								'name'      => 'saswp_event_organizer_email',
								'type'      => 'text',                        
							),							                                                            
						),
			'event_schedule'	=> array(
				array(
					'label'	=> esc_html__( 'Schedule Name', 'schema-and-structured-data-for-wp' ),
					'name'		=> 'saswp_event_schema_schedule_n',
					'type'		=> 'text',
				),
	                     array(
                            	'label' => esc_html__( 'Start Time', 'schema-and-structured-data-for-wp' ),
                            	'name'  => 'saswp_event_schema_schedule_st',
                            	'type'  => 'text',                                
	                     ),
	                     array(
	                     	'label' => esc_html__( 'End Time', 'schema-and-structured-data-for-wp' ),
	                            'name'  => 'saswp_event_schema_schedule_et',
	                            'type' => 'text',                                
	                     ),
				array(
					'label'	=> 'Schedule Repeat Frequency',
					'name'		=> 'saswp_event_schema_schedule_rf',
					'type'    	=> 'select',
                                	'options' 	=> array(
                                        ''      => 'Choose',
                                        'P1W'   => 'Weekly',
                                        'P1M'   => 'Monthly',
                                        'P1D'   => 'EveryDay'                                        
                               ) 
				),
				array(
                                	'label' => esc_html__( 'Schedule byDay', 'schema-and-structured-data-for-wp' ),
                                	'name'  => 'saswp_event_schema_schedule_bd',
                                	'type'  => 'text',
                                	'attributes' => array(
                                        'placeholder' => 'Monday, Wednesday'
                                 	),
                                	'note' => 'Note: Separate it by comma ( , )'                                  
                        	),
                        	array(
                                	'label' => esc_html__( 'Schedule byMonthDay', 'schema-and-structured-data-for-wp' ),
                                	'name'  => 'saswp_event_schema_schedule_bmd',
                                	'type'  => 'text',
                                	'attributes' => array(
                                        'placeholder' => '1, 13, 24'
                                 	),
                                 	'note' => 'Note: Separate it by comma ( , )'                                                                  
                        	),
                        	array(
                                'label'  => esc_html__( 'Schedule Timezone', 'schema-and-structured-data-for-wp' ),
                                'name'     => 'saswp_event_schema_schedule_tmz',
                                'type'   => 'text',
                                'attributes' => array(
                                        'placeholder' => 'Europe/London'
                                 ),                                
                        	)
			),
						'joblocation' => array(                    						
							array(
								'label'     => esc_html__( 'Street Address', 'schema-and-structured-data-for-wp' ),
								'name'      => 'saswp_jobposting_street_address',
								'type'      => 'text'                        
							),
							array(
								'label'     => esc_html__( 'Locality', 'schema-and-structured-data-for-wp' ),
								'name'      => 'saswp_jobposting_locality',
								'type'      => 'text'                        
							),
							array(
								'label'     => esc_html__( 'Region', 'schema-and-structured-data-for-wp' ),
								'name'      => 'saswp_jobposting_region',
								'type'      => 'text'
							),
							array(
								'label'     => esc_html__( 'Postal Code', 'schema-and-structured-data-for-wp' ),
								'name'      => 'saswp_jobposting_postalcode',
								'type'      => 'text'                        
							),							                   
							array(
								'label'     => esc_html__( 'Country', 'schema-and-structured-data-for-wp' ),
								'name'      => 'saswp_jobposting_country',
								'type'      => 'text'
							),
							array(
								'label'     => esc_html__( 'GeoCoordinates Latitude', 'schema-and-structured-data-for-wp' ),
								'name'      => 'saswp_jobposting_latitude',
								'type'      => 'text'                        
							),
							array(
								'label'     => esc_html__( 'GeoCoordinates Longitude', 'schema-and-structured-data-for-wp' ),
								'name'      => 'saswp_jobposting_longitude',
								'type'      => 'text'                        
							)							                                                            
						),

                    'howto_supply' => array(                    
						array(
							'label'     => esc_html__( 'Supply Name', 'schema-and-structured-data-for-wp' ),
							'name'      => 'saswp_howto_supply_name',
							'type'      => 'text',                        
							),
									array(
							'label'     => esc_html__( 'Supply URL', 'schema-and-structured-data-for-wp' ),
							'name'      => 'saswp_howto_supply_url',
							'type'      => 'text',                        
							),    
									array(
							'label'     => esc_html__( 'Supply Image', 'schema-and-structured-data-for-wp' ),
							'name'      => 'saswp_howto_supply_image',
							'type'      => 'media',                        
							)                                        
					),					
					'accepted_answer' => array(                    
						array(
							'label'     => esc_html__( 'Accepted Answer Text', 'schema-and-structured-data-for-wp' ),
							'name'      => 'saswp_qa_accepted_answer_text',
							'type'      => 'text',                        
							),
						array(
							'label'     => esc_html__( 'Accepted Answer Date Created', 'schema-and-structured-data-for-wp' ),
							'name'      => 'saswp_qa_accepted_answer_date_created',
							'type'      => 'text',                        
							),    
						array(
							'label'     => esc_html__( 'Accepted Answer Upvote Count', 'schema-and-structured-data-for-wp' ),
							'name'      => 'saswp_qa_accepted_answer_upvote_count',
							'type'      => 'number',                        
							),
						array(
								'label'     => esc_html__( 'Accepted Answer Url', 'schema-and-structured-data-for-wp' ),
								'name'      => 'saswp_qa_accepted_answer_url',
								'type'      => 'text',                        
						),
						array(
							'label'     => esc_html__( 'Accepted Answer Author Type', 'schema-and-structured-data-for-wp' ),
							'name'      => 'saswp_qa_accepted_author_type',
							'type'      => 'select',
							'options'   => array(
								'Person' 		=> 'Person',
								'Organization'  => 'Organization'
							)                       
						),
						array(
								'label'     => esc_html__( 'Accepted Answer Author Name', 'schema-and-structured-data-for-wp' ),
								'name'      => 'saswp_qa_accepted_author_name',
								'type'      => 'text',                        
							),
						array(
			                            'label'      => esc_html__( 'Author URL', 'schema-and-structured-data-for-wp' ),
			                            'name'         => 'saswp_qa_accepted_author_url',
			                            'type'       => 'text'
			                    )                                        
                    ),
					'suggested_answer' => array(                    
						array(
							'label'     => esc_html__( 'suggested Answer Text', 'schema-and-structured-data-for-wp' ),
							'name'      => 'saswp_qa_suggested_answer_text',
							'type'      => 'text',                        
							),
						array(
							'label'     => esc_html__( 'suggested Answer Date Created', 'schema-and-structured-data-for-wp' ),
							'name'      => 'saswp_qa_suggested_answer_date_created',
							'type'      => 'text',                        
							),    
						array(
							'label'     => esc_html__( 'suggested Answer Upvote Count', 'schema-and-structured-data-for-wp' ),
							'name'      => 'saswp_qa_suggested_answer_upvote_count',
							'type'      => 'number',                        
							),
						array(
								'label'     => esc_html__( 'suggested Answer Url', 'schema-and-structured-data-for-wp' ),
								'name'      => 'saswp_qa_suggested_answer_url',
								'type'      => 'text',                        
						),
						array(
							'label'     => esc_html__( 'Accepted Answer Author Type', 'schema-and-structured-data-for-wp' ),
							'name'      => 'saswp_qa_suggested_author_type',
							'type'      => 'select',
							'options'   => array(
								'Person' 		=> 'Person',
								'Organization'  => 'Organization'
							)                       
						),
						array(
								'label'     => esc_html__( 'suggested Answer Author Name', 'schema-and-structured-data-for-wp' ),
								'name'      => 'saswp_qa_suggested_author_name',
								'type'      => 'text',                        
						),
						array(
			                            'label'      => esc_html__( 'Author URL', 'schema-and-structured-data-for-wp' ),
			                            'name'         => 'saswp_qa_suggested_author_url',
			                            'type'       => 'text'
			                    )                                        
                    ),
                    'howto_tool' => array(                    
                    array(
			'label'     => esc_html__( 'Tool Name', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_howto_tool_name',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => esc_html__( 'Tool URL', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_howto_tool_url',
			'type'      => 'text',                        
		    ),    
                    array(
			'label'     => esc_html__( 'Tool Image', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_howto_tool_image',
			'type'      => 'media',                        
		    )                                        
                    ),
                    'howto_step' => array(                    
								array(
									'label'     => esc_html__( 'Step Name', 'schema-and-structured-data-for-wp' ),
									'name'      => 'saswp_howto_step_name',
									'type'      => 'text',                        
									),
								array(
									'label'     => esc_html__( 'HowToDirection Text', 'schema-and-structured-data-for-wp' ),
									'name'      => 'saswp_howto_direction_text',
									'type'      => 'text',                        
									),
								array(
									'label'     => esc_html__( 'HowToTip Text', 'schema-and-structured-data-for-wp' ),
									'name'      => 'saswp_howto_tip_text',
									'type'      => 'text',                        
									),    
								array(
									'label'     => esc_html__('Step Image', 'schema-and-structured-data-for-wp' ),
									'name'      => 'saswp_howto_step_image',
									'type'      => 'media',                        
								),        								
								array(
									'label'     => esc_html__( 'Video Clip Name', 'schema-and-structured-data-for-wp' ),
									'name'      => 'saswp_howto_video_clip_name',
									'type'      => 'text',                        
								),    
								array(
									'label'     => esc_html__( 'Video Clip URL', 'schema-and-structured-data-for-wp' ),
									'name'      => 'saswp_howto_video_clip_url',
									'type'      => 'text',                        
								),    
								array(
									'label'     => esc_html__( 'Video Clip StartOffset', 'schema-and-structured-data-for-wp' ),
									'name'      => 'saswp_howto_video_start_offset',
									'type'      => 'number',                        
								),    
								array(
									'label'     => esc_html__( 'Video Clip EndOffset', 'schema-and-structured-data-for-wp' ),
									'name'      => 'saswp_howto_video_end_offset',
									'type'      => 'number',                        
								)
					),	
					'recipe_instructions' => array(                    
						array(
							'label'     => esc_html__( 'Step Name', 'schema-and-structured-data-for-wp' ),
							'name'      => 'saswp_recipe_instructions_step_name',
							'type'      => 'text',                        
							),
						array(
							'label'     => esc_html__( 'Step Text','schema-and-structured-data-for-wp' ),
							'name'      => 'saswp_recipe_instructions_step_text',
							'type'      => 'text',                        
							),
						array(
							'label'     => esc_html__( 'Step Image', 'schema-and-structured-data-for-wp' ),
							'name'      => 'saswp_recipe_instructions_step_image',
							'type'      => 'media',
							),					
					),					
					'announcement_location' => array(                    
						array(
							'label'     => esc_html__( 'Location Type', 'schema-and-structured-data-for-wp' ),
							'name'      => 'saswp_sp_location_type',
							'type'      => 'select',
								'options'   => array(
									'CovidTestingFacility'  => 'CovidTestingFacility',
									'School'                => 'School'
								)                        
							),
							array(
								'label'     => esc_html__( 'Location Name', 'schema-and-structured-data-for-wp' ),
								'name'      => 'saswp_sp_location_name',
								'type'      => 'text',                        
							),
							array(
								'label'     => esc_html__( 'Location Street Address', 'schema-and-structured-data-for-wp' ),
								'name'      => 'saswp_sp_location_street_address',
								'type'      => 'text',                        
							),	
							array(
								'label'     => esc_html__( 'Location Address Locality', 'schema-and-structured-data-for-wp' ),
								'name'      => 'saswp_sp_location_street_locality',
								'type'      => 'text',                        
							),	
							array(
								'label'     => esc_html__( 'Location Address Region', 'schema-and-structured-data-for-wp' ),
								'name'      => 'saswp_sp_location_street_region',
								'type'      => 'text',                        
							),
							array(
								'label'     => esc_html__( 'Location URL','schema-and-structured-data-for-wp' ),
								'name'      => 'saswp_sp_location_url',
								'type'      => 'text',                        
							),							
							array(
								'label'     => esc_html__( 'Location Telephone', 'schema-and-structured-data-for-wp' ),
								'name'      => 'saswp_sp_location_telephone',
								'type'      => 'text',                        
							),
							array(
								'label'     => esc_html__( 'Location PriceRange', 'schema-and-structured-data-for-wp' ),
								'name'      => 'saswp_sp_location_price_range',
								'type'      => 'text',                        
							),							
							array(
							'label'     => 'Location Image',
							'name'      => 'saswp_sp_location_image',
							'type'      => 'media',                        
							)                                        
					),
                    'mc_symptom' => array(                    
                    array(
			'label'     => esc_html__( 'Sign Or Symptom', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_mc_symptom_name',
			'type'      => 'text',                        
		    )                                                         
                    ),
                    'mc_risk_factor' => array(                    
                    array(
			'label'     => esc_html__( 'Risk Factor', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_mc_risk_factor_name',
			'type'      => 'text',                        
		    )                                                           
                    ),                                                        
                    'tvseries_actor' => array(                    
                    array(
			'label'     => esc_html__( 'Actor Name', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_tvseries_actor_name',
			'type'      => 'text',                        
		    )                                                           
                    ),
                    'tvseries_character' => array(                    
                    		array(
					'label'     => esc_html__( 'Charater Name', 'schema-and-structured-data-for-wp' ),
					'name'      => 'saswp_tvseries_character_name',
					'type'      => 'text',                        
			    	),
			    	array(
					'label'     => esc_html__( 'Charater Description', 'schema-and-structured-data-for-wp' ),
					'name'      => 'saswp_tvseries_character_description',
					'type'      => 'textarea',                        
			    	),                                                            
                    ),
                    'tvseries_season' => array(                    
                    array(
			'label'     => esc_html__( 'Season Name', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_tvseries_season_name',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => esc_html__( 'Season Published Date', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_tvseries_season_published_date',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => esc_html__( 'Number Of Episodes', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_tvseries_season_episodes',
			'type'      => 'text',                        
		    )                                                            
                    ),
                   'trip_itinerary' => array(                    
                    array(
						'label'     => esc_html__( 'Itinerary Type', 'schema-and-structured-data-for-wp' ),
						'name'      => 'saswp_trip_itinerary_type',
						'type'      => 'select',
									'options'   => array(
											'City'                            => 'City',
											'LandmarksOrHistoricalBuildings'  => 'LandmarksOrHistoricalBuildings',
											'AdministrativeArea'              => 'AdministrativeArea',
											'LakeBodyOfWater'                 => 'LakeBodyOfWater'
									)
		    ),
                    array(
						'label'     => esc_html__( 'Itinerary Name', 'schema-and-structured-data-for-wp' ),
						'name'      => 'saswp_trip_itinerary_name',
						'type'      => 'text'                        
		    ),
                     array(
						'label'     => esc_html__( 'Itinerary Description', 'schema-and-structured-data-for-wp' ),
						'name'      => 'saswp_trip_itinerary_description',
						'type'      => 'textarea'                        
		    ),
                     array(
						'label'     => esc_html__( 'Itinerary URL', 'schema-and-structured-data-for-wp' ),
						'name'      => 'saswp_trip_itinerary_url',
						'type'      => 'text'                        
						)   
                    ),   
					'boat_trip_itinerary' => array(                    
						array(
							'label'     => esc_html__( 'Itinerary Type', 'schema-and-structured-data-for-wp' ),
							'name'      => 'saswp_boat_trip_itinerary_type',
							'type'      => 'select',
										'options'   => array(
												'City'                            => 'City',
												'LandmarksOrHistoricalBuildings'  => 'LandmarksOrHistoricalBuildings',
												'AdministrativeArea'              => 'AdministrativeArea',
												'LakeBodyOfWater'                 => 'LakeBodyOfWater'
										)
							),
						array(
							'label'     => esc_html__( 'Itinerary Name', 'schema-and-structured-data-for-wp' ),
							'name'      => 'saswp_boat_trip_itinerary_name',
							'type'      => 'text'                        
							),
						 array(
							'label'     => esc_html__( 'Itinerary Description', 'schema-and-structured-data-for-wp' ),
							'name'      => 'saswp_boat_trip_itinerary_description',
							'type'      => 'textarea'                        
							),
						 array(
							'label'     => esc_html__( 'Itinerary URL', 'schema-and-structured-data-for-wp' ),
							'name'      => 'saswp_boat_trip_itinerary_url',
							'type'      => 'text'                        
							)   
						),                                                                     
                    'faq_question' => array(                                       
                    array(
						'label'     => esc_html__( 'Question', 'schema-and-structured-data-for-wp' ),
						'name'      => 'saswp_faq_question_name',
						'type'      => 'text'                        
						),
                     array(
			'label'     => esc_html__( 'Accepted Answer', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_faq_question_answer',
			'type'      => 'textarea'                        
		    )                    
                    ),
                    'apartment_amenities' => array(                    
                    array(
			'label'     => esc_html__( 'Amenity Name', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_apartment_amenities_name',
			'type'      => 'text',                        
		    )                                                                                    
                    ),
                    'additional_property' => array(                    
                    array(
			'label'     => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_apartment_additional_property_name',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => esc_html__( 'Value', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_apartment_additional_property_value',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => esc_html__( 'Code Type', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_apartment_additional_property_code_type',
			'type'      => 'select',
                        'options'   => array(
                                'unitCode'   => 'Unit Code',
                                'unitText'   => 'Unit Text',                                                                                                
                        )
		    ),
                    array(
			'label'     => esc_html__( 'Code Value', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_apartment_additional_property_code_value',
			'type'      => 'text',                        
		    ),    
                    ),
                    'music_playlist_track' => array(                    
                    array(
			'label'     => esc_html__( 'Track Artist', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_music_playlist_track_artist',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => esc_html__( 'Track Duration', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_music_playlist_track_duration',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => esc_html__( 'Track In Album', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_music_playlist_track_inalbum',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => esc_html__( 'Track Name', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_music_playlist_track_name',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => esc_html__( 'Track URL', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_music_playlist_track_url',
			'type'      => 'text',                        
		    ),    
                       
                    ),
                    'music_album_track' => array(                                        
                    array(
			'label'     => esc_html__( 'Track Duration', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_music_album_track_duration',
			'type'      => 'text',                        
		    ),                    
                    array(
			'label'     => esc_html__( 'Track Name', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_music_album_track_name',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => esc_html__( 'Track URL', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_music_album_track_url',
			'type'      => 'text',                        
		    ),                           
             ),
             'faq_repeater_question' => array(
             		array(
				'label'     => esc_html__( 'Question', 'schema-and-structured-data-for-wp' ),
				'name'      => 'saswp_faq_repeater_question_name',
				'type'      => 'select',
					'options'   => array()                      
			),	
                     array(
				'label'     => esc_html__( 'Accepted Answer', 'schema-and-structured-data-for-wp' ),
				'name'      => 'saswp_faq_repeater_question_answer',
				'type'      => 'select',
					'options'   => array()                            
		    )
             ),
             'tourist_trip_itinerary' => array(                    
		array(
			'label'     => esc_html__( 'Itinerary Name', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_tourist_trip_itinerary_name',
			'type'      => 'text'                        
			),
		 array(
			'label'     => esc_html__( 'Itinerary Description', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_tourist_trip_itinerary_description',
			'type'      => 'textarea'                        
			),  
		),
		'course_instance' => array(                    
		array(
			'label'     => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_course_instance_name',
			'type'      => 'text'                     
			),
		array(
			'label'     => esc_html__( 'Course Mode', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_course_instance_mode',
			'type'      => 'text'                     
			),
		 array(
			'label'     => esc_html__( 'Start Date', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_course_instance_start_date',
			'type'      => 'date',
			'default' => get_the_date("Y-m-d")
			),  
		 array(
			'label'     => esc_html__( 'End Date', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_course_instance_end_date',
			'type'      => 'date',
			'default' => get_the_date("Y-m-d")
			),
		  array(
              	'label' => esc_html__( 'Start Time', 'schema-and-structured-data-for-wp' ),
              	'name'  => 'saswp_course_instance_start_time',
              	'type'  => 'text',                                
	              ),
	              array(
              	'label' => esc_html__( 'End Time', 'schema-and-structured-data-for-wp' ),
                     'name'  => 'saswp_course_instance_end_time',
                     'type' => 'text',                                
	              ),
		array(
			'label'     => esc_html__( 'Course Workload', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_course_instance_wl',
			'type'      => 'text'
			),  
		array(
			'label'     => esc_html__( 'Schedule Duration', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_course_instance_sd',
			'type'      => 'text'
			), 
		array(
			'label'     => esc_html__( 'Schedule Repeat Count', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_course_instance_src',
			'type'      => 'number'
			),
		array(
			'label'     => esc_html__( 'Schedule Repeat Frequency', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_course_instance_srf',
			'type'      => 'select',
			'options'   => array(                                                                                              
					'Daily'      => 'Daily',
					'Weekly'     => 'Weekly',
					'Monthly'    => 'Monthly',
					'Yearly'     => 'Yearly'
				)
			),
		array(
                  	'label' => esc_html__( 'Schedule byDay', 'schema-and-structured-data-for-wp' ),
                  	'name'  => 'saswp_course_instance_sbyd',
                  	'type'  => 'text',
                  	'attributes' => array(
                          'placeholder' => 'Monday, Wednesday'
                   	),
                  	'note' => 'Note: Separate it by comma ( , )'                                  
          	),
          	array(
                  	'label' => esc_html__( 'Schedule byMonthDay', 'schema-and-structured-data-for-wp' ),
                  	'name'  => 'saswp_course_instance_sbmd',
                  	'type'  => 'text',
                  	'attributes' => array(
                          'placeholder' => '1, 13, 24'
                   	),
                   	'note' => 'Note: Separate it by comma ( , )'                                                                  
          	), 
		array(
			'label'     => esc_html__( 'Location', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_course_instance_location',
			'type'      => 'text'
			),
		array(
			'label'     => esc_html__( 'Offer Price', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_course_instance_offer_price',
			'type'      => 'number'
			), 
		array(
			'label'     => 'Offer Price Currency',
			'name'      => 'saswp_course_instance_offer_currency',
			'type'      => 'text'
			), 
		),
		'vacation_rental_bed_details' => array(
		array(
			'label'     => esc_html__( 'Number Of Beds', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_vr_bed_details_nob',
			'type'      => 'number'
			),
		array(
			'label'     => esc_html__( 'Type Of Bed', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_vr_bed_details_tob',
			'type'       => 'select',
                     'options' => array(
	                         'CaliforniaKing' => 'CaliforniaKing',
	                         'King'          => 'King',
	                         'Queen'         => 'Queen',
	                         'Full'          => 'Full',
	                         'Double'        => 'Double',
	                         'SemiDouble'    => 'SemiDouble',
	                         'Single'        => 'Single',
                     	)
			),
		),
		'vacation_rental_amenity_feature' => array(
		array(
			'label'     => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_vr_amenity_feature_name',
			'type'       => 'select',
                     'options' => array(
	                         'ac'                        => 'AC',
	                         'airportShuttle'            => 'Airport Shuttle',
	                         'balcony'                   => 'Balcony',
	                         'beachAccess'               => 'Beach Access',
	                         'childFriendly'             => 'Child Friendly',
	                         'crib'                      => 'Crib',
	                         'elevator'                  => 'Elevator',
	                         'fireplace'                 => 'Fire Place',
	                         'freeBreakfast'             => 'Free Break Fast',
	                         'gymFitnessEquipment'       => 'Gym Fitness Equipment',
	                         'heating'                   => 'Heating',
	                         'hotTub'                    => 'Hot Tub',
	                         'instantBookable'           => 'Instant Bookable',
	                         'ironingBoard'              => 'Ironing Board',
	                         'kitchen'                   => 'Kitchen',
	                         'outdoorGrill'              => 'Outdoor Grill',
	                         'ovenStove'                 => 'Oven Stove',
	                         'patio'                     => 'Patio',
	                         'petsAllowed'               => 'Pets Allowed',
	                         'pool'                      => 'Pool',
	                         'privateBeachAccess'        => 'Private Beach Access',
	                         'selfCheckinCheckout'       => 'Self Checkin Checkout',
	                         'smokingAllowed'            => 'Smoking Allowed',
	                         'tv'                        => 'TV',
	                         'washerDryer'               => 'Washer Dryer',
	                         'wheelchairAccessible'      => 'Wheel Chair Accessible',
	                         'wifi'                      => 'Wifi',
	                         'internetType'              => 'Internet Type',
	                         'parkingType'               => 'Parking Type',
	                         'poolType'                  => 'Pool Type',
	                         'licenseNum'                => 'License Num',
                     	)
			),
		array(
			'label'     => esc_html__( 'Value', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_vr_amenity_feature_value',
			'type'      => 'text'
			)
		),
		'vacation_rental_property_images' => array(
			array(
			'label'     => esc_html__( 'Property Image', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_vr_property_image',
			'type'      => 'media',                        
			) 
		),
		'vacation_rental_review_rating' => array(
		array(
			'label'     => esc_html__( 'Rating Value', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_vr_review_rating_value',
			'type'      => 'number'
			),
		array(
			'label'     => esc_html__( 'Best Rating', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_vr_review_rating_best_value',
			'type'      => 'number'
			),
		array(
			'label'     => esc_html__( 'Author Type', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_vr_review_rating_author_type',
			'type'      => 'select',
			'options'   => array(
				'Person' 		=> 'Person',
				'Organization'  	=> 'Organization'
				)                       
			),
		array(
			'label'     => esc_html__( 'Author Name', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_vr_review_rating_author_name',
			'type'      => 'text'
			),
		array(
			'label'     => esc_html__( 'Date Published', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_vr_review_rating_date_pub',
			'type'      => 'date',
			'default'   => get_the_date("Y-m-d")
			),
		array(
			'label'     => esc_html__( 'Content Reference Time', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_vr_review_rating_cr_time',
			'type'      => 'date',
			'default'   => get_the_date("Y-m-d")
			)
		),
		'learning_resource_educational_alignment' => array(
		array(
			'label'     => esc_html__( 'Alignment Type', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_lr_eaat',
			'type'      => 'text'
			),
		array(
			'label'     => esc_html__( 'Educational Framework', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_lr_eaef',
			'type'      => 'text'
			),
		array(
			'label'     => esc_html__( 'Target Name', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_lr_eatn',
			'type'      => 'text'
			),
		array(
			'label'     => esc_html__( 'Target URL', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_lr_eatu',
			'type'      => 'text'
			),
		array(
			'label'     => esc_html__( 'Audience', 'schema-and-structured-data-for-wp' ),
			'name'      => 'saswp_lr_audience',
			'type'      => 'text'
			),
		),
		'live_blog_update' 	=>	array(
			array(
				'label'     => esc_html__( 'Headline', 'schema-and-structured-data-for-wp' ),
				'name'      => 'saswp_lbp_lbu_headline',
				'type'      => 'text'
			),
			array(
				'label'     => esc_html__( 'Published Date', 'schema-and-structured-data-for-wp' ),
				'name'      => 'saswp_lbp_lbu_published_date',
				'type'      => 'text',
				'default'   => get_the_date( 'Y-m-d' )                            
			),
			array(
                         	'label'     => esc_html__( 'Published Time', 'schema-and-structured-data-for-wp' ),
                         	'name'      => 'saswp_lbp_lbu_published_time',
                         	'type'      => 'text',
                     ),
			array(
				'label'     => esc_html__( 'Article Body', 'schema-and-structured-data-for-wp' ),
				'name'      => 'saswp_lbp_lbu_article_body',
				'type'      => 'textarea',                          
			),
			array(
				'label'     => esc_html__( 'Image', 'schema-and-structured-data-for-wp' ),
				'name'      => 'saswp_lbp_lbu_image',
				'type'      => 'media',                        
			),	
		),
		'media_gallery_associated_media' 	=>	array(
			array(
				'label'     => esc_html__( 'Thumbnail URL', 'schema-and-structured-data-for-wp' ),
				'name'      => 'saswp_mg_thumbnail_url',
				'type'      => 'media'
			),
			array(
				'label'     => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
				'name'      => 'saswp_mg_name',
				'type'      => 'text',                         
			),
			array(
				'label'     => esc_html__( 'Content URL', 'schema-and-structured-data-for-wp' ),
				'name'      => 'saswp_mg_content_url',
				'type'      => 'media',                          
			),
			array(
				'label'     => esc_html__( 'Content URL', 'schema-and-structured-data-for-wp' ),
				'name'      => 'saswp_mg_caption',
				'type'      => 'text',                          
			),
			array(
				'label'     => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
				'name'      => 'saswp_mg_description',
				'type'      => 'textarea',                          
			),	
		),
		'image_gallery_collections' 	=>	array(
			array(
				'label'     => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
				'name'      => 'saswp_image_gallery_name',
				'type'      => 'text',                        
			),
			array(
				'label'     => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
				'name'      => 'saswp_image_gallery_description',
				'type'      => 'textarea',                          
			),
			array(
				'label'     => esc_html__( 'Caption', 'schema-and-structured-data-for-wp' ),
				'name'      => 'saswp_image_gallery_caption',
				'type'      => 'text',                          
			),
			array(
				'label'     => esc_html__( 'Thumbnail URL', 'schema-and-structured-data-for-wp' ),
				'name'      => 'saswp_image_gallery_thumbnail_url',
				'type'      => 'media'
			),
			array(
				'label'     => esc_html__( 'Content URL', 'schema-and-structured-data-for-wp' ),
				'name'      => 'saswp_image_gallery_content_url',
				'type'      => 'media',                          
			),	
		),
		'vehicle_engine' 	=>	array(
			array(
				'label'     => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
				'name'      => 'saswp_vehicle_engine_name',
				'type'      => 'text',                        
			),
			array(
				'label'     => esc_html__( 'Type', 'schema-and-structured-data-for-wp' ),
				'name'      => 'saswp_vehicle_engine_type',
				'type'      => 'text',                        
			),
			array(
				'label'     => esc_html__( 'Fuel Type', 'schema-and-structured-data-for-wp' ),
				'name'      => 'saswp_vehicle_engine_fuel_type',
				'type'      => 'text',                        
			),
			array(
				'label'     => esc_html__( 'Displacement Value', 'schema-and-structured-data-for-wp' ),
				'name'      => 'saswp_vehicle_engine_dis_value',
				'type'      => 'text',                        
			),
			array(
				'label'     => esc_html__( 'Displacement Unit Code', 'schema-and-structured-data-for-wp' ),
				'name'      => 'saswp_vehicle_engine_dis_unit_code',
				'type'      => 'text',                        
			),
			array(
				'label'     => esc_html__( 'Power Value', 'schema-and-structured-data-for-wp' ),
				'name'      => 'saswp_vehicle_engine_power_value',
				'type'      => 'text',                        
			),
			array(
				'label'     => esc_html__( 'Power Unit Code', 'schema-and-structured-data-for-wp' ),
				'name'      => 'saswp_vehicle_engine_power_unit_code',
				'type'      => 'text',                        
			),
			array(
				'label'     => esc_html__( 'Torque Value', 'schema-and-structured-data-for-wp' ),
				'name'      => 'saswp_vehicle_engine_torque_value',
				'type'      => 'text',                        
			),
			array(
				'label'     => esc_html__( 'Torque Unit Code', 'schema-and-structured-data-for-wp' ),
				'name'      => 'saswp_vehicle_engine_torque_unit_code',
				'type'      => 'text',                        
			),
		),
		'available_service' 	=>	array(
			array(
				'label'     => esc_html__( 'Type', 'schema-and-structured-data-for-wp' ),
				'name'      => 'saswp_local_business_as_type',
				'type'      => 'text',                        
			),
			array(
				'label'     => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
				'name'      => 'saswp_local_business_as_name',
				'type'      => 'text',                        
			),
		),
		'sports_team_member_of' 	=>	array(
			
			array(
				'label'     => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
				'name'      => 'saswp_sports_team_as_name',
				'type'      => 'text',                        
			),
		),
		'sports_team_athlete' 	=>	array(
			
			array(
				'label'     => esc_html__( 'Name', 'schema-and-structured-data-for-wp' ),
				'name'      => 'saswp_sports_team_as_name',
				'type'      => 'text',                        
			),
		),
		                    
        )    
);