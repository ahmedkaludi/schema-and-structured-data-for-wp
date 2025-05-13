<?php
/**
 * Review pros and cons template
 * @since 	1.45 
 * */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

global $sd_data;

if ( ! empty( $sd_data['saswp-rf-page-settings-social-review'] ) ) {
	$comment_content 	=	urlencode( wp_strip_all_tags( $comment->comment_content ) );
	$str_length 		=	strlen( $comment_content );
	if ( $comment_content > 100 ) {
		$comment_content= 	substr( $comment_content, 0, 100 ) . '...'; 	
	}
    $post_url 			=	get_comment_link( $comment->comment_ID );

     $twitter_url 		=	"https://twitter.com/intent/tweet?text={$comment_content}&url={$post_url}";
     $facebook_url 		=	"https://www.facebook.com/sharer/sharer.php?u={$post_url}";
?>
	<li class="saswp-rf-template-review-social-share">
		<i class="dashicons dashicons-share"></i>
		<span><?php echo esc_html__( 'Share:', 'schema-and-structured-data-for-wp' ); ?></span>
		<label class="saswp-rf-template-social-share-labels">
			<a href="<?php echo esc_url( $twitter_url ) ?>" target="_blank">
				<span><i class="dashicons dashicons-twitter"></i></span>
			</a>
		</label>
		<label class="saswp-rf-template-social-share-labels">
			<a href="<?php echo esc_url( $facebook_url ) ?>" target="_blank">
				<span><i class="dashicons dashicons-facebook-alt"></i></span>
			</a>
		</label>
	</li>
<?php

}

