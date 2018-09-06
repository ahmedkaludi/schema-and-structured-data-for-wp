<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
add_action( 'add_meta_boxes', 'saswp_help_meta_box' );
function saswp_help_meta_box()
{
    add_meta_box( 'saswp_help_meta_box_id',
        esc_html__('Help', 'schema-and-structured-data-for-wp'),
            'saswp_help_meta_box_cb',
            'saswp',
            'advanced',
            'low'
    );
}

function saswp_help_meta_box_cb()
{
    echo '<a href="admin.php?page=structured_data_options&tab=help">'.esc_html__('Need Help', 'schema-and-structured-data-for-wp').'</a>';
}