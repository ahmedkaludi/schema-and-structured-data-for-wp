<?php
/**
 * Review layout template
 * @since 	1.45 
 * */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

global $sd_data;
$highlight_class 	=	'';
// if ( ! empty( $sd_rf_data['saswp-rf-page-settings-highlight-review'] ) ) {
	$highlighted 		=	get_comment_meta( $comment->comment_ID, 'saswp-rf-template-review-highlight', true );
	$highlight_class	=	! empty( $highlighted ) ? 'saswp-rf-template-top-review' : '';
// }
?>

<div class="saswp-rf-template-single-review <?php echo esc_attr( $highlight_class ); ?>">
	<?php if ( get_option( 'show_avatars' ) ) { ?>
		<div class="saswp-rf-template-avatar-holder">
			<?php
			$avatar = '';
			if ( get_comment_meta( get_comment_ID(), 'saswp-rf-form-anonymous', true ) ) {
				$avatar = SASWP_DIR_URI.'admin_section/images/saswp-avatar.jpg';
			} else {
				$avatar = get_avatar_url( $comment->comment_author_email, [ 'size' => '70' ] );
			}
			?>
			<img src="<?php echo esc_url( $avatar ); ?>" alt="">
		</div>
	<?php } ?> 

	<div class="saswp-rf-template-review-container">
		<?php
		if ( empty( $sd_data['saswp-rf-page-settings-title'] ) ) {
			$title = get_comment_meta( get_comment_ID(), 'saswp_review_form_title', true ); 
			if ( $title ) { ?>
				<h4 class="saswp-rf-template-review-title"><?php echo esc_html( $title ); ?></h4>
		<?php 
			} 
		}	
		?>
		<ul class="saswp-rf-template-review-meta">
			<?php 
			$average 	= 	get_comment_meta( get_comment_ID(), 'rating', true );
			// Sync existing star rating feature here
			if ( empty( $average ) ) {
				$average = 	get_comment_meta( get_comment_ID(), 'review_rating', true );	
				if ( empty( $average ) ) {
					$average 	=	! empty( $sd_data['saswp-default-rating'] ) ? absint( $sd_data['saswp-default-rating'] ) : 5;
				}
			}
			if ( $average ) { 
			?>
				<li class="saswp-rf-template-review-rating"><?php echo SASWP_Review_Feature_Frontend::summary_review_stars( $average ); ?></li>
			<?php 
			} 
			require SASWP_DIR_NAME . '/modules/reviews/templates/author-template.php';
			?>
			<li class="saswp-rf-template-review-date"><i class="dashicons dashicons-calendar-alt"></i>
				<?php 
				echo SASWP_Review_Feature_Frontend::render_comment_time( $comment ); 
				if ( $comment->user_id != 0 && $comment->user_id == get_current_user_id() ) {
					?>
					<span class="saswp-rf-template-review-edit-btn" data-comment-post-id="<?php echo esc_attr( $comment->comment_post_ID ); ?>" data-comment-id="<?php echo esc_attr( $comment->comment_ID ); ?>">
						<?php echo esc_html__( '(Edit)', 'schema-and-structured-data-for-wp' );?>
					</span> 
				<?php } ?>
			</li>
		</ul>
		<?php 
		comment_text();
		if ( $comment->comment_approved == '0' ) { ?>
			<p><em class="comment-awaiting-moderation"><?php echo esc_html__( 'Your comment is awaiting moderation.', 'schema-and-structured-data-for-wp' ); ?></em></p>
		<?php
		}

		require SASWP_DIR_NAME . '/modules/reviews/templates/attachment-template.php';
		
		require SASWP_DIR_NAME . '/modules/reviews/templates/pros-cons-template.php';
		?>
		<div class="saswp-rf-template-action-area">
			<?php 
			require SASWP_DIR_NAME . '/modules/reviews/templates/helpful-template.php';
			?>
		</div> 
		<?php

		require SASWP_DIR_NAME . '/modules/reviews/templates/reply-template.php';
		?>
	</div>
</div>