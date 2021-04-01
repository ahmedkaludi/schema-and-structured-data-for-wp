<?php
/**
 * Post Specific Class
 *
 * @author   Magazine3
 * @category Admin
 * @path     reviews/reviews_setup
 * @version 1.9
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'init', 'saswp_register_saswp_reviews',20); 
add_action( 'init', 'saswp_register_saswp_reviews_location',20); 

add_action( 'manage_saswp_reviews_posts_custom_column' , 'saswp_reviews_custom_columns_set', 10, 2 );
add_filter( 'manage_saswp_reviews_posts_columns', 'saswp_reviews_custom_columns' );
add_filter( 'manage_edit-saswp_reviews_sortable_columns', 'saswp_reviews_set_sortable_columns',10,2 );
add_action( 'pre_get_posts', 'saswp_sort_reviews_date_column_query' );

add_action( 'manage_saswp-collections_posts_custom_column' , 'saswp_collection_custom_columns_set', 10, 2 );
add_filter( 'manage_saswp-collections_posts_columns', 'saswp_collection_custom_columns' );

/**
 * Function to register reviews post type
 * since @version 1.9
 */
function saswp_register_saswp_reviews_location() {
                        
        $post_type = array(
	    'labels' => array(
	        'name' 		            	=> saswp_t_string( 'Location' ),	        
	        'add_new' 		            => saswp_t_string( 'Add Location' ),
	        'add_new_item'  	        => saswp_t_string( 'Edit Location' ),
            'edit_item'                 => saswp_t_string( 'Edit Location'),                
	    ),
      	'public' 		        => false,
      	'has_archive' 		    => false,
      	'exclude_from_search'	=> true,
    	'publicly_queryable'	=> false,
       // 'show_in_menu'        => 'edit.php?post_type=saswp',                
        'show_ui'               => false,
	    'show_in_nav_menus'     => false,			
        'show_admin_column'     => true,        
	    'rewrite'               => false,        
    );
        
    if(saswp_current_user_allowed()){
        
        $cap = saswp_post_type_capabilities();

        if(!empty($cap)){        
            $post_type['capabilities'] = $cap;         
        }
        
        register_post_type( 'saswp_rvs_location', $post_type );   
    }    
    
                                
}

/**
 * Function to register reviews post type
 * since @version 1.9
 */
function saswp_register_saswp_reviews() {
                        
        $post_type = array(
            'labels' => array(
                'name' 			    => saswp_t_string( 'Reviews' ),	        
                'add_new' 		    => saswp_t_string( 'Add Review' ),
                'add_new_item'  	=> saswp_t_string( 'Edit Review' ),
                'edit_item'         => saswp_t_string( 'Edit Review'),                
            ),
            'public' 		        => true,
            'has_archive' 		    => false,
            'exclude_from_search'	=> true,
            'show_in_admin_bar'     => false,
            'publicly_queryable'	=> false,
            'show_in_menu'          => 'edit.php?post_type=saswp',                
            'show_ui'               => true,
            'show_in_nav_menus'     => false,			
            'show_admin_column'     => true,        
            'rewrite'               => false
        );
    
        if(saswp_current_user_allowed()){
            
            $cap = saswp_post_type_capabilities();

            if(!empty($cap)){        
                $post_type['capabilities'] = $cap;         
            }
            
            register_post_type( 'saswp_reviews', $post_type );   
        }
                                            
}

function saswp_collection_custom_columns_set( $column, $post_id ) {
                
            switch ( $column ) {       
                
                case 'saswp_collection_shortcode' :
                                        
                    echo '[saswp-reviews-collection id="'.esc_attr($post_id).'"]';
                    
                break;                 
                              
            }
}

function saswp_collection_custom_columns($columns) {    
    
    unset($columns['date']);
    
    $columns['saswp_collection_shortcode']       = '<a>'.saswp_t_string( 'Shortcode' ).'<a>';
    
    return $columns;
    
}

function saswp_reviews_custom_columns_set( $column, $post_id ) {
                
            switch ( $column ) {       
                
                case 'saswp_reviewer_image' :
                    
                    $name = get_post_meta( $post_id, $key='saswp_reviewer_name', true);                      
                    
                    $image_url = get_post_meta( $post_id, $key='saswp_reviewer_image', true);
                    if(!$image_url){
                        $image_url = SASWP_PLUGIN_URL.'/admin_section/images/default_user.jpg';
                    }
                    $url = admin_url( 'post.php?post='.$post_id.'&action=edit' );
                    echo '<div class="saswp-rv-img">'
                       . '<a href="'.esc_url($url).'">'
                       . '<span><img height="65" width="65" src="'.esc_url($image_url).'" alt="Reviewer"></span>'
                       . '<span><strong>'.esc_attr($name).'</strong></span>'
                       . '</a>'
                       . '</div>';
                                                            
                    break;                 
                case 'saswp_review_rating' :
                    
                    $rating_val = get_post_meta( $post_id, $key='saswp_review_rating', true);                   
                    echo saswp_get_rating_html_by_value($rating_val);                                                                                                                                       
                    
                    break;
                case 'saswp_review_platform' :
                    
                    $platform = get_post_meta( $post_id, $key='saswp_review_platform', true);  
                    $term     = get_term( $platform, 'platform' );
                    
                    if(isset($term->slug)){
                        
                        if($term->slug == 'self'){
                            
                             $service_object     = new saswp_output_service();
                             $default_logo       = $service_object->saswp_get_publisher(true);                                                         
                             
                             if(isset($default_logo['url'])){
                            
                                 echo '<span class="saswp-g-plus"><img src="'.esc_url($default_logo['url']).'" alt="Icon" /></span>';
                                 
                             }
                            
                        }else{
                            echo '<span class="saswp-g-plus"><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/'.esc_attr($term->slug).'-img.png'.'" alt="Icon" /></span>';
                        }
                                                
                    }
                                                                                                                                                                                
                    break;
                case 'saswp_review_date' :
                    
                    $name = get_post_meta( $post_id, $key='saswp_review_date', true);
                    echo esc_attr($name);
                                                                                                                                                            
                    break;
                case 'saswp_review_place_id' :
                    
                    $name = get_post_meta( $post_id, $key='saswp_review_location_id', true);
                    echo '<a target="_blank" href="'.esc_url(get_permalink($name)).'">'.esc_attr($name).'</a>';
                                                                                                                                                            
                    break; 
                case 'saswp_review_shortcode' :
                                        
                    echo '[saswp-reviews id="'. esc_attr($post_id).'"]';
                                                                                                                                                            
                    break; 
               
            }
}
function saswp_sort_reviews_date_column_query( $query ) {

    if ( ! is_admin() )
    return;

    $orderby = $query->get( 'orderby');

    if ( 'saswp_review_date' == $orderby ) {
        $query->set( 'meta_key', 'saswp_review_date' );
        $query->set( 'orderby', 'meta_value_num' );
    }
    if ( 'saswp_review_rating' == $orderby ) {
        $query->set( 'meta_key', 'saswp_review_rating' );
        $query->set( 'orderby', 'meta_value_num' );
    }

}

function saswp_reviews_set_sortable_columns( $columns ){

    $columns['saswp_review_date']  = 'saswp_review_date';
    $columns['saswp_review_rating'] = 'saswp_review_rating';
    return $columns;
}

function saswp_reviews_custom_columns($columns) {    
    
    unset($columns);
    
    $columns['cb']                         = '<input type="checkbox" />';
    $columns['saswp_reviewer_image']       = saswp_t_string( 'Image' );
    $columns['title']                      = saswp_t_string( 'Title' );    
    $columns['saswp_review_rating']        = saswp_t_string( 'Rating' );    
    $columns['saswp_review_platform']      = saswp_t_string( 'Platform' );    
    $columns['saswp_review_date']          = saswp_t_string( 'Review Date' ); 
    $columns['saswp_review_place_id']      = saswp_t_string( 'Place ID/Reviewed To' );    
    $columns['saswp_review_shortcode']     = saswp_t_string( 'Shortcode' );    
    
    return $columns;
}

function saswp_get_rating_html_by_value($rating_val){
            
        $starating = '';
        
        $starating .= '<div class="saswp-rvw-str">';
        for($j=0; $j<5; $j++){  

              if($rating_val >$j){

                    $explod = explode('.', $rating_val);

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
        
        return $starating;
        
}

/**
 * Enqueue CSS and JS
 */
function saswp_enqueue_rateyo_script( $hook ) { 
    
            
        $post_type = '';
        
        $current_screen = get_current_screen(); 
       
        if(isset($current_screen->post_type)){                  
            $post_type = $current_screen->post_type;                
        }  
                
        if($post_type =='saswp_reviews'){
            
            $rating_val= 0;
            $rv_rating = get_post_meta( get_the_ID(), $key='saswp_review_rating', true);
            if($rv_rating){
                $rating_val = $rv_rating;
            }
                                                
            $data = array(                                    
                'rating_val'                      => $rating_val, 
                'readonly'                        => false, 
            );

            $data = apply_filters('saswp_reviews_filter',$data,'saswp_reviews_data');

            wp_register_script( 'saswp-rateyo-js', SASWP_PLUGIN_URL . 'admin_section/js/jquery.rateyo.min.js', array('jquery', 'jquery-ui-core'), SASWP_VERSION , true );                                        
            wp_localize_script( 'saswp-rateyo-js', 'saswp_reviews_data', $data );
            wp_enqueue_script( 'saswp-rateyo-js' );

            wp_enqueue_style( 'saswp-rateyo-css', SASWP_PLUGIN_URL . 'admin_section/css/jquery.rateyo.min.css', false , SASWP_VERSION );
        
        }
                    
}
add_action( 'admin_enqueue_scripts', 'saswp_enqueue_rateyo_script' );

add_action( 'init', 'saswp_create_platform_custom_taxonomy', 21 );
 
function saswp_create_platform_custom_taxonomy() {
 
  $labels = array(
    'name'          => _x( 'Platforms', 'taxonomy general name' ),
    'singular_name' => _x( 'Platform', 'taxonomy singular name' ),
    'search_items'  => __( 'Search Types' ),
    'all_items'     => __( 'All Platform' ),        
    'edit_item'     => __( 'Edit Platform' ), 
    'update_item'   => __( 'Update Platform' ),
    'add_new_item'  => __( 'Add New Platform' ),
    'new_item_name' => __( 'New Platform Name' ),
    'menu_name'     => __( 'Platforms' ),
  ); 	
 
  register_taxonomy(
          
    'platform',
    array('saswp'), 
    array(
        'hierarchical'      => false,
        'labels'            => $labels,
        'public'            => false,   
        'show_ui'           => false,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'platform' ),
  )
  );
    
  add_action('admin_init', 'saswp_insert_platform_terms');
  
}

function saswp_insert_platform_terms(){

    $term_ids = array();

    $platform_inserted = get_transient('saswp_platform_inserted');
    
    if($platform_inserted != 81){
        
        $term_array = array(    
            'Self',
            'Agoda', 
            'Avvo', 
            'Angies List',
            'Apple AppStore',
            'Expedia', 
            'Facebook', 
            'Google', 
            'Goodreads',
            'TripAdvisor', 
            'Yelp', 
            'Zillow', 
            'Zomato',                        
            'Airbnb', 
            'AliExpress', 
            'AlternativeTo', 
            'Amazon',
            'BBB',
            'BestBuy',
            'Booking.com', 
            'Capterra', 
            'CarGurus',
            'Cars.com', 
            'Citysearch', 
            'Classpass', 
            'Consumer Affairs', 
            'Clutch',
            'CreditKarma', 
            'CustomerLobby', 
            'DealerRater', 
            'Ebay', 
            'Edmunds', 
            'Etsy', 
            'Foursquare',
            'Flipkart',
            'G2Crowd', 
            'Gearbest',
            'Gartner',
            'Glassdoor', 
            'Healthgrades', 
            'HomeAdvisor', 
            'Homestars', 
            'Houzz', 
            'Hotels.com', 
            'HungerStation',
            'Indeed',
            'IMDB',
            'Insider Pages', 
            'Jet',
            'Judge.me',
            'Lawyers.com', 
            'Lending Tree', 
            'Martindale', 
            'Newegg', 
            'OpenRice', 
            'Opentable', 
            'ProductHunt',
            'Playstore',
            'RateMDs', 
            'ReserveOut',
            'Rotten Tomatoes',
            'Sitejabber', 
            'Siftery', 
            'Steam',
            'SoftwareAdvice',
            'Shopify App Store',                     
            'Shopper Approved',
            'Talabat', 
            'The Knot', 
            'Thumbtack', 
            'Trulia', 
            'TrustedShops', 
            'Trustpilot', 
            'TrustRadius', 
            'Vitals', 
            'Walmart', 
            'WeddingWire',
            'Wish',
            'Yell', 
            'YellowPages', 
            'ZocDoc'                     
        );

        foreach($term_array as $term){

            $term_id = term_exists( $term, 'platform' );                         
                
            if(!$term_id){

                $result = wp_insert_term(  $term, 'platform', array('slug' => $term) );

                if(!is_wp_error($result)){
                    $term_ids[] = $result;
                }

            }else{
                
                if(isset($term_id['term_id'])){
                    $term_ids[] = $term_id['term_id'];
                }
                
            }

        }

        if(count($term_ids)  == 81){
            set_transient('saswp_platform_inserted', 81,  24*7*HOUR_IN_SECONDS ); 
        }

    }
        
}

function saswp_get_terms_as_array(){
    
    $terms_array = array();
    $terms       = get_terms( 'platform', array( 'hide_empty' => false ) );  
    
    if($terms){
        foreach ($terms as $val){
            $terms_array[$val->term_id] = $val->name;
            
        }
    }
    
    return $terms_array;
    
}


/**
 * Filter slugs
 * @global type $typenow
 * @global type $wp_query
 */
function saswp_reviews_filter() {
    
  global $typenow;
  global $wp_query;
    if ( $typenow == 'saswp_reviews' ) { // Your custom post type slug
      $plugins = saswp_get_terms_as_array();
      $current_plugin = '';
      if( isset( $_GET['slug'] ) ) {
        $current_plugin = esc_attr($_GET['slug']); // Check if option has been selected
      } ?>
      <select name="slug" id="slug">
        <option value="all" <?php selected( 'all', $current_plugin ); ?>><?php esc_html_e( 'All' ); ?></option>
        <?php foreach( $plugins as $key=>$value ) { ?>
          <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $current_plugin ); ?>><?php echo esc_attr( $value ); ?></option>
        <?php } ?>
      </select>
  <?php }
}

add_action( 'restrict_manage_posts', 'saswp_reviews_filter' );


/**
 * Function to add display type filter in ads list dashboard
 * @global type $pagenow
 * @param type $query
 */
function saswp_sort_reviews_by_platform( $query ) {
    
  global $pagenow;
  // Get the post type
  $post_type = isset( $_GET['post_type'] ) ? $_GET['post_type'] : '';
  
  if ( is_admin() && $pagenow == 'edit.php' && $post_type == 'saswp_reviews' && isset( $_GET['slug'] ) && $_GET['slug'] !='all' ) {
      
    $query->query_vars['meta_key']     = 'saswp_review_platform';
    $query->query_vars['meta_value']   = esc_attr($_GET['slug']);
    $query->query_vars['meta_compare'] = '=';
    
  }
  
}

add_filter( 'parse_query', 'saswp_sort_reviews_by_platform' );

function saswp_reviews_form_shortcode_metabox($post){
    
    echo '<p>'.saswp_t_string( 'Use Below shortcode to show reviews form in your website. Using this you can collect reviews from your website directly.').'</p>';
    echo '<input type="text" value="[saswp-reviews-form]" readonly>';
}

function saswp_reviews_usage_metabox ($post) {

    echo '<p>'.saswp_t_string( 'Use these reviews to create a collection and use them to show on frontend.').'</p>';
    echo '<div><a href="'.esc_url( admin_url("edit.php?post_type=saswp-collections") ).'">'.saswp_t_string( 'Add to collection').'</a></div>';

}