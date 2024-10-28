<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      


acsc_trc('━━━━ acsc-FE-proccess.php');

// Proccess Values
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
function acsc_proccess_values( $item ){
	global $ACSC, $post;

	if ( ! isset( $item ) ) return array();
	
	if ( ! isset($item['_version']) || ! $item['_version'] )
		$item['_version'] = $ACSC['options']['version'];

	$item = acsc_replace_video_ids( $item );
	
	// Add Breadcrumb Data
	// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
	if ( substr( $item['@id'], 0, 5 ) == '#page' ) {
		if ( isset($ACSC['wp']['breadcrumb']) )
			$item['breadcrumb'] = $ACSC['wp']['breadcrumb'];
	}
	
	
	if ( isset($item['@type']) ) {
	
		// Item Page (post) - remove media
		// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
		if ( ( ( is_array($item['@type']) &&
			in_array('ItemPage', $item['@type']) )  ||
			$item['@type'] == 'ItemPage' ) &&
			get_post_type() == 'post' ) {
		
			unset( $item['image'] );
			unset( $item['video'] );
			unset( $item['audio'] );
			
		}


		// Product - remove video & audio
		// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
		if ( ( ( is_array($item['@type']) &&
			in_array('Product', $item['@type']) )  ||
			$item['@type'] == 'Product' ) ) {
			unset( $item['video'] );
			unset( $item['audio'] );
		}
		
		
		
		
		// Event Location
		// sets eventAttendanceMode
		// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
		if ( ( is_array($item['@type']) &&
			in_array('Event', $item['@type']) )  ||
			$item['@type'] == 'Event' ) {
			
			if ( ! isset($item['location']) )
				$item['location'] = array();
			if ( ! isset($item['_vlocation']) )
				$item['_vlocation'] = array();

			if ( isset( $item['eventAttendanceMode'] ) ) {
				if ( $item['eventAttendanceMode'] == "MixedEventAttendanceMode" ){
					// (merge physical and virtual )
					$item['location'] =
						array_merge_recursive( $item['location'], $item['_vlocation'] );
				}
				if ( $item['eventAttendanceMode'] == "OnlineEventAttendanceMode" ){
					$item['location'] = $item['_vlocation'];
				}
			}
			
		}

	}
	
	
	// Archive Collections
	// sets mainEntity
	// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
	if ( isset( $item['_collection'] ) &&
		 is_array( $item['_collection'] ) &&
		 sizeof( $item['_collection'] ) > 0 ) {
		
		$items = array();
		foreach($item['_collection'] as $i => $row){
			$items[] = array(
				"@type" 	=> "ListItem",
				"position"	=> $i+1,
				"url" 		=> $row,
			);
		}
		
		global $wp;
		$url = home_url( add_query_arg( array(), $wp->request ) );
		if ( !( sizeof($items) == 1 &&
		        $items[0]['url'] == $url) ) {

			if ( ! $item['mainEntity'] )
				$item['mainEntity'] = array();
			
			$item['mainEntity'][] = array(
				"@type" 		  => "ItemList",
				"itemListElement" => $items
			);
			
		}
		
	}
	
	
	
	// Audience
	// set properties depending on type
	// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
	if ( substr($item['@id'], 0, 5) == '#audi' ) {
		
		if ( $item['@type'] != 'BusinessAudience' ) {
			unset( $item['numberOfEmployees'] );
			unset( $item['yearlyRevenue'] );
		}
		
		if ( $item['@type'] != 'PeopleAudience' ) {
			unset( $item['suggestedGender'] );
			unset( $item['requiredGender'] );
			unset( $item['suggestedAge'] );
			unset( $item['requiredMinAge'] );
			unset( $item['requiredMaxAge'] );
		}
		
		if ( $item['@type'] != 'EducationalAudience' ) {
			unset( $item['educationalRole'] );
		}
		
	}

	
	
	// Video views
	// sets interactionStatistic
	// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
	if ( isset( $item['video'] ) &&
		 is_array($item['video']) &&
		 sizeof($item['video']) ){
		foreach($item['video'] as $i => $row){
			if ( ! isset($row['@type']) || $row['@type'] == 'CreativeWork' )
				$item['video'][ $i ]['@type'] = 'VideoObject';

			if (isset($row['views']) && $row['views']){
				$item['video'][$i]['interactionStatistic'] = array(
					
					"@type"	=> "InteractionCounter",
					"interactionType" => array(
						"@type" => "WatchAction"
					),
					"userInteractionCount" => $row['views']
				);
				unset( $item['video'][$i]['views'] );
			}
		}
	}
		
	
	
	// Sitelinks search box
	// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
	if ( isset($item['@type']) && $item['@type'] == 'WebSite' ) {
		if ( ! isset( $item['_show_search'] ) )
			$item['_show_search'] = 0;
		
		if ( $item['_show_search'] &&
		     is_front_page() ) {
			unset( $item['_show_search'] );
			$item["potentialAction"] = array (
				"@type"	 => "SearchAction",
				"query-input" => "required name=search_term_string",
				"target" => array(
					'@type' => "EntryPoint",
					'urlTemplate' => get_site_url() . "/?s={search_term_string}"
				),
				"url" => get_site_url(),
			);

		} else {
			unset( $item['potentialAction'] );
		}
		unset( $item['search_url'] );
	}

	
		
	
	// Video
	// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
	if ( isset($item['video']) && is_array($item['video']) ) {
		foreach( $item['video'] as $i => $vid){
			if ( ! isset($vid['thumbnailUrl']) ||
			     ! $vid['thumbnailUrl'] ) {
				$item['video'][$i]['thumbnailUrl'] =
					ACSC_URL . "img/video_placeholder.png";
			}
		}
	
		
	}
		
	
	// Review
	// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
	if ( isset($item['review']) && is_array($item['review']) ) {
		
		foreach( $item['review'] as $i => $row ){
		if ( ! $row['reviewRating'] )
			$row['reviewRating'] = array();
		if ( ! $row['reviewRating']['worstRating'] )
			$row['reviewRating']['worstRating'] = 1;
		if ( ! $row['reviewRating']['bestRating'] )
			$row['reviewRating']['bestRating']  = 5;
		$item['review'][$i] = array(
			"@type"	=> "Review",
			"author"	=> $row['author'],
			"reviewBody"	=> $row['reviewBody'],
			"reviewRating" => array(
				"@type"	=> "Rating",
				"ratingValue"	=> $row['reviewRating']['ratingValue'],
				"bestRating"	=> $row['reviewRating']['bestRating'],
				"worstRating"	=> $row['reviewRating']['worstRating'],
			),
			"datePublished"	=> $row['datePublished'],
		);
		}
		
	}
	
	
	// Rating - sets aggregateRating
	// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
	if ( isset($item['aggregateRating']) ) {
		
		$row = $item['aggregateRating'];
		if ( ! isset( $row['worstRating'] ) )
			$row['worstRating'] = 1;
		if ( ! isset( $row['bestRating'] ) )
			$row['bestRating']  = 5;

		if ( ! isset($row['ratingValue']) ) $row['ratingValue'] = 5;
		if ( ! isset($row['ratingCount']) ) $row['ratingCount'] = 0;
		$item['aggregateRating'] = array(
			"@type"	=> "AggregateRating",
			"ratingValue"	=> $row['ratingValue'],
			"ratingCount"	=> $row['ratingCount'],
			"bestRating"	=> $row['bestRating'],
			"worstRating"	=> $row['worstRating'],
		);
		//if ( ! $row['ratingCount'] )
			//unset( $item['aggregateRating'] );
	}
	
	

	// Primary Image
	// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
	if ( isset( $item['primaryImageOfPage'] ) ){
		
		if ( ! isset( $item['primaryImageOfPage']['contentUrl'] ) )
			$item['primaryImageOfPage']['contentUrl'] = "";
		if ( ! $item['primaryImageOfPage']['contentUrl'] )
			unset( $item['primaryImageOfPage'] );
	}
	
	
	// thumbnail
	// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
	if ( isset( $item['thumbnail'] ) ){
		$item['thumbnail'] = array(
			'@type' => 'ImageObject',
			'contentUrl' => $item['thumbnail']
		);
	}
	
	
	
	// Pros
	// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
	if ( isset( $item['positiveNotes'] ) ){
		$tmp = array(
			'@type'	=> 'ItemList',
			'ItemListElement' => array(),
		);
		foreach ($item['positiveNotes'] as $i => $row){
			$tmp['ItemListElement'][] = array (
				'@type' => 'ListItem',
				'position' => $i+1,
				'name' => $row,
			);
			
		}
		$item['positiveNotes'] = $tmp;
	}
	// Cons
	// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
	if ( isset( $item['negativeNotes'] ) ){
		$tmp = array(
			'@type'	=> 'ItemList',
			'ItemListElement' => array(),
		);
		foreach ($item['negativeNotes'] as $i => $row){
			$tmp['ItemListElement'][] = array (
				'@type' => 'ListItem',
				'position' => $i+1,
				'name' => $row,
			);
			
		}
		$item['negativeNotes'] = $tmp;
	}
	// Pros & Cons
	// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
	if ( isset( $item['positiveNotes'] ) ||
	     isset( $item['negativeNotes'] ) ){
		if ( ! isset( $item['review'] ) ) {
			$item['review'] = array();
		}
		if ( is_array( $item['review'] ) ) {
			$item['review'][] = array(
				'@type' => 'Review',
				'name' => $item['name'] . ' review',
				'author' => acsc_dynamic_val('{author}'),
				'positiveNotes' => $item['positiveNotes'],
				'negativeNotes' => $item['negativeNotes'],
			);
			unset( $item['positiveNotes'] );
			unset( $item['negativeNotes'] );
		}
		
	}
	
	
	
	
	// Energy Consumption
	// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
	if ( isset( $item['hasEnergyConsumptionDetails'] ) ){
		
		// set min-max if EU
		if ( $item['hasEnergyConsumptionDetails']['_standard'] == 'EUEnergyEfficiency' ) {
		// ┅┅┅┅┅┅┅┅┅
			$item['hasEnergyConsumptionDetails']['energyEfficiencyScaleMin'] = "EUEnergyEfficiencyCategoryF";
			$item['hasEnergyConsumptionDetails']['energyEfficiencyScaleMax'] = "EUEnergyEfficiencyCategoryA3Plus";
		}
		
		
		if ( ! $item['hasEnergyConsumptionDetails']['hasEnergyEfficiencyCategory'] || $item['hasEnergyConsumptionDetails']['hasEnergyEfficiencyCategory'] == 'NotCertified' ) {
		// ┅┅┅┅┅┅┅┅┅
			
			// unset if no category
			unset( $item['hasEnergyConsumptionDetails'] );
			
		}
		
	}


	
	// Works For (Thing Wiki)
	// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
	if ( isset($item['worksFor']) && is_array($item['worksFor']) ) {

		foreach ($item['worksFor'] as $i => $row){
			if ( is_array($row) && isset($row['@type']) && $row['@type'] == 'Thing' )
				 $item['worksFor'][$i]['@type'] = 'Organization';
		}

	}
	// Affiliation (Thing Wiki)
	// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
	if ( isset($item['affiliation']) && is_array($item['affiliation']) ) {
		foreach ($item['affiliation'] as $i => $row){
			if ( is_array($row) && $row['@type'] == 'Thing' )
				$item['affiliation'][$i]['@type'] = 'Organization';
		}
	}

	
	// Geographic Area (Thing Wiki)
	// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
	if ( isset($item['geographicArea']) && is_array($item['geographicArea']) ) {

		foreach ($item['geographicArea'] as $i => $row){
			if ( is_array($row) && $row['@type'] == 'Thing' ) $item['geographicArea'][$i]['@type'] = 'AdministrativeArea';
		}

	}
	// Area Served (Thing Wiki)
	// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
	if ( isset($item['areaServed']) && is_array($item['areaServed']) ) {

		foreach ($item['areaServed'] as $i => $row){
			if ( is_array($row) && $row['@type'] == 'Thing' ) $item['areaServed'][$i]['@type'] = 'AdministrativeArea';
		}

	}
	
	
	
	
	
	//$item = acsc_property_linking( $item );

	
	return $item;
	
}
	

// Link Properties
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
function acsc_property_linking( $item ){
	global $ACSC, $post;
	
	// get post type
	$post_type = "";
	if ( is_object($post) )	$post_type = $post->post_type;
	if ( isset($ACSC['wp']['post_type']) )
		$post_type = $ACSC['wp']['post_type'];
	$post_type = strtolower( $post_type );

	
	// IS SINGLE
	if ( is_single() ) {
		
		// Add post item as mainEntity of page
		// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
		//if ( $item['@type'] == 'WebPage' ) {
		$type = "";
		if ( isset( $ACSC['options']['targets'][$post->post_type] ) )
			$type = $ACSC['options']['targets'][$post->post_type];
		
		if ( explode('-', $item['@id'])[0] == '#page' ) {
			if ( ! isset($item['mainEntity']) ||
				 ! $item['mainEntity'] )
				$item['mainEntity'] = array();
			$item['mainEntity'][] = array(
				'@id'	=> "#" . $post_type . '-' . $post->ID,
				'@type'	=> $type,
			);
			
	
		}
		
		$req_uri = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) );
		
		// On Item's Schema
		if ( explode('-', $item['@id'])[0] == '#post' ) {
		//if ( $scope == 'post' || $scope == 'items' ) {
			 //'#' . $post->post_type ) {
			
			// part of and main entity of page
			//$item['isPartOf'] =
				//array('@id'	=> '#page-' . $post->ID);
			//$item['mainEntityOfPage'] =
				//array('@id'	=> '#page-item-' . $post->ID);
			
			
			// Comments
			if ( comments_open( $post->ID ) ){
				$item['commentCount'] = get_comments_number();
				$item['potentialAction'] = array(
					"@type"	 => "CommentAction ",
					"name"	 => "Comment",
					"target" => array( urldecode( home_url( $req_uri ) ) . '#comment' )
				);
			}
					

		}
		
		
	}

	
	// Read Action	
	if ( is_single() || is_page() ) {
		if ( isset( $item['@id'] ) )
		if ( explode('-', $item['@id'])[0] == '#page' ) {
			$item['potentialAction'] = array(
				"@type"	 => "ReadAction",
		  		"target" => array(urldecode( home_url($req_uri) ))
			);
			
		}
	}
	
	return $item;
	
}
	

// Filter Values
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅	
function acsc_filter_values( $item ){
	global $ACSC, $post;
	
	
	
	// LOOP
	foreach ( $item as $key => $row ) {
		

		// FULLDATE
		// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
		if ( substr($key, 0,4) == '_fd_' ) {
			$name = substr($key, 4);
			if ( ! isset($item[ $name ]) || ! $item[ $name ] ) {
				$_date = $row['_date'];
				$_time = $row['_time'];
				if ( strlen($_date) > 10 )
					$_date = substr( acsc_dynamic_val( $row['_date'], $item, $key.'.'.$row['_date'] ), 0, 10);
				if ( strlen($_time) > 5 )
					$_time = substr( acsc_dynamic_val( $row['_time'], $item, $key.'.'.$row['_time'] ), 11, 5);
				
				$_timezone = acsc_dynamic_val( $row['_timezone'], $item, $key.'.'.$row['_timezone'] );
				
				$res = '';
				if ( $_date ) {
					$res = $_date;
					if ( $_time )
						$res .= "T" . $_time . $_timezone;
				}
				if ( $res ) $item[ $name ] = $res;
			}
			
		}


		// remove '_', empty options & '@context'
		// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
		$item = acsc_filter_empty( $item, $key, $row );
		
		
		
		// remove 'minValue', 'maxValue'
		// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
		$minmax_fields = array( 'numberOfEmployees', 'suggestedAge', 'yearlyRevenue' );
		if ( in_array( $key, $minmax_fields ) ){
			if ( ! is_array($row) ||
				(! $row['minValue'] && ! $row['maxValue'] ) ) {
				unset( $item[ $key ] );
				$row = null;
			}
		}
		
		
		
		// Opening Hours
		// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
		if ( $key == "openingHoursSpecification" ) {
			$rows = array();
			foreach( $row as $day){
				if ( !isset($day['opens']) )
					$day['opens'] = "";
				if ( !isset($day['closes']) )
					$day['closes'] = "";
				if ( $day['opens'] || $day['closes'] )
					$rows[] = $day;
			}
			if ( sizeof($rows) == 0 )
				unset( $item[ $key ] );
			else 
				$item[ $key ] = $rows;

			// seasonal hours
			if ( isset($item['_seasonalHours']) &&
				 is_array($item['_seasonalHours']) &&
				 sizeof($item['_seasonalHours']) ) {

				$item['openingHoursSpecification'] =
					array_merge( $item['openingHoursSpecification'], $item["_seasonalHours"] );
				unset( $item["_seasonalHours"] );
			}
				
		}
		

		
		
		// number Of Employees
		// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
		if ( $key == "numberOfEmployees" &&
			 isset( $item["numberOfEmployees"] ) ) {
			$tmp = $item["numberOfEmployees"];
			if ( $tmp['maxValue'] && ! $tmp['minValue'] ) {
				$tmp['value'] = $tmp['maxValue'];
				unset( $tmp['minValue'] );
				unset( $tmp['maxValue'] );
			}
			if ( $tmp['minValue'] && ! $tmp['maxValue'] ) {
				$tmp['value'] = $tmp['minValue'];
				unset( $tmp['minValue'] );
				unset( $tmp['maxValue'] );
			}
			$tmp['@type'] = 'QuantitativeValue';
			$item["numberOfEmployees"] = $tmp;

		}




		
		// Geo
		// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
		if ( $key == "geo" ) {
			
			if ( !isset($row['latitude']) )
				$row['latitude'] = "";
			if ( !isset($row['longitude']) )
				$row['longitude'] = "";
			if (  ! $row['latitude']  &&
				  ! $row['longitude'] )
				unset( $item[ $key ] );

			if ( is_array( $item['@type'] ) &&
				 in_array('OnlineBusiness', $item['@type']) )
				unset( $item[ $key ] );
		}
		
		
		// Address
		// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
		if ( $key == "address" ) {
			if (  sizeof( $item[ $key ] ) == 1 &&
			      ! isset( $item[ $key ][0] ) )
				unset( $item[ $key ] );
		}
		
		
				
		// isPartOf
		// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
		if ( $key == "isPartOf" ) {
			if (  $item[ $key ]['@id'] == '#website-1' )
				$item[ $key ]['@type'] = 'WebSite';
		}
		
		
		
		
		// How To Steps
		// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
		if ( $key == "step" ) {
			
			foreach($item[ $key ] as $i => $v){
				if (isset($v['name']) && $v['name'] && ! $v['text']){
					$v['text'] = $v['name'];
					unset( $v['name'] );
					$item[ $key ][ $i ] = $v;
				}
			}
			
		}
		


		if ( $key == "offers" && is_array($item[ $key ]) ) {

			foreach( $item[ $key ] as $ii => $row2 ){
				if ( isset($row2['shippingDetails']) &&
				     is_array($row2['shippingDetails']) ){
						foreach( $row2['shippingDetails'] as $iii => $row3 ){
							if ( isset($item['weight']) ) $row3['weight'] = $item['weight'];
							if ( isset($item['width']) )  $row3['width']  = $item['width'];
							if ( isset($item['height']) ) $row3['height'] = $item['height'];
							if ( isset($item['depth']) )  $row3['depth']  = $item['depth'];
							
							if ( isset($row3['shippingRate']) ) {
								$row3['shippingRate']['@type'] = 'MonetaryAmount';
							}
							if ( isset($row3['deliveryTime']) ) {
								$row3['deliveryTime']['@type'] = 'ShippingDeliveryTime';
								if ( isset($row3['deliveryTime']['handlingTime']) ) {
									$row3['deliveryTime']['handlingTime']['@type'] = 'QuantitativeValue';
									$row3['deliveryTime']['handlingTime']['unitCode'] = 'd';
								}
								if ( isset($row3['deliveryTime']['transitTime']) ) {
									$row3['deliveryTime']['transitTime']['@type'] = 'QuantitativeValue';
									$row3['deliveryTime']['transitTime']['unitCode'] = 'd';
								}
							}
							$row2['shippingDetails'][$iii] = $row3;
						}
						$item[$key][$ii] = $row2;
				}
			}
		}
		
		
		
	}
	
	
	/*
	// ImageObjects to array of URLs
	if ( $item['@type'] == 'Event' && $item['image'] ) {
		$tmp = array();
		foreach( $item['image'] as $img ){
			$tmp[] = $img['contentUrl'];
		}
		$item['image'] = $tmp;
	}
	*/

	
	
	return $item;
}
function acsc_filter_empty( $item, $key, $row ){
	global $ACSC;

		
	// remove '_', empty options & '@context'
	if ( $row != 0 && ! $row ||
		 substr($key,0,1) == '_' ||
		 $key == 'form_title' ||
		 $key == '@context' ) {
		unset( $item[$key] );
		return $item;
	}

	
	// if $row is array
	if ( is_array( $row ) ){
		if ( sizeof($row) == 0 ){
			
			// unset if empty
			unset( $item[ $key ] );

		} else {
			// loop array props
			foreach($row as $key2 => $row2){
				
				// unset if empty
				if ( $row2 != 0 && ! $row2 )
					unset( $row[ $key2 ] );

				// unset if '_'
				if ( substr($key2,0,1) == '_' ){
					unset( $row[ $key2 ] );
				}

				// if $row2 is array
				if ( is_array( $row2 ) ){
					// unset if {@id:''}
					if ( isset($row2['@id']) &&
						 $row2['@id'] == '' )
						unset( $row[ $key2 ] );
					
					foreach($row2 as $key3 => $row3){
						// if $row3 is array
						if ( is_array( $row3 ) ){
							// unset if {@id:''}
							if ( isset($row3['@id']) ) {
								 if ( $row3['@id'] == '' )
									unset( $row2[ $key3 ] );
								if ( substr($row3['@id'], 0, 1) == '#' ) {
									$row3['@id'] = $ACSC['sys']['site_url'] .'/'. $row3['@id'];
									$row2[ $key3 ] = $row3;
								}
							}
							
							foreach($row3 as $key4 => $row4){
								// unset if {@id:''}
								if ( isset($row4['@id']) &&
									 $row4['@id'] == '' )
									unset( $row3[ $key4 ] );
								
								// unset if empty
								if ( $row4 != 0 && ! $row4 )
									unset( $row3[ $key4 ] );

								// unset if '_'
								if ( substr($key4,0,1) == '_' ){
									unset( $row3[ $key4 ] );
								}
								
								
							}
							$row2[ $key3 ] = $row3;

						}
						
						// unset if empty
						if ( $row3 != 0 && ! $row3 )
							unset( $row2[ $key3 ] );

						// unset if '_'
						if ( substr($key3,0,1) == '_' ){
							unset( $row2[ $key3 ] );
						}
						
						
					}
					$row[ $key2 ] = $row2;
				}

			}
			$item[ $key ] = $row;
		}
	}
	
	
	
	// rating
	if ( $key == "aggregateRating" ){
		$row["@type"] = "AggregateRating";
		if ( ! isset( $row['ratingCount'] ) || ! is_numeric( $row['ratingCount'] ) )
			$row['ratingCount'] = 0;
		if ( ! isset( $row['ratingValue'] ) || ! is_numeric( $row['ratingValue'] ) )
			$row['ratingValue'] = 0;
		$item[$key] = $row;
		if ( $row['ratingCount'] == 0 ) unset( $item[$key] );
	}

	
	

	return $item;
}




// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅	
function acsc_scan_for_video_ids($item, $ids=array(), $fullkey=""){
	
if ( isset($item['@type']) && $item['@type'] == "Recipe" )
	
	foreach($item as $key => $row){
		if ( $key == '_dynamic' ) continue;
		if ( isset($item['_version']) ) $fullkey = $key;
		else {
			$row_idx = explode('.', $fullkey);
			$row_idx = $row_idx[ sizeof($row_idx)-1 ];
			if ( is_numeric($row_idx) &&
				 intval($row_idx) != 0 ){
				$fullkey = explode('.', $fullkey);
				array_pop( $fullkey );
				if ( is_numeric($fullkey[sizeof($fullkey)-1]) )
					array_pop( $fullkey );
				$fullkey = implode('.', $fullkey);
				$fullkey .= '.'.$row_idx;
			}
		}
		
		if ( $key == 'video' ) {
			//if ( $fullkey ) $fullkey .= '.';
			//$fullkey .= $key;
			
			if ( isset($row['@id']) )
				$ids[$fullkey.'.video'] = $row['@id'];
			
		} else if ( is_array($row) ) {

			
			if ( ! isset($item['_version']) ) {

				if ( $fullkey ) $fullkey .= '.';
				$fullkey .= $key;
			}
			$ids = acsc_scan_for_video_ids($row, $ids, $fullkey);

		}
		


		
	}
	
	
	return $ids;
}
function acsc_replace_video_ids( $item ){
	global $ACSC;
			

	$ids = acsc_scan_for_video_ids( $item );

	$videos = array();
	if ( isset($ACSC['wp']) && isset($ACSC['wp']['videos']) )
	foreach($ACSC['wp']['videos'] as $vid){
		if ( isset($vid['@id']) ) $videos[] = $vid;
		
		if ( isset($vid['hasPart']) ){
			foreach ($vid['hasPart'] as $clip){
				if ( isset($clip['@id']) )
					$videos[] = $clip;
			}
		}
	}
	
	
	
	foreach ($ids as $key => $vID){
		$obj = array_column($videos, null, '@id')[$vID] ?? false;
		$item = acsc_set_by_key($item, $key, $obj);
		
	}
	
	
	
	/*
	if ( is_array($videos) ) {
		foreach($videos as $vid){
			if ( isset($vid['@id']) &&
				 $vid['@id'] == $val ) {
				$val = $vid;
			}
			if ( isset($vid['hasPart']) ){
				foreach ($vid['hasPart'] as $clip){
					if ( isset($clip['@id']) &&
						 $clip['@id'] == $val )
						$val = $clip;

				}
			}

		}
	}
	*/
	
	return $item;
	
	
}
function acsc_set_by_key($conf, $path, $value=null) {
	
	// We split each word seperated by a dot character
    $paths = explode('.', $path);
    if ($value === null) {
        // Get
        $result = $conf;
        foreach ($paths as $path) {
            $result = $result[$path];
        }
        return $result;
    }
    // Set
    if (!isset($conf)) $conf = array(); // Initialize array if $conf not set
    $result = &$conf;
    foreach ($paths as $i=>$path) {
        if ($i < count($paths)-1) {
            if (!isset($result[$path])) {
                $result[$path] = array();
            }
            $result = &$result[$path];
        } else {
            $result[$path] = $value;
        }
    }
	
    return $conf ;
}

?>