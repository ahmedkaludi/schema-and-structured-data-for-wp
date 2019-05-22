<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if(!function_exists('wp_get_current_user')) {
    include(ABSPATH . "wp-includes/pluggable.php"); 
}
       
        if(isset($_POST['sasw_post_ID'])){
                      
            $post_id    = sanitize_text_field($_POST['sasw_post_ID']);
            $post_title = sanitize_text_field($_POST['saswp_g_review_title']);
            
            $post = array(                 
                    'ID'                    => $post_id,
                    'post_title'            => $post_title,                    
                    'post_status'           => 'publish',
                    'post_name'             => $post_title,                                        
                    'post_type'             => 'saswp-google-review',                                                            
                );
                                          
            wp_update_post($post);
                                        
            $post_meta = array();
            
            $post_meta['saswp_google_place_id'] = sanitize_text_field($_POST['saswp_google_place_id']);
            $post_meta['saswp_language_list']   = sanitize_text_field($_POST['saswp_language_list']);
            $post_meta['saswp_googel_api']      = sanitize_text_field($_POST['saswp_googel_api']);
            
            if(!empty($post_meta)){
                
                foreach($post_meta as $meta_key => $meta_val){
                    
                    update_post_meta($post_id, $meta_key, $meta_val); 
                    
                }
                
            }
                                    
            if($_POST['saswp-page'] == 'collection'){
                
                $current_url = htmlspecialchars_decode(wp_nonce_url(admin_url('admin.php?post_id='.$post_id.'&page=collection'), '_wpnonce'));           
                wp_redirect( $current_url );
                exit;
            }
            
        }

class saswp_google_review_page{
        
    public function __construct() {
            
        add_action( 'admin_menu', array($this, 'saswp_add_google_review_links'),20);
        add_action( 'wp_ajax_saswp_connect_google_place', array($this,'saswp_connect_google_place'));
                
    }
    
    public function saswp_connect_google_place(){
        
                if ( ! isset( $_POST['saswp_security_nonce'] ) ){
                    return; 
                }
                if ( !wp_verify_nonce( $_POST['saswp_security_nonce'], 'saswp_ajax_check_nonce' ) ){
                   return;  
                }
                
                $place_id   = '';
                $language   = '';
                $google_api = '';
                
                if(isset($_POST['place_id'])){
                    $place_id = sanitize_text_field($_POST['place_id']);
                }
                if(isset($_POST['language'])){
                    $language = sanitize_text_field($_POST['language']);
                }
                if(isset($_POST['google_api'])){
                    $google_api = sanitize_text_field($_POST['google_api']);
                }
                
                if($place_id){
                    
                  $result = saswp_get_google_review_data($place_id, $language);
                  
                  if($result){
                      
                      echo json_encode(array('status' => 't'));
                      
                  }else{
                      
                      echo json_encode(array('status' => 'f'));
                      
                  }
                    
                }
                
                wp_die();
                
    }
    
    public function saswp_add_google_review_links() {	
	                                                 
             add_submenu_page( 'edit.php?post_type=saswp',
                esc_html__( 'Structured Data', 'schema-and-structured-data-for-wp' ),
                esc_html__( '', 'schema-and-structured-data-for-wp' ),
                'manage_options',
                'collection',
                array($this, 'saswp_admin_google_review_interface_render'));             
                
    }
    
    public function saswp_admin_google_review_interface_render(){
        
         global $wpdb;
        
         $language = array(
                    'af' => 'Afrikanns',
                    'sq' => 'Albanian',
                    'ar' => 'Arabic',
                    'hy' => 'Armenian',
                    'eu' => 'Basque',
                    'bn' => 'Bengali',
                    'bg' => 'Bulgarian',
                    'ca' => 'Catalan',
                    'km' => 'Cambodian',
                    'zh' => 'Chinese (Mandarin)',
                    'hr' => 'Croation',
                    'cs' => 'Czech',
                    'da' => 'Danish',
                    'nl' => 'Dutch',
                    'en' => 'English',
                    'et' => 'Estonian',
                    'fj' => 'Fiji',
                    'fi' => 'Finnish',
                    'fr' => 'French',
                    'ka' => 'Georgian',
                    'de' => 'German',
                    'el' => 'Greek',
                    'gu' => 'Gujarati',
                    'he' => 'Hebrew',
                    'hi' => 'Hindi',
                    'hu' => 'Hungarian',
                    'is' => 'Icelandic',
                    'id' => 'Indonesian',
                    'ga' => 'Irish',
                    'it' => 'Italian',
                    'ja' => 'Japanese',
                    'jw' => 'Javanese',
                    'ko' => 'Korean',
                    'la' => 'Latin',
                    'lv' => 'Latvian',
                    'lt' => 'Lithuanian',
                    'mk' => 'Macedonian',
                    'ms' => 'Malay',
                    'ml' => 'Malayalam',
                    'mt' => 'Maltese',
                    'mi' => 'Maori',
                    'mr' => 'Marathi',
                    'mn' => 'Mongolian',
                    'ne' => 'Nepali',
                    'no' => 'Norwegian',
                    'fa' => 'Persian',
                    'pl' => 'Polish',
                    'pt' => 'Portuguese',
                    'pa' => 'Punjabi',
                    'qu' => 'Quechua',
                    'ro' => 'Romanian',
                    'ru' => 'Russian',
                    'sm' => 'Samoan',
                    'sr' => 'Serbian',
                    'sk' => 'Slovak',
                    'sl' => 'Slovenian',
                    'es' => 'Spanish',
                    'sw' => 'Swahili',
                    'sv' => 'Swedish ',
                    'ta' => 'Tamil',
                    'tt' => 'Tatar',
                    'te' => 'Telugu',
                    'th' => 'Thai',
                    'bo' => 'Tibetan',
                    'to' => 'Tonga',
                    'tr' => 'Turkish',
                    'uk' => 'Ukranian',
                    'ur' => 'Urdu',
                    'uz' => 'Uzbek',
                    'vi' => 'Vietnamese',
                    'cy' => 'Welsh',
                    'xh' => 'Xhosa'
            );
        
        $post_meta = array();
        $post_id   = '';
        $reviews   = null;
        
        if(isset($_GET['post_id'])){
            
            $post_id = $_GET['post_id'];
            
            $post_meta = get_post_meta($post_id, $key='', true );            
            
            
        } else{
            
            $post    = get_default_post_to_edit( 'saswp-google-review', true );
            $post_id = $post->ID;
        }
        
        if(isset($post_meta['saswp_google_place_id'])){
            
            $place_id = trim($post_meta['saswp_google_place_id'][0]);
            $place    = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "saswp_google_place WHERE place_id = %s", $place_id));
            
            if(is_object($place)){
                
                $reviews = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "saswp_google_review WHERE google_place_id = %d ORDER BY time DESC", $place->id));               
                
            }
                        
        }        
                                        
        ?>
        <div class="saswp-heading">
            <h1 class="wp-heading-inline"><?php echo esc_html__('Google Review','schema-and-structured-data-for-wp'); ?>  </h1><span class="saswp-need-help"><a target="_blank" href="http://structured-data-for-wp.com/docs/article/how-to-display-google-review/"><?php echo esc_html__('Need Help?','schema-and-structured-data-for-wp'); ?></a></span>
        </div>

            <div class="saswp-g-review-container">
                 <form method="post" action="post.php">
                    <div class="saswp-g-review-header">

                            <input type="hidden" name="post_type" value="saswp-google-review">
                            <input type="hidden" name="saswp-page" value="collection">
                            <input type="hidden" id="sasw_post_ID" name="sasw_post_ID" value="<?php echo $post_id; ?>">
                            <input type="text" value="<?php if(get_the_title($post_id) == 'Auto Draft'){ echo 'Untitled'; }else{ echo get_the_title($post_id); } ?>" id="saswp_g_review_title" name="saswp_g_review_title" style="width: 30%;">

                            <button type="submit" class="btn btn-success button-primary" > <?php echo esc_html__('Save','schema-and-structured-data-for-wp'); ?>  </button>
                            <div>Use ShortCode [saswp_google_review id="<?php echo $post_id; ?>"]</div>

                    </div>
                
                <div class="saswp-g-review-body">
                    
                    <div class="saswp-review-list">
                        
                        
                       
                        <?php 
                
                        if($reviews){
                            
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
                                
                                                                                                                             
                                echo '<div class="saswp-g-review-panel">
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
                            
                        }                        
                        
                        ?>
                                                                                                                        
                    </div>
                    
                    <div class="saswp-channel-list">                                                                             
                        <div class="saswp-panel">
                            <h3><?php echo esc_html__('Google Review Settings','schema-and-structured-data-for-wp'); ?></h3>
                            <div class="saswp-input-fields">
                                <label><?php echo esc_html__('Place ID','schema-and-structured-data-for-wp'); ?>:</label><input value="<?php if(isset($post_meta['saswp_google_place_id'])){ echo $post_meta['saswp_google_place_id'][0];}  ?>" type="text" id="saswp_google_place_id" name="saswp_google_place_id" placeholder="<?php echo esc_html__('Place Id', 'schema-and-structured-data-for-wp' ); ?>">   
                            </div>

                            <div class="saswp-input-fields">
                                <label><?php echo esc_html__('Languages','schema-and-structured-data-for-wp'); ?>:</label>
                                <select name="saswp_language_list" id="saswp_language_list">
                                         <?php  
                                            
                                            foreach ($language as $key => $value) {
                                                
                                              $sel = '';
                                              
                                              if(saswp_remove_warnings($post_meta, 'saswp_language_list', 'saswp_array')==$key){
                                                  
                                                $sel = 'selected';
                                                
                                              }
                                              
                                              echo "<option value='".esc_attr($key)."' ".esc_attr($sel).">".esc_html__($value, 'schema-and-structured-data-for-wp' )."</option>";
                                              
                                            }
                                                ?>
                                </select>

                            </div>

                            <div class="saswp-input-fields">
                                <label><?php echo esc_html__('Goolge API','schema-and-structured-data-for-wp'); ?></label>
                                <input value="<?php if(isset($post_meta['saswp_googel_api'])){ echo $post_meta['saswp_googel_api'][0];}  ?>" type="text" id="saswp_googel_api" name="saswp_googel_api" placeholder="<?php echo esc_html__('Google API', 'schema-and-structured-data-for-wp' ); ?>">   
                            </div>
                            
                            <div class="saswp-input-fields">
                                <a class="saswp_coonect_google_place btn btn-success button-primary" ><?php echo esc_html__('Connect Google','schema-and-structured-data-for-wp'); ?></a>
                            </div>

                        </div>                                                                                                
                                                
                    </div>
                    
                                                           
                </div>

                </form>                
            </div>
            
        <?php
    }    
                
}

if (class_exists('saswp_google_review_page')) {
	new saswp_google_review_page;
};

