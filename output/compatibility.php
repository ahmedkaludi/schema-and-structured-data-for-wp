<?php
/**
 * Output Page
 *
 * @author   Magazine3
 * @category Frontend
 * @path  output/compatibility
 * @version 1.9.0
 */
if ( ! defined( 'ABSPATH' ) ) exit;

class saswp_output_compatibility{
    
    public $_plugins_list = array(); 
    public $_theme_list   = array();
    
    public function __construct() {
                        
            $mappings_file = SASWP_DIR_NAME . '/core/array-list/compatibility-list.php';

            if ( file_exists( $mappings_file ) ) {
                
                $plugins_arr = include $mappings_file;
                $this->_plugins_list = $plugins_arr['plugins'];
                $this->_theme_list   = $plugins_arr['themes'];
                
                foreach($plugins_arr['plugins'] as $key => $plugin){

                register_activation_hook( WP_PLUGIN_DIR.'/'.$plugin['free'], array($this, $key.'_on_activation') );

                if(isset($plugin['pro'])){
                    register_activation_hook( WP_PLUGIN_DIR.'/'.$plugin['pro'], array($this, $key.'_on_activation') );
                }

               }
            
            }
            
    }
    
    public function saswp_service_compatibility_hooks(){
            
           add_action( 'init', array($this, 'saswp_override_schema_markup'));
           add_filter( 'amp_init', array($this, 'saswp_override_schema_markup'));  
           add_filter( 'wpsso_json_prop_https_schema_org_graph', array($this ,'saswp_exclude_wpsso_schema_graph'), 10, 5 );            
           add_action( 'mv_create_modify_card_style_hooks', array($this, 'saswp_remove_create_mediavine'),100,2);          
    }
    
    public function saswp_remove_create_mediavine($attr, $type){
        
           remove_action( 'mv_create_card_before', array( 'Mediavine\Create\Creations_Views_Hooks', 'mv_create_schema' ), 10 );               
        
    } 
    
    public function saswp_exclude_wpsso_schema_graph( $prop_data, $mod, $mt_og, $page_type_id=false, $is_main=false ) {
         
        return array();
        
    }

    public function saswp_override_schema_markup(){
        
        if(!is_admin() && saswp_global_option()){

        global $sd_data;        

        if(!empty($this->_plugins_list)){
        
            foreach ($this->_plugins_list as $key =>  $plugins){
            
            if(isset($sd_data[$plugins['opt_name']]) && $sd_data[$plugins['opt_name']] == 1){
                
                if(is_plugin_active($plugins['free']) || (isset($plugins['pro']) && is_plugin_active($plugins['pro']))){
                    
                    $func_name = 'saswp_'.$key.'_override';
                    
                    if(method_exists($this, $func_name)){                        
                        call_user_func(array($this, $func_name));                        
                    }
                    
                }
                
            }
            
        }
            
       }
       //Theme starts here
       
       if(!empty($this->_theme_list)){
        
            foreach ($this->_theme_list as $key =>  $plugins){
            
            if(isset($sd_data[$plugins['opt_name']]) && $sd_data[$plugins['opt_name']] == 1){
                
                if(get_template() == $plugins['free'] || (isset($plugins['pro']) && get_template() ==$plugins['pro'])){
                    
                    $func_name = 'saswp_'.$key.'_override';
                    
                    if(method_exists($this, $func_name)){                        
                        call_user_func(array($this, $func_name));                        
                    }
                    
                }
                
            }
            
        }
            
       }       
       // Theme ends here
      }        
                                   
    }        
    
    public function saswp_starsrating_override(){            
       update_option('google_search_stars', 'hide');
    }

    public function saswp_wpjobopenings_override(){      
        add_filter( 'awsm_job_structured_data', '__return_false');                  
    }
    public function saswp_wpjobmanager_override(){      
        add_filter( 'wpjm_output_job_listing_structured_data', '__return_false');             
    }    
    public function saswp_soledad_override(){
            
         saswp_remove_anonymous_object_filter_or_action(
            'wp_head',
            'Penci_JSON_Schema_Validator',
            'output_schema',
            'action'    
        );
        
    }
    public function saswp_classipress_override(){
        add_filter( 'appthemes_schema_output', '__return_false');                
   }
    public function saswp_ampwp_override(){                 
        add_action( 'template_redirect', array($this, 'saswp_ampwp_override_full'),99);                
    }
    public function saswp_ampwp_override_full(){            
        remove_action( 'amp_wp_template_head', 'Amp_WP_Json_Ld_Generator::print_output' );
    }
    public function saswp_wpamp_override(){        
        add_action('amphtml_template_head', array($this, 'saswp_wpamp_override_full'));        
    }
    public function saswp_wpamp_override_full($data){        
        $data->metadata = array();
    }    
    public function saswp_betteramp_override(){
        
        add_action( 'template_redirect', array($this, 'saswp_betteramp_override_full') ,99);
        
    }
    public function saswp_faqschemaforpost_override(){                
    }
    public function saswp_betteramp_override_full(){
        
             remove_action( 'wp_head', 'BF_Json_LD_Generator::print_output' );
             remove_action( 'better-amp/template/head', 'BF_Json_LD_Generator::print_output' );       
             
    }       
    public function saswp_easy_testimonials_override(){
                        
        add_filter('easy_testimonials_json_ld', '__return_false'); 
                        
    }
    public function saswp_all_in_one_event_calendar_override(){                                                        
    }
    public function saswp_stachethemes_event_calendar_override(){                                                        
    }
    public function saswp_wptastyrecipe_override(){ 
        remove_action( 'wp_head', array( 'Tasty_Recipes\Distribution_Metadata', 'action_wp_head_google_schema' ) );
    }    
    public function saswp_wordpress_news_override(){                                                        
    }
    public function saswp_total_recipe_generator_override(){                                                        
    }
    public function saswp_wp_customer_reviews_override(){                                                        
    }
    public function saswp_yet_another_stars_rating_override(){    
        remove_filter('the_content', 'yasr_add_schema');                                                    
    }
    public function saswp_testimonial_pro_override(){
      
                $args = array(                   
                    'post_type' => 'sp_tpro_shortcodes'
                  );                
                $my_posts = new WP_Query($args);
                  
                if ( $my_posts->have_posts() ) {
                    
                  while ( $my_posts->have_posts() ) : $my_posts->the_post();                 
                  $shortcode_opt['tpro_schema_markup'] = '';
                  update_post_meta(get_the_id(), 'sp_tpro_shortcode_options',$shortcode_opt);                    
                  endwhile;
                  
                  wp_reset_postdata();
                  
            }
                
    }
    
    public function saswp_taqyeem_override(){         
        remove_filter('tie_taqyeem_after_review_box', 'taqyeem_review_rich_snippet');
    }

    public function saswp_wp_product_review_override(){

        $wppr_rich     = get_option('cwppos_options');
        $wppr_rich['wppr_rich_snippet'] = 'no';
        update_option('cwppos_options', $wppr_rich); 
        
    }

    public function saswp_kk_star_ratings_override(){
                        
        remove_action('wp_head', 'Bhittani\StarRating\structured_data');
                        
    }
    
    public function saswp_geodirectory_override(){                
        remove_action( 'wp_head', array( 'GeoDir_Post_Data', 'schema' ), 10 );                         
    }
    
    public function saswp_woo_event_manager_override(){
                                
        remove_action('wp_head', 'mep_event_rich_text_data');
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
        
        return array();
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
    public function saswp_webstories_override(){        
        add_filter('web_stories_enable_schemaorg_metadata', '__return_false');   
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
    
    /**
     * Functions on compatiblity plugin activation starts here
     */
    public function flex_mls_idx_on_activation(){        
        $this->saswp_update_option_on_compatibility_activation('saswp-flexmlx-compativility');                
    }
    public function simple_author_box_on_activation(){
        $this->saswp_update_option_on_compatibility_activation('saswp-simple-author-box');
    }
    public function kk_star_ratings_on_activation(){
        $this->saswp_update_option_on_compatibility_activation('saswp-kk-star-raring');
    }
    public function rmprating_on_activation(){
        $this->saswp_update_option_on_compatibility_activation('saswp-rmprating');
    }
    public function elementor_on_activation(){
        $this->saswp_update_option_on_compatibility_activation('saswp-elementor');
    }
    public function ratingform_on_activation(){
        $this->saswp_update_option_on_compatibility_activation('saswp-ratingform');
    }
    public function wpdiscuz_on_activation(){
        $this->saswp_update_option_on_compatibility_activation('saswp-wpdiscuz');
    }
    public function easy_testimonials_on_activation(){
         $this->saswp_update_option_on_compatibility_activation('saswp-easy-testimonials');
    }
    public function testimonial_pro_on_activation(){
         $this->saswp_update_option_on_compatibility_activation('saswp-testimonial-pro');
    }
    public function bne_testimonials_on_activation(){
         $this->saswp_update_option_on_compatibility_activation('saswp-bne-testimonials');
    }
    public function learn_press_on_activation(){
         $this->saswp_update_option_on_compatibility_activation('saswp-learn-press');
    }
    public function learn_dash_on_activation(){
         $this->saswp_update_option_on_compatibility_activation('saswp-learn-dash');
    }
    public function lifter_lms_on_activation(){
         $this->saswp_update_option_on_compatibility_activation('saswp-lifter-lms');
    }
    public function senseilms_on_activation(){
        $this->saswp_update_option_on_compatibility_activation('saswp-senseilms');
    }
    public function wp_post_ratings_on_activation(){
        $this->saswp_update_option_on_compatibility_activation('saswp-wppostratings-raring');
    }
    public function bb_press_on_activation(){
         $this->saswp_update_option_on_compatibility_activation('saswp-bbpress');
    }
    public function woocommerce_on_activation(){            
        $this->saswp_update_option_on_compatibility_activation('saswp-woocommerce');                
    }
    public function woocommerce_bookings_on_activation(){
         $this->saswp_update_option_on_compatibility_activation('saswp-woocommerce-booking');
    }
    public function woocommerce_membership_on_activation(){
         $this->saswp_update_option_on_compatibility_activation('saswp-woocommerce-membership');
    }
    public function cooked_on_activation(){
         $this->saswp_update_option_on_compatibility_activation('saswp-cooked');
    }
    public function all_in_one_event_calendar_on_activation(){
        $this->saswp_update_option_on_compatibility_activation('saswp-all-in-one-event-calendar');
    }
    public function xo_event_calendar_on_activation(){
        $this->saswp_update_option_on_compatibility_activation('saswp-xo-event-calendar');
    }
    public function calendarize_it_on_activation(){
        $this->saswp_update_option_on_compatibility_activation('saswp-calendarize-it');
    }
    public function events_schedule_on_activation(){
        $this->saswp_update_option_on_compatibility_activation('saswp-events-schedule');
    }
    public function woo_event_manager_on_activation(){
        $this->saswp_update_option_on_compatibility_activation('saswp-woo-event-manager');
    }
    public function vs_event_list_on_activation(){
        $this->saswp_update_option_on_compatibility_activation('saswp-vs-event-list');
    }
    public function the_events_calendar_on_activation(){
         $this->saswp_update_option_on_compatibility_activation('saswp-the-events-calendar');
    }
    public function event_organiser_on_activation(){
         $this->saswp_update_option_on_compatibility_activation('saswp-event-organiser');
    }
    public function modern_events_calendar_on_activation(){
         $this->saswp_update_option_on_compatibility_activation('saswp-modern-events-calendar');
    }
    public function wp_event_manager_on_activation(){
         $this->saswp_update_option_on_compatibility_activation('saswp-wp-event-manager');
    }
    public function events_manager_on_activation(){
         $this->saswp_update_option_on_compatibility_activation('saswp-events-manager');
    }
    public function event_calendar_wd_on_activation(){
         $this->saswp_update_option_on_compatibility_activation('saswp-event-calendar-wd');
    }
    public function sabaidiscuss_on_activation(){
        $this->saswp_update_option_on_compatibility_activation('saswp-sabaidiscuss');
    }
    public function dw_qna_on_activation(){
         $this->saswp_update_option_on_compatibility_activation('saswp-dw-question-answer');
    }
    public function wpqa_on_activation(){
        $this->saswp_update_option_on_compatibility_activation('saswp-wpqa');
    }
    public function brb_on_activation(){
        $this->saswp_update_option_on_compatibility_activation('saswp-brb');
    }
    public function yoast_seo_on_activation(){
         $this->saswp_update_option_on_compatibility_activation('saswp-yoast');
    }
    public function metatagmanager_on_activation(){
        $this->saswp_update_option_on_compatibility_activation('saswp-metatagmanager');
    }
    public function slimseo_on_activation(){
        $this->saswp_update_option_on_compatibility_activation('saswp-slimseo');
    }
    public function rank_math_on_activation(){
         $this->saswp_update_option_on_compatibility_activation('saswp-rankmath');
    }
    public function smart_crawl_on_activation(){
         $this->saswp_update_option_on_compatibility_activation('saswp-smart-crawl');
    }
    public function the_seo_framework_on_activation(){
         $this->saswp_update_option_on_compatibility_activation('saswp-the-seo-framework');
    }
    public function seo_press_on_activation(){
         $this->saswp_update_option_on_compatibility_activation('saswp-seo-press');
    }
    public function aiosp_on_activation(){
         $this->saswp_update_option_on_compatibility_activation('saswp-aiosp');
    }
    public function taqyeem_on_activation(){
        $this->saswp_update_option_on_compatibility_activation('saswp-taqyeem');
    }
    public function wp_product_review_on_activation(){
        $this->saswp_update_option_on_compatibility_activation('saswp-wp-product-review');
    }
    public function stamped_on_activation(){
        $this->saswp_update_option_on_compatibility_activation('saswp-stamped');
    }
    public function squirrly_seo_on_activation(){
         $this->saswp_update_option_on_compatibility_activation('saswp-squirrly-seo');
    }
    public function starsrating_on_activation(){
        $this->saswp_update_option_on_compatibility_activation('saswp-starsrating');
    }
    public function wp_recipe_maker_on_activation(){
         $this->saswp_update_option_on_compatibility_activation('saswp-wp-recipe-maker');
    }
    public function ultimate_blocks_on_activation(){
        $this->saswp_update_option_on_compatibility_activation('saswp-ultimate-blocks');
    }
    public function video_thumbnails_on_activation(){
        $this->saswp_update_option_on_compatibility_activation('saswp-video-thumbnails');
    }
    public function featured_video_plus_on_activation(){
        $this->saswp_update_option_on_compatibility_activation('saswp-featured-video-plus');
    }
    public function geodirectory_on_activation(){
        $this->saswp_update_option_on_compatibility_activation('saswp-geodirectory');
    }
    public function wpzoom_on_activation(){
        $this->saswp_update_option_on_compatibility_activation('saswp-wpzoom');
    }
    public function yotpo_on_activation(){
        $this->saswp_update_option_on_compatibility_activation('saswp-yotpo');
    }
    public function ryviu_on_activation(){
        $this->saswp_update_option_on_compatibility_activation('saswp-ryviu');
    }
    public function wptastyrecipe_on_activation(){
        $this->saswp_update_option_on_compatibility_activation('saswp-wptastyrecipe');
    }
    public function recipress_on_activation(){
        $this->saswp_update_option_on_compatibility_activation('saswp-recipress');
    }
    public function wp_ultimate_recipe_on_activation(){
         $this->saswp_update_option_on_compatibility_activation('saswp-wp-ultimate-recipe');
    }
    public function zip_recipes_on_activation(){
         $this->saswp_update_option_on_compatibility_activation('saswp-zip-recipes');
    }
    public function mediavine_create_on_activation(){
         $this->saswp_update_option_on_compatibility_activation('saswp-mediavine-create');
    }
    public function ht_recipes_on_activation(){
         $this->saswp_update_option_on_compatibility_activation('saswp-ht-recipes');
    }
    public function ampforwp_on_activation(){
         $this->saswp_update_option_on_compatibility_activation('saswp-ampforwp');
    }
    public function ampbyautomatic_on_activation(){
         $this->saswp_update_option_on_compatibility_activation('saswp-ampbyautomatic');
    }
    public function cmp_on_activation(){
        $this->saswp_update_option_on_compatibility_activation('saswp-cmp');
    }
    public function wpecommerce_on_activation(){
        $this->saswp_update_option_on_compatibility_activation('saswp-wpecommerce');
    }
    public function wpreviewpro_on_activation(){
        $this->saswp_update_option_on_compatibility_activation('saswp-wpreviewpro');
    }
    public function webstories_on_activation(){
        $this->saswp_update_option_on_compatibility_activation('saswp-webstories');
    }
    public function betteramp_on_activation(){
         $this->saswp_update_option_on_compatibility_activation('saswp-betteramp');
    }
    public function wpamp_on_activation(){
         $this->saswp_update_option_on_compatibility_activation('saswp-wpamp');
    }
    public function ampwp_on_activation(){
         $this->saswp_update_option_on_compatibility_activation('saswp-ampwp');
    }
    public function wordlift_on_activation(){
         $this->saswp_update_option_on_compatibility_activation('saswp-wordlift');
    }
    public function strong_testimonials_on_activation(){
         $this->saswp_update_option_on_compatibility_activation('saswp-strong-testimonials');
    }
    public function tevolution_events_on_activation(){
         $this->saswp_update_option_on_compatibility_activation('saswp-tevolution-events');
    }
    public function wp_event_aggregator_on_activation(){
         $this->saswp_update_option_on_compatibility_activation('saswp-wp-event-aggregator');
    }
    public function timetable_event_on_activation(){
        $this->saswp_update_option_on_compatibility_activation('saswp-timetable-event');
    }
    public function stachethemes_event_calendar_on_activation(){
        $this->saswp_update_option_on_compatibility_activation('saswp-stachethemes-event-calendar');
    }
    public function easy_recipe_on_activation(){
         $this->saswp_update_option_on_compatibility_activation('saswp-easy-recipe');
    }
    public function wordpress_news_on_activation(){
         $this->saswp_update_option_on_compatibility_activation('saswp-wordpress-news');
    }
    public function schemaforfaqs_on_activation(){        
        $this->saswp_update_option_on_compatibility_activation('saswp-schemaforfaqs');
    }
    public function quickandeasyfaq_on_activation(){        
        $this->saswp_update_option_on_compatibility_activation('saswp-quickandeasyfaq');
    }
    public function accordionfaq_on_activation(){        
        $this->saswp_update_option_on_compatibility_activation('saswp-accordionfaq');
    }
    public function ultimatefaqs_on_activation(){        
        $this->saswp_update_option_on_compatibility_activation('saswp-ultimatefaqs');
    }
    public function easyaccordion_on_activation(){        
        $this->saswp_update_option_on_compatibility_activation('saswp-easyaccordion');
    }
    public function wpresponsivefaq_on_activation(){        
        $this->saswp_update_option_on_compatibility_activation('saswp-wpresponsivefaq');
    }
    public function arconixfaq_on_activation(){        
        $this->saswp_update_option_on_compatibility_activation('saswp-arconixfaq');
    }
    public function faqconcertina_on_activation(){        
        $this->saswp_update_option_on_compatibility_activation('saswp-faqconcertina');
    }
    public function faqschemaforpost_on_activation(){        
        $this->saswp_update_option_on_compatibility_activation('saswp-faqschemaforpost');
    }
    public function masteraccordion_on_activation(){        
        $this->saswp_update_option_on_compatibility_activation('saswp-masteraccordion');
    }
    public function simplejobboard_on_activation(){        
        $this->saswp_update_option_on_compatibility_activation('saswp-simplejobboard');
    }
    public function wpjobmanager_on_activation(){        
        $this->saswp_update_option_on_compatibility_activation('saswp-wpjobmanager');
    }
    public function wpjobopenings_on_activation(){        
        $this->saswp_update_option_on_compatibility_activation('saswp-wpjobopenings');
    }
    public function webfaq10_on_activation(){        
        $this->saswp_update_option_on_compatibility_activation('saswp-webfaq10');
    }
    public function wpfaqschemamarkup_on_activation(){        
        $this->saswp_update_option_on_compatibility_activation('saswp-wpfaqschemamarkup');
    }
    public function easyfaqs_on_activation(){        
        $this->saswp_update_option_on_compatibility_activation('saswp-easyfaqs');
    }
    public function accordion_on_activation(){        
        $this->saswp_update_option_on_compatibility_activation('saswp-accordion');
    }
    public function html5responsivefaq_on_activation(){        
        $this->saswp_update_option_on_compatibility_activation('saswp-html5responsivefaq');
    }
    public function helpiefaq_on_activation(){        
        $this->saswp_update_option_on_compatibility_activation('saswp-helpiefaq');
    }
    public function polylang_on_activation(){        
        $this->saswp_update_option_on_compatibility_activation('saswp-polylang');
    }
    public function total_recipe_generator_on_activation(){
         $this->saswp_update_option_on_compatibility_activation('saswp-total-recipe-generator');
    }
    public function wp_customer_reviews_on_activation(){
        $this->saswp_update_option_on_compatibility_activation('saswp-wp-customer-reviews');
    }
    public function yet_another_stars_rating_on_activation(){
        $this->saswp_update_option_on_compatibility_activation('saswp-yet-another-stars-rating');
    }
    public function saswp_update_option_on_compatibility_activation($opt_name){   
        
        $defaults = get_option('sd_data');   
        $defaults[$opt_name] = 1;        
        update_option('sd_data', $defaults); 
        
    }
    /**
     * Functions on compatiblity plugin activation ends here
     */
        
}
if(class_exists('saswp_output_compatibility')){
   $obj_compatibility =  new saswp_output_compatibility();
   $obj_compatibility->saswp_service_compatibility_hooks();
}

//Remove Slim seo schema 

add_action('slim_seo_init', 'saswp_override_slim_seo',1,10);

function saswp_override_slim_seo($plugin){

    global $sd_data;

    if( isset($sd_data['saswp-slimseo']) && $sd_data['saswp-slimseo'] == 1){
        $plugin->disable( 'schema' );
    }

}
