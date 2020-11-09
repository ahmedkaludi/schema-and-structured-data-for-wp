<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class SASWP_Gutenberg_Render {
    
    public function qanda_block_data($attributes){
        
         $response          = '';
         $accepted_answers  = '';
         $suggested_answers = '';        
         $question          = '';

         $question  = '<div class="saswp-qanda-block-question">
                <h3>'.esc_html($attributes['question_name']).'</h3>
                <span class="saswp-qand-date">'.esc_html($attributes['question_date_created']).' '.esc_html($attributes['question_time_created']).' '.saswp_t_string( 'by' ).' '.esc_html($attributes['question_author']).'</span>                
                <p>'.esc_html($attributes['question_text']).'</p>
                '. saswp_t_string( 'Vote' ).' <span class="dashicons dashicons-thumbs-up"></span> ('.esc_html($attributes['question_up_vote']).')
                </div>';
                
                if(isset($attributes['accepted_answers']) && !empty($attributes['accepted_answers'])){

                    foreach($attributes['accepted_answers'] as $answer){

                        $accepted_answers .= '<li>
                        <a href="'.esc_url($answer['url']).'">
                        <p>'.esc_html($answer['text']).'</p>                        
                        </a>
                        <span class="saswp-qand-date">'.esc_html($answer['date_created']).' '.esc_html($answer['time_created']).' by <strong>'.esc_html($answer['author']).'</strong></span>                        
                        <br> '. saswp_t_string( 'Vote' ).' <span class="dashicons dashicons-thumbs-up"></span> ('.esc_html($answer['vote']).')
                        
                        </li>';
                       
                    }

                }

                if(isset($attributes['suggested_answers']) && !empty($attributes['suggested_answers'])){

                    foreach($attributes['suggested_answers'] as $answer){

                        $suggested_answers .= '<li>
                        <a href="'.esc_url($answer['url']).'">
                        <p>'.esc_html($answer['text']).'</p>                        
                        </a>
                        <span class="saswp-qand-date">'.esc_html($answer['date_created']).' '.esc_html($answer['time_created']).' by <strong>'.esc_html($answer['author']).'</strong></span>                        
                        <br> '. saswp_t_string( 'Vote' ).' <span class="dashicons dashicons-thumbs-up"></span> ('.esc_html($answer['vote']).')                        
                        </li>';                       
                    }

                }
              //Escaping has been done above for all below html  
        $response = '<div class="saswp-qanda-block-html">
        '.$question.'
        <div class="saswp-qanda-block-answer"><h3>'. saswp_t_string( 'Accepted Answers' ).'</h3>'.$accepted_answers.'</div>
        <div class="saswp-qanda-block-answer"><h3>'. saswp_t_string( 'Suggested Answers' ) .'</h3>'.$suggested_answers.'</div>
        </div>';
                
        return $response;
    }
    public function event_block_data($attributes){
        
        $response       = '';
        $org_html       = '';
        $performer_html = '';
                
                if(isset($attributes['organizers']) && !empty($attributes['organizers'])){

                    foreach($attributes['organizers'] as $org){

                       $org_html .= '<div class="saswp-event-organiser"><span>'.esc_html($org['name']).'</span><br>';
                       $org_html .= '<strong>'.saswp_t_string('Phone').' : </strong><span>'.esc_html($org['phone']).'</span><br>';
                       $org_html .= '<strong>'.saswp_t_string('Email').' : </strong><span>'.esc_html($org['email']).'</span><br>';
                       $org_html .= '<strong>'.saswp_t_string('Website').' : </strong> <span>'.esc_html($org['website']).'</span></div>';

                    }

                }
                
                if(isset($attributes['performers']) && !empty($attributes['performers'])){

                    foreach($attributes['performers'] as $org){

                       $performer_html .= '<div class="saswp-event-organiser"><span>'.esc_html($org['name']).'</span><br>';
                       $performer_html .= '<strong>'.saswp_t_string('URL').' : </strong><span><a href="'.esc_url($org['url']).'">'.esc_url($org['url']).'</a></span><br>';
                       $performer_html .= '<strong>'.saswp_t_string('Email').' : </strong><span>'.esc_html($org['email']).'</span><br>';                       

                    }

                }

        $previous_date = '';

        if(isset($attributes['event_status']) && $attributes['event_status'] == 'EventRescheduled' && isset($attributes['previous_date'])){

            $previous_date = '<strong>'.saswp_t_string('Previous Date').' : </strong> <span>'.esc_html($attributes['previous_date']).'</span>'
                            . (!isset($attributes['all_day']) ?  '<span> ,'.esc_html($attributes['previous_time']).'</span><br>' : '<br>');        

        }                
        
        $response   .= '<div class="saswp-event-wrapper">'
                    . (isset($attributes['description']) ? '<p>'.$attributes['description'].'</p>' : '')
                    . '<div class="saswp-event-dates">'
                    . '<h5>'.saswp_t_string('Event Details').'</h5>'
                    . '<strong>'.saswp_t_string('Start Date').' : </strong> <span>'.esc_html($attributes['start_date']).'</span>'
                    . (!isset($attributes['all_day']) ?  '<span> ,'.esc_html($attributes['start_time']).'</span><br>' : '<br>')
                    . '<strong>'.saswp_t_string('End Date').' : </strong> <span>'.esc_html($attributes['end_date']).'</span>'
                    . (!isset($attributes['all_day']) ?  '<span> ,'.esc_html($attributes['end_time']).'</span><br>' : '<br>')                    
                    . $previous_date
                    . ($attributes['website'] ? '<strong>'.saswp_t_string('Website').' : </strong> <span><a href="'.esc_url($attributes['website']).'">'.esc_url($attributes['website']).'</a></span><br>' : '')
                    . ($attributes['price'] ? '<strong>'.saswp_t_string('Price').' : </strong> <span>'.esc_html($attributes['price']).' '. (isset($attributes['currency_code']) ? esc_html($attributes['currency_code']) : 'USD').'</span><br>' : '')
                    . ($attributes['attendance_mode'] ? '<strong>'.saswp_t_string('Attendance Mode').' : </strong> <span>'.esc_html($attributes['attendance_mode']).'</span><br>' : '')
                    . ($attributes['event_status'] ? '<strong>'.saswp_t_string('Status').' : </strong> <span>'.esc_html($attributes['event_status']).'</span>' : '')
                    . (isset($attributes['all_day']) ?  '<div>'.saswp_t_string('This event is all day').'</div>' : '')
                    . '</div>'
                
                    . '<div class="saswp-event-venue-details">'
                    . (($attributes['venue_name'] || $attributes['venue_address']) ? '<h5>'.saswp_t_string('Venue').'</h5>' : '')
                    . ($attributes['venue_name'] ? '<span>'.esc_html($attributes['venue_name']).'</span><br><br>' : '')
                    . ($attributes['venue_address'] ? '<span>'.esc_html($attributes['venue_address']).'</span>, ': '')
                    . ($attributes['venue_city'] ? '<span>'.esc_html($attributes['venue_city']).'</span>, <br>': '')                    
                    . ($attributes['venue_state'] ? '<span>'.esc_html($attributes['venue_state']).'</span> ': '')
                    . ($attributes['venue_postal_code'] ? '<span>'.esc_html($attributes['venue_postal_code']).'</span>, ': '')
                    . ($attributes['venue_country'] ? '<span>'.esc_html($attributes['venue_country']).'</span><br>': '');
                    if($attributes['venue_phone']){
                        $response.= '<strong>'.saswp_t_string('Phone').' : </strong><span>'.esc_html($attributes['venue_phone']).'</span>';
                    }                       
                    $response.= '</div>'                                    
                    . '<div class="saswp-event-organizers-details">'
                    . '<h5>'.saswp_t_string('Organizers').'</h5>'                    
                    . $org_html
                    . '</div>'                
                    . '<div class="saswp-event-performers-details">'
                    . '<h5>'.saswp_t_string('Performers').'</h5>'                    
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
                        $response .=  '<a target="_blank" class="saswp-job-company-website" href="'.esc_url($attributes['company_website']).'"><span class="dashicons dashicons-admin-links"></span> '.saswp_t_string('Website').'</a>';
                    }
                    if($attributes['company_twitter']){
                        $response .= '<a target="_blank" class="saswp-job-company-twitter" href="'.esc_url($attributes['company_twitter']).'"><span class="dashicons dashicons-twitter"></span> '.saswp_t_string('Twitter').'</a>';
                    }
                    if($attributes['company_facebook']){
                        $response .= '<a target="_blank" class="saswp-job-company-facebook" href="'.esc_url($attributes['company_facebook']).'"><span class="dashicons dashicons-facebook-alt"></span>'.saswp_t_string('Facebook').'</a>';
                    }
                             
                    $response .= '<strong>'.esc_html($attributes['company_name']).'</strong>'
                    . '</p>'
                    . '<p class="saswp-job-company-tagline">'.esc_html($attributes['company_tagline']).'</p>';
                    
                    if($attributes['base_salary']){
                        $response .= '<p><strong>'.saswp_t_string('Base Salary').': </strong> <span>'.esc_html($attributes['base_salary']).' '.esc_html($attributes['currency_code']).' '.saswp_t_string('per').' '.esc_html($attributes['unit_text']).'</span> <p>';
                    }
             
                    $response.= '</div>'
                    . '<div class="saswp-job-description">'
                    . esc_html($attributes['job_description'])
                    . '</div>'
                    . '<div class="saswp-job-application">'
                    . '<div class="saswp-job-application-details">';
                    
                    if($attributes['app_email_or_website']){
                        $response.= saswp_t_string('To apply for this job').' <strong>'.esc_html($attributes['app_email_or_website']).'</strong> '
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
                      . '<h3 class="saswp-course-detail">'.saswp_t_string('Course Details').'</h3>'
                      . '<h5>'.esc_html($course['name']).'</h5>'
                      . '<p>';
            if($course['image_url']){
                $response .='<img src="'.esc_url($course['image_url']).'">';
            }          
            $response .= ''.esc_html($course['description']).'</p>'
                      . '<h5>'.saswp_t_string('Provider Details').'</h5>'
                      . '<div><strong>'.saswp_t_string('Provider Name').'</strong> : '. esc_html($course['provider_name']). '</div>'
                      . '<div><strong>'.saswp_t_string('Provider Website').'</strong> : '. '<a href="'.esc_url($course['provider_website']).'">'.esc_url($course['provider_website']).'</a></div>'                    
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