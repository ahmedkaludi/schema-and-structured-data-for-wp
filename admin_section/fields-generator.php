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

    public $platforms = array(
        array(
            "image" => "/admin_section/images/reviews_platform_icon/google-1-img.png",
            "name"  => "Google Reviews"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/shopper-approved-img.png",
            "name"  => "Shopper Approved"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/agoda-img.png",
            "name"  => "Agoda"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/airbnb-img.png",
            "name"  => "Airbnb"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/alternativeto-img.png",
            "name"  => "AlternativeTo"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/amazon-img.png",
            "name"  => "Amazon"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/angies-list-img.png",
            "name"  => "Angies List"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/aliexpress-img.png",
            "name"  => "Ali Express"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/appstore-img.png",
            "name"  => "App Store"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/avvo-img.png",
            "name"  => "Avvo"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/bbb-img.png",
            "name"  => "BBB"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/bestbuy-img.png",
            "name"  => "Bestbuy"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/booking-com-img.png",
            "name"  => "Booking.com"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/capterra-img.png",
            "name"  => "Capterra"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/cars-com-img.png",
            "name"  => "Cars.com"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/cargurus-img.png",
            "name"  => "Cargurus"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/clutch-img.png",
            "name"  => "Clutch"),
        array("image" => "/admin_section/images/reviews_platform_icon/citysearch-img.png",
            "name"  => "Citysearch"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/consumer-affairs-img.png",
            "name"  => "Consumer Affairs"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/creditkarma-img.png",
            "name"  => "CreditKarma"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/customerlobby-img.png",
            "name"  => "CustomerLobby"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/dealerrater-img.png",
            "name"  => "DealerRater"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/ebay-img.png",
            "name"  => "Ebay"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/edmunds-img.png",
            "name"  => "Edmunds"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/etsy-img.png",
            "name"  => "Etsy"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/expedia-img.png",
            "name"  => "Expedia"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/facebook-img.png",
             "name"  => "Facebook"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/flipkart-img.png",
             "name"  => "Flipkart"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/foursquare-img.png",
            "name"  => "Foursquare"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/g2crowd-img.png",
             "name"  => "G2Crowd"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/gearbest-img.png",
            "name"  => "Gearbest"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/glassdoor-img.png",
            "name"  => "Glassdoor                           "),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/healthgrades-img.png",
            "name"  => "Healthgrades"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/homeadvisor-img.png",
            "name"  => "HomeAdvisor"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/homestars-img.png",
            "name"  => "Homestars"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/houzz-img.png",
            "name"  => "Houzz"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/hotels-com-img.png",
             "name"  => "Hotels.com"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/hungerstation-img.png",
             "name"  => "Hungerstation"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/imdb-img.png",
            "name"  => "Imdb"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/indeed-img.png",
            "name"  => "Indeed"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/insiderpages-img.png",
            "name"  => "Insider Pages"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/jet-img.png",
            "name"  => "Jet"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/lawyers-com-img.png",
             "name"  => "Lawyers.com"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/lendingtree-img.png",
             "name"  => "Lending Tree"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/martindale-img.png",
             "name"  => "Martindale"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/newegg-img.png",
            "name"  => "Newegg"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/openrice-img.png",
            "name"  => "OpenRice"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/opentable-img.png",
             "name"  => "Opentable"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/playstore-img.png",
             "name"  => "Playstore"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/producthunt-img.png",
             "name"  => "ProductHunt"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/ratemds-img.png",
             "name"  => "RateMDs"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/reserveout-img.png",
             "name"  => "Reserveout"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/rottentomatoes-img.png",
             "name"  => "Rottentomatoes"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/siftery-img.png",
             "name"  => "Siftery"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/sitejabber-img.png",
             "name"  => "Sitejabber"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/softwareadvice-img.png",
             "name"  => "SoftwareAdvice"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/steam-img.png",
             "name"  => "Steam"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/talabat-img.png",
             "name"  => "Talabat"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/theknot-img.png",
             "name"  => "The Knot"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/thumbtack-img.png",
             "name"  => "Thumbtack"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/tripadvisor-img.png",
            "name"  => "TripAdvisor"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/trulia-img.png",
            "name"  => "Trulia"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/trustedshops-img.png",
             "name"  => "TrustedShops"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/trustpilot-img.png",
            "name"  => "Trustpilot"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/trustradius-img.png",
            "name"  => "TrustRadius"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/vitals-img.png",
            "name"  => "Vitals"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/walmart-img.png",
            "name"  => "Walmart"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/weddingwire-img.png",
             "name"  => "WeddingWire"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/wish-img.png",
             "name"  => "Wish "),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/yelp-img.png",
            "name"  => "Yelp"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/yellowpages-img.png",
             "name"  => "Yellow Pages"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/zillow-img.png",
             "name"  => "Zillow"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/zocdoc-img.png",
            "name"  => "ZocDoc"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/zomato-img.png",
             "name"  => "Zomato"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/judge-me-img.png",
             "name"  => "Judge.me"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/shopify-app-store-img.png",
            "name"  => "Shopify App Store"),
        array(
            "image" => "/admin_section/images/reviews_platform_icon/goodreads-img.png",
            "name"  => "Goodreads")
);
    
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
                            
                            $label = '<label class="saswp-tooltip" for="' . esc_attr($meta_field['id']) . '">' . saswp_t_string( $meta_field['label'] ).' <span class="saswp-tooltiptext">'.saswp_t_string($tooltip_message).'</span></label>';			
                        
                        }else{
                            
                            $label = '<label class="saswp-tooltip" for="' . esc_attr($meta_field['id']) . '">' . saswp_t_string( $meta_field['label'] ).' <span class="saswp-tooltiptext"></span></label>';			    
                        
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
							saswp_t_string( $value )
						);
					}
					$input .= '</select>';
					break; 
                                case 'multiselect':
					$input = sprintf(
						'<select class="%s" id="%s" name="%s[]" multiple>',
                                                $class,
						esc_attr($meta_field['id']),
						esc_attr($meta_field['name'])
					);                                      
                                        $settings_meta_field = array();
                                        if(isset($settings[$meta_field['id']])){
                                         $settings_meta_field   = $settings[$meta_field['id']];
                                        }
                                    
					foreach ( $meta_field['options'] as $key => $value ) {	  
                                            
                                                if($key == 'administrator'){
                                                    
                                                    $input .= sprintf(
							'<option %s value="%s">%s (Default)</option>',
							'selected',                                                        
							$key,
							saswp_t_string( $value )
						   );
                                                    
                                                }else{                                                    
                                                    $input .= sprintf(
							'<option %s value="%s">%s</option>',
							in_array($key, $settings_meta_field)  ? 'selected' : '',                                                        
							$key,
							saswp_t_string( $value )
						   );
                                                    
                                                }                                            
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

                                            if($this->platforms){

                                                $input = '<div class="saswp_rv_module_pro_notice">
                                                      <h2>Get Your 5 Stars Reviews on Google SERPs</h2>
                                                      <p class="saswp_desc">Automatically Fetch your customer reviews from 80+ Platforms and show them on your website with proper schema support. <a target="_blank" href="https://structured-data-for-wp.com/reviews-for-schema">Learn More...</a></p>
                                                      <div class="saswp_cmpny_lst">                                                            
                                                      <span class="saswp_lst saswp_avlbl">Integrations Available</span>
                                                      <ul>';
                                                      
                                                    foreach ($this->platforms as $platform) {
                                                        
                                                    $input .= '<li class=""><img src="'.esc_url(SASWP_PLUGIN_URL.$platform['image']).'">
                                                              <span class="saswp_cmpny">'.esc_html($platform['name']).'</span>
                                                              </li>';        
                                                    }      
                                                      
                                                $input .= '</ul>
                                                        </div>
                                                        <div class="saswp-rev-btn">
                                                            <span>With our API service, you can fetch reviews from anywhere you want! and we are always increasing the number of integrations. You can also request for an integration as well.</span>
                                                            <a target="_blank" href="https://structured-data-for-wp.com/reviews-for-schema">Get The Reviews Addon Now</a>
                                                        </div>    
                                                      </div>';

                                            }
                                                                                                                                                                                                    
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
                                        . '<td style="width:12%;"><strong>'.saswp_t_string( 'Place Id' ).'</strong></td>'
                                        . '<td style="width:20%;"><input class="saswp-g-location-field" name="sd_data[saswp_reviews_location_name][]" type="text" value="'. esc_attr($rvl).'"></td>'
                                        . '<td style="width:10%;"><strong>'.saswp_t_string( 'Reviews' ).'</strong></td>'
                                        . '<td style="width:10%;">'.$blocks_fields.'</td>'                                        
                                        . '<td style="width:10%;"><a class="button button-default saswp-fetch-g-reviews">'.saswp_t_string( 'Fetch' ).'</a></td>'
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
                                . '<a class="button button-default saswp-add-g-location-btn">'.saswp_t_string( 'Add Location' ).'</a>'
                                .  '<p><a target="_blank" href="https://developers.google.com/maps/documentation/javascript/examples/places-placeid-finder">'.saswp_t_string( 'Place ID Finder' ).'</a></p>'  
                                . '</div>'    
                                . '</div>';  
                                                                                                     
                                $input = $reviews;        

                                        break;
                                            
                                        case 'saswp-shopper-approved-section':
                                            
                                            $reviews = '<div class="saswp-s-approved-reviews-settings saswp-knowledge-label">'                                                                
                                                . '<table class="saswp-s-reviews-settings-table" style="width:100%">'                                                                               
                                                . '<tr>'
                                                . '<td style="width:12%;"><strong>'.saswp_t_string( 'Site Id' ).'</strong></td>'
                                                . '<td style="width:10%;"><input class="saswp-g-location-field" id="saswp_s_approved_site_id" name="sd_data[saswp_s_approved_site_id]" type="text" value="'.$settings['saswp_s_approved_site_id'].'"></td>'
                                                . '<td style="width:10%;"><strong>'.saswp_t_string( 'Token' ).'</strong></td>'
                                                . '<td style="width:20%;"><input class="saswp-g-blocks-field" id="saswp_s_approved_token" name="sd_data[saswp_s_approved_token]" type="text" value="'.$settings['saswp_s_approved_token'].'"></td>'                                        
                                                . '<td style="width:5%;"><strong>'.saswp_t_string( 'Reviews' ).'</strong></td>'
                                                . '<td style="width:15%;"><input class="saswp-g-blocks-field" id="saswp_s_approved_reviews" name="sd_data[saswp_s_approved_reviews]" type="number" min="1" max="500" value="'.$settings['saswp_s_approved_reviews'].'"></td>'                                        
                                                . '<td style="width:10%;"><a class="button button-default saswp-fetch-s-approved-reviews">'.saswp_t_string( 'Fetch' ).'</a></td>'                                                        
                                                . '<td style="width:10%;"><p class="saswp-rv-fetched-msg"></p></td>'        
                                                . '</tr>'   
                                                . '</table>'                                
                                                . '<div>'                                                
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
                        $reviews =  $pro_api = '';
                        
                        $subfields = '';

                        if($meta_field['id'] == 'saswp-stars-rating-checkbox'){
                            
                            $sel_value = array();

                            if(isset($settings['saswp-stars-post-taype'])){
                                $sel_value = $settings['saswp-stars-post-taype'];                           
                            }                             

                            $post_type = saswp_post_type_generator();
                            
                            if($post_type){
                                
                                if(isset($settings['saswp-stars-rating']) && $settings['saswp-stars-rating'] == 1){
                                    $subfields .= '<div><table class="saswp-stars-post-table">';
                                }else{
                                    $subfields .= '<div><table class="saswp-stars-post-table saswp_hide">';
                                }
                                
                                foreach($post_type as $key => $value){
                                    $subfields .= '<tr><td><input type="checkbox" name="sd_data[saswp-stars-post-taype][]" value="'.esc_attr($key).'" '.(in_array($key, $sel_value) ? 'checked':'' ).' /></td><td> '.esc_html($value).' </td><tr>';
                                }

                                $subfields .= '</table></div>';

                            }

                        }
                        

                        $allowed_html = saswp_expanded_allowed_tags();
                        
                        if($meta_field['id'] == 'saswp-reviews-module-section'){
                            $output .= '<li class="saswp-rev-mod">'                                                                
                                .  '<div class="saswp-knowledge-label">'.$label.'</div>'
                                .  '<div class="saswp-knowledge-field">'.$input.'<p class="">'.$note.'</p>'.$subfields.'</div>'                                                               
                                .  '</li>';
                        }else{
                            $output .= '<li>'                                                                
                                .  '<div class="saswp-knowledge-label">'.$label.'</div>'
                                .  '<div class="saswp-knowledge-field">'.$input.'<p class="">'.$note.'</p>'.$subfields.'</div>'
                                                               
                                .  '</li>';    
                        }
                                                                                                        
		}
                                
		echo '<div><div class="saswp-settings-list"><ul>' . wp_kses($output, $allowed_html) . '</ul></div></div>';
	}	        
}