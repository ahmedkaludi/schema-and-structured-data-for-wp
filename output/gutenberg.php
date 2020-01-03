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
                        
                global $post;
                $input1 = array();

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

                            if($val['title']){
                            $direction['@type']     = 'HowToDirection';
                            $direction['text']      = $val['title'];
                        }

                        if($val['description']){

                            $tip['@type']           = 'HowToTip';
                            $tip['text']            = $val['description'];

                        }

                        $supply_data['@type']   = 'HowToStep';
                        $supply_data['url']     = trailingslashit(saswp_get_permalink()).'#step'.++$key;
                        $supply_data['name']    = $val['title'];    

                        if(isset($direction['text']) || isset($tip['text'])){
                            $supply_data['itemListElement']  = array($direction, $tip);
                        }

                        if(isset($val['imageId']) && $val['imageId'] !=''){

                                    $image_details   = wp_get_attachment_image_src($val['imageId']);                                                 
                                    $supply_data['image']['@type']  = 'ImageObject';                                                
                                    $supply_data['image']['url']    = esc_url($image_details[0]);
                                    $supply_data['image']['width']  = esc_attr($image_details[1]);
                                    $supply_data['image']['height'] = esc_attr($image_details[2]);

                        }

                        $step_arr[] =  $supply_data;

                       }

                    }

                   $input1['step'] = $step_arr;

                }  
                
                 if(isset($parse_blocks['attrs']['days']) || $parse_blocks['attrs']['hours'] || $parse_blocks['attrs']['minutes']){
                     
                             $input1['totalTime'] = 'P'. 
                             ((isset($parse_blocks['attrs']['days']) && $parse_blocks['attrs']['days'] !='') ? esc_attr($parse_blocks['attrs']['days']).'DT':''). 
                             ((isset($parse_blocks['attrs']['hours']) && $parse_blocks['attrs']['hours'] !='') ? esc_attr($parse_blocks['attrs']['hours']).'H':''). 
                             ((isset($parse_blocks['attrs']['minutes']) && $parse_blocks['attrs']['minutes'] !='') ? esc_attr($parse_blocks['attrs']['minutes']).'M':''); 
                             
                 }   

                }
                                
            return $input1;
    
}

function saswp_gutenberg_faq_schema(){
                        
            global $post;
            $input1 = array();

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

                                       $image_details   = wp_get_attachment_image_src($val['imageId']);                                                 
                                       $supply_data['image']['@type']  = 'ImageObject';                                                
                                       $supply_data['image']['url']    = esc_url($image_details[0]);
                                       $supply_data['image']['width']  = esc_attr($image_details[1]);
                                       $supply_data['image']['height'] = esc_attr($image_details[2]);

                                     }

                                  $faq_question_arr[] =  $supply_data;
                               }
                              $input1['mainEntity'] = $faq_question_arr;
                           }

           }

            return $input1;
    
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
        $input1['description']           = saswp_get_the_excerpt();
        $input1['startDate']             = saswp_format_date_time($data['start_date'], $data['start_time']);
        $input1['endDate']               = saswp_format_date_time($data['end_date'], $data['end_time']);
        
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
        $input1['offers']['priceCurrency'] = $data['currency'] ? $data['currency'] : 'USD';
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