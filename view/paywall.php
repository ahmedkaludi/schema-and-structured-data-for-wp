<?php    
/**
 * Paywall page
 *
 * @author   Magazine3
 * @category Admin
 * @path     view/paywall
 * @version 1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

	add_action( 'add_meta_boxes', 'saswp_schema_options_add_meta_box' ) ;
	add_action( 'save_post', 'saswp_schema_options_add_meta_box_save' ) ;
	
        function saswp_schema_options_add_meta_box() {
            
	add_meta_box(
		'schema_options',
		esc_html__( 'Advance Schema Options', 'schema-and-structured-data-for-wp' ),
		'saswp_schema_options_meta_box_callback',
		'saswp',
		'advanced',
		'low'
	);
        
        }
        function saswp_schema_options_get_meta( $value ) {
            global $post;
            
            $field = get_post_meta( $post->ID, $value, true );
           
            if ( ! empty( $field ) ) {
                    return is_array( $field ) ? stripslashes_deep( $field ) : stripslashes( wp_kses_decode_entities( $field ) );
            } else {
                    return false;
            }
      }
        function saswp_schema_options_meta_box_callback( $post) {
            
                wp_nonce_field( 'saswp_schema_options_nonce', 'saswp_schema_options_nonce' ); 
                
                $schema_options    = esc_sql ( get_post_meta($post->ID, 'schema_options', true)  );
                                
                $meta_list         = esc_sql ( get_post_meta($post->ID, 'saswp_meta_list_val', true)  ); 
                $fixed_text        = esc_sql ( get_post_meta($post->ID, 'saswp_fixed_text', true)  ); 
                $cus_field         = esc_sql ( get_post_meta($post->ID, 'saswp_custom_meta_field', true)  ); 
                
                ?>    
                
                <div class="misc-pub-section">
                    
                   <table class="option-table-class saswp-paywall-table-class">
                        <tbody>
                            <tr>
                                <td><label for="notAccessibleForFree"><?php echo esc_html__( 'Paywall', 'schema-and-structured-data-for-wp' ) ?></label></td>
                              <td><input type="checkbox" id="notAccessibleForFree" name="notAccessibleForFree" value="1" <?php if(isset($schema_options['notAccessibleForFree']) && $schema_options['notAccessibleForFree']==1){echo 'checked'; }?>>
                              </td>
                            </tr>
                            <tr <?php if(!isset($schema_options['notAccessibleForFree']) || $schema_options['notAccessibleForFree']!=1){echo 'style="display:none"'; }?>>
                              <td><label for="isAccessibleForFree"><?php echo esc_html__( 'Is accessible for free', 'schema-and-structured-data-for-wp' ) ?></label></td>
                              <td>
                                  <select name="isAccessibleForFree" id="isAccessibleForFree">
                                    <option value="False" <?php if( isset($schema_options['isAccessibleForFree']) && $schema_options['isAccessibleForFree']=='False'){echo 'selected'; }?>><?php echo esc_html__( 'False', 'schema-and-structured-data-for-wp' ); ?></option>
                                    <option value="True" <?php if( isset($schema_options['isAccessibleForFree']) && $schema_options['isAccessibleForFree']=='True'){echo 'selected'; }?>><?php echo esc_html__( 'True', 'schema-and-structured-data-for-wp' ); ?></option>
                                  </select>
                              </td>
                            </tr>
                            <tr <?php if(!isset($schema_options['notAccessibleForFree']) || $schema_options['notAccessibleForFree']!=1){echo 'style="display:none"'; }?>>
                              <td>
                                <label for="paywall_class_name"><?php echo esc_html__( 'Enter the class name of paywall section', 'schema-and-structured-data-for-wp' ); ?></label>  
                              </td>
                              <td><input type="text" id="paywall_class_name" name="paywall_class_name" value="<?php if( isset($schema_options['paywall_class_name']) ){echo esc_attr($schema_options['paywall_class_name']); }?>"></td>
                            </tr>
                        </tbody>
                    </table> 
                    
                </div>
               <!-- custom fields for schema output starts here -->
               
               <div class="misc-pub-section">
                <table class="option-table-class">
                        <tr><td><label><?php echo esc_html__( 'Modify Schema Output', 'schema-and-structured-data-for-wp' ) ?></label></td><td><input type="checkbox" id="saswp_enable_custom_field" name="saswp_enable_custom_field" value="1" <?php if(isset($schema_options['enable_custom_field']) && $schema_options['enable_custom_field']==1){echo 'checked'; }?>></td></tr>   
                </table>  
                   <div class="saswp-custom-fields-div" <?php if(!isset($schema_options['enable_custom_field']) || $schema_options['enable_custom_field'] ==0){echo 'style="display:none;"'; }?>>
                       <table class="option-table-class saswp-custom-fields-table">
                           
                        <?php 
                        
                        if(!empty($meta_list)){
                            
                            $schema_type    = esc_sql ( get_post_meta($post->ID, 'schema_type', true)  );
                            
                            $service     = new saswp_output_service();
                            $meta_fields = $service->saswp_get_all_schema_type_fields($schema_type);
                            
                            foreach($meta_list as $fieldkey => $fieldval){
                                                                                        
                            $option = '';
                            echo '<tr>'; 
                            echo '<td><select class="saswp-custom-fields-name">';
                            
                            foreach ($meta_fields as $key =>$val){
                                
                                if( $fieldkey == $key){
                                    
                                 $option .='<option value="'.esc_attr($key).'" selected>'.esc_attr($val).'</option>';   
                                 
                                }else{
                                    
                                 $option .='<option value="'.esc_attr($key).'">'.esc_attr($val).'</option>';   
                                 
                                }
                                
                            }
                            
                            
                            echo $option;                            
                            echo '</select>';
                            echo '</td>';
                                                        
                            $list_html = '';
                            $meta_list_fields = include(SASWP_DIR_NAME . '/core/array-list/meta_list.php');                            
                            
                            $meta_list_arr = $meta_list_fields['text'];
                            
                            if (strpos($fieldkey, 'image') !== false) {
                                  $meta_list_arr = $meta_list_fields['image'];
                            }
                                                                                    
                            foreach($meta_list_arr as $list){
                            
                                $list_html.= '<optgroup label="'.$list['label'].'">';
                                
                                foreach ($list['meta-list'] as $key => $val){
                                    
                                    if( $fieldval == $key){
                                        $list_html.= '<option value="'.$key.'" selected>'.$val.'</option>';    
                                    }else{
                                        $list_html.= '<option value="'.$key.'">'.$val.'</option>';    
                                    }
                                                                        
                                }
                                
                                $list_html.= '</optgroup>';
                                
                            } 
                            echo '<td>';
                            echo '<select class="saswp-custom-meta-list" name="saswp_meta_list_val['.$fieldkey.']">';
                            echo $list_html;
                            echo '</select>';
                            echo '</td>';
                            
                            
                            if($fieldval == 'manual_text'){
                                echo '<td><input style="width:100%;" type="text" name="saswp_fixed_text['.$fieldkey.']" value="'.$fixed_text[$fieldkey].'"></td>';    
                            }
                            
                            if($fieldval == 'custom_field'){
                                 echo '<td><select class="saswp-custom-fields-select2" name="saswp_custom_meta_field['.esc_attr($fieldkey).']">';
                                 echo '<option value="'.esc_attr($cus_field[$fieldkey]).'">'.preg_replace( '/^_/', '', esc_html( str_replace( '_', ' ', $cus_field[$fieldkey] ) ) ).'</option>';
                                 echo '</select></td>';
                            }
                                                                                   
                            echo '</tr>';
                            
                            }
                            
                        }
                        
                        ?>
                           
                           
                        </table>                    
                   <table class="option-table-class">
                       <tr><td></td><td><a style="float:right;" class="button button-primary saswp-add-custom-fields"><?php echo esc_html__( 'Add Field', 'schema-and-structured-data-for-wp' ); ?></a></td></tr>   
                   </table>
                       
                   </div>                   
                  
                </div>
               <!-- custom fields for schema output ends here -->
                    <?php
        }
   
        function saswp_schema_options_add_meta_box_save( $post_id ) {
            
                if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
                if ( ! isset( $_POST['saswp_schema_options_nonce'] ) || ! wp_verify_nonce( $_POST['saswp_schema_options_nonce'], 'saswp_schema_options_nonce' ) ) return;
                if ( ! current_user_can( 'edit_post', $post_id ) ) return;
                                                  
                $notAccessibleForFree = '';
                $isAccessibleForFree  = '';
                $paywall_class_name   = '';
                $enable_custom_field  = '';
                $custom_fields        = '';
                
                if ( isset( $_POST['notAccessibleForFree'] ) )
                        $notAccessibleForFree = sanitize_text_field($_POST['notAccessibleForFree']);
                if ( isset( $_POST['isAccessibleForFree'] ) )
                        $isAccessibleForFree = sanitize_text_field($_POST['isAccessibleForFree']);
                if ( isset( $_POST['paywall_class_name'] ) )
                        $paywall_class_name = sanitize_text_field($_POST['paywall_class_name']);
                if ( isset( $_POST['saswp_enable_custom_field'] ) )
                        $enable_custom_field = sanitize_text_field($_POST['saswp_enable_custom_field']);                
                if ( isset( $_POST['saswp_meta_list_val'] ) )                    
                    $meta_list = array_map ('sanitize_text_field', $_POST['saswp_meta_list_val']);                
                if ( isset( $_POST['saswp_fixed_text'] ) )                    
                    $fixed_text = array_map ('sanitize_text_field', $_POST['saswp_fixed_text']);
                if ( isset( $_POST['saswp_custom_meta_field'] ) )                    
                    $cus_meta_field = array_map ('sanitize_text_field', $_POST['saswp_custom_meta_field']);
                
                 $saswp_schema_options  =    array(
                                                'isAccessibleForFree'   => $isAccessibleForFree,
                                                'notAccessibleForFree'  => $notAccessibleForFree,
                                                'paywall_class_name'    => $paywall_class_name, 
                                                'enable_custom_field'   => $enable_custom_field
                                            );   
                 
                 update_post_meta( $post_id, 'schema_options', $saswp_schema_options);
                 
                 update_post_meta( $post_id, 'saswp_meta_list_val', $meta_list);
                 update_post_meta( $post_id, 'saswp_fixed_text', $fixed_text);
                 update_post_meta( $post_id, 'saswp_custom_meta_field', $cus_meta_field);
               
               
        }    


