<?php
namespace SASWPElementorModule\Widgets;

use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class HowTo_Block extends Widget_Base {

	public function get_name() {
		return 'saswp-how-to-block';
	}

	public function get_title() {
		return esc_html__( 'HowTo Block', 'schema-and-structured-data-for-wp' );
	}
        public function get_keywords() {
		return [ 'howto', 'how to','how to schema', 'schema', 'structured data' ];
	}
        public function get_icon() {
		return 'eicon-text';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'time_section',
			[
				'label' => esc_html__( 'Time Needed', 'schema-and-structured-data-for-wp' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		
		$this->add_control(
			'howto_days',
			[
				'label'       => esc_html__( 'Days', 'schema-and-structured-data-for-wp' ),
				'type'        =>   Controls_Manager::NUMBER, 
				'placeholder' => 'DD'                               
			]
		);

		$this->add_control(
			'howto_hours',
			[
				'label'       => esc_html__( 'Hours', 'schema-and-structured-data-for-wp' ),
				'type'        =>   Controls_Manager::NUMBER,                                
				'placeholder' => 'HH'  
			]
		);

		$this->add_control(
			'howto_minutes',
			[
				'label'       => esc_html__( 'Minutes', 'schema-and-structured-data-for-wp' ),
				'type'        =>   Controls_Manager::NUMBER,                                
				'placeholder' => 'MM'  
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'cost_section',
			[
				'label' => esc_html__( 'Estimate Cost', 'schema-and-structured-data-for-wp' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		
		$this->add_control(
			'howto_currency',
			[
				'label'            => esc_html__( 'Currency', 'schema-and-structured-data-for-wp' ),
				'type'             =>   Controls_Manager::TEXT,
				'placeholder'      =>   'USD',                                
			]
		);

		$this->add_control(
			'howto_price',
			[
				'label'       => esc_html__( 'Price', 'schema-and-structured-data-for-wp' ),
				'type'        =>   Controls_Manager::NUMBER,                                
				'placeholder' => '20'
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'description_section',
			[
				'label' => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'howto_description',
			[
				'label'            => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
				'type'             =>   Controls_Manager::TEXTAREA,
				'placeholder'      =>  esc_html__( 'Enter how to description', 'schema-and-structured-data-for-wp' ),                                
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'step_section',
			[
				'label' => esc_html__( 'Steps', 'schema-and-structured-data-for-wp' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);		

		$repeater = new Repeater();

		$repeater->add_control(
			'howto_step_title', [
				'label' => esc_html__( 'Step', 'schema-and-structured-data-for-wp' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter step title', 'schema-and-structured-data-for-wp' ),				
				'label_block' => true,                                
                                'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'howto_step_description', [
				'label' => esc_html__( 'Description', 'schema-and-structured-data-for-wp' ),
				'type' => Controls_Manager::WYSIWYG,				
				'show_label' => false                                
			]
		);        

		$this->add_control(
			'step_list',
			[
				'label' => esc_html__( 'Steps', 'schema-and-structured-data-for-wp' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'saswp_howto_step_title' => esc_html__( 'Step Title', 'schema-and-structured-data-for-wp' ),
						'saswp_howto_step_description' => esc_html__( 'Step Description. Click the edit button to change this text.', 'schema-and-structured-data-for-wp' ),
					]					
				],
				'title_field' => '{{{ howto_step_title }}}',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'tools_section',
			[
				'label' => esc_html__( 'Tools', 'schema-and-structured-data-for-wp' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$tool_repeater = new Repeater();

		$tool_repeater->add_control(
			'howto_tool_name', [
				'label' => esc_html__( 'Tool Name', 'schema-and-structured-data-for-wp' ),
				'type' => Controls_Manager::TEXT,				
				'placeholder' => esc_html__( 'Enter tool name', 'schema-and-structured-data-for-wp' ),
				'label_block' => true,                                
                                'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'tool_list',
			[
				'label' => esc_html__( 'Tool', 'schema-and-structured-data-for-wp' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $tool_repeater->get_controls(),				
				'title_field' => '{{{ howto_tool_name }}}',
				'prevent_empty' => false
			]
		);
				
		$this->end_controls_section();

		$this->start_controls_section(
			'materials_section',
			[
				'label' => esc_html__( 'Materials', 'schema-and-structured-data-for-wp' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$material_repeater = new Repeater();

		$material_repeater->add_control(
			'howto_material_name', [
				'label' => esc_html__( 'Material Name', 'schema-and-structured-data-for-wp' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter material name', 'schema-and-structured-data-for-wp' ),				
				'label_block' => true,                                
                                'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'material_list',
			[
				'label' => esc_html__( 'Material', 'schema-and-structured-data-for-wp' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $material_repeater->get_controls(),				
				'title_field' => '{{{ howto_material_name }}}',
				'prevent_empty' => false
			]
		);
				
		$this->end_controls_section();

		$this->start_controls_section(
			'settings_section',
			[
				'label' => esc_html__( 'Settings', 'schema-and-structured-data-for-wp' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'order_type',
			[
				'label'     => esc_html__( 'Order Type', 'schema-and-structured-data-for-wp' ),
				'type'      =>   Controls_Manager::SELECT,
                                'options' => [
                                        ''               => esc_html__( 'Select', 'schema-and-structured-data-for-wp' ),
								'order_list'     => esc_html__( 'Order List', 'schema-and-structured-data-for-wp' ),
								'unorder_list'   => esc_html__( 'Unorder List', 'schema-and-structured-data-for-wp' ),					
				],
			]
		);

		$this->end_controls_section();
		

	}

	protected function render() {
            
                global $saswp_elementor_howto;
            
				$settings              = $this->get_settings_for_display();
				$saswp_elementor_howto = $settings;
                $order_type            = $settings['order_type'];                
                
		if ( $settings['step_list'] ) {                    			
			
			echo '<div class="elementor-how-to-block-content">';

			if($settings['howto_days'] || $settings['howto_hours'] || $settings['howto_minutes']){
				echo '<div class="elementor-how-to-time-needed">';				
				echo '<strong>'.esc_html__( 'Time Needed', 'schema-and-structured-data-for-wp' ).' : </strong>';

				if($settings['howto_days']){
					echo esc_html( $settings['howto_days']) .' '. esc_html__( 'days', 'schema-and-structured-data-for-wp' ).' ';
				}
				if($settings['howto_hours']){
					echo esc_html( $settings['howto_hours']) .' '. esc_html__( 'hours', 'schema-and-structured-data-for-wp' ).' ';
				}
				if($settings['howto_minutes']){
					echo esc_html( $settings['howto_minutes']) .' '. esc_html__( 'minutes', 'schema-and-structured-data-for-wp' );
				}

				echo '</div>';
			}

			if($settings['howto_currency'] && $settings['howto_price']){
				echo '<div class="elementor-how-to-estimate-cost">';				
				echo '<strong> '.esc_html__( 'Estimate Cost', 'schema-and-structured-data-for-wp' ).' : </strong>';
				echo esc_html( $settings['howto_currency']). ' '. esc_html( $settings['howto_price']); 						
				echo '</div>';
			}
			if($settings['howto_description']){
				echo '<div class="elementor-how-to-description">';
				echo wp_kses($settings['howto_description'], wp_kses_allowed_html('post'));
				echo '</div>';
			}
			echo '<div class="elementor-repeater-steps-div">';
			echo '<ul>';
                        $i = 1;
			foreach (  $settings['step_list'] as $item ) {
				if($order_type == 'order_list' || $order_type == ''){
				?>
					<li style="list-style:none;" class="elementor-repeater-item-<?php echo esc_attr( $item['_id']); ?>">
				<?php 	
				}else{
				?>
					<li class="elementor-repeater-item-<?php echo esc_attr( $item['_id']); ?>">
				<?php	
				}
                                echo '<h3>';
                                
                                if($order_type == 'order_list'){
                                    echo '<span>'.esc_html( $i).'. </span>';
                                } 
                                echo esc_html( $item['howto_step_title']);
                                echo '</h3>';
								echo '<p>' . wp_kses($item['howto_step_description'], wp_kses_allowed_html('post')) . '</p>';
                                
                                $i++;
			}
			echo '</ul>';
			echo '</div>';

			if($settings['tool_list']){

				echo '<div class="elementor-repeater-tools-div">';
				echo '<h5>'.esc_html__( 'Tools', 'schema-and-structured-data-for-wp' ).'</h5>';
				echo '<ul>';
							$i = 1;
				foreach (  $settings['tool_list'] as $item ) {
					if($order_type == 'order_list' || $order_type == ''){
					?>
						<li style="list-style:none;" class="elementor-repeater-tool-<?php echo esc_attr( $item['_id']); ?>">
					<?php 	
					}else{
					?>
						<li class="elementor-repeater-tool-<?php echo esc_attr( $item['_id']); ?>">
					<?php	
					}
									echo '<strong>';
									
									if($order_type == 'order_list'){
										echo '<span>'.esc_html( $i).'. </span>';
									} 
									echo esc_html( $item['howto_tool_name']);
									echo '</strong>';				                                
									$i++;
				}
				echo '</ul>';
				echo '</div>';

			}

			if($settings['material_list']){

				echo '<div class="elementor-repeater-material-div">';
				echo '<h5>'.esc_html__( 'Material', 'schema-and-structured-data-for-wp' ).'</h5>';
				echo '<ul>';
							$i = 1;
				foreach (  $settings['material_list'] as $item ) {
					if($order_type == 'order_list' || $order_type == ''){
					?>
						<li style="list-style:none;" class="elementor-repeater-material-<?php echo esc_attr( $item['_id']); ?>">
					<?php 	
					}else{
					?>
						<li class="elementor-repeater-material-<?php echo esc_attr( $item['_id']); ?>">
					<?php	
					}
									echo '<strong>';
									
									if($order_type == 'order_list'){
										echo '<span>'.esc_html( $i).'. </span>';
									} 
									echo esc_html( $item['howto_material_name']);
									echo '</strong>';				                                
									$i++;
				}
				echo '</ul>';
				echo '</div>';

			}
			
			echo '</div>';
		}
	}

	protected function content_template() {
		?>
		<# if ( settings.step_list.length ) { 
                
                var order_type = settings['order_type'];                
                var step_style = '';
                
                if(order_type == 'order_list' || order_type == ''){
                    step_style = 'style="list-style:none"';
                }
                
                #>	
				<div class="elementor-how-to-block-content">
				<# if(settings['howto_days'] || settings['howto_hours'] || settings['howto_minutes']) { #>
				<div class="elementor-how-to-time-needed">				
				<strong>Time Needed : </strong>
					<# if(settings['howto_days']) { #>
						{{{ settings['howto_days'] }}} days
					<# } #>
					<# if(settings['howto_hours']) { #>
						{{{ settings['howto_hours'] }}} hours
					<# } #>
					<# if(settings['howto_minutes']) { #>
						{{{ settings['howto_minutes'] }}} minutes
					<# } #>					
				</div>
				<# } #>

				<# if(settings['howto_currency'] && settings['howto_price']) { #>
				<div class="elementor-how-to-estimate-cost">				
				<strong>Estimate Cost : </strong>
					{{{ settings['howto_currency'] }}}
					{{{ settings['howto_price']}}}			
				</div>
				<# } #>		

				<# if(settings['howto_description'])  { #>		
					<div class="elementor-how-to-description">
					{{{ settings['howto_description'] }}}
					</div>	
				<# } #>		
				<div class="elementor-repeater-steps-div">
                        <ul>
			<# _.each( settings.step_list, function( item, index ) { #>
				<li {{{step_style}}} class="elementor-repeater-item-{{ item._id }}">                                   
                                    <h3> 
                                        <# if(order_type == 'order_list'){ #>
                                        <span>{{{ index + 1 }}} .</span>
                                        <# } #>
                                        {{{ item.howto_step_title }}}
                                    </h3>
				<p>{{{ item.howto_step_description }}}</p>
                                </li>
			<# }); #>
			</ul>
			</div>

			<# if(settings.tool_list) { #>	
			<div class="elementor-repeater-tools-div">
				<h5>Tools</h5>
                        <ul>
			<# _.each( settings.tool_list, function( item, index ) { #>
				<li {{{step_style}}} class="elementor-reapter-tool-{{ item._id }}">                                   
                                    <strong> 
                                        <# if(order_type == 'order_list'){ #>
                                        <span>{{{ index + 1 }}} .</span>
                                        <# } #>
                                        {{{ item.howto_tool_name }}}
                                    </strong>				
                                </li>
			<# }); #>
			</ul>
			</div>
			<# } #>

			<# if(settings.material_list) { #>								
			<div class="elementor-repeater-material-div">
				<h5>Material</h5>
                        <ul>
			<# _.each( settings.material_list, function( item, index ) { #>
				<li {{{step_style}}} class="elementor-repeater-material-{{ item._id }}">                                   
                                    <strong> 
                                        <# if(order_type == 'order_list'){ #>
                                        <span>{{{ index + 1 }}} .</span>
                                        <# } #>
                                        {{{ item.howto_material_name }}}
                                    </strong>				
                                </li>
			<# }); #>
			</ul>
			</div>
			<# } #>
			</div>
		<# } #>
		<?php
	}
}