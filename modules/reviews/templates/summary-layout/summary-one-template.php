<?php
/**
 * Review summary template
 * @since 	1.45 
 * */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

global $sd_data
?>
<div class="saswp-rf-template-summary-wrapper">

	<?php 
	$average_rating 	=	SASWP_Review_Feature_Frontend::get_average_rating( get_the_ID() );
	if ( $average_rating ) {
	?>
		<div class="saswp-rf-template-summary-layout-wrapper">
			<div class="saswp-rf-template-summary-rating-wrapper">

				<div class="saswp-rf-template-rating-number">
	                <span class="saswp-rf-template-rating"><?php echo esc_html( $average_rating ); ?></span>
	                <span class="saswp-rf-template-rating-overall">/5</span>
	            </div>

		        <div class="saswp-rf-template-rating-icon">
	                <?php echo SASWP_Review_Feature_Frontend::summary_review_stars( $average_rating ); ?>
	                <div class="saswp-rf-template-rating-text"> 
	                    <?php 
	                        printf(
	                            esc_html( _n( 'Based on %d rating', 'Based on %d ratings', $rf_rating_count, 'review-schema' ) ),
	                            esc_html( $rf_rating_count ) 
	                        ); 
	                    ?> 
	                </div>
	            </div>

			</div>
		</div>
	<?php
	}

	if ( isset( $sd_data['saswp-rf-page-criteria'] ) && $sd_data['saswp-rf-page-criteria'] == 'multi' ) {
	?>
		<div class="saswp-rf-template-summary-layout-wrapper">
			<div class="saswp-rf-template-multi-wrapper">
			<?php 
				$multi_rating 	= 	SASWP_Review_Feature_Frontend::get_multi_criteria_average( get_the_ID() );	
				if ( ! empty( $multi_rating ) ) {
					foreach( $multi_rating as $key => $value ) {
						
						if ( ! $value['avg'] ) continue;
						$title 	=	ucfirst( str_replace( '-', ' ', $key ) );
						?>
						<div class="saswp-rf-template-multi-progress">
							<label><?php echo esc_html( $title ); ?></label>
							<progress class="saswp-rf-template-progress-bar service-preogress" value="<?php echo esc_html( $value['avg'] * 20 ); ?>" max="100"></progress>
                    		<span class="progress-percent"><?php echo esc_html( $value['avg'] * 20 ); ?>%</span>
						</div>
						<?php
					}
				}
			?>
			</div>
		</div>
	<?php	
	}
	?>

</div>