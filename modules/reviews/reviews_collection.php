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
        private $_design  = null;

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
          add_action( 'amp_post_template_data', array($this, 'saswp_reviews_collection_amp_script'));                                   
          add_shortcode( 'saswp-reviews-collection', array($this, 'saswp_reviews_collection_shortcode_render' ),10);        

          add_filter('the_content', array( $this, 'saswp_reviews_display_collection' ));
                                 
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

        
        public function saswp_collection_logic_checker($collection_id){

            $response = false;

            $where        = get_post_meta($collection_id, 'saswp_collection_where', true);
            $where_data   = get_post_meta($collection_id, 'saswp_collection_where_data', true);
            
            if(isset($where[0])){

                $input = array();

                $input['key_1'] = $where[0];
                $input['key_2'] = 'equal';
                $input['key_3'] = $where_data[0];

                $response = saswp_comparison_logic_checker($input);
                
            }

            
            return $response;
        }
        
        public function saswp_reviews_display_collection($content){

            
            $display_type_opt = get_option('saswp_collection_display_opt');
            
            if(!empty($display_type_opt)){

                foreach ($display_type_opt as $key => $value) {

                    $logic = $this->saswp_collection_logic_checker($key);

                    if($logic){

                        $attr       = array();
                        $attr['id'] = $key;
                        $coll_html  = $this->saswp_reviews_collection_shortcode_render($attr);
                        
                        if($value == 'before_the_content'){
                            $content = $coll_html.$content;
                        }else if($value == 'after_the_content'){
                            $content = $content.$coll_html;
                        }else{

                            $closing_p        = '</p>';
                            $paragraphs       = explode( $closing_p, $content );       
                            $total_paragraphs = count($paragraphs);
                            $paragraph_id     = round($total_paragraphs /2);  
                    
                            foreach ($paragraphs as $index => $paragraph) {
                                if ( trim( $paragraph ) ) {
                                    $paragraphs[$index] .= $closing_p;
                                }
                                if ( $paragraph_id == $index + 1 ) {
                                    $paragraphs[$index] .= $coll_html;
                                }
                            }
                            $content = implode( '', $paragraphs ); 

                        }

                    }
                                        
                }

            }

            return $content;
        }
        public function saswp_add_collection_menu_links(){
            
             add_submenu_page( 'edit.php?post_type=saswp',
                saswp_t_string( 'Structured Data' ),
                saswp_t_string( '' ),
                saswp_current_user_can(),
                'collection',
                array($this, 'saswp_admin_collection_interface_render'));   
            
        }
        
        public function saswp_set_collection_edit_link($link, $post_id, $context){
                
                if (function_exists('get_current_screen') && (isset(get_current_screen()->id) && get_current_screen()->id == 'edit-saswp-collections' ) && $context == 'display') {

                        return wp_nonce_url(admin_url('admin.php?post_id='.$post_id.'&page=collection'), '_wpnonce');

                } else {

                        return $link;

                }
            
        }
        
        public function saswp_reviews_collection_amp_script($data){
            
            $design = $this->_design;
            
            if($design == 'gallery' || $design == 'fomo'){
                
                if ( empty( $data['amp_component_scripts']['amp-carousel'] ) ) {
                     $data['amp_component_scripts']['amp-carousel'] = "https://cdn.ampproject.org/v0/amp-carousel-latest.js";
                }
            }
            
            if($design == 'popup' || $design == 'gallery' || $design == 'fomo'){
                
                if ( empty( $data['amp_component_scripts']['amp-bind'] ) ) {
                    $data['amp_component_scripts']['amp-bind'] = "https://cdn.ampproject.org/v0/amp-bind-latest.js";
                }
                
            }
            
           return $data;
                        
        }
        
        public function saswp_reviews_collection_amp_css(){            
            
           $global_css  =  SASWP_PLUGIN_DIR_PATH . 'admin_section/css/amp/collection-front-global.css'; 
           $grid_css    =  SASWP_PLUGIN_DIR_PATH . 'admin_section/css/amp/collection-front-grid.css';
           $fomo_css    =  SASWP_PLUGIN_DIR_PATH . 'admin_section/css/amp/collection-front-fomo.css';
           $gallery_css =  SASWP_PLUGIN_DIR_PATH . 'admin_section/css/amp/collection-front-gallery.css';
           $popup_css   =  SASWP_PLUGIN_DIR_PATH . 'admin_section/css/amp/collection-front-popup.css';
           $badge_css   =  SASWP_PLUGIN_DIR_PATH . 'admin_section/css/amp/collection-front-badge.css';
                               
           if($this->_design){               
               
                echo @file_get_contents($global_css);
                
                switch ($this->_design) {
                    
                    case 'grid':                       
                            echo @file_get_contents($grid_css);
                        break;
                    case 'gallery':
                            echo @file_get_contents($gallery_css);
                        break;
                    case 'badge':
                            echo @file_get_contents($badge_css);
                        break;
                    case 'popup':
                            echo @file_get_contents($popup_css);
                        break;
                    case 'fomo':
                            echo @file_get_contents($fomo_css);
                        break;

                    default:
                        break;
                }
               
           }
           
        }
              
        public function saswp_register_collection_post_type(){
                        
            $collections = array(
                    'labels' => array(
                        'name' 			=> saswp_t_string( 'Collections' ),	        
                        'add_new' 		=> saswp_t_string( 'Add Collection' ),
                        'add_new_item'  	=> saswp_t_string( 'Edit Collection' ),
                        'edit_item'             => saswp_t_string( 'Edit Collection'),                
                    ),
                    'public' 		    => true,
                    'has_archive' 	    => true,
                    'exclude_from_search'   => true,
                    'publicly_queryable'    => false,
                    'show_in_admin_bar'     => false,
                    //'show_in_menu'          => 'edit.php?post_type=saswp',                
                    'show_in_menu'          => false,                
                    'show_ui'               => true,
                    'show_in_nav_menus'     => true,			
                    'show_admin_column'     => true,        
                    'rewrite'               => false,  
            );
            
            if(saswp_current_user_allowed()){
                
                $cap = saswp_post_type_capabilities();

                if(!empty($cap)){        
                    $collections['capabilities'] = $cap;         
                }
                
                register_post_type( 'saswp-collections', $collections );   
            }
                        
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
            $review_id   = ''; 
            $attr        = array();

            if(isset($_GET['reviews_ids']) && $_GET['reviews_ids'] != ''){
                $attr['in'] = json_decode($_GET['reviews_ids']);
            }

            if(isset($_GET['review_id']) && $_GET['review_id'] != ''){
                $review_id   = intval($_GET['review_id']);
                $attr['in'] = array($review_id);
            }
                        
            if( $platform_id ||  isset($attr['in']) ){
                                                     
            $reviews_list = $this->_service->saswp_get_reviews_list_by_parameters($attr, $platform_id, $rvcount); 
             
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
                            
        public function saswp_reviews_collection_shortcode_render($attr){
             
            global $saswp_post_reviews, $collection_aggregate;
            
            $html = $htmlp = '';
            
            if(!is_admin()){
                
                if(isset($attr['id'])){
                
                $total_reviews        = array();                 
                $total_reviews_count  = 0;
                $collection           = array();                  
                $platform_id          = array();
                $pagination           = null;
                $perpage              = null;
                $data_id              = null;
                $dots = $f_interval = $f_visibility = $arrow = 1;
                $g_type = $design = $cols = $sorting = $date_format = '';
                
                $collection_data = get_post_meta($attr['id']);
                
                if(isset($collection_data['saswp_collection_design'][0])){
                    $design        = $collection_data['saswp_collection_design'][0];
                    $this->_design = $design;
                }

                if(isset($collection_data['saswp_collection_date_format'][0])){
                    $date_format        = $collection_data['saswp_collection_date_format'][0];                    
                }
                
                if(isset($collection_data['saswp_collection_cols'][0])){
                    
                    $cols          = $collection_data['saswp_collection_cols'][0];
                }
                
                if(isset($collection_data['saswp_collection_cols'][0])){
                    
                    $cols          = $collection_data['saswp_collection_cols'][0];
                }
                
                if(isset($collection_data['saswp_gallery_arrow'][0])){
                    
                    $arrow        = $collection_data['saswp_gallery_arrow'][0];
                }
                                
                if(isset($collection_data['saswp_gallery_dots'][0])){
                    $dots         = $collection_data['saswp_gallery_dots'][0];
                }
                            
                if(isset($collection_data['saswp_collection_gallery_type'][0])){
                    $g_type       = $collection_data['saswp_collection_gallery_type'][0];
                }
                
                if(isset($collection_data['saswp_fomo_interval'][0])){
                    $f_interval   = $collection_data['saswp_fomo_interval'][0];
                }
                
                if(isset($collection_data['saswp_fomo_visibility'][0])){
                    $f_visibility = $collection_data['saswp_fomo_visibility'][0];
                }
                                
                if(isset($collection_data['saswp_collection_sorting'][0])){
                    $sorting      = $collection_data['saswp_collection_sorting'][0];                
                }
                
                if(isset($collection_data['saswp_platform_ids'][0])){
                    $platform_id  = unserialize($collection_data['saswp_platform_ids'][0]);                
                }
                if(isset($collection_data['saswp_platform_ids'][0])){
                    
                    $total_reviews  = unserialize($collection_data['saswp_total_reviews'][0]);
                    if( is_array($total_reviews) && !empty($total_reviews) ){
                        $total_reviews_count = count($total_reviews);
                    }
                    
                }
                if(isset($collection_data['saswp_collection_pagination'][0])){
                    $pagination  = $collection_data['saswp_collection_pagination'][0];                
                }
                if(isset($collection_data['saswp_platform_ids'][0])){
                    $perpage  = $collection_data['saswp_collection_per_page'][0];                
                }
                                                
                if($total_reviews){
                    
                    wp_enqueue_style( 'saswp-collection-front-css', SASWP_PLUGIN_URL . 'admin_section/css/'.(SASWP_ENVIRONMENT == 'production' ? 'collection-front.min.css' : 'collection-front.css'), false , SASWP_VERSION );
                    wp_enqueue_script( 'saswp-collection-front-js', SASWP_PLUGIN_URL . 'admin_section/js/'.(SASWP_ENVIRONMENT == 'production' ? 'collection-front.min.js' : 'collection-front.js'), array('jquery') , SASWP_VERSION );
                    
                    add_action( 'amp_post_template_css', array($this, 'saswp_reviews_collection_amp_css'));
                    
                    
                    if($design == 'grid'){
                        
                        $nextpage = $perpage;
                        $offset   = 0;

                        if($pagination){

                            $data_id = 1; 
                            
                            if(isset($_GET['rv_page'])){
                                $data_id = sanitize_text_field($_GET['rv_page']); 
                            }
                            
                            if($data_id > 0){                        
                                $nextpage            = $data_id * $perpage;                
                            }                    
                            $offset    = $nextpage - $perpage;
                            
                            $array_chunk = array_chunk($total_reviews,$perpage);                            
                            $total_reviews = $array_chunk[($data_id-1)]; 

                        }
                        
                        if(!$collection_aggregate){

                            $col_average = $this->_service->saswp_get_collection_average_rating(unserialize($collection_data['saswp_total_reviews'][0]));

                            if($col_average){
                                $collection_aggregate['count'] = $total_reviews_count;
                                $collection_aggregate['average']  = $col_average;
                            }
                            
                        }
                        
                        
                    }
                                     
                    $collection = $this->_service->saswp_get_reviews_list_by_design($design, $platform_id, $total_reviews, $sorting);                    

                    if($design == 'badge'){

                        $new_coll = array();

                        if($collection){
                            foreach($collection as $coll){
                                foreach($coll as $new){
                                    $new_coll[] = $new;   
                                }
                            }
                        }

                        $saswp_post_reviews = array_merge($saswp_post_reviews, $new_coll);
                    }else{
                        $saswp_post_reviews = array_merge($saswp_post_reviews, $collection);
                    }
                                        
                    switch($design) {
                    
                    case "grid":
                        
                        $html = $this->_service->saswp_create_collection_grid($cols, $collection, $total_reviews, $pagination, $perpage, $offset, $nextpage, $data_id, $total_reviews_count, $date_format);
                        
                        break;
                        
                    case 'gallery':
                        
                        $html = $this->_service->saswp_create_collection_slider($g_type, $arrow, $dots, $collection, $date_format);
                        
                        break;
                    
                    case 'badge':
                        
                        $html = $this->_service->saswp_create_collection_badge($collection);
                        
                        break;
                        
                    case 'popup':
                        
                        $html = $this->_service->saswp_create_collection_popup($collection, $date_format);
                        
                        break;
                    
                    case 'fomo':
                        
                        $html = $this->_service->saswp_create_collection_fomo($f_interval, $f_visibility, $collection, $date_format);
                        
                        
                        break;
                                                                
                }
                                        
                }
                                              
            }
            
                                
            }
            
            $htmlp .= '<div class="saswp-r">';
            $htmlp .= $html;  
            $htmlp .= '</div>';
            return $htmlp;
                                    
        }
        
        public function saswp_admin_collection_interface_render(){
            
             if ( ! current_user_can( saswp_current_user_can() ) ) return;
             if ( !wp_verify_nonce( $_GET['_wpnonce'], '_wpnonce' ) ) return;
             
            $post_meta = array();
            $post_id   = null;            

            if(isset($_GET['post_id'])){

                $post_id = intval($_GET['post_id']);

                $post_meta = get_post_meta($post_id);            


            } else{

                $post    = get_default_post_to_edit( 'saswp-google-review', true );
                $post_id = intval($post->ID);
            }
            
            $coll_desing = array(
                'grid'     => 'Grid',
                'gallery'  => 'Gallery',
                'badge'    => 'Badge',
                'popup'    => 'PopUp',
                'fomo'     => 'Fomo',
            );
            $date_format = array(
                'Y-m-d'  => 'yyyy-mm-dd',
                'd-m-Y'  => 'dd-mm-yyyy',                
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
                'shortcode'               => 'Shortcode',  
                'before_the_content'      => 'Before The Content',
                'between_the_content'     => 'Between The Content',
                'after_the_content'       => 'After The Content',                              
            );
            
            ?> 

            <div class="saswp-collection-wrapper">  
                
                <form method="post" action="post.php">
                    <input type="hidden" name="saswp_collection_nonce" value="<?php echo wp_create_nonce('saswp_collection_nonce_data');    ?>">
                    <input type="hidden" name="post_type" value="saswp-collections">
                    <input type="hidden" name="saswp-collection-page" value="1">
                    <input type="hidden" id="saswp_collection_id" name="saswp_collection_id" value="<?php echo esc_attr($post_id); ?>">                   
                    
                    <div class="saswp-collection-container">
                      <div class="saswp-collection-body">
                        <div class="saswp-collection-lp">
                            <div class="saswp-collection-title">
                                <input type="text" value="<?php if(get_the_title($post_id) == 'Auto Draft'){ echo 'Untitled'; }else{ echo get_the_title($post_id); } ?>" id="saswp_collection_title" name="saswp_collection_title">
                                <span class="saswp-rmv-coll-rv dashicons dashicons-admin-generic"></span>
                            </div>
                            <span class="spinner saswp-spinner"></span>
                            <div class="saswp-collection-preview">                                
                                <!-- Collections html will be loaded on ajax call -->
                            </div>
                        </div><!-- /.saswp-collection-lp --> 
                        <div class="saswp-collection-settings">
                            <ul>
                                <li>
                                    <a class="saswp-accordion"><?php echo saswp_t_string('Reviews Source'); ?></a>
                                    <div class="saswp-accordion-panel">
                                      <?php $platforms = saswp_get_terms_as_array();
                                          if($platforms){
                                          global $wpdb;
                                          $exists_platforms = $wpdb->get_results("
                                            SELECT meta_value, count(meta_value) as meta_count FROM {$wpdb->postmeta} WHERE `meta_key`='saswp_review_platform' group by meta_value",
                                            ARRAY_A
                                         );  ?>
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
                                        <a class="button button-default saswp-add-to-collection"><?php echo saswp_t_string('Add'); ?></a>
                                      </div>
                                      <div class="saswp-platform-added-list">  
                                          
                                      </div>
                                        <div class="saswp-total-reviews-list">  

                                        <?php 
                                        
                                        if(isset($post_meta['saswp_total_reviews'][0])){

                                            $reviews_list = unserialize($post_meta['saswp_total_reviews'][0]);

                                            if(is_array($reviews_list)){
                                                echo '<input type="hidden" id="saswp_total_reviews_list" name="saswp_total_reviews" value="'.json_encode($reviews_list).'">';
                                            }
                                                                                        
                                        }

                                        
                                        ?>

                                      </div>
                                    </div>
                                </li>
                                <li>                                     
                                    <a class="saswp-accordion"><?php echo saswp_t_string('Presentation'); ?></a>
                                    <div class="saswp-accordion-panel">
                                        <div class="saswp-dp-dsg">
                                        <lable><?php echo saswp_t_string('Design'); ?></lable>  
                                        <select name="saswp_collection_design" class="saswp-collection-desing saswp-coll-settings-options">
                                            <?php
                                            foreach($coll_desing as $key => $val){
                                                
                                                echo '<option value="'.esc_attr($key).'" '.($post_meta['saswp_collection_design'][0] == $key ? 'selected':'').' >'.saswp_t_string( $val  ).'</option>';
                                            }
                                            ?>                                    
                                         </select>
                                        </div>
                                        <div class="saswp-dp-dsg saswp-coll-options saswp-grid-options saswp-dp-dtm">
                                        <label><?php echo saswp_t_string( 'Columns' ); ?></label>
                                        <input type="number" id="saswp-collection-cols" name="saswp_collection_cols" min="1" value="<?php echo (isset($post_meta['saswp_collection_cols'][0]) ? $post_meta['saswp_collection_cols'][0] : '2' ); ?>" class="saswp-number-change saswp-coll-settings-options saswp-coll-options saswp-grid-options">    
                                        </div>
                                        
                                        <div class="saswp-dp-dsg saswp-coll-options saswp-grid-options saswp-dp-dtm">
                                            <span><?php echo saswp_t_string( 'Pagination' ); ?></span>
                                            <span><input name="saswp_collection_pagination" type="checkbox" id="saswp-coll-pagination" class="saswp-coll-settings-options" value="1" <?php echo (isset($post_meta['saswp_collection_pagination'][0]) && $post_meta['saswp_collection_pagination'][0] == 1 ? 'checked' : '' ); ?>></span>
                                        </div>
                                        
                                        <div class="saswp-dp-dsg saswp-coll-options saswp-grid-options saswp-dp-dtm saswp_hide_imp">
                                            <label><?php echo saswp_t_string( 'Per Page' ); ?></label>
                                            <input name="saswp_collection_per_page" type="number" min="1" id="saswp-coll-per-page"  class="saswp-coll-settings-options" value="<?php echo (isset($post_meta['saswp_collection_per_page'][0]) ? $post_meta['saswp_collection_per_page'][0] : '10' ); ?>">
                                        </div>
                                        
                                        <div class="saswp-dp-dsg saswp-dp-dtm saswp-slider-options saswp-coll-options">
                                         <label><?php echo saswp_t_string( 'Slider Type' ); ?></label>
                                        <select name="saswp_collection_gallery_type" id="saswp_collection_gallery_type" class="saswp-slider-type saswp-slider-options saswp_hide saswp-coll-settings-options saswp-coll-options">
                                            <option value="slider" <?php echo (isset($post_meta['saswp_collection_gallery_type'][0]) && $post_meta['saswp_collection_gallery_type'][0] == 'slider'  ? 'selected' : '' ); ?>><?php echo saswp_t_string( 'Slider' ); ?></option>
                                            <option value="carousel" <?php echo (isset($post_meta['saswp_collection_gallery_type'][0]) && $post_meta['saswp_collection_gallery_type'][0] == 'carousel'  ? 'selected' : '' ); ?>><?php echo saswp_t_string( 'Carousel' ); ?></option>
                                        </select>
                                        </div>
                                        <div class="saswp-slider-display saswp-slider-options saswp_hide saswp-coll-settings-options saswp-coll-options">
                                            <span><input type="checkbox" id="saswp_gallery_arrow" name="saswp_gallery_arrow" value="1" <?php echo (isset($post_meta['saswp_gallery_arrow'][0]) && $post_meta['saswp_gallery_arrow'][0] == 1 ? 'checked' : '' ); ?>> <?php echo saswp_t_string('Arrows'); ?></span>
                                            <span><input type="checkbox" id="saswp_gallery_dots" name="saswp_gallery_dots" value="1" <?php echo (isset($post_meta['saswp_gallery_dots'][0]) && $post_meta['saswp_gallery_dots'][0] == 1 ? 'checked' : '' ); ?>> <?php echo saswp_t_string('Dots'); ?></span>
                                        </div>
                                        
                                        <div class="saswp-fomo-options saswp_hide saswp-coll-options"> 
                                            <div class="saswp-dp-dsg saswp-dp-dtm">
                                            <span><?php echo saswp_t_string('Delay Time In Sec'); ?>
                                            </span>
                                            <input type="number" id="saswp_fomo_interval" name="saswp_fomo_interval" class="saswp-number-change" min="1" value="<?php echo (isset($post_meta['saswp_fomo_interval'][0]) ? $post_meta['saswp_fomo_interval'][0] : '3' ); ?>"> 
                                            </div>                                                                           
                                        </div>      
                                        <div class="saswp-dp-dsg">
                                        <lable><?php echo saswp_t_string('Date Format'); ?></lable>  
                                        <select name="saswp_collection_date_format" class="saswp-collection-date-format saswp-coll-settings-options">
                                            <?php
                                            foreach($date_format as $key => $val){                                                
                                                echo '<option value="'.esc_attr($key).'" '.($post_meta['saswp_collection_date_format'][0] == $key ? 'selected':'').' >'.saswp_t_string( $val  ).'</option>';
                                            }
                                            ?>                                    
                                         </select>
                                        </div>                                                                  
                                    </div>
                                </li>
                              <li>

                                <a class="saswp-accordion"><?php echo saswp_t_string('Filter'); ?></a>
                                <div class="saswp-accordion-panel">
                                    <div class="saswp-dp-dsg">
                                        <lable><?php echo saswp_t_string('Sorting'); ?></lable>  
                                        <select name="saswp_collection_sorting" class="saswp-collection-sorting saswp-coll-settings-options">                                      
                                          <?php
                                            foreach($coll_sorting as $key => $val){
                                                echo '<option value="'.esc_attr($key).'" '.($post_meta['saswp_collection_sorting'][0] == $key ? 'selected':'').' >'.saswp_t_string( $val  ).'</option>';
                                                
                                            }
                                            
                                            ?>
                                        </select>
                                    </div>
                                </div>
                              </li>
                              <li>
                                <a class="saswp-accordion"><?php echo saswp_t_string('Display'); ?></a>

                                <div class="saswp-accordion-panel">
                                    <div class="saswp-dp-dsg">
                                        <label><?php echo saswp_t_string( 'Display Type' ); ?></label>
                                        <select class="saswp-collection-display-method" name="saswp_collection_display_type">
                                            <?php
                                            foreach($coll_display_type as $key => $val){
                                                
                                                echo '<option value="'.esc_attr($key).'" '.($post_meta['saswp_collection_display_type'][0] == $key ? 'selected':'').' >'.saswp_t_string( $val  ).'</option>';
                                            }
                                            ?> 
                                        </select>
                                    </div>

                                    
                                    <div id="saswp-motivatebox" class="saswp-collection-shortcode">
                                        <span class="motivate">
                                        [saswp-reviews-collection id="<?php echo $post_id; ?>"]
                                        </span>
                                    </div>                                                                                                                   

                                    <div class="saswp-dp-dsg saswp_hide saswp-coll-where">
                                         <label><?php echo saswp_t_string( 'Where' ); ?></label>
                                         <?php
                                            $choice = array(
                                                'post_type'     => saswp_t_string("Post Type"),
                                                'user_type'     => saswp_t_string("Logged in User Type"),
                                                'post'          => saswp_t_string("Post"),
                                                'post_category' => saswp_t_string("Post Category"),
                                                'post_format'   => saswp_t_string("Post Format"),
                                                'page'          => saswp_t_string("Page"),
                                                'page_template' => saswp_t_string("Page Template"),
                                                'ef_taxonomy'   => saswp_t_string("Tag"),
                                            )

                                          ?>
                                         <select class="saswp-collection-where " name="saswp_collection_where[]">
                                            
                                            <?php
                                                $selected_val = unserialize($post_meta['saswp_collection_where'][0]);

                                                foreach ($choice as $key => $value) {

                                                    if(isset($selected_val[0]) && $selected_val[0] == $key){
                                                        echo '<option value="'.$key.'" selected>'.$value.'</option>';
                                                    }else{
                                                        echo '<option value="'.$key.'">'.$value.'</option>';
                                                    }
                                                    
                                                }

                                            ?>

                                         </select>

                                    </div>
                                    <div class="saswp-dp-dsg saswp_hide saswp-coll-where">
                                                <?php
                                                    $condition_val = 'post_type';
                                                    $saved_choices = array();

                                                    if(isset($selected_val[0]) && $selected_val[0] != ''){
                                                        $condition_val = $selected_val[0];
                                                    }   
                                                    
                                                    $type_list = saswp_get_condition_list($condition_val);
                                                    
                                                    
                                                    $where_data = unserialize($post_meta['saswp_collection_where_data'][0]);
                                                    
                                                    if ( isset($where_data[0]) && $where_data[0] !=  '' ) {
                                                        $saved_choices = saswp_get_condition_list($condition_val, '', $where_data[0]);                        
                                                    }

                                                    if($type_list){
                                                        echo '<label></label>';
                                                        echo '<select data-type="post_type" class="saswp-select2 saswp-collection-where-data" name="saswp_collection_where_data[]">';

                                                        foreach ($type_list as $value) {
                                                            echo '<option value="'.esc_attr($value['id']).'">'.esc_html($value['text']).'</option>';
                                                        }

                                                        if($saved_choices){
                                                            foreach($saved_choices as $value){
                                                                echo '<option value="' . esc_attr($value['id']) .'" selected> ' .  saswp_t_string($value['text']) .'</option>';                     
                                                            }
                                                        }

                                                        echo '</select>';

                                                    }

                                                ?>
                                    </div>

                                </div>
                              </li>
                            </ul>
                            <div class="saswp-sv-btn">
                                <button type="submit" class="button button-primary" > 
                                    <?php echo saswp_t_string('Save Menu'); ?>
                                </button>
                            </div>   
                        </div><!-- /.saswp-collection-body -->
                      </div><!-- /.saswp-collection-body -->
                    </div><!-- /.saswp-collection-container -->
                </form>    
            </div><!-- /.saswp-collection-wrapper -->

            <?php
                                    
        }
                        
        public function saswp_save_collection_data(){
                                    
            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
            if ( ! current_user_can( saswp_current_user_can() ) ) return ;		    		
            if ( ! isset( $_POST['saswp_collection_nonce'] ) || ! wp_verify_nonce( $_POST['saswp_collection_nonce'], 'saswp_collection_nonce_data' ) ) return;            
            
            if(isset($_POST['saswp_collection_id'])){
            $display_type_opt    = array();
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

            $display_type = isset($_POST['saswp_collection_display_type']) ? sanitize_text_field($_POST['saswp_collection_display_type']) : '';
            
            $display_type_opt = get_option('saswp_collection_display_opt');

            unset($display_type_opt[$post_id]);

            if($display_type != 'shortcode'){
                $display_type_opt[$post_id] = $display_type;
            }

            update_option('saswp_collection_display_opt', $display_type_opt);
            
            $post_meta['saswp_collection_design']       = isset($_POST['saswp_collection_design']) ? sanitize_text_field($_POST['saswp_collection_design']) : '';                        
            $post_meta['saswp_collection_date_format']  = isset($_POST['saswp_collection_date_format']) ? sanitize_text_field($_POST['saswp_collection_date_format']) : '';                        
            $post_meta['saswp_collection_sorting']      = isset($_POST['saswp_collection_sorting']) ? sanitize_text_field($_POST['saswp_collection_sorting']) : '';
            $post_meta['saswp_collection_display_type'] = $display_type;
            $post_meta['saswp_collection_gallery_type'] = isset($_POST['saswp_collection_gallery_type']) ? sanitize_text_field($_POST['saswp_collection_gallery_type']) : '';
            $post_meta['saswp_collection_cols']         = isset($_POST['saswp_collection_cols']) ? intval($_POST['saswp_collection_cols']) : '';
            $post_meta['saswp_gallery_arrow']           = isset($_POST['saswp_gallery_arrow']) ? intval($_POST['saswp_gallery_arrow']) : '';
            $post_meta['saswp_gallery_dots']            = isset($_POST['saswp_gallery_dots']) ? intval($_POST['saswp_gallery_dots']) : '';            
            $post_meta['saswp_collection_pagination']   = isset($_POST['saswp_collection_pagination']) ? intval($_POST['saswp_collection_pagination']) : '';            
            $post_meta['saswp_collection_per_page']     = isset($_POST['saswp_collection_per_page']) ? intval($_POST['saswp_collection_per_page']) : '';            
            $post_meta['saswp_fomo_interval']           = isset($_POST['saswp_fomo_interval']) ? intval($_POST['saswp_fomo_interval']) : '';
            $post_meta['saswp_fomo_visibility']         = isset($_POST['saswp_fomo_visibility']) ? intval($_POST['saswp_fomo_visibility']) : '';                                                        
            $post_meta['saswp_platform_ids']            = array_map('intval', $_POST['saswp_platform_ids']);
            $post_meta['saswp_collection_where']        = array_map('sanitize_text_field', $_POST['saswp_collection_where']);
            $post_meta['saswp_collection_where_data']   = array_map('sanitize_text_field', $_POST['saswp_collection_where_data']);
            $post_meta['saswp_total_reviews']           = array_map('intval', json_decode($_POST['saswp_total_reviews']));
                        
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