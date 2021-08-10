<?php
/**
 * Help page
 *
 * @author   Magazine3
 * @category Admin
 * @path     view/help
 * @version 1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function saswp_help_meta_box_cb(){

    echo '<a href="'.esc_url(admin_url('admin.php?page=structured_data_options&tab=support')).'">'.saswp_t_string('Need Help').'</a>';   
}

/**
* Remove Add new menu
**/
function saswp_disable_new_posts() {
	// Hide sidebar link
	global $submenu;
	unset($submenu['edit.php?post_type=saswp'][10]);
        
}
add_action('admin_menu', 'saswp_disable_new_posts'); 	

function saswp_location_meta_box_cb($post){

	$location     = get_post_meta($post->ID, 'saswp_loc_display_on_front', true);

	?>

	<ul>
		<li>
			<label><input name="saswp_loc_display_on_front" id="saswp_loc_display_on_front" type="checkbox" value="1" <?php echo (  $location == 1  ? 'checked' : '' ); ?> /> <?php echo saswp_t_string('Display On Page Content'); ?>   </label>
		</li>
	</ul>
	<div class="saswp-front-location-inst <?php echo (  $location == 1  ? '' : 'saswp_hide' ); ?>">
		<p><?php echo saswp_t_string('There are three ways to display it.'); ?></p>
		<ul>
		<li><?php echo saswp_t_string('1. Using Gutenberg Block'); ?> <a target="_blank" href="https://structured-data-for-wp.com/docs/" ><?php echo saswp_t_string('Learn More'); ?></a></li>
		<li><?php echo saswp_t_string('2. Using Widget'); ?> <a target="_blank" href="https://structured-data-for-wp.com/docs/" ><?php echo saswp_t_string('Learn More'); ?></a></li>
		<li><?php echo saswp_t_string('3. Using shortcode'); ?> <a target="_blank" href="https://structured-data-for-wp.com/docs/" ><?php echo saswp_t_string('Learn More'); ?></a></li>
		<li><?php echo saswp_t_string('Shortcode'); ?> <input type="text" value='[saswp-location id="<?php echo get_the_ID(); ?>"]' readonly /></li>
		</ul>
	</div>
		
	<?php    		
}