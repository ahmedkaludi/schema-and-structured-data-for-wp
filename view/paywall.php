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
	
	add_action( 'save_post', 'saswp_schema_options_add_meta_box_save' ) ;
	       
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
                
                $schema_options    = get_post_meta($post->ID, 'schema_options', true);
                                                                
                ?>    
                
                <div class="misc-pub-section">
                    
                   <table class="option-table-class saswp-paywall-table-class">
                        <tbody>
                            <tr>
                                <td><label for="notAccessibleForFree"><?php echo esc_html__( 'Paywall', 'schema-and-structured-data-for-wp' ) ?></label></td>
                              <td><input type="checkbox" id="notAccessibleForFree" name="notAccessibleForFree" value="1" <?php if ( isset( $schema_options['notAccessibleForFree']) && $schema_options['notAccessibleForFree']==1){echo 'checked'; }?>>
                              </td>
                            </tr>
                            <tr <?php if ( ! isset( $schema_options['notAccessibleForFree']) || $schema_options['notAccessibleForFree']!=1){echo 'style="display:none"'; }?>>
                              <td><label for="isAccessibleForFree"><?php echo esc_html__( 'Is accessible for free', 'schema-and-structured-data-for-wp' ) ?></label></td>
                              <td>
                                  <select name="isAccessibleForFree" id="isAccessibleForFree">
                                    <option value="False" <?php if( isset($schema_options['isAccessibleForFree']) && $schema_options['isAccessibleForFree']=='False'){echo 'selected'; }?>><?php echo esc_html__( 'False', 'schema-and-structured-data-for-wp' ); ?></option>
                                    <option value="True" <?php if( isset($schema_options['isAccessibleForFree']) && $schema_options['isAccessibleForFree']=='True'){echo 'selected'; }?>><?php echo esc_html__( 'True', 'schema-and-structured-data-for-wp' ); ?></option>
                                  </select>
                              </td>
                            </tr>
                            <tr <?php if ( ! isset( $schema_options['notAccessibleForFree']) || $schema_options['notAccessibleForFree']!=1){echo 'style="display:none"'; }?>>
                              <td>
                                <label for="paywall_class_name"><?php echo esc_html__( 'Enter the class name of paywall section', 'schema-and-structured-data-for-wp' ); ?></label>  
                              </td>
                              <td><input type="text" id="paywall_class_name" name="paywall_class_name" value="<?php if( isset($schema_options['paywall_class_name']) ){echo esc_attr( $schema_options['paywall_class_name']); }?>"></td>
                            </tr>
                        </tbody>
                    </table> 
                    
                </div>
               
                    <?php
        }
   
        function saswp_schema_options_add_meta_box_save( $post_id ) {
            
                if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
                // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: Nonce verification done here so unslash is not used.
                if ( ! isset( $_POST['saswp_schema_options_nonce'] ) || ! wp_verify_nonce( $_POST['saswp_schema_options_nonce'], 'saswp_schema_options_nonce' ) ) return;
                if ( ! current_user_can( 'edit_post', $post_id ) ) return;
                                                  
                $notAccessibleForFree = '';
                $isAccessibleForFree  = '';
                $paywall_class_name   = '';
                $enable_custom_field  = '';                
                $fixed_text           = '';
                $taxonomy_term        = '';
                $fixed_image          = '';
                $cus_meta_field       = array();
                $meta_list            = array();
                $template_field       = array();
                
                if ( isset( $_POST['notAccessibleForFree'] ) )
                        $notAccessibleForFree = sanitize_text_field( wp_unslash( $_POST['notAccessibleForFree'] ) );
                if ( isset( $_POST['isAccessibleForFree'] ) )
                        $isAccessibleForFree = sanitize_text_field( wp_unslash( $_POST['isAccessibleForFree'] ) );
                if ( isset( $_POST['paywall_class_name'] ) )
                        $paywall_class_name = sanitize_text_field( wp_unslash( $_POST['paywall_class_name'] ) );
                if ( isset( $_POST['saswp_enable_custom_field'] ) )
                        $enable_custom_field = sanitize_text_field( wp_unslash( $_POST['saswp_enable_custom_field'] ) );
                if ( isset( $_POST['saswp_modify_method'] ) )
                        $saswp_modify_method = sanitize_text_field( wp_unslash( $_POST['saswp_modify_method'] ) );                
                if ( isset( $_POST['saswp_meta_list_val'] ) ) {                    
                    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash --Reason post data is just used here so there is no necessary of unslash
                    $meta_list = array_map ('sanitize_text_field', $_POST['saswp_meta_list_val']); 
                }               
                if ( isset( $_POST['saswp_fixed_text'] ) ) {   
                    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash --Reason post data is just used here so there is no necessary of unslash                 
                    $fixed_text = array_map ('sanitize_text_field', $_POST['saswp_fixed_text']);
                }
                if ( isset( $_POST['saswp_taxonomy_term'] ) ) {
                    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash --Reason post data is just used here so there is no necessary of unslash                    
                    $taxonomy_term = array_map ('sanitize_text_field', $_POST['saswp_taxonomy_term']);
                }
                if ( isset( $_POST['saswp_custom_meta_field'] ) ) {
                    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash --Reason post data is just used here so there is no necessary of unslash                    
                    $cus_meta_field = array_map ('sanitize_text_field', $_POST['saswp_custom_meta_field']);
                }
                if ( isset( $_POST['saswp_fixed_image'] ) ) { 
                    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                    $fixed_image = wp_unslash($_POST['saswp_fixed_image']);
                }
                if ( isset( $_POST['saswp_schema_template_field'] ) ) {  
                    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                    $template_field     =   $_POST['saswp_schema_template_field'];
                    if ( ! empty( $template_field ) && is_array( $template_field ) ) {
                        foreach ($template_field as $tf_key => $tf_value) {

                            if ( ! empty( $tf_value ) && is_array( $tf_value ) ) {
                                $template_field[$tf_key]    =   array_map( 'sanitize_text_field', $tf_value );
                            }
                        }
                    }
                }
                
                 $saswp_schema_options  =    array(
                                                'isAccessibleForFree'   => $isAccessibleForFree,
                                                'notAccessibleForFree'  => $notAccessibleForFree,
                                                'paywall_class_name'    => $paywall_class_name, 
                                                'enable_custom_field'   => $enable_custom_field,
                                                'saswp_modify_method'   => $saswp_modify_method
                                            );   
                                            
                 update_post_meta( $post_id, 'schema_options', $saswp_schema_options);                 
                 update_post_meta( $post_id, 'saswp_meta_list_val', $meta_list);
                 update_post_meta( $post_id, 'saswp_fixed_text', $fixed_text);
                 update_post_meta( $post_id, 'saswp_taxonomy_term', $taxonomy_term);
                 update_post_meta( $post_id, 'saswp_fixed_image', $fixed_image);
                 update_post_meta( $post_id, 'saswp_custom_meta_field', $cus_meta_field);                              
                 update_post_meta( $post_id, 'saswp_schema_template_field', $template_field);                              
}    