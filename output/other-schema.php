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

function saswp_schema_for_faqs_schema(){

    global $post, $sd_data;

    $input1    = array();
    $saswp_faq = array();

    if(isset($sd_data['saswp-schemaforfaqs']) && $sd_data['saswp-schemaforfaqs'] == 1 && class_exists('Schema_Faqs') && !saswp_non_amp()){

        $post_meta = get_post_meta($post->ID, 'schema_faqs_ques_ans_data', true);
        $post_meta = str_replace("\'","'",$post_meta);

        if(!empty($post_meta)){

            $data_arr = json_decode($post_meta, true);

            foreach($data_arr as $value){
                
                if(isset($value['question'])){
    
                    $saswp_faq[] =  array(
                        '@type'     => 'Question',
                        'name'      => stripslashes($value['question']),
                        'acceptedAnswer'=> array(
                            '@type' => 'Answer',
                            'text'  => stripslashes($value['answer']),
                        )
                    );

                }

            }

            if(!empty($saswp_faq)){

                $input1['@context']   = saswp_context_url();
                $input1['@type']      = 'FAQPage';
                $input1['mainEntity'] = $saswp_faq;

            }            

        }

    }

    return $input1;
}

function saswp_taqyeem_review_rich_snippet(){

    global $post, $sd_data;

    $input1    = array();    

    if(isset($sd_data['saswp-taqyeem']) && $sd_data['saswp-taqyeem'] == 1 && function_exists('taqyeem_review_get_rich_snippet')){

        $get_meta = get_post_custom( $post->ID );
        
        if( !empty( $get_meta['taq_review_position'][0] ) ){
            $input1 = taqyeem_review_get_rich_snippet();
        }
        
    }

    return apply_filters('saswp_modify_taqeem_default_schema', $input1);    

}

add_action( 'amp_post_template_footer', 'saswp_wordlift_amp_schema' );

function saswp_wordlift_amp_schema( $amp_template ) {

    global $sd_data;

    $metadata = $amp_template->get( 'metadata' );
    
    if ( empty( $metadata ) ) {
       return;
    }

    if(isset($sd_data['saswp-wordlift']) && $sd_data['saswp-wordlift'] == 1 && class_exists('Wordlift\Jsonld\Jsonld_Adapter')){

        ?>
        <script type="application/ld+json" id="wl-jsonld"><?php echo wp_json_encode( $metadata,JSON_UNESCAPED_UNICODE); ?></script>
        <?php

    } 
}

add_filter('saswp_modify_recipe_schema_output', 'saswp_wp_recipe_maker_json_ld',10,1);

function saswp_wp_recipe_maker_json_ld($input1){

    global $sd_data;

    $recipe_json = array();

    if(isset($sd_data['saswp-wp-recipe-maker']) && $sd_data['saswp-wp-recipe-maker'] == 1){                            
        
        $recipe_ids = saswp_get_ids_from_content_by_type('wp_recipe_maker');

        if($recipe_ids){

            foreach($recipe_ids as $recipe){

                if(class_exists('WPRM_Recipe_Manager')){

                    $recipe_arr    = WPRM_Recipe_Manager::get_recipe( $recipe );

                    if($recipe_arr){
                        $recipe_json[] = saswp_wp_recipe_schema_json($recipe_arr);                                            
                    }
                    
                }

            } 
            
            if($recipe_json){
                $input1 = $recipe_json[0];
            }

         }

    }

    return $input1;

}