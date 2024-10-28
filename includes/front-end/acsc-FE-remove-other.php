<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      


acsc_trc('━━━━ acsc-FE-remove-other.php');


add_filter('the_seo_framework_receive_json_data',
		   'acsc_remove_the_seo_f_schema');
add_filter('wpseo_json_ld_output',
		   'acsc_remove_yoast_schema', 10, 1);
add_filter( 'rank_math/json_ld', 
			'acsc_remove_rankmath_schema', 9999, 2 );

add_filter( 'tribe_json_ld_event_object', 
			'acsc_remove_tribe_events_schema', 9999, 3 );
add_filter( 'tribe_json_ld_place_object', 
			'acsc_remove_tribe_events_schema', 9999, 3 );
add_filter( 'tribe_json_ld_person_object', 
			'acsc_remove_tribe_events_schema', 9999, 3 );

add_filter('cooked_schema_html',
		   'acsc_remove_cooked_schema', 10, 1);

	
	
// Remove Other Plugin's Schemas
// ≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣

// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
function acsc_remove_other_schemas() {
	
	if ( ! acsc_check_disable_other() ) return;
	
	acsc_remove_woocommerce_schema();
	
		
}
function acsc_remove_woocommerce_schema() {
	global $ACSC;

	if ( ! is_plugin_active('woocommerce/woocommerce.php') ) 
		return;

	remove_action ('wp_footer',
				   array (WC () -> structured_data,
				   'output_structured_data'), 10);
	
}
function acsc_remove_the_seo_f_schema( $data = "" ) {
	if ( ! acsc_check_disable_other() ) return $data;
	return array();
}
function acsc_remove_yoast_schema($data){
	if ( ! acsc_check_disable_other() ) return $data;
	return false;
}
function acsc_remove_rankmath_schema($data, $jsonld){
	if ( ! acsc_check_disable_other() ) return $data;
	return array();
}
function acsc_remove_tribe_events_schema( $data, $args, $post ){
	if ( ! acsc_check_disable_other() ) return $data;
	return (object)array();
}
function acsc_remove_cooked_schema( $data = "" ) {
	if ( ! acsc_check_disable_other() ) return $data;
	return false;
}
// ≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣
function acsc_check_disable_other(){
	global $ACSC, $post;
	
	// check if plugin is enabled
	if ( ! acsc_opt('enable') )
		return false;
	
	// check disable_other_schemas option
	if ( ! acsc_opt('disable_other_schemas') && $post &&
	      $post->post_name != 'ads-view-schema' )
		return false;
	
	return true;
}


?>