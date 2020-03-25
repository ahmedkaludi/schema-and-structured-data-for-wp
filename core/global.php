<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
$saswp_divi_faq = array();
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
        'BlogPosting',
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
$translation_labels = array(
    'translation-pros'            => 'Pros',
    'translation-cons'            => 'Cons',
    'translation-review-overview' => 'Review Overview',
    'translation-overall-score'   => 'Overall Score',
    'translation-tools'           => 'Tools',
    'translation-materials'       => 'Materials',
    'translation-time-needed'     => 'Time Needed',
    'translation-estimate-cost'     => 'Estimate Cost',
    'translation-name'            => 'Name',
    'translation-comment'         => 'Comment',
    'translation-review-form'     => 'Review Form',
);          
$saswp_post_reviews = array();
$saswp_elementor_faq = array();
$saswp_wisdom;