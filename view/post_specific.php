<?php
/**
 * Post Specific Class
 *
 * @author   Magazine3
 * @category Admin
 * @path     view/post_specific
 * @version 1.0.4
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class saswp_post_specific {
    
	public    $screen                    = array();				
        public    $all_schema                = null;
        public    $options_response          = array();
        public    $modify_schema_post_enable = false;        
        public    $_local_sub_business       = array(); 
        public    $_common_view              = null;
        

        public function __construct() {
            
                $mapping_local_sub = SASWP_DIR_NAME . '/core/array-list/local-sub-business.php';
                
		if ( file_exists( $mapping_local_sub ) ) {
                    $this->_local_sub_business = include $mapping_local_sub;
		}
                
                if($this->_common_view == null){
                    require_once SASWP_DIR_NAME.'/view/common.php';  
                    $this->_common_view = new saswp_view_common_class();
                }
                
        }

        /**
         * List of hooks used in this context
         */                       
        public function saswp_post_specific_hooks(){
            
                add_action( 'admin_init', array( $this, 'saswp_get_all_schema_list' ) );
                
                add_action( 'wp_ajax_saswp_get_item_reviewed_fields', array($this, 'saswp_get_item_reviewed_fields')) ;
                           
		        add_action( 'add_meta_boxes', array( $this, 'saswp_post_specifc_add_meta_boxes' ) );		
		        add_action( 'save_post', array( $this, 'saswp_post_specific_save_fields' ) );
                add_action( 'wp_ajax_saswp_get_sub_business_ajax', array($this,'saswp_get_sub_business_ajax'));
                
                add_action( 'wp_ajax_saswp_get_schema_dynamic_fields_ajax', array($this,'saswp_get_schema_dynamic_fields_ajax'));                                                                                                                
                add_action( 'wp_ajax_saswp_enable_disable_schema_on_post', array($this,'saswp_enable_disable_schema_on_post'));
                add_action( 'wp_ajax_saswp_modify_schema_post_enable', array($this,'saswp_modify_schema_post_enable')); 
                add_action( 'wp_ajax_saswp_modify_schema_post_restore', array($this,'saswp_modify_schema_post_restore'));                    
                
        }
        
        /**
         * Generate the post specific metabox html with dynamic values on ajax call
         * @return type string
         * @since version 1.0.4
         */                             
        public function saswp_modify_schema_post_restore(){
            
            

            if ( ! isset( $_POST['saswp_security_nonce'] ) ){
                return; 
            }
            if ( !wp_verify_nonce( $_POST['saswp_security_nonce'], 'saswp_ajax_check_nonce' ) ){
               return;  
            }  
            
                $post_id        = intval($_POST['post_id']);
                $schema_id      = intval($_POST['schema_id']);            
             
                update_post_meta($post_id, 'saswp_modify_this_schema_'.$schema_id, 0); 

                $meta_field = saswp_get_fields_by_schema_type($schema_id);
                
                if($meta_field){
                    foreach($meta_field as $field){
                        $result = delete_post_meta($post_id, $field['id']); 
                    }
                }                             
                echo json_encode(array('status'=> 't', 'msg'=>esc_html__( 'Schema has been restored', 'schema-and-structured-data-for-wp' )));                
                wp_die();
             
            }

        public function saswp_get_schema_fields_on_ajax($post_id, $schema_id, $item_reviewed = null){

                $response = array();

                $args = array(
                    'p'         => $post_id, // ID of a page, post, or custom type
                    'post_type' => 'any'
                );
             
                $my_posts = new WP_Query($args);
            
                if ( $my_posts->have_posts() ) {
                    
                    while ( $my_posts->have_posts() ) : $my_posts->the_post();   
                    
                        if($item_reviewed != null){
                            $response          = saswp_get_fields_by_schema_type($schema_id, null, $item_reviewed); 
                        }else{
                            $response          = saswp_get_fields_by_schema_type($schema_id);    
                        }
                                             
                    endwhile;
                
                }
                return $response;
        }    

        /**
         * Generate the post specific metabox html with dynamic values on ajax call
         * @return type string
         * @since version 1.0.4
         */                             
        public function saswp_modify_schema_post_enable(){
            
            if ( ! isset( $_GET['saswp_security_nonce'] ) ){
                return; 
            }
            if ( !wp_verify_nonce( $_GET['saswp_security_nonce'], 'saswp_ajax_check_nonce' ) ){
               return;  
            }  
            
             $post_id        = intval($_GET['post_id']);
             $schema_id      = intval($_GET['schema_id']);
             $modify_this    = 1;
             $disabled       = '';
             $modified       = false;
             
             update_post_meta($post_id, 'saswp_modify_this_schema_'.$schema_id, 1); 
             $schema_type       = get_post_meta($schema_id, 'schema_type', true); 
             $response = $this->saswp_get_schema_fields_on_ajax($post_id, $schema_id);                                            
             $saswp_meta_fields = array_filter($response); 
             
             $output            = $this->_common_view->saswp_saswp_post_specific($schema_type, $saswp_meta_fields, $post_id, $schema_id, null, $disabled, $modify_this, $modified ); 

             if($schema_type == 'Review'){
                        
                $item_reviewed     = get_post_meta($post->ID, 'saswp_review_item_reviewed_'.$schema_id, true);                         
                if(!$item_reviewed){
                    $item_reviewed = 'Book';
                }
                $response = $this->saswp_get_schema_fields_on_ajax($post_id, $schema_id, $item_reviewed);                                                                
                $saswp_meta_fields = array_filter($response);                           
                $output           .= $this->_common_view->saswp_saswp_post_specific($schema_type, $saswp_meta_fields, $post_id, $schema_id ,$item_reviewed, $disabled, $modify_this, $modified);
                
            }

             echo $output;
                                               
             wp_die();
             
            }

        /**
        * Function to get review schema type html markup
        * @since 1.0.8 
        * @return type html string
        */
         public  function saswp_get_item_reviewed_fields(){

            if ( ! isset( $_GET['saswp_security_nonce'] ) ){
                return; 
            }
            if ( !wp_verify_nonce( $_GET['saswp_security_nonce'], 'saswp_ajax_check_nonce' ) ){
               return;  
            } 
            
            $output        = '';
            $disabled      = '';
            
            $item_reviewed = sanitize_text_field($_GET['item']);  
            $schema_id     = sanitize_text_field($_GET['schema_id']);
            $schema_type   = sanitize_text_field($_GET['schema_type']);
            $post_id       = intval($_GET['post_id']);  
            $modify_this   = intval($_GET['modify_this']);
            
            $schema_enable     = get_post_meta($post_id, 'saswp_enable_disable_schema', true); 
                        
            if(isset($schema_enable[$schema_id]) && $schema_enable[$schema_id] == 0){                        
                        $disabled = 'checked';                         
            } 
            
            $response          = saswp_get_fields_by_schema_type($schema_id, null, $item_reviewed);                                                              
            $saswp_meta_fields = array_filter($response);                
            $output            = $this->_common_view->saswp_saswp_post_specific($schema_type, $saswp_meta_fields, $post_id, $schema_id, $item_reviewed, $disabled, $modify_this); 
                                 
            echo $output;

            wp_die();
        }
        /**
         * 
         */
        public function saswp_enable_disable_schema_on_post(){
            
                if ( ! isset( $_POST['saswp_security_nonce'] ) ){
                   return; 
                }
                if ( !wp_verify_nonce( $_POST['saswp_security_nonce'], 'saswp_ajax_check_nonce' ) ){
                   return;  
                } 
                
                $schema_enable = array();
                $post_id       = intval($_POST['post_id']);
                $schema_id     = sanitize_text_field($_POST['schema_id']);
                $status        = sanitize_text_field($_POST['status']);
                              
                $schema_enable_status = get_post_meta($post_id, 'saswp_enable_disable_schema', true);     
                               
                if(is_array($schema_enable_status)){
                   
                    $schema_enable = $schema_enable_status;
                   
                }else{
                    
                    delete_post_meta($post_id, 'saswp_enable_disable_schema');
                    
                } 
                                
                $schema_enable[$schema_id] = $status;   
                                
                update_post_meta( $post_id, 'saswp_enable_disable_schema', $schema_enable);                   
                
                echo json_encode(array('status'=>'t'));
                wp_die();                        
                
        }

        public function saswp_get_all_schema_list(){
            
                    $schema_ids = array();
                    $schema_id_array = json_decode(get_transient('saswp_transient_schema_ids'), true); 

                    if(!$schema_id_array){

                       $schema_id_array = saswp_get_saved_schema_ids();

                    }                                                
                    if($schema_id_array && is_array($schema_id_array)){

                        foreach($schema_id_array as $schema_id){

                            $schema_ids['ID']   = $schema_id;
                            $this->all_schema[] = (object)$schema_ids;
                        }                                                                                                                                                   
                    }
                                                                                                                      
        }

        public function saswp_post_specifc_add_meta_boxes() {
            
            global $post, $saswp_metaboxes;
                        
            $post_specific_id = '';            
            
            if(is_object($post)){
                $post_specific_id = $post->ID;
            }     
                
            $show_post_types = get_post_types();
            unset($show_post_types['adsforwp'],$show_post_types['saswp'],$show_post_types['attachment'], $show_post_types['revision'], $show_post_types['nav_menu_item'], $show_post_types['user_request'], $show_post_types['custom_css']);            
            
            $this->screen = $show_post_types;
            
            if($this->screen){
                 
                 foreach ( $this->screen as $single_screen ) {
                     
                     if(saswp_current_user_allowed()){
                      
                         add_meta_box(
                                'saswp_post_specific',
                                esc_html__( 'Schema & Structured Data on this post', 'schema-and-structured-data-for-wp' ),
                                array( $this, 'saswp_post_meta_box_callback' ),
                                $single_screen,
                                'advanced',
                                'default'
                        );
                        $saswp_metaboxes[]= 'saswp_post_specific';                         
                    }			                        
		        }   
             }   
                         		
	}
        
        public function saswp_get_schema_dynamic_fields_ajax(){
        
            if ( ! isset( $_GET['saswp_security_nonce'] ) ){
                return; 
            }
            if ( !wp_verify_nonce( $_GET['saswp_security_nonce'], 'saswp_ajax_check_nonce' ) ){
               return;  
            }
            $meta_name   = '';
            $meta_array  = array();
            $schema_id   = null;
            $schema_type = '';
            
            if(isset($_GET['schema_id'])){
                $schema_id = intval($_GET['schema_id']);
            }
            if(isset($_GET['schema_type'])){
                $schema_type = sanitize_text_field($_GET['schema_type']);
            }              
            if(isset($_GET['meta_name'])){  
                
                $meta_name = sanitize_text_field($_GET['meta_name']);                     
                if($meta_name == 'itemlist_item'){
                    
                     $itemval     = $this->_common_view->_meta_name[$meta_name][$schema_type];                     
                     if($itemval){
                         
                         foreach($itemval as $key => $val){
                             $itemval[$key]['name'] = $val['id'];
                             unset($itemval[$key]['id']);
                         }
                         
                     }
                     
                     $meta_array  = $itemval;                                               
                }else{
                     $meta_array = $this->_common_view->_meta_name[$meta_name];         
                }                                                           
            }           
            if(!empty($meta_array)){
             echo json_encode($meta_array);   
            }            
            wp_die();
        }
        
        public function saswp_post_meta_box_fields($post){    
            
             $response_html     = '';
             $disable_btn       = '';
             $cus_schema        = '';
             $tabs              = '';
             $tabs_fields       = '';
             $schema_ids        = array();
              
             $modify_option = get_option('modify_schema_post_enable_'.esc_attr($post->ID));      
             $schema_enable = get_post_meta($post->ID, 'saswp_enable_disable_schema', true);   
             $custom_markp  = get_post_meta($post->ID, 'saswp_custom_schema_field', true);              
             $disable_btn.= '<div class="saswp-disable-btn-container">'
                            . '<span class="saswp-disable-label">'.esc_html__( 'Disable custom schema on this page', 'schema-and-structured-data-for-wp' ).'</span>'
                            . '<label class="saswp-switch">'
                            . '<input type="checkbox" class="saswp-schema-type-toggle" value="1" data-schema-id="custom" data-post-id="'.esc_attr($post->ID).'" '.( (isset($schema_enable['custom']) && $schema_enable['custom'] == 0) ? 'checked' : '' ).'>'
                            . '<span class="saswp-slider"></span>'
                            . '</label>'
                            . '</div>';
             
                $cus_schema .= '<div id="saswp_specific_custom" class="saswp-post-specific-wrapper saswp_hide">';                                      
                $cus_schema .= '<div class="'.((isset($schema_enable['custom']) && $schema_enable['custom'] == 0) ? 'saswp_hide' : '').'"><textarea style="margin-left:5px;" placeholder="JSON-LD" schema-id="custom" id="saswp_custom_schema_field" name="saswp_custom_schema_field" rows="5" cols="95">'
                            .  $custom_markp
                            .  '</textarea>';
                $cus_schema .= '<span><strong>'.esc_html__( 'Note', 'schema-and-structured-data-for-wp' ).': </strong>'.esc_html__( 'Please enter the valid Json-ld. Whatever you enter will be added in page source', 'schema-and-structured-data-for-wp' ).'</span>';
                $cus_schema .= '</div>';
                $cus_schema .= $disable_btn;
                $cus_schema .= '</div>';
                          
             if(!empty($this->all_schema)){  
                    
                 foreach($this->all_schema as $key => $schema){
                     
                      $advnace_status = saswp_check_advance_display_status($schema->ID);
                                          
                      if($advnace_status !== 1){
                          continue;
                      }
                                                          
                     $disabled  = '';
                     $modified  = false;
                     $item_type = '';
                     $output    = '';
                                                                                    
                     if(isset($schema_enable[$schema->ID]) && $schema_enable[$schema->ID] == 0){
                         
                        $disabled = 'checked';    
                     
                     }
                     
                     if($modify_option == 'enable' && !isset($schema_enable[$schema->ID])){
                     
                        $disabled = 'checked'; 
                         
                     }
                     
                     if($modify_option == 'enable' && (isset($schema_enable[$schema->ID]) && $schema_enable[$schema->ID] == 1)){
                     
                        $modified = true;  
                         
                     }
                     
                     $modify_this       = get_post_meta($post->ID, 'saswp_modify_this_schema_'.$schema->ID, true);                                          
                     $schema_type       = get_post_meta($schema->ID, 'schema_type', true);  
                     $response          = @saswp_get_fields_by_schema_type($schema->ID);                       
                     $saswp_meta_fields = array_filter($response); 
                     if($modify_this){
                        $output            = $this->_common_view->saswp_saswp_post_specific($schema_type, $saswp_meta_fields, $post->ID, $schema->ID, null, $disabled, $modify_this, $modified ); 
                     }                    
                     
                     
                     if($schema_type == 'ItemList'){
                         $item_type         = '('.get_post_meta($schema->ID, 'saswp_itemlist_item_type', true).')';
                     }
                     
                     if($schema_type == 'Review' && $modify_this){
                        
                         $item_reviewed     = get_post_meta($post->ID, 'saswp_review_item_reviewed_'.$schema->ID, true);                         
                         if(!$item_reviewed){
                             $item_reviewed = 'Book';
                         }
                         $response          = @saswp_get_fields_by_schema_type($schema->ID, null, $item_reviewed);                                                              
                         $saswp_meta_fields = array_filter($response);                           
                         $output           .= $this->_common_view->saswp_saswp_post_specific($schema_type, $saswp_meta_fields, $post->ID, $schema->ID ,$item_reviewed, $disabled, $modify_this, $modified);
                         
                     }
                     
                     if($schema_type == 'ItemList'){
                         $setting_options = '<div class="saswp-post-specific-setting saswp_hide">';
                     }else{
                         $setting_options = '<div class="saswp-post-specific-setting">';
                     }
                     
                         $setting_options.= '<div class="saswp-ps-buttons">';
                         
                            if($schema_type == 'ItemList'){
                                 $setting_options  .= '<input class="saswp_modify_this_schema_hidden_'.esc_attr($schema->ID).'" type="hidden" name="saswp_modify_this_schema_'.esc_attr($schema->ID).'" value="1">';
                            }else{
                                 $setting_options  .= '<input class="saswp_modify_this_schema_hidden_'.esc_attr($schema->ID).'" type="hidden" name="saswp_modify_this_schema_'.esc_attr($schema->ID).'" value="'.( ($modify_this || $modified ) ? 1 : 0).'">';
                            }
                    
                         if(!empty($disabled)){
                             $setting_options  .= '<div class="saswp-ps-text saswp_hide">';
                         }else{
                             $setting_options  .= '<div class="saswp-ps-text '.( ($modify_this || $modified ) ? '' : 'saswp_hide').'">';
                         }
                         
                         $setting_options  .= '<a class="button button-default saswp-restore-schema button" schema-id="'.esc_attr($schema->ID).'">'.esc_html__( 'Restore to Auto Fetch', 'schema-and-structured-data-for-wp' ).'</a>';                         
                         $setting_options  .= '</div>';
                                                  
                         if(!empty($disabled)){
                             $setting_options  .= '<div class="saswp-ps-text saswp_hide">';
                         }else{
                             $setting_options  .= '<div class="saswp-ps-text '.(($modify_this || $modified ) ? 'saswp_hide' : '').'">';
                         }    
                         
                         $schema_type_txt = $schema_type;
                         
                         if($schema_type == 'local_business'){
                             $schema_type_txt = 'Local Business';
                         }
                         if($schema_type == 'qanda'){
                             $schema_type_txt = 'Q&A';
                         }
                         
                         $setting_options  .= '<span>'.esc_html__( $schema_type_txt.' schema is fetched automatically', 'schema-and-structured-data-for-wp' ).'</span><br><br>';
                         $setting_options  .= '<a class="button button-default saswp-modify-schema button" schema-id="'.esc_attr($schema->ID).'">'.esc_html__( 'Modify '.$schema_type.' Schema Output', 'schema-and-structured-data-for-wp' ).'</a>';
                         $setting_options  .= '</div>';                                                                  
                                        
                    $setting_options.= '</div>';                                                
                    $setting_options.= '</div>';
                     
                    $btn_in_loop = '<div class="saswp-disable-btn-container">'
                            . '<span class="saswp-disable-label">'.esc_html__( 'Disable '.$schema_type_txt.' on this page', 'schema-and-structured-data-for-wp' ).'</span>'
                            . '<label class="saswp-switch">'
                            . '<input type="checkbox" class="saswp-schema-type-toggle" value="1" data-schema-id="'.esc_attr($schema->ID).'" data-post-id="'.esc_attr($post->ID).'" '.$disabled.'>'
                            . '<span class="saswp-slider"></span>'
                            . '</label>'
                            . '</div>';
                    
                     if($key==0){
                         
                     $tabs .='<li class="selected"><a saswp-schema-type="'.esc_attr($schema_type).'" data-id="saswp_specific_'.esc_attr($schema->ID).'" class="saswp-tab-links selected">'.esc_attr(($schema_type == 'local_business'? 'LocalBusiness': ($schema_type =='qanda' ? 'Q&A' : $schema_type)).' '.$item_type ).'</a>'
                             . '</li>';    
                     
                     $tabs_fields .= '<div data-id="'.esc_attr($schema->ID).'" id="saswp_specific_'.esc_attr($schema->ID).'" class="saswp-post-specific-wrapper">';                                                                  
                     $tabs_fields .= $setting_options;                                                                 
                     $tabs_fields .= $output;  
                     $tabs_fields .= $btn_in_loop;
                     $tabs_fields .= '</div>';
                     
                     }else{
                         
                     $tabs .='<li>'
                             . '<a saswp-schema-type="'.esc_attr($schema_type).'" data-id="saswp_specific_'.esc_attr($schema->ID).'" class="saswp-tab-links">'.esc_attr(($schema_type == 'local_business'? 'LocalBusiness': ($schema_type =='qanda' ? 'Q&A' : $schema_type)).' '.$item_type ).'</a>'
                             . '</li>';   
                     
                     $tabs_fields .= '<div data-id="'.esc_attr($schema->ID).'" id="saswp_specific_'.esc_attr($schema->ID).'" class="saswp-post-specific-wrapper saswp_hide">';                                                                  
                     $tabs_fields .= $setting_options;                       
                     $tabs_fields .= $output;                     
                     $tabs_fields .= $btn_in_loop;                     
                     $tabs_fields .= '</div>';
                     
                     } 
                     
                     $schema_ids[] =$schema->ID;
                 }   
                                  
                $response_html .= '<div>';                  
                $response_html .= '<div class="saswp-tab saswp-post-specific-tab-wrapper">';                
		        $response_html .= '<ul class="saswp-tab-nav">';
                $response_html .= $tabs;    
                
                $response_html .='<li>'
                             . '<a class="saswp-tab-links" data-id="saswp_specific_custom">'.esc_html__( 'Custom Schema', 'schema-and-structured-data-for-wp' ).'</a>'
                             . '</li>';                
                $response_html .= '</ul>';                
                $response_html .= '</div>';                
                $response_html .= '<div class="saswp-post-specific-container">';                
                $response_html .= $tabs_fields; 
                $response_html .= $cus_schema;                                
                $response_html .= '</div>';
                                                                                
                $response_html .= '<input class="saswp-post-specific-schema-ids" type="hidden" value="'. json_encode($schema_ids).'">';
                $response_html .= '</div>'; 
                                  
                }
             else{
                 
                 
                $response_html .= '<div class="saswp-tab saswp-post-specific-tab-wrapper">';
                $response_html .= '<div><a href="'.esc_url( admin_url( 'edit.php?post_type=saswp' ) ).'" class="button button-default saswp-setup-schema-btn">'.esc_html__( 'Setup Schema', 'schema-and-structured-data-for-wp' ).'</div>';                
		$response_html .= '<ul class="saswp-tab-nav">';                
                $response_html .= '<li class="selected">'
                             . '<a class="saswp-tab-links" data-id="saswp_specific_custom">'.esc_html__( 'Custom Schema', 'schema-and-structured-data-for-wp' ).'</a>'
                             . '</li>';                
                $response_html .= '</ul>';                
                $response_html .= '</div>';                
                $response_html .= '<div class="saswp-post-specific-container">';                                
                $response_html .= $cus_schema;  
                $response_html .= '</div>';
                                  
             }
                
             return $response_html;   
        }

        public function saswp_post_meta_box_html($std_post){
                
                global $post;
                
                if(!is_object($post)){
                    $post = $std_post;
                }
                                               
                $response_html = '';                                
                $schema_avail  = false;                
                if($this->all_schema){
                    
                    foreach ($this->all_schema as $schema){
                        
                      $advnace_status = saswp_check_advance_display_status($schema->ID);
                    
                      if($advnace_status == 1){
                          $schema_avail = true;
                          break;
                      }
                                                
                    }
                    
                }
                 
                $response_html .= $this->saswp_post_meta_box_fields($post);  
                
                return $response_html;
        }
        
        public function saswp_post_meta_box_callback() { 
                    
                global $post;                 
		        wp_nonce_field( 'post_specific_data', 'post_specific_nonce' );  
                echo $this->saswp_post_meta_box_html($post);                                             
                                                                                                                                                                   		
        }        
        
        /**
         * Function to save post specific metabox fields value
         * @param type $post_id
         * @return type null
         * @since version 1.0.4
         */
	public function saswp_post_specific_save_fields( $post_id ) {
                                            
		if ( ! isset( $_POST['post_specific_nonce'] ) ) return $post_id;					        
		if ( !wp_verify_nonce( $_POST['post_specific_nonce'], 'post_specific_data' ) ) return $post_id;			
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return $post_id;       			
                if ( ! current_user_can( 'edit_post', $post_id ) ) return $post_id;    
                                       
                $allowed_html = saswp_expanded_allowed_tags(); 
                                                 
                $custom_schema  = wp_kses(wp_unslash($_POST['saswp_custom_schema_field']), $allowed_html);
                update_post_meta( $post_id, 'saswp_custom_schema_field', $custom_schema );                 
                                                                               
                $this->_common_view->saswp_save_common_view($post_id, $this->all_schema);
	}
        
        public function saswp_get_sub_business_ajax(){
            
            if ( ! isset( $_GET['saswp_security_nonce'] ) ){
                return; 
            }
            if ( !wp_verify_nonce( $_GET['saswp_security_nonce'], 'saswp_ajax_check_nonce' ) ){
               return;  
            } 
            $business_type = sanitize_text_field($_GET['business_type']);
                                       
            $response = $this->_local_sub_business[$business_type]; 
            
           if($response){                              
              echo json_encode(array('status'=>'t', 'result'=>$response)); 
           }else{
              echo json_encode(array('status'=>'f', 'result'=>'data not available')); 
           }
            wp_die();
        }
                
}
if (class_exists('saswp_post_specific')) {
	$object = new saswp_post_specific();
        $object->saswp_post_specific_hooks();
};