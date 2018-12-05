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
        
        public function saswp_dw_question_answers_details($post_id){
                global $sd_data;
                $dw_qa = array();
                $qa_page = array();
               
                $post_type = get_post_type($post_id);
                if($post_type =='dwqa-question' && isset($sd_data['saswp-dw-question-answer']) && $sd_data['saswp-dw-question-answer'] ==1 ){
                 
                $post_meta = get_post_meta($post_id, $key='', true);
                $best_answer_id = $post_meta['_dwqa_best_answer'][0];
                                               
                $userid = get_post_field( 'post_author', $post_id );
                $userinfo = get_userdata($userid);
                               
                $dw_qa['@type'] = 'Question';
                $dw_qa['name'] = get_the_title(); 
                $dw_qa['upvoteCount'] = get_post_meta( $post_id, '_dwqa_votes', true );                                             
                $args = array(
                    'p'         => $post_id, // ID of a page, post, or custom type
                    'post_type' => 'dwqa-question'
                  );
                
                $my_posts = new WP_Query($args);
                if ( $my_posts->have_posts() ) {
                  while ( $my_posts->have_posts() ) : $my_posts->the_post();                   
                   $dw_qa['text'] = get_the_content();
                  endwhile;
                } 
                $dw_qa['dateCreated'] = get_the_date("Y-m-d\TH:i:s\Z");
                $dw_qa['author'] = array('@type' => 'Person','name' =>$userinfo->data->user_nicename);   
                $dw_qa['answerCount'] = $post_meta['_dwqa_answers_count'][0];                  
                
                $args = array(
			'post_type' => 'dwqa-answer',
			'post_parent' => $post_id,
			'post_per_page' => '-1',
			'post_status' => array('publish')
		);
                
                $answer_array = get_posts($args);
               
                $accepted_answer = array();
                $suggested_answer = array();
                foreach($answer_array as $answer){
                    $authorinfo = get_userdata($answer->post_author);                    
                    if($answer->ID == $best_answer_id){
                        $accepted_answer['@type'] = 'Answer';
                        $accepted_answer['upvoteCount'] = get_post_meta( $answer->ID, '_dwqa_votes', true );
                        $accepted_answer['url'] = get_permalink($answer->ID);
                        $accepted_answer['text'] = $answer->post_content;
                        $accepted_answer['dateCreated'] = get_the_date("Y-m-d\TH:i:s\Z", $answer);
                        $accepted_answer['author'] = array('@type' => 'Person', 'name' => $authorinfo->data->user_nicename);
                    }else{
                        $suggested_answer[] =  array(
                            '@type' => 'Answer',
                            'upvoteCount' => get_post_meta( $answer->ID, '_dwqa_votes', true ),
                            'url' => get_permalink($answer->ID),
                            'text' => $answer->post_content,
                            'dateCreated' => get_the_date("Y-m-d\TH:i:s\Z", $answer),
                            'author' => array('@type' => 'Person', 'name' => $authorinfo->data->user_nicename),
                        );
                    }
                }
                $dw_qa['acceptedAnswer'] = $accepted_answer;
                $dw_qa['suggestedAnswer'] = $suggested_answer;
                    
                $qa_page['@context'] = 'http://schema.org';
                $qa_page['@type'] = 'QAPage';
                $qa_page['mainEntity'] = $dw_qa;
                
                }                
                 return $qa_page;
        }
}
