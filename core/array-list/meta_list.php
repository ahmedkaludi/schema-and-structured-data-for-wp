<?php 
return array(
                    'text'  => array(                            
                            array(
                                    'label'     => __( 'Single Elements', 'wp-schema-pro' ),
                                    'meta-list' => array(
                                            'blogname'         => __( 'Site Title', 'wp-schema-pro' ),
                                            'blogdescription'   => __( 'Tagline', 'wp-schema-pro' ),
                                            'site_url'          => __( 'Site URL', 'wp-schema-pro' ),
                                            'post_title'        => __( 'Title', 'wp-schema-pro' ),
                                            'post_content'      => __( 'Content', 'wp-schema-pro' ),
                                            'post_excerpt'      => __( 'Excerpt', 'wp-schema-pro' ),
                                            'post_permalink'    => __( 'Permalink', 'wp-schema-pro' ),
                                            'author_name'       => __( 'Author name', 'wp-schema-pro' ),
                                            'author_first_name' => __( 'Author first name', 'wp-schema-pro' ),
                                            'author_last_name'  => __( 'Author last name', 'wp-schema-pro' ),
                                            'post_date'         => __( 'Publish Date', 'wp-schema-pro' ),
                                            'post_modified'     => __( 'Last Modify Date', 'wp-schema-pro' ),
                                    ),
                            ),
                            array(
                                    'label'     => __( 'Manual Fields', 'wp-schema-pro' ),
                                    'meta-list' => array(
                                            'custom-text'  => __( 'Fixed Text', 'wp-schema-pro' ),                                            
                                    ),
                            ),
                            array(
                                    'label'     => __( 'Custom Fields', 'wp-schema-pro' ),
                                    'meta-list' => array(
                                            'specific-field' => __( 'Select Other Custom Fields Here', 'wp-schema-pro' ),
                                    ),
                            ),
                    ),
                    'image' => array(
                            
                            array(
                                    'label'     => __( 'Single Elements', 'wp-schema-pro' ),
                                    'meta-list' => array(
                                            'featured_img' => __( 'Featured image', 'wp-schema-pro' ),
                                            'author_image' => __( 'Author image', 'wp-schema-pro' ),
                                            'site_logo' => __( 'Logo', 'wp-schema-pro' ),
                                    ),
                            ),
                            array(
                                    'label'     => __( 'Manual fields', 'wp-schema-pro' ),
                                    'meta-list' => array(
                                            'custom-text'  => __( 'Fixed Image', 'wp-schema-pro' ),
                                            'fixed-text'   => __( 'Image URL', 'wp-schema-pro' ),                                           
                                    ),
                            ),
                            array(
                                    'label'     => __( 'Custom Fields', 'wp-schema-pro' ),
                                    'meta-list' => array(
                                            'specific-field' => __( 'Select Other Custom Fields Here', 'wp-schema-pro' ),
                                    ),
                            ),
                    ),
            );