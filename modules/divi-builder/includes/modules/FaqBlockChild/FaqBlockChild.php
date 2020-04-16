<?php

class SASWP_Divi_FaqBlockChild extends ET_Builder_Module {
	// Module slug (also used as shortcode tag)
	public $slug                     = 'saswpfaqblockchild';

	// Module item has to use `child` as its type property
	public $type                     = 'child';

	// Module item's attribute that will be used for module item label on modal
	public $child_title_var          = 'question';

	// Full Visual Builder support
	public $vb_support = 'on';

	/**
	 * Module properties initialization
	 *
	 * @since 1.0.0
	 *
	 * @todo Remove $this->advanced_options['background'] once https://github.com/elegantthemes/Divi/issues/6913 has been addressed
	 */
	function init() {

		$this->advanced_setting_title_text = esc_html__( 'Question', 'schema-and-structured-data-for-wp' );
		$this->settings_text               = esc_html__( 'Question Settings', 'schema-and-structured-data-for-wp' );
		$this->main_css_element = '%%order_class%%';
		// Toggle settings
		$this->settings_modal_toggles  = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Content', 'schema-and-structured-data-for-wp' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'text' => array(
						'title'    => esc_html__( 'Text', 'schema-and-structured-data-for-wp' ),
						'priority' => 45,
						'bb_icons_support' => true,
					),
					'header' => array(
						'title'    => esc_html__( 'Heading Text', 'schema-and-structured-data-for-wp' ),
						'priority' => 49,
						'tabbed_subtoggles' => true,
						'sub_toggles' => array(
							'h2' => array(
								'name' => 'H2',
								'icon' => 'text-h2',
							),
							'h3' => array(
								'name' => 'H3',
								'icon' => 'text-h3',
							),


						),
					),
				),
			),
		);
	}

	public function get_fields() {
		return array(
			'faq_question' => array(
				'label'           => esc_html__( 'Question', 'schema-and-structured-data-for-wp' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input your question here', 'schema-and-structured-data-for-wp' ),
				'toggle_slug'     => 'main_content',
			),
			'faq_answer' => array(
				'label'           => esc_html__( 'Answer', 'schema-and-structured-data-for-wp' ),
				'type'            => 'tiny_mce',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input your answer here', 'schema-and-structured-data-for-wp' ),
				'toggle_slug'     => 'main_content',
			),
		);


	}
	// public function get_advanced_fields_config() {
	// 	$advanced_fields = $this->advanced_tab_options;

	// 	$advanced_fields['fonts']  = array(
	// 		'main_text'   => array(
	// 			'label'    => esc_html__( 'Content', 'schema-and-structured-data-for-wp' ),
	// 			'css'      => array(
	// 				'main' => "{$this->main_css_element} .saswp_timeline_child_inner",
	// 			),
	// 			'font_size' => array(
	// 				'default' => absint( '14' ) . 'px',
	// 			),
	// 			'line_height' => array(
	// 				'default' => '1.4em',
	// 			),
	// 			'letter_spacing' => array(
	// 				'default' => '0px',
	// 			),
	// 			'toggle_slug' => 'text',
	// 		),
	// 		'header'   => array(
	// 			'label'    => esc_html__( 'Heading', 'schema-and-structured-data-for-wp' ),
	// 			'css'      => array(
	// 				'main' => "{$this->main_css_element} .saswp_timeline_child_inner h1",
	// 			),
	// 			'font_size' => array(
	// 				'default' => absint( et_get_option( 'body_header_size', '30' ) ) . 'px',
	// 			),
	// 			'toggle_slug' => 'header',
	// 			'sub_toggle'  => 'h1',
	// 		),
	// 		'header_2'   => array(
	// 			'label'    => esc_html__( 'Heading 2', 'schema-and-structured-data-for-wp' ),
	// 			'css'      => array(
	// 				'main' => "{$this->main_css_element} .saswp_timeline_child_inner h2",
	// 			),
	// 			'font_size' => array(
	// 				'default' => '26px',
	// 			),
	// 			'line_height' => array(
	// 				'default' => '1em',
	// 			),
	// 			'toggle_slug' => 'header',
	// 			'sub_toggle'  => 'h2',
	// 		),
	// 		'header_3'   => array(
	// 			'label'    => esc_html__( 'Heading 3', 'schema-and-structured-data-for-wp' ),
	// 			'css'      => array(
	// 				'main' => "{$this->main_css_element} .saswp_timeline_date h3",
	// 			),
	// 			'font_size' => array(
	// 				'default' => '22px',
	// 			),
	// 			'line_height' => array(
	// 				'default' => '1em',
	// 			),
	// 			'toggle_slug' => 'header',
	// 			'sub_toggle'  => 'h3',
	// 		),
	// 		'header_4'   => array(
	// 			'label'    => esc_html__( 'Heading 4', 'schema-and-structured-data-for-wp' ),
	// 			'css'      => array(
	// 				'main' => "{$this->main_css_element} h4",
	// 			),
	// 			'font_size' => array(
	// 				'default' => '18px',
	// 			),
	// 			'line_height' => array(
	// 				'default' => '1em',
	// 			),
	// 			'toggle_slug' => 'header',
	// 			'sub_toggle'  => 'h4',
	// 		),
	// 		'header_5'   => array(
	// 			'label'    => esc_html__( 'Heading 5', 'schema-and-structured-data-for-wp' ),
	// 			'css'      => array(
	// 				'main' => "{$this->main_css_element} h5",
	// 			),
	// 			'font_size' => array(
	// 				'default' => '16px',
	// 			),
	// 			'line_height' => array(
	// 				'default' => '1em',
	// 			),
	// 			'toggle_slug' => 'header',
	// 			'sub_toggle'  => 'h5',
	// 		),
	// 		'header_6'   => array(
	// 			'label'    => esc_html__( 'Heading 6', 'schema-and-structured-data-for-wp' ),
	// 			'css'      => array(
	// 				'main' => "{$this->main_css_element} h6",
	// 			),
	// 			'font_size' => array(
	// 				'default' => '14px',
	// 			),
	// 			'line_height' => array(
	// 				'default' => '1em',
	// 			),
	// 			'toggle_slug' => 'header',
	// 			'sub_toggle'  => 'h6',
	// 		),
	// 	);
	// 	return $advanced_fields;
	// }

	function render( $attrs, $content = null, $render_slug ) {
            
             global $saswp_divi_faq; 
             $saswp_divi_faq[] = $attrs;      
                          
	}
}
new SASWP_Divi_FaqBlockChild;