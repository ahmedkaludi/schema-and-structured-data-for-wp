<?php
class saswp_post_specific {
	private $screen = array(
		'post',		
	);
	private $meta_fields = array(
			
	);
        protected $all_schema = null;
        protected $options_response = array();
        protected $modify_schema_post_enable = false;
        public function __construct() {                               
	}
        public function saswp_post_specific_hooks(){
                $this->saswp_get_all_schema_list();
		add_action( 'add_meta_boxes', array( $this, 'saswp_post_specifc_add_meta_boxes' ) );		
		add_action( 'save_post', array( $this, 'saswp_post_specific_save_fields' ) );
                add_action( 'wp_ajax_saswp_get_sub_business_ajax', array($this,'saswp_get_sub_business_ajax'));
                
                add_action( 'wp_ajax_saswp_modify_schema_post_enable', array($this,'saswp_modify_schema_post_enable'));
        }
        public function saswp_get_all_schema_list(){
               $all_schema = get_posts(
                    array(
                            'post_type' 	 => 'saswp',
                            'posts_per_page' => -1,   
                            'post_status' => 'publish',
                    )
                 );  
                 $this->all_schema = $all_schema;                             
        }

        public function saswp_post_specifc_add_meta_boxes($post) {
            if(count($this->all_schema)>0 && get_post_status($post->ID)=='publish'){
             foreach ( $this->screen as $single_screen ) {
			add_meta_box(
				'post_specific',
				__( 'Post Specific Schema', 'schema-and-structured-data-for-wp' ),
				array( $this, 'saswp_post_meta_box_callback' ),
				$single_screen,
				'advanced',
				'default'
			);
		}   
            }		
	}
        
        public function saswp_post_meta_box_fields($post){            
             if(count($this->all_schema)>1){
                    $tabs = '';
                    $tabs_fields = '';
                    
                 foreach($this->all_schema as $key => $schema){
                     $response = $this->saswp_get_fields_by_schema_type($schema->ID);                     
                     $this->meta_fields = $response;
                     $output = $this->saswp_saswp_post_specific( $post, $schema->ID );                    
                     if($key==0){
                     $tabs .='<li><a data-id="saswp_specific_'.esc_attr($schema->ID).'" class="saswp-tab-links selected">'.esc_attr($schema->post_title).'</a></li>';    
                     $tabs_fields .= '<div data-id="'.esc_attr($schema->ID).'" id="saswp_specific_'.esc_attr($schema->ID).'" class="saswp-post-specific-wrapper">';
                     $tabs_fields .= '<table class="form-table"><tbody>' . $output . '</tbody></table>';
                     $tabs_fields .= '</div>';
                     }else{
                     $tabs .='<li><a data-id="saswp_specific_'.esc_attr($schema->ID).'" class="saswp-tab-links">'.esc_attr($schema->post_title).'</a></li>';    
                     $tabs_fields .= '<div data-id="'.esc_attr($schema->ID).'" id="saswp_specific_'.esc_attr($schema->ID).'" class="saswp-post-specific-wrapper saswp_hide">';
                     $tabs_fields .= '<table class="form-table"><tbody>' . $output . '</tbody></table>';
                     $tabs_fields .= '</div>';
                     
                     }                                          
                 }   
                                  
                echo '<div class="saswp-tab saswp-post-specific-tab-wrapper">';                
		echo '<ul class="saswp-tab-nav">';
                echo $tabs;                
                echo '</ul>';                
                echo '</div>';                
                echo '<div class="saswp-post-specific-container">';                
                echo $tabs_fields;                                 
                echo '</div>';
                                  
                }else{
                    
                 $all_schema = $this->all_schema;                  
                 $response = $this->saswp_get_fields_by_schema_type($all_schema[0]->ID); 
                
                 $this->meta_fields = $response;
                 $output = $this->saswp_saswp_post_specific( $post, $all_schema[0]->ID );                    
                 $tabs_fields .= '<div id="saswp_specific_'.esc_attr($all_schema[0]->ID).'" class="saswp-post-specific-wrapper">';
                 $tabs_fields .= '<table class="form-table"><tbody>' . $output . '</tbody></table>';
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
                $image_id     = get_post_thumbnail_id();
                $image_details = wp_get_attachment_image_src($image_id, 'full');
                if(empty($image_details[0]) || $image_details[0] === NULL ){
                $image_details[0] = $sd_data['sd_logo']['url'];
                }
                $current_user = wp_get_current_user();
                $author_details	= get_avatar_data($current_user->ID);                
                $schema_type  = esc_sql ( get_post_meta($schema_id, 'schema_type', true)  );  
		$output = '';
                $this->meta_fields = array_filter($this->meta_fields);
		foreach ( $this->meta_fields as $meta_field ) {
                        $input ='';
			$label = '<label for="' . $meta_field['id'] . '">' . $meta_field['label'] . '</label>';
			$meta_value = get_post_meta( $post->ID, $meta_field['id'], true );
			if ( empty( $meta_value ) ) {
				$meta_value = $meta_field['default'];                                 
                        }
			switch ( $meta_field['type'] ) {
				case 'media':
                                        $media_value = array();
                                        $media_key = $meta_field['id'].'detail';
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
                                            
                                                $media_value['height'] = $sd_data['sd_logo']['height'];                                                                                         
                                                $media_value['width'] = $sd_data['sd_logo']['width'];                                                                                         
                                                $media_value['thumbnail'] = $sd_data['sd_logo']['url'];                                             
                                        }
                                        if (strpos($meta_field['id'], 'business_logo') !== false && empty($media_value_meta)) {
                                            
                                                $business_details = esc_sql ( get_post_meta($schema_id, 'saswp_local_business_details', true)  );                                                                                            
                                                $media_value['height'] = $business_details['local_business_logo']['height'];                                                                                         
                                                $media_value['width'] = $business_details['local_business_logo']['width'];                                                                                         
                                                $media_value['thumbnail'] = $business_details['local_business_logo']['url'];                                             
                                        }
                                                                                
					$input = sprintf(
						'<fieldset><input style="width: 80%%" id="%s" name="%s" type="text" value="%s">'
                                                . '<input data-id="media" style="width: 19%%" class="button" id="%s_button" name="%s_button" type="button" value="Upload" />'
//                                                . '<input type="hidden" data-id="'.esc_attr($meta_field['id']).'_id" class="upload-id " name="'.esc_attr($meta_field['id']).'_id" id="'.esc_attr($meta_field['id']).'_id" value="'.esc_attr($media_value['id']).'">'
                                                . '<input type="hidden" data-id="'.esc_attr($meta_field['id']).'_height" class="upload-height" name="'.esc_attr($meta_field['id']).'_height" id="'.esc_attr($meta_field['id']).'_height" value="'.esc_attr($media_value['height']).'">'
                                                . '<input type="hidden" data-id="'.esc_attr($meta_field['id']).'_width" class="upload-width" name="'.esc_attr($meta_field['id']).'_width" id="'.esc_attr($meta_field['id']).'_width" value="'.esc_attr($media_value['width']).'">'
                                                . '<input type="hidden" data-id="'.esc_attr($meta_field['id']).'_thumbnail" class="upload-thumbnail" name="'.esc_attr($meta_field['id']).'_thumbnail" id="'.esc_attr($meta_field['id']).'_thumbnail" value="'.esc_attr($media_value['thumbnail']).'">'                                                
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
                                        
					$input = sprintf(
						'<select class="%s" id="%s" name="%s">',
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
						'<textarea style="width: 100%%" id="%s" name="%s" rows="5">%s</textarea>',
						$meta_field['id'],
						$meta_field['id'],
						$meta_value
					);
					break;
                                case 'text':
                                case 'number':    
                                    $class = '';
                                             if (strpos($meta_field['id'], 'closes_time') !== false || strpos($meta_field['id'], 'opens_time') !== false){
                                             $class='saswp-local-schema-time-picker';    
                                             }
                                             if (strpos($meta_field['id'], 'date_modified') !== false || strpos($meta_field['id'], 'date_published') !== false){
                                             $class='saswp-local-schema-datepicker-picker';    
                                             }
                                            $input = sprintf(
						'<input class="%s" %s id="%s" name="%s" type="%s" value="%s">',
                                                $class,    
						$meta_field['type'] !== 'color' ? 'style="width: 100%"' : '',
						$meta_field['id'],
						$meta_field['id'],
						$meta_field['type'],
						$meta_value                                                                                                 
					   );
                                         break;	
                                
				default:       
                                            			
			}
			$output .= '<tr><th>'.$label.'</th><td>'.$input.'</td></tr>';
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
                     $response = $this->saswp_get_fields_by_schema_type($schema->ID);                     
                     $this->meta_fields = $response;                     
                        foreach ( $this->meta_fields as $meta_field ) {
			if ( isset( $_POST[ $meta_field['id'] ] ) ) {
				switch ( $meta_field['type'] ) {
                                        case 'media':                                                                                                  
                                                $media_key = $meta_field['id'].'detail';
                                                //$media_id = sanitize_text_field( $_POST[ $meta_field['id'].'_id' ] );
                                                $media_height = sanitize_text_field( $_POST[ $meta_field['id'].'_height' ] );
                                                $media_width = sanitize_text_field( $_POST[ $meta_field['id'].'_width' ] );
                                                $media_thumbnail = sanitize_text_field( $_POST[ $meta_field['id'].'_thumbnail' ] );
                                                $media_detail = array(
                                                    //'id' =>$media_id,
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
                                     'autobodyshop' => 'Auto Body Shop',
                                     'autodealer' => 'Auto Dealer',
                                     'autopartsstore'     => 'Auto Parts Store',
                                     'autorental'     => 'Auto Rental',
                                     'autorepair'      => 'Auto Repair',
                                     'autowash'     => 'Auto Wash',
                                     'gasstation' => 'Gas Station',
                                     'motorcycledealer' => 'Motorcycle Dealer',
                                     'motorcyclerepair' => 'Motorcycle Repair'
                                 ); 
                            break;
                        case 'emergencyservice':
                            $sub_business_options = array(
                                     'firestation' => 'Fire Station',
                                     'hospital' => 'Hospital',
                                     'policestation'     => 'Police Station',                                    
                                 ); 
                            break;
                        case 'entertainmentbusiness':
                           $sub_business_options = array(
                                      'adultentertainment' => 'Adult Entertainment',
                                      'amusementpark' => 'Amusement Park',
                                      'artgallery'     => 'Art Gallery',
                                      'casino'     => 'Casino',
                                      'comedyclub'     => 'Comedy Club',
                                      'movietheater'     => 'Movie Theater',
                                      'nightclub'     => 'Night Club',
                                      
                                 );  
                            break;
                        case 'financialservice':
                            $sub_business_options = array(
                                      'accountingservice' => 'Accounting Service',
                                      'automatedteller' => 'Automated Teller',
                                      'bankorcredit_union'     => 'Bank Or Credit Union',
                                      'insuranceagency'     => 'Insurance Agency',                                      
                                      
                                 );   
                            break;
                        case 'foodestablishment':
                             $sub_business_options = array(
                                      'bakery' => 'Bakery',
                                      'barorpub' => 'Bar Or Pub',
                                      'brewery'     => 'Brewery',
                                      'cafeorcoffee_shop'     => 'Cafe Or Coffee Shop', 
                                      'fastfoodrestaurant' => 'Fast Food Restaurant',
                                      'icecreamshop' => 'Ice Cream Shop',
                                      'restaurant'     => 'Restaurant',
                                      'winery'     => 'Winery', 
                                      
                                 );
                            break;
                        case 'healthandbeautybusiness':
                            $sub_business_options = array(
                                      'beautysalon' => 'Beauty Salon',
                                      'dayspa' => 'DaySpa',
                                      'hairsalon'     => 'Hair Salon',
                                      'healthclub'     => 'Health Club', 
                                      'nailsalon' => 'Nail Salon',
                                      'tattooparlor' => 'Tattoo Parlor',                                                                          
                                 );   
                            break;
                        case 'homeandconstructionbusiness':
                            $sub_business_options = array(
                                      'electrician' => 'Electrician',
                                      'generalcontractor' => 'General Contractor',
                                      'hvacbusiness'     => 'HVAC Business',
                                      'locksmith'     => 'Locksmith', 
                                      'movingcompany' => 'Moving Company',
                                      'plumber' => 'Plumber',       
                                      'roofingcontractor' => 'Roofing Contractor',       
                                 );   
                            break;
                        case 'legalservice':
                            $sub_business_options = array(
                                      'attorney' => 'Attorney',
                                      'notary' => 'Notary',                                            
                                 );  
                            break;
                        case 'lodgingbusiness':
                             $sub_business_options = array(
                                      'bedandbreakfast' => 'Bed And Breakfast',
                                      'campground' => 'Campground',
                                      'hostel' => 'Hostel',
                                      'hotel' => 'Hotel',
                                      'motel' => 'Motel',
                                      'resort' => 'Resort',
                                 );   
                            break;
                        case 'sportsactivitylocation':
                             $sub_business_options = array(
                                      'bowlingalley' => 'Bowling Alley',
                                      'exercisegym' => 'Exercise Gym',
                                      'golfcourse' => 'Golf Course',
                                      'healthclub' => 'Health Club',
                                      'publicswimming_pool' => 'Public Swimming Pool',
                                      'skiresort' => 'Ski Resort',
                                      'sportsclub' => 'Sports Club',
                                      'stadiumorarena' => 'Stadium Or Arena',
                                      'tenniscomplex' => 'Tennis Complex'
                                 );  
                            break;
                        case 'store':
                             $sub_business_options = array(
                                        'autopartsstore'=>'Auto Parts Store',
                                        'bikestore'=>'Bike Store',
                                        'bookstore'=>'Book Store',
                                        'clothingstore'=>'Clothing Store',
                                        'computerstore'=>'Computer Store',
                                        'conveniencestore'=>'Convenience Store',
                                        'departmentstore'=>'Department Store',
                                        'electronicsstore'=>'Electronics Store',
                                        'florist'=>'Florist',
                                        'furniturestore'=>'Furniture Store',
                                        'gardenstore'=>'Garden Store',
                                        'grocerystore'=>'Grocery Store',
                                        'hardwarestore'=>'Hardware Store',
                                        'hobbyshop'=>'Hobby Shop',
                                        'homegoodsstore'=>'HomeGoods Store',
                                        'jewelrystore'=>'Jewelry Store',
                                        'liquorstore'=>'Liquor Store',
                                        'mensclothingstore'=>'Mens Clothing Store',
                                        'mobilephonestore'=>'Mobile Phone Store',
                                        'movierentalstore'=>'Movie Rental Store',
                                        'musicstore'=>'Music Store',
                                        'officeequipmentstore'=>'Office Equipment Store',
                                        'outletstore'=>'Outlet Store',
                                        'pawnshop'=>'Pawn Shop',
                                        'petstore'=>'Pet Store',
                                        'shoestore'=>'Shoe Store',
                                        'sportinggoodsstore'=>'Sporting Goods Store',
                                        'tireshop'=>'Tire Shop',
                                        'toystore'=>'Toy Store',
                                        'wholesalestore'=>'Wholesale Store'
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
            $image_id 	= get_post_thumbnail_id();
            $image_details = wp_get_attachment_image_src($image_id, 'full');
            if(empty($image_details[0]) || $image_details[0] === NULL ){
            $image_details[0] = $sd_data['sd_logo']['url'];
	    }
            $current_user = wp_get_current_user();
            $author_details	= get_avatar_data($current_user->ID);           
            $schema_type  = esc_sql ( get_post_meta($schema_id, 'schema_type', true)  );  
            
            $business_type    = esc_sql ( get_post_meta($schema_id, 'saswp_business_type', true)  ); 
            $business_name    = esc_sql ( get_post_meta($schema_id, 'saswp_business_name', true)  ); 
            $business_details = esc_sql ( get_post_meta($schema_id, 'saswp_local_business_details', true)  );
            $saswp_business_type_key = 'saswp_business_type_'.$schema_id;
            $saved_business_type = get_post_meta( $post->ID, $saswp_business_type_key, true );
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
                            'label' => 'Sub Business Type',
                            'id' => 'saswp_business_name_'.$schema_id,
                            'type' => 'select',
                            'options' => $this->options_response,
                            'default' => $business_name  
                       ); 
                    }
                    
                    $meta_field = array(                   
                    array(
                            'label' => 'Business Type',
                            'id' => 'saswp_business_type_'.$schema_id,
                            'type' => 'select',
                            'default' => $business_type,
                            'options' => array(
                                    'animalshelter' => 'Animal Shelter',
                                    'automotivebusiness' => 'Automotive Business',
                                    'childcare' => 'ChildCare',
                                    'dentist' => 'Dentist',
                                    'drycleaningorlaundry' => 'Dry Cleaning Or Laundry',
                                    'emergencyservice' => 'Emergency Service',
                                    'employmentagency' => 'Employment Agency',
                                    'entertainmentbusiness' => 'Entertainment Business',
                                    'financialservice' => 'Financial Service',
                                    'foodestablishment' => 'Food Establishment',
                                    'governmentoffice' => 'Government Office',
                                    'healthandbeautybusiness' => 'Health And Beauty Business',
                                    'homeandconstructionbusiness' => 'Home And Construction Business',
                                    'internetcafe' => 'Internet Cafe',
                                    'legalservice' => 'Legal Service',
                                    'library' => 'Library',
                                    'lodgingbusiness' => 'Lodging Business',
                                    'professionalservice' => 'Professional Service',
                                    'radiostation' => 'Radio Station',
                                    'realestateagent' => 'Real Estate Agent',
                                    'recyclingcenter' => 'Recycling Center',
                                    'selfstorage' => 'Self Storage',
                                    'shoppingcenter' => 'Shopping Center',
                                    'sportsactivitylocation' => 'Sports Activity Location',
                                    'store' => 'Store',
                                    'televisionstation' => 'Television Station',
                                    'touristinformationcenter' => 'Tourist Information Center',
                                    'travelagency' => 'Travel Agency',
                            )
                        ),
                         $sub_business_options,
                        array(
                            'label' => 'Business Name',
                            'id' => 'local_business_name_'.$schema_id,
                            'type' => 'text',
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
                            'type' => 'text',
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
                            'type' => 'multiselect',
                            'options' => array(
                                     'monday'       => 'Monday',
                                     'tuesday'      => 'Tuesday',
                                     'wednesday'    => 'Wednesday',
                                     'thursday'     => 'Thursday',
                                     'friday'       => 'Friday',
                                     'staturday'    => 'Staturday',
                                     'sunday'       => 'Sunday',                                      
                            ),
                            'default' => $business_details['saswp_dayofweek']
                       ),
                        array(
                            'label' => 'Opens',
                            'id' => 'local_opens_time_'.$schema_id,
                            'type' => 'text',
                            'default' => $business_details['local_opens_time']    
                       ),
                        array(
                            'label' => 'Closes',
                            'id' => 'local_closes_time_'.$schema_id,
                            'type' => 'text',
                            'default' => $business_details['local_closes_time']
                       ),
                        array(
                            'label' => 'Price Range',
                            'id' => 'local_price_range_'.$schema_id,
                            'type' => 'text',
                            'default' => $business_details['local_price_range']
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
                            'type' => 'text',
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
                            'default' => $sd_data['sd_name']
                    ),
                     array(
                            'label' => 'Organization Logo',
                            'id' => 'saswp_blogposting_organization_logo_'.$schema_id,
                            'type' => 'media',
                            'default' => $sd_data['sd_logo']['url']
                    )                         
                    );
                    break;
                
                case 'NewsArticle':
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
                            'type' => 'text',
                            'default' => $post->post_excerpt
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
                            'default' => $author_details['url']
                    ),
                    array(
                            'label' => 'Organization Name',
                            'id' => 'saswp_newsarticle_organization_name_'.$schema_id,
                            'type' => 'text',
                            'default'=>$sd_data['sd_name']
                    ),
                    array(
                            'label' => 'Organization Logo',
                            'id' => 'saswp_newsarticle_organization_logo_'.$schema_id,
                            'type' => 'media',
                            'default' => $sd_data['sd_logo']['url']
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
                            'type' => 'text',
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
                            'label' => 'Description',
                            'id' => 'saswp_webpage_description_'.$schema_id,
                            'type' => 'text',
                            'default' => $post->post_excerpt
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
                            'default' => $sd_data['sd_name']
                    ), 
                     array(
                            'label' => 'Organization Logo',
                            'id' => 'saswp_webpage_organization_logo_'.$schema_id,
                            'type' => 'media',
                            'default' => $sd_data['sd_logo']['url']
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
                            'default' => $sd_data['sd_name']
                    ),
                    array(
                            'label' => 'Organization Logo',
                            'id' => 'saswp_article_organization_logo_'.$schema_id,
                            'type' => 'media',
                            'default' => $sd_data['sd_logo']['url']
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
                            'type' => 'text',
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
                            'default' => $sd_data['sd_name']
                    ),
                    array(
                            'label' => 'Organization Logo',
                            'id' => 'saswp_recipe_organization_logo_'.$schema_id,
                            'type' => 'media',
                            'default' => $sd_data['sd_logo']['url']
                    ),    
                    );
                    break;
                
                case 'Product':
                    $meta_field = array(
                    array(
                            'label' => 'URL',
                            'id' => 'saswp_product_url_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink()
                    ),
                    array(
                            'label' => 'Name',
                            'id' => 'saswp_product_name_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_title(),
                    ),
                    array(
                            'label' => 'Description',
                            'id' => 'saswp_product_description_'.$schema_id,
                            'type' => 'text',
                            'default' => $post->post_excerpt
                    ),
                    array(
                            'label' => 'Main Entity Id',
                            'id' => 'saswp_product_main_entity_id_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink()
                    )    
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
                            'type' => 'text',
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
