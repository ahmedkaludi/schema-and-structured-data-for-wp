<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function saswp_post_exists($id){
    
    global $wpdb;
    $post_exists = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE id = '" . $id . "'", 'ARRAY_A');    
    return $post_exists;
    
}

function saswp_get_reviews_list($type){
    
    switch ($type) {
        case $value:


            break;

        default:
            break;
    }
    
    global $wpdb;
    $post_exists = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE id = '" . $id . "'", 'ARRAY_A');    
    return $post_exists;
    
}