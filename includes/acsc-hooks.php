<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      
//if ( ! current_user_can( 'manage_options' ) ) return;
// ≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣

acsc_trc('━━━━ acsc-hooks.php');

// HOOKS
// ≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣
// runs hooks for -name-
function acsc_hooks( $name, $data = "" ){
	$hooks = acsc_get_hooks( $name );
	if ( $hooks ){
		foreach( $hooks as $hook ){
			if ( function_exists( $hook ) ) {
				$data = $hook( $data );
			}
		}
	}
	
	return $data;
}
// hook to -name-
function acsc_hook( $name, $func, $data = "" ){
	global $ACSC;
	$hooks = acsc_get_hooks();
	
	if ( ! isset($hooks[ $name ]) ||
		 ! is_array($hooks[ $name ]) ) {
		$hooks[ $name ] = array();
	}
	//if ( ! in_array($func, $hooks[ $name ]) ) {
		
		$hooks[ $name ][] = $func;
	
		if ( $data ) {
			if ( ! isset( $hooks[ $name."_data" ] ) )
				$hooks[ $name."_data" ] = array();
			$hooks[ $name."_data" ][] = $data;
		}
		
	//}
	
	
	if ( ! isset( $ACSC['hooks'] ) || ! $ACSC['hooks'] )
		$ACSC['hooks'] = array();
	$ACSC['hooks'][ $name ] = $hooks[ $name ];
	if ( ! isset($hooks[ $name."_data" ]) )
		$hooks[ $name."_data" ] = array();
	$ACSC['hooks'][ $name."_data" ] = $hooks[ $name."_data" ];
	

	
}
// get hooks
function acsc_get_hooks( $name="" ){
	global $ACSC;
	
	$hooks = array();
	
	if ( isset( $ACSC['hooks'] ) )
		$hooks = $ACSC['hooks'];
	
	/*
	if ( isset( $_POST['hooks'] ) )
		$hooks = array_merge_recursive($hooks, map_deep( wp_unslash( $_POST['hooks'] ), 'sanitize_text_field' ));
	*/

	if ( $name ) {
		if ( ! isset( $hooks[ $name ] ) )
			$hooks[ $name ] = array();
		return $hooks[ $name ];
	} else {
		return $hooks;
	}
}
// ≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣

// acsc_hooks()
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
// ----- acsc-WP-meta
// wp/post-data.php			>>> acsc_get_WP_meta	

// ----- schemaTemplates
// acsc-data.php			>>> acsc_schema_templates	


// acsc_hook()
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
// the-events-calendar.php	>>> acsc_the_events_calendar_hooks
// ----- acsc-WP-meta
// ----- schemaTemplates
// ----- js_admin_home
// ----- js_admin_settings

// woocommerce.php	>>> acsc_woocommerce_hooks
// ----- acsc-WP-meta
// ----- schemaTemplates
// ----- js_admin_home
// ----- js_admin_settings


?>