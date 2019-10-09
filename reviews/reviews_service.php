<?php 
/**
 * Newsletter class
 *
 * @author   Magazine3
 * @category Admin
 * @path     reviews/reviews_service
 * @Version 1.9
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class saswp_reviews_service {
    
    /**
     * List of hooks used in this context
     */
    public function saswp_service_hooks(){
        add_action( 'wp_ajax_saswp_fetch_google_reviews', array($this,'saswp_fetch_google_reviews'));
        add_shortcode( 'saswp-reviews', array($this, 'saswp_reviews_shortcode' ));
    }
    
    /**
     * Function to get reviews schema markup
     * @global type $sd_data
     * @return string
     */
    public function saswp_get_reviews_schema_markup($reviews){
                            
                            $sumofrating = 0;
                            $avg_rating  = 1;
                            $reviews_arr = array();
                            $input1      = array();
                            
                            if($reviews){
                                
                                foreach($reviews as $rv){
                                                                        
                                    $sumofrating += $rv['saswp_review_rating'];
                                    
                                    if($rv['saswp_review_rating'] && $rv['saswp_reviewer_name']){
                                        
                                        $reviews_arr[] = array(
                                            '@type'         => 'Review',
                                            'author'        => $rv['saswp_reviewer_name'],
                                            'datePublished' => $rv['saswp_review_date'],
                                            'description'   => $rv['saswp_review_text'],
                                            'reviewRating'  => array(
                                                        '@type'       => 'Rating',
                                                        'bestRating'  => 5,
                                                        'ratingValue' => $rv['saswp_review_rating'],
                                                        'worstRating' => 1
                                            ),
                                       );
                                        
                                    }
                                    
                                }
                                
                                    if($sumofrating> 0){
                                      $avg_rating = $sumofrating /  count($reviews); 
                                    }
                                
                                    if(!empty($reviews_arr)){
                                       
                                        $input1['review'] = $reviews_arr;
                                        
                                    }

                                    $input1['aggregateRating'] = array(
                                        '@type'       => 'AggregateRating',
                                        'reviewCount' => count($reviews),
                                        'ratingValue' => esc_attr($avg_rating),                                        
                                     );
                                
                                }
                            return $input1;                                      
                        
    }
    
    /**
     * Function to generate reviews html
     * @param type $reviews
     * @return string
     */
    public function saswp_reviews_html_markup($reviews){
        
        $output = '';
        if($reviews){
                        
            foreach ($reviews as $review){

                    $review_rating = $review['saswp_review_rating'];

                    $starating = saswp_get_rating_html_by_value($review_rating);

                        $term      = get_term( $review['saswp_review_platform'], 'platform' );
                        $term_slug  = ''; 
                    
                        if(is_object($term)){
                            $term_slug = $term->slug; 
                        }
                        
                        $img_src = SASWP_DIR_URI.'/admin_section/images/default_user.jpg';
                                                
                        if(isset($review['saswp_reviewer_image']) && $review['saswp_reviewer_image'] !=''){
                            $img_src = $review['saswp_reviewer_image'];
                        }
                                                                        
                        $output.= '<div class="saswp-g-review-panel">
                              <div class="saswp-glg-review-body">
                                <div class="saswp-rv-img">
                                    <img src="'.esc_url($img_src).'" alt="'.esc_attr($review['saswp_reviewer_name']).'">
                                </div>
                                <div class="saswp-rv-cnt">
                                    <div class="saswp-str-rtng">
                                        <div class="saswp-str">
                                            <span class="saswp-athr">'.esc_attr($review['saswp_reviewer_name']).'</span>
                                            '.$starating.'                                  
                                        </div> 
                                        <span class="saswp-g-plus">
                                            <a target="_blank" href="'.esc_attr($review['saswp_review_link']).'"><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/'.esc_attr($term_slug).'-img.png'.'"></a>
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
                
                $location  = $blocks = $premium_status = $g_api = $reviews_api = $reviews_api_status = '';
                
                if(isset($_POST['reviews_api'])){
                    $reviews_api = sanitize_text_field($_POST['reviews_api']);
                }
                
                if(isset($_POST['reviews_api_status'])){
                    $reviews_api_status = sanitize_text_field($_POST['reviews_api_status']);
                }
                                
                if(isset($_POST['location'])){
                    $location = sanitize_text_field($_POST['location']);
                }
                
                if(isset($_POST['g_api'])){                    
                    $g_api = sanitize_text_field($_POST['g_api']);                                        
                }
                
                if(isset($_POST['premium_status'])){
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
                                      
                   if(isset($sd_data['saswp_reviews_location_blocks'])){
                          
                       if(!in_array($blocks, $sd_data['saswp_reviews_location_blocks'])){
                           array_push($sd_data['saswp_reviews_location_blocks'], $blocks);                       
                       }
                                              
                   }else{
                       
                           $sd_data['saswp_reviews_location_blocks'] = array($blocks);  
                       
                   }
                        
                  $sd_data['saswp-google-review']        = 1;
                  $sd_data['saswp_google_place_api_key'] = $g_api;
                  update_option('sd_data', $sd_data);    
                                    
                  $result         = null;                                    
                  $user_id        = get_option('reviews_addon_user_id');
                    
                  if($reviews_api){                       
                        
                      if($premium_status == 'premium'){
                        
                        if($reviews_api_status == 'active'){
                          
                            if($user_id){
                             
                                if(function_exists('saswp_get_paid_reviews_data')){

                                $result = saswp_get_paid_reviews_data($location, $reviews_api, $user_id, $blocks); 

                                if($result['status'] && is_numeric($result['message'])){
                                    
                                    $rv_limits = get_option('reviews_addon_reviews_limits');
                                    
                                    $result['message'] = 'Reviews fetched : '. $rv_limits - $result['message']. ', Remains Limit : '.$result['message'];                                    
                                    
                                    update_option('reviews_addon_reviews_limits', intval($result['message']));
                                }

                                }else{
                                    $result['status']  = false;
                                    $result['message'] = esc_html__( 'Reviews for schema plugin is not activated', 'schema-and-structured-data-for-wp' );
                                }
                                
                            }else{
                                $result['status']  = false;
                                $result['message'] = esc_html__( 'User is not register', 'schema-and-structured-data-for-wp' );
                            }                                                        
                            
                        }else{
                                $result['status']  = false;
                                $result['message'] = esc_html__( 'License key is not active', 'schema-and-structured-data-for-wp' );
                        }  
                                                  
                        
                      }else{
                          
                          if($g_api){
                                                                          
                             $result = $this->saswp_get_free_reviews_data($location, $g_api);                                                                                                                                  
                             
                         }
                         
                      }
                                              
                  }else{
                      
                      if($g_api){
                                                                              
                          $result = $this->saswp_get_free_reviews_data($location, $g_api);                                                                                                                                  
                      }                      
                      
                  }  
                                                             
                  echo json_encode($result);
                    
                }else{
                    
                  echo json_encode(array('status' => false, 'message' => esc_html__( 'Place id is empty', 'schema-and-structured-data-for-wp' ))); 
                  
                }
                
            wp_die();
        
    }
    
    public function saswp_get_reviews_by_attr($attr){
        
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
        
        return $reviews;
        
    }

    /**
     * Function to show value using shortcode "saswp-reviews"
     * @param type $attr
     * @return type
     */
    public function saswp_reviews_front_output($attr){
        
            global $sd_data;
            $reviews = $this->saswp_get_reviews_by_attr($attr);
                        
            $output = $html = '';
            
            if($reviews){
                
               $output = $this->saswp_reviews_html_markup($reviews);  
                            
               if(saswp_global_option()){
                
                 $rv_markup = $this->saswp_get_reviews_schema_markup($reviews);
                 
                 if($rv_markup){
                                          
                        $input1['@context'] = saswp_context_url();
                        $input1['@type']    = (isset($sd_data['saswp_organization_type']) && $sd_data['saswp_organization_type'] !='' )? $sd_data['saswp_organization_type'] : 'Organization';
                        $input1['name']     = (isset($sd_data['sd_name']) && $sd_data['sd_name'] !='' )? $sd_data['sd_name'] : get_bloginfo();
                                          
                        $input1  = $input1 + $rv_markup;
                      
                        $html .= "\n";
                        $html .= '<!-- Schema & Structured Data For Reviews v'.esc_attr(SASWP_VERSION).' - -->';
                        $html .= "\n";
                        $html .= '<script type="application/ld+json" class="saswp-reviews-markup">'; 
                        $html .= "\n";       
                        $html .= saswp_json_print_format($input1);       
                        $html .= "\n";
                        $html .= '</script>';
                        $html .= "\n\n";
                      
                      $output = $output.$html;

                  }
          
                }
                              
            }
            
            return $output;
                                        
    }
    
    public function saswp_reviews_shortcode($attr){
                                                        
        $response = $this->saswp_reviews_front_output($attr);
                                               
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
                        'saswp_rvs_loc_id'                 => $result['place_id'],      
                        'saswp_rvs_loc_review_count'       => $result['user_ratings_total'], 
                        'saswp_rvs_loc_avg_rating'         => $result['rating'],
                        'saswp_rvs_loc_icon'               => $result['icon'],
                        'saswp_rvs_loc_address'            => $result['formatted_address'],
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
    
    public function saswp_get_free_reviews_data($place_id, $g_api){
                                                   
        $result = @wp_remote_get('https://maps.googleapis.com/maps/api/place/details/json?placeid='.trim($place_id).'&key='.trim($g_api));                
        
        if(isset($result['body'])){
            
           $result = json_decode($result['body'],true);   
           
           if($result['result']){
               
               $response = $this->saswp_save_free_reviews_data($result['result'], $place_id);
               
               if($response){
                    return array('status' => true, 'message' => esc_html__( 'Fetched Successfully', 'schema-and-structured-data-for-wp' ));
               }else{                                             
                    return array('status' => false, 'message' => esc_html__( 'Not fetched', 'schema-and-structured-data-for-wp' ));
               }
               
           }else{
               if($result['error_message']){
                   return array('status' => false, 'message' => $result['error_message']);
               }else{
                   return array('status' => false, 'message' => esc_html__( 'Something went wrong', 'schema-and-structured-data-for-wp' ));
               }                             
           }
                                                       
        }else{
           return null;
        }        
                                            
    }
        	                      
}

$saswp_service_obj = new saswp_reviews_service();
$saswp_service_obj->saswp_service_hooks();
?>