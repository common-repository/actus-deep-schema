<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// ADMIN MENU
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
function acsc_admin_menu(){
	// Adds ACTUS menu on admin panel
	if ( !function_exists( 'actus_menu' ) ) {
		function actus_menu(){
			add_menu_page( 
				'ACTUS Plugins',
				'ACTUS',
				'manage_options',
				'actus-plugins',
				'actus_plugins_page_load',
				ACSC_URL . 'img/actus_white_20.png',
				66
			);
		}
		if ( is_admin() ) {
			add_action( 'admin_menu', 'actus_menu' );
		}
	}

	// Adds submenu on ACTUS menu
	if ( !function_exists( 'acsc_submenu' ) ) {
		function acsc_submenu() {
			add_submenu_page(
				'actus-plugins', 
				__('Deep Schema', 'actus-deep-schema'), 
				'Deep Schema', 
				'manage_options', 
				'actus-deep-schema', 
				'acsc_deep_schema_page_load'
			);
		}
		if ( is_admin() ) {
			add_action( 'admin_menu', 'acsc_submenu' );
		}
	}
}





?>