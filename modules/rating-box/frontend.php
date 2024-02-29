<?php 
/**
 * Rating Output Class
 *
 * @author   Magazine3
 * @category Frontend
 * @path  output/review-output
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

Class saswp_rating_box_frontend{
        /**
         * All the hooks list which are used in rating module
         */            
        public function saswp_review_hooks(){
            
            add_filter('the_content', array($this, 'saswp_display_review_box'));             
            add_action('wp_head', array($this, 'saswp_display_review_box_schema')); 
            add_action('amp_post_template_head', array($this, 'saswp_display_review_box_schema')); 
            add_action('wp_enqueue_scripts', array($this, 'saswp_rating_box_custom_style')); 
            add_action('wp_footer', array($this, 'saswp_rating_box_custom_script')); 
                                    
        }
        /**
         * Display the review box via shortcode
         * @param type $attr
         * @return type string
         */
        public function saswp_review_display_via_shortcode($attr){
            
            $review_id = '';
            if(isset($attr['id'])){
                $review_id = $attr['id'];
            }
            
            if($review_id > 0){
                
              $result = $this->saswp_get_review_box_content();  
              return $result;
              
            }
            
        }
        /**
         * Generate and add the schema markup for review box
         * @global type $sd_data
         * echo string
         */
        public function saswp_display_review_box_schema(){
                          
                        global $sd_data;
            
                        if(saswp_global_option() && isset($sd_data['saswp-review-module']) && $sd_data['saswp-review-module'] == 1){
                          
                            $saswp_review_details           = get_post_meta(get_the_ID(), 'saswp_review_details', true);    
                                                                        
                        if(isset($saswp_review_details['saswp-review-item-enable'])){
                          
                        $author_id      = get_the_author_meta('ID');
			$author_details = array();
                        
                        if(function_exists('get_avatar_data')){
                            $author_details	= get_avatar_data($author_id);
                        }
			
                        
			$date 		= get_the_date("c");
			$modified_date 	= get_the_modified_date("c");
			$aurthor_name 	= get_the_author();
                        
                        if(!$aurthor_name){
				
                        $author_id    = get_post_field ('post_author', get_the_ID());
		        $aurthor_name = get_the_author_meta( 'display_name' , $author_id ); 
                        
			}
                        
                        $overall_rating = null; 
                        if(isset($saswp_review_details['saswp-review-item-over-all'])){
                            $overall_rating   = $saswp_review_details['saswp-review-item-over-all'];
                        }
                                                                        
                        if($overall_rating){
                         
                            $total_score = esc_attr(number_format((float)$overall_rating, 2, '.', ''));
                            
                            $input1 = array(
                                    '@context'       => saswp_context_url(),
                                    '@type'          => 'Review',
                                    'dateCreated'    => esc_html($date),
                                    'datePublished'  => esc_html($date),
                                    'dateModified'   => esc_html($modified_date),
                                    'headline'       => saswp_get_the_title(),
                                    'name'           => saswp_get_the_title(),                                    
                                    'url'            => get_permalink(),
                                    'description'    => saswp_get_the_excerpt(),
                                    'copyrightYear'  => get_the_modified_date('Y'),                                                                                                           
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
                                            '@type' => 'Organization',
                                            'name'  => saswp_get_the_title(),
                                    );

                                    $input1['reviewRating'] = array(
                                        '@type'       => 'Rating',
                                        'worstRating' => 1,
                                        'bestRating'  => 5,
                                        'ratingValue' => esc_attr($total_score),
                                        'description' => strip_tags(get_post_meta( get_the_ID(), 'saswp-review-item-description', true )),
                                     ); 
                            
                            if(!empty($input1)){
                                if(isset($sd_data['saswp_remove_version_tag']) && $sd_data['saswp_remove_version_tag'] != 1){
                                    echo '<!-- Schema & Structured Data For WP Rating Module v'.esc_attr(SASWP_VERSION).' - -->';
                                }
                                echo "\n";
                                echo '<script type="application/ld+json" class="saswp-schema-markup-rating-module-output">'; 
                                echo "\n";       
                                echo saswp_json_print_format($input1);       
                                echo "\n";
                                echo '</script>';
                                echo "\n\n";
                                
                            }        
                            
                            
                        }
                             
                             
                      } 
                                                                    
                        }            
            
        }
        /**
         * Generate the review box html with its dynamic data
         * @return string
         */
        public function saswp_get_review_box_content(){
            
            $saswp_review_details           = array();                      
            $saswp_review_item_feature      = array();
            $saswp_review_item_star_rating  = array();
                                    
            $saswp_review_details           = get_post_meta(get_the_ID(), 'saswp_review_details', true);             
            $saswp_review_description       = get_post_meta( get_the_ID(), 'saswp-review-item-description', true );
            $saswp_review_props             = get_post_meta( get_the_ID(), 'saswp-review-item-props', true );
            $saswp_review_cons              = get_post_meta( get_the_ID(), 'saswp-review-item-cons', true );
            $saswp_over_all_rating          = 0;
            
            if(isset($saswp_review_details['saswp-review-item-feature'])){
                $saswp_review_item_feature = $saswp_review_details['saswp-review-item-feature'];    
            }
            if(isset($saswp_review_details['saswp-review-item-star-rating'])){
                $saswp_review_item_star_rating = $saswp_review_details['saswp-review-item-star-rating'];    
            }                                   
            if(isset($saswp_review_details['saswp-review-item-over-all'])){
                $saswp_over_all_rating = (float)$saswp_review_details['saswp-review-item-over-all'];    
            }    
            
            $boxdata ='';
            
            if($saswp_review_props != '' || $saswp_review_cons != '' ){
                
             $boxdata .='
                <div class="saswp-pc-wrap">
                    <div class="saswp-lst">
                        <span>'.saswp_t_string(saswp_label_text('translation-pros')).'</span><br>
                         '.wpautop( stripslashes ( $saswp_review_props ) ).'
                    </div>
                    <div class="saswp-lst">   
                        <span>'.saswp_t_string(saswp_label_text('translation-cons')).'</span><br>
                        '.wpautop( stripslashes ( $saswp_review_cons ) ).'
                    </div>
                </div>';   
             
            }
                  
            if(!empty($saswp_review_item_feature) || $saswp_review_description != ''){
                
                $boxdata.='<table class="saswp-rvw">
                        <tbody>
                        <div class="saswp-rvw-hd saswp-rb-hd">
                            <span>'.saswp_t_string(saswp_label_text('translation-review-overview')).'</span>
                        </div>';  
                  
                if(isset($saswp_review_item_feature)){
                    
                    for($i=0; $i<count($saswp_review_item_feature); $i++){
                        
                     $boxdata.='<tr>
                            <td class="saswp-rb-rif">'.esc_attr($saswp_review_item_feature[$i]).'</td>
                            <td class="saswp-rb-risr">
                                '.saswp_get_rating_html_by_value($saswp_review_item_star_rating[$i]).'
                            </td>
                        </tr>'; 
                   }   
                }                                                                                                              
                $boxdata.='<tr>
                            <td class="saswp-rvw-sm">
                                <span>'.saswp_t_string(saswp_label_text('translation-summary')).'</span>
                                <div class="rvw-dsc">
                                '.wpautop( stripslashes ( $saswp_review_description ) ).'
                                </div>
                            </td>
                            <td>';                                
                                if($saswp_over_all_rating > 0){
                                    
                                    $boxdata.= '<div class="saswp-rvw-ov">'
                                               .'<div class="saswp-rvw-fs">'.esc_html(number_format ($saswp_over_all_rating, 1)).'</div>                                                                        
                                               '.saswp_get_rating_html_by_value($saswp_over_all_rating).'</div>';
                                    
                                }                                                                                                         
                $boxdata.= '</td>
                        <tr>
                    </tbody>
                </table>'; 
               }
                                           
            return $boxdata;
            
        }        
        /**
         * Display the review box
         * @global type $sd_data
         * @param type $content
         * @return string
         */
        public function saswp_display_review_box($content){
            
            global $sd_data;  
            $saswp_review_details     =  get_post_meta(get_the_ID(), 'saswp_review_details', true); 
            $saswp_review_item_enable = 0;
            
            if(isset($saswp_review_details['saswp-review-item-enable'])){
             $saswp_review_item_enable =  $saswp_review_details['saswp-review-item-enable'];  
            }  
            $review_module = 0;
            
            if(isset($sd_data['saswp-review-module'])){
               $review_module =  $sd_data['saswp-review-module'];
            }
            if($review_module==0 || $saswp_review_item_enable ==0){
                return $content;
            }
            $result = $this->saswp_get_review_box_content();                 
            
            if(isset($saswp_review_details['saswp-review-location'])){
                
            switch ($saswp_review_details['saswp-review-location']) {
                case 1: 
                    $content = $content.$result;                    
                    break;
                case 2:
                    $content = $result.$content;
                    break;
                case 3:
                    add_shortcode('saswp-review', array($this,'saswp_review_display_via_shortcode'));
                    break;
                
                default:
                    break;
            }   
            
            }
            
            return $content;
        }
        
        /**
         * Add custom css of rating box module
         * @since 1.27
         * */
        public function saswp_rating_box_custom_style()
        {
            global $sd_data;
            if(saswp_global_option() && isset($sd_data['saswp-review-module']) && $sd_data['saswp-review-module'] == 1){

                $bg_review_color = '#000'; $review_f_color = '#fff'; $review_f_size = '15'; $review_f_unit = 'px';
                $review_if_color = '#000'; $review_if_size = '18'; $review_if_unit = 'px';
                $stars_size = '18px';
                $review_ar_color = '#000'; $review_ar_size = '48'; $review_ar_unit = 'px';
                if(isset($sd_data['saswp-rbcc-review-bg-color'])){
                    $bg_review_color =  esc_attr($sd_data['saswp-rbcc-review-bg-color']);
                }
                if(isset($sd_data['saswp-rbcc-review-f-color'])){
                    $review_f_color =  esc_attr($sd_data['saswp-rbcc-review-f-color']);
                }
                if(isset($sd_data['saswp-rbcc-review-f-size'])){
                    $review_f_size =  esc_attr($sd_data['saswp-rbcc-review-f-size']);
                }
                if(isset($sd_data['saswp-rbcc-review-f-unit'])){
                    $review_f_unit =  esc_attr($sd_data['saswp-rbcc-review-f-unit']);
                }
                if(isset($sd_data['saswp-rbcc-if-color'])){
                    $review_if_color =  esc_attr($sd_data['saswp-rbcc-if-color']);
                }
                if(isset($sd_data['saswp-rbcc-if-f-size'])){
                    $review_if_size =  esc_attr($sd_data['saswp-rbcc-if-f-size']);
                }
                if(isset($sd_data['saswp-rbcc-if-f-unit'])){
                    $review_if_unit =  esc_attr($sd_data['saswp-rbcc-if-f-unit']);
                } 
                if(isset($sd_data['saswp-rbcc-stars-f-size'])){
                    $stars_size = esc_attr($sd_data['saswp-rbcc-stars-f-size'].'px');
                }
                if(isset($sd_data['saswp-rbcc-ar-color'])){
                    $review_ar_color = esc_attr($sd_data['saswp-rbcc-ar-color']);
                }
                if(isset($sd_data['saswp-rbcc-ar-f-size'])){
                    $review_ar_size = esc_attr($sd_data['saswp-rbcc-ar-f-size']);
                }
                if(isset($sd_data['saswp-rbcc-ar-f-unit'])){
                    $review_ar_unit = esc_attr($sd_data['saswp-rbcc-ar-f-unit']);
                }    
                $custom_css = ".saswp-rb-hd span, .saswp-rvw-sm span{
                            background-color: {$bg_review_color};
                            color: {$review_f_color};
                            font-size: {$review_f_size}{$review_f_unit}; 
                        }
                        .saswp-rb-rif{
                            color: {$review_if_color};
                            font-size: {$review_if_size}{$review_if_unit};
                        }
                        .saswp-rvw-str .saswp_star_color svg, .saswp-rvw-str .saswp_star_color_gray svg, .saswp-rvw-str .saswp_half_star_color svg{
                            width: {$stars_size}; 
                        }
                        .saswp-rvw-fs{
                            color: {$review_ar_color};
                            font-size: {$review_ar_size}{$review_ar_unit};
                        }
                    ";
                wp_add_inline_style( 'saswp-style', $custom_css );
            }
            
        }

        /**
         * Add custom js of rating box module
         * @since 1.27
         * */
        public function saswp_rating_box_custom_script()
        {
            global $sd_data;
            if(saswp_global_option() && isset($sd_data['saswp-review-module']) && $sd_data['saswp-review-module'] == 1){
                ?>
                    <script type="text/javascript">
                        let saswpStarColor = "<?php echo isset($sd_data['saswp-rbcc-stars-color'])?$sd_data['saswp-rbcc-stars-color']:'#000';  ?>";
                        jQuery('.saswp_star_color .saswp_star').attr('stop-color', saswpStarColor);   
                    </script>
                <?php    
            }
        }
        
}
if (class_exists('saswp_rating_box_frontend')) {
	$object = new saswp_rating_box_frontend();
        $object->saswp_review_hooks();
};