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
    
    
    public function saswp_get_reviews_schema_markup(){
                                        
                            global $sd_data; 
                        
                            $html = '';                                                                                                                                      														
                            $date 		= get_the_date("Y-m-d\TH:i:s\Z");
                            $modified_date 	= get_the_modified_date("Y-m-d\TH:i:s\Z");
			                                                                                                                                                 
                            $total_score = esc_attr(number_format((float)5, 2, '.', ''));
                            
                            $input1 = array(
                                    '@context'       => 'http://schema.org',
                                    '@type'          => 'Review',
                                    'dateCreated'    => esc_html($date),
                                    'datePublished'  => esc_html($date),
                                    'dateModified'   => esc_html($modified_date),
                                    'headline'       => saswp_get_the_title(),
                                    'name'           => saswp_get_the_title(),                                    
                                    'url'            => get_permalink(),
                                    'description'    => saswp_get_the_excerpt(),
                                    'copyrightYear'  => get_the_time( 'Y' ),                                                                                                           
                                    'author'	     => saswp_get_author_details()                                                                                        
                                    );
                                    
                                    $input1['itemReviewed'] = array(
                                            '@type' => 'Thing',
                                            'name'  => saswp_get_the_title(),
                                    );

                                    $input1['reviewRating'] = array(
                                        '@type'       => 'Rating',
                                        'worstRating' => 1,
                                        'bestRating'  => 5,
                                        'ratingValue' => esc_attr($total_score),                                        
                                     ); 
                                                                                                
                            if(!empty($input1)){
                                
                                $html .= "\n";
                                $html .= '<!-- Schema & Structured Data For Reviews v'.esc_attr(SASWP_VERSION).' - -->';
                                $html .= "\n";
                                $html .= '<script type="application/ld+json" class="saswp-reviews-markup">'; 
                                $html .= "\n";       
                                $html .= saswp_json_print_format($input1);       
                                $html .= "\n";
                                $html .= '</script>';
                                $html .= "\n\n";
                                
                            }        
                                                                                                        
                        return $html;              
    }
    
    public function saswp_reviews_html_markup($reviews){
        
        $output = '';
        if($reviews){
                        
            foreach ($reviews as $review){

                    $review_rating = $review['saswp_review_rating'];

                    $starating = saswp_get_rating_html_by_value($review_rating);

                    $term     = get_term( $review['saswp_review_platform'], 'platform' );

                    $output.= '<div class="saswp-g-review-panel">
                              <div class="saswp-glg-review-body">
                                <div class="saswp-rv-img">
                                    <img src="'.esc_url($review['saswp_reviewer_image']).'" alt="'.$review['saswp_reviewer_name'].'">
                                </div>
                                <div class="saswp-rv-cnt">
                                    <div class="saswp-str-rtng">
                                        <div class="saswp-str">
                                            <span class="saswp-athr">'.$review['saswp_reviewer_name'].'</span>
                                            '.$starating.'                                  
                                        </div> 
                                        <span class="saswp-g-plus">
                                            <a href="#"><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/'.$term->slug.'-img.png'.'"></a>
                                        </span>
                                    </div>                                                
                                    <p>'.substr($review['saswp_review_text'],0,300).'</p>
                                </div>
                              </div>
                          </div>';

                }

             wp_enqueue_style( 'saswp-style', SASWP_PLUGIN_URL . 'admin_section/css/saswp-style.min.css', false , SASWP_VERSION );       

            } 
        return $output;            
        
    }
    public function saswp_reviews_front_output($attr){
        
        
            $arg = array();
            $arg['post_type']      = 'saswp_reviews';
            $arg['posts_per_page'] = -1;
            $arg['post_status']    = 'publish';
            
            if(isset($attr['id'])){
              $arg['attachment_id']    = $attr['id'];  
            }
            if(isset($attr['title'])){
              $arg['title']    = $attr['title'];  
            }
            if(isset($attr['count'])){
                $arg['posts_per_page'] = $attr['count'];
            }
            
            $meta_query = array();
                    
            if(isset($attr['rating'])){
                    $meta_query[] = array(
                        'key'     => 'saswp_review_rating',
                        'value'   => $attr['rating'],
                        'compare' => '='
                    );
            }
            if(isset($attr['platform'])){
                $term     = get_term_by( 'slug', $attr['platform'], 'platform' );
                
                  $meta_query[] =   array(
                        'key'     => 'saswp_review_platform',
                        'value'   => $term->term_id,
                        'compare' => '='
                    );
            }
                        
            $meta_query_args = array(            
            array(
                'relation' => 'AND',
                 $meta_query 
                )
            );
            
            $arg['meta_query'] = $meta_query_args;
                                
            $posts_list = get_posts($arg); 
                 
            $reviews = array();
            
            $post_meta = array(              
              'saswp_reviewer_image',
              'saswp_reviewer_name',
              'saswp_review_rating',
              'saswp_review_date',
              'saswp_review_text',
              'saswp_review_link',
              'saswp_review_platform',
            );
        
            if($posts_list){
                foreach($posts_list as $post){
                
                $review_data = array();
                $post_id = $post->ID; 
                                
                foreach($post_meta as $meta_key){
                    
                    $review_data[$meta_key] = get_post_meta($post_id, $meta_key, true ); 
                    
                }
                   $reviews[] = $review_data;  
                }
            }
            $output = '';
            if($reviews){
               $output = $this->saswp_reviews_html_markup($reviews);    
            }
            
            return $output;
                                        
    }
    
    public function saswp_reviews_shortcode($attr){
                                        
        
        if(saswp_global_option()){
                
          $schema_markup = $this->saswp_get_reviews_schema_markup();
                
        }
        
        $response = $this->saswp_reviews_front_output($attr);
        
        if($schema_markup){
               $response = $response.$schema_markup;
               
        }
        
        return $response;
        
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
    
    public function saswp_get_paid_reviews_data($location, $api_key, $user_id, $blocks){
                            
        $body = array(                        
            'place_id'   => $location, 
            'user_id'    => $user_id, 
            'blocks'     => $blocks,
            'api_key'    => $api_key,
        );
        
        $server_url = 'http://localhost/wordpress/wp-json/reviews-route/add_profile';                 
        $result = @wp_remote_post($server_url,
                    array(
                        'method'      => 'POST',
                        'timeout'     => 45,
                        'redirection' => 5,
                        'httpversion' => '1.1',
                        'blocking'    => true,                        
                        'body'        => $body,                        
                    )                                      
                );                         
              
       if(wp_remote_retrieve_response_code($result) == 200 && wp_remote_retrieve_body($result)){
            
              $add_response = json_decode(wp_remote_retrieve_body($result), true); 
            
              if($add_response['status']){
                  
                  $server_url = 'http://localhost/wordpress/wp-json/reviews-route/get_reviews?api_key='.$api_key.'&place_id='.$location;   
                  $get_response = @wp_remote_get($server_url);
                  
                   if(isset($get_response['body'])){

                   $get_response = json_decode($get_response['body'],true); 

                   $response = $this->saswp_save_paid_reviews_data($get_response, $location);
                   
                   if($response){
                       return $add_response;
                   }else{
                       return array('status'=>false, 'message' => 'Data is not saved');
                   }
                   
                   } 
                                   
              }else{
                  return $add_response;
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
    
    public function saswp_get_free_reviews_data($place_id, $g_api){
                                                   
        $result = @wp_remote_get('https://maps.googleapis.com/maps/api/place/details/json?placeid='.trim($place_id).'&key='.trim($g_api));                
        
        if(isset($result['body'])){
            
           $result = json_decode($result['body'],true);   
           
           if($result['result']){
               
               $response = $this->saswp_save_free_reviews_data($result['result'], $place_id);
               
               if($response){
                    return array('status' => true, 'message' => 'fetched successfully');
               }else{                                             
                    return array('status' => false, 'message' => 'Not fetched');
               }
               
           }else{
               if($result['error_message']){
                   return array('status' => false, 'message' => $result['error_message']);
               }else{
                   return array('status' => false, 'message' => 'Something went wrong');
               }                             
           }
                                                       
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
                $location  = $blocks = $premium_status = $g_api = '';
                                                
                if(isset($_POST['location'])){
                    $location = sanitize_text_field($_POST['location']);
                }
                
                if(isset($_POST['g_api'])){
                    $g_api = sanitize_text_field($_POST['g_api']);
                }
                
                if(isset($_POST['blocks'])){
                    $premium_status = sanitize_text_field($_POST['premium_status']);
                }
                
                if(isset($_POST['blocks'])){
                    $blocks = intval($_POST['blocks']);
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
                  $api_key        = $sd_data['google_addon_license_key'];
                  $api_key_status = $sd_data['google_addon_license_key_status'];
                  $user_id        = get_option('google_addon_user_id');
                     
                  if($api_key && $api_key_status == 'active' && $user_id && $premium_status == 'premium'){                       
                      
                       $result = $this->saswp_get_paid_reviews_data($location, $api_key, $user_id, $blocks); 
                       
                       if($result['status'] && $result['message']){
                              update_option('google_addon_reviews_limits', intval($result['message']));
                       }
                       
                  }else{
                      
                      if($g_api){
                                                                              
                          $result = $this->saswp_get_free_reviews_data($location, $g_api);                                                                                                                                  
                      }                      
                      
                  }  
                                                        
                  echo json_encode($result);
                    
                }
                
                wp_die();
        
    }
	                       
}
$saswp_service_obj = new saswp_reviews_service();
$saswp_service_obj->saswp_service_hooks();
?>