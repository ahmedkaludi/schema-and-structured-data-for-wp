<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class SASWP_Rest_Api_Service {

    private $migration_service = null;
    private $output_service    = null;

    public function __construct() {
                
        if($this->output_service == null){
          
            $this->output_service = new saswp_output_service();

        }
                            
    }

    public function getSettings(){

      global $sd_data;
      
      $response      = array();
      $resultset     = array();
      $mappings_file = SASWP_DIR_NAME . '/core/array-list/compatibility-list.php';
      $saswp_option  = $sd_data;

      
      if ( file_exists( $mappings_file ) ) {

                $plugins_arr = include $mappings_file;                

                foreach ($plugins_arr['plugins'] as $key =>  $plugins){

                  $int_arr = array();

                  $int_arr['name']     = $plugins['name'];
                  $int_arr['opt_name'] = $plugins['opt_name'];
                  $int_arr['part_in']  = $plugins['part_in'];
                  $int_arr['active']   = false;
                  $int_arr['status']   = isset($saswp_option[$plugins['opt_name']]) ? $saswp_option[$plugins['opt_name']] : false;
                  if(is_plugin_active($plugins['free']) || (isset($plugins['pro']) && is_plugin_active($plugins['pro']))){
                    
                    $int_arr['active']  = true;
                    
                  }

                  $response[] = $int_arr;

                  unset($saswp_option[$plugins['opt_name']]);

                }

                foreach ($plugins_arr['themes'] as $key =>  $plugins){
                  
                  $int_arr = array();

                  $int_arr['name']     = $plugins['name'];
                  $int_arr['opt_name'] = $plugins['opt_name'];
                  $int_arr['part_in']  = $plugins['part_in'];
                  $int_arr['active']   = false;
                  $int_arr['status']   = isset($saswp_option[$plugins['opt_name']]) ? $saswp_option[$plugins['opt_name']] : false;
                  if(get_template() == $plugins['free'] || (isset($plugins['pro']) && get_template() ==$plugins['pro'])){
                    
                    $int_arr['active']  = true;
                    
                  }

                  $response[] = $int_arr;

                  unset($saswp_option[$plugins['opt_name']]);

                }

                $resultset['settings']      = $saswp_option;
                $resultset['compatibility'] = $response;
      }
      
      return $resultset;
    }
    public function importFromFile($import_file){
      
        global $wpdb;
        
        $result          = null;
        $errorDesc       = array();
        $all_schema_post = array();

        $json_data       = @file_get_contents($import_file);

        if($json_data){
            
          $json_array      = json_decode($json_data, true);   
      
          $posts_data      = $json_array['posts'];                   
                      
          if($posts_data){  
              
          foreach($posts_data as $data){
                  
          $all_schema_post = $data;                   
                              
          $schema_post = array();                     
             
          if($all_schema_post && is_array($all_schema_post)){
          // begin transaction
          $wpdb->query('START TRANSACTION');
          
          foreach($all_schema_post as $schema_post){  
                            
              $post_meta =     $schema_post['post_meta'];   
              
              if(saswp_post_exists($schema_post['post']['ID'])){
                  
                  $post_id    =     wp_update_post($schema_post['post']);  
                   
              }else{
                  
                  unset($schema_post['post']['ID']);
                  
                  $post_id    =     wp_insert_post($schema_post['post']); 
                  
                  if($post_meta){
                      
                      foreach($post_meta as $key => $val){

                        $explod_key = explode("_",$key);

                        $exp_count  = count($explod_key);

                        $explod_key[($exp_count-1)] = $post_id;

                        $explod_key = implode("_", $explod_key);

                        $post_meta[$explod_key] = $val;

                    }  
                      
                  }
                                      
              }
                                                                                        
              foreach($post_meta as $key => $meta){
                  
                  $meta = wp_unslash($meta);
                  
                  if(is_array($meta)){    
                      
                      $meta = wp_unslash($meta);
                      update_post_meta($post_id, $key, $meta);
                      
                  }else{
                      update_post_meta($post_id, $key, sanitize_text_field($meta));
                  }
                                                          
              }
                                                                                                                  
              if(is_wp_error($post_id)){
                  $errorDesc[] = $result->get_error_message();
              }
              } 
              
              }      
                                      
             }
              
          }            
          //Saving settings data starts here
          if(array_key_exists('sd_data', $json_array)){
              
              $saswp_sd_data = $json_array['sd_data'];
              
              foreach($saswp_sd_data as $key => $val){
                  
                  if(is_array($val)){
                      
                      $saswp_sd_data[$key] = $meta = array_map( 'sanitize_text_field' ,$val);   
                      
                  }else{
                      
                      $saswp_sd_data[$key] = sanitize_text_field($val);
                      
                  }
                  
              }
              
              update_option('sd_data', $saswp_sd_data); 
          } 
          //Saving settings data ends here             
           update_option('saswp-file-upload_url','');
          
      }
                                   
      if ( count($errorDesc) ){
        echo implode("\n<br/>", $errorDesc);              
        $wpdb->query('ROLLBACK');             
      }else{
        $wpdb->query('COMMIT'); 
        return true;
      }

    }
    public function searchPostMeta($search_string){
                                          
            $data          = array();            
            
            global $wpdb;

	          $saswp_meta_array = $wpdb->get_results( "SELECT DISTINCT meta_key FROM {$wpdb->postmeta} WHERE meta_key LIKE '%{$search_string}%'", ARRAY_A ); // WPCS: unprepared SQL OK.         
              if ( isset( $saswp_meta_array ) && ! empty( $saswp_meta_array ) ) {
                  
                foreach ( $saswp_meta_array as $value ) {
      
                    $data[] = array(
                      'value'   => $value['meta_key'],
                      'label' => preg_replace( '/^_/', '', esc_html( str_replace( '_', ' ', $value['meta_key'] ) ) ),
                    );
      
                }
                                        
              }
                                    
            return $data;  

    }
    public function getConditionList($condition, $search = null,$saved_data = null, $diff = null){

        $choices = array();    
    
        switch($condition){
        
          case "post_type":
            
              $post_type = array();
              $args['public'] = true;
                
              if(!empty($search) && $search != null){                
                $args['name'] = $search; 
              }              
              $choices = get_post_types( $args, 'names', 'and' );    
              unset($choices['attachment'], $choices['amp_acf'], $choices['quads-ads']);

              if($choices){
                foreach($choices as $key =>$value){
                  $post_type[] = array('label' => $value, 'value' => $key);
                }
              }

              $choices = $post_type;
                                
            break;                         

          case "page_template" :

            $choices[] = array('label' => 'Default Template', 'value' => 'default');

            $templates = get_page_templates();
            
            if($templates){
                
                foreach($templates as $k => $v){
                                 
                     $choices[] = array('label' => $k, 'value' => $v);
              
                }
                
            }
            
            break;

          case "post" :
          case "page" :
            
            if($condition == 'page'){

              $post_types['page'] = 'page';

            }else{

              $post_types = get_post_types();                        
              unset( $post_types['page'], $post_types['attachment'], $post_types['revision'] , $post_types['nav_menu_item'], $post_types['acf'] , $post_types['amp_acf'],$post_types['saswp']  );

            }

            if( $post_types )
            {
              foreach( $post_types as $post_type ){
              
                $arg['post_type']      = $post_type;
                $arg['posts_per_page'] = 50;  
                $arg['post_status']    = 'any'; 

                if(!empty($search)){
                  $arg['s']              = $search;
                }

                if($saved_data){
                  $arg['p'] = $saved_data;  
                }
		            
                $posts = $this->getPostsByArg($arg); 
                
                if(isset($posts['posts_data'])){
                                
                  foreach($posts['posts_data'] as $post){                                                          
                    
                    $choices[] = array('value' => $post['post']['post_id'], 'label' => $post['post']['post_title']);

                  }
                  
                }
                
              }
              
            }
            
            break;

          case "post_category" :

            $args = array( 
                        'hide_empty' => false,
                        'number'     => 10, 
                      );

            if(!empty($search)){
              $args['name__like'] = $search;
            }         

            $terms = get_terms( 'category', $args);

            if( !empty($terms) ) {

              foreach( $terms as $term ) {

                $choices[] = array('value' => $term->term_id, 'label' => $term->name);                

              }

            }

            break;

          case "user_type" :
          case "post_format" :
          case "taxonomy" :  
          case "general":          
              $general_arr = array();
            if($condition == 'post_format'){
              $choices = get_post_format_strings();
            }else if($condition == 'user_type'){
              global $wp_roles;
              
              $choices = $wp_roles->get_names();            
  
              if( is_multisite() ){
              
                $choices['super_admin'] = esc_html__('Super Admin','schema-and-structured-data-for-wp');
                
              }
            }else if($condition == 'taxonomy'){

              $choices    = array('all' => esc_html__('All','schema-and-structured-data-for-wp'));
              $taxonomies = $this->quads_post_taxonomy_generator();        
              $choices    = array_merge($choices, $taxonomies);

            }else{
                $choices = array(
                  'homepage'      => 'HomePage',
                  'show_globally' => 'Show Globally',                                
                ); 
            }                          

            if(!empty($search) && $search != null){

                $search_user = array();

                foreach($choices as $key => $val){
                  if((strpos($key, $search) !== false) || strpos($key, $val) !== false){
                    $search_user[$key] = $val; 
                  }
                }

                $choices = $search_user;
            }            

            if($choices){
              foreach($choices as $key =>$value){
                $general_arr[] = array('label' => $value, 'value' => $key);
              }
            }

            $choices = $general_arr;
            
            break;

          case "tags" :

            $args = array( 
              'hide_empty' => false,
              'number'     => 10, 
            );

            if(!empty($search)){
              $args['name__like'] = $search;
            }         

            $taxonomies = $this->quads_post_taxonomy_generator();

            foreach($taxonomies as $key => $val){

              if(strpos($key, 'tag') !== false){
                
                $terms = get_terms( $key, $args);

                if( !empty($terms) ) {

                  foreach( $terms as $term ) {
                    
                   $choices[] = array('value' => $term->slug, 'label' => $term->name);                    
                    
                  }
    
                }

              }

            }
                        
            break;  
        }        
    
     return $choices;
    }

    public function quads_post_taxonomy_generator(){
    
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

    public function getCollectionById($review_id){

      $response  = array();
      $meta_data = array();

      if($review_id){
          
          $post_meta             = get_post_meta($review_id, '', true);  

          if($post_meta){

              foreach($post_meta as $key => $meta){
                
                  if(is_serialized($meta[0])){
                    $meta_data[$key] = unserialize($meta[0]);
                  }else{
                    $meta_data[$key] = $meta[0];
                  }
                  
              }
          }

          $meta_data['saswp_collection_title'] =   get_the_title($review_id);  
          $response['post_meta'] = $meta_data;
          
      }        
      return $response;

  }


    public function getReviewById($review_id){

      $response  = array();
      $meta_data = array();

      if($review_id){

          $response['post']      = get_post($review_id, ARRAY_A);  
          $post_meta             = get_post_meta($review_id, '', true);  

          if($post_meta){

              foreach($post_meta as $key => $meta){
                  if(is_serialized($meta[0])){
                    $meta_data[$key] = unserialize($meta[0]);
                  }else{
                    $meta_data[$key] = $meta[0];
                  }
                  
              }
          }

          $response['post_meta'] = $meta_data;
          
      }        
      return $response;

  }

    public function getSchemaDataByType($schema_type){

      $response = array();

      $meta_fields      = $this->output_service->saswp_get_all_schema_type_fields($schema_type);
      $meta_list_fields = include(SASWP_DIR_NAME . '/core/array-list/meta_list.php'); 

      $choices    = array('all' => esc_html__('All','schema-and-structured-data-for-wp'));
      $taxonomies = saswp_post_taxonomy_generator();
      $choices    = array_merge($choices, $taxonomies); 
      
      $response = array(
                  'meta_fields'      => $meta_fields, 
                  'meta_list_fields' => $meta_list_fields,
                  'taxonomies'       => $choices,
                );

      return $response;

    }

    public function getSchemaById($schema_id){

        $response  = array();
        $meta_data = array();

        if($schema_id){

            $response['post']      = get_post($schema_id, ARRAY_A);  
            $post_meta             = get_post_meta($schema_id, '', true);  

            if($post_meta){

                foreach($post_meta as $key => $meta){
                    if(is_serialized($meta[0])){
                      $meta_data[$key] = unserialize($meta[0]);
                    }else{
                      $meta_data[$key] = $meta[0];
                    }
                    
                }
            }

            $response['post_meta'] = $meta_data;
            
        }        
        return $response;

    }

    public function getCollectionsList($post_type, $attr = null, $rvcount = null, $paged = null, $offset = null, $search_param=null){
            
      $response   = array();                                
      $arg        = array();
      $meta_query = array();
      $posts_data = array();
      
      $arg['post_type']      = $post_type;
      $arg['posts_per_page'] = -1;  
      $arg['post_status']    = 'any';    
          
      
      if(isset($attr['in'])){
        $arg['post__in']    = $attr['in'];  
      }                    
      if(isset($attr['id'])){
        $arg['attachment_id']    = $attr['id'];  
      }
      if(isset($attr['title'])){
        $arg['title']    = $attr['title'];  
      }          
      
      if($rvcount){
          $arg['posts_per_page']    = $rvcount;
      }
      if($paged){
          $arg['paged']    = $paged;
      }
      if($offset){
          $arg['offset']    = $offset;
      }       
                    
      $response = $this->getPostsByArg($arg);
      
      return $response;
  }

  public function getReviewsList($post_type, $attr = null, $rvcount = null, $paged = null, $offset = null, $search_param=null){
            
    $response   = array();                                
    $arg        = array();
    $meta_query = array();
    $posts_data = array();
    
    $arg['post_type']      = $post_type;
    $arg['posts_per_page'] = -1;  
    $arg['post_status']    = 'any';    
        
    
    if(isset($attr['in'])){
      $arg['post__in']    = $attr['in'];  
    }                    
    if(isset($attr['id'])){
      $arg['attachment_id']    = $attr['id'];  
    }
    if(isset($attr['title'])){
      $arg['title']    = $attr['title'];  
    }          
    
    if($rvcount){
        $arg['posts_per_page']    = $rvcount;
    }
    if($paged){
        $arg['paged']    = $paged;
    }
    if($offset){
        $arg['offset']    = $offset;
    }       
                  
    $response = $this->getPostsByArg($arg);
    
    return $response;
}
    public function getSchemaList($post_type, $attr = null, $rvcount = null, $paged = null, $offset = null, $search_param=null){
            
        $response   = array();                                
        $arg        = array();
        $meta_query = array();
        $posts_data = array();
        
        $arg['post_type']      = $post_type;
        $arg['posts_per_page'] = -1;  
        $arg['post_status']    = 'any';    
            
        
        if(isset($attr['in'])){
          $arg['post__in']    = $attr['in'];  
        }                    
        if(isset($attr['id'])){
          $arg['attachment_id']    = $attr['id'];  
        }
        if(isset($attr['title'])){
          $arg['title']    = $attr['title'];  
        }          
        
        if($rvcount){
            $arg['posts_per_page']    = $rvcount;
        }
        if($paged){
            $arg['paged']    = $paged;
        }
        if($offset){
            $arg['offset']    = $offset;
        }       
                        
        $response = $this->getPostsByArg($arg);
        
        return $response;
    }

    

    public function getPostsByArg($arg){
      
      $response = array();

      $meta_query = new WP_Query($arg);        
              
        if($meta_query->have_posts()) {
             
            $data = array();  
            $post_meta = array();        
            while($meta_query->have_posts()) {
                $meta_query->the_post();
                $data['post_id']       =  get_the_ID();
                $data['post_title']    =  get_the_title();
                $data['post_status']   =  get_post_status();
                $data['post_modified'] =  get_the_date('d M, Y');
                $post_meta             = get_post_meta(get_the_ID(), '', true);

                if($post_meta){
                    foreach($post_meta as $key => $val ){
                        $post_meta[$key] = $val[0];
                    }
                }
                                
                if(isset($post_meta['saswp_platform_ids'])){

                  $post_meta['saswp_platform_ids'] = unserialize($post_meta['saswp_platform_ids']);
                  
                }
                if(isset($post_meta['saswp_total_reviews'])){

                  $post_meta['saswp_total_reviews'] = unserialize($post_meta['saswp_total_reviews']);

                  $rv_images = array();
                  $k = 1; 
                  foreach ($post_meta['saswp_total_reviews'] as $value) {
                    
                    $image = get_post_meta( $value, $key='saswp_reviewer_image', true);  
                    
                    $rv_images[] = $image;

                    if($k == 3){
                      break;
                    }
                    $k++;
                  }

                  $post_meta['saswp_collection_images'] = $rv_images;

                }

                if(isset($post_meta['saswp_review_platform'])){

                  $platform = get_post_meta( get_the_ID(), $key='saswp_review_platform', true);  
                  $term     = get_term( $platform, 'platform' );

                  if(isset($term->slug)){
                        
                    if($term->slug == 'self'){
                        
                         $service_object     = new saswp_output_service();
                         $default_logo       = $service_object->saswp_get_publisher(true);                                                         
                         
                         if(isset($default_logo['url'])){
                        
                          $post_meta['saswp_review_platform_image'] =  $default_logo['url'];
                             
                         }
                        
                    }else{
                         $post_meta['saswp_review_platform_image'] = SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/'.esc_attr($term->slug).'-img.png';
                    }
                                            
                }

                }

                $posts_data[] = array(
                'post'        => (array) $data,
                'post_meta'   => $post_meta                
                ); 

            }
            wp_reset_postdata(); 
            $response['posts_data']  = $posts_data;
            $response['posts_found'] = $meta_query->found_posts;
        }

        return $response;

    }

    public function updateSettings($parameters){

        
        $settings      = json_decode($parameters['settings'], true);
        $compatibility = json_decode($parameters['compatibility'], true);
        
        if($compatibility){
          foreach ($compatibility as $value) {
            $settings[$value['opt_name']] = $value['status'];
          }
        }
        
        $response = false;

        if($settings){

          $saswp_options = get_option('sd_data');
          
          foreach($settings as $key => $val){
            $saswp_options[$key] = $val;
          }
          
         $response =  update_option( 'sd_data', $saswp_options );

        }

        return $response;
    }

    public function validateAdsTxt($content){
        
        $sanitized = array();
        $errors    = array();  

        if($content){
          
          $lines     = preg_split( '/\r\n|\r|\n/', $content );                  

            foreach ( $lines as $i => $line ) {
              $line_number = $i + 1;
              $result      = quads_validate_ads_txt_line( $line, $line_number );
          
              $sanitized[] = $result['sanitized'];
              if ( ! empty( $result['errors'] ) ) {
                $errors = array_merge( $errors, $result['errors'] );
              }
            }               
            $sanitized = implode( PHP_EOL, $sanitized );             
        }        
        return array('errors' => $errors, 'sanitized_content' => $sanitized);

    }
    public function updateSchema($parameters){
            
            $post_meta      = $parameters['post_meta'];                                                                   
            
            $schema_id      = isset($parameters['schema_id']) ? $parameters['schema_id'] : '';                 
            $post_status    = 'publish';            

            if(isset($parameters['status'])){
              $post_status    = $parameters['status'];   
            }
            
            $arg = array(
                'post_title'   => sanitize_text_field( $post_meta['schema_type']),                                                            
                'post_status'  => sanitize_text_field($post_status),
                'post_type'    => 'saswp',
            );
                         
            if($schema_id){                

                $arg['post_id'] = $schema_id;
                
                @wp_update_post( $arg );                

            }else{                
              $schema_id = wp_insert_post( $arg );
            }                        
            
            if($post_meta){
                
                foreach($post_meta as $key => $val){
                    
                    $filterd_meta = saswp_sanitize_post_meta($key, $val);

                    update_post_meta($schema_id, $key, $filterd_meta);
                }                               
            }
            
            return  $schema_id;
    }

    public function updateCollection($parameters){

      $post_meta          = $parameters['post_meta'];                                                                   
      
      $collection_id      = isset($parameters['collection_id']) ? $parameters['collection_id'] : '';                 
            
      $arg = array(
          'post_title'   => $post_meta['saswp_collection_title'],
          'post_name'    => $post_meta['saswp_collection_title'],
          'post_status'  => 'publish',
          'post_type'    => 'saswp-collections',
      );
                   
      if($collection_id){                

          $arg['post_id'] = $collection_id;
          
          @wp_update_post( $arg );                

      }else{                
        $collection_id = wp_insert_post( $arg );
      }                        
            
      if($post_meta){
          
          foreach($post_meta as $key => $val){
              
              $filterd_meta = saswp_sanitize_post_meta($key, $val);

              update_post_meta($collection_id, $key, $filterd_meta);
          }                               
      }
      
      return  $collection_id;
      
    }
    public function updateReview($parameters){
            
      $post_meta      = $parameters['post_meta'];                                                                   
      $schema_id      = isset($parameters['review_id']) ? $parameters['review_id'] : '';                 
      $post_status    = 'publish';            

      if(isset($parameters['status'])){
        $post_status    = $parameters['status'];   
      }
      
      $arg = array(
          'post_title'   => $post_meta['saswp_reviewer_name'],
          'post_status'  => sanitize_text_field($post_status),
          'post_type'    => 'saswp_reviews',
      );
                   
      if($schema_id){                

          $arg['post_id'] = $schema_id;
          
          @wp_update_post( $arg );                

      }else{                
        $schema_id = wp_insert_post( $arg );
      }                        
            
      if($post_meta){
          
          foreach($post_meta as $key => $val){
              
              $filterd_meta = saswp_sanitize_post_meta($key, $val);

              update_post_meta($schema_id, $key, $filterd_meta);
          }                               
      }
      
      return  $schema_id;
} 
    
    public function changePostStatus($ad_id, $action){

      $response = wp_update_post(array(
                    'ID'            =>  $ad_id,
                    'post_status'   =>  $action
                  ));

      return $response;
      
    }
    public function duplicatePost($ad_id){

      $response = null;

      global $wpdb;
      $post = get_post( $ad_id);
     
      if ( isset( $post ) && $post != null ) {        
         // args for new post
          $args = array(
            'post_title'       => $post->post_title,                        
            'post_status'      => $post->post_status,
            'post_type'        => $post->post_type,            
            ); 

          $new_post_id = wp_insert_post( $args );
          
          $post_metas = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$ad_id");
          
          if ( count( $post_metas )!=0 ) {
 
            $sql_query = "INSERT INTO $wpdb->postmeta ( post_id, meta_key, meta_value ) ";
           
            foreach ( $post_metas as $post_meta ) {
           
             $meta_key = $post_meta->meta_key;

             if( $meta_key == '_wp_old_slug' ) continue;

                if($meta_key == 'ad_id'){
                  $meta_value = addslashes( $new_post_id);
                }else{
                  $meta_value = addslashes( $post_meta->meta_value);
                }
                $sql_query_sel[]= "SELECT $new_post_id, '$meta_key', '$meta_value'";
             }
           
             $sql_query.= implode(" UNION ALL ", $sql_query_sel);
             $wpdb->query( $sql_query );
           
            }
            $response = $new_post_id;
      }

      return $response;
    }

    public function deletePost($ad_id){
      $response = wp_delete_post($ad_id, true);
      return $response; 
    }    
    public function getPlugins($search){

      $response = array();  
      $response[] = array('value' => 'woocommerce', 'label' => 'woocommerce');
      $response[] = array('value' => 'buddypress', 'label' => 'buddypress');        
      return $response;          

    }  
                 
}