<?php
add_action( 'admin_menu', 'saswp_add_new_data_menu' );
add_action( 'admin_init', 'saswp_add_new_init');
add_action( 'admin_footer', 'saswp_add_new_svg_sprite');
add_action( 'wp_ajax_saswp_add_new_save_installer', 'saswp_add_new_save_steps_data', 10, 0 );
$saswp_add_data_type_config = array(
				'installer_dir' => 'admin_section',
				'plugin_title'  => esc_html__( ucfirst( 'Schema and Structured Data for WP' ), 'schema-and-structured-data-for-wp'),
				'start_steps' => 1,
				'total_steps' => 2,
				'installerpage' => 'saswp_add_new_data_type',
				'dev_mode' => false, 
				'steps' => array(
								1=>array(
								'title'=>esc_html__('Choose Schema type', 'schema-and-structured-data-for-wp'),
								'description'=>esc_html__('','schema-and-structured-data-for-wp'),
								'fields'=>saswp_add_new_schema_type_selection(),
								),
								2=>array(
								'title'=>esc_html__('Enjoy', 'schema-and-structured-data-for-wp'),
								'description'=>esc_html__('Navigate to ', 'schema-and-structured-data-for-wp'),
								'fields'=>'',
								),
							),
				'current_step'=>array(
							'title'=>'',
							'step_id'=>1
							)
			);
	
	function saswp_add_new_data_menu(){
		saswp_add_new_init();
	}

	function saswp_add_new_init(){
		// Exit if the user does not have proper permissions
		if(! current_user_can( 'manage_options' ) ) {
			return ;
		}
		global $saswp_add_data_type_config;
		saswp_add_new_steps_call();
	}

	function saswp_add_new_steps_call(){
		global $saswp_add_data_type_config;
		if ( empty( $_GET['page'] ) || $saswp_add_data_type_config['installerpage'] !== $_GET['page'] ) {
			return;
		}
		 if ( ob_get_length() ) {
			ob_end_clean();
		} 
		$step = isset( $_GET['step'] ) ? sanitize_key( $_GET['step'] ) :  $saswp_add_data_type_config['start_steps'];
		$title = $saswp_add_data_type_config['steps'][$step]['title'];
		$saswp_add_data_type_config['current_step']['step_id'] = $step;
		
		// Use minified libraries if dev mode is turned on.
		$suffix = '';
		// Enqueue styles.
		wp_enqueue_style( 'saswp_install', SASWP_PLUGIN_URL. $saswp_add_data_type_config['installer_dir']. '/css/saswp-install' . $suffix . '.css' , array( 'wp-admin' ), '0.1');
		// Enqueue javascript.
		wp_enqueue_script( 'saswp_install', SASWP_PLUGIN_URL. $saswp_add_data_type_config['installer_dir']. '/js/saswp-install' . $suffix . '.js' , array( 'jquery-core' ), '0.1' );
		//wp_enqueue_script( 'saswp_install_script', SASWP_PLUGIN_URL. '/admin_section/js/main-script.js' , array( 'jquery-core' ), '0.1' );
		
		wp_localize_script( 'saswp_install', 'saswp_install_params', array(
			'ajaxurl'      		=> admin_url( 'admin-ajax.php' ),
			'wpnonce'      		=> wp_create_nonce( 'saswp_install_nonce' ),
			'pluginurl'			=> SASWP_DIR_URI,
		) );
		

		ob_start();
		saswp_add_new_header(); ?>
		<div class="merlin__wrapper">
            <div class="amp_install_wizard"><?php esc_html_e('Schema and Structured Data Installation Wizard','schema-and-structured-data-for-wp'); ?></div>
			<div class="merlin__content merlin__content--<?php echo esc_attr( strtolower( $title ) ); ?>">
				<?php
				// Content Handlers.
				$show_content = true;

				if ( ! empty( $_REQUEST['save_step'] ) && isset( $saswp_add_data_type_config['current_step']['steps'] ) ) {
					//saswp_save_steps_data();
				}

				if ( $show_content ) {
					saswp_add_new_show_steps_body();
				} ?>

			<?php saswp_add_new_step_output_bottom_dots(); ?>

			</div>

			<?php echo sprintf( '<a class="return-to-dashboard" href="%s">%s</a>', esc_url( admin_url( 'admin.php?page=amp_options' ) ), esc_html( 'Return to dashboard' ) ); ?>

		</div>

		<?php saswp_add_new_install_footer(); 
		exit;
	}
	
	function saswp_add_new_show_steps_body(){
		global $saswp_add_data_type_config;
		if($saswp_add_data_type_config['total_steps']==$saswp_add_data_type_config['current_step']['step_id']){
			call_user_func('saswp_add_new_finish_page');
		}else{
			if(function_exists('saswp_add_new_step'.$saswp_add_data_type_config['current_step']['step_id'])){
				call_user_func('saswp_add_new_step'.$saswp_add_data_type_config['current_step']['step_id']);
			}else{
				call_user_func('saswp_add_new_finish_page');
			}
		}
	}
	function saswp_add_new_header() {
		global $saswp_installer_config;
		
		// Get the current step.
		$current_step = strtolower( $saswp_installer_config['steps'][$saswp_installer_config['current_step']['step_id']]['title'] ); ?>

		<!DOCTYPE html>
		<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
		<head>
			<meta name="viewport" content="width=device-width"/>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
			<title><?php echo ucwords($current_step); ?></title>
			<?php do_action( 'admin_print_styles' ); ?>
			<?php do_action( 'admin_print_scripts' ); ?>
			<?php do_action( 'admin_head' ); ?>
		</head>
		<body class="merlin__body merlin__body--<?php echo esc_attr( $current_step ); ?>">
		<?php
	}
	
	function saswp_add_new_step1(){
		global $saswp_add_data_type_config;
		$stepDetails = $saswp_add_data_type_config['steps'][$saswp_add_data_type_config['current_step']['step_id']];
		?>
		<div class="merlin__content--transition">

			<div class="amp_branding"></div>
			<svg class="icon icon--checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
				<circle class="icon--checkmark__circle" cx="26" cy="26" r="25" fill="none"/><path class="icon--checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
			</svg>

			<h1><?php echo $stepDetails['title']; ?></h1>

			<p><?php echo esc_html( 'This Installation Wizard helps you to setup the necessary options for AMP. It is optional & should take only a few minutes.' ); ?></p>
	
		</div>

		<footer class="merlin__content__footer">
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=amp_options' ) ); ?>" class="merlin__button merlin__button--skip"><?php echo esc_html( 'Cancel' ); ?></a>
			
			<a href="<?php echo esc_url( saswp_add_new_step_next_link() ); ?>" class="merlin__button merlin__button--next merlin__button--proceed merlin__button--colorchange"><?php echo esc_html( 'Start' ); ?></a>
			<?php wp_nonce_field( 'saswp_install_nonce' ); ?>
		</footer>
	<?php
	}
	
	function saswp_add_new_step2(){
		global $saswp_add_data_type_config;
		$stepDetails = $saswp_add_data_type_config['steps'][$saswp_add_data_type_config['current_step']['step_id']];
		?>

		<div class="merlin__content--transition">

			<div class="amp_branding"></div>
			<svg class="icon icon--checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
				<circle class="icon--checkmark__circle" cx="26" cy="26" r="25" fill="none"/><path class="icon--checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
			</svg>
			
			<h1><?php echo $stepDetails['title']; ?></h1>

			<p><?php echo isset($stepDetails['description'])? $stepDetails['description'] : ''; ?></p>
			
			
			
		</div>
		<form action="" method="post">
			
			<ul class="merlin__drawer--import-content">
				<?php 
					echo $stepDetails['fields'];
				?>
			</ul>
			

			<footer class="merlin__content__footer">
				<?php saswp_add_new_skip_button(); ?>
				
				<a id="skip" href="<?php echo esc_url( saswp_add_new_step_next_link() ); ?>" class="merlin__button merlin__button--skip merlin__button--proceed"><?php echo esc_html( 'Skip' ); ?></a>
				
				<a href="<?php echo esc_url( saswp_add_new_step_next_link() ); ?>" class="merlin__button merlin__button--next button-next" data-callback="save_logo">
					<span class="merlin__button--loading__text"><?php echo esc_html( 'Save' ); ?></span><?php echo ampforwp_loading_spinner(); ?>
				</a>
				
				<?php wp_nonce_field( 'ampforwp_install_nonce' ); ?>
			</footer>
		</form>
	<?php
	}
	
	




	
	
	function saswp_add_new_save_steps_data(){
		$pre_sd_data = get_option('sd_data');
		$sd_data = $_POST['sd_data'];
		if($pre_sd_data){
			$sd_data = array_merge($pre_sd_data,$sd_data);
		}
		update_option('sd_data',$sd_data);
		//
		if(isset($_POST['sd_data_create__post_schema']) && isset($_POST['sd_data_create__post_schema_checkbox'])){
			$checkbox = array_filter($_POST['sd_data_create__post_schema_checkbox']);
			if(count($checkbox)>0){
				foreach ($checkbox as $key => $value) {
					$postType = $_POST['sd_data_create__post_schema'][$key]['posttype'];
					$schemaType = $_POST['sd_data_create__post_schema'][$key]['schema_type'];
					
					$postarr = array(
		                  'post_type'=>'saswp',
		                  'post_title'=>'Default '.$postType.' Type',
		                  'post_status'=>'publish',
		                     );
					$insertedPageId = wp_insert_post(  $postarr );
					if($insertedPageId){
					$post_data_array  = array(
					                      array(
					                          'key_1'=>'post_type',
					                          'key_2'=>'equal',
					                          'key_3'=>$postType,
					                        )
					                      );
					$schema_options_array = array('isAccessibleForFree'=>False,'notAccessibleForFree'=>0,'paywall_class_name'=>'');
					update_post_meta( $insertedPageId, 'data_array', $post_data_array);
					update_post_meta( $insertedPageId, 'schema_type', $schemaType);
					update_post_meta( $insertedPageId, 'schema_options', $schema_options_array);
					}
				}
				
			}
			/**/



		}
		wp_send_json(
			array(
				'done' => 1,
				'message' => "Stored Successfully",
			)
		);
	}
	
	
	function saswp_add_new_skip_button(){
		?>
		<a href="<?php echo esc_url(  saswp_add_new_step_next_link() ); ?>" class="merlin__button merlin__button--skip"><?php echo esc_html( 'Skip' ); ?></a>
		<?php
	}
	function saswp_add_new_finish_page() {
		global $saswp_add_data_type_config;
		// Theme Name.
		$plugin_title 					= $saswp_add_data_type_config['plugin_title'];
		// Strings passed in from the config file.
		$strings = null;

		
		$allowed_html_array = array(
			'a' => array(
				'href' 		=> array(),
				'title' 	=> array(),
				'target' 	=> array(),
			),
		);

		update_option( 'ampforwp_installer_completed', time() ); ?>

		<div class="merlin__content--transition">

			<div class="amp_branding"></div>
			
			<h1><?php echo esc_html( 'Setup Done. Have fun!' ); ?></h1>

			<p><?php echo wp_kses(  'Basic Setup has been done. Navigate to AMP options panel to access all the options.','ampforwp_install' ); ?></p>

		</div> 

		<footer class="merlin__content__footer merlin__content__footer--fullwidth">
			
			<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=saswp&page=structured_data_options' ) ); ?>" class="merlin__button merlin__button--blue merlin__button--fullwidth merlin__button--popin"><?php echo esc_html( 'Let\'s Go' ); ?></a>
			
			
			<ul class="merlin__drawer merlin__drawer--extras">

				<li><?php //echo wp_kses( $link_1, $allowed_html_array ); ?></li>
				<li><?php //echo wp_kses( $link_2, $allowed_html_array ); ?></li>
				<li><?php //echo wp_kses( $link_3, $allowed_html_array ); ?></li>

			</ul>

		</footer>

	<?php
	}

	function saswp_add_new_loading_spinner(){
		global $saswp_add_data_type_config;
		$spinner = SASWP_DIR_NAME. $saswp_add_data_type_config['installer_dir']. '/images/spinner.php';

		// Retrieve the spinner.
		get_template_part(  $spinner );
	}
	
	function saswp_add_new_svg_sprite() {
		global $saswp_add_data_type_config;
		// Define SVG sprite file.
		$svg = SASWP_DIR_NAME. $saswp_add_data_type_config['installer_dir'] . '/images/sprite.svg' ;

		// If it exists, include it.
		if ( file_exists( $svg ) ) {
			require_once apply_filters( 'merlin_svg_sprite', $svg );
		}
	}
	function saswp_add_new_step_next_link() {
		global $saswp_add_data_type_config;
		$step = $saswp_add_data_type_config['current_step']['step_id'] + 1;

		return add_query_arg( 'step', $step );
	}
	
	function saswp_add_new_install_header() {
		global $saswp_add_data_type_config;
		
		// Get the current step.
		$current_step = strtolower( $saswp_add_data_type_config['steps'][$saswp_add_data_type_config['current_step']['step_id']]['title'] ); ?>

		<!DOCTYPE html>
		<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
		<head>
			<meta name="viewport" content="width=device-width"/>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
			<title><?php echo ucwords($current_step); ?></title>
			<?php do_action( 'admin_print_styles' ); ?>
			<?php do_action( 'admin_print_scripts' ); ?>
			<?php do_action( 'admin_head' ); ?>
		</head>
		<body class="merlin__body merlin__body--<?php echo esc_attr( $current_step ); ?>">
		<?php
	}
	
	
	function saswp_add_new_install_footer() {
		?>	 
		</body>
		<?php do_action( 'admin_footer' ); ?>
		<?php do_action( 'admin_print_footer_scripts' ); ?>
		</html>
		<?php
	}
	
	function saswp_add_new_makesvg( $args = array() ){
		// Make sure $args are an array.
		if ( empty( $args ) ) {
			return __( 'Please define default parameters in the form of an array.', 'accelerated-mobile-pages' );
		}

		// Define an icon.
		if ( false === array_key_exists( 'icon', $args ) ) {
			return __( 'Please define an SVG icon filename.', 'accelerated-mobile-pages' );
		}

		// Set defaults.
		$defaults = array(
			'icon'        => '',
			'title'       => '',
			'desc'        => '',
			'aria_hidden' => true, // Hide from screen readers.
			'fallback'    => false,
		);

		// Parse args.
		$args = wp_parse_args( $args, $defaults );

		// Set aria hidden.
		$aria_hidden = '';

		if ( true === $args['aria_hidden'] ) {
			$aria_hidden = ' aria-hidden="true"';
		}

		// Set ARIA.
		$aria_labelledby = '';

		if ( $args['title'] && $args['desc'] ) {
			$aria_labelledby = ' aria-labelledby="title desc"';
		}

		// Begin SVG markup.
		$svg = '<svg class="icon icon--' . esc_attr( $args['icon'] ) . '"' . $aria_hidden . $aria_labelledby . ' role="img">';

		// If there is a title, display it.
		if ( $args['title'] ) {
			$svg .= '<title>' . esc_html( $args['title'] ) . '</title>';
		}

		// If there is a description, display it.
		if ( $args['desc'] ) {
			$svg .= '<desc>' . esc_html( $args['desc'] ) . '</desc>';
		}

		$svg .= '<use xlink:href="#icon-' . esc_html( $args['icon'] ) . '"></use>';

		// Add some markup to use as a fallback for browsers that do not support SVGs.
		if ( $args['fallback'] ) {
			$svg .= '<span class="svg-fallback icon--' . esc_attr( $args['icon'] ) . '"></span>';
		}

		$svg .= '</svg>';

		return $svg;
	
	}
	
	/**
	 * Adds data attributes to the body, based on Customizer entries.
	 */
	function saswp_add_new_svg_allowed_html() {

		$array = array(
			'svg' => array(
				'class' => array(),
				'aria-hidden' => array(),
				'role' => array(),
			),
			'use' => array(
				'xlink:href' => array(),
			),
		);

		return $array;

	}
	
	function saswp_add_new_step_output_bottom_dots(){
		global $saswp_add_data_type_config;
		?>
		<ol class="dots">

			<?php for( $i = 1; $i<$saswp_add_data_type_config['total_steps']; $i++ ) :

				$class_attr = '';
				$show_link = false;

				if ( $i === $saswp_add_data_type_config['current_step']['step_id'] ) {
					$class_attr = 'active';
				} elseif ( $saswp_add_data_type_config['current_step']['step_id'] >  $i) {
					$class_attr = 'done';
					$show_link = true;
				} ?>

				<li class="<?php echo esc_attr( $class_attr ); ?>">
					<a href="<?php echo esc_url( add_query_arg( 'step', $i ) ); ?>" title="<?php echo esc_attr( $saswp_add_data_type_config['current_step']['title'] ); ?>"></a>
				</li>

			<?php endfor; ?>

		</ol>
		<?php
	}


function saswp_add_new_schema_type_selection(){
	global $sd_data;
	$settings = $sd_data;
	$returnHtml = '<li class="saswp_fields">
			<label>Data type</label>
			<select name="sd_data[saswp_kb_type]">
				<option value="Organization" '.($sd_data['saswp_kb_type']=='Organization'? 'selected' : '').'>Organization</option>
				<option value="Person" '.($sd_data['saswp_kb_type']=='Person'? 'selected' : '').'>Person</option>
			</select>
		</li>
		<li class="saswp_fields">
			<label>About Us Page</label>
			 '. wp_dropdown_pages( array( 
								'name' => 'sd_data[sd_about_page]', 
					            'id' => 'sd_about_page',
								'echo' => 0, 
								'show_option_none' => esc_attr( 'Select an item' ), 
								'option_none_value' => '', 
								'selected' =>  isset($settings['sd_about_page']) ? $settings['sd_about_page'] : '',
							)).'
		</li>
		<li class="saswp_fields">
			<label>Contact Us Page</label>
			'.wp_dropdown_pages( array( 
					'name' => 'sd_data[sd_contact_page]', 
		                        'id' => 'sd_contact_page-select',
					'echo' => 0, 
					'show_option_none' => esc_attr( 'Select an item' ), 
					'option_none_value' => '', 
					'selected' =>  isset($settings['sd_contact_page']) ? $settings['sd_contact_page'] : '',
				)).'
		</li>';
		return $returnHtml;
}

?>