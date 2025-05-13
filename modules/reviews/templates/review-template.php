<?php
/**
 * Review template
 * @since 	1.45 
 * */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

global $sd_data;	
$parent_class 	=	isset( $sd_data['saswp-rf-page-parent-class'] ) ? $sd_data['saswp-rf-page-parent-class'] : ''; 
?>

<div class="saswp-rf-template-wrapper <?php echo esc_attr( $parent_class ); ?>" id="comments">
	<?php 
	if ( have_comments() ) {

		$rf_rating_count 	=	SASWP_Review_Feature_Frontend::get_all_ratings( get_the_ID() );
		$summary_layout 	=	isset( $sd_data['saswp-rf-page-summary-layout'] ) ? $sd_data['saswp-rf-page-summary-layout'] : 'one';
		
		require SASWP_DIR_NAME . '/modules/reviews/templates/summary-layout/summary-'.$summary_layout.'-template.php';
		?>

		<?php if ( $rf_rating_count ) { ?>
		<div class="saswp-rf-template-sorting-bar">
			<h3 class="saswp-rf-template-sorting-title"> 
			<?php
			$review_title_escaped = esc_html(  sprintf(
				_n( 'Reviewed by %d user', 'Reviewed by %d users', $rf_rating_count, 'schema-and-structured-data-for-wp' ) ,
				$rf_rating_count
			) );
			echo $review_title_escaped;
			?>
			</h3>

			<div class="saswp-rf-template-sorting-select">
				<?php
					$filter = ( isset( $sd_data['saswp-rf-page-settings-filter'] ) && $sd_data['saswp-rf-page-settings-filter'] == '1' );
				if ( $filter ) {
					$filter_option = isset( $sd_data['saswp-rf-page-settings-filter-options'] ) ? $sd_data['saswp-rf-page-settings-filter-options'] : '';
					?>
				<div>
					<label><i class="dashicons dashicons-sort"></i> <?php esc_html_e( 'Sort:', 'schema-and-structured-data-for-wp' ); ?></label> 
					<select class="saswp_rf_template_review_filter saswp-rf-template-sort-filter" name="saswp_rf_template_review_sort_filter" data-type="sort">
						<option value="all"><?php esc_html_e( 'All Review', 'schema-and-structured-data-for-wp' ); ?></option>  
					<?php if ( in_array( 'top_rated', $filter_option ) ) { ?>
						<option value="top_rated"><?php esc_html_e( 'Top Rated', 'schema-and-structured-data-for-wp' ); ?></option>
						<?php } if ( in_array( 'low_rated', $filter_option ) ) { ?>
						<option value="low_rated"><?php esc_html_e( 'Low Rated', 'schema-and-structured-data-for-wp' ); ?></option>
						<?php } if ( in_array( 'recommended', $filter_option ) ) { ?>
						<option value="recommended"><?php esc_html_e( 'Recommended', 'schema-and-structured-data-for-wp' ); ?></option> 
						<?php } if ( in_array( 'highlighted', $filter_option ) ) { ?>
						<option value="highlighted"><?php esc_html_e( 'Highlighted', 'schema-and-structured-data-for-wp' ); ?></option>  
						<?php } if ( in_array( 'latest_first', $filter_option ) ) { ?>
						<option value="latest_first"><?php esc_html_e( 'Latest First', 'schema-and-structured-data-for-wp' ); ?></option> 
						<?php } if ( in_array( 'oldest_first', $filter_option ) ) { ?>
						<option value="oldest_first"><?php esc_html_e( 'Oldest First', 'schema-and-structured-data-for-wp' ); ?></option>  
						<?php } ?>
					</select>
				</div>
				<?php } ?>

				<?php
					$filter = ( isset( $sd_data['saswp-rf-page-settings-filter'] ) && $sd_data['saswp-rf-page-settings-filter'] == '1' );
				if ( $filter ) {
					?>
					<div>
						<label><i class="dashicons dashicons-filter"></i> <?php esc_html_e( 'Filter:', 'schema-and-structured-data-for-wp' ); ?></label> 
						<select class="saswp_rf_template_review_filter" name="saswp_rf_template_review_rating_filter" data-type="rating">
							<option value=""><?php esc_html_e( 'All Star', 'schema-and-structured-data-for-wp' ); ?></option>   
							<option value="5"><?php esc_html_e( '5 Star', 'schema-and-structured-data-for-wp' ); ?></option>  
							<option value="4"><?php esc_html_e( '4 Star', 'schema-and-structured-data-for-wp' ); ?></option>  
							<option value="3"><?php esc_html_e( '3 Star', 'schema-and-structured-data-for-wp' ); ?></option>  
							<option value="2"><?php esc_html_e( '2 Star', 'schema-and-structured-data-for-wp' ); ?></option>  
							<option value="1"><?php esc_html_e( '1 Star', 'schema-and-structured-data-for-wp' ); ?></option>  
						</select>
					</div>
				<?php } ?>
			</div>
		</div>
		<?php } ?>

		<div class="saswp-rf-template-comment-box">
			<ul class="saswp-rf-template-comment-list">
				<?php
				$args = [
					'post_id' => get_the_ID(),
					'status'  => 'approve', // Change this to the type of comments to be displayed
				];
				$comments = get_comments( $args );
				wp_list_comments(
					[
						'style'      => 'li',
						'short_ping' => true,
						'callback'   => [ SASWP_Review_Feature_Frontend::class, 'comment_list' ],
					],
					$comments
				);
				?>
			</ul>
		</div>
		<?php
		// Pagination starts here
		$pagination_type = isset( $sd_data['saswp-rf-page-pagination-type'] ) ? $sd_data['saswp-rf-page-pagination-type'] : 'number';
		
		if ( $pagination_type == 'number' ) {
			?>
			<?php 
			if ( get_the_comments_pagination() ) { 
			?>
				<div class="saswp-rf-template-paginate">
					<?php
					paginate_comments_links(
						[
							'prev_text' => '<i class="dashicons dashicons-arrow-left-alt2"></i>',
							'next_text' => '<i class="dashicons dashicons-arrow-right-alt2"></i>',
						]
					);
					?>
				</div>
			<?php
			} 

		} elseif ( $pagination_type == 'number-ajax' ) { 
			if ( get_the_comments_pagination() ) { 
		?>
				<div class="saswp-rf-template-paginate saswp-rf-template-paginate-ajax" data-max="<?php echo esc_attr( get_comment_pages_count() ); ?>">
					<?php
					paginate_comments_links(
						[
							'prev_text' => '<i class="dashicons dashicons-arrow-left-alt2"></i>',
							'next_text' => '<i class="dashicons dashicons-arrow-right-alt2"></i>',
						]
					);
					?>
				</div>

			<?php
			} 

		} elseif ( $pagination_type == 'load-more' ) { ?>
			<div class="saswp-rf-template-paginate saswp-rf-template-paginate-load-more saswp-rf-template-align-center">
				<a href="#" id="saswp-rf-template-load-more" data-max="<?php echo esc_attr( get_comment_pages_count() ); ?>"><?php echo esc_html_e( 'Load More', 'schema-and-structured-data-for-wp' ); ?></a>
			</div>
		<?php
		} elseif ( $pagination_type == 'auto-scroll' ) { ?>
			<div class="saswp-rf-template-paginate saswp-rf-template-paginate-onscroll" data-max="<?php echo esc_attr( get_comment_pages_count() ); ?>"></div>
		<?php 
		} 		
		
	}

	comment_form();
	?>
</div>