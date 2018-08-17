<?php
/*
Plugin Name: Schema and Structured Data for WP
Description: Add Structured data in your site. 
Version: 1.0
Text Domain: schema-and-structured-data-for-wp
Author: Mohammed Kaludi, AMPforWP Team
Author URI: http://ampforwp.com
Donate link: https://www.paypal.me/Kaludi/25
License: GPL2
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

define('SASWP_VERSION', '1.0');
define('SASWP_DIR_NAME', dirname( __FILE__ ));

if ( ! defined( 'SASWP_VERSION' ) ) {
  define( 'SASWP_VERSION', '1.0' );
}
// the name of the settings page for the license input to be displayed
if(! defined('SASWP_ITEM_FOLDER_NAME')){
    $folderName = basename(__DIR__);
    define( 'SASWP_ITEM_FOLDER_NAME', $folderName );
}
define('SASWP_PLUGIN_URL', plugin_dir_url( __FILE__ ));

// including the output file
require_once SASWP_DIR_NAME .'/output/function.php';
require_once SASWP_DIR_NAME .'/output/output.php';

// Non amp checker
if ( ! function_exists('saswp_non_amp') ){  
  function saswp_non_amp(){
    $non_amp = true;
    if(function_exists('ampforwp_is_amp_endpoint') && ampforwp_is_amp_endpoint() ) {
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
//Loading Metaboxes
require SASWP_DIR_NAME.'/metaboxes/help.php';  
