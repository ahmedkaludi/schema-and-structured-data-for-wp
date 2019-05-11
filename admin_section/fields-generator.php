<?php
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
    public function saswp_field_generator( $meta_fields, $settings ) {            
        
		$output          = '';
                $tooltip_message = '';
                
                
		foreach ( $meta_fields as $meta_field ) {
                    
                        $tooltip_message = $this->saswp_tooltip_message($meta_field['id']);
                        
                        $class      = "";
                        $note       = "";  
                        $proversion = false;
                        $hidden     = array();
                        $attribute  = array();
                        
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
			
			
			switch ( $meta_field['type'] ) {
                            
				case 'media':
                                    
                                        $attribute_str ='';
                                    
                                        foreach ($attribute as $key => $attr ){
                                            
                                           $attribute_str .=''.$key.'="'.$attr.'"';
                                           
                                        }
                                    
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
                                    
                                        $attribute_str ='';
                                    
                                        foreach ($attribute as $key => $attr ){
                                            
                                           $attribute_str .=''.$key.'="'.$attr.'"';
                                           
                                        }
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
					
                                        $stng_meta_field = '';
                                    
                                        if(isset($settings[$meta_field['id']])){
                                            
                                         $stng_meta_field =  $settings[$meta_field['id']];  
                                         
                                        }
                                    
					$input = sprintf(
						'<input class="%s" %s id="%s" name="%s" type="%s" value="%s">',
                                                $class,
						$meta_field['type'] !== 'color' ? 'style="width: 100%"' : '',
						esc_attr(saswp_remove_warnings($meta_field, 'id', 'saswp_string')),
						esc_attr(saswp_remove_warnings($meta_field, 'name', 'saswp_string')),
						esc_attr(saswp_remove_warnings($meta_field, 'type', 'saswp_string')),
						esc_attr($stng_meta_field)
					);	
									
			}
                        
                        $allowed_html = saswp_expanded_allowed_tags();
                        
                        $output .= '<li><div class="saswp-knowledge-label">'.$label.'</div><div class="saswp-knowledge-field">'.$input.'<p class="">'.$note.'</p></div></li>';			
//                        if($note =='' || $proversion == 1){                            
//                            $output .= '<li><div class="saswp-knowledge-label">'.$label.'</div><div class="saswp-knowledge-field">'.$input.'<p data-id="'.esc_attr($proversion).'">'.$note.'</p></div></li>';			
//                        }else{
//                            $output .= '<li><div class="saswp-knowledge-label">'.$label.'</div><div class="saswp-knowledge-field">'.$input.'<p class="">'.$note.'</p></div></li>';			
//                        }
                                                
		}
		echo '<div><div class="saswp-settings-list"><ul>' . wp_kses($output, $allowed_html) . '</ul></div></div>';
	}	        
}
