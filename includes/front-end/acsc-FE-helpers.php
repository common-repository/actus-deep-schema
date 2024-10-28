<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      

acsc_trc('━━━━ acsc-FE-helpers.php');

// set page template for Deep Schema View page
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
add_filter( 'page_template', 'acsc_view_template', 10, 3 );
function acsc_view_template( $template, $type, $templates ) {
	
	$plugin_name =
		basename( plugin_dir_path( dirname( __FILE__ , 2 ) ) );

    if( 'ads-view.php' == basename( $templates[0] ) ) {
        $template = WP_PLUGIN_DIR . "/$plugin_name/ads-view.php";
	}
	
    return $template;
}




// acsc_str_replace()
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
if (!function_exists('acsc_get_all_attached_images')) {
    function acsc_get_all_attached_images($id, $size = "full") {
        
        // get images using the get_posts() function
    	$images = get_posts(array(
    		'numberposts'    => -1, // get all images
    		'post_mime_type' => 'image', // so we can get images
    		'post_parent'    => $id, // get images attached to the current post
    		'post_type'      => 'attachment',
            'order'          => 'ASC',
            'orderby'        => 'menu_order' // pull them out in the order you set them as
    	));
    	
    	// loop through images and display them
    	// you can add HTML into this section to display them how you wish
		foreach($images as $image) {
			echo wp_kses_post( wp_get_attachment_image($image->ID, $size) ); // returns an image HTML tag if there is one
		}
	}
}
if (!function_exists('acsc_str_replace'))
{
   function acsc_str_replace($search, $replace, $subject, &$count = 0)
   {
      if (!is_array($subject))
      {
         $searches = is_array($search) ? array_values($search) : array($search);
         $replacements = is_array($replace) ? array_values($replace) : array($replace);
         $replacements = array_pad($replacements, count($searches), '');
         foreach ($searches as $key => $search)
         {
            $parts = mb_split(preg_quote($search), $subject);
            $count += count($parts) - 1;
            $subject = implode($replacements[$key], $parts);
         }
      }
      else
      {
         foreach ($subject as $key => $value)
         {
            $subject[$key] = acsc_str_replace($search, $replace, $value, $count);
         }
      }
      return $subject;
   }
}

// acsc_recursiveFind_FE()
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
function acsc_recursiveFind_FE($array, $needle) {
	if ( ! is_array($array) ) return [];
  $iterator = new RecursiveArrayIterator($array);
  $recursive = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::SELF_FIRST);
  $return = [];
  foreach ($recursive as $key => $value) {
    if ($key === $needle) {
      $return[] = $value;
    }
  } 
  return $return;
}



// filter allowed schemas and fields ($ACSC['schema_fe'])
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
function acsc_allow_props(){
	global $ACSC;
	if ( sizeof($ACSC['options']['modules_active']) > 1 )
		return;
	
	$block_schemas = array(
		'audience', 'items'
	);
	$block_fields = array(
		'website'	=> ['audience'],
		'busi'	=> ['vatID', 'taxID', 'brand', 'priceRange', 'servesCuisine', 'hasMenu', 'acceptsReservations', 'audience', 'amenityFeature', 'iataCode', 'medicalSpecialty', 'healthPlanNetworkId', 'sport', 'contactPoint', 'areaServed', 'geo', 'founder', 'employee', 'department', 'subOrganization'],
		'page'	=> ['medicalAudience', 'audience', 'mainEntity', 'image', 'video', 'audio', '_collection'],
		'post'	=> ['dependencies', 'proficiencyLevel', 'audience'],
	);
	
	
	foreach($ACSC['schema_fe'] as $sc => $scopes){
		foreach( $scopes as $id => $row){
			
			if ( in_array($sc, $block_schemas) ) {
				unset( $ACSC['schema_fe'][ $sc ][ $id ] );
				
			} else {
				foreach( $row as $key => $val){

					$block =
						$block_fields[ explode('-', $id)[0] ];
					if ( is_array( $block ) &&
						 in_array($key, $block) ) {
						unset( $ACSC['schema_fe'][ $sc ][ $id ][ $key ] );
					}

				}
			}
			
		}
	}
	
	
}










// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ ???
function acsc_short_business( $item ){
	if ( ! $item ) return;
	
	$loaded = get_option( "ACSC-" . substr($item['@id'], 1) );
	$short = array(
		'@id'	=> $item['@id'] . '_short',
		'@type'	=> $loaded['@type'],
		'name'	=> $loaded['name'],
		'logo'	=> $loaded['logo'],
		'image'	=> $loaded['image'],
		'email'	=> $loaded['email'],
		'telephone'	=> $loaded['telephone'],
		//'address'	=> $publisher['address'],
	);

	return $short;
}




function acsc_get_item_by_id( $id ){
	global $ACSC;
	
	if ( substr($id, 0, 6) == '#audi-' )
		$scope = 'audience';
	if ( substr($id, 0, 6) == '#busi-' )
		$scope = 'business';
	if ( substr($id, 0, 6) == '#pers-' )
		$scope = 'persons';
	if ( substr($id, 0, 6) == '#page-' )
		$scope = 'page';
	if ( substr($id, 0, 6) == '#post-' )
		$scope = 'post';

	$item = $ACSC['schema_fe'][ $scope ][substr($id, 1)];
	
	$item['@id'] = $id;
		
	return $item;
}








//add_filter( 'wpseo_schema_graph',
		    //'acsc_yoast_test', 10, 2 );
function acsc_yoast_test( $data, $context ){
	//_cos( $data );
}
 


?>