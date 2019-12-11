<?php
/**
 * Reviews Form  Class
 *
 * @author   Magazine3
 * @category Admin
 * @path     reviews/reviews_form
 * @Version 1.9.18
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class SASWP_Reviews_Form {
        
        /**
         * Static private variable to hold instance this class
         * @var type 
         */
        private static $instance;
        private $_service = null;

        private function __construct() {
            
          if($this->_service == null){
              
              $this->_service = new saswp_reviews_service();
              
          }  
                                                     
          add_shortcode( 'saswp-reviews-form', array($this, 'saswp_reviews_form_render' ));
          add_action( 'admin_post_saswp_review_form', array($this, 'saswp_save_review_form_data') );
                                 
        }
        
         /**
         * Return the unique instance 
         * @return type instance
         * @since version 1.9.18
         */
        public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
        }
        
        public function saswp_save_review_form_data(){
            
            $rv_link   = $_POST['saswp_review_link']; 
            
            if(!wp_verify_nonce($_POST['saswp_review_nonce'], 'saswp_review_form')){
                wp_redirect( $rv_link );
                exit;
            }
            
            if($_POST['action'] == 'saswp_review_form'){
               
                $rv_image = '';
                $postarr = array();
                
                if(is_user_logged_in()){
                    
                     $current_user = wp_get_current_user();
                     $postarr['post_author'] = $current_user->ID;
                     $rv_image     = get_avatar_url($current_user->ID, array('size' => 300));                     
                    
                }
                
                $rv_text   = sanitize_textarea_field($_POST['saswp_review_text']);
                $rv_name   = sanitize_text_field($_POST['saswp_reviewer_name']);
                $rv_rating = intval($_POST['saswp_review_rating']);                             
                $rv_date   = date('Y-m-d');
                $rv_time   = date("h:i:sa");
                                
                if($rv_rating){
                    
                    $postarr = array(                                                                           
                    'post_title'            => $rv_name,                    
                    'post_status'           => 'pending',                                                            
                    'post_name'             => $rv_name,                                                            
                    'post_type'             => 'saswp_reviews',
                                                                             
                );
                                        
                $post_id = wp_insert_post(  $postarr );    
                    
                $term     = get_term_by( 'slug','google', 'platform' );   
                
                if($rv_image){
                    
                    $image_details = saswp_get_attachment_details($rv_image);   
                    
                    $media_detail = array(                                                    
                        'width'      => $image_details[0][0],
                        'height'     => $image_details[0][1],
                        'thumbnail'  => $rv_image,
                    );
                    
                }
                
                $review_meta = array(
                        'saswp_review_platform'       => $term->term_id,
                        'saswp_review_location_id'    => null,
                        'saswp_review_time'           => $rv_time,
                        'saswp_review_date'           => $rv_date,
                        'saswp_review_rating'         => $rv_rating,
                        'saswp_review_text'           => $rv_text,                                
                        'saswp_reviewer_lang'         => null,
                        'saswp_reviewer_name'         => $rv_name,
                        'saswp_review_link'           => $rv_link,
                        'saswp_reviewer_image'        => $rv_image ? $rv_image : SASWP_DIR_URI.'/admin_section/images/default_user.jpg',
                        'saswp_reviewer_image_detail' => $media_detail
                );
                                   
                if($post_id && !empty($review_meta) && is_array($review_meta)){
                                        
                    foreach ($review_meta as $key => $val){                     
                        update_post_meta($post_id, $key, $val);  
                    }
            
                 }
                    
                }
                                
            }
            
             wp_redirect( $rv_link );
             exit;
                        
        }
        public function saswp_reviews_form_render($attr){
               
            $data = array();
            
            wp_register_script( 'saswp-rateyo-front-js', SASWP_PLUGIN_URL . 'admin_section/js/jquery.rateyo.min.js', array('jquery', 'jquery-ui-core'), SASWP_VERSION , true );                                        
            wp_localize_script( 'saswp-rateyo-front-js', 'saswp_reviews_front_data', $data );
            wp_enqueue_script( 'saswp-rateyo-front-js' );
            
            wp_enqueue_script( 'saswp-review-form', SASWP_PLUGIN_URL . 'admin_section/js/'.(SASWP_ENVIRONMENT == 'production' ? 'form.min.js' : 'form.js'), false, SASWP_VERSION );
            
            $form = '';
            //echo '<script async custom-element="amp-form" src="https://cdn.ampproject.org/v0/amp-form-0.1.js"></script>';
            global $wp;
            
            $current_url = home_url( add_query_arg( array(), $wp->request ) );
                
            $form   .= '<div class="saswp-rv-form-container">'
                    . '<form action="'.esc_url( admin_url('admin-post.php') ).'" method="post">'
                    . wp_nonce_field( 'saswp_review_form', 'saswp_review_nonce' )
                    . '<table class="form-table">'
                    . '<tr><td>Name</td> <td><input type="text" name="saswp_reviewer_name" required></td></tr>'
                    . '<tr><td> Text </td><td><textarea name="saswp_review_text"></textarea>'
                    . '<input type="hidden" name="saswp_review_link" value="'.esc_url($current_url).'"></td></tr>'                    
                    . '<tr><td></td><td><div class="saswp-rating-front-div"></div>'
                    . '<input type="hidden" name="saswp_review_rating" value="5"></td></tr>'
                    . '<tr><td colspan="2"><input name="saswp-review-save" type="submit" class="submit">'
                    . '<input type="hidden" name="action" value="saswp_review_form"></td></tr>'                                        
                    . '</table>'
                    . '</form>'
                    . '</div>';
            
            return $form;
            
        }
            
}

if ( class_exists( 'SASWP_Reviews_Form') ) {
	SASWP_Reviews_Form::get_instance();
}