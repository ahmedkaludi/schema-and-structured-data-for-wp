<?php
namespace SASWPElementorModule;

/**
 * Class Plugin
 *
 * Main Plugin class
 * @since 1.2.0
 */
class SASWP_Elementor_Loader {

	/**
	 * Instance
	 *
	 * @since 1.2.0
	 * @access private
	 * @static
	 *
	 * @var Plugin The single instance of the class.
	 */
	private static $_instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.2.0
	 * @access public
	 *
	 * @return Plugin An instance of the class.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * widget_scripts
	 *
	 * Load required plugin core files.
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function widget_scripts() {
		wp_register_script( 'saswp-elementor-faq-widget', SASWP_PLUGIN_URL .'/modules/elementor/assets/js/faq-block.js', array('jquery'), SASWP_VERSION, true );
		wp_register_script( 'saswp-elementor-faq-widget', SASWP_PLUGIN_URL .'/modules/elementor/assets/js/qanda-block.js', array('jquery'), SASWP_VERSION, true );
		wp_register_script( 'saswp-elementor-how-to-widget', SASWP_PLUGIN_URL .'/modules/elementor/assets/js/how-to-block.js', array('jquery'), SASWP_VERSION, true );
	}

	/**
	 * Include Widgets files
	 *
	 * Load widgets files
	 *
	 * @since 1.2.0
	 * @access private
	 */
	private function include_widgets_files() {		
		require_once( __DIR__ . '/widgets/class-faq-block.php' );
		require_once( __DIR__ . '/widgets/class-qanda-block.php' );
		require_once( __DIR__ . '/widgets/class-howto-block.php' );
	}

	/**
	 * Register Widgets
	 *
	 * Register new Elementor widgets.
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function register_widgets() {
		// Its is now safe to include Widgets files
		$this->include_widgets_files();

		// Register Widgets		
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Faq_Block() );

		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Qanda_Block() );

		// Register Widgets		
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\HowTo_Block() );
	}

	public function register() {
		// Its is now safe to include Widgets files
		$this->include_widgets_files();

		// Register Widgets		
		\Elementor\Plugin::instance()->widgets_manager->register( new Widgets\Faq_Block() );

		\Elementor\Plugin::instance()->widgets_manager->register( new Widgets\Qanda_Block() );

		// Register Widgets		
		\Elementor\Plugin::instance()->widgets_manager->register( new Widgets\HowTo_Block() );
	}

	/**
	 *  Plugin class constructor
	 *
	 * Register plugin action hooks and filters
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function __construct() {

		// Register widget scripts
		add_action( 'elementor/frontend/after_register_scripts', [ $this, 'widget_scripts' ] );

		// Register widgets

		if(defined('ELEMENTOR_VERSION') && version_compare(ELEMENTOR_VERSION, '3.5.0') >= 0 ) {
			add_action( 'elementor/widgets/register', [ $this, 'register' ] );
		}
		else{
			add_action( 'elementor/widgets/widgets_registered', [ $this, 'register_widgets' ] );		
		}
				
	}
}
// Instantiate Plugin Class
SASWP_Elementor_Loader::instance();