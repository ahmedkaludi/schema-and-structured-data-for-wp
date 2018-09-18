<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
add_action( 'add_meta_boxes', 'saswp_help_meta_box' );
function saswp_help_meta_box()
{
    add_meta_box( 'saswp_help_meta_box_id', 
            esc_html__('Help', 'schema-and-structured-data-for-wp' ), 
            'saswp_help_meta_box_cb', 
            'saswp', 
            'advanced', 'low' 
            );
}

function saswp_help_meta_box_cb()
{
    echo '<a href="admin.php?page=structured_data_options&tab=help">'.esc_html__('Need Help', 'schema-and-structured-data-for-wp').'</a>';   
}

/**
* Remove Add new menu
**/
function saswp_disable_new_posts() {
	// Hide sidebar link
	global $submenu;
	unset($submenu['edit.php?post_type=saswp'][10]);

	// Hide link on listing page
	if (isset($_GET['post_type']) && $_GET['post_type'] == 'saswp') {
	    return '<style type="text/css">
	    #favorite-actions, .add-new-h2, .tablenav { display:none; }
	    </style>';
	}
}
add_action('admin_menu', 'saswp_disable_new_posts');


add_action('admin_head-edit.php','saswp_addCustomImportButton');
function saswp_addCustomImportButton()
{
    global $current_screen;

    // Not our post type, exit earlier
    // You can remove this if condition if you don't have any specific post type to restrict to. 
    if ('saswp' != $current_screen->post_type) {
        return;
    }

    ?>
        <script type="text/javascript">
            jQuery(document).ready( function($)
            {
                jQuery(jQuery(".wrap a")[0]).after("<a href='<?php echo esc_url(admin_url('edit.php?post_type=saswp&page=structured_data_options')) ?>' id='' class='page-title-action'>Settings</a>");
            });
        </script>
    <?php
}
apply_filters( 'admin_url', 'wpse_271288_change_add_new_link_for_post_type', 10, 2 );
function wpse_271288_change_add_new_link_for_post_type( $url, $path ){
    if( $path === 'post-new.php?post_type=saswp' ) {
        $url = 'google.com';
    }
    return $url;
}
/**
 *  Finish setub and Import default settings 
 *
 */
//On module upgrade
add_action('plugins_loaded', 'ampforwp_schema_upgraded');
function ampforwp_schema_upgraded(){
	$moduleStatus = get_option("ampforwp_structure_data_module_upgread");
	if($moduleStatus){
		add_action('admin_notices', 'ampforwp_update_notice_structure_data');
	}
}
function ampforwp_update_notice_structure_data(){
	//$screen = get_current_screen();
	//if(is_object($screen) && $screen->base == 'saswp_page_structured_data_options'){
		echo ' <div class="notice notice-success is-dismissible">
			<p>
			 	Thank you For updating <strong>Structured Data</strong> <button type="button" id="finalized-import-structure-data-from-amp">Finish Setup</button>
			</p>
		</div>';
	//}
}

add_action("wp_ajax_ampforwp_import_structure_data", "ampforwp_import_structure_data");
function ampforwp_import_structure_data(){
	global $redux_builder_amp;
	$sd_data_update = array();
	switch($_REQUEST['from']){
		case 'ampforwp_basic_settings':
			$sd_data_update['sd-data-logo-ampforwp'] = $redux_builder_amp['amp-structured-data-logo'];
			$sd_data_update['saswp-logo-width'] = $redux_builder_amp['ampforwp-sd-logo-width'];
			$sd_data_update['saswp-logo-height'] = $redux_builder_amp['ampforwp-sd-logo-height'];
			$sd_data_update['sd_default_image'] = $redux_builder_amp['amp-structured-data-placeholder-image'];
			$sd_data_update['sd_default_image_width'] = $redux_builder_amp['amp-structured-data-placeholder-image-width'];
			$sd_data_update['sd_default_image_height'] = $redux_builder_amp['amp-structured-data-placeholder-image-height'];
			$sd_data_update['sd_default_video_thumbnail'] = $redux_builder_amp['amporwp-structured-data-video-thumb-url'];
			$ampforwp_sd_type_posts = $redux_builder_amp['ampforwp-sd-type-posts'];
			$ampforwp_sd_type_pages = $redux_builder_amp['ampforwp-sd-type-pages'];
 			$postarr = array(
                  'post_type'=>'saswp',
                  'post_title'=>'Default Page Type',
                  'post_status'=>'publish',
                     );
			$insertedPageId = wp_insert_post(  $postarr );
			if($insertedPageId){
			$post_data_array  = array(
			                      array(
			                          'key_1'=>'post_type',
			                          'key_2'=>'equal',
			                          'key_3'=>'page',
			                        )
			                      );
			$schema_options_array = array('isAccessibleForFree'=>False,'notAccessibleForFree'=>0,'paywall_class_name'=>'');
			update_post_meta( $insertedPageId, 'data_array', $post_data_array);
			update_post_meta( $insertedPageId, 'schema_type', $ampforwp_sd_type_pages);
			update_post_meta( $insertedPageId, 'schema_options', $schema_options_array);
			}
 			$postarr = array(
			          'post_type'=>'saswp',
			          'post_title'=>'Default Post Type',
			          'post_status'=>'publish',
 			            );
			$insertedPageId = wp_insert_post(  $postarr );
			if($insertedPageId){
			$post_data_array  = array(
			                      array(
			                          'key_1'=>'post_type',
			                          'key_2'=>'equal',
			                          'key_3'=>'post',
			                        )
			                      );
			$schema_options_array = array('isAccessibleForFree'=>False,'notAccessibleForFree'=>0,'paywall_class_name'=>'');
			update_post_meta( $insertedPageId, 'data_array', $post_data_array);
			update_post_meta( $insertedPageId, 'schema_type', $ampforwp_sd_type_posts);
			update_post_meta( $insertedPageId, 'schema_options', $schema_options_array);
			}
 		break;
	}
	update_option('sd_data', $sd_data_update);
	update_option('ampforwp_structure_data_module_upgread','migrated');
	echo json_encode(array("status"=>200,"message"=>"data imported successfully"));
	wp_die();
} 	