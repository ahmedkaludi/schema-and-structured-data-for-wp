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
                                                                        
            add_filter('the_content', array($this,'saswp_content'),9);            
	          add_action('wp_footer', array($this, 'saswp_get_flexidx_listing'));            
            add_action('amp_post_template_footer',array($this, 'saswp_get_flexidx_listing'));                
	}
        public function saswp_get_flexidx_listing(){
            
            global $fmc_api;
                           
            if(!empty($fmc_api)){		


					$custom_page = new flexmlsConnectPageSearchResults($fmc_api);
		        	list($params, $cleaned_raw_criteria, $context) =  $custom_page->parse_search_parameters_into_api_request();                  
							
					require_once( ABSPATH . '/wp-content/plugins/flexmls-idx/lib/flexmlsAPI/Core.php' );
					$flexcoreApi = new flexmlsAPI_Core();
                                    
					$tag = get_query_var('fmc_tag');
					 preg_match('/mls\_(.*?)$/', $tag, $matches);

					$id_found = $matches[1];

					$filterstr = "ListingId Eq '{$id_found}'";

					if ( $mls_id = flexmlsConnect::wp_input_get('m') ) {
					  $filterstr .= " and MlsId Eq '".$mls_id."'";
					}
					$params['_filter'] = $filterstr;
					$params['_limit']  = 1;
					$results = $flexcoreApi->GetListings($params);								
				  
				  if(!empty($results)){
					  
					$count = count($results); 
                  
                    echo "\n";
                    echo '<!-- Schema & Structured Data For WP v'.esc_attr(SASWP_VERSION)   .' IDX - -->';
                    echo "\n";
                    echo '<script class="saswp-schema-markup-output" type="application/ld+json">'; 
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
               
          
          $link_to_details = get_permalink();
          
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
                                "brand"             => array(
                                    '@type' => 'Brand',
                                    'name'  => get_bloginfo()                                    
                                ),
                                "mpn"               => esc_attr($result['StandardFields']['YearBuilt']),
				"url"		    => esc_url($link_to_details),
                                "aggregateRating"   => array(
                                                            "@type"=> "AggregateRating",
                                                            "ratingValue" => '5.0',
                                                            "reviewCount" => '1'
                                                         ),
                                "review"            => array(
                                                                        '@type'	=> 'Review',
                                                                        'author'	=> array(
                                                                                '@type' => 'Person',
                                                                                'name'  => get_the_author(),
                                                                         ),
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