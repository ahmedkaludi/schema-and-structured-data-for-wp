<?php
/*
Plugin Name: Schema & Structured Data for WP
Description: Schema & Structured Data adds Google Rich Snippets markup according to Schema.org guidelines to structure your site for SEO. (AMP Compatible) 
Version: 1.9.15
Text Domain: schema-and-structured-data-for-wp
Domain Path: /languages
Author: Magazine3
Author URI: http://structured-data-for-wp.com/
Donate link: https://www.paypal.me/Kaludi/25
License: GPL2
*/
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

define('SASWP_VERSION', '1.9.15');
define('SASWP_DIR_NAME_FILE', __FILE__ );
define('SASWP_DIR_NAME', dirname( __FILE__ ));
define('SASWP_DIR_URI', plugin_dir_url(__FILE__));
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
require_once SASWP_DIR_NAME .'/output/output_post_specific.php';

if ( ! function_exists( 'is_plugin_active' ) )
     require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

if ( is_plugin_active('flexmls-idx/flexmls_connect.php') && class_exists('flexmlsConnectPageCore')) {
     require_once SASWP_DIR_NAME .'/output/flexmls.php';    
}

// Non amp checker
if ( ! function_exists('saswp_non_amp') ){
    
  function saswp_non_amp(){
      
    $non_amp = true;
    
    if(function_exists('ampforwp_is_amp_endpoint')) {
        
     if(ampforwp_is_amp_endpoint()){
        $non_amp = false;   
     }   
                           
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
require_once SASWP_DIR_NAME.'/view/review.php';  
require_once SASWP_DIR_NAME.'/output/review-output.php'; 
require_once SASWP_DIR_NAME.'/output/service.php'; 
require_once SASWP_DIR_NAME.'/output/compatibility.php'; 
//Loading Reviews files
require_once SASWP_DIR_NAME.'/reviews/reviews_admin.php'; 
require_once SASWP_DIR_NAME.'/reviews/reviews_setup.php';
require_once SASWP_DIR_NAME.'/reviews/reviews_service.php';
require_once SASWP_DIR_NAME.'/reviews/reviews_widget.php';
//Module files load
require_once SASWP_DIR_NAME.'/modules/gutenberg/includes/class-gutenberg.php';

//Loading Third party files
require_once SASWP_DIR_NAME.'/core/3rd-party/aqua_resizer.php';
/**
 * set user defined message on plugin activate
 */
register_activation_hook( __FILE__, 'saswp_on_activation' );
register_uninstall_hook( __FILE__, 'saswp_on_uninstall' );

add_action( 'admin_notices', 'saswp_admin_notice' );

function saswp_admin_notice(){
    
    $screen_id = ''; 
    $current_screen = get_current_screen();
    
    if(is_object($current_screen)){
        $screen_id =  $current_screen->id;
    }
    
    $nonce = wp_create_nonce( 'saswp_install_wizard_nonce' );  
    
    $setup_notice = '<div class="updated notice message notice notice-alt saswp-setup-notice">'
                    . '<p>'
                    . '<strong>'.esc_html__('Welcome to Schema & Structured Data For WP', 'schema-and-structured-data-for-wp').'</strong>'
                    .' - '.esc_html__('You are almost ready :)', 'schema-and-structured-data-for-wp')
                    . '</p>'
                    . '<p>'
                    . '<a class="button button-primary" href="'.esc_url(admin_url( 'plugins.php?page=saswp-setup-wizard' ).'&_saswp_nonce='.$nonce).'">'
                    . esc_html__('Run the Setup Wizard', 'schema-and-structured-data-for-wp')
                    . '</a> '
                    .'<a class="button saswp-skip-button">'
                    . esc_html__('Skip Setup', 'schema-and-structured-data-for-wp')
                    . '</a>'
                    . '</p>'
                    . '</div>';        
    
    
          
    $sd_data         = get_option('sd_data'); 
        
    if(($screen_id =='saswp_page_structured_data_options' ||$screen_id == 'plugins' || $screen_id =='edit-saswp' || $screen_id == 'saswp') && !isset($sd_data['sd_initial_wizard_status'])){
            
        echo $setup_notice;
        
    }     
     //Feedback notice
    $activation_date  =  get_option("saswp_activation_date");  
    $activation_never =  get_option("saswp_activation_never");      
    $next_days        =  strtotime("+7 day", strtotime($activation_date));
    $next_days        =  date('Y-m-d', $next_days);   
    $current_date     =  date("Y-m-d");
    
    if(($next_days < $current_date) && $activation_never !='never' ){
      ?>
         <div class="updated notice is-dismissible message notice notice-alt saswp-feedback-notice">
            <p><span class="dashicons dashicons-thumbs-up"></span> 
            <?php echo esc_html__('You have been using the Schema & Structured Data for WP & AMP plugin for some time. Now, Do you like it? If Yes.', 'schema-and-structured-data-for-wp') ?>
            <a class="saswp-revws-lnk" target="_blank" href="https://wordpress.org/plugins/schema-and-structured-data-for-wp/#reviews"> <?php echo esc_html__('Rate Plugin', 'schema-and-structured-data-for-wp') ?></a>
          </p>
            <div class="saswp-update-notice-btns">
                <a  class="saswp-feedback-remindme"><?php echo esc_html__('Remind Me Later', 'schema-and-structured-data-for-wp') ?></a>
                <a  class="saswp-feedback-no-thanks"><?php echo esc_html__('No Thanks', 'schema-and-structured-data-for-wp') ?></a>
            </div>
        </div>
        <?php
    }  
        
    if(isset($sd_data['sd_default_image']['url']) && $sd_data['sd_default_image']['url'] == '' && ($screen_id =='saswp_page_structured_data_options' ||$screen_id == 'plugins' || $screen_id =='edit-saswp' || $screen_id == 'saswp')){

        ?>
        <div class="updated notice is-dismissible message notice notice-alt saswp-feedback-notice">
            <p>
                  <span><?php echo esc_html__('You have not set up default image in Schema & Structured Data For WP.', 'schema-and-structured-data-for-wp') ?> </span>                                               
                  <a href="<?php echo esc_url( admin_url( 'admin.php?page=structured_data_options&tab=general#saswp-default-container' ) ); ?>"> <?php echo esc_html__('Please Setup', 'schema-and-structured-data-for-wp') ?></a>
            </p>
        </div>

      <?php   
        
    }
            
}

add_filter('plugin_row_meta' , 'saswp_add_plugin_meta_links', 10, 2);

function saswp_add_plugin_meta_links($meta_fields, $file) {
    
    if ( SASWP_PLUGIN_BASENAME == $file ) {
                       
      $forum_url  = "https://structured-data-for-wp.com/contact-us/";
      
      $meta_fields[] = "<a href='" . esc_url($forum_url) . "' target='_blank'>" . esc_html__('Technical Support', 'schema-and-structured-data-for-wp') . "</a>";                
    }

    return $meta_fields;
    
  }