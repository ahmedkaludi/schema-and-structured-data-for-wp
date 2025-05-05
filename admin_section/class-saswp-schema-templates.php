<?php
/**
* Class: SASWP_Schema_Templates
* Create custom templates for reuse
* @author  Magazine3
* @since   1.39
* */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class SASWP_Schema_Templates {

    protected $template_type    = 'saswp_template';
    protected $template_label   = 'Schema Template';
    
    public function __construct(){
      
      if ( is_admin() && saswp_check_if_schema_builder_is_active() ) {

        add_action( 'init', array( $this, 'register_saswp_template' ) );
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes') ) ;
        add_filter( 'manage_saswp_template_posts_columns', array( $this, 'custom_columns' ) );
        add_action( 'manage_saswp_template_posts_custom_column' , array( $this, 'set_custom_columns' ), 10, 2 );
        add_action( 'save_post', array( $this, 'save_modify_meta_data' ) ) ;
        add_action( 'wp_ajax_saswp_get_schema_templates', array($this, 'get_get_schema_templates'));

      }

    }

    /**
     * Register custom saswp_template post type
     * @since   1.39
     * */
    public function register_saswp_template(  ){         
    
      $saswp_template = array(
            'labels' => array(
                'name'              => esc_html__( 'Schema Template', 'schema-and-structured-data-for-wp' ),
                'singular_name'     => esc_html__( 'Schema Template', 'schema-and-structured-data-for-wp' ),
                'add_new'           => esc_html__( 'Add New Template', 'schema-and-structured-data-for-wp' ),
                'add_new_item'      => esc_html__( 'Add New Template', 'schema-and-structured-data-for-wp' ),
                'edit_item'         => esc_html__( 'Edit Template', 'schema-and-structured-data-for-wp' ),           
                'all_items'         => esc_html__( 'Schema Templates', 'schema-and-structured-data-for-wp' ),   
           ),
          'public'              => true,
          'has_archive'         => false,
          'exclude_from_search' => true,
          'show_in_admin_bar'   => false,
          'publicly_queryable'  => false,
          'show_in_menu'        => 'edit.php?post_type=saswp',                
          'show_ui'             => true,
          'show_in_nav_menus'   => false,     
          'show_admin_column'   => true,        
          'rewrite'             => false,
          'supports'            => array( 'title' ),
          
      );    
    
      if(saswp_current_user_allowed() ) {        
        $cap = saswp_post_type_capabilities();
        if ( ! empty( $cap) ) {        
            $saswp_template['capabilities'] = $cap;         
        }
        
        register_post_type( 'saswp_template', $saswp_template);
      }
  
    }

    /**
     * Add Schema Types meta box
     * @since   1.39
     * */
    public function add_meta_boxes(){

      add_meta_box(
          'saswp_schema_template',
          esc_html__( 'Schema Template', 'schema-and-structured-data-for-wp' ),
          array( $this, 'saswp_schema_template_meta_box_callback' ),
          'saswp_template',
          'normal',
          'high',
          array( 'saswp_is_template_page' => 'yes' ),
      ); 

    }

    /**
     * Add custom column headings
     * @since   1.39
     * */
    public function custom_columns( $columns ){
      
      $title = $columns['title'];
      $cb    = $columns['cb'];
      unset($columns);
      $columns['cb']    = $cb;
      $columns['title'] = $title;
      $columns['saswp_template_type']       = '<a>'.esc_html__( 'Template Type', 'schema-and-structured-data-for-wp' ).'<a>';

      return $columns;

    }

    /**
     * Add custom column values
     * @since   1.39
     * */
    public function set_custom_columns( $column, $post_id ){
      
      switch ( $column ) {

        case 'saswp_template_type':
                    
            $schema_type = get_post_meta( $post_id, $key='schema_type', true);
             $url = admin_url( 'post.php?post='.$post_id.'&action=edit' );
            
            if($schema_type == 'local_business'){

              $business_type     = get_post_meta($post_id, 'saswp_business_type', true);
              $business_name     = get_post_meta($post_id, 'saswp_business_name', true);

              if($business_name){
                  echo '<strong><a class="row-title" href="'. esc_url( $url).'">LocalBusiness ('.esc_html( $business_name).')</a></strong>';   
              } elseif($business_type){
                  echo '<strong><a class="row-title" href="'. esc_url( $url).'">LocalBusiness ('.esc_html( $business_type).')</a></strong>';
              } else {
                  echo '<strong><a class="row-title" href="'. esc_url( $url).'">LocalBusiness</a></strong>';
              }
                
            }elseif($schema_type == 'qanda'){
                echo '<strong><a class="row-title" href="'. esc_url( $url).'">Q&A</a></strong>';
            }else{
                echo '<strong><a class="row-title" href="'. esc_url( $url).'">'.esc_html( $schema_type).'</a></strong>';
            }
                           
        break;

      }

    }

    /**
     * Callback function for template meta box
     * @param   $post   Array
     * @since   1.39
     * */
    public function saswp_schema_template_meta_box_callback( $post ){
      
      wp_nonce_field( 'saswp_schema_template_nonce', 'saswp_schema_template_nonce' );
      
      // Use the schema type function to save the data of template to eliminate the duplicate code
      saswp_schema_type_meta_box_callback( $post );

    }
    
    /**
     * Save modify template output meta data
     * @param   $post_id  integer
     * @since   1.39
     * */
    public function save_modify_meta_data( $post_id ){
      
      if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
      // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: Nonce verification done here so unslash is not used.
      if ( ! isset( $_POST['saswp_schema_template_nonce'] ) || ! wp_verify_nonce( $_POST['saswp_schema_template_nonce'], 'saswp_schema_template_nonce' ) ) return;
      if ( ! current_user_can( 'edit_post', $post_id ) ) return;

      $enable_custom_field  = '';                
      $fixed_text           = '';
      $taxonomy_term        = '';
      $fixed_image          = '';
      $cus_meta_field       = array();
      $meta_list            = array();
      
      if ( isset( $_POST['saswp_enable_custom_field'] ) )
            $enable_custom_field = sanitize_text_field( wp_unslash( $_POST['saswp_enable_custom_field'] ) );
      if ( isset( $_POST['saswp_modify_method'] ) )
            $saswp_modify_method = sanitize_text_field( wp_unslash( $_POST['saswp_modify_method'] ) );                
      if ( isset( $_POST['saswp_meta_list_val'] ) ) {
          // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash --Reason Server data is just used here so there is no necessary of unslash                    
          $meta_list = array_map ('sanitize_text_field', $_POST['saswp_meta_list_val']);
      }                
      if ( isset( $_POST['saswp_fixed_text'] ) ) { 
          // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash --Reason Server data is just used here so there is no necessary of unslash                   
          $fixed_text = array_map ('sanitize_text_field', $_POST['saswp_fixed_text']);
      }
      if ( isset( $_POST['saswp_taxonomy_term'] ) ) {
          // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash --Reason Server data is just used here so there is no necessary of unslash                    
          $taxonomy_term = array_map ('sanitize_text_field', $_POST['saswp_taxonomy_term']);
      }
      if ( isset( $_POST['saswp_custom_meta_field'] ) ) {
          // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash --Reason Server data is just used here so there is no necessary of unslash                    
          $cus_meta_field = array_map ('sanitize_text_field', $_POST['saswp_custom_meta_field']);
      }
      if ( isset( $_POST['saswp_fixed_image'] ) ) {
          // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized --Reason Server data is just used here so there is no necessary of unslash                    
          $fixed_image = wp_unslash($_POST['saswp_fixed_image']);
      }
      
       $saswp_schema_options  =    array(
                                      'enable_custom_field'   => $enable_custom_field,
                                      'saswp_modify_method'   => $saswp_modify_method
                                  );   
                                  
       update_post_meta( $post_id, 'schema_options', $saswp_schema_options);                 
       update_post_meta( $post_id, 'saswp_meta_list_val', $meta_list);
       update_post_meta( $post_id, 'saswp_fixed_text', $fixed_text);
       update_post_meta( $post_id, 'saswp_taxonomy_term', $taxonomy_term);
       update_post_meta( $post_id, 'saswp_fixed_image', $fixed_image);
       update_post_meta( $post_id, 'saswp_custom_meta_field', $cus_meta_field);

    }
    
    /**
     * Add template posts to meta list
     * @param   $fields   Array
     * @return  $fields   Array
     * @since   1.39
     * */
    public function get_get_schema_templates(){

      if ( ! isset( $_POST['saswp_security_nonce'] ) ){
        return; 
      }
      // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: Nonce verification done here so unslash is not used.
      if ( !wp_verify_nonce( $_POST['saswp_security_nonce'], 'saswp_ajax_check_nonce' ) ){
        return;  
      }
      if(!current_user_can( saswp_current_user_can()) ) {
        die( '-1' );    
      }
      
      $template_list    = array();
      $result           = array();

      $args = array(
          'post_type'      => $this->template_type,
          'posts_per_page' => -1,
          'post_status'    => 'publish',
      );

      $query = new WP_Query($args);

      if ($query->have_posts()) {
      
          while ($query->have_posts()) {
            
            $query->the_post();
            $template_list[]  = array(
                                  'id'    => get_the_ID(),
                                  'text'  => get_the_title(),
                                );

          }

          wp_reset_postdata();
      }

      if ( ! empty( $template_list ) ) {
        $result[] = array(
          'children' => $template_list,
        );
      }

      wp_send_json( $result );            
            
      wp_die();

    }
}
  
new SASWP_Schema_Templates();