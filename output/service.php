<?php 
Class saswp_output_service{
        public function __construct() {                                 
	}
                    
        public function saswp_woocommerce_product_details($post_id){           
             $product_details = array();   
             $product;
             if (class_exists('WC_Product')) {
	     $product = new WC_Product($post_id);      
             }                              
             if(is_object($product)){                 
             $availability = $product->get_availability();
             $product_details['product_name'] = $product->get_title();
             $product_details['product_description'] = $product->get_description();
             $product_details['product_image'] = $product->get_image();
             $product_details['product_availability'] = $availability['class'];
             $product_details['product_price'] = $product->get_price();
             $product_details['product_currency'] = get_option( 'woocommerce_currency' );
             
             $reviews_arr = array();
             $reviews = get_approved_comments( $post_id );
             if($reviews){
             foreach($reviews as $review){                 
                 $reviews_arr[] = array(
                     'author' => $review->comment_author,
                     'datePublished' => $review->comment_date,
                     'description' => $review->comment_content,
                     'reviewRating' => get_comment_meta( $review->comment_ID, 'rating', true ),
                 );
             }    
             }                          
             $product_details['product_review_count'] = $product->get_review_count();
             $product_details['product_average_rating'] = $product->get_average_rating();
             
             $product_details['product_reviews'] = $reviews_arr;      
             }
                       
             return $product_details;                       
        }
        
        public function saswp_extra_theme_review_details($post_id){
            global $sd_data;
           
            $review_data = array();
            $rating_value =0;
            $post_review_title ='';
            $post_review_desc ='';
            
            $post_meta   = esc_sql ( get_post_meta($post_id, $key='', true)  );                                       
            
            if(isset($post_meta['_post_review_box_breakdowns_score'])){
              $rating_value = bcdiv($post_meta['_post_review_box_breakdowns_score'][0], 20, 2);        
            }
            if(isset($post_meta['_post_review_box_title'])){
              $post_review_title = $post_meta['_post_review_box_title'][0];     
            }
            if(isset($post_meta['_post_review_box_summary'])){
              $post_review_desc = $post_meta['_post_review_box_summary'][0];        
            }                            
            if($post_review_title && $rating_value>0 &&  (isset($sd_data['saswp-extra']) && $sd_data['saswp-extra'] ==1) && get_template()=='Extra'){
            
            $review_data['aggregateRating'] = array(
                '@type' => 'AggregateRating',
                'ratingValue' => $rating_value,
                'reviewCount' => 1,
            );
            
            $review_data['review'] = array(
                '@type' => 'Review',
                'author' => get_the_author(),
                'datePublished' => get_the_date("Y-m-d\TH:i:s\Z"),
                'name' => $post_review_title,
                'reviewBody' => $post_review_desc,
                'reviewRating' => array(
                    '@type' => 'Rating',
                    'ratingValue' => $rating_value,
                ),
                
            );
            
           }
           return $review_data;
            
        }
}
