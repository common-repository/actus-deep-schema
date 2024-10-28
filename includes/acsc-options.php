<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
add_action( 'current_screen', 'acsc_opt_init' );
if ( ! is_admin() ) add_action( 'wp', 'acsc_opt_init' );



// Get Options
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
function acsc_opt( $name="" ){
	global $ACSC;
	$opt = get_option( 'ACSC-options' );
	
	$ACSC['options'] = $opt;
	
	$result = null;
	if ( $name=='keys' ) $result = array_keys( $opt );
	if ( $name && isset($opt[ $name ]) )
		$result = $opt[ $name ];
	if ( ! $name ) $result = $opt;
	
	return $result;
}

// Save Option
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
function acsc_opt_save( $name="", $value="" ){
	if ( ! $name ) return false;
	global $ACSC;
	
	$opt = acsc_opt();
	
	if ( is_string( $name ) ){
		$opt[ $name ] = $value;
	} else {
		$opt = $name;
	}
	update_option( 'ACSC-options', $opt, false );
		
	
	$ACSC['options'] = $opt;
	
	return true;
}




// Init Options
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
function acsc_opt_init(){
	global $ACSC;
	
	$opt = acsc_opt();
	
	// create options if don't exist
	if ( ! $opt ) {
		$opt = array(
			'version'		  	=> ACSC_VERSION,
			'enable' 			=> 1,
			'youtube_api_key' 	=> '',
			'console_admin' 	=> 1,
			'console_all' 		=> 0,
			'tips_enable' 		=> 1,
			'user_roles' 		=> ['administrator'],
			
			'modules_active'  => array( 'basic' ),
			'modules' => array(
				array(
					'name'	  => 'Basic',
					'slug'    => 'basic',
					'version' => ACSC_VERSION,
					'forms' => array(
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
					),
					'items' => array(
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
					),
					'itemsTitles' => array(
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
					),
				),
				array(
					'name'	  => 'Premium',
					'slug'    => 'premium',
					'version' => ACSC_VERSION,
					'forms' => array(
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
					),
					'items' => array(
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
					),
					'itemsTitles' => array(
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
					),
				),
			),
			'plugins' => array(),
			'pages'   => array(),
			'targets' => array(
				'post'	=> 'Article',
				//'services'	=> 'Service',
				//'tribe_events'	=> 'Event',
			),
		);
		
	
	
		update_option( "ACSC-options", $opt, false );
	}
	
	acsc_opt_new_versions();
	
	
	if ( ! isset($opt['plugins']) )
		$opt['plugins'] = array();
	
	
	// if selected thumb_size no longer exists
	// added on ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅ 1.0.1
	if ( ! in_array( acsc_opt('thumb_size'), 
		array_keys(wp_get_registered_image_subsizes())) ) {
		acsc_opt_save('thumb_size', 'thumbnail');
	}
	

	// Add Vehicle and VacationRental schemas
	// added on ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅ 1.3
	if ( ! in_array('Vehicle', $opt['modules'][1]['forms'] ) ) {
		$opt['modules'][1]['forms'][] = 'Vehicle';
		$opt['modules'][1]['items'][] = 'Vehicle';
		$opt['modules'][1]['itemsTitles'][] = 'Vehicle';
		acsc_opt_save('modules', $opt['modules']);
	}
	if ( ! in_array('VacationRental', $opt['modules'][1]['forms'] ) ) {
		$opt['modules'][1]['forms'][] = 'VacationRental';
		$opt['modules'][1]['items'][] = 'VacationRental';
		$opt['modules'][1]['itemsTitles'][] = 'Vacation Rental';
		acsc_opt_save('modules', $opt['modules']);
	}
	if ( ! in_array('Movie', $opt['modules'][1]['forms'] ) ) {
		$opt['modules'][1]['forms'][] = 'Movie';
		$opt['modules'][1]['items'][] = 'Movie';
		$opt['modules'][1]['itemsTitles'][] = 'Movie';
		acsc_opt_save('modules', $opt['modules']);
	}
	
	
	
	
	$opt['disable_other_schemas'] = false;
	$ACSC['options'] = $opt;
}

// Options added in new versions
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
function acsc_opt_new_versions(){
	// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
	$options = acsc_opt();

	
	if ( version_compare(ACSC_VERSION,
						 $options['version']) == 0 )
		return;
	
	

	// thumb_size ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅ 1.0.1
	if ( ! isset( $options['thumb_size'] ) ) {
		$options['thumb_size'] = 'thumbnail';
	}
	


	// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅ UPDATE OPTIONS & VERSION
	$options['version'] = ACSC_VERSION;
	acsc_opt_save( $options );
	
}