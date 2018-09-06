<?php
/**
 * Admin Settings
 * Function saswp_add_menu_links
 *
 */
function saswp_add_menu_links() {
    // Settings page - Same as main menu page
    add_submenu_page( 'edit.php?post_type=saswp', esc_html__( 'Schema And Structured Data For Wp', 'schema-and-structured-data-for-wp' ), esc_html__( 'Settings', 'schema-and-structured-data-for-wp' ), 'manage_options', 'structured_data_options', 'saswp_admin_interface_render' );

}
add_action( 'admin_menu', 'saswp_add_menu_links' );

function saswp_admin_interface_render(){
    // Authentication
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    // Handing save settings
    if ( isset( $_GET['settings-updated'] ) ) {
        // Show Settings Saved Message
        settings_errors();
    }
        $is_amp =false;
        if ( is_plugin_active('accelerated-mobile-pages/accelerated-moblie-pages.php') || is_plugin_active('amp/amp.php') ) {
    $is_amp = true;
        }
        if($is_amp){
        $tab = saswp_get_tab('general', array('general','knowledge','schema','amp', 'help'));
        }else{
        $tab = saswp_get_tab('general', array('general','knowledge','schema', 'help'));
        }

    ?>
    <div class="wrap saswp-settings-form">
        <h1> <?php echo esc_html__( 'Schema And Structured Data For WP', 'schema-and-structured-data-for-wp' ); ?></h1>
        <h2 class="nav-tab-wrapper saswp-tabs">
            <?php

            echo '<a href="' . esc_url(saswp_admin_link('general')) . '" class="nav-tab ' . esc_attr( $tab == 'general' ? 'nav-tab-active' : '') . '"><span class="dashicons dashicons-admin-generic"></span> ' . esc_html__('General','schema-and-structured-data-for-wp') . '</a>';

            echo '<a href="' . esc_url(saswp_admin_link('knowledge')) . '" class="nav-tab ' . esc_attr( $tab == 'knowledge' ? 'nav-tab-active' : '') . '"><span class="dashicons dashicons-info"></span> ' . esc_html__('Knowledge Base','schema-and-structured-data-for-wp') . '</a>';

            echo '<a href="' . esc_url(saswp_admin_link('schema')) . '" class="nav-tab ' . esc_attr( $tab == 'schema' ? 'nav-tab-active' : '') . '"><span class="dashicons dashicons-welcome-view-site"></span> ' . esc_html__('Schema Type','schema-and-structured-data-for-wp') . '</a>';

                        if($is_amp){
                        echo '<a href="' . esc_url(saswp_admin_link('amp')) . '" class="nav-tab ' . esc_attr( $tab == 'amp' ? 'nav-tab-active' : '') . '"><span class="dashicons dashicons-welcome-view-site"></span> ' . esc_html__('AMP','schema-and-structured-data-for-wp') . '</a>';
                        }

                        echo '<a href="' . esc_url(saswp_admin_link('help')) . '" class="nav-tab ' . esc_attr( $tab == 'help' ? 'nav-tab-active' : '') . '"><span class="dashicons dashicons-dashboard"></span> ' . esc_html__('Help','schema-and-structured-data-for-wp') . '</a>';
            ?>
        </h2>
        <form action="options.php" method="post" enctype="multipart/form-data">
            <div class="form-wrap">
            <?php
            // Output nonce, action, and option_page fields for a settings page.
            settings_fields( 'sd_data_group' );
            echo "<div class='saswp-general' ".( $tab != 'general' ? 'style="display:none;"' : '').">";
                // general Application Settings
                do_settings_sections( 'saswp_general_section' );	// Page slug
            echo "</div>";

            echo "<div class='saswp-knowledge' ".( $tab != 'knowledge' ? 'style="display:none;"' : '').">";
                // knowledge Application Settings
                do_settings_sections( 'saswp_knowledge_section' );	// Page slug
            echo "</div>";
            echo "<div class='saswp-schema' ".( $tab != 'schema' ? 'style="display:none;"' : '').">";
                do_settings_sections( 'saswp_schema_section' );	// Page slug
            echo "</div>";

                        echo "<div class='saswp-amp' ".( $tab != 'amp' ? 'style="display:none;"' : '').">";
                do_settings_sections( 'saswp_amp_section' );	// Page slug
            echo "</div>";

                        echo "<div class='saswp-help' ".( $tab != 'help' ? 'style="display:none;"' : '').">";
            // Status
            do_settings_sections( 'saswp_help_section' );	// Page slug
            echo "</div>";

            ?>
        </div>
            <div class="button-wrapper">
                <?php
                // Output save settings button
                                submit_button( esc_html__('Save Settings', 'schema-and-structured-data-for-wp') );
                ?>
            </div>
        </form>
    </div>
    <?php
}
/*
    WP Settings API
*/
add_action('admin_init', 'saswp_settings_init');

function saswp_settings_init(){
              register_setting( 'sd_data_group', 'sd_data' );
                add_settings_section('saswp_general_section', __return_false(), '__return_false', 'saswp_general_section');

                add_settings_field(
            'general_settings',								// ID
            '',		// Title
            'saswp_general_page_callback',								// CB
            'saswp_general_section',						// Page slug
            'saswp_general_section'						// Settings Section ID
        );

                add_settings_section('saswp_knowledge_section', __return_false(), '__return_false', 'saswp_knowledge_section');

        add_settings_field(
            'knowledge_settings',								// ID
            '',		// Title
            'saswp_knowledge_page_callback',								// CB
            'saswp_knowledge_section',						// Page slug
            'saswp_knowledge_section'						// Settings Section ID
        );
                add_settings_section('saswp_schema_section', __return_false(), '__return_false', 'saswp_schema_section');

        add_settings_field(
            'saswp_schema_settings',								// ID
            '',		// Title
            'saswp_schema_page_callback',								// CB
            'saswp_schema_section',						// Page slug
            'saswp_schema_section'						// Settings Section ID
        );

                add_settings_section('saswp_amp_section', __return_false(), '__return_false', 'saswp_amp_section');

        add_settings_field(
            'saswp_amp_settings',								// ID
            '',		// Title
            'saswp_amp_page_callback',								// CB
            'saswp_amp_section',						// Page slug
            'saswp_amp_section'						// Settings Section ID
        );

                add_settings_section('saswp_help_section', __return_false(), '__return_false', 'saswp_help_section');

                add_settings_field(
            'saswp_help_settings',								// ID
            '',		// Title
            'saswp_help_page_callback',								// CB
            'saswp_help_section',						// Page slug
            'saswp_help_section'						// Settings Section ID
        );
}
function saswp_schema_page_callback(){
    // Get Settings
    $settings = saswp_defaultSettings();
        $field_objs = new saswp_fields_generator();
         $meta_fields_default = array(
                array(
            'label' => 'Default Structured Data Logo',
            'id' => 'sd-data-logo-ampforwp',
                        'name' => 'sd_data[sd-data-logo-ampforwp][url]',
                        'class' => 'saswp-logo-ampforwp regular-text',
            'type' => 'media',
        ),
                array(
            'label' => 'Custom Logo Size',
            'id' => 'saswp-logo-dimensions-check',
                        'name' => 'sd_data[saswp-logo-dimensions]',
            'type' => 'checkbox',
                        'class' => 'checkbox saswp-checkbox',
                        'hidden' => array(
                             'id' => 'saswp-logo-dimensions',
                             'name' => 'sd_data[saswp-logo-dimensions]',
                        )
        ),
                array(
            'label' => 'Logo Width',
            'id' => 'saswp-logo-width',
                        'name' => 'sd_data[saswp-logo-width]',
                        'class' => 'regular-text',
            'type' => 'text',
                        'note' => 'Default width is 600 pixels'
        ),
                array(
            'label' => 'Logo Height',
            'id' => 'saswp-logo-height',
                        'name' => 'sd_data[saswp-logo-height]',
                        'class' => 'regular-text',
            'type' => 'text',
                        'note' => 'Default height is 60 pixels'
        ),
                array(
            'label' => 'Default Image',
            'id' => 'sd_default_image',
                        'name' => 'sd_data[sd_default_image][url]',
                        'class' => 'saswp-sd_default_image',
            'type' => 'media',
        ),
                array(
            'label' => 'Default Post Image Width',
            'id' => 'sd_default_image_width',
                        'name' => 'sd_data[sd_default_image_width]',
                        'class' => 'regular-text',
            'type' => 'text',
        ),
                array(
            'label' => 'Default Post Image Height',
            'id' => 'sd_default_image_height',
                        'name' => 'sd_data[sd_default_image_height]',
                        'class' => 'regular-text',
            'type' => 'text',
        ),
                array(
            'label' => 'Default Thumbnail for VideoObject',
            'id' => 'sd_default_video_thumbnail',
                        'name' => 'sd_data[sd_default_video_thumbnail][url]',
                        'class' => 'saswp-sd_default_video_thumbnail',
            'type' => 'media',
        ),
                array(
            'label' => 'Archive',
            'id' => 'saswp_archive_schema_checkbox',
                        'name' => 'saswp_archive_schema_checkbox',
            'type' => 'checkbox',
                        'class' => 'checkbox saswp-checkbox',
                        'hidden' => array(
                             'id' => 'saswp_archive_schema',
                             'name' => 'sd_data[saswp_archive_schema]',
                        )
        ),
                array(
            'label' => 'BreadCrumbs',
            'id' => 'saswp_breadcrumb_schema_checkbox',
                        'name' => 'saswp_breadcrumb_schema_checkbox',
            'type' => 'checkbox',
                        'class' => 'checkbox saswp-checkbox',
                        'hidden' => array(
                             'id' => 'saswp_breadcrumb_schema',
                             'name' => 'sd_data[saswp_breadcrumb_schema]',
                        )
        ),
    );
          echo '<h2>'.esc_html__('Default Values','schema-and-structured-data-for-wp').'</h2>';
         $field_objs->saswp_field_generator($meta_fields_default, $settings);
        ?>

    <?php
}

function saswp_amp_page_callback(){
    $settings = saswp_defaultSettings();
        $field_objs = new saswp_fields_generator();
        $meta_fields = array(

                array(
            'label' => 'Structured Data for AMP',
            'id' => 'saswp-for-amp-checkbox',
                        'name' => 'saswp-for-amp-checkbox',
            'type' => 'checkbox',
                        'class' => 'checkbox saswp-checkbox',
                        'hidden' => array(
                             'id' => 'saswp-for-amp',
                             'name' => 'sd_data[saswp-for-amp]',
                        )
        ),
                array(
            'label' => 'Structured Data for Non AMP',
            'id' => 'saswp-for-wordpress-checkbox',
                        'name' => 'saswp-for-wordpress-checkbox',
            'type' => 'checkbox',
                        'class' => 'checkbox saswp-checkbox',
                        'note'  => '',
                        'hidden' => array(
                             'id' => 'saswp-for-wordpress',
                             'name' => 'sd_data[saswp-for-wordpress]',
                        )
        ),
    );
        echo '<h2>'.esc_html__('Set Up','schema-and-structured-data-for-wp').'</h2>';
        $field_objs->saswp_field_generator($meta_fields, $settings);
}

function saswp_general_page_callback(){
    $settings = saswp_defaultSettings();
        ?>
<div class="saswp-settings-list">
<h2><?php echo esc_html__('Page Schema','schema-and-structured-data-for-wp') ?></h2>
<ul><li><div style="width:200px;float:left;clear: both;"><label>
     <?php echo esc_html__('About Us','schema-and-structured-data-for-wp') ?>
                </label>
        </div>
        <div style="width:85%">
        <div style="width:75%; float:right;">

                    <label for="sd_about_page-select">
    <?php
        echo wp_dropdown_pages( array(
            'name' => 'sd_data[sd_about_page]',
                        'id' => 'sd_about_page',
            'echo' => 0,
            'show_option_none' => esc_attr( 'Select an item' ),
            'option_none_value' => '',
            'selected' =>  isset($settings['sd_about_page']) ? $settings['sd_about_page'] : '',
        )); ?>
          </label>
        </div>
       </div>
    </li>
    <li><div style="width:200px;float:left;clear: both;">
            <label>
    <?php echo esc_html__('Contact Us','schema-and-structured-data-for-wp') ?>
            </label>
        </div>
        <div style="width:85%">
        <div style="width:75%; float:right;">

           <label for="sd_contact_page-select">
      <?php echo wp_dropdown_pages( array(
            'name' => 'sd_data[sd_contact_page]',
                        'id' => 'sd_contact_page-select',
            'echo' => 0,
            'show_option_none' => esc_attr( 'Select an item' ),
            'option_none_value' => '',
            'selected' =>  isset($settings['sd_contact_page']) ? $settings['sd_contact_page'] : '',
        )); ?>
                  </label>
                    </div>
             </div>
                </li>
            </ul>
        </div>
    <?php
}
function saswp_knowledge_page_callback(){
    // Get Settings
    $settings = saswp_defaultSettings();
        $field_objs = new saswp_fields_generator();
        $meta_fields = array(
                array(
            'label' => 'Data Type',
            'id' => 'saswp_kb_type',
                        'name' => 'sd_data[saswp_kb_type]',
            'type' => 'select',
            'options' => array(
                                ''=>'Select an item',
                'Organization'=>'Organization',
                'Person'=>'Person',
            )
                    ),

                array(
            'label' => 'Data Name',
            'id' => 'sd_name',
                        'name' => 'sd_data[sd_name]',
                        'class' => 'regular-text',
            'type' => 'text',
        ),
                array(
            'label' => 'Alternative Name',
            'id' => 'sd_alt_name',
                        'name' => 'sd_data[sd_alt_name]',
                        'class' => 'regular-text',
            'type' => 'text',
        ),
                array(
            'label' => 'Data url',
            'id' => 'sd_url',
                        'name' => 'sd_data[sd_url]',
                        'class' => 'regular-text',
            'type' => 'text',
        ),
                array(
            'label' => 'Logo',
            'id' => 'sd_logo',
                        'name' => 'sd_data[sd_logo][url]',
                        'class' => 'saswp-icon upload large-text',
            'type' => 'media',
        ),
                array(
            'label' => 'Contact details',
            'id' => 'saswp_kb_contact_1_checkbox',
                        'name' => 'saswp_kb_contact_1_checkbox',
            'type' => 'checkbox',
                        'class' => 'checkbox saswp-checkbox',
                        'hidden' => array(
                            'id' => 'saswp_kb_contact_1',
                            'name' => 'sd_data[saswp_kb_contact_1]'
                        )
        ),
                array(
            'label' => 'Telephone Number',
            'id' => 'saswp_kb_telephone',
                        'name' => 'sd_data[saswp_kb_telephone]',
                        'class' => 'regular-text',
            'type' => 'text',
        ),
                array(
            'label' => 'Contact Type',
            'id' => 'saswp_contact_type',
                        'name' => 'sd_data[saswp_contact_type]',
                        'class' => '',
            'type' => 'select',
            'options' => array(
                                ''=>'Select an item',
                'customer support'=>'Customer Support',
                'technical support'=>'Technical Support',
                                'billing support'=>'Billing Support',
                                'bill payment'=>'Bill payment',
                                'sales'=>'Sales',
                                'reservations'=>'Reservations',
                                'credit card support'=>'Credit Card Support',
                                'emergency'=>'Emergency',
                                'baggage tracking'=>'Baggage Tracking',
                                'roadside assistance'=>'Roadside Assistance',
                                'package tracking'=>'Package Tracking',
            )
                   ),
                   array(
            'label' => 'Name',
            'id' => 'sd-person-name',
                        'name' => 'sd_data[sd-person-name]',
                        'class' => 'regular-text',
            'type' => 'text',
            ),
                    array(
            'label' => 'Job Title',
            'id' => 'sd-person-job-title',
                        'name' => 'sd_data[sd-person-job-title]',
                        'class' => 'regular-text',
            'type' => 'text',
            ),
                    array(
            'label' => 'Image',
            'id' => 'sd-person-image',
                        'name' => 'sd_data[sd-person-image][url]',
                        'class' => 'upload large-text',
            'type' => 'media',
           ),
                    array(
            'label' => 'Phone Number',
            'id' => 'sd-person-phone-number',
                        'name' => 'sd_data[sd-person-phone-number]',
                        'class' => 'regular-text',
            'type' => 'text',
            ),
                     array(
            'label' => 'URL',
            'id' => 'sd-person-url',
                        'name' => 'sd_data[sd-person-url]',
                        'class' => 'regular-text',
            'type' => 'text',
            ),

    );
        echo '<h2>'.esc_html__('Knowledge Base','schema-and-structured-data-for-wp').'</h2>';
        echo '<div class="saswp-knowledge-base">';
        $field_objs->saswp_field_generator($meta_fields, $settings);
        echo '</div>';

        //social
        echo '<h2>'.esc_html__( 'Social Fields', 'schema-and-structured-data-for-wp' ).'</h2>';
        $social_meta_fields = array(
                array(
            'label' => 'Facebook',
            'id' => 'saswp-facebook-enable-checkbox',
                        'name' => 'saswp-facebook-enable-checkbox',
            'type' => 'checkbox',
                        'class' => 'checkbox saswp-checkbox',
                        'hidden' => array(
                             'id' => 'saswp-facebook-enable',
                             'name' => 'sd_data[saswp-facebook-enable]',
                        )
        ),
        array(
            'label' => '',
            'id' => 'sd_facebook',
                        'name' => 'sd_data[sd_facebook]',
                        'class' => 'regular-text',
            'type' => 'text',
                        'attributes' => array(
                            'placeholder' => 'https://'
                        )
            ),
                array(
            'label' => 'Twitter',
            'id' => 'saswp-twitter-enable-checkbox',
                        'name' => 'saswp-twitter-enable-checkbox',
            'type' => 'checkbox',
                        'class' => 'checkbox saswp-checkbox',
                        'hidden' => array(
                             'id' => 'saswp-twitter-enable',
                             'name' => 'sd_data[saswp-twitter-enable]',
                        )
        ),
                array(
            'label' => '',
            'id' => 'sd_twitter',
                        'name' => 'sd_data[sd_twitter]',
                        'class' => 'regular-text',
            'type' => 'text',
                        'attributes' => array(
                            'placeholder' => 'https://'
                        )
            ),
              array(
            'label' => 'Google+',
            'id' => 'saswp-google-plus-enable-checkbox',
                        'name' => 'saswp-google-plus-enable-checkbox',
            'type' => 'checkbox',
                        'class' => 'checkbox saswp-checkbox',
                        'hidden' => array(
                             'id' => 'saswp-google-plus-enable',
                             'name' => 'sd_data[saswp-google-plus-enable]',
                        )
        ),
                array(
            'label' => '',
            'id' => 'sd_google_plus',
                        'name' => 'sd_data[sd_google_plus]',
                        'class' => 'regular-text',
            'type' => 'text',
                        'attributes' => array(
                            'placeholder' => 'https://'
                        )
            ),
                array(
            'label' => 'Instagram',
            'id' => 'saswp-instagram-enable-checkbox',
                        'name' => 'saswp-instagram-enable-checkbox',
            'type' => 'checkbox',
                        'class' => 'checkbox saswp-checkbox',
                        'hidden' => array(
                             'id' => 'saswp-instagram-enable',
                             'name' => 'sd_data[saswp-instagram-enable]',
                        )
        ),
                array(
            'label' => '',
            'id' => 'sd_instagram',
                        'name' => 'sd_data[sd_instagram]',
                        'class' => 'regular-text',
            'type' => 'text',
                        'attributes' => array(
                            'placeholder' => 'https://'
                        )
            ),
                array(
            'label' => 'Youtube',
            'id' => 'saswp-youtube-enable-checkbox',
                        'name' => 'saswp-youtube-enable-checkbox',
            'type' => 'checkbox',
                        'class' => 'checkbox saswp-checkbox',
                        'hidden' => array(
                             'id' => 'saswp-youtube-enable',
                             'name' => 'sd_data[saswp-youtube-enable]',
                        )
        ),
                array(
            'label' => '',
            'id' => 'sd_youtube',
                        'name' => 'sd_data[sd_youtube]',
                        'class' => 'regular-text',
            'type' => 'text',
                        'attributes' => array(
                            'placeholder' => 'https://'
                        )
            ),
               array(
            'label' => 'LinkedIn',
            'id' => 'saswp-linkedin-enable-checkbox',
                        'name' => 'saswp-linkedin-enable-checkbox',
            'type' => 'checkbox',
                        'class' => 'checkbox saswp-checkbox',
                        'hidden' => array(
                             'id' => 'saswp-linkedin-enable',
                             'name' => 'sd_data[saswp-linkedin-enable]',
                        )
        ),
               array(
            'label' => '',
            'id' => 'sd_linkedin',
                        'name' => 'sd_data[sd_linkedin]',
                        'class' => 'regular-text',
            'type' => 'text',
                        'attributes' => array(
                            'placeholder' => 'https://'
                        )
            ),
                array(
            'label' => 'Pinterest',
            'id' => 'saswp-pinterest-enable-checkbox',
                        'name' => 'saswp-pinterest-enable-checkbox',
            'type' => 'checkbox',
                        'class' => 'checkbox saswp-checkbox',
                        'hidden' => array(
                             'id' => 'saswp-pinterest-enable',
                             'name' => 'sd_data[saswp-pinterest-enable]',
                        )
        ),
                array(
            'label' => '',
            'id' => 'sd_pinterest',
                        'name' => 'sd_data[sd_pinterest]',
                        'class' => 'regular-text',
            'type' => 'text',
                        'attributes' => array(
                            'placeholder' => 'https://'
                        )
            ),
                array(
            'label' => 'SoundCloud',
            'id' => 'saswp-soundcloud-enable-checkbox',
                        'name' => 'saswp-soundcloud-enable-checkbox',
            'type' => 'checkbox',
                        'class' => 'checkbox saswp-checkbox',
                        'hidden' => array(
                             'id' => 'saswp-soundcloud-enable',
                             'name' => 'sd_data[saswp-soundcloud-enable]',
                        )
        ),
                array(
            'label' => '',
            'id' => 'sd_soundcloud',
                        'name' => 'sd_data[sd_soundcloud]',
                        'class' => 'regular-text',
            'type' => 'text',
                        'attributes' => array(
                            'placeholder' => 'https://'
                        )
            ),
             array(
            'label' => 'Tumblr',
            'id' => 'saswp-tumblr-enable-checkbox',
                        'name' => 'saswp-tumblr-enable-checkbox',
            'type' => 'checkbox',
                        'class' => 'checkbox saswp-checkbox',
                        'hidden' => array(
                             'id' => 'saswp-tumblr-enable',
                             'name' => 'sd_data[saswp-tumblr-enable]',
                        )
        ),
                array(
            'label' => '',
            'id' => 'sd_tumblr',
                        'name' => 'sd_data[sd_tumblr]',
                        'class' => 'regular-text',
            'type' => 'text',
                        'attributes' => array(
                            'placeholder' => 'https://'
                        )
            ),

    );
        $field_objs->saswp_field_generator($social_meta_fields, $settings);

        ?>
    <?php
}
function saswp_help_page_callback(){
   echo '<h1>'.esc_html__( 'Here are some Q&A which helps you to understand and setup the Schema and Structured data for wp.', 'schema-and-structured-data-for-wp' ).'</h1>
         <br><br>
     <h3>1Q) '.esc_html__( 'How can I setup the Schema and Structured data for individual pages and posts?', 'schema-and-structured-data-for-wp' ).'</h3>
      <p class="saswp_qanda_p">A) '.esc_html__( 'Just with one click on the Structured data option, you will find an add new options window in the structured data option panel. Secondly, you need to write the name of the title where, if you would like to set the individual Page/Post then you can set the Page/Post type equal to the Page/Post(Name).', 'schema-and-structured-data-for-wp' ).'</p>

      <h3>2Q) '.esc_html__( 'How can I check the code whether the structured data is working or not?', 'schema-and-structured-data-for-wp' ).'</h3>
       <p class="saswp_qanda_p">A) To check the code, the first step we need to take is to copy the code of a page or post then visit the <a href="https://search.google.com/structured-data/testing-tool" target="_blank">Structured data testing tool</a> by clicking on code snippet. Once we paste the snippet we can run the test.</p>

    <h3> 3Q) '.esc_html__( 'How can I check whether the pages or posts are valid or not?', 'schema-and-structured-data-for-wp' ).'</h3>
    <p class="saswp_qanda_p"> A) '.esc_html__( 'To check the page and post validation, please visit the', 'schema-and-structured-data-for-wp' ).' <a href="https://search.google.com/structured-data/testing-tool" target="_blank">'.esc_html__( 'Structured data testing tool', 'schema-and-structured-data-for-wp' ).'</a> '.esc_html__( 'and paste the link of your website.', 'schema-and-structured-data-for-wp' ).' '.esc_html__( 'Once we click on run test we can see the result whether the page or post is a valid one or not.', 'schema-and-structured-data-for-wp' ).'</p>

     <h3>4Q) '.esc_html__( 'Where should users contact if they faced any issues?', 'schema-and-structured-data-for-wp' ).'</h3>
      <p class="saswp_qanda_p">A) '.esc_html__( 'We always welcome all our users to share their issues and get them fixed just with one click to the link', 'schema-and-structured-data-for-wp' ).' team@magazine3.com or <a href="https://ampforwp.com/support/" target="_blank">'.esc_html__( 'Support link', 'schema-and-structured-data-for-wp' ).'</a></p><br>';
}
/**
 * Enqueue CSS and JS
 */
function saswp_enqueue_style_js( $hook ) {
    if ( strpos( $hook, 'structured_data_options' ) === false ) {
        return;
    }
    // Color picker CSS
    // @refer https://make.wordpress.org/core/2012/11/30/new-color-picker-in-wp-3-5/
        wp_enqueue_style( 'wp-color-picker' );
    // Everything needed for media upload
        wp_enqueue_media();
    // Main JS
        wp_enqueue_script( 'saswp-main-js', SASWP_PLUGIN_URL . 'admin_section/js/main-script.js', array( 'wp-color-picker' ), SASWP_VERSION, true );
        //Main Css
        wp_enqueue_style( 'saswp-main-css', SASWP_PLUGIN_URL . 'admin_section/css/main-style.css', false , SASWP_VERSION );
}
add_action( 'admin_enqueue_scripts', 'saswp_enqueue_style_js' );