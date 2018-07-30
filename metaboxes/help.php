<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

add_action( 'add_meta_boxes', 'saswp_help_meta_box' );
function saswp_help_meta_box()
{
    add_meta_box( 'my-meta-box-id', 'Help', 'saswp_help_meta_box_cb', 'structured-data-wp', 'advanced', 'low' );
}

function saswp_help_meta_box_cb()
{
    echo 'Need Help';   
}