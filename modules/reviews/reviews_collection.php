<?php
/**
 * Reviews Collection  Class
 *
 * @author   Magazine3
 * @category Admin
 * @path     reviews/reviews_collection
 * @Version 1.9.17
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class SASWP_Reviews_Collection {
        
        /**
         * Static private variable to hold instance this class
         * @var type 
         */
        private static $instance;
        private $_service = null;

        private function __construct() {
            
          if($this->_service == null){
              
              $this->_service = new saswp_reviews_service();
              
          }  
             
          add_filter( 'get_edit_post_link', array($this, 'saswp_set_collection_edit_link' ), 99, 3); 
          add_action( 'admin_menu', array($this, 'saswp_add_collection_menu_links' ),20);
          add_action( 'init', array($this, 'saswp_register_collection_post_type' ),20);
          add_action( 'admin_init', array($this, 'saswp_save_collection_data' ));
          add_action( 'wp_ajax_saswp_add_to_collection', array($this, 'saswp_add_to_collection' ));
          add_action( 'wp_ajax_saswp_get_collection_platforms', array($this, 'saswp_get_collection_platforms' ));
                                 
        }
        
         /**
         * Return the unique instance 
         * @return type instance
         * @since version 1.9.17
         */
        public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
        }
        
        public static function saswp_add_collection_menu_links(){
            
             add_submenu_page( 'edit.php?post_type=saswp',
                esc_html__( 'Structured Data', 'schema-and-structured-data-for-wp' ),
                esc_html__( '', 'schema-and-structured-data-for-wp' ),
                'manage_options',
                'collection',
                array($this, 'saswp_admin_collection_interface_render'));   
            
        }
        
        public static function saswp_set_collection_edit_link($link, $post_id, $context){
            
            if(is_admin()){
                
                if ( (isset(get_current_screen()->id) && get_current_screen()->id == 'edit-saswp-collections' ) && $context == 'display') {

                        return wp_nonce_url(admin_url('admin.php?post_id='.$post_id.'&page=collection'), '_wpnonce');

                } else {

                        return $link;

                }
                
           }
            
        }
        
        public function saswp_register_collection_post_type(){
                        
            $collections = array(
                    'labels' => array(
                        'name' 			=> esc_html__( 'Collections', 'schema-and-structured-data-for-wp' ),	        
                        'add_new' 		=> esc_html__( 'Add Collection', 'schema-and-structured-data-for-wp' ),
                        'add_new_item'  	=> esc_html__( 'Edit Collection', 'schema-and-structured-data-for-wp' ),
                        'edit_item'             => esc_html__( 'Edit Collection','schema-and-structured-data-for-wp'),                
                    ),
                    'public' 		    => true,
                    'has_archive' 	    => true,
                    'exclude_from_search'   => true,
                    'publicly_queryable'    => true,
                    //'show_in_menu'          => 'edit.php?post_type=saswp',                
                    'show_in_menu'          => false,                
                    'show_ui'               => true,
                    'show_in_nav_menus'     => true,			
                    'show_admin_column'     => true,        
                    'rewrite'               => false,  
            );
            register_post_type( 'saswp-collections', $collections );   
        }
        
        public function saswp_get_collection_platforms(){
                        
            if ( ! isset( $_GET['saswp_security_nonce'] ) ){
                return; 
            }
            if ( !wp_verify_nonce( $_GET['saswp_security_nonce'], 'saswp_ajax_check_nonce' ) ){
               return;  
            }
            
            $collection_id = intval($_GET['collection_id']);            
            
            if($collection_id){
                
            $reviews_list = get_post_meta($collection_id, 'saswp_platform_ids', true);
             
            if($reviews_list){
                
                echo json_encode(array('status' => true, 'message'=> $reviews_list));
                                                  
            }else{
                
                echo json_encode(array('status' => false, 'message'=> 'Data not found'));
                
            }
                                         
            }else{
                
                echo json_encode(array('status' => false, 'message'=> 'Collection id is missing'));
                
            }
                        
            wp_die();
        }
        
        public function saswp_add_to_collection(){
                        
            if ( ! isset( $_GET['saswp_security_nonce'] ) ){
                return; 
            }
            if ( !wp_verify_nonce( $_GET['saswp_security_nonce'], 'saswp_ajax_check_nonce' ) ){
               return;  
            }
            
            $platform_id = intval($_GET['platform_id']);
            $rvcount     = intval($_GET['rvcount']);
            
            if($platform_id  && $rvcount){
                                
            $reviews_list = $this->_service->saswp_get_reviews_list_by_parameters(null, $platform_id, $rvcount); 
             
            if($reviews_list){
                
                echo json_encode(array('status' => true, 'message'=> $reviews_list));
                                                  
            }else{
                
                echo json_encode(array('status' => false, 'message'=> 'Data not found'));
                
            }
                                         
            }else{
                
                echo json_encode(array('status' => false, 'message'=> 'Platform id or review count is missing'));
                
            }
                        
            wp_die();
        }
        
        public function saswp_admin_collection_interface_render(){
         
            $post_meta = array();
            $post_id   = null;            

            if(isset($_GET['post_id'])){

                $post_id = intval($_GET['post_id']);

                $post_meta = get_post_meta($post_id, $key='', true );            


            } else{

                $post    = get_default_post_to_edit( 'saswp-google-review', true );
                $post_id = intval($post->ID);
            }
            
            $coll_desing = array(
                'grid'     => 'Grid',
                'gallery'  => 'Gallery',
                'badge'    => 'Badge',
                'popup'    => 'Popop',
                'fomo'     => 'Fomo',
            );
       
            $coll_sorting = array(
                'recent'     => 'Recent',
                'oldest'     => 'Oldest',
                'newest'     => 'Newest',
                'highest'    => 'Highest Rating',
                'lowest'     => 'Lowest Rating',
                'random'     => 'Random'
            );
                        
            $coll_display_type = array(
                'before_the_content'      => 'Before the content',
                'between_the_content'     => 'Beetween the content',
                'after_the_content'       => 'After the content',
                'shortcode'               => 'Shortcode',                
            );
            
            ?> 

            <div class="saswp-collection-wrapper">  
                
                <form method="post" action="post.php">
                    <input type="hidden" name="post_type" value="saswp-collections">
                    <input type="hidden" name="saswp-collection-page" value="1">
                    <input type="hidden" id="saswp_collection_id" name="saswp_collection_id" value="<?php echo esc_attr($post_id); ?>">                   
                    
                    <div class="saswp-collection-container">
                      <div class="saswp-collection-title">
                        <input type="text" value="<?php if(get_the_title($post_id) == 'Auto Draft'){ echo 'Untitled'; }else{ echo get_the_title($post_id); } ?>" id="saswp_collection_title" name="saswp_collection_title" style="width: 30%;">
                      </div>
                      <div class="saswp-collection-body">
                          
                      <div class="saswp-collection-preview">
                          
                          <span class="spinner"></span>
                        <!-- Collections html will be loaded on ajax call -->                                                                                               
                      </div>
                                                       
                          <div class="saswp-collection-settings">
                            <ul>
                              <li>
                                <a class="saswp-accordion"><?php echo esc_html__('Reviews Source','schema-and-structured-data-for-wp'); ?></a>
                                <div class="saswp-accordion-panel">
                                  <?php $platforms = saswp_get_terms_as_array();
                                      if($platforms){
                                      global $wpdb;
                                      $exists_platforms = $wpdb->get_results("
                                        SELECT meta_value, count(meta_value) as meta_count FROM `wp_postmeta` WHERE `meta_key`='saswp_review_platform' group by meta_value",
                                        ARRAY_A
                                     ); ?>
                                    <div class="saswp-plf-lst-rv-cnt">
                                      <?php
                                      echo '<select id="saswp-plaftorm-list" name="saswp-plaftorm-list">';
                                   
                                      $active_options   = '';
                                      $inactive_options = '';
                                      
                                      foreach($platforms as $key => $val){
                                        if(in_array($key, array_column($exists_platforms, 'meta_value'))){
                                               $active_options .= '<option value="'.esc_attr($key).'">'.esc_attr($val).'</option>';
                                        }else{
                                           $inactive_options.= '<option value="'.esc_attr($key).'" disabled>'.esc_attr($val).'</option>';
                                        }
                                      }
                                      
                                     echo '<optgroup label="Active">';
                                     echo $active_options;
                                     echo '</optgroup>';
                                     echo '<optgroup label="InActive">';
                                     echo $inactive_options;
                                     echo '</optgroup>';
                                     echo '</select>';
                                                
                                } ?>   
                                    <input type="number" id="saswp-review-count" name="saswp-review-count" min="0" value="5">
                                    <a class="button button-default saswp-add-to-collection"><?php echo esc_html__('Add','schema-and-structured-data-for-wp'); ?></a>
                                  </div>
                                  <div class="saswp-platform-added-list">  
                                      
                                  </div>
                                </div>
                              </li>
                              <li>                                     
                                <a class="saswp-accordion"><?php echo esc_html__('Presentation','schema-and-structured-data-for-wp'); ?></a>
                                <div class="saswp-accordion-panel">
                                    <lable><?php echo esc_html__('Design','schema-and-structured-data-for-wp'); ?></lable>  
                                    <select name="saswp_collection_design" class="saswp-collection-desing saswp-coll-settings-options">
                                        
                                        <?php
                                        
                                        foreach($coll_desing as $key => $val){
                                            
                                            echo '<option value="'.esc_attr($key).'" '.($post_meta['saswp_collection_design'][0] == $key ? 'selected':'').' >'.esc_html__( $val , 'schema-and-structured-data-for-wp' ).'</option>';
                                            
                                        }
                                        
                                        ?>                                    
                                     </select>
                                    
                                    <input type="number" id="saswp-collection-cols" name="saswp_collection_cols" min="0" value="<?php echo (isset($post_meta['saswp_collection_cols'][0]) ? $post_meta['saswp_collection_cols'][0] : '2' ); ?>" class="saswp-number-change saswp-coll-settings-options saswp-coll-options saswp-grid-options">                                    
                                    <select name="saswp-collection-gallery-type" id="saswp-collection-gallery-type" class="saswp-slider-type saswp-slider-options saswp_hide saswp-coll-settings-options saswp-coll-options">
                                        <option value="slider" <?php echo (isset($post_meta['saswp-collection-gallery-type'][0]) && $post_meta['saswp-collection-gallery-type'][0] == 'slider'  ? 'selected' : '' ); ?>>Slider</option>
                                        <option value="carousel" <?php echo (isset($post_meta['saswp-collection-gallery-type'][0]) && $post_meta['saswp-collection-gallery-type'][0] == 'carousel'  ? 'selected' : '' ); ?>>Carousel</option>
                                    </select>
                                    <div class="saswp-slider-display saswp-slider-options saswp_hide saswp-coll-settings-options saswp-coll-options">
                                        <span><input type="checkbox" id="saswp-gallery-arrow" name="saswp-gallery-arrow" value="1" <?php echo (isset($post_meta['saswp-gallery-arrow'][0]) && $post_meta['saswp-gallery-arrow'][0] == 1 ? 'checked' : '' ); ?>> <?php echo esc_html__('Arrows','schema-and-structured-data-for-wp'); ?></span>
                                        <span><input type="checkbox" id="saswp-gallery-dots" name="saswp-gallery-dots" value="1" <?php echo (isset($post_meta['saswp-gallery-dots'][0]) && $post_meta['saswp-gallery-dots'][0] == 1 ? 'checked' : '' ); ?>> <?php echo esc_html__('Dots','schema-and-structured-data-for-wp'); ?></span>
                                    </div>
                                    
                                    <div class="saswp-fomo-options saswp_hide saswp-coll-options">                                       
                                        <?php echo esc_html__('Interval in Seconds','schema-and-structured-data-for-wp'); ?>
                                        <input type="number" id="saswp-fomo-interval" name="saswp-fomo-interval" class="saswp-number-change" min="1" value="<?php echo (isset($post_meta['saswp-fomo-interval'][0]) ? $post_meta['saswp-fomo-interval'][0] : '3' ); ?>">
                                    <?php echo esc_html__('Visibility in Seconds','schema-and-structured-data-for-wp'); ?>
                                    <input type="number" id="saswp-fomo-visibility" name="saswp-fomo-visibility" class="saswp-number-change" min="1" value="<?php echo (isset($post_meta['saswp-fomo-visibility'][0]) ? $post_meta['saswp-fomo-visibility'][0] : '3' ); ?>">                                    
                                        
                                    </div>
                                                                        
                                </div>
                              </li>
                              <li>
                                <a class="saswp-accordion"><?php echo esc_html__('Filter','schema-and-structured-data-for-wp'); ?></a>
                                <div class="saswp-accordion-panel">
                                  <lable><?php echo esc_html__('Sorting','schema-and-structured-data-for-wp'); ?></lable>  
                                  <select name="saswp_collection_sorting" class="saswp-collection-sorting saswp-coll-settings-options">                                      
                                     
                                      <?php
                                        
                                        foreach($coll_sorting as $key => $val){
                                            
                                            echo '<option value="'.esc_attr($key).'" '.($post_meta['saswp_collection_sorting'][0] == $key ? 'selected':'').' >'.esc_html__( $val , 'schema-and-structured-data-for-wp' ).'</option>';
                                            
                                        }
                                        
                                        ?>
                                      
                                     </select>
                                </div>
                              </li>
                              <li>
                                <a class="saswp-accordion"><?php echo esc_html__('Display','schema-and-structured-data-for-wp'); ?></a>
                                <div class="saswp-accordion-panel">
                                  <select class="saswp-collection-display-method" name="saswp_collection_display_type">
                                    
                                        <?php
                                        
                                        foreach($coll_display_type as $key => $val){
                                            
                                            echo '<option value="'.esc_attr($key).'" '.($post_meta['saswp_collection_display_type'][0] == $key ? 'selected':'').' >'.esc_html__( $val , 'schema-and-structured-data-for-wp' ).'</option>';
                                            
                                        }
                                        
                                        ?> 
                                   </select>
                                  <div class="saswp_hide saswp-collection-shortcode">[reviews-collection id="<?php echo $post_id; ?>"]</div>
                                </div>
                              </li>
                            </ul>
                            <button type="submit" class="button button-primary" > <?php echo esc_html__('Save','schema-and-structured-data-for-wp'); ?></button>   
                          </div><!-- /.saswp-collection-body -->
                      </div>
                    </div><!-- /.saswp-collection-container -->
                </form>    
            </div><!-- /.saswp-collection-wrapper -->

            <?php
                                    
        }
                        
        public function saswp_save_collection_data(){
                        
            if(isset($_POST['saswp_collection_id'])){
                      
            $post_id         = intval($_POST['saswp_collection_id']);
            $collection_page = intval($_POST['saswp-collection-page']);
            $post_title      = sanitize_text_field($_POST['saswp_collection_title']);
                        
            $post = array(                 
                    'ID'                    => $post_id,
                    'post_title'            => $post_title,                    
                    'post_status'           => 'publish',
                    'post_name'             => $post_title,                                        
                    'post_type'             => 'saswp-collections',                                                            
                );
                                          
            wp_update_post($post);                                      
            $post_meta = array();            
            $post_meta['saswp_collection_design']       = sanitize_text_field($_POST['saswp_collection_design']);            
            $post_meta['slider_display']                = sanitize_text_field($_POST['slider_display']);
            $post_meta['saswp_collection_sorting']      = sanitize_text_field($_POST['saswp_collection_sorting']);
            $post_meta['saswp_collection_display_type'] = sanitize_text_field($_POST['saswp_collection_display_type']);
            $post_meta['saswp-collection-gallery-type'] = sanitize_text_field($_POST['saswp-collection-gallery-type']);
            $post_meta['saswp_collection_cols']         = intval($_POST['saswp_collection_cols']);
            $post_meta['saswp-gallery-arrow']           = intval($_POST['saswp-gallery-arrow']);
            $post_meta['saswp-gallery-dots']            = intval($_POST['saswp-gallery-dots']);            
            $post_meta['saswp-fomo-interval']           = intval($_POST['saswp-fomo-interval']);
            $post_meta['saswp-fomo-visibility']         = intval($_POST['saswp-fomo-visibility']);
                                                        //Escaping is missing will do tomorrow
            $post_meta['saswp_platform_ids']            = $_POST['saswp_platform_ids'];
                        
            if(!empty($post_meta)){
                
                foreach($post_meta as $meta_key => $meta_val){
                    
                    update_post_meta($post_id, $meta_key, $meta_val); 
                    
                }
                
            }
                                    
            if($collection_page == 1){
                
                $current_url = htmlspecialchars_decode(wp_nonce_url(admin_url('admin.php?post_id='.$post_id.'&page=collection'), '_wpnonce'));           
                wp_redirect( $current_url );
                exit;
            }
            
        }
                                    
        }
            
}

if ( class_exists( 'SASWP_Reviews_Collection') ) {
	SASWP_Reviews_Collection::get_instance();
}