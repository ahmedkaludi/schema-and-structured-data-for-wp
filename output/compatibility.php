<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class saswp_output_compatibility{
    
    private $_plugins_list = array(
        
        array(
            'key'        => 'kk_star_ratings',
            'name'       => 'kk Star Ratings',
            'path_free'  => 'kk-star-ratings/index.php',            
            'status_key' => 'saswp-kk-star-raring',    
        ),        
        array(
            'key'        => 'wp_post_ratings',
            'name'       => 'WP-PostRatings',
            'path_free'  => 'wp-postratings/wp-postratings.php',            
            'status_key' => 'saswp-wppostratings-raring',    
        ),
        array(
            'key'        => 'bb_press',
            'name'       => 'bbPress',
            'path_free'  => 'bbpress/bbpress.php',            
            'status_key' => 'saswp-bbpress',    
        ),
        array(
            'key'        => 'woocommerce',
            'name'       => 'Woocommerce',
            'path_free'  => 'woocommerce/woocommerce.php',            
            'status_key' => 'saswp-woocommerce',    
        ),
        array(
            'key'        => 'cooked',
            'name'       => 'Cooked',
            'path_free'  => 'cooked/cooked.php',  
            'path_pro'   => 'cooked-pro/cooked-pro.php',  
            'status_key' => 'saswp-cooked',    
        ),
        array(
            'key'        => 'the_events_calendar',
            'name'       => 'The Events Calendar',
            'path_free'  => 'the-events-calendar/the-events-calendar.php',            
            'status_key' => 'saswp-the-events-calendar',    
        ),                
        array(
            'key'        => 'dw_qna',
            'name'       => 'DW Question Answer',
            'path_free'  => 'dw-question-answer/dw-question-answer.php',
            'path_pro'   => 'dw-question-answer-pro/dw-question-answer.php',
            'status_key' => 'saswp-dw-question-answer',    
        ),
        array(
            'key'        => 'yoast_seo',
            'name'       => 'Yoast Seo',
            'path_free'  => 'wordpress-seo/wp-seo.php',
            'path_pro'   => 'wordpress-seo-premium/wp-seo-premium.php',
            'status_key' => 'saswp-yoast',    
        ),
        array(
            'key'        => 'rank_math',
            'name'       => 'Rank Math',
            'path_free'  => 'seo-by-rank-math/rank-math.php',   
            'path_pro'   => 'seo-by-rank-math-premium/rank-math-premium.php',   
            'status_key' => 'saswp-rankmath',    
        ),
        array(
            'key'        => 'smart_crawl',
            'name'       => 'SmartCrawl Seo',
            'path_free'  => 'smartcrawl-seo/wpmu-dev-seo.php',               
            'status_key' => 'saswp-smart-crawl',    
        ),
                
    );

    public function __construct() {
    
           
            
    }
    
    public function saswp_service_compatibility_hooks(){
            
           add_action( 'init', array($this, 'saswp_override_schema_markup'));
           add_filter( 'amp_init', array($this, 'saswp_override_schema_markup'));           
           
    }

    public function saswp_override_schema_markup(){
        
        global $sd_data;
            
        foreach ($this->_plugins_list as $plugins){
            
            if(isset($sd_data[$plugins['status_key']]) && $sd_data[$plugins['status_key']] == 1){
                
                if(is_plugin_active($plugins['path_free']) || (isset($plugins['path_pro']) && is_plugin_active($plugins['path_pro']))){
                    
                    $func_name = 'saswp_'.$plugins['key'].'_override';
                    
                    if(method_exists($this, $func_name) && saswp_global_option()){                        
                        call_user_func(array($this, $func_name));                        
                    }
                    
                }
                
            }
            
        }
                   
    }
    
    public function saswp_wp_post_ratings_override(){
        
        add_filter('wp_postratings_schema_itemtype', '__return_false');
        add_filter('wp_postratings_google_structured_data', '__return_false');
                
    }
    
    public function saswp_rank_math_override(){        
        add_action( 'rank_math/json_ld', array($this, 'saswp_remove_rank_math_schema'),99 );                
    }
    
    public function saswp_yoast_seo_override(){        
        add_filter('wpseo_json_ld_output', '__return_false');         
        $this->saswp_remove_yoast_product_schema();                
    }
    
    public function saswp_smart_crawl_override(){        
        add_filter('wds-schema-data', '__return_false');                
    }
    public function saswp_woocommerce_override(){
        
        if(class_exists('WooCommerce')){
            
            remove_action( 'wp_footer', array( WC()->structured_data, 'output_structured_data' ), 10 ); // This removes structured data from all frontend pages
            remove_action( 'woocommerce_email_order_details', array( WC()->structured_data, 'output_email_structured_data' ), 30 ); // This removes structured data from all Emails sent by WooCommerce
            
        }
        
    }
        
    public function saswp_remove_yoast_product_schema(){
         
       global $wp_filter;
               
       if(isset($wp_filter['wp_footer'])){
         
        $callbacks =  $wp_filter['wp_footer']->callbacks;
        
        if(is_array($callbacks)){
        
            foreach($callbacks as $key=>$actions){
                
            if(is_array($actions)){
            
                foreach ($actions as $actualKey => $priorities){
                
                    if(is_array($priorities['function'])){
                    
                        if(is_object($priorities['function'][0])){
                        
                            if ($priorities['function'][0] instanceof WPSEO_WooCommerce_Schema && $priorities['function'][1] == 'output_schema_footer') {
                                 unset($wp_filter['wp_footer']->callbacks[$key][$actualKey]);
                            }
                            
                        }
                                                                        
                    }
                                                                                
                }
                
            }    
                                           
          }
            
        }   
        
        
      }                       

    }
                    
    public function saswp_remove_rank_math_schema($entry){
        return array();  
    }
}

if(class_exists('saswp_output_compatibility')){
   $obj_compatibility =  new saswp_output_compatibility();
   $obj_compatibility->saswp_service_compatibility_hooks();
}
