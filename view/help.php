<?php
/**
 * Help page
 *
 * @author   Magazine3
 * @category Admin
 * @path     view/help
 * @version 1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function saswp_help_meta_box_cb(){

    echo '<a href="'.esc_url(admin_url('admin.php?page=structured_data_options&tab=support')).'">'.esc_html__('Need Help', 'schema-and-structured-data-for-wp').'</a>';   
}

/**
* Remove Add new menu
**/
function saswp_disable_new_posts() {
	// Hide sidebar link
	global $submenu;
	unset($submenu['edit.php?post_type=saswp'][10]);
        
}
add_action('admin_menu', 'saswp_disable_new_posts'); 	