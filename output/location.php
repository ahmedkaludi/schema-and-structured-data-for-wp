<?php

class SASWP_Location_Widget extends WP_Widget {
                 
/**
 * Register widget with WordPress.
 */
function __construct() {
                               
    parent::__construct(
        'saswp_location_widget', // Base ID
        saswp_t_string('SASWP Location'), // Name
        array( 'description' => saswp_t_string('Widget to display location') ) // Args
    );
}

/**
 * Front-end display of widget.
 *
 * @see WP_Widget::widget()
 *
 * @param array $args     Widget arguments.
 * @param array $instance Saved values from database.
 */
public function widget( $args, $instance ) {
                      
    echo html_entity_decode(esc_attr($args['before_widget']));
                            
    
    $all_loc = saswp_get_location_list();    
    
    foreach($all_loc as $ad){
        
        if($ad['value'] == $instance['loc']){   
                            
            echo saswp_add_location_content($instance['loc']);
                
        }   
        
    }
    
    echo html_entity_decode(esc_attr($args['after_widget']));		
}

/**
 * Back-end widget form.
 *
 * @see WP_Widget::form()
 *
 * @param array $instance Previously saved values from database.
 */
public function form( $instance ) {
            
    $loc = ! empty( $instance['loc'] ) ? $instance['loc'] : saswp_t_string('Widget to display location');?>

    <p><label for="<?php echo esc_attr( $this->get_field_id( 'loc' ) ); ?>"><?php saswp_t_string('Locations'); ?></label><?php 
    
    $loc_select_html = '';
            
    $all_loc = saswp_get_location_list();    
    
    $loc_select_html .= '<option value="">'.saswp_t_string('Select Location').'</option>';

    foreach($all_loc as $ad){
     
        $loc_select_html .='<option '. esc_attr(selected( $loc, $ad['value'], false)).' value="'.esc_attr($ad['value']).'">'.esc_html($ad['label']).'</option>';
     
    }
    
    $allow_html = saswp_expanded_allowed_tags();

    echo '<select id="'.esc_attr( $this->get_field_id( 'loc' )).'" name="'.esc_attr( $this->get_field_name( 'loc' )).'">'
         .wp_kses($loc_select_html, $allow_html)
         . '</select>';
    ?></p><?php 
}

/**
 * Sanitize widget form values as they are saved.
 *
 * @see WP_Widget::update()
 *
 * @param array $new_instance Values just sent to be saved.
 * @param array $old_instance Previously saved values from database.
 *
 * @return array Updated safe values to be saved.
 */
public function update( $new_instance, $old_instance ) {
    $instance = array();                
    
    $instance['loc'] = ( ! empty( $new_instance['loc'] ) ) ? sanitize_text_field( $new_instance['loc'] ) : '';                                
    return $instance;
}

} // class SASWP_Location_Widget

function saswp_register_location_widget(){
    register_widget('SASWP_Location_Widget');
}
add_action('widgets_init', 'saswp_register_location_widget');

function saswp_get_location_list(){

            $response  = array();
        
            $schema_id_array = json_decode(get_transient('saswp_transient_schema_ids'), true); 
        
            if(!$schema_id_array){
                
                $schema_id_array = saswp_get_saved_schema_ids();
                
            } 
            
            if($schema_id_array){

                $col_opt = array(); 

                foreach($schema_id_array as $col){

                    $schema_type   = get_post_meta($col, 'schema_type', true);
                    $display_front = get_post_meta($col, 'saswp_loc_display_on_front', true);

                    if( $schema_type == 'local_business' && $display_front ){
                        $col_opt[] = array(
                            'value' => $col,
                            'label' => get_the_title($col)
                        );
                    }
                   
                }

               $response  = $col_opt;

            }
          return $response; 

}

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