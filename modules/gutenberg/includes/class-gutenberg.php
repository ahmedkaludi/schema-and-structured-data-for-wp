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
            
            'event' => array(            
                'handler'      => 'saswp-event-js-reg',
                'path'         => SASWP_PLUGIN_URL . '/modules/gutenberg/assets/blocks/event.js',
                'local_var'    => 'saswpGutenbergEvent',
                'block_name'   => 'event-block',
                'render_func'  => 'render_event_data',
                'local'        => array()            
            ),
            
            'faq' => array(            
                'handler'      => 'saswp-faq-js-reg',
                'path'         => SASWP_PLUGIN_URL . '/modules/gutenberg/assets/blocks/faq.js',
                'local_var'    => 'saswpGutenbergFaq',
                'block_name'   => 'faq-block',
                'render_func'  => 'render_faq_data',
                'local'        => array()            
            ),
            'howto' => array(            
                'handler'      => 'saswp-how-to-js-reg',
                'path'         => SASWP_PLUGIN_URL . '/modules/gutenberg/assets/blocks/how-to.js',                
                'block_name'   => 'how-to-block',
                'render_func'  => 'render_how_to_data',
                'local_var'    => 'saswpGutenbergHowTo',
                'local'        => array()
            ),
        );

        /**
         * This is class constructer to use all the hooks and filters used in this class
         */
        private function __construct() {
                
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
                    //add_action( 'enqueue_block_assets', array( $this, 'register_frontend_assets' ) ); 
                    add_filter( 'block_categories', array( $this, 'saswp_add_blocks_categories' ) );     
        }
        /**
         * Function to enqueue frontend assets for gutenberg blocks
         * @Since Version 1.9.7
         */
        public function register_frontend_assets() {
                            
                    if(!is_admin){
                        
                        wp_enqueue_style(
                            'saswp-gutenberg-css-reg',
                            SASWP_PLUGIN_URL . '/modules/gutenberg/assets/css/style.css',
                            array()                        
                        );
                        
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
                    
                        foreach($this->blocks as $block){
                        
                            wp_register_script(
                               $block['handler'],
                               $block['path'],
                               array( 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-editor' )                                 
                           );
                        
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
                            'style'         => 'saswp-gutenberg-css-reg',
                            'editor_style'  => 'saswp-gutenberg-css-reg-editor',
                            'editor_script' => $block['handler'],
                            'render_callback' => array( $this, $block['render_func'] ),
                      ) );
                        
                    }
                                      
                }                                        
	}
        
        public function render_event_data($attributes){
            
            ob_start();
            
            if ( !isset( $attributes ) ) {
			ob_end_clean();
                                                                       
			return '';
            }
            
            //echo '';
            
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
                    echo '<p>'.esc_attr($attributes['description']).'</p>';
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
                            echo '<li>'. esc_attr($val['name']).'</li>';
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
                            echo '<li>'. esc_attr($val['name']).'</li>';
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
        public function saswp_add_blocks_categories($categories){
        
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