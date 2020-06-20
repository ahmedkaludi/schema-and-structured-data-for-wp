<?php
/*
Plugin Name: Schema & Structured Data for WP & AMP
Description: Schema & Structured Data adds Google Rich Snippets markup according to Schema.org guidelines to structure your site for SEO. (AMP Compatible) 
Version: 1.9.41.3
Text Domain: schema-and-structured-data-for-wp
Domain Path: /languages
Author: Magazine3
Author URI: http://structured-data-for-wp.com/
Donate link: https://www.paypal.me/Kaludi/25
License: GPL2
*/
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

define('SASWP_VERSION', '1.9.41.3');
define('SASWP_DIR_NAME_FILE', __FILE__ );
define('SASWP_DIR_NAME', dirname( __FILE__ ));
define('SASWP_DIR_URI', plugin_dir_url(__FILE__));
define('SASWP_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ));
// the name of the settings page for the license input to be displayed
if(! defined('SASWP_ITEM_FOLDER_NAME')){
    $folderName = basename(__DIR__);
    define( 'SASWP_ITEM_FOLDER_NAME', $folderName );
}
define('SASWP_PLUGIN_URL', plugin_dir_url( __FILE__ ));
define('SASWP_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('SASWP_EDD_STORE_URL', 'http://structured-data-for-wp.com/');

//define('SASWP_ENVIRONMENT', 'development');
define('SASWP_ENVIRONMENT', 'production');
// including the output file
require_once SASWP_DIR_NAME .'/output/function.php';
require_once SASWP_DIR_NAME .'/output/output.php';
require_once SASWP_DIR_NAME .'/output/markup.php';
require_once SASWP_DIR_NAME .'/output/other-schema.php';
require_once SASWP_DIR_NAME .'/output/gutenberg.php';
require_once SASWP_DIR_NAME .'/output/elementor.php';
require_once SASWP_DIR_NAME .'/output/divi-builder.php';

if ( ! function_exists( 'is_plugin_active' ) )
     require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

if ( is_plugin_active('flexmls-idx/flexmls_connect.php') && class_exists('flexmlsConnectPageCore')) {
     require_once SASWP_DIR_NAME .'/output/flexmls.php';    
}

// Non amp checker
if ( ! function_exists('saswp_non_amp') ){
    
  function saswp_non_amp(){
      
    $non_amp = true;
    
    if( function_exists('ampforwp_is_amp_endpoint') && @ampforwp_is_amp_endpoint() ) {                
        $non_amp = false;                       
    }     
    if(function_exists('is_amp_endpoint') && @is_amp_endpoint() ){
        $non_amp = false;           
    }
    if(function_exists('is_better_amp') && @is_better_amp()){       
        $non_amp = false;           
    }
    if(function_exists('is_amp_wp') && @is_amp_wp()){       
        $non_amp = false;           
    }
    
    return $non_amp;
    
  }
  
}
// Schema App end here
require_once SASWP_DIR_NAME.'/admin_section/structure_admin.php';
require_once SASWP_DIR_NAME.'/admin_section/settings.php';
require_once SASWP_DIR_NAME.'/admin_section/common-function.php';
require_once SASWP_DIR_NAME.'/admin_section/fields-generator.php';  
require_once SASWP_DIR_NAME.'/admin_section/newsletter.php';  
require_once SASWP_DIR_NAME.'/admin_section/plugin-installer/install.php';  
//Loading View files
require_once SASWP_DIR_NAME.'/view/help.php';  
require_once SASWP_DIR_NAME.'/view/schema_type.php';  
require_once SASWP_DIR_NAME.'/view/paywall.php';  
require_once SASWP_DIR_NAME.'/admin_section/add-schema/add_new.php';  
require_once SASWP_DIR_NAME.'/view/post_specific.php';  
require_once SASWP_DIR_NAME.'/modules/rating-box/backend.php';  
require_once SASWP_DIR_NAME.'/modules/rating-box/frontend.php'; 
require_once SASWP_DIR_NAME.'/output/service.php'; 
require_once SASWP_DIR_NAME.'/output/compatibility.php'; 
//Loading Reviews files
require_once SASWP_DIR_NAME.'/modules/divi-builder/extension.php'; 
require_once SASWP_DIR_NAME.'/modules/reviews/reviews_admin.php'; 
require_once SASWP_DIR_NAME.'/modules/reviews/comments.php'; 
require_once SASWP_DIR_NAME.'/modules/reviews/reviews_setup.php';
require_once SASWP_DIR_NAME.'/modules/reviews/reviews_service.php';
require_once SASWP_DIR_NAME.'/modules/reviews/reviews_widget.php';
require_once SASWP_DIR_NAME.'/modules/reviews/reviews_collection.php';
require_once SASWP_DIR_NAME.'/modules/reviews/reviews_form.php';
require_once SASWP_DIR_NAME.'/core/array-list/schema-properties.php';
require_once SASWP_DIR_NAME.'/core/global.php';
require_once SASWP_DIR_NAME.'/core/queries_function.php';
//Module files load
require_once SASWP_DIR_NAME.'/modules/gutenberg/includes/class-gutenberg.php';
require_once SASWP_DIR_NAME.'/modules/elementor/elementor-loader.php';

//Loading Third party files
require_once SASWP_DIR_NAME.'/core/3rd-party/aqua_resizer.php';
/**
 * set user defined message on plugin activate
 */
register_activation_hook( __FILE__, 'saswp_on_activation' );
register_uninstall_hook( __FILE__, 'saswp_on_uninstall' );

add_filter('plugin_row_meta' , 'saswp_add_plugin_meta_links', 10, 2);

function saswp_add_plugin_meta_links($meta_fields, $file) {
    
    if ( SASWP_PLUGIN_BASENAME == $file ) {
                       
      $forum_url  = "https://structured-data-for-wp.com/contact-us/";
      
      $meta_fields[] = "<a href='" . esc_url($forum_url) . "' target='_blank'>" . esc_html__('Technical Support', 'schema-and-structured-data-for-wp') . "</a>";                
    }

    return $meta_fields;
    
  }
  
if( ! class_exists( 'SASWP_Plugin_Usage_Tracker') ) {
  require_once SASWP_DIR_NAME. '/admin_section/tracking/class-saswp-plugin-usage-tracker.php';
}
if( ! function_exists( 'saswp_start_plugin_tracking' ) ) {
  function saswp_start_plugin_tracking() {
    global $saswp_wisdom;                  
    $saswp_wisdom = new SASWP_Plugin_Usage_Tracker(
      __FILE__,
      'http://data.ampforwp.com/ssdw',
      array('sd_data'),
      true,
      true,
      0
    );
  }
  
  saswp_start_plugin_tracking();
}