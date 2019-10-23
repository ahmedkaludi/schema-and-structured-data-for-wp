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
    
	private   $screen                    = array();
	private   $meta_fields               = array();				
        protected $all_schema                = null;
        protected $options_response          = array();
        protected $modify_schema_post_enable = false;
        public    $_local_sub_business       = array();
        public    $schema_type_element       =  array(                        
                        'Product' => array(
                               'product_reviews' => 'product_reviews',                                                
                        ),
                        'DataFeed' => array(
                               'feed_element' => 'feed_element',                                                
                        ),
                        'FAQ' => array(
                               'faq-question' => 'faq_question',                                                
                        ),
                        'Event' => array(
                               'performer'     => 'performer',                                                
                        ),
                        'HowTo' => array(
                               'how-to-supply' => 'howto_supply', 
                               'how-to-tool'   => 'howto_tool', 
                               'how-to-step'   => 'howto_step', 
                        ),
                        'MusicPlaylist' => array(
                               'music-playlist-track' => 'music_playlist_track',                                                               
                        ),
                        'MusicAlbum' => array(
                               'music-album-track' => 'music_album_track',                                                               
                        ),
                        'Apartment' => array(
                               'apartment-amenities' => 'apartment_amenities',
                               'additional-property' => 'additional_property',
                                                              
                        ),
                        'MedicalCondition' => array(
                               'mc-cause'       => 'mc_cause', 
                               'mc-symptom'     => 'mc_symptom', 
                               'mc-risk_factor' => 'mc_risk_factor', 

                        ),
                        'TVSeries' => array(
                               'tvseries-actor'  => 'tvseries_actor',
                               'tvseries-season' => 'tvseries_season', 
                        ),
                        'Trip' => array(
                               'trip-itinerary'  => 'trip_itinerary'
                        )                                                                          
                    );
                                
        public $_meta_name =  array(    
                    'product_reviews' => array(                    
                    array(
			'label'     => 'Reviewer Name',
			'name'      => 'saswp_product_reviews_reviewer_name',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => 'Rating',
			'name'      => 'saswp_product_reviews_reviewer_rating',
			'type'      => 'number',                        
		    ),
                    array(
			'label'     => 'Text',
			'name'      => 'saswp_product_reviews_text',
			'type'      => 'textarea',                        
		    ),
                    array(
			'label'     => 'Created Date',
			'name'      => 'saswp_product_reviews_created_date',
			'type'      => 'text',                        
		    )    
                    ),                   
                    'feed_element' => array(                    
                    array(
			'label'     => 'Date Created',
			'name'      => 'saswp_feed_element_date_created',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => 'Feed Element Name',
			'name'      => 'saswp_feed_element_name',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => 'Feed Element email',
			'name'      => 'saswp_feed_element_email',
			'type'      => 'text',                        
		    )    
                    ),
                    'performer' => array(                    
                    array(
			'label'     => 'Performer Type',
			'name'      => 'saswp_event_performer_type',
			'type'      => 'select',
                        'options'   => array(
                                'MusicGroup'    => 'MusicGroup',                                                              
                                'Person'        => 'Person'
                        )
		    ),
                    array(
			'label'     => 'Performer Name',
			'name'      => 'saswp_event_performer_name',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => 'Performer URL',
			'name'      => 'saswp_event_performer_url',
			'type'      => 'text',                        
		    )                                                            
                    ),
                    'howto_supply' => array(                    
                    array(
			'label'     => 'Supply Name',
			'name'      => 'saswp_howto_supply_name',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => 'Supply URL',
			'name'      => 'saswp_howto_supply_url',
			'type'      => 'text',                        
		    ),    
                    array(
			'label'     => 'Supply Image',
			'name'      => 'saswp_howto_supply_image',
			'type'      => 'media',                        
		    )                                        
                    ),   
                    'howto_tool' => array(                    
                    array(
			'label'     => 'Tool Name',
			'name'      => 'saswp_howto_tool_name',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => 'Tool URL',
			'name'      => 'saswp_howto_tool_url',
			'type'      => 'text',                        
		    ),    
                    array(
			'label'     => 'Tool Image',
			'name'      => 'saswp_howto_tool_image',
			'type'      => 'media',                        
		    )                                        
                    ),
                    'howto_step' => array(                    
                    array(
			'label'     => 'Step Name',
			'name'      => 'saswp_howto_step_name',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => 'HowToDirection Text',
			'name'      => 'saswp_howto_direction_text',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => 'HowToTip Text',
			'name'      => 'saswp_howto_tip_text',
			'type'      => 'text',                        
		    ),    
                    array(
			'label'     => 'Step Image',
			'name'      => 'saswp_howto_step_image',
			'type'      => 'media',                        
		    )                                        
                    ),
                    'mc_symptom' => array(                    
                    array(
			'label'     => 'Sign Or Symptom',
			'name'      => 'saswp_mc_symptom_name',
			'type'      => 'text',                        
		    )                                                         
                    ),
                    'mc_risk_factor' => array(                    
                    array(
			'label'     => 'Risk Factor',
			'name'      => 'saswp_mc_risk_factor_name',
			'type'      => 'text',                        
		    )                                                           
                    ),
                    'mc_cause' => array(                    
                    array(
			'label'     => 'Cause',
			'name'      => 'saswp_mc_cause_name',
			'type'      => 'text',                        
		    )                                                           
                    ),                                    
                    'tvseries_actor' => array(                    
                    array(
			'label'     => 'Actor Name',
			'name'      => 'saswp_tvseries_actor_name',
			'type'      => 'text',                        
		    )                                                           
                    ),
                    'tvseries_season' => array(                    
                    array(
			'label'     => 'Season Name',
			'name'      => 'saswp_tvseries_season_name',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => 'Season Published Date',
			'name'      => 'saswp_tvseries_season_published_date',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => 'Number Of Episodes',
			'name'      => 'saswp_tvseries_season_episodes',
			'type'      => 'text',                        
		    )                                                            
                    ),
                   'trip_itinerary' => array(                    
                    array(
			'label'     => 'Itinerary Type',
			'name'      => 'saswp_trip_itinerary_type',
			'type'      => 'select',
                        'options'   => array(
                                'City'                            => 'City',
                                'LandmarksOrHistoricalBuildings'  => 'LandmarksOrHistoricalBuildings',
                                'AdministrativeArea'              => 'AdministrativeArea',
                                'LakeBodyOfWater'                 => 'LakeBodyOfWater'
                        )
		    ),
                    array(
			'label'     => 'Itinerary Name',
			'name'      => 'saswp_trip_itinerary_name',
			'type'      => 'text'                        
		    ),
                     array(
			'label'     => 'Itinerary Description',
			'name'      => 'saswp_trip_itinerary_description',
			'type'      => 'textarea'                        
		    ),
                     array(
			'label'     => 'Itinerary URL',
			'name'      => 'saswp_trip_itinerary_url',
			'type'      => 'text'                        
		    )   
                    ),                                                                       
                    'faq_question' => array(                                       
                    array(
			'label'     => 'Question',
			'name'      => 'saswp_faq_question_name',
			'type'      => 'text'                        
		    ),
                     array(
			'label'     => 'Accepted Answer',
			'name'      => 'saswp_faq_question_answer',
			'type'      => 'textarea'                        
		    )                    
                    ),
                    'apartment_amenities' => array(                    
                    array(
			'label'     => 'Amenity Name',
			'name'      => 'saswp_apartment_amenities_name',
			'type'      => 'text',                        
		    )                                                                                    
                    ),
                    'additional_property' => array(                    
                    array(
			'label'     => 'Name',
			'name'      => 'saswp_apartment_additional_property_name',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => 'Value',
			'name'      => 'saswp_apartment_additional_property_value',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => 'Code Type',
			'name'      => 'saswp_apartment_additional_property_code_type',
			'type'      => 'select',
                        'options'   => array(
                                'unitCode'   => 'Unit Code',
                                'unitText'   => 'Unit Text',                                                                                                
                        )
		    ),
                    array(
			'label'     => 'Code Value',
			'name'      => 'saswp_apartment_additional_property_code_value',
			'type'      => 'text',                        
		    ),    
                    ),
                    'music_playlist_track' => array(                    
                    array(
			'label'     => 'Track Artist',
			'name'      => 'saswp_music_playlist_track_artist',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => 'Track Duration',
			'name'      => 'saswp_music_playlist_track_duration',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => 'Track In Album',
			'name'      => 'saswp_music_playlist_track_inalbum',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => 'Track Name',
			'name'      => 'saswp_music_playlist_track_name',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => 'Track URL',
			'name'      => 'saswp_music_playlist_track_url',
			'type'      => 'text',                        
		    ),    
                       
                    ),
                    'music_album_track' => array(                                        
                    array(
			'label'     => 'Track Duration',
			'name'      => 'saswp_music_album_track_duration',
			'type'      => 'text',                        
		    ),                    
                    array(
			'label'     => 'Track Name',
			'name'      => 'saswp_music_album_track_name',
			'type'      => 'text',                        
		    ),
                    array(
			'label'     => 'Track URL',
			'name'      => 'saswp_music_album_track_url',
			'type'      => 'text',                        
		    ),    
                       
                    )                    
                  );

        public function __construct() {
            
                $mappings_local_sub = SASWP_DIR_NAME . '/core/array-list/local-sub-business.php';
                
		if ( file_exists( $mappings_local_sub ) ) {
                    $this->_local_sub_business = include $mappings_local_sub;
		}
            
        }

        /**
         * List of hooks used in this context
         */                       
        public function saswp_post_specific_hooks(){
            
                add_action( 'admin_init', array( $this, 'saswp_get_all_schema_list' ) );
                           
		add_action( 'add_meta_boxes', array( $this, 'saswp_post_specifc_add_meta_boxes' ) );		
		add_action( 'save_post', array( $this, 'saswp_post_specific_save_fields' ) );
                add_action( 'wp_ajax_saswp_get_sub_business_ajax', array($this,'saswp_get_sub_business_ajax'));
                
                add_action( 'wp_ajax_saswp_get_schema_dynamic_fields_ajax', array($this,'saswp_get_schema_dynamic_fields_ajax'));
                
                add_action( 'wp_ajax_saswp_modify_schema_post_enable', array($this,'saswp_modify_schema_post_enable'));
                                                
                add_action( 'wp_ajax_saswp_restore_schema', array($this,'saswp_restore_schema'));
                
                add_action( 'wp_ajax_saswp_enable_disable_schema_on_post', array($this,'saswp_enable_disable_schema_on_post'));
                
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
                $schema_id     = intval($_POST['schema_id']);
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
            
            global $post;
                        
            $post_specific_id = '';
            $schema_count     = 0;
            
            if(is_object($post)){
                $post_specific_id = $post->ID;
            }     
            if(!empty($this->all_schema)){
              $schema_count = count($this->all_schema);  
            }            
            
            if($schema_count > 0){
                
            $show_post_types = get_post_types();
            unset($show_post_types['adsforwp'],$show_post_types['saswp'],$show_post_types['attachment'], $show_post_types['revision'], $show_post_types['nav_menu_item'], $show_post_types['user_request'], $show_post_types['custom_css']);            
            
            $this->screen = $show_post_types;
            
            if($this->screen){
                 
                 foreach ( $this->screen as $single_screen ) {
                     
			add_meta_box(
				'post_specific',
				esc_html__( 'Post Specific Schema', 'schema-and-structured-data-for-wp' ),
				array( $this, 'saswp_post_meta_box_callback' ),
				$single_screen,
				'advanced',
				'default'
			);
                        
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
            $meta_name  = '';
            $meta_array = array();
            if(isset($_GET['meta_name'])){
                $meta_name = sanitize_text_field($_GET['meta_name']);
                $meta_array = $this->_meta_name[$meta_name];
            }
            if(!empty($meta_array)){
             echo json_encode($meta_array);   
            }            
            wp_die();
        }
        
        public function saswp_get_dynamic_html($schema_id, $meta_name, $index, $data){
                                                             
                $meta_fields = $this->_meta_name[$meta_name];               
            
                $output  = '';                                                                                                                                                         
		foreach ( $meta_fields as $meta_field ) {
                    
                    
			$label = '<label for="' . $meta_field['name'] . '">' . esc_html__( $meta_field['label'], 'schema-and-structured-data-for-wp' ) . '</label>';			
			                                                                        
			switch ( $meta_field['type'] ) {
                                                            								                                
                                case 'media':
                                                $name = $meta_field['name'].'_'.$index.'_'.$schema_id;
                                    
                                                $img_prev = '';
                                                $src      = '';
                                                
                                                if(wp_get_attachment_url($data[$meta_field['name'].'_id'])){
                                                 
                                                $src = wp_get_attachment_url(esc_attr($data[$meta_field['name'].'_id']));
                                                    
                                                $img_prev = '<div class="saswp_image_thumbnail">'
                                                           . '<img class="saswp_image_prev" src="'.esc_url($src).'">'
                                                           . '<a data-id="'.esc_attr($name).'" href="#" class="saswp_prev_close">X</a>'
                                                           . '</div>';     

                                                }
                                        
                                                //$img_prev is already escapped
                                                $input = '<fieldset>
                                                        <input style="width:79%" type="text" id="'.esc_attr($name).'" name="'.esc_attr($name).'" value="'.esc_url($src).'">
                                                        <input type="hidden" data-id="'.esc_attr($name).'_id" name="'.esc_attr($meta_name).'_'.esc_attr($schema_id).'['.esc_attr($index).']['.esc_attr($meta_field['name']).'_id]'.'" id="'.esc_attr($name).'_id" value="'.esc_attr($data[$meta_field['name'].'_id']).'">
                                                        <input data-id="media" style="width: 19%" class="button" id="'.esc_attr($name).'_button" name="'.esc_attr($name).'_button" type="button" value="Upload">
                                                        <div class="saswp_image_div_'.esc_attr($name).'">'.$img_prev.'</div>
                                                        </fieldset>';
                                                
                                            
                                                                                                                        
                                                break;
                                                
                                case 'textarea':
					$input = sprintf(
						'<textarea style="width: 100%%" id="%s" name="%s" rows="5">%s</textarea>',                                                
						esc_attr($meta_field['name']).'_'.esc_attr($index).'_'.esc_attr($schema_id),
						esc_attr($meta_name).'_'.esc_attr($schema_id).'['.esc_attr($index).']['.esc_attr($meta_field['name']).']',
						esc_textarea($data[$meta_field['name']])
					);
                                        
					break;                
                                
                                case 'select':                                        
                                                                                     
					$input = sprintf(
						'<select id="%s" name="%s">',                                                
						esc_attr($meta_field['name']).'_'.esc_attr($index).'_'.esc_attr($schema_id),
						esc_attr($meta_name).'_'.esc_attr($schema_id).'['.esc_attr($index).']['.esc_attr($meta_field['name']).']'
					);
					foreach ( $meta_field['options'] as $key => $value ) {
                                            
						$meta_field_value = !is_numeric( $key ) ? $key : $value;
						$input .= sprintf(
							'<option %s value="%s">%s</option>',
							$data[$meta_field['name']] === $meta_field_value ? 'selected' : '',
							$meta_field_value,
							esc_html__($value, 'schema-and-structured-data-for-wp' )
						);
					}
					$input .= '</select>';
					break;                
                                         
				default:
                                                    
                                    $class = '';

                                    if ((strpos($meta_field['name'].'_'.$index.'_'.$schema_id, 'published_date') !== false) || (strpos($meta_field['name'].'_'.$index.'_'.$schema_id, 'date_created') !== false) || (strpos($meta_field['name'].'_'.$index.'_'.$schema_id, 'created_date') !== false)){                                                                                                           

                                            $class = 'class="saswp-datepicker-picker"';    
                                    }
                                                                                                            
                                     $input = sprintf(
						'<input %s  style="width:100%%" id="%s" name="%s" type="%s" value="%s">',
                                                $class,
						esc_attr($meta_field['name']).'_'.esc_attr($index).'_'.esc_attr($schema_id),
						esc_attr($meta_name).'_'.esc_attr($schema_id).'['.esc_attr($index).']['.esc_attr($meta_field['name']).']',
						esc_attr($meta_field['type']),
						esc_attr($data[$meta_field['name']])                                            
                                             );
                                        
					
			}
                        //$lable and $input has been escapped while create this variable
			$output .= '<tr><th>'.$label.'</th><td>'.$input.'</td></tr>';
		}
                
                //$output has been escapped while create this variable                                               		                                
		 $response = '<table class="form-table">'.$output.'</table>';                 
                 return $response;
                 
        }
        
        public function saswp_schema_fields_html_on_the_fly($schema_type, $schema_id, $post_id){
            
                    $howto_data        = array();                    
                    $tabs_fields       = '';
                    
                    $schema_type_fields = $this->schema_type_element;
                    
                    $type_fields = array_key_exists($schema_type, $schema_type_fields) ? $schema_type_fields[$schema_type]:'';  
                        
                    if($type_fields){
                        
                        $tabs_fields .= '<div class="saswp-table-create-onajax">';
                        
                        foreach($type_fields as $key => $value){
                            
                            $howto_data[$value.'_'.$schema_id]  = get_post_meta($post_id, $value.'_'.$schema_id, true);                                       
                                                 
                            $tabs_fields .= '<div class="saswp-'.$key.'-section-main">';                                                  
                            $tabs_fields .= '<div class="saswp-'.$key.'-section" data-id="'.esc_attr($schema_id).'">';                         
                            if(isset($howto_data[$value.'_'.$schema_id])){

                                $howto_supply = $howto_data[$value.'_'.$schema_id];                                                     
                                $supply_html  = '';

                                if(!empty($howto_supply)){

                                       $i = 0;
                                       foreach ($howto_supply as $supply){

                                           $supply_html .= '<div class="saswp-'.$key.'-table-div saswp-dynamic-properties" data-id="'.$i.'">';
                                           $supply_html .= '<a class="saswp-table-close">X</a>';
                                           $supply_html .= $this->saswp_get_dynamic_html($schema_id, $value, $i, $supply);
                                           $supply_html .= '</div>';

                                        $i++;   
                                       }

                                }

                                $tabs_fields .= $supply_html;

                            }                         
                            $tabs_fields .= '</div>';
                            
                            $btn_text = '';
                            
                            if($value){
                                
                                $btn_array = explode('_',$value);
                            
                                if($btn_array){
                                    foreach ($btn_array as $btn){
                                        $btn_text .= ucfirst($btn).' ';
                                    }
                                }
                                
                            }
                                                        
                            $tabs_fields .= '<a data-id="'.esc_attr($schema_id).'" div_type="'.$key.'" fields_type="'.$value.'" class="button saswp_add_schema_fields_on_fly saswp-'.$key.'">'.esc_html__( 'Add '.$btn_text, 'schema-and-structured-data-for-wp' ).'</a>';                                                                                                    
                            $tabs_fields .= '</div>';                                                                                                
                         
                        }
                        
                        $tabs_fields .= '</div>';
                            
                        }
                                                                                                                                                                                                                                   
                     return $tabs_fields;
            
        }
        
        public function saswp_post_meta_box_fields($post){    
            
             $response_html     = '';
             $tabs              = '';
             $tabs_fields       = '';
             $schema_ids        = array();
                                     
             $schema_enable = get_post_meta($post->ID, 'saswp_enable_disable_schema', true);
                                
             if(!empty($this->all_schema)){  
                 
                 foreach($this->all_schema as $key => $schema){
                     
                      $advnace_status = saswp_check_advance_display_status($schema->ID);
                                          
                      if($advnace_status !== 1){
                          continue;
                      }
                                           
                     $checked = '';
                                                                                    
                     if(isset($schema_enable[$schema->ID]) && $schema_enable[$schema->ID] == 1){
                         
                        $checked = 'checked';    
                     
                     }  
                     
                     $response = $this->saswp_get_fields_by_schema_type($schema->ID);                     
                     $this->meta_fields = $response;
                     
                     $output       = $this->saswp_saswp_post_specific( $post, $schema->ID ); 
                     $schema_type  = get_post_meta($schema->ID, 'schema_type', true);                                           
                                                                                                           
                     if($key==0){
                         
                     $tabs .='<li class="selected"><a saswp-schema-type="'.esc_attr($schema_type).'" data-id="saswp_specific_'.esc_attr($schema->ID).'" class="saswp-tab-links selected">'.esc_attr($schema_type == 'local_business'? 'LocalBusiness': $schema_type).'</a>'
                             . '<label class="saswp-switch">'
                             . '<input type="checkbox" class="saswp-schema-type-toggle" value="1" data-schema-id="'.esc_attr($schema->ID).'" data-post-id="'.esc_attr($post->ID).'" '.$checked.'>'
                             . '<span class="saswp-slider"></span>'
                             . '</li>';    
                     
                     $tabs_fields .= '<div data-id="'.esc_attr($schema->ID).'" id="saswp_specific_'.esc_attr($schema->ID).'" class="saswp-post-specific-wrapper">';
                     $tabs_fields .= '<div class="saswp-table-create-onload">';
                     //varible $output has been escapped while creating it
                     $tabs_fields .= '<table class="form-table"><tbody>' . $output . '</tbody></table>';
                     $tabs_fields .= '</div>';
                                                               
                     $tabs_fields .=  $this->saswp_schema_fields_html_on_the_fly($schema_type, $schema->ID, $post->ID);
                                                                                    
                     $tabs_fields .= '</div>';
                     
                     }else{
                         
                     $tabs .='<li>'
                             . '<a saswp-schema-type="'.esc_attr($schema_type).'" data-id="saswp_specific_'.esc_attr($schema->ID).'" class="saswp-tab-links">'.esc_attr($schema_type == 'local_business'? 'LocalBusiness': $schema_type).'</a>'
                             . '<label class="saswp-switch">'
                             . '<input type="checkbox" class="saswp-schema-type-toggle" value="1" data-schema-id="'.esc_attr($schema->ID).'" data-post-id="'.esc_attr($post->ID).'" '.$checked.'>'
                             . '<span class="saswp-slider"></span>'
                             . '</li>';    
                     $tabs_fields .= '<div data-id="'.esc_attr($schema->ID).'" id="saswp_specific_'.esc_attr($schema->ID).'" class="saswp-post-specific-wrapper saswp_hide">';
                     $tabs_fields .= '<div class="saswp-table-create-onload">';
                     $tabs_fields .= '<table class="form-table"><tbody>' . $output . '</tbody></table>';
                     $tabs_fields .= '</div>';
                     
                     $tabs_fields .=  $this->saswp_schema_fields_html_on_the_fly($schema_type, $schema->ID, $post->ID);
                     
                     $tabs_fields .= '</div>';
                     
                     } 
                     
                     $schema_ids[] =$schema->ID;
                 }   
                                  
                $response_html .= '<div>';  
                $response_html .= '<div><a href="#" class="saswp-restore-post-schema button">'.esc_html__( 'Restore Default', 'schema-and-structured-data-for-wp' ).'</a></div>';  
                $response_html .= '<div class="saswp-tab saswp-post-specific-tab-wrapper">';                
		$response_html .= '<ul class="saswp-tab-nav">';
                $response_html .= $tabs;                
                $response_html .= '</ul>';                
                $response_html .= '</div>';                
                $response_html .= '<div class="saswp-post-specific-container">';                
                $response_html .= $tabs_fields;                                 
                $response_html .= '</div>';
                                
                //custom schema starts here
                
                $custom_markup = get_post_meta($post->ID, 'saswp_custom_schema_field', true);
                  
                  $response_html .= '<div class="saswp-add-custom-schema-div">';                            
                  
                  if($custom_markup){
                      
                      $response_html .= '<a style="display:none;" class="button saswp-add-custom-schema">'.esc_html__( 'Add Custom Schema', 'schema-and-structured-data-for-wp' ).'</a>' ;                   
                      $response_html .= '<div class="saswp-add-custom-schema-field">';
                  
                  }else{
                      
                      $response_html .= '<a class="button saswp-add-custom-schema">'.esc_html__( 'Add Custom Schema', 'schema-and-structured-data-for-wp' ).'</a>' ;                   
                      $response_html .= '<div class="saswp-add-custom-schema-field saswp_hide">';
                  }
                                                      
                  $response_html .= '<a class="button saswp-delete-custom-schema">'.esc_html__( 'Delete Custom Schema', 'schema-and-structured-data-for-wp' ).'</a>';              
                  $response_html .= '<textarea style="margin-left:5px;" placeholder="{ Json Markup }" id="saswp_custom_schema_field" name="saswp_custom_schema_field" rows="5" cols="100">'
                  . $custom_markup
                  . '</textarea>';
                  
                  if(json_decode($custom_markup) == false){
                      $response_html .= '<p style="text-align:center;color:red;margin-top:0px;">'.esc_html__( 'Not a valid json', 'schema-and-structured-data-for-wp' ).'</p>';
                  }                  
                  $response_html .= '</div>';                                    
                  $response_html .= '</div>';
                
                //custom schema ends here
                                
                $response_html .= '<input class="saswp-post-specific-schema-ids" type="hidden" value="'. json_encode($schema_ids).'">';
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
             
                $modify_option = get_option('modify_schema_post_enable_'.esc_attr($post->ID));                
                
                if($modify_option == 'enable' && $schema_avail){
                    
                  $response_html .= $this->saswp_post_meta_box_fields($post);  
                
                }else{
                  
                  if($advnace_status){
                      $response_html .= '<a class="button saswp-modify_schema_post_enable">'.esc_html__( 'Modify Current Schema', 'schema-and-structured-data-for-wp' ).'</a>' ;
                  }                                                          
                  $custom_markup = get_post_meta($post->ID, 'saswp_custom_schema_field', true);
                  
                  $response_html .= '<div class="saswp-add-custom-schema-div">';                            
                  
                  if($custom_markup){
                      
                      $response_html .= '<a style="display:none;" class="button saswp-add-custom-schema">'.esc_html__( 'Add Custom Schema', 'schema-and-structured-data-for-wp' ).'</a>' ;                   
                      $response_html .= '<div class="saswp-add-custom-schema-field">';
                  
                  }else{
                      
                      $response_html .= '<a class="button saswp-add-custom-schema">'.esc_html__( 'Add Custom Schema', 'schema-and-structured-data-for-wp' ).'</a>' ;                   
                      $response_html .= '<div class="saswp-add-custom-schema-field saswp_hide">';
                  }
                                                      
                  $response_html .= '<a class="button saswp-delete-custom-schema">Delete Custom Schema</a>';              
                  $response_html .= '<textarea style="margin-left:5px;" placeholder="{ Json Markup }" id="saswp_custom_schema_field" name="saswp_custom_schema_field" rows="5" cols="100">'
                  . $custom_markup
                  . '</textarea>';
                  if(json_decode($custom_markup) == false){
                      $response_html .= '<p style="text-align:center;color:red;margin-top:0px;">'.esc_html__( 'Not a valid json', 'schema-and-structured-data-for-wp' ).'</p>';
                  }
                  $response_html .= '</div>';                                    
                  $response_html .= '</div>';
                  
                  
                } 
             
                return $response_html;
        }
        
        public function saswp_post_meta_box_callback() { 
                    
                global $post; 
		wp_nonce_field( 'post_specific_data', 'post_specific_nonce' );  
                echo $this->saswp_post_meta_box_html($post);                                             
                                                                                                                                                                   		
	}
        /**
         * Function to restoere all the post specific schema on a particular post/page
         * @return type string
         * @since version 1.0.4
         */
        public function saswp_restore_schema(){
            
                if ( ! isset( $_POST['saswp_security_nonce'] ) ){
                    return; 
                }
                if ( !wp_verify_nonce( $_POST['saswp_security_nonce'], 'saswp_ajax_check_nonce' ) ){
                   return;  
                } 
                
                $result     = '';
                $post_id    = intval($_POST['post_id']); 
                $schema_ids = array_map( 'sanitize_text_field', $_POST['schema_ids'] );
                   
                if($schema_ids){
                    
                    foreach($schema_ids as $id){
                    
                         $meta_field = $this->saswp_get_fields_by_schema_type($id);
                  
                            foreach($meta_field as $field){

                                 $result = delete_post_meta($post_id, $field['id']); 

                            }   
                  
                     }
                    
                }
                
                update_post_meta($post_id, 'saswp_custom_schema_field', '');
                update_option('modify_schema_post_enable_'.$post_id, 'disable');
                               
                if($result){ 
                    
                    echo json_encode(array('status'=> 't', 'msg'=>esc_html__( 'Schema has been restored', 'schema-and-structured-data-for-wp' )));
                    
                }else{
                    
                    echo json_encode(array('status'=> 'f', 'msg'=>esc_html__( 'Schema has already been restored', 'schema-and-structured-data-for-wp' )));
                    
                }                                              
                 wp_die();
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
                
                 $post_id = intval($_GET['post_id']);
                                                   
                 update_option('modify_schema_post_enable_'.$post_id, 'enable');    
                                                 
                 $post = get_post($post_id);
                 
                 $response = $this->saswp_post_meta_box_html($post);
                 
                 echo $response;
                                                   
                 wp_die();
                 
                }

        public function saswp_saswp_post_specific( $post, $schema_id ) { 
                                
                global $sd_data;                        
                
                $image_id      = get_post_thumbnail_id();
                $image_details = wp_get_attachment_image_src($image_id, 'full');
                
                if(empty($image_details[0]) || $image_details[0] === NULL ){
                
                 if(isset($sd_data['sd_logo']['url'])){
                     $image_details[0] = $sd_data['sd_logo']['url'];
                 }
                                    
                }
                
                $current_user   = wp_get_current_user();
                $author_details = array();
                
                if(function_exists('get_avatar_data')){
                    $author_details	= get_avatar_data($current_user->ID);                
                }                                
                $schema_type    = get_post_meta($schema_id, 'schema_type', true);  
		$output = '';                
                $this->meta_fields = array_filter($this->meta_fields);
                
		foreach ( $this->meta_fields as $meta_field ) {
                    
                        $input      = '';
                        $attributes = '';
                        
			$label      = '<label for="' . esc_attr($meta_field['id']) . '">' . esc_html__( $meta_field['label'], 'schema-and-structured-data-for-wp' ). '</label>';
			$meta_value = get_post_meta( $post->ID, $meta_field['id'], true );
                        
			if ( empty( $meta_value ) && isset($meta_field['default'])) {
                            
				$meta_value = $meta_field['default'];                                 
                        }
                        
                        if(isset($meta_field['attributes'])){
                            
                            foreach ($meta_field['attributes'] as $key => $attr ){
                                
                                           $attributes .=''.esc_attr($key).'="'.esc_attr($attr).'"';
                                }
                                
                        }                        
                        
			switch ( $meta_field['type'] ) {
                            
				case 'media':
                                    
                                        $f_image_id 	       = get_post_thumbnail_id();
                                        $f_image_details       = wp_get_attachment_image_src($f_image_id, 'full'); 
                                        
                                        $media_value = array();
                                        $media_key = $meta_field['id'].'_detail';
                                        
                                        $media_value_meta = get_post_meta( $post->ID, $media_key, true ); 
                                        
                                        if(!empty($media_value_meta)){
                                            $media_value = $media_value_meta;  
                                        }  
                                        
                                        if (strpos($meta_field['id'], 'author_image') !== false && empty($media_value_meta)) {                                          
                                                $media_value['height']    = $author_details['height'];                                                                                         
                                                $media_value['width']     = $author_details['width'];                                                                                         
                                                $media_value['thumbnail'] = $author_details['url'];                                             
                                        }
                                        if (strpos($meta_field['id'], 'image') !== false && empty($media_value_meta)) {
                                                                                            
                                                if(!empty($f_image_details)){
                                                    $media_value['thumbnail'] = $f_image_details[0];
                                                    $media_value['width']     = $f_image_details[1];
                                                    $media_value['height']    = $f_image_details[2];
                                                                                                        
                                                }
                                                                                                                                        
                                        }          
                                        
                                        if (strpos($meta_field['id'], 'organization_logo') !== false && empty($media_value_meta)) {
                                                                                            
                                                if(isset($sd_data['sd_logo'])){
                                                    $media_value['height']    = $sd_data['sd_logo']['height'];                                                                                         
                                                    $media_value['width']     = $sd_data['sd_logo']['width'];                                                                                         
                                                    $media_value['thumbnail'] = $sd_data['sd_logo']['url']; 
                                                }
                                                                                                                                        
                                        }
                                             
                                        $media_height    = '';
                                        $media_width     = '';
                                        $media_thumbnail = '';
                                        
                                        if(isset($media_value['thumbnail'])){
                                            $media_thumbnail =$media_value['thumbnail'];
                                        }
                                        if(isset($media_value['height'])){
                                           $media_height =$media_value['height']; 
                                        }
                                        if(isset($media_value['width'])){
                                             $media_width =$media_value['width'];
                                        }
                                            
                                        $image_pre = '';
                                        if($media_thumbnail){
                                            
                                           $image_pre = '<div class="saswp_image_thumbnail">
                                                         <img class="saswp_image_prev" src="'.esc_attr($media_thumbnail).'" />
                                                         <a data-id="'.esc_attr($meta_field['id']).'" href="#" class="saswp_prev_close">X</a>
                                                        </div>'; 
                                            
                                        }
					$input = sprintf(
						'<fieldset><input style="width: 80%%" id="%s" name="%s" type="text" value="%s">'
                                                . '<input data-id="media" style="width: 19%%" class="button" id="%s_button" name="%s_button" type="button" value="Upload" />'
                                                . '<input type="hidden" data-id="'.esc_attr($meta_field['id']).'_height" class="upload-height" name="'.esc_attr($meta_field['id']).'_height" id="'.esc_attr($meta_field['id']).'_height" value="'.esc_attr($media_height).'">'
                                                . '<input type="hidden" data-id="'.esc_attr($meta_field['id']).'_width" class="upload-width" name="'.esc_attr($meta_field['id']).'_width" id="'.esc_attr($meta_field['id']).'_width" value="'.esc_attr($media_width).'">'
                                                . '<input type="hidden" data-id="'.esc_attr($meta_field['id']).'_thumbnail" class="upload-thumbnail" name="'.esc_attr($meta_field['id']).'_thumbnail" id="'.esc_attr($meta_field['id']).'_thumbnail" value="'.esc_attr($media_thumbnail).'">'                                                
                                                . '<div class="saswp_image_div_'.esc_attr($meta_field['id']).'">'                                               
                                                . $image_pre                                                 
                                                . '</div>'
                                                .'</fieldset>',
						$meta_field['id'],
						$meta_field['id'],
						$media_thumbnail,
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
							esc_html__($value, 'schema-and-structured-data-for-wp' )
						);
					}
					$input .= '</select>';
					break;
                                
                                case 'checkbox':
                                    
                                        $rating_class = '';
                                         
                                        if (strpos($meta_field['id'], 'speakable') === false){
                                             $rating_class = 'class="saswp-enable-rating-review-'.strtolower($schema_type).'"';   
                                        }
                                                                            
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
                                            
                                                $meta_field_selected = '';
                                                
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
                                            
                                          $input .='<p>'.esc_attr($meta_field['note']).'</p>';  
                                          
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
                                                     || strpos($meta_field['id'], 'start_date') !== false
                                                     || strpos($meta_field['id'], 'end_date') !== false
                                                     || strpos($meta_field['id'], 'validfrom') !== false
                                                     || strpos($meta_field['id'], 'dateposted') !== false
                                                     || strpos($meta_field['id'], 'validthrough') !== false
                                                     || strpos($meta_field['id'], 'date_of_birth') !== false
                                                     || strpos($meta_field['id'], 'date_created') !== false
                                                     || strpos($meta_field['id'], 'created_date') !== false
                                                     ) {
                                             $class='saswp-datepicker-picker';    
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
                        
                        
                        if($meta_field['id'] == 'saswp_service_schema_rating_'.$schema_id       || 
                           $meta_field['id'] == 'saswp_product_schema_rating_'.$schema_id       ||
                           $meta_field['id'] == 'saswp_review_schema_rating_'.$schema_id        ||
                           $meta_field['id'] == 'local_rating_'.$schema_id                      ||
                           $meta_field['id'] == 'saswp_software_schema_rating_'.$schema_id      ||                             
                           $meta_field['id'] == 'saswp_service_schema_review_count_'.$schema_id || 
                           $meta_field['id'] == 'saswp_product_schema_review_count_'.$schema_id ||
                           $meta_field['id'] == 'saswp_review_schema_review_count_'.$schema_id  ||
                           $meta_field['id'] == 'local_review_count_'.$schema_id                ||
                           $meta_field['id'] == 'saswp_recipe_schema_rating_'.$schema_id        ||
                           $meta_field['id'] == 'saswp_recipe_schema_review_count_'.$schema_id ||
                           $meta_field['id'] == 'saswp_software_schema_rating_count_'.$schema_id     
                                
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
        /**
         * Function to save post specific metabox fields value
         * @param type $post_id
         * @return type null
         * @since version 1.0.4
         */
	public function saswp_post_specific_save_fields( $post_id ) {
                                            
		if ( ! isset( $_POST['post_specific_nonce'] ) )
			return $post_id;
		$nonce = $_POST['post_specific_nonce'];
		if ( !wp_verify_nonce( $nonce, 'post_specific_data' ) )
			return $post_id;
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;       
                if ( ! current_user_can( 'edit_post', $post_id ) ) 
                       return $post_id;    
                
                $option         = get_option('modify_schema_post_enable_'.$post_id);
                                                                                    
                $custom_schema = wp_unslash($_POST['saswp_custom_schema_field']);
                update_post_meta( $post_id, 'saswp_custom_schema_field', $custom_schema );
                                                                    
                if($option != 'enable'){
                    return;
                }  
                                                                               
                $post_meta    = array();                    
                
                if(is_array($_POST)){
                    $post_meta    = $_POST;
                }
                    
                $schema_count = 0;
                                                
                if(!empty($this->all_schema)){
                  $schema_count = count($this->all_schema);  
                }
                
                if($schema_count > 0){
                                                                      
                 foreach($this->all_schema as $schema){
                                          
                     foreach ($this->schema_type_element as $element){
                         
                        foreach($element as $key => $val){
                                                                                                                   
                            $element_val          = array();    
                            $data = (array) $_POST[$val.'_'.$schema->ID];  

                            foreach ($data as $supply){
                                
                                $sanitize_data = array();
                                
                                foreach($supply as $k => $el){  
                                    $sanitize_data[$k] = wp_kses_post(wp_unslash($el));                                   
                                }
                                
                                $element_val[] = $sanitize_data;     
                                
                            }
                            
                            update_post_meta( $post_id, $val.'_'.intval($schema->ID), $element_val);
                                                                                  
                        }    
                         
                     }                                          
                                                                                    
                     $response          = $this->saswp_get_fields_by_schema_type($schema->ID, 'save'); 
                     
                     $this->meta_fields = $response; 
                     
                        foreach ( $this->meta_fields as $meta_field ) {
                            
			if ( isset( $post_meta[ $meta_field['id'] ] ) ) {
                            
				switch ( $meta_field['type'] ) {
                                    
                                        case 'media':                                                                                                  
                                                $media_key       = $meta_field['id'].'_detail';                                                                                            
                                                $media_height    = sanitize_text_field( $post_meta[ $meta_field['id'].'_height' ] );
                                                $media_width     = sanitize_text_field( $post_meta[ $meta_field['id'].'_width' ] );
                                                $media_thumbnail = sanitize_text_field( $post_meta[ $meta_field['id'].'_thumbnail' ] );
                                                
                                                $media_detail = array(                                                    
                                                        'height'    => $media_height,
                                                        'width'     => $media_width,
                                                        'thumbnail' => $media_thumbnail,
                                                );
                                                
                                                update_post_meta( $post_id, $media_key, $media_detail);                                                    
                                                break;
					case 'email':
						$post_meta[ $meta_field['id'] ] = sanitize_email( $post_meta[ $meta_field['id'] ] );
						break;
					case 'text':
						$post_meta[ $meta_field['id'] ] = sanitize_text_field( $post_meta[ $meta_field['id'] ] );
						break;
                                        case 'textarea':
						$post_meta[ $meta_field['id'] ] = sanitize_textarea_field( $post_meta[ $meta_field['id'] ] );
						break;    
                                        default:
						$post_meta[ $meta_field['id'] ] = wp_unslash( $post_meta[ $meta_field['id'] ] );						
                                            
				}
				update_post_meta( $post_id, $meta_field['id'], $post_meta[ $meta_field['id'] ] );
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
                                       
            $response = $this->_local_sub_business[$business_type]; 
            
           if($response){                              
              echo json_encode(array('status'=>'t', 'result'=>$response)); 
           }else{
              echo json_encode(array('status'=>'f', 'result'=>'data not available')); 
           }
            wp_die();
        }
        
        /**
         * Function to get the fields of a particular schema type as an array
         * @global type $post
         * @global type $sd_data
         * @param type $schema_id
         * @return array
         * @since version 1.0.4
         */
        public function saswp_get_fields_by_schema_type( $schema_id, $condition = null ) {  
            
            global $post;
            global $sd_data;  
            
            $post_id = $post->ID; 
            
            $image_details = array();
            
            $image_id 	   = get_post_thumbnail_id();
            
            if($image_id){
                
                $image_details = wp_get_attachment_image_src($image_id, 'full');
                
            }                        
            
            if(empty($image_details[0]) || $image_details[0] === NULL ){
             
                if(isset($sd_data['sd_logo']['url'])){
                    $image_details[0] = $sd_data['sd_logo']['url'];
                }
                
	    }
            
            $current_user       = wp_get_current_user();
            $author_desc        = get_the_author_meta( 'user_description' ); 
            $author_details     = array();
            
            if(function_exists('get_avatar_data')){
                $author_details	= get_avatar_data($current_user->ID);           
            }
            
            $schema_type        = get_post_meta($schema_id, 'schema_type', true);  
            
            $business_type      = get_post_meta($schema_id, 'saswp_business_type', true); 
            $business_name      = get_post_meta($schema_id, 'saswp_business_name', true); 
                                    
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
                    
                    if($condition !=null){
                                                
                        if(!empty($this->_local_sub_business)){
                        
                        $sub_business_options = array(
                             'label'     => 'Sub Business Type',
                             'id'        => 'saswp_business_name_'.$schema_id,
                             'type'      => 'select',
                             'options'   => $this->_local_sub_business[$business_type],
                             'default'   => $business_name  
                        ); 

                    }
                        
                        
                    }else{
                        
                       if(!empty($this->_local_sub_business) && array_key_exists($business_type, $this->_local_sub_business)){
                        
                       $sub_business_options = array(
                            'label'     => 'Sub Business Type',
                            'id'        => 'saswp_business_name_'.$schema_id,
                            'type'      => 'select',
                            'options'   => $this->_local_sub_business[$business_type],
                            'default'   => $business_name  
                       ); 
                       
                    }
                        
                    }
                    
                    $meta_field = array(                   
                       array(
                            'label'   => 'ID',
                            'id'      => 'saswp_business_id_'.$schema_id,
                            'type'    => 'text',
                            'default' => 'LocalBusiness'                            
                        ),     
                       array(
                            'label'   => 'Business Type',
                            'id'      => 'saswp_business_type_'.$schema_id,
                            'type'    => 'select',
                            'default' => $business_type,
                            'options' => $this->_local_sub_business['all_business_type']
                        ),
                         $sub_business_options,
                        array(
                            'label'   => 'Business Name',
                            'id'      => 'local_business_name_'.$schema_id,
                            'type'    => 'text',                             
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
                       ),
                        array(
                            'label' => 'City',
                            'id' => 'local_city_'.$schema_id,
                            'type' => 'text',                            
                       ),
                        array(
                            'label' => 'State',
                            'id' => 'local_state_'.$schema_id,
                            'type' => 'text',                            
                       ),
                        array(
                            'label' => 'Postal Code',
                            'id' => 'local_postal_code_'.$schema_id,
                            'type' => 'text',                            
                       ),
                        array(
                            'label' => 'Latitude',
                            'id' => 'local_latitude_'.$schema_id,
                            'type' => 'text',                            
                       ),
                        array(
                            'label' => 'Longitude',
                            'id' => 'local_longitude_'.$schema_id,
                            'type' => 'text',                            
                       ),
                        array(
                            'label' => 'Phone',
                            'id' => 'local_phone_'.$schema_id,
                            'type' => 'text',                            
                       ),
                        array(
                            'label' => 'Website',
                            'id' => 'local_website_'.$schema_id,
                            'type' => 'text',                            
                       ),
                        array(
                            'label' => 'Image',
                            'id' => 'local_business_logo_'.$schema_id,
                            'type' => 'media',                            
                       ),
                        array(
                            'label' => 'Operation Days',
                            'id' => 'saswp_dayofweek_'.$schema_id,
                            'type' => 'textarea',                                                                                       
                       ),                        
                        array(
                            'label' => 'Price Range',
                            'id' => 'local_price_range_'.$schema_id,
                            'type' => 'text',                            
                       ),
                        array(
                            'label' => 'Menu',
                            'id' => 'local_menu_'.$schema_id,
                            'type' => 'text',                            
                       ),
                        array(
                            'label' => 'HasMap',
                            'id' => 'local_hasmap_'.$schema_id,
                            'type' => 'text',                            
                       ),
                        array(
                            'label' => 'Serves Cuisine',
                            'id' => 'local_serves_cuisine_'.$schema_id,
                            'type' => 'text',                            
                       ),                                                
                        array(
                            'label' => 'Facebook',
                            'id' => 'local_facebook_'.$schema_id,
                            'type' => 'text',                            
                        ),
                        array(
                            'label' => 'Twitter',
                            'id' => 'local_twitter_'.$schema_id,
                            'type' => 'text',                            
                        ),
                        array(
                            'label' => 'Instagram',
                            'id' => 'local_instagram_'.$schema_id,
                            'type' => 'text',                            
                        ),
                        array(
                            'label' => 'Pinterest',
                            'id' => 'local_pinterest_'.$schema_id,
                            'type' => 'text',                            
                        ),
                        array(
                            'label' => 'Linkedin',
                            'id' => 'local_linkedin_'.$schema_id,
                            'type' => 'text',                            
                        ),
                        array(
                            'label' => 'Soundcloud',
                            'id' => 'local_soundcloud_'.$schema_id,
                            'type' => 'text',                            
                        ),
                        array(
                            'label' => 'local_tumblr',
                            'id' => 'local_tumblr_'.$schema_id,
                            'type' => 'text',                            
                        ),
                        array(
                            'label' => 'Youtube',
                            'id' => 'local_youtube_'.$schema_id,
                            'type' => 'text',                            
                        ),                                                                                                                        
                        array(
                            'label' => 'Aggregate Rating',
                            'id' => 'local_enable_rating_'.$schema_id,
                            'type' => 'checkbox',                          
                        ),
                        array(
                            'label' => 'Rating',
                            'id' => 'local_rating_'.$schema_id,
                            'type' => 'text',                            
                        ),
                        array(
                            'label' => 'Number of Reviews',
                            'id' => 'local_review_count_'.$schema_id,
                            'type' => 'text',                            
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
                            'default'=> saswp_get_the_title()
                    ),
                    array(
                            'label' => 'Description',
                            'id' => 'saswp_blogposting_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => get_the_excerpt()
                    ),
                    array(
                            'label' => 'Keywords',
                            'id' => 'saswp_blogposting_keywords_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_get_the_tags()
                    ),    
                    array(
                            'label' => 'Name',
                            'id' => 'saswp_blogposting_name_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_get_the_title()
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
                            'label' => 'Author Description',
                            'id' => 'saswp_blogposting_author_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => $author_desc
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
                    ),
                    array(
                        'label' => 'Speakable',
                        'id' => 'saswp_blogposting_speakable_'.$schema_id,
                        'type' => 'checkbox',

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
                            'label' => 'Image',
                            'id' => 'saswp_newsarticle_image_'.$schema_id,
                            'type' => 'media',                            
                    ),    
                    array(
                            'label' => 'Headline',
                            'id' => 'saswp_newsarticle_headline_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_get_the_title(),
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
                            'default' => get_the_excerpt()
                    ),
                    array(
                            'label' => 'Keywords',
                            'id' => 'saswp_newsarticle_keywords_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_get_the_tags()
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
                            'default' => $post->post_content
                    ),
                     array(
                            'label' => 'Name',
                            'id' => 'saswp_newsarticle_name_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_get_the_title()
                    ), 
                     array(
                            'label' => 'Thumbnail URL',
                            'id' => 'saswp_newsarticle_thumbnailurl_'.$schema_id,
                            'type' => 'text'                            
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
                            'label' => 'Author Description',
                            'id' => 'saswp_newsarticle_author_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => $author_desc
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
                    array(
                        'label' => 'Speakable',
                        'id' => 'saswp_newsarticle_speakable_'.$schema_id,
                        'type' => 'checkbox',

                    )                        
                    );
                    break;
                
                case 'WebPage':
                    $meta_field = array(
                    array(
                            'label' => 'Name',
                            'id' => 'saswp_webpage_name_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_get_the_title()
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
                            'default' => get_the_excerpt()
                    ),
                    array(
                            'label' => 'Keywords',
                            'id' => 'saswp_webpage_keywords_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_get_the_tags()
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
                    ), 
                    array(
                            'label' => 'Headline',
                            'id' => 'saswp_webpage_headline_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_get_the_title(),
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
                            'label' => 'Author Description',
                            'id' => 'saswp_webpage_author_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => $author_desc
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
                    array(
                        'label' => 'Speakable',
                        'id' => 'saswp_webpage_speakable_'.$schema_id,
                        'type' => 'checkbox',

                    )    
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
                            'label'   => 'URL',
                            'id'      => 'saswp_article_url_'.$schema_id,
                            'type'    => 'text',
                            'default' => get_permalink(),
                    ),    
                    array(
                            'label' => 'Image',
                            'id' => 'saswp_article_image_'.$schema_id,
                            'type' => 'media'                            
                    ),
                    array(
                            'label' => 'Headline',
                            'id' => 'saswp_article_headline_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_get_the_title()
                    ),
                    array(
                            'label' => 'Description',
                            'id' => 'saswp_article_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => get_the_excerpt()
                    ),
                    array(
                            'label' => 'Keywords',
                            'id' => 'saswp_article_keywords_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_get_the_tags()
                    ),    
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
                            'label' => 'Author Description',
                            'id' => 'saswp_article_author_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => $author_desc
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
                    ),
                    array(
                        'label' => 'Speakable',
                        'id' => 'saswp_article_speakable_'.$schema_id,
                        'type' => 'checkbox',

                    )                        
                    );
                    break;
                
                case 'Event':
                                        
                    $meta_field = array(
                        array(
                            'label'   => 'Type',
                            'id'      => 'saswp_event_schema_type_'.$schema_id,
                            'type'    => 'select',                           
                            'options' => array(
                                ''                 => 'Select Type (Optional)',
                                'BusinessEvent'    => 'BusinessEvent',
                                'ChildrensEvent'   => 'ChildrensEvent',
                                'ComedyEvent'      => 'ComedyEvent',
                                'CourseInstance'   => 'CourseInstance',
                                'DanceEvent'       => 'DanceEvent',
                                'DeliveryEvent'    => 'DeliveryEvent',
                                'EducationEvent'   => 'EducationEvent',
                                'EventSeries'      => 'EventSeries',
                                'ExhibitionEvent'  => 'ExhibitionEvent',
                                'Festival'         => 'Festival',
                                'FoodEvent'        => 'FoodEvent',
                                'LiteraryEvent'    => 'LiteraryEvent',
                                'MusicEvent'       => 'MusicEvent',
                                'PublicationEvent' => 'PublicationEvent',
                                'SaleEvent'        => 'SaleEvent',
                                'ScreeningEvent'   => 'ScreeningEvent',
                                'SocialEvent'      => 'SocialEvent',
                                'SportsEvent'      => 'SportsEvent',
                                'TheaterEvent'     => 'TheaterEvent',
                                'VisualArtsEvent'  => 'VisualArtsEvent'
                            ) 
                        ),    
                        array(
                                'label' => 'Name',
                                'id' => 'saswp_event_schema_name_'.$schema_id,
                                'type' => 'text',                                
                        ),
                        array(
                                'label' => 'Description',
                                'id' => 'saswp_event_schema_description_'.$schema_id,
                                'type' => 'textarea',                                
                        ),
                        array(
                                'label' => 'Location Name',
                                'id' => 'saswp_event_schema_location_name_'.$schema_id,
                                'type' => 'text',                                
                        ),
                        array(
                                'label' => 'Location Street Address',
                                'id' => 'saswp_event_schema_location_streetaddress_'.$schema_id,
                                'type' => 'text',                                
                        ),
                        array(
                                'label' => 'Location Locality',
                                'id' => 'saswp_event_schema_location_locality_'.$schema_id,
                                'type' => 'text',                                
                        ),
                        array(
                                'label' => 'Location Region',
                                'id' => 'saswp_event_schema_location_region_'.$schema_id,
                                'type' => 'text',                                
                        ),
                        array(
                                'label' => 'Location PostalCode',
                                'id' => 'saswp_event_schema_location_postalcode_'.$schema_id,
                                'type' => 'text',                                
                        ),
                        array(
                                'label' => 'Start Date',
                                'id' => 'saswp_event_schema_start_date_'.$schema_id,
                                'type' => 'text',                                
                        ),
                        array(
                                'label' => 'End Date',
                                'id' => 'saswp_event_schema_end_date_'.$schema_id,
                                'type' => 'text',                                
                        ),
                        array(
                                'label' => 'Image',
                                'id' => 'saswp_event_schema_image_'.$schema_id,
                                'type' => 'media',                                
                        ),                        
                        array(
                                'label' => 'Price',
                                'id' => 'saswp_event_schema_price_'.$schema_id,
                                'type' => 'number',                                
                        ),
                        array(
                                'label' => 'Price Currency',
                                'id' => 'saswp_event_schema_price_currency_'.$schema_id,
                                'type' => 'text',                                
                        ),
                        array(
                            'label'   => 'Availability',
                            'id'      => 'saswp_event_schema_availability_'.$schema_id,
                            'type'    => 'select',                           
                            'options' => array(
                                     'InStock'           => 'In Stock',
                                     'OutOfStock'        => 'Out Of Stock',
                                     'Discontinued'      => 'Discontinued',
                                     'PreOrder'          => 'Pre Order', 
                            ) 
                       ),
                        array(
                                'label' => 'Valid From',
                                'id' => 'saswp_event_schema_validfrom_'.$schema_id,
                                'type' => 'text',                                
                        ),
                        array(
                                'label' => 'URL',
                                'id' => 'saswp_event_schema_url_'.$schema_id,
                                'type' => 'text',                                
                        ),
                    );
                    break;
                
                case 'TechArticle':                                        
                    $meta_field = array(
                    array(
                            'label' => 'Main Entity Of Page',
                            'id' => 'saswp_tech_article_main_entity_of_page_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink()
                    ),
                    array(
                            'label' => 'Image',
                            'id' => 'saswp_tech_article_image_'.$schema_id,
                            'type' => 'media',
                            'default' => $image_details[0]
                    ),
                    array(
                            'label' => 'Headline',
                            'id' => 'saswp_tech_article_headline_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_get_the_title()
                    ),
                    array(
                            'label' => 'Description',
                            'id' => 'saswp_tech_article_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => get_the_excerpt()
                    ) , 
                    array(
                            'label' => 'Keywords',
                            'id' => 'saswp_tech_article_keywords_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_get_the_tags()
                    ),     
                    array(
                            'label' => 'Date Published',
                            'id' => 'saswp_tech_article_date_published_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_date("Y-m-d")
                    ), 
                    array(
                            'label' => 'Date Modified',
                            'id' => 'saswp_tech_article_date_modified_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_modified_date("Y-m-d")
                    ),
                    array(
                            'label' => 'Author Name',
                            'id' => 'saswp_tech_article_author_name_'.$schema_id,
                            'type' => 'text',
                            'default' => $current_user->display_name
                    ),
                    array(
                            'label' => 'Author Description',
                            'id' => 'saswp_tech_article_author_name_'.$schema_id,
                            'type' => 'textarea',
                            'default' => $author_desc
                    ),    
                    array(
                            'label' => 'Organization Name',
                            'id' => 'saswp_tech_article_organization_name_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                    ),
                    array(
                            'label' => 'Organization Logo',
                            'id' => 'saswp_tech_article_organization_logo_'.$schema_id,
                            'type' => 'media',
                            'default' => isset($sd_data['sd_logo']) ? $sd_data['sd_logo']['url']:''
                    ),
                    array(
                        'label' => 'Speakable',
                        'id' => 'saswp_tech_article_speakable_'.$schema_id,
                        'type' => 'checkbox',

                    )                        
                    );
                    break;
                
                case 'Course':                                        
                    $meta_field = array(
                    array(
                            'label' => 'Name',
                            'id' => 'saswp_course_name_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_get_the_title()
                    ),
                    array(
                            'label' => 'Description',
                            'id' => 'saswp_course_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => $post->post_excerpt
                    ) ,    
                    array(
                            'label' => 'URL',
                            'id' => 'saswp_course_url_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink()
                    ),                     
                    array(
                            'label' => 'Date Published',
                            'id' => 'saswp_course_date_published_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_date("Y-m-d")
                    ), 
                    array(
                            'label' => 'Date Modified',
                            'id' => 'saswp_course_date_modified_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_modified_date("Y-m-d")
                    ),                    
                    array(
                            'label' => 'Provider Name',
                            'id' => 'saswp_course_provider_name_'.$schema_id,
                            'type' => 'text',
                            'default' => get_bloginfo()
                    ),
                    array(
                            'label' => 'Provider SameAs',
                            'id' => 'saswp_course_sameas_'.$schema_id,
                            'type' => 'text',
                            'default' => get_home_url() 
                    )                                                     
                    );
                    break;
                
                case 'DiscussionForumPosting':                                        
                    $meta_field = array(
                    array(
                            'label'   => 'mainEntityOfPage',
                            'id'      => 'saswp_dfp_main_entity_of_page_'.$schema_id,
                            'type'    => 'text',
                            'default' => get_permalink()
                    ),    
                    array(
                            'label' => 'Headline',
                            'id' => 'saswp_dfp_headline_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_get_the_title()
                    ),
                    array(
                            'label' => 'Description',
                            'id' => 'saswp_dfp_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => get_the_excerpt()
                    ) ,    
                    array(
                            'label' => 'URL',
                            'id' => 'saswp_dfp_url_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink()
                    ),
                    array(
                            'label' => 'Image',
                            'id' => 'saswp_dfp_image_'.$schema_id,
                            'type' => 'media',
                            'default' => $image_details[0]
                    ),    
                    array(
                            'label' => 'Date Published',
                            'id' => 'saswp_dfp_date_published_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_date("Y-m-d")
                    ), 
                    array(
                            'label' => 'Date Modified',
                            'id' => 'saswp_dfp_date_modified_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_modified_date("Y-m-d")
                    ),
                    array(
                            'label' => 'Author Name',
                            'id' => 'saswp_dfp_author_name_'.$schema_id,
                            'type' => 'text',
                            'default' => $current_user->display_name
                    ),
                    array(
                            'label'   => 'Author Description',
                            'id'      => 'saswp_dfp_author_description_'.$schema_id,
                            'type'    => 'textarea',
                            'default' => $author_desc
                    ),  
                    array(
                            'label'   => 'Organization Name',
                            'id'      => 'saswp_dfp_organization_name_'.$schema_id,
                            'type'    => 'text',
                            'default' => saswp_remove_warnings($sd_data, 'sd_name', 'saswp_string')
                    ),
                    array(
                            'label'   => 'Organization Logo',
                            'id'      => 'saswp_dfp_organization_logo_'.$schema_id,
                            'type'    => 'media',
                            'default' => $sd_data['sd_logo']['url']
                    ),    
                        
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
                            'default' => saswp_get_the_title()
                    ),
                    array(
                        'label' => 'Image',
                        'id' => 'saswp_recipe_image_'.$schema_id,
                        'type' => 'media'                        
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
                            'default' => get_the_excerpt()
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
                            'label' => 'Author Description',
                            'id' => 'saswp_recipe_author_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => $author_desc
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
                            'note' => 'Note: Separate Instructions step by semicolon ( ; )'  
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
                    array(
                        'label' => 'Aggregate Rating',
                        'id' => 'saswp_recipe_schema_enable_rating_'.$schema_id,
                        'type' => 'checkbox',                            
                    ),
                    array(
                        'label' => 'Rating',
                        'id' => 'saswp_recipe_schema_rating_'.$schema_id,
                        'type' => 'text',                            
                    ),
                    array(
                        'label' => 'Number of Reviews',
                        'id' => 'saswp_recipe_schema_review_count_'.$schema_id,
                        'type' => 'text',                            
                    )

                    );
                    break;
                
                case 'Product':                
                    
                    $service = new saswp_output_service();
                    $product_details = $service->saswp_woocommerce_product_details($post_id);     
                    
                    $meta_field = array(
                        
                    array(
                            'label'   => 'Name',
                            'id'      => 'saswp_product_schema_name_'.$schema_id,
                            'type'    => 'text',     
                            'default' => saswp_remove_warnings($product_details, 'product_name', 'saswp_string')
                    ),
                    array(
                            'label'   => 'Description',
                            'id'      => 'saswp_product_schema_description_'.$schema_id,
                            'type'    => 'textarea', 
                            'default' => saswp_remove_warnings($product_details, 'product_description', 'saswp_string')
                    ), 
                        array(
                            'label'    => 'Image',
                            'id'       => 'saswp_product_schema_image_'.$schema_id,
                            'type'     => 'media',                           
                     ),
                         array(
                            'label'    => 'Brand Name',
                            'id'       => 'saswp_product_schema_brand_name_'.$schema_id,
                            'type'     => 'text',
                             'default' => saswp_remove_warnings($product_details, 'product_brand', 'saswp_string')
                     ),
                        array(
                            'label'   => 'Price',
                            'id'      => 'saswp_product_schema_price_'.$schema_id,
                            'type'    => 'text',
                            'default' => saswp_remove_warnings($product_details, 'product_price', 'saswp_string')
                     ),
                        array(
                            'label'   => 'Price Valid Until',
                            'id'      => 'saswp_product_schema_priceValidUntil_'.$schema_id,
                            'type'    => 'text',
                            'default' => saswp_remove_warnings($product_details, 'product_priceValidUntil', 'saswp_string')    
                       ),
                        array(
                            'label'   => 'Currency',
                            'id'      => 'saswp_product_schema_currency_'.$schema_id,
                            'type'    => 'text', 
                            'default' => saswp_remove_warnings($product_details, 'product_currency', 'saswp_string')    
                      ),
                        array(
                            'label'   => 'Availability',
                            'id'      => 'saswp_product_schema_availability_'.$schema_id,
                            'type'    => 'select',                            
                            'options' => array(
                                     'InStock'           => 'In Stock',
                                     'OutOfStock'        => 'Out Of Stock',
                                     'Discontinued'      => 'Discontinued',
                                     'PreOrder'          => 'Pre Order', 
                            ),
                            'default' => saswp_remove_warnings($product_details, 'product_availability', 'saswp_string')
                     ),
                        array(
                            'label'   => 'Condition',
                            'id'      => 'saswp_product_schema_condition_'.$schema_id,
                            'type'    => 'select',                            
                            'options' => array(
                                     'NewCondition'              => 'New',
                                     'UsedCondition'             => 'Used',
                                     'RefurbishedCondition'      => 'Refurbished',
                                     'DamagedCondition'          => 'Damaged',   
                            ),
                     ),
                        array(
                            'label'   => 'SKU',
                            'id'      => 'saswp_product_schema_sku_'.$schema_id,
                            'type'    => 'text', 
                            'default' => saswp_remove_warnings($product_details, 'product_sku', 'saswp_string')    
                      ),
                        array(
                            'label'   => 'MPN',
                            'id'      => 'saswp_product_schema_mpn_'.$schema_id,
                            'type'    => 'text',
                            'note'    => 'OR',                            
                            'default' => saswp_remove_warnings($product_details, 'product_mpn', 'saswp_string')
                       ),                       
                        array(
                            'label'   => 'GTIN8',
                            'id'      => 'saswp_product_schema_gtin8_'.$schema_id,
                            'type'    => 'text',  
                            'default' => saswp_remove_warnings($product_details, 'product_gtin8', 'saswp_string')    
                       ),
                        array(
                            'label' => 'Seller Organization',
                            'id'    => 'saswp_product_schema_seller_'.$schema_id,
                            'type'  => 'text',                             
                       ),
                        array(
                            'label' => 'Aggregate Rating',
                            'id'    => 'saswp_product_schema_enable_rating_'.$schema_id,
                            'type'  => 'checkbox',                            
                        ),
                        array(
                            'label'   => 'Rating',
                            'id'      => 'saswp_product_schema_rating_'.$schema_id,
                            'type'    => 'text',
                            'default' => saswp_remove_warnings($product_details, 'product_average_rating', 'saswp_string')
                        ),
                        array(
                            'label'   => 'Number of Reviews',
                            'id'      => 'saswp_product_schema_review_count_'.$schema_id,
                            'type'    => 'text',
                            'default' => saswp_remove_warnings($product_details, 'product_review_count', 'saswp_string')
                        ),
                        
                    );
                    
                    break;
                
                case 'Service':
                    
                    $meta_field = array(
                    array(
                            'label' => 'Name',
                            'id' => 'saswp_service_schema_name_'.$schema_id,
                            'type' => 'text',                    
                    ),
                    array(
                            'label' => 'Service Type',
                            'id' => 'saswp_service_schema_type_'.$schema_id,
                            'type' => 'text',                            
                    ),
                    array(
                            'label' => 'Provider Name',
                            'id' => 'saswp_service_schema_provider_name_'.$schema_id,
                            'type' => 'text',                           
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
                    ),    
                    array(
                            'label' => 'Image',
                            'id' => 'saswp_service_schema_image_'.$schema_id,
                            'type' => 'media',                            
                    ),
                    array(
                            'label' => 'Locality',
                            'id' => 'saswp_service_schema_locality_'.$schema_id,
                            'type' => 'text',                            
                    ),
                    array(
                            'label' => 'Postal Code',
                            'id' => 'saswp_service_schema_postal_code_'.$schema_id,
                            'type' => 'text',                           
                    ),
                    array(
                            'label' => 'Telephone',
                            'id' => 'saswp_service_schema_telephone_'.$schema_id,
                            'type' => 'text',                            
                    ),
                    array(
                            'label' => 'Price Range',
                            'id' => 'saswp_service_schema_price_range_'.$schema_id,
                            'type' => 'text',                           
                    ),
                    array(
                            'label' => 'Description',
                            'id' => 'saswp_service_schema_description_'.$schema_id,
                            'type' => 'textarea',                           
                    ),
                    array(
                            'label' => 'Area Served (City)',
                            'id' => 'saswp_service_schema_area_served_'.$schema_id,
                            'type' => 'textarea',                           
                            'note'   => 'Note: Enter all the City name in comma separated',
                            'attributes' => array(
                                'placeholder' => 'New York, Los Angeles'
                            ),
                    ),
                    array(
                            'label' => 'Service Offer',
                            'id' => 'saswp_service_schema_service_offer_'.$schema_id,
                            'type' => 'textarea',                           
                            'note'   => 'Note: Enter all the service offer in comma separated',
                            'attributes' => array(
                                'placeholder' => 'Apartment light cleaning, carpet cleaning'
                            ),                                                        
                    ),
                        
                        array(
                            'label' => 'Aggregate Rating',
                            'id' => 'saswp_service_schema_enable_rating_'.$schema_id,
                            'type' => 'checkbox',                           
                        ),
                        array(
                            'label' => 'Rating',
                            'id' => 'saswp_service_schema_rating_'.$schema_id,
                            'type' => 'text',                            
                        ),
                        array(
                            'label' => 'Number of Reviews',
                            'id' => 'saswp_service_schema_review_count_'.$schema_id,
                            'type' => 'text',                           
                        ),                                                
                        
                    );
                    break;
                
                case 'Review':
                    
                    $service_schema_details = array();
                    
                    if(isset($_POST['saswp_review_schema_item_type_'.$schema_id])){
                                            
                    $reviewed_field = saswp_item_reviewed_fields(sanitize_text_field($_POST['saswp_review_schema_item_type_'.$schema_id]), $post_specific=1, $schema_id);    
                        
                        
                    }else{
                    
                    $item_type_by_post =  get_post_meta($post->ID, 'saswp_review_schema_item_type_'.$schema_id, true);
                    
                    if($item_type_by_post){
                     
                    $reviewed_field = saswp_item_reviewed_fields($item_type_by_post, $post_specific=1, $schema_id);        
                        
                    }else{
                     
                    $service_schema_details = get_post_meta($schema_id, 'saswp_review_schema_details', true);
                    $reviewed_field = saswp_item_reviewed_fields($service_schema_details['saswp_review_schema_item_type'], $post_specific=1, $schema_id);    
                        
                    }
                                            
                    }
                                        
                    $meta_field = array(
                    array(
                            'label'   => 'Item Reviewed Type',
                            'id'      => 'saswp_review_schema_item_type_'.$schema_id,
                            'type'    => 'select',
                            'options' => array(
                                        'Book'                  => 'Book',                             
                                        'Course'                => 'Course',                             
                                        'Event'                 => 'Event',                              
                                        'HowTo'                 => 'HowTo',   
                                        'LocalBusiness'         => 'LocalBusiness',                                 
                                        'MusicPlaylist'         => 'Music Playlist',                                                                                                                                                                                               
                                        'Product'               => 'Product',                                
                                        'Recipe'                => 'Recipe',                             
                                        'SoftwareApplication'   => 'SoftwareApplication',
                                        'VideoGame'             => 'VideoGame', 
                            ),                                                        
                         ),                                            
                        array(
                            'label' => 'Review Rating',
                            'id' => 'saswp_review_schema_enable_rating_'.$schema_id,
                            'type' => 'checkbox',                           
                        ),
                        array(
                            'label' => 'Rating Value',
                            'id' => 'saswp_review_schema_rating_'.$schema_id,
                            'type' => 'text',                            
                        ),
                        array(
                            'label' => 'Best Rating',
                            'id' => 'saswp_review_schema_review_count_'.$schema_id,
                            'type' => 'text',                            
                        ),
                        
                        
                    );
                    $meta_field = array_merge($meta_field, $reviewed_field);                    
                    break;
                
                case 'AudioObject':
                                        
                    $meta_field = array(
                    
                    array(
                            'label' => 'Name',
                            'id' => 'saswp_audio_schema_name_'.$schema_id,
                            'type' => 'text',                           
                    ),
                    array(
                            'label' => 'Description',
                            'id' => 'saswp_audio_schema_description_'.$schema_id,
                            'type' => 'textarea',                            
                    ),
                    array(
                            'label' => 'Content Url',
                            'id' => 'saswp_audio_schema_contenturl_'.$schema_id,
                            'type' => 'text',                            
                    ),
                   array(
                            'label' => 'Duration',
                            'id' => 'saswp_audio_schema_duration_'.$schema_id,
                            'type' => 'text',                            
                    ),
                     array(
                            'label' => 'Encoding Format',
                            'id' => 'saswp_audio_schema_encoding_format_'.$schema_id,
                            'type' => 'text',                           
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
                            'label' => 'Author Name',
                            'id' => 'saswp_audio_schema_author_name_'.$schema_id,
                            'type' => 'text',                            
                    ),
                    array(
                            'label'   => 'Author Description',
                            'id'      => 'saswp_audio_schema_author_description_'.$schema_id,
                            'type'    => 'textarea',
                            'default' => $author_desc
                    )    
                                                
                    );
                    break;
                
                case 'SoftwareApplication':
                                        
                    $meta_field = array(
                    
                    array(
                            'label' => 'Name',
                            'id' => 'saswp_software_schema_name_'.$schema_id,
                            'type' => 'text',                           
                    ),
                    array(
                            'label' => 'Description',
                            'id' => 'saswp_software_schema_description_'.$schema_id,
                            'type' => 'textarea',                            
                    ),
                    array(
                            'label' => 'Image',
                            'id'    => 'saswp_software_schema_image_'.$schema_id,
                            'type'  => 'media',                            
                    ),    
                    array(
                            'label' => 'Operating System',
                            'id' => 'saswp_software_schema_operating_system_'.$schema_id,
                            'type' => 'text',                            
                    ),
                    array(
                            'label' => 'Application Category',
                            'id' => 'saswp_software_schema_application_category_'.$schema_id,
                            'type' => 'text',                            
                    ),
                    array(
                            'label' => 'Price',
                            'id' => 'saswp_software_schema_price_'.$schema_id,
                            'type' => 'text',                            
                    ),
                    array(
                            'label' => 'Price Currency',
                            'id' => 'saswp_software_schema_price_currency_'.$schema_id,
                            'type' => 'text',                           
                    ),                            
                    array(
                            'label' => 'Date Published',
                            'id' => 'saswp_software_schema_date_published_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_date("Y-m-d")
                    ),
                    array(
                            'label' => 'Date Modified',
                            'id' => 'saswp_software_schema_date_modified_'.$schema_id,
                            'type' => 'text',
                            'default' => get_the_modified_date("Y-m-d")
                    ),
                    array(
                            'label' => 'Aggregate Rating',
                            'id' => 'saswp_software_schema_enable_rating_'.$schema_id,
                            'type' => 'checkbox',                           
                        ),
                    array(
                            'label' => 'Rating',
                            'id' => 'saswp_software_schema_rating_'.$schema_id,
                            'type' => 'text',                           
                        ),
                    array(
                            'label' => 'Rating Count',
                            'id' => 'saswp_software_schema_rating_count_'.$schema_id,
                            'type' => 'text',                            
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
                            'default' => saswp_get_the_title()
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
                            'default' => get_the_excerpt()
                    ),
                    array(
                            'label' => 'Name',
                            'id' => 'saswp_video_object_name_'.$schema_id,
                            'type' => 'text',
                            'default' => saswp_get_the_title()
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
                            'label' => 'Content Url',
                            'id' => 'saswp_video_object_content_url_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink()
                    ),
                    array(
                            'label' => 'Embed Url',
                            'id' => 'saswp_video_object_embed_url_'.$schema_id,
                            'type' => 'text',
                            'default' => get_permalink()
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
                            'label' => 'Author Description',
                            'id' => 'saswp_video_object_author_description_'.$schema_id,
                            'type' => 'textarea',
                            'default' => $author_desc
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
                
                case 'HowTo':
                    
                    $meta_field = array(
                    array(
                            'label'      => 'Name',
                            'id'         => 'saswp_howto_schema_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                    ),
                    array(
                            'label'      => 'Description',
                            'id'         => 'saswp_howto_schema_description_'.$schema_id,
                            'type'       => 'textarea',                            
                    ), 
                    array(
                            'label'      => 'Image',
                            'id'         => 'saswp_howto_schema_image_'.$schema_id,
                            'type'       => 'media',                            
                    ),     
                    array(
                            'label'      => 'Estimated Cost Currency',
                            'id'         => 'saswp_howto_ec_schema_currency_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'USD'
                            ), 
                    ),
                    array(
                            'label'      => 'Estimated Cost Value',
                            'id'         => 'saswp_howto_ec_schema_value_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => '20'
                            ), 
                    ),
                    array(
                            'label'      => 'Total Time',
                            'id'         => 'saswp_howto_schema_totaltime_'.$schema_id,
                            'type'       => 'text', 
                            'attributes' => array(
                                'placeholder' => 'PT30M'
                            ), 
                    ),
                     array(
                            'label'      => 'Date Published',
                            'id'         => 'saswp_howto_ec_schema_date_published_'.$schema_id,
                            'type'       => 'text', 
                            
                    ),
                        array(
                            'label'      => 'Date Modified',
                            'id'         => 'saswp_howto_ec_schema_date_modified_'.$schema_id,
                            'type'       => 'text',                             
                    )
                   );
                    break;
                
                case 'MedicalCondition':
                    
                    $meta_field = array(
                    array(
                            'label'      => 'Name',
                            'id'         => 'saswp_mc_schema_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                    ),
                    array(
                            'label'      => 'Alternate Name',
                            'id'         => 'saswp_mc_schema_alternate_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Alternate Name'
                            ), 
                    ),    
                    array(
                            'label'      => 'Description',
                            'id'         => 'saswp_mc_schema_description_'.$schema_id,
                            'type'       => 'textarea',                            
                    ),
                    array(
                            'label'      => 'Image',
                            'id'         => 'saswp_mc_schema_image_'.$schema_id,
                            'type'       => 'media',                            
                    ),                             
                    array(
                            'label'      => 'Associated Anatomy Name',
                            'id'         => 'saswp_mc_schema_anatomy_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                    ),
                    array(
                            'label'      => 'Medical Code',
                            'id'         => 'saswp_mc_schema_medical_code_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => '413'
                            ), 
                    ),
                    array(
                            'label'      => 'Coding System',
                            'id'         => 'saswp_mc_schema_coding_system_'.$schema_id,
                            'type'       => 'text', 
                            'attributes' => array(
                                'placeholder' => 'ICD-9'
                            ), 
                    ),
                     array(
                            'label'      => 'Diagnosis Name',
                            'id'         => 'saswp_mc_schema_diagnosis_name_'.$schema_id,
                            'type'       => 'text', 
                            
                    )                     
                   );
                    break;
                
                case 'VideoGame':
                    
                    $meta_field = array(
                    array(
                            'label'      => 'Name',
                            'id'         => 'saswp_vg_schema_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                    ),
                    array(
                            'label'      => 'URL',
                            'id'         => 'saswp_vg_schema_url_'.$schema_id,
                            'type'       => 'text',
                            
                    ),
                    array(
                            'label'      => 'Image',
                            'id'         => 'saswp_vg_schema_image_'.$schema_id,
                            'type'       => 'media',
                            
                    ),
                    array(
                            'label'      => 'Description',
                            'id'         => 'saswp_vg_schema_description_'.$schema_id,
                            'type'       => 'textarea',
                            
                    ),
                    array(
                            'label'      => 'Operating System',
                            'id'         => 'saswp_vg_schema_operating_system_'.$schema_id,
                            'type'       => 'text',
                            
                    ),
                    array(
                            'label'      => 'Application Category',
                            'id'         => 'saswp_vg_schema_application_category_'.$schema_id,
                            'type'       => 'text',
                            
                    ),
                    array(
                            'label'      => 'Author Name',
                            'id'         => 'saswp_vg_schema_author_name_'.$schema_id,
                            'type'       => 'text',
                            
                    ),
                    array(
                            'label'      => 'Price',
                            'id'         => 'saswp_vg_schema_price_'.$schema_id,
                            'type'       => 'text',
                            
                    ),
                    array(
                            'label'      => 'Price Currency',
                            'id'         => 'saswp_vg_schema_price_currency_'.$schema_id,
                            'type'       => 'text',
                            
                    ),    
                    array(
                            'label'   => 'Availability',
                            'id'      => 'saswp_vg_schema_price_availability_'.$schema_id,
                            'type'    => 'select',                            
                            'options' => array(
                                     'InStock'           => 'In Stock',
                                     'OutOfStock'        => 'Out Of Stock',
                                     'Discontinued'      => 'Discontinued',
                                     'PreOrder'          => 'Pre Order', 
                            ) 
                       ), 
                    array(
                            'label'      => 'Publisher',
                            'id'         => 'saswp_vg_schema_publisher_'.$schema_id,
                            'type'       => 'text',
                            
                    ),
                    array(
                            'label'      => 'Genre',
                            'id'         => 'saswp_vg_schema_genre_'.$schema_id,
                            'type'       => 'text',
                            
                    ),
                    array(
                            'label'      => 'Processor Requirements',
                            'id'         => 'saswp_vg_schema_processor_requirements_'.$schema_id,
                            'type'       => 'text',
                            
                    ),
                    array(
                            'label'      => 'Memory Requirements',
                            'id'         => 'saswp_vg_schema_memory_requirements_'.$schema_id,
                            'type'       => 'text',
                            
                    ),
                    array(
                            'label'      => 'Storage Requirements',
                            'id'         => 'saswp_vg_schema_storage_requirements_'.$schema_id,
                            'type'       => 'text',
                            
                    ),
                    array(
                            'label'      => 'Game Platform',
                            'id'         => 'saswp_vg_schema_game_platform_'.$schema_id,
                            'type'       => 'text',
                            
                    ),
                    array(
                            'label'      => 'Cheat Code',
                            'id'         => 'saswp_vg_schema_cheat_code_'.$schema_id,
                            'type'       => 'text',
                            
                    ),
                    array(
                            'label' => 'Aggregate Rating',
                            'id' => 'saswp_vg_schema_enable_rating_'.$schema_id,
                            'type' => 'checkbox',                          
                        ),
                        array(
                            'label' => 'Rating',
                            'id' => 'saswp_vg_schema_rating_'.$schema_id,
                            'type' => 'text',                           
                        ),
                        array(
                            'label' => 'Number of Reviews',
                            'id' => 'saswp_vg_schema_review_count_'.$schema_id,
                            'type' => 'text',                           
                        ),    
                        
                   );
                    break;
                
                case 'TVSeries':
                    
                    $meta_field = array(
                    array(
                            'label'      => 'Name',
                            'id'         => 'saswp_tvseries_schema_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                    ),
                     array(
                            'label'      => 'Image',
                            'id'         => 'saswp_tvseries_schema_image_'.$schema_id,
                            'type'       => 'media'                            
                    ),
                    array(
                            'label'      => 'Author Name',
                            'id'         => 'saswp_tvseries_schema_author_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Author Name'
                            ), 
                    ),    
                    array(
                            'label'      => 'Description',
                            'id'         => 'saswp_tvseries_schema_description_'.$schema_id,
                            'type'       => 'textarea'                            
                    )  
                        
                   );
                    break;
                
                case 'Apartment':
                    
                    $meta_field = array(
                    array(
                            'label'      => 'Name',
                            'id'         => 'saswp_apartment_schema_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                    ),
                    array(
                            'label'      => 'URL',
                            'id'         => 'saswp_apartment_schema_url_'.$schema_id,
                            'type'       => 'text',
                            'default'    => get_permalink()
                    ),
                    array(
                            'label'      => 'Image',
                            'id'         => 'saswp_apartment_schema_image_'.$schema_id,
                            'type'       => 'media',
                            'default'    => get_permalink()
                    ),    
                    array(
                            'label'      => 'Description',
                            'id'         => 'saswp_apartment_schema_description_'.$schema_id,
                            'type'       => 'textarea',
                            'attributes' => array(
                                'placeholder' => 'Description'
                            ), 
                    ),
                    array(
                            'label'      => 'Number Of Rooms',
                            'id'         => 'saswp_apartment_schema_numberofrooms_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => '5'
                            ), 
                    ),
                    array(
                            'label'      => 'Floor Size',
                            'id'         => 'saswp_apartment_schema_floor_size_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => '140 Sq.Ft'
                            ), 
                    ),    
                    array(
                            'label'      => 'Country',
                            'id'         => 'saswp_apartment_schema_country_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => 'Locality',
                            'id'         => 'saswp_apartment_schema_locality_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Region',
                            'id'         => 'saswp_apartment_schema_region_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Postal Code',
                            'id'         => 'saswp_apartment_schema_postalcode_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Telephone',
                            'id'         => 'saswp_apartment_schema_telephone_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'GeoCoordinates Latitude',
                            'id'         => 'saswp_apartment_schema_latitude_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => '17.412'
                            ), 
                    ),
                    array(
                            'label'      => 'GeoCoordinates Longitude',
                            'id'         => 'saswp_apartment_schema_longitude_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => '78.433'
                            ),
                    ),    
                                              
                   );
                    break;
                
                case 'House':
                    
                    $meta_field = array(
                    array(
                            'label'      => 'Name',
                            'id'         => 'saswp_house_schema_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                    ),
                    array(
                            'label'      => 'URL',
                            'id'         => 'saswp_house_schema_url_'.$schema_id,
                            'type'       => 'text',
                            'default'    => get_permalink()
                    ),
                    array(
                            'label'      => 'Image',
                            'id'         => 'saswp_house_schema_image_'.$schema_id,
                            'type'       => 'media',                            
                    ),
                    array(
                            'label'      => 'Description',
                            'id'         => 'saswp_house_schema_description_'.$schema_id,
                            'type'       => 'textarea',
                            'attributes' => array(
                                'placeholder' => 'Description'
                            ), 
                    ),
                     array(
                            'label'      => 'Pets Allowed',
                            'id'         => 'saswp_house_schema_pets_allowed_'.$schema_id,
                            'type'       => 'select',
                            'options' => array(
                                     'yes'       => 'Yes',
                                     'no'        => 'No'                                                                          
                            ) 
                    ),
                    array(
                            'label'      => 'Country',
                            'id'         => 'saswp_house_schema_country_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => 'Locality',
                            'id'         => 'saswp_house_schema_locality_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Region',
                            'id'         => 'saswp_house_schema_region_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Postal Code',
                            'id'         => 'saswp_house_schema_postalcode_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Telephone',
                            'id'         => 'saswp_house_schema_telephone_'.$schema_id,
                            'type'       => 'text',                            
                    ),                      
                    array(
                            'label'      => 'Has Map',
                            'id'         => 'saswp_house_schema_hasmap_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Floor Size',
                            'id'         => 'saswp_house_schema_floor_size_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                    array(
                            'label'      => 'Number of Rooms',
                            'id'         => 'saswp_house_schema_no_of_rooms_'.$schema_id,
                            'type'       => 'text',                            
                    )                                                 
                   );
                    break;   
                
                case 'SingleFamilyResidence':
                    
                    $meta_field = array(
                    array(
                            'label'      => 'Name',
                            'id'         => 'saswp_sfr_schema_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                    ),
                    array(
                            'label'      => 'URL',
                            'id'         => 'saswp_sfr_schema_url_'.$schema_id,
                            'type'       => 'text',
                            'default'    => get_permalink()
                    ),
                    array(
                            'label'      => 'Image',
                            'id'         => 'saswp_sfr_schema_image_'.$schema_id,
                            'type'       => 'media',                            
                    ),
                    array(
                            'label'      => 'Description',
                            'id'         => 'saswp_sfr_schema_description_'.$schema_id,
                            'type'       => 'textarea',
                            'attributes' => array(
                                'placeholder' => 'Description'
                            ), 
                    ),
                    array(
                            'label'      => 'Number Of Rooms',
                            'id'         => 'saswp_sfr_schema_numberofrooms_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => '5'
                            ), 
                    ),    
                     array(
                            'label'      => 'Pets Allowed',
                            'id'         => 'saswp_sfr_schema_pets_allowed_'.$schema_id,
                            'type'       => 'select',
                            'options' => array(
                                     'yes'       => 'Yes',
                                     'no'        => 'No'                                                                          
                            ) 
                    ),
                    array(
                            'label'      => 'Country',
                            'id'         => 'saswp_sfr_schema_country_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => 'Locality',
                            'id'         => 'saswp_sfr_schema_locality_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Region',
                            'id'         => 'saswp_sfr_schema_region_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Postal Code',
                            'id'         => 'saswp_sfr_schema_postalcode_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Telephone',
                            'id'         => 'saswp_sfr_schema_telephone_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Has Map',
                            'id'         => 'saswp_sfr_schema_hasmap_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Floor Size',
                            'id'         => 'saswp_sfr_schema_floor_size_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                    array(
                            'label'      => 'Number of Rooms',
                            'id'         => 'saswp_sfr_schema_no_of_rooms_'.$schema_id,
                            'type'       => 'text',                            
                    )    
                                              
                   );
                    break;
                
                case 'TouristAttraction':
                    
                    $meta_field = array(
                    array(
                            'label'      => 'Name',
                            'id'         => 'saswp_ta_schema_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                    ),
                    array(
                            'label'      => 'Description',
                            'id'         => 'saswp_ta_schema_description_'.$schema_id,
                            'type'       => 'textarea',
                            'attributes' => array(
                                'placeholder' => 'Description'
                            ), 
                    ),
                    array(
                            'label'      => 'Image',
                            'id'         => 'saswp_ta_schema_image_'.$schema_id,
                            'type'       => 'media',                            
                    ),    
                    array(
                            'label'      => 'URL',
                            'id'         => 'saswp_ta_schema_url_'.$schema_id,
                            'type'       => 'text',
                            'default'    => get_permalink()
                    ),
                    array(
                            'label'      => 'Is Accessible For Free',
                            'id'         => 'saswp_ta_schema_is_acceesible_free_'.$schema_id,
                            'type'       => 'select',
                            'options' => array(
                                'true' => 'True',
                                'false' => 'False',
                            ),
                    ),
                    array(
                            'label'      => 'Address Locality',
                            'id'         => 'saswp_ta_schema_locality_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Address Region',
                            'id'         => 'saswp_ta_schema_region_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                    array(
                            'label'      => 'Address Country',
                            'id'         => 'saswp_ta_schema_country_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Address PostalCode',
                            'id'         => 'saswp_ta_schema_postal_code_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                                              
                   );
                    break;
                
                case 'TouristDestination':
                    
                    $meta_field = array(
                    array(
                            'label'      => 'Name',
                            'id'         => 'saswp_td_schema_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                    ),
                    array(
                            'label'      => 'Description',
                            'id'         => 'saswp_td_schema_description_'.$schema_id,
                            'type'       => 'textarea',
                            'attributes' => array(
                                'placeholder' => 'Description'
                            ), 
                    ),
                    array(
                            'label'      => 'Image',
                            'id'         => 'saswp_td_schema_image_'.$schema_id,
                            'type'       => 'media',                            
                    ),    
                    array(
                            'label'      => 'URL',
                            'id'         => 'saswp_td_schema_url_'.$schema_id,
                            'type'       => 'text',
                            'default'    => get_permalink()
                    ),                                                                                
                    array(
                            'label'      => 'Address Locality',
                            'id'         => 'saswp_td_schema_locality_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Address Region',
                            'id'         => 'saswp_td_schema_region_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                    array(
                            'label'      => 'Address Country',
                            'id'         => 'saswp_td_schema_country_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Address PostalCode',
                            'id'         => 'saswp_td_schema_postal_code_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                                              
                   );
                    break;
                
                case 'LandmarksOrHistoricalBuildings':
                    
                    $meta_field = array(
                    array(
                            'label'      => 'Name',
                            'id'         => 'saswp_lorh_schema_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                    ),
                    array(
                            'label'      => 'Description',
                            'id'         => 'saswp_lorh_schema_description_'.$schema_id,
                            'type'       => 'textarea',
                            'attributes' => array(
                                'placeholder' => 'Description'
                            ), 
                    ),
                    array(
                            'label'      => 'Image',
                            'id'         => 'saswp_lorh_schema_image_'.$schema_id,
                            'type'       => 'media',                            
                    ),    
                    array(
                            'label'      => 'URL',
                            'id'         => 'saswp_lorh_schema_url_'.$schema_id,
                            'type'       => 'text',
                            'default'    => get_permalink()
                    ), 
                    array(
                            'label'      => 'Has Map',
                            'id'         => 'saswp_lorh_schema_hasmap_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Is Accessible For Free',
                            'id'         => 'saswp_lorh_schema_is_acceesible_free_'.$schema_id,
                            'type'       => 'select',
                            'options'    => array(
                                    'true'   => 'True',
                                    'false'  => 'False',
                            )
                    ),
                    array(
                            'label'      => 'Maximum Attendee Capacity',
                            'id'         => 'saswp_lorh_schema_maximum_a_capacity_'.$schema_id,
                            'type'       => 'number',                            
                    ),    
                    array(
                            'label'      => 'Address Locality',
                            'id'         => 'saswp_lorh_schema_locality_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Address Region',
                            'id'         => 'saswp_lorh_schema_region_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                    array(
                            'label'      => 'Address Country',
                            'id'         => 'saswp_lorh_schema_country_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Address PostalCode',
                            'id'         => 'saswp_lorh_schema_postal_code_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                                              
                   );
                    break;
                
                case 'HinduTemple':
                    
                    $meta_field = array(
                    array(
                            'label'      => 'Name',
                            'id'         => 'saswp_hindutemple_schema_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                    ),
                    array(
                            'label'      => 'Description',
                            'id'         => 'saswp_hindutemple_schema_description_'.$schema_id,
                            'type'       => 'textarea',
                            'attributes' => array(
                                'placeholder' => 'Description'
                            ), 
                    ),
                    array(
                            'label'      => 'Image',
                            'id'         => 'saswp_hindutemple_schema_image_'.$schema_id,
                            'type'       => 'media',                            
                    ),    
                    array(
                            'label'      => 'URL',
                            'id'         => 'saswp_hindutemple_schema_url_'.$schema_id,
                            'type'       => 'text',
                            'default'    => get_permalink()
                    ),  
                    array(
                            'label'      => 'Has Map',
                            'id'         => 'saswp_hindutemple_schema_hasmap_'.$schema_id,
                            'type'       => 'text',                            
                    ),                      
                    array(
                            'label'      => 'Is Accessible For Free',
                            'id'         => 'saswp_hindutemple_schema_is_accesible_free_'.$schema_id,
                            'type'       => 'select',
                            'options'    => array(
                                    'true'   => 'True',
                                    'false'  => 'False',
                            )
                    ),
                    array(
                            'label'      => 'Maximum Attendee Capacity',
                            'id'         => 'saswp_hindutemple_schema_maximum_a_capacity_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                    array(
                            'label'      => 'Address Locality',
                            'id'         => 'saswp_hindutemple_schema_locality_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Address Region',
                            'id'         => 'saswp_hindutemple_schema_region_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                    array(
                            'label'      => 'Address Country',
                            'id'         => 'saswp_hindutemple_schema_country_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Address PostalCode',
                            'id'         => 'saswp_hindutemple_schema_postal_code_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                                              
                   );
                    break;
                
                case 'Church':
                    
                    $meta_field = array(
                    array(
                            'label'      => 'Name',
                            'id'         => 'saswp_church_schema_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                    ),
                    array(
                            'label'      => 'Description',
                            'id'         => 'saswp_church_schema_description_'.$schema_id,
                            'type'       => 'textarea',
                            'attributes' => array(
                                'placeholder' => 'Description'
                            ), 
                    ),
                    array(
                            'label'      => 'Image',
                            'id'         => 'saswp_church_schema_image_'.$schema_id,
                            'type'       => 'media',                            
                    ),    
                    array(
                            'label'      => 'URL',
                            'id'         => 'saswp_church_schema_url_'.$schema_id,
                            'type'       => 'text',
                            'default'    => get_permalink()
                    ),  
                    array(
                            'label'      => 'Has Map',
                            'id'         => 'saswp_church_schema_hasmap_'.$schema_id,
                            'type'       => 'text',                            
                    ),                      
                    array(
                            'label'      => 'Is Accessible For Free',
                            'id'         => 'saswp_church_schema_is_accesible_free_'.$schema_id,
                            'type'       => 'select',
                            'options'    => array(
                                    'true'   => 'True',
                                    'false'  => 'False',
                            )
                    ),
                    array(
                            'label'      => 'Maximum Attendee Capacity',
                            'id'         => 'saswp_church_schema_maximum_a_capacity_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                    array(
                            'label'      => 'Address Locality',
                            'id'         => 'saswp_church_schema_locality_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Address Region',
                            'id'         => 'saswp_church_schema_region_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                    array(
                            'label'      => 'Address Country',
                            'id'         => 'saswp_church_schema_country_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Address PostalCode',
                            'id'         => 'saswp_church_schema_postal_code_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                                              
                   );
                    break;
                
                case 'Mosque':
                    
                    $meta_field = array(
                    array(
                            'label'      => 'Name',
                            'id'         => 'saswp_mosque_schema_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                    ),
                    array(
                            'label'      => 'Description',
                            'id'         => 'saswp_mosque_schema_description_'.$schema_id,
                            'type'       => 'textarea',
                            'attributes' => array(
                                'placeholder' => 'Description'
                            ), 
                    ),
                    array(
                            'label'      => 'Image',
                            'id'         => 'saswp_mosque_schema_image_'.$schema_id,
                            'type'       => 'media',                            
                    ),    
                    array(
                            'label'      => 'URL',
                            'id'         => 'saswp_mosque_schema_url_'.$schema_id,
                            'type'       => 'text',
                            'default'    => get_permalink()
                    ),
                    array(
                            'label'      => 'Has Map',
                            'id'         => 'saswp_mosque_schema_hasmap_'.$schema_id,
                            'type'       => 'text',                            
                    ),                      
                    array(
                            'label'      => 'Is Accessible For Free',
                            'id'         => 'saswp_mosque_schema_is_accesible_free_'.$schema_id,
                            'type'       => 'select',
                            'options'    => array(
                                    'true'   => 'True',
                                    'false'  => 'False',
                            )
                    ),
                    array(
                            'label'      => 'Maximum Attendee Capacity',
                            'id'         => 'saswp_mosque_schema_maximum_a_capacity_'.$schema_id,
                            'type'       => 'text',                            
                    ),  
                    array(
                            'label'      => 'Address Locality',
                            'id'         => 'saswp_mosque_schema_locality_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                    array(
                            'label'      => 'Address Region',
                            'id'         => 'saswp_mosque_schema_region_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                    array(
                            'label'      => 'Address Country',
                            'id'         => 'saswp_mosque_schema_country_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Address PostalCode',
                            'id'         => 'saswp_mosque_schema_postal_code_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                                              
                   );
                    break;
                
                case 'JobPosting':
                    
                    $meta_field = array(
                    array(
                            'label'      => 'Title',
                            'id'         => 'saswp_jobposting_schema_title_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Title'
                            ), 
                    ),
                    array(
                            'label'      => 'Description',
                            'id'         => 'saswp_jobposting_schema_description_'.$schema_id,
                            'type'       => 'textarea',
                            'attributes' => array(
                                'placeholder' => 'Description'
                            ), 
                    ),
                    array(
                            'label'      => 'URL',
                            'id'         => 'saswp_jobposting_schema_url_'.$schema_id,
                            'type'       => 'text',
                            'default'    => get_permalink()
                    ),     
                    array(
                            'label'      => 'Date Posted',
                            'id'         => 'saswp_jobposting_schema_dateposted_'.$schema_id,
                            'type'       => 'text',                             
                    ),
                    array(
                            'label'      => 'Valid Through',
                            'id'         => 'saswp_jobposting_schema_validthrough_'.$schema_id,
                            'type'       => 'text',                             
                    ),
                    array(
                            'label'      => 'Employment Type',
                            'id'         => 'saswp_jobposting_schema_employment_type_'.$schema_id,
                            'type'       => 'select', 
                            'options'    => array(
                                'Full-Time'  => 'Full-Time',
                                'Part-Time'  => 'Part-Time',
                                'Contractor' => 'Contractor',       
                            )
                    ), 
                    array(
                            'label'      => 'Hiring Organization Name',
                            'id'         => 'saswp_jobposting_schema_ho_name_'.$schema_id,
                            'type'       => 'text',                             
                    ),
                    array(
                            'label'      => 'Hiring Organization URL',
                            'id'         => 'saswp_jobposting_schema_ho_url_'.$schema_id,
                            'type'       => 'text',                             
                    ),
                    array(
                            'label'      => 'Hiring Organization Logo',
                            'id'         => 'saswp_jobposting_schema_ho_logo_'.$schema_id,
                            'type'       => 'media',                             
                    ),
                    array(
                            'label'      => 'Street Address',
                            'id'         => 'saswp_jobposting_schema_street_address_'.$schema_id,
                            'type'       => 'text',                             
                    ),
                    array(
                            'label'      => 'Address Locality',
                            'id'         => 'saswp_jobposting_schema_locality_'.$schema_id,
                            'type'       => 'text',                             
                    ),
                    array(
                            'label'      => 'Address Region',
                            'id'         => 'saswp_jobposting_schema_region_'.$schema_id,
                            'type'       => 'text',                             
                    ),
                    array(
                            'label'      => 'Address Postal Code',
                            'id'         => 'saswp_jobposting_schema_postalcode_'.$schema_id,
                            'type'       => 'text',                             
                    ),
                    array(
                            'label'      => 'Address Country',
                            'id'         => 'saswp_jobposting_schema_country_'.$schema_id,
                            'type'       => 'text',                             
                    ),
                    array(
                            'label'      => 'Base Salary Currency',
                            'id'         => 'saswp_jobposting_schema_bs_currency_'.$schema_id,
                            'type'       => 'text', 
                            'attributes' => array(
                                'placeholder' => 'USD'
                            )
                    ),
                    array(
                            'label'      => 'Base Salary Value',
                            'id'         => 'saswp_jobposting_schema_bs_value_'.$schema_id,
                            'type'       => 'text', 
                            'attributes' => array(
                                'placeholder' => '40.00'
                            )
                    ),
                    array(
                            'label'      => 'Base Salary Unit Text',
                            'id'         => 'saswp_jobposting_schema_bs_unittext_'.$schema_id,
                            'type'       => 'text', 
                            'attributes' => array(
                                'placeholder' => 'Hour'
                            )
                    ),    
                   
                                              
                   );
                    break;
               
                case 'Trip':
                    
                    $meta_field = array(
                    array(
                            'label'      => 'Name',
                            'id'         => 'saswp_trip_schema_name_'.$schema_id,
                            'type'       => 'text',
                            'attributes' => array(
                                'placeholder' => 'Name'
                            ), 
                    ),
                    array(
                            'label'      => 'Description',
                            'id'         => 'saswp_trip_schema_description_'.$schema_id,
                            'type'       => 'textarea',
                            'attributes' => array(
                                'placeholder' => 'Description'
                            )
                    ),
                    array(
                            'label'      => 'URL',
                            'id'         => 'saswp_trip_schema_url_'.$schema_id,
                            'type'       => 'text',
                            'default'    => get_permalink() 
                    ),
                    array(
                            'label'      => 'Image',
                            'id'         => 'saswp_trip_schema_image_'.$schema_id,
                            'type'       => 'media'                            
                    )    
                        
                        
                   );
                    break;
                
                case 'FAQ':
                    
                    $meta_field = array(
                    array(
                            'label'      => 'Headline',
                            'id'         => 'saswp_faq_headline_'.$schema_id,
                            'type'       => 'text'                             
                    ),
                    array(
                            'label'      => 'Tags',
                            'id'         => 'saswp_faq_keywords_'.$schema_id,
                            'type'       => 'text'                            
                    ),
                    array(
                            'label'      => 'Author',
                            'id'         => 'saswp_faq_author_'.$schema_id,
                            'type'       => 'text'                            
                    ),    
                    array(
                            'label'      => 'DateCreated',
                            'id'         => 'saswp_faq_date_created_'.$schema_id,
                            'type'       => 'text'                            
                    ),
                    array(
                            'label'      => 'DatePublished',
                            'id'         => 'saswp_faq_date_published_'.$schema_id,
                            'type'       => 'text'                            
                    ),
                    array(
                            'label'      => 'DateModified',
                            'id'         => 'saswp_faq_date_modified_'.$schema_id,
                            'type'       => 'text'                            
                    )                                                    
                   );                                                                 
                   
                    break;
                
                case 'Person':
                    
                    $meta_field = array(
                    array(
                            'label'      => 'Name',
                            'id'         => 'saswp_person_schema_name_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => 'Description',
                            'id'         => 'saswp_person_schema_description_'.$schema_id,
                            'type'       => 'textarea',                           
                    ),    
                    array(
                            'label'      => 'URL',
                            'id'         => 'saswp_person_schema_url_'.$schema_id,
                            'type'       => 'text',
                            'default'    => get_permalink()
                    ),    
                    array(
                            'label'      => 'Street Address',
                            'id'         => 'saswp_person_schema_street_address_'.$schema_id,
                            'type'       => 'text',
                           
                    ),
                    array(
                            'label'      => 'Locality',
                            'id'         => 'saswp_person_schema_locality_'.$schema_id,
                            'type'       => 'text',
                           
                    ),
                    array(
                            'label'      => 'Region',
                            'id'         => 'saswp_person_schema_region_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => 'Postal Code',
                            'id'         => 'saswp_person_schema_postal_code_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => 'Country',
                            'id'         => 'saswp_person_schema_country_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => 'Email',
                            'id'         => 'saswp_person_schema_email_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => 'Telephone',
                            'id'         => 'saswp_person_schema_telephone_'.$schema_id,
                            'type'       => 'text',                           
                    ),    
                    array(
                            'label'      => 'Gender',
                            'id'         => 'saswp_person_schema_gender_'.$schema_id,
                            'type'       => 'select',
                            'options'    => array(
                                    'Male'   => 'Male',
                                    'Female' => 'Female',    
                            )
                    ),
                    array(
                            'label'      => 'Date Of Birth',
                            'id'         => 'saswp_person_schema_date_of_birth_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Member Of',
                            'id'         => 'saswp_person_schema_member_of_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Nationality',
                            'id'         => 'saswp_person_schema_nationality_'.$schema_id,
                            'type'       => 'text',                            
                    ),
                    array(
                            'label'      => 'Image',
                            'id'         => 'saswp_person_schema_image_'.$schema_id,
                            'type'       => 'media',                            
                    ),
                    array(
                            'label'      => 'Job Title',
                            'id'         => 'saswp_person_schema_job_title_'.$schema_id,
                            'type'       => 'text',                            
                    ),    
                                                                    
                   );
                    break;
                
                case 'DataFeed':
                    
                    $meta_field = array(
                    array(
                            'label'      => 'Name',
                            'id'         => 'saswp_data_feed_schema_name_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => 'Description',
                            'id'         => 'saswp_data_feed_schema_description_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => 'DateModified',
                            'id'         => 'saswp_data_feed_schema_date_modified_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => 'License',
                            'id'         => 'saswp_data_feed_schema_license_'.$schema_id,
                            'type'       => 'text',                           
                    )    
                   );
                    break;
                
                case 'MusicPlaylist':
                    
                    $meta_field = array(
                    array(
                            'label'      => 'Name',
                            'id'         => 'saswp_music_playlist_name_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => 'Description',
                            'id'         => 'saswp_music_playlist_description_'.$schema_id,
                            'type'       => 'textarea',                           
                    ), 
                    array(
                            'label'      => 'URL',
                            'id'         => 'saswp_music_playlist_url_'.$schema_id,
                            'type'       => 'text',                           
                    )    
                        
                   );
                    break;
                
                case 'MusicAlbum':
                    
                    $meta_field = array(
                    array(
                            'label'      => 'Name',
                            'id'         => 'saswp_music_album_name_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => 'Description',
                            'id'         => 'saswp_music_album_description_'.$schema_id,
                            'type'       => 'textarea',                           
                    ),
                    array(
                            'label'      => 'Genre',
                            'id'         => 'saswp_music_album_genre_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => 'Image',
                            'id'         => 'saswp_music_album_image_'.$schema_id,
                            'type'       => 'media',                           
                    ),
                    array(
                            'label'      => 'Artist',
                            'id'         => 'saswp_music_album_artist_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => 'URL',
                            'id'         => 'saswp_music_album_url_'.$schema_id,
                            'type'       => 'text',                           
                    )    
                        
                   );
                    break;
                
                case 'Book':
                    
                    $meta_field = array(
                    array(
                            'label'      => 'Name',
                            'id'         => 'saswp_book_name_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => 'Description',
                            'id'         => 'saswp_book_description_'.$schema_id,
                            'type'       => 'textarea',                           
                    ),
                    array(
                            'label'      => 'URL',
                            'id'         => 'saswp_book_url_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => 'Image',
                            'id'         => 'saswp_book_image_'.$schema_id,
                            'type'       => 'media',                           
                    ),
                    array(
                            'label'      => 'Author',
                            'id'         => 'saswp_book_author_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => 'ISBN',
                            'id'         => 'saswp_book_isbn_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => 'Number Of Page',
                            'id'         => 'saswp_book_no_of_page_'.$schema_id,
                            'type'       => 'text',                           
                    ),    
                    array(
                            'label'      => 'Publisher',
                            'id'         => 'saswp_book_publisher_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => 'Published Date',
                            'id'         => 'saswp_book_date_published_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'   => 'Availability',
                            'id'      => 'saswp_book_availability_'.$schema_id,
                            'type'    => 'select',                           
                            'options' => array(
                                     'InStock'           => 'In Stock',
                                     'OutOfStock'        => 'Out Of Stock',
                                     'Discontinued'      => 'Discontinued',
                                     'PreOrder'          => 'Pre Order', 
                            ) 
                       ), 
                    array(
                            'label'      => 'Price',
                            'id'         => 'saswp_book_price_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label'      => 'Price Currency',
                            'id'         => 'saswp_book_price_currency_'.$schema_id,
                            'type'       => 'text',                           
                    ),
                    array(
                            'label' => 'Aggregate Rating',
                            'id'    => 'saswp_book_enable_rating_'.$schema_id,
                            'type'  => 'checkbox',                            
                    ),
                    array(
                            'label'   => 'Rating',
                            'id'      => 'saswp_book_rating_value_'.$schema_id,
                            'type'    => 'text',
                            
                    ),
                    array(
                            'label'   => 'Rating Count',
                            'id'      => 'saswp_book_rating_count_'.$schema_id,
                            'type'    => 'text',                            
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


