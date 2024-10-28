<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      






// ≣≣≣≣ The Events Calendar
// ≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣
function acsc_the_events_calendar( $mode="" ){
	global $ACSC;
	if ( ! acsc_is_active('the-events-calendar') ) return false;
	acsc_trc('--- The Events Calendar');
	
	// TARGETING
	if ( acsc_is_enabled('tec') ) {
		
		$ACSC['options'] = acsc_opt();
		// EVENT
		if ( ! isset($ACSC['options']['targets']['tribe_events']) ||
			 ! $ACSC['options']['targets']['tribe_events'] ) {
			$ACSC['options']['targets']['tribe_events'] = 'Event';
			update_option( "ACSC-options", $ACSC['options'], false );
		}
		// ORGANIZER
		if ( ! isset($ACSC['options']['targets']['tribe_organizer']) ||
			 ! $ACSC['options']['targets']['tribe_organizer'] ) {
			$ACSC['options']['targets']['tribe_organizer'] = 'persons';
			update_option( "ACSC-options", $ACSC['options'], false );
		}
		// VENUE
		if ( ! isset($ACSC['options']['targets']['tribe_venue']) ||
			 ! $ACSC['options']['targets']['tribe_venue'] ) {
			$ACSC['options']['targets']['tribe_venue'] = 'Place';
			update_option( "ACSC-options", $ACSC['options'], false );
		}
		
	}
	
	
	if ( ! $mode ) {
		
		// TEMPLATE
		acsc_the_events_calendar_template();

		
	}
	
	
	
	// HOOKS
	acsc_the_events_calendar_hooks( $mode );
	
	
}


// HOOKS
// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
function acsc_the_events_calendar_hooks( $mode ){
	global $ACSC;
	
	// HOOK on acsc-WP-meta
	/*
	if ( ! $ACSC['hooks']['acsc-WP-meta'] )
		$ACSC['hooks']['acsc-WP-meta'] = array();
	
	$ACSC['hooks']['acsc-WP-meta'][] = 
		acsc_hook_tec_acsc-WP-meta;
	
	
	// HOOK on schemaTemplates
	if ( ! $ACSC['hooks']['schemaTemplates'] )
		$ACSC['hooks']['schemaTemplates'] = array();
	
	$ACSC['hooks']['schemaTemplates'][] = 
		acsc_hook_schemaTemplates_events_calendar;
	
	*/
	
	
	if ( isset($ACSC['options']['plugins']['tec_enable']) &&
		 $ACSC['options']['plugins']['tec_enable'] ){
		
		
			

	
	// parsing extra WP meta
	// hook: acsc-WP-meta ( $data ) - on wp/post-data.php
	// modifies WP $data
	// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	if ( ! $mode || $mode == 'before-wp' )
	add_filter( 'acsc-WP-meta', 'acsc_tec_WP_meta' );
	// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	if ( ! $mode || $mode == 'before-wp' )
	add_filter( 'acsc-WP-labels', 'acsc_tec_WP_meta_labels' );
	// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	


		
		// HOOKS on acsc_schema_templates()
		// -- sets plugin dynamic values to Product template
		// -- return product-0 template
		// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		if ( ! $mode )
		acsc_hook('schemaTemplates',
			'acsc_hook_schemaTemplates_events_calendar');
		// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	
		
	}
	
	
	$text = '<h3><b>PLUGIN integrated</b> : The Events Calendar</h3>';
	$note_target = '.ACSC-BODY[alt="home"]';
	$note_target = '.ACSC-NOTE';
	$clss = 'note-plugin note-plugin-tec acsc-sc active';
	
	if ( !isset($ACSC['options']['plugins']['tec_enable']) ||
		 !$ACSC['options']['plugins']['tec_enable'] ){
		$note_target = '.ACSC-notes';
		$text = '<h3><b>PLUGIN detected</b> : The Events Calendar</h3> <p>Enable connection with this plugin.</p>';
		$clss = 'note-plugin note-plugin-tec acsc-sc';
	
	}
	
	
	// note on admin home screen
	if ( ! $mode )
	acsc_hook('js_admin_home', 'admin_note', array(
		'icon'	=> 'extension',
		'clss'	=> $clss,
		'text'	=> $text,
		'controls' 	=> array(
			array(
				'type'	=> 'toggle',
				'name'	=> 'plugins.tec_enable',
				'label'	=> 'enabled',
				'autosave' => true,
			),
		),
	));
		
	// options on settings
	if ( ! $mode )
	acsc_hook('js_admin_settings', 'settings', array(
		array( 'type'	=> 'seperator' ),
		array(
			'type'	=> 'toggle',
			'name'	=> 'plugins.tec_enable',
			'label'	=> 'The Events Calendar',
			'note'	=> 'Enable connection with <b>The Events Calendar</b> plugin.<br>The Event schema will be assigned on every Event page and will automatically get values from the Event.<br>(refresh the page after saving options)',
		),
	));
	
	

		
	


}



// parse WP Product meta for The Events Calendar
// ------------------------------------------------------
// hook: acsc-WP-meta ( $data ) - on wp/post-data.php
// modifies WP $data
function acsc_tec_WP_meta( $data ){
	// HOOKS on parse WP
	// -- defines Event Type (physical/online)
	// -- parses Venue Data
	if ( isset($post) && $post ) {
		if ( $post->post_type != 'tribe_events' ) return $data;
	} else {
		if ( ! isset($data['_ptype']) || $data['_ptype'] != 'tribe_events' )  return $data;
	}

	acsc_trc('    acsc_tec_WP_meta');
	
	// Get Meta Labels
	//$data['_dynamic_labels'] = acsc_tec_WP_meta_labels();
	



	// define Event Type
	$data['_EventType'] = '';
	
	// parse Venue Meta
	// ------------------------------------------
	if ( isset($data['_EventVenueID']) )
		$vid = $data['_EventVenueID'];
	if ( isset($vid) && $vid ) {
		$venue = get_post( $vid );
		$venue_meta = get_post_meta( $vid );
		if ( $venue_meta ) {
			foreach($venue_meta as $key => $meta){
				$data[ $key ] = $meta[0];
			}
		}
		$data['_VenueName'] = '';
		if ( $venue )
			$data['_VenueName'] = $venue->post_title;
	}
	
	
	// parse Organizer Meta
	// ------------------------------------------
	if ( isset($data['_EventOrganizerID']) )
		$oid = $data['_EventOrganizerID'];
	if ( isset($oid) && $oid ) {
		$org = get_post( $oid );
		$org_meta = get_post_meta( $oid );
		if ( $org ) $name = $org->post_title;
		$data['_EventOrganizer'] = array(
			"@type" 	=> 'LocalBusiness',
			"name"		=> $name,
			"email"		=> $org_meta['_OrganizerEmail'][0],
			"url"		=> $org_meta['_OrganizerWebsite'][0],
			"telephone" => $org_meta['_OrganizerPhone'][0],
		);
		$data['_OrganizerName'] = $name;
			
	}
	
	// set Event Mode
	$type = 'MixedEventAttendanceMode';
	if ( ( ! isset($data['_tribe_events_virtual_url']) ||
			! $data['_tribe_events_virtual_url'] )  &&
			isset($data['_VenueAddress']) )
			$type = 'OfflineEventAttendanceMode';
		
	if ( isset($data['_tribe_virtual_events_type']) &&
			isset($data['_tribe_events_virtual_url']) &&
			$data['_tribe_events_virtual_url'] ) {
	
		if ( $data['_tribe_virtual_events_type'] == 'virtual' ) {
			$type =	'OnlineEventAttendanceMode';
		} else if ( $data['_tribe_virtual_events_type'] == 'hybrid' ) {
			$type =	'MixedEventAttendanceMode';
		} 
	}
	$data['_tribe_virtual_events_type'] = $type;
	

	
	return $data;
}

// Get Meta Labels
// ------------------------------------------------------	
function acsc_tec_WP_meta_labels( $data = array() ){
	if ( ! acsc_is_active('the-events-calendar') ) return array();
    if ( ! is_admin() && ! wp_doing_cron() ) return array(); // Front End

	acsc_trc('    acsc_tec_WP_meta_labels');

	
	$meta = array(
		
		'TITLE-ROW-tec'		=> 'EVENT META',

		'_EventURL'			=> 'Event URL',
		'_EventStartDate'	=> 'Event Start Date',
		'_EventEndDate'		=> 'Event End Date',
		'_EventOrganizer'	=> 'Event Organizer',
		'_EventCost'		=> 'Event Cost',
		'_EventCurrencyCode'=> 'Event Currency',
		'_tribe_events_status'=> 'Event Status',
		'_tribe_virtual_events_type'=> 'Event Mode',
		'SPACE-ROW-tec'        		=> '',

		'TITLE-ROW-tec2'		=> 'Event Venue',
		'_VenueName'		=> 'Venue Name',
		'_VenueAddress'		=> 'Venue Address',
		'_VenueZip'			=> 'Venue Zip',
		'_VenueCity'		=> 'Venue City',
		'_VenueStateProvince'=> 'Venue State',
		'_VenueCountry'		=> 'Venue Country',
		'_VenuePhone'		=> 'Venue Phone',
		'_VenueURL'			=> 'Venue URL',
		'SPACE-ROW-tec2'        		=> '',

		'TITLE-ROW-tec23'	=> 'Event Organizer',
		'_OrganizerPhone'	=> 'Organizer Phone',
		'_OrganizerEmail'	=> 'Organizer Email',
		'_OrganizerWebsite'	=> 'Organizer Website',
		'SPACE-ROW-tec3'        		=> '',
	);


	if ( ! isset($data['meta']) ) $data['meta'] = array();
	$data['meta'] = array_merge($data['meta'], $meta );


	return $data;
}





// TEMPLATE
// ------------------------------------------------------
// -- sets plugin dynamic values to Event / Organizer / Venue templates
// -- return tribe_events-0 / tribe_organizer-0 / tribe_venue-0 to $ACSC['templates']
function acsc_the_events_calendar_template(){
	global $ACSC, $post;
    if ( wp_doing_ajax() ) return false;
	
	acsc_trc('        acsc_the_events_calendar_template');


	// TEMPLATE tribe_event
	$event = array();
	if ( isset($ACSC['templates']['Event']) )
		$event = $ACSC['templates']['Event'];
	$event['@id']  = '{post_id}';
	$event['name'] = '{post_title}';
	$event['description'] = '{post_excerpt}';
	$event['url'] = '{_EventURL}';
	$event['_fd_startDate']['_date'] = '{_EventStartDate}';
	$event['_fd_startDate']['_time'] = '{_EventStartDate}';
	$event['_fd_endDate']['_date'] = '{_EventEndDate}';
	$event['_fd_endDate']['_time'] = '{_EventEndDate}';
	
	$event['eventAttendanceMode'] = 
		'{_tribe_virtual_events_type}';
	
	$event['location'][0] = array(
		"@type" 	=> 'Place',
		"name"		=> '{_VenueName}',
		"address"	=> array(
			'streetAddress'		=> '{_VenueAddress}',
			'postalCode'		=> '{_VenueZip}',
			'addressLocality'	=> '{_VenueCity}',
			'addressRegion'		=> '{_VenueStateProvince}',
			'addressCountry'	=> '{_VenueCountry}',
		),
	);
	$event['_vlocation'][0] = array(
		"@type" => 'VirtualLocation',
		"url" 	=> '{_tribe_events_virtual_url}',
	);
	
	$event['offers'][0] = array(
		"@type" 		=> 'Offer',
		"availability"	=> 'InStock',
		"price"			=> '{_EventCost}',
		"priceCurrency"	=> '{_EventCurrencyCode}',
		"validFrom"		=> '{date_published}',
		"url"			=> '{_EventURL}',
	);
	$event['organizer'][0] = '{_EventOrganizer}';
	/*
	$event['image'][0] = '{featured_image}';
	$event['image'][0] = array(
		//'_t_contentUrl'	=> $featured,
		"@type" 		=> "ImageObject",
		'name'			=> "{featured_alt}",
		'caption'		=> "{featured_caption}",
		'contentUrl'	=> "{featured_url}",
		'thumbnail'		=> "{featured_thumb}",
		'width'			=> "{featured_w}",
		'height'		=> "{featured_h}",
	);
	*/
	$event['image'] = '{all_images}';
	
	$ACSC['templates']['tribe_events-0'] = $event;
	
	
	// TEMPLATE tribe_organizer
	$org = array();
	if ( isset($ACSC['templates']['persons']) )
		$org = $ACSC['templates']['persons'];
	$org["name"]		= '{_OrganizerName}';
	$org["email"]		= array('{_OrganizerEmail}');
	$org["telephone"]	= array('{_OrganizerPhone}');
	$org["url"]			= '{_OrganizerWebsite}';
	$org["image"]		= '{featured_url}';
	

	$ACSC['templates']['tribe_organizer-0'] = $org;
	
	
	// TEMPLATE tribe_venue
	$venue = array();
	if ( isset($ACSC['templates']['Place']) )
		$venue = $ACSC['templates']['Place'];
	$venue["name"]		= '{_VenueName}';
	$venue["telephone"]	= array('{_VenuePhone}');
	$venue["url"]			= '{ _VenueURL}';
	$venue["image"]		= '{featured_url}';
	$venue["address"]		= array(
		'streetAddress'		=> '{_VenueAddress}',
		'postalCode'		=> '{_VenueZip}',
		'addressLocality'	=> '{_VenueCity}',
		'addressRegion'		=> '{_VenueStateProvince}',
		'addressCountry'	=> '{_VenueCountry}',
	);
	

	$ACSC['templates']['tribe_venue-0'] = $venue;
	
	
	
	return $event;
}
function acsc_hook_schemaTemplates_events_calendar( $templates ){
// HOOKS on acsc_schema_templates()
// -- sets plugin dynamic values to Event template
	global $ACSC;
	
	// set plugin dynamic values to Event template
	acsc_the_events_calendar_template();
	
	return $ACSC['templates'];
}


?>