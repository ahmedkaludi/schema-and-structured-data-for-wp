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
            $sd_name = 'default';
            $bloginfo = get_bloginfo('name', 'display'); 
            if($bloginfo){
            $sd_name =$bloginfo;
            }            
            $current_url = get_home_url();           
            $custom_logo_id = get_theme_mod( 'custom_logo' );
            $logo = wp_get_attachment_image_src( $custom_logo_id , 'full' );

            $user_id = get_current_user_id();
            $username = '';
            if($user_id>0){
                $user_info = get_userdata($user_id);
                $username = $user_info->data->display_name;
            }
            $defaults = array(
                    //General Block
                    'sd_about_page'     => '',
                    'sd_contact_page'   => '',         
                    //knowledge Block
                    'saswp_kb_type'     => 'Organization',    
                    'sd_name'           => $sd_name,   
                    'sd_alt_name'       => $sd_name,
                    'sd_url'            => $current_url,
                    'sd_logo'           => array(
                                'url'           =>$logo[0],
                                'id'            =>$custom_logo_id,
                                'height'        =>$logo[2],
                                'width'         =>$logo[1],
                                'thumbnail'     =>$logo[0]        
                            ),
                    'sd-person-name'    => $username,                    
                    'sd-person-job-title'=> '',
                    'sd-person-url'     => $current_url,
                    'sd-person-image'   => array(
                                'url'           =>'',
                                'id'            =>'',
                                'height'        =>'',
                                'width'         =>'',
                                'thumbnail'     =>'' ),
                    'sd-person-phone-number'=> '',
                    'saswp_kb_telephone'=> '',
                    'saswp_contact_type'=> '',
                    'saswp_kb_contact_1'=> 0,
                    //Social
                    'sd_facebook'=> '',
                    'sd_twitter'=> '',
                    'sd_google_plus'=> '',
                    'sd_instagram'=> '',
                    'sd_youtube'=> '',
                    'sd_linkedin'=> '',
                    'sd_pinterest'=> '',
                    'sd_soundcloud'=> '',
                    'sd_tumblr'=> '',


                    'sd-data-logo-ampforwp' => array(
                        'url'=>$logo[0],
                        'id'=>$custom_logo_id,
                        'height'=>$logo[2],
                        'width'=>$logo[1],
                        'thumbnail'=>$logo[0]        
                    ),

                    //AMP Block           
                    'saswp-for-amp'  => 1, 
                    'saswp-for-wordpress'=>1,      
                    'saswp-logo-width' => '60',
                    'saswp-logo-height' => '60',
                    
                    'sd_default_image' => array(
                        'url'=>$logo[0],
                        'id'=>$custom_logo_id,
                        'height'=>$logo[2],
                        'width'=>$logo[1],
                        'thumbnail'=>$logo[0]        
                    ),
                    'sd_default_image_width' =>$logo[1],
                    'sd_default_image_height' =>$logo[2],
                    
                    

            );	
            $sd_data = $settings = get_option( 'sd_data', $defaults);                     
            return $settings;
        }
