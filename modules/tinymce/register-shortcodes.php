<?php

add_shortcode( 'saswp_tiny_howto', 'saswp_tiny_howto_render' );

function saswp_tiny_howto_render( $atts, $content = null ){

    global $saswp_tiny_howto;

    $output = '';

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
            $output .= '<p>'.html_entity_decode(esc_attr($saswp_tiny_howto['description'])).'</p>';
        }
        
        if( !empty($saswp_tiny_howto['elements']) ){

            $output .= '<div class="saswp-how-to-block-steps">';
            $output .= '<ol>';

            foreach ($saswp_tiny_howto['elements'] as $value) {
                
                if($value['step_title'] || $value['step_description']){
                    
                    $output .= '<li>'; 
                    $output .= '<strong class="saswp-how-to-step-name">'. html_entity_decode(esc_attr($value['step_title'])).'</strong>';
                    $output .= '<p class="saswp-how-to-step-text">'.html_entity_decode(esc_textarea($value['step_description']));

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

    $saswp_tiny_multi_faq = shortcode_atts(
        [
            'css_class' => '',
            'count'     => '1',
            'html'      => true,
            'elements'  => [],
        ], $atts );

    foreach ( $atts as $key => $merged_att ) {
        if ( strpos( $key, 'headline' ) !== false || strpos( $key, 'question' ) !== false || strpos( $key,
                'answer' ) !== false || strpos( $key, 'image' ) !== false ) {
            $saswp_tiny_multi_faq['elements'][ explode( '-', $key )[1] ][ substr( $key, 0, strpos( $key, '-' ) ) ] = $merged_att;
        }
    }

    
    if($saswp_tiny_multi_faq['html'] == 'true'){

        if( !empty($saswp_tiny_multi_faq['elements']) ){

            foreach ($saswp_tiny_multi_faq['elements'] as $value) {
                
                $output .= '<section>';
                $output .= '<summary>';
                $output .= '<'.esc_attr($value['headline']).'>';
                $output .=  esc_html($value['question']);
                $output .= '</'.esc_attr($value['headline']).'>';
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

        if($saswp_tiny_faq['html'] == 'true'){                        

            $output .= '<summary>';
            $output .= '<'.esc_attr($saswp_tiny_faq['headline']).'>';
            $output .=  esc_html($saswp_tiny_faq['question']);
            $output .= '</'.esc_attr($saswp_tiny_faq['headline']).'>';
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