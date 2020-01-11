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