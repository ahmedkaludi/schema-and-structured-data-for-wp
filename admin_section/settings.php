<?php
/**
 * Settings Page
 *
 * @author   Magazine3
 * @category Admin
 * @path     admin_section/settings
 * @version 1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Admin Settings
 * Function saswp_add_menu_links
 *
 */
add_action( 'plugin_action_links_' . plugin_basename( SASWP_DIR_NAME_FILE ), 'saswp_plugin_action_links' );

function saswp_plugin_action_links( $links ) {
        
        $nonce = wp_create_nonce( 'saswp_install_wizard_nonce' );  

        if(is_array($links)){

                $links[] = '<a href="' . esc_url( admin_url( 'edit.php?post_type=saswp&page=structured_data_options' ) ) . '">' . esc_html__( 'Settings', 'schema-and-structured-data-for-wp' ) . '</a>';
                $links[] = '<a href="'.  esc_url( admin_url( 'plugins.php?page=saswp-setup-wizard' ).'&_saswp_nonce='.$nonce).'">' . esc_html__( 'Setup Wizard', 'schema-and-structured-data-for-wp' ) . '</a>';
                $links[] = '<a target="_blank" href="http://structured-data-for-wp.com/docs/">' . esc_html__( 'Documentation', 'schema-and-structured-data-for-wp' ) . '</a>';

        }	

  	return $links;        
}

function saswp_ext_installed_status(){
        
            $mappings_file = SASWP_DIR_NAME . '/core/array-list/pro_extensions.php';
            
            $pro_ext = array();
            
            if ( file_exists( $mappings_file ) ) {
                $pro_ext = include $mappings_file;
            }
            
            $check_active_ext = false;
            
            if(!empty($pro_ext)){
                
                foreach($pro_ext as $ext){
                    
                    if(is_plugin_active($ext['path'])){
                        
                        $check_active_ext = true;                        
                         break;
                    }
                                        
                }
                
            }
            
            return $check_active_ext;
    
}

function saswp_add_menu_links() {	
                       
	    add_submenu_page( 'edit.php?post_type=saswp',
                    esc_html__( 'Schema & Structured Data For Wp', 'schema-and-structured-data-for-wp' ),
                    esc_html__( 'Settings', 'schema-and-structured-data-for-wp' ), 
                    saswp_current_user_can(),
                    'structured_data_options', 
                    'saswp_admin_interface_render'
                    );	
                                
            if(!saswp_ext_installed_status()){
                add_submenu_page( 'edit.php?post_type=saswp', esc_html__( 'Schema & Structured Data For Wp', 'schema-and-structured-data-for-wp' ), '<span class="saswp-upgrade-to-pro" style="color:#fff176;">'.esc_html__( 'Upgrade To Premium', 'schema-and-structured-data-for-wp' ).'</span>', 'manage_options', 'structured_data_premium', 'saswp_premium_interface_render' );	
            }
                                                            
}
add_action( 'admin_menu', 'saswp_add_menu_links' );

function saswp_premium_interface_render(){
    
    wp_redirect( 'https://structured-data-for-wp.com/pricing/' );
    exit;    
        
}
function saswp_admin_interface_render(){
	            
        if ( ! current_user_can( saswp_current_user_can() ) ) {
		return;
        }
    	
	// Handing save settings
	if ( isset( $_GET['settings-updated'] ) ) {							                                                 
		settings_errors();               
	}
            $is_amp = false;
        if ( is_plugin_active('accelerated-mobile-pages/accelerated-moblie-pages.php') || is_plugin_active('amp/amp.php') ) {
            $is_amp = true;			
        }   
       
        $tab = saswp_get_tab('general', array('general', 'amp','review','compatibility','email_schema', 'tools', 'tools','premium_features', 'services', 'support'));            
	
	?>
<div class="saswp-settings-container">
	<div class="wrap saswp-settings-form saswp-settings-first-div">	
		<h1 class="wp-heading-inline"> <?php echo esc_html__( 'Schema & Structured Data For WP', 'schema-and-structured-data-for-wp' ); ?> <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=saswp' ) ); ?>" class="page-title-action"><?php echo esc_html__( 'Schema Types', 'schema-and-structured-data-for-wp' ); ?></a></h1><br>		
                <div>
		<h2 class="nav-tab-wrapper saswp-tabs">
                    
			<?php			

			echo '<a href="' . esc_url(saswp_admin_link('general')) . '" class="nav-tab ' . esc_attr( $tab == 'general' ? 'nav-tab-active' : '') . '"><span class=""></span> ' . esc_html__('Global','schema-and-structured-data-for-wp') . '</a>';
			                                               
                        echo '<a href="' . esc_url(saswp_admin_link('amp')) . '" class="nav-tab ' . esc_attr( $tab == 'amp' ? 'nav-tab-active' : '') . '"><span class=""></span> ' . esc_html__('AMP','schema-and-structured-data-for-wp') . '</a>';    
                                                                                                                                                                                                                                              
                        echo '<a href="' . esc_url(saswp_admin_link('review')) . '" class="nav-tab ' . esc_attr( $tab == 'review' ? 'nav-tab-active' : '') . '"><span class=""></span> ' . esc_html__('Review','schema-and-structured-data-for-wp') . '</a>';
                        
                        echo '<a href="' . esc_url(saswp_admin_link('compatibility')) . '" class="nav-tab ' . esc_attr( $tab == 'compatibility' ? 'nav-tab-active' : '') . '"><span class=""></span> ' . esc_html__('Compatibility','schema-and-structured-data-for-wp') . '</a>';
                        
                        echo '<a href="' . esc_url(saswp_admin_link('email_schema')) . '" class="nav-tab ' . esc_attr( $tab == 'email_schema' ? 'nav-tab-active' : '') . '"><span class=""></span> ' . esc_html__('Email Schema','schema-and-structured-data-for-wp') . '</a>';
                        
                        echo '<a href="' . esc_url(saswp_admin_link('tools')) . '" class="nav-tab ' . esc_attr( $tab == 'tools' ? 'nav-tab-active' : '') . '"><span class=""></span> ' . esc_html__('Advanced','schema-and-structured-data-for-wp') . '</a>';                         			
                        
                        echo '<a href="' . esc_url(saswp_admin_link('premium_features')) . '" class="nav-tab ' . esc_attr( $tab == 'premium_features' ? 'nav-tab-active' : '') . '"><span class=""></span> ' . esc_html__('Premium Features','schema-and-structured-data-for-wp') . '</a>';                         			
                        
                        echo '<a href="' . esc_url(saswp_admin_link('services')) . '" class="nav-tab ' . esc_attr( $tab == 'services' ? 'nav-tab-active' : '') . '"><span class=""></span> ' . esc_html__('Services','schema-and-structured-data-for-wp') . '</a>';                         			
                        
                        echo '<a href="' . esc_url(saswp_admin_link('support')) . '" class="nav-tab ' . esc_attr( $tab == 'support' ? 'nav-tab-active' : '') . '"><span class=""></span> ' . esc_html__('Support','schema-and-structured-data-for-wp') . '</a>';
			?>
                    
		</h2>
                 
                                           
                </div>
                
                <form action="<?php echo admin_url("options.php") ?>" method="post" enctype="multipart/form-data" class="saswp-settings-form">		
			<div class="form-wrap saswp-settings-form-wrap">
			<?php
			// Output nonce, action, and option_page fields for a settings page.
			settings_fields( 'sd_data_group' );												
			echo "<div class='saswp-general' ".( $tab != 'general' ? 'style="display:none;"' : '').">";
                        
                        echo '<div id="saswp-global-tabs" style="margin-top: 10px;">';
                        
                        echo '<a data-id="saswp-general-container">'.esc_html__('General Settings','schema-and-structured-data-for-wp').'</a> | <a data-id="saswp-knowledge-container">'.esc_html__('Knowledge Graph','schema-and-structured-data-for-wp').'</a> | <a data-id="saswp-default-container" >'.esc_html__('Default Data','schema-and-structured-data-for-wp').'</a>';
                        
                        echo'</div> ';
                        
				// general Application Settings                        
				do_settings_sections( 'saswp_general_section' );	// Page slug
			echo "</div>";
						                                                
                        echo "<div class='saswp-amp' ".( $tab != 'amp' ? 'style="display:none;"' : '').">";
                        
				do_settings_sections( 'saswp_amp_section' );	// Page slug
			echo "</div>";
                                                                        
                        echo "<div class='saswp-review' ".( $tab != 'review' ? 'style="display:none;"' : '').">";
                        
                            echo '<div id="saswp-review-tabs" style="margin-top: 10px;">';

                            echo '<a data-id="saswp-review-reviews-container">'.esc_html__('Reviews Module','schema-and-structured-data-for-wp').'</a> | <a data-id="saswp-review-rating-container">'.esc_html__('Rating Module','schema-and-structured-data-for-wp').'</a>';

                            echo'</div> ';
                        
			     // Status                        
			        do_settings_sections( 'saswp_review_section' );	// Page slug
			echo "</div>";
                        
                        echo "<div class='saswp-compatibility' ".( $tab != 'compatibility' ? 'style="display:none;"' : '').">";
			     // Status
                        
                                echo '<div id="saswp-compatibility-tabs" style="margin-top: 10px;">';

                                echo '<a data-id="saswp-active-compatibility-container">'.esc_html__('Active','schema-and-structured-data-for-wp').'</a> | <a data-id="saswp-inactive-compatibility-container">'.esc_html__('InActive','schema-and-structured-data-for-wp').'</a>';

                                echo'</div> ';
                        
			        do_settings_sections( 'saswp_compatibility_section' );	// Page slug
			echo "</div>";
                        
                        echo "<div class='saswp-email_schema' ".( $tab != 'email_schema' ? 'style="display:none;"' : '').">";
			     // Status                        
			        do_settings_sections( 'saswp_email_schema_section' );	// Page slug
			echo "</div>";
                        
                        echo "<div class='saswp-tools' ".( $tab != 'tools' ? 'style="display:none;"' : '').">";
                        
                            echo '<div id="saswp-tools-tabs" style="margin-top: 10px;">';

                            echo '<a data-id="saswp-tools-advanced-container">'.esc_html__('Advanced','schema-and-structured-data-for-wp').'</a> | <a data-id="saswp-tools-translation-container">'.esc_html__('Translation Panel','schema-and-structured-data-for-wp').'</a>';

                            echo'</div> ';
			     // Status
                        
			        do_settings_sections( 'saswp_tools_section' );	// Page slug
			echo "</div>";
                        
                        echo "<div class='saswp-premium_features' ".( $tab != 'premium_features' ? 'style="display:none;"' : '').">";
			     // Status                        
			        do_settings_sections( 'saswp_premium_features_section' );	// Page slug
			echo "</div>";
                        
                        echo "<div class='saswp-services' ".( $tab != 'services' ? 'style="display:none;"' : '').">";
			     // Status                        
			        do_settings_sections( 'saswp_services_section' );	// Page slug
			echo "</div>";
                        
                        echo "<div class='saswp-support' ".( $tab != 'support' ? 'style="display:none;"' : '').">";
			     // Status                        
			        do_settings_sections( 'saswp_support_section' );	// Page slug
			echo "</div>";

			?>
		</div>
			<div class="button-wrapper">
				<?php
				// Output save settings button
                                submit_button( esc_html__('Save Settings', 'schema-and-structured-data-for-wp') );
				?>
			</div>  
                    <input type="hidden" name="sd_data[sd_initial_wizard_status]" value="1">
		</form>
	</div>
    <div class="saswp-settings-second-div">
<!--        <a target="_blank" href="http://structured-data-for-wp.com/pricing/">
        <div class="saswp-upgrade-pro promo-cpn">
            <h2><?php echo esc_html__('50% OFF on PRO','schema-and-structured-data-for-wp') ?></h2>
            <span><?php echo esc_html__('Limited Time Offer for this festive season','schema-and-structured-data-for-wp') ?></span>
            <div class="saswp-promo">
                <span class="prm-txt"><?php echo esc_html__('Promo:','schema-and-structured-data-for-wp') ?></span>
                <span class="prm-cpn"><?php echo esc_html__('BFCM2019','schema-and-structured-data-for-wp') ?></span>
            </div>
        </div>
        </a>-->
        <?php if(!saswp_ext_installed_status()) { ?>
            <div class="saswp-upgrade-pro">
                <h2><?php echo esc_html__('Upgrade to Pro!','schema-and-structured-data-for-wp') ?></h2>
                <ul>
                    <li><?php echo esc_html__('Premium features','schema-and-structured-data-for-wp') ?></li>
                    <li><?php echo esc_html__('Dedicated Schema Support','schema-and-structured-data-for-wp') ?></li>
                    <li><?php echo esc_html__('Active Development','schema-and-structured-data-for-wp') ?></li>
                </ul>
                <a target="_blank" href="http://structured-data-for-wp.com/pricing/"><?php echo esc_html__('UPGRADE','schema-and-structured-data-for-wp') ?></a>
            </div>
        <?php  } ?>        
    </div>
</div>

	<?php
}
/*
	WP Settings API
*/
add_action('admin_init', 'saswp_settings_init');

function saswp_settings_init(){
    
          	register_setting( 'sd_data_group', 'sd_data', 'saswp_handle_file_upload' );
                add_settings_section('saswp_general_section', __return_false(), '__return_false', 'saswp_general_section');

                add_settings_field(
			'general_settings',								// ID
			'',		// Title
			'saswp_general_page_callback',								// CB
			'saswp_general_section',						// Page slug
			'saswp_general_section'						// Settings Section ID
		);
                                                                 
                add_settings_section('saswp_amp_section', __return_false(), '__return_false', 'saswp_amp_section');
	
		add_settings_field(
			'saswp_amp_settings',								// ID
			'',		// Title
			'saswp_amp_page_callback',								// CB
			'saswp_amp_section',						// Page slug
			'saswp_amp_section'						// Settings Section ID
		); 
                
                
                add_settings_section('saswp_review_section', __return_false(), '__return_false', 'saswp_review_section');

                add_settings_field(
			'saswp_review_settings',								// ID
			'',		// Title
			'saswp_review_page_callback',								// CB
			'saswp_review_section',						// Page slug
			'saswp_review_section'						// Settings Section ID
		);
                
                add_settings_section('saswp_compatibility_section', __return_false(), '__return_false', 'saswp_compatibility_section');

                add_settings_field(
			'saswp_compatibility_settings',								// ID
			'',		// Title
			'saswp_compatibility_page_callback',								// CB
			'saswp_compatibility_section',						// Page slug
			'saswp_compatibility_section'						// Settings Section ID
		);
                
                add_settings_section('saswp_email_schema_section', __return_false(), '__return_false', 'saswp_email_schema_section');

                add_settings_field(
			'saswp_email_schema_settings',								// ID
			'',		// Title
			'saswp_email_schema_callback',								// CB
			'saswp_email_schema_section',						// Page slug
			'saswp_email_schema_section'						// Settings Section ID
		);
                
                
                add_settings_section('saswp_support_section', __return_false(), '__return_false', 'saswp_support_section');

                add_settings_field(
			'saswp_support_settings',								// ID
			'',		// Title
			'saswp_support_page_callback',								// CB
			'saswp_support_section',						// Page slug
			'saswp_support_section'						// Settings Section ID
		);
                
                
                add_settings_section('saswp_tools_section', __return_false(), '__return_false', 'saswp_tools_section');
                
                // the meta_key 'diplay_on_homepage' with the meta_value 'true'                    
                    add_settings_field(
                            'saswp_import_status',								// ID
                            '',			// Title
                            'saswp_import_callback',					// Callback
                            'saswp_tools_section',							// Page slug
                            'saswp_tools_section'							// Settings Section ID
                    );
                    
                    add_settings_section('saswp_premium_features_section', __return_false(), '__return_false', 'saswp_premium_features_section');
                
                // the meta_key 'diplay_on_homepage' with the meta_value 'true'                    
                    add_settings_field(
                            'saswp_premium_features_settings',								// ID
                            '',			// Title
                            'saswp_premium_features_callback',					// Callback
                            'saswp_premium_features_section',							// Page slug
                            'saswp_premium_features_section'							// Settings Section ID
                    );
                    
                    add_settings_section('saswp_services_section', __return_false(), '__return_false', 'saswp_services_section');
                
                // the meta_key 'diplay_on_homepage' with the meta_value 'true'                    
                    add_settings_field(
                            'saswp_services_settings',								// ID
                            '',			// Title
                            'saswp_services_callback',					// Callback
                            'saswp_services_section',							// Page slug
                            'saswp_services_section'							// Settings Section ID
                    );
                                                     
                 
}

function saswp_custom_upload_mimes($mimes = array()) {
	
	$mimes['json'] = "application/json";

	return $mimes;
}

add_action('upload_mimes', 'saswp_custom_upload_mimes');

function saswp_handle_file_upload($option){
    
    if ( ! current_user_can( saswp_current_user_can() ) ) {
		return $option;
    }

   if(isset($_FILES['saswp_import_backup'])){
     
       $fileInfo = wp_check_filetype(basename($_FILES['saswp_import_backup']['name']));
    
        if (!empty($fileInfo['ext']) && $fileInfo['ext'] == 'json') {

            if(!empty($_FILES["saswp_import_backup"]["tmp_name"])){

              $urls = wp_handle_upload($_FILES["saswp_import_backup"], array('test_form' => FALSE));    
              $url = $urls["url"];
              update_option('saswp-file-upload_url',esc_url($url));

           }
        }
       
   }  
   
  return $option;
  
}


function saswp_premium_features_callback(){ ?>
	<div class="saswp-pre-ftrs-wrap">
		<ul class="saswp-features-blocks">
                    
                <li>
                            
                            <?php
                            
                            $cooked_active_text = '';
                            
                            if(is_plugin_active('reviews-for-schema/reviews-for-schema.php')){                                        
                                $cooked_active_text = '<label class="saswp-sts-txt">Status :<span style="color:green;">Active</span></label>';                                            
                            }else{
                                $cooked_active_text .='<label class="saswp-sts-txt">Status :<span>Inactive</span></label>';
                                $cooked_active_text .='<a target="_blank" href="http://structured-data-for-wp.com/extensions/"><span class="saswp-d-btn">Download</span></a>';
                            }
                            
                            ?> 
                                            
                    <div class="saswp-features-ele">
                        <div class="saswp-ele-ic" style="background: #509207;">
                                <img src="<?php echo SASWP_PLUGIN_URL; ?>/admin_section/images/customer-review.png">
                            </div>
                            <div class="saswp-ele-tlt">
                                    <h3><?php echo esc_html__('Reviews for Schema','schema-and-structured-data-for-wp') ?></h3>
                                    <p><?php echo esc_html__('Fetch reviews from 75+ platforms with a single click with proper structured data so you can get the stars in your search engine rankings. It also works for the AMP.','schema-and-structured-data-for-wp') ?></p>
                            </div>
                    </div>
                    <div class="saswp-sts-btn">
                        
                        <?php echo $cooked_active_text; ?>
                                                                                                                                               
                    </div>
            </li>
                        <li>
                            
                                        <?php
                                        
                                        $cooked_active_text = '';
                                        
                                        if(is_plugin_active('recipe-schema-for-saswp/recipe-schema-for-saswp.php')){                                        
                                            $cooked_active_text = '<label class="saswp-sts-txt">Status :<span style="color:green;">Active</span></label>';                                            
                                        }else{
                                            $cooked_active_text .='<label class="saswp-sts-txt">Status :<span>Inactive</span></label>';
                                            $cooked_active_text .='<a target="_blank" href="http://structured-data-for-wp.com/extensions/"><span class="saswp-d-btn">Download</span></a>';
                                        }
                                        
                                        ?> 
                                                        
				<div class="saswp-features-ele">
                                    <div class="saswp-ele-ic" style="background: #509207;">
                                            <img src="<?php echo SASWP_PLUGIN_URL; ?>/admin_section/images/recipe.png">
					</div>
					<div class="saswp-ele-tlt">
						<h3><?php echo esc_html__('Recipe Schema','schema-and-structured-data-for-wp') ?></h3>
						<p><?php echo esc_html__('Recipe Schema extension is the number one solution to enhance your recipe website with the right structured data.','schema-and-structured-data-for-wp') ?></p>
					</div>
				</div>
				<div class="saswp-sts-btn">
                                    
                                    <?php echo $cooked_active_text; ?>
                                                                           										
				</div>
			</li>
                    
                        <li>
                            
                                        <?php
                                        
                                        $cooked_active_text = '';
                                        
                                        if(is_plugin_active('event-schema-for-saswp/event-schema-for-saswp.php')){                                        
                                            $cooked_active_text = '<label class="saswp-sts-txt">Status :<span style="color:green;">Active</span></label>';                                            
                                        }else{
                                            $cooked_active_text .='<label class="saswp-sts-txt">Status :<span>Inactive</span></label>';
                                            $cooked_active_text .='<a target="_blank" href="http://structured-data-for-wp.com/extensions/"><span class="saswp-d-btn">Download</span></a>';
                                        }
                                        
                                        ?> 
                                                        
				<div class="saswp-features-ele">
                                    <div class="saswp-ele-ic" style="background: #eae4ca;">
                                            <img src="<?php echo SASWP_PLUGIN_URL; ?>/admin_section/images/event.png">
					</div>
					<div class="saswp-ele-tlt">
						<h3><?php echo esc_html__('Event Schema','schema-and-structured-data-for-wp') ?></h3>
						<p><?php echo esc_html__('Event Schema extension is the number one solution to enhance your event website with the right structured data.','schema-and-structured-data-for-wp') ?></p>
					</div>
				</div>
				<div class="saswp-sts-btn">
                                    
                                    <?php echo $cooked_active_text; ?>
                                                                           										
				</div>
			</li>
                        <li>
                            
                                        <?php
                                        
                                        $cooked_active_text = '';
                                        
                                        if(is_plugin_active('course-schema-for-saswp/course-schema-for-saswp.php')){                                        
                                            $cooked_active_text = '<label class="saswp-sts-txt">Status :<span style="color:green;">Active</span></label>';                                            
                                        }else{
                                            $cooked_active_text .='<label class="saswp-sts-txt">Status :<span>Inactive</span></label>';
                                            $cooked_active_text .='<a target="_blank" href="http://structured-data-for-wp.com/extensions/"><span class="saswp-d-btn">Download</span></a>';
                                        }
                                        
                                        ?> 
                                                        
				<div class="saswp-features-ele">
                                    <div class="saswp-ele-ic" style="background: #dcb71d;">
                                            <img src="<?php echo SASWP_PLUGIN_URL; ?>/admin_section/images/course.png">
					</div>
					<div class="saswp-ele-tlt">
						<h3><?php echo esc_html__('Course Schema','schema-and-structured-data-for-wp') ?></h3>
						<p><?php echo esc_html__('Course Schema extension is the number one solution to enhance your course offering website with the right structured data.','schema-and-structured-data-for-wp') ?></p>
					</div>
				</div>
				<div class="saswp-sts-btn">
                                    
                                    <?php echo $cooked_active_text; ?>
                                                                           										
				</div>
			</li>
			<li>
                             <?php
                                        $woocommerce_active_text = '';
                                        if(is_plugin_active('woocommerce-compatibility-for-schema/woocommerce-compatibility-for-schema.php')){                                           
                                          $woocommerce_active_text = '<label class="saswp-sts-txt">Status :<span style="color:green">Active</span></label>';                                          ;
                                        }else{                                            
                                           $woocommerce_active_text .= '<label class="saswp-sts-txt">Status :<span>Inactive</span></label>'; 
                                           $woocommerce_active_text .= '<a target="_blank" href="http://structured-data-for-wp.com/extensions/woocommerce-compatibility-for-schema/"><span class="saswp-d-btn">Download</span></a>';
                                        }
                                        
                                        ?>                                                        
				<div class="saswp-features-ele">
					<div class="saswp-ele-ic saswp-ele-1">
                                            <img src="<?php echo SASWP_PLUGIN_URL; ?>/admin_section/images/woocommerce-icon.png">
					</div>
					<div class="saswp-ele-tlt">
						<h3><?php echo esc_html__('WooCommerce Compatibility for Schema','schema-and-structured-data-for-wp') ?></h3>
						<p><?php echo esc_html__('WooCommerce Compatibility extension is the number one solution to enhance your store with the right structured data.','schema-and-structured-data-for-wp') ?></p>
					</div>
				</div>
				<div class="saswp-sts-btn">
                                    
                                    <?php echo $woocommerce_active_text; ?>
                                                                           										
				</div>
			</li>			                          
                        <li>
                            
                                        <?php
                                        
                                        $cooked_active_text = '';
                                        
                                        if(is_plugin_active('real-estate-schema/real-estate-schema.php')){                                        
                                            $cooked_active_text = '<label class="saswp-sts-txt">Status :<span style="color:green;">Active</span></label>';                                            
                                        }else{
                                            $cooked_active_text .='<label class="saswp-sts-txt">Status :<span>Inactive</span></label>';
                                            $cooked_active_text .='<a target="_blank" href="http://structured-data-for-wp.com/extensions/real-estate-schema/"><span class="saswp-d-btn">Download</span></a>';
                                        }
                                        
                                        ?> 
                                                        
				<div class="saswp-features-ele">
                                    <div class="saswp-ele-ic" style="background: #ace;">
                                            <img src="<?php echo SASWP_PLUGIN_URL; ?>/admin_section/images/real-estate-schema-wp.png">
					</div>
					<div class="saswp-ele-tlt">
						<h3><?php echo esc_html__('Real Estate Schema','schema-and-structured-data-for-wp') ?></h3>
						<p><?php echo esc_html__('Real Estate Schema extension is the number one solution to enhance your real estate website with the right structured data.','schema-and-structured-data-for-wp') ?></p>
					</div>
				</div>
				<div class="saswp-sts-btn">
                                    
                                    <?php echo $cooked_active_text; ?>
                                                                           										
				</div>
			</li>                        
                        
		</ul>
	</div>

 <?php
}

function saswp_services_callback(){ ?>
   <div class="saswp-pre-ftrs-wrap">
		<ul class="saswp-features-blocks">
                        <li>
				<div class="saswp-features-ele">
					<div class="saswp-ele-ic saswp-ele-4" style="background: #69e781;">
                                            <img src="<?php echo SASWP_PLUGIN_URL; ?>/admin_section/images/support-1.png">
					</div>
					<div class="saswp-ele-tlt">
						<h3><?php echo esc_html__('Priority Support','schema-and-structured-data-for-wp') ?></h3>
						<p><?php echo esc_html__('We get more than 100 technical queries a day but the Priority support plan will help you skip that and get the help from a dedicated team.','schema-and-structured-data-for-wp') ?></p>
					</div>
				</div>
                                <a target="_blank" href="https://structured-data-for-wp.com/priority-support//">
                                    <div class="saswp-sts-btn">					
					<span class="saswp-d-btn"><?php echo esc_html__('Try it','schema-and-structured-data-for-wp') ?></span>
				    </div>
                                </a>
				
			</li>
			<li>
				<div class="saswp-features-ele">
					<div class="saswp-ele-ic saswp-ele-3">
                                            <img src="<?php echo SASWP_PLUGIN_URL; ?>/admin_section/images/news.png">
					</div>
					<div class="saswp-ele-tlt">
						<h3><?php echo esc_html__('Google News Schema Setup','schema-and-structured-data-for-wp') ?></h3>
						<p><?php echo esc_html__('Get quick approval to Google News with our service. Our structured data experts will set up the Google News schema properly on your website.','schema-and-structured-data-for-wp') ?></p>
					</div>
				</div>
                            <a target="_blank" href="http://structured-data-for-wp.com/services/google-news-schema-setup/">
                                <div class="saswp-sts-btn">					
					<span class="saswp-d-btn"><?php echo esc_html__('Try it','schema-and-structured-data-for-wp') ?></span>
				</div>
                            </a>
				
			</li>
			<li>
				<div class="saswp-features-ele">
					<div class="saswp-ele-ic saswp-ele-4">
                                            <img src="<?php echo SASWP_PLUGIN_URL; ?>/admin_section/images/schema-setup-icon.png">
					</div>
					<div class="saswp-ele-tlt">
						<h3><?php echo esc_html__('Structured Data Setup & Error Clean Up','schema-and-structured-data-for-wp') ?></h3>
						<p><?php echo esc_html__('We will help you setup Schema and Structured data on your website as per your requirements and as per recommendation by our expert developers.','schema-and-structured-data-for-wp') ?></p>
					</div>
				</div>
                                <a target="_blank" href="http://structured-data-for-wp.com/services/structured-data-setup-error-clean-up/">
                                    <div class="saswp-sts-btn">					
					<span class="saswp-d-btn"><?php echo esc_html__('Try it','schema-and-structured-data-for-wp') ?></span>
				    </div>
                                </a>
				
			</li>                        
		</ul>
	</div>

<?php }
function saswp_amp_page_callback(){
    
        $settings = saswp_defaultSettings();  
        
        $field_objs = new saswp_fields_generator();
        
        $non_amp_enable_field = array(
			'label'  => 'Structured Data for AMP',
			'id'     => 'saswp-for-amp-checkbox',                        
                        'name'   => 'saswp-for-amp-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'hidden' => array(
                             'id'   => 'saswp-for-amp',
                             'name' => 'sd_data[saswp-for-amp]',                             
                        )
		) ;                                        
                        
        if ( 
                is_plugin_active('accelerated-mobile-pages/accelerated-moblie-pages.php') || 
                is_plugin_active('amp/amp.php') || 
                is_plugin_active('better-amp/better-amp.php')  ||
                is_plugin_active('wp-amp/wp-amp.php') ||
                is_plugin_active('amp-wp/amp-wp.php')
                        
                ) {                         
        }else{
            
            $non_amp_enable_field['attributes'] = array(
                 'disabled' => 'disabled'
             );
             $non_amp_enable_field['note'] = esc_html__('Requires','schema-and-structured-data-for-wp'). ' <a target="_blank" href="https://wordpress.org/plugins/accelerated-mobile-pages/">AMP for WP</a> or <a target="_blank" href="https://wordpress.org/plugins/amp/">AMP</a> or <a target="_blank" href="https://wordpress.org/plugins/better-amp/">Better AMP</a> or <a target="_blank" href="https://codecanyon.net/item/wp-amp-accelerated-mobile-pages-for-wordpress-and-woocommerce/16278608">WP AMP</a>';
             $settings['saswp-for-amp'] = 0;	
            
        }
                
        $meta_fields = array(
            $non_amp_enable_field,
		 array(
			'label'  => 'Structured Data for Non AMP',
			'id'     => 'saswp-for-wordpress-checkbox',
                        'name'   => 'saswp-for-wordpress-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => '',
                        'hidden' => array(
                             'id'   => 'saswp-for-wordpress',
                             'name' => 'sd_data[saswp-for-wordpress]',                             
                        )
		)                                         
	);        
         echo '<div class="saswp-heading">';
         echo '<h2>'.esc_html__('Enable On','schema-and-structured-data-for-wp').'</h2>';        
         echo '</div>';
         echo '<p>'.esc_html__('Using this option, one can hide and show schema markup on AMP and Non AMP','schema-and-structured-data-for-wp').'</p>';
        
        $field_objs->saswp_field_generator($meta_fields, $settings);    
}

function saswp_general_page_callback(){	
            
	$settings = saswp_defaultSettings(); 
        $field_objs = new saswp_fields_generator(); 
        $nav_menu   = wp_get_nav_menus();
        
        $meta_fields_default = array(	
                array(
			'label'  => 'Website Schema (Global)',
			'id'     => 'saswp_website_schema_checkbox', 
                        'name'   => 'saswp_website_schema_checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',                        
                        'hidden' => array(
                             'id'   => 'saswp_website_schema',
                             'name' => 'sd_data[saswp_website_schema]',                             
                        )
		),
                array(
			'label'  => 'Sitelinks Search Box',
			'id'     => 'saswp_search_box_schema_checkbox', 
                        'name'   => 'saswp_search_box_schema_checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',                         
                        'hidden' => array(
                             'id'   => 'saswp_search_box_schema',
                             'name' => 'sd_data[saswp_search_box_schema]',                             
                        )
		),
		array(
			'label'  => 'Archive',
			'id'     => 'saswp_archive_schema_checkbox', 
                        'name'   => 'saswp_archive_schema_checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',                        
                        'hidden' => array(
                             'id'   => 'saswp_archive_schema',
                             'name' => 'sd_data[saswp_archive_schema]',                             
                        )
		),
                array(
			'label'   => 'Schema Type',
			'id'      => 'saswp_archive_schema_type',
                        'name'    => 'sd_data[saswp_archive_schema_type]',
                        'class'   => 'saswp_archive_schema_type_class',
			'type'    => 'select',
			'options' => array(                                
				     'Article'          => 'Article',                                     
                                     'BlogPosting'      => 'BlogPosting',                                     
                                     'NewsArticle'      => 'NewsArticle',                                                                                                                                                                                                                                                                   
                                     'WebPage'          => 'WebPage' 
			)
                   ),
                array(
			'label'  => 'BreadCrumbs',
			'id'     => 'saswp_breadcrumb_schema_checkbox', 
                        'name'   => 'saswp_breadcrumb_schema_checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',                        
                        'hidden' => array(
                             'id'   => 'saswp_breadcrumb_schema',
                             'name' => 'sd_data[saswp_breadcrumb_schema]',                             
                        )
		),
                array(
			'label'  => 'Comments',
			'id'     => 'saswp_comments_schema_checkbox', 
                        'name'   => 'saswp_comments_schema_checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',                        
                        'hidden' => array(
                             'id'   => 'saswp_comments_schema',
                             'name' => 'sd_data[saswp_comments_schema]',                             
                        )
		)
                                
            );
            if($nav_menu){
                
             $options = array();
             
             foreach($nav_menu as $menu){
                 
                 $options[$menu->term_id] = $menu->name;
             }
             
             $options = array('' => 'Select A Menu') + $options;
             
             $meta_fields_default[] =   array(
			'label'  => 'Site Navigation Menu',
			'id'     => 'saswp_site_navigation_menu', 
                        'name'   => 'sd_data[saswp_site_navigation_menu]',
			'type'   => 'select',                        
                        'options'=> $options
                        
		); 
            }                    
        ?>

    <div class="saswp-global-container" id="saswp-general-container">
                        
        <div class="saswp-settings-list">      
            
            <div class="saswp-heading">
              <h2><?php echo esc_html__('General Settings','schema-and-structured-data-for-wp'); ?></h2>              
            </div>
            <p><?php echo esc_html__('This is a global schema settings, to display about, contact, website, archive, breadcrumbs, comments and site navigation schema type.','schema-and-structured-data-for-wp') ?> <a target="_blank" href="http://structured-data-for-wp.com/docs/article/what-is-general-settings-in-schema/"><?php echo esc_html__('Learn More','schema-and-structured-data-for-wp') ?></a></p>   
        <ul><li><div class="saswp-about-contact-page-tooltip"><label class="saswp-tooltip">
        <?php echo esc_html__('About','schema-and-structured-data-for-wp') ?>
                <span class="saswp-tooltiptext"><?php echo esc_html__('Set the about page of of your website','schema-and-structured-data-for-wp') ?></span>
                </label>
        </div>
        <div>
        <div class="saswp-about-contact-page">  

                    <label for="sd_about_page-select">
                        <select data-type="page" class="saswp-select2" name="sd_data[sd_about_page]" id="sd_about_page">
                         <?php 
                         $saved_choices = array();
                         $choices  = saswp_get_condition_list('page');               
                         
                         if ( isset($settings['sd_about_page']) && $settings['sd_about_page'] !=  '' ) {
                                $saved_choices = saswp_get_condition_list('page', '', $settings['sd_about_page']);                        
                         }

                         $html_str = '';       
                         foreach ($choices as $value) {                                                                    
                              $html_str .= '<option value="'.esc_attr($value['id']).'">'.esc_html($value['text']).'</option>';
                         }
                         if($saved_choices){
                                foreach($saved_choices as $value){
                                        $html_str .= '<option value="' . esc_attr($value['id']) .'" selected> ' .  esc_html__($value['text'], 'schema-and-structured-data-for-wp') .'</option>';                     
                                }
                        } 

                        echo $html_str;
                         ?>                               
                        </select>
	            </label>  

        </div>
       </div>
    </li>
    <li><div class="saswp-about-contact-page-tooltip">
            <label class="saswp-tooltip">
    <?php echo esc_html__('Contact','schema-and-structured-data-for-wp') ?>
                <span class="saswp-tooltiptext"><?php echo esc_html__('Set the contact us page of your website','schema-and-structured-data-for-wp') ?></span>
            </label>
        </div>
        <div>
                 <div class="saswp-about-contact-page">          

                         <label for="sd_contact_page-select">
                         <select data-type="page" class="saswp-select2" name="sd_data[sd_contact_page]" id="sd_contact_page">
                         <?php 
                         $saved_choices = array();
                         $choices  = saswp_get_condition_list('page');               
                         
                         if ( isset($settings['sd_contact_page']) && $settings['sd_contact_page'] !=  '' ) {
                                $saved_choices = saswp_get_condition_list('page', '', $settings['sd_contact_page']);                        
                         }

                         $html_str = '';       
                         foreach ($choices as $value) {                                                                    
                              $html_str .= '<option value="'.esc_attr($value['id']).'">'.esc_html($value['text']).'</option>';
                         }
                         if($saved_choices){
                                foreach($saved_choices as $value){
                                        $html_str .= '<option value="' . esc_attr($value['id']) .'" selected> ' .  esc_html__($value['text'], 'schema-and-structured-data-for-wp') .'</option>';                     
                                }
                        } 

                        echo $html_str;
                         ?>                               
                        </select>
	     		 </label>       

       	 		</div>
        	        </div>
   			 </li>
			</ul>
		</div> 
        
        <?php
        
        echo '<div class="saswp-archive-div">';
        $field_objs->saswp_field_generator($meta_fields_default, $settings);
        echo '</div>';
        
        ?>
        
    </div>
    <div class="saswp-global-container" id="saswp-knowledge-container">
        
        <?php 
        
        
        $meta_fields = array(	                
                array(
			'label'   => 'Data Type',
			'id'      => 'saswp_kb_type',
                        'name'    => 'sd_data[saswp_kb_type]',
			'type'    => 'select',
			'options' => array(
                                ''             => 'Select an item',
				'Organization' => 'Organization',
				'Person'       => 'Person',
			)
                    ),
                    array(
			'label' => 'Organization Type',
			'id'    => 'saswp_organization_type',
                        'name'  => 'sd_data[saswp_organization_type]',
                        'class' => 'saswp_org_fields',
			'type'  => 'select',
			'options' => array(                                
				''                          => 'Select (Optional)',
				'Airline'                   => 'Airline',
                                'Consortium'                => 'Consortium',
                                'Corporation'               => 'Corporation',
                                'EducationalOrganization'   => 'EducationalOrganization',
                                'GovernmentOrganization'    => 'GovernmentOrganization',
                                'LibrarySystem'             => 'LibrarySystem',                                
                                'MedicalOrganization'       => 'MedicalOrganization',
                                'NewsMediaOrganization'     => 'NewsMediaOrganization',
                                'NGO'                       => 'NGO',
                                'PerformingGroup'           => 'PerformingGroup',
                                'SportsOrganization'        => 'SportsOrganization',
                                'WorkersUnion'              => 'WorkersUnion',
			)
                   ),
                array(
			'label' => 'Organization Name',
			'id'    => 'sd_name',
                        'name'  => 'sd_data[sd_name]',
                        'class' => 'regular-text saswp_org_fields',                        
			'type'  => 'text',
                        'attributes' => array(
                                'placeholder' => 'Organization Name'
                            )
		),
                               
                array(
			'label' => 'Organization URL',
			'id'    => 'sd_url',
                        'name'  => 'sd_data[sd_url]',
                        'class' => 'regular-text saswp_org_fields',                        
			'type'  => 'text',
                        'attributes' => array(
                                'placeholder' => 'https://www.example.com'
                            )
		), 
                array(
			'label' => 'Contact Type',
			'id'    => 'saswp_contact_type',
                        'name'  => 'sd_data[saswp_contact_type]',
                        'class' => 'saswp_org_fields',
			'type'  => 'select',
			'options' => array(
                                ''                    => 'Select an item',
				'customer support'    => 'Customer Support',
				'technical support'   => 'Technical Support',
                                'billing support'     => 'Billing Support',
                                'bill payment'        => 'Bill payment',
                                'sales'               => 'Sales',
                                'reservations'        => 'Reservations',
                                'credit card support' => 'Credit Card Support',
                                'emergency'           => 'Emergency',
                                'baggage tracking'    => 'Baggage Tracking',
                                'roadside assistance' => 'Roadside Assistance',
                                'package tracking'    => 'Package Tracking',
			)                        
                   ),
                    array(
                            'label' => 'Contact Number',
                            'id'    => 'saswp_kb_telephone',
                            'name'  => 'sd_data[saswp_kb_telephone]',
                            'class' => 'regular-text saswp_org_fields',                        
                            'type'  => 'text',
                            'attributes' => array(
                                    'placeholder' => '+1-012-012-0124'
                            )
                    ),
                    array(
                            'label' => 'Contact URL',
                            'id'    => 'saswp_kb_contact_url',
                            'name'  => 'sd_data[saswp_kb_contact_url]',
                            'class' => 'regular-text saswp_org_fields',                        
                            'type'  => 'text',
                            'attributes' => array(
                                    'placeholder' => 'https://www.example.com/contact'
                            )
                    ),
                                   
                   array(
			'label' => 'Name',
			'id'    => 'sd-person-name',
                        'name'  => 'sd_data[sd-person-name]',
                        'class' => 'regular-text saswp_person_fields',                        
			'type'  => 'text',
                        'attributes' => array(
                                    'placeholder' => 'Name'
                            )
		    ),
                    array(
			'label' => 'Job Title',
			'id'    => 'sd-person-job-title',
                        'name'  => 'sd_data[sd-person-job-title]',
                        'class' => 'regular-text saswp_person_fields',                        
			'type'  => 'text',
                        'attributes' => array(
                                    'placeholder' => 'Job Title'
                            )
		    ),  
                    array(
			'label'      => 'Image',
			'id'         => 'sd-person-image',
                        'name'       => 'sd_data[sd-person-image][url]',
                        'class'      => 'upload large-text saswp_person_fields',
			'type'       => 'media',
                        'attributes' => array(
                                'readonly' => 'readonly'
                            ) 
		   ),
                    array(
			'label'  => 'Phone Number',
			'id'     => 'sd-person-phone-number',
                        'name'   => 'sd_data[sd-person-phone-number]',
                        'class'  => 'regular-text saswp_person_fields',                        
			'type'   => 'text',
                        'attributes' => array(
                                    'placeholder' => '+1-012-012-0124'
                            )
		    ),
                     array(
			'label' => 'URL',
			'id'    => 'sd-person-url',
                        'name'  => 'sd_data[sd-person-url]',
                        'class' => 'regular-text saswp_person_fields',                        
			'type'  => 'text',
                        'attributes' => array(
                                            'placeholder' => 'https://www.example.com/person'
                        )                             
		    ),
                    array(
			'label' => 'Logo',
			'id'    => 'sd_logo',
                        'name'  => 'sd_data[sd_logo][url]',
                        'class' => 'saswp-icon upload large-text saswp_kg_logo',
			'type'  => 'media',
                        'note'  => 'According to google validation tool, Logo size must be 160*50 or 600*60',
                        'attributes' => array(
                                'readonly' => 'readonly'                                
                            )    
		   ),
                
	);
        
        echo '<div class="saswp-heading">';
        echo '<h2>'.esc_html__('Knowledge Graph','schema-and-structured-data-for-wp').'</h2>';                 
        echo '</div>';                
        echo '<p>'.esc_html__('The Knowledge Graph is a knowledge base used by Google and its services to enhance its search engine\'s results.','schema-and-structured-data-for-wp').' <a target="_blank" href="http://structured-data-for-wp.com/docs/article/how-to-setup-knowledge-graph-in-schema-in-wordpress/">'.esc_html__('Learn More','schema-and-structured-data-for-wp').'</a> </p>';
        echo '<div class="saswp-knowledge-base">';
        $field_objs->saswp_field_generator($meta_fields, $settings);
        echo '</div>';
        
        //social
        echo '<h2>'.esc_html__( 'Social Profile', 'schema-and-structured-data-for-wp' ).'</h2>';                  
        echo '<div class="saswp-social-fileds">';
        echo '<p>'.esc_html__('Add your social profile, Google will automatically crawl it in Knowledge Graph','schema-and-structured-data-for-wp').' <a target="_blank" href="https://structured-data-for-wp.com/docs/">'.esc_html__('Learn More','schema-and-structured-data-for-wp').'</a></p>';
        echo '<div class="saswp-social-links">';
        echo '<table class="saswp-social-links-table">';  
        if(isset($settings['saswp_social_links']) && !empty($settings['saswp_social_links'])){
           
                foreach($settings['saswp_social_links'] as $link){
                    echo '<tr><td><input type="text" placeholder="https://www.facebook.com/profile" name="sd_data[saswp_social_links][]" value="'.esc_url($link).'"></td><td><a class="button button-default saswp-rmv-modify_row">X</a></td></tr>';
                }
            
        } 
        echo '</table>';  
        echo '</div>';
        echo '<a class="button button-default saswp-add-social-links">Add New Social Profile</a>'; 
        echo '</div>';
                
        ?>
                        
    </div>
    <div class="saswp-global-container" id="saswp-default-container">
    
        <?php
                
        $meta_fields_default = array(	                                		                             
                array(
			'label' => 'Default Image',
			'id'    => 'sd_default_image',
                        'name'  => 'sd_data[sd_default_image][url]',
                        'class' => 'saswp-sd_default_image',
			'type'  => 'media',
		),
                array(
			'label' => 'Default Post Image Width',
			'id'    => 'sd_default_image_width',
                        'name'  => 'sd_data[sd_default_image_width]',
                        'class' => 'regular-text',                        
			'type'  => 'text',
		),
                array(
			'label' => 'Default Post Image Height',
			'id'    => 'sd_default_image_height',
                        'name'  => 'sd_data[sd_default_image_height]',
                        'class' => 'regular-text',                        
			'type'  => 'text',
                        'note'  => esc_html__('According to google validation tool, Image size must be greater than or equal to 1200*728','schema-and-structured-data-for-wp')
		)                
            
	);
        
        
        if(is_plugin_active('woocommerce/woocommerce.php')){
                              
            $meta_fields_default[] = array(
			'label'  => 'Product Default Review',
			'id'     => 'saswp-default-review-checkbox', 
                        'name'   => 'saswp-default-review-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',      
                        'note'   => 'This option will add a default review to a woocommerce product if reviews are not there', 
                        'hidden' => array(
                             'id'   => 'saswp_default_review',
                             'name' => 'sd_data[saswp_default_review]',                             
                        )
		);
                                      
        }
        
         echo '<div class="saswp-heading">';
         echo '<h2>'.esc_html__('Default Data','schema-and-structured-data-for-wp').'</h2>';                  
         echo '</div>';
         echo '<p>'.esc_html__('If schema markup doest not have image, it adds this image to validate schema markup.','schema-and-structured-data-for-wp').' <a target="_blank" href="http://structured-data-for-wp.com/docs/article/how-to-set-up-the-default-structured-data-values/">'.esc_html__('Learn More','schema-and-structured-data-for-wp').'</a></p>';
         echo '<div class="saswp-schema-type-fields">';
         $field_objs->saswp_field_generator($meta_fields_default, $settings);
         echo '</div>';  
        
        
        ?>
    </div>        
                                                                                                                             
	<?php
                
        
}

function saswp_check_data_imported_from($plugin_post_type_name){
    
       $cc_args    = array(
                        'posts_per_page'   => -1,
                        'post_type'        => 'saswp',
                        'meta_key'         => 'imported_from',
                        'meta_value'       => $plugin_post_type_name,
                    );	
       
	$imported_from = new WP_Query( $cc_args ); 
        
        return $imported_from;
}
function saswp_import_callback(){
    
        global $sd_data;
                                
        $settings = saswp_defaultSettings();         
        $field_objs = new saswp_fields_generator();
        $meta_fields = array(				
                array(
			'label'  => 'Defragment Schema Markup',
			'id'     => 'saswp-defragment-checkbox',                        
                        'name'   => 'saswp-defragment-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => 'It relates all schema markups on page to a main entity and merge all markup to a single markup. <a target="_blank" href="https://structured-data-for-wp.com/docs/article/what-is-defragment-schema-markup-and-how-to-add-it/">Learn More</a>',
                        'hidden' => array(
                             'id'   => 'saswp-defragment',
                             'name' => 'sd_data[saswp-defragment]',                             
                        )
		),
                array(
			'label'  => 'Add Schema Markup in footer',
			'id'     => 'saswp-markup-footer-checkbox',                        
                        'name'   => 'saswp-markup-footer-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'  => 'By default schema markup will be added in header section',
                        'hidden' => array(
                             'id'   => 'saswp-markup-footer',
                             'name' => 'sd_data[saswp-markup-footer]',                             
                        )
		),
                array(
			'label'  => 'Pretty Print Schema Markup',
			'id'     => 'saswp-pretty-print-checkbox',                        
                        'name'   => 'saswp-pretty-print-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'  => 'By default schema markup will be minified format',
                        'hidden' => array(
                             'id'   => 'saswp-pretty-print',
                             'name' => 'sd_data[saswp-pretty-print]',                             
                        )
		),
                array(
			'label'  => 'MicroData CleanUp',
			'id'     => 'saswp-microdata-cleanup-checkbox',                        
                        'name'   => 'saswp-microdata-cleanup-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',   
                        'note'   => 'It removes all the microdata generated by third party plugins which cause validation error on google testing tool',   
                        'hidden' => array(
                             'id'   => 'saswp-microdata-cleanup',
                             'name' => 'sd_data[saswp-microdata-cleanup]',                             
                        )
		),
                array(
			'label'  => 'Add All Available Images On Post',
			'id'     => 'saswp-other-images-checkbox',                        
                        'name'   => 'saswp-other-images-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',   
                        'note'   => 'It adds all the available images on a post to schema markup',   
                        'hidden' => array(
                             'id'   => 'saswp-other-images',
                             'name' => 'sd_data[saswp-other-images]',                             
                        )
                ),                
                array(
                        'label'  => 'Allow Multiple Size Image Creation',
                        'id'     => 'saswp-multiple-size-image-checkbox',                        
                        'name'   => 'saswp-multiple-size-image-checkbox',
                        'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',   
                        'note'   => 'According to Google, For best results, multiple high-resolution images with the following aspect ratios: 16x9, 4x3, and 1x1 should be there',   
                        'hidden' => array(
                                'id'   => 'saswp-multiple-size-image',
                                'name' => 'sd_data[saswp-multiple-size-image]',                             
                        )
                ),
                array(
                        'label'  => 'Add Featured Image in RSS feed',
                        'id'     => 'saswp-rss-feed-image-checkbox',                        
                        'name'   => 'saswp-rss-feed-image-checkbox',
                        'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',   
                        'note'   => 'Showing images alongside news/blogs if your website or blog appears in Google News',   
                        'hidden' => array(
                                'id'   => 'saswp-rss-feed-image',
                                'name' => 'sd_data[saswp-rss-feed-image]',                             
                        )
                )                  
                
	);   
        
        if(is_super_admin()){
            
            $meta_fields[] = array(
			'label'   => 'Role Based Access',
			'id'      => 'saswp-role-based-access',                        
                        'name'    => 'sd_data[saswp-role-based-access]',
			'type'    => 'multiselect',
                        'note'    => 'Choose the users whom you want to allow full access to this plugin',
                        'class'   => 'saswp-role-based-access-class',                          
                        'options' => saswp_get_user_roles()
		    );
            
        }       
                                                        
        $message                 = 'This plugin\'s data already has been imported. Do you want to import again?. click on button above button.';
        $schema_message          = '';
        $schema_pro_message      = '';
        $wp_seo_schema_message   = '';
        $seo_pressor_message     = '';
        $wpsso_core_message      = '';
        $aiors_message           = '';
        $wp_custom_rv_message    = '';
        $schema_for_faqs_message = '';
        $schema_plugin         = saswp_check_data_imported_from('schema'); 
        $schema_pro_plugin     = saswp_check_data_imported_from('schema_pro');
        $wp_seo_schema_plugin  = saswp_check_data_imported_from('wp_seo_schema');
        $seo_pressor           = saswp_check_data_imported_from('seo_pressor');
        $wpsso_core            = saswp_check_data_imported_from('wpsso_core');
        $aiors                 = saswp_check_data_imported_from('aiors');
        $wp_custom_rv          = saswp_check_data_imported_from('wp_custom_rv');
        $schema_for_faqs       = saswp_check_data_imported_from('schema_for_faqs');
        
        if($schema_for_faqs->post_count !=0 ){
            
          $schema_for_faqs_message = $message;
               
        }
        if($wp_custom_rv->post_count !=0 ){
            
          $wp_custom_rv_message = $message;
         
        }
        if($aiors->post_count !=0 ){
            
          $aiors_message = $message;
         
        }
        
        if($wpsso_core->post_count !=0){
            
          $wpsso_core_message = $message;
         
        }
        
        if($seo_pressor->post_count !=0){
            
          $seo_pressor_message = $message;
         
        }        
	if($schema_plugin->post_count !=0){
            
          $schema_message    = $message;
         
        }
        if($schema_pro_plugin->post_count !=0){
            
          $schema_pro_message = $message;   
         
        }
        if($wp_seo_schema_plugin->post_count !=0){
            
          $wp_seo_schema_message = $message;   
         
        }        	 
                              
         ?>
        <div class="saswp-tools-container" id="saswp-tools-advanced-container">
            
         <?php   
                echo '<h2>'.esc_html__('Advanced Settings','schema-and-structured-data-for-wp').'</h2>'; 
                $field_objs->saswp_field_generator($meta_fields, $settings);  
		echo '<h2>'.esc_html__('Migration','schema-and-structured-data-for-wp').'</h2>';       	                  
        ?>	
            <ul>
                <li><div class="saswp-tools-field-title"><div class="saswp-tooltip"><span class="saswp-tooltiptext"><?php echo esc_html__('All the settings and data you can import from this plugin when you click start importing','schema-and-structured-data-for-wp') ?></span><strong><?php echo esc_html__('Schema Plugin','schema-and-structured-data-for-wp'); ?></strong></div><button data-id="schema" class="button saswp-import-plugins"><?php echo esc_html__('Import','schema-and-structured-data-for-wp'); ?></button>
                        <p class="saswp-imported-message"></p>
                        <?php echo '<p>'.esc_html__($schema_message, 'schema-and-structured-data-for-wp').'</p>'; ?>    
                    </div>
                </li>
                <li><div class="saswp-tools-field-title"><div class="saswp-tooltip"><span class="saswp-tooltiptext"><?php echo esc_html__('All the settings and data you can import from this plugin when you click start importing','schema-and-structured-data-for-wp') ?></span><strong><?php echo esc_html__('Schema Pro','schema-and-structured-data-for-wp'); ?></strong></div><button data-id="schema_pro" class="button saswp-import-plugins"><?php echo esc_html__('Import','schema-and-structured-data-for-wp'); ?></button>
                        <p class="saswp-imported-message"></p>
                        <?php echo '<p>'.esc_html__($schema_pro_message, 'schema-and-structured-data-for-wp').'</p>'; ?>                       
                    </div>
                </li>
                <li><div class="saswp-tools-field-title"><div class="saswp-tooltip"><span class="saswp-tooltiptext"><?php echo esc_html__('All the settings and data you can import from this plugin when you click start importing','schema-and-structured-data-for-wp') ?></span><strong><?php echo esc_html__('WP SEO Schema','schema-and-structured-data-for-wp'); ?></strong></div><button data-id="wp_seo_schema" class="button saswp-import-plugins"><?php echo esc_html__('Import','schema-and-structured-data-for-wp'); ?></button>
                        <p class="saswp-imported-message"></p>
                        <?php echo '<p>'.esc_html__($wp_seo_schema_message, 'schema-and-structured-data-for-wp').'</p>'; ?>                       
                    </div>
                </li>
                <li><div class="saswp-tools-field-title"><div class="saswp-tooltip"><span class="saswp-tooltiptext"><?php echo esc_html__('All the settings and data you can import from this plugin when you click start importing','schema-and-structured-data-for-wp') ?></span><strong><?php echo esc_html__('SEO Pressor','schema-and-structured-data-for-wp'); ?></strong></div><button data-id="seo_pressor" class="button saswp-import-plugins"><?php echo esc_html__('Import','schema-and-structured-data-for-wp'); ?></button>
                        <p class="saswp-imported-message"></p>
                        <?php echo '<p>'.esc_html__($seo_pressor_message, 'schema-and-structured-data-for-wp').'</p>'; ?>                          
                    </div>
                </li>
                
                <li><div class="saswp-tools-field-title"><div class="saswp-tooltip"><span class="saswp-tooltiptext"><?php echo esc_html__('All the settings and data you can import from this plugin when you click start importing','schema-and-structured-data-for-wp') ?></span><strong><?php echo esc_html__('WPSSO Core','schema-and-structured-data-for-wp'); ?></strong></div><button data-id="wpsso_core" class="button saswp-import-plugins"><?php echo esc_html__('Import','schema-and-structured-data-for-wp'); ?></button>
                        <p class="saswp-imported-message"></p>
                        <?php echo '<p>'.esc_html__($wpsso_core_message, 'schema-and-structured-data-for-wp').'</p>'; ?>                          
                    </div>
                </li>
                <li><div class="saswp-tools-field-title"><div class="saswp-tooltip"><span class="saswp-tooltiptext"><?php echo esc_html__('All the settings and data you can import from this plugin when you click start importing','schema-and-structured-data-for-wp') ?></span><strong><?php echo esc_html__('Schema – All In One Schema Rich Snippets','schema-and-structured-data-for-wp'); ?></strong></div><button data-id="aiors" class="button saswp-import-plugins"><?php echo esc_html__('Import','schema-and-structured-data-for-wp'); ?></button>
                        <p class="saswp-imported-message"></p>
                        <?php echo '<p>'.esc_html__($aiors_message, 'schema-and-structured-data-for-wp').'</p>'; ?>                          
                    </div>
                </li>
                <li><div class="saswp-tools-field-title"><div class="saswp-tooltip"><span class="saswp-tooltiptext"><?php echo esc_html__('All the settings and data you can import from this plugin when you click start importing','schema-and-structured-data-for-wp') ?></span><strong><?php echo esc_html__('WP Customer Reviews','schema-and-structured-data-for-wp'); ?></strong></div><button data-id="wp_custom_rv" class="button saswp-import-plugins"><?php echo esc_html__('Import','schema-and-structured-data-for-wp'); ?></button>
                        <p class="saswp-imported-message"></p>
                        <?php echo '<p>'.esc_html__($wp_custom_rv_message, 'schema-and-structured-data-for-wp').'</p>'; ?>                          
                    </div>
                </li>

                <li><div class="saswp-tools-field-title"><div class="saswp-tooltip"><span class="saswp-tooltiptext"><?php echo esc_html__('All the settings and data you can import from this plugin when you click start importing','schema-and-structured-data-for-wp') ?></span><strong><?php echo esc_html__('FAQ Schema Markup – FAQ Structured Data','schema-and-structured-data-for-wp'); ?></strong></div><button data-id="schema_for_faqs" class="button saswp-import-plugins"><?php echo esc_html__('Import','schema-and-structured-data-for-wp'); ?></button>
                        <p class="saswp-imported-message"></p>
                        <?php echo '<p>'.esc_html__($schema_for_faqs_message, 'schema-and-structured-data-for-wp').'</p>'; ?>                          
                    </div>
                </li>
                
            </ul>                   
	<?php   
            echo '<h2>'.esc_html__('Import / Export','schema-and-structured-data-for-wp').'</h2>'; 
            $url = wp_nonce_url(admin_url('admin-ajax.php?action=saswp_export_all_settings_and_schema'), '_wpnonce');         
        ?>
        <ul>
                <li>
                    <div class="saswp-tools-field-title"><div class="saswp-tooltip"><strong><?php echo esc_html__('Export All Settings & Schema','schema-and-structured-data-for-wp'); ?></strong></div><a href="<?php echo esc_url($url); ?>"class="button saswp-export-data"><?php echo esc_html__('Export','schema-and-structured-data-for-wp'); ?></a>                         
                    </div>
                </li> 
                <li>
                    <div class="saswp-tools-field-title"><div class="saswp-tooltip"><strong><?php echo esc_html__('Import All Settings & Schema','schema-and-structured-data-for-wp'); ?></strong></div><input type="file" name="saswp_import_backup" id="saswp_import_backup">                         
                    </div>
                </li> 
        </ul>
        <?php                
         echo '<h2>'.esc_html__('Reset','schema-and-structured-data-for-wp').'</h2>'; 
         ?>
            <ul>
                <li>
                    <div class="saswp-tools-field-title">
                        <div class="saswp-tooltip"><strong><?php echo esc_html__('Reset Settings','schema-and-structured-data-for-wp'); ?></strong></div><a href="#"class="button saswp-reset-data"><?php echo esc_html__('Reset','schema-and-structured-data-for-wp'); ?></a>                         
                        <p><?php echo esc_html__('This will reset your settings and schema types','schema-and-structured-data-for-wp'); ?></p>
                    </div>
                </li> 
                
            </ul>
            
            <ul>
                <li>
                    <div class="">
                        <div class="saswp-tooltip"><strong><?php echo esc_html__('Remove Data On Uninstall','schema-and-structured-data-for-wp'); ?></strong></div><input type="checkbox" id="saswp_rmv_data_on_uninstall" name="sd_data[saswp_rmv_data_on_uninstall]" <?php echo (isset($sd_data['saswp_rmv_data_on_uninstall'])? 'checked': ''); ?>>                        
                        <p><?php echo esc_html__('This will remove all of its data when the plugin is deleted','schema-and-structured-data-for-wp'); ?></p>
                    </div>
                </li> 
                
            </ul>
            
            <ul>
                <li>
                    <div class="saswp-tools-field-title">
                        
                        <div class="saswp-tooltip"><strong><?php echo esc_html__('Data Tracking','schema-and-structured-data-for-wp'); ?></strong></div>
                        
                        <?php
                        
                            $settings       = saswp_defaultSettings();
                            $allow_tracking = get_option( 'wisdom_allow_tracking' );
                            
                            $plugin         = basename( SASWP_DIR_NAME_FILE, '.php' );
                       
                            if(isset($allow_tracking[$plugin])){
                                                $track_url = add_query_arg( array(
                                                        'plugin'        => $plugin,
                                                        'plugin_action'	=> 'no'
                                                ) );
                                echo '<a href="'. esc_url_raw( $track_url ).'" class="button-secondary">'.esc_html__( 'Disallow', 'schema-and-structured-data-for-wp' ).'</a>';

                            }else{

                                $track_url = add_query_arg(array(
					'plugin' 		=> $plugin,
					'plugin_action'   	=> 'yes'
				));
                             
                                 echo '<a href="'. esc_url_raw( $track_url ).'" class="button-secondary">'.esc_html__( 'Allow', 'schema-and-structured-data-for-wp' ).'</a>';
                                
                            }
                        
                        ?>
                                                                        
                        <p><?php echo esc_html__('We guarantee no sensitive data is collected','schema-and-structured-data-for-wp'); ?>
                            <a target="_blank" href="https://structured-data-for-wp.com/docs/article/usage-data-tracking/"><?php echo esc_html__('Learn more', 'schema-and-structured-data-for-wp'); ?></a>
                        </p>
                    </div>
                </li> 
                
            </ul>
            
        <?php    
                                
        $add_on = array();
                
        if(is_plugin_active('cooked-compatibility-for-schema/cooked-compatibility-for-schema.php')){
                      
           $add_on[] = 'Cooked';           
                                      
        }
        
        if(is_plugin_active('woocommerce-compatibility-for-schema/woocommerce-compatibility-for-schema.php')){
                      
           $add_on[] = 'Woocommerce';           
                                      
        }
        if(is_plugin_active('real-estate-schema/real-estate-schema.php')){
                      
           $add_on[] = 'Res';           
                                      
        }
        if(is_plugin_active('course-schema-for-saswp/course-schema-for-saswp.php')){
                      
           $add_on[] = 'Cs';           
                                      
        }
        if(is_plugin_active('event-schema-for-saswp/event-schema-for-saswp.php')){
                      
           $add_on[] = 'Es';           
                                      
        }
        if(is_plugin_active('recipe-schema-for-saswp/recipe-schema-for-saswp.php')){
                      
           $add_on[] = 'Rs';           
                                      
        }
                
        if(!empty($add_on)){
            
            echo '<h2>'.esc_html__('License','schema-and-structured-data-for-wp').'</h2>';
            
            echo '<ul>';
            
            foreach($add_on as $on){
                
                $license_key        = '';
                $license_status     = 'inactive';
                $license_status_msg = '';
                
                if(isset($sd_data[strtolower($on).'_addon_license_key'])){
                  $license_key =   $sd_data[strtolower($on).'_addon_license_key'];
                }
                
                if(isset($sd_data[strtolower($on).'_addon_license_key_status'])){
                  $license_status =   $sd_data[strtolower($on).'_addon_license_key_status'];
                }
                
                if(isset($sd_data[strtolower($on).'_addon_license_key_message'])){
                  $license_status_msg =   $sd_data[strtolower($on).'_addon_license_key_message'];
                }
                
                echo '<li>';
                echo saswp_get_license_section_html($on, $license_key, $license_status, $license_status_msg, true, false);
                echo '</li>';
                
            }
            
            echo '</ul>';
            
        }
            
         ?>   
            
        </div>
        <div class="saswp-tools-container" id="saswp-tools-translation-container">
          <?php 
          echo '<h2>'.esc_html__('Translation Panel','schema-and-structured-data-for-wp').'</h2>';
          
          global  $translation_labels;
                              
           ?> 
            <table>
            
           <?php 
           if(is_array($translation_labels)){
               
               foreach($translation_labels as $key => $val){
               if(isset($settings[$key]) && $settings[$key] !='' ){
                   $translation = $settings[$key];
               }else{
                   $translation = $val;
               }               
                echo  '<tr>'
                    . '<td><strong>'.esc_attr($val).'</strong></td>'
                    . '<td><input class="regular-text" type="text" name="sd_data['.esc_attr($key).']" value="'. esc_attr($translation).'"></td>'
                    . '</tr>';
               }
           
           }
           
           ?>
            
            </table>
          <?php
          ?>  
            
        </div>

        

<?php
         
}

function saswp_get_license_section_html($on, $license_key, $license_status, $license_status_msg, $label=null, $limit_status=null){
            
            $limits_html = $response = '';
    
            $limits = get_option('reviews_addon_reviews_limits');
    
            if($limit_status){
               $limits_html = '<span style="padding:10px;">Maximum Reviews Limits '. esc_attr($limits).'</span>'; 
            }

            $response.= '<div class="saswp-tools-field-title">';
                
               if($label == true && $on == 'Cooked'){
                   
                    $response.= '<div class="" style="display:inline-block">';
                    $response.= '<strong>'.esc_html__(''.$on.' Compatibility For Schema','schema-and-structured-data-for-wp').'</strong>';
                    $response.= '</div>';
                
               }
               if($label == true && $on == 'Woocommerce'){
                   
                    $response.= '<div class="" style="display:inline-block">';
                    $response.= '<strong>'.esc_html__(''.$on.' Compatibility For Schema','schema-and-structured-data-for-wp').'</strong>';
                    $response.= '</div>';
                
               }
               
               if($label == true && $on == 'Res'){
                   
                    $response.= '<div class="" style="display:inline-block">';
                    $response.= '<strong>'.esc_html__('Real Estate Schema','schema-and-structured-data-for-wp').'</strong>';
                    $response.= '</div>';
                
               }
               
               if($label == true && $on == 'Cs'){
                   
                    $response.= '<div class="" style="display:inline-block">';
                    $response.= '<strong>'.esc_html__('Course Schema','schema-and-structured-data-for-wp').'</strong>';
                    $response.= '</div>';
                
               }
               if($label == true && $on == 'Es'){
                   
                    $response.= '<div class="" style="display:inline-block">';
                    $response.= '<strong>'.esc_html__('Event Schema','schema-and-structured-data-for-wp').'</strong>';
                    $response.= '</div>';
                
               }
               
               if($label == true && $on == 'Rs'){
                   
                    $response.= '<div class="" style="display:inline-block">';
                    $response.= '<strong>'.esc_html__('Recipe Schema','schema-and-structured-data-for-wp').'</strong>';
                    $response.= '</div>';
                
               }
                                               
                if($license_status == 'active'){
                
                    $response.= '<span class="dashicons dashicons-yes saswp-'.strtolower($on).'-dashicons" style="color: #46b450;"></span>';    
                    
                }else{
                
                    $response.= '<span class="dashicons dashicons-no-alt saswp-'.strtolower($on).'-dashicons" style="color: #dc3232;"></span>';
                    
                }
                                                
                $response.= '<input type="text" placeholder="Enter License Key" id="'.strtolower($on).'_addon_license_key" name="sd_data['.strtolower($on).'_addon_license_key]" value="'.esc_attr($license_key).'">';
                
                $response.= '<input type="hidden" id="'.strtolower($on).'_addon_license_key_status" name="sd_data['.strtolower($on).'_addon_license_key_status]" value="'.esc_attr($license_status).'">';                
                
                if($license_status == 'active'){
                
                    $response.= '<a license-status="inactive" add-on="'.strtolower($on).'" class="button button-default saswp_license_activation">'.esc_html__('Deactivate', 'schema-and-structured-data-for-wp').'</a>'.$limits_html;
                    
                }else{
                
                    $response.= '<a license-status="active" add-on="'.strtolower($on).'" class="button button-default saswp_license_activation">'.esc_html__('Activate', 'schema-and-structured-data-for-wp').'</a>'.$limits_html;
                    
                }
                
                if($license_status_msg !='active'){
                    
                    $response.= '<p style="color:red;" add-on="'.strtolower($on).'" class="saswp_license_status_msg">'.$license_status_msg.'</p>';
                }                
                                                
                $response.= '<p>'.esc_html__('Enter your '.$on.' addon license key to activate updates & support.','schema-and-structured-data-for-wp').'</p>';
                
                $response.= '</div>';
                
                return $response;
    
}

function saswp_review_page_callback(){
        
        $settings = saswp_defaultSettings();         
        $field_objs = new saswp_fields_generator();
                                
        $meta_fields = array(				                               
                array(
			'label'  => 'Google Review',
			'id'     => 'saswp-google-review-checkbox',                        
                        'name'   => 'saswp-google-review-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => 'This option enables the google review section. <a target="_blank" href="https://structured-data-for-wp.com/docs/article/how-to-fetch-google-reviewfree-version/">Learn More</a>',
                        'hidden' => array(
                             'id'   => 'saswp-google-review',
                             'name' => 'sd_data[saswp-google-review]',                             
                        )
		),                                
                array(
                            'label' => 'Google place API Key',
                            'id'    => 'saswp_google_place_api_key',
                            'name'  => 'sd_data[saswp_google_place_api_key]',
                            'note'  => '<a target="_blank" href="https://console.developers.google.com/apis/library">Get place API Key</a> Note : Google allows only 5 latest reviews per location',
                            'class' => '',
                            'type'  => 'text',
                  ),                 
                array(
                            'label' => '',
                            'id'    => 'saswp-google-place-section',
                            'name'  => 'sd_data[saswp-google-place-section]',
                            'type'  => 'text',
                            
                  ),                
                 array(
                            'label' => 'Review Module',
                            'id'    => 'saswp-reviews-module-section',
                            'name'  => 'sd_data[saswp-reviews-module-section]',
                            'type'  => 'text',
                            
                  )  
                                  
	);    
                          
        ?>
        
    <div class="saswp-review-container" id="saswp-review-reviews-container">
        <?php 

            $meta_fields = apply_filters('saswp_modify_reviews_settings_page', $meta_fields);

            $field_objs->saswp_field_generator($meta_fields, $settings);  
            
            if(class_exists('saswp_reviews_platform_markup')){
                
                $platform_obj = new saswp_reviews_platform_markup();
                                            
                echo $platform_obj->reviews_markup();
                
            }
            
       ?>
        <div class="saswp-quick-links-div">
            <h4><?php echo esc_html__('Quick Links','schema-and-structured-data-for-wp'); ?></h4>       
            <p><a href="<?php echo admin_url('edit.php?post_type=saswp_reviews'); ?>"><?php echo esc_html__('View Current Reviews','schema-and-structured-data-for-wp'); ?></a></p>
            <p><a target="_blank" href="https://structured-data-for-wp.com/docs/article/how-to-display-reviews-with-collection-feature/"><?php echo esc_html__('How to show reviews on the website','schema-and-structured-data-for-wp'); ?></a></p>
        </div>
         
    </div>

    <div class="saswp-review-container" id="saswp-review-rating-container">
                
       <?php 
       
       $meta_fields = array(				
                array(
			'label'  => 'Rating Box',
			'id'     => 'saswp-review-module-checkbox',                        
                        'name'   => 'saswp-review-module-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => 'This option enables the review metabox on every post/page. <a target="_blank" href="https://structured-data-for-wp.com/docs/article/how-to-use-rating-module-in-schema-and-structured-data/">Learn More</a>',
                        'hidden' => array(
                             'id'   => 'saswp-review-module',
                             'name' => 'sd_data[saswp-review-module]',                             
                        )
                ),
                array(
                        'label'  => 'Stars Rating',
                        'id'     => 'saswp-stars-rating-checkbox',                        
                        'name'   => 'saswp-stars-rating-checkbox',
                        'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => 'This option adds rating field in wordpress default comment box <a target="_blank" href="https://structured-data-for-wp.com/docs/article/how-to-use-rating-module-in-schema-and-structured-data/">Learn More</a>',
                        'hidden' => array(
                                'id'   => 'saswp-stars-rating',
                                'name' => 'sd_data[saswp-stars-rating]',                             
                        )
                )
           );  
       
       $field_objs->saswp_field_generator($meta_fields, $settings); 
       ?> 
    </div>
    
    <?php
        
        
}

function saswp_email_schema_callback(){
        
        $settings = saswp_defaultSettings();  
                                        
        $woocommerce = array(
			'label'  => 'Woocommerce Booking',
			'id'     => 'saswp-woocommerce-booking-main-checkbox',                        
                        'name'   => 'saswp-woocommerce-booking-main-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'hidden' => array(
                                'id'   => 'saswp-woocommerce-booking-main',
                                'name' => 'sd_data[saswp-woocommerce-booking-main]',                             
                        )
		);
        
        if(!is_plugin_active('woocommerce/woocommerce.php') || !is_plugin_active('woocommerce-bookings/woocommerce-bookings.php')){
                      
             $woocommerce['note'] = esc_html__('Requires','schema-and-structured-data-for-wp').' <a target="_blank" href="https://wordpress.org/plugins/woocommerce/">Woocommerce</a>';
                                      
        }
        
        if(!is_plugin_active('woocommerce-compatibility-for-schema/woocommerce-compatibility-for-schema.php')){
                      
             $woocommerce['note'] = esc_html__('This feature requires','schema-and-structured-data-for-wp').' <a target="_blank" href="http://structured-data-for-wp.com/woocommerce-compatibility-for-schema/">WooCommerce Addon</a>';
                                      
        }
                                   
        $field_objs = new saswp_fields_generator();
        $meta_fields = array(				               
                $woocommerce,                                              
	);       
        
        $field_objs->saswp_field_generator($meta_fields, $settings);
                        
}

function saswp_compatibility_page_callback(){
        
        $settings = saswp_defaultSettings();  
        
        $wordpress_news = array(
			'label'  => 'Wordpress News',
			'id'     => 'saswp-wordpress-news-checkbox',                        
                        'name'   => 'saswp-wordpress-news-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('wordpress_news'),
                        'hidden' => array(
                                'id'   => 'saswp-wordpress-news',
                                'name' => 'sd_data[saswp-wordpress-news]',                             
                        )
		);
        
        $ampforwp = array(
			'label'  => 'AMPforWP',
			'id'     => 'saswp-ampforwp-checkbox',                        
                        'name'   => 'saswp-ampforwp-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('ampforwp'),
                        'hidden' => array(
                                'id'   => 'saswp-ampforwp',
                                'name' => 'sd_data[saswp-ampforwp]',                             
                        )
		);
        $ampbyautomatic = array(
			'label'  => 'AMP By Automatic',
			'id'     => 'saswp-ampbyautomatic-checkbox',                        
                        'name'   => 'saswp-ampbyautomatic-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('ampbyautomatic'),
                        'hidden' => array(
                                'id'   => 'saswp-ampbyautomatic',
                                'name' => 'sd_data[saswp-ampbyautomatic]',                             
                        )
                );
        $schemaforfaqs = array(
                'label'  => 'FAQ Schema Markup',
                'id'     => 'saswp-schemaforfaqs-checkbox',                        
                'name'   => 'saswp-schemaforfaqs-checkbox',
                'type'   => 'checkbox',
                'class'  => 'checkbox saswp-checkbox',
                'note'   => saswp_get_field_note('schemaforfaqs'),
                'hidden' => array(
                        'id'   => 'saswp-schemaforfaqs',
                        'name' => 'sd_data[saswp-schemaforfaqs]',                             
                )
        );
                        
        $total_recipe_generator = array(
			'label'  => 'Total Recipe Generator',
			'id'     => 'saswp-total-recipe-generator-checkbox',                        
                        'name'   => 'saswp-total-recipe-generator-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('total_recipe_generator'),
                        'hidden' => array(
                                'id'   => 'saswp-total-recipe-generator',
                                'name' => 'sd_data[saswp-total-recipe-generator]',                             
                        )
                );
        $wp_customer_review = array(
                'label'  => 'WP Customer Reviews',
                'id'     => 'saswp-wp-customer-reviews-checkbox',                        
                'name'   => 'saswp-wp-customer-reviews-checkbox',
                'type'   => 'checkbox',
                'class'  => 'checkbox saswp-checkbox',
                'note'   => saswp_get_field_note('wp_customer_reviews'),
                'hidden' => array(
                        'id'   => 'saswp-wp-customer-reviews',
                        'name' => 'sd_data[saswp-wp-customer-reviews]',                             
                )
        );        
        $simple_author_box = array(
                'label'  => 'Simple Author Box',
                'id'     => 'saswp-simple-author-box-checkbox',                        
                'name'   => 'saswp-simple-author-box-checkbox',
                'type'   => 'checkbox',
                'class'  => 'checkbox saswp-checkbox',
                'note'   => saswp_get_field_note('simple_author_box'),
                'hidden' => array(
                        'id'   => 'saswp-simple-author-box',
                        'name' => 'sd_data[saswp-simple-author-box]',                             
                )
        );        
        $ampwp = array(
			'label'  => 'AMP WP',
			'id'     => 'saswp-ampwp-checkbox',                        
                        'name'   => 'saswp-ampwp-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('ampwp'),
                        'hidden' => array(
                                'id'   => 'saswp-ampwp',
                                'name' => 'sd_data[saswp-ampwp]',                             
                        )
		);
        $tevolution_events = array(
			'label'  => 'Tevolution Events',
			'id'     => 'saswp-tevolution-events-checkbox',                        
                        'name'   => 'saswp-tevolution-events-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('tevolution_events'),
                        'hidden' => array(
                                'id'   => 'saswp-tevolution-events',
                                'name' => 'sd_data[saswp-tevolution-events]',                             
                        )
                );
        $stachethemes_events = array(
                'label'  => 'Stachethemes Event Calendar',
                'id'     => 'saswp-stachethemes-event-calendar-checkbox',                        
                'name'   => 'saswp-stachethemes-event-calendar-checkbox',
                'type'   => 'checkbox',
                'class'  => 'checkbox saswp-checkbox',
                'note'   => saswp_get_field_note('stachethemes_event_calendar'),
                'hidden' => array(
                        'id'   => 'saswp-stachethemes-event-calendar',
                        'name' => 'sd_data[saswp-stachethemes-event-calendar]',                             
                )
        );        
        $wp_event_aggregator = array(
			'label'  => 'WP Event Aggregator',
			'id'     => 'saswp-wp-event-aggregator-checkbox',                        
                        'name'   => 'saswp-wp-event-aggregator-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('wp_event_aggregator'),
                        'hidden' => array(
                                'id'   => 'saswp-wp-event-aggregator',
                                'name' => 'sd_data[saswp-wp-event-aggregator]',                             
                        )
        );
        $all_in_one_event_calendar = array(
                'label'  => 'All In One Event Calendar',
                'id'     => 'saswp-all-in-one-event-calendar-checkbox',                        
                'name'   => 'saswp-all-in-one-event-calendar-checkbox',
                'type'   => 'checkbox',
                'class'  => 'checkbox saswp-checkbox',
                'note'   => saswp_get_field_note('all_in_one_event_calendar'),
                'hidden' => array(
                        'id'   => 'saswp-all-in-one-event-calendar',
                        'name' => 'sd_data[saswp-all-in-one-event-calendar]',                             
                )
        ); 
        $event_on = array(
                'label'  => 'Event On',
                'id'     => 'saswp-event-on-checkbox',                        
                'name'   => 'saswp-event-on-checkbox',
                'type'   => 'checkbox',
                'class'  => 'checkbox saswp-checkbox',
                'note'   => saswp_get_field_note('event_on'),
                'hidden' => array(
                        'id'   => 'saswp-event-on',
                        'name' => 'sd_data[saswp-event-on]',                             
                )
        );        
        $betteramp = array(
			'label'  => 'Better AMP',
			'id'     => 'saswp-betteramp-checkbox',                        
                        'name'   => 'saswp-betteramp-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('betteramp'),
                        'hidden' => array(
                                'id'   => 'saswp-betteramp',
                                'name' => 'sd_data[saswp-betteramp]',                             
                        )
		);
        $wpamp = array(
			'label'  => 'WP AMP',
			'id'     => 'saswp-wpamp-checkbox',                        
                        'name'   => 'saswp-wpamp-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('wpamp'),
                        'hidden' => array(
                                'id'   => 'saswp-wpamp',
                                'name' => 'sd_data[saswp-wpamp]',                             
                        )
		);
        
        $taqyeem = array(
			'label'  => 'Taqyeem',
			'id'     => 'saswp-taqyeem-checkbox',                        
                        'name'   => 'saswp-taqyeem-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('taqyeem'),
                        'hidden' => array(
                                'id'   => 'saswp-taqyeem',
                                'name' => 'sd_data[saswp-taqyeem]',                             
                        )
		);
        $smart_crawl = array(
			'label'  => 'SmartCrawl Seo',
			'id'     => 'saswp-smart-crawl-checkbox',                        
                        'name'   => 'saswp-smart-crawl-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('smart_crawl'),
                        'hidden' => array(
                                'id'   => 'saswp-smart-crawl',
                                'name' => 'sd_data[saswp-smart-crawl]',                             
                        )
		);
        
        $the_seo_framework = array(
			'label'  => 'The SEO Framework',
			'id'     => 'saswp-the-seo-framework-checkbox',                        
                        'name'   => 'saswp-the-seo-framework-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('the_seo_framework'),
                        'hidden' => array(
                                'id'   => 'saswp-the-seo-framework',
                                'name' => 'sd_data[saswp-the-seo-framework]',                             
                        )
		);
        $homeland_theme = array(
			'label'  => 'HomeLand Theme',
			'id'     => 'saswp-homeland-checkbox',                        
                        'name'   => 'saswp-homeland-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox', 
                        'note'   => saswp_get_field_note('homeland'),
                        'hidden' => array(
                                'id'   => 'saswp-homeland',
                                'name' => 'sd_data[saswp-homeland]',                             
                        )
		);
        $real_homes = array(
			'label'  => 'RealHomes Theme',
			'id'     => 'saswp-realhomes-checkbox',                        
                        'name'   => 'saswp-realhomes-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('realhomes'),
                        'hidden' => array(
                                'id'   => 'saswp-realhomes',
                                'name' => 'sd_data[saswp-realhomes]',                             
                        )
		);
        $wpresidence = array(
			'label'  => 'WP Residence Theme',
			'id'     => 'saswp-wpresidence-checkbox',                        
                        'name'   => 'saswp-wpresidence-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('wpresidence'),
                        'hidden' => array(
                                'id'   => 'saswp-wpresidence',
                                'name' => 'sd_data[saswp-wpresidence]',                             
                        )
                );
        $myhome = array(
                'label'  => 'My Home Theme',
                'id'     => 'saswp-myhome-checkbox',                        
                'name'   => 'saswp-myhome-checkbox',
                'type'   => 'checkbox',
                'class'  => 'checkbox saswp-checkbox',
                'note'   => saswp_get_field_note('myhome'),
                'hidden' => array(
                        'id'   => 'saswp-myhome',
                        'name' => 'sd_data[saswp-myhome]',                             
                )
        ); 

        $classipress = array(
                'label'  => 'ClassiPress',
                'id'     => 'saswp-classipress-checkbox',                        
                'name'   => 'saswp-classipress-checkbox',
                'type'   => 'checkbox',
                'class'  => 'checkbox saswp-checkbox',
                'note'   => saswp_get_field_note('classipress'),
                'hidden' => array(
                        'id'   => 'saswp-classipress',
                        'name' => 'sd_data[saswp-classipress]',                             
                )
        ); 
        
        $realestate_5 = array(
                'label'  => 'WP Pro Real Estate 5',
                'id'     => 'saswp-realestate-5-checkbox',                        
                'name'   => 'saswp-realestate-5-checkbox',
                'type'   => 'checkbox',
                'class'  => 'checkbox saswp-checkbox',
                'note'   => saswp_get_field_note('realestate_5'),
                'hidden' => array(
                        'id'   => 'saswp-realestate-5',
                        'name' => 'sd_data[saswp-realestate-5]',                             
                )
        ); 
        
        $learn_press = array(
			'label'  => 'LearnPress',
			'id'     => 'saswp-learn-press-checkbox',                        
                        'name'   => 'saswp-learn-press-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('learn_press'),
                        'hidden' => array(
                                'id'   => 'saswp-learn-press',
                                'name' => 'sd_data[saswp-learn-press]',                             
                )
	);
        
        $learn_dash = array(
			'label'  => 'LearnDash',
			'id'     => 'saswp-learn-dash-checkbox',                        
                        'name'   => 'saswp-learn-dash-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',  
                        'note'   => saswp_get_field_note('learn_dash'),
                        'hidden' => array(
                                'id'   => 'saswp-learn-dash',
                                'name' => 'sd_data[saswp-learn-dash]',                             
                )
        );
        $wplms = array(
                'label'  => 'WPLMS',
                'id'     => 'saswp-wplms-checkbox',                        
                'name'   => 'saswp-wplms-checkbox',
                'type'   => 'checkbox',
                'class'  => 'checkbox saswp-checkbox',  
                'note'   => saswp_get_field_note('wplms'),
                'hidden' => array(
                        'id'   => 'saswp-wplms',
                        'name' => 'sd_data[saswp-wplms]',                             
        )
        );
        
        $lifter_lms = array(
			'label'  => 'LifterLMS',
			'id'     => 'saswp-lifter-lms-checkbox',                        
                        'name'   => 'saswp-lifter-lms-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('lifter_lms'),
                        'hidden' => array(
                                'id'   => 'saswp-lifter-lms',
                                'name' => 'sd_data[saswp-lifter-lms]',                             
                )
	);
        $wp_event_manager = array(
			'label'  => 'WP Event Manager',
			'id'     => 'saswp-wp-event-manager-checkbox',                        
                        'name'   => 'saswp-wp-event-manager-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('wp_event_manager'),
                        'hidden' => array(
                                'id'   => 'saswp-wp-event-manager',
                                'name' => 'sd_data[saswp-wp-event-manager]',                             
                )
	);
        
        $events_manager = array(
			'label'  => 'Events Manager',
			'id'     => 'saswp-events-manager-checkbox',                        
                        'name'   => 'saswp-events-manager-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('events_manager'),
                        'hidden' => array(
                                'id'   => 'saswp-events-manager',
                                'name' => 'sd_data[saswp-events-manager]',                             
                )
	);
        
        $events_calendar_wd = array(
			'label'  => 'Event Calendar WD',
			'id'     => 'saswp-event-calendar-wd-checkbox',                        
                        'name'   => 'saswp-event-calendar-wd-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('event_calendar_wd'),
                        'hidden' => array(
                                'id'   => 'saswp-event-calendar-wd',
                                'name' => 'sd_data[saswp-event-calendar-wd]',                             
                )
	);
        
        $event_organiser = array(
			'label'  => 'Event Organiser',
			'id'     => 'saswp-event-organiser-checkbox',                        
                        'name'   => 'saswp-event-organiser-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('event_organiser'),
                        'hidden' => array(
                                'id'   => 'saswp-event-organiser',
                                'name' => 'sd_data[saswp-event-organiser]',                             
                )
	);
        
        $modern_events_calendar = array(
			'label'  => 'Modern Events Calendar Lite',
			'id'     => 'saswp-modern-events-calendar-checkbox',                        
                        'name'   => 'saswp-modern-events-calendar-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('modern_events_calendar'),
                        'hidden' => array(
                                'id'   => 'saswp-modern-events-calendar',
                                'name' => 'sd_data[saswp-modern-events-calendar]',                             
                )
	);
                
        $seo_press = array(
			'label'  => 'SEOPress',
			'id'     => 'saswp-seo-press-checkbox',                        
                        'name'   => 'saswp-seo-press-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('seo_press'),
                        'hidden' => array(
                                'id'   => 'saswp-seo-press',
                                'name' => 'sd_data[saswp-seo-press]',                             
                        )
		);
        $squirrly_seo = array(
			'label'  => 'Squirrly Seo',
			'id'     => 'saswp-squirrly-seo-checkbox',                        
                        'name'   => 'saswp-squirrly-seo-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('squirrly_seo'),
                        'hidden' => array(
                                'id'   => 'saswp-squirrly-seo',
                                'name' => 'sd_data[saswp-squirrly-seo]',                             
                        )
		);
        $aiosp = array(
			'label'  => 'All in One SEO Pack',
			'id'     => 'saswp-aiosp-checkbox',                        
                        'name'   => 'saswp-aiosp-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('aiosp'),
                        'hidden' => array(
                                'id'   => 'saswp-aiosp',
                                'name' => 'sd_data[saswp-aiosp]',                             
                        )
		);        
        
        $recipe_maker = array(
			'label'  => 'WP Recipe Maker',
			'id'     => 'saswp-wp-recipe-maker-checkbox',                        
                        'name'   => 'saswp-wp-recipe-maker-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('wp_recipe_maker'),
                        'hidden' => array(
                                'id'   => 'saswp-wp-recipe-maker',
                                'name' => 'sd_data[saswp-wp-recipe-maker]',                             
                        )
		);
        $wp_ultimate_recipe = array(
			'label'  => 'WP Ultimate Recipe',
			'id'     => 'saswp-wp-ultimate-recipe-checkbox',                        
                        'name'   => 'saswp-wp-ultimate-recipe-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('wp_ultimate_recipe'),
                        'hidden' => array(
                                'id'   => 'saswp-wp-ultimate-recipe',
                                'name' => 'sd_data[saswp-wp-ultimate-recipe]',                             
                        )
		);
        $zip_recipes = array(
			'label'  => 'Zip Recipes',
			'id'     => 'saswp-zip-recipes-checkbox',                        
                        'name'   => 'saswp-zip-recipes-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('zip_recipes'),
                        'hidden' => array(
                                'id'   => 'saswp-zip-recipes',
                                'name' => 'sd_data[saswp-zip-recipes]',                             
                        )
		);
        $easyrecipe = array(
			'label'  => 'EasyRecipe',
			'id'     => 'saswp-easy-recipe-checkbox',                        
                        'name'   => 'saswp-easy-recipe-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('easy_recipe'),
                        'hidden' => array(
                                'id'   => 'saswp-easy-recipe',
                                'name' => 'sd_data[saswp-easy-recipe]',                             
                        )
		);
        $mediavine_create = array(
			'label'  => 'Create by Mediavine',
			'id'     => 'saswp-mediavine-create-checkbox',                        
                        'name'   => 'saswp-mediavine-create-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('mediavine_create'),
                        'hidden' => array(
                                'id'   => 'saswp-mediavine-create',
                                'name' => 'sd_data[saswp-mediavine-create]',                             
                        )
		);
        $ht_recipes = array(
			'label'  => 'HT Recipes',
			'id'     => 'saswp-ht-recipes-checkbox',                        
                        'name'   => 'saswp-ht-recipes-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('ht_recipes'),
                        'hidden' => array(
                                'id'   => 'saswp-ht-recipes',
                                'name' => 'sd_data[saswp-ht-recipes]',                             
                        )
		);
        
        $the_events_calendar = array(
			'label'  => 'The Events Calendar',
			'id'     => 'saswp-the-events-calendar-checkbox',                        
                        'name'   => 'saswp-the-events-calendar-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('the_events_calendar'),
                        'hidden' => array(
                                'id'   => 'saswp-the-events-calendar',
                                'name' => 'sd_data[saswp-the-events-calendar]',                             
                        )
		);
        
        
        
        $kk_star = array(
			'label'  => 'kk Star Ratings',
			'id'     => 'saswp-kk-star-raring-checkbox',                        
                        'name'   => 'saswp-kk-star-raring-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('kk_star_ratings'),
                        'hidden' => array(
                                'id'   => 'saswp-kk-star-raring',
                                'name' => 'sd_data[saswp-kk-star-raring]',                             
                        )
                );
        $yasr = array(
			'label'  => 'Yet Another Stars Rating',
			'id'     => 'saswp-yet-another-stars-rating-checkbox',                        
                        'name'   => 'saswp-yet-another-stars-rating-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('yet_another_stars_rating'),
                        'hidden' => array(
                                'id'   => 'saswp-yet-another-stars-rating',
                                'name' => 'sd_data[saswp-yet-another-stars-rating]',                             
                        )
		);
        $wppostratings = array(
			'label'  => 'WP-PostRatings',
			'id'     => 'saswp-wppostratings-raring-checkbox',                        
                        'name'   => 'saswp-wppostratings-raring-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('wp_post_ratings'),
                        'hidden' => array(
                                'id'   => 'saswp-wppostratings-raring',
                                'name' => 'sd_data[saswp-wppostratings-raring]',                             
                        )
		);
        $woocommerce = array(
			'label'  => 'Woocommerce',
			'id'     => 'saswp-woocommerce-checkbox',                        
                        'name'   => 'saswp-woocommerce-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('woocommerce'),
                        'hidden' => array(
                                'id'   => 'saswp-woocommerce',
                                'name' => 'sd_data[saswp-woocommerce]',                             
                        )
		);
        $woocommerce_bok = array(
			'label'  => 'Woocommerce Booking',
			'id'     => 'saswp-woocommerce-booking-checkbox',                        
                        'name'   => 'saswp-woocommerce-booking-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('woocommerce_bookings'),
                        'hidden' => array(
                                'id'   => 'saswp-woocommerce-booking',
                                'name' => 'sd_data[saswp-woocommerce-booking]',                             
                        )
		);
        
        $cooked = array(
			'label'  => 'Cooked',
			'id'     => 'saswp-cooked-checkbox',                        
                        'name'   => 'saswp-cooked-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('cooked'),
                        'hidden' => array(
                                'id'   => 'saswp-cooked',
                                'name' => 'sd_data[saswp-cooked]',                             
                        )
		);
        
        $woocommerce_mem = array(
			'label'  => 'Woocommerce Membership',
			'id'     => 'saswp-woocommerce-membership-checkbox',                        
                        'name'   => 'saswp-woocommerce-membership-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('woocommerce_membership'),
                        'hidden' => array(
                                'id'   => 'saswp-woocommerce-membership',
                                'name' => 'sd_data[saswp-woocommerce-membership]',                             
                        )
		);
        
        $extratheme = array(
			'label'  => 'Extra Theme By Elegant',
			'id'     => 'saswp-extra-checkbox',                        
                        'name'   => 'saswp-extra-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('extra'),
                        'hidden' => array(
                                'id'   => 'saswp-extra',
                                'name' => 'sd_data[saswp-extra]',                             
                        )
		);
        $soledad = array(
			'label'  => 'Soledad Theme',
			'id'     => 'saswp-soledad-checkbox',                        
                        'name'   => 'saswp-soledad-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('soledad'),
                        'hidden' => array(
                                'id'   => 'saswp-soledad',
                                'name' => 'sd_data[saswp-soledad]',                             
                        )
                );
        $reviews_wp_theme = array(
                'label'  => 'Reviews WP Theme',
                'id'     => 'saswp-wp-theme-reviews-checkbox',                        
                'name'   => 'saswp-wp-theme-reviews-checkbox',
                'type'   => 'checkbox',
                'class'  => 'checkbox saswp-checkbox',
                'note'   => saswp_get_field_note('reviews'),
                'hidden' => array(
                        'id'   => 'saswp-wp-theme-reviews',
                        'name' => 'sd_data[saswp-wp-theme-reviews]',                             
                )
        );        
        $dwquestiton = array(
			'label'  => 'DW Question Answer',
			'id'     => 'saswp-dw-question-answer-checkbox',                        
                        'name'   => 'saswp-dw-question-answer-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('dw_qna'),
                        'hidden' => array(
                                    'id'   => 'saswp-dw-question-answer',
                                    'name' => 'sd_data[saswp-dw-question-answer]',                             
                        )
		);
        
        $bbpress = array(
			'label'  => 'bbPress',
			'id'     => 'saswp-bbpress-checkbox',                        
                        'name'   => 'saswp-bbpress-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('bb_press'),
                        'hidden' => array(
                                    'id'   => 'saswp-bbpress',
                                    'name' => 'sd_data[saswp-bbpress]',                             
                        )
		);
                
        $yoast      = array(
			'label'   => 'Yoast SEO Plugin',
			'id'      => 'saswp-yoast-checkbox',                        
                        'name'    => 'saswp-yoast-checkbox',
			'type'    => 'checkbox',
                        'note'   => saswp_get_field_note('yoast_seo'),
                        'class'   => 'checkbox saswp-checkbox',
                        'hidden'  => array(
                                'id'   => 'saswp-yoast',
                                'name' => 'sd_data[saswp-yoast]',                             
                        )
		);
        $rankmath      = array(
			'label'   => 'Rank Math',
			'id'      => 'saswp-rankmath-checkbox',                        
                        'name'    => 'saswp-rankmath-checkbox',
			'type'    => 'checkbox',
                        'class'   => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('rank_math'),
                        'hidden'  => array(
                                'id'   => 'saswp-rankmath',
                                'name' => 'sd_data[saswp-rankmath]',                             
                        )
		); 
        
         $easy_testimonials = array(
			'label'  => 'Easy Testimonials',
			'id'     => 'saswp-easy-testimonials-checkbox',                        
                        'name'   => 'saswp-easy-testimonials-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('easy_testimonials'),
                        'hidden' => array(
                                'id'   => 'saswp-easy-testimonials',
                                'name' => 'sd_data[saswp-easy-testimonials]',                             
                        )
		);
         $bne_testimonials = array(
			'label'  => 'BNE Testimonials',
			'id'     => 'saswp-bne-testimonials-checkbox',                        
                        'name'   => 'saswp-bne-testimonials-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('bne_testimonials'),
                        'hidden' => array(
                                'id'   => 'saswp-bne-testimonials',
                                'name' => 'sd_data[saswp-bne-testimonials]',                             
                        )
		);
         
         $testimonial_pro = array(
			'label'  => 'Testimonial Pro',
			'id'     => 'saswp-testimonial-pro-checkbox',                        
                        'name'   => 'saswp-testimonial-pro-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('testimonial_pro'),
                        'hidden' => array(
                                'id'   => 'saswp-testimonial-pro',
                                'name' => 'sd_data[saswp-testimonial-pro]',                             
                        )
		);
         $strong_testimonials = array(
			'label'  => 'Strong Testimonials',
			'id'     => 'saswp-strong-testimonials-checkbox',                        
                        'name'   => 'saswp-strong-testimonials-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('strong_testimonials'),
                        'hidden' => array(
                                'id'   => 'saswp-strong-testimonials',
                                'name' => 'sd_data[saswp-strong-testimonials]',                             
                        )
		);
         $WordLift = array(
			'label'  => 'WordLift',
			'id'     => 'saswp-wordlift-checkbox',                        
                        'name'   => 'saswp-wordlift-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('wordlift'),
                        'hidden' => array(
                                'id'   => 'saswp-wordlift',
                                'name' => 'sd_data[saswp-wordlift]',                             
                        )
		);
        
        $flex_lmx = array(
			'label'  => 'FlexMLS IDX Plugin',
			'id'     => 'saswp-flexmlx-compativility-checkbox', 
                        'name'   => 'saswp-flexmlx-compativility-checkbox',
			'type'   => 'checkbox',
                        'class'  => 'checkbox saswp-checkbox',
                        'note'   => saswp_get_field_note('flex_mls_idx'),
                        'hidden' => array(
                             'id'   => 'saswp-flexmlx-compativility',
                             'name' => 'sd_data[saswp-flexmlx-compativility]',                             
                        )
		);
                
        if(!is_plugin_active('woocommerce-compatibility-for-schema/woocommerce-compatibility-for-schema.php')){
                      
             $woocommerce_bok['note'] = esc_html__('This feature requires','schema-and-structured-data-for-wp').' <a target="_blank" href="http://structured-data-for-wp.com/woocommerce-compatibility-for-schema/">Woocommerce Addon</a>';
                                      
        }
        
        if(!is_plugin_active('cooked-compatibility-for-schema/cooked-compatibility-for-schema.php')){
                          
             $cooked['note'] = esc_html__('This feature requires','schema-and-structured-data-for-wp').' <a target="_blank" href="http://structured-data-for-wp.com/cooked-compatibility-for-schema/">Cooked Addon</a>';
             
         }
         
         if(!is_plugin_active('real-estate-schema/real-estate-schema.php')){
                          
             $homeland_theme['note'] = esc_html__('This feature requires','schema-and-structured-data-for-wp').' <a target="_blank" href="https://structured-data-for-wp.com/extensions/">Real Estate Schema Addon</a>';
             $real_homes['note']     = esc_html__('This feature requires','schema-and-structured-data-for-wp').' <a target="_blank" href="https://structured-data-for-wp.com/extensions/">Real Estate Schema Addon</a>';
             $wpresidence['note']    = esc_html__('This feature requires','schema-and-structured-data-for-wp').' <a target="_blank" href="https://structured-data-for-wp.com/extensions/">Real Estate Schema Addon</a>';
             $myhome['note']         = esc_html__('This feature requires','schema-and-structured-data-for-wp').' <a target="_blank" href="https://structured-data-for-wp.com/extensions/">Real Estate Schema Addon</a>';
             $realestate_5['note']   = esc_html__('This feature requires','schema-and-structured-data-for-wp').' <a target="_blank" href="https://structured-data-for-wp.com/extensions/">Real Estate Schema Addon</a>';
             $classipress['note']   = esc_html__('This feature requires','schema-and-structured-data-for-wp').' <a target="_blank" href="https://structured-data-for-wp.com/extensions/">Real Estate Schema Addon</a>';
             
             
         }
         
         if(!is_plugin_active('course-schema-for-saswp/course-schema-for-saswp.php')){
                          
             $learn_press['note'] = esc_html__('This feature requires','schema-and-structured-data-for-wp').' <a target="_blank" href="https://structured-data-for-wp.com/course-schema/">Course Schema Addon</a>';
             $learn_dash['note']  = esc_html__('This feature requires','schema-and-structured-data-for-wp').' <a target="_blank" href="https://structured-data-for-wp.com/course-schema/">Course Schema Addon</a>';
             $lifter_lms['note']  = esc_html__('This feature requires','schema-and-structured-data-for-wp').' <a target="_blank" href="https://structured-data-for-wp.com/course-schema/">Course Schema Addon</a>';
             $wplms['note']  = esc_html__('This feature requires','schema-and-structured-data-for-wp').' <a target="_blank" href="https://structured-data-for-wp.com/course-schema/">Course Schema Addon</a>';
             
         }
         
         if(!is_plugin_active('event-schema-for-saswp/event-schema-for-saswp.php')){
                          
             $the_events_calendar['note']         = esc_html__('This feature requires','schema-and-structured-data-for-wp').' <a target="_blank" href="https://structured-data-for-wp.com/event-schema/">Event Schema Addon</a>';
             $events_calendar_wd['note']          = esc_html__('This feature requires','schema-and-structured-data-for-wp').' <a target="_blank" href="https://structured-data-for-wp.com/event-schema/">Event Schema Addon</a>';
             $wp_event_manager['note']            = esc_html__('This feature requires','schema-and-structured-data-for-wp').' <a target="_blank" href="https://structured-data-for-wp.com/event-schema/">Event Schema Addon</a>';
             $events_manager['note']              = esc_html__('This feature requires','schema-and-structured-data-for-wp').' <a target="_blank" href="https://structured-data-for-wp.com/event-schema/">Event Schema Addon</a>';
             $event_organiser['note']             = esc_html__('This feature requires','schema-and-structured-data-for-wp').' <a target="_blank" href="https://structured-data-for-wp.com/event-schema/">Event Schema Addon</a>';
             $modern_events_calendar['note']      = esc_html__('This feature requires','schema-and-structured-data-for-wp').' <a target="_blank" href="https://structured-data-for-wp.com/event-schema/">Event Schema Addon</a>';
             $tevolution_events['note']           = esc_html__('This feature requires','schema-and-structured-data-for-wp').' <a target="_blank" href="https://structured-data-for-wp.com/event-schema/">Event Schema Addon</a>';               
             $wp_event_aggregator['note']         = esc_html__('This feature requires','schema-and-structured-data-for-wp').' <a target="_blank" href="https://structured-data-for-wp.com/event-schema/">Event Schema Addon</a>';               
             $stachethemes_events['note']         = esc_html__('This feature requires','schema-and-structured-data-for-wp').' <a target="_blank" href="https://structured-data-for-wp.com/event-schema/">Event Schema Addon</a>';               
             $all_in_one_event_calendar['note']   = esc_html__('This feature requires','schema-and-structured-data-for-wp').' <a target="_blank" href="https://structured-data-for-wp.com/event-schema/">Event Schema Addon</a>';               
             $event_on['note']                    = esc_html__('This feature requires','schema-and-structured-data-for-wp').' <a target="_blank" href="https://structured-data-for-wp.com/event-schema/">Event Schema Addon</a>';               
             
         }
         
         if(!is_plugin_active('recipe-schema-for-saswp/recipe-schema-for-saswp.php')){
                          
             $zip_recipes['note']                = esc_html__('This feature requires','schema-and-structured-data-for-wp').' <a target="_blank" href="https://structured-data-for-wp.com/recipe-schema/">Recipe Schema Addon</a>';             
             $wp_ultimate_recipe['note']         = esc_html__('This feature requires','schema-and-structured-data-for-wp').' <a target="_blank" href="https://structured-data-for-wp.com/recipe-schema/">Recipe Schema Addon</a>';             
             $mediavine_create['note']           = esc_html__('This feature requires','schema-and-structured-data-for-wp').' <a target="_blank" href="https://structured-data-for-wp.com/recipe-schema/">Recipe Schema Addon</a>';             
             $ht_recipes['note']                 = esc_html__('This feature requires','schema-and-structured-data-for-wp').' <a target="_blank" href="https://structured-data-for-wp.com/recipe-schema/">Recipe Schema Addon</a>';
             $easyrecipe['note']                 = esc_html__('This feature requires','schema-and-structured-data-for-wp').' <a target="_blank" href="https://structured-data-for-wp.com/event-schema/">Recipe Schema Addon</a>';                            
             $total_recipe_generator['note']     = esc_html__('This feature requires','schema-and-structured-data-for-wp').' <a target="_blank" href="https://structured-data-for-wp.com/event-schema/">Recipe Schema Addon</a>';                            
             
         }
         
                                                 
        $field_objs = new saswp_fields_generator();
        
        $meta_fields = array(
                $ampforwp,
                $ampbyautomatic,
                $betteramp,
                $wpamp,
                $ampwp,
                $kk_star,
                $yasr,
                $wp_customer_review,
                $simple_author_box,  
                $wppostratings,
                $bbpress,
                $woocommerce,
                $woocommerce_bok,
                $woocommerce_mem,
                $cooked, 
                $soledad,
                $reviews_wp_theme,
                $taqyeem,
                $extratheme,
                $dwquestiton,                
                $yoast,
                $smart_crawl,
                $seo_press,
                $the_seo_framework,
                $aiosp,
                $squirrly_seo,                
                $recipe_maker,
                $wp_ultimate_recipe,
                $zip_recipes,
                $total_recipe_generator,
                $easyrecipe,
                $mediavine_create,
                $ht_recipes,
                $rankmath,
                $homeland_theme,
                $real_homes,
                $wpresidence,
                $myhome,
                $classipress,
                $realestate_5,
                $learn_press,
                $learn_dash,
                $lifter_lms,
                $wplms,
                $the_events_calendar,
                $wp_event_manager,
                $events_manager,
                $events_calendar_wd,
                $event_organiser,
                $modern_events_calendar,
                $tevolution_events,
                $wp_event_aggregator,
                $all_in_one_event_calendar,
                $event_on,
                $stachethemes_events,
                $easy_testimonials,
                $bne_testimonials,
                $testimonial_pro,
                $strong_testimonials,
                $wordpress_news,
                $WordLift,
                $schemaforfaqs,
                $flex_lmx
                
	);  
                
         $flex_mlx_extra_fields = array();
         $flex_mlx_extra_fields[] = array(
                        'label' => 'Name',
			'id'    => 'sd-seller-name',
                        'name'  => 'sd_data[sd-seller-name]',
                        'class' => 'regular-text',                        
			'type'  => 'text',
        );
         $flex_mlx_extra_fields[] = array(
                        'label' => 'Addres',
			'id'    => 'sd-seller-address',
                        'name'  => 'sd_data[sd-seller-address]',
                        'class' => 'regular-text',                        
			'type'  => 'text',
        );
         $flex_mlx_extra_fields[] = array(
                        'label' => 'Telephone',
			'id'    => 'sd-seller-telephone',
                        'name'  => 'sd_data[sd-seller-telephone]',
                        'class' => 'regular-text',                        
			'type'  => 'text',
        );
         $flex_mlx_extra_fields[] = array(
                        'label' => 'Price Range',
			'id'    => 'sd-seller-price-range',
                        'name'  => 'sd_data[sd-seller-price-range]',
                        'class' => 'regular-text',                        
			'type'  => 'text',
        );
        $flex_mlx_extra_fields[] = array(
			'label' => 'URL',
			'id'    => 'sd-seller-url',
                        'name'  => 'sd_data[sd-seller-url]',
                        'class' => 'regular-text',
			'type'  => 'text',
		);                                
        $flex_mlx_extra_fields[] = array(
			'label' => 'Image',
			'id'    => 'sd_seller_image',
                        'name'  => 'sd_data[sd_seller_image][url]',
                        'class' => 'saswp-sd_seller_image',
			'type'  => 'media',
	);
                
        ?> 

        <div class="saswp-compatibility-container" id="saswp-active-compatibility-container">
            
           <?php
            $act_meta_fields = $meta_fields;
             
            $active_plugins = saswp_compatible_active_list();
             
            foreach ($act_meta_fields as $key => $field){
                                  
                 if($field['hidden']['id'] == 'saswp-woocommerce-booking' || $field['hidden']['id'] == 'saswp-woocommerce-membership'){
                     
                     if(!array_search('saswp-woocommerce', $active_plugins)){
                                         
                         unset($act_meta_fields[$key]);
                     
                     }
                                          
                 }else{
                 
                     if(!array_search($field['hidden']['id'], $active_plugins)){
                                         
                         unset($act_meta_fields[$key]);
                     
                     }
                     
                 }
                 
                 
             }
            if($act_meta_fields){
                $field_objs->saswp_field_generator($act_meta_fields, $settings);
            }
                                                              
            if ( is_plugin_active('flexmls-idx/flexmls_connect.php') && isset($settings['saswp-flexmlx-compativility']) && $settings['saswp-flexmlx-compativility'] == 1) {
            
                echo '<div class="saswp-seller-div">';
                echo '<strong>'.esc_html__('Real estate agent info :','schema-and-structured-data-for-wp').'</strong>';

                $field_objs->saswp_field_generator($flex_mlx_extra_fields, $settings);

                echo '</div>';    
            }
            
            ?>
            
        </div>    

        <div class="saswp-compatibility-container" id="saswp-inactive-compatibility-container">
            
            <?php
            
            $ina_meta_fields = $meta_fields;
             
            $active_plugins = saswp_compatible_active_list();
             
            foreach ($ina_meta_fields as $key => $field){
                                  
                 if($field['hidden']['id'] == 'saswp-woocommerce-booking' || $field['hidden']['id'] == 'saswp-woocommerce-membership'){
                     
                     if(array_search('saswp-woocommerce', $active_plugins)){
                                         
                         unset($ina_meta_fields[$key]);
                     
                     }
                                          
                 }else{
                 
                     if(array_search($field['hidden']['id'], $active_plugins)){
                                         
                         unset($ina_meta_fields[$key]);
                     
                     }
                     
                 }
                                  
             }
            if($ina_meta_fields){
                $field_objs->saswp_field_generator($ina_meta_fields, $settings);
            }
            
            ?>
            
        </div>    
        <?php
                        
}

function saswp_support_page_callback(){
            
    ?>
     <div class="saswp_support_div">
            <strong><?php echo esc_html__('If you have any query, please write the query in below box or email us at', 'schema-and-structured-data-for-wp') ?> <a href="mailto:team@ampforwp.com">team@ampforwp.com</a>. <?php echo esc_html__('We will reply to your email address shortly', 'schema-and-structured-data-for-wp') ?></strong>
       
            <ul>
                <li>
                   <input type="text" id="saswp_query_email" name="saswp_query_email" placeholder="email">
                </li>
                <li>                    
                    <div><textarea rows="5" cols="60" id="saswp_query_message" name="saswp_query_message" placeholder="Write your query"></textarea></div>
                    <span class="saswp-query-success saswp_hide"><?php echo esc_html__('Message sent successfully, Please wait we will get back to you shortly', 'schema-and-structured-data-for-wp'); ?></span>
                    <span class="saswp-query-error saswp_hide"><?php echo esc_html__('Message not sent. please check your network connection', 'schema-and-structured-data-for-wp'); ?></span>
                </li>
                <li>
                    <strong><?php echo esc_html__('Are you a premium customer ?', 'schema-and-structured-data-for-wp'); ?></strong>  
                    <select id="saswp_query_premium_cus" name="saswp_query_premium_cus">                       
                        <option value=""><?php echo esc_html__('Select', 'schema-and-structured-data-for-wp'); ?></option>
                        <option value="yes"><?php echo esc_html__('Yes', 'schema-and-structured-data-for-wp'); ?></option>
                        <option value="no"><?php echo esc_html__('No', 'schema-and-structured-data-for-wp'); ?></option>
                    </select>                      
                </li>
                <li><button class="button saswp-send-query"><?php echo esc_html__('Send Message', 'schema-and-structured-data-for-wp'); ?></button></li>
            </ul>            
                   
        </div>
    <?php
    
   echo  '<h1>'.esc_html__( 'Frequently Asked Questions.', 'schema-and-structured-data-for-wp' ).'</h1> 
          <br>          
          <h3>1Q) '.esc_html__( 'Is there a Documentation Available?', 'schema-and-structured-data-for-wp' ).'</h3>
	  <p class="saswp_qanda_p">A) '.esc_html__( 'The Documentation is always updated and available at ', 'schema-and-structured-data-for-wp' ).'<a href="http://structured-data-for-wp.com/docs/" target="_blank"><strong>http://structured-data-for-wp.com/docs/</strong></a></p>
          
	  <h3>2Q) '.esc_html__( 'How can I setup the Schema and Structured data for individual pages and posts?', 'schema-and-structured-data-for-wp' ).'</h3>
	  <p class="saswp_qanda_p">A) '.esc_html__( 'Just with one click on the Structured data option, you will find an add new options window in the structured data option panel. Secondly, you need to write the name of the title where, if you would like to set the individual Page/Post then you can set the Page/Post type equal to the Page/Post(Name).', 'schema-and-structured-data-for-wp' ).'</p>

	  <h3>3Q) '.esc_html__( 'How can I check the code whether the structured data is working or not?', 'schema-and-structured-data-for-wp' ).'</h3>
	  <p class="saswp_qanda_p">A) To check the code, the first step we need to take is to copy the code of a page or post then visit the <a href="https://search.google.com/structured-data/testing-tool" target="_blank">Structured data testing tool</a> by clicking on code snippet. Once we paste the snippet we can run the test.</p>

	  <h3> 4Q) '.esc_html__( 'How can I check whether the pages or posts are valid or not?', 'schema-and-structured-data-for-wp' ).'</h3>
	  <p class="saswp_qanda_p"> A) '.esc_html__( 'To check the page and post validation, please visit the', 'schema-and-structured-data-for-wp' ).' <a href="https://search.google.com/structured-data/testing-tool" target="_blank">'.esc_html__( 'Structured data testing tool', 'schema-and-structured-data-for-wp' ).'</a> '.esc_html__( 'and paste the link of your website.', 'schema-and-structured-data-for-wp' ).' '.esc_html__( 'Once we click on run test we can see the result whether the page or post is a valid one or not.', 'schema-and-structured-data-for-wp' ).'</p>

	  <h3>5Q) '.esc_html__( 'Where should users contact if they faced any issues?', 'schema-and-structured-data-for-wp' ).'</h3>
	  <p class="saswp_qanda_p">A) '.esc_html__( 'We always welcome all our users to share their issues and get them fixed just with one click to the link', 'schema-and-structured-data-for-wp' ).' team@ampforwp.com or <a href="https://ampforwp.com/support/" target="_blank">'.esc_html__( 'Support link', 'schema-and-structured-data-for-wp' ).'</a></p><br>';
}

/**
 * Enqueue CSS and JS
 */
function saswp_enqueue_style_js( $hook ) { 
        
        global $saswp_metaboxes;

        $translable_txt = array(
                'attach_review'     => esc_html__( 'Attach reviews to this schema type' , 'schema-and-structured-data-for-wp' ),
                'place_id'          => esc_html__( 'Place ID' , 'schema-and-structured-data-for-wp' ),
                'reviews'           => esc_html__( 'Reviews' , 'schema-and-structured-data-for-wp' ),
                'fetch'             => esc_html__( 'Fetch' , 'schema-and-structured-data-for-wp' ),
                'step_in'           => esc_html__( 'Reviews count should be in step of 10' , 'schema-and-structured-data-for-wp' ),
                'blocks_zero'       => esc_html__( 'Blocks value is zero' , 'schema-and-structured-data-for-wp' ),
                'success'           => esc_html__( 'Success', 'schema-and-structured-data-for-wp' ),
                'enter_place_id'    => esc_html__( 'Please enter place id' , 'schema-and-structured-data-for-wp' ),
                'enter_api_key'     => esc_html__( 'Please enter api key' , 'schema-and-structured-data-for-wp' ),
                'enter_rv_api_key'  => esc_html__( 'Please enter reviews api key' , 'schema-and-structured-data-for-wp' ),
                'using_schema'      => esc_html__( 'Thanks for using Structured Data!' , 'schema-and-structured-data-for-wp' ),
                'do_you_want'       => esc_html__( 'Do you want the latest on ' , 'schema-and-structured-data-for-wp' ),
                'sd_update'         => esc_html__( 'Structured Data update' , 'schema-and-structured-data-for-wp' ),
                'before_others'     => esc_html__( ' before others and some best resources on monetization in a single email? - Free just for users of Structured Data!' , 'schema-and-structured-data-for-wp' ),
                'fill_email'        => esc_html__( 'Please fill in your name and email.' , 'schema-and-structured-data-for-wp' ),
                'invalid_email'     => esc_html__( 'Your email address is invalid.' , 'schema-and-structured-data-for-wp' ),
                'list_id_invalid'   => esc_html__( 'Your list ID is invalid.' , 'schema-and-structured-data-for-wp' ),
                'already_subsribed' => esc_html__( 'You\'re already subscribed!' , 'schema-and-structured-data-for-wp' ),
                'subsribed'         => esc_html__( 'Please enter reviews api key' , 'schema-and-structured-data-for-wp' ),
                'try_again'         => esc_html__( 'Please enter reviews api key' , 'schema-and-structured-data-for-wp' )
        );
        
        $post_type = '';
        
        $current_screen = get_current_screen(); 
       
        if(isset($current_screen->post_type)){                  
            $post_type = $current_screen->post_type;                
        }    
        
        if($saswp_metaboxes || $post_type == 'saswp' || $post_type == 'saswp-collections' || $post_type == 'saswp_reviews' || $hook == 'saswp_page_structured_data_options' || $hook == 'saswp_page_collection' ){

        $all_schema_array = array();
        
        $mappings_file = SASWP_DIR_NAME . '/core/array-list/schemas.php';
                
        if ( file_exists( $mappings_file ) ) {
            $all_schema_array = include $mappings_file;
        }
        
        $data = array(     
            'current_url'                  => saswp_get_current_url(), 
            'post_id'                      => get_the_ID(),
            'ajax_url'                     => admin_url( 'admin-ajax.php' ),            
            'saswp_security_nonce'         => wp_create_nonce('saswp_ajax_check_nonce'),  
            'new_url_selector'             => esc_url(admin_url()).'post-new.php?post_type=saswp',
            'new_url_href'                 => htmlspecialchars_decode(wp_nonce_url(admin_url('index.php?page=saswp_add_new_data_type&'), '_wpnonce')),            
            'collection_post_add_url'      => esc_url(admin_url()).'post-new.php?post_type=saswp-collections',
            'collection_post_add_new_url'  => htmlspecialchars_decode(wp_nonce_url(admin_url('admin.php?page=collection'), '_wpnonce')),
            'collections_page_url'         => htmlspecialchars_decode(admin_url('edit.php?post_type=saswp-collections')),
            'reviews_page_url'             => htmlspecialchars_decode(admin_url('edit.php?post_type=saswp_reviews')),
            'post_type'                    => $post_type,   
            'page_now'                     => $hook,
            'saswp_settings_url'           => esc_url(admin_url('edit.php?post_type=saswp&page=structured_data_options')),
            'saswp_schema_types'           =>  $all_schema_array,
            'trans_based_on'               => saswp_label_text('translation-based-on'),
            'trans_reviews'                => saswp_label_text('translation-reviews'),
            'trans_self'                   => saswp_label_text('translation-self'),
            'translable_txt'               => $translable_txt
        );
                        
        $data = apply_filters('saswp_localize_filter',$data,'saswp_localize_data');
	// Color picker CSS
	// @refer https://make.wordpress.org/core/2012/11/30/new-color-picker-in-wp-3-5/
        wp_enqueue_style( 'wp-color-picker' );	
	// Everything needed for media upload
        wp_enqueue_media();
        
        wp_enqueue_script('thickbox');
        wp_enqueue_style('thickbox');
                       	
        wp_enqueue_script( 'saswp-timepicker-js', SASWP_PLUGIN_URL . 'admin_section/js/jquery.timepicker.js', array( 'jquery', 'jquery-core', 'jquery-ui-core' ), SASWP_VERSION);        
        wp_enqueue_style( 'saswp-timepicker-css', SASWP_PLUGIN_URL . 'admin_section/css/jquery.timepicker.css', false , SASWP_VERSION );

        wp_enqueue_script( 'jquery-ui-datepicker' );
        wp_register_style( 'jquery-ui', SASWP_PLUGIN_URL. 'admin_section/css/jquery-ui.css' );
        wp_enqueue_style( 'jquery-ui' ); 
                                
        wp_enqueue_script( 'saswp-functions-list', SASWP_PLUGIN_URL . 'admin_section/js/'.(SASWP_ENVIRONMENT == 'production' ? 'functions-list.min.js' : 'functions-list.js'), false, SASWP_VERSION );
        
        wp_register_script( 'saswp-main-js', SASWP_PLUGIN_URL . 'admin_section/js/'.(SASWP_ENVIRONMENT == 'production' ? 'main-script.min.js' : 'main-script.js'), array('jquery','jquery-ui-core'), SASWP_VERSION , true );
                        
        wp_localize_script( 'saswp-main-js', 'saswp_localize_data', $data );
        
        wp_enqueue_script( 'saswp-main-js' );
        
        wp_enqueue_style( 'saswp-main-css', SASWP_PLUGIN_URL . 'admin_section/css/'.(SASWP_ENVIRONMENT == 'production' ? 'main-style.min.css' : 'main-style.css'), false , SASWP_VERSION );
                        
        wp_style_add_data( 'saswp-main-css', 'rtl', 'replace' );
        

        }                
        
}

function saswp_enqueue_saswp_select2_js( $hook ) { 
        
        global $saswp_metaboxes;
        
        $post_type = '';
        
        $current_screen = get_current_screen(); 
       
        if(isset($current_screen->post_type)){                  
            $post_type = $current_screen->post_type;                
        }    
        
        if($saswp_metaboxes || $post_type == 'saswp' || $post_type == 'saswp-collections' || $post_type == 'saswp_reviews' || $hook == 'saswp_page_structured_data_options' || $hook == 'saswp_page_collection' ){
                                        
        wp_enqueue_style('saswp-select2-style', SASWP_PLUGIN_URL. 'admin_section/css/select2.min.css' , false, SASWP_VERSION);
        wp_enqueue_script('saswp-select2-script', SASWP_PLUGIN_URL. 'admin_section/js/select2.min.js', array( 'jquery', 'jquery-core', 'jquery-ui-core' ), SASWP_VERSION, true);
        wp_enqueue_script('saswp-select2-extended-script', SASWP_PLUGIN_URL. 'admin_section/js/select2-extended.min.js', array( 'jquery', 'jquery-core', 'jquery-ui-core' ), SASWP_VERSION, true);
        	                                        
        }                
        
}

add_action( 'admin_enqueue_scripts', 'saswp_enqueue_saswp_select2_js',9999 );

add_action( 'admin_enqueue_scripts', 'saswp_enqueue_style_js' );

function saswp_option_page_capability( $capability ) {         
    return saswp_current_user_can();         
}

add_filter( 'option_page_capability_sd_data_group', 'saswp_option_page_capability' );

function saswp_pre_update_settings($value, $old_value,  $option){
    
    if(!is_super_admin()){
    
        if(isset($old_value['saswp-role-based-access'])){
           $value['saswp-role-based-access'] = $old_value['saswp-role-based-access']; 
        }
        
    }else{
        
        if(isset($value['saswp-role-based-access']) && !empty($value['saswp-role-based-access'])){
                if(!in_array('administrator', $value['saswp-role-based-access'])){
                    array_push($value['saswp-role-based-access'], 'administrator');
                }
        }else{
                $value['saswp-role-based-access'] = array();
                array_push($value['saswp-role-based-access'], 'administrator');
        }
                
    }    
   return $value; 
}

add_filter('pre_update_option_sd_data', 'saswp_pre_update_settings',10,3);