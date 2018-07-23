<?php
// Admin Panel Options
global $redux_builder_amp;
if ( ! class_exists( 'Redux' ) ) {
    return;
}

if( ! defined('AMPFORWP_IMAGE_DIR')):
  define('AMPFORWP_IMAGE_DIR',plugin_dir_url(__FILE__).'images');
endif;
// Option name where all the Redux data is stored.
$opt_name = "sd_data";
$comment_AD_URL = "http://ampforwp.com/amp-comments/#utm_source=options-panel&utm_medium=comments-tab&utm_campaign=AMP%20Plugin";
$cta_AD_URL = "http://ampforwp.com/call-to-action/#utm_source=options-panel&utm_medium=call-to-action_banner_in_notification_bar&utm_campaign=AMP%20Plugin";
$comment_desc = '<a href="'.$comment_AD_URL.'"  target="_blank"><img class="ampforwp-ad-img-banner" src="'.AMPFORWP_IMAGE_DIR . '/comments-banner.png" width="560" height="85" /></a>';
$cta_desc = '<a href="'.$cta_AD_URL.'"  target="_blank"><img class="ampforwp-ad-img-banner" src="'.AMPFORWP_IMAGE_DIR . '/cta-banner.png" width="560" height="85" /></a>';


// All the possible arguments for Redux.
//$amp_redux_header = '<span id="name"><span style="color: #4dbefa;">U</span>ltimate <span style="color: #4dbefa;">W</span>idgets</span>';

$args = array(
    // TYPICAL -> Change these values as you need/desire
    'opt_name'              => $opt_name, // This is where your data is stored in the database and also becomes your global variable name.
    'display_name'          =>  __( 'Structured data Options','sd-for-wp' ), // Name that appears at the top of your panel
    'menu_type'             => 'menu', //Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
    'allow_sub_menu'        => true, // Show the sections below the admin menu item or not
    'menu_title'            => __( 'Structured data', 'sd-for-wp' ),
    'page_title'            => __('Structured data Options','sd-for-wp'),
    'display_version'       => STRUCTURED_DATA_VERSION,
    'update_notice'         => false,
    'global_variable'       => '', // Set a different name for your global variable other than the opt_name
    'dev_mode'              => false, // Show the time the page took to load, etc
    'customizer'            => false, // Enable basic customizer support,
    'async_typography'      => false, // Enable async for fonts,
    'disable_save_warn'     => true,
    'open_expanded'         => false,
    // OPTIONAL -> Give you extra features
    'page_priority'         => null, // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
    'page_parent'           => 'themes.php', // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
    'page_permissions'      => 'manage_options', // Permissions needed to access the options panel.
    'last_tab'              => '', // Force your panel to always open to a specific tab (by id)
    'page_icon'             => 'icon-themes', // Icon displayed in the admin panel next to your menu_title
    'page_slug'             => 'structured_data_options', // Page slug used to denote the panel
    'save_defaults'         => true, // On load save the defaults to DB before user clicks save or not
    'default_show'          => false, // If true, shows the default value next to each field that is not the default value.
    'default_mark'          => '', // What to print by the field's title if the value shown is default. Suggested: *
    'admin_bar'             => false,
    'admin_bar_icon'        => 'dashicons-admin-generic', 
    // CAREFUL -> These options are for advanced use only
    'output'                => false, // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
    'output_tag'            => false, // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
    //'domain'              => 'redux-framework', // Translation domain key. Don't change this unless you want to retranslate all of Redux.
    'footer_credit'         => false, // Disable the footer credit of Redux. Please leave if you can help it.
    'footer_text'           => "",
    'show_import_export'    => true,
    'system_info'           => true,

);

    $args['share_icons'][] = array(
        'url'   => 'https://github.com/ahmedkaludi/sd-for-wp',
        'title' => __('Visit us on GitHub','sd-for-wp'),
        'icon'  => 'el el-github'
        //'img'   => '', // You can use icon OR img. IMG needs to be a full URL.
    );


Redux::setArgs( $opt_name, $args );




    $tabs = array(
        array(
            'id'      => 'redux-help-tab-1',
            'title'   => __( 'Theme Information 1', 'sd-for-wp' ),
            'content' => __( '<p>This is the tab content, HTML is allowed.</p>', 'sd-for-wp' )
        ),
        array(
            'id'      => 'redux-help-tab-2',
            'title'   => __( 'Theme Information 2', 'sd-for-wp' ),
            'content' => __( '<p>This is the tab content, HTML is allowed.</p>', 'sd-for-wp' )
        )
    );
    Redux::setHelpTab( $opt_name, $tabs );

    // Set the help sidebar
    $content = __( '<p>This is the sidebar content, HTML is allowed.</p>', 'admin_folder' );
    Redux::setHelpSidebar( $opt_name, $content );

// Blank field created so that Getting Started can Work
    Redux::setSection( $opt_name, array(
        'title'  => __( 'Basic Field', 'accelerated-mobile-pages' ),
        'id'     => 'basic',
        'desc'   => __( 'Basic field with no subsections.', 'accelerated-mobile-pages' ),
        'icon'   => 'el el-home',
        'fields' => array(
            array(
                'id'       => 'opt-blank',
            )
        )
    ) );

Redux::setSection( $opt_name, array(
        'title' => __( 'Getting Started', 'sd-for-wp' ),
        'id'    => 'basic',
    
        'desc'  => __( '<div class="amp-faq">'. ' ' .

                       '<p><strong>' . __( '1. <a href="https://ampforwp.com/tutorials/article/set-structured-data-plugin-site/" target="_blank">View Documentation</a>: ', 'sd-for-wp' ) . '</strong>' . __( 'This tutorial will show you the usage of the Structured data extension options.' ) . '</p>'
                           . '<p><strong>' . __( '2. <a href="https://ampforwp.com/support/#contact" target="_blank">Contact Technical Team</a>: ', 'sd-for-wp' ) . '</strong>' . __( 'You can get in touch with our technical team to clarify any doubts or questions.') . '</p>'
                           . '<p><strong>' . __( '3. <a href="https://ampforwp.com/support/#contact" target="_blank">Report A Bug</a>: ', 'sd-for-wp' ) . '</strong>' . __( 'Have you found an issue? Just report it our team and we will solve it in the next update.' ) . '</p>'
                           . '<p><strong>' . __( '4. <a href="https://ampforwp.com/support/#contact" target="_blank">Request a Feature</a>: ', 'sd-for-wp' ) . '</strong>' . __( 'Just request a new feature and we will include them as soon as possible' ) . '</p>'

                                 . '</p></div>
                                 '

                 , 'sd-for-wp' ),
        'icon'  => 'el el-home'
    ) );

     Redux::setSection( $opt_name, array(
        'title'      => __( 'General Settings', 'sd-for-wp' ),
       // 'desc'       => __( 'For full documentation on this field, visit: ', 'sd-for-wp' ) . '<a href="http://docs.reduxframework.com/core/fields/text/" target="_blank">http://docs.reduxframework.com/core/fields/text/</a>',
        'id'         => 'sd_general_settings',
        'subsection' => true,
        'fields'     => array(
               array(
               'id'       => 'sd_setup_section',
               'type'     => 'section',
               'title'    => __( 'Set up', 'sd-for-wp' ),
               'indent' => true,
               'layout_type' => 'accordion',
                'accordion-open'=> 1,
              ),
               array(
                'id'       => 'sd-for-wordpress',
                'type'     => 'switch',
                'title'    => __('Structured Data for WordPress', 'sd-for-wp'), 
                'default'  =>'0'
                ),
               array(
                'id'       => 'sd-for-ampforwp',
                'type'     => 'switch',
                'title'    => __('Structured Data for AMP', 'sd-for-wp'),
                'default'  => '1',
                'required'  => array(
                                array('sd-for-ampforwp-with-scheme-app','=','0'),
                ),
              ),
               array(
                'id'       => 'sd-for-ampforwp-with-scheme-app',
                'type'     => 'switch',
                'title'    => __('Schema App by Hunch Manifest compatibility in AMP', 'sd-for-wp'),
                'desc'     => 'Note: It will override the \'Strucuture Data for AMP\' option',
                'default'  => '0'
              ),
              array(
               'id'       => 'sd_about_page_section',
               'type'     => 'section',
               'title'    => __( 'Page Schema', 'sd-for-wp' ),
               'indent' => true,
               'layout_type' => 'accordion',
                'accordion-open'=> 1,
              ),
              array(
                'id'       => 'sd_about_page',
                'type'     => 'select',
                'title'    => __('About Us', 'sd-for-wp'),
                'tooltip-subtitle' => __('This will apply the about page schema on the selected page', 'sd-for-wp'),
                'data'     => 'page',
                'args'     => array(
                    'post_type' => 'page',
                    'posts_per_page' => 500
                ),
            ),
              
              array(
                'id'       => 'sd_contact_page',
                'type'     => 'select',
                'title'    => __('Contact Us', 'sd-for-wp'),
                'tooltip-subtitle' => __('This will apply the contact page schema on the selected page', 'sd-for-wp'),
                'data'     => 'page',
                'args'     => array(
                    'post_type' => 'page',
                    'posts_per_page' => 500
                ),
            ),
             
      )
    ) );

    // END General Settings Tab

//Knowledge Base
    Redux::setSection( $opt_name, array(
        'title'      => __( 'Knowledge Base', 'sd-for-wp' ),
       // 'desc'       => __( 'For full documentation on this field, visit: ', 'sd-for-wp' ) . '<a href="http://docs.reduxframework.com/core/fields/text/" target="_blank">http://docs.reduxframework.com/core/fields/text/</a>',
        'id'         => 'sd-knowledge-base',
        'subsection' => true,
        'fields'     => array(
           array(
                       'id' => 'ampforwp-homepage-section-general',
                       'type' => 'section',
                       'title' => __('Knowledge Base', 'sd-for-wp'),
                       'indent' => true,
                       'layout_type' => 'accordion',
                        'accordion-open'=> 1,
                ),

             array(
               'id'       => 'sd_kb_type',
               'type'     => 'select',
               'title'    => __( 'Data Type', 'sd-for-wp' ),
               'tooltip-subtitle' => __( 'Select your preferece structured data types', 'sd-for-wp' ),
               'options'  => array(
                   'Organization' => 'Organization',
                   'Person' => 'Person',
               ),
               'default'  => 'Organization'
           ),


        // Person Section Options

             array(
                'id'       => 'sd-person-name',
                'type'     => 'text',
                'title'    => __('Name', 'sd-for-wp'),
                'desc'    => __('Enter the person name', 'sd-for-wp'),
                'required'=>array('sd_kb_type','=','Person'),
            ),
             array(
                'id'       => 'sd-person-job-title',
                'type'     => 'text',
                'title'    => __('Job Title', 'sd-for-wp'),
                'desc'    => __('Enter the job title', 'sd-for-wp'),
                'required'=>array('sd_kb_type','=','Person'),

            ),
             array(
                'id'       => 'sd-person-image',
                'type'     => 'media',
                'url'      => true,
                'title'    => __('Image', 'sd-for-wp'),
                'desc'    => __('Upload the image', 'sd-for-wp'),
                'required'=>array('sd_kb_type','=','Person'),

            ),
             array(
                'id'       => 'sd-person-phone-number',
                'type'     => 'text',
                'title'    => __('Phone Number', 'sd-for-wp'),
                'desc'    => __('Enter the Phone Number', 'sd-for-wp'),
                'required'=>array('sd_kb_type','=','Person'),

            ),
             array(
                'id'       => 'sd-person-url',
                'type'     => 'text',
                'title'    => __('URL', 'sd-for-wp'),
                'desc'    => __('Enter the URL', 'sd-for-wp'),
                'required'=>array('sd_kb_type','=','Person'),

            ),

        // Organization Section Options


             array(
               'id'       => 'sd_name',
               'type'     => 'text',
               'title'    => __( 'Data Name', 'sd-for-wp' ),
               'tooltip-subtitle' => __( 'Enter your preferece structured data name', 'sd-for-wp' ),
               'required'=>array('sd_kb_type','=','Organization'),
               
           ),

             array(
               'id'       => 'sd_alt_name',
               'type'     => 'text',
               'title'    => __( 'Alternative Name', 'sd-for-wp' ),
               'tooltip-subtitle' => __( 'Enter your structured data alternative name', 'sd-for-wp' ),
               'required'=>array('sd_kb_type','=','Organization'),
               
           ),

             array(
               'id'       => 'sd_url',
               'type'     => 'text',
               'title'    => __( 'Data url', 'sd-for-wp' ),
               'tooltip-subtitle' => __( 'Enter the URL for structured data', 'sd-for-wp' ),
               'required'=>array('sd_kb_type','=','Organization'),
               
           ),

             array(
                'id'       => 'sd_logo',
                'type'     => 'media',
                'url'      => true,
                'title'    => __('Logo', 'sd-for-wp'),
                'tooltip-subtitle' => __('Upload a structured data logo.', 'sd-for-wp'),
                'required'=>array('sd_kb_type','=','Organization'),

            ),

            
              array(
               'id'       => 'sd_kb_contact_1',
               'type'     => 'switch',
               'title'    => __( 'Contact details', 'sd-for-wp' ),
               'tooltip-subtitle' => __( 'Enter the telephone details for structured data', 'sd-for-wp' ),
               'required'=>array('sd_kb_type','=','Organization'),
              'default'  =>'0'
           ),
              array(
               'id'       => 'sd_kb_telephone',
               'type'     => 'text',
               'title'    => __( 'Telephone Number', 'sd-for-wp' ),
               'tooltip-subtitle' => __( 'Enter the telephone details for structured data', 'sd-for-wp' ),
               'required'=>array('sd_kb_contact_1','=','1'),
               
           ),
              array(
               'id'       => 'sd_contact_type',
               'type'     => 'select',
               'title'    => __( 'Contact Type', 'sd-for-wp' ),
               'tooltip-subtitle' => __( 'Select the given contact types which best suits for your site', 'sd-for-wp' ),
               'options'  => array(
                   'customer support'   => 'Customer Support',
                   'technical support'  => 'Technical Support',
                   'billing support'    => 'Billing Support',
                   'bill payment'       => 'Bill payment',
                   'sales'              => 'Sales',
                   'reservations'       => 'Reservations',
                   'credit card support' => 'Credit Card Support',
                   'emergency'          => 'Emergency',
                   'baggage tracking'   => 'Baggage Tracking',
                   'roadside assistance' => 'Roadside Assistance',
                   'package tracking'   => 'Package Tracking',
               ),
               'required'=>array('sd_kb_contact_1','=','1'),
               
           ),
              array(
               'id'       => 'sd_social_profile',
               'type'     => 'section',
               'title'    => __( 'Social Profile', 'sd-for-wp' ),
               'indent' => true,

                'layout_type' => 'accordion',
                'accordion-open'=> 1,
                

              
               
           ),
              array(
               'id'       => 'sd_facebook',
               'type'     => 'text',
               'placeholder'=> 'https://',
               'title'    => __( 'Facebook', 'sd-for-wp' ),
               
               
           ),
              array(
               'id'       => 'sd_twitter',
               'type'     => 'text',
               'placeholder'=> 'https://',
               'title'    => __( 'Twitter', 'sd-for-wp' ),
               
               
           ),
              array(
               'id'       => 'sd_google_plus',
               'type'     => 'text',
               'placeholder'=> 'https://',
               'title'    => __( 'Google+', 'sd-for-wp' ),
               
               
           ),
              array(
               'id'       => 'sd_instagram',
               'type'     => 'text',
               'placeholder'=> 'https://',
               'title'    => __( 'Instagram', 'sd-for-wp' ),
               
               
           ),
              array(
               'id'       => 'sd_youtube',
               'type'     => 'text',
               'placeholder'=> 'https://',
               'title'    => __( 'Youtube', 'sd-for-wp' ),
               
               
           ),
              array(
               'id'       => 'sd_linkedin',
               'type'     => 'text',
               'placeholder'=> 'https://',
               'title'    => __( 'LinkedIn', 'sd-for-wp' ),
               
               
           ),
              array(
               'id'       => 'sd_pinterest',
               'type'     => 'text',
               'placeholder'=> 'https://',
               'title'    => __( 'Pinterest', 'sd-for-wp' ),
               
               
           ),
              array(
               'id'       => 'sd_soundcloud',
               'type'     => 'text',
               'placeholder'=> 'https://',
               'title'    => __( 'SoundCloud', 'sd-for-wp' ),
               
               
           ),
              array(
               'id'       => 'sd_tumblr',
               'type'     => 'text',
               'placeholder'=> 'https://',
               'title'    => __( 'Tumblr', 'sd-for-wp' ),
               
               
           ),

      )
    ) );//END
    // Schema type Section
Redux::setSection( $opt_name, array(
        'title'      => __( 'Schema Type', 'sd-for-wp' ),
        'id'         => 'sd-schema-type',
        'subsection' => true,
        'fields'     => array(
           array(
                       'id' => 'sd-schema-type',
                       'type' => 'section',
                       'title' => __('Schema & Structured Data', 'sd-for-wp'),
                       'indent' => true,
                       'layout_type' => 'accordion',
                       'accordion-open'=> 1,
            ),

           array(
               'id'       => 'sd_post_type',
               'type'     => 'select',
               'title'    => __( 'Post', 'sd-for-wp' ),
               'tooltip-subtitle' => __( 'Select the schema for structured data types', 'sd-for-wp' ),
               'options'  => array(
                   'Blogposting' => 'Blogposting',
                   'NewsArticle' => 'NewsArticle',
                   'WebPage'     => 'WebPage',
                   'Article'     => 'Article',
                   'Recipe'      => 'Recipe',
                   'Product'     => 'Product',
                   'VideoObject' => 'VideoObject'
               ),
               'default'  => $redux_builder_amp['ampforwp-sd-type-posts']
           ),
              
          array(
               'id'       => 'sd_page_type',
               'type'     => 'select',
               'title'    => __( 'Page', 'sd-for-wp' ),
               'tooltip-subtitle' => __( 'Select the type for which structured data needed', 'sd-for-wp' ),
               'options'  => array(
                   'Blogposting' => 'Blogposting',
                   'NewsArticle' => 'NewsArticle',
                   'WebPage'     => 'WebPage',
                   'Article'     => 'Article',
                   'Recipe'      => 'Recipe',
                   'Product'     => 'Product',
                   'VideoObject' => 'VideoObject'
               ),
               'default'  => $redux_builder_amp['ampforwp-sd-type-pages']
           ),
            array(
               'id'       => 'sd_default_section',
               'type'     => 'section',
               'title'    => __( 'Default vales setup', 'sd-for-wp' ),
               'indent' => true,
               'layout_type' => 'accordion',
               'accordion-open'=> 1, 

           ),
            array(
              'id'       => 'sd-data-logo-ampforwp',
              'type'     => 'media',
              'url'      => true,
              'title'    => __('Default Structured Data Logo', 'accelerated-mobile-pages'),
              'tooltip-subtitle' => __('Upload the logo you want to show in Google structured data. ', 'accelerated-mobile-pages'),
              'default' => $redux_builder_amp['amp-structured-data-logo'],
            ),
             array(
                'id'       => 'sd-logo-dimensions-ampforwp',
                'title'    => __('Custom Logo Size', 'accelerated-mobile-pages'),
                'type'     => 'switch',
                'default'  => $redux_builder_amp['ampforwp-sd-logo-dimensions']
            ),
             array(
                'id'       => 'sd-logo-width-ampforwp',
                'type'     => 'text',
                'title'    => __('Logo Width', 'accelerated-mobile-pages'),
                'desc'    => __('Default width is 600 pixels', 'accelerated-mobile-pages'),
                'default' => $redux_builder_amp['ampforwp-sd-logo-width'],
                'required'=>array('sd-logo-dimensions-ampforwp','=','1'),
            ),
             array(
                'id'       => 'sd-logo-height-ampforwp',
                'type'     => 'text',
                'title'    => __('Logo Height', 'accelerated-mobile-pages'),
                'desc'    => __('Default height is 60 pixels', 'accelerated-mobile-pages'),
                'default' => $redux_builder_amp['ampforwp-sd-logo-height'],
                'required'=>array('sd-logo-dimensions-ampforwp','=','1'),

            ),
            array(
                'id'       => 'sd_default_image',
                'type'     => 'media',
                'url'      => true,
                'title'    => __('Default Image', 'sd-for-wp'),
                'desc'    => __('Upload the Image you want to show as Placeholder Image.', 'sd-for-wp'),
               'placeholder'  => __('when there is no featured image set in the post','sd-for-wp'),      
               'default'  => $redux_builder_amp['amp-structured-data-placeholder-image'],
            ),
            array(
            'id'       => 'sd_default_image_width',
            'title'    => __('Default Post Image Width', 'sd-for-wp'),
            'type'     => 'text',
            'placeholder' => '550',
            'tooltip-subtitle' => __('Please don\'t add "PX" in the image size.','sd-for-wp'),
            'default'  => $redux_builder_amp['amp-structured-data-placeholder-image-width'],
            ),
            array(
              'id'       => 'sd_default_image_height',
              'title'    => __('Default Post Image Height', 'sd-for-wp'),
              'type'     => 'text',
              'placeholder' => '350',
              'tooltip-subtitle' => __('Please don\'t add "PX" in the image size.','sd-for-wp'),
              'default'  => $redux_builder_amp['amp-structured-data-placeholder-image-height'],
             ),
            array(
              'id'      => 'sd_default_video_thumbnail',
              'type'    => 'media',
              'url'     => true,
              'title'   => __('Default Thumbnail for VideoObject', 'sd-for-wp'),
              'tooltip-subtitle'    => __('Upload the thumbnail you want to show as video thumbnail.', 'sd-for-wp'),
              'placeholder'  => __('When there is no thumbnail set for the video','sd-for-wp'),
              'default'   => $redux_builder_amp['amporwp-structured-data-video-thumb-url'],
            ),
            array(
                'id'       => 'archive_schema',
                'title'    => __('Archive', 'sd-for-wp'),
                'tooltip-subtitle'    => __('Schema support for archive pages', 'sd-for-wp'),
                'type'     => 'switch',
                'default'  => 1,
            ),
            array(
                'id'       => 'breadcrumb_schema',
                'title'    => __('BreadCrumbs', 'sd-for-wp'),
                'tooltip-subtitle'    => __('Schema for breadCrumbs', 'sd-for-wp'),
                'type'     => 'switch',
                'default'  => 1,
            ),
    ),) );
  
/*
* <--- END SECTIONS*/

