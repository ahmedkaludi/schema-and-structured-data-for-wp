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
                $business_name ='';
                $schema_type ='';
                $business_type ='';
                $business_details ='';
                if($post){
                $schema_type      = esc_sql ( get_post_meta($post->ID, 'schema_type', true)  );    
                $business_type    = esc_sql ( get_post_meta($post->ID, 'saswp_business_type', true)  ); 
                $business_name    = esc_sql ( get_post_meta($post->ID, 'saswp_business_name', true)  ); 
                $business_details = esc_sql ( get_post_meta($post->ID, 'saswp_local_business_details', true)  ); 
                
                $custom_logo_id = get_theme_mod( 'custom_logo' );
                $logo = wp_get_attachment_image_src( $custom_logo_id , 'full' );
                
                if($schema_type != 'local_business'){
                 $style_business_type = 'style="display:none"';
                 $style_business_name = 'style="display:none"';
                }
                }   
                        
                                $all_dayofweek_array = array(
                                     'monday' => 'Monday',
                                     'tuesday' => 'Tuesday',
                                     'wednesday'     => 'Wednesday',
                                     'thursday'     => 'Thursday',
                                     'friday'      => 'Friday',
                                     'staturday'     => 'Staturday',  
                                     'sunday'     => 'Sunday',  
                                 );
                
                
                                $all_schema_array = array(
                                     'Blogposting' => 'Blogposting',
                                     'NewsArticle' => 'NewsArticle',
                                     'WebPage'     => 'WebPage',
                                     'Article'     => 'Article',
                                     'Recipe'      => 'Recipe',
                                     'Product'     => 'Product',
                                     'VideoObject' => 'VideoObject',
                                     'local_business' => 'Local Business'
                                 );
                                 $all_business_type = array(
                                    'animalshelter' => 'AnimalShelter',
                                    'automotivebusiness' => 'AutomotiveBusiness',
                                    'childcare' => 'ChildCare',
                                    'dentist' => 'Dentist',
                                    'drycleaningorlaundry' => 'DryCleaningOrLaundry',
                                    'emergencyservice' => 'EmergencyService',
                                    'employmentagency' => 'EmploymentAgency',
                                    'entertainmentbusiness' => 'EntertainmentBusiness',
                                    'financialservice' => 'FinancialService',
                                    'foodestablishment' => 'FoodEstablishment',
                                    'governmentoffice' => 'GovernmentOffice',
                                    'healthandbeautybusiness' => 'HealthAndBeautyBusiness',
                                    'homeandconstructionbusiness' => 'HomeAndConstructionBusiness',
                                    'internetcafe' => 'InternetCafe',
                                    'legalservice' => 'LegalService',
                                    'library' => 'Library',
                                    'lodgingbusiness' => 'LodgingBusiness',
                                    'professionalservice' => 'ProfessionalService',
                                    'radiostation' => 'RadioStation',
                                    'realestateagent' => 'RealEstateAgent',
                                    'recyclingcenter' => 'RecyclingCenter',
                                    'selfstorage' => 'SelfStorage',
                                    'shoppingcenter' => 'ShoppingCenter',
                                    'sportsactivitylocation' => 'SportsActivityLocation',
                                    'store' => 'Store',
                                    'televisionstation' => 'TelevisionStation',
                                    'touristinformationcenter' => 'TouristInformationCenter',
                                    'travelagency' => 'TravelAgency',
                                 );
                
                                  $all_automotive_array = array(
                                     'autobodyshop' => 'AutoBodyShop',
                                     'autodealer' => 'AutoDealer',
                                     'autopartsstore'     => 'AutoPartsStore',
                                     'autorental'     => 'AutoRental',
                                     'autorepair'      => 'AutoRepair',
                                     'autowash'     => 'AutoWash',
                                     'gasstation' => 'GasStation',
                                     'motorcycledealer' => 'MotorcycleDealer',
                                     'motorcyclerepair' => 'MotorcycleRepair'
                                 );
                                  
                                  $all_emergency_array = array(
                                     'firestation' => 'FireStation',
                                     'hospital' => 'Hospital',
                                     'policestation'     => 'PoliceStation',                                    
                                 );
                                  $all_entertainment_array = array(
                                      'adultentertainment' => 'AdultEntertainment',
                                      'amusementpark' => 'AmusementPark',
                                      'artgallery'     => 'ArtGallery',
                                      'casino'     => 'Casino',
                                      'comedyclub'     => 'ComedyClub',
                                      'movietheater'     => 'MovieTheater',
                                      'nightclub'     => 'NightClub',
                                      
                                 );
                                  $all_financial_array = array(
                                      'accountingservice' => 'AccountingService',
                                      'automatedteller' => 'AutomatedTeller',
                                      'bankorcredit_union'     => 'BankOrCreditUnion',
                                      'insuranceagency'     => 'InsuranceAgency',                                      
                                      
                                 );
                                  
                                  $all_food_establishment_array = array(
                                      'bakery' => 'Bakery',
                                      'barorpub' => 'BarOrPub',
                                      'brewery'     => 'Brewery',
                                      'cafeorcoffee_shop'     => 'CafeOrCoffeeShop', 
                                      'fastfoodrestaurant' => 'FastFoodRestaurant',
                                      'icecreamshop' => 'IceCreamShop',
                                      'restaurant'     => 'Restaurant',
                                      'winery'     => 'Winery', 
                                      
                                 );
                                  $all_health_and_beauty_array = array(
                                      'beautysalon' => 'BeautySalon',
                                      'dayspa' => 'DaySpa',
                                      'hairsalon'     => 'HairSalon',
                                      'healthclub'     => 'HealthClub', 
                                      'nailsalon' => 'NailSalon',
                                      'tattooparlor' => 'TattooParlor',                                                                          
                                 );
                                  
                                  $all_home_and_construction_array = array(
                                      'electrician' => 'Electrician',
                                      'generalcontractor' => 'GeneralContractor',
                                      'hvacbusiness'     => 'HVACBusiness',
                                      'locksmith'     => 'Locksmith', 
                                      'movingcompany' => 'MovingCompany',
                                      'plumber' => 'Plumber',       
                                      'roofingcontractor' => 'RoofingContractor',       
                                 );
                                  
                                  $all_legal_service_array = array(
                                      'attorney' => 'Attorney',
                                      'notary' => 'Notary',                                            
                                 );
                                  
                                  $all_lodging_array = array(
                                      'bedandbreakfast' => 'BedAndBreakfast',
                                      'campground' => 'Campground',
                                      'hostel' => 'Hostel',
                                      'hotel' => 'Hotel',
                                      'motel' => 'Motel',
                                      'resort' => 'Resort',
                                 );
                                  
                                  $all_sports_activity_location = array(
                                      'bowlingalley' => 'BowlingAlley',
                                      'exercisegym' => 'ExerciseGym',
                                      'golfcourse' => 'GolfCourse',
                                      'healthclub' => 'HealthClub',
                                      'publicswimming_pool' => 'PublicSwimmingPool',
                                      'skiresort' => 'SkiResort',
                                      'sportsclub' => 'SportsClub',
                                      'stadiumorarena' => 'StadiumOrArena',
                                      'tenniscomplex' => 'TennisComplex'
                                 );
                                  $all_store = array(
                                        'autopartsstore'=>'AutoPartsStore',
                                        'bikestore'=>'BikeStore',
                                        'bookstore'=>'BookStore',
                                        'clothingstore'=>'ClothingStore',
                                        'computerstore'=>'ComputerStore',
                                        'conveniencestore'=>'ConvenienceStore',
                                        'departmentstore'=>'DepartmentStore',
                                        'electronicsstore'=>'ElectronicsStore',
                                        'florist'=>'Florist',
                                        'furniturestore'=>'FurnitureStore',
                                        'gardenstore'=>'GardenStore',
                                        'grocerystore'=>'GroceryStore',
                                        'hardwarestore'=>'HardwareStore',
                                        'hobbyshop'=>'HobbyShop',
                                        'homegoodsstore'=>'HomeGoodsStore',
                                        'jewelrystore'=>'JewelryStore',
                                        'liquorstore'=>'LiquorStore',
                                        'mensclothingstore'=>'MensClothingStore',
                                        'mobilephonestore'=>'MobilePhoneStore',
                                        'movierentalstore'=>'MovieRentalStore',
                                        'musicstore'=>'MusicStore',
                                        'officeequipmentstore'=>'OfficeEquipmentStore',
                                        'outletstore'=>'OutletStore',
                                        'pawnshop'=>'PawnShop',
                                        'petstore'=>'PetStore',
                                        'shoestore'=>'ShoeStore',
                                        'sportinggoodsstore'=>'SportingGoodsStore',
                                        'tireshop'=>'TireShop',
                                        'toystore'=>'ToyStore',
                                        'wholesalestore'=>'WholesaleStore'
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
                            <td><input value="<?php if(isset($business_details['local_business_name'])) { echo $business_details['local_business_name']; }  ?>" type="text" name="local_business_name" placeholder="<?php echo esc_html__('Business Name', 'schema-and-structured-data-for-wp' ); ?>"></td>
                        </tr>
                        <tr class="saswp-business-text-field-tr" <?php echo $style_business_type; ?>>
                            <td><?php echo esc_html__('Street Address', 'schema-and-structured-data-for-wp' ); ?></td>
                            <td><input value="<?php if(isset($business_details['local_street_address'])) { echo $business_details['local_street_address']; } ?>" type="text" name="local_street_address" placeholder="<?php echo esc_html__('Street Address', 'schema-and-structured-data-for-wp' ); ?>"></td>
                        </tr>
                        <tr class="saswp-business-text-field-tr" <?php echo $style_business_type; ?>>
                            <td><?php echo esc_html__('City', 'schema-and-structured-data-for-wp' ); ?></td>
                            <td><input value="<?php if(isset($business_details['local_city'])){ echo $business_details['local_city'];} ?>" type="text" name="local_city" placeholder="<?php echo esc_html__('City', 'schema-and-structured-data-for-wp' ); ?>"></td>
                        </tr>
                        <tr class="saswp-business-text-field-tr" <?php echo $style_business_type; ?>>
                            <td><?php echo esc_html__('State', 'schema-and-structured-data-for-wp' ); ?></td>
                            <td><input value="<?php if(isset($business_details['local_state'])){echo $business_details['local_state'];} ?>" type="text" name="local_state" placeholder="<?php echo esc_html__('State', 'schema-and-structured-data-for-wp' ); ?>"></td>
                        </tr>
                        <tr class="saswp-business-text-field-tr" <?php echo $style_business_type; ?>>
                            <td><?php echo esc_html__('Postal Code', 'schema-and-structured-data-for-wp' ); ?></td>
                            <td><input value="<?php if(isset($business_details['local_postal_code'])) {echo $business_details['local_postal_code']; } ?>" type="text" name="local_postal_code" placeholder="<?php echo esc_html__('Postal Code', 'schema-and-structured-data-for-wp' ); ?>"></td>
                        </tr>
                        <tr class="saswp-business-text-field-tr" <?php echo $style_business_type; ?>>
                            <td><?php echo esc_html__('Phone', 'schema-and-structured-data-for-wp' ); ?></td>
                            <td><input value="<?php if(isset($business_details['local_phone'])){echo $business_details['local_phone']; } ?>" type="text" name="local_phone" placeholder="<?php echo esc_html__('Phone', 'schema-and-structured-data-for-wp' ); ?>"></td>
                        </tr>
                        <tr class="saswp-business-text-field-tr" <?php echo $style_business_type; ?>>
                            <td><?php echo esc_html__('Website', 'schema-and-structured-data-for-wp' ); ?></td>
                            <td><input value="<?php if(isset($business_details['local_website'])){echo $business_details['local_website']; }else{ echo site_url();} ?>" type="text" name="local_website" placeholder="<?php echo esc_html__('Website', 'schema-and-structured-data-for-wp' ); ?>"></td>
                        </tr>
                        <tr class="saswp-business-text-field-tr" <?php echo $style_business_type; ?>>
                            <td><?php echo esc_html__('Logo', 'schema-and-structured-data-for-wp' ); ?></td>
                            <td style="display: flex;">
                                <input value="<?php if(isset($business_details['local_business_logo'])) { echo $business_details['local_business_logo']['url'];} else { echo $logo[0]; } ?>" id="local_business_logo" type="text" name="local_business_logo[url]" placeholder="<?php echo esc_html__('Logo', 'schema-and-structured-data-for-wp' ); ?>" readonly="readonly" style="background: #FFF;">
                                <input value="<?php if(isset($business_details['local_business_logo'])) { echo $business_details['local_business_logo']['id'];} else { echo $custom_logo_id; }?>" data-id="local_business_logo_id" type="hidden" name="local_business_logo[id]">
                                <input value="<?php if(isset($business_details['local_business_logo'])) { echo $business_details['local_business_logo']['width'];} else { echo $logo[1]; } ?>" data-id="local_business_logo_width" type="hidden" name="local_business_logo[width]">
                                <input value="<?php if(isset($business_details['local_business_logo'])) { echo $business_details['local_business_logo']['height'];} else { echo $logo[2]; } ?>" data-id="local_business_logo_height" type="hidden" name="local_business_logo[height]">
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
                                    if(array_search($key, $selected_days)){
                                      $sel = 'selected';
                                    }
                                    echo "<option value='".esc_attr($key)."' ".esc_attr($sel).">".esc_html__($value, 'schema-and-structured-data-for-wp' )."</option>";
                                  }
                                ?>
                               </select>
                            </td>
                        </tr>
                         <tr class="saswp-business-text-field-tr" <?php echo $style_business_type; ?>>
                            <td><?php echo esc_html__('Opens', 'schema-and-structured-data-for-wp' ); ?></td>
                            <td><input id="saswp-dayofweek-opens-time" value="<?php if(isset($business_details['local_opens_time'])){echo $business_details['local_opens_time']; } ?>" type="text" name="local_opens_time" ></td>
                        </tr>
                        <tr class="saswp-business-text-field-tr" <?php echo $style_business_type; ?>>
                            <td><?php echo esc_html__('Closes', 'schema-and-structured-data-for-wp' ); ?></td>
                            <td><input id="saswp-dayofweek-closes-time" value="<?php if(isset($business_details['local_closes_time'])){echo $business_details['local_closes_time']; } ?>" type="text" name="local_closes_time" ></td>
                        </tr>
                        <tr class="saswp-business-text-field-tr" <?php echo $style_business_type; ?>>
                            <td><?php echo esc_html__('Price Range', 'schema-and-structured-data-for-wp' ); ?></td>
                            <td><input  value="<?php if(isset($business_details['local_price_range'])){echo $business_details['local_price_range']; } ?>" type="text" name="local_price_range" placeholder="<?php echo esc_html__('$10-$50 or $$$ ', 'schema-and-structured-data-for-wp' ); ?>" ></td>
                        </tr>
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
                }
                
                if ( isset( $_POST['saswp_business_name'] ) ){
                        update_post_meta( $post_id, 'saswp_business_name', esc_attr( $_POST['saswp_business_name'] ) );
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
                              
        }           


