<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class SASWP_Gutenberg_Render {
    
    public function event_block_data($attributes){
        
        $response       = '';
        $org_html       = '';
        $performer_html = '';
                
                if(isset($attributes['organizers']) && !empty($attributes['organizers'])){

                    foreach($attributes['organizers'] as $org){

                       $org_html .= '<div class="saswp-event-organiser"><span>'.esc_html($org['name']).'</span><br>';
                       $org_html .= '<strong>'.esc_html__('Phone', 'schema-and-structured-data-for-wp').' : </strong><span>'.esc_html($org['phone']).'</span><br>';
                       $org_html .= '<strong>'.esc_html__('Email', 'schema-and-structured-data-for-wp').' : </strong><span>'.esc_html($org['email']).'</span><br>';
                       $org_html .= '<strong>'.esc_html__('Website', 'schema-and-structured-data-for-wp').' : </strong> <span>'.esc_html($org['website']).'</span></div>';

                    }

                }
                
                if(isset($attributes['performers']) && !empty($attributes['performers'])){

                    foreach($attributes['performers'] as $org){

                       $performer_html .= '<div class="saswp-event-organiser"><span>'.esc_html($org['name']).'</span><br>';
                       $performer_html .= '<strong>'.esc_html__('URL', 'schema-and-structured-data-for-wp').' : </strong><span><a href="'.esc_url($org['url']).'">'.esc_url($org['url']).'</a></span><br>';
                       $performer_html .= '<strong>'.esc_html__('Email', 'schema-and-structured-data-for-wp').' : </strong><span>'.esc_html($org['email']).'</span><br>';                       

                    }

                }

        $previous_date = '';

        if(isset($attributes['event_status']) && $attributes['event_status'] == 'EventRescheduled' && isset($attributes['previous_date'])){

            $previous_date = '<strong>'.esc_html__('Previous Date', 'schema-and-structured-data-for-wp').' : </strong> <span>'.esc_html($attributes['previous_date']).'</span>'
                            . (!isset($attributes['all_day']) ?  '<span> ,'.esc_html($attributes['previous_time']).'</span><br>' : '<br>');        

        }                
        
        $response   .= '<div class="saswp-event-wrapper">'
                    . (isset($attributes['description']) ? '<p>'.$attributes['description'].'</p>' : '')
                    . '<div class="saswp-event-dates">'
                    . '<h5>'.esc_html__('Event Details', 'schema-and-structured-data-for-wp').'</h5>'
                    . '<strong>'.esc_html__('Start Date', 'schema-and-structured-data-for-wp').' : </strong> <span>'.esc_html($attributes['start_date']).'</span>'
                    . (!isset($attributes['all_day']) ?  '<span> ,'.esc_html($attributes['start_time']).'</span><br>' : '<br>')
                    . '<strong>'.esc_html__('End Date', 'schema-and-structured-data-for-wp').' : </strong> <span>'.esc_html($attributes['end_date']).'</span>'
                    . (!isset($attributes['all_day']) ?  '<span> ,'.esc_html($attributes['end_time']).'</span><br>' : '<br>')                    
                    . $previous_date
                    . ($attributes['website'] ? '<strong>'.esc_html__('Website', 'schema-and-structured-data-for-wp').' : </strong> <span><a href="'.esc_url($attributes['website']).'">'.esc_url($attributes['website']).'</a></span><br>' : '')
                    . ($attributes['price'] ? '<strong>'.esc_html__('Price', 'schema-and-structured-data-for-wp').' : </strong> <span>'.esc_html($attributes['price']).' '. (isset($attributes['currency_code']) ? esc_html($attributes['currency_code']) : 'USD').'</span><br>' : '')
                    . ($attributes['attendance_mode'] ? '<strong>'.esc_html__('Attendance Mode', 'schema-and-structured-data-for-wp').' : </strong> <span>'.esc_html($attributes['attendance_mode']).'</span><br>' : '')
                    . ($attributes['event_status'] ? '<strong>'.esc_html__('Status', 'schema-and-structured-data-for-wp').' : </strong> <span>'.esc_html($attributes['event_status']).'</span>' : '')
                    . (isset($attributes['all_day']) ?  '<div>'.esc_html__('This event is all day', 'schema-and-structured-data-for-wp').'</div>' : '')
                    . '</div>'
                
                    . '<div class="saswp-event-venue-details">'
                    . (($attributes['venue_name'] || $attributes['venue_address']) ? '<h5>'.esc_html__('Venue', 'schema-and-structured-data-for-wp').'</h5>' : '')
                    . ($attributes['venue_name'] ? '<span>'.esc_html($attributes['venue_name']).'</span><br><br>' : '')
                    . ($attributes['venue_address'] ? '<span>'.esc_html($attributes['venue_address']).'</span>, ': '')
                    . ($attributes['venue_city'] ? '<span>'.esc_html($attributes['venue_city']).'</span>, <br>': '')                    
                    . ($attributes['venue_state'] ? '<span>'.esc_html($attributes['venue_state']).'</span> ': '')
                    . ($attributes['venue_postal_code'] ? '<span>'.esc_html($attributes['venue_postal_code']).'</span>, ': '')
                    . ($attributes['venue_country'] ? '<span>'.esc_html($attributes['venue_country']).'</span><br>': '');
                    if($attributes['venue_phone']){
                        $response.= '<strong>'.esc_html__('Phone', 'schema-and-structured-data-for-wp').' : </strong><span>'.esc_html($attributes['venue_phone']).'</span>';
                    }                       
                    $response.= '</div>'                                    
                    . '<div class="saswp-event-organizers-details">'
                    . '<h5>'.esc_html__('Organizers', 'schema-and-structured-data-for-wp').'</h5>'                    
                    . $org_html
                    . '</div>'                
                    . '<div class="saswp-event-performers-details">'
                    . '<h5>'.esc_html__('Performers', 'schema-and-structured-data-for-wp').'</h5>'                    
                    . $performer_html
                    . '</div>'        
                    . '</div>';
                
        return $response;
    }
    
    public function job_block_data($attributes){
                        
        $response = $location = '';
       
        if($attributes){
 
            if($attributes['location_address']){
                $location .= $attributes['location_address']. ', <br>'; 
            }
            if($attributes['location_city']){
                $location .= $attributes['location_city']. ', ';
            }
            if($attributes['location_state']){
                $location .= $attributes['location_state']. ', <br>';
            }
            if($attributes['location_country']){
                $location .= $attributes['location_country']. ', ';
            }
            if($attributes['location_postal_code']){
                $location .= $attributes['location_postal_code']. ', ';
            }
                                          
         $response  .='<div class="saswp-job-listing-wrapper">'                    
                    . '<ul class="saswp-job-listing-meta">'
                    . '<li class="saswp-location"><span class="dashicons dashicons-location"></span><a target="_blank" href="'.esc_url( 'https://maps.google.com/maps?q=' . rawurlencode( wp_strip_all_tags( $location ) ) . '&zoom=14&size=512x512&maptype=roadmap&sensor=false' ).'" class="saswp-google-map-link">'. $location .'</a></li>'
                    . '<li class="saswp-date-posted"><span class="dashicons dashicons-calendar-alt"></span> '.get_the_date("Y-m-d").'</li>'
                    . '</ul>'
                    . '<div class="saswp-job-company">';
         if($attributes['company_logo_url']){
             $response.= '<img src="'.esc_url($attributes['company_logo_url']).'">';
         }
         
         $response.= '<p class="saswp-job-company-name">';
         
                    if($attributes['company_website']){
                        $response .=  '<a target="_blank" class="saswp-job-company-website" href="'.esc_url($attributes['company_website']).'"><span class="dashicons dashicons-admin-links"></span> '.esc_html__('Website', 'schema-and-structured-data-for-wp').'</a>';
                    }
                    if($attributes['company_twitter']){
                        $response .= '<a target="_blank" class="saswp-job-company-twitter" href="'.esc_url($attributes['company_twitter']).'"><span class="dashicons dashicons-twitter"></span> '.esc_html__('Twitter', 'schema-and-structured-data-for-wp').'</a>';
                    }
                    if($attributes['company_facebook']){
                        $response .= '<a target="_blank" class="saswp-job-company-facebook" href="'.esc_url($attributes['company_facebook']).'"><span class="dashicons dashicons-facebook-alt"></span>'.esc_html__('Facebook', 'schema-and-structured-data-for-wp').'</a>';
                    }
                             
                    $response .= '<strong>'.esc_html($attributes['company_name']).'</strong>'
                    . '</p>'
                    . '<p class="saswp-job-company-tagline">'.esc_html($attributes['company_tagline']).'</p>';
                    
                    if($attributes['base_salary']){
                        $response .= '<p><strong>'.esc_html__('Base Salary', 'schema-and-structured-data-for-wp').': </strong> <span>'.esc_html($attributes['base_salary']).' '.esc_html($attributes['currency_code']).' '.esc_html__('per', 'schema-and-structured-data-for-wp').' '.esc_html($attributes['unit_text']).'</span> <p>';
                    }
             
                    $response.= '</div>'
                    . '<div class="saswp-job-description">'
                    . esc_html($attributes['job_description'])
                    . '</div>'
                    . '<div class="saswp-job-application">'
                    . '<div class="saswp-job-application-details">';
                    
                    if($attributes['app_email_or_website']){
                        $response.= esc_html__('To apply for this job', 'schema-and-structured-data-for-wp').' <strong>'.esc_html($attributes['app_email_or_website']).'</strong> '
                       . '<a href="mailto:'.esc_attr($attributes['app_email_or_website']).'">'.esc_attr($attributes['app_email_or_website']).'</a>';
                    }
                                                            
                    $response.= '</div>'
                    . '</div>'
                    . '</div>';   
            
        }
        
        return $response;
    }
    
    public function course_block_data($attributes){
        
        $response = '';
        
        if(isset($attributes['courses'])){
                        
          foreach($attributes['courses'] as $course){
            
            $response .= '<div class="saswp-course-loop">'
                      . '<h3 class="saswp-course-detail">'.esc_html__('Course Details', 'schema-and-structured-data-for-wp').'</h3>'
                      . '<h5>'.esc_html($course['name']).'</h5>'
                      . '<p>';
            if($course['image_url']){
                $response .='<img src="'.esc_url($course['image_url']).'">';
            }          
            $response .= ''.esc_html($course['description']).'</p>'
                      . '<h5>'.esc_html__('Provider Details', 'schema-and-structured-data-for-wp').'</h5>'
                      . '<div><strong>'.esc_html__('Provider Name', 'schema-and-structured-data-for-wp').'</strong> : '. esc_html($course['provider_name']). '</div>'
                      . '<div><strong>'.esc_html__('Provider Website', 'schema-and-structured-data-for-wp').'</strong> : '. '<a href="'.esc_url($course['provider_website']).'">'.esc_url($course['provider_website']).'</a></div>'                    
                      . '</div>';  

            }  
                        
        }
        
        return $response;        
    }
    
    public function collection_block_data($attributes){
                
        $collection_id = null; 
        
        if(isset($attributes['id'])){            
            $collection_id = $attributes['id'];                        
        }else{
             $review_service = new saswp_reviews_service();
             $col_opt  = $review_service->saswp_get_collection_list(1);
             if(isset($col_opt[0]['value'])){
                 $collection_id = $col_opt[0]['value'];
             }
        }
                
        return do_shortcode('[saswp-reviews-collection id="'.$collection_id.'"]');
        
    }
    
}