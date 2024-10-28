<?php
/**
 * The Front End work.
 *
 * @package    Actus_Deep_Schema
 */
// ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓
/*
	▄████▄	▄████▄	██████	██	██	▄█████
	██▄▄██	██	 	  ██	██	██	▀█▄
	██▀▀██	██		  ██	██	██	   ▀█▄
	██  ██	▀████▀	  ██	▀████▀	█████▀
*/
// ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


acsc_trc('━━━━ acsc-FE.php');

// Helper functions
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
include_once "front-end/acsc-FE-helpers.php";


// Remove Other Plugin's Schemas
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
include_once "front-end/acsc-FE-remove-other.php";
add_action('wp_head', 'acsc_remove_other_schemas', 1);





// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ 
add_action('wp_enqueue_scripts', 'acsc_FE_depedencies' );
add_action('wp', 'acsc_FE_init' );
add_action('wp_head', 'acsc_FE_schema_to_page');
//add_action('admin_bar_menu', 'acsc_admin_bar', 100);



// INIT
// ≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣
function acsc_FE_init(){
    global $ACSC, $acsc_phpDATA, $wp, $post;
	
	// check if plugin is enabled
	if ( $ACSC['options'] && ! $ACSC['options']['enable'] ) return;

	include_once ACSC_DIR . '/includes/acsc-data.php';
	include_once ACSC_DIR . '/includes/acsc-plugins.php';
	

	// get schema templates
	$templates = acsc_schema_templates();

	// Initialize $ACSC['schema_fe']
	acsc_init_schemas_FE();
	
	// get website, page and post schema ids
	//$acsc_schema_ids = acsc_parse_schemas();
	
	// get WP meta data
	if ( isset($post) && is_object($post) ) {
		//if ( function_exists('acsc_get_WP_meta') )
			$meta = acsc_get_WP_meta( $post );
		if ( ! $meta || ! is_array($meta) ) $meta = array();
		if ( ! $ACSC['wp'] || ! is_array($ACSC['wp']) ) $ACSC['wp'] = array();
		$ACSC['wp'] = array_merge( $ACSC['wp'], $meta );
	}

	
	
	// get website schema
	if ( is_front_page() ) {
		acsc_load_schemas_FE( array('website' => array('website-1') ) );
	}

	// run once to get schema idx needed in acsc_schema_targets 
	//acsc_page_schemas( $templates );

	

	//acsc_schema_targets( $templates );


	// get post schemas (default & current)
	acsc_post_schemas( $templates );
	
	// get page schemas (default & current)
	acsc_page_schemas( $templates );
	
	

	// get other schemas used in website/page/post schema
	$acsc_schema_ids = acsc_parse_base_schema_ids();
	
	// get items targets
	if ( isset($ACSC['wp']['id']) && isset($ACSC['options']['targets']['items']) ){
		foreach ($ACSC['options']['targets']['items'] as $key => $row){
			if (is_array($row) &&
				in_array($ACSC['wp']['id'], $row) ) {
				$acsc_schema_ids['items'][] = $key;
			}
		}
		}
	


	

	// empty items from $acsc_schema_ids if is archive or search page
	// MOVED below - changed to altering $ACSC['schema_fe']


	
	// load rest of schemas to $ACSC['schema_fe']
	for ($n=1; $n<=5; $n++) {
		$ids = acsc_find_ids_in_schemas( $acsc_schema_ids );
		
		// empty items from $acsc_schema_ids if is archive or search page
		//if ( is_archive() ) $acsc_schema_ids['items'] = array();
		//if ( is_search() )  $acsc_schema_ids['items'] = array();

		if ( json_encode($ids) !=
		     json_encode($acsc_schema_ids) ) {
			acsc_load_schemas_FE( $ids );
		}
		$acsc_schema_ids = array_merge($acsc_schema_ids, $ids);
		
	}


	// empty items from $ACSC['schema_fe'] if is archive or search page
	if ( is_archive() || is_search() || is_author() ) {
		$ACSC['schema_fe']['items'] = array();
		$ACSC['schema_fe']['post'] = array();
	}

	
	// commented on 0.9.1
	// --- wp data parsed on acsc-init-data.php (acsc_DATA())
	//$ACSC['wp'] = acsc_parse_wp_data( $post );
	
	
	// load schemas to $ACSC['schema_fe']
	//acsc_load_schemas_FE( $acsc_schema_ids );
	
	
	
	// ▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒
	$ACSC['schema_fe'] = apply_filters( 'acsc-FE-loaded-schemas', $ACSC['schema_fe'] );
	// ▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒
	

	
	$acsc_phpDATA['schema_fe'] = $ACSC['schema_fe'];
	
}
// ------------------------------------
function acsc_init_schemas_FE(){
    global $ACSC;
	
	// Initialize $ACSC['schema_fe']
	if ( ! isset( $ACSC['schema_fe']) )
		$ACSC['schema_fe'] = array();
	$defaults = array(
		'website'  => array(),
		'audience' => array(),
		'persons'  => array(),
		'business' => array(),
		'items'    => array(),
		'post'     => array(),
		'page'     => array(),
	);

	$ACSC['schema_fe'] =
		array_merge_recursive($defaults, $ACSC['schema_fe']);
}
function acsc_load_schemas_FE( $acsc_schema_ids ){
    global $ACSC, $acsc_phpDATA, $post;
	
	// Load schemas
	foreach( $acsc_schema_ids as $scope_name => $scope ) {
	foreach( $scope as $idx ) {
		if ( ! isset($ACSC['schema_fe'][$scope_name][ $idx ]) ) {
			$read = get_option( "ACSC-$idx" );

			if ( $read ) {
				
				if ( isset( $mode ) && $mode == 'short' ) {
					//if ( $scope_name == 'business' )
						//$read = acsc_short_business( $read );
				}
				
				if ( is_single() ) {
					
				}
				
				// ADD TO SCHEMA
				$ACSC['schema_fe'][$scope_name][ $idx ] = $read;
				
			}
		}
	}
	}
	
//_con( $ACSC['schema_fe'] );
	
}
function acsc_post_schemas( $templates ){
    global $ACSC, $post;
	
	// get post type & scope
	$post_type = "";
	if ( is_object($post) )	$post_type = $post->post_type;
	if ( isset($ACSC['wp']['post_type']) )
		$post_type = $ACSC['wp']['post_type'];
	$post_type = strtolower( $post_type );

	$scope = 'post';
	if ( $post_type != 'post' ) $scope = 'items';
	
	
	// get post schema ids
	$acsc_schema_ids = array(
		'post' => array(),
		'items' => array(),
	);
	if ( isset($post) ) {
	if ( $post_type == 'post' ||
	   sizeof($ACSC['options']['modules_active']) > 1) {
		$acsc_schema_ids[$scope][] = $post_type . '-0';
		$acsc_schema_ids[$scope][] = $post_type . '-' . $post->ID;
	}
	}
	
	
	$items_posts = array_unique( array_merge(
		$acsc_schema_ids['items'],
		$acsc_schema_ids['post']
	));
	
	
	// LOOP through schema Ids
	foreach( $items_posts as $sch ){
		if ( $post_type == 'page' ) continue;
		
		
		// load schema if exists
		$read = get_option( "ACSC-$sch" );
		if ( ! $read ) {
			
			$targetType = 'Article';

			// if there is a target item for the posttype
			if ( isset($post) ) {
			if ( isset($ACSC['options']['targets'][$post->post_type]) ) {
				$targetType = $ACSC['options']['targets'][$post->post_type];
			}
			}

			// load schema defaults if exists
			$read = get_option("ACSC-$post_type-0");
			
			
			// else get schema defaults from templates
			if ( ! $read && isset($post) && isset($templates[ $post->post_type.'-0' ]) )
				$read = $templates[ $post->post_type.'-0' ];
			
					
			// Targeted Defaults
			if ( explode('-', $sch)[1] != 0 ) {
				$targeted_defaults = acsc_schema_targets($sch);
				if ( $targeted_defaults ) $read = $targeted_defaults;
			}

			


			if ( ! is_array( $read ) ) $read = array();



			if ( isset( $templates[ $targetType ] ) &&
				 is_array( $templates[ $targetType ] ) )
				$read = array_merge(
					$templates[ $targetType ], $read );
			
		}

		$ACSC['current_schema_id'] = $sch;

		

		// assign schema 
		$ACSC['schema_fe'][$scope][ $sch ] = $read;


		/*
		// assign schema if not already exists (from targets)
		if ( isset($ACSC['schema_fe'][$scope][ $sch ]) &&
		     ! is_array($ACSC['schema_fe'][$scope][ $sch ]) )
				$ACSC['schema_fe'][$scope][ $sch ] = $read;
		*/
					
				
	}

}
function acsc_page_schemas( $templates ){
    global $ACSC, $post;
	
	// Page Schema Defaults
	if ( isset( $ACSC['schema_fe']['page']["page-0"] ) )
		$read = $ACSC['schema_fe']['page']['page-0'];
	if ( ! isset( $read ) ) $read = $templates['page-0'];
	
	
	$ACSC['schema_fe']['page']["page-0"] = $read;
	
	
	// PAGE TYPE SCHEMA
	acsc_page_type_schema();
	
}
function acsc_page_type_schema(){
	global $ACSC, $schema, $acsc_schema_id, $acsc_parseID;
acsc_trc('     acsc_page_type_schema');
	
	$schema = '';
	$acsc_schema_id = '';


	// -------------------------------------------------
	function simplePage(){
		global $ACSC, $schema, $acsc_schema_id;
		// Simple Page
		$schema = $ACSC['templates']['page-0'];
		if ( isset($ACSC['wp']) && isset($ACSC['wp']['page_id']) )
			$acsc_schema_id = 'page-'. explode('-', $ACSC['wp']['page_id'])[1];
	}
	function specialPages(){
		global $ACSC, $schema, $acsc_schema_id;
		// About / Contact / Search Page

		if ( ! isset( $ACSC['options']['targets']['pages'] ) )  return false;

		$targets = $ACSC['options']['targets']['pages'];
			
		$pid = 0;
		if ( isset($ACSC['wp']) && isset($ACSC['wp']['id']) ) $pid = $ACSC['wp']['id'];
		
		// about
		if ( isset( $targets['about'] ) && in_array($pid, $targets['about']) ){
			if ( ! is_array( $targets['about'] ) ) $targets['about'] = array( $targets['about'] );
			$schema = $ACSC['templates']['page-about'];
			//$schema = get_option("ACSC-page-about");
			$acsc_schema_id = "page-about";
		}
	
		// contact
		if ( isset( $targets['contact'] ) && in_array($pid, $targets['contact']) ) {
			if ( ! is_array( $targets['contact'] ) ) $targets['contact'] = array( $targets['contact'] );
			$schema = $ACSC['templates']['page-contact'];
			//$schema = get_option("ACSC-page-contact");
			$acsc_schema_id = "page-contact";
		}
	
		// search
		if ( isset( $targets['search'] ) && in_array($pid, $targets['search']) ) {
			if ( ! is_array( $targets['search'] ) ) $targets['search'] = array( $targets['search'] );
			$schema = $ACSC['templates']['page-search'];
			//$schema = get_option("ACSC-page-contact");
			$acsc_schema_id = "page-search";
		}
			

	}
	function ItemPage(){
		if ( ! is_single() ) return false;
		global $ACSC, $schema, $acsc_schema_id;
	
		$schema = $ACSC['templates']['page-item'];
		if ( isset($ACSC['wp']) && isset($ACSC['wp']['page_id']) )
		$acsc_schema_id = 'page-item-'. explode('-', $ACSC['wp']['page_id'])[1];
	}
	function TaxonomyPage(){
		if ( ! is_archive() ) return false;
		global $ACSC, $schema, $acsc_schema_id;

		$obj = get_queried_object();

		//$schema = get_option("ACSC-page-archive");
		$schema = $ACSC['templates']['page-archive'];
		$slug = "archive";
		if ( isset($ACSC['wp']) &&
			isset( $ACSC['wp']['taxonomy_slug'] ) )
			$slug = $ACSC['wp']['taxonomy_slug'];
		
		$tid = $acsc_parseID;
		if ( isset($ACSC['wp']['term_id']) )
			$tid = $ACSC['wp']['term_id'];
		
		if ( $obj->label ) 
			$slug = strtolower($obj->label);
		
		$acsc_schema_id = "page-$slug-$tid";

	}
	function AuthorPage(){
		if ( ! is_author() ) return false;
		global $ACSC, $schema, $acsc_schema_id;

		//$schema = get_option("ACSC-page-author");
		$schema = $ACSC['templates']['page-author'];
		$acsc_author = get_queried_object();
		$acsc_author_id = $acsc_author->ID;
		//$schema['@type'] =
			//array('CollectionPage', 'ProfilePage');
		$acsc_schema_id = "page-author-" . $acsc_author_id;
		//$schema['mainEntity'] = $schema['author'];
	}
	function searchPage(){
		if ( ! is_search() ) return false;
		global $ACSC, $schema, $acsc_schema_id;
	
		//$schema = get_option("ACSC-page-search");
		$schema = $ACSC['templates']['page-search'];
		$acsc_schema_id = "page-search";
		//$schema['mainEntity'] = $schema['author'];
		unset($ACSC['schema_fe']['page']['page-search']);
	}
	function archivePage(){
		if ( ! is_day() && ! is_month() && ! is_year() ) return false;
		global $ACSC, $schema, $acsc_schema_id;

		$acsc_schema_id = "page-archive-";
		$day = get_query_var( 'day' );
		$year = get_query_var( 'year' );
		$month = get_query_var( 'monthnum' );
		$name = "The Archive for ";
		$date = "";
		if ( $month ) $date .= "$month/";
		if ( $day )   $date .= "$day/";
		if ( $year )  $date .= $year;
		$schema['name'] = $name . $date;
		$schema['description'] = "Archive Page. Date: $date";
		
		$acsc_schema_id = "page-date-$year";
		if ( $month ) $acsc_schema_id .= "-$month";
		if ( $day )   $acsc_schema_id .= "-$day";
		unset($ACSC['schema_fe']['page']['page-archive']);
		
	}

	// -------------------------------------------------
	simplePage();
	specialPages();
	ItemPage();
	TaxonomyPage();
	AuthorPage();
	searchPage();
	archivePage();



	// if there is already a schema for this id in schema_fe (put from targets)
	//if ( isset( $ACSC['schema_fe']['page'][$acsc_schema_id] ) )
		//$schema = $ACSC['schema_fe']['page'][$acsc_schema_id];

	// Targeted Defaults
	$targeted_defaults = acsc_schema_targets( $acsc_schema_id );
	if ( $targeted_defaults ) $schema = $targeted_defaults;
	

	// set page schema
	if ( $schema ){
		$schema['@id'] = $acsc_schema_id;
		$page0 = $ACSC['schema_fe']['page']['page-0'];


		if ( ! $page0 ) $page0 = $ACSC['schemas']['page-0'];
		if ( ! $page0 ) $page0 = array();
		
		
		// load schema if exists
		$read = get_option( "ACSC-$acsc_schema_id" );
		if ( $read )
			$read = array_merge( $schema, $read );
		else
			$read = $schema;

		$ACSC['current_schema_id'] = $acsc_schema_id;
	
		$ACSC['schema_fe']['page'][$acsc_schema_id] = array_merge( $page0, $read );
	}
	
}
function acsc_schema_targets( $check = '' ){
	global $ACSC, $post;
	$key1 = explode('-', $check)[1];
	if ( is_numeric($key1) ) $key1 = 0;
	$check = explode('-', $check)[0] .'-'. $key1;

	$post_type = "";
	if ( is_object($post) )	$post_type = $post->post_type;
	$post_type = strtolower( $post_type );

	$page_template = '';

	// TARGETED DEFAULTS
	// ----------------------------------
	if ( str_contains($check, 'page-') || str_contains($check, $post_type.'-') ){
		if ( isset($ACSC['options']['targets']) &&
		     isset($ACSC['options']['targets']['defaults']) ){
			foreach ($ACSC['options']['targets']['defaults'] as $key => $row){
				$key1 = explode('-', $key)[1];
				if ( is_numeric($key1) ) $key1 = 0;
				$type = explode('-', $key)[0] .'-'. $key1;
				if ( $check && $type != $check ) continue;
//_cos($check .' --- '. $type .' --- CHECK');

				$page_template = acsc_check_targets( $key, $row );
			
				/*
				if ( ($post_type == 'post' || $post_type == 'page') &&
						$post_type != $type &&
						! ( is_archive() && $type=='archive') ) {
						//$page_template = '';
				}
				*/

				if ( $page_template ) break;
			}
		}
	}






	$result = false;
	if ( $page_template ){
		$read = get_option( "ACSC-$page_template" );

		$scope = 'items';
		if ($type == 'page') $scope = 'page';
		if ($post_type == 'post') $scope = 'post';


		if ( $read ) $result = $read;
			//$ACSC['schema_fe'][$scope][$type.'-'.$ACSC['wp']['id']] = $read;

	}



	
	// TARGETED ITEMS
	// ----------------------------------
	if ( str_contains($check, 'page-') ) {
		if ( isset($ACSC['options']['targets']) &&
		     isset($ACSC['options']['targets']['_items']) ){
			foreach ($ACSC['options']['targets']['_items'] as $key => $row){

				$item = acsc_check_targets( $key, $row );
				if ( $item ) {
					$item = get_option( "ACSC-$item" );
					$ACSC['schema_fe']['items'][strtolower($key)] = $item;
				}

			}
		}


	}





	return $result;





}
function acsc_check_targets( $key, $item_targets ){
	global $ACSC;


	$page_template = "";
	foreach($item_targets as $i => $trg){
		//$ACSC['schema_fe']['page'][$val] = $ACSC['templates'][$val];
		//if ( $type == 'post' )
		// IDS / TITLES

		// ────────────────────────────────────────────
		if ( ($trg['type'] == 'ids' || $trg['type'] == 'titles') &&
			 isset($ACSC['wp']['id']) ) {
				
			$ids = str_replace(" ", "", $trg['value']);
			$ids = explode(',', $ids);
			if ( in_array($ACSC['wp']['id'], $ids) ) {
				$page_template = $key;
			}
		}

		// PARENT
		// ────────────────────────────────────────────
		if ( $trg['type'] == 'parent' && isset($ACSC['wp']['id']) ) {

			if ( in_array($ACSC['wp']['parent'], $trg['value']) ) {
				$page_template = $key;
			}
		}

		// AUTHOR
		// ────────────────────────────────────────────
		if ( $trg['type'] == 'author' && isset($ACSC['wp']['id']) ) {
			if ( isset($ACSC['wp']['author']['_wpUser']) &&
				 $ACSC['wp']['author']['_wpUser'] == $trg['value'] ) {
				$page_template = $key;
			}
		}

		// TEMPLATE
		// ────────────────────────────────────────────
		if ( $trg['type'] == 'template' && isset($ACSC['wp']['id']) ) {
			if ( ! $ACSC['wp']['template'] )
				$ACSC['wp']['template'] = 'page.php';
			if ( $ACSC['wp']['template'] == $trg['value'] ) {
				$page_template = $key;
			}
		}
		// TAXONOMY
		// ────────────────────────────────────────────
		if ( $trg['type'] == 'tax' && isset($ACSC['wp']['id']) ) {

			if ( isset($ACSC['wp'][ $trg['tax'] ]) )
				$tax = $ACSC['wp'][ $trg['tax'] ];
			
			if ( isset($ACSC['wp'][ $trg['tax'].'_array' ]) )
				$tax = $ACSC['wp'][ $trg['tax'].'_array' ];
			
			if ( isset($ACSC['wp'][ $trg['tax'].'_ids' ]) )
				$tax = $ACSC['wp'][ $trg['tax'].'_ids' ];


			if ( isset( $tax ) ){
				if ( is_array( $tax ) ){
					if ( in_array($trg['title'], $tax) ||
						 in_array($trg['value'], $tax) ){
						$page_template = $key;
					}
				} else if ( $tax == $trg['title'] ||
							$tax == $trg['value'] ||
						 str_contains($tax, $trg['title']) ){
						$page_template = $key;
				}
			}


		}
		
		// ARCHIVE
		// ────────────────────────────────────────────
		if ( $trg['type'] == 'archive' ) {
			$obj = get_queried_object();
			if ( $trg['tax'] == $obj->taxonomy &&
				 intval($trg['value']) == $obj->term_id ) {
					$page_template = $key;
			}
		}

		if ( $page_template ) break;

	}

	return $page_template;

}
function acsc_parse_base_schema_ids(){
    global $ACSC, $acsc_phpDATA, $post;
	
	// initialize $acsc_schema_ids array
	$acsc_schema_ids = array(
		'website'  => array(),
		'audience' => array(),
		'persons'  => array(),
		'business' => array(),
		'items'    => array(),
		'page'     => array(),
		'post'	   => array(),
	);


	// get page schema ids (page-0, page-416)
	$pid = 0;
	if ( $post ) $pid = $post->ID;
	$acsc_schema_ids['page'][] = 'page-0';
	$acsc_schema_ids['page'][] = 'page-' . $pid;

	
	// Front Page
	if ( is_front_page() ) {
		// get website schema id
		$acsc_schema_ids['website'][] = 'website-1';
	
	// Rest Pages
	} else {
		// get website schema id
		//$acsc_schema_ids['website'][] = 'website-1';
		
		
		// page targets  ???
		if ( isset($ACSC['options']['targets']['pages']) ){
		foreach( $ACSC['options']['targets']['pages'] as $type => $id ){
			if ( $id == $pid ) $pid = $type;
		}
		}
		
		// get post schema ids (post-0, post-416)
		// (for post and custom post types)
		if ( is_single() && $post ) {
			$ptype = $post->post_type;
			
			if ( $ptype == 'post' ||
			   sizeof($ACSC['options']['modules_active']) > 1) {
				$scope =  'post';
				if ( $ptype != 'post' ) $scope = 'items';
				
				$acsc_schema_ids[ $scope ][] = $ptype . '-0';
				$acsc_schema_ids[ $scope ][] = $ptype . '-' . $post->ID;
			}
		}
	}
	
	
	return $acsc_schema_ids;
}
function acsc_find_ids_in_schemas( $ids ){
	global $ACSC, $acsc_phpDATA, $post;
	
	$post_id = 0;
	$post_type = "";
	if ( is_object($post) ) {
		$post_id = $post->ID;
		$post_type = $post->post_type;
	}
	
	foreach( $ACSC['schema_fe'] as $scope_key => $scope ){
	foreach( $scope as $schema_key => $schema ){
		if ( is_array($schema) ) {
			$tmp = acsc_recursiveFind_FE($schema, '@id');
			$types = acsc_recursiveFind_FE($schema, '@type');
			
			foreach($tmp as $i => $val){
			if ( is_string($val) ) {
				if ( substr($val, 0, 8) == '#website' )
					$scope = 'website';
				else if ( substr($val, 0, 5) == '#audi' ) $scope = 'audience';
				else if ( substr($val, 0, 5) == '#busi' ) $scope = 'business';
				else if ( substr($val, 0, 5) == '#pers' ) $scope = 'persons';
				else if ( substr($val, 0, 5) == '#page' ) $scope = 'page';
				else if ( substr($val, 0, 4) == 'page' ) $scope = 'page';
				else if ( substr($val, 0, 5) == '#post' ) $scope = 'post';
				else if ( explode("-",$val)[0] == '#'.$post_type ) $scope = 'post';
				else $scope = 'items';
				
				
				if ( ! in_array( substr($val, 1), $ids[$scope] ) && $scope != 'website' ) {
					if ( substr($val, 0, 1) == '{') {
						$iid = $post_type.'-'.$post_id;
//_con([ $scope, $iid, $val, $types[0] ]);
						if ( !in_array($iid, $ids[$scope]) )
							$ids[$scope][] = $iid;
						//$ids[$scope][] = $val;
					} else {
						if ( substr($val, 0, 1) == '#' )
							if ( !in_array(substr($val, 1),
										   $ids[$scope]) )
								$ids[$scope][] = substr($val, 1);
						else
							if ( !in_array($val, $ids[$scope]) )
								$ids[$scope][] = $val;
					}
				}
			}
			}
				

		} else {
			unset($ACSC['schema_fe'][$scope_key][$schema_key]);
		}
	}
	}
	
	// clean up item schema ids
	$item_ids = array();
	foreach ($ids['items'] as $row){
		if ( substr($row,0,5)!='page-' &&
		     substr($row,0,5)!='post-' &&
		     ! in_array($row, $item_ids) )
			$item_ids[] = $row;
	}
	$ids['items'] = $item_ids;

	foreach ($ids['post'] as $i => $row){
		if ( substr($row,0,5)!='post-' ) {
			if ( ! in_array($row, $ids['items']) )
				$ids['items'][] = $row;
			unset( $ids['post'][$i] );
		}
	}
	
	
	return $ids;
}
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅





// ADD SCHEMA TO PAGE
// ≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣
function acsc_FE_schema_to_page(){
	global $ACSC, $post;

	
	// check if plugin is enabled
	if ( ! isset($ACSC['options']) ) return;
	else if ( ! $ACSC['options']['enable'] ) return;
	
	
	// Run on Schema Validation
	// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
	if ( isset($post) &&
		 $post->post_name == 'ads-view-schema' ) {
		acsc_schema_validation();
		return;
	}
	

	// filter allowed schemas and fields    
	// on $ACSC['schema_fe']
	acsc_allow_props();
	
	// Proccess & Filter schemas and set dynamic values
	// Result goes to $schemas array
	// ≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣
	$schemas = acsc_FE_schemas_array();


	// ▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒
	$schemas = apply_filters( 'acsc-FE-proccessed-schemas',
							  $schemas );
	// ▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒
	
//_con( $ACSC['schema_fe'] );
//_con( $schemas );
	
	// COMPOSE FINAL SCHEMA with @graph
	// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
	$final = array(
		"@context" => "https://schema.org",
		"@context" => array(
			"@base"  => get_site_url() . '/',
			"@vocab" => "http://schema.org/",
		),
		"@graph" => $schemas,
	);

	
	
	// console display
	// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
	foreach ($schemas as $i => $row) {
		$iid = '#' . explode('/#', $row['@id'])[1];
		if ( isset($row['@type']) ) $iid = $row['@type'];
		if ( is_array($iid) ) $iid = $iid[0];
		if ( isset($row['name']) && $row['name']) {
			$iid = str_pad($iid,  25, ".");
			$iid .= $row['name'];
		} else if ( isset($row['headline']) && $row['headline']) {
			$iid = str_pad($iid,  25, ".");
			$iid .= $row['headline'];
		}
		
		// console display
		if ( $ACSC['options']['console_all'] || 
		    ($ACSC['options']['console_admin'] &&
			 current_user_can('manage_options')) ){
			
			
			acsc_cof( $iid, $row );
			
		}
		
		
	}



	// ▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒
	$final = apply_filters( 'acsc-FE-final-schemas',
						    $final );
	// ▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒


	
	// Export Schema
	// ≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣
	echo '<script type="application/ld+json" class="deep-schema">';
	echo wp_json_encode( $final, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
	echo '</script>';
	
	
	$acsc_phpDATA['url'] = ACSC_URL;
	$acsc_phpDATA['options'] = $ACSC['options'];
	$acsc_phpDATA['schemas'] = $schemas;
	wp_localize_script( 'acsc_FE_script', 'acscDATA', $acsc_phpDATA );
	
	
}
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
function acsc_FE_schemas_array(){
	global $ACSC, $post;
	
	$schemas = array();
	$schemas_used = array();
	
	
	// >>> LOOP through loaded schemas
	// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
	foreach($ACSC['schema_fe'] as $sc => $scopes){
		foreach( $scopes as $id => $item){
			
			$id = strtolower($id);
			$id1 = explode('-', $id)[0];
			$id2 = 0;
			if ( isset(explode('-', $id)[1]) )
				$id2 = explode('-', $id)[1];
			
			if ( $sc == 'page' &&
				 $id == 'page-0' &&
				 isset( $scopes[ $id ] ) ) continue;

			
			// skip if it's a defaults schema (page-0)
			if ( $id2 == '0' ) continue;
			
			
			// skip if already exists in array
			if ( ! isset( $item['@id'] ) )
				$item['@id'] = "";
			if ( $item['@id'] == '{post_id}' ||
			     $item['@id'] == '{page_id}' )
				$item['@id'] = $id;
			$item['@id'] = strtolower( $item['@id'] );
			if ( substr($item['@id'],0,1) != '#' )
				$item['@id'] = '#'.$item['@id'];
			if ( in_array($item['@id'], $schemas_used) )
				continue;
			$schemas_used[] = $item['@id'];
			
			
			// set page type template if not numeric
			if ( $id1 == 'page' && ! is_numeric($id2) ){
				$item['@id'] = "#$id";
			}
			
			// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
			if ( isset($item['_dynamic']) )
			$item['_dynamic'] = apply_filters( 'acsc-FE-item-dynamic', $item['_dynamic'] );
			// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■

			// Dynamic Values
			// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
			include_once "front-end/acsc-FE-dynamic.php";
			$item = acsc_dynamic_values( $item );

			
			// Proccess & Filter Values
			// Link Properties
			// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
			include_once "front-end/acsc-FE-proccess.php";
			$item = acsc_proccess_values( $item );
			$item = acsc_property_linking( $item );
			$item = acsc_filter_values( $item );


			// set schema IDS
			// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
			$item = acsc_set_schema_ids( $item, $sc );
			
			
			
			// APPEND TO $schemas ARRAY
			if ( $sc == 'website' ||
				 $sc == 'post'   ||
				 $sc == 'page' ) {
				array_unshift($schemas, $item);
			} else
				$schemas[] = $item;
			
		}
	}
	
	
	
	
	// move breadcrumb to top level
	// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
	$webpage = array_search("WebPage",
					array_column($schemas, '@type'));
	foreach( $schemas as $idx => $schema ){
		if ( strpos($schema['@id'], '/#page-') !== false ) {
			$webpage = $idx;
			break;
		}
	}
	if ( is_front_page() ) {
		unset( $schemas[$webpage]['breadcrumb'] );
	}
	if ( isset( $schemas[$webpage]['breadcrumb'] ) ){
		
		// generate new @id
		$iid = str_replace('page', 'breadcrumb', 
						   $schemas[$webpage]['@id']);
		$schemas[$webpage]['breadcrumb']['@id'] = $iid;
		
		// remove -item- from last element
		$list = $schemas[$webpage]['breadcrumb']['itemListElement'];
		unset( $list[sizeof($list)-1]['item'] );
		$schemas[$webpage]['breadcrumb']['itemListElement'] = $list;
		
		
		if (sizeof($ACSC['options']['modules_active']) > 1){
			// add to top level
			$schemas[] = $schemas[$webpage]['breadcrumb'];

			// assign only @id to WebPage level
			$schemas[$webpage]['breadcrumb'] = array(
				'@id' => $iid
			);
		} else {
			// remove in free version
			unset( $schemas[$webpage]['breadcrumb'] );

		}
		
	}


	
	// set faq pages
	// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
	if ( isset($ACSC['options']['targets']['items']) ){
	foreach( $ACSC['options']['targets']['items'] as $key => $row ){
		if ( explode('-', $key)[0] == 'FAQ' &&
		     substr($key, -2) != '_t' ) {
			
			if ( in_array(strval($post->ID), $row) ) {
				// get faq
				
				$faqschema =
					array_search(get_site_url() . '/#' . strtolower($key),
								array_column($schemas, '@id'));
				$faq = $schemas[ $faqschema ]['faq'];
				// set page @type
				if ( ! in_array('FAQPage',
								$schemas[$webpage]['@type']) )
					$schemas[$webpage]['@type'][] = 'FAQPage';
				
				// set page mainEntity
				if ( ! isset($schemas[$webpage]['mainEntity']) ||
					 ! is_array($schemas[$webpage]['mainEntity']) )
					$schemas[$webpage]['mainEntity'] = array();
				$schemas[$webpage]['mainEntity'][] = $faq;
				
				array_splice($schemas, $faqschema, 1);
			}
		}
	}
	}


	
	
	return $schemas;
	
}
function acsc_set_schema_ids( $item, $sc ){
	global $post;
	
	// SET IDS
	if ( $item )
	foreach($item as $key => $row){
		$url = get_site_url();


		if ( is_array($row) ) {
			foreach ( $row as $key2 => $row2 ) {
				if ( is_string($key2) &&
					 $key2 == '@id' ) {
					$row[ $key2 ] = "$url/$row2";
				}

				if ( is_array($row2) ) {
					foreach ( $row2 as $key3 => $row3 ) {
						if ( is_string($key3) &&
							 $key3 == '@id' ) {
							$row2[ $key3 ] = "$url/$row3";
						}
						

					if ( is_array($row3) ) {
						foreach ( $row3 as $key4 => $row4 ) {

							if ( is_string($key4) &&
								 $key4 == '@id' ) {
								if (substr($row4,0,4) != 'http')
								$row3[ $key4 ] = "$url/$row4";
								else
								$row3[ $key4 ] = $row4;
							}
						}
						$row2[ $key3 ] = $row3;
					}


					}
					$row[ $key2 ] = $row2;
				}
			}
			$item[ $key ] = $row;
		} else {

			if ( $key == '@id' ) {
				//if ( $sc == 'post' )
					//$url = get_permalink($post->ID);
				$item[ $key ] = "$url/$row";
			}

		}

	}
	
	return $item;
	
}
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅





// Load acsc_FE_script
// ≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣
function acsc_FE_depedencies() {
	global $ACSC;
	
	// check if plugin is enabled
	if ( $ACSC['options'] && ! $ACSC['options']['enable'] ) return;
	
	if ( ! is_admin() ) {

		if ( ! isset( $acsc_phpDATA ) ) $acsc_phpDATA = array();
		$acsc_phpDATA['options'] = $ACSC['options'];
		$acsc_phpDATA['url'] = ACSC_URL;
		
		wp_enqueue_script(
			'acsc_FE_script',
			ACSC_URL . 'js/acsc-FE.min.js',
			array('jquery'), $ACSC['options']['version'] . '7', true);

		wp_localize_script( 'acsc_FE_script', 'acscDATA', $acsc_phpDATA );


	}
}





// VALIDATION
// ≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣
function acsc_schema_validation(){
	global $ACSC;
	$ACSC['options'] = get_option( 'ACSC-options' );
	
	$json_url  = site_url() . '/schema_debug.json';
	$json_data = file_get_contents( $json_url );
	$json_data = stripslashes( $json_data );

	$SCHEMA = json_decode($json_data, true);
	if ( ! $SCHEMA ) return;
	
	$post = null;
	$post_id = explode('-', $SCHEMA['@id'])[1];
	
	foreach( $ACSC['options']['targets'] as $ptype => $label) {
		if ( explode('-', $SCHEMA['@id'])[0] == "#$ptype" ) {
			$post = get_post( $post_id );
		}
	}
	if ( substr( $SCHEMA['@id'], 0, 5 ) == '#page' ) {
		$post_id = substr( $SCHEMA['@id'], 6 );
		$post = get_post( $post_id );
	}
	
	
	// get WP meta data
	if ( isset($post) && is_object($post) ) {
		$meta = acsc_get_WP_meta( $post );
		if ( ! $meta || ! is_array($meta) ) $meta = array();
		if ( ! $ACSC['wp'] || ! is_array($ACSC['wp']) ) $ACSC['wp'] = array();
		$ACSC['wp'] = array_merge( $ACSC['wp'], $meta );
	}

	
	// filter schema and set dynamic values
	$schemas = array();

	
	//$SCHEMA['id'] = $post_id;
	// ▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒
	$SCHEMA['_dynamic'] =
		apply_filters( 'acsc-FE-item-dynamic',
					   $SCHEMA['_dynamic'] );
	// ▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒

	
	
	// Dynamic Values
	// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
	include_once "front-end/acsc-FE-dynamic.php";
	$SCHEMA = acsc_dynamic_values( $SCHEMA );


	// Proccess & Filter Values
	// Link Properties
	// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
	include_once "front-end/acsc-FE-proccess.php";
	$SCHEMA = acsc_proccess_values( $SCHEMA );
	$SCHEMA = acsc_property_linking( $SCHEMA );
	$SCHEMA = acsc_filter_values( $SCHEMA );


	$schemas[] = $SCHEMA;
		
	
	// ▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒
	$schemas = apply_filters( 'acsc-FE-proccessed-schemas', $schemas );
	// ▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒
	
	$SCHEMA = $schemas[0];
	
		
	
	// COMPOSE FINAL SCHEMA
	$final = array(
		"@context" => array(
			"@base"  => get_site_url() . '/',
			"@vocab" => "http://schema.org/",
		),
		"@graph" => $schemas,
	);
	
		

	// ▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒
	$final = apply_filters( 'acsc-FE-final-schemas', $final );
	// ▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒
	
		

	// Export Schema
	// ≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣
	echo '<script type="application/ld+json" class="acsc-validation">';
	echo wp_json_encode( $final, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
	echo '</script>';
	
		
	
	
	//$schemas = json_decode( $schemas );
	echo "<h1 style='margin: 48px 48px; border-bottom:16px solid #DDD;'>Actus Deep Schema preview</h1>";
	echo "<pre style='margin-left: 48px; white-space: pre-wrap;'>";
	
	/*
	echo str_replace(array('&lt;?php&nbsp;','?&gt;'), '', highlight_string( '<?php ' . var_export($filtered, true) . ' ?>', true ) );
	*/
	echo wp_json_encode($SCHEMA, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
	//print_r( $filtered );
	
	echo "<hr style='margin: 48px 0; border: 0; border-top:16px solid #DDD;'/>";
	echo "<pre style='margin-left: 48px; font-size:10px; white-space: pre-wrap;'>";
	print_r( $SCHEMA );
	echo "</pre>";
	echo "<hr style='margin: 48px 0; border: 0; border-top:16px solid #DDD;'/>";
	
}






// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
// ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓
// ▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒
// ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
?>
