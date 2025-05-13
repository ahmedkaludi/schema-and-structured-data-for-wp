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

$likes 				=	get_comment_meta( $comment->comment_ID, 'saswp_rf_form_helpful_like', true );
$dislike 			=	get_comment_meta( $comment->comment_ID, 'saswp_rf_form_helpful_dislike', true );
if ( empty( $likes ) ) {
	$likes 			=	array();
}
if ( empty( $dislike ) ) {
	$dislike 		=	array();
}

$like_count 		=	0;
$dislike_count 		=	0;
$like_class 		=	'';
$dislike_class 		=	'';
$my_helpful 		=	0; // 1 - Like, 2 -Dislike
$like_checked 		=	'';
$dislike_checked	=	'';

if ( ! empty( $likes ) && is_array( $likes ) ) {
	$like_count 	=	count( $likes );	
}
if ( ! empty( $dislike ) && is_array( $dislike ) ) {
	$dislike_count 	=	count( $dislike );	
}

if ( is_user_logged_in() ) {
	$current_user 	=	wp_get_current_user();
	$user_id      	=	$current_user->ID;

	if ( in_array( $user_id, $likes ) ) {
		$my_helpful 	=	1;	
		$like_class 	=	'saswp-rf-helpful-selected';
		$like_checked 	=	'checked';
	} else if ( in_array( $user_id, $dislike ) ) {
		$my_helpful 	=	2;
		$dislike_class 	=	'saswp-rf-helpful-selected';
		$dislike_checked =	'checked';
	}
}
$like_id 	= "saswp-rf-form-helpful-like-" . $comment->comment_ID;
$dislike_id = "saswp-rf-form-helpful-dislike-" . $comment->comment_ID;
?>
<div class="saswp-rf-template-review-helpful">
	<?php 
	if ( ! empty( $sd_data['saswp-rf-page-settings-review-like'] ) ) {
	?>
		<label for="<?php echo esc_attr( $like_id ); ?>">
			<input type="radio" name="saswp_rf_form_helpful" id="<?php echo esc_attr( $like_id ); ?>" class="saswp-rf-form-helful-radio" value="like" data-comment-id="<?php echo esc_attr( $comment->comment_ID ); ?>" <?php echo esc_attr( $like_checked ); ?>>
			<i class="dashicons dashicons-thumbs-up <?php echo esc_attr( $like_class ); ?>"></i>
			<span class="saswp-rf-template-helpful-count" id="saswp-rf-template-helpful-like-count"><?php echo esc_html( $like_count ); ?></span>
		</label>
	<?php 
	}

	if ( ! empty( $sd_data['saswp-rf-page-settings-review-dislike'] ) ) {
	?>
	<label for="<?php echo esc_attr( $dislike_id ); ?>">
		<input type="radio" name="saswp_rf_form_helpful" id="<?php echo esc_attr( $dislike_id ); ?>" class="saswp-rf-form-helful-radio" value="dislike" data-comment-id="<?php echo esc_attr( $comment->comment_ID ); ?>" <?php echo esc_attr( $dislike_checked ); ?>>
		<i class="dashicons dashicons-thumbs-down <?php echo esc_attr( $dislike_class ); ?>"></i>
		<span class="saswp-rf-template-helpful-count" id="saswp-rf-template-helpful-dislike-count"><?php echo esc_html( $dislike_count ); ?></span>
	</label>
	<?php 
	}
	?>
</div>