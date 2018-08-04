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

           $sd_data=array();
function saswp_defaultSettings(){
            global $sd_data;
            $defaults = array(
                    'saswp-for-wordpress' => 0,
                    'saswp-for-amp'  => 0,                
            );	
            $sd_data = $settings = get_option( 'sd_data', $defaults);                
            return $settings;
        }
saswp_defaultSettings();