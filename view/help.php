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
function saswp_help_meta_box_cb() {

    echo '<a href="'. esc_url( admin_url( 'admin.php?page=structured_data_options&tab=support' )). '">'. esc_html__( 'Need Help', 'schema-and-structured-data-for-wp' ) .'</a>';   
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
			<label><input name="saswp_loc_display_on_front" id="saswp_loc_display_on_front" type="checkbox" value="1" <?php echo (  $location == 1  ? 'checked' : '' ); ?> /> <?php echo esc_html__( 'Display On Page Content', 'schema-and-structured-data-for-wp' ); ?>   </label>
		</li>
	</ul>
	<div class="saswp-front-location-inst <?php echo (  $location == 1  ? '' : 'saswp_hide' ); ?>">
		<p><?php echo esc_html__( 'There are three ways to display it.', 'schema-and-structured-data-for-wp' ); ?></p>
		<ul>
		<li><?php echo esc_html__( '1. Using Gutenberg Block', 'schema-and-structured-data-for-wp' ); ?> <a target="_blank" href="https://structured-data-for-wp.com/docs/" ><?php echo esc_html__( 'Learn More', 'schema-and-structured-data-for-wp' ); ?></a></li>
		<li><?php echo esc_html__( '2. Using Widget', 'schema-and-structured-data-for-wp' ); ?> <a target="_blank" href="https://structured-data-for-wp.com/docs/" ><?php echo esc_html__( 'Learn More', 'schema-and-structured-data-for-wp' ); ?></a></li>
		<li><?php echo esc_html__( '3. Using shortcode', 'schema-and-structured-data-for-wp' ); ?> <a target="_blank" href="https://structured-data-for-wp.com/docs/" ><?php echo esc_html__( 'Learn More', 'schema-and-structured-data-for-wp' ); ?></a></li>
		<li><?php echo esc_html__( 'Shortcode', 'schema-and-structured-data-for-wp' ); ?> <input type="text" value='[saswp-location id="<?php echo esc_attr(get_the_ID()); ?>"]' readonly /></li>
		</ul>
	</div>
		
	<?php    		
}