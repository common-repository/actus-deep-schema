<?php
/**
 * The administration options.
 *
 * @package    Actus_Deep_Schema
 * Text Domain: actus-deep-schema
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
global $mode;
$mode='prod-mode';


acsc_trc('━━━━ acsc-admin.php');


// ≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣
//add_action( 'current_screen', 'acsc_admin_init' );
add_action( 'admin_init', 'acsc_admin_init' );
add_action( 'wp_enqueue_scripts', 'acsc_depedencies' );
add_action( 'admin_enqueue_scripts', 'acsc_depedencies' );





// ≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣

// ADMIN INIT
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
function acsc_admin_init() {
    global $ACSC;
    
	//$acsc_phpDATA['data']   = $ACSC['data'];
    
} 



// ADMIN DEPEDENCIES
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
function acsc_depedencies() {
    global $ACSC, $acsc_phpDATA, $post, $mode;
	$screen = get_current_screen()->id;

	if ( isset($ACSC['sys']) &&
		 is_array($ACSC['sys']['post_types']) )
		$screens = array_keys( $ACSC['sys']['post_types'] );
	else $screens = array();
	
	// On all ACTUS pages
	// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
	if ( $screen != 'toplevel_page_actus-plugins' &&
	     substr($screen, 0, 11) != 'actus_page_' )
		return;
	

	// AJAX
	// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
	require_once ACSC_DIR . '/includes/acsc-admin-ajax.php';


	wp_enqueue_script("jquery");
	wp_enqueue_media();
	wp_enqueue_script('wp-api');
	//wp_enqueue_script('wp-api-fetch');

	$url = ACSC_URL . 'css/actus-admin.css';
	if ( $mode == 'prod-mode' )
		$url = ACSC_URL . 'css/actus-admin.min.css';

	wp_enqueue_style( 'actus-admin-css', $url,
		false, ACSC_VERSION, 'all' );

	
	
	// On Actus Deep Schema and Actus License pages
	// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
	//if ( $screen != 'actus_page_' . ACSC_NAME &&
	     //$screen != 'actus_page_actus-license' ) return;
	$url = ACSC_URL . 'css/acsc-admin.css';
	if ( $mode == 'prod-mode' )
		$url = ACSC_URL . 'css/acsc-admin.min.css';
	
	wp_enqueue_style( 'acsc-admin-styles', $url,
		false, ACSC_VERSION, 'all' );
	
	if ( $mode != 'prod-mode' ) {
		wp_enqueue_style( 'axf-admin-styles', 
			ACSC_URL . 'css/axf-admin.css',
			false, ACSC_VERSION, 'all' );
	}

	
	// On Actus Deep Schema page
	// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
	if ( $screen != 'actus_page_' . ACSC_NAME ) return;
	
	
	$url = ACSC_URL . 'jsm/index.js';
	if ( $mode == 'prod-mode' )
		$url = ACSC_URL . 'js/index.min.js';
	
	wp_enqueue_script(
		'acsc_admin_script', $url, 
		array('jquery', 'wp-api', 'wp-i18n'), ACSC_VERSION, true);
	
	

	wp_localize_script( 'acsc_admin_script',
					    'acscDATA', $acsc_phpDATA );


}



// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
add_filter('script_loader_tag',
		   'acsc_module_scripts' , 10, 3);
function acsc_module_scripts($tag, $handle, $src) {
    // if not your script, do nothing and return original $tag
    if ( 'acsc_admin_script' !== $handle ) {
        return $tag;
    }
    $tag = '<script type="module" src="' . esc_url( $src ) . '"></script>';
    return $tag;
}







?>
