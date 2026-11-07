<?php
/**
 * Review pros and cons template
 * @since 	1.45 
 * */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

global $sd_data;
$saswp_pros_cons 		=	( isset( $sd_data['saswp-rf-page-settings-pros-cons'] ) && $sd_data['saswp-rf-page-settings-pros-cons'] == '1' );
if ( !$saswp_pros_cons ) return;
$saswp_pros 			=	get_comment_meta( get_comment_ID(), 'saswp_rf_form_pros', true ); 
$saswp_cons 			=	get_comment_meta( get_comment_ID(), 'saswp_rf_form_cons', true ); 
if ( ! empty( $saswp_pros ) || ! empty( $$saswp_cons ) ) {
?>
	<div class="saswp-rf-template-review-pros-cons">

		<?php 
		if ( is_array( $saswp_pros ) && ! empty( $saswp_pros[0] ) ) {
		?>
			<div class="saswp-rf-template-feedback-list-box saswp-rf-template-like-feedback">
		        <h3 class="saswp-rf-template-feedback-title">
		            <span class="item-icon like-icon"><i class="dashicons dashicons-thumbs-up"></i></span>
		            <span class="item-text"><?php echo esc_html__( 'PROS', 'schema-and-structured-data-for-wp' ); ?></span>
		        </h3>
		        <ul class="saswp-rf-template-feedback-list">
		            <?php foreach( $saswp_pros as $saswp_value ) { ?>
		            <li><i class="dashicons dashicons-yes"></i><?php echo esc_html( $saswp_value ); ?></li>
		            <?php } ?>
		        </ul>
		    </div>
		<?php
		}

		if ( is_array( $saswp_cons ) && ! empty( $saswp_cons[0] ) ) {
		?>
			<div class="saswp-rf-template-feedback-list-box saswp-rf-template-unlike-feedback">
		        <h3 class="saswp-rf-template-feedback-title">
		            <span class="item-icon unlike-icon"><i class=" dashicons dashicons-thumbs-down"></i></span>
		            <span class="item-text"><?php echo esc_html__( 'CONS', 'schema-and-structured-data-for-wp' ); ?></span>
		        </h3>
		        <ul class="saswp-rf-template-feedback-list">
		            <?php foreach( $saswp_cons as $saswp_value ) { ?>
		            <li><i class="dashicons dashicons-yes"></i><?php echo esc_html( $saswp_value ); ?></li>
		            <?php } ?>
		        </ul>
		    </div>
		<?php	
		}
		?>

	</div> 
<?php
}
?>
