<?php
/**
 * Output Page
 *
 * @author   Magazine3
 * @category Frontend
 * @path  output/elementor
 * @version 1.9.24
 */
if (! defined('ABSPATH') ) exit;

/**
 * Function to generate schema markup for elementor Faq block
 * @global type $post
 * @global type $saswp_elementor_faq
 * @return type array
 */
function saswp_elementor_faq_schema(){
              
            $input1 = array();
            
            global $post, $saswp_elementor_faq;
            
            if($saswp_elementor_faq){

                           $input1['@context']              = saswp_context_url();
                           $input1['@type']                 = 'FAQPage';
                           $input1['@id']                   = trailingslashit(saswp_get_permalink()).'#FAQPage';                            

                               $faq_question_arr = array();

                               foreach($saswp_elementor_faq as $val){

                                   $supply_data = array();
                                   $supply_data['@type']                   = 'Question';
                                   $supply_data['name']                    = $val['faq_question'];
                                   $supply_data['acceptedAnswer']['@type'] = 'Answer';
                                   $supply_data['acceptedAnswer']['text']  = $val['faq_answer'];
 
                                   $faq_question_arr[] =  $supply_data;
                               }
                              $input1['mainEntity'] = $faq_question_arr;                           

           }

            return $input1;    
}


/**
 * Function to generate schema markup for elementor HowTo block
 * @global type $post
 * @global type $saswp_elementor_howto
 * @return type array
 */
function saswp_elementor_howto_schema(){
              
    $input1 = array();
    
    global $post, $saswp_elementor_howto;

                if($saswp_elementor_howto){

                    $howto_description = '';
                    $step_list = $tool_list = $material_list = array();

                    if(isset($saswp_elementor_howto['step_list'])){
                        $step_list           = $saswp_elementor_howto['step_list'];
                    }
                    if(isset($saswp_elementor_howto['tool_list'])){
                        $tool_list           = $saswp_elementor_howto['tool_list'];
                    }						
                    if(isset($saswp_elementor_howto['material_list'])){
                        $material_list       = $saswp_elementor_howto['material_list'];
                    }
                    if(isset($saswp_elementor_howto['howto_description'])){
                        $howto_description   = $saswp_elementor_howto['howto_description'];						
                    }                                                                

                    $input1['@context']              = saswp_context_url();
                    $input1['@type']                 = 'HowTo';
                    $input1['@id']                   = trailingslashit(saswp_get_permalink()).'#HowTo';
                    $input1['name']                  = saswp_get_the_title();                
                    $input1['datePublished']         = get_the_date("c");
                    $input1['dateModified']          = get_the_modified_date("c");
                    $input1['description']           = $howto_description;                    

                    if(!empty($material_list)){

                        foreach($material_list as $val){
    
                            $supply_data = array();
    
                            if($val['howto_material_name']){
                                $supply_data['@type'] = 'HowToSupply';
                                $supply_data['name']  = $val['howto_material_name'];                            
                            }
    
                           $supply_arr[] =  $supply_data;
                        }
                       $input1['supply'] = $supply_arr;
                    }

                    $tool     = array();
                    $tool_arr = array();
                                            
                    if(!empty($tool_list)){

                    foreach($tool_list as $val){

                    $supply_data = array();

                    if($val['howto_tool_name']){
                        $supply_data['@type'] = 'HowToTool';
                        $supply_data['name']  = $val['howto_tool_name'];                            
                    }

                   $tool_arr[] =  $supply_data;
                }
               $input1['tool'] = $tool_arr;
            }
            
            $step     = array();
            $step_arr = array(); 
                                              
            if(!empty($step_list)){

                foreach($step_list as $key => $val){

                    $supply_data = array();
                    $direction   = array();
                    $tip         = array();

                   if($val['howto_step_title'] || $val['howto_step_description']){

                    if($val['howto_step_description']){
                        $direction['@type']     = 'HowToDirection';
                        $direction['text']      = $val['howto_step_description'];
                    }

                    if($val['howto_step_description']){

                        $tip['@type']           = 'HowToTip';
                        $tip['text']            = $val['howto_step_description'];

                    }

                    $supply_data['@type']   = 'HowToStep';
                    $supply_data['url']     = trailingslashit(saswp_get_permalink()).'#step'.++$key;
                    $supply_data['name']    = $val['howto_step_title'];    

                    if(isset($direction['text']) || isset($tip['text'])){
                        $supply_data['itemListElement']  = array($direction, $tip);
                    }

                    $regex   = '/<img(.*?)src="(.*?)"(.*?)>/';                          
                    @preg_match_all( $regex, $val['howto_step_description'], $match , PREG_SET_ORDER); 
                    
                    if(isset($match[0][2])){

                                $image_details   = saswp_get_image_by_url($match[0][2]);                                        
                                if($image_details){
                                    $supply_data['image']  = $image_details;                                                
                                }	
                    }
                    $step_arr[] =  $supply_data;

                   }

                }

               $input1['step'] = $step_arr;

            } 
            
                    if(isset($saswp_elementor_howto['howto_days']) || isset($saswp_elementor_howto['howto_hours']) || isset($saswp_elementor_howto['howto_minutes'])){
                        
                        $input1['totalTime'] = 'P'. 
                        ((isset($saswp_elementor_howto['howto_days']) && $saswp_elementor_howto['howto_days'] !='') ? esc_attr($saswp_elementor_howto['howto_days']).'DT':''). 
                        ((isset($saswp_elementor_howto['howto_hours']) && $saswp_elementor_howto['howto_hours'] !='') ? esc_attr($saswp_elementor_howto['howto_hours']).'H':''). 
                        ((isset($saswp_elementor_howto['howto_minutes']) && $saswp_elementor_howto['howto_minutes'] !='') ? esc_attr($saswp_elementor_howto['howto_minutes']).'M':''); 
                        
                    }   

                    if(isset($saswp_elementor_howto['howto_currency']) && isset($saswp_elementor_howto['howto_price'])){
                
                        $input1['estimatedCost']['@type']   = 'MonetaryAmount';
                        $input1['estimatedCost']['currency']= $saswp_elementor_howto['howto_currency'];
                        $input1['estimatedCost']['value']   = $saswp_elementor_howto['howto_price'];
                    }
                }    

    return $input1;    
}