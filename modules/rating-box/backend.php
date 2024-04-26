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
                                      
            if(saswp_remove_warnings($sd_data, 'saswp-review-module', 'saswp_string')==1){
                
            $show_post_types = get_post_types();
            unset($show_post_types['adsforwp'],$show_post_types['saswp'], $show_post_types['revision'], $show_post_types['nav_menu_item'], $show_post_types['user_request'], $show_post_types['custom_css']);            
            $this->screen = $show_post_types;
            
            if($this->screen){
               
               foreach ( $this->screen as $single_screen ) {
                   
                        if(saswp_current_user_allowed()){

                            add_meta_box(
                              'saswp_rating_box',
                              saswp_t_string( 'Rating Box' ),
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
                    <div class="saswp-enable-review-on-post"><label><?php echo saswp_t_string('Enable/Disable'); ?>   <input type="checkbox" id="saswp-review-item-enable" name="saswp-review-item-enable" <?php echo (isset( $saswp_review_details['saswp-review-item-enable'] ) &&  $saswp_review_details['saswp-review-item-enable'] == 1 ? 'checked="checked"' : ''); ?> value="1"></label></div>
                    
                    <div class="saswp-review-fields">
                    <div class="saswp-review-item">
                        <table class="saswp-review-tables">
                        <tr>
                            <td><label><?php echo saswp_t_string('Review Title'); ?></label></td>                            
                            <td><div class="saswp-field"><input type="text" id="saswp-review-item-title" name="saswp-review-item-title" value="<?php if ( isset( $saswp_review_details['saswp-review-item-title'] ) && ( ! empty( $saswp_review_details['saswp-review-item-title'] ) ) ) echo esc_attr( $saswp_review_details['saswp-review-item-title'] ); ?>"></div></td>
                        </tr>
                        <tr>
                            <td><label><?php echo saswp_t_string('Review Location'); ?></label></td>                            
                            <td>
                                <div class="saswp-field">
                                    <select id="saswp-review-location" name="saswp-review-location">                                        
                                        <option value="1" <?php if ( isset( $saswp_review_details['saswp-review-location'] ) && (  $saswp_review_details['saswp-review-location'] == 1 ) )  echo 'selected'; ?>><?php echo saswp_t_string('After The Content'); ?></option>    
                                        <option value="2" <?php if ( isset( $saswp_review_details['saswp-review-location'] ) && (  $saswp_review_details['saswp-review-location'] == 2 ) )  echo 'selected'; ?>><?php echo saswp_t_string('Before The Content'); ?></option>    
                                        <option value="3" <?php if ( isset( $saswp_review_details['saswp-review-location'] ) && (  $saswp_review_details['saswp-review-location'] == 3 ) )  echo 'selected'; ?>><?php echo saswp_t_string('Custom (Use ShortCode)'); ?></option>    
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
                                <td><?php echo saswp_t_string('Review Item Feature'); ?></td>
                                <td><input type="text" name="saswp-review-item-feature[]" value="<?php echo esc_attr($saswp_review_item_feature[$i]); ?>"></td>
                                <td><?php echo saswp_t_string('Rating'); ?></td>
                                <td><input step="0.1" min="0" max="5" type="number" name="saswp-review-item-star-rating[]" value="<?php echo esc_attr($saswp_review_item_star_rating[$i]); ?>"></td>
                                <td><a type="button" class="saswp-remove-review-item button">x</a></td>
                                </tr>
                                <?php       
                               
                                }                               
                            }
                            ?>
                        </table>
                        <div class="saswp-over-all-raring"><label><?php echo saswp_t_string('Over All Rating'); ?></label><input type="text" id="saswp-review-item-over-all" name="saswp-review-item-over-all" value="<?php if ( isset( $saswp_review_details['saswp-review-item-over-all'] ) && ( ! empty( $saswp_review_details['saswp-review-item-over-all'] ) ) ) echo esc_attr( $saswp_review_details['saswp-review-item-over-all'] ); ?>" readonly></div>
                        <div><a class="button saswp-add-more-item"><?php echo saswp_t_string('Add Item'); ?></a></div>
                    </div>
             
                    <div class="saswp-review-description">
                        <div><label><?php echo saswp_t_string('Summary Title'); ?></label> <input type="text" id="saswp-review-item-description-title" name="saswp-review-item-description-title" value="<?php if ( isset( $saswp_review_details['saswp-review-item-description-title'] ) && ( ! empty( $saswp_review_details['saswp-review-item-description-title'] ) ) ) echo esc_attr( $saswp_review_details['saswp-review-item-description-title'] ); ?>"></div>  
                        <div class="saswp-wp-ediot-desc"><label><?php echo saswp_t_string('Description'); ?></label></div>
                        <?php                        
                        $content       = get_post_meta( $post->ID, 'saswp-review-item-description', true );                        
                        wp_editor( $content, 'saswp-review-item-description', array('textarea_rows'=> '5', 'media_buttons' => FALSE,) );                   
                       ?>
                    </div>

                    <div class="saswp-review-pros-and-cons">                        
                    <div class="saswp-props">
                        <div class="saswp-wp-ediot-desc"><label><?php echo saswp_t_string('Pros'); ?></label></div>
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
                        <div class="saswp-wp-ediot-desc"><label><?php echo saswp_t_string('Cons'); ?></label></div>
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
                if(isset($_POST['saswp-review-item-description']) && $_POST['saswp-review-item-description'] != '' ){
                    update_post_meta( $post_id, 'saswp-review-item-description', wp_kses_post( wp_unslash( $_POST['saswp-review-item-description'] )) );                    
                }
                if(isset($_POST['saswp-review-item-props']) && $_POST['saswp-review-item-props'] !='' ){
                    update_post_meta( $post_id, 'saswp-review-item-props', wp_kses_post( wp_unslash( $_POST['saswp-review-item-props'] )) );
                }
                if(isset($_POST['saswp-review-item-cons']) && $_POST['saswp-review-item-cons'] != ''){
                    update_post_meta( $post_id, 'saswp-review-item-cons', wp_kses_post( wp_unslash( $_POST['saswp-review-item-cons'] )) );                    
                }                          
                if(!empty($saswp_review_details)){
                    update_post_meta( $post_id, 'saswp_review_details', $saswp_review_details );   
                }               
        }  

        /**
         * Render rating box custom css box appearance
         * @since 1.27
         * */
        public function saswp_rating_box_appearance()
        {
            global $sd_data;
            $rating_head_bgcolor = isset( $sd_data['saswp-rbcc-review-bg-color'] ) ? $sd_data['saswp-rbcc-review-bg-color'] : '#000';
            $rating_head_fcolor = isset( $sd_data['saswp-rbcc-review-f-color'] ) ? $sd_data['saswp-rbcc-review-f-color'] : '#fff';
            $rating_head_fsize = isset( $sd_data['saswp-rbcc-review-f-size'] ) ? $sd_data['saswp-rbcc-review-f-size'] : '15';
            $rating_head_funit = isset($sd_data['saswp-rbcc-review-f-unit'])?$sd_data['saswp-rbcc-review-f-unit']:'px';

            $rating_item_fcolor = isset($sd_data['saswp-rbcc-if-color'])?$sd_data['saswp-rbcc-if-color']:'#000';
            $rating_item_fsize = isset( $sd_data['saswp-rbcc-if-f-size'] ) ? $sd_data['saswp-rbcc-if-f-size'] : '18';
            $rating_item_funit = isset($sd_data['saswp-rbcc-if-f-unit'])?$sd_data['saswp-rbcc-if-f-unit']:'px';

            $overall_rating_fcolor = isset($sd_data['saswp-rbcc-ar-color'])?$sd_data['saswp-rbcc-ar-color']:'#000';
            $overall_rating_fsize = isset( $sd_data['saswp-rbcc-ar-f-size'] ) ? $sd_data['saswp-rbcc-ar-f-size'] : '48';
            $overall_rating_funit = isset($sd_data['saswp-rbcc-ar-f-unit'])?$sd_data['saswp-rbcc-ar-f-unit']:'px'; 

            $stars_fcolor = isset($sd_data['saswp-rbcc-stars-color'])?$sd_data['saswp-rbcc-stars-color']:'#000';
            $stars_fsize = isset( $sd_data['saswp-rbcc-stars-f-size'] ) ? $sd_data['saswp-rbcc-stars-f-size'] : '18';
            $stars_funit = 'px';
            // ob_start();
        ?>
        <div id="saswp-appearance-modal">
            <div class="saswp-rbcc-fields">
                <span id="saswp-appearance-modal-close">&times;</span>
                <h1 style="margin-left: 10px;"><?php echo saswp_t_string('Customize The Design Appearance'); ?></h1>
                <div class="saswp-rbcc-fields-items">
                    <div id="saswp-rbcc-app-wrapper" class="saswp-rbcc-containers">
                        <table class="saswp-rbcc-field-table">
                            <tbody>
                                <tr class="saswp-rbcc-tr-row">
                                    <td class="saswp-rbcc-td-headings"><?php echo saswp_t_string('Rating Heading'); ?></td>
                                    <td class="saswp-rbcc-td-attributes saswp-rbcc-bg-color">
                                        <div><?php echo saswp_t_string('Background Color'); ?></div>
                                        <input type="text" name="sd_data[saswp-rbcc-review-bg-color]" id="saswp-rbcc-review-bg-color" class="saswpforwp-colorpicker" data-alpha-enabled="false"  value="<?php echo esc_attr($rating_head_bgcolor); ?>" data-default-color="#000">
                                    </td>
                                    <td class="saswp-rbcc-td-attributes saswp-rbcc-font-color">
                                        <div><?php echo saswp_t_string('Color'); ?></div>
                                        <input type="text" name="sd_data[saswp-rbcc-review-f-color]" id="saswp-rbcc-review-f-color" class="saswpforwp-colorpicker" data-alpha-enabled="false"  value="<?php echo esc_attr($rating_head_fcolor); ?>" data-default-color="#fff">
                                    </td>
                                    <td class="saswp-rbcc-td-attributes">
                                        <div><?php echo saswp_t_string('Size'); ?></div>
                                        <input type="number" name="sd_data[saswp-rbcc-review-f-size]" id="saswp-rbcc-review-f-size" class="saswp-rbcc-review-input-num" value="<?php echo esc_attr($rating_head_fsize); ?>">
                                        <select name="sd_data[saswp-rbcc-review-f-unit]" id="saswp-rbcc-review-f-unit">
                                        <?php 
                                        $unit_array = array('px', 'pt', '%', 'em');
                                        foreach ($unit_array as $ukey => $uvalue) {
                                            if($uvalue == $rating_head_funit){
                                            ?>
                                                <option value="<?php echo esc_attr($uvalue);?>" selected><?php echo esc_html($uvalue);?></option>    
                                            <?php    
                                            }else{
                                            ?>  
                                                <option value="<?php echo esc_attr($uvalue);?>"><?php echo esc_html($uvalue);?></option>    
                                            <?php    
                                            }
                                        }
                                        ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr class="saswp-rbcc-tr-row">
                                    <td class="saswp-rbcc-td-headings"><?php echo saswp_t_string('Review Item Feature'); ?></td>
                                    <td class="saswp-rbcc-td-attributes saswp-rbcc-dc">
                                        <div><?php echo saswp_t_string('Color'); ?></div>
                                        <input type="text" name="sd_data[saswp-rbcc-if-color]" id="saswp-rbcc-if-color" class="saswpforwp-colorpicker" data-alpha-enabled="false"  value="<?php echo esc_attr($rating_item_fcolor); ?>" data-default-color="#000">
                                    </td>
                                    <td class="saswp-rbcc-td-attributes"> 
                                        <div><?php echo saswp_t_string('Size'); ?></div>
                                        <input type="number" name="sd_data[saswp-rbcc-if-f-size]" id="saswp-rbcc-if-f-size" class="saswp-rbcc-review-input-num" value="<?php echo esc_attr($rating_item_fsize); ?>">
                                        <select name="sd_data[saswp-rbcc-if-f-unit]" id="saswp-rbcc-if-f-unit">
                                            <?php 
                                            $unit_array = array('px', 'pt', '%', 'em');
                                            foreach ($unit_array as $ukey => $uvalue) {
                                                if($uvalue == $rating_item_funit){
                                                ?>
                                                    <option value="<?php echo esc_attr($uvalue);?>" selected><?php echo esc_html($uvalue);?></option>    
                                                <?php    
                                                }else{
                                                ?>  
                                                    <option value="<?php echo esc_attr($uvalue);?>"><?php echo esc_html($uvalue);?></option>    
                                                <?php    
                                                }
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr class="saswp-rbcc-tr-row">
                                    <td class="saswp-rbcc-td-headings"><?php echo saswp_t_string('Review Rating Stars'); ?></td>
                                    <td class="saswp-rbcc-td-attributes saswp-rbcc-dc">
                                        <div><?php echo saswp_t_string('Color'); ?></div>
                                        <input type="text" name="sd_data[saswp-rbcc-stars-color]" id="saswp-rbcc-stars-color" class="saswpforwp-colorpicker" data-alpha-enabled="false"  value="<?php echo isset( $sd_data['saswp-rbcc-stars-color'] ) ? esc_attr( $sd_data['saswp-rbcc-stars-color']) : '#000'; ?>" data-default-color="#000">
                                    </td>
                                    <td class="saswp-rbcc-td-attributes"> 
                                        <div><?php echo saswp_t_string('Size'); ?></div>
                                        <input type="number" name="sd_data[saswp-rbcc-stars-f-size]" id="saswp-rbcc-stars-f-size" class="saswp-rbcc-review-input-num" value="<?php echo isset( $sd_data['saswp-rbcc-stars-f-size'] ) ? esc_attr( $sd_data['saswp-rbcc-stars-f-size']) : '18'; ?>">
                                        <input type="text" name="sd_data[saswp-rbcc-stars-f-unit]" id="saswp-rbcc-stars-f-unit" class="saswp-rbcc-review-input-num" value="px" readonly disabled>
                                    </td>
                                </tr>
                                <tr class="saswp-rbcc-tr-row">
                                    <td class="saswp-rbcc-td-headings"><?php echo saswp_t_string('Average Rating'); ?></td>
                                    <td class="saswp-rbcc-td-attributes saswp-rbcc-dc">
                                        <div><?php echo saswp_t_string('Color'); ?></div>
                                        <input type="text" name="sd_data[saswp-rbcc-ar-color]" id="saswp-rbcc-ar-color" class="saswpforwp-colorpicker" data-alpha-enabled="false"  value="<?php echo esc_attr($overall_rating_fcolor); ?>" data-default-color="#000">
                                    </td>
                                    <td class="saswp-rbcc-td-attributes"> 
                                        <div><?php echo saswp_t_string('Size'); ?></div>
                                        <input type="number" name="sd_data[saswp-rbcc-ar-f-size]" id="saswp-rbcc-ar-f-size" class="saswp-rbcc-review-input-num" value="<?php echo esc_attr($overall_rating_fsize); ?>">
                                        <select name="sd_data[saswp-rbcc-ar-f-unit]" id="saswp-rbcc-ar-f-unit">
                                            <?php 
                                            $unit_array = array('px', 'pt', '%', 'em');
                                            foreach ($unit_array as $ukey => $uvalue) {
                                                if($uvalue == $overall_rating_funit){
                                                ?>
                                                    <option value="<?php echo esc_attr($uvalue);?>" selected><?php echo esc_html($uvalue);?></option>    
                                                <?php    
                                                }else{
                                                ?>  
                                                    <option value="<?php echo esc_attr($uvalue);?>"><?php echo esc_html($uvalue);?></option>    
                                                <?php    
                                                }
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr class="saswp-rbcc-tr-row">
                                    <td colspan="4" class="saswp-rbcc-td-attributes" id="saswp-rbcc-reset"><h4><a href="#"><?php echo saswp_t_string('Reset to Default') ?></a></h4></td>
                                </tr>
                            </tbody>
                        </table>
                    </div> <!-- saswp-rbcc-app-wrapper -->

                    <!-- <h3 id="saswp-rbcc-preview-hd"><?php echo saswp_t_string('Design Preview'); ?></h3> -->
                    <div id="saswp-rbcc-app-preview" class="saswp-rbcc-containers">
                        <?php 
                        $preview_head_style = "background-color: ".$rating_head_bgcolor."; color: ".$rating_head_fcolor."; font-size: ".$rating_head_fsize.$rating_head_funit;
                        $preview_review_item_style = "color: ".$rating_item_fcolor."; font-size: ".$rating_item_fsize.$rating_item_funit; 
                        $preview_overall_rating_style = "color: ".$overall_rating_fcolor."; font-size: ".$overall_rating_fsize.$overall_rating_funit; 
                        ?>
                        <div class="saswp-rbcc-preview-head" style="<?php echo esc_attr($preview_head_style); ?>"><span>Review Overview</span></div>
                            <table id="saswp-rbcc-preview-table" class="saswp-rvw">
                                <tbody>
                                    <?php 
                                    for ($j=1; $j <= 3 ; $j++) { 
                                    ?>    
                                        <tr>
                                            <td class="saswp-rb-rif saswp-rbcc-rif" style="<?php echo esc_attr($preview_review_item_style); ?>"><?php echo saswp_t_string('Demo Review Text'); ?></td>
                                            <td class="saswp-rb-risr">
                                                <div class="saswp-rvw-str">
                                                    <?php 
                                                    for ($i=1; $i <= 5 ; $i++) { 
                                                        $a =  wp_rand(1231,7879).$i; 
                                                        $review_id = wp_rand(2699,4907).$i;
                                                        ?>
                                                        <span class="saswp_star_color"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="<?php echo esc_attr($stars_fsize).esc_attr($stars_funit); ?>" viewBox="0 0 32 32"><defs><linearGradient id="grad<?php echo esc_attr($review_id).''.esc_attr($a); ?>"><stop offset="100%" class="saswp_star" stop-color="<?php echo esc_attr($stars_fcolor); ?>" /><stop offset="100%" stop-color="grey"/></linearGradient></defs><path fill="url(#grad<?php echo esc_attr($review_id).''.esc_attr($a); ?>" d="M20.388,10.918L32,12.118l-8.735,7.749L25.914,31.4l-9.893-6.088L6.127,31.4l2.695-11.533L0,12.118 l11.547-1.2L16.026,0.6L20.388,10.918z"/></svg></span>
                                                    <?php } ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                <tr>
                                    <td class="saswp-rvw-sm saswp-rbcc-rvs">
                                        <span style="<?php echo esc_attr($preview_head_style); ?>">Summary</span>
                                        <div class="rvw-dsc">
                                        
                                        </div>
                                    </td>
                                    <td>
                                        <div class="saswp-rvw-ov">
                                            <div class="saswp-rvw-fs saswp-rbcc-rvar" style="<?php echo esc_attr($preview_overall_rating_style); ?>"><?php echo saswp_t_string('5') ?></div>                                                                        
                                            <div class="saswp-rvw-str">
                                                <?php 
                                                for ($i=1; $i <= 5 ; $i++) { 
                                                    $a =  wp_rand(1231,7879).$i; 
                                                    $review_id = wp_rand(2699,4907).$i;
                                                    ?>
                                                    <span class="saswp_star_color"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="<?php echo esc_attr($stars_fsize).esc_attr($stars_funit); ?>" viewBox="0 0 32 32"><defs><linearGradient id="grad<?php echo esc_attr($review_id).''.esc_attr($a); ?>"><stop offset="100%" class="saswp_star" stop-color="<?php echo esc_attr($stars_fcolor); ?>" /><stop offset="100%" stop-color="grey"/></linearGradient></defs><path fill="url(#grad<?php echo esc_attr($review_id).''.esc_attr($a); ?>" d="M20.388,10.918L32,12.118l-8.735,7.749L25.914,31.4l-9.893-6.088L6.127,31.4l2.695-11.533L0,12.118 l11.547-1.2L16.026,0.6L20.388,10.918z"/></svg></span>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>    
                    </div> <!-- saswp-rbcc-app-preview -->
                </div> 
                <div id="saswp-modal-save-settings" style="margin-left: 10px;">
                    <p class="submit"><input type="submit" name="saswp_settings_save" class="button button-primary" value="Save Settings"></p>
                </div>   
            </div>
        </div>
        <?php
        // return ob_get_clean();
        }  
}
if (class_exists('saswp_rating_box_backend')) {
	new saswp_rating_box_backend;
};