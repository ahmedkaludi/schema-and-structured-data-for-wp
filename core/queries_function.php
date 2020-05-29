<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function saswp_post_exists($id){
    
    global $wpdb;
    $post_exists = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE id = '" . esc_sql($id) . "'", 'ARRAY_A');    
    return $post_exists;
    
}

function saswp_get_post_ids($type){
    
    global $wpdb;

    $response = array();

    $post_data = $wpdb->get_results("SELECT id FROM $wpdb->posts WHERE post_type = '".esc_sql($type)."'", 'ARRAY_A');    

    if(is_array($post_data) && $post_data){

        $response = array_column($post_data, 'id');
    }    
    return $response;
    
}