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
}
