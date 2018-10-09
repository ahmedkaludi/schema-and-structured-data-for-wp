<?php
/*
Plugin Name: Schema & Structured Data for WP
Description: Schema & Structured Data adds Google Rich Snippets markup according to Schema.org guidelines to structure your site for SEO. (AMP Compatible) 
Version: 1.0.3
Text Domain: schema-and-structured-data-for-wp
Author: Mohammed Kaludi, Ahmed Kaludi
Author URI: http://structured-data-for-wp.com/
Donate link: https://www.paypal.me/Kaludi/25
License: GPL2
*/
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

define('SASWP_VERSION', '1.0.3');
define('SASWP_DIR_NAME_FILE', __FILE__ );
define('SASWP_DIR_NAME', dirname( __FILE__ ));
define('SASWP_DIR_URI', plugin_dir_url(__FILE__));
// the name of the settings page for the license input to be displayed
if(! defined('SASWP_ITEM_FOLDER_NAME')){
    $folderName = basename(__DIR__);
    define( 'SASWP_ITEM_FOLDER_NAME', $folderName );
}
define('SASWP_PLUGIN_URL', plugin_dir_url( __FILE__ ));
// including the output file
require_once SASWP_DIR_NAME .'/output/function.php';
require_once SASWP_DIR_NAME .'/output/output.php';

if ( is_plugin_active('flexmls-idx/flexmls_connect.php')) {
require_once SASWP_DIR_NAME .'/output/flexmls.php';    
}

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
require_once SASWP_DIR_NAME.'/admin_section/plugin-installer/install.php';  
//Loading Metaboxes
require_once SASWP_DIR_NAME.'/view/help.php';  
require_once SASWP_DIR_NAME.'/view/schema_type.php';  
require_once SASWP_DIR_NAME.'/view/paywall.php';  
require_once SASWP_DIR_NAME.'/admin_section/add-schema/add_new.php';  


/**
 * set user defined message on plugin activate
 */
register_activation_hook( __FILE__, 'saswp_admin_notice_activation_hook' );
function saswp_admin_notice_activation_hook() {
    set_transient( 'saswp_admin_notice_transient', true, 5 );
    update_option( "saswp_activation_date", date("Y-m-d"));
}
add_action( 'admin_notices', 'saswp_admin_notice' );

function saswp_admin_notice(){
    ?>
       <div class="updated notice is-dismissible message notice notice-alt saswp-setup-notice saswp_hide">
         <p><span class="dashicons dashicons-thumbs-up"></span> <?php echo esc_html__('Thank you for using Schema & Structured Data For WP plugin!', 'schema-and-structured-data-for-wp') ?>
                <a href="<?php echo esc_url( admin_url( 'plugins.php?page=saswp-setup-wizard' ) ); ?>"> <?php echo esc_html__('Start Quick Setup', 'schema-and-structured-data-for-wp') ?></a>
            </p>
        </div>
     
        <div class="updated notice is-dismissible message notice notice-alt saswp-feedback-notice saswp_hide">
            <p><span class="dashicons dashicons-thumbs-up"></span> <?php echo esc_html__('You have been using the Schema & structured data for wp plugin for some time now, do you like it?, If so,', 'schema-and-structured-data-for-wp') ?>
                <a target="_blank" href="https://wordpress.org/plugins/schema-and-structured-data-for-wp/#reviews"> <?php echo esc_html__('please write us a review', 'schema-and-structured-data-for-wp') ?></a>&nbsp;&nbsp;&nbsp;&nbsp;<a  class="saswp-feedback-no-thanks button button-primary"><?php echo esc_html__('No Thanks', 'schema-and-structured-data-for-wp') ?></a>
            </p>
        </div>
    <?php
    /* Check transient, if available display notice */
    if( get_transient( 'saswp_admin_notice_transient' ) ){
        ?>
        <script type="text/javascript">  
             jQuery(document).ready( function($) {
                 $(".saswp-setup-notice").show(); 
             });
        </script> 
        <?php
        /* Delete transient, only display this notice once. */
        delete_transient( 'saswp_admin_notice_transient' );
    }    
    $current_screen = get_Current_screen();        
    $post_type = get_post_type();         
    $sd_data = get_option('sd_data');    
    if(($post_type == 'saswp' || $current_screen->id =='saswp_page_structured_data_options') && !isset($sd_data['sd_initial_wizard_status'])){
            ?>
        <script type="text/javascript">  
             jQuery(document).ready( function($) {
                 $(".saswp-setup-notice").show();
                 $(".saswp-start-quck-setup").hide();
             });
        </script>                
    <?php
     }     
     //Feedback notice
    $activation_date =  get_option("saswp_activation_date");  
    $next_days = strtotime("+7 day", strtotime($activation_date));
    $next_days = date('Y-m-d', $next_days);   
    $current_date = date("Y-m-d");
    
    if($next_days < $current_date){
      ?>
         <script type="text/javascript">  
             jQuery(document).ready( function($) {
                 $(".saswp-feedback-notice").show();                
             });
        </script> 
        <?php
    }  
}