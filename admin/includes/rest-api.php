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
            
            register_rest_route( 'saswp-route', 'get-schema-list', array(
                    'methods'    => 'GET',
                    'callback'   => array($this, 'getSchemaList'),
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

            $settings = array();
	        $settings = get_option( 'quads_settings' );
            header( 'Content-Type: application/json; charset=utf-8' );
	        header( 'Content-Disposition: attachment; filename=' . apply_filters( 'quads_settings_export_filename', 'quads-settings-export-' . date( 'm-d-Y' ) ) . '.json' );
            header( "Expires: 0" );
            return   $settings ;	                   
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
        public function sendCustomerQuery($request){

             $parameters = $request->get_params();
             
               
             $customer_type  = 'Are you a premium customer ? No';
             $message        = sanitize_textarea_field($parameters['message']); 
             $email          = sanitize_text_field($parameters['email']); 
             $premium_cus    = sanitize_text_field($parameters['type']);                
             
             if($premium_cus == 'yes'){
                $customer_type  = 'Are you a premium customer ? Yes';
             }
             
             $message = '<p>'.$message.'</p><br><br>'
                     . $customer_type
                     . '<br><br>'.'Query from WP Quads plugin support tab';
             
             if($email && $message){
                           
                 //php mailer variables        
                 $sendto    = 'team@magazine3.com';
                 $subject   = "WP Quads Customer Query";
                 
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
        public function getSettings($request){

            $quads_settings = get_option('quads_settings');            
            $quads_settings['QckTags'] = isset($quads_settings['quicktags']['QckTags']) ? $quads_settings['quicktags']['QckTags'] : false;                        
            return $quads_settings;
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
                
                $settings = json_decode( file_get_contents( $import_file ), true);
                update_option( 'quads_settings', $settings );
                $response = array('file_status' => 't','status' => 't', 'msg' =>  __( 'file uploaded successfully', 'quick-adsense-reloaded' ));                                       

            }else{
                if(isset($parameters['settings'])){
                    $result      = $this->api_service->updateSettings(json_decode($parameters['settings'], true));
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