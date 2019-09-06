<?php 
/**
 * Flexmls Class
 *
 * @author   Magazine3
 * @category Frontend
 * @path  output/flexmls
 * @Version 1.0.7
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class saswp_flexmls_list extends flexmlsConnectPageCore{
    
        public $shorcode = array();
        protected $search_criteria;
        
	function __construct() {  
            
            global $fmc_api;      
            
            if(!empty($fmc_api)){
                
                parent::__construct($fmc_api);
                
            }
                                                
            add_filter('the_content', array($this,'saswp_content'),9);            
	    add_action('wp_footer', array($this, 'saswp_get_flexidx_listing'));            
            add_action('amp_post_template_footer',array($this, 'saswp_get_flexidx_listing'));                
	}
        public function saswp_get_flexidx_listing(){
            
            global $fmc_api;
                           
            if(!empty($fmc_api)){
                
            $settings = array();          
            
            foreach ($this->shorcode[1] as $shortcodeAttr){
                
               if (preg_match_all('/(\b\w+)="(.*?(?="\s\w+=|$))/', $shortcodeAttr, $matches)) {
                   
                $settings = array_combine ( $matches[1], $matches[2] ); 
                
                }
                
                $title              = isset($settings['title']) ? ($settings['title']) : '';
                $source             = isset($settings['source']) ? trim($settings['source']) : '';
                $display            = isset($settings['display']) ? trim($settings['display']) : '';
                $days               = isset($settings['days']) ? trim($settings['days']) :  '';
                $property_type      = isset($settings['property_type']) ? trim($settings['property_type']): '';
                $property_sub_type  = isset($settings['property_sub_type']) ? trim($settings['property_sub_type']): '';
                $link               = isset($settings['link']) ? trim($settings['link']) : '';
                $sort               = isset($settings['sort']) ? trim($settings['sort']) : '';
                $agent              = isset($settings['agent']) ? trim($settings['agent']) : '';
                $status             = isset($settings['status']) ? trim($settings['status'], '"') : '';                
                $locations = '';

                if ( isset($settings['location']) ) {
                  $locations = html_entity_decode( flexmlsConnect::clean_comma_list( stripslashes( $settings['location'] )  ) );
                }
                 if ($link == "default") {
                    $link = flexmlsConnect::get_default_idx_link();
                  }
                $source = (empty($source)) ? "my" : $source;  
                $pure_conditions = array();
                
                if (isset($settings['days'])){
                    $days = $settings['days'];
                  }
                  elseif ($display == "open_houses"){
                    //For backward compatibility. Set # of days for open house default to 10
                    $days = 10;
                  }
                  else{
                    $days = 1;
                    if (date("l") == "Monday")
                      $days = 3;
                  }
                
                   $flexmls_temp_date = date_default_timezone_get();
                    date_default_timezone_set('America/Chicago');
                    $specific_time = date("Y-m-d\TH:i:s.u",strtotime("-".$days." days"));
                    date_default_timezone_set($flexmls_temp_date);
                
                  if ($display == "new") {
                    $pure_conditions["OriginalOnMarketTimestamp"] = $specific_time;
                  }
                  elseif ($display == "open_houses") {
                    $pure_conditions['OpenHouses'] = $days;
                  }
                  elseif ($display == "price_changes") {
                    $pure_conditions["PriceChangeTimestamp"] = $specific_time;
                  }
                  elseif ($display == "recent_sales") {
                    $pure_conditions["StatusChangeTimestamp"] = $specific_time;
                  }

                  if ($sort == "recently_changed") {
                    $pure_conditions['OrderBy'] = "-ModificationTimestamp"; // special tag caught later
                  }
                  elseif ($sort == "price_low_high") {
                    $pure_conditions['OrderBy'] = "+ListPrice";
                  }
                  elseif ($sort == "price_high_low") {
                    $pure_conditions['OrderBy'] = "-ListPrice";
                  }
                 elseif ($sort == "open_house"){
                    $pure_conditions['OrderBy'] = "+OpenHouses";
                  }
                  elseif ($sort == "sqft_low_high") {
                    $pure_conditions['OrderBy'] = "+BuildingAreaTotal";
                  }
                  elseif ($sort == "sqft_high_low") {
                    $pure_conditions['OrderBy'] = "-BuildingAreaTotal";
                  }
                  elseif ($sort == "year_built_high_low") {
                    $pure_conditions['OrderBy'] = "-YearBuilt";
                  }
                  elseif ($sort == "year_built_low_high") {
                    $pure_conditions['OrderBy'] = "+YearBuilt";
                  }

                  $apply_property_type = ($source == 'location') ? true : false;
                  
                  if ($source == 'agent') {
                      
                    $pure_conditions['ListAgentId'] = $agent;
                    
                  }
                   // parse location search settings
                  $locations = flexmlsConnect::parse_location_search_string($locations);
                  if($locations){
                      
                  foreach ($locations as $loc) {
                      
                        if(array_key_exists($loc['f'], $pure_conditions)) {
                          $pure_conditions[$loc['f']] .=  ',' . $loc['v'];
                        } else {
                          $pure_conditions[$loc['f']] = $loc['v'];
                        }
                        
                      }    
                  }                  

                      if ($apply_property_type and !empty($property_type)) {
                        $pure_conditions['PropertyType'] = $property_type;
                        $pure_conditions['PropertySubType'] = $property_sub_type;
                      }

                      if ($link) {
                        $link_details = $fmc_api->GetIDXLinkFromTinyId($link);

                        if ($link_details['LinkType'] == "SavedSearch") {
                            $pure_conditions['SavedSearch'] = $link_details['SearchId'];
                          }
                      }
                  
                      if ($source == "my") {
                     // make a simple request to /my/listings with no _filter's
                        $pure_conditions['My'] = 'listings';
                      }
                      elseif ($source == "office") {
                        $pure_conditions['My'] = 'office';
                      }
                      elseif ($source == "company") {
                        $pure_conditions['My'] = 'company';
                      }

                      if ($status) {
                        $pure_conditions["StandardStatus"] = $status;
                      }
                 
                  $custom_page = new flexmlsConnectPageSearchResults($fmc_api);
                  $custom_page->title = $title;
                  $custom_page->input_source = 'shortcode'; 
                  $custom_page->input_data = $pure_conditions;
                  list($params, $cleaned_raw_criteria, $context) =  $custom_page->parse_search_parameters_into_api_request();                  
                  unset($params['_select']);
                  $this->search_criteria = $cleaned_raw_criteria;
                  
                   require_once( ABSPATH . '/wp-content/plugins/flexmls-idx/lib/flexmlsAPI/Core.php' );
                  $flexcoreApi = new flexmlsAPI_Core();
                  
                   if ($context == "listings") {
                    $results = $flexcoreApi->GetMyListings($params);

                  }
                  elseif ($context == "office") {
                    $results = $flexcoreApi->GetOfficeListings($params);
                  }
                  elseif ($context == "company") {
                    $results = $flexcoreApi->GetCompanyListings($params);
                  }
                  else {
                    $cache_time = (strpos($params['_filter'],'ListingCart')!==false) ? 0 : '10m';
                    $results = $flexcoreApi->GetListings($params, $cache_time);
                  }                  
                   $count = count($results); 
                    echo "\n";
                    echo '<!-- Schema & Structured Data For WP v'.esc_attr(SASWP_VERSION)   .' IDX - -->';
                    echo "\n";
                    echo '<script type="application/ld+json">'; 
                    echo "\n";
                    echo "[";                     
                    
                    foreach ($results as $result){  
                        
                        if($count > 1){
                            
                            echo json_encode($this->saswp_generate_schema_markup($result)).',';   
                        
                        }else{
                            
                            echo json_encode($this->saswp_generate_schema_markup($result));   
                         
                        }   
                        
                        $count --;    
                        
                    }                 
                    echo ']';
                    echo "\n";
                    echo '</script>';                                     
                  }
                
            }
                                                                                                                                                   
        }
        public function saswp_content($content){
            
            preg_match_all("/[[]idx_listing_summary(.*?)[]]/", $content,$matches);  
            
            $this->shorcode = $matches;
            
            return $content;
            
        }
        public function saswp_generate_schema_markup($result){    
            
          global $sd_data;
          $sellername        = '';
          $sellerurl         = '';
          $sellerimage       = '';
          $selleraddress     = '';
          $sellertelephone   = '';
          $sellerpricerange  = '';                   
          
          if(isset($sd_data['saswp-flexmlx-compativility']) &&  $sd_data['saswp-flexmlx-compativility'] == 1 && is_plugin_active('flexmls-idx/flexmls_connect.php')){
           
              if(isset($sd_data['sd-seller-name'])){
                $sellername  =$sd_data['sd-seller-name'];
              }
              if(isset($sd_data['sd-seller-url'])){
                 $sellerurl =$sd_data['sd-seller-url'];
              }
              if(isset($sd_data['sd_seller_image']['thumbnail'])){
                 $sellerimage = $sd_data['sd_seller_image']['thumbnail'];
              } 
              if(isset($sd_data['sd-seller-address'])){
                 $selleraddress =$sd_data['sd-seller-address'];
              }
              if(isset($sd_data['sd-seller-telephone'])){
                 $sellertelephone =$sd_data['sd-seller-telephone'];
              }
              if(isset($sd_data['sd-seller-price-range'])){
                 $sellerpricerange =$sd_data['sd-seller-price-range'];
              }
               
          $link_to_details_criteria = $this->search_criteria;
          $link_to_details = flexmlsConnect::make_nice_address_url($result['StandardFields'], $link_to_details_criteria, 'fmc_tag');
          
          $photos = array();
          
          if(isset($result['StandardFields'])){              
              
              foreach ($result['StandardFields']['Photos'] as $photo){
                  
               $photos[] = array(
                   'url' => $photo['UriThumb']
               ); 
               
              }              
          }    
          
            $input = array();
            $input = array(
				"@context" 	    => saswp_context_url(),
				"@type"		    => ["Product", "Apartment"],
				"name"              => esc_attr($result['StandardFields']['UnparsedFirstLineAddress']),
                                "description"       => isset($result['StandardFields']['PublicRemarks'])? $result['StandardFields']['PublicRemarks']:strip_tags(get_the_excerpt()),
                                "sku"               => esc_attr($result['StandardFields']['BuildingAreaTotal']),
                                "brand"             => get_bloginfo(),
                                "mpn"               => esc_attr($result['StandardFields']['YearBuilt']),
				"url"		    => esc_url($link_to_details),
                                "aggregateRating"   => array(
                                                            "@type"=> "AggregateRating",
                                                            "ratingValue" => '5.0',
                                                            "reviewCount" => '1'
                                                         ),
                                "review"            => array(
                                                                        '@type'	=> 'Review',
                                                                        'author'	=> get_the_author(),
                                                                        'datePublished'	=> $result['StandardFields']['ListingUpdateTimestamp'],                                                                        
                                                                        'reviewRating'  => array(
                                                                                '@type'	=> 'Rating',
                                                                                'bestRating'	=> '5.0',
                                                                                'ratingValue'	=> '5.0',
                                                                                'worstRating'	=> '1.0',
                                                                        )  
                                          ),                         
                                "image" 	    => esc_url($result['StandardFields']['Photos'][0]['Uri300']),
				"offers"            => array(
							"priceCurrency"	  => "USD",
                                                        "price"		  => esc_attr($result['StandardFields']['ListPrice']),
                                                        "availability"	  => 'InStock',
                                                        "url"		  => esc_url($link_to_details),
                                                        "priceValidUntil" => $result['StandardFields']['ListingUpdateTimestamp'],
							"seller"	  => array(
                                                            array(
                                                                "@type"      => "RealEstateAgent",
                                                                "name"       => esc_attr($sellername), 
                                                                "url"        => esc_url($sellerurl),
                                                                "image"      => esc_attr($sellerimage),   
                                                                "address"    => esc_attr($selleraddress),
                                                                "priceRange" => esc_attr($sellerpricerange),  
                                                                "telephone"  => esc_attr($sellertelephone),         
                                                            )
                                                        ),
							),
				'address'	    => esc_attr($result['StandardFields']['StreetNumber']).' '. esc_attr($result['StandardFields']['StreetName']).' '.esc_attr($result['StandardFields']['StreetSuffix']) .' '.esc_attr($result['StandardFields']['City']).' '. esc_attr($result['StandardFields']['PostalCode']),
				'geo'		    => array(
                                                            "@type"             => "GeoCoordinates",
                                                             "address"          => esc_attr($result['StandardFields']['UnparsedFirstLineAddress']), 
                                                             "addressCountry"   => esc_attr($result['StandardFields']['UnparsedFirstLineAddress']),                                                             
                                                     ),
						
				'photos'	    => $photos,
			);
           
             return $input;
             }  
        }        		
}
if (class_exists('saswp_flexmls_list')) {
	new saswp_flexmls_list;
};