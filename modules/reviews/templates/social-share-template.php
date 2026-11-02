<?php
/**
 * Review pros and cons template
 * @since 	1.45 
 * */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

global $sd_data;

if ( ! empty( $sd_data['saswp-rf-page-settings-social-review'] ) ) {
	$saswp_comment_content 	=	urlencode( wp_strip_all_tags( $comment->comment_content ) );
	$saswp_str_length 		=	strlen( $saswp_comment_content );
	if ( $saswp_comment_content > 100 ) {
		$saswp_comment_content= 	substr( $saswp_comment_content, 0, 100 ) . '...'; 	
	}
    $saswp_post_url 			=	get_comment_link( $comment->comment_ID );

     $saswp_twitter_url 		=	"https://twitter.com/intent/tweet?text={$saswp_comment_content}&url={$saswp_post_url}";
     $saswp_facebook_url 		=	"https://www.facebook.com/sharer/sharer.php?u={$saswp_post_url}";
?>
	<li class="saswp-rf-template-review-social-share">
		<i class="dashicons dashicons-share"></i>
		<span><?php echo esc_html__( 'Share:', 'schema-and-structured-data-for-wp' ); ?></span>
		<label class="saswp-rf-template-social-share-labels">
			<a href="<?php echo esc_url( $saswp_twitter_url ) ?>" target="_blank">
				<span><i class="dashicons dashicons-twitter"></i></span>
			</a>
		</label>
		<label class="saswp-rf-template-social-share-labels">
			<a href="<?php echo esc_url( $saswp_facebook_url ) ?>" target="_blank">
				<span><i class="dashicons dashicons-facebook-alt"></i></span>
			</a>
		</label>
	</li>
<?php

}

