<?php 
Class saswp_review_output{
        public function __construct() {                               
	}
        
        public function saswp_review_hooks(){
            add_filter('the_content', array($this, 'saswp_display_review_box'));   
            
        }
        
        public function saswp_review_display_via_shortcode($attr){
            $review_id = $attr['id'];
            if(isset($review_id)){
              $result = $this->saswp_get_review_box_content();  
              return $result;
            }
            
        }


        public function saswp_get_review_box_content(){
            $saswp_review_details = array();
            $saswp_review_details = esc_sql ( get_post_meta(get_the_ID(), 'saswp_review_details', true));           
            $saswp_review_item_feature = array();
            $saswp_review_item_star_rating = array();
            $saswp_review_title = '';
            $saswp_review_description_title = '';
            $saswp_review_description = '';
            $saswp_review_props = '';
            $saswp_review_cons = '';
            $saswp_over_all_rating = '';
            if(isset($saswp_review_details['saswp-review-item-feature'])){
            $saswp_review_item_feature = $saswp_review_details['saswp-review-item-feature'];    
            }
            if(isset($saswp_review_details['saswp-review-item-star-rating'])){
            $saswp_review_item_star_rating = $saswp_review_details['saswp-review-item-star-rating'];    
            }
            if(isset($saswp_review_details['saswp-review-item-title'])){
            $saswp_review_title = $saswp_review_details['saswp-review-item-title'];    
            }
            if(isset($saswp_review_details['saswp-review-item-description-title'])){
            $saswp_review_description_title = $saswp_review_details['saswp-review-item-description-title'];    
            }
            if(isset($saswp_review_details['saswp-review-item-description'])){
            $saswp_review_description = $saswp_review_details['saswp-review-item-description'];    
            }
            if(isset($saswp_review_details['saswp-review-item-props'])){
            $saswp_review_props = $saswp_review_details['saswp-review-item-props'];    
            }
            if(isset($saswp_review_details['saswp-review-item-cons'])){
            $saswp_review_cons = $saswp_review_details['saswp-review-item-cons'];    
            }
            if(isset($saswp_review_details['saswp-review-item-over-all'])){
            $saswp_over_all_rating = $saswp_review_details['saswp-review-item-over-all'];    
            }
            
            $boxdata = '<div class="saswp-review-wrapper" style="border: 1px solid #e7e7e7;width: 100%;float: left;padding:10px">
                <h5>'.esc_attr($saswp_review_title).'</h5>'
                     . '<ul class="saswp-review-list">';
            if(!empty($saswp_review_details)){
             for($i=0; $i<count($saswp_review_details); $i++){
              $boxdata .='<li><span>'.esc_attr($saswp_review_item_feature[$i]).'</span><div>'.esc_attr($saswp_review_item_star_rating[$i]).'</div></li>';  
            }   
            }                                                            
              $boxdata .=  '</ul>
                <div>Over All rating: '.esc_attr($saswp_over_all_rating).'</div>  
                <div class="saswp-review-summary">
                  <h5>'.esc_attr($saswp_review_description_title).'</h5>  
                    '.esc_attr($saswp_review_description).'  
                </div>                
                <div class="saswp-review-props-and-cons">
                <div class="sasw-review-props">'.esc_attr($saswp_review_props).'</div>
                <div class="sasw-review-cons">'.esc_attr($saswp_review_cons).'</div>  
                </div>
                
            </div>';

            
            
            return $boxdata;
            
        }
        public function saswp_display_review_box($content){
            global $sd_data;
            if($sd_data['saswp-review-module']==0){
                return $content;
            }
            $result = $this->saswp_get_review_box_content();           
            $saswp_review_details = esc_sql ( get_post_meta(get_the_ID(), 'saswp_review_details', true)); 
            if(isset($saswp_review_details['saswp-review-location'])){
            switch ($saswp_review_details['saswp-review-location']) {
                case 1: 
                    $content = $content.$result;                    
                    break;
                case 2:
                    $content = $result.$content;
                    break;
                case 3:
                    add_shortcode('saswp-review', array($this,'saswp_review_display_via_shortcode'));
                    break;
                
                default:
                    break;
            }    
            }
            
            return $content;
        }
        
}
if (class_exists('saswp_review_output')) {
	$object = new saswp_review_output();
        $object->saswp_review_hooks();
};