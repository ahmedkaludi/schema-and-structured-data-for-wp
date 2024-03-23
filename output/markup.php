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
                                    
                                    if($rv['saswp_review_rating'] && $rv['saswp_reviewer_name'] !='' ){
                                        $review_rate = intval($rv['saswp_review_rating']);
                                        if($review_rate > 0){
                                            $sumofrating += $review_rate;
                                        }
                                        
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

function saswp_get_modified_image( $key, $input1 ){
    
    $image = get_post_meta( get_the_ID(), $key ,true);
    
    if( !(empty($image)) && is_array($image) ){

        if(isset($image['thumbnail']) && $image['thumbnail'] != ''){

            $input1['image']['@type']        = 'ImageObject';
            $input1['image']['url']          = $image['thumbnail'];
            $input1['image']['height']       = isset($image['width'])     ? esc_attr($image['width'])   :'';
            $input1['image']['width']        = isset($image['height'])    ? esc_attr($image['height'])  :'';

        }
        
    }

    return $input1;

}   

function saswp_book_schema_markup($schema_id, $schema_post_id, $all_post_meta){
 
            $input1 = array();
            $checkIdPro = ((isset($all_post_meta['saswp_book_id_'.$schema_id][0]) && $all_post_meta['saswp_book_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_book_id_'.$schema_id][0] : ''); 

                        
            $input1['@context']              = saswp_context_url();
            $input1['@type']                 = 'Book';
            if($checkIdPro){
                $input1['@id']               = $checkIdPro;  
            }
            $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_book_url_'.$schema_id, 'saswp_array');                            
            $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_book_name_'.$schema_id, 'saswp_array');                            
            $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_book_description_'.$schema_id, 'saswp_array');

            $input1                          = saswp_get_modified_image('saswp_book_image_'.$schema_id.'_detail', $input1);
              
            if( isset($all_post_meta['saswp_book_author_'.$schema_id][0]) && !empty($all_post_meta['saswp_book_author_'.$schema_id][0]) ){

                $input1['author']['@type']   = 'Person';

                if(isset($all_post_meta['saswp_book_author_type_'.$schema_id][0])){
                    $input1['author']['@type']   = $all_post_meta['saswp_book_author_type_'.$schema_id][0];
                }

                $input1['author']['name']    = $all_post_meta['saswp_book_author_'.$schema_id][0];
                
                if($all_post_meta['saswp_book_author_url_'.$schema_id][0]){                    
                    $input1['author']['sameAs']    = $all_post_meta['saswp_book_author_url_'.$schema_id][0];
                }
                
            }
                                    
            $input1['datePublished']        = isset($all_post_meta['saswp_book_date_published_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_book_date_published_'.$schema_id][0])):'';
            $input1['isbn']                 = saswp_remove_warnings($all_post_meta, 'saswp_book_isbn_'.$schema_id, 'saswp_array');                          
            $input1['inLanguage']           = saswp_remove_warnings($all_post_meta, 'saswp_book_inlanguage_'.$schema_id, 'saswp_array');                          
            $input1['bookFormat']           = saswp_remove_warnings($all_post_meta, 'saswp_book_format_'.$schema_id, 'saswp_array');                          
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
                $input1['aggregateRating']['ratingValue']   = $all_post_meta['saswp_book_rating_value_'.$schema_id][0];
                $input1['aggregateRating']['ratingCount']   = $all_post_meta['saswp_book_rating_count_'.$schema_id][0];                                
            }
        
    return $input1;
}

function saswp_movie_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
        $input1 = array();        
        
        $tool        = get_post_meta($schema_post_id, 'movie_actor_'.$schema_id, true);     

        $checkIdPro = ((isset($all_post_meta['saswp_movie_id_'.$schema_id][0]) && $all_post_meta['saswp_movie_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_movie_id_'.$schema_id][0] : '');                 

        $input1['@context']              = saswp_context_url();
        $input1['@type']                 = 'Movie';
        if($checkIdPro){
            $input1['@id']               = $checkIdPro;  
        }
        $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_movie_name_'.$schema_id, 'saswp_array');
        $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_movie_url_'.$schema_id, 'saswp_array');
        $input1['sameAs']                = saswp_remove_warnings($all_post_meta, 'saswp_movie_url_'.$schema_id, 'saswp_array');
        $input1['dateCreated']           = isset($all_post_meta['saswp_movie_date_created_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_movie_date_created_'.$schema_id][0])):'';        
        $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_movie_description_'.$schema_id, 'saswp_array');

        $input1                          = saswp_get_modified_image('saswp_movie_image_'.$schema_id.'_detail', $input1);                      
        
        if(isset($all_post_meta['saswp_movie_director_'.$schema_id][0])){
            
        $input1['director']['@type']        = 'Person';
        $input1['director']['name']          = $all_post_meta['saswp_movie_director_'.$schema_id][0];        
            
        }

        if(isset($all_post_meta['saswp_movie_actor_'.$schema_id][0])){
            
            $input1['actor']['@type']        = 'Person';
            $input1['actor']['name']          = $all_post_meta['saswp_movie_actor_'.$schema_id][0];        
                
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
        
        $tool    = get_post_meta($schema_post_id, 'howto_tool_'.$schema_id, true);              
        $step    = get_post_meta($schema_post_id, 'howto_step_'.$schema_id, true);              
        $supply  = get_post_meta($schema_post_id, 'howto_supply_'.$schema_id, true);              

        $input1['@context']              = saswp_context_url();
        $input1['@type']                 = 'HowTo';
        $input1['@id']                   = ((isset($all_post_meta['saswp_howto_schema_id_'.$schema_id][0]) && $all_post_meta['saswp_howto_schema_id_'.$schema_id][0] !='') ? $all_post_meta['saswp_howto_schema_id_'.$schema_id][0] : get_permalink().'#HowTo');                
        $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_howto_schema_name_'.$schema_id, 'saswp_array');
        $input1['datePublished']         = isset($all_post_meta['saswp_howto_ec_schema_date_published_'.$schema_id][0])? saswp_format_date_time($all_post_meta['saswp_howto_ec_schema_date_published_'.$schema_id][0], get_post_time('h:i:s')) : '';
        $input1['dateModified']          = isset($all_post_meta['saswp_howto_ec_schema_date_modified_'.$schema_id][0])? saswp_format_date_time($all_post_meta['saswp_howto_ec_schema_date_modified_'.$schema_id][0], get_the_modified_time('h:i:s')) : '';
        $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_howto_schema_description_'.$schema_id, 'saswp_array');

        $input1                          = saswp_get_modified_image('saswp_howto_schema_image_'.$schema_id.'_detail', $input1);                                    

        


        if(saswp_remove_warnings($all_post_meta, 'saswp_howto_ec_schema_currency_'.$schema_id, 'saswp_array') !='' && saswp_remove_warnings($all_post_meta, 'saswp_howto_ec_schema_value_'.$schema_id, 'saswp_array') !='')
        {
            $input1['estimatedCost']['@type']   = 'MonetaryAmount';
            $input1['estimatedCost']['currency']= saswp_remove_warnings($all_post_meta, 'saswp_howto_ec_schema_currency_'.$schema_id, 'saswp_array');
            $input1['estimatedCost']['value']   = saswp_remove_warnings($all_post_meta, 'saswp_howto_ec_schema_value_'.$schema_id, 'saswp_array');
        }

        $video_object = array();

        if( isset($all_post_meta['saswp_howto_schema_video_name_'.$schema_id][0]) ){
            $video_object['name']         = $all_post_meta['saswp_howto_schema_video_name_'.$schema_id][0];            
        }
        if( isset($all_post_meta['saswp_howto_schema_video_description_'.$schema_id][0]) ){
            $video_object['description']         = $all_post_meta['saswp_howto_schema_video_description_'.$schema_id][0];            
        }
        if( isset($all_post_meta['saswp_howto_schema_video_thumbnail_url_'.$schema_id][0]) ){
            $video_object['thumbnailUrl']         = $all_post_meta['saswp_howto_schema_video_thumbnail_url_'.$schema_id][0];            
        }
        if( isset($all_post_meta['saswp_howto_schema_video_content_url_'.$schema_id][0]) ){
            $video_object['contentUrl']         = $all_post_meta['saswp_howto_schema_video_content_url_'.$schema_id][0];            
        }
        if( isset($all_post_meta['saswp_howto_schema_video_embed_url_'.$schema_id][0]) ){
            $video_object['embedUrl']         = $all_post_meta['saswp_howto_schema_video_embed_url_'.$schema_id][0];            
        }
        if( isset($all_post_meta['saswp_howto_schema_video_upload_date_'.$schema_id][0]) ){
            $video_object['uploadDate']         = $all_post_meta['saswp_howto_schema_video_upload_date_'.$schema_id][0];            
        }
        if( isset($all_post_meta['saswp_howto_schema_video_duration_'.$schema_id][0]) ){
            $video_object['duration']         = $all_post_meta['saswp_howto_schema_video_duration_'.$schema_id][0];            
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
        $haspart = array();
        $step_arr = array();                            
        if(!empty($step)){
             $j = 1;   
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
                $supply_data['url']     = get_permalink().'#step'.++$key;
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

                if(isset($val['saswp_howto_video_clip_name']) && $val['saswp_howto_video_start_offset']){

                    $haspart[] = array(
                        '@type'       => 'Clip',
                        '@id'         => 'Clip'.$j,
                        'name'        => $val['saswp_howto_video_clip_name'],
                        'startOffset' => $val['saswp_howto_video_start_offset'],
                        'endOffset'   => $val['saswp_howto_video_end_offset'],
                        'url'         => $val['saswp_howto_video_clip_url'],
                    );      
                    
                    $supply_data['video']['@id'] = 'Clip'.$j; 
                }

               $step_arr[] =  $supply_data;
                $j++;
            }

           $input1['step'] = $step_arr;

        }

        if(!empty($video_object)){
            $video_object['@type']   = 'VideoObject';
            $video_object['hasPart'] = $haspart;
            $input1['video'] = $video_object;
        }

         $input1['totalTime'] = saswp_remove_warnings($all_post_meta, 'saswp_howto_schema_totaltime_'.$schema_id, 'saswp_array');
        
         $explode_about = explode(',', $all_post_meta['saswp_howto_about_'.$schema_id][0]);
         if(!empty($explode_about)){
             $about_arr = array();
                     foreach($explode_about as $val){
                         $about_arr[] = array(
                                     '@type' => 'Thing',
                                     'name'  => $val
                         );
                     }
                     $input1['about'] = $about_arr;
        }                                            
        
    return $input1;
}

function saswp_eop_schema_markup($schema_id, $schema_post_id, $all_post_meta){

            $input1 = array();
            $checkIdPro = ((isset($all_post_meta['saswp_eop_id_'.$schema_id][0]) && $all_post_meta['saswp_eop_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_eop_id_'.$schema_id][0] : '');
           
            $input1['@context']                     = saswp_context_url();
            $input1['@type']                        = 'EducationalOccupationalProgram';
            if($checkIdPro){
                $input1['@id']                      = $checkIdPro;  
            } 
            $input1['name']                         = saswp_remove_warnings($all_post_meta, 'saswp_eop_name_'.$schema_id, 'saswp_array');
            $input1['url']                          = saswp_remove_warnings($all_post_meta, 'saswp_eop_url_'.$schema_id, 'saswp_array');                            
            $input1['description']                  = saswp_remove_warnings($all_post_meta, 'saswp_eop_description_'.$schema_id, 'saswp_array');
           
            $howto_image = get_post_meta( get_the_ID(), 'saswp_eop_image_'.$schema_id.'_detail',true); 
            
          if(!(empty($howto_image))){

            $input1['image']['@type']        = 'ImageObject';
            $input1['image']['url']          = isset($howto_image['thumbnail']) ? esc_url($howto_image['thumbnail']):'';
            $input1['image']['height']       = isset($howto_image['width'])     ? esc_attr($howto_image['width'])   :'';
            $input1['image']['width']        = isset($howto_image['height'])    ? esc_attr($howto_image['height'])  :'';

          }

          $input1['provider']['@type']                        = 'EducationalOrganization';
          $input1['provider']['address']['name']              = saswp_remove_warnings($all_post_meta, 'saswp_eop_provider_name_'.$schema_id, 'saswp_array');
          $input1['provider']['address']['streetAddress']     = saswp_remove_warnings($all_post_meta, 'saswp_eop_provider_street_address_'.$schema_id, 'saswp_array');
          $input1['provider']['address']['addressCountry']    = saswp_remove_warnings($all_post_meta, 'saswp_eop_provider_address_country_'.$schema_id, 'saswp_array');
          $input1['provider']['address']['addressLocality']   = saswp_remove_warnings($all_post_meta, 'saswp_eop_provider_address_locality_'.$schema_id, 'saswp_array');
          $input1['provider']['address']['addressRegion']     = saswp_remove_warnings($all_post_meta, 'saswp_eop_provider_address_region_'.$schema_id, 'saswp_array');
          $input1['provider']['address']['postalCode']        = saswp_remove_warnings($all_post_meta, 'saswp_eop_provider_postal_code_'.$schema_id, 'saswp_array');

          $input1['provider']['contactPoint']['@type'] = 'ContactPoint';
          $input1['provider']['contactPoint']['contactType'] = 'Admissions';
          $input1['provider']['contactPoint']['telephone']         = saswp_remove_warnings($all_post_meta, 'saswp_eop_provider_telephone_'.$schema_id, 'saswp_array');                                                                  
          
          if( isset($all_post_meta['saswp_eop_time_to_complete_'.$schema_id][0]) ){
            $input1['timeToComplete']         = $all_post_meta['saswp_eop_time_to_complete_'.$schema_id][0];            
          }
          if( isset($all_post_meta['saswp_eop_occupational_category_'.$schema_id][0]) ){
            $input1['occupationalCategory']         = explode(',', $all_post_meta['saswp_eop_occupational_category_'.$schema_id][0]);            
          }
          if( isset($all_post_meta['saswp_eop_occupational_credential_awarded_'.$schema_id][0]) ){
            $input1['occupationalCredentialAwarded']['@type']                      = 'EducationalOccupationalCredential';
            $input1['occupationalCredentialAwarded']['credentialCategory']         = saswp_format_date_time($all_post_meta['saswp_eop_occupational_credential_awarded_'.$schema_id][0]);            
          }
          if( isset($all_post_meta['saswp_eop_program_prerequisites_'.$schema_id][0]) ){
            $input1['programPrerequisites']['@type'] = 'EducationalOccupationalCredential';
            $input1['programPrerequisites']          = $all_post_meta['saswp_eop_program_prerequisites_'.$schema_id][0];            
          }
          if( isset($all_post_meta['saswp_eop_application_start_date_'.$schema_id][0]) ){
            $input1['applicationStartDate']         = $all_post_meta['saswp_eop_application_start_date_'.$schema_id][0];            
          }
          if( isset($all_post_meta['saswp_eop_application_deadline_'.$schema_id][0]) ){
            $input1['applicationDeadline']         = $all_post_meta['saswp_eop_application_deadline_'.$schema_id][0];            
          }
          if( isset($all_post_meta['saswp_eop_start_date_'.$schema_id][0]) ){
            $input1['startDate']         = $all_post_meta['saswp_eop_start_date_'.$schema_id][0];            
          }
          if( isset($all_post_meta['saswp_eop_end_date_'.$schema_id][0]) ){
            $input1['endDate']         = $all_post_meta['saswp_eop_end_date_'.$schema_id][0];            
          }
          if( isset($all_post_meta['saswp_eop_day_of_week_'.$schema_id][0]) ){
            $input1['dayOfWeek']         = explode(',' ,$all_post_meta['saswp_eop_day_of_week_'.$schema_id][0]);            
          }
          if( isset($all_post_meta['saswp_eop_time_of_day_'.$schema_id][0]) ){
            $input1['timeOfDay']         = $all_post_meta['saswp_eop_time_of_day_'.$schema_id][0];            
          }          
          if( isset($all_post_meta['saswp_eop_number_of_credits_'.$schema_id][0]) ){
            $input1['numberOfCredits']         = $all_post_meta['saswp_eop_number_of_credits_'.$schema_id][0];            
          }
          if( isset($all_post_meta['saswp_eop_typical_credits_per_term_'.$schema_id][0]) ){
            $input1['typicalCreditsPerTerm']         = $all_post_meta['saswp_eop_typical_credits_per_term_'.$schema_id][0];            
          }
          if( isset($all_post_meta['saswp_eop_term_duration_'.$schema_id][0]) ){
            $input1['termDuration']         = $all_post_meta['saswp_eop_term_duration_'.$schema_id][0];            
          }
          if( isset($all_post_meta['saswp_eop_terms_per_year_'.$schema_id][0]) ){
            $input1['termsPerYear']         = $all_post_meta['saswp_eop_terms_per_year_'.$schema_id][0];            
          }
          if( isset($all_post_meta['saswp_eop_maximum_enrollment_'.$schema_id][0]) ){
            $input1['maximumEnrollment']         = $all_post_meta['saswp_eop_maximum_enrollment_'.$schema_id][0];            
          }
          if( isset($all_post_meta['saswp_eop_educational_program_mode_'.$schema_id][0]) ){
            $input1['educationalProgramMode']         = $all_post_meta['saswp_eop_educational_program_mode_'.$schema_id][0];            
          }
          if( isset($all_post_meta['saswp_eop_financial_aid_eligible_'.$schema_id][0]) ){
            $input1['financialAidEligible']         = $all_post_meta['saswp_eop_financial_aid_eligible_'.$schema_id][0];            
          }

          $identifier    = get_post_meta($schema_post_id, 'eopidentifier_'.$schema_id, true);
          
          if(!empty($identifier)){
              $data = array();
              foreach ($identifier as $value) {
                  $data[] = array(
                      '@type'      => 'PropertyValue',
                      'propertyID' => $value['saswp_eopidentifier_property_id'],
                      'value'      => $value['saswp_eopidentifier_property_value']
                  );
              }
              $input1['identifier'] = $data;
          }

          $offer    = get_post_meta($schema_post_id, 'eopoffer_'.$schema_id, true);
          
          if(!empty($offer)){
              $data = array();
              foreach ($offer as $value) {
                  $data[] = array(
                      '@type'      => 'Offer',
                      'category'   => $value['saswp_eopoffer_category'],
                      'priceSpecification' => array(
                          '@type'         => 'PriceSpecification',
                          'price'         => $value['saswp_eopoffer_price'],
                          'priceCurrency' => $value['saswp_eopoffer_price_currency']
                      )                      
                  );
              }
              $input1['offers'] = $data;
          }
                                                  
        return $input1;

}
function saswp_event_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
            $input1 = array();
                
            $input1 = array(
                '@context'			=> saswp_context_url(),
                '@type'				=> (isset($all_post_meta['saswp_event_schema_type_'.$schema_id][0]) && $all_post_meta['saswp_event_schema_type_'.$schema_id][0] !='') ? $all_post_meta['saswp_event_schema_type_'.$schema_id][0] : 'Event' ,            
                'name'			    => saswp_remove_warnings($all_post_meta, 'saswp_event_schema_name_'.$schema_id, 'saswp_array'),
                'description'       => saswp_remove_warnings($all_post_meta, 'saswp_event_schema_description_'.$schema_id, 'saswp_array')
            );

            $input1                          = saswp_get_modified_image('saswp_event_schema_image_'.$schema_id.'_detail', $input1);

                
                if(isset($all_post_meta['saswp_event_schema_high_price_'.$schema_id][0]) && isset($all_post_meta['saswp_event_schema_low_price_'.$schema_id][0])){

                        $input1['offers'] = array(
                            '@type'           => 'AggregateOffer',
                            'url'             => saswp_remove_warnings($all_post_meta, 'saswp_event_schema_url_'.$schema_id, 'saswp_array'),	                        
                            'highPrice'       => saswp_remove_warnings($all_post_meta, 'saswp_event_schema_high_price_'.$schema_id, 'saswp_array'),
                            'lowPrice'       => saswp_remove_warnings($all_post_meta, 'saswp_event_schema_low_price_'.$schema_id, 'saswp_array'),
                            'price'           => saswp_remove_warnings($all_post_meta, 'saswp_event_schema_price_'.$schema_id, 'saswp_array'),
                            'priceCurrency'   => saswp_remove_warnings($all_post_meta, 'saswp_event_schema_price_currency_'.$schema_id, 'saswp_array'),
                            'availability'    => saswp_remove_warnings($all_post_meta, 'saswp_event_schema_availability_'.$schema_id, 'saswp_array'),
                            'validFrom'       => isset($all_post_meta['saswp_event_schema_validfrom_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_event_schema_validfrom_'.$schema_id][0])):'',
                        );

                }else{

                    if(isset($all_post_meta['saswp_event_schema_price_'.$schema_id][0])){

                            $input1['offers'] = array(
                                '@type'           => 'Offer',
                                'url'             => saswp_remove_warnings($all_post_meta, 'saswp_event_schema_url_'.$schema_id, 'saswp_array'),	                        
                                'price'           => saswp_remove_warnings($all_post_meta, 'saswp_event_schema_price_'.$schema_id, 'saswp_array'),
                                'priceCurrency'   => saswp_remove_warnings($all_post_meta, 'saswp_event_schema_price_currency_'.$schema_id, 'saswp_array'),
                                'availability'    => saswp_remove_warnings($all_post_meta, 'saswp_event_schema_availability_'.$schema_id, 'saswp_array'),
                                'validFrom'       => isset($all_post_meta['saswp_event_schema_validfrom_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_event_schema_validfrom_'.$schema_id][0])):'',
                            );

                    }

                }

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
                
                if(isset($all_post_meta['saswp_event_schema_id_'.$schema_id][0]) && $all_post_meta['saswp_event_schema_id_'.$schema_id][0] != ''){
                    $input1['@id']                   = $all_post_meta['saswp_event_schema_id_'.$schema_id][0];
                }else{
                    $input1['@id']                   = get_permalink().'#Event';
                }                
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
                $start_date = '';
                $start_time = '';
                if(isset($all_post_meta['saswp_event_schema_start_date_'.$schema_id][0])){
                                                           
                    $start_date = $all_post_meta['saswp_event_schema_start_date_'.$schema_id][0];
                    
                    if(isset($all_post_meta['saswp_event_schema_start_time_'.$schema_id][0])){
                        $start_time  = $all_post_meta['saswp_event_schema_start_time_'.$schema_id][0];    
                    }
                                        
                    $input1['startDate']        = saswp_format_date_time($start_date, $start_time);
                    
                }
                $end_date = '';
                $end_time = '';
                if(isset($all_post_meta['saswp_event_schema_end_date_'.$schema_id][0])){
                                                            
                    $end_date = $all_post_meta['saswp_event_schema_end_date_'.$schema_id][0];
                    
                    if(isset($all_post_meta['saswp_event_schema_end_time_'.$schema_id][0])){
                        $end_time  = $all_post_meta['saswp_event_schema_end_time_'.$schema_id][0];    
                    }
                    
                    $input1['endDate']        = saswp_format_date_time($end_date, $end_time);
                    
                }

                if(!empty($all_post_meta['saswp_event_schema_schedule_repeat_frequency_'.$schema_id][0])){

                    $input1['eventSchedule']['@type']           = 'Schedule';
                    $input1['eventSchedule']['startDate']       = $start_date;
                    $input1['eventSchedule']['endDate']         = $end_date;
                    $input1['eventSchedule']['startTime']       = $start_time;
                    $input1['eventSchedule']['endTime']         = $end_time;
                    $input1['eventSchedule']['repeatFrequency'] = $all_post_meta['saswp_event_schema_schedule_repeat_frequency_'.$schema_id][0];

                    if(!empty($all_post_meta['saswp_event_schema_schedule_by_day_'.$schema_id][0])){
                        $input1['eventSchedule']['byDay'] = explode(',', $all_post_meta['saswp_event_schema_schedule_by_day_'.$schema_id][0]);
                    }
                    if(!empty($all_post_meta['saswp_event_schema_schedule_timezone_'.$schema_id][0])){
                        $input1['eventSchedule']['scheduleTimezone'] = $all_post_meta['saswp_event_schema_schedule_timezone_'.$schema_id][0];
                    }
                    if(!empty($all_post_meta['saswp_event_schema_schedule_by_month_day_'.$schema_id][0])){
                        $input1['eventSchedule']['byMonthDay'] = explode(',', $all_post_meta['saswp_event_schema_schedule_by_month_day_'.$schema_id][0]);
                    }   

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
    $checkIdPro = ((isset($all_post_meta['saswp_course_id_'.$schema_id][0]) && $all_post_meta['saswp_course_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_course_id_'.$schema_id][0] : '');  
    
    $input1 = array(
             '@context'			=> saswp_context_url(),
             '@type'				=> 'Course' ,	
             '@id'                  => $checkIdPro,
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

                if(empty($input1['@id'])){
                    unset($input1['@id']);
                }

                if( isset($all_post_meta['saswp_course_content_location_name_'.$schema_id][0]) || isset($all_post_meta['saswp_course_content_location_locality_'.$schema_id][0]) || isset($all_post_meta['saswp_course_content_location_country_'.$schema_id][0])  ){

                        $input1['contentLocation']['@type']                        =   'Place';
                        $input1['contentLocation']['name']                         =   $all_post_meta['saswp_course_content_location_name_'.$schema_id][0];
                        $input1['contentLocation']['address']['addressLocality']   =   $all_post_meta['saswp_course_content_location_locality_'.$schema_id][0];
                        $input1['contentLocation']['address']['addressRegion']     =   $all_post_meta['saswp_course_content_location_region_'.$schema_id][0];
                        $input1['contentLocation']['address']['postalCode']        =   $all_post_meta['saswp_course_content_location_postal_code_'.$schema_id][0];
                        $input1['contentLocation']['address']['addressCountry']    =   $all_post_meta['saswp_course_content_location_country_'.$schema_id][0];

                } 
                 
                 if(isset($all_post_meta['saswp_course_enable_rating_'.$schema_id]) && saswp_remove_warnings($all_post_meta, 'saswp_course_rating_'.$schema_id, 'saswp_array') && saswp_remove_warnings($all_post_meta, 'saswp_course_review_count_'.$schema_id, 'saswp_array')){
                                 
                    $input1['aggregateRating'] = array(
                                       "@type"       => "AggregateRating",
                                       "ratingValue" => saswp_remove_warnings($all_post_meta, 'saswp_course_rating_'.$schema_id, 'saswp_array'),
                                       "reviewCount" => saswp_remove_warnings($all_post_meta, 'saswp_course_review_count_'.$schema_id, 'saswp_array')
                    );                                       
            }
            
            $input1['offers']['@type'] = 'Offer';
            $input1['offers']['category'] = saswp_remove_warnings($all_post_meta, 'saswp_course_offer_category_'.$schema_id, 'saswp_array');
            $input1['offers']['price'] = saswp_remove_warnings($all_post_meta, 'saswp_course_offer_price_'.$schema_id, 'saswp_array');
            $input1['offers']['priceCurrency'] = saswp_remove_warnings($all_post_meta, 'saswp_course_offer_currency_'.$schema_id, 'saswp_array');;
            
            /**
             * Add hasCourseInstance field to schema markup
             * @since 1.25
             * */
            $course_instance = get_post_meta($schema_post_id, 'course_instance_'.$schema_id, true);
            if(!empty($course_instance) && is_array($course_instance)){
                foreach ($course_instance as $ci_key => $ci_value) {
                    $instance_array = array();
                    if(!empty($ci_value) && is_array($ci_value)){
                        $instance_array['@type'] = 'CourseInstance';
                        $instance_array['courseMode'] = isset($ci_value['saswp_course_instance_mode'])?sanitize_text_field($ci_value['saswp_course_instance_mode']):'';
                        if(isset($ci_value['saswp_course_instance_mode']) && !empty($ci_value['saswp_course_instance_mode'])){
                            $explode_mode = explode(',', $ci_value['saswp_course_instance_mode']);
                            if(!empty($explode_mode) && is_array($explode_mode)){
                                if(count($explode_mode) > 1){
                                    $cmode = array();
                                    foreach ($explode_mode as $em_key => $em_value) {
                                        if(!empty($em_value)){
                                            array_push($cmode, $em_value);
                                        }
                                    }
                                    $instance_array['courseMode'] = $cmode;
                                }else if(count($explode_mode) == 1){
                                    $instance_array['courseMode'] = isset($explode_mode[0])?sanitize_text_field($explode_mode[0]):'';
                                }
                            } 
                        }
                        // If course work load data is empty then add course schedule in the markup otherwise add course work load
                        if((isset($ci_value['saswp_course_instance_wl']) && empty($ci_value['saswp_course_instance_wl'])) && (isset($ci_value['saswp_course_instance_sd']) || isset($ci_value['saswp_course_instance_src']) || isset($ci_value['saswp_course_instance_srf']))){
                            $instance_array['courseSchedule']['@type'] = 'Schedule';
                            $instance_array['courseSchedule']['duration'] = isset($ci_value['saswp_course_instance_sd'])?sanitize_text_field($ci_value['saswp_course_instance_sd']):'';
                            $instance_array['courseSchedule']['repeatFrequency'] = isset($ci_value['saswp_course_instance_srf'])?sanitize_text_field($ci_value['saswp_course_instance_srf']):'';
                            $instance_array['courseSchedule']['repeatCount'] = isset($ci_value['saswp_course_instance_src'])?intval($ci_value['saswp_course_instance_src']):'';
                            $instance_array['courseSchedule']['endDate'] = isset($ci_value['saswp_course_instance_end_date'])?date('Y-m-d', strtotime(sanitize_text_field($ci_value['saswp_course_instance_end_date']))):'';
                            $instance_array['courseSchedule']['startDate'] = isset($ci_value['saswp_course_instance_start_date'])?date('Y-m-d', strtotime(sanitize_text_field($ci_value['saswp_course_instance_start_date']))):'';
                        }else if(isset($ci_value['saswp_course_instance_wl']) && !empty($ci_value['saswp_course_instance_wl'])){
                            $instance_array['courseWorkload'] = sanitize_text_field($ci_value['saswp_course_instance_wl']);
                            $instance_array['endDate'] = isset($ci_value['saswp_course_instance_end_date'])?date('Y-m-d', strtotime(sanitize_text_field($ci_value['saswp_course_instance_end_date']))):'';
                            $instance_array['startDate'] = isset($ci_value['saswp_course_instance_start_date'])?date('Y-m-d', strtotime(sanitize_text_field($ci_value['saswp_course_instance_start_date']))):'';
                        }else{
                            $instance_array['endDate'] = isset($ci_value['saswp_course_instance_end_date'])?date('Y-m-d', strtotime(sanitize_text_field($ci_value['saswp_course_instance_end_date']))):'';
                            $instance_array['startDate'] = isset($ci_value['saswp_course_instance_start_date'])?date('Y-m-d', strtotime(sanitize_text_field($ci_value['saswp_course_instance_start_date']))):'';
                        }
                        $instance_array['location'] = isset($ci_value['saswp_course_instance_location'])?sanitize_text_field($ci_value['saswp_course_instance_location']):'';
                        if(isset($ci_value['saswp_course_instance_offer_price']) && isset($ci_value['saswp_course_instance_offer_currency'])){
                            if(!empty($ci_value['saswp_course_instance_offer_price']) && !empty($ci_value['saswp_course_instance_offer_currency'])){
                                $instance_array['offers']['@type'] = 'Offer';   
                                $instance_array['offers']['price'] = sanitize_text_field($ci_value['saswp_course_instance_offer_price']);   
                                $instance_array['offers']['priceCurrency'] = sanitize_text_field($ci_value['saswp_course_instance_offer_currency']);   
                            }
                        }
                        $input1['hasCourseInstance'][] = $instance_array;
                    }
                }
            }
    return $input1;
    
}
function saswp_mobile_app_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
    $checkIdPro = ((isset($all_post_meta['saswp_mobile_app_id_'.$schema_id][0]) && $all_post_meta['saswp_mobile_app_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_mobile_app_id_'.$schema_id][0] : '');
        
    $input1 = array(
             '@context'			=> saswp_context_url(),
             '@type'				=> 'MobileApplication',
             '@id'                           => $checkIdPro,     
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

                if(empty($input1['@id'])){
                    unset($input1['@id']);
                }

                $input1 = saswp_get_modified_image('saswp_mobile_app_schema_image_'.$schema_id.'_detail', $input1);
                 
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
    $checkIdPro = ((isset($all_post_meta['saswp_software_schema_id_'.$schema_id][0]) && $all_post_meta['saswp_software_schema_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_software_schema_id_'.$schema_id][0] : '');
    
    $input1 = array(
             '@context'			=> saswp_context_url(),
             '@type'				=> 'SoftwareApplication',
             '@id'                           =>  $checkIdPro,     
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
                
                if(empty($input1['@id'])){
                    unset($input1['@id']);
                }

                $input1 = saswp_get_modified_image('saswp_software_schema_image_'.$schema_id.'_detail', $input1);
                 
                 if(saswp_remove_warnings($all_post_meta, 'saswp_software_schema_enable_rating_'.$schema_id, 'saswp_array') == 1){   
                                 
                                          $input1['aggregateRating'] = array(
                                                            "@type"       => "AggregateRating",
                                                            "ratingValue" => saswp_remove_warnings($all_post_meta, 'saswp_software_schema_rating_'.$schema_id, 'saswp_array'),
                                                            "reviewCount" => saswp_remove_warnings($all_post_meta, 'saswp_software_schema_rating_count_'.$schema_id, 'saswp_array')
                                                         );          
                                                         
                    $itinerary  = get_post_meta($schema_post_id, 'product_reviews_'.$schema_id, true);

                    $itinerary_arr = array();

                if(!empty($itinerary)){

                    foreach($itinerary as $review){
                            
                        $review_fields = array();
                        
                        $review_fields['@type']           = 'Review';
                        
                        if(isset($all_post_meta['product_pros_'.$schema_id][0])){

                            $review_fields['positiveNotes']['@type'] = 'ItemList';
                            
                            $itemList = [];                                                    
                            foreach(unserialize($all_post_meta['product_pros_'.$schema_id][0]) as $key => $positiveNotes){                                              
                            
                                $itemList[$key]['@type'] = 'ListItem';
                                $itemList[$key]['position'] = 1;
                                $itemList[$key]['name'] = $positiveNotes['saswp_product_pros_title'];
                            }
                            $review_fields['positiveNotes']['itemListElement'] = $itemList;
                        
                        }

                    if(isset($all_post_meta['product_cons_'.$schema_id][0])){

                        $review_fields['negativeNotes']['@type'] = 'ItemList';
                        
                        $itemList = [];                                                      
                        foreach(unserialize($all_post_meta['product_cons_'.$schema_id][0]) as $key => $positiveNotes){                                              
                        
                            $itemList[$key]['@type'] = 'ListItem';
                            $itemList[$key]['position'] = 1;
                            $itemList[$key]['name'] = $positiveNotes['saswp_product_cons_title'];
                        }

                        $review_fields['negativeNotes']['itemListElement'] = $itemList;
                    
                    }

                    $review_fields['author']['@type'] = 'Person';
                    $review_fields['author']['name']  = $review['saswp_product_reviews_reviewer_name'] ? esc_attr($review['saswp_product_reviews_reviewer_name']) : 'Anonymous';

                    if(isset($review['saswp_product_reviews_created_date'])){
                        $review_fields['datePublished'] = esc_html($review['saswp_product_reviews_created_date']);
                    }
                    if(isset($review['saswp_product_reviews_text'])){
                        $review_fields['description']   = esc_textarea($review['saswp_product_reviews_text']);
                    }                    
                                                                                  
                    if($review['saswp_product_reviews_reviewer_rating']){

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
                    
                    $review_fields['@type']           = 'Review';
                    $review_fields['author']['@type'] = 'Person';
                    $review_fields['author']['name']  = $review['author'] ? esc_attr($review['author']) : 'Anonymous';
                    $review_fields['datePublished']   = esc_html($review['datePublished']);
                    $review_fields['description']     = $review['description'];
                                                                
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
                 
                                 if(!isset($input1['review'])){
                                     $input1 = saswp_append_fetched_reviews($input1); 
                                 }                             
                }
    
    
    return $input1;
    
}

function saswp_recipe_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
            $input1 = array();
            $checkIdPro = ((isset($all_post_meta['saswp_recipe_id_'.$schema_id][0]) && $all_post_meta['saswp_recipe_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_recipe_id_'.$schema_id][0] : '');

            $recipe_logo           = get_post_meta( get_the_ID(), 'saswp_recipe_organization_logo_'.$schema_id.'_detail',true);            
            $recipe_author_image   = get_post_meta( get_the_ID(), 'saswp_recipe_author_image_'.$schema_id.'_detail',true);

            $step    = get_post_meta($schema_post_id, 'recipe_instructions_'.$schema_id, true);  

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
                                                   'text'   => wp_strip_all_tags($val),                                                                                                                            
                                                   );  

                  }                                                     
            }


            $input1 = array(
            '@context'			             => saswp_context_url(),
            '@type'				             => 'Recipe' ,
            '@id'                            =>  $checkIdPro,   
            'url'				             => saswp_remove_warnings($all_post_meta, 'saswp_recipe_url_'.$schema_id, 'saswp_array'),
            'name'			                 => saswp_remove_warnings($all_post_meta, 'saswp_recipe_name_'.$schema_id, 'saswp_array'),
            'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_recipe_description_'.$schema_id, 'saswp_array'),                       
            'prepTime'                       => saswp_format_time_to_ISO_8601(saswp_remove_warnings($all_post_meta, 'saswp_recipe_preptime_'.$schema_id, 'saswp_array')),  
            'cookTime'                       => saswp_format_time_to_ISO_8601(saswp_remove_warnings($all_post_meta, 'saswp_recipe_cooktime_'.$schema_id, 'saswp_array')),  
            'totalTime'                      => saswp_format_time_to_ISO_8601(saswp_remove_warnings($all_post_meta, 'saswp_recipe_totaltime_'.$schema_id, 'saswp_array')),  
            'keywords'                       => saswp_remove_warnings($all_post_meta, 'saswp_recipe_keywords_'.$schema_id, 'saswp_array'),  
            'recipeYield'                    => saswp_remove_warnings($all_post_meta, 'saswp_recipe_recipeyield_'.$schema_id, 'saswp_array'),  
            'recipeCategory'                 => saswp_remove_warnings($all_post_meta, 'saswp_recipe_category_'.$schema_id, 'saswp_array'),
            'recipeCuisine'                  => saswp_remove_warnings($all_post_meta, 'saswp_recipe_cuisine_'.$schema_id, 'saswp_array'),              
            'recipeIngredient'               => $ingredient, 
            'recipeInstructions'             => $instruction,                                                                                                                                                                                 
            'datePublished'                 => isset($all_post_meta['saswp_recipe_date_published_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_recipe_date_published_'.$schema_id][0])):'',
            'dateModified'                  => isset($all_post_meta['saswp_recipe_date_modified_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_recipe_date_modified_'.$schema_id][0])):'',            
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

            if(empty($input1['@id'])){
                unset($input1['@id']);
            }

            $input1 = saswp_get_modified_image('saswp_recipe_image_'.$schema_id.'_detail', $input1);

            //Recipe instruction improved version

            $step_arr = array(); 
            
            if(!empty($step)){

                foreach($step as $key => $val){

                    $supply_data = array();
                                                          
                    $supply_data['@type']   = 'HowToStep';
                    $supply_data['url']     = get_permalink().'#step'.++$key;
                    $supply_data['name']    = $val['saswp_recipe_instructions_step_name'];  
                    $supply_data['text']    = $val['saswp_recipe_instructions_step_text'];                        

                    if(isset($val['saswp_recipe_instructions_step_image_id']) && $val['saswp_recipe_instructions_step_image_id'] !=''){

                        $image_details   = saswp_get_image_by_id($val['saswp_recipe_instructions_step_image_id']);  
                        
                        if($image_details){
                            $supply_data['image']  = $image_details;                                                
                        }
                                
                    }

                    $step_arr[] =  $supply_data;

                }

                 $input1['recipeInstructions'] = $step_arr;

            }

            if( isset($all_post_meta['saswp_recipe_author_name_'.$schema_id][0]) ) {

                $input1['author']['@type']          = 'Person';

                if(isset($all_post_meta['saswp_recipe_author_type_'.$schema_id][0])){
                    $input1['author']['@type']          = $all_post_meta['saswp_recipe_author_type_'.$schema_id][0];
                }

                $input1['author']['name']           = $all_post_meta['saswp_recipe_author_name_'.$schema_id][0];
                $input1['author']['description']    = $all_post_meta['saswp_recipe_author_description_'.$schema_id][0];
                $input1['author']['url']            = saswp_remove_warnings($all_post_meta, 'saswp_recipe_author_url_'.$schema_id, 'saswp_array');

                $input1['author']['image']['@type']  = 'ImageObject';
                $input1['author']['image']['url']    = $all_post_meta['saswp_recipe_author_image_'.$schema_id][0];
                $input1['author']['image']['height'] = isset($recipe_author_image['height'])?$recipe_author_image['height']:'';
                $input1['author']['image']['width']  = isset($recipe_author_image['width'])?$recipe_author_image['width']:'';

            }

            if($all_post_meta['saswp_recipe_nutrition_'.$schema_id][0]){                
                 $input1['nutrition']['@type']    = 'NutritionInformation';
                 $input1['nutrition']['calories'] = $all_post_meta['saswp_recipe_nutrition_'.$schema_id][0];
            }
            if($all_post_meta['saswp_recipe_protein_'.$schema_id][0]){                
                $input1['nutrition']['@type']    = 'NutritionInformation';
                $input1['nutrition']['proteinContent'] = $all_post_meta['saswp_recipe_protein_'.$schema_id][0];
               }
            if($all_post_meta['saswp_recipe_fat_'.$schema_id][0]){                
                $input1['nutrition']['@type']    = 'NutritionInformation';
                $input1['nutrition']['fatContent'] = $all_post_meta['saswp_recipe_fat_'.$schema_id][0];
            }
            if($all_post_meta['saswp_recipe_fiber_'.$schema_id][0]){                
                $input1['nutrition']['@type']    = 'NutritionInformation';
                $input1['nutrition']['fiberContent'] = $all_post_meta['saswp_recipe_fiber_'.$schema_id][0];
            }
            if($all_post_meta['saswp_recipe_sodium_'.$schema_id][0]){                
                $input1['nutrition']['@type']    = 'NutritionInformation';
                $input1['nutrition']['sodiumContent'] = $all_post_meta['saswp_recipe_sodium_'.$schema_id][0];
            }
            if($all_post_meta['saswp_recipe_sugar_'.$schema_id][0]){                
                $input1['nutrition']['@type']    = 'NutritionInformation';
                $input1['nutrition']['sugarContent'] = $all_post_meta['saswp_recipe_sugar_'.$schema_id][0];
            }
            if($all_post_meta['saswp_recipe_carbohydrate_'.$schema_id][0]){                
                $input1['nutrition']['@type']    = 'NutritionInformation';
                $input1['nutrition']['carbohydrateContent'] = $all_post_meta['saswp_recipe_carbohydrate_'.$schema_id][0];
            }
            if($all_post_meta['saswp_recipe_cholesterol_'.$schema_id][0]){                
                $input1['nutrition']['@type']    = 'NutritionInformation';
                $input1['nutrition']['cholesterolContent'] = $all_post_meta['saswp_recipe_cholesterol_'.$schema_id][0];
            }
            if($all_post_meta['saswp_recipe_saturated_fat_'.$schema_id][0]){                
                $input1['nutrition']['@type']    = 'NutritionInformation';
                $input1['nutrition']['saturatedFatContent'] = $all_post_meta['saswp_recipe_saturated_fat_'.$schema_id][0];
            }
            if($all_post_meta['saswp_recipe_unsaturated_fat_'.$schema_id][0]){                
                $input1['nutrition']['@type']    = 'NutritionInformation';
                $input1['nutrition']['unsaturatedFatContent'] = $all_post_meta['saswp_recipe_unsaturated_fat_'.$schema_id][0];
            }
            if($all_post_meta['saswp_recipe_trans_fat_'.$schema_id][0]){                
                $input1['nutrition']['@type']    = 'NutritionInformation';
                $input1['nutrition']['transFatContent'] = $all_post_meta['saswp_recipe_trans_fat_'.$schema_id][0];
            }
            if($all_post_meta['saswp_recipe_serving_size_'.$schema_id][0]){                
                $input1['nutrition']['@type']    = 'NutritionInformation';
                $input1['nutrition']['servingSize'] = $all_post_meta['saswp_recipe_serving_size_'.$schema_id][0];
            }
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
                                                                                          
            $input1 = array(
            '@context'			            => saswp_context_url(),
            '@type'				            => 'Product',
            '@id'                           => get_permalink().'#product',    
            'url'				            => get_permalink(),
            'name'                          => saswp_remove_warnings($all_post_meta, 'saswp_product_schema_name_'.$schema_id, 'saswp_array'),
            'sku'                           => saswp_remove_warnings($all_post_meta, 'saswp_product_schema_sku_'.$schema_id, 'saswp_array'),
            'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_product_schema_description_'.$schema_id, 'saswp_array'),													                       
            'brand'                         => array('@type' => 'Brand',
                                                     'name'  => saswp_remove_warnings($all_post_meta, 'saswp_product_schema_brand_name_'.$schema_id, 'saswp_array'),
                                                    )    
            ); 
           
            if( isset($all_post_meta['saswp_product_schema_brand_url_'.$schema_id][0]) && $all_post_meta['saswp_product_schema_brand_url_'.$schema_id][0] != '' ){
                $input1['brand']['url'] = $all_post_meta['saswp_product_schema_brand_url_'.$schema_id][0];
            }
            
            if( isset($all_post_meta['saswp_product_schema_brand_image_'.$schema_id][0]) && $all_post_meta['saswp_product_schema_brand_image_'.$schema_id][0] != '' ){
                $input1['brand']['image'] = $all_post_meta['saswp_product_schema_brand_image_'.$schema_id][0];
            }
            if( isset($all_post_meta['saswp_product_schema_brand_logo_'.$schema_id][0]) && $all_post_meta['saswp_product_schema_brand_logo_'.$schema_id][0] != '' ){
                $input1['brand']['logo'] = $all_post_meta['saswp_product_schema_brand_logo_'.$schema_id][0];
            }

            if( isset($all_post_meta['saswp_product_schema_id_'.$schema_id][0]) && $all_post_meta['saswp_product_schema_id_'.$schema_id][0] != '' ){
                    $input1['@id'] = $all_post_meta['saswp_product_schema_id_'.$schema_id][0];
            }

            // if( isset($all_post_meta['product_pros_'.$schema_id][0]) && $all_post_meta['product_pros_'.$schema_id][0] != '' ){
            //     $input1['brand']['url'] = $all_post_meta['product_pros_'.$schema_id][0];
            // }
           
            
            $input1 = saswp_get_modified_image('saswp_product_schema_image_'.$schema_id.'_detail', $input1);
            
           
            if( (isset($all_post_meta['saswp_product_schema_price_'.$schema_id][0]) && $all_post_meta['saswp_product_schema_price_'.$schema_id][0]) || (isset($all_post_meta['saswp_product_schema_high_price_'.$schema_id][0]) && isset($all_post_meta['saswp_product_schema_low_price_'.$schema_id][0]) ) ){
                            
                $input1['offers']['@type']           = 'Offer';
                $input1['offers']['availability']    = saswp_remove_warnings($all_post_meta, 'saswp_product_schema_availability_'.$schema_id, 'saswp_array');
                $input1['offers']['itemCondition']   = saswp_remove_warnings($all_post_meta, 'saswp_product_schema_condition_'.$schema_id, 'saswp_array');
                $input1['offers']['price']           = saswp_remove_warnings($all_post_meta, 'saswp_product_schema_price_'.$schema_id, 'saswp_array');
                $input1['offers']['priceCurrency']   = saswp_modify_currency_code(saswp_remove_warnings($all_post_meta, 'saswp_product_schema_currency_'.$schema_id, 'saswp_array'));
                $input1['offers']['url']             = saswp_get_permalink();
                $input1['offers']['priceValidUntil'] = isset($all_post_meta['saswp_product_schema_priceValidUntil_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_product_schema_priceValidUntil_'.$schema_id][0])):'';
            
                if( isset($all_post_meta['saswp_product_schema_high_price_'.$schema_id][0]) && isset($all_post_meta['saswp_product_schema_low_price_'.$schema_id][0]) ){
                    $input1['offers']['@type']           = 'AggregateOffer';
                    $input1['offers']['highPrice']       = $all_post_meta['saswp_product_schema_high_price_'.$schema_id][0];
                    $input1['offers']['lowPrice']        = $all_post_meta['saswp_product_schema_low_price_'.$schema_id][0];

                    if( isset($all_post_meta['saswp_product_schema_offer_count_'.$schema_id][0]) ){
                        $input1['offers']['offerCount'] = $all_post_meta['saswp_product_schema_offer_count_'.$schema_id][0];
                    }

                }

                if(isset($all_post_meta['saswp_product_schema_seller_'.$schema_id])){
                    $input1['offers']['seller']['@type']   = 'Organization';
                    $input1['offers']['seller']['name']    = esc_attr($all_post_meta['saswp_product_schema_seller_'.$schema_id][0]);  
                }

                if(isset($all_post_meta['saswp_product_schema_vat_'.$schema_id])){
                    $input1['offers']['priceSpecification']['@type']                    = 'priceSpecification';
                    $input1['offers']['priceSpecification']['valueAddedTaxIncluded']    = esc_attr($all_post_meta['saswp_product_schema_vat_'.$schema_id][0]);  
                }

                // Changes since version 1.15
                if(isset($all_post_meta['saswp_product_schema_rp_country_code_'.$schema_id][0]) || isset($all_post_meta['saswp_product_schema_rp_category_'.$schema_id][0]) || isset($all_post_meta['saswp_product_schema_rp_return_days_'.$schema_id][0]) || isset($all_post_meta['saswp_product_schema_rp_return_method_'.$schema_id][0]) || isset($all_post_meta['saswp_product_schema_rp_return_fees_'.$schema_id][0])){
                    $input1['offers']['hasMerchantReturnPolicy']['@type'] = 'MerchantReturnPolicy';
                    $input1['offers']['hasMerchantReturnPolicy']['applicableCountry'] = esc_attr($all_post_meta['saswp_product_schema_rp_country_code_'.$schema_id][0]);
                    if(isset($all_post_meta['saswp_product_schema_rp_category_'.$schema_id][0])){
                        $rp_category = array('MerchantReturnFiniteReturnWindow','MerchantReturnNotPermitted','MerchantReturnUnlimitedWindow','MerchantReturnUnspecified');
                        if(in_array($all_post_meta['saswp_product_schema_rp_category_'.$schema_id][0], $rp_category)){
                            $input1['offers']['hasMerchantReturnPolicy']['returnPolicyCategory'] = esc_attr($all_post_meta['saswp_product_schema_rp_category_'.$schema_id][0]);
                        }
                    }
                    if(isset($all_post_meta['saswp_product_schema_rp_return_days_'.$schema_id][0])){
                            $input1['offers']['hasMerchantReturnPolicy']['merchantReturnDays'] = esc_attr($all_post_meta['saswp_product_schema_rp_return_days_'.$schema_id][0]);
                    }
                    if(isset($all_post_meta['saswp_product_schema_rp_return_method_'.$schema_id][0])){
                        $rm_category = array('ReturnAtKiosk','ReturnByMail','ReturnInStore');
                        if(in_array($all_post_meta['saswp_product_schema_rp_return_method_'.$schema_id][0], $rm_category)){
                            $input1['offers']['hasMerchantReturnPolicy']['returnMethod'] = esc_attr($all_post_meta['saswp_product_schema_rp_return_method_'.$schema_id][0]);
                        }
                    }
                    if((isset($all_post_meta['saswp_product_schema_rsf_name_'.$schema_id][0]) && !empty($all_post_meta['saswp_product_schema_rsf_name_'.$schema_id][0])) || (isset($all_post_meta['saswp_product_schema_rsf_value_'.$schema_id][0]) && !empty($all_post_meta['saswp_product_schema_rsf_value_'.$schema_id][0])) || (isset($all_post_meta['saswp_product_schema_rsf_currency_'.$schema_id][0]) && !empty($all_post_meta['saswp_product_schema_rsf_currency_'.$schema_id][0]))){
                        $input1['offers']['hasMerchantReturnPolicy']['returnShippingFeesAmount']['@type'] = 'MonetaryAmount';
                        if(isset($all_post_meta['saswp_product_schema_rsf_name_'.$schema_id][0])){
                            $input1['offers']['hasMerchantReturnPolicy']['returnShippingFeesAmount']['name'] = esc_attr($all_post_meta['saswp_product_schema_rsf_name_'.$schema_id][0]);    
                        }
                        if(isset($all_post_meta['saswp_product_schema_rsf_value_'.$schema_id][0])){
                            $input1['offers']['hasMerchantReturnPolicy']['returnShippingFeesAmount']['value'] = esc_attr($all_post_meta['saswp_product_schema_rsf_value_'.$schema_id][0]);    
                        }
                        if(isset($all_post_meta['saswp_product_schema_rsf_currency_'.$schema_id][0])){
                            $input1['offers']['hasMerchantReturnPolicy']['returnShippingFeesAmount']['currency'] = esc_attr($all_post_meta['saswp_product_schema_rsf_currency_'.$schema_id][0]);    
                        }    
                    }else{
                        if(isset($all_post_meta['saswp_product_schema_rp_return_fees_'.$schema_id][0])){
                            $rf_category = array('FreeReturn','OriginalShippingFees','RestockingFees','ReturnFeesCustomerResponsibility','ReturnShippingFees');
                                $input1['offers']['hasMerchantReturnPolicy']['returnFees'] = esc_attr($all_post_meta['saswp_product_schema_rp_return_fees_'.$schema_id][0]);
                        }   
                    }
                }

                if(isset($all_post_meta['saswp_product_schema_sr_value_'.$schema_id][0])){
                    $input1['offers']['shippingDetails']['@type'] = 'OfferShippingDetails';
                    $input1['offers']['shippingDetails']['shippingRate']['@type'] = 'MonetaryAmount';
                    $input1['offers']['shippingDetails']['shippingRate']['value'] = esc_attr($all_post_meta['saswp_product_schema_sr_value_'.$schema_id][0]);
                    if(isset($all_post_meta['saswp_product_schema_sr_currency'])){
                        $input1['offers']['shippingDetails']['shippingRate']['currency'] = esc_attr($all_post_meta['saswp_product_schema_sr_currency_'.$schema_id][0]);
                    }
                    if(isset($all_post_meta['saswp_product_schema_sr_currency'])){
                        $input1['offers']['shippingDetails']['shippingRate']['currency'] = esc_attr($all_post_meta['saswp_product_schema_sr_currency_'.$schema_id][0]);
                    }
                    if(isset($all_post_meta['saswp_product_schema_sa_locality_'.$schema_id][0]) || isset($all_post_meta['saswp_product_schema_sa_region_'.$schema_id][0]) || isset($all_post_meta['saswp_product_schema_sa_postal_code_'.$schema_id][0]) || isset($all_post_meta['saswp_product_schema_sa_address_'.$schema_id][0]) || isset($all_post_meta['saswp_product_schema_sa_country_'.$schema_id][0])){
                        $input1['offers']['shippingDetails']['shippingDestination']['@type'] = 'DefinedRegion';
                        if(isset($all_post_meta['saswp_product_schema_sa_locality_'.$schema_id][0])){
                            $input1['offers']['shippingDetails']['shippingDestination']['addressLocality'] = esc_attr($all_post_meta['saswp_product_schema_sa_locality_'.$schema_id][0]);
                        }
                        if(isset($all_post_meta['saswp_product_schema_sa_region_'.$schema_id][0])){
                            $input1['offers']['shippingDetails']['shippingDestination']['addressRegion'] = esc_attr($all_post_meta['saswp_product_schema_sa_region_'.$schema_id][0]);
                        }
                        if(isset($all_post_meta['saswp_product_schema_sa_postal_code_'.$schema_id][0])){
                            $input1['offers']['shippingDetails']['shippingDestination']['postalCode'] = esc_attr($all_post_meta['saswp_product_schema_sa_postal_code_'.$schema_id][0]);
                        }
                        if(isset($all_post_meta['saswp_product_schema_sa_address_'.$schema_id][0])){
                            $input1['offers']['shippingDetails']['shippingDestination']['streetAddress'] = esc_attr($all_post_meta['saswp_product_schema_sa_address_'.$schema_id][0]);
                        }
                        if(isset($all_post_meta['saswp_product_schema_sa_country_'.$schema_id][0])){
                            $input1['offers']['shippingDetails']['shippingDestination']['addressCountry'] = esc_attr($all_post_meta['saswp_product_schema_sa_country_'.$schema_id][0]);
                        }
                    }
                    if(isset($all_post_meta['saswp_product_schema_sdh_minval_'.$schema_id][0]) && isset($all_post_meta['saswp_product_schema_sdh_maxval_'.$schema_id][0]) && isset($all_post_meta['saswp_product_schema_sdh_unitcode_'.$schema_id][0])){
                        $input1['offers']['shippingDetails']['deliveryTime']['@type'] = 'ShippingDeliveryTime';
                        $input1['offers']['shippingDetails']['deliveryTime']['handlingTime']['@type'] = 'QuantitativeValue';
                        $input1['offers']['shippingDetails']['deliveryTime']['handlingTime']['minValue'] = esc_attr($all_post_meta['saswp_product_schema_sdh_minval_'.$schema_id][0]);
                        $input1['offers']['shippingDetails']['deliveryTime']['handlingTime']['maxValue'] = esc_attr($all_post_meta['saswp_product_schema_sdh_maxval_'.$schema_id][0]);
                        $input1['offers']['shippingDetails']['deliveryTime']['handlingTime']['unitCode'] = esc_attr($all_post_meta['saswp_product_schema_sdh_unitcode_'.$schema_id][0]);
                    }
                    if(isset($all_post_meta['saswp_product_schema_sdt_minval_'.$schema_id][0]) && isset($all_post_meta['saswp_product_schema_sdt_maxval_'.$schema_id][0]) && isset($all_post_meta['saswp_product_schema_sdt_unitcode_'.$schema_id][0])){
                        $input1['offers']['shippingDetails']['deliveryTime']['transitTime']['@type'] = 'QuantitativeValue';
                        $input1['offers']['shippingDetails']['deliveryTime']['transitTime']['minValue'] = esc_attr($all_post_meta['saswp_product_schema_sdt_minval_'.$schema_id][0]);
                        $input1['offers']['shippingDetails']['deliveryTime']['transitTime']['maxValue'] = esc_attr($all_post_meta['saswp_product_schema_sdt_maxval_'.$schema_id][0]);
                        $input1['offers']['shippingDetails']['deliveryTime']['transitTime']['unitCode'] = esc_attr($all_post_meta['saswp_product_schema_sdt_unitcode_'.$schema_id][0]);
                    }
                }
            }
                                                    
            if(isset($all_post_meta['saswp_product_schema_gtin8_'.$schema_id])){
                $input1['gtin8'] = esc_attr($all_post_meta['saswp_product_schema_gtin8_'.$schema_id][0]);  
            }
            if(isset($all_post_meta['saswp_product_schema_color_'.$schema_id])){
                $input1['color'] = esc_attr($all_post_meta['saswp_product_schema_color_'.$schema_id][0]);  
            }
            if(isset($all_post_meta['saswp_product_schema_gtin13_'.$schema_id])){
                $input1['gtin13'] = esc_attr($all_post_meta['saswp_product_schema_gtin13_'.$schema_id][0]);  
            }
            if(isset($all_post_meta['saswp_product_schema_gtin12_'.$schema_id])){
                $input1['gtin12'] = esc_attr($all_post_meta['saswp_product_schema_gtin12_'.$schema_id][0]);  
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
                                          
                                          $review_fields['@type']           = 'Review';
                                        
                                        if(isset($all_post_meta['product_pros_'.$schema_id][0])){

                                            $review_fields['positiveNotes']['@type'] = 'ItemList';
                                            
                                            $itemList = [];                                                    
                                            foreach(unserialize($all_post_meta['product_pros_'.$schema_id][0]) as $key => $positiveNotes){                                              
                                              
                                                $itemList[$key]['@type'] = 'ListItem';
                                                $itemList[$key]['position'] = 1;
                                                $itemList[$key]['name'] = $positiveNotes['saswp_product_pros_title'];
                                            }
                                            $review_fields['positiveNotes']['itemListElement'] = $itemList;
                                        
                                        }

                                        if(isset($all_post_meta['product_cons_'.$schema_id][0])){

                                            $review_fields['negativeNotes']['@type'] = 'ItemList';
                                            
                                            $itemList = [];                                                      
                                            foreach(unserialize($all_post_meta['product_cons_'.$schema_id][0]) as $key => $positiveNotes){                                              
                                              
                                                $itemList[$key]['@type'] = 'ListItem';
                                                $itemList[$key]['position'] = 1;
                                                $itemList[$key]['name'] = $positiveNotes['saswp_product_cons_title'];
                                            }

                                            $review_fields['negativeNotes']['itemListElement'] = $itemList;
                                        
                                        }

                                          $review_fields['author']['@type'] = 'Person';
                                          $review_fields['author']['name']  = $review['saswp_product_reviews_reviewer_name'] ? esc_attr($review['saswp_product_reviews_reviewer_name']) : 'Anonymous';

                                          if(isset($review['saswp_product_reviews_created_date'])){
                                            $review_fields['datePublished'] = esc_html($review['saswp_product_reviews_created_date']);
                                          }
                                          if(isset($review['saswp_product_reviews_text'])){
                                            $review_fields['description']   = esc_textarea($review['saswp_product_reviews_text']);
                                          }
                                                                                                                                                                        
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
                                          
                                          $review_fields['@type']           = 'Review';
                                          $review_fields['author']['@type'] = 'Person';
                                          $review_fields['author']['name']  = $review['author'] ? esc_attr($review['author']) : 'Anonymous';
                                          $review_fields['datePublished']   = esc_html($review['datePublished']);
                                          $review_fields['description']     = $review['description'];
                                                                                    
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

                if(!isset($input1['review'])){
                    $input1 = saswp_append_fetched_reviews($input1); 
                }
                
    return $input1;
    
}

function saswp_rent_action_schema_markup($schema_id, $schema_post_id, $all_post_meta){
     
    $checkIdPro = ((isset($all_post_meta['saswp_rent_action_id_'.$schema_id][0]) && $all_post_meta['saswp_rent_action_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_rent_action_id_'.$schema_id][0] : '');
       $input1 = array(
        '@context'			=> saswp_context_url(),
        '@type'				=> 'RentAction',
        '@id'               => $checkIdPro,    
        'url'				=> get_permalink(),        
        );

        if(empty($input1['@id'])){
            unset($input1['@id']);
        }

        if(isset($all_post_meta['saswp_rent_action_agent_name_'.$schema_id][0])){
            $input1['agent']['@type'] =    'Person';
            $input1['agent']['name']  =    $all_post_meta['saswp_rent_action_agent_name_'.$schema_id][0];
        }

        if(isset($all_post_meta['saswp_rent_action_land_lord_name_'.$schema_id][0])){
            $input1['landlord']['@type'] =    'Person';
            $input1['landlord']['name']  =    $all_post_meta['saswp_rent_action_land_lord_name_'.$schema_id][0];
        }

        if(isset($all_post_meta['saswp_rent_action_object_name_'.$schema_id][0])){
            $input1['object']['@type'] =    'Residence';
            $input1['object']['name']  =    $all_post_meta['saswp_rent_action_object_name_'.$schema_id][0];
        }

        return $input1;
}

function saswp_real_estate_listing_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    $checkIdPro = ((isset($all_post_meta['saswp_real_estate_listing_id_'.$schema_id][0]) && $all_post_meta['saswp_real_estate_listing_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_real_estate_listing_id_'.$schema_id][0] : '');

    $input1 = array(
        '@context'			=> saswp_context_url(),
        '@type'				=> 'RealEstateListing',
        '@id'                           => $checkIdPro,    
        'url'				=> get_permalink(),
        'name'                          => saswp_remove_warnings($all_post_meta, 'saswp_real_estate_listing_name_'.$schema_id, 'saswp_array'),    
        'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_real_estate_listing_description_'.$schema_id, 'saswp_array'),													                            
        );
         
        if(empty($input1['@id'])){
            unset($input1['@id']);
        }

        $input1['datePosted']           = isset($all_post_meta['saswp_real_estate_listing_date_posted_'.$schema_id][0])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_real_estate_listing_date_posted_'.$schema_id][0])):'';
        
        $input1 = saswp_get_modified_image('saswp_real_estate_listing_image_'.$schema_id.'_detail', $input1);

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
    
    $checkIdPro = ((isset($all_post_meta['saswp_psychological_treatment_id_'.$schema_id][0]) && $all_post_meta['saswp_psychological_treatment_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_psychological_treatment_id_'.$schema_id][0] : '');
        $input1 = array(
        '@context'			=> saswp_context_url(),
        '@type'				=> 'PsychologicalTreatment',
        '@id'               => $checkIdPro,    
        'url'				=> saswp_remove_warnings($all_post_meta, 'saswp_psychological_treatment_url_'.$schema_id, 'saswp_array'),
        'name'              => saswp_remove_warnings($all_post_meta, 'saswp_psychological_treatment_name_'.$schema_id, 'saswp_array'),    
        'description'       => saswp_remove_warnings($all_post_meta, 'saswp_psychological_treatment_description_'.$schema_id, 'saswp_array'),													                            
        );

        if(empty($input1['@id'])){
            unset($input1['@id']);
        }

        $input1 = saswp_get_modified_image('saswp_psychological_treatment_image_'.$schema_id.'_detail', $input1);

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
            
            $mapping_local_sub = SASWP_DIR_NAME . '/core/array-list/local-sub-business.php';

            $post_specific_obj   = include $mapping_local_sub;
                
            if(array_key_exists($business_type, $post_specific_obj)){

                $check_business_type = $post_specific_obj[$business_type];

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
            
            $input1 = array(
            '@context'			=> saswp_context_url(),
            '@type'				=> $local_business ,
            '@id'                           => ((isset($all_post_meta['local_business_id_'.$schema_id][0]) && $all_post_meta['local_business_id_'.$schema_id][0] !='') ? $all_post_meta['local_business_id_'.$schema_id][0] : get_permalink().'#'.strtolower($local_business)),        
            'name'                          => saswp_remove_warnings($all_post_meta, 'local_business_name_'.$schema_id, 'saswp_array'),                                   
            'url'				=> saswp_remove_warnings($all_post_meta, 'local_business_name_url_'.$schema_id, 'saswp_array'),				
            'description'                   => saswp_remove_warnings($all_post_meta, 'local_business_description_'.$schema_id, 'saswp_array'),				               
            'address'                       => array(
                                            "@type"           => "PostalAddress",
                                            "streetAddress"   => saswp_remove_warnings($all_post_meta, 'local_street_address_'.$schema_id, 'saswp_array'),
                                            "addressLocality" => saswp_remove_warnings($all_post_meta, 'local_city_'.$schema_id, 'saswp_array'),
                                            "addressRegion"   => saswp_remove_warnings($all_post_meta, 'local_state_'.$schema_id, 'saswp_array'),
                                            "postalCode"      => saswp_remove_warnings($all_post_meta, 'local_postal_code_'.$schema_id, 'saswp_array'),                                                                                                                                  
                                            "addressCountry"      => saswp_remove_warnings($all_post_meta, 'local_country_'.$schema_id, 'saswp_array'),                                                                                                                                  
                                             ),	
            'telephone'                   => saswp_remove_warnings($all_post_meta, 'local_phone_'.$schema_id, 'saswp_array'),
            'openingHours'                => $operation_days,                                                                                                     
            );

                $input1 = saswp_get_modified_image('local_business_logo_'.$schema_id.'_detail', $input1);

                if(isset($all_post_meta['local_business_logo_'.$schema_id][0]) && $all_post_meta['local_business_logo_'.$schema_id][0] !='' ){
                    $input1['image'] = $all_post_meta['local_business_logo_'.$schema_id][0];   
                }
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
                    $input1['areaServed'] = saswp_explode_comma_seprated( $all_post_meta['local_area_served_'.$schema_id][0], 'Place' );                                                       
                }

                if(isset($all_post_meta['local_business_founder_'.$schema_id][0])){                    
                    $input1['founder'] = saswp_explode_comma_seprated( $all_post_meta['local_business_founder_'.$schema_id][0], 'Person' );
                }
                if(isset($all_post_meta['local_business_employee_'.$schema_id][0])){                    
                    $input1['employee'] = saswp_explode_comma_seprated( $all_post_meta['local_business_employee_'.$schema_id][0], 'Person' );
                }

                if(isset($all_post_meta['local_service_offered_name_'.$schema_id][0])){                    
                    $input1['makesOffer']['@type'] = 'Offer';
                    $input1['makesOffer']['@id']   = '#service';
                    $input1['makesOffer']['itemOffered']['@type'] = 'Service';
                    $input1['makesOffer']['itemOffered']['name'] = $all_post_meta['local_service_offered_name_'.$schema_id][0];                     
                    if(isset($all_post_meta['local_service_offered_url_'.$schema_id][0])){                                             
                        $input1['makesOffer']['itemOffered']['url']  = $all_post_meta['local_service_offered_url_'.$schema_id][0]; 
                    }
                    $input1['makesOffer']['itemOffered']['areaServed'] = saswp_explode_comma_seprated( $all_post_meta['local_area_served_'.$schema_id][0], 'Place' );                                                       
                    
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
                if(isset($all_post_meta['local_threads_'.$schema_id][0]) && $all_post_meta['local_threads_'.$schema_id][0] !=''){
                  $local_social[] = esc_url($all_post_meta['local_threads_'.$schema_id][0]);   
                }
                if(isset($all_post_meta['local_mastodon_'.$schema_id][0]) && $all_post_meta['local_mastodon_'.$schema_id][0] !=''){
                  $local_social[] = esc_url($all_post_meta['local_mastodon_'.$schema_id][0]);   
                }
                if(isset($all_post_meta['local_vibehut_'.$schema_id][0]) && $all_post_meta['local_vibehut_'.$schema_id][0] !=''){
                  $local_social[] = esc_url($all_post_meta['local_vibehut_'.$schema_id][0]);   
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

                if( (isset($all_post_meta['local_latitude_'.$schema_id][0]) && $all_post_meta['local_latitude_'.$schema_id][0] != '') && (isset($all_post_meta['local_longitude_'.$schema_id][0])  && $all_post_meta['local_longitude_'.$schema_id][0] !='' )  ){

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

                if(!isset($input1['review'])){
                    $input1 = saswp_append_fetched_reviews($input1); 
                }
                
                if(isset($all_post_meta['local_rating_automate_'.$schema_id][0]) && $all_post_meta['local_google_place_id_'.$schema_id][0]){

                    if(function_exists('saswp_automated_aggregate_rating')){
                        $input1 = saswp_automated_aggregate_rating($input1, $all_post_meta['local_google_place_id_'.$schema_id][0]);
                    }
                                        
                }
                

    return $input1;
}

function saswp_organization_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
            $input1 = array();
            
            $checkIdPro = ((isset($all_post_meta['saswp_organization_id_'.$schema_id][0]) && $all_post_meta['saswp_organization_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_organization_id_'.$schema_id][0] : '');
           
            $input1['@context']                     = saswp_context_url();
            $input1['@type']                        = 'Organization';
            if($checkIdPro){
                $input1['@id']                      = $checkIdPro;  
            } 
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
          $input1['address']['postalCode']        = saswp_remove_warnings($all_post_meta, 'saswp_organization_postal_code_'.$schema_id, 'saswp_array');
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
          
        $sameas = array();

        if(isset($all_post_meta['saswp_organization_facebook_'.$schema_id][0])){
            $sameas[]        = $all_post_meta['saswp_organization_facebook_'.$schema_id][0];
        }
        if(isset($all_post_meta['saswp_organization_twitter_'.$schema_id][0])){
            $sameas[]        = $all_post_meta['saswp_organization_twitter_'.$schema_id][0];
        }
        if(isset($all_post_meta['saswp_organization_linkedin_'.$schema_id][0])){
            $sameas[]        = $all_post_meta['saswp_organization_linkedin_'.$schema_id][0];
        }
        if(isset($all_post_meta['saswp_organization_threads_'.$schema_id][0])){
            $sameas[]        = $all_post_meta['saswp_organization_threads_'.$schema_id][0];
        }
        if(isset($all_post_meta['saswp_organization_mastodon_'.$schema_id][0])){
            $sameas[]        = $all_post_meta['saswp_organization_mastodon_'.$schema_id][0];
        }
        if(isset($all_post_meta['saswp_organization_vibehut_'.$schema_id][0])){
            $sameas[]        = $all_post_meta['saswp_organization_vibehut_'.$schema_id][0];
        }
        if($sameas){
            $input1['sameAs'] = $sameas;
        }                    
        return $input1;
}

function saswp_project_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
            $input1 = array();

            $checkIdPro = ((isset($all_post_meta['saswp_project_id_'.$schema_id][0]) && $all_post_meta['saswp_project_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_project_id_'.$schema_id][0] : '');
        
            $input1['@context']                     = saswp_context_url();
            $input1['@type']                        = 'Project';
            if($checkIdPro){
                $input1['@id']                      = $checkIdPro;  
            } 
            $input1['name']                         = saswp_remove_warnings($all_post_meta, 'saswp_project_name_'.$schema_id, 'saswp_array');
            $input1['url']                          = saswp_remove_warnings($all_post_meta, 'saswp_project_url_'.$schema_id, 'saswp_array');                            
            $input1['description']                  = saswp_remove_warnings($all_post_meta, 'saswp_project_description_'.$schema_id, 'saswp_array');
        
            $howto_image = get_post_meta( get_the_ID(), 'saswp_project_logo_'.$schema_id.'_detail',true); 
            
        if(!(empty($howto_image))){

            $input1['logo']['@type']        = 'ImageObject';
            $input1['logo']['url']          = isset($howto_image['thumbnail']) ? esc_url($howto_image['thumbnail']):'';
            $input1['logo']['height']       = isset($howto_image['width'])     ? esc_attr($howto_image['width'])   :'';
            $input1['logo']['width']        = isset($howto_image['height'])    ? esc_attr($howto_image['height'])  :'';

        }
        
        $input1['address']['@type']             = 'PostalAddress';
        $input1['address']['streetAddress']     = saswp_remove_warnings($all_post_meta, 'saswp_project_street_address_'.$schema_id, 'saswp_array');
        $input1['address']['addressCountry']    = saswp_remove_warnings($all_post_meta, 'saswp_project_country_'.$schema_id, 'saswp_array');
        $input1['address']['addressLocality']   = saswp_remove_warnings($all_post_meta, 'saswp_project_city_'.$schema_id, 'saswp_array');
        $input1['address']['addressRegion']     = saswp_remove_warnings($all_post_meta, 'saswp_project_state_'.$schema_id, 'saswp_array');
        $input1['address']['postalCode']        = saswp_remove_warnings($all_post_meta, 'saswp_project_postal_code_'.$schema_id, 'saswp_array');
        $input1['address']['telephone']         = saswp_remove_warnings($all_post_meta, 'saswp_project_telephone_'.$schema_id, 'saswp_array');                                                        
        $input1['address']['email']             = saswp_remove_warnings($all_post_meta, 'saswp_project_email_'.$schema_id, 'saswp_array');                                                        
        
        if( isset($all_post_meta['saswp_project_duns_'.$schema_id][0]) ){
            $input1['duns']         = $all_post_meta['saswp_project_duns_'.$schema_id][0];            
        }
        if( isset($all_post_meta['saswp_project_founder_'.$schema_id][0]) ){
            $input1['founder']         = $all_post_meta['saswp_project_founder_'.$schema_id][0];            
        }
        if( isset($all_post_meta['saswp_project_founding_date_'.$schema_id][0]) ){
            $input1['foundingDate']         = saswp_format_date_time($all_post_meta['saswp_project_founding_date_'.$schema_id][0]);            
        }
        if( isset($all_post_meta['saswp_project_qualifications_'.$schema_id][0]) ){
            $input1['hasCredential']         = $all_post_meta['saswp_project_qualifications_'.$schema_id][0];            
        }
        if( isset($all_post_meta['saswp_project_knows_about_'.$schema_id][0]) ){
            $input1['knowsAbout']         = $all_post_meta['saswp_project_knows_about_'.$schema_id][0];            
        }
        if( isset($all_post_meta['saswp_project_member_of_'.$schema_id][0]) ){
            $input1['memberOf']         = $all_post_meta['saswp_project_member_of_'.$schema_id][0];            
        }
        if( isset($all_post_meta['saswp_project_parent_project_'.$schema_id][0]) ){
            $input1['parentProject']         = $all_post_meta['saswp_project_parent_project_'.$schema_id][0];            
        }          
        if(isset($all_post_meta['saswp_project_enable_rating_'.$schema_id]) && isset($all_post_meta['saswp_project_rating_value_'.$schema_id]) && isset($all_post_meta['saswp_project_rating_count_'.$schema_id])){
                $input1['aggregateRating']['@type']         = 'aggregateRating';
                $input1['aggregateRating']['ratingValue']   = $all_post_meta['saswp_project_rating_value_'.$schema_id][0];
                $input1['aggregateRating']['ratingCount']   = $all_post_meta['saswp_project_rating_count_'.$schema_id][0];                                
        }          
        
        $sameas = array();

        if(isset($all_post_meta['saswp_project_facebook_'.$schema_id][0])){
            $sameas[]        = $all_post_meta['saswp_project_facebook_'.$schema_id][0];
        }
        if(isset($all_post_meta['saswp_project_twitter_'.$schema_id][0])){
            $sameas[]        = $all_post_meta['saswp_project_twitter_'.$schema_id][0];
        }
        if(isset($all_post_meta['saswp_project_linkedin_'.$schema_id][0])){
            $sameas[]        = $all_post_meta['saswp_project_linkedin_'.$schema_id][0];
        }
        if(isset($all_post_meta['saswp_project_threads_'.$schema_id][0])){
            $sameas[]        = $all_post_meta['saswp_project_threads_'.$schema_id][0];
        }
        if(isset($all_post_meta['saswp_project_mastodon_'.$schema_id][0])){
            $sameas[]        = $all_post_meta['saswp_project_mastodon_'.$schema_id][0];
        }
        if(isset($all_post_meta['saswp_project_vibehut_'.$schema_id][0])){
            $sameas[]        = $all_post_meta['saswp_project_vibehut_'.$schema_id][0];
        }
        if($sameas){
            $input1['sameAs'] = $sameas;
        }                         
        return $input1;
}

function saswp_hotel_room_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
                $input1 = array();

                $checkIdPro = ((isset($all_post_meta['saswp_hotelroom_hotel_id_'.$schema_id][0]) && $all_post_meta['saswp_hotelroom_hotel_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_hotelroom_hotel_id_'.$schema_id][0] : '');
    
                $input1['@context']                     = saswp_context_url();
                $input1['@type']                        = 'Hotel';
                if($checkIdPro){
                    $input1['@id']                      = $checkIdPro;  
                } 
                
                if(isset($all_post_meta['saswp_hotelroom_hotel_name_'.$schema_id][0])){
                    $input1['name'] =    $all_post_meta['saswp_hotelroom_hotel_name_'.$schema_id][0];
                }
                if(isset($all_post_meta['saswp_hotelroom_hotel_image_'.$schema_id][0])){
                    $input1['image'] =    $all_post_meta['saswp_hotelroom_hotel_image_'.$schema_id][0];
                }
                if(isset($all_post_meta['saswp_hotelroom_hotel_description_'.$schema_id][0])){
                    $input1['description'] =    $all_post_meta['saswp_hotelroom_hotel_description_'.$schema_id][0];
                }
                if(isset($all_post_meta['saswp_hotelroom_hotel_price_range_'.$schema_id][0])){
                    $input1['priceRange'] =    $all_post_meta['saswp_hotelroom_hotel_price_range_'.$schema_id][0];
                }
                if(isset($all_post_meta['saswp_hotelroom_hotel_telephone_'.$schema_id][0])){
                    $input1['telephone'] =    $all_post_meta['saswp_hotelroom_hotel_telephone_'.$schema_id][0];
                }

                if(isset($all_post_meta['saswp_hotelroom_hotel_streetaddress_'.$schema_id][0])){
                    $input1['address']['streetAddress'] =    $all_post_meta['saswp_hotelroom_hotel_streetaddress_'.$schema_id][0];
                }                    
                if(isset($all_post_meta['saswp_hotelroom_hotel_locality_'.$schema_id][0])){
                    $input1['address']['addressLocality'] =    $all_post_meta['saswp_hotelroom_hotel_locality_'.$schema_id][0];
                }
                if(isset($all_post_meta['saswp_hotelroom_hotel_region_'.$schema_id][0])){
                    $input1['address']['addressRegion'] =    $all_post_meta['saswp_hotelroom_hotel_region_'.$schema_id][0];
                }
                if(isset($all_post_meta['saswp_hotelroom_hotel_country_'.$schema_id][0])){
                    $input1['address']['addressCountry'] =    $all_post_meta['saswp_hotelroom_hotel_country_'.$schema_id][0];
                }
                if(isset($all_post_meta['saswp_hotelroom_hotel_postalcode_'.$schema_id][0])){
                    $input1['address']['postalCode'] =    $all_post_meta['saswp_hotelroom_hotel_postalcode_'.$schema_id][0];
                }

                if(isset($all_post_meta['saswp_hotelroom_name_'.$schema_id][0])){
                    $input1['containsPlace']['@type'] = 'HotelRoom'; 
                    $input1['containsPlace']['name'] =    $all_post_meta['saswp_hotelroom_name_'.$schema_id][0];
                }
                if(isset($all_post_meta['saswp_hotelroom_description_'.$schema_id][0])){
                    $input1['containsPlace']['@type'] = 'HotelRoom'; 
                    $input1['containsPlace']['description'] =    $all_post_meta['saswp_hotelroom_description_'.$schema_id][0];
                }
                if(isset($all_post_meta['saswp_hotelroom_image_'.$schema_id][0])){
                    $input1['containsPlace']['@type'] = 'HotelRoom'; 
                    $input1['containsPlace']['image'] =    $all_post_meta['saswp_hotelroom_image_'.$schema_id][0];
                }

                if(isset($all_post_meta['saswp_hotelroom_offer_name_'.$schema_id][0])){
                    $input1['makesOffer']['@type'] = 'offer'; 
                    $input1['makesOffer']['name'] =    $all_post_meta['saswp_hotelroom_offer_name_'.$schema_id][0];
                }

                if(isset($all_post_meta['saswp_hotelroom_offer_description_'.$schema_id][0])){
                    $input1['makesOffer']['@type'] = 'offer'; 
                    $input1['makesOffer']['description'] =    $all_post_meta['saswp_hotelroom_offer_description_'.$schema_id][0];
                }

                if(isset($all_post_meta['saswp_hotelroom_offer_price_'.$schema_id][0]) && isset($all_post_meta['saswp_hotelroom_offer_price_currency_'.$schema_id][0])){

                    $input1['makesOffer']['@type']                       = 'offer';
                    $input1['makesOffer']['priceSpecification']['@type'] = 'UnitPriceSpecification'; 

                    $input1['makesOffer']['priceSpecification']['priceCurrency']  = $all_post_meta['saswp_hotelroom_offer_price_currency_'.$schema_id][0]; 
                    $input1['makesOffer']['priceSpecification']['price']          = $all_post_meta['saswp_hotelroom_offer_price_'.$schema_id][0]; 
                    $input1['makesOffer']['priceSpecification']['unitCode']       = $all_post_meta['saswp_hotelroom_offer_unitcode_'.$schema_id][0]; 
                    $input1['makesOffer']['priceSpecification']['validThrough']   = $all_post_meta['saswp_hotelroom_offer_validthrough_'.$schema_id][0]; 
                                                
                }
                                                
                return $input1;
}

function saswp_educational_occupational_credential_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
            $input1 = array();
            $checkIdPro = ((isset($all_post_meta['saswp_eoc_id_'.$schema_id][0]) && $all_post_meta['saswp_eoc_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_eoc_id_'.$schema_id][0] : '');

            $input1['@context']                     = saswp_context_url();
            $input1['@type']                        = 'EducationalOccupationalCredential';
            if($checkIdPro){
                $input1['@id']                      = $checkIdPro;  
            }
            
            if( isset($all_post_meta['saswp_eoc_additional_type_'.$schema_id][0]) ){
                $input1['additionalType']         = $all_post_meta['saswp_eoc_additional_type_'.$schema_id][0];            
            }
            if( isset($all_post_meta['saswp_eoc_name_'.$schema_id][0]) ){
                $input1['name']         = $all_post_meta['saswp_eoc_name_'.$schema_id][0];            
            }
            if( isset($all_post_meta['saswp_eoc_alt_name_'.$schema_id][0]) ){
                $input1['alternateName']         = $all_post_meta['saswp_eoc_alt_name_'.$schema_id][0];            
            }
            if( isset($all_post_meta['saswp_eoc_description_'.$schema_id][0]) ){
                $input1['description']         = $all_post_meta['saswp_eoc_description_'.$schema_id][0];            
            }

            if( isset($all_post_meta['saswp_eoc_e_lavel_name_'.$schema_id][0]) ){
                $input1['educationalLevel']['@type']                     = 'DefinedTerm';
                $input1['educationalLevel']['name']                      = $all_post_meta['saswp_eoc_e_lavel_name_'.$schema_id][0];            
                $input1['educationalLevel']['inDefinedTermSet']          = $all_post_meta['saswp_eoc_e_lavel_definedtermset_'.$schema_id][0];            
            }

            if( isset($all_post_meta['saswp_eoc_c_category_name_'.$schema_id][0]) ){
                $input1['credentialCategory']['@type']                  = 'DefinedTerm';
                $input1['credentialCategory']['name']                   = $all_post_meta['saswp_eoc_c_category_name_'.$schema_id][0];            
                $input1['credentialCategory']['inDefinedTermSet']       = $all_post_meta['saswp_eoc_c_category_definedtermset_'.$schema_id][0];            
                $input1['credentialCategory']['termCode']               = $all_post_meta['saswp_eoc_c_category_term_code_'.$schema_id][0];            
            }

            if( isset($all_post_meta['saswp_eoc_c_required_name_'.$schema_id][0]) ){
                $input1['competencyRequired']['@type']                  = 'DefinedTerm';
                $input1['competencyRequired']['name']                   = $all_post_meta['saswp_eoc_c_required_name_'.$schema_id][0];            
                $input1['competencyRequired']['inDefinedTermSet']       = $all_post_meta['saswp_eoc_c_required_definedtermset_'.$schema_id][0];            
                $input1['competencyRequired']['termCode']               = $all_post_meta['saswp_eoc_c_required_term_code_'.$schema_id][0];            
                $input1['competencyRequired']['url']                    = $all_post_meta['saswp_eoc_c_required_url_'.$schema_id][0];            
            }
                            
        return $input1;
}

function saswp_video_game_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
            $input1 = array();
            $checkIdPro = ((isset($all_post_meta['saswp_vg_schema_id_'.$schema_id][0]) && $all_post_meta['saswp_vg_schema_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_vg_schema_id_'.$schema_id][0] : '');
            
            $input1['@context']                     = saswp_context_url();
            $input1['@type']                        = 'VideoGame';
            if($checkIdPro){
                $input1['@id']                      = $checkIdPro;  
            } 
            $input1['name']                         = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_name_'.$schema_id, 'saswp_array');
            $input1['url']                          = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_url_'.$schema_id, 'saswp_array');                            
            $input1['description']                  = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_description_'.$schema_id, 'saswp_array');

            $input1 = saswp_get_modified_image('saswp_vg_schema_image_'.$schema_id.'_detail', $input1);
            
            $input1['operatingSystem']          = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_operating_system_'.$schema_id, 'saswp_array');
            $input1['applicationCategory']      = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_application_category_'.$schema_id, 'saswp_array');

            $input1['author']['@type']          = 'Organization';

            if(isset($all_post_meta['saswp_vg_schema_author_type_'.$schema_id][0])){
                $input1['author']['@type']          = $all_post_meta['saswp_vg_schema_author_type_'.$schema_id][0];
            }

            $input1['author']['name']           = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_author_name_'.$schema_id, 'saswp_array');

            if(isset($all_post_meta['saswp_vg_schema_price_'.$schema_id][0]) && $all_post_meta['saswp_vg_schema_price_'.$schema_id][0] != ''){

                $input1['offers']['@type']         = 'Offer';
                $input1['offers']['price']         = $all_post_meta['saswp_vg_schema_price_'.$schema_id][0];
                
                if(!empty($all_post_meta['saswp_vg_schema_price_currency_'.$schema_id][0])){
                    $input1['offers']['priceCurrency'] = $all_post_meta['saswp_vg_schema_price_currency_'.$schema_id][0];                    
                }

                if(!empty($all_post_meta['saswp_vg_schema_price_availability_'.$schema_id][0])){                    
                    $input1['offers']['availability']  = $all_post_meta['saswp_vg_schema_price_availability_'.$schema_id][0];
                }

            }
            
            $input1['publisher']                = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_publisher_'.$schema_id, 'saswp_array');
            $input1['genre']                    = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_genre_'.$schema_id, 'saswp_array');
            $input1['processorRequirements']    = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_processor_requirements_'.$schema_id, 'saswp_array');
            $input1['memoryRequirements']       = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_memory_requirements_'.$schema_id, 'saswp_array');
            $input1['storageRequirements']      = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_storage_requirements_'.$schema_id, 'saswp_array');
            $input1['gamePlatform']             = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_game_platform_'.$schema_id, 'saswp_array');
            $input1['cheatCode']                = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_cheat_code_'.$schema_id, 'saswp_array');
            $input1['fileSize']                 = saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_file_size_'.$schema_id, 'saswp_array');
                        
            if( saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_enable_rating_'.$schema_id, 'saswp_array') == 1 && saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_rating_'.$schema_id, 'saswp_array') && saswp_remove_warnings($all_post_meta, 'saswp_vg_schema_review_count_'.$schema_id, 'saswp_array')){
                                            
                        if($all_post_meta['saswp_vg_schema_rating_'.$schema_id][0] > 5){
                            $input1['aggregateRating']['@type']         = 'aggregateRating';
                            $input1['aggregateRating']['worstRating']   =   0;
                            $input1['aggregateRating']['bestRating']    =   100;
                            $input1['aggregateRating']['ratingValue']   = $all_post_meta['saswp_vg_schema_rating_'.$schema_id][0];
                            $input1['aggregateRating']['ratingCount']   = $all_post_meta['saswp_vg_schema_review_count_'.$schema_id][0];
                        }else{
                            $input1['aggregateRating']['@type']         = 'aggregateRating';                        
                            $input1['aggregateRating']['ratingValue']   = $all_post_meta['saswp_vg_schema_rating_'.$schema_id][0];
                            $input1['aggregateRating']['reviewCount']   = $all_post_meta['saswp_vg_schema_review_count_'.$schema_id][0];
                        }            
                                
            }
    
    return $input1;
}

function saswp_music_playlist_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
            $input1 = array();
            $checkIdPro = ((isset($all_post_meta['saswp_music_playlist_id_'.$schema_id][0]) && $all_post_meta['saswp_music_playlist_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_music_playlist_id_'.$schema_id][0] : '');



            $input1['@context']              = saswp_context_url();
            $input1['@type']                 = 'MusicPlaylist';
            if($checkIdPro){
                $input1['@id']               = $checkIdPro;  
            }      
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
            $checkIdPro = ((isset($all_post_meta['saswp_music_composition_id_'.$schema_id][0]) && $all_post_meta['saswp_music_composition_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_music_composition_id_'.$schema_id][0] : '');


            $input1['@context']              = saswp_context_url();
            $input1['@type']                 = 'MusicComposition';
            if($checkIdPro){
                $input1['@id']               = $checkIdPro;  
            }
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
                                    
            $input1 = saswp_get_modified_image('saswp_music_composition_image_'.$schema_id.'_detail', $input1);

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
        
        $input1['@context']              = saswp_context_url();
        $input1['@type']                 = 'Person';

        if(isset($all_post_meta['saswp_person_schema_id_'.$schema_id][0]) && $all_post_meta['saswp_person_schema_id_'.$schema_id][0] != ''){
            $input1['@id']                   = $all_post_meta['saswp_person_schema_id_'.$schema_id][0];
        }else{
            $input1['@id']                   = get_permalink().'#Person';
        }
        
        $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_person_schema_url_'.$schema_id, 'saswp_array');                            
        $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_person_schema_name_'.$schema_id, 'saswp_array');                                                        
        $input1['familyName']            = saswp_remove_warnings($all_post_meta, 'saswp_person_schema_family_name_'.$schema_id, 'saswp_array');                                                        
        $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_person_schema_description_'.$schema_id, 'saswp_array');                                                        
        $input1['gender']                = saswp_remove_warnings($all_post_meta, 'saswp_person_schema_gender_'.$schema_id, 'saswp_array');                                                        
        $input1['birthDate']             = isset($all_post_meta['saswp_person_schema_date_of_birth_'.$schema_id][0])&& $all_post_meta['saswp_person_schema_date_of_birth_'.$schema_id][0] !='' ? date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_person_schema_date_of_birth_'.$schema_id][0])):'';
        $input1['deathDate']             = isset($all_post_meta['saswp_person_schema_date_of_death_'.$schema_id][0])&& $all_post_meta['saswp_person_schema_date_of_death_'.$schema_id][0] !='' ? date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_person_schema_date_of_death_'.$schema_id][0])):'';
        $input1['nationality']           = saswp_remove_warnings($all_post_meta, 'saswp_person_schema_nationality_'.$schema_id, 'saswp_array');                                                        
        $input1['jobTitle']              = saswp_remove_warnings($all_post_meta, 'saswp_person_schema_job_title_'.$schema_id, 'saswp_array');                                                        

        if(isset($all_post_meta['saswp_person_schema_company_'.$schema_id][0])){
            $input1['worksFor']['@type']       = 'Organization';
            $input1['worksFor']['name']        = $all_post_meta['saswp_person_schema_company_'.$schema_id][0];
            if(isset($all_post_meta['saswp_person_schema_website_'.$schema_id][0])){
                $input1['worksFor']['url']     = $all_post_meta['saswp_person_schema_website_'.$schema_id][0];
            }
        }

        $input1['address']['@type']             = 'PostalAddress';
        $input1['address']['streetAddress']     = saswp_remove_warnings($all_post_meta, 'saswp_person_schema_street_address_'.$schema_id, 'saswp_array');
        $input1['address']['addressCountry']    = saswp_remove_warnings($all_post_meta, 'saswp_person_schema_country_'.$schema_id, 'saswp_array');
        $input1['address']['addressLocality']   = saswp_remove_warnings($all_post_meta, 'saswp_person_schema_locality_'.$schema_id, 'saswp_array');
        $input1['address']['addressRegion']     = saswp_remove_warnings($all_post_meta, 'saswp_person_schema_region_'.$schema_id, 'saswp_array');
        $input1['address']['postalCode']        = saswp_remove_warnings($all_post_meta, 'saswp_person_schema_postal_code_'.$schema_id, 'saswp_array');

        $input1['telephone']                    = saswp_remove_warnings($all_post_meta, 'saswp_person_schema_telephone_'.$schema_id, 'saswp_array');                                                        
        $input1['email']                        = saswp_remove_warnings($all_post_meta, 'saswp_person_schema_email_'.$schema_id, 'saswp_array');                                                        

        $input1 = saswp_get_modified_image('saswp_trip_schema_image_'.$schema_id.'_detail', $input1);

        if(isset($all_post_meta['saswp_person_schema_spouse_'.$schema_id][0])){
            $input1['spouse']['@type']       =  'Person';
            $input1['spouse']['name']        = $all_post_meta['saswp_person_schema_spouse_'.$schema_id][0];
        }        

        if(isset($all_post_meta['saswp_person_schema_b_street_address_'.$schema_id])){
            $input1['homeLocation']['@type'] = 'Place';
            $input1['homeLocation']['address']['streetAddress'] =    $all_post_meta['saswp_person_schema_b_street_address_'.$schema_id];
        }
        if(isset($all_post_meta['saswp_person_schema_b_locality_'.$schema_id])){
            $input1['homeLocation']['@type'] = 'Place';
            $input1['homeLocation']['address']['addressLocality'] =    $all_post_meta['saswp_person_schema_b_locality_'.$schema_id];
        }
        if(isset($all_post_meta['saswp_person_schema_b_region_'.$schema_id])){
            $input1['homeLocation']['@type'] = 'Place';
            $input1['homeLocation']['address']['addressRegion'] =    $all_post_meta['saswp_person_schema_b_region_'.$schema_id];
        }
        if(isset($all_post_meta['saswp_person_schema_b_postal_code_'.$schema_id])){
            $input1['homeLocation']['@type'] = 'Place';
            $input1['homeLocation']['address']['postalCode']  =    $all_post_meta['saswp_person_schema_b_postal_code_'.$schema_id];
        }
        if(isset($all_post_meta['saswp_person_schema_b_country_'.$schema_id])){
            $input1['homeLocation']['@type'] = 'Place';
            $input1['homeLocation']['address']['addressCountry'] =    $all_post_meta['saswp_person_schema_b_country_'.$schema_id];
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
        if(isset($all_post_meta['saswp_person_schema_youtube_'.$schema_id][0])){
            $sameas[]        = $all_post_meta['saswp_person_schema_youtube_'.$schema_id][0];
        }
        if(isset($all_post_meta['saswp_person_schema_instagram_'.$schema_id][0])){
            $sameas[]        = $all_post_meta['saswp_person_schema_instagram_'.$schema_id][0];
        }
        if(isset($all_post_meta['saswp_person_schema_snapchat_'.$schema_id][0])){
            $sameas[]        = $all_post_meta['saswp_person_schema_snapchat_'.$schema_id][0];
        }
        if(isset($all_post_meta['saswp_person_schema_threads_'.$schema_id][0])){
            $sameas[]        = $all_post_meta['saswp_person_schema_threads_'.$schema_id][0];
        }
        if(isset($all_post_meta['saswp_person_schema_mastodon_'.$schema_id][0])){
            $sameas[]        = $all_post_meta['saswp_person_schema_mastodon_'.$schema_id][0];
        }
        if(isset($all_post_meta['saswp_person_schema_vibehut_'.$schema_id][0])){
            $sameas[]        = $all_post_meta['saswp_person_schema_vibehut_'.$schema_id][0];
        }
        if($sameas){
            $input1['sameAs'] = $sameas;
        }

        if(isset($all_post_meta['saswp_person_schema_occupation_name_'.$schema_id][0]) && $all_post_meta['saswp_person_schema_occupation_name_'.$schema_id][0] != ''){
            $input1['hasOccupation']['name'] =    $all_post_meta['saswp_person_schema_occupation_name_'.$schema_id][0];
        }
        if(isset($all_post_meta['saswp_person_schema_occupation_description_'.$schema_id][0]) && $all_post_meta['saswp_person_schema_occupation_description_'.$schema_id][0] != ''){
            $input1['hasOccupation']['description'] =    $all_post_meta['saswp_person_schema_occupation_description_'.$schema_id][0];
        }
        if(isset($all_post_meta['saswp_person_schema_occupation_city_'.$schema_id][0]) && $all_post_meta['saswp_person_schema_occupation_city_'.$schema_id][0] != ''){
            $input1['hasOccupation']['occupationLocation']['@type'] = 'City'; 
            $input1['hasOccupation']['occupationLocation']['name']  =    $all_post_meta['saswp_person_schema_occupation_city_'.$schema_id][0];
        }
        if(isset($all_post_meta['saswp_person_schema_estimated_salary_'.$schema_id][0]) && $all_post_meta['saswp_person_schema_estimated_salary_'.$schema_id][0] != ''){
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
        if(isset( $all_post_meta['saswp_person_schema_salary_last_reviewed_'.$schema_id][0] ) && $all_post_meta['saswp_person_schema_salary_last_reviewed_'.$schema_id][0] != '' ){
            $input1['hasOccupation']['mainEntityOfPage']['@type']         = 'WebPage'; 
            $input1['hasOccupation']['mainEntityOfPage']['lastReviewed']  =    saswp_format_date_time($all_post_meta['saswp_person_schema_salary_last_reviewed_'.$schema_id][0]);
        }

        if(!empty($all_post_meta['saswp_person_schema_alternate_name_'.$schema_id][0])){
            $input1['alternateName'] = $all_post_meta['saswp_person_schema_alternate_name_'.$schema_id][0];
        }
        if(!empty($all_post_meta['saswp_person_schema_additional_name_'.$schema_id][0])){
            $input1['additionalName'] = $all_post_meta['saswp_person_schema_additional_name_'.$schema_id][0];
        }
        if(!empty($all_post_meta['saswp_person_schema_given_name_'.$schema_id][0])){
            $input1['givenName'] = $all_post_meta['saswp_person_schema_given_name_'.$schema_id][0];
        }
        if(!empty($all_post_meta['saswp_person_schema_parent_'.$schema_id][0])){
            $input1['parent'] = $all_post_meta['saswp_person_schema_parent_'.$schema_id][0];
        }
        if(!empty($all_post_meta['saswp_person_schema_sibling_'.$schema_id][0])){
            $input1['sibling'] = $all_post_meta['saswp_person_schema_sibling_'.$schema_id][0];
        }
        if(!empty($all_post_meta['saswp_person_schema_colleague_'.$schema_id][0])){
            $input1['colleague'] = $all_post_meta['saswp_person_schema_colleague_'.$schema_id][0];
        }
        if(!empty($all_post_meta['saswp_person_schema_main_entity_of_page_'.$schema_id][0])){
            $input1['mainEntityOfPage'] = $all_post_meta['saswp_person_schema_main_entity_of_page_'.$schema_id][0];
        }
        if(!empty($all_post_meta['saswp_person_schema_sponsor_'.$schema_id][0])){
            $input1['sponsor'] = $all_post_meta['saswp_person_schema_sponsor_'.$schema_id][0];
        }
        if(!empty($all_post_meta['saswp_person_schema_seeks_'.$schema_id][0])){
            $input1['seeks'] = $all_post_meta['saswp_person_schema_seeks_'.$schema_id][0];
        }        
        if(!empty($all_post_meta['saswp_person_schema_knows_'.$schema_id][0])){
            $input1['knows'] = $all_post_meta['saswp_person_schema_knows_'.$schema_id][0];
        }
        if(!empty($all_post_meta['saswp_person_schema_owns_'.$schema_id][0])){
            $input1['owns'] = $all_post_meta['saswp_person_schema_owns_'.$schema_id][0];
        }

        $perform_in = array();

        if(!empty($all_post_meta['saswp_person_schema_performerin_name_'.$schema_id][0])){
            $perform_in['name'] = $all_post_meta['saswp_person_schema_performerin_name_'.$schema_id][0];
        }
        if(!empty($all_post_meta['saswp_person_schema_performerin_location_name_'.$schema_id][0])){
            $perform_in['location']['name'] = $all_post_meta['saswp_person_schema_performerin_location_name_'.$schema_id][0];
        }
        if(!empty($all_post_meta['saswp_person_schema_performerin_location_locality_'.$schema_id][0])){
            $perform_in['location']['address']['addressLocality'] = $all_post_meta['saswp_person_schema_performerin_location_locality_'.$schema_id][0];
        }
        if(!empty($all_post_meta['saswp_person_schema_performerin_location_postal_code_'.$schema_id][0])){
            $perform_in['location']['address']['postalCode'] = $all_post_meta['saswp_person_schema_performerin_location_postal_code_'.$schema_id][0];
        }
        if(!empty($all_post_meta['saswp_person_schema_performerin_location_street_address_'.$schema_id][0])){
            $perform_in['location']['address']['streetAddress'] = $all_post_meta['saswp_person_schema_performerin_location_street_address_'.$schema_id][0];
        }
        if(!empty($all_post_meta['saswp_person_schema_performerin_offers_name_'.$schema_id][0])){
            $perform_in['offers']['name'] = $all_post_meta['saswp_person_schema_performerin_offers_name_'.$schema_id][0];
        }
        if(!empty($all_post_meta['saswp_person_schema_performerin_offers_availability_'.$schema_id][0])){
            $perform_in['offers']['availability'] = $all_post_meta['saswp_person_schema_performerin_offers_availability_'.$schema_id][0];
        }
        if(!empty($all_post_meta['saswp_person_schema_performerin_offers_price_'.$schema_id][0])){
            $perform_in['offers']['price'] = $all_post_meta['saswp_person_schema_performerin_offers_price_'.$schema_id][0];
        }
        if(!empty($all_post_meta['saswp_person_schema_performerin_offers_currency_'.$schema_id][0])){
            $perform_in['offers']['priceCurrency'] = $all_post_meta['saswp_person_schema_performerin_offers_currency_'.$schema_id][0];
        }
        if(!empty($all_post_meta['saswp_person_schema_performerin_offers_valid_from_'.$schema_id][0])){
            $perform_in['offers']['validFrom'] = $all_post_meta['saswp_person_schema_performerin_offers_valid_from_'.$schema_id][0];
        }
        if(!empty($all_post_meta['saswp_person_schema_performerin_offers_url_'.$schema_id][0])){
            $perform_in['offers']['url'] = $all_post_meta['saswp_person_schema_performerin_offers_url_'.$schema_id][0];
        }
        if(!empty($all_post_meta['saswp_person_schema_performerin_start_date_'.$schema_id][0])){
            $perform_in['startDate'] = $all_post_meta['saswp_person_schema_performerin_start_date_'.$schema_id][0];
        }
        if(!empty($all_post_meta['saswp_person_schema_performerin_end_date_'.$schema_id][0])){
            $perform_in['endDate'] = $all_post_meta['saswp_person_schema_performerin_end_date_'.$schema_id][0];
        }
        if(!empty($all_post_meta['saswp_person_schema_performerin_description_'.$schema_id][0])){
            $perform_in['description'] = $all_post_meta['saswp_person_schema_performerin_description_'.$schema_id][0];
        }
        if(!empty($all_post_meta['saswp_person_schema_performerin_image_'.$schema_id][0])){
            $perform_in['image'] = $all_post_meta['saswp_person_schema_performerin_image_'.$schema_id][0];
        }
        if(!empty($all_post_meta['saswp_person_schema_performerin_performer_'.$schema_id][0])){
            $perform_in['performer']['@type'] = 'Person';
            $perform_in['performer']['name']  = $all_post_meta['saswp_person_schema_performerin_performer_'.$schema_id][0];
        }

        if(!empty($perform_in)){
            $input1['performerIn'] = $perform_in;
        }

        return $input1;
}

function saswp_trip_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();

    $checkIdPro = ((isset($all_post_meta['saswp_trip_schema_id_'.$schema_id][0]) && $all_post_meta['saswp_trip_schema_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_trip_schema_id_'.$schema_id][0] : '');
        
    $input1['@context']              = saswp_context_url();
    $input1['@type']                 = 'Trip';
    if($checkIdPro){
        $input1['@id']               = $checkIdPro;  
    }
    $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_trip_schema_url_'.$schema_id, 'saswp_array');                            
    $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_trip_schema_name_'.$schema_id, 'saswp_array');                            
    $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_trip_schema_description_'.$schema_id, 'saswp_array');                            

    $input1 = saswp_get_modified_image('saswp_trip_schema_image_'.$schema_id.'_detail', $input1);

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

function saswp_boat_trip_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();

    $checkIdPro = ((isset($all_post_meta['saswp_boat_trip_schema_id_'.$schema_id][0]) && $all_post_meta['saswp_boat_trip_schema_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_boat_trip_schema_id_'.$schema_id][0] : '');
        
    $input1['@context']              = saswp_context_url();
    $input1['@type']                 = 'BoatTrip';
    if($checkIdPro){
        $input1['@id']               = $checkIdPro;  
    }
    $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_boat_trip_schema_url_'.$schema_id, 'saswp_array');                            
    $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_boat_trip_schema_name_'.$schema_id, 'saswp_array');                            
    $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_boat_trip_schema_description_'.$schema_id, 'saswp_array');                            

    if(!empty($all_post_meta['saswp_boat_trip_schema_arrival_time_'.$schema_id][0])){
        $input1['arrivalTime'] = $all_post_meta['saswp_boat_trip_schema_arrival_time_'.$schema_id][0];
    }
    if(!empty($all_post_meta['saswp_boat_trip_schema_departure_time_'.$schema_id][0])){
        $input1['departureTime'] = $all_post_meta['saswp_boat_trip_schema_departure_time_'.$schema_id][0];
    }
    if(!empty($all_post_meta['saswp_boat_trip_schema_arrival_boat_terminal_'.$schema_id][0])){
        $input1['arrivalBoatTerminal'] = $all_post_meta['saswp_boat_trip_schema_arrival_boat_terminal_'.$schema_id][0];
    }
    if(!empty($all_post_meta['saswp_boat_trip_schema_departure_boat_terminal_'.$schema_id][0])){
        $input1['departureBoatTerminal'] = $all_post_meta['saswp_boat_trip_schema_departure_boat_terminal_'.$schema_id][0];
    }

    $input1 = saswp_get_modified_image('saswp_boat_trip_schema_image_'.$schema_id.'_detail', $input1);

    $itinerary  = get_post_meta($schema_post_id, 'boat_trip_itinerary_'.$schema_id, true);

    $itinerary_arr = array();

    if(!empty($itinerary)){

        foreach($itinerary as $val){

            $supply_data = array();
            $supply_data['@type']        = $val['saswp_boat_trip_itinerary_type'];
            $supply_data['name']         = $val['saswp_boat_trip_itinerary_name'];
            $supply_data['description']  = $val['saswp_boat_trip_itinerary_description'];
            $supply_data['url']          = $val['saswp_boat_trip_itinerary_url'];


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
                    $response['@type']                        = 'ItemList';  
                    $response['url']                          = saswp_get_permalink(); 
                    $response['itemListElement']              = $list_item;
       
    }else{
        if($itemtype == 'ItemType'){
            global $wp_query;
            $item_list = array();
            $loop_query_string = array(
                'posts_per_page' => 10
            );
            
            if($wp_query->query_vars['posts_per_page']){
                $loop_query_string = array(
                    'posts_per_page' => $wp_query->query_vars['posts_per_page']
                );  
            }
            
            $post_loop = new WP_Query( $loop_query_string );                

            $i = 1;
            if ( $post_loop->have_posts() ):
                while( $post_loop->have_posts() ): $post_loop->the_post();
                    $result            = saswp_get_loop_markup($i);                                                                                                     
                    $item_list[]       = isset($result['itemlist'])?$result['itemlist']:'';                
                    $i++;
                endwhile;
            endif;
            wp_reset_postdata();
            if(!empty($item_list) && is_array($item_list) && count($item_list)){
                $response['@context']                     = saswp_context_url();
                $response['@type']                        = 'ItemList';  
                $response['url']                          = saswp_get_permalink(); 
                $response['itemListElement']              = $item_list;        
            }
        }         
    }    
    
    return $response;
    
}

function saswp_faq_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
    $checkIdPro = ((isset($all_post_meta['saswp_faq_id_'.$schema_id][0]) && $all_post_meta['saswp_faq_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_faq_id_'.$schema_id][0] : '');
    
    $input1['@context']              = saswp_context_url();
    $input1['@type']                 = 'FAQPage';
    if($checkIdPro){
        $input1['@id']               = $checkIdPro;  
    }  

    if(isset($all_post_meta['saswp_faq_id_'.$schema_id]) && isset($all_post_meta['saswp_faq_id_'.$schema_id][0]) && !empty($all_post_meta['saswp_faq_id_'.$schema_id][0])){
        $input1['@id']                   = saswp_remove_warnings($all_post_meta, 'saswp_faq_id_'.$schema_id, 'saswp_array');
    }

    if(isset($all_post_meta['saswp_faq_headline_'.$schema_id]) && isset($all_post_meta['saswp_faq_headline_'.$schema_id][0]) && !empty($all_post_meta['saswp_faq_headline_'.$schema_id][0])){
        $input1['headline']              = saswp_remove_warnings($all_post_meta, 'saswp_faq_headline_'.$schema_id, 'saswp_array');
    }           
    if(isset($all_post_meta['saswp_faq_keywords_'.$schema_id]) && isset($all_post_meta['saswp_faq_keywords_'.$schema_id][0]) && !empty($all_post_meta['saswp_faq_keywords_'.$schema_id][0])){              
        $input1['keywords']              = saswp_remove_warnings($all_post_meta, 'saswp_faq_keywords_'.$schema_id, 'saswp_array');
    }               
    if(isset($all_post_meta['saswp_faq_date_published_'.$schema_id]) && isset($all_post_meta['saswp_faq_date_published_'.$schema_id][0]) && !empty($all_post_meta['saswp_faq_date_published_'.$schema_id][0])){     
        $input1['datePublished']         = date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_faq_date_published_'.$schema_id][0]));
    }
    if(isset($all_post_meta['saswp_faq_date_modified_'.$schema_id]) && isset($all_post_meta['saswp_faq_date_modified_'.$schema_id][0]) && !empty($all_post_meta['saswp_faq_date_modified_'.$schema_id][0])){  
        $input1['dateModified']          = date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_faq_date_modified_'.$schema_id][0]));
    }
    if(isset($all_post_meta['saswp_faq_date_created_'.$schema_id]) && isset($all_post_meta['saswp_faq_date_created_'.$schema_id][0]) && !empty($all_post_meta['saswp_faq_date_created_'.$schema_id][0])){
        $input1['dateCreated']           = date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_faq_date_created_'.$schema_id][0]));
    }

    if(isset($all_post_meta['saswp_faq_author_name_'.$schema_id]) && isset($all_post_meta['saswp_faq_author_name_'.$schema_id][0]) && !empty($all_post_meta['saswp_faq_author_name_'.$schema_id][0])){

        $input1['author']['@type']       = 'Person';
        $input1['author']['name']       = saswp_remove_warnings($all_post_meta, 'saswp_faq_author_name_'.$schema_id, 'saswp_array');

        if(isset($all_post_meta['saswp_faq_author_type_'.$schema_id]) && isset($all_post_meta['saswp_faq_author_type_'.$schema_id][0])){
            $input1['author']['@type']       = saswp_remove_warnings($all_post_meta, 'saswp_faq_author_type_'.$schema_id, 'saswp_array');
        }
        if(isset($all_post_meta['saswp_faq_author_description_'.$schema_id]) && isset($all_post_meta['saswp_faq_author_description_'.$schema_id][0])){
            $input1['author']['description']        = saswp_remove_warnings($all_post_meta, 'saswp_faq_author_description_'.$schema_id, 'saswp_array');
        }
        if(isset($all_post_meta['saswp_faq_author_url_'.$schema_id]) && isset($all_post_meta['saswp_faq_author_url_'.$schema_id][0])){
            $input1['author']['url']        = saswp_remove_warnings($all_post_meta, 'saswp_faq_author_url_'.$schema_id, 'saswp_array');
        }
        if(isset($all_post_meta['saswp_faq_author_image_'.$schema_id]) && isset($all_post_meta['saswp_faq_author_image_'.$schema_id][0])){
            if(!empty($all_post_meta['saswp_faq_author_image_'.$schema_id][0])){
                $author_details = array();
                $author_details['@type']  = 'ImageObject';
                $author_details['url']    = $all_post_meta['saswp_faq_author_image_'.$schema_id][0];

                $input1['author']['image']         = $author_details; 
            }
        } 
    }
    
    $faq_question  = get_post_meta($schema_post_id, 'faq_question_'.$schema_id, true);

    $faq_question_arr = array();

    if(!empty($faq_question)){

        foreach($faq_question as $val){

            $supply_data = array();
            $supply_data['@type']                   = 'Question';
            $supply_data['name']                    = $val['saswp_faq_question_name'];
            $supply_data['acceptedAnswer']['@type'] = 'Answer';
            $supply_data['acceptedAnswer']['text']  = do_shortcode($val['saswp_faq_question_answer']);

           $faq_question_arr[] =  $supply_data;
        }
       $input1['mainEntity'] = $faq_question_arr;
    }
   
    if( !empty($all_post_meta['saswp_faq_about_'.$schema_id][0]) && isset( $all_post_meta['saswp_faq_about_'.$schema_id][0] )){ 

        $explode_about = explode(',', $all_post_meta['saswp_faq_about_'.$schema_id][0]);
            if(!empty($explode_about)){
                $about_arr = array();
                foreach($explode_about as $val){
                    $about_arr[] = array(
                                '@type' => 'Thing',
                                'name'  => $val
                    );
                }
                $input1['about'] = $about_arr;
            }                                            
    }
    
    return $input1;
    
}

function saswp_music_album_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
    $checkIdPro = ((isset($all_post_meta['saswp_music_album_id_'.$schema_id][0]) && $all_post_meta['saswp_music_album_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_music_album_id_'.$schema_id][0] : '');

    $input1['@context']              = saswp_context_url();
    $input1['@type']                 = 'MusicAlbum';
    if($checkIdPro){
        $input1['@id']               = $checkIdPro;  
    }
                          
    $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_music_album_url_'.$schema_id, 'saswp_array');                                
    $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_music_album_name_'.$schema_id, 'saswp_array');                            
    $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_music_album_description_'.$schema_id, 'saswp_array');                                
    $input1['genre']                 = saswp_remove_warnings($all_post_meta, 'saswp_music_album_genre_'.$schema_id, 'saswp_array');                                                            


    if(isset($all_post_meta['saswp_music_album_artist_'.$schema_id][0])){

        $input1['byArtist']['@type']     = 'MusicGroup';
        $input1['byArtist']['name']      = $all_post_meta['saswp_music_album_artist_'.$schema_id][0];

    }
    
    $input1 = saswp_get_modified_image('saswp_music_album_image_'.$schema_id.'_detail', $input1);

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

    $checkIdPro = ((isset($all_post_meta['saswp_jobposting_schema_id_'.$schema_id][0]) && $all_post_meta['saswp_jobposting_schema_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_jobposting_schema_id_'.$schema_id][0] : '');
        
    $input1['@context']              = saswp_context_url();
    $input1['@type']                 = 'JobPosting';
    if($checkIdPro){
        $input1['@id']               = $checkIdPro;  
    }
    $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_url_'.$schema_id, 'saswp_array');                            
    $input1['title']                 = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_title_'.$schema_id, 'saswp_array');                            
    $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_description_'.$schema_id, 'saswp_array');
    $input1['datePosted']            = isset($all_post_meta['saswp_jobposting_schema_dateposted_'.$schema_id][0])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_jobposting_schema_dateposted_'.$schema_id][0])):'';                            
    $input1['directApply']           = isset($all_post_meta['saswp_jobposting_schema_direct_apply_'.$schema_id][0])?$all_post_meta['saswp_jobposting_schema_direct_apply_'.$schema_id][0]:'false';                            
    $input1['validThrough']          = isset($all_post_meta['saswp_jobposting_schema_validthrough_'.$schema_id][0])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_jobposting_schema_validthrough_'.$schema_id][0])):'';                            
    $input1['employmentType']        = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_employment_type_'.$schema_id, 'saswp_array');
    $input1['industry']              = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_industry_'.$schema_id, 'saswp_array');
    $input1['occupationalCategory']  = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_occupational_category_'.$schema_id, 'saswp_array');
    $input1['hiringOrganization']['@type']     = 'Organization';
    $input1['hiringOrganization']['name']      = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_ho_name_'.$schema_id, 'saswp_array');
    $input1['hiringOrganization']['sameAs']    = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_ho_url_'.$schema_id, 'saswp_array');

    if(!(empty($howto_image))){

    $input1['hiringOrganization']['logo']['@type']        = 'ImageObject';
    $input1['hiringOrganization']['logo']['url']          = isset($howto_image['thumbnail']) ? esc_url($howto_image['thumbnail']):'';
    $input1['hiringOrganization']['logo']['height']       = isset($howto_image['width'])     ? esc_attr($howto_image['width'])   :'';
    $input1['hiringOrganization']['logo']['width']        = isset($howto_image['height'])    ? esc_attr($howto_image['height'])  :'';

    }

    if( isset($all_post_meta['saswp_jobposting_schema_job_location_type_'.$schema_id][0]) ){
        $input1['jobLocationType']                       = $all_post_meta['saswp_jobposting_schema_job_location_type_'.$schema_id][0];
    }

    if( isset($all_post_meta['saswp_jobposting_schema_applicant_location_requirements_'.$schema_id][0]) ){
        $input1['applicantLocationRequirements']['@type']     = 'Country';
        $input1['applicantLocationRequirements']['name']     = $all_post_meta['saswp_jobposting_schema_applicant_location_requirements_'.$schema_id][0];
    }

    $job_location_arr = array();
    $job_location     = array();

    $job_location_arr['@type']                        = 'Place';
    $job_location_arr['address']['@type']             = 'PostalAddress';                            
    $job_location_arr['address']['streetAddress']     = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_street_address_'.$schema_id, 'saswp_array');
    $job_location_arr['address']['addressLocality']   = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_locality_'.$schema_id, 'saswp_array');
    $job_location_arr['address']['addressRegion']     = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_region_'.$schema_id, 'saswp_array');
    $job_location_arr['address']['addressCountry']    = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_country_'.$schema_id, 'saswp_array');
    $job_location_arr['address']['postalCode']        = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_postalcode_'.$schema_id, 'saswp_array');

    if(isset($all_post_meta['saswp_jobposting_schema_latitude_'.$schema_id][0]) && isset($all_post_meta['saswp_jobposting_schema_longitude_'.$schema_id][0])){

        $job_location_arr['geo']['@type']     = 'GeoCoordinates';
        $job_location_arr['geo']['latitude']  = $all_post_meta['saswp_jobposting_schema_latitude_'.$schema_id][0];
        $job_location_arr['geo']['longitude'] = $all_post_meta['saswp_jobposting_schema_longitude_'.$schema_id][0];
    }

    $job_location[] = $job_location_arr;

    $joblocation_meta  = get_post_meta($schema_post_id, 'joblocation_'.$schema_id, true);

    if(!empty($joblocation_meta)){

        foreach($joblocation_meta as $value){
                
            $supply_data = array();

            $supply_data['@type']                        = 'Place';
            $supply_data['address']['@type']             = 'PostalAddress';                            
            $supply_data['address']['streetAddress']     = $value['saswp_jobposting_street_address'];
            $supply_data['address']['addressLocality']   = $value['saswp_jobposting_locality'];
            $supply_data['address']['addressRegion']     = $value['saswp_jobposting_region'];
            $supply_data['address']['addressCountry']    = $value['saswp_jobposting_country'];
            $supply_data['address']['postalCode']        = $value['saswp_jobposting_postalcode'];

            if( isset($value['saswp_jobposting_latitude']) && isset($value['saswp_jobposting_longitude']) ){

                $supply_data['geo']['@type']     = 'GeoCoordinates';
                $supply_data['geo']['latitude']  = $value['saswp_jobposting_latitude'];
                $supply_data['geo']['longitude'] = $value['saswp_jobposting_longitude'];

            }            

            $job_location[] =  $supply_data;
        }                       

    }

    if(!empty($job_location)){
        $input1['jobLocation'] = $job_location;
    }

    if( isset($all_post_meta['saswp_jobposting_schema_jobimmediatestart_'.$schema_id][0]) ){
        $input1['jobImmediateStart'] = $all_post_meta['saswp_jobposting_schema_jobimmediatestart_'.$schema_id][0];
    }

    $input1['baseSalary']['@type']             = 'MonetaryAmount';
    $input1['baseSalary']['currency']          = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_bs_currency_'.$schema_id, 'saswp_array');
    $input1['baseSalary']['value']['@type']    = 'QuantitativeValue';
    $input1['baseSalary']['value']['value']    = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_bs_value_'.$schema_id, 'saswp_array');
    $input1['baseSalary']['value']['unitText'] = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_bs_unittext_'.$schema_id, 'saswp_array');

    if( isset($all_post_meta['saswp_jobposting_schema_bs_min_value_'.$schema_id][0]) ){
        $input1['baseSalary']['value']['minValue'] = $all_post_meta['saswp_jobposting_schema_bs_min_value_'.$schema_id][0];
    }
    if( isset($all_post_meta['saswp_jobposting_schema_bs_max_value_'.$schema_id][0]) ){
        $input1['baseSalary']['value']['maxValue'] = $all_post_meta['saswp_jobposting_schema_bs_max_value_'.$schema_id][0];
    }
    $input1['estimatedSalary']['@type']             = 'MonetaryAmount';
    $input1['estimatedSalary']['currency']          = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_es_currency_'.$schema_id, 'saswp_array');
    $input1['estimatedSalary']['value']['@type']    = 'QuantitativeValue';
    $input1['estimatedSalary']['value']['value']    = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_es_value_'.$schema_id, 'saswp_array');
    $input1['estimatedSalary']['value']['unitText'] = saswp_remove_warnings($all_post_meta, 'saswp_jobposting_schema_es_unittext_'.$schema_id, 'saswp_array');
    
    if( isset($all_post_meta['saswp_jobposting_schema_es_min_value_'.$schema_id][0]) ){
        $input1['estimatedSalary']['value']['minValue'] = $all_post_meta['saswp_jobposting_schema_es_min_value_'.$schema_id][0];
    }
    if( isset($all_post_meta['saswp_jobposting_schema_es_max_value_'.$schema_id][0]) ){
        $input1['estimatedSalary']['value']['maxValue'] = $all_post_meta['saswp_jobposting_schema_es_max_value_'.$schema_id][0];
    }

    if( ( isset($all_post_meta['saswp_jobposting_schema_validthrough_'.$schema_id][0] ) && $all_post_meta['saswp_jobposting_schema_validthrough_'.$schema_id][0] !='' ) && date('Y-m-d',strtotime($all_post_meta['saswp_jobposting_schema_validthrough_'.$schema_id][0]) ) < date('Y-m-d') ){        
        $input1 = array();    
    }    
    return $input1;            
}

function saswp_mosque_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();

    $checkIdPro = ((isset($all_post_meta['saswp_mosque_schema_id_'.$schema_id][0]) && $all_post_meta['saswp_mosque_schema_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_mosque_schema_id_'.$schema_id][0] : '');
        
    $input1['@context']              = saswp_context_url();
    $input1['@type']                 = 'Mosque';
    if($checkIdPro){
        $input1['@id']               = $checkIdPro;  
    }
    $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_mosque_schema_url_'.$schema_id, 'saswp_array');                            
    $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_mosque_schema_name_'.$schema_id, 'saswp_array');                            
    $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_mosque_schema_description_'.$schema_id, 'saswp_array');

    $input1 = saswp_get_modified_image('saswp_mosque_schema_image_'.$schema_id.'_detail', $input1);  

    $input1['isAccessibleForFree']        = saswp_remove_warnings($all_post_meta, 'saswp_mosque_schema_is_accesible_free_'.$schema_id, 'saswp_array');                            
    $input1['maximumAttendeeCapacity']    = saswp_remove_warnings($all_post_meta, 'saswp_mosque_schema_maximum_a_capacity_'.$schema_id, 'saswp_array');
    $input1['hasMap']                     = saswp_remove_warnings($all_post_meta, 'saswp_mosque_schema_hasmap_'.$schema_id, 'saswp_array');

    $input1['address']['@type']             = 'PostalAddress';
    $input1['address']['addressCountry']    = saswp_remove_warnings($all_post_meta, 'saswp_mosque_schema_country_'.$schema_id, 'saswp_array');
    $input1['address']['addressLocality']   = saswp_remove_warnings($all_post_meta, 'saswp_mosque_schema_locality_'.$schema_id, 'saswp_array');
    $input1['address']['addressRegion']     = saswp_remove_warnings($all_post_meta, 'saswp_mosque_schema_region_'.$schema_id, 'saswp_array');
    $input1['address']['postalCode']        = saswp_remove_warnings($all_post_meta, 'saswp_mosque_schema_postal_code_'.$schema_id, 'saswp_array');
    
    return $input1;
    
}

function saswp_church_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();

    $checkIdPro = ((isset($all_post_meta['saswp_buddhisttemple_schema_id_'.$schema_id][0]) && $all_post_meta['saswp_church_schema_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_church_schema_id_'.$schema_id][0] : '');
        
    $input1['@context']              = saswp_context_url();
    $input1['@type']                 = 'Church';
    if($checkIdPro){
        $input1['@id']               = $checkIdPro;  
    }
    $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_church_schema_url_'.$schema_id, 'saswp_array');                            
    $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_church_schema_name_'.$schema_id, 'saswp_array');                            
    $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_church_schema_description_'.$schema_id, 'saswp_array');

    $input1 = saswp_get_modified_image('saswp_church_schema_image_'.$schema_id.'_detail', $input1);  

    $input1['isAccessibleForFree']        = saswp_remove_warnings($all_post_meta, 'saswp_church_schema_is_acceesible_free_'.$schema_id, 'saswp_array');                           
    $input1['maximumAttendeeCapacity']    = saswp_remove_warnings($all_post_meta, 'saswp_church_schema_maximum_a_capacity_'.$schema_id, 'saswp_array');
    $input1['hasMap']                     = saswp_remove_warnings($all_post_meta, 'saswp_church_schema_hasmap_'.$schema_id, 'saswp_array');

    $input1['address']['@type']             = 'PostalAddress';
    $input1['address']['addressCountry']    = saswp_remove_warnings($all_post_meta, 'saswp_church_schema_country_'.$schema_id, 'saswp_array');
    $input1['address']['addressLocality']   = saswp_remove_warnings($all_post_meta, 'saswp_church_schema_locality_'.$schema_id, 'saswp_array');
    $input1['address']['addressRegion']     = saswp_remove_warnings($all_post_meta, 'saswp_church_schema_region_'.$schema_id, 'saswp_array');
    $input1['address']['postalCode']        = saswp_remove_warnings($all_post_meta, 'saswp_church_schema_postal_code_'.$schema_id, 'saswp_array');
    
    return $input1;
    
}

function saswp_buddhist_temple_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();

    $checkIdPro = ((isset($all_post_meta['saswp_buddhisttemple_schema_id_'.$schema_id][0]) && $all_post_meta['saswp_buddhisttemple_schema_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_buddhisttemple_schema_id_'.$schema_id][0] : '');
        
    $input1['@context']              = saswp_context_url();
    $input1['@type']                 = 'BuddhistTemple';
    if($checkIdPro){
        $input1['@id']               = $checkIdPro;  
    }
    $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_buddhisttemple_schema_url_'.$schema_id, 'saswp_array');                            
    $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_buddhisttemple_schema_name_'.$schema_id, 'saswp_array');                            
    $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_buddhisttemple_schema_description_'.$schema_id, 'saswp_array');

    $input1 = saswp_get_modified_image('saswp_buddhisttemple_schema_image_'.$schema_id.'_detail', $input1); 

    $input1['isAccessibleForFree']        = saswp_remove_warnings($all_post_meta, 'saswp_buddhisttemple_schema_is_accesible_free_'.$schema_id, 'saswp_array');                           
    $input1['maximumAttendeeCapacity']    = saswp_remove_warnings($all_post_meta, 'saswp_buddhisttemple_schema_maximum_a_capacity_'.$schema_id, 'saswp_array');
    $input1['hasMap']                     = saswp_remove_warnings($all_post_meta, 'saswp_buddhisttemple_schema_hasmap_'.$schema_id, 'saswp_array');

    $input1['address']['@type']             = 'PostalAddress';
    $input1['address']['addressCountry']    = saswp_remove_warnings($all_post_meta, 'saswp_buddhisttemple_schema_country_'.$schema_id, 'saswp_array');
    $input1['address']['addressLocality']   = saswp_remove_warnings($all_post_meta, 'saswp_buddhisttemple_schema_locality_'.$schema_id, 'saswp_array');
    $input1['address']['addressRegion']     = saswp_remove_warnings($all_post_meta, 'saswp_buddhisttemple_schema_region_'.$schema_id, 'saswp_array');
    $input1['address']['postalCode']        = saswp_remove_warnings($all_post_meta, 'saswp_buddhisttemple_schema_postal_code_'.$schema_id, 'saswp_array');
    
    return $input1;
    
}

function saswp_hindu_temple_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
        
    $checkIdPro = ((isset($all_post_meta['saswp_hindutemple_schema_id_'.$schema_id][0]) && $all_post_meta['saswp_hindutemple_schema_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_hindutemple_schema_id_'.$schema_id][0] : '');

    $input1['@context']              = saswp_context_url();
    $input1['@type']                 = 'HinduTemple';
    if($checkIdPro){
        $input1['@id']               = $checkIdPro;  
    }
    $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_hindutemple_schema_url_'.$schema_id, 'saswp_array');                            
    $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_hindutemple_schema_name_'.$schema_id, 'saswp_array');                            
    $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_hindutemple_schema_description_'.$schema_id, 'saswp_array');

    $input1 = saswp_get_modified_image('saswp_hindutemple_schema_image_'.$schema_id.'_detail', $input1);
     
    $input1['isAccessibleForFree']        = saswp_remove_warnings($all_post_meta, 'saswp_hindutemple_schema_is_accesible_free_'.$schema_id, 'saswp_array');                           
    $input1['maximumAttendeeCapacity']    = saswp_remove_warnings($all_post_meta, 'saswp_hindutemple_schema_maximum_a_capacity_'.$schema_id, 'saswp_array');
    $input1['hasMap']                     = saswp_remove_warnings($all_post_meta, 'saswp_hindutemple_schema_hasmap_'.$schema_id, 'saswp_array');

    $input1['address']['@type']             = 'PostalAddress';
    $input1['address']['addressCountry']    = saswp_remove_warnings($all_post_meta, 'saswp_hindutemple_schema_country_'.$schema_id, 'saswp_array');
    $input1['address']['addressLocality']   = saswp_remove_warnings($all_post_meta, 'saswp_hindutemple_schema_locality_'.$schema_id, 'saswp_array');
    $input1['address']['addressRegion']     = saswp_remove_warnings($all_post_meta, 'saswp_hindutemple_schema_region_'.$schema_id, 'saswp_array');
    $input1['address']['postalCode']        = saswp_remove_warnings($all_post_meta, 'saswp_hindutemple_schema_postal_code_'.$schema_id, 'saswp_array');
    
    return $input1;
    
}

function saswp_lorh_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
    $checkIdPro = ((isset($all_post_meta['saswp_lorh_schema_id_'.$schema_id][0]) && $all_post_meta['saswp_lorh_schema_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_lorh_schema_id_'.$schema_id][0] : '');
            
    $input1['@context']              = saswp_context_url();
    $input1['@type']                 = 'LandmarksOrHistoricalBuildings';
    if($checkIdPro){
        $input1['@id']               = $checkIdPro;  
    }
    $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_lorh_schema_url_'.$schema_id, 'saswp_array');                            
    $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_lorh_schema_name_'.$schema_id, 'saswp_array');                            
    $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_lorh_schema_description_'.$schema_id, 'saswp_array');

    $input1 = saswp_get_modified_image('saswp_lorh_schema_image_'.$schema_id.'_detail', $input1); 

    $input1['hasMap']                     = saswp_remove_warnings($all_post_meta, 'saswp_lorh_schema_hasmap_'.$schema_id, 'saswp_array');                        
    $input1['isAccessibleForFree']        = saswp_remove_warnings($all_post_meta, 'saswp_lorh_schema_is_acceesible_free_'.$schema_id, 'saswp_array');
    $input1['maximumAttendeeCapacity']    = saswp_remove_warnings($all_post_meta, 'saswp_lorh_schema_maximum_a_capacity_'.$schema_id, 'saswp_array');                          

    $input1['address']['@type']             = 'PostalAddress';
    $input1['address']['addressCountry']    = saswp_remove_warnings($all_post_meta, 'saswp_lorh_schema_country_'.$schema_id, 'saswp_array');
    $input1['address']['addressLocality']   = saswp_remove_warnings($all_post_meta, 'saswp_lorh_schema_locality_'.$schema_id, 'saswp_array');
    $input1['address']['addressRegion']     = saswp_remove_warnings($all_post_meta, 'saswp_lorh_schema_region_'.$schema_id, 'saswp_array');
    $input1['address']['postalCode']        = saswp_remove_warnings($all_post_meta, 'saswp_lorh_schema_postal_code_'.$schema_id, 'saswp_array');

    if(isset($all_post_meta['saswp_lorh_schema_latitude_'.$schema_id][0]) && isset($all_post_meta['saswp_lorh_schema_longitude_'.$schema_id][0])){

        $input1['geo']['@type']     = 'GeoCoordinates';
        $input1['geo']['latitude']  = $all_post_meta['saswp_lorh_schema_latitude_'.$schema_id][0];
        $input1['geo']['longitude'] = $all_post_meta['saswp_lorh_schema_longitude_'.$schema_id][0];
    }
    
    return $input1;
    
}

function saswp_tourist_attraction_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
        $input1 = array();        
            
        $checkIdPro = ((isset($all_post_meta['saswp_ta_schema_id_'.$schema_id][0]) && $all_post_meta['saswp_ta_schema_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_ta_schema_id_'.$schema_id][0] : '');

        $input1['@context']              = saswp_context_url();
        $input1['@type']                 = 'TouristAttraction';
        if($checkIdPro){
            $input1['@id']               = $checkIdPro;  
        }
        $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_ta_schema_url_'.$schema_id, 'saswp_array');                            
        $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_ta_schema_name_'.$schema_id, 'saswp_array');                            
        $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_ta_schema_description_'.$schema_id, 'saswp_array');

        $input1 = saswp_get_modified_image('saswp_ta_schema_image_'.$schema_id.'_detail', $input1);

        $input1['isAccessibleForFree']    = saswp_remove_warnings($all_post_meta, 'saswp_ta_schema_is_acceesible_free_'.$schema_id, 'saswp_array');

        $input1['address']['@type']             = 'PostalAddress';
        $input1['address']['addressCountry']    = saswp_remove_warnings($all_post_meta, 'saswp_ta_schema_country_'.$schema_id, 'saswp_array');
        $input1['address']['addressLocality']   = saswp_remove_warnings($all_post_meta, 'saswp_ta_schema_locality_'.$schema_id, 'saswp_array');
        $input1['address']['addressRegion']     = saswp_remove_warnings($all_post_meta, 'saswp_ta_schema_region_'.$schema_id, 'saswp_array');
        $input1['address']['postalCode']        = saswp_remove_warnings($all_post_meta, 'saswp_ta_schema_postal_code_'.$schema_id, 'saswp_array');

        if(isset($all_post_meta['saswp_ta_schema_latitude_'.$schema_id][0]) && isset($all_post_meta['saswp_ta_schema_longitude_'.$schema_id][0])){

            $input1['geo']['@type']     = 'GeoCoordinates';
            $input1['geo']['latitude']  = $all_post_meta['saswp_ta_schema_latitude_'.$schema_id][0];
            $input1['geo']['longitude'] = $all_post_meta['saswp_ta_schema_longitude_'.$schema_id][0];
        }
    
    
    return $input1;
    
}

function saswp_tourist_destination_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
    $checkIdPro = ((isset($all_post_meta['saswp_td_schema_id_'.$schema_id][0]) && $all_post_meta['saswp_td_schema_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_td_schema_id_'.$schema_id][0] : '');
        
    $input1['@context']              = saswp_context_url();
    $input1['@type']                 = 'TouristDestination';
    if($checkIdPro){
        $input1['@id']               = $checkIdPro;  
    }
    $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_td_schema_url_'.$schema_id, 'saswp_array');                            
    $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_td_schema_name_'.$schema_id, 'saswp_array');                            
    $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_td_schema_description_'.$schema_id, 'saswp_array');

    $input1 = saswp_get_modified_image('saswp_td_schema_image_'.$schema_id.'_detail', $input1);


    $input1['address']['@type']             = 'PostalAddress';
    $input1['address']['addressCountry']    = saswp_remove_warnings($all_post_meta, 'saswp_td_schema_country_'.$schema_id, 'saswp_array');
    $input1['address']['addressLocality']   = saswp_remove_warnings($all_post_meta, 'saswp_td_schema_locality_'.$schema_id, 'saswp_array');
    $input1['address']['addressRegion']     = saswp_remove_warnings($all_post_meta, 'saswp_td_schema_region_'.$schema_id, 'saswp_array');
    $input1['address']['postalCode'] = saswp_remove_warnings($all_post_meta, 'saswp_td_schema_postal_code_'.$schema_id, 'saswp_array');                                                       
    
    if(isset($all_post_meta['saswp_td_schema_latitude_'.$schema_id][0]) && isset($all_post_meta['saswp_td_schema_longitude_'.$schema_id][0])){

        $input1['geo']['@type']     = 'GeoCoordinates';
        $input1['geo']['latitude']  = $all_post_meta['saswp_td_schema_latitude_'.$schema_id][0];
        $input1['geo']['longitude'] = $all_post_meta['saswp_td_schema_longitude_'.$schema_id][0];
    }
    
    return $input1;
}

/**
 * Prepare markup for post specific
 * @since 1.25
 * @param $schema_id            int
 * @param $schema_post_id       int
 * @param $all_post_meta        array
 * @return array
 * */
function saswp_tourist_trip_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    $input1 = array();
    $checkIdPro = ((isset($all_post_meta['saswp_tt_schema_id_'.$schema_id][0]) && $all_post_meta['saswp_tt_schema_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_tt_schema_id_'.$schema_id][0] : '');
        
    $input1['@context']              = saswp_context_url();
    $input1['@type']                 = 'TouristTrip';
    if($checkIdPro){
        $input1['@id']               = $checkIdPro;  
    }                            
    $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_tt_schema_name_'.$schema_id, 'saswp_array');                            
    $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_tt_schema_description_'.$schema_id, 'saswp_array');
    if(isset($all_post_meta['saswp_tt_schema_ttype_'.$schema_id]) && isset($all_post_meta['saswp_tt_schema_ttype_'.$schema_id][0])){
        if(is_string($all_post_meta['saswp_tt_schema_ttype_'.$schema_id][0])){
            $explode_type = explode(',', $all_post_meta['saswp_tt_schema_ttype_'.$schema_id][0]);
            if(!empty($explode_type) && is_array($explode_type)){
                $input1['touristType'] =   $explode_type;
            }
        }
    }
    if((isset($all_post_meta['saswp_tt_schema_son_'.$schema_id]) && isset($all_post_meta['saswp_tt_schema_son_'.$schema_id][0]) && !empty($all_post_meta['saswp_tt_schema_son_'.$schema_id][0])) || (isset($all_post_meta['saswp_tt_schema_sou_'.$schema_id]) && isset($all_post_meta['saswp_tt_schema_sou_'.$schema_id][0]) && !empty($all_post_meta['saswp_tt_schema_sou_'.$schema_id][0]))){
        $input1['subjectOf']['@type'] =   "CreativeWork";    
    }
    if(isset($all_post_meta['saswp_tt_schema_son_'.$schema_id]) && isset($all_post_meta['saswp_tt_schema_son_'.$schema_id][0])){
        $input1['subjectOf']['name'] =   saswp_remove_warnings($all_post_meta, 'saswp_tt_schema_son_'.$schema_id, 'saswp_array');    
    }
    if(isset($all_post_meta['saswp_tt_schema_sou_'.$schema_id]) && isset($all_post_meta['saswp_tt_schema_sou_'.$schema_id][0])){
        $input1['subjectOf']['url'] =   saswp_remove_warnings($all_post_meta, 'saswp_tt_schema_sou_'.$schema_id, 'saswp_array');    
    }
    $tourist_itinerary  = get_post_meta($schema_post_id, 'tourist_trip_itinerary_'.$schema_id, true);
    if(!empty($tourist_itinerary) && is_array($tourist_itinerary)){
        $cnt = 1;
        $itemlist_array = array();
        foreach ($tourist_itinerary as $tt_key => $tt_value) {
            if(!empty($tt_value) && is_array($tt_value)){
                $itemlist_element = array();
                $itemlist_element['@type'] = 'ListItem';
                $itemlist_element['position'] = $cnt;
                $itemlist_element['item']['@type'] = 'TouristAttraction';
                $itemlist_element['item']['name'] = isset($tt_value['saswp_tourist_trip_itinerary_name'])?$tt_value['saswp_tourist_trip_itinerary_name']:'';
                $itemlist_element['item']['description'] = isset($tt_value['saswp_tourist_trip_itinerary_description'])?$tt_value['saswp_tourist_trip_itinerary_description']:'';
                $cnt++;

                $itemlist_array[] = $itemlist_element;
            }
        }
        if(count($tourist_itinerary) > 0){
            $input1['itinerary']['@type'] = 'ItemList';
            $input1['itinerary']['numberOfItems'] = count($tourist_itinerary);
            $input1['itinerary']['itemListElement'] = $itemlist_array;
        }
    }
    
    return $input1;
}

function saswp_apartment_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();

    $checkIdPro = ((isset($all_post_meta['saswp_apartment_schema_id_'.$schema_id][0]) && $all_post_meta['saswp_apartment_schema_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_apartment_schema_id_'.$schema_id][0] : '');
        
    $input1['@context']              = saswp_context_url();
    $input1['@type']                 = 'Apartment';
    if($checkIdPro){
        $input1['@id']               = $checkIdPro;  
    }
    $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_apartment_schema_url_'.$schema_id, 'saswp_array');                            
    $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_apartment_schema_name_'.$schema_id, 'saswp_array');                            
    $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_apartment_schema_description_'.$schema_id, 'saswp_array');
    $input1['floorSize']             = saswp_remove_warnings($all_post_meta, 'saswp_apartment_schema_floor_size_'.$schema_id, 'saswp_array');

    $input1 = saswp_get_modified_image('saswp_apartment_schema_image_'.$schema_id.'_detail', $input1);
      
    $input1['numberOfRooms']           = saswp_remove_warnings($all_post_meta, 'saswp_apartment_schema_numberofrooms_'.$schema_id, 'saswp_array');

    $input1['address']['@type']             = 'PostalAddress';
    $input1['address']['addressCountry']    = saswp_remove_warnings($all_post_meta, 'saswp_apartment_schema_country_'.$schema_id, 'saswp_array');
    $input1['address']['addressLocality']   = saswp_remove_warnings($all_post_meta, 'saswp_apartment_schema_locality_'.$schema_id, 'saswp_array');
    $input1['address']['addressRegion']     = saswp_remove_warnings($all_post_meta, 'saswp_apartment_schema_region_'.$schema_id, 'saswp_array');
    $input1['address']['postalCode']        = saswp_remove_warnings($all_post_meta, 'saswp_apartment_schema_postalcode_'.$schema_id, 'saswp_array');

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
            $supply_data[$val['saswp_apartment_additional_property_code_type']]    = isset($val['saswp_apartment_additional_property_code_value']) ? $val['saswp_apartment_additional_property_code_value'] : '';
            $supply_data['value']                                                  = isset($val['saswp_apartment_additional_property_value']) ? $val['saswp_apartment_additional_property_value'] : '';

           $add_property_arr[] =  $supply_data;
        }

        $input1['additionalProperty'] = $add_property_arr;
    }
    
    return $input1;
    
}

function saswp_apartment_complex_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();

    $checkIdPro = ((isset($all_post_meta['saswp_apartment_complex_id_'.$schema_id][0]) && $all_post_meta['saswp_apartment_complex_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_apartment_complex_id_'.$schema_id][0] : '');
        
    $input1['@context']              = saswp_context_url();
    $input1['@type']                 = 'ApartmentComplex';
    if($checkIdPro){
        $input1['@id']               = $checkIdPro;  
    }
    $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_apartment_complex_url_'.$schema_id, 'saswp_array');                            
    $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_apartment_complex_name_'.$schema_id, 'saswp_array');                            
    $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_apartment_complex_description_'.$schema_id, 'saswp_array');    

    $input1 = saswp_get_modified_image('saswp_apartment_complex_image_'.$schema_id.'_detail', $input1);

    $input1['numberOfBedrooms']             = saswp_remove_warnings($all_post_meta, 'saswp_apartment_complex_no_of_bedrooms_'.$schema_id, 'saswp_array');
    $input1['petsAllowed']                  = saswp_remove_warnings($all_post_meta, 'saswp_apartment_complex_pets_allowed_'.$schema_id, 'saswp_array');

    $input1['address']['@type']             = 'PostalAddress';
    $input1['address']['streetAddress']     = saswp_remove_warnings($all_post_meta, 'saswp_apartment_complex_streetaddress_'.$schema_id, 'saswp_array');
    $input1['address']['addressCountry']    = saswp_remove_warnings($all_post_meta, 'saswp_apartment_complex_country_'.$schema_id, 'saswp_array');
    $input1['address']['addressLocality']   = saswp_remove_warnings($all_post_meta, 'saswp_apartment_complex_locality_'.$schema_id, 'saswp_array');
    $input1['address']['addressRegion']     = saswp_remove_warnings($all_post_meta, 'saswp_apartment_complex_region_'.$schema_id, 'saswp_array');
    $input1['address']['postalCode']        = saswp_remove_warnings($all_post_meta, 'saswp_apartment_complex_postalcode_'.$schema_id, 'saswp_array');
    $input1['address']['telephone']         = saswp_remove_warnings($all_post_meta, 'saswp_apartment_complex_phone_'.$schema_id, 'saswp_array');    
    
    if(isset($all_post_meta['saswp_apartment_complex_latitude_'.$schema_id][0]) && isset($all_post_meta['saswp_apartment_complex_longitude_'.$schema_id][0])){

        $input1['geo']['@type']     = 'GeoCoordinates';
        $input1['geo']['latitude']  = $all_post_meta['saswp_apartment_complex_latitude_'.$schema_id][0];
        $input1['geo']['longitude'] = $all_post_meta['saswp_apartment_complex_longitude_'.$schema_id][0];

    }

    return $input1;
    
}

function saswp_house_schema_makrup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();

    $checkIdPro = ((isset($all_post_meta['saswp_house_schema_id_'.$schema_id][0]) && $all_post_meta['saswp_house_schema_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_house_schema_id_'.$schema_id][0] : '');
    
    $input1['@context']              = saswp_context_url();
    $input1['@type']                 = 'House';
    if($checkIdPro){
        $input1['@id']               = $checkIdPro;  
    }
    $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_house_schema_url_'.$schema_id, 'saswp_array');                            
    $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_house_schema_name_'.$schema_id, 'saswp_array');                            
    $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_house_schema_description_'.$schema_id, 'saswp_array');

    $input1 = saswp_get_modified_image('saswp_house_schema_image_'.$schema_id.'_detail', $input1);

    $input1['petsAllowed']                  = saswp_remove_warnings($all_post_meta, 'saswp_house_schema_pets_allowed_'.$schema_id, 'saswp_array');

    $input1['address']['@type']             = 'PostalAddress';
    $input1['address']['addressCountry']    = saswp_remove_warnings($all_post_meta, 'saswp_house_schema_country_'.$schema_id, 'saswp_array');
    $input1['address']['addressLocality']   = saswp_remove_warnings($all_post_meta, 'saswp_house_schema_locality_'.$schema_id, 'saswp_array');
    $input1['address']['addressRegion']     = saswp_remove_warnings($all_post_meta, 'saswp_house_schema_region_'.$schema_id, 'saswp_array');
    $input1['address']['postalCode']        = saswp_remove_warnings($all_post_meta, 'saswp_house_schema_postalcode_'.$schema_id, 'saswp_array');

    $input1['telephone']                    = saswp_remove_warnings($all_post_meta, 'saswp_house_schema_telephone_'.$schema_id, 'saswp_array');

    $input1['hasMap']                       = saswp_remove_warnings($all_post_meta, 'saswp_house_schema_hasmap_'.$schema_id, 'saswp_array');
    $input1['floorSize']                    = saswp_remove_warnings($all_post_meta, 'saswp_house_schema_floor_size_'.$schema_id, 'saswp_array');
    $input1['numberOfRooms']                = saswp_remove_warnings($all_post_meta, 'saswp_house_schema_no_of_rooms_'.$schema_id, 'saswp_array');
    
    return $input1;
    
}

function saswp_single_family_residence_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();

    $checkIdPro = ((isset($all_post_meta['saswp_sfr_schema_id_'.$schema_id][0]) && $all_post_meta['saswp_sfr_schema_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_sfr_schema_id_'.$schema_id][0] : '');
                                   
    $input1['@context']              = saswp_context_url();
    $input1['@type']                 = 'SingleFamilyResidence';
    if($checkIdPro){
        $input1['@id']               = $checkIdPro;  
    }
    $input1['url']                   = saswp_remove_warnings($all_post_meta, 'saswp_sfr_schema_url_'.$schema_id, 'saswp_array');                            
    $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_sfr_schema_name_'.$schema_id, 'saswp_array');                            
    $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_sfr_schema_description_'.$schema_id, 'saswp_array');
    $input1 = saswp_get_modified_image('saswp_sfr_schema_image_'.$schema_id.'_detail', $input1);
    $input1['numberOfRooms']                = saswp_remove_warnings($all_post_meta, 'saswp_sfr_schema_numberofrooms_'.$schema_id, 'saswp_array');
    $input1['petsAllowed']                  = saswp_remove_warnings($all_post_meta, 'saswp_sfr_schema_pets_allowed_'.$schema_id, 'saswp_array');

    $input1['address']['@type']             = 'PostalAddress';
    $input1['address']['addressCountry']    = saswp_remove_warnings($all_post_meta, 'saswp_sfr_schema_country_'.$schema_id, 'saswp_array');
    $input1['address']['addressLocality']   = saswp_remove_warnings($all_post_meta, 'saswp_sfr_schema_locality_'.$schema_id, 'saswp_array');
    $input1['address']['addressRegion']     = saswp_remove_warnings($all_post_meta, 'saswp_sfr_schema_region_'.$schema_id, 'saswp_array');
    $input1['address']['postalCode']        = saswp_remove_warnings($all_post_meta, 'saswp_sfr_schema_postalcode_'.$schema_id, 'saswp_array');

    $input1['telephone']                    = saswp_remove_warnings($all_post_meta, 'saswp_sfr_schema_telephone_'.$schema_id, 'saswp_array');
    $input1['hasMap']                       = saswp_remove_warnings($all_post_meta, 'saswp_sfr_schema_hasmap_'.$schema_id, 'saswp_array');
    $input1['floorSize']                    = saswp_remove_warnings($all_post_meta, 'saswp_sfr_schema_floor_size_'.$schema_id, 'saswp_array');
    $input1['numberOfRooms']                = saswp_remove_warnings($all_post_meta, 'saswp_sfr_schema_no_of_rooms_'.$schema_id, 'saswp_array');
    
    return $input1;
    
}

function saswp_tv_series_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
        
    $actor     = get_post_meta($schema_post_id, 'tvseries_actor_'.$schema_id, true);              
    $season    = get_post_meta($schema_post_id, 'tvseries_season_'.$schema_id, true);                                          
    $checkIdPro = ((isset($all_post_meta['saswp_tvseries_schema_id_'.$schema_id][0]) && $all_post_meta['saswp_tvseries_schema_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_tvseries_schema_id_'.$schema_id][0] : '');

    $input1['@context']              = saswp_context_url();
    $input1['@type']                 = 'TVSeries';
    if($checkIdPro){
        $input1['@id']               = $checkIdPro;  
    }
    $input1['name']                  = saswp_remove_warnings($all_post_meta, 'saswp_tvseries_schema_name_'.$schema_id, 'saswp_array');                            			    
    $input1['description']           = saswp_remove_warnings($all_post_meta, 'saswp_tvseries_schema_description_'.$schema_id, 'saswp_array');

    $input1 = saswp_get_modified_image('saswp_tvseries_schema_image_'.$schema_id.'_detail', $input1);                            

    $input1['author']['@type']       = 'Person';

    if( isset($all_post_meta['saswp_tvseries_schema_author_type_'.$schema_id][0]) ) {
        $input1['author']['@type']       = $all_post_meta['saswp_tvseries_schema_author_type_'.$schema_id][0];
    }

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
    
    $input1      = array();
                    
    $symptom     = get_post_meta($schema_post_id, 'mc_symptom_'.$schema_id, true);              
    $riskfactro  = get_post_meta($schema_post_id, 'mc_risk_factor_'.$schema_id, true); 
      
    $checkIdPro = ((isset($all_post_meta['saswp_mc_schema_id_'.$schema_id][0]) && $all_post_meta['saswp_mc_schema_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_mc_schema_id_'.$schema_id][0] : '');           

    $input1['@context']                     = saswp_context_url();
    $input1['@type']                        = 'MedicalCondition';
    if($checkIdPro){
        $input1['@id']                      = $checkIdPro;  
    }
    $input1['name']                         = saswp_remove_warnings($all_post_meta, 'saswp_mc_schema_name_'.$schema_id, 'saswp_array');
    $input1['alternateName']                = saswp_remove_warnings($all_post_meta, 'saswp_mc_schema_alternate_name_'.$schema_id, 'saswp_array');                            
    $input1['description']                  = saswp_remove_warnings($all_post_meta, 'saswp_mc_schema_description_'.$schema_id, 'saswp_array');

    $input1 = saswp_get_modified_image('saswp_mc_schema_image_'.$schema_id.'_detail', $input1);

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
        $input1['@id']      = get_permalink().'#qapage';

        $input1['mainEntity']['@type']         = 'Question';
        $input1['mainEntity']['name']          = saswp_remove_warnings($all_post_meta, 'saswp_qa_question_title_'.$schema_id, 'saswp_array');
        $input1['mainEntity']['text']          = saswp_remove_warnings($all_post_meta, 'saswp_qa_question_description_'.$schema_id, 'saswp_array');
        $input1['mainEntity']['upvoteCount']   = saswp_remove_warnings($all_post_meta, 'saswp_qa_upvote_count_'.$schema_id, 'saswp_array');
        $input1['mainEntity']['dateCreated']   = isset($all_post_meta['saswp_qa_date_created_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_qa_date_created_'.$schema_id][0])):'';

        $input1['mainEntity']['author']['@type']  = 'Person';
       
        if(isset($all_post_meta['saswp_qa_question_author_type_'.$schema_id][0])){
            $input1['mainEntity']['author']['@type']  = $all_post_meta['saswp_qa_question_author_type_'.$schema_id][0];
        }

        $input1['mainEntity']['author']['name']   =  saswp_remove_warnings($all_post_meta, 'saswp_qa_question_author_name_'.$schema_id, 'saswp_array');
        $input1['mainEntity']['author']['url']   =  saswp_remove_warnings($all_post_meta, 'saswp_qa_question_author_url_'.$schema_id, 'saswp_array');
        
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

                    $supply_data['author']['@type'] = 'Person';

                    if(!empty($val['saswp_qa_accepted_author_type'])){
                        $supply_data['author']['@type'] = $val['saswp_qa_accepted_author_type'];
                    }

                    $supply_data['author']['name']      = $val['saswp_qa_accepted_author_name'];        
                    if(isset($val['saswp_qa_accepted_author_url'])){            
                        $supply_data['author']['url']      = $val['saswp_qa_accepted_author_url'];   
                    }                 
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

                    $supply_data['author']['@type'] = 'Person';

                    if(!empty($val['saswp_qa_suggested_author_type'])){
                        $supply_data['author']['@type'] = $val['saswp_qa_suggested_author_type'];
                    }

                    $supply_data['author']['name']      = $val['saswp_qa_suggested_author_name'];
                    if(isset($val['saswp_qa_suggested_author_url'])){                    
                        $supply_data['author']['url']      = $val['saswp_qa_suggested_author_url'];  
                    }                  
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
                '@id'                       => get_permalink().'#DataFeed',    
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
        
    $slogo = get_post_meta( get_the_ID(), 'saswp_dfp_organization_logo_'.$schema_id.'_detail',true); 

    $checkIdPro = ((isset($all_post_meta['saswp_dfp_id_'.$schema_id][0]) && $all_post_meta['saswp_dfp_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_dfp_id_'.$schema_id][0] : '');

    $input1 = array(
        '@context'			=> saswp_context_url(),
        '@type'				=> 'DiscussionForumPosting' ,
        '@id'				=> $checkIdPro,    			
        'mainEntityOfPage'		=> saswp_remove_warnings($all_post_meta, 'saswp_dfp_main_entity_of_page_'.$schema_id, 'saswp_array'),    			
        'headline'			=> saswp_remove_warnings($all_post_meta, 'saswp_dfp_headline_'.$schema_id, 'saswp_array'),
        'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_dfp_description_'.$schema_id, 'saswp_array'),			
        'url'				=> saswp_remove_warnings($all_post_meta, 'saswp_dfp_url_'.$schema_id, 'saswp_array'),        
        'datePublished'                 => isset($all_post_meta['saswp_dfp_date_published_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_dfp_date_published_'.$schema_id][0])):'',
        'dateModified'                  => isset($all_post_meta['saswp_dfp_date_modified_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_dfp_date_modified_'.$schema_id][0])):'',        
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

        if(empty($input1['@id'])){
            unset($input1['@id']);
        }

         $input1 = saswp_get_modified_image('saswp_dfp_image_'.$schema_id.'_detail', $input1);
    
         $input1['author']['@type']       = 'Person';

         if(isset( $all_post_meta['saswp_dfp_author_type_'.$schema_id][0] )){
            $input1['author']['@type']       = $all_post_meta['saswp_dfp_author_type_'.$schema_id][0];
         }  

         $input1['author']['name']        = saswp_remove_warnings($all_post_meta, 'saswp_dfp_author_name_'.$schema_id, 'saswp_array');
         $input1['author']['description'] = saswp_remove_warnings($all_post_meta, 'saswp_dfp_author_description_'.$schema_id, 'saswp_array');
         $input1['author']['url']         = saswp_remove_warnings($all_post_meta, 'saswp_dfp_author_url_'.$schema_id, 'saswp_array');
         

    return $input1;
    
}

function saswp_blogposting_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
    $author_image = get_post_meta( get_the_ID(), 'saswp_blogposting_author_image_'.$schema_id.'_detail',true);
    $slogo = get_post_meta( get_the_ID(), 'saswp_blogposting_organization_logo_'.$schema_id.'_detail',true);  

    $checkIdPro = ((isset($all_post_meta['saswp_blogposting_id_'.$schema_id][0]) && $all_post_meta['saswp_blogposting_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_blogposting_id_'.$schema_id][0] : '');           

    $input1 = array(
    '@context'			=> saswp_context_url(),
    '@type'				=> 'BlogPosting' ,
    '@id'                           => $checkIdPro,  
    'inLanguage'                    => saswp_remove_warnings($all_post_meta, 'saswp_blogposting_inlanguage_'.$schema_id, 'saswp_array'),
    'mainEntityOfPage'              => saswp_remove_warnings($all_post_meta, 'saswp_blogposting_main_entity_of_page_'.$schema_id, 'saswp_array'),
    'headline'			            => saswp_remove_warnings($all_post_meta, 'saswp_blogposting_headline_'.$schema_id, 'saswp_array'),
    'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_blogposting_description_'.$schema_id, 'saswp_array'),
    'articleBody'                   => saswp_remove_warnings($all_post_meta, 'saswp_blogposting_body_'.$schema_id, 'saswp_array'),
    'keywords'                      => saswp_remove_warnings($all_post_meta, 'saswp_blogposting_keywords_'.$schema_id, 'saswp_array'),
    'name'				            => saswp_remove_warnings($all_post_meta, 'saswp_blogposting_name_'.$schema_id, 'saswp_array'),
    'url'				            => saswp_remove_warnings($all_post_meta, 'saswp_blogposting_url_'.$schema_id, 'saswp_array'),    
    'datePublished'                 => isset($all_post_meta['saswp_blogposting_date_published_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_blogposting_date_published_'.$schema_id][0], get_post_time('h:i:s')) :'',
    'dateModified'                  => isset($all_post_meta['saswp_blogposting_date_modified_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_blogposting_date_modified_'.$schema_id][0], get_the_modified_time('h:i:s')) :'',    
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

    if(empty($input1['@id'])){
        unset($input1['@id']);
    }

    if(isset($all_post_meta['saswp_blogposting_image_'.$schema_id][0]) && $all_post_meta['saswp_blogposting_image_'.$schema_id][0]){
        $input1['image'] = $all_post_meta['saswp_blogposting_image_'.$schema_id][0];
    }
    
    $input1['author']['@type']       = 'Person';

    if(isset( $all_post_meta['saswp_blogposting_author_type_'.$schema_id][0] )){
        $input1['author']['@type']       = $all_post_meta['saswp_blogposting_author_type_'.$schema_id][0];
    }  

    $input1['author']['name']        = saswp_remove_warnings($all_post_meta, 'saswp_blogposting_author_name_'.$schema_id, 'saswp_array');
    $input1['author']['honorificSuffix']        = saswp_remove_warnings($all_post_meta, 'saswp_blogposting_author_honorific_suffix_'.$schema_id, 'saswp_array');
    $input1['author']['description'] = saswp_remove_warnings($all_post_meta, 'saswp_blogposting_author_description_'.$schema_id, 'saswp_array');
    $input1['author']['url']         = saswp_remove_warnings($all_post_meta, 'saswp_blogposting_author_url_'.$schema_id, 'saswp_array');
   
    $input1['author']['JobTitle']    = saswp_remove_warnings($all_post_meta, 'saswp_blogposting_author_jobtitle_'.$schema_id, 'saswp_array');  
   
    $input1['author']['image']['@type']   = 'ImageObject';
    $input1['author']['image']['url']     = saswp_remove_warnings($all_post_meta, 'saswp_blogposting_author_image_'.$schema_id, 'saswp_array');       
    if(isset($all_post_meta['saswp_blogposting_author_social_profile_'.$schema_id][0]) && !empty($all_post_meta['saswp_blogposting_author_social_profile_'.$schema_id][0])){
        $explode_sp = saswp_remove_warnings($all_post_meta, 'saswp_blogposting_author_social_profile_'.$schema_id, 'saswp_array');
        $explode_sp = explode(',',$explode_sp);
        if(!empty($explode_sp) && is_array($explode_sp)){
            $input1['author']['sameAs'] = $explode_sp; 
        }
    }

    $input1['author']['image']['height']  = isset($author_image['height']) ? $author_image['height'] : '';
    $input1['author']['image']['width']   = isset($author_image['width']) ? $author_image['width'] : '';

    if(!empty($all_post_meta['saswp_blogposting_editor_type_'.$schema_id][0]) && isset($all_post_meta['saswp_blogposting_editor_type_'.$schema_id][0])){
        $input1['editor']['@type']       = 'Person';
        if(!empty( $all_post_meta['saswp_blogposting_editor_type_'.$schema_id][0] )){
            $input1['editor']['@type']       = $all_post_meta['saswp_blogposting_editor_type_'.$schema_id][0];
        }  
        if(!empty( $all_post_meta['saswp_blogposting_editor_name_'.$schema_id][0] )){
            $input1['editor']['name']        = saswp_remove_warnings($all_post_meta, 'saswp_blogposting_editor_name_'.$schema_id, 'saswp_array');
        }  
        if(!empty( $all_post_meta['saswp_blogposting_editor_honorific_suffix_'.$schema_id][0] )){
            $input1['editor']['honorificSuffix']        = saswp_remove_warnings($all_post_meta, 'saswp_blogposting_editor_honorific_suffix_'.$schema_id, 'saswp_array');
        }  
        if(!empty( $all_post_meta['saswp_blogposting_editor_description_'.$schema_id][0] )){
            $input1['editor']['description'] = saswp_remove_warnings($all_post_meta, 'saswp_blogposting_editor_description_'.$schema_id, 'saswp_array');
        }  
        if(!empty( $all_post_meta['saswp_blogposting_editor_url_'.$schema_id][0] )){
            $input1['editor']['url']         = saswp_remove_warnings($all_post_meta, 'saswp_blogposting_editor_url_'.$schema_id, 'saswp_array');
        }  
        if(isset($all_post_meta['saswp_blogposting_author_social_profile_'.$schema_id][0]) && !empty($all_post_meta['saswp_blogposting_author_social_profile_'.$schema_id][0])){
            $explode_sp = saswp_remove_warnings($all_post_meta, 'saswp_blogposting_author_social_profile_'.$schema_id, 'saswp_array');
            $explode_sp = explode(',',$explode_sp);
            if(!empty($explode_sp) && is_array($explode_sp)){
                $input1['editor']['sameAs'] = $explode_sp; 
            }
        }
        if(!empty( $all_post_meta['saswp_blogposting_editor_image_'.$schema_id][0] )){
            $input1['editor']['image']['@type']   = 'ImageObject';
            $input1['editor']['image']['url']     = saswp_remove_warnings($all_post_meta, 'saswp_blogposting_editor_image_'.$schema_id, 'saswp_array');       
            $input1['editor']['image']['height']  = isset($author_image['height']) ? $author_image['height'] : '';
            $input1['editor']['image']['width']   = isset($author_image['width']) ? $author_image['width'] : '';
        }  
    }
       

    if(!empty($all_post_meta['saswp_blogposting_reviewedby_type_'.$schema_id][0]) && isset($all_post_meta['saswp_blogposting_reviewedby_type_'.$schema_id][0])){
        $input1['reviewedBy']['@type']       = 'Person';
        if(!empty( $all_post_meta['saswp_blogposting_reviewedby_type_'.$schema_id][0] )){
            $input1['reviewedBy']['@type']       = $all_post_meta['saswp_blogposting_reviewedby_type_'.$schema_id][0];
        }  
        if(!empty($all_post_meta['saswp_blogposting_reviewedby_name_'.$schema_id][0])){
            $input1['reviewedBy']['name']        = saswp_remove_warnings($all_post_meta, 'saswp_blogposting_reviewedby_name_'.$schema_id, 'saswp_array');
        }
        if(!empty($all_post_meta['saswp_blogposting_reviewedby_honorific_suffix_'.$schema_id][0])){
            $input1['reviewedBy']['honorificSuffix']        = saswp_remove_warnings($all_post_meta, 'saswp_blogposting_reviewedby_honorific_suffix_'.$schema_id, 'saswp_array');
        }
        if(!empty($all_post_meta['saswp_blogposting_reviewedby_description_'.$schema_id][0])){
            $input1['reviewedBy']['description'] = saswp_remove_warnings($all_post_meta, 'saswp_blogposting_reviewedby_description_'.$schema_id, 'saswp_array');
        }
        if(!empty($all_post_meta['saswp_blogposting_reviewedby_url_'.$schema_id][0])){
            $input1['reviewedBy']['url']         = saswp_remove_warnings($all_post_meta, 'saswp_blogposting_reviewedby_url_'.$schema_id, 'saswp_array');
        }
    }
      

    if(!empty($all_post_meta['saswp_blogposting_alumniof_'.$schema_id][0]) && isset( $all_post_meta['saswp_blogposting_alumniof_'.$schema_id][0] )){
        $itemlist = explode(',', $all_post_meta['saswp_blogposting_alumniof_'.$schema_id][0]);
        foreach ($itemlist as $key => $list){
            $vnewarr['@type'] = 'Organization';
            $vnewarr['Name']   = $list;   
            $input1['alumniOf'][] = $vnewarr;
        }
    }
    if( !empty($all_post_meta['saswp_blogposting_about_'.$schema_id][0]) && isset( $all_post_meta['saswp_blogposting_about_'.$schema_id][0] )){
        $input1['about']['@type']       = 'Event';
        $input1['about']['name']       = explode(',', $all_post_meta['saswp_blogposting_about_'.$schema_id][0]);
    }
    if( !empty($all_post_meta['saswp_blogposting_knowsabout_'.$schema_id][0]) && isset( $all_post_meta['saswp_blogposting_knowsabout_'.$schema_id][0] )){
        $input1['knowsAbout']       = explode(',', $all_post_meta['saswp_blogposting_knowsabout_'.$schema_id][0]);
    }
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
function saswp_vehicle_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();

    $checkIdPro = ((isset($all_post_meta['saswp_vehicle_schema_id_'.$schema_id][0]) && $all_post_meta['saswp_vehicle_schema_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_vehicle_schema_id_'.$schema_id][0] : '');
                                                                              
    $input1 = array(
    '@context'			            => saswp_context_url(),
    '@type'				            => ['Product','Vehicle'],   
    '@id'                           =>  $checkIdPro, 
    'url'				            => saswp_remove_warnings($all_post_meta, 'saswp_vehicle_schema_url_'.$schema_id, 'saswp_array'),
    'name'                          => saswp_remove_warnings($all_post_meta, 'saswp_vehicle_schema_name_'.$schema_id, 'saswp_array'),
    'sku'                           => saswp_remove_warnings($all_post_meta, 'saswp_vehicle_schema_sku_'.$schema_id, 'saswp_array'),
    'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_vehicle_schema_description_'.$schema_id, 'saswp_array'),
    'brand'                         => array('@type' => 'Brand',
                                             'name'  => saswp_remove_warnings($all_post_meta, 'saswp_vehicle_schema_brand_name_'.$schema_id, 'saswp_array'),
                                            )    
    );

    if(empty($input1['@id'])){
        unset($input1['@id']);
    } 
    
    $input1 = saswp_get_modified_image('saswp_vehicle_schema_image_'.$schema_id.'_detail', $input1);
    
    if( (isset($all_post_meta['saswp_vehicle_schema_price_'.$schema_id][0]) && $all_post_meta['saswp_vehicle_schema_price_'.$schema_id][0]) || (isset($all_post_meta['saswp_vehicle_schema_high_price_'.$schema_id][0]) && isset($all_post_meta['saswp_vehicle_schema_low_price_'.$schema_id][0]) ) ){
                    
        $input1['offers']['@type']           = 'Offer';
        $input1['offers']['availability']    = saswp_remove_warnings($all_post_meta, 'saswp_vehicle_schema_availability_'.$schema_id, 'saswp_array');
        $input1['offers']['itemCondition']   = saswp_remove_warnings($all_post_meta, 'saswp_vehicle_schema_condition_'.$schema_id, 'saswp_array');
        $input1['offers']['price']           = saswp_remove_warnings($all_post_meta, 'saswp_vehicle_schema_price_'.$schema_id, 'saswp_array');
        $input1['offers']['priceCurrency']   = saswp_remove_warnings($all_post_meta, 'saswp_vehicle_schema_currency_'.$schema_id, 'saswp_array');
        $input1['offers']['url']             = saswp_get_permalink();
        $input1['offers']['priceValidUntil'] = isset($all_post_meta['saswp_vehicle_schema_priceValidUntil_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_vehicle_schema_priceValidUntil_'.$schema_id][0])):'';
    
        if( isset($all_post_meta['saswp_vehicle_schema_high_price_'.$schema_id][0]) && isset($all_post_meta['saswp_vehicle_schema_low_price_'.$schema_id][0]) ){
            $input1['offers']['@type']           = 'AggregateOffer';
            $input1['offers']['highPrice']       = $all_post_meta['saswp_vehicle_schema_high_price_'.$schema_id][0];
            $input1['offers']['lowPrice']        = $all_post_meta['saswp_vehicle_schema_low_price_'.$schema_id][0];

            if( isset($all_post_meta['saswp_vehicle_schema_offer_count_'.$schema_id][0]) ){
                $input1['offers']['offerCount'] = $all_post_meta['saswp_vehicle_schema_offer_count_'.$schema_id][0];
            }

        }       

    }
                                            
    if(isset($all_post_meta['saswp_vehicle_schema_model_'.$schema_id])){
        $input1['model'] = esc_attr($all_post_meta['saswp_vehicle_schema_model_'.$schema_id][0]);  
    }
    if(isset($all_post_meta['saswp_vehicle_schema_body_type_'.$schema_id])){
        $input1['bodyType'] = esc_attr($all_post_meta['saswp_vehicle_schema_body_type_'.$schema_id][0]);  
    }
    if(isset($all_post_meta['saswp_vehicle_schema_fuel_type_'.$schema_id])){
        $input1['fuelType'] = esc_attr($all_post_meta['saswp_vehicle_schema_fuel_type_'.$schema_id][0]);  
    }

    if(isset($all_post_meta['saswp_vehicle_schema_fuel_efficiency_'.$schema_id])){
        $input1['fuelEfficiency'] = esc_attr($all_post_meta['saswp_vehicle_schema_fuel_efficiency_'.$schema_id][0]);  
    }
    if(isset($all_post_meta['saswp_vehicle_schema_seating_capacity_'.$schema_id])){
        $input1['seatingCapacity'] = esc_attr($all_post_meta['saswp_vehicle_schema_seating_capacity_'.$schema_id][0]);  
    }
    if(isset($all_post_meta['saswp_vehicle_schema_number_of_doors_'.$schema_id])){
        $input1['numberOfdoors'] = esc_attr($all_post_meta['saswp_vehicle_schema_number_of_doors_'.$schema_id][0]);  
    }

    if(isset($all_post_meta['saswp_vehicle_schema_weight_'.$schema_id])){
        $input1['weight'] = esc_attr($all_post_meta['saswp_vehicle_schema_weight_'.$schema_id][0]);  
    }
    if(isset($all_post_meta['saswp_vehicle_schema_width_'.$schema_id])){
        $input1['width'] = esc_attr($all_post_meta['saswp_vehicle_schema_width_'.$schema_id][0]);  
    }
    if(isset($all_post_meta['saswp_vehicle_schema_height_'.$schema_id])){
        $input1['height'] = esc_attr($all_post_meta['saswp_vehicle_schema_height_'.$schema_id][0]);  
    }

    if(isset($all_post_meta['saswp_vehicle_schema_manufacturer_'.$schema_id])){
        $input1['manufacturer'] = esc_attr($all_post_meta['saswp_vehicle_schema_manufacturer_'.$schema_id][0]);  
    }

    if(isset($all_post_meta['saswp_vehicle_schema_mpn_'.$schema_id])){
      $input1['mpn'] = esc_attr($all_post_meta['saswp_vehicle_schema_mpn_'.$schema_id][0]);  
    }    
    
    if(saswp_remove_warnings($all_post_meta, 'saswp_vehicle_schema_enable_rating_'.$schema_id, 'saswp_array') == 1 && saswp_remove_warnings($all_post_meta, 'saswp_vehicle_schema_rating_value_'.$schema_id, 'saswp_array') && saswp_remove_warnings($all_post_meta, 'saswp_vehicle_schema_rating_count_'.$schema_id, 'saswp_array')){   
                         
                                  $input1['aggregateRating'] = array(
                                                    "@type"       => "AggregateRating",
                                                    "ratingValue" => saswp_remove_warnings($all_post_meta, 'saswp_vehicle_schema_rating_value_'.$schema_id, 'saswp_array'),
                                                    "reviewCount" => saswp_remove_warnings($all_post_meta, 'saswp_vehicle_schema_rating_count_'.$schema_id, 'saswp_array')
                                                 );                                       
                                 }
                                     
                                 
                                $itinerary  = get_post_meta($schema_post_id, 'car_reviews_'.$schema_id, true);
                    
                                $itinerary_arr = array();

                                if(!empty($itinerary)){

                                 foreach($itinerary as $review){
                                        
                                  $review_fields = array();
                                  
                                  $review_fields['@type']           = 'Review';
                                  $review_fields['author']['@type'] = 'Person';
                                  $review_fields['author']['name']  = esc_attr($review['saswp_vehicle_reviews_reviewer_name']);

                                  if(isset($review['saswp_vehicle_reviews_created_date'])){
                                    $review_fields['datePublished'] = esc_html($review['saswp_vehicle_reviews_created_date']);
                                  }
                                  if(isset($review['saswp_vehicle_reviews_text'])){
                                    $review_fields['description']   = esc_textarea($review['saswp_vehicle_reviews_text']);
                                  }
                                                                                                                                                                
                                  if(is_int($review['saswp_vehicle_reviews_reviewer_rating'])){
                                      
                                        $review_fields['reviewRating']['@type']   = 'Rating';
                                        $review_fields['reviewRating']['bestRating']   = '5';
                                        $review_fields['reviewRating']['ratingValue']   = esc_attr($review['saswp_vehicle_reviews_reviewer_rating']);
                                        $review_fields['reviewRating']['worstRating']   = '1';
                                  
                                  }
                                                                                                                                                                
                                  $itinerary_arr[] = $review_fields;
                                    }
                                   $input1['review'] = $itinerary_arr;
                                }
                                
                                $service = new saswp_output_service();
                                $car_details = $service->saswp_woocommerce_product_details(get_the_ID());  

                                if(!empty($car_details['car_reviews'])){
                              
                                $reviews = array();
                              
                                 foreach ($car_details['car_reviews'] as $review){
                                                                                  
                                  $review_fields = array();
                                  
                                  $review_fields['@type']           = 'Review';
                                  $review_fields['author']['@type'] = 'Person';
                                  $review_fields['author']['name']  = esc_attr($review['author']);
                                  $review_fields['datePublished']   = esc_html($review['datePublished']);
                                  $review_fields['description']     = $review['description'];
                                                                            
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
function saswp_car_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
    $checkIdPro = ((isset($all_post_meta['saswp_car_schema_id_'.$schema_id][0]) && $all_post_meta['saswp_car_schema_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_car_schema_id_'.$schema_id][0] : '');
                                                                            
    $input1 = array(
    '@context'			            => saswp_context_url(),
    '@type'				            => ['Product','Car'],    
    '@id'                              => $checkIdPro,
    'url'				            => saswp_remove_warnings($all_post_meta, 'saswp_car_schema_url_'.$schema_id, 'saswp_array'),
    'name'                          => saswp_remove_warnings($all_post_meta, 'saswp_car_schema_name_'.$schema_id, 'saswp_array'),
    'sku'                           => saswp_remove_warnings($all_post_meta, 'saswp_car_schema_sku_'.$schema_id, 'saswp_array'),
    'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_car_schema_description_'.$schema_id, 'saswp_array'),
    'brand'                         => array('@type' => 'Brand',
                                             'name'  => saswp_remove_warnings($all_post_meta, 'saswp_car_schema_brand_name_'.$schema_id, 'saswp_array'),
                                            )    
    ); 

    if(empty($input1['@id'])){
        unset($input1['@id']);
    }
    
    $input1 = saswp_get_modified_image('saswp_car_schema_image_'.$schema_id.'_detail', $input1);
    
    if( (isset($all_post_meta['saswp_car_schema_price_'.$schema_id][0]) && $all_post_meta['saswp_car_schema_price_'.$schema_id][0]) || (isset($all_post_meta['saswp_car_schema_high_price_'.$schema_id][0]) && isset($all_post_meta['saswp_car_schema_low_price_'.$schema_id][0]) ) ){
                    
        $input1['offers']['@type']           = 'Offer';
        $input1['offers']['availability']    = saswp_remove_warnings($all_post_meta, 'saswp_car_schema_availability_'.$schema_id, 'saswp_array');
        $input1['offers']['itemCondition']   = saswp_remove_warnings($all_post_meta, 'saswp_car_schema_condition_'.$schema_id, 'saswp_array');
        $input1['offers']['price']           = saswp_remove_warnings($all_post_meta, 'saswp_car_schema_price_'.$schema_id, 'saswp_array');
        $input1['offers']['priceCurrency']   = saswp_remove_warnings($all_post_meta, 'saswp_car_schema_currency_'.$schema_id, 'saswp_array');
        $input1['offers']['url']             = saswp_get_permalink();
        $input1['offers']['priceValidUntil'] = isset($all_post_meta['saswp_car_schema_priceValidUntil_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswp_car_schema_priceValidUntil_'.$schema_id][0])):'';
    
        if( isset($all_post_meta['saswp_car_schema_high_price_'.$schema_id][0]) && isset($all_post_meta['saswp_car_schema_low_price_'.$schema_id][0]) ){
            $input1['offers']['@type']           = 'AggregateOffer';
            $input1['offers']['highPrice']       = $all_post_meta['saswp_car_schema_high_price_'.$schema_id][0];
            $input1['offers']['lowPrice']        = $all_post_meta['saswp_car_schema_low_price_'.$schema_id][0];

            if( isset($all_post_meta['saswp_car_schema_offer_count_'.$schema_id][0]) ){
                $input1['offers']['offerCount'] = $all_post_meta['saswp_car_schema_offer_count_'.$schema_id][0];
            }

        }       

    }
                                            
    if(isset($all_post_meta['saswp_car_schema_model_'.$schema_id])){
        $input1['model'] = esc_attr($all_post_meta['saswp_car_schema_model_'.$schema_id][0]);  
    }
    if(isset($all_post_meta['saswp_car_schema_body_type_'.$schema_id])){
        $input1['bodyType'] = esc_attr($all_post_meta['saswp_car_schema_body_type_'.$schema_id][0]);  
    }
    if(isset($all_post_meta['saswp_car_schema_fuel_type_'.$schema_id])){
        $input1['fuelType'] = esc_attr($all_post_meta['saswp_car_schema_fuel_type_'.$schema_id][0]);  
    }

    if(isset($all_post_meta['saswp_car_schema_fuel_efficiency_'.$schema_id])){
        $input1['fuelEfficiency'] = esc_attr($all_post_meta['saswp_car_schema_fuel_efficiency_'.$schema_id][0]);  
    }
    if(isset($all_post_meta['saswp_car_schema_seating_capacity_'.$schema_id])){
        $input1['seatingCapacity'] = esc_attr($all_post_meta['saswp_car_schema_seating_capacity_'.$schema_id][0]);  
    }
    if(isset($all_post_meta['saswp_car_schema_number_of_doors_'.$schema_id])){
        $input1['numberOfdoors'] = esc_attr($all_post_meta['saswp_car_schema_number_of_doors_'.$schema_id][0]);  
    }

    if(isset($all_post_meta['saswp_car_schema_weight_'.$schema_id])){
        $input1['weight'] = esc_attr($all_post_meta['saswp_car_schema_weight_'.$schema_id][0]);  
    }
    if(isset($all_post_meta['saswp_car_schema_width_'.$schema_id])){
        $input1['width'] = esc_attr($all_post_meta['saswp_car_schema_width_'.$schema_id][0]);  
    }
    if(isset($all_post_meta['saswp_car_schema_height_'.$schema_id])){
        $input1['height'] = esc_attr($all_post_meta['saswp_car_schema_height_'.$schema_id][0]);  
    }

    if(isset($all_post_meta['saswp_car_schema_manufacturer_'.$schema_id])){
        $input1['manufacturer'] = esc_attr($all_post_meta['saswp_car_schema_manufacturer_'.$schema_id][0]);  
    }

    if(isset($all_post_meta['saswp_car_schema_mpn_'.$schema_id])){
      $input1['mpn'] = esc_attr($all_post_meta['saswp_car_schema_mpn_'.$schema_id][0]);  
    }    
    
    if(saswp_remove_warnings($all_post_meta, 'saswp_car_schema_enable_rating_'.$schema_id, 'saswp_array') == 1 && saswp_remove_warnings($all_post_meta, 'saswp_car_schema_rating_value_'.$schema_id, 'saswp_array') && saswp_remove_warnings($all_post_meta, 'saswp_car_schema_rating_count_'.$schema_id, 'saswp_array')){   
                         
                                  $input1['aggregateRating'] = array(
                                                    "@type"       => "AggregateRating",
                                                    "ratingValue" => saswp_remove_warnings($all_post_meta, 'saswp_car_schema_rating_value_'.$schema_id, 'saswp_array'),
                                                    "reviewCount" => saswp_remove_warnings($all_post_meta, 'saswp_car_schema_rating_count_'.$schema_id, 'saswp_array')
                                                 );                                       
                                 }
                                     
                                 
                                $itinerary  = get_post_meta($schema_post_id, 'car_reviews_'.$schema_id, true);
                    
                                $itinerary_arr = array();

                                if(!empty($itinerary)){

                                 foreach($itinerary as $review){
                                        
                                  $review_fields = array();
                                  
                                  $review_fields['@type']           = 'Review';
                                  $review_fields['author']['@type'] = 'Person';
                                  $review_fields['author']['name']  = esc_attr($review['saswp_car_reviews_reviewer_name']);

                                  if(isset($review['saswp_car_reviews_created_date'])){
                                    $review_fields['datePublished'] = esc_html($review['saswp_car_reviews_created_date']);
                                  }
                                  if(isset($review['saswp_car_reviews_text'])){
                                    $review_fields['description']   = esc_textarea($review['saswp_car_reviews_text']);
                                  }
                                                                                                                                                                
                                  if(is_int($review['saswp_car_reviews_reviewer_rating'])){
                                      
                                        $review_fields['reviewRating']['@type']   = 'Rating';
                                        $review_fields['reviewRating']['bestRating']   = '5';
                                        $review_fields['reviewRating']['ratingValue']   = esc_attr($review['saswp_car_reviews_reviewer_rating']);
                                        $review_fields['reviewRating']['worstRating']   = '1';
                                  
                                  }
                                                                                                                                                                
                                  $itinerary_arr[] = $review_fields;
                                    }
                                   $input1['review'] = $itinerary_arr;
                                }
                                
                                $service = new saswp_output_service();
                                $car_details = $service->saswp_woocommerce_product_details(get_the_ID());  

                                if(!empty($car_details['car_reviews'])){
                              
                                $reviews = array();
                              
                                 foreach ($car_details['car_reviews'] as $review){
                                                                                  
                                  $review_fields = array();
                                  
                                  $review_fields['@type']           = 'Review';
                                  $review_fields['author']['@type'] = 'Person';
                                  $review_fields['author']['name']  = esc_attr($review['author']);
                                  $review_fields['datePublished']   = esc_html($review['datePublished']);
                                  $review_fields['description']     = $review['description'];
                                                                            
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

function saswp_creative_work_series_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
    
    $slogo = get_post_meta( get_the_ID(), 'saswp_cws_schema_organization_logo_'.$schema_id.'_detail',true);                                 
    $checkIdPro = ((isset($all_post_meta['saswp_cws_schema_id_'.$schema_id][0]) && $all_post_meta['saswp_cws_schema_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_cws_schema_id_'.$schema_id][0] : '');  
    $input1 = array(
    '@context'			            => saswp_context_url(),
    '@type'				            => 'CreativeWorkSeries' ,
    '@id'                           => $checkIdPro,  
    'inLanguage'                    => get_bloginfo('language'),        
    'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_cws_schema_description_'.$schema_id, 'saswp_array'),
    'keywords'                      => saswp_remove_warnings($all_post_meta, 'saswp_cws_schema_keywords_'.$schema_id, 'saswp_array'),
    'name'				            => saswp_remove_warnings($all_post_meta, 'saswp_cws_schema_name_'.$schema_id, 'saswp_array'),
    'url'				            => saswp_remove_warnings($all_post_meta, 'saswp_cws_schema_url_'.$schema_id, 'saswp_array'),
    'datePublished'                 => isset($all_post_meta['saswp_cws_schema_date_published_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_cws_schema_date_published_'.$schema_id][0], get_post_time('h:i:s')) :'',
    'dateModified'                  => isset($all_post_meta['saswp_cws_schema_date_modified_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_cws_schema_date_modified_'.$schema_id][0], get_the_modified_time('h:i:s')) :'',
    'startDate'                     => isset($all_post_meta['saswp_cws_schema_start_date_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_cws_schema_start_date_'.$schema_id][0]) :'',
    'endDate'                       => isset($all_post_meta['saswp_cws_schema_end_date_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_cws_schema_end_date_'.$schema_id][0]) :'',    
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

    if(empty($input1['@id'])){
        unset($input1['@id']);
    }

    $input1['author']['@type']       = 'Person';

    if(isset( $all_post_meta['saswp_cws_schema_author_type_'.$schema_id][0] )){
        $input1['author']['@type']       = $all_post_meta['saswp_cws_schema_author_type_'.$schema_id][0];
    }  

    $input1['author']['name']        = saswp_remove_warnings($all_post_meta, 'saswp_cws_schema_author_name_'.$schema_id, 'saswp_array');
    $input1['author']['description'] = saswp_remove_warnings($all_post_meta, 'saswp_cws_schema_author_description_'.$schema_id, 'saswp_array');
    $input1['author']['url']         = saswp_remove_warnings($all_post_meta, 'saswp_cws_schema_author_url_'.$schema_id, 'saswp_array');
                        
    return $input1;
    
}

function saswp_audio_object_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
    $checkIdPro = ((isset($all_post_meta['saswp_audio_schema_id_'.$schema_id][0]) && $all_post_meta['saswp_audio_schema_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_audio_schema_id_'.$schema_id][0] : '');
    
    $input1 = array(
    '@context'			=> saswp_context_url(),
    '@type'		       => 'AudioObject',
    '@id'                           =>  $checkIdPro,    
    'name'			        => saswp_remove_warnings($all_post_meta, 'saswp_audio_schema_name_'.$schema_id, 'saswp_array'),
    'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_audio_schema_description_'.$schema_id, 'saswp_array'),
    'contentUrl'		        => saswp_remove_warnings($all_post_meta, 'saswp_audio_schema_contenturl_'.$schema_id, 'saswp_array'),
    'duration'		        => saswp_remove_warnings($all_post_meta, 'saswp_audio_schema_duration_'.$schema_id, 'saswp_array'),
    'encodingFormat'		=> saswp_remove_warnings($all_post_meta, 'saswp_audio_schema_encoding_format_'.$schema_id, 'saswp_array'),
    'datePublished'                 => isset($all_post_meta['saswp_audio_schema_date_published_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_audio_schema_date_published_'.$schema_id][0], get_post_time('h:i:s')) :'',
    'dateModified'                  => isset($all_post_meta['saswp_audio_schema_date_modified_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_audio_schema_date_modified_'.$schema_id][0], get_the_modified_time('h:i:s')) :''    
    );

    if(empty($input1['@id'])){
        unset($input1['@id']);
    }

    $input1['author']['@type']       = 'Person';

    if(isset( $all_post_meta['saswp_audio_schema_author_type_'.$schema_id][0] )){
        $input1['author']['@type']       = $all_post_meta['saswp_audio_schema_author_type_'.$schema_id][0];
    }  

    $input1['author']['name']        = saswp_remove_warnings($all_post_meta, 'saswp_audio_schema_author_name_'.$schema_id, 'saswp_array');
    $input1['author']['description'] = saswp_remove_warnings($all_post_meta, 'saswp_audio_schema_author_description_'.$schema_id, 'saswp_array');
    $input1['author']['url']         = saswp_remove_warnings($all_post_meta, 'saswp_audio_schema_author_url_'.$schema_id, 'saswp_array');   
    
    return $input1;
        
}

function saswp_webpage_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
        $input1 = array();
    
        $slogo = get_post_meta( get_the_ID(), 'saswp_webpage_organization_logo_'.$schema_id.'_detail',true);
        $checkIdPro = ((isset($all_post_meta['saswp_webpage_id_'.$schema_id][0]) && $all_post_meta['saswp_webpage_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_webpage_id_'.$schema_id][0] : '');
        $input1 = array(
        '@context'			=> saswp_context_url(),
        '@type'				=> 'WebPage' ,
        '@id'               => $checkIdPro,    
        'inLanguage'        => saswp_remove_warnings($all_post_meta, 'saswp_webpage_inlanguage_'.$schema_id, 'saswp_array'),   
        'name'				=> saswp_remove_warnings($all_post_meta, 'saswp_webpage_name_'.$schema_id, 'saswp_array'),
        'url'				=> saswp_remove_warnings($all_post_meta, 'saswp_webpage_url_'.$schema_id, 'saswp_array'),
        'lastReviewed' 	    => isset($all_post_meta['saswp_webpage_last_reviewed_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_webpage_last_reviewed_'.$schema_id][0], get_post_time('h:i:s')) :'',
        'dateCreated' 	    => isset($all_post_meta['saswp_webpage_date_created_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_webpage_date_created_'.$schema_id][0], get_post_time('h:i:s')) :'',
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
                        'articleSection'	=> saswp_remove_warnings($all_post_meta, 'saswp_webpage_section_'.$schema_id, 'saswp_array'),                        
                        'datePublished' 	=> isset($all_post_meta['saswp_webpage_date_published_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_webpage_date_published_'.$schema_id][0], get_post_time('h:i:s')) :'',
                        'dateModified'		=> isset($all_post_meta['saswp_webpage_date_modified_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_webpage_date_modified_'.$schema_id][0], get_the_modified_time('h:i:s')) :'',                        
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

        if(empty($input1['@id'])){
            unset($input1['@id']);
        }

        $input1['mainEntity']['author']['@type']       = 'Person';

        if(isset( $all_post_meta['saswp_webpage_author_type_'.$schema_id][0] )){
            $input1['mainEntity']['author']['@type']       = $all_post_meta['saswp_webpage_author_type_'.$schema_id][0];
        }  

        $input1['mainEntity']['author']['name']        = saswp_remove_warnings($all_post_meta, 'saswp_webpage_author_name_'.$schema_id, 'saswp_array');
        $input1['mainEntity']['author']['description'] = saswp_remove_warnings($all_post_meta, 'saswp_webpage_author_description_'.$schema_id, 'saswp_array');
        $input1['mainEntity']['author']['url']         = saswp_remove_warnings($all_post_meta, 'saswp_webpage_author_url_'.$schema_id, 'saswp_array');   

        if(isset($all_post_meta['saswp_webpage_speakable_'.$schema_id]) && $all_post_meta['saswp_webpage_speakable_'.$schema_id][0] == 1){

            $input1['speakable']['@type'] = 'SpeakableSpecification';
            $input1['speakable']['xpath'] = array(
                 "/html/head/title",
                 "/html/head/meta[@name='description']/@content"
            );

        }
    
    return $input1;
    
}

function saswp_itempage_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();

    $slogo = get_post_meta( get_the_ID(), 'saswp_itempage_organization_logo_'.$schema_id.'_detail',true);
    $checkIdPro = ((isset($all_post_meta['saswp_itempage_id_'.$schema_id][0]) && $all_post_meta['saswp_itempage_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_itempage_id_'.$schema_id][0] : get_permalink().'#ItemPage');
    $input1 = array(
    '@context'			=> saswp_context_url(),
    '@type'				=> 'ItemPage' ,
    '@id'               => $checkIdPro,    
    'inLanguage'        => saswp_remove_warnings($all_post_meta, 'saswp_itempage_inlanguage_'.$schema_id, 'saswp_array'),   
    'name'				=> saswp_remove_warnings($all_post_meta, 'saswp_itempage_name_'.$schema_id, 'saswp_array'),
    'url'				=> saswp_remove_warnings($all_post_meta, 'saswp_itempage_url_'.$schema_id, 'saswp_array'),
    'lastReviewed' 	    => isset($all_post_meta['saswp_itempage_last_reviewed_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_itempage_last_reviewed_'.$schema_id][0], get_post_time('h:i:s')) :'',
    'dateCreated' 	    => isset($all_post_meta['saswp_itempage_date_created_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_itempage_date_created_'.$schema_id][0], get_post_time('h:i:s')) :'',
    'reviewedBy'	    => array(
        '@type'			=> 'Organization',
        'logo' 			=> array(
                '@type'		=> 'ImageObject',
                'url'		=> saswp_remove_warnings($all_post_meta, 'saswp_itempage_organization_logo_'.$schema_id, 'saswp_array'),
                'width'		=> saswp_remove_warnings($slogo, 'width', 'saswp_string'),
                'height'	=> saswp_remove_warnings($slogo, 'height', 'saswp_string'),
                ),
        'name'			=> saswp_remove_warnings($all_post_meta, 'saswp_itempage_organization_name_'.$schema_id, 'saswp_array'),
     ),        
    'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_itempage_description_'.$schema_id, 'saswp_array'),
    'mainEntity'                    => array(
                    '@type'			=> 'Article',
                    'mainEntityOfPage'	=> wp_strip_all_tags(strip_shortcodes(saswp_remove_warnings($all_post_meta, 'saswp_itempage_main_entity_of_page_'.$schema_id, 'saswp_array'))),
                    'image'			=> saswp_remove_warnings($all_post_meta, 'saswp_itempage_image_'.$schema_id, 'saswp_array'),
                    'headline'		=> saswp_remove_warnings($all_post_meta, 'saswp_itempage_headline_'.$schema_id, 'saswp_array'),
                    'description'		=> saswp_remove_warnings($all_post_meta, 'saswp_itempage_description_'.$schema_id, 'saswp_array'),
                    'keywords'		=> saswp_remove_warnings($all_post_meta, 'saswp_itempage_keywords_'.$schema_id, 'saswp_array'),
                    'articleSection'	=> saswp_remove_warnings($all_post_meta, 'saswp_itempage_section_'.$schema_id, 'saswp_array'),                        
                    'datePublished' 	=> isset($all_post_meta['saswp_itempage_date_published_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_itempage_date_published_'.$schema_id][0], get_post_time('h:i:s')) :'',
                    'dateModified'		=> isset($all_post_meta['saswp_itempage_date_modified_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_itempage_date_modified_'.$schema_id][0], get_the_modified_time('h:i:s')) :'',                        
                    'publisher'			=> array(
                            '@type'			=> 'Organization',
                            'logo' 			=> array(
                                    '@type'		=> 'ImageObject',
                                    'url'		=> saswp_remove_warnings($all_post_meta, 'saswp_itempage_organization_logo_'.$schema_id, 'saswp_array'),
                                    'width'		=> saswp_remove_warnings($slogo, 'width', 'saswp_string'),
                                    'height'	=> saswp_remove_warnings($slogo, 'height', 'saswp_string'),
                                    ),
                            'name'			=> saswp_remove_warnings($all_post_meta, 'saswp_itempage_organization_name_'.$schema_id, 'saswp_array'),
                    ),
            ),


    );

    if(empty($input1['@id'])){
        unset($input1['@id']);
    }

    $input1['mainEntity']['author']['@type']       = 'Person';

    if(isset( $all_post_meta['saswp_itempage_author_type_'.$schema_id][0] )){
        $input1['mainEntity']['author']['@type']       = $all_post_meta['saswp_itempage_author_type_'.$schema_id][0];
    }  

    $input1['mainEntity']['author']['name']        = saswp_remove_warnings($all_post_meta, 'saswp_itempage_author_name_'.$schema_id, 'saswp_array');
    $input1['mainEntity']['author']['description'] = saswp_remove_warnings($all_post_meta, 'saswp_itempage_author_description_'.$schema_id, 'saswp_array');
    $input1['mainEntity']['author']['url']         = saswp_remove_warnings($all_post_meta, 'saswp_itempage_author_url_'.$schema_id, 'saswp_array');   

    if(isset($all_post_meta['saswp_itempage_speakable_'.$schema_id]) && $all_post_meta['saswp_itempage_speakable_'.$schema_id][0] == 1){

        $input1['speakable']['@type'] = 'SpeakableSpecification';
        $input1['speakable']['xpath'] = array(
             "/html/head/title",
             "/html/head/meta[@name='description']/@content"
        );

    }

return $input1;

}

function saswp_medicalwebpage_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();

    $slogo = get_post_meta( get_the_ID(), 'saswp_medicalwebpage_organization_logo_'.$schema_id.'_detail',true);
    $input1 = array(
    '@context'			=> saswp_context_url(),
    '@type'				=> 'MedicalWebPage' ,
    '@id'                           => get_permalink().'#medicalwebpage',     
    'inLanguage'                    => get_bloginfo('language'),    
    'name'				=> saswp_remove_warnings($all_post_meta, 'saswp_medicalwebpage_name_'.$schema_id, 'saswp_array'),
    'url'				=> saswp_remove_warnings($all_post_meta, 'saswp_medicalwebpage_url_'.$schema_id, 'saswp_array'),
    'lastReviewed' 	    => isset($all_post_meta['saswp_medicalwebpage_last_reviewed_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_medicalwebpage_last_reviewed_'.$schema_id][0], get_post_time('h:i:s')) :'',
    'dateCreated' 	    => isset($all_post_meta['saswp_medicalwebpage_date_created_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_medicalwebpage_date_created_'.$schema_id][0], get_post_time('h:i:s')) :'',
    'reviewedBy'	    => array(
        '@type'			=> 'Organization',
        'logo' 			=> array(
                '@type'		=> 'ImageObject',
                'url'		=> saswp_remove_warnings($all_post_meta, 'saswp_medicalwebpage_organization_logo_'.$schema_id, 'saswp_array'),
                'width'		=> saswp_remove_warnings($slogo, 'width', 'saswp_string'),
                'height'	=> saswp_remove_warnings($slogo, 'height', 'saswp_string'),
                ),
        'name'			=> saswp_remove_warnings($all_post_meta, 'saswp_medicalwebpage_organization_name_'.$schema_id, 'saswp_array'),
     ),        
    'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_medicalwebpage_description_'.$schema_id, 'saswp_array'),
    'mainEntity'                    => array(
                    '@type'			=> 'Article',
                    'mainEntityOfPage'	=> wp_strip_all_tags(strip_shortcodes(saswp_remove_warnings($all_post_meta, 'saswp_webpage_main_entity_of_page_'.$schema_id, 'saswp_array'))),
                    'image'			=> saswp_remove_warnings($all_post_meta, 'saswp_medicalwebpage_image_'.$schema_id, 'saswp_array'),
                    'headline'		=> saswp_remove_warnings($all_post_meta, 'saswp_medicalwebpage_headline_'.$schema_id, 'saswp_array'),
                    'description'		=> saswp_remove_warnings($all_post_meta, 'saswp_medicalwebpage_description_'.$schema_id, 'saswp_array'),
                    'keywords'		=> saswp_remove_warnings($all_post_meta, 'saswp_medicalwebpage_keywords_'.$schema_id, 'saswp_array'),
                    'articleSection'	=> saswp_remove_warnings($all_post_meta, 'saswp_medicalwebpage_section_'.$schema_id, 'saswp_array'),                        
                    'datePublished' 	=> isset($all_post_meta['saswp_medicalwebpage_date_published_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_medicalwebpage_date_published_'.$schema_id][0], get_post_time('h:i:s')) :'',
                    'dateModified'		=> isset($all_post_meta['saswp_medicalwebpage_date_modified_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_medicalwebpage_date_modified_'.$schema_id][0], get_the_modified_time('h:i:s')) :'',                        
                    'publisher'			=> array(
                            '@type'			=> 'Organization',
                            'logo' 			=> array(
                                    '@type'		=> 'ImageObject',
                                    'url'		=> saswp_remove_warnings($all_post_meta, 'saswp_medicalwebpage_organization_logo_'.$schema_id, 'saswp_array'),
                                    'width'		=> saswp_remove_warnings($slogo, 'width', 'saswp_string'),
                                    'height'	=> saswp_remove_warnings($slogo, 'height', 'saswp_string'),
                                    ),
                            'name'			=> saswp_remove_warnings($all_post_meta, 'saswp_medicalwebpage_organization_name_'.$schema_id, 'saswp_array'),
                    ),
            ),


    );

    $input1['mainEntity']['author']['@type']       = 'Person';

    if(isset( $all_post_meta['saswp_medicalwebpage_author_type_'.$schema_id][0] )){
        $input1['mainEntity']['author']['@type']       = $all_post_meta['saswp_medicalwebpage_author_type_'.$schema_id][0];
    }  

    $input1['mainEntity']['author']['name']        = saswp_remove_warnings($all_post_meta, 'saswp_medicalwebpage_author_name_'.$schema_id, 'saswp_array');
    $input1['mainEntity']['author']['description'] = saswp_remove_warnings($all_post_meta, 'saswp_medicalwebpage_author_description_'.$schema_id, 'saswp_array');
    $input1['mainEntity']['author']['url']         = saswp_remove_warnings($all_post_meta, 'saswp_medicalwebpage_author_url_'.$schema_id, 'saswp_array');   

    if(isset($all_post_meta['saswp_medicalwebpage_speakable_'.$schema_id]) && $all_post_meta['saswp_medicalwebpage_speakable_'.$schema_id][0] == 1){

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

    $slogo        = get_post_meta( get_the_ID(), 'saswp_special_announcement_organization_logo_'.$schema_id.'_detail',true);
    $location_img = get_post_meta( get_the_ID(), 'saswp_special_announcement_location_image_'.$schema_id.'_detail',true);    
    $checkIdPro = ((isset($all_post_meta['saswp_special_announcement_id_'.$schema_id][0]) && $all_post_meta['saswp_special_announcement_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_special_announcement_id_'.$schema_id][0] : '');
    $input1 = array(
            '@context'			=> saswp_context_url(),
            '@type'				=> 'SpecialAnnouncement',
            '@id'                           => $checkIdPro,
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
            'expires'                   => isset($all_post_meta['saswp_special_announcement_date_modified_'.$schema_id][0])? saswp_format_date_time($all_post_meta['saswp_special_announcement_date_expires_'.$schema_id][0], get_the_modified_time('h:i:s')):''                 
            );

        if(empty($input1['@id'])){
            unset($input1['@id']);
        }

        $input1['author']['@type']       = 'Person';

        if(isset( $all_post_meta['saswp_special_announcement_author_type_'.$schema_id][0] )){
            $input1['author']['@type']       = $all_post_meta['saswp_special_announcement_author_type_'.$schema_id][0];
        }  

        $input1['author']['name']        = saswp_remove_warnings($all_post_meta, 'saswp_special_announcement_author_name_'.$schema_id, 'saswp_array');
        $input1['author']['description'] = saswp_remove_warnings($all_post_meta, 'saswp_special_announcement_author_description_'.$schema_id, 'saswp_array');
        $input1['author']['url']         = saswp_remove_warnings($all_post_meta, 'saswp_special_announcement_author_url_'.$schema_id, 'saswp_array');   

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

function saswp_visualartwork_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();    
    $checkIdPro = ((isset($all_post_meta['saswp_visualartwork_id_'.$schema_id][0]) && $all_post_meta['saswp_visualartwork_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_visualartwork_id_'.$schema_id][0] : '');

    $input1 = array(
            '@context'			            => saswp_context_url(),
            '@type'				            => 'VisualArtwork',
            '@id'                           => $checkIdPro,
            'url'				            => saswp_remove_warnings($all_post_meta, 'saswp_visualartwork_url_'.$schema_id, 'saswp_array'),            
            'image'				            => saswp_remove_warnings($all_post_meta, 'saswp_visualartwork_image'.$schema_id, 'saswp_array'),
            'name'			                => saswp_remove_warnings($all_post_meta, 'saswp_visualartwork_name_'.$schema_id, 'saswp_array'),
            'alternateName'			        => saswp_remove_warnings($all_post_meta, 'saswp_visualartwork_alternate_name_'.$schema_id, 'saswp_array'),
            'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_visualartwork_description_'.$schema_id, 'saswp_array'),            
            'dateCreated'                   => isset($all_post_meta['saswp_visualartwork_date_created_'.$schema_id][0])? saswp_format_date_time($all_post_meta['saswp_visualartwork_date_created_'.$schema_id][0], get_post_time('h:i:s')):'',            
            'artform'                       => saswp_remove_warnings($all_post_meta, 'saswp_visualartwork_artform_'.$schema_id, 'saswp_array'),            
            'artEdition'                    => saswp_remove_warnings($all_post_meta, 'saswp_visualartwork_artedition_'.$schema_id, 'saswp_array'),            
            'artworkSurface'                => saswp_remove_warnings($all_post_meta, 'saswp_visualartwork_artwork_surface_'.$schema_id, 'saswp_array'),            

    );
    
    if(empty($input1['@id'])){
        unset($input1['@id']);
    }

    if(isset( $all_post_meta['saswp_visualartwork_artmedium_'.$schema_id][0] )){
        $input1['artMedium']       = explode(',', $all_post_meta['saswp_visualartwork_artmedium_'.$schema_id][0]);
    }
    if(isset( $all_post_meta['saswp_visualartwork_size_'.$schema_id][0] )){
        $input1['size']       = $all_post_meta['saswp_visualartwork_size_'.$schema_id][0];
    }
    if(isset( $all_post_meta['saswp_visualartwork_license_'.$schema_id][0] )){
        $input1['license']       = $all_post_meta['saswp_visualartwork_license_'.$schema_id][0];
    }
    if(isset( $all_post_meta['saswp_visualartwork_width_'.$schema_id][0] )){
        $input1['width']['@type']       = 'Distance';
        $input1['width']['name']       = $all_post_meta['saswp_visualartwork_width_'.$schema_id][0];
    }
    if(isset( $all_post_meta['saswp_visualartwork_height_'.$schema_id][0] )){
        $input1['height']['@type']       = 'Distance';
        $input1['height']['name']       = $all_post_meta['saswp_visualartwork_height_'.$schema_id][0];
    }

    $input1['creator']['@type']       = 'Person';

    if(isset( $all_post_meta['saswp_photograph_author_type_'.$schema_id][0] )){
        $input1['creator']['@type']       = $all_post_meta['saswp_photograph_author_type_'.$schema_id][0];
    }  

    $input1['creator']['name']        = saswp_remove_warnings($all_post_meta, 'saswp_photograph_author_name_'.$schema_id, 'saswp_array');
    $input1['creator']['description'] = saswp_remove_warnings($all_post_meta, 'saswp_photograph_author_description_'.$schema_id, 'saswp_array');
    $input1['creator']['url']         = saswp_remove_warnings($all_post_meta, 'saswp_photograph_author_url_'.$schema_id, 'saswp_array');   
                
    return $input1;

}

function saswp_photograph_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
    $author_image = get_post_meta( get_the_ID(), 'saswp_photograph_author_image_'.$schema_id.'_detail',true);
    $slogo = get_post_meta( get_the_ID(), 'saswp_photograph_organization_logo_'.$schema_id.'_detail',true);
    $checkIdPro = ((isset($all_post_meta['saswp_photograph_id_'.$schema_id][0]) && $all_post_meta['saswp_photograph_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_photograph_id_'.$schema_id][0] : '');

    $input1 = array(
            '@context'			            => saswp_context_url(),
            '@type'				            => 'Photograph',
            '@id'                           => $checkIdPro,
            'url'				            => saswp_remove_warnings($all_post_meta, 'saswp_photograph_url_'.$schema_id, 'saswp_array'),
            'inLanguage'                    => saswp_remove_warnings($all_post_meta, 'saswp_photograph_inlanguage_'.$schema_id, 'saswp_array'),            
            'image'				            => saswp_remove_warnings($all_post_meta, 'saswp_photograph_image_'.$schema_id, 'saswp_array'),
            'headline'			            => saswp_remove_warnings($all_post_meta, 'saswp_photograph_headline_'.$schema_id, 'saswp_array'),
            'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_photograph_description_'.$schema_id, 'saswp_array'),            
            'datePublished'                 => isset($all_post_meta['saswp_photograph_date_published_'.$schema_id][0])? saswp_format_date_time($all_post_meta['saswp_photograph_date_published_'.$schema_id][0], get_post_time('h:i:s')):'',
            'dateModified'                  => isset($all_post_meta['saswp_photograph_date_modified_'.$schema_id][0])? saswp_format_date_time($all_post_meta['saswp_photograph_date_modified_'.$schema_id][0], get_the_modified_time('h:i:s')):'',                                

    );

    if(empty($input1['@id'])){
        unset($input1['@id']);
    }

    $input1['author']['@type']       = 'Person';

    if(isset( $all_post_meta['saswp_photograph_author_type_'.$schema_id][0] )){
        $input1['author']['@type']       = $all_post_meta['saswp_photograph_author_type_'.$schema_id][0];
    }  

    $input1['author']['name']        = saswp_remove_warnings($all_post_meta, 'saswp_photograph_author_name_'.$schema_id, 'saswp_array');
    $input1['author']['honorificSuffix']        = saswp_remove_warnings($all_post_meta, 'saswp_photograph_author_honorific_suffix_'.$schema_id, 'saswp_array');
    $input1['author']['description'] = saswp_remove_warnings($all_post_meta, 'saswp_photograph_author_description_'.$schema_id, 'saswp_array');
    $input1['author']['url']         = saswp_remove_warnings($all_post_meta, 'saswp_photograph_author_url_'.$schema_id, 'saswp_array');   
    
    $input1['author']['image']['@type']   = 'ImageObject';
    $input1['author']['image']['url']     = saswp_remove_warnings($all_post_meta, 'saswp_photograph_author_image_'.$schema_id, 'saswp_array');       
    $input1['author']['image']['height']  = isset($author_image['height']) ? $author_image['height'] : '';
    $input1['author']['image']['width']   = isset($author_image['width']) ? $author_image['width'] : '';

    $input1['author']['JobTitle']         = saswp_remove_warnings($all_post_meta, 'saswp_photograph_author_jobtitle_'.$schema_id, 'saswp_array');   

    if(!empty($all_post_meta['saswp_photograph_editor_type_'.$schema_id][0]) && isset($all_post_meta['saswp_photograph_editor_type_'.$schema_id][0])){
        $input1['editor']['@type']       = 'Person';
        if(!empty( $all_post_meta['saswp_photograph_editor_type_'.$schema_id][0] )){
            $input1['editor']['@type']       = $all_post_meta['saswp_photograph_editor_type_'.$schema_id][0];
        }  
        if(!empty( $all_post_meta['saswp_photograph_editor_name_'.$schema_id][0] )){
            $input1['editor']['name']        = saswp_remove_warnings($all_post_meta, 'saswp_photograph_editor_name_'.$schema_id, 'saswp_array');
        }  
        if(!empty( $all_post_meta['saswp_photograph_editor_honorific_suffix_'.$schema_id][0] )){
            $input1['editor']['honorificSuffix']        = saswp_remove_warnings($all_post_meta, 'saswp_photograph_editor_honorific_suffix_'.$schema_id, 'saswp_array');
        }  
        if(!empty( $all_post_meta['saswp_photograph_editor_description_'.$schema_id][0] )){
            $input1['editor']['description'] = saswp_remove_warnings($all_post_meta, 'saswp_photograph_editor_description_'.$schema_id, 'saswp_array');
        }  
        if(!empty( $all_post_meta['saswp_photograph_editor_url_'.$schema_id][0] )){
            $input1['editor']['url']         = saswp_remove_warnings($all_post_meta, 'saswp_photograph_editor_url_'.$schema_id, 'saswp_array');
        }  
        if(!empty( $all_post_meta['saswp_photograph_editor_image_'.$schema_id][0] )){
            $input1['editor']['image']['@type']   = 'ImageObject';
            $input1['editor']['image']['url']     = saswp_remove_warnings($all_post_meta, 'saswp_photograph_editor_image_'.$schema_id, 'saswp_array');       
            $input1['editor']['image']['height']  = isset($author_image['height']) ? $author_image['height'] : '';
            $input1['editor']['image']['width']   = isset($author_image['width']) ? $author_image['width'] : '';
        }  
    }
      

    if(!empty($all_post_meta['saswp_photograph_reviewedby_type_'.$schema_id][0]) && isset($all_post_meta['saswp_photograph_reviewedby_type_'.$schema_id][0])){
        $input1['reviewedBy']['@type']       = 'Person';
        if(!empty( $all_post_meta['saswp_photograph_reviewedby_type_'.$schema_id][0] )){
            $input1['reviewedBy']['@type']       = $all_post_meta['saswp_photograph_reviewedby_type_'.$schema_id][0];
        }  
        if(!empty( $all_post_meta['saswp_photograph_reviewedby_name_'.$schema_id][0] )){
            $input1['reviewedBy']['name']        = saswp_remove_warnings($all_post_meta, 'saswp_photograph_reviewedby_name_'.$schema_id, 'saswp_array');
        }  
        if(!empty( $all_post_meta['saswp_photograph_reviewedby_honorific_suffix_'.$schema_id][0] )){
            $input1['reviewedBy']['honorificSuffix']        = saswp_remove_warnings($all_post_meta, 'saswp_photograph_reviewedby_honorific_suffix_'.$schema_id, 'saswp_array');
        }  
        if(!empty( $all_post_meta['saswp_photograph_reviewedby_description_'.$schema_id][0] )){
            $input1['reviewedBy']['description'] = saswp_remove_warnings($all_post_meta, 'saswp_photograph_reviewedby_description_'.$schema_id, 'saswp_array');
        }  
        if(!empty( $all_post_meta['saswp_photograph_reviewedby_url_'.$schema_id][0] )){
            $input1['reviewedBy']['url']         = saswp_remove_warnings($all_post_meta, 'saswp_photograph_reviewedby_url_'.$schema_id, 'saswp_array');   
        }  
    }

    if( !empty($all_post_meta['saswp_photograph_knowsabout_'.$schema_id][0]) && isset( $all_post_meta['saswp_photograph_knowsabout_'.$schema_id][0] )){
        $input1['knowsAbout']       = explode(',', $all_post_meta['saswp_photograph_knowsabout_'.$schema_id][0]);
    }
    if( !empty($all_post_meta['saswp_photograph_about_'.$schema_id][0]) && isset( $all_post_meta['saswp_photograph_about_'.$schema_id][0] )){
        $input1['about']['@type']       = 'Event';
        $input1['about']['name']       = explode(',', $all_post_meta['saswp_photograph_about_'.$schema_id][0]);
    }
    if( !empty($all_post_meta['saswp_photograph_alumniof_'.$schema_id][0]) && isset( $all_post_meta['saswp_photograph_alumniof_'.$schema_id][0] )){
        $itemlist = explode(',', $all_post_meta['saswp_photograph_alumniof_'.$schema_id][0]);
        foreach ($itemlist as $key => $list){
            $vnewarr['@type'] = 'Organization';
            $vnewarr['Name']   = $list;   
            $input1['alumniOf'][] = $vnewarr;
        }
    }

    if(isset($all_post_meta['saswp_photograph_organization_logo_'.$schema_id][0]) || isset($all_post_meta['saswp_photograph_organization_name_'.$schema_id][0])){
    
        $input1['publisher']['@type']          = 'Organization';
        $input1['publisher']['logo']['@type']  = 'ImageObject';
        $input1['publisher']['logo']['url']    = saswp_remove_warnings($all_post_meta, 'saswp_photograph_organization_logo_'.$schema_id, 'saswp_array');
        $input1['publisher']['logo']['width']  = saswp_remove_warnings($slogo, 'width', 'saswp_string');
        $input1['publisher']['logo']['height'] = saswp_remove_warnings($slogo, 'height', 'saswp_string');
        $input1['publisher']['name']           = saswp_remove_warnings($all_post_meta, 'saswp_photograph_organization_name_'.$schema_id, 'saswp_array');
        
    }
            
    return $input1;

}

function saswp_article_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
        $input1 = array();
        $author_image = get_post_meta( get_the_ID(), 'saswp_article_author_image_'.$schema_id.'_detail',true);
        $slogo = get_post_meta( get_the_ID(), 'saswp_article_organization_logo_'.$schema_id.'_detail',true);
        $checkIdPro = ((isset($all_post_meta['saswp_article_id_'.$schema_id][0]) && $all_post_meta['saswp_article_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_article_id_'.$schema_id][0] : '');
        $input1 = array(
                '@context'			            => saswp_context_url(),
                '@type'				            => 'Article',
                '@id'                           => $checkIdPro,
                'url'				            => saswp_remove_warnings($all_post_meta, 'saswp_article_url_'.$schema_id, 'saswp_array'),
                'inLanguage'                    => saswp_remove_warnings($all_post_meta, 'saswp_article_inlanguage_'.$schema_id, 'saswp_array'),
                'mainEntityOfPage'              => saswp_remove_warnings($all_post_meta, 'saswp_article_main_entity_of_page_'.$schema_id, 'saswp_array'),
                'image'				            => saswp_remove_warnings($all_post_meta, 'saswp_article_image_'.$schema_id, 'saswp_array'),
                'headline'			            => saswp_remove_warnings($all_post_meta, 'saswp_article_headline_'.$schema_id, 'saswp_array'),
                'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_article_description_'.$schema_id, 'saswp_array'),
                'articleSection'                => saswp_remove_warnings($all_post_meta, 'saswp_article_section_'.$schema_id, 'saswp_array'),
                'articleBody'                   => isset($all_post_meta['saswp_article_body_'.$schema_id][0]) ? wp_strip_all_tags(strip_shortcodes($all_post_meta['saswp_article_body_'.$schema_id][0])) : '',
                'keywords'		                => saswp_remove_warnings($all_post_meta, 'saswp_article_keywords_'.$schema_id, 'saswp_array'),                
                'datePublished'                 => isset($all_post_meta['saswp_article_date_published_'.$schema_id][0])? saswp_format_date_time($all_post_meta['saswp_article_date_published_'.$schema_id][0], get_post_time('h:i:s')):'',
                'dateModified'                  => isset($all_post_meta['saswp_article_date_modified_'.$schema_id][0])? saswp_format_date_time($all_post_meta['saswp_article_date_modified_'.$schema_id][0], get_the_modified_time('h:i:s')):'',                                

        );

        if(empty($input1['@id'])){
            unset($input1['@id']);
        }

        $input1['author']['@type']       = 'Person';

        if(isset( $all_post_meta['saswp_article_author_type_'.$schema_id][0] )){
            $input1['author']['@type']       = $all_post_meta['saswp_article_author_type_'.$schema_id][0];
        }  

        $input1['author']['name']        = saswp_remove_warnings($all_post_meta, 'saswp_article_author_name_'.$schema_id, 'saswp_array');
        $input1['author']['honorificSuffix']        = saswp_remove_warnings($all_post_meta, 'saswp_article_author_honorific_suffix_'.$schema_id, 'saswp_array');
        $input1['author']['description'] = saswp_remove_warnings($all_post_meta, 'saswp_article_author_description_'.$schema_id, 'saswp_array');
        $input1['author']['url']         = saswp_remove_warnings($all_post_meta, 'saswp_article_author_url_'.$schema_id, 'saswp_array');   

        $input1['author']['JobTitle']    = saswp_remove_warnings($all_post_meta, 'saswp_article_author_jobtitle_'.$schema_id, 'saswp_array');   
        
        $input1['author']['image']['@type']   = 'ImageObject';
        $input1['author']['image']['url']     = saswp_remove_warnings($all_post_meta, 'saswp_article_author_image_'.$schema_id, 'saswp_array');       
        $input1['author']['image']['height']  = isset($author_image['height']) ? $author_image['height'] : '';
        $input1['author']['image']['width']   = isset($author_image['width']) ? $author_image['width'] : '';

        if(isset($all_post_meta['saswp_article_author_social_profile_'.$schema_id][0]) && !empty($all_post_meta['saswp_article_author_social_profile_'.$schema_id][0])){
            $explode_sp = saswp_remove_warnings($all_post_meta, 'saswp_article_author_social_profile_'.$schema_id, 'saswp_array');
            $explode_sp = explode(',',$explode_sp);
            if(!empty($explode_sp) && is_array($explode_sp)){
                $input1['author']['sameAs'] = $explode_sp; 
            }
        }

        if(!empty($all_post_meta['saswp_article_editor_type_'.$schema_id][0]) && isset($all_post_meta['saswp_article_editor_type_'.$schema_id][0])){
            $input1['editor']['@type']       = 'Person';
            if(!empty( $all_post_meta['saswp_article_editor_type_'.$schema_id][0] )){
                $input1['editor']['@type']       = $all_post_meta['saswp_article_editor_type_'.$schema_id][0];
            }  
            if(!empty( $all_post_meta['saswp_article_editor_name_'.$schema_id][0] )){
                $input1['editor']['name']        = saswp_remove_warnings($all_post_meta, 'saswp_article_editor_name_'.$schema_id, 'saswp_array');
            }  
            if(!empty( $all_post_meta['saswp_article_editor_honorific_suffix_'.$schema_id][0] )){
                $input1['editor']['honorificSuffix']        = saswp_remove_warnings($all_post_meta, 'saswp_article_editor_honorific_suffix_'.$schema_id, 'saswp_array');
            }  
            if(!empty( $all_post_meta['saswp_article_editor_description_'.$schema_id][0] )){
                $input1['editor']['description'] = saswp_remove_warnings($all_post_meta, 'saswp_article_editor_description_'.$schema_id, 'saswp_array');
            }  
            if(!empty( $all_post_meta['saswp_article_editor_url_'.$schema_id][0] )){
                $input1['editor']['url']         = saswp_remove_warnings($all_post_meta, 'saswp_article_editor_url_'.$schema_id, 'saswp_array');  
            }  
            if(!empty( $all_post_meta['saswp_article_editor_image_'.$schema_id][0] )){
                $input1['editor']['image']['@type']   = 'ImageObject';
                $input1['editor']['image']['url']     = saswp_remove_warnings($all_post_meta, 'saswp_article_editor_image_'.$schema_id, 'saswp_array');       
                $input1['editor']['image']['height']  = isset($author_image['height']) ? $author_image['height'] : '';
                $input1['editor']['image']['width']   = isset($author_image['width']) ? $author_image['width'] : '';  
            }  
            if(isset($all_post_meta['saswp_article_author_social_profile_'.$schema_id][0]) && !empty($all_post_meta['saswp_article_author_social_profile_'.$schema_id][0])){
                $explode_sp = saswp_remove_warnings($all_post_meta, 'saswp_article_author_social_profile_'.$schema_id, 'saswp_array');
                $explode_sp = explode(',',$explode_sp);
                if(!empty($explode_sp) && is_array($explode_sp)){
                    $input1['editor']['sameAs'] = $explode_sp; 
                }
            }
        }
            

        if(!empty($all_post_meta['saswp_article_reviewedby_type_'.$schema_id][0]) && isset($all_post_meta['saswp_article_reviewedby_type_'.$schema_id][0])){
            $input1['reviewedBy']['@type']       = 'Person';
            if(!empty( $all_post_meta['saswp_article_reviewedby_type_'.$schema_id][0] )){
                $input1['reviewedBy']['@type']       = $all_post_meta['saswp_article_reviewedby_type_'.$schema_id][0];
            }  
            if(!empty( $all_post_meta['saswp_article_reviewedby_name_'.$schema_id][0] )){
                $input1['reviewedBy']['name']        = saswp_remove_warnings($all_post_meta, 'saswp_article_reviewedby_name_'.$schema_id, 'saswp_array');
            }
            if(!empty( $all_post_meta['saswp_article_reviewedby_honorific_suffix_'.$schema_id][0] )){
                $input1['reviewedBy']['honorificSuffix']        = saswp_remove_warnings($all_post_meta, 'saswp_article_reviewedby_honorific_suffix_'.$schema_id, 'saswp_array');
            }
            if(!empty( $all_post_meta['saswp_article_reviewedby_description_'.$schema_id][0] )){
                $input1['reviewedBy']['description'] = saswp_remove_warnings($all_post_meta, 'saswp_article_reviewedby_description_'.$schema_id, 'saswp_array');
            }
            if(!empty( $all_post_meta['saswp_article_reviewedby_url_'.$schema_id][0] )){
                $input1['reviewedBy']['url']         = saswp_remove_warnings($all_post_meta, 'saswp_article_reviewedby_url_'.$schema_id, 'saswp_array');   
            }
        }
            if( !empty($all_post_meta['saswp_article_about_'.$schema_id][0]) && isset( $all_post_meta['saswp_article_about_'.$schema_id][0] )){

                $explode_about = explode(',', $all_post_meta['saswp_article_about_'.$schema_id][0]);
                    if(!empty($explode_about)){
                        $about_arr = array();
                        foreach($explode_about as $val){
                            $about_arr[] = array(
                                        '@type' => 'Thing',
                                        'name'  => $val
                            );
                        }
                        $input1['about'] = $about_arr;
                    }                                            
            }
            if( !empty($all_post_meta['saswp_article_knowsabout_'.$schema_id][0]) && isset( $all_post_meta['saswp_article_knowsabout_'.$schema_id][0] )){
                $input1['knowsAbout']       = explode(',', $all_post_meta['saswp_article_knowsabout_'.$schema_id][0]);
            }

        if(!empty($all_post_meta['saswp_article_alumniof_'.$schema_id][0]) && isset( $all_post_meta['saswp_article_alumniof_'.$schema_id][0] )){
            $itemlist = explode(',', $all_post_meta['saswp_article_alumniof_'.$schema_id][0]);
            foreach ($itemlist as $key => $list){
                $vnewarr['@type'] = 'Organization';
                $vnewarr['Name']   = $list;   
                $input1['alumniOf'][] = $vnewarr;
            }
        }
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


function saswp_scholarlyarticle_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
    $author_image = get_post_meta( get_the_ID(), 'saswp_scholarlyarticle_author_image_'.$schema_id.'_detail',true);
    $slogo = get_post_meta( get_the_ID(), 'saswp_scholarlyarticle_organization_logo_'.$schema_id.'_detail',true);
    $checkIdPro = ((isset($all_post_meta['saswp_scholarlyarticle_id_'.$schema_id][0]) && $all_post_meta['saswp_scholarlyarticle_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_scholarlyarticle_id_'.$schema_id][0] : '');
    $input1 = array(
            '@context'			            => saswp_context_url(),
            '@type'				            => 'ScholarlyArticle',
            '@id'                           => $checkIdPro,
            'url'				            => saswp_remove_warnings($all_post_meta, 'saswp_scholarlyarticle_url_'.$schema_id, 'saswp_array'),
            'inLanguage'                    => saswp_remove_warnings($all_post_meta, 'saswp_scholarlyarticle_inlanguage_'.$schema_id, 'saswp_array'),
            'mainEntityOfPage'              => saswp_remove_warnings($all_post_meta, 'saswp_scholarlyarticle_main_entity_of_page_'.$schema_id, 'saswp_array'),
            'image'				            => saswp_remove_warnings($all_post_meta, 'saswp_scholarlyarticle_image_'.$schema_id, 'saswp_array'),
            'headline'			            => saswp_remove_warnings($all_post_meta, 'saswp_scholarlyarticle_headline_'.$schema_id, 'saswp_array'),
            'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_scholarlyarticle_description_'.$schema_id, 'saswp_array'),
            'articleSection'                => saswp_remove_warnings($all_post_meta, 'saswp_scholarlyarticle_section_'.$schema_id, 'saswp_array'),
            'articleBody'                   => isset($all_post_meta['saswp_scholarlyarticle_body_'.$schema_id][0]) ? wp_strip_all_tags(strip_shortcodes($all_post_meta['saswp_scholarlyarticle_body_'.$schema_id][0])) : '',
            'keywords'		                => saswp_remove_warnings($all_post_meta, 'saswp_scholarlyarticle_keywords_'.$schema_id, 'saswp_array'),                
            'datePublished'                 => isset($all_post_meta['saswp_scholarlyarticle_date_published_'.$schema_id][0])? saswp_format_date_time($all_post_meta['saswp_scholarlyarticle_date_published_'.$schema_id][0], get_post_time('h:i:s')):'',
            'dateModified'                  => isset($all_post_meta['saswp_scholarlyarticle_date_modified_'.$schema_id][0])? saswp_format_date_time($all_post_meta['saswp_scholarlyarticle_date_modified_'.$schema_id][0], get_the_modified_time('h:i:s')):'',                                

    );

    if(empty($input1['@id'])){
        unset($input1['@id']);
    }

    $input1['author']['@type']       = 'Person';

    if(isset( $all_post_meta['saswp_scholarlyarticle_author_type_'.$schema_id][0] )){
        $input1['author']['@type']       = $all_post_meta['saswp_scholarlyarticle_author_type_'.$schema_id][0];
    }  

    $input1['author']['name']        = saswp_remove_warnings($all_post_meta, 'saswp_scholarlyarticle_author_name_'.$schema_id, 'saswp_array');
    $input1['author']['honorificSuffix']        = saswp_remove_warnings($all_post_meta, 'saswp_scholarlyarticle_author_honorific_suffix_'.$schema_id, 'saswp_array');
    $input1['author']['description'] = saswp_remove_warnings($all_post_meta, 'saswp_scholarlyarticle_author_description_'.$schema_id, 'saswp_array');
    $input1['author']['url']         = saswp_remove_warnings($all_post_meta, 'saswp_scholarlyarticle_author_url_'.$schema_id, 'saswp_array');   

    $input1['author']['JobTitle']    = saswp_remove_warnings($all_post_meta, 'saswp_scholarlyarticle_author_jobtitle_'.$schema_id, 'saswp_array');   
    
    $input1['author']['image']['@type']   = 'ImageObject';
    $input1['author']['image']['url']     = saswp_remove_warnings($all_post_meta, 'saswp_scholarlyarticle_author_image_'.$schema_id, 'saswp_array');       
    $input1['author']['image']['height']  = isset($author_image['height']) ? $author_image['height'] : '';
    $input1['author']['image']['width']   = isset($author_image['width']) ? $author_image['width'] : '';

    if(!empty($all_post_meta['saswp_scholarlyarticle_editor_type_'.$schema_id][0]) && isset($all_post_meta['saswp_scholarlyarticle_editor_type_'.$schema_id][0])){
        $input1['editor']['@type']       = 'Person';
        if(!empty( $all_post_meta['saswp_scholarlyarticle_editor_type_'.$schema_id][0] )){
            $input1['editor']['@type']       = $all_post_meta['saswp_scholarlyarticle_editor_type_'.$schema_id][0];
        }  
        if(!empty( $all_post_meta['saswp_scholarlyarticle_editor_name_'.$schema_id][0] )){
            $input1['editor']['name']        = saswp_remove_warnings($all_post_meta, 'saswp_scholarlyarticle_editor_name_'.$schema_id, 'saswp_array');
        }  
        if(!empty( $all_post_meta['saswp_scholarlyarticle_editor_honorific_suffix_'.$schema_id][0] )){
            $input1['editor']['honorificSuffix']        = saswp_remove_warnings($all_post_meta, 'saswp_scholarlyarticle_editor_honorific_suffix_'.$schema_id, 'saswp_array');
        }  
        if(!empty( $all_post_meta['saswp_scholarlyarticle_editor_description_'.$schema_id][0] )){
            $input1['editor']['description'] = saswp_remove_warnings($all_post_meta, 'saswp_scholarlyarticle_editor_description_'.$schema_id, 'saswp_array');
        }  
        if(!empty( $all_post_meta['saswp_scholarlyarticle_editor_url_'.$schema_id][0] )){
            $input1['editor']['url']         = saswp_remove_warnings($all_post_meta, 'saswp_scholarlyarticle_editor_url_'.$schema_id, 'saswp_array');  
        }  
        if(!empty( $all_post_meta['saswp_scholarlyarticle_editor_image_'.$schema_id][0] )){
            $input1['editor']['image']['@type']   = 'ImageObject';
            $input1['editor']['image']['url']     = saswp_remove_warnings($all_post_meta, 'saswp_scholarlyarticle_editor_image_'.$schema_id, 'saswp_array');       
            $input1['editor']['image']['height']  = isset($author_image['height']) ? $author_image['height'] : '';
            $input1['editor']['image']['width']   = isset($author_image['width']) ? $author_image['width'] : '';  
        }  

    }
        

    if(!empty($all_post_meta['saswp_scholarlyarticle_reviewedby_type_'.$schema_id][0]) && isset($all_post_meta['saswp_scholarlyarticle_reviewedby_type_'.$schema_id][0])){
        $input1['reviewedBy']['@type']       = 'Person';
        if(!empty( $all_post_meta['saswp_scholarlyarticle_reviewedby_type_'.$schema_id][0] )){
            $input1['reviewedBy']['@type']       = $all_post_meta['saswp_scholarlyarticle_reviewedby_type_'.$schema_id][0];
        }  
        if(!empty( $all_post_meta['saswp_scholarlyarticle_reviewedby_name_'.$schema_id][0] )){
            $input1['reviewedBy']['name']        = saswp_remove_warnings($all_post_meta, 'saswp_scholarlyarticle_reviewedby_name_'.$schema_id, 'saswp_array');
        }
        if(!empty( $all_post_meta['saswp_scholarlyarticle_reviewedby_honorific_suffix_'.$schema_id][0] )){
            $input1['reviewedBy']['honorificSuffix']        = saswp_remove_warnings($all_post_meta, 'saswp_scholarlyarticle_reviewedby_honorific_suffix_'.$schema_id, 'saswp_array');
        }
        if(!empty( $all_post_meta['saswp_scholarlyarticle_reviewedby_description_'.$schema_id][0] )){
            $input1['reviewedBy']['description'] = saswp_remove_warnings($all_post_meta, 'saswp_scholarlyarticle_reviewedby_description_'.$schema_id, 'saswp_array');
        }
        if(!empty( $all_post_meta['saswp_scholarlyarticle_reviewedby_url_'.$schema_id][0] )){
            $input1['reviewedBy']['url']         = saswp_remove_warnings($all_post_meta, 'saswp_scholarlyarticle_reviewedby_url_'.$schema_id, 'saswp_array');   
        }
    }
        if( !empty($all_post_meta['saswp_scholarlyarticle_about_'.$schema_id][0]) && isset( $all_post_meta['saswp_scholarlyarticle_about_'.$schema_id][0] )){

            $explode_about = explode(',', $all_post_meta['saswp_scholarlyarticle_about_'.$schema_id][0]);
                if(!empty($explode_about)){
                    $about_arr = array();
                    foreach($explode_about as $val){
                        $about_arr[] = array(
                                    '@type' => 'Thing',
                                    'name'  => $val
                        );
                    }
                    $input1['about'] = $about_arr;
                }                                            
        }
        if( !empty($all_post_meta['saswp_scholarlyarticle_knowsabout_'.$schema_id][0]) && isset( $all_post_meta['saswp_scholarlyarticle_knowsabout_'.$schema_id][0] )){
            $input1['knowsAbout']       = explode(',', $all_post_meta['saswp_scholarlyarticle_knowsabout_'.$schema_id][0]);
        }

    if(!empty($all_post_meta['saswp_scholarlyarticle_alumniof_'.$schema_id][0]) && isset( $all_post_meta['saswp_scholarlyarticle_alumniof_'.$schema_id][0] )){
        $itemlist = explode(',', $all_post_meta['saswp_scholarlyarticle_alumniof_'.$schema_id][0]);
        foreach ($itemlist as $key => $list){
            $vnewarr['@type'] = 'Organization';
            $vnewarr['Name']   = $list;   
            $input1['alumniOf'][] = $vnewarr;
        }
    }
    if(isset($all_post_meta['saswp_scholarlyarticle_organization_logo_'.$schema_id][0]) || isset($all_post_meta['saswp_scholarlyarticle_organization_name_'.$schema_id][0])){
    
        $input1['publisher']['@type']          = 'Organization';
        $input1['publisher']['logo']['@type']  = 'ImageObject';
        $input1['publisher']['logo']['url']    = saswp_remove_warnings($all_post_meta, 'saswp_scholarlyarticle_organization_logo_'.$schema_id, 'saswp_array');
        $input1['publisher']['logo']['width']  = saswp_remove_warnings($slogo, 'width', 'saswp_string');
        $input1['publisher']['logo']['height'] = saswp_remove_warnings($slogo, 'height', 'saswp_string');
        $input1['publisher']['name']           = saswp_remove_warnings($all_post_meta, 'saswp_scholarlyarticle_organization_name_'.$schema_id, 'saswp_array');
        
    }
    
    $itemlist  = get_post_meta($schema_post_id, 'scholarlyarticle_items_'.$schema_id, true);

    if($itemlist){

        $list_arr = array();

        foreach ($itemlist as $list){
            $list_arr[] = $list['saswp_scholarlyarticle_items_name'];
        }

        $input1['mainEntity']['@type']            = 'ItemList';
        $input1['mainEntity']['itemListElement']  = $list_arr;                 
        $input1['mainEntity']['itemListOrder']    = 'http://schema.org/ItemListOrderAscending ';
        $input1['mainEntity']['name']             = saswp_get_the_title();

    }

    if(isset($all_post_meta['saswp_scholarlyarticle_speakable_'.$schema_id]) && $all_post_meta['saswp_scholarlyarticle_speakable_'.$schema_id][0] == 1){

    $input1['speakable']['@type'] = 'SpeakableSpecification';
    $input1['speakable']['xpath'] = array(
         "/html/head/title",
         "/html/head/meta[@name='description']/@content"
    );

   }

return $input1;

}

function saswp_creativework_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
    $author_image = get_post_meta( get_the_ID(), 'saswp_creativework_author_image_'.$schema_id.'_detail',true);
    $slogo = get_post_meta( get_the_ID(), 'saswp_creativework_organization_logo_'.$schema_id.'_detail',true);
    $checkIdPro = ((isset($all_post_meta['saswp_creativework_id_'.$schema_id][0]) && $all_post_meta['saswp_creativework_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_creativework_id_'.$schema_id][0] : ''); 

    $input1 = array(
            '@context'			            => saswp_context_url(),
            '@type'				            => 'CreativeWork',
            '@id'                           => $checkIdPro,
            'url'				            => saswp_remove_warnings($all_post_meta, 'saswp_creativework_url_'.$schema_id, 'saswp_array'),
            'inLanguage'                    => saswp_remove_warnings($all_post_meta, 'saswp_creativework_inlanguage_'.$schema_id, 'saswp_array'),
            'mainEntityOfPage'              => saswp_remove_warnings($all_post_meta, 'saswp_creativework_main_entity_of_page_'.$schema_id, 'saswp_array'),
            'image'				            => saswp_remove_warnings($all_post_meta, 'saswp_creativework_image_'.$schema_id, 'saswp_array'),
            'headline'			            => saswp_remove_warnings($all_post_meta, 'saswp_creativework_headline_'.$schema_id, 'saswp_array'),
            'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_creativework_description_'.$schema_id, 'saswp_array'),
            'articleSection'                => saswp_remove_warnings($all_post_meta, 'saswp_creativework_section_'.$schema_id, 'saswp_array'),
            'articleBody'                   => isset($all_post_meta['saswp_creativework_body_'.$schema_id][0]) ? wp_strip_all_tags(strip_shortcodes($all_post_meta['saswp_creativework_body_'.$schema_id][0])) : '',
            'keywords'		                => saswp_remove_warnings($all_post_meta, 'saswp_creativework_keywords_'.$schema_id, 'saswp_array'),                
            'datePublished'                 => isset($all_post_meta['saswp_creativework_date_published_'.$schema_id][0])? saswp_format_date_time($all_post_meta['saswp_creativework_date_published_'.$schema_id][0], get_post_time('h:i:s')):'',
            'dateModified'                  => isset($all_post_meta['saswp_creativework_date_modified_'.$schema_id][0])? saswp_format_date_time($all_post_meta['saswp_creativework_date_modified_'.$schema_id][0], get_the_modified_time('h:i:s')):'',                                

    );

    if(empty($input1['@id'])){
        unset($input1['@id']);
    }

    $input1['author']['@type']       = 'Person';

    if(isset( $all_post_meta['saswp_creativework_author_type_'.$schema_id][0] )){
        $input1['author']['@type']       = $all_post_meta['saswp_creativework_author_type_'.$schema_id][0];
    }  

    $input1['author']['name']        = saswp_remove_warnings($all_post_meta, 'saswp_creativework_author_name_'.$schema_id, 'saswp_array');
    $input1['author']['honorificSuffix']        = saswp_remove_warnings($all_post_meta, 'saswp_creativework_author_honorific_suffix_'.$schema_id, 'saswp_array');
    $input1['author']['description'] = saswp_remove_warnings($all_post_meta, 'saswp_creativework_author_description_'.$schema_id, 'saswp_array');
    $input1['author']['url']         = saswp_remove_warnings($all_post_meta, 'saswp_creativework_author_url_'.$schema_id, 'saswp_array');   
    
    $input1['author']['JobTitle']    = saswp_remove_warnings($all_post_meta, 'saswp_creativework_author_jobtitle_'.$schema_id, 'saswp_array'); 

    $input1['author']['image']['@type']   = 'ImageObject';
    $input1['author']['image']['url']     = saswp_remove_warnings($all_post_meta, 'saswp_creativework_author_image_'.$schema_id, 'saswp_array');       
    $input1['author']['image']['height']  = isset($author_image['height']) ? $author_image['height'] : '';
    $input1['author']['image']['width']   = isset($author_image['width']) ? $author_image['width'] : '';

    if(!empty($all_post_meta['saswp_creativework_editor_type_'.$schema_id][0]) && isset($all_post_meta['saswp_creativework_editor_type_'.$schema_id][0])){
        $input1['editor']['@type']       = 'Person';
        if(!empty( $all_post_meta['saswp_creativework_editor_type_'.$schema_id][0] )){
            $input1['editor']['@type']       = $all_post_meta['saswp_creativework_editor_type_'.$schema_id][0];
        }
        if(!empty( $all_post_meta['saswp_creativework_editor_name_'.$schema_id][0] )){
            $input1['editor']['name']        = saswp_remove_warnings($all_post_meta, 'saswp_creativework_editor_name_'.$schema_id, 'saswp_array');
        }  
        if(!empty( $all_post_meta['saswp_creativework_editor_honorific_suffix_'.$schema_id][0] )){
            $input1['editor']['honorificSuffix']        = saswp_remove_warnings($all_post_meta, 'saswp_creativework_editor_honorific_suffix_'.$schema_id, 'saswp_array');
        }  
        if(!empty( $all_post_meta['saswp_creativework_editor_description_'.$schema_id][0] )){
            $input1['editor']['description'] = saswp_remove_warnings($all_post_meta, 'saswp_creativework_editor_description_'.$schema_id, 'saswp_array');
        }  
        if(!empty( $all_post_meta['saswp_creativework_editor_url_'.$schema_id][0] )){
            $input1['editor']['url']         = saswp_remove_warnings($all_post_meta, 'saswp_creativework_editor_url_'.$schema_id, 'saswp_array');   
        }  
        if(!empty( $all_post_meta['saswp_creativework_editor_image_'.$schema_id][0] )){
            $input1['editor']['image']['@type']   = 'ImageObject';
            $input1['editor']['image']['url']     = saswp_remove_warnings($all_post_meta, 'saswp_creativework_editor_image_'.$schema_id, 'saswp_array');       
            $input1['editor']['image']['height']  = isset($author_image['height']) ? $author_image['height'] : '';
            $input1['editor']['image']['width']   = isset($author_image['width']) ? $author_image['width'] : '';
        }  
    }

    if(!empty($all_post_meta['saswp_creativework_reviewedby_type_'.$schema_id][0]) && isset($all_post_meta['saswp_creativework_reviewedby_type_'.$schema_id][0])){
        $input1['reviewedBy']['@type']       = 'Person';
        if(!empty( $all_post_meta['saswp_creativework_reviewedby_type_'.$schema_id][0] )){
            $input1['reviewedBy']['@type']       = $all_post_meta['saswp_creativework_reviewedby_type_'.$schema_id][0];
        }  
        if(!empty( $all_post_meta['saswp_creativework_reviewedby_name_'.$schema_id][0] )){
            $input1['reviewedBy']['name']        = saswp_remove_warnings($all_post_meta, 'saswp_creativework_reviewedby_name_'.$schema_id, 'saswp_array');
        }  
        if(!empty( $all_post_meta['saswp_creativework_reviewedby_honorific_suffix_'.$schema_id][0] )){
            $input1['reviewedBy']['honorificSuffix']        = saswp_remove_warnings($all_post_meta, 'saswp_creativework_reviewedby_honorific_suffix_'.$schema_id, 'saswp_array');
        }  
        if(!empty( $all_post_meta['saswp_creativework_reviewedby_description_'.$schema_id][0] )){
            $input1['reviewedBy']['description'] = saswp_remove_warnings($all_post_meta, 'saswp_creativework_reviewedby_description_'.$schema_id, 'saswp_array');
        }  
        if(!empty( $all_post_meta['saswp_creativework_reviewedby_url_'.$schema_id][0] )){
            $input1['reviewedBy']['url']         = saswp_remove_warnings($all_post_meta, 'saswp_creativework_reviewedby_url_'.$schema_id, 'saswp_array');   
        }  
    }
       

    if( !empty($all_post_meta['saswp_creativework_knowsabout_'.$schema_id][0]) && isset( $all_post_meta['saswp_creativework_knowsabout_'.$schema_id][0] )){
        $input1['knowsAbout']       = explode(',', $all_post_meta['saswp_creativework_knowsabout_'.$schema_id][0]);
    }
    if(isset( $all_post_meta['saswp_creativework_size_'.$schema_id][0] )){
        $input1['size']       = $all_post_meta['saswp_creativework_size_'.$schema_id][0];
    }
    if(isset( $all_post_meta['saswp_creativework_license_'.$schema_id][0] )){
        $input1['license']       = $all_post_meta['saswp_creativework_license_'.$schema_id][0];
    }
    if( !empty($all_post_meta['saswp_creativework_about_'.$schema_id][0]) && isset( $all_post_meta['saswp_creativework_about_'.$schema_id][0] )){
        $input1['about']['@type']       = 'Event';
        $input1['about']['name']       = explode(',', $all_post_meta['saswp_creativework_about_'.$schema_id][0]);
    }
    if(!empty($all_post_meta['saswp_creativework_alumniof_'.$schema_id][0]) && isset( $all_post_meta['saswp_creativework_alumniof_'.$schema_id][0] )){
        $itemlist = explode(',', $all_post_meta['saswp_creativework_alumniof_'.$schema_id][0]);
        foreach ($itemlist as $key => $list){
            $vnewarr['@type'] = 'Organization';
            $vnewarr['Name']   = $list;   
            $input1['alumniOf'][] = $vnewarr;
        }
    }
    if(isset($all_post_meta['saswp_creativework_organization_logo_'.$schema_id][0]) || isset($all_post_meta['saswp_creativework_organization_name_'.$schema_id][0])){
    
        $input1['publisher']['@type']          = 'Organization';
        $input1['publisher']['logo']['@type']  = 'ImageObject';
        $input1['publisher']['logo']['url']    = saswp_remove_warnings($all_post_meta, 'saswp_creativework_organization_logo_'.$schema_id, 'saswp_array');
        $input1['publisher']['logo']['width']  = saswp_remove_warnings($slogo, 'width', 'saswp_string');
        $input1['publisher']['logo']['height'] = saswp_remove_warnings($slogo, 'height', 'saswp_string');
        $input1['publisher']['name']           = saswp_remove_warnings($all_post_meta, 'saswp_creativework_organization_name_'.$schema_id, 'saswp_array');
        
    }
    
    $itemlist  = get_post_meta($schema_post_id, 'article_items_'.$schema_id, true);

    if($itemlist){

        $list_arr = array();

        foreach ($itemlist as $list){
            $list_arr[] = $list['saswp_creativework_items_name'];
        }

        $input1['mainEntity']['@type']            = 'ItemList';
        $input1['mainEntity']['itemListElement']  = $list_arr;                 
        $input1['mainEntity']['itemListOrder']    = 'http://schema.org/ItemListOrderAscending ';
        $input1['mainEntity']['name']             = saswp_get_the_title();

    }       

    return $input1;

}

function saswp_tech_article_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
        $input1 = array();
        $author_image = get_post_meta( get_the_ID(), 'saswp_tech_article_author_image_'.$schema_id.'_detail',true);
        $slogo = get_post_meta( get_the_ID(), 'saswp_tech_article_organization_logo_'.$schema_id.'_detail',true);
        $checkIdPro = ((isset($all_post_meta['saswp_tech_article_id_'.$schema_id][0]) && $all_post_meta['saswp_tech_article_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_tech_article_id_'.$schema_id][0] : '');

        $same_as_str = saswp_remove_warnings($all_post_meta, 'saswp_tech_article_same_as_'.$schema_id, 'saswp_array'); 

        $input1 = array(
                '@context'			            => saswp_context_url(),
                '@type'				            => 'TechArticle',
                '@id'                           =>  $checkIdPro,
                'inLanguage'                    => saswp_remove_warnings($all_post_meta, 'saswp_tech_article_inlanguage_'.$schema_id, 'saswp_array'),
                'mainEntityOfPage'              => saswp_remove_warnings($all_post_meta, 'saswp_tech_article_main_entity_of_page_'.$schema_id, 'saswp_array'),
                'image'				            => saswp_remove_warnings($all_post_meta, 'saswp_tech_article_image_'.$schema_id, 'saswp_array'),
                'headline'			            => saswp_remove_warnings($all_post_meta, 'saswp_tech_article_headline_'.$schema_id, 'saswp_array'),
                'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_tech_article_description_'.$schema_id, 'saswp_array'),
                'keywords'		                => saswp_remove_warnings($all_post_meta, 'saswp_tech_article_keywords_'.$schema_id, 'saswp_array'),
                'datePublished'                 => isset($all_post_meta['saswp_tech_article_date_published_'.$schema_id][0])? saswp_format_date_time($all_post_meta['saswp_tech_article_date_published_'.$schema_id][0], get_post_time('h:i:s')) :'',
                'dateModified'                  => isset($all_post_meta['saswp_tech_article_date_modified_'.$schema_id][0])? saswp_format_date_time($all_post_meta['saswp_tech_article_date_modified_'.$schema_id][0], get_the_modified_time('h:i:s')) :'',                
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

        if(!empty($same_as_str)){
            $same_as_arr = explode(',', $same_as_str);
            if(is_array($same_as_arr) && isset($same_as_arr[0])){
                $input1['sameAs'] = $same_as_arr;       
            }
        }

        if(empty($input1['@id'])){
            unset($input1['@id']);
        }

        $input1['author']['@type']       = 'Person';

        if(isset( $all_post_meta['saswp_tech_article_author_type_'.$schema_id][0] )){
            $input1['author']['@type']       = $all_post_meta['saswp_tech_article_author_type_'.$schema_id][0];
        }  
        $input1['author']['name']        = saswp_remove_warnings($all_post_meta, 'saswp_tech_article_author_name_'.$schema_id, 'saswp_array');
        $input1['author']['honorificSuffix']        = saswp_remove_warnings($all_post_meta, 'saswp_tech_article_author_honorific_suffix_'.$schema_id, 'saswp_array');
        $input1['author']['description'] = saswp_remove_warnings($all_post_meta, 'saswp_tech_article_author_description_'.$schema_id, 'saswp_array');
        $input1['author']['url']         = saswp_remove_warnings($all_post_meta, 'saswp_tech_article_author_url_'.$schema_id, 'saswp_array');   

        $input1['author']['JobTitle']    = saswp_remove_warnings($all_post_meta, 'saswp_tech_article_author_jobtitle_'.$schema_id, 'saswp_array'); 

        $input1['author']['image']['@type']   = 'ImageObject';
        $input1['author']['image']['url']     = saswp_remove_warnings($all_post_meta, 'saswp_tech_article_author_image_'.$schema_id, 'saswp_array');

        if(isset($all_post_meta['saswp_tech_article_author_social_profile_'.$schema_id][0]) && !empty($all_post_meta['saswp_tech_article_author_social_profile_'.$schema_id][0])){
            $explode_sp = saswp_remove_warnings($all_post_meta, 'saswp_tech_article_author_social_profile_'.$schema_id, 'saswp_array');
            $explode_sp = explode(',',$explode_sp);
            if(!empty($explode_sp) && is_array($explode_sp)){
                $input1['author']['sameAs'] = $explode_sp; 
            }
        }

        $input1['author']['image']['height']  = isset($author_image['height']) ? $author_image['height'] : '';
        $input1['author']['image']['width']   = isset($author_image['width']) ? $author_image['width'] : '';

        if(!empty($all_post_meta['saswp_tech_article_editor_type_'.$schema_id][0]) && isset($all_post_meta['saswp_tech_article_editor_type_'.$schema_id][0])){
            $input1['editor']['@type']       = 'Person';
            if(!empty( $all_post_meta['saswp_tech_article_editor_type_'.$schema_id][0] )){
                $input1['editor']['@type']       = $all_post_meta['saswp_tech_article_editor_type_'.$schema_id][0];
            }  
            if(!empty( $all_post_meta['saswp_tech_article_editor_name_'.$schema_id][0] )){
                $input1['editor']['name']        = saswp_remove_warnings($all_post_meta, 'saswp_tech_article_editor_name_'.$schema_id, 'saswp_array');
            }  
            if(!empty( $all_post_meta['saswp_tech_article_editor_honorific_suffix_'.$schema_id][0] )){
                $input1['editor']['honorificSuffix']        = saswp_remove_warnings($all_post_meta, 'saswp_tech_article_editor_honorific_suffix_'.$schema_id, 'saswp_array');
            }  
            if(!empty( $all_post_meta['saswp_tech_article_editor_description_'.$schema_id][0] )){
                $input1['editor']['description'] = saswp_remove_warnings($all_post_meta, 'saswp_tech_article_editor_description_'.$schema_id, 'saswp_array');
            }  
            if(!empty( $all_post_meta['saswp_tech_article_editor_url_'.$schema_id][0] )){
                $input1['editor']['url']         = saswp_remove_warnings($all_post_meta, 'saswp_tech_article_editor_url_'.$schema_id, 'saswp_array');   
            }  
            if(isset($all_post_meta['saswp_tech_article_author_social_profile_'.$schema_id][0]) && !empty($all_post_meta['saswp_tech_article_author_social_profile_'.$schema_id][0])){
                $explode_sp = saswp_remove_warnings($all_post_meta, 'saswp_tech_article_author_social_profile_'.$schema_id, 'saswp_array');
                $explode_sp = explode(',',$explode_sp);
                if(!empty($explode_sp) && is_array($explode_sp)){
                    $input1['editor']['sameAs'] = $explode_sp; 
                }
            }
            if(!empty( $all_post_meta['saswp_tech_article_editor_image_'.$schema_id][0] )){
                $input1['editor']['image']['@type']   = 'ImageObject';
                $input1['editor']['image']['url']     = saswp_remove_warnings($all_post_meta, 'saswp_tech_article_editor_image_'.$schema_id, 'saswp_array');       
                $input1['editor']['image']['height']  = isset($author_image['height']) ? $author_image['height'] : '';
                $input1['editor']['image']['width']   = isset($author_image['width']) ? $author_image['width'] : '';
            }  
        }
            

        if(!empty($all_post_meta['saswp_tech_article_reviewedby_type_'.$schema_id][0]) && isset($all_post_meta['saswp_tech_article_reviewedby_type_'.$schema_id][0])){
            $input1['reviewedBy']['@type']       = 'Person';
            if(!empty( $all_post_meta['saswp_tech_article_reviewedby_type_'.$schema_id][0] )){
                $input1['reviewedBy']['@type']       = $all_post_meta['saswp_tech_article_reviewedby_type_'.$schema_id][0];
            }  
            if(!empty( $all_post_meta['saswp_tech_article_reviewedby_name_'.$schema_id][0] )){
                $input1['reviewedBy']['name']        = saswp_remove_warnings($all_post_meta, 'saswp_tech_article_reviewedby_name_'.$schema_id, 'saswp_array');
            }
            if(!empty( $all_post_meta['saswp_tech_article_reviewedby_honorific_suffix_'.$schema_id][0] )){
                $input1['reviewedBy']['honorificSuffix']        = saswp_remove_warnings($all_post_meta, 'saswp_tech_article_reviewedby_honorific_suffix_'.$schema_id, 'saswp_array');
            }
            if(!empty( $all_post_meta['saswp_tech_article_reviewedby_description_'.$schema_id][0] )){
                $input1['reviewedBy']['description'] = saswp_remove_warnings($all_post_meta, 'saswp_tech_article_reviewedby_description_'.$schema_id, 'saswp_array');
            }
            if(!empty( $all_post_meta['saswp_tech_article_reviewedby_url_'.$schema_id][0] )){
                $input1['reviewedBy']['url']         = saswp_remove_warnings($all_post_meta, 'saswp_tech_article_reviewedby_url_'.$schema_id, 'saswp_array');   
            }
        }
        if( !empty($all_post_meta['saswp_tech_article_about_'.$schema_id][0]) && isset( $all_post_meta['saswp_tech_article_about_'.$schema_id][0] )){
            $input1['about']['@type']       = 'Event';
            $input1['about']['name']       = explode(',', $all_post_meta['saswp_tech_article_about_'.$schema_id][0]);
        }
        if( !empty($all_post_meta['saswp_tech_article_knowsabout_'.$schema_id][0]) && isset( $all_post_meta['saswp_tech_article_knowsabout_'.$schema_id][0] )){
            $input1['knowsAbout']       = explode(',', $all_post_meta['saswp_tech_article_knowsabout_'.$schema_id][0]);
        }
        if( !empty($all_post_meta['saswp_tech_article_alumniof_'.$schema_id][0]) && isset( $all_post_meta['saswp_tech_article_alumniof_'.$schema_id][0] )){
            $itemlist = explode(',', $all_post_meta['saswp_tech_article_alumniof_'.$schema_id][0]);
            foreach ($itemlist as $key => $list){
                $vnewarr['@type'] = 'Organization';
                $vnewarr['Name']   = $list;   
                $input1['alumniOf'][] = $vnewarr;
            }
        }
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
                
                $slogo        = get_post_meta( get_the_ID(), 'saswp_newsarticle_organization_logo_'.$schema_id.'_detail',true);
                $author_image = get_post_meta( get_the_ID(), 'saswp_newsarticle_author_image_'.$schema_id.'_detail',true);
                $checkIdPro = ((isset($all_post_meta['saswp_newsarticle_id_'.$schema_id][0]) && $all_post_meta['saswp_newsarticle_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_newsarticle_id_'.$schema_id][0] : ''); 
                             
				$input1 = array(
					'@context'			            => saswp_context_url(),
					'@type'				            => 'NewsArticle' ,
                    '@id'                           => $checkIdPro,    
                    'inLanguage'                    => saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_inlanguage_'.$schema_id, 'saswp_array'),       
					'mainEntityOfPage'              => saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_main_entity_of_page_'.$schema_id, 'saswp_array'),
					'url'			            	=> saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_URL_'.$schema_id, 'saswp_array'),
                    'image'				            => saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_image_'.$schema_id, 'saswp_array'),
					'headline'			            => saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_headline_'.$schema_id, 'saswp_array'),
					'alternativeHeadline'			=> saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_alternative_headline_'.$schema_id, 'saswp_array'),
                    'datePublished'                 => isset($all_post_meta['saswp_newsarticle_date_published_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_newsarticle_date_published_'.$schema_id][0], get_post_time('h:i:s')) :'',
					'dateModified'                  => isset($all_post_meta['saswp_newsarticle_date_modified_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_newsarticle_date_modified_'.$schema_id][0], get_the_modified_time('h:i:s')) :'',
					'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_description_'.$schema_id, 'saswp_array'),
                    'keywords'		                => saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_keywords_'.$schema_id, 'saswp_array'),
                    'articleSection'                => saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_section_'.$schema_id, 'saswp_array'),
                    'articleBody'                   => saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_body_'.$schema_id, 'saswp_array'),     
					'name'				            => saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_name_'.$schema_id, 'saswp_array'), 					
					'thumbnailUrl'                  => saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_thumbnailurl_'.$schema_id, 'saswp_array'),
                    'wordCount'                     => saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_word_count_'.$schema_id, 'saswp_array'),
                    'timeRequired'                  => saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_timerequired_'.$schema_id, 'saswp_array'),    
					'mainEntity'                    => array(
                                                              '@type' => 'WebPage',
                                                              '@id'   => saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_main_entity_id_'.$schema_id, 'saswp_array'),
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
                                        
                if(empty($input1['@id'])){
                    unset($input1['@id']);
                }


                $input1['author']['@type']       = 'Person';

                if(isset( $all_post_meta['saswp_newsarticle_author_type_'.$schema_id][0] )){
                    $input1['author']['@type']       = $all_post_meta['saswp_newsarticle_author_type_'.$schema_id][0];
                }  
        
                $input1['author']['name']        = saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_author_name_'.$schema_id, 'saswp_array');
                $input1['author']['description'] = saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_author_description_'.$schema_id, 'saswp_array');
                $input1['author']['url']         = saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_author_url_'.$schema_id, 'saswp_array');       

                $input1['author']['image']['@type']   = 'ImageObject';
                $input1['author']['image']['url']     = saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_author_image_'.$schema_id, 'saswp_array');       
                $input1['author']['image']['height']  = isset($author_image['height']) ? $author_image['height'] : '';
                $input1['author']['image']['width']   = isset($author_image['width']) ? $author_image['width'] : '';
                if(isset($all_post_meta['saswp_newsarticle_author_social_profile_'.$schema_id][0]) && !empty($all_post_meta['saswp_newsarticle_author_social_profile_'.$schema_id][0])){
                    $explode_sp = saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_author_social_profile_'.$schema_id, 'saswp_array');
                    $explode_sp = explode(',',$explode_sp);
                    if(!empty($explode_sp) && is_array($explode_sp)){
                        $input1['author']['sameAs'] = $explode_sp; 
                    }
                }
                if( !empty($all_post_meta['saswp_newsarticle_editor_type_'.$schema_id][0]) && isset( $all_post_meta['saswp_newsarticle_editor_type_'.$schema_id][0] )){
                    $input1['editor']['@type']       = 'Person';
                    if(!empty( $all_post_meta['saswp_newsarticle_editor_type_'.$schema_id][0] )){
                        $input1['editor']['@type']       = $all_post_meta['saswp_newsarticle_editor_type_'.$schema_id][0];
                    }  
                    if(!empty( $all_post_meta['saswp_newsarticle_editor_name_'.$schema_id][0] )){
                        $input1['editor']['name']        = saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_editor_name_'.$schema_id, 'saswp_array');
                    }  
                    if(!empty( $all_post_meta['saswp_newsarticle_editor_honorific_suffix_'.$schema_id][0] )){
                        $input1['editor']['honorificSuffix']  = saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_editor_honorific_suffix_'.$schema_id, 'saswp_array');
                    }  
                    if(!empty( $all_post_meta['saswp_newsarticle_editor_description_'.$schema_id][0] )){
                        $input1['editor']['description'] = saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_editor_description_'.$schema_id, 'saswp_array');
                    }  
                    if(!empty( $all_post_meta['saswp_newsarticle_editor_url_'.$schema_id][0] )){
                        $input1['editor']['url']         = saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_editor_url_'.$schema_id, 'saswp_array');   
                    }  
                    if(!empty( $all_post_meta['saswp_newsarticle_editor_image_'.$schema_id][0] )){
                        $input1['editor']['image']['@type']   = 'ImageObject';
                        $input1['editor']['image']['url']     = saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_editor_image_'.$schema_id, 'saswp_array');       
                        $input1['editor']['image']['height']  = isset($author_image['height']) ? $author_image['height'] : '';
                        $input1['editor']['image']['width']   = isset($author_image['width']) ? $author_image['width'] : '';
                    } 
                    if(isset($all_post_meta['saswp_newsarticle_author_social_profile_'.$schema_id][0]) && !empty($all_post_meta['saswp_newsarticle_author_social_profile_'.$schema_id][0])){
                        $explode_sp = saswp_remove_warnings($all_post_meta, 'saswp_newsarticle_author_social_profile_'.$schema_id, 'saswp_array');
                        $explode_sp = explode(',',$explode_sp);
                        if(!empty($explode_sp) && is_array($explode_sp)){
                            $input1['editor']['sameAs'] = $explode_sp; 
                        }
                    } 
                }

                if( !empty($all_post_meta['saswp_newsarticle_about_'.$schema_id][0]) && isset( $all_post_meta['saswp_newsarticle_about_'.$schema_id][0] )){
                    $input1['about']['@type']       = 'Event';
                    $input1['about']['name']       = explode(',', $all_post_meta['saswp_newsarticle_about_'.$schema_id][0]);
                }
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

function saswp_analysis_newsarticle_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
    
    $slogo        = get_post_meta( get_the_ID(), 'saswp_analysisnewsarticle_organization_logo_'.$schema_id.'_detail',true);
    $author_image = get_post_meta( get_the_ID(), 'saswp_analysisnewsarticle_author_image_'.$schema_id.'_detail',true);
    $checkIdPro = ((isset($all_post_meta['saswp_analysisnewsarticle_id_'.$schema_id][0]) && $all_post_meta['saswp_analysisnewsarticle_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_analysisnewsarticle_id_'.$schema_id][0] : ''); 
                 
    $input1 = array(
        '@context'			            => saswp_context_url(),
        '@type'				            => 'AnalysisNewsArticle' ,
        '@id'                           => $checkIdPro,    
        'inLanguage'                    => saswp_remove_warnings($all_post_meta, 'saswp_analysisnewsarticle_inlanguage_'.$schema_id, 'saswp_array'),       
        'mainEntityOfPage'              => saswp_remove_warnings($all_post_meta, 'saswp_analysisnewsarticle_main_entity_of_page_'.$schema_id, 'saswp_array'),
        'url'			            	=> saswp_remove_warnings($all_post_meta, 'saswp_analysisnewsarticle_URL_'.$schema_id, 'saswp_array'),
        'image'				            => saswp_remove_warnings($all_post_meta, 'saswp_analysisnewsarticle_image_'.$schema_id, 'saswp_array'),
        'headline'			            => saswp_remove_warnings($all_post_meta, 'saswp_analysisnewsarticle_headline_'.$schema_id, 'saswp_array'),
        'datePublished'                 => isset($all_post_meta['saswp_analysisnewsarticle_date_published_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_analysisnewsarticle_date_published_'.$schema_id][0], get_post_time('h:i:s')) :'',
        'dateModified'                  => isset($all_post_meta['saswp_analysisnewsarticle_date_modified_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_analysisnewsarticle_date_modified_'.$schema_id][0], get_the_modified_time('h:i:s')) :'',
        'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_analysisnewsarticle_description_'.$schema_id, 'saswp_array'),
        'keywords'		                => saswp_remove_warnings($all_post_meta, 'saswp_analysisnewsarticle_keywords_'.$schema_id, 'saswp_array'),
        'articleSection'                => saswp_remove_warnings($all_post_meta, 'saswp_analysisnewsarticle_section_'.$schema_id, 'saswp_array'),
        'articleBody'                   => saswp_remove_warnings($all_post_meta, 'saswp_analysisnewsarticle_body_'.$schema_id, 'saswp_array'),     
        'name'				            => saswp_remove_warnings($all_post_meta, 'saswp_analysisnewsarticle_name_'.$schema_id, 'saswp_array'), 					
        'thumbnailUrl'                  => saswp_remove_warnings($all_post_meta, 'saswp_analysisnewsarticle_thumbnailurl_'.$schema_id, 'saswp_array'),
        'wordCount'                     => saswp_remove_warnings($all_post_meta, 'saswp_analysisnewsarticle_word_count_'.$schema_id, 'saswp_array'),
        'timeRequired'                  => saswp_remove_warnings($all_post_meta, 'saswp_analysisnewsarticle_timerequired_'.$schema_id, 'saswp_array'),    
        'mainEntity'                    => array(
                                                  '@type' => 'WebPage',
                                                  '@id'   => saswp_remove_warnings($all_post_meta, 'saswp_analysisnewsarticle_main_entity_id_'.$schema_id, 'saswp_array'),
            ), 					
        'publisher'			=> array(
                '@type'				=> 'Organization',
                'logo' 				=> array(
                '@type'				=> 'ImageObject',
                'url'				=> saswp_remove_warnings($all_post_meta, 'saswp_analysisnewsarticle_organization_logo_'.$schema_id, 'saswp_array'),
                'width'				=> saswp_remove_warnings($slogo, 'width', 'saswp_string'),
                'height'			=> saswp_remove_warnings($slogo, 'height', 'saswp_string'),
                            ),
                'name'				=> saswp_remove_warnings($all_post_meta, 'saswp_analysisnewsarticle_organization_name_'.$schema_id, 'saswp_array'),
                ),
        );
                            
    if(empty($input1['@id'])){
        unset($input1['@id']);
    }

    $input1['author']['@type']       = 'Person';

    if(isset( $all_post_meta['saswp_analysisnewsarticle_author_type_'.$schema_id][0] )){
        $input1['author']['@type']       = $all_post_meta['saswp_analysisnewsarticle_author_type_'.$schema_id][0];
    }  

    $input1['author']['name']        = saswp_remove_warnings($all_post_meta, 'saswp_analysisnewsarticle_author_name_'.$schema_id, 'saswp_array');
    $input1['author']['description'] = saswp_remove_warnings($all_post_meta, 'saswp_analysisnewsarticle_author_description_'.$schema_id, 'saswp_array');
    $input1['author']['url']         = saswp_remove_warnings($all_post_meta, 'saswp_analysisnewsarticle_author_url_'.$schema_id, 'saswp_array');       

    $input1['author']['image']['@type']   = 'ImageObject';
    $input1['author']['image']['url']     = saswp_remove_warnings($all_post_meta, 'saswp_analysisnewsarticle_author_image_'.$schema_id, 'saswp_array');       
    $input1['author']['image']['height']  = isset($author_image['height']) ? $author_image['height'] : '';
    $input1['author']['image']['width']   = isset($author_image['width']) ? $author_image['width'] : '';

    if( !empty($all_post_meta['saswp_analysisnewsarticle_editor_type_'.$schema_id][0]) && isset( $all_post_meta['saswp_analysisnewsarticle_editor_type_'.$schema_id][0] )){
        $input1['editor']['@type']       = 'Person';
        if(!empty( $all_post_meta['saswp_analysisnewsarticle_editor_type_'.$schema_id][0] )){
            $input1['editor']['@type']       = $all_post_meta['saswp_analysisnewsarticle_editor_type_'.$schema_id][0];
        }  
        if(!empty( $all_post_meta['saswp_analysisnewsarticle_editor_name_'.$schema_id][0] )){
            $input1['editor']['name']        = saswp_remove_warnings($all_post_meta, 'saswp_analysisnewsarticle_editor_name_'.$schema_id, 'saswp_array');
        }  
        if(!empty( $all_post_meta['saswp_analysisnewsarticle_editor_honorific_suffix_'.$schema_id][0] )){
            $input1['editor']['honorificSuffix']  = saswp_remove_warnings($all_post_meta, 'saswp_analysisnewsarticle_editor_honorific_suffix_'.$schema_id, 'saswp_array');
        }  
        if(!empty( $all_post_meta['saswp_analysisnewsarticle_editor_description_'.$schema_id][0] )){
            $input1['editor']['description'] = saswp_remove_warnings($all_post_meta, 'saswp_analysisnewsarticle_editor_description_'.$schema_id, 'saswp_array');
        }  
        if(!empty( $all_post_meta['saswp_analysisnewsarticle_editor_url_'.$schema_id][0] )){
            $input1['editor']['url']         = saswp_remove_warnings($all_post_meta, 'saswp_analysisnewsarticle_editor_url_'.$schema_id, 'saswp_array');   
        }  
        if(!empty( $all_post_meta['saswp_analysisnewsarticle_editor_image_'.$schema_id][0] )){
            $input1['editor']['image']['@type']   = 'ImageObject';
            $input1['editor']['image']['url']     = saswp_remove_warnings($all_post_meta, 'saswp_analysisnewsarticle_editor_image_'.$schema_id, 'saswp_array');       
            $input1['editor']['image']['height']  = isset($author_image['height']) ? $author_image['height'] : '';
            $input1['editor']['image']['width']   = isset($author_image['width']) ? $author_image['width'] : '';
        }  
    }

    if( !empty($all_post_meta['saswp_analysisnewsarticle_about_'.$schema_id][0]) && isset( $all_post_meta['saswp_analysisnewsarticle_about_'.$schema_id][0] )){
        $input1['about']['@type']       = 'Event';
        $input1['about']['name']       = explode(',', $all_post_meta['saswp_analysisnewsarticle_about_'.$schema_id][0]);
    }
    $itemlist  = get_post_meta($schema_post_id, 'analysisnewsarticle_items_'.$schema_id, true);
    
    if($itemlist){
        
        $list_arr = array();
        
        foreach ($itemlist as $list){
            $list_arr[] = $list['saswp_analysisnewsarticle_items_name'];
        }
        
        $input1['mainEntity']['@type']            = 'ItemList';
        $input1['mainEntity']['itemListElement']  = $list_arr;                 
        $input1['mainEntity']['itemListOrder']    = 'http://schema.org/ItemListOrderAscending ';
        $input1['mainEntity']['name']             = saswp_get_the_title();
        
    }

    if(isset($all_post_meta['saswp_analysisnewsarticle_speakable_'.$schema_id]) && $all_post_meta['saswp_analysisnewsarticle_speakable_'.$schema_id][0] == 1){

        $input1['speakable']['@type'] = 'SpeakableSpecification';
        $input1['speakable']['xpath'] = array(
                "/html/head/title",
                "/html/head/meta[@name='description']/@content"
        );

        }

return $input1;

}

function saswp_askpublic_newsarticle_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
    
    $slogo        = get_post_meta( get_the_ID(), 'saswp_askpublicnewsarticle_organization_logo_'.$schema_id.'_detail',true);
    $author_image = get_post_meta( get_the_ID(), 'saswp_askpublicnewsarticle_author_image_'.$schema_id.'_detail',true);
    $checkIdPro = ((isset($all_post_meta['saswp_askpublicnewsarticle_id_'.$schema_id][0]) && $all_post_meta['saswp_askpublicnewsarticle_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_askpublicnewsarticle_id_'.$schema_id][0] : ''); 
                 
    $input1 = array(
        '@context'			            => saswp_context_url(),
        '@type'				            => 'AskPublicNewsArticle' ,
        '@id'                           => $checkIdPro,    
        'inLanguage'                    => saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_inlanguage_'.$schema_id, 'saswp_array'),       
        'mainEntityOfPage'              => saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_main_entity_of_page_'.$schema_id, 'saswp_array'),
        'url'			            	=> saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_URL_'.$schema_id, 'saswp_array'),
        'image'				            => saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_image_'.$schema_id, 'saswp_array'),
        'headline'			            => saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_headline_'.$schema_id, 'saswp_array'),
        'datePublished'                 => isset($all_post_meta['saswp_askpublicnewsarticle_date_published_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_askpublicnewsarticle_date_published_'.$schema_id][0], get_post_time('h:i:s')) :'',
        'dateModified'                  => isset($all_post_meta['saswp_askpublicnewsarticle_date_modified_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_askpublicnewsarticle_date_modified_'.$schema_id][0], get_the_modified_time('h:i:s')) :'',
        'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_description_'.$schema_id, 'saswp_array'),
        'keywords'		                => saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_keywords_'.$schema_id, 'saswp_array'),
        'articleSection'                => saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_section_'.$schema_id, 'saswp_array'),
        'articleBody'                   => saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_body_'.$schema_id, 'saswp_array'),     
        'name'				            => saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_name_'.$schema_id, 'saswp_array'), 					
        'thumbnailUrl'                  => saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_thumbnailurl_'.$schema_id, 'saswp_array'),
        'wordCount'                     => saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_word_count_'.$schema_id, 'saswp_array'),
        'timeRequired'                  => saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_timerequired_'.$schema_id, 'saswp_array'),    
        'mainEntity'                    => array(
                                                  '@type' => 'WebPage',
                                                  '@id'   => saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_main_entity_id_'.$schema_id, 'saswp_array'),
            ), 					
        'publisher'			=> array(
                '@type'				=> 'Organization',
                'logo' 				=> array(
                '@type'				=> 'ImageObject',
                'url'				=> saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_organization_logo_'.$schema_id, 'saswp_array'),
                'width'				=> saswp_remove_warnings($slogo, 'width', 'saswp_string'),
                'height'			=> saswp_remove_warnings($slogo, 'height', 'saswp_string'),
                            ),
                'name'				=> saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_organization_name_'.$schema_id, 'saswp_array'),
                ),
        );
                            
    if(empty($input1['@id'])){
        unset($input1['@id']);
    }

    $input1['author']['@type']       = 'Person';

    if(isset( $all_post_meta['saswp_askpublicnewsarticle_author_type_'.$schema_id][0] )){
        $input1['author']['@type']       = $all_post_meta['saswp_askpublicnewsarticle_author_type_'.$schema_id][0];
    }  

    $input1['author']['name']        = saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_author_name_'.$schema_id, 'saswp_array');
    $input1['author']['description'] = saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_author_description_'.$schema_id, 'saswp_array');
    $input1['author']['url']         = saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_author_url_'.$schema_id, 'saswp_array');       

    $input1['author']['image']['@type']   = 'ImageObject';
    $input1['author']['image']['url']     = saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_author_image_'.$schema_id, 'saswp_array');       
    $input1['author']['image']['height']  = isset($author_image['height']) ? $author_image['height'] : '';
    $input1['author']['image']['width']   = isset($author_image['width']) ? $author_image['width'] : '';

    if( !empty($all_post_meta['saswp_askpublicnewsarticle_editor_type_'.$schema_id][0]) && isset( $all_post_meta['saswp_askpublicnewsarticle_editor_type_'.$schema_id][0] )){
        $input1['editor']['@type']       = 'Person';
        if(!empty( $all_post_meta['saswp_askpublicnewsarticle_editor_type_'.$schema_id][0] )){
            $input1['editor']['@type']       = $all_post_meta['saswp_askpublicnewsarticle_editor_type_'.$schema_id][0];
        }  
        if(!empty( $all_post_meta['saswp_askpublicnewsarticle_editor_name_'.$schema_id][0] )){
            $input1['editor']['name']        = saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_editor_name_'.$schema_id, 'saswp_array');
        }  
        if(!empty( $all_post_meta['saswp_askpublicnewsarticle_editor_honorific_suffix_'.$schema_id][0] )){
            $input1['editor']['honorificSuffix']  = saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_editor_honorific_suffix_'.$schema_id, 'saswp_array');
        }  
        if(!empty( $all_post_meta['saswp_askpublicnewsarticle_editor_description_'.$schema_id][0] )){
            $input1['editor']['description'] = saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_editor_description_'.$schema_id, 'saswp_array');
        }  
        if(!empty( $all_post_meta['saswp_askpublicnewsarticle_editor_url_'.$schema_id][0] )){
            $input1['editor']['url']         = saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_editor_url_'.$schema_id, 'saswp_array');   
        }  
        if(!empty( $all_post_meta['saswp_askpublicnewsarticle_editor_image_'.$schema_id][0] )){
            $input1['editor']['image']['@type']   = 'ImageObject';
            $input1['editor']['image']['url']     = saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_editor_image_'.$schema_id, 'saswp_array');       
            $input1['editor']['image']['height']  = isset($author_image['height']) ? $author_image['height'] : '';
            $input1['editor']['image']['width']   = isset($author_image['width']) ? $author_image['width'] : '';
        }  
    }

    if( !empty($all_post_meta['saswp_askpublicnewsarticle_about_'.$schema_id][0]) && isset( $all_post_meta['saswp_askpublicnewsarticle_about_'.$schema_id][0] )){
        $input1['about']['@type']       = 'Event';
        $input1['about']['name']       = explode(',', $all_post_meta['saswp_askpublicnewsarticle_about_'.$schema_id][0]);
    }
    $itemlist  = get_post_meta($schema_post_id, 'askpublicnewsarticle_items_'.$schema_id, true);
    
    if($itemlist){
        
        $list_arr = array();
        
        foreach ($itemlist as $list){
            $list_arr[] = $list['saswp_askpublicnewsarticle_items_name'];
        }
        
        $input1['mainEntity']['@type']            = 'ItemList';
        $input1['mainEntity']['itemListElement']  = $list_arr;                 
        $input1['mainEntity']['itemListOrder']    = 'http://schema.org/ItemListOrderAscending ';
        $input1['mainEntity']['name']             = saswp_get_the_title();
        
    }

    if(isset($all_post_meta['saswp_askpublicnewsarticle_speakable_'.$schema_id]) && $all_post_meta['saswp_askpublicnewsarticle_speakable_'.$schema_id][0] == 1){

        $input1['speakable']['@type'] = 'SpeakableSpecification';
        $input1['speakable']['xpath'] = array(
                "/html/head/title",
                "/html/head/meta[@name='description']/@content"
        );

        }

return $input1;

}

function saswp_background_newsarticle_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
    
    $slogo        = get_post_meta( get_the_ID(), 'saswp_backgroundnewsarticle_organization_logo_'.$schema_id.'_detail',true);
    $author_image = get_post_meta( get_the_ID(), 'saswp_askpublicnewsarticle_author_image_'.$schema_id.'_detail',true);
    $checkIdPro = ((isset($all_post_meta['saswp_askpublicnewsarticle_id_'.$schema_id][0]) && $all_post_meta['saswp_askpublicnewsarticle_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_askpublicnewsarticle_id_'.$schema_id][0] : ''); 
                 
    $input1 = array(
        '@context'			            => saswp_context_url(),
        '@type'				            => 'AskPublicNewsArticle' ,
        '@id'                           => $checkIdPro,    
        'inLanguage'                    => saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_inlanguage_'.$schema_id, 'saswp_array'),       
        'mainEntityOfPage'              => saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_main_entity_of_page_'.$schema_id, 'saswp_array'),
        'url'			            	=> saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_URL_'.$schema_id, 'saswp_array'),
        'image'				            => saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_image_'.$schema_id, 'saswp_array'),
        'headline'			            => saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_headline_'.$schema_id, 'saswp_array'),
        'datePublished'                 => isset($all_post_meta['saswp_askpublicnewsarticle_date_published_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_askpublicnewsarticle_date_published_'.$schema_id][0], get_post_time('h:i:s')) :'',
        'dateModified'                  => isset($all_post_meta['saswp_askpublicnewsarticle_date_modified_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_askpublicnewsarticle_date_modified_'.$schema_id][0], get_the_modified_time('h:i:s')) :'',
        'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_description_'.$schema_id, 'saswp_array'),
        'keywords'		                => saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_keywords_'.$schema_id, 'saswp_array'),
        'articleSection'                => saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_section_'.$schema_id, 'saswp_array'),
        'articleBody'                   => saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_body_'.$schema_id, 'saswp_array'),     
        'name'				            => saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_name_'.$schema_id, 'saswp_array'), 					
        'thumbnailUrl'                  => saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_thumbnailurl_'.$schema_id, 'saswp_array'),
        'wordCount'                     => saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_word_count_'.$schema_id, 'saswp_array'),
        'timeRequired'                  => saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_timerequired_'.$schema_id, 'saswp_array'),    
        'mainEntity'                    => array(
                                                  '@type' => 'WebPage',
                                                  '@id'   => saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_main_entity_id_'.$schema_id, 'saswp_array'),
            ), 					
        'publisher'			=> array(
                '@type'				=> 'Organization',
                'logo' 				=> array(
                '@type'				=> 'ImageObject',
                'url'				=> saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_organization_logo_'.$schema_id, 'saswp_array'),
                'width'				=> saswp_remove_warnings($slogo, 'width', 'saswp_string'),
                'height'			=> saswp_remove_warnings($slogo, 'height', 'saswp_string'),
                            ),
                'name'				=> saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_organization_name_'.$schema_id, 'saswp_array'),
                ),
        );
                            
    if(empty($input1['@id'])){
        unset($input1['@id']);
    }

    $input1['author']['@type']       = 'Person';

    if(isset( $all_post_meta['saswp_askpublicnewsarticle_author_type_'.$schema_id][0] )){
        $input1['author']['@type']       = $all_post_meta['saswp_askpublicnewsarticle_author_type_'.$schema_id][0];
    }  

    $input1['author']['name']        = saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_author_name_'.$schema_id, 'saswp_array');
    $input1['author']['description'] = saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_author_description_'.$schema_id, 'saswp_array');
    $input1['author']['url']         = saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_author_url_'.$schema_id, 'saswp_array');       

    $input1['author']['image']['@type']   = 'ImageObject';
    $input1['author']['image']['url']     = saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_author_image_'.$schema_id, 'saswp_array');       
    $input1['author']['image']['height']  = isset($author_image['height']) ? $author_image['height'] : '';
    $input1['author']['image']['width']   = isset($author_image['width']) ? $author_image['width'] : '';

    if( !empty($all_post_meta['saswp_askpublicnewsarticle_editor_type_'.$schema_id][0]) && isset( $all_post_meta['saswp_askpublicnewsarticle_editor_type_'.$schema_id][0] )){
        $input1['editor']['@type']       = 'Person';
        if(!empty( $all_post_meta['saswp_askpublicnewsarticle_editor_type_'.$schema_id][0] )){
            $input1['editor']['@type']       = $all_post_meta['saswp_askpublicnewsarticle_editor_type_'.$schema_id][0];
        }  
        if(!empty( $all_post_meta['saswp_askpublicnewsarticle_editor_name_'.$schema_id][0] )){
            $input1['editor']['name']        = saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_editor_name_'.$schema_id, 'saswp_array');
        }  
        if(!empty( $all_post_meta['saswp_askpublicnewsarticle_editor_honorific_suffix_'.$schema_id][0] )){
            $input1['editor']['honorificSuffix']  = saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_editor_honorific_suffix_'.$schema_id, 'saswp_array');
        }  
        if(!empty( $all_post_meta['saswp_askpublicnewsarticle_editor_description_'.$schema_id][0] )){
            $input1['editor']['description'] = saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_editor_description_'.$schema_id, 'saswp_array');
        }  
        if(!empty( $all_post_meta['saswp_askpublicnewsarticle_editor_url_'.$schema_id][0] )){
            $input1['editor']['url']         = saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_editor_url_'.$schema_id, 'saswp_array');   
        }  
        if(!empty( $all_post_meta['saswp_askpublicnewsarticle_editor_image_'.$schema_id][0] )){
            $input1['editor']['image']['@type']   = 'ImageObject';
            $input1['editor']['image']['url']     = saswp_remove_warnings($all_post_meta, 'saswp_askpublicnewsarticle_editor_image_'.$schema_id, 'saswp_array');       
            $input1['editor']['image']['height']  = isset($author_image['height']) ? $author_image['height'] : '';
            $input1['editor']['image']['width']   = isset($author_image['width']) ? $author_image['width'] : '';
        }  
    }

    if( !empty($all_post_meta['saswp_askpublicnewsarticle_about_'.$schema_id][0]) && isset( $all_post_meta['saswp_askpublicnewsarticle_about_'.$schema_id][0] )){
        $input1['about']['@type']       = 'Event';
        $input1['about']['name']       = explode(',', $all_post_meta['saswp_askpublicnewsarticle_about_'.$schema_id][0]);
    }
    $itemlist  = get_post_meta($schema_post_id, 'askpublicnewsarticle_items_'.$schema_id, true);
    
    if($itemlist){
        
        $list_arr = array();
        
        foreach ($itemlist as $list){
            $list_arr[] = $list['saswp_askpublicnewsarticle_items_name'];
        }
        
        $input1['mainEntity']['@type']            = 'ItemList';
        $input1['mainEntity']['itemListElement']  = $list_arr;                 
        $input1['mainEntity']['itemListOrder']    = 'http://schema.org/ItemListOrderAscending ';
        $input1['mainEntity']['name']             = saswp_get_the_title();
        
    }

    if(isset($all_post_meta['saswp_askpublicnewsarticle_speakable_'.$schema_id]) && $all_post_meta['saswp_askpublicnewsarticle_speakable_'.$schema_id][0] == 1){

        $input1['speakable']['@type'] = 'SpeakableSpecification';
        $input1['speakable']['xpath'] = array(
                "/html/head/title",
                "/html/head/meta[@name='description']/@content"
        );

        }

return $input1;

}

function saswp_opinion_newsarticle_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
    
    $slogo        = get_post_meta( get_the_ID(), 'saswp_opinionnewsarticle_organization_logo_'.$schema_id.'_detail',true);
    $author_image = get_post_meta( get_the_ID(), 'saswp_opinionnewsarticle_author_image_'.$schema_id.'_detail',true);
    $checkIdPro = ((isset($all_post_meta['saswp_opinionnewsarticle_id_'.$schema_id][0]) && $all_post_meta['saswp_opinionnewsarticle_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_opinionnewsarticle_id_'.$schema_id][0] : ''); 
                 
    $input1 = array(
        '@context'			            => saswp_context_url(),
        '@type'				            => 'OpinionNewsArticle' ,
        '@id'                           => $checkIdPro,    
        'inLanguage'                    => saswp_remove_warnings($all_post_meta, 'saswp_opinionnewsarticle_inlanguage_'.$schema_id, 'saswp_array'),       
        'mainEntityOfPage'              => saswp_remove_warnings($all_post_meta, 'saswp_opinionnewsarticle_main_entity_of_page_'.$schema_id, 'saswp_array'),
        'url'			            	=> saswp_remove_warnings($all_post_meta, 'saswp_opinionnewsarticle_URL_'.$schema_id, 'saswp_array'),
        'image'				            => saswp_remove_warnings($all_post_meta, 'saswp_opinionnewsarticle_image_'.$schema_id, 'saswp_array'),
        'headline'			            => saswp_remove_warnings($all_post_meta, 'saswp_opinionnewsarticle_headline_'.$schema_id, 'saswp_array'),
        'datePublished'                 => isset($all_post_meta['saswp_opinionnewsarticle_date_published_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_opinionnewsarticle_date_published_'.$schema_id][0], get_post_time('h:i:s')) :'',
        'dateModified'                  => isset($all_post_meta['saswp_opinionnewsarticle_date_modified_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_opinionnewsarticle_date_modified_'.$schema_id][0], get_the_modified_time('h:i:s')) :'',
        'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_opinionnewsarticle_description_'.$schema_id, 'saswp_array'),
        'keywords'		                => saswp_remove_warnings($all_post_meta, 'saswp_opinionnewsarticle_keywords_'.$schema_id, 'saswp_array'),
        'articleSection'                => saswp_remove_warnings($all_post_meta, 'saswp_opinionnewsarticle_section_'.$schema_id, 'saswp_array'),
        'articleBody'                   => saswp_remove_warnings($all_post_meta, 'saswp_opinionnewsarticle_body_'.$schema_id, 'saswp_array'),     
        'name'				            => saswp_remove_warnings($all_post_meta, 'saswp_opinionnewsarticle_name_'.$schema_id, 'saswp_array'), 					
        'thumbnailUrl'                  => saswp_remove_warnings($all_post_meta, 'saswp_opinionnewsarticle_thumbnailurl_'.$schema_id, 'saswp_array'),
        'wordCount'                     => saswp_remove_warnings($all_post_meta, 'saswp_opinionnewsarticle_word_count_'.$schema_id, 'saswp_array'),
        'timeRequired'                  => saswp_remove_warnings($all_post_meta, 'saswp_opinionnewsarticle_timerequired_'.$schema_id, 'saswp_array'),    
        'mainEntity'                    => array(
                                                  '@type' => 'WebPage',
                                                  '@id'   => saswp_remove_warnings($all_post_meta, 'saswp_opinionnewsarticle_main_entity_id_'.$schema_id, 'saswp_array'),
            ), 					
        'publisher'			=> array(
                '@type'				=> 'Organization',
                'logo' 				=> array(
                '@type'				=> 'ImageObject',
                'url'				=> saswp_remove_warnings($all_post_meta, 'saswp_opinionnewsarticle_organization_logo_'.$schema_id, 'saswp_array'),
                'width'				=> saswp_remove_warnings($slogo, 'width', 'saswp_string'),
                'height'			=> saswp_remove_warnings($slogo, 'height', 'saswp_string'),
                            ),
                'name'				=> saswp_remove_warnings($all_post_meta, 'saswp_opinionnewsarticle_organization_name_'.$schema_id, 'saswp_array'),
                ),
        );
                            
    if(empty($input1['@id'])){
        unset($input1['@id']);
    }

    $input1['author']['@type']       = 'Person';

    if(isset( $all_post_meta['saswp_opinionnewsarticle_author_type_'.$schema_id][0] )){
        $input1['author']['@type']       = $all_post_meta['saswp_opinionnewsarticle_author_type_'.$schema_id][0];
    }  

    $input1['author']['name']        = saswp_remove_warnings($all_post_meta, 'saswp_opinionnewsarticle_author_name_'.$schema_id, 'saswp_array');
    $input1['author']['description'] = saswp_remove_warnings($all_post_meta, 'saswp_opinionnewsarticle_author_description_'.$schema_id, 'saswp_array');
    $input1['author']['url']         = saswp_remove_warnings($all_post_meta, 'saswp_opinionnewsarticle_author_url_'.$schema_id, 'saswp_array');       

    $input1['author']['image']['@type']   = 'ImageObject';
    $input1['author']['image']['url']     = saswp_remove_warnings($all_post_meta, 'saswp_opinionnewsarticle_author_image_'.$schema_id, 'saswp_array');       
    $input1['author']['image']['height']  = isset($author_image['height']) ? $author_image['height'] : '';
    $input1['author']['image']['width']   = isset($author_image['width']) ? $author_image['width'] : '';

    if( !empty($all_post_meta['saswp_opinionnewsarticle_editor_type_'.$schema_id][0]) && isset( $all_post_meta['saswp_opinionnewsarticle_editor_type_'.$schema_id][0] )){
        $input1['editor']['@type']       = 'Person';
        if(!empty( $all_post_meta['saswp_opinionnewsarticle_editor_type_'.$schema_id][0] )){
            $input1['editor']['@type']       = $all_post_meta['saswp_opinionnewsarticle_editor_type_'.$schema_id][0];
        }  
        if(!empty( $all_post_meta['saswp_opinionnewsarticle_editor_name_'.$schema_id][0] )){
            $input1['editor']['name']        = saswp_remove_warnings($all_post_meta, 'saswp_opinionnewsarticle_editor_name_'.$schema_id, 'saswp_array');
        }  
        if(!empty( $all_post_meta['saswp_opinionnewsarticle_editor_honorific_suffix_'.$schema_id][0] )){
            $input1['editor']['honorificSuffix']  = saswp_remove_warnings($all_post_meta, 'saswp_opinionnewsarticle_editor_honorific_suffix_'.$schema_id, 'saswp_array');
        }  
        if(!empty( $all_post_meta['saswp_opinionnewsarticle_editor_description_'.$schema_id][0] )){
            $input1['editor']['description'] = saswp_remove_warnings($all_post_meta, 'saswp_opinionnewsarticle_editor_description_'.$schema_id, 'saswp_array');
        }  
        if(!empty( $all_post_meta['saswp_opinionnewsarticle_editor_url_'.$schema_id][0] )){
            $input1['editor']['url']         = saswp_remove_warnings($all_post_meta, 'saswp_opinionnewsarticle_editor_url_'.$schema_id, 'saswp_array');   
        }  
        if(!empty( $all_post_meta['saswp_opinionnewsarticle_editor_image_'.$schema_id][0] )){
            $input1['editor']['image']['@type']   = 'ImageObject';
            $input1['editor']['image']['url']     = saswp_remove_warnings($all_post_meta, 'saswp_opinionnewsarticle_editor_image_'.$schema_id, 'saswp_array');       
            $input1['editor']['image']['height']  = isset($author_image['height']) ? $author_image['height'] : '';
            $input1['editor']['image']['width']   = isset($author_image['width']) ? $author_image['width'] : '';
        }  
    }

    if( !empty($all_post_meta['saswp_opinionnewsarticle_about_'.$schema_id][0]) && isset( $all_post_meta['saswp_opinionnewsarticle_about_'.$schema_id][0] )){
        $input1['about']['@type']       = 'Event';
        $input1['about']['name']       = explode(',', $all_post_meta['saswp_opinionnewsarticle_about_'.$schema_id][0]);
    }
    $itemlist  = get_post_meta($schema_post_id, 'opinionnewsarticle_items_'.$schema_id, true);
    
    if($itemlist){
        
        $list_arr = array();
        
        foreach ($itemlist as $list){
            $list_arr[] = $list['saswp_opinionnewsarticle_items_name'];
        }
        
        $input1['mainEntity']['@type']            = 'ItemList';
        $input1['mainEntity']['itemListElement']  = $list_arr;                 
        $input1['mainEntity']['itemListOrder']    = 'http://schema.org/ItemListOrderAscending ';
        $input1['mainEntity']['name']             = saswp_get_the_title();
        
    }

    if(isset($all_post_meta['saswp_opinionnewsarticle_speakable_'.$schema_id]) && $all_post_meta['saswp_opinionnewsarticle_speakable_'.$schema_id][0] == 1){

        $input1['speakable']['@type'] = 'SpeakableSpecification';
        $input1['speakable']['xpath'] = array(
                "/html/head/title",
                "/html/head/meta[@name='description']/@content"
        );

        }

return $input1;

}


function saswp_reportage_newsarticle_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
    
    $slogo        = get_post_meta( get_the_ID(), 'saswp_reportagenewsarticle_organization_logo_'.$schema_id.'_detail',true);
    $author_image = get_post_meta( get_the_ID(), 'saswp_reportagenewsarticle_author_image_'.$schema_id.'_detail',true);
    $checkIdPro = ((isset($all_post_meta['saswp_reportagenewsarticle_id_'.$schema_id][0]) && $all_post_meta['saswp_reportagenewsarticle_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_reportagenewsarticle_id_'.$schema_id][0] : ''); 
                 
    $input1 = array(
        '@context'			            => saswp_context_url(),
        '@type'				            => 'ReportageNewsArticle' ,
        '@id'                           => $checkIdPro,    
        'inLanguage'                    => saswp_remove_warnings($all_post_meta, 'saswp_reportagenewsarticle_inlanguage_'.$schema_id, 'saswp_array'),       
        'mainEntityOfPage'              => saswp_remove_warnings($all_post_meta, 'saswp_reportagenewsarticle_main_entity_of_page_'.$schema_id, 'saswp_array'),
        'url'			            	=> saswp_remove_warnings($all_post_meta, 'saswp_reportagenewsarticle_URL_'.$schema_id, 'saswp_array'),
        'image'				            => saswp_remove_warnings($all_post_meta, 'saswp_reportagenewsarticle_image_'.$schema_id, 'saswp_array'),
        'headline'			            => saswp_remove_warnings($all_post_meta, 'saswp_reportagenewsarticle_headline_'.$schema_id, 'saswp_array'),
        'datePublished'                 => isset($all_post_meta['saswp_reportagenewsarticle_date_published_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_reportagenewsarticle_date_published_'.$schema_id][0], get_post_time('h:i:s')) :'',
        'dateModified'                  => isset($all_post_meta['saswp_reportagenewsarticle_date_modified_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_reportagenewsarticle_date_modified_'.$schema_id][0], get_the_modified_time('h:i:s')) :'',
        'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_reportagenewsarticle_description_'.$schema_id, 'saswp_array'),
        'keywords'		                => saswp_remove_warnings($all_post_meta, 'saswp_reportagenewsarticle_keywords_'.$schema_id, 'saswp_array'),
        'articleSection'                => saswp_remove_warnings($all_post_meta, 'saswp_reportagenewsarticle_section_'.$schema_id, 'saswp_array'),
        'articleBody'                   => saswp_remove_warnings($all_post_meta, 'saswp_reportagenewsarticle_body_'.$schema_id, 'saswp_array'),     
        'name'				            => saswp_remove_warnings($all_post_meta, 'saswp_reportagenewsarticle_name_'.$schema_id, 'saswp_array'), 					
        'thumbnailUrl'                  => saswp_remove_warnings($all_post_meta, 'saswp_reportagenewsarticle_thumbnailurl_'.$schema_id, 'saswp_array'),
        'wordCount'                     => saswp_remove_warnings($all_post_meta, 'saswp_reportagenewsarticle_word_count_'.$schema_id, 'saswp_array'),
        'timeRequired'                  => saswp_remove_warnings($all_post_meta, 'saswp_reportagenewsarticle_timerequired_'.$schema_id, 'saswp_array'),    
        'mainEntity'                    => array(
                                                  '@type' => 'WebPage',
                                                  '@id'   => saswp_remove_warnings($all_post_meta, 'saswp_reportagenewsarticle_main_entity_id_'.$schema_id, 'saswp_array'),
            ), 					
        'publisher'			=> array(
                '@type'				=> 'Organization',
                'logo' 				=> array(
                '@type'				=> 'ImageObject',
                'url'				=> saswp_remove_warnings($all_post_meta, 'saswp_reportagenewsarticle_organization_logo_'.$schema_id, 'saswp_array'),
                'width'				=> saswp_remove_warnings($slogo, 'width', 'saswp_string'),
                'height'			=> saswp_remove_warnings($slogo, 'height', 'saswp_string'),
                            ),
                'name'				=> saswp_remove_warnings($all_post_meta, 'saswp_reportagenewsarticle_organization_name_'.$schema_id, 'saswp_array'),
                ),
        );
                            
    if(empty($input1['@id'])){
        unset($input1['@id']);
    }

    $input1['author']['@type']       = 'Person';

    if(isset( $all_post_meta['saswp_reportagenewsarticle_author_type_'.$schema_id][0] )){
        $input1['author']['@type']       = $all_post_meta['saswp_reportagenewsarticle_author_type_'.$schema_id][0];
    }  

    $input1['author']['name']        = saswp_remove_warnings($all_post_meta, 'saswp_reportagenewsarticle_author_name_'.$schema_id, 'saswp_array');
    $input1['author']['description'] = saswp_remove_warnings($all_post_meta, 'saswp_reportagenewsarticle_author_description_'.$schema_id, 'saswp_array');
    $input1['author']['url']         = saswp_remove_warnings($all_post_meta, 'saswp_reportagenewsarticle_author_url_'.$schema_id, 'saswp_array');       

    $input1['author']['image']['@type']   = 'ImageObject';
    $input1['author']['image']['url']     = saswp_remove_warnings($all_post_meta, 'saswp_reportagenewsarticle_author_image_'.$schema_id, 'saswp_array');       
    $input1['author']['image']['height']  = isset($author_image['height']) ? $author_image['height'] : '';
    $input1['author']['image']['width']   = isset($author_image['width']) ? $author_image['width'] : '';

    if( !empty($all_post_meta['saswp_reportagenewsarticle_editor_type_'.$schema_id][0]) && isset( $all_post_meta['saswp_reportagenewsarticle_editor_type_'.$schema_id][0] )){
        $input1['editor']['@type']       = 'Person';
        if(!empty( $all_post_meta['saswp_reportagenewsarticle_editor_type_'.$schema_id][0] )){
            $input1['editor']['@type']       = $all_post_meta['saswp_reportagenewsarticle_editor_type_'.$schema_id][0];
        }  
        if(!empty( $all_post_meta['saswp_reportagenewsarticle_editor_name_'.$schema_id][0] )){
            $input1['editor']['name']        = saswp_remove_warnings($all_post_meta, 'saswp_reportagenewsarticle_editor_name_'.$schema_id, 'saswp_array');
        }  
        if(!empty( $all_post_meta['saswp_reportagenewsarticle_editor_honorific_suffix_'.$schema_id][0] )){
            $input1['editor']['honorificSuffix']  = saswp_remove_warnings($all_post_meta, 'saswp_reportagenewsarticle_editor_honorific_suffix_'.$schema_id, 'saswp_array');
        }  
        if(!empty( $all_post_meta['saswp_reportagenewsarticle_editor_description_'.$schema_id][0] )){
            $input1['editor']['description'] = saswp_remove_warnings($all_post_meta, 'saswp_reportagenewsarticle_editor_description_'.$schema_id, 'saswp_array');
        }  
        if(!empty( $all_post_meta['saswp_reportagenewsarticle_editor_url_'.$schema_id][0] )){
            $input1['editor']['url']         = saswp_remove_warnings($all_post_meta, 'saswp_reportagenewsarticle_editor_url_'.$schema_id, 'saswp_array');   
        }  
        if(!empty( $all_post_meta['saswp_reportagenewsarticle_editor_image_'.$schema_id][0] )){
            $input1['editor']['image']['@type']   = 'ImageObject';
            $input1['editor']['image']['url']     = saswp_remove_warnings($all_post_meta, 'saswp_reportagenewsarticle_editor_image_'.$schema_id, 'saswp_array');       
            $input1['editor']['image']['height']  = isset($author_image['height']) ? $author_image['height'] : '';
            $input1['editor']['image']['width']   = isset($author_image['width']) ? $author_image['width'] : '';
        }  
    }

    if( !empty($all_post_meta['saswp_reportagenewsarticle_about_'.$schema_id][0]) && isset( $all_post_meta['saswp_reportagenewsarticle_about_'.$schema_id][0] )){
        $input1['about']['@type']       = 'Event';
        $input1['about']['name']       = explode(',', $all_post_meta['saswp_reportagenewsarticle_about_'.$schema_id][0]);
    }
    $itemlist  = get_post_meta($schema_post_id, 'reportagenewsarticle_items_'.$schema_id, true);
    
    if($itemlist){
        
        $list_arr = array();
        
        foreach ($itemlist as $list){
            $list_arr[] = $list['saswp_reportagenewsarticle_items_name'];
        }
        
        $input1['mainEntity']['@type']            = 'ItemList';
        $input1['mainEntity']['itemListElement']  = $list_arr;                 
        $input1['mainEntity']['itemListOrder']    = 'http://schema.org/ItemListOrderAscending ';
        $input1['mainEntity']['name']             = saswp_get_the_title();
        
    }

    if(isset($all_post_meta['saswp_reportagenewsarticle_speakable_'.$schema_id]) && $all_post_meta['saswp_reportagenewsarticle_speakable_'.$schema_id][0] == 1){

        $input1['speakable']['@type'] = 'SpeakableSpecification';
        $input1['speakable']['xpath'] = array(
                "/html/head/title",
                "/html/head/meta[@name='description']/@content"
        );

        }

return $input1;

}

function saswp_review_newsarticle_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
    
    $slogo        = get_post_meta( get_the_ID(), 'saswp_reviewnewsarticle_organization_logo_'.$schema_id.'_detail',true);
    $author_image = get_post_meta( get_the_ID(), 'saswp_reviewnewsarticle_author_image_'.$schema_id.'_detail',true);
    $checkIdPro = ((isset($all_post_meta['saswp_reviewnewsarticle_id_'.$schema_id][0]) && $all_post_meta['saswp_reviewnewsarticle_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_reviewnewsarticle_id_'.$schema_id][0] : ''); 
                 
    $input1 = array(
        '@context'			            => saswp_context_url(),
        '@type'				            => 'ReviewNewsArticle' ,
        '@id'                           => $checkIdPro,    
        'inLanguage'                    => saswp_remove_warnings($all_post_meta, 'saswp_reviewnewsarticle_inlanguage_'.$schema_id, 'saswp_array'),       
        'mainEntityOfPage'              => saswp_remove_warnings($all_post_meta, 'saswp_reviewnewsarticle_main_entity_of_page_'.$schema_id, 'saswp_array'),
        'url'			            	=> saswp_remove_warnings($all_post_meta, 'saswp_reviewnewsarticle_URL_'.$schema_id, 'saswp_array'),
        'image'				            => saswp_remove_warnings($all_post_meta, 'saswp_reviewnewsarticle_image_'.$schema_id, 'saswp_array'),
        'headline'			            => saswp_remove_warnings($all_post_meta, 'saswp_reviewnewsarticle_headline_'.$schema_id, 'saswp_array'),
        'datePublished'                 => isset($all_post_meta['saswp_reviewnewsarticle_date_published_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_reviewnewsarticle_date_published_'.$schema_id][0], get_post_time('h:i:s')) :'',
        'dateModified'                  => isset($all_post_meta['saswp_reviewnewsarticle_date_modified_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_reviewnewsarticle_date_modified_'.$schema_id][0], get_the_modified_time('h:i:s')) :'',
        'description'                   => saswp_remove_warnings($all_post_meta, 'saswp_reviewnewsarticle_description_'.$schema_id, 'saswp_array'),
        'keywords'		                => saswp_remove_warnings($all_post_meta, 'saswp_reviewnewsarticle_keywords_'.$schema_id, 'saswp_array'),
        'articleSection'                => saswp_remove_warnings($all_post_meta, 'saswp_reviewnewsarticle_section_'.$schema_id, 'saswp_array'),
        'articleBody'                   => saswp_remove_warnings($all_post_meta, 'saswp_reviewnewsarticle_body_'.$schema_id, 'saswp_array'),     
        'name'				            => saswp_remove_warnings($all_post_meta, 'saswp_reviewnewsarticle_name_'.$schema_id, 'saswp_array'), 					
        'thumbnailUrl'                  => saswp_remove_warnings($all_post_meta, 'saswp_reviewnewsarticle_thumbnailurl_'.$schema_id, 'saswp_array'),
        'wordCount'                     => saswp_remove_warnings($all_post_meta, 'saswp_reviewnewsarticle_word_count_'.$schema_id, 'saswp_array'),
        'timeRequired'                  => saswp_remove_warnings($all_post_meta, 'saswp_reviewnewsarticle_timerequired_'.$schema_id, 'saswp_array'),    
        'mainEntity'                    => array(
                                                  '@type' => 'WebPage',
                                                  '@id'   => saswp_remove_warnings($all_post_meta, 'saswp_reviewnewsarticle_main_entity_id_'.$schema_id, 'saswp_array'),
            ), 					
        'publisher'			=> array(
                '@type'				=> 'Organization',
                'logo' 				=> array(
                '@type'				=> 'ImageObject',
                'url'				=> saswp_remove_warnings($all_post_meta, 'saswp_reviewnewsarticle_organization_logo_'.$schema_id, 'saswp_array'),
                'width'				=> saswp_remove_warnings($slogo, 'width', 'saswp_string'),
                'height'			=> saswp_remove_warnings($slogo, 'height', 'saswp_string'),
                            ),
                'name'				=> saswp_remove_warnings($all_post_meta, 'saswp_reviewnewsarticle_organization_name_'.$schema_id, 'saswp_array'),
                ),
        );
                            
    if(empty($input1['@id'])){
        unset($input1['@id']);
    }

    $input1['author']['@type']       = 'Person';

    if(isset( $all_post_meta['saswp_reviewnewsarticle_author_type_'.$schema_id][0] )){
        $input1['author']['@type']       = $all_post_meta['saswp_reviewnewsarticle_author_type_'.$schema_id][0];
    }  

    $input1['author']['name']        = saswp_remove_warnings($all_post_meta, 'saswp_reviewnewsarticle_author_name_'.$schema_id, 'saswp_array');
    $input1['author']['description'] = saswp_remove_warnings($all_post_meta, 'saswp_reviewnewsarticle_author_description_'.$schema_id, 'saswp_array');
    $input1['author']['url']         = saswp_remove_warnings($all_post_meta, 'saswp_reviewnewsarticle_author_url_'.$schema_id, 'saswp_array');       

    $input1['author']['image']['@type']   = 'ImageObject';
    $input1['author']['image']['url']     = saswp_remove_warnings($all_post_meta, 'saswp_reviewnewsarticle_author_image_'.$schema_id, 'saswp_array');       
    $input1['author']['image']['height']  = isset($author_image['height']) ? $author_image['height'] : '';
    $input1['author']['image']['width']   = isset($author_image['width']) ? $author_image['width'] : '';

    if( !empty($all_post_meta['saswp_reviewnewsarticle_editor_type_'.$schema_id][0]) && isset( $all_post_meta['saswp_reviewnewsarticle_editor_type_'.$schema_id][0] )){
        $input1['editor']['@type']       = 'Person';
        if(!empty( $all_post_meta['saswp_reviewnewsarticle_editor_type_'.$schema_id][0] )){
            $input1['editor']['@type']       = $all_post_meta['saswp_reviewnewsarticle_editor_type_'.$schema_id][0];
        }  
        if(!empty( $all_post_meta['saswp_reviewnewsarticle_editor_name_'.$schema_id][0] )){
            $input1['editor']['name']        = saswp_remove_warnings($all_post_meta, 'saswp_reviewnewsarticle_editor_name_'.$schema_id, 'saswp_array');
        }  
        if(!empty( $all_post_meta['saswp_reviewnewsarticle_editor_honorific_suffix_'.$schema_id][0] )){
            $input1['editor']['honorificSuffix']  = saswp_remove_warnings($all_post_meta, 'saswp_reviewnewsarticle_editor_honorific_suffix_'.$schema_id, 'saswp_array');
        }  
        if(!empty( $all_post_meta['saswp_reviewnewsarticle_editor_description_'.$schema_id][0] )){
            $input1['editor']['description'] = saswp_remove_warnings($all_post_meta, 'saswp_reviewnewsarticle_editor_description_'.$schema_id, 'saswp_array');
        }  
        if(!empty( $all_post_meta['saswp_reviewnewsarticle_editor_url_'.$schema_id][0] )){
            $input1['editor']['url']         = saswp_remove_warnings($all_post_meta, 'saswp_reviewnewsarticle_editor_url_'.$schema_id, 'saswp_array');   
        }  
        if(!empty( $all_post_meta['saswp_reviewnewsarticle_editor_image_'.$schema_id][0] )){
            $input1['editor']['image']['@type']   = 'ImageObject';
            $input1['editor']['image']['url']     = saswp_remove_warnings($all_post_meta, 'saswp_reviewnewsarticle_editor_image_'.$schema_id, 'saswp_array');       
            $input1['editor']['image']['height']  = isset($author_image['height']) ? $author_image['height'] : '';
            $input1['editor']['image']['width']   = isset($author_image['width']) ? $author_image['width'] : '';
        }  
    }

    if( !empty($all_post_meta['saswp_reviewnewsarticle_about_'.$schema_id][0]) && isset( $all_post_meta['saswp_reviewnewsarticle_about_'.$schema_id][0] )){
        $input1['about']['@type']       = 'Event';
        $input1['about']['name']       = explode(',', $all_post_meta['saswp_reviewnewsarticle_about_'.$schema_id][0]);
    }
    $itemlist  = get_post_meta($schema_post_id, 'reviewnewsarticle_items_'.$schema_id, true);
    
    if($itemlist){
        
        $list_arr = array();
        
        foreach ($itemlist as $list){
            $list_arr[] = $list['saswp_reviewnewsarticle_items_name'];
        }
        
        $input1['mainEntity']['@type']            = 'ItemList';
        $input1['mainEntity']['itemListElement']  = $list_arr;                 
        $input1['mainEntity']['itemListOrder']    = 'http://schema.org/ItemListOrderAscending ';
        $input1['mainEntity']['name']             = saswp_get_the_title();
        
    }

    if(isset($all_post_meta['saswp_reviewnewsarticle_speakable_'.$schema_id]) && $all_post_meta['saswp_reviewnewsarticle_speakable_'.$schema_id][0] == 1){

        $input1['speakable']['@type'] = 'SpeakableSpecification';
        $input1['speakable']['xpath'] = array(
                "/html/head/title",
                "/html/head/meta[@name='description']/@content"
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

function saswp_audiobook_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
                $input1 = array();
    
                $author_image       = get_post_meta( get_the_ID(), 'saswp_audiobook_author_image_'.$schema_id.'_detail',true);
                $checkIdPro = ((isset($all_post_meta['saswp_audiobook_id_'.$schema_id][0]) && $all_post_meta['saswp_audiobook_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_audiobook_id_'.$schema_id][0] : '');
                                            
                $input1 = array(
                '@context'			=> saswp_context_url(),
                '@type'				=> 'Audiobook' ,
                '@id'               => $checkIdPro,    
                'inLanguage'        => get_bloginfo('language'),       
                'mainEntityOfPage'  => saswp_remove_warnings($all_post_meta, 'saswp_audiobook_main_entity_of_page_'.$schema_id, 'saswp_array'),
                'url'				=> saswp_remove_warnings($all_post_meta, 'saswp_audiobook_url_'.$schema_id, 'saswp_array'),                    
                'name'		    	=> saswp_remove_warnings($all_post_meta, 'saswp_audiobook_name_'.$schema_id, 'saswp_array'),
                'datePublished'     => isset($all_post_meta['saswp_audiobook_date_published_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_audiobook_date_published_'.$schema_id][0], get_post_time('h:i:s')) :'',
                'dateModified'      => isset($all_post_meta['saswp_audiobook_date_modified_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_audiobook_date_modified_'.$schema_id][0], get_the_modified_time('h:i:s')) :'',
                'description'       => saswp_remove_warnings($all_post_meta, 'saswp_audiobook_description_'.$schema_id, 'saswp_array'),                                         					
                'contentUrl'        => saswp_remove_warnings($all_post_meta, 'saswp_audiobook_content_url_'.$schema_id, 'saswp_array'),         
                
                'publisher'         => saswp_remove_warnings($all_post_meta, 'saswp_audiobook_publisher_'.$schema_id, 'saswp_array'),         
                'provider'          => saswp_remove_warnings($all_post_meta, 'saswp_audiobook_provider_'.$schema_id, 'saswp_array'),         
                'duration'          => saswp_remove_warnings($all_post_meta, 'saswp_audiobook_duration_'.$schema_id, 'saswp_array'),         
                'encodingFormat'    => saswp_remove_warnings($all_post_meta, 'saswp_audiobook_encoding_format_'.$schema_id, 'saswp_array'),         
                'playerType'        => saswp_remove_warnings($all_post_meta, 'saswp_audiobook_player_type_'.$schema_id, 'saswp_array'),         
                'readBy'            => saswp_remove_warnings($all_post_meta, 'saswp_audiobook_readby_'.$schema_id, 'saswp_array')                                         				
                );

                if(empty($input1['@id'])){
                    unset($input1['@id']);
                }

                $input1['author']['@type']       = 'Person';

                if(isset( $all_post_meta['saswp_audiobook_author_type_'.$schema_id][0] )){
                    $input1['author']['@type']       = $all_post_meta['saswp_audiobook_author_type_'.$schema_id][0];
                }  
        
                $input1['author']['name']        = saswp_remove_warnings($all_post_meta, 'saswp_audiobook_author_name_'.$schema_id, 'saswp_array');
                $input1['author']['description'] = saswp_remove_warnings($all_post_meta, 'saswp_audiobook_author_description_'.$schema_id, 'saswp_array');
                $input1['author']['url']         = saswp_remove_warnings($all_post_meta, 'saswp_audiobook_author_url_'.$schema_id, 'saswp_array');       

                $input1['author']['image']['@type']   = 'ImageObject';
                $input1['author']['image']['url']     = saswp_remove_warnings($all_post_meta, 'saswp_audiobook_author_image_'.$schema_id, 'saswp_array');       
                $input1['author']['image']['height']  = $author_image['height'];
                $input1['author']['image']['width']   = $author_image['width'];

                $input1 = saswp_get_modified_image('saswp_audiobook_image_'.$schema_id.'_detail', $input1);
                                                                     
    return $input1;
    
}

function saswp_podcast_episode_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
    $checkIdPro = ((isset($all_post_meta['saswp_podcast_episode_id_'.$schema_id][0]) && $all_post_meta['saswp_podcast_episode_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_podcast_episode_id_'.$schema_id][0] : ''); 
                        
    $input1 = array(
        '@context'			=> saswp_context_url(),
        '@type'				=> 'PodcastEpisode' ,
        '@id'               => $checkIdPro,    
        'inLanguage'        => get_bloginfo('language'),           
        'url'				=> saswp_remove_warnings($all_post_meta, 'saswp_podcast_episode_url_'.$schema_id, 'saswp_array'),                    
        'name'		    	=> saswp_remove_warnings($all_post_meta, 'saswp_podcast_episode_name_'.$schema_id, 'saswp_array'),
        'datePublished'     => isset($all_post_meta['saswp_podcast_episode_date_published_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_podcast_episode_date_published_'.$schema_id][0], get_post_time('h:i:s')) :'',
        'dateModified'      => isset($all_post_meta['saswp_podcast_episode_date_modified_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_podcast_episode_date_modified_'.$schema_id][0], get_the_modified_time('h:i:s')) :'',
        'description'       => saswp_remove_warnings($all_post_meta, 'saswp_podcast_episode_description_'.$schema_id, 'saswp_array'),                                         					            
    );

    if(empty($input1['@id'])){
        unset($input1['@id']);
    }

    if(isset($all_post_meta['saswp_podcast_episode_content_url_'.$schema_id][0])){

        $input1['associatedMedia']['@type']      = 'MediaObject';
        $input1['associatedMedia']['contentUrl'] =   $all_post_meta['saswp_podcast_episode_content_url_'.$schema_id][0];
    }

    if(isset($all_post_meta['saswp_podcast_episode_series_name_'.$schema_id][0])){
        $input1['partOfSeries']['@type'] = 'PodcastSeries';
        $input1['partOfSeries']['name']  =   $all_post_meta['saswp_podcast_episode_series_name_'.$schema_id][0];
    }

    if(isset($all_post_meta['saswp_podcast_episode_series_url_'.$schema_id][0])){
        $input1['partOfSeries']['@type'] = 'PodcastSeries';
        $input1['partOfSeries']['url']   =    $all_post_meta['saswp_podcast_episode_series_url_'.$schema_id][0];
    }

    $input1 = saswp_get_modified_image('saswp_podcast_episode_image_'.$schema_id.'_detail', $input1);
                                                         
    return $input1;

}


function saswp_podcast_season_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();
    $checkIdPro = ((isset($all_post_meta['saswp_podcast_season_id_'.$schema_id][0]) && $all_post_meta['saswp_podcast_season_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_podcast_season_id_'.$schema_id][0] : '');
                        
    $input1 = array(
        '@context'			=> saswp_context_url(),
        '@type'				=> 'PodcastSeason' ,
        '@id'               => $checkIdPro,    
        'inLanguage'        => get_bloginfo('language'),           
        'url'				=> saswp_remove_warnings($all_post_meta, 'saswp_podcast_season_url_'.$schema_id, 'saswp_array'),                    
        'name'		    	=> saswp_remove_warnings($all_post_meta, 'saswp_podcast_season_name_'.$schema_id, 'saswp_array'),
        'datePublished'     => isset($all_post_meta['saswp_podcast_season_date_published_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_podcast_season_date_published_'.$schema_id][0], get_post_time('h:i:s')) :'',
        'dateModified'      => isset($all_post_meta['saswp_podcast_season_date_modified_'.$schema_id])? saswp_format_date_time($all_post_meta['saswp_podcast_season_date_modified_'.$schema_id][0], get_the_modified_time('h:i:s')) :'',
        'description'       => saswp_remove_warnings($all_post_meta, 'saswp_podcast_season_description_'.$schema_id, 'saswp_array'),                                         					            
    );

    if(empty($input1['@id'])){
        unset($input1['@id']);
    }

    if(isset($all_post_meta['saswp_podcast_season_number_'.$schema_id][0])){
    
        $input1['seasonNumber'] =   $all_post_meta['saswp_podcast_season_number_'.$schema_id][0];
    }

    if(isset($all_post_meta['saswp_podcast_season_number_of_seasons_'.$schema_id][0])){
    
        $input1['numberOfEpisodes'] =   $all_post_meta['saswp_podcast_season_number_of_seasons_'.$schema_id][0];
    }

    if(isset($all_post_meta['saswp_podcast_season_series_name_'.$schema_id][0])){
        $input1['partOfSeries']['@type'] = 'PodcastSeries';
        $input1['partOfSeries']['name']  =   $all_post_meta['saswp_podcast_season_series_name_'.$schema_id][0];
    }

    if(isset($all_post_meta['saswp_podcast_season_series_url_'.$schema_id][0])){
        $input1['partOfSeries']['@type'] = 'PodcastSeries';
        $input1['partOfSeries']['url']   =    $all_post_meta['saswp_podcast_season_series_url_'.$schema_id][0];
    }

    $input1 = saswp_get_modified_image('saswp_podcast_season_image_'.$schema_id.'_detail', $input1);
                                                         
    return $input1;

}


function saswp_video_object_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
        $input1 = array();
    
        $slogo = get_post_meta( get_the_ID(), 'saswp_video_object_organization_logo_'.$schema_id.'_detail',true);
        $author_image = get_post_meta( get_the_ID(), 'saswp_video_object_author_image_'.$schema_id.'_detail',true);

        $checkIdPro = ((isset($all_post_meta['saswp_video_object_id_'.$schema_id][0]) && $all_post_meta['saswp_video_object_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_video_object_id_'.$schema_id][0] : '');

        $input1 = array(
        '@context'			            => saswp_context_url(),
        '@type'				            => 'VideoObject',
        '@id'                           => $checkIdPro,    
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

        if(empty($input1['@id'])){
            unset($input1['@id']);
        }

        $input1['author']['@type']       = 'Person';

        if(isset( $all_post_meta['saswp_video_object_author_type_'.$schema_id][0] )){
            $input1['author']['@type']       = $all_post_meta['saswp_video_object_author_type_'.$schema_id][0];
        }  

        $input1['author']['name']        = saswp_remove_warnings($all_post_meta, 'saswp_video_object_author_name_'.$schema_id, 'saswp_array');
        $input1['author']['description'] = saswp_remove_warnings($all_post_meta, 'saswp_video_object_author_description_'.$schema_id, 'saswp_array');
        $input1['author']['url']         = saswp_remove_warnings($all_post_meta, 'saswp_video_object_author_url_'.$schema_id, 'saswp_array');       

        if(!empty($author_image) && is_array($author_image)){

            $input1['author']['image']['@type']   = 'ImageObject';
            $input1['author']['image']['url']     = saswp_remove_warnings($all_post_meta, 'saswp_video_object_author_image_'.$schema_id, 'saswp_array');       
            $input1['author']['image']['height']  = $author_image['height'];
            $input1['author']['image']['width']   = $author_image['width'];

        }
            
        if(isset($all_post_meta['saswp_video_object_duration_'.$schema_id][0]) ) {
            $input1['duration']   = $all_post_meta['saswp_video_object_duration_'.$schema_id][0];        
        }    

        if(isset($all_post_meta['saswp_video_object_content_url_'.$schema_id][0]) && wp_http_validate_url($all_post_meta['saswp_video_object_content_url_'.$schema_id][0]) ) {
            $input1['contentUrl']   = $all_post_meta['saswp_video_object_content_url_'.$schema_id][0];        
        }
        if(isset($all_post_meta['saswp_video_object_embed_url_'.$schema_id][0]) && wp_http_validate_url($all_post_meta['saswp_video_object_embed_url_'.$schema_id][0])){
            $input1['embedUrl']     = $all_post_meta['saswp_video_object_embed_url_'.$schema_id][0];        
        }
        
        if(!empty($all_post_meta['saswp_video_object_seek_to_seconds_'.$schema_id][0]) && !empty($all_post_meta['saswp_video_object_seek_to_video_url_'.$schema_id][0])){

            $input1['potentialAction']['@type']             = 'SeekToAction';
            $input1['potentialAction']['target']            = $all_post_meta['saswp_video_object_seek_to_video_url_'.$schema_id][0].'?t'.$all_post_meta['saswp_video_object_seek_to_seconds_'.$schema_id][0];
            $input1['potentialAction']['startOffset-input'] = 'required name=seek_to_second_number';

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
        '@id'                           => get_permalink().'#imageobject',    
        'url'				=> saswp_remove_warnings($all_post_meta, 'saswpimage_object_url_'.$schema_id, 'saswp_array'),						
        'datePublished'                 => isset($all_post_meta['saswpimage_object_date_published_'.$schema_id])? saswp_format_date_time($all_post_meta['saswpimage_object_date_published_'.$schema_id][0], get_post_time('h:i:s')) : '',
        'dateModified'                  => isset($all_post_meta['saswpimage_object_date_modified_'.$schema_id])? saswp_format_date_time($all_post_meta['saswpimage_object_date_modified_'.$schema_id][0], get_the_modified_time('h:i:s')) : '',
        'description'                   => saswp_remove_warnings($all_post_meta, 'saswpimage_object_description_'.$schema_id, 'saswp_array'),
        'name'				            => saswp_remove_warnings($all_post_meta, 'saswpimage_object_name_'.$schema_id, 'saswp_array'),
        'license'				        => saswp_remove_warnings($all_post_meta, 'saswpimage_object_license_'.$schema_id, 'saswp_array'),
        'acquireLicensePage'	        => saswp_remove_warnings($all_post_meta, 'saswpimage_object_acquire_license_page_'.$schema_id, 'saswp_array'),
        'uploadDate'                    => isset($all_post_meta['saswpimage_object_upload_date_'.$schema_id])?date('Y-m-d\TH:i:s\Z',strtotime($all_post_meta['saswpimage_object_upload_date_'.$schema_id][0])):'',						
        'contentUrl'                    => saswp_remove_warnings($all_post_meta, 'saswpimage_object_content_url_'.$schema_id, 'saswp_array'),
        'contentLocation'                    => saswp_remove_warnings($all_post_meta, 'saswpimage_object_content_location_'.$schema_id, 'saswp_array'),						        
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

        $input1['author']['@type']       = 'Person';

        if(isset( $all_post_meta['saswpimage_object_author_type_'.$schema_id][0] )){
            $input1['author']['@type']       = $all_post_meta['saswpimage_object_author_type_'.$schema_id][0];
        }  

        $input1['author']['name']        = saswp_remove_warnings($all_post_meta, 'saswpimage_object_author_name_'.$schema_id, 'saswp_array');
        $input1['author']['description'] = saswp_remove_warnings($all_post_meta, 'saswpimage_object_author_description_'.$schema_id, 'saswp_array');
        $input1['author']['url']         = saswp_remove_warnings($all_post_meta, 'saswpimage_object_author_url_'.$schema_id, 'saswp_array');       

        $input1['author']['image']['@type']   = 'ImageObject';
        $input1['author']['image']['url']     = saswp_remove_warnings($all_post_meta, 'saswpimage_object_author_image_'.$schema_id, 'saswp_array'); 
        if((isset($author_image['height']) && !empty($author_image['height'])) && (isset($author_image['height']) && !empty($author_image['height'])))
        {      
            $input1['author']['image']['height']  = $author_image['height'];
            $input1['author']['image']['width']   = $author_image['width'];
        }

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

function saswp_taxi_service_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
    $input1 = array();

    $area_served_str = saswp_remove_warnings($all_post_meta, 'saswp_taxi_service_schema_area_served_'.$schema_id, 'saswp_array');
    $area_served_arr = explode(',', $area_served_str);

    $service_offer_str = saswp_remove_warnings($all_post_meta, 'saswp_taxi_service_schema_service_offer_'.$schema_id, 'saswp_array');
    $service_offer_arr = explode(',', $service_offer_str);

    $checkIdPro = ((isset($all_post_meta['saswp_taxi_service_schema_id_'.$schema_id][0]) && $all_post_meta['saswp_taxi_service_schema_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_taxi_service_schema_id_'.$schema_id][0] : '');

    $input1['@context']    = saswp_context_url();
    $input1['@type']       = 'TaxiService';
    if($checkIdPro){
        $input1['@id']     = $checkIdPro;  
    }
    $input1['name']        = saswp_remove_warnings($all_post_meta, 'saswp_taxi_service_schema_name_'.$schema_id, 'saswp_array');
    $input1['serviceType'] = saswp_remove_warnings($all_post_meta, 'saswp_taxi_service_schema_type_'.$schema_id, 'saswp_array');

    if(isset($all_post_meta['saswp_taxi_service_schema_additional_type_'.$schema_id][0])){
        $input1['additionalType']         = $all_post_meta['saswp_taxi_service_schema_additional_type_'.$schema_id][0];
    }
    if(isset($all_post_meta['saswp_taxi_service_schema_service_output_'.$schema_id][0])){
        $input1['serviceOutput']         = $all_post_meta['saswp_taxi_service_schema_service_output_'.$schema_id][0];
    }

    if(isset($all_post_meta['saswp_taxi_service_schema_provider_type_'.$schema_id][0])){

         $input1['provider']['@type']                      = $all_post_meta['saswp_taxi_service_schema_provider_type_'.$schema_id][0];
         $input1['provider']['name']                       = $all_post_meta['saswp_taxi_service_schema_provider_name_'.$schema_id][0];                                                 
         $input1['provider']['address']['@type']           = 'PostalAddress';
         $input1['provider']['address']['addressLocality'] = saswp_remove_warnings($all_post_meta, 'saswp_taxi_service_schema_locality_'.$schema_id, 'saswp_array');
         $input1['provider']['address']['postalCode']      = saswp_remove_warnings($all_post_meta, 'saswp_taxi_service_schema_postal_code_'.$schema_id, 'saswp_array');
         $input1['provider']['address']['telephone']       = saswp_remove_warnings($all_post_meta, 'saswp_taxi_service_schema_telephone_'.$schema_id, 'saswp_array');

         if(isset($all_post_meta['saswp_taxi_service_schema_price_range_'.$schema_id][0])){
            $input1['provider']['priceRange']                 = $all_post_meta['saswp_taxi_service_schema_price_range_'.$schema_id][0];
         }

    }

    if( isset($all_post_meta['saswp_taxi_service_schema_image_'.$schema_id][0]) && !empty($all_post_meta['saswp_taxi_service_schema_image_'.$schema_id][0]) ){
        $input1['image']                      = $all_post_meta['saswp_taxi_service_schema_image_'.$schema_id][0];             
    }

    $input1['description'] = saswp_remove_warnings($all_post_meta, 'saswp_taxi_service_schema_description_'.$schema_id, 'saswp_array');

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
                'name'            => saswp_remove_warnings($all_post_meta, 'saswp_taxi_service_schema_name_'.$schema_id, 'saswp_array'),
                'itemListElement' => $serviceOffer
           );

    return $input1;

}

function saswp_service_schema_markup($schema_id, $schema_post_id, $all_post_meta){
    
        $input1 = array();

        $checkIdPro = ((isset($all_post_meta['saswp_service_schema_id_'.$schema_id][0]) && $all_post_meta['saswp_service_schema_id_'.$schema_id][0] !='') ? get_permalink().'#'.$all_post_meta['saswp_service_schema_id_'.$schema_id][0] : '');

        $area_served_str = saswp_remove_warnings($all_post_meta, 'saswp_service_schema_area_served_'.$schema_id, 'saswp_array'); 
        $area_served_arr = explode(',', $area_served_str);

        $service_offer_str = saswp_remove_warnings($all_post_meta, 'saswp_service_schema_service_offer_'.$schema_id, 'saswp_array');
        $service_offer_arr = explode(',', $service_offer_str);

        $input1['@context']    = saswp_context_url();
        $input1['@type']       = 'Service';
        if($checkIdPro){
            $input1['@id']     = $checkIdPro;  
        }
        $input1['name']        = saswp_remove_warnings($all_post_meta, 'saswp_service_schema_name_'.$schema_id, 'saswp_array');
        $input1['serviceType'] = saswp_remove_warnings($all_post_meta, 'saswp_service_schema_type_'.$schema_id, 'saswp_array');

        if(isset($all_post_meta['saswp_service_schema_additional_type_'.$schema_id][0])){
            $input1['additionalType']         = $all_post_meta['saswp_service_schema_additional_type_'.$schema_id][0];
        }
        if(isset($all_post_meta['saswp_service_schema_service_output_'.$schema_id][0])){
            $input1['serviceOutput']         = $all_post_meta['saswp_service_schema_service_output_'.$schema_id][0];
        }

        if(isset($all_post_meta['saswp_service_schema_provider_mobility_'.$schema_id][0])){
            $input1['providerMobility']         = $all_post_meta['saswp_service_schema_provider_mobility_'.$schema_id][0];
        }

        if(isset($all_post_meta['saswp_service_schema_provider_type_'.$schema_id][0])){

             $input1['provider']['@type']                      = $all_post_meta['saswp_service_schema_provider_type_'.$schema_id][0];
             $input1['provider']['name']                       = $all_post_meta['saswp_service_schema_provider_name_'.$schema_id][0];                                                 
             $input1['provider']['address']['@type']           = 'PostalAddress';
             $input1['provider']['address']['addressLocality'] = saswp_remove_warnings($all_post_meta, 'saswp_service_schema_locality_'.$schema_id, 'saswp_array');
             $input1['provider']['address']['postalCode']      = saswp_remove_warnings($all_post_meta, 'saswp_service_schema_postal_code_'.$schema_id, 'saswp_array');
             $input1['provider']['address']['telephone']       = saswp_remove_warnings($all_post_meta, 'saswp_service_schema_telephone_'.$schema_id, 'saswp_array');

             if(isset($all_post_meta['saswp_service_schema_price_range_'.$schema_id][0])){
                $input1['provider']['priceRange']                 = $all_post_meta['saswp_service_schema_price_range_'.$schema_id][0];
             }

             if( isset($all_post_meta['saswp_service_schema_image_'.$schema_id][0]) && !empty($all_post_meta['saswp_service_schema_image_'.$schema_id][0]) ){
                $input1['provider']['image']                      = $all_post_meta['saswp_service_schema_image_'.$schema_id][0];             
             }

             if(isset($all_post_meta['saswp_service_schema_enable_rating_'.$schema_id]) && isset($all_post_meta['saswp_service_schema_rating_value_'.$schema_id]) && isset($all_post_meta['saswp_service_schema_rating_count_'.$schema_id])){
                $input1['provider']['aggregateRating']['@type']         = 'aggregateRating';
                $input1['provider']['aggregateRating']['ratingValue']   = $all_post_meta['saswp_service_schema_rating_value_'.$schema_id][0];
                $input1['provider']['aggregateRating']['ratingCount']   = $all_post_meta['saswp_service_schema_rating_count_'.$schema_id][0];                                
            }

        }

        if( isset($all_post_meta['saswp_service_schema_image_'.$schema_id][0]) && !empty($all_post_meta['saswp_service_schema_image_'.$schema_id][0]) ){
            $input1['image']                      = $all_post_meta['saswp_service_schema_image_'.$schema_id][0];             
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
    $input1['@id']                          = get_permalink().'#review';                                                           
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

    if(isset($all_post_meta['saswp_review_description_'.$schema_id][0])){                                                                     
        $input1['description']              = $all_post_meta['saswp_review_description_'.$schema_id][0];
    }

    if(isset($all_post_meta['saswp_review_body_'.$schema_id][0])){
        $input1['reviewBody']              = $all_post_meta['saswp_review_body_'.$schema_id][0];
    }

    if(isset($all_post_meta['saswp_review_author_'.$schema_id])){

       $review_author = $all_post_meta['saswp_review_author_'.$schema_id][0];  

    }

    if($review_author){

    $input1['author']['@type']              = 'Person';    
    
    if(isset($all_post_meta['saswp_review_author_type'.$schema_id][0])){
        $input1['author']['@type']              = $all_post_meta['saswp_review_author_type'.$schema_id][0];    
    }
    
    $input1['author']['name']               = esc_attr($review_author);

    if(isset($all_post_meta['saswp_review_author_url_'.$schema_id])){

     $input1['author']['sameAs']            = esc_url($all_post_meta['saswp_review_author_url_'.$schema_id][0]);   

    }
    
    }

    if(saswp_remove_warnings($all_post_meta, 'saswp_review_enable_rating_'.$schema_id, 'saswp_array') == 1){   

           $input1['reviewRating'] = array(
                             "@type"        => "Rating",
                             "ratingValue"  => saswp_remove_warnings($all_post_meta, 'saswp_review_rating_'.$schema_id, 'saswp_array'),
                             "bestRating"   => saswp_remove_warnings($all_post_meta, 'saswp_review_review_count_'.$schema_id, 'saswp_array'),
                             "worstRating"  => saswp_remove_warnings($all_post_meta, 'saswp_review_worst_count_'.$schema_id, 'saswp_array')
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


function saswp_vacation_rental_schema_markup($schema_id, $schema_post_id, $all_post_meta)
{
    $input1 = array();
    $input1['@context']              = saswp_context_url();
    $input1['@type']                 = 'VacationRental';
    $input1['@id']                   = saswp_get_permalink().'#VacationRental';
    if(isset($all_post_meta['saswp_vr_schema_additional_type_'.$schema_id]) && isset($all_post_meta['saswp_vr_schema_additional_type_'.$schema_id][0])){
        $input1['additionalType'] = saswp_remove_warnings($all_post_meta, 'saswp_vr_schema_additional_type_'.$schema_id, 'saswp_array');
    }
    if(isset($all_post_meta['saswp_vr_schema_brand_'.$schema_id]) && isset($all_post_meta['saswp_vr_schema_brand_'.$schema_id][0])){
        $input1['brand'] = saswp_remove_warnings($all_post_meta, 'saswp_vr_schema_brand_'.$schema_id, 'saswp_array');
    }
    $input1['containsPlace']['@type'] = 'Accommodation';
    if(isset($all_post_meta['saswp_vr_schema_cpat_'.$schema_id]) && isset($all_post_meta['saswp_vr_schema_cpat_'.$schema_id][0])){
        $input1['containsPlace']['additionalType'] = saswp_remove_warnings($all_post_meta, 'saswp_vr_schema_cpat_'.$schema_id, 'saswp_array');
    }

    $bed_details  = get_post_meta($schema_post_id, 'vacation_rental_bed_details_'.$schema_id, true);

    if(!empty($bed_details) && is_array($bed_details) && count($bed_details) > 0){
        $bcnt = 0;
        foreach ($bed_details as $bd_key => $bd_value) {
            if(!empty($bd_value) && is_array($bd_value)){
                $input1['containsPlace']['bed'][$bcnt]['@type'] = 'BedDetails';
                $input1['containsPlace']['bed'][$bcnt]['numberOfBeds'] = isset($bd_value['saswp_vr_bed_details_nob'])?intval($bd_value['saswp_vr_bed_details_nob']):'';
                $input1['containsPlace']['bed'][$bcnt]['typeOfBed'] = isset($bd_value['saswp_vr_bed_details_tob'])?sanitize_text_field($bd_value['saswp_vr_bed_details_tob']):'';
                $bcnt++;    
            }
        }
    }

    if(isset($all_post_meta['saswp_vr_schema_occupancy_'.$schema_id]) && isset($all_post_meta['saswp_vr_schema_occupancy_'.$schema_id][0])){
        $input1['containsPlace']['occupancy']['@type'] = 'QuantitativeValue';
        $input1['containsPlace']['occupancy']['value'] = saswp_remove_warnings($all_post_meta, 'saswp_vr_schema_occupancy_'.$schema_id, 'saswp_array');
    }

    $amenity_feature  = get_post_meta($schema_post_id, 'vacation_rental_amenity_feature_'.$schema_id, true);

    if(!empty($amenity_feature) && is_array($amenity_feature) && count($amenity_feature) > 0){
        $afcnt = 0;
        foreach ($amenity_feature as $af_key => $af_value) {
            if(!empty($af_value) && is_array($af_value)){
                $input1['containsPlace']['amenityFeature'][$afcnt]['@type'] = 'LocationFeatureSpecification';
                $input1['containsPlace']['amenityFeature'][$afcnt]['name'] = isset($af_value['saswp_vr_amenity_feature_name'])?sanitize_text_field($af_value['saswp_vr_amenity_feature_name']):'';
                $input1['containsPlace']['amenityFeature'][$afcnt]['value'] = isset($af_value['saswp_vr_amenity_feature_value'])?sanitize_text_field($af_value['saswp_vr_amenity_feature_value']):'';
                $afcnt++;    
            }
        }
    }

    if(isset($all_post_meta['saswp_vr_schema_floor_value_'.$schema_id]) || isset($all_post_meta['saswp_vr_schema_floor_value_'.$schema_id])){
        $input1['containsPlace']['floorSize']['@type'] = 'QuantitativeValue';   
        $input1['containsPlace']['floorSize']['value'] = isset($all_post_meta['saswp_vr_schema_floor_value_'.$schema_id])?saswp_remove_warnings($all_post_meta, 'saswp_vr_schema_floor_value_'.$schema_id, 'saswp_array'):'';   
        $input1['containsPlace']['floorSize']['unitCode'] = isset($all_post_meta['saswp_vr_schema_floor_uc_'.$schema_id])?saswp_remove_warnings($all_post_meta, 'saswp_vr_schema_floor_uc_'.$schema_id, 'saswp_array'):'';
    }

    if(isset($all_post_meta['saswp_vr_schema_total_bathrooms_'.$schema_id]) && isset($all_post_meta['saswp_vr_schema_total_bathrooms_'.$schema_id][0])){
        $input1['containsPlace']['numberOfBathroomsTotal'] = saswp_remove_warnings($all_post_meta, 'saswp_vr_schema_total_bathrooms_'.$schema_id, 'saswp_array');
    }
    if(isset($all_post_meta['saswp_vr_schema_total_bedrooms_'.$schema_id]) && isset($all_post_meta['saswp_vr_schema_total_bedrooms_'.$schema_id][0])){
        $input1['containsPlace']['numberOfBedrooms'] = saswp_remove_warnings($all_post_meta, 'saswp_vr_schema_total_bedrooms_'.$schema_id, 'saswp_array');
    }
    if(isset($all_post_meta['saswp_vr_schema_total_rooms_'.$schema_id]) && isset($all_post_meta['saswp_vr_schema_total_rooms_'.$schema_id][0])){
        $input1['containsPlace']['numberOfRooms'] = saswp_remove_warnings($all_post_meta, 'saswp_vr_schema_total_rooms_'.$schema_id, 'saswp_array');
    }
    if(isset($all_post_meta['saswp_vr_schema_identifier_'.$schema_id]) && isset($all_post_meta['saswp_vr_schema_identifier_'.$schema_id][0])){
        $input1['identifier'] = saswp_remove_warnings($all_post_meta, 'saswp_vr_schema_identifier_'.$schema_id, 'saswp_array');
    }
    if(isset($all_post_meta['saswp_vr_schema_latitude_'.$schema_id]) && isset($all_post_meta['saswp_vr_schema_latitude_'.$schema_id][0])){
        $input1['latitude'] = saswp_remove_warnings($all_post_meta, 'saswp_vr_schema_latitude_'.$schema_id, 'saswp_array');
    }
    if(isset($all_post_meta['saswp_vr_schema_longitude_'.$schema_id]) && isset($all_post_meta['saswp_vr_schema_longitude_'.$schema_id][0])){
        $input1['longitude'] = saswp_remove_warnings($all_post_meta, 'saswp_vr_schema_longitude_'.$schema_id, 'saswp_array');
    }
    if(isset($all_post_meta['saswp_vr_schema_name_'.$schema_id]) && isset($all_post_meta['saswp_vr_schema_name_'.$schema_id][0])){
        $input1['name'] = saswp_remove_warnings($all_post_meta, 'saswp_vr_schema_name_'.$schema_id, 'saswp_array');
    }
    if(isset($all_post_meta['saswp_vr_schema_country_'.$schema_id]) && isset($all_post_meta['saswp_vr_schema_country_'.$schema_id][0])){
        $input1['address']['addressCountry'] = saswp_remove_warnings($all_post_meta, 'saswp_vr_schema_country_'.$schema_id, 'saswp_array');
    }
    if(isset($all_post_meta['saswp_vr_schema_locality_'.$schema_id]) && isset($all_post_meta['saswp_vr_schema_locality_'.$schema_id][0])){
        $input1['address']['addressLocality'] = saswp_remove_warnings($all_post_meta, 'saswp_vr_schema_locality_'.$schema_id, 'saswp_array');
    }
    if(isset($all_post_meta['saswp_vr_schema_region_'.$schema_id]) && isset($all_post_meta['saswp_vr_schema_region_'.$schema_id][0])){
        $input1['address']['addressRegion'] = saswp_remove_warnings($all_post_meta, 'saswp_vr_schema_region_'.$schema_id, 'saswp_array');
    }
    if(isset($all_post_meta['saswp_vr_schema_p_code_'.$schema_id]) && isset($all_post_meta['saswp_vr_schema_p_code_'.$schema_id][0])){
        $input1['address']['postalCode'] = saswp_remove_warnings($all_post_meta, 'saswp_vr_schema_p_code_'.$schema_id, 'saswp_array');
    }
    if(isset($all_post_meta['saswp_vr_schema_s_address_'.$schema_id]) && isset($all_post_meta['saswp_vr_schema_s_address_'.$schema_id][0])){
        $input1['address']['streetAddress'] = saswp_remove_warnings($all_post_meta, 'saswp_vr_schema_s_address_'.$schema_id, 'saswp_array');
    }
    if(isset($all_post_meta['saswp_vr_schema_rating_value_'.$schema_id]) && isset($all_post_meta['saswp_vr_schema_rating_value_'.$schema_id][0])){
        $input1['aggregateRating']['ratingValue'] = saswp_remove_warnings($all_post_meta, 'saswp_vr_schema_rating_value_'.$schema_id, 'saswp_array');
    }
    if(isset($all_post_meta['saswp_vr_schema_rating_count_'.$schema_id]) && isset($all_post_meta['saswp_vr_schema_rating_count_'.$schema_id][0])){
        $input1['aggregateRating']['ratingCount'] = saswp_remove_warnings($all_post_meta, 'saswp_vr_schema_rating_count_'.$schema_id, 'saswp_array');
    }
    if(isset($all_post_meta['saswp_vr_schema_review_count_'.$schema_id]) && isset($all_post_meta['saswp_vr_schema_review_count_'.$schema_id][0])){
        $input1['aggregateRating']['reviewCount'] = saswp_remove_warnings($all_post_meta, 'saswp_vr_schema_review_count_'.$schema_id, 'saswp_array');
    }
    if(isset($all_post_meta['saswp_vr_schema_best_rating_'.$schema_id]) && isset($all_post_meta['saswp_vr_schema_best_rating_'.$schema_id][0])){
        $input1['aggregateRating']['bestRating'] = saswp_remove_warnings($all_post_meta, 'saswp_vr_schema_best_rating_'.$schema_id, 'saswp_array');
    }

    $property_images  = get_post_meta($schema_post_id, 'vacation_rental_property_images_'.$schema_id, true);

    if(!empty($property_images) && is_array($property_images) && count($property_images) > 0){
        $picnt = 0;
        foreach ($property_images as $pi_key => $pi_value) {
            if(!empty($pi_value) && is_array($pi_value)){
                if(isset($pi_value['saswp_vr_property_image_id']) && !empty($pi_value['saswp_vr_property_image_id'])){
                    $image_url = wp_get_attachment_image_url($pi_value['saswp_vr_property_image_id']);
                    if(!empty($image_url) && is_string($image_url) ){
                        $input1['image'][$picnt] = $image_url;
                        $picnt++;
                    }
                }
            }
        }
    }    

    if(isset($all_post_meta['saswp_vr_schema_checkin_time_'.$schema_id]) && isset($all_post_meta['saswp_vr_schema_checkin_time_'.$schema_id][0])){
        $input1['checkinTime'] = saswp_remove_warnings($all_post_meta, 'saswp_vr_schema_checkin_time_'.$schema_id, 'saswp_array');
    }
    if(isset($all_post_meta['saswp_vr_schema_checkout_time_'.$schema_id]) && isset($all_post_meta['saswp_vr_schema_checkout_time_'.$schema_id][0])){
        $input1['checkoutTime'] = saswp_remove_warnings($all_post_meta, 'saswp_vr_schema_checkout_time_'.$schema_id, 'saswp_array');
    }
    if(isset($all_post_meta['saswp_vr_schema_description_'.$schema_id]) && isset($all_post_meta['saswp_vr_schema_description_'.$schema_id][0])){
        $input1['description'] = saswp_remove_warnings($all_post_meta, 'saswp_vr_schema_description_'.$schema_id, 'saswp_array');
    }
    if(isset($all_post_meta['saswp_vr_schema_knows_language_'.$schema_id]) && isset($all_post_meta['saswp_vr_schema_knows_language_'.$schema_id][0])){
        if(!empty($all_post_meta['saswp_vr_schema_knows_language_'.$schema_id][0])){
            $explode_lang = explode(',', $all_post_meta['saswp_vr_schema_knows_language_'.$schema_id][0]);
            if(!empty($explode_lang) && is_array($explode_lang)){
                foreach ($explode_lang as $el_key => $el_value) {
                    if(!empty($el_value)){
                        $input1['knowsLanguage'] = $el_value;
                    }
                }
            }
        }
    }

    $review_rating  = get_post_meta($schema_post_id, 'vacation_rental_review_rating_'.$schema_id, true);

    if(!empty($review_rating) && is_array($review_rating) && count($review_rating) > 0){
        $rrcnt = 0;
        foreach ($review_rating as $rr_key => $rr_value) {
            if(!empty($rr_value) && is_array($rr_value)){
                $input1['review'][$rrcnt]['@type'] = 'Review';
                $input1['review'][$rrcnt]['reviewRating']['@type'] = 'Rating';
                $input1['review'][$rrcnt]['reviewRating']['ratingValue'] = isset($rr_value['saswp_vr_review_rating_value'])?intval($rr_value['saswp_vr_review_rating_value']):'';
                $input1['review'][$rrcnt]['reviewRating']['bestRating'] = isset($rr_value['saswp_vr_review_rating_best_value'])?intval($rr_value['saswp_vr_review_rating_best_value']):'';
                $input1['review'][$rrcnt]['author']['@type'] = isset($rr_value['saswp_vr_review_rating_author_type'])?sanitize_text_field($rr_value['saswp_vr_review_rating_author_type']):'';
                $input1['review'][$rrcnt]['author']['name'] = isset($rr_value['saswp_vr_review_rating_author_name'])?sanitize_text_field($rr_value['saswp_vr_review_rating_author_name']):'';
                $input1['review'][$rrcnt]['datePublished'] = isset($rr_value['saswp_vr_review_rating_date_pub'])?date('Y-m-d', strtotime($rr_value['saswp_vr_review_rating_date_pub'])):'';
                $input1['review'][$rrcnt]['contentReferenceTime'] = isset($rr_value['saswp_vr_review_rating_cr_time'])?date('Y-m-d', strtotime($rr_value['saswp_vr_review_rating_cr_time'])):'';
                $rrcnt++;    
            }
        }
    }

    return $input1;
}

/**
 * Schema markup function for Learning Resource Schema
 * @since 1.28
 * @param   $schema_id  Integer
 * @param   $schema_post_id  Integer
 * @param   $all_post_meta  Array
 * @return  $input1  Array
 * */
function saswp_learning_resource_schema_markup($schema_id, $schema_post_id, $all_post_meta)
{
    $input1['@context']              = saswp_context_url();
    $input1['@type']                 = 'LearningResource';
    $input1['@id']                   = saswp_get_permalink().'#LearningResource';                                
    $input1['url']                   = saswp_get_permalink();  

    $thumbnail_id = get_post_thumbnail_id(get_the_ID());
    $thumbnail_url = wp_get_attachment_url($thumbnail_id);
    if(!empty($thumbnail_url) && is_string($thumbnail_url)){
        $image_details                   = saswp_get_image_by_url($thumbnail_url);
        if(!empty($image_details) && is_array($image_details)){
            $input1['image']         = $image_details;
        }
    }    

    $thumbnail_details   = wp_get_attachment_image_src($thumbnail_id, 'thumbnail');
    if(is_array($thumbnail_details) && isset($thumbnail_details[0])){
        $image_details                   = saswp_get_image_by_url($thumbnail_details[0]);
        if(!empty($image_details) && is_array($image_details)){
            $input1['thumbnail']     = $image_details;
        } 
        $input1['thumbnailUrl']  = saswp_remove_warnings($thumbnail_details, 0, 'saswp_string');
    }     
    if(isset($all_post_meta['saswp_lr_name_'.$schema_id]) && isset($all_post_meta['saswp_lr_name_'.$schema_id][0])){
        $input1['name'] = saswp_remove_warnings($all_post_meta, 'saswp_lr_name_'.$schema_id, 'saswp_array');
    }
    if(isset($all_post_meta['saswp_lr_description_'.$schema_id]) && isset($all_post_meta['saswp_lr_description_'.$schema_id][0])){
        $input1['description'] = saswp_remove_warnings($all_post_meta, 'saswp_lr_description_'.$schema_id, 'saswp_array');
    } 
    if(isset($all_post_meta['saswp_lr_keywords_'.$schema_id]) && isset($all_post_meta['saswp_lr_keywords_'.$schema_id][0])){
        $input1['keywords'] = saswp_remove_warnings($all_post_meta, 'saswp_lr_keywords_'.$schema_id, 'saswp_array');
    } 
    if(isset($all_post_meta['saswp_lr_lrt_'.$schema_id]) && isset($all_post_meta['saswp_lr_lrt_'.$schema_id][0])){
        $input1['learningResourceType'] = saswp_remove_warnings($all_post_meta, 'saswp_lr_lrt_'.$schema_id, 'saswp_array');
    }
    if(isset($all_post_meta['saswp_lr_lrt_'.$schema_id]) && isset($all_post_meta['saswp_lr_lrt_'.$schema_id][0])){
        $input1['learningResourceType'] = saswp_remove_warnings($all_post_meta, 'saswp_lr_lrt_'.$schema_id, 'saswp_array');
    }                
    $input1['author'] = saswp_get_author_details();
    if(isset($all_post_meta['saswp_lr_inlanguage_'.$schema_id]) && isset($all_post_meta['saswp_lr_inlanguage_'.$schema_id][0])){
        if(!empty($all_post_meta['saswp_lr_inlanguage_'.$schema_id][0]) && is_string($all_post_meta['saswp_lr_inlanguage_'.$schema_id][0])){
            $explode_lang = explode(',', $all_post_meta['saswp_lr_inlanguage_'.$schema_id][0]);
            if(!empty($explode_lang) && is_array($explode_lang)){
                foreach ($explode_lang as $el_key => $el_value) {
                    $input1['inLanguage'][] = $el_value;
                }
            }
        }
    } 
    $input1['dateCreated'] = date('Y-m-d', strtotime(get_the_date()));  
    if(isset($all_post_meta['saswp_lr_date_created_'.$schema_id]) && isset($all_post_meta['saswp_lr_date_created__'.$schema_id][0])){
        $input1['dateCreated'] = saswp_remove_warnings($all_post_meta, 'saswp_lr_date_created_'.$schema_id, 'saswp_array');
    } 
    $input1['dateModified'] = date('Y-m-d', strtotime(get_the_modified_date()));
    if(isset($all_post_meta['saswp_lr_date_modified_'.$schema_id]) && isset($all_post_meta['saswp_lr_date_modified_'.$schema_id][0])){
        $input1['dateModified'] = saswp_remove_warnings($all_post_meta, 'saswp_lr_date_modified_'.$schema_id, 'saswp_array');
    }
    if(isset($all_post_meta['saswp_lr_tar_'.$schema_id]) && isset($all_post_meta['saswp_lr_tar_'.$schema_id][0])){
        $input1['typicalAgeRange'] = saswp_remove_warnings($all_post_meta, 'saswp_lr_tar_'.$schema_id, 'saswp_array');
    } 
    if(isset($all_post_meta['saswp_lr_education_level_name_'.$schema_id]) || isset($all_post_meta['saswp_lr_education_level_url'.$schema_id]) || isset($all_post_meta['saswp_lr_education_level_term_set'.$schema_id])){
        $input1['educationalLevel']['@type'] = 'DefinedTerm';
        if(isset($all_post_meta['saswp_lr_education_level_name_'.$schema_id])){
            $input1['educationalLevel']['name'] = saswp_remove_warnings($all_post_meta, 'saswp_lr_education_level_name_'.$schema_id, 'saswp_array');
        }
        if(isset($all_post_meta['saswp_lr_education_level_url_'.$schema_id])){
            $input1['educationalLevel']['url'] = saswp_remove_warnings($all_post_meta, 'saswp_lr_education_level_url_'.$schema_id, 'saswp_array');
        }
        if(isset($all_post_meta['saswp_lr_education_level_term_set_'.$schema_id])){
            $input1['educationalLevel']['inDefinedTermSet'] = saswp_remove_warnings($all_post_meta, 'saswp_lr_education_level_term_set_'.$schema_id, 'saswp_array');
        }
    }

    $education_alignment  = get_post_meta($schema_post_id, 'learning_resource_educational_alignment_'.$schema_id, true);
    if(!empty($education_alignment) && is_array($education_alignment) && count($education_alignment) > 0){
        $eacnt = 0;
        foreach ($education_alignment as $ea_key => $ea_value) {
            if(!empty($ea_value) && is_array($ea_value)){
                $input1['educationalAlignment'][$eacnt]['@type'] = 'AlignmentObject';    
                $input1['educationalAlignment'][$eacnt]['alignmentType'] = isset($ea_value['saswp_lr_eaat'])?sanitize_text_field($ea_value['saswp_lr_eaat']):'';    
                $input1['educationalAlignment'][$eacnt]['educationalFramework'] = isset($ea_value['saswp_lr_eaef'])?sanitize_text_field($ea_value['saswp_lr_eaef']):'';    
                $input1['educationalAlignment'][$eacnt]['targetName'] = isset($ea_value['saswp_lr_eatn'])?sanitize_text_field($ea_value['saswp_lr_eatn']):'';    
                $input1['educationalAlignment'][$eacnt]['targetUrl'] = isset($ea_value['saswp_lr_eatu'])?sanitize_text_field($ea_value['saswp_lr_eatu']):'';    
            }
        }
    }
    
    if(isset($all_post_meta['saswp_lr_time_required_'.$schema_id]) && isset($all_post_meta['saswp_lr_time_required_'.$schema_id][0])){
        $input1['timeRequired'] = saswp_remove_warnings($all_post_meta, 'saswp_lr_time_required_'.$schema_id, 'saswp_array');
    }
    if(isset($all_post_meta['saswp_lr_license_'.$schema_id]) && isset($all_post_meta['saswp_lr_license_'.$schema_id][0])){
        $input1['license'] = saswp_remove_warnings($all_post_meta, 'saswp_lr_license_'.$schema_id, 'saswp_array');
    }
    if(isset($all_post_meta['saswp_lr_time_iaff_'.$schema_id]) && isset($all_post_meta['saswp_lr_time_iaff_'.$schema_id][0])){
        $input1['isAccessibleForFree'] = saswp_remove_warnings($all_post_meta, 'saswp_lr_time_iaff_'.$schema_id, 'saswp_array');
    }

    return $input1;
}