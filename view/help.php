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
            'side', 'low' 
            );
}

function saswp_help_meta_box_cb(){

    echo '<a href="'.esc_url(admin_url('admin.php?page=structured_data_options&tab=help')).'">'.esc_html__('Need Help', 'schema-and-structured-data-for-wp').'</a>';   
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
 	