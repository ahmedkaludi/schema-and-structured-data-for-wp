<?php

class SASWP_Gutenberg {

        private static $instance;
	    	
        private function __construct() {
                    add_action( 'init', array( $this, 'init' ) );     
                    add_filter( 'block_categories', array( $this, 'saswp_add_blocks_categories' ) );     
        }

	/**
	 * Register blocks
	 */
	public function init() {
            
                    if ( !function_exists( 'register_block_type' ) ) {
                            // no Gutenberg, Abort
                            return;
                    }		                  
		    wp_register_script(
                        'saswp-gutenberg-js-reg',
                        SASWP_PLUGIN_URL . '/modules/gutenberg/assets/js/saswp-gutenberg-blocks.js',
                        array( 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-editor' )
                    );
                    
                    
                     wp_register_style(
                        'saswp-gutenberg-css-reg-editor',
                        SASWP_PLUGIN_URL . '/modules/gutenberg/assets/css/editor.css',
                        array( 'wp-edit-blocks' )
                    );
 
                    wp_register_style(
                        'saswp-gutenberg-css-reg',
                        SASWP_PLUGIN_URL . '/modules/gutenberg/assets/css/style.css',
                        array( )                        
                    );
                     
                    register_block_type( 'saswp-gutenberg-blocks-namsp/how-to-block', array(
                        'style'         => 'saswp-gutenberg-css-reg',
                        'editor_style'  => 'saswp-gutenberg-css-reg-editor',
                        'editor_script' => 'saswp-gutenberg-js-reg',
                    ) );
                    
                    $inline_script = array( 
                                 'title' => 'How To'
                    );            		
                    		
		 wp_localize_script( 'saswp-gutenberg-js-reg', 'saswpGutenberg', $inline_script );
        
                 wp_enqueue_script( 'saswp-gutenberg-js-reg' );
                    
	}
        
    public function saswp_add_blocks_categories($categories){
        
        $categories[] = array(
                'slug'  => 'saswp-blocks',
                'title' => 'SASWP Blocks'
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