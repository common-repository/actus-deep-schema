=== ACTUS Deep Schema ===
Contributors: actusanima
Tags: structured data, schema, seo, rich snippets
Requires at least:	5.8
Tested up to: 		6.5.2
Stable tag: 		1.3.2
Requires PHP: 		5.6
License: 			GPL-3.0
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Schema Markup Creator for WordPress

== Description ==
= the Ultimate Schema Markup Tool. =
<br>
**Elevate your SEO experience.**
*The most complete and correct schemas you can get.*
<br><br>
**Actus Deep Schema** is the ultimate solution for managing structured data and schema markups for your WordPress website. Our plugin simplifies the process of adding structured data to your website, allowing you to optimize your site for search engines. With Actus Deep Schema, you don’t need to have extensive technical knowledge to create and manage schema markups effectively. Our user-friendly interface makes it easy for you to add proper schema markup to your website’s content with just a few clicks.
<br>


= Features =

* **One Click Schema**. Auto-generated schemas for all kinds of pages with dynamic data from your content.
* **Clean and Powerfull UI**. Clean and functional user interface that enables you to easily manage different types of data.
* **Expert Guidance for Schema Markup**. Detailed help information for each of the hundreds of different fields, along with suggested available values for each field. Extensive documentation on our website.
* **Schema Linking Made Easy**. Actus Deep Schema simplifies the process of linking schema types together. Create a properly formated schema with no hassle.
* **Preview and Validate**. Preview schema JSONs or test your schemas in Schema Validator or Google's Rich Results Test with one click.


= Premium Features =
* **Extensive Schema Type Support**. Choose from 19 primary schema types, as well as hundrends of subtypes, to meet your specific needs. Incredible depth in schema markup.
* **Third-Party Plugin Integration**. Integrations with popular plugins like Woocommerce and The Events Calendar. More options coming soon.


== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. A new menu named ACTUS will appear in your wordpress admin. Select ACTUS Deep Schema to get started.



== Usage ==
To get started with Actus Deep Schema, follow these steps:

* Install and activate Actus Deep Schema on your website.
* Once installed, you can refine the auto-generated schemas or create your own to suit your specific needs.

Begin by defining the basic information about your website using the Website schema. Next, add detailed information to the Business or Organization schema. Additionally, you can set defaults for the auto-generated webpage schemas and define schemas for your post types.

Throughout the process, our tips and help system will provide guidance and assistance.

For comprehensive documentation on the plugin, visit our website:
[Actus Deep Schema](https://deepschema.org/ "Actus Deep Schema")
[Deep Schema Documentation](https://deepschema.org/doc/ "Deep Schema Documentation")
[Deep Schema FAQ](https://deepschema.org/faq/ "Deep Schema FAQ")


== More ==
* Actus Deep Schema uses the **YouTube Data API v3** in order to retrieve more rich information from videos and populate VideoObject Schema. You need to provide a valid API key in order to use that function.
[YouTube Data API v3](https://developers.google.com/youtube/v3/ "YouTube Data API v3")
[YouTube Data API v3 Terms of Service](https://developers.google.com/youtube/terms/api-services-terms-of-service "YouTube Data API v3 Terms of Service")
* Actus Deep Schema uses the **Wikidata:REST API** in order to retrieve Wikidata IDs and assign them on desired properties.
[Wikidata:REST API](https://www.wikidata.org/wiki/Wikidata:REST_API "Wikidata:REST API")
* Actus Deep Schema uses the **MediaWiki Action API** in order to retrieve schema data for Business, Products, Services and Vehicles.
[MediaWiki Action API](https://www.mediawiki.org/wiki/API:Main_page "MediaWiki Action API")
[MediaWiki Action API Terms of Use](https://foundation.wikimedia.org/wiki/Policy:Terms_of_Use "MediaWiki Action API Terms of Use")


== Frequently Asked Questions ==

= What is structured data? =
Structured data refers to a specific way of organizing and presenting information on a webpage, using a pre-defined set of tags and attributes that describe the content of the page. This data can include information about the page’s title, description, author, images, and more.

= How can structured data benefit my website? =
Structured data can benefit your website by making it easier for search engines to understand and present your content to users. This can improve your website’s ranking on search engine results pages, and increase visibility and click-through rates.

= How does Actus Deep Schema work? =
Actus Deep Schema offers a user-friendly interface that allows website owners to add schema markup without any coding knowledge. The plugin offers automatic schema generation, multiple layers of assistance and provides search engines with the most rich and proper schema tree you can get.

= What features does Actus Deep Schema offer? =
Actus Deep Schema offers a variety of features, including the ability to add structured data to posts and pages, customize the markup for different types of content, and preview the structured data before publishing.




== Screenshots ==
1. The plugin's home page.
2. Website Schema.
3. Business/Organization Schema.
4. Web Pages.
5. WebPage Schema.
6. Post Types.
7. Article Schema.
8. Items
9. Person Schema.
10. Audience Schema.



== Changelog ==

= 1.3.1 =
 3 April 2024
 
* Minor fixes
* Minor styling enhancements
* Tips additions


= 1.3 =
 15 March 2024
 
* add: Vehicle schema
* add: Vacation Rental schema
* add: Movie schema
* Major styling and layout enhancements


= 1.2.5 =
 15 December 2023
 
* add: LearnPress plugin integration
* add: Extra Input Buttons on Fields.
* add: Targeting on Schema Defaults
* Major styling and layout enhancements


= 1.2 =
 30 November 2023
 
* add: pricing, publisher, total number of students, date published, teaches, about, financial aid eligible, languages, syllabus, course instances, course program to Course schema.
* add: review, rating, seasonal hours, founding date, number of employees, identifiers (ISO6523, DUNS, LEI, NAICS, GS1) to Business/Organization schema


= 1.1.4 =
 29 November 2023
 
* add: itemCondition to Product schema
* add: deliveryTime to Product schema
* add: additionalProperty to Product schema
* add: Assign Schemas to Post Types screen
* add: context menu on complex fields
* change: properties section on Product schema


= 1.1.3 =
 23 November 2023
 
* fix: main archive title
* fix: get youtube chapters ajax call
* fix: duration input
* change: wikidata country & continent type
* change: dont't get post data on archive pages


= 1.1.2 =
 20 November 2023
 
* add: material property to Product schema
* add: Woocommerce link to color & material properties
* fix: dropdown values - items
* change: remove video property from Product
* styling changes


= 1.1.1 =
 19 November 2023
 
* add: View Page Schemas on admin bar
* add: publisher property to WebPage schema
* fix: store _dynamic.wpID only on post/page/item
* fix: Recipe suitableForDiet bug
* fix: youtube videos added twice
* fix: datetime-local - remove timezone from display string
* fix: Media Edit bug
* fix: wikidata bug
* change: get post data in priority of _dynamic


= 1.1 =
 16 November 2023
 
* add: New properties on Product Schema (hasMerchantReturnPolicy, shippingDetails, hasEnergyConsumptionDetails, weight, width, height)
* add: Reset Defaults Button to schema forms
* add: custom meta on dynamic popup
* fix: Default Article form
* fix: strip html from excerpt
* fix: load WP data for each item
* fix: title bug on adding new item


= 1.0.6 =
 12 November 2023
 
* add: {dynamic} option to field.conditions
* add: {dynamic} condition to Event location, _vlocation
* fix: Wikidata type problem
* fix: event attendance mode type fix
* fix: run acsc_external_plugins before parseWP hook
* fix: callback from get type
* fix: wikidata search titles bug


= 1.0.5 =
 11 November 2023
 
* add: Dynamic context on recipeIngredient, recipeInstructions.
* add: seller and audience property to Product and SoftwareApplication
* add: description property to Product
* fix: recipeYield value from single to array
* fix: wordpress data tied to Item data
* fix: reviewBody instead of description property on Review
* fix: Article mainEntity & about @id problem


= 1.0.4 =
 09 November 2023
 
* add: Reviews schema on Book, Course, Event, HowTo, Place, Product, Recipe, Service, SoftwareApplication.


= 1.0.3 =
 08 November 2023
 
* add: chapters on page content videos
* add: Get images and video from cooked gallery
* add: Cooked directions (recipeInstructions)
* add: Cooked ingredients - Cooke yield
* add: Get videos of current page in dropdowns
* add: replace video ids with video objects
* fix: minor bugs
* show dynamic value on duration fields
* change post type selection to switch control


= 1.0.2 =
 07 November 2023
 
* fix: type selection bug
* fix: minor bugs
* wiki results limit to 60
* get users in chunks


= 1.0.1 =
 03 November 2023
 
* add: Event Type selection
* add: places-business dropdown options
* add: settings - thumbnails size
* add: Cooked integration
* fix: minor bugs
 

= 1.0.0 =
 23 October 2023

* First Release.



 == Upgrade Notice ==

