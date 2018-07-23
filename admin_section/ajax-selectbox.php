<?php
add_action('wp_ajax_create_ajax_select_sdwp','amp_sdwp_ajax_select_creator');
function amp_sdwp_ajax_select_creator($data = '', $saved_data= '', $current_number = '') {
 
    //$response         = $_POST["id"];
 //   $current_number   = $_POST["number"];
      $response         = $data;

      if ( isset( $_POST["id"] ) ) {
        $response = $_POST["id"];
      }

      if ( isset( $_POST["number"] ) ) {
        $current_number   = $_POST["number"];
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

        $choices = amp_sdwp_post_type_generator();

        $choices = apply_filters('amp_acf_modify_select_post_type', $choices );
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
      
      // Page Type Deactivated the support for now
      // case "page_type" :
        
      //   $choices = array(
      //     'front_page'  =>  __("Front Page"),
      //     'posts_page'  =>  __("Posts Page"),
      //     'top_level'   =>  __("Top Level Page (parent of 0)"),
      //     'parent'    =>  __("Parent Page (has children)"),
      //     'child'     =>  __("Child Page (has parent)"),
      //   );
                
      //   break;
        
      case "page_template" :
        
        $choices = array(
          'default' =>  esc_attr__('Default Template','amp-acf'),
        );
        
        $templates = get_page_templates();
        foreach($templates as $k => $v)
        {
          $choices[$v] = $k;
        }
        
        break;
      
      case "post" :
        
        $post_types = get_post_types();
        
        unset( $post_types['page'], $post_types['attachment'], $post_types['revision'] , $post_types['nav_menu_item'], $post_types['acf'] , $post_types['amp_acf']  );
        
        if( $post_types )
        {
          foreach( $post_types as $post_type )
          {
            
            $posts = get_posts(array(
              'numberposts' => '-1',
              'post_type' => $post_type,
              'post_status' => array('publish', 'private', 'draft', 'inherit', 'future'),
              'suppress_filters' => false,
            ));
            
            if( $posts)
            {
              $choices[$post_type] = array();
              
              foreach($posts as $post)
              {
                $title = apply_filters( 'the_title', $post->post_title, $post->ID );
                
                // status
                if($post->post_status != "publish")
                {
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
      // Post Status Deactivated the support for now
      // case "post_status" :
        
      //   $choices = array(
      //     'publish' => __( 'Published'),
      //     'pending' => __( 'Pending Review'),
      //     'draft'   => __( 'Draft'),
      //     'future'  => __( 'Future'),
      //     'private' => __( 'Private'),
      //     'inherit' => __( 'Revision'),
      //     'trash'   => __( 'Trash'),
      //   );
                
      //   break;
      
      case "user_type" :
       global $wp_roles;
        $choices = $wp_roles->get_names();

        if( is_multisite() )
        {
          $choices['super_admin'] = esc_attr__('Super Admin','amp-acf');
        }
                
        break;
      // Post Taxonomy Deactivated the support for now
      // case "taxonomy" :
        
      //   $choices = array();
      //   $simple_value = true;
      //   $choices = apply_filters('acf/get_taxonomies_for_select', $choices, $simple_value);
                
      //   break;
      
      case "ef_taxonomy" :
        
        $choices = array('all' => esc_attr__('All','structured-data-wp'));
        $taxonomies = amp_sdwf_post_taxonomy_generator();
        
        $choices = array_merge($choices, $taxonomies);
      
                
        break;
      // Users Deactivated the support for now
      // case "ef_user" :
        
      //   global $wp_roles;
        
      //   $choices = array_merge( array('all' => __('All')), $wp_roles->get_names() );
      
      //   break;
        
      // Attachment Pages Deactivated the support for now  
      // case "ef_media" :
        
      //   $choices = array('all' => __('All'));
      
      //   break;
        
    }
    
    
    // allow custom location rules
    $choices = $choices; 

    // Add None if no elements found in the current selected items
    if ( empty( $choices) ) {
      $choices = array('none' => esc_attr__('No Items', 'amp-acf') );
    }
     //  echo $current_number;
    // echo $saved_data;

      $output = '<select  class="widefat ajax-output" name="data_array['. $current_number .'][key_3]">'; 

        // Generate Options for Posts
        if ( $options['param'] == 'post' ) {
          foreach ($choices as $choice_post_type) {      
            foreach ($choice_post_type as $key => $value) { 
                if ( $saved_data ==  $key ) {
                    $selected = 'selected="selected"';
                } else {
                  $selected = '';
                }

                $output .= '<option '. $selected .' value="' .  $key .'"> ' .  $value .'  </option>';            
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

            $output .= '<option '. $selected .' value="' .  $key .'"> ' .  $value .'  </option>';            
          } 
        }
      $output .= ' </select> '; 

    echo $output; 

    
    if ( isset( $_POST['id'] )) {
      die();
    }
// endif;  

}
// Generate Proper Post Taxonomy for select and to add data.
function amp_sdwf_post_taxonomy_generator(){
    $taxonomies = '';  
    $choices    = '';
      $taxonomies = get_taxonomies( array('public' => true), 'objects' );

      foreach($taxonomies as $taxonomy) {
        $choices[ $taxonomy->name ] = $taxonomy->labels->name;
      }

      // unset post_format (why is this a public taxonomy?)
      if( isset($choices['post_format']) ) {
        unset( $choices['post_format']) ;
      }

    return $choices;
}
add_action('wp_ajax_create_ajax_select_sdwp_taxonomy','create_ajax_select_sdwp_taxonomy');
function create_ajax_select_sdwp_taxonomy($selectedParentValue = '',$selectedValue, $current_number =''){
  if(isset($_POST['id'])){
    $selectedParentValue = $_POST['id'];
  }
  if(isset($_POST['number'])){
    $current_number = $_POST['number'];
  }
  $taxonomies =  get_terms( $selectedParentValue, array(
                      'hide_empty' => true,
                  ) );
   $choices = '<option value="all">'.esc_attr__('All','structured-data-wp').'</option>';
  foreach($taxonomies as $taxonomy) {
    $sel="";
    if($selectedValue == $taxonomy->slug){
      $sel = "selected";
    }
    $choices .= '<option value="'.$taxonomy->slug.'" '.$sel.'>'.$taxonomy->name.'</option>';
  }
  echo '<select  class="widefat ajax-output-child" name="data_array['. $current_number .'][key_4]">'. $choices.'</select>';
  if(isset($_POST['id'])){
    die;
  }
}