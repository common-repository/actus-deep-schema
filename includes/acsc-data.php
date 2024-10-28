<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      

// more template examples on acsc-data.js

acsc_trc('━━━━ acsc-data.php');

// Creates Schema Data on the First Run
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
function acsc_DATA_init(){
    if ( wp_doing_ajax() ) return false;
acsc_trc('acsc_DATA_init');
	
	global $ACSC, $acsc_phpDATA;
	$new_installation = false;
	
	if ( ! $ACSC['data'] ) {
		$new_installation = true;
		
		$ACSC['data'] = array(
			'counter' => 1,
			'admin'   => array(),
			'schema'  => array(
				'website'  => array( 'website-1' ),
				'audience' => array( 'audi-1' ),
				'business' => array( 'busi-1' ),
				'persons'  => array(),
				'page'     => array(
					'page-0',
					'page-author',
					'page-archive',
					'page-item',
					'page-about',
					'page-contact',
					'page-search',
				),
				'post'     => array( 'post-0' ),
				'items'    => array(),
			),
		);
	}
	
	acsc_items_and_forms();

	$website  = acsc_DEF_website();
	$website_admin  = acsc_DEF_website_admin();
	$audience = acsc_DEF_audience();
	$business = acsc_DEF_business();
	$audi_1   = array_merge( acsc_DEF_audience(),
							 acsc_DEF_audience_1() );
	$busi_1   = array_merge( acsc_DEF_business(),
							 acsc_DEF_business_1() );
	$business_admin = acsc_DEF_business_admin();
	$webpage  = acsc_DEF_webpage();
	$article  = acsc_DEF_article();
	
	$acsc_phpDATA['data']   = $ACSC['data'];

	if ( $new_installation ) {
		update_option( 'ACSC_data', $ACSC['data'], false );
		update_option( 'ACSC-website-1', $website, false );
		update_option( 'ACSC-website-1-admin', $website_admin, false );
		update_option( 'ACSC-audi-1', $audi_1, false );
		update_option( 'ACSC-busi-1', $busi_1, false );
		update_option( 'ACSC-busi-1-admin', $business_admin, false );
	}
	//update_option( 'ACSC-page-0', $webpage, false );
	//update_option( 'ACSC-post-0', $article, false );
	
	/*
	$about 	 = array_merge($webpage, acsc_DEF_about());
	$contact = array_merge($webpage, acsc_DEF_contact());
	$item 	 = array_merge($webpage, acsc_DEF_item());
	$acsc_author  = array_merge($webpage, acsc_DEF_author());
	$archive = array_merge($webpage, acsc_DEF_archive());
	$search  = array_merge($webpage, acsc_DEF_search());
	

	update_option( 'ACSC-page-about', $about, false );
	update_option( 'ACSC-page-contact', $contact, false );
	update_option( 'ACSC-page-item', $item, false );
	update_option( 'ACSC-page-author', $acsc_author, false );
	update_option( 'ACSC-page-archive', $archive, false );
	update_option( 'ACSC-page-search', $search, false );
	*/
}
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
// used in  { main }.php -> acsc_DATA()


// Schema Arrays
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
function acsc_DEF_website(){
	
	$custom_logo = acsc_wp_logo();
	
	if ( ! isset( $site_image ) ) $site_image = $custom_logo;
	
	$website = array(
		"@context"		=> array(
			"@vocab" => "http://schema.org",
			"@base"  => '{site_url}',
		),
		"@type"				=> "WebSite",
		"@id"				=> "#website-1",
		"name"				=> '{site_name}',
		"alternateName"		=> '{site_name}',
		"url"				=> '{site_url}',
		"description" 		=> '{site_descr}',
		"audience"			=> array(
			array('@id' => '#audi-1')
		),
		"creator"			=> array(),
		"publisher"			=> array('@id' => '#busi-1'),
		"copyrightHolder" 	=> array('@id' => '#busi-1'),
		"copyrightYear"   	=> "{year}",
		"inLanguage" 		=> array(
			array(
				"@type" => "Language",
				"name"  => "{locale}",
			),
		),
		"mainEntity" 		=> array(),
		"about" 			=> array(),
		"image"				=> array(
			"@type" => "ImageObject",
			"contentUrl" => '{site_image}',
			"name"	=> "",
			"caption" => "",
			"thumbnail" => "",
			"width" => "",
			"height" => "",
		),
		"sameAs" 			=> array(),
		"_show_search"		=> '1',
		"potentialAction" 	=> array (
			"@type"	 	  => "SearchAction",
			"target" 	  => get_site_url() . "/?s={search_term_string}",
			"query-input" => "required name=search_term_string"
		),
		//"medicalSpecialty" => array(),
	);
	return $website;
}
function acsc_DEF_website_admin(){
	$website = array(
		"_show_search"	=> '1',
	);
	return $website;
}
function acsc_DEF_audience(){
	$audience = array(
		"@id"  => "",
		"@type"	=> "PeopleAudience",
		"name"	=> "",
		"audienceType"	=> "",
		"geographicArea"=> array(),
		// people
		'suggestedGender' => 'unisex',
		'suggestedAge' => array(
			'minValue' => null,
			'maxValue' => null,
			'unitCode' => 'ANN',
		),
		'requiredGender'  => '',
		'requiredMinAge'  => null,
		'requiredMaxAge'  => null,
		
		// business
		'numberOfEmployees' => array(
			'minValue' => null,
			'maxValue' => null,
		),
		'yearlyRevenue' => array(
			'minValue' => null,
			'maxValue' => null,
			'unitCode' => 'EUR',
		),
	);
	
	return $audience;
}
function acsc_DEF_audience_1(){
	$audience = array(
		"@id"	=> "#audi-1",
		"name"	=> "General Audience",
	);
	
	return $audience;
}
function acsc_DEF_person(){
	$template = array (
		"@id"				=> '',
		"@context"			=> "http://schema.org",
		"@type"				=> "Person",
		"honorificPrefix"	=> '',
		"name"				=> '',
		"honorificSuffix"	=> '',
		"gender"			=> '',
		//"givenName"		: "Ricardo",
		//"familyName"		: "Rodriguez",
		"jobTitle"			=> '',
		"email"				=> array(),
		"telephone"			=> array(),
		"address"	 		=> array(),
		"url"				=> '',
		"worksFor"	 		=> array(),
		"affiliation" 		=> array(),
		"sameAs"	 		=> array(),
		"image"				=> '',
	);
	return $template;
	
}
function acsc_DEF_business(){
	$custom_logo = acsc_wp_logo();
	
	$business = array(
		"@id" 			=> "",
		"@type" 		=> array("LocalBusiness"),
		"@context"		=> array(
			"@vocab" => "http://schema.org",
			"@base"  => '{site_url}',
		),
		"name"			=> '',
		"legalName"		=> '',
		"brand"			=> array(),
		"openingHours"	=> '',
		"priceRange"	=> '',
		"_seasonalHours" => array(),
		"openingHoursSpecification" => array(
			0 => array("@type" => "OpeningHoursSpecification", "dayOfWeek"	=> "https://schema.org/Monday",),
			1 => array("@type" => "OpeningHoursSpecification", "dayOfWeek"	=> "https://schema.org/Tuesday",),
			2 => array("@type" => "OpeningHoursSpecification", "dayOfWeek"	=> "https://schema.org/Wednesday",),
			3 => array("@type" => "OpeningHoursSpecification", "dayOfWeek"	=> "https://schema.org/Thursday",),
			4 => array("@type" => "OpeningHoursSpecification", "dayOfWeek"	=> "https://schema.org/Friday",),
			5 => array("@type" => "OpeningHoursSpecification", "dayOfWeek"	=> "https://schema.org/Saturday",),
			6 => array(
				"@type"		=> "OpeningHoursSpecification",
				"dayOfWeek"	=> "https://schema.org/Sunday",
				"opens"		=> "", //"09:00:00"
				"closes"	=> "",
			),
		),
		
		"amenityFeature"		=> array(),
		
		"iataCode"				=> '',
		
		"medicalSpecialty" 		=> '',
		"healthPlanNetworkId" 	=> '',
		
		"sport" 				=> '',
		
		
		"vatID"					=> '',
		"taxID"					=> '',
		"iso6523Code"			=> '',
		"duns"					=> '',
		"leiCode"				=> '',
		"naics"					=> '',
		"globalLocationNumber"	=> '',
		
		"email"				=> array(),
		"telephone"			=> array(),
		"faxNumber"			=> array(),
		"founder"			=> array(),
		"employee"			=> array(),
		"numberOfEmployees"	=> '',
		"url"				=> '',
		"foundingDate" 		=> '',
		"description" 		=> '',
		"sameAs"			=> array(),
		"logo"	 			=> '',
		"image" 			=> '',
		"hasMap"	 		=> '',
		"address"			=> array(
			"@type"		=> "PostalAddress",
		),
		"geo"				=> array(
			"@type"		=> "GeoCoordinates",
		),
		"areaServed"		=> array(),
		"departments"		=> array(),
		"subOrganization" 	=> array(),
		"parentOrganization"=> '',
		"memberOf" 			=> '',
		"aggregateRating" 	=> array(
			"@type"			=> "AggregateRating",
		),
		"review" 			=> array(),
		
		"_type" 			=> array(
			array(
				'__type_a' 	=> "Business",
				'__type_b' 	=> "LocalBusiness",
			),
		),
	);
	return $business;
}
function acsc_DEF_business_1(){
	$custom_logo = acsc_wp_logo();
	
	$business = array(
		"@id" 	=> "#busi-1",
		"name"	=> '{site_name}',
		"url"	=> '{site_url}',
		"logo"	=> '{site_logo}',
		"image" => '{site_logo}',
	);
	return $business;
}
function acsc_DEF_business_admin(){
	$business = array(
		"__type_a"	 	=> "Business",
		"__type_b" 		=> "LocalBusiness",
		"_type" 			=> array(
			array(
				'__type_a' 	=> "Business",
				'__type_b' 	=> "LocalBusiness",
			),
		),
	);
	return $business;
	
}
function acsc_DEF_webpage(){
	$webpage = array (
		"@context"		=> array(
			"@vocab" => "http://schema.org",
			"@base"  => "{site_url}",
		),
		"@type"			=> array("WebPage"),
		"@id"			=> '{page_id}',
		"name"			=> "",
		"description"	=> "",
		"audience"		=> array(),
		"inLanguage" 	=> array(),
		"url"			=> "",
		"primaryImageOfPage" => array(),
		"image" 		=> "",
		"video" 		=> "",
		"audio" 		=> "",
		"specialty" 	=> "",
		"mainEntity" 	=> array(),
		"_collection" 	=> "",
		"about" 		=> array(),
		"author"		=> "",
		"publisher"		=> "",
		"isPartOf" 	 	=> array(),
		"datePublished" => '',
		"dateModified"  => '',
	);
	return $webpage;
}
function acsc_DEF_webpage_defaults(){
	$webpage = array (
		"@context"		=> array(
			"@vocab" => "http://schema.org",
			"@base"  => "{site_url}",
		),
		"@type"			=> array("WebPage"),
		"@id"			=> '{page_id}',
		"name"			=> "{page_title}",
		"description"	=> "{page_excerpt}",
		"audience"			=> array(
			array('@id' => '#audi-1')
		),
		"inLanguage" 	=> array(
			array (
				"@type"	=> "Language",
				"name"	=> "{locale}"
			)
		),
		"url"			=> "{page_url}",
		"primaryImageOfPage" => array(
			"@type" 	=> "ImageObject",
			"contentUrl"=> '{featured_url}',
			"name"		=> '{featured_alt}',
			"caption" 	=> '{featured_caption}',
			"thumbnail" => '{featured_thumb}',
			"width" 	=> '{featured_w}',
			"height" 	=> '{featured_h}',
		),
		"image" 		=> "{content_images}",
		"video" 		=> "{all_videos}",
		"audio" 		=> "{audio}",
		"specialty" 	=> "",
		"mainEntity" 	=> array(),
		"about" 		=> array(),
		"author"		=> array('{page_author}'),
		"publisher"	    => array(
			array('@id' => '#busi-1')
		),
		"isPartOf" 	 	=> array('@id' => '#website-1'),
		"datePublished" => '{date_published}',
		"dateModified"  => '{date_modified}',
	);
	return $webpage;
}

function acsc_DEF_about(){
	$webpage = array (
		"@type"			=> array("AboutPage"),
		"mainEntity" 	=> array( array('@id' => '#busi-1') ),
	);
	return $webpage;
}
function acsc_DEF_contact(){
	$webpage = array (
		"@type"			=> array("ContactPage"),
		"mainEntity" 	=> array( array('@id' => '#busi-1') ),
	); 
	return $webpage;
}
function acsc_DEF_item(){
	$webpage = array (
		"@type"			=> array("ItemPage"),
		"description"	=> "{post_type} Page.",
	);
	return $webpage;
}
function acsc_DEF_author(){
	$webpage = array (
		"@type"			=> array('CollectionPage',
								 'ProfilePage'),
		"name"			=> 'Articles posted by {author_name}.',
		"description"	=> '{author_description}',
		"mainEntity" 	=> array("{author}"),
		//"mainEntity" 	=> array( array('@id' => '{author_ID}') ),
		"primaryImageOfPage" => array(
			"@type" => "ImageObject",
			"contentUrl" => '{author_avatar}',
			"name"	=> '{author_name}',
			"caption" => '',
			"thumbnail" => '',
			"width" => '',
			"height" => '',
		),
		"_collection" 	=> "{archive}",
	);
	return $webpage;
}
function acsc_DEF_archive(){
	$webpage = array (
		"@type"			=> array("CollectionPage"),
		"name"			=> 'Archive by {taxonomy_singular} {term_name}.',
		"description"	=> "Archive Page. {taxonomy_singular}: {term_name}. \n{term_description}",
		"_collection" 	=> "{archive}",
	);
	return $webpage;
}
function acsc_DEF_search(){
	$webpage = array (
		"@type"			=> array("SearchResultsPage"),
		"name"			=> "Search Results for: {search}.",
		"description"	=> "Search Results Page.",
	);
	return $webpage;
}
function acsc_DEF_article(){
	$article = array (
			"@context"		=> array(
				"@vocab" => "http://schema.org",
				"@base"  => "{site_url}",
			),
			"@type" 			=> array("Article"),
			"_type" 			=> array(
				array('__type_a' => "Article"),
			),
			
			"@id"			=> '{post_id}',
			"headline"		=> "",
			"alternativeHeadline" => "",
			"image" 		=> "",
			"video" 		=> "",
			"audio" 		=> "",
			"author"		=> array(),
			"publisher"		=> '',
			"articleSection"=> "", 
			//"genre"			=> "{category}", 
			"keywords"		=> "",
			
			"description"	=> "",
			"articleBody"	=> "",
			"wordCount"		=> "",
			"audience"			=> array(),
			"inLanguage" 	=> array(),
			"url"			=> "",
			"mainEntity" => array(),
			"about" => array(),

			"copyrightHolder" 	=> array(),
			"copyrightYear"   	=> "",
			"copyrightHolder" 	=> "",
			"copyrightYear"   	=> "",

			"publisher"			=> array(),
			"datePublished" 	=> '',
			"dateModified"  	=> '',
			"_includeArticleBody"  => '',
			
		);
	return $article;
}
function acsc_DEF_article_defaults(){
	$article = array (
			"@context"		=> array(
				"@vocab" => "http://schema.org",
				"@base"  => "{site_url}",
			),
			"@type" 			=> array("Article"),
			"_type" 			=> array(
				array('__type_a' => "Article"),
			),
			"@id"			=> '{post_id}',
			"headline"		=> "{post_title}",
			"alternativeHeadline" => "",
			"image" 		=> "{all_images}",
			"video" 		=> "{all_videos}",
			"audio" 		=> "{audio}",
			"author"		=> array( "{author}" ),
			"publisher"		=> 'default',
			"articleSection"=> "{category}", 
			//"genre"			=> "{category}", 
			"keywords"		=> "{post_tag}",
			
			"description"	=> "{post_excerpt}",
			"articleBody"	=> "{post_content}",
			"wordCount"		=> "{word_count}",
			"audience"			=> array(
				array('@id' => '#audi-1')
			),
			"inLanguage" 	=> array(
				array (
					"@type"	=> "Language",
					"name"	=> "{locale}"
				)
			),
			"url"			=> "{post_url}",
			"mainEntity" => array(),
			"about" => array(),
			/*
			"mainEntityOfPage" => array(
				//"@type": "WebPage",
				"@id" => '{page_id}'
			),
			*/
			//"mainEntityOfPage" => '{page_url}',
			//"mainEntityOfPage" => array('@id' => '{page_id}'),
			//"isPartOf"		   => array('@id' => '{page_id}'),
		
			"copyrightHolder" 	=> array('@id' => '#busi-1'),
			"copyrightYear"   	=> "{year}",
			"copyrightHolder" 	=> '',
			"copyrightYear"   	=> "",

			"publisher"	    => array(
				array('@id' => '#busi-1')
			),
			"datePublished" => '{date_published}',
			"dateModified"  => '{date_modified}',
			"_includeArticleBody"  => '1',
			
		);
	return $article;
}

function acsc_DEF_Service(){
	$template = array (
		"@type" 			=> "Service",
		"@id"				=> '{post_id}',
		"url"				=> "",
		"name"				=> "{post_title}",
		"category"	 		=> array(),
		"serviceOutput"	 	=> array(),
		"additionalType" 	=> array(),
		"providers" 		=> array(),
		"brokers"	 		=> array(),
		"mainEntityOfPage" 	=> "",
		"audience"	 		=> array(),
		"availableChannel"	=> array(),
		"areaServed" 		=> array(),
		"slogan"			=> "",
		"hoursAvailable"	=> array(),
		"brand"				=> array(),
		"award"				=> array(),
		"termsOfService"	=> "",
		"review"			=> array(),
		"aggregateRating" 	=> array(),
		"logo"	 			=> "",
		"image" 			=> "",
		
		"_target"			=> "",
		
	);
	return $template;
	
}
function acsc_DEF_Product(){
	$template = array (
		"@type" 			=> "Product",
		"@type" 			=> array("Product"),
		"_type" 			=> array(
			array('__type_a' => "Product"),
		),
		"url"				=> "",
		"name" 				=> "",
		"model"				=> "",
		"sku"				=> "",
		"category"	 		=> array(),
		"description"		=> "",
		"color"			 	=> array(),
		"material"		 	=> array(),
		"additionalProperty"=> array(),
		"weight"		 	=> array(
			'@type' 	=> 'QuantitativeValue',
			'value' 	=> '',
			'unitCode' 	=> 'KGM',
		),
		"width"			 	=> array(),
		"height"		 	=> array(),
		"depth"			 	=> array(),
		"manufacturer"	 	=> array(),
		"additionalType"	 => array(),
		"countryOfAssembly"	=> "",
		"mainEntityOfPage" 	=> "",
		"audience"	 		=> array(),
		"availableChannel"	=> array(),
		"areaServed" 		=> array(),
		"slogan"			=> "",
		"brand"				=> array(),
		"award"				=> array(),
		"hasEnergyConsumptionDetails" => array(
			'_standard' => '',
			'hasEnergyEfficiencyCategory' => "",
		),
		"offers"			=> array(),
		"aggregateRating" 	=> array(
			"@type"		=> "AggregateRating",
		),
		"review" 			=> array(),
		"image" 			=> "{all_images}",
		
		"_target"			=> "",
	);
	return $template;
	
}
function acsc_DEF_HowTo(){
	$template = array (
		"@type" 		=> "HowTo",
		"name" 			=> "",
		"description" 	=> "",
		"inLanguage" 	=> array(
			array (
				"@type"	=> "Language",
				"name"	=> "{locale}"
			)
		),
		"review"			=> array(),
		"aggregateRating"	=> array(),
		"totalTime" 	=> "",
		"yield" 		=> "",
		"image" 		=> "{all_images}",
		"video" 		=> "{all_videos}",
		"supply" 		=> array(),
		"tool" 			=> array(),
		"step" 			=> array(),
		"estimatedCost" => array(
			"@type"		=> "MonetaryAmount",
			"currency"	=> "",
			//"value"		=> "",
			"maxValue"	=> "",
			"minValue"	=> "",
			"value"		=> "",
		),
		"_target" 		=> array(),
	);
	return $template;
	
}
function acsc_DEF_Recipe(){
	$template = array (
		"@type" 		=> "Recipe",
		"name" 			=> "",
		"description" 	=> "",
		"recipeCategory"=> array(),
		"recipeCuisine"	=> "",
		"suitableForDiet" => array(),
		"keywords" 		=> "",
		"prepTime" 		=> "",
		"cookTime" 		=> "",
		"totalTime"		=> "",
		
		"recipeIngredient"	=> array(),
		"recipeInstructions"=> array(),
		"recipeYield"		=> array(),
		
		
		"nutrition" 	=> array(
			"@type"					=> 'NutritionInformation',
			"servingSize"			=> '',
			"calories"				=> '',
			"carbohydrateContent"	=> '',
			"cholesterolContent"	=> '',
			"fatContent"			=> '',
			"fiberContent"			=> '',
			"proteinContent"		=> '',
			"saturatedFatContent"	=> '',
			"sodiumContent"			=> '',
			"sugarContent"			=> '',
			"transFatContent"		=> '',
			"unsaturatedFatContent"	=> '',
		),
		
		
		//"image" 		=> array(),
		//"video" 		=> array(),
		"image" 		=> "{content_images}",
		"video" 		=> "{all_videos}",
		
		
		"datePublished" => '{date_published}',
		"dateModified"  => '{date_modified}',
		"author"		=> array( "{author}" ),
		"publisher"		=> array(),
		"inLanguage" 	=> array(
			array (
				"@type"	=> "Language",
				"name"	=> "{locale}"
			)
		),
		"review"			=> array(),
		"aggregateRating"	=> array(),
	
		"_target" 		=> array(),
	);
	return $template;
	
}
function acsc_DEF_SoftwareApplication(){
	$template = array (
		"@type" 				=> array("SoftwareApplication"),
		"name" 					=> "",
		"description"			=> "",
		"softwareVersion"		=> "",
		"applicationSuite" 		=> "",
		"operatingSystem" 		=> array(),
		"applicationCategory" 	=> array(),
		"featureList" 			=> array(),
		"softwareAddOn" 		=> array(),
		"downloadUrl" 			=> array(),
		"releaseNotes" 			=> "",
		
		
		
		"offers"				=> array(),
		"screenshot" 			=> "{content_images}",
		
		
		"datePublished" 	=> "",
		"author" 			=> array(),
		"publisher" 		=> array(),
		"inLanguage" 		=> array(),
		"sameAs" 			=> array(),
		"review"			=> array(),
		"aggregateRating"	=> array(),
	); 
	return $template;
	
}
function acsc_DEF_Book(){
	$template = array (
		"@type" 			=> "Book",
		"name" 				=> "",
		"url"		 		=> "",
		"author" 			=> array(),
		"sameAs" 			=> array(),
		"review"			=> array(),
		"aggregateRating"	=> array(),
		"workExample" 		=> array(),
	); 
	return $template;
	
}
function acsc_DEF_Event(){
	$template = array (
		"@type" 		=> "Event",
		"@type" 		=> array("Event"),
		"__type_a"	 	=> "Event",
		"_type" 			=> array(
			array('__type_a' => "Event"),
		),
		"@id"			=> '{post_id}',
		"name" 			=> "",
		"description" 	=> "",
		"url"		 	=> "",
		"eventStatus"	=> "EventScheduled",
		"startDate"		=> "",
		"endDate"		=> "",
		"_fd_startDate"	=> array(
			"_date" 	=> "",
			"_time" 	=> "",
			"_timezone" => "{timezone}",
		),
		"_fd_endDate"	=> array(
			"_date" 	=> "",
			"_time" 	=> "",
			"_timezone" => "{timezone}",
		),
		"previousStartDate"		=> "",
		"eventAttendanceMode" 	=> "OfflineEventAttendanceMode",
		"review"				=> array(),
		"aggregateRating"		=> array(),
		"location" 				=> array(
			array(
				"address"		=> array(
					"@type"		=> "PostalAddress",
				),
			),
		),
		"_vlocation" 			=> array(
			array(
				"@type"		=> "VirtualLocation",
			),
		),
		"organizer"				=> array(),
		"performer"				=> array(),
		"offers"				=> array(),
		"image"					=> "{all_images}",
	); 
	return $template;
}
function acsc_DEF_Course(){
	$template = array (
		"@type" 			=> "Course",
		"name" 				=> "",
		"courseCode" 		=> "",
		"description" 		=> "",
		"publisher"			=> array(),
		"provider" 			=> array(),
		"educationalLevel" 	=> array(),
		"educationalCredentialAwarded" 	=> array(),
		"review"			=> array(),
		"aggregateRating"	=> array(),
		"image" 		=> "{all_images}",
		"video" 		=> "{all_videos}",
		"offers"			=> array(),
		"teaches"			=> array(),
		"about"				=> array(),
		"inLanguage"		=> array(
			'@type'	=> 'Language',
		),
		"syllabusSections"	=> array(),
		"hasCourseInstance" => array(),
		"hasPart" 			=> array(),
		"datePublished" 	=> '',
		"coursePrerequisites" 		=> array(),
		"financialAidEligible" 		=> array(),
		"totalHistoricalEnrollment" => array(),
	); 
	return $template;
}
function acsc_DEF_FAQ(){
	$template = array (
		"faq" => array(),
	);
	return $template;
}
function acsc_DEF_SpecialAnnouncement(){
	$template = array (
		"@type" 		=> "SpecialAnnouncement",
		"name" 			=> "",
		"text"		 	=> "",
		"datePosted"	=> "",
		"expires" 		=> "",
		"category" 		=> "",
		"diseasePreventionInfo"    => "",
		"diseaseSpreadStatistics"  => "",
		"gettingTestedInfo"   	   => "",
		"governmentBenefitsInfo"   => "",
		"newsUpdatesAndGuidelines" => "",
		"publicTransportClosuresInfo" => "",
		"quarantineGuidelines"     => "",
		"schoolClosuresInfo"   	   => "",
		"travelBans"   	   		   => "",
		"announcementLocation"     => "",
		"spatialCoverage" 		   => array(),
	); 
	return $template;
}
function acsc_DEF_Place(){
	$custom_logo = acsc_wp_logo();
	
	$place = array(
		"@type" 			=> array("Place"),
		"_type" 			=> array(
			array(
				'__type_a' => "Place",
				'__type_b' => "",
			),
		),
		"name"				=> '',
		"description" 		=> '',
		"containsPlace"		=> '',
		"containedInPlace"	=> '',
		"url"				=> '',
		"sameAs"			=> array(),
		
		"hasMap"	 		=> '',
		"address"			=> array(
			"@type"			=> "PostalAddress",
		),
		"geo"				=> array(
			"@type"			=> "GeoCoordinates",
		),
		
		"telephone"				=> array(),
		"faxNumber"				=> array(),
		"tourBookingPage"		=> array(),
		
		"publicAccess" 			=> "",
		"hasDriveThroughService"=> "",
		"amenityFeature"		=> array(),
		"event"		 			=> array(),
		
		"logo"	 		=> '',
		"image" 		=> '',
		"photo" 		=> "{all_images}",
		
	);
	return $place;
}

function acsc_DEF_AudioObject(){
	$template = array (
		"@type" 		=> "AudioObject",
		"name" 			=> "",
		"description" 	=> "",
		"contentUrl" 	=> "",
		"duration" 		=> "",
		"bitrate" 		=> "",
		"encodingFormat"=> "",
		
		
		"datePublished" 	=> "",
		"author" 			=> array(),
		"inLanguage" 		=> array(),
		"sameAs" 			=> array(),
		"aggregateRating"	=> array(),
	); 
	return $template;
	
}
function acsc_DEF_VideoObject(){
	$template = array (
		"@type" 		=> "VideoObject",
		"name" 			=> "",
		"description" 	=> "",
		"contentUrl" 	=> "",
		"thumbnailUrl" 	=> "",
		"uploadDate" 	=> "",
		"duration" 		=> "",
		"hasPart" 		=> array(),
		
		
		
		"datePublished" 	=> "",
		"author" 			=> array(),
		"publisher" 		=> array(),
		"inLanguage" 		=> array(),
		"sameAs" 			=> array(),
		"aggregateRating"	=> array(),
		"interactionStatistic" => array(),
		"videoQuality" 		=> "",
		"genre" 			=> "",
	); 
	return $template;
	
}
function acsc_DEF_ImageObject(){
	$template = array (
		"@type" 	=> "ImageObject",
		"contentUrl"=> "",
		"name" 		=> "",
		"caption" 	=> "",
		"thumbnail" => "",
		"width" 	=> "",
		"height" 	=> "",
	);
	return $template;
	
}

function acsc_DEF_Vehicle(){
	$template = array (
		"@type" 		=> array("Car"),
		"_type" 		=> array(
			array(
				'__type_a' 	=> "Vehicle",
				'__type_b' 	=> "Car",
			),
		),

		"name" 			=> "",
		"url" 			=> "",
		"model"			=> "",
		"color"			=> "",
		"bodyType"		=> "",
		"numberOfDoors"	=> "",
		"vehicleModelDate"  			=> "",
		"vehicleTransmission" 	 		=> "",
		"vehicleConfiguration"  		=> "",
		"vehicleIdentificationNumber"  	=> "",
		"vehicleSeatingCapacity"		=> "",
		"vehicleInteriorType"  			=> "",
		"vehicleInteriorColor"  		=> "",
		"vehicleTransmission"			=> "",
		"driveWheelConfiguration"		=> "",
		"vehicleEngine"		  			=> array(),
		"mileageFromOdometer"  			=> array(
			'@type' => 'QuantitativeValue',
			'value' => 0,
			'unitCode' => 'KMT',
		),
		"itemCondition"	=> 'NewCondition',
		"offers" 		=> array(),
		"image" 		=> array(),
		"brand" 		=> array(),

	
		"_target" 		=> array(),
	);
	return $template;
	
}
function acsc_DEF_VacationRental(){
	$custom_logo = acsc_wp_logo();
	
	$schema = array(
		"@id" 			=> "",
		"@type" 		=> array("VacationRental"),
		'additionalType'=> 'VacationRental',
		"@context"		=> array(
			"@vocab" => "http://schema.org",
			"@base"  => '{site_url}',
		),
		"name"				=> '',
		"brand"				=> '',
		"identifier"		=> '',
		"description"		=> '',

		"containsPlace"		=> array(
			"@type"			=> "Accommodation",
			'additionalType'=> 'EntirePlace',
			'occupancy'		=> array(
				array(
					"@type"			=> "QuantitativeValue",
					"value"			=> "",
				),
			),
			'numberOfBedrooms'		=> "",
			'numberOfBathroomsTotal'=> "",
			'numberOfRooms'			=> "",
			'floorSize'		=> array(
				array(
					"@type"			=> "QuantitativeValue",
					"value"			=> "",
					"unitCode"		=> "SQM",
				),
			),
			'bed'			=> array(
				array(
					"@type"			=> "BedDetails",
					"typeOfBed"		=> "",
					"numberOfBeds"	=> "",
				),
			),
			'amenityFeature' => array(
				array(
					"@type"			=> "LocationFeatureSpecification",
					"name"			=> "",
					"value"			=> 'true',
				),
			),
		),
		"amenityFeature_1" 	=> array(),
		"amenityFeature_2" 	=> array(),


		"image" 		=> "{all_images}",
		//"video" 		=> "{all_videos}",

		"address"			=> array(
			"@type"			=> "PostalAddress",
		),
		"geo"				=> array(
			"@type"			=> "GeoCoordinates",
		),
		"aggregateRating" 	=> array(
			"@type"			=> "AggregateRating",
		),
		"review" 			=> array(),
		"checkinTime"		=> '',
		"checkoutTime"		=> '',
		"knowsLanguage"		=> array('{locale}'),




		"legalName"		=> '',
		"priceRange"	=> '',

		
		"amenityFeature"		=> array(),
		
	
		
		"email"				=> array(),
		"telephone"			=> array(),
		"faxNumber"			=> array(),
		"founder"			=> array(),
		"employee"			=> array(),
		"numberOfEmployees"	=> '',
		"url"				=> '',
		"foundingDate" 		=> '',
		"description" 		=> '',
		"sameAs"			=> array(),
		"logo"	 			=> '',
		"hasMap"	 		=> '',
		"address"			=> array(
			"@type"		=> "PostalAddress",
		),
		"geo"				=> array(
			"@type"		=> "GeoCoordinates",
		),
		"areaServed"		=> array(),
		"departments"		=> array(),
		"subOrganization" 	=> array(),
		"parentOrganization"=> '',
		"memberOf" 			=> '',
		

	);
	return $schema;
}
function acsc_DEF_Movie(){
	$template = array (
		"@type" 			=> "Movie",
		"name" 				=> "",
		"alternativeHeadline" => "",
		"description"		=> array(),
		"genre"				=> array(),
		"countryOfOrigin"	=> array(),
		"dateCreated"		=> "",

		"productionCompany"	=> array(),
		"inLanguage"		=> array(),
		"subtitleLanguage"	=> array(),
		"duration" 			=> "",
		"copyrightHolder" 	=> array(),
		"copyrightYear"   	=> "{year}",
		"award"   			=> array(),

		"director"	 		=> array(),
		"author"	 		=> array(),
		"actor"	 			=> array(),
		"musicBy" 			=> array(),
		"editor" 			=> array(),
		"character"			=> array(),

		"image" 			=> "{all_images}",
		"trailer" 			=> "{all_video}",
		"review" 			=> array(),
		"aggregateRating" 	=> array(
			"@type"		=> "AggregateRating",
		),
		
		"_target"			=> "",
	);
	return $template;
	
}


// Set Items and Forms
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
function acsc_items_and_forms(){
	global $ACSC, $acsc_phpDATA;

	$ACSC['modules'] = array(
		'basic'		=> array(),
		'premium'	=> array(),
	);
	$ACSC['forms'] = $ACSC['options']['modules'][0]['forms'] ?: array(
		'website',
		'business',
		'page',
		'post',
		'Article',
		'audience',
		'persons',
		'Book',
		'Course',
		'SpecialAnnouncement',
		'HowTo',
		'Event',
		'FAQ',
		'Movie',
		'Place',
		'Product',
		'Recipe',
		'Service',
		'SoftwareApplication',
		'VideoObject',
		'AudioObject',
		'Vehicle',
		'VacationRental',
	);
	$ACSC['items'] = $ACSC['options']['modules'][0]['items'] ?: array(
		'Article',
		'AudioObject',
		'Book',
		'Business',
		'Course',
		'SpecialAnnouncement',
		'HowTo',
		'Event',
		'FAQ',
		'Movie',
		'Person',
		'Place',
		'Product',
		'Recipe',
		'Service',
		'SoftwareApplication',
		'VacationRental',
		'Vehicle',
		'VideoObject',
	);
	$ACSC['itemsTitles'] = $ACSC['options']['modules'][0]['itemsTitles'] ?: array(
		'Article',
		'Audio',
		'Book',
		'Business/Org',
		'Course',
		'COVID-19 announcement',
		'How-To',
		'Event',
		'FAQ',
		'Movie',
		'Person',
		'Place',
		'Product',
		'Recipe',
		'Service',
		'Software Application',
		'Vacation Rental',
		'Vehicle',
		'Video',
	);
	
	$acsc_phpDATA['modules'] 	= $ACSC['modules'];
	$acsc_phpDATA['forms'] 		= $ACSC['forms'];
	$acsc_phpDATA['items']   	= $ACSC['items'];
	$acsc_phpDATA['itemsTitles'] = $ACSC['itemsTitles'];

}

// Set Schema Templates
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
function acsc_schema_templates(){
global $ACSC;
if ( wp_doing_ajax() ) return false;

acsc_trc('    acsc_schema_templates');

	$TMPL = array();
	
	$TMPL['basic'] = array(
		'website'  => acsc_DEF_website(),
		'business' => acsc_DEF_business(),
		'page'     => acsc_DEF_webpage(),
		'post'     => acsc_DEF_article(),
	
		'page-0'   => acsc_DEF_webpage_defaults(),
		'post-0'   => acsc_DEF_article_defaults(),
		'audi-1'   => array_merge(
			acsc_DEF_audience(),
			acsc_DEF_audience_1()
		),
		'busi-1'   => array_merge(
			acsc_DEF_business(),
			acsc_DEF_business_1()
		),
		
		'Article'  => acsc_DEF_article(),
		'persons'  => acsc_DEF_person(),
		
		'page-item'		=> acsc_DEF_item(),
		'page-author'	=> acsc_DEF_author(),
		'page-archive'	=> acsc_DEF_archive(),
		'page-search'	=> acsc_DEF_search(),
		'page-about'	=> acsc_DEF_about(),
		'page-contact'	=> acsc_DEF_contact(),
	);
	$TMPL['premium'] = array(
		'audience' => acsc_DEF_audience(),
		
		'Service'  => acsc_DEF_Service(),
		'Product'  => acsc_DEF_Product(),
		'HowTo'    => acsc_DEF_HowTo(),
		'Place'    => acsc_DEF_Place(),
		'Recipe'   => acsc_DEF_Recipe(),
		'Book'     => acsc_DEF_Book(),
		'Course'   => acsc_DEF_Course(),
		'Event'    => acsc_DEF_Event(),
		'FAQ'      => acsc_DEF_FAQ(),
		'SpecialAnnouncement' => acsc_DEF_SpecialAnnouncement(),
		'SoftwareApplication' => acsc_DEF_SoftwareApplication(),
		
		'VideoObject' 	=> acsc_DEF_VideoObject(),
		'AudioObject' 	=> acsc_DEF_AudioObject(),
		'ImageObject' 	=> acsc_DEF_ImageObject(),
		
		'Vehicle'		=> acsc_DEF_Vehicle(),
		'VacationRental'=> acsc_DEF_VacationRental(),
		'Movie'			=> acsc_DEF_Movie(),
		
		
		'page-about'	=> acsc_DEF_about(),
		'page-contact'	=> acsc_DEF_contact(),
		'page-search'	=> acsc_DEF_search(),
	);
	$TMPL['final'] = array();
	
	if ( $ACSC['options'] )
	foreach ($ACSC['options']['modules_active'] as $mod){
		$TMPL['final'] = array_merge( $TMPL['final'], $TMPL[ $mod ] );
	}
	
	
	$TMPL['final'] = array_merge($TMPL['basic'], $TMPL['premium']);
	
	
	// HOOKS - schemaTemplates
	// ==================================
	$TMPL['final'] = array_merge( $TMPL['final'],
	 					 acsc_hooks('schemaTemplates', $TMPL['final']) );


	return $TMPL['final'];
}
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
// used in  { main }.php -> acsc_DATA()
// used in  acsc-FE.php -> acsc_load_schemas_FE()
// hook		acsc-plugins.php




?>