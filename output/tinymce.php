<?php
/**
 * Output Page
 *
 * @author   Magazine3
 * @category Frontend
 * @path  output_post_specific/output_post_specific
 * @version 1.0
 */
if (! defined('ABSPATH') ) exit;

function saswp_tinymce_how_to_schema(){
                        
                global $saswp_tiny_howto;
                
                $input1 = array();
                                
                if( !empty($saswp_tiny_howto['elements']) ){
                    
                $service_object     = new saswp_output_service();   
                $feature_image      = $service_object->saswp_get_fetaure_image();                  
                                       
                $input1['@context']              = saswp_context_url();
                $input1['@type']                 = 'HowTo';
                $input1['@id']                   = trailingslashit(saswp_get_permalink()).'#HowTo';
                $input1['name']                  = saswp_get_the_title();                
                $input1['datePublished']         = get_the_date("c");
                $input1['dateModified']          = get_the_modified_date("c");
                
                if(!empty($feature_image)){
                            
                    $input1 = array_merge($input1, $feature_image);   
                         
                }                
                
                if( !empty($saswp_tiny_howto) ){
                    $input1['description']           = $saswp_tiny_howto['description'];
                }
                          
                $step     = array();
                $step_arr = array(); 
                
                                                                       
                if( !empty($saswp_tiny_howto['elements']) ){

                    foreach($saswp_tiny_howto['elements'] as $key => $val){
                        
                        $supply_data = array();
                        $direction   = array();
                        $tip         = array();                        

                       if($val['step_title'] || $val['step_description']){

                            if($val['step_description']){
                            $direction['@type']     = 'HowToDirection';
                            $direction['text']      = saswp_remove_all_images($val['step_description']);
                        }

                        if($val['step_description']){

                            $tip['@type']           = 'HowToTip';
                            $tip['text']            = saswp_remove_all_images($val['step_description']);

                        }

                        $supply_data['@type']   = 'HowToStep';
                        $supply_data['url']     = trailingslashit(saswp_get_permalink()).'#step'.++$key;
                        $supply_data['name']    = $val['step_title'];    

                        if(isset($direction['text']) || isset($tip['text'])){
                            $supply_data['itemListElement']  = array($direction, $tip);
                        }

                        if(isset($val['image']) && $val['image'] !=''){

                                    $image_details   = saswp_get_image_by_id($val['image']);    
                                    
                                    if($image_details){
                                        $supply_data['image']  = $image_details;                                                
                                    }                                    

                        }

                        $step_arr[] =  $supply_data;

                       }

                    }

                   $input1['step'] = $step_arr;

                }  
                
                 if(isset($saswp_tiny_howto['days']) || isset($saswp_tiny_howto['hours']) || isset($saswp_tiny_howto['minutes'])){
                     
                             $input1['totalTime'] = 'P'. 
                             ((isset($saswp_tiny_howto['days']) && $saswp_tiny_howto['days'] !='') ? esc_attr($saswp_tiny_howto['days']).'DT':''). 
                             ((isset($saswp_tiny_howto['hours']) && $saswp_tiny_howto['hours'] !='') ? esc_attr($saswp_tiny_howto['hours']).'H':''). 
                             ((isset($saswp_tiny_howto['minutes']) && $saswp_tiny_howto['minutes'] !='') ? esc_attr($saswp_tiny_howto['minutes']).'M':''); 
                             
                 }   

                 if(isset($saswp_tiny_howto['cost']) && isset($saswp_tiny_howto['cost_currency'])){
                
                    $input1['estimatedCost']['@type']   = 'MonetaryAmount';
                    $input1['estimatedCost']['currency']= $saswp_tiny_howto['cost_currency'];
                    $input1['estimatedCost']['value']   = $saswp_tiny_howto['cost'];
                 }

                }
                                                    
            if($input1){
                
                $service_object     = new saswp_output_service();

                $extra_theme_review = $service_object->saswp_extra_theme_review_details(get_the_ID());
                $aggregateRating    = $service_object->saswp_rating_box_rating_markup(get_the_ID());
				
                if(!empty($aggregateRating)){
                        $input1['aggregateRating'] = $aggregateRating;
                }                                
                if(!empty($extra_theme_review)){
                    $input1 = array_merge($input1, $extra_theme_review);
                }
        
                $input1 = saswp_append_fetched_reviews($input1, get_the_ID());
                
            }    
                                
            return apply_filters('saswp_modify_howto_schema_output', $input1 );
    
}

function saswp_tinymce_faq_schema(){
                        
    global $saswp_tiny_multi_faq, $sd_data;

    $input1 = array();
    
    $faq_question_arr = array();

    if(!empty($saswp_tiny_multi_faq['elements'])){

        $input1['@context']              = saswp_context_url();
        $input1['@type']                     = 'FAQPage';
        $input1['@id']                       = trailingslashit(saswp_get_permalink()).'#FAQPage';                            

        foreach($saswp_tiny_multi_faq['elements'] as $val){

            $supply_data = array();
            $supply_data['@type']                   = 'Question';
            $supply_data['name']                    = (isset($val['question']) && is_string($val['question']) ) ? htmlspecialchars($val['question'], ENT_QUOTES, 'UTF-8') : '';
            $supply_data['acceptedAnswer']['@type'] = 'Answer';
            $supply_data['acceptedAnswer']['text']  = (isset($val['answer']) && is_string($val['answer']) ) ? htmlspecialchars($val['answer'], ENT_QUOTES, 'UTF-8') : '';

            if(!empty($val['image'])){

                $image_details   = saswp_get_image_by_id($val['image']); 
                
                if($image_details){
                    $supply_data['image']  = $image_details;                                                
                }
                                                        
                }

            $faq_question_arr[] =  $supply_data;
        }
        $input1['mainEntity'] = $faq_question_arr;

    }                                          

    return apply_filters('saswp_modify_faq_block_schema_output', $input1 );    
}