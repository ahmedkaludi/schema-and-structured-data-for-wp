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
        'CreativeWorkSeries', 
        'SoftwareApplication',
        'MobileApplication',                                                                  
        'Event',                    
        'VideoGame',                
        'AudioObject',              
        'VideoObject',              
        'local_business',
        'Organization',           
        'Product'              
);
$without_aggregate = array(
        'Apartment',
        'RealEstateListing',
        'House',
        'SingleFamilyResidence',
        'Article',
        'BlogPosting',
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
        'PsychologicalTreatment',
        'HinduTemple',
        'BuddhistTemple',
        'Church',
        'Mosque',
        'Person',
        'SpecialAnnouncement'
);
$translation_labels = array(
    'translation-pros'            => 'Pros',
    'translation-cons'            => 'Cons',
    'translation-review-overview' => 'Review Overview',
    'translation-overall-score'   => 'Overall Score',
    'translation-tools'           => 'Tools',
    'translation-materials'       => 'Materials',
    'translation-time-needed'     => 'Time Needed',
    'translation-estimate-cost'   => 'Estimate Cost',
    'translation-name'            => 'Name',
    'translation-comment'         => 'Comment',
    'translation-review-form'     => 'Review Form',
    'translation-based-on'        => 'Based On',
    'translation-reviews'         => 'Reviews',
    'translation-self'            => 'Self',
);          
$saswp_post_reviews = array();
$saswp_elementor_faq = array();
$saswp_wisdom;