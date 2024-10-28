<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

acsc_trc('━━━━ acsc-FE-dynamic.php');
global $acsc_retrieved;
$acsc_retrieved=array();

function acsc_dynamic_values( $item ){
	global $ACSC, $parsed_media, $acsc_retrieved;
	if ( ! $parsed_media ) $parsed_media = array();
	
	
	$wpID = 0;
	// get ID from 'wp' object
	if ( isset($ACSC['wp']) && isset($ACSC['wp']['id'] ))
		$wpID = $ACSC['wp']['id'];
	
	
	// get ID from item '_dynamic'
	if ( isset( $item['_dynamic'] ) ) {
		if ( isset( $item['_dynamic']['wpID'] ) &&
			 $wpID != $item['_dynamic']['wpID'] ){
			$wpID = $item['_dynamic']['wpID'];
			//$ACSC['wp'] = acsc_get_WP_meta( $wpID );
		}
		// clear media - will be loaded from post meta or parsed
		unset( $item['_dynamic']['image'] );
		unset( $item['_dynamic']['video'] );
		unset( $item['_dynamic']['audio'] );
		unset( $item['_dynamic']['primaryImageOfPage._t_contentUrl'] );
		unset( $item['_dynamic']['primaryImageOfPage.contentUrl'] );
		unset( $item['_dynamic']['primaryImageOfPage.height'] );
		unset( $item['_dynamic']['primaryImageOfPage.width'] );
		unset( $item['_dynamic']['primaryImageOfPage.thumbnail'] );
	}
	
	$itemID = explode('-', $item['@id']);
	$itemID = intval( $itemID[ sizeof($itemID)-1 ] );
	if ( $wpID == '0' ) $wpID = $itemID;
	

	// Get media from content
	// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
	if ( $wpID == $itemID && ! in_array($wpID, $acsc_retrieved) ) {
acsc_trc('>>>> FE-dynamic - Get media from content');
		// Parse WP media
		$ACSC['wp'] = acsc_wp_parse_post_media($wpID, $ACSC['wp']);
		$parsed_media[] = $wpID;

		if ( $item ) {
			$acsc_retrieved[] = $wpID;
			acsc_trc('acsc_dynamic_values : '.$wpID . ' - ' . $itemID);
		}
		
	}

	if ( ! $item ) return $item;
	


	// replace dynamic placeholders
	foreach ( $item as $key => $row ) {
		$fullkey = $key;
		if ( $key == '_dynamic' ) continue;
		
		
		if ( is_string($row) ) {
			$row = acsc_dynamic_val( $row, $key, $item, $fullkey );
			$item[$key] = $row;

			
		} else {
			
			if ( is_array($row) ){
			foreach( $row as $key2 => $row2 ){

				if ( is_string($row2) ) {
					$fullkey = "$key.$key2";

					
					$row2 = acsc_dynamic_val( $row2, $key2, $item, $fullkey );
					$row[$key2] = $row2;


				} else {
					
					if ( is_array($row2) ) {
					foreach( $row2 as $key3 => $row3 ){
						if ( is_string($row3) ) {
							$fullkey = "$key.$key2.$key3";
							
							
							$row3 = acsc_dynamic_val( $row3, $key3, $item, $fullkey );
							$row2[$key3] = $row3;
						
						} else {
							
							if ( is_array($row3) ){
							foreach( $row3 as $key4 => $row4 ){
								if ( is_string($row4) ) {
									$fullkey = "$key.$key2.$key3.$key4";
									$row4 = acsc_dynamic_val($row4, $key4, $item, $fullkey);
									$row3[$key4] = $row4;
								} else {
									
									if ( is_array($row4) ) {
									foreach( $row4 as $key5 => $row5 ){
										if ( is_string($row5) ) {
											$fullkey = "$key.$key2.$key3.$key4.$key5";
											$row5 = acsc_dynamic_val($row5, $key5, $item, $fullkey);
											$row4[$key5] = $row5;
										} else {

											if ( is_array($row5) ) {
											foreach( $row5 as $key6 => $row6 ){
												if ( is_string($row6) ) {
													$fullkey = "$key.$key2.$key3.$key4.$key5.$key6";
													$row6 = acsc_dynamic_val($row6, $key6, $item, $fullkey);
													$row5[$key6] = $row6;
												}
		
											}
											}
											$row4[$key5] = $row5;

											
										}

									}
									}
									$row3[$key4] = $row4;

									
								}
								
							}
							}
							$row2[$key3] = $row3;
						}

					}
					}
					$row[$key2] = $row2;
				}
			}
			}
			$item[$key] = $row;
		}
		
		

	}
	
	return $item;
}

// value - key - item - fullkey
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
function acsc_dynamic_val( $val, $key="", $item=array(), $fullkey="" ){
	global $ACSC, $post;
	$dyn_label = "";
	
	if ( ! $val ) return $val;
	if ( is_array($val) ) {
		// if it's an @id value, return the @id
		if ( $val['@id'] ) $val = $val['@id'];
		// clean extra hash
		if ( is_string($val) &&
			 substr($val, 0, 2) == '##')
			$val = substr($val, 1);
	}
	// return if value is not string
	if ( ! is_string($val) ) return $val;

	

	
	if ( substr($val, 0, 1) == '{' &&
	     substr($val, -1) == '}' ) {
	// ┅┅┅┅┅┅┅┅┅┅ if it's dynamic placeholder
		
		// strip brackets
		$dyn_label = trim( substr($val, 1, -1) );
		
		// rename placeholders
		if ( $dyn_label == 'content_images' )
			$dyn_label = 'image';
		if ( $dyn_label == 'all_videos' )
			$dyn_label = 'video';
		

		
		if ( isset( $ACSC['wp'][ $dyn_label ] ) ){
		// ┅┅┅┅┅┅┅┅┅┅
			
			// get dynamic value from Wordpress
			// ▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒
			$val = $ACSC['wp'][ $dyn_label ];

			
			// ???
			// set '#post-' values to '#{postype}-'
			if ( is_string($val) &&
				 substr($val, 0, 6) == '#post-' )
				$val = '#'.$ACSC['wp']['post_type'].'-'.substr($val,6);

		} else $val = '';


	}


	if ( isset( $item['_dynamic'] ) &&
	     ( isset( $item['_dynamic'][$dyn_label] ) ||
		   isset( $item['_dynamic'][$fullkey] ) ||
		   isset( $item['_dynamic'][$key] ) )) {
	// ┅┅┅┅┅┅┅┅┅┅ if key exists in _dynamic

		
		if ( $dyn_label ) {
		// ┅┅┅┅┅┅┅┅┅┅ if dynamic p/holder exists
			
			// get dynamic value from item _dynamic
			// ▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒
			$item['_dynamic']['wpID'] =
				intval( $item['_dynamic']['wpID'] );
			
			
			
			if ( $item['_dynamic']['wpID'] != $post->ID ||
				 $val == null ) {
			// ┅┅┅┅┅┅┅┅┅┅ item schema with diff post id or
			//			  value is null
					
				if (isset( $item['_dynamic'][$key] ) ){
					$val = $item['_dynamic'][$key];
				}
				if (isset( $item['_dynamic'][$fullkey] ) ){
					$val = $item['_dynamic'][$fullkey];
				}
				if (isset( $item['_dynamic'][$dyn_label] ) ){
					$val = $item['_dynamic'][$dyn_label];
				}
				
				
			}

		}


	} else {
	// else get value from dynamic_labels

		// replace dynamic placeholder strings
		if ( is_string($val) ) {
			if ( $matches_count = preg_match_all("/\{(.*?)\}/", $val, $matches)) {
				
				foreach ( $matches[1] as $key){
					if ( isset( $ACSC['wp'][$key] ) ) {
						$val = str_replace('{'.$key.'}', $ACSC['wp'][$key], $val);
					}	
				}
			}
		}


	}
	

	
	
	// VIDEO CLIP
	// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
	if ( is_string( $val ) &&
		 ( substr($val, 0, 6) == '#Clip-' ||
		   substr($val, 0, 13) == '#VideoObject-' )  ){
		
		if ( isset($item['_dynamic'][$fullkey]) )
			$val = $item['_dynamic'][$fullkey];
		
		
	}

	
	
	

		
	if ( $key == 'price' && ! $val) $val = 0;
	
	

	if ( $key == 'name' && is_string($val) )
		$val = sanitize_text_field( $val );


	
	return $val;
}

?>