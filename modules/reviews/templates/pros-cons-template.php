<?php
/**
 * Review pros and cons template
 * @since 	1.45 
 * */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

global $sd_data;
$pros_cons 		=	( isset( $sd_data['saswp-rf-page-settings-pros-cons'] ) && $sd_data['saswp-rf-page-settings-pros-cons'] == '1' );
if ( !$pros_cons ) return;
$pros 			=	get_comment_meta( get_comment_ID(), 'saswp_rf_form_pros', true ); 
$cons 			=	get_comment_meta( get_comment_ID(), 'saswp_rf_form_cons', true ); 
if ( ! empty( $pros ) || ! empty( $cons ) ) {
?>
	<div class="saswp-rf-template-review-pros-cons">

		<?php 
		if ( is_array( $pros ) && ! empty( $pros[0] ) ) {
		?>
			<div class="saswp-rf-template-feedback-list-box saswp-rf-template-like-feedback">
		        <h3 class="saswp-rf-template-feedback-title">
		            <span class="item-icon like-icon"><i class="dashicons dashicons-thumbs-up"></i></span>
		            <span class="item-text"><?php echo esc_html__( 'PROS', 'schema-and-structured-data-for-wp' ); ?></span>
		        </h3>
		        <ul class="saswp-rf-template-feedback-list">
		            <?php foreach( $pros as $value ) { ?>
		            <li><i class="dashicons dashicons-yes"></i><?php echo esc_html( $value ); ?></li>
		            <?php } ?>
		        </ul>
		    </div>
		<?php
		}

		if ( is_array( $cons ) && ! empty( $cons[0] ) ) {
		?>
			<div class="saswp-rf-template-feedback-list-box saswp-rf-template-unlike-feedback">
		        <h3 class="saswp-rf-template-feedback-title">
		            <span class="item-icon unlike-icon"><i class=" dashicons dashicons-thumbs-down"></i></span>
		            <span class="item-text"><?php echo esc_html__( 'CONS', 'schema-and-structured-data-for-wp' ); ?></span>
		        </h3>
		        <ul class="saswp-rf-template-feedback-list">
		            <?php foreach( $cons as $value ) { ?>
		            <li><i class="dashicons dashicons-yes"></i><?php echo esc_html( $value ); ?></li>
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
