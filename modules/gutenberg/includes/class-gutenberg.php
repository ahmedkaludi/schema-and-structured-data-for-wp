<?php

class SASWP_Gutenberg {

        private static $instance;
	    	
        private function __construct() {
                    add_action( 'init', array( $this, 'register_how_to' ) );
                    add_action( 'init', array( $this, 'register_faq' ) );
                    add_action( 'enqueue_block_editor_assets', array( $this, 'register_admin_assets' ) ); 
                    //add_action( 'enqueue_block_assets', array( $this, 'register_frontend_assets' ) ); 
                    add_filter( 'block_categories', array( $this, 'saswp_add_blocks_categories' ) );     
        }
        
        public function register_frontend_assets() {
                            
                    if(!is_admin){
                        
                        wp_enqueue_style(
                            'saswp-gutenberg-css-reg',
                            SASWP_PLUGIN_URL . '/modules/gutenberg/assets/css/style.css',
                            array()                        
                        );
                        
                    }                               
	}
        
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
                                         
                    wp_register_script(
                        'saswp-faq-js-reg',
                        SASWP_PLUGIN_URL . '/modules/gutenberg/assets/blocks/faq.js',
                        array( 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-editor' )
                    );
                    
                    $inline_script = array( 
                                 'title' => 'Faq'
                    );            		
                    		
		    wp_localize_script( 'saswp-how-to-js-reg', 'saswpGutenbergFaq', $inline_script );
        
                    wp_enqueue_script( 'saswp-faq-js-reg' );
                    
                    wp_register_script(
                        'saswp-how-to-js-reg',
                        SASWP_PLUGIN_URL . '/modules/gutenberg/assets/blocks/how-to.js',
                        array( 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-editor' )
                    );
                    
                    $inline_script = array( 
                                 'title' => 'How To'
                    );            		
                    		
		    wp_localize_script( 'saswp-how-to-js-reg', 'saswpGutenbergHowTo', $inline_script );
        
                    wp_enqueue_script( 'saswp-how-to-js-reg' );                    
	}
        /**
	 * Register blocks
	 */
	public function register_how_to() {
            
                    if ( !function_exists( 'register_block_type' ) ) {
                            // no Gutenberg, Abort
                            return;
                    }		                  		    
                     
                    register_block_type( 'saswp/how-to-block', array(
                        'style'         => 'saswp-gutenberg-css-reg',
                        'editor_style'  => 'saswp-gutenberg-css-reg-editor',
                        'editor_script' => 'saswp-how-to-js-reg',
                        'render_callback' => array( $this, 'render_how_to_data' ),
                    ) );
                                        
                    
	}
        /**
	 * Register blocks
	 */
	public function register_faq() {
            
                    if ( !function_exists( 'register_block_type' ) ) {
                            // no Gutenberg, Abort
                            return;
                    }		                  
		                                                                                     
                    register_block_type( 'saswp/faq-block', array(
                        'style'         => 'saswp-gutenberg-css-reg',
                        'editor_style'  => 'saswp-gutenberg-css-reg-editor',
                        'editor_script' => 'saswp-faq-js-reg',
                        'render_callback' => array( $this, 'render_faq_data' ),
                    ) );
                                                            
	}
        
        public static function render_faq_data( $attributes ) {
                                        
		ob_start();
		
		if ( !isset( $attributes ) ) {
			ob_end_clean();
                                                                       
			return '';
		}
                
                echo '<div class="saswp-faq-block-section">';                                
                if($attributes['items']){
                    
                    if(!isset($attributes['toggleList'])){
                     echo '<ol>';   
                    }else{
                     echo '<ul>';      
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
                    echo '<span class="saswp-how-to-duration-time-text"><strong>Time Needed :</strong> </span>'; 
                                                            
                    echo esc_attr($attributes['days']).' days '.esc_attr($attributes['hours']).' hours '.esc_attr($attributes['minutes']).' minutes';
                    echo '</p>';
                }                
                if(isset($attributes['description'])){
                    echo '<p>'.esc_attr($attributes['description']).'</p>';
                }
                                
                if(isset($attributes['items'])){
                    
                    if(!isset($attributes['toggleList'])){
                     echo '<ol>';   
                    }else{
                     echo '<ul>';      
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
        	        
        public function saswp_add_blocks_categories($categories){
        
        $categories[] = array(
                'slug'  => 'saswp-blocks',
                'title' => 'Schema & Structured Data Blocks'
        );
        
        return $categories;
        
    }    
	        
        /**
     * Return the unique instance 
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