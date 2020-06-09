<?php
/**
 * Json-ld Markup file
 *
 * @author   Magazine3
 * @category Frontend
 * @path  output/markup
 * @Version 1.9.17
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
     * Function to get reviews schema markup
     * @global type $sd_data
     * @return string
     */
function saswp_get_reviews_schema_markup($reviews){
                            
                            $sumofrating = 0;
                            $avg_rating  = 1;
                            $reviews_arr = array();
                            $input1      = array();
                            
                            if($reviews){
                                
                                foreach($reviews as $rv){
                                                                        
                                    $sumofrating += $rv['saswp_review_rating'];
                                    
                                    if($rv['saswp_review_rating'] && $rv['saswp_reviewer_name']){
                                        
                                        $reviews_arr[] = array(
                                            '@type'         => 'Review',
                                            'author'        => array('@type'=> 'Person', 'name' => $rv['saswp_reviewer_name']),
                                            'datePublished' => $rv['saswp_review_date'],
                                            'description'   => $rv['saswp_review_text'],
                                            'reviewRating'  => array(
                                                        '@type'       => 'Rating',
                                                        'bestRating'  => 5,
                                                        'ratingValue' => $rv['saswp_review_rating'],
                                                        'worstRating' => 1
                                            ),
                                       );
                                        
                                    }
                                    
                                }
                                
                                    if($sumofrating> 0){
                                      $avg_rating = $sumofrating /  count($reviews); 
                                    }
                                
                                    if(!empty($reviews_arr)){
                                        

                                        global $collection_aggregate;

                                        if($collection_aggregate){

                                            $input1['aggregateRating'] = array(
                                                '@type'       => 'AggregateRating',
                                                'reviewCount' => $collection_aggregate['count'],
                                                'ratingValue' => $collection_aggregate['average']
                                             );
                                             
                                        }else{

                                            $input1['aggregateRating'] = array(
                                                '@type'       => 'AggregateRating',
                                                'reviewCount' => count($reviews),
                                                'ratingValue' => $avg_rating,                                        
                                             );

                                        }                                        

                                        $input1['review'] = $reviews_arr;
                                        
                                    }                                    
                                
                                }
                            return $input1;                                      
                        
    }

function saswp_book_schema_markup($schema_id, $schema_post_id, $all_post_meta){
 
            $input1 = array();
    
            $howto_image = get_post_meta( get_the_ID(), 'saswp_book_image_'.$schema_id.'_detail',true); 

            $input1['@context']              = saswp_context_url();
            $input1['@type']                 = 'Book';
            $input1['@id']                   = trailingslashit(get_permalink()).'#Book';
            $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_book_url_'.$schema_id, 'saswp_array');                            
            $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_book_name_'.$schema_id, 'saswp_array');                            
            $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_book_description_'.$schema_id, 'saswp_array');

            if(!(empty($howto_image))){

            $input1['image']['@type']        = 'ImageObject';
            $input1['image']['url']          = isset($howto_image['thumbnail']) ? esc_url($howto_image['thumbnail']):'';
            $input1['image']['height']       = isset($howto_image['width'])     ? esc_attr($howto_image['width'])   :'';
            $input1['image']['width']        = isset($howto_image['height'])    ? esc_attr($howto_image['height'])  :'';

            }  

            if($all_post_meta['saswp_book_author_'.$schema_id][0]){
                $input1['author']['@type']   = 'Person';
                $input1['author']['name']    = $all_post_meta['saswp_book_author_'.$schema_id][0];
                
                if($all_post_meta['saswp_book_author_url_'.$schema_id][0]){                    
                    $input1['author']['sameAs']    = $all_post_meta['saswp_book_author_url_'.$schema_id][0];
                }
                
            }
                                    
            $input1['datePublished']        = isset($all_post_meta['saswp_book_date_published_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_book_date_published_'.$schema_id][0])):'';
            $input1['isbn']                 = saswp_remove_warnings($all_post_meta, 'saswp_book_isbn_'.$schema_id, 'saswp_array');                          
            $input1['numberOfPages']        = saswp_remove_warnings($all_post_meta, 'saswp_book_no_of_page_'.$schema_id, 'saswp_array');                          
            $input1['publisher']            = saswp_remove_warnings($all_post_meta, 'saswp_book_publisher_'.$schema_id, 'saswp_array');                          

            if(isset($all_post_meta['saswp_book_price_'.$schema_id]) && isset($all_post_meta['saswp_book_price_currency_'.$schema_id])){
                $input1['offers']['@type']         = 'Offer';
                $input1['offers']['availability']  = saswp_remove_warnings($all_post_meta, 'saswp_book_availability_'.$schema_id, 'saswp_array');
                $input1['offers']['price']         = $all_post_meta['saswp_book_price_'.$schema_id];
                $input1['offers']['priceCurrency'] = $all_post_meta['saswp_book_price_currency_'.$schema_id];
            }

            if(isset($all_post_meta['saswp_book_enable_rating_'.$schema_id]) && isset($all_post_meta['saswp_book_rating_value_'.$schema_id]) && isset($all_post_meta['saswp_book_rating_count_'.$schema_id])){
                $input1['aggregateRating']['@type']         = 'aggregateRating';
                $input1['aggregateRating']['ratingValue']   = $all_post_meta['saswp_book_rating_value_'.$schema_id];
                $input1['aggregateRating']['ratingCount']   = $all_post_meta['saswp_book_rating_count_'.$schema_id];                                
            }
        
    return $input1;
}

function saswp_movie_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
        $input1 = array();
        
        $howto_image = get_post_meta($schema_post_id, 'saswp_movie_image_'.$schema_id.'_detail',true); 

        $tool        = get_post_meta($schema_post_id, 'movie_actor_'.$schema_id, true);                      

        $input1['@context']              = saswp_context_url();
        $input1['@type']                 = 'Movie';
        $input1['@id']                   = ((isset($all_post_meta['saswp_howto_schema_id_'.$schema_id][0]) && $all_post_meta['saswp_howto_schema_id_'.$schema_id][0] !='') ? $all_post_meta['saswp_howto_schema_id_'.$schema_id][0] : trailingslashit(get_permalink()).'#Movie');                
        $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_movie_name_'.$schema_id, 'saswp_array');
        $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_movie_url_'.$schema_id, 'saswp_array');
        $input1['sameAs']                = saswp_remove_warnings($all_post_meta, 'saswp_movie_url_'.$schema_id, 'saswp_array');
        $input1['dateCreated']           = isset($all_post_meta['saswp_movie_date_created_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_movie_date_created_'.$schema_id][0])):'';        
        $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_movie_description_'.$schema_id, 'saswp_array');

        if(!(empty($howto_image))){

        $input1['image']['@type']        = 'ImageObject';
        $input1['image']['url']          = isset($howto_image['thumbnail']) ? esc_url($howto_image['thumbnail']):'';
        $input1['image']['height']       = isset($howto_image['width'])     ? esc_attr($howto_image['width'])   :'';
        $input1['image']['width']        = isset($howto_image['height'])    ? esc_attr($howto_image['height'])  :'';

        }      
        
        if(isset($all_post_meta['saswp_movie_director_'.$schema_id][0])){
            
        $input1['director']['@type']        = 'Person';
        $input1['director']['name']          = $all_post_meta['saswp_movie_director_'.$schema_id][0];        
            
        }
        
        if(saswp_remove_warnings($all_post_meta, 'saswp_movie_enable_rating_'.$schema_id, 'saswp_array') == 1 && saswp_remove_warnings($all_post_meta, 'saswp_movie_rating_value_'.$schema_id, 'saswp_array') && saswp_remove_warnings($all_post_meta, 'saswp_movie_rating_count_'.$schema_id, 'saswp_array')){   
                                 
                                          $input1['aggregateRating'] = array(
                                                            "@type"       => "AggregateRating",
                                                            "ratingValue" => saswp_remove_warnings($all_post_meta, 'saswp_movie_rating_value_'.$schema_id, 'saswp_array'),
                                                            "reviewCount" => saswp_remove_warnings($all_post_meta, 'saswp_movie_rating_count_'.$schema_id, 'saswp_array')
                                                         );                                       
                                         }
                
        $supply_arr = array();
        
        if(!empty($tool)){

            foreach($tool as $val){

                $supply_data = array();

                if($val['saswp_movie_actor_name']){
                    $supply_data['@type'] = 'Person';
                    $supply_data['name']  = $val['saswp_movie_actor_name'];
                    $supply_data['url']   = $val['saswp_movie_actor_url'];
                }

               $supply_arr[] =  $supply_data;
            }
           $input1['actor'] = $supply_arr;
        }
                                    
        return $input1;
}

function saswp_howto_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
        $input1 = array();
        $howto_image = get_post_meta( get_the_ID(), 'saswp_howto_schema_image_'.$schema_id.'_detail',true); 

        $tool    = get_post_meta($schema_post_id, 'howto_tool_'.$schema_id, true);              
        $step    = get_post_meta($schema_post_id, 'howto_step_'.$schema_id, true);              
        $supply  = get_post_meta($schema_post_id, 'howto_supply_'.$schema_id, true);              

        $input1['@context']              = saswp_context_url();
        $input1['@type']                 = 'HowTo';
        $input1['@id']                   = ((isset($all_post_meta['saswp_howto_schema_id_'.$schema_id][0]) && $all_post_meta['saswp_howto_schema_id_'.$schema_id][0] !='') ? $all_post_meta['saswp_howto_schema_id_'.$schema_id][0] : trailingslashit(get_permalink()).'#HowTo');                
        $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_howto_schema_name_'.$schema_id, 'saswp_array');
        $input1['datePublished']         = isset($all_post_meta['saswp_howto_ec_schema_date_published_'.$schema_id][0])? saswp_format_date_time($all_post_meta['saswp_howto_ec_schema_date_published_'.$schema_id][0], get_post_time('h:i:s')) : '';
        $input1['dateModified']          = isset($all_post_meta['saswp_howto_ec_schema_date_modified_'.$schema_id][0])? saswp_format_date_time($all_post_meta['saswp_howto_ec_schema_date_modified_'.$schema_id][0], get_the_modified_time('h:i:s')) : '';
        $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_howto_schema_description_'.$schema_id, 'saswp_array');

        if(!(empty($howto_image))){

        $input1['image']['@type']        = 'ImageObject';
        $input1['image']['url']          = isset($howto_image['thumbnail']) ? esc_url($howto_image['thumbnail']):'';
        $input1['image']['height']       = isset($howto_image['width'])     ? esc_attr($howto_image['width'])   :'';
        $input1['image']['width']        = isset($howto_image['height'])    ? esc_attr($howto_image['height'])  :'';

        }                            

        if(saswp_remove_warnings($all_post_meta, 'saswp_howto_ec_schema_currency_'.$schema_id, 'saswp_array') !='' && saswp_remove_warnings($all_post_meta, 'saswp_howto_ec_schema_value_'.$schema_id, 'saswp_array') !='')
        {
            $input1['estimatedCost']['@type']   = 'MonetaryAmount';
            $input1['estimatedCost']['currency']= saswp_remove_warnings($all_post_meta, 'saswp_howto_ec_schema_currency_'.$schema_id, 'saswp_array');
            $input1['estimatedCost']['value']   = saswp_remove_warnings($all_post_meta, 'saswp_howto_ec_schema_value_'.$schema_id, 'saswp_array');
        }


        $supply_arr = array();
        if(!empty($supply)){

            foreach($supply as $val){

                $supply_data = array();

                if($val['saswp_howto_supply_name'] || $val['saswp_howto_supply_url']){
                    $supply_data['@type'] = 'HowToSupply';
                    $supply_data['name']  = $val['saswp_howto_supply_name'];
                    $supply_data['url']   = $val['saswp_howto_supply_url'];
                }

                if(isset($val['saswp_howto_supply_image_id']) && $val['saswp_howto_supply_image_id'] !=''){

                            $image_details   = saswp_get_image_by_id($val['saswp_howto_supply_image_id']); 

                            if($image_details){
                                $supply_data['image'] = $image_details;
                            }
                                                        
                }
               $supply_arr[] =  $supply_data;
            }
           $input1['supply'] = $supply_arr;
        }

        $tool_arr = array();
        if(!empty($tool)){

            foreach($tool as $val){

                $supply_data = array();

                if($val['saswp_howto_tool_name'] || $val['saswp_howto_tool_url']){
                    $supply_data['@type'] = 'HowToTool';
                    $supply_data['name'] = $val['saswp_howto_tool_name'];
                    $supply_data['url']  = $val['saswp_howto_tool_url'];
                }

                if(isset($val['saswp_howto_tool_image_id']) && $val['saswp_howto_tool_image_id'] !=''){

                            $image_details   = saswp_get_image_by_id($val['saswp_howto_tool_image_id']); 
                            
                            if($image_details){
                                $supply_data['image']  = $image_details;                                                
                            }

                }
               $tool_arr[] =  $supply_data;
            }
           $input1['tool'] = $tool_arr;
        }

        //step

        $step_arr = array();                            
        if(!empty($step)){

            foreach($step as $key => $val){

                $supply_data = array();
                $direction   = array();
                $tip         = array();

                if($val['saswp_howto_direction_text']){
                    $direction['@type']     = 'HowToDirection';
                    $direction['text']      = $val['saswp_howto_direction_text'];
                }

                if($val['saswp_howto_tip_text']){

                    $tip['@type']           = 'HowToTip';
                    $tip['text']            = $val['saswp_howto_tip_text'];

                }

                $supply_data['@type']   = 'HowToStep';
                $supply_data['url']     = trailingslashit(get_permalink()).'#step'.++$key;
                $supply_data['name']    = $val['saswp_howto_step_name'];    

                if($direction['text'] ||  $tip['text']){
                    $supply_data['itemListElement']  = array($direction, $tip);
                }

                if(isset($val['saswp_howto_step_image_id']) && $val['saswp_howto_step_image_id'] !=''){

                            $image_details   = saswp_get_image_by_id($val['saswp_howto_step_image_id']);  
                            
                            if($image_details){
                                $supply_data['image']  = $image_details;                                                
                            }
                            
                }

               $step_arr[] =  $supply_data;

            }

           $input1['step'] = $step_arr;

        }

         $input1['totalTime'] = saswp_remove_warnings($all_post_meta, 'saswp_howto_schema_totaltime_'.$schema_id, 'saswp_array');
                             
    return $input1;
}

function saswp_event_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
            $input1 = array();
    
            $event_image = get_post_meta( get_the_ID(), 'saswp_event_schema_image_'.$schema_id.'_detail',true); 

            $input1 = array(
            '@context'			=> saswp_context_url(),
            '@type'				=> (isset($all_post_meta['saswp_event_schema_type_'.$schema_id][0]) && $all_post_meta['saswp_event_schema_type_'.$schema_id][0] !='') ? $all_post_meta['saswp_event_schema_type_'.$schema_id][0] : 'Event' ,
            '@id'                           => trailingslashit(get_permalink()).'#event',    
            'name'			        => saswp_remove_warnings($all_post_meta, 'saswp_event_schema_name_'.$schema_id, 'saswp_array'),
            'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_event_schema_description_'.$schema_id, 'saswp_array'),			                
            'image'                         => array(
                                                        '@type'		=>'ImageObject',
                                                        'url'		=>  isset($event_image['thumbnail']) ? esc_url($event_image['thumbnail']):'' ,
                                                        'width'		=>  isset($event_image['width'])     ? esc_attr($event_image['width'])   :'' ,
                                                        'height'            =>  isset($event_image['height'])    ? esc_attr($event_image['height'])  :'' ,
                                                    ),                                            
            'offers'			=> array(
                                                '@type'           => 'Offer',
                                                'url'             => saswp_remove_warnings($all_post_meta, 'saswp_event_schema_url_'.$schema_id, 'saswp_array'),	                        
                                                'price'           => saswp_remove_warnings($all_post_meta, 'saswp_event_schema_price_'.$schema_id, 'saswp_array'),
                                                'priceCurrency'   => saswp_remove_warnings($all_post_meta, 'saswp_event_schema_price_currency_'.$schema_id, 'saswp_array'),
                                                'availability'    => saswp_remove_warnings($all_post_meta, 'saswp_event_schema_availability_'.$schema_id, 'saswp_array'),
                                                'validFrom'       => isset($all_post_meta['saswp_event_schema_validfrom_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_event_schema_validfrom_'.$schema_id][0])):'',
                            )                         
                );


                $phy_location = array(
                    '@type'   => 'Place',
                    'name'    => saswp_remove_warnings($all_post_meta, 'saswp_event_schema_location_name_'.$schema_id, 'saswp_array'),
                    'address' => array(
                         '@type'           => 'PostalAddress',
                         'streetAddress'   => saswp_remove_warnings($all_post_meta, 'saswp_event_schema_location_streetaddress_'.$schema_id, 'saswp_array'),
                         'addressLocality' => saswp_remove_warnings($all_post_meta, 'saswp_event_schema_location_locality_'.$schema_id, 'saswp_array'),
                         'postalCode'      => saswp_remove_warnings($all_post_meta, 'saswp_event_schema_location_postalcode_'.$schema_id, 'saswp_array'),
                         'addressRegion'   => saswp_remove_warnings($all_post_meta, 'saswp_event_schema_location_region_'.$schema_id, 'saswp_array'),                                                     
                         'addressCountry'  => saswp_remove_warnings($all_post_meta, 'saswp_event_schema_location_country_'.$schema_id, 'saswp_array'),                                                     
                    )    
                    );

                $vir_location = array(
                    '@type'   => 'VirtualLocation',
                    'name'    => isset($all_post_meta['saswp_event_schema_virtual_location_name_'.$schema_id][0]) ? $all_post_meta['saswp_event_schema_virtual_location_name_'.$schema_id][0] : '',
                    'url'     => isset($all_post_meta['saswp_event_schema_virtual_location_url_'.$schema_id][0]) ? $all_post_meta['saswp_event_schema_virtual_location_url_'.$schema_id][0]: ''
                );    
                if(isset($all_post_meta['saswp_event_schema_attendance_mode_'.$schema_id][0]) && $all_post_meta['saswp_event_schema_attendance_mode_'.$schema_id][0] == 'OfflineEventAttendanceMode'){
                    $input1['location'] =  $phy_location;  
                }else if(isset($all_post_meta['saswp_event_schema_attendance_mode_'.$schema_id][0]) && $all_post_meta['saswp_event_schema_attendance_mode_'.$schema_id][0] == 'OnlineEventAttendanceMode'){
                    $input1['location'] =  $vir_location;   
                }else{
                    $input1['location'] =  array($vir_location, $phy_location);   
                }            
                if(isset($all_post_meta['saswp_event_schema_status_'.$schema_id][0])){
                    $input1['eventStatus'] = $all_post_meta['saswp_event_schema_status_'.$schema_id][0];
                }
                if(isset($all_post_meta['saswp_event_schema_attendance_mode_'.$schema_id][0])){
                    $input1['eventAttendanceMode'] = $all_post_meta['saswp_event_schema_attendance_mode_'.$schema_id][0];
                }
                if(isset($all_post_meta['saswp_event_schema_previous_start_date_'.$schema_id][0])){

                    $date = $time = '';
                    
                    $date = $all_post_meta['saswp_event_schema_previous_start_date_'.$schema_id][0];
                    
                    if(isset($all_post_meta['saswp_event_schema_previous_start_time_'.$schema_id][0])){
                        $time  = $all_post_meta['saswp_event_schema_previous_start_time_'.$schema_id][0];    
                    }
                    
                    $input1['previousStartDate']        = saswp_format_date_time($date, $time);

                }

                if(isset($all_post_meta['saswp_event_schema_start_date_'.$schema_id][0])){
                    
                    $date = $time = '';
                    
                    $date = $all_post_meta['saswp_event_schema_start_date_'.$schema_id][0];
                    
                    if(isset($all_post_meta['saswp_event_schema_start_time_'.$schema_id][0])){
                        $time  = $all_post_meta['saswp_event_schema_start_time_'.$schema_id][0];    
                    }
                    
                    $input1['startDate']        = saswp_format_date_time($date, $time);
                    
                }
                
                if(isset($all_post_meta['saswp_event_schema_end_date_'.$schema_id][0])){
                    
                    $date = $time = '';
                    
                    $date = $all_post_meta['saswp_event_schema_end_date_'.$schema_id][0];
                    
                    if(isset($all_post_meta['saswp_event_schema_end_time_'.$schema_id][0])){
                        $time  = $all_post_meta['saswp_event_schema_end_time_'.$schema_id][0];    
                    }
                    
                    $input1['endDate']        = saswp_format_date_time($date, $time);
                    
                }

                    //Performer starts here
                    $performer  = get_post_meta($schema_post_id, 'performer_'.$schema_id, true);

                    $performer_arr = array();

                    if(isset($all_post_meta['saswp_event_schema_performer_name_'.$schema_id][0])){
                        $performer_arr[] = array(
                            '@type' => 'Person',
                            'name'  => $all_post_meta['saswp_event_schema_performer_name_'.$schema_id][0]
                        );
                    }

                    if(!empty($performer)){

                        foreach($performer as $val){

                            $supply_data = array();
                            $supply_data['@type']        = $val['saswp_event_performer_type'];
                            $supply_data['name']         = $val['saswp_event_performer_name'];                                    
                            $supply_data['url']          = $val['saswp_event_performer_url'];

                            $performer_arr[] =  $supply_data;
                        }                       

                    }
                    if($performer_arr){
                        $input1['performer'] = $performer_arr;    
                    }                    
                    //Performer ends here

                    //Organizer starts here
                    $organizer  = get_post_meta($schema_post_id, 'organizer_'.$schema_id, true);

                    $organizer_arr = array();

                    if(isset($all_post_meta['saswp_event_schema_organizer_name_'.$schema_id][0])){
                        $organizer_arr[] = array(
                            '@type'      => 'Organization',
                            'name'       => $all_post_meta['saswp_event_schema_organizer_name_'.$schema_id][0],
                            'url'        => $all_post_meta['saswp_event_schema_organizer_url_'.$schema_id][0],
                            'email'      => $all_post_meta['saswp_event_schema_organizer_email_'.$schema_id][0],
                            'telephone'  => $all_post_meta['saswp_event_schema_organizer_phone_'.$schema_id][0]
                        );
                    }

                    if(!empty($organizer)){

                        foreach($organizer as $val){

                            $supply_data = array();
                            $supply_data['@type']        = 'Organization';
                            $supply_data['name']         = $val['saswp_event_organizer_name'];                                    
                            $supply_data['url']          = $val['saswp_event_organizer_url'];
                            $supply_data['email']        = $val['saswp_event_organizer_email'];
                            $supply_data['telephone']    = $val['saswp_event_organizer_phone'];

                            $organizer_arr[] =  $supply_data;
                        }                       

                    }
                    if($organizer_arr){
                        $input1['organizer'] = $organizer_arr;
                    }
                    //Organizer ends here
    
    
    return $input1;
    
    
}

function saswp_course_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
    
    $input1 = array(
             '@context'			=> saswp_context_url(),
             '@type'				=> 'Course' ,	
             '@id'                           => trailingslashit(get_permalink()).'#course',    
             'name'			        => saswp_remove_warnings($all_post_meta, 'saswp_course_name_'.$schema_id, 'saswp_array'),
             'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_course_description_'.$schema_id, 'saswp_array'),			
             'courseCode'                   => saswp_remove_warnings($all_post_meta, 'saswp_course_code_'.$schema_id, 'saswp_array'),			
             'timeRequired'                   => saswp_remove_warnings($all_post_meta, 'saswp_course_duration_'.$schema_id, 'saswp_array'),			
             'url'				=> saswp_remove_warnings($all_post_meta, 'saswp_course_url_'.$schema_id, 'saswp_array'),
             'datePublished'                 => isset($all_post_meta['saswp_course_date_published_'.$schema_id][0])? saswp_format_date_time($all_post_meta['saswp_course_date_published_'.$schema_id][0], get_post_time('h:i:s')) : '',
             'dateModified'                  => isset($all_post_meta['saswp_course_date_modified_'.$schema_id][0])? saswp_format_date_time($all_post_meta['saswp_course_date_modified_'.$schema_id][0], get_the_modified_time('h:i:s')) : '',
             'provider'			=> array(
                                                 '@type' 	        => 'Organization',
                                                 'name'		=> saswp_remove_warnings($all_post_meta, 'saswp_course_provider_name_'.$schema_id, 'saswp_array'),
                                                 'sameAs'		=> saswp_remove_warnings($all_post_meta, 'saswp_course_sameas_'.$schema_id, 'saswp_array') 
                                             )											
                 );

                if( isset($all_post_meta['saswp_course_content_location_name_'.$schema_id][0]) || isset($all_post_meta['saswp_course_content_location_locality_'.$schema_id][0]) || isset($all_post_meta['saswp_course_content_location_country_'.$schema_id][0])  ){

                        $input1['contentLocation']['@type']                        =   'Place';
                        $input1['contentLocation']['name']                         =   $all_post_meta['saswp_course_content_location_name_'.$schema_id][0];
                        $input1['contentLocation']['address']['addressLocality']   =   $all_post_meta['saswp_course_content_location_locality_'.$schema_id][0];
                        $input1['contentLocation']['address']['addressRegion']     =   $all_post_meta['saswp_course_content_location_region_'.$schema_id][0];
                        $input1['contentLocation']['address']['PostalCode']        =   $all_post_meta['saswp_course_content_location_postal_code_'.$schema_id][0];
                        $input1['contentLocation']['address']['addressCountry']    =   $all_post_meta['saswp_course_content_location_country_'.$schema_id][0];

                } 
                 
                 if(isset($all_post_meta['saswp_course_enable_rating_'.$schema_id]) && saswp_remove_warnings($all_post_meta, 'saswp_course_rating_'.$schema_id, 'saswp_array') && saswp_remove_warnings($all_post_meta, 'saswp_course_review_count_'.$schema_id, 'saswp_array')){
                                 
                    $input1['aggregateRating'] = array(
                                       "@type"       => "AggregateRating",
                                       "ratingValue" => saswp_remove_warnings($all_post_meta, 'saswp_course_rating_'.$schema_id, 'saswp_array'),
                                       "reviewCount" => saswp_remove_warnings($all_post_meta, 'saswp_course_review_count_'.$schema_id, 'saswp_array')
                    );                                       
            }
    
    return $input1;
    
}
function saswp_mobile_app_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
        
    $input1 = array(
             '@context'			=> saswp_context_url(),
             '@type'				=> 'MobileApplication',
             '@id'                           => trailingslashit(get_permalink()).'#MobileApplication',     
             'name'			        => saswp_remove_warnings($all_post_meta, 'saswp_mobile_app_schema_name_'.$schema_id, 'saswp_array'),
             'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_mobile_app_schema_description_'.$schema_id, 'saswp_array'),
             'operatingSystem'		=> saswp_remove_warnings($all_post_meta, 'saswp_mobile_app_schema_operating_system_'.$schema_id, 'saswp_array'),
             'applicationCategory'		=> saswp_remove_warnings($all_post_meta, 'saswp_mobile_app_schema_application_category_'.$schema_id, 'saswp_array'),                        
             'offers'                        => array(
                                                 '@type'         => 'Offer',
                                                 'price'         => saswp_remove_warnings($all_post_meta, 'saswp_mobile_app_schema_price_'.$schema_id, 'saswp_array'),	                         
                                                 'priceCurrency' => saswp_remove_warnings($all_post_meta, 'saswp_mobile_app_schema_price_currency_'.$schema_id, 'saswp_array'),	                         
                                              ),
             'datePublished'                 => isset($all_post_meta['saswp_mobile_app_schema_date_published_'.$schema_id][0])? saswp_format_date_time($all_post_meta['saswp_mobile_app_schema_date_published_'.$schema_id][0], get_post_time('h:i:s')) : '',
             'dateModified'                  => isset($all_post_meta['saswp_mobile_app_schema_date_modified_'.$schema_id][0])? saswp_format_date_time($all_post_meta['saswp_mobile_app_schema_date_modified_'.$schema_id][0], get_the_modified_time('h:i:s')) : '',

                );

                $soft_image = get_post_meta( get_the_ID(), 'saswp_mobile_app_schema_image_'.$schema_id.'_detail',true); 

                if(!(empty($soft_image))){

                     $input1['image']['@type']        = 'ImageObject';
                     $input1['image']['url']          = isset($soft_image['thumbnail']) ? esc_url($soft_image['thumbnail']):'';
                     $input1['image']['height']       = isset($soft_image['width'])     ? esc_attr($soft_image['width'])   :'';
                     $input1['image']['width']        = isset($soft_image['height'])    ? esc_attr($soft_image['height'])  :'';

                 }
                 
                 if(saswp_remove_warnings($all_post_meta, 'saswp_mobile_app_schema_enable_rating_'.$schema_id, 'saswp_array') == 1){   
                                 
                                          $input1['aggregateRating'] = array(
                                                            "@type"       => "AggregateRating",
                                                            "ratingValue" => saswp_remove_warnings($all_post_meta, 'saswp_mobile_app_schema_rating_value_'.$schema_id, 'saswp_array'),
                                                            "reviewCount" => saswp_remove_warnings($all_post_meta, 'saswp_mobile_app_schema_rating_count_'.$schema_id, 'saswp_array')
                                                         );                                       
                }
        
    return $input1;
    
}
function saswp_software_app_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
    
    
    $input1 = array(
             '@context'			=> saswp_context_url(),
             '@type'				=> 'SoftwareApplication',
             '@id'                           => trailingslashit(get_permalink()).'#softwareapplication',     
             'name'			        => saswp_remove_warnings($all_post_meta, 'saswp_software_schema_name_'.$schema_id, 'saswp_array'),
             'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_software_schema_description_'.$schema_id, 'saswp_array'),
             'operatingSystem'		=> saswp_remove_warnings($all_post_meta, 'saswp_software_schema_operating_system_'.$schema_id, 'saswp_array'),
             'applicationCategory'		=> saswp_remove_warnings($all_post_meta, 'saswp_software_schema_application_category_'.$schema_id, 'saswp_array'),                        
             'offers'                        => array(
                                                 '@type'         => 'Offer',
                                                 'price'         => saswp_remove_warnings($all_post_meta, 'saswp_software_schema_price_'.$schema_id, 'saswp_array'),	                         
                                                 'priceCurrency' => saswp_remove_warnings($all_post_meta, 'saswp_software_schema_price_currency_'.$schema_id, 'saswp_array'),	                         
                                              ),
             'datePublished'                 => isset($all_post_meta['saswp_software_schema_date_published_'.$schema_id][0])? saswp_format_date_time($all_post_meta['saswp_software_schema_date_published_'.$schema_id][0], get_post_time('h:i:s')) : '',
             'dateModified'                  => isset($all_post_meta['saswp_software_schema_date_modified_'.$schema_id][0])? saswp_format_date_time($all_post_meta['saswp_software_schema_date_modified_'.$schema_id][0], get_the_modified_time('h:i:s')) : '',

                );

                $soft_image = get_post_meta( get_the_ID(), 'saswp_software_schema_image_'.$schema_id.'_detail',true); 

                if(!(empty($soft_image))){

                     $input1['image']['@type']        = 'ImageObject';
                     $input1['image']['url']          = isset($soft_image['thumbnail']) ? esc_url($soft_image['thumbnail']):'';
                     $input1['image']['height']       = isset($soft_image['width'])     ? esc_attr($soft_image['width'])   :'';
                     $input1['image']['width']        = isset($soft_image['height'])    ? esc_attr($soft_image['height'])  :'';

                 }
                 
                 if(saswp_remove_warnings($all_post_meta, 'saswp_software_schema_enable_rating_'.$schema_id, 'saswp_array') == 1){   
                                 
                                          $input1['aggregateRating'] = array(
                                                            "@type"       => "AggregateRating",
                                                            "ratingValue" => saswp_remove_warnings($all_post_meta, 'saswp_software_schema_rating_'.$schema_id, 'saswp_array'),
                                                            "reviewCount" => saswp_remove_warnings($all_post_meta, 'saswp_software_schema_rating_count_'.$schema_id, 'saswp_array')
                                                         );                                       
                }
    
    
    return $input1;
    
}

function saswp_recipe_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
            $input1 = array();

            $recipe_logo    = get_post_meta( get_the_ID(), 'saswp_recipe_organization_logo_'.$schema_id.'_detail',true);
            $recipe_image   = get_post_meta( get_the_ID(), 'saswp_recipe_image_'.$schema_id.'_detail',true);                                                                           
            $recipe_author_image   = get_post_meta( get_the_ID(), 'saswp_recipe_author_image_'.$schema_id.'_detail',true);

            $ingredient     = array();
            $instruction    = array();

            if(isset($all_post_meta['saswp_recipe_ingredient_'.$schema_id])){
                 $ingredient = saswp_explod_by_semicolon($all_post_meta['saswp_recipe_ingredient_'.$schema_id][0]); 
            }

            if(isset($all_post_meta['saswp_recipe_instructions_'.$schema_id])){

                $explod = saswp_explod_by_semicolon($all_post_meta['saswp_recipe_instructions_'.$schema_id][0]);  
                
                   foreach ($explod as $val){

                        $instruction[] = array(
                                                   '@type'  => "HowToStep",
                                                   'text'   => $val,                                                                                                                            
                                                   );  

                  }                                                     
            }

            $input1 = array(
            '@context'			=> saswp_context_url(),
            '@type'				=> 'Recipe' ,
            '@id'                           => trailingslashit(get_permalink()).'#recipe',    
            'url'				=> saswp_remove_warnings($all_post_meta, 'saswp_recipe_url_'.$schema_id, 'saswp_array'),
            'name'			        => saswp_remove_warnings($all_post_meta, 'saswp_recipe_name_'.$schema_id, 'saswp_array'),
            'image'                         =>array(
             '@type'		=> 'ImageObject',
                'url'		=> saswp_remove_warnings( $recipe_image, 'thumbnail', 'saswp_string'),
            'width'		=> saswp_remove_warnings( $recipe_image, 'width', 'saswp_string'),
             'height'    => saswp_remove_warnings( $recipe_image , 'height', 'saswp_string'),
         ),
            'author'			=> array(
                                            '@type' 	=> 'Person',
                                            'name'		=> saswp_remove_warnings($all_post_meta, 'saswp_recipe_author_name_'.$schema_id, 'saswp_array'),
                                            'description'	=> saswp_remove_warnings($all_post_meta, 'saswp_recipe_author_description_'.$schema_id, 'saswp_array'),
                                            'Image'		=> array(
                                                    '@type'			=> 'ImageObject',
                                                    'url'			=> saswp_remove_warnings($all_post_meta, 'saswp_recipe_author_image_'.$schema_id, 'saswp_array'),
                                                    'height'		=> saswp_remove_warnings($recipe_author_image, 'height', 'saswp_string'),
                                                    'width'			=> saswp_remove_warnings($recipe_author_image, 'width', 'saswp_string')
                                            ),
                                    ),


            'prepTime'                       => saswp_remove_warnings($all_post_meta, 'saswp_recipe_preptime_'.$schema_id, 'saswp_array'),  
            'cookTime'                       => saswp_remove_warnings($all_post_meta, 'saswp_recipe_cooktime_'.$schema_id, 'saswp_array'),  
            'totalTime'                      => saswp_remove_warnings($all_post_meta, 'saswp_recipe_totaltime_'.$schema_id, 'saswp_array'),  
            'keywords'                       => saswp_remove_warnings($all_post_meta, 'saswp_recipe_keywords_'.$schema_id, 'saswp_array'),  
            'recipeYield'                    => saswp_remove_warnings($all_post_meta, 'saswp_recipe_recipeyield_'.$schema_id, 'saswp_array'),  
            'recipeCategory'                 => saswp_remove_warnings($all_post_meta, 'saswp_recipe_category_'.$schema_id, 'saswp_array'),
            'recipeCuisine'                  => saswp_remove_warnings($all_post_meta, 'saswp_recipe_cuisine_'.$schema_id, 'saswp_array'),  
            'nutrition'                      => array(
                                                '@type'  => "NutritionInformation",
                                                'calories'  => saswp_remove_warnings($all_post_meta, 'saswp_recipe_nutrition_'.$schema_id, 'saswp_array'),                                                                 
                                             ), 
            'recipeIngredient'               => $ingredient, 
            'recipeInstructions'             => $instruction,                                                                                                                                                                                 
            'datePublished'                 => isset($all_post_meta['saswp_recipe_date_published_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_recipe_date_published_'.$schema_id][0])):'',
            'dateModified'                  => isset($all_post_meta['saswp_recipe_date_modified_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_recipe_date_modified_'.$schema_id][0])):'',
            'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_recipe_description_'.$schema_id, 'saswp_array'),
            'mainEntity'                    => array(
                            '@type'				=> 'WebPage',
                            '@id'				=> saswp_remove_warnings($all_post_meta, 'saswp_recipe_main_entity_'.$schema_id, 'saswp_array'),						
                            'publisher'			=> array(
                                    '@type'			=> 'Organization',
                                    'logo' 			=> array(
                                            '@type'		=> 'ImageObject',
                                            'url'		=> saswp_remove_warnings($all_post_meta, 'saswp_recipe_organization_logo_'.$schema_id, 'saswp_array'),
                                            'width'		=> saswp_remove_warnings($recipe_logo, 'width', 'saswp_string'),
                                            'height'	=> saswp_remove_warnings($recipe_logo, 'height', 'saswp_string'),
                                            ),
                                    'name'			=> saswp_remove_warnings($all_post_meta, 'saswp_recipe_organization_name_'.$schema_id, 'saswp_array'),
                            ),
                    ),


            );

            if(saswp_remove_warnings($all_post_meta, 'saswp_recipe_video_name_'.$schema_id, 'saswp_array') !='' && saswp_remove_warnings($all_post_meta, 'saswp_recipe_video_thumbnailurl_'.$schema_id, 'saswp_array') !='' && saswp_remove_warnings($all_post_meta, 'saswp_recipe_video_description_'.$schema_id, 'saswp_array') !=''){

                $input1['video']['@type']        = 'VideoObject';
                $input1['video']['name']         = saswp_remove_warnings($all_post_meta, 'saswp_recipe_video_name_'.$schema_id, 'saswp_array');
                $input1['video']['description']  = saswp_remove_warnings($all_post_meta, 'saswp_recipe_video_description_'.$schema_id, 'saswp_array');
                $input1['video']['thumbnailUrl'] = saswp_remove_warnings($all_post_meta, 'saswp_recipe_video_thumbnailurl_'.$schema_id, 'saswp_array');
                $input1['video']['contentUrl']   = saswp_remove_warnings($all_post_meta, 'saswp_recipe_video_contenturl_'.$schema_id, 'saswp_array');
                $input1['video']['embedUrl']     = saswp_remove_warnings($all_post_meta, 'saswp_recipe_video_embedurl_'.$schema_id, 'saswp_array');
                $input1['video']['uploadDate']   = isset($all_post_meta['saswp_recipe_video_upload_date_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_recipe_video_upload_date_'.$schema_id][0])):'';
                $input1['video']['duration']     = saswp_remove_warnings($all_post_meta, 'saswp_recipe_video_duration_'.$schema_id, 'saswp_array');
            } 
            
            if(saswp_remove_warnings($all_post_meta, 'saswp_recipe_schema_enable_rating_'.$schema_id, 'saswp_array') == 1 && saswp_remove_warnings($all_post_meta, 'saswp_recipe_schema_rating_'.$schema_id, 'saswp_array') && saswp_remove_warnings($all_post_meta, 'saswp_recipe_schema_review_count_'.$schema_id, 'saswp_array')){   
                                                
                                    $input1['aggregateRating'] = array(
                                                    "@type"       => "AggregateRating",
                                                    "ratingValue" => saswp_remove_warnings($all_post_meta, 'saswp_recipe_schema_rating_'.$schema_id, 'saswp_array'),
                                                    "reviewCount" => saswp_remove_warnings($all_post_meta, 'saswp_recipe_schema_review_count_'.$schema_id, 'saswp_array')
                                                );                                       
                                }
    
    return $input1;
    
}

function saswp_product_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
            $input1 = array();
    
            $product_image = get_post_meta( get_the_ID(), 'saswp_product_schema_image_'.$schema_id.'_detail',true);                                                                           
            $input1 = array(
            '@context'			=> saswp_context_url(),
            '@type'				=> 'Product',
            '@id'                           => trailingslashit(get_permalink()).'#product',    
            'url'				=> trailingslashit(get_permalink()),
            'name'                          => saswp_remove_warnings($all_post_meta, 'saswp_product_schema_name_'.$schema_id, 'saswp_array'),
            'sku'                           => saswp_remove_warnings($all_post_meta, 'saswp_product_schema_sku_'.$schema_id, 'saswp_array'),
            'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_product_schema_description_'.$schema_id, 'saswp_array'),													                       
            'brand'                         => array('@type' => 'Thing',
                                                     'name'  => saswp_remove_warnings($all_post_meta, 'saswp_product_schema_brand_name_'.$schema_id, 'saswp_array'),
                                                    )    
            ); 
            
            if(isset($product_image['thumbnail']) && $product_image['thumbnail']){
                
                $input1['image']['@type']           = 'ImageObject';
                $input1['image']['url']             = $product_image['thumbnail'];
                $input1['image']['width']           = $product_image['width'];
                $input1['image']['height']          = $product_image['height'];
                
            }
            
            if(isset($all_post_meta['saswp_product_schema_price_'.$schema_id][0]) && $all_post_meta['saswp_product_schema_price_'.$schema_id][0]){
                            
                $input1['offers']['@type']           = 'Offer';
                $input1['offers']['availability']    = saswp_remove_warnings($all_post_meta, 'saswp_product_schema_availability_'.$schema_id, 'saswp_array');
                $input1['offers']['itemCondition']   = saswp_remove_warnings($all_post_meta, 'saswp_product_schema_condition_'.$schema_id, 'saswp_array');
                $input1['offers']['price']           = saswp_remove_warnings($all_post_meta, 'saswp_product_schema_price_'.$schema_id, 'saswp_array');
                $input1['offers']['priceCurrency']   = saswp_remove_warnings($all_post_meta, 'saswp_product_schema_currency_'.$schema_id, 'saswp_array');
                $input1['offers']['url']             = trailingslashit(saswp_get_permalink());
                $input1['offers']['priceValidUntil'] = isset($all_post_meta['saswp_product_schema_priceValidUntil_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_product_schema_priceValidUntil_'.$schema_id][0])):'';
            
                if(isset($all_post_meta['saswp_product_schema_seller_'.$schema_id])){
                    $input1['offers']['seller']['@type']   = 'Organization';
                    $input1['offers']['seller']['name']    = esc_attr($all_post_meta['saswp_product_schema_seller_'.$schema_id][0]);  
                }
            }
                                                    
            if(isset($all_post_meta['saswp_product_schema_gtin8_'.$schema_id])){
                $input1['gtin8'] = esc_attr($all_post_meta['saswp_product_schema_gtin8_'.$schema_id][0]);  
            }
            if(isset($all_post_meta['saswp_product_schema_gtin13_'.$schema_id])){
                $input1['gtin13'] = esc_attr($all_post_meta['saswp_product_schema_gtin13_'.$schema_id][0]);  
            }
            if(isset($all_post_meta['saswp_product_schema_mpn_'.$schema_id])){
              $input1['mpn'] = esc_attr($all_post_meta['saswp_product_schema_mpn_'.$schema_id][0]);  
            }

            if(isset($all_post_meta['saswp_product_additional_type_'.$schema_id][0])){
                $input1['additionalType'] = esc_attr($all_post_meta['saswp_product_additional_type_'.$schema_id][0]);  
            }
            
            if(saswp_remove_warnings($all_post_meta, 'saswp_product_schema_enable_rating_'.$schema_id, 'saswp_array') == 1 && saswp_remove_warnings($all_post_meta, 'saswp_product_schema_rating_'.$schema_id, 'saswp_array') && saswp_remove_warnings($all_post_meta, 'saswp_product_schema_review_count_'.$schema_id, 'saswp_array')){   
                                 
                                          $input1['aggregateRating'] = array(
                                                            "@type"       => "AggregateRating",
                                                            "ratingValue" => saswp_remove_warnings($all_post_meta, 'saswp_product_schema_rating_'.$schema_id, 'saswp_array'),
                                                            "reviewCount" => saswp_remove_warnings($all_post_meta, 'saswp_product_schema_review_count_'.$schema_id, 'saswp_array')
                                                         );                                       
                                         }
                                             
                                         
                                        $itinerary  = get_post_meta($schema_post_id, 'product_reviews_'.$schema_id, true);
                            
                                        $itinerary_arr = array();

                                        if(!empty($itinerary)){

                                         foreach($itinerary as $review){
                                                
                                          $review_fields = array();
                                          
                                          $review_fields['@type']         = 'Review';
                                          $review_fields['author']        = esc_attr($review['saswp_product_reviews_reviewer_name']);
                                          $review_fields['datePublished'] = esc_html($review['saswp_product_reviews_created_date']);
                                          $review_fields['description']   = esc_textarea($review['saswp_product_reviews_text']);
                                                                                    
                                          if(is_int($review['saswp_product_reviews_reviewer_rating'])){
                                              
                                                $review_fields['reviewRating']['@type']   = 'Rating';
                                                $review_fields['reviewRating']['bestRating']   = '5';
                                                $review_fields['reviewRating']['ratingValue']   = esc_attr($review['saswp_product_reviews_reviewer_rating']);
                                                $review_fields['reviewRating']['worstRating']   = '1';
                                          
                                          }
                                                                                                                                                                        
                                          $itinerary_arr[] = $review_fields;
                                            }
                                           $input1['review'] = $itinerary_arr;
                                        }
                                        
                                        $service = new saswp_output_service();
                                        $product_details = $service->saswp_woocommerce_product_details(get_the_ID());  


                                        if(!empty($product_details['product_reviews'])){
                                      
                                        $reviews = array();
                                      
                                         foreach ($product_details['product_reviews'] as $review){
                                                                                          
                                          $review_fields = array();
                                          
                                          $review_fields['@type']         = 'Review';
                                          $review_fields['author']        = esc_attr($review['author']);
                                          $review_fields['datePublished'] = esc_html($review['datePublished']);
                                          $review_fields['description']   = $review['description'];
                                                                                    
                                          if(isset($review['reviewRating']) && $review['reviewRating'] !=''){
                                              
                                                $review_fields['reviewRating']['@type']   = 'Rating';
                                                $review_fields['reviewRating']['bestRating']   = '5';
                                                $review_fields['reviewRating']['ratingValue']   = esc_attr($review['reviewRating']);
                                                $review_fields['reviewRating']['worstRating']   = '1';
                                          
                                          }
                                                                                                                                                                        
                                          $reviews[] = $review_fields;
                                          
                                      }
                                         $input1['review'] =  $reviews;
                                }
    
    return $input1;
    
}

function saswp_real_estate_listing_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $product_image = get_post_meta( get_the_ID(), 'saswp_real_estate_listing_image_'.$schema_id.'_detail',true);

    $input1 = array(
        '@context'			=> saswp_context_url(),
        '@type'				=> 'RealEstateListing',
        '@id'                           => trailingslashit(get_permalink()).'#RealEstateListing',    
        'url'				=> trailingslashit(get_permalink()),
        'name'                          => saswp_remove_warnings($all_post_meta, 'saswp_real_estate_listing_name_'.$schema_id, 'saswp_array'),    
        'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_real_estate_listing_description_'.$schema_id, 'saswp_array'),													                            
        );
                
        $input1['datePosted']           = isset($all_post_meta['saswp_real_estate_listing_date_posted_'.$schema_id][0])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_real_estate_listing_date_posted_'.$schema_id][0])):'';
        
        if(isset($product_image['thumbnail']) && $product_image['thumbnail']){
        
            $input1['image']['@type']           = 'ImageObject';
            $input1['image']['url']             = $product_image['thumbnail'];
            $input1['image']['width']           = $product_image['width'];
            $input1['image']['height']          = $product_image['height'];
            
        }

        if(isset($all_post_meta['saswp_real_estate_listing_price_'.$schema_id][0]) && $all_post_meta['saswp_real_estate_listing_price_'.$schema_id][0]){
                    
            $input1['offers']['@type']           = 'Offer';
            $input1['offers']['availability']    = saswp_remove_warnings($all_post_meta, 'saswp_real_estate_listing_availability_'.$schema_id, 'saswp_array');        
            $input1['offers']['price']           = saswp_remove_warnings($all_post_meta, 'saswp_real_estate_listing_price_'.$schema_id, 'saswp_array');
            $input1['offers']['priceCurrency']   = saswp_remove_warnings($all_post_meta, 'saswp_real_estate_listing_currency_'.$schema_id, 'saswp_array');        
            $input1['offers']['validFrom']       = isset($all_post_meta['saswp_real_estate_listing_validfrom_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_real_estate_listing_validfrom_'.$schema_id][0])):'';
                
        }

        $location = array();
                            
        if(isset($all_post_meta['saswp_real_estate_listing_location_name_'.$schema_id][0])){

            $location[] = array(
                '@type' => 'Place',
                'name' => saswp_remove_warnings($all_post_meta, 'saswp_real_estate_listing_location_name_'.$schema_id, 'saswp_array'),                
                'telephone' => saswp_remove_warnings($all_post_meta, 'saswp_real_estate_listing_phone_'.$schema_id, 'saswp_array'),                
                'address' => array(
                            '@type' => 'PostalAddress',
                            'streetAddress' => saswp_remove_warnings($all_post_meta, 'saswp_real_estate_listing_streetaddress_'.$schema_id, 'saswp_array'),
                            'addressLocality' => saswp_remove_warnings($all_post_meta, 'saswp_real_estate_listing_locality_'.$schema_id, 'saswp_array'),
                            'addressRegion' => saswp_remove_warnings($all_post_meta, 'saswp_real_estate_listing_region_'.$schema_id, 'saswp_array'),  
                            'addressCountry' => saswp_remove_warnings($all_post_meta, 'saswp_real_estate_listing_country_'.$schema_id, 'saswp_array'),  
                            'postalCode   ' => saswp_remove_warnings($all_post_meta, 'saswp_real_estate_listing_postalcode_'.$schema_id, 'saswp_array'),  
                ),
            );
            $input1['contentLocation'] = $location;
        } 

    return $input1;

}

function saswp_psychological_treatment_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $product_image = get_post_meta( get_the_ID(), 'saswp_psychological_treatment_image_'.$schema_id.'_detail',true);

    $input1 = array(
        '@context'			=> saswp_context_url(),
        '@type'				=> 'PsychologicalTreatment',
        '@id'               => trailingslashit(get_permalink()).'#PsychologicalTreatment',    
        'url'				=> saswp_remove_warnings($all_post_meta, 'saswp_psychological_treatment_url_'.$schema_id, 'saswp_array'),
        'name'              => saswp_remove_warnings($all_post_meta, 'saswp_psychological_treatment_name_'.$schema_id, 'saswp_array'),    
        'description'       => saswp_remove_warnings($all_post_meta, 'saswp_psychological_treatment_description_'.$schema_id, 'saswp_array'),													                            
        );
                        
        if(isset($product_image['thumbnail']) && $product_image['thumbnail']){
        
            $input1['image']['@type']           = 'ImageObject';
            $input1['image']['url']             = $product_image['thumbnail'];
            $input1['image']['width']           = $product_image['width'];
            $input1['image']['height']          = $product_image['height'];
            
        }
        if(isset($all_post_meta['saswp_psychological_treatment_drug_'.$schema_id][0])){
            $input1['drug']                    = $all_post_meta['saswp_psychological_treatment_drug_'.$schema_id][0];
        }
        if(isset($all_post_meta['saswp_psychological_treatment_body_location_'.$schema_id][0])){
            $input1['bodyLocation']                    = $all_post_meta['saswp_psychological_treatment_body_location_'.$schema_id][0];
        }
        if(isset($all_post_meta['saswp_psychological_treatment_preparation_'.$schema_id][0])){
            $input1['preparation']                    = $all_post_meta['saswp_psychological_treatment_preparation_'.$schema_id][0];
        }
        if(isset($all_post_meta['saswp_psychological_treatment_followup_'.$schema_id][0])){
            $input1['followup']                    = $all_post_meta['saswp_psychological_treatment_followup_'.$schema_id][0];
        }
        if(isset($all_post_meta['saswp_psychological_treatment_how_performed_'.$schema_id][0])){
            $input1['Howperformed']                    = $all_post_meta['saswp_psychological_treatment_how_performed_'.$schema_id][0];
        }
        if(isset($all_post_meta['saswp_psychological_treatment_procedure_type_'.$schema_id][0])){
            $input1['procedureType']            = $all_post_meta['saswp_psychological_treatment_procedure_type_'.$schema_id][0];
        }
        if(isset($all_post_meta['saswp_psychological_treatment_medical_code_'.$schema_id][0])){
            $input1['code']                    = $all_post_meta['saswp_psychological_treatment_medical_code_'.$schema_id][0];
        }
        if(isset($all_post_meta['saswp_psychological_treatment_additional_type_'.$schema_id][0])){
            $input1['additionalType']          = $all_post_meta['saswp_psychological_treatment_additional_type_'.$schema_id][0];
        }                 

    return $input1;

}

function saswp_local_business_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
            $input1 = array();

            $operation_days      = explode( "rn", esc_html( stripslashes(saswp_remove_warnings($all_post_meta, 'saswp_dayofweek_'.$schema_id, 'saswp_array'))) );;                               
            $business_sub_name   = '';
            $business_type       = saswp_remove_warnings($all_post_meta, 'saswp_business_type_'.$schema_id, 'saswp_array'); 
            $post_specific_obj   = new saswp_post_specific();

            if(array_key_exists($business_type, $post_specific_obj->_local_sub_business)){

                $check_business_type = $post_specific_obj->_local_sub_business[$business_type];

                if(!empty($check_business_type)){

                 $business_sub_name = saswp_remove_warnings($all_post_meta, 'saswp_business_name_'.$schema_id, 'saswp_array');   

                }

            }

            if($business_sub_name){

            $local_business = $business_sub_name; 

            }else if($business_type){

            $local_business = $business_type;        

            }else{

            $local_business = 'LocalBusiness';  

            }   

            $local_image = get_post_meta( $schema_post_id, 'local_business_logo_'.$schema_id.'_detail',true);

            $input1 = array(
            '@context'			=> saswp_context_url(),
            '@type'				=> $local_business ,
            '@id'                           => ((isset($all_post_meta['local_business_id_'.$schema_id][0]) && $all_post_meta['local_business_id_'.$schema_id][0] !='') ? $all_post_meta['local_business_id_'.$schema_id][0] : trailingslashit(get_permalink()).'#'.strtolower($local_business)),        
            'name'                          => saswp_remove_warnings($all_post_meta, 'local_business_name_'.$schema_id, 'saswp_array'),                                   
            'url'				=> saswp_remove_warnings($all_post_meta, 'local_business_name_url_'.$schema_id, 'saswp_array'),				
            'description'                   => saswp_remove_warnings($all_post_meta, 'local_business_description_'.$schema_id, 'saswp_array'),				
            'image' 			=> array(
                                                '@type'		=> 'ImageObject',
                                                'url'		=> saswp_remove_warnings($local_image, 'thumbnail', 'saswp_string'),
                                                'width'		=> saswp_remove_warnings($local_image, 'width', 'saswp_string'),
                                                'height'            => saswp_remove_warnings($local_image, 'height', 'saswp_string'),
                                            ),    
            'address'                       => array(
                                            "@type"           => "PostalAddress",
                                            "streetAddress"   => saswp_remove_warnings($all_post_meta, 'local_street_address_'.$schema_id, 'saswp_array'),
                                            "addressLocality" => saswp_remove_warnings($all_post_meta, 'local_city_'.$schema_id, 'saswp_array'),
                                            "addressRegion"   => saswp_remove_warnings($all_post_meta, 'local_state_'.$schema_id, 'saswp_array'),
                                            "postalCode"      => saswp_remove_warnings($all_post_meta, 'local_postal_code_'.$schema_id, 'saswp_array'),                                                                                                                                  
                                             ),	
            'telephone'                   => saswp_remove_warnings($all_post_meta, 'local_phone_'.$schema_id, 'saswp_array'),
            'openingHours'                => $operation_days,                                                                                                     
            );
            
                if(isset($all_post_meta['local_additional_type_'.$schema_id][0])){
                    $input1['additionalType'] = $all_post_meta['local_additional_type_'.$schema_id][0];   
                }

                if(isset($all_post_meta['local_price_range_'.$schema_id][0])){
                   $input1['priceRange'] = esc_attr($all_post_meta['local_price_range_'.$schema_id][0]);   
                }

                if(isset($all_post_meta['local_accepts_reservations_'.$schema_id][0])){
                  $input1['acceptsReservations'] = esc_attr($all_post_meta['local_price_accepts_reservations_'.$schema_id][0]);   
                }

                if(isset($all_post_meta['local_serves_cuisine_'.$schema_id][0])){
                  $input1['servesCuisine'] = esc_attr($all_post_meta['local_serves_cuisine_'.$schema_id][0]);   
                }
                
                if(isset($all_post_meta['local_area_served_'.$schema_id][0])){
                    
                  $area_served = explode(',', $all_post_meta['local_area_served_'.$schema_id][0]);
                  
                  $input1['areaServed'] = $area_served;   
                  
                }
               
                //social fields starts here

                $local_social = array();

                if(isset($all_post_meta['local_facebook_'.$schema_id][0]) && $all_post_meta['local_facebook_'.$schema_id][0] !=''){
                  $local_social[] = esc_url($all_post_meta['local_facebook_'.$schema_id][0]);   
                }
                if(isset($all_post_meta['local_twitter_'.$schema_id][0]) && $all_post_meta['local_twitter_'.$schema_id][0] !=''){
                  $local_social[] = esc_url($all_post_meta['local_twitter_'.$schema_id][0]);   
                }
                if(isset($all_post_meta['local_instagram_'.$schema_id][0]) && $all_post_meta['local_instagram_'.$schema_id][0] !=''){
                  $local_social[] = esc_url($all_post_meta['local_instagram_'.$schema_id][0]);   
                }
                if(isset($all_post_meta['local_pinterest_'.$schema_id][0]) && $all_post_meta['local_pinterest_'.$schema_id][0] !=''){
                  $local_social[] = esc_url($all_post_meta['local_pinterest_'.$schema_id][0]);   
                }
                if(isset($all_post_meta['local_linkedin_'.$schema_id][0]) && $all_post_meta['local_linkedin_'.$schema_id][0] !=''){
                  $local_social[] = esc_url($all_post_meta['local_linkedin_'.$schema_id][0]);   
                }
                if(isset($all_post_meta['local_soundcloud_'.$schema_id][0]) && $all_post_meta['local_soundcloud_'.$schema_id][0] !=''){
                  $local_social[] = esc_url($all_post_meta['local_soundcloud_'.$schema_id][0]);   
                }
                if(isset($all_post_meta['local_tumblr_'.$schema_id][0]) && $all_post_meta['local_tumblr_'.$schema_id][0] !=''){
                  $local_social[] = esc_url($all_post_meta['local_tumblr_'.$schema_id][0]);   
                }
                if(isset($all_post_meta['local_youtube_'.$schema_id][0]) && $all_post_meta['local_youtube_'.$schema_id][0] !=''){
                  $local_social[] = esc_url($all_post_meta['local_youtube_'.$schema_id][0]);   
                }

                if(!empty($local_social)){
                  $input1['sameAs'] =  $local_social; 
                }
                //social fields ends here

                if(isset($all_post_meta['local_menu_'.$schema_id][0])){
                  $input1['hasMenu'] = esc_url($all_post_meta['local_menu_'.$schema_id][0]);   
                }

                if(isset($all_post_meta['local_hasmap_'.$schema_id][0])){
                  $input1['hasMap'] = esc_url($all_post_meta['local_hasmap_'.$schema_id][0]);   
                }

                if(isset($all_post_meta['local_latitude_'.$schema_id][0]) && isset($all_post_meta['local_longitude_'.$schema_id][0])){

                    $input1['geo']['@type']     = 'GeoCoordinates';
                    $input1['geo']['latitude']  = $all_post_meta['local_latitude_'.$schema_id][0];
                    $input1['geo']['longitude'] = $all_post_meta['local_longitude_'.$schema_id][0];

                }
                
                if(isset($all_post_meta['local_enable_rating_'.$schema_id]) && saswp_remove_warnings($all_post_meta, 'local_rating_'.$schema_id, 'saswp_array') && saswp_remove_warnings($all_post_meta, 'local_review_count_'.$schema_id, 'saswp_array')){
                                 
                        $input1['aggregateRating'] = array(
                                           "@type"       => "AggregateRating",
                                           "ratingValue" => saswp_remove_warnings($all_post_meta, 'local_rating_'.$schema_id, 'saswp_array'),
                                           "reviewCount" => saswp_remove_warnings($all_post_meta, 'local_review_count_'.$schema_id, 'saswp_array')
                        );                                       
                }
    
    return $input1;
}

function saswp_organization_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
            $input1 = array();
           
            $input1['@context']                     = saswp_context_url();
            $input1['@type']                        = 'Organization';
            $input1['@id']                          = trailingslashit(get_permalink()).'#Organization'; 
            $input1['name']                         = saswp_remove_warnings($all_post_meta, 'saswp_organization_name_'.$schema_id, 'saswp_array');
            $input1['url']                          = saswp_remove_warnings($all_post_meta, 'saswp_organization_url_'.$schema_id, 'saswp_array');                            
            $input1['description']                  = saswp_remove_warnings($all_post_meta, 'saswp_organization_description_'.$schema_id, 'saswp_array');
           
            $howto_image = get_post_meta( get_the_ID(), 'saswp_organization_logo_'.$schema_id.'_detail',true); 
            
          if(!(empty($howto_image))){

            $input1['logo']['@type']        = 'ImageObject';
            $input1['logo']['url']          = isset($howto_image['thumbnail']) ? esc_url($howto_image['thumbnail']):'';
            $input1['logo']['height']       = isset($howto_image['width'])     ? esc_attr($howto_image['width'])   :'';
            $input1['logo']['width']        = isset($howto_image['height'])    ? esc_attr($howto_image['height'])  :'';

          }
          
          $input1['address']['@type']             = 'PostalAddress';
          $input1['address']['streetAddress']     = saswp_remove_warnings($all_post_meta, 'saswp_organization_street_address_'.$schema_id, 'saswp_array');
          $input1['address']['addressCountry']    = saswp_remove_warnings($all_post_meta, 'saswp_organization_country_'.$schema_id, 'saswp_array');
          $input1['address']['addressLocality']   = saswp_remove_warnings($all_post_meta, 'saswp_organization_city_'.$schema_id, 'saswp_array');
          $input1['address']['addressRegion']     = saswp_remove_warnings($all_post_meta, 'saswp_organization_state_'.$schema_id, 'saswp_array');
          $input1['address']['PostalCode']        = saswp_remove_warnings($all_post_meta, 'saswp_organization_postal_code_'.$schema_id, 'saswp_array');
          $input1['address']['telephone']         = saswp_remove_warnings($all_post_meta, 'saswp_organization_telephone_'.$schema_id, 'saswp_array');                                                        
          $input1['address']['email']             = saswp_remove_warnings($all_post_meta, 'saswp_organization_email_'.$schema_id, 'saswp_array');                                                        
          
          if( isset($all_post_meta['saswp_organization_duns_'.$schema_id][0]) ){
            $input1['duns']         = $all_post_meta['saswp_organization_duns_'.$schema_id][0];            
          }
          if( isset($all_post_meta['saswp_organization_founder_'.$schema_id][0]) ){
            $input1['founder']         = $all_post_meta['saswp_organization_founder_'.$schema_id][0];            
          }
          if( isset($all_post_meta['saswp_organization_founding_date_'.$schema_id][0]) ){
            $input1['foundingDate']         = saswp_format_date_time($all_post_meta['saswp_organization_founding_date_'.$schema_id][0]);            
          }
          if( isset($all_post_meta['saswp_organization_qualifications_'.$schema_id][0]) ){
            $input1['hasCredential']         = $all_post_meta['saswp_organization_qualifications_'.$schema_id][0];            
          }
          if( isset($all_post_meta['saswp_organization_knows_about_'.$schema_id][0]) ){
            $input1['knowsAbout']         = $all_post_meta['saswp_organization_knows_about_'.$schema_id][0];            
          }
          if( isset($all_post_meta['saswp_organization_member_of_'.$schema_id][0]) ){
            $input1['memberOf']         = $all_post_meta['saswp_organization_member_of_'.$schema_id][0];            
          }
          if( isset($all_post_meta['saswp_organization_parent_organization_'.$schema_id][0]) ){
            $input1['parentOrganization']         = $all_post_meta['saswp_organization_parent_organization_'.$schema_id][0];            
          }          
          if(isset($all_post_meta['saswp_organization_enable_rating_'.$schema_id]) && isset($all_post_meta['saswp_organization_rating_value_'.$schema_id]) && isset($all_post_meta['saswp_organization_rating_count_'.$schema_id])){
                $input1['aggregateRating']['@type']         = 'aggregateRating';
                $input1['aggregateRating']['ratingValue']   = $all_post_meta['saswp_organization_rating_value_'.$schema_id][0];
                $input1['aggregateRating']['ratingCount']   = $all_post_meta['saswp_organization_rating_count_'.$schema_id][0];                                
          }          
                              
        return $input1;
}

function saswp_video_game_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
            $input1 = array();

            $howto_image = get_post_meta( get_the_ID(), 'saswp_vg_schema_image_'.$schema_id.'_detail',true);  

            $input1['@context']                     = saswp_context_url();
            $input1['@type']                        = 'VideoGame';
            $input1['@id']                          = trailingslashit(get_permalink()).'#VideoGame'; 
            $input1['name']                         = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_name_'.$schema_id, 'saswp_array');
            $input1['url']                          = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_url_'.$schema_id, 'saswp_array');                            
            $input1['description']                  = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_description_'.$schema_id, 'saswp_array');

            if(!(empty($howto_image))){

            $input1['image']['@type']        = 'ImageObject';
            $input1['image']['url']          = isset($howto_image['thumbnail']) ? esc_url($howto_image['thumbnail']):'';
            $input1['image']['height']       = isset($howto_image['width'])     ? esc_attr($howto_image['width'])   :'';
            $input1['image']['width']        = isset($howto_image['height'])    ? esc_attr($howto_image['height'])  :'';

            }

            $input1['operatingSystem']  = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_operating_system_'.$schema_id, 'saswp_array');
            $input1['applicationCategory']  = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_application_category_'.$schema_id, 'saswp_array');

            $input1['author']['@type']  = 'Organization';
            $input1['author']['name']   = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_author_name_'.$schema_id, 'saswp_array');

            $input1['offers']['@type']  = 'Offer';                            
            $input1['offers']['price']  = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_price_'.$schema_id, 'saswp_array');
            $input1['offers']['priceCurrency']  = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_price_currency_'.$schema_id, 'saswp_array');
            $input1['offers']['availability']  = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_price_availability_'.$schema_id, 'saswp_array');


            $input1['publisher'] = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_publisher_'.$schema_id, 'saswp_array');
            $input1['genre'] = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_genre_'.$schema_id, 'saswp_array');
            $input1['processorRequirements'] = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_processor_requirements_'.$schema_id, 'saswp_array');
            $input1['memoryRequirements'] = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_memory_requirements_'.$schema_id, 'saswp_array');
            $input1['storageRequirements'] = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_storage_requirements_'.$schema_id, 'saswp_array');
            $input1['gamePlatform'] = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_game_platform_'.$schema_id, 'saswp_array');
            $input1['cheatCode'] = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_cheat_code_'.$schema_id, 'saswp_array');
            
            if( saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_enable_rating_'.$schema_id, 'saswp_array') == 1 && saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_rating_'.$schema_id, 'saswp_array') && saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_review_count_'.$schema_id, 'saswp_array')){
                            
                            $input1['aggregateRating']['@type']       = 'AggregateRating';
                            $input1['aggregateRating']['ratingValue'] = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_rating_'.$schema_id, 'saswp_array');
                            $input1['aggregateRating']['ratingCount'] = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_review_count_'.$schema_id, 'saswp_array');
                                
            }
    
    return $input1;
}

function saswp_music_playlist_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
            $input1 = array();

            $input1['@context']              = saswp_context_url();
            $input1['@type']                 = 'MusicPlaylist';
            $input1['@id']                   = trailingslashit(get_permalink()).'#MusicPlaylist';                            
            $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_music_playlist_url_'.$schema_id, 'saswp_array');                                
            $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_music_playlist_name_'.$schema_id, 'saswp_array');                            
            $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_music_playlist_description_'.$schema_id, 'saswp_array');                                

            $faq_question  = get_post_meta($schema_post_id, 'music_playlist_track_'.$schema_id, true);

            $faq_question_arr = array();

            if(!empty($faq_question)){

                $input1['numTracks'] = count($faq_question);

                foreach($faq_question as $val){

                    $supply_data = array();
                    $supply_data['@type']                   = 'MusicRecording';
                    $supply_data['byArtist']                = $val['saswp_music_playlist_track_artist'];
                    $supply_data['duration']                = $val['saswp_music_playlist_track_duration'];
                    $supply_data['inAlbum']                 = $val['saswp_music_playlist_track_inalbum'];
                    $supply_data['name']                    = $val['saswp_music_playlist_track_name'];
                    $supply_data['url']                     = $val['saswp_music_playlist_track_url'];

                   $faq_question_arr[] =  $supply_data;
                }
               $input1['track'] = $faq_question_arr;
            }
    
    return $input1;
}

function saswp_music_composition_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
            $input1 = array();

            $input1['@context']              = saswp_context_url();
            $input1['@type']                 = 'MusicComposition';
            $input1['@id']                   = trailingslashit(get_permalink()).'#MusicComposition';                            
            $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_music_composition_url_'.$schema_id, 'saswp_array');                                
            $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_music_composition_name_'.$schema_id, 'saswp_array');                            
            $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_music_composition_description_'.$schema_id, 'saswp_array');                                
            $input1['iswcCode']              = saswp_remove_warnings($all_post_meta, 'saswp_music_composition_iswccode_'.$schema_id, 'saswp_array');                                
            $input1['inLanguage']            = saswp_remove_warnings($all_post_meta, 'saswp_music_composition_inlanguage_'.$schema_id, 'saswp_array');                                
            $input1['datePublished']         = isset($all_post_meta['saswp_music_composition_date_published_'.$schema_id][0])&& $all_post_meta['saswp_music_composition_date_published_'.$schema_id][0] !='' ? date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_music_composition_date_published_'.$schema_id][0])):'';
                        
            if(isset($all_post_meta['saswp_music_composition_lyrics_'.$schema_id][0])){
                $input1['lyrics']['@type'] = 'CreativeWork';
                $input1['lyrics']['text'] = $all_post_meta['saswp_music_composition_lyrics_'.$schema_id][0];
            }
            
            if(isset($all_post_meta['saswp_music_composition_publisher_'.$schema_id][0])){
                $input1['publisher']['@type'] = 'Organization';
                $input1['publisher']['name'] = $all_post_meta['saswp_music_composition_publisher_'.$schema_id][0];
            }
            
            $image = get_post_meta( get_the_ID(), 'saswp_music_composition_image_'.$schema_id.'_detail',true);
            
            if(!(empty($image))){

            $input1['image']['@type']        = 'ImageObject';
            $input1['image']['url']          = isset($image['thumbnail']) ? esc_url($image['thumbnail']):'';
            $input1['image']['height']       = isset($image['width'])     ? esc_attr($image['width'])   :'';
            $input1['image']['width']        = isset($image['height'])    ? esc_attr($image['height'])  :'';

            }

            $faq_question  = get_post_meta($schema_post_id, 'music_composer_'.$schema_id, true);

            $faq_question_arr = array();

            if(!empty($faq_question)){
               
                foreach($faq_question as $val){

                    $supply_data = array();
                    $supply_data['@type']      = 'Person';
                    $supply_data['name']       = $val['saswp_music_composition_composer_name'];
                    $supply_data['url']        = $val['saswp_music_composition_composer_url'];                    

                   $faq_question_arr[] =  $supply_data;
                }
               $input1['composer'] = $faq_question_arr;
            }
    
    return $input1;
}

function saswp_person_schema_markup($schema_id, $schema_post_id, $all_post_meta){
        
        $input1 = array();

        $image = get_post_meta( get_the_ID(), 'saswp_trip_schema_image_'.$schema_id.'_detail',true); 

        $input1['@context']              = saswp_context_url();
        $input1['@type']                 = 'Person';
        $input1['@id']                   = trailingslashit(get_permalink()).'#Person';
        $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_person_schema_url_'.$schema_id, 'saswp_array');                            
        $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_person_schema_name_'.$schema_id, 'saswp_array');                                                        
        $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_person_schema_description_'.$schema_id, 'saswp_array');                                                        
        $input1['gender']                = saswp_remove_warnings($all_post_meta, 'saswp_person_schema_gender_'.$schema_id, 'saswp_array');                                                        
        $input1['birthDate']             = isset($all_post_meta['saswp_person_schema_date_of_birth_'.$schema_id][0])&& $all_post_meta['saswp_person_schema_date_of_birth_'.$schema_id][0] !='' ? date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_person_schema_date_of_birth_'.$schema_id][0])):'';
        $input1['nationality']           = saswp_remove_warnings($all_post_meta, 'saswp_person_schema_nationality_'.$schema_id, 'saswp_array');                                                        
        $input1['jobTitle']              = saswp_remove_warnings($all_post_meta, 'saswp_person_schema_job_title_'.$schema_id, 'saswp_array');                                                        

        $input1['address']['@type']             = 'PostalAddress';
        $input1['address']['streetAddress']     = saswp_remove_warnings($all_post_meta, 'saswp_person_schema_street_address_'.$schema_id, 'saswp_array');
        $input1['address']['addressCountry']    = saswp_remove_warnings($all_post_meta, 'saswp_person_schema_country_'.$schema_id, 'saswp_array');
        $input1['address']['addressLocality']   = saswp_remove_warnings($all_post_meta, 'saswp_person_schema_locality_'.$schema_id, 'saswp_array');
        $input1['address']['addressRegion']     = saswp_remove_warnings($all_post_meta, 'saswp_person_schema_region_'.$schema_id, 'saswp_array');
        $input1['address']['PostalCode']        = saswp_remove_warnings($all_post_meta, 'saswp_person_schema_postal_code_'.$schema_id, 'saswp_array');

        $input1['telephone']                    = saswp_remove_warnings($all_post_meta, 'saswp_person_schema_telephone_'.$schema_id, 'saswp_array');                                                        
        $input1['email']                        = saswp_remove_warnings($all_post_meta, 'saswp_person_schema_email_'.$schema_id, 'saswp_array');                                                        

        if(!(empty($image))){

        $input1['image']['@type']        = 'ImageObject';
        $input1['image']['url']          = isset($image['thumbnail']) ? esc_url($image['thumbnail']):'';
        $input1['image']['height']       = isset($image['width'])     ? esc_attr($image['width'])   :'';
        $input1['image']['width']        = isset($image['height'])    ? esc_attr($image['height'])  :'';

        }

        if(isset($all_post_meta['saswp_person_schema_award_'.$schema_id][0])){
            $input1['award']        = $all_post_meta['saswp_person_schema_award_'.$schema_id][0];
        }
        if(isset($all_post_meta['saswp_person_schema_brand_'.$schema_id][0])){
            $input1['brand']        = $all_post_meta['saswp_person_schema_brand_'.$schema_id][0];
        }
        if(isset($all_post_meta['saswp_person_schema_honorific_prefix_'.$schema_id][0])){
            $input1['honorificPrefix']        = $all_post_meta['saswp_person_schema_honorific_prefix_'.$schema_id][0];
        }
        if(isset($all_post_meta['saswp_person_schema_honorific_suffix_'.$schema_id][0])){
            $input1['honorificSuffix']        = $all_post_meta['saswp_person_schema_honorific_suffix_'.$schema_id][0];
        }
        if(isset($all_post_meta['saswp_person_schema_qualifications_'.$schema_id][0])){
            $input1['hasCredential']        = $all_post_meta['saswp_person_schema_qualifications_'.$schema_id][0];
        }
        if(isset($all_post_meta['saswp_person_schema_affiliation_'.$schema_id][0])){
            $input1['affiliation']        = $all_post_meta['saswp_person_schema_affiliation_'.$schema_id][0];
        }
        if(isset($all_post_meta['saswp_person_schema_alumniof_'.$schema_id][0])){
            $input1['alumniOf']        = $all_post_meta['saswp_person_schema_alumniof_'.$schema_id][0];
        }

        $sameas = array();

        if(isset($all_post_meta['saswp_person_schema_website_'.$schema_id][0])){
            $sameas[]        = $all_post_meta['saswp_person_schema_website_'.$schema_id][0];
        }
        if(isset($all_post_meta['saswp_person_schema_facebook_'.$schema_id][0])){
            $sameas[]        = $all_post_meta['saswp_person_schema_facebook_'.$schema_id][0];
        }
        if(isset($all_post_meta['saswp_person_schema_twitter_'.$schema_id][0])){
            $sameas[]        = $all_post_meta['saswp_person_schema_twitter_'.$schema_id][0];
        }
        if(isset($all_post_meta['saswp_person_schema_linkedin_'.$schema_id][0])){
            $sameas[]        = $all_post_meta['saswp_person_schema_linkedin_'.$schema_id][0];
        }
        if($sameas){
            $input1['sameAs'] = $sameas;
        }

        if(isset($all_post_meta['saswp_person_schema_occupation_name_'.$schema_id][0])){
            $input1['hasOccupation']['name'] =    $all_post_meta['saswp_person_schema_occupation_name_'.$schema_id][0];
        }
        if(isset($all_post_meta['saswp_person_schema_occupation_description_'.$schema_id][0])){
            $input1['hasOccupation']['description'] =    $all_post_meta['saswp_person_schema_occupation_description_'.$schema_id][0];
        }
        if(isset($all_post_meta['saswp_person_schema_occupation_city_'.$schema_id][0])){
            $input1['hasOccupation']['occupationLocation']['@type'] = 'City'; 
            $input1['hasOccupation']['occupationLocation']['name']  =    $all_post_meta['saswp_person_schema_occupation_city_'.$schema_id][0];
        }
        if(isset($all_post_meta['saswp_person_schema_estimated_salary_'.$schema_id][0])){
            $input1['hasOccupation']['estimatedSalary']['@type']     =  'MonetaryAmountDistribution';
            $input1['hasOccupation']['estimatedSalary']['name']      =  'base';
            $input1['hasOccupation']['estimatedSalary']['currency']  =  $all_post_meta['saswp_person_schema_salary_currency_'.$schema_id][0];
            $input1['hasOccupation']['estimatedSalary']['duration']  =  $all_post_meta['saswp_person_schema_salary_duration_'.$schema_id][0];
            
            $input1['hasOccupation']['estimatedSalary']['percentile10']  =  $all_post_meta['saswp_person_schema_salary_percentile10_'.$schema_id][0];
            $input1['hasOccupation']['estimatedSalary']['percentile25']  =  $all_post_meta['saswp_person_schema_salary_percentile25_'.$schema_id][0];
            $input1['hasOccupation']['estimatedSalary']['median']        =  $all_post_meta['saswp_person_schema_salary_median_'.$schema_id][0];
            $input1['hasOccupation']['estimatedSalary']['percentile75']  =  $all_post_meta['saswp_person_schema_salary_percentile75_'.$schema_id][0];
            $input1['hasOccupation']['estimatedSalary']['percentile90']  =  $all_post_meta['saswp_person_schema_salary_percentile90_'.$schema_id][0];
        }
        if(isset($all_post_meta['saswp_person_schema_salary_last_reviewed_'.$schema_id][0])){
            $input1['hasOccupation']['mainEntityOfPage']['@type']         = 'WebPage'; 
            $input1['hasOccupation']['mainEntityOfPage']['lastReviewed']  =    saswp_format_date_time($all_post_meta['saswp_person_schema_salary_last_reviewed_'.$schema_id][0]);
        }

        return $input1;
}

function saswp_trip_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
    
    $howto_image = get_post_meta( get_the_ID(), 'saswp_trip_schema_image_'.$schema_id.'_detail',true); 

    $input1['@context']              = saswp_context_url();
    $input1['@type']                 = 'Trip';
    $input1['@id']                   = trailingslashit(get_permalink()).'#Trip';
    $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_trip_schema_url_'.$schema_id, 'saswp_array');                            
    $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_trip_schema_name_'.$schema_id, 'saswp_array');                            
    $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_trip_schema_description_'.$schema_id, 'saswp_array');                            

    if(!(empty($howto_image))){

    $input1['image']['@type']        = 'ImageObject';
    $input1['image']['url']          = isset($howto_image['thumbnail']) ? esc_url($howto_image['thumbnail']):'';
    $input1['image']['height']       = isset($howto_image['width'])     ? esc_attr($howto_image['width'])   :'';
    $input1['image']['width']        = isset($howto_image['height'])    ? esc_attr($howto_image['height'])  :'';

    }

    $itinerary  = get_post_meta($schema_post_id, 'trip_itinerary_'.$schema_id, true);

    $itinerary_arr = array();

    if(!empty($itinerary)){

        foreach($itinerary as $val){

            $supply_data = array();
            $supply_data['@type']        = $val['saswp_trip_itinerary_type'];
            $supply_data['name']         = $val['saswp_trip_itinerary_name'];
            $supply_data['description']  = $val['saswp_trip_itinerary_description'];
            $supply_data['url']          = $val['saswp_trip_itinerary_url'];


           $itinerary_arr[] =  $supply_data;
        }
       $input1['itinerary'] = $itinerary_arr;
    }
    
    
    return $input1;
}


function saswp_itemlist_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $response = array();
    
    $itemlist      = get_post_meta($schema_post_id, 'itemlist_item_'.$schema_id, true);  
    $itemtype      = get_post_meta($schema_id, 'saswp_itemlist_item_type', true);  
    $type_func     = 'saswp_'.$itemtype.'_schema_markup';    
    $list_item     = array();
    $image_details = array();
    $logo_details  = array();
    
    if($itemlist){
        
                  $i = 1;
                  foreach($itemlist as $item_meta){
            
                    $all_post_meta = array(); 
            
                    foreach($item_meta as $key => $val){
                        
                        $all_post_meta[$key.$schema_id] = array($val);   
                       
                        if( strpos($key, 'image__id') !== false ){
                         
                         $image_details   = saswp_get_image_by_id($val); 
                                                  
                        }
                        
                        if( strpos($key, 'logo__id') !== false ){
                         
                         $logo_details   = saswp_get_image_by_id($val); 
                                                  
                        }
                        
                    }
                   
                    if(function_exists($type_func)){
                        
                        $markup = call_user_func($type_func, $schema_id, $schema_post_id, $all_post_meta);                  
                        unset($markup['@context'], $markup['@id']);
                        
                        if($image_details){
                          $markup['image'] =  $image_details; 
                        }
                        if($logo_details){
                          $markup['publisher']['@type'] =  'Organization'; 
                          $markup['publisher']['logo']  =  $logo_details; 
                        }
                                                
                        $json_markup['@type']       = 'ListItem';
                        $json_markup['position']    = $i;
                        $json_markup['item']        = $markup;

                        $list_item[] = $json_markup;
                        
                    }
                    
                 $i++;
                }
            
                    $response['@context']                     = saswp_context_url();
                    $response['@context']                     = saswp_context_url();
                    $response['@type']                        = 'ItemList';  
                    $response['url']                          = saswp_get_permalink(); 
                    $response['itemListElement']              = $list_item;
       
    }    
    
    return $response;
    
}

function saswp_faq_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
    
    $input1['@context']              = saswp_context_url();
    $input1['@type']                 = 'FAQPage';
    $input1['@id']                   = trailingslashit(get_permalink()).'#FAQPage';  

    $input1['headline']              = saswp_remove_warnings($all_post_meta, 'saswp_faq_headline_'.$schema_id, 'saswp_array');                                                        
    $input1['keywords']              = saswp_remove_warnings($all_post_meta, 'saswp_faq_keywords_'.$schema_id, 'saswp_array');                                                        
    $input1['datePublished']         = isset($all_post_meta['saswp_faq_date_published_'.$schema_id][0])&& $all_post_meta['saswp_faq_date_published_'.$schema_id][0] !='' ? date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_faq_date_published_'.$schema_id][0])):'';
    $input1['dateModified']          = isset($all_post_meta['saswp_faq_date_modified_'.$schema_id][0])&& $all_post_meta['saswp_faq_date_modified_'.$schema_id][0] !='' ? date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_faq_date_modified_'.$schema_id][0])):'';
    $input1['dateCreated']           = isset($all_post_meta['saswp_faq_date_created_'.$schema_id][0])&& $all_post_meta['saswp_faq_date_created_'.$schema_id][0] !='' ? date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_faq_date_created_'.$schema_id][0])):'';

    if(isset($all_post_meta['saswp_faq_author_'.$schema_id][0])){
        $input1['author']['@type']       = 'Person';
        $input1['author']['name']        = $all_post_meta['saswp_faq_author_'.$schema_id][0];
    }

    $faq_question  = get_post_meta($schema_post_id, 'faq_question_'.$schema_id, true);

    $faq_question_arr = array();

    if(!empty($faq_question)){

        foreach($faq_question as $val){

            $supply_data = array();
            $supply_data['@type']                   = 'Question';
            $supply_data['name']                    = $val['saswp_faq_question_name'];
            $supply_data['acceptedAnswer']['@type'] = 'Answer';
            $supply_data['acceptedAnswer']['text']  = $val['saswp_faq_question_answer'];

           $faq_question_arr[] =  $supply_data;
        }
       $input1['mainEntity'] = $faq_question_arr;
    }
    
    return $input1;
    
}

function saswp_music_album_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
    
    $input1['@context']              = saswp_context_url();
    $input1['@type']                 = 'MusicAlbum';
    $input1['@id']                   = trailingslashit(get_permalink()).'#MusicAlbum';                            
    $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_music_album_url_'.$schema_id, 'saswp_array');                                
    $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_music_album_name_'.$schema_id, 'saswp_array');                            
    $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_music_album_description_'.$schema_id, 'saswp_array');                                
    $input1['genre']                 = saswp_remove_warnings($all_post_meta, 'saswp_music_album_genre_'.$schema_id, 'saswp_array');                                                            


    if(isset($all_post_meta['saswp_music_album_artist_'.$schema_id][0])){

        $input1['byArtist']['@type']     = 'MusicGroup';
        $input1['byArtist']['name']      = $all_post_meta['saswp_music_album_artist_'.$schema_id][0];

    }

    $howto_image = get_post_meta( get_the_ID(), 'saswp_music_album_image_'.$schema_id.'_detail',true); 

    if(!(empty($howto_image))){

    $input1['image']['@type']        = 'ImageObject';
    $input1['image']['url']          = isset($howto_image['thumbnail']) ? esc_url($howto_image['thumbnail']):'';
    $input1['image']['height']       = isset($howto_image['width'])     ? esc_attr($howto_image['width'])   :'';
    $input1['image']['width']        = isset($howto_image['height'])    ? esc_attr($howto_image['height'])  :'';

    }

    $faq_question  = get_post_meta($schema_post_id, 'music_album_track_'.$schema_id, true);

    $faq_question_arr = array();

    if(!empty($faq_question)){

        $input1['numTracks'] = count($faq_question);

        foreach($faq_question as $val){

            $supply_data = array();
            $supply_data['@type']                   = 'MusicRecording';                                    
            $supply_data['duration']                = $val['saswp_music_album_track_duration'];                                    
            $supply_data['name']                    = $val['saswp_music_album_track_name'];
            $supply_data['url']                     = $val['saswp_music_album_track_url'];                                                                                                                                                
           $faq_question_arr[] =  $supply_data;
        }
       $input1['track'] = $faq_question_arr;
    }
    
    return $input1;
}

function saswp_job_posting_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
    
    $howto_image = get_post_meta( get_the_ID(), 'saswp_jobposting_schema_ho_logo_'.$schema_id.'_detail',true); 

    $input1['@context']              = saswp_context_url();
    $input1['@type']                 = 'JobPosting';
    $input1['@id']                   = trailingslashit(get_permalink()).'#JobPosting';
    $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_url_'.$schema_id, 'saswp_array');                            
    $input1['title']                 = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_title_'.$schema_id, 'saswp_array');                            
    $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_description_'.$schema_id, 'saswp_array');
    $input1['datePosted']            = isset($all_post_meta['saswp_jobposting_schema_dateposted_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_jobposting_schema_dateposted_'.$schema_id][0])):'';                            
    $input1['validThrough']          = isset($all_post_meta['saswp_jobposting_schema_validthrough_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_jobposting_schema_validthrough_'.$schema_id][0])):'';                            
    $input1['employmentType']        = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_employment_type_'.$schema_id, 'saswp_array');

    $input1['hiringOrganization']['@type']     = 'Organization';
    $input1['hiringOrganization']['name']      = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_ho_name_'.$schema_id, 'saswp_array');
    $input1['hiringOrganization']['sameAs']    = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_ho_url_'.$schema_id, 'saswp_array');

    if(!(empty($howto_image))){

    $input1['hiringOrganization']['logo']['@type']        = 'ImageObject';
    $input1['hiringOrganization']['logo']['url']          = isset($howto_image['thumbnail']) ? esc_url($howto_image['thumbnail']):'';
    $input1['hiringOrganization']['logo']['height']       = isset($howto_image['width'])     ? esc_attr($howto_image['width'])   :'';
    $input1['hiringOrganization']['logo']['width']        = isset($howto_image['height'])    ? esc_attr($howto_image['height'])  :'';

    }

    $input1['jobLocation']['@type']                        = 'Place';
    $input1['jobLocation']['address']['@type']             = 'PostalAddress';                            
    $input1['jobLocation']['address']['streetAddress']     = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_street_address_'.$schema_id, 'saswp_array');
    $input1['jobLocation']['address']['addressLocality']   = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_locality_'.$schema_id, 'saswp_array');
    $input1['jobLocation']['address']['addressRegion']     = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_region_'.$schema_id, 'saswp_array');
    $input1['jobLocation']['address']['addressCountry']    = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_country_'.$schema_id, 'saswp_array');
    $input1['jobLocation']['address']['PostalCode']        = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_postalcode_'.$schema_id, 'saswp_array');


    $input1['baseSalary']['@type']             = 'MonetaryAmount';
    $input1['baseSalary']['currency']          = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_bs_currency_'.$schema_id, 'saswp_array');
    $input1['baseSalary']['value']['@type']    = 'QuantitativeValue';
    $input1['baseSalary']['value']['value']    = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_bs_value_'.$schema_id, 'saswp_array');
    $input1['baseSalary']['value']['unitText'] = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_bs_unittext_'.$schema_id, 'saswp_array');

    $input1['estimatedSalary']['@type']             = 'MonetaryAmount';
    $input1['estimatedSalary']['currency']          = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_es_currency_'.$schema_id, 'saswp_array');
    $input1['estimatedSalary']['value']['@type']    = 'QuantitativeValue';
    $input1['estimatedSalary']['value']['value']    = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_es_value_'.$schema_id, 'saswp_array');
    $input1['estimatedSalary']['value']['unitText'] = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_es_unittext_'.$schema_id, 'saswp_array');
    
    if(isset($all_post_meta['saswp_jobposting_schema_validthrough_'.$schema_id][0]) && date('Y-m-d',strtotime($all_post_meta['saswp_jobposting_schema_validthrough_'.$schema_id][0])) < date('Y-m-d')){
        $input1 = array();    
    }
    
    return $input1;            
}

function saswp_mosque_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
    
    $howto_image = get_post_meta( get_the_ID(), 'saswp_mosque_schema_image_'.$schema_id.'_detail',true); 

    $input1['@context']              = saswp_context_url();
    $input1['@type']                 = 'Mosque';
    $input1['@id']                   = trailingslashit(get_permalink()).'#Mosque';
    $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_mosque_schema_url_'.$schema_id, 'saswp_array');                            
    $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_mosque_schema_name_'.$schema_id, 'saswp_array');                            
    $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_mosque_schema_description_'.$schema_id, 'saswp_array');

    if(!(empty($howto_image))){

    $input1['image']['@type']        = 'ImageObject';
    $input1['image']['url']          = isset($howto_image['thumbnail']) ? esc_url($howto_image['thumbnail']):'';
    $input1['image']['height']       = isset($howto_image['width'])     ? esc_attr($howto_image['width'])   :'';
    $input1['image']['width']        = isset($howto_image['height'])    ? esc_attr($howto_image['height'])  :'';

    }  

    $input1['isAccessibleForFree']        = saswp_remove_warnings($all_post_meta, 'saswp_mosque_schema_is_accesible_free_'.$schema_id, 'saswp_array');                            
    $input1['maximumAttendeeCapacity']    = saswp_remove_warnings($all_post_meta, 'saswp_mosque_schema_maximum_a_capacity_'.$schema_id, 'saswp_array');
    $input1['hasMap']                     = saswp_remove_warnings($all_post_meta, 'saswp_mosque_schema_hasmap_'.$schema_id, 'saswp_array');

    $input1['address']['@type']             = 'PostalAddress';
    $input1['address']['addressCountry']    = saswp_remove_warnings($all_post_meta, 'saswp_mosque_schema_country_'.$schema_id, 'saswp_array');
    $input1['address']['addressLocality']   = saswp_remove_warnings($all_post_meta, 'saswp_mosque_schema_locality_'.$schema_id, 'saswp_array');
    $input1['address']['addressRegion']     = saswp_remove_warnings($all_post_meta, 'saswp_mosque_schema_region_'.$schema_id, 'saswp_array');
    $input1['address']['PostalCode']        = saswp_remove_warnings($all_post_meta, 'saswp_mosque_schema_postal_code_'.$schema_id, 'saswp_array');
    
    return $input1;
    
}

function saswp_church_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
    
    $howto_image = get_post_meta( get_the_ID(), 'saswp_church_schema_image_'.$schema_id.'_detail',true); 

    $input1['@context']              = saswp_context_url();
    $input1['@type']                 = 'Church';
    $input1['@id']                   = trailingslashit(get_permalink()).'#Church';
    $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_church_schema_url_'.$schema_id, 'saswp_array');                            
    $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_church_schema_name_'.$schema_id, 'saswp_array');                            
    $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_church_schema_description_'.$schema_id, 'saswp_array');

    if(!(empty($howto_image))){

    $input1['image']['@type']        = 'ImageObject';
    $input1['image']['url']          = isset($howto_image['thumbnail']) ? esc_url($howto_image['thumbnail']):'';
    $input1['image']['height']       = isset($howto_image['width'])     ? esc_attr($howto_image['width'])   :'';
    $input1['image']['width']        = isset($howto_image['height'])    ? esc_attr($howto_image['height'])  :'';

    }  

    $input1['isAccessibleForFree']        = saswp_remove_warnings($all_post_meta, 'saswp_church_schema_is_acceesible_free_'.$schema_id, 'saswp_array');                           
    $input1['maximumAttendeeCapacity']    = saswp_remove_warnings($all_post_meta, 'saswp_church_schema_maximum_a_capacity_'.$schema_id, 'saswp_array');
    $input1['hasMap']                     = saswp_remove_warnings($all_post_meta, 'saswp_church_schema_hasmap_'.$schema_id, 'saswp_array');

    $input1['address']['@type']             = 'PostalAddress';
    $input1['address']['addressCountry']    = saswp_remove_warnings($all_post_meta, 'saswp_church_schema_country_'.$schema_id, 'saswp_array');
    $input1['address']['addressLocality']   = saswp_remove_warnings($all_post_meta, 'saswp_church_schema_locality_'.$schema_id, 'saswp_array');
    $input1['address']['addressRegion']     = saswp_remove_warnings($all_post_meta, 'saswp_church_schema_region_'.$schema_id, 'saswp_array');
    $input1['address']['PostalCode']        = saswp_remove_warnings($all_post_meta, 'saswp_church_schema_postal_code_'.$schema_id, 'saswp_array');
    
    return $input1;
    
}

function saswp_buddhist_temple_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
    
    $howto_image = get_post_meta( get_the_ID(), 'saswp_buddhisttemple_schema_image_'.$schema_id.'_detail',true); 

    $input1['@context']              = saswp_context_url();
    $input1['@type']                 = 'BuddhistTemple';
    $input1['@id']                   = trailingslashit(get_permalink()).'#BuddhistTemple';
    $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_buddhisttemple_schema_url_'.$schema_id, 'saswp_array');                            
    $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_buddhisttemple_schema_name_'.$schema_id, 'saswp_array');                            
    $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_buddhisttemple_schema_description_'.$schema_id, 'saswp_array');

    if(!(empty($howto_image))){

    $input1['image']['@type']        = 'ImageObject';
    $input1['image']['url']          = isset($howto_image['thumbnail']) ? esc_url($howto_image['thumbnail']):'';
    $input1['image']['height']       = isset($howto_image['width'])     ? esc_attr($howto_image['width'])   :'';
    $input1['image']['width']        = isset($howto_image['height'])    ? esc_attr($howto_image['height'])  :'';

    }  

    $input1['isAccessibleForFree']        = saswp_remove_warnings($all_post_meta, 'saswp_buddhisttemple_schema_is_accesible_free_'.$schema_id, 'saswp_array');                           
    $input1['maximumAttendeeCapacity']    = saswp_remove_warnings($all_post_meta, 'saswp_buddhisttemple_schema_maximum_a_capacity_'.$schema_id, 'saswp_array');
    $input1['hasMap']                     = saswp_remove_warnings($all_post_meta, 'saswp_buddhisttemple_schema_hasmap_'.$schema_id, 'saswp_array');

    $input1['address']['@type']             = 'PostalAddress';
    $input1['address']['addressCountry']    = saswp_remove_warnings($all_post_meta, 'saswp_buddhisttemple_schema_country_'.$schema_id, 'saswp_array');
    $input1['address']['addressLocality']   = saswp_remove_warnings($all_post_meta, 'saswp_buddhisttemple_schema_locality_'.$schema_id, 'saswp_array');
    $input1['address']['addressRegion']     = saswp_remove_warnings($all_post_meta, 'saswp_buddhisttemple_schema_region_'.$schema_id, 'saswp_array');
    $input1['address']['PostalCode']        = saswp_remove_warnings($all_post_meta, 'saswp_buddhisttemple_schema_postal_code_'.$schema_id, 'saswp_array');
    
    return $input1;
    
}

function saswp_hindu_temple_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
    
    $howto_image = get_post_meta( get_the_ID(), 'saswp_hindutemple_schema_image_'.$schema_id.'_detail',true); 

    $input1['@context']              = saswp_context_url();
    $input1['@type']                 = 'HinduTemple';
    $input1['@id']                   = trailingslashit(get_permalink()).'#HinduTemple';
    $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_hindutemple_schema_url_'.$schema_id, 'saswp_array');                            
    $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_hindutemple_schema_name_'.$schema_id, 'saswp_array');                            
    $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_hindutemple_schema_description_'.$schema_id, 'saswp_array');

    if(!(empty($howto_image))){

    $input1['image']['@type']        = 'ImageObject';
    $input1['image']['url']          = isset($howto_image['thumbnail']) ? esc_url($howto_image['thumbnail']):'';
    $input1['image']['height']       = isset($howto_image['width'])     ? esc_attr($howto_image['width'])   :'';
    $input1['image']['width']        = isset($howto_image['height'])    ? esc_attr($howto_image['height'])  :'';

    }  

    $input1['isAccessibleForFree']        = saswp_remove_warnings($all_post_meta, 'saswp_hindutemple_schema_is_accesible_free_'.$schema_id, 'saswp_array');                           
    $input1['maximumAttendeeCapacity']    = saswp_remove_warnings($all_post_meta, 'saswp_hindutemple_schema_maximum_a_capacity_'.$schema_id, 'saswp_array');
    $input1['hasMap']                     = saswp_remove_warnings($all_post_meta, 'saswp_hindutemple_schema_hasmap_'.$schema_id, 'saswp_array');

    $input1['address']['@type']             = 'PostalAddress';
    $input1['address']['addressCountry']    = saswp_remove_warnings($all_post_meta, 'saswp_hindutemple_schema_country_'.$schema_id, 'saswp_array');
    $input1['address']['addressLocality']   = saswp_remove_warnings($all_post_meta, 'saswp_hindutemple_schema_locality_'.$schema_id, 'saswp_array');
    $input1['address']['addressRegion']     = saswp_remove_warnings($all_post_meta, 'saswp_hindutemple_schema_region_'.$schema_id, 'saswp_array');
    $input1['address']['PostalCode']        = saswp_remove_warnings($all_post_meta, 'saswp_hindutemple_schema_postal_code_'.$schema_id, 'saswp_array');
    
    return $input1;
    
}

function saswp_lorh_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
        
    $howto_image = get_post_meta( get_the_ID(), 'saswp_lorh_schema_image_'.$schema_id.'_detail',true); 

    $input1['@context']              = saswp_context_url();
    $input1['@type']                 = 'LandmarksOrHistoricalBuildings';
    $input1['@id']                   = trailingslashit(get_permalink()).'#LandmarksOrHistoricalBuildings';
    $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_lorh_schema_url_'.$schema_id, 'saswp_array');                            
    $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_lorh_schema_name_'.$schema_id, 'saswp_array');                            
    $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_lorh_schema_description_'.$schema_id, 'saswp_array');

    if(!(empty($howto_image))){

    $input1['image']['@type']        = 'ImageObject';
    $input1['image']['url']          = isset($howto_image['thumbnail']) ? esc_url($howto_image['thumbnail']):'';
    $input1['image']['height']       = isset($howto_image['width'])     ? esc_attr($howto_image['width'])   :'';
    $input1['image']['width']        = isset($howto_image['height'])    ? esc_attr($howto_image['height'])  :'';

    }  

    $input1['hasMap']                     = saswp_remove_warnings($all_post_meta, 'saswp_lorh_schema_hasmap_'.$schema_id, 'saswp_array');                        
    $input1['isAccessibleForFree']        = saswp_remove_warnings($all_post_meta, 'saswp_lorh_schema_is_acceesible_free_'.$schema_id, 'saswp_array');
    $input1['maximumAttendeeCapacity']    = saswp_remove_warnings($all_post_meta, 'saswp_lorh_schema_maximum_a_capacity_'.$schema_id, 'saswp_array');                          

    $input1['address']['@type']             = 'PostalAddress';
    $input1['address']['addressCountry']    = saswp_remove_warnings($all_post_meta, 'saswp_lorh_schema_country_'.$schema_id, 'saswp_array');
    $input1['address']['addressLocality']   = saswp_remove_warnings($all_post_meta, 'saswp_lorh_schema_locality_'.$schema_id, 'saswp_array');
    $input1['address']['addressRegion']     = saswp_remove_warnings($all_post_meta, 'saswp_lorh_schema_region_'.$schema_id, 'saswp_array');
    $input1['address']['PostalCode']        = saswp_remove_warnings($all_post_meta, 'saswp_lorh_schema_postal_code_'.$schema_id, 'saswp_array');

    if(isset($all_post_meta['saswp_lorh_schema_latitude_'.$schema_id][0]) && isset($all_post_meta['saswp_lorh_schema_longitude_'.$schema_id][0])){

        $input1['geo']['@type']     = 'GeoCoordinates';
        $input1['geo']['latitude']  = $all_post_meta['saswp_lorh_schema_latitude_'.$schema_id][0];
        $input1['geo']['longitude'] = $all_post_meta['saswp_lorh_schema_longitude_'.$schema_id][0];
    }
    
    return $input1;
    
}

function saswp_tourist_attraction_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
        $input1 = array();        
    
        $howto_image = get_post_meta( get_the_ID(), 'saswp_ta_schema_image_'.$schema_id.'_detail',true); 

        $input1['@context']              = saswp_context_url();
        $input1['@type']                 = 'TouristAttraction';
        $input1['@id']                   = trailingslashit(get_permalink()).'#TouristAttraction';
        $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_ta_schema_url_'.$schema_id, 'saswp_array');                            
        $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_ta_schema_name_'.$schema_id, 'saswp_array');                            
        $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_ta_schema_description_'.$schema_id, 'saswp_array');

        if(!(empty($howto_image))){

        $input1['image']['@type']        = 'ImageObject';
        $input1['image']['url']          = isset($howto_image['thumbnail']) ? esc_url($howto_image['thumbnail']):'';
        $input1['image']['height']       = isset($howto_image['width'])     ? esc_attr($howto_image['width'])   :'';
        $input1['image']['width']        = isset($howto_image['height'])    ? esc_attr($howto_image['height'])  :'';

        }  

        $input1['isAccessibleForFree']    = saswp_remove_warnings($all_post_meta, 'saswp_ta_schema_is_acceesible_free_'.$schema_id, 'saswp_array');

        $input1['address']['@type']             = 'PostalAddress';
        $input1['address']['addressCountry']    = saswp_remove_warnings($all_post_meta, 'saswp_ta_schema_country_'.$schema_id, 'saswp_array');
        $input1['address']['addressLocality']   = saswp_remove_warnings($all_post_meta, 'saswp_ta_schema_locality_'.$schema_id, 'saswp_array');
        $input1['address']['addressRegion']     = saswp_remove_warnings($all_post_meta, 'saswp_ta_schema_region_'.$schema_id, 'saswp_array');
        $input1['address']['PostalCode']        = saswp_remove_warnings($all_post_meta, 'saswp_ta_schema_postal_code_'.$schema_id, 'saswp_array');

        if(isset($all_post_meta['saswp_ta_schema_latitude_'.$schema_id][0]) && isset($all_post_meta['saswp_ta_schema_longitude_'.$schema_id][0])){

            $input1['geo']['@type']     = 'GeoCoordinates';
            $input1['geo']['latitude']  = $all_post_meta['saswp_ta_schema_latitude_'.$schema_id][0];
            $input1['geo']['longitude'] = $all_post_meta['saswp_ta_schema_longitude_'.$schema_id][0];
        }
    
    
    return $input1;
    
}

function saswp_tourist_destination_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
    
    $howto_image = get_post_meta( get_the_ID(), 'saswp_td_schema_image_'.$schema_id.'_detail',true); 

    $input1['@context']              = saswp_context_url();
    $input1['@type']                 = 'TouristDestination';
    $input1['@id']                   = trailingslashit(get_permalink()).'#TouristDestination';
    $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_td_schema_url_'.$schema_id, 'saswp_array');                            
    $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_td_schema_name_'.$schema_id, 'saswp_array');                            
    $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_td_schema_description_'.$schema_id, 'saswp_array');

    if(!(empty($howto_image))){

    $input1['image']['@type']        = 'ImageObject';
    $input1['image']['url']          = isset($howto_image['thumbnail']) ? esc_url($howto_image['thumbnail']):'';
    $input1['image']['height']       = isset($howto_image['width'])     ? esc_attr($howto_image['width'])   :'';
    $input1['image']['width']        = isset($howto_image['height'])    ? esc_attr($howto_image['height'])  :'';

    }  


    $input1['address']['@type']             = 'PostalAddress';
    $input1['address']['addressCountry']    = saswp_remove_warnings($all_post_meta, 'saswp_td_schema_country_'.$schema_id, 'saswp_array');
    $input1['address']['addressLocality']   = saswp_remove_warnings($all_post_meta, 'saswp_td_schema_locality_'.$schema_id, 'saswp_array');
    $input1['address']['addressRegion']     = saswp_remove_warnings($all_post_meta, 'saswp_td_schema_region_'.$schema_id, 'saswp_array');
    $input1['address']['PostalCode'] = saswp_remove_warnings($all_post_meta, 'saswp_td_schema_postal_code_'.$schema_id, 'saswp_array');                                                       
    
    if(isset($all_post_meta['saswp_td_schema_latitude_'.$schema_id][0]) && isset($all_post_meta['saswp_td_schema_longitude_'.$schema_id][0])){

        $input1['geo']['@type']     = 'GeoCoordinates';
        $input1['geo']['latitude']  = $all_post_meta['saswp_td_schema_latitude_'.$schema_id][0];
        $input1['geo']['longitude'] = $all_post_meta['saswp_td_schema_longitude_'.$schema_id][0];
    }
    
    return $input1;
}

function saswp_apartment_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
    
    $howto_image = get_post_meta( get_the_ID(), 'saswp_apartment_schema_image_'.$schema_id.'_detail',true); 

    $input1['@context']              = saswp_context_url();
    $input1['@type']                 = 'Apartment';
    $input1['@id']                   = trailingslashit(get_permalink()).'#Apartment';
    $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_apartment_schema_url_'.$schema_id, 'saswp_array');                            
    $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_apartment_schema_name_'.$schema_id, 'saswp_array');                            
    $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_apartment_schema_description_'.$schema_id, 'saswp_array');
    $input1['floorSize']             = saswp_remove_warnings($all_post_meta, 'saswp_apartment_schema_floor_size_'.$schema_id, 'saswp_array');

    if(!(empty($howto_image))){

    $input1['image']['@type']        = 'ImageObject';
    $input1['image']['url']          = isset($howto_image['thumbnail']) ? esc_url($howto_image['thumbnail']):'';
    $input1['image']['height']       = isset($howto_image['width'])     ? esc_attr($howto_image['width'])   :'';
    $input1['image']['width']        = isset($howto_image['height'])    ? esc_attr($howto_image['height'])  :'';

    }  

    $input1['numberOfRooms']           = saswp_remove_warnings($all_post_meta, 'saswp_apartment_schema_numberofrooms_'.$schema_id, 'saswp_array');

    $input1['address']['@type']             = 'PostalAddress';
    $input1['address']['addressCountry']    = saswp_remove_warnings($all_post_meta, 'saswp_apartment_schema_country_'.$schema_id, 'saswp_array');
    $input1['address']['addressLocality']   = saswp_remove_warnings($all_post_meta, 'saswp_apartment_schema_locality_'.$schema_id, 'saswp_array');
    $input1['address']['addressRegion']     = saswp_remove_warnings($all_post_meta, 'saswp_apartment_schema_region_'.$schema_id, 'saswp_array');
    $input1['address']['PostalCode']        = saswp_remove_warnings($all_post_meta, 'saswp_apartment_schema_postalcode_'.$schema_id, 'saswp_array');

    $input1['telephone']                    = saswp_remove_warnings($all_post_meta, 'saswp_apartment_schema_telephone_'.$schema_id, 'saswp_array');

    if(isset($all_post_meta['saswp_apartment_schema_latitude_'.$schema_id][0]) && isset($all_post_meta['saswp_apartment_schema_longitude_'.$schema_id][0])){

            $input1['geo']['@type']     = 'GeoCoordinates';
            $input1['geo']['latitude']  = $all_post_meta['saswp_apartment_schema_latitude_'.$schema_id][0];
            $input1['geo']['longitude'] = $all_post_meta['saswp_apartment_schema_longitude_'.$schema_id][0];

    }

    $itinerary  = get_post_meta($schema_post_id, 'apartment_amenities_'.$schema_id, true);

    $itinerary_arr = array();

    if(!empty($itinerary)){

        foreach($itinerary as $val){

            $supply_data = array();
            $supply_data['@type']        = 'LocationFeatureSpecification';
            $supply_data['name']         = $val['saswp_apartment_amenities_name'];                                                                        

           $itinerary_arr[] =  $supply_data;
        }

        $input1['amenityFeature'] = $itinerary_arr;
    }

    $add_property     = get_post_meta($schema_post_id, 'additional_property_'.$schema_id, true);

    $add_property_arr = array();

    if(!empty($add_property)){

        foreach($add_property as $val){

            $supply_data = array();
            $supply_data['@type']                                                  = 'PropertyValue';
            $supply_data['name']                                                   = $val['saswp_apartment_additional_property_name'];
            $supply_data[$val['saswp_apartment_additional_property_code_type']]    = $val['saswp_apartment_additional_property_code_value'];
            $supply_data['value']                                                  = $val['saswp_apartment_additional_property_value'];

           $add_property_arr[] =  $supply_data;
        }

        $input1['additionalProperty'] = $add_property_arr;
    }
    
    return $input1;
    
}

function saswp_house_schema_makrup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();

    $howto_image = get_post_meta( get_the_ID(), 'saswp_house_schema_image_'.$schema_id.'_detail',true); 

    $input1['@context']              = saswp_context_url();
    $input1['@type']                 = 'House';
    $input1['@id']                   = trailingslashit(get_permalink()).'#House';
    $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_house_schema_url_'.$schema_id, 'saswp_array');                            
    $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_house_schema_name_'.$schema_id, 'saswp_array');                            
    $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_house_schema_description_'.$schema_id, 'saswp_array');

    if(!(empty($howto_image))){

    $input1['image']['@type']        = 'ImageObject';
    $input1['image']['url']          = isset($howto_image['thumbnail']) ? esc_url($howto_image['thumbnail']):'';
    $input1['image']['height']       = isset($howto_image['width'])     ? esc_attr($howto_image['width'])   :'';
    $input1['image']['width']        = isset($howto_image['height'])    ? esc_attr($howto_image['height'])  :'';

    }  

    $input1['petsAllowed']                  = saswp_remove_warnings($all_post_meta, 'saswp_house_schema_pets_allowed_'.$schema_id, 'saswp_array');

    $input1['address']['@type']             = 'PostalAddress';
    $input1['address']['addressCountry']    = saswp_remove_warnings($all_post_meta, 'saswp_house_schema_country_'.$schema_id, 'saswp_array');
    $input1['address']['addressLocality']   = saswp_remove_warnings($all_post_meta, 'saswp_house_schema_locality_'.$schema_id, 'saswp_array');
    $input1['address']['addressRegion']     = saswp_remove_warnings($all_post_meta, 'saswp_house_schema_region_'.$schema_id, 'saswp_array');
    $input1['address']['PostalCode']        = saswp_remove_warnings($all_post_meta, 'saswp_house_schema_postalcode_'.$schema_id, 'saswp_array');

    $input1['telephone']                    = saswp_remove_warnings($all_post_meta, 'saswp_house_schema_telephone_'.$schema_id, 'saswp_array');

    $input1['hasMap']                       = saswp_remove_warnings($all_post_meta, 'saswp_house_schema_hasmap_'.$schema_id, 'saswp_array');
    $input1['floorSize']                    = saswp_remove_warnings($all_post_meta, 'saswp_house_schema_floor_size_'.$schema_id, 'saswp_array');
    $input1['numberOfRooms']                = saswp_remove_warnings($all_post_meta, 'saswp_house_schema_no_of_rooms_'.$schema_id, 'saswp_array');
    
    return $input1;
    
}

function saswp_single_family_residence_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
    
    $howto_image = get_post_meta( get_the_ID(), 'saswp_sfr_schema_image_'.$schema_id.'_detail',true);                            

    $input1['@context']              = saswp_context_url();
    $input1['@type']                 = 'SingleFamilyResidence';
    $input1['@id']                   = trailingslashit(get_permalink()).'#SingleFamilyResidence';
    $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_sfr_schema_url_'.$schema_id, 'saswp_array');                            
    $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_sfr_schema_name_'.$schema_id, 'saswp_array');                            
    $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_sfr_schema_description_'.$schema_id, 'saswp_array');

    if(!(empty($howto_image))){

    $input1['image']['@type']        = 'ImageObject';
    $input1['image']['url']          = isset($howto_image['thumbnail']) ? esc_url($howto_image['thumbnail']):'';
    $input1['image']['height']       = isset($howto_image['width'])     ? esc_attr($howto_image['width'])   :'';
    $input1['image']['width']        = isset($howto_image['height'])    ? esc_attr($howto_image['height'])  :'';

    }  
    $input1['numberOfRooms']                = saswp_remove_warnings($all_post_meta, 'saswp_sfr_schema_numberofrooms_'.$schema_id, 'saswp_array');
    $input1['petsAllowed']                  = saswp_remove_warnings($all_post_meta, 'saswp_sfr_schema_pets_allowed_'.$schema_id, 'saswp_array');

    $input1['address']['@type']             = 'PostalAddress';
    $input1['address']['addressCountry']    = saswp_remove_warnings($all_post_meta, 'saswp_sfr_schema_country_'.$schema_id, 'saswp_array');
    $input1['address']['addressLocality']   = saswp_remove_warnings($all_post_meta, 'saswp_sfr_schema_locality_'.$schema_id, 'saswp_array');
    $input1['address']['addressRegion']     = saswp_remove_warnings($all_post_meta, 'saswp_sfr_schema_region_'.$schema_id, 'saswp_array');
    $input1['address']['PostalCode']        = saswp_remove_warnings($all_post_meta, 'saswp_sfr_schema_postalcode_'.$schema_id, 'saswp_array');

    $input1['telephone']                    = saswp_remove_warnings($all_post_meta, 'saswp_sfr_schema_telephone_'.$schema_id, 'saswp_array');
    $input1['hasMap']                       = saswp_remove_warnings($all_post_meta, 'saswp_sfr_schema_hasmap_'.$schema_id, 'saswp_array');
    $input1['floorSize']                    = saswp_remove_warnings($all_post_meta, 'saswp_sfr_schema_floor_size_'.$schema_id, 'saswp_array');
    $input1['numberOfRooms']                = saswp_remove_warnings($all_post_meta, 'saswp_sfr_schema_no_of_rooms_'.$schema_id, 'saswp_array');
    
    return $input1;
    
}

function saswp_tv_series_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
    
    $howto_image = get_post_meta( get_the_ID(), 'saswp_tvseries_schema_image_'.$schema_id.'_detail',true); 

    $actor     = get_post_meta($schema_post_id, 'tvseries_actor_'.$schema_id, true);              
    $season    = get_post_meta($schema_post_id, 'tvseries_season_'.$schema_id, true);                                          

    $input1['@context']              = saswp_context_url();
    $input1['@type']                 = 'TVSeries';
    $input1['@id']                   = trailingslashit(get_permalink()).'#TVSeries';                                            
    $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_tvseries_schema_name_'.$schema_id, 'saswp_array');                            			    
    $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_tvseries_schema_description_'.$schema_id, 'saswp_array');

    if(!(empty($howto_image))){

    $input1['image']['@type']        = 'ImageObject';
    $input1['image']['url']          = isset($howto_image['thumbnail']) ? esc_url($howto_image['thumbnail']):'';
    $input1['image']['height']       = isset($howto_image['width'])     ? esc_attr($howto_image['width'])   :'';
    $input1['image']['width']        = isset($howto_image['height'])    ? esc_attr($howto_image['height'])  :'';

    }                            

    $input1['author']['@type']       = 'Person';
    $input1['author']['name']        = saswp_remove_warnings($all_post_meta, 'saswp_tvseries_schema_author_name_'.$schema_id, 'saswp_array');                            

    $supply_arr = array();
    if(!empty($actor)){

        foreach($actor as $val){

            $supply_data = array();
            $supply_data['@type'] = 'Person';
            $supply_data['name']  = $val['saswp_tvseries_actor_name'];

            $supply_arr[] =  $supply_data;
        }
       $input1['actor'] = $supply_arr;
    }

    $tool_arr = array();
    if(!empty($season)){

        foreach($season as $val){

            $supply_data = array();
            $supply_data['@type']            = 'TVSeason';
            $supply_data['datePublished']    = $val['saswp_tvseries_season_published_date'];
            $supply_data['name']             = $val['saswp_tvseries_season_name'];
            $supply_data['numberOfEpisodes'] = $val['saswp_tvseries_season_episodes'];

            $tool_arr[] =  $supply_data;
        }
       $input1['containsSeason'] = $tool_arr;
    }
    
    return $input1;
    
}

function saswp_medical_condition_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
    
    $howto_image = get_post_meta( get_the_ID(), 'saswp_mc_schema_image_'.$schema_id.'_detail',true);  

    $cause       = get_post_meta($schema_post_id, 'mc_cause_'.$schema_id, true);              
    $symptom     = get_post_meta($schema_post_id, 'mc_symptom_'.$schema_id, true);              
    $riskfactro  = get_post_meta($schema_post_id, 'mc_risk_factor_'.$schema_id, true);              

    $input1['@context']                     = saswp_context_url();
    $input1['@type']                        = 'MedicalCondition';
    $input1['@id']                          = trailingslashit(get_permalink()).'#MedicalCondition';
    $input1['name']                         = saswp_remove_warnings($all_post_meta, 'saswp_mc_schema_name_'.$schema_id, 'saswp_array');
    $input1['alternateName']                = saswp_remove_warnings($all_post_meta, 'saswp_mc_schema_alternate_name_'.$schema_id, 'saswp_array');                            
    $input1['description']                  = saswp_remove_warnings($all_post_meta, 'saswp_mc_schema_description_'.$schema_id, 'saswp_array');

    if(!(empty($howto_image))){

    $input1['image']['@type']        = 'ImageObject';
    $input1['image']['url']          = isset($howto_image['thumbnail']) ? esc_url($howto_image['thumbnail']):'';
    $input1['image']['height']       = isset($howto_image['width'])     ? esc_attr($howto_image['width'])   :'';
    $input1['image']['width']        = isset($howto_image['height'])    ? esc_attr($howto_image['height'])  :'';

    }

    if( isset($all_post_meta['saswp_mc_schema_drug_'.$schema_id][0]) ){
        $input1['drug']              = $all_post_meta['saswp_mc_schema_drug_'.$schema_id][0];
    }

    if( isset($all_post_meta['saswp_mc_schema_primary_prevention_name_'.$schema_id][0]) || isset($all_post_meta['saswp_mc_schema_primary_prevention_performed_'.$schema_id][0]) ){
        $input1['primaryPrevention']['@type']                    = 'MedicalTherapy';
        $input1['primaryPrevention']['name']         = $all_post_meta['saswp_mc_schema_primary_prevention_name_'.$schema_id][0];
        $input1['primaryPrevention']['howPerformed'] = $all_post_meta['saswp_mc_schema_primary_prevention_performed_'.$schema_id][0];
    }

    if( isset($all_post_meta['saswp_mc_schema_possible_treatment_name_'.$schema_id][0]) || isset($all_post_meta['saswp_mc_schema_possible_treatment_performed_'.$schema_id][0]) ){
        $input1['possibleTreatment']['@type']                    = 'MedicalTherapy';
        $input1['possibleTreatment']['name']         = $all_post_meta['saswp_mc_schema_possible_treatment_name_'.$schema_id][0];
        $input1['possibleTreatment']['howPerformed'] = $all_post_meta['saswp_mc_schema_possible_treatment_performed_'.$schema_id][0];
    }
    
    $input1['associatedAnatomy']['@type']   = 'AnatomicalStructure';
    $input1['associatedAnatomy']['name']    = saswp_remove_warnings($all_post_meta, 'saswp_mc_schema_anatomy_name_'.$schema_id, 'saswp_array');                            

    $input1['code']['@type']                = 'MedicalCode';
    $input1['code']['code']                 = saswp_remove_warnings($all_post_meta, 'saswp_mc_schema_medical_code_'.$schema_id, 'saswp_array');                            
    $input1['code']['codingSystem']         = saswp_remove_warnings($all_post_meta, 'saswp_mc_schema_coding_system_'.$schema_id, 'saswp_array');                            

    $cause_arr = array();
    if(!empty($cause)){

        foreach($cause as $val){

            $supply_data = array();
            $supply_data['@type'] = 'MedicalCause';
            $supply_data['name'] = $val['saswp_mc_cause_name'];

           $cause_arr[] =  $supply_data;
        }
       $input1['cause'] = $cause_arr;
    }

    $symptom_arr = array();
    if(!empty($symptom)){

        foreach($symptom as $val){

            $supply_data = array();
            $supply_data['@type'] = 'MedicalSymptom';
            $supply_data['name'] = $val['saswp_mc_symptom_name'];

           $symptom_arr[] =  $supply_data;
        }
       $input1['signOrSymptom'] = $symptom_arr;
    }

    $riskfactor_arr = array();
    if(!empty($riskfactro)){
                                
                                foreach($riskfactro as $val){
                                   
                                    $supply_data = array();
                                    $supply_data['@type'] = 'MedicalRiskFactor';
                                    $supply_data['name'] = $val['saswp_mc_risk_factor_name'];
                                    
                                   $riskfactor_arr[] =  $supply_data;
                                }
                               $input1['riskFactor'] = $riskfactor_arr;
                            }
    
    return $input1;
    
}

function saswp_qanda_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
    
    if(trim(saswp_remove_warnings($all_post_meta, 'saswp_qa_question_title_'.$schema_id, 'saswp_array')) ==''){

        $service_object = new saswp_output_service();
        $input1  = $service_object->saswp_dw_question_answers_details(get_the_ID());  

    }else{
        
        $input1['@context'] = saswp_context_url();
        $input1['@type']    = 'QAPage';
        $input1['@id']      = trailingslashit(get_permalink()).'#qapage';

        $input1['mainEntity']['@type']         = 'Question';
        $input1['mainEntity']['name']          = saswp_remove_warnings($all_post_meta, 'saswp_qa_question_title_'.$schema_id, 'saswp_array');
        $input1['mainEntity']['text']          = saswp_remove_warnings($all_post_meta, 'saswp_qa_question_description_'.$schema_id, 'saswp_array');
        $input1['mainEntity']['upvoteCount']   = saswp_remove_warnings($all_post_meta, 'saswp_qa_upvote_count_'.$schema_id, 'saswp_array');
        $input1['mainEntity']['dateCreated']   = isset($all_post_meta['saswp_qa_date_created_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_qa_date_created_'.$schema_id][0])):'';
        $input1['mainEntity']['author']        = array('@type' => 'Person','name' =>saswp_remove_warnings($all_post_meta, 'saswp_qa_question_author_name_'.$schema_id, 'saswp_array'));
        $input1['mainEntity']['answerCount']   = saswp_remove_warnings($all_post_meta, 'saswp_qa_answer_count_'.$schema_id, 'saswp_array');
        
        $answer    = get_post_meta($schema_post_id, 'accepted_answer_'.$schema_id, true);

        $answer_arr = array();
        
        if(!empty($answer)){

            foreach($answer as $val){

                $supply_data = array();

                if($val['saswp_qa_accepted_answer_text']){
                    $supply_data['@type']       = 'Answer';
                    $supply_data['upvoteCount'] = $val['saswp_qa_accepted_answer_upvote_count'];
                    $supply_data['url']         = $val['saswp_qa_accepted_answer_url'];
                    $supply_data['text']        = $val['saswp_qa_accepted_answer_text'];
                    $supply_data['dateCreated'] = saswp_format_date_time($val['saswp_qa_accepted_answer_date_created']);
                    $supply_data['author']      = $val['saswp_qa_accepted_author_name'];                    
                }

               $answer_arr[] =  $supply_data;
            }
           $input1['mainEntity']['acceptedAnswer'] = $answer_arr;
        }

        $answer    = get_post_meta($schema_post_id, 'suggested_answer_'.$schema_id, true);

        $answer_arr = array();
        
        if(!empty($answer)){

            foreach($answer as $val){

                $supply_data = array();

                if($val['saswp_qa_suggested_answer_text']){
                    $supply_data['@type']       = 'Answer';
                    $supply_data['upvoteCount'] = $val['saswp_qa_suggested_answer_upvote_count'];
                    $supply_data['url']         = $val['saswp_qa_suggested_answer_url'];
                    $supply_data['text']        = $val['saswp_qa_suggested_answer_text'];
                    $supply_data['dateCreated'] = saswp_format_date_time($val['saswp_qa_suggested_answer_date_created']);
                    $supply_data['author']      = $val['saswp_qa_suggested_author_name'];                    
                }

               $answer_arr[] =  $supply_data;
            }
           $input1['mainEntity']['suggestedAnswer'] = $answer_arr;
        }

    }   
    
    return $input1;
    
}

function saswp_data_feed_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
    
            $input1 = array(
                '@context'			=> saswp_context_url(),
                '@type'		        => 'DataFeed' ,
                '@id'                       => trailingslashit(get_permalink()).'#DataFeed',    
                'name'			=> saswp_remove_warnings($all_post_meta, 'saswp_data_feed_schema_name_'.$schema_id, 'saswp_array'),
                'description'               => saswp_remove_warnings($all_post_meta, 'saswp_data_feed_schema_description_'.$schema_id, 'saswp_array'),									                                             
                'dateModified'              => isset($all_post_meta['saswp_data_feed_schema_date_modified_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_data_feed_schema_date_modified_'.$schema_id][0])):'',                               
                'license'                   => saswp_remove_warnings($all_post_meta, 'saswp_data_feed_schema_license_'.$schema_id, 'saswp_array'),									                                             
            );


            $performer  = get_post_meta($schema_post_id, 'feed_element_'.$schema_id, true);

            $performer_arr = array();

            if(!empty($performer)){

                foreach($performer as $val){

                    $supply_data = array();
                    $supply_data['@type']        = 'DataFeedItem';
                    $supply_data['dateCreated']  = isset($val['saswp_feed_element_date_created'])?date('Y-m-d\TH:i:s\Z',strtotime($val['saswp_feed_element_date_created'])):'';  
                    $supply_data['item']  = array(
                       '@type'    => 'Person',
                       'name'     => $val['saswp_feed_element_name'],
                       'email'    => $val['saswp_feed_element_email'],
                    );

                    $performer_arr[] =  $supply_data;
                }

               $input1['dataFeedElement'] = $performer_arr;

            }      
    
    return $input1;
    
}

function saswp_dfp_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
    
    $event_image = get_post_meta( get_the_ID(), 'saswp_dfp_image_'.$schema_id.'_detail',true);  
    $slogo = get_post_meta( get_the_ID(), 'saswp_dfp_organization_logo_'.$schema_id.'_detail',true); 
    $input1 = array(
        '@context'			=> saswp_context_url(),
        '@type'				=> 'DiscussionForumPosting' ,
        '@id'				=> saswp_remove_warnings($all_post_meta, 'saswp_dfp_url_'.$schema_id, 'saswp_array').'#blogposting',    			
        'mainEntityOfPage'		=> saswp_remove_warnings($all_post_meta, 'saswp_dfp_main_entity_of_page_'.$schema_id, 'saswp_array'),    			
        'headline'			=> saswp_remove_warnings($all_post_meta, 'saswp_dfp_headline_'.$schema_id, 'saswp_array'),
        'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_dfp_description_'.$schema_id, 'saswp_array'),			
        'url'				=> saswp_remove_warnings($all_post_meta, 'saswp_dfp_url_'.$schema_id, 'saswp_array'),
        'image'                         => array(
                                            '@type'		=>'ImageObject',
                                            'url'		=>  isset($event_image['thumbnail']) ? esc_url($event_image['thumbnail']):'' ,
                                            'width'		=>  isset($event_image['width'])     ? esc_attr($event_image['width'])   :'' ,
                                            'height'            =>  isset($event_image['height'])    ? esc_attr($event_image['height'])  :'' ,
                                        ), 
        'datePublished'                 => isset($all_post_meta['saswp_dfp_date_published_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_dfp_date_published_'.$schema_id][0])):'',
        'dateModified'                  => isset($all_post_meta['saswp_dfp_date_modified_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_dfp_date_modified_'.$schema_id][0])):'',
        'author'			=> array(
                                            '@type' 	        => 'Person',
                                            'name'		=> saswp_remove_warnings($all_post_meta, 'saswp_dfp_author_name_'.$schema_id, 'saswp_array'),
                                            'description'	=> saswp_remove_warnings($all_post_meta, 'saswp_dfp_author_description_'.$schema_id, 'saswp_array'),
                                            'url'	        => saswp_remove_warnings($all_post_meta, 'saswp_dfp_author_url_'.$schema_id, 'saswp_array') 
                                            
                                        ),
        'publisher'			=> array(
                        '@type'			=> 'Organization',
                        'logo' 			=> array(
                                '@type'		=> 'ImageObject',
                                'url'		=> saswp_remove_warnings($all_post_meta, 'saswp_dfp_organization_logo_'.$schema_id, 'saswp_array'),
                                'width'		=> saswp_remove_warnings($slogo, 'width', 'saswp_string'),
                                'height'	=> saswp_remove_warnings($slogo, 'height', 'saswp_string'),
                                ),
                        'name'			=> saswp_remove_warnings($all_post_meta, 'saswp_dfp_organization_name_'.$schema_id, 'saswp_array'),
                ),
            );
    
    return $input1;
    
}

function saswp_blogposting_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
    
    $slogo = get_post_meta( get_the_ID(), 'saswp_blogposting_organization_logo_'.$schema_id.'_detail',true);                                 
    $input1 = array(
    '@context'			=> saswp_context_url(),
    '@type'				=> 'BlogPosting' ,
    '@id'                           => trailingslashit(get_permalink()).'#BlogPosting',  
    'inLanguage'                    => get_bloginfo('language'),
    'mainEntityOfPage'              => saswp_remove_warnings($all_post_meta, 'saswp_blogposting_main_entity_of_page_'.$schema_id, 'saswp_array'),
    'headline'			=> saswp_remove_warnings($all_post_meta, 'saswp_blogposting_headline_'.$schema_id, 'saswp_array'),
    'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_blogposting_description_'.$schema_id, 'saswp_array'),
    'keywords'                      => saswp_remove_warnings($all_post_meta, 'saswp_blogposting_keywords_'.$schema_id, 'saswp_array'),
    'name'				=> saswp_remove_warnings($all_post_meta, 'saswp_blogposting_name_'.$schema_id, 'saswp_array'),
    'url'				=> saswp_remove_warnings($all_post_meta, 'saswp_blogposting_url_'.$schema_id, 'saswp_array'),
    'datePublished'                 => isset($all_post_meta['saswp_blogposting_date_published_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_blogposting_date_published_'.$schema_id][0], get_post_time('h:i:s')) :'',
    'dateModified'                  => isset($all_post_meta['saswp_blogposting_date_modified_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_blogposting_date_modified_'.$schema_id][0], get_the_modified_time('h:i:s')) :'',
    'author'			=> array(
                    '@type' 	        => 'Person',
                    'name'		=> saswp_remove_warnings($all_post_meta, 'saswp_blogposting_author_name_'.$schema_id, 'saswp_array'),
                    'description'	=> saswp_remove_warnings($all_post_meta, 'saswp_blogposting_author_description_'.$schema_id, 'saswp_array'),
                    'url'       	=> saswp_remove_warnings($all_post_meta, 'saswp_blogposting_author_url_'.$schema_id, 'saswp_array')
                                        ),
    'publisher'			=> array(
            '@type'			=> 'Organization',
            'logo' 			=> array(
                    '@type'		=> 'ImageObject',
                    'url'		=> saswp_remove_warnings($all_post_meta, 'saswp_blogposting_organization_logo_'.$schema_id, 'saswp_array'),
                    'width'		=> saswp_remove_warnings($slogo, 'width', 'saswp_string'),
                    'height'	        => saswp_remove_warnings($slogo, 'height', 'saswp_string'),
                    ),
            'name'			=> saswp_remove_warnings($all_post_meta, 'saswp_blogposting_organization_name_'.$schema_id, 'saswp_array'),
            ),
    );

        $itemlist  = get_post_meta($schema_post_id, 'blogposting_items_'.$schema_id, true);

        if($itemlist){

            $list_arr = array();

            foreach ($itemlist as $list){
                $list_arr[] = $list['saswp_blogposting_items_name'];
            }

            $input1['mainEntity']['@type']            = 'ItemList';
            $input1['mainEntity']['itemListElement']  = $list_arr;                 
            $input1['mainEntity']['itemListOrder']    = 'http://schema.org/ItemListOrderAscending ';
            $input1['mainEntity']['name']             = saswp_get_the_title();

        }

        if(isset($all_post_meta['saswp_blogposting_speakable_'.$schema_id]) && $all_post_meta['saswp_blogposting_speakable_'.$schema_id][0] == 1 ){

            $input1['speakable']['@type'] = 'SpeakableSpecification';
            $input1['speakable']['xpath'] = array(
                 "/html/head/title",
                 "/html/head/meta[@name='description']/@content"
            );

        }            
    
    return $input1;
    
}

function saswp_creative_work_series_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
    
    $slogo = get_post_meta( get_the_ID(), 'saswp_cws_schema_organization_logo_'.$schema_id.'_detail',true);                                 

    $input1 = array(
    '@context'			            => saswp_context_url(),
    '@type'				            => 'CreativeWorkSeries' ,
    '@id'                           => trailingslashit(get_permalink()).'#CreativeWorkSeries',  
    'inLanguage'                    => get_bloginfo('language'),        
    'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_cws_schema_description_'.$schema_id, 'saswp_array'),
    'keywords'                      => saswp_remove_warnings($all_post_meta, 'saswp_cws_schema_keywords_'.$schema_id, 'saswp_array'),
    'name'				            => saswp_remove_warnings($all_post_meta, 'saswp_cws_schema_name_'.$schema_id, 'saswp_array'),
    'url'				            => saswp_remove_warnings($all_post_meta, 'saswp_cws_schema_url_'.$schema_id, 'saswp_array'),
    'datePublished'                 => isset($all_post_meta['saswp_cws_schema_date_published_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_cws_schema_date_published_'.$schema_id][0], get_post_time('h:i:s')) :'',
    'dateModified'                  => isset($all_post_meta['saswp_cws_schema_date_modified_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_cws_schema_date_modified_'.$schema_id][0], get_the_modified_time('h:i:s')) :'',
    'startDate'                 => isset($all_post_meta['saswp_cws_schema_start_date_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_cws_schema_start_date_'.$schema_id][0]) :'',
    'endDate'                   => isset($all_post_meta['saswp_cws_schema_end_date_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_cws_schema_end_date_'.$schema_id][0]) :'',
    'author'			=> array(
                            '@type' 	        => 'Person',
                            'name'		        => saswp_remove_warnings($all_post_meta, 'saswp_cws_schema_author_name_'.$schema_id, 'saswp_array'),
                            'description'	    => saswp_remove_warnings($all_post_meta, 'saswp_cws_schema_author_description_'.$schema_id, 'saswp_array'),
                            'url'       	    => saswp_remove_warnings($all_post_meta, 'saswp_cws_schema_author_url_'.$schema_id, 'saswp_array')
                            ),
    'publisher'			=> array(
                            '@type'			=> 'Organization',
                            'logo' 			=> array(
                            '@type'		    => 'ImageObject',
                            'url'		    => saswp_remove_warnings($all_post_meta, 'saswp_cws_schema_organization_logo_'.$schema_id, 'saswp_array'),
                            'width'		    => saswp_remove_warnings($slogo, 'width', 'saswp_string'),
                            'height'	    => saswp_remove_warnings($slogo, 'height', 'saswp_string'),
                            ),
    'name'			=> saswp_remove_warnings($all_post_meta, 'saswp_cws_schema_organization_name_'.$schema_id, 'saswp_array'),
    ),
    );
                        
    return $input1;
    
}

function saswp_audio_object_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
    
    $input1 = array(
    '@context'			=> saswp_context_url(),
    '@type'		       => 'AudioObject',
    '@id'                           => trailingslashit(get_permalink()).'#audioobject',    
    'name'			        => saswp_remove_warnings($all_post_meta, 'saswp_audio_schema_name_'.$schema_id, 'saswp_array'),
    'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_audio_schema_description_'.$schema_id, 'saswp_array'),
    'contentUrl'		        => saswp_remove_warnings($all_post_meta, 'saswp_audio_schema_contenturl_'.$schema_id, 'saswp_array'),
    'duration'		        => saswp_remove_warnings($all_post_meta, 'saswp_audio_schema_duration_'.$schema_id, 'saswp_array'),
    'encodingFormat'		=> saswp_remove_warnings($all_post_meta, 'saswp_audio_schema_encoding_format_'.$schema_id, 'saswp_array'),
    'datePublished'                 => isset($all_post_meta['saswp_audio_schema_date_published_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_audio_schema_date_published_'.$schema_id][0], get_post_time('h:i:s')) :'',
    'dateModified'                  => isset($all_post_meta['saswp_audio_schema_date_modified_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_audio_schema_date_modified_'.$schema_id][0], get_the_modified_time('h:i:s')) :'',
    'author'			=> array(
                    '@type' 	   => 'Person',
                    'name'		=> saswp_remove_warnings($all_post_meta, 'saswp_audio_schema_author_name_'.$schema_id, 'saswp_array'),
                    'description'	=> saswp_remove_warnings($all_post_meta, 'saswp_audio_schema_author_description_'.$schema_id, 'saswp_array'),
                    'url'    	=> saswp_remove_warnings($all_post_meta, 'saswp_audio_schema_author_url_'.$schema_id, 'saswp_array')
                ),

       );
    
    return $input1;
        
}

function saswp_webpage_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
        $input1 = array();
    
        $slogo = get_post_meta( get_the_ID(), 'saswp_webpage_organization_logo_'.$schema_id.'_detail',true);
        $input1 = array(
        '@context'			=> saswp_context_url(),
        '@type'				=> 'WebPage' ,
        '@id'                           => trailingslashit(get_permalink()).'#webpage',     
        'inLanguage'                    => get_bloginfo('language'),    
        'name'				=> saswp_remove_warnings($all_post_meta, 'saswp_webpage_name_'.$schema_id, 'saswp_array'),
        'url'				=> saswp_remove_warnings($all_post_meta, 'saswp_webpage_url_'.$schema_id, 'saswp_array'),
        'lastReviewed' 	    => isset($all_post_meta['saswp_webpage_last_reviewed_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_webpage_last_reviewed_'.$schema_id][0], get_post_time('h:i:s')) :'',
        'reviewedBy'	    => array(
            '@type'			=> 'Organization',
            'logo' 			=> array(
                    '@type'		=> 'ImageObject',
                    'url'		=> saswp_remove_warnings($all_post_meta, 'saswp_webpage_organization_logo_'.$schema_id, 'saswp_array'),
                    'width'		=> saswp_remove_warnings($slogo, 'width', 'saswp_string'),
                    'height'	=> saswp_remove_warnings($slogo, 'height', 'saswp_string'),
                    ),
            'name'			=> saswp_remove_warnings($all_post_meta, 'saswp_webpage_organization_name_'.$schema_id, 'saswp_array'),
         ),        
        'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_webpage_description_'.$schema_id, 'saswp_array'),
        'mainEntity'                    => array(
                        '@type'			=> 'Article',
                        'mainEntityOfPage'	=> wp_strip_all_tags(strip_shortcodes(saswp_remove_warnings($all_post_meta, 'saswp_webpage_main_entity_of_page_'.$schema_id, 'saswp_array'))),
                        'image'			=> saswp_remove_warnings($all_post_meta, 'saswp_webpage_image_'.$schema_id, 'saswp_array'),
                        'headline'		=> saswp_remove_warnings($all_post_meta, 'saswp_webpage_headline_'.$schema_id, 'saswp_array'),
                        'description'		=> saswp_remove_warnings($all_post_meta, 'saswp_webpage_description_'.$schema_id, 'saswp_array'),
                        'keywords'		=> saswp_remove_warnings($all_post_meta, 'saswp_webpage_keywords_'.$schema_id, 'saswp_array'),
                        'datePublished' 	=> isset($all_post_meta['saswp_webpage_date_published_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_webpage_date_published_'.$schema_id][0], get_post_time('h:i:s')) :'',
                        'dateModified'		=> isset($all_post_meta['saswp_webpage_date_modified_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_webpage_date_modified_'.$schema_id][0], get_the_modified_time('h:i:s')) :'',
                        'author'			=> array(
                                        '@type' 	=> 'Person',
                                        'name'		=> saswp_remove_warnings($all_post_meta, 'saswp_webpage_author_name_'.$schema_id, 'saswp_array'), 
                                        'description'	=> saswp_remove_warnings($all_post_meta, 'saswp_webpage_author_description_'.$schema_id, 'saswp_array'), 
                                        'url'	        => saswp_remove_warnings($all_post_meta, 'saswp_webpage_author_url_'.$schema_id, 'saswp_array'), 
                            ),
                        'publisher'			=> array(
                                '@type'			=> 'Organization',
                                'logo' 			=> array(
                                        '@type'		=> 'ImageObject',
                                        'url'		=> saswp_remove_warnings($all_post_meta, 'saswp_webpage_organization_logo_'.$schema_id, 'saswp_array'),
                                        'width'		=> saswp_remove_warnings($slogo, 'width', 'saswp_string'),
                                        'height'	=> saswp_remove_warnings($slogo, 'height', 'saswp_string'),
                                        ),
                                'name'			=> saswp_remove_warnings($all_post_meta, 'saswp_webpage_organization_name_'.$schema_id, 'saswp_array'),
                        ),
                ),


        );


        if(isset($all_post_meta['saswp_webpage_speakable_'.$schema_id]) && $all_post_meta['saswp_webpage_speakable_'.$schema_id][0] == 1){

            $input1['speakable']['@type'] = 'SpeakableSpecification';
            $input1['speakable']['xpath'] = array(
                 "/html/head/title",
                 "/html/head/meta[@name='description']/@content"
            );

        }
    
    return $input1;
    
}
function saswp_special_announcement_schema_markup($schema_id, $schema_post_id, $all_post_meta){
        
    $input1 = array();

    $slogo = get_post_meta( get_the_ID(), 'saswp_special_announcement_organization_logo_'.$schema_id.'_detail',true);
    $location_img = get_post_meta( get_the_ID(), 'saswp_special_announcement_location_image_'.$schema_id.'_detail',true);    
    
    $input1 = array(
            '@context'			=> saswp_context_url(),
            '@type'				=> 'SpecialAnnouncement',
            '@id'                           => trailingslashit(get_permalink()).'#SpecialAnnouncement',
            'url'				=> saswp_remove_warnings($all_post_meta, 'saswp_special_announcement_url_'.$schema_id, 'saswp_array'),
            'inLanguage'                    => get_bloginfo('language'),            
            'image'				=> saswp_remove_warnings($all_post_meta, 'saswp_special_announcement_image_'.$schema_id, 'saswp_array'),
            'name'			=> saswp_remove_warnings($all_post_meta, 'saswp_special_announcement_name_'.$schema_id, 'saswp_array'),
            'category'			           => saswp_remove_warnings($all_post_meta, 'saswp_special_announcement_category_'.$schema_id, 'saswp_array'),
            'quarantineGuidelines'			=> saswp_remove_warnings($all_post_meta, 'saswp_special_announcement_quarantine_guidelines_'.$schema_id, 'saswp_array'),
            'newsUpdatesAndGuidelines'			=> saswp_remove_warnings($all_post_meta, 'saswp_special_announcement_newsupdates_and_guidelines_'.$schema_id, 'saswp_array'),
            'diseasePreventionInfo'			=> saswp_remove_warnings($all_post_meta, 'saswp_special_announcement_disease_prevention_info_'.$schema_id, 'saswp_array'),
            'text'                   => saswp_remove_warnings($all_post_meta, 'saswp_special_announcement_description_'.$schema_id, 'saswp_array'),                        
            'keywords'		        => saswp_remove_warnings($all_post_meta, 'saswp_special_announcement_keywords_'.$schema_id, 'saswp_array'),                
            'datePublished'                 => isset($all_post_meta['saswp_special_announcement_date_published_'.$schema_id][0])? saswp_format_date_time($all_post_meta['saswp_special_announcement_date_published_'.$schema_id][0], get_post_time('h:i:s')):'',
            'dateModified'                  => isset($all_post_meta['saswp_special_announcement_date_modified_'.$schema_id][0])? saswp_format_date_time($all_post_meta['saswp_special_announcement_date_modified_'.$schema_id][0], get_the_modified_time('h:i:s')):'',
            'datePosted'                 => isset($all_post_meta['saswp_special_announcement_date_published_'.$schema_id][0])? saswp_format_date_time($all_post_meta['saswp_special_announcement_date_posted_'.$schema_id][0], get_post_time('h:i:s')):'',
            'expires'                   => isset($all_post_meta['saswp_special_announcement_date_modified_'.$schema_id][0])? saswp_format_date_time($all_post_meta['saswp_special_announcement_date_expires_'.$schema_id][0], get_the_modified_time('h:i:s')):'',
            'author'			=> array(
                            '@type' 	=> 'Person',
                            'name'		=> saswp_remove_warnings($all_post_meta, 'saswp_special_announcement_author_name_'.$schema_id, 'saswp_array'),
                            'description'   => saswp_remove_warnings($all_post_meta, 'saswp_special_announcement_author_description_'.$schema_id, 'saswp_array'),
                            'url'           => saswp_remove_warnings($all_post_meta, 'saswp_special_announcement_author_url_'.$schema_id, 'saswp_array') 
                             )                

    );

    if(isset($all_post_meta['saswp_special_announcement_organization_logo_'.$schema_id][0]) || isset($all_post_meta['saswp_special_announcement_organization_name_'.$schema_id][0])){
    
        $input1['publisher']['@type']          = 'Organization';
        $input1['publisher']['logo']['@type']  = 'ImageObject';
        $input1['publisher']['logo']['url']    = saswp_remove_warnings($all_post_meta, 'saswp_special_announcement_organization_logo_'.$schema_id, 'saswp_array');
        $input1['publisher']['logo']['width']  = saswp_remove_warnings($slogo, 'width', 'saswp_string');
        $input1['publisher']['logo']['height'] = saswp_remove_warnings($slogo, 'height', 'saswp_string');
        $input1['publisher']['name']           = saswp_remove_warnings($all_post_meta, 'saswp_special_announcement_organization_name_'.$schema_id, 'saswp_array');
        
    }

        $location = array();
                            
        if(isset($all_post_meta['saswp_special_announcement_location_type_'.$schema_id][0])){

            $loc_imgobject = array();

            if($location_img){

                $loc_imgobject =   array(
                    '@type'		=> 'ImageObject',
                    'url'		=> $location_img['thumbnail'],
                    'width'		=> $location_img['width'],
                    'height'	=> $location_img['height'],
                );

            }

            $location[] = array(
                '@type' => saswp_remove_warnings($all_post_meta, 'saswp_special_announcement_location_type_'.$schema_id, 'saswp_array'),
                'name' => saswp_remove_warnings($all_post_meta, 'saswp_special_announcement_location_name_'.$schema_id, 'saswp_array'),
                'image' => $loc_imgobject,
                'url' => saswp_remove_warnings($all_post_meta, 'saswp_special_announcement_location_url_'.$schema_id, 'saswp_array'),
                'telephone' => saswp_remove_warnings($all_post_meta, 'saswp_special_announcement_location_telephone_'.$schema_id, 'saswp_array'),
                'priceRange' => saswp_remove_warnings($all_post_meta, 'saswp_special_announcement_location_price_range_'.$schema_id, 'saswp_array'),
                'address' => array(
                            '@type' => 'PostalAddress',
                            'streetAddress' => saswp_remove_warnings($all_post_meta, 'saswp_special_announcement_location_street_address_'.$schema_id, 'saswp_array'),
                            'addressLocality' => saswp_remove_warnings($all_post_meta, 'saswp_special_announcement_location_address_locality_'.$schema_id, 'saswp_array'),
                            'addressRegion' => saswp_remove_warnings($all_post_meta, 'saswp_special_announcement_location_address_region_'.$schema_id, 'saswp_array'),  
                ),
            );

        }  
        
        $supply  = get_post_meta($schema_post_id, 'announcement_location_'.$schema_id, true);         

        if(!empty($supply)){

            foreach($supply as $val){

                $supply_data = array();
                
                $supply_data['@type']                        = $val['saswp_sp_location_type'];
                $supply_data['name']                         = $val['saswp_sp_location_name'];
                $supply_data['url']                          = $val['saswp_sp_location_url'];
                $supply_data['telephone']                    = $val['saswp_sp_location_telephone'];
                $supply_data['priceRange']                   = $val['saswp_sp_location_price_range'];
                $supply_data['address']['@type']             = 'PostalAddress';
                $supply_data['address']['streetAddress']     = $val['saswp_sp_location_street_address'];
                $supply_data['address']['addressLocality']   = $val['saswp_sp_location_street_locality'];
                $supply_data['address']['addressRegion']     = $val['saswp_sp_location_street_region'];                
                

                if(isset($val['saswp_sp_location_image_id']) && $val['saswp_sp_location_image_id'] !=''){

                            $image_details   = saswp_get_image_by_id($val['saswp_sp_location_image_id']); 

                            if($image_details){
                                $supply_data['image'] = $image_details;
                            }
                                                        
                }
               $location[] =  $supply_data;
            }

        }

        $input1['announcementLocation'] = $location;
        
    return $input1;

}

function saswp_article_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
        $input1 = array();
    
        $slogo = get_post_meta( get_the_ID(), 'saswp_article_organization_logo_'.$schema_id.'_detail',true);

        $input1 = array(
                '@context'			=> saswp_context_url(),
                '@type'				=> 'Article',
                '@id'                           => trailingslashit(get_permalink()).'#article',
                'url'				=> saswp_remove_warnings($all_post_meta, 'saswp_article_url_'.$schema_id, 'saswp_array'),
                'inLanguage'                    => get_bloginfo('language'),
                'mainEntityOfPage'              => saswp_remove_warnings($all_post_meta, 'saswp_article_main_entity_of_page_'.$schema_id, 'saswp_array'),
                'image'				=> saswp_remove_warnings($all_post_meta, 'saswp_article_image_'.$schema_id, 'saswp_array'),
                'headline'			=> saswp_remove_warnings($all_post_meta, 'saswp_article_headline_'.$schema_id, 'saswp_array'),
                'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_article_description_'.$schema_id, 'saswp_array'),
                'articleSection'                => saswp_remove_warnings($all_post_meta, 'saswp_article_section_'.$schema_id, 'saswp_array'),
                'articleBody'                   => isset($all_post_meta['saswp_article_body_'.$schema_id][0]) ? wp_strip_all_tags(strip_shortcodes($all_post_meta['saswp_article_body_'.$schema_id][0])) : '',
                'keywords'		        => saswp_remove_warnings($all_post_meta, 'saswp_article_keywords_'.$schema_id, 'saswp_array'),                
                'datePublished'                 => isset($all_post_meta['saswp_article_date_published_'.$schema_id][0])? saswp_format_date_time($all_post_meta['saswp_article_date_published_'.$schema_id][0], get_post_time('h:i:s')):'',
                'dateModified'                  => isset($all_post_meta['saswp_article_date_modified_'.$schema_id][0])? saswp_format_date_time($all_post_meta['saswp_article_date_modified_'.$schema_id][0], get_the_modified_time('h:i:s')):'',
                'author'			=> array(
                                '@type' 	=> 'Person',
                                'name'		=> saswp_remove_warnings($all_post_meta, 'saswp_article_author_name_'.$schema_id, 'saswp_array'),
                                'description'   => saswp_remove_warnings($all_post_meta, 'saswp_article_author_description_'.$schema_id, 'saswp_array'),
                                'url'           => saswp_remove_warnings($all_post_meta, 'saswp_article_author_url_'.$schema_id, 'saswp_array') 
                                 )                

        );

        if(isset($all_post_meta['saswp_article_organization_logo_'.$schema_id][0]) || isset($all_post_meta['saswp_article_organization_name_'.$schema_id][0])){
        
            $input1['publisher']['@type']          = 'Organization';
            $input1['publisher']['logo']['@type']  = 'ImageObject';
            $input1['publisher']['logo']['url']    = saswp_remove_warnings($all_post_meta, 'saswp_article_organization_logo_'.$schema_id, 'saswp_array');
            $input1['publisher']['logo']['width']  = saswp_remove_warnings($slogo, 'width', 'saswp_string');
            $input1['publisher']['logo']['height'] = saswp_remove_warnings($slogo, 'height', 'saswp_string');
            $input1['publisher']['name']           = saswp_remove_warnings($all_post_meta, 'saswp_article_organization_name_'.$schema_id, 'saswp_array');
            
        }
        
        $itemlist  = get_post_meta($schema_post_id, 'article_items_'.$schema_id, true);

        if($itemlist){

            $list_arr = array();

            foreach ($itemlist as $list){
                $list_arr[] = $list['saswp_article_items_name'];
            }

            $input1['mainEntity']['@type']            = 'ItemList';
            $input1['mainEntity']['itemListElement']  = $list_arr;                 
            $input1['mainEntity']['itemListOrder']    = 'http://schema.org/ItemListOrderAscending ';
            $input1['mainEntity']['name']             = saswp_get_the_title();

        }

        if(isset($all_post_meta['saswp_article_speakable_'.$schema_id]) && $all_post_meta['saswp_article_speakable_'.$schema_id][0] == 1){

        $input1['speakable']['@type'] = 'SpeakableSpecification';
        $input1['speakable']['xpath'] = array(
             "/html/head/title",
             "/html/head/meta[@name='description']/@content"
        );

       }
    
    return $input1;
    
}

function saswp_tech_article_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
        $input1 = array();
    
        $slogo = get_post_meta( get_the_ID(), 'saswp_tech_article_organization_logo_'.$schema_id.'_detail',true);

        $input1 = array(
                '@context'			=> saswp_context_url(),
                '@type'				=> 'TechArticle',
                '@id'                           => trailingslashit(get_permalink()).'#techarticle',
                'inLanguage'                    => get_bloginfo('language'),
                'mainEntityOfPage'              => saswp_remove_warnings($all_post_meta, 'saswp_tech_article_main_entity_of_page_'.$schema_id, 'saswp_array'),
                'image'				=> saswp_remove_warnings($all_post_meta, 'saswp_tech_article_image_'.$schema_id, 'saswp_array'),
                'headline'			=> saswp_remove_warnings($all_post_meta, 'saswp_tech_article_headline_'.$schema_id, 'saswp_array'),
                'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_tech_article_description_'.$schema_id, 'saswp_array'),
                'keywords'		        => saswp_remove_warnings($all_post_meta, 'saswp_tech_article_keywords_'.$schema_id, 'saswp_array'),
                'datePublished'                 => isset($all_post_meta['saswp_tech_article_date_published_'.$schema_id][0])? saswp_format_date_time($all_post_meta['saswp_tech_article_date_published_'.$schema_id][0], get_post_time('h:i:s')) :'',
                'dateModified'                  => isset($all_post_meta['saswp_tech_article_date_modified_'.$schema_id][0])? saswp_format_date_time($all_post_meta['saswp_tech_article_date_modified_'.$schema_id][0], get_the_modified_time('h:i:s')) :'',
                'author'			=> array(
                                '@type' 	=> 'Person',
                                'name'		=> saswp_remove_warnings($all_post_meta, 'saswp_tech_article_author_name_'.$schema_id, 'saswp_array'),
                                'description'	=> saswp_remove_warnings($all_post_meta, 'saswp_tech_article_author_description_'.$schema_id, 'saswp_array'),
                                'url'	        => saswp_remove_warnings($all_post_meta, 'saswp_tech_article_author_url_'.$schema_id, 'saswp_array') 
                                 ),
                'publisher'			=> array(
                        '@type'			=> 'Organization',
                        'logo' 			=> array(
                                '@type'		=> 'ImageObject',
                                'url'		=> saswp_remove_warnings($all_post_meta, 'saswp_tech_article_organization_logo_'.$schema_id, 'saswp_array'),
                                'width'		=> saswp_remove_warnings($slogo, 'width', 'saswp_string'),
                                'height'	=> saswp_remove_warnings($slogo, 'height', 'saswp_string'),
                                ),
                        'name'			=> saswp_remove_warnings($all_post_meta, 'saswp_tech_article_organization_name_'.$schema_id, 'saswp_array'),
                ),

        ); 

        $itemlist  = get_post_meta($schema_post_id, 'tech_article_items_'.$schema_id, true);

        if($itemlist){

            $list_arr = array();

            foreach ($itemlist as $list){
                $list_arr[] = $list['saswp_tech_article_items_name'];
            }

            $input1['mainEntity']['@type']            = 'ItemList';
            $input1['mainEntity']['itemListElement']  = $list_arr;                 
            $input1['mainEntity']['itemListOrder']    = 'http://schema.org/ItemListOrderAscending ';
            $input1['mainEntity']['name']             = saswp_get_the_title();

        }

        if(isset($all_post_meta['saswp_tech_article_speakable_'.$schema_id]) && $all_post_meta['saswp_tech_article_speakable_'.$schema_id][0] == 1){

        $input1['speakable']['@type'] = 'SpeakableSpecification';
        $input1['speakable']['xpath'] = array(
             "/html/head/title",
             "/html/head/meta[@name='description']/@content"
        );

        }
    
    return $input1;
}

function saswp_news_article_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
    
    $slogo = get_post_meta( get_the_ID(), 'saswp_newsarticle_organization_logo_'.$schema_id.'_detail',true);
                                $author_image = get_post_meta( get_the_ID(), 'saswp_newsarticle_author_image_'.$schema_id.'_detail',true);
                             
				        $input1 = array(
					'@context'			=> saswp_context_url(),
					'@type'				=> 'NewsArticle' ,
                                        '@id'                           => trailingslashit(get_permalink()).'#newsarticle',    
                                        'inLanguage'                    => get_bloginfo('language'),       
					'mainEntityOfPage'              => saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_main_entity_of_page_'.$schema_id, 'saswp_array'),
					'url'				=> saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_URL_'.$schema_id, 'saswp_array'),
                                        'image'				=> saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_image_'.$schema_id, 'saswp_array'),
					'headline'			=> saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_headline_'.$schema_id, 'saswp_array'),
					'datePublished'                 => isset($all_post_meta['saswp_newsarticle_date_published_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_newsarticle_date_published_'.$schema_id][0], get_post_time('h:i:s')) :'',
					'dateModified'                  => isset($all_post_meta['saswp_newsarticle_date_modified_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_newsarticle_date_modified_'.$schema_id][0], get_the_modified_time('h:i:s')) :'',
					'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_description_'.$schema_id, 'saswp_array'),
                                        'keywords'		        => saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_keywords_'.$schema_id, 'saswp_array'),
                                        'articleSection'                => saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_section_'.$schema_id, 'saswp_array'),
                                        'articleBody'                   => saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_body_'.$schema_id, 'saswp_array'),     
					'name'				=> saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_name_'.$schema_id, 'saswp_array'), 					
					'thumbnailUrl'                  => saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_thumbnailurl_'.$schema_id, 'saswp_array'),
                                        'wordCount'                     => saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_word_count_'.$schema_id, 'saswp_array'),
                                        'timeRequired'                  => saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_timerequired_'.$schema_id, 'saswp_array'),    
					'mainEntity'                    => array(
                                                                            '@type' => 'WebPage',
                                                                            '@id'   => saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_main_entity_id_'.$schema_id, 'saswp_array'),
						), 
					'author'			=> array(
							'@type' 			=> 'Person',
							'name'				=> saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_author_name_'.$schema_id, 'saswp_array'),
                                                        'description'			=> saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_author_description_'.$schema_id, 'saswp_array'),
                                                        'url'    			=> saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_author_url_'.$schema_id, 'saswp_array'),
							'Image'				=> array(
                                                                '@type'				=> 'ImageObject',
                                                                'url'				=> saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_author_image_'.$schema_id, 'saswp_array'),
                                                                'height'			=> saswp_remove_warnings($author_image, 'height', 'saswp_string'),
                                                                'width'				=> saswp_remove_warnings($author_image, 'width', 'saswp_string')
										),
							),
					'publisher'			=> array(
							'@type'				=> 'Organization',
							'logo' 				=> array(
							'@type'				=> 'ImageObject',
							'url'				=> saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_organization_logo_'.$schema_id, 'saswp_array'),
							'width'				=> saswp_remove_warnings($slogo, 'width', 'saswp_string'),
							'height'			=> saswp_remove_warnings($slogo, 'height', 'saswp_string'),
										),
							'name'				=> saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_organization_name_'.$schema_id, 'saswp_array'),
							),
					);
                                        
                                        
                                            $itemlist  = get_post_meta($schema_post_id, 'newsarticle_items_'.$schema_id, true);
                                            
                                            if($itemlist){
                                                
                                                $list_arr = array();
                                                
                                                foreach ($itemlist as $list){
                                                    $list_arr[] = $list['saswp_newsarticle_items_name'];
                                                }
                                                
                                                $input1['mainEntity']['@type']            = 'ItemList';
                                                $input1['mainEntity']['itemListElement']  = $list_arr;                 
                                                $input1['mainEntity']['itemListOrder']    = 'http://schema.org/ItemListOrderAscending ';
                                                $input1['mainEntity']['name']             = saswp_get_the_title();
                                                
                                            }
                                        
                                            if(isset($all_post_meta['saswp_newsarticle_speakable_'.$schema_id]) && $all_post_meta['saswp_newsarticle_speakable_'.$schema_id][0] == 1){

                                                $input1['speakable']['@type'] = 'SpeakableSpecification';
                                                $input1['speakable']['xpath'] = array(
                                                     "/html/head/title",
                                                     "/html/head/meta[@name='description']/@content"
                                                );

                                             }
    
    return $input1;
    
}

function saswp_video_object_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
        $input1 = array();
    
        $slogo = get_post_meta( get_the_ID(), 'saswp_video_object_organization_logo_'.$schema_id.'_detail',true);
        $author_image = get_post_meta( get_the_ID(), 'saswp_video_object_author_image_'.$schema_id.'_detail',true);

        $input1 = array(
        '@context'			            => saswp_context_url(),
        '@type'				            => 'VideoObject',
        '@id'                           => trailingslashit(get_permalink()).'#videoobject',    
        'url'				            => saswp_remove_warnings($all_post_meta, 'saswp_video_object_url_'.$schema_id, 'saswp_array'),
        'headline'			            => saswp_remove_warnings($all_post_meta, 'saswp_video_object_headline_'.$schema_id, 'saswp_array'),
        'datePublished'                 => isset($all_post_meta['saswp_video_object_date_published_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_video_object_date_published_'.$schema_id][0], get_post_time('h:i:s')) :'',
        'dateModified'                  => isset($all_post_meta['saswp_video_object_date_modified_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_video_object_date_modified_'.$schema_id][0], get_the_modified_time('h:i:s')) :'',
        'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_video_object_description_'.$schema_id, 'saswp_array'),
        'transcript'                    => saswp_remove_warnings($all_post_meta, 'saswp_video_object_transcript_'.$schema_id, 'saswp_array'),
        'name'				            => saswp_remove_warnings($all_post_meta, 'saswp_video_object_name_'.$schema_id, 'saswp_array'),
        'uploadDate'                    => isset($all_post_meta['saswp_video_object_upload_date_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_video_object_upload_date_'.$schema_id][0])):'',
        'thumbnailUrl'                  => saswp_remove_warnings($all_post_meta, 'saswp_video_object_thumbnail_url_'.$schema_id, 'saswp_array'),        
        'mainEntity'                    => array(
                        '@type'				=> 'WebPage',
                        '@id'				=> saswp_remove_warnings($all_post_meta, 'saswp_video_object_main_entity_id_'.$schema_id, 'saswp_array'),
                        ), 
        'author'			=> array(
                        '@type' 			=> 'Person',
                        'name'				=> saswp_remove_warnings($all_post_meta, 'saswp_video_object_author_name_'.$schema_id, 'saswp_array'),
                        'description'		        => saswp_remove_warnings($all_post_meta, 'saswp_video_object_author_description_'.$schema_id, 'saswp_array'),
                        'url'    		        => saswp_remove_warnings($all_post_meta, 'saswp_video_object_author_url_'.$schema_id, 'saswp_array'),
                        'Image'				=> array(
                            '@type'				=> 'ImageObject',
                            'url'				=> saswp_remove_warnings($all_post_meta, 'saswp_video_object_author_image_'.$schema_id, 'saswp_array'),
                            'height'			=> saswp_remove_warnings($author_image, 'height', 'saswp_string'),
                            'width'				=> saswp_remove_warnings($author_image, 'width', 'saswp_string')
                        ),
                ),
        'publisher'			=> array(
                        '@type'				=> 'Organization',
                        'logo' 				=> array(
                        '@type'				=> 'ImageObject',
                        'url'				=> saswp_remove_warnings($all_post_meta, 'saswp_video_object_organization_logo_'.$schema_id, 'saswp_array'),
                        'width'				=> saswp_remove_warnings($slogo, 'width', 'saswp_string'),
                        'height'			=> saswp_remove_warnings($slogo, 'height', 'saswp_string'),
                                        ),
                        'name'                          => saswp_remove_warnings($all_post_meta, 'saswp_video_object_organization_name_'.$schema_id, 'saswp_array'),
                ),
        );
    
    if(isset($all_post_meta['saswp_video_object_content_url_'.$schema_id][0]) && wp_http_validate_url($all_post_meta['saswp_video_object_content_url_'.$schema_id][0]) ) {
        $input1['contentUrl']   = $all_post_meta['saswp_video_object_content_url_'.$schema_id][0];        
    }
    if(isset($all_post_meta['saswp_video_object_embed_url_'.$schema_id][0]) && wp_http_validate_url($all_post_meta['saswp_video_object_embed_url_'.$schema_id][0])){
        $input1['embedUrl']     = $all_post_meta['saswp_video_object_embed_url_'.$schema_id][0];        
    }    
    
    return $input1;
    
}

function saswp_image_object_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
        $input1 = array();
    
        $slogo = get_post_meta( get_the_ID(), 'saswpimage_object_organization_logo_'.$schema_id.'_detail',true);
        $author_image = get_post_meta( get_the_ID(), 'saswpimage_object_author_image_'.$schema_id.'_detail',true);

        $input1 = array(
        '@context'			=> saswp_context_url(),
        '@type'				=> 'ImageObject',
        '@id'                           => trailingslashit(get_permalink()).'#imageobject',    
        'url'				=> saswp_remove_warnings($all_post_meta, 'saswpimage_object_url_'.$schema_id, 'saswp_array'),						
        'datePublished'                 => isset($all_post_meta['saswpimage_object_date_published_'.$schema_id])? saswp_format_date_time($all_post_meta['saswpimage_object_date_published_'.$schema_id][0], get_post_time('h:i:s')) : '',
        'dateModified'                  => isset($all_post_meta['saswpimage_object_date_modified_'.$schema_id])? saswp_format_date_time($all_post_meta['saswpimage_object_date_modified_'.$schema_id][0], get_the_modified_time('h:i:s')) : '',
        'description'                   => saswp_remove_warnings($all_post_meta, 'saswpimage_object_description_'.$schema_id, 'saswp_array'),
        'name'				=> saswp_remove_warnings($all_post_meta, 'saswpimage_object_name_'.$schema_id, 'saswp_array'),
        'uploadDate'                    => isset($all_post_meta['saswpimage_object_upload_date_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswpimage_object_upload_date_'.$schema_id][0])):'',						
        'contentUrl'                    => saswp_remove_warnings($all_post_meta, 'saswpimage_object_content_url_'.$schema_id, 'saswp_array'),
        'contentLocation'                    => saswp_remove_warnings($all_post_meta, 'saswpimage_object_content_location_'.$schema_id, 'saswp_array'),						
        'author'			=> array(
                        '@type' 			=> 'Person',
                        'name'				=> saswp_remove_warnings($all_post_meta, 'saswpimage_object_author_name_'.$schema_id, 'saswp_array'),
                        'description'		        => saswp_remove_warnings($all_post_meta, 'saswpimage_object_author_description_'.$schema_id, 'saswp_array'),
                        'url'    		        => saswp_remove_warnings($all_post_meta, 'saswpimage_object_author_url_'.$schema_id, 'saswp_array'),
                        'Image'				=> array(
                            '@type'				=> 'ImageObject',
                            'url'				=> saswp_remove_warnings($all_post_meta, 'saswpimage_object_author_image_'.$schema_id, 'saswp_array'),
                            'height'			=> saswp_remove_warnings($author_image, 'height', 'saswp_string'),
                            'width'				=> saswp_remove_warnings($author_image, 'width', 'saswp_string')
                        ),
                ),
        'publisher'			=> array(
                        '@type'				=> 'Organization',
                        'logo' 				=> array(
                        '@type'				=> 'ImageObject',
                        'url'				=> saswp_remove_warnings($all_post_meta, 'saswpimage_object_organization_logo_'.$schema_id, 'saswp_array'),
                        'width'				=> saswp_remove_warnings($slogo, 'width', 'saswp_string'),
                        'height'			=> saswp_remove_warnings($slogo, 'height', 'saswp_string'),
                                        ),
                        'name'                          => saswp_remove_warnings($all_post_meta, 'saswpimage_object_organization_name_'.$schema_id, 'saswp_array'),
                ),
        );



        $itinerary  = get_post_meta($schema_post_id, 'image_object_exif_data_'.$schema_id, true);

        $itinerary_arr = array();

        if(!empty($itinerary)){

            foreach($itinerary as $val){

                $supply_data = array();
                $supply_data['@type']        = 'PropertyValue';
                $supply_data['name']         = $val['saswpimage_object_exif_data_name'];
                $supply_data['value']        = $val['saswpimage_object_exif_data_value'];                                                        

               $itinerary_arr[] =  $supply_data;
            }
           $input1['exifData'] = $itinerary_arr;
        }
    
    return $input1;
    
}

function saswp_service_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
        $input1 = array();
    
        $area_served_str = saswp_remove_warnings($all_post_meta, 'saswp_service_schema_area_served_'.$schema_id, 'saswp_array');
        $area_served_arr = explode(',', $area_served_str);

        $service_offer_str = saswp_remove_warnings($all_post_meta, 'saswp_service_schema_service_offer_'.$schema_id, 'saswp_array');
        $service_offer_arr = explode(',', $service_offer_str);

        $input1['@context']    = saswp_context_url();
        $input1['@type']       = 'Service';
        $input1['@id']         = trailingslashit(get_permalink()).'#service';
        $input1['name']        = saswp_remove_warnings($all_post_meta, 'saswp_service_schema_name_'.$schema_id, 'saswp_array');
        $input1['serviceType'] = saswp_remove_warnings($all_post_meta, 'saswp_service_schema_type_'.$schema_id, 'saswp_array');

        if(isset($all_post_meta['saswp_service_schema_provider_type_'.$schema_id][0])){

             $input1['provider']['@type']                      = $all_post_meta['saswp_service_schema_provider_type_'.$schema_id][0];
             $input1['provider']['name']                       = $all_post_meta['saswp_service_schema_provider_name_'.$schema_id][0];                                    
             $input1['provider']['image']                      = saswp_remove_warnings($all_post_meta, 'saswp_service_schema_image_'.$schema_id, 'saswp_array');
             $input1['provider']['priceRange']                 = saswp_remove_warnings($all_post_meta, 'saswp_service_schema_price_range_'.$schema_id, 'saswp_array');
             $input1['provider']['address']['@type']           = 'PostalAddress';
             $input1['provider']['address']['addressLocality'] = saswp_remove_warnings($all_post_meta, 'saswp_service_schema_locality_'.$schema_id, 'saswp_array');
             $input1['provider']['address']['postalCode']      = saswp_remove_warnings($all_post_meta, 'saswp_service_schema_postal_code_'.$schema_id, 'saswp_array');
             $input1['provider']['address']['telephone']       = saswp_remove_warnings($all_post_meta, 'saswp_service_schema_telephone_'.$schema_id, 'saswp_array');

        }

        $input1['description'] = saswp_remove_warnings($all_post_meta, 'saswp_service_schema_description_'.$schema_id, 'saswp_array');

                $areaServed = array();
                foreach($area_served_arr as $area){
                    $areaServed[] = array(
                        '@type' => 'City',
                        'name'  => $area
                    );
                }
                $serviceOffer = array();
                foreach($service_offer_arr as $offer){
                    $serviceOffer[] = array(
                        '@type' => 'Offer',
                        'name'  => $offer
                    );
                }
               $input1['areaServed'] = $areaServed;
               $input1['hasOfferCatalog'] = array(
                   '@type'            => 'OfferCatalog',
                    'name'            => saswp_remove_warnings($all_post_meta, 'saswp_service_schema_name_'.$schema_id, 'saswp_array'),
                    'itemListElement' => $serviceOffer
               );
    
    return $input1;
    
}

function saswp_review_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    global $sd_data;
    $input1        = array();
    $review_author = '';
    
    if(isset($sd_data['saswp-taqyeem']) && $sd_data['saswp-taqyeem'] == 1 && (is_plugin_active('taqyeem/taqyeem.php') || get_template() != 'jannah')){

         remove_action( 'TieLabs/after_post_entry',  'tie_article_schemas' );

     }                                                                                                                                                              
    
    $input1['@context']                     = saswp_context_url();
    $input1['@type']                        = 'Review';
    $input1['@id']                          = trailingslashit(get_permalink()).'#review';                                                           
    $input1['name']                         = isset($all_post_meta['saswp_review_name_'.$schema_id][0]) ? $all_post_meta['saswp_review_name_'.$schema_id][0] : '';
    $input1['url']                          = isset($all_post_meta['saswp_review_url_'.$schema_id][0]) ? $all_post_meta['saswp_review_url_'.$schema_id][0] : '';                                
    $input1['datePublished']                = isset($all_post_meta['saswp_review_date_published_'.$schema_id][0])&& $all_post_meta['saswp_review_date_published_'.$schema_id][0] !='' ? saswp_format_date_time($all_post_meta['saswp_review_date_published_'.$schema_id][0], get_post_time('h:i:s')) : '';                               
    $input1['dateModified']                 = isset($all_post_meta['saswp_review_date_modified_'.$schema_id][0])&& $all_post_meta['saswp_review_date_modified_'.$schema_id][0] !='' ? saswp_format_date_time($all_post_meta['saswp_review_date_modified_'.$schema_id][0], get_post_time('h:i:s')) : '';                               

    if(isset($all_post_meta['saswp_review_publisher_'.$schema_id][0])){
        $input1['publisher']['@type']          =   'Organization';                                              
        $input1['publisher']['name']           =    $all_post_meta['saswp_review_publisher_'.$schema_id][0];                                              
        if(isset($all_post_meta['saswp_review_publisher_url'.$schema_id][0])){

            $input1['publisher']['sameAs']            = $all_post_meta['saswp_review_publisher_url'.$schema_id][0];   
       
        }
     }

    if(isset($all_post_meta['saswp_review_description_'.$schema_id])){                                                                     
        $input1['description']              = $all_post_meta['saswp_review_description_'.$schema_id][0];
    }

    if(isset($all_post_meta['saswp_review_author_'.$schema_id])){

       $review_author = $all_post_meta['saswp_review_author_'.$schema_id][0];  

    }

    if($review_author){

    $input1['author']['@type']              = 'Person';      
    $input1['author']['name']               = esc_attr($review_author);

    if(isset($all_post_meta['saswp_review_author_url_'.$schema_id])){

     $input1['author']['sameAs']            = esc_url($all_post_meta['saswp_review_author_url_'.$schema_id][0]);   

    }
    
    }

    if(saswp_remove_warnings($all_post_meta, 'saswp_review_enable_rating_'.$schema_id, 'saswp_array') == 1){   

           $input1['reviewRating'] = array(
                             "@type"        => "Rating",
                             "ratingValue"  => saswp_remove_warnings($all_post_meta, 'saswp_review_rating_'.$schema_id, 'saswp_array'),
                             "bestRating"   => saswp_remove_warnings($all_post_meta, 'saswp_review_review_count_'.$schema_id, 'saswp_array')
                          );                                       
     } 

     $item_reviewed = isset($all_post_meta['saswp_review_item_reviewed_'.$schema_id][0]) ? $all_post_meta['saswp_review_item_reviewed_'.$schema_id][0] : '';
     $item_schema = array();
     switch ($item_reviewed) {
         case 'Book':

             $item_schema = saswp_book_schema_markup($schema_id, $schema_post_id, $all_post_meta);

             break;
         case 'Course':

             $item_schema = saswp_course_schema_markup($schema_id, $schema_post_id, $all_post_meta);   

             break;
         case 'Event':

             $item_schema = saswp_event_schema_markup($schema_id, $schema_post_id, $all_post_meta);

             break;
         case 'HowTo':

             $item_schema = saswp_howto_schema_markup($schema_id, $schema_post_id, $all_post_meta);

             break;
         case 'local_business':

             $item_schema = saswp_local_business_schema_markup($schema_id, $schema_post_id, $all_post_meta);

             break;
         case 'MusicPlaylist':

             $item_schema = saswp_music_playlist_schema_markup($schema_id, $schema_post_id, $all_post_meta);

             break;
         case 'Product':

             $item_schema = saswp_product_schema_markup($schema_id, $schema_post_id, $all_post_meta);

             break;
         case 'Recipe':

             $item_schema = saswp_recipe_schema_markup($schema_id, $schema_post_id, $all_post_meta);

             break;
         case 'SoftwareApplication':

             $item_schema = saswp_software_app_schema_markup($schema_id, $schema_post_id, $all_post_meta);

             break;
         case 'MobileApplication':

             $item_schema = saswp_mobile_app_schema_markup($schema_id, $schema_post_id, $all_post_meta);
   
            break;    
         case 'VideoGame':

             $item_schema = saswp_video_game_schema_markup($schema_id, $schema_post_id, $all_post_meta);

             break;
         
         case 'Organization':

             $item_schema = saswp_organization_schema_markup($schema_id, $schema_post_id, $all_post_meta);

             break;
         
         case 'Movie':

             $item_schema = saswp_movie_schema_markup($schema_id, $schema_post_id, $all_post_meta);

             break;

         default:
             break;
     }

     if($item_schema){
         unset($item_schema['@context']);
         unset($item_schema['@id']);
         $input1['itemReviewed'] = $item_schema;

     }
    
    return $input1;    
}