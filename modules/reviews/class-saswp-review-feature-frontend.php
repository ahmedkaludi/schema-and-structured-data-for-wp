<?php
/**
 * Reviews Form  Class
 *
 * @author   Magazine3
 * @class 	 SASWP_Review_Feature_Frontend
 * @path     reviews/reviews_form
 * @Version 1.45
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class SASWP_Review_Feature_Frontend {

	public function __construct() {

		add_filter( 'comment_form_defaults', array( $this, 'modify_comment_form' ), 20 );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts_and_styles' ) );
		add_action( 'comment_post', array( $this, 'save_comment_meta' ) );
		// Change the default comments template of wordpress
		add_filter( 'comments_template', array( $this, 'render_template' ), 100 );

		add_action('wp_ajax_saswp_rf_review_edit', [$this, 'saswp_rf_review_edit']);

		add_action( 'wp_ajax_saswp_rf_form_image_upload', [$this, 'saswp_image_upload'] );
		add_action( 'wp_ajax_nopriv_saswp_rf_form_image_upload', [$this, 'saswp_image_upload'] );

		add_action( 'wp_ajax_saswp_rf_form_remove_file', [$this, 'saswp_remove_file'] );
		add_action( 'wp_ajax_nopriv_saswp_rf_form_remove_file', [$this, 'saswp_remove_file'] );

		add_action( 'wp_ajax_saswp_rf_form_video_upload', [$this, 'saswp_video_upload'] );
		add_action( 'wp_ajax_nopriv_saswp_rf_form_video_upload', [$this, 'saswp_video_upload'] );

		add_action('wp_ajax_saswp_rf_form_self_video_popup', [$this, 'saswp_self_video_popup']);
		add_action('wp_ajax_nopriv_saswp_rf_form_self_video_popup', [$this, 'saswp_self_video_popup']);

		add_action( 'wp_ajax_saswp_rf_template_review_filter', [$this, 'saswp_review_filter'] );
		add_action( 'wp_ajax_nopriv_saswp_rf_template_review_filter', [$this, 'saswp_review_filter'] );

		add_action('wp_ajax_saswp_rf_template_pagination', [$this, 'saswp_pagination'] );
		add_action('wp_ajax_nopriv_saswp_rf_template_pagination', [$this, 'saswp_pagination'] );

		add_action('wp_ajax_saswp_rf_template_review_edit_form', [$this, 'saswp_rf_template_review_edit_form']);
		add_action('wp_ajax_nopriv_saswp_rf_template_review_edit_form', [$this, 'saswp_rf_template_review_edit_form']);

		add_action('wp_ajax_saswp_rf_template_review_helpful', [$this, 'saswp_review_helpful']);

		add_action('wp_ajax_saswp_template_review_hightlight', [$this, 'saswp_review_highlight']);

		/**
		 * If current theme is not a block based theme then keep the previous feature of start rating
		 * */
		if ( saswp_is_block_theme() ) {
			//Create the rating interface.
			add_filter( 'comment_text', 'saswp_comment_rating_display_rating', 10, 2);
			add_action( 'comment_form_before', 'saswp_comment_rating_display_average_rating' );
		}else{
			add_action( 'comment_form_top', 'saswp_comment_rating_rating_field' );
		}


	}

	/**
	 * Modify comment form
	 * @param 	$defaults 	array
	 * @return 	$defaults 	array
	 * @since 	1.45
	 * */
	public function modify_comment_form( $defaults ) {
		
		if ( ! $this->check_support() || ! saswp_check_stars_rating() ) {
			return $defaults;
		}

		global $sd_data;

		$commenter 	= wp_get_current_commenter();

		$email   	= isset( $sd_data['saswp-rf-page-settings-anonymus-email'] ) 	&&	$sd_data['saswp-rf-page-settings-anonymus-email'] 	== '1' ? false : true;
		$website 	= isset( $sd_data['saswp-rf-page-settings-url-disable'] )		&&	$sd_data['saswp-rf-page-settings-url-disable'] 		== '1' ? false : true;
		$title   	= isset( $sd_data['saswp-rf-page-settings-title'] ) 			&&	$sd_data['saswp-rf-page-settings-title'] 			== '1' ? false : true;
		$author  	= isset( $sd_data['saswp-rf-page-settings-anonymus-author'] ) 	&&	$sd_data['saswp-rf-page-settings-anonymus-author'] 	== '1' ? false : true;

		$defaults['fields'] 				=	[];
		if ( $author ) {
			$defaults['fields']['author'] 	= '<div class="saswp-rf-form-group"><input class="saswp-rf-form-control" placeholder="Enter Author*" name="author"  type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" required /></div>';
		}
		if ( $email ) {
			$defaults['fields']['email'] 	= '<div class="saswp-rf-form-group"><input id="saswp-review-form-author" class="saswp-rf-form-control" placeholder="Enter Author Email*" name="email"  type="text" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30" required/></div>';
		}

		if ( $website ) {
			$defaults['fields']['url']     	= '<div class="saswp-rf-form-group"><input id="saswp-review-form-author-url" class="saswp-rf-form-control" placeholder="Enter Author Website" name="url" type="text" value="' . esc_url( $commenter['comment_author_url'] ) . '" size="30"/></div>';
		}

		$defaults['id_form']            	=	'comment_form';
		$defaults['class_form']         	=	'saswp-rf-form-box';
		$defaults['id_submit']          	=	'submit';
		$defaults['class_submit']       	=	'saswp-rf-form-submit-btn saswp-rf-form-submit';
		$defaults['class_container']    	=	'comment-respond saswp-rf-form';
		$defaults['submit_field']       	=	'<div class="saswp-rf-form-group saswp-rf-form-submit-wrapper ">%1$s %2$s</div>';
		$defaults['name_submit']        	=	'submit';
		$defaults['title_reply_before'] 	=	'<h2 id="reply-title" class="saswp-rf-form-title">';
		$defaults['title_reply_after']  	=	'</h2>';

		$defaults['comment_field'] 			=	'';

		if ( $title ) {
			$defaults['comment_field'] 			.=	'<div class="saswp-rf-form-group saswp-rf-form-hide-reply">';
			$defaults['comment_field']   		.=	'<input id="saswp_review_form_title" class="saswp-rf-form-control" placeholder="'.esc_html__( 'Enter Title', 'schema-and-structured-data-for-wp' ).'" name="saswp_review_form_title" type="text" value="" size="30" aria-required="true">';
			$defaults['comment_field'] 			.=	'</div>'; 	//saswp-rf-form-group saswp-rf-form-hide-reply
		}
		$defaults['comment_field'] 			.= '<div class="saswp-rf-form-group">';
		$defaults['comment_field'] 			.=	'<textarea id="message" class="saswp-rf-form-control" placeholder="Enter your review*"  name="comment" required="required"  aria-required="true" rows="6" cols="45"></textarea>';
		$defaults['comment_field'] 			.=	'</div>'; 	//saswp-rf-form-group

		if ( is_user_logged_in() ) {
			$defaults['comment_field'] 		.=	$this->add_aditional_fields();
		} else {
			$defaults['fields']['extra'] 	= 	$this->add_aditional_fields();
		}

		return $defaults;

	}
	
	/**
	 * Load scripts and styles on frontend
	 * @since 	1.45
	 * */
	public function load_scripts_and_styles() {
		
		if ( $this->check_support() && saswp_check_stars_rating() ) {

			global $sd_data;
			wp_enqueue_style( 'saswp-rf-style', SASWP_PLUGIN_URL . 'admin_section/css/'.(SASWP_ENVIRONMENT == 'production' ? 'saswp-review-feature-front.min.css' : 'saswp-review-feature-front.css'), false , SASWP_VERSION );
			if ( !is_user_logged_in() ) {
	        	wp_enqueue_style( 'dashicons' );
	    	}

			$data 	=	array(
				'saswp_rfpage_settings_pros_cons_limit' 	=>	isset( $sd_data['saswp-rf-page-settings-pros-cons-limit'] ) ? $sd_data['saswp-rf-page-settings-pros-cons-limit'] : 3, 
				'saswp_multi_criteria_count' 				=> 	isset( $sd_data['saswp-rf-page-criteria-multiple'] ) ? count( $sd_data['saswp-rf-page-criteria-multiple'] ) : 0,
				'saswp_rf_page_security_nonce'         		=> wp_create_nonce('saswp_rf_form_action_nonce'),
				'loading' 									=> esc_html__( 'Loading...', 'schema-and-structured-data-for-wp' ),
				'edit' 										=> esc_html__( 'Edit', 'schema-and-structured-data-for-wp' ),
				'upload_img' 								=> esc_html__( 'Upload Image', 'schema-and-structured-data-for-wp' ),
				'upload_video' 								=> esc_html__( 'Upload Video', 'schema-and-structured-data-for-wp' ),
				'ajaxurl' 									=> admin_url('admin-ajax.php'),
				'sure_txt' 									=> esc_html__( 'Are you sure to delete?', 'schema-and-structured-data-for-wp' ),
				'post_id' 									=> get_the_ID(), 
				'current_page' 								=> get_query_var( 'cpage' ) ? get_query_var( 'cpage' ) : 1, 
				'default_rating' 							=> 5,
			);
			wp_register_script( 'saswp-rf-frontend-script', SASWP_PLUGIN_URL . 'admin_section/js/'.(SASWP_ENVIRONMENT == 'production' ? 'saswp-review-feature-frontend.min.js' : 'saswp-review-feature-frontend.js'), array( 'jquery' ), SASWP_VERSION , true );
			wp_localize_script( 'saswp-rf-frontend-script', 'saswp_rf_localize_data', $data );
		    wp_enqueue_script( 'saswp-rf-frontend-script' );

		    // if ( isset( $sd_data['saswp-rf-page-criteria'] ) && $sd_data['saswp-rf-page-criteria'] == 'multi' && ! empty( $sd_data['saswp-rf-page-criteria-multiple'] ) && is_array( $sd_data['saswp-rf-page-criteria-multiple'] ) ) {

			    wp_enqueue_style( 'saswp-frontend-css', SASWP_PLUGIN_URL . 'admin_section/css/'.(SASWP_ENVIRONMENT == 'production' ? 'saswp-frontend.min.css' : 'saswp-frontend.css'), false , SASWP_VERSION );	
				wp_enqueue_script( 'saswp-rateyo-front-js', SASWP_PLUGIN_URL . 'admin_section/js/jquery.rateyo.min.js', array('jquery', 'jquery-ui-core'), SASWP_VERSION , true );                                                                                        
				wp_enqueue_style( 'jquery-rateyo-min-css', SASWP_PLUGIN_URL . 'admin_section/css/'.(SASWP_ENVIRONMENT == 'production' ? 'jquery.rateyo.min.css' : 'jquery.rateyo.min.css'), false, SASWP_VERSION );


				$data = array(     
		            'rateyo_default_rating'  =>  isset($sd_data['saswp-default-rating']) ? $sd_data['saswp-default-rating'] : 5
				);

				wp_register_script( 'saswp-frontend-js', SASWP_PLUGIN_URL . 'admin_section/js/'.(SASWP_ENVIRONMENT == 'production' ? 'saswp-frontend.min.js' : 'saswp-frontend.js'), array('jquery', 'jquery-ui-core'), SASWP_VERSION, true );
				wp_localize_script( 'saswp-frontend-js', 'saswp_localize_front_data', $data );
				wp_enqueue_script( 'saswp-frontend-js' );


		    // } 


			$custom_style 	=	$this->custom_style();
			wp_add_inline_style( 'saswp-rf-style', $custom_style );
		}


	}

	/**
	 * Check if current post is supported
	 * @since 	1.45
	 * */
	public function check_support( $get_post = null ) {
		
		global $sd_data, $post;
		if ( empty( $post ) && ! empty( $get_post ) ) {
			$post 	=	$get_post;
		}
		if ( ! empty( $sd_data['saswp-stars-post-taype'] ) && is_object( $post ) && ! empty( $post->post_type ) ) {

			if ( in_array( $post->post_type, $sd_data['saswp-stars-post-taype'] ) ) {
				return true;
			}

		}

		return false;
	}

	/**
	 * Render additional fields
	 * @since 	1.45
	 * */
	public function add_aditional_fields() {
		
		global $sd_data;
		ob_start();
		?>
		<div class="saswp-rf-form-group saswp-rf-form-hide-reply">
			<ul class="saswp-rf-form-rating-ul">
				<?php
				if ( isset( $sd_data['saswp-rf-page-criteria'] ) && $sd_data['saswp-rf-page-criteria'] == 'multi' && ! empty( $sd_data['saswp-rf-page-criteria-multiple'] ) && is_array( $sd_data['saswp-rf-page-criteria-multiple'] ) ){
					$i = 1;
					foreach ( $sd_data['saswp-rf-page-criteria-multiple'] as $value ) {
					?>
						<li>
							<div class="saswp-rf-form-rating-text"><?php echo esc_html( $value ); ?></div>	
							<div class="saswp-rf-form-rating-container">
							<?php 
								$sanitize_value 	=	sanitize_title( $value );
			
								$id 			=	'saswp-rf-form-multi-criteria-' . $i;
								$name 			=	'saswp-rf-form-rating-' . $sanitize_value;

							?>
								<div class="saswp-rating-container">
									<div id="<?php echo esc_attr( $id ) ?>"></div>
									<div class="saswp-rateyo-counter"></div>
									<input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="5" />
								</div>
							</div>
						</li>
						<br>
					<?php
						$i++;
					}
				}else{
				?>
					<li>
						<div class="saswp-rf-form-rating-text"><?php echo esc_html__( 'Rating', 'schema-and-structured-data-for-wp' ); ?></div>
						<div class="saswp-rf-form-rating-container">
							<div class="saswp-rating-container">
								<div id="saswp-rf-form-rating"></div>
								<div class="saswp-rateyo-counter"></div>
								<input type="hidden" name="saswp_rf_form_rating" value="5" />
							</div>
						</div>
					</li>
				<?php
				}
				?>
			</ul>
		</div>

		<?php
		if ( ! empty( $sd_data['saswp-rf-page-settings-pros-cons'] ) ) {
		?>
			<div class="saswp-rf-form-group saswp-rf-form-hide-reply">
				<div class="saswp-rf-form-pros-cons-wrapper">

					<div class="saswp-rf-form-pros-items saswp-rf-form-pros">
						<h4 class="saswp-rf-form-input-title">
							<span class="saswp-rf-form-item-icon"><i class="dashicons dashicons-thumbs-up"></i></span>
							<span class="saswp-rf-form-item-text"><?php echo esc_html__( 'PROS', 'schema-and-structured-data-for-wp' ); ?></span>
						</h4>
						<div id="saswp-rf-form-pros-field-wrapper">
							<div class="saswp-rf-form-input-filed">
								<span class="saswp-rf-form-remove-field">+</span>
								<input type="text" class="form-control" name="saswp_rf_form_pros[]" placeholder="<?php echo esc_html__( 'Write Pros', 'schema-and-structured-data-for-wp' ); ?>">
							</div>
						</div>
						<div class="saswp-rf-form-add-field" id="saswp-rf-form-add-pros-field"><i class="dashicons dashicons-plus"></i><?php echo esc_html__( 'Add Pros', 'schema-and-structured-data-for-wp' ); ?></div>
					</div>

					<div class="saswp-rf-form-cons-items saswp-rf-form-cons">
						<h4 class="saswp-rf-form-input-title">
							<span class="saswp-rf-form-item-icon"><i class="dashicons dashicons-thumbs-down"></i></span>
							<span class="saswp-rf-form-item-text"><?php echo esc_html__( 'Cons', 'schema-and-structured-data-for-wp' ); ?></span>
						</h4>
						<div id="saswp-rf-form-cons-field-wrapper">
							<div class="saswp-rf-form-input-filed">
								<span class="saswp-rf-form-remove-field">+</span>
								<input type="text" class="form-control" name="saswp_rf_form_cons[]" placeholder="<?php echo esc_html__( 'Write Cons', 'schema-and-structured-data-for-wp' ); ?>">
							</div>
						</div>
						<div class="saswp-rf-form-add-field" id="saswp-rf-form-add-cons-field"><i class="dashicons dashicons-plus"></i><?php echo esc_html__( 'Add Cons', 'schema-and-structured-data-for-wp' ); ?></div>
					</div>

				</div>
			</div>
		<?php		
		}
		?>
		<div class="saswp-rf-form-media-buttons">
			<?php

			if ( ! empty( $sd_data['saswp-rf-page-settings-image-review'] ) ) {
				?>
				<div class="saswp-rf-form-image-media-groups">
					<div class="saswp-rf-form-group saswp-rf-form-hide-reply">
						<div class="saswp-rf-form-preview-imgs"></div>
					</div>
					<div class="saswp-rf-form-group saswp-rf-form-media-form-group saswp-rf-form-hide-reply">
						<div class="saswp-rf-form-button-label">
							<label class="saswp-rf-form-input-image-label"><?php echo esc_html__( 'Upload Image', 'schema-and-structured-data-for-wp' ); ?></label>
						</div>

						<div class="saswp-rf-form-image-button">
							<div class="saswp-rf-form-multimedia-upload">
								<div class="saswp-rf-form-upload-box" id="saswp-rf-form-upload-box-image">
									<span><?php echo esc_html__( 'Choose Image', 'schema-and-structured-data-for-wp' ); ?></span>
								</div>
							</div>
							<input type="file" id="saswp-rf-form-image" accept="image/*" style="display:none">
							<div class="saswp-rf-form-image-error"></div>
						</div>
					</div>
				</div>
				<?php
			}

			if ( ! empty( $sd_data['saswp-rf-page-settings-video-review'] ) ) {
				?>
				<div class="saswp-rf-form-video-media-groups">
					<div class="saswp-rf-form-group saswp-rf-form-hide-reply">
						<div class="saswp-rf-form-preview-videos"></div>
					</div>

					<div class="saswp-rf-form-group saswp-rf-form-media-form-group saswp-rf-form-hide-reply">
						<div class="saswp-rf-form-button-label">
							<label class="saswp-rf-form-input-video-label"><?php echo esc_html__( 'Upload Video', 'schema-and-structured-data-for-wp' ); ?></label>
						</div>

						<div class="saswp-rf-form-video-source-selector">
							<select name="saswp-rf-form-video-source" id="saswp-rf-form-video-source" class="saswp-rf-form-control">
								<option value="self"><?php echo esc_html__( 'Hosted Video', 'schema-and-structured-data-for-wp' ); ?></option>
								<option value="external"><?php echo esc_html__( 'External Video', 'schema-and-structured-data-for-wp' ); ?></option>
							</select>
						</div>

						<div class="saswp-rf-form-source-video">
							<div class="saswp-rf-form-multimedia-upload">
								<div class="saswp-rf-form-upload-box" id="saswp-rf-form-upload-box-video">
									<span><?php echo esc_html__( 'Choose Video', 'schema-and-structured-data-for-wp' ); ?></span>
								</div>
							</div>
							<input type="file" id="saswp-rf-form-video" accept="video/*" style="display:none">
							<div class="saswp-rf-form-video-error"></div>
						</div>
					</div>

					<div class="saswp-rf-form-group saswp-rf-form-source-external saswp-rf-form-hide-reply">
						<label class="saswp-rf-form-input-label" for="saswp-rf-form-external-video"><?php echo esc_html__( 'External Video Link', 'schema-and-structured-data-for-wp' ); ?></label>
						<input id="saswp-rf-form-external-video" class="saswp-rf-form-control" placeholder="https://www.youtube.com/watch?v=668nUCeBHyY" name="saswp-rf-form-external-video" type="text">
					</div>
				</div>
				<?php
			}
		?>
		</div>
		<?php

		$anonymous_review = ( isset( $sd_data['saswp-rf-page-settings-anonymus-review'] ) && $sd_data['saswp-rf-page-settings-anonymus-review'] == '1' );
		if ( $anonymous_review ) {
			?>

			<div class="saswp-rf-form-group saswp-rf-form-hide-reply">
				<div class="saswp-rf-form-check">
					<input type="checkbox" class="saswp-rf-form-checkbox" name="saswp-rf-form-anonymous" id="saswp-rf-form-anonymous">
					<label for="saswp-rf-form-anonymous" class="saswp-rf-form-checkbox-label"><?php echo esc_html__( 'Review anonymously', 'schema-and-structured-data-for-wp' ); ?></label>
				</div>
			</div>
			<?php
		}

		wp_nonce_field('saswp_rf_form_action_nonce', 'saswp_rf_form_nonce');
		return ob_get_clean();
	}

	/**
	 * Save comment review meta data
	 * @param 	$comment_id 	integer
	 * @since 	1.45
	 * */
	public function save_comment_meta( $comment_id ) {
		
		global $sd_data;
		if ( ! isset( $_POST['saswp_rf_form_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['saswp_rf_form_nonce'], 'saswp_rf_form_action_nonce') ) {
			return;
		}

		if ( isset( $_POST['saswp_review_form_title'] ) ) {
			$comment_title 		=	sanitize_text_field( wp_unslash( $_POST['saswp_review_form_title'] ) );
			update_comment_meta( $comment_id, 'saswp_review_form_title', $comment_title );
		}

		if ( isset( $_POST['saswp_rf_form_rating'] ) ) {
			$comment_rating 	=	floatval( $_POST['saswp_rf_form_rating'] );
			update_comment_meta( $comment_id, 'rating', $comment_rating );
			update_comment_meta( $comment_id, 'review_rating', $comment_rating );
		}else{
			if ( ! empty( $sd_data['saswp-rf-page-criteria-multiple'] ) && is_array( $sd_data['saswp-rf-page-criteria-multiple'] ) ) {

				$multi_criteria_rating 	=	array();
				foreach ( $sd_data['saswp-rf-page-criteria-multiple'] as $key => $value ) {

					$sanitize_value 	=	sanitize_title( $value );
					$name 			=	'saswp-rf-form-rating-' . $sanitize_value;
					if ( isset( $_POST[ $name ] ) ) {
						$multi_criteria_rating[ $sanitize_value ] 	=	floatval( $_POST[ $name ] );
					}		
				}

				if ( ! empty( $multi_criteria_rating ) ) {
					update_comment_meta( $comment_id, 'saswp_rf_form_multi_rating', $multi_criteria_rating );
					$comment_rating 	=	array_sum( $multi_criteria_rating );
					$comment_rating 	=	$comment_rating / count( $multi_criteria_rating );
					$comment_rating 	=	round( $comment_rating, 1 );
					update_comment_meta( $comment_id, 'rating', $comment_rating );
					update_comment_meta( $comment_id, 'review_rating', $comment_rating );
				}

			}
		}

		if ( ! empty( $_POST['saswp_rf_form_pros'] ) && is_array( $_POST['saswp_rf_form_pros'] ) && ! empty( $_POST['saswp_rf_form_pros'][0] ) ) {
			$comment_pros 		=	array_map( 'sanitize_text_field', $_POST['saswp_rf_form_pros'] );
			update_comment_meta( $comment_id, 'saswp_rf_form_pros', $comment_pros );
		}

		if ( ! empty( $_POST['saswp_rf_form_cons'] ) && is_array( $_POST['saswp_rf_form_cons'] ) && ! empty( $_POST['saswp_rf_form_cons'][0] ) ) {
			$comment_cons 		=	array_map( 'sanitize_text_field', $_POST['saswp_rf_form_cons'] );
			update_comment_meta( $comment_id, 'saswp_rf_form_cons', $comment_cons );
		}

		// add image & video
		$attachments = [];
		if ( isset( $_POST['saswp_rf_form_attachment']['imgs'] ) && ( '' !== $_POST['saswp_rf_form_attachment']['imgs'] ) ) {
			$attachments['imgs'] = array_map( 'absint', $_POST['saswp_rf_form_attachment']['imgs'] );
		}

		if ( isset( $_POST['saswp-rf-form-video-source'] ) && $_POST['saswp-rf-form-video-source'] == 'self' ) {
			if ( isset( $_POST['saswp_rf_form_attachment'] ) && ( ! empty( $_POST['saswp_rf_form_attachment']['videos'] ) ) ) {
				$attachments['videos']       = array_map( 'absint', $_POST['saswp_rf_form_attachment']['videos'] );
				$attachments['video_source'] = 'self';
			}
		} elseif ( isset( $_POST['saswp-rf-form-video-source'] ) && $_POST['saswp-rf-form-video-source'] == 'external' ) {
			if ( isset( $_POST['saswp-rf-form-external-video'] ) && ( '' !== $_POST['saswp-rf-form-video-source'] ) ) {
				$attachments['videos']       = [ esc_url( $_POST['saswp-rf-form-external-video'] ) ];
				$attachments['video_source'] = 'external';
			}
		}

		if ( isset( $attachments['imgs'] ) || isset( $attachments['videos'] ) ) {
			update_comment_meta( $comment_id, 'saswp_rf_form_attachment', $attachments );
		}

		// add anonymous
		if ( isset( $_POST['saswp-rf-form-anonymous'] ) ) {
			update_comment_meta( $comment_id, 'saswp-rf-form-anonymous', 1 );
		}

	}

	/**
	 * Replace wordpress default comment template with custom template
	 * @param 	$comment_template 	filename
	 * @return 	$comment_template 	filename
	 * @since 	1.45
	 * */
	public function render_template( $comment_template ) {

		global $post;
		if ( ! ( is_singular() && ( have_comments() || 'open' == $post->comment_status ) ) ) {
			return $comment_template;
		}

		if ( $this->check_support() && saswp_check_stars_rating() ) {
			$comment_template 	=	SASWP_DIR_NAME . '/modules/reviews/templates/review-template.php';
		}

		return $comment_template;

	}

	/**
	 * Get review form comments count
	 * @param 	$post_id 			integer
	 * @return 	$review_comments 	integer
	 * @since 	1.45
	 * */
	public static function get_all_ratings( $post_id ) {
		
		$args = [
            'meta_query' => array( 
                array( 
                  'key' => 'rating', 
                  'compare' => 'EXISTS', 
                ), 
            ), 
        ];
        $review_comments = get_approved_comments( $post_id, $args );
    	
        if ( $review_comments ) {
            return count( $review_comments );
        }

	}

	/**
	 * Get average rating of all comments
	 * @param 	$post_id 		integer
	 * @return 	$total_rating 	integer
	 * @sincce 	1.45
	 * */
	public static function get_average_rating( $post_id ) {
        $review_comments 	= 	get_approved_comments( $post_id );
    
        if ( $review_comments ) {

            $i 				=	0;
            $total_rating 	=	0;

            foreach( $review_comments as $comment ){
                $rating = get_comment_meta( $comment->comment_ID, 'rating', true );
                if( isset( $rating ) && '' !== $rating ) {
                    $i++;
                    $total_rating += $rating;
                }
            }
    
            if ( 0 === $i ) {
                return false;
            } else {
                return round( $total_rating / $i, 1 );
            }
        } else {
            return false;
        }
    }

    public static function summary_review_stars( $average_rating ) {

		ob_start();
		for ( $x = 0; $x < 5; $x++ ) {
			if ( floor( $average_rating ) - $x >= 1 ) {
				echo '<i class="dashicons dashicons-star-filled"></i>';
			} elseif ( $average_rating - $x > 0 ) {
				echo '<i class="dashicons dashicons-star-half"></i>';
			} else {
				echo '<i class="dashicons dashicons-star-empty"></i>';

			}
		}

		return ob_get_clean();

	}

	/**
	 * Get multi criteria rating
	 * @param 	$post_id 			integer
	 * @return 	$criteria_name_avg 	mixed
	 * @since 	1.45
	 * */
	public static function get_multi_criteria_average( $post_id ) { 
        
        $criteria_total_sum = $criteria_avg = $criteria_name_avg = []; 

        $comments = get_approved_comments( $post_id ); 
        if ( $comments ) { 
            $i = 0;
            // get total of each criteria by comments
            foreach( $comments as $comment_key => $comment ) {
                $rating = get_comment_meta( $comment->comment_ID, 'saswp_rf_form_multi_rating', true );
                if(  isset( $rating ) && '' !== $rating ) {
                    if( is_array( $rating ) && count( $rating )){
                        $i++;
                    }
                }

                //calculate criteria 
                if ( $rating ) {
                    foreach( $rating as $rate_key => $rate ) {   
                        if ( isset( $criteria_total_sum[$rate_key] ) ) {
                            $criteria_total_sum[$rate_key] += $rate;
                        } else {
                            $criteria_total_sum[$rate_key] = $rate;
                        } 
                    }  
                }
            } 

            if ( ! empty( $criteria_total_sum ) && is_array( $criteria_total_sum ) ) {
	            // get avg of criteria
	            foreach ($criteria_total_sum as $c_key => $value) {
	                $criteria_avg[$c_key]['avg'] = round( $value / $i, 1 );
	            }
	        }else{
	        	$criteria_avg 	=	[];
	        }
            
            return $criteria_avg;

        } else {
            return [];
        }
    }

    /**
     * Render review template
     * @param 	$comment 	array
     * @param 	$args 		array
     * @param 	$depth 		integer
     * @since 	1.45
     * */
    public static function comment_list( $comment, $args, $depth ) { 

    	global $sd_data;
        extract($args, EXTR_SKIP);
        if ( 'div' == $args['style'] ) {
            $tag = 'div';
            $add_below = 'comment';
        } else {
            $tag = 'li';
            $add_below = 'div-comment';
        } 
    ?>
    <<?php echo esc_attr( $tag ); ?> <?php comment_class( empty( $args['has_children'] ) ? 'saswp-rf-template-main-review' : 'parent saswp-rf-template-main-review' ) ?> id="div-comment-<?php comment_ID() ?>"> 
        <?php  
             
            $layout = isset( $sd_data['saswp-rf-page-review-layout'] ) ? $sd_data['saswp-rf-page-review-layout'] : 'one';  

            require SASWP_DIR_NAME . '/modules/reviews/templates/review-layout/review-'.$layout.'-template.php'; 

    }

    /**
     * Modify comment time and render
     * @param 	$comment 	WP_Comment
     * @since 	1.45
     * */
    public static function render_comment_time( $comment ) {
    	
    	global $sd_data;
		$human_readable_time  = isset( $sd_data['saswp-rf-page-settings-human-time-diff'] ) && $sd_data['saswp-rf-page-settings-human-time-diff'] == '1' ? false : true;
		
		if( $human_readable_time ){
			$time =  human_time_diff( strtotime( $comment->comment_date ), current_time( 'timestamp') ) . ' ' . esc_html__('ago', 'schema-and-structured-data-for-wp' ); 
		}else{
			$time =  date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) , strtotime( $comment->comment_date )  );  ;
		}
		return $time ;

    }

    /**
     * Handle review upload image
     * @since 	1.45
     * */
    public function saswp_image_upload() {

		if ( ! isset( $_POST['saswp_rf_form_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['saswp_rf_form_nonce'], 'saswp_rf_form_action_nonce') ) {
			return;
		}

		$img_max_size = 1024;

		$file               = $_FILES['saswp-rf-form-image'];
		$allowed_file_types = [ 'image/jpg', 'image/jpeg', 'image/png', 'image/gif' ];
		// Allowed file size -> 2MB
		$allowed_file_size = $img_max_size * 1024;

		if (! empty($file['name'])) {
			// Check file type
			if (! in_array($file['type'], $allowed_file_types)) {
				$valid_file_type = str_replace('image/', '', implode(', ', $allowed_file_types));
				$error_file_type = str_replace('image/', '', $file['type']);

				wp_send_json_error(['msg' => sprintf(esc_html__('Invalid file type: %s. Supported file types: %s', 'schema-and-structured-data-for-wp'), $error_file_type, $valid_file_type)]);
			}

			// Check file size
			if ($file['size'] > $allowed_file_size) {
				wp_send_json_error(['msg' => sprintf(esc_html__('File is too large. Max. upload file size is %s', 'schema-and-structured-data-for-wp'), self::format_bytes($allowed_file_size))]);
			}

			if (! function_exists('wp_handle_upload')) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
			}
			$upload_overrides = ['test_form' => false];
			$uploaded         = wp_handle_upload($file, $upload_overrides);

			if ($uploaded && ! isset($uploaded['error'])) {
				$filename = $uploaded['file'];
				$filetype = wp_check_filetype(basename($filename), null);

				$attach_id = wp_insert_attachment(
					[
						'guid'            => $uploaded['url'],
						'post_title'      => sanitize_text_field(preg_replace('/\.[^.]+$/', '', basename($filename))),
						'post_excerpt'    => '',
						'post_content'    => '',
						'post_mime_type'  => sanitize_text_field($filetype['type']),
						'post_status'     => 'reivew-inherit',
						'comments_status' => 'closed',
					],
					$uploaded['file'],
					0
				);

				$file_info = [];
				if (! is_wp_error($attach_id)) {
					wp_update_attachment_metadata($attach_id, wp_generate_attachment_metadata($attach_id, $filename));
					update_post_meta($attach_id, 'attach_type', 'review');

					$file_info = [
						'id'  => $attach_id,
						'url' => wp_get_attachment_image_url($attach_id, 'thumbnail'),
					];
				}

				wp_send_json_success(['file_info' => $file_info]);
			} else {
				/*
				 * Error generated by _wp_handle_upload()
				 * @see _wp_handle_upload() in wp-admin/includes/file.php
				 */
				wp_send_json_error(['msg' => $uploaded['error']]);
			}
		}
	}

	/**
	 * Return image size
	 * @param 	bytes
	 * @return 	bytes
	 * @since 	1.45
	 * */
	public static function format_bytes($bytes) {
		$label = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
		for ($i = 0; $bytes >= 1024 && $i < (count($label) - 1); $bytes /= 1024, $i++);

		return  round($bytes, 2) . ' ' . $label[$i];
	}

	/**
	 * Delete attached review file form review form
	 * @since 	1.45
	 * */
	public function saswp_remove_file() {

		if ( ! isset( $_POST['saswp_rf_form_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['saswp_rf_form_nonce'], 'saswp_rf_form_action_nonce') ) {
			return;
		}
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$attachment_id = isset( $_REQUEST['attachment_id'] ) ? absint( $_REQUEST['attachment_id'] ) : '';

		if ( ! current_user_can( 'delete_post', $attachment_id ) ) {
			return;
		}

		$deleted = wp_delete_attachment( $attachment_id );
		if ($deleted) {
			wp_send_json_success();
		} else {
			wp_send_json_error();
		}

	}

	/**
	 * Self video popup
	 * @since 	1.45
	 * */	
	public function saswp_self_video_popup() {
		
		if ( ! isset( $_POST['saswp_rf_form_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['saswp_rf_form_nonce'], 'saswp_rf_form_action_nonce') ) {
			return;
		}
		$video_url = isset($_REQUEST['video_url']) ? esc_url($_REQUEST['video_url']) : null;
		ob_start();

		echo '<div class="saswp-rf-modal">';
		echo '<div class="saswp-rf-form saswp-rf-review-popup">';
		echo '<div class="saswp-rf-form-self-video"><video src="' . $video_url . '"  autoplay controls /></div>';

		echo '</div>';
		echo '</div>'; //modal

		$edit_form = ob_get_clean();
		wp_send_json_success($edit_form);
	
	}

	/**
	 * Handle review upload video
	 * @since 	1.45
	 * */
	public function saswp_video_upload() {
		if ( ! isset( $_POST['saswp_rf_form_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['saswp_rf_form_nonce'], 'saswp_rf_form_action_nonce') ) {
			return;
		}

		$file               = $_FILES['saswp-rf-form-video'];
		$allowed_file_types = ['video/mp4', 'video/mov', 'video/avi'];

		$video_max_size    =  2048;
		$allowed_file_size = $video_max_size * 1024;

		if (! empty($file['name'])) {
			// Check file type
			if (! in_array($file['type'], $allowed_file_types)) {
				$valid_file_type = str_replace('video/', '', implode(', ', $allowed_file_types));
				$error_file_type = str_replace('video/', '', $file['type']);

				wp_send_json_error(['msg' => sprintf(esc_html__('Invalid file type: %s. Supported file types: %s', 'schema-and-structured-data-for-wp'), $error_file_type, $valid_file_type)]);
			}

			// Check file size
			if ($file['size'] > $allowed_file_size) {
				wp_send_json_error(['msg' => sprintf(esc_html__('File is too large. Max. upload file size is %s', 'schema-and-structured-data-for-wp'), self::format_bytes($allowed_file_size))]);
			}

			if (! function_exists('wp_handle_upload')) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
			}
			$upload_overrides = ['test_form' => false];
			$uploaded         = wp_handle_upload($file, $upload_overrides);

			if ($uploaded && ! isset($uploaded['error'])) {
				$filename = $uploaded['file'];
				$filetype = wp_check_filetype(basename($filename), null);

				//Todo: think about sanitization here
				$attach_id = wp_insert_attachment(
					[
						'guid'           => $uploaded['url'],
						'post_title'     => sanitize_text_field(preg_replace('/\.[^.]+$/', '', basename($filename))),
						'post_excerpt'   => '',
						'post_content'   => '',
						'post_mime_type' => sanitize_text_field($filetype['type']),
						'post_status'    => 'inherit',
					],
					$uploaded['file'],
					0
				);

				$file_info = [];
				if (! is_wp_error($attach_id)) {
					$file_info = [
						'id'   => $attach_id,
						'name' => preg_replace('/\.[^.]+$/', '', basename($filename)),
					];
				}

				wp_send_json_success(['file_info' => $file_info]);
			} else {
				/*
				 * Error generated by _wp_handle_upload()
				 * @see _wp_handle_upload() in wp-admin/includes/file.php
				 */
				wp_send_json_error(['msg' => $uploaded['error']]);
			}
		}
	}

	/**
	 * Filter reviews
	 * @since 	1.45
	 * */
	public function saswp_review_filter() {

		if ( ! isset( $_POST['saswp_rf_form_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['saswp_rf_form_nonce'], 'saswp_rf_form_action_nonce') ) {
			return;
		}

		$sort_by   	= isset( $_REQUEST['sort_by'] ) 		? sanitize_text_field( $_REQUEST['sort_by'] ) : '';
		$filter_by 	= isset( $_REQUEST['filter_by'] ) 	? sanitize_text_field( $_REQUEST['filter_by'] ) : '';
		$cur_page  	= isset( $_REQUEST['current_page'] ) ? absint( $_REQUEST['current_page'] ) : 1;

		$max_page 	= $this->get_sorted_reviews( $sort_by, $filter_by );

		ob_start();
		$this->get_reviews( $sort_by, $filter_by );
		$review = ob_get_clean();

		$pagination = $this->paginate_comments_links( $cur_page, $max_page );
		wp_send_json_success( ['review' => $review, 'pagination' => $pagination, 'sort_by' => $sort_by] );
	}

	/**
	 * Sort the reviews and get comments from db
	 * @param 	$sort_by	string
	 * @param 	$filter_by	string
	 * @return 	$comments	array
	 * @since 	1.45
	 * */
	public function get_sorted_reviews( $sort_by, $filter_by ) {
		
		$post_id = isset( $_REQUEST['post_id'] ) ? absint( $_REQUEST['post_id'] ) : null;

		$args = [
			'post_id' => $post_id,
			'count'   => true,
		];

		switch ($sort_by) {
			case 'top_rated':

				$args['meta_key'] = 'rating';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'DESC';

				break;

			case 'low_rated':

				$args['meta_key'] = 'rating';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'ASC';

				break;

			case 'recommended':

				$args['meta_query'] = [
					'relation' => 'OR',
					[
						'key'     => 'recommended',
						'value'   => '1',
						'compare' => '=',
					],
				];

				break;

			case 'highlighted':

				$args['meta_query'] = [
					'relation' => 'OR',
					[
						'key'     => 'highlight',
						'value'   => '1',
						'compare' => '=',
					],
				];

				break;

			case 'oldest_first':

				$args['order'] = 'ASC';

				break;
		}

		if ($filter_by) {
			$filter_by_value = [1, 5];
			switch ($filter_by) {
				case '5': $filter_by_value = [4.01, 5]; break;
				case '4': $filter_by_value = [3.01, 4]; break;
				case '3': $filter_by_value = [2.01, 3]; break;
				case '2': $filter_by_value = [1.01, 2]; break;
				case '1': $filter_by_value = [0.01, 0.99]; break;
			}

			$args['meta_query'] = [
				[
					'key'     => 'rating',
					'value'   => $filter_by_value,
					'compare' => 'BETWEEN',
				],
			];
		}

		return get_comments( $args );
	}

	/**
	 * Filter reviews
	 * @param 	$sort_by	string
	 * @param 	$filter_by	string
	 * @return 	$comments	array
	 * @since 	1.45
	 * */
	public function get_reviews( $sort_by, $filter_by ) {

		if ( ! isset( $_POST['saswp_rf_form_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['saswp_rf_form_nonce'], 'saswp_rf_form_action_nonce') ) {
			return;
		}


		$per_page = get_option( 'comments_per_page' );
		$cur_page = isset( $_REQUEST['current_page'] ) 	? absint( $_REQUEST['current_page'] ) : 1;
		$post_id  = isset( $_REQUEST['post_id'] ) 		? absint( $_REQUEST['post_id'] ) : null;
		$cur_page = isset( $_REQUEST['pagi_num'] ) 		? ( $cur_page - 1) : $cur_page;
		$offset   = $cur_page * $per_page;

		$args = [
			'number'  => $per_page,
			'post_id' => $post_id,
            'status'  => 'approve',
		];

		if (isset($_REQUEST['pagi_num'])) {
			//TODO: check it later
			//if ( !$sort_by ) {
			$args['offset'] = $offset;
		}

		switch ($sort_by) {
			case 'top_rated':

				$args['meta_key'] = 'rating';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'DESC';

				break;

			case 'low_rated':

				$args['meta_key'] = 'rating';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'ASC';

				break;

			case 'recommended':

				$args['meta_query'] = [
					'relation' => 'OR',
					[
						'key'     => 'recommended',
						'value'   => '1',
						'compare' => '=',
					],
				];

				break;

			case 'highlighted':

				$args['meta_query'] = [
					'relation' => 'OR',
					[
						'key'     => 'highlight',
						'value'   => '1',
						'compare' => '=',
					],
				];

				break;

			case 'oldest_first':

				$args['order'] = 'ASC';

				break;
		}

		if ($filter_by) {
			$filter_by_value = [1, 5];
			switch ($filter_by) {
				case '5': $filter_by_value = [4.01, 5]; break;
				case '4': $filter_by_value = [3.01, 4]; break;
				case '3': $filter_by_value = [2.01, 3]; break;
				case '2': $filter_by_value = [1.01, 2]; break;
				case '1': $filter_by_value = [1, 1.99]; break;
			}

			$args['meta_query'] = [
				[
					'key'     => 'rating',
					'value'   => $filter_by_value,
					'compare' => 'BETWEEN',
				],
			];
		}

		$comments = get_comments( $args );

		wp_list_comments([
			'style'      => 'li',
			'short_ping' => true,
			'callback'   => [self::class, 'comment_list'],
		], $comments);
	}

	/**
	 * Generate pagination link
	 * @param 	$cur_page 	integer
	 * @param 	$max_page 	integer
	 * @param 	$args 		array
	 * @since 	1.45
	 * */
	public function paginate_comments_links( $cur_page, $max_page, $args = [] ) {

		// if ( $cur_page >= $max_page ) {
		// 	return;
		// }

		global $wp_rewrite;
		$args    = ['prev_text' => '<i class="dashicons dashicons-arrow-left-alt2"></i>', 'next_text' => '<i class="dashicons dashicons-arrow-right-alt2"></i>'];
		
		$post_id = isset( $_REQUEST['post_id'] ) ? absint( $_REQUEST['post_id'] ) : null;

		$defaults = [
			'base'         => add_query_arg( 'cpage', '%#%' ),
			'format'       => '',
			'total'        => $max_page,
			'current'      => $cur_page,
			'echo'         => true,
			'type'         => 'plain',
			'add_fragment' => '#comments',
		];

		if ( $wp_rewrite->using_permalinks() ) {
			$defaults['base'] = user_trailingslashit( trailingslashit( get_permalink( $post_id ) ) . $wp_rewrite->comments_pagination_base . '-%#%', 'commentpaged' );
		}

		$args       = wp_parse_args( $args, $defaults );
		$page_links = paginate_links( $args );

		return $page_links;
	}

	/**
	 * Comment pagination
	 * @since 	1.45
	 * */
	public function saswp_pagination() {

		if ( ! isset( $_POST['saswp_rf_form_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['saswp_rf_form_nonce'], 'saswp_rf_form_action_nonce') ) {
			return;
		}

		$sort_by   = isset( $_REQUEST['sort_by'] ) 		? sanitize_text_field( $_REQUEST['sort_by'] ) : '';
		$filter_by = isset( $_REQUEST['filter_by'] ) 	? sanitize_text_field( $_REQUEST['filter_by'] ) : '';
		$cur_page  = isset( $_REQUEST['current_page'] ) ? absint( $_REQUEST['current_page'] ) : 1;
		if ( $sort_by ) {
			$max_page = $this->get_sorted_reviews( $sort_by, $filter_by );
		} else {
			$max_page = isset( $_REQUEST['max_page'] ) ? absint( $_REQUEST['max_page'] ) : 1;
		}

		ob_start();
		$this->get_reviews( $sort_by, $filter_by );
		$review = ob_get_clean();

		$pagination = $this->paginate_comments_links( $cur_page, $max_page );

		wp_send_json_success( ['review' => $review, 'pagination' => $pagination] );
	}

	/**
	 * Edit comment 
	 * @since 	1.45
	 * */
	public function saswp_rf_template_review_edit_form() {

		if ( ! isset( $_POST['saswp_rf_form_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['saswp_rf_form_nonce'], 'saswp_rf_form_action_nonce') ) {
			return;
		}

		global $sd_data;

		$comment_post_id 	= isset( $_REQUEST['comment_post_id'] ) 	? absint( $_REQUEST['comment_post_id'] ) : null;
		$comment_id      	= isset( $_REQUEST['comment_id'] ) 		? absint( $_REQUEST['comment_id'] ) : null;
		$comment_data 		= get_comment( $comment_id );
		$review_edit 		= 'yes';
		if ( $review_edit != 'yes' || $comment_data->user_id != get_current_user_id() ) {
			wp_send_json_error( esc_html__( 'Sorry! You do not have permission.', 'schema-and-structured-data-for-wp' ) );
		}

		ob_start();
		$post_type 	= 	get_post_type( $comment_post_id );
		$get_post 	=	get_post( $comment_post_id );
		if ( ! $this->check_support( $get_post ) && saswp_check_stars_rating() ) {
			return;
		}

		$criteria       = ( isset( $sd_data['saswp-rf-page-criteria'] ) && $sd_data['saswp-rf-page-criteria'] == 'multi' );
		$multi_criteria = get_comment_meta( $comment_id, 'saswp_rf_form_multi_rating', true );

		echo '<div class="saswp-rf-modal">';
		echo '<div class="saswp-rf-form saswp-rf-review-popup">';
		echo '<h2 id="saswp-rf-title" class="saswp-rf-form-title">' . esc_html__('Edit your review', 'schema-and-structured-data-for-wp') . '</h2>';
		echo '<form action="#" method="post" class="saswp-rf-form-box">';

		if (! $comment_data->comment_parent) {
			?>
			<div class="saswp-rf-form-group saswp-rf-form-hide-reply">
				
				<div class="saswp-rf-form-group saswp-rf-form-hide-reply">
		            <input id="saswp_review_form_title" class="saswp-rf-form-control" placeholder="<?php echo esc_attr__('Title', 'schema-and-structured-data-for-wp'); ?>" name="saswp_review_form_title" value="<?php echo esc_attr( get_comment_meta( $comment_id, 'saswp_review_form_title', true ) ); ?>" type="text" value="" size="30" aria-required="true">
		        </div>
		        
		        <div class="saswp-rf-form-group">
		            <textarea id="message" class="saswp-rf-form-control" placeholder="<?php echo esc_attr__( 'Write your review', 'schema-and-structured-data-for-wp' ); ?>" name="comment" aria-required="true" rows="6" cols="45"><?php
					$comment = get_comment( intval($comment_id) );
					echo wp_kses_post( $comment->comment_content ); ?></textarea>
		        </div>

				<ul class="saswp-rf-form-rating-ul">
					<?php
					if ( $criteria && $multi_criteria ) {
						$criteria_count = 1;
						foreach ( $multi_criteria as $key => $value ) {
	
							$id 				=	'saswp-rf-edit-form-multi-criteria-' . $criteria_count;
							$name 				=	'saswp-rf-form-rating-' . $key;
							$label 				=	ucfirst( str_replace( '-', ' ',  $key ) );
							$rating             = 	! empty( $value ) ? $value : 5;
							?>
		                	<li>
			                	<div class="saswp-rf-form-rating-text"><?php echo esc_html( $label ); ?></div>	
								<div class="saswp-rf-form-rating-container">
									<div class="saswp-rating-container">
										<div id="<?php echo esc_attr( $id ) ?>"></div>
										<div class="saswp-rateyo-counter"></div>
										<input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $rating ); ?>" />
									</div>
								</div>
			                </li> 
			                <br>
		                <?php $criteria_count++;
						}
					} else { ?> 
		                <li>
		                    <?php $rt_rating = get_comment_meta( $comment_id, 'rating', true ); ?>
		                    <div class="saswp-rf-form-rating-text"><?php echo esc_html__( 'Rating', 'schema-and-structured-data-for-wp' ); ?></div>
							<div class="saswp-rf-form-rating-container">
								<div class="saswp-rating-container">
									<div id="saswp-rf-edit-form-rating"></div>
									<div class="saswp-rateyo-counter"></div>
									<input type="hidden" name="saswp_rf_form_rating" value="<?php echo esc_attr( $rt_rating ); ?>" />
								</div>
							</div>
		                </li> 
		                <?php
					}
					?>
				</ul>
			</div>
		<?php
		}
		
		if ( ! empty( $sd_data['saswp-rf-page-settings-pros-cons'] ) ) {
		?>
			<div class="saswp-rf-form-group saswp-rf-form-hide-reply">
				<div class="saswp-rf-form-pros-cons-wrapper">

					<div class="saswp-rf-form-pros-items saswp-rf-form-pros">
						<h4 class="saswp-rf-form-input-title">
							<span class="saswp-rf-form-item-icon"><i class="dashicons dashicons-thumbs-up"></i></span>
							<span class="saswp-rf-form-item-text"><?php echo esc_html__( 'PROS', 'schema-and-structured-data-for-wp' ); ?></span>
						</h4>
						<div id="saswp-rf-form-pros-field-wrapper">
								<?php 
								$pros = get_comment_meta( $comment_id, 'saswp_rf_form_pros', true );
								if ( ! empty( $pros ) && is_array( $pros ) ) {
									foreach ( $pros as $key => $value ) { 
									?>
										<div class="saswp-rf-form-input-filed">
											<span class="saswp-rf-form-remove-field">+</span>
											<input type="text" class="form-control" name="saswp_rf_form_pros[]" placeholder="<?php echo esc_html__( 'Write Pros', 'schema-and-structured-data-for-wp' ); ?>" value="<?php echo esc_attr( $value ); ?>">
										</div>
									<?php
									}
								}else{
								?>
									<div class="saswp-rf-form-input-filed">
										<span class="saswp-rf-form-remove-field">+</span>
										<input type="text" class="form-control" name="saswp_rf_form_pros[]" placeholder="<?php echo esc_html__( 'Write Pros', 'schema-and-structured-data-for-wp' ); ?>">
									</div>
								<?php
								}
								?>
							
						</div>
						<div class="saswp-rf-form-add-field" id="saswp-rf-form-add-pros-field"><i class="dashicons dashicons-plus"></i><?php echo esc_html__( 'Add Pros', 'schema-and-structured-data-for-wp' ); ?></div>
					</div>

					<div class="saswp-rf-form-cons-items saswp-rf-form-cons">
						<h4 class="saswp-rf-form-input-title">
							<span class="saswp-rf-form-item-icon"><i class="dashicons dashicons-thumbs-down"></i></span>
							<span class="saswp-rf-form-item-text"><?php echo esc_html__( 'Cons', 'schema-and-structured-data-for-wp' ); ?></span>
						</h4>
						<div id="saswp-rf-form-cons-field-wrapper">
							<?php 
								$cons = get_comment_meta( $comment_id, 'saswp_rf_form_cons', true );
								if ( ! empty( $cons ) && is_array( $cons ) ) {
									foreach ( $cons as $key => $value ) { 
									?>										
										<div class="saswp-rf-form-input-filed">
											<span class="saswp-rf-form-remove-field">+</span>
											<input type="text" class="form-control" name="saswp_rf_form_cons[]" placeholder="<?php echo esc_html__( 'Write Cons', 'schema-and-structured-data-for-wp' ); ?>" value="<?php echo esc_attr( $value ); ?>">
										</div>
									<?php
									}
								}else{
								?>
									<div class="saswp-rf-form-input-filed">
										<span class="saswp-rf-form-remove-field">+</span>
										<input type="text" class="form-control" name="saswp_rf_form_cons[]" placeholder="<?php echo esc_html__( 'Write Cons', 'schema-and-structured-data-for-wp' ); ?>">
									</div>

								<?php
								}
								?>
						</div>
						<div class="saswp-rf-form-add-field" id="saswp-rf-form-add-cons-field"><i class="dashicons dashicons-plus"></i><?php echo esc_html__( 'Add Cons', 'schema-and-structured-data-for-wp' ); ?></div>
					</div>

				</div>
			</div>
		<?php		
		}

		$image_review = ( isset( $sd_data['saswp-rf-page-settings-image-review'] ) && $sd_data['saswp-rf-page-settings-image-review'] == '10' );
		if ( $image_review === 10 ) { ?>
        <div class="saswp-rf-form-group saswp-rf-form-hide-reply">
            <div class="saswp-rf-form-preview-imgs"></div>
        </div> 
        
        <div class="saswp-rf-form-group saswp-rf-form-media-form-group saswp-rf-form-hide-reply">             

            <div>
                <label class="saswp-rf-form-input-image-label"><?php echo esc_html__( 'Upload Image', 'schema-and-structured-data-for-wp' ); ?></label>
            </div>

            <div>
                <div class="saswp-rf-form-multimedia-upload">
                    <div class="saswp-rf-form-upload-box" id="saswp-rf-form-upload-box-image"> 
                        <span><?php echo esc_html__('Upload Image', 'schema-and-structured-data-for-wp'); ?></span>
                    </div> 
                </div>
                <input type="file" id="saswp-rf-form-image" accept="image/*" style="display:none">
                <div class="saswp-rf-form-image-error"></div>
            </div>
        </div> 
        <?php }

		$video_review = ( isset( $sd_data['saswp-rf-page-settings-video-review'] ) && $sd_data['saswp-rf-page-settings-video-review'] == '10' );
		if ( $video_review === 10 ) { ?>
			<div class="saswp-rf-form-video-media-groups">
	        	<div class="saswp-rf-form-group saswp-rf-form-hide-reply">
					<div class="saswp-rf-form-preview-videos"></div>
				</div> 

		        <div class="saswp-rf-form-group saswp-rf-form-media-form-group saswp-rf-form-hide-reply">
					<div class="saswp-rf-form-button-label">
						<label class="saswp-rf-form-input-video-label"><?php echo esc_html__( 'Upload Video', 'schema-and-structured-data-for-wp' ); ?></label>
					</div>

		            <div class="saswp-rf-form-video-source-selector">
						<select name="saswp-rf-form-video-source" id="saswp-rf-form-video-source" class="saswp-rf-form-control">
							<option value="self"><?php echo esc_html__( 'Hosted Video', 'schema-and-structured-data-for-wp' ); ?></option>
							<option value="external"><?php echo esc_html__( 'External Video', 'schema-and-structured-data-for-wp' ); ?></option>
						</select>
					</div>

		            <div class="saswp-rf-form-source-video">
						<div class="saswp-rf-form-multimedia-upload">
							<div class="saswp-rf-form-upload-box" id="saswp-rf-form-upload-box-video">
								<span><?php echo esc_html__( 'Choose Video', 'schema-and-structured-data-for-wp' ); ?></span>
							</div>
						</div>
						<input type="file" id="saswp-rf-form-video" accept="video/*" style="display:none">
						<div class="saswp-rf-form-video-error"></div>
					</div>
		        </div>  

		        <div class="saswp-rf-form-group saswp-rf-form-source-external saswp-rf-form-hide-reply">
					<label class="saswp-rf-form-input-label" for="saswp-rf-form-external-video"><?php echo esc_html__( 'External Video Link', 'schema-and-structured-data-for-wp' ); ?></label>
					<input id="saswp-rf-form-external-video" class="saswp-rf-form-control" placeholder="https://www.youtube.com/watch?v=668nUCeBHyY" name="saswp-rf-form-external-video" type="text">
				</div>
			</div>
        <?php 
        } 
        ?>

        <div class="saswp-rf-form-group">
            <input name="submit" type="submit" id="submit" class="saswp-rf-form-submit-btn saswp-rf-edit-submit" value="<?php echo esc_attr__('Submit Review', 'schema-and-structured-data-for-wp'); ?>"> 
            <input type="hidden" name="action" value="saswp_rf_review_edit">
            <input type="hidden" name="comment_post_ID" value="<?php echo esc_attr($comment_post_id); ?>" id="comment_post_ID">
            <input type="hidden" name="comment_ID" value="<?php echo esc_attr($comment_id); ?>" id="comment_ID">
            <input type="hidden" name="comment_parent" id="comment_parent" value="0">

        </div>

        <?php
		echo '</form>';
		echo '</div>';
		echo '</div>'; //modal
		$edit_form = ob_get_clean();
		wp_send_json_success($edit_form);
	}

	/**
	 * Edit review
	 * @since 	1.45
	 * */
	public function saswp_rf_review_edit() {
		
		if ( ! isset( $_POST['saswp_rf_form_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['saswp_rf_form_nonce'], 'saswp_rf_form_action_nonce') ) {
			return;
		}

		$comment_id = absint($_POST['comment_ID']);
		if ( ! current_user_can( 'edit_comment', $comment_id ) ){
			$comment = get_comment( $comment_id );
            if( get_current_user_id() != $comment->user_id ){
	            return ;
            }
        }

        $this->save_comment_meta( $comment_id );

		if ( isset( $_POST['comment'] ) ) {
			// comment data
			$commentarr = [
				'comment_ID'      => intval( $comment_id ),
				'comment_content' => sanitize_text_field( $_POST['comment'] ),
			];
			// update data in the database
			$updated = wp_update_comment( $commentarr );
		}

		wp_send_json_success();

	}


	/**
	 * Generate custom css
	 * @since 	1.45
	 * */
	public function custom_style() {
		
		global $sd_data;
		$custom_css = $title_css = $text_css = $date_css = $author_css = $star_css = $meta_icon = $style_escaped = '';
		$width = $margin = $padding = '';
		if ( ! empty( $sd_data['saswp-rf-page-width'] ) ) { 
			$custom_css 	.= 'width: ' . $sd_data['saswp-rf-page-width'] . ';';	
		}
		if ( ! empty( $sd_data['saswp-rf-page-margin'] ) ) { 
			$custom_css 	.= ' margin: ' . $sd_data['saswp-rf-page-margin'] . ';';	
		}
		if ( ! empty( $sd_data['saswp-rf-page-padding'] ) ) { 
			$custom_css 	.= ' padding: ' . $sd_data['saswp-rf-page-padding'] . ';';	
		}
		
		if ( ! empty( $sd_data['saswp_rf_page_review_title'] ) ) {
		 	if ( ! empty( $sd_data['saswp_rf_page_review_title']['color'] ) ) { 
				$title_css 		.=	'color: ' . $sd_data['saswp_rf_page_review_title']['color'] . ';';	
			}
			if ( ! empty( $sd_data['saswp_rf_page_review_title']['size'] ) ) { 
				$title_css 		.=	'font-size: ' . $sd_data['saswp_rf_page_review_title']['size'] . ';';	
			}
			if ( ! empty( $sd_data['saswp_rf_page_review_title']['weight'] ) ) { 
				$title_css 		.=	'font-weight: ' . $sd_data['saswp_rf_page_review_title']['weight'] . ';';	
			}
			if ( ! empty( $sd_data['saswp_rf_page_review_title']['alignment'] ) ) { 
				$title_css 		.=	'text-align: ' . $sd_data['saswp_rf_page_review_title']['alignment'] . ';';	
			}
		}

		if ( ! empty( $sd_data['saswp_rf_page_review_text'] ) ) {
		 	if ( ! empty( $sd_data['saswp_rf_page_review_text']['color'] ) ) { 
				$text_css 		.=	'color: ' . $sd_data['saswp_rf_page_review_text']['color'] . ';';	
			}
			if ( ! empty( $sd_data['saswp_rf_page_review_text']['size'] ) ) { 
				$text_css 		.=	'font-size: ' . $sd_data['saswp_rf_page_review_text']['size'] . ';';	
			}
			if ( ! empty( $sd_data['saswp_rf_page_review_text']['weight'] ) ) { 
				$text_css 		.=	'font-weight: ' . $sd_data['saswp_rf_page_review_text']['weight'] . ';';	
			}
			if ( ! empty( $sd_data['saswp_rf_page_review_text']['alignment'] ) ) { 
				$text_css 		.=	'text-align: ' . $sd_data['saswp_rf_page_review_text']['alignment'] . ';';	
			}
		}

		if ( ! empty( $sd_data['saswp_rf_page_review_date_text'] ) ) {
		 	if ( ! empty( $sd_data['saswp_rf_page_review_date_text']['color'] ) ) { 
				$date_css 		.=	'color: ' . $sd_data['saswp_rf_page_review_date_text']['color'] . ';';	
			}
			if ( ! empty( $sd_data['saswp_rf_page_review_date_text']['size'] ) ) { 
				$date_css 		.=	'font-size: ' . $sd_data['saswp_rf_page_review_date_text']['size'] . ';';	
			}
			if ( ! empty( $sd_data['saswp_rf_page_review_date_text']['weight'] ) ) { 
				$date_css 		.=	'font-weight: ' . $sd_data['saswp_rf_page_review_date_text']['weight'] . ';';	
			}
			if ( ! empty( $sd_data['saswp_rf_page_review_date_text']['alignment'] ) ) { 
				$date_css 		.=	'text-align: ' . $sd_data['saswp_rf_page_review_date_text']['alignment'] . ';';	
			}
		}

		if ( ! empty( $sd_data['saswp_rf_page_review_author_name_text'] ) ) {
		 	if ( ! empty( $sd_data['saswp_rf_page_review_author_name_text']['color'] ) ) { 
				$author_css 		.=	'color: ' . $sd_data['saswp_rf_page_review_author_name_text']['color'] . ';';	
			}
			if ( ! empty( $sd_data['saswp_rf_page_review_author_name_text']['size'] ) ) { 
				$author_css 		.=	'font-size: ' . $sd_data['saswp_rf_page_review_author_name_text']['size'] . ';';	
			}
			if ( ! empty( $sd_data['saswp_rf_page_review_author_name_text']['weight'] ) ) { 
				$author_css 		.=	'font-weight: ' . $sd_data['saswp_rf_page_review_author_name_text']['weight'] . ';';	
			}
			if ( ! empty( $sd_data['saswp_rf_page_review_author_name_text']['alignment'] ) ) { 
				$author_css 		.=	'text-align: ' . $sd_data['saswp_rf_page_review_author_name_text']['alignment'] . ';';	
			}
		}

		if ( ! empty( $sd_data['saswp-rf-page-star-color'] ) ) { 
			$star_css 	.= 'color: ' . $sd_data['saswp-rf-page-star-color'] . ';';	
		}

		if ( ! empty( $sd_data['saswp-rf-page-meta-icon-color'] ) ) { 
			$meta_icon 	.= 'color: ' . $sd_data['saswp-rf-page-meta-icon-color'] . ';';	
		}

		if ( ! empty( $custom_css ) ) {
			$style_escaped 	.=	".saswp-rf-template-wrapper{ {$custom_css} }";
		}
		if ( ! empty( $title_css ) ) {
			$style_escaped 	.=	".saswp-rf-template-comment-box .saswp-rf-template-review-container .saswp-rf-template-review-title{ {$title_css} }";
		}
		if ( ! empty( $text_css ) ) {
			$style_escaped 	.=	".saswp-rf-template-comment-box .saswp-rf-template-review-container p{ {$text_css} }";
		}
		if ( ! empty( $date_css ) ) {
			$style_escaped 	.=	".saswp-rf-template-comment-box .saswp-rf-template-review-container .saswp-rf-template-review-meta .saswp-rf-template-review-date{ {$date_css} }";
		}
		if ( ! empty( $author_css ) ) {
			$style_escaped 	.=	".saswp-rf-template-comment-box .saswp-rf-template-review-container .saswp-rf-template-review-meta .saswp-rf-template-author-link{ {$author_css} }";
		}
		if ( ! empty( $star_css ) ) { 
			$style_escaped 	.=	".saswp-rf-template-comment-box .saswp-rf-template-review-container .saswp-rf-template-review-meta .saswp-rf-template-review-rating i{ {$star_css} }";		
		}
		if ( ! empty( $meta_icon ) ) { 
			$style_escaped 	.=	".saswp-rf-template-comment-box .saswp-rf-template-review-container i{ {$meta_icon} }";		
		}	
		return $style_escaped;
	}
	
	/**
	 * Review like and dislike
	 * @since 	1.45
	 * */
	public function saswp_review_helpful() {

		if ( ! isset( $_POST['saswp_rf_form_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['saswp_rf_form_nonce'], 'saswp_rf_form_action_nonce') ) {
			return;
		}

		if ( is_user_logged_in() ) {
			$comment_id   = isset( $_REQUEST['comment_id'] ) ? absint( $_REQUEST['comment_id'] ) : null;
			$helpful_type = ( isset( $_REQUEST['type'] ) && $_REQUEST['type'] == 'like' ) ? 'like' : 'dislike';

			if ( $comment_id ) {
				$current_user = wp_get_current_user();
				$user_id      = $current_user->ID;

				$old_helpful = get_comment_meta( $comment_id, 'saswp_rf_form_helpful_' . $helpful_type, true );
				$old_helpful = isset( $old_helpful ) ? $old_helpful : '';
				if ( $old_helpful ) {
					if (! in_array( $user_id, $old_helpful ) ) {
						$old_helpful[] = $user_id;
						update_comment_meta( $comment_id, 'saswp_rf_form_helpful_' . $helpful_type, $old_helpful );
					} else {
						if ( ( $key = array_search( $user_id, $old_helpful ) ) !== false ) {
							unset( $old_helpful[$key] );
						}
						update_comment_meta($comment_id, 'saswp_rf_form_helpful_' . $helpful_type, $old_helpful);
					}
				} else {
					$new_helpful   = [];
					$new_helpful[] = $user_id;
					update_comment_meta( $comment_id, 'saswp_rf_form_helpful_' . $helpful_type, $new_helpful );
				}

				//decrement
				$decrement_type = ( $helpful_type == 'like' ) ? 'dislike' : 'like';
				$decrement      = get_comment_meta( $comment_id, 'saswp_rf_form_helpful_' . $decrement_type, true );
				$decrement      = isset( $decrement ) ? $decrement : '';
				if ( $decrement ) {
					if ( in_array( $user_id, $decrement ) ) {
						if ( ( $key = array_search( $user_id, $decrement ) ) !== false ) {
							unset( $decrement[$key] );
						}
						update_comment_meta( $comment_id, 'saswp_rf_form_helpful_' . $decrement_type, $decrement );
					}
				}
			}
		}

		$likes 				=	get_comment_meta( $comment_id, 'saswp_rf_form_helpful_like', true );
		$dislike 			=	get_comment_meta( $comment_id, 'saswp_rf_form_helpful_dislike', true );
		$likes_cnt 			=	0;
		$dislik_cnt 		=	0;
		if ( empty( $likes ) ) {
			$likes 			=	array();
		}
		if ( empty( $dislike ) ) {
			$dislike 		=	array();
		}
		if ( ! empty( $likes ) && is_array( $likes ) ) {
			$likes_cnt 		=	count( $likes );	
		}
		if ( ! empty( $dislike ) && is_array( $dislike ) ) {
			$dislik_cnt 		=	count( $dislike );	
		}

		wp_send_json_success(['likes' => $likes_cnt, 'dislikes' => $dislik_cnt ]);
	}
	
	/**
	 * Highlight the review
	 * @since 	1.45
	 * */
	public function saswp_review_highlight() {

		if ( ! isset( $_POST['saswp_rf_form_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['saswp_rf_form_nonce'], 'saswp_rf_form_action_nonce') ) {
			return;
		}

		if ( current_user_can('administrator') ) {
			
			$comment_id = isset( $_REQUEST['comment_id'] ) ? absint( $_REQUEST['comment_id'] ) : null;
			$highlight = ( isset($_REQUEST['highlight'] ) && $_REQUEST['highlight'] == 'yes' ) ? 1 : 0;
			if ( $comment_id ) {
				update_comment_meta( $comment_id, 'saswp-rf-template-review-highlight', $highlight );
			}
		}
		wp_send_json_success();
	}

	/**
	 * Check if any purchase is made through woocommerce
	 * @param 	$email 	string
	 * @return 	boolean 
	 * @since 	1.45
	 * */
	public static function woocommerce_verified_customer( $email ) {
		
		$status 	=	false;

		if ( function_exists( 'wc_get_orders' ) && ! empty( $email ) ) {

			$args = array(
		        'limit'    => 1,        
		        'customer' => $email,
		        'status'       => array( 'wc-processing', 'wc-completed', 'wc-cancelled', 'wc-refunded' ),
		        'return'   => 'ids',   
		    );

		    $orders = wc_get_orders( $args );
		    
		    if ( ! empty( $orders ) && is_array( $orders ) ) {
		    	$status 	=	true;
		    }

		}

		return $status;

	}

	/**
	 * Check if any purchase is made through Easy Digital Downloads (EDD)
	 * @param 	$email 	string
	 * @return 	boolean 
	 * @since 	1.45
	 * */
	public static function edd_verified_customer( $email ) {
		
		$status 	=	false;

		if ( function_exists( 'edd_get_payments' ) ) {

			$payments = edd_get_payments(array(
		        'number' => -1,
		        'email'  => $email,
		        'status' => 'publish',
		    ));

		    if ( ! empty( $payments ) ) {
		    	$status 	=	true;
		    }

		}

		return $status;

	}

}		

new SASWP_Review_Feature_Frontend();