<?php
/**
 * Extended Comments
 *
 * @author   Magazine3
 * @category Admin
 * @path     reviews/comments
 * @version 1.9
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

function saswp_check_stars_rating(){

	global $sd_data, $post;

	if(isset($sd_data['saswp-stars-rating']) && $sd_data['saswp-stars-rating'] == 1){

		$post_types = array();

		if(isset($sd_data['saswp-stars-post-taype'])){
			$post_types = $sd_data['saswp-stars-post-taype'];
		}
		
		if(in_array(get_post_type(), $post_types)){

			return true;
		}else{
			return false;
		}
		
	}else{
		return false;
	}
}

//Get the average rating of a post.
function saswp_comment_rating_get_average_ratings( $id ) {

	$comments = get_approved_comments( $id );

	if ( $comments ) {
		$i = 0;
		$total = 0;
		foreach( $comments as $comment ){
			$rate = get_comment_meta( $comment->comment_ID, 'review_rating', true );
			if( isset( $rate ) && '' !== $rate ) {
				$i++;
				$total += $rate;
			}
		}

		if ( 0 === $i ) {
			return false;
		} else {
			return array('average' => round( $total / $i, 1 ), 'count' => count($comments));
		}
	} else {
		return false;
	}
}

//Create the rating interface.
add_action( 'comment_form_top', 'saswp_comment_rating_rating_field' );

function saswp_comment_rating_rating_field () {	

	if(saswp_check_stars_rating()){

		wp_enqueue_style( 'saswp-frontend-css', SASWP_PLUGIN_URL . 'admin_section/css/'.(SASWP_ENVIRONMENT == 'production' ? 'saswp-frontend.min.css' : 'saswp-frontend.css'), false , SASWP_VERSION );	
		wp_enqueue_script( 'saswp-rateyo-front-js', SASWP_PLUGIN_URL . 'admin_section/js/jquery.rateyo.min.js', array('jquery', 'jquery-ui-core'), SASWP_VERSION , true );                                                                                        
		wp_enqueue_style( 'jquery-rateyo-min-css', SASWP_PLUGIN_URL . 'admin_section/css/'.(SASWP_ENVIRONMENT == 'production' ? 'jquery.rateyo.min.css' : 'jquery.rateyo.min.css'), false, SASWP_VERSION );
		wp_enqueue_script( 'saswp-frontend-js', SASWP_PLUGIN_URL . 'admin_section/js/'.(SASWP_ENVIRONMENT == 'production' ? 'saswp-frontend.min.js' : 'saswp-frontend.js'), array('jquery', 'jquery-ui-core'), SASWP_VERSION );

		?>
		<p class="comment-form-comment">
		<div id="saswp-comment-rating-div"></div>
		<input type="hidden" name="review_rating" value="5" />
		</p>		
		<?php

	}	
}

//Save the rating submitted by the user.
add_action( 'comment_post', 'saswp_comment_rating_save_comment_rating' );
function saswp_comment_rating_save_comment_rating( $comment_id ) {
		
		if ( ( isset( $_POST['review_rating'] ) ) && ( '' !== $_POST['review_rating'] ) ){

			$rating = intval( $_POST['review_rating'] );
			add_comment_meta( $comment_id, 'review_rating', $rating );
			
		}	
		
}

//Display the rating on a submitted comment.
add_filter( 'comment_text', 'saswp_comment_rating_display_rating');

function saswp_comment_rating_display_rating( $comment_text ){
	
	if ( saswp_check_stars_rating() ) {

		$rating = get_comment_meta( get_comment_ID(), 'review_rating', true );

		return '<p>'.saswp_get_rating_html_by_value($rating).'</p><p>'.esc_html($comment_text).'</p>';
	} else {
		return '<p>'.$comment_text.'</p>';
	}
}

//Display the average rating above the content.
add_action( 'comment_form_before', 'saswp_comment_rating_display_average_rating' );
function saswp_comment_rating_display_average_rating() {

	global $post;

	if(saswp_check_stars_rating()){

		$average_rate = saswp_comment_rating_get_average_ratings( $post->ID );

		if($average_rate){
			
			$average = $average_rate['average'];
			$count   = $average_rate['count'];
				
			$custom_content  = '<div class="saswp-average-rating">'.esc_html__('Average','schema-and-structured-data-for-wp').' '. saswp_get_rating_html_by_value($average).' '.$average.' '. esc_html__('Based On','schema-and-structured-data-for-wp') .' '.$count.'</div>';

			echo $custom_content;

		}

	}	
			
}