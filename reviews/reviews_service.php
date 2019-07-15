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
    }
                
    public function saswp_save_free_reviews_data($place, $place_id) {
        
        
                                            
        if (isset($place['reviews'])) {
            
            $reviews = $place['reviews'];
            
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
        
        return $response;
    }
    
    public function saswp_save_paid_reviews_data($result, $place_id) {
                               
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
                                                 
                $review_meta = array(
                        'saswp_rvs_location_id'                 => $result['unique_id'],      
                        'saswp_rvs_location_review_count'       => $result['unique_id'], 
                        'saswp_rvs_location_avg_rating'         => $result['unique_id'], 
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
                
    }
    
    public function saswp_get_paid_reviews_data($location){
                
        global $sd_data;
        
        $api_key = '54244555454';
        
        $body = array(
            'user_email' => 'abddd@gmail.com',
            'user_name' => 'Raju',
        );
        
        $result = @wp_remote_post('http://localhost/wordpress/wp-json/reviews-route/add_profile',
                    array(
                       'headers' => array('Content-Type' => 'application/json; charset=utf-8'),
                        'body'      => json_encode($body),
                        'method'    => 'POST'
                   )
                   
                ); 
        
       // print_r($result);die;
        $result = @wp_remote_get('http://localhost/wordpress/wp-json/reviews-route/get_reviews?api_key=54561616');        
        
        if(isset($result['body'])){
           $result = json_decode($result['body'],true);              
           $response = $this->saswp_save_paid_reviews_data($result, $location);
           return $response;
        }else{
           return null;
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
                
                $location   = '';
                
                $google_api = '';
                
                if(isset($_POST['location'])){
                    $location = sanitize_text_field($_POST['location']);
                }
                                                
                if($location){
                 
                  $api_key        = '56564646';
                  $api_key_status = 'valid';
                    
                  if($api_key && $api_key_status == 'valid'){
                        
                      $result = $this->saswp_get_paid_reviews_data($location);
                      
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