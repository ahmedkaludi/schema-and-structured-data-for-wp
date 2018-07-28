<?php
function saswp_data_generator($input) {
    
	$output =  '';
	global $sd_data;	        
	if( (  1 == $sd_data['sd-for-wordpress'] && saswp_non_amp() ) || ( 1 == $sd_data['sd-for-ampforwp'] && !saswp_non_amp() ) ) {
		if ($input) {
			$output .= "\n\n";
			$output .= '<!-- This site is optimized with the Structured data  plugin v'.SASWP_VERSION.' - -->';
			$output .= "\n";
			$output .= '<script type="application/ld+json">' . json_encode($input) . '</script>';
			$output .= "\n\n";
		}
	}
	return $output;
}
add_action('wp', function(){
	if( saswp_non_amp() ){
		return;
	}
	remove_filter( 'the_content', 'prefix_insert_post_ads' );
});

add_filter('the_content', 'saswp_paywall_data_for_login');
function saswp_paywall_data_for_login($content){
	if( saswp_non_amp() ){
		return $content;
	}
	remove_filter('the_content', 'MeprAppCtrl::page_route', 60);
	
	$schemaConditionals = saswp_get_all_schema_posts();
	if(!$schemaConditionals){
		return $content;
	}else{
		$schema_options = $schemaConditionals['schema_options'];
		$schema_type = $schemaConditionals['schema_type'];
		if($schema_options['paywall_class_name']!=''){
			$className = $schema_options['paywall_class_name'];
		}
		if(strpos($content, '<!--more-->')!==false && !is_user_logged_in()){
			global $wp;
			$redirect =  home_url( $wp->request );
			$breakedContent = explode("<!--more-->", $content);
			$content = $breakedContent[0].'<a href="'.wp_login_url( $redirect ) .'">Login</a>';
		}elseif(strpos($content, '<!--more-->')!==false && is_user_logged_in()){
			global $wp;
			$redirect =  home_url( $wp->request );
			$breakedContent = explode("<!--more-->", $content);
			$content = $breakedContent[0].'<div class="'.$className.'">'.$breakedContent[1].'</div>';
		}
	}
	return $content;
}
add_filter('memberpress_form_update', function($form){
	if( !saswp_non_amp() ){
		add_action('amp_post_template_css',function(){
			echo '.amp-mem-login{background-color: #fef5c4;padding: 13px 30px 9px 30px;}';
		},11); 
		global $wp;
		$redirect =  home_url( $wp->request );
		$form = '<a class="amp-mem-login" href="'.wp_login_url( $redirect ) .'">Login</a>';
	}
	return $form;
});