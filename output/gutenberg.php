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


function saswp_gutenberg_recipe_schema() {
                        
    global $post, $sd_data;

    $input1 = array();

    if( (isset($sd_data['saswp-wpzoom']) && $sd_data['saswp-wpzoom'] == 1) && class_exists('WPZOOM_Structured_Data_Helpers') && class_exists('WPZOOM_Helpers') ){
    $attributes    = array();
    $recipe_block = saswp_get_gutenberg_block_data('wpzoom-recipe-card/block-recipe-card');     

    if ( isset( $recipe_block['attrs']) ) {
        $attributes = $recipe_block['attrs'];
    }    
    
    $service_object          = new SASWP_Output_Service();   
    $structured_data_helpers = new WPZOOM_Structured_Data_Helpers();
    $helpers                 = new WPZOOM_Helpers();
    $feature_image           = $service_object->saswp_get_featured_image();                  
                                       
    $input1['@context']              = saswp_context_url();
    $input1['@type']                 = 'Recipe';
    $input1['@id']                   = saswp_get_permalink().'#Recipe';
    $input1['name']                  = isset($attributes['recipeTitle']) ? $attributes['recipeTitle'] : saswp_get_the_title();                
    $input1['description']           = isset($attributes['summary']) ? $attributes['summary'] : saswp_get_the_excerpt();                   
    $input1['datePublished']         = get_the_date("c");
    $input1['dateModified']          = get_the_modified_date("c");
    $input1['keywords']              = isset($attributes['keywords']) ? $attributes['keywords'] :  saswp_get_the_tags();    
    $input1['author']                = saswp_get_author_details();
    

    if ( isset( $attributes['cuisine']) ) {

        $input1['recipeCuisine']    = $attributes['cuisine'];   

    }
    if ( isset( $attributes['course']) ) {

        $input1['recipeCategory']    = $attributes['course'];   

    }

    if ( ! empty( $attributes['details'] ) && is_array( $attributes['details'] ) ) {

        $details = array_filter( $attributes['details'], 'is_array' );
        
        foreach ( $details as $key => $detail ) {

            if ( $key === 0 ) {
                if ( ! empty( $detail[ 'value' ] ) ) {
                    if ( !is_array( $detail['value'] ) ) {
                        $yield = array(
                             $detail['value']
                         );

                        if ( isset( $detail['unit'] ) && ! empty( $detail['unit'] ) ) {
                            $yield[] = $detail['value'] .' '. $detail['unit'];
                        }
                    }
                    elseif ( isset( $detail['jsonValue'] ) ) {
                        $yield = array(
                             $detail['jsonValue']
                         );

                        if ( isset( $detail['unit'] ) && ! empty( $detail['unit'] ) ) {
                            $yield[] = $detail['value'] .' '. $detail['unit'];
                        }
                    }

                    if ( isset( $yield ) ) {
                         $input1['recipeYield'] = $yield;
                     }
                }
            }elseif ( $key === 3 ) {
                if ( ! empty( $detail[ 'value' ] ) ) {
                    if ( !is_array( $detail['value'] ) ) {
                        $input1['nutrition']['calories'] = $detail['value'] .' cal';
                    }
                    elseif ( isset( $detail['jsonValue'] ) ) {
                        $input1['nutrition']['calories'] = $detail['jsonValue'] .' cal';
                    }
                }
            }elseif ( $key === 1 ) {
                if ( ! empty( $detail[ 'value' ] ) ) {
                    if ( !is_array( $detail['value'] ) ) {
                        $prepTime = $structured_data_helpers->get_number_from_string( $detail['value'] );
                        $input1['prepTime'] = $structured_data_helpers->get_period_time( $detail['value'] );
                    }
                    elseif ( isset( $detail['jsonValue'] ) ) {
                        $prepTime = $structured_data_helpers->get_number_from_string( $detail['jsonValue'] );
                        $input1['prepTime'] = $structured_data_helpers->get_period_time( $detail['jsonValue'] );
                    }
                }
            }elseif ( $key === 2 ) {
                if ( ! empty( $detail[ 'value' ] )) {
                    if ( !is_array( $detail['value'] ) ) {
                        $cookTime = $structured_data_helpers->get_number_from_string( $detail['value'] );
                        $input1['cookTime'] = $structured_data_helpers->get_period_time( $detail['value'] );
                    }
                    elseif ( isset( $detail['jsonValue'] ) ) {
                        $cookTime = $structured_data_helpers->get_number_from_string( $detail['jsonValue'] );
                        $input1['cookTime'] = $structured_data_helpers->get_period_time( $detail['jsonValue'] );
                    }
                }
            }
            elseif ( $key === 8 ) {
                if ( ! empty( $detail[ 'value' ] )) {
                    if ( !is_array( $detail['value'] ) ) {
                        $input1['totalTime'] = $structured_data_helpers->get_period_time( $detail['value'] );
                    }
                    elseif ( isset( $detail['jsonValue'] ) ) {
                        $input1['totalTime'] = $structured_data_helpers->get_period_time( $detail['jsonValue'] );
                    }
                }
            }

        }

        if ( empty( $input1['totalTime'] ) ) {
            if ( isset( $prepTime, $cookTime ) && ( $prepTime + $cookTime ) > 0 ) {
                $input1['totalTime'] = $structured_data_helpers->get_period_time( $prepTime + $cookTime );
            }
        }

    }

    if ( ! empty( $attributes['ingredients'] ) && is_array( $attributes['ingredients'] ) ) {
        $ingredients = array_filter( $attributes['ingredients'], 'is_array' );
        foreach ( $ingredients as $ingredient ) {
            $isGroup = isset( $ingredient['isGroup'] ) ? $ingredient['isGroup'] : false;

            if ( ! $isGroup ) {
                $input1['recipeIngredient'][] = $structured_data_helpers->get_ingredient_json_ld( $ingredient );
            }

        }
    }

    if ( ! empty( $attributes['steps'] ) && is_array( $attributes['steps'] ) ) {
        $steps = array_filter( $attributes['steps'], 'is_array' );
        $groups_section = array();
        $instructions = array();

        foreach ( $steps as $key => $step ) {
            $isGroup = isset( $step['isGroup'] ) ? $step['isGroup'] : false;
            $parent_permalink = get_the_permalink();
            
            if ( $isGroup ) {
                $groups_section[ $key ] = array(
                    '@type' => 'HowToSection',
                    'name' => '',
                    'itemListElement' => array(),
                );
                if ( ! empty( $step['jsonText'] ) ) {
                    $groups_section[ $key ]['name'] = $step['jsonText'];
                } else {
                    $groups_section[ $key ]['name'] = $structured_data_helpers->step_text_to_JSON( $step['text'] );
                }
            }

            if ( count( $groups_section ) > 0 ) {
                end( $groups_section );
                $last_key = key( $groups_section );

                if ( ! $isGroup && $key > $last_key ) {
                    $groups_section[ $last_key ]['itemListElement'][] = $structured_data_helpers->get_step_json_ld( $step, $parent_permalink );
                }
            } else {
                $instructions[] = $structured_data_helpers->get_step_json_ld( $step, $parent_permalink );
            }
        }

        $groups_section = array_merge( $instructions, $groups_section );
        $input1['recipeInstructions'] = $groups_section;
    }

    if ( isset( $attributes['image']['id']) ) {

        $image_details   = saswp_get_image_by_id($attributes['image']['id']); 

        if($image_details){
            $input1['image'] = $image_details;
        }else{
            if ( ! empty( $feature_image) ) {
                    
                $input1 = array_merge($input1, $feature_image);   
                        
            }
        }

    }        
    
    //video json

    if ( isset( $attributes['video'] ) && ! empty( $attributes['video'] ) && isset( $attributes['hasVideo'] ) && $attributes['hasVideo'] ) {
        $video = $attributes['video'];
        $video_id = isset( $video['id'] ) ? $video['id'] : 0;
        $video_type = isset( $video['type'] ) ? $video['type'] : '';

        if ( 'self-hosted' === $video_type ) {
             $video_attachment = get_post( $video_id );

             if ( $video_attachment ) {
                 $video_data = wp_get_attachment_metadata( $video_id );
                 $video_url = wp_get_attachment_url( $video_id );

                 $image_id = get_post_thumbnail_id( $video_id );
                 $thumb = wp_get_attachment_image_src( $image_id, 'full' );
                 $thumbnail_url = $thumb && isset( $thumb[0] ) ? $thumb[0] : '';

                 $input1['video'] = array_merge(
                     $input1['video'], array(
                         'name' => $video_attachment->post_title,
                         'description' => $video_attachment->post_content,
                         'thumbnailUrl' => $thumbnail_url,
                         'contentUrl' => $video_url,
                         'uploadDate' => gmdate( 'c', strtotime( $video_attachment->post_date ) ),
                         'duration' => 'PT' . $video_data['length'] . 'S',
                     )
                 );
             }
         }

        if ( isset( $video['title'] ) && ! empty( $video['title'] ) ) {
            $input1['video']['name'] = esc_html( $video['title'] );
        }
        if ( isset( $video['caption'] ) && !empty( $video['caption'] ) ) {
            $input1['video']['description'] = esc_html( $video['caption'] );
        }
        if ( isset( $video['description'] ) && !empty( $video['description'] ) ) {
            $input1['video']['description'] = esc_html( $video['description'] );
        }
        if ( isset( $video['poster']['url'] ) ) {
            $input1['video']['thumbnailUrl'] = esc_url( $video['poster']['url'] );

            if ( isset( $video['poster']['id'] ) ) {
                 $poster_id = $video['poster']['id'];
                 $poster_sizes_url = array(
                     saswp_get_image_size_url( $poster_id, 'full' ),
                     saswp_get_image_size_url( $poster_id, 'wpzoom-rcb-structured-data-1_1' ),
                     saswp_get_image_size_url( $poster_id, 'wpzoom-rcb-structured-data-4_3' ),
                     saswp_get_image_size_url( $poster_id, 'wpzoom-rcb-structured-data-16_9' ),
                 );
                 $input1['video']['thumbnailUrl'] = array_values( array_unique( $poster_sizes_url ) );
             }
        }
        if ( isset( $video['url'] ) ) {
            $input1['video']['contentUrl'] = esc_url( $video['url'] );

            if ( 'embed' === $video_type ) {
                $video_embed_url = $video['url'];

                $input1['video']['@type'] = 'VideoObject';

                if ( ! empty( $attributes['image'] ) && isset( $attributes['hasImage'] ) && $attributes['hasImage'] ) {
                    $image_id = isset( $attributes['image']['id'] ) ? $attributes['image']['id'] : 0;
                     $image_sizes = isset( $attributes['image']['sizes'] ) ? $attributes['image']['sizes'] : array();
                     $image_sizes_url = array(
                         saswp_get_image_size_url( $image_id, 'full', $image_sizes ),
                         saswp_get_image_size_url( $image_id, 'wpzoom-rcb-structured-data-1_1', $image_sizes ),
                         saswp_get_image_size_url( $image_id, 'wpzoom-rcb-structured-data-4_3', $image_sizes ),
                         saswp_get_image_size_url( $image_id, 'wpzoom-rcb-structured-data-16_9', $image_sizes ),
                     );
                     $input1['video']['thumbnailUrl'] = array_values( array_unique( $image_sizes_url ) );
                }

                if ( strpos( $video['url'], 'youtu' ) ) {
                    $video_embed_url = $helpers->convert_youtube_url_to_embed( $video['url'] );
                }
                elseif ( strpos( $video['url'] , 'vimeo' ) ) {
                    $video_embed_url = $helpers->convert_vimeo_url_to_embed( $video['url'] );
                }

                $input1['video']['embedUrl'] = esc_url( $video_embed_url );
            }
        }
        if ( isset( $video['date'] ) && 'embed' === $video_type ) {
            $input1['video']['uploadDate'] = $video['date'];
        }
    }

        $extra_theme_review = $service_object->saswp_extra_theme_review_details(get_the_ID());
        $aggregateRating    = $service_object->saswp_rating_box_rating_markup(get_the_ID());
				
		if ( ! empty( $aggregateRating) ) {
                $input1['aggregateRating'] = $aggregateRating;
        }                                
        if ( ! empty( $extra_theme_review) ) {
            $input1 = array_merge($input1, $extra_theme_review);
        }
        
        $input1 = saswp_append_fetched_reviews($input1, get_the_ID());

    }else{

        
        $attributes = saswp_get_gutenberg_block_data('saswp/recipe-block');

        if ( isset( $attributes['attrs']) ) {

            $data = $attributes['attrs'];            

            $input1['@context']              = saswp_context_url();
            $input1['@type']                 = 'Recipe';
            $input1['@id']                   = saswp_get_permalink().'#Recipe';
            $input1['name']                  = isset($data['title']) ? $data['title'] : saswp_get_the_title();                           
            $input1['datePublished']         = get_the_date("c");
            $input1['dateModified']          = get_the_modified_date("c");            
            $input1['author']                = isset($data['author']) ? $data['author'] : saswp_get_author_details();

            $keywords = array();

            if ( ! empty( $data['cook_time']) ) {
                $input1['cookTime']             = 'PT'.$data['cook_time'].'M';
            }
            if ( ! empty( $data['pre_time']) ) {
                $input1['prepTime']             = 'PT'.$data['pre_time'].'M';
            }
            if ( ! empty( $data['cuisine']) ) {
                $keywords[] = $data['cuisine']; 
                $input1['recipeCuisine']        = $data['cuisine'];
            }
            if ( ! empty( $data['calories']) ) {
                $input1['nutrition']['@type']   = 'NutritionInformation';
                $input1['nutrition']['calories'] = $data['calories'];
            }
            if ( ! empty( $data['servings']) ) {
                $keywords[] = $data['servings']; 
                $input1['recipeYield']           = $data['servings'];
            }
            if ( ! empty( $data['course']) ) {
                $keywords[] = $data['course']; 
                $input1['recipeCategory']        = $data['course'];
            }
            if ( ! empty( $data['banner_url']) ) {
                $input1['image']        = $data['banner_url'];
            }

            $input1['keywords']              = isset($keywords) ? $keywords :  saswp_get_the_tags();    

            if ( ! empty( $data['notes']) ) {
                $ing = '';
                foreach ( $data['notes'] as $value) {
                    if ( isset( $value['name']) ) {
                        $ing .= $value['name']. ', ';
                    }                    
                }
                $input1['description'] = $ing;
            }

            if ( ! empty( $data['ingredients']) ) {
                $ing = array();
                foreach ( $data['ingredients'] as $value) {
                    $ing[] = $value['name'];
                }
                $input1['recipeIngredient'] = $ing;
            }

            if ( ! empty( $data['directions']) ) {
                $ing = array();
                foreach ( $data['directions'] as $value) {
                    $ing[] = array(
                        '@type' => 'HoWToStep',
                        'name'  => $value['name'],
                        'text'  => isset($value['text']) ? $value['text'] : '', 
                    );
                }
                $input1['recipeInstructions'] = $ing;
            }

        }

    }    
                                    
    return apply_filters('saswp_modify_recipe_block_schema_output', $input1 );

}

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
    
    if ( function_exists( 'parse_blocks') && is_object($post) ) {
        
            $blocks = parse_blocks($post->post_content);            
            
            if($blocks){

                foreach ( $blocks as $parse_blocks){
                        $block_list[] = $parse_blocks['blockName'];
                        $block_data[$parse_blocks['blockName']] = $parse_blocks;
                }

            }        
    }
    
    if($block_list){
    
        if(in_array($block, $block_list) ) {
            $response = $block_data[$block];
        }
        
    }
    
    if(empty($response) ) {
        $block_matched = saswp_search_gutenberg_block($block_data, $block);
        if ( ! empty( $block_matched) && is_array($block_matched) ) {
            if ( isset( $block_matched[0]) && !empty($block_matched[0]) ) {
                $response = $block_matched[0];
            }
        }
    }

    return $response;
    
}

function saswp_search_gutenberg_block($block_data, $block) {
    $matches = [];
    if ( ! empty( $block_data) && is_array($block_data) ) {
        foreach ( $block_data as $item) {
            if (is_array($item)) {
                // If the item is an array, recursively search it
                $nestedMatches = saswp_search_gutenberg_block($item, $block);
                $matches = array_merge($matches, $nestedMatches);
            } elseif ($item === $block) {
                // If the item matches the desired value, add the entire subarray to the matches
                $matches[] = $block_data;
                break; // If you want to find only the first match, you can remove this line.
            }
        }
    }
    return $matches;
}

/* multiple videos */
function saswp_get_gutenberg_multiple_block_data($block){
    
    global $post;
     
    $block_list = array();
    $block_data = array();
    $response   = array();
    
    if ( function_exists( 'parse_blocks') && is_object($post) ) {
        
            $blocks = parse_blocks($post->post_content);            
            
            if($blocks){

                foreach ( $blocks as $parse_blocks){
                        $block_list[] = $parse_blocks['blockName'];
                        $block_data[][$parse_blocks['blockName']] = $parse_blocks;
                }

            }        
    }
    
    if ( ! empty( $block_data) ) {
        foreach ( $block_data as $value) {   
            if ( isset( $value[$block]) ) {
                $response[] = $value[$block];                                          
            }
            
        }
    }
    return $response;
    
}

function saswp_gutenberg_how_to_schema() {
                        
                global $post, $sd_data;
                
                $input1 = array();
                
                $yoast_howto = saswp_get_gutenberg_block_data('yoast/how-to-block');  

                $ub_howto    = saswp_get_gutenberg_block_data('ub/how-to');                      

                if ( isset( $sd_data['saswp-yoast']) && $sd_data['saswp-yoast'] == 1 && $yoast_howto && isset($yoast_howto['attrs']) ) {
                    
                $service_object     = new SASWP_Output_Service();   
                $feature_image      = $service_object->saswp_get_featured_image();                  
                                       
                $input1['@context']              = saswp_context_url();
                $input1['@type']                 = 'HowTo';
                $input1['@id']                   = saswp_get_permalink().'#HowTo';
                $input1['name']                  = saswp_get_the_title();                
                $input1['datePublished']         = get_the_date("c");
                $input1['dateModified']          = get_the_modified_date("c");
                
                if ( ! empty( $feature_image) ) {
                            
                    $input1 = array_merge($input1, $feature_image);   
                         
                }                
                
                if(array_key_exists('jsonDescription', $yoast_howto['attrs']) ) {
                    $input1['description']           = $yoast_howto['attrs']['jsonDescription'];
                }else{
                    $input1['description']           = saswp_get_the_excerpt();
                }
                             
                $step     = array();
                $step_arr = array(); 
                
                if(array_key_exists('steps', $yoast_howto['attrs']) ) {
                    $step = $yoast_howto['attrs']['steps'];
                }                                                           
                if ( ! empty( $step) ) {

                    foreach( $step as $key => $val){

                        $supply_data = array();
                        $direction   = array();
                        $tip         = array();

                       if($val['name'] || $val['text']){

                        if ( isset( $val['text'][0]) ) {
                            $direction['@type']     = 'HowToDirection';
                            $direction['text']      = $val['text'][0];
                        }

                        if ( isset( $val['text'][0]) ) {

                            $tip['@type']           = 'HowToTip';
                            $tip['text']            = $val['text'][0];

                        }

                        $supply_data['@type']   = 'HowToStep';
                        $supply_data['url']     = saswp_get_permalink().'#step'.++$key;
                        $supply_data['name']    = $val['name'][0];    

                        if ( isset( $direction['text']) || isset($tip['text']) ) {
                            $supply_data['itemListElement']  = array($direction, $tip);
                        }

                        if ( isset( $val['text'][1]['key']) && $val['text'][1]['key'] !='' ) {

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
                
                 if ( isset( $yoast_howto['attrs']['days']) || isset($yoast_howto['attrs']['hours']) || isset($yoast_howto['attrs']['minutes']) ) {
                     
                             $input1['totalTime'] = 'P'. 
                             ((isset($yoast_howto['attrs']['days']) && $yoast_howto['attrs']['days'] !='') ? esc_attr( $yoast_howto['attrs']['days']).'DT':''). 
                             ((isset($yoast_howto['attrs']['hours']) && $yoast_howto['attrs']['hours'] !='') ? esc_attr( $yoast_howto['attrs']['hours']).'H':''). 
                             ((isset($yoast_howto['attrs']['minutes']) && $yoast_howto['attrs']['minutes'] !='') ? esc_attr( $yoast_howto['attrs']['minutes']).'M':''); 
                             
                 }       

                } elseif( (isset($sd_data['saswp-ultimate-blocks']) && $sd_data['saswp-ultimate-blocks'] == 1 ) && $ub_howto && isset($ub_howto['attrs']) ) {
                    
                    extract($ub_howto['attrs']);
                    
                    $input1['@context']              = saswp_context_url();
                    $input1['@type']                 = 'HowTo';
                    $input1['@id']                   = saswp_get_permalink().'#HowTo';
                    $input1['name']                  = saswp_get_the_title();                
                    $input1['datePublished']         = get_the_date("c");
                    $input1['dateModified']          = get_the_modified_date("c");    
                    $input1['description']           = $introduction ? $introduction : saswp_get_the_excerpt(); 

                    if ( function_exists( 'generateISODurationCode') ) {
                        $ISOTotalTime = generateISODurationCode($totalTime);

                        if($ISOTotalTime){
                            $input1['totalTime'] = $ISOTotalTime;
                        }
                    }
                    

                    $supply     = array();
                    $supply_arr = array();
                                        
                    if($advancedMode && $includeSuppliesList && count($supplies) > 0){

                        foreach( $supplies as $val){

                            $supply_data = array();

                            if($val['name']){
                                $supply_data['@type'] = 'HowToSupply';
                                $supply_data['name']  = $val['name'];                            
                                $supply_data['image'] = $val['imageURL'];                            
                            }

                        $supply_arr[] =  $supply_data;
                        }
                        $input1['supply'] = $supply_arr;
                    }

                    $tool     = array();
                    $tool_arr = array();
                                                            
                    if($advancedMode && $includeToolsList && count($tools) > 0){

                        foreach( $tools as $val){

                            $supply_data = array();

                            if($val['name']){
                                $supply_data['@type'] = 'HowToTool';
                                $supply_data['name']  = $val['name'];    
                                $supply_data['image'] = $val['imageURL'];                        
                            }

                        $tool_arr[] =  $supply_data;
                        }
                    $input1['tool'] = $tool_arr;
                    }
                    $step_sec = array();
                    if ( isset( $useSections) ) {

                        foreach( $section as $i => $s){

                            $step_arr = array();

                            foreach( $s['steps'] as $j => $step){
                                $step_arr[] = array(
                                    '@type'               => 'HowToStep',                                  
                                    'name'                => $step['title'],
                                    'image'               => $step['stepPic']['url'],
                                    'url'                 => get_permalink(). '#'. $step['anchor'],
                                    'itemListElement'     => array(
                                        $step['direction'] ? array('@type' => 'HowToDirection', 'text' => $step['direction']) : '',
                                        $step['tip'] ? array('@type' => 'HowToTip', 'text' => $step['tip']) : ''
                                    ),
                                );
                            }

                            $step_sec[] = array(
                                '@type'             => 'HowToSection',
                                'name'              => $s['sectionName'],                                
                                'itemListElement'   => $step_arr,
                            );

                            $input1['step'] = $step_sec;
                        }

                    }else{

                        if(count($section) > 0){

                            $step_arr = array();

                            foreach( $section[0]['steps'] as $j => $step){

                                $step_arr[] = array(
                                    '@type'               => 'HowToStep',                                  
                                    'name'                => $step['title'],
                                    'image'               => $step['stepPic']['url'],
                                    'url'                 => get_permalink(). '#'. $step['anchor'],
                                    'itemListElement'     => array(
                                        $step['direction'] ? array('@type' => 'HowToDirection', 'text' => $step['direction']): '',
                                        $step['tip']? array('@type' => 'HowToTip', 'text' => $step['tip']) : '',
                                    ),
                                );

                            }
                            
                            $input1['step'] = $step_arr;
                        }
                    }
                    
                } else {
                
                $parse_blocks = saswp_get_gutenberg_block_data('saswp/how-to-block');

                if ( isset( $parse_blocks['attrs']) ) {
                    
                $service_object     = new SASWP_Output_Service();   
                $feature_image      = $service_object->saswp_get_featured_image();                  
                                       
                $input1['@context']              = saswp_context_url();
                $input1['@type']                 = 'HowTo';
                $input1['@id']                   = saswp_get_permalink().'#HowTo';
                $input1['name']                  = saswp_get_the_title();                
                $input1['datePublished']         = get_the_date("c");
                $input1['dateModified']          = get_the_modified_date("c");
                
                if ( ! empty( $feature_image) ) {
                            
                    $input1 = array_merge($input1, $feature_image);   
                         
                }                
                
                if(array_key_exists('description', $parse_blocks['attrs']) ) {
                    $input1['description']           = $parse_blocks['attrs']['description'];
                }
                
                $supply     = array();
                $supply_arr = array();
                
                if(array_key_exists('materials', $parse_blocks['attrs']) ) {
                    $supply = $parse_blocks['attrs']['materials'];
                }
                
                if ( ! empty( $supply) ) {

                    foreach( $supply as $val){

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
                
                if(array_key_exists('tools', $parse_blocks['attrs']) ) {
                    $tool = $parse_blocks['attrs']['tools'];
                }
                
                if ( ! empty( $tool) ) {

                    foreach( $tool as $val){

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
                
                if(array_key_exists('items', $parse_blocks['attrs']) ) {
                    $step = $parse_blocks['attrs']['items'];
                }                                                           
                if ( ! empty( $step) ) {

                    foreach( $step as $key => $val){
                        
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
                        $supply_data['url']     = saswp_get_permalink().'#step'.++$key;
                        $supply_data['name']    = $val['title'];    

                        if ( isset( $direction['text']) || isset($tip['text']) ) {
                            $supply_data['itemListElement']  = array($direction, $tip);
                        }

                        if ( isset( $val['imageId']) && $val['imageId'] !='' ) {

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
                
                 if ( isset( $parse_blocks['attrs']['days']) || isset($parse_blocks['attrs']['hours']) || isset($parse_blocks['attrs']['minutes']) ) {
                     
                             $input1['totalTime'] = 'P'. 
                             ((isset($parse_blocks['attrs']['days']) && $parse_blocks['attrs']['days'] !='') ? esc_attr( $parse_blocks['attrs']['days']).'DT':''). 
                             ((isset($parse_blocks['attrs']['hours']) && $parse_blocks['attrs']['hours'] !='') ? esc_attr( $parse_blocks['attrs']['hours']).'H':''). 
                             ((isset($parse_blocks['attrs']['minutes']) && $parse_blocks['attrs']['minutes'] !='') ? esc_attr( $parse_blocks['attrs']['minutes']).'M':''); 
                             
                 }   

                 if ( isset( $parse_blocks['attrs']['price']) && isset($parse_blocks['attrs']['currency']) ) {
                
                    $input1['estimatedCost']['@type']   = 'MonetaryAmount';
                    $input1['estimatedCost']['currency']= $parse_blocks['attrs']['currency'];
                    $input1['estimatedCost']['value']   = $parse_blocks['attrs']['price'];
                 }

                }
                    
                }
                
            if($input1){
                
                $service_object     = new SASWP_Output_Service();

                $extra_theme_review = $service_object->saswp_extra_theme_review_details(get_the_ID());
                $aggregateRating    = $service_object->saswp_rating_box_rating_markup(get_the_ID());
				
                if ( ! empty( $aggregateRating) ) {
                        $input1['aggregateRating'] = $aggregateRating;
                }                                
                if ( ! empty( $extra_theme_review) ) {
                    $input1 = array_merge($input1, $extra_theme_review);
                }
        
                $input1 = saswp_append_fetched_reviews($input1, get_the_ID());
                
            }    
                                
            return apply_filters('saswp_modify_howto_schema_output', $input1 );
    
}

function saswp_gutenberg_faq_schema() {
                        
            global $post, $sd_data;
            $input1 = array();

            $yoast_faq = saswp_get_gutenberg_block_data('yoast/faq-block');
            
            if ( isset( $sd_data['saswp-yoast']) && $sd_data['saswp-yoast'] == 1 && $yoast_faq && isset($yoast_faq['attrs']) ) {
                                
                           $input1['@context']              = saswp_context_url();
                           $input1['@type']                 = 'FAQPage';
                           $input1['@id']                   = saswp_get_permalink().'#FAQPage';                            

                           $faq_question_arr = array();

                           if ( ! empty( $yoast_faq['attrs']['questions']) ) {

                               foreach( $yoast_faq['attrs']['questions'] as $val){

                                   $supply_data = array();
                                   $supply_data['@type']                   = 'Question';
                                   $supply_data['name']                    = (isset($val['jsonQuestion']) && is_string($val['jsonQuestion']) ) ? htmlspecialchars($val['jsonQuestion'], ENT_QUOTES, 'UTF-8') : '';
                                   $supply_data['acceptedAnswer']['@type'] = 'Answer';
                                   $supply_data['acceptedAnswer']['text']  = (isset($val['jsonAnswer']) && is_string($val['jsonAnswer']) ) ? htmlspecialchars($val['jsonAnswer'], ENT_QUOTES, 'UTF-8') : '';

                                    if ( isset( $val['answer'][1]['key']) && $val['answer'][1]['key'] !='' ) {

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

                if ( isset( $attributes['attrs']) ) {

                           $input1['@context']              = saswp_context_url();
                           $input1['@type']                 = 'FAQPage';
                           $input1['@id']                   = saswp_get_permalink().'#FAQPage';                            

                           $faq_question_arr = array();

                           if ( ! empty( $attributes['attrs']['items']) ) {

                               foreach( $attributes['attrs']['items'] as $val){

                                   $supply_data = array();
                                   $supply_data['@type']                   = 'Question';
                                   $supply_data['name']                    = htmlspecialchars(wp_strip_all_tags($val['title']), ENT_QUOTES, 'UTF-8');
                                   $supply_data['acceptedAnswer']['@type'] = 'Answer';
                                   $supply_data['acceptedAnswer']['text']  = isset($val['description'])?htmlspecialchars(wp_strip_all_tags(do_shortcode($val['description'])), ENT_QUOTES, 'UTF-8'):'';

                                    if ( isset( $val['imageId']) && $val['imageId'] !='' ) {

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

            return apply_filters('saswp_modify_faq_block_schema_output', $input1 );
    
}

function saswp_gutenberg_event_schema() {
    
    $input1 = array();
     
    $attributes = saswp_get_gutenberg_block_data('saswp/event-block');
    
    if ( isset( $attributes['attrs']) ) {
        
        $data = $attributes['attrs'];
                
        $input1['@context']              = saswp_context_url();
        $input1['@type']                 = 'Event';
        $input1['@id']                   = saswp_get_permalink().'#Event';  
        $input1['name']                  = saswp_get_the_title();  
        $input1['description']           = isset($data['description']) ? $data['description'] : saswp_get_the_excerpt();
        $input1['startDate']             = saswp_format_date_time($data['start_date'], $data['start_time']);
        $input1['endDate']               = saswp_format_date_time($data['end_date'], $data['end_time']);

        $input1['eventStatus']           = $data['event_status'];
        $input1['eventAttendanceMode']   = $data['attendance_mode'];

        if ( isset( $data['event_status']) && $data['event_status'] == 'EventRescheduled' && isset($data['previous_date']) ) {
            $input1['PreviousStartDate']               = saswp_format_date_time($data['previous_date'], $data['previous_time']);
        }
        
        if ( isset( $data['venue_address']) || isset($data['venue_name']) ) {
                            
        $input1['location']['@type']                      = 'Place';
        $input1['location']['name']                       = $data['venue_address'];
        $input1['location']['address']['@type']           = 'PostalAddress';
        $input1['location']['address']['streetAddress']   = $data['venue_address'];
        $input1['location']['address']['addressLocality'] = $data['venue_city'];
        $input1['location']['address']['postalCode']      = $data['venue_postal_code'];
        $input1['location']['address']['addressRegion']   = $data['venue_state'];
        $input1['location']['address']['addressCountry']  = $data['venue_country'];
        
        }
        if ( isset( $data['price']) ) {
        
        $input1['offers']['@type']         = 'Offer';
        $input1['offers']['url']           = saswp_get_permalink();
        $input1['offers']['price']         = $data['price'];
        $input1['offers']['priceCurrency'] = (isset($data['currency']) && $data['currency']) ? $data['currency'] : 'USD';
        $input1['offers']['availability']  = 'InStock';
        $input1['offers']['validFrom']     = saswp_format_date_time($data['start_date'], $data['start_time']);
        
        }
        
         if ( ! empty( $data['organizers']) ) {
             
             foreach( $data['organizers'] as $org){
                
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

        if ( ! empty( $data['performers']) ) {

            foreach( $data['performers'] as $val){

                $supply_data = array();
                $supply_data['@type']        = 'Person';
                $supply_data['name']         = $val['name'];                                    
                $supply_data['url']          = $val['url'];
                $supply_data['email']        = $val['email'];

                $performer_arr[] =  $supply_data;
            }

           $input1['performer'] = $performer_arr;

        }       

        if( !empty($input1) && !isset($input1['image']) ) {

                        $service_object     = new SASWP_Output_Service();
                        $input2             = $service_object->saswp_get_featured_image();

                        if ( ! empty( $input2) ) {

                          $input1 = array_merge($input1,$input2); 

                        }                                                                    
                    }
         
        }
                        
    return $input1;
        
}

function saswp_gutenberg_qanda_schema() {
    
    $input1 = array();
     
    $attributes = saswp_get_gutenberg_block_data('saswp/qanda-block');
    
    if ( isset( $attributes['attrs']) ) {
        
        $data                           = $attributes['attrs'];
        $accepted_answer                = $data['accepted_answers'];
        $suggested_answer               = $data['suggested_answers'];
                
        $answer_count   = 0;
        $accepted_json  = array();
        $suggested_json = array();

        if($accepted_answer){
            foreach( $accepted_answer as $answer){
                $accepted_json[] = array(
                    '@type'         => 'Answer',
                    'text'          => htmlspecialchars($answer['text'], ENT_QUOTES, 'UTF-8'),
                    'dateCreated'   => $answer['date_created_iso'],
                    'upvoteCount'   => $answer['vote'],
                    'url'           => $answer['url'],
                    'author'        => array(
                                    '@type' => 'Person',
                                    'name'  => $answer['author']
                    ),                    
                );
            }

            $answer_count += count($accepted_answer);
        }

        if($suggested_answer){
            foreach( $suggested_answer as $answer){
                $suggested_json[] = array(
                    '@type'         => 'Answer',
                    'text'          => htmlspecialchars($answer['text'], ENT_QUOTES, 'UTF-8'),
                    'dateCreated'   => $answer['date_created_iso'],
                    'upvoteCount'   => $answer['vote'],
                    'url'           => $answer['url'],
                    'author'        => array(
                                    '@type' => 'Person',
                                    'name'  => $answer['author']
                    ),                    
                );
            }
            $answer_count += count($suggested_json);
        }
                
        $input1['@context']              = saswp_context_url();
        $input1['@type']                 = 'QAPage';
        $input1['@id']                   = saswp_get_permalink().'#QAPage';  

        $input1['mainEntity']['@type']                        = 'Question';
        $input1['mainEntity']['name']                         = $data['question_name'];
        $input1['mainEntity']['text']                         = $data['question_text'];
        $input1['mainEntity']['answerCount']                  = $answer_count;
        $input1['mainEntity']['upvoteCount']                  = $data['question_up_vote'];
        $input1['mainEntity']['dateCreated']                  = $data['question_date_created_iso'];
        $input1['mainEntity']['author']['@type']              = 'Person';
        $input1['mainEntity']['author']['name']               = $data['question_author'];
        $input1['mainEntity']['acceptedAnswer']               = $accepted_json;
        $input1['mainEntity']['suggestedAnswer']              = $suggested_json;

    }    
                
    return $input1;
        
}

function saswp_gutenberg_book_schema() {

    $input1 = array();

    $attributes = saswp_get_gutenberg_block_data('saswp/book-block');

    if ( isset( $attributes['attrs']) ) {

        $data = $attributes['attrs'];

        $input1['@context']              = saswp_context_url();
        $input1['@type']                 = 'Book';
        $input1['@id']                   = saswp_get_permalink().'#Book';  
        $input1['name']                  = $data['title'] ? $data['title'] : saswp_get_the_title(); 

        if ( ! empty( $data['description']) ) {
            $input1['description']           = wp_strip_all_tags($data['description']);
        }
        
        if ( ! empty( $data['release_date']) ) {            
            $input1['datePublished']  = saswp_format_date_time($data['release_date']);
        }
        if ( ! empty( $data['author']) ) {
            $input1['author']['@type'] = 'Person';
            $input1['author']['name']  = $data['author'];
        }
        if ( ! empty( $data['publisher']) ) {
            $input1['publisher']['@type'] = 'Organization';
            $input1['publisher']['name']  = $data['publisher'];
        }
        if ( ! empty( $data['pages']) ) {            
            $input1['numberOfPages']  = $data['pages'];
        }
        if ( ! empty( $data['format']) ) {            
            $input1['bookFormat']  = $data['format'];
        }
        if ( ! empty( $data['genre']) ) {            
            $input1['genre']  = $data['genre'];
        }
        if ( ! empty( $data['rating']) ) {            
            $input1['aggregateRating']['@type']       = 'AggregateRating';
            $input1['aggregateRating']['ratingValue'] = $data['rating'];
            $input1['aggregateRating']['reviewCount'] = 1;
        }        
    }

    return $input1;
}

function saswp_gutenberg_job_schema() {
    
    $input1 = array();
     
    $attributes = saswp_get_gutenberg_block_data('saswp/job-block');
    
    if ( isset( $attributes['attrs']) ) {
        
        $data = $attributes['attrs'];
                
        $input1['@context']              = saswp_context_url();
        $input1['@type']                 = 'JobPosting';
        $input1['@id']                   = saswp_get_permalink().'#JobPosting';  
        $input1['title']                 = saswp_get_the_title();  
        $input1['description']           = $data['job_description'] ? wp_strip_all_tags($data['job_description']) : saswp_get_the_excerpt();
        $input1['datePosted']            = get_the_date("c");        
        $input1['validThrough']          = saswp_format_date_time($data['listing_expire_date']);  
        $input1['employmentType']        = $data['job_types'];  
        
        if ( isset( $data['location_address']) ) {
                            
        $input1['jobLocation']['@type']                      = 'Place';        
        $input1['jobLocation']['address']['@type']           = 'PostalAddress';
        $input1['jobLocation']['address']['streetAddress']   = $data['location_address'];
        $input1['jobLocation']['address']['addressLocality'] = $data['location_city'];
        $input1['jobLocation']['address']['postalCode']      = $data['location_postal_code'];
        $input1['jobLocation']['address']['addressRegion']   = $data['location_state'];
        $input1['jobLocation']['address']['addressCountry']  = $data['location_country'];
        
        }
        if ( isset( $data['base_salary']) ) {
        
        $input1['baseSalary']['@type']             = 'MonetaryAmount';        
        $input1['baseSalary']['currency']          = $data['currency_code'];
        $input1['baseSalary']['value']['@type']    = 'QuantitativeValue';
        $input1['baseSalary']['value']['value']    = $data['base_salary'];
        $input1['baseSalary']['value']['unitText'] = $data['unit_text'];                
        }
        
        if ( isset( $data['company_name']) || isset($data['company_website']) ) {
                                
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
                                                     
        if( !empty($input1) && !isset($input1['image']) ) {

                        $service_object     = new SASWP_Output_Service();
                        $input2             = $service_object->saswp_get_featured_image();

                        if ( ! empty( $input2) ) {

                          $input1 = array_merge($input1,$input2); 

                        }                                                                    
                    }
         
        }
                        
    return $input1;
        
}

function saswp_gutenberg_course_schema() {
    
    $input1 = array();
     
    $attributes = saswp_get_gutenberg_block_data('saswp/course-block');
    
    if ( isset( $attributes['attrs']) && !empty($attributes['attrs']) ) {
                
        $loop_markup  = array();
        $item_list    = array();
        $course_count = count($attributes['attrs']['courses']);
        $i = 1;
        foreach( $attributes['attrs']['courses'] as $course){
            
            $markup = array();
            
            $markup['@context']           = saswp_context_url();
            $markup['@type']              = 'Course';
            $markup['url']                = ($course_count > 1 ? saswp_get_permalink().'#course_'.$i : saswp_get_permalink());
            $markup['@id']                = saswp_get_permalink().'#Course'; 
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

/**
 * Generate schema markup for live blog posting block
 * @return  $input1     array
 * @since   1.43
 * */
function saswp_gutenberg_live_blog_posting_schema() {
    
    $input1 = array();
     
    $attributes = saswp_get_gutenberg_block_data( 'saswp/live-blog-posting' );
    
    if ( ! empty( $attributes['attrs'] ) ) {

        $date                           =   get_the_date("c");
        $modified_date                  =   get_the_modified_date("c"); 

        $input1['@context']             =   saswp_context_url();
        $input1['@type']                =   'LiveBlogPosting';
        $input1['@id']                  =   saswp_get_permalink().'#LiveBlogPosting'; 
        $input1['url']                  =   saswp_get_permalink();                               
        $input1['headline']             =   saswp_get_the_title();
        $input1['description']          =   saswp_get_the_excerpt();
        $input1['datePublished']        =   esc_html( $date);
        $input1['dateModified']         =   esc_html( $modified_date );

        $thumb_id                       =   get_post_thumbnail_id();
        if ( $thumb_id > 0 ) {
            $thumbnail_url              =   saswp_get_image_by_id( $thumb_id );
            $input1['image']            =   $thumbnail_url;
        }
        
        $location   =   array();

        if ( isset( $attributes['attrs']['locationname'] ) ) {
            $location['@type']        =   'Place';
            $location['name']         =   esc_html( $attributes['attrs']['locationname'] );
        }
        if ( isset( $attributes['attrs']['address'] ) || isset( $attributes['attrs']['locality'] ) || isset( $attributes['attrs']['postalcode'] ) || isset( $attributes['attrs']['region'] ) ) {
            $location['address']['@type']                       =   'PostalAddress';
            if ( isset( $attributes['attrs']['address'] ) ) {      
                $location['address']['streetAddress']           =   $attributes['attrs']['address'];
            }
            if ( isset( $attributes['attrs']['locality'] ) ) {      
                $location['address']['addressLocality']         =   $attributes['attrs']['locality'];
            }
            if ( isset( $attributes['attrs']['postalcode'] ) ) {      
                $location['address']['postalCode']              =   $attributes['attrs']['postalcode'];
            }
            if ( isset( $attributes['attrs']['region'] ) ) {      
                $location['address']['addressRegion']           =   $attributes['attrs']['region'];
            }
            if ( isset( $attributes['attrs']['country'] ) ) {      
                $location['address']['addressCountry']['@type'] =   'Country';
                $location['address']['addressCountry']['name '] =   $attributes['attrs']['country'];
            }     
        }

        if ( isset( $attributes['attrs']['name'] ) || isset( $attributes['attrs']['name'] ) ) {
            $input1['about']['@type']                   =   'Event';   
            if ( isset( $attributes['attrs']['name'] ) ) {
                $input1['about']['name']                =   esc_html( $attributes['attrs']['name'] );
            }
            if ( isset( $attributes['attrs']['event_status'] ) ) {
                $input1['about']['eventStatus']         =  $attributes['attrs']['event_status'];    
            }
            if ( isset( $attributes['attrs']['attendance_mode'] ) ) {
                $input1['about']['eventAttendanceMode'] =  $attributes['attrs']['attendance_mode'];    
            }
            if ( isset( $attributes['attrs']['event_start_date_iso'] ) ) {
                $input1['about']['startDate']           =  $attributes['attrs']['event_start_date_iso'];    
            }
            if ( isset( $attributes['attrs']['event_end_date_iso'] ) ) {
                $input1['about']['endDate']             =  $attributes['attrs']['event_end_date_iso'];    
            }
            if ( ! empty( $attributes['attrs']['price'] ) || ! empty( $attributes['attrs']['low_price'] ) || ! empty( $attributes['attrs']['high_price'] ) ) {
                $input1['about']['offers']['@type']     = 'Offer';
                if ( isset( $attributes['attrs']['offer_url'] ) ) {
                    $input1['about']['offers']['url']   = $attributes['attrs']['offer_url'];
                }else{
                    $input1['about']['offers']['url']   = saswp_get_permalink();
                }
                if ( ! empty( $attributes['attrs']['low_price'] ) && ! empty( $attributes['attrs']['high_price'] ) ) {
                    $input1['about']['offers']['@type'] = 'AggregateOffer';
                    $input1['about']['offers']['highPrice']     = $attributes['attrs']['high_price'];
                    $input1['about']['offers']['lowPrice']     = $attributes['attrs']['low_price'];    
                }else{
                    $input1['about']['offers']['price'] = $attributes['attrs']['price'];    
                }
                $input1['about']['offers']['priceCurrency'] = isset( $attributes['attrs']['offer_currency_code'] ) ? $attributes['attrs']['offer_currency_code'] : 'USD';
                $input1['about']['offers']['availability']  = 'InStock';
                if ( isset( $attributes['attrs']['event_offer_date_iso'] ) ) {
                    $input1['about']['offers']['validFrom']     = $attributes['attrs']['event_offer_date_iso'];
                }
            
            }

            if ( ! empty( $attributes['attrs']['organizers']) ) {
                 
                 foreach( $attributes['attrs']['organizers'] as $org){
                    
                     $input1['about']['organizer'][] = array(
                                        '@type'          => 'Organization',
                                        'name'           => $org['name'],                                                                      
                                        'url'            => $org['phone'],
                                        'email'          => $org['email'],
                                        'telephone'      => $org['phone'],                                                                        
                        );                 
                     
                 }
                                                             
             }
                   
            $performer_arr = array();

            if ( ! empty( $attributes['attrs']['performers']) ) {

                foreach( $attributes['attrs']['performers'] as $val){

                    $supply_data = array();
                    $supply_data['@type']        = 'Person';
                    $supply_data['name']         = $val['name'];                                    
                    $supply_data['url']          = $val['url'];
                    $supply_data['email']        = $val['email'];

                    $performer_arr[] =  $supply_data;
                }

               $input1['about']['performer'] = $performer_arr;

            } 

            if ( ! empty( $location ) ) {
                $input1['about']['location']    =   $location;
            }
            $input1['about']['startDate']   =   esc_html( $date );          
        }

        if ( ! empty( $attributes['attrs']['coverage_start_date_iso'] ) ) {
            $date   =   explode( 'T', $attributes['attrs']['coverage_start_date_iso'] );
            if ( is_array( $date ) && ! empty( $date[0] ) && ! empty( $date[1] ) ) {
                $input1['coverageStartTime']   =   saswp_format_date_time( $date[0], $date[1] );
            }   
        }

        if ( ! empty( $attributes['attrs']['coverage_end_date_iso'] ) ) {
            $date   =   explode( 'T', $attributes['attrs']['coverage_end_date_iso'] );
            if ( is_array( $date ) && ! empty( $date[0] ) && ! empty( $date[1] ) ) {
                $input1['coverageEndTime']     =   saswp_format_date_time( $date[0], $date[1] );
            }   
        }

        $live_blog_update               =   array();
        if ( ! empty( $attributes['attrs']['blog_update'] ) && is_array( $attributes['attrs']['blog_update'] ) ) {

            foreach ( $attributes['attrs']['blog_update'] as $blog ) {
                
                $blog_array                         =   array();
                $blog_array['@type']                =   'BlogPosting';

                if ( isset( $blog['headline'] ) ) {
                    $blog_array['headline']         =   esc_html( $blog['headline'] );
                }
                if ( isset( $blog['date'] ) ) {
                    $blog_array['datePublished']    =   gmdate( 'c', strtotime( $blog['date'] ) );
                }
                if ( isset( $blog['body'] ) ) {
                    $blog_array['articleBody']      =   esc_html( $blog['body'] );
                }
                if ( ! empty( $blog['image_id'] ) ) {
                    $blog_array['image']            =   saswp_get_image_by_id( $blog['image_id'] );
                }
                $live_blog_update[]                 =   $blog_array;
            }

        }

        if ( ! empty( $live_blog_update ) ) {
            $input1['liveBlogUpdate']   =   $live_blog_update;
        }

    }

    return $input1;

}