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
        
        private function __construct() {
             
          add_filter( 'get_edit_post_link', array($this, 'saswp_set_collection_edit_link' ), 99, 3); 
          add_action( 'admin_menu', array($this, 'saswp_add_collection_menu_links' ),20);
          add_action( 'init', array($this, 'saswp_register_collection_post_type' ),20);
          add_action( 'admin_init', array($this, 'saswp_save_collection_data' ));
             
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
                    'public' 		=> true,
                    'has_archive' 		=> false,
                    'exclude_from_search'	=> true,
                    'publicly_queryable'	=> false,
                    'show_in_menu'          => 'edit.php?post_type=saswp',                
                    'show_ui'               => true,
                    'show_in_nav_menus'     => false,			
                    'show_admin_column'     => true,        
                    'rewrite'               => false,  
            );
            register_post_type( 'saswp-collections', $collections );   
        }
        
        public function saswp_admin_collection_interface_render(){
         
            $post_meta = array();
            $post_id   = null;            

            if(isset($_GET['post_id'])){

                $post_id = intval($_GET['post_id']);

                $post_meta = get_post_meta($post_id, $key='', true );            


            } else{

                $post    = get_default_post_to_edit( 'saswp-google-review', true );
                $post_id = $post->ID;
            }
            
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
                                   Preview 
                                </div>
                             
                                <div class="saswp-collection-settings">
                                  <input type="text" name="test_value" value="<?php echo $post_meta['test_value'][0] ?>">
                                 <button type="submit" class="btn btn-success button-primary" > <?php echo esc_html__('Save','schema-and-structured-data-for-wp'); ?>  </button>   
                                    
                                </div>
                                                                
                            </div>
                                                                            
                    </div>
                    
                </form>    
                
            </div> 

            <?php
                                    
        }
                        
        public function saswp_save_collection_data(){
                        
            if(isset($_POST['saswp_collection_id'])){
                      
            $post_id         = intval($_POST['saswp_collection_id']);
            $post_title      = sanitize_text_field($_POST['saswp_collection_title']);
            $collection_page = intval($_POST['saswp-collection-page']);
            
            $post = array(                 
                    'ID'                    => $post_id,
                    'post_title'            => $post_title,                    
                    'post_status'           => 'publish',
                    'post_name'             => $post_title,                                        
                    'post_type'             => 'saswp-collections',                                                            
                );
                                          
            wp_update_post($post);
                                        
            $post_meta = array();
            
            $post_meta['test_value'] = sanitize_text_field($_POST['test_value']);           
            
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
