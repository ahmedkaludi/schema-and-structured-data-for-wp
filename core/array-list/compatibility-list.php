<?php 
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

return array(    
    'plugins' =>    array(        
        'easy_recipe'  =>  array(            
                    'name'       => 'EasyRecipe',
                    'free'       => 'easyrecipe/easyrecipe.php', 
                    'pro'        => 'easyrecipe-pro/easyrecipe-pro.php', 
                    'opt_name'   => 'saswp-easy-recipe',
                    'part_in'    => 'pro',
        ),
        'wp_event_aggregator'  =>  array(            
                    'name'       => 'WP Event Aggregator',
                    'free'       => 'wp-event-aggregator/wp-event-aggregator.php', 
                    'pro'        => 'wp-event-aggregator-pro/wp-event-aggregator-pro.php', 
                    'opt_name'   => 'saswp-wp-event-aggregator',
                    'part_in'    => 'pro',
        ),
        'tevolution_events'  =>  array(            
                    'name'       => 'Tevolution Events',
                    'free'       => 'Tevolution-Events/events.php',            
                    'opt_name'   => 'saswp-tevolution-events',
                    'part_in'    => 'pro',
        ),
        'strong_testimonials'  =>  array(            
                    'name'       => 'Strong Testimonials',
                    'free'       => 'strong-testimonials/strong-testimonials.php',            
                    'opt_name'   => 'saswp-strong-testimonials',
                    'part_in'    => 'free',
        ),
        'wordlift'  =>  array(            
                    'name'       => 'WordLift',
                    'free'       => 'wordlift/wordlift.php',            
                    'opt_name'   => 'saswp-wordlift',
                    'part_in'    => 'free',
        ),
        'ampforwp'  =>  array(            
                    'name'       => 'AMPforWP',
                    'free'       => 'accelerated-mobile-pages/accelerated-moblie-pages.php',            
                    'opt_name'   => 'saswp-ampforwp',
                    'part_in'    => 'free',
        ),
        'ampbyautomatic'  =>  array(            
                    'name'       => 'AMP',
                    'free'       => 'amp/amp.php',            
                    'opt_name'   => 'saswp-ampbyautomatic',
                    'part_in'    => 'free',
        ),
        'betteramp'  =>  array(            
                    'name'       => 'Better AMP',
                    'free'       => 'better-amp/better-amp.php',            
                    'opt_name'   => 'saswp-betteramp',
                    'part_in'    => 'free',
        ),
        'wpamp'  =>  array(            
                    'name'       => 'WP AMP',
                    'free'       => 'wp-amp/wp-amp.php',            
                    'opt_name'   => 'saswp-wpamp',
                    'part_in'    => 'free',
        ),
       'flex_mls_idx'  =>  array(            
                    'name'       => 'FlexMLS IDX',
                    'free'       => 'flexmls-idx/flexmls_connect.php',            
                    'opt_name'   => 'saswp-flexmlx-compativility',
                    'part_in'    => 'free',
        ), 
       'kk_star_ratings'  =>  array(            
                    'name'       => 'kk Star Ratings',
                    'free'       => 'kk-star-ratings/index.php',            
                    'opt_name'   => 'saswp-kk-star-raring',
                    'part_in'    => 'free',
        ),
        'easy_testimonials'  =>  array(            
                    'name'       => 'Easy Testimonials',
                    'free'       => 'easy-testimonials/easy-testimonials.php',            
                    'opt_name'   => 'saswp-easy-testimonials',
                    'part_in'    => 'free',
        ),
        'bne_testimonials'  =>  array(            
                    'name'       => 'BNE Testimonials',
                    'free'       => 'bne-testimonials/bne-testimonials.php', 
                    'pro'        => 'bne-testimonials-pro/bne-testimonials-pro.php', 
                    'opt_name'   => 'saswp-bne-testimonials',
                    'part_in'    => 'free',
        ),
        'testimonial_pro'  =>  array(            
                    'name'       => 'Testimonial Pro',
                    'free'       => 'testimonial-pro/testimonial-pro.php',                     
                    'opt_name'   => 'saswp-testimonial-pro',
                    'part_in'    => 'free',
        ),
        'learn_press'  =>  array(            
                    'name'       => 'Learn Press',
                    'free'       => 'learnpress/learnpress.php',    
                    'opt_name'   => 'saswp-learn-press',
                    'part_in'    => 'pro',
        ),
        'learn_dash'  =>  array(            
                    'name'       => 'Learn Dash',
                    'free'       => 'learndash/learndash.php',    
                    'opt_name'   => 'saswp-learn-dash',
                    'part_in'    => 'pro',
        ),
        'lifter_lms'  =>  array(            
                    'name'       => 'Lifter LMS',
                    'free'       => 'lifterlms/lifterlms.php',              
                    'opt_name'   => 'saswp-lifter-lms',
                    'part_in'    => 'pro',
        ),
        'wp_post_ratings' =>  array(            
                    'name'       => 'WP-PostRatings',
                    'free'       => 'wp-postratings/wp-postratings.php',            
                    'opt_name'   => 'saswp-wppostratings-raring',  
                    'part_in'    => 'free',
        ),
        'bb_press' => array(            
                    'name'       => 'bbPress',
                    'free'       => 'bbpress/bbpress.php',            
                    'opt_name'   => 'saswp-bbpress',
                    'part_in'    => 'free',
        ),
        'woocommerce' => array(            
                    'name'       => 'Woocommerce',
                    'free'       => 'woocommerce/woocommerce.php',            
                    'opt_name'   => 'saswp-woocommerce',
                    'part_in'    => 'free',
        ),        
        'woocommerce_bookings' => array(            
                    'name'       => 'Woocommerce Bookings',
                    'free'       => 'woocommerce-bookings/woocommerce-bookings.php',            
                    'opt_name'   => 'saswp-woocommerce-booking', 
                    'part_in'    => 'pro',
                    'parent'     => 'woocommerce'
        ),
        'woocommerce_membership' => array(            
                    'name'       => 'Woocommerce Membership',
                    'free'       => 'woocommerce-memberships/woocommerce-memberships.php',            
                    'opt_name'   => 'saswp-woocommerce-membership',    
                    'part_in'    => 'pro',
                    'parent'     => 'woocommerce'
        ),        
        'cooked' => array(            
                    'name'       => 'Cooked',
                    'free'       => 'cooked/cooked.php',  
                    'pro'        => 'cooked-pro/cooked-pro.php',  
                    'opt_name'   => 'saswp-cooked',
                    'part_in'    => 'pro',
        ),
        'the_events_calendar' => array(            
                    'name'       => 'The Events Calendar',
                    'free'       => 'the-events-calendar/the-events-calendar.php',            
                    'opt_name'   => 'saswp-the-events-calendar', 
                    'part_in'    => 'pro',
        ),
        'event_organiser' => array(            
                    'name'       => 'Event Organiser',
                    'free'       => 'event-organiser/event-organiser.php',            
                    'opt_name'   => 'saswp-event-organiser',  
                    'part_in'    => 'pro',
        ),
        'modern_events_calendar' => array(            
                    'name'       => 'Modern Events Calendar Lite',
                    'free'       => 'modern-events-calendar-lite/modern-events-calendar-lite.php',            
                    'opt_name'   => 'saswp-modern-events-calendar', 
                    'part_in'    => 'pro',
        ),
        'wp_event_manager' => array(            
                    'name'       => 'WP Event Manager',
                    'free'       => 'wp-event-manager/wp-event-manager.php',            
                    'opt_name'   => 'saswp-wp-event-manager', 
                    'part_in'    => 'pro',
        ),
        'events_manager' => array(            
                    'name'       => 'Events Manager',
                    'free'       => 'events-manager/events-manager.php',            
                    'opt_name'   => 'saswp-events-manager', 
                    'part_in'    => 'pro',
        ),
        'event_calendar_wd' => array(            
                    'name'       => 'Event Calendar WD',
                    'free'       => 'event-calendar-wd/ecwd.php',            
                    'opt_name'   => 'saswp-event-calendar-wd',
                    'part_in'    => 'pro',
        ),
        'dw_qna' => array(            
                    'name'       => 'DW Question Answer',
                    'free'       => 'dw-question-answer/dw-question-answer.php',
                    'pro'        => 'dw-question-answer-pro/dw-question-answer.php',
                    'opt_name'   => 'saswp-dw-question-answer',
                    'part_in'    => 'free',
        ),
        'yoast_seo' => array(            
                    'name'       => 'Yoast Seo',
                    'free'       => 'wordpress-seo/wp-seo.php',
                    'pro'        => 'wordpress-seo-premium/wp-seo-premium.php',
                    'opt_name'   => 'saswp-yoast', 
                    'part_in'    => 'free',
        ),
        'rank_math' => array(            
                    'name'       => 'Rank Math',
                    'free'       => 'seo-by-rank-math/rank-math.php',   
                    'pro'        => 'seo-by-rank-math-premium/rank-math-premium.php',   
                    'opt_name'   => 'saswp-rankmath',
                    'part_in'    => 'free',
        ),
        'smart_crawl' => array(            
                    'name'       => 'SmartCrawl Seo',
                    'free'       => 'smartcrawl-seo/wpmu-dev-seo.php',               
                    'opt_name'   => 'saswp-smart-crawl', 
                    'part_in'    => 'free',
        ),
        'the_seo_framework' => array(            
                    'name'       => 'The SEO Framework',
                    'free'       => 'autodescription/autodescription.php',               
                    'opt_name'   => 'saswp-the-seo-framework', 
                    'part_in'    => 'free',
        ),
        'seo_press' => array(            
                    'name'       => 'SEOPress',
                    'free'       => 'wp-seopress/seopress.php',               
                    'opt_name'   => 'saswp-seo-press',
                    'part_in'    => 'free',
        ),
        'aiosp' => array(            
                    'name'       => 'All in One SEO Pack',
                    'free'       => 'all-in-one-seo-pack/all_in_one_seo_pack.php',               
                    'opt_name'   => 'saswp-aiosp',
                    'part_in'    => 'free',
        ),
        'squirrly_seo' => array(            
                    'name'       => 'Squirrly SEO',
                    'free'       => 'squirrly-seo/squirrly.php',               
                    'opt_name'   => 'saswp-squirrly-seo',
                    'part_in'    => 'free',
        ),        
        'wp_recipe_maker' => array(            
                    'name'       => 'WP Recipe Maker',
                    'free'       => 'wp-recipe-maker/wp-recipe-maker.php',               
                    'opt_name'   => 'saswp-wp-recipe-maker',
                    'part_in'    => 'free',
        ),
        'wp_ultimate_recipe' => array(            
                    'name'       => 'WP Ultimate Recipe',
                    'free'       => 'wp-ultimate-recipe/wp-ultimate-recipe.php', 
                    'pro'        => 'wp-ultimate-recipe-premium/wp-ultimate-recipe-premium.php', 
                    'opt_name'   => 'saswp-wp-ultimate-recipe',
                    'part_in'    => 'pro',
        ),
        'zip_recipes' => array(            
                    'name'       => 'Zip Recipes',
                    'free'       => 'zip-recipes/zip-recipes.php',                     
                    'opt_name'   => 'saswp-zip-recipes',
                    'part_in'    => 'pro',
        ),
        'mediavine_create' => array(            
                    'name'       => 'Create by Mediavine',
                    'free'       => 'mediavine-create/mediavine-create.php',                     
                    'opt_name'   => 'saswp-mediavine-create',
                    'part_in'    => 'pro',
        ),
        'ht_recipes' => array(            
                    'name'       => 'HT Recipes',
                    'free'       => 'ht-recipes/ht-recipes.php',                     
                    'opt_name'   => 'saswp-ht-recipes',
                    'part_in'    => 'pro',
        )    
                
    ),
    'themes' => array(
        'soledad' => array(            
                    'name'       => 'Soledad Theme',
                    'free'       => 'soledad',               
                    'opt_name'   => 'saswp-soledad',
                    'part_in'    => 'free',
        ),
        'jannah' => array(            
                    'name'       => 'Jannah',
                    'free'       => 'jannah',               
                    'opt_name'   => 'saswp-tagyeem',
                    'part_in'    => 'free',
        ),
        'extra' => array(            
                    'name'       => 'Extra',
                    'free'       => 'Extra',               
                    'opt_name'   => 'saswp-extra',
                    'part_in'    => 'free',
        ),
        'saswp_homeland' => array(            
                    'name'       => 'HomeLand Theme',
                    'free'       => 'homeland',               
                    'opt_name'   => 'saswp-homeland',
                    'part_in'    => 'pro',
        ),
        'saswp_realhomes' => array(            
                    'name'       => 'RealHomes Theme',
                    'free'       => 'realhomes',               
                    'opt_name'   => 'saswp-realhomes',
                    'part_in'    => 'pro',
        ),
        'wpresidence' => array(            
                    'name'       => 'WP Residence',
                    'free'       => 'wpresidence',               
                    'opt_name'   => 'saswp-wpresidence',
                    'part_in'    => 'pro',
        ),
    )
);