<?php
/**
 * Attachment layout template
 * @since   1.45 
 * */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

global $sd_data;

$get_attachment     =   get_comment_meta( get_comment_ID(), 'saswp_rf_form_attachment', true );
if ( $get_attachment ) {  
?>
<div class="saswp-rf-template-review-item-media">
    <?php 
    if ( ! empty( $sd_data['saswp-rf-page-settings-image-review'] ) ) { 
        if ( isset( $get_attachment['imgs'] ) ) {  
        foreach( $get_attachment['imgs'] as $img) { ?> 
            <div class="saswp-rf-template-review-media-item saswp-rf-template-review-media-image">
                <a class="saswp-rf-template-review-attachment-img" data-featherlight="image" href="<?php echo wp_get_attachment_image_url( $img, '' ); ?>"><?php echo wp_get_attachment_image( $img, array('70', '70'), "thumbnail" ); ?></a>
            </div>
        <?php } } 
    } ?>  

    <?php   
    if ( ! empty( $sd_data['saswp-rf-page-settings-video-review'] ) ) { 
        if ( isset( $get_attachment['videos'] ) ) {  
            foreach( $get_attachment['videos'] as $video ) { ?>   
                <div class="saswp-rf-template-review-media-item saswp-rf-template-review-media-video">
                    <?php
                        $self_video = ( isset( $get_attachment['video_source'] ) && $get_attachment['video_source'] == 'self' ); 
                        $youtube_video_id = '';
                        if ( !$self_video ) {
                            $pattern = '#^(?:https?://)?(?:www\.)?(?:youtu\.be/|youtube\.com(?:/embed/|/v/|/watch\?v=|/watch\?.+&v=))([\w-]{11})(?:.+)?$#x';
                            preg_match($pattern, $video, $matches);
                            $youtube_video_id = (isset($matches[1])) ? $matches[1] : '';
                        } 

                        $place_holder_img  =   SASWP_DIR_URI.'admin_section/images/saswp-img-placeholder.jpg';
                        $image_url = $self_video ? $place_holder_img : 'https://img.youtube.com/vi/'. $youtube_video_id .'/default.jpg'; 

                        $video_url = $self_video ? wp_get_attachment_url( $video ) : 'https://www.youtube.com/embed/'.$youtube_video_id; 
                    ?>
                    <img src="<?php echo esc_url( $image_url ); ?>" style="width: 80px;" alt="<?php echo esc_attr__( 'Review Schema', 'schema-and-structured-data-for-wp' ); ?>">  
                    <?php if ( !$self_video ) { ?>
                        <a href="<?php echo esc_url( $video_url ); ?>?rel=0&amp;autoplay=1" data-featherlight="iframe" data-featherlight-iframe-width="640" data-featherlight-iframe-height="480" data-featherlight-iframe-frameborder="0" data-featherlight-iframe-allow="autoplay; encrypted-media" data-featherlight-iframe-allowfullscreen="true" class="saswp-rf-template-review-video-icon"><i class="dashicons dashicons-play"></i></a> 
                    <?php } else { ?>
                        <a href="#" data-video-url="<?php echo esc_url( $video_url ); ?>" class="saswp-rf-template-review-video-icon saswp-rf-template-review-play-self-video"><i class="dashicons dashicons-play"></i></a> 
                    <?php } ?>
                </div>
            <?php 
            } 
        }
    } 
    ?>  
</div>
<?php } ?> 
