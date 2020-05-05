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

/**
 * Function to generate schema markup for Gutenberg Faq block
 * @global type $post
 * @param type $block
 * @return type array
 */
function saswp_get_gutenberg_block_data($block){
    
    global $post;
     
    $block_list = array();
    $block_data = array();
    $response   = array();
    
    if(function_exists('parse_blocks') && is_object($post)){
        
            $blocks = parse_blocks($post->post_content);            
            
            if($blocks){

                foreach ($blocks as $parse_blocks){
                        $block_list[] = $parse_blocks['blockName'];
                        $block_data[$parse_blocks['blockName']] = $parse_blocks;
                }

            }        
    }
    
    if($block_list){
    
        if(in_array($block, $block_list)){
            $response = $block_data[$block];
        }
        
    }
    
    return $response;
    
}

function saswp_gutenberg_how_to_schema(){
                        
                global $post, $sd_data;
                
                $input1 = array();
                
                $yoast_howto = saswp_get_gutenberg_block_data('yoast/how-to-block');                 
                if(isset($sd_data['saswp-yoast']) && $sd_data['saswp-yoast'] == 1 && $yoast_howto && isset($yoast_howto['attrs'])){
                    
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
                
                if(array_key_exists('jsonDescription', $yoast_howto['attrs'])){
                    $input1['description']           = $yoast_howto['attrs']['jsonDescription'];
                }else{
                    $input1['description']           = saswp_get_the_excerpt();
                }
                             
                $step     = array();
                $step_arr = array(); 
                
                if(array_key_exists('steps', $yoast_howto['attrs'])){
                    $step = $yoast_howto['attrs']['steps'];
                }                                                           
                if(!empty($step)){

                    foreach($step as $key => $val){

                        $supply_data = array();
                        $direction   = array();
                        $tip         = array();

                       if($val['name'] || $val['text']){

                        if(isset($val['text'][0])){
                            $direction['@type']     = 'HowToDirection';
                            $direction['text']      = $val['text'][0];
                        }

                        if(isset($val['text'][0])){

                            $tip['@type']           = 'HowToTip';
                            $tip['text']            = $val['text'][0];

                        }

                        $supply_data['@type']   = 'HowToStep';
                        $supply_data['url']     = trailingslashit(saswp_get_permalink()).'#step'.++$key;
                        $supply_data['name']    = $val['name'][0];    

                        if(isset($direction['text']) || isset($tip['text'])){
                            $supply_data['itemListElement']  = array($direction, $tip);
                        }

                        if(isset($val['text'][1]['key']) && $val['text'][1]['key'] !=''){

                                    $image_details   = saswp_get_image_by_id($val['text'][1]['key']);    
                                    
                                    if($image_details){
                                        $supply_data['image']  = $image_details;                                                
                                    }                                    

                        }

                        $step_arr[] =  $supply_data;

                       }

                    }

                   $input1['step'] = $step_arr;

                }  
                
                 if(isset($yoast_howto['attrs']['days']) || isset($yoast_howto['attrs']['hours']) || isset($yoast_howto['attrs']['minutes'])){
                     
                             $input1['totalTime'] = 'P'. 
                             ((isset($yoast_howto['attrs']['days']) && $yoast_howto['attrs']['days'] !='') ? esc_attr($yoast_howto['attrs']['days']).'DT':''). 
                             ((isset($yoast_howto['attrs']['hours']) && $yoast_howto['attrs']['hours'] !='') ? esc_attr($yoast_howto['attrs']['hours']).'H':''). 
                             ((isset($yoast_howto['attrs']['minutes']) && $yoast_howto['attrs']['minutes'] !='') ? esc_attr($yoast_howto['attrs']['minutes']).'M':''); 
                             
                 }       

                }else{
                
                $parse_blocks = saswp_get_gutenberg_block_data('saswp/how-to-block');

                if(isset($parse_blocks['attrs'])){
                    
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
                
                if(array_key_exists('description', $parse_blocks['attrs'])){
                    $input1['description']           = $parse_blocks['attrs']['description'];
                }
                
                $supply     = array();
                $supply_arr = array();
                
                if(array_key_exists('materials', $parse_blocks['attrs'])){
                    $supply = $parse_blocks['attrs']['materials'];
                }
                
                if(!empty($supply)){

                    foreach($supply as $val){

                        $supply_data = array();

                        if($val['name']){
                            $supply_data['@type'] = 'HowToSupply';
                            $supply_data['name']  = $val['name'];                            
                        }

                       $supply_arr[] =  $supply_data;
                    }
                   $input1['supply'] = $supply_arr;
                }
                                
                $tool     = array();
                $tool_arr = array();
                
                if(array_key_exists('tools', $parse_blocks['attrs'])){
                    $tool = $parse_blocks['attrs']['tools'];
                }
                
                if(!empty($tool)){

                    foreach($tool as $val){

                        $supply_data = array();

                        if($val['name']){
                            $supply_data['@type'] = 'HowToTool';
                            $supply_data['name']  = $val['name'];                            
                        }

                       $tool_arr[] =  $supply_data;
                    }
                   $input1['tool'] = $tool_arr;
                }
                                
                $step     = array();
                $step_arr = array(); 
                
                if(array_key_exists('items', $parse_blocks['attrs'])){
                    $step = $parse_blocks['attrs']['items'];
                }                                                           
                if(!empty($step)){

                    foreach($step as $key => $val){
                        
                        $supply_data = array();
                        $direction   = array();
                        $tip         = array();                        

                       if($val['title'] || $val['description']){

                            if($val['description']){
                            $direction['@type']     = 'HowToDirection';
                            $direction['text']      = saswp_remove_all_images($val['description']);
                        }

                        if($val['description']){

                            $tip['@type']           = 'HowToTip';
                            $tip['text']            = saswp_remove_all_images($val['description']);

                        }

                        $supply_data['@type']   = 'HowToStep';
                        $supply_data['url']     = trailingslashit(saswp_get_permalink()).'#step'.++$key;
                        $supply_data['name']    = $val['title'];    

                        if(isset($direction['text']) || isset($tip['text'])){
                            $supply_data['itemListElement']  = array($direction, $tip);
                        }

                        if(isset($val['imageId']) && $val['imageId'] !=''){

                                    $image_details   = saswp_get_image_by_id($val['imageId']);    
                                    
                                    if($image_details){
                                        $supply_data['image']  = $image_details;                                                
                                    }                                    

                        }

                        $step_arr[] =  $supply_data;

                       }

                    }

                   $input1['step'] = $step_arr;

                }  
                
                 if(isset($parse_blocks['attrs']['days']) || isset($parse_blocks['attrs']['hours']) || isset($parse_blocks['attrs']['minutes'])){
                     
                             $input1['totalTime'] = 'P'. 
                             ((isset($parse_blocks['attrs']['days']) && $parse_blocks['attrs']['days'] !='') ? esc_attr($parse_blocks['attrs']['days']).'DT':''). 
                             ((isset($parse_blocks['attrs']['hours']) && $parse_blocks['attrs']['hours'] !='') ? esc_attr($parse_blocks['attrs']['hours']).'H':''). 
                             ((isset($parse_blocks['attrs']['minutes']) && $parse_blocks['attrs']['minutes'] !='') ? esc_attr($parse_blocks['attrs']['minutes']).'M':''); 
                             
                 }   

                 if(isset($parse_blocks['attrs']['price']) && isset($parse_blocks['attrs']['currency'])){
                
                    $input1['estimatedCost']['@type']   = 'MonetaryAmount';
                    $input1['estimatedCost']['currency']= $parse_blocks['attrs']['currency'];
                    $input1['estimatedCost']['value']   = $parse_blocks['attrs']['price'];
                 }

                }
                    
                }
                
                                
            return apply_filters('saswp_modify_howto_schema_output', $input1 );
    
}

function saswp_gutenberg_faq_schema(){
                        
            global $post, $sd_data;
            $input1 = array();

            $yoast_faq = saswp_get_gutenberg_block_data('yoast/faq-block');
            
            if(isset($sd_data['saswp-yoast']) && $sd_data['saswp-yoast'] == 1 && $yoast_faq && isset($yoast_faq['attrs'])){
                                
                           $input1['@context']              = saswp_context_url();
                           $input1['@type']                 = 'FAQPage';
                           $input1['@id']                   = trailingslashit(saswp_get_permalink()).'#FAQPage';                            

                           $faq_question_arr = array();

                           if(!empty($yoast_faq['attrs']['questions'])){

                               foreach($yoast_faq['attrs']['questions'] as $val){

                                   $supply_data = array();
                                   $supply_data['@type']                   = 'Question';
                                   $supply_data['name']                    = $val['question'][0];
                                   $supply_data['acceptedAnswer']['@type'] = 'Answer';
                                   $supply_data['acceptedAnswer']['text']  = $val['answer'][0];

                                    if(isset($val['answer'][1]['key']) && $val['answer'][1]['key'] !=''){

                                       $image_details   = saswp_get_image_by_id($val['answer'][1]['key']); 
                                       
                                       if($image_details){
                                           $supply_data['image']  = $image_details;                                                
                                       }
                                                                              
                                     }

                                  $faq_question_arr[] =  $supply_data;
                               }
                              $input1['mainEntity'] = $faq_question_arr;
                           }                                          
            }else{
            
                $attributes = saswp_get_gutenberg_block_data('saswp/faq-block');

                if(isset($attributes['attrs'])){

                           $input1['@context']              = saswp_context_url();
                           $input1['@type']                 = 'FAQPage';
                           $input1['@id']                   = trailingslashit(saswp_get_permalink()).'#FAQPage';                            

                           $faq_question_arr = array();

                           if(!empty($attributes['attrs']['items'])){

                               foreach($attributes['attrs']['items'] as $val){

                                   $supply_data = array();
                                   $supply_data['@type']                   = 'Question';
                                   $supply_data['name']                    = $val['title'];
                                   $supply_data['acceptedAnswer']['@type'] = 'Answer';
                                   $supply_data['acceptedAnswer']['text']  = $val['description'];

                                    if(isset($val['imageId']) && $val['imageId'] !=''){

                                       $image_details   = saswp_get_image_by_id($val['imageId']); 
                                       
                                       if($image_details){
                                           $supply_data['image']  = $image_details;                                                
                                       }
                                                                              
                                     }

                                  $faq_question_arr[] =  $supply_data;
                               }
                              $input1['mainEntity'] = $faq_question_arr;
                           }

           }
                
            }            

            return apply_filters('saswp_modify_faq_schema_output', $input1 );
    
}

function saswp_gutenberg_event_schema(){
    
    $input1 = array();
     
    $attributes = saswp_get_gutenberg_block_data('saswp/event-block');
    
    if(isset($attributes['attrs'])){
        
        $data = $attributes['attrs'];
                
        $input1['@context']              = saswp_context_url();
        $input1['@type']                 = 'Event';
        $input1['@id']                   = trailingslashit(saswp_get_permalink()).'#Event';  
        $input1['name']                  = saswp_get_the_title();  
        $input1['description']           = isset($data['description']) ? $data['description'] : saswp_get_the_excerpt();
        $input1['startDate']             = saswp_format_date_time($data['start_date'], $data['start_time']);
        $input1['endDate']               = saswp_format_date_time($data['end_date'], $data['end_time']);

        $input1['eventStatus']           = $data['event_status'];
        $input1['eventAttendanceMode']   = $data['attendance_mode'];

        if(isset($data['event_status']) && $data['event_status'] == 'EventRescheduled' && isset($data['previous_date'])){
            $input1['PreviousStartDate']               = saswp_format_date_time($data['previous_date'], $data['previous_time']);
        }
        
        if(isset($data['venue_address']) || isset($data['venue_name'])){
                            
        $input1['location']['@type']                      = 'Place';
        $input1['location']['name']                       = $data['venue_address'];
        $input1['location']['address']['@type']           = 'PostalAddress';
        $input1['location']['address']['streetAddress']   = $data['venue_address'];
        $input1['location']['address']['addressLocality'] = $data['venue_city'];
        $input1['location']['address']['postalCode']      = $data['venue_postal_code'];
        $input1['location']['address']['addressRegion']   = $data['venue_state'];
        $input1['location']['address']['addressCountry']  = $data['venue_country'];
        
        }
        if(isset($data['price'])){
        
        $input1['offers']['@type']         = 'Offer';
        $input1['offers']['url']           = saswp_get_permalink();
        $input1['offers']['price']         = $data['price'];
        $input1['offers']['priceCurrency'] = (isset($data['currency']) && $data['currency']) ? $data['currency'] : 'USD';
        $input1['offers']['availability']  = 'InStock';
        $input1['offers']['validFrom']     = saswp_format_date_time($data['start_date'], $data['start_time']);
        
        }
        
         if(!empty($data['organizers'])){
             
             foreach($data['organizers'] as $org){
                
                 $input1['organizer'][] = array(
                                    '@type'          => 'Organization',
                                    'name'           => $org['name'],                                                                      
                                    'url'            => $org['phone'],
                                    'email'          => $org['email'],
                                    'telephone'      => $org['phone'],                                                                        
                    );                 
                 
             }
                                                         
         }
               
        $performer_arr = array();

        if(!empty($data['performers'])){

            foreach($data['performers'] as $val){

                $supply_data = array();
                $supply_data['@type']        = 'Person';
                $supply_data['name']         = $val['name'];                                    
                $supply_data['url']          = $val['url'];
                $supply_data['email']        = $val['email'];

                $performer_arr[] =  $supply_data;
            }

           $input1['performer'] = $performer_arr;

        }       

        if( !empty($input1) && !isset($input1['image'])){

                        $service_object     = new saswp_output_service();
                        $input2             = $service_object->saswp_get_fetaure_image();

                        if(!empty($input2)){

                          $input1 = array_merge($input1,$input2); 

                        }                                                                    
                    }
         
        }
                        
    return $input1;
        
}

function saswp_gutenberg_job_schema(){
    
    $input1 = array();
     
    $attributes = saswp_get_gutenberg_block_data('saswp/job-block');
    
    if(isset($attributes['attrs'])){
        
        $data = $attributes['attrs'];
                
        $input1['@context']              = saswp_context_url();
        $input1['@type']                 = 'JobPosting';
        $input1['@id']                   = trailingslashit(saswp_get_permalink()).'#JobPosting';  
        $input1['title']                 = saswp_get_the_title();  
        $input1['description']           = $data['job_description'] ? wp_strip_all_tags($data['job_description']) : saswp_get_the_excerpt();
        $input1['datePosted']            = get_the_date("c");        
        $input1['validThrough']          = saswp_format_date_time($data['listing_expire_date']);  
        $input1['employmentType']        = $data['job_types'];  
        
        if(isset($data['location_address'])){
                            
        $input1['jobLocation']['@type']                      = 'Place';        
        $input1['jobLocation']['address']['@type']           = 'PostalAddress';
        $input1['jobLocation']['address']['streetAddress']   = $data['location_address'];
        $input1['jobLocation']['address']['addressLocality'] = $data['location_city'];
        $input1['jobLocation']['address']['postalCode']      = $data['location_postal_code'];
        $input1['jobLocation']['address']['addressRegion']   = $data['location_state'];
        $input1['jobLocation']['address']['addressCountry']  = $data['location_country'];
        
        }
        if(isset($data['base_salary'])){
        
        $input1['baseSalary']['@type']             = 'MonetaryAmount';        
        $input1['baseSalary']['currency']          = $data['currency_code'];
        $input1['baseSalary']['value']['@type']    = 'QuantitativeValue';
        $input1['baseSalary']['value']['value']    = $data['base_salary'];
        $input1['baseSalary']['value']['unitText'] = $data['unit_text'];                
        }
        
        if(isset($data['company_name']) || isset($data['company_website'])){
                                
        $input1['hiringOrganization']['@type']             = 'Organization';        
        $input1['hiringOrganization']['name']              = $data['company_name'];
        $input1['hiringOrganization']['description']       = $data['company_tagline'];
        $input1['hiringOrganization']['logo']              = $data['company_logo_url'];
        $input1['hiringOrganization']['sameAs']            = array(
                                                                    $data['company_website'],
                                                                    $data['company_twitter'],
                                                                    $data['company_facebook']
                                                            );
                        
        }
                                                     
        if( !empty($input1) && !isset($input1['image'])){

                        $service_object     = new saswp_output_service();
                        $input2             = $service_object->saswp_get_fetaure_image();

                        if(!empty($input2)){

                          $input1 = array_merge($input1,$input2); 

                        }                                                                    
                    }
         
        }
                        
    return $input1;
        
}

function saswp_gutenberg_course_schema(){
    
    $input1 = array();
     
    $attributes = saswp_get_gutenberg_block_data('saswp/course-block');
    
    if(isset($attributes['attrs'])){
                
        $loop_markup  = array();
        $item_list    = array();
        $course_count = count($attributes['attrs']['courses']);
        $i = 1;
        foreach($attributes['attrs']['courses'] as $course){
            
            $markup = array();
            
            $markup['@context']           = saswp_context_url();
            $markup['@type']              = 'Course';
            $markup['url']                = ($course_count > 1 ? saswp_get_permalink().'#course_'.$i : saswp_get_permalink());
            $markup['@id']                = trailingslashit(saswp_get_permalink()).'#Course'; 
            $markup['name']               = $course['name'];
            $markup['description']        = $course['description'];
                        
            $image = saswp_get_image_by_id($course['image_id']);
                                
            if($image){
                $markup['image']        = $image;
            }
            
            $markup['provider']['@type']  = 'Organization';
            $markup['provider']['name']   = $course['provider_name'];
            $markup['provider']['sameAs'] = array($course['provider_website']);
                        
            $loop_markup[] = $markup;
            
            unset($markup['@context'],$markup['@id']);
            
            $item_list[] = array(
                                         '@type' 		=> 'ListItem',
                                         'position' 		=> $i,
                                         'item' 		=> $markup,                                         
                                );
            
            $i++;   
        }
                 
        if($course_count > 1){
            
            $input1['@context']        = saswp_context_url();
            $input1['@type']           = 'ItemList';
            $input1['itemListElement'] = $item_list;
            
        }else{
            $input1 = $loop_markup[0];
        }
                                
    }
   
    return $input1;    
}