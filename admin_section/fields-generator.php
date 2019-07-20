<?php
/**
 * Field Generator class
 *
 * @author   Magazine3
 * @category Admin
 * @path     google_review/google_review
 * @Version 1.8
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/*
  Metabox to show ads type such as custom and adsense 
 */
class saswp_fields_generator {
    
    public function saswp_tooltip_message($meta_field_id){
        
        $tooltip_message = '';
        
        switch ($meta_field_id) {
            
            case 'saswp_kb_type':
               //$tooltip_message = 'Test Message';
                break;

            default:
                break;
        }
        
        return $tooltip_message;
    }
    /**
     * Function to generate html element from the given elements array
     * @param type $meta_fields
     * @param type $settings
     * @param type $field_type
     * @since version 1.0
     */
    public function saswp_field_generator( $meta_fields, $settings, $field_type = null ) {  
                        
		$output          = '';
                $tooltip_message = '';
                
                
		foreach ( $meta_fields as $meta_field ) {
                    
                        $tooltip_message = $this->saswp_tooltip_message($meta_field['id']);
                        
                        $class      = "";
                        $note       = "";  
                        $proversion = false;
                        $hidden     = array();
                        $attribute  = array();
                        
                            $on                 = 'Google';
                            $license_key        = '';
                            $license_status     = 'inactive';
                            $license_status_msg = '';
                            $rv_limits          = '';
                            
                            if(isset($settings[strtolower($on).'_addon_license_key'])){
                            $license_key =   $settings[strtolower($on).'_addon_license_key'];
                            }

                            if(isset($settings[strtolower($on).'_addon_license_key_status'])){
                              $license_status =   $settings[strtolower($on).'_addon_license_key_status'];
                            }

                            if(isset($settings[strtolower($on).'_addon_license_key_message'])){
                              $license_status_msg =   $settings[strtolower($on).'_addon_license_key_message'];
                            }
                            
                            if($license_status =='active'){
                              $rv_limits =   get_option(strtolower($on).'_addon_reviews_limits');
                            }
                        
                        
                        
                        if(array_key_exists('class', $meta_field)){
                            
                            $class = $meta_field['class'];    
                            
                        }
                        if(array_key_exists('proversion', $meta_field)){
                            
                            $proversion = $meta_field['proversion'];  
                            
                        
                        }
                        if(array_key_exists('note', $meta_field)){
                            
                            $note = $meta_field['note'];     
                        
                        }
                        if(array_key_exists('hidden', $meta_field)){
                            
                            $hidden = $meta_field['hidden'];     
                        
                        }
                        if(array_key_exists('attributes', $meta_field)){
                            
                            $attribute = $meta_field['attributes'];     
                        
                        }
                        if($tooltip_message){
                            
                            $label = '<label class="saswp-tooltip" for="' . esc_attr($meta_field['id']) . '">' . esc_html__( $meta_field['label'], 'schema-and-structured-data-for-wp' ).' <span class="saswp-tooltiptext">'.esc_html__($tooltip_message, 'schema-and-structured-data-for-wp').'</span></label>';			
                        
                        }else{
                            
                            $label = '<label class="saswp-tooltip" for="' . esc_attr($meta_field['id']) . '">' . esc_html__( $meta_field['label'], 'schema-and-structured-data-for-wp' ).' <span class="saswp-tooltiptext"></span></label>';			    
                        
                        }
			
                        $attribute_str ='';
                        
                        if(!empty($attribute)){
                            
                            foreach ($attribute as $key => $attr ){

                                $attribute_str .=''.esc_attr($key).'="'.esc_attr($attr).'"';
                           
                            }
                        
                        }            
                        			                        
			switch ( $meta_field['type'] ) {
                            
				case 'media':
                                                                                                     
                                        $mediavalue = array();
                                    
                                            if(isset($settings[$meta_field['id']])){
                                                
                                                $mediavalue = $settings[$meta_field['id']];                                          
                                                
                                            }
                                        
                                        $image_pre = '';
                                        if(saswp_remove_warnings($mediavalue, 'thumbnail', 'saswp_string')){
                                            
                                           $image_pre = '<div class="saswp_image_thumbnail">
                                                         <img class="saswp_image_prev" src="'.esc_attr(saswp_remove_warnings($mediavalue, 'thumbnail', 'saswp_string')).'" />
                                                         <a data-id="'.esc_attr($meta_field['id']).'" href="#" class="saswp_prev_close">X</a>
                                                        </div>'; 
                                            
                                        }    
                                            
					$input = sprintf(
						'<fieldset><input %s class="%s" style="width: 80%%" id="%s" name="%s" type="text" value="%s">'
                                                . '<input data-id="media" style="width: 19%%" class="button" id="%s_button" name="%s_button" type="button" value="Upload" />'
                                                . '<input type="hidden" data-id="'.esc_attr($meta_field['id']).'_id" class="upload-id " name="sd_data['.esc_attr($meta_field['id']).'][id]" id="sd_data['.esc_attr($meta_field['id']).'][id]" value="'.esc_attr(saswp_remove_warnings($mediavalue, 'id', 'saswp_string')).'">'
                                                . '<input type="hidden" data-id="'.esc_attr($meta_field['id']).'_height" class="upload-height" name="sd_data['.esc_attr($meta_field['id']).'][height]" id="sd_data['.esc_attr($meta_field['id']).'][height]" value="'.esc_attr(saswp_remove_warnings($mediavalue, 'height', 'saswp_string')).'">'
                                                . '<input type="hidden" data-id="'.esc_attr($meta_field['id']).'_width" class="upload-width" name="sd_data['.esc_attr($meta_field['id']).'][width]" id="sd_data['.esc_attr($meta_field['id']).'][width]" value="'.esc_attr(saswp_remove_warnings($mediavalue, 'width', 'saswp_string')).'">'
                                                . '<input type="hidden" data-id="'.esc_attr($meta_field['id']).'_thumbnail" class="upload-thumbnail" name="sd_data['.esc_attr($meta_field['id']).'][thumbnail]" id="sd_data['.esc_attr($meta_field['id']).'][thumbnail]" value="'.esc_attr(saswp_remove_warnings($mediavalue, 'thumbnail', 'saswp_string')).'">'
                                                . '<div class="saswp_image_div_'.esc_attr($meta_field['id']).'">'                                               
                                                . $image_pre                                                 
                                                . '</div>'	
                                                . '</fieldset>',
                                                $attribute_str,
                                                $class,
						esc_attr($meta_field['id']),
						esc_attr($meta_field['name']),
                                                esc_url(saswp_remove_warnings($mediavalue, 'url', 'saswp_string')),
						esc_attr($meta_field['id']),
						esc_attr($meta_field['id'])                                                
					);
					break;
				case 'checkbox':
                                    
                                        $hiddenvalue ="";
                                        
                                        if(array_key_exists('id', $hidden) && isset($settings[$hidden['id']])){
                                            
                                         $hiddenvalue = $settings[$hidden['id']];  
                                         
                                        }
                                        $hiddenfield="";
                                        
                                        if(!empty($hidden)){   
                                            
                                          $hiddenfield = sprintf(
						'<input id="%s" name="%s" type="hidden" value="%s">',                                                						
						esc_attr($hidden['id']),
						esc_attr($hidden['name']),					
						esc_attr($hiddenvalue)
					 );  
                                          
                                         }   
                                        $message =''; 
                                                                                                                                                                                                         
					$input = sprintf(
						'<input class="%s" id="%s" name="%s" type="checkbox" %s %s><p>'.$message.'</p>',
                                                esc_attr($class),
                                                esc_attr($meta_field['id']),    
						esc_attr($meta_field['name']),                                              
                                                $hiddenvalue == 1 ? 'checked' : '',
                                                $attribute_str
						);                                          
                                         $input .=$hiddenfield;
					break;
                                    
				case 'select':
					$input = sprintf(
						'<select class="%s" id="%s" name="%s">',
                                                $class,
						esc_attr($meta_field['id']),
						esc_attr($meta_field['name'])
					);                                    
					foreach ( $meta_field['options'] as $key => $value ) {	  
                                                $settings_meta_field = '';
                                                if(isset($settings[$meta_field['id']])){
                                                 $settings_meta_field   = $settings[$meta_field['id']];
                                                }
                                            
						$input .= sprintf(
							'<option %s value="%s">%s</option>',
							$settings_meta_field == $key ? 'selected' : '',                                                        
							$key,
							esc_html__( $value, 'schema-and-structured-data-for-wp' )
						);
					}
					$input .= '</select>';
					break;                                
				default:
					
                                    
                                    
                                    switch ($meta_field['id']) {
                                    
                                        case 'saswp-reviews-pro-api':

                                            $pro_api    = '<div class="" style="display:block;">
                                                          '.saswp_get_license_section_html($on, $license_key, $license_status, $license_status_msg, $lable=false, $rv_limits).'
                                                          </div>';
                                                          
                                           
                                            $input = $pro_api;        

                                            break;
                                        
                                        case 'saswp-google-place-section':

                                            $location = '';
                            
                            if(isset($settings['saswp_reviews_location_name']) && !empty($settings['saswp_reviews_location_name'])){
                                $rv_loc = $settings['saswp_reviews_location_name'];
                                $rv_blocks = $settings['saswp_reviews_location_blocks'];
                                $i=0;
                                foreach($rv_loc as $rvl){
                                    if($rvl){
                                        $location .= '<tr>'
                                        . '<td style="width:12%;"><strong>Place Id</strong></td>'
                                        . '<td style="width:20%;"><input class="saswp-g-location-field" name="sd_data[saswp_reviews_location_name][]" type="text" value="'. esc_attr($rvl).'"></td>'
                                        . '<td style="width:10%;"><strong>Blocks</strong></td>'
                                        . '<td style="width:10%;"><input class="saswp-g-blocks-field" name="sd_data[saswp_reviews_location_blocks][]" type="number" placeholder="10" value="'. esc_attr($rv_blocks[$i]).'"></td>'                                        
                                        . '<td style="width:10%;"><a class="button button-default saswp-fetch-g-reviews">Fetch</a></td>'
                                        . '<td style="width:10%;"><a type="button" class="saswp-remove-review-item button">x</a></td>'
                                        . '<td style="width:10%;"><p class="saswp-rv-fetched-msg"></p></td>'        
                                        . '</tr>'; 
                                    }
                                   $i++;
                                }
                            }
                            
                            $reviews = '<div class="saswp-g-reviews-settings saswp-knowledge-label">'                                                                
                                . '<table class="saswp-g-reviews-settings-table" style="width:100%">'
                                . $location                                 
                                . '</table>'                                
                                . '<div><a class="button button-default saswp-add-g-location-btn">Add Location</a></div>'    
                                . '</div>';  
                                                          
                                           
                                            $input = $reviews;        

                                            break;

                                        default:
                                            
                                             $stng_meta_field = '';
                                    
                                        if(isset($settings[$meta_field['id']])){
                                            
                                         $stng_meta_field =  $settings[$meta_field['id']];  
                                         
                                        }
                                    
					$input = sprintf(
						'<input class="%s" %s id="%s" name="%s" type="%s" value="%s" %s>',
                                                $class,
						$meta_field['type'] !== 'color' ? 'style="width: 100%"' : '',
						esc_attr(saswp_remove_warnings($meta_field, 'id', 'saswp_string')),
						esc_attr(saswp_remove_warnings($meta_field, 'name', 'saswp_string')),
						esc_attr(saswp_remove_warnings($meta_field, 'type', 'saswp_string')),
						esc_attr($stng_meta_field),
                                                $attribute_str
					);
                                            
                                            break;
                                    }
                                                                          
                                                                            	
									
			}
                        $reviews =  $pro_api = $toggle_button = '';
                        
                        if($meta_field['id'] == 'saswp_google_place_api_key'){
                                                                                                                                                                                                                                                                                                                                                                     
                        }
                        
                        $allowed_html = saswp_expanded_allowed_tags();
                        
                        $output .= '<li>'                                                                
                                .  '<div class="saswp-knowledge-label">'.$label.'</div>'
                                .  '<div class="saswp-knowledge-field">'.$input.'<p class="">'.$note.'</p></div>'
                                                               
                                .  '</li>';
                                
                                                
		}
                if($field_type == 'general'){
                                        
                    $reg_menus  = get_registered_nav_menus();
                    $locations  = get_nav_menu_locations();
                    
                    if($reg_menus){
                        
                        foreach ($reg_menus as $type => $title){
                                                                                             
                            if(array_key_exists($type, $locations) && $locations[$type]){
                            
                            $checked = '';
                            
                            if(isset($settings['saswp-'.$type])){
                                $checked = 'checked';
                            }
                            
                            $output .= '<li class="saswp-nav-menu-list"><div class="saswp-knowledge-label"><label>'.esc_attr($title).'</label></div>'
                                    . '<div class="saswp-knowledge-field">'
                                    . '<input type="checkbox" name="sd_data[saswp-'.$type.']" class="regular-text" value="1" '.$checked.'>'
                                    . '</div>'
                                    . '</li>';
                                
                            }
                            
                        }    
            
                    }
                                       			 
                }
                
		echo '<div><div class="saswp-settings-list"><ul>' . wp_kses($output, $allowed_html) . '</ul></div></div>';
	}	        
}
