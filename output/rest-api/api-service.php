<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class SASWP_Output_Rest_Api_Service {
        
    public function get_schema($post_id) {

        $response = array();

        if( $post_id ){

            $permalink = get_permalink($post_id);

            $result = wp_remote_get($permalink);

            if(isset($result['body'])){

                $regex = '/<script type\=\"application\/ld\+json\" class\=\"saswp\-schema\-markup\-output\"\>(.*?)<\/script>/s'; 

                preg_match( $regex, $result['body'], $match);

                if(isset($match[1])){
                    $response = json_decode($match[1], true);
                }

            }
            
        }
                                    
        return $response;        

    }  
                 
}