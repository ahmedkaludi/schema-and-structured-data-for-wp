<?php
namespace SASWPElementorModule\Widgets;

use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class Qanda_Block extends Widget_Base {

	public function get_name() {
		return 'saswp-qanda-block';
	}

	public function get_title() {
		return __( 'Q&A Block', 'elementor' );
	}
        public function get_keywords() {
		return [ 'Q&A', 'Q&A schema','qanda schema', 'schema', 'structured data' ];
	}
        public function get_icon() {
		return 'eicon-text';
	}

	protected function _register_controls() {

		//Question section starts here

		$this->start_controls_section(
			'question_section',
			[
				'label' => __( 'Question', 'schema-and-structured-data-for-wp' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'question_name',
			[
				'label'            => __( 'Name', 'schema-and-structured-data-for-wp' ),
				'type'             =>   Controls_Manager::TEXT,
				'placeholder'      =>  __( 'Enter Question Name', 'schema-and-structured-data-for-wp' ),                                
			]
		);

		$this->add_control(
			'question_text',
			[
				'label'            => __( 'Text', 'schema-and-structured-data-for-wp' ),
				'type'             =>   Controls_Manager::TEXTAREA,
				'placeholder'      =>  __( 'Enter Question Description', 'schema-and-structured-data-for-wp' ),                                
			]
		);

		$this->add_control(
			'question_vote',
			[
				'label'            => __( 'Vote', 'schema-and-structured-data-for-wp' ),
				'type'             =>   Controls_Manager::NUMBER,				
			]
		);

		$this->add_control(
			'question_author',
			[
				'label'            => __( 'Author', 'schema-and-structured-data-for-wp' ),
				'type'             =>   Controls_Manager::TEXT,
				'placeholder'      =>  __( 'Enter author name', 'schema-and-structured-data-for-wp' ),                                
			]
		);

		$this->add_control(
			'question_date',
			[
				'label'            => __( 'Date', 'schema-and-structured-data-for-wp' ),
				'type'             =>   Controls_Manager::DATE_TIME,				
			]
		);

		$this->end_controls_section();


		//Question section ends here

		//Accepted Ansesers section starts here
		$this->start_controls_section(
			'accepted_answers_section',
			[
				'label' => __( 'Accepted Answers', 'schema-and-structured-data-for-wp' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$accepted_repeater = new Repeater();

		$accepted_repeater->add_control(
			'text', [
				'label' => __( 'Text', 'schema-and-structured-data-for-wp' ),
				'type' => Controls_Manager::TEXTAREA,				
				'label_block' => true                              
			]
		);

		$accepted_repeater->add_control(
			'vote', [
				'label' => __( 'Vote', 'schema-and-structured-data-for-wp' ),
				'type' => Controls_Manager::NUMBER,				
				'show_label' => true                
			]
		);          
		$accepted_repeater->add_control(
			'url', [
				'label' => __( 'URL', 'schema-and-structured-data-for-wp' ),
				'type' => Controls_Manager::TEXT,				
				'show_label' => true                
			]
		);          
		$accepted_repeater->add_control(
			'author', [
				'label' => __( 'Author', 'schema-and-structured-data-for-wp' ),
				'type' => Controls_Manager::TEXT,				
				'show_label' => true                
			]
		);
		$accepted_repeater->add_control(
			'date', [
				'label' => __( 'Date Created', 'schema-and-structured-data-for-wp' ),
				'type' => Controls_Manager::DATE_TIME,				
				'show_label' => true                
			]
		);          

		$this->add_control(
			'accepted_answers',
			[
				'label' => __( 'Accepted Answers List', 'schema-and-structured-data-for-wp' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $accepted_repeater->get_controls(),				
				'title_field' => '{{{ text }}}',
			]
		);

		$this->end_controls_section();
		//Accepted answers section ends here

		//suggested Ansesers section starts here
		$this->start_controls_section(
			'suggested_answers_section',
			[
				'label' => __( 'Suggested Answers', 'schema-and-structured-data-for-wp' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$suggested_repeater = new Repeater();

		$suggested_repeater->add_control(
			'text', [
				'label' => __( 'Text', 'schema-and-structured-data-for-wp' ),
				'type' => Controls_Manager::TEXTAREA,				
				'label_block' => true                              
			]
		);

		$suggested_repeater->add_control(
			'vote', [
				'label' => __( 'Vote', 'schema-and-structured-data-for-wp' ),
				'type' => Controls_Manager::NUMBER,				
				'show_label' => true                
			]
		);          
		$suggested_repeater->add_control(
			'url', [
				'label' => __( 'URL', 'schema-and-structured-data-for-wp' ),
				'type' => Controls_Manager::TEXT,				
				'show_label' => true                
			]
		);          
		$suggested_repeater->add_control(
			'author', [
				'label' => __( 'Author', 'schema-and-structured-data-for-wp' ),
				'type' => Controls_Manager::TEXT,				
				'show_label' => true                
			]
		);
		$suggested_repeater->add_control(
			'date', [
				'label' => __( 'Date Created', 'schema-and-structured-data-for-wp' ),
				'type' => Controls_Manager::DATE_TIME,				
				'show_label' => true                
			]
		);          

		$this->add_control(
			'suggested_answers',
			[
				'label' => __( 'suggested Answers List', 'schema-and-structured-data-for-wp' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $suggested_repeater->get_controls(),				
				'title_field' => '{{{ text }}}',
			]
		);

		$this->end_controls_section();
		//suggested answers section ends here

	}

	protected function render() {
            
		 global $saswp_elementor_qanda;
	
		 $attributes            = $this->get_settings_for_display();
		 $saswp_elementor_qanda =  $attributes;                      
		 $response          	= '';
         $accepted_answers  	= '';
         $suggested_answers 	= '';        
         $question          	= '';

         $question  = '<div class="saswp-qanda-block-question">
                <h3>'.esc_html($attributes['question_name']).'</h3>
                <span class="saswp-qand-date">'.esc_html($attributes['question_date']).' '. __( 'Accepted Answers', 'schema-and-structured-data-for-wp' ).' '.esc_html($attributes['question_author']).'</span>                
                <p>'.esc_html($attributes['question_text']).'</p>
                '.__( 'Vote', 'schema-and-structured-data-for-wp' ).' <span class="dashicons dashicons-thumbs-up"></span> ('.esc_html($attributes['question_vote']).')
                </div>';
                
                if(isset($attributes['accepted_answers']) && !empty($attributes['accepted_answers'])){

                    foreach($attributes['accepted_answers'] as $answer){

                        $accepted_answers .= '<li>
                        <a href="'.esc_url($answer['url']).'">
                        <p>'.esc_html($answer['text']).'</p>                        
                        </a>
                        <span class="saswp-qand-date">'.esc_html($answer['date']).' '.__( 'By', 'schema-and-structured-data-for-wp' ).' <strong>'.esc_html($answer['author']).'</strong></span>                        
                        <br> '. __( 'Vote', 'schema-and-structured-data-for-wp' ).' <span class="dashicons dashicons-thumbs-up"></span> ('.esc_html($answer['vote']).')                        
                        </li>';
                       
                    }

                }

                if(isset($attributes['suggested_answers']) && !empty($attributes['suggested_answers'])){

                    foreach($attributes['suggested_answers'] as $answer){

                        $suggested_answers .= '<li>
                        <a href="'.esc_url($answer['url']).'">
                        <p>'.esc_html($answer['text']).'</p>                        
                        </a>
                        <span class="saswp-qand-date">'.esc_html($answer['date']).' '.__( 'by', 'schema-and-structured-data-for-wp' ).' <strong>'.esc_html($answer['author']).'</strong></span>                        
                        <br> '.__( 'vote', 'schema-and-structured-data-for-wp' ).' <span class="dashicons dashicons-thumbs-up"></span> ('.esc_html($answer['vote']).')                        
                        </li>';                       
                    }

                }
              //Escaping has been done above for all below html  
        $response = '<div class="saswp-qanda-block-html">
        '.$question.'
        <div class="saswp-qanda-block-answer"><h3>'.__( 'Accepted Answers', 'schema-and-structured-data-for-wp' ).'</h3>'.$accepted_answers.'</div>
        <div class="saswp-qanda-block-answer"><h3>'.__( 'Suggested Answers', 'schema-and-structured-data-for-wp' ).'</h3>'.$suggested_answers.'</div>
        </div>';
                
        echo $response;
	}

	protected function _content_template() {
		?>
		
		<# 
		
		if ( settings.question_name) {
		#>
		<h3>{{{settings.question_name}}}</h3>
		<p>{{{settings.question_text}}}</p>
		<#

		var q_date = new Date(settings.question_date);		

		if(q_date.getDate()){
			#>
			<span class="saswp-qand-date">{{{q_date.getDate()}}}-{{{q_date.getMonth()}}}-{{{q_date.getFullYear()}}}  {{{q_date.getHours()}}}:{{{q_date.getMinutes()}}}:{{{q_date.getSeconds()}}}</span>
			<#
		}

		if(settings.question_author){
			#>
			by <strong>{{{settings.question_author}}}</strong>                        
			<# 
		}

		if(settings.question_vote){
			#>
			<br> Vote <span class="dashicons dashicons-thumbs-up"></span> ({{{ settings.question_vote }}})
			<# 
		}

		}
		if ( settings.accepted_answers.length ) {					

			#>
			<h3><?php echo __( 'Accepted Answers', 'schema-and-structured-data-for-wp' ); ?></h3>
            <ul>
			<# _.each( settings.accepted_answers, function( item, index ) { 
				
				var date = new Date( item.date );
				
				if(item.text){

				#>
				<li class="elementor-repeater-item-{{ item._id }}">                                   
				<a href="{{ item.url }}">
                <p>{{{ item.text }}}</p>                        
				</a>
				<# 
				if(date.getDate()){
					#>
					<span class="saswp-qand-date">{{{date.getDate()}}}-{{{date.getMonth()}}}-{{{date.getFullYear()}}}  {{{date.getHours()}}}:{{{date.getMinutes()}}}:{{{date.getSeconds()}}} </span>
					<# 
				}				
				if(item.author){
					#>
					by <strong>{{{item.author}}}</strong>                        
					<# 
				}

				if(item.vote){
					#>
					<br> <?php echo __( 'Vote', 'schema-and-structured-data-for-wp' ); ?> <span class="dashicons dashicons-thumbs-up"></span> ({{{ item.vote }}})
					<# 
				}
				#>	
				                
				</li>
				
			<# } }); #>
			</ul>
		<# } #>

		<# if ( settings.suggested_answers.length ) {					

			#>
			<h3><?php echo __( 'Suggested Answers', 'schema-and-structured-data-for-wp' ); ?></h3>
			<ul>
			<# _.each( settings.suggested_answers, function( item, index ) { 
				
				var date = new Date( item.date );
				
				if(item.text){

				#>
				<li class="elementor-repeater-item-{{ item._id }}">                                   
				<a href="{{ item.url }}">
				<p>{{{ item.text }}}</p>                        
				</a>
				<# 
				if(date.getDate()){
					#>
					<span class="saswp-qand-date">{{{date.getDate()}}}-{{{date.getMonth()}}}-{{{date.getFullYear()}}}  {{{date.getHours()}}}:{{{date.getMinutes()}}}:{{{date.getSeconds()}}} </span>
					<# 
				}				
				if(item.author){
					#>
					by <strong>{{{item.author}}}</strong>
					<# 
				}

				if(item.vote){
					#>
					<br> <?php echo __( 'Vote', 'schema-and-structured-data-for-wp' ); ?> <span class="dashicons dashicons-thumbs-up"></span> ({{{ item.vote }}})
					<# 
				}
				#>	
								
				</li>
				
			<# } }); #>
			</ul>
			<# } #>

		<?php
	}
}