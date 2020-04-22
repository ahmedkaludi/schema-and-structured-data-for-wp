<?php       
/**
 * Schema Type Page
 *
 * @author   Magazine3
 * @category Admin
 * @path     view/schema_type
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * List of hooks used in this file
 */
add_action( 'wp_ajax_saswp_get_manual_fields_on_ajax', 'saswp_get_manual_fields_on_ajax' ) ;
add_action( 'wp_ajax_saswp_get_reviews_on_load', 'saswp_get_reviews_on_load' ) ;
add_action( 'save_post', 'saswp_schema_type_add_meta_box_save' ) ;
add_action( 'add_meta_boxes', 'saswp_add_all_meta_boxes',99 ) ;

/**
 * Register a schema type metabox
 * @return null
 * @since version 1.0
 * 
 */
function saswp_add_all_meta_boxes() {
    
    global $saswp_metaboxes;

    saswp_remove_unwanted_metabox();        
    
    add_meta_box(
            'saswp_schema_type',
            esc_html__( 'Schema Type', 'schema-and-structured-data-for-wp' ),
            'saswp_schema_type_meta_box_callback',
            'saswp',
            'normal',
            'high'
    );
    
    add_meta_box( 'saswp_help_meta_box', 
                esc_html__('Help', 'schema-and-structured-data-for-wp' ), 
                'saswp_help_meta_box_cb', 
                'saswp', 
                'side', 'low' 
                );
        
    add_meta_box(
                'saswp_schema_options',
                esc_html__( 'Advance Schema Options', 'schema-and-structured-data-for-wp' ),
                'saswp_schema_options_meta_box_callback',
                'saswp',
                'advanced',
                'low'
                );
    add_meta_box( 
                'saswp_amp_select', 
                esc_html__( 'Placement','schema-and-structured-data-for-wp' ), 
                'saswp_select_callback', 'saswp',
                'normal', 
                'high' 
              );
    add_meta_box( 
                'saswp_reviews_form', 
                esc_html__( 'Reviews form Shortcode','schema-and-structured-data-for-wp' ), 
                'saswp_reviews_form_shortcode_metabox', 'saswp_reviews',
                'side', 
                'low' 
              );
    add_meta_box(
            'saswp_submitdiv',
                esc_html__( 'Publish' ), 
                'post_submit_meta_box',
                array('saswp', 'saswp_reviews'), 
                'side', 
                'high' 
            );

            $saswp_metaboxes[]= 'saswp_schema_type';
            $saswp_metaboxes[]= 'saswp_help_meta_box';
            $saswp_metaboxes[]= 'saswp_schema_options';
            $saswp_metaboxes[]= 'saswp_amp_select';
            $saswp_metaboxes[]= 'saswp_submitdiv';
            $saswp_metaboxes[]= 'saswp_reviews_form';
}
/**
 * Function to get schema type meta 
 * @global type $post
 * @param type $value
 * @return boolean
 * @since version 1.0
 */
function saswp_schema_type_get_meta( $value ) {

    global $post;

    $field = get_post_meta( $post->ID, $value, true );

    if ( ! empty( $field ) ) {
            return is_array( $field ) ? stripslashes_deep( $field ) : stripslashes( wp_kses_decode_entities( $field ) );
    } else {
            return false;
    }
}


function saswp_migrate_global_static_data($schema_type){
    
            $meta_list = array();
            $service           = new saswp_output_service();
            $meta_fields = $service->saswp_get_all_schema_type_fields($schema_type);

            foreach($meta_fields as $key => $field){
                $meta_list[$key] = 'manual_text';
            }
            
            return $meta_list;
}

/**
 * Function to generate html markup for schema type metabox
 * @param type $post
 * return null
 * @since version 1.0
 */
function saswp_schema_type_meta_box_callback( $post) {

                    wp_nonce_field( 'saswp_schema_type_nonce', 'saswp_schema_type_nonce' );  

                    $style_business_type = '';
                    $style_business_name = '';         
                    $style_review_name   = ''; 
                    $business_name       = '';
                    $schema_type         = '';
                    $business_type       = '';                                    
                    $speakable           = '';
                    $item_list_enable    = '';
                    $item_list_tags      = '';
                    $item_list_custom    = '';
                    $append_reviews      = '';  
                    $event_type          = '';

                    if($post){
            
                        $schema_options    = get_post_meta($post->ID, 'schema_options', true);            
                        $meta_list         = get_post_meta($post->ID, 'saswp_meta_list_val', true);                         
                        $fixed_text        = get_post_meta($post->ID, 'saswp_fixed_text', true);  
                        $taxonomy_term     = get_post_meta($post->ID, 'saswp_taxonomy_term', true);  
                        $fixed_image       = get_post_meta($post->ID, 'saswp_fixed_image', true);  
                        $cus_field         = get_post_meta($post->ID, 'saswp_custom_meta_field', true); 
                        $schema_type       = get_post_meta($post->ID, 'schema_type', true);     
                        $append_reviews    = get_post_meta($post->ID, 'saswp_enable_append_reviews', true);
                        $event_type        = get_post_meta($post->ID, 'saswp_event_type', true);                         
                        $speakable         = get_post_meta($post->ID, 'saswp_enable_speakable_schema', true);
                        $item_list_enable  = get_post_meta($post->ID, 'saswp_enable_itemlist_schema', true);
                        $item_list_tags    = get_post_meta($post->ID, 'saswp_item_list_tags', true);
                        $item_list_custom  = get_post_meta($post->ID, 'saswp_item_list_custom', true);
                        $business_type     = get_post_meta($post->ID, 'saswp_business_type', true);
                        $business_name     = get_post_meta($post->ID, 'saswp_business_name', true);
                                                                                                 
                        if($schema_type != 'local_business'){

                            $style_business_type = 'style="display:none"';
                            $style_business_name = 'style="display:none"';

                         }                            
                        }
                        $item_list_item = array(                                                                                    
                             'Article'               => 'Article',                                                              
                             'Course'                => 'Course',                                                                                                                                                                                                            
                             'Movie'                 => 'Movie',                                   
                             'Product'               => 'Product',                                
                             'Recipe'                => 'Recipe',                                                                                      
                        );
                        
                        $item_reviewed = array(                                                                                    
                             'Book'                  => 'Book',                             
                             'Course'                => 'Course',                             
                             'Event'                 => 'Event',                              
                             'HowTo'                 => 'HowTo',   
                             'local_business'        => 'LocalBusiness',                                 
                             'MusicPlaylist'         => 'Music Playlist',
                             'Movie'                 => 'Movie',   
                             'Organization'          => 'Organization',    
                             'Product'               => 'Product',                                
                             'Recipe'                => 'Recipe',                             
                             'SoftwareApplication'   => 'SoftwareApplication',
                             'MobileApplication'     => 'MobileApplication',
                             'VideoGame'             => 'VideoGame', 
                        );                                                             

                        $mappings_file = SASWP_DIR_NAME . '/core/array-list/schemas.php';
                
                        if ( file_exists( $mappings_file ) ) {
                            $all_schema_array = include $mappings_file;
                        }
                        
                        $mappings_sub_business = SASWP_DIR_NAME . '/core/array-list/local-sub-business.php';
                
                        if ( file_exists( $mappings_sub_business ) ) {
                            $sub_business_arr = include $mappings_sub_business;
                        }
                            
                          $event_type_list = array(                              
                                ''                 => 'Select Type (Optional)',
                                'BusinessEvent'    => 'BusinessEvent',
                                'ChildrensEvent'   => 'ChildrensEvent',
                                'ComedyEvent'      => 'ComedyEvent',
                                'CourseInstance'   => 'CourseInstance',
                                'DanceEvent'       => 'DanceEvent',
                                'DeliveryEvent'    => 'DeliveryEvent',
                                'EducationEvent'   => 'EducationEvent',
                                'EventSeries'      => 'EventSeries',
                                'ExhibitionEvent'  => 'ExhibitionEvent',
                                'Festival'         => 'Festival',
                                'FoodEvent'        => 'FoodEvent',
                                'LiteraryEvent'    => 'LiteraryEvent',
                                'MusicEvent'       => 'MusicEvent',
                                'PublicationEvent' => 'PublicationEvent',
                                'SaleEvent'        => 'SaleEvent',
                                'ScreeningEvent'   => 'ScreeningEvent',
                                'SocialEvent'      => 'SocialEvent',
                                'SportsEvent'      => 'SportsEvent',
                                'TheaterEvent'     => 'TheaterEvent',
                                'VisualArtsEvent'  => 'VisualArtsEvent'                           
                          );  
                        
                          $all_business_type               = $sub_business_arr['all_business_type'];

                          $all_medical_business_array      = $sub_business_arr['medicalbusiness'];                         
                          $all_automotive_array            = $sub_business_arr['automotivebusiness'];
                          $all_emergency_array             = $sub_business_arr['emergencyservice'];
                          $all_entertainment_array         = $sub_business_arr['entertainmentbusiness'];
                          $all_financial_array             = $sub_business_arr['financialservice'];
                          $all_food_establishment_array    = $sub_business_arr['foodestablishment']; 
                          $all_health_and_beauty_array     = $sub_business_arr['healthandbeautybusiness'];
                          $all_home_and_construction_array = $sub_business_arr['homeandconstructionbusiness'];
                          $all_legal_service_array         = $sub_business_arr['legalservice'];
                          $all_lodging_array               = $sub_business_arr['lodgingbusiness']; 
                          $all_sports_activity_location    = $sub_business_arr['sportsactivitylocation']; 
                          $all_store                       = $sub_business_arr['store'];
        ?>                   
        <!-- Below variable $style_business_type is static -->
        <div class="misc-pub-section">
            
            <div class="saswp-schema-type-section">
               
                <table class="option-table-class saswp-option-table-class">
                <tr>
                   <td><label for="schema_type"><?php echo esc_html__( 'Schema Type' ,'schema-and-structured-data-for-wp');?></label></td>
                   <td><select class="saswp-schame-type-select" id="schema_type" name="schema_type">
                        <?php

                          if(!empty($all_schema_array)){

                              foreach ($all_schema_array as $parent_type => $type) {

                               $option_html = '';   

                               foreach($type as $key => $value){
                                   
                                    $sel = '';
                                
                                    if($schema_type == $key){
                                        $sel = 'selected';
                                    }

                                    if($schema_type == 'Blogposting' && $key == 'BlogPosting'){
                                        $sel = 'selected';
                                    }
                                    
                                    $option_html.= "<option value='".esc_attr($key)."' ".esc_attr($sel).">".esc_html__($value, 'schema-and-structured-data-for-wp' )."</option>";    

                                }   

                                    echo '<optgroup label="'.esc_attr($parent_type).'">';
                                    //Escaping is done while adding data in this variable
                                    echo $option_html;   
                                    echo '</optgroup>';                                                                                 
                            }

                          }                                                                    
                        ?>
                    </select>                      
                   </td>
                </tr>                                                                                                                                                                         
                <tr class="saswp-business-type-tr" <?php echo $style_business_type; ?>>
                    <td>
                    <?php echo esc_html__('Business Type', 'schema-and-structured-data-for-wp' ); ?>    
                    </td>
                    <td>
                      <select id="saswp_business_type" name="saswp_business_type">
                        <?php

                          foreach ($all_business_type as $key => $value) {
                            $sel = '';
                            if($business_type==$key){
                              $sel = 'selected';
                            }
                            echo "<option value='".esc_attr($key)."' ".esc_attr($sel).">".esc_html__($value, 'schema-and-structured-data-for-wp' )."</option>";
                          }
                        ?>
                    </select>  
                    </td>
                </tr>
                
                <tr class="saswp-event-text-field-tr" <?php echo $style_business_type; ?>>
                    <td>
                    <?php echo esc_html__('Event Type', 'schema-and-structured-data-for-wp' ); ?>    
                    </td>
                    <td>
                      <select id="saswp_event_type" name="saswp_event_type">
                        <?php
                        
                          foreach ($event_type_list as $key => $value) {
                            $sel = '';
                            if($event_type==$key){
                              $sel = 'selected';
                            }
                            echo "<option value='".esc_attr($key)."' ".esc_attr($sel).">".esc_html__($value, 'schema-and-structured-data-for-wp' )."</option>";
                          }
                        ?>
                    </select>  
                    </td>
                </tr>
                
                <tr class="saswp-automotivebusiness-tr" <?php if(!array_key_exists($business_name, $all_automotive_array)){ echo 'style="display:none;"';}else{ echo $style_business_name;} ?>>
                    <td><?php echo esc_html__('Sub Business Type', 'schema-and-structured-data-for-wp' ); ?></td>
                    <td>
                        <select class="saswp-local-sub-type-2" id="saswp_automotive" name="saswp_business_name">
                            
                        <?php
                          foreach ($all_automotive_array as $key => $value) {
                            $sel = '';
                            if($business_name==$key){
                              $sel = 'selected';
                            }
                            echo "<option value='".esc_attr($key)."' ".esc_attr($sel).">".esc_html__($value, 'schema-and-structured-data-for-wp' )."</option>";
                          }
                        ?>
                            
                    </select>
                    </td>

                </tr>
                <tr class="saswp-emergencyservice-tr" <?php if(!array_key_exists($business_name, $all_emergency_array)){ echo 'style="display:none;"';}else{ echo $style_business_name;} ?>>
                <td><?php echo esc_html__('Sub Business Type', 'schema-and-structured-data-for-wp' ); ?></td>    
                <td>
                    <select class="saswp-local-sub-type-2" id="saswp_emergency_service" name="saswp_business_name">
                        <?php

                          foreach ($all_emergency_array as $key => $value) {
                            $sel = '';
                            if($business_name==$key){
                              $sel = 'selected';
                            }
                            echo "<option value='".esc_attr($key)."' ".esc_attr($sel).">".esc_html__($value, 'schema-and-structured-data-for-wp' )."</option>";
                          }
                        ?>
                    </select>
                </td>    
                </tr>
                <tr class="saswp-entertainmentbusiness-tr" <?php if(!array_key_exists($business_name, $all_entertainment_array)){ echo 'style="display:none;"';}else{ echo $style_business_name;} ?>>
                <td><?php echo esc_html__('Sub Business Type', 'schema-and-structured-data-for-wp' ); ?></td>    
                <td>
                    <select class="saswp-local-sub-type-2" id="saswp_entertainment" name="saswp_business_name">
                        <?php

                          foreach ($all_entertainment_array as $key => $value) {
                            $sel = '';
                            if($business_name == $key){
                              $sel = 'selected';
                            }
                            echo "<option value='".esc_attr($key)."' ".esc_attr($sel).">".esc_html__($value, 'schema-and-structured-data-for-wp' )."</option>";
                          }
                        ?>
                    </select>
                </td>    
                </tr>
                
                <tr class="saswp-medicalbusiness-tr" <?php if(!array_key_exists($business_name, $all_medical_business_array)){ echo 'style="display:none;"';}else{ echo $style_business_name;} ?>>
                <td><?php echo esc_html__('Sub Business Type', 'schema-and-structured-data-for-wp' ); ?></td>    
                <td>
                    <select class="saswp-local-sub-type-2" id="saswp_medicalbusiness" name="saswp_business_name">
                        <?php

                          foreach ($all_medical_business_array as $key => $value) {
                            $sel = '';
                            if($business_name == $key){
                              $sel = 'selected';
                            }
                            echo "<option value='".esc_attr($key)."' ".esc_attr($sel).">".esc_html__($value, 'schema-and-structured-data-for-wp' )."</option>";
                          }
                        ?>
                    </select>
                </td>    
                </tr>
                
                <tr class="saswp-financialservice-tr" <?php if(!array_key_exists($business_name, $all_financial_array)){ echo 'style="display:none;"';}else{ echo $style_business_name;} ?>>
                <td><?php echo esc_html__('Sub Business Type', 'schema-and-structured-data-for-wp' ); ?></td>    
                <td>
                    <select class="saswp-local-sub-type-2" id="saswp_financial_service" name="saswp_business_name">
                        <?php
                          foreach ($all_financial_array as $key => $value) {
                            $sel = '';
                            if($business_name == $key){
                              $sel = 'selected';
                            }
                            echo "<option value='".esc_attr($key)."' ".esc_attr($sel).">".esc_html__($value, 'schema-and-structured-data-for-wp' )."</option>";
                          }
                        ?>
                    </select>
                </td>    
                </tr>                        
                <tr class="saswp-foodestablishment-tr" <?php if(!array_key_exists($business_name, $all_food_establishment_array)){ echo 'style="display:none;"';}else{ echo $style_business_name;} ?>>
                <td><?php echo esc_html__('Sub Business Type', 'schema-and-structured-data-for-wp' ); ?></td>  
                <td>
                    <select class="saswp-local-sub-type-2" id="saswp_food_establishment" name="saswp_business_name">                        
                        <?php
                          foreach ($all_food_establishment_array as $key => $value) {
                            $sel = '';
                            if($business_name==$key){
                              $sel = 'selected';
                            }
                            echo "<option value='".esc_attr($key)."' ".esc_attr($sel).">".esc_html__($value, 'schema-and-structured-data-for-wp' )."</option>";
                          }
                        ?>
                    </select>
                </td>    
                </tr>
                <tr class="saswp-healthandbeautybusiness-tr" <?php if(!array_key_exists($business_name, $all_health_and_beauty_array)){ echo 'style="display:none;"';}else{ echo $style_business_name;} ?>>
                <td><?php echo esc_html__('Sub Business Type', 'schema-and-structured-data-for-wp' ); ?></td>   
                <td>
                    <select class="saswp-local-sub-type-2" id="saswp_health_and_beauty" name="saswp_business_name">
                        <?php


                          foreach ($all_health_and_beauty_array as $key => $value) {
                            $sel = '';
                            if($business_name==$key){
                              $sel = 'selected';
                            }
                            echo "<option value='".esc_attr($key)."' ".esc_attr($sel).">".esc_html__($value, 'schema-and-structured-data-for-wp' )."</option>";
                          }
                        ?>
                    </select>
                </td>    
                </tr>                        
                <tr class="saswp-homeandconstructionbusiness-tr" <?php if(!array_key_exists($business_name, $all_home_and_construction_array)){ echo 'style="display:none;"';}else{ echo $style_business_name;} ?>>
                <td><?php echo esc_html__('Sub Business Type', 'schema-and-structured-data-for-wp' ); ?></td>
                <td>
                    <select class="saswp-local-sub-type-2" id="saswp_home_and_construction" name="saswp_business_name">
                        <?php

                          foreach ($all_home_and_construction_array as $key => $value) {
                            $sel = '';
                            if($business_name==$key){
                              $sel = 'selected';
                            }
                            echo "<option value='".esc_attr($key)."' ".esc_attr($sel).">".esc_html__($value, 'schema-and-structured-data-for-wp' )."</option>";
                          }
                        ?>
                    </select>
                </td>    
                </tr>
                <tr class="saswp-legalservice-tr" <?php if(!array_key_exists($business_name, $all_legal_service_array)){ echo 'style="display:none;"';}else{ echo $style_business_name;} ?>>
                <td><?php echo esc_html__('Sub Business Type', 'schema-and-structured-data-for-wp' ); ?></td>
                <td>
                    <select class="saswp-local-sub-type-2" id="saswp_legal_service" name="saswp_business_name">
                        <?php

                          foreach ($all_legal_service_array as $key => $value) {
                            $sel = '';
                            if($business_name==$key){
                              $sel = 'selected';
                            }
                            echo "<option value='".esc_attr($key)."' ".esc_attr($sel).">".esc_html__($value, 'schema-and-structured-data-for-wp' )."</option>";
                          }
                        ?>
                    </select>
                </td>    
                </tr>
                <tr class="saswp-lodgingbusiness-tr" <?php if(!array_key_exists($business_name, $all_lodging_array)){ echo 'style="display:none;"';}else{ echo $style_business_name;} ?>>
                <td><?php echo esc_html__('Sub Business Type', 'schema-and-structured-data-for-wp' ); ?></td>
                <td>
                    <select class="saswp-local-sub-type-2" id="saswp_lodging" name="saswp_business_name">
                        <?php

                          foreach ($all_lodging_array as $key => $value) {
                            $sel = '';
                            if($business_name==$key){
                              $sel = 'selected';
                            }
                            echo "<option value='".esc_attr($key)."' ".esc_attr($sel).">".esc_html__($value, 'schema-and-structured-data-for-wp' )."</option>";
                          }
                        ?>
                    </select>
                </td>    
                </tr>
                <tr class="saswp-sportsactivitylocation-tr" <?php if(!array_key_exists($business_name, $all_sports_activity_location)){ echo 'style="display:none;"';}else{ echo $style_business_name;} ?>>
                <td><?php echo esc_html__('Sub Business Type', 'schema-and-structured-data-for-wp' ); ?></td>
                <td>
                    <select class="saswp-local-sub-type-2" id="saswp_sports_activity_location" name="saswp_business_name">
                        <?php

                          foreach ($all_sports_activity_location as $key => $value) {
                            $sel = '';
                            if($business_name==$key){
                              $sel = 'selected';
                            }
                            echo "<option value='".esc_attr($key)."' ".esc_attr($sel).">".esc_html__($value, 'schema-and-structured-data-for-wp' )."</option>";
                          }
                        ?>
                    </select>
                </td>    
                </tr>
                <tr class="saswp-store-tr" <?php if(!array_key_exists($business_name, $all_store)){ echo 'style="display:none;"';}else{ echo $style_business_name;} ?>>
                <td><?php echo esc_html__('Sub Business Type', 'schema-and-structured-data-for-wp' ); ?></td>    
                <td>
                    <select class="saswp-local-sub-type-2" id="saswp_store" name="saswp_business_name">
                        <?php


                          foreach ($all_store as $key => $value) {
                            $sel = '';
                            if($business_name==$key){
                              $sel = 'selected';
                            }
                            echo "<option value='".esc_attr($key)."' ".esc_attr($sel).">".esc_html__($value, 'schema-and-structured-data-for-wp' )."</option>";
                          }
                        ?>
                    </select>
                </td>    
                </tr>                
                                
                <!-- ItemList Schema type starts here -->
                <tr class="saswp-itemlist-text-field-tr" <?php echo $style_review_name; ?>>
                    <td><?php echo esc_html__('Item Type', 'schema-and-structured-data-for-wp' ); ?></td>
                    <td>

                        <select data-id="<?php if(is_object($post)){ echo esc_attr($post->ID); }  ?>" name="saswp_itemlist_item_type" class="saswp-itemlist-item-type-list">
                        <?php
                        
                          $item = get_post_meta($post->ID, 'saswp_itemlist_item_type', true);                                                                                        
                          foreach ($item_list_item as $key => $value) {
                            $sel = '';
                            if($item == $key){
                              $sel = 'selected';
                            }
                            echo "<option value='".esc_attr($key)."' ".esc_attr($sel).">".esc_html__($value, 'schema-and-structured-data-for-wp' )."</option>";
                          }
                        ?>
                    </select>                                                                
                    </td>
                </tr>                                                                                        
                <!-- ItemList Schema type ends here -->
                                
                <!-- Review Schema type starts here -->
                <tr class="saswp-review-text-field-tr" <?php echo $style_review_name; ?>>
                    <td><?php echo esc_html__('Item Reviewed Type', 'schema-and-structured-data-for-wp' ); ?></td>
                    <td>

                        <select data-id="<?php if(is_object($post)){ echo esc_attr($post->ID); }  ?>" name="saswp_review_item_reviewed_<?php echo $post->ID; ?>" class="saswp-item-reivewed-list">
                        <?php
                        
                          $item = get_post_meta($post->ID, 'saswp_review_item_reviewed_'.$post->ID, true);                                                                                        
                          foreach ($item_reviewed as $key => $value) {
                            $sel = '';
                            if($item == $key){
                              $sel = 'selected';
                            }
                            echo "<option value='".esc_attr($key)."' ".esc_attr($sel).">".esc_html__($value, 'schema-and-structured-data-for-wp' )."</option>";
                          }
                        ?>
                    </select>                                                                
                    </td>
                </tr>                                                                        
                
                <!-- Review Schema type ends here -->

                <tr>
                   <td>
                       <label for="saswp-itemlist"><?php echo esc_html__( 'ItemList ' ,'schema-and-structured-data-for-wp');?></label>
                   </td>
                   <td>
                       <div class="saswp-enable-speakable">
                           
                       <div class="saswp-item-list-div">
                           
                       <input class="saswp-enable-itemlist" type="checkbox" name="saswp_enable_itemlist_schema" value="1" <?php if($item_list_enable == 1){echo 'checked'; }else{ echo ''; } ?>>                                                                                                           
                                                 
                       <select  name="saswp_item_list_tags" id="saswp_item_list_tags" class="<?php if($item_list_enable == 1){echo ''; }else{ echo 'saswp_hide'; } ?>">
                           
                           <?php
                           
                           $list_tags = array(
                               'h1' => 'H1',
                               'h2' => 'H2',
                               'h3' => 'H3',
                               'h4' => 'H4',
                               'h5' => 'H5',
                               'h6' => 'H6',
                               'custom' => 'Custom',
                               
                           );
                           
                           foreach ($list_tags as $key => $tag){
                               
                               if($item_list_tags == $key){
                                   echo ' <option value="'.$key.'" selected>'.$tag.'</option>';
                               }else{
                                   echo ' <option value="'.$key.'">'.$tag.'</option>';
                               }
                                                              
                           }
                           
                           ?>                          
                        </select>               
                      
                       <input type="text" id="saswp_item_list_custom" name="saswp_item_list_custom" placeholder="classname" value="<?php echo esc_attr($item_list_custom); ?>" class="<?php if($item_list_enable == 1 && $item_list_tags == 'custom'){echo ''; }else{ echo 'saswp_hide'; } ?>">
                        
                       </div> 
                           <p class="saspw-item-list-note <?php if($item_list_enable == 1){echo ''; }else{ echo 'saswp_hide'; } ?>"><?php echo esc_html__( 'It will collect all the data from selected tag to a itemlist' ,'schema-and-structured-data-for-wp');?></p>
                       </div>
                      
                   </td>
                  
                </tr>
                
                <tr>
                   <td>
                       <label for="saswp-speakable"><?php echo esc_html__( 'Speakable' ,'schema-and-structured-data-for-wp');?></label>
                   </td>
                   <td>
                      <input class="saswp-enable-speakable" type="checkbox" name="saswp_enable_speakable_schema" value="1" <?php if(isset($speakable) && $speakable == 1){echo 'checked'; }else{ echo ''; } ?>>                                                                                                           
                   </td>
                </tr>
                                
                <tr>
                   <td>
                       <label for="saswp-append-reviews"><?php echo esc_html__('Add Reviews' ,'schema-and-structured-data-for-wp');?></label>
                   </td>
                   <td>
                      
                      <input class="saswp-attach-reviews saswp-enable-append-reviews" type="checkbox" name="saswp_enable_append_reviews" value="1" <?php if(isset($append_reviews) && $append_reviews == 1){echo 'checked'; }else{ echo ''; } ?>>                                                                                                           
                      <a class="saswp-attach-reviews">
                        <?php 
                        
                                $attached_rv_json = '';
                                $attached_rv      = get_post_meta($post->ID, 'saswp_attahced_reviews', true);     
                                if($attached_rv){
                                    $attached_rv_json = json_encode($attached_rv);
                                }
                                
                                $attached_col_json = '';
                                $attached_col      = get_post_meta($post->ID, 'saswp_attached_collection', true);     
                                if($attached_col){
                                    $attached_col_json = json_encode($attached_col);
                                }
                                                                
                                if($append_reviews == 1){
                                    
                                    $rv_text = '';
                                    
                                    if($attached_rv && count($attached_rv) > 0){
                                        $rv_text .= count($attached_rv). ' Reviews, ';
                                    }
                                    
                                    if($attached_col && count($attached_col) > 0){
                                        $rv_text .= count($attached_col). ' Collection';
                                    }
                                    if(!$rv_text){
                                         $rv_text = 0;
                                    }
                                    
                                    if(is_array($attached_rv)){
                                        echo '<span class="saswp-attached-rv-count">Attached '.esc_html($rv_text).'</span>';
                                    }else{
                                        echo '<span class="saswp-attached-rv-count">Attached 0</span>';
                                    }
                                    
                                }else{
                                    echo '<span class="saswp-attached-rv-count saswp_hide"> Attached 0</span>';
                                }                                
                        
                        ?> 
                      </a>
                      <div style="display:none;" id="saswp-embed-code-div">
                          <div class="saswp-add-rv-title"><?php echo esc_html__('Get reviews attached to the schema type with three different method.' ,'schema-and-structured-data-for-wp');?> <a target="_blank" href="https://structured-data-for-wp.com/docs/article/how-to-append-fetched-reviews-in-schema-markup/"><?php echo esc_html__('Learn More...' ,'schema-and-structured-data-for-wp');?></a></div>
                          <div class="saswp-thick-box-container">
                              
                        <div class="saswp-add-rv-popup" id="saswp-global-tabs">
                            <h2 class="nav-tab-wrapper">
                            <a class="nav-tab" data-id="saswp-add-rv-automatic"><?php echo esc_html__( 'Reviews' ,'schema-and-structured-data-for-wp');?></a>
                            <a class="nav-tab" data-id="saswp-add-rv-collection"><?php echo esc_html__( 'Collection' ,'schema-and-structured-data-for-wp');?></a>
                            <a class="nav-tab" data-id="saswp-add-rv-manual"><?php echo esc_html__( 'Shortcode' ,'schema-and-structured-data-for-wp');?></a>
                           </h2>
                        </div>
                           
                            <div class="saswp-global-container" id="saswp-add-rv-automatic">
                                <div class="saswp-add-rv-note"><strong><?php echo esc_html__( 'Note:' ,'schema-and-structured-data-for-wp');?></strong> <span><?php echo esc_html__( 'The attached reviews will only be added in Json-ld' ,'schema-and-structured-data-for-wp');?></span> </div>
                                <div data-type="review" class="saswp-add-rv-automatic-list">
                                
                                <?php 
                                                                                               
                                $reviews_service = new saswp_reviews_service();
                                
                                $reviews = $reviews_service->saswp_get_reviews_list_by_parameters(null, null, 10, 1);
                                
                                if($reviews){
                                    
                                   foreach($reviews as $key => $val){    
                                       
                                       $checked = '';
                                       echo '<div class="saswp-add-rv-loop" data-type="review" data-id="'.esc_attr($val['saswp_review_id']).'">';
                                                                              
                                       if(is_array($attached_rv) && in_array($val['saswp_review_id'], $attached_rv)){
                                           $checked = 'checked';
                                       }
                                       
                                       echo '<input class="saswp-attach-rv-checkbox" type="checkbox" '.$checked.'>  <strong> '.esc_attr($val['saswp_reviewer_name']).' ( Rating - '.esc_attr($val['saswp_review_rating']).' ) <span class="saswp-g-plus"><img src="'.esc_url($val['saswp_review_platform_icon']).'"/></span></strong>';
                                       echo '</div>';
                                       
                                   }
                                   
                                }
                                
                                ?>
                                    
                                </div>
                                
                                <?php 
                                 echo '<input id="saswp_attahced_reviews" type="hidden" name="saswp_attahced_reviews" value="'. esc_attr($attached_rv_json).'">';
                                ?>
                                
                                <div class="saswp-rv-not-found saswp_hide" data-type="review"><?php echo esc_html__( 'Reviews not found' ,'schema-and-structured-data-for-wp');?></div>
                                <span class="spinner" data-type="review"></span>
                                <div><a class="saswp-load-more-rv" data-type="review"><?php echo esc_html__( 'Load More...' ,'schema-and-structured-data-for-wp');?></a></div>
                                                                
                            </div>
                              
                            <div class="saswp-global-container" id="saswp-add-rv-collection">
                                <div class="saswp-add-rv-note"><strong>Note:</strong> <span><?php echo esc_html__( 'The attached collection will only be added in Json-ld' ,'schema-and-structured-data-for-wp');?></span> </div>
                                <div data-type="collection" class="saswp-add-rv-automatic-list">
                                
                                <?php 
                                                                                               
                                $review_service = new saswp_reviews_service();
                                $reviews  = $review_service->saswp_get_collection_list(10,1);
                                
                                if($reviews){
                                    
                                   foreach($reviews as $key => $val){    
                                       
                                       $checked = '';
                                       echo '<div class="saswp-add-rv-loop" data-type="collection" data-id="'.esc_attr($val['value']).'">';
                                                                              
                                       if(is_array($attached_col) && in_array($val['value'], $attached_col)){
                                           $checked = 'checked';
                                       }
                                       
                                       echo '<input class="saswp-attach-rv-checkbox" type="checkbox" '.$checked.'>  <strong> '.esc_attr($val['label']).' </strong>';
                                       echo '</div>';
                                       
                                   }
                                   
                                }
                                
                                ?>
                                    
                                </div>
                                
                                <?php 
                                 echo '<input id="saswp_attached_collection" type="hidden" name="saswp_attached_collection" value="'. esc_attr($attached_col_json).'">';                                 
                                ?>
                                
                                <div class="saswp-rv-not-found saswp_hide" data-type="collection"><?php echo esc_html__( 'Reviews not found' ,'schema-and-structured-data-for-wp');?></div>
                                <span class="spinner" data-type="collection"></span>
                                <div><a class="saswp-load-more-rv" data-type="collection"><?php echo esc_html__( 'Load More...' ,'schema-and-structured-data-for-wp');?></a></div>
                                                                
                            </div>  

                            <div class="saswp-global-container" id="saswp-add-rv-manual">
                               <p> <?php echo esc_html__('Output reviews in front and its schema markup in source by using below shortcode' ,'schema-and-structured-data-for-wp');?> </p>
                                <strong>[saswp-reviews]</strong><br>OR<br>
                                <strong>[saswp-reviews-collection id="your collection id"]</strong>
                            </div>
                              <a class="button button-default close-attached-reviews-popup"><?php echo esc_html__( 'OK' ,'schema-and-structured-data-for-wp');?></a>                          
                          </div>
                          
                      </div>
                   </td>
                </tr>
                
                
            </table>  
                
            </div>
            
            <div class="saswp-schema-modify-section">
                
                <!-- custom fields for schema output starts here -->
                              
                <table class="option-table-class saswp_modify_schema_checkbox">
                    <tr>
                        <td>    
                            <a class="button button-default saswp-modify-schema-toggle"><?php echo esc_html__( 'Modify Schema Output', 'schema-and-structured-data-for-wp' ) ?></a>                                                                                    
                            <input type="hidden" name="saswp_enable_custom_field" id="saswp_enable_custom_field" value="<?php echo isset($schema_options['enable_custom_field']) ? $schema_options['enable_custom_field']:'0'; ?>">
                        </td>
                    </tr>   
                </table>  
                
                <div class="saswp-modify-container <?php echo ((isset($schema_options['enable_custom_field']) && $schema_options['enable_custom_field'] == 1) ? '':'saswp_hide'); ?>">
                   
                        <?php
                        
                            $allowed_manaul = false;
                            if($schema_type == 'HowTo' || $schema_type == 'FAQ' || $schema_type == 'local_business'){                            
                                echo '<div class="saswp-enable-modify-schema">';                                
                            }else{
                                $allowed_manaul = true;
                                echo '<div class="saswp-enable-modify-schema saswp_hide">';
                            }
                            
                            echo '<strong>'.esc_html__( 'Choose Method', 'schema-and-structured-data-for-wp' ).'</strong>';
                            echo '<select name="saswp_modify_method" class="saswp-enable-modify-schema-output">';
                            
                            $method_option = array(
                                ''          => 'Select',
                                'automatic' => 'Automatic',
                                'manual'    => 'Manual'
                            );
                            
                            foreach($method_option as $key => $val){
                                
                                $sel = '';
                                                                
                                if($allowed_manaul && $key == 'automatic'){
                                        $sel = 'selected';
                                }else{
                                    
                                    if(isset($schema_options['saswp_modify_method'])){
                                        if($key == $schema_options['saswp_modify_method'] && !$allowed_manaul){
                                            $sel = 'selected';
                                        }
                                    }
                                    
                                }
                                
                                echo '<option value="'.esc_attr($key).'" '.esc_attr($sel).'>'.esc_html($val).'</option>';
                                
                            }
                            
                            echo '</select>';
                        
                        ?>                                    
                            </div>
                    
                    <div class="saswp-dynamic-container <?php echo ((isset($schema_options['saswp_modify_method']) && $schema_options['saswp_modify_method'] == 'automatic') ? '':'saswp_hide'); ?>">
                        
                        <div class="saswp-custom-fields-div">
                        <table class="saswp-custom-fields-table">
                           
                        <?php                         
                        if(!empty($meta_list)){  
                            
                            $review_fields = array();                            
                            $service       = new saswp_output_service();
                                                        
                            $schema_type    = get_post_meta($post->ID, 'schema_type', true);
                            
                            if($schema_type == 'Review'){
                                
                                $item_reviewed = get_post_meta($post->ID, 'saswp_review_item_reviewed_'.$post->ID, true);                                                                
                                $schema_type   = $item_reviewed;
                                
                                $review_fields['saswp_review_name']           = 'Review Name';
                                $review_fields['saswp_review_description']    = 'Review Description';                                
                                $review_fields['saswp_review_author']         = 'Review Author';
                                $review_fields['saswp_review_author_url']     = 'Review Author Profile URL';
                                $review_fields['saswp_review_publisher']      = 'Review Publisher';
                                $review_fields['saswp_review_publisher_url']  = 'Review Publisher URL';
                                $review_fields['saswp_review_rating_value']   = 'Review Rating Value';
                                $review_fields['saswp_review_date_published'] = 'Review Published Date'; 
                                $review_fields['saswp_review_date_modified']  = 'Review Modified Date'; 
                                $review_fields['saswp_review_url']            = 'Review URL'; 
                               
                            }
                            
                            $meta_fields = $service->saswp_get_all_schema_type_fields($schema_type);
                            
                            foreach($meta_list as $fieldkey => $fieldval){
                                                                                        
                            $option = '';
                            echo '<tr>'; 
                            echo '<td><select class="saswp-custom-fields-name">';
                            
                            if($review_fields){
                            
                                $option .= '<optgroup label="Reviews">';
                                
                                foreach ($review_fields as $key =>$val){
                                
                                if( $fieldkey == $key){
                                    
                                    $option .='<option value="'.esc_attr($key).'" selected>'.esc_attr($val).'</option>';   
                                 
                                }else{
                                    
                                    $option .='<option value="'.esc_attr($key).'">'.esc_attr($val).'</option>';   
                                 
                                }
                                
                              }
                                $option .= '</optgroup>';
                            }
                                                        
                            if($review_fields){
                                $option .= '<optgroup label="'.esc_attr($schema_type).'">'; 
                            }
                            
                            foreach ($meta_fields as $key =>$val){
                                
                                if( $fieldkey == $key){
                                    
                                    $option .='<option value="'.esc_attr($key).'" selected>'.esc_attr($val).'</option>';   
                                 
                                }else{
                                    
                                    $option .='<option value="'.esc_attr($key).'">'.esc_attr($val).'</option>';   
                                 
                                }
                                
                            }
                            
                            if($review_fields){
                                 $option .= '</optgroup>';
                            }
                                                        
                            echo $option;                            
                            echo '</select>';
                            echo '</td>';
                                                        
                            $list_html = '';
                            $meta_list_fields = include(SASWP_DIR_NAME . '/core/array-list/meta_list.php');                            
                            
                            $meta_list_arr = $meta_list_fields['text'];
                            
                            if ((strpos($fieldkey, '_image') !== false) || strpos($fieldkey, '_logo') !== false) {
                                  $meta_list_arr = $meta_list_fields['image'];
                            }
                                                                                    
                            foreach($meta_list_arr as $list){
                            
                                $list_html.= '<optgroup label="'.$list['label'].'">';
                                
                                foreach ($list['meta-list'] as $key => $val){
                                    
                                    if( $fieldval == $key){
                                        $list_html.= '<option value="'.esc_attr($key).'" selected>'.esc_html($val).'</option>';    
                                    }else{
                                        $list_html.= '<option value="'.esc_attr($key).'">'.esc_html($val).'</option>';    
                                    }
                                                                        
                                }
                                
                                $list_html.= '</optgroup>';
                                
                            } 
                            echo '<td>';
                            echo '<select class="saswp-custom-meta-list" name="saswp_meta_list_val['.$fieldkey.']">';
                            echo $list_html;
                            echo '</select>';
                            echo '</td>';
                                                        
                            if($fieldval == 'manual_text'){
                                 echo '<td><input type="text" name="saswp_fixed_text['.esc_attr($fieldkey).']" value="'.(isset($fixed_text[$fieldkey]) ? esc_html($fixed_text[$fieldkey]) :'').'"></td>';    
                            }else if($fieldval == 'taxonomy_term'){
                                
                                $choices    = array('all' => esc_html__('All','schema-and-structured-data-for-wp'));
                                $taxonomies = saswp_post_taxonomy_generator();        
                                $choices    = array_merge($choices, $taxonomies); 
                                
                                echo '<td>';
                                
                                if($choices){
                                    
                                    echo '<select name="saswp_taxonomy_term['.esc_attr($fieldkey).']">';
                                    
                                    foreach ($choices as $key => $val){
                                        
                                        echo '<option value="'.esc_attr($key).'" '.((isset($taxonomy_term[$fieldkey]) && $taxonomy_term[$fieldkey] == $key) ? 'selected' :'').'>'.esc_attr($val).'</option>';
                                        
                                    }
                                    echo '</select>';
                                    
                                }
                                echo '</td>';
                                                                
                            }else if($fieldval == 'custom_field'){
                                 echo '<td><select class="saswp-custom-fields-select2" name="saswp_custom_meta_field['.esc_attr($fieldkey).']">';
                                 echo '<option value="'.esc_attr($cus_field[$fieldkey]).'">'.preg_replace( '/^_/', '', esc_html( str_replace( '_', ' ', $cus_field[$fieldkey] ) ) ).'</option>';
                                 echo '</select></td>';
                                 
                            }else if($fieldval == 'fixed_image'){
                                            
                                            $image_pre    = '';
                                            $el_id        = strtolower($schema_type). '_'.$fieldkey;                                            
                                            $media_name   = 'saswp_fixed_image['.esc_attr($fieldkey).']';
                                            $media_url    = $fixed_image[$fieldkey]['thumbnail'];
                                            $media_width  = $fixed_image[$fieldkey]['width'];
                                            $media_height = $fixed_image[$fieldkey]['height'];
                                            
                                            if($media_url){
                                            
                                                    $image_pre = '<div class="saswp_image_thumbnail">
                                                                 <img class="saswp_image_prev" src="'.esc_attr($media_url).'" />
                                                                 <a data-id="'.esc_attr($el_id).'" href="#" class="saswp_prev_close">X</a>
                                                                 </div>'; 
                                            
                                                }
                                
                                            echo '<td>'
                                                .'<fieldset>'
                                                . '<input data-id="media" style="width: 30%;" class="button" id="'. esc_attr($el_id).'_button" name="'. esc_attr($el_id).'_button" type="button" value="Upload" />'
                                                . '<input type="hidden" data-id="'.esc_attr($el_id).'_height" class="upload-height" name="'.esc_attr($media_name).'[height]" id="'.esc_attr($el_id).'_height" value="'.esc_attr($media_height).'">'
                                                . '<input type="hidden" data-id="'.esc_attr($el_id).'_width" class="upload-width" name="'.esc_attr($media_name).'[width]" id="'.esc_attr($el_id).'_width" value="'.esc_attr($media_width).'">'
                                                . '<input type="hidden" data-id="'.esc_attr($el_id).'_thumbnail" class="upload-thumbnail" name="'.esc_attr($media_name).'[thumbnail]" id="'.esc_attr($el_id).'_thumbnail" value="'.esc_attr($media_url).'">'                                                
                                                . '<div class="saswp_image_div_'.esc_attr($el_id).'">'                                               
                                                . $image_pre                                                 
                                                . '</div>'
                                                . '</fieldset>'
                                                . '</td>';
                                
                            }else{
                                echo '<td></td>';
                            }
                                                        
                            echo '<td><a class="button button-default saswp-rmv-modify_row">X</a></td>';
                                                                                   
                            echo '</tr>';
                            
                            }
                            
                        }
                        
                        ?>
                                                      
                        </table>                    
                        <table class="option-table-class">
                            <tr><td><a class="button button-primary saswp-add-custom-fields"><?php echo esc_html__( 'Add Property', 'schema-and-structured-data-for-wp' ); ?></a></td><td></td></tr>   
                        </table>
                       
                        </div> 
                        
                    </div>
                    
                        <div class="saswp-static-container <?php echo ((isset($schema_options['saswp_modify_method']) && $schema_options['saswp_modify_method'] == 'manual') ? '':'saswp_hide'); ?>">
                        
                        <div class="saswp-manual-modification">
                        
                            <?php                        
                        
                                $output = '';
                                $common_obj = new saswp_view_common_class();
                                
                                $schema_type    = get_post_meta($post->ID, 'schema_type', true);

                                $schema_fields = saswp_get_fields_by_schema_type($post->ID, null, $schema_type, 'manual');
                                $output = $common_obj->saswp_saswp_post_specific($schema_type, $schema_fields, $post->ID, $post->ID, null, null, 1);
                                
                                if($schema_type == 'Review'){
                                                                        
                                    $item_reviewed     = get_post_meta($post->ID, 'saswp_review_item_reviewed_'.$post->ID, true);                         
                                    if(!$item_reviewed){
                                        $item_reviewed = 'Book';
                                    }
                                    $response          = saswp_get_fields_by_schema_type($post->ID, null, $item_reviewed);                                                                                                        
                                    $output           .= $common_obj->saswp_saswp_post_specific($schema_type, $response, $post->ID, $post->ID ,$item_reviewed, null, 1);

                                }
                                                                
                                echo $output;
                                                                                        
                            ?>
                            
                        </div>
                        <span class="spinner"></span>
                    </div>
                    
                </div>
                
               <!-- custom fields for schema output ends here -->
                
            </div>
                        
        </div>
            <?php
} 

function saswp_get_reviews_on_load(){
            
            if ( ! isset( $_GET['saswp_security_nonce'] ) ){
                return; 
            }
            if ( !wp_verify_nonce( $_GET['saswp_security_nonce'], 'saswp_ajax_check_nonce' ) ){
               return;  
            }
            $reviews    = array();
            $offset     = intval($_GET['offset']);
            $paged      = intval($_GET['paged']);
            $data_type  = sanitize_text_field($_GET['data_type']);
            
            if($paged && $offset){
                
                $reviews_service = new saswp_reviews_service();  
                
                if($data_type == 'review'){
                                                                      
                    $reviews = $reviews_service->saswp_get_reviews_list_by_parameters(null, null, 10, $paged, $offset);
                }
                
                if($data_type == 'collection'){
                    
                    $collection  = $reviews_service->saswp_get_collection_list(10, $paged, $offset);
                    
                    if($collection){
                        
                        foreach($collection as $col){
                            
                            $reviews[] = array(
                                'saswp_review_id'     => $col['value'],
                                'saswp_reviewer_name' => $col['label']
                            );
                            
                        }
                    }
                    
                }
                
                if($reviews){
                    echo json_encode(array('status' => 't', 'result' => $reviews));
                }else{
                    echo json_encode(array('status' => 't', 'message' => 'Reviews not found'));
                }
                
            }else{
                echo json_encode(array('status' => 'f', 'message' => 'Page number or offset is missing'));
            }
        wp_die();        
}

function saswp_get_manual_fields_on_ajax(){
    
            if ( ! isset( $_GET['saswp_security_nonce'] ) ){
                return; 
            }
            if ( !wp_verify_nonce( $_GET['saswp_security_nonce'], 'saswp_ajax_check_nonce' ) ){
               return;  
            } 
            $output      = '';
            $post_id     = intval($_GET['post_id']);
            $schema_type = sanitize_text_field($_GET['schema_type']);
        
            $common_obj = new saswp_view_common_class();

            $schema_fields = saswp_get_fields_by_schema_type($post_id, null, $schema_type, 'manual');
            
            $output = $common_obj->saswp_saswp_post_specific($schema_type, $schema_fields, $post_id, $post_id, null, null, 1);
            
            echo $output;

            wp_die();        
}


/**
 * Function to save schema type metabox value
 * @param type $post_id
 * @return type null
 * @since version 1.0
 */
function saswp_schema_type_add_meta_box_save( $post_id ) {     
            
                if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
                
                if ( ! isset( $_POST['saswp_schema_type_nonce'] ) || ! wp_verify_nonce( $_POST['saswp_schema_type_nonce'], 'saswp_schema_type_nonce' ) ) return;
                if ( ! current_user_can( 'edit_post', $post_id ) ) return;
                                                
                update_post_meta( $post_id, 'schema_type', sanitize_text_field( $_POST['schema_type'] ) );                
                update_post_meta( $post_id, 'saswp_business_type', sanitize_text_field( $_POST['saswp_business_type'] ) );
                update_post_meta( $post_id, 'saswp_event_type', sanitize_text_field( $_POST['saswp_event_type'] ) );

                if(isset($_POST['saswp_business_name'])){
                    update_post_meta( $post_id, 'saswp_business_name', sanitize_text_field( $_POST['saswp_business_name'] ) );   
                }else{
                    update_post_meta( $post_id, 'saswp_business_name', '' );   
                }
                if(isset($_POST['saswp_enable_speakable_schema'])){
                    update_post_meta( $post_id, 'saswp_enable_speakable_schema', intval($_POST['saswp_enable_speakable_schema']) );                                                                       
                }

                if(isset($_POST['saswp_enable_append_reviews'])){
                    update_post_meta( $post_id, 'saswp_enable_append_reviews', intval($_POST['saswp_enable_append_reviews']) );                                                                       
                }else{
                    update_post_meta( $post_id, 'saswp_enable_append_reviews', 0);                                                                       
                }

                if(isset($_POST['saswp_enable_itemlist_schema'])){
                    update_post_meta( $post_id, 'saswp_enable_itemlist_schema', intval($_POST['saswp_enable_itemlist_schema']) );                                                                       
                }
                
                update_post_meta( $post_id, 'saswp_item_list_tags', sanitize_text_field($_POST['saswp_item_list_tags']) );                                                                       
                update_post_meta( $post_id, 'saswp_item_list_custom', sanitize_text_field($_POST['saswp_item_list_custom']) );                                                                       
                update_post_meta( $post_id, 'saswp_review_item_reviewed_'.$post_id, sanitize_text_field($_POST['saswp_review_item_reviewed_'.$post_id]) );                                                                       
                update_post_meta( $post_id, 'saswp_itemlist_item_type', sanitize_text_field($_POST['saswp_itemlist_item_type']) );                                                                       
                                                
                update_post_meta( $post_id, 'saswp_attahced_reviews', json_decode(wp_unslash($_POST['saswp_attahced_reviews'])) );                                                                       
                update_post_meta( $post_id, 'saswp_attached_collection', json_decode(wp_unslash($_POST['saswp_attached_collection'])) );                                                                       
                
                $common_obj = new saswp_view_common_class();
                
                $post_obj[] = (object) array(
                    'ID' => $post_id
                );
                
                $common_obj->saswp_save_common_view($post_id, $post_obj);                                              
}           