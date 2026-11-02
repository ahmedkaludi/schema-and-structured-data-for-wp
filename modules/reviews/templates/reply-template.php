<?php
/**
 * Reply template
 * @since 	1.45 
 * */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

$saswp_comment_reply_link = get_comment_reply_link(
	array_merge(
		$args,
		[
			'add_below' => $add_below,
			'depth'     => $depth,
			// 'reply_to_text' => '',
			'max_depth' => $args['max_depth'],
		]
	)
);

if( empty( $saswp_comment_reply_link )){
	return;
}
?>
<div class="saswp-rf-template-reply-btn"> 
	<?php
		/* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */
		echo preg_replace(
			'/comment-reply-link/',
			'comment-reply-link saswp-rf-template-item-btn',
			$saswp_comment_reply_link,
			1
		);
		?>
</div> 
<?php 
if ( current_user_can( 'administrator' )  ) {
	$saswp_comment_id 	=	$comment->comment_ID;
	$id 			=	'saswp-rf-template-review-highlight-' . $saswp_comment_id;
	$saswp_highlighted 	=	get_comment_meta( $saswp_comment_id, 'saswp-rf-template-review-highlight', true );	
	?>
	<div class="saswp-rf-template-reaction-review-wrapper">
		<div class="saswp-rf-form-check saswp-rf-form-highlight-wrapper saswp-rf-template-hide">
			<input type="checkbox" class="saswp-rf-template-review-highlight saswp-rf-template-hide" id="<?php echo esc_attr( $id ); ?>" <?php checked( $saswp_highlighted ); ?> data-comment-id="<?php echo esc_attr( $saswp_comment_id ); ?>" >
			<label for="<?php echo esc_attr( $id ); ?>" class="saswp-rf-form-checkbox-label"><?php echo esc_html__( 'Highlight', 'schema-and-structured-data-for-wp' ); ?></label>
		</div>
	</div>
	<?php
}
?>