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
        $choices       = array();    
        $saved_choices = array();

        $options['param'] = $response;
      
        if($options['param'] == "page_parent")
        {
          $options['param'] = "page";
        }

          $choices = saswp_get_condition_list($response);
          
          if($saved_data){            
            $saved_choices = saswp_get_condition_list($response, '', $saved_data);                        
          }
                               
          $output = '<select data-type="'.esc_attr($response).'"  class="widefat ajax-output saswp-select2" name="data_group_array[group-'.esc_attr($current_group_number).'][data_array]['. esc_attr($current_number) .'][key_3]">'; 
          
          foreach ($choices as $value) {              
           $output .= '<option value="' . esc_attr($value['id']) .'"> ' .  esc_html__($value['text'], 'schema-and-structured-data-for-wp') .'</option>';                     
          }
          
          if($saved_choices){
            foreach($saved_choices as $value){
              $output .= '<option value="' . esc_attr($value['id']) .'" selected> ' .  esc_html__($value['text'], 'schema-and-structured-data-for-wp') .'</option>';                     
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
    $taxonomies    = array();
    $saved_choices = array();

    $taxonomies = saswp_get_condition_list($selectedParentValue);       

    if($selectedValue){            
      $saved_choices = saswp_get_condition_list($selectedParentValue, '', $selectedValue);                              
    }
                 
    $choices = '<option value="all">'.esc_html__('All','schema-and-structured-data-for-wp').'</option>';
    
    if(!empty($taxonomies)){
        
        foreach($taxonomies as $taxonomy) {                    
            $choices .= '<option value="'.esc_attr($taxonomy['id']).'">'.esc_html__($taxonomy['text'],'schema-and-structured-data-for-wp').'</option>';                                    
        }
    
        if($saved_choices){
          foreach($saved_choices as $value){
            $choices .= '<option value="' . esc_attr($value['id']) .'" selected> ' .  esc_html__($value['text'], 'schema-and-structured-data-for-wp') .'</option>';                     
          }
        }   

    $allowed_html = saswp_expanded_allowed_tags();  
    
    echo '<select data-type="'.esc_attr($selectedParentValue).'" class="widefat ajax-output-child saswp-select2" name="data_group_array[group-'. esc_attr($current_group_number) .'][data_array]['.esc_attr($current_number).'][key_4]">'. wp_kses($choices, $allowed_html).'</select>';
        
    }    
    
    if($is_ajax){
      die;
    }
}