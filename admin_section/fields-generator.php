<?php
/**
 * Field Generator class
 *
 * @author   Magazine3
 * @category Admin
 * @path     google_review/google_review
 * @Version 1.8
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/*
  Metabox to show ads type such as custom and adsense 
 */
class saswp_fields_generator {
    
    public function saswp_tooltip_message($meta_field_id){
        
        $tooltip_message = '';
        
        switch ($meta_field_id) {
            
            case 'saswp_kb_type':
               //$tooltip_message = 'Test Message';
                break;

            default:
                break;
        }
        
        return $tooltip_message;
    }
    /**
     * Function to generate html element from the given elements array
     * @param type $meta_fields
     * @param type $settings
     * @param type $field_type
     * @since version 1.0
     */
    public function saswp_field_generator( $meta_fields, $settings, $field_type = null ) {  
                        
		$output          = '';
                $tooltip_message = '';
                
                
		foreach ( $meta_fields as $meta_field ) {
                    
                        $tooltip_message = $this->saswp_tooltip_message($meta_field['id']);
                        
                        $class      = "";
                        $note       = "";  
                        $proversion = false;
                        $hidden     = array();
                        $attribute  = array();
                        
                            $on                 = 'Reviews';
                            $license_key        = '';
                            $license_status     = 'inactive';
                            $license_status_msg = '';
                            $rv_limits          = '';
                            
                            if(isset($settings[strtolower($on).'_addon_license_key'])){
                            $license_key =   $settings[strtolower($on).'_addon_license_key'];
                            }

                            if(isset($settings[strtolower($on).'_addon_license_key_status'])){
                              $license_status =   $settings[strtolower($on).'_addon_license_key_status'];
                            }

                            if(isset($settings[strtolower($on).'_addon_license_key_message'])){
                              $license_status_msg =   $settings[strtolower($on).'_addon_license_key_message'];
                            }
                            
                            if($license_status =='active'){
                              $rv_limits =   get_option(strtolower($on).'_addon_reviews_limits');
                            }
                                                                        
                        if(array_key_exists('class', $meta_field)){
                            
                            $class = $meta_field['class'];    
                            
                        }
                        if(array_key_exists('proversion', $meta_field)){
                            
                            $proversion = $meta_field['proversion'];  
                            
                        
                        }
                        if(array_key_exists('note', $meta_field)){
                            
                            $note = $meta_field['note'];     
                        
                        }
                        if(array_key_exists('hidden', $meta_field)){
                            
                            $hidden = $meta_field['hidden'];     
                        
                        }
                        if(array_key_exists('attributes', $meta_field)){
                            
                            $attribute = $meta_field['attributes'];     
                        
                        }
                        if($tooltip_message){
                            
                            $label = '<label class="saswp-tooltip" for="' . esc_attr($meta_field['id']) . '">' . esc_html__( $meta_field['label'], 'schema-and-structured-data-for-wp' ).' <span class="saswp-tooltiptext">'.esc_html__($tooltip_message, 'schema-and-structured-data-for-wp').'</span></label>';			
                        
                        }else{
                            
                            $label = '<label class="saswp-tooltip" for="' . esc_attr($meta_field['id']) . '">' . esc_html__( $meta_field['label'], 'schema-and-structured-data-for-wp' ).' <span class="saswp-tooltiptext"></span></label>';			    
                        
                        }
			
                        $attribute_str ='';
                        
                        if(!empty($attribute)){
                            
                            foreach ($attribute as $key => $attr ){

                                $attribute_str .=''.esc_attr($key).'="'.esc_attr($attr).'"';
                           
                            }
                        
                        }            
                        			                        
			switch ( $meta_field['type'] ) {
                            
				case 'media':
                                                                                                     
                                        $mediavalue = array();
                                    
                                            if(isset($settings[$meta_field['id']])){
                                                
                                                $mediavalue = $settings[$meta_field['id']];                                          
                                                
                                            }
                                        
                                        $image_pre = '';
                                        if(saswp_remove_warnings($mediavalue, 'thumbnail', 'saswp_string')){
                                            
                                           $image_pre = '<div class="saswp_image_thumbnail">
                                                         <img class="saswp_image_prev" src="'.esc_attr(saswp_remove_warnings($mediavalue, 'thumbnail', 'saswp_string')).'" />
                                                         <a data-id="'.esc_attr($meta_field['id']).'" href="#" class="saswp_prev_close">X</a>
                                                        </div>'; 
                                            
                                        }    
                                            
					$input = sprintf(
						'<fieldset><input %s class="%s" style="width: 80%%" id="%s" name="%s" type="text" value="%s">'
                                                . '<input data-id="media" style="width: 19%%" class="button" id="%s_button" name="%s_button" type="button" value="Upload" />'
                                                . '<input type="hidden" data-id="'.esc_attr($meta_field['id']).'_id" class="upload-id " name="sd_data['.esc_attr($meta_field['id']).'][id]" id="sd_data['.esc_attr($meta_field['id']).'][id]" value="'.esc_attr(saswp_remove_warnings($mediavalue, 'id', 'saswp_string')).'">'
                                                . '<input type="hidden" data-id="'.esc_attr($meta_field['id']).'_height" class="upload-height" name="sd_data['.esc_attr($meta_field['id']).'][height]" id="sd_data['.esc_attr($meta_field['id']).'][height]" value="'.esc_attr(saswp_remove_warnings($mediavalue, 'height', 'saswp_string')).'">'
                                                . '<input type="hidden" data-id="'.esc_attr($meta_field['id']).'_width" class="upload-width" name="sd_data['.esc_attr($meta_field['id']).'][width]" id="sd_data['.esc_attr($meta_field['id']).'][width]" value="'.esc_attr(saswp_remove_warnings($mediavalue, 'width', 'saswp_string')).'">'
                                                . '<input type="hidden" data-id="'.esc_attr($meta_field['id']).'_thumbnail" class="upload-thumbnail" name="sd_data['.esc_attr($meta_field['id']).'][thumbnail]" id="sd_data['.esc_attr($meta_field['id']).'][thumbnail]" value="'.esc_attr(saswp_remove_warnings($mediavalue, 'thumbnail', 'saswp_string')).'">'
                                                . '<div class="saswp_image_div_'.esc_attr($meta_field['id']).'">'                                               
                                                . $image_pre                                                 
                                                . '</div>'	
                                                . '</fieldset>',
                                                $attribute_str,
                                                $class,
						esc_attr($meta_field['id']),
						esc_attr($meta_field['name']),
                                                esc_url(saswp_remove_warnings($mediavalue, 'url', 'saswp_string')),
						esc_attr($meta_field['id']),
						esc_attr($meta_field['id'])                                                
					);
					break;
				case 'checkbox':
                                    
                                        $hiddenvalue ="";
                                        
                                        if(array_key_exists('id', $hidden) && isset($settings[$hidden['id']])){
                                            
                                         $hiddenvalue = $settings[$hidden['id']];  
                                         
                                        }
                                        $hiddenfield="";
                                        
                                        if(!empty($hidden)){   
                                            
                                          $hiddenfield = sprintf(
						'<input id="%s" name="%s" type="hidden" value="%s">',                                                						
						esc_attr($hidden['id']),
						esc_attr($hidden['name']),					
						esc_attr($hiddenvalue)
					 );  
                                          
                                         }   
                                        $message =''; 
                                                                                                                                                                                                         
					$input = sprintf(
						'<input class="%s" id="%s" name="%s" type="checkbox" %s %s><p>'.$message.'</p>',
                                                esc_attr($class),
                                                esc_attr($meta_field['id']),    
						esc_attr($meta_field['name']),                                              
                                                $hiddenvalue == 1 ? 'checked' : '',
                                                $attribute_str
						);                                          
                                         $input .=$hiddenfield;
					break;                                    
				case 'select':
					$input = sprintf(
						'<select class="%s" id="%s" name="%s">',
                                                $class,
						esc_attr($meta_field['id']),
						esc_attr($meta_field['name'])
					);                                    
					foreach ( $meta_field['options'] as $key => $value ) {	  
                                                $settings_meta_field = '';
                                                if(isset($settings[$meta_field['id']])){
                                                 $settings_meta_field   = $settings[$meta_field['id']];
                                                }
                                            
						$input .= sprintf(
							'<option %s value="%s">%s</option>',
							$settings_meta_field == $key ? 'selected' : '',                                                        
							$key,
							esc_html__( $value, 'schema-and-structured-data-for-wp' )
						);
					}
					$input .= '</select>';
					break;                               
				default:
					                                                                        
                                    switch ($meta_field['id']) {
                                    
                                        case 'saswp-reviews-pro-api':

                                            $pro_api    = '<div class="" style="display:block;">
                                                          '.saswp_get_license_section_html($on, $license_key, $license_status, $license_status_msg, $lable=false, $rv_limits).'
                                                          </div>';
                                                          
                                           
                                            $input = $pro_api;        

                                            break;
                                        
                                        case 'saswp-reviews-module-section':

                                            $input = '<div class="saswp_rv_module_pro_notice">
                                                        <h4>Auto fetch more than 5 reviews, go to pro</strong>
                                                        </h4>
                                                        <ul>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/agoda-img.png">
                                                                <span>Agoda (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/airbnb-img.png">
                                                                <span>Airbnb (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/alternativeto-img.png">
                                                                <span>AlternativeTo (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/amazon-img.png">
                                                                <span>Amazon (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/amazon-img.png">
                                                                <span>Angies List (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/aliexpress-img.png">
                                                                <span>Ali Express (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/appstore-img.png">
                                                                <span>App Store (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/avvo-img.png">
                                                                <span>Avvo (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/bbb-img.png">
                                                                <span>BBB (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/bestbuy-img.png">
                                                                <span>Bestbuy (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/booking-img.png">
                                                                <span>Booking.com (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/capterra-img.png">
                                                                <span>Capterra (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/cars-img.png">
                                                                <span>Cars.com (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/cargurus-img.png">
                                                                <span>Cargurus (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/clutch-img.png">
                                                                <span>Clutch (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/citysearch-img.png">
                                                                <span>Citysearch (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/consumeraffairs-img.png">
                                                                <span>Consumer Affairs (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/creditkarma-img.png">
                                                                <span>CreditKarma (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/customerlobby-img.png">
                                                                <span>CustomerLobby (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/dealerrater-img.png">
                                                                <span>DealerRater (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/ebay-img.png">
                                                                <span>Ebay (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/edmunds-img.png">
                                                                <span>Edmunds (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/etsy-img.png">
                                                                <span>Etsy (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/expedia-img.png">
                                                                <span>Expedia (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/facebook-1-img.png">
                                                                <span>Facebook (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/flipkart-img.png">
                                                                <span>Flipkart (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/foursquare-img.png">
                                                                <span>Foursquare (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/g2crowd-img.png">
                                                                <span>G2Crowd (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/gearbest-img.png">
                                                                <span>Gearbest (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/glassdoor-img.png">
                                                                <span>Glassdoor (coming soon)</span>
                                                            </li>                                                            
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/healthgrades-img.png">
                                                                <span>Healthgrades (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/homeadvisor-img.png">
                                                                <span>HomeAdvisor (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/homestars-img.png">
                                                                <span>Homestars (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/houzz-img.png">
                                                                <span>Houzz (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/hotels-img.png">
                                                                <span>Hotels.com (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/hungerstation-img.png">
                                                                <span>Hungerstation (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/imdb-img.png">
                                                                <span>Imdb (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/indeed-img.png">
                                                                <span>Indeed (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/insiderpages-img.png">
                                                                <span>Insider Pages (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/jet-img.png">
                                                                <span>Jet (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/lawyers-img.png">
                                                                <span>Lawyers.com (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/lendingtree-img.png">
                                                                <span>Lending Tree (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/martindale-img.png">
                                                                <span>Martindale (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/newegg-img.png">
                                                                <span>Newegg (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/openrice-img.png">
                                                                <span>OpenRice (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/opentable-img.png">
                                                                <span>Opentable (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/playstore-img.png">
                                                                <span>Playstore (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/producthunt-img.png">
                                                                <span>ProductHunt (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/ratemds-img.png">
                                                                <span>RateMDs (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/reserveout-img.png">
                                                                <span>Reserveout (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/rottentomatoes-img.png">
                                                                <span>Rottentomatoes (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/siftery-img.png">
                                                                <span>Siftery (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/sitejabber-img.png">
                                                                <span>Sitejabber (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/softwareadvice-img.png">
                                                                <span>SoftwareAdvice (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/steam-img.png">
                                                                <span>Steam (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/talabat-img.png">
                                                                <span>Talabat (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/theknot-img.png">
                                                                <span>The Knot (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/thumbtack-img.png">
                                                                <span>Thumbtack (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/tripadvisor-img.png">
                                                                <span>TripAdvisor (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/trulia-img.png">
                                                                <span>Trulia (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/trustedshops-img.png">
                                                                <span>TrustedShops (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/trustpilot-img.png">
                                                                <span>Trustpilot (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/trustradius-img.png">
                                                                <span>TrustRadius (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/vitals-img.png">
                                                                <span>Vitals (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/walmart-img.png">
                                                                <span>Walmart (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/weddingwire-img.png">
                                                                <span>WeddingWire (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/wish-img.png">
                                                                <span>Wish (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/yelp-img.png">
                                                                <span>Yelp (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/yellowpages-img.png">
                                                                <span>Yellow Pages (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/zillow-img.png">
                                                                <span>Zillow (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/zocdoc-img.png">
                                                                <span>ZocDoc (coming soon)</span>
                                                            </li>
                                                            <li><img src="'.SASWP_PLUGIN_URL.'/admin_section/images/reviews_platform_icon/zomato-img.png">
                                                                <span>Zomato (coming soon)</span>
                                                            </li>
                                                        </ul>
                                                        <div class="saswp-rev-btn">
                                                            <span>Get Review Module with one click</span>
                                                            <a href="#">Get this Module</a>
                                                        </div>    
                                                      </div>';
                                                                                                                                                        
                                            break;
                                        
                                        case 'saswp-google-place-section':

                                            $location = '';
                            
                            if(isset($settings['saswp_reviews_location_name']) && !empty($settings['saswp_reviews_location_name'])){
                                
                                $rv_loc    = $settings['saswp_reviews_location_name'];
                                $rv_blocks = isset($settings['saswp_reviews_location_blocks'])? $settings['saswp_reviews_location_blocks']:array();
                                
                                $i=0;
                                
                                foreach($rv_loc as $rvl){
                                    
                                    if($rvl){
                                                                                
                                        $blocks_fields = apply_filters('saswp_modify_blocks_field', '<input class="saswp-g-blocks-field" name="sd_data[saswp_reviews_location_blocks][]" type="number" min="5" step="5" placeholder="5" value="5" disabled="disabled">', isset($rv_blocks[$i])? $rv_blocks[$i]: 5);
                                        
                                        $location .= '<tr>'
                                        . '<td style="width:12%;"><strong>'.esc_html__( 'Place Id', 'schema-and-structured-data-for-wp' ).'</strong></td>'
                                        . '<td style="width:20%;"><input class="saswp-g-location-field" name="sd_data[saswp_reviews_location_name][]" type="text" value="'. esc_attr($rvl).'"></td>'
                                        . '<td style="width:10%;"><strong>'.esc_html__( 'Reviews', 'schema-and-structured-data-for-wp' ).'</strong></td>'
                                        . '<td style="width:10%;">'.$blocks_fields.'</td>'                                        
                                        . '<td style="width:10%;"><a class="button button-default saswp-fetch-g-reviews">'.esc_html__( 'Fetch', 'schema-and-structured-data-for-wp' ).'</a></td>'
                                        . '<td style="width:10%;"><a type="button" class="saswp-remove-review-item button">x</a></td>'
                                        . '<td style="width:10%;"><p class="saswp-rv-fetched-msg"></p></td>'        
                                        . '</tr>'; 
                                    }
                                   $i++;
                                }
                                
                            }
                            
                            $reviews = '<div class="saswp-g-reviews-settings saswp-knowledge-label">'                                                                
                                . '<table class="saswp-g-reviews-settings-table" style="width:100%">'
                                . $location                                 
                                . '</table>'                                
                                . '<div>'
                                . '<a class="button button-default saswp-add-g-location-btn">'.esc_html__( 'Add Location', 'schema-and-structured-data-for-wp' ).'</a>'
                                .  '<p><a target="_blank" href="https://developers.google.com/maps/documentation/javascript/examples/places-placeid-finder">'.esc_html__( 'Place ID Finder', 'schema-and-structured-data-for-wp' ).'</a></p>'  
                                . '</div>'    
                                . '</div>';  
                                                          
                                           
                                            $input = $reviews;        

                                            break;

                                        default:
                                            
                                             $stng_meta_field = '';
                                    
                                        if(isset($settings[$meta_field['id']])){
                                            
                                                $stng_meta_field =  $settings[$meta_field['id']];  
                                         
                                        }
                                    
					$input = sprintf(
						'<input class="%s" %s id="%s" name="%s" type="%s" value="%s" %s>',
                                                $class,
						$meta_field['type'] !== 'color' ? 'style="width: 100%"' : '',
						esc_attr(saswp_remove_warnings($meta_field, 'id', 'saswp_string')),
						esc_attr(saswp_remove_warnings($meta_field, 'name', 'saswp_string')),
						esc_attr(saswp_remove_warnings($meta_field, 'type', 'saswp_string')),
						esc_attr($stng_meta_field),
                                                $attribute_str
					);
                                            
                                            break;
                                    }
                                                                          
                                                                            	
									
			}
                        $reviews =  $pro_api = $toggle_button = '';
                        
                        if($meta_field['id'] == 'saswp_google_place_api_key'){
                                                                                                                                                                                                                                                                                                                                                                     
                        }
                        
                        $allowed_html = saswp_expanded_allowed_tags();
                        
                        if($meta_field['id'] == 'saswp-reviews-module-section'){
                            $output .= '<li class="saswp-rev-mod">'                                                                
                                .  '<div class="saswp-knowledge-label">'.$label.'</div>'
                                .  '<div class="saswp-knowledge-field">'.$input.'<p class="">'.$note.'</p></div>'
                                                               
                                .  '</li>';
                        }else{
                            $output .= '<li>'                                                                
                                .  '<div class="saswp-knowledge-label">'.$label.'</div>'
                                .  '<div class="saswp-knowledge-field">'.$input.'<p class="">'.$note.'</p></div>'
                                                               
                                .  '</li>';    
                        }

                        
                                
                                                
		}
                                
		echo '<div><div class="saswp-settings-list"><ul>' . wp_kses($output, $allowed_html) . '</ul></div></div>';
	}	        
}
