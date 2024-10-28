<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      
acsc_trc('━━━━ wp/global-data.php');





// gets WP global data
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
function acsc_get_WP_global(){
	global $ACSC;
    if ( wp_doing_ajax() ) return false;

	acsc_trc( "━━━━━━━━━━━━━ WP Global" );

	// logo
	$custom_logo = acsc_wp_logo();
	
	// locale
	$locale = get_bloginfo("language");
	$locale = explode('-', $locale)[0];
	
	// set wp global website data
	$data = array(
		'site_url'	  => get_site_url() . '/',
		'site_name'	  => get_bloginfo('name', 'display'),
		'site_descr'  => get_bloginfo('description', 'display'),
		'site_logo'   => $custom_logo,
		'site_image'  => $custom_logo,
		'site_locale' => $locale,
		'locale'  	  => $locale,
		'timezone' 	  => acsc_wp_timezone(),
		'breadcrumb'  => acsc_wp_breadcrumb(),
		'year'  	  => date("Y"),
	);

	acsc_trc( "━━━━━━━━━━━━━ WP Global - ".$data['site_name'] );

	// external plugins
	include_once(__DIR__.'/helpers.php');
	include_once(__DIR__.'/../acsc-plugins.php');
	//acsc_external_plugins1('before-wp');



	// HOOK: acsc-WP-global
    // ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	$data = apply_filters( 'acsc-WP-global', $data );
    // ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	


    return $data;
	

}
add_action('wp_ajax_acsc_get_WP_global', 'acsc_get_WP_global');
add_action('wp_ajax_nopriv_acsc_get_WP_global', 'acsc_get_WP_global' );




// gets WP global labels
// -------------------------------------
// called from acsc_DATA() -> acsc-init-data.php
// used in deep-schema-premium.php -> acsc_DATA()
// modifies $ACSC['sys']['dynamic_labels']
// -------------------------------------
function acsc_get_WP_global_labels(){
	global $ACSC;
    if ( wp_doing_ajax() ) return false;
    if ( ( ! is_admin() || wp_doing_ajax() ) && ! wp_doing_cron() ) return false; // Front End
	acsc_trc( "━━━━━━━━━━━━━ WP Global labels" );



    // initialize $ACSC['sys']['dynamic_labels']
    $vars = array();
	if ( isset($ACSC['sys']['dynamic_labels']) ) $vars = $ACSC['sys']['dynamic_labels'];
	if ( ! isset($vars['global']) )  $vars['global']  = array();
	if ( ! isset($vars['post']) )    $vars['post']    = array();
	if ( ! isset($vars['archive']) ) $vars['archive'] = array();
	if ( ! isset($vars['meta']) ) 	 $vars['meta']    = array();
	if ( ! isset($vars['all']) ) 	 $vars['all']     = array();
	if ( ! isset($vars['taxonomies']) ) $vars['taxonomies'] = array();
	

    // set Website Data labels
	$vars['global'] = array_merge($vars['global'], array(
        'TITLE-ROW-site' => 'WEBSITE DATA',
		'locale'	=> 'Language',
		'timezone'	=> 'Timezone',
		'site_url'	=> 'Site Url',
		'site_name'	=> 'Site Name',
		'site_descr'=> 'Site Description',
		'site_image'=> 'Site Image',
		'site_logo'	=> 'Site Logo',
		'year'		=> 'Year',
        'SPACE-ROW-site' => '',
	));

	
	// Taxonomies
	foreach ( $ACSC['tax'] as $tax => $tax_data ) {
		$vars['taxonomies'][ $tax ] = array();
		foreach ( $tax_data as $key => $row ) {
			$vars['taxonomies'][ $tax ][ $key ] = $row['label'];
		}
		$vars['all'] = array_merge( $vars['taxonomies'][ $tax ], $vars['all'] );
	}
	



	
    // HOOK: acsc-WP-global-labels
    // ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
    $vars = apply_filters( 'acsc-WP-global-labels', $vars );
    // ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
    $vars = apply_filters( 'acsc-WP-labels', $vars );
    // ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■

 
	
    // create ALL array
	$vars['all'] = array_merge(
		$vars['global'],
		$vars['post'],
		$vars['archive'],
		$vars['meta'],
		$vars['all'],
	);
    // result
	$ACSC['sys']['dynamic_labels'] = $vars;

	return $vars;
}




