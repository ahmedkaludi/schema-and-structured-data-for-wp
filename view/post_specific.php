<?php
class saswp_post_specific {
    
	private   $screen                    = array();
	private   $meta_fields               = array();				
        protected $all_schema                = null;
        protected $options_response          = array();
        protected $modify_schema_post_enable = false;
        
        public function __construct() {
            
	}
        
        public function saswp_post_specific_hooks(){
            
                $this->saswp_get_all_schema_list();
		add_action( 'add_meta_boxes', array( $this, 'saswp_post_specifc_add_meta_boxes' ) );		
		add_action( 'save_post', array( $this, 'saswp_post_specific_save_fields' ) );
                add_action( 'wp_ajax_saswp_get_sub_business_ajax', array($this,'saswp_get_sub_business_ajax'));
                
                add_action( 'wp_ajax_saswp_modify_schema_post_enable', array($this,'saswp_modify_schema_post_enable'));
                
                add_action( 'wp_ajax_saswp_restore_schema', array($this,'saswp_restore_schema'));
                
                add_action( 'wp_ajax_saswp_enable_disable_schema_on_post', array($this,'saswp_enable_disable_schema_on_post'));
                
        }
        public function saswp_enable_disable_schema_on_post(){
            
                if ( ! isset( $_POST['saswp_security_nonce'] ) ){
                   return; 
                }
                if ( !wp_verify_nonce( $_POST['saswp_security_nonce'], 'saswp_ajax_check_nonce' ) ){
                   return;  
                } 
                
                $schema_enable = array();
                $post_id       = sanitize_text_field($_POST['post_id']);
                $schema_id     = sanitize_text_field($_POST['schema_id']);
                $status        = sanitize_text_field($_POST['status']);
                
                $schema_enable = get_post_meta($post_id, 'saswp_enable_disable_schema', true);                                
                
                $schema_enable[$schema_id] = $status;                                 
                update_post_meta( $post_id, 'saswp_enable_disable_schema', $schema_enable);                   
                
                echo json_encode(array('status'=>'t'));
                wp_die();                        
                
        }

        public function saswp_get_all_schema_list(){
            
                if($this->all_schema == null){
                    
                 $all_schema = get_posts(
                    array(
                            'post_type' 	 => 'saswp',
                            'posts_per_page' => -1,   
                            'post_status' => 'publish',
                    )
                 ); 
                 
                 $this->all_schema = $all_schema;    
                }
                                           
        }

        public function saswp_post_specifc_add_meta_boxes($post) {
            
            $post_specific_id = '';
            if(is_object($post)){
                $post_specific_id = $post->ID;
            }           
            if(count($this->all_schema)>0 && get_post_status($post_specific_id)=='publish'){
                
            $show_post_types = get_post_types();
            unset($show_post_types['adsforwp'],$show_post_types['saswp'],$show_post_types['attachment'], $show_post_types['revision'], $show_post_types['nav_menu_item'], $show_post_types['user_request'], $show_post_types['custom_css']);            
            $this->screen = $show_post_types;
                
             foreach ( $this->screen as $single_screen ) {
                 $post_title ='';
                 if(count($this->all_schema)==1){
                    $all_schemas = $this->all_schema;
                    $post_title = '('.$all_schemas[0]->post_title.')';                                      
                     }
			add_meta_box(
				'post_specific',
				__( 'Post Specific Schema '.$post_title, 'schema-and-structured-data-for-wp' ),
				array( $this, 'saswp_post_meta_box_callback' ),
				$single_screen,
				'advanced',
				'default'
			);
                        
		}   
            }		
	}
        
        public function saswp_post_meta_box_fields($post){    
            
                $tabs         = '';
                $tabs_fields  = '';
                $schema_ids   = array();
                
                $schema_enable = get_post_meta($post->ID, 'saswp_enable_disable_schema', true);
                
             if(count($this->all_schema)>1){  
                 
                 foreach($this->all_schema as $key => $schema){
                     
                     $checked = '';
                     
                     if(isset($schema_enable[$schema->ID]) && $schema_enable[$schema->ID] == 1){
                         
                     $checked = 'checked';    
                     
                     }                     
                     $response = $this->saswp_get_fields_by_schema_type($schema->ID);                     
                     $this->meta_fields = $response;
                     
                     $output       = $this->saswp_saswp_post_specific( $post, $schema->ID ); 
                     $schema_type  = esc_sql ( get_post_meta($schema->ID, 'schema_type', true)  ); 
                     
                     if($key==0){
                         
                     $tabs .='<li class="selected"><a saswp-schema-type="'.$schema_type.'" data-id="saswp_specific_'.esc_attr($schema->ID).'" class="saswp-tab-links selected">'.esc_attr($schema->post_title).'</a>'
                             . '<label class="saswp-switch">'
                             . '<input type="checkbox" class="saswp-schema-type-toggle" value="1" data-schema-id="'.esc_attr($schema->ID).'" data-post-id="'.esc_attr($post->ID).'" '.$checked.'>'
                             . '<span class="saswp-slider"></span>'
                             . '</li>';    
                     
                     $tabs_fields .= '<div data-id="'.esc_attr($schema->ID).'" id="saswp_specific_'.esc_attr($schema->ID).'" class="saswp-post-specific-wrapper">';
                     $tabs_fields .= '<table class="form-table"><tbody>' . $output . '</tbody></table>';
                     $tabs_fields .= '</div>';
                     
                     }else{
                         
                     $tabs .='<li>'
                             . '<a saswp-schema-type="'.$schema_type.'" data-id="saswp_specific_'.esc_attr($schema->ID).'" class="saswp-tab-links">'.esc_attr($schema->post_title).'</a>'
                             . '<label class="saswp-switch">'
                             . '<input type="checkbox" class="saswp-schema-type-toggle" value="1" data-schema-id="'.esc_attr($schema->ID).'" data-post-id="'.esc_attr($post->ID).'" '.$checked.'>'
                             . '<span class="saswp-slider"></span>'
                             . '</li>';    
                     $tabs_fields .= '<div data-id="'.esc_attr($schema->ID).'" id="saswp_specific_'.esc_attr($schema->ID).'" class="saswp-post-specific-wrapper saswp_hide">';
                     $tabs_fields .= '<table class="form-table"><tbody>' . $output . '</tbody></table>';
                     $tabs_fields .= '</div>';
                     
                     } 
                     
                     $schema_ids[] =$schema->ID;
                 }   
                                  
                echo '<div>';  
                echo '<div><a href="#" class="saswp-restore-post-schema button">'.esc_html__( 'Restore Default Schema', 'schema-and-structured-data-for-wp' ).'</a></div>';  
                echo '<div class="saswp-tab saswp-post-specific-tab-wrapper">';                
		echo '<ul class="saswp-tab-nav">';
                echo $tabs;                
                echo '</ul>';                
                echo '</div>';                
                echo '<div class="saswp-post-specific-container">';                
                echo $tabs_fields;                                 
                echo '</div>';
                echo '<input class="saswp-post-specific-schema-ids" type="hidden" value="'. json_encode($schema_ids).'">';
                echo '</div>'; 
                                  
                }else{
                                                            
                 $all_schema = $this->all_schema;                  
                 $response   = $this->saswp_get_fields_by_schema_type($all_schema[0]->ID); 
                
                 $schema_ids[] =$all_schema[0]->ID;
                 $schema_type  = esc_sql ( get_post_meta($all_schema[0]->ID, 'schema_type', true)  ); 
                 $checked = '';
                 if(isset($schema_enable[$all_schema[0]->ID]) && $schema_enable[$all_schema[0]->ID] == 1){
                 $checked = 'checked';    
                 }
                 
                 $this->meta_fields = $response;
                 $output = $this->saswp_saswp_post_specific( $post, $all_schema[0]->ID );  
                 $tabs_fields .= '<div>';
                 $tabs_fields .= '<div class="saswp-single-post-restore"><a href="#" class="saswp-restore-post-schema button saswp-tab-links selected" saswp-schema-type="'.$schema_type.'">'.esc_html__( 'Restore Default Schema', 'schema-and-structured-data-for-wp' ).'</a>'
                              . '<label class="saswp-switch" style="margin-left:10px;">'
                              . '<input type="checkbox" class="saswp-schema-type-toggle" value="1" data-schema-id="'.esc_attr($all_schema[0]->ID).'" data-post-id="'.esc_attr($post->ID).'" '.$checked.'>'
                              . '<span class="saswp-slider"></span>'
                              . '</div>';
                 $tabs_fields .= '<div id="saswp_specific_'.esc_attr($all_schema[0]->ID).'" class="saswp-post-specific-wrapper">';
                 $tabs_fields .= '<table class="form-table"><tbody>' . $output . '</tbody></table>';
                 $tabs_fields .= '</div>';
                 $tabs_fields .= '<input class="saswp-post-specific-schema-ids" type="hidden" value="'. json_encode($schema_ids).'">';
                 $tabs_fields .= '</div>';
                 echo $tabs_fields;                                                  
                }
        }

        public function saswp_post_meta_box_callback() { 
            
		wp_nonce_field( 'post_specific_data', 'post_specific_nonce' );                                
                global $post;  
                $option = get_option('modify_schema_post_enable_'.$post->ID);
                
                if($option == 'enable'){
                    
                  $this->saswp_post_meta_box_fields($post);  
                
                }else{
                    
                  echo '<a class="button saswp-modify_schema_post_enable">Modify Schema</a>' ;
                  
                }                               
                                                                                                                                                                   		
	}
        
        public function saswp_restore_schema(){
            
                if ( ! isset( $_POST['saswp_security_nonce'] ) ){
                return; 
                }
                if ( !wp_verify_nonce( $_POST['saswp_security_nonce'], 'saswp_ajax_check_nonce' ) ){
                   return;  
                } 
                $result     = '';
                $post_id    = sanitize_text_field($_POST['post_id']); 
                $schema_ids = $_POST['schema_ids'];
                
                foreach($schema_ids as $id){
                    
                  $meta_field = $this->saswp_get_fields_by_schema_type($id);
                  
                  foreach($meta_field as $field){
                      
                   $result = delete_post_meta($post_id, $field['id']); 
                   
                  }   
                  
                }    
                
                update_option('modify_schema_post_enable_'.$post_id, 'disable'); 
                
                if($result){                     
                    echo json_encode(array('status'=> 't', 'msg'=>esc_html__( 'Schema has been restored', 'schema-and-structured-data-for-wp' )));
                }else{
                    echo json_encode(array('status'=> 'f', 'msg'=>esc_html__( 'Schema has already been restored', 'schema-and-structured-data-for-wp' )));
                }                                              
                 wp_die();
                }
        
        public function saswp_modify_schema_post_enable(){
            
                if ( ! isset( $_GET['saswp_security_nonce'] ) ){
                    return; 
                }
                if ( !wp_verify_nonce( $_GET['saswp_security_nonce'], 'saswp_ajax_check_nonce' ) ){
                   return;  
                }  
                
                 $post_id = sanitize_text_field($_GET['post_id']);
                 update_option('modify_schema_post_enable_'.$post_id, 'enable');    
                 
                 $args = array(
                    'p'         => $post_id, // ID of a page, post, or custom type
                    'post_type' => 'any'
                  );
                 
                $my_posts = new WP_Query($args);
                
                if ( $my_posts->have_posts() ) {
                    
                  while ( $my_posts->have_posts() ) : $my_posts->the_post();   
                  
                   echo $this->saswp_post_meta_box_callback();   
                   
                  endwhile;
                  
                }
                                                   
                 wp_die();
                 
                }

                public function saswp_saswp_post_specific( $post, $schema_id ) { 
            
                global $post;
                global $sd_data;                        
                $image_id      = get_post_thumbnail_id();
                $image_details = wp_get_attachment_image_src($image_id, 'full');
                
                if(empty($image_details[0]) || $image_details[0] === NULL ){
                
                 if(isset($sd_data['sd_logo'])){
                     $image_details[0] = $sd_data['sd_logo']['url'];
                 }
                                    
                }
                
                $current_user   = wp_get_current_user();
                $author_details	= get_avatar_data($current_user->ID);                
                $schema_type    = esc_sql ( get_post_meta($schema_id, 'schema_type', true)  );  
		$output = '';
                
                $this->meta_fields = array_filter($this->meta_fields);
                
		foreach ( $this->meta_fields as $meta_field ) {
                    
                        $input      ='';
                        $attributes ='';
                        
			$label      = '<label for="' . $meta_field['id'] . '">' . $meta_field['label'] . '</label>';
			$meta_value = get_post_meta( $post->ID, $meta_field['id'], true );
                        
			if ( empty( $meta_value ) && isset($meta_field['default'])) {
                            
				$meta_value = $meta_field['default'];                                 
                        }
                        if(isset($meta_field['attributes'])){
                            foreach ($meta_field['attributes'] as $key => $attr ){
                                           $attributes .=''.$key.'="'.$attr.'"';
                                }
                        }                        
                        
			switch ( $meta_field['type'] ) {
				case 'media':
                                        $media_value = array();
                                        $media_key = $meta_field['id'].'_detail';
                                        $media_value_meta = get_post_meta( $post->ID, $media_key, true ); 
                                        if(!empty($media_value_meta)){
                                        $media_value =$media_value_meta;  
                                        }                                        
                                        if (strpos($meta_field['id'], 'author_image') !== false && empty($media_value_meta)) {                                          
                                                $media_value['height'] = $author_details['height'];                                                                                         
                                                $media_value['width'] = $author_details['width'];                                                                                         
                                                $media_value['thumbnail'] = $author_details['url'];                                             
                                        }
                                        if (strpos($meta_field['id'], 'organization_logo') !== false && empty($media_value_meta)) {
                                                                                            
                                                if(isset($sd_data['sd_logo'])){
                                                    $media_value['height'] = $sd_data['sd_logo']['height'];                                                                                         
                                                    $media_value['width'] = $sd_data['sd_logo']['width'];                                                                                         
                                                    $media_value['thumbnail'] = $sd_data['sd_logo']['url']; 
                                                }
                                                                                                                                        
                                        }
                                        if (strpos($meta_field['id'], 'business_logo') !== false && empty($media_value_meta)) {
                                            
                                                $business_details = esc_sql ( get_post_meta($schema_id, 'saswp_local_business_details', true)  );                                                                                            
                                                $media_value['height'] = $business_details['local_business_logo']['height'];                                                                                         
                                                $media_value['width'] = $business_details['local_business_logo']['width'];                                                                                         
                                                $media_value['thumbnail'] = $business_details['local_business_logo']['url'];                                             
                                        }
                                        
                                        if (strpos($meta_field['id'], 'product_schema_image') !== false && empty($media_value_meta)) {
                                            
                                                $business_details = esc_sql ( get_post_meta($schema_id, 'saswp_product_schema_details', true)  );                                                                                            
                                                $media_value['height'] = $business_details['saswp_product_schema_image']['height'];                                                                                         
                                                $media_value['width'] = $business_details['saswp_product_schema_image']['width'];                                                                                         
                                                $media_value['thumbnail'] = $business_details['saswp_product_schema_image']['url'];                                             
                                        }
                                        
                                        if (strpos($meta_field['id'], 'service_schema_image') !== false && empty($media_value_meta)) {
                                            
                                                $business_details = esc_sql ( get_post_meta($schema_id, 'saswp_service_schema_details', true)  );                                                                                            
                                                $media_value['height'] = $business_details['saswp_service_schema_image']['height'];                                                                                         
                                                $media_value['width'] = $business_details['saswp_service_schema_image']['width'];                                                                                         
                                                $media_value['thumbnail'] = $business_details['saswp_service_schema_image']['url'];                                             
                                        }
                                        
                                        if (strpos($meta_field['id'], 'review_schema_image') !== false && empty($media_value_meta)) {
                                            
                                                $business_details = esc_sql ( get_post_meta($schema_id, 'saswp_review_schema_details', true)  );                                                                                            
                                                $media_value['height'] = $business_details['saswp_review_schema_image']['height'];                                                                                         
                                                $media_value['width'] = $business_details['saswp_review_schema_image']['width'];                                                                                         
                                                $media_value['thumbnail'] = $business_details['saswp_review_schema_image']['url'];                                             
                                        }
                                             
                                        $media_height ='';
                                        $media_width ='';
                                        $media_thumbnail ='';
                                        
                                        if(isset($media_value['thumbnail'])){
                                            $media_thumbnail =$media_value['thumbnail'];
                                        }
                                        if(isset($media_value['height'])){
                                           $media_height =$media_value['height']; 
                                        }
                                        if(isset($media_value['width'])){
                                             $media_width =$media_value['width'];
                                        }
                                        
                                        
					$input = sprintf(
						'<fieldset><input style="width: 80%%" id="%s" name="%s" type="text" value="%s">'
                                                . '<input data-id="media" style="width: 19%%" class="button" id="%s_button" name="%s_button" type="button" value="Upload" />'
//                                                . '<input type="hidden" data-id="'.esc_attr($meta_field['id']).'_id" class="upload-id " name="'.esc_attr($meta_field['id']).'_id" id="'.esc_attr($meta_field['id']).'_id" value="'.esc_attr($media_value['id']).'">'
                                                . '<input type="hidden" data-id="'.esc_attr($meta_field['id']).'_height" class="upload-height" name="'.esc_attr($meta_field['id']).'_height" id="'.esc_attr($meta_field['id']).'_height" value="'.esc_attr($media_height).'">'
                                                . '<input type="hidden" data-id="'.esc_attr($meta_field['id']).'_width" class="upload-width" name="'.esc_attr($meta_field['id']).'_width" id="'.esc_attr($meta_field['id']).'_width" value="'.esc_attr($media_width).'">'
                                                . '<input type="hidden" data-id="'.esc_attr($meta_field['id']).'_thumbnail" class="upload-thumbnail" name="'.esc_attr($meta_field['id']).'_thumbnail" id="'.esc_attr($meta_field['id']).'_thumbnail" value="'.esc_attr($media_thumbnail).'">'                                                
                                                .'</fieldset>',
						$meta_field['id'],
						$meta_field['id'],
						$meta_value,
						$meta_field['id'],
						$meta_field['id']
					);
					break;
				case 'radio':
					$input = '<fieldset>';
					$input .= '<legend class="screen-reader-text">' . $meta_field['label'] . '</legend>';
					$i = 0;
					foreach ( $meta_field['options'] as $key => $value ) {
						$meta_field_value = !is_numeric( $key ) ? $key : $value;
						$input .= sprintf(
							'<label><input %s id=" % s" name="% s" type="radio" value="% s"> %s</label>%s',
							$meta_value === $meta_field_value ? 'checked' : '',
							$meta_field['id'],
							$meta_field['id'],
							$meta_field_value,
							$value,
							$i < count( $meta_field['options'] ) - 1 ? '<br>' : ''
						);
						$i++;
					}
					$input .= '</fieldset>';
					break;
				case 'select':                                        
                                             $class = '';
                                             if (strpos($meta_field['id'], 'business_type') !== false){
                                             $class='saswp-local-business-type-select';    
                                             }
                                             if (strpos($meta_field['id'], 'business_name') !== false){
                                             $class='saswp-local-business-name-select';    
                                             }
                                             if (strpos($meta_field['id'], 'saswp_review_schema_item_type') !== false){
                                             $class='saswp-item-reviewed';    
                                             }
                                        
					$input = sprintf(
						'<select post-specific="1" data-id="'.$schema_id.'" class="%s" id="%s" name="%s">',
                                                $class,
						$meta_field['id'],
						$meta_field['id']
					);
					foreach ( $meta_field['options'] as $key => $value ) {
						$meta_field_value = !is_numeric( $key ) ? $key : $value;
						$input .= sprintf(
							'<option %s value="%s">%s</option>',
							$meta_value === $meta_field_value ? 'selected' : '',
							$meta_field_value,
							$value
						);
					}
					$input .= '</select>';
					break;
                                
                                case 'checkbox':
                                    
                                        $rating_class = 'class="saswp-enable-rating-review-'.strtolower($schema_type).'"';
                                        
                                        
                                        
					$input = sprintf(
						'<input %s %s id="%s" name="%s" type="checkbox" value="1">',
                                                $rating_class,
						$meta_value === '1' ? 'checked' : '',
						$meta_field['id'],
						$meta_field['id']
						);
					break;        
                                        
                                case 'multiselect':                                       
					$input = sprintf(
						'<select multiple id="%s" name="%s[]">',
						$meta_field['id'],
						$meta_field['id']
					);
					foreach ( $meta_field['options'] as $key => $value ) {
                                                $meta_field_selected='';
                                                if(isset($meta_value)){
                                                if(in_array($key, $meta_value)){
                                                $meta_field_selected = 'selected';                                                 
                                                }    
                                                }                                                
						$input .= sprintf(
							'<option %s value="%s">%s</option>',
							$meta_field_selected,
							$key,
							$value
						);
					}
					$input .= '</select>';
					break;        
                                        
                                        
				case 'textarea':
					$input = sprintf(
						'<textarea %s style="width: 100%%" id="%s" name="%s" rows="5">%s</textarea>',
                                                $attributes,
						$meta_field['id'],
						$meta_field['id'],
						$meta_value
					);
                                        if(isset($meta_field['note'])){
                                          $input .='<p>'.$meta_field['note'].'</p>';  
                                        }
					break;
                                case 'text':
                                case 'number':    
                                    $class = '';
                                             if (strpos($meta_field['id'], 'closes_time') !== false || strpos($meta_field['id'], 'opens_time') !== false){
                                             $class='saswp-local-schema-time-picker';    
                                             }
                                             if (strpos($meta_field['id'], 'date_modified') !== false 
                                                     || strpos($meta_field['id'], 'date_published') !== false  
                                                     || strpos($meta_field['id'], 'video_upload_date') !== false
                                                     || strpos($meta_field['id'], 'qa_date_created') !== false 
                                                     || strpos($meta_field['id'], 'accepted_answer_date_created') !== false 
                                                     || strpos($meta_field['id'], 'suggested_answer_date_created') !== false 
                                                     || strpos($meta_field['id'], 'priceValidUntil') !== false
                                                     || strpos($meta_field['id'], 'priceValidUntil') !== false
                                                     || strpos($meta_field['id'], 'priceValidUntil') !== false
                                                     ) {
                                             $class='saswp-local-schema-datepicker-picker';    
                                             }
                                             
                                            $input = sprintf(
						'<input %s class="%s" %s id="%s" name="%s" type="%s" value="%s">',
                                                $attributes,    
                                                $class,    
						$meta_field['type'] !== 'color' ? 'style="width: 100%"' : '',
						$meta_field['id'],
						$meta_field['id'],
						$meta_field['type'],
						$meta_value                                                                                                 
					   );
                                            if(isset($meta_field['note'])){
                                            $input .='<p>'.$meta_field['note'].'</p>';  
                                           }
                                         break;	
                                
				default:       
                                            			
			}
                        
                        
                        if($meta_field['id'] == 'saswp_service_schema_rating_'.$schema_id || 
                           $meta_field['id'] == 'saswp_product_schema_rating_'.$schema_id ||
                           $meta_field['id'] == 'saswp_review_schema_rating_'.$schema_id ||
                           $meta_field['id'] == 'local_rating_'.$schema_id               ||
                           
                           $meta_field['id'] == 'saswp_service_schema_review_count_'.$schema_id || 
                           $meta_field['id'] == 'saswp_product_schema_review_count_'.$schema_id ||
                           $meta_field['id'] == 'saswp_review_schema_review_count_'.$schema_id ||
                           $meta_field['id'] == 'local_review_count_'.$schema_id     
                                
                          )
                          {
                            $output .= '<tr class="saswp-rating-review-'.strtolower($schema_type).'"><th>'.$label.'</th><td>'.$input.'</td></tr>'; 
                          }else if($schema_type == 'Review' && $meta_field['id'] != 'saswp_review_schema_enable_rating_'.$schema_id) {
                            
                            $output .= '<tr class="saswp-review-tr"><th>'.$label.'</th><td>'.$input.'</td></tr>';   
                              
                          }else{
                             $output .= '<tr><th>'.$label.'</th><td>'.$input.'</td></tr>';  
                          }                                                                       
			
		}
                return $output;                                               
	}	
	public function saswp_post_specific_save_fields( $post_id ) {
            
		if ( ! isset( $_POST['post_specific_nonce'] ) )
			return $post_id;
		$nonce = $_POST['post_specific_nonce'];
		if ( !wp_verify_nonce( $nonce, 'post_specific_data' ) )
			return $post_id;
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;       
                 
                $option = get_option('modify_schema_post_enable_'.$post_id);
                
                if($option != 'enable'){
                    return;
                }  
                
                
                
                if(count($this->all_schema)>0){
                                                                      
                 foreach($this->all_schema as $schema){
                     
                     $response          = $this->saswp_get_fields_by_schema_type($schema->ID);                          
                     $this->meta_fields = $response; 
                     
                        foreach ( $this->meta_fields as $meta_field ) {
                            
			if ( isset( $_POST[ $meta_field['id'] ] ) ) {
                            
				switch ( $meta_field['type'] ) {
                                    
                                        case 'media':                                                                                                  
                                                $media_key       = $meta_field['id'].'_detail';                                                                                            
                                                $media_height    = sanitize_text_field( $_POST[ $meta_field['id'].'_height' ] );
                                                $media_width     = sanitize_text_field( $_POST[ $meta_field['id'].'_width' ] );
                                                $media_thumbnail = sanitize_text_field( $_POST[ $meta_field['id'].'_thumbnail' ] );
                                                $media_detail = array(                                                    
                                                    'height' =>$media_height,
                                                    'width' =>$media_width,
                                                    'thumbnail' =>$media_thumbnail,
                                                );                                                
                                                update_post_meta( $post_id, $media_key, $media_detail);                                                    
                                                break;
					case 'email':
						$_POST[ $meta_field['id'] ] = sanitize_email( $_POST[ $meta_field['id'] ] );
						break;
					case 'text':
						$_POST[ $meta_field['id'] ] = sanitize_text_field( $_POST[ $meta_field['id'] ] );
						break;
                                            
				}
				update_post_meta( $post_id, $meta_field['id'], $_POST[ $meta_field['id'] ] );
			} else if ( $meta_field['type'] === 'checkbox' ) {
				update_post_meta( $post_id, $meta_field['id'], '0' );
			}
		   }                                
                 }                                                                                      
            }                                                                		                                                                               
	}
        public function saswp_get_sub_business_ajax(){
            
            if ( ! isset( $_GET['saswp_security_nonce'] ) ){
                return; 
            }
            if ( !wp_verify_nonce( $_GET['saswp_security_nonce'], 'saswp_ajax_check_nonce' ) ){
               return;  
            } 
            $business_type = sanitize_text_field($_GET['business_type']);
           
            $response = $this->saswp_get_sub_business_array($business_type); 
            
           if($response){               
              $this->options_response = $response; 
              echo json_encode(array('status'=>'t', 'result'=>$response)); 
           }else{
              echo json_encode(array('status'=>'f', 'result'=>'data not available')); 
           }
            wp_die();
        }
        public function saswp_get_sub_business_array($business_type){
            
            $sub_business_options = array();
            
            switch ($business_type) {
                        case 'automotivebusiness':
                           $sub_business_options = array(
                                     'autobodyshop'      => 'Auto Body Shop',
                                     'autodealer'        => 'Auto Dealer',
                                     'autopartsstore'    => 'Auto Parts Store',
                                     'autorental'        => 'Auto Rental',
                                     'autorepair'        => 'Auto Repair',
                                     'autowash'          => 'Auto Wash',
                                     'gasstation'        => 'Gas Station',
                                     'motorcycledealer'  => 'Motorcycle Dealer',
                                     'motorcyclerepair'  => 'Motorcycle Repair'
                                 ); 
                            break;
                        case 'emergencyservice':
                            $sub_business_options = array(
                                     'firestation'    => 'Fire Station',
                                     'hospital'       => 'Hospital',
                                     'policestation'  => 'Police Station',                                    
                                 ); 
                            break;
                        case 'entertainmentbusiness':
                           $sub_business_options = array(
                                      'adultentertainment' => 'Adult Entertainment',
                                      'amusementpark'      => 'Amusement Park',
                                      'artgallery'         => 'Art Gallery',
                                      'casino'             => 'Casino',
                                      'comedyclub'         => 'Comedy Club',
                                      'movietheater'       => 'Movie Theater',
                                      'nightclub'          => 'Night Club',
                                      
                                 );  
                            break;
                        case 'financialservice':
                            $sub_business_options = array(
                                      'accountingservice'  => 'Accounting Service',
                                      'automatedteller'    => 'Automated Teller',
                                      'bankorcredit_union' => 'Bank Or Credit Union',
                                      'insuranceagency'    => 'Insurance Agency',                                      
                                      
                                 );   
                            break;
                        case 'foodestablishment':
                             $sub_business_options = array(
                                      'bakery'             => 'Bakery',
                                      'barorpub'           => 'Bar Or Pub',
                                      'brewery'            => 'Brewery',
                                      'cafeorcoffee_shop'  => 'Cafe Or Coffee Shop', 
                                      'fastfoodrestaurant' => 'Fast Food Restaurant',
                                      'icecreamshop'       => 'Ice Cream Shop',
                                      'restaurant'         => 'Restaurant',
                                      'winery'             => 'Winery', 
                                      
                                 );
                            break;
                        case 'healthandbeautybusiness':
                            $sub_business_options = array(
                                      'beautysalon'  => 'Beauty Salon',
                                      'dayspa'       => 'DaySpa',
                                      'hairsalon'    => 'Hair Salon',
                                      'healthclub'   => 'Health Club', 
                                      'nailsalon'    => 'Nail Salon',
                                      'tattooparlor' => 'Tattoo Parlor',                                                                          
                                 );   
                            break;
                        case 'homeandconstructionbusiness':
                            $sub_business_options = array(
                                      'electrician'       => 'Electrician',
                                      'generalcontractor' => 'General Contractor',
                                      'hvacbusiness'      => 'HVAC Business',
                                      'locksmith'         => 'Locksmith', 
                                      'movingcompany'     => 'Moving Company',
                                      'plumber'           => 'Plumber',       
                                      'roofingcontractor' => 'Roofing Contractor',       
                                 );   
                            break;
                        case 'legalservice':
                            $sub_business_options = array(
                                      'attorney' => 'Attorney',
                                      'notary'   => 'Notary',                                            
                                 );  
                            break;
                        case 'lodgingbusiness':
                             $sub_business_options = array(
                                      'bedandbreakfast' => 'Bed And Breakfast',
                                      'campground'      => 'Campground',
                                      'hostel'          => 'Hostel',
                                      'hotel'           => 'Hotel',
                                      'motel'           => 'Motel',
                                      'resort'          => 'Resort',
                                 );   
                            break;
                        case 'sportsactivitylocation':
                             $sub_business_options = array(
                                      'bowlingalley'        => 'Bowling Alley',
                                      'exercisegym'         => 'Exercise Gym',
                                      'golfcourse'          => 'Golf Course',
                                      'healthclub'          => 'Health Club',
                                      'publicswimming_pool' => 'Public Swimming Pool',
                                      'skiresort'           => 'Ski Resort',
                                      'sportsclub'          => 'Sports Club',
                                      'stadiumorarena'      => 'Stadium Or Arena',
                                      'tenniscomplex'       => 'Tennis Complex'
                                 );  
                            break;
                        case 'store':
                             $sub_business_options = array(
                                        'autopartsstore'        => 'Auto Parts Store',
                                        'bikestore'             => 'Bike Store',
                                        'bookstore'             => 'Book Store',
                                        'clothingstore'         => 'Clothing Store',
                                        'computerstore'         => 'Computer Store',
                                        'conveniencestore'      => 'Convenience Store',
                                        'departmentstore'       => 'Department Store',
                                        'electronicsstore'      => 'Electronics Store',
                                        'florist'               => 'Florist',
                                        'furniturestore'        => 'Furniture Store',
                                        'gardenstore'           => 'Garden Store',
                                        'grocerystore'          => 'Grocery Store',
                                        'hardwarestore'         => 'Hardware Store',
                                        'hobbyshop'             => 'Hobby Shop',
                                        'homegoodsstore'        => 'HomeGoods Store',
                                        'jewelrystore'          => 'Jewelry Store',
                                        'liquorstore'           => 'Liquor Store',
                                        'mensclothingstore'     => 'Mens Clothing Store',
                                        'mobilephonestore'      => 'Mobile Phone Store',
                                        'movierentalstore'      => 'Movie Rental Store',
                                        'musicstore'            => 'Music Store',
                                        'officeequipmentstore'  => 'Office Equipment Store',
                                        'outletstore'           => 'Outlet Store',
                                        'pawnshop'              => 'Pawn Shop',
                                        'petstore'              => 'Pet Store',
                                        'shoestore'             => 'Shoe Store',
                                        'sportinggoodsstore'    => 'Sporting Goods Store',
                                        'tireshop'              => 'Tire Shop',
                                        'toystore'              => 'Toy Store',
                                        'wholesalestore'        => 'Wholesale Store'
                                 );  
                            break;
                        default:
                            break;
                    }
            return  $sub_business_options;       
        }
        
        public function saswp_get_fields_by_schema_type( $schema_id ) {  
            
            global $post;
            global $sd_data;  
            
            $image_id 	   = get_post_thumbnail_id();
            $image_details = wp_get_attachment_image_src($image_id, 'full');
            
            if(empty($image_details[0]) || $image_details[0] === NULL ){
             
                if(isset($sd_data['sd_logo'])){
                    $image_details[0] = $sd_data['sd_logo']['url'];
                }
                
	    }
            $current_user       = wp_get_current_user();
            $author_details	= get_avatar_data($current_user->ID);           
            $schema_type        = esc_sql ( get_post_meta($schema_id, 'schema_type', true)  );  
            
            $business_type    = esc_sql ( get_post_meta($schema_id, 'saswp_business_type', true)  ); 
            $business_name    = esc_sql ( get_post_meta($schema_id, 'saswp_business_name', true)  ); 
            $business_details = esc_sql ( get_post_meta($schema_id, 'saswp_local_business_details', true)  );
            $dayoftheweek     = get_post_meta ($schema_id, 'saswp_dayofweek', true); 
            
            $saswp_business_type_key   = 'saswp_business_type_'.$schema_id;
            $saved_business_type       = get_post_meta( $post->ID, $saswp_business_type_key, true );
            $saved_saswp_business_name = get_post_meta( $post->ID, 'saswp_business_name_'.$schema_id, true );    
            
            if($saved_business_type){
              $business_type = $saved_business_type;
            }
            if($saved_saswp_business_name){
              $business_name = $saved_saswp_business_name;
            }
            $meta_field = array();
            switch ($schema_type) {
                
                case 'local_business':
                    $sub_business_options = array();                        
                    switch ($business_type) {
                        case 'automotivebusiness':
                            $this->options_response = $this->saswp_get_sub_business_array($business_type);   
                            break;
                        case 'emergencyservice':
                           
                            $this->options_response = $this->saswp_get_sub_business_array($business_type);   
                            break;
                        case 'entertainmentbusiness':
                            $this->options_response = $this->saswp_get_sub_business_array($business_type);   
                            break;
                        case 'financialservice':
                            $this->options_response = $this->saswp_get_sub_business_array($business_type);   
                            break;
                        case 'foodestablishment':
                            $this->options_response = $this->saswp_get_sub_business_array($business_type);   
                            break;
                        case 'healthandbeautybusiness':
                            $this->options_response = $this->saswp_get_sub_business_array($business_type);   
                            break;
                        case 'homeandconstructionbusiness':
                            $this->options_response = $this->saswp_get_sub_business_array($business_type);   
                            break;
                        case 'legalservice':
                            $this->options_response = $this->saswp_get_sub_business_array($business_type);   
                            break;
                        case 'lodgingbusiness':
                            $this->options_response = $this->saswp_get_sub_business_array($business_type);   
                            break;
                        case 'sportsactivitylocation':
                            $this->options_response = $this->saswp_get_sub_business_array($business_type);   
                            break;
                        case 'store':
                            $this->options_response = $this->saswp_get_sub_business_array($business_type);   
                            break;
                        default:                            
                            break;
                    }                                         
                   
                    if(!empty($this->options_response)){
                        
                       $sub_business_options = array(
                            'label'     => 'Sub Business Type',
                            'id'        => 'saswp_business_name_'.$schema_id,
                            'type'      => 'select',
                            'options'   => $this->options_response,
                            'default'   => $business_name  
                       ); 
                       
                    }
                    
                    $meta_field = array(                   
                    array(
                            'label'   => 'Business Type',
                            'id'      => 'saswp_business_type_'.$schema_id,
                            'type'    => 'select',
                            'default' => $business_type,
                            'options' => array(
                                    'animalshelter'                 => 'Animal Shelter',
                                    'automotivebusiness'            => 'Automotive Business',
                                    'childcare'                     => 'ChildCare',
                                    'dentist'                       => 'Dentist',
                                    'drycleaningorlaundry'          => 'Dry Cleaning Or Laundry',
                                    'emergencyservice'              => 'Emergency Service',
                                    'employmentagency'              => 'Employment Agency',
                                    'entertainmentbusiness'         => 'Entertainment Business',
                                    'financialservice'              => 'Financial Service',
                                    'foodestablishment'             => 'Food Establishment',
                                    'governmentoffice'              => 'Government Office',
                                    'healthandbeautybusiness'       => 'Health And Beauty Business',
                                    'homeandconstructionbusiness'   => 'Home And Construction Business',
                                    'internetcafe'                  => 'Internet Cafe',
                                    'legalservice'                  => 'Legal Service',
                                    'library'                       => 'Library',
                                    'lodgingbusiness'               => 'Lodging Business',
                                    'professionalservice'           => 'Professional Service',
                                    'radiostation'                  => 'Radio Station',
                                    'realestateagent'               => 'Real Estate Agent',
                                    'recyclingcenter'               => 'Recycling Center',
                                    'selfstorage'                   => 'Self Storage',
                                    'shoppingcenter'                => 'Shopping Center',
                                    'sportsactivitylocation'        => 'Sports Activity Location',
                                    'store'                         => 'Store',
                                    'televisionstation'             => 'Television Station',
                                    'touristinformationcenter'      => 'Tourist Information Center',
                                    'travelagency'                  => 'Travel Agency',
                            )
                        ),
                         $sub_business_options,
                        array(
                            'label'   => 'Business Name',
                            'id'      => 'local_business_name_'.$schema_id,
                            'type'    => 'text',
                            'default' => $business_details['local_business_name']    
                       ),
                        
                        array(
                            'label' => 'URL',
                            'id' => 'local_business_name_url_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink()
                         ),
			array(
                            'label' => 'Description',
                            'id' => 'local_business_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => $post->post_excerpt
                         ),
                        array(
                            'label' => 'Street Address',
                            'id' => 'local_street_address_'.$schema_id,
                            'type' => 'text',
                            'default' => $business_details['local_street_address']    
                       ),
                        array(
                            'label' => 'City',
                            'id' => 'local_city_'.$schema_id,
                            'type' => 'text',
                            'default' => $business_details['local_city']
                       ),
                        array(
                            'label' => 'State',
                            'id' => 'local_state_'.$schema_id,
                            'type' => 'text',
                            'default' => $business_details['local_state']
                       ),
                        array(
                            'label' => 'Postal Code',
                            'id' => 'local_postal_code_'.$schema_id,
                            'type' => 'text',
                            'default' => $business_details['local_postal_code']
                       ),
                        array(
                            'label' => 'Phone',
                            'id' => 'local_phone_'.$schema_id,
                            'type' => 'text',
                            'default' => $business_details['local_phone']
                       ),
                        array(
                            'label' => 'Website',
                            'id' => 'local_website_'.$schema_id,
                            'type' => 'text',
                            'default' => $business_details['local_website']
                       ),
                        array(
                            'label' => 'Image',
                            'id' => 'local_business_logo_'.$schema_id,
                            'type' => 'media',
                            'default' => $business_details['local_business_logo']['url']
                       ),
                        array(
                            'label' => 'Operation Days',
                            'id' => 'saswp_dayofweek_'.$schema_id,
                            'type' => 'textarea',                                                           
                            'default' => $dayoftheweek
                       ),                        
                        array(
                            'label' => 'Price Range',
                            'id' => 'local_price_range_'.$schema_id,
                            'type' => 'text',
                            'default' => $business_details['local_price_range']
                       ),
                        array(
                            'label' => 'Aggregate Rating',
                            'id' => 'local_enable_rating_'.$schema_id,
                            'type' => 'checkbox',
                          //  'default' => saswp_remove_warnings($business_details, 'local_enable_rating', 'saswp_string')
                        ),
                        array(
                            'label' => 'Rating',
                            'id' => 'local_rating_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_remove_warnings($business_details, 'local_rating', 'saswp_string')
                        ),
                        array(
                            'label' => 'Number of Reviews',
                            'id' => 'local_review_count_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_remove_warnings($business_details, 'local_review_count', 'saswp_string')
                        ),
                    );
                    
                    break;
                
                case 'Blogposting':
                    $meta_field = array(
                    array(
                            'label' => 'Main Entity Of Page',
                            'id' => 'saswp_blogposting_main_entity_of_page_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink()
                    ),
                    array(
                            'label' => 'Headline',
                            'id' => 'saswp_blogposting_headline_'.$schema_id,
                            'type' => 'text',
                            'default'=> get_the_title()
                    ),
                    array(
                            'label' => 'Description',
                            'id' => 'saswp_blogposting_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => $post->post_excerpt,   
                    ),
                    array(
                            'label' => 'Name',
                            'id' => 'saswp_blogposting_name_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_title()
                    ), 
                    array(
                            'label' => 'URL',
                            'id' => 'saswp_blogposting_url_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink()
                    ),
                    array(
                            'label' => 'Date Published',
                            'id' => 'saswp_blogposting_date_published_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_date("Y-m-d")
                    ), 
                    array(
                            'label' => 'Date Modified',
                            'id' => 'saswp_blogposting_date_modified_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_modified_date("Y-m-d")
                    ),     
                    array(
                            'label' => 'Author Name',
                            'id' => 'saswp_blogposting_author_name_'.$schema_id,
                            'type' => 'text',
                            'default' => $current_user->display_name
                    ),
                    array(
                            'label' => 'Organization Name',
                            'id' => 'saswp_blogposting_organization_name_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                   ),
                     array(
                            'label' => 'Organization Logo',
                            'id' => 'saswp_blogposting_organization_logo_'.$schema_id,
                            'type' => 'media',
                            'default' => isset($sd_data['sd_logo']) ? $sd_data['sd_logo']['url'] : ''
                    )                         
                    );
                    break;
                
                case 'NewsArticle':
                    
                    $category_detail=get_the_category(get_the_ID());//$post->ID
                    $article_section = '';
                    
                    foreach($category_detail as $cd){
                        
                    $article_section =  $cd->cat_name;
                    
                    }
                    $word_count = saswp_reading_time_and_word_count();
                    
                    $meta_field = array(
                    array(
                            'label' => 'Main Entity Of Page',
                            'id' => 'saswp_newsarticle_main_entity_of_page_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink()
                    ),
                    array(
                            'label' => 'URL',
                            'id' => 'saswp_newsarticle_URL_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink(),
                    ),	
                    array(
                            'label' => 'Headline',
                            'id' => 'saswp_newsarticle_headline_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_title(),
                    ),
                    array(
                            'label' => 'Date Published',
                            'id' => 'saswp_newsarticle_date_published_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_date("Y-m-d")
                    ),
                    array(
                            'label' => 'Date Modified',
                            'id' => 'saswp_newsarticle_date_modified_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_modified_date("Y-m-d")
                    ),
                     array(
                            'label' => 'Description',
                            'id' => 'saswp_newsarticle_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => $post->post_excerpt
                    ),
                     array(
                            'label' => 'Article Section',
                            'id' => 'saswp_newsarticle_section_'.$schema_id,
                            'type' => 'text',
                            'default' => $article_section
                    ),
                    array(
                            'label' => 'Article Body',
                            'id' => 'saswp_newsarticle_body_'.$schema_id,
                            'type' => 'textarea',
                            'default' => get_the_content()
                    ),
                     array(
                            'label' => 'Name',
                            'id' => 'saswp_newsarticle_name_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_title()
                    ), 
                     array(
                            'label' => 'Thumbnail URL',
                            'id' => 'saswp_newsarticle_thumbnailurl_'.$schema_id,
                            'type' => 'text',
                            'default' => $image_details[0]
                    ),
                    array(
                            'label' => 'Word Count',
                            'id' => 'saswp_newsarticle_word_count_'.$schema_id,
                            'type' => 'text',
                            'default' => $word_count['word_count']
                    ),
                    array(
                            'label' => 'Time Required',
                            'id' => 'saswp_newsarticle_timerequired_'.$schema_id,
                            'type' => 'text',
                            'default' => $word_count['timerequired']
                    ),    
                    array(
                            'label' => 'Main Entity Id',
                            'id' => 'saswp_newsarticle_main_entity_id_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink()
                    ),
                    array(
                            'label' => 'Author Name',
                            'id' => 'saswp_newsarticle_author_name_'.$schema_id,
                            'type' => 'text',
                            'default' => $current_user->display_name
                    ), 
                    array(
                            'label' => 'Author Image',
                            'id' => 'saswp_newsarticle_author_image_'.$schema_id,
                            'type' => 'media',
                            'default' => isset($author_details['url']) ? $author_details['url']: ''
                    ),
                    array(
                            'label' => 'Organization Name',
                            'id' => 'saswp_newsarticle_organization_name_'.$schema_id,
                            'type' => 'text',
                            'default'=> saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                    ),
                    array(
                            'label' => 'Organization Logo',
                            'id' => 'saswp_newsarticle_organization_logo_'.$schema_id,
                            'type' => 'media',
                            'default' => isset($sd_data['sd_logo'])? $sd_data['sd_logo']['url']:''
                    ),    
                    );
                    break;
                
                case 'WebPage':
                    $meta_field = array(
                    array(
                            'label' => 'Name',
                            'id' => 'saswp_webpage_name_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_title()
                    ),
                    array(
                            'label' => 'URL',
                            'id' => 'saswp_webpage_url_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink()
                    ),
                    array(
                            'label' => 'Description',
                            'id' => 'saswp_webpage_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => $post->post_excerpt
                    ),
                    array(
                            'label' => 'Main Entity Of Page',
                            'id' => 'saswp_webpage_main_entity_of_page_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink()
                    ), 
                    array(
                            'label' => 'Image',
                            'id' => 'saswp_webpage_image_'.$schema_id,
                            'type' => 'media',
                            'default' => $image_details[0]
                    ), 
                    array(
                            'label' => 'Headline',
                            'id' => 'saswp_webpage_headline_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_title(),
                    ),
                   
                    array(
                            'label' => 'Date Published',
                            'id' => 'saswp_webpage_date_published_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_date("Y-m-d")
                    ),
                    array(
                            'label' => 'Date Modified',
                            'id' => 'saswp_webpage_date_modified_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_modified_date("Y-m-d")
                    ),
                    array(
                            'label' => 'Author Name',
                            'id' => 'saswp_webpage_author_name_'.$schema_id,
                            'type' => 'text',
                            'default' => $current_user->display_name
                    ),
                    array(
                            'label' => 'Organization Name',
                            'id' => 'saswp_webpage_organization_name_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                    ), 
                     array(
                            'label' => 'Organization Logo',
                            'id' => 'saswp_webpage_organization_logo_'.$schema_id,
                            'type' => 'media',
                            'default' => isset($sd_data['sd_logo']) ? $sd_data['sd_logo']['url']:''
                    ),     
                    );
                    break;
                
                case 'Article':                                        
                    $meta_field = array(
                    array(
                            'label' => 'Main Entity Of Page',
                            'id' => 'saswp_article_main_entity_of_page_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink()
                    ),
                    array(
                            'label' => 'Image',
                            'id' => 'saswp_article_image_'.$schema_id,
                            'type' => 'media',
                            'default' => $image_details[0]
                    ),
                    array(
                            'label' => 'Headline',
                            'id' => 'saswp_article_headline_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_title()
                    ),
                    array(
                            'label' => 'Description',
                            'id' => 'saswp_article_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => $post->post_excerpt
                    ) , 
                    array(
                            'label' => 'Date Published',
                            'id' => 'saswp_article_date_published_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_date("Y-m-d")
                    ), 
                    array(
                            'label' => 'Date Modified',
                            'id' => 'saswp_article_date_modified_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_modified_date("Y-m-d")
                    ),
                    array(
                            'label' => 'Author Name',
                            'id' => 'saswp_article_author_name_'.$schema_id,
                            'type' => 'text',
                            'default' => $current_user->display_name
                    ),
                    array(
                            'label' => 'Organization Name',
                            'id' => 'saswp_article_organization_name_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                    ),
                    array(
                            'label' => 'Organization Logo',
                            'id' => 'saswp_article_organization_logo_'.$schema_id,
                            'type' => 'media',
                            'default' => isset($sd_data['sd_logo']) ? $sd_data['sd_logo']['url']:''
                    )                                                     
                    );
                    break;
                
                case 'Recipe':
                    $meta_field = array(
                    array(
                            'label' => 'URL',
                            'id' => 'saswp_recipe_url_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink(),
                    ),
                    array(
                            'label' => 'Name',
                            'id' => 'saswp_recipe_name_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_title()
                    ),
                    array(
                            'label' => 'Date Published',
                            'id' => 'saswp_recipe_date_published_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_date("Y-m-d")
                    ),
                    array(
                            'label' => 'Date Modified',
                            'id' => 'saswp_recipe_date_modified_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_modified_date("Y-m-d")
                    ),
                    array(
                            'label' => 'Description',
                            'id' => 'saswp_recipe_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => $post->post_excerpt
                    ),
                    array(
                            'label' => 'Main Entity Id',
                            'id' => 'saswp_recipe_main_entity_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink()
                    ),
                    array(
                            'label' => 'Author Name',
                            'id' => 'saswp_recipe_author_name_'.$schema_id,
                            'type' => 'text',
                            'default' => $current_user->display_name
                    ),
                    array(
                            'label' => 'Author Image',
                            'id' => 'saswp_recipe_author_image_'.$schema_id,
                            'type' => 'media',
                            'default' => $author_details['url']
                    ),
                    array(
                            'label' => 'Organization Name',
                            'id' => 'saswp_recipe_organization_name_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                    ),
                    array(
                            'label' => 'Organization Logo',
                            'id' => 'saswp_recipe_organization_logo_'.$schema_id,
                            'type' => 'media',
                            'default' => $sd_data['sd_logo']['url']
                    ),                                                                                            
                    array(
                            'label' => 'Prepare Time',
                            'id' => 'saswp_recipe_preptime_'.$schema_id,
                            'type' => 'text', 
                            'attributes' => array(
                                'placeholder' => 'PT20M'
                            ),
                    ),    
                    array(
                            'label' => 'Cook Time',
                            'id' => 'saswp_recipe_cooktime_'.$schema_id,
                            'type' => 'text', 
                            'attributes' => array(
                                'placeholder' => 'PT30M'
                            ),
                    ),
                    array(
                            'label' => 'Total Time',
                            'id' => 'saswp_recipe_totaltime_'.$schema_id,
                            'type' => 'text', 
                            'attributes' => array(
                                'placeholder' => 'PT50M'
                            ),
                    ),    
                    array(
                            'label' => 'Keywords',
                            'id' => 'saswp_recipe_keywords_'.$schema_id,
                            'type' => 'text',  
                            'attributes' => array(
                                'placeholder' => 'cake for a party, coffee'
                            ),
                    ),    
                    array(
                            'label' => 'Recipe Yield',
                            'id' => 'saswp_recipe_recipeyield_'.$schema_id,
                            'type' => 'text', 
                            'attributes' => array(
                                'placeholder' => '10 servings'
                            ),
                    ),    
                    array(
                            'label' => 'Recipe Category',
                            'id' => 'saswp_recipe_category_'.$schema_id,
                            'type' => 'text',
                            'attributes' => array(
                                'placeholder' => 'Dessert'
                            ),
                    ),
                    array(
                            'label' => 'Recipe Cuisine',
                            'id' => 'saswp_recipe_cuisine_'.$schema_id,
                            'type' => 'text', 
                            'attributes' => array(
                                'placeholder' => 'American'
                            ),
                    ),    
                    array(
                            'label' => 'Nutrition',
                            'id' => 'saswp_recipe_nutrition_'.$schema_id,
                            'type' => 'text',
                            'attributes' => array(
                                'placeholder' => '270 calories'
                            ),
                    ),
                    array(
                            'label' => 'Recipe Ingredient',
                            'id' => 'saswp_recipe_ingredient_'.$schema_id,
                            'type' => 'textarea',
                            'attributes' => array(
                                'placeholder' => '2 cups of flour; 3/4 cup white sugar;'
                            ),
                            'note' => 'Note: Separate Ingredient list by semicolon ( ; )'  
                    ), 
                    array(
                            'label' => 'Recipe Instructions',
                            'id' => 'saswp_recipe_instructions_'.$schema_id,
                            'type' => 'textarea',
                            'attributes' => array(
                                'placeholder' => 'Preheat the oven to 350 degrees F. Grease and flour a 9x9 inch pan; large bowl, combine flour, sugar, baking powder, and salt. pan.;'
                            ),
                            'note' => 'Note: Separate Ingredient step by semicolon ( ; )'  
                    ), 
                    array(
                            'label' => 'Video Name',
                            'id' => 'saswp_recipe_video_name_'.$schema_id,
                            'type' => 'text', 
                            'attributes' => array(
                                'placeholder' => 'Video Name'
                            ),
                    ),
                    array(
                            'label' => 'Video Description',
                            'id' => 'saswp_recipe_video_description_'.$schema_id,
                            'type' => 'text', 
                            'attributes' => array(
                                'placeholder' => 'Video Description'
                            ),
                    ),
                    array(
                            'label' => 'Video ThumbnailUrl',
                            'id' => 'saswp_recipe_video_thumbnailurl_'.$schema_id,
                            'type' => 'media',
                            
                    ),
                    array(
                            'label' => 'Video ContentUrl',
                            'id' => 'saswp_recipe_video_contenturl_'.$schema_id,
                            'type' => 'text',                            
                            'attributes' => array(
                                'placeholder' => 'http://www.example.com/video123.mp4'
                            ),
                    ),
                    array(
                            'label' => 'Video EmbedUrl',
                            'id' => 'saswp_recipe_video_embedurl_'.$schema_id,
                            'type' => 'text', 
                            'attributes' => array(
                                'placeholder' => 'http://www.example.com/videoplayer?video=123'
                            ),
                    ),
                    array(
                            'label' => 'Video Upload Date',
                            'id' => 'saswp_recipe_video_upload_date_'.$schema_id,
                            'type' => 'text', 
                            'attributes' => array(
                                'placeholder' => '2018-12-18'
                            ),
                    ),
                    array(
                            'label' => 'Video Duration',
                            'id' => 'saswp_recipe_video_duration_'.$schema_id,
                            'type' => 'text',
                            'attributes' => array(
                                'placeholder' => 'PT1M33S'
                            ),
                    ),                                                                            
                    );
                    break;
                
                case 'Product':                                                           
                    $product_schema_details = esc_sql ( get_post_meta($schema_id, 'saswp_product_schema_details', true)  );
                    $meta_field = array(
                        
                    array(
                            'label' => 'Name',
                            'id' => 'saswp_product_schema_name_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_remove_warnings($product_schema_details, 'saswp_product_schema_name', 'saswp_string'),
                    ),
                    array(
                            'label' => 'Description',
                            'id' => 'saswp_product_schema_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => saswp_remove_warnings($product_schema_details, 'saswp_product_schema_description', 'saswp_string'),
                    ), 
                        array(
                            'label' => 'Image',
                            'id' => 'saswp_product_schema_image_'.$schema_id,
                            'type' => 'media',
                            'default' => $product_schema_details['saswp_product_schema_image']['url'], 
                     ),
                         array(
                            'label' => 'Brand Name',
                            'id' => 'saswp_product_schema_brand_name_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_remove_warnings($product_schema_details, 'saswp_product_schema_brand_name', 'saswp_string'), 
                     ),
                        array(
                            'label' => 'Price',
                            'id' => 'saswp_product_schema_price_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_remove_warnings($product_schema_details, 'saswp_product_schema_price', 'saswp_string'), 
                     ),
                        array(
                            'label' => 'Price Valid Until',
                            'id' => 'saswp_product_schema_priceValidUntil_'.$schema_id,
                            'type' => 'text',
                             'default' => saswp_remove_warnings($product_schema_details, 'saswp_product_schema_priceValidUntil', 'saswp_string'), 
                       ),
                        array(
                            'label' => 'Currency',
                            'id' => 'saswp_product_schema_currency_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_remove_warnings($product_schema_details, 'saswp_product_schema_currency', 'saswp_string'), 
                      ),
                        array(
                            'label'   => 'Availability',
                            'id'      => 'saswp_product_schema_availability_'.$schema_id,
                            'type'    => 'select',
                            'default' => saswp_remove_warnings($product_schema_details, 'saswp_product_schema_availability', 'saswp_string'), 
                            'options' => array(
                                     'InStock'           => 'In Stock',
                                     'OutOfStock'        => 'Out Of Stock',
                                     'Discontinued'      => 'Discontinued',
                                     'PreOrder'          => 'Pre Order', 
                            ) 
                     ),
                        array(
                            'label'   => 'Condition',
                            'id'      => 'saswp_product_schema_condition_'.$schema_id,
                            'type'    => 'select',
                            'default' => saswp_remove_warnings($product_schema_details, 'saswp_product_schema_condition', 'saswp_string'), 
                            'options' => array(
                                     'NewCondition'              => 'New',
                                     'UsedCondition'             => 'Used',
                                     'RefurbishedCondition'      => 'Refurbished',
                                     'DamagedCondition'          => 'Damaged',   
                            ),
                     ),
                        array(
                            'label' => 'SKU',
                            'id' => 'saswp_product_schema_sku_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_remove_warnings($product_schema_details, 'saswp_product_schema_sku', 'saswp_string'), 
                     ),
                        array(
                            'label' => 'MPN',
                            'id' => 'saswp_product_schema_mpn_'.$schema_id,
                            'type' => 'text',
                            'note'   => 'OR',
                            'default' => saswp_remove_warnings($product_schema_details, 'saswp_product_schema_mpn', 'saswp_string'), 
                       ),
                        array(
                            'label' => 'ISBN',
                            'id' => 'saswp_product_schema_isbn_'.$schema_id,
                            'type' => 'text',
                            'note'   => 'OR',
                            'default' => saswp_remove_warnings($product_schema_details, 'saswp_product_schema_isbn', 'saswp_string'), 
                     ),
                        array(
                            'label' => 'GTIN8',
                            'id' => 'saswp_product_schema_gtin8_'.$schema_id,
                            'type' => 'text', 
                            'default' => saswp_remove_warnings($product_schema_details, 'saswp_product_schema_gtin8', 'saswp_string'),                           
                       ),
                        array(
                            'label' => 'Aggregate Rating',
                            'id' => 'saswp_product_schema_enable_rating_'.$schema_id,
                            'type' => 'checkbox',
                            //'default' => saswp_remove_warnings($product_schema_details, 'saswp_product_schema_enable_rating', 'saswp_string')
                        ),
                        array(
                            'label' => 'Rating',
                            'id' => 'saswp_product_schema_rating_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_remove_warnings($product_schema_details, 'saswp_product_schema_rating', 'saswp_string')
                        ),
                        array(
                            'label' => 'Number of Reviews',
                            'id' => 'saswp_product_schema_review_count_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_remove_warnings($product_schema_details, 'saswp_product_schema_review_count', 'saswp_string')
                        ),
                        
                    );
                                                                                                        
                       
                                     
                    break;
                
                case 'Service':
                    $service_schema_details = esc_sql ( get_post_meta($schema_id, 'saswp_service_schema_details', true)  );
                    $meta_field = array(
                    array(
                            'label' => 'Name',
                            'id' => 'saswp_service_schema_name_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_remove_warnings($service_schema_details, 'saswp_service_schema_name', 'saswp_string')
                    ),
                    array(
                            'label' => 'Service Type',
                            'id' => 'saswp_service_schema_type_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_remove_warnings($service_schema_details, 'saswp_service_schema_type', 'saswp_string')
                    ),
                    array(
                            'label' => 'Provider Name',
                            'id' => 'saswp_service_schema_provider_name_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_remove_warnings($service_schema_details, 'saswp_service_schema_provider_name', 'saswp_string')
                    ),
                    array(
                            'label' => 'Provider Type',
                            'id' => 'saswp_service_schema_provider_type_'.$schema_id,
                            'type' => 'select',
                            'options' => array(
                                     'Airline'                      => 'Airline',
                                     'Corporation'                  => 'Corporation',
                                     'EducationalOrganization'      => 'Educational Organization',
                                     'GovernmentOrganization'       => 'Government Organization',
                                     'LocalBusiness'                => 'Local Business',
                                     'MedicalOrganization'          => 'Medical Organization',  
                                     'NGO'                          => 'NGO', 
                                     'PerformingGroup'              => 'Performing Group', 
                                     'SportsOrganization'           => 'Sports Organization',
                            ),
                            'default' => saswp_remove_warnings($service_schema_details, 'saswp_service_schema_provider_type', 'saswp_string')
                    ),    
                    array(
                            'label' => 'Image',
                            'id' => 'saswp_service_schema_image_'.$schema_id,
                            'type' => 'media',
                            'default' => $service_schema_details['saswp_service_schema_image']['url']
                    ),
                    array(
                            'label' => 'Locality',
                            'id' => 'saswp_service_schema_locality_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_remove_warnings($service_schema_details, 'saswp_service_schema_locality', 'saswp_string')
                    ),
                    array(
                            'label' => 'Postal Code',
                            'id' => 'saswp_service_schema_postal_code_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_remove_warnings($service_schema_details, 'saswp_service_schema_postal_code', 'saswp_string')
                    ),
                    array(
                            'label' => 'Telephone',
                            'id' => 'saswp_service_schema_telephone_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_remove_warnings($service_schema_details, 'saswp_service_schema_telephone', 'saswp_string')
                    ),
                    array(
                            'label' => 'Price Range',
                            'id' => 'saswp_service_schema_price_range_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_remove_warnings($service_schema_details, 'saswp_service_schema_price_range', 'saswp_string')
                    ),
                    array(
                            'label' => 'Description',
                            'id' => 'saswp_service_schema_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => saswp_remove_warnings($service_schema_details, 'saswp_service_schema_description', 'saswp_string')
                    ),
                    array(
                            'label' => 'Area Served (City)',
                            'id' => 'saswp_service_schema_area_served_'.$schema_id,
                            'type' => 'textarea',
                            'default' => saswp_remove_warnings($service_schema_details, 'saswp_service_schema_area_served', 'saswp_string'),
                            'note'   => 'Note: Enter all the City name in comma separated',
                            'attributes' => array(
                                'placeholder' => 'New York, Los Angeles'
                            ),
                    ),
                    array(
                            'label' => 'Service Offer',
                            'id' => 'saswp_service_schema_service_offer_'.$schema_id,
                            'type' => 'textarea',
                            'default' => saswp_remove_warnings($service_schema_details, 'saswp_service_schema_service_offer', 'saswp_string'),
                            'note'   => 'Note: Enter all the service offer in comma separated',
                            'attributes' => array(
                                'placeholder' => 'Apartment light cleaning, carpet cleaning'
                            ),                                                        
                    ),
                        
                        array(
                            'label' => 'Aggregate Rating',
                            'id' => 'saswp_service_schema_enable_rating_'.$schema_id,
                            'type' => 'checkbox',
                           // 'default' => saswp_remove_warnings($service_schema_details, 'saswp_service_schema_enable_rating', 'saswp_string')
                        ),
                        array(
                            'label' => 'Rating',
                            'id' => 'saswp_service_schema_rating_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_remove_warnings($service_schema_details, 'saswp_service_schema_rating', 'saswp_string')
                        ),
                        array(
                            'label' => 'Number of Reviews',
                            'id' => 'saswp_service_schema_review_count_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_remove_warnings($service_schema_details, 'saswp_service_schema_review_count', 'saswp_string')
                        ),                                                
                        
                    );
                    break;
                
                case 'Review':
                    
                    
                    if(isset($_POST['saswp_review_schema_item_type_'.$schema_id])){
                                            
                    $reviewed_field = item_reviewed_fields(sanitize_text_field($_POST['saswp_review_schema_item_type_'.$schema_id]), $post_specific=1, $schema_id);    
                        
                        
                    }else{
                    
                    $item_type_by_post =  esc_sql ( get_post_meta($post->ID, 'saswp_review_schema_item_type_'.$schema_id, true)  );
                    
                    if($item_type_by_post){
                     
                    $reviewed_field = item_reviewed_fields($item_type_by_post, $post_specific=1, $schema_id);        
                        
                    }else{
                     
                    $service_schema_details = esc_sql ( get_post_meta($schema_id, 'saswp_review_schema_details', true)  );
                    $reviewed_field = item_reviewed_fields($service_schema_details['saswp_review_schema_item_type'], $post_specific=1, $schema_id);    
                        
                    }
                                            
                    }
                                        
                    $meta_field = array(
                    array(
                            'label' => 'Item Reviewed Type',
                            'id' => 'saswp_review_schema_item_type_'.$schema_id,
                            'type' => 'select',
                            'options' => array(
                                     'Article'               => 'Article',
                                     'Adultentertainment'    => 'Adult Entertainment',
                                     'Blog'                  => 'Blog',
                                     'Book'                  => 'Book',
                                     'Casino'                => 'Casino', 
                                     'Diet'                  => 'Diet',
                                     'Episode'               => 'Episode',
                                     'ExercisePlan'          => 'Exercise Plan',  
                                     'Game'                  => 'Game', 
                                     'Movie'                 => 'Movie', 
                                     'MusicPlaylist'         => 'Music Playlist',                                      
                                     'MusicRecording'        => 'MusicRecording',
                                     'Photograph'            => 'Photograph',
                                     //'Recipe'                => 'Recipe',
                                     'Restaurant'            => 'Restaurant', 
                                     'Series'                => 'Series',
                                    // 'SoftwareApplication'   => 'Software Application',
                                     'VisualArtwork'         => 'Visual Artwork',  
                                     'Webpage'               => 'Webpage', 
                                     'WebSite'               => 'WebSite',
                            ),                            
                            'default' => saswp_remove_warnings($service_schema_details, 'saswp_review_schema_item_type', 'saswp_string')
                         ),                                            
                        array(
                            'label' => 'Review Rating',
                            'id' => 'saswp_review_schema_enable_rating_'.$schema_id,
                            'type' => 'checkbox',
                           // 'default' => saswp_remove_warnings($service_schema_details, 'saswp_review_schema_enable_rating', 'saswp_string')
                        ),
                        array(
                            'label' => 'Rating Value',
                            'id' => 'saswp_review_schema_rating_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_remove_warnings($service_schema_details, 'saswp_review_schema_rating', 'saswp_string')
                        ),
                        array(
                            'label' => 'Best Rating',
                            'id' => 'saswp_review_schema_review_count_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_remove_warnings($service_schema_details, 'saswp_review_schema_review_count', 'saswp_string')
                        ),
                        
                        
                    );
                    $meta_field = array_merge($meta_field, $reviewed_field);                    
                    break;
                
                case 'AudioObject':
                    
                    $service_schema_details = esc_sql ( get_post_meta($schema_id, 'saswp_audio_schema_details', true)  );
                    $meta_field = array(
                    
                    array(
                            'label' => 'Name',
                            'id' => 'saswp_audio_schema_name_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_remove_warnings($service_schema_details, 'saswp_audio_schema_name', 'saswp_string')
                    ),
                    array(
                            'label' => 'Description',
                            'id' => 'saswp_audio_schema_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => saswp_remove_warnings($service_schema_details, 'saswp_audio_schema_description', 'saswp_string')
                    ),
                    array(
                            'label' => 'Content Url',
                            'id' => 'saswp_audio_schema_contenturl_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_remove_warnings($service_schema_details, 'saswp_audio_schema_contenturl', 'saswp_string')
                    ),
                   array(
                            'label' => 'Duration',
                            'id' => 'saswp_audio_schema_duration_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_remove_warnings($service_schema_details, 'saswp_audio_schema_duration', 'saswp_string')
                    ),
                     array(
                            'label' => 'Encoding Format',
                            'id' => 'saswp_audio_schema_encoding_format_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_remove_warnings($service_schema_details, 'saswp_audio_schema_encoding_format', 'saswp_string')
                    ),                           
                    array(
                            'label' => 'Date Published',
                            'id' => 'saswp_audio_schema_date_published_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_date("Y-m-d")
                    ),
                    array(
                            'label' => 'Date Modified',
                            'id' => 'saswp_audio_schema_date_modified_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_modified_date("Y-m-d")
                    ),
                    array(
                            'label' => 'Author',
                            'id' => 'saswp_audio_schema_author_name_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_remove_warnings($service_schema_details, 'saswp_audio_author_name', 'saswp_string')
                    ),    
                                                
                    );
                    break;
                
                case 'VideoObject':
                    $meta_field = array(
                    array(
                            'label' => 'URL',
                            'id' => 'saswp_video_object_url_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink()
                    ),
                    array(
                            'label' => 'Headline',
                            'id' => 'saswp_video_object_headline_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_title()
                    ),
                    array(
                            'label' => 'Date Published',
                            'id' => 'saswp_video_object_date_published_'.$schema_id,
                            'type' => 'text',
                             'default' => get_the_date("Y-m-d")
                    ), 
                    array(
                            'label' => 'Date date Modified',
                            'id' => 'saswp_video_object_date_modified_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_modified_date("Y-m-d")
                    ),
                    array(
                            'label' => 'Description',
                            'id' => 'saswp_video_object_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => $post->post_excerpt
                    ),
                    array(
                            'label' => 'Name',
                            'id' => 'saswp_video_object_name_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_title()
                    ),
                    array(
                            'label' => 'Upload Date',
                            'id' => 'saswp_video_object_upload_date_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_date("Y-m-d")
                    ),
                    array(
                            'label' => 'Thumbnail Url',
                            'id' => 'saswp_video_object_thumbnail_url_'.$schema_id,
                            'type' => 'text',
                            'default' => $image_details[0]
                    ),
                    array(
                            'label' => 'Main Entity Id',
                            'id' => 'saswp_video_object_main_entity_id_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink()
                    ),
                    array(
                            'label' => 'Author Name',
                            'id' => 'saswp_video_object_author_name_'.$schema_id,
                            'type' => 'text',
                            'default' => $current_user->display_name    
                    ),
                    array(
                            'label' => 'Author Image',
                            'id' => 'saswp_video_object_author_image_'.$schema_id,
                            'type' => 'media',
                            'default' => $author_details['url']   
                    ),
                    array(
                            'label' => 'Organization Name',
                            'id' => 'saswp_video_object_organization_name_'.$schema_id,
                            'type' => 'text',
                            'default' =>  $sd_data['sd_name']
                    ),
                    array(
                            'label' => 'Organization Logo',
                            'id' => 'saswp_video_object_organization_logo_'.$schema_id,
                            'type' => 'media',
                            'default' => $sd_data['sd_logo']['url']
                    ),    
                   );
                    break;
                
                case 'qanda':
                    
                    $meta_field = array(
                    array(
                            'label' => 'Question Title',
                            'id' => 'saswp_qa_question_title_'.$schema_id,
                            'type' => 'text',                           
                    ),
                    array(
                            'label' => 'Question Description',
                            'id' => 'saswp_qa_question_description_'.$schema_id,
                            'type' => 'text',                           
                    ),                    
                    array(
                            'label' => 'Question Upvote Count',
                            'id' => 'saswp_qa_upvote_count_'.$schema_id,
                            'type' => 'text',                           
                    ),
                    array(
                            'label' => 'Question Date Created',
                            'id' => 'saswp_qa_date_created_'.$schema_id,
                            'type' => 'text',                           
                    ),    
                    array(
                            'label' => 'Author Name',
                            'id' => 'saswp_qa_question_author_name_'.$schema_id,
                            'type' => 'text',                           
                    ),    
                    array(
                            'label' => 'Accepted Answer Text',
                            'id' => 'saswp_qa_accepted_answer_text_'.$schema_id,
                            'type' => 'text',                           
                    ),
                    array(
                            'label' => 'Accepted Answer Date Created',
                            'id' => 'saswp_qa_accepted_answer_date_created_'.$schema_id,
                            'type' => 'text',                           
                    ),
                    array(
                            'label' => 'Accepted Answer Upvote Count',
                            'id' => 'saswp_qa_accepted_answer_upvote_count_'.$schema_id,
                            'type' => 'text',                           
                    ),
                    array(
                            'label' => 'Accepted Answer Url',
                            'id' => 'saswp_qa_accepted_answer_url_'.$schema_id,
                            'type' => 'text',                           
                    ),
                    array(
                            'label' => 'Accepted Answer Author Name',
                            'id' => 'saswp_qa_accepted_author_name_'.$schema_id,
                            'type' => 'text',                           
                    ),    
                                                
                    array(
                            'label' => 'Suggested Answer Text',
                            'id' => 'saswp_qa_suggested_answer_text_'.$schema_id,
                            'type' => 'text',                           
                    ),
                    array(
                            'label' => 'Suggested Answer Date Created',
                            'id' => 'saswp_qa_suggested_answer_date_created_'.$schema_id,
                            'type' => 'text',                           
                    ),
                    array(
                            'label' => 'Suggested Answer Upvote Count',
                            'id' => 'saswp_qa_suggested_answer_upvote_count_'.$schema_id,
                            'type' => 'text',                           
                    ),
                    array(
                            'label' => 'Suggested Answer Url',
                            'id' => 'saswp_qa_suggested_answer_url_'.$schema_id,
                            'type' => 'text',                           
                    ),
                    array(
                            'label' => 'Suggested Answer Author Name',
                            'id' => 'saswp_qa_suggested_author_name_'.$schema_id,
                            'type' => 'text',                           
                    ),                        
                        
                   );
                    break;
                
                default:
                    break;
            }                           
          return $meta_field;
	}
}
if (class_exists('saswp_post_specific')) {
	$object = new saswp_post_specific();
        $object->saswp_post_specific_hooks();
};


