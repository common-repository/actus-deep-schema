<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      




// ≣≣≣≣ Cooked
// ≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣
function acsc_cooked( $mode="" ){
	global $ACSC;
	if ( ! acsc_is_active('cooked') ) return false;
	acsc_trc('--- Cooked');
	
	$options = acsc_opt();



	// WP Recipe Meta
	// hook: acsc-WP-meta ( $data ) - on wp/post-data.php
	// modifies WP $data
	// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	add_filter( 'acsc-WP-meta', 'acsc_cooked_WP_meta' );
	// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	add_filter( 'acsc-WP-labels', 'acsc_cooked_WP_meta_labels' );
	// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	

	// Images from gallery
	// ■■■■■■■■■■■■■■■■■■■■
	add_filter( 'acsc-content-images',
				'acsc_cooked_gallery_images', 10, 2 );

	// Images from directions
	// ■■■■■■■■■■■■■■■■■■■■
	add_filter( 'acsc-content-images',
				'acsc_cooked_directions_images', 10, 2 );


	// Youtube Urls from gallery
	// ■■■■■■■■■■■■■■■■■■■■
	add_filter( 'acsc-youtube-urls',
				'acsc_cooked_youtube_urls', 20, 2 );

	// Filter Item Dynamic
	// ■■■■■■■■■■■■■■■■■■■■
	add_filter( 'acsc-FE-item-dynamic',
				'acsc_cooked_filter_item_dynamic' );




	// TARGETING
	if ( acsc_is_enabled('cooked') ) {
		
		// RECIPE
		// set Recipe schema as default for cp_recipe post type
		if ( ! isset($options['targets']['cp_recipe']) ||
			 ! $options['targets']['cp_recipe'] ) {
			$options = acsc_opt();
			$options['targets']['cp_recipe'] = 'Recipe';
			update_option( "ACSC-options", $options, false );
			
		}
		
	}
	
	
	// TEMPLATE
	acsc_cooked_template();
	
	
	
	// HOOKS
	acsc_cooked_hooks();
	
	
}



// HOOKS
// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
function acsc_cooked_hooks( $mode="" ){
	global $ACSC;
	
	
	if (  isset($options['plugins']['cooked_enable']) &&
		  $ACSC['options']['plugins']['cooked_enable'] ) {

		
		// fill Recipe template values
		// return cp_recipe-0 template
		acsc_hook('schemaTemplates',
				  'acsc_hook_cooked_schemaTemplates');
	


					


		
	}
		
	$text = '<h3><b>PLUGIN integrated</b> : Cooked</h3>';
	$clss = 'note-plugin note-plugin-cooked acsc-sc active';
	
	
	if ( !isset($ACSC['options']['plugins']['cooked_enable']) ||
		 ! $ACSC['options']['plugins']['cooked_enable'] ) {
		
		$text = '<h3><b>PLUGIN detected</b> : Cooked</h3> <p>Enable connection with this plugin.</p>';
		$clss = 'note-plugin note-plugin-cooked acsc-sc';
		
	}
	
	// note on admin home screen
	// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	acsc_hook('js_admin_home', 'admin_note', array(
		'icon'	=> 'extension',
		'clss'	=> $clss,
		'text'	=> $text,
		'controls' 	=> array(
			array(
				'type'	=> 'toggle',
				'name'	=> 'plugins.cooked_enable',
				'label'	=> 'enabled',
				'autosave' => true,
			),
		),
	));
	
	

	// options on settings
	// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	acsc_hook('js_admin_settings', 'settings', array(
		array( 'type'	=> 'seperator' ),
		array(
			'type'	=> 'toggle',
			'name'	=> 'plugins.cooked_enable',
			'label'	=> 'Cooked',
			'note'	=> 'Enable connection with <b>Cooked</b> plugin.<br>The Recipe schema will be assigned on every recipe page and will automatically get values from the recipe.<br>(refresh the page after saving options)',
		),
	));
	
	
	
	// Filter Proccessed Schemas
	// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	add_filter( 'acsc-FE-proccessed-schemas',
			    'acsc_cooked_proccessed_schemas', 10, 1 );
	
	// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	

	

	// Add product info to schema form
	// if selected default is another schema type
	// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	/*
	acsc_hook('js_schema_form', 'product_form_add', array(
		'name'   => 'product_info',
		'label'	 => 'product info',
		'type'	 => 'section',
		'fields' => array(
			array(
				'name' 	=> 'sku',
				'label'	=> 'sku',
			),
			array(
				'name' 	=> 'gtin',
				'label'	=> 'gtin',
			),
			array(
				'name' 	=> 'category',
				'label'	=> 'categories',
			),
		),
	));
	*/
	// ▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒
	
	
	
}




// WP Recipe meta
// ------------------------------------------------------
// hook: acsc-WP-meta ( $data ) - on wp/post-data.php
// modifies WP $data
function acsc_cooked_WP_meta( $data ){
	if ( ! acsc_is_active('cooked') ) return array();
	if ( ! acsc_is_enabled('cooked') ) return array();
	global $post;
	if ( isset($post) && $post ) {
		if ( $post->post_type != 'cp_recipe' ) return $data;
	} else {
		if ( ! isset($data['_ptype']) || $data['_ptype'] != 'cp_recipe' )  return $data;
	}
	
	acsc_trc('    acsc_cooked_WP_meta');


	//$data['_dynamic_labels'] = acsc_cooked_WP_meta_labels();



	// parse Cooked Data
	// ------------------------------------------
	//$data['settings'] = get_post_meta($pid, '_recipe_settings', true);
	$post_id = 0;
	if ( $post ) $post_id = $post->ID;
	if ( isset( $data ) && isset( $data['id'] ) )
		$post_id = $data['id'];
	
	$vals = get_post_meta( $post_id, '_recipe_settings', true);
	if ( ! is_array($vals) ) return $data;

	
	$data['cooked_seo_description'] = $vals['seo_description'];
	$data['cooked_excerpt'] = $vals['excerpt'];

	if ( isset($vals['prep_time']) )
		$data['cooked_prep_time'] = acsc_cooked_min_to_duration( $vals['prep_time'] );
	if ( isset($vals['cook_time']) )
		$data['cooked_cook_time']  = acsc_cooked_min_to_duration( $vals['cook_time'] );
	if ( isset($vals['total_time']) )
		$data['cooked_total_time'] =  acsc_cooked_min_to_duration( $vals['total_time'] );


	$data['cooked_directions'] 	= acsc_cooked_directions( $vals['directions'], $post_id );
	$data['cooked_ingredients'] = acsc_cooked_ingredients( $vals['ingredients'] );

	$data['cooked_gallery'] = $vals['gallery'];

	$data['cooked_servings'] = $vals['nutrition']['servings'];
	if ( $data['cooked_servings'] )
		$data['cooked_servings'] .= ' servings';

	$data['cooked_serving_size'] = $vals['nutrition']['serving_size'];
	//if ( intval($vals['nutrition']['servings']) > 1 )
		//$data['cooked_serving_size'] .=
			//' x ' . $vals['nutrition']['servings'];

	$data['cooked_calories'] 	= $vals['nutrition']['calories'];
	$data['cooked_carbs'] 		= $vals['nutrition']['carbs'];
	$data['cooked_cholesterol'] = $vals['nutrition']['cholesterol'];
	$data['cooked_fat'] 		= $vals['nutrition']['fat'];
	$data['cooked_fiber'] 		= $vals['nutrition']['fiber'];
	$data['cooked_protein'] 	= $vals['nutrition']['protein'];
	$data['cooked_sat_fat'] 	= $vals['nutrition']['sat_fat'];
	$data['cooked_sodium'] 		= $vals['nutrition']['sodium'];
	$data['cooked_sugars'] 		= $vals['nutrition']['sugars'];
	$data['cooked_trans_fat'] 	= $vals['nutrition']['trans_fat'];


	
	/*
	if ( ! isset($data['_dynamic_labels']) )
		$data['_dynamic_labels'] = array();

	$data['_dynamic_labels'] = array_merge( 
		$data['_dynamic_labels'],
		acsc_cooked_WP_meta_labels()
	);
	*/
	
	
	
	
	return $data;
}

// Get Meta Labels
// ------------------------------------------------------	
function acsc_cooked_WP_meta_labels( $data = array() ){
	if ( ! acsc_is_active('cooked') ) return array();
    if ( ! is_admin() && ! wp_doing_cron() ) return array(); // Front End

	acsc_trc('    acsc_cooked_WP_meta_labels');


	//$vars = array();
	//$vars['cooked'] = array(
	$meta = array(
		'TITLE-ROW-cooked-meta'	=> 'COOKED RECIPE META',
		
		'cooked_excerpt'		=> 'Cooked excerpt',
		'cooked_prep_time'		=> 'Cooked prep time',
		'cooked_cook_time'		=> 'Cooked cook time',
		'cooked_total_time'		=> 'Cooked total time',
		'cooked_directions'		=> 'Cooked directions',
		'cooked_ingredients'	=> 'Cooked ingredients',
		'cooked_gallery'		=> 'Cooked gallery',
		'cooked_seo_description'=> 'Cooked SEO description',
		
		'cooked_servings'		=> 'Cooked servings',
		'cooked_serving_size'	=> 'Cooked serving size',
		'cooked_calories'		=> 'Cooked calories',
		'cooked_carbs'			=> 'Cooked carbohydrate',
		'cooked_cholesterol'	=> 'Cooked cholesterol',
		'cooked_fat'			=> 'Cooked total fat',
		'cooked_fiber'			=> 'Cooked fiber',
		'cooked_protein'		=> 'Cooked protein',
		'cooked_sat_fat'		=> 'Cooked saturated fat',
		'cooked_sodium'			=> 'Cooked sodium',
		'cooked_sugars'			=> 'Cooked sugars',
		'cooked_trans_fat'		=> 'Cooked trans fat',
		'_recipe_settings'		=> 'Recipe settings',

		'SPACE-ROW-cooked-meta' => '',
		
		
	);

	
	if ( ! isset($data['meta']) ) $data['meta'] = array();
	$data['meta'] = array_merge($data['meta'], $meta );

	return $data;


}

	



// TEMPLATE
// ------------------------------------------------------
// -- sets plugin dynamic values to Recipe template
// -- return cp_recipe-0 to $ACSC['templates']
function acsc_cooked_template(){
	global $ACSC;
    if ( wp_doing_ajax() ) return false;
	//if ( ! is_admin() && ! wp_doing_cron() ) return array(); // Front End
	
	acsc_trc('        acsc_cooked_template');


	// TEMPLATE cp_recipe
	$tmpl = array();
	if ( isset($ACSC['templates']) &&
	     isset($ACSC['templates']['Recipe']) )
		$tmpl = $ACSC['templates']['Recipe'];


	$tmpl['@id']  = '{post_id}';
	$tmpl['name'] = '{post_title}';
	$tmpl['description'] = '{post_excerpt}';
	$tmpl['url']  = '{page_url}';


	$tmpl['keywords'] = '{post_tag}';
	$tmpl['recipeCategory']  = '{cp_recipe_category}';

 
	/*
	$tmpl['image'][0] = array(
		//'_t_contentUrl'	=> $featured,
		"@type" 		=> "ImageObject",
		'name'			=> "{featured_alt}",
		'caption'		=> "{featured_caption}",
		'contentUrl'	=> "{featured_url}",
		'thumbnail'		=> "{featured_thumb}",
		'width'			=> "{featured_w}",
		'height'		=> "{featured_h}",
	);
	*/
	$tmpl['image'] = '{all_images}';
	$tmpl["video"] = "{all_videos}";
	
	/*
	$tmpl['aggregateRating'] = array(
		'ratingValue' => '{_wc_average_rating}',
		'ratingCount' => '{_wc_review_count}',
		'bestRating'  => 5,
		'worstRating' => 1,
	);
	*/
	
	
	$tmpl['cookTime']  = '{cooked_cook_time}';
	$tmpl['prepTime']  = '{cooked_prep_time}';
	$tmpl['totalTime'] = '{cooked_total_time}';
	
	
	$tmpl['recipeInstructions'] = '{cooked_directions}';
	$tmpl['recipeIngredient'] = '{cooked_ingredients}';
	$tmpl['recipeYield'] = array('{cooked_servings}');
	$tmpl['nutrition']['servingSize'] = '{cooked_serving_size}';
	
	$tmpl['nutrition']['calories'] = '{cooked_calories}';
	$tmpl['nutrition']['carbohydrateContent'] = '{cooked_carbs}';
	$tmpl['nutrition']['cholesterolContent'] = '{cooked_cholesterol}';
	$tmpl['nutrition']['fatContent'] = '{cooked_fat}';
	$tmpl['nutrition']['fiberContent'] = '{cooked_fiber}';
	$tmpl['nutrition']['proteinContent'] = '{cooked_protein}';
	$tmpl['nutrition']['saturatedFatContent'] = '{cooked_sat_fat}';
	$tmpl['nutrition']['sodiumContent'] = '{cooked_sodium}';
	$tmpl['nutrition']['sugarContent'] = '{cooked_sugars}';
	$tmpl['nutrition']['transFatContent'] = '{cooked_trans_fat}';
	

	$ACSC['templates']['cp_recipe-0'] = $tmpl;
	
	return $tmpl;
}
function acsc_hook_cooked_schemaTemplates( $templates ){
	// HOOKS on acsc_schema_templates()
	// -- sets plugin dynamic values to Recipe template
	global $ACSC;
	
	// set plugin dynamic values to Recipe template
	acsc_cooked_template();
	
	return $ACSC['templates'];
}

// XXXXXXX
function acsc_cooked_filter_item_dynamic( $dyn=array() ){
return $dyn;
	if ( ! acsc_is_active('cooked') ) return $dyn;
	if ( ! acsc_is_enabled('cooked') ) return $dyn;
	global $post;
	if ( isset($post) && $post ) {
		if ( $post->post_type != 'cp_recipe' ) return $dyn;
	} else {
		if ( ! isset($data['_ptype']) || $data['_ptype'] != 'cp_recipe' )  return $dyn;
	}

	
	acsc_trc('        acsc_cooked_filter_item_dynamic');


	
	$post_id = 0;
	if ( $post ) $post_id = $post->ID;
	if ( isset( $dyn ) && isset( $dyn['wpID'] ) )
		$post_id = $dyn['wpID'];
	
	$vals = get_post_meta($post_id, '_recipe_settings',true);


	if ( ! $vals ) return $dyn;
	
	$dyn['cooked_seo_description'] = $vals['seo_description'];
	$dyn['cooked_excerpt'] = $vals['excerpt'];
	$dyn['cooked_prep_time'] = 'PT' . $vals['prep_time'] . 'M';
	$dyn['cooked_cook_time'] = 'PT' . $vals['cook_time'] . 'M';
	$dyn['cooked_total_time'] = 'PT' . $vals['total_time'] . 'M';
	$dyn['cooked_directions'] = acsc_cooked_directions( $vals['directions'], $post_id );


	$dyn['cooked_ingredients'] =  acsc_cooked_ingredients( $vals['ingredients'] );
	
	$dyn['cooked_gallery'] = $vals['gallery'];

	$dyn['cooked_servings'] = $vals['nutrition']['servings'];
	if ( $dyn['cooked_servings'] )
		$dyn['cooked_servings'] .= ' servings';
	
	$dyn['cooked_serving_size'] = $vals['nutrition']['serving_size'];
	//if ( intval($vals['nutrition']['servings']) > 1 )
		//$dyn['cooked_serving_size'] .=
			//' x ' . $vals['nutrition']['servings'];

	$dyn['cooked_calories'] = $vals['nutrition']['calories'];
	$dyn['cooked_carbs'] = $vals['nutrition']['carbs'];
	$dyn['cooked_cholesterol'] = $vals['nutrition']['cholesterol'];
	$dyn['cooked_fat'] = $vals['nutrition']['fat'];
	$dyn['cooked_fiber'] = $vals['nutrition']['fiber'];
	$dyn['cooked_protein'] = $vals['nutrition']['protein'];
	$dyn['cooked_sat_fat'] = $vals['nutrition']['sat_fat'];
	$dyn['cooked_sodium'] = $vals['nutrition']['sodium'];
	$dyn['cooked_sugars'] = $vals['nutrition']['sugars'];
	$dyn['cooked_trans_fat'] = $vals['nutrition']['trans_fat'];

	
	return $dyn;
}
// XXXXXXX


function acsc_cooked_image_object_by_id( $ID, $post_id ){


	$opt = get_option( 'ACSC-options' );
	if ( ! $opt['thumb_size'] ) $opt['thumb_size'] = 'thumbnail';
	
	$image_url = wp_get_attachment_image_src( $ID, 'full' )[0];
	
	$saved = acsc_saved_media( $post_id );
//_cos([$saved['images'], $image_url]);
	foreach ($saved['images'] as $item) {
		if ( $image_url && isset($item['contentUrl']) &&
		     $item['contentUrl'] == $image_url ) {
			$result = $item;
			break;
		}
	}
	if ( isset($result) && sizeof($result) ) return $result;



	acsc_trc('        acsc_cooked_image_object_by_id');


	$thumb = wp_get_attachment_image_src( $ID, $opt['thumb_size'] )[0];
	
	$image_caption = wp_get_attachment_caption( $ID );
	$image_alt = get_post_meta($ID, '_wp_attachment_image_alt', TRUE);

	if ( ! isset($image_w) ) $image_w = "";
	if ( ! isset($image_h) ) $image_h = "";
	$newRec = array(
		"@type" => "ImageObject",
		"_t_contentUrl" => $thumb ? $thumb : $image_url,
		"url"		=> $image_url,
		"contentUrl"=> $image_url,
		"name"  	=> $image_alt,
		"caption" 	=> $image_caption,
		"width" 	=> $image_w,
		"height" 	=> $image_h,
	);
	if ( $thumb != '-' )
		$newRec['thumbnail'] = $thumb;
	
	
	return $newRec;
	
}
function acsc_cooked_directions_images( $images=array(), $post_id="0" ){
	global $post;
	if ( ! isset($post) ) $post = get_post( intval($post_id) );
	if ( isset($post) && $post ) {
		if ( $post->post_type != 'cp_recipe' ) return $images;
	} else {
		if ( ! isset($data['_ptype']) || $data['_ptype'] != 'cp_recipe' )  return $images;
	}

	acsc_trc('        acsc_cooked_directions_images');

	$vals = get_post_meta( $post_id, '_recipe_settings', true);
	
	$direction_images = array();
	if ( is_array($vals) &&
		 isset($vals['directions']) &&
		 is_array($vals['directions']) ) {

		foreach($vals['directions'] as $row){
			$ID = $row['image'];
			$direction_images[] = acsc_cooked_image_object_by_id( $ID, $post_id );
		}
		$images = array_merge( $images, $direction_images );

	}

	return $images;

}
function acsc_cooked_gallery_images( $images=array(), $post_id="0" ){
	global $post;
	if ( isset($post) && $post ) {
		if ( $post->post_type != 'cp_recipe' ) return $images;
	} else {
		if ( ! isset($data['_ptype']) || $data['_ptype'] != 'cp_recipe' )  return $images;
	}

	acsc_trc('        acsc_cooked_gallery_images');


	$vals = get_post_meta( $post_id, '_recipe_settings', true);

	$gallery_images = array();
	if ( is_array($vals) &&
		 isset($vals['gallery']) &&
		 isset($vals['gallery']['items']) &&
	     is_array($vals['gallery']['items']) ) {
		foreach($vals['gallery']['items'] as $ID){
			
			$gallery_images[] = acsc_cooked_image_object_by_id( $ID, $post_id );
			
		}
		$images = array_merge( $images, $gallery_images );
		
	}
	
	
	return $images;
}
function acsc_cooked_youtube_urls( $urls=array(), $post_id=0 ){
	global $post;
	if ( isset($post) && $post ) {
		if ( $post->post_type != 'cp_recipe' ) return $urls;
	} else {
		if ( ! isset($data['_ptype']) || $data['_ptype'] != 'cp_recipe' )  return $urls;
	}

	acsc_trc('        acsc_cooked_youtube_urls');


	$pattern = "/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be)\/watch\?v=([a-zA-Z0-9_-]+)$/";
	
 	$vals = get_post_meta( $post_id, '_recipe_settings',
						   true);
	if ( is_array($vals) &&
		 isset($vals['gallery']) &&
		 isset($vals['gallery']['video_url']) )
		if (preg_match($pattern,
					   $vals['gallery']['video_url'], $matches))
			$urls[] = $vals['gallery']['video_url'];
		
	return $urls;
	
}

function acsc_cooked_ingredients( $vals ){

	


	$result = array();
	
	foreach ($vals as $row){
		
		$result[] = $row['amount'] . ' ' .
					$row['measurement'] . ' ' .
					$row['name'];
			
	}
	
	return $result;
}
function acsc_cooked_directions( $vals, $post_id=0 ){
	$result = array();
	
	foreach ($vals as $row){
		
		$image = '';
		if ( isset($row['image']) && $row['image'] ) {
			$image = acsc_cooked_image_object_by_id( $row['image'], $post_id );
			$image = $image['url'];
		}

		$result[] = array(
			"@type"	=> "HowToStep",
			"name"	=> "",
			"text"	=> $row['content'],
			"image"	=> $image,
			"video"	=> array(
				"@id" => ""
			)
		);
		
	}
	
	
	return $result;
}
	

function acsc_cooked_proccessed_schemas( $schemas ) {
	global $ACSC;

	foreach ($schemas as $idx => $schema){

		$selected = $ACSC['options']['targets']['cp_recipe'];


	}

	return $schemas;
}
function acsc_cooked_min_to_duration( $minutes ){
	
	$hours = floor($minutes / 60);
	$minutes = $minutes - floor($minutes / 60) * 60;
	$time = 'PT';
	if ( $hours ) $time .= $hours . "H";
	$time .= $minutes . "M";
	
	return $time;
}



?>