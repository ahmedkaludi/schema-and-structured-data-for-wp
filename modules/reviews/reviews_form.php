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
          
          add_action( 'wp_ajax_saswp_review_form', array($this, 'saswp_save_review_form_data') );
          add_action( 'wp_ajax_nopriv_saswp_review_form', array($this, 'saswp_save_review_form_data') );  
          
          add_filter( 'amp_content_sanitizers_template_mode',array($this, 'saswp_review_form_blacklist_sanitizer'), 99);
          add_filter( 'amp_content_sanitizers',array($this, 'saswp_review_form_blacklist_sanitizer'), 99);          
                                 
        }
       
        public function saswp_review_form_blacklist_sanitizer($data){

                global $post;                
                        
                if( function_exists('ampforwp_is_amp_endpoint') && is_object($post) ){

                    if(preg_match( '/\[saswp\-reviews\-form\]/', $post->post_content, $match ) || preg_match( '/\[saswp\-reviews\-form onbutton\=\"1\"\]/', $post->post_content, $match )){

                        if(!empty($match)){
                                
                            require_once SASWP_PLUGIN_DIR_PATH .'core/3rd-party/class-amp-review-form-blacklist.php';            
                            unset($data['AMPFORWP_Blacklist_Sanitizer']);
                            unset($data['AMP_Blacklist_Sanitizer']);
                            $data[ 'AMP_Review_Form_Blacklist' ] = array();

                        }
                        
                    }                                        
                    
                }
                                
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
            /**
             * getallheaders() is supported only in Apache Web Server
             * and not in other popular web servers like NGINX.
             * 
             * Create the function if not already present, following the code
             * in https://www.php.net/manual/en/function.getallheaders.php#84262
             */
            if (!function_exists('getallheaders')) {
                function getallheaders()
                {
                    $headers = [];
                    foreach ($_SERVER as $name => $value) {
                        if (substr($name, 0, 5) == 'HTTP_') {
                            $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                        }
                    }
                    return $headers;
                }
            }  
            
            $form_data = $_POST;                 
            $headers = getallheaders();
            $is_amp  = false;
            $rv_link   = $form_data['saswp_review_link'];
            if(isset($headers['AMP-Same-Origin'])){
                $is_amp = true;
            }
            
            if($form_data['action'] == 'saswp_review_form'){
                
                if(!wp_verify_nonce($form_data['saswp_review_nonce'], 'saswp_review_form')){
                    
                    if($is_amp){
                        header("AMP-Redirect-To: ".$rv_link);
                        header("Access-Control-Expose-Headers: AMP-Redirect-To, AMP-Access-Control-Allow-Source-Origin");                                 
                        echo json_decode(array('message'=> 'Nonce MisMatch'));die;
                    }else{
                        wp_redirect( $rv_link );
                        exit; 
                    }
                    
               }
                               
               if($is_amp){
                   
                    header("access-control-allow-credentials:true");
                    header("access-control-allow-headers:Content-Type, Content-Length, Accept-Encoding, X-CSRF-Token");
                    header("Access-Control-Allow-Origin:".$_SERVER['HTTP_ORIGIN']);
                    $siteUrl = parse_url(  get_site_url() );
                    header("AMP-Access-Control-Allow-Source-Origin:".$siteUrl['scheme'] . '://' . $siteUrl['host']);        
                    header("Content-Type:application/json;charset=utf-8");
                   
               }
                               
               $response = $this->_service->saswp_review_form_process_data($form_data);
            
                if($response){
                    
                    if($is_amp){
                        header("AMP-Redirect-To: ".$rv_link);
                        header("Access-Control-Expose-Headers: AMP-Redirect-To, AMP-Access-Control-Allow-Source-Origin");                                 
                    }else{                        
                        wp_redirect( $rv_link );
                        exit;
                    }                                        
                }                                                      
                                
            }  
            
            if($is_amp){
                 wp_die();     
            }
            
        }
        public function saswp_review_form_amp_css(){
            
             $review_css  =  SASWP_PLUGIN_DIR_PATH . 'admin_section/css/amp/review-form.css';  
             
             ?>
            .saswp-rvw-str .str-ic{           
                background-image: url(<?php echo esc_url(SASWP_DIR_URI.'/admin_section/images/full_star.png'); ?>);
            }
            .saswp-rvw-str .df-clr{           
                background-image: url(<?php echo esc_url(SASWP_DIR_URI.'/admin_section/images/blank_star.png'); ?>);
            }

            <?php
                          
             echo @file_get_contents($review_css);
            
        }
        
        public function saswp_reviews_form_amp_script($data){
                        
             if ( empty( $data['amp_component_scripts']['amp-form'] ) ) {
                     $data['amp_component_scripts']['amp-form'] = "https://cdn.ampproject.org/v0/amp-form-latest.js";
             }
            return $data;
        }
        
        public function saswp_reviews_form_render($attr){
            
            $on_button = false;
            
            if(isset($attr['onbutton'])){
                $on_button = true;
            }
            
            $is_amp = false;
            
            if(!saswp_non_amp()){
                $is_amp = true;
            }
            add_action( 'amp_post_template_css', array($this, 'saswp_review_form_amp_css'));
            
            ob_start();
            
            global $post;
            global $wp;
           
            wp_enqueue_script( 'saswp-rateyo-front-js', SASWP_PLUGIN_URL . 'admin_section/js/jquery.rateyo.min.js', array('jquery', 'jquery-ui-core'), SASWP_VERSION , true );                                                                            
            wp_enqueue_script( 'saswp-review-form-js', SASWP_PLUGIN_URL . 'admin_section/js/'.(SASWP_ENVIRONMENT == 'production' ? 'review-form.min.js' : 'review-form.js'), array('jquery', 'jquery-ui-core'), SASWP_VERSION );
            wp_enqueue_style(  'saswp-review-form-css', SASWP_PLUGIN_URL . 'admin_section/css/'.(SASWP_ENVIRONMENT == 'production' ? 'review-form.min.css' : 'review-form.css'), false, SASWP_VERSION );
            wp_enqueue_style(  'jquery-rateyo-min-css', SASWP_PLUGIN_URL . 'admin_section/css/'.(SASWP_ENVIRONMENT == 'production' ? 'jquery.rateyo.min.css' : 'jquery.rateyo.min.css'), false, SASWP_VERSION );
            
            $form = $current_url = '';
            
            if(is_object($wp)){
                $current_url = home_url( add_query_arg( array(), $wp->request ) );
            }
            
            $form       .= '<div class="saswp-rv-form-container">';
            
            if(!$is_amp){ 
                
                if($on_button){
                    $form       .= '<div class="saswp-rv-form-btn"><a href="#" class="button button-default">'.saswp_label_text('translation-review-form').'</a></div>';
                }
                
                $rating_html = '<div class="saswp-rating-front-div"></div><input type="hidden" name="saswp_review_rating" value="5">';
                $form   .= '<form action="'.esc_url( admin_url('admin-post.php') ).'" method="post" class="saswp-review-submission-form '.($on_button ? "saswp_hide" : "").'">';
                
            }else{
                
                add_action( 'amp_post_template_data', array($this, 'saswp_reviews_form_amp_script'));  
                
                if($on_button){
                    $form       .= '<div class="saswp-rv-form-btn"><span href="#" class="button button-default" on="tap:AMP.setState({ saswp_review_form_toggle: !saswp_review_form_toggle })" role="button" tabindex="1">'.saswp_label_text('translation-review-form').'</span></div>';
                }
                
                $form   .= '<form   action-xhr="'.esc_url( admin_url('admin-post.php') ).'" method="post" class="saswp-review-submission-form '.($on_button ? "saswp_hide" : "").'" [class]="saswp_review_form_toggle ? \'saswp-review-submission-form\' : \'saswp_hide saswp-review-submission-form\' ">';
                
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

                    . '<div class="saswp-form-tbl">'
                    
                    . '<div class="saswp-form-fld">'
                    .   '<span>'.saswp_label_text('translation-name').'</span>'
                    .   '<input type="text" name="saswp_reviewer_name" required>'
                    . '</div>'

                    . '<div class="saswp-form-fld">'
                    .   '<span>'.saswp_label_text('translation-comment').'</span>'
                    .   '<textarea name="saswp_review_text"></textarea>'
                    . '</div>'

                    . '<input type="hidden" name="saswp_review_link" value="'.esc_url($current_url).'">'
                    . $rating_html
                    . '<input type="hidden" name="saswp_place_id" value="'.esc_attr($post->ID).'">'
                    . '<input type="hidden" name="action" value="saswp_review_form">'
                    . '<input name="saswp-review-save" type="submit" class="submit">'                                        
                    . '</div>'
                    . '</form>'
                    . '</div>';
            
            
             echo $form;
             return ob_get_clean();
            
        }
            
}

if ( class_exists( 'SASWP_Reviews_Form') ) {
	SASWP_Reviews_Form::get_instance();
}