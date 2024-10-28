<?php
/**
 * The administration interface.
 *
 * @package    Actus_Deep_Schema
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}



// ┅┅┅┅┅┅┅┅ ADMIN MENU
// ┅┅┅┅┅┅┅┅ ACTUS plugins page
// ┅┅┅┅┅┅┅┅ ACTUS Deep Schema page
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
include_once ACSC_DIR . '/includes/interface/admin-menu.php';
function actus_plugins_page_load(){
	include_once ACSC_DIR . '/includes/interface/actus.php';
	include_once ACSC_DIR . '/includes/interface/acsc-actus.php';
	actus_plugins_page();
}
function acsc_deep_schema_page_load(){
	include_once ACSC_DIR . '/includes/interface/deep-schema.php';
}
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅






// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
if ( ! current_user_can('manage_options')
     || ! function_exists( 'is_admin_bar_showing' ) 
     || ! is_admin_bar_showing() ) { 

} else {
	if ( is_admin() ) {
		acsc_admin_menu();
	}
}



?>