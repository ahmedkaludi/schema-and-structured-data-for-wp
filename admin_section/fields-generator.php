<?php
/*
  Metabox to show ads type such as custom and adsense 
 */
class saswp_fields_generator {
    
    public function saswp_tooltip_message($meta_field_id){
        $tooltip_message='';
        switch ($meta_field_id) {
            case 'saswp_kb_type':
               //$tooltip_message = esc_html__('ss','schema-and-structured-data-for-wp');
                break;

            default:
                break;
        }
        return $tooltip_message;
    }
    public function saswp_field_generator( $meta_fields, $settings ) {            
		$output = '';
                $tooltip_message='';
		foreach ( $meta_fields as $meta_field ) {
                    
                        $tooltip_message = $this->saswp_tooltip_message($meta_field['id']);
                        
                        $class = "";
                        $note = "";
                        $tooltip_element="";
                        $hidden = array();
                        if(array_key_exists('class', $meta_field)){
                        $class = $meta_field['class'];    
                        }
                        if(array_key_exists('note', $meta_field)){
                        $note = $meta_field['note'];     
                        }
                        if(array_key_exists('hidden', $meta_field)){
                        $hidden = $meta_field['hidden'];     
                        }
                        if($tooltip_message){
                        $tooltip_element = '<span class="saswp-tooltiptext">'.$tooltip_message.'</span>';    
                        }
			$label = '<label class="saswp-tooltip" for="' . esc_attr($meta_field['id']) . '">' . esc_html__( $meta_field['label'], 'schema-and-structured-data-for-wp' ) .$tooltip_element.'</label>';			
			
			switch ( $meta_field['type'] ) {
				case 'media':
                                        $mediavalue = $settings[$meta_field['id']];                                          
					$input = sprintf(
						'<fieldset><input class="%s" style="width: 80%%" id="%s" name="%s" type="text" value="%s"> <input data-id="media" style="width: 19%%" class="button" id="%s_button" name="%s_button" type="button" value="Upload" />'
                                                . '<input type="hidden" data-id="'.esc_attr($meta_field['id']).'_id" class="upload-id " name="sd_data['.esc_attr($meta_field['id']).'][id]" id="sd_data['.esc_attr($meta_field['id']).'][id]" value="'.esc_attr($mediavalue['id']).'">'
                                                . '<input type="hidden" data-id="'.esc_attr($meta_field['id']).'_height" class="upload-height" name="sd_data['.esc_attr($meta_field['id']).'][height]" id="sd_data['.esc_attr($meta_field['id']).'][height]" value="'.esc_attr($mediavalue['height']).'">'
                                                . '<input type="hidden" data-id="'.esc_attr($meta_field['id']).'_width" class="upload-width" name="sd_data['.esc_attr($meta_field['id']).'][width]" id="sd_data['.esc_attr($meta_field['id']).'][width]" value="'.esc_attr($mediavalue['width']).'">'
                                                . '<input type="hidden" data-id="'.esc_attr($meta_field['id']).'_thumbnail" class="upload-thumbnail" name="sd_data['.esc_attr($meta_field['id']).'][thumbnail]" id="sd_data['.esc_attr($meta_field['id']).'][thumbnail]" value="'.esc_attr($mediavalue['thumbnail']).'">'
                                                . '</fieldset>',
                                                $class,
						esc_attr($meta_field['id']),
						esc_attr($meta_field['name']),
                                                esc_url($mediavalue['url']),
						esc_attr($meta_field['id']),
						esc_attr($meta_field['id'])                                                
					);
					break;
				case 'checkbox':
                                        $hiddenvalue ="";
                                        if(array_key_exists('id', $hidden)){
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
                                         
					$input = sprintf(
						'<input class="%s" id="%s" name="%s" type="checkbox" %s>',
                                                esc_attr($class),
                                                esc_attr($meta_field['id']),    
						esc_attr($meta_field['name']),                                              
                                                $hiddenvalue == 1 ? 'checked' : ''
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
						$input .= sprintf(
							'<option %s value="%s">%s</option>',
							$settings[$meta_field['id']] == $key ? 'selected' : '',                                                        
							$key,
							esc_html__( $value, 'schema-and-structured-data-for-wp' )
						);
					}
					$input .= '</select>';
					break;                                
				default:
					
					$input = sprintf(
						'<input class="%s" %s id="%s" name="%s" type="%s" value="%s">',
                                                $class,
						$meta_field['type'] !== 'color' ? 'style="width: 100%"' : '',
						esc_attr($meta_field['id']),
						esc_attr($meta_field['name']),
						esc_attr($meta_field['type']),
						esc_attr($settings[$meta_field['id']])
					);	
									
			}
                        
                        $allowed_html = saswp_expanded_allowed_tags();
			$output .= '<li><div class="saswp-knowledge-label">'.$label.'</div><div class="saswp-knowledge-field">'.$input.'<p>'.esc_html__($note,'schema-and-structured-data-for-wp').'</p></div></li>';
		}
		echo '<div><div class="saswp-settings-list"><ul>' . wp_kses($output, $allowed_html) . '</ul></div></div>';
	}	        
}
