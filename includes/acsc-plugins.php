<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


//global $ACSC, $post, $plg_count;
//if ( ! isset($ACSC['options']) ) return;


// ┅┅┅┅┅┅┅┅ Woocommerce
// ┅┅┅┅┅┅┅┅ The Events Calendar
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
include_once(__DIR__ . '/plugins/woocommerce.php');
include_once(__DIR__ . '/plugins/cooked.php');
include_once(__DIR__ . '/plugins/the-events-calendar.php');
include_once(__DIR__ . '/plugins/learnpress.php');
include_once(__DIR__ . '/plugins/seo-plugins.php');
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅

 acsc_trc('━━━━ acsc-plugins.php');

// EXTERNAL PLUGIN DATA
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
function acsc_external_plugins1( $mode="" ){
acsc_trc( 'acsc_external_plugins1' );
	global $ACSC;
	
	acsc_woocommerce( $mode );
	acsc_the_events_calendar( $mode );
	acsc_cooked( $mode );
	acsc_learnpress( $mode );
	acsc_seo_plugins( $mode );
	
}
function acsc_external_plugins(){
acsc_trc( 'acsc_external_plugins' );
	global $ACSC;
	
	//if ( ! is_array($ACSC['options']['modules_active']) )
		//return;
	//if ( sizeof($ACSC['options']['modules_active']) == 1 )
		//return;
	
	acsc_woocommerce();
	acsc_the_events_calendar();
	acsc_cooked();
	acsc_learnpress();
	acsc_seo_plugins();
	
}
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
acsc_external_plugins();

 

// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
function acsc_is_active( $name ){
	if ( is_plugin_active("$name/$name.php") ) return true;
	return false;
}
function acsc_is_enabled( $name ){
	global $ACSC;
	if ( isset($ACSC['options']['plugins'][$name.'_enable']) &&
		 $ACSC['options']['plugins'][$name.'_enable'] ) return true;
	return false;
}

?>