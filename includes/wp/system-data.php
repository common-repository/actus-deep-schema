<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      

acsc_trc('━━━━ wp/system-data.php');


// Parse System Data
// -------------------------------------
// called from acsc-init-data.php - acsc_DATA()
// modifies $ACSC['sys']
// -------------------------------------
function acsc_parse_system_data(){
    if ( wp_doing_ajax() ) return false;
	acsc_trc('acsc_parse_system_data');

	global $ACSC, $wp_taxonomies;
	
	// create ACSC['sys'] and get sys info
	acsc_get_sys_info();
	
	
	// post types
	acsc_get_post_types();
	
	// post types meta
	//acsc_get_post_types_meta();

	// post types taxonomies
	acsc_get_post_types_tax();
	
	if ( is_admin() )
		$ACSC['sys']['page_templates'] = get_page_templates();

	$ACSC['sys']['taxonomies'] = $wp_taxonomies;
	$ACSC['sys']['page_types'] = acsc_get_page_types();
	//$ACSC['sys']['registered_meta_keys'] = get_registered_meta_keys('post');
	
	
	// users
	//$ACSC['sys']['users'] = acsc_get_all_users();
	$ACSC['sys']['users'] = array();
	$ACSC['sys']['user']  = get_current_user_id();
	
	
	if ( is_admin() ) {
		$ACSC['sys']['roles'] = array();
		$ACSC['sys']['screens'] = array();
		if ( ! wp_doing_ajax() ) {
			$roles = get_editable_roles();
			foreach ( $roles as $name => $role ) {
				$ACSC['sys']['roles'][] = $name;
			}
		
		
		$ACSC['sys']['screen'] = get_current_screen()->id;
		$ACSC['sys']['screens'] =
			array_keys( $ACSC['sys']['post_types'] );
		}
	}
	
	
	return $ACSC['sys'];
}
// -------------------------------------
function acsc_get_sys_info(){
	global $ACSC;
	$active_plugins = array();
acsc_trc('    acsc_get_sys_info');
	
	if( !function_exists('is_plugin_active') ) {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}
	
	if ( is_plugin_active('the-events-calendar/the-events-calendar.php') )
		$active_plugins[] = 'the-events-calendar';
	
	if ( is_plugin_active('woocommerce/woocommerce.php') )
		$active_plugins[] = 'woocommerce';
	
	if ( is_plugin_active('wordpress-seo/wp-seo.php') )
		$active_plugins[] = 'yoast';
	
	
	$ACSC['sys'] = array(
		'site_url'   => get_site_url(),
		'plugin_url' => ACSC_URL,
		'version'    => ACSC_VERSION,
		'plugin_dir' => ACSC_DIR,
		//'image_sizes'=> get_intermediate_image_sizes(),
		'image_sizes'=> 
			array_keys(wp_get_registered_image_subsizes()),
		'active_plugins' => $active_plugins,
	);
	
	
}
function acsc_get_page_types(){
	global $ACSC, $wp_taxonomies;
	acsc_trc('    acsc_get_page_types');
	
	$page_types = array(
		'page'		=> 'Web Page',
		'about'		=> 'About',
		'contact'	=> 'Contact',
		'item'		=> 'Custom Post',
		'archive'	=> 'Archive',
		'search'	=> 'Search Results',
		//'post'		=> 'Post archive',
	);


		
	return $page_types;
}
function acsc_get_post_types(){
	global $ACSC; 
	acsc_trc('    acsc_get_post_types');
	
	// post types
	$args = array(
		'public'   => true,
		'_builtin' => false
	);
	$ptypes = get_post_types($args, 'objects', 'and');

	$post_types = array();
	$post_types['post'] = 'Posts';
	$post_types['page'] = 'Pages';
	foreach($ptypes as $key => $row){
		//if ( is_post_type_viewable($key) )
		if ( $row->publicly_queryable == true )
			$post_types[ $key ] = $row->label;
	}
	
	$ACSC['post_types'] = $post_types;
	$ACSC['sys']['post_types'] = $post_types;
	
	
	return $ACSC['post_types'];
}
function acsc_get_post_types_tax(){
	acsc_trc('    acsc_get_post_types_tax');
	global $ACSC;

	$tax  = array();
	foreach ($ACSC['post_types'] as $key => $val){
		$postType = get_post_type_object( $key );
		if ($postType) {
			$ACSC['post_types'][$key] = esc_html($postType->labels->name);
			$tax[$key] = acsc_wp_posttype_taxonomies( $key );
		}
	}

	$ACSC['tax']  = $tax;
	$ACSC['sys']['tax']  = $tax;

}
// XXXXXXXX
function acsc_get_post_types_meta(){
	acsc_trc('    acsc_get_post_types_meta');
	global $ACSC; 
	
	foreach ($ACSC['post_types'] as $key => $val){
		$postType = get_post_type_object( $key );
		if ($postType) {
			$ACSC['post_types'][$key] =
				esc_html($postType->labels->name);
			
			$ACSC['sys']['meta'][$key] = acsc_wp_posttype_meta( $key );
			
		}
	}
	

	$ACSC['meta'] = $ACSC['sys']['meta'];
}
// XXXXXXX
function acsc_get_all_users(){
	// XXX
acsc_trc('    acsc_get_all_users');
	$users = array(
		'all'			=> array(),
		'authors'		=> array(),
		'editors'		=> array(),
		'subscribers' 	=> array(),
		'administrators'=> array(),
		'contributors'	=> array(),
	);
	$acsc_authors = array();
	$editors = array();
	$subscribers = array();
	
	
	
	$chunk_size = 1000;
	$args = array(
		'number' => $chunk_size,
		'offset' => 0,
	);
	$current = $chunk_size;

	$user_query = new WP_User_Query( $args );
	$load = (array) $user_query->results;
	$total = $user_query->get_total();
	
	while ( $current < $total ) {
		
		$args['offset'] += $chunk_size;
		$current += $chunk_size;
		
		$user_query = new WP_User_Query( $args );
		$new_users = (array) $user_query->results;
		$load = array_merge($load, $new_users);
	}
	
	
	foreach ( $load as $idx => $row ){
		unset($row->data->user_pass);
		unset($row->data->user_activation_key);
		
		 
		//$row->data->meta  = get_user_meta( $row->ID );
		$row->data->description = get_user_meta( $row->ID, 'description', true );
		$row->data->social = get_user_meta( $row->ID, 'autodescription-user-settings', true );
		$row->data->roles = $row->caps;
		$row->data->avatar = get_avatar_url( $row->ID,
								array('default' => '',
									  'force_default' => true) );
		
		
		$users['all'][] = $row->data;
		if ( isset( $row->caps['subscriber'] ) )
			$users['subscribers'][] = $row->data;
		if ( isset( $row->caps['author'] ) )
			$users['authors'][] = $row->data;
		if ( isset( $row->caps['editor'] ) )
			$users['editors'][] = $row->data;
		if ( isset( $row->caps['administrator'] ) )
			$users['administrators'][] = $row->data;
		if ( isset( $row->caps['contributor'] ) )
			$users['contributors'][] = $row->data;
		
	}
	
	
	
	return $users;
	
}
// XXXXXXX





?>