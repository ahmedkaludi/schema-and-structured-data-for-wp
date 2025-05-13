<?php
/**
 * Review summary template
 * @since 	1.45 
 * */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
global $sd_data;
?>
<div class="saswp-rf-template-summary-wrapper-two">
	<div class="saswp-rf-template-rating-summary">
		<?php 
		$average_rating 	=	SASWP_Review_Feature_Frontend::get_average_rating( get_the_ID() );
		if ( $average_rating ) {
		?>
			<div class="saswp-rf-template-rating-item">
				<div class="saswp-rf-template-circle">

					<div class="saswp-rf-template-circle-bar">
						<svg> 
	                        <circle cx="70" cy="70" r="80" style="stroke-dashoffset: <?php echo esc_attr( 490 - ( 490 * ( $average_rating * 20 ) ) / 100 ); ?>;"></circle>
	                        <circle cx="70" cy="70" r="80" style="stroke-dashoffset: <?php echo esc_attr( 490 - ( 490 * ( $average_rating * 20 ) ) / 100 ); ?>;"></circle>
	                    </svg>
					</div>

					<div class="saswp-rf-template-circle-content">
						<div class="saswp-rf-template-rating-percent"><?php echo esc_html( $average_rating * 20 ); ?>%</div>
	                    <div class="saswp-rf-template-rating-text"><?php echo esc_html__( 'OVERALL', 'schema-and-structured-data-for-wp' ); ?></div>
	                    <div class="saswp-rf-template-rating-icon">
	                        <?php echo SASWP_Review_Feature_Frontend::summary_review_stars( $average_rating ); ?>
	                    </div>
					</div>

				</div>
			</div>
		<?php	
		}

		if ( isset( $sd_data['saswp-rf-page-criteria'] ) && $sd_data['saswp-rf-page-criteria'] == 'multi' ) {
		?>
			<div class="saswp-rf-template-rating-item">
	            <ul class="saswp-rf-template-rating-multi-criteria">
	                <?php 
	                $multi_rating 	= 	SASWP_Review_Feature_Frontend::get_multi_criteria_average( get_the_ID() );
	                foreach( $multi_rating as $key => $value ) {
	                    if ( !$value['avg'] ) continue;
	                    $title 	=	ucfirst( str_replace( '-', ' ', $key ) );
	                    ?>   
	                    <li>
	                        <label><?php echo esc_html( $title ); ?></label>
	                        <?php echo SASWP_Review_Feature_Frontend::summary_review_stars( $value['avg'] ); ?>
	                    </li>
	                <?php 
	            	} 
	            	?>  
	            </ul>
	        </div> 
		<?php
		}
		?>
	</div>
</div>