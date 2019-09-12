<?php

class SASWP_Gutenberg {

        private static $instance;
	    	
        private function __construct() {
                    add_action( 'init', array( $this, 'init' ) );                    
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
                        'gutenberg-examples-01',
                        SASWP_PLUGIN_URL . '/modules/gutenberg/js/saswp-gutenberg-blocks.js',
                        array( 'wp-blocks', 'wp-element' )
                    );

                    register_block_type( 'gutenberg-examples/example-01-basic', array(
                        'editor_script' => 'gutenberg-examples-01',
                    ) );
                    
                    $inline_script = array( 
                                 'title' => 'How To'
                    );            		
                    		
		 wp_localize_script( 'gutenberg-examples-01', 'saswpGutenberg', $inline_script );
        
                 wp_enqueue_script( 'gutenberg-examples-01' );
                    
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