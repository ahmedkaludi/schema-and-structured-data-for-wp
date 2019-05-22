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
            
            $id = '';
            
            if(is_object($scr)){
                $id = $scr->id;
            }
            
            if ($id == 'edit-saswp-google-review' && $context == 'display') {
                
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
                        
            if(saswp_global_option()){
                
                $schema_markup = $this->saswp_get_google_review_schema_markup($post_id);
                
            }                                                
            $output = $this->saswp_google_review_front_output($post_id);
            
            if($schema_markup){
               $output = $output.$schema_markup; 
            }
           return $output;
            
        }
        
    }
    
    public function saswp_get_google_review_schema_markup($post_id){
                
                        global $wpdb;
                        global $sd_data;                        
                        $html = '';  
                        
                        $place_id = get_post_meta($post_id, $key='saswp_google_place_id', true ); 

                        if($place_id){

                            $place   = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "saswp_google_place WHERE place_id = %s", $place_id));
                            
                        }
                                                            
                        $author_id      = get_the_author_meta('ID');
											
			$author_details	= get_avatar_data($author_id);
			$date 		= get_the_date("Y-m-d\TH:i:s\Z");
			$modified_date 	= get_the_modified_date("Y-m-d\TH:i:s\Z");
			$aurthor_name 	= get_the_author();
                        
                        if(!$aurthor_name){
				
                        $author_id    = get_post_field ('post_author', get_the_ID());
		        $aurthor_name = get_the_author_meta( 'display_name' , $author_id ); 
                        
			}
                        
                        
                        if($place->rating && isset($sd_data['saswp-google-review']) && $sd_data['saswp-google-review'] == 1){
                         
                            $total_score = esc_attr(number_format((float)$place->rating, 2, '.', ''));
                            
                            $input1 = array(
                                    '@context'       => 'http://schema.org',
                                    '@type'          => 'Review',
                                    'dateCreated'    => esc_html($date),
                                    'datePublished'  => esc_html($date),
                                    'dateModified'   => esc_html($modified_date),
                                    'headline'       => get_the_title(),
                                    'name'           => get_the_title(),                                    
                                    'url'            => get_permalink(),
                                    'description'    => strip_tags(strip_shortcodes(get_the_excerpt())),
                                    'copyrightYear'  => get_the_time( 'Y' ),                                                                                                           
                                    'author'	     => array(
                                                            '@type' 	=> 'Person',
                                                            'name'		=> esc_attr($aurthor_name),
                                                            'image'		=> array(
                                                                    '@type'			=> 'ImageObject',
                                                                    'url'			=> saswp_remove_warnings($author_details, 'url', 'saswp_string'),
                                                                    'height'                    => saswp_remove_warnings($author_details, 'height', 'saswp_string'),
                                                                    'width'			=> saswp_remove_warnings($author_details, 'width', 'saswp_string')
                                                            ),
							),                                                        
                                
                                    );
                                    
                                    $input1['itemReviewed'] = array(
                                            '@type' => 'Thing',
                                            'name'  => get_the_title(),
                                    );

                                    $input1['reviewRating'] = array(
                                        '@type'       => 'Rating',
                                        'worstRating' => 1,
                                        'bestRating'  => 5,
                                        'ratingValue' => esc_attr($total_score),                                        
                                     ); 
                                                                                                
                            if(!empty($input1)){
                                
                                $html .= "\n";
                                $html .= '<!-- Schema & Structured Data For Google Review v'.esc_attr(SASWP_VERSION).' - -->';
                                $html .= "\n";
                                $html .= '<script type="application/ld+json">'; 
                                $html .= "\n";       
                                $html .= saswp_json_print_format($input1);       
                                $html .= "\n";
                                $html .= '</script>';
                                $html .= "\n\n";
                                
                            }        
                                                        
                        }   
                        
                        return $html;              
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

