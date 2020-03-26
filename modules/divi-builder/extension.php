<?php
if ( ! function_exists( 'saswp_initialize_extension' ) ):
/**
 * Creates the extension's main class instance.
 *
 * @since 1.0.0
 */
function saswp_initialize_extension() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/Extension.php';
}
add_action( 'divi_extensions_init', 'saswp_initialize_extension' );
endif;