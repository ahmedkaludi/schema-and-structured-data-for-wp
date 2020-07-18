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

function saswp_wp_product_review_lite_rich_snippet(){

    global $post, $sd_data;

    $input1    = array();    

    if( is_object($post) && (isset($sd_data['saswp-wp-product-review']) && $sd_data['saswp-wp-product-review']) && class_exists('WPPR_Review_Model') ){        

        $review_object = new WPPR_Review_Model($post->ID);
        $input1        = $review_object->get_json_ld();        
    }

    return apply_filters('saswp_modify_wp_product_review_lite_default_schema', $input1);    

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

add_filter('saswp_modify_recipe_schema_output', 'saswp_recipress_json_ld',10,1);

function saswp_recipress_json_ld($input1){

    global $sd_data, $post;

    if( (isset($sd_data['saswp-recipress']) && $sd_data['saswp-recipress'] == 1) && function_exists('has_recipress_recipe') && has_recipress_recipe() && function_exists('recipress_recipe')){

        if(recipress_recipe('title')){
            $input1['name']          = recipress_recipe('title');
        }
        if(recipress_recipe('summary')){
            $input1['description']   = recipress_recipe('summary');    
        }                        
        if(recipress_recipe('cook_time','iso')){
            $input1['cookTime'] = recipress_recipe('cook_time','iso');
        }        
        if(recipress_recipe('prep_time', 'iso')){
            $input1['prepTime'] = recipress_recipe('prep_time', 'iso');
        }        
        if(recipress_recipe('ready_time','iso')){
            $input1['totalTime'] = recipress_recipe('ready_time','iso');
        }

        $cuisines = strip_tags( get_the_term_list( $post->ID, 'cuisine', '', ', ') );

        if($cuisines){
              $input1['recipeCuisine'] = $cuisines;
        }
        if(recipress_recipe('yield')){
            $input1['recipeYield'] = recipress_recipe('yield');
        }        
        $ingredients     = recipress_recipe('ingredients');
        $ingredients_arr = array();

        if($ingredients){
            foreach($ingredients as $ing){
                $ingredients_arr[] = $ing['ingredient'];
            }
            $input1['recipeIngredient'] = $ingredients_arr;
        }

        $instructions     = recipress_recipe('instructions');
        
        $instructions_arr = array();

        if($instructions){
            foreach($instructions as $ing){
                $instructions_arr[] = $ing['description'];
            }
            $input1['recipeInstructions'] = $instructions_arr;
        }
        
        if(saswp_get_the_categories()){
            $input1['recipeCategory'] = saswp_get_the_categories();    
        }                
        
    }
   
    return $input1;
}

function saswp_wp_tasty_recipe_json_ld(){

    if ( ! is_singular() ) {
        return array();
    }
    global $sd_data;
    $resposne = array();

    if( isset($sd_data['saswp-wptastyrecipe']) && $sd_data['saswp-wptastyrecipe'] == 1 && class_exists('Tasty_Recipes') && class_exists('Tasty_Recipes\Distribution_Metadata') ){

        $recipes = Tasty_Recipes::get_recipes_for_post(
            get_queried_object()->ID,
            array(
                'disable-json-ld' => false,
            )
        );
        if ( empty( $recipes ) ) {
            return array();
        }
                    
            foreach ( $recipes as $recipe ) {
                $resposne[] = Tasty_Recipes\Distribution_Metadata::get_enriched_google_schema_for_recipe( $recipe, get_queried_object() );                
            }
                        
    }
    
    return $resposne;

}

