<?php
/**
 * Post Specific Class
 *
 * @author   Magazine3
 * @category Admin
 * @path     reviews/reviews_admin
 * @version 1.9
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class saswp_reviews_admin {
        
	private $screen = array(		
            'saswp_reviews'                                                      
	);
	private $meta_fields = array(
		array(
			'label'   => 'Reviewer Image',
			'id'      => 'saswp_reviewer_image',
			'type'    => 'media',                        			
		    ),
                array(
			'label'   => 'Reviewer Name',
			'id'      => 'saswp_reviewer_name',
			'type'    => 'text',                        			
		    ),
                array(
			'label'   => 'Rating',
			'id'      => 'saswp_review_rating',
			'type'    => 'star',                        			
		    ),
                array(
			'label'   => 'Review Date',
			'id'      => 'saswp_review_date',
			'type'    => 'text',                        			
		    ),
                array(
			'label'   => 'Review Text',
			'id'      => 'saswp_review_text',
			'type'    => 'textarea',                        			
		    ),
                array(
			'label'   => 'Review Link',
			'id'      => 'saswp_review_link',
			'type'    => 'text',                        			
		    ),    
                array(
			'label'   => 'Review Platform',
			'id'      => 'saswp_review_platform',
			'type'    => 'select',
                        
		    ),     
                                            
	);
        
	public function __construct() {
                
		add_action( 'add_meta_boxes', array( $this, 'saswp_add_meta_boxes' ),99 );
		add_action( 'save_post', array( $this, 'saswp_save_fields' ) );
                add_action( 'admin_init', array( $this, 'saswp_removing_reviews_wysiwig' ) );
                
	}
        
        /**
         * Function to disable default wordpress editor
         * @since version 1.9
         */
        public function saswp_removing_reviews_wysiwig(){
            
            remove_post_type_support( 'saswp_reviews', 'editor');
            remove_post_type_support( 'saswp-collections', 'editor');
            
        }
        
        /**
         * Function to add review_content metabox 
         * @since version 1.9
         */
	public function saswp_add_meta_boxes() {
		
		global $saswp_metaboxes;
		
		foreach ( $this->screen as $single_screen ) {
			
			add_meta_box(
				'saswp_review_content',
				esc_html__( 'Review Content', 'schema-and-structured-data-for-wp' ),
				array( $this, 'saswp_meta_box_callback' ),
				$single_screen,
				'normal',
				'high'
			);

			$saswp_metaboxes[]= 'saswp_review_content'; 

		}
                
	}
                
	public function saswp_meta_box_callback( $post ) {
                
		wp_nonce_field( 'saswp_reviews_data', 'saswp_reviews_nonce' );
		$this->saswp_field_generator( $post );
                
	}
        
        /**
         * Function to generate html elements based on passed array as a parameter
         * @param type $post
         * @since version 1.9
         */
	public function saswp_field_generator( $post ) {
            
                $this->meta_fields[6]['options'] = saswp_get_terms_as_array();
                
		$output = '';                     
		foreach ( $this->meta_fields as $meta_field ) {
                    
                    $attributes = $label = '';
                    
                    if(isset($meta_field['label'])){
                      $label =  $meta_field['label']; 
                    }
			$label = '<label for="' . esc_attr($meta_field['id']) . '">' . esc_html__( $label, 'schema-and-structured-data-for-wp' ) . '</label>';
			$meta_value = get_post_meta( $post->ID, $meta_field['id'], true );
                        
			if ( empty( $meta_value ) ) {
				$meta_value = isset($meta_field['default']); 
                                
                        }
                        
                        if(isset($meta_field['attributes'])){
                            
                            if(array_key_exists('provider_type', $meta_field['attributes'])){
                                
                               $provider_type = $meta_field['attributes']['provider_type']; 
                                
                            }
                            
                            
                        }
                        
			switch ( $meta_field['type'] ) {
                            
				case 'select':                                                                        
                                                                                               
					$input = sprintf(
						'<select class="saswp_select" id="%s" name="%s" %s>',
						esc_attr($meta_field['id']),
						esc_attr($meta_field['id']),
                                                $attributes    
					);
					foreach ( $meta_field['options'] as $key => $value ) {
						                                                
						$input .= sprintf(
							'<option %s value="%s">%s</option>',
							$meta_value == $key ? 'selected' : '',
							$key,
							esc_html__($value, 'schema-and-structured-data-for-wp')
						);
					}
                                        $input .= '</select>';
					
					break;
				case 'textarea':
					$input = sprintf(
						'<textarea class="saswp_textarea" id="%s" name="%s" rows="5">%s</textarea>',
						esc_attr($meta_field['id']),
						esc_attr($meta_field['id']),
						$meta_value
					); 
                                    break;

                                case 'checkbox':
					$input = sprintf(
						'<input %s id="%s" name="%s" type="checkbox" value="1">',
						$meta_value === '1' ? 'checked' : '',
						esc_attr($meta_field['id']),
						esc_attr($meta_field['id'])
						);
					break;
                                case 'media':
                                        $media_value = array();
                                        $media_key   = $meta_field['id'].'_detail';
                                        
                                        $media_value_meta = get_post_meta( $post->ID, $media_key, true );   
                                        
                                        if(!empty($media_value_meta)){
                                            $media_value = $media_value_meta;  
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
					
					$input = '<fieldset><input style="width: 80%" id="'. esc_attr($meta_field['id']).'" name="'. esc_attr($meta_field['id']).'" type="text" value="'.esc_url($media_thumbnail).'">'
                                                . '<input data-id="media" style="width: 19%" class="button" id="'. esc_attr($meta_field['id']).'_button" name="'. esc_attr($meta_field['id']).'_button" type="button" value="Upload" />'
                                                . '<input type="hidden" data-id="'.esc_attr($meta_field['id']).'_height" class="upload-height" name="'.esc_attr($meta_field['id']).'_height" id="'.esc_attr($meta_field['id']).'_height" value="'.esc_attr($media_height).'">'
                                                . '<input type="hidden" data-id="'.esc_attr($meta_field['id']).'_width" class="upload-width" name="'.esc_attr($meta_field['id']).'_width" id="'.esc_attr($meta_field['id']).'_width" value="'.esc_attr($media_width).'">'
                                                . '<input type="hidden" data-id="'.esc_attr($meta_field['id']).'_thumbnail" class="upload-thumbnail" name="'.esc_attr($meta_field['id']).'_thumbnail" id="'.esc_attr($meta_field['id']).'_thumbnail" value="'.esc_url($media_thumbnail).'">'                                                
                                                . '<div class="saswp_image_div_'.esc_attr($meta_field['id']).'">'                                               
                                                . $image_pre                                                 
                                                . '</div>'
                                                .'</fieldset>';
					
					break;   
                                case 'star':
                                                                              
                                     $input = sprintf(
						'<div class="saswp-rating-div"></div>'
                                              . '<input id="%s" name="%s" type="hidden" value="%s">',                                                						
						esc_attr($meta_field['id']),
                                                esc_attr($meta_field['id']),
						esc_attr($meta_value),
                                                $attributes
                                             );
                                    
                                    break;
				default:
                                             $class = '';
                                             if (strpos($meta_field['id'], 'saswp_review_date') !== false ){
                                             
                                                $class='saswp-reviews-datepicker-picker';    
                                                
                                             }
                                                                              
                                     $input = sprintf(
						'<input class="%s" %s id="%s" name="%s" type="%s" value="%s" %s>',
                                                $class,
						$meta_field['type'] !== 'color' ? '' : '',
						esc_attr($meta_field['id']),
						esc_attr($meta_field['id']),
						esc_attr($meta_field['type']),
						esc_attr($meta_value),
                                                $attributes
                                             );
                                        
					
			}
                        
			$output .= '<tr><th>'.$label.'</th><td>'.$input.'</td></tr>';
		}
                                
                $allowed_html = saswp_expanded_allowed_tags();                                                		                                
		echo '<table class="form-table saswp-review-content-table"><tbody>' . wp_kses($output, $allowed_html) . '</tbody></table>';
	}
	
        /**
         * Function to save current metabox elements value into database
         * @param type $post_id
         * @return type
         * @since version 1.9
         */
	public function saswp_save_fields( $post_id ) { 
            
		if ( ! isset( $_POST['saswp_reviews_nonce'] ) )
			return $post_id;		
		if ( !wp_verify_nonce( $_POST['saswp_reviews_nonce'], 'saswp_reviews_data' ) )
			return $post_id;
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;                    
                if ( !current_user_can( saswp_current_user_can() ) ) 
                        return $post_id;
                
			$post_meta = array();                    
			$post_meta = (array) $_POST; // Sanitized below before saving
                        
                        $this->meta_fields[6]['options'] = saswp_get_terms_as_array();
                        
			foreach ( $this->meta_fields as $meta_field ) {

				if ( isset( $post_meta[ $meta_field['id'] ] ) ) {
					switch ( $meta_field['type'] ) {						
						case 'text':
							$post_meta[ $meta_field['id'] ] = sanitize_text_field( $post_meta[ $meta_field['id'] ] );
							break;
                                                case 'textarea':
							$post_meta[ $meta_field['id'] ] = esc_html( $post_meta[ $meta_field['id'] ] );
							break; 
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
						default:     
							$post_meta[ $meta_field['id'] ] = sanitize_text_field( $post_meta[ $meta_field['id'] ] );
					}
                                        update_post_meta( $post_id, $meta_field['id'], $post_meta[ $meta_field['id'] ] );
					
				} else if ( $meta_field['type'] === 'checkbox' ) {
					update_post_meta( $post_id, $meta_field['id'], '0' );
				}
			}
       	
	}
}
if (class_exists('saswp_reviews_admin')) {
	new saswp_reviews_admin;
};