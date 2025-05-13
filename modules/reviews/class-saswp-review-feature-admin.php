<?php
/**
 * Reviews Feature  Class
 *
 * @author   Magazine3
 * @class 	 SASWP_Review_Feature_Admin
 * @path     reviews/reviews_form
 * @Version 1.46
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class SASWP_Review_Feature_Admin {

	private static $instance = null;

	// Get the single instance
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

	private function __construct() {
		
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_metabox_scripts' ) );
		add_action( 'admin_post_saswp_review_feature_form_submission', array( $this, 'save_data' ) );
		add_action( 'add_meta_boxes_comment', array( $this, 'add_meta_box' ) );
		add_action('edit_comment', array( $this, 'save_edit_comment_data' ) );

	}

	/**
	 * Set options data
	 * */
	public function deafult_options(  ) {
		
		$options 	=	array(
							'saswp-rf-page-post-type'					=>	array(),
							'saswp-rf-page-criteria-multiple'			=>	array(),
							'saswp-rf-page-criteria'					=>	'single',
							'saswp-rf-page-criteria-multiple'			=>	array( 'Quality', 'Price', 'Service' ),
							'saswp-rf-page-summary-layout'				=>	'one',
							'saswp-rf-page-review-layout'				=>	'one',
							'saswp-rf-page-pagination-type'				=>	'number',
							'saswp-rf-page-settings-title'				=>	0,
							'saswp-rf-page-settings-human-time-diff'	=>	0,
							'saswp-rf-page-settings-url-disable'		=>	0,
							'saswp-rf-page-settings-image-review'		=>	0,
							'saswp-rf-page-settings-video-review'		=>	0,
							'saswp-rf-page-settings-pros-cons'			=>	0,
							'saswp-rf-page-settings-pros-cons-limit'	=>	3,
							// 'saswp-rf-page-settings-highlight-review'	=>	0,
							'saswp-rf-page-settings-social-review'		=>	0,
							'saswp-rf-page-settings-review-like'		=>	0,
							'saswp-rf-page-settings-review-dislike'		=>	0,
							'saswp-rf-page-settings-anonymus-review'	=>	0,
							'saswp-rf-page-settings-anonymus-email'		=>	0,
							'saswp-rf-page-settings-anonymus-author'	=>	0,
							'saswp-rf-page-settings-purchase-badge'		=>	0,
							'saswp-rf-page-settings-filter'				=>	0,
							'saswp-rf-page-settings-filter-options'		=>	array( 'top_rated', 'low_rated', 'latest_first', 'oldest_first' ),
							'saswp-rf-page-parent-class'				=>	'',
							'saswp-rf-page-width'						=>	'',
							'saswp-rf-page-margin'						=>	'',
							'saswp-rf-page-padding'						=>	'',
							'saswp_rf_page_review_title'				=>	array(
																				'color' 		=>	'#000',
																				'size' 			=>	'16px',
																				'weight' 		=>	'600',
																				'alignment' 	=>	'',
																			),
							'saswp_rf_page_review_text'					=>	array(
																				'color' 		=>	'#646464',
																				'size' 			=>	'16px',
																				'weight' 		=>	'',
																				'alignment' 	=>	'',
																			),
							'saswp_rf_page_review_date_text'			=>	array(
																				'color' 		=>	'#646464',
																				'size' 			=>	'14px',
																				'weight' 		=>	'',
																				'alignment' 	=>	'',
																			),
							'saswp_rf_page_review_author_name_text'		=>	array(
																				'color' 		=>	'#646464',
																				'size' 			=>	'14px',
																				'weight' 		=>	'',
																				'alignment' 	=>	'',
																			),
							'saswp-rf-page-star-color' 					=>	'#ffb300',	
							'saswp-rf-page-meta-icon-color' 			=>	'#646464',	
						);

		return $options;
	}	

	/**
	 * Set default data
	 * @since 	1.46
	 * */
	public function set_default_data() {
		
		global $sd_data; 
                
        $sd_data = get_option( 'sd_rf_data', $this->deafult_options() );     

        return $sd_data;

	}

	/**
	 * Enqueue admin scripts and styles
	 * @param 	$hook 	string
	 * @since 	1.46
	 * */
	public function enqueue_metabox_scripts( $hook ) {
		
		global $sd_data;

		if ( ( $hook === 'saswp_page_structured_data_options' ) || ( $hook == 'comment.php' ) ) {

	        wp_enqueue_script( 'postbox' );
	        wp_enqueue_script( 'dashboard' ); // Optional: for drag UI
	        wp_enqueue_style( 'dashboard' );  // Optional: for styling

	        wp_enqueue_style( 'saswp-rf-page-admin-style', SASWP_PLUGIN_URL . 'admin_section/css/'.(SASWP_ENVIRONMENT == 'production' ? 'saswp-review-feature-admin.min.css' : 'saswp-review-feature-admin.css'), false , SASWP_VERSION );
	        wp_enqueue_style('saswp-select2-style', SASWP_PLUGIN_URL. 'admin_section/css/select2.min.css' , false, SASWP_VERSION);

	        wp_register_script( 'saswp-rf-page-admin-script', SASWP_PLUGIN_URL . 'admin_section/js/'.(SASWP_ENVIRONMENT == 'production' ? 'saswp-review-feature-admin.min.js' : 'saswp-review-feature-admin.js'), array( 'jquery' ), SASWP_VERSION , true );
	        wp_enqueue_script( 'saswp-rf-page-admin-script' );
        	wp_enqueue_script('select2', SASWP_PLUGIN_URL. 'admin_section/js/select2.min.js', array( 'jquery'), SASWP_VERSION, true);

	    }

	    if ( $hook == 'comment.php' ) { 

	    	$comment_id 	=	isset( $_GET['c'] ) ? intval( $_GET['c'] ) : 0;
	    	$post_id 		=	0;
	    	
	    	if ( $comment_id > 0 ) {
	    		$comment  	=	get_comment( $comment_id );
	    		if ( is_object( $comment ) && ! empty( $comment->comment_post_ID ) ) {
	    			$post_id 	=	$comment->comment_post_ID;
	    		}
	    	}

	    	wp_enqueue_style( 'saswp-rf-style', SASWP_PLUGIN_URL . 'admin_section/css/'.(SASWP_ENVIRONMENT == 'production' ? 'saswp-review-feature-front.min.css' : 'saswp-review-feature-front.css'), false , SASWP_VERSION );

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
				'post_id' 									=> $post_id, 
				'current_page' 								=> get_query_var( 'cpage' ) ? get_query_var( 'cpage' ) : 1, 
				'default_rating' 							=> 5,
				'admin_ry_rating' 							=> 1,
			);
			wp_register_script( 'saswp-rf-frontend-script', SASWP_PLUGIN_URL . 'admin_section/js/'.(SASWP_ENVIRONMENT == 'production' ? 'saswp-review-feature-frontend.min.js' : 'saswp-review-feature-frontend.js'), array( 'jquery' ), SASWP_VERSION , true );
			wp_localize_script( 'saswp-rf-frontend-script', 'saswp_rf_localize_data', $data );
		    wp_enqueue_script( 'saswp-rf-frontend-script' );

		    wp_enqueue_style( 'saswp-frontend-css', SASWP_PLUGIN_URL . 'admin_section/css/'.(SASWP_ENVIRONMENT == 'production' ? 'saswp-frontend.min.css' : 'saswp-frontend.css'), false , SASWP_VERSION );	
			wp_enqueue_script( 'saswp-rateyo-front-js', SASWP_PLUGIN_URL . 'admin_section/js/jquery.rateyo.min.js', array('jquery', 'jquery-ui-core'), SASWP_VERSION , true );                                                                                        
			wp_enqueue_style( 'jquery-rateyo-min-css', SASWP_PLUGIN_URL . 'admin_section/css/'.(SASWP_ENVIRONMENT == 'production' ? 'jquery.rateyo.min.css' : 'jquery.rateyo.min.css'), false, SASWP_VERSION );


			$data = array(     
	            'rateyo_default_rating'  =>  isset($sd_data['saswp-default-rating']) ? $sd_data['saswp-default-rating'] : 5
			);

			wp_register_script( 'saswp-frontend-js', SASWP_PLUGIN_URL . 'admin_section/js/'.(SASWP_ENVIRONMENT == 'production' ? 'saswp-frontend.min.js' : 'saswp-frontend.js'), array('jquery', 'jquery-ui-core'), SASWP_VERSION, true );
			wp_localize_script( 'saswp-frontend-js', 'saswp_localize_front_data', $data );
			wp_enqueue_script( 'saswp-frontend-js' );

	    }

	}

	/**
	 * Render review feature page 
	 * @since 	1.46
	 * */
	public function saswp_render_review_feature_page() {

		global $sd_data;
		$hide_class 	=	'saswp_hide';
		if ( ! empty( $sd_data['saswp-stars-rating'] ) ) {
			$hide_class =	'';	
		}
		?>
		<div class="wrap saswp-settings-form saswp-rf-page-settings-container <?php echo esc_attr( $hide_class ); ?>">	
			<div>
				<h2 class="nav-tab-wrapper saswp-rf-tab-nav">
					<span id="saswp-rf-page-tab-heading"><?php echo esc_html__( 'Comments Review Module', 'schema-and-structured-data-for-wp' ); ?></span>
					<a href="#saswp-rf-page-review-tab" class="nav-tab nav-tab-active"><?php echo esc_html__( 'Review', 'schema-and-structured-data-for-wp' ); ?></a>
					<a href="#saswp-rf-page-settings-tab" class="nav-tab"><?php echo esc_html__( 'Settings', 'schema-and-structured-data-for-wp' ); ?></a>
					<a href="#saswp-rf-page-style-tab" class="nav-tab"><?php echo esc_html__( 'Style', 'schema-and-structured-data-for-wp' ); ?></a>
				</h2>
			</div>

		    <!-- <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" class="saswp-settings-form"> -->
		        <div class="form-wrap saswp-settings-form-wrap">
		            
		            <?php $this->saswp_review_feature_box_callback(); ?>
		        </div>
		        <?php //submit_button(); ?>
		        <!-- <input type="hidden" name="action" value="saswp_review_feature_form_submission"> -->
		        <?php //wp_nonce_field('saswp_rf_page_action_nonce', 'saswp_rf_page_nonce'); ?>
		    <!-- </form> -->
		</div>
		<?php

	}

	/**
	 * Metabox render content
	 * @since 	1.46
	 * */
	public function saswp_review_feature_box_callback() {
		global $sd_data;
	    ?>
    	<div class="saswp-rf-page-metabox-tab-content">

    		<?php 
    		$this->render_review_tab(); 
    		$this->render_settings_tab(); 
    		$this->render_style_tab(); 
    		// $this->render_support_tab(); 
    		?>
   
		</div>
	    <?php
	}

	/**
	 * Render revie tab
	 * @since 	1.46
	 * */
	public function render_review_tab() {
		
		global $sd_data;
		
		$criteria 			=	'single';
		if ( ! empty( $sd_data['saswp-rf-page-criteria'] ) ) {
			if ( $sd_data['saswp-rf-page-criteria'] == 'multi' ) {
				$criteria 	=	'multi';	
			}
		}

		$summary 			=	'one';
		if ( ! empty( $sd_data['saswp-rf-page-summary-layout'] ) ) {
			if ( $sd_data['saswp-rf-page-summary-layout'] == 'two' ) {
				$summary 	=	'two';	
			}
		}

		$reeview_layout 	=	'one';
		if ( ! empty( $sd_data['saswp-rf-page-review-layout'] ) ) {
			if ( $sd_data['saswp-rf-page-review-layout'] == 'two' ) {
				$reeview_layout 	=	'two';	
			}
		}

		$tab 				=	isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'review';
		$hide_Class 		=	'saswp_hide';
		if ( $tab == 'review'  ) {
			$hide_Class 	=	'';	
		}

	?>
		<div id="saswp-rf-page-review-tab" class="saswp-rf-page-tab-content-wrapper <?php echo esc_attr( $hide_Class ); ?>">

			<div class="saswp-rf-page-metabox-field-wrapper">

	    		<div class="saswp-rf-page-field-label">
	    			<label><?php echo esc_html__( 'Criteria', 'schema-and-structured-data-for-wp' ); ?></label>
	    		</div>
	    		<div class="saswp-rf-page-field">
	    			<div id="saswp-rf-page-field-criteria" class="saswp-rf-page-radio-image">
					    <label for="saswp-rf-page-criteria-single">
			                <input type="radio" id="saswp-rf-page-criteria-single" class="saswp-rf-page-criteria-radio" name="sd_data[saswp-rf-page-criteria]" value="single" data-pro="" <?php checked( $criteria, 'single' ); ?>>
			                <div class="saswp-rf-page-radio-image-pro-wrap">
			                	<?php $img 	=	SASWP_DIR_URI.'admin_section/images/saswp-single-criteria.jpg'; ?>
			                    <img src="<?php echo esc_attr( $img ); ?>" alt="single">
			                    <div class="saswp-rf-page-checked"><span class="dashicons dashicons-yes"></span></div>
			                </div>
		                </label>
						<label for="saswp-rf-page-criteria-multi">
			                <input type="radio" id="saswp-rf-page-criteria-multi" name="sd_data[saswp-rf-page-criteria]" value="multi" data-pro="" class="saswp-rf-page-criteria-radio" <?php checked( $criteria, 'multi' ); ?>>
			                <div class="saswp-rf-page-radio-image-pro-wrap">
			                	<?php $img 	=	SASWP_DIR_URI.'admin_section/images/saswp-multi-criteria.jpg'; ?>
			                    <img src="<?php echo esc_attr( $img ); ?>" alt="multi">
			                    <div class="saswp-rf-page-checked"><span class="dashicons dashicons-yes"></span></div>
			                </div>
		                </label>
	    			</div>
	    		</div>

	    	</div>

	    	<div class="saswp-rf-page-metabox-field-wrapper" id="saswp-rf-page-multi-criteria-wrapper">

	    		<div class="saswp-rf-page-field-label">
	    			<label><?php echo esc_html__( 'Multi criteria', 'schema-and-structured-data-for-wp' ); ?></label>
	    		</div>
	    		<div class="saswp-rf-page-field">
	    			<div class="saswp-rf-page-rtrs-repeater checkbox-group vertical ui-sortable" id="saswp-rf-page-multi-criteria">
	    				<?php 
	    				if ( ! empty( $sd_data['saswp-rf-page-criteria-multiple'] ) && is_array( $sd_data['saswp-rf-page-criteria-multiple'] ) ) {
	    					$cnt 	=	0;
	    					foreach ( $sd_data['saswp-rf-page-criteria-multiple'] as $key => $value ) {
	    						$field_id 	=	'saswp-rf-multi-criteria-' . $cnt;
	    					?>
		    					<label for="<?php echo esc_attr( $field_id ) ?>" class="ui-sortable-handle">
				                	<input type="text" id="<?php echo esc_attr( $field_id ); ?>" name="sd_data[saswp-rf-page-criteria-multiple][]" value="<?php echo esc_attr( $value ); ?>"><i class="saswp-rf-page-remove-multiple-criteria dashicons dashicons-dismiss"></i>
				                </label>
	    					<?php
	    						$cnt++;	
	    					}
	    				}else if ( ! isset( $sd_data['saswp-rf-page-criteria-multiple'] ) ){
	    					$multi_array 	=	array( 'Quality', 'Price', 'Service' );
	    					$cnt 			=	0;
	    					foreach ( $multi_array as $key => $value ) {
	    						$field_id 	=	'saswp-rf-multi-criteria-' . $cnt;
	    					?>
	    						<label for="<?php echo esc_attr( $field_id ) ?>" class="ui-sortable-handle">
				                	<input type="text" id="<?php echo esc_attr( $field_id ); ?>" name="sd_data[saswp-rf-page-criteria-multiple][]" value="<?php echo esc_attr( $value ); ?>"><i class="saswp-rf-page-remove-multiple-criteria dashicons dashicons-dismiss"></i>
				                </label>
	    					<?php
	    						$cnt++;

	    					}
			            } ?>
    				</div>
    				<a href="#" id="saswp-rf-page-add-multi-criteria"><i class="dashicons dashicons-insert"></i><?php echo esc_html__( 'Add New', 'schema-and-structured-data-for-wp' ); ?> </a>
	    		</div>

	    	</div>

	    	<div class="saswp-rf-page-metabox-field-wrapper">

	    		<div class="saswp-rf-page-field-label">
	    			<label><?php echo esc_html__( 'Review Summary Layout', 'schema-and-structured-data-for-wp' ); ?></label>
	    		</div>
	    		<div class="saswp-rf-page-field">
	    			<div id="saswp-rf-page-field-summary-layout" class="saswp-rf-page-radio-image">
					    <label for="saswp-rf-page-summary-layout-one">
			                <input type="radio" id="saswp-rf-page-summary-layout-one" class="saswp-rf-page-summary-layout-radio" name="sd_data[saswp-rf-page-summary-layout]" value="one" data-pro="" <?php checked( $summary, 'one' ) ?>>
			                <div class="saswp-rf-page-radio-image-pro-wrap">
			                	<?php $img 	=	SASWP_DIR_URI.'admin_section/images/saswp-summary-one.jpg'; ?>
			                    <img src="<?php echo esc_attr( $img ); ?>" alt="one">
			                    <div class="saswp-rf-page-checked"><span class="dashicons dashicons-yes"></span></div>
			                </div>
		                </label>
						<label for="saswp-rf-page-summary-layout-two">
			                <input type="radio" id="saswp-rf-page-summary-layout-two" name="sd_data[saswp-rf-page-summary-layout]" value="two" data-pro="" class="saswp-rf-page-summary-layout-radio" <?php checked( $summary, 'two' ) ?>>
			                <div class="saswp-rf-page-radio-image-pro-wrap">
			                	<?php $img 	=	SASWP_DIR_URI.'admin_section/images/saswp-summary-two.jpg'; ?>
			                    <img src="<?php echo esc_attr( $img ); ?>" alt="two">
			                    <div class="saswp-rf-page-checked"><span class="dashicons dashicons-yes"></span></div>
			                </div>
		                </label>
	    			</div>
	    		</div>

	    	</div>

	    	<div class="saswp-rf-page-metabox-field-wrapper">

	    		<div class="saswp-rf-page-field-label">
	    			<label><?php echo esc_html__( 'Review Layout', 'schema-and-structured-data-for-wp' ); ?></label>
	    		</div>
	    		<div class="saswp-rf-page-field">
	    			<div id="saswp-rf-page-field-review-layout" class="saswp-rf-page-radio-image">
					    <label for="saswp-rf-page-review-layout-one">
			                <input type="radio" id="saswp-rf-page-review-layout-one" class="saswp-rf-page-review-layout-radio" name="sd_data[saswp-rf-page-review-layout]" value="one" data-pro="" <?php checked( $reeview_layout, 'one' ); ?>>
			                <div class="saswp-rf-page-radio-image-pro-wrap">
			                	<?php $img 	=	SASWP_DIR_URI.'admin_section/images/saswp-review-one.jpg'; ?>
			                    <img src="<?php echo esc_attr( $img ); ?>" alt="one">
			                    <div class="saswp-rf-page-checked"><span class="dashicons dashicons-yes"></span></div>
			                </div>
		                </label>
						<label for="saswp-rf-page-review-layout-two">
			                <input type="radio" id="saswp-rf-page-review-layout-two" name="sd_data[saswp-rf-page-review-layout]" value="two" data-pro="" class="saswp-rf-page-review-layout-radio" <?php checked( $reeview_layout, 'two' ); ?>>
			                <div class="saswp-rf-page-radio-image-pro-wrap">
			                	<?php $img 	=	SASWP_DIR_URI.'admin_section/images/saswp-review-two.jpg'; ?>
			                    <img src="<?php echo esc_attr( $img ); ?>" alt="two">
			                    <div class="saswp-rf-page-checked"><span class="dashicons dashicons-yes"></span></div>
			                </div>
		                </label>
	    			</div>
	    		</div>

	    	</div>

	    	<div class="saswp-rf-page-metabox-field-wrapper">

	    		<div class="saswp-rf-page-field-label">
	    			<label><?php echo esc_html__( 'Pagination Type', 'schema-and-structured-data-for-wp' ); ?></label>
	    		</div>
	    		<div class="saswp-rf-page-field">
	    			<?php 
	    			$options 	=	array(
	    								'Number' 		=>	'number',
	    								'Number Ajax' 	=>	'number-ajax',
	    								'Load More' 	=>	'load-more',
	    								'Auto Scroll' 	=>	'auto-scroll',
	    							);
	    			?>
	    			<select name="sd_data[saswp-rf-page-pagination-type]" id="saswp-rf-page-pagination-type" class="select2 select2-hidden-accessible saswp-rf-page-select2" tabindex="-1" aria-hidden="true">
	    				<?php 
	    				foreach ( $options as $key => $option ) {
	    					if ( ! empty( $sd_data['saswp-rf-page-pagination-type'] ) && $sd_data['saswp-rf-page-pagination-type'] == $option ) {
	    					?> <option selected value="<?php echo esc_attr( $option ); ?>"><?php echo esc_html( $key ); ?></option> <?php	
	    					}else{
	    					?> <option value="<?php echo esc_attr( $option ); ?>"><?php echo esc_html( $key ); ?></option> <?php
	    					}	
	    				}
	    				?>
	    			</select>
	    		</div>
	    	</div>

		</div> <!-- saswp-rf-page-review-tab div end -->
	<?php

	}

		/**
	 * Render settings tab
	 * @since 	1.46
	 * */
	public function render_settings_tab() {
		
		global $sd_data;

		$setting_fields 	=	self::get_settings_fields();

	?>
		<div id="saswp-rf-page-settings-tab" class="saswp-rf-page-tab-content-wrapper saswp_hide">
        	
			<?php 
			foreach ( $setting_fields as $field_key => $field ) {

				switch( $field['type'] ) {

					case 'checkbox':
					?>

						<div class="saswp-rf-page-metabox-field-wrapper" id="<?php echo esc_attr( $field['wrapper_id'] ); ?>">
				    		<div class="saswp-rf-page-field-label saswp-rf-page-field-settings-label">
				    			<label class="saswp-review-feature-post-type-label" for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
				    		</div>
				    		<div class="saswp-rf-page-field">
				    			<label>
				    				<input type="checkbox" id="<?php echo esc_attr( $field['id'] ); ?>" name="<?php echo esc_attr( $field['name'] ); ?>" value="1" <?php checked( $field['value'] ); ?>>
				    			</label>
				    			<?php 
				    			if ( ! empty( $field['desc'] ) ) {
				    			?>
				    				<p class="description"><?php echo esc_html( $field['desc'] ); ?></p>	
				    			<?php	
				    			}
				    			?>
				    		</div>
				    	</div>

					<?php	
					break;

					case 'text':
					?>
						<div class="saswp-rf-page-metabox-field-wrapper" id="<?php echo esc_attr( $field['wrapper_id'] ); ?>">
				    		<div class="saswp-rf-page-field-label saswp-rf-page-field-settings-label">
				    			<label><?php echo esc_html( $field['label'] ); ?></label>
				    		</div>
				    		<div class="saswp-rf-page-field">
				    			<label for="<?php echo esc_attr( $field['id'] ); ?>">
				    				<input type="text" id="<?php echo esc_attr( $field['id'] ); ?>" name="<?php echo esc_attr( $field['name'] ); ?>" value="<?php echo esc_attr( $field['value'] ); ?>">
				    			</label>
				    			<?php 
				    			if ( ! empty( $field['desc'] ) ) {
				    			?>
				    				<p class="description"><?php echo esc_html( $field['desc'] ); ?></p>	
				    			<?php	
				    			}
				    			?>
				    		</div>
				    	</div>
					<?php
					break;

				}
			}
			?>

	    	<div class="saswp-rf-page-metabox-field-wrapper" id="saswp-rf-page-settings-filters-opt">
	    		<div class="saswp-rf-page-field-label">
	    			<label><?php echo esc_html__( 'Filter Options?', 'schema-and-structured-data-for-wp' ); ?></label>
	    		</div>
	    		<div class="saswp-rf-page-field">
	    			<div class="checkbox-group vertical" id="saswp-rf-page-settings-filter-options">
	    				<?php 
	    				$filter_array 	=	array(
	    											'top_rated' 	=> 'Top Rated',
	    											'low_rated' 	=> 'Lowest Rating',
	    											'latest_first' 	=> 'Latest Rating',
	    											'oldest_first' 	=> 'Oldest First',
	    										);
	    				if ( ! empty( $sd_data['saswp-rf-page-settings-filter-options'] ) && is_array( $sd_data['saswp-rf-page-settings-filter-options'] ) ) {
	    					
	    					foreach ( $filter_array as $fkey => $fvalue ) {
	    						foreach ( $sd_data['saswp-rf-page-settings-filter-options'] as $key => $value ) {
	    							$exists 	=	0;
	    							$id 		=	'saswp-rf-page-filter-options-' . $value;
	    							if ( $value == $fkey ) {
	    								$exists =	1;
	    							?>
	    								<label for="<?php echo esc_attr( $id ); ?>">
                        					<input type="checkbox" id="<?php echo esc_attr( $id ); ?>" checked="" name="sd_data[saswp-rf-page-settings-filter-options][]" value="<?php echo esc_attr( $fkey ); ?>"><?php echo esc_html( $fvalue ); ?> 
                        				</label>
	    							<?php
	    							break;	
	    							}
	    						}

	    						if ( $exists == 0 ) {
	    							$id 		=	'saswp-rf-page-filter-options-' . $fkey;
	    						?>
	    							<label for="<?php echo esc_attr( $id ); ?>">
                        				<input type="checkbox" id="<?php echo esc_attr( $id ); ?>" name="sd_data[saswp-rf-page-settings-filter-options][]" value="<?php echo esc_attr( $fkey ); ?>"><?php echo esc_html( $fvalue ); ?> 
                        			</label>
	    						<?php	
	    						}
	    					}
	    				}else{
	    					foreach ( $filter_array as $key => $value ) {
	    						$id 	=	'saswp-rf-page-filter-options-' . $key;
	    					?>
	    						<label for="<?php echo esc_attr( $id ); ?>">
	                        		<input type="checkbox" id="<?php echo esc_attr( $id ); ?>" checked="" name="sd_data[saswp-rf-page-settings-filter-options][]" value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $value ); ?> 
	                        	</label>	
	    					<?php
	    					}
	                	}
	                    ?>
                    </div>
	    		</div>
	    	</div>

        </div> <!-- saswp-rf-page-settings-tab div end -->
	<?php	

	}

	/**
	 * Render style tab
	 * @since 	1.46
	 * */
	public function render_style_tab() {

		$style_fields 	=	$this->get_style_settings();
		?>
		<div id="saswp-rf-page-style-tab" class="saswp-rf-page-tab-content-wrapper saswp_hide">
        	
			<?php 
			foreach ( $style_fields as $field_key => $field ) {

				switch( $field['type'] ) {

					case 'text':
					?>
						<div class="saswp-rf-page-metabox-field-wrapper" id="<?php echo esc_attr( $field['wrapper_id'] ); ?>">
				    		<div class="saswp-rf-page-field-label">
				    			<label><?php echo esc_html( $field['label'] ); ?></label>
				    		</div>
				    		<div class="saswp-rf-page-field">
				    			<label for="<?php echo esc_attr( $field['id'] ); ?>">
				    				<input class="<?php echo esc_attr( $field['class'] ); ?>" type="text" id="<?php echo esc_attr( $field['id'] ); ?>" name="<?php echo esc_attr( $field['name'] ); ?>" value="<?php echo esc_attr( $field['value'] ); ?>">
				    			</label>
				    			<?php 
				    			if ( ! empty( $field['desc'] ) ) {
				    			?>
				    				<p class="description"><?php echo esc_html( $field['desc'] ); ?></p>	
				    			<?php	
				    			}
				    			?>
				    		</div>
				    	</div>
					<?php
					break;

					case 'multi-columns':
						?>
							<div class="saswp-rf-page-metabox-field-wrapper" id="<?php echo esc_attr( $field['wrapper_id'] ); ?>">
								<div class="saswp-rf-page-field-label">
					    			<label><?php echo esc_html( $field['label'] ); ?></label>
					    		</div>	
					    		<div class="saswp-rf-page-field">
					    			<div class="saswp-rf-page-multiple-field-container">
					    				<?php 
					    				foreach ( $field['fields'] as $child_field ) {
					    				?>
					    					<div class="saswp-rf-page-inner-field saswp-rf-page-col-4">
					    						<div class="saswp-rf-page-inner-field-container size">
					    							<span class="label"><?php echo esc_html( $child_field['label'] ); ?></span>	
					    							<?php 
					    							switch( $child_field['type'] ) {

					    								case 'color_picker':

					    									?>
					    										<input type="text" class="<?php echo esc_attr( $child_field['class'] ); ?>" name="<?php echo esc_attr( $child_field['name'] ); ?>" id="<?php echo esc_attr( $child_field['id'] ); ?>" value="<?php echo esc_attr( $child_field['value'] ); ?>">
					    									<?php

					    								break;

					    								case 'text':

					    									?>
					    										<input type="text" class="<?php echo esc_attr( $child_field['class'] ); ?>" name="<?php echo esc_attr( $child_field['name'] ); ?>" id="<?php echo esc_attr( $child_field['id'] ); ?>" value="<?php echo esc_attr( $child_field['value'] ); ?>">
					    									<?php

					    								break;

					    								case 'select':

					    									?>
					    									<select class="<?php echo esc_attr( $child_field['class'] ); ?>" name="<?php echo esc_attr( $child_field['name'] ); ?>">
					    										<?php 
											    				foreach ( $child_field['options'] as $key => $option ) {
											    					if (  $child_field['value'] == $key ) {
											    					?> <option selected value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $option ); ?></option> <?php	
											    					}else{
											    					?> <option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $option ); ?></option> <?php
											    					}	
											    				}
											    				?>
					    									</select>
					    									<?php

					    								break;

					    							}
					    							?>
					    						</div>
					    					</div>
					    				<?php
					    				}
					    				?>
					    			</div>
					    		</div>
							</div>
						<?php
						
					break;

					case 'color_picker':
						?>
						<div class="saswp-rf-page-metabox-field-wrapper" id="<?php echo esc_attr( $field['wrapper_id'] ); ?>">
				    		<div class="saswp-rf-page-field-label">
				    			<label><?php echo esc_html( $field['label'] ); ?></label>
				    		</div>
				    		<div class="saswp-rf-page-field">
				    			<label for="<?php echo esc_attr( $field['id'] ); ?>">
				    				<input class="<?php echo esc_attr( $field['class'] ); ?>" type="text" id="<?php echo esc_attr( $field['id'] ); ?>" name="<?php echo esc_attr( $field['name'] ); ?>" value="<?php echo esc_attr( $field['value'] ); ?>">
				    			</label>
				    			<?php 
				    			if ( ! empty( $field['desc'] ) ) {
				    			?>
				    				<p class="description"><?php echo esc_html( $field['desc'] ); ?></p>	
				    			<?php	
				    			}
				    			?>
				    		</div>
				    	</div>
						<?php
					break;

				}
			}
			?>

        </div> <!-- saswp-rf-page-style-tab div end -->
       	<?php

	}

	/**
	 * Render support tab
	 * @since 	1.46
	 * */
	public function render_support_tab() {

		global $sd_data;

		?>
		<div id="saswp-rf-page-support-tab" class="saswp-rf-page-tab-content-wrapper">

			<div class="saswp-rf-page-metabox-content-block">
		    	<div id="saswp-rf-page-metabox-support">
			    	<div class="saswp-rf-page-metabox-field-wrapper">
			    		<div class="saswp-rf-page-field-label">
			    			<label><?php echo esc_html__('Support', 'schema-and-structured-data-for-wp'); ?></label>
			    		</div>
			    		<div class="saswp-rf-page-field">
			    			<?php 
			    			$post_type 					=	saswp_post_type_generator();
			    			if ( ! empty( $post_type ) ) {
			    				
			    				$feature_post_type 		=	array();
			    				
			    				if ( ! empty( $sd_data['saswp-rf-page-post-type'] ) ) {
			    					$feature_post_type 	= 	$sd_data['saswp-rf-page-post-type'];	
			    				}

			    				foreach ( $post_type as $key => $value ) {

			    					$checked 	=	'';
			    					if (  in_array( $value, $feature_post_type ) ) {
			    						$checked 	=	'checked';	
			    					}

			    				?>
			    					<label class="saswp-review-feature-post-type-label">
			    						<input type="checkbox" name="saswp-rf-page-post-type[]" value="<?php echo esc_attr( $key ); ?>" <?php echo esc_attr( $checked ); ?>  />
			    						<?php echo esc_html( $value ); ?>
			    					</label>
			    				<?php	
			    				}
			    			}
			    			?>
			    		</div>
			    	</div> <!-- saswp-rf-page-metabox-field-wrapper div end -->
			    </div> <!-- saswp-rf-page-metabox-support div end -->
		    </div>  <!--saswp-rf-page-metabox-content-block div end  -->

		</div> <!-- saswp-rf-page-support-tab div end -->
		<?php
	}

	/**
	 * Initialize settings fields
	 * @since 	1.46
	 * */
	public static function get_settings_fields() {
		
		global $sd_data;

		$setting_fields 	=	array(
			'saswp-rf-page-settings-title' 			=>	array(
														'id' 			=>	'saswp-rf-page-settings-title',
														'label' 		=>	'Review title disable?',
														'desc' 			=>	'',
														'name' 			=>	'sd_data[saswp-rf-page-settings-title]',
														'type' 			=>	'checkbox',
														'wrapper_id' 	=> 	'',
														'value' 		=>	isset( $sd_data['saswp-rf-page-settings-title'] ) ? $sd_data['saswp-rf-page-settings-title'] : ''
													),
			'saswp-rf-page-settings-human-time-diff'=> array(
														'id' 			=>	'saswp-rf-page-settings-human-time-diff',
														'label' 		=>	'Disable human readable time format ?',
														'desc' 			=>	'By default review time is human readable format such as "1 hour ago", "5 mins ago", "2 days ago " ',
														'name' 			=>	'sd_data[saswp-rf-page-settings-human-time-diff]',
														'type' 			=>	'checkbox',
														'wrapper_id' 	=> 	'',
														'value' 		=>	isset( $sd_data['saswp-rf-page-settings-human-time-diff'] ) ? $sd_data['saswp-rf-page-settings-human-time-diff'] : ''
													),
			'saswp-rf-page-settings-url-disable'=> array(
														'id' 			=>	'saswp-rf-page-settings-url-disable',
														'label' 		=>	'Review website url disable?',
														'desc' 			=>	'',
														'name' 			=>	'sd_data[saswp-rf-page-settings-url-disable]',
														'type' 			=>	'checkbox',
														'wrapper_id' 	=> 	'',
														'value' 		=>	isset( $sd_data['saswp-rf-page-settings-url-disable'] ) ? $sd_data['saswp-rf-page-settings-url-disable'] : ''
													),
			'saswp-rf-page-settings-image-review'=> array(
														'id' 			=>	'saswp-rf-page-settings-image-review',
														'label' 		=>	'Allow image review?',
														'desc' 			=>	'',
														'name' 			=>	'sd_data[saswp-rf-page-settings-image-review]',
														'type' 			=>	'checkbox',
														'wrapper_id' 	=> 	'',
														'value' 		=>	isset( $sd_data['saswp-rf-page-settings-image-review'] ) ? $sd_data['saswp-rf-page-settings-image-review'] : ''
													),
			'saswp-rf-page-settings-video-review'=> array(
														'id' 			=>	'saswp-rf-page-settings-video-review',
														'label' 		=>	'Allow video review?',
														'desc' 			=>	'',
														'name' 			=>	'sd_data[saswp-rf-page-settings-video-review]',
														'type' 			=>	'checkbox',
														'wrapper_id' 	=> 	'',
														'value' 		=>	isset( $sd_data['saswp-rf-page-settings-video-review'] ) ? $sd_data['saswp-rf-page-settings-video-review'] : ''
													),
			'saswp-rf-page-settings-pros-cons'	=> array(
														'id' 			=>	'saswp-rf-page-settings-pros-cons',
														'label' 		=>	'Allow pros cons?',
														'desc' 			=>	'',
														'name' 			=>	'sd_data[saswp-rf-page-settings-pros-cons]',
														'type' 			=>	'checkbox',
														'wrapper_id' 	=> 	'',
														'value' 		=>	isset( $sd_data['saswp-rf-page-settings-pros-cons'] ) ? $sd_data['saswp-rf-page-settings-pros-cons'] : ''
													),
			'saswp-rf-page-settings-pros-cons-limit'=> array(
														'id' 			=>	'saswp-rf-page-settings-pros-cons-limit',
														'label' 		=>	'Pros cons limit?',
														'desc' 			=>	'',
														'name' 			=>	'sd_data[saswp-rf-page-settings-pros-cons-limit]',
														'type' 			=>	'text',
														'wrapper_id' 	=> 	'saswp-rf-page-settings-pros-cons-limit-wrapper',
														'value' 		=>	! empty( $sd_data['saswp-rf-page-settings-pros-cons-limit'] ) ? $sd_data['saswp-rf-page-settings-pros-cons-limit'] : 3
													),
			'saswp-rf-page-settings-social-review'	=> array(
														'id' 			=>	'saswp-rf-page-settings-social-review',
														'label' 		=>	'Social review?',
														'desc' 			=>	'',
														'name' 			=>	'sd_data[saswp-rf-page-settings-social-review]',
														'type' 			=>	'checkbox',
														'wrapper_id' 	=> 	'',
														'value' 		=>	isset( $sd_data['saswp-rf-page-settings-social-review'] ) ? $sd_data['saswp-rf-page-settings-social-review'] : ''
													),
			'saswp-rf-page-settings-review-like'	=> array(
														'id' 			=>	'saswp-rf-page-settings-review-like',
														'label' 		=>	'Allow review like?',
														'desc' 			=>	'',
														'name' 			=>	'sd_data[saswp-rf-page-settings-review-like]',
														'type' 			=>	'checkbox',
														'wrapper_id' 	=> 	'',
														'value' 		=>	isset( $sd_data['saswp-rf-page-settings-review-like'] ) ? $sd_data['saswp-rf-page-settings-review-like'] : ''
													),
			'saswp-rf-page-settings-review-dislike'	=> array(
														'id' 			=>	'saswp-rf-page-settings-review-dislike',
														'label' 		=>	'Allow review dislike?',
														'desc' 			=>	'',
														'name' 			=>	'sd_data[saswp-rf-page-settings-review-dislike]',
														'type' 			=>	'checkbox',
														'wrapper_id' 	=> 	'',
														'value' 		=>	isset( $sd_data['saswp-rf-page-settings-review-dislike'] ) ? $sd_data['saswp-rf-page-settings-review-dislike'] : ''
													),
			'saswp-rf-page-settings-anonymus-review'	=> array(
														'id' 			=>	'saswp-rf-page-settings-anonymus-review',
														'label' 		=>	'Allow anonymous review?',
														'desc' 			=>	'',
														'name' 			=>	'sd_data[saswp-rf-page-settings-anonymus-review]',
														'type' 			=>	'checkbox',
														'wrapper_id' 	=> 	'',
														'value' 		=>	isset( $sd_data['saswp-rf-page-settings-anonymus-review'] ) ? $sd_data['saswp-rf-page-settings-anonymus-review'] : ''
													),
			'saswp-rf-page-settings-anonymus-email'	=> array(
														'id' 			=>	'saswp-rf-page-settings-anonymus-email',
														'label' 		=>	'Email field disable?',
														'desc' 			=>	'',
														'name' 			=>	'sd_data[saswp-rf-page-settings-anonymus-email]',
														'type' 			=>	'checkbox',
														'wrapper_id' 	=> 	'saswp-rf-page-settings-email-wrapper',
														'value' 		=>	isset( $sd_data['saswp-rf-page-settings-anonymus-email'] ) ? $sd_data['saswp-rf-page-settings-anonymus-email'] : ''
													),
			'saswp-rf-page-settings-anonymus-author'	=> array(
														'id' 			=>	'saswp-rf-page-settings-anonymus-author',
														'label' 		=>	'Author field disable?',
														'desc' 			=>	'',
														'name' 			=>	'sd_data[saswp-rf-page-settings-anonymus-author]',
														'type' 			=>	'checkbox',
														'wrapper_id' 	=> 	'saswp-rf-page-settings-author-wrapper',
														'value' 		=>	isset( $sd_data['saswp-rf-page-settings-anonymus-author'] ) ? $sd_data['saswp-rf-page-settings-anonymus-author'] : ''
													),
			'saswp-rf-page-settings-purchase-badge'	=> array(
														'id' 			=>	'saswp-rf-page-settings-purchase-badge',
														'label' 		=>	'Show purchase badge?',
														'desc' 			=>	esc_html__( 'It will show WC, EDD purchased badge', 'schema-and-structured-data-for-wp' ),
														'name' 			=>	'sd_data[saswp-rf-page-settings-purchase-badge]',
														'type' 			=>	'checkbox',
														'wrapper_id' 	=> 	'',
														'value' 		=>	isset( $sd_data['saswp-rf-page-settings-purchase-badge'] ) ? $sd_data['saswp-rf-page-settings-purchase-badge'] : ''
													),
			'saswp-rf-page-settings-filter'	=> array(
														'id' 			=>	'saswp-rf-page-settings-filter',
														'label' 		=>	'Filter?',
														'desc' 			=>	'',
														'name' 			=>	'sd_data[saswp-rf-page-settings-filter]',
														'type' 			=>	'checkbox',
														'wrapper_id' 	=> 	'',
														'value' 		=>	isset( $sd_data['saswp-rf-page-settings-filter'] ) ? $sd_data['saswp-rf-page-settings-filter'] : ''
													),
		);

		return $setting_fields;

	}

	/**
	 * Initialize style setting fields
	 * @since 	1.46
	 * */
	public function get_style_settings() {
		
		global $sd_data;
		
		$style_fields 	=	array(
			'saswp-rf-page-parent-class' 	=>	array(
														'id' 			=>	'saswp-rf-page-parent-class',
														'label' 		=>	'Parent class',
														'desc' 			=>	'Add your class for custom css',
														'name' 			=>	'sd_data[saswp-rf-page-parent-class]',
														'type' 			=>	'text',
														'wrapper_id' 	=> 	'',
														'class' 		=>	'medium-text saswp-rf-form-control',
														'value' 		=>	isset( $sd_data['saswp-rf-page-parent-class'] ) ? $sd_data['saswp-rf-page-parent-class'] : ''
													),
			'saswp-rf-page-width'			=>		array(
														'id' 			=>	'saswp-rf-page-width',
														'label' 		=>	'Width',
														'desc' 			=>	'Layout width, Like: 400px or 50% etc',
														'name' 			=>	'sd_data[saswp-rf-page-width]',
														'type' 			=>	'text',
														'wrapper_id' 	=> 	'',
														'class' 		=>	'small-width saswp-rf-form-control',
														'value' 		=>	isset( $sd_data['saswp-rf-page-width'] ) ? $sd_data['saswp-rf-page-width'] : ''
													),
			'saswp-rf-page-margin'			=>		array(
														'id' 			=>	'saswp-rf-page-margin',
														'label' 		=>	'Margin',
														'desc' 			=>	'Layout margin, Like: 50px',
														'name' 			=>	'sd_data[saswp-rf-page-margin]',
														'type' 			=>	'text',
														'wrapper_id' 	=> 	'',
														'class' 		=>	'small-width saswp-rf-form-control',
														'value' 		=>	isset( $sd_data['saswp-rf-page-margin'] ) ? $sd_data['saswp-rf-page-margin'] : ''
													),
			'saswp-rf-page-padding'			=>		array(
														'id' 			=>	'saswp-rf-page-padding',
														'label' 		=>	'Padding',
														'desc' 			=>	'Layout padding, Like: 50px',
														'name' 			=>	'sd_data[saswp-rf-page-padding]',
														'type' 			=>	'text',
														'wrapper_id' 	=> 	'',
														'class' 		=>	'small-width saswp-rf-form-control',
														'value' 		=>	isset( $sd_data['saswp-rf-page-padding'] ) ? $sd_data['saswp-rf-page-padding'] : ''
													),
			'saswp-rf-page-review-title-options' 	=>		array(
																'type' 			=>	'multi-columns',
																'label' 		=>	'Review Title',
																'wrapper_id'	=>	'',
																'name' 			=>	'saswp_rf_page_review_title',
																'fields' 		=>	array(
																				array(
																					'id' 			=>	'saswp-rf-page-review-title-color',
																					'label' 		=>	'Color',
																					'desc' 			=>	'',
																					'name' 			=>	'sd_data[saswp_rf_page_review_title][color]',
																					'type' 			=>	'color_picker',
																					'wrapper_id' 	=> 	'',
																					'class' 		=>	'saswpforwp-colorpicker',
																					'value' 		=>	isset( $sd_data['saswp_rf_page_review_title']['color'] ) ? $sd_data['saswp_rf_page_review_title']['color'] : '#000'
																				),
																				array(
																					'id' 			=>	'saswp-rf-page-review-title-size',
																					'label' 		=>	'Font Size',
																					'desc' 			=>	'',
																					'name' 			=>	'sd_data[saswp_rf_page_review_title][size]',
																					'type' 			=>	'text',
																					'wrapper_id' 	=> 	'',
																					'class' 		=>	'',
																					'value' 		=>	isset( $sd_data['saswp_rf_page_review_title']['size'] ) ? $sd_data['saswp_rf_page_review_title']['size'] : '16px'
																				),
																				array(
																					'id' 			=>	'saswp-rf-page-review-title-weight',
																					'label' 		=>	'Font Weight',
																					'desc' 			=>	'',
																					'name' 			=>	'sd_data[saswp_rf_page_review_title][weight]',
																					'type' 			=>	'select',
																					'wrapper_id' 	=> 	'',
																					'class' 		=>	'saswp-rf-page-select2',
																					'value' 		=>	isset( $sd_data['saswp_rf_page_review_title']['weight'] ) ? $sd_data['saswp_rf_page_review_title']['weight'] : 'bold',
																					'options' 		=>	$this->font_weight_options(),
																				),
																				array(
																					'id' 			=>	'saswp-rf-page-review-title-alignment',
																					'label' 		=>	'Font Alignment',
																					'desc' 			=>	'',
																					'name' 			=>	'sd_data[saswp_rf_page_review_title][alignment]',
																					'type' 			=>	'select',
																					'wrapper_id' 	=> 	'',
																					'class' 		=>	'saswp-rf-page-select2',
																					'value' 		=>	isset( $sd_data['saswp_rf_page_review_title']['alignment'] ) ? $sd_data['saswp_rf_page_review_title']['alignment'] : 'left',
																					'options' 		=>	$this->font_alignment_options(),
																				),
																			),
															),
			'saswp-rf-page-review-text-options' 	=>		array(
																'type' 			=>	'multi-columns',
																'label' 		=>	'Review Text',
																'wrapper_id'	=>	'',
																'name' 			=>	'saswp_rf_page_review_text',
																'fields' 		=>	array(
																				array(
																					'id' 			=>	'saswp-rf-page-review-text-color',
																					'label' 		=>	'Color',
																					'desc' 			=>	'',
																					'name' 			=>	'sd_data[saswp_rf_page_review_text][color]',
																					'type' 			=>	'color_picker',
																					'wrapper_id' 	=> 	'',
																					'class' 		=>	'saswpforwp-colorpicker',
																					'value' 		=>	isset( $sd_data['saswp_rf_page_review_text']['color'] ) ? $sd_data['saswp_rf_page_review_text']['color'] : '#000'
																				),
																				array(
																					'id' 			=>	'saswp-rf-page-review-text-size',
																					'label' 		=>	'Font Size',
																					'desc' 			=>	'',
																					'name' 			=>	'sd_data[saswp_rf_page_review_text][size]',
																					'type' 			=>	'text',
																					'wrapper_id' 	=> 	'',
																					'class' 		=>	'',
																					'value' 		=>	isset( $sd_data['saswp_rf_page_review_text']['size'] ) ? $sd_data['saswp_rf_page_review_text']['size'] : '16px'
																				),
																				array(
																					'id' 			=>	'saswp-rf-page-review-text-weight',
																					'label' 		=>	'Font Weight',
																					'desc' 			=>	'',
																					'name' 			=>	'sd_data[saswp_rf_page_review_text][weight]',
																					'type' 			=>	'select',
																					'wrapper_id' 	=> 	'',
																					'class' 		=>	'saswp-rf-page-select2',
																					'value' 		=>	isset( $sd_data['saswp_rf_page_review_text']['weight'] ) ? $sd_data['saswp_rf_page_review_text']['weight'] : '',
																					'options' 		=>	$this->font_weight_options(),
																				),
																				array(
																					'id' 			=>	'saswp-rf-page-review-text-alignment',
																					'label' 		=>	'Font Alignment',
																					'desc' 			=>	'',
																					'name' 			=>	'sd_data[saswp_rf_page_review_text][alignment]',
																					'type' 			=>	'select',
																					'wrapper_id' 	=> 	'',
																					'class' 		=>	'saswp-rf-page-select2',
																					'value' 		=>	isset( $sd_data['saswp_rf_page_review_text']['alignment'] ) ? $sd_data['saswp_rf_page_review_text']['alignment'] : '',
																					'options' 		=>	$this->font_alignment_options(),
																				),
																			),
															),
			'saswp-rf-page-review-date-text-options' 	=>		array(
																'type' 			=>	'multi-columns',
																'label' 		=>	'Date Text',
																'wrapper_id'	=>	'',
																'name' 			=>	'saswp_rf_page_review_date_text',
																'fields' 		=>	array(
																				array(
																					'id' 			=>	'saswp-rf-page-review-date-text-color',
																					'label' 		=>	'Color',
																					'desc' 			=>	'',
																					'name' 			=>	'sd_data[saswp_rf_page_review_date_text][color]',
																					'type' 			=>	'color_picker',
																					'wrapper_id' 	=> 	'',
																					'class' 		=>	'saswpforwp-colorpicker',
																					'value' 		=>	isset( $sd_data['saswp_rf_page_review_date_text']['color'] ) ? $sd_data['saswp_rf_page_review_date_text']['color'] : '#000'
																				),
																				array(
																					'id' 			=>	'saswp-rf-page-review-date-text-size',
																					'label' 		=>	'Font Size',
																					'desc' 			=>	'',
																					'name' 			=>	'sd_data[saswp_rf_page_review_date_text][size]',
																					'type' 			=>	'text',
																					'wrapper_id' 	=> 	'',
																					'class' 		=>	'',
																					'value' 		=>	isset( $sd_data['saswp_rf_page_review_date_text']['size'] ) ? $sd_data['saswp_rf_page_review_date_text']['size'] : '14px'
																				),
																				array(
																					'id' 			=>	'saswp-rf-page-review-date-text-weight',
																					'label' 		=>	'Font Weight',
																					'desc' 			=>	'',
																					'name' 			=>	'sd_data[saswp_rf_page_review_date_text][weight]',
																					'type' 			=>	'select',
																					'wrapper_id' 	=> 	'',
																					'class' 		=>	'saswp-rf-page-select2',
																					'value' 		=>	isset( $sd_data['saswp_rf_page_review_date_text']['weight'] ) ? $sd_data['saswp_rf_page_review_date_text']['weight'] : '',
																					'options' 		=>	$this->font_weight_options(),
																				),
																				array(
																					'id' 			=>	'saswp-rf-page-review-date-text-alignment',
																					'label' 		=>	'Font Alignment',
																					'desc' 			=>	'',
																					'name' 			=>	'sd_data[saswp_rf_page_review_date_text][alignment]',
																					'type' 			=>	'select',
																					'wrapper_id' 	=> 	'',
																					'class' 		=>	'saswp-rf-page-select2',
																					'value' 		=>	isset( $sd_data['saswp_rf_page_review_date_text']['alignment'] ) ? $sd_data['saswp_rf_page_review_date_text']['alignment'] : 'left',
																					'options' 		=>	$this->font_alignment_options(),
																				),
																			),
															),
			'saswp-rf-page-review-author-name-options' 	=>		array(
																'type' 			=>	'multi-columns',
																'label' 		=>	'Author name',
																'wrapper_id'	=>	'',
																'name' 			=>	'saswp_rf_page_review_author_name_text',
																'fields' 		=>	array(
																				array(
																					'id' 			=>	'saswp-rf-page-review-author-name-color',
																					'label' 		=>	'Color',
																					'desc' 			=>	'',
																					'name' 			=>	'sd_data[saswp_rf_page_review_author_name_text][color]',
																					'type' 			=>	'color_picker',
																					'wrapper_id' 	=> 	'',
																					'class' 		=>	'saswpforwp-colorpicker',
																					'value' 		=>	isset( $sd_data['saswp_rf_page_review_author_name_text']['color'] ) ? $sd_data['saswp_rf_page_review_author_name_text']['color'] : '#000'
																				),
																				array(
																					'id' 			=>	'saswp-rf-page-review-author-name-text-size',
																					'label' 		=>	'Font Size',
																					'desc' 			=>	'',
																					'name' 			=>	'sd_data[saswp_rf_page_review_author_name_text][size]',
																					'type' 			=>	'text',
																					'wrapper_id' 	=> 	'',
																					'class' 		=>	'',
																					'value' 		=>	isset( $sd_data['saswp_rf_page_review_author_name_text']['size'] ) ? $sd_data['saswp_rf_page_review_author_name_text']['size'] : '14px'
																				),
																				array(
																					'id' 			=>	'saswp-rf-page-review-author-name-text-weight',
																					'label' 		=>	'Font Weight',
																					'desc' 			=>	'',
																					'name' 			=>	'sd_data[saswp_rf_page_review_author_name_text][weight]',
																					'type' 			=>	'select',
																					'wrapper_id' 	=> 	'',
																					'class' 		=>	'saswp-rf-page-select2',
																					'value' 		=>	isset( $sd_data['saswp_rf_page_review_author_name_text']['weight'] ) ? $sd_data['saswp_rf_page_review_author_name_text']['weight'] : '',
																					'options' 		=>	$this->font_weight_options(),
																				),
																				array(
																					'id' 			=>	'saswp-rf-page-review-author-name-text-alignment',
																					'label' 		=>	'Font Alignment',
																					'desc' 			=>	'',
																					'name' 			=>	'sd_data[saswp_rf_page_review_author_name_text][alignment]',
																					'type' 			=>	'select',
																					'wrapper_id' 	=> 	'',
																					'class' 		=>	'saswp-rf-page-select2',
																					'value' 		=>	isset( $sd_data['saswp_rf_page_review_author_name_text']['alignment'] ) ? $sd_data['saswp_rf_page_review_author_name_text']['alignment'] : 'left',
																					'options' 		=>	$this->font_alignment_options(),
																				),
																			),
															),
			'saswp-rf-page-star-color'			=>		array(
														'id' 			=>	'saswp-rf-page-star-color',
														'label' 		=>	'Star color',
														'desc' 			=>	'',
														'name' 			=>	'sd_data[saswp-rf-page-star-color]',
														'type' 			=>	'color_picker',
														'wrapper_id' 	=> 	'',
														'class' 		=>	'saswpforwp-colorpicker',
														'value' 		=>	isset( $sd_data['saswp-rf-page-star-color'] ) ? $sd_data['saswp-rf-page-star-color'] : '#ffb300'
													),
			'saswp-rf-page-meta-icon-color'			=>		array(
														'id' 			=>	'saswp-rf-page-meta-icon-color',
														'label' 		=>	'Meta icon color',
														'desc' 			=>	'',
														'name' 			=>	'sd_data[saswp-rf-page-meta-icon-color]',
														'type' 			=>	'color_picker',
														'wrapper_id' 	=> 	'',
														'class' 		=>	'saswpforwp-colorpicker',
														'value' 		=>	isset( $sd_data['saswp-rf-page-meta-icon-color'] ) ? $sd_data['saswp-rf-page-meta-icon-color'] : '#646464'
													),
		);

		return $style_fields;

	}


	/**
	 * Font weight options
	 * @since 	1.46
	 * */
	public function font_weight_options() {
		
		$options 	=	array(
						'' 				=>	'Default',
						'normal' 		=>	'Normal',
						'bold' 			=>	'Bold',
						'bolder' 		=>	'Bolder',
						'lighter' 		=>	'Lighter',
						'inherit' 		=>	'Inherit',
						'initial' 		=>	'Initial',
						'unset' 		=>	'Unset',
						'100' 			=>	'100',
						'200' 			=>	'200',
						'300' 			=>	'300',
						'400' 			=>	'400',
						'500' 			=>	'500',
						'600' 			=>	'600',
						'700' 			=>	'700',
						'800' 			=>	'800',
						'900' 			=>	'900',
					);
		return $options;

	}

	/**
	 * Font alignment options
	 * @since 	1.46
	 * */
	public function font_alignment_options() {
		
		$options 	=	array(
						'' 			=>	'Default',
						'left' 		=>	'Left',
						'right' 	=>	'Right',
						'center' 	=>	'Center',
						'justify' 	=>	'Justify',
					);
		return $options;

	}

	/**
	 * Save review features settings data
	 * @since	1.46
	 * */
	public function save_data() {
		
		if ( ! isset( $_POST['saswp_rf_page_nonce'] ) ) {
			return;
		}
		
		if ( ! wp_verify_nonce( $_POST['saswp_rf_page_nonce'], 'saswp_rf_page_action_nonce') ) {
			return;
		}	

		$tab 	=	isset( $_POST['tab'] ) ? sanitize_text_field( wp_unslash( $_POST['tab'] ) ) : 'review';

		$settings_data 	=	array();
		
		if ( ! empty( $_POST['saswp-rf-page-post-type'] ) ) {
			$settings_data['saswp-rf-page-post-type'] 			=	array_map( 'sanitize_text_field' , $_POST['saswp-rf-page-post-type'] );
		}else{
			$settings_data['saswp-rf-page-post-type'] 			=	array();
		}
		if ( ! empty( $_POST['saswp-rf-page-criteria-multiple'] ) ) {
			$settings_data['saswp-rf-page-criteria-multiple'] 	=	array_map( 'sanitize_text_field' , $_POST['saswp-rf-page-criteria-multiple'] );
		}else{
			$settings_data['saswp-rf-page-criteria-multiple'] 	=	array();
		}
		if ( ! empty( $_POST['saswp-rf-page-criteria-single'] ) ) {
			$settings_data['saswp-rf-page-criteria'] 			=	sanitize_text_field( wp_unslash( $_POST['saswp-rf-page-criteria-single'] ) );
		}
		if ( ! empty( $_POST['saswp-rf-page-criteria-multi'] ) ) {
			$settings_data['saswp-rf-page-criteria'] 			=	sanitize_text_field( wp_unslash( $_POST['saswp-rf-page-criteria-multi'] ) );
		}
		if ( ! empty( $_POST['saswp-rf-page-summary-layout-one'] ) ) {
			$settings_data['saswp-rf-page-summary-layout'] 		=	sanitize_text_field( wp_unslash( $_POST['saswp-rf-page-summary-layout-one'] ) );
		}
		if ( ! empty( $_POST['saswp-rf-page-summary-layout-two'] ) ) {
			$settings_data['saswp-rf-page-summary-layout'] 		=	sanitize_text_field( wp_unslash( $_POST['saswp-rf-page-summary-layout-two'] ) );
		}
		if ( ! empty( $_POST['saswp-rf-page-review-layout-one'] ) ) {
			$settings_data['saswp-rf-page-review-layout'] 		=	sanitize_text_field( wp_unslash( $_POST['saswp-rf-page-review-layout-one'] ) );
		}
		if ( ! empty( $_POST['saswp-rf-page-review-layout-two'] ) ) {
			$settings_data['saswp-rf-page-review-layout'] 		=	sanitize_text_field( wp_unslash( $_POST['saswp-rf-page-review-layout-two'] ) );
		}
		if ( ! empty( $_POST['saswp-rf-page-pagination-type'] ) ) {
			$settings_data['saswp-rf-page-pagination-type'] 	=	sanitize_text_field( wp_unslash( $_POST['saswp-rf-page-pagination-type'] ) );
		}
		
		$setting_fields 	=	self::get_settings_fields();
		foreach ( $setting_fields as $field ) {
			
			switch ( $field['type'] ) {

				case 'checkbox':
					if ( ! empty( $_POST[ $field['name'] ] ) ) {
						$settings_data[ $field['name'] ] 		=	true;
					}else{
						$settings_data[ $field['name'] ] 		=	false;
					}
				break;

				case 'text':
					if ( isset(  $_POST[ $field['name'] ] ) ) {
						$settings_data[ $field['name'] ] 	=	intval( $_POST[ $field['name'] ] );
					}
				break;

			}

		}

		$style_fields 	=	$this->get_style_settings();
		foreach ( $style_fields as  $field ) {

			switch ( $field['type'] ) {

				case 'text':
				case 'color_picker':
					if ( isset(  $_POST[ $field['name'] ] ) ) {
						$settings_data[ $field['name'] ] 	=	sanitize_text_field( $_POST[ $field['name'] ] );
					}
				break;

				case 'multi-columns':
					if ( isset(  $_POST[ $field['name'] ] ) && is_array( $_POST[ $field['name'] ] ) ) {
						$settings_data[ $field['name'] ] 	=	array_map( 'sanitize_text_field', $_POST[ $field['name'] ] );
					}
				break;

			}
		}

		if ( isset( $_POST['filter_option'] ) ) {
			$settings_data['saswp-rf-page-settings-filter-options'] 	=	array_map( 'sanitize_text_field' , $_POST['filter_option'] );
		}
  		
  		if ( ! empty( $settings_data ) ) {
  			$get_options   =	get_option( 'sd_rf_data' );
  			$merge_options =	$settings_data;
  			if ( ! empty( $get_options ) && is_array( $get_options ) ) {
  				$merge_options =	array_merge( $get_options, $settings_data );
  			}
  			update_option( 'sd_rf_data', $merge_options );
  		}

  		wp_redirect( admin_url( 'edit.php?post_type=saswp&page=structured_data_review_feature&tab='.$tab ) );

	}
	
	/**
	 * Add custom meta box to comment edit section
	 * @since 	1.46
	 * */
	public function add_meta_box() {
		
		add_meta_box(
	        'saswp_reviews_comment_module',
	        'Comment Reviews Module',
	        array( $this, 'saswp_reviews_comment_module_callback' ),
	        'comment',
	        'normal',
	        'high'
    	);

	}

	/**
	 * Reviews comments meta box callback
	 * @param 	$comment 	WP_Comment
	 * @since 	1.46
	 * */
	public function saswp_reviews_comment_module_callback( $comment ) {
		if ( is_object( $comment ) && ! empty( $comment->comment_ID ) ) {

			global $sd_data;
			$comment_id 	=	$comment->comment_ID;
			$criteria       = 	( isset( $sd_data['saswp-rf-page-criteria'] ) && $sd_data['saswp-rf-page-criteria'] == 'multi' );
			$multi_criteria = 	get_comment_meta( $comment_id, 'saswp_rf_form_multi_rating', true );
			$rating_label 	=	'Rating';
			if ( ! empty( $multi_criteria ) ) {
				$rating_label 	=	'Multi Criteria';
			}
			
			
			// echo "<pre>multi_criteria===== "; print_r($multi_criteria); die;
		?>
			<div class="saswp-rf-edit-comment-wrapper saswp-rf-form" style="background: #fff;padding: 0px;">
				<table class="form-table editcomment saswp-rf-edit-comment-table" role="presentation">

					<tr>
						<td class="saswp-rf-edit-comment-first">
							<label class="saswp-rf-edit-comment-label"><?php echo esc_html( $rating_label ); ?></label>
						</td>
						<td>
							<?php
							if ( ! empty( $multi_criteria ) && is_array( $multi_criteria ) ) {
								$criteria_count = 1;
								foreach ( $multi_criteria as $key => $value ) {
			
									$id 				=	'saswp-rf-edit-form-multi-criteria-' . $criteria_count;
									$name 				=	'saswp-rf-edit-comment-rating['.$key.']';
									$label 				=	ucfirst( str_replace( '-', ' ',  $key ) );
									$rating             = 	! empty( $value ) ? $value : 5;
									?>
				                	
				                	<div class="saswp-rf-form-rating-text"><?php echo esc_html( $label ); ?></div>	
									<div class="saswp-rf-form-rating-container">
										<div class="saswp-rating-container">
											<div id="<?php echo esc_attr( $id ) ?>"></div>
											<div class="saswp-rateyo-counter"></div>
											<input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $rating ); ?>" />
										</div>
									</div>
					                
					                <br>
				                <?php $criteria_count++;
								}
							} else {
				                
				                    $rt_rating = get_comment_meta( $comment_id, 'rating', true );
				                    if ( empty( $rt_rating ) ) {
				                    	$rt_rating = get_comment_meta( $comment_id, 'review_rating', true );
				                    } 
				                    if ( $rt_rating > 0 ) {
				                    	$rt_rating = round( $rt_rating, 1 ); 
				                    }else{
				                    	$rt_rating 	=	5;	
				                    }
				                    ?>
				                    <div class="saswp-rf-form-rating-text"><?php echo esc_html__( 'Rating', 'schema-and-structured-data-for-wp' ); ?></div>
									<div class="saswp-rf-form-rating-container">
										<div class="saswp-rating-container">
											<div id="saswp-rf-edit-form-rating"></div>
											<div class="saswp-rateyo-counter"></div>
											<input type="hidden" name="saswp_rf_form_rating" value="<?php echo esc_attr( $rt_rating ); ?>" />
										</div>
									</div>
				                 
				                <?php
							}
							?>
						</td>
					</tr>	

					<tr>
						<td class="saswp-rf-edit-comment-first">
							<label class="saswp-rf-edit-comment-label"><?php echo esc_html__( 'Title', 'schema-and-structured-data-for-wp' ); ?></label>
						</td>
						<td>
							<input id="saswp_review_form_title" class="saswp-rf-form-control" placeholder="<?php echo esc_attr__('Title', 'schema-and-structured-data-for-wp'); ?>" name="saswp_review_form_title" value="<?php echo esc_attr( get_comment_meta( $comment_id, 'saswp_review_form_title', true ) ); ?>" type="text" value="" size="30" aria-required="true">
						</td>
					</tr>

					<?php 
					$pros = get_comment_meta( $comment_id, 'saswp_rf_form_pros', true );
					$cons = get_comment_meta( $comment_id, 'saswp_rf_form_cons', true );
					if ( ( ! empty( $pros ) && ! empty( $cons ) ) || ! empty( $sd_data['saswp-rf-page-settings-pros-cons'] ) ) {
					?>
					<tr>
						<td class="saswp-rf-edit-comment-first">
							<label class="saswp-rf-edit-comment-label"><?php echo esc_html__( 'Pros & Cons', 'schema-and-structured-data-for-wp' ); ?></label>
						</td>
						<td>
							<div class="saswp-rf-form-pros-cons-wrapper">
								<div class="saswp-rf-form-pros-items saswp-rf-form-pros">
									<h4 class="saswp-rf-form-input-title">
										<span class="saswp-rf-form-item-icon"><i class="dashicons dashicons-thumbs-up"></i></span>
										<span class="saswp-rf-form-item-text"><?php echo esc_html__( 'PROS', 'schema-and-structured-data-for-wp' ); ?></span>
									</h4>
									<div id="saswp-rf-form-pros-field-wrapper">
											<?php 
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
						</td>
					</tr>	
					<?php
					}
					
					$get_attachment     =   get_comment_meta( get_comment_ID(), 'saswp_rf_form_attachment', true );
					
					if ( ( is_array( $get_attachment ) && ! empty( $get_attachment['imgs'] ) )  || ( ! empty( $sd_data['saswp-rf-page-settings-image-review'] ) ) ) {
					?>
					<tr>
						<td class="saswp-rf-edit-comment-first">
							<label class="saswp-rf-edit-comment-label"><?php echo esc_html__( 'Image', 'schema-and-structured-data-for-wp' ); ?></label>
						</td>
						<td>
							<div class="saswp-rf-form-media-buttons">
								<div class="saswp-rf-form-image-media-groups">
									<div class="saswp-rf-form-group saswp-rf-form-hide-reply">
										<?php 
										if ( is_array( $get_attachment ) && ! empty( $get_attachment['imgs'] ) ) {
										?>
											<div class="saswp-rf-form-preview-imgs">
												<?php 
												foreach ( $get_attachment['imgs'] as $image ) {
													$attach_url 	=	wp_get_attachment_url( $image );
												?>
													<div class="saswp-rf-form-preview-img">
														<img src="<?php echo esc_url( $attach_url ); ?>">
														<input type="hidden" name="saswp_rf_form_attachment[imgs][]" value="<?php echo esc_attr( $image ); ?>">
														<span class="saswp-rf-form-file-remove" data-id="<?php echo esc_attr( $image ); ?>">x</span>
													</div>
												<?php
												}
												?>
											</div>
										<?php 
										}
										?>
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
							</div>
						</td>
					</tr>
					<?php	
					}
					?>

				</table>
				<?php wp_nonce_field('saswp_rf_edit_comment_action_nonce', 'saswp_rf_edit_comment_nonce'); ?>
			</div> <!-- saswp-rf-comment-edit-wrapper div end -->
		<?php
		}

	}

	/**
	 * Save edited comment meta
	 * @param 	$comment_id 	integer
	 * @since 	1.46
	 * */
	public function save_edit_comment_data( $comment_id ) {
		
		if ( ! isset( $_POST['saswp_rf_edit_comment_nonce'] ) ) {
			return;
		}
		
		if ( ! wp_verify_nonce( $_POST['saswp_rf_edit_comment_nonce'], 'saswp_rf_edit_comment_action_nonce') ) {
			return;
		}

		if ( isset( $_POST['saswp_review_form_title'] ) ) {
			$title 	=	sanitize_text_field( wp_unslash( $_POST['saswp_review_form_title'] ) );
			update_comment_meta( $comment_id, 'saswp_review_form_title', $title );
		}

		if ( isset( $_POST['saswp_rf_form_rating'] ) ) {
			$rating 	=	floatval( $_POST['saswp_rf_form_rating'] );
			update_comment_meta( $comment_id, 'rating', $rating );
			update_comment_meta( $comment_id, 'review_rating', $rating );
		}

		if ( isset( $_POST['saswp-rf-edit-comment-rating'] ) ) {
			$multi_rating 	=	array_map( 'floatval', $_POST['saswp-rf-edit-comment-rating'] );
			update_comment_meta( $comment_id, 'saswp_rf_form_multi_rating', $multi_rating );
			$comment_rating 	=	array_sum( $multi_rating );
			$comment_rating 	=	$comment_rating / count( $multi_rating );
			$comment_rating 	=	round( $comment_rating, 1 );
			update_comment_meta( $comment_id, 'rating', $comment_rating );
		}

		if ( isset( $_POST['saswp_rf_form_pros'] ) && is_array( $_POST['saswp_rf_form_pros'] ) ) {
			$comment_pros 		=	array_map( 'sanitize_text_field', $_POST['saswp_rf_form_pros'] );
			update_comment_meta( $comment_id, 'saswp_rf_form_pros', $comment_pros );
		}

		if ( isset( $_POST['saswp_rf_form_cons'] ) && is_array( $_POST['saswp_rf_form_cons'] ) ) {
			$comment_cons 		=	array_map( 'sanitize_text_field', $_POST['saswp_rf_form_cons'] );
			update_comment_meta( $comment_id, 'saswp_rf_form_cons', $comment_cons );
		}

		$attachments 	=	get_comment_meta( $comment_id, 'saswp_rf_form_attachment', true );
		
		if ( is_array( $attachments ) && isset( $attachments['imgs'] ) ) {
			$attachments['imgs'] 	=	$attachments['imgs'];
		}else{
			$attachments 			=	array();
			$attachments['imgs'] 	=	array();
		}
		if ( isset( $_POST['saswp_rf_form_attachment'] ) && isset( $_POST['saswp_rf_form_attachment']['imgs'] ) ) {
			$attachments['imgs'] = array_map( 'absint', $_POST['saswp_rf_form_attachment']['imgs'] );
		}
		update_comment_meta( $comment_id, 'saswp_rf_form_attachment', $attachments );
		
	}
}