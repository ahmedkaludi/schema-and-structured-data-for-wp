<?php

add_filter( 'amp_init', 'saswp_structured_data' );

function saswp_structured_data()
{
add_action( 'amp_post_template_head' , 'saswp_data_generator' );
remove_action( 'amp_post_template_head', 'amp_post_template_add_schemaorg_metadata',10,1);
}

add_action('wp_head', 'saswp_data_generator');

function saswp_data_generator() {
    global $sd_data;

    // will store each block
    $output                   = [];

    // get all schemas outputs
    $contact_page_output      = saswp_contact_page_output();
    $about_page_output        = saswp_about_page_output();
    $author_output            = saswp_author_output();
    $archive_output           = saswp_archive_output();
    $kb_website_output        = saswp_kb_website_output();
    $schema_breadcrumb_output = saswp_schema_breadcrumb_output($sd_data);
    $schema_output            = saswp_schema_output();
    $kb_schema_output         = saswp_kb_schema_output();

    if( (  1 == $sd_data['saswp-for-wordpress'] && saswp_non_amp() ) || ( 1 == $sd_data['saswp-for-amp'] && !saswp_non_amp() ) ) {

            if(!empty($contact_page_output)) {
                array_push($output, $contact_page_output);
            }
            if(!empty($about_page_output)) {
                array_push($output, $about_page_output);
            }
            if(!empty($author_output)) {
                array_push($output, $author_output);
            }
            if(!empty($archive_output)) {
                array_push($output, $archive_output);
            }
            if(!empty($kb_website_output)) {
                array_push($output, $kb_website_output);
            }
            if(!empty($schema_breadcrumb_output)) {
                array_push($output, $schema_breadcrumb_output);
            }
            if(!empty($schema_output)) {
                array_push($output, $schema_output);
            }
            if(!empty($kb_schema_output)) {
                array_push($output, $schema_output);
            }
    }

    // output version
    echo '<!-- Schema And Structured Data For WP v'.SASWP_VERSION.' - -->';

    // create a seperate <script> block for each structured data ld+json output
    foreach ($output as $key => $block) {
        if (!empty($block)) {
            echo "\n";
            echo '<script type="application/ld+json">';
            echo "\n";
            echo html_entity_decode(esc_html($block));
            echo "\n";
            echo '</script>';
            echo "\n\n";
        }
    }


}

add_filter('the_content', 'saswp_paywall_data_for_login');

function saswp_paywall_data_for_login($content){
    if( saswp_non_amp() ){
           return $content;
    }
    remove_filter('the_content', 'MeprAppCtrl::page_route', 60);

    $schemaConditionals = saswp_get_all_schema_posts();
    if(!$schemaConditionals){
        return $content;
    } else {
        $schema_options = $schemaConditionals['schema_options'];
        if($schema_options['paywall_class_name']!=''){
            $className = $schema_options['paywall_class_name'];
        }
        if(strpos($content, '<!--more-->')!==false && !is_user_logged_in()){
            global $wp;
            $redirect =  home_url( $wp->request );
            $breakedContent = explode("<!--more-->", $content);
            $content = $breakedContent[0].'<a href="'.wp_login_url( $redirect ) .'">'.esc_html__( 'Login', 'schema-and-structured-data-for-wp' ).'</a>';
        }elseif(strpos($content, '<!--more-->')!==false && is_user_logged_in()){
            global $wp;
            $redirect =  home_url( $wp->request );
            $breakedContent = explode("<!--more-->", $content);
            $content = $breakedContent[0].'<div class="'.$className.'">'.$breakedContent[1].'</div>';
        }
    }
    return $content;
}

add_filter('memberpress_form_update', 'saswp_memberpress_form_update');

function saswp_memberpress_form_update($form){
    if( !saswp_non_amp() ){
        add_action('amp_post_template_css',function(){
            echo '.amp-mem-login{ background-color: #fef5c4;padding: 13px 30px 9px 30px; }';
        }, 11);
        global $wp;
        $redirect =  home_url( $wp->request );
        $form = '<a class="amp-mem-login" href="'.wp_login_url( $redirect ) .'">'.esc_html__( 'Login', 'schema-and-structured-data-for-wp' ).'</a>';
    }
    return $form;
}