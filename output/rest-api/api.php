<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class SASWP_Output_Rest_Api {
                
        private static $instance;   
        private $api_service        = null; 
        
        private function __construct() {
            
            if($this->api_service == null){
                require_once SASWP_PLUGIN_DIR_PATH.'output/rest-api/api-service.php'; 
                $this->api_service = new SASWP_Output_Rest_Api_Service();
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
            
            register_rest_route( 'saswp-output', 'posts/(?P<id>\d+)', array(
                    'methods'    => 'GET',
                    'callback'   => array($this, 'json_ld'),
                    'permission_callback' => function(){
                        return true;
                    }
            ));

        }  
         
        public function json_ld($request){
            
            $response  = array();
            $post_id   = null;

            $parameters = $request->get_params();
         
            if(isset($parameters['id'])){
                $post_id   = $parameters['id'];
            }else{
                return array('status' => 'f', 'message' => 'post_id is required');
            }

            $response = $this->api_service->get_schema($post_id);

            if($response){
                return array('status' => 't', 'json_ld' => $response);
            }else{
                return array('status' => 'f', 'json_ld' => array());
            }
            
            return $response;

        }
       
}
if(class_exists('SASWP_Output_Rest_Api')){
    SASWP_Output_Rest_Api::getInstance();
}