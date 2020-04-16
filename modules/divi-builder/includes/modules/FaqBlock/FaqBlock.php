<?php
class SASWP_Divi_FaqBlock extends ET_Builder_Module {

	public $slug       = 'saswp_divi_faqblock';
	public $vb_support = 'on';
	public $child_slug = 'saswpfaqblockchild';

	protected $module_credits = array(
		'module_uri' => 'https://structured-data-for-wp.com/',
		'author'     => 'Magazine3',
		'author_uri' => 'https://structured-data-for-wp.com/',
	);

	public function init() {
		$this->name = esc_html__( 'Faq Block', 'schema-and-structured-data-for-wp' );
		$this->main_css_element = '%%order_class%%';		
	}

	public function get_fields() {
		$fields = array(			
			'order_type' => array(				
				'label'           => esc_html__( 'Order Type', 'schema-and-structured-data-for-wp' ),
				'type'            => 'select',
                                'default'         => '',
				'options'         => array(
                                        ''               => __( 'Select', 'schema-and-structured-data-for-wp' ),
					'order_list'     => esc_html__( 'Order List', 'schema-and-structured-data-for-wp' ),
					'unorder_list'   => esc_html__( 'Unorder List', 'schema-and-structured-data-for-wp' ),					
				),
				'toggle_slug'     => 'main_content',
			)
		);	
		return $fields;
	}

        
	public function render( $attrs, $content = null, $render_slug ) {
            
                global $saswp_divi_faq; 

                $output     = '';                                                
                $style      = '';
                $order_type = '';
                
                if(isset($attrs['order_type'])){
                    $order_type = $attrs['order_type'];
                }
                
                if($order_type == 'order_list' || $order_type == ''){
                    $style = 'style="list-style:none"';
                }
                
		if ( $saswp_divi_faq) {
                                            
			$output .= '<ul>';
                        $i = 1;
			foreach (  $saswp_divi_faq as $item ) {

				if(isset($item['faq_question'])){
					
					$output .= '<li '.$style.' class="elementor-repeater-item-' . $i . '">';
                                $output .= '<h3>';
                                
                                if($order_type == 'order_list'){
                                    $output .= '<span>'.$i.'. </span>';
                                } 
                                $output .= esc_html($item['faq_question']);
                                $output .= '</h3>';
					$output .= '<p>' . wp_unslash(str_replace(array("%22", ""), array('"', "'"),$item['faq_answer'])) . '</p>';
                	$output .= '</li>';                
                                $i++;

				}				
			}
			$output .= '</ul>';
		}
                        
		return $output;
	}      

}
new SASWP_Divi_FaqBlock;