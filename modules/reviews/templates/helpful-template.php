<?php
/**
 * Review pros and cons template
 * @since 	1.45 
 * */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

global $sd_data;

if ( empty( $sd_data['saswp-rf-page-settings-review-like'] ) && empty( $sd_data['saswp-rf-page-settings-review-dislike'] ) ) {
	return;
}

$saswp_likes 				=	get_comment_meta( $comment->comment_ID, 'saswp_rf_form_helpful_like', true );
$saswp_dislike 			=	get_comment_meta( $comment->comment_ID, 'saswp_rf_form_helpful_dislike', true );
if ( empty( $saswp_likes ) ) {
	$saswp_likes 			=	array();
}
if ( empty( $saswp_dislike ) ) {
	$saswp_dislike 		=	array();
}

$saswp_like_count 		=	0;
$saswp_dislike_count 		=	0;
$saswp_like_class 		=	'';
$saswp_dislike_class 		=	'';
$saswp_my_helpful 		=	0; // 1 - Like, 2 -Dislike
$saswp_like_checked 		=	'';
$saswp_dislike_checked	=	'';

if ( ! empty( $saswp_likes ) && is_array( $saswp_likes ) ) {
	$saswp_like_count 	=	count( $saswp_likes );	
}
if ( ! empty( $saswp_dislike ) && is_array( $saswp_dislike ) ) {
	$saswp_dislike_count 	=	count( $saswp_dislike );	
}

if ( is_user_logged_in() ) {
	$current_user 	=	wp_get_current_user();
	$saswp_user_id      	=	$current_user->ID;

	if ( in_array( $saswp_user_id, $saswp_likes ) ) {
		$saswp_my_helpful 	=	1;	
		$saswp_like_class 	=	'saswp-rf-helpful-selected';
		$saswp_like_checked 	=	'checked';
	} else if ( in_array( $saswp_user_id, $saswp_dislike ) ) {
		$saswp_my_helpful 	=	2;
		$saswp_dislike_class 	=	'saswp-rf-helpful-selected';
		$saswp_dislike_checked =	'checked';
	}
}
$saswp_like_id 	= "saswp-rf-form-helpful-like-" . $comment->comment_ID;
$saswp_dislike_id = "saswp-rf-form-helpful-dislike-" . $comment->comment_ID;
?>
<div class="saswp-rf-template-review-helpful">
	<?php 
	if ( ! empty( $sd_data['saswp-rf-page-settings-review-like'] ) ) {
	?>
		<label for="<?php echo esc_attr( $saswp_like_id ); ?>">
			<input type="radio" name="saswp_rf_form_helpful" id="<?php echo esc_attr( $saswp_like_id ); ?>" class="saswp-rf-form-helful-radio" value="like" data-comment-id="<?php echo esc_attr( $comment->comment_ID ); ?>" <?php echo esc_attr( $saswp_like_checked ); ?>>
			<i class="dashicons dashicons-thumbs-up <?php echo esc_attr( $saswp_like_class ); ?>"></i>
			<span class="saswp-rf-template-helpful-count" id="saswp-rf-template-helpful-like-count"><?php echo esc_html( $saswp_like_count ); ?></span>
		</label>
	<?php 
	}

	if ( ! empty( $sd_data['saswp-rf-page-settings-review-dislike'] ) ) {
	?>
	<label for="<?php echo esc_attr( $saswp_dislike_id ); ?>">
		<input type="radio" name="saswp_rf_form_helpful" id="<?php echo esc_attr( $saswp_dislike_id ); ?>" class="saswp-rf-form-helful-radio" value="dislike" data-comment-id="<?php echo esc_attr( $comment->comment_ID ); ?>" <?php echo esc_attr( $saswp_dislike_checked ); ?>>
		<i class="dashicons dashicons-thumbs-down <?php echo esc_attr( $saswp_dislike_class ); ?>"></i>
		<span class="saswp-rf-template-helpful-count" id="saswp-rf-template-helpful-dislike-count"><?php echo esc_html( $saswp_dislike_count ); ?></span>
	</label>
	<?php 
	}
	?>
</div>