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
add_filter( 'default_hidden_columns', 'saswp_hide_review_text_columns', 10, 2 );
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
	        'name' 		            	=> esc_html__( 'Location', 'schema-and-structured-data-for-wp' ),	        
	        'add_new' 		            => esc_html__( 'Add Location', 'schema-and-structured-data-for-wp' ),
	        'add_new_item'  	        => esc_html__( 'Edit Location', 'schema-and-structured-data-for-wp' ),
            'edit_item'                 => esc_html__( 'Edit Location', 'schema-and-structured-data-for-wp' ),                
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
        
    if(saswp_current_user_allowed() ) {
        
        $cap = saswp_post_type_capabilities();

        if ( ! empty( $cap) ) {        
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
                'name' 			    => esc_html__( 'Reviews', 'schema-and-structured-data-for-wp' ),	        
                'add_new' 		    => esc_html__( 'Add Review', 'schema-and-structured-data-for-wp' ),
                'add_new_item'  	=> esc_html__( 'Edit Review', 'schema-and-structured-data-for-wp' ),
                'edit_item'         => esc_html__( 'Edit Review', 'schema-and-structured-data-for-wp' ),                
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
    
        if(saswp_current_user_allowed() ) {
            
            $cap = saswp_post_type_capabilities();

            if ( ! empty( $cap) ) {        
                $post_type['capabilities'] = $cap;         
            }
            
            register_post_type( 'saswp_reviews', $post_type );   
        }
                                            
}

function saswp_collection_custom_columns_set( $column, $post_id ) {
                
            switch ( $column ) {       
                
                case 'saswp_collection_shortcode' :
                                        
                    echo '[saswp-reviews-collection id="'. esc_attr( $post_id).'"]';
                    
                break;                 
                              
            }
}

function saswp_collection_custom_columns($columns) {    
    
    unset($columns['date']);
    
    $columns['saswp_collection_shortcode']       = '<a>'.esc_html__( 'Shortcode', 'schema-and-structured-data-for-wp' ).'<a>';
    
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
                       . '<a href="'. esc_url( $url).'">'
                       . '<span><img height="65" width="65" src="'. esc_url( $image_url).'" alt="Reviewer"></span>'
                       . '<span><strong>'.esc_html( $name).'</strong></span>'
                       . '</a>'
                       . '</div>';
                                                            
                    break;                 
                case 'saswp_review_rating' :
                 
                    $rating_val = get_post_meta( $post_id, $key='saswp_review_rating', true);                   
                    $rating_html_escaped = saswp_get_rating_html_by_value_column($rating_val,'');
                    //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped	 -- html is already fully escaped in function saswp_get_rating_html_by_value
                    echo $rating_html_escaped;
                    
                    break;
                case 'saswp_review_platform' :
                    
                    $platform = get_post_meta( $post_id, 'saswp_review_platform', true);  
                    $term     = get_term( $platform, 'platform' );
                    
                    if ( isset( $term->slug) ) {
                        
                        if($term->slug == 'self'){
                            
                             $service_object     = new SASWP_Output_Service();
                             $default_logo       = $service_object->saswp_get_publisher(true);                                                         
                             
                             if ( isset( $default_logo['url']) ) {
                            
                                 echo '<span class="saswp-g-plus"><img src="'. esc_url( $default_logo['url']).'" alt="Icon" /></span>';
                                 
                             }
                            
                        }else{
                            echo '<span class="saswp-g-plus"><img src="'. esc_url( SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/'.$term->slug.'-img.png').'" alt="Icon" /></span>';
                        }
                                                
                    }
                                                                                                                                                                                
                    break;

                case 'saswp_review_text' :
                
                    $string = get_post_meta( $post_id, $key='saswp_review_text', true);
                    
                    if ( ! empty( $string) ) {

                        $url    = admin_url( 'post.php?post='.$post_id.'&action=edit' );
                        $string = wp_strip_all_tags($string);

                        if (strlen($string) > 150) {

                            $stringCut = substr($string, 0, 150);
                            $endPoint  = strrpos($stringCut, ' ');
                            $string    = $endPoint? substr($stringCut, 0, $endPoint):substr($stringCut, 0);
                            echo esc_html( $string).'... <a style="cursor: pointer;" href="'. esc_url( $url).'" >'. esc_html__( 'Read More', 'schema-and-structured-data-for-wp' ) .'</a>';
                            
                        }else{
                            echo esc_html( $string);
                        }
                        
                    }
                                                                                                                                                                                                    
                    break;
                case 'saswp_review_date' :
                    
                    $date = get_post_meta( $post_id, $key='saswp_review_date', true);

                    if($date){                        
                        $date = gmdate('m-d-Y H:i:s', strtotime($date));
                        echo esc_attr( $date);
                    }
                                                                                                                                                                                                    
                    break;
                case 'saswp_review_place_id' :
                    
                    $name = get_post_meta( $post_id, 'saswp_review_location_id', true);
                    if(saswp_validate_url($name) ) {
                        echo '<a target="_blank" href="'. esc_url( $name).'">'.esc_html( $name).'</a>';
                    }else{
                        echo '<a target="_blank" href="'. esc_url( get_permalink($name)).'">'.esc_html( $name).'</a>';
                    }
                                                                                                                                                                                
                    break; 
                case 'saswp_review_shortcode' :
                                        
                    echo '[saswp-reviews id="'. esc_attr( $post_id).'"]';
                                                                                                                                                            
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
    $columns['saswp_reviewer_image']       = esc_html__( 'Image', 'schema-and-structured-data-for-wp' );
    $columns['title']                      = esc_html__( 'Title', 'schema-and-structured-data-for-wp' ); 
    $columns['saswp_review_text']          = esc_html__( 'Text', 'schema-and-structured-data-for-wp' );    
    $columns['saswp_review_rating']        = esc_html__( 'Rating', 'schema-and-structured-data-for-wp' );    
    $columns['saswp_review_platform']      = esc_html__( 'Platform', 'schema-and-structured-data-for-wp' );    
    $columns['saswp_review_date']          = esc_html__( 'Review Date', 'schema-and-structured-data-for-wp' ); 
    $columns['saswp_review_place_id']      = esc_html__( 'Place ID/Reviewed To', 'schema-and-structured-data-for-wp' );    
    $columns['saswp_review_shortcode']     = esc_html__( 'Shortcode', 'schema-and-structured-data-for-wp' );    
    
    return $columns;
}
function saswp_hide_review_text_columns( $hidden, $screen ) {
    
    if( isset( $screen->id ) && 'edit-saswp_reviews' === $screen->id ){      
        $hidden[] = 'saswp_review_text';     
    }   
    return $hidden;
}

function saswp_get_rating_html_by_value($rating_val,$stars_color="",$review_id=""){        
        $starating = '';
      
        $starating .= '<div class="saswp-rvw-str">';
        for($j=0; $j<5; $j++){  

              if($rating_val >$j){

                    $explod = explode('.', $rating_val);

                    if ( isset( $explod[1]) ) {

                        if($j <$explod[0]){
                           $a = wp_rand(1231,7879); 
                            $starating.='<span class="saswp_star_color"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="18px" viewBox="0 0 32 32"><defs><linearGradient id="grad'. esc_attr( $review_id).''. esc_attr( $a).'"><stop offset="100%" class="saswp_star" stop-color='.$stars_color.' /><stop offset="100%" stop-color="grey"/></linearGradient></defs><path fill="url(#grad'. esc_attr( $review_id).''. esc_attr( $a).')" d="M20.388,10.918L32,12.118l-8.735,7.749L25.914,31.4l-9.893-6.088L6.127,31.4l2.695-11.533L0,12.118 l11.547-1.2L16.026,0.6L20.388,10.918z"/></svg></span>';
                        }else{
                            $b = wp_rand(1231,7879);
                            $starating.='<span class="saswp_half_star_color"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="18px" viewBox="0 0 32 32"><defs><linearGradient id="grad'. esc_attr( $review_id).''. esc_attr( $b).'"><stop offset="50%" class="saswp_star" stop-color='.$stars_color.' /><stop offset="50%" stop-color="grey"/></linearGradient></defs><path fill="url(#grad'. esc_attr( $review_id).''. esc_attr( $b).')" d="M20.388,10.918L32,12.118l-8.735,7.749L25.914,31.4l-9.893-6.088L6.127,31.4l2.695-11.533L0,12.118 l11.547-1.2L16.026,0.6L20.388,10.918z"/></svg></span>';
                        }                                           
                    }else{
                        $c = wp_rand(1231,7879);
                        $starating.='<span class="saswp_star_color"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="18px" viewBox="0 0 32 32"><defs><linearGradient id="grad'. esc_attr( $review_id).''. esc_attr( $c).'"><stop offset="100%" class="saswp_star" stop-color='.$stars_color.' /><stop offset="100%" stop-color="grey"/></linearGradient></defs><path fill="url(#grad'. esc_attr( $review_id).''. esc_attr( $c).')" d="M20.388,10.918L32,12.118l-8.735,7.749L25.914,31.4l-9.893-6.088L6.127,31.4l2.695-11.533L0,12.118 l11.547-1.2L16.026,0.6L20.388,10.918z"/></svg></span>';
                    }
              } else{
                    $d = wp_rand(1231,7879);
                    $starating.='<span class="saswp_star_color_gray"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="18px" viewBox="0 0 32 32"><defs><linearGradient id="grad1'. esc_attr( $review_id).''. esc_attr( $d).'"><stop offset="100%" stop-color="grey" /><stop offset="100%" stop-color="grey"/></linearGradient></defs><path fill="url(#grad1'. esc_attr( $review_id).''. esc_attr( $d).')" d="M20.388,10.918L32,12.118l-8.735,7.749L25.914,31.4l-9.893-6.088L6.127,31.4l2.695-11.533L0,12.118 l11.547-1.2L16.026,0.6L20.388,10.918z"/></svg></span>';
                }                                                                                                                                
            }
        $starating .= '</div>';
        
        return $starating;
        
}

function saswp_get_rating_html_by_value_column($rating_val){
        $starating = '';
        
        $starating .= '<div class="saswp-rvw-str">';
        for($j=0; $j<5; $j++){  

              if($rating_val >$j){

                    $explod = explode('.', $rating_val);

                    if ( isset( $explod[1]) ) {

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
       
        if ( isset( $current_screen->post_type) ) {                  
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

function saswp_insert_platform_terms() {

    $term_ids = array();
    
    $platform_inserted = get_transient('saswp_platform_inserted');
    
    if( $platform_inserted != 105 ){
            
        $term_array = array(    
            'Self',
            'Agoda', 
            'Avvo', 
            'Angies List',
            'Apple AppStore',
            'Expedia',
            'Feefo', 
            'Facebook', 
            'Google',
            'Cusrev', 
            'Google Shopping', 
            'Goodreads',
            'TripAdvisor', 
            'Yelp', 
            'Zillow', 
            'Zomato',                        
            'Airbnb',
            'Airbnb Experiences', 
            'AliExpress', 
            'AlternativeTo', 
            'Amazon',
            'BBB',
            'bidvine',
            'BestBuy',
            'Booking.com',
            'Bark.com',
            'advieskeuze.nl', 
            'Capterra', 
            'CarGurus',
            'Cars.com', 
            'Citysearch', 
            'Classpass', 
            'Consumer Affairs', 
            'Clutch.co',
            'Clutch.com',
            'CreditKarma', 
            'CustomerLobby', 
            'DealerRater', 
            'Ebay', 
            'Edmunds', 
            'Etsy', 
            'Foursquare',
            'Flipkart',
            'Freelancer',
            'G2Crowd', 
            'Gearbest',
            'Gartner',
            'Glassdoor', 
            'Healthgrades', 
            'HomeAdvisor', 
            'Homestars', 
            'Houzz', 
            'Hotels.com',
            'Hipages', 
            'HungerStation',
            'Indeed',
            'IMDB',
            'Insider Pages', 
            'Jet',
            'Judge.me',
            'Lawyers.com', 
            'Lending Tree', 
            'Martindale',
            'mariages.net', 
            'Newegg', 
            'OpenRice', 
            'Opentable',
            'Oneflare', 
            'ProductHunt',
            'ProductReview',
            'Playstore',
            'Podcasts',
            'RateMDs', 
            'ReserveOut',
            'Rotten Tomatoes',
            'Sitejabber', 
            'Siftery', 
            'Steam',
            'StyleSeat',
            'SoftwareAdvice',
            'Shopify App Store',                     
            'Shopper Approved',
            'Serviceseeking',
            'solarquotes',
            'Talabat', 
            'The Knot', 
            'Thumbtack', 
            'Trulia', 
            'TrustedShops', 
            'Trustpilot', 
            'TrustRadius', 
            'Upwork',
            'Vitals', 
            'Walmart', 
            'WeddingWire',
            'Wish',
            'Yell', 
            'YellowPages', 
            'ZocDoc',
            'zankyou',
            'Abia.com',
            'WordofMouth',
            'Guaranteed',                     
            'Webwinkelkeur',                     
            'Dreams.co'                    
        );

        foreach( $term_array as $term){

            $term_id = term_exists( $term, 'platform' );                         
                
            if(!$term_id){

                $result = wp_insert_term(  $term, 'platform', array('slug' => $term) );

                if ( ! is_wp_error( $result) ) {
                    $term_ids[] = $result;
                }

            }else{
                
                if ( isset( $term_id['term_id']) ) {
                    $term_ids[] = $term_id['term_id'];
                }
                
            }

        }

        if( count($term_ids)  == 105 ){
            set_transient( 'saswp_platform_inserted', 105,  24*7*HOUR_IN_SECONDS ); 
        }

    }
        
}

function saswp_get_terms_as_array() {
    
    $terms_array = array();
    $terms       = get_terms( array('taxonomy' => 'platform', 'hide_empty' => false ) );  
    
    if($terms){
        foreach ( $terms as $val){
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
      // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information but only loading it inside the restrict_manage_posts hook.
      if( isset( $_GET['slug'] ) ) {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information but only loading it inside the restrict_manage_posts hook.
        $current_plugin = sanitize_text_field($_GET['slug']); // Check if option has been selected
      } ?>
      <select name="slug" id="slug">
        <option value="all" <?php selected( 'all', $current_plugin ); ?>><?php echo esc_html__( 'All', 'schema-and-structured-data-for-wp' ); ?></option>
        <?php foreach( $plugins as $key=>$value ) {
             ?>
          <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $current_plugin ); ?>><?php echo esc_html( $value ); ?></option>
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

   if ( ! $query->is_main_query() ) {
        return;
   }    
  global $pagenow;  
  $post_type = get_query_var('post_type');
  // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information but only loading it inside parse_query filter.
  $slug = isset($_GET['slug']) ? $_GET['slug'] : '';  
  if ( is_admin() && $pagenow == 'edit.php' && $post_type == 'saswp_reviews' && $slug !='all' ) {
      
    $query->query_vars['meta_key']     = 'saswp_review_platform';
    $query->query_vars['meta_value']   = sanitize_text_field($slug);
    $query->query_vars['meta_compare'] = '=';
    
  }
  
}

add_filter( 'parse_query', 'saswp_sort_reviews_by_platform' );

function saswp_reviews_form_shortcode_metabox($post){
    
    echo '<p>'.esc_html__( 'Use Below shortcode to show reviews form in your website. Using this you can collect reviews from your website directly.', 'schema-and-structured-data-for-wp' ) .'</p>';
    echo '<input type="text" value="[saswp-reviews-form]" readonly>';
}

function saswp_reviews_usage_metabox ($post) {

    echo '<p>'.esc_html__( 'Use these reviews to create a collection and use them to show on frontend.', 'schema-and-structured-data-for-wp' ) .'</p>';
    echo '<div><a href="'. esc_url(  admin_url("edit.php?post_type=saswp-collections") ).'">'.esc_html__( 'Add to collection', 'schema-and-structured-data-for-wp' ) .'</a></div>';

}