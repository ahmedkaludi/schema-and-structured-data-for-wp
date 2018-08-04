<?php
//FrontEnd
function saswp_get_all_schema_posts(){
        $post_idArray = array();
        $query = new WP_Query(
          array(
              'post_type' => 'saswp',
              'post_status' => 'publish',
              'posts_per_page' => -1,
          ));
          while ($query->have_posts()) {
              $query->the_post();
              $post_idArray[] = get_the_ID();
          }
          wp_reset_query();
          wp_reset_postdata();

    if(count($post_idArray)>0){    
      $returnData = array();
      foreach ($post_idArray as $key => $post_id) 
        {
          $data = saswp_generate_field_data( $post_id );
          $data = array_filter($data);
          $number_of_fields = count($data);
          $unique_checker = 0;
          // Check if we have more then 1 fields.
          if ( $number_of_fields > 0 ) {
            // Check if all the arrays have TRUE setup, then send the value 1, if all the 
            // values are same.
            $unique_checker = count( array_unique($data) );
            // Check and make sure only all TRUE values only passed on, if all values are FALSE,
            // then making sure all FALSE are converting to 0, and returing false.
            // Code will not run.
            $array_is_false =  in_array(false, $data);
            if (  $array_is_false ) {
              $unique_checker = 0;
            }
          }

          if ( $unique_checker === 1 || $unique_checker === true) {
              $conditions = get_post_meta( $post_id, 'data_array', true);
              $conditions = $conditions[0];
              $returnData[] = array(
                    'schema_type' => get_post_meta( $post_id, 'schema_type', true),
                    'schema_options' => get_post_meta( $post_id, 'schema_options', true),
                    'conditions'  => $conditions, 
                  );
            }
      }//foreach closed post_idArray
      //Prioritize
      if(count($returnData)>0){
          $priority = array(
            'post_type'=>1,  
            'user_type'=>2, 
            'post'=> 3 , 
            'post_category'=> 4,
            'post_format'=> 5, 
            'Page'=> 6,  
            'page_template'=>7,  
            'ef_taxonomy'=>8
          );
            $actualReturnData = array();
            foreach ($returnData as $key => $value) {
              $actualReturnData[$priority[$value['conditions']['key_1']]] = $value;
            }
            $maxs = array_keys($actualReturnData, max($actualReturnData));
            return $actualReturnData[$maxs[0]];
      }
  }//iF Closed post_idArray
   return false;
}

function saswp_generate_field_data( $post_id ){
      $conditions = get_post_meta( $post_id, 'data_array', true);  

      $output = array();
      if ( $conditions ) { 
        $output = array_map('saswp_comparison_logic_checker', $conditions); 
      }
      return $output;
}

function saswp_comparison_logic_checker($input){
       global $post;
        $type       = $input['key_1'];
        $comparison = $input['key_2'];
        $data       = $input['key_3'];
        $result             = ''; 
       
        // Get all the users registered
        $user               = wp_get_current_user();

        switch ($type) {
        // Basic Controls ------------ 
          // Posts Type
          case 'post_type':   
                $current_post_type  = $post->post_type;            
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

      // Logged in User Type
         case 'user_type':            
            if ( $comparison == 'equal') {
                if ( in_array( $data, (array) $user->roles ) ) {
                    $result = true;
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
            $current_post = $post->ID;
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
          $postcat = get_the_category( $post->ID );
          $current_category = $postcat[0]->cat_ID; 

          if ( $comparison == 'equal') {
              if ( $data == $current_category ) {
                  $result = true;
              }
          }
          if ( $comparison == 'not_equal') {
              if ( $data != $current_category ) {
                  $result = true;
              }
          }
        break;
      // Post Format
      case 'post_format':
          $current_post_format = get_post_format( $post->ID );
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
        if(ampforwp_is_front_page()){
          $current_post = $redux_builder_amp['amp-frontpage-select-option-pages'];
        }else{
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

      // Page Template 
      case 'page_template':
        $current_page_template = get_page_template_slug( $post->ID );
            if ( $current_page_template == false ) {
                $current_page_template = 'default';
            }
            if ( $comparison == 'equal' ) {
                if ( $current_page_template == $data ) {
                    $result = true;
                }
            }
            if ( $comparison == 'not_equal') {              
                if ( $current_page_template != $data ) {
                    $result = true;
                }
            }

        break; 

    // Other Controls ---------------
      // Taxonomy Term
      case 'ef_taxonomy':
        // Get all the post registered taxonomies        
        // Get the list of all the taxonomies associated with current post
        $taxonomy_names = get_post_taxonomies( $post->ID );

        $checker    = '';
        $post_terms = '';

          if ( $data != 'all') {
            $post_terms = wp_get_post_terms($post->ID, $data);           

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
            if($result==true && isset( $input['key_4'] ) && $input['key_4'] !='all'){
              $term_data       = $input['key_4'];
              $terms = wp_get_post_terms( $post->ID ,$data);
              if(count($terms)>0){
                $termChoices = array();
                foreach ($terms as $key => $termvalue) {
                   $termChoices[] = $termvalue->slug;
                 } 
              }
              $result = false;
              if(in_array($term_data, $termChoices)){
                $result = true;
              }
            }//if closed for key_4

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


    register_post_type( 'saswp',
      array(
            'labels' => array(
            'name'              => esc_html__( 'Structured Data', 'schema-and-structured-data-for-wp' ),
            'singular_name'     => esc_html__( 'Structured Data', 'schema-and-structured-data-for-wp' ),
            'add_new' 		      => esc_html__( 'Add New', 'schema-and-structured-data-for-wp' ),
	          'add_new_item'  	  => esc_html__( 'Add New', 'schema-and-structured-data-for-wp' ),
            'edit_item'         => esc_html__( 'Edit Structured Data','schema-and-structured-data-for-wp')
        ),
          'public'                => true,
          'has_archive'           => false,
          'exclude_from_search'   => true,
          'publicly_queryable'    => false,
          'supports'              => array('title'),          
      )
    );
  }
  add_action( 'add_meta_boxes', 'saswp_create_meta_box_select' );
  function saswp_create_meta_box_select(){
    // Repeater Comparison Field
    add_meta_box( 'amp_sdwp_select', esc_html__( 'Placement','schema-and-structured-data-for-wp' ), 'saswp_select_callback', 'saswp','normal', 'high' );
    
  }



  function saswp_select_callback($post) {
      
    $data_array    = esc_sql ( get_post_meta($post->ID, 'data_array', true)  );    
    $schema_type    = esc_sql ( get_post_meta($post->ID, 'schema_type', true)  );
    $schema_options    = esc_sql ( get_post_meta($post->ID, 'schema_options', true)  );
    $data_array = is_array($data_array)? array_values($data_array): array();      
    if ( empty( $data_array ) ) {
      $data_array = array(
        array(
        'key_1' => 'post_type',
        'key_2' => 'not_equal',
        'key_3' => 'none',
        )
      );
    }
    //security check
    wp_nonce_field( 'saswp_select_action_nonce', 'saswp_select_name_nonce' );?>

    <?php 
    // Type Select    
      $choices = array(
        esc_html__("Basic",'schema-and-structured-data-for-wp') => array(        
          'post_type'   =>  esc_html__("Post Type",'schema-and-structured-data-for-wp'),
          'user_type'   =>  esc_html__("Logged in User Type",'schema-and-structured-data-for-wp'),
        ),
        esc_html__("Post",'schema-and-structured-data-for-wp') => array(
          'post'      =>  esc_html__("Post",'schema-and-structured-data-for-wp'),
          'post_category' =>  esc_html__("Post Category",'schema-and-structured-data-for-wp'),
          'post_format' =>  esc_html__("Post Format",'schema-and-structured-data-for-wp'), 
        ),
        esc_html__("Page",'schema-and-structured-data-for-wp') => array(
          'page'      =>  esc_html__("Page",'schema-and-structured-data-for-wp'), 
          'page_template' =>  esc_html__("Page Template",'schema-and-structured-data-for-wp'),
        ),
        esc_html__("Other",'schema-and-structured-data-for-wp') => array( 
          'ef_taxonomy' =>  esc_html__("Taxonomy Term",'schema-and-structured-data-for-wp'), 
        )
      ); 

      $comparison = array(
        'equal'   =>  esc_html__( 'Equal to', 'schema-and-structured-data-for-wp'), 
        'not_equal' =>  esc_html__( 'Not Equal to', 'schema-and-structured-data-for-wp'),     
      );

      $total_fields = count( $data_array ); ?>

      <table class="widefat">
        <tbody id="sdwp-repeater-tbody" class="fields-wrapper-1">
        <?php  for ($i=0; $i < $total_fields; $i++) {  
          $selected_val_key_1 = $data_array[$i]['key_1']; 
          $selected_val_key_2 = $data_array[$i]['key_2']; 
          $selected_val_key_3 = $data_array[$i]['key_3'];
          $selected_val_key_4 = '';
          if(isset($data_array[$i]['key_4'])){
            $selected_val_key_4 = $data_array[$i]['key_4'];
          }
          ?>
          <tr class="toclone">
            <td style="width:31%" class="post_types"> 
              <select class="widefat select-post-type <?php echo esc_attr( $i );?>" name="data_array[<?php echo esc_attr( $i) ?>][key_1]">    
                <?php 
                foreach ($choices as $choice_key => $choice_value) { ?>         
                  <option disabled class="pt-heading" value="<?php echo esc_attr($choice_key);?>"> <?php echo esc_html__($choice_key,'schema-and-structured-data-for-wp');?> </option>
                  <?php
                  foreach ($choice_value as $sub_key => $sub_value) { ?> 
                    <option class="pt-child" value="<?php echo esc_attr( $sub_key );?>" <?php selected( $selected_val_key_1, $sub_key );?> > <?php echo esc_html__($sub_value,'schema-and-structured-data-for-wp');?> </option>
                    <?php
                  }
                } ?>
              </select>
            </td>
            <td style="width:31%">
              <select class="widefat comparison" name="data_array[<?php echo esc_attr( $i )?>][key_2]"> <?php
                foreach ($comparison as $key => $value) { 
                  $selcomp = '';
                  if($key == $selected_val_key_2){
                    $selcomp = 'selected';
                  }
                  ?>
                  <option class="pt-child" value="<?php echo esc_attr( $key );?>" <?php echo esc_attr($selcomp); ?> > <?php echo esc_html__($value,'schema-and-structured-data-for-wp');?> </option>
                  <?php
                } ?>
              </select>
            </td>
            <td style="width:31%">
              <div class="insert-ajax-select">              
                <?php saswp_ajax_select_creator($selected_val_key_1, $selected_val_key_3, $i );
                if($selected_val_key_1 == 'ef_taxonomy'){
                  saswp_create_ajax_select_taxonomy($selected_val_key_3, $selected_val_key_4, $i);
                }
                ?>
                <div class="spinner"></div>
              </div>
            </td>

            <td class="widefat structured-clone" style="width:3.5%">
            <span> <button type="button"> <?php echo esc_html__('Add' ,'schema-and-structured-data-for-wp');?> </button> </span> </td>
            
            <td class="widefat structured-delete" style="width:3.5%">
            <span> <button  type="button"> <?php echo esc_html__( 'Remove' ,'schema-and-structured-data-for-wp');?> </button> </span> </td>         
          </tr>
          <?php 
        } ?>
        </tbody>
      </table>
      <br/>
      <style type="text/css">
        .option-table-class{width:100%;}
         .option-table-class tr td {padding: 10px 10px 10px 10px ;}
         .option-table-class tr > td{width: 30%;}
         .option-table-class tr td:last-child{width: 60%;}
         .option-table-class input[type="text"], select{width:100%;}
      </style>
      <table class="option-table-class">
        <tbody>
          <tr>
            <td><label for="schema_type"><?php echo esc_html__( 'Schema Type' ,'schema-and-structured-data-for-wp');?></label></td>
            <td><select id="schema_type" name="schema_type">
                <?php
                  
                  $all_schema_array = array(
                     'Blogposting' => 'Blogposting',
                     'NewsArticle' => 'NewsArticle',
                     'WebPage'     => 'WebPage',
                     'Article'     => 'Article',
                     'Recipe'      => 'Recipe',
                     'Product'     => 'Product',
                     'VideoObject' => 'VideoObject'
                 );
                  foreach ($all_schema_array as $key => $value) {
                    $sel = '';
                    if($schema_type==$key){
                      $sel = 'selected';
                    }
                    echo "<option value='".esc_attr($key)."' ".esc_attr($sel).">".esc_html__($value, 'schema-and-structured-data-for-wp' )."</option>";
                  }
                ?>
            </select></td>
          </tr>
          
          <tr>
            <td><label for="notAccessibleForFree"><?php echo esc_html__( 'Paywall', 'schema-and-structured-data-for-wp' ) ?></label></td>
            <td><input type="checkbox" id="notAccessibleForFree" name="notAccessibleForFree" value="1" <?php if(isset($schema_options['notAccessibleForFree']) && $schema_options['notAccessibleForFree']==1){echo 'checked'; }?>>
            </td>
          </tr>
          <tr <?php if(!isset($schema_options['notAccessibleForFree']) || $schema_options['notAccessibleForFree']!=1){echo 'style="display:none"'; }?>>
            <td><label for="isAccessibleForFree"><?php echo esc_html__( 'Is accessible for free', 'schema-and-structured-data-for-wp' ) ?></label></td>
            <td>
                <select name="isAccessibleForFree" id="isAccessibleForFree">
                  <option value="False" <?php if( isset($schema_options['isAccessibleForFree']) && $schema_options['isAccessibleForFree']=='False'){echo 'selected'; }?>><?php echo esc_html__( 'False', 'schema-and-structured-data-for-wp' ); ?></option>
                  <option value="True" <?php if( isset($schema_options['isAccessibleForFree']) && $schema_options['isAccessibleForFree']=='True'){echo 'selected'; }?>><?php echo esc_html__( 'True', 'schema-and-structured-data-for-wp' ); ?></option>
                </select>
            </td>
          </tr>
          <tr <?php if(!isset($schema_options['notAccessibleForFree']) || $schema_options['notAccessibleForFree']!=1){echo 'style="display:none"'; }?>>
            <td>
              <label for="paywall_class_name"><?php echo esc_html__( 'Enter the class name of paywall section', 'schema-and-structured-data-for-wp' ); ?></label>  
            </td>
            <td><input type="text" id="paywall_class_name" name="paywall_class_name" value="<?php if( isset($schema_options['paywall_class_name']) ){echo esc_attr($schema_options['paywall_class_name']); }?>"></td>
          </tr>
        </tbody>
      </table>
    <?php
  }
  add_action( 'admin_enqueue_scripts', 'saswp_style_script_include' );
  function saswp_style_script_include() {
     global $pagenow, $typenow;
    if (is_admin() && $pagenow=='post-new.php' OR $pagenow=='post.php' && $typenow=='saswp') {
       wp_register_script( 'structure_admin', plugin_dir_url(__FILE__) . '/js/structure_admin.js', array( 'jquery'), SASWP_VERSION, true );
       // Localize the script with new data
      $data_array = array(
          'ajax_url'    =>  admin_url( 'admin-ajax.php' ) 
      );
      wp_localize_script( 'structure_admin', 'amp_sdwp_field_data', $data_array );
      wp_enqueue_script('structure_admin');
    }
  }

  // Save PHP Editor
  add_action ( 'save_post' , 'saswp_select_save_data' );
  function saswp_select_save_data ( $post_id ) {      
      if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
       
      // if our nonce isn't there, or we can't verify it, bail
      if( !isset( $_POST['saswp_select_name_nonce'] ) || !wp_verify_nonce( $_POST['saswp_select_name_nonce'], 'saswp_select_action_nonce' ) ) return;
      
      // if our current user can't edit this post, bail
      if( !current_user_can( 'edit_post' ) ) return;      
      $post_data_array = array();
      
    foreach($_POST['data_array'] as $post){
    $post_data_array[] = array_map('sanitize_text_field', $post);  
    }          
    $post_schema_type =         sanitize_text_field($_POST['schema_type']);  
    $notAccessibleForFree =     sanitize_text_field($_POST['notAccessibleForFree']);    
    $isAccessibleForFree =      sanitize_text_field($_POST['isAccessibleForFree']);   
    $paywall_class_name =       sanitize_text_field($_POST['paywall_class_name']);
    $saswp_schema_options  =    array('isAccessibleForFree'=>$isAccessibleForFree,'notAccessibleForFree'=>$notAccessibleForFree,'paywall_class_name'=>$paywall_class_name);
    
    if(isset($_POST['data_array'])){
      update_post_meta(
        $post_id, 
        'data_array', 
        $post_data_array 
      );
      update_post_meta(
        $post_id, 
        'schema_type', 
        $post_schema_type 
      );
      update_post_meta(
        $post_id, 
        'schema_options', 
        $saswp_schema_options
      );
    }
  }
add_action("admin_init",'saswp_migration');
function saswp_migration(){
    $sdwp_migration_posts = get_option("sdwp_migration_posts");
    $sd_data = get_option("sd_data");
    if($sdwp_migration_posts != 'inserted'){
      if(isset($sd_data['sd_page_type'])){
        $postarr = array(
                    'post_type'=>'saswp',
                  'post_title'=>'Default page type',
                  'post_status'=>'publish',

                    );
      $insertedPageId = wp_insert_post(  $postarr );
      if($insertedPageId){
        $post_data_array  = array(
                              array(
                                  'key_1'=>'post_type',
                                  'key_2'=>'equal',
                                  'key_3'=>'page',
                                )
                              );
        $schema_options_array = array('isAccessibleForFree'=>False,'notAccessibleForFree'=>0,'paywall_class_name'=>'');
        update_post_meta( $insertedPageId, 'data_array', $post_data_array);
        update_post_meta( $insertedPageId, 'schema_type', $sd_data['sd_page_type']);
        update_post_meta( $insertedPageId, 'schema_options', $schema_options_array);
      }
    }
    if(isset($sd_data['sd_post_type'])){
      $postarr = array(
                  'post_type'=>'saswp',
                  'post_title'=>'Default post type',
                  'post_status'=>'publish',

                    );
      $insertedPageId = wp_insert_post(  $postarr );
      if($insertedPageId){
        $post_data_array  = array(
                              array(
                                  'key_1'=>'post_type',
                                  'key_2'=>'equal',
                                  'key_3'=>'post',
                                )
                              );
        $schema_options_array = array('isAccessibleForFree'=>False,'notAccessibleForFree'=>0,'paywall_class_name'=>'');
        update_post_meta( $insertedPageId, 'data_array', $post_data_array);
        update_post_meta( $insertedPageId, 'schema_type', $sd_data['sd_post_type']);
        update_post_meta( $insertedPageId, 'schema_options', $schema_options_array);
      }
    }

    update_option( "sdwp_migration_posts", "inserted");

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

add_action('wp_head','saswp_custom_breadcrumbs',99);

// Breadcrumbs
function saswp_custom_breadcrumbs() {
    global $sd_data;	
    $variables1_titles = array();   
    $variables2_links = array();   
    // Settings
    $separator          = '&gt;';        
    $home_title         = esc_html__('Homepage', 'schema-and-structured-data-for-wp' );
      
    // If you have any custom post types with custom taxonomies, put the taxonomy name below (e.g. product_cat)
    $custom_taxonomy    = 'product_cat';
       
    // Get the query & post information
    global $post;
       
    // Do not display on the homepage
    if ( !is_front_page() ) {
       
        // Build the breadcrums
        // Home page
        $variables1_titles[] = $home_title;
        $variables2_links[] = get_home_url();


        if ( is_archive() && !is_tax() && !is_category() && !is_tag() && !is_author() ) {
            $archive_title = post_type_archive_title($prefix, false);
              $variables1_titles[] = $archive_title;


        } else if  ( is_author() ) {
	    		global $author;
	    		
	            $userdata = get_userdata( $author ); 
	            $author_url= get_author_posts_url($userdata->ID);

	            // author name
	            $variables1_titles[]= $userdata->display_name;
	            $variables2_links[]= $author_url;

        } else if ( is_archive() && is_tax() && !is_category() && !is_tag() ) {
              
            // If post is a custom post type
            $post_type = get_post_type();
              
            // If it is a custom post type display name and link
            if($post_type != 'post') {
                  
                $post_type_object = get_post_type_object($post_type);
                $post_type_archive = get_post_type_archive_link($post_type);
                $variables1_titles[] = $post_type_object->labels->name;
                $variables2_links[] = $post_type_archive;
              
            }
              
            $custom_tax_name = get_queried_object()->name;
              $variables1_titles[] = $custom_tax_name;

        } else if ( is_single() ) {
              
            // If post is a custom post type
            $post_type = get_post_type();
              
            // If it is a custom post type display name and link
            if($post_type != 'post') {
                  
                $post_type_object = get_post_type_object($post_type);
                $post_type_archive = get_post_type_archive_link($post_type);
              
                $variables1_titles[]= $post_type_object->labels->name;
                $variables2_links[]= $post_type_archive;              
            }
              
            // Get post category info
            $category = get_the_category();
             
            if(!empty($category)) {
              $category_values = array_values( $category );
              foreach ($category_values as $category_value) {
                  $category_name = get_category($category_value);
                  $cat_name = $category_name->name;
                  $variables1_titles[]=$cat_name;
                  $variables2_links[]=get_category_link( $category_value );
              
              }
                // Get last category post is in
                $last_category = end(($category));
                  $category_name = get_category($last_category);
                // Get parent any categories and create array
                $get_cat_parents = rtrim(get_category_parents($last_category->term_id, true, ','),',');
                $cat_parents = explode(',',$get_cat_parents);
                  
                // Loop through parent categories and store in variable $cat_display
                $cat_display = '';
                foreach($cat_parents as $parents) {
                    $cat_display .= '<li class="item-cat">'.esc_html__( $parents, 'schema-and-structured-data-for-wp' ).'</li>';
                    $cat_display .= '<li class="separator"> ' . esc_html__( $separator, 'schema-and-structured-data-for-wp' ) . ' </li>';
                }
            }
              
            // If it's a custom post type within a custom taxonomy
            $taxonomy_exists = taxonomy_exists($custom_taxonomy);
            if(empty($last_category) && !empty($custom_taxonomy) && $taxonomy_exists) {
                   
                $taxonomy_terms = get_the_terms( $post->ID, $custom_taxonomy );
                $cat_id         = $taxonomy_terms[0]->term_id;                
                $cat_link       = get_term_link($taxonomy_terms[0]->term_id, $custom_taxonomy);
                $cat_name       = $taxonomy_terms[0]->name;

            }
              
             if(!empty($cat_id)) {
              $variables1_titles[]= $cat_name;
              $variables2_links[]=$cat_link;

            } else {
                if($post_type == 'post') { 
                     $variables1_titles[]= get_the_title();
                }
            }
              
        } else if ( is_category() ) {
                $category = get_the_category();
             
            if(!empty($category)) {
              $category_values = array_values( $category );
              foreach ($category_values as $category_value) {
                  $category_name = get_category($category_value);
                  $cat_name = $category_name->name;
                  $variables1_titles[]=$cat_name;
                  $variables2_links[]=get_category_link( $category_value );
              
              }
          }                          
        } else if ( is_page() ) {
               
            // Standard page
            if( $post->post_parent ){
                   
                // If child page, get parents 
                $anc = get_post_ancestors( $post->ID );
                   
                // Get parents in the right order
                $anc = array_reverse($anc);
                   
                // Parent page loop
                if ( !isset( $parents ) ) $parents = null;
                foreach ( $anc as $ancestor ) {
                    $parents .= '<li class="item-parent item-parent-' . esc_attr($ancestor) . '"><a class="bread-parent bread-parent-' . esc_attr($ancestor) . '" href="' . esc_url(get_permalink($ancestor)) . '" title="' . esc_attr(get_the_title($ancestor)) . '">' . esc_html__(get_the_title($ancestor), 'schema-and-structured-data-for-wp' ) . '</a></li>';
                    $parents .= '<li class="separator separator-' . esc_attr($ancestor) . '"> ' . esc_html__($separator, 'schema-and-structured-data-for-wp' ) . ' </li>';
                    $variables1_titles[]= get_the_title($ancestor);
                    $variables2_links[]=get_permalink($ancestor);
                }
             
                    $variables1_titles[]= get_the_title();
                    $variables2_links[]=get_permalink();
                   
            } else {                                                  
                   $variables1_titles[]=get_the_title();
                   $variables2_links[]=get_permalink();
            }
               
        } else if ( is_tag() ) {               
            // Tag page               
            // Get tag information
            $term_id        = get_query_var('tag_id');
            $taxonomy       = 'post_tag';
            $args           = 'include=' . $term_id;
            $terms          = get_terms( $taxonomy, $args );
            $get_term_id    = $terms[0]->term_id;            
            $get_term_name  = $terms[0]->name;
            $term_link      = get_term_link($get_term_id );
               
            // Tag name and link

            $variables1_titles[] = $get_term_name;
            $variables2_links[] = $term_link;           
          }         
          $sd_data['titles']= $variables1_titles;
          $sd_data['links']= $variables2_links;         
    }
       
}
