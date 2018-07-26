<?php

function saswp_pwa_admin_link($tab = '', $args = array()){
	//return add_query_arg(array('record_id'=>$record_id,'mode'=>'view_record'),admin_url('admin.php?page=storage'));

	$page = 'saswp-options';// Menu Slug name "While change please, Change in ampforwp_pwa_add_menu_links also"

	if ( ! is_multisite() ) {
		$link = admin_url( 'admin.php?page=' . $page );
	}
	else {
		$link = network_admin_url( 'admin.php?page=' . $page );
	}

	if ( $tab ) {
		$link .= '&tab=' . $tab;
	}

	if ( $args ) {
		foreach ( $args as $arg => $value ) {
			$link .= '&' . $arg . '=' . urlencode( $value );
		}
	}

	return esc_url($link);
}
function saswp_get_tab( $default = '', $available = array() ) {

	$tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $default;

	if ( ! in_array( $tab, $available ) ) {
		$tab = $default;
	}

	return $tab;
}

function saswp_defaultSettings(){
//	$defaults = array(
//		'app_blog_name'			=> get_bloginfo( 'name' ),
//		'app_blog_short_name'	=> get_bloginfo( 'name' ),
//		'description'		=> get_bloginfo( 'description' ),
//		'icon'				=> SASWP_PLUGIN_URL . 'images/logo.png',
//		'splash_icon'		=> SASWP_PLUGIN_URL . 'images/logo-512x512.png',
//		'background_color' 	=> '#D5E0EB',
//		'theme_color' 		=> '#D5E0EB',
//		'start_url' 		=> 0,
//		'start_url_amp'		=> 0,
//		'offline_page' 		=> 0,
//		'404_page' 			=> 0,
//		'orientation'		=> 'portrait',
//	);
	//$settings = get_option( 'sd_data', $defaults );
        $settings = get_option( 'sd_data');
        //print_r($settings);die;
        //print_r($settings['sd-for-wordpress']);die;
	return $settings;
}