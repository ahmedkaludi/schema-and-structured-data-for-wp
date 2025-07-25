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

class SASWP_Reviews_Service {
        
    /**
     * List of hooks used in this context
     */
    public function saswp_service_hooks() {
        
        add_action('wp_ajax_saswp_fetch_google_reviews', array($this,'saswp_fetch_google_reviews'));
        add_shortcode('saswp-reviews', array($this, 'saswp_reviews_shortcode' ),10);  
        add_action('admin_init', array($this, 'saswp_import_reviews_from_csv' ),9);
        
    }
    
    public function saswp_get_reviews_list_by_design($design, $platform_id, $total_reviews, $sorting, $stars_color,$collection_review_imag=''){
        
        $badge_collection = array();
        $collection       = array();
        $attr             = array();
        
        switch ($design) {
            
            case 'grid':                                
                $attr['in'] = $total_reviews;
                $collection = $this->saswp_get_reviews_list_by_parameters($attr,null,null,null,null,null,$collection_review_imag); 
                $collection = $this->saswp_sort_collection($collection, $sorting);     
                break;
            case 'gallery':              
            case 'badge':                                
            case 'popup':                
            case 'fomo':
                
                if($platform_id){

                    foreach ( $platform_id as $key => $val){

                        $reviews_list = $this->saswp_get_reviews_list_by_parameters(null, $key, $val,$stars_color,null,null,$collection_review_imag); 
                        $badge_collection[] = $reviews_list;

                        if($reviews_list){

                            $collection = array_merge($collection, $reviews_list);
                        }

                    }

                }
                
                if($design == 'badge'){
                    $collection = $badge_collection;
                }
                $collection = $this->saswp_sort_collection($collection, $sorting);                
                break;

            default:
                break;
        }
                
        return $collection;        
    }
    
    public function saswp_get_collection_list($colcount = null, $paged = null, $offset = null){
        
            $response  = array();
        
            $arg['post_type']      = 'saswp-collections';
            $arg['numberposts']    = -1;
            $arg['post_status']    = 'publish';
            
            if($colcount){
                $arg['numberposts']    = $colcount;
            }
            if($paged){
                $arg['paged']    = $paged;
            }
            if($offset){
                $arg['offset']    = $offset;
            }
            
            $collection = get_posts( $arg );
            
            if($collection){

                $col_opt = array(); 

                foreach( $collection as $col){

                   $col_opt[] = array(
                       'value' => $col->ID,
                       'label' => $col->post_title
                   );

                }

               $response  = $col_opt;

            }
          return $response;             
    }
    
    public function saswp_review_form_process_data($form_data){
        
                $rv_image = '';
                $postarr = array();
                
                if( (function_exists('is_user_logged_in') && is_user_logged_in() ) && function_exists('wp_get_current_user') ){
                    
                     $current_user = wp_get_current_user();
                     $postarr['post_author'] = $current_user->ID;
                     $rv_image     = get_avatar_url($current_user->ID, array('size' => 300));                     
                    
                }
                
                $rv_text     = saswp_sanitize_textarea_field($form_data['saswp_review_text']);
                $rv_name     = sanitize_text_field($form_data['saswp_reviewer_name']);
                $rv_rating   = floatval($form_data['saswp_review_rating']);  
                $rv_place_id = intval($form_data['saswp_place_id']);  
                $rv_link     = sanitize_text_field($form_data['saswp_review_link']);
                $rv_date     = gmdate('Y-m-d');
                $rv_time     = gmdate("h:i:sa");
                                
                if($rv_rating){
                    
                    $postarr = array(                                                                           
                    'post_title'            => $rv_name,                    
                    'post_status'           => 'pending',                                                            
                    'post_name'             => $rv_name,                                                            
                    'post_type'             => 'saswp_reviews',
                                                                             
                );
                // Data is sanitized at the top of this function                        
                $post_id = wp_insert_post(  $postarr );    
                    
                $term     = get_term_by( 'slug','self', 'platform' );   
                
                if ( ! empty( $rv_image) ) {
                    
                    $image_details = saswp_get_attachment_details($rv_image);   
                    if((isset($image_details[0]) && isset($image_details[0][0])) && (isset($image_details[0]) && isset($image_details[0][1])) ) {
                        $media_detail = array(                                                    
                            'width'      => $image_details[0][0],
                            'height'     => $image_details[0][1],
                            'thumbnail'  => $rv_image,
                        );
                    }else{
                        $media_detail = "";
                    }
                    
                }else{
                    $rv_image = "";
                }
                
                $review_meta = array(
                        'saswp_review_platform'       => $term->term_id,
                        'saswp_review_location_id'    => $rv_place_id,
                        'saswp_review_time'           => $rv_time,
                        'saswp_review_date'           => $rv_date,
                        'saswp_review_rating'         => $rv_rating,
                        'saswp_review_text'           => $rv_text,                                
                        'saswp_reviewer_lang'         => null,
                        'saswp_reviewer_name'         => $rv_name,
                        'saswp_review_link'           => $rv_link,
                        'saswp_reviewer_image'        => $rv_image ? $rv_image : SASWP_DIR_URI.'/admin_section/images/default_user.jpg',
                        'saswp_reviewer_image_detail' => $media_detail
                );
                                   
                if($post_id && !empty($review_meta) && is_array($review_meta) ) {
                                        
                    foreach ( $review_meta as $key => $val){                     
                        update_post_meta($post_id, $key, $val);  
                    }
            
                 }
                    
                }
                
                return $post_id;
        
    }                


    function dateDiffInDays($date1, $date2) 
    {
        // Calculating the difference in timestamps
        $diff = strtotime($date2) - strtotime($date1);
    
        // 1 day = 24 hours
        // 24 * 60 * 60 = 86400 seconds
        return abs(round($diff / 86400));
    }

    function saswp_getDaysDiff($time)
        {

            $time = time() - $time; // to get the time since that moment
            $time = ($time<1)? 1 : $time;
            $tokens = array (
                31536000 => 'year',
                2592000 => 'month',
                604800 => 'week',
                86400 => 'day',
                3600 => 'hour',
                60 => 'minute',
                1 => 'second'
            );

            foreach ( $tokens as $unit => $text) {
                if ($time < $unit) continue;
                $numberOfUnits = floor($time / $unit);
                $adAgo = $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
                return $adAgo.' ago';   
            }

        }
  

    /**
     * Function to generate reviews html
     * @param type $reviews
     * @return string
     */
    public function saswp_reviews_html_markup($reviews){

        global $sd_data;   

      

        $output = '';

        if($reviews){
                        
            foreach ( $reviews as $review){
                if ( isset( $sd_data['saswp_date_format']) && $sd_data['saswp_date_format'] == 'days'){

                    if($sd_data['saswp_date_format'] == 'days'){
                        
                        $curr_date = gmdate("Y-m-d"); // Start date
                        $interval = $review['saswp_review_date']; // End date

            
                        // Function call to find date difference
                        $dateDiffInDays =  $this->dateDiffInDays($interval, $curr_date);

                        if($dateDiffInDays > 1){
                            $days_ago_format = $dateDiffInDays.' Days ago';
                        }else{
                            $days_ago_format = $dateDiffInDays.' Day ago';

                        }
            
                    }   
                    
                   
                }           
                if ( ! empty( $sd_data['saswp_date_format']) && $sd_data['saswp_date_format'] == 'default'){
                    $days_ago_format = gmdate('d-m-Y',strtotime($review['saswp_review_date']));
                }else{
                    $days_ago_format = "";
                }  
                        
                        $review_rating = $review['saswp_review_rating'];

                        $starating = saswp_get_rating_html_by_value($review_rating);
                        if ( ! empty( $starating) ) {
                            $starating = $starating;
                        }else{
                            $starating = "";
                        }
                                                                                                                   
                        $img_src = SASWP_DIR_URI.'/admin_section/images/default_user.jpg';
                                                
                        if ( isset( $review['saswp_reviewer_image']) && $review['saswp_reviewer_image'] !='' ) {
                            $img_src = $review['saswp_reviewer_image'];
                        }

                        $link = '';

                        if ( ! empty( $review['saswp_review_location_id']) ) {
                            $link = $review['saswp_review_location_id'];
                        }else{
                            $link = $review['saswp_review_link'];
                        }

                        if($review['saswp_review_platform_name'] == 'Google'){
                            $link = $review['saswp_review_link'];
                        }  

                        if($review['saswp_review_platform_name'] == 'ProductReview'){
                            $link = 'https://www.productreview.com.au/listings/'.$review['saswp_review_location_id'];
                        }     
                                                                        
                        $output.= '<div class="saswp-g-review-panel">
                              <div class="saswp-glg-review-body">
                                <div class="saswp-rv-img">
                                    <img width="100%" height="auto" loading="lazy" src="'. esc_url( $img_src).'" alt="'. esc_attr( $review['saswp_reviewer_name']).'">
                                </div>
                                <div class="saswp-rv-cnt">
                                    <div class="saswp-r5-rng">
                                        <div class="saswp-str">
                                            <a target="_blank" href="'. esc_url( $link).'"><span class="saswp-athr">'.esc_html( $review['saswp_reviewer_name']).'</span></a>
                                            '.$starating.'
                                            <div>'.(($days_ago_format) ? esc_attr( $days_ago_format) : '').'</div>                                  
                                        </div> 
                                        <span class="saswp-g-plus">
                                            <a target="_blank" href="'. esc_url( $link).'"><img alt="'. esc_attr( $review['saswp_reviewer_name']).'" width="20" height="20" src="'. esc_url( $review['saswp_review_platform_icon']).'"></a>
                                        </span>
                                    </div>                                                
                                   <div class="saswp-rv-txt"> <p>'.wp_strip_all_tags(html_entity_decode($review['saswp_review_text'])).'</p></div>
                                </div>
                              </div>
                          </div>';
                                                                
                }

             wp_enqueue_style( 'saswp-style', SASWP_PLUGIN_URL . 'admin_section/css/'.(SASWP_ENVIRONMENT == 'production' ? 'saswp-style.min.css' : 'saswp-style.css'), false , SASWP_VERSION );       

            } 
        return $output;            
        
    }
    
    public function saswp_import_reviews_from_csv() {
        
        if ( ! current_user_can( saswp_current_user_can() ) ) {
            return;
        }
       
       global $wpdb;
       
       $result          = null;
       $errorDesc       = array();       
       $reviews_arr     = array();
       $place_id        = 'upload_by_csv';
       $url             = get_option('saswp_rv_csv_upload_url');
       
       if($url && $url != '' ) {
        //phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen -- We are not able to find a proper method to open and read csv file using wp_filesystem.
        $handle = fopen($url, "r");

        if($handle){
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Reason: Just starting transaction            
        $wpdb->query('START TRANSACTION');    
        $counter = 0;        
        
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            
            // Skip the first row as is likely column names
            if ($counter === 0) {
                $counter++;
                continue;
            }    
            if(empty($data[5]) ) {
                return false;
            }
            $reviews_arr   = array();
            $reviews_arr[] = array(
                'author_name'           => $data[0],
                'author_url'            => $data[1],
                'profile_photo_url'     => $data[2],                                                
                'date'                  => $data[3],
                'time'                  => isset($data[3]) ? $data[3] : null,
                'rating'                => $data[5],
                'title'                 => $data[6],
                'text'                  => $data[7],
                'platform'              => $data[8],
                'language'              => isset($data[9]) ? $data[9] : null
            );
            if ( ! empty( $data[10]) ) {
                $place_id =    $data[10];        
            }
            $reviews_total            = array();
            $reviews_total['reviews'] = $reviews_arr;
            $result                   = $this->saswp_save_free_reviews_data($reviews_total, $place_id);
            
            if(is_wp_error($result) ) {
                $errorDesc[] = $result->get_error_message();
            }
        }    
                 
        update_option('saswp_rv_csv_upload_url','');                                            
        if ( count($errorDesc) ){
            echo esc_html( implode("\n<br/>", $errorDesc));              
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Reason: Just rollbacking transaction                        
            $wpdb->query('ROLLBACK');             
        }else{
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Reason: Just commiting transaction            
            $wpdb->query('COMMIT'); 
            return true;
        }
        }   
      }

    }
    public function saswp_fetch_google_reviews() {
                
                if ( ! current_user_can( saswp_current_user_can() ) ) {
                    return;
                }
        
                if ( ! isset( $_POST['saswp_security_nonce'] ) ){
                    return; 
                }
                // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: Nonce verification done here so unslash is not used.
                if ( !wp_verify_nonce( $_POST['saswp_security_nonce'], 'saswp_ajax_check_nonce' ) ){
                   return;  
                }
                
                global $sd_data;
                
                $location  = $blocks = $premium_status = $g_api = $reviews_api = $reviews_api_status = $language = '';
                
                if ( isset( $_POST['reviews_api']) ) {
                    $reviews_api = sanitize_text_field( wp_unslash( $_POST['reviews_api'] ) );
                }
                
                if ( isset( $_POST['reviews_api_status']) ) {
                    $reviews_api_status = sanitize_text_field( wp_unslash( $_POST['reviews_api_status'] ) );
                }
                                
                if ( isset( $_POST['location']) ) {
                    $location = sanitize_text_field( wp_unslash( $_POST['location'] ) );
                }
                if ( isset( $_POST['language']) ) {
                    $language = sanitize_text_field( wp_unslash( $_POST['language'] ) );
                }
                
                if ( isset( $_POST['g_api']) ) {                    
                    $g_api = sanitize_text_field( wp_unslash( $_POST['g_api'] ) );                                        
                }
                
                if ( isset( $_POST['premium_status']) ) {
                    $premium_status = sanitize_text_field( wp_unslash( $_POST['premium_status'] ) );
                }
                
                if ( isset( $_POST['blocks']) ) {
                    $blocks = intval($_POST['blocks']);
                }
                                                
                if($location){
                    
                   if ( isset( $sd_data['saswp_reviews_location_name']) ) {
                          
                       if(!in_array($location, $sd_data['saswp_reviews_location_name']) ) {
                           array_push($sd_data['saswp_reviews_location_name'], $location);                       
                       }
                                              
                   }else{
                       $sd_data['saswp_reviews_location_name'] = array($location);  
                       
                   }
                                      
                   if ( isset( $sd_data['saswp_reviews_location_blocks']) ) {
                          
                       if(!in_array($blocks, $sd_data['saswp_reviews_location_blocks']) ) {
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
                             
                                if ( function_exists( 'saswp_get_paid_reviews_data') ) {

                                $result = saswp_get_paid_reviews_data($location, $reviews_api, $user_id, $blocks); 

                                if($result['status'] && is_numeric($result['message']) ) {
                                    
                                    $rv_limits = get_option('reviews_addon_reviews_limits');
                                    
                                    $result['message'] = esc_html__( 'Reviews fetched', 'schema-and-structured-data-for-wp' ) .' : '. ($rv_limits - $result['message'] ). ', '. esc_html__( 'Remains Limit', 'schema-and-structured-data-for-wp' ) .' : '.$result['message'];                                    
                                    
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
                                                                          
                             $result = $this->saswp_get_free_reviews_data($location, $g_api, $language);                                                                                                                                  
                             
                         }
                         
                      }
                                              
                  }else{
                      
                      if($g_api){
                                                                              
                          $result = $this->saswp_get_free_reviews_data($location, $g_api, $language);                                                                                                                                  
                      }                      
                      
                  }  
                                                             
                  echo wp_json_encode($result);
                    
                }else{
                    
                  echo wp_json_encode(array('status' => false, 'message' => esc_html__( 'Place id is empty', 'schema-and-structured-data-for-wp' ))); 
                  
                }
                
            wp_die();
        
    }
        
    /**
     * Function to show value using shortcode "saswp-reviews"
     * @param type $attr
     * @return type
     */
        
    public function saswp_reviews_shortcode($attr){
                            
        $response = '';
        if ( isset( $attr['id']) ) {
            $attr['id'] = intval($attr['id']);
            if($attr['id'] <= 0){
                return $response;
            }
        }
        
        $reviews = $this->saswp_get_reviews_list_by_parameters($attr);
        
        if($reviews){
               global $saswp_post_reviews;
               $saswp_post_reviews = array_merge($saswp_post_reviews, $reviews);    
               $response = $this->saswp_reviews_html_markup($reviews);                                                                                         
        }
                                           
        return $response;
        
    }
                            
    public function saswp_save_free_reviews_data($result, $place_id) {
                
        $place_saved   = array();
        $reviews_saved = array();
        
        if (isset($result['place_id']) && $result['place_id'] != '') {
                                                                   
                $user_id     = get_current_user_id();
                $postarr = array(
                    'post_author'           => intval($user_id),          
                    'post_title'            => sanitize_text_field($result['name']),                    
                    'post_status'           => 'publish',                                                            
                    'post_name'             => sanitize_text_field($result['name']),
                    'post_type'             => 'saswp_rvs_location',
                                                                             
                );
                // Data is sanitized at the top of this function    
                $post_id = wp_insert_post(  $postarr );   
                $place_saved[] = $post_id;                                                  
                $review_meta = array(
                        'saswp_rvs_loc_id'                 => sanitize_text_field($result['place_id']),      
                        'saswp_rvs_loc_review_count'       => sanitize_text_field($result['user_ratings_total']), 
                        'saswp_rvs_loc_avg_rating'         => sanitize_text_field($result['rating']),
                        'saswp_rvs_loc_icon'               => esc_url($result['icon']),
                        'saswp_rvs_loc_address'            => sanitize_textarea_field($result['formatted_address']),
                );

                if($post_id && !empty($review_meta) && is_array($review_meta) ) {
                                        
                    foreach ( $review_meta as $key => $val){                     
                        update_post_meta($post_id, $key, $val);  
                    }
            
                 }
                            
        }
        
                                            
        if (isset($result['reviews'])) {
            
            $reviews = $result['reviews'];
            
            foreach ( $reviews as $review) {
               
                $user_id     = get_current_user_id();
                $postarr = array(
                    'post_author'           => intval($user_id),                                                            
                    'post_title'            => isset($review['title']) ? sanitize_text_field($review['title']) : sanitize_text_field($review['author_name']),
                    'post_status'           => 'publish',                                                            
                    'post_name'             => 'Default Review',                                                            
                    'post_type'             => 'saswp_reviews',
                                                                             
                );
                // Data is sanitized at the top of this function   
                $post_id = wp_insert_post(  $postarr );   
                $reviews_saved[] = $post_id;

                if ( isset( $review['platform']) && $review['platform'] != '' ) {
                    $term     = get_term_by( 'slug',$review['platform'], 'platform' );
                    if ( ! isset( $term->term_id) ) {
                        $term     = get_term_by( 'slug','self', 'platform' );
                    }
                }else{
                    $term     = get_term_by( 'slug','google', 'platform' );
                }                               
                
                $media_detail = array();
                
                if ( isset( $review['profile_photo_url']) && $review['profile_photo_url'] != '' ) {
                    
                    $image_details = saswp_get_attachment_details(array($review['profile_photo_url']));   
                    if((isset($image_details[0]) && isset($image_details[0][0])) && (isset($image_details[0]) && isset($image_details[0][1])) ) {
                        $media_detail = array(                                                    
                            'width'      => intval($image_details[0][0]),
                            'height'     => intval($image_details[0][1]),
                            'thumbnail'  => esc_url($review['profile_photo_url']),
                        );
                    }
                    
                }                
                
                $review_meta = array(
                        'saswp_review_platform'       => intval($term->term_id),
                        'saswp_review_location_id'    => sanitize_text_field($place_id),
                        'saswp_review_time'           => sanitize_text_field($review['time']),
                        'saswp_review_date'           => sanitize_text_field($review['date']),
                        'saswp_review_rating'         => sanitize_text_field($review['rating']),
                        'saswp_review_text'           => sanitize_textarea_field($review['text']),                                
                        'saswp_reviewer_lang'         => sanitize_text_field($review['language']),
                        'saswp_reviewer_name'         => sanitize_text_field($review['author_name']),
                        'saswp_review_link'           => isset($review['author_url']) ? esc_url($review['author_url']) : null,
                        'saswp_reviewer_image'        => isset($review['profile_photo_url']) ? $review['profile_photo_url'] : SASWP_DIR_URI.'/admin_section/images/default_user.jpg',
                        'saswp_reviewer_image_detail' => $media_detail
                );

                if($post_id && !empty($review_meta) && is_array($review_meta) ) {
                                        
                    foreach ( $review_meta as $key => $val){                     
                        update_post_meta($post_id, $key, $val);  
                    }
            
                }
                
            }
        }
        
        if ( ! empty( $place_saved) || !empty($reviews_saved) ) {
            return true;
        }else{
            return false;
        }
                
    }
    
    public function saswp_get_free_reviews_data($place_id, $g_api, $language = null){

        $api_endpoint = 'https://maps.googleapis.com/maps/api/place/details/json?placeid='.trim($place_id).'&key='.trim($g_api);
        if($language){
            $api_endpoint .= '&language='.trim($language);     
        }

        $result = wp_remote_get($api_endpoint);                
                        
        if ( isset( $result['body']) ) {
            
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
    
    public function saswp_get_reviews_list_by_parameters($attr = null, $platform_id = null, $rvcount = null, $paged = null, $offset = null,$stars_color = null, $collection_review_imag=null, $platform_place='all'){
                        
            $response   = array();                                
            $arg        = array();
            $meta_query = array();
            
            $arg['post_type']      = 'saswp_reviews';
            $arg['numberposts']    = -1;
            $arg['post_status']    = 'publish';
                        
            if($attr){
            
            if ( isset( $attr['in']) ) {
              $arg['post__in']    = $attr['in'];  
            }                    
            if ( isset( $attr['id']) ) {
              $arg['attachment_id']    = $attr['id'];  
            }
            if ( isset( $attr['title']) ) {
              $arg['title']    = $attr['title'];  
            }
            if ( isset( $attr['count']) ) {
                $arg['posts_per_page'] = $attr['count'];
            }    
            
            if ( isset( $attr['place_id']) ) {
                    $meta_query[] = array(
                        'key'     => 'saswp_review_location_id',
                        'value'   => $attr['place_id'],
                        'compare' => '='
                    );
            }
            
            if ( isset( $attr['rating']) ) {
                    $meta_query[] = array(
                        'key'     => 'saswp_review_rating',
                        'value'   => $attr['rating'],
                        'compare' => '='
                    );
            }
            if ( isset( $attr['platform']) ) {
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
            // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
            $arg['meta_query'] = $meta_query_args;    
            }
            
            if($rvcount){
                $arg['numberposts']    = $rvcount;
            }
            if($paged){
                $arg['paged']    = $paged;
            }
            if($offset){
                $arg['offset']    = $offset;
            }
            
            if($platform_id){

                 $meta_query = array();

                 $meta_query[] =   array(
                                'key'     => 'saswp_review_platform',
                                'value'   => $platform_id,
                                'compare' => '==',
                            );

                 if ( isset( $attr['q']) ) {
                    $meta_query[] =   array(
                            'key'     => 'saswp_reviewer_name',
                            'value'   => $attr['q'],
                            'compare' => 'LIKE'
                        );                    
                 }

                 if ( isset( $platform_place) && $platform_place != 'all'){
                    $meta_query[] =   array(
                            'key'     => 'saswp_review_location_id',
                            'value'   => $platform_place,
                            'compare' => '=='
                        );                    
                 }

                 $meta_query_args = array(            
                    array(
                        'relation' => 'AND',
                            $meta_query 
                        )
                    );

                 // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
                 $arg['meta_query'] = $meta_query_args;                                 
            }
                        
            $posts_list = get_posts($arg); 
                     
            if($posts_list){
            
             $post_meta = array(                     
              'saswp_reviewer_image',
              'saswp_reviewer_name',
              'saswp_review_rating',
              'saswp_review_date',
              'saswp_review_text',
              'saswp_review_link',
              'saswp_review_platform',
              'saswp_review_platform_icon',
              'saswp_review_platform_name',
              'saswp_review_location_id'   
            );
             
             $service_object     = new SASWP_Output_Service();
            
            foreach( $posts_list as $rv_post){
                $review_data = array();                
                
                $review_data['saswp_review_id'] = $rv_post->ID;
                foreach( $post_meta as $meta_key){
                    
                    $review_data[$meta_key] = get_post_meta($rv_post->ID, $meta_key, true ); 
                                                                               
                }
                
                if(!$review_data['saswp_reviewer_image']){
                    $review_data['saswp_reviewer_image'] = SASWP_DIR_URI.'/admin_section/images/default_user.jpg';
                }

                if ( ! empty( $collection_review_imag) ) {
                    if ( isset( $review_data['saswp_reviewer_image']) ) {
                        if(strpos($review_data['saswp_reviewer_image'], 'default_user') !== false){
                            $review_data['saswp_reviewer_image'] = $collection_review_imag;
                        }
                    }else{
                        $review_data['saswp_reviewer_image'] = $collection_review_imag;
                    }
                }

                $term     = get_term( $review_data['saswp_review_platform'], 'platform' );  
                
                if(!$review_data['saswp_review_platform_icon']){
                                        
                    if ( isset( $term->slug) ) {
                        
                        if($term->slug == 'self'){
                                                         
                            $default_logo       = $service_object->saswp_get_publisher(true);  
                            
                            if ( isset( $default_logo['url']) ) {
                                
                                $review_data['saswp_review_platform_icon'] = $default_logo['url'];
                                
                            }
                            
                        }else{
                            $review_data['saswp_review_platform_icon'] = SASWP_PLUGIN_URL.'admin_section/images/reviews_platform_icon/'. esc_attr( $term->slug).'-img.png';
                        }
                        
                    }

                }
                
                if(!$review_data['saswp_review_platform_name']){
                    if ( isset( $term->name) ) {
                        $review_data['saswp_review_platform_name'] = $term->name;
                    }
                }
                
                   $review_data['saswp_review_post_id'] = $rv_post->ID;
                   $response[] = $review_data;  
            }
            
        }
                                      
        return $response;
    }
    
    public function saswp_sort_collection($collection, $sorting){
             
         if($collection){
               
               switch($sorting){
                    
                case 'lowest':
                    
                        usort($collection, function($a, $b) {                            
                                return (((float)$a['saswp_review_rating']) - ((float)$b['saswp_review_rating']));
                        });
                                                
                        break;
                    
                case 'highest':
                    
                        usort($collection, function($a, $b) {
                                return ( ((float)$a['saswp_review_rating']) - ((float)$b['saswp_review_rating']));
                        });
                        
                        $collection = array_reverse($collection);
                        
                        break;
                        
               case 'newest':
               case 'recent':
                   
                        usort($collection, function($a, $b) {                            
                                return strtotime($a['saswp_review_date']) - strtotime($b['saswp_review_date']);                            
                            
                        });
                        
                        $collection = array_reverse($collection);
                                                                                                             
                    break;
                    
               case 'oldest':
                   
                        usort($collection, function($a, $b) {                            
                                return strtotime($a['saswp_review_date']) - strtotime($b['saswp_review_date']);                                                        
                        });
                                                                                                                                                           
                    break; 
                
                case 'random':
                    
                       shuffle($collection);
                                                                                                                  
                    break;
                    
                }
               
           }
                
           return $collection;
                   
    }
    
    public function saswp_convert_datetostring($date_str, $date_format = ''){
        
        $response = array();
        
        if($date_str){
            if ( ! empty( $date_format) ) {
                $response['date'] = gmdate($date_format, strtotime($date_str));
            }else{
                $response['date'] = gmdate('Y-m-d', strtotime($date_str));
            }
            
            $response['time'] = gmdate('G:i:s', strtotime($date_str));
        }
        
        return $response;
        
    }
    
    public function saswp_create_collection_grid($cols, $collection, $total_reviews, $pagination, $perpage, $offset, $nextpage, $data_id, $total_reviews_count, $date_format, $pagination_wpr = null, $saswp_collection_hide_col_rew_img = null,$stars_color= null,$saswp_collection_readmore_desc=null){
        
           $html          = '';                
           $grid_cols     = '';
           $perpage_break = $perpage; 
           if($collection){
             
               $page_count = ceil($total_reviews_count / $perpage);               
               $html .= '<div class="saswp-r1">';

               for($i=1; $i <= $cols; $i++ ){
                   $grid_cols .=' 1fr'; 
               }     

               if($cols > 5){

                $html .= '<ul style="grid-template-columns:'. esc_attr( $grid_cols).';overflow-x: scroll;">'; 
                }else{
                $html .= '<ul style="grid-template-columns:'. esc_attr( $grid_cols).';overflow-x:hidden;">';     
                }                               
                                
               $k = 1;
               $break = 1; 
               
               foreach ( $collection as $value){
                        
                       $date_str = $this->saswp_convert_datetostring($value['saswp_review_date'], $date_format );                     
                       if ( ! empty( $date_format) && $date_format == 'days'){                               
                           
                            $date_str['date'] = $this->saswp_getDaysDiff( strtotime($value['saswp_review_date']) );
                
                        }   

                    
                       $review_link = '';

                       if($value['saswp_review_location_id']){
                           $review_link = $value['saswp_review_location_id'];
                       }else{
                           $review_link = $value['saswp_review_link'];
                       }
                   
                        if($value['saswp_review_platform_name'] == 'Google'){
                            $review_link = $value['saswp_review_link'];
                        }      
                   
                       if($value['saswp_review_platform_name'] == 'Avvo' && $review_link == ''){
                            $review_link = $value['saswp_review_location_id'].'#client_reviews';
                       }         

                       if($value['saswp_review_platform_name'] == 'ProductReview' && !empty($value['saswp_review_location_id']) ) {
                            $review_link = 'https://www.productreview.com.au/listings/'.$value['saswp_review_location_id'];
                       }       
                     
                       if ( ! empty( $pagination_wpr) && !empty($pagination) ) {

                          if($break == 1){
                            $html .= '<li data-id="'. esc_attr( $break).'">';                       
                           }else{
                            $html .= '<li data-id="'. esc_attr( $break).'" class="saswp_grid_dp_none">';                       
                           }
                           
                           if($perpage == $k){
                            $break++;  
                            $perpage += $perpage_break;                     
                           }   
                           
                           $k++;     

                       }else{
                             $html .= '<li>';                       
                       }                                             
                       
                       $html .= '<div class="saswp-rc">';
                       $html .= '<div class="saswp-rc-a">';
                       if(empty($saswp_collection_hide_col_rew_img) && $saswp_collection_hide_col_rew_img != 1){
                        $html .= '<div class="saswp-r1-aimg">';
                        $html .= '<img alt="'. esc_attr( $value['saswp_reviewer_name']).'" loading="lazy" src="'. esc_url( $value['saswp_reviewer_image']).'" width="56" height="56"/>';
                        $html .= '</div>';
                       }
                      
                       $html .= '<div class="saswp-rc-nm saswp-grid">';
                        if(empty($review_link) || is_numeric($review_link) ) {
                            $html .= '<span><strong>'. esc_html( $value['saswp_reviewer_name']).'</strong></span>';
                        }else{
                            $html .= '<a target="_blank" rel="noopener" href="'. esc_url( $review_link).'">'. esc_html( $value['saswp_reviewer_name']).'</a>';
                        }

                       $html .= saswp_get_rating_html_by_value($value['saswp_review_rating'],$stars_color,$value['saswp_review_id']);                       
                       $html .= '<span class="saswp-rc-dt">'.(isset($date_str['date']) ? esc_attr( $date_str['date']): '' ).'</span>';
                       $html .= '</div>';
                       $html .= '</div>';

                       $html .= '<div class="saswp-rc-lg">';
                       $html .= '<img width="25" height="25" alt="'. esc_attr( $value['saswp_review_platform_name']).'" src="'. esc_url( $value['saswp_review_platform_icon']).'"/>';
                       $html .= '</div>';

                       $html .= '</div>';
                       $html .='<div class="saswp-rc-cnt">';
                       $review_text = wp_strip_all_tags(html_entity_decode($value['saswp_review_text']));
                       if($saswp_collection_readmore_desc == 1){
                        $review_text = $this->saswp_add_readmore_to_review_text($review_text, 20);
                       } 
                       $html .= '<p>'. $review_text.'</p>';
                       $html .= '</div>';
                       $html .= '</li>'; 

               }

               $html .= '</ul>';
               
               if(($page_count > 0 && $pagination ) && !$pagination_wpr){
                   
                        $current_url = saswp_get_current_url();
                        
                        if(strpos($current_url, "?rv_page") !== false){
                            $current_url = substr($current_url, 0, strpos($current_url, "?rv_page"));
                        }        
                        
                        $sidenr = 1;
                        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information but only loading it inside the shortcode calls.
                        if ( isset( $_GET['rv_page']) ) {
                            // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information but only loading it inside the shortcode calls.
                            $sidenr = intval($_GET['rv_page']);   
                        }
                        
                        list($min,$max) = saswp_get_page_range($sidenr, $page_count);
                         
                        $html .= '<div class="saswp-grid-pagination">';                    
                        $html .= '<a class="saswp-grid-page" data-id="1" href="'. esc_url( $current_url).'">&laquo;</a>'; 
                        
                        foreach (range($min, $max) as $number) {
                            
                            if($number == $data_id){
                                $html .= '<a class="active saswp-grid-page" href="'. esc_url( $current_url.'?rv_page='.$number).'">'.esc_html( $number).'</a>';    
                            }else{
                                $html .= '<a class="saswp-grid-page" href="'. esc_url( $current_url.'?rv_page='.$number).'">'.esc_html( $number).'</a>';    
                            }
                        }
                                                
                        $html .= '<a class="saswp-grid-page" href="'. esc_url( $current_url.'?rv_page='.$page_count).'">&raquo;</a>';                                     
                        
                        $html .= '</div>';                        
                        
                } 

                if(($page_count > 0 && $pagination ) && !empty($pagination_wpr) ) {

                        $html .= '<div class="saswp-grid-pagination saswp-grid-wpr">';                    
                        $html .= '<a data-id="1" class="saswp-grid-page saswp-pagination-first-last" href="#">&laquo;</a>'; 
                        
                        for($i=1; $i <= $page_count; $i++){
                            
                            if($i == 1){
                                $html .= '<a data-id="'. esc_attr( $i).'" class="saswp-grid-page active" href="#">'.esc_html( $i).'</a>';    
                            }else{
                                if($i > 5 ){
                                    $html .= '<a data-id="'. esc_attr( $i).'" class="saswp-grid-page saswp_grid_dp_none" href="#">'.esc_html( $i).'</a>'; 
                                }else{
                                    $html .= '<a data-id="'. esc_attr( $i).'" class="saswp-grid-page" href="#">'.esc_html( $i).'</a>';
                                }   
                            }
                            
                        }      
                        
                        $html .= '<a data-id="'. esc_attr( $page_count).'" class="saswp-grid-page saswp-pagination-first-last" href="#">&raquo;</a>';                                     
                        
                        $html .= '</div>';  
                        $html .= '<input type="hidden" id="saswp-no-page-load" value="'. esc_attr( $pagination_wpr).'"/>';  

                }
                                             
               $html .= '</div>';

                if($saswp_collection_readmore_desc == 1){
                    do_action('saswp_set_collection_card_height');
                } 
           }           
           return $html;
        
    }
    
    public function saswp_review_desing_for_slider($value, $date_format = '', $saswp_collection_gallery_img_hide = '',$stars_color='', $g_type='slider',$saswp_review_desing_for_slider=null){
        
                if ( ! empty( $value['saswp_review_location_id']) ) {
                    $review_link = $value['saswp_review_location_id'];
                }else{
                    $review_link = $value['saswp_review_link'];
                }
                if($value['saswp_review_platform_name'] == 'Google'){
                    $review_link = $value['saswp_review_link'];
                }  

                if($value['saswp_review_platform_name'] == 'Avvo' && $review_link == ''){
                
                    $review_link = $value['saswp_review_location_id'].'#client_reviews';

                }
                
                if($value['saswp_review_platform_name'] == 'Bark.com' && $review_link == ''){
                
                    $review_link = $value['saswp_review_location_id'].'#parent-reviews';

                }

                if($value['saswp_review_platform_name'] == 'ProductReview' && !empty($value['saswp_review_location_id']) ) {
                    $review_link = 'https://www.productreview.com.au/listings/'.$value['saswp_review_location_id'];
                }
        
                $html = '';
                $date_str = $this->saswp_convert_datetostring($value['saswp_review_date'], $date_format); 
                if ( ! empty( $date_format) && $date_format == 'days'){                               
                           
                    $date_str['date'] = $this->saswp_getDaysDiff( strtotime($value['saswp_review_date']) );
        
                }   
                
                $html .= '<div class="saswp-r2-sli">';
                $html .= '<div class="saswp-r2-b">';
                
                $html .= '<div class="saswp-r2-q">';
                $html .= '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" width="95.333px" height="95.332px" viewBox="0 0 95.333 95.332" style="enable-background:new 0 0 95.333 95.332;" xml:space="preserve"><path d="M30.512,43.939c-2.348-0.676-4.696-1.019-6.98-1.019c-3.527,0-6.47,0.806-8.752,1.793    c2.2-8.054,7.485-21.951,18.013-23.516c0.975-0.145,1.774-0.85,2.04-1.799l2.301-8.23c0.194-0.696,0.079-1.441-0.318-2.045    s-1.035-1.007-1.75-1.105c-0.777-0.106-1.569-0.16-2.354-0.16c-12.637,0-25.152,13.19-30.433,32.076    c-3.1,11.08-4.009,27.738,3.627,38.223c4.273,5.867,10.507,9,18.529,9.313c0.033,0.001,0.065,0.002,0.098,0.002    c9.898,0,18.675-6.666,21.345-16.209c1.595-5.705,0.874-11.688-2.032-16.851C40.971,49.307,36.236,45.586,30.512,43.939z"></path><path d="M92.471,54.413c-2.875-5.106-7.61-8.827-13.334-10.474c-2.348-0.676-4.696-1.019-6.979-1.019    c-3.527,0-6.471,0.806-8.753,1.793c2.2-8.054,7.485-21.951,18.014-23.516c0.975-0.145,1.773-0.85,2.04-1.799l2.301-8.23    c0.194-0.696,0.079-1.441-0.318-2.045c-0.396-0.604-1.034-1.007-1.75-1.105c-0.776-0.106-1.568-0.16-2.354-0.16    c-12.637,0-25.152,13.19-30.434,32.076c-3.099,11.08-4.008,27.738,3.629,38.225c4.272,5.866,10.507,9,18.528,9.312    c0.033,0.001,0.065,0.002,0.099,0.002c9.897,0,18.675-6.666,21.345-16.209C96.098,65.559,95.376,59.575,92.471,54.413z"></path></svg>';
                $html .= '</div>';
                $html .= '<div class="saswp-rc-cnt">';
                $html .= '<p>';
                $review_text = esc_attr( $value['saswp_review_text']);
                if($saswp_review_desing_for_slider == 1){
                    if($g_type == 'slider'){
                        $review_text = $this->saswp_add_readmore_to_review_text(esc_attr( $value['saswp_review_text']) , 40);
                    }else{
                        $review_text = $this->saswp_add_readmore_to_review_text(esc_attr( $value['saswp_review_text']), 20);
                    }
                }
                $html .= $review_text;
                $html .= '</p>';
                $html .= '</div>';
                $html .= '<div class="saswp-r2-strs">';
                $html .= '<span class="saswp-r2-s saswp-gallery">';
                $html .= saswp_get_rating_html_by_value($value['saswp_review_rating'],$stars_color,$value['saswp_review_id']);
                $html .= '</span>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '<div class="saswp-rc">';
                    $html .= '<div class="saswp-rc-a">';
                if(empty($saswp_collection_gallery_img_hide) && $saswp_collection_gallery_img_hide !=1){

                    $html .= '<img alt="'. esc_attr( $value['saswp_reviewer_name']).'" loading="lazy" src="'. esc_url( $value['saswp_reviewer_image']).'"/>';
                }                
                $html .= '<div class="saswp-rc-nm">';
                if(empty($review_link) || is_numeric($review_link) ) {
                    $html .= '<span><strong>'. esc_html( $value['saswp_reviewer_name']).'</strong></span>';
                }else{
                    $html .= '<a target="_blank" rel="noopener" href="'. esc_url( $review_link).'">'. esc_html( $value['saswp_reviewer_name']).'</a>';
                }
                $html .= '<span class="saswp-rc-dt">'.(isset($date_str['date']) ? esc_attr( $date_str['date']): '' ).'</span>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '<div class="saswp-rc-lg">';
                $html .= '<img width="25" height="25" alt="'. esc_attr( $value['saswp_review_platform_name']).'" src="'. esc_url( $value['saswp_review_platform_icon']).'"/>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';

                return $html;

    }

    public function saswp_create_collection_slider($g_type, $arrow, $dots, $collection, $date_format, $saswp_collection_gallery_img_hide,$stars_color,$g_interval=3000,$auto_slider=0,$saswp_collection_readmore_desc=null){
                
                $html = '';                               
                
                if($collection){
                    $html .= '<input type="hidden" id="saswp-review-slider-interval" value="'. esc_attr( $g_interval).'"/>';      
                    $html .= '<input type="hidden" id="saswp-review-auto-slider" value="'. esc_attr( $auto_slider).'"/>';
                    $html .= '<input type="hidden" id="saswp-presentation-type" value="'. esc_attr( $g_type).'"/>';
                    if(saswp_non_amp() ) {
                      
                        if($g_type == 'slider'){
                          $html .= '<div class="saswp-cst">';  
                        }else{
                          $html .= '<div class="saswp-cct">';  
                        }

                        
                        $html .= '<div class="saswp-cs">';
                        $html .= '<div class="saswp-sic">';      
                    if($g_type == 'slider'){
                            
                         foreach ( $collection as $value){
                                                          
                                $html .= '<div class="saswp-si">';
                                
                                $html .= $this->saswp_review_desing_for_slider($value, $date_format, $saswp_collection_gallery_img_hide,$stars_color,$g_type,$saswp_collection_readmore_desc);
                                
                                $html .= '</div>';
                             
                            }
                                                                                    
                         }   
                         
                    if($g_type == 'carousel'){
                             
                            $chunkarr = array_chunk($collection,3);
                            
                            if($chunkarr){
                                                                                                                
                            foreach( $chunkarr as $coll){
                                
                                $html .= '<div class="saswp-si">';
                                                                    
                                foreach( $coll as $value){

                                     $html .= $this->saswp_review_desing_for_slider($value, $date_format, $saswp_collection_gallery_img_hide,$stars_color,$g_type,$saswp_collection_readmore_desc);

                                }
                                
                                $html .= '</div>';   
                                                               
                            }
                                                                
                            }
                                                       
                          }                                                                                     
                    
                    $html .= '</div>';
                                        
                    if($arrow){
                        $html .= '<div class="saswp-slider-controls">';    
                        $html .= '<a href="#" class="saswp-slider-prev-btn"></a>';
                        $html .= '<a href="#" class="saswp-slider-next-btn"></a>';
                        $html .= '</div>';
                    }
                    
                    if($dots){
                    
                    $html .= '<div class="saswp-sd">';
                    $html .= '</div>';
                        
                    }
                    
                    $html .= '</div>';
                    $html .= '</div>';
                        
                    }else{
                        
                     if($collection){
                         
                         $slide_button = '';
                         
                         if($g_type == 'carousel'){
                              $html .= '<amp-carousel class="carousel-type" id="carousel-with-preview" height="290" layout="fixed-height" type="carousel"   delay="2000"  on="slideChange:AMP.setState({currentClass: event.index})">';
                         }
                         if($g_type == 'slider'){
                              $html .= '<amp-carousel class="slider-type" id="carousel-with-preview" height="290" layout="fixed-height" type="slides"  delay="2000" on="slideChange:AMP.setState({currentClass: event.index})">';
                         }
                                                 
                         $i = 0;
                      
                         foreach ( $collection as $value){
                           
                             $html .= '<li>';
                             $html .= $this->saswp_review_desing_for_slider($value, $date_format, '',$stars_color,$value['saswp_review_id']);
                             $html .= '</li>';
                             
                             if($i == 0){
                                 $slide_button .= '<button on="tap:carousel-with-preview.goToSlide(index='.$i.'),AMP.setState({currentClass:'.$i.'})" [class]="currentClass=='.$i.' ? \'active\' : \'\' " class="active"></button>';
                             }else{
                                 $slide_button .= '<button on="tap:carousel-with-preview.goToSlide(index='.$i.'),AMP.setState({currentClass:'.$i.'})" [class]="currentClass=='.$i.' ? \'active\' : \'\' "></button>';
                             }
                                                         
                             $i++;
                         }
                                                  
                         $html .= '</amp-carousel>';
                         $html .= '<div class="saswp-cp">';
                         $html .= $slide_button;                         
                         $html .= '</div>';
                         
                     }   
                        
                    $html .= '<div class="saswp-rd2-warp">';    
                    
                    $html .= '</div>';    
                        
                        
                    }
                    
                    if($saswp_collection_readmore_desc == 1){
                        do_action('saswp_set_collection_card_height');
                    }                                                             
                 }
                 
                 return $html;
                
    }
    public function saswp_create_collection_badge($collection,$saswp_collection_hide_col_rew_img='',$stars_color='',$saswp_collection_gallery_readmore_desc=''){
   
                $html = '';                
                if($collection){       
            
                    if(saswp_non_amp() ) {
                        
                    $html .= '<div class="saswp-r3">';
                    $html .= '<ul style="list-style-type: none;">';
                                                            
                    foreach ( $collection as $platform_wise){

                        $platform_icon  = '';
                        $platform_name  = '';
                        $review_count   = 0;                        
                        $sum_of_rating  = 0;
                        $average_rating = 1;
                        $source_url     = '';
                        
                        foreach ( $platform_wise as $key => $value){
                            
                            $platform_name  = $value['saswp_review_platform_name'];
                            if ( ! empty( $value['saswp_review_location_id']) ) {
                                $source_url = $value['saswp_review_location_id'];
                            }else{
                                $source_url = $value['saswp_review_link'];
                            }
                            
                            if($platform_name == 'Google'){
                                $source_url = 'https://search.google.com/local/reviews?placeid='.$source_url;
                            }

                            if($platform_name == 'ProductReview' && !empty($value['saswp_review_location_id']) ) {
                                $source_url = 'https://www.productreview.com.au/listings/'.$value['saswp_review_location_id'];
                            }

                            if($platform_name == 'Self'){
                                $platform_name = saswp_label_text('translation-self');
                            }

                            $platform_icon  = $value['saswp_review_platform_icon'];
                            $sum_of_rating += $value['saswp_review_rating'];
                            $review_count++;
                            
                        }
                        
                      if($sum_of_rating > 0){
                        
                            $average_rating = $sum_of_rating / $review_count;
                            
                        }
                            
                      $html .= '<li>';                       
                      if(empty($saswp_collection_gallery_readmore_desc) ) {                       
                        $html .= '<a target="_blank" href="'. esc_url( $source_url).'">'; 
                      }
                      $html .= '<div class="saswp-r3-lg">';
                      $html .= '<span>';
                      $html .= '<img alt="'. esc_attr( $platform_name).'" src="'. esc_url( $platform_icon).'"/>';
                      $html .= '</span>';
                      $html .= '<span class="saswp-r3-tlt">'.esc_html( $platform_name).'</span>';                      
                      $html .= '</div>';
                      $html .= '<div class="saswp-r3-rtng">';
                      $html .= '<div class="saswp-r3-rtxt">';
                      $html .= '<span class="saswp-r3-num">';
                      $html .= esc_attr(number_format($average_rating,1));
                      $html .= '</span>';
                      $html .= '<span class="saswp-stars saswp-badge">';
                      $html .= saswp_get_rating_html_by_value($average_rating,$stars_color,$value['saswp_review_id']); 
                      $html .= '</span>';
                      $html .= '</div>';
                      $html .= '<span class="saswp-r3-brv">';
                      $html .= saswp_label_text('translation-based-on').' '. esc_attr( $review_count).' '.saswp_label_text('translation-reviews');
                      $html .= '</span>';
                      $html .= '</div>';
                      $html .= '</a>';
                      $html .= '</li>';                                                                     

                    }      
                    
                    $html .= '</ul>';
                    $html .= '</div>';
                        
                    }else{
                        
                    $html .= '<div class="saswp-r3">';
                    $html .= '<ul>';
                                                            
                    foreach ( $collection as $platform_wise){

                        $platform_icon  = '';
                        $platform_name  = '';
                        $review_count   = 0;                        
                        $sum_of_rating  = 0;
                        $average_rating = 1;
                        
                        foreach ( $platform_wise as $key => $value){
                            
                            $platform_name  = $value['saswp_review_platform_name'];
                            $review_id = $value['saswp_review_id'];
                            if($platform_name == 'Self'){
                                $platform_name = saswp_label_text('translation-self');
                            }
                            $platform_icon  = $value['saswp_review_platform_icon'];
                            $sum_of_rating += $value['saswp_review_rating'];
                            $review_count++;
                            
                        }
                        
                      if($sum_of_rating > 0){
                        
                            $average_rating = $sum_of_rating / $review_count;
                            
                        }
                            
                      $html .= '<li>';                       
                      $html .= '<a href="#">'; 
                      $html .= '<div class="saswp-r3-lg">';
                      $html .= '<span>';
                      $html .= '<amp-img src="'. esc_url( $platform_icon).'" width="70" height="56"></amp-img>'; 
                      $html .= '</span>';
                      $html .= '<span class="saswp-r3-tlt">'.esc_html( $platform_name).'</span>';                      
                      $html .= '</div>';
                      $html .= '<div class="saswp-r3-rtng">';
                      $html .= '<div class="saswp-r3-rtxt">';
                      $html .= '<span class="saswp-r3-num">';
                      $html .= esc_attr(number_format($average_rating, 1));
                      $html .= '</span>';
                      $html .= '<span class="saswp-stars">';
                      $html .= saswp_get_rating_html_by_value($average_rating,$stars_color,$review_id); 
                      $html .= '</span>';
                      $html .= '</div>';
                      $html .= '<span class="saswp-r3-brv">';
                      $html .= saswp_label_text('translation-based-on').' '. esc_attr( $review_count).' '.saswp_label_text('translation-reviews');
                      $html .= '</span>';
                      $html .= '</div>';
                      $html .= '</a>';
                      $html .= '</li>';                                                                     

                    }      
                    
                    $html .= '</ul>';
                    $html .= '</div>';
                        
                    }
                    
                                         
                }
        
        return $html;
        
    }
    public function saswp_create_collection_popup($collection, $date_format,$saswp_collection_hide_col_rew_img='',$stars_color=''){
               
                   $html          = '';                
                   $html_list     = '';
                
                if($collection){
                        
                        $review_count   = 0;                        
                        $sum_of_rating  = 0;
                        $average_rating = 1;
                            
                        foreach( $collection as $value){
                                                        
                            $sum_of_rating += $value['saswp_review_rating'];
                            $review_count++;
                            $review_id = $value['saswp_review_id'];
                            
                            $date_str = $this->saswp_convert_datetostring($value['saswp_review_date'], $date_format); 
                            if ( ! empty( $date_format) && $date_format == 'days'){                               
                           
                                $date_str['date'] = $this->saswp_getDaysDiff( strtotime($value['saswp_review_date']) );
                    
                            } 
                            
                            $html_list .= '<li>';
                            $html_list .= '<div class="saswp-r4-b">';
                            $html_list .= '<span class="saswp-r4-str saswp-popup">';
                            $html_list .= saswp_get_rating_html_by_value($value['saswp_review_rating'],$stars_color);
                            $html_list .= '</span>';
                            $html_list .= '<span class="saswp-r4-tx">'. (isset($date_str['date']) ? esc_attr( $date_str['date']): '' ).'</span>';
                            $html_list .= '</div>';
                            
                            $html_list .= '<div class="saswp-r4-cnt">';
                            $html_list .= '<h3>'. esc_html( $value['saswp_reviewer_name']).'</h3>';
                            $html_list .= '<p>'. esc_html( $value['saswp_review_text']).'</p>';
                            $html_list .= '</div>';
                            
                            $html_list .= '</li>';
                            
                        }
                       
                        if($sum_of_rating > 0){
                        
                            $average_rating = $sum_of_rating / $review_count;
                            
                        }                                                                                                                
                    
                    if($review_count > 0){
                        
                        if(saswp_non_amp() ) {
                         $html .= '<div id="saswp-sticky-review">';
                        $html .= '<div class="saswp-open-class saswp-popup-btn">';
                        $html .= '<div class="saswp-opn-cls-btn">';

                        $html .= '<div class="saswp-onclick-hide">';
                        $html .= '<span>';
                        $html .= saswp_get_rating_html_by_value($average_rating,$stars_color,$review_id);
                        $html .= '</span>';
                        $html .= '<span class="saswp-r4-rnm">'.esc_html( number_format ($average_rating, 1)).' from '.esc_html( $review_count).' '. esc_html__( 'reviews', 'schema-and-structured-data-for-wp' ) .'</span>';                    
                        $html .= '</div>';

                        $html .= '<div class="saswp-onclick-show">';
                        $html .= '<span class="saswp-rar">'. esc_html__( 'Ratings and reviews', 'schema-and-structured-data-for-wp' ) .'</span>';                    
                        $html .= '<span class="saswp-mines"></span>';                    
                        $html .= '</div>';

                        $html .= '</div>';
                        $html .= '<div id="saswp-reviews-cntn">';
                        $html .= '<div class="saswp-r4-info">';
                        $html .= '<ul>';

                        $html .= '<li class="saswp-r4-r">';
                        $html .= '<span class="saswp-popup">';
                        $html .= saswp_get_rating_html_by_value($average_rating,$stars_color,$review_id);
                        $html .= '</span>';
                        $html .= '<span class="saswp-r4-rnm">'. esc_html( number_format ($average_rating, 1)).' from '. esc_html( $review_count).' '. esc_html__( 'reviews', 'schema-and-structured-data-for-wp' ) .'</span>';                    
                        $html .= '</li>';                                        
                        $html .= $html_list;
                        $html .= '</ul>';                    
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '</div>';
                        }else{
                            
                        $html .= '<div id="saswp-sticky-review">';    
                        
                        $html .= '<div class="btn" [class]="review==1 ? '."'open-class btn'".': '."'btn'".'"  id="open" >';                        
                        
                        $html .= '<div class="saswp-opn-cls-btn" role="1" tabindex="1" on="tap:AMP.setState({review: ( review==1? 0 : 1 ) })">';
                        $html .= '<div class="saswp-onclick-hide">';
                        $html .= '<span class="saswp-popup">';
                        $html .= saswp_get_rating_html_by_value($average_rating,$stars_color,$review_id);
                        $html .= '</span>';
                        $html .= '<span class="saswp-r4-rnm">'.esc_attr(number_format($average_rating, 1) ).' from '. esc_attr( $review_count).' '. esc_html__( 'reviews', 'schema-and-structured-data-for-wp' ) .'</span>';                    
                        $html .= '</div>';
                        $html .= '<div class="saswp-onclick-show">';
                        $html .= '<span class="saswp-rar">'. esc_html__( 'Ratings and reviews', 'schema-and-structured-data-for-wp' ) .'</span>';                    
                        $html .= '<span class="saswp-mines"></span>';                    
                        $html .= '</div>';
                        $html .= '</div>';
                                                
                        $html .= '<div id="saswp-reviews-cntn">';
                        $html .= '<div class="saswp-r4-info">';
                        $html .= '<ul>';

                        $html .= '<li class="saswp-r4-r">';
                        $html .= '<span class="saswp-popup">';
                        $html .= saswp_get_rating_html_by_value($average_rating,$stars_color,$review_id);
                        $html .= '</span>';
                        $html .= '<span class="saswp-r4-rnm">'. esc_html( number_format($average_rating, 1)).' from '. esc_html( $review_count).' reviews</span>';                    
                        $html .= '</li>';                                        
                        $html .= $html_list;
                        $html .= '</ul>';                    
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '</div>';
                            
                        }
                                                
                    }
                                           
                }
                
                return $html;
                
    }
    public function saswp_create_collection_fomo($f_interval, $f_visibility, $collection, $date_format,$saswp_collection_hide_col_rew_img = '',$stars_color=''){
         
        $html = '';
        if($collection){
            
            if(saswp_non_amp() ) {
                
             $i=0;
            
            $html .= '<input type="hidden" id="saswp_fomo_interval" value="'. esc_attr( $f_interval).'">';
            foreach ( $collection as $value){
                
                    $date_str = $this->saswp_convert_datetostring($value['saswp_review_date'], $date_format); 
                    if ( ! empty( $date_format) && $date_format == 'days'){                               
                           
                        $date_str['date'] = $this->saswp_getDaysDiff( strtotime($value['saswp_review_date']) );
            
                    } 

                    $html .= '<div id="'.$i.'" class="saswp-r5">';
                    $html .= '<div class="saswp-r5-r">';                            
                    $html .= '<div class="saswp-r5-lg">';
                    $html .= '<span>';
                    $html .= '<img alt="'. esc_attr( $value['saswp_review_platform_name']).'" height="70" width="70" src="'. esc_url($value['saswp_review_platform_icon']).'"/>';
                    $html .= '</span>';
                    $html .= '</div>';                            
                    $html .= '<div class="saswp-r5-rng saswp-star">';
                    $html .= saswp_get_rating_html_by_value($value['saswp_review_rating'],$stars_color,$value['saswp_review_id']);
                    $html .='<div class="saswp-r5-txrng">';
                    $html .='<span>'. esc_html( $value['saswp_review_rating']).' Stars</span>';
                    $html .='<span>by</span>';
                    $html .= '<span>'.esc_html( $value['saswp_reviewer_name']).'</span>';
                    $html .='</div>';
                    $html .= '<span class="saswp-r5-dt">'.(isset($date_str['date']) ? esc_attr( $date_str['date']): '' ).'</span>';
                    $html .= '</div>';                            
                    $html .= '</div>';
                    $html .= '</div>';     
    
                    $i++;
            }
            
               
            }else{
               
            $i=0;
            
            $html .='<amp-carousel id="saswp-reviews-fomo-amp" height="50" layout="fixed-height" type="slides"  autoplay delay="10000">';
            
            foreach ( $collection as $value){
                
                    $date_str = $this->saswp_convert_datetostring($value['saswp_review_date']); 

                    $html .= '<div id="'.$i.'" class="saswp-r5">';
                    $html .= '<div class="saswp-r5-r">';                            
                    $html .= '<div class="saswp-r5-lg">';
                    $html .= '<span>';
                    $html .= '<img alt="'. esc_attr( $value['saswp_review_platform_name']).'" height="70" width="70" src="'. esc_url($value['saswp_review_platform_icon']).'"/>';
                    $html .= '</span>';
                    $html .= '</div>';                            
                    $html .= '<div class="saswp-r5-rng saswp-star">';
                    $html .= saswp_get_rating_html_by_value($value['saswp_review_rating'],$stars_color,$value['saswp_review_id']);
                    $html .='<div class="saswp-r5-txrng">';
                    $html .='<span>'. esc_html( $value['saswp_review_rating']).' Stars</span>';
                    $html .='<span> by</span>';
                    $html .= '<span>'.esc_html( $value['saswp_reviewer_name']).'</span>';
                    $html .='</div>';
                    $html .= '<span class="saswp-r5-dt">'.(isset($date_str['date']) ? esc_attr( $date_str['date']): '' ).'</span>';
                    $html .= '</div>';                            
                    $html .= '</div>';
                    $html .= '</div>';     
    
                    $i++;
            }
            $html .= ' </amp-carousel>';
                                                           
            }
        }
                
        return $html;
        
    }
            
    public  function saswp_get_collection_average_rating($post_ids){
    
        $response = null;

        if($post_ids){

            $avg = 0;

            foreach ( $post_ids as $value) {

                $rating = get_post_meta($value, 'saswp_review_rating', true);

                if(is_numeric($rating) ) {
                    $avg += get_post_meta($value, 'saswp_review_rating', true);
                }
                                
            }
            if($avg > 0){
                $response = $avg/ count($post_ids);
            }            
        }
        return $response;

    }

    /**
     * Add Read more anchor link to review text
     * @since 1.23
     * @param $review_text  string
     * @param $strip_index  integer
     * */
    public function saswp_add_readmore_to_review_text($review_text, $strip_index)
    {
        if ( ! empty( $review_text) ) {
            $r_word_count = str_word_count($review_text, 1);
            if ( is_array( $r_word_count) && count($r_word_count) > $strip_index){
                $wcnt = 1;
                $brief_text = $read_more_text = '';
                $brief_text = '<span class="saswp-breaf-review-text">';
                $read_more_text = '<span class="saswp-more-review-text" style="display: none;">';
                foreach ( $r_word_count as $rkey => $rvalue) {
                    if($wcnt <= $strip_index){
                        $brief_text .= $rvalue ." ";
                    }else{
                        $read_more_text .= $rvalue . " ";
                    }
                    $wcnt++;
                }
                $brief_text .= ' <a href="#" class="saswp-read-more">Read More</a> </span>';
                $read_more_text .= '</span>';
                $review_text = $brief_text.$read_more_text;
            } 
        }
        return $review_text;
    }
}

$saswp_service_obj = new SASWP_Reviews_Service();
$saswp_service_obj->saswp_service_hooks();