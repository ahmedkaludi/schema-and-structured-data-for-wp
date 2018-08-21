<?php
//Function to expand html tags form allowed html tags in wordpress    
function saswp_expanded_allowed_tags() {
            $my_allowed = wp_kses_allowed_html( 'post' );
            // form fields - input
            $my_allowed['input']  = array(
                    'class'        => array(),
                    'id'           => array(),
                    'name'         => array(),
                    'value'        => array(),
                    'type'         => array(),
                    'style'        => array(),
                    'placeholder'  => array(),
                    'maxlength'    => array(),
                    'checked'      => array(),
                    'readonly'     => array(),
                    'disabled'     => array(),
                    'width'        => array(),  
                    'data-id'      => array()
            );
            $my_allowed['hidden']  = array(                    
                    'id'           => array(),
                    'name'         => array(),
                    'value'        => array(),
                    'type'         => array(), 
                    'data-id'         => array(), 
            );
            //number
            $my_allowed['number'] = array(
                    'class'        => array(),
                    'id'           => array(),
                    'name'         => array(),
                    'value'        => array(),
                    'type'         => array(),
                    'style'        => array(),                    
                    'width'        => array(),                    
            ); 
            //textarea
             $my_allowed['textarea'] = array(
                    'class' => array(),
                    'id'    => array(),
                    'name'  => array(),
                    'value' => array(),
                    'type'  => array(),
                    'style'  => array(),
                    'rows'  => array(),                                                            
            );              
            // select
            $my_allowed['select'] = array(
                    'class'  => array(),
                    'id'     => array(),
                    'name'   => array(),
                    'value'  => array(),
                    'type'   => array(),                    
            );
            // checkbox
            $my_allowed['checkbox'] = array(
                    'class'  => array(),
                    'id'     => array(),
                    'name'   => array(),
                    'value'  => array(),
                    'type'   => array(),                    
            );
            //  options
            $my_allowed['option'] = array(
                    'selected' => array(),
                    'value' => array(),
            );                       
            // style
            $my_allowed['style'] = array(
                    'types' => array(),
            );
            return $my_allowed;
        }    
function saswp_admin_link($tab = '', $args = array()){
           
            $page = 'structured_data_options';
            if ( ! is_multisite() ) {
                    $link = admin_url( 'admin.php?page=' . $page );
            }
            else {
                    $link = network_admin_url( 'admin.php?page=' . $page );
            }

            if ( $tab ) {
                    $link .= '&tab=' . $tab;
            }

            if ( $args ) {
                    foreach ( $args as $arg => $value ) {
                            $link .= '&' . $arg . '=' . urlencode( $value );
                    }
            }

            return esc_url($link);
}
function saswp_get_tab( $default = '', $available = array() ) {

            $tab = isset( $_GET['tab'] ) ? sanitize_text_field(wp_unslash($_GET['tab'])) : $default;            
            if ( ! in_array( $tab, $available ) ) {
                    $tab = $default;
            }

            return $tab;
        }

add_action('plugins_loaded', 'saswp_defaultSettings' );

             $sd_data=array();                
function saswp_defaultSettings(){
            global $sd_data;    
            $current_user = wp_get_current_user();           
            $current_url = get_home_url();           
            $custom_logo_id = get_theme_mod( 'custom_logo' );
            $logo = wp_get_attachment_image_src( $custom_logo_id , 'full' );                            
            $defaults = array(
                    'saswp-for-wordpress' => 0,
                    'saswp-for-amp'  => 1, 
                    'saswp-for-wordpress'=>1,
                    'sd_post_type'=> 'Blogposting',
                    'sd_page_type'=> 'WebPage',
                    'saswp_kb_type' => 'Organization',    
                    'saswp_kb_contact_1' => 0,                    
                    'sd_name' => $current_user->user_login,   
                    'sd_alt_name' => $current_user->user_login,                                       
                    'sd-person-name' => $current_user->user_nicename,                    
                    'sd-person-url' => $current_url,                                                          
                    'saswp-logo-width' => '600',
                    'saswp-logo-height' => '60',
                    'sd_logo' => array(
                        'url'=>$logo[0],
                        'id'=>$custom_logo_id,
                        'height'=>$logo[2],
                        'width'=>$logo[1],
                        'thumbnail'=>$logo[0]        
                    ),
                    'sd-data-logo-ampforwp' => array(
                        'url'=>$logo[0],
                        'id'=>$custom_logo_id,
                        'height'=>$logo[2],
                        'width'=>$logo[1],
                        'thumbnail'=>$logo[0]        
                    ),
                    'sd_default_image' => array(
                        'url'=>$logo[0],
                        'id'=>$custom_logo_id,
                        'height'=>$logo[2],
                        'width'=>$logo[1],
                        'thumbnail'=>$logo[0]        
                    ),
                    'sd_default_image_width' =>$logo[1],
                    'sd_default_image_height' =>$logo[2]

            );	
            $sd_data = $settings = get_option( 'sd_data', $defaults);                     
            return $settings;
        }
