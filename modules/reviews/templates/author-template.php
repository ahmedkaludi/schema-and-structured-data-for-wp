<?php
/**
 * Author layout template
 * @since 	1.45 
 * */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
global $comment, $sd_data;
?>
<li class="saswp-rf-template-author-link"><?php esc_html_e('by:', 'schema-and-structured-data-for-wp'); ?> 
    <?php 
    if ( get_comment_meta( get_comment_ID(), 'saswp-rf-form-anonymous', true ) ) {
            echo esc_html__( 'Anonymous', 'schema-and-structured-data-for-wp' );
    } else {
            echo ! empty( $comment->comment_author ) ? $comment->comment_author : '';
    }

    if ( ! empty( $sd_data['saswp-rf-page-settings-purchase-badge'] ) && ! empty( $comment->comment_author_email ) ) {

        $comment_email          =   $comment->comment_author_email;
        $woocommerce_status     =   SASWP_Review_Feature_Frontend::woocommerce_verified_customer( $comment_email );
        $edd_status             =   SASWP_Review_Feature_Frontend::edd_verified_customer( $comment_email );

        if ( ! empty( $woocommerce_status ) || ! empty( $edd_status ) ) {
        ?>
            <i class="dashicons dashicons-yes-alt" title="<?php echo esc_attr__( 'Verified Customer', 'schema-and-structured-data-for-wp' ); ?>"></i>
        <?php
        }
    }
    ?>
</li> 
