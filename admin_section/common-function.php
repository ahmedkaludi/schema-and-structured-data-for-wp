<?php

/**
     * We are here fetching all schema and its settings from schema plugin
     * note: Transaction is applied on this function, if any error occure all the data will be rollbacked
     * @global type $wpdb
     * @return boolean
     */
    function saswp_import_schema_plugin_data(){           
                                                    
        $schema_post = array();
        global $wpdb;
        $user_id = get_current_user_id();
        $all_schema_post = get_posts(
                    array(
                            'post_type' 	 => 'schema',                                                                                   
                            'posts_per_page' => -1,   
                            'post_status' => 'any',
                    )
                 );         
        if($all_schema_post){
            // begin transaction
            $wpdb->query('START TRANSACTION');
            foreach($all_schema_post as $schema){    
                
                $schema_post = array(
                    'post_author' => $user_id,
                    'post_date' => $schema->post_date,
                    'post_date_gmt' => $schema->post_date_gmt,
                    'post_content' => $schema->post_content,
                    'post_title' => $schema->post_title,
                    'post_excerpt' => $schema->post_excerpt,
                    'post_status' => $schema->post_status,
                    'comment_status' => $schema->comment_status,
                    'ping_status' => $schema->ping_status,
                    'post_password' => $schema->post_password,
                    'post_name' =>  $schema->post_name,
                    'to_ping' => $schema->to_ping,
                    'pinged' => $schema->pinged,
                    'post_modified' => $schema->post_modified,
                    'post_modified_gmt' => $schema->post_modified_gmt,
                    'post_content_filtered' => $schema->post_content_filtered,
                    'post_parent' => $schema->post_parent,                                        
                    'menu_order' => $schema->menu_order,
                    'post_type' => 'saswp',
                    'post_mime_type' => $schema->post_mime_type,
                    'comment_count' => $schema->comment_count,
                    'filter' => $schema->filter,                    
                );                                      
                $post_id = wp_insert_post($schema_post);
                $result = $post_id;
                $guid = get_option('siteurl') .'/?post_type=saswp&p='.$post_id;                
                $wpdb->get_results("UPDATE wp_posts SET guid ='".$guid."' WHERE ID ='".$post_id."'");   
                
                $schema_post_meta = get_post_meta($schema->ID, $key='', true ); 
                $schema_post_types = get_post_meta($schema->ID, $key='_schema_post_types', true );                  
                $schema_post_meta_box = get_post_meta($schema->ID, $key='_schema_post_meta_box', true );
                
                $data_group_array = array();
                if($schema_post_types){
                                        
                    $i=0;
                    foreach ($schema_post_types as $post_type){
                       
                       $data_group_array['group-'.$i] =array(
                          'data_array' => array(
                            array(
                            'key_1' => 'post_type',
                            'key_2' => 'equal',
                            'key_3' => $post_type,
                            )
                          )               
                         );                                               
                    $i++;  
                    
                    }                                        
                }                                
                $schema_type ='';
                $schema_article_type ='';                                                
                
                if(isset($schema_post_meta['_schema_type'])){
                  $schema_type = $schema_post_meta['_schema_type'];  
                }
                if(isset($schema_post_meta['_schema_article_type'])){
                  $schema_article_type = $schema_post_meta['_schema_article_type'][0];  
                }                      
                $saswp_meta_key = array(
                    'schema_type' => $schema_article_type,
                    'data_group_array'=>$data_group_array,
                    'imported_from' => 'schema'
                );
                
                foreach ($saswp_meta_key as $key => $val){                     
                    update_post_meta($post_id, $key, $val);  
                }                                                        
              }          
                            
              //Importing settings starts here
              
              
                $schema_plugin_options = get_option('schema_wp_settings');                                      
                $custom_logo_id = get_theme_mod( 'custom_logo' );
                $logo = wp_get_attachment_image_src( $custom_logo_id , 'full' );
                                
                $saswp_plugin_options = array(                    
                    'sd_logo'           => array(
                                'url'           =>$schema_plugin_options['logo'],  
                                'id'            =>$custom_logo_id,
                                'height'        =>'600',
                                'width'         =>'60',
                                'thumbnail'     =>$schema_plugin_options['logo']        
                            ),                                                                                                                                                             
                    'saswp_kb_contact_1'=> 0,                                                        
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
                    'sd_initial_wizard_status' =>1,
                                        
               );
                
                if(isset($schema_plugin_options['facebook'])){
                  $saswp_plugin_options['sd_facebook'] =  $schema_plugin_options['facebook']; 
                  $saswp_plugin_options['saswp-facebook-enable'] =  1; 
                }
                if(isset($schema_plugin_options['twitter'])){
                  $saswp_plugin_options['sd_twitter'] =  $schema_plugin_options['twitter']; 
                  $saswp_plugin_options['saswp-twitter-enable'] =  1;
                }
                if(isset($schema_plugin_options['google'])){
                  $saswp_plugin_options['sd_google_plus'] =  $schema_plugin_options['google']; 
                  $saswp_plugin_options['saswp-google-plus-enable'] =  1;
                }
                if(isset($schema_plugin_options['instagram'])){
                  $saswp_plugin_options['sd_instagram'] =  $schema_plugin_options['instagram']; 
                  $saswp_plugin_options['saswp-instagram-enable'] =  1;
                }
                if(isset($schema_plugin_options['youtube'])){
                  $saswp_plugin_options['sd_youtube'] =  $schema_plugin_options['youtube']; 
                  $saswp_plugin_options['saswp-youtube-enable'] =  1;
                }
                if(isset($schema_plugin_options['linkedin'])){
                  $saswp_plugin_options['sd_linkedin'] =  $schema_plugin_options['linkedin']; 
                  $saswp_plugin_options['saswp-linkedin-enable'] =  1;
                }
                if(isset($schema_plugin_options['pinterest'])){
                  $saswp_plugin_options['sd_pinterest'] =  $schema_plugin_options['pinterest']; 
                  $saswp_plugin_options['saswp-pinterest-enable'] =  1;
                }
                if(isset($schema_plugin_options['soundcloud'])){
                  $saswp_plugin_options['sd_soundcloud'] =  $schema_plugin_options['soundcloud']; 
                  $saswp_plugin_options['saswp-soundcloud-enable'] =  1;
                }
                if(isset($schema_plugin_options['tumblr'])){
                  $saswp_plugin_options['sd_tumblr'] =  $schema_plugin_options['tumblr']; 
                  $saswp_plugin_options['saswp-tumblr-enable'] =  1;
                }                
                if(isset($schema_plugin_options['organization_or_person'])){
                  $saswp_plugin_options['saswp_kb_type'] = ucfirst($schema_plugin_options['organization_or_person']);  
                }                
                if(isset($schema_plugin_options['about_page'])){
                  $saswp_plugin_options['sd_about_page'] = $schema_plugin_options['about_page'];  
                }
                if(isset($schema_plugin_options['contact_page'])){
                  $saswp_plugin_options['sd_contact_page'] = $schema_plugin_options['contact_page'];  
                }
                if(isset($schema_plugin_options['site_name'])){
                  $saswp_plugin_options['sd_name'] = $schema_plugin_options['site_name'];  
                }
                if(isset($schema_plugin_options['site_alternate_name'])){
                  $saswp_plugin_options['sd_alt_name'] = $schema_plugin_options['site_alternate_name'];  
                }
                if(isset($schema_plugin_options['url'])){
                  $saswp_plugin_options['sd_url'] = $schema_plugin_options['url'];  
                  $saswp_plugin_options['sd-person-url'] = $schema_plugin_options['url'];  
                }
                if(isset($schema_plugin_options['name'])){
                  $saswp_plugin_options['sd-person-name'] = $schema_plugin_options['name'];  
                }
                if(isset($schema_plugin_options['corporate_contacts_telephone'])){
                  $saswp_plugin_options['saswp_kb_telephone'] = $schema_plugin_options['corporate_contacts_telephone'];  
                }
                if(isset($schema_plugin_options['corporate_contacts_contact_type'])){
                  $saswp_plugin_options['saswp_contact_type'] = $schema_plugin_options['corporate_contacts_contact_type'];  
                }                
                if(isset($schema_plugin_options['breadcrumbs_enable'])){
                  $saswp_plugin_options['saswp_breadcrumb_schema'] = $schema_plugin_options['breadcrumbs_enable'];  
                }                
                update_option('sd_data', $saswp_plugin_options);
                //Importing settings ends here
              
            if (is_wp_error($result) ){
              echo $result->get_error_message();              
              $wpdb->query('ROLLBACK');             
            }else{
              $wpdb->query('COMMIT'); 
              return true;
            }            
        }
                             
    }
    

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
                    'sd_initial_wizard_status' =>1,
                    
                    

            );	            
            $sd_data = $settings = get_option( 'sd_data', $defaults);                     
            return $settings;
        }
