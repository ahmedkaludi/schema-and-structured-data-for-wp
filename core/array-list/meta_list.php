<?php 
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

return apply_filters('saswp_modify_post_meta_list',
        array(
                    'text'  => array(                            
                            array(
                                    'label'     => __( 'Single Element', 'schema-and-structured-data-for-wp' ),
                                    'meta-list' => array(
                                            'blogname'          => __( 'Site Title', 'schema-and-structured-data-for-wp' ),
                                            'blogdescription'   => __( 'Tagline', 'schema-and-structured-data-for-wp' ),
                                            'site_url'          => __( 'Site URL', 'schema-and-structured-data-for-wp' ),
                                            'post_title'        => __( 'Title', 'schema-and-structured-data-for-wp' ),
                                            'post_content'      => __( 'Content', 'schema-and-structured-data-for-wp' ),
                                            'post_category'     => __( 'Category', 'schema-and-structured-data-for-wp' ),
                                            'post_excerpt'      => __( 'Excerpt', 'schema-and-structured-data-for-wp' ),
                                            'post_permalink'    => __( 'Permalink', 'schema-and-structured-data-for-wp' ),
                                            'author_name'       => __( 'Author Name', 'schema-and-structured-data-for-wp' ),
                                            'author_first_name' => __( 'Author First Name', 'schema-and-structured-data-for-wp' ),
                                            'author_last_name'  => __( 'Author Last Name', 'schema-and-structured-data-for-wp' ),
                                            'post_date'         => __( 'Publish Date', 'schema-and-structured-data-for-wp' ),
                                            'post_modified'     => __( 'Last Modify Date', 'schema-and-structured-data-for-wp' ),
                                    ),
                            ),
                            array(
                                    'label'     => __( 'Taxonomy Term', 'schema-and-structured-data-for-wp' ),
                                    'meta-list' => array(
                                            'taxonomy_term'  => __( 'Taxonomy Term', 'schema-and-structured-data-for-wp' ),                                            
                                    ),
                            ),
                            array(
                                    'label'     => __( 'Manual Field', 'schema-and-structured-data-for-wp' ),
                                    'meta-list' => array(
                                            'manual_text'  => __( 'Manual Text', 'schema-and-structured-data-for-wp' ),                                            
                                    ),
                            ),
                            array(
                                    'label'     => __( 'Custom Field', 'schema-and-structured-data-for-wp' ),
                                    'meta-list' => array(
                                            'custom_field' => __( 'Custom Field', 'schema-and-structured-data-for-wp' ),
                                    ),
                            ),
                    ),
                    'image' => array(
                            
                            array(
                                    'label'     => __( 'Single Element', 'schema-and-structured-data-for-wp' ),
                                    'meta-list' => array(
                                            'featured_img' => __( 'Featured image', 'schema-and-structured-data-for-wp' ),
                                            'author_image' => __( 'Author image', 'schema-and-structured-data-for-wp' ),
                                            'site_logo'    => __( 'Logo', 'schema-and-structured-data-for-wp' ),
                                    ),
                            ),
                            array(
                                    'label'     => __( 'Manual field', 'schema-and-structured-data-for-wp' ),
                                    'meta-list' => array(                                            
                                            'manual_text'   => __( 'Manual Image URL', 'schema-and-structured-data-for-wp' ),                                           
                                    ),
                            ),
                            array(
                                    'label'     => __( 'Custom Field', 'schema-and-structured-data-for-wp' ),
                                    'meta-list' => array(
                                            'fixed_image'  => __( 'Fixed Image', 'schema-and-structured-data-for-wp' ),
                                            'custom_field' => __( 'Custom Field', 'schema-and-structured-data-for-wp' ),
                                    ),
                            ),
                    ),
            )
 );