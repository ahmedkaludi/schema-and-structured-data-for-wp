<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class saswp_google_review{
        
    public function __construct() {
        	                
    }
    public function saswp_google_review_hooks(){
        
        add_action( 'init', array($this, 'saswp_add_google_review_menu_links'),20);  
        
        add_shortcode('saswp_google_review', array($this,'saswp_google_review_shortcode'));
        
        if(is_admin()){
            
            add_filter('get_edit_post_link', array($this, 'saswp_get_edit_post_link'), 99, 3);
            
        }
        
    }
        
    
    public function saswp_get_edit_post_link($link, $post_id, $context){
        
            $scr = get_current_screen();
        
            if ($scr->id == 'edit-saswp-google-review' && $context == 'display') {
                
                    return wp_nonce_url(admin_url('admin.php?post_id='.$post_id.'&page=collection'), '_wpnonce');
                    
                } else {
                    
                    return $link;
                
            }
    }
     
    public function saswp_add_google_review_menu_links() {
                        
        $collection_post_type = array(
	    'labels' => array(
	        'name' 			=> esc_html__( 'Google Review', 'schema-and-structured-data-for-wp' ),	        
	        'add_new' 		=> esc_html__( 'Add Place', 'schema-and-structured-data-for-wp' ),
	        'add_new_item'  	=> esc_html__( 'Edit Collection', 'schema-and-structured-data-for-wp' ),
                'edit_item'             => esc_html__( 'Edit AD','schema-and-structured-data-for-wp'),                
	    ),
      	'public' 		=> true,
      	'has_archive' 		=> false,
      	'exclude_from_search'	=> true,
    	'publicly_queryable'	=> false,
        'show_in_menu'          => 'edit.php?post_type=saswp',                
        'show_ui'               => true,
	'show_in_nav_menus'     => false,			
        'show_admin_column'     => true,        
	'rewrite'               => false,        
    );
    register_post_type( 'saswp-google-review', $collection_post_type );   
                                
    }
    
    public function saswp_fetch_all_google_review_post(){
            
              $all_post = get_posts(
                    array(
                            'post_type' 	 => 'saswp-google-review',
                            'posts_per_page'     => -1,   
                            'post_status'        => 'publish',
                    )
                 ); 
                                      
        return $all_post;        
    }
    
    
    public function saswp_google_review_shortcode($attr){
        
        $post_id = $attr['id'];
        
        if($post_id){   
                        
            return $this->saswp_google_review_front_output($post_id);
            
        }
        
    }
    
    public function saswp_google_review_front_output($post_id){
        
            global $wpdb;
            $reviews = null;
            $output  = '';
        
            $place_id = get_post_meta($post_id, $key='saswp_google_place_id', true ); 

            if($place_id){

                $place   = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "saswp_google_place WHERE place_id = %s", $place_id));

                if($place->id){

                    $reviews = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "saswp_google_review WHERE google_place_id = %d ORDER BY time DESC", $place->id));

                }

            }   
        
                     foreach ($reviews as $review){
                         
                         
                         $review_rating = $review->rating;
                                
                                $starating = '';
                                
                                $starating .= '<div class="saswp-rvw-str">';
                                for($j=0; $j<5; $j++){  
                                        
                                      if($review_rating >$j){
                                      
                                            $explod = explode('.', $review_rating);
                                            
                                            if(isset($explod[1])){
                                                
                                                if($j <$explod[0]){
                                                    
                                                    $starating.='<span class="str-ic"></span>';   
                                                    
                                                }else{
                                                    
                                                    $starating.='<span class="half-str"></span>';   
                                                    
                                                }                                           
                                            }else{
                                                
                                                $starating.='<span class="str-ic"></span>';    
                                                
                                            }
                                                                                                                           
                                      } else{
                                            $starating.='<span class="df-clr"></span>';   
                                      }                                                                                                                                
                                    }
                                $starating .= '</div>';
                                                                                                           
                                $output.= '<div class="saswp-g-review-panel">
                                          <div class="saswp-glg-review-body">
                                            <div class="saswp-rv-img">
                                                <img src="'.esc_url($review->profile_photo_url).'" alt="'.$review->author_name.'">
                                            </div>
                                            <div class="saswp-rv-cnt">
                                                <div class="saswp-str-rtng">
                                                    <div class="saswp-str">
                                                        <span class="saswp-athr">'.$review->author_name.'</span>
                                                        '.$starating.'                                  
                                                    </div> 
                                                    <span class="saswp-g-plus">
                                                        <a href="#"><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/google-img.png'.'"></a>
                                                    </span>
                                                </div>
                                                <span class="saswp-pt-dt">'.gmdate("H:i d M y", $review->time).'</span>
                                                <p>'.substr($review->text,0,300).'</p>
                                            </div>
                                          </div>
                                      </div>';

                            }
        
        return $output;
        
    }
    
                
}

if (class_exists('saswp_google_review')) {
    
	$object = new saswp_google_review;
        $object->saswp_google_review_hooks();
        
};

