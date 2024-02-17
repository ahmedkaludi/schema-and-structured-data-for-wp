<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class SASWP_Output_Rest_Api_Service {
        
    public function get_schema($post_id, $graph_name = 'saswpSchema') {

        $response = array();

        if( $post_id ){

            $permalink = get_permalink($post_id);

            $result = wp_remote_get($permalink);

            if( !is_wp_error( $result ) && 200 === wp_remote_retrieve_response_code( $result )){

                $regex = '/<script type\=\"application\/ld\+json\" class\=\"saswp\-schema\-markup\-output\"\>(.*?)<\/script>/s';
                if($graph_name == 'saswpCustomSchema'){ 
                    $regex = '/<script type\=\"application\/ld\+json\" class\=\"saswp\-custom\-schema\-markup\-output\"\>(.*?)<\/script>/s'; 
                }else if($graph_name == 'saswpUserSchema'){
                    $regex = '/<script type\=\"application\/ld\+json\" class\=\"saswp\-user\-custom\-schema\-markup\-output\"\>(.*?)<\/script>/s';
                }else if($graph_name == 'saswpOtherSchema'){
                    $regex = '/<script type\=\"application\/ld\+json\" class\=\"saswp\-other\-schema\-markup\-output\"\>(.*?)<\/script>/s';
                }

                preg_match( $regex, $result['body'], $match);

                if(isset($match[1])){
                    $response = json_decode($match[1], true);
                }

            }
            
        }
                                    
        return $response;        

    }  
                 
}