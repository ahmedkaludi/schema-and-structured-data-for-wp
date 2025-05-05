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

        private $_service   = null;        

        private function __construct() {
            
          if($this->_service == null){
              
              $this->_service = new SASWP_Reviews_Service();
              
          }  
                         
          add_shortcode( 'saswp-reviews-form', array($this, 'saswp_reviews_form_render' ));              
          add_action( 'admin_post_saswp_review_form', array($this, 'saswp_save_review_form_data') );
          add_action( 'admin_post_nopriv_saswp_review_form', array($this, 'saswp_save_review_form_data') );  
          
          add_action( 'wp_ajax_saswp_review_form', array($this, 'saswp_save_review_form_data') );
          add_action( 'wp_ajax_nopriv_saswp_review_form', array($this, 'saswp_save_review_form_data') );  
          
          add_filter( 'amp_content_sanitizers_template_mode',array($this, 'saswp_review_form_blacklist_sanitizer'), 99);
          add_filter( 'amp_content_sanitizers',array($this, 'saswp_review_form_blacklist_sanitizer'), 99);          
          
          add_action( 'wp_ajax_saswp_update_google_captch_keys', array($this, 'saswp_update_google_captch_keys') );          
                                 
        }
       
        public function saswp_review_form_blacklist_sanitizer($data){

                global $post;                
                        
                if( function_exists('ampforwp_is_amp_endpoint') && is_object($post) ){

                    if(preg_match( '/\[saswp\-reviews\-form\]/', $post->post_content, $match ) || preg_match( '/\[saswp\-reviews\-form onbutton\=\"1\"\]/', $post->post_content, $match ) ) {

                        if ( ! empty( $match) ) {
                                
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
                
        public function saswp_save_review_form_data() {
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
                    foreach ( $_SERVER as $name => $value) {
                        if (substr($name, 0, 5) == 'HTTP_') {
                            $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                        }
                    }
                    return $headers;
                }
            }  
                        
            // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
            $rv_link   = isset($_SERVER['HTTP_REFERER'])?sanitize_url($_SERVER['HTTP_REFERER']):''; 
            // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: Nonce verification done here so unslash is not used.
            if ( isset( $_POST['saswp_review_nonce'] ) && ! wp_verify_nonce( $_POST['saswp_review_nonce'], 'saswp_review_form' ) ) {
                if($is_amp){
                    header("AMP-Redirect-To: ".$rv_link);
                    header("Access-Control-Expose-Headers: AMP-Redirect-To, AMP-Access-Control-Allow-Source-Origin");                                 
                    echo wp_json_encode(array('message'=> esc_html__( 'Nonce MisMatch', 'schema-and-structured-data-for-wp' )));die;
                }else{
                    wp_safe_redirect( $rv_link );
                    exit; 
                }
                
           }

            $headers = getallheaders();
            $is_amp  = false;
            if ( isset( $headers['AMP-Same-Origin']) ) {
                $is_amp = true;
            }
            $site_key   = get_option('saswp_g_site_key');
            $secret_key = get_option('saswp_g_secret_key');

            if( $site_key != '' && $secret_key != '' ){
                
                $captcha = '';

                // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash --Reason: Nonce verification done here so unslash is not used.
                if ( isset( $_POST['g-recaptcha-response']) ) {
                    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash --Reason: Nonce verification done here so unslash is not used.
                    $captcha = $_POST['g-recaptcha-response'];
                }
                
                if(!$captcha){
                    wp_safe_redirect( $rv_link );
                    exit;
                }
                
                $url          = 'https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($secret_key) .  '&response=' . urlencode($captcha);
                $resultset       = wp_remote_get($url);
                if ( ! is_wp_error( $resultset) ) {
                    $responseKeys = json_decode(wp_remote_retrieve_body($resultset), true);
                    if(!$responseKeys["success"]){
                        wp_safe_redirect( $rv_link );
                        exit;
                    }
                }                                                                

            }
                                    
            // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash --Reason: Nonce verification done here so unslash is not used.
            if ( isset( $_POST['action'] ) && $_POST['action'] == 'saswp_review_form'){
                               
               if($is_amp){
                    
                    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash --Reason: Nonce verification done here so unslash is not used.
                    $http_origin = isset($_SERVER['HTTP_ORIGIN'])?sanitize_text_field($_SERVER['HTTP_ORIGIN']):'';
                    header("access-control-allow-credentials:true");
                    header("access-control-allow-headers:Content-Type, Content-Length, Accept-Encoding, X-CSRF-Token");
                    header("Access-Control-Allow-Origin:".$http_origin);
                    $siteUrl = wp_parse_url(  get_site_url() );
                    header("AMP-Access-Control-Allow-Source-Origin:".$siteUrl['scheme'] . '://' . $siteUrl['host']);        
                    header("Content-Type:application/json;charset=utf-8");
                   
               }
                               
               $response = $this->_service->saswp_review_form_process_data($_POST);
            
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
        public function saswp_review_form_amp_css() {
            
             $review_css  =  SASWP_PLUGIN_DIR_PATH . 'admin_section/css/amp/review-form.css';  
             
             ?>
            .saswp-rvw-str .str-ic{           
                background-image: url(<?php echo esc_url(SASWP_DIR_URI.'/admin_section/images/full_star.png' ); ?>);
            }
            .saswp-rvw-str .df-clr{           
                background-image: url(<?php echo esc_url(SASWP_DIR_URI.'/admin_section/images/blank_star.png' ); ?>);
            }

            <?php
                          
             saswp_local_file_get_contents($review_css);
            
        }
        
        public function saswp_reviews_form_amp_script($data){
                        
             if ( empty( $data['amp_component_scripts']['amp-form'] ) ) {
                     $data['amp_component_scripts']['amp-form'] = "https://cdn.ampproject.org/v0/amp-form-latest.js";
             }
            return $data;
        }
        
        public function saswp_reviews_form_render($attr){
            $attr = saswp_wp_kses_post($attr);
            $on_button = false;
            global $sd_data;
            
            
            
            if ( isset( $attr['onbutton']) ) {
                $on_button = true;
            }
            
            $is_amp = false;
            
            if(!saswp_non_amp() ) {
                $is_amp = true;
            }
            add_action( 'amp_post_template_css', array($this, 'saswp_review_form_amp_css'));
            
            ob_start();
            
            global $post;
            global $wp;
           
            $localize = array(
                'is_rtl'                       => is_rtl()
            );


            wp_register_script( 'saswp-review-form-js', SASWP_PLUGIN_URL . 'admin_section/js/'.(SASWP_ENVIRONMENT == 'production' ? 'review-form.min.js' : 'review-form.js'), array('jquery', 'jquery-ui-core'), SASWP_VERSION, true );

            wp_localize_script( 'saswp-review-form-js', 'saswp_localize_review_data', $localize );
        
            wp_enqueue_script( 'saswp-review-form-js' );

            wp_enqueue_script( 'saswp-rateyo-front-js', SASWP_PLUGIN_URL . 'admin_section/js/jquery.rateyo.min.js', array('jquery', 'jquery-ui-core'), SASWP_VERSION , true );                                                                                        
            wp_enqueue_style(  'saswp-review-form-css', SASWP_PLUGIN_URL . 'admin_section/css/'.(SASWP_ENVIRONMENT == 'production' ? 'review-form.min.css' : 'review-form.css'), false, SASWP_VERSION );
            wp_enqueue_style(  'jquery-rateyo-min-css', SASWP_PLUGIN_URL . 'admin_section/css/'.(SASWP_ENVIRONMENT == 'production' ? 'jquery.rateyo.min.css' : 'jquery.rateyo.min.css'), false, SASWP_VERSION );
            
            if( isset($sd_data['saswp_ar_captcha_checkbox']) && $sd_data['saswp_ar_captcha_checkbox'] == 1 ){
                wp_enqueue_script( 'saswp-recaptcha', 'https://www.google.com/recaptcha/api.js', array(), SASWP_VERSION, true); 
            }

            $form_escaped = $current_url = '';
            
            if(is_object($wp) ) {
                $current_url = home_url( add_query_arg( array(), $wp->request ) );
            }
            
            $form_escaped       .= '<div class="saswp-rv-form-container">';
            
            if(!$is_amp){ 
                
                if($on_button){
                    $form_escaped       .= '<div class="saswp-rv-form-btn"><a href="#" class="button button-default">'.saswp_label_text('translation-review-form').'</a></div>';
                }
                
                $rating_html = '<div class="saswp-rating-container"><div class="saswp-rating-front-div"></div><div class="saswp-rateyo-counter"></div><input type="hidden" name="saswp_review_rating" value="5"></div>';
                $form_escaped   .= '<form action="'. esc_url(  admin_url( 'admin-post.php') ).'" method="post" class="saswp-review-submission-form '.($on_button ? "saswp_hide" : "").'">';
                
            }else{
                
                add_action( 'amp_post_template_data', array($this, 'saswp_reviews_form_amp_script'));  
                
                if($on_button){
                    $form_escaped       .= '<div class="saswp-rv-form-btn"><a href="#" class="button button-default" on="tap:AMP.setState({ saswp_review_form_toggle: !saswp_review_form_toggle })" role="button" tabindex="1">'.saswp_label_text('translation-review-form').'</a></div>';
                }
                
                $form_escaped   .= '<form   action-xhr="'. esc_url(  admin_url( 'admin-post.php') ).'" method="post" class="saswp-review-submission-form '.($on_button ? "saswp_hide" : "").'" [class]="saswp_review_form_toggle ? \'saswp-review-submission-form\' : \'saswp_hide saswp-review-submission-form\' ">';
                
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
            
            $form_escaped   .= wp_nonce_field( 'saswp_review_form', 'saswp_review_nonce' )

                    . '<div class="saswp-form-tbl">'
                    
                    . '<div class="saswp-form-fld">'
                    .   '<span>'.saswp_label_text('translation-name').'</span>'
                    .   '<input type="text" name="saswp_reviewer_name" required>'
                    . '</div>'

                    . '<div class="saswp-form-fld">'
                    .   '<span>'.saswp_label_text('translation-comment').'</span>'
                    .   '<textarea name="saswp_review_text"></textarea>'
                    . '</div>'

                    . '<input type="hidden" name="saswp_review_link" value="'. esc_url( $current_url).'">'
                    . $rating_html
                    . '<input type="hidden" name="saswp_place_id" value="'. esc_attr( $post->ID).'">'
                    . '<input type="hidden" name="action" value="saswp_review_form">';
                    
                    if( isset($sd_data['saswp_ar_captcha_checkbox']) && $sd_data['saswp_ar_captcha_checkbox'] == 1 ) {
                        if((isset($sd_data['saswp_g_site_key']) && !empty($sd_data['saswp_g_site_key'])) && (isset($sd_data['saswp_g_secret_key']) && !empty($sd_data['saswp_g_secret_key'])))
                        $form_escaped.=    '<div class="g-recaptcha" data-sitekey="'. esc_attr( $sd_data['saswp_g_site_key']).'"></div>';
                    }

                    $form_escaped.= '<input name="saswp-review-save" type="submit" class="submit">'                                        
                    . '</div>'
                    . '</form>'
                    . '</div>';
            
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Reason: It is static html and its all dynamic values have been esacped.
             echo $form_escaped;
             return ob_get_clean();
            
        }
        
    /**
     * Save google captcha site key and secret keys
     * @since 1.27
     * */
    public function saswp_update_google_captch_keys()
    {
        if ( ! isset( $_POST['saswp_security_nonce'] ) ){
            return; 
        }
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: Nonce verification done here so unslash is not used.
        if ( !wp_verify_nonce( $_POST['saswp_security_nonce'], 'saswp_ajax_check_nonce' ) ){
           return;  
        } 
        if(!current_user_can( saswp_current_user_can()) ) {
            die( '-1' );    
        }

        if ( ! isset( $_POST['gsitekey']) && !isset($_POST['gsecretkey']) ) {
            $captcha_enable = isset( $_POST['captcha_enable'] ) ? intval( $_POST['captcha_enable'] ) : '';
            $keys['saswp_ar_captcha_checkbox'] = $captcha_enable;

            $get_options   = get_option('sd_data');
            $merge_options = array_merge($get_options, $keys);
            update_option('sd_data', $merge_options);
        }elseif ( isset( $_POST['gsitekey']) && isset($_POST['gsecretkey']) ) {
            $gsitekey = isset( $_POST['gsitekey'] ) ? sanitize_text_field( wp_unslash( $_POST['gsitekey'] ) ) : '';
            $gsecretkey = isset( $_POST['gsecretkey'] ) ? sanitize_text_field( wp_unslash( $_POST['gsecretkey'] ) ) : '';
            $captcha_enable = isset( $_POST['captcha_enable'] ) ? intval( $_POST['captcha_enable'] ) : '';

            $keys['saswp_g_site_key'] = $gsitekey;
            $keys['saswp_g_secret_key'] = $gsecretkey;
            $keys['saswp_ar_captcha_checkbox'] = $captcha_enable;

            $get_options   = get_option('sd_data');
            $merge_options = array_merge($get_options, $keys);
            update_option('sd_data', $merge_options);
        }
        wp_die();
    }    
}

if ( class_exists( 'SASWP_Reviews_Form') ) {
	SASWP_Reviews_Form::get_instance();
}