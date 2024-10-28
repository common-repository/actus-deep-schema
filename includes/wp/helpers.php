<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      

acsc_trc('━━━━ wp/helpers.php');


// Get post type taxonomies & meta
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
function acsc_wp_posttype_taxonomies( $post_type ){
	$tmp = get_object_taxonomies( $post_type, 'objects' );
	
	$args = array(
		'public'   => true,
		//'_builtin' => false
	);
	$ptypes = get_post_types($args, 'objects', 'and');
;
	if ( ! isset( $ptypes[$post_type] ) ||
	     ! is_object($ptypes[$post_type]) ) return array();
	
	$ptax = $ptypes[$post_type]->taxonomies;
	foreach($ptax as $tx){
		$tmp[$tx] = get_taxonomy( $tx );
	}
	
	$taxonomies = array();
	foreach ($tmp as $key => $taxonomy){
		$taxonomy = (array) $taxonomy;
		if ( !isset($taxonomy['label']) ) $taxonomy['label'] = '';
		$taxonomies[$key] = array();
		$taxonomies[$key]['label'] = $taxonomy['label'];
		$taxonomies[$key]['terms'] = array();
		$taxonomies[$key]['name']  = $key;
		
		// retrieve all available terms
		$terms = get_terms([
			'taxonomy' => $key,
			'hide_empty' => false
		]);
		$hasTerms = is_array($terms) && $terms;
		if($hasTerms)
			$taxonomies[$key]['terms'] = $terms;        
	}
	
	
	return $taxonomies;
}
function acsc_wp_posttype_meta( $post_type, $sample_size=20 ){
	
	//$meta = get_registered_meta_keys( 'post', $post_type );
	
	$exclude = array(
		"_edit_lock",
		"_edit_last",
		//"_thumbnail_id",
		"_wp_old_slug",
		"_wp_page_template",
		"_pingme",
		"_encloseme",
		"_thumbnail_id",
		"ACSC-media",
	);

	$meta_keys = array();
	$posts     = get_posts( array(
		'post_type' => $post_type,
		'limit' 	=> $sample_size,
		'post_status' => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash')    
	));
	
	
	foreach ( $posts as $post ) {
		$post_meta_keys = get_post_custom_keys( $post->ID );
		if ( is_array( $post_meta_keys ) )
			$meta_keys = array_merge_recursive(
				$meta_keys, $post_meta_keys );
	}
	
	$result = array();
	foreach ($meta_keys as $meta){
		if (substr($meta, 0, 8) != '_oembed_')
			$result[] = $meta;
	}

	// Use array_unique to remove duplicate meta_keys that we received from all posts
	// Use array_values to reset the index of the array
	$result = array_values( array_unique( $result ) );
	$result = array_values( array_diff( $result, $exclude ) );
	
	return $result;

}
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
// used in  actus-wp.php
//			acsc-plugins.php






// Get WP Logo
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
function acsc_wp_logo(){
	
	$custom_logo_id = get_theme_mod( 'custom_logo' );
	$custom_logo =
		wp_get_attachment_image_src( $custom_logo_id , 'full' );
	if ( is_array($custom_logo) ) $custom_logo = $custom_logo[0];

	
	if ( ! $custom_logo ) $custom_logo = '';
	
	return $custom_logo;
}
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
// used in  deep-schema-premium.php
//			actus-data.php



// timezone
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
function acsc_wp_timezone(){
	$offset  = (float) get_option( 'gmt_offset' );
	$hours   = (int) $offset;
	$minutes = ( $offset - $hours );

	$sign      = ( $offset < 0 ) ? '-' : '+';
	$abs_hour  = abs( $hours );
	$abs_mins  = abs( $minutes * 60 );
	$acsc_tz_offset = sprintf( '%s%02d:%02d', $sign, $abs_hour, $abs_mins );

	return $acsc_tz_offset;
}





?>