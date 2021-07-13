<?php

add_shortcode( 'saswp-location', 'saswp_location_shortcode_render');

function saswp_location_shortcode_render($attr){

    if(isset($attr['id'])){
        echo saswp_add_location_content($attr['id']);
    }
    
}

function saswp_add_location_content( $post_id ){
    
    $post_meta = get_post_meta($post_id);
    
    $html  = '<div class="saswp-location-container">';

    if( !empty($post_meta['local_business_name_'.$post_id][0]) ){
        $html .= '<h2>'.esc_html($post_meta['local_business_name_'.$post_id][0]).'</h2>';
    }

    $html .= '<div class="saswp-location-address-wrapper">';
                        
    if( !empty($post_meta['local_street_address_'.$post_id][0]) ){
        $html .= '<span>'.esc_html($post_meta['local_street_address_'.$post_id][0]).'</span>';  
    }
    if( !empty($post_meta['local_city_'.$post_id][0]) ){
        $html .= '<span> '.esc_html($post_meta['local_city_'.$post_id][0]).'</span>';    
    }
    if( !empty($post_meta['local_state_'.$post_id][0]) ){
        $html .= '<span> '.esc_html($post_meta['local_state_'.$post_id][0]).'</span>';   
    }
    if( !empty($post_meta['local_postal_code_'.$post_id][0]) ){
        $html .= '<span> '.esc_html($post_meta['local_postal_code_'.$post_id][0]).'</span>';   
    }
    
    $html .= '</div>';
                  
    if( !empty($post_meta['local_phone_'.$post_id][0]) ){
        $html .= '<div>Phone : '.esc_html($post_meta['local_phone_'.$post_id][0]).'</div>';
    }
    if( !empty($post_meta['local_website_'.$post_id][0]) ){
        $html .= '<div>Website : '.esc_html($post_meta['local_website_'.$post_id][0]).'</div>';
    }
    if( !empty($post_meta['local_price_range_'.$post_id][0]) ){
        $html .= '<div>Price indication : '.esc_html($post_meta['local_price_range_'.$post_id][0]).'</div>';
    }
    if( !empty($post_meta['local_facebook_'.$post_id][0]) ){
        $html .= '<div>Facebook : '.esc_html($post_meta['local_facebook_'.$post_id][0]).'</div>';
    }
    if( !empty($post_meta['local_twitter_'.$post_id][0]) ){
        $html .= '<div>Twitter : '.esc_html($post_meta['local_twitter_'.$post_id][0]).'</div>';
    }
    if( !empty($post_meta['local_instagram_'.$post_id][0]) ){
        $html .= '<div>Instagram : '.esc_html($post_meta['local_instagram_'.$post_id][0]).'</div>';
    }
    if( !empty($post_meta['local_pinterest_'.$post_id][0]) ){
        $html .= '<div>Pinterest : '.esc_html($post_meta['local_pinterest_'.$post_id][0]).'</div>';
    }
    if( !empty($post_meta['local_linkedin_'.$post_id][0]) ){
        $html .= '<div>Linkedin : '.esc_html($post_meta['local_linkedin_'.$post_id][0]).'</div>';
    }
    if( !empty($post_meta['local_soundcloud_'.$post_id][0]) ){
        $html .= '<div>Soundcloud : '.esc_html($post_meta['local_soundcloud_'.$post_id][0]).'</div>';
    }
    if( !empty($post_meta['local_tumblr_'.$post_id][0]) ){
        $html .= '<div>Tumblr : '.esc_html($post_meta['local_tumblr_'.$post_id][0]).'</div>';
    }
    if( !empty($post_meta['local_youtube_'.$post_id][0]) ){
        $html .= '<div>Youtube : '.esc_html($post_meta['local_youtube_'.$post_id][0]).'</div>';
    }    
            
    if(!empty($post_meta['saswp_dayofweek_'.$post_id][0])){

            $short_days = array('Monday'      => 'Mo',
                                'Tuesday'      => 'Tu', 
                                'Wednesday'    => 'We', 
                                'Thursday'     => 'Th', 
                                'Friday'       => 'Fr', 
                                'Saturday'     => 'Sa', 
                                'Sunday'       => 'Su');

            $operation_days = explode(PHP_EOL, $post_meta['saswp_dayofweek_'.$post_id][0]);

            $op_tr = '';

            foreach ( $short_days as $key => $value ) {
                
                $s_key = null;

                foreach ( $operation_days as $okey => $oval ) {
                    
                    if(strpos(strtolower($oval), strtolower($value)) !== false){
                        $s_key = $okey;
                    }

                }
                        
                if($s_key){
                    $exploded = explode( ' ', $operation_days[$s_key] );
                }

                if( isset($exploded[1]) ){
                    $op_tr .= '<tr><td>'.esc_html($key).'</td><td>'.esc_html($exploded[1]).'</td></tr>';
                }
            }
            
            if($op_tr){

                $html.= '<table>';
                $html.= '<tbody>';
                $html.=  $op_tr;
                $html.= '</tbody>';
                $html.= '</table>';

            }

            if( !empty($post_meta['local_latitude_'.$post_id][0]) && !empty($post_meta['local_longitude_'.$post_id][0]) ){
                $html .= '<iframe src="https://maps.google.com/maps?q='.esc_attr($post_meta['local_latitude_'.$post_id][0]).', '.esc_attr($post_meta['local_longitude_'.$post_id][0]).'&z=15&output=embed" width="360" height="270" frameborder="0" style="border:0"></iframe>';
            }
            

    }                         

    $html .= '</div>';

    return $html;
}