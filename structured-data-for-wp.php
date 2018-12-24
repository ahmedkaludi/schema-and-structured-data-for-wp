<?php
/*
Plugin Name: Schema & Structured Data for WP
Description: Schema & Structured Data adds Google Rich Snippets markup according to Schema.org guidelines to structure your site for SEO. (AMP Compatible) 
Version: 1.0.6.1
Text Domain: schema-and-structured-data-for-wp
Domain Path: /languages
Author: Mohammed Kaludi, Ahmed Kaludi
Author URI: http://structured-data-for-wp.com/
Donate link: https://www.paypal.me/Kaludi/25
License: GPL2
*/
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

define('SASWP_VERSION', '1.0.6.1');
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

if ( ! function_exists( 'is_plugin_active' ) )
     require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

if ( is_plugin_active('flexmls-idx/flexmls_connect.php') && class_exists('flexmlsConnectPageCore')) {
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
require_once SASWP_DIR_NAME.'/view/post_specific.php';  
require_once SASWP_DIR_NAME.'/view/review.php';  
require_once SASWP_DIR_NAME.'/output/review-output.php'; 
require_once SASWP_DIR_NAME.'/output/service.php'; 


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
    $nonce = wp_create_nonce( 'saswp_install_wizard_nonce' );  
    ?>
       <div class="updated notice is-dismissible message notice notice-alt saswp-setup-notice saswp_hide">
         <p><span class="dashicons dashicons-thumbs-up"></span> <?php echo esc_html__('Thank you for using Schema & Structured Data For WP plugin!', 'schema-and-structured-data-for-wp') ?>
                <a href="<?php echo esc_url(admin_url( 'plugins.php?page=saswp-setup-wizard' ).'&_saswp_nonce='.$nonce); ?>"> <?php echo esc_html__('Start Quick Setup', 'schema-and-structured-data-for-wp') ?></a>
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

add_filter('plugin_row_meta' , 'saswp_add_plugin_meta_links', 10, 2);

function saswp_add_plugin_meta_links($meta_fields, $file) {
    if ( plugin_basename(__FILE__) == $file ) {
      $plugin_url = "https://wordpress.org/support/plugin/schema-and-structured-data-for-wp";      
      $meta_fields[] = "<a href='" . esc_url($plugin_url) . "' target='_blank'>" . esc_html__('Support Forum', 'ads-for-wp') . "</a>";
      $meta_fields[] = "<a href='" . esc_url($plugin_url) . "/reviews#new-post' target='_blank' title='" . esc_html__('Rate', 'ads-for-wp') . "'>
            <i class='saswp-wdi-rate-stars'>"
        . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
        . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
        . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
        . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
        . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
        . "</i></a>";      
      echo "<style>"
        . ".saswp-wdi-rate-stars{display:inline-block;color:#ffb900;position:relative;top:3px;}"
        . ".saswp-wdi-rate-stars svg{fill:#ffb900;}"
        . ".saswp-wdi-rate-stars svg:hover{fill:#ffb900}"
        . ".saswp-wdi-rate-stars svg:hover ~ svg{fill:none;}"
        . "</style>";
    }

    return $meta_fields;
  }