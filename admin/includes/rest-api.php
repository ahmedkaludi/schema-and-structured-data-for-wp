<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class SASWP_Rest_Api {
                
        private static $instance;   
        private $api_service = null;        

        private function __construct() {
            
            if($this->api_service == null){
                require_once SASWP_DIR_NAME . '/admin/includes/rest-api-service.php';
                $this->api_service = new SASWP_Rest_Api_Service();
            }
            
            
            add_action( 'rest_api_init', array($this, 'registerRoute'));
                                 
        }
                
        public static function getInstance() {
            
            if ( null == self::$instance ) {
                self::$instance = new self;
            }
		    return self::$instance;
        }
        
        public function registerRoute(){
            
            register_rest_route( 'saswp-route', 'get-translations', array(
                    'methods'    => 'GET',
                    'callback'   => array($this, 'getTranslations'),
                    'permission_callback' => function(){
                        return current_user_can( 'manage_options' );
                    }
            ));
            register_rest_route( 'saswp-route', 'get-schema-list', array(
                'methods'    => 'GET',
                'callback'   => array($this, 'getSchemaList'),
                'permission_callback' => function(){
                    return current_user_can( 'manage_options' );
                }
            ));
            register_rest_route( 'saswp-route', 'get-migration-status', array(
                'methods'    => 'GET',
                'callback'   => array($this, 'getMigrationStatus'),
                'permission_callback' => function(){
                    return current_user_can( 'manage_options' );
                }
            ));
            register_rest_route( 'saswp-route', 'change-mode', array(
                'methods'    => 'POST',
                'callback'   => array($this, 'changeMode'),
                'permission_callback' => function(){
                    return current_user_can( 'manage_options' );
                }
            ));
            register_rest_route( 'saswp-route', 'ad-more-action', array(
                'methods'    => 'POST',
                'callback'   => array($this, 'adMoreAction'),
                'permission_callback' => function(){
                    return current_user_can( 'manage_options' );
                }
            ));
            register_rest_route( 'saswp-route', 'update-schema', array(
                'methods'    => 'POST',
                'callback'   => array($this, 'updateSchema'),
                'permission_callback' => function(){
                    return current_user_can( 'manage_options' );
                }
            ));
            register_rest_route( 'saswp-route', 'update-settings', array(
                'methods'    => 'POST',
                'callback'   => array($this, 'updateSettings'),
                'permission_callback' => function(){
                    return current_user_can( 'manage_options' );
                }
            ));
            register_rest_route( 'saswp-route', 'validate-ads-txt', array(
                'methods'    => 'POST',
                'callback'   => array($this, 'validateAdsTxt'),
                'permission_callback' => function(){
                    return current_user_can( 'manage_options' );
                }
            ));
            register_rest_route( 'saswp-route', 'send-customer-query', array(
                'methods'    => 'POST',
                'callback'   => array($this, 'sendCustomerQuery'),
                'permission_callback' => function(){
                    return current_user_can( 'manage_options' );
                }
            ));
            register_rest_route( 'saswp-route', 'migration', array(
                'methods'    => 'POST',
                'callback'   => array($this, 'migration'),
                'permission_callback' => function(){
                    return current_user_can( 'manage_options' );
                }
            ));
            register_rest_route( 'saswp-route', 'reset-settings', array(
                'methods'    => 'POST',
                'callback'   => array($this, 'resetSettings'),
                'permission_callback' => function(){
                    return current_user_can( 'manage_options' );
                }
            ));
            register_rest_route( 'saswp-route', 'get-schema-data-by-id', array(
                'methods'    => 'GET',
                'callback'   => array($this, 'getSchemaById'),
                'permission_callback' => function(){
                    return current_user_can( 'manage_options' );
                }
            ));
            register_rest_route( 'saswp-route', 'get-settings', array(
                'methods'    => 'GET',
                'callback'   => array($this, 'getSettings'),
                'permission_callback' => function(){
                    return current_user_can( 'manage_options' );
                }
            ));
            register_rest_route( 'saswp-route', 'get-condition-list', array(
                'methods'    => 'GET',
                'callback'   => array($this, 'getConditionList'),
                'permission_callback' => function(){
                    return current_user_can( 'manage_options' );
                }
            ));            
            register_rest_route( 'saswp-route', 'export-settings', array(
                'methods'    => 'GET',
                'callback'   => array($this, 'exportSettings')                
            ));
            register_rest_route( 'saswp-route', 'get-quads-info', array(
                'methods'    => 'GET',
                'callback'   => array($this, 'getQuadsInfo'),
                'permission_callback' => function(){
                    return current_user_can( 'manage_options' );
                }
            ));
            register_rest_route( 'saswp-route', 'get-user-role', array(
                'methods'    => 'GET',
                'callback'   => array($this, 'getUserRole'),
                'permission_callback' => function(){
                    return current_user_can( 'manage_options' );
                }
            ));
            register_rest_route( 'saswp-route', 'get-tags', array(
                'methods'    => 'GET',
                'callback'   => array($this, 'getTags'),
                'permission_callback' => function(){
                    return current_user_can( 'manage_options' );
                }
            ));
            register_rest_route( 'saswp-route', 'get-plugins', array(
                'methods'    => 'GET',
                'callback'   => array($this, 'getPlugins'),
                'permission_callback' => function(){
                    return current_user_can( 'manage_options' );
                }
            ));
            register_rest_route( 'saswp-route', 'get-premium-extensions', array(
                'methods'    => 'GET',
                'callback'   => array($this, 'getPremiumExtensions'),
                'permission_callback' => function(){
                    return current_user_can( 'manage_options' );
                }
            ));             
        }  

        public function changeMode($request){
            
            $parameters = $request->get_params();
            $mode       = '';
            
            if(isset($parameters['mode'])){
                $mode   = sanitize_text_field($parameters['mode']);
            }

            $response = update_option('quads-mode', $mode);            

            return array('status' => 't');                        

        }
        public function getPremiumExtensions($request){

            $parameters = $request->get_params();
            $mappings_file = SASWP_DIR_NAME . '/core/array-list/pro_extensions.php';

            $pro_ext  = array();
            $response = array();
            
            if ( file_exists( $mappings_file ) ) {
                $pro_ext = include $mappings_file;
            }

            if(!empty($pro_ext)){

                foreach($pro_ext as $ext){
                                        
                    if(is_plugin_active($ext['path'])){
                        
                        $ext['status'] = 'Active';
                        
                    }

                    $response[] = $ext;

                }

            }

            return $response;

        }
        public function getPlugins($request){

            $response = array();
            $search   = '';

            $parameters = $request->get_params();

            if(isset($parameters['search'])){
                $search   = $parameters['search'];
            }

            $response = $this->api_service->getPlugins($search);
            if($response){
                return array('status' => 't', 'data' => $response);
            }else{
                return array('status' => 'f', 'data' => 'data not found');
            }
            
            return $response;

        }

        public function getTags($request){

            $response = array();
            $search   = '';

            $parameters = $request->get_params();

            if(isset($parameters['search'])){
                $search   = $parameters['search'];
            }

            $response = $this->api_service->getConditionList('tags', $search, 'diff');
            if($response){
                return array('status' => 't', 'data' => $response);
            }else{
                return array('status' => 'f', 'data' => 'data not found');
            }
            
            return $response;

        }

        public function getUserRole($request){

            $response = array();
            $search   = '';

            $parameters = $request->get_params();

            if(isset($parameters['search'])){
                $search   = $parameters['search'];
            }

            $result = $this->api_service->getConditionList('user_type', $search);

            if($result){                
                return array('status' => 't', 'data' => $result);
            }else{
                return array('status' => 'f', 'data' => array());
            }
            
            return $response;
        }
        public function getQuadsInfo(){
            require_once SASWP_DIR_NAME . 'includes/admin/tools.php';
            $info = quads_tools_sysinfo_get();
            return array('info' => $info);
        }
        public function exportSettings(){
            
            $post_type = array('saswp_reviews', 'saswp', 'saswp-collections');
            $export_data_all   = array(); 
            
            foreach($post_type as $type){
                
                $export_data       = array();                

                $all_schema_post = get_posts(

                    array(
                            'post_type' 	     => $type,                                                                                   
                            'posts_per_page'     => -1,   
                            'post_status'        => 'any',
                    )

                    );                        

                if($all_schema_post){
                
                    foreach($all_schema_post as $schema){    

                    $export_data[$schema->ID]['post']      = (array)$schema;                    
                    $post_meta                             = get_post_meta($schema->ID, $key='', true );    

                    if($post_meta){

                        foreach ($post_meta as $key => $meta){

                            if(@unserialize($meta[0]) !== false){
                                $post_meta[$key] = @unserialize($meta[0]);
                            }else{
                                $post_meta[$key] = $meta[0];
                            }

                        }

                    }

                    $export_data[$schema->ID]['post_meta'] = $post_meta;  

                    }       

                    $export_data_all['posts'][$type] = $export_data;    
                    
                }
                                    
                
            }
            
            $export_data_all['sd_data']         = get_option('sd_data');

            header( 'Content-Type: application/json; charset=utf-8' );
	        header('Content-disposition: attachment; filename=structuredatabackup.json');
            header( "Expires: 0" );
            return   $export_data_all;	                   
        }
        public function adMoreAction($request){

            $response   = array();
            $parameters = $request->get_params();
            $action     = $parameters['action'];
            $ad_id      = $parameters['ad_id'];
            $result     = null;
            
            if($action){

                switch ($action) {

                    case 'publish':
                        $result = $this->api_service->changeAdStatus($ad_id, 'publish');
                        if($result){
                            $response = array('status'=> 't', 'msg' => 'Changed Successfully', 'data' => array());
                        }
                        break;
                    case 'draft':
                        $result = $this->api_service->changeAdStatus($ad_id, 'draft');
                        if($result){
                            $response = array('status'=> 't', 'msg' => 'Changed Successfully', 'data' => array());
                        }    
                        break;
                    case 'duplicate':
                        $new_ad_id = $this->api_service->duplicateAd($ad_id);
                        if($new_ad_id){
                            $data     = $this->api_service->getAdById($new_ad_id);                            
                            $response = array('status'=> 't', 'msg' => 'Duplicated Successfully', 'data' => $data);
                        }
                        break;
                    case 'delete':
                        $result = $this->api_service->deleteAd($ad_id);
                        if($result){
                            $response = array('status'=> 't', 'msg' => 'Deleted Successfully', 'data' => array());
                        }
                        break;        
                    
                    default:
                        # code...
                        break;
                }

            }

            return $response;
        }
        public function resetSettings($request){

                $result = '';
        
                delete_option( 'sd_data');  
                
                $allposts= get_posts( array('post_type'=>'saswp','numberposts'=>-1) );
                
                foreach ($allposts as $eachpost) {
                    
                    $result = wp_delete_post( $eachpost->ID, true );
                
                }

                if($result){
                    return array('status'=>'t', 'msg' => 'Reset Successfully');
                }

        }
        public function migration($request){

            $parameters = $request->get_params();   
            $plugin_name   = sanitize_text_field($parameters['plugin_name']);         

            $result        = '';

            switch ($plugin_name) {
            
                case 'schema':
                    if ( is_plugin_active('schema/schema.php')) {
                        $result = saswp_import_schema_plugin_data();      
                    }                
                    break;
                    
                case 'schema_pro':                
                    if ( is_plugin_active('wp-schema-pro/wp-schema-pro.php')) {
                        $result = saswp_import_schema_pro_plugin_data();      
                    }                
                    break;
                case 'wp_seo_schema':                
                    if ( is_plugin_active('wp-seo-structured-data-schema/wp-seo-structured-data-schema.php')) {
                        $result = saswp_import_wp_seo_schema_plugin_data();      
                    }
                     break;
                case 'seo_pressor':                
                    if ( is_plugin_active('seo-pressor/seo-pressor.php')) {
                        $result = saswp_import_seo_pressor_plugin_data();      
                    }                
                    break;
               case 'wpsso_core':                
                    if ( is_plugin_active('wpsso/wpsso.php') && is_plugin_active('wpsso-schema-json-ld/wpsso-schema-json-ld.php')) {
                        $result = saswp_import_wpsso_core_plugin_data();      
                    }                
                    break;
                case 'aiors':                
                    if ( is_plugin_active('all-in-one-schemaorg-rich-snippets/index.php')) {
                        $result = saswp_import_aiors_plugin_data();      
                    }                
                    break;   
                    
                    case 'wp_custom_rv':                
                    if ( is_plugin_active('wp-customer-reviews/wp-customer-reviews-3.php')) {
                        $result = saswp_import_wp_custom_rv_plugin_data();      
                    }                
                    break; 
    
                    case 'starsrating':       
                          
                      if ( is_plugin_active('stars-rating/stars-rating.php')) {                      
                          update_option('saswp_imported_starsrating', 1);
                          $result = 'updated';
                      }                
                    break; 
                    
                    case 'schema_for_faqs':                
                      if ( is_plugin_active('faq-schema-markup-faq-structured-data/schema-for-faqs.php')) {
                          $result = saswp_import_schema_for_faqs_plugin_data();      
                      }                
                    break;                 
    
                default:
                    break;
            }                             
            if($result){
                
                 echo json_encode(array('status'=>'t', 'message'=>esc_html__('Data has been imported succeessfully','schema-and-structured-data-for-wp')));            
                 
            }else{
                
                echo json_encode(array('status'=>'f', 'message'=>esc_html__('Plugin data is not available or it is not activated','schema-and-structured-data-for-wp')));            
            
            }

        }
        public function sendCustomerQuery($request){

             $parameters = $request->get_params();
                            
             $customer_type  = 'Premium Customer ? No';
             $message        = sanitize_textarea_field($parameters['message']); 
             $email          = sanitize_text_field($parameters['email']); 
             $premium_cus    = sanitize_text_field($parameters['type']);                
             
             if($premium_cus == 'yes'){
                $customer_type  = 'Premium Customer ? Yes';
             }
             
             $message = '<p>'.$message.'</p><br><br>'
                     . $customer_type
                     . '<br><br>'.'Query from SASWP support tab';
             
             if($email && $message){
                           
                 //php mailer variables        
                 $sendto    = 'team@magazine3.com';
                 $subject   = "SASWP Customer Query";
                 
                 $headers[] = 'Content-Type: text/html; charset=UTF-8';
                 $headers[] = 'From: '. esc_attr($email);            
                 $headers[] = 'Reply-To: ' . esc_attr($email);
                 // Load WP components, no themes.                      
                 $sent = wp_mail($sendto, $subject, $message, $headers); 
     
                 if($sent){
     
                    return array('status'=>'t');
     
                 }else{
     
                    return array('status'=>'f');
     
                 }
                 
             }else{
                return array('status'=>'f', 'msg' => 'Please provide message and email');
             }
        }
        public function validateAdsTxt($request){

            $response = array();

            $parameters = $request->get_params();

            if($parameters[0]){
                $result = $this->api_service->validateAdsTxt($parameters[0]);
                if($result['errors']){
                    $response['errors'] = $result['errors'];
                }else{
                    $response['valid'] = true;
                }
            }
            return $response;
           
        }        
        
        public function getSettings($request_data){

                $parameters = $request_data->get_params();
                $response   = $this->api_service->getSettings();

                return  $response;

        }   
        public function getConditionList($request_data){

            $response = array();
            $search   = '';

            $parameters = $request_data->get_params();

            if(isset($parameters['search'])){
                $search   = $parameters['search'];
            }

            if(isset($parameters['condition'])){
                $response = $this->api_service->getConditionList($parameters['condition'], $search);
            }else{
                $response =  array('status' => '404', 'message' => 'property type is required');
            }
            return $response;

            
        }
        public function getSchemaById($request_data){

            $response = array();

            $parameters = $request_data->get_params();

            if(isset($parameters['schema_id'])){
                $response = $this->api_service->getSchemaById($parameters['schema_id']);
            }else{
                $response =  array('status' => '404', 'message' => 'Schema id is required');
            }
            return $response;
           
        }
        public function getMigrationStatus(){

            $message                 = 'This plugin\'s data already has been imported. Do you want to import again?. click on button above button.';

            $data_id = array(
                'schema'         => '',
                'schema_pro'     => '',
                'wp_seo_schema'  => '',
                'seo_pressor'    => '',
                'wpsso_core'     => '',
                'aiors'          => '',
                'wp_custom_rv'   => '',
                'schema_for_faqs'=> '',
                'starsrating'    => '',
            );

            foreach ($data_id as $key => $value) {
                
                $cc_args    = array(
                    'posts_per_page'   => -1,
                    'post_type'        => 'saswp',
                    'meta_key'         => 'imported_from',
                    'meta_value'       => $key,
                );	
   
                $result = new WP_Query( $cc_args ); 

                if($result->post_count !=0 ){
            
                    $data_id[$key] = $message;                    
                             
                }

            }

            return $data_id;

        }
        public function getTranslations(){

            global $sd_data;
                        
            global  $translation_labels;

            $response = array();
            
            if(is_array($translation_labels)){
               
                foreach($translation_labels as $key => $val){
                if(isset($sd_data[$key]) && $sd_data[$key] !='' ){
                    $translation = $sd_data[$key];
                }else{
                    $translation = $val;
                }   
                
                $response[] = array(
                    'name'  => $val,
                    'key'   => $key,
                    'value' => $translation,
                );
                 
                }
            
            }

            return $response;
        }
        public function getSchemaList(){
            
            $search_param = '';
            $rvcount      = 10;
            $attr         = array();
            $paged        =  1;
            $offset       =  0;
            $post_type    = 'saswp';

            if(isset($_GET['page'])){
                $paged    = sanitize_text_field($_GET['page']);
            }
            
            if(isset($_GET['search_param'])){
                $search_param = sanitize_text_field($_GET['search_param']);
            }            
            $result = $this->api_service->getSchemaList($post_type, $attr, $rvcount, $paged, $offset, $search_param);                       
            return $result;
                        
        }
        public function updateSettings($request_data){
            
            $response        = array();
            $parameters      = $request_data->get_params();
            $file            = $request_data->get_file_params();
            
            if(isset($file['file'])){

                $parts = explode( '.',$file['file']['name'] );                
                if( end($parts) != 'json' ) {
                    $response = array('status' => 'f', 'msg' =>  __( 'Please upload a valid .json file', 'quick-adsense-reloaded' ));                   
                }
              
                $import_file = $file['file']['tmp_name'];
                if( empty( $import_file ) ) {
                    $response = array('status' => 'f', 'msg' =>  __( 'Please upload a file to import', 'quick-adsense-reloaded' ));                                       
                }
                                                
                if($import_file){
                    $result      = $this->api_service->importFromFile($import_file);    
                    $response = array('file_status' => 't','status' => 't', 'msg' =>  __( 'file uploaded successfully', 'quick-adsense-reloaded' ));                                       
                }else{
                    $response = array('status' => 'f', 'msg' =>  __( 'File not found', 'quick-adsense-reloaded' ));                   
                }
                                                
            }else{
                
                if($parameters){
                    $result      = $this->api_service->updateSettings($parameters);
                    if($result){
                        $response = array('status' => 't', 'msg' =>  __( 'Settings has been saved successfully', 'quick-adsense-reloaded' ));                                               
                    }
                }
            }
            
            return $response;    
        }
        public function updateSchema($request_data){

            $parameters     = $request_data->get_params();                                   
            $schema_id      = $this->api_service->updateSchema($parameters);                       
            
            if($schema_id){
                return array('status' => 't', 'schema_id' => $schema_id);
            }else{
                return array('status' => 'f', 'schema_id' => null);
            }     
        }      
       
}
if(class_exists('SASWP_Rest_Api')){
    SASWP_Rest_Api::getInstance();
}