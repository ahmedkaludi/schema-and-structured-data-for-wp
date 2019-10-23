=== Schema & Structured Data for WP & AMP ===
Contributors: magazine3
Tags: Schema, Structured Data, Google Snippets, Rich Snippets, Schema.org, SEO, AMP
Requires at least: 3.0
Tested up to: 5.2
Stable tag: 1.9.15
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


== Description ==

Schema & Structured Data for WP & AMP adds Google Rich Snippets markup according to Schema.org guidelines to structure your site for SEO. (AMP Compatible) 

### Features 

* <strong>Schema Types</strong>: Currently, We have 33 schema types such as Blog Posting, News article, Local Business, Web page, Article, Recipe, Product, and Video Object [view all](https://structured-data-for-wp.com/docs/article/how-many-schema-types-do-we-support/). We are going to add all the schema types in the future. You can request the one you want and we will add it for you! 
* <strong>Conditional Display Fields</strong>: Meaning you include or exclude any posts, pages, post types, taxonomies and more! 
* <strong>Knowlegde Base Support</strong>: Recognize the content based on the organization or a person via data type option.
* <strong>Full AMP Compatiblity</strong>: Supports the AMP for WP and AMP by Automattic plugins. 
* <strong>Advanced Settings</strong>: Play with output of schema markup using these options (Defragment, Add in Footer, Pretty Print, MicroData CleanUp etc.)
* <strong>Migration</strong>: Import the data from other schema plugins such as (SEO Pressor, WP SEO Schema, Schema Plugin etc )
* <strong>Compatibility</strong>: Generate the schema markup for the plugins. We have provided schema support for them. Few of them are - kk Star Ratings, WP-PostRatings, bbPress
* <strong>Google Review</strong>: Display your business google reviews and its schema markup on your website.
* **[Premium]** Reviews ( [Fetch](https://structured-data-for-wp.com/reviews-for-schema) reviews from 75+ platforms ).
* **[Premium]** Priority Support. [Get it](https://structured-data-for-wp.com/priority-support/) We get more than 100 technical queries a day but the Priority support plan will help you skip that and get the help from a dedicated team.
* <strong>Review Module</strong>: Create your own review rating box with pros and cons and its schema markup
* <strong>Schema Type Blocks in Gutenberg</strong>: Create your own content with the blocks and json schema markup will be added automatically
* <strong>Unlimited Custom Post Types</strong>: You can control to represent the Rich Snippets data in the google search console using unlimited custom post types.
* <strong>Easy to use</strong> with Minimal Settings
* <strong>Archive Page Listing</strong> Support 
* <strong>JSON-LD</strong> Format
* <strong>Easy to use</strong> Setup Wizard
* <strong>Breadcrumbs</strong> Listing Support
* <strong>Comments</strong> Post comments Support
* <strong>Constant Development & New Features</strong>: We’ll be releasing the constant updates along with the more handy features as soon as we get the feedback from the users.


### Supported Schema Types

* Apartment
* House
* SingleFamilyResidence
* Article
* Blogposting
* Book
* Course
* DiscussionForumPosting,
* DataFeed
* HowTo
* NewsArticle
* QAPage
* Review
* Recipe
* TVSeries
* SoftwareApplication
* TechArticle
* WebPage
* Event
* VideoGame
* JobPosting
* Service
* Trip
* AudioObject
* VideoObject
* MedicalCondition
* MusicPlaylist
* MusicAlbum
* LocalBusiness with all the sub categories
* Product
* TouristAttraction
* TouristDestination
* LandmarksOrHistoricalBuildings
* HinduTemple
* Church
* Mosque
* Person
* [View All](https://structured-data-for-wp.com/docs/article/how-many-schema-types-do-we-support/)

### Extensions

Some useful extensions to extend Schema & Structured Data for WP & AMP features, check [Woocommerce Compatibility For Schema](https://structured-data-for-wp.com/extensions/woocommerce-compatibility-for-schema/), [Cooked Compatibility For Schema](https://structured-data-for-wp.com/extensions/cooked-compatibility-for-schema/) and We are going to add more.

### Support

We try our best to provide support on WordPress.org forums. However, We have a special [team support](https://structured-data-for-wp.com/contact-us/) where you can ask us questions and get help. Delivering a good user experience means a lot to us and so we try our best to reply each and every question that gets asked.

### Bug Reports

Bug reports for Schema & Structured Data for WP & AMP are [welcomed on GitHub](https://github.com/ahmedkaludi/schema-and-structured-data-for-wp/issues). Please note GitHub is not a support forum, and issues that aren't properly qualified as bugs will be closed.

### Credits

* Merlin WP used https://github.com/richtabor/MerlinWP - License URI: https://github.com/richtabor/MerlinWP/blob/master/LICENSE,
* jquery-timepicker used https://github.com/jonthornton/jquery-timepicker
* Rate Yo! used https://github.com/prrashi/rateYo - License URI: https://github.com/prrashi/rateYo/commit/f3812fe96c38b08627d209795176053550fb1427
* Aqua Resizer used http://aquagraphite.com - License URI: WTFPL - http://sam.zoy.org/wtfpl/



== Frequently Asked Questions ==

= How to install and use this Schema plugin? =

After you Active this plugin, just go to Dashboard > Structured data > Settings, and then setup the default settings, after that, just go back to 'Structured Data' click on Add New and add any data that you like!  

= How do I report bugs and suggest new features? =

You can report the bugs for this Schema plugin [here](https://github.com/ahmedkaludi/schema-and-structured-data-for-wp/issues)

= Will you include features to my request? =

Yes, Absolutely! We would suggest you send your feature request by creating an issue in [Github](https://github.com/ahmedkaludi/schema-and-structured-data-for-wp/issues/new/) . It helps us organize the feedback easily.


= How do I get in touch? =
You can contact us from [here](http://structured-data-for-wp.com/contact-us/)

== Changelog ==

= 1.9.15 (23 Oct 2019) =

* Fixed: Multiple Instances of Schema Type (i.e. LocalBusiness) Merging Improperly on Same Page #537
* Fixed: Price not showing in product schema for variable product #530
* Fixed: Published and modified date needs to correct with proper timezone #562
* Fixed: ImageObject Error when using the enfold theme. #558
* Enhancement: Added default author value in review array for anonymous user #557
* Enhancement: Added two section in compatiblity tab (Active & InActive) #560
* Added: Recipe Schema Box in Premium Features tab #561

= 1.9.14 (18 Oct 2019) =

* Fixed: Google icon is not visible in schema review #554
* Fixed: Cannot redeclare aq_resize() #555
* Fixed: Fatal Error: Can't use method return value in right context #550
* Fixed: HTML tags are not being saved in "Add Custom Schema" #544
* Fixed: servesCuisine field is missing in the Local Business schema type. #545
* Fixed: Type property is missing for Location object #552
* Added: numberOfRooms property in House Schema type #547
* Added: Event Schema Box in Premium Features tab #540

= 1.9.13 (14 Oct 2019) =

* Fixed: Trying to get property ‘ID’ of non-object #543
* Fixed: Fix Service Schema "Provider Type" Output #536
* Added: Course Schema Box in Premium Features tab #540

= 1.9.12 (9 Oct 2019) =

* Added: Custom Taxonomy in modify schema output #533
* Added: Coordinates fields(latitude, longitude) for real estate schema #525
* Added: Real Estate Schema Box in Premium Features tab #472
* Enhancement: Review for Schema improvements #534

= 1.9.11 (5 Oct 2019) =

* Added: Add support for Event Subcategory schema #418
* Fixed: Additional CSS not working on FAQ blocks #529
* Fixed: HowTo Block time is not being added in json markup when days and hours are empty #531
* Fixed: Improved the db query #493
* Fixed: Error and performance issues #497
* Fixed: Review Link is not working properly #510
* Fixed: Bug in defragmented schema ( Corporation being invalid for Publisher ) #505

= 1.9.10 (3 Oct 2019) =

* Added: Field to enter Yelp link to Knowledge Graph Social Fields section #517
* Fixed: Breadcrumb list error on custom taxonomy #523
* Fixed: ItemList and CollectionPage schema are not being added on custom texonomy page #521

= 1.9.9 (28 Sept 2019) =

* Added: Post category in modify meta list. #509
* Fixed: Setup Wizard is very slow. Need to improve the timing. #518
* Fixed: Image is not being pre filled or populated on post specific modify schema #508
* Fixed: KK Star is not working anymore #506
* Fixed: The property publisher is not recognized by Google for an object of type LocalBusiness. #512


= 1.9.8 (25 Sept 2019) =

* Added: The Supply and Tool for the How To Schema of Gutenberg #500
* Added: MusicPlaylist Schema Type #488
* Added: MusicAlbum Schema Type #488
* Added: Book Schema Type #488
* Added: RTL support (To fit the design with other language which start with right to left) #491
* Enhancement: In product schema add the description to the Short+Long description #477
* Bug Fixed: Added core jquery dependencies whereever js is being enqued #494
* Bug Fixed: Image size smaller than recommended size #485
* Bug Fixed: Modify Schema issue #498
* Bug Fixed: Previously set schema markups "stick" on the website by default. #475
* Bug Fixed: Review CSS are not loading based on condition #492
* Bug Fixed: Update the Reviews markup as per the new Google Update #484
* Bug Fixed: Google reviews not appearing properly on website (Design improvements) #387
* Bug Fixed: Review schema and webpage given an error "Thing is not a known valid target type for the itemReviewed property." #488
* Bug Fixed: Featured image should be placed first in schema markup before the other images present in the content #496


= 1.9.7 (19 Sept 2019) =

* Major Feature: Schema type blocks (How To & FAQ) have been added in Gutenberg editor
* Added: Schema markup to category of WooCommerce #405
* Added: Option to remove data on uninstall in Advanced tab #468
* Added: Priority support in the Services section in the first place #346
* Bug Fixed: "[POST, PAGE] Specific Schema" options don't show up in /wp-admin/post-new.php #409
* Bug Fixed: Call to undefined function get_avatar_data #480
* Bug Fixed: Need to show the modify schema list based on target location of schema type #483
* Bug Fixed: Fatal error function bcdiv () #489


= 1.9.6 (11 Sept 2019) =

* Enhancement: Remove all the global static fields and migrate its data to modify schema output #465
* Enhancement: Other images should also get resized as like the featured image #371
* Bug Fixed: Website layout is break when microdata clean up option enable #461
* Bug Fixed: Post specific modify schema option should also show when a post is drafted #335
* Bug Fixed: NOTICE: PHP message: PHP Fatal error ( Call to undefined method WP_Image_Editor_Imagick::get_error_message() ) #467
* Bug Fixed: Default product data is not loading in fields of product at Modify current schema #432
* Bug Fixed: When WordPress installed in sub folder, Schema takes the path of subfolder instead of root #406
* Bug Fixed: AMP should be added if it is amp url in mainEntityOfPage #392
* Bug Fixed: WP Recipe Maker (Compatibility), the image is smaller than the recommended size error should not get appeared. #382
* Added: Importer for Schema- AIORS ( https://wordpress.org/plugins/all-in-one-schemaorg-rich-snippets) #160

= 1.9.5 (06 Sept 2019) =

* Added: More Organization schema type support #423
* Added: MedicalBusiness Schema type #334
* Added: Carousels (itemList) Schema type on archive post/page #306
* Added: Blog Schema type on archive post/page #460
* Added: Manual Reviews fields in the product schema #137 
* Added: Aqua Resizer to fix image size is smaller than recommended in google console #435
* Enhancement: Toggle (on/off) button should be on by default on schema post specific modification #457
* Bug Fixed: Error log cluttered with php warning regarding count() / PART II #444
* Bug Fixed: Debug error logs: flexmlsConnectPageCore->__construct(NULL) #446
* Bug Fixed: In the FAQ schema fields, accepted textarea strip html tags and convert special character to encoded string. #458


= 1.9.4 (03 Sept 2019) =

* Added: DataFeed Schema type #411
* Added: Option to add multiple performer in event Schema type #411
* Enhancement: Remove button to modified Schema Output item #456.
* Bug Fixed: Compatibility code for The SEO Framework results in invalid output. #452
* Bug Fixed: Custom fields for products has the wrong identifier. #453
* Bug Fixed: Custom fields does not fetch post values #454
* Bug Fixed: Add field in Modify Schema Output defaults to null #455
* Bug Fixed: @context property value should be changed based on site ssl certificate #447


= 1.9.3 (30 Aug 2019) =

* New Feature: ACF meta fields inside Modify Schema Output #216
* Added: Compatibility with Squirrly Seo ( https://wordpress.org/plugins/squirrly-seo/ ) #426
* Added: Compatibility with WP Recipe maker ( https://wordpress.org/plugins/wp-recipe-maker/ ) #323
* Added: bbPress support with "Q&A Schema" #391
* Added: WPSSO Core importer #85
* Bug Fixed: Upgrade to pro message should not be there, if any pro extension is activated #450
* Bug Fixed: Notice: Undefined index: professionalservice in /schema-and-structured-data-for-wp/output/output.php on line 3731 #448

= 1.9.2 (28 Aug 2019) =

* Added: Compatibility with SEO Framework ( https://wordpress.org/plugins/autodescription/ ) #426
* Added: Compatibility with SEOPress ( https://wordpress.org/plugins/wp-seopress/ ) #421
* Added: Compatibility with Smartcrawl SEO ( https://wordpress.org/plugins/smartcrawl-seo/ ) #319
* Added: Compatibility with All in One SEO Pack ( https://wordpress.org/plugins/all-in-one-seo-pack/ ) #383
* Bug Fixed: @type ImageObject and VideoObject are getting created eventhough values are not present on the site #437
* Bug Fixed: Post specific schema fields are not showing on first button click inside custom post types #424
* Bug Fixed: Fatal Error ( Cannot access protected property saswp_post_specific::$_local_sub_business ) #443
* Bug Fixed: HowTo Schema (If steps images are not there its type should not come in json markup). #442
* Bug Fixed: Missing datePublished and mainEntityOfPage fields in DiscussionForumPosting schema #438


= 1.9.1 (21 Aug 2019) =

* Major Feature: Option to add schema fields value from give dropdown meta list inside Modify Schema Output section #192
* Bug Fixed: Error log cluttered with php warning regarding count() #434
* Bug Fixed: Debug error #439
* Bug Fixed: Php json_encode function does not encode other languages characters, replace it with wp_json_encode #433
* Bug Fixed: CSS border-box rule causing issues #420
* Bug Fixed: Setup link of default image doesn't take it anywhere #429
* Bug Fixed: Conflict with Gutenberg and Merlin. Fatal error: Uncaught Error: Call to a member function is_block_editor() on null #413

= 1.9 (03 Aug 2019) =

* Major Feature: Reviews Module :- Allow users to manually add reviews from more than 80+ platforms and show it on the website with schema markup #325
* Added: FAQ schema type #402
* Added: Upgrade to premium as a menu item in the SD #343
* Enhancement: If the plguins which are in compatibility section are active than respective checkbox should be checked #353
* Enhancement: Added Post Tags inside schema markup (Urgent) #389
* Enhancement: Sub business type should have "LocalBusiness" type as well #380
* Enhancement: Added validation message in default data #369
* Enhancement: Added validation message in phone number #369
* Enhancement: Added some more fields in the product schema #360
* Bug Fixed: Error in Breadcrumb when using custom post type #403
* Bug Fixed: Automatically smaller titles which is created as a validation error of "Headline String Too Long" in NewsArticle #396
* Bug Fixed: Do not need to add organization schema markup on every page #395
* Bug Fixed: If Defragmentation is enabled "Site Navigation Element" should be included in main schema #386
* Bug Fixed: Remove double slash // in the type id #379
* Bug Fixed: Not compatible with Orbital theme #385
* Bug Fixed: Any AMP extensions redux settings are not being displayed when saswp is installed #412

= 1.8.9 (08 July 2019) =

* Added: Translation panel. User can add own text for List of labels which is being output in content #361
* Added: compatibility with DWQA Pro version plugin ( https://wordpress.org/plugins/dw-question-answer ) #372
* Added: Social fields in local business schema type
* Added: Error message should be shown if custom schema markup is not valid
* Bug Fixed: Defragmentation is not working for BlogPost schema type #367
* Bug Fixed: Schema title attributes show invalid title when yoast compatibility is enabled , like this (%%title%%%page%%sep) #364
* Bug Fixed: Description and Article body have same data #363
* Bug Fixed: Debug Warning & notices #362
* Bug Fixed: Micro data clean up is not working properly. #359

= 1.8.8 (22 June 2019) =
* Bug Fixed: Uncaught Error: Class 'saswp_google_review' not found. #351

= 1.8.7 (21 June 2019) =

* Bug Fixed: Fatal error ( Uncaught Error: Cannot use object of type Closure as array ) #317
* Bug Fixed: Call to undefined function ampforwp_is_home() #330
* Bug Fixed: Micro Data cleanup properly does not work #337
* Bug Fixed: View Post option should not be there in schema post type admin menu bar #327
* Bug Fixed: count(): Parameter must be an array or an object that implements Countable in #309
* Improvement: If Yoast plugin is active then the checkbox should be on by default in the compatibility section #338
* Improvement: Change message "AMP Requires Selected Plugin" in AMP tab #328
* Added: Aggregate rating fields in Recipe Schema type #331


= 1.8.6 (07 June 2019) =

* Bug Fixed: Notice: Undefined variable: custom_markup #308
* Bug Fixed: JS and CSS should be included where it's need. #294
* Bug Fixed: Remove duplicate queries #296
* Bug Fixed: Google Review Module ( update_post_meta was called earlier than its actually call ) #307
* Bug Fixed: When extensions are active then it should show #313
* Bug Fixed: Compatibility conflicts with WooCommerce SEO #312
* Bug Fixed: '@type' => 'VideoObject' is missing in recipe schema video entity #314
* Bug Fixed: Navigation Menu should have Assigned location name #310


= 1.8.5 (28 May 2019) =

* Added: Person schema type #220
* Added: Trip schema type #289
* Added: JobPosting schema type #289
* Added: Author fields should have description field #275
* Added: Homepage selection in the "Placement" dropdown #280
* Bug Fixed: Remove shortcode and share button text from schema markup description #250
* Bug Fixed: Modify schema does not take default description #295
* Bug Fixed: saswp-style.css should only load wherever its need #287
* Bug Fixed: Unspecified Type error when defragmentation is enabled and website schema is disabled #291
* Improvement: getimagesize function improvement #278
* Improvement: Site Navigation Menu option should have list of menus to be added in schema markup #272
* Improvement: If Yoast compatibility is on make sure, We skip the schema of the Yoast, not the whole metadata and og tags #288
* Improvement: If target location is empty than by default target location should be post #292
* Improvement: An option for adding own custom json schema markup on every post along with current schema #274


= 1.8.4 (22 May 2019) =

* Added: Accomodation Schema type ( House, Apartment and SingleFamilyResidence )#41
* Added: Speakable Schema type #79
* Added: HowTo Schema type #268
* Added: TvSeries Schema type #108
* Added: VideoGame Schema type #77
* Added: MedicalCondition Schema type #58
* Added: TouristAttraction Schema type #167
* Added: TouristDestination Schema type #167
* Added: PlaceOfWorship Schema type ( HinduTemple, Church and Mosque ) #167
* Bug Fixed: [BUG REPORT] Sorry, you are not allowed to access this page ( Multisite Issue ) #286
* Bug Fixed: Warning: count() ( Parameter must be an array or an object that implements Countable ) #273
* Bug Fixed: About and Contact page dropdown not appearing on setup wizard ( Multisite Issue ) #282
* Bug Fixed: PHP Error: Undefined offset #285
* Bug Fixed: Basic issues ( Image icon should come from plugin own directory and If the logo is not there, then it should show logo mission error ) #283
* Improvement: Product select should not have the fields on installer #266
* Improvement: HasMap field should be there in local schema #276


= 1.8.3 (16 May 2019) =

* Bug Fixed: Warning: getimagesize #271


= 1.8.2 (13 May 2019) =

* Bug Fixed: Warning: getimagesize #271


= 1.8.1 (13 May 2019) =

* New Feature: An option for adding own custom json schema markup on every post #257
* Added: Compatibility with WP-PostRatings ( https://wordpress.org/plugins/wp-postratings/ )#208
* Added: Compatibility with bbPress ( https://wordpress.org/plugins/bbpress/ )#208
* Added: Option to minified or pretty print schema markup #166
* Added: To show all images of the article in the structured data. #84
* Added: Geo Location (latitude and longitude) fields in local schema #90
* Added: Micro data cleanup option (This option cleans all the micro data on page to valid the json markup) #262
* Added: Image preview panel on every images, When these are added. #249
* Bug Fixed: Content url and Embed url are missing in video object of structured data #190
* Bug Fixed: Remove unnecessary css which effect on whole wordpress admin panel #264
* Bug Fixed: If porto theme is activated, there are validation issues in product schema type. #163


= 1.8 (08 May 2019) =

* New Feature: Google Reviews and its schema markup (Users can list their business reviews from google reviews using place id)  #61
* Added: New docs link in the plugin and everywhere #202
* Added: Review Module Schema markup has been added #205
* Added: Defragment Schema (Merge all the schema markup in one markup) #223
* Added: "Need Help" label in default data
* Added: Option to to add schema markup in (header or footer) #165
* Changes: PRE Release Checklist (Settings panel has many improvement with new tabs) #236
* Bug Fixed: Missing Field “mainEntity.author in Q&A schema after Update #191
* Bug Fixed: The property aggregateRating is not recognised by Google for an object of type Thing in course schema type #194
* Bug Fixed: The description is required for VideoObject schema type on homepage #195
* Bug Fixed: Review Module Schema is not generating in the backend #205
* Bug Fixed: Missing attributes have been added for the product schema generating from WooCommerce #205
* Bug Fixed: Error in Article schema automatically generate by Yoast SEO in non-amp #231
* Bug Fixed: Issue in the selecting the organisation type as twice, generating an error. #234
* Bug Fixed: Debug errors after Version 1.7 #240
* Bug Fixed: In AMP's Home page Breadcrumb scheme markup is generating two item list #244
* Bug Fixed: Remove shortcode from schema markup description #250
* Bug Fixed: Notification Improvements #239
* Bug Fixed: The Support tab Email changes #238


= 1.7 (24 April 2019) =

* Added: Option to enable and disable website schema markup #225
* Added: Option to enable and disable Sitelinks Search Box in website schema markup #196
* Bug Fixed: Event Time and date is not changing as per the option #227
* Bug Fixed: Two images url are null from three different image sizes in amp article schema #222
* Bug Fixed: number_format() Debug error #213
* Bug Fixed: Undefined offset errors #214, #232, #229


= 1.6 (12 April 2019) =

* Added : Option to use URL rather than phone number for contact type in Knowledge Graph #199
* Bug Fixed: Image markup src remains null in AMP despite featured images on post #206
* Bug Fixed: Array (The value provided for image must be a valid URL.) #203
* Bug Fixed: After removing social links from the knowledge graph it still shows up in the markup #200
* Bug Fixed: If schema is not enabled on amp, it still adds blank script #189
* Bug Fixed: Modify schema output for the review schema has no author fields in dropdown #193
* Bug Fixed: Notice: Undefined offset #197
* Bug Fixed: Undefined index #204


= 1.5 (27 March 2019) =

* Bug Fixed: Warning - Missing "review" field in product schema for WooCommerce product page. #179
* Bug Fixed: Modify schema output with custom filters #178
* Bug Fixed: https://schema.org/Publisher should be https://schema.org/publisher #175
* Bug Fixed: In AMP schema markup featured image should be 4x3,16x9,1x1 #173
* Bug Fixed: Warning - number_format() expects parameter 1 to be float, string given in  #172
* Bug Fixed: Set-Up wizard is not working when Polylang plugin is activated #170

= 1.4 (26 March 2019) =

* Bug Fixed: Security improvement.

= 1.3.1 (12 March 2019) =

* Bug Fixed: Organization schema type has null value

= 1.3 (11 March 2019) =

* Added: Event schema type added to the schema type list #157
* Added: Compatibility with The Events Calendar plugin ( https://wordpress.org/plugins/the-events-calendar/ ) has been added in AMP. #157
* Added: New Organization type list has been added to knowledge graph section including "NewsMediaOrganization". #147
* Added: Compatibility with ( Tagyeem Review Plugin & Jannah News Theme ). #151
* Bug Fixed: ItemAvailability text "onbackorder" should be converted to "PreOrder" for product schema #161
* Bug Fixed: Bad escape sequence in string. #168

= 1.2 (22 February 2019) =

* New Feature: Software Application and Course schema type added to the schema type list #115
* Bug Fixed: Modify Schema Output is not getting, Selected post meta fields value in schema markup #155
* Bug Fixed: Author name field value is not being filled in the Review schema markup #154
* Bug Fixed: Notice cluttering in error logs #152

= 1.1 (15 February 2019) =

* New Feature: TechArticle schema type add to the schema type list #115
* Added: Show admin notice, when schema's default image is not set inside setting #145
* Bug Fixed: Schema markup is not getting author name from the post #146
* Bug Fixed: When Review and Article schema are enabled at the same time. There is a validation error in logo markup #141
* Bug Fixed: Image is not being fetched in local_business schema markup from local schema image field #135


= 1.0.9 (07 February 2019) =

* New Feature: SiteNavigationElement schema type add to the schema type list #115
* Added: Option to edit the Schema type for Archive page #123
* Added: Menu Property to FoodEstablishment #116
* Bug Fixed: On/Off buttons inside modify schema are not changing its state on post save #138
* Bug Fixed: Yoast compatibility checkbox is not active when yoast premium plugin is activated #138


= 1.0.8.1 (31 January 2019) =

* Bug Fixed: JSON-LD Error, when yoast compatibility is enabled #133


= 1.0.8 (28 January 2019) =

* Added: Yoast plugin compatibility ( If yoast is activated for schema. Organization & website markup should not be duplicate )
* Improvement: Show the fields of review schema type according to selected Item Reviewed Type
* Bug Fixed: Product schema is not getting feature image/product image when WooCommerce compatibility is enable #122
* Bug Fixed: Missing escaping, Warnings and Notices fixed
* Bug Fixed: Google is requiring 1200 pixel wide feature images #131


= 1.0.7.1 (5 January 2019) =

* Improvement: AMP tab should always be shown and amp option should be disabled if amp is not activated. #87
* Improvement: Notice box to ask for review in day interval should not be shown again, if users click no thanks button. #118
* Bug Fixed: Warnings and Notices fixed


= 1.0.7 (31 December 2018) =

* New Feature: AudioObject schema type add to the schema type list #115
* New Feature: Option to reset plugin's settings #104
* New Feature: Fields to enter Aggregate Rating below schema type #95
* New Feature: SEO Pressor plugin importer #93
* Improvement: Changed default "Homepage" to site name in Breadcrumbs. #87
* Improvement: Product fields have been added when selecting product schema type #94
* Improvement: Added provider type in service schema #103
* Improvement: On/Off button has been added to post specific schema list #99
* Improvement: List of compatible plugins should always be shown in compatibility tab  #102
* Improvement: Inside review schema type, item reviewed should be in dropdown #97
* Improvement: Moved FlexMLS IDX compatibilty from tool tab to compatibility tab #112
* Improvement: Properly prepared for localization to make plugin translatable #105
* Bug Fixed: Modify Schema : - In Local Business schema type phone number is not being added in schema markup output  #114
* Bug Fixed: Local Business -> Food Establishment -> Bakery is buggy #111
* Bug Fixed: select 2 conflicts with avada theme #92
* Bug Fixed: warning issues on product/apartment schema #88


* Bug Fixed: Other bug fixed

= 1.0.6.1 (22 December 2018) =

* Bug Fixed: Errors with WP5.2 #107
* Bug Fixed: Blank recipe post screen #91
* Bug Fixed: Other bug fixed

= 1.0.6 (14 December 2018) =

* New Feature: Review schema type add to the schema type list
* New Feature: Compatibility with Extra theme by Elegant Themes ( https://www.elegantthemes.com ) , Now extra theme built in review and rating will be indexed in google after enabling option
* New Feature: Compatibility with WooCommerce ( https://wordpress.org/plugins/woocommerce ), Now the product schema with its WooCommerce product details will be indexed in google search
* New Feature: Q&A schema type add to the schema type list, Currently it is compatible with DW Question & Answer plugin ( https://wordpress.org/plugins/dw-question-answer ) 
* New Feature: Modify Schema output, custom fields can be selected for schema fields
* Improvement: Different opening and closing hours for different days of the week in local business schema type
* Improvement: Now post specific modify schema will support on page and custom post type
* Improvement: Added missing recipe fields in the post specific metabox #81
* Bug Fixed: Compatibility issue with the Blackbar plugin #83
* Bug Fixed: Restore schema button was not working on first load #81


= 1.0.5 (30 November 2018) =

* New Feature: Service schema type add to the schema type list
* New Feature: Comments Markup, The comments on post will also appear in schema markup
* New Feature: WP SEO Structured Data Schema migration tool 
* New Feature: Compatibility with kk Star Ratings plugin, Now the ratings will be indexed in google search and results will be appearing in the form of a rich snippet
* Bug Fixed: Some of the missing fields added in NewsArticle schema type markup( articleSection, articleBody, wordCount & timeRequired ).

= 1.0.4.1 (17 November 2018) =

* Bug Fixed: Load review css only when review is enable for that post. #67
* Menified review css in amp to put under amp css limitation. 

= 1.0.4 (16 November 2018) =
* Major Feature: Post Specific Meta boxes to override the schemas on posts
* Major Feature: Rich Snippets for Reviews and Ratings
* Security and other bug fixed

= 1.0.3 (24 October 2018) =
* Schema Pro migrator
* Import / export functionality, so it can be transferred from staging to live website
* Added WP Recipe Maker and WP Ultimate Recipe plugins compatibility
* Added compatibility with FlexMLS® IDX plugin
* Tested security issue and fixed
* Breadcrumbs bug fixed
* Debug errors
* Minor Bugs Fixed

= 1.0.2 (28 August 2018) =
* First Time Installation Setup Wizard
* Added a Support form to provide faster support
* Full Local Business Schema Markup Support
* Schema Press Migrator - You can easy switch from Schema Press plugin with just one-click.
* Moved the options panel to the bottom for better UX
* Asks for review after a week.
* Shows Schema type next to the name of the post
* Knowledge Graph typo
* Debug errors
* Date format has been fixed via PR. Thanks to @thetoine
* minor Bugs Fixed

= 1.0.1 (27 August 2018) =
* AMP Compatibility improved for Schema 
* Default Schema compatibility added, which means the posts and pages will be set by default. 
* Minor bugs fixed  

= 1.0 (6 August 2018) =
* Version 1.0 Released