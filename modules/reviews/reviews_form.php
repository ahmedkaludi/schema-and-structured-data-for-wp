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
          add_action( 'admin_post_nopriv_saswp_review_form', array($this, 'saswp_save_review_form_data') );          
          add_filter( 'amp_content_sanitizers_template_mode',array($this, 'saswp_review_form_blacklist_sanitizer'), 99);
          add_filter( 'amp_content_sanitizers',array($this, 'saswp_review_form_blacklist_sanitizer'), 99);
          add_action( 'amp_post_template_css', array($this, 'saswp_review_form_amp_css'));
                                 
        }
        
        public function saswp_review_form_blacklist_sanitizer($data){
            
                require_once SASWP_PLUGIN_DIR_PATH .'core/3rd-party/class-amp-review-form-blacklist.php';
            
                unset($data['AMPFORWP_Blacklist_Sanitizer']);
                unset($data['AMP_Blacklist_Sanitizer']);
		$data[ 'AMP_Review_Form_Blacklist' ] = array();
                
                return $data;
            
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
            
            if(isset($_POST['action']) && $_POST['action'] == 'saswp_review_form'){
                    $form_data = $_POST;
            }else{
                    $form_data = $_GET;
            }                     
            if($form_data['action'] == 'saswp_review_form'){                              
                $rv_link   = $form_data['saswp_review_link']; 
            
            if(!wp_verify_nonce($form_data['saswp_review_nonce'], 'saswp_review_form')){
                wp_redirect( $rv_link );
                exit;
            }
            
                $rv_image = '';
                $postarr = array();
                
                if(is_user_logged_in()){
                    
                     $current_user = wp_get_current_user();
                     $postarr['post_author'] = $current_user->ID;
                     $rv_image     = get_avatar_url($current_user->ID, array('size' => 300));                     
                    
                }
                
                $rv_text     = sanitize_textarea_field($form_data['saswp_review_text']);
                $rv_name     = sanitize_text_field($form_data['saswp_reviewer_name']);
                $rv_rating   = intval($form_data['saswp_review_rating']);  
                $rv_place_id = intval($form_data['saswp_place_id']);  
                $rv_date     = date('Y-m-d');
                $rv_time     = date("h:i:sa");
                                
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
                        'saswp_review_location_id'    => $rv_place_id,
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
                        
             wp_redirect( $rv_link );
             exit;
                                
            }
                        
        }
        public function saswp_review_form_amp_css(){
            
             $review_css  =  SASWP_PLUGIN_DIR_PATH . 'admin_section/css/amp/review-form.css';             
             echo @file_get_contents($review_css);
            
        }
        public function saswp_reviews_form_render($attr){
            
            $is_amp = false;
            
            if(!saswp_non_amp()){
                $is_amp = true;
            }
            
            ob_start();
            
            global $post;
            global $wp;
           
            wp_enqueue_script( 'saswp-rateyo-front-js', SASWP_PLUGIN_URL . 'admin_section/js/jquery.rateyo.min.js', array('jquery', 'jquery-ui-core'), SASWP_VERSION , true );                                                                            
            wp_enqueue_script( 'saswp-review-form-js', SASWP_PLUGIN_URL . 'admin_section/js/'.(SASWP_ENVIRONMENT == 'production' ? 'review-form.min.js' : 'review-form.js'), array('jquery', 'jquery-ui-core'), SASWP_VERSION );
            wp_enqueue_style(  'saswp-review-form-css', SASWP_PLUGIN_URL . 'admin_section/css/'.(SASWP_ENVIRONMENT == 'production' ? 'review-form.min.css' : 'review-form.css'), false, SASWP_VERSION );
            
            $form = $current_url = '';
            
            if(is_object($wp)){
                $current_url = home_url( add_query_arg( array(), $wp->request ) );
            }
            
            $form       .= '<div class="saswp-rv-form-container">';
            
            if(!$is_amp){ 
                
                $rating_html = '<div class="saswp-rating-front-div"></div><input type="hidden" name="saswp_review_rating" value="5">';
                $form   .= '<form action="'.esc_url( admin_url('admin-post.php') ).'" method="post">';
                
            }else{
                
                $form   .= '<form action="'.esc_url( admin_url('admin-post.php') ).'" method="get">';
                
                $rating_html = ''
                        . '<input type="hidden" name="saswp_review_rating" [value]="saswp_review_rating">'
                        . '<div class="saswp-rvw-str">'
                        . '<span [class]="saswp_review_rating >= 1 ? \'str-ic\' : \'df-clr\' " class="df-clr" on="tap:AMP.setState({ saswp_review_rating: 1 })" role="button" tabindex="1"></span>'
                        . '<span [class]="saswp_review_rating >= 2 ? \'str-ic\' : \'df-clr\' " class="df-clr" on="tap:AMP.setState({ saswp_review_rating: 2 })" role="button" tabindex="2"></span>'
                        . '<span [class]="saswp_review_rating >= 3 ? \'str-ic\' : \'df-clr\' " class="df-clr" on="tap:AMP.setState({ saswp_review_rating: 3 })" role="button" tabindex="3"></span>'
                        . '<span [class]="saswp_review_rating >= 4 ? \'str-ic\' : \'df-clr\' " class="df-clr" on="tap:AMP.setState({ saswp_review_rating: 4 })" role="button" tabindex="4"></span>'
                        . '<span [class]="saswp_review_rating >= 5 ? \'str-ic\' : \'df-clr\' " class="df-clr" on="tap:AMP.setState({ saswp_review_rating: 5 })" role="button" tabindex="5"></span>'
                        . '</div>';
                                
            }
                    
            $form   .= wp_nonce_field( 'saswp_review_form', 'saswp_review_nonce' )
                    . '<table class="form-table">'
                    . '<tr><td>Name</td> <td><input type="text" name="saswp_reviewer_name" required></td></tr>'
                    . '<tr><td> Text </td><td><textarea name="saswp_review_text"></textarea>'
                    . '<input type="hidden" name="saswp_review_link" value="'.esc_url($current_url).'"></td></tr>'                    
                    . '<tr><td></td><td>'
                    . $rating_html                    
                    . '</td></tr>'
                    . '<tr><td colspan="2">'
                    . '<input type="hidden" name="saswp_place_id" value="'.esc_attr($post->ID).'">'
                    . '<input type="hidden" name="action" value="saswp_review_form">'
                    . '<input name="saswp-review-save" type="submit" class="submit">'
                    . '</td></tr>'                                        
                    . '</table>'
                    . '</form>'
                    . '</div>';
            
             echo $form;
             return ob_get_clean();
            
        }
            
}

if ( class_exists( 'SASWP_Reviews_Form') ) {
	SASWP_Reviews_Form::get_instance();
}