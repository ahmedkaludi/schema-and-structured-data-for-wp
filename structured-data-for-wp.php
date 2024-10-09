<?php
/*
Plugin Name: Schema & Structured Data for WP & AMP
Description: Schema & Structured Data adds Google Rich Snippets markup according to Schema.org guidelines to structure your site for SEO. (AMP Compatible) 
Version: 1.37
Text Domain: schema-and-structured-data-for-wp
Domain Path: /languages
Author: Magazine3
Author URI: http://structured-data-for-wp.com/
Donate link: https://www.paypal.me/Kaludi/25
License: GPL2
*/
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'SASWP_VERSION', '1.37' );
define( 'SASWP_DIR_NAME_FILE', __FILE__ );
define( 'SASWP_DIR_NAME', dirname( __FILE__ ) );
define( 'SASWP_DIR_URI', plugin_dir_url( __FILE__ ) );
define( 'SASWP_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
// the name of the settings page for the license input to be displayed
if ( ! defined( 'SASWP_ITEM_FOLDER_NAME' ) ) {
    $folderName = basename( __DIR__ );
    define( 'SASWP_ITEM_FOLDER_NAME', $folderName );
}
define( 'SASWP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SASWP_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'SASWP_EDD_STORE_URL', 'http://structured-data-for-wp.com/' );

// define( 'SASWP_ENVIRONMENT', 'development' );
define( 'SASWP_ENVIRONMENT', 'production' );
// including the output file
require_once SASWP_DIR_NAME .'/output/function.php';
require_once SASWP_DIR_NAME .'/output/output.php';
require_once SASWP_DIR_NAME .'/output/markup.php';
require_once SASWP_DIR_NAME .'/output/other-schema.php';
require_once SASWP_DIR_NAME .'/output/gutenberg.php';
require_once SASWP_DIR_NAME .'/output/tinymce.php';
require_once SASWP_DIR_NAME .'/output/elementor.php';
require_once SASWP_DIR_NAME .'/output/divi-builder.php';

if ( ! function_exists( 'is_plugin_active' ) )
     require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

if ( is_plugin_active('flexmls-idx/flexmls_connect.php') && class_exists( 'flexmlsConnectPageCore' )) {
     require_once SASWP_DIR_NAME .'/output/class-saswp-flexmls-list.php';    
}

// Non amp checker
if ( ! function_exists('saswp_non_amp') ) {
    
  function saswp_non_amp() {
      
    $non_amp = true;
    
    if( function_exists( 'ampforwp_is_amp_endpoint' ) && ampforwp_is_amp_endpoint() ) {
        $non_amp = false;
    }     
    if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ){
        $non_amp = false;
    }
    if ( function_exists( 'is_better_amp' ) && is_better_amp() ) {
        $non_amp = false;
    }
    if ( function_exists( 'is_amp_wp' ) && is_amp_wp() ) {       
        $non_amp = false;
    }
    
    return $non_amp;
    
  }
  
}

// Schema App end here
require_once SASWP_DIR_NAME.'/admin_section/structure-admin.php';
require_once SASWP_DIR_NAME.'/admin_section/settings.php';
require_once SASWP_DIR_NAME.'/admin_section/common-function.php';
require_once SASWP_DIR_NAME.'/output/class-saswp-location-widget.php';
require_once SASWP_DIR_NAME.'/admin_section/class-saswp-fields-generator.php';  
require_once SASWP_DIR_NAME.'/admin_section/class-saswp-newsletter-popup.php';  
require_once SASWP_DIR_NAME.'/admin_section/plugin-installer/install.php';
require_once SASWP_DIR_NAME.'/admin_section/tracking/make-better-helper.php';  
//Loading View files

require_once SASWP_DIR_NAME.'/view/help.php';  
require_once SASWP_DIR_NAME.'/view/schema-type.php';  
require_once SASWP_DIR_NAME.'/view/paywall.php';  
require_once SASWP_DIR_NAME.'/admin_section/add-schema/add-new.php';  
require_once SASWP_DIR_NAME.'/view/class-saswp-post-specific.php';  
require_once SASWP_DIR_NAME.'/modules/rating-box/class-saswp-rating-box-backend.php';  
require_once SASWP_DIR_NAME.'/modules/rating-box/class-saswp-rating-box-frontend.php'; 
require_once SASWP_DIR_NAME.'/output/class-saswp-output-service.php'; 
require_once SASWP_DIR_NAME.'/output/class-saswp-output-compatibility.php'; 

//Loading api files

require_once SASWP_PLUGIN_DIR_PATH.'output/rest-api/class-saswp-output-rest-api.php';
require_once SASWP_PLUGIN_DIR_PATH.'output/rest-api/wpgraphql.php';

//Loading Reviews files
require_once SASWP_DIR_NAME.'/modules/divi-builder/extension.php'; 
require_once SASWP_DIR_NAME.'/modules/reviews/class-saswp-reviews-admin.php'; 
require_once SASWP_DIR_NAME.'/modules/reviews/comments.php'; 
require_once SASWP_DIR_NAME.'/modules/reviews/reviews-setup.php';
require_once SASWP_DIR_NAME.'/modules/reviews/class-saswp-reviews-service.php';
require_once SASWP_DIR_NAME.'/modules/reviews/class-saswp-reviews-widget.php';
require_once SASWP_DIR_NAME.'/modules/reviews/class-saswp-reviews-collection.php';
require_once SASWP_DIR_NAME.'/modules/reviews/class-saswp-reviews-form.php';
require_once SASWP_DIR_NAME.'/modules/tinymce/register-tinymce.php';
require_once SASWP_DIR_NAME.'/modules/tinymce/register-shortcodes.php';
require_once SASWP_DIR_NAME.'/core/array-list/schema-properties.php';
require_once SASWP_DIR_NAME.'/core/global.php';
//Module files load
require_once SASWP_DIR_NAME.'/modules/gutenberg/includes/class-saswp-gutenberg.php';
require_once SASWP_DIR_NAME.'/modules/elementor/class-saswp-elementor-loader.php';

//Loading Third party files
require_once SASWP_DIR_NAME.'/core/3rd-party/class-saswp-aq-resize.php';
require_once SASWP_DIR_NAME.'/core/3rd-party/class-saswp-youtube.php';
/**
 * set user defined message on plugin activate
 */
register_activation_hook( __FILE__, 'saswp_on_activation' );
register_uninstall_hook( __FILE__, 'saswp_on_uninstall' );

add_filter( 'plugin_row_meta' , 'saswp_add_plugin_meta_links', 10, 2 );

function saswp_add_plugin_meta_links( $meta_fields, $file ) {
    
    if ( SASWP_PLUGIN_BASENAME == $file ) {
                       
      $forum_url  = "https://structured-data-for-wp.com/contact-us/";
      
      $meta_fields[] = "<a href='" . esc_url( $forum_url ) . "' target='_blank'>" . esc_html__( 'Technical Support' , 'schema-and-structured-data-for-wp' ) . "</a>";
     
    }

    return $meta_fields;
    
  }