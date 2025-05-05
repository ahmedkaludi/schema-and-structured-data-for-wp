<?php

// Exit if accessed directly
if( !defined( 'ABSPATH' ) )
    exit;

/**
 * Helper method to check if user is in the plugins page.
 *
 * @author 
 * @since  1.4.0
 *
 * @return bool
 */

function saswp_is_plugins_page() {

    if ( function_exists( 'get_current_screen' ) ){

        $screen = get_current_screen();

            if ( is_object( $screen ) ) {
                if ( $screen->id == 'plugins' || $screen->id == 'plugins-network' ) {
                    return true;
                }
            }
    }
    return false;
}

/**
 * display deactivation logic on plugins page
 * 
 * @since 1.4.0
 */


function saswp_add_deactivation_feedback_modal() {
    
  
    if( !is_admin() && !saswp_is_plugins_page()) {
        return;
    }

    $current_user = wp_get_current_user();
    if( !($current_user instanceof WP_User) ) {
        $email = '';
    } else {
        $email = trim( $current_user->user_email );
    }

    require_once SASWP_DIR_NAME. '/admin_section/tracking/deactivate-feedback.php';
    
}

/**
 * send feedback via email
 * 
 * @since 1.4.0
 */
function saswp_send_feedback() {
    if(!current_user_can( saswp_current_user_can()) ) {
        die( '-1' );    
    }
    // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Reason: We are just verifiying nonce below this lines.
    if( isset( $_POST['data'] ) ) {
        // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: We are just verifiying nonce below this lines.
        parse_str( $_POST['data'], $form );
    }
    if ( ! isset( $form['saswp_feedback_nonce'] ) ){
       return; 
    }
    if ( !wp_verify_nonce( $form['saswp_feedback_nonce'], 'saswp_feedback_nonce' ) ){
       return;  
    }

    $text = '';
    if( isset( $form['saswp_disable_text'] ) ) {
        $text = implode( "\n\r", $form['saswp_disable_text'] );
    }

    $headers = array();

    $from = isset( $form['saswp_disable_from'] ) ? $form['saswp_disable_from'] : '';
    if( $from ) {
        $headers[] = "From: $from";
        $headers[] = "Reply-To: $from";
    }

    $subject = isset( $form['saswp_disable_reason'] ) ? $form['saswp_disable_reason'] : '(no reason given)';

    $subject = $subject.' - Schema & Structured Data for WP & AMP';

    if($subject == 'technical - Schema & Structured Data for WP & AMP'){

          $text = trim($text);

          if ( ! empty( $text) ) {

            $text = 'technical issue description: '.$text;

          }else{

            $text = 'no description: '.$text;
          }
      
    }

    $success = wp_mail( 'team@magazine3.in', $subject, $text, $headers );

    die();
}
add_action( 'wp_ajax_saswp_send_feedback', 'saswp_send_feedback' );



add_action( 'admin_enqueue_scripts', 'saswp_enqueue_makebetter_email_js' );

function saswp_enqueue_makebetter_email_js() {
 
    if( ! is_admin() && ! saswp_is_plugins_page() ) {
        return;
    }

    wp_enqueue_script( 'saswp-make-better-js', SASWP_DIR_URI . '/admin_section/tracking/make-better-admin.js', array( 'jquery' ), SASWP_VERSION, true );

    wp_enqueue_style( 'saswp-make-better-css', SASWP_DIR_URI . '/admin_section/tracking/make-better-admin.css', false , SASWP_VERSION );
}

add_filter( 'admin_footer', 'saswp_add_deactivation_feedback_modal' );