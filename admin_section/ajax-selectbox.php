<?php
/**
 * Ajax Selectbox Page
 *
 * @author   Magazine3
 * @category Admin
 * @path     admin_section/ajax-selectbox
 * @version 1.1
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * List of hooks used in this context
 */
add_action('wp_ajax_create_ajax_select_sdwp','saswp_ajax_select_creator');
add_action('wp_ajax_create_ajax_select_sdwp_taxonomy','saswp_create_ajax_select_taxonomy');


function saswp_ajax_select_creator($data = '', $saved_data= '', $current_number = '', $current_group_number ='') {
 
    $response = $data;
    $is_ajax = false;
    
    if( $_SERVER['REQUEST_METHOD']=='POST'){
        
        $is_ajax = true;
        
        if(wp_verify_nonce($_POST["saswp_call_nonce"],'saswp_select_action_nonce')){
            
            if ( isset( $_POST["id"] ) ) {
              $response = sanitize_text_field(wp_unslash($_POST["id"]));
            }
            if ( isset( $_POST["number"] ) ) {
              $current_number   = intval(sanitize_text_field($_POST["number"]));
            }
            if ( isset( $_POST["group_number"] ) ) {
              $current_group_number   = intval(sanitize_text_field($_POST["group_number"]));
            }
            
        }else{
            
            exit;
            
        }
       
    }          
        // send the response back to the front end
       // vars
    $choices = array();    
    
    $options['param'] = $response;
    // some case's have the same outcome
        if($options['param'] == "page_parent")
        {
          $options['param'] = "page";
        }
    
        switch($options['param'])
        {
          case "post_type":

            $choices = saswp_post_type_generator();
            
            $choices = apply_filters('saswp_modify_select_post_type', $choices );   
            
            unset($choices['saswp']);
            
            break;
        
         case "homepage":

            $choices = array(
                'true'  => 'True',
                'false' => 'False',                                
            ); 
             
            break;

          case "page":

            $post_type = 'page';
              
            $posts = get_posts(array(
              'posts_per_page'          =>  -1,
              'post_type'               => $post_type,
              'orderby'                 => 'menu_order title',
              'order'                   => 'ASC',
              'post_status'             => 'any',
              'suppress_filters'        => false,
              'update_post_meta_cache'  => false,
            ));

            if( $posts )
            {
              // sort into hierachial order!
              if( is_post_type_hierarchical( $post_type ) )
              {
                $posts = get_page_children( 0, $posts );
              }

              foreach( $posts as $page )
              {
                $title = '';
                $ancestors = get_ancestors($page->ID, 'page');
                if($ancestors)
                {
                  foreach($ancestors as $a)
                  {
                    $title .= '- ';
                  }
                }

                $title .= apply_filters( 'the_title', $page->post_title, $page->ID );                        
                // status
                if($page->post_status != "publish")
                {
                  $title .= " ($page->post_status)";
                }

                $choices[ $page->ID ] = $title;

              }
              // foreach($pages as $page)

            }

            break;

          case "page_template" :

            $choices = array(
              'default' =>  esc_html__('Default Template','schema-and-structured-data-for-wp'),
            );

            $templates = get_page_templates();
            
            if($templates){
                
                foreach($templates as $k => $v){
            
                     $choices[$v] = $k;
              
                }
                
            }
            

            break;

          case "post" :

            $post_types = get_post_types();

            unset( $post_types['page'], $post_types['attachment'], $post_types['revision'] , $post_types['nav_menu_item'], $post_types['acf'] , $post_types['amp_acf'],$post_types['saswp']  );

            if( $post_types )
            {
              foreach( $post_types as $post_type ){
              
                $posts = get_posts(array(
                    
                    'numberposts'      => '-1',
                    'post_type'        => $post_type,
                    'post_status'      => array('publish', 'private', 'draft', 'inherit', 'future'),
                    'suppress_filters' => false,
                    
                ));

                if( $posts){
                
                  $choices[$post_type] = array();

                  foreach($posts as $post){
                  
                    $title = apply_filters( 'the_title', $post->post_title, $post->ID );
                    // status
                    if($post->post_status != "publish"){
                    
                      $title .= " ($post->post_status)";
                    }

                    $choices[$post_type][$post->ID] = $title;

                  }
                  // foreach($posts as $post)
                }
                // if( $posts )
              }
              // foreach( $post_types as $post_type )
            }
            // if( $post_types )


            break;

          case "post_category" :

            $terms = get_terms( 'category', array( 'hide_empty' => false ) );

            if( !empty($terms) ) {

              foreach( $terms as $term ) {

                $choices[ $term->term_id ] = $term->name;

              }

            }

            break;

          case "post_format" :

            $choices = get_post_format_strings();

            break;

          case "user_type" :
              
            global $wp_roles;
              
            $choices = $wp_roles->get_names();

            if( is_multisite() ){
            
              $choices['super_admin'] = esc_html__('Super Admin','schema-and-structured-data-for-wp');
              
            }

            break;

          case "ef_taxonomy" :

            $choices    = array('all' => esc_html__('All','schema-and-structured-data-for-wp'));
            $taxonomies = saswp_post_taxonomy_generator();        
            $choices    = array_merge($choices, $taxonomies);                      
            
            break;

        }        
    // allow custom location rules
    $choices = $choices; 

    // Add None if no elements found in the current selected items
    if ( empty( $choices) ) {
      $choices = array('none' => esc_html__('No Items', 'schema-and-structured-data-for-wp') );
    }
         

      $output = '<select  class="widefat ajax-output" name="data_group_array[group-'.esc_attr($current_group_number).'][data_array]['. esc_attr($current_number) .'][key_3]">'; 

        // Generate Options for Posts
        if ( $options['param'] == 'post' ) {
            
          foreach ($choices as $choice_post_type) {
              
            foreach ($choice_post_type as $key => $value) { 
                
                if ( $saved_data ==  $key ) {
                    
                    $selected = 'selected="selected"';
                    
                } else {
                    
                  $selected = '';
                  
                }

                $output .= '<option '. esc_attr($selected) .' value="' .  esc_attr($key) .'"> ' .  esc_html__($value, 'schema-and-structured-data-for-wp') .'  </option>';            
            }
          }
         // Options for Other then posts
        } else {
            
          foreach ($choices as $key => $value) { 
              
                if ( $saved_data ==  $key ) {
                    
                    $selected = 'selected="selected"';
                    
                } else {
                    
                  $selected = '';
                  
                }

            $output .= '<option '. esc_attr($selected) .' value="' . esc_attr($key) .'"> ' .  esc_html__($value, 'schema-and-structured-data-for-wp') .'  </option>';            
          } 
        }
        
    $output .= ' </select> '; 
    $allowed_html = saswp_expanded_allowed_tags();
    echo wp_kses($output, $allowed_html); 
    
    if ( $is_ajax ) {
      die();
    }
// endif;  

}
/**
 * Function to Generate Proper Post Taxonomy for select and to add data.
 * @return type array
 * @since version 1.0
 */
function saswp_post_taxonomy_generator(){
    
    $taxonomies = '';  
    $choices    = array();
        
    $taxonomies = get_taxonomies( array('public' => true), 'objects' );
    
    if($taxonomies){
        
      foreach($taxonomies as $taxonomy) {
          
        $choices[ $taxonomy->name ] = $taxonomy->labels->name;
        
      }
        
    }
    
      // unset post_format (why is this a public taxonomy?)
      if( isset($choices['post_format']) ) {
          
        unset( $choices['post_format']) ;
        
      }
      
    return $choices;
}
/**
 * Function to create taxonomy
 * @param type $selectedParentValue
 * @param type $selectedValue
 * @param type $current_number
 * @param type $current_group_number
 * @since version 1.0
 */
function saswp_create_ajax_select_taxonomy($selectedParentValue = '',$selectedValue='', $current_number ='', $current_group_number  = ''){
    
    $is_ajax = false;
    
    if( $_SERVER['REQUEST_METHOD']=='POST'){
        
        $is_ajax = true;
        
        if(! current_user_can( saswp_current_user_can() ) ) {
          exit;
        }
        
        if(wp_verify_nonce($_POST["saswp_call_nonce"],'saswp_select_action_nonce')){
            
              if(isset($_POST['id'])){
                  
                $selectedParentValue = sanitize_text_field(wp_unslash($_POST['id']));
                
              }
              
              if(isset($_POST['number'])){
                  
                $current_number = intval(sanitize_text_field($_POST['number']));
                
              }
              
              if ( isset( $_POST["group_number"] ) ) {
                  
                $current_group_number   = intval(sanitize_text_field($_POST["group_number"]));
              
              }
              
        }else{
            
            exit;
            
        }       
    }
    $taxonomies = array(); 
    
    if($selectedParentValue == 'all'){
        
    $taxonomies =  get_terms( array(
                        'hide_empty' => true,
                    ) );   
    
    }else{
        
    $taxonomies =  get_terms($selectedParentValue, array(
                        'hide_empty' => true,
                    ) );    
    }     
    
    $choices = '<option value="all">'.esc_html__('All','schema-and-structured-data-for-wp').'</option>';
    
    if(!empty($taxonomies)){
        
        foreach($taxonomies as $taxonomy) {
        
        $sel="";
      
         if(is_object($taxonomy)){

            if($selectedValue == $taxonomy->slug){
            
              $sel = "selected";
            
            }
            $choices .= '<option value="'.esc_attr($taxonomy->slug).'" '.esc_attr($sel).'>'.esc_html__($taxonomy->name,'schema-and-structured-data-for-wp').'</option>';
            
         }         
      
    }
    
    $allowed_html = saswp_expanded_allowed_tags();  
    
    echo '<select  class="widefat ajax-output-child" name="data_group_array[group-'. esc_attr($current_group_number) .'][data_array]['.esc_attr($current_number).'][key_4]">'. wp_kses($choices, $allowed_html).'</select>';
        
    }    
    
    if($is_ajax){
      die;
    }
}