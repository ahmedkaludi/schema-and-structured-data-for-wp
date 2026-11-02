<?php
/**
 * Attachment layout template
 * @since   1.45 
 * */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

global $sd_data;

$saswp_get_attachment     =   get_comment_meta( get_comment_ID(), 'saswp_rf_form_attachment', true );
if ( $saswp_get_attachment ) {  
?>
<div class="saswp-rf-template-review-item-media">
    <?php 
    if ( ! empty( $sd_data['saswp-rf-page-settings-image-review'] ) ) { 
        if ( isset( $saswp_get_attachment['imgs'] ) ) {  
        foreach( $saswp_get_attachment['imgs'] as $saswp_atta_img) { ?> 
            <div class="saswp-rf-template-review-media-item saswp-rf-template-review-media-image">
                <a class="saswp-rf-template-review-attachment-img" data-featherlight="image" href="<?php echo esc_url( wp_get_attachment_image_url( $saswp_atta_img, '' ) ); ?>"><?php echo wp_get_attachment_image( $saswp_atta_img, array('70', '70'), "thumbnail" ); ?></a>
            </div>
        <?php } } 
    } ?>  

    <?php   
    if ( ! empty( $sd_data['saswp-rf-page-settings-video-review'] ) ) { 
        if ( isset( $saswp_get_attachment['videos'] ) ) {  
            foreach( $saswp_get_attachment['videos'] as $saswp_video ) { ?>   
                <div class="saswp-rf-template-review-media-item saswp-rf-template-review-media-video">
                    <?php
                        $saswp_self_video = ( isset( $saswp_get_attachment['video_source'] ) && $saswp_get_attachment['video_source'] == 'self' ); 
                        $saswp_youtube_video_id = '';
                        if ( !$saswp_self_video ) {
                            $saswp_pattern = '#^(?:https?://)?(?:www\.)?(?:youtu\.be/|youtube\.com(?:/embed/|/v/|/watch\?v=|/watch\?.+&v=))([\w-]{11})(?:.+)?$#x';
                            preg_match($saswp_pattern, $saswp_video, $matches);
                            $saswp_youtube_video_id = (isset($matches[1])) ? $matches[1] : '';
                        } 

                        $saswp_place_holder_img  =   SASWP_DIR_URI.'admin_section/images/saswp-img-placeholder.jpg';
                        $saswp_image_url = $saswp_self_video ? $saswp_place_holder_img : 'https://img.youtube.com/vi/'. $saswp_youtube_video_id .'/default.jpg'; 

                        $saswp_video_url = $saswp_self_video ? wp_get_attachment_url( $saswp_video ) : 'https://www.youtube.com/embed/'.$saswp_youtube_video_id; 
                    ?>
                    <img src="<?php echo esc_url( $saswp_image_url ); ?>" style="width: 80px;" alt="<?php echo esc_attr__( 'Review Schema', 'schema-and-structured-data-for-wp' ); ?>">  
                    <?php if ( !$saswp_self_video ) { ?>
                        <a href="<?php echo esc_url( $saswp_video_url ); ?>?rel=0&amp;autoplay=1" data-featherlight="iframe" data-featherlight-iframe-width="640" data-featherlight-iframe-height="480" data-featherlight-iframe-frameborder="0" data-featherlight-iframe-allow="autoplay; encrypted-media" data-featherlight-iframe-allowfullscreen="true" class="saswp-rf-template-review-video-icon"><i class="dashicons dashicons-play"></i></a> 
                    <?php } else { ?>
                        <a href="#" data-video-url="<?php echo esc_url( $saswp_video_url ); ?>" class="saswp-rf-template-review-video-icon saswp-rf-template-review-play-self-video"><i class="dashicons dashicons-play"></i></a> 
                    <?php } ?>
                </div>
            <?php 
            } 
        }
    } 
    ?>  
</div>
<?php } ?> 
