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
            $time_html .=   esc_attr($saswp_tiny_howto['cost']). ' ';

            if(!empty($saswp_tiny_howto['cost_currency'])){
                $time_html .=     esc_attr($saswp_tiny_howto['cost_currency']);
            }

            if($time_html !=''){
                $output .= '<p class="saswp-how-to-total-time">';
                $output .= '<span class="saswp-how-to-duration-time-text"><strong>'.saswp_label_text('translation-estimate-cost').' :</strong> </span>';    
                $output .= $time_html;
                $output .= '</p>';
            }

        }
        
        if( !empty($saswp_tiny_howto['days']) || !empty($saswp_tiny_howto['hours']) || !empty($saswp_tiny_howto['minutes']) ){

            $time_html = '';            

            if(!empty($saswp_tiny_howto['days'])){
                $time_html .=     esc_attr($saswp_tiny_howto['days']).' days ';
            }
            if(!empty($saswp_tiny_howto['hours'])){
                $time_html .=     esc_attr($saswp_tiny_howto['hours']).' hours ';
            }
            if(!empty($saswp_tiny_howto['minutes'])){
                $time_html .=     esc_attr($saswp_tiny_howto['minutes']).' minutes ';
            }

            if($time_html !=''){
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

            foreach ($saswp_tiny_howto['elements'] as $value) {
                
                if($value['step_title'] || $value['step_description']){
                    
                    $output .= '<li>'; 
                    $output .= '<strong class="saswp-how-to-step-name">'. wp_kses_post($value['step_title']).'</strong>';
                    $output .= '<p class="saswp-how-to-step-text">'.wp_kses_post($value['step_description']);

                    if ( ! empty( $value['image'] ) ) {
                    
                        $image_id       = intval( $value['image'] );                
                        $image_thumburl = wp_get_attachment_image_url( $image_id, [ 150, 150 ] );
                        
                        $output .= '<figure>';
                        $output .= '<a href="'.esc_url(esc_url($image_thumburl)).'"><img class="saswp_tiny_howto_image" src="'.esc_url($image_thumburl).'"></a>';
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

    $atts = saswp_wp_kses_post($atts);
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

            foreach ($saswp_tiny_multi_faq['elements'] as $value) {
                $validate_headings = array('h1','h2','h3','h4','h5','h6','p');
                if(!in_array(strtolower($value['headline']), $validate_headings)){
                    continue;
                }
                $title_css = '';
                if(isset($value['fontsize']) && $value['fontsize'] > 0){
                    if(isset($value['fontunit']) && is_string($value['fontunit'])){
                        $valid_units = array('pt', 'px', '%', 'em');
                        if(in_array($value['fontunit'], $valid_units)){
                            $title_css = 'style=font-size:'.$value['fontsize'].$value['fontunit'].';';    
                        }
                    }
                }
                $output .= '<section>';
                $output .= '<summary>';
                $output .= '<'.esc_html($value['headline']).' '.esc_html($title_css). '>';
                $output .=  esc_html($value['question']);
                $output .= '</'.esc_html($value['headline']).'>';
                $output .= '</summary>';

                $output .= '<div>';

                if ( ! empty( $value['image'] ) ) {
                    
                    $image_id       = intval( $value['image'] );                
                    $image_thumburl = wp_get_attachment_image_url( $image_id, [ 150, 150 ] );
                    
                    $output .= '<figure>';
                    $output .= '<a href="'.esc_url(esc_url($image_thumburl)).'"><img class="saswp_tiny_faq_image" src="'.esc_url($image_thumburl).'"></a>';
                    $output .= '</figure>';

                }
                
                $output .= '<div class="saswp_faq_tiny_content">'.esc_html($value['answer']).'</div>';
                
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
        if($saswp_tiny_faq['html'] == 'true' && in_array(strtolower($saswp_tiny_faq['headline']), $validate_headings)){                        

            $output .= '<summary>';
            $output .= '<'.esc_html($saswp_tiny_faq['headline']).'>';
            $output .=  esc_html($saswp_tiny_faq['question']);
            $output .= '</'.esc_html($saswp_tiny_faq['headline']).'>';
            $output .= '</summary>';

            $output .= '<div>';

            if ( ! empty( $saswp_tiny_faq['img'] ) ) {
                
                $image_id       = intval( $saswp_tiny_faq['img'] );                
                $image_thumburl = wp_get_attachment_image_url( $image_id, [ 150, 150 ] );
                
                $output .= '<figure>';
                $output .= '<a href="'.esc_url(esc_url($image_thumburl)).'"><img class="saswp_tiny_faq_image" src="'.esc_url($image_thumburl).'"></a>';
                $output .= '</figure>';

            }
            
            $output .= '<div class="saswp_faq_tiny_content">'.esc_html($content).'</div>';
            
            $output .= '</div>';

        }    
                
        return $output;
}

add_shortcode( 'saswp_tiny_recipe', 'saswp_tiny_recipe_render' );

function saswp_tiny_recipe_render( $atts, $content = null ){

    wp_enqueue_style(
         'saswp-g-recipe-css',
         SASWP_PLUGIN_URL . '/modules/gutenberg/assets/css/recipe.css',
         array()                        
    );

    global $saswp_tiny_recipe;

    $output = '';

    $atts = saswp_wp_kses_post($atts);
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
            if(!empty($merged_att)){
                $saswp_tiny_recipe['ingredients'][] = $merged_att;
            }
        }
    }
    foreach ( $atts as $key => $merged_att ) {
        if ( strpos( $key, 'direction_name' ) !== false) {
            if(!empty($merged_att)){
                $saswp_tiny_recipe['directions'][] = $merged_att;
            }
        }
    }
    foreach ( $atts as $key => $merged_att ) {
        if ( strpos( $key, 'notes_name' ) !== false) {
            if(!empty($merged_att)){
                $saswp_tiny_recipe['notes'][] = $merged_att;
            }
        }
    }
    if($saswp_tiny_recipe['html'] == 'true'){
        $image_thumburl = '';
        if ( ! empty( $saswp_tiny_recipe['image'] ) ) {
            $image_id       = intval( $saswp_tiny_recipe['image'] );                
            $image_thumburl = wp_get_attachment_image_url( $image_id);
        }
        $output .= '<div class="saswp-recipe-block-container">';
        $output .= '<div class="saswp-recipe-field-banner"><div class="saswp-book-banner-div">';
        if(!empty($image_thumburl)){    
            $output .= '<img decoding="async" src="'.esc_url($image_thumburl).'">';
        }else{
            $output .= '<img decoding="async" src="" alt="No Image">';
        }
        $output .= '</div></div>'; // saswp-recipe-field-banner div end
        $output .= '<div class="saswp-recipe-block-heading">';
        $output .= '<h4></h5>';
        $output .= '<p class="saswp-recipe-block-author"><strong>'.esc_html__('Recipe By ', 'saswp-for-wp').esc_html($saswp_tiny_recipe['recipe_by']).'</strong></p>';
        $output .= '<div class="saswp-r-course-section">';
        $output .= '<span class="saswp-recipe-block-course" style="width: 30%;"> '.esc_html__('Course: ', 'saswp-for-wp'). '<strong>'.esc_html($saswp_tiny_recipe['course']).'</strong></span>';
        $output .= '<span class="saswp-recipe-block-cuisine" style="width: 30%;"> '.esc_html__('Cusine: ', 'saswp-for-wp'). ' <strong>'.esc_html($saswp_tiny_recipe['cusine']).'</strong></span>';
        $output .= '<span class="saswp-recipe-block-difficulty" style="width: 30%;"> '.esc_html__('Difficulty: ', 'saswp-for-wp'). ': <strong>'.esc_html($saswp_tiny_recipe['difficulty']).'</strong></span>';
        $output .= '</div>'; // saswp-r-course-section div end
        $output .= '</div>'; // saswp-recipe-block-heading div end
        $output .= '<div class="saswp-recipe-block-details"><div class="saswp-recipe-block-details-items">';
        $output .= '<div class="saswp-recipe-block-details-item">';
        $output .= '<p class="saswp-r-b-label">'.esc_html__('Servings', 'saswp-for-wp').'</p>';
        $output .= '<p class="saswp-r-b-unit">'.esc_html($saswp_tiny_recipe['servings']). esc_html__(' minutes', 'saswp-for-wp').' </p>';
        $output .= '</div>';
        $output .= '<div class="saswp-recipe-block-details-item">';
        $output .= '<p class="saswp-r-b-label">'.esc_html__('Preparing Time', 'saswp-for-wp').'</p>';
        $output .= '<p class="saswp-r-b-unit">'.esc_html($saswp_tiny_recipe['prepration_time']). esc_html__(' minutes', 'saswp-for-wp').' </p>';
        $output .= '</div>';
        $output .= '<div class="saswp-recipe-block-details-item">';
        $output .= '<p class="saswp-r-b-label">'.esc_html__('Cooking Time', 'saswp-for-wp').'</p>';
        $output .= '<p class="saswp-r-b-unit">'.esc_html($saswp_tiny_recipe['cooking_time']). esc_html__(' minutes', 'saswp-for-wp').' </p>';
        $output .= '</div>';
        $output .= '<div class="saswp-recipe-block-details-item">';
        $output .= '<p class="saswp-r-b-label">'.esc_html__('Calories', 'saswp-for-wp').'</p>';
        $output .= '<p class="saswp-r-b-unit">'.esc_html($saswp_tiny_recipe['calories']). esc_html__(' kcal', 'saswp-for-wp').' </p>';
        $output .= '</div>';
        $output .= '</div></div>'; // saswp-recipe-block-details div end
        $output .= '<div class="saswp-recipe-block-ingredients">';
        $output .= '<h4>'.esc_html__('INGREDIENTS', 'saswp-for-wp').'</h4>';
        if(isset($saswp_tiny_recipe['ingredients']) && isset($saswp_tiny_recipe['ingredients'][0])){
            $output .= '<ol class="saswp-dirction-ul">';
            foreach ($saswp_tiny_recipe['ingredients'] as $stci_key => $stci_value) {
                $output .= '<li class="saswp-r-b-direction-item"><p>'.esc_html($stci_value).'</p></li>';       
            }
            $output .= '</ol>';
        }
        $output .= '</div>'; // saswp-recipe-block-ingredients div end
        $output .= '<div class="saswp-recipe-block-direction">';
        $output .= '<h4>'.esc_html__('DIRECTION', 'saswp-for-wp').'</h4><ol class="saswp-dirction-ul">';
        if(isset($saswp_tiny_recipe['directions']) && isset($saswp_tiny_recipe['directions'][0])){
            foreach ($saswp_tiny_recipe['directions'] as $stcd_key => $stcd_value) {
                $output .= '<li class="saswp-r-b-direction-item"><p>'.esc_html($stcd_value).'</p></li>';       
            }
        }
        $output .= '</ol></div>'; // saswp-recipe-block-direction div end
        $output .= '<div class="saswp-recipe-block-direction">';
        $output .= '<h4>'.esc_html__('NOTES', 'saswp-for-wp').'</h4><ol class="saswp-dirction-ul">';
        if(isset($saswp_tiny_recipe['notes']) && isset($saswp_tiny_recipe['notes'][0])){
            foreach ($saswp_tiny_recipe['notes'] as $stcn_key => $stcn_value) {
                $output .= '<li class="saswp-r-b-direction-item"><p>'.esc_html($stcn_value).'</p></li>';       
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
    if(!empty($atts) && is_array($atts)){
        foreach ($atts as $atts_key => $atts_value) {
            $atts[$atts_key] = wp_kses_post($atts_value);
        }
    }
    return $atts;
}