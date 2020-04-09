<?php
namespace SASWPElementorModule\Widgets;

use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class Faq_Block extends Widget_Base {

	public function get_name() {
		return 'saswp-faq-block';
	}

	public function get_title() {
		return __( 'Faq Block', 'elementor' );
	}
        public function get_keywords() {
		return [ 'faq', 'faq schema', 'schema', 'structured data' ];
	}
        public function get_icon() {
		return 'eicon-text';
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'FAQ Block', 'plugin-name' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'faq_question', [
				'label' => __( 'Question', 'schema-and-structured-data-for-wp' ),
				'type' => Controls_Manager::TEXT,				
				'label_block' => true,
                                'default' => __( 'Question' , 'schema-and-structured-data-for-wp' ),
                                'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'faq_answer', [
				'label' => __( 'Answer', 'schema-and-structured-data-for-wp' ),
				'type' => Controls_Manager::WYSIWYG,				
				'show_label' => false,
                                'default' => __( 'Answer' , 'schema-and-structured-data-for-wp' ),
			]
		);

                $this->add_control(
			'order_type',
			[
				'label'     => __( 'Order Type', 'schema-and-structured-data-for-wp' ),
				'type'      =>   Controls_Manager::SELECT,
                                'options' => [
                                        ''               => __( 'Select', 'schema-and-structured-data-for-wp' ),
					'order_list'     => __( 'Order List', 'schema-and-structured-data-for-wp' ),
					'unorder_list'   => __( 'Unorder List', 'schema-and-structured-data-for-wp' ),					
				],
			]
		);

		$this->add_control(
			'list',
			[
				'label' => __( 'Question List', 'schema-and-structured-data-for-wp' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'saswp_faq_question' => __( 'First Question', 'schema-and-structured-data-for-wp' ),
						'saswp_faq_answer' => __( 'First Answer. Click the edit button to change this text.', 'schema-and-structured-data-for-wp' ),
					]					
				],
				'title_field' => '{{{ faq_question }}}',
			]
		);

		$this->end_controls_section();

	}

	protected function render() {
            
                global $saswp_elementor_faq;
            
				$settings            = $this->get_settings_for_display();
                $order_type          = $settings['order_type'];                
                $style               ='';
                
                if($order_type == 'order_list' || $order_type == ''){
                    $style = 'style="list-style:none"';
                }
                
		if ( $settings['list'] ) {
                    
                        $saswp_elementor_faq = $settings['list'];
			echo '<ul>';
                        $i = 1;
			foreach (  $settings['list'] as $item ) {
				echo '<li '.$style.' class="elementor-repeater-item-' . esc_attr($item['_id']) . '">';
                                echo '<h3>';
                                
                                if($order_type == 'order_list'){
                                    echo '<span>'.$i.'. </span>';
                                } 
                                echo esc_html($item['faq_question']);
                                echo '</h3>';
				echo '<p>' . wp_unslash($item['faq_answer']) . '</p>';
                                
                                $i++;
			}
			echo '</ul>';
		}
	}

	protected function _content_template() {
		?>
		<# if ( settings.list.length ) { 
                
                var order_type = settings['order_type'];                
                var step_style = '';
                
                if(order_type == 'order_list' || order_type == ''){
                    step_style = 'style="list-style:none"';
                }
                
                #>
                        <ul>
			<# _.each( settings.list, function( item, index ) { #>
				<li {{{step_style}}} class="elementor-repeater-item-{{ item._id }}">                                   
                                    <h3> 
                                        <# if(order_type == 'order_list'){ #>
                                        <span>{{{ index + 1 }}} .</span>
                                        <# } #>
                                        {{{ item.faq_question }}}
                                    </h3>
				<p>{{{ item.faq_answer }}}</p>
                                </li>
			<# }); #>
			</ul>
		<# } #>
		<?php
	}
}