<?php                                                                               
	add_action( 'add_meta_boxes', 'saswp_schema_options_add_meta_box' ) ;
	add_action( 'save_post', 'saswp_schema_options_add_meta_box_save' ) ;
	
        function saswp_schema_options_add_meta_box() {
	add_meta_box(
		'schema_options',
		esc_html__( 'Advance Schema Options', 'ads-for-wp' ),
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
                ?>    
                <style type="text/css">
                   .option-table-class{width:100%;}
                   .option-table-class tr td {padding: 10px 10px 10px 10px ;}
                   .option-table-class tr > td{width: 30%;}
                   .option-table-class tr td:last-child{width: 60%;}
                   .option-table-class input[type="text"], select{width:100%;}
                </style>      
                <div class="misc-pub-section">
                   <table class="option-table-class">
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
                    <?php
        }
   
        function saswp_schema_options_add_meta_box_save( $post_id ) {
                if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
                if ( ! isset( $_POST['saswp_schema_options_nonce'] ) || ! wp_verify_nonce( $_POST['saswp_schema_options_nonce'], 'saswp_schema_options_nonce' ) ) return;
                if ( ! current_user_can( 'edit_post', $post_id ) ) return;
                                               
                $notAccessibleForFree ='';
                $isAccessibleForFree ='';
                $paywall_class_name ='';
                if ( isset( $_POST['notAccessibleForFree'] ) )
                        $notAccessibleForFree = $_POST['notAccessibleForFree'];
                if ( isset( $_POST['isAccessibleForFree'] ) )
                        $isAccessibleForFree = $_POST['isAccessibleForFree'];
                if ( isset( $_POST['paywall_class_name'] ) )
                        $paywall_class_name = $_POST['paywall_class_name'];
                
                 $saswp_schema_options  =    array('isAccessibleForFree'=>$isAccessibleForFree,'notAccessibleForFree'=>$notAccessibleForFree,'paywall_class_name'=>$paywall_class_name);
                 update_post_meta( $post_id, 'schema_options', $saswp_schema_options);
               
               
        }    


