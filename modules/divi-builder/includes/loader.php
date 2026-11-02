<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ET_Builder_Element' ) ) {
	return;
}

$saswp_module_files = glob( __DIR__ . '/modules/*/*.php' );

// Load custom Divi Builder modules
foreach ( (array) $saswp_module_files as $saswp_module_file ) {
	require_once $saswp_module_file;
}