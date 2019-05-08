<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Adds Saswp_Google_Review_Widget widget.
 */
class Saswp_Google_Review_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'saswp_google_review_widget', // Base ID
			esc_html__( 'Schema Google Review', 'schema-and-structured-data-for-wp' ), // Name
			array( 'description' => esc_html__( 'Widget to display google reviews', 'schema-and-structured-data-for-wp' ), ) // Args
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
//		if ( ! empty( $instance['title'] ) ) {
//			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
//		}
                                
                $object = new saswp_google_review();
                $all_ads = $object->saswp_fetch_all_google_review_post();
                $goolge_review_obj = new saswp_google_review();
                
                foreach($all_ads as $ad){
                    
                    if($ad->ID == $instance['g_review']){   
                                                   
                            $ad_code =  $object->saswp_google_review_front_output($instance['g_review']); 
                            
                            $goolge_review = $goolge_review_obj->saswp_get_google_review_schema_markup($instance['g_review']);
                            
                            if($goolge_review){
                                
                                echo $ad_code.$goolge_review;    
                            }else{
                                
                                echo $ad_code;    
                            }
                                                                                    
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
                $ads   = ! empty( $instance['g_review'] ) ? $instance['g_review'] : esc_html__( 'review list to be display', 'schema-and-structured-data-for-wp' );                                
                
		?>

<!--		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
                    <?php esc_attr_e( 'Title:', 'schema-and-structured-data-for-wp' ); ?></label> 
		<input 
                    class="widefat" 
                    id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" 
                    name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" 
                    type="text" 
                    value="<?php echo esc_attr( $title ); ?>">
		</p>-->
                
                <p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'g_review' ) ); ?>">
                    <?php esc_attr_e( 'Places :', 'schema-and-structured-data-for-wp' ); ?>
                </label> 
                
                 <?php 
                 
                 $ads_select_html = '';                 
                 $object  = new saswp_google_review();
                 $all_ads = $object->saswp_fetch_all_google_review_post();                 
                 
                 foreach($all_ads as $ad){
                     
                     $ads_select_html .= '<option '. esc_attr(selected( $ads, $ad->ID, false)).' value="'.esc_attr($ad->ID).'">'.esc_html__($ad->post_title, 'schema-and-structured-data-for-wp').'</option>';
                     
                 }
                                                   
                 echo '<select id="'.esc_attr( $this->get_field_id( 'g_review' )).'" name="'.esc_attr( $this->get_field_name( 'g_review' )).'">'
                         .$ads_select_html.                         
                      '</select>';
                 ?>                       
		
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
		//$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
                $instance['g_review'] = ( ! empty( $new_instance['g_review'] ) ) ? sanitize_text_field( $new_instance['g_review'] ) : '';                                
		return $instance;
                
	}

} // class Adsforwp_Ads_Widget


/**
 * We are registering our widget here in wordpress
 */
function register_saswp_google_review_widget(){
    register_widget('Saswp_Google_Review_Widget');
}

add_action('widgets_init', 'register_saswp_google_review_widget');