<?php
/**
 * Google Review Setup Page
 *
 * @author   Magazine3
 * @category Admin
 * @path     google_review/google_review_setup
 * @Version 1.8
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

add_action('admin_init', 'saswp_create_database_for_existing_users');
add_action('the_post', 'saswp_create_database_for_existing_users');

/**
 * Function to initiate database installation
 * @return type null
 * @Since version 1.8
 */
function saswp_create_database_for_existing_users(){
        
                if ( ! current_user_can( 'manage_options' ) ) {
                    return;
                }
		$status = get_option('saswp-database-on-first-load');
        
		if($status != 'enable'){
                    
			saswp_google_review_database_install();                        
			update_option('saswp-database-on-first-load', 'enable');			
                        
		}                                                                  
 		   
}
/**
 * Function to install database tables for google review
 * @global type $wpdb
 * @Since version 1.8
 */
function saswp_google_review_database_install() {
    
	global $wpdb;                

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	
	$charset_collate = $engine = '';	
	

	if(!empty($wpdb->charset)) {
		$charset_collate .= " DEFAULT CHARACTER SET {$wpdb->charset}";
	} 
	if($wpdb->has_cap('collation') AND !empty($wpdb->collate)) {
		$charset_collate .= " COLLATE {$wpdb->collate}";
	}

	$found_engine = $wpdb->get_var("SELECT ENGINE FROM `information_schema`.`TABLES` WHERE `TABLE_SCHEMA` = '".DB_NAME."' AND `TABLE_NAME` = '{$wpdb->prefix}posts';");
        
	if(strtolower($found_engine) == 'innodb') {
		$engine = ' ENGINE=InnoDB';
	}

	$found_tables = $wpdb->get_col("SHOW TABLES LIKE '{$wpdb->prefix}saswp%';");	        
        
	if(!in_array("{$wpdb->prefix}saswp_google_place", $found_tables)) {
            
		dbDelta("CREATE TABLE `{$wpdb->prefix}saswp_google_place` (".
		"id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,".
                "place_id VARCHAR(80) NOT NULL,".
                "name VARCHAR(255) NOT NULL,".
                "photo VARCHAR(425),".
                "icon VARCHAR(255),".
                "address VARCHAR(255),".
                "rating DOUBLE PRECISION,".
                "url VARCHAR(255),".
                "website VARCHAR(255),".
                "updated BIGINT(20),".
                "PRIMARY KEY (`id`),".
                "UNIQUE INDEX saswp_place_id (`place_id`)".                        
		") ".$charset_collate.$engine.";");
                
	}
        
        if(!in_array("{$wpdb->prefix}saswp_google_review", $found_tables)) {
            
		dbDelta("CREATE TABLE `{$wpdb->prefix}saswp_google_review` (".
		"id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,".
                "google_place_id BIGINT(20) UNSIGNED NOT NULL,".
                "hash VARCHAR(40) NOT NULL,".
                "rating INTEGER NOT NULL,".
                "text VARCHAR(10000),".
                "time INTEGER NOT NULL,".
                "language VARCHAR(10),".
                "author_name VARCHAR(255),".
                "author_url VARCHAR(255),".
                "profile_photo_url VARCHAR(255),".
                "PRIMARY KEY (`id`),".
                "UNIQUE INDEX saswp_google_review_hash (`hash`),".
                "INDEX saswp_google_place_id (`google_place_id`)".                       
		") ".$charset_collate.$engine.";");
                
	}

}
/**
 * Function to retrive google review data from google with maps api
 * @param type $place_id
 * @param string $language
 * @return type array
 * @Since version 1.8 
 */
function saswp_get_google_review_data($place_id, $language=null){
    
    if($language){
        
        $language = '&language='.$language;
        
    }
        
    if($place_id){
            
        $result = wp_remote_get('https://maps.googleapis.com/maps/api/place/details/json?placeid='.$place_id.'&key=AIzaSyAQ1j_iD1npoqTRuhrIx-ADeVZjQddUqKs'.$language);        
        
        if($result){

           $result = json_decode($result['body']);           
           $result->result->business_photo = saswp_business_image($result->result);           
           $response = saswp_save_google_reviews($result->result);
           return $response;
        }        
                        
    }
                
}
/**
 * Function to save retrived google review data from google place 
 * @global type $wpdb
 * @param type $place
 * @return type string
 * @Since version 1.8
 */
function saswp_save_google_reviews($place) {
       
    global $wpdb;
    $response = null;
    
    $google_place_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM " . $wpdb->prefix . "saswp_google_place WHERE place_id = %s", $place->place_id));
    
    if ($google_place_id) {
        
       $response =  $wpdb->update($wpdb->prefix . 'saswp_google_place', array(
            'name'     => $place->name,
            'photo'    => $place->business_photo,
            'rating'   => $place->rating
        ), array('ID'  => $google_place_id));
        
    } else {
        
       $response = $wpdb->insert($wpdb->prefix . 'saswp_google_place', array(
            'place_id' => $place->place_id,
            'name'     => $place->name,
            'photo'    => $place->business_photo,
            'icon'     => $place->icon,
            'address'  => $place->formatted_address,
            'rating'   => isset($place->rating) ? $place->rating : null,
            'url'      => isset($place->url) ? $place->url : null,
            'website'  => isset($place->website) ? $place->website : null
        ));
        
        $google_place_id = $wpdb->insert_id;
        
    }

    if ($place->reviews) {
        
        $reviews = $place->reviews;
         
        foreach ($reviews as $review) {
            
            $google_review_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM " . $wpdb->prefix . "saswp_google_review WHERE time = %s", $review->time));
            
            if ($google_review_id) {
                
                $update_params = array(
                    'rating' => $review->rating,
                    'text'   => $review->text
                );
                
                if (isset($review->profile_photo_url)) {
                    
                    $update_params['profile_photo_url'] = $review->profile_photo_url;
                    
                }
                
            $response =   $wpdb->update($wpdb->prefix . 'saswp_google_review', $update_params, array('id' => $google_review_id));
                
            } else {
                
            $response  = $wpdb->insert($wpdb->prefix . 'saswp_google_review', array(
                            'google_place_id'   => $google_place_id,
                            'hash'              => $review->time, 
                            'rating'            => $review->rating,
                            'text'              => $review->text,
                            'time'              => $review->time,
                            'language'          => $review->language,
                            'author_name'       => $review->author_name,
                            'author_url'        => isset($review->author_url) ? $review->author_url : null,
                            'profile_photo_url' => isset($review->profile_photo_url) ? $review->profile_photo_url : null
                ));
                
            }
        }
    }
    
    return $response;
}
/**
 * Function to retrive image from google place 
 * @global type $sd_data
 * @param type $result_json
 * @return type string
 * @Since version 1.8
 */
function saswp_business_image($result_json) {
    
    global $sd_data;
        
    if (isset($result_json->photos)) {
        
        $request_url = add_query_arg(
            array(
                'photoreference' => $result_json->photos[0]->photo_reference,
                'key'            => isset($sd_data['google_place_api_key']) ? $sd_data['google_place_api_key']:'',
                'maxwidth'       => '300',
                'maxheight'      => '300',
            ),
            'https://maps.googleapis.com/maps/api/place/photo'
        );
       
       return $request_url;
                        
    }
    return null;
}
