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
        
        $response   = '<div class="saswp-event-wrapper">'
                
                    . '<div class="saswp-event-dates">'
                    . '<h5>'.esc_html__('Event Details', 'schema-and-structured-data-for-wp').'</h5>'
                    . '<strong>'.esc_html__('Start Date', 'schema-and-structured-data-for-wp').' : </strong> <span>'.esc_html($attributes['start_date']).'</span>'
                    . (!$attributes['all_day'] ?  '<span> ,'.esc_html($attributes['start_time']).'</span><br>' : '<br>')
                    . '<strong>'.esc_html__('End Date', 'schema-and-structured-data-for-wp').' : </strong> <span>'.esc_html($attributes['end_date']).'</span>'
                    . (!$attributes['all_day'] ?  '<span> ,'.esc_html($attributes['end_time']).'</span><br>' : '<br>')
                    . '<strong>'.esc_html__('Website', 'schema-and-structured-data-for-wp').' : </strong> <span><a href="'.esc_url($attributes['website']).'">'.esc_url($attributes['website']).'</a></span><br>'
                    . '<strong>'.esc_html__('Price', 'schema-and-structured-data-for-wp').' : </strong> <span>'.esc_html($attributes['price']).' '. ($attributes['currency_code'] ? esc_html($attributes['currency_code']) : 'USD').'</span>'
                    . ($attributes['all_day'] ?  '<div>'.esc_html__('This event is all day', 'schema-and-structured-data-for-wp').'</div>' : '')
                    . '</div>'
                
                    . '<div class="saswp-event-venue-details">'
                    . '<h5>'.esc_html__('Vanue', 'schema-and-structured-data-for-wp').'</h5>'
                    . '<span>'.esc_html($attributes['venue_name']).'</span><br><br>'
                    . '<span>'.esc_html($attributes['venue_address']).'</span>, '
                    . '<span>'.esc_html($attributes['venue_city']).'</span>, <br>'                    
                    . '<span>'.esc_html($attributes['venue_state']).'</span> '
                    . '<span>'.esc_html($attributes['venue_postal_code']).'</span>, '
                    . '<span>'.esc_html($attributes['venue_country']).'</span><br>'
                    . '<strong>'.esc_html__('Phone', 'schema-and-structured-data-for-wp').' : </strong><span>'.esc_html($attributes['venue_phone']).'</span>'
                    . '</div>'
                                    
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
    
}
