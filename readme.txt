=== Schema & Structured Data for WP & AMP ===
Contributors: magazine3
Tags: Schema, Structured Data, Google Snippets, Rich Snippets, Schema.org, SEO, AMP
Requires at least: 3.0
Tested up to: 5.2
Stable tag: 1.8.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


== Description ==
Schema & Structured Data adds Google Rich Snippets markup according to Schema.org guidelines to structure your site for SEO. (AMP Compatible) 

= Features =

* <strong>Schema Types</strong>: are divided into 8 types such as Blog Posting, News article, Local Business, Web page, Article, Recipe, Product, and Video Object. We are going to add all the schema types in the future. You can request the one you want and we will add it for you! 
* <strong>Conditional Display Feilds</strong>: Meaning you include or exclude any posts, pages, post types, taxonomies and more! 
* <strong>Knowlegde Base Support</strong>: Recognize the content based on the organization or a person via data type option.
* <strong>Full AMP Compatiblity</strong>: Supports the AMP for WP and AMP by Automattic plugins.  
* <strong>Unlimited Custom Post Types</strong>: You can control to represent the Rich Snippets data in the google search console using unlimited custom post types.
* <strong>Easy to use</strong> with Minimal Settings
* <strong>Archive Page Listing</strong> Support 
* <strong>JSON-LD</strong> Format
* <strong>Easy to use</strong> Setup Wizard
* <strong>Breadcrumbs</strong> Listing Support
* <strong>Constant Development & New Features</strong>: We’ll be releasing the constant updates along with the more handy features as soon as we get the feedback from the users.
* <strong>Constant Development & New Features</strong>: We’ll be releasing the constant updates along with the more handy features as soon as we get the feedback from the users.

= Supported Schema & Structured Data Types: =
* LocalBusiness Schema with all the sub categories
* BlogPosting Schema
* News Article Schema
* WebPage Schema
* NewsArticle Schema
* Recipe Schema
* Product Schema
* VideoObject Schema

**We Act Fast on Feedback!**
We are actively developing this plugin and our aim is to make this plugin into the #1 solution for Schema and Google Structured Data in the world. You can [Request a Feature](https://github.com/ahmedkaludi/schema-and-structured-data-for-wp/issues) or [Report a Bug](http://magazine3.company/contact/).

**Technical Support**
Support is provided in [Forum](https://wordpress.org/support/plugin/schema-and-structured-data-for-wp). You can also [Contact us](http://magazine3.company/contact/), our turn around time on email is around 12 hours. 

**Would you like to contribute?**
You may now contribute to this Schema plugin on Github: [View repository](https://github.com/ahmedkaludi/schema-and-structured-data-for-wp) on Github

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