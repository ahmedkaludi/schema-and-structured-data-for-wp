<?php 
/**
 * Newsletter class
 *
 * @author   Magazine3
 * @category Admin
 * @path     admin_section/newsletter
 * @Version 1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class saswp_reviews_service {
        
    public function saswp_service_hooks(){
        add_action( 'wp_ajax_saswp_fetch_google_reviews', array($this,'saswp_fetch_google_reviews'));
        add_shortcode('saswp-reviews', array($this,'saswp_reviews_shortcode'));
    }
    
    public function saswp_reviews_shortcode($attr){
        
        
        if(isset($attr['id'])){
            
        }
        if(isset($attr['name'])){
            
        }
        if(isset($attr['count'])){
            
        }
        if(isset($attr['rating'])){
            
        }
        if(isset($attr['platform'])){
            
        }
        
        $output = 'yahooo';
        
        return $output;
        
    }
    public function saswp_save_free_reviews_data($result, $place_id) {
                
        $place_saved   = array();
        $reviews_saved = array();
        
        if (isset($result['place_id']) && $result['place_id'] != '') {
                                                                   
                $user_id     = get_current_user_id();
                $postarr = array(
                    'post_author'           => $user_id,                                                            
                    'post_title'            => $result['name'],                    
                    'post_status'           => 'publish',                                                            
                    'post_name'             => $result['name'],                                                            
                    'post_type'             => 'saswp_rvs_location',
                                                                             
                );
                   
                $post_id = wp_insert_post(  $postarr );   
                $place_saved[] = $post_id;                                                  
                $review_meta = array(
                        'saswp_rvs_location_id'                 => $result['place_id'],      
                        'saswp_rvs_location_review_count'       => $result['user_ratings_total'], 
                        'saswp_rvs_location_avg_rating'         => $result['rating'],
                        'saswp_rvs_location_icon'               => $result['icon'],
                        'saswp_rvs_location_address'            => $result['formatted_address'],
                );

                if($post_id && !empty($review_meta) && is_array($review_meta)){
                                        
                    foreach ($review_meta as $key => $val){                     
                        update_post_meta($post_id, $key, $val);  
                    }
            
                 }
                            
        }
        
                                            
        if (isset($result['reviews'])) {
            
            $reviews = $result['reviews'];
            
            foreach ($reviews as $review) {
               
                $user_id     = get_current_user_id();
                $postarr = array(
                    'post_author'           => $user_id,                                                            
                    'post_title'            => $review['author_name'],                    
                    'post_status'           => 'publish',                                                            
                    'post_name'             => 'Default Review',                                                            
                    'post_type'             => 'saswp_reviews',
                                                                             
                );
                   
                $post_id = wp_insert_post(  $postarr );   
                $reviews_saved[] = $post_id;
                $term     = get_term_by( 'slug','google', 'platform' );
                
                $media_detail = array();
                
                if(isset($review['profile_photo_url'])){
                    
                    $image_details = saswp_get_attachment_details(array($review['profile_photo_url']));   
                    
                    $media_detail = array(                                                    
                        'width'      => $image_details[0][0],
                        'height'     => $image_details[0][1],
                        'thumbnail'  => $review['profile_photo_url'],
                    );
                    
                }                
                
                $review_meta = array(
                        'saswp_review_platform'       => $term->term_id,
                        'saswp_review_location_id'    => $place_id,
                        'saswp_review_time'           => $review['time'], 
                        'saswp_review_rating'         => $review['rating'],
                        'saswp_review_text'           => $review['text'],                                
                        'saswp_reviewer_lang'         => $review['language'],
                        'saswp_reviewer_name'         => $review['author_name'],
                        'saswp_review_link'           => isset($review['author_url']) ? $review['author_url'] : null,
                        'saswp_reviewer_image'        => isset($review['profile_photo_url']) ? $review['profile_photo_url'] : null,
                        'saswp_reviewer_image_detail' => $media_detail
                );

                if($post_id && !empty($review_meta) && is_array($review_meta)){
                                        
                    foreach ($review_meta as $key => $val){                     
                        update_post_meta($post_id, $key, $val);  
                    }
            
                 }
                
            }
        }
        
        if(!empty($place_saved) || !empty($reviews_saved)){
            return true;
        }else{
            return false;
        }
                
    }
    
    public function saswp_save_paid_reviews_data($result, $place_id) {
            
        $place_saved   = array();
        $reviews_saved = array();
        
        if (isset($result['unique_id']) && $result['unique_id'] != '') {
                                                                   
                $user_id     = get_current_user_id();
                $postarr = array(
                    'post_author'           => $user_id,                                                            
                    'post_title'            => 'Location-'.$result['unique_id'],                    
                    'post_status'           => 'publish',                                                            
                    'post_name'             => 'Default Review',                                                            
                    'post_type'             => 'saswp_rvs_location',
                                                                             
                );
                   
                $post_id = wp_insert_post(  $postarr );   
                $place_saved[] =  $post_id;                                
                $review_meta = array(
                        'saswp_rvs_location_id'                 => $result['unique_id'],      
                        'saswp_rvs_location_review_count'       => $result['review_count'], 
                        'saswp_rvs_location_avg_rating'         => $result['average_rating'],                                                
                );

                if($post_id && !empty($review_meta) && is_array($review_meta)){
                                        
                    foreach ($review_meta as $key => $val){                     
                        update_post_meta($post_id, $key, $val);  
                    }
            
                 }
                            
        }
                
        
        if (isset($result['reviews']) && is_array($result['reviews'])) {
            
            $reviews = $result['reviews'];
            
            foreach ($reviews as $review) {
               
                $user_id     = get_current_user_id();
                $postarr = array(
                    'post_author'           => $user_id,                                                            
                    'post_title'            => $review['author_name'],                    
                    'post_status'           => 'publish',                                                            
                    'post_name'             => 'Default Review',                                                            
                    'post_type'             => 'saswp_reviews',
                                                                             
                );
                   
                $post_id = wp_insert_post(  $postarr );   
                $reviews_saved[] =  $post_id;                              
                $media_detail = array();
                
                if(isset($review['saswp_reviewer_image'])){
                    
                    $image_details = saswp_get_attachment_details(array($review['saswp_reviewer_image']));   
                    
                    $media_detail = array(                                                    
                        'width'      => $image_details[0][0],
                        'height'     => $image_details[0][1],
                        'thumbnail'  => $review['profile_photo_url'],
                    );
                    
                }                
                
                $review_meta = array(
                        'saswp_review_platform'       => $review['saswp_review_platform'],
                        'saswp_review_location_id'    => $place_id,
                        'saswp_review_time'           => $review['saswp_review_time'], 
                        'saswp_review_rating'         => $review['saswp_review_rating'],
                        'saswp_review_text'           => $review['saswp_review_text'],                                
                        'saswp_reviewer_lang'         => $review['saswp_reviewer_lang'],
                        'saswp_reviewer_name'         => $review['saswp_reviewer_name'],
                        'saswp_review_link'           => isset($review['saswp_review_link']) ? $review['saswp_review_link'] : null,
                        'saswp_reviewer_image'        => isset($review['saswp_reviewer_image']) ? $review['saswp_reviewer_image'] : null,
                        'saswp_reviewer_image_detail' => $media_detail
                );

                if($post_id && !empty($review_meta) && is_array($review_meta)){
                                        
                    foreach ($review_meta as $key => $val){                     
                        update_post_meta($post_id, $key, $val);  
                    }
            
                 }
                
            }
        }
        
        if(!empty($place_saved) || !empty($reviews_saved)){
            return true;
        }else{
            return false;
        }
                
    }
    
    public function saswp_get_paid_reviews_data($location, $api_key){
                
        global $sd_data;
        
        //$api_key = base64_encode( urlencode( "n8KP16uvGZA6xvFTtb8IAA:i4pmOV0duXJv7TyF5IvyFdh5wDIqfJOovKjs92ei878" ) );
        
        $api_key = '545455456454';
        
        $current_user = wp_get_current_user();
        $current_user = $current_user->data;        
        $body = array(
            'user_login'  => $current_user->user_login,
            'user_email' => $current_user->user_email,
            'place_id'   => $location,    
        );
        $header = array(
           'Authorization' => 'Basic' . $api_key 
        );
        
        $result = @wp_remote_post('http://localhost/wordpress/wp-json/reviews-route/add_profile',
                    array(
                        'method'      => 'POST',
                        'timeout'     => 45,
                        'redirection' => 5,
                        'httpversion' => '1.1',
                        'blocking'    => true,
                        'headers'     => $header,
                        'body'        => $body,
                        'cookies'     => array()
                    )                                      
                );                         
              
       if(wp_remote_retrieve_response_code($result) == 200 && wp_remote_retrieve_body($result)){
            
              $api_key = '23030303';  
              $result = @wp_remote_get('http://localhost/wordpress/wp-json/reviews-route/get_reviews?api_key='.$api_key.'&place_id='.$location);        
              
              if(isset($result['body'])){
              $result = json_decode($result['body'],true);              
              $response = $this->saswp_save_paid_reviews_data($result, $location);
              
              return $response;
              }else{
               return null;
              }
        }
        
        if ( is_wp_error( $result ) ) {
            $error_message = $result->get_error_message();
            echo "Something went wrong: $error_message";
        } else {
            echo 'Response:<pre>';
            print_r( $result );
            echo '</pre>';
        }
                
    }
    
    public function saswp_get_free_reviews_data($place_id){
                
        global $sd_data;                         
        
        $result = @wp_remote_get('https://maps.googleapis.com/maps/api/place/details/json?placeid='.trim($place_id).'&key='.trim($sd_data['saswp_google_place_api_key']).$language);                
        if(isset($result['body'])){
           $result = json_decode($result['body'],true);                                          
           $response = $this->saswp_save_free_reviews_data($result['result'], $place_id);
           return $response;
        }else{
           return null;
        }        
                                            
    }
    
    public function saswp_fetch_google_reviews(){
                
                if ( ! current_user_can( 'manage_options' ) ) {
                    return;
                }
        
                if ( ! isset( $_POST['saswp_security_nonce'] ) ){
                    return; 
                }
                
                if ( !wp_verify_nonce( $_POST['saswp_security_nonce'], 'saswp_ajax_check_nonce' ) ){
                   return;  
                }
                global $sd_data;
                $location  = '';
                                                
                if(isset($_POST['location'])){
                    $location = sanitize_text_field($_POST['location']);
                }
                                                
                if($location){
                    
                   if(isset($sd_data['saswp_reviews_location_name'])){
                          
                       if(!in_array($location, $sd_data['saswp_reviews_location_name'])){
                           array_push($sd_data['saswp_reviews_location_name'], $location);                       
                       }
                                              
                   }else{
                       $sd_data['saswp_reviews_location_name'] = array($location);  
                       
                   }
                  update_option('sd_data', $sd_data);    
                  
                  $result = null;
                  $api_key        = '56564646';
                  $api_key_status = 'valid';
                    
                  if($api_key && $api_key_status == 'validd'){
                        
                          $result = $this->saswp_get_paid_reviews_data($location, $api_key);
                      
                  }else{
                      
                      if(isset($sd_data['saswp_google_place_api_key']) && $sd_data['saswp_google_place_api_key'] !=''){
                          $result = $this->saswp_get_free_reviews_data($location);
                      }                      
                      
                  }  
                                                        
                  if($result){
                      
                      echo json_encode(array('status' => 't'));
                      
                  }else{
                      
                      echo json_encode(array('status' => 'f'));
                      
                  }
                    
                }
                
                wp_die();
        
    }
	                       
}
$saswp_service_obj = new saswp_reviews_service();
$saswp_service_obj->saswp_service_hooks();
?>