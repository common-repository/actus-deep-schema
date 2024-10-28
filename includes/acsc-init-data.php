<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/*
Uses:
acsc_parse_system_data	---	wp/system-data.php
acsc_get_WP_meta		---	wp/post-data.php
acsc_wp_logo			---	wp/helpers.php
acsc_schema_templates	---	acsc-data.php
acsc_DATA_init			---	acsc-data.php
*/

acsc_trc('━━━━ acsc-init-data.php');

// set DATA & OPTIONS
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
add_action( 'current_screen', 'acsc_DATA' );
if ( ! is_admin() ) add_action( 'wp', 'acsc_DATA' );


// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
function acsc_DATA() {
	global $ACSC, $acsc_phpDATA, $post;
	$name = basename(plugin_dir_path(dirname( __FILE__ , 1 )));
	
	
	if ( ! wp_doing_ajax() && ! wp_doing_cron() ) {

		if ( isset($ACSC['sys']) &&
			 is_array($ACSC['sys']['post_types']) )
			$screens = array_keys( $ACSC['sys']['post_types'] );
		else $screens = array();


		// Page check
		// run only on plugin admin page or front end
		$screen = 'front-end';
		if ( is_admin() ) {
			$screen = get_current_screen()->id;
			$base = get_current_screen()->base;
		}
		if ( $screen != "actus_page_$name" &&
			 $screen != 'front-end' &&
			 ! in_array($screen, $screens) &&
			 $base != 'post' ) return;

	}
	
	
	
	// ≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣
	// ≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣ RETURN IF CRON OR AJAX
	$action = '';
	if ( wp_doing_cron() ) return false;
	if ( wp_doing_ajax() ) {
		// Verify nonce
		$nonce = sanitize_key($_POST['nonce']);
		if ( ! wp_verify_nonce( $nonce, 'acsc_nonce' ) ) {
			//wp_send_json_error( 'Invalid nonce.' );
			return false;
		}
		if ( ! isset( $_POST['action'] ) ) return false;


		$action = sanitize_text_field( wp_unslash($_POST['action']) );

		if ( $action != 'acsc_get_youtube_chapters' && 
			 $action != 'acsc_get_WP_meta'  )  return false;
	
	}
	
acsc_trc('acsc_DATA');
	
	
	// On Deep Schema page & post edit for metabox
	// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
	include_once ACSC_DIR . 'includes/acsc-wp.php';
	include_once ACSC_DIR . 'includes/acsc-hooks.php';
	
	
	if ( $action == 'acsc_get_youtube_chapters' ) return false;



	// get_option 'ACSC_data' (schema ids tree)
	$ACSC['data'] = get_option( 'ACSC_data' );
	if ( $ACSC['data'] ) {
		$ACSC['data'] = stripslashes_deep( $ACSC['data'] );
		// refactoring on 1.1.2 - remove remains
		unset( $ACSC['data']['modules'] );
		unset( $ACSC['data']['forms'] );
		unset( $ACSC['data']['items'] );
		unset( $ACSC['data']['itemsTitles'] );
	}
	
	
	
	// Parse System Data	- $ACSC['sys']
	acsc_parse_system_data( $post );
	// --- wp/system-data.php
	
	// get acsc url parameter
	if ( isset($_GET['acsc']) )
		$ACSC['sys']['urlParam'] = sanitize_text_field( wp_unslash( $_GET['acsc'] ) );
	
	
	
	
	// get schema templates
	include_once ACSC_DIR . '/includes/acsc-data.php';
	$ACSC['templates'] = acsc_schema_templates();
	

	
	
	// Parse WP Global Data
	$ACSC['wp'] = acsc_get_WP_global( $post );
	// --- wp/post-data.php
	
	//if ( $_POST['action'] != 'acsc_get_WP_meta' )
	//$ACSC['wp'] = acsc_get_WP_meta( $post );
	


	
	if ( ! current_user_can( 'manage_options' ) ) return;
	// ≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣
	// ≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣ ADMIN ONLY

	
	// get WP Global dynamic labels
	$ACSC['sys']['dynamic_labels'] = acsc_get_WP_global_labels();

	
	
	// set first time ACSC.data (define initial schemas)
	acsc_DATA_init();
	
	// external plugins
	include_once ACSC_DIR . '/includes/acsc-plugins.php';
	
	// get templates again to run plugin hooks
	$ACSC['templates'] = acsc_schema_templates();
	
	
	// load all schema from options	- $ACSC['schema/_fe']
	acsc_load_schema();
	
	
	// populate $acsc_phpDATA
	acsc_PHPDATA();
	
	
}
function acsc_PHPDATA(){
    if ( wp_doing_ajax() ) return false;
	acsc_trc('    acsc_PHPDATA');
	global $ACSC, $acsc_phpDATA, $post, $wp_taxonomies; 
	
	include_once ACSC_DIR . '/languages/acsc-js-i8n.php';
	
	// logo
	$custom_logo = acsc_wp_logo();

	if ( ! isset( $ACSC['hooks'] ) )
		$ACSC['hooks'] = array();
	
	$options = acsc_opt();
	
	$name = basename(plugin_dir_path(dirname( __FILE__ , 1 )));
	// PHP DATA
	$acsc_phpDATA = array(
		'ajax_url'   => admin_url( 'admin-ajax.php' ),
		'nonce'      => wp_create_nonce( 'acsc_nonce' ),
		'rest_nonce' => wp_create_nonce( 'wp_rest' ),
		'data'    	 => $ACSC['data'],
		'options'  	 => $options,
		'schema'  	 => $ACSC['schema'],
		'schemas'  	 => $ACSC['schemas'],
		'hooks' 	 => $ACSC['hooks'],
		'sys' 	 	 => $ACSC['sys'],
		'wp' 	 	 => $ACSC['wp'],
		'templates'	 => $ACSC['templates'],
		'modules'	 => $ACSC['modules'],
		'forms'	 	 => $ACSC['forms'],
		'items'	 	 => $ACSC['items'],
		'itemsTitles'=> $ACSC['itemsTitles'],
		'i8n' 	 	 => $ACSC['i8n'],
		'plugin_name'=> $name,
		'plugin' 	 => get_plugin_data( WP_PLUGIN_DIR . "/$name/$name.php" ),
	);
	
	
	
	// ******************************* ADMIN DATA
    if ( is_admin() && ! wp_doing_ajax()  ) {
		
		$acsc_phpDATA['current_screen']  = get_current_screen();
		if ( $acsc_phpDATA['current_screen'] )
		$acsc_phpDATA['screen']  		= get_current_screen()->id;
		//$acsc_phpDATA['templates'] 		= get_page_templates();
		//$acsc_phpDATA['allcategories'] 	= get_categories();
		
	}
	
	
}


if ( wp_doing_ajax() ) {
	
	// Verify nonce
	if ( isset($_POST['nonce']) )
		$nonce = sanitize_key($_POST['nonce']);
	if ( isset($_POST['_nonce']) )
		$nonce = sanitize_key($_POST['_nonce']);
	if ( wp_verify_nonce( $nonce, 'acsc_nonce' ) ) {
		if ( isset( $_POST['action'] ) ) {
			$action = sanitize_text_field( wp_unslash( $_POST['action'] ) );
			if ( substr($action,0,5) == 'acsc_' ) acsc_DATA();
		}
	}

}



// load all schema options - $ACSC['schema']
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
function acsc_load_schema(){
    if ( wp_doing_ajax() ) return false;
	acsc_trc('    acsc_load_schema');

	global $ACSC, $acsc_phpDATA;
	
	// LOAD SAVED SCHEMA
	$ACSC['schemas'] = array();
	$ACSC['schema'] = array(
		'website'  => array(),
		'audience' => array(),
		'persons'  => array(),
		'business' => array(),
		'items'    => array(),
		'page'     => array(),
		'post'	   => array(),
	);
	
	foreach( $ACSC['data']['schema'] as $scope => $ids ) {
		foreach( $ids as $key => $id ) {
			//if ( is_array($id) ) $id = $key;
			
			$val = get_option( "ACSC-$id" );
			if ( ! $val  && isset($ACSC['templates'][$id]) ) {
				$val = $ACSC['templates'][ $id ];
			}
			
			if ( $val && isset( $ACSC['schema'][$scope] ) )
				$ACSC['schema'][$scope][ $id ] = $val;
			
			if ( ! $val ) $val = array();
			if ( ! isset( $val['_dynamic'] ) )
				$val['_dynamic'] = array();
			
			// ▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒
			$val['_dynamic'] = apply_filters( 'acsc-FE-item-dynamic', $val['_dynamic'] );
			// ▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒
			
			
			
			if ( $val ) $ACSC['schemas'][$id] = $val;
			
		}
	}
	
	if ( ! isset( $ACSC['schema_fe'] ) )
		$ACSC['schema_fe'] = array();
	$acsc_phpDATA['schema']    = $ACSC['schema'];
	$acsc_phpDATA['schemas']   = $ACSC['schemas'];
	$acsc_phpDATA['schema_fe'] = $ACSC['schema_fe'];

	
}



?>