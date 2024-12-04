<?php
/**
 * Common Class
 *
 * @author   Magazine3
 * @category Admin
 * @path     view/common
 * @Version 1.9.17
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class SASWP_View_Common {
    
    public    $_meta_name                = array();
    public    $schema_type_element       = array();
    public    $itemlist_meta             = array();
    public    $item_list_item = array(
                             'Article'               => 'Article',   
                             'ScholarlyArticle'      => 'ScholarlyArticle',                                                              
                             'Course'                => 'Course',                                                                                                                                                                                                            
                             'Movie'                 => 'Movie',                                   
                             'Product'               => 'Product',                                
                             'Recipe'                => 'Recipe',                                                                                      
                        );
    
    public function __construct() {
        
                $mapping_repeater = SASWP_DIR_NAME . '/core/array-list/repeater-fields.php';
                require_once SASWP_DIR_NAME.'/core/array-list/schema-properties.php';
                
                if ( file_exists( $mapping_repeater ) ) {
                    
                    $repeater_fields =  include $mapping_repeater;
                    
                    $this->schema_type_element = $repeater_fields['schema_type_element'];
                    $this->_meta_name          = $repeater_fields['meta_name'];
                    
                    foreach( $this->item_list_item as $item){
                        $this->itemlist_meta[$item]  = saswp_get_fields_by_schema_type(null, null, $item, 'manual');                        
                    }
                    $this->_meta_name['itemlist_item'] = $this->itemlist_meta;
		}                
                
        }
    
    public function saswp_get_dynamic_html( $schema_id, $meta_name, $index, $data ) {
                
                $meta_fields = array();
                $response    = '';
                $output      = '';    
        
                $item_type = get_post_meta($schema_id, 'saswp_itemlist_item_type', true); 
                
                if($meta_name == 'itemlist_item'){
                    
                    $itemval = $this->_meta_name[$meta_name][$item_type];
                    if($itemval){
                         
                         foreach( $itemval as $key => $val){
                             $itemval[$key]['name'] = $val['id'];
                             unset($itemval[$key]['id']);
                         }
                         
                     }
                    
                    $meta_fields = $itemval;  
                }else{
                    $meta_fields = $this->_meta_name[$meta_name];               
                }    
                
                
                 if($meta_fields){
                    
                     foreach ( $meta_fields as $meta_field ) {
                    
                    
			$label = '<label for="' . $meta_field['name'] . '">' . esc_html( $meta_field['label'] ) . '</label>';			
			                                                                        
			switch ( $meta_field['type'] ) {
                                                            								                                
                                case 'media':
                                                $name = $meta_field['name'].'_'.$index.'_'.$schema_id;
                                    
                                                $img_prev = '';
                                                $src      = '';
                                                
                                                if( isset($data[$meta_field['name'].'_id']) && wp_get_attachment_url( $data[$meta_field['name'].'_id'] ) ){
                                                 
                                                $src = wp_get_attachment_url(esc_attr( $data[$meta_field['name'].'_id']));
                                                    
                                                $img_prev = '<div class="saswp_image_thumbnail">'
                                                           . '<img class="saswp_image_prev" src="'. esc_url( $src).'">'
                                                           . '<a data-id="'. esc_attr( $name).'" href="#" class="saswp_prev_close">X</a>'
                                                           . '</div>';     

                                                }
                                        
                                                //$img_prev is already escapped
                                                $img_val = '';

                                                if( isset($data[$meta_field['name'].'_id']) ){
                                                    $img_val = $data[$meta_field['name'].'_id'];
                                                }
                                                
                                                $input = '<fieldset>
                                                        <input style="width:79%" type="text" id="'. esc_attr( $name).'" name="'. esc_attr( $name).'" value="'. esc_url( $src).'">
                                                        <input type="hidden" data-id="'. esc_attr( $name).'_id" name="'. esc_attr( $meta_name).'_'. esc_attr( $schema_id).'['. esc_attr( $index).']['. esc_attr( $meta_field['name']).'_id]'.'" id="'. esc_attr( $name).'_id" value="'. esc_attr( $img_val).'">
                                                        <input data-id="media" style="width: 19%" class="button" id="'. esc_attr( $name).'_button" name="'. esc_attr( $name).'_button" type="button" value="Upload">
                                                        <div class="saswp_image_div_'. esc_attr( $name).'">'.$img_prev.'</div>
                                                        </fieldset>';
                                                
                                            
                                                                                                                        
                                                break;
                                                
                                case 'textarea':
                                    $textarea_val = '';    
                                    if( isset($data[$meta_field['name']]) ){
                                        $textarea_val = $data[$meta_field['name']];
                                    }

                                $input = sprintf(
                                    '<textarea style="width: 100%%" id="%s" name="%s" rows="5">%s</textarea>',                                                
                                    esc_attr( $meta_field['name']).'_'. esc_attr( $index).'_'. esc_attr( $schema_id),
                                    esc_attr( $meta_name).'_'. esc_attr( $schema_id).'['. esc_attr( $index).']['. esc_attr( $meta_field['name']).']',
                                    esc_textarea($textarea_val)
                                );
                                                    
                                break;                
                                
                                case 'select':                                        
                                                                                     
					$input = sprintf(
						'<select id="%s" name="%s">',                                                
						esc_attr( $meta_field['name']).'_'. esc_attr( $index).'_'. esc_attr( $schema_id),
						esc_attr( $meta_name).'_'. esc_attr( $schema_id).'['. esc_attr( $index).']['. esc_attr( $meta_field['name']).']'
					);
					foreach ( $meta_field['options'] as $key => $value ) {
                                            
						$meta_field_value = !is_numeric( $key ) ? $key : $value;
						$input .= sprintf(
							'<option %s value="%s">%s</option>',
							$data[$meta_field['name']] === $meta_field_value ? 'selected' : '',
							$meta_field_value,
							esc_html( $value )
						);
					}
					$input .= '</select>';
					break;  
                                        
                                case 'checkbox':
                                    $check_val = '';
                                    if ( isset( $data[$meta_field['name']]) ) {
                                        $check_val = $data[$meta_field['name']];
                                    }
                                                                        
					$input = sprintf(
						'<input id="%s" name="%s" type="checkbox" value="1" %s>', 
                                                esc_attr( $meta_field['name']).'_'. esc_attr( $index).'_'. esc_attr( $schema_id),
                                                esc_attr( $meta_name).'_'. esc_attr( $schema_id).'['. esc_attr( $index).']['. esc_attr( $meta_field['name']).']',
                                                $check_val === '1' ? 'checked' : ''												
						);
					break;           
                                         
				default:
                                                    
                                    $class = '';
                                    
                                    if (saswp_is_date_field($meta_field['name'].'_'.$index.'_'.$schema_id)) {
                                                $class='saswp-datepicker-picker';    
                                    }
                                    if (saswp_is_time_field($meta_field['name'])) {
                                                $class='saswp-timepicker';    
                                    }
                                    $data_value = isset($data[$meta_field['name']]) ? $data[$meta_field['name']] : '';
                                     $input = sprintf(
						'<input class="%s"  style="width:100%%" id="%s" name="%s" type="%s" value="%s">',
                                                $class,
						esc_attr( $meta_field['name']).'_'. esc_attr( $index).'_'. esc_attr( $schema_id),
						esc_attr( $meta_name).'_'. esc_attr( $schema_id).'['. esc_attr( $index).']['. esc_attr( $meta_field['name']).']',
						esc_attr( $meta_field['type']),
						esc_attr( $data_value)                                            
                                             );
                                        
					
			}
                        //$lable and $input has been escapped while create this variable
			$output .= '<tr><th>'.$label.'</th><td>'.$input.'</td>';
            if($meta_name == 'product_pros' || $meta_name == 'product_cons'){
                $output .= '<td class="saswp-table-close-new-td"><a class="saswp-table-close-new">X</a></td>';

            }
            $output .= '</tr>';
		}
                
                    //$output has been escapped while create this variable                                               		                                
                     $response = '<table class="form-table">'.$output.'</table>';   
                     
                 }   
                              
                 return $response;
                 
        }
        
    public function saswp_schema_fields_html_on_the_fly( $schema_type, $schema_id, $post_id, $disabled_schema=null, $modify_this=null, $modified = null ) {
            
                    $howto_data        = array();                    
                    $tabs_fields       = '';
                    $itemlist_sub_type = '';
                    
                    $schema_type_fields = $this->schema_type_element;
                    
                    if($schema_type !='' ) {
                        
                        $type_fields = array_key_exists($schema_type, $schema_type_fields) ? $schema_type_fields[$schema_type]:'';  
                        
                    if($type_fields){
                       
                    if($schema_type == 'ItemList'){
                         $itemlist_sub_type     = get_post_meta($schema_id, 'saswp_itemlist_item_type', true); 
                         $tabs_fields .= '<div schema-id="'. esc_attr( $schema_id).'" class="saswp-table-create-onajax saswp-ps-toggle">';   
                        
                    }else{
                    
                        if(empty($disabled_schema) ) {
                        
                        if( $modified || $modify_this == 1){
                             $tabs_fields .= '<div schema-id="'. esc_attr( $schema_id).'" class="saswp-table-create-onajax saswp-ps-toggle">';   
                        }else{
                             $tabs_fields .= '<div schema-id="'. esc_attr( $schema_id).'" class="saswp-table-create-onajax saswp-ps-toggle saswp_hide">';
                        }

                        }else{                         
                            $tabs_fields .= '<div schema-id="'. esc_attr( $schema_id).'" class="saswp-table-create-onajax saswp-ps-toggle saswp_hide">';                     
                        }
                                                
                    } 
                        
                     foreach( $type_fields as $key => $value){
                            
                           
                            $howto_data[$value.'_'.$schema_id]  = saswp_get_post_meta($post_id, $value.'_'.$schema_id, true);  
                            $prosCheckBoxMeta = saswp_get_post_meta($post_id,"saswp_schema_type_product_pros_enable_pros", true);                            
                                                          
                            $enablePros ='';
                            if ( isset( $prosCheckBoxMeta) ) {
                                $enablePros = 'checked';
                            }
                            $prosCheckboxFalse = false;
                            if($value == 'product_pros'){
                                $prosCheckboxFalse = true;
                                $tabs_fields .= '
                                <table class="form-table" style="margin: 23px 0 4px 0;">
                                    <tr>
                                        <th>
                                        <label for="saswp_schema_type_product_pros_enable_pros"><b>'.esc_html__( 'Pros & Cons', 'schema-and-structured-data-for-wp' ).'</b></label>
                                        </th> 
                                        <td><input type="checkbox" id="saswp_schema_type_product_pros_enable_pros" name="saswp_schema_type_product_pros_enable_pros" value="1" '. esc_attr( $enablePros).'>
                                        </td>
                                    </tr>
                                </table>';       
                            }
                            $mainStyle='';
                            $prosCheckboxFalseClass = '';
                            if($prosCheckboxFalse){
                                $tabs_fields .='<div class="thepros_main_section_outer">';
                                $prosCheckboxFalseClass ="thepros_main_section";
                            }
                            $tabs_fields .= '<div class="saswp-'. esc_attr( $key).'-section-main '.$prosCheckboxFalseClass.'" >'; 
                            $hideoldCloseBtn='';
                            if($value == 'product_pros'){
                                $tabs_fields .="<h3>Pros</h3>";
                                $hideoldCloseBtn = 'hideoldclosebtn';
                            }elseif($value == 'product_cons'){
                                $tabs_fields .="<h3>Cons</h3>";
                                $hideoldCloseBtn = 'hideoldclosebtn';
                            }             
                            $tabs_fields .= '<div class="saswp-'. esc_attr( $key).'-section" data-id="'. esc_attr( $schema_id).'">';                         
                            if ( isset( $howto_data[$value.'_'.$schema_id]) ) {

                                $howto_supply = $howto_data[$value.'_'.$schema_id];                                                     
                                $supply_html  = '';

                                if ( ! empty( $howto_supply) ) {
                                    
                                       $i = 0;
                                       $reviewNumber = 1;
                                       foreach ( $howto_supply as $supply){
                                           $supply_html .= '<div class="saswp-'.$key.'-table-div saswp-dynamic-properties" data-id="'.$i.'">';
                                            if($key == 'product_reviews'){
                                                $supply_html .= "<h3 style='float: left;'>Review ".$reviewNumber."</h3>";
                                            }
                                           $supply_html .= '<a class="saswp-table-close '.$hideoldCloseBtn.'">X</a>';
                                           $supply_html .= $this->saswp_get_dynamic_html($schema_id, $value, $i, $supply);
                                           $supply_html .= '</div>';

                                        $i++;  
                                        $reviewNumber++; 
                                       }

                                }

                                $tabs_fields .= $supply_html;

                            }                         
                            $tabs_fields .= '</div>';
                            
                            $btn_text = '';
                            
                            if($value){
                                
                                $btn_array = explode('_',$value);
                            
                                if($btn_array){
                                    foreach ( $btn_array as $btn){
                                        $btn_text .= ucfirst($btn).' ';
                                    }
                                }
                                
                            }
                                                        
                            if($value == 'product_pros'){
                                $tabs_fields .= '<a itemlist_sub_type="'. esc_attr( $itemlist_sub_type).'" data-id="'. esc_attr( $schema_id).'" div_type="'.$key.'" fields_type="'.$value.'" class="button saswp_add_schema_fields_on_fly saswp-'.$key.'">'.esc_html__( 'New Pros', 'schema-and-structured-data-for-wp' ).'</a>';   
                            }elseif($value == 'product_cons'){
                                $tabs_fields .= '<a itemlist_sub_type="'. esc_attr( $itemlist_sub_type).'" data-id="'. esc_attr( $schema_id).'" div_type="'.$key.'" fields_type="'.$value.'" class="button saswp_add_schema_fields_on_fly saswp-'.$key.'">'.esc_html__( 'New Cons', 'schema-and-structured-data-for-wp' ).'</a>';   
                            }else{
                                $tabs_fields .= '<a itemlist_sub_type="'. esc_attr( $itemlist_sub_type).'" data-id="'. esc_attr( $schema_id).'" div_type="'.$key.'" fields_type="'.$value.'" class="button saswp_add_schema_fields_on_fly saswp-'.$key.'">'
                                /* translators: %s: button label */
                                .esc_html( sprintf(__('Add %s', 'schema-and-structured-data-for-wp' ),$btn_text)).
                                '</a>';   
                            }                                                                                                  
                            $tabs_fields .= '</div>';                                                                                                
                            if($prosCheckboxFalse){
                                $tabs_fields .='</div>';
                            }                                                                                                
                         
                        }
                        
                        $tabs_fields .= '</div>';
                            
                        }
                        
                    }
                                                                                                                                                                                                                                   
                    return $tabs_fields;
            
        }
        
    public function saswp_post_specific_schema($schema_type, $saswp_meta_fields, $post_id, $schema_id=null, $item_reviewed = null, $disabled_schema=null, $modify_this=null, $modified= null, $is_post_specific='no') { 
                                
                global $sd_data;                        

                $author_details = array();

                if( function_exists('wp_get_current_user') ) {

                    $current_user   = wp_get_current_user();
                                
                    if ( function_exists( 'get_avatar_data') &&  ! empty( get_option( 'show_avatars' ) ) ) {
                        $author_details	= get_avatar_data($current_user->ID);                
                    }

                }

		$output = '';                
                                
        $post_type          =   '';
        if ( $schema_id > 0 ) {
            $post_type      =   get_post_type( $schema_id );
        }

		foreach ( $saswp_meta_fields as $meta_field ) {
        
            if ( ( $post_type == 'saswp_template' && isset( $meta_field['is_template_attr'] ) ) || ( $is_post_specific == 'yes'  && isset( $meta_field['is_template_attr'] ) ) ) {
                continue;
            }

            if( isset( $meta_field['type'] ) ) {
                    
                        $input      = '';
                        $attributes = '';
                        $label      = '';
                        $meta_value = array();
                    if($meta_field['type'] != 'global_mapping'){
                        if($meta_field['type'] != 'repeater'){
                            $label      = '<label for="' . esc_attr( $meta_field['id']) . '">' . esc_html( $meta_field['label'] ). '</label>';
                            $meta_value = saswp_get_post_meta( $post_id, $meta_field['id'], true );
                        }
                    }                                
                    if ( empty( $meta_value ) && isset($meta_field['default'])) {
                                    
                        $meta_value = $meta_field['default'];                                 
                        
                    }
                                
                    if ( isset( $meta_field['attributes']) ) {
                        
                        foreach ( $meta_field['attributes'] as $key => $attr ){
                            
                                    $attributes .=''. esc_attr( $key).'="'. esc_attr( $attr).'"';
                            }
                            
                    }                        
                        
			switch ( $meta_field['type'] ) {
                            
				case 'media':
                                    
                                        $f_image_id 	       = get_post_thumbnail_id();
                                        $f_image_details       = wp_get_attachment_image_src($f_image_id, 'full'); 
                                        
                                        $media_value = array();
                                        $media_key = $meta_field['id'].'_detail';
                                        
                                        $media_value_meta = saswp_get_post_meta( $post_id, $media_key, true ); 
                                        
                                        if ( ! empty( $media_value_meta) ) {
                                            $media_value = $media_value_meta;  
                                        }  

                                        if (strpos($meta_field['id'], 'image') !== false && empty($media_value_meta)) {
                                                                                            
                                                if ( ! empty( $f_image_details) ) {
                                                    $media_value['thumbnail'] = $f_image_details[0];
                                                    $media_value['width']     = $f_image_details[1];
                                                    $media_value['height']    = $f_image_details[2];
                                                                                                        
                                                }
                                                                                                                                        
                                        }
                                        
                                        if (strpos($meta_field['id'], 'author_image') !== false && empty($media_value_meta)) {                                          
                                                $media_value['height']    = $author_details['height'];                                                                                         
                                                $media_value['width']     = $author_details['width'];                                                                                         
                                                $media_value['thumbnail'] = $author_details['url'];                                             
                                        }
                                                  
                                        if (strpos($meta_field['id'], 'organization_logo') !== false && empty($media_value_meta)) {
                                                                                            
                                                if ( isset( $sd_data['sd_logo']) ) {
                                                    $media_value['height']    = $sd_data['sd_logo']['height'];                                                                                         
                                                    $media_value['width']     = $sd_data['sd_logo']['width'];                                                                                         
                                                    $media_value['thumbnail'] = $sd_data['sd_logo']['url']; 
                                                }
                                                                                                                                        
                                        }
                                             
                                        $media_height    = '';
                                        $media_width     = '';
                                        $media_thumbnail = '';
                                        
                                        if (strpos($meta_field['id'], 'faq_author_image') !== false) {
                                            $media_meta_data = get_post_meta( $post_id, $meta_field['id']);
                                            if ( isset( $media_meta_data[0]) && empty($media_meta_data[0]) ) {
                                                $media_value = array();
                                            }
                                        } 

                                        if ( isset( $media_value['thumbnail']) ) {
                                            $media_thumbnail =$media_value['thumbnail'];
                                        }
                                        if ( isset( $media_value['height']) ) {
                                           $media_height =$media_value['height']; 
                                        }
                                        if ( isset( $media_value['width']) ) {
                                             $media_width =$media_value['width'];
                                        }
                                            
                                        $image_pre = '';
                                        if($media_thumbnail){
                                            
                                           $image_pre = '<div class="saswp_image_thumbnail">
                                                         <img class="saswp_image_prev" src="'. esc_url( $media_thumbnail).'" />
                                                         <a data-id="'. esc_attr( $meta_field['id']).'" href="#" class="saswp_prev_close">X</a>
                                                        </div>'; 
                                            
                                        }
					$input = sprintf(
						'<fieldset><input style="width: 80%%" id="%s" name="%s" type="text" value="%s">'
                                                . '<input data-id="media" style="width: 19%%" class="button" id="%s_button" name="%s_button" type="button" value="Upload" />'
                                                . '<input type="hidden" data-id="'. esc_attr( $meta_field['id']).'_height" class="upload-height" name="'. esc_attr( $meta_field['id']).'_height" id="'. esc_attr( $meta_field['id']).'_height" value="'. esc_attr( $media_height).'">'
                                                . '<input type="hidden" data-id="'. esc_attr( $meta_field['id']).'_width" class="upload-width" name="'. esc_attr( $meta_field['id']).'_width" id="'. esc_attr( $meta_field['id']).'_width" value="'. esc_attr( $media_width).'">'
                                                . '<input type="hidden" data-id="'. esc_attr( $meta_field['id']).'_thumbnail" class="upload-thumbnail" name="'. esc_attr( $meta_field['id']).'_thumbnail" id="'. esc_attr( $meta_field['id']).'_thumbnail" value="'. esc_attr( $media_thumbnail).'">'                                                
                                                . '<div class="saswp_image_div_'. esc_attr( $meta_field['id']).'">'                                               
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
                                             if (strpos($meta_field['id'], 'saswp_review_item_reviewed') !== false){
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
							esc_html( $value )
						);
					}
					$input .= '</select>';
					break;
                                
                                case 'checkbox':
                                    
                                        $rating_class = '';
                                         
                                        if (strpos($meta_field['id'], 'speakable') === false){
                                             $rating_class = 'class="saswp-enable-rating-review-'.strtolower($schema_type).'"';   
                                        }
                                                                            
                                        if(strpos($meta_field['id'], 'rating_automate') !== false){
                                            $rating_class = 'class="saswp-enable-rating-automate-'.strtolower($schema_type).'"';  
                                        }
                                        if(strpos($meta_field['id'], 'product_pros') !== false){
                                            $rating_class = 'class="saswp-enable-pros-'.strtolower($schema_type).'"';  
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
                                                
                                                if ( isset( $meta_value) ) {
                                                    
                                                    if(in_array($key, $meta_value) ) {

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
                                        if ( isset( $meta_field['note']) ) {
                                            
                                          $input .='<p>'.esc_html( $meta_field['note']).'</p>';  
                                          
                                        }
					break;
                                case 'text':
                                case 'number':    
                                    $class = '';
                                             if (strpos($meta_field['id'], 'closes_time') !== false || strpos($meta_field['id'], 'opens_time') !== false || strpos($meta_field['id'], 'start_time') !== false || strpos($meta_field['id'], 'end_time') !== false){
                                                $class='saswp-timepicker';    
                                             }
                                             if (saswp_is_date_field($meta_field['id'])) {
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
                                            if ( isset( $meta_field['note']) ) {
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
                           $meta_field['id'] == 'saswp_review_review_count_'.$schema_id         ||
                           $meta_field['id'] == 'saswp_review_rating_'.$schema_id               ||
                           $meta_field['id'] == 'local_review_count_'.$schema_id                ||
                           $meta_field['id'] == 'local_rating_automate_'.$schema_id                ||
                           $meta_field['id'] == 'local_google_place_id_'.$schema_id                ||
                           $meta_field['id'] == 'saswp_recipe_schema_rating_'.$schema_id        ||
                           $meta_field['id'] == 'saswp_recipe_schema_review_count_'.$schema_id  ||
                           $meta_field['id'] == 'saswp_software_schema_rating_count_'.$schema_id ||
                           $meta_field['id'] == 'saswp_review_worst_count_'.$schema_id
                                
                          )
                          {
                            $output .= '<tr class="saswp-rating-review-'.strtolower($schema_type).'"><th>'.$label.'</th><td>'.$input.'</td></tr>'; 
                          }elseif($schema_type == 'Review' && $meta_field['id'] != 'saswp_review_schema_enable_rating_'.$schema_id) {
                            
                            $output .= '<tr class="saswp-review-tr"><th>'.$label.'</th><td>'.$input.'</td></tr>';   
                              
                          }elseif($meta_field['id'] != 'product_pros_'.$schema_id){
                            if($meta_field['type'] != 'global_mapping'){
                                $output .= '<tr class="saswp-product-pros"><th>'.$label.'</th><td>'.$input.'</td></tr>';  
                            } 
                          }else{
                             $output .= '<tr><th>'.$label.'</th><td>'.$input.'</td></tr>';  
                          }                                                                       
			
		    }
        }
                
                     $tabs_fields  = '';
                     
                     if(empty($disabled_schema) ) {
                         
                         if($modified  ||$modify_this == 1){
                             $tabs_fields .= '<div schema-id="'. esc_attr( $schema_id).'" class="saswp-table-create-onload saswp-ps-toggle">';    
                         }else{
                             $tabs_fields .= '<div schema-id="'. esc_attr( $schema_id).'" class="saswp-table-create-onload saswp-ps-toggle saswp_hide">'; 
                         }
                                                     
                     }else{                         
                         $tabs_fields .= '<div schema-id="'. esc_attr( $schema_id).'" class="saswp-table-create-onload saswp-ps-toggle saswp_hide">';                          
                     }
                     
                     
                     //$output variable is already escaped above
                     $tabs_fields .= '<table class="form-table"><tbody>' . $output . '</tbody></table>';
                     $tabs_fields .= '</div>';
                       
                     if($item_reviewed){
                        $tabs_fields .=  $this->saswp_schema_fields_html_on_the_fly($item_reviewed, $schema_id, $post_id, $disabled_schema, $modify_this, $modified);    
                     }else{
                         
                        $tabs_fields .=  $this->saswp_schema_fields_html_on_the_fly($schema_type, $schema_id, $post_id, $disabled_schema, $modify_this, $modified); 
                        
                     }
                                                     
                return $tabs_fields;                                               
	}	
     
    public function saswp_save_meta_fields_value($post_meta, $response, $post_id){
            
            foreach ( $response as $meta_field ) {
                            
			if ( isset($meta_field['id']) && isset( $post_meta[ $meta_field['id'] ] ) ) {
                            
				if ( isset( $meta_field['type']) ) {
                    switch ( $meta_field['type'] ) {
                                        
                        case 'media':                                                                                                  
                                $media_key       = $meta_field['id'].'_detail';
                                $media_height    = sanitize_text_field( $post_meta[ $meta_field['id'].'_height' ] );
                                $media_width     = sanitize_text_field( $post_meta[ $meta_field['id'].'_width' ] );
                                $media_thumbnail = sanitize_text_field( $post_meta[ $meta_field['id'].'_thumbnail' ] );
                                
                                if($media_height && $media_width && $media_thumbnail){

                                    $media_detail = array(                                                    
                                        'height'    => $media_height,
                                        'width'     => $media_width,
                                        'thumbnail' => $media_thumbnail,
                                    );
                                
                                    saswp_update_post_meta( $post_id, $media_key, $media_detail);

                                }
                                
                                break;
    					case 'email':
    						$post_meta[ $meta_field['id'] ] = sanitize_email( $post_meta[ $meta_field['id'] ] );
    						break;
    					case 'text':
    						$post_meta[ $meta_field['id'] ] = sanitize_text_field( $post_meta[ $meta_field['id'] ] );
    						break;
                        case 'textarea':
    						$post_meta[ $meta_field['id'] ] = saswp_sanitize_textarea_field( $post_meta[ $meta_field['id'] ] );
    						break;    
                        default:
    						$post_meta[ $meta_field['id'] ] = wp_unslash( $post_meta[ $meta_field['id'] ] );						
                                                
    				}
                }
				saswp_update_post_meta( $post_id, $meta_field['id'], $post_meta[ $meta_field['id'] ] );
			} elseif ( isset($meta_field['type']) && $meta_field['type'] === 'checkbox' ) {
				saswp_delete_post_meta( $post_id, $meta_field['id']);
			}
		    }
            
        }    
        
    public function saswp_save_common_view($post_id, $all_schema = null){

                
                $post_meta    = array();                    
                // phpcs:ignore WordPress.Security.NonceVerification.Missing -- this is a dependent function and its all security measurament is done wherever it has been used.
                if ( is_array( $_POST) ) {
                    // phpcs:ignore WordPress.Security.NonceVerification.Missing -- this is a dependent function and its all security measurament is done wherever it has been used.
                    $post_meta    = $_POST;
                    // phpcs:ignore WordPress.Security.NonceVerification.Missing -- this is a dependent function and its all security measurament is done wherever it has been used.
                }                
                // phpcs:ignore WordPress.Security.NonceVerification.Missing -- this is a dependent function and its all security measurament is done wherever it has been used.    
                $schema_count = 0;
                                                
                if ( ! empty( $all_schema) ) {
                  $schema_count = count($all_schema);  
                }
               
                if($schema_count > 0){
                                                                      
                 foreach( $all_schema as $schema){
                   // phpcs:ignore WordPress.Security.NonceVerification.Missing -- this is a dependent function and its all security measurament is done wherever it has been used.
                     if( isset($_POST['saswp_modify_this_schema_'.$schema->ID]) && !empty($_POST['saswp_modify_this_schema_'.$schema->ID]) ){
                        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- this is a dependent function and its all security measurament is done wherever it has been used.
                        saswp_update_post_meta( $post_id, 'saswp_modify_this_schema_'.$schema->ID, intval($_POST['saswp_modify_this_schema_'.$schema->ID]));
                     }                     
                    
                     foreach ( $this->schema_type_element as $element){
                          
                        foreach( $element as $key => $val){
                            
                            $element_val          = array();   
                            // phpcs:ignore WordPress.Security.NonceVerification.Missing -- this is a dependent function and its all security measurament is done wherever it has been used.                
                                if ( is_array( $_POST) ) {
                                // phpcs:ignore WordPress.Security.NonceVerification.Missing -- this is a dependent function and its all security measurament is done wherever it has been used.
                                    if(array_key_exists($val.'_'.$schema->ID, $_POST) ) {
                               // phpcs:ignore WordPress.Security.NonceVerification.Missing -- this is a dependent function and its all security measurament is done wherever it has been used.
                                        $data = (array) $_POST[$val.'_'.$schema->ID];  
                                     // phpcs:ignore WordPress.Security.NonceVerification.Missing -- this is a dependent function and its all security measurament is done wherever it has been used.
                                            if($data){
                                        
                                                foreach ( $data as $supply){
        
                                                    $sanitize_data = array();
        
                                                        foreach( $supply as $k => $el){   
                                                                if ( isset( $el) ) {
                                                                    $sanitize_data[$k] = wp_kses_post(wp_unslash($el));                                                                                                                                   
                                                                }                                               
                                                                
                                                        }
                                                        if($sanitize_data){
                                                            $element_val[] = $sanitize_data;     
                                                        }                                         
                                                } 
                                            }                                                                                                         
                                    }
                                }   

                                if ( ! empty( $element_val) ) {
                                    saswp_update_post_meta( $post_id, $val.'_'.intval($schema->ID), $element_val);                                                                                                              
                                }                            
                                
                           }    
                         
                     }    
                                                                     
                        $response          = saswp_get_fields_by_schema_type($schema->ID, 'save'); 
                        
                        $this->saswp_save_meta_fields_value($post_meta, $response, $post_id);
                        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- this is a dependent function and its all security measurament is done wherever it has been used.
                        if ( isset( $_POST['saswp_review_item_reviewed_'.$schema->ID]) && $_POST['saswp_review_item_reviewed_'.$schema->ID] !='' ) {
                            // phpcs:ignore WordPress.Security.NonceVerification.Missing -- this is a dependent function and its all security measurament is done wherever it has been used.
                             $item_reviewed = sanitize_text_field($_POST['saswp_review_item_reviewed_'.$schema->ID]);
                            
                             $response          = saswp_get_fields_by_schema_type($schema->ID, 'save', $item_reviewed); 
                             $this->saswp_save_meta_fields_value($post_meta, $response, $post_id);
                        }
                   
                }                                                                                      
            }           
    }    
                    
}