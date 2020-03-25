<?php
/**
 * Reviews Widget Class
 *
 * @author   Magazine3
 * @category Admin
 * @path     reviews/reviews_widget
 * @Version 1.9
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Adds Saswp_Google_Review_Widget widget.
 */
class Saswp_Reviews_Widget extends WP_Widget {

        private $_serviceClass = null; 
    /**
	 * Register widget with WordPress.
	 */
	function __construct() {
            
            if($this->_serviceClass == null){
                $this->_serviceClass = new saswp_reviews_service();  
            }
            
		parent::__construct(
			'saswp_google_review_widget', // Base ID
			esc_html__( 'Reviews', 'schema-and-structured-data-for-wp' ), // Name
			array( 'description' => esc_html__( 'Widget to display Reviews', 'schema-and-structured-data-for-wp' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
                          
        echo html_entity_decode(esc_attr($args['before_widget']));
	
                
        if(saswp_global_option()){
          
            $attr = array(
                'count' => $instance['g_review']
            );  
                            
            $reviews = $this->_serviceClass->saswp_get_reviews_list_by_parameters($attr);

            if($reviews){
                   global $saswp_post_reviews;
                   $saswp_post_reviews = array_merge($saswp_post_reviews, $reviews);    
                   echo $this->_serviceClass->saswp_reviews_html_markup($reviews);                                                                                         
            }
                        
        }
        
        echo html_entity_decode(esc_attr($args['after_widget']));
    }

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
            
            
		$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Review Title', 'schema-and-structured-data-for-wp' );
                $ads   = ! empty( $instance['g_review'] ) ? $instance['g_review'] : esc_html__( 'review list to be display', 'schema-and-structured-data-for-wp' );?>
                <p>
                    <label for="<?php echo esc_attr( $this->get_field_id( 'g_review' ) ); ?>">
        		<?php esc_attr_e( 'Reviews :', 'schema-and-structured-data-for-wp' ); ?>
                    </label>
                    <input id="<?php echo esc_attr( $this->get_field_id( 'g_review' )); ?>" name="<?php echo esc_attr( $this->get_field_name( 'g_review' ) ); ?>" type="text" placeholder="review count" value="<?php echo (isset($instance['g_review']) ? $instance['g_review'] : 5); ?>">
                </p>
            <?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
            
		$instance = array();                		
                $instance['g_review'] = ( ! empty( $new_instance['g_review'] ) ) ? sanitize_text_field( $new_instance['g_review'] ) : '';                                
		return $instance;
                
	}

} // class Saswp_Google_Review_Widget

/**
 * We are registering our widget here in wordpress
 */
function register_saswp_reviews_widget(){
    register_widget('Saswp_Reviews_Widget');
}
add_action('widgets_init', 'register_saswp_reviews_widget');