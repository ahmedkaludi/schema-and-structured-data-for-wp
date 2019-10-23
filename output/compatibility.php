<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class saswp_output_compatibility{
    
    public $_plugins_list = array(); 

    public function __construct() {
    
            $mappings_file = SASWP_DIR_NAME . '/core/array-list/compatibility-list.php';

            if ( file_exists( $mappings_file ) ) {
                $plugins_arr = include $mappings_file;
                $this->_plugins_list = $plugins_arr['plugins'];
            }
            
    }
    
    public function saswp_service_compatibility_hooks(){
            
           add_action( 'init', array($this, 'saswp_override_schema_markup'));
           add_filter( 'amp_init', array($this, 'saswp_override_schema_markup'));  
           add_filter( 'wpsso_json_prop_https_schema_org_graph', 'saswp_exclude_wpsso_schema_graph', 10, 5 );            
           add_action("mv_create_modify_card_style_hooks", array($this, 'saswp_remove_create_mediavine'),100,2);          
    }
    
    public function saswp_remove_create_mediavine($attr, $type){
        
           remove_action( 'mv_create_card_before', array( 'Mediavine\Create\Creations_Views_Hooks', 'mv_create_schema' ), 10 );               
        
    } 
    
    public function saswp_exclude_wpsso_schema_graph( $prop_data, $mod, $mt_og, $page_type_id, $is_main ) {
         
        return __return_empty_array;
        
    }

    public function saswp_override_schema_markup(){
        
        global $sd_data;
        
        if(!empty($this->_plugins_list)){
        
            foreach ($this->_plugins_list as $key =>  $plugins){
            
            if(isset($sd_data[$plugins['opt_name']]) && $sd_data[$plugins['opt_name']] == 1){
                
                if(is_plugin_active($plugins['free']) || (isset($plugins['pro']) && is_plugin_active($plugins['pro']))){
                    
                    $func_name = 'saswp_'.$key.'_override';
                    
                    if(method_exists($this, $func_name) && saswp_global_option()){                        
                        call_user_func(array($this, $func_name));                        
                    }
                    
                }
                
            }
            
        }
            
       }
                                   
    }
    
    public function saswp_wp_event_manager_override(){
                        
        if(class_exists('WP_Event_Manager_Post_Types')){
            remove_action( 'wp_footer', array( WP_Event_Manager_Post_Types::instance(), 'output_structured_data' ), 10 ); 
        }
                        
    }
    
    public function saswp_the_events_calendar_override(){
                                
        add_filter('tribe_json_ld_event_data', array($this, 'saswp_remove_the_events_calendar_markup'),10,2);
                                
    }
    public function saswp_remove_the_events_calendar_markup( $data, $args ){
        
        return __return_empty_array;
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
    
    public function saswp_the_seo_framework_override(){        
        
        add_filter('the_seo_framework_receive_json_data', '__return_null');
    }
    public function saswp_squirrly_seo_override(){        
        add_filter('sq_json_ld', '__return_false',99);                
    }
    public function saswp_smart_crawl_override(){        
        add_filter('wds-schema-data', '__return_false');                
    }
    public function saswp_seo_press_hooks(){                
        remove_action('wp_head', 'seopress_social_accounts_jsonld_hook',1);
        remove_action('wp_head', 'seopress_social_website_option',1);                                    
    }    
    public function saswp_seo_press_override(){                             
        add_action('wp_head', array($this, 'saswp_seo_press_hooks'),0);                        
    }    
    public function saswp_woocommerce_override(){
        
        if(class_exists('WooCommerce')){
            
            remove_action( 'wp_footer', array( WC()->structured_data, 'output_structured_data' ), 10 ); // This removes structured data from all frontend pages
            remove_action( 'woocommerce_email_order_details', array( WC()->structured_data, 'output_email_structured_data' ), 30 ); // This removes structured data from all Emails sent by WooCommerce
            
        }
        
    }
        
    public function saswp_remove_yoast_product_schema(){
         
       global $wp_filter;
               
       if(isset($wp_filter['wp_footer']) && is_object($wp_filter['wp_footer'])){
         
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
