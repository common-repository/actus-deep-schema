<?php
/**
 * 
 * @package     Actus_Deep_Schema
 *
 * Plugin Name: Actus Deep Schema
 * Plugin URI:  https://deepschema.org/
 * Description: Schema Markup Creator for WordPress
 * Version:     1.3.2
 * Author:      ACTUS anima
 * Author URI:  https://actus.works/
 * Text Domain: actus-deep-schema
 * License: 	GPL-3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */
// ≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣
/*
	▄████▄	▄████▄	██████	██	██	▄█████
	██▄▄██	██	 	  ██	██	██	▀█▄
	██▀▀██	██		  ██	██	██	   ▀█▄
	██  ██	▀████▀	  ██	▀████▀	█████▀
*/
// ≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// DEFINE CONSTANTS
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ 
define('ACSC_VERSION', '1.3.2' );
define('ACSC_DOM', 'actus-deep-schema' );
define('ACSC_DIR', plugin_dir_path( __FILE__ ) );
define('ACSC_URL', plugin_dir_url( __FILE__ ) );
define('ACSC_FILE', __FILE__ );
define('ACSC_DB_VERSION', '0.2' );
define('ACSC_NAME',
	   trim( dirname( plugin_basename(__FILE__) ), '/') );

 
function acsc_trc(){}


// Get the list of active plugins
$active_plugins = get_option( 'active_plugins' );



// Disable if the premium version is active
if ( in_array( 'deep-schema-premium/deep-schema-premium.php', $active_plugins ) ) {
    return;
}



// run on plugins_loaded
add_action('plugins_loaded', 'actus_deep_schema', 20 );


// ≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣
function actus_deep_schema() {
// ≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣
	
	$active_plugins = get_option( 'active_plugins' );
	// Disable if the premium version is active
	if ( in_array( 'deep-schema-premium/deep-schema-premium.php', $active_plugins ) ) {
		wp_die();
	}

	


// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
add_action( 'init', 'acsc_INIT' );
add_action( 'init', 'acsc_internationalization' );
add_action( 'admin_head', 'acsc_hide_notices', 1 );


// INTERFACE
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
require_once ACSC_DIR . '/includes/acsc-admin-interface.php';


/*
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
function ACSC_RESET_basic(){
	global $wpdb;
	$options_table = $wpdb->prefix . 'options';
	$options = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT option_name FROM $options_table WHERE option_name LIKE %s OR option_name LIKE %s",
			'ACSC%',
			'acsc\_%'
		)
	);

	foreach ($options as $option) {
		delete_option($option->option_name);
	}
}
if ( isset($_GET['acsc']) && $_GET['acsc'] == 'reset' ) {
	ACSC_RESET_basic();
	die;
}
*/	


// INITIALIZE & set DATA
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
function acsc_INIT() {
	global $ACSC, $post;
	
	require_once ACSC_DIR . 'includes/acsc-options.php';
	include_once ACSC_DIR . 'includes/acsc-init-data.php';
	if ( is_admin() || wp_doing_ajax() || wp_doing_cron() ) {
		include_once ACSC_DIR . '/includes/acsc-admin-ajax.php';
	}
	
	
	// load hooks functions
	include_once ACSC_DIR . 'includes/acsc-hooks.php';
	
	
    if ( is_admin() || wp_doing_cron() ) {
		
	// ADMIN or CRON
	// ━━━━━━━━━━━━━━━━━━━━━━━
		// Check User Role
		if ( acsc_role_check() ) {

			
			// ADMIN (ajax & dependencies)
			// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
			require_once ACSC_DIR . '/includes/acsc-admin.php';
		}

	} else {
	// FRONT END
	// ━━━━━━━━━━━━━━━━━━━━━━━
		include_once ACSC_DIR . '/includes/acsc-FE.php';
	}
	
	
	
}


 




// Load language files
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
function acsc_internationalization() {
	
	load_plugin_textdomain( ACSC_DOM, false,
						    ACSC_NAME . '/languages' );
	
} 




// Hide admin notices
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
function acsc_hide_notices(){
	$screen = get_current_screen()->id;
	if ( $screen != 'actus_page_actus-deep-schema' )
		return;
	
	remove_all_actions( 'admin_notices' );
}







// Add settings link
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
$plugin = plugin_basename(__FILE__);
add_filter( "plugin_action_links_$plugin", 'acsc_settings_link' );
function acsc_settings_link( $links ) { 
  $settings_link = '<a href="admin.php?page=actus-deep-schema">Actus Deep Schema</a>'; 
  array_unshift( $links, $settings_link );
  return $links; 
}






// User Role Check
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
function acsc_role_check(){
	$options = get_option('ACSC-options');
	if ( ! $options ) return false;
	$roles = array('administrator');
	if ( $options['user_roles'] )
		$roles = $options['user_roles'];
	$user  = wp_get_current_user();
	foreach ( $roles as $role ) {
		if ( current_user_can( $role ) ) {
			return true;
		}
	}
	return false;
}

	
	
	
	
	

// ≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣
function acsc_cof( $val, $schema, $echo = false, $console = true ){
	//if ( ! $val ) return; 
	
	$dir = get_stylesheet_directory();


	$backtrace = debug_backtrace();
	$file = '';
	if ( $backtrace[2] ) {
		$tmp  = explode('/', $backtrace[2]['file']);
		if ( $tmp[sizeof($tmp) - 1] !=
				'template-loader.php' &&
			 $tmp[sizeof($tmp) - 1] !=
				'wp-blog-header.php'  ) {
			$file = $file . $tmp[ sizeof($tmp) - 1 ] .
					':'.$backtrace[2]['line'].
					' (' . $backtrace[2]['function'] . ') >>> ';
		}
	}
	if ( $backtrace[1] ) {
		$tmp = explode('/', $backtrace[1]['file']);
		if ( $tmp[sizeof($tmp) - 1] !=
				'template-loader.php' &&
			 $tmp[sizeof($tmp) - 1] !=
				'wp-blog-header.php'  )
			$file = $file . $tmp[ sizeof($tmp) - 1 ] .
					':'.$backtrace[1]['line'].
					' (' . $backtrace[1]['function'] . ') >>> ';
	}
	$tmp = explode('/', $backtrace[0]['file']);
	$file = $file . $tmp[ sizeof($tmp) - 1 ] . 
			':' . $backtrace[0]['line'];


	$file = str_replace('.php', '', $file);

	/*
	if ( $echo !== false ) {
		echo "<br>------------------------------<br>";
		echo "<br>" . $file;
		echo "<br>------------------------------<br>";
		echo "<pre>";
		print_r( $val );
		echo "</pre>";
	}
	*/

	if ( $console !== false ) {
		//if ( ! is_array( $val ) )
			//$val = array( $val );

		?><script>
			if ( typeof dbgval == 'undefined' )
				{ let dbgval = []; }
			if ( typeof schema == 'undefined' )
				{ let schema = {}; }
			dbgval = <?php echo wp_json_encode($val); ?>;
			schema = <?php echo wp_json_encode($schema); ?>;
			//dbgval = JSON.parse( dbgval );
			console.groupCollapsed('%c███████',
								   'color: #DDD;',
								   dbgval);

			
			if ( typeof( dbgval == 'object' ) ){
				//if ( Object.keys(dbgval).length < 14 )
					console.table(schema);
			} 
/*
			for (key in dbgval){
				console.log(`----------  - ${key} >>> `,
							 dbgval[key]);
			}
*/
			console.groupEnd();

		</script><?php
	}

		
		
	
}
	
	
	
	
// ≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣
}
// ≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣




	

// ACTIVATION
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
function acsc_activation() {
	global $ACSC, $acsc_upload_dir;
	
	$options = get_option('ACSC-options');
	if ( isset($options) ){
	if ( isset($options['modules_active']) ){
		$ACSC['options'] = $options;
		$ACSC['options']['modules_active'] =
			array( 'basic' );
		update_option('ACSC-options', $ACSC['options'], false);
	}
	}
	
	acsc_check_for_premium();
		

	// BASIC
	require_once( get_home_path() . 'wp-admin/includes/upgrade.php');
	
	
	// Create upload dir deep-schema
	$upload = wp_upload_dir();
	$acsc_upload_dir = $upload['basedir'] . '/deep-schema/';
	if (! is_dir($acsc_upload_dir)) {
	   mkdir( $acsc_upload_dir, 0700 );
	}
	
	
	
    add_option( 'actus-deep-schema-activated', true );
	
	
	
}
function acsc_redirect_on_activation() {
    if ( get_option( 'actus-deep-schema-activated' ) ) {
        delete_option( 'actus-deep-schema-activated' );
        wp_redirect( admin_url('admin.php?page=actus-deep-schema&acsc=activation' ) );
        exit;
    }
}
register_activation_hook( __FILE__, 'acsc_activation');
add_action('admin_init', 'acsc_redirect_on_activation');





// CHECK FOR PREMIUM VERSION
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
function acsc_check_for_premium() {
	
	
	if ( ! function_exists( 'is_plugin_active' ) ) {
		include_once get_home_path() . 'wp-admin/includes/plugin.php';
	}
	
	if ( ! is_admin() ) { return; }

	
	if ( is_plugin_active('deep-schema-premium/deep-schema-premium.php') ) {
		
		deactivate_plugins('actus-deep-schema/actus-deep-schema.php');
		
		return true;
	} else {
		return false;
	}
	
	
	
}



?>