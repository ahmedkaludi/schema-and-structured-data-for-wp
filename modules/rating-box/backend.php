<?php
/**
 * Rating Class
 *
 * @author   Magazine3
 * @category Admin
 * @path     view/review
 * @Version 1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class saswp_rating_box_backend {
    
 private $screen = array();
    
        public function __construct() {    
     
		add_action( 'add_meta_boxes', array( $this, 'saswp_review_add_meta_box' ) );
		add_action( 'save_post', array( $this, 'saswp_review_save' ) );
                
	}
        
        function saswp_review_add_meta_box($post) {
            
            global $sd_data, $saswp_metaboxes;          
             
            $review_post_id = '';
            
            if(is_object($post)){
                
                $review_post_id = $post->ID;
                
            } 
             
          if(get_post_status($review_post_id)=='publish' && saswp_remove_warnings($sd_data, 'saswp-review-module', 'saswp_string')==1){
              
           $show_post_types = get_post_types();
           unset($show_post_types['adsforwp'],$show_post_types['saswp'],$show_post_types['attachment'], $show_post_types['revision'], $show_post_types['nav_menu_item'], $show_post_types['user_request'], $show_post_types['custom_css']);            
           $this->screen = $show_post_types;
           
           if($this->screen){
               
               foreach ( $this->screen as $single_screen ) {
                   
                        if(saswp_current_user_allowed()){

                            add_meta_box(
                              'saswp_rating_box',
                              esc_html__( 'Rating Box', 'schema-and-structured-data-for-wp' ),
                              array( $this, 'saswp_meta_box_callback' ),
                              $single_screen,
                              'advanced',
                              'default'
                            );                   
                            $saswp_metaboxes[]= 'saswp_rating_box';                         
                        }               
                    }           
               }                          
          }
       }
        function saswp_review_get_meta( $value ) {
            
            global $post;
            
            $field = get_post_meta( $post->ID, $value, true );
           
            if ( ! empty( $field ) ) {
                    return is_array( $field ) ? stripslashes_deep( $field ) : stripslashes( wp_kses_decode_entities( $field ) );
            } else {
                    return false;
            }
    }
        function saswp_meta_box_callback( $post) {
            
                wp_nonce_field( 'saswp_review_nonce', 'saswp_review_nonce' ); 
                
                $saswp_review_item_feature     = array();
                $saswp_review_item_star_rating = array();
                
                $saswp_review_details          = get_post_meta($post->ID, 'saswp_review_details', true);
                                                
                if(isset($saswp_review_details['saswp-review-item-feature'])){
                    $saswp_review_item_feature = $saswp_review_details['saswp-review-item-feature'];    
                }
                if(isset($saswp_review_details['saswp-review-item-star-rating'])){
                    $saswp_review_item_star_rating = $saswp_review_details['saswp-review-item-star-rating'];    
                }
                
                ?>                     
                <div>
                    <div class="saswp-enable-review-on-post"><label><?php echo esc_html__('Enable/Disable', 'schema-and-structured-data-for-wp'); ?>   <input type="checkbox" id="saswp-review-item-enable" name="saswp-review-item-enable" <?php echo (isset( $saswp_review_details['saswp-review-item-enable'] ) &&  $saswp_review_details['saswp-review-item-enable'] == 1 ? 'checked="checked"' : ''); ?> value="1"></label></div>
                    
                    <div class="saswp-review-fields">
                    <div class="saswp-review-item">
                        <table class="saswp-review-tables">
                        <tr>
                            <td><label><?php echo esc_html__('Review Title', 'schema-and-structured-data-for-wp'); ?></label></td>                            
                            <td><div class="saswp-field"><input type="text" id="saswp-review-item-title" name="saswp-review-item-title" value="<?php if ( isset( $saswp_review_details['saswp-review-item-title'] ) && ( ! empty( $saswp_review_details['saswp-review-item-title'] ) ) ) echo esc_attr( $saswp_review_details['saswp-review-item-title'] ); ?>"></div></td>
                        </tr>
                        <tr>
                            <td><label><?php echo esc_html__('Review Location', 'schema-and-structured-data-for-wp'); ?></label></td>                            
                            <td>
                                <div class="saswp-field">
                                    <select id="saswp-review-location" name="saswp-review-location">                                        
                                        <option value="1" <?php if ( isset( $saswp_review_details['saswp-review-location'] ) && (  $saswp_review_details['saswp-review-location'] == 1 ) )  echo 'selected'; ?>><?php echo esc_html__('After The Content', 'schema-and-structured-data-for-wp'); ?></option>    
                                        <option value="2" <?php if ( isset( $saswp_review_details['saswp-review-location'] ) && (  $saswp_review_details['saswp-review-location'] == 2 ) )  echo 'selected'; ?>><?php echo esc_html__('Before The Content', 'schema-and-structured-data-for-wp'); ?></option>    
                                        <option value="3" <?php if ( isset( $saswp_review_details['saswp-review-location'] ) && (  $saswp_review_details['saswp-review-location'] == 3 ) )  echo 'selected'; ?>><?php echo esc_html__('Custom (Use ShortCode)', 'schema-and-structured-data-for-wp'); ?></option>    
                                    </select>
                                    <input class="saswp-review-shortcode" type="text" value="<?php echo '[saswp-review id=&quot;review&quot;]'; ?>" readonly> 
                                </div>
                            </td>
                        </tr>
                        
                    </table> 
                    </div>

                    <div class="saspw-review-item-list">
                        <table class="saswp-review-item-list-table saswp-review-tables">
                            
                            <?php
                            if(!empty($saswp_review_item_feature)){
                                
                                for ($i=0; $i<count($saswp_review_item_feature); $i++){
                                ?>
                                <tr class="saswp-review-item-tr">
                                <td><?php echo esc_html__('Review Item Feature', 'schema-and-structured-data-for-wp'); ?></td>
                                <td><input type="text" name="saswp-review-item-feature[]" value="<?php echo esc_attr($saswp_review_item_feature[$i]); ?>"></td>
                                <td><?php echo esc_html__('Rating', 'schema-and-structured-data-for-wp'); ?></td>
                                <td><input step="0.1" min="0" max="5" type="number" name="saswp-review-item-star-rating[]" value="<?php echo esc_attr($saswp_review_item_star_rating[$i]); ?>"></td>
                                <td><a type="button" class="saswp-remove-review-item button">x</a></td>
                                </tr>
                                <?php       
                               
                                }                               
                            }
                            ?>
                        </table>
                        <div class="saswp-over-all-raring"><label><?php echo esc_html__('Over All Rating', 'schema-and-structured-data-for-wp'); ?></label><input type="text" id="saswp-review-item-over-all" name="saswp-review-item-over-all" value="<?php if ( isset( $saswp_review_details['saswp-review-item-over-all'] ) && ( ! empty( $saswp_review_details['saswp-review-item-over-all'] ) ) ) echo esc_attr( $saswp_review_details['saswp-review-item-over-all'] ); ?>" readonly></div>
                        <div><a class="button saswp-add-more-item"><?php echo esc_html__('Add Item', 'schema-and-structured-data-for-wp'); ?></a></div>
                    </div>
                
                    <div class="saswp-review-description">
                        <div><label><?php echo esc_html__('Summary Title', 'schema-and-structured-data-for-wp'); ?></label> <input type="text" id="saswp-review-item-description-title" name="saswp-review-item-description-title" value="<?php if ( isset( $saswp_review_details['saswp-review-item-description-title'] ) && ( ! empty( $saswp_review_details['saswp-review-item-description-title'] ) ) ) echo esc_attr( $saswp_review_details['saswp-review-item-description-title'] ); ?>"></div>  
                        <div class="saswp-wp-ediot-desc"><label><?php echo esc_html__('Description', 'schema-and-structured-data-for-wp'); ?></label></div>
                        <?php                        
                        $content       = get_post_meta( $post->ID, 'saswp-review-item-description', true );                        
                        wp_editor( $content, 'saswp-review-item-description', array('textarea_rows'=> '5', 'media_buttons' => FALSE,) );                   
                       ?>
                    </div>

                    <div class="saswp-review-pros-and-cons">                        
                    <div class="saswp-props">
                        <div class="saswp-wp-ediot-desc"><label><?php echo esc_html__('Pros', 'schema-and-structured-data-for-wp'); ?></label></div>
                        <?php
                        $content       = get_post_meta( $post->ID, 'saswp-review-item-props', true );                                         
                        wp_editor( $content, 'saswp-review-item-props',
                                array(
						'tinymce'       => array(
							'toolbar1' => 'bold,italic,underline,bullist,numlist,separator,separator,link,unlink,undo,redo,removeformat',
							'toolbar2' => '',
							'toolbar3' => '',
						),
						'quicktags'     => true,
						'media_buttons' => false,
						'textarea_rows' => 6,
					)
                                );                   
                   ?> 
                    </div>
                    <div class="saswp-cons">
                        <div class="saswp-wp-ediot-desc"><label><?php echo esc_html__('Cons', 'schema-and-structured-data-for-wp'); ?></label></div>
                         <?php
                        $content       = get_post_meta( $post->ID, 'saswp-review-item-cons', true );  
                        wp_editor( $content, 
                            'saswp-review-item-cons', 
                                  array(
						'tinymce'       => array(
							'toolbar1' => 'bold,italic,underline,bullist,numlist,separator,separator,link,unlink,undo,redo,removeformat',
							'toolbar2' => '',
							'toolbar3' => '',
						),
						'quicktags'     => true,
						'media_buttons' => false,
						'textarea_rows' => 6,
					)
                            );                   
                   ?>
                    </div>
                    <div class="clearfix"></div>
                    </div>
                    </div>
                    </div>
                    <?php
        }
   
        function saswp_review_save( $post_id ) {
            
                if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
                if ( ! isset( $_POST['saswp_review_nonce'] ) || ! wp_verify_nonce( $_POST['saswp_review_nonce'], 'saswp_review_nonce' ) ) return;
                if ( ! current_user_can( 'edit_post', $post_id ) ) return; 
                
                $saswp_review_details = array();
                
                if(isset($_POST['saswp-review-item-title'])){
                    $saswp_review_details['saswp-review-item-title'] = sanitize_text_field($_POST['saswp-review-item-title']);
                }
                if(isset($_POST['saswp-review-location'])){
                    $saswp_review_details['saswp-review-location'] = sanitize_text_field($_POST['saswp-review-location']);
                }
                if(isset($_POST['saswp-review-item-feature'])){
                    $saswp_review_details['saswp-review-item-feature'] = array_map( 'sanitize_text_field', wp_unslash( $_POST['saswp-review-item-feature'] ) );
                }
                if(isset($_POST['saswp-review-item-star-rating'])){
                    $saswp_review_details['saswp-review-item-star-rating'] = array_map( 'sanitize_text_field', wp_unslash( $_POST['saswp-review-item-star-rating'] ) );
                }
                if(isset($_POST['saswp-review-item-over-all'])){
                    $saswp_review_details['saswp-review-item-over-all'] = sanitize_text_field($_POST['saswp-review-item-over-all']);
                }
                if(isset($_POST['saswp-review-item-description-title'])){
                    $saswp_review_details['saswp-review-item-description-title'] = sanitize_text_field($_POST['saswp-review-item-description-title']);
                }
                if(isset($_POST['saswp-review-item-enable'])){
                    $saswp_review_details['saswp-review-item-enable'] = sanitize_text_field($_POST['saswp-review-item-enable']);
                }
                if(isset($_POST['saswp-review-item-description'])){
                    update_post_meta( $post_id, 'saswp-review-item-description', wp_kses_post( wp_unslash( $_POST['saswp-review-item-description'] )) );                    
                }
                if(isset($_POST['saswp-review-item-props'])){
                    update_post_meta( $post_id, 'saswp-review-item-props', wp_kses_post( wp_unslash( $_POST['saswp-review-item-props'] )) );                    
                }
                if(isset($_POST['saswp-review-item-cons'])){
                    update_post_meta( $post_id, 'saswp-review-item-cons', wp_kses_post( wp_unslash( $_POST['saswp-review-item-cons'] )) );                    
                }                          
                if(!empty($saswp_review_details)){
                    update_post_meta( $post_id, 'saswp_review_details', $saswp_review_details );   
                }               
        }    
}
if (class_exists('saswp_rating_box_backend')) {
	new saswp_rating_box_backend;
};