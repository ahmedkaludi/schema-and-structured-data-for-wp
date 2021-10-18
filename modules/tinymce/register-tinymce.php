<?php

add_action( 'admin_enqueue_scripts', 'saswp_enqueue_style_tinymce_css' );

function saswp_enqueue_style_tinymce_css(){

   wp_enqueue_style( 'saswp-tinyme-css', SASWP_PLUGIN_URL . 'modules/tinymce/js/tiny-mce.css', false , SASWP_VERSION );			
}

add_action( 'init', 'saswp_tinymce_buttons_init' );

function saswp_tinymce_buttons_init() {

   // Check if the logged in WordPress User can edit Posts or Pages
   // If not, don't register our TinyMCE plugin
     
   if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
      return;
   }

   // Check if the logged in WordPress User has the Visual Editor enabled
   // If not, don't register our TinyMCE plugin
   if ( get_user_option( 'rich_editing' ) !== 'true' ) {
      return;
   }
    // add new buttons
    add_filter( 'mce_buttons', 'saswp_tinymce_register_buttons' );
    // Load the TinyMCE plugin : editor_plugin.js (wp2.5)
    add_filter( 'mce_external_plugins', 'saswp_register_tinymce_javascript' );   
}

function saswp_tinymce_register_buttons( $buttons ) {
   array_push( $buttons, 'separator', 'saswp_tinymce_dropdown' );
   return $buttons;
}
 
function saswp_register_tinymce_javascript( $plugin_array ) {
   $plugin_array['saswp_tinymce_dropdown'] = SASWP_PLUGIN_URL.'modules/tinymce/js/tiny-mce.js';
   return $plugin_array;
}