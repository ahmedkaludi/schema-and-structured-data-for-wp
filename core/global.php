<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
$with_aggregate = array(
        'Book',                     
        'Course',                                         
        'HowTo',                    
        'ImageObject',              
        'MusicPlaylist',            
        'MusicAlbum',               
        'MusicComposition',         
        'Movie',                                                    
        'Review',                   
        'Recipe',                   
        'TVSeries', 
        'SoftwareApplication',                                                                  
        'Event',                    
        'VideoGame',                
        'AudioObject',              
        'VideoObject',              
        'local_business',           
        'Product'              
);
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
$saswp_post_reviews = array();
