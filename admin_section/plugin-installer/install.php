<?php
/**
 * Merlin WP
 * Better WordPress Theme Onboarding
 *
 * The following code is a derivative work from the
 * Envato WordPress Theme Setup Wizard by David Baker.

 * @link      https://merlinwp.com/
 * @author    Richard Tabor, from ThemeBeans.com
 * @copyright Copyright (c) 2017, Merlin WP of Inventionn LLC
 * @license   Licensed GPLv3 for open source use
 */	
	$saswp_installer_config = array();
        
        add_action('plugins_loaded', 'saswp_add_installer_config');
        
        function saswp_add_installer_config(){
            global $saswp_installer_config;
            $saswp_installer_config = array(
					'installer_dir' => 'plugin-installer',
					'plugin_title'  => esc_html__( ucfirst( 'Schema & Structured Data for WP' ), 'schema-and-structured-data-for-wp'),
					'start_steps'   => 1,
					'total_steps'   => 5,
					'installerpage' => 'saswp-setup-wizard',
					'dev_mode'      => false, 
					'steps'         => array(
									1=>array(
									'title'=>esc_html__('Welcome', 'schema-and-structured-data-for-wp'),
									'fields'=>'',
									'description'=>esc_html__('This wizard will set up AMP on your website, install plugin, and import content. It is optional & should take only a few minutes.','schema-and-structured-data-for-wp'),
									),
									2=>array(
									'title'=>esc_html__('General Settings', 'schema-and-structured-data-for-wp'),
									'description'=>esc_html__('', 'schema-and-structured-data-for-wp'),
									'fields'=>saswp_general_setting_fields_callback()
									),
									3=>array(
									'title'=>esc_html__('Social Profiles', 'schema-and-structured-data-for-wp'),
									'description'=>esc_html__('Would you like to setup Social Profiles?', 'schema-and-structured-data-for-wp'),
									'fields'=>saswp_social_profile_fields_callback(),
									),
									4=>array(
									'title'=>esc_html__('Select Schema', 'schema-and-structured-data-for-wp'),
									'description'=>esc_html__('Where would you like to enable the schema?', 'schema-and-structured-data-for-wp'),
									'fields'=>saswp_select_schema_fields_callback(),
									),
									5=>array(
									'title'=>esc_html__('Enjoy', 'schema-and-structured-data-for-wp'),
									'description'=>esc_html__('Navigate to ', 'schema-and-structured-data-for-wp'),
									'fields'=>'',
									),
								),
					'current_step'  =>array(
								'title'=>'',
								'step_id'=>1
								)
				);
            
            
        }
        
	add_action( 'admin_menu', 'saswp_add_admin_menu' );
	add_action( 'admin_init', 'saswp_installer_init');
	add_action( 'admin_footer', 'saswp_svg_sprite');
	add_action( 'wp_ajax_saswp_save_installer', 'saswp_save_steps_data', 10, 0 );
        
	function saswp_add_admin_menu(){
            
		global $saswp_installer_config;
		saswp_installer_init();
                
	}

	function saswp_installer_init(){
		// Exit if the user does not have proper permissions
		if(! current_user_can( saswp_current_user_can() ) ) {
			return ;
		}
		global $saswp_installer_config;
                if(!isset($_GET['_saswp_nonce'])){
                    return;
                }else{
                 $wp_nonce = $_GET['_saswp_nonce'];   
                 if( wp_verify_nonce($wp_nonce, 'saswp_install_wizard_nonce')){
			saswp_steps_call(); 		
		}     
                }                                              
		
	}

	function saswp_steps_call(){
            
		global $saswp_installer_config;
		if ( !wp_verify_nonce($_GET['_saswp_nonce'], 'saswp_install_wizard_nonce') || empty( $_GET['page'] ) || $saswp_installer_config['installerpage'] !== $_GET['page'] ) {
			return;
		}
		 if ( ob_get_length() ) {
			ob_end_clean();
		} 
		$step = isset( $_GET['step'] ) ? sanitize_key( $_GET['step'] ) :  $saswp_installer_config['start_steps'];
		$title = $saswp_installer_config['steps'][$step]['title'];
		$saswp_installer_config['current_step']['step_id'] = $step;
						
		// Enqueue styles.
		wp_enqueue_style( 'saswp_install', SASWP_PLUGIN_URL. '/admin_section/css/saswp-install.min.css' , array( 'wp-admin' ), SASWP_VERSION );
		// Enqueue javascript.
		wp_enqueue_script( 'saswp_install', SASWP_PLUGIN_URL. '/admin_section/js/saswp-install.min.js' , array( 'jquery' ,'jquery-ui-core', 'jquery-core' ), SASWP_VERSION );		
		
		wp_localize_script( 'saswp_install', 'saswp_install_params', array(
			'ajaxurl'      		=> admin_url( 'admin-ajax.php' ),
			'wpnonce'      		=> wp_create_nonce( 'saswp_install_nonce' ),
			'pluginurl'	        => SASWP_DIR_URI,
		) );
		

		ob_start();
		saswp_install_header(); ?>
		<div class="merlin__wrapper">
            <div class="saswp_install_wizard"><?php esc_html_e('Schema and Structured Data Installation Wizard','schema-and-structured-data-for-wp'); ?></div>
			<div class="merlin__content merlin__content--<?php echo esc_attr( strtolower( $title ) ); ?>">
				<?php
				// Content Handlers.
				$show_content = true;

				if ( ! empty( $_REQUEST['save_step'] ) && isset( $saswp_installer_config['current_step']['steps'] ) ) {
					//saswp_save_steps_data();
				}

				if ( $show_content ) {
					saswp_show_steps_body();
				} ?>

			<?php saswp_step_output_bottom_dots(); ?>

			</div>

			<?php echo sprintf( '<a class="return-to-dashboard" href="%s">%s</a>', esc_url( admin_url( 'admin.php?page=structured_data_options' ) ), esc_html__( 'Return to dashboard', 'schema-and-structured-data-for-wp' ) ); ?>

		</div>

		<?php saswp_install_footer(); 
		exit;
	}
	
	function saswp_show_steps_body(){
            
		global $saswp_installer_config;
		if($saswp_installer_config['total_steps']==$saswp_installer_config['current_step']['step_id']){
			call_user_func('saswp_finish_page');
		}else{
			if(function_exists('saswp_step'.$saswp_installer_config['current_step']['step_id'])){
				call_user_func('saswp_step'.$saswp_installer_config['current_step']['step_id']);
			}else{
				call_user_func('saswp_finish_page');
			}
		}
	}
	
	
	function saswp_step1(){
            
		global $saswp_installer_config;
		$stepDetails = $saswp_installer_config['steps'][$saswp_installer_config['current_step']['step_id']];
		?>
		<div class="merlin__content--transition">

			<div class="saswp_branding"></div>
			<svg class="icon icon--checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
				<circle class="icon--checkmark__circle" cx="26" cy="26" r="25" fill="none"/><path class="icon--checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
			</svg>

			<h1><?php echo $stepDetails['title']; ?></h1>

			<p><?php echo esc_html__( 'This Installation Wizard helps you to setup the necessary options for schema & structured data. It is optional & should take only a few minutes.', 'schema-and-structured-data-for-wp' ); ?></p>
	
		</div>

		<footer class="merlin__content__footer">
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=structured_data_options' ) ); ?>" class="merlin__button merlin__button--skip"><?php echo esc_html__( 'Cancel', 'schema-and-structured-data-for-wp' ); ?></a>
			
			<a href="<?php echo esc_url( saswp_step_next_link() ); ?>" class="merlin__button merlin__button--next merlin__button--proceed merlin__button--colorchange"><?php echo esc_html__( 'Start', 'schema-and-structured-data-for-wp' ); ?></a>
			<?php wp_nonce_field( 'saswp_install_nonce' ); ?>
		</footer>
	<?php
	}
	
	function saswp_step2(){
            
		global $saswp_installer_config;
		$stepDetails = $saswp_installer_config['steps'][$saswp_installer_config['current_step']['step_id']];
		
		?>

		<div class="merlin__content--transition">

			<div class="saswp_branding"></div>
			<svg class="icon icon--checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
				<circle class="icon--checkmark__circle" cx="26" cy="26" r="25" fill="none"/><path class="icon--checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
			</svg>
			<!--Escaping has been done above while adding to array ref array $saswp_installer_config-->
			<h1><?php echo $stepDetails['title']; ?></h1>

                        <p><?php echo isset($stepDetails['description'])? $stepDetails['description'] : ''; ?></p>
			
		</div>
		<form action="" method="post">
			
			<ul class="merlin__drawer--import-content">
				
				<?php 
				wp_enqueue_media ();
					echo $stepDetails['fields'];
				?>
				
			</ul>
			

			<footer class="merlin__content__footer">
				<?php saswp_skip_button(); ?>
				
				<a id="skip" href="<?php echo esc_url( saswp_step_next_link() ); ?>" class="merlin__button merlin__button--skip merlin__button--proceed"><?php echo esc_html__( 'Skip', 'schema-and-structured-data-for-wp' ); ?></a>
				
				<a href="<?php echo esc_url( saswp_step_next_link() ); ?>" class="merlin__button merlin__button--next button-next" data-callback="save_logo">
					<span class="merlin__button--loading__text"><?php echo esc_html__( 'Next', 'schema-and-structured-data-for-wp' ); ?></span><?php echo saswp_loading_spinner(); ?>
				</a>
				
				<?php wp_nonce_field( 'saswp_install_nonce' ); ?>
			</footer>
		</form>
	<?php
	}
	
	function saswp_step3(){
            
		global $saswp_installer_config;
		$stepDetails = $saswp_installer_config['steps'][$saswp_installer_config['current_step']['step_id']];
		?>

		<div class="merlin__content--transition">

			<div class="saswp_branding"></div>
			<svg class="icon icon--checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
				<circle class="icon--checkmark__circle" cx="26" cy="26" r="25" fill="none"/><path class="icon--checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
			</svg>
			<!--Escaping has been done above while adding to array ref array $saswp_installer_config-->
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
				<?php saswp_skip_button(); ?>
				
				<a id="skip" href="<?php echo esc_url( saswp_step_next_link() ); ?>" class="merlin__button merlin__button--skip merlin__button--proceed"><?php echo esc_html__( 'Skip', 'schema-and-structured-data-for-wp' ); ?></a>
				
				<a href="<?php echo esc_url( saswp_step_next_link() ); ?>" class="merlin__button merlin__button--next button-next" data-callback="save_logo">
					<span class="merlin__button--loading__text"><?php echo esc_html__( 'Next', 'schema-and-structured-data-for-wp' ); ?></span><?php echo saswp_loading_spinner(); ?>
				</a>
				
				
				<?php wp_nonce_field( 'saswp_install_nonce' ); ?>
			</footer>
		</form>
	<?php
	}
	
	function saswp_step4(){
            
		global $saswp_installer_config;
		$stepDetails = $saswp_installer_config['steps'][$saswp_installer_config['current_step']['step_id']];
		?>

		<div class="merlin__content--transition">

			<div class="saswp_branding"></div>
			<svg class="icon icon--checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
				<circle class="icon--checkmark__circle" cx="26" cy="26" r="25" fill="none"/><path class="icon--checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
			</svg>
			<!--Escaping has been done above while adding to array ref array $saswp_installer_config-->
			<h1><?php echo $stepDetails['title']; ?></h1>

                        <p><?php echo isset($stepDetails['description'])? $stepDetails['description'] : ''; ?></p>
		</div>
		<form action="" method="post">
			
			<ul class="merlin__drawer--import-content">
				<li>
				<?php 
					echo $stepDetails['fields'];
				?>
				</li>
			</ul>
			
			<footer class="merlin__content__footer">
				<?php saswp_skip_button(); ?>
				
				<a id="skip" href="<?php echo esc_url( saswp_step_next_link() ); ?>" class="merlin__button merlin__button--skip merlin__button--proceed"><?php echo esc_html__( 'Skip', 'schema-and-structured-data-for-wp' ); ?></a>
				
				<a href="<?php echo esc_url( saswp_step_next_link() ); ?>" class="merlin__button merlin__button--next button-next" data-callback="save_logo">
					<span class="merlin__button--loading__text"><?php echo esc_html__( 'Next', 'schema-and-structured-data-for-wp' ); ?></span><?php echo saswp_loading_spinner(); ?>
				</a>
				
				
				<?php wp_nonce_field( 'saswp_install_nonce' ); ?>
			</footer>
		</form>
	<?php
	}
	
	function saswp_step5(){
            
		global $saswp_installer_config;
		$stepDetails = $saswp_installer_config['steps'][$saswp_installer_config['current_step']['step_id']];
		?>

		<div class="merlin__content--transition">

			<div class="saswp_branding"></div>
			<svg class="icon icon--checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
				<circle class="icon--checkmark__circle" cx="26" cy="26" r="25" fill="none"/><path class="icon--checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
			</svg>
			<!--Escaping has been done above while adding to array ref array $saswp_installer_config-->
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
				<?php saswp_skip_button(); ?>
				
				<a id="skip" href="<?php echo esc_url( saswp_step_next_link() ); ?>" class="merlin__button merlin__button--skip merlin__button--proceed"><?php echo esc_html__( 'Skip', 'schema-and-structured-data-for-wp' ); ?></a>
				
				<a href="<?php echo esc_url( saswp_step_next_link() ); ?>" class="merlin__button merlin__button--next button-next" data-callback="save_logo">
					<span class="merlin__button--loading__text"><?php echo esc_html__( 'Next', 'schema-and-structured-data-for-wp' ); ?></span><?php echo saswp_loading_spinner(); ?>
				</a>
				
				<?php wp_nonce_field( 'saswp_install_nonce' ); ?>
			</footer>
		</form>
	<?php
	}
	
	function saswp_save_steps_data(){ 
            
                 if(! current_user_can( saswp_current_user_can() ) ) {
                    return ;
                 }
                 if ( ! isset( $_POST['wpnonce'] ) ){
                    return; 
                 }
                 if ( !wp_verify_nonce( $_POST['wpnonce'], 'saswp_install_nonce' ) ){
                    return;  
                 }                                 
                if(isset($_POST['sd_data'])){
                    
                $pre_sd_data                              = get_option('sd_data'); 
                $pre_sd_data['sd_initial_wizard_status']  = 1;                
                $sd_data                                  = array_map('sanitize_text_field', $_POST['sd_data']);
                
                if($pre_sd_data){
                    
			$sd_data = array_merge($pre_sd_data,$sd_data);
		}
                        update_option('sd_data',$sd_data);
                
                }
		
		if(isset($_POST['sd_data_create__post_schema']) && isset($_POST['sd_data_create__post_schema_checkbox'])){
                    
			$checkbox = array_filter($_POST['sd_data_create__post_schema_checkbox']);
			if(count($checkbox)>0){
                            
				foreach ($checkbox as $key => $value) {
                                    
					$postType   = sanitize_text_field($_POST['sd_data_create__post_schema'][$key]['posttype']);
					$schemaType = sanitize_text_field($_POST['sd_data_create__post_schema'][$key]['schema_type']);
					
					$postarr = array(
                                            'post_type'   => 'saswp',
                                            'post_title'  => ucfirst($postType),
                                            'post_status' => 'publish',
		                        );
                                        
					$insertedPageId = wp_insert_post(  $postarr );
                                        
					if($insertedPageId){
                                            
                                        $data_group_array = array();  
                                        
					$data_group_array['group-0'] =array(
                                            
                                            'data_array' => array(
                                                        array(
                                                        'key_1' => 'post_type',
                                                        'key_2' => 'equal',
                                                        'key_3' => $postType,
                                              )
                                            ) 
                                            
                                           );
                                        $data_group_array = saswp_sanitize_multi_array($data_group_array, 'data_array');
					$schema_options_array = array('isAccessibleForFree'=>False,'notAccessibleForFree'=>0,'paywall_class_name'=>'');
					update_post_meta( $insertedPageId, 'data_group_array', $data_group_array);
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
				'message' => esc_html__( 'Stored Successfully', 'schema-and-structured-data-for-wp' ),
			)
		);
	}
	
	
	function saswp_skip_button(){
		?>
		<a href="<?php echo esc_url(  saswp_step_next_link() ); ?>" class="merlin__button merlin__button--skip"><?php echo esc_html__( 'Skip', 'schema-and-structured-data-for-wp' ); ?></a>
		<?php
	}
	function saswp_finish_page() {
		global $saswp_installer_config;
		// Theme Name.
		$plugin_title 					= $saswp_installer_config['plugin_title'];
		// Strings passed in from the config file.
		$strings = null;

		
		$allowed_html_array = array(
			'a' => array(
				'href' 		=> array(),
				'title' 	=> array(),
				'target' 	=> array(),
			),
		);

		update_option( 'saswp_installer_completed', time() ); ?>

		<div class="merlin__content--transition">

			<div class="saswp_branding"></div>
			
			<h1><?php echo esc_html__( 'Setup Done. Have fun!', 'schema-and-structured-data-for-wp' ); ?></h1>

			<p><?php echo esc_html__(  'Basic Setup has been done. Navigate to plugin options panel to access all the options.','schema-and-structured-data-for-wp' ); ?></p>

		</div> 

		<footer class="merlin__content__footer merlin__content__footer--fullwidth">
			
			<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=saswp&page=structured_data_options' ) ); ?>" class="merlin__button merlin__button--blue merlin__button--fullwidth merlin__button--popin"><?php echo esc_html__( 'Let\'s Go' ,'schema-and-structured-data-for-wp'); ?></a>
						
			<ul class="merlin__drawer merlin__drawer--extras">

				<li><?php //echo wp_kses( $link_1, $allowed_html_array ); ?></li>
				<li><?php //echo wp_kses( $link_2, $allowed_html_array ); ?></li>
				<li><?php //echo wp_kses( $link_3, $allowed_html_array ); ?></li>

			</ul>

		</footer>

	<?php
	}
	
	
	function saswp_loading_spinner(){
		global $saswp_installer_config;
		$spinner = SASWP_DIR_NAME. $saswp_installer_config['installer_dir']. '/images/spinner.php';

		// Retrieve the spinner.
		get_template_part(  $spinner );
	}
	
	function saswp_svg_sprite() {
		global $saswp_installer_config;
		// Define SVG sprite file.
		$svg = SASWP_DIR_NAME. $saswp_installer_config['installer_dir'] . '/images/sprite.svg' ;

		// If it exists, include it.
		if ( file_exists( $svg ) ) {
			require_once apply_filters( 'merlin_svg_sprite', $svg );
		}
	}
	function saswp_step_next_link() {
		global $saswp_installer_config;
		$step = $saswp_installer_config['current_step']['step_id'] + 1;

		return add_query_arg( 'step', $step );
	}
	
	function saswp_install_header() {
            
                if( is_null ( get_current_screen() )) {
				set_current_screen('Merlin');
		}
                
		global $saswp_installer_config;
		
		// Get the current step.
		$current_step = strtolower( $saswp_installer_config['steps'][$saswp_installer_config['current_step']['step_id']]['title'] ); ?>

		<!DOCTYPE html>
		<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
		<head>
			<meta name="viewport" content="width=device-width"/>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
			<title><?php echo esc_attr(ucwords($current_step)); ?></title>
			<?php do_action( 'admin_print_styles' ); ?>
			<?php do_action( 'admin_print_scripts' ); ?>
			<?php do_action( 'admin_head' ); ?>
		</head>
		<body class="merlin__body merlin__body--<?php echo esc_attr( $current_step ); ?>">
		<?php
	}
	
	
	function saswp_install_footer() {
		?>	 
		</body>
		<?php do_action( 'admin_footer' ); ?>
		<?php do_action( 'admin_print_footer_scripts' ); ?>
		</html>
		<?php
	}
	
	function saswp_makesvg( $args = array() ){
		// Make sure $args are an array.
		if ( empty( $args ) ) {
			return __( 'Please define default parameters in the form of an array.', 'schema-and-structured-data-for-wp' );
		}

		// Define an icon.
		if ( false === array_key_exists( 'icon', $args ) ) {
			return __( 'Please define an SVG icon filename.', 'schema-and-structured-data-for-wp' );
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
			$svg .= '<title>' . esc_html__( $args['title'], 'schema-and-structured-data-for-wp' ) . '</title>';
		}

		// If there is a description, display it.
		if ( $args['desc'] ) {
			$svg .= '<desc>' . esc_html__( $args['desc'], 'schema-and-structured-data-for-wp' ) . '</desc>';
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
	function saswp_svg_allowed_html() {

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
	
	function saswp_step_output_bottom_dots(){
		global $saswp_installer_config;
		?>
		<ol class="dots">

			<?php for( $i = 1; $i<$saswp_installer_config['total_steps']; $i++ ) :

				$class_attr = '';
				$show_link = false;

				if ( $i === $saswp_installer_config['current_step']['step_id'] ) {
					$class_attr = 'active';
				} elseif ( $saswp_installer_config['current_step']['step_id'] >  $i) {
					$class_attr = 'done';
					$show_link = true;
				} ?>

				<li class="<?php echo esc_attr( $class_attr ); ?>">
					<a href="<?php echo esc_url( add_query_arg( 'step', $i ) ); ?>" title="<?php echo esc_attr( $saswp_installer_config['current_step']['title'] ); ?>"></a>
				</li>

			<?php endfor; ?>

		</ol>
		<?php
	}


function saswp_general_setting_fields_callback(){
                                
	global $sd_data;
        global $wp_query;
        $saswp_kb_type ='';
        
        if(isset($sd_data['saswp_kb_type'])){
          $saswp_kb_type =  $sd_data['saswp_kb_type']; 
        }
        $about_page   =   '';
        $contact_page =   '';
        $pages        = null;
        
        if($wp_query){
            
            $pages = get_posts( array(
                'order'       => 'ASC',
                'orderby'     => 'ID',
                'post_type'   => 'page',
                'post_status' => 'publish',
        ) );
            
        }
        
        if(!empty($pages)){
            
            foreach ($pages as $page){
              
               $about_page .= '<option value="'.esc_attr($page->ID).'">'.esc_attr($page->post_title).'</option>'; 
               $contact_page .= '<option value="'.esc_attr($page->ID).'">'.esc_attr($page->post_title).'</option>'; 
                
            }
            
        }
        if($about_page){
            
            $about_page   = '<li class="saswp_fields">
			    <label>'.esc_html__('About', 'schema-and-structured-data-for-wp').'</label>	                                                            
                            <select name="sd_data[sd_about_page]" id="sd_about_page-select">
                            <option value="">'.esc_html__( 'Select an item', 'schema-and-structured-data-for-wp' ).'</option>    
                            '.$about_page.'
                            </select>                            	                                
                          </li>';            
        }
        
        if($contact_page){
            
            $contact_page   = '<li class="saswp_fields">
			    <label>'.esc_html__('Contact', 'schema-and-structured-data-for-wp').'</label>	                                                            
                            <select name="sd_data[sd_contact_page]" id="sd_contact_page-select">
                            <option value="">'.esc_html__( 'Select an item', 'schema-and-structured-data-for-wp' ).'</option>
                            '.$contact_page.'
                            </select>                            	                                
                          </li>';            
        }                     
	$returnHtml = '<li class="saswp_fields">
			<label>'.esc_html__('Data Type', 'schema-and-structured-data-for-wp').'</label>
			<select name="sd_data[saswp_kb_type]">
				<option value="Organization" '.($saswp_kb_type=='Organization'? 'selected' : '').'>'.esc_html__('Organization', 'schema-and-structured-data-for-wp').'</option>
				<option value="Person" '.($saswp_kb_type=='Person'? 'selected' : '').'>'.esc_html__('Person', 'schema-and-structured-data-for-wp').'</option>
			</select>
		</li>
                '.$about_page.'
                '.$contact_page.'';				
		return $returnHtml;
}

function saswp_social_profile_fields_callback(){
    
	global $sd_data;
        
	$settings       = $sd_data;
        $sd_facebook    = saswp_remove_warnings($settings, 'sd_facebook', 'saswp_string');
        $sd_twitter     = saswp_remove_warnings($settings, 'sd_twitter', 'saswp_string');
        $sd_linkedin    = saswp_remove_warnings($settings, 'sd_linkedin', 'saswp_string');
        $sd_instagram   = saswp_remove_warnings($settings, 'sd_instagram', 'saswp_string');
        
        
	$returnHtml = '
		<li class="merlin__drawer--import-content__list-item status saswp-social-fields">
			<input type="checkbox" name="sd_data[saswp-facebook-enable]" id="saswp-facebook-enable" class="checkbox" value="1" '.($sd_facebook!=''? 'checked': '').'>
			<label for="saswp-facebook-enable"><i></i><span>'.esc_html__('Facebook', 'schema-and-structured-data-for-wp').'</span></label>
			<input type="text"  name="sd_data[sd_facebook]" value="'.esc_url($sd_facebook).'" placeholder="'.esc_html__('Enter Facebook Page UR', 'schema-and-structured-data-for-wp').'">
		</li>
		<li class="merlin__drawer--import-content__list-item status saswp-social-fields">
			<input type="checkbox" name="sd_data[saswp-twitter-enable]" id="saswp-twitter-enable" class="checkbox" value="1" '.($sd_twitter!=''? 'checked': '').'>
			<label for="saswp-twitter-enable"><i></i><span>'.esc_html__('Twitter', 'schema-and-structured-data-for-wp').'</span></label>
			<input type="text" name="sd_data[sd_twitter]" value="'.esc_url($sd_twitter).'" placeholder="'.esc_html__('Enter Twitter Page UR', 'schema-and-structured-data-for-wp').'">
		</li>
		<li class="merlin__drawer--import-content__list-item status saswp-social-fields">
			<input type="checkbox" name="sd_data[saswp-linkedin-enable]" id="saswp-linkedin-enable" class="checkbox" value="1" '.($sd_linkedin!=''? 'checked': '').'>
			<label for="saswp-linkedin-enable"><i></i><span>'.esc_html__('Linkedin', 'schema-and-structured-data-for-wp').'</span></label>
			<input type="text" name="sd_data[sd_linkedin]" value="'.esc_url($sd_linkedin).'" placeholder="'.esc_html__('Enter Linkedin Page UR', 'schema-and-structured-data-for-wp').'">
		</li>
		<li class="merlin__drawer--import-content__list-item status saswp-social-fields">
			<input type="checkbox" name="sd_data[saswp-instagram-enable]" id="saswp-instagram-enable" class="checkbox" value="1" '.($sd_instagram!=''? 'checked': '').'>
			<label for="saswp-instagram-enable"><i></i><span>'.esc_html__('Instagram', 'schema-and-structured-data-for-wp').'</span></label>
			<input type="text" name="sd_data[sd_instagram]" value="'.esc_url($sd_instagram).'" placeholder="'.esc_html__('Enter Linkedin Page UR', 'schema-and-structured-data-for-wp').'">
		</li>';
		return $returnHtml;
}

function saswp_select_schema_fields_callback(){
    
	global $sd_data;
	$returnHtml = $post_types = '';
        $post_types = get_post_types( array( 'public' => true ), 'names' );
    // Remove Unsupported Post types
        unset($post_types['attachment'], $post_types['amp_acf'], $post_types['saswp']);
        $option = '';
        
        if(count($post_types)>0){
            
    	foreach ($post_types as $key => $value) {
            
    		$returnHtml .= '<li class="merlin__drawer--import-content__list-item status post-type-fields">
    					<input type="checkbox" name="sd_data_create__post_schema_checkbox['.esc_attr($key).']" id="sd_data_create__post_schema_'.esc_attr($key).'" class="checkbox" value="1" >
    					<label for="sd_data_create__post_schema_'.esc_attr($key).'"><i></i><span>'.esc_html(ucfirst($value)).'</span></label>
    					<input type="hidden" name="sd_data_create__post_schema['.esc_attr($key).'][posttype]" class="checkbox" value="'.esc_attr($key).'" >

    					<select id="schema_type" name="sd_data_create__post_schema['.$key.'][schema_type]">
                			<option value="">'.esc_html__('Select Schema Type', 'schema-and-structured-data-for-wp').'</option>
                			<option value="BlogPosting">'.esc_html__('BlogPosting', 'schema-and-structured-data-for-wp').'</option>
                			<option value="NewsArticle">'.esc_html__('NewsArticl', 'schema-and-structured-data-for-wp').'e</option>
                			<option value="WebPage">'.esc_html__('WebPage', 'schema-and-structured-data-for-wp').'</option>
                			<option value="Article">'.esc_html__('Article', 'schema-and-structured-data-for-wp').'</option>
                			<option value="Recipe">'.esc_html__('Recipe', 'schema-and-structured-data-for-wp').'</option>
                			<option value="Product">'.esc_html__('Product', 'schema-and-structured-data-for-wp').'</option>
                			<option value="VideoObject">'.esc_html__('VideoObject', 'schema-and-structured-data-for-wp').'</option>
                		</select>
    				</li>';
    	}
    }

    return $returnHtml;
}
?>