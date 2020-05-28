<?php 
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

return array( 'schema_type_element' => array( 
                        'ItemList' => array(
                               'itemlist_item'       => 'itemlist_item',                                                     
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
                        'ImageObject' => array(
                               'image_object_exif_data' => 'image_object_exif_data',                                                
                        ),
                        'BlogPosting' => array(
                               'blogposting_items' => 'blogposting_items',                                                
                        ),
                        'NewsArticle' => array(
                                'newsarticle_items' => 'newsarticle_items',                                                
                                    ),
                        'TechArticle' => array(
                               'tech_article_items' => 'tech_article_items',                                                
                        ),
                        'Product' => array(
                               'product_reviews' => 'product_reviews',                                                
                        ),                        
                        'DataFeed' => array(
                               'feed_element' => 'feed_element',                                                
                        ),
                        'FAQ' => array(
                               'faq-question' => 'faq_question',                                                
                        ),
                        'Event' => array(
							   'performer'     => 'performer', 
							   'organizer'     => 'organizer',                                                
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
                               'mc-cause'       => 'mc_cause', 
                               'mc-symptom'     => 'mc_symptom', 
                               'mc-risk_factor' => 'mc_risk_factor', 

                        ),
                        'TVSeries' => array(
                               'tvseries-actor'  => 'tvseries_actor',
                               'tvseries-season' => 'tvseries_season', 
                        ),
                        'Trip' => array(
                               'trip-itinerary'  => 'trip_itinerary'
                        )                                                                          
                    ),
    'meta_name' => array(
                     'movie_actor' => array(                    
                        array(
                            'label'     => 'Name',
                            'name'      => 'saswp_movie_actor_name',
                            'type'      => 'text',                        
                        ),
                        array(
                            'label'     => 'URL',
                            'name'      => 'saswp_movie_actor_url',
                            'type'      => 'text',                        
                        ) 
                    ),
                    'music_composer' => array(                    
                    array(
			'label'     => 'Name',
			'name'      => 'saswp_music_composition_composer_name',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => 'URL',
			'name'      => 'saswp_music_composition_composer_url',
			'type'      => 'text',                        
		    )    
                    ),
                    'article_items' => array(                    
                    array(
			'label'     => 'Item Name',
			'name'      => 'saswp_article_items_name',
			'type'      => 'text',                        
		    )                     
                    ),
                    'image_object_exif_data' => array(                    
                    array(
			'label'     => 'Name',
			'name'      => 'saswpimage_object_exif_data_name',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => 'Value',
			'name'      => 'saswpimage_object_exif_data_value',
			'type'      => 'text',                        
		    )    
                    ),
                    'newsarticle_items' => array(                    
                    array(
			'label'     => 'Item Name',
			'name'      => 'saswp_newsarticle_items_name',
			'type'      => 'text',                        
		    )                     
                    ),
                    'tech_article_items' => array(                    
                    array(
			'label'     => 'Item Name',
			'name'      => 'saswp_tech_article_items_name',
			'type'      => 'text',                        
		    )                     
                    ),
                    'blogposting_items' => array(                    
                    array(
			'label'     => 'Item Name',
			'name'      => 'saswp_blogposting_items_name',
			'type'      => 'text',                        
		    )                     
                    ),
                    'product_reviews' => array(                    
                    array(
			'label'     => 'Reviewer Name',
			'name'      => 'saswp_product_reviews_reviewer_name',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => 'Rating',
			'name'      => 'saswp_product_reviews_reviewer_rating',
			'type'      => 'number',                        
		    ),
                    array(
			'label'     => 'Text',
			'name'      => 'saswp_product_reviews_text',
			'type'      => 'textarea',                        
		    ),
                    array(
			'label'     => 'Created Date',
			'name'      => 'saswp_product_reviews_created_date',
			'type'      => 'text',                        
		    )    
                    ),                   
                    'feed_element' => array(                    
                    array(
			'label'     => 'Date Created',
			'name'      => 'saswp_feed_element_date_created',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => 'Feed Element Name',
			'name'      => 'saswp_feed_element_name',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => 'Feed Element email',
			'name'      => 'saswp_feed_element_email',
			'type'      => 'text',                        
		    )    
                    ),
                    'performer' => array(                    
                    array(
			'label'     => 'Performer Type',
			'name'      => 'saswp_event_performer_type',
			'type'      => 'select',
                        'options'   => array(                                                                                              
								'Person'           => 'Person',
								'Organization'     => 'Organization',
								'MusicGroup'       => 'MusicGroup'
                        )
		    ),
                    array(
			'label'     => 'Performer Name',
			'name'      => 'saswp_event_performer_name',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => 'Performer URL',
			'name'      => 'saswp_event_performer_url',
			'type'      => 'text',                        
		    )                                                            
					),
					
					'organizer' => array(                    						
							array(
								'label'     => 'Organizer Name',
								'name'      => 'saswp_event_organizer_name',
								'type'      => 'text',                        
							),
							array(
								'label'     => 'Organizer URL',
								'name'      => 'saswp_event_organizer_url',
								'type'      => 'text',                        
							),
							array(
								'label'     => 'Organizer Phone',
								'name'      => 'saswp_event_organizer_phone',
								'type'      => 'text',                        
							),
							array(
								'label'     => 'Organizer Email',
								'name'      => 'saswp_event_organizer_email',
								'type'      => 'text',                        
							),							                                                            
						),

                    'howto_supply' => array(                    
						array(
							'label'     => 'Supply Name',
							'name'      => 'saswp_howto_supply_name',
							'type'      => 'text',                        
							),
									array(
							'label'     => 'Supply URL',
							'name'      => 'saswp_howto_supply_url',
							'type'      => 'text',                        
							),    
									array(
							'label'     => 'Supply Image',
							'name'      => 'saswp_howto_supply_image',
							'type'      => 'media',                        
							)                                        
					),					
					'accepted_answer' => array(                    
						array(
							'label'     => 'Accepted Answer Text',
							'name'      => 'saswp_qa_accepted_answer_text',
							'type'      => 'text',                        
							),
						array(
							'label'     => 'Accepted Answer Date Created',
							'name'      => 'saswp_qa_accepted_answer_date_created',
							'type'      => 'text',                        
							),    
						array(
							'label'     => 'Accepted Answer Upvote Count',
							'name'      => 'saswp_qa_accepted_answer_upvote_count',
							'type'      => 'number',                        
							),
						array(
								'label'     => 'Accepted Answer Url',
								'name'      => 'saswp_qa_accepted_answer_url',
								'type'      => 'text',                        
						),
						array(
								'label'     => 'Accepted Answer Author Name',
								'name'      => 'saswp_qa_accepted_author_name',
								'type'      => 'text',                        
							)                                        
                    ),
					'suggested_answer' => array(                    
						array(
							'label'     => 'suggested Answer Text',
							'name'      => 'saswp_qa_suggested_answer_text',
							'type'      => 'text',                        
							),
						array(
							'label'     => 'suggested Answer Date Created',
							'name'      => 'saswp_qa_suggested_answer_date_created',
							'type'      => 'text',                        
							),    
						array(
							'label'     => 'suggested Answer Upvote Count',
							'name'      => 'saswp_qa_suggested_answer_upvote_count',
							'type'      => 'number',                        
							),
						array(
								'label'     => 'suggested Answer Url',
								'name'      => 'saswp_qa_suggested_answer_url',
								'type'      => 'text',                        
						),
						array(
								'label'     => 'suggested Answer Author Name',
								'name'      => 'saswp_qa_suggested_author_name',
								'type'      => 'text',                        
							)                                        
                    ),
                    'howto_tool' => array(                    
                    array(
			'label'     => 'Tool Name',
			'name'      => 'saswp_howto_tool_name',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => 'Tool URL',
			'name'      => 'saswp_howto_tool_url',
			'type'      => 'text',                        
		    ),    
                    array(
			'label'     => 'Tool Image',
			'name'      => 'saswp_howto_tool_image',
			'type'      => 'media',                        
		    )                                        
                    ),
                    'howto_step' => array(                    
                    array(
			'label'     => 'Step Name',
			'name'      => 'saswp_howto_step_name',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => 'HowToDirection Text',
			'name'      => 'saswp_howto_direction_text',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => 'HowToTip Text',
			'name'      => 'saswp_howto_tip_text',
			'type'      => 'text',                        
		    ),    
                    array(
			'label'     => 'Step Image',
			'name'      => 'saswp_howto_step_image',
			'type'      => 'media',                        
		    )                                        
					),					
					'announcement_location' => array(                    
						array(
							'label'     => 'Location Type',
							'name'      => 'saswp_sp_location_type',
							'type'      => 'select',
								'options'   => array(
									'CovidTestingFacility'  => 'CovidTestingFacility',
									'School'                => 'School'
								)                        
							),
							array(
								'label'     => 'Location Name',
								'name'      => 'saswp_sp_location_name',
								'type'      => 'text',                        
							),
							array(
								'label'     => 'Location Street Address',
								'name'      => 'saswp_sp_location_street_address',
								'type'      => 'text',                        
							),	
							array(
								'label'     => 'Location Address Locality',
								'name'      => 'saswp_sp_location_street_locality',
								'type'      => 'text',                        
							),	
							array(
								'label'     => 'Location Address Region',
								'name'      => 'saswp_sp_location_street_region',
								'type'      => 'text',                        
							),
							array(
								'label'     => 'Location URL',
								'name'      => 'saswp_sp_location_url',
								'type'      => 'text',                        
							),							
							array(
								'label'     => 'Location Telephone',
								'name'      => 'saswp_sp_location_telephone',
								'type'      => 'text',                        
							),
							array(
								'label'     => 'Location PriceRange',
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
			'label'     => 'Sign Or Symptom',
			'name'      => 'saswp_mc_symptom_name',
			'type'      => 'text',                        
		    )                                                         
                    ),
                    'mc_risk_factor' => array(                    
                    array(
			'label'     => 'Risk Factor',
			'name'      => 'saswp_mc_risk_factor_name',
			'type'      => 'text',                        
		    )                                                           
                    ),
                    'mc_cause' => array(                    
                    array(
			'label'     => 'Cause',
			'name'      => 'saswp_mc_cause_name',
			'type'      => 'text',                        
		    )                                                           
                    ),                                    
                    'tvseries_actor' => array(                    
                    array(
			'label'     => 'Actor Name',
			'name'      => 'saswp_tvseries_actor_name',
			'type'      => 'text',                        
		    )                                                           
                    ),
                    'tvseries_season' => array(                    
                    array(
			'label'     => 'Season Name',
			'name'      => 'saswp_tvseries_season_name',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => 'Season Published Date',
			'name'      => 'saswp_tvseries_season_published_date',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => 'Number Of Episodes',
			'name'      => 'saswp_tvseries_season_episodes',
			'type'      => 'text',                        
		    )                                                            
                    ),
                   'trip_itinerary' => array(                    
                    array(
			'label'     => 'Itinerary Type',
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
			'label'     => 'Itinerary Name',
			'name'      => 'saswp_trip_itinerary_name',
			'type'      => 'text'                        
		    ),
                     array(
			'label'     => 'Itinerary Description',
			'name'      => 'saswp_trip_itinerary_description',
			'type'      => 'textarea'                        
		    ),
                     array(
			'label'     => 'Itinerary URL',
			'name'      => 'saswp_trip_itinerary_url',
			'type'      => 'text'                        
		    )   
                    ),                                                                       
                    'faq_question' => array(                                       
                    array(
			'label'     => 'Question',
			'name'      => 'saswp_faq_question_name',
			'type'      => 'text'                        
		    ),
                     array(
			'label'     => 'Accepted Answer',
			'name'      => 'saswp_faq_question_answer',
			'type'      => 'textarea'                        
		    )                    
                    ),
                    'apartment_amenities' => array(                    
                    array(
			'label'     => 'Amenity Name',
			'name'      => 'saswp_apartment_amenities_name',
			'type'      => 'text',                        
		    )                                                                                    
                    ),
                    'additional_property' => array(                    
                    array(
			'label'     => 'Name',
			'name'      => 'saswp_apartment_additional_property_name',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => 'Value',
			'name'      => 'saswp_apartment_additional_property_value',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => 'Code Type',
			'name'      => 'saswp_apartment_additional_property_code_type',
			'type'      => 'select',
                        'options'   => array(
                                'unitCode'   => 'Unit Code',
                                'unitText'   => 'Unit Text',                                                                                                
                        )
		    ),
                    array(
			'label'     => 'Code Value',
			'name'      => 'saswp_apartment_additional_property_code_value',
			'type'      => 'text',                        
		    ),    
                    ),
                    'music_playlist_track' => array(                    
                    array(
			'label'     => 'Track Artist',
			'name'      => 'saswp_music_playlist_track_artist',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => 'Track Duration',
			'name'      => 'saswp_music_playlist_track_duration',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => 'Track In Album',
			'name'      => 'saswp_music_playlist_track_inalbum',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => 'Track Name',
			'name'      => 'saswp_music_playlist_track_name',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => 'Track URL',
			'name'      => 'saswp_music_playlist_track_url',
			'type'      => 'text',                        
		    ),    
                       
                    ),
                    'music_album_track' => array(                                        
                    array(
			'label'     => 'Track Duration',
			'name'      => 'saswp_music_album_track_duration',
			'type'      => 'text',                        
		    ),                    
                    array(
			'label'     => 'Track Name',
			'name'      => 'saswp_music_album_track_name',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => 'Track URL',
			'name'      => 'saswp_music_album_track_url',
			'type'      => 'text',                        
		    ),                           
             )                    
        )    
);