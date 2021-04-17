<?php
/**
 * Structure admin page
 *
 * @author   Magazine3
 * @category Admin
 * @path     admin_section/structure_admin
 * @version 1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

function saswp_skip_wizard(){                  
    
        if ( ! isset( $_POST['saswp_security_nonce'] ) ){
           return; 
        }
        if ( !wp_verify_nonce( $_POST['saswp_security_nonce'], 'saswp_ajax_check_nonce' ) ){
           return;  
        }    
         
        $sd_data = get_option('sd_data');
        $sd_data['sd_initial_wizard_status'] = 0;
        update_option('sd_data', $sd_data);
        
        wp_die();           
}

add_action('wp_ajax_saswp_skip_wizard', 'saswp_skip_wizard');


add_action ( 'save_post' , 'saswp_delete_post_transient' );

function saswp_delete_post_transient( $post_id ){
        
     delete_transient('saswp_imageobject_' .$post_id);
    
}

function saswp_get_saved_schema_ids(){

    global $all_schemas;
    $schema_ids = array();

    if(!$all_schemas){

      $all_schemas = get_posts(
        array(
                'post_type' 	 => 'saswp',
                'posts_per_page'     => -1,   
                'post_status'        => 'publish',
        )
     );   

    }

    if($all_schemas){
      
      foreach($all_schemas as $schema){
         
        $schema_ids[] = $schema->ID;
        
      }
      
    }    
    
    return $schema_ids;

}

/*
 *      Storing and updating all ads post ids in transient on different actions 
 *      which we will fetch all ids from here to display our post
 */    
function saswp_published(){
    
        $schema_post_ids = saswp_get_saved_schema_ids();
        
        if($schema_post_ids){
            
            $schema_id_json = json_encode($schema_post_ids);
            set_transient('saswp_transient_schema_ids', $schema_id_json);  
            
        }
        
        
}

function saswp_update_ids_on_trash(){
    
     delete_transient('saswp_transient_schema_ids');
     saswp_published();      
     
}

function saswp_update_ids_on_untrash(){  
    
     saswp_published();    
     
}

add_action( 'publish_saswp', 'saswp_published' );
add_action( 'trash_saswp', 'saswp_update_ids_on_trash' );    
add_action( 'untrash_saswp', 'saswp_update_ids_on_untrash' );

function saswp_reset_all_settings(){   
    
        if ( ! current_user_can( saswp_current_user_can() ) ) {
             return;
        }
        
        if ( ! isset( $_POST['saswp_security_nonce'] ) ){
           return; 
        }
        if ( !wp_verify_nonce( $_POST['saswp_security_nonce'], 'saswp_ajax_check_nonce' ) ){
           return;  
        }
        
        $result = '';
        
        update_option( 'sd_data', array());  
        
        $allposts= get_posts( array('post_type'=>'saswp','numberposts'=>-1) );
        
        foreach ($allposts as $eachpost) {
            
            $result = wp_delete_post( $eachpost->ID, true );
        
        }
                        
        if($result){
            echo json_encode(array('status'=>'t'));            
        }else{
            echo json_encode(array('status'=>'f'));            
        }        
           wp_die();           
}

add_action('wp_ajax_saswp_reset_all_settings', 'saswp_reset_all_settings');

function saswp_load_plugin_textdomain() {
    
    load_plugin_textdomain( 'schema-and-structured-data-for-wp', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
    
}
add_action( 'plugins_loaded', 'saswp_load_plugin_textdomain' );



function saswp_check_advance_display_status($post_id, $post){
              
          $unique_checker = '';
          $resultset = saswp_generate_field_data( $post_id, $post );
          
          if($resultset){
              
          $condition_array = array();    
              
          foreach ($resultset as $result){
              
            if(is_array($result)){
                $data             = array_filter($result);
                $number_of_fields = count($data);
                $checker          = 0;
                
                if ( $number_of_fields > 0 ) {
                  
                  $checker = count( array_unique($data) );
                  
                  $array_is_false =  in_array(false, $result);
                  
                  if (  $array_is_false ) {
                      
                    $checker = 0;
                    
                  }
                  
                }  
                
                $condition_array[] = $checker;
            }
          
          }          
            $array_is_true = in_array(true,$condition_array);
          
          if($array_is_true){
              
             $unique_checker = 1;    
          
          }
          
          }else{
              $unique_checker = 'notset';
          }
    
          return $unique_checker;
    
}

function saswp_get_all_schema_posts(){
    global $post;
    $schema_id_array = array();

    $schema_id_array = json_decode(get_transient('saswp_transient_schema_ids'), true); 
    
    
    if(!$schema_id_array){
        
       $schema_id_array = saswp_get_saved_schema_ids();
        
    }         
       
    if($schema_id_array){
     
     if(count($schema_id_array)>0){    
        
      $returnData = array();
      
      foreach ($schema_id_array as $post_id){ 
        
          $unique_checker = saswp_check_advance_display_status($post_id, $post);
                                        
          if ( $unique_checker === 1 || $unique_checker === true || $unique_checker == 'notset') {
              
              $conditions = array();
              
              $data_group_array = get_post_meta( $post_id, 'data_group_array', true);                                         
              
              if(isset($data_group_array['group-0'])){
                  
                 $conditions = $data_group_array['group-0']['data_array'];                  
                 
              }
              if(isset($conditions[0])){
                  
                $conditions = $conditions[0];    
              
              }
              
              if(empty($conditions)){
                  
                 $conditions['key_1'] = 'post_type';
                 $conditions['key_2'] = 'equal';
                 $conditions['key_3'] = 'post';
                 
              }
              
              $returnData[] = array(
                    'schema_type'      => get_post_meta( $post_id, 'schema_type', true),
                    'schema_options'   => get_post_meta( $post_id, 'schema_options', true),
                    'conditions'       => $conditions,
                    'post_id'          => $post_id,
                  );
              
            }
            
      }
      
      return $returnData;      
    }
        
    }                                
  
   return false;
}

function saswp_generate_field_data( $post_id, $post ){
    
      $data_group_array = get_post_meta( $post_id, 'data_group_array', true);  
      
      $output = array();
      
      if(!empty($data_group_array) && is_array($data_group_array)){ 
          
        foreach ($data_group_array as $group){

          if(is_array($group['data_array'])){
            
            $inner_output = array();

            foreach($group['data_array'] as $value){
              $inner_output[] = saswp_comparison_logic_checker($value, $post); 
            }
            $output[] = $inner_output;            
          }
           
        }   
      
      } 
      
      return $output;
      
}

function saswp_comparison_logic_checker($input, $post){
            
        $type       = isset($input['key_1']) ? $input['key_1'] : '';
        $comparison = isset($input['key_2']) ? $input['key_2'] : '';
        $data       = isset($input['key_3']) ? $input['key_3'] : '';
        $result     = ''; 
       
        // Get all the users registered
        $user       = null;

        if( function_exists('wp_get_current_user') ){
            $user       = wp_get_current_user();
        }        

        switch ($type) {
            
        case 'show_globally':  
              
               $result = true;      
              
          break;
        
        case 'date':  
              
          $published_date ='';  
          
          if(is_singular() || is_admin()){
             $published_date = get_the_date('Y-m-d');  
          }
           
         if ( $comparison == 'before_published' ) {
             if ( $published_date <= $data ) {
               $result = true;
             }
         }
         if ( $comparison == 'after_published') {              
             if ( $published_date >= $data ) {
               $result = true;
             }
         }
           
        break;  
        // Basic Controls ------------ 
          // Posts Type
        case 'post_type':   
              
                  $current_post_type = '';
              
                  if( (is_singular() || is_admin()) && is_object($post) && !is_front_page()){
                      
                     $current_post_type  = get_post_type($post->ID);   
                     
                  } 
                                                      
                  if ( $comparison == 'equal' ) {
                      
                  if ( $current_post_type == $data ) {
                      
                    $result = true;
                    
                  }
                  
              }
              if ( $comparison == 'not_equal') {              
                  if ( $current_post_type != $data ) {
                    $result = true;
                  }
              }            
          break;
          
          
          // Posts
        case 'homepage':    
          
            $homepage ='false';  
          
            if(is_home() || is_front_page() || ( function_exists('ampforwp_is_home') && ampforwp_is_home()) ){
               $homepage = 'true';  
            }
                      
            if ( $comparison == 'equal' ) {
                if ( $homepage == $data ) {
                  $result = true;
                }
            }
            if ( $comparison == 'not_equal') {              
                if ( $homepage != $data ) {
                  $result = true;
                }
            }

        break;

      // Logged in User Type
        case 'user_type':            
            if ( $comparison == 'equal') {
              
                if(is_object($user)){

                  if ( in_array( $data, (array) $user->roles ) ) {
                    $result = true;
                  }

                }
                
            }            
            if ( $comparison == 'not_equal') {
                
                require_once ABSPATH . 'wp-admin/includes/user.php';
                // Get all the registered user roles
                $roles = get_editable_roles();                
                $all_user_types = array();
                foreach ($roles as $key => $value) {
                  $all_user_types[] = $key;
                }
                // Flip the array so we can remove the user that is selected from the dropdown
                $all_user_types = array_flip( $all_user_types );

                // User Removed
                unset( $all_user_types[$data] );

                // Check and make the result true that user is not found 
                if ( in_array( $data, (array) $all_user_types ) ) {
                    $result = true;
                }
            }
            
           break; 

    // Post Controls  ------------ 
      // Posts
        case 'post':    
          
            $current_post ='';  
          
             if(is_singular() || is_admin()){
                $current_post = $post->ID;  
             }
                      
            if ( $comparison == 'equal' ) {
                if ( $current_post == $data ) {
                  $result = true;
                }
            }
            if ( $comparison == 'not_equal') {              
                if ( $current_post != $data ) {
                  $result = true;
                }
            }

        break;

      // Post Category
        case 'post_category':

          global $cat_id_obj;
          $cat_id_arr = array();          
          
          if(is_object($post)){

              if(!$cat_id_obj){
                $cat_id_obj = get_the_category( $post->ID );
              }
                            
              if($cat_id_obj){

                foreach ($cat_id_obj as $value) {
                  $cat_id_arr[] = $value->term_id;
                }

              }                              
                            
          }
          
          if ( $comparison == 'equal') {
              if (in_array($data, $cat_id_arr) ) {
                  $result = true;
              }
          }
          
          if ( $comparison == 'not_equal') {
            if (!in_array($data, $cat_id_arr) ) {
              $result = true;
            }
          }
        break;
      // Post Format
        case 'post_format':
          
          $current_post_format = '';
          
          if(is_object($post)){
          
              $current_post_format = get_post_format( $post->ID );
              
          }
                    
          if ( $current_post_format === false ) {
              $current_post_format = 'standard';
          }
          if ( $comparison == 'equal') {
              if ( $data == $current_post_format ) {
                  $result = true;
              }
          }
          if ( $comparison == 'not_equal') {
              if ( $data != $current_post_format ) {
                  $result = true;
              }
          }
        break;

    // Page Controls ---------------- 
      // Page
        case 'page': 
          
        global $redux_builder_amp;
          
        $current_post = '';
        
        if(function_exists('ampforwp_is_front_page')){
            
          if(ampforwp_is_front_page()){
              
                $current_post = $redux_builder_amp['amp-frontpage-select-option-pages'];  
          
          } else{
              
                if(is_object($post)){
                    $current_post = $post->ID;   
                }
                          
          }           
        }else{
                if(is_object($post)){
                    $current_post = $post->ID;   
                }
        }
        
            if ( $comparison == 'equal' ) {
                if ( $current_post == $data ) {
                  $result = true;
                }
            }
            if ( $comparison == 'not_equal') {              
                if ( $current_post != $data ) {
                  $result = true;
                }
            }
        break;

      // Page Template 
        case 'page_template':
          
            $page_template = '';
                      
            if(is_object($post)){
             
              $page_template = get_page_template_slug( $post->ID );
                
            }                        
                                              
            if ( $page_template == false ) {
              $page_template = 'default';
            }

            $page_template = strtolower($page_template);
            $page_template = str_replace('.php','',$page_template);
            $data          = strtolower($data);
            $data          = str_replace('.php','',$data);
            
            if ( $comparison == 'equal' ) {
                if ( $page_template == $data ) {
                    $result = true;
                }
            }
            if ( $comparison == 'not_equal') {              
                if ( $page_template != $data ) {
                    $result = true;
                }
            }

        break; 

    // Other Controls ---------------
      // Taxonomy Term
        case 'ef_taxonomy':
        // Get all the post registered taxonomies        
        // Get the list of all the taxonomies associated with current post
        $taxonomy_names = '';

        if(is_object($post)){
          $taxonomy_names = get_post_taxonomies( $post->ID );        
        }
        
        $checker    = '';
        $post_terms = '';

          if ( $data != 'all') {

            if(is_object($post)){
              $post_terms = wp_get_post_terms($post->ID, $data);           
            }
                                            
            if(isset( $input['key_4'] ) && $input['key_4'] !='all'){
             
              $term_data       = $input['key_4'];
              $termChoices     = array();

              if(is_tax()){

                $queried_obj   = get_queried_object();
                $termChoices[] = $queried_obj->slug;

              }else{

                if( is_object($post) ) {

                  $terms           = wp_get_post_terms( $post->ID ,$data);
                
                  if(count($terms)>0){
                                                      
                    foreach ($terms as $key => $termvalue) {
                        
                      $termChoices[] = $termvalue->slug;
                      
                    } 
                    
                  }

                }                

              }
                                                                      
              
            if ( $comparison == 'equal' ) {
              if(in_array($term_data, $termChoices)){
                $result = true;
              }
            }

            if ( $comparison == 'not_equal') { 
              if(!in_array($term_data, $termChoices)){
                $result = true;
              }
            }

            }else{
              
              if($input['key_4'] == 'all'){
              
                if ( $comparison == 'equal' ) {
                  if ( $post_terms ) {
                      $result = true;
                  }
                }

                if ( $comparison == 'not_equal') { 
                  if ( !$post_terms ) {
                    $result = true;
                  }
                }

              }else{

                if ( $comparison == 'equal' ) {
                  if ( $post_terms ) {
                      $result = true;
                  }
                }

                if ( $comparison == 'not_equal') { 
                    $checker =  in_array($data, $taxonomy_names);       
                    if ( ! $checker ) {
                        $result = true;
                    }
                }

              }
                
            }

          } else {

            if ( $comparison == 'equal' ) {
              if ( $taxonomy_names ) {                
                  $result = true;
              }
            }

            if ( $comparison == 'not_equal') { 
              if ( ! $taxonomy_names ) {                
                  $result = true;
              }
            }

          }
        break;
      
      default:
        $result = false;
        break;
    }

    return $result;
}


  require_once( untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/ajax-selectbox.php' );
//Back End
if(is_admin()){
         
  add_action( 'init', 'saswp_create_post_type' );
  
  function saswp_create_post_type() {
      
    $nonce = wp_create_nonce( 'saswp_install_wizard_nonce' );      
    $not_found_button = '<div><span class="dashicons dashicons-thumbs-up"></span>'.saswp_t_string("Thank you for using Schema & Structured Data For WP plugin!").' <a href="'.esc_url(admin_url( 'plugins.php?page=saswp-setup-wizard' ).'&_saswp_nonce='.$nonce).'">'.saswp_t_string("Start Quick Setup?").'</a></div>';       
    
    $saswp = array(
            'labels' => array(
                'name'              => saswp_t_string( 'Structured Data' ),
                'singular_name'     => saswp_t_string( 'Structured Data' ),
                'add_new' 	        => saswp_t_string( 'Add Schema Type' ),
                'add_new_item'      => '',
                'edit_item'         => saswp_t_string( 'Edit Schema Type'),           
                'all_items'         => saswp_t_string( 'Schema Types' ),  
                'not_found'         => $not_found_button    
           ),
          'public'                => true,
          'has_archive'           => false,
          'exclude_from_search'   => true,
          'publicly_queryable'    => false,
          'show_in_admin_bar'     => false,
          'supports'              => array('title'),  
          'menu_position'         => 100
          
      );    
    
    if(saswp_current_user_allowed()){        
        
        $cap = saswp_post_type_capabilities();

        if(!empty($cap)){        
            $saswp['capabilities'] = $cap;         
        }
        
        register_post_type( 'saswp', $saswp);
    }
    
  } 
  
  function saswp_select_callback($post) {
    
    $data_group_array =  get_post_meta($post->ID, 'data_group_array', true );     
                
    $data_group_array = is_array($data_group_array)? array_values($data_group_array): array();  
    
    if ( empty( $data_group_array ) ) {
        
               $data_group_array[0] =array(
                   
                   'data_array' => array(
                       
                            array(
                                
                            'key_1' => 'post_type',
                            'key_2' => 'equal',
                            'key_3' => 'none',
                                
                            )
                       
               )    
                   
      );
    }
    //security check
    wp_nonce_field( 'saswp_select_action_nonce', 'saswp_select_name_nonce' );?>

    <?php 
    // Type Select    
      $choices = array(
        saswp_t_string("Basic") => array(        
          'post_type'           =>  saswp_t_string("Post Type"),
          'show_globally'       =>  saswp_t_string("Show Globally"),    
          'user_type'           =>  saswp_t_string("Logged in User Type"),
          'homepage'            =>  saswp_t_string("Homepage"),  
        ),
        saswp_t_string("Post") => array(
          'post'                =>  saswp_t_string("Post"),
          'post_category'       =>  saswp_t_string("Post Category"),
          'post_format'         =>  saswp_t_string("Post Format"), 
        ),
        saswp_t_string("Page") => array(
          'page'                =>  saswp_t_string("Page"), 
          'page_template'       =>  saswp_t_string("Page Template"),
        ),
        saswp_t_string("Other") => array( 
          'ef_taxonomy'         =>  saswp_t_string("Taxonomy (Tag)"), 
          'date'                =>  saswp_t_string("Date"), 

        )
      ); 

      $comparison = array(
        'equal'                =>  saswp_t_string( 'Equal to'), 
        'not_equal'            =>  saswp_t_string( 'Not Equal to (Exclude)'),     
      );

      $total_group_fields = count( $data_group_array ); ?>
<div class="saswp-placement-groups">
    
    <?php for ($j=0; $j < $total_group_fields; $j++) {
        
        $data_array = $data_group_array[$j]['data_array'];
        
        $total_fields = count( $data_array );
        ?>
    <div class="saswp-placement-group" name="data_group_array[<?php echo esc_attr( $j) ?>]" data-id="<?php echo esc_attr($j); ?>">           
     <?php 
     if($j>0){
     echo '<span style="margin-left:10px;font-weight:600">Or</span>';    
     }     
     ?>   
     <table class="widefat saswp-placement-table" style="border:0px;">
        <tbody id="sdwp-repeater-tbody" class="fields-wrapper-1">
        <?php  for ($i=0; $i < $total_fields; $i++) {  
            
            
          $selected_val_key_1 = isset($data_array[$i]['key_1']) ? $data_array[$i]['key_1'] : '';           
          $selected_val_key_2 = isset($data_array[$i]['key_2']) ? $data_array[$i]['key_2'] : '';           
          $selected_val_key_3 = isset($data_array[$i]['key_3']) ? $data_array[$i]['key_3'] : '';          
          $selected_val_key_4 = '';
          if(isset($data_array[$i]['key_4'])){
            $selected_val_key_4 = $data_array[$i]['key_4'];
          }

          if($selected_val_key_1 == 'date'){
            $comparison = array(
              'before_published'           =>  saswp_t_string( 'Before Published'), 
              'after_published'            =>  saswp_t_string( 'After Published'),     
            );
          }else{
            $comparison = array(
              'equal'                =>  saswp_t_string( 'Equal to'), 
              'not_equal'            =>  saswp_t_string( 'Not Equal to (Exclude)'),     
            );    
          }

          ?>
          <tr class="toclone">
            <td style="width:31%" class="post_types"> 
              <select class="widefat select-post-type <?php echo esc_attr( $i );?>" name="data_group_array[group-<?php echo esc_attr( $j) ?>][data_array][<?php echo esc_attr( $i) ?>][key_1]">    
                <?php 
                foreach ($choices as $choice_key => $choice_value) { ?>         
                  <optgroup label="<?php echo esc_attr($choice_key);?>">
                  <?php
                  foreach ($choice_value as $sub_key => $sub_value) { ?> 
                    <option class="pt-child" value="<?php echo esc_attr( $sub_key );?>" <?php selected( $selected_val_key_1, $sub_key );?> > <?php echo saswp_t_string($sub_value);?> </option>
                    <?php
                  }
                  ?> </optgroup > <?php
                } ?>
              </select>
            </td>
            <td style="width:31%; <?php if (  $selected_val_key_1 =='show_globally' ) { echo 'display:none;'; }  ?>">
              <select class="widefat comparison" name="data_group_array[group-<?php echo esc_attr( $j) ?>][data_array][<?php echo esc_attr( $i )?>][key_2]"> <?php
                foreach ($comparison as $key => $value) { 
                  $selcomp = '';
                  if($key == $selected_val_key_2){
                    $selcomp = 'selected';
                  }
                  ?>
                  <option class="pt-child" value="<?php echo esc_attr( $key );?>" <?php echo esc_attr($selcomp); ?> > <?php echo saswp_t_string($value);?> </option>
                  <?php
                } ?>
              </select>
            </td>
            <td style="width:31%; <?php if (  $selected_val_key_1 =='show_globally' ) { echo 'display:none;'; }  ?>">
              <div class="insert-ajax-select">              
                <?php saswp_ajax_select_creator($selected_val_key_1, $selected_val_key_3, $i, $j );
                if($selected_val_key_1 == 'ef_taxonomy'){
                  saswp_create_ajax_select_taxonomy($selected_val_key_3, $selected_val_key_4, $i, $j);
                }
                ?>
                  <div style="display:none;" class="spinner"></div>
              </div>
            </td>

            <td class="widefat structured-clone" style="width:3.5%; <?php if (  $selected_val_key_1 =='show_globally' ) { echo 'display:none;'; }  ?>">
                <span> <button class="saswp-placement-button" type="button"> <?php echo saswp_t_string('And' );?> </button> </span> </td>
            
            <td class="widefat structured-delete" style="width:3.5%; <?php if (  $selected_val_key_1 =='show_globally' ) { echo 'display:none;'; }  ?>">
                <button class="saswp-placement-button" type="button"><span class="dashicons dashicons-trash"></span>  </button></td>         
          </tr>
          <?php 
        } ?>
        </tbody>
      </table>   
    </div>
    <?php } ?>
    
    
    <a style="margin-left: 8px; margin-bottom: 8px;" class="button saswp-placement-or-group saswp-placement-button" href="#"><?php echo saswp_t_string('Or'); ?></a>
</div>        
    <?php
  }
  
  /**
 * Dequeue the jQuery UI script.
 *
 * Hooked to the wp_print_scripts action, with a late priority (100),
 * so that it is after the script was enqueued.
 */
function saswp_dequeue_script() {
    
    if(get_post_type() == 'saswp'){
        
        wp_dequeue_script( 'avada-fusion-options' );
        
    }   
}

  add_action( 'wp_print_scripts', 'saswp_dequeue_script', 100 );
  
  add_action( 'admin_enqueue_scripts', 'saswp_style_script_include' );
  
      
  function saswp_style_script_include($hook) {
                          
    if (is_admin()) {
                                           
       $post_found_status = $post_type = '';
       $current_screen = get_current_screen(); 
       
       if(isset($current_screen->post_type)){                  
           $post_type = $current_screen->post_type;                
       }        
              
       $saswp_posts       = json_decode(get_transient('saswp_transient_schema_ids'), true);
       
       if(!$saswp_posts){        
        $saswp_posts = saswp_get_saved_schema_ids();        
       }      
       
       if(empty($saswp_posts)){           
        $post_found_status ='not_found';           
       }       
       
      $data_array = array(
          
          'ajax_url'                  => admin_url( 'admin-ajax.php' ), 
          'post_found_status'         => $post_found_status,
          'post_type'                 => $post_type,   
          'page_now'                  => $hook,
          'saswp_settings_url'        => esc_url(admin_url('edit.php?post_type=saswp&page=structured_data_options'))                       
          
      );
                   
      //Enque select 2 script starts here      
       if($hook == 'saswp' || get_post_type() == 'saswp'){
           
        wp_dequeue_script( 'avada-fusion-options' );          
        wp_register_script( 'structure_admin', SASWP_PLUGIN_URL . 'admin_section/js/'.(SASWP_ENVIRONMENT == 'production' ? 'structure_admin.min.js' : 'structure_admin.js'), array( 'jquery', 'jquery-ui-core'), SASWP_VERSION, true );   
           
        wp_localize_script( 'structure_admin', 'saswp_app_object', $data_array );
        wp_enqueue_script( 'structure_admin' );
                                    
        }
      //Enque select 2 script ends here                    
    }
  }
  
  // Save PHP Editor
  add_action ( 'save_post' , 'saswp_select_save_data' );
  
  function saswp_select_save_data ( $post_id ) {           
      
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
       
      // if our nonce isn't there, or we can't verify it, bail
    if( !isset( $_POST['saswp_select_name_nonce'] ) || !wp_verify_nonce( $_POST['saswp_select_name_nonce'], 'saswp_select_action_nonce' ) ) return;
      
      // if our current user can't edit this post, bail
    if( !current_user_can( saswp_current_user_can() ) ) return;  
                
    $post_data_group_array = array();  
    $temp_condition_array  = array();
    $show_globally         = false;
    
    if(isset($_POST['data_group_array'])){        
        
    $post_data_group_array = (array) $_POST['data_group_array'];    
    
    foreach($post_data_group_array as $groups){        
        
          foreach($groups['data_array'] as $group ){              
              
            if(array_search('show_globally', $group))
            {
                
              $temp_condition_array[0] =  $group;  
              $show_globally           = true;
              
            }
          }
      }    
      if($show_globally){
          
            unset($post_data_group_array);
            $post_data_group_array['group-0']['data_array'] = $temp_condition_array;       
      
      }      
    }                      
    if(isset($_POST['data_group_array'])){
        
      $post_data_group_array = saswp_sanitize_multi_array($post_data_group_array, 'data_array'); 
      
      update_post_meta(
        $post_id, 
        'data_group_array', 
        $post_data_group_array 
      );
      
    }
  }

}//CLosed is_admin

// Generate Proper post types for select and to add data.
add_action('wp_loaded', 'saswp_post_type_generator');
 
function saswp_post_type_generator(){

    $post_types = '';
    $post_types = get_post_types( array( 'public' => true ), 'names' );

    // Remove Unsupported Post types
    unset($post_types['attachment'], $post_types['amp_acf']);

    return $post_types;
}

add_action('wp','saswp_custom_breadcrumbs',99);

// Breadcrumbs
function saswp_custom_breadcrumbs() {
    
    global $sd_data;	
    $variables1_titles = array();   
    $variables2_links  = array();  
    $breadcrumb_url    = '';
    // Settings
    $prefix        = '';
    $home_title    = '';
    $separator     = '&gt;';        
    $home_title    = get_bloginfo();
    
    if(!$home_title){
        
        if(isset($sd_data['sd_name'])){
            
           $home_title =  $sd_data['sd_name'];
           
        }else{
            
           $home_title =  'HomePage'; 
           
        }
    }
      
    // If you have any custom post types with custom taxonomies, put the taxonomy name below (e.g. product_cat)
    $custom_taxonomy    = 'product_cat';
       
    // Get the query & post information
    global $post;       
    // Do not display on the homepage
    if ( !is_front_page() ) {      
        // Build the breadcrums
        // Home page
        $variables1_titles[] = $home_title;
        $variables2_links[]  = get_home_url();
        $breadcrumb_url      = get_home_url();

        
        if ( is_archive() && !is_tax() && !is_category() && !is_tag() && !is_author() ) {
                                
                    $variables1_titles[] = post_type_archive_title($prefix, false);
                    $variables2_links[]  = get_post_type_archive_link(get_post_type());  
                    $breadcrumb_url      = get_post_type_archive_link(get_post_type());
                    
        } else if  ( is_author() ) {
            
	    	 global $authordata;	    		               
              
              if($authordata){
                
                $author_url          = get_author_posts_url($authordata->ID);	                            
	              $variables1_titles[] = $authordata->display_name;
	              $variables2_links[]  = $author_url;
                $breadcrumb_url      = $author_url;

              }	            
                    
        } else if ( is_archive() && is_tax() && !is_category() && !is_tag() ) {
              
            // If post is a custom post type
                $post_type = get_post_type();
              
            // If it is a custom post type display name and link
                if($post_type != 'post') {

                    $post_type_object    = get_post_type_object($post_type);
                    $post_type_archive   = get_post_type_archive_link($post_type);
                    
                    if(!$post_type_archive){
                        $post_type_archive = get_permalink();
                    }
                    
                     if(!$post_type_archive){
                         
                        $queried_obj = get_queried_object();
                        
                        if(is_object($queried_obj)){
                            $variables1_titles[] = $queried_obj->name;
                            $variables2_links[]  = get_term_link($queried_obj->term_id);  
                            $breadcrumb_url      = get_term_link($queried_obj->term_id);
                        }
                        
                    }else{
                        $variables1_titles[] = $post_type_object->labels->name;
                        $variables2_links[]  = $post_type_archive;
                        $breadcrumb_url      = $post_type_archive;
                    }

                }
              
                    $queried_obj = get_queried_object();
                    
                    if(is_object($queried_obj)){
                         $variables1_titles[] = get_queried_object()->name;
                         $variables2_links[]  = get_term_link($queried_obj->term_id);
                         $breadcrumb_url      = get_term_link($queried_obj->term_id);
                    }
                                                           
        } else if ( is_single() ) {
              
            // If post is a custom post type
               $post_type = get_post_type();
              
            // If it is a custom post type display name and link
            if($post_type != 'post') {
                  
                    $post_type_object   = get_post_type_object($post_type);
                    $post_type_archive  = get_post_type_archive_link($post_type);  
                    
                    if(!$post_type_archive){
                        $post_type_archive = get_permalink();
                    }
                    
                    $variables1_titles[]= $post_type_object->labels->name;
                    $variables2_links[] = $post_type_archive;     
                    $breadcrumb_url      = $post_type_archive;
            }
             
            if( !isset($sd_data['saswp_breadcrumb_remove_cat']) || (isset($sd_data['saswp_breadcrumb_remove_cat']) && $sd_data['saswp_breadcrumb_remove_cat'] == 0 ) ){

              // Get post category info
            $category = get_the_category();
              
            if(!empty($category)) {
              
              $yoast_primary_cat_name     = '';
              $yoast_primary_cat_url      = '';

              if ( class_exists('WPSEO_Primary_Term') && ( isset($sd_data['saswp-yoast']) && $sd_data['saswp-yoast'] == 1 ) ) {

                $wpseo_primary_term = new WPSEO_Primary_Term( 'category', get_the_id() );
                $wpseo_primary_term = $wpseo_primary_term->get_primary_term();
                $term_yoast = get_term( $wpseo_primary_term );
                
                if (!is_wp_error( $term_yoast ) ) {
                                                               
                  $yoast_primary_cat_name  = $term_yoast->name;
                  $yoast_primary_cat_url   = get_category_link( $term_yoast->term_id );                  
  
                }

               }

               if(!empty($yoast_primary_cat_name) && !empty($yoast_primary_cat_url)){

                      $variables1_titles[]  = $yoast_primary_cat_name;
                      $variables2_links[]   = $yoast_primary_cat_url;
                      $breadcrumb_url       = $yoast_primary_cat_url;

               }else{

                  $category_values = array_values( $category );
              
                  foreach ($category_values as $category_value) {
                      
                      $category_name        = get_category($category_value);
                      $cat_name             = $category_name->name;
                      $variables1_titles[]  = $cat_name;
                      $variables2_links[]   = get_category_link( $category_value );
                      $breadcrumb_url       = get_category_link( $category_value );
                  
                  }

               }                                                        
              
                // Get last category post is in
                $last_category   = end(($category));
                $category_name   = get_category($last_category);
                // Get parent any categories and create array
                $get_cat_parents = get_category_parents($last_category->term_id, true, ',');

                if(is_string($get_cat_parents)){

                  $get_cat_parents = rtrim($get_cat_parents,',');
                  $cat_parents     = explode(',',$get_cat_parents);

                  // Loop through parent categories and store in variable $cat_display
                  $cat_display = '';
                  
                  if( !empty($cat_parents) && is_array($cat_parents) ){

                    foreach($cat_parents as $parents) {
                      
                      $cat_display .= '<li class="item-cat">'.saswp_t_string( $parents ).'</li>';
                      $cat_display .= '<li class="separator"> ' . saswp_t_string( $separator ) . ' </li>';
                      
                    }

                  }                  

                }
                                                                                  
            }

            }
              
            // If it's a custom post type within a custom taxonomy
            $taxonomy_exists = taxonomy_exists($custom_taxonomy);
            
            if(empty($last_category) && !empty($custom_taxonomy) && $taxonomy_exists) {
                   
                $taxonomy_terms = get_the_terms( $post->ID, $custom_taxonomy );

                if($taxonomy_terms){
                    
                    $cat_id         = $taxonomy_terms[0]->term_id;                
                    $cat_link       = get_term_link($taxonomy_terms[0]->term_id, $custom_taxonomy);
                    $cat_name       = $taxonomy_terms[0]->name;
                    
                }
                                
            }
              
             if(!empty($cat_id)) {
                 
                $variables1_titles[]  = $cat_name;
                $variables2_links[]   = $cat_link;
                $breadcrumb_url       = $cat_link;
              
            } 
            
            $variables1_titles[] = saswp_get_the_title();
            $variables2_links[]  = get_permalink();
            $breadcrumb_url      = get_permalink();
                          
        } else if ( is_category() ) {
            
                $current_url   = saswp_get_current_url();
                $exploded_cat  = explode('/', $current_url);
                                
                if(!empty($exploded_cat) && is_array($exploded_cat)) {
                                                      
                  foreach ($exploded_cat as $value) {

                      $category_value = get_category_by_slug($value);
                      
                      if($category_value && is_object($category_value)){

                        $category_name        = get_category($category_value);
                        $cat_name             = $category_name->name;
                        $variables1_titles[]  = $cat_name;
                        $variables2_links[]   = get_category_link( $category_value );
                        $breadcrumb_url       = get_category_link( $category_value );

                      }
                      
                  }
              }                          
        } else if ( is_page() ) {
              
            // Standard page
            if( is_object( $post ) &&  $post->post_parent ){
                   
                // If child page, get parents 
                $anc = get_post_ancestors( $post->ID );
                   
                // Get parents in the right order
                $anc = array_reverse($anc);
                   
                // Parent page loop
                if ( !isset( $parents ) ) $parents = null;
                
                foreach ( $anc as $ancestor ) {
                    
                    $parents .= '<li class="item-parent item-parent-' . esc_attr($ancestor) . '"><a class="bread-parent bread-parent-' . esc_attr($ancestor) . '" href="' . esc_url(get_permalink($ancestor)) . '" title="' . esc_attr(@get_the_title($ancestor)) . '">' . saswp_t_string(@get_the_title($ancestor) ) . '</a></li>';
                    $parents .= '<li class="separator separator-' . esc_attr($ancestor) . '"> ' . saswp_t_string($separator ) . ' </li>';
                    $variables1_titles[]    = @get_the_title($ancestor);
                    $variables2_links[]     = get_permalink($ancestor);
                    $breadcrumb_url         = get_permalink($ancestor);
                    
                }
             
                    $variables1_titles[]    = saswp_get_the_title();
                    $variables2_links[]     = get_permalink();
                    $breadcrumb_url         = get_permalink();
                   
            } else {      
                
                   $variables1_titles[]     = saswp_get_the_title();
                   $variables2_links[]      = get_permalink();
                   $breadcrumb_url          = get_permalink();
            }
              
        } else if ( is_tag() ) {
            
            // Tag page               
            // Get tag information
            $term_id        = get_query_var('tag_id');                   
            $get_term       = get_term($term_id);
            
            if( is_object($get_term) && isset($get_term->name) ){
                
                $variables1_titles[] = $get_term->name;
                $variables2_links[]  = get_term_link($term_id);
                $breadcrumb_url      = get_term_link($term_id);
                              
            }
            // Tag name and link                  
          }    
          
          $sd_data['titles']            = $variables1_titles;                        
          $sd_data['links']             = $variables2_links;
          $sd_data['breadcrumb_url']     = $breadcrumb_url;
          
    }
       
}

//Adding extra columns and displaying its data starts here
function saswp_custom_column_set( $column, $post_id ) {
                
            switch ( $column ) {       
                
                case 'saswp_schema_type' :
                    
                    $schema_type = get_post_meta( $post_id, $key='schema_type', true);
                     $url = admin_url( 'post.php?post='.$post_id.'&action=edit' );
                    
                    if($schema_type == 'local_business'){

                      $business_type     = get_post_meta($post_id, 'saswp_business_type', true);
                      $business_name     = get_post_meta($post_id, 'saswp_business_name', true);

                      if($business_name){
                          echo '<strong><a class="row-title" href="'.esc_url($url).'">LocalBusiness ('.esc_html($business_name).')</a></strong>';   
                      } else if($business_type){
                          echo '<strong><a class="row-title" href="'.esc_url($url).'">LocalBusiness ('.esc_html($business_type).')</a></strong>';
                      } else {
                          echo '<strong><a class="row-title" href="'.esc_url($url).'">LocalBusiness</a></strong>';
                      }
                        
                    }else if($schema_type == 'qanda'){
                        echo '<strong><a class="row-title" href="'.esc_url($url).'">Q&A</a></strong>';
                    }else{
                        echo '<strong><a class="row-title" href="'.esc_url($url).'">'.esc_html($schema_type).'</a></strong>';
                    }
                    
                    
                    break; 
                case 'saswp_target_location' :
                    
                    $enabled ='';
                    $exclude ='';
                    $data_group_array = get_post_meta( $post_id, $key='data_group_array', true);
                   
                    
                    if($data_group_array){
                        
                    foreach ($data_group_array as $groups){
                         
                        foreach($groups['data_array'] as $group){                           
                           
                           if($group['key_2'] == 'equal'){
                               
                               if($group['key_1'] == 'show_globally'){
                                   
                                   $enabled .= 'Globally';  
                                   
                               }else{
                                   
                                   if(isset($group['key_3'])){
                                      $enabled .= $group['key_3'].', ';   
                                   } 
                                                                      
                               }
                                                                                       
                           }else{
                               
                            $exclude .= $group['key_3']. ', ';   
                            
                           }
                           
                        }
                        
                    } 
                    if($enabled){
                        
                        echo '<div><strong>'.saswp_t_string( 'Enable on: ' ).'</strong> '.esc_attr($enabled).'</div>';    
                    
                    }
                    if($exclude){
                        
                        echo '<div><strong>'.saswp_t_string( 'Exclude from: ' ).'</strong>'.esc_attr($exclude).'</div>';   
                    
                    }                    
                    }                    
                    
                                     
                    break;
               
            }
}
add_action( 'manage_saswp_posts_custom_column' , 'saswp_custom_column_set', 10, 2 );

/**
 * Add the custom columns to the Ads post type:
 * @param array $columns
 * @return string
 */

function saswp_custom_columns($columns) { 
    
    $title = $columns['title'];
    $cb    = $columns['cb'];
    unset($columns);
    $columns['cb']    = $cb;
    $columns['title'] = $title;
    $columns['saswp_schema_type']       = '<a>'.saswp_t_string( 'Schema Type' ).'<a>';
    $columns['saswp_target_location']   = '<a>'.saswp_t_string( 'Target Location' ).'<a>';    
    
    return $columns;
}
add_filter( 'manage_saswp_posts_columns', 'saswp_custom_columns' );

//Adding extra columns and displaying its data ends here


   /**
     * This is a ajax handler function for sending email from user admin panel to us. 
     * @return type json string
     */
function saswp_send_query_message(){   
    
        if ( ! isset( $_POST['saswp_security_nonce'] ) ){
           return; 
        }
        if ( !wp_verify_nonce( $_POST['saswp_security_nonce'], 'saswp_ajax_check_nonce' ) ){
           return;  
        }   
        $customer_type  = 'Are you a premium customer ? No';
        $message        = sanitize_textarea_field($_POST['message']); 
        $email          = sanitize_textarea_field($_POST['email']); 
        $premium_cus    = sanitize_textarea_field($_POST['premium_cus']);   
                                
        if(function_exists('wp_get_current_user')){

            $user           = wp_get_current_user();

            if($premium_cus == 'yes'){
              $customer_type  = 'Are you a premium customer ? Yes';
            }
         
            $message = '<p>'.$message.'</p><br><br>'
                 . $customer_type
                 . '<br><br>'.'Query from plugin support tab';
            
            $user_data  = $user->data;        
            $user_email = $user_data->user_email;     
            
            if($email){
                $user_email = $email;
            }            
            //php mailer variables        
            $sendto    = 'team@magazine3.com';
            $subject   = "Schema Customer Query";
            
            $headers[] = 'Content-Type: text/html; charset=UTF-8';
            $headers[] = 'From: '. esc_attr($user_email);            
            $headers[] = 'Reply-To: ' . esc_attr($user_email);
            // Load WP components, no themes.                      
            $sent = wp_mail($sendto, $subject, $message, $headers); 

            if($sent){

                 echo json_encode(array('status'=>'t'));  

            }else{

                echo json_encode(array('status'=>'f'));            

            }
            
        }
                        
        wp_die();           
}

add_action('wp_ajax_saswp_send_query_message', 'saswp_send_query_message');

add_action('wp_ajax_saswp_dismiss_notices', 'saswp_dismiss_notices');

function saswp_dismiss_notices(){

  if ( ! isset( $_POST['saswp_security_nonce'] ) ){
    return; 
  }
  if ( !wp_verify_nonce( $_POST['saswp_security_nonce'], 'saswp_ajax_check_nonce' ) ){
    return;  
  }
  
  if(isset($_POST['notice_type'])){
    
    $notice_type = $_POST['notice_type'];

    $user_id      = get_current_user_id();
    
    
    $updated = update_user_meta( $user_id, $notice_type.'_dismiss_date', date("Y-m-d"));

    if($updated){
      echo json_encode(array('status'=>'t'));  
    }else{
      echo json_encode(array('status'=>'f'));  
    }

  }
  
  wp_die();           
}
   /**
     * This is a ajax handler function for sending email from user admin panel to us. 
     * @return type json string
     */
function saswp_import_plugin_data(){                  
    
        if ( ! current_user_can( saswp_current_user_can() ) ) {
             return;
        }
        
        if ( ! isset( $_GET['saswp_security_nonce'] ) ){
           return; 
        }
        if ( !wp_verify_nonce( $_GET['saswp_security_nonce'], 'saswp_ajax_check_nonce' ) ){
           return;  
        }    
        
        $plugin_name   = sanitize_text_field($_GET['plugin_name']);         
        $result        = '';
        
        switch ($plugin_name) {
            
            case 'schema':
                if ( is_plugin_active('schema/schema.php')) {
                    $result = saswp_import_schema_plugin_data();      
                }                
                break;
                
            case 'schema_pro':                
                if ( is_plugin_active('wp-schema-pro/wp-schema-pro.php')) {
                    $result = saswp_import_schema_pro_plugin_data();      
                }                
                break;
            case 'wp_seo_schema':                
                if ( is_plugin_active('wp-seo-structured-data-schema/wp-seo-structured-data-schema.php')) {
                    $result = saswp_import_wp_seo_schema_plugin_data();      
                }
                 break;
            case 'seo_pressor':                
                if ( is_plugin_active('seo-pressor/seo-pressor.php')) {
                    $result = saswp_import_seo_pressor_plugin_data();      
                }                
                break;
           case 'wpsso_core':                
                if ( is_plugin_active('wpsso/wpsso.php') && is_plugin_active('wpsso-schema-json-ld/wpsso-schema-json-ld.php')) {
                    $result = saswp_import_wpsso_core_plugin_data();      
                }                
                break;
            case 'aiors':                
                if ( is_plugin_active('all-in-one-schemaorg-rich-snippets/index.php')) {
                    $result = saswp_import_aiors_plugin_data();      
                }                
                break;   
                
                case 'wp_custom_rv':                
                if ( is_plugin_active('wp-customer-reviews/wp-customer-reviews-3.php')) {
                    $result = saswp_import_wp_custom_rv_plugin_data();      
                }                
                break; 

                case 'starsrating':       
                      
                  if ( is_plugin_active('stars-rating/stars-rating.php')) {                      
                      update_option('saswp_imported_starsrating', 1);
                      $result = 'updated';
                  }                
                break; 
                
                case 'schema_for_faqs':                
                  if ( is_plugin_active('faq-schema-markup-faq-structured-data/schema-for-faqs.php')) {
                      $result = saswp_import_schema_for_faqs_plugin_data();      
                  }                
                break;                 

            default:
                break;
        }                             
        if($result){
            
             echo json_encode(array('status'=>'t', 'message'=>saswp_t_string('Data has been imported succeessfully')));            
             
        }else{
            
            echo json_encode(array('status'=>'f', 'message'=>saswp_t_string('Plugin data is not available or it is not activated')));            
        
        }        
           wp_die();           
}

add_action('wp_ajax_saswp_import_plugin_data', 'saswp_import_plugin_data');


function saswp_feeback_no_thanks(){     
    
        if ( ! current_user_can( saswp_current_user_can() ) ) {
             return;
        }
        if ( ! isset( $_GET['saswp_security_nonce'] ) ){
           return; 
        }
        if ( !wp_verify_nonce( $_GET['saswp_security_nonce'], 'saswp_ajax_check_nonce' ) ){
           return;  
        }
        
        $result = update_option( "saswp_activation_never", 'never'); 
        
        if($result){
            
            echo json_encode(array('status'=>'t'));            
            
        }else{
            
            echo json_encode(array('status'=>'f'));            
            
        }   
        
        wp_die();           
}

add_action('wp_ajax_saswp_feeback_no_thanks', 'saswp_feeback_no_thanks');


function saswp_feeback_remindme(){  
    
        if ( ! current_user_can( saswp_current_user_can() ) ) {
             return;
        }
        if ( ! isset( $_GET['saswp_security_nonce'] ) ){
           return; 
        }
        if ( !wp_verify_nonce( $_GET['saswp_security_nonce'], 'saswp_ajax_check_nonce' ) ){
           return;  
        }
    
        $result = update_option( "saswp_activation_date", date("Y-m-d"));   
        
        if($result){
            
            echo json_encode(array('status'=>'t'));            
        
        }else{
            
            echo json_encode(array('status'=>'f'));            
        
        }        
        wp_die();           
}

add_action('wp_ajax_saswp_feeback_remindme', 'saswp_feeback_remindme');

/**
 * Licensing code starts here
 */

function saswp_license_status($add_on, $license_status, $license_key){
                                      
                $item_name = array(                       
                       'jobposting'   => 'JobPosting Schema Compatibility',
                       'polylang'     => 'Polylang Compatibility For SASWP',
                       'woocommerce'  => 'Woocommerce compatibility for Schema',
                       'reviews'      => 'Reviews for schema',
                       'res'          => 'Real Estate Schema',
                       'cs'           => 'Course Schema',
                       'es'           => 'Event Schema',
                       'rs'           => 'Recipe Schema',
                       'qanda'        => 'Q&A Schema Compatibility',
                       'faq'          => 'FAQ Schema Compatibility',
                       'ociaifs'      => '1 Click Indexing Api Integration For SASWP'
                );
                                                                            
                $edd_action = '';
                if($license_status =='active'){
                   $edd_action = 'activate_license'; 
                }
                
                if($license_status =='inactive'){
                   $edd_action = 'deactivate_license'; 
                }
            // data to send in our API request
              $api_params = array(
                'edd_action' => $edd_action,
                'license'    => $license_key,
                'item_name'  => $item_name[strtolower($add_on)],
                'author'     => 'Magazine3',			
                'url'        => home_url(),
                'beta'       => false,
              );
                
                $message        = '';
                $current_status = '';
                $response       = @wp_remote_post( SASWP_EDD_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );
                           
                // make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
                        
			$message =  ( is_wp_error( $response ) && $response->get_error_message() ) ? $response->get_error_message() : __( 'An error occurred, please try again.' );
		} else {
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
                        
			if ( false === $license_data->success ) {
                            
                                $current_status = $license_data->error;
                                
				switch( $license_data->error ) {
					case 'expired' :
						$message = sprintf(
							__( 'Your license key expired on %s.' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
						);
						break;
					case 'revoked' :
						$message = __( 'Your license key has been disabled.' );
						break;
					case 'missing' :
						$message = __( 'Invalid license.' );
						break;
					case 'invalid' :
					case 'site_inactive' :
						$message = __( 'Your license is not active for this URL.' );
						break;
					case 'item_name_mismatch' :
						$message = __( 'This appears to be an invalid license key.' );
						break;
					case 'no_activations_left':
						$message = __( 'Your license key has reached its activation limit.' );
						break;
                                        case 'license_not_activable':
						$message = __( 'Your license key may not belong to this extension.' );
						break;    
					default :
						$message = __( 'An error occurred, please try again.' );
						break;
				}
			}
		}
                if($message){
                    
                        $license[strtolower($add_on).'_addon_license_key_status'] = $current_status;
                        $license[strtolower($add_on).'_addon_license_key']        = $license_key;
                        $license[strtolower($add_on).'_addon_license_key_message']= $message;
                    
                }else{

                    if($license_status == 'active'){
                                        
                        if(strtolower($add_on) == 'reviews'){
                            
                            if(function_exists('saswp_create_reviews_user')){
                                                             
                                $user_create = saswp_create_reviews_user($license_key, $item_name[strtolower($add_on)]);   
                            
                                if($user_create['status']){ 

                                    update_option(strtolower($add_on).'_addon_user_id', intval($user_create['user_id']));
                                    update_option(strtolower($add_on).'_addon_reviews_limits', intval($user_create['remains_limit']));        

                                }
                                
                            }
                            
                        } 
                        
                        $license[strtolower($add_on).'_addon_license_key_status']  = 'active';
                        $license[strtolower($add_on).'_addon_license_key']         = $license_key;
                        $license[strtolower($add_on).'_addon_license_key_message'] = 'active';
                                                                        
                        $current_status = 'active';
                        $message = 'Activated';
                    }
                    
                    if($license_status == 'inactive'){
                        
                        $license[strtolower($add_on).'_addon_license_key_status']  = 'deactivated';
                        $license[strtolower($add_on).'_addon_license_key']         = $license_key;
                        $license[strtolower($add_on).'_addon_license_key_message'] = 'Deactivated';
                        $current_status = 'deactivated';
                        $message = 'Deactivated';
                    }
                    
                }
                
                $get_options   = get_option('sd_data');
                $merge_options = array_merge($get_options, $license);
                update_option('sd_data', $merge_options);  
                
                return array('status'=> $current_status, 'message'=> $message);
                                                                
}

function saswp_license_status_check(){  
    
        if ( ! current_user_can( saswp_current_user_can() ) ) {
             return;
        }
        if ( ! isset( $_POST['saswp_security_nonce'] ) ){
             return; 
        }
        if ( !wp_verify_nonce( $_POST['saswp_security_nonce'], 'saswp_ajax_check_nonce' ) ){
             return;  
        }    
        
        $add_on           = sanitize_text_field($_POST['add_on']);
        $license_status   = sanitize_text_field($_POST['license_status']);
        $license_key      = sanitize_text_field($_POST['license_key']);
        
        if($add_on && $license_status && $license_key){
            
          $result = saswp_license_status($add_on, $license_status, $license_key);
          
          echo json_encode($result);
                        
        }          
                        
        wp_die();           
}

add_action('wp_ajax_saswp_license_status_check', 'saswp_license_status_check');
/**
 * Licensing code ends here
 */

add_action( 'upgrader_process_complete', 'saswp_upgrade_function',10, 2);

function saswp_upgrade_function( $upgrader_object, $options ) {
    
    $current_plugin_path_name = SASWP_PLUGIN_BASENAME;

    if ($options['action'] == 'update' && $options['type'] == 'plugin' ){
       
       if(is_array($options) && array_key_exists('plugins', $options)){
        
           foreach($options['plugins'] as $each_plugin){
           
            if ($each_plugin == $current_plugin_path_name){

               saswp_review_module_upgradation();
               saswp_migrate_old_social_profile();

            }
          }           
       } 
                     
    }
    
}

function saswp_review_module_upgradation(){
                    
            $upgrade_option = get_option('saswp_google_upgrade');

            if(!$upgrade_option){
               
                global $sd_data;
                
                $g_review_status = $g_review_api = '';
                
                if(isset($sd_data['saswp-google-review']) && $sd_data['saswp-google-review'] == 1){
                    $g_review_status = $sd_data['saswp-google-review'];
                }
                
                if(isset($sd_data['saswp_google_place_api_key']) && $sd_data['saswp_google_place_api_key'] != ''){
                    $g_review_api = $sd_data['saswp_google_place_api_key'];
                }
                
                if($g_review_status && $g_review_api){
                                     
                    $posts_list = get_posts( 
                        array(
                            'post_type' 	 => 'saswp-google-review',                                                                                   
                            'posts_per_page'     => -1,   
                            'post_status'        => 'publish',
                            'meta_query'  => array(
                                array(
                                'key'     => 'saswp_google_place_id',                                
                                'compare' => 'EXISTS',
                                 )
                            )
                           
                    ) );
                                                            
                    if($posts_list){
                        
                        if(class_exists('saswp_reviews_service')){
                        
                            $service = new saswp_reviews_service(); 
                        
                                foreach($posts_list as $list){

                                    $g_place_id = get_post_meta($list->ID, $key='saswp_google_place_id', true);
                                    
                                    if($g_place_id){
                                        $service->saswp_get_free_reviews_data($g_place_id, $g_review_api); 
                                    }

                                }
                                                        
                        }
                        
                }
                                                            
                } 
                
                 update_option('saswp_google_upgrade', date("Y-m-d"));
                 
           }
                                    
}

add_action('wp_update_nav_menu', 'saswp_save_nav_menu_on_menu_update');

function saswp_save_nav_menu_on_menu_update(){
    
    global $sd_data; 
    $menu_id = null;
    
    if(isset($sd_data['saswp_site_navigation_menu'])){
        $menu_id = $sd_data['saswp_site_navigation_menu'];
    }
    
    saswp_save_nav_menu_in_transient($menu_id);
    
}

add_action('update_option_sd_data', 'saswp_save_nav_menu_on_option_update',10, 3);

function saswp_save_nav_menu_on_option_update($old, $new, $opt_name){
    
    $menu_id = null;
    
    if(isset($new['saswp_site_navigation_menu'])){
        $menu_id = $new['saswp_site_navigation_menu'];
    }
    
    saswp_save_nav_menu_in_transient($menu_id);
    
}

function saswp_save_nav_menu_in_transient($menu_id){
                        
    $menuItems = wp_get_nav_menu_items($menu_id);                
    set_transient('saswp_nav_menu', $menuItems);                
                     
}

add_action( 'wp_ajax_saswp_get_select2_data', 'saswp_get_select2_data'); 

function saswp_get_select2_data(){
            
        if ( ! isset( $_GET['saswp_security_nonce'] ) ){
          return; 
        }
        
        if ( (wp_verify_nonce( $_GET['saswp_security_nonce'], 'saswp_ajax_check_nonce' ) ) ||  (wp_verify_nonce( $_GET['saswp_security_nonce'], 'saswp_add_new_nonce' ) )){

          $search        = isset( $_GET['q'] ) ? sanitize_text_field( $_GET['q'] ) : '';                                    
          $type          = isset( $_GET['type'] ) ? sanitize_text_field( $_GET['type'] ) : '';                                    

          $result = saswp_get_condition_list($type, $search);
                      
          wp_send_json( $result );            

        }else{
          return;  
        }                
        
        wp_die();
}

function saswp_create_resized_image_folder(){                  
    
  if ( ! isset( $_POST['saswp_security_nonce'] ) ){
     return; 
  }
  if ( !wp_verify_nonce( $_POST['saswp_security_nonce'], 'saswp_ajax_check_nonce' ) ){
     return;  
  }    

  $response    = array();       
  $upload_info = wp_upload_dir();
  $upload_dir  = $upload_info['basedir'];
  $upload_url  = $upload_info['baseurl'];  
  
  $make_new_dir = $upload_dir . '/schema-and-structured-data-for-wp';

  if (! is_dir($make_new_dir)) {
      mkdir( $make_new_dir, 0700 );
  }

  if(is_dir($make_new_dir)){

    $old_url    = SASWP_PLUGIN_URL.'/admin_section/images/sd-logo-white.png';            
    $url        = $upload_url.'/schema-and-structured-data-for-wp/sd-logo-white.png';
    $new_url    = $make_new_dir.'/sd-logo-white.png';    
    @copy($old_url, $new_url);

    $result = @wp_remote_get($url);
    
    if(wp_remote_retrieve_response_code($result) == 200){
      $response = array('status' => 't');   
    }else{
      $response = array('status' => 'f', 'message' => 'We are unable to create a folder in your uploads directory. Please Check your folder permission settings on server and allow it.');
    }

  }else{
    $response = array('status' => 'f', 'message' => 'We are unable to create a folder in your uploads directory. Please Check your folder permission settings on server and allow it.');
  }

  wp_send_json( $response );

  wp_die();           

}

add_action('wp_ajax_saswp_create_resized_image_folder', 'saswp_create_resized_image_folder');

// add async and defer attributes to enqueued scripts
function saswp_script_loader_tag($tag, $handle, $src) {
	
	if ($handle === 'saswp-recaptcha') {
		
		if (false === stripos($tag, 'async')) {
			
			$tag = str_replace(' src', ' async src', $tag);
			
		}
		
		if (false === stripos($tag, 'defer')) {
			
			$tag = str_replace('<script ', '<script defer ', $tag);
			
		}
		
	}
	
	return $tag;
	
}
add_filter('script_loader_tag', 'saswp_script_loader_tag', 10, 3);