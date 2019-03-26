<?php 
Class saswp_review_output{
                    
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
            
            $saswp_review_details           = array();
            $saswp_review_details           = esc_sql ( get_post_meta(get_the_ID(), 'saswp_review_details', true));           
            $saswp_review_item_feature      = array();
            $saswp_review_item_star_rating  = array();
            $saswp_review_title             = '';
            $saswp_review_description_title = '';
            
            $saswp_review_description = get_post_meta( get_the_ID(), 'saswp-review-item-description', true );
            $saswp_review_props       = get_post_meta( get_the_ID(), 'saswp-review-item-props', true );
            $saswp_review_cons        = get_post_meta( get_the_ID(), 'saswp-review-item-cons', true );
            $saswp_over_all_rating    = '';
            
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
           
            if(isset($saswp_review_details['saswp-review-item-over-all'])){
                $saswp_over_all_rating = $saswp_review_details['saswp-review-item-over-all'];    
            }    
            
            $boxdata ='';
            
            if($saswp_review_props != '' || $saswp_review_cons != '' ){
                
             $boxdata .='
                <div class="saswp-pc-wrap">
                    <div class="saswp-lst">
                        <span>'.esc_html__('Pros', 'schema-and-structured-data-for-wp').'</span><br>
                         '.wpautop( stripslashes ( $saswp_review_props ) ).'
                    </div>
                    <div class="saswp-lst">   
                        <span>'.esc_html__('Cons', 'schema-and-structured-data-for-wp').'</span><br>
                        '.wpautop( stripslashes ( $saswp_review_cons ) ).'
                    </div>
                </div>';   
             
            }
                  
            if(!empty($saswp_review_item_feature) || $saswp_review_description != ''){
                
                $boxdata.='<table class="saswp-rvw">
                        <tbody>
                        <div class="saswp-rvw-hd">
                            <span>'.esc_html__('REVIEW OVERVIEW', 'schema-and-structured-data-for-wp').'</span>
                        </div>';  
                  
                if(isset($saswp_review_item_feature)){
                    
                    for($i=0; $i<count($saswp_review_item_feature); $i++){
                        
                     $boxdata.='<tr>
                            <td>'.esc_attr($saswp_review_item_feature[$i]).'</td>
                            <td>
                                <div class="saswp-rvw-str">';                                                                  
                                    for($j=0; $j<5; $j++){  
                                        
                                      if($saswp_review_item_star_rating[$i] >$j){
                                      
                                            $explod = explode('.', $saswp_review_item_star_rating[$i]);
                                            
                                            if(isset($explod[1])){
                                                
                                                if($j <$explod[0]){
                                                    
                                                    $boxdata.='<span class="str-ic"></span>';   
                                                    
                                                }else{
                                                    
                                                    $boxdata.='<span class="half-str"></span>';   
                                                    
                                                }                                           
                                            }else{
                                                
                                                $boxdata.='<span class="str-ic"></span>';    
                                                
                                            }
                                                                                                                           
                                      } else{
                                            $boxdata.='<span class="df-clr"></span>';   
                                      }                                                                                                                                
                                    }       
                                   
                    $boxdata.='</div>
                            </td>
                        </tr>'; 
                   }   
                }                                                                                                              
                $boxdata.='<tr>
                            <td class="saswp-rvw-sm">
                                <span>'.esc_html__('SUMMARY', 'schema-and-structured-data-for-wp').'</span>
                                <div class="rvw-dsc">
                                '.wpautop( stripslashes ( $saswp_review_description ) ).'
                                </div>
                            </td>
                            <td>
                                <div class="saswp-rvw-ov">
                                    <div class="saswp-rvw-fs">'.isset($saswp_over_all_rating)? esc_attr(number_format($saswp_over_all_rating, 2, '.', '')):''.'</div>';
                                                                        
                                    if($saswp_over_all_rating !=''){
                                        
                                      $boxdata.='<div class="tvw-fnl-str saswp-rvw-str">';                                            
                                      $explod = explode('.', $saswp_over_all_rating);
                                      
                                      for($x=0;$x<5;$x++) { 
                                          
                                            if(isset($explod[1])){

                                                if($saswp_over_all_rating >$x){

                                                if($x <$explod[0]){
                                                    $boxdata.='<span class="str-ic"></span>';                   
                                                }else{
                                                    $boxdata.='<span class="half-str"></span>';                       
                                                }  

                                                }else{
                                                    $boxdata.='<span class="df-clr"></span>';        
                                                }
                                             }else{
                                                if($saswp_over_all_rating >$x){
                                                    $boxdata.='<span class="str-ic"></span>';      
                                                } else{
                                                    $boxdata.='<span class="df-clr"></span>';          
                                                }                                        
                                             }    
                                        }                                      
                                       $boxdata.='</div><span class="ovs">'.esc_html__('OVERALL SCORE', 'schema-and-structured-data-for-wp').'</span>';
                                    }                                                                                                                                                                                       
                               $boxdata.=' </div>
                            </td>
                        <tr>
                    </tbody>
                </table>'; 
               }
                                           
            return $boxdata;
            
        }
        
        public function saswp_display_review_box($content){
            
            global $sd_data;  
            $saswp_review_details     = esc_sql ( get_post_meta(get_the_ID(), 'saswp_review_details', true)); 
            $saswp_review_item_enable = 0;
            
            if(isset($saswp_review_details['saswp-review-item-enable'])){
             $saswp_review_item_enable =  $saswp_review_details['saswp-review-item-enable'];  
            }  
            $review_module = 0;
            
            if(isset($sd_data['saswp-review-module'])){
               $review_module =  $sd_data['saswp-review-module'];
            }
            if($review_module==0 || $saswp_review_item_enable ==0){
                return $content;
            }
            $result = $this->saswp_get_review_box_content();                 
            
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