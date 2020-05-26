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

    if(isset($sd_data['saswp-schemaforfaqs']) && $sd_data['saswp-schemaforfaqs'] == 1 && class_exists('Schema_Faqs')){

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

        $input1 = taqyeem_review_get_rich_snippet();

    }

    return apply_filters('saswp_modify_taqeem_default_schema', $input1);    

}