<?php
/**
 * Class SASWP_Gutenberg
 *
 * @author   Magazine3
 * @category Backend
 * @path  modules/gutenberg/includes/class-gutenberg
 * @Since Version 1.9.7
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class SASWP_Gutenberg {

        /**
         * Static private variable to hold instance this class
         * @var type 
         */
        private static $instance;
        private $service;
        private $render;
        
        private $blocks = array(
            'collection' => array(            
                'handler'      => 'saswp-collection-js-reg',                
                'local_var'    => 'saswpGutenbergCollection',
                'block_name'   => 'collection-block',
                'render_func'  => 'render_collection_data',
                'style'        => 'saswp-g-collection-css',
                'editor'       => 'saswp-gutenberg-css-reg-editor',
                'local'        => array()            
            ),
            'course' => array(            
                'handler'      => 'saswp-course-js-reg',                
                'local_var'    => 'saswpGutenbergCourse',
                'block_name'   => 'course-block',
                'render_func'  => 'render_course_data',
                'style'        => 'saswp-g-course-css',
                'editor'       => 'saswp-gutenberg-css-reg-editor',
                'local'        => array()            
            ),
            'event' => array(            
                'handler'      => 'saswp-event-js-reg',                
                'local_var'    => 'saswpGutenbergEvent',
                'block_name'   => 'event-block',
                'render_func'  => 'render_event_data',
                'style'        => 'saswp-g-event-css',
                'editor'       => 'saswp-gutenberg-css-reg-editor',
                'local'        => array()            
            ),
            'job' => array(            
                'handler'      => 'saswp-job-js-reg',                
                'local_var'    => 'saswpGutenbergJob',
                'block_name'   => 'job-block',
                'style'        => 'saswp-g-job-css',
                'editor'       => 'saswp-gutenberg-css-reg-editor',
                'render_func'  => 'render_job_data',
                'local'        => array()            
            ),            
            'faq' => array(            
                'handler'      => 'saswp-faq-js-reg',                
                'local_var'    => 'saswpGutenbergFaq',
                'block_name'   => 'faq-block',
                'style'        => 'saswp-g-faq-css',
                'editor'       => 'saswp-gutenberg-css-reg-editor',
                'render_func'  => 'render_faq_data',
                'local'        => array()            
            ),
            'howto' => array(            
                'handler'      => 'saswp-how-to-js-reg',                                
                'block_name'   => 'how-to-block',
                'render_func'  => 'render_how_to_data',
                'style'        => 'saswp-g-howto-css',
                'editor'       => 'saswp-gutenberg-css-reg-editor',
                'local_var'    => 'saswpGutenbergHowTo',
                'local'        => array()
            ),
        );

        /**
         * This is class constructer to use all the hooks and filters used in this class
         */
        private function __construct() {
            
                    foreach ($this->blocks as $key => $value) {
                        $this->blocks[$key]['path'] = SASWP_PLUGIN_URL. '/modules/gutenberg/assets/blocks/'.$key.'.js'; 
                    }

                    if($this->service == null){
                        require_once SASWP_DIR_NAME.'/modules/gutenberg/includes/service.php';
                        $this->service = new SASWP_Gutenberg_Service();
                    }
                    if($this->render == null){
                        require_once SASWP_DIR_NAME.'/modules/gutenberg/includes/render.php';
                        $this->render = new SASWP_Gutenberg_Render();
                    }
                                                                                                                                            
                    add_action( 'init', array( $this, 'register_saswp_blocks' ) );                    
                    add_action( 'enqueue_block_editor_assets', array( $this, 'register_admin_assets' ) ); 
                    add_action( 'enqueue_block_assets', array( $this, 'register_frontend_assets' ) ); 
                    add_filter( 'block_categories', array( $this, 'add_blocks_categories' ) );  
                    add_action( 'amp_post_template_css', array($this, 'register_frontend_assets_amp'));
        }
        
        public function register_frontend_assets_amp(){
            
             global $post;
             
             if(function_exists('parse_blocks') && is_object($post)){
                 
                  $blocks = parse_blocks($post->post_content);
                  
                   if($blocks){
                       
                        foreach ($blocks as $parse_blocks){
                            
                            if(isset($parse_blocks['blockName']) && $parse_blocks['blockName'] === 'saswp/event-block'){
                                $amp_css  =  SASWP_PLUGIN_DIR_PATH . 'modules/gutenberg/assets/css/amp/event.css';              
                                echo @file_get_contents($amp_css);
                            }
                            if(isset($parse_blocks['blockName']) && $parse_blocks['blockName'] === 'saswp/job-block'){
                                $amp_css  =  SASWP_PLUGIN_DIR_PATH . 'modules/gutenberg/assets/css/amp/job.css';              
                                echo @file_get_contents($amp_css);
                            }
                            if(isset($parse_blocks['blockName']) && $parse_blocks['blockName'] === 'saswp/course-block'){
                                $amp_css  =  SASWP_PLUGIN_DIR_PATH . 'modules/gutenberg/assets/css/amp/course.css';              
                                echo @file_get_contents($amp_css);
                            }
                            
                        }
                        
                   }
             }
                                                
        }
        /**
         * Function to enqueue frontend assets for gutenberg blocks
         * @Since Version 1.9.7
         */
        public function register_frontend_assets() {
                                                                      
                        global $post;
             
                        if(function_exists('parse_blocks') && is_object($post)){

                             $blocks = parse_blocks($post->post_content);

                              if($blocks){

                                   foreach ($blocks as $parse_blocks){

                                       if(isset($parse_blocks['blockName']) && $parse_blocks['blockName'] === 'saswp/event-block'){
                                           
                                           wp_enqueue_style(
                                                'saswp-g-event-css',
                                                SASWP_PLUGIN_URL . '/modules/gutenberg/assets/css/event.css',
                                                array()                        
                                           );
                                           
                                       }
                                       if(isset($parse_blocks['blockName']) && $parse_blocks['blockName'] === 'saswp/job-block'){
                                           
                                           wp_enqueue_style(
                                                'saswp-g-job-css',
                                                SASWP_PLUGIN_URL . '/modules/gutenberg/assets/css/job.css',
                                                array()                        
                                           );
                                           
                                       }
                                       if(isset($parse_blocks['blockName']) && $parse_blocks['blockName'] === 'saswp/course-block'){
                                           
                                           wp_enqueue_style(
                                                'saswp-g-course-css',
                                                SASWP_PLUGIN_URL . '/modules/gutenberg/assets/css/course.css',
                                                array()                        
                                           );
                                           
                                       }

                                   }

                              }
                        }                        
                                                                           
	}
        /**
         * Function to enqueue admin assets for gutenberg blocks
         * @Since Version 1.9.7
         */
        public function register_admin_assets() {
            
                    if ( !function_exists( 'register_block_type' ) ) {
                            // no Gutenberg, Abort
                            return;
                    }		                  		                                           
                     wp_register_style(
                        'saswp-gutenberg-css-reg-editor',
                        SASWP_PLUGIN_URL . 'modules/gutenberg/assets/css/editor.css',
                        array( 'wp-edit-blocks' )
                    );
                     
                    if($this->blocks){
                    
                        foreach($this->blocks as $key => $block){
                        
                            wp_register_script(
                               $block['handler'],
                               $block['path'],
                               array( 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-editor' )                                 
                           );
                            
                            if($key == 'collection'){
                                
                                 $review_service = new saswp_reviews_service();
                                 $col_opt  = $review_service->saswp_get_collection_list();
                                        
                                if($col_opt){                                    
                                    $block['local']['collection'] = $col_opt;
                                }else{
                                    $block['local']['collection_not_found']      = true;
                                    $block['local']['collection_url']            = wp_nonce_url(admin_url('admin.php?page=collection'), '_wpnonce');
                                }
                               }
                                                    
                            wp_localize_script( $block['handler'], $block['local_var'], $block['local'] );
                         
                            wp_enqueue_script( $block['handler'] );
                        }
                        
                    } 
                                                         
	}
        /**
         * Register a how to block
         * @return type
         * @since version 1.9.7
         */
	public function register_saswp_blocks() {
            
                    if ( !function_exists( 'register_block_type' ) ) {
                            // no Gutenberg, Abort
                            return;
                    }		                  		    
                     
                   if($this->blocks){
                    
                    foreach($this->blocks as $block){

                        register_block_type( 'saswp/'.$block['block_name'], array(
                            'style'           => $block['style'],
                            'editor_style'    => $block['editor'],
                            'editor_script'   => $block['handler'],
                            'render_callback' => array( $this, $block['render_func'] ),
                      ) );
                        
                    }
                                      
                }                                        
	}
        
        public function render_collection_data($attributes){
            
            ob_start();
            
            if ( !isset( $attributes ) ) {
			ob_end_clean();
                                                                       
			return '';
            }
            
            echo $this->render->collection_block_data($attributes);
            
            return ob_get_clean();
            
        }
        
        public function render_course_data($attributes){
            
            ob_start();
            
            if ( !isset( $attributes ) ) {
			ob_end_clean();
                                                                       
			return '';
            }
            
            echo $this->render->course_block_data($attributes);
            
            return ob_get_clean();
            
        }        
        
        public function render_job_data($attributes){
            
            ob_start();
            
            if ( !isset( $attributes ) ) {
			ob_end_clean();
                                                                       
			return '';
            }
            
            echo $this->render->job_block_data($attributes);
            
            return ob_get_clean();
            
        }
        
        public function render_event_data($attributes){
            
            ob_start();
            
            if ( !isset( $attributes ) ) {
			ob_end_clean();
                                                                       
			return '';
            }
            
            echo $this->render->event_block_data($attributes);
            
            return ob_get_clean();
            
        }
        
        /**
         * Function to render faq block data in frontend post content
         * @param type $attributes
         * @return string
         * @since version 1.9.7
         */
        public static function render_faq_data( $attributes ) {
                                        
		ob_start();
		
		if ( !isset( $attributes ) ) {
			ob_end_clean();
                                                                       
			return '';
		}
                
                echo '<div class="saswp-faq-block-section">';                                
                if($attributes['items']){
                    
                    $className = '';
                    if(isset($attributes['className'])){
                        $className = 'class="'.esc_attr($attributes['className']).'"';
                    }
                    
                    if(!isset($attributes['toggleList'])){
                     echo '<ol '.$className.'>';   
                    }else{
                     echo '<ul '.$className.'>';      
                    }
                    
                    foreach($attributes['items'] as $item){
                        
                      if($item['title'] || $item['description']){
                        echo '<li>'; 
                        echo '<strong class="saswp-faq-question-title">'. html_entity_decode(esc_attr($item['title'])).'</strong>';
                        echo '<p class="saswp-faq-answer-text">'.html_entity_decode(esc_textarea($item['description'])).'</p>';
                        echo '</li>';
                      }  
                       
                    }                    
                    if(!isset($attributes['toggleList'])){
                     echo '</ol>';   
                    }else{
                     echo '</ul>';    
                    }                    
                }
                                
                echo '</div>';
                						
		return ob_get_clean();
	}
        /**
         * Function to render 'howto' block data in frontend post content
         * @param type $attributes
         * @return string
         * @since version 1.9.7
         */
	public static function render_how_to_data( $attributes ) {
                                                                    
		ob_start();
		
		if ( !isset( $attributes ) ) {
			ob_end_clean();
                                                                       
			return '';
		}
                
                echo '<div class="saswp-how-to-block-section">';
                
                echo '<div class="saswp-how-to-block-steps">';
                
                if(isset($attributes['hasCost'])){
                    echo '<p class="saswp-how-to-total-time">';
                    
                    $time_html = '';
                       
                    if(isset($attributes['price']) && $attributes['price'] != ''){
                        $time_html .=   esc_attr($attributes['price']). ' ';
                    }
                    
                    if(isset($attributes['currency']) && $attributes['currency'] != ''){
                        $time_html .=     esc_attr($attributes['currency']);
                    }
                                        
                    if($time_html !=''){
                     echo '<span class="saswp-how-to-duration-time-text"><strong>'.saswp_label_text('translation-estimate-cost').' :</strong> </span>';    
                     echo $time_html;
                    }
                                        
                    echo '</p>';
                }

                if(isset($attributes['hasDuration'])){
                    echo '<p class="saswp-how-to-total-time">';
                    
                    $time_html = '';
                       
                    if(isset($attributes['days']) && $attributes['days'] != ''){
                        $time_html .=   esc_attr($attributes['days']).' days ';
                    }
                    
                    if(isset($attributes['hours']) && $attributes['hours'] != ''){
                        $time_html .=     esc_attr($attributes['hours']).' hours ';
                    }
                    
                    if(isset($attributes['minutes']) && $attributes['minutes'] != ''){
                        $time_html .=     esc_attr($attributes['minutes']).' minutes';
                    }
                    
                    if($time_html !=''){
                     echo '<span class="saswp-how-to-duration-time-text"><strong>'.saswp_label_text('translation-time-needed').' :</strong> </span>';    
                     echo $time_html;
                    }
                                        
                    echo '</p>';
                }                
                if(isset($attributes['description'])){
                    echo '<p>'.html_entity_decode(esc_attr($attributes['description'])).'</p>';
                }
                                
                if(isset($attributes['items'])){
                    
                    $className = '';
                    if(isset($attributes['className'])){
                        $className = 'class="'.esc_attr($attributes['className']).'"';
                    }
                        
                    if(!isset($attributes['toggleList'])){
                     echo '<ol '.$className.'>';   
                    }else{
                     echo '<ul '.$className.'>';      
                    }
                    
                    foreach($attributes['items'] as $item){
                        
                      if($item['title'] || $item['description']){
                        echo '<li>'; 
                        echo '<strong class="saswp-how-to-step-name">'. html_entity_decode(esc_attr($item['title'])).'</strong>';
                        echo '<p class="saswp-how-to-step-text">'.html_entity_decode(esc_textarea($item['description'])).'</p>';
                        echo '</li>';
                      }  
                       
                    }                    
                    if(!isset($attributes['toggleList'])){
                     echo '</ol>';   
                    }else{
                     echo '</ul>';    
                    }                    
                }                                
                echo '</div>';
                
                echo '<div class="saswp-how-to-block-tools">';
                
                if(!empty($attributes['tools'])){
                    
                    echo '<h5>'.saswp_label_text('translation-tools').'</h5>';
                    
                    echo '<ul>';
                    foreach($attributes['tools'] as $val){
                        if($val['name']){
                            echo '<li>'. wp_kses_post($val['name']).'</li>';
                        }
                        
                    }
                    echo '</ul>';
                    
                }
                                
                echo '</div>';
                
                echo '<div class="saswp-how-to-block-material">';
                
                if(!empty($attributes['materials'])){
                    
                    echo '<h5>'.saswp_label_text('translation-materials').'</h5>';  
                    
                    echo '<ul>';
                    foreach($attributes['materials'] as $val){

                        if($val['name']){
                            echo '<li>'. wp_kses_post($val['name']).'</li>';
                        }
                        
                    }
                    echo '</ul>';
                                        
                }
                                                
                echo '</div>';
                
                echo '</div>';
                						
		return ob_get_clean();
	}
        /**
         * Function to register schema blocks category in Gutenberg block's categories list
         * @param array $categories
         * @return array
         * @since version 1.9.7
         */	        
        public function add_blocks_categories($categories){
        
        $categories[] = array(
                'slug'  => 'saswp-blocks',
                'title' => 'Schema & Structured Data Blocks'
        );
        
        return $categories;
        
    }    
	        
        /**
         * Return the unique instance 
         * @return type instance
         * @since version 1.9.7
         */
        public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
    }

}

if ( class_exists( 'SASWP_Gutenberg') ) {
	SASWP_Gutenberg::get_instance();
}
