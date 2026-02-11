<?php

add_shortcode( 'saswp_tiny_howto', 'saswp_tiny_howto_render' );

function saswp_tiny_howto_render( $atts, $content = null ){

    global $saswp_tiny_howto;

    $output = '';

    $atts = saswp_wp_kses_post($atts);
    $saswp_tiny_howto = shortcode_atts(
        [
            'css_class'     => '',
            'count'         => '1',
            'html'          => true,
            'cost'          => '',
            'cost_currency' => '',
            'days'          => '',
            'hours'         => '',
            'minutes'       => '',
            'description'   => '',            
            'elements'      => [],
        ], $atts );

    foreach ( $atts as $key => $merged_att ) {
        if ( strpos( $key, 'headline' ) !== false || strpos( $key, 'step_title' ) !== false || strpos( $key,
                'step_description' ) !== false || strpos( $key, 'image' ) !== false ) {
            $saswp_tiny_howto['elements'][ explode( '-', $key )[1] ][ substr( $key, 0, strpos( $key, '-' ) ) ] = $merged_att;
        }
    }
    
    if($saswp_tiny_howto['html'] == 'true'){
                               
        if( !empty($saswp_tiny_howto['cost']) ){

            $time_html = '';
            $time_html .=   esc_attr( $saswp_tiny_howto['cost']). ' ';

            if ( ! empty( $saswp_tiny_howto['cost_currency']) ) {
                $time_html .=     esc_attr( $saswp_tiny_howto['cost_currency']);
            }

            if($time_html !='' ) {
                $output .= '<p class="saswp-how-to-total-time">';
                $output .= '<span class="saswp-how-to-duration-time-text"><strong>'.saswp_label_text('translation-estimate-cost').' :</strong> </span>';    
                $output .= $time_html;
                $output .= '</p>';
            }

        }
        
        if( !empty($saswp_tiny_howto['days']) || !empty($saswp_tiny_howto['hours']) || !empty($saswp_tiny_howto['minutes']) ){

            $time_html = '';            

            if ( ! empty( $saswp_tiny_howto['days']) ) {
                $time_html .=     esc_attr( $saswp_tiny_howto['days']).' days ';
            }
            if ( ! empty( $saswp_tiny_howto['hours']) ) {
                $time_html .=     esc_attr( $saswp_tiny_howto['hours']).' hours ';
            }
            if ( ! empty( $saswp_tiny_howto['minutes']) ) {
                $time_html .=     esc_attr( $saswp_tiny_howto['minutes']).' minutes ';
            }

            if($time_html !='' ) {
                $output .= '<p class="saswp-how-to-total-time">';
                $output .= '<span class="saswp-how-to-duration-time-text"><strong>'.saswp_label_text('translation-time-needed').' :</strong> </span>';    
                $output .= $time_html;
                $output .= '</p>';
            }

        }

        if( !empty($saswp_tiny_howto['description']) ){
            $output .= '<p>'.wp_kses_post($saswp_tiny_howto['description']).'</p>';
        }
        
        if( !empty($saswp_tiny_howto['elements']) ){

            $output .= '<div class="saswp-how-to-block-steps">';
            $output .= '<ol>';

            foreach ( $saswp_tiny_howto['elements'] as $value) {
                
                if($value['step_title'] || $value['step_description']){
                    
                    $output .= '<li>'; 
                    $output .= '<strong class="saswp-how-to-step-name">'. wp_kses_post($value['step_title']).'</strong>';
                    $output .= '<p class="saswp-how-to-step-text">'.wp_kses_post($value['step_description']);

                    if ( ! empty( $value['image'] ) ) {
                    
                        $image_id       = intval( $value['image'] );                
                        $image_thumburl = wp_get_attachment_image_url( $image_id, [ 150, 150 ] );
                        
                        $output .= '<figure>';
                        // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage
                        $output .= '<a href="'. esc_url( $image_thumburl ).'"><img class="saswp_tiny_howto_image" src="'. esc_url( $image_thumburl).'"></a>';
                        $output .= '</figure>';
    
                    }

                    $output .= '</p>';
                    $output .= '</li>';
                }  

            }
            $output .= '</ol>';
            $output .= '</div>';
            
        }

    }    

    return $output;
}

add_shortcode( 'saswp_tiny_multiple_faq', 'saswp_tiny_multi_faq_render' );

function saswp_tiny_multi_faq_render( $atts, $content = null ){

    global $saswp_tiny_multi_faq;

    $output = '';
    
    if( is_array($atts) ){
        $fixed_atts = [];
        $broken_buffer = '';
        $capturing_key = null;

        foreach ($atts as $key => $val) {            
            
            if ( is_string($key) ) {
                if( $capturing_key ){
                    $fixed_atts[$capturing_key] = rtrim($broken_buffer, '"');
                    $capturing_key = null;
                    $broken_buffer = '';
                }
                $fixed_atts[$key] = $val;
                continue;
            }
            
            if ( is_int($key) ) {
                if ( preg_match('/^(answer-\d+)="(.*)/s', $val, $matches) ) {
                    $capturing_key = $matches[1];
                    $broken_buffer = $matches[2];
                } 
                elseif ( $capturing_key ) {
                    $broken_buffer .= ' ' . $val;
                }
            }
        }

        if( $capturing_key ){
            $fixed_atts[$capturing_key] = rtrim($broken_buffer, '"');
        }

        if( !empty($fixed_atts) ){
            $atts = array_merge($atts, $fixed_atts);
        }
    }    

    $saswp_tiny_multi_faq = shortcode_atts(
        [
            'css_class' => '',
            'count'     => '1',
            'html'      => true,
            'elements'  => [],
        ], $atts );
    
    foreach ( $atts as $key => $merged_att ) {
        if ( strpos( $key, 'headline' ) !== false || strpos( $key, 'question' ) !== false || strpos( $key,
                'answer' ) !== false || strpos( $key, 'image' ) !== false || strpos( $key, 'fontsize' ) !== false || strpos( $key, 'fontunit' ) !== false ) {
            $saswp_tiny_multi_faq['elements'][ explode( '-', $key )[1] ][ substr( $key, 0, strpos( $key, '-' ) ) ] = $merged_att;
        }
    }

    if($saswp_tiny_multi_faq['html'] == 'true'){

        if( !empty($saswp_tiny_multi_faq['elements']) ){

            $validate_headings = array('h1','h2','h3','h4','h5','h6','p');
            $valid_units       = array('pt', 'px', '%', 'em');
            
            foreach ( $saswp_tiny_multi_faq['elements'] as $key => $value ) {
                
                // Initialize Defaults
                $current_headline = !empty($value['headline']) && in_array(strtolower($value['headline']), $validate_headings) ? $value['headline'] : 'h2';
                $current_question = !empty($value['question']) ? $value['question'] : '';
                $current_answer   = !empty($value['answer']) ? $value['answer'] : '';
                $current_image    = !empty($value['image']) ? intval($value['image']) : 0;
                
                // CSS Logic
                $title_css = '';
                if ( !empty($value['fontsize']) && !empty($value['fontunit']) && in_array($value['fontunit'], $valid_units) ) {
                    $title_css = 'style="font-size:'.esc_attr( intval($value['fontsize']) ) .esc_attr( $value['fontunit'] ) .';"';     
                }

                // Render
                $output .= '<section>';
                
                $output .= '<summary>';
                $output .= '<'.esc_html( $current_headline ).' '. $title_css . '>'; 
                $output .=  esc_html( $current_question );
                $output .= '</'.esc_html( $current_headline ).'>';
                $output .= '</summary>';

                $output .= '<div>';

                if ( $current_image ) {
                    $image_thumburl = wp_get_attachment_image_url( $current_image, [ 150, 150 ] );
                    $output .= '<figure>';
                    $output .= '<a href="'. esc_url( $image_thumburl ).'"><img class="saswp_tiny_faq_image" src="'. esc_url( $image_thumburl ).'"></a>';
                    $output .= '</figure>';
                }
                
                // Decode output: Logic restores HTML tags from entities sent by JS
                $clean_answer = stripslashes( html_entity_decode( $current_answer ) );
                
                $output .= '<div class="saswp_faq_tiny_content">'.wp_kses_post( $clean_answer ).'</div>';
                
                $output .= '</div>';
                $output .= '</section>';
            }
        }
    }    

    return $output;
}

add_shortcode( 'saswp_tiny_faq', 'saswp_tiny_faq_render' );

function saswp_tiny_faq_render( $atts, $content = null ){

        global $saswp_tiny_faq;

        $atts = saswp_wp_kses_post($atts);
        $saswp_tiny_faq = shortcode_atts(
            [            
                'headline'  => 'h2',
                'img'       => 0,
                'img_alt'   => '',
                'question'  => '',
                'answer'    => '',
                'html'      => 'true',         
            ], $atts );
            
        $output = '';
        $validate_headings = array('h1','h2','h3','h4','h5','h6','p');
        if($saswp_tiny_faq['html'] == 'true' && in_array(strtolower($saswp_tiny_faq['headline']), $validate_headings) ) {                        

            $output .= '<summary>';
            $output .= '<'.esc_html( $saswp_tiny_faq['headline']).'>';
            $output .=  esc_html( $saswp_tiny_faq['question']);
            $output .= '</'.esc_html( $saswp_tiny_faq['headline']).'>';
            $output .= '</summary>';

            $output .= '<div>';

            if ( ! empty( $saswp_tiny_faq['img'] ) ) {
                
                $image_id       = intval( $saswp_tiny_faq['img'] );                
                $image_thumburl = wp_get_attachment_image_url( $image_id, [ 150, 150 ] );
                
                $output .= '<figure>';
                // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage
                $output .= '<a href="'. esc_url( $image_thumburl ).'"><img class="saswp_tiny_faq_image" src="'. esc_url( $image_thumburl).'"></a>';
                $output .= '</figure>';

            }
            
            $output .= '<div class="saswp_faq_tiny_content">'.esc_html( $content).'</div>';
            
            $output .= '</div>';

        }    
                
        return $output;
}

add_shortcode( 'saswp_tiny_recipe', 'saswp_tiny_recipe_render' );

function saswp_tiny_recipe_render( $atts, $content = null ) {

    wp_enqueue_style( 'saswp-g-recipe-css', SASWP_PLUGIN_URL . '/modules/gutenberg/assets/css/recipe.css', array(), SASWP_VERSION );

    global $saswp_tiny_recipe;

    $output = '';

    $atts = saswp_wp_kses_post( $atts );
    $saswp_tiny_recipe = shortcode_atts(
        [
            'recipe_by'         => '',
            'course'            => '',
            'cusine'            => '',
            'difficulty'        => '',
            'servings'          => '',
            'prepration_time'   => '',
            'cooking_time'      => '',
            'calories'          => '',
            'image'             => 0,            
            'ingredients'       => [],
            'directions'        => [],
            'notes'             => [],
            'html'              => true,
        ], $atts );

    foreach ( $atts as $key => $merged_att ) {
        if ( strpos( $key, 'ingradient_name' ) !== false) {
            if ( ! empty( $merged_att) ) {
                $saswp_tiny_recipe['ingredients'][] = $merged_att;
            }
        }
    }
    foreach ( $atts as $key => $merged_att ) {
        if ( strpos( $key, 'direction_name' ) !== false) {
            if ( ! empty( $merged_att) ) {
                $saswp_tiny_recipe['directions'][] = $merged_att;
            }
        }
    }
    foreach ( $atts as $key => $merged_att ) {
        if ( strpos( $key, 'notes_name' ) !== false) {
            if ( ! empty( $merged_att) ) {
                $saswp_tiny_recipe['notes'][] = $merged_att;
            }
        }
    }
    if( $saswp_tiny_recipe['html'] == 'true' ) {
        $image_thumburl = '';
        if ( ! empty( $saswp_tiny_recipe['image'] ) ) {
            $image_id       = intval( $saswp_tiny_recipe['image'] );                
            $image_thumburl = wp_get_attachment_image_url( $image_id);
        }
        $output .= '<div class="saswp-recipe-block-container">';
        $output .= '<div class="saswp-recipe-field-banner"><div class="saswp-book-banner-div">';
        if ( ! empty( $image_thumburl) ) {    
            // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage
            $output .= '<img decoding="async" src="'. esc_url( $image_thumburl).'">';
        }else{
            // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage
            $output .= '<img decoding="async" src="" alt="No Image">';
        }
        $output .= '</div></div>'; // saswp-recipe-field-banner div end
        $output .= '<div class="saswp-recipe-block-heading">';
        $output .= '<h4></h5>';
        $output .= '<p class="saswp-recipe-block-author"><strong>'. esc_html__( 'Recipe By ', 'schema-and-structured-data-for-wp' ) .esc_html( $saswp_tiny_recipe['recipe_by']).'</strong></p>';
        $output .= '<div class="saswp-r-course-section">';
        $output .= '<span class="saswp-recipe-block-course" style="width: 30%;"> '. esc_html__( 'Course: ', 'schema-and-structured-data-for-wp' ) . '<strong>'.esc_html( $saswp_tiny_recipe['course']).'</strong></span>';
        $output .= '<span class="saswp-recipe-block-cuisine" style="width: 30%;"> '. esc_html__( 'Cusine: ', 'schema-and-structured-data-for-wp' ) . ' <strong>'.esc_html( $saswp_tiny_recipe['cusine']).'</strong></span>';
        $output .= '<span class="saswp-recipe-block-difficulty" style="width: 30%;"> '. esc_html__( 'Difficulty: ', 'schema-and-structured-data-for-wp' ) . ': <strong>'.esc_html( $saswp_tiny_recipe['difficulty']).'</strong></span>';
        $output .= '</div>'; // saswp-r-course-section div end
        $output .= '</div>'; // saswp-recipe-block-heading div end
        $output .= '<div class="saswp-recipe-block-details"><div class="saswp-recipe-block-details-items">';
        $output .= '<div class="saswp-recipe-block-details-item">';
        $output .= '<p class="saswp-r-b-label">'. esc_html__( 'Servings', 'schema-and-structured-data-for-wp' ) .'</p>';
        $output .= '<p class="saswp-r-b-unit">'.esc_html( $saswp_tiny_recipe['servings']). esc_html__( ' minutes', 'schema-and-structured-data-for-wp' ) .' </p>';
        $output .= '</div>';
        $output .= '<div class="saswp-recipe-block-details-item">';
        $output .= '<p class="saswp-r-b-label">'. esc_html__( 'Preparing Time', 'schema-and-structured-data-for-wp' ) .'</p>';
        $output .= '<p class="saswp-r-b-unit">'.esc_html( $saswp_tiny_recipe['prepration_time']). esc_html__( ' minutes', 'schema-and-structured-data-for-wp' ) .' </p>';
        $output .= '</div>';
        $output .= '<div class="saswp-recipe-block-details-item">';
        $output .= '<p class="saswp-r-b-label">'. esc_html__( 'Cooking Time', 'schema-and-structured-data-for-wp' ) .'</p>';
        $output .= '<p class="saswp-r-b-unit">'.esc_html( $saswp_tiny_recipe['cooking_time']). esc_html__( ' minutes', 'schema-and-structured-data-for-wp' ) .' </p>';
        $output .= '</div>';
        $output .= '<div class="saswp-recipe-block-details-item">';
        $output .= '<p class="saswp-r-b-label">'. esc_html__( 'Calories', 'schema-and-structured-data-for-wp' ) .'</p>';
        $output .= '<p class="saswp-r-b-unit">'.esc_html( $saswp_tiny_recipe['calories']). esc_html__( ' kcal', 'schema-and-structured-data-for-wp' ) .' </p>';
        $output .= '</div>';
        $output .= '</div></div>'; // saswp-recipe-block-details div end
        $output .= '<div class="saswp-recipe-block-ingredients">';
        $output .= '<h4>'. esc_html__( 'INGREDIENTS', 'schema-and-structured-data-for-wp' ) .'</h4>';
        if ( isset( $saswp_tiny_recipe['ingredients']) && isset($saswp_tiny_recipe['ingredients'][0]) ) {
            $output .= '<ol class="saswp-dirction-ul">';
            foreach ( $saswp_tiny_recipe['ingredients'] as $stci_key => $stci_value) {
                $output .= '<li class="saswp-r-b-direction-item"><p>'.esc_html( $stci_value).'</p></li>';       
            }
            $output .= '</ol>';
        }
        $output .= '</div>'; // saswp-recipe-block-ingredients div end
        $output .= '<div class="saswp-recipe-block-direction">';
        $output .= '<h4>'. esc_html__( 'DIRECTION', 'schema-and-structured-data-for-wp' ) .'</h4><ol class="saswp-dirction-ul">';
        if ( isset( $saswp_tiny_recipe['directions']) && isset($saswp_tiny_recipe['directions'][0]) ) {
            foreach ( $saswp_tiny_recipe['directions'] as $stcd_key => $stcd_value) {
                $output .= '<li class="saswp-r-b-direction-item"><p>'.esc_html( $stcd_value).'</p></li>';       
            }
        }
        $output .= '</ol></div>'; // saswp-recipe-block-direction div end
        $output .= '<div class="saswp-recipe-block-direction">';
        $output .= '<h4>'. esc_html__( 'NOTES', 'schema-and-structured-data-for-wp' ) .'</h4><ol class="saswp-dirction-ul">';
        if ( isset( $saswp_tiny_recipe['notes']) && isset($saswp_tiny_recipe['notes'][0]) ) {
            foreach ( $saswp_tiny_recipe['notes'] as $stcn_key => $stcn_value) {
                $output .= '<li class="saswp-r-b-direction-item"><p>'.esc_html( $stcn_value).'</p></li>';       
            }
        }
        $output .= '</ol></div>'; // saswp-recipe-block-direction div end
        $output .= '</div>'; // saswp-recipe-block-container div end
    }
    return $output;
}

/**
 * Sanitize shortcode attributes
 * @since 1.26
 * @param $atts array
 * @return $atts array
 * */
function saswp_wp_kses_post($atts=array())
{
    if ( ! empty( $atts) && is_array($atts) ) {
        foreach ( $atts as $atts_key => $atts_value) {
            $atts[$atts_key] = wp_kses_post($atts_value);
        }
    }
    return $atts;
}
