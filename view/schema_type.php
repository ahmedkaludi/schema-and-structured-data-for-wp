<?php                                                                               
	add_action( 'add_meta_boxes', 'saswp_schema_type_add_meta_box' ) ;
	add_action( 'save_post', 'saswp_schema_type_add_meta_box_save' ) ;
	
        function saswp_schema_type_add_meta_box() {
	add_meta_box(
		'schema_type',
		esc_html__( 'Schema Type', 'ads-for-wp' ),
		'saswp_schema_type_meta_box_callback',
		'saswp',
		'advanced',
		'high'
	);
        }
        function saswp_schema_type_get_meta( $value ) {
            global $post;
            
            $field = get_post_meta( $post->ID, $value, true );
           
            if ( ! empty( $field ) ) {
                    return is_array( $field ) ? stripslashes_deep( $field ) : stripslashes( wp_kses_decode_entities( $field ) );
            } else {
                    return false;
            }
      }
        function saswp_schema_type_meta_box_callback( $post) {
                wp_nonce_field( 'saswp_schema_type_nonce', 'saswp_schema_type_nonce' );  
                $style_business_type ='';
                $style_business_name =''; 
                $style_service_name =''; 
                $business_name ='';
                $schema_type ='';
                $business_type ='';
                $business_details ='';
                if($post){
                $schema_type      = esc_sql ( get_post_meta($post->ID, 'schema_type', true)  );                  
                $business_type    = esc_sql ( get_post_meta($post->ID, 'saswp_business_type', true)  ); 
                $business_name    = esc_sql ( get_post_meta($post->ID, 'saswp_business_name', true)  ); 
                $business_details = esc_sql ( get_post_meta($post->ID, 'saswp_local_business_details', true)  ); 
                $service_details  = esc_sql ( get_post_meta($post->ID, 'saswp_service_schema_details', true)  );                 
                
                $custom_logo_id = get_theme_mod( 'custom_logo' );
                $logo = wp_get_attachment_image_src( $custom_logo_id , 'full' );
                                
                if($schema_type != 'local_business'){
                 $style_business_type = 'style="display:none"';
                 $style_business_name = 'style="display:none"';
                }
                if($schema_type != 'Service'){
                // $style_service_name = 'style="display:none"';                
                }               
                }   
                        
                                $all_dayofweek_array = array(
                                     'monday' => 'Monday',
                                     'tuesday' => 'Tuesday',
                                     'wednesday'     => 'Wednesday',
                                     'thursday'     => 'Thursday',
                                     'friday'      => 'Friday',
                                     'saturday'     => 'Saturday',  
                                     'sunday'     => 'Sunday',  
                                 );
                
                
                                $all_schema_array = array(
                                     'Blogposting' => 'Blogposting',
                                     'NewsArticle' => 'NewsArticle',
                                     'WebPage'     => 'WebPage',
                                     'Article'     => 'Article',
                                     'Recipe'      => 'Recipe',
                                     'Product'     => 'Product',
                                     'Service'     => 'Service',
                                     'qanda'       => 'Q&A',   
                                     'VideoObject' => 'VideoObject',
                                     'local_business' => 'Local Business'
                                 );
                                 $all_business_type = array(
                                    'animalshelter' => 'Animal Shelter',
                                    'automotivebusiness' => 'Automotive Business',
                                    'childcare' => 'ChildCare',
                                    'dentist' => 'Dentist',
                                    'drycleaningorlaundry' => 'Dry Cleaning Or Laundry',
                                    'emergencyservice' => 'Emergency Service',
                                    'employmentagency' => 'Employment Agency',
                                    'entertainmentbusiness' => 'Entertainment Business',
                                    'financialservice' => 'Financial Service',
                                    'foodestablishment' => 'Food Establishment',
                                    'governmentoffice' => 'Government Office',
                                    'healthandbeautybusiness' => 'Health And Beauty Business',
                                    'homeandconstructionbusiness' => 'Home And Construction Business',
                                    'internetcafe' => 'Internet Cafe',
                                    'legalservice' => 'Legal Service',
                                    'library' => 'Library',
                                    'lodgingbusiness' => 'Lodging Business',
                                    'professionalservice' => 'Professional Service',
                                    'radiostation' => 'Radio Station',
                                    'realestateagent' => 'Real Estate Agent',
                                    'recyclingcenter' => 'Recycling Center',
                                    'selfstorage' => 'Self Storage',
                                    'shoppingcenter' => 'Shopping Center',
                                    'sportsactivitylocation' => 'Sports Activity Location',
                                    'store' => 'Store',
                                    'televisionstation' => 'Television Station',
                                    'touristinformationcenter' => 'Tourist Information Center',
                                    'travelagency' => 'Travel Agency',
                                 );
                
                                  $all_automotive_array = array(
                                     'autobodyshop' => 'Auto Body Shop',
                                     'autodealer' => 'Auto Dealer',
                                     'autopartsstore'     => 'Auto Parts Store',
                                     'autorental'     => 'Auto Rental',
                                     'autorepair'      => 'Auto Repair',
                                     'autowash'     => 'Auto Wash',
                                     'gasstation' => 'Gas Station',
                                     'motorcycledealer' => 'Motorcycle Dealer',
                                     'motorcyclerepair' => 'Motorcycle Repair'
                                 );
                                  
                                  $all_emergency_array = array(
                                     'firestation' => 'Fire Station',
                                     'hospital' => 'Hospital',
                                     'policestation'     => 'Police Station',                                    
                                 );
                                  $all_entertainment_array = array(
                                      'adultentertainment' => 'Adult Entertainment',
                                      'amusementpark' => 'Amusement Park',
                                      'artgallery'     => 'Art Gallery',
                                      'casino'     => 'Casino',
                                      'comedyclub'     => 'Comedy Club',
                                      'movietheater'     => 'Movie Theater',
                                      'nightclub'     => 'Night Club',
                                      
                                 );
                                  $all_financial_array = array(
                                      'accountingservice' => 'Accounting Service',
                                      'automatedteller' => 'Automated Teller',
                                      'bankorcredit_union'     => 'Bank Or Credit Union',
                                      'insuranceagency'     => 'Insurance Agency',                                      
                                      
                                 );
                                  
                                  $all_food_establishment_array = array(
                                      'bakery' => 'Bakery',
                                      'barorpub' => 'Bar Or Pub',
                                      'brewery'     => 'Brewery',
                                      'cafeorcoffee_shop'     => 'Cafe Or Coffee Shop', 
                                      'fastfoodrestaurant' => 'Fast Food Restaurant',
                                      'icecreamshop' => 'Ice Cream Shop',
                                      'restaurant'     => 'Restaurant',
                                      'winery'     => 'Winery', 
                                      
                                 );
                                  $all_health_and_beauty_array = array(
                                      'beautysalon' => 'Beauty Salon',
                                      'dayspa' => 'DaySpa',
                                      'hairsalon'     => 'Hair Salon',
                                      'healthclub'     => 'Health Club', 
                                      'nailsalon' => 'Nail Salon',
                                      'tattooparlor' => 'Tattoo Parlor',                                                                          
                                 );
                                  
                                  $all_home_and_construction_array = array(
                                      'electrician' => 'Electrician',
                                      'generalcontractor' => 'General Contractor',
                                      'hvacbusiness'     => 'HVAC Business',
                                      'locksmith'     => 'Locksmith', 
                                      'movingcompany' => 'Moving Company',
                                      'plumber' => 'Plumber',       
                                      'roofingcontractor' => 'Roofing Contractor',       
                                 );
                                  
                                  $all_legal_service_array = array(
                                      'attorney' => 'Attorney',
                                      'notary' => 'Notary',                                            
                                 );
                                  
                                  $all_lodging_array = array(
                                      'bedandbreakfast' => 'Bed And Breakfast',
                                      'campground' => 'Campground',
                                      'hostel' => 'Hostel',
                                      'hotel' => 'Hotel',
                                      'motel' => 'Motel',
                                      'resort' => 'Resort',
                                 );
                                  
                                  $all_sports_activity_location = array(
                                      'bowlingalley' => 'Bowling Alley',
                                      'exercisegym' => 'Exercise Gym',
                                      'golfcourse' => 'Golf Course',
                                      'healthclub' => 'Health Club',
                                      'publicswimming_pool' => 'Public Swimming Pool',
                                      'skiresort' => 'Ski Resort',
                                      'sportsclub' => 'Sports Club',
                                      'stadiumorarena' => 'Stadium Or Arena',
                                      'tenniscomplex' => 'Tennis Complex'
                                 );
                                  $all_store = array(
                                        'autopartsstore'=>'Auto Parts Store',
                                        'bikestore'=>'Bike Store',
                                        'bookstore'=>'Book Store',
                                        'clothingstore'=>'Clothing Store',
                                        'computerstore'=>'Computer Store',
                                        'conveniencestore'=>'Convenience Store',
                                        'departmentstore'=>'Department Store',
                                        'electronicsstore'=>'Electronics Store',
                                        'florist'=>'Florist',
                                        'furniturestore'=>'Furniture Store',
                                        'gardenstore'=>'Garden Store',
                                        'grocerystore'=>'Grocery Store',
                                        'hardwarestore'=>'Hardware Store',
                                        'hobbyshop'=>'Hobby Shop',
                                        'homegoodsstore'=>'HomeGoods Store',
                                        'jewelrystore'=>'Jewelry Store',
                                        'liquorstore'=>'Liquor Store',
                                        'mensclothingstore'=>'Mens Clothing Store',
                                        'mobilephonestore'=>'Mobile Phone Store',
                                        'movierentalstore'=>'Movie Rental Store',
                                        'musicstore'=>'Music Store',
                                        'officeequipmentstore'=>'Office Equipment Store',
                                        'outletstore'=>'Outlet Store',
                                        'pawnshop'=>'Pawn Shop',
                                        'petstore'=>'Pet Store',
                                        'shoestore'=>'Shoe Store',
                                        'sportinggoodsstore'=>'Sporting Goods Store',
                                        'tireshop'=>'Tire Shop',
                                        'toystore'=>'Toy Store',
                                        'wholesalestore'=>'Wholesale Store'
                                 );
                ?>                                               
                <div class="misc-pub-section">
                    <table class="option-table-class saswp-option-table-class">
                        <tr>
                           <td><label for="schema_type"><?php echo esc_html__( 'Schema Type' ,'schema-and-structured-data-for-wp');?></label></td>
                           <td><select class="saswp-schame-type-select" id="schema_type" name="schema_type">
                                <?php
                                  
                                  foreach ($all_schema_array as $key => $value) {
                                    $sel = '';
                                    if($schema_type==$key){
                                      $sel = 'selected';
                                    }
                                    echo "<option value='".esc_attr($key)."' ".esc_attr($sel).">".esc_html__($value, 'schema-and-structured-data-for-wp' )."</option>";
                                  }
                                ?>
                            </select>
                           </td>
                        </tr>    
                        <tr class="saswp-business-type-tr" <?php echo $style_business_type; ?>>
                            <td>
                            <?php echo esc_html__('Business Type', 'schema-and-structured-data-for-wp' ); ?>    
                            </td>
                            <td>
                              <select id="saswp_business_type" name="saswp_business_type">
                                <?php

                                  
                                  foreach ($all_business_type as $key => $value) {
                                    $sel = '';
                                    if($business_type==$key){
                                      $sel = 'selected';
                                    }
                                    echo "<option value='".esc_attr($key)."' ".esc_attr($sel).">".esc_html__($value, 'schema-and-structured-data-for-wp' )."</option>";
                                  }
                                ?>
                            </select>  
                            </td>
                        </tr>
                        <tr class="saswp-automotivebusiness-tr" <?php if(!array_key_exists($business_name, $all_automotive_array)){ echo 'style="display:none;"';}else{ echo $style_business_name;} ?>>
                            <td><?php echo esc_html__('Sub Business Type', 'schema-and-structured-data-for-wp' ); ?></td>
                            <td>
                                <select id="saswp_automotive" name="saswp_business_name">
                                <?php

                                  foreach ($all_automotive_array as $key => $value) {
                                    $sel = '';
                                    if($business_name==$key){
                                      $sel = 'selected';
                                    }
                                    echo "<option value='".esc_attr($key)."' ".esc_attr($sel).">".esc_html__($value, 'schema-and-structured-data-for-wp' )."</option>";
                                  }
                                ?>
                            </select>
                            </td>
                            
                        </tr>
                        <tr class="saswp-emergencyservice-tr" <?php if(!array_key_exists($business_name, $all_emergency_array)){ echo 'style="display:none;"';}else{ echo $style_business_name;} ?>>
                        <td><?php echo esc_html__('Sub Business Type', 'schema-and-structured-data-for-wp' ); ?></td>    
                        <td>
                            <select id="saswp_emergency_service" name="saswp_business_name">
                                <?php

                                  foreach ($all_emergency_array as $key => $value) {
                                    $sel = '';
                                    if($business_name==$key){
                                      $sel = 'selected';
                                    }
                                    echo "<option value='".esc_attr($key)."' ".esc_attr($sel).">".esc_html__($value, 'schema-and-structured-data-for-wp' )."</option>";
                                  }
                                ?>
                            </select>
                        </td>    
                        </tr>
                        <tr class="saswp-entertainmentbusiness-tr" <?php if(!array_key_exists($business_name, $all_entertainment_array)){ echo 'style="display:none;"';}else{ echo $style_business_name;} ?>>
                        <td><?php echo esc_html__('Sub Business Type', 'schema-and-structured-data-for-wp' ); ?></td>    
                        <td>
                            <select id="saswp_entertainment" name="saswp_business_name">
                                <?php

                                  
                                  foreach ($all_entertainment_array as $key => $value) {
                                    $sel = '';
                                    if($business_name==$key){
                                      $sel = 'selected';
                                    }
                                    echo "<option value='".esc_attr($key)."' ".esc_attr($sel).">".esc_html__($value, 'schema-and-structured-data-for-wp' )."</option>";
                                  }
                                ?>
                            </select>
                        </td>    
                        </tr>
                        <tr class="saswp-financialservice-tr" <?php if(!array_key_exists($business_name, $all_financial_array)){ echo 'style="display:none;"';}else{ echo $style_business_name;} ?>>
                        <td><?php echo esc_html__('Sub Business Type', 'schema-and-structured-data-for-wp' ); ?></td>    
                        <td>
                            <select id="saswp_financial_service" name="saswp_business_name">
                                <?php

                                  
                                  foreach ($all_financial_array as $key => $value) {
                                    $sel = '';
                                    if($business_name==$key){
                                      $sel = 'selected';
                                    }
                                    echo "<option value='".esc_attr($key)."' ".esc_attr($sel).">".esc_html__($value, 'schema-and-structured-data-for-wp' )."</option>";
                                  }
                                ?>
                            </select>
                        </td>    
                        </tr>                        
                        <tr class="saswp-foodestablishment-tr" <?php if(!array_key_exists($business_name, $all_food_establishment_array)){ echo 'style="display:none;"';}else{ echo $style_business_name;} ?>>
                        <td><?php echo esc_html__('Sub Business Type', 'schema-and-structured-data-for-wp' ); ?></td>  
                        <td>
                            <select id="saswp_food_establishment" name="saswp_business_name">
                                <?php

                                  foreach ($all_food_establishment_array as $key => $value) {
                                    $sel = '';
                                    if($business_name==$key){
                                      $sel = 'selected';
                                    }
                                    echo "<option value='".esc_attr($key)."' ".esc_attr($sel).">".esc_html__($value, 'schema-and-structured-data-for-wp' )."</option>";
                                  }
                                ?>
                            </select>
                        </td>    
                        </tr>
                        <tr class="saswp-healthandbeautybusiness-tr" <?php if(!array_key_exists($business_name, $all_health_and_beauty_array)){ echo 'style="display:none;"';}else{ echo $style_business_name;} ?>>
                        <td><?php echo esc_html__('Sub Business Type', 'schema-and-structured-data-for-wp' ); ?></td>   
                        <td>
                            <select id="saswp_health_and_beauty" name="saswp_business_name">
                                <?php

                                  
                                  foreach ($all_health_and_beauty_array as $key => $value) {
                                    $sel = '';
                                    if($business_name==$key){
                                      $sel = 'selected';
                                    }
                                    echo "<option value='".esc_attr($key)."' ".esc_attr($sel).">".esc_html__($value, 'schema-and-structured-data-for-wp' )."</option>";
                                  }
                                ?>
                            </select>
                        </td>    
                        </tr>                        
                        <tr class="saswp-homeandconstructionbusiness-tr" <?php if(!array_key_exists($business_name, $all_home_and_construction_array)){ echo 'style="display:none;"';}else{ echo $style_business_name;} ?>>
                        <td><?php echo esc_html__('Sub Business Type', 'schema-and-structured-data-for-wp' ); ?></td>
                        <td>
                            <select id="saswp_home_and_construction" name="saswp_business_name">
                                <?php

                                  foreach ($all_home_and_construction_array as $key => $value) {
                                    $sel = '';
                                    if($business_name==$key){
                                      $sel = 'selected';
                                    }
                                    echo "<option value='".esc_attr($key)."' ".esc_attr($sel).">".esc_html__($value, 'schema-and-structured-data-for-wp' )."</option>";
                                  }
                                ?>
                            </select>
                        </td>    
                        </tr>
                        <tr class="saswp-legalservice-tr" <?php if(!array_key_exists($business_name, $all_legal_service_array)){ echo 'style="display:none;"';}else{ echo $style_business_name;} ?>>
                        <td><?php echo esc_html__('Sub Business Type', 'schema-and-structured-data-for-wp' ); ?></td>
                        <td>
                            <select id="saswp_legal_service" name="saswp_business_name">
                                <?php

                                  foreach ($all_legal_service_array as $key => $value) {
                                    $sel = '';
                                    if($business_name==$key){
                                      $sel = 'selected';
                                    }
                                    echo "<option value='".esc_attr($key)."' ".esc_attr($sel).">".esc_html__($value, 'schema-and-structured-data-for-wp' )."</option>";
                                  }
                                ?>
                            </select>
                        </td>    
                        </tr>
                        <tr class="saswp-lodgingbusiness-tr" <?php if(!array_key_exists($business_name, $all_lodging_array)){ echo 'style="display:none;"';}else{ echo $style_business_name;} ?>>
                        <td><?php echo esc_html__('Sub Business Type', 'schema-and-structured-data-for-wp' ); ?></td>
                        <td>
                            <select id="saswp_lodging" name="saswp_business_name">
                                <?php

                                  foreach ($all_lodging_array as $key => $value) {
                                    $sel = '';
                                    if($business_name==$key){
                                      $sel = 'selected';
                                    }
                                    echo "<option value='".esc_attr($key)."' ".esc_attr($sel).">".esc_html__($value, 'schema-and-structured-data-for-wp' )."</option>";
                                  }
                                ?>
                            </select>
                        </td>    
                        </tr>
                        <tr class="saswp-sportsactivitylocation-tr" <?php if(!array_key_exists($business_name, $all_sports_activity_location)){ echo 'style="display:none;"';}else{ echo $style_business_name;} ?>>
                        <td><?php echo esc_html__('Sub Business Type', 'schema-and-structured-data-for-wp' ); ?></td>
                        <td>
                            <select id="saswp_sports_activity_location" name="saswp_business_name">
                                <?php

                                  foreach ($all_sports_activity_location as $key => $value) {
                                    $sel = '';
                                    if($business_name==$key){
                                      $sel = 'selected';
                                    }
                                    echo "<option value='".esc_attr($key)."' ".esc_attr($sel).">".esc_html__($value, 'schema-and-structured-data-for-wp' )."</option>";
                                  }
                                ?>
                            </select>
                        </td>    
                        </tr>
                        <tr class="saswp-store-tr" <?php if(!array_key_exists($business_name, $all_store)){ echo 'style="display:none;"';}else{ echo $style_business_name;} ?>>
                        <td><?php echo esc_html__('Sub Business Type', 'schema-and-structured-data-for-wp' ); ?></td>    
                        <td>
                            <select id="saswp_store" name="saswp_business_name">
                                <?php

                                  
                                  foreach ($all_store as $key => $value) {
                                    $sel = '';
                                    if($business_name==$key){
                                      $sel = 'selected';
                                    }
                                    echo "<option value='".esc_attr($key)."' ".esc_attr($sel).">".esc_html__($value, 'schema-and-structured-data-for-wp' )."</option>";
                                  }
                                ?>
                            </select>
                        </td>    
                        </tr>
                        <tr class="saswp-business-text-field-tr" <?php echo $style_business_type; ?>>
                            <td><?php echo esc_html__('Business Name', 'schema-and-structured-data-for-wp' ); ?></td>
                            <td><input value="<?php if(isset($business_details['local_business_name'])) { echo esc_attr($business_details['local_business_name']); }  ?>" type="text" name="local_business_name" placeholder="<?php echo esc_html__('Business Name', 'schema-and-structured-data-for-wp' ); ?>"></td>
                        </tr>
                        <tr class="saswp-business-text-field-tr" <?php echo $style_business_type; ?>>
                            <td><?php echo esc_html__('Street Address', 'schema-and-structured-data-for-wp' ); ?></td>
                            <td><input value="<?php if(isset($business_details['local_street_address'])) { echo esc_attr($business_details['local_street_address']); } ?>" type="text" name="local_street_address" placeholder="<?php echo esc_html__('Street Address', 'schema-and-structured-data-for-wp' ); ?>"></td>
                        </tr>
                        <tr class="saswp-business-text-field-tr" <?php echo $style_business_type; ?>>
                            <td><?php echo esc_html__('City', 'schema-and-structured-data-for-wp' ); ?></td>
                            <td><input value="<?php if(isset($business_details['local_city'])){ echo esc_attr($business_details['local_city']);} ?>" type="text" name="local_city" placeholder="<?php echo esc_html__('City', 'schema-and-structured-data-for-wp' ); ?>"></td>
                        </tr>
                        <tr class="saswp-business-text-field-tr" <?php echo $style_business_type; ?>>
                            <td><?php echo esc_html__('State', 'schema-and-structured-data-for-wp' ); ?></td>
                            <td><input value="<?php if(isset($business_details['local_state'])){echo esc_attr($business_details['local_state']);} ?>" type="text" name="local_state" placeholder="<?php echo esc_html__('State', 'schema-and-structured-data-for-wp' ); ?>"></td>
                        </tr>
                        <tr class="saswp-business-text-field-tr" <?php echo $style_business_type; ?>>
                            <td><?php echo esc_html__('Postal Code', 'schema-and-structured-data-for-wp' ); ?></td>
                            <td><input value="<?php if(isset($business_details['local_postal_code'])) {echo esc_attr($business_details['local_postal_code']); } ?>" type="text" name="local_postal_code" placeholder="<?php echo esc_html__('Postal Code', 'schema-and-structured-data-for-wp' ); ?>"></td>
                        </tr>
                        <tr class="saswp-business-text-field-tr" <?php echo $style_business_type; ?>>
                            <td><?php echo esc_html__('Phone', 'schema-and-structured-data-for-wp' ); ?></td>
                            <td><input value="<?php if(isset($business_details['local_phone'])){echo esc_attr($business_details['local_phone']); } ?>" type="text" name="local_phone" placeholder="<?php echo esc_html__('Phone', 'schema-and-structured-data-for-wp' ); ?>"></td>
                        </tr>
                        <tr class="saswp-business-text-field-tr" <?php echo $style_business_type; ?>>
                            <td><?php echo esc_html__('Website', 'schema-and-structured-data-for-wp' ); ?></td>
                            <td><input value="<?php if(isset($business_details['local_website'])){echo esc_attr($business_details['local_website']); }else{ echo site_url();} ?>" type="text" name="local_website" placeholder="<?php echo esc_html__('Website', 'schema-and-structured-data-for-wp' ); ?>"></td>
                        </tr>
                        <tr class="saswp-business-text-field-tr" <?php echo $style_business_type; ?>>
                            <td><?php echo esc_html__('Image', 'schema-and-structured-data-for-wp' ); ?></td>
                            <td style="display: flex; width: 97%">
                                <input value="<?php if(isset($business_details['local_business_logo'])) { echo esc_url($business_details['local_business_logo']['url']);} else { echo esc_url($logo[0]); } ?>" id="local_business_logo" type="text" name="local_business_logo[url]" placeholder="<?php echo esc_html__('Logo', 'schema-and-structured-data-for-wp' ); ?>" readonly="readonly" style="background: #FFF;">
                                <input value="<?php if(isset($business_details['local_business_logo'])) { echo esc_attr($business_details['local_business_logo']['id']);} else { echo esc_attr($custom_logo_id); }?>" data-id="local_business_logo_id" type="hidden" name="local_business_logo[id]">
                                <input value="<?php if(isset($business_details['local_business_logo'])) { echo esc_attr($business_details['local_business_logo']['width']);} else { echo esc_attr($logo[1]); } ?>" data-id="local_business_logo_width" type="hidden" name="local_business_logo[width]">
                                <input value="<?php if(isset($business_details['local_business_logo'])) { echo esc_attr($business_details['local_business_logo']['height']);} else { echo esc_attr($logo[2]); } ?>" data-id="local_business_logo_height" type="hidden" name="local_business_logo[height]">
                                <input data-id="media" class="button" id="local_business_logo_button" type="button" value="Upload"></td>
                        </tr>
                        <tr class="saswp-business-text-field-tr" <?php echo $style_business_type; ?>>
                            <td><?php echo esc_html__('Operation Days', 'schema-and-structured-data-for-wp' ); ?></td>
                            <td>
                                <select multiple id="saswp_dayofweek" name="saswp_dayofweek[]">
                                <?php

                                  $selected_days = $business_details['saswp_dayofweek'];                                 
                                  foreach ($all_dayofweek_array as $key => $value) {
                                    $sel = '';
                                    if(isset($selected_days)){
                                     if(in_array($key, $selected_days)){
                                      $sel = 'selected';
                                    }                                    
                                    echo "<option value='".esc_attr($key)."' ".esc_attr($sel).">".esc_html__($value, 'schema-and-structured-data-for-wp' )."</option>";   
                                    }else{
                                    echo "<option value='".esc_attr($key)."'>".esc_html__($value, 'schema-and-structured-data-for-wp' )."</option>";       
                                    }                                    
                                  }
                                ?>
                               </select>
                            </td>
                        </tr>
                         <tr class="saswp-business-text-field-tr" <?php echo $style_business_type; ?>>
                            <td><?php echo esc_html__('Opens', 'schema-and-structured-data-for-wp' ); ?></td>
                            <td><input id="saswp-dayofweek-opens-time" value="<?php if(isset($business_details['local_opens_time'])){echo esc_attr($business_details['local_opens_time']); } ?>" type="text" name="local_opens_time" ></td>
                        </tr>
                        <tr class="saswp-business-text-field-tr" <?php echo $style_business_type; ?>>
                            <td><?php echo esc_html__('Closes', 'schema-and-structured-data-for-wp' ); ?></td>
                            <td><input id="saswp-dayofweek-closes-time" value="<?php if(isset($business_details['local_closes_time'])){echo esc_attr($business_details['local_closes_time']); } ?>" type="text" name="local_closes_time" ></td>
                        </tr>
                        <tr class="saswp-business-text-field-tr" <?php echo $style_business_type; ?>>
                            <td><?php echo esc_html__('Price Range', 'schema-and-structured-data-for-wp' ); ?></td>
                            <td><input  value="<?php if(isset($business_details['local_price_range'])){echo esc_attr($business_details['local_price_range']); } ?>" type="text" name="local_price_range" placeholder="<?php echo esc_html__('$10-$50 or $$$ ', 'schema-and-structured-data-for-wp' ); ?>" ></td>
                        </tr>
                        <!-- Service Schema type starts here -->
                        
                        <tr class="saswp-service-text-field-tr" <?php echo $style_service_name; ?>>
                            <td><?php echo esc_html__('Name', 'schema-and-structured-data-for-wp' ); ?></td>
                            <td><input  value="<?php if(isset($service_details['saswp_service_schema_name'])){echo esc_attr($service_details['saswp_service_schema_name']); } ?>" type="text" name="saswp_service_schema_name" placeholder="<?php echo esc_html__('Name', 'schema-and-structured-data-for-wp' ); ?>" ></td>
                        </tr>
                        <tr class="saswp-service-text-field-tr" <?php echo $style_service_name; ?>>
                            <td><?php echo esc_html__('Service Type', 'schema-and-structured-data-for-wp' ); ?></td>
                            <td><input  value="<?php if(isset($service_details['saswp_service_schema_type'])){echo esc_attr($service_details['saswp_service_schema_type']); } ?>" type="text" name="saswp_service_schema_type" placeholder="<?php echo esc_html__('Service Type', 'schema-and-structured-data-for-wp' ); ?>" ></td>
                        </tr>
                        <tr class="saswp-service-text-field-tr" <?php echo $style_service_name; ?>>
                            <td><?php echo esc_html__('Provider Name', 'schema-and-structured-data-for-wp' ); ?></td>
                            <td><input  value="<?php if(isset($service_details['saswp_service_schema_provider_name'])){echo esc_attr($service_details['saswp_service_schema_provider_name']); } ?>" type="text" name="saswp_service_schema_provider_name" placeholder="<?php echo esc_html__('Provider Name', 'schema-and-structured-data-for-wp' ); ?>" ></td>
                        </tr>
                        <tr class="saswp-service-text-field-tr" <?php echo $style_service_name; ?>>
                            <td><?php echo esc_html__('Image', 'schema-and-structured-data-for-wp' ); ?></td>
                            <td style="display: flex; width: 97%">
                                <input value="<?php if(isset($service_details['saswp_service_schema_image'])) { echo esc_url($service_details['saswp_service_schema_image']['url']);} else { echo esc_url($logo[0]); } ?>" id="saswp_service_schema_image" type="text" name="saswp_service_schema_image[url]" placeholder="<?php echo esc_html__('Image', 'schema-and-structured-data-for-wp' ); ?>" readonly="readonly" style="background: #FFF;">
                                <input value="<?php if(isset($service_details['saswp_service_schema_image'])) { echo esc_attr($service_details['saswp_service_schema_image']['id']);} else { echo esc_attr($custom_logo_id); }?>" data-id="saswp_service_schema_image_id" type="hidden" name="saswp_service_schema_image[id]">
                                <input value="<?php if(isset($service_details['saswp_service_schema_image'])) { echo esc_attr($service_details['saswp_service_schema_image']['width']);} else { echo esc_attr($logo[1]); } ?>" data-id="saswp_service_schema_image_width" type="hidden" name="saswp_service_schema_image[width]">
                                <input value="<?php if(isset($service_details['saswp_service_schema_image'])) { echo esc_attr($service_details['saswp_service_schema_image']['height']);} else { echo esc_attr($logo[2]); } ?>" data-id="saswp_service_schema_image_height" type="hidden" name="saswp_service_schema_image[height]">
                                <input data-id="media" class="button" id="saswp_service_schema_image_button" type="button" value="Upload"></td>
                        </tr>
                        <tr class="saswp-service-text-field-tr" <?php echo $style_service_name; ?>>
                            <td><?php echo esc_html__('Locality', 'schema-and-structured-data-for-wp' ); ?></td>
                            <td><input  value="<?php if(isset($service_details['saswp_service_schema_locality'])){echo esc_attr($service_details['saswp_service_schema_locality']); } ?>" type="text" name="saswp_service_schema_locality" placeholder="<?php echo esc_html__('Locality', 'schema-and-structured-data-for-wp' ); ?>" ></td>
                        </tr>
                        <tr class="saswp-service-text-field-tr" <?php echo $style_service_name; ?>>
                            <td><?php echo esc_html__('PostalCode', 'schema-and-structured-data-for-wp' ); ?></td>
                            <td><input  value="<?php if(isset($service_details['saswp_service_schema_postal_code'])){echo esc_attr($service_details['saswp_service_schema_postal_code']); } ?>" type="text" name="saswp_service_schema_postal_code" placeholder="<?php echo esc_html__('Postal Code', 'schema-and-structured-data-for-wp' ); ?>" ></td>
                        </tr>
                        <tr class="saswp-service-text-field-tr" <?php echo $style_service_name; ?>>
                            <td><?php echo esc_html__('Telephone', 'schema-and-structured-data-for-wp' ); ?></td>
                            <td><input  value="<?php if(isset($service_details['saswp_service_schema_telephone'])){echo esc_attr($service_details['saswp_service_schema_telephone']); } ?>" type="text" name="saswp_service_schema_telephone" placeholder="<?php echo esc_html__('Telephone', 'schema-and-structured-data-for-wp' ); ?>" ></td>
                        </tr>
                        <tr class="saswp-service-text-field-tr" <?php echo $style_service_name; ?>>
                            <td><?php echo esc_html__('Price Range', 'schema-and-structured-data-for-wp' ); ?></td>
                            <td><input  value="<?php if(isset($service_details['saswp_service_schema_price_range'])){echo esc_attr($service_details['saswp_service_schema_price_range']); } ?>" type="text" name="saswp_service_schema_price_range" placeholder="<?php echo esc_html__('$10-$50 or $$$ ', 'schema-and-structured-data-for-wp' ); ?>" ></td>
                        </tr>
                        <tr class="saswp-service-text-field-tr" <?php echo $style_service_name; ?>>
                            <td><?php echo esc_html__('Description', 'schema-and-structured-data-for-wp' ); ?></td>
                            <td><textarea placeholder="Description" rows="3" cols="70" name="saswp_service_schema_description"><?php if(isset($service_details['saswp_service_schema_description'])){echo esc_attr($service_details['saswp_service_schema_description']); } ?></textarea></td>
                        </tr>
                        <tr class="saswp-service-text-field-tr" <?php echo $style_service_name; ?>>
                            <td><?php echo esc_html__('Area Served (City)', 'schema-and-structured-data-for-wp' ); ?></td>
                            <td><textarea placeholder="New York, Los Angeles" rows="3" cols="70" name="saswp_service_schema_area_served"><?php if(isset($service_details['saswp_service_schema_area_served'])){echo esc_attr($service_details['saswp_service_schema_area_served']); } ?></textarea><p>Note: Enter all the City name in comma separated</p></td>
                        </tr>
                        <tr class="saswp-service-text-field-tr" <?php echo $style_service_name; ?>>
                            <td><?php echo esc_html__('Service Offer', 'schema-and-structured-data-for-wp' ); ?></td>
                            <td><textarea placeholder="Apartment light cleaning, Carpet cleaning" rows="3" cols="70" name="saswp_service_schema_service_offer"><?php if(isset($service_details['saswp_service_schema_service_offer'])){echo esc_attr($service_details['saswp_service_schema_service_offer']); } ?></textarea><p>Note: Enter all the service offer in comma separated</p></td>
                        </tr>
                        
                        <!-- Service Schema type ends here -->
                    </table>  
                   
                </div>
                    <?php
        }
   
        function saswp_schema_type_add_meta_box_save( $post_id ) {     
            
                if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
                if ( ! isset( $_POST['saswp_schema_type_nonce'] ) || ! wp_verify_nonce( $_POST['saswp_schema_type_nonce'], 'saswp_schema_type_nonce' ) ) return;
                if ( ! current_user_can( 'edit_post', $post_id ) ) return;
                                
                if ( isset( $_POST['schema_type'] ) ){
                        update_post_meta( $post_id, 'schema_type', esc_attr( $_POST['schema_type'] ) );
                }
                                
                if ( isset( $_POST['saswp_business_type'] ) ){
                        update_post_meta( $post_id, 'saswp_business_type', esc_attr( $_POST['saswp_business_type'] ) );
                }else{
                        update_post_meta( $post_id, 'saswp_business_type', '' );
                }
                
                if ( isset( $_POST['saswp_business_name'] ) ){
                        update_post_meta( $post_id, 'saswp_business_name', esc_attr( $_POST['saswp_business_name'] ) );
                }else{
                       update_post_meta( $post_id, 'saswp_business_name', '' );
                }
                
                $local_business_details = array();
                if ( isset( $_POST['local_business_name'] ) ){
                $local_business_details['local_business_name'] = $_POST['local_business_name'];        
                }
                if ( isset( $_POST['local_street_address'] ) ){
                $local_business_details['local_street_address'] = $_POST['local_street_address'];        
                }
                if ( isset( $_POST['local_city'] ) ){
                $local_business_details['local_city'] = $_POST['local_city'];        
                }
                if ( isset( $_POST['local_state'] ) ){
                $local_business_details['local_state'] = $_POST['local_state'];        
                }
                if ( isset( $_POST['local_postal_code'] ) ){
                $local_business_details['local_postal_code'] = $_POST['local_postal_code'];        
                }
                if ( isset( $_POST['local_phone'] ) ){
                $local_business_details['local_phone'] = $_POST['local_phone'];        
                }
                if ( isset( $_POST['local_website'] ) ){
                $local_business_details['local_website'] = $_POST['local_website'];        
                }
                if ( isset( $_POST['local_business_logo'] ) ){
                 
                $local_business_details['local_business_logo']['id'] = $_POST['local_business_logo']['id'];    
                $local_business_details['local_business_logo']['url'] = $_POST['local_business_logo']['url'];
                $local_business_details['local_business_logo']['width'] = $_POST['local_business_logo']['width'];
                $local_business_details['local_business_logo']['height'] = $_POST['local_business_logo']['height'];
                }
                if ( isset( $_POST['local_opens_time'] ) ){
                $local_business_details['local_opens_time'] = $_POST['local_opens_time'];        
                }
                if ( isset( $_POST['local_closes_time'] ) ){
                $local_business_details['local_closes_time'] = $_POST['local_closes_time'];        
                }
                if ( isset( $_POST['saswp_dayofweek'] ) ){
                $local_business_details['saswp_dayofweek'] = $_POST['saswp_dayofweek'];        
                }
                if ( isset( $_POST['local_price_range'] ) ){
                $local_business_details['local_price_range'] = $_POST['local_price_range'];        
                }
                              
                update_post_meta( $post_id, 'saswp_local_business_details', $local_business_details );
                
                //Service schema details starts here
                $service_schema_details = array();
                $schema_type = $_POST['schema_type'];               
               
                if($schema_type =='Service'){
                    if ( isset( $_POST['saswp_service_schema_name'] ) ){
                     $service_schema_details['saswp_service_schema_name'] = $_POST['saswp_service_schema_name'];        
                   }
                   if ( isset( $_POST['saswp_service_schema_type'] ) ){
                     $service_schema_details['saswp_service_schema_type'] = $_POST['saswp_service_schema_type'];        
                   }
                   if ( isset( $_POST['saswp_service_schema_provider_name'] ) ){
                     $service_schema_details['saswp_service_schema_provider_name'] = $_POST['saswp_service_schema_provider_name'];        
                   }
                   if ( isset( $_POST['saswp_service_schema_image'] ) ){
                    $service_schema_details['saswp_service_schema_image']['id'] = $_POST['saswp_service_schema_image']['id'];    
                    $service_schema_details['saswp_service_schema_image']['url'] = $_POST['saswp_service_schema_image']['url'];
                    $service_schema_details['saswp_service_schema_image']['width'] = $_POST['saswp_service_schema_image']['width'];
                    $service_schema_details['saswp_service_schema_image']['height'] = $_POST['saswp_service_schema_image']['height'];
                   }
                   if ( isset( $_POST['saswp_service_schema_locality'] ) ){
                     $service_schema_details['saswp_service_schema_locality'] = $_POST['saswp_service_schema_locality'];        
                   }
                   if ( isset( $_POST['saswp_service_schema_postal_code'] ) ){
                     $service_schema_details['saswp_service_schema_postal_code'] = $_POST['saswp_service_schema_postal_code'];        
                   }
                   if ( isset( $_POST['saswp_service_schema_telephone'] ) ){
                     $service_schema_details['saswp_service_schema_telephone'] = $_POST['saswp_service_schema_telephone'];        
                   }
                   if ( isset( $_POST['saswp_service_schema_price_range'] ) ){
                     $service_schema_details['saswp_service_schema_price_range'] = $_POST['saswp_service_schema_price_range'];        
                   }
                   if ( isset( $_POST['saswp_service_schema_description'] ) ){
                     $service_schema_details['saswp_service_schema_description'] = $_POST['saswp_service_schema_description'];        
                   }
                   if ( isset( $_POST['saswp_service_schema_area_served'] ) ){
                     $service_schema_details['saswp_service_schema_area_served'] = $_POST['saswp_service_schema_area_served'];        
                   }
                   if ( isset( $_POST['saswp_service_schema_service_offer'] ) ){
                     $service_schema_details['saswp_service_schema_service_offer'] = $_POST['saswp_service_schema_service_offer'];        
                   }                   
                   update_post_meta( $post_id, 'saswp_service_schema_details', $service_schema_details );
                }
                
                //Service schema details ends here
                
                              
        }           


