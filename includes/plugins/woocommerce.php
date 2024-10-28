<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      





// ≣≣≣≣ Woocommerce
// ≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣
function acsc_woocommerce( $mode="" ){
	global $ACSC;
	if ( ! acsc_is_active('woocommerce') ) return false;
	
	if ( ! isset($ACSC['options'] ) ) $ACSC['options'] = acsc_opt();
	acsc_trc('--- Woocommerce');


	// TARGETING
	if ( acsc_is_enabled('woo') ) {
		
		// set Product schema as default for product post type
		if ( ! isset($ACSC['options']['targets']['product']) ||
			! $ACSC['options']['targets']['product'] ) {
			$ACSC['options'] = acsc_opt();
			$ACSC['options']['targets']['product'] = 'Product';
			update_option( "ACSC-options", $ACSC['options'], false );
		}
		
	}
	
	if ( ! $mode ) {
	
		// TEMPLATE
		// -- sets plugin dynamic values to Product template
		// -- return product-0 template
		acsc_woocommerce_template();

	}
	
	// HOOKS
	acsc_woocommerce_hooks( $mode );
	
	
}


// HOOKS
// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
function acsc_woocommerce_hooks( $mode="" ){
	global $ACSC;
	if ( ! isset($ACSC['options'] ) ) return false;
	

	// if plugin is enabled
	if (  isset($ACSC['options']['plugins']['woo_enable']) &&
		  $ACSC['options']['plugins']['woo_enable'] ) {
		

    	// WP Dynamic Labels for Woocommerce
		// hook: acsc-WP-global-labels ( $data ) - on wp/system-data.php
		// modifies $ACSC['sys']['dynamic_labels']
		// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		if ( ! $mode || $mode == 'before-wp' )
			add_filter( 'acsc-WP-global-labels', 'acsc_woocommerce_WP_global_labels' );
		// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		if ( ! $mode || $mode == 'before-wp' )
			add_filter( 'acsc-WP-global-labels', 'acsc_woocommerce_WP_meta_labels' );
		// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■

			
			
		// WP global Woocommerce data
		// hook: acsc-WP-global ( $data ) - on wp/post-data.php
		// modifies WP $data
		// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		if ( ! $mode || $mode == 'before-wp' )
		add_filter( 'acsc-WP-global', 'acsc_woocommerce_WP_global' );
		// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	

				
    	// WP Product Meta
		// hook: acsc-WP-meta ( $data ) - on wp/post-data.php
		// modifies WP $data
		// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		if ( ! $mode || $mode == 'before-wp' )
			add_filter( 'acsc-WP-meta', 'acsc_woocommerce_WP_meta' );
		// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		

		


		// HOOKS on acsc_schema_templates()
		// -- sets plugin dynamic values to Product template
		// -- return product-0 template
		// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		if ( ! $mode )
		acsc_hook('schemaTemplates', 'acsc_hook_woocommerce_schemaTemplates');
		// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		



			
		// Product Images
		// ■■■■■■■■■■■■■■■■■■■■
		add_filter( 'acsc-WP-image-urls', 'acsc_woocommerce_product_image_urls', 10, 3 );

		
		
		// Product Images
		// ■■■■■■■■■■■■■■■■■■■■
		//add_filter( 'acsc-content-images', 'acsc_woocommerce_product_images', 10, 2 );

		
		
	}
	


	// sets home screen note text
	$text = '<h3><b>PLUGIN integrated</b> : Woocommerce</h3>';
	$clss = 'note-plugin note-plugin-woo acsc-sc active';
	if ( !isset($ACSC['options']['plugins']['woo_enable']) ||
		 ! $ACSC['options']['plugins']['woo_enable'] ) {
		
		$text = '<h3><b>PLUGIN detected</b> : Woocommerce</h3> <p>Enable connection with this plugin.</p>';
		$clss = 'note-plugin note-plugin-woo acsc-sc';
		
	}
	// note on admin home screen
	// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	if ( ! $mode )
	acsc_hook('js_admin_home', 'admin_note', array(
		'icon'	=> 'extension',
		'clss'	=> $clss,
		'text'	=> $text,
		'controls' 	=> array(
			array(
				'type'	=> 'toggle',
				'name'	=> 'plugins.woo_enable',
				'label'	=> 'enabled',
				'autosave' => true,
			),
		),
	));
		

	// options on settings
	// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	if ( ! $mode )
	acsc_hook('js_admin_settings', 'settings', array(
		array( 'type'	=> 'seperator' ),
		array(
			'type'	=> 'toggle',
			'name'	=> 'plugins.woo_enable',
			'label'	=> 'Woocommerce',
			'note'	=> 'Enable connection with <b>Woocommerce</b> plugin.<br>The Product schema will be assigned on every product page and will automatically get values from the product.<br>(refresh the page after saving options)',
		),
	));
	
	
	
	if ( ! $mode ) {

		// Filter Proccessed Schemas
		// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		add_filter( 'acsc-FE-proccessed-schemas',
					'acsc_woocommerce_proccessed_schemas', 10, 1 );
		// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■

		// -- adds Product @type to schema
		//    if selected default is another schema type
		// ------------------------------------------
		function acsc_woocommerce_proccessed_schemas( $schemas ) {
			global $ACSC;
			
			foreach ($schemas as $idx => $schema){
				if ( ! isset($ACSC['options']['targets']['product']) ) continue;

				// add Product @type to schema
				// if selected default is another schema type
				$selected = $ACSC['options']['targets']['product'];
				
				if ( is_array( $schema['@type'] ) &&
					in_array($selected, $schema['@type']) &&
					! in_array('Product', $schema['@type']) ){
					$schemas[ $idx ]['@type'][] = 'Product';
				}
			}
			
			return $schemas;
		}
	
	}
	

	

	// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	if ( ! $mode ) {
		// HOOKS on js_schema_form()
		// -- Adds product info to schema form
		// -- Adds pros & cons to schema form
		//    if selected default is another schema type
		acsc_hook('js_schema_form', 'product_form_add', array(
			array(
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
					array(
						'name' 	=> 'brand',
						'label'	=> 'Brands',
						'type' 	=> 'multi',
						'base' 	=> array(
							'type' 	=> 'select',
							'values'=> '[business]',
							'titles'=> '[business]',
							'ctx'=> array('dynamic', 'text', 'wiki'),
						),
					),
				),
			),
			array(
				'name'   => 'pros_cons_section',
				'label'	 => 'Pros & Cons',
				'type'	 => 'section',
				'fields' => array(
					array(
						'name' 	=> 'positiveNotes',
						'label'	=> 'pros',
						'type' 	=> 'multi',
						'base' 	=> array(),
					),
					array(
						'name' 	=> 'negativeNotes',
						'label'	=> 'cons',
						'type' 	=> 'multi',
						'base' 	=> array(),
					),
				),
			),
		));
	}	
	
	
}



// WP Product Images
// ------------------------------------------------------
// hook: acsc-WP-image-urls ( $urls ) - on wp/post-data.php
function acsc_woocommerce_product_image_urls( $urls=array(), $imgs=array(), $post_id="0" ){
	global $post;
	if ( ! isset($post) ) $post = get_post( intval($post_id) );
	if ( isset($post) && $post ) {
		if ( $post->post_type != 'product' ) return array($urls, $imgs);
	} else {
		if ( ! isset($data['_ptype']) || $data['_ptype'] != 'product' )  return array($urls, $imgs);
	}
	
	acsc_trc('        acsc_woocommerce_product_image_urls');
	$prod = wc_get_product( intval($post_id) );

	// Product Images
	if ( $prod ) {
		$attachment_ids = $prod->get_gallery_image_ids();
		foreach($attachment_ids as $attachment_id) {
			$urls[$attachment_id] = wp_get_attachment_url($attachment_id);
			$imgs[$attachment_id] = $attachment_id;
		}
	}

	return array($urls, $imgs);

}


// WP Product Images
// ------------------------------------------------------
// hook: acsc-content-images ( $images ) - on wp/post-data.php
function acsc_woocommerce_product_images( $images=array(), $post_id="0" ){
	acsc_trc('        acsc_woocommerce_product_images');
	global $post;
	if ( ! isset($post) ) $post = get_post( intval($post_id) );
	if ( isset($post) && $post ) {
		if ( $post->post_type != 'product' ) return $images;
	} else {
		if ( ! isset($data['_ptype']) || $data['_ptype'] != 'product' )  return $images;
	}

	
	$prod = wc_get_product( intval($post_id) );
	$opt = get_option( 'ACSC-options' );
	if ( ! $opt['thumb_size'] ) $opt['thumb_size'] = 'thumbnail';

	// Product Images
	$attachment_ids = $prod->get_gallery_image_ids();
	foreach($attachment_ids as $attachment_id) {
		$image_url = wp_get_attachment_url($attachment_id);
		$meta = wp_get_attachment_metadata( $attachment_id );
		$thumb = wp_get_attachment_image_src( $attachment_id, $opt['thumb_size'] )[0];
		$image_alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', TRUE);
		$image_caption = wp_get_attachment_caption( $attachment_id );
		if ( ! $image_caption ) $image_caption = $meta['image_meta']['caption'];
		$images[] = array(
			"@type" => "ImageObject",
			"_t_contentUrl" => $thumb ? $thumb : $image_url,
			"url"		=> $image_url,
			"contentUrl"=> $image_url,
			"name"  	=> $image_alt,
			"caption" 	=> $image_caption,
			"_title" 	=> $meta['image_meta']['title'],
			"width" 	=> $meta['width'],
			"height" 	=> $meta['height'], 
			"_key"	 	=> $attachment_id, 
			"_created"  => $meta['image_meta']['created_timestamp'], 
			"_filesize" => $meta['filesize'], 
		);
	}




	return $images;

}




// Dynamic Labels for Woocommerce
// ------------------------------------------------------
// hook: acsc-WP-global-labels ( $data ) - on wp/system-data.php
// modifies $ACSC['sys']['dynamic_labels']
function acsc_woocommerce_WP_global_labels( $data = array() ){
	if ( ! acsc_is_active('woocommerce') ) return $data;
	if ( ! acsc_is_enabled('woo') ) return $data;
	acsc_trc('    acsc_woocommerce_WP_global_labels');

	$vars = array(
		'TITLE-ROW-woo'        			=> 'WOOCOMMERCE',

		'woo_currency' 					=> 'currency',
		'woo_default_country' 			=> 'Country',
		'woo_specific_allowed_countries'=> 'allowed countries',
		//'woo_specific_ship_to_countries'=> 'specific ship to countries',
		'woo_ship_to_countries'			=> 'ship to countries',
		
		'woo_weight_unit'				=> 'weight unit',
		'woo_dimension_unit'			=> 'dimension unit',
		
		'woo_store_address'				=> 'store address',
		'woo_store_address_2'			=> 'store address 2',
		'woo_store_city'				=> 'store city',
		'woo_store_postcode'			=> 'store postcode',
		'woo_default_country' 			=> 'default country',

		'SPACE-ROW-woo'        			=> '',
	);
	//$data['woocommerce'] = $vars;

	if ( ! isset($data['global']) ) $data['global'] = array();
	$data['global'] = array_merge($data['global'], $vars );


	return $data;


}



// WP data for Woocommerce
// ------------------------------------------------------
// hook: acsc-WP-global-labels ( $data ) - on wp/system-data.php
// modifies $ACSC['sys']['dynamic_labels']
function acsc_woocommerce_WP_global( $data ){
	global $ACSC;
	//if ( ! acsc_is_active('woocommerce') ) return array();
	//if ( ! acsc_is_enabled('woo') ) return array();
	acsc_trc('    acsc_woocommerce_WP_global');


	
	// Get all WooCommerce options
	$woo = array(
		'woo_currency' 		=> get_woocommerce_currency(),
		//'woo_calc_taxes' 	=> get_option('woocommerce_calc_taxes'),

		'woo_allowed_countries' 		=> get_option('woocommerce_allowed_countries'),
		'woo_ship_to_countries' 		=> get_option('woocommerce_ship_to_countries'),
		'woo_specific_allowed_countries'=> get_option('woocommerce_specific_allowed_countries'),
		'woo_specific_ship_to_countries'=> get_option('woocommerce_specific_ship_to_countries'),
		'woo_all_except_countries'		=> get_option('woocommerce_all_except_countries'),
		
		'woo_weight_unit'				=> get_option('woocommerce_weight_unit'),
		'woo_dimension_unit'			=> get_option('woocommerce_dimension_unit'),
		
		'woo_store_city'				=> get_option('woocommerce_store_city'),
		'woo_store_postcode'			=> get_option('woocommerce_store_postcode'),
		'woo_default_country' 			=> get_option('woocommerce_default_country'),
		//'woo_base_location' 			=> get_option('woocommerce_base_location'),

		/*
		'woo_enable_reviews' 			=> get_option('woocommerce_enable_reviews'),
		'woo_manage_stock' 				=> get_option('woocommerce_manage_stock'),
		'woo_prices_include_tax' 		=> get_option('woocommerce_prices_include_tax'),
		'woo_permalinks' 				=> get_option('woocommerce_permalinks'),
		'wooc_placeholder_image' 		=> get_option('woocommerce_placeholder_image'),

		'woo_email_from_address' 		=> get_option('woocommerce_email_from_address'),
		'woo_email_from_name' 			=> get_option('woocommerce_email_from_name'),
		'woo_onboarding_profile' 		=> get_option('woocommerce_onboarding_profile'),

		'woo_shop_page_id' 				=> get_option('woocommerce_shop_page_id'),
		'woo_cart_page_id' 				=> get_option('woocommerce_cart_page_id'),
		'woo_checkout_page_id' 			=> get_option('woocommerce_checkout_page_id'),
		'woo_myaccount_page_id' 		=> get_option('woocommerce_myaccount_page_id'),
		'woo_edit_address_page_id' 		=> get_option('woocommerce_edit_address_page_id'),
		'woo_view_order_page_id' 		=> get_option('woocommerce_view_order_page_id'),
		'woo_terms_page_id' 			=> get_option('woocommerce_terms_page_id'),
		'woo_refund_returns_page_id' 	=> get_option('woocommerce_refund_returns_page_id'),
		*/
		
		
	);

	$data['woo_store_address'] = trim(
		get_option('woocommerce_store_address') . ' ' .
		get_option('woocommerce_store_address_2') );



	// allowed and ship to countries
	if ( $woo['woo_ship_to_countries'] == "" ){
		$woo['woo_ship_to_countries'] = $woo['woo_specific_allowed_countries'];
	
	} else {
		$woo['woo_ship_to_countries'] = $woo['woo_specific_ship_to_countries'];

	}
	if ( sizeof($woo['woo_ship_to_countries']) ){
		$countries = array();
		foreach ($woo['woo_ship_to_countries'] as $row){
			$countries[] = array(
				'@type'			 => 'DefinedRegion',
				'addressCountry' => array(
					'@type'	=> 'Country',
					'name' 	=> $row,
				),
				'addressCountry' => $row,
			);
		}
		$woo['woo_ship_to_countries'] = $countries;
	} else $woo['woo_ship_to_countries'] = array();
	





	$data = array_merge( $data, $woo );
	return $data;


}



// WP Product meta
// ------------------------------------------------------
// hook: parseWP ( $data ) - on wp/post-data.php
// modifies WP $data
function acsc_woocommerce_WP_meta( $data ){
	global $ACSC, $acsc_parseID, $post;
	if ( isset($post) && $post ) {
		if ( $post->post_type != 'product' ) return $data;
	} else {
		if ( ! isset($data['_ptype']) || $data['_ptype'] != 'product' )  return $data;
	}

	acsc_trc('    acsc_woocommerce_WP_meta');
	// HOOKS on parse WP
	// -- parses Woocommerce Product Meta Data
		
	// parse Woocommerce Product Meta Data
	// ------------------------------------------

	// Get Product Meta Labels
	//	$data['_dynamic_labels'] = acsc_woocommerce_WP_meta_labels();


	

	
	
	$from = "";
	$to   = "";
	if ( isset($data['_sale_price_dates_from']) )
		$from = $data['_sale_price_dates_from'];
	if ( isset($data['_sale_price_dates_to']) )
		$to   = $data['_sale_price_dates_to'];
	if ( $from && is_numeric( $from ) ) {
		$from = intval( $from );
		$from = date( "Y-m-d H:i:s", $from );
		$data['_sale_price_dates_from'] = $from;
	}
	if ( $to && is_numeric( $to ) ) {
		$to = intval( $to );
		$to = date( "Y-m-d H:i:s", $to );
		$data['_sale_price_dates_to'] = $to;
	}
	
	

	if ( $acsc_parseID ) {
		$prod = wc_get_product( $acsc_parseID );
		if ( $prod ) {
			
			if ( isset($data['all_images']) )
				$data['product_images'] = $data['all_images'];
			
//acsc_trc('    acsc_woocommerce get_attributes', $prod->get_attributes());
	
			// availability
			$avail = $prod->get_availability();
			$avail = $avail['class'];

			if ( $avail == 'in-stock' ) $avail = 'InStock';
			if ( $avail == 'out-of-stock' ) $avail = 'OutOfStock';
			if ( $avail == 'out-of-stock' ) $avail = 'OutOfStock';
			if ( $avail == 'available-on-backorder' )
				$avail = 'BackOrder';

			$data['availability'] = $avail;

			// Color
			$color = $prod->get_attribute( 'color' );
			$color = $color ? $color : $prod->get_attribute( 'pa_color' );
			if ( $color ){
				$data['color'] = explode(' | ', $color);
				//$color = implode(', ', $color);
			}

			// Material
			$material = $prod->get_attribute( 'material' );
			$material = $material ? $material : $prod->get_attribute( 'pa_material' );
			if ( $material ){
				$data['material'] = explode(' | ', $material);
			}


			//$data['A_____DBG1'] = $data['_product_attributes'];

			// Attributes
			$data['attributes'] = array();
			$labels = wc_get_attribute_taxonomy_labels();
			if ( is_array($data['_product_attributes']) ) {
				foreach($data['_product_attributes'] as $key => $row){
					if ( $key == 'color' || $key == 'pa_color' || 
						$key == 'material' || $key == 'pa_material' ) continue;

					$name = $key;
					if ( substr($key, 0, 3) == 'pa_' ) $name = substr($key, 3);
					$name = $labels[ $name ];
					$data['attributes'][] = array(
						'@type' 	=> 'PropertyValue',
						'name' 		=> $name ? $name : $key,
						'value' 	=> $prod->get_attribute( $key ),
					);
				}
			}
			


			// Units
			$dim = array(
				'm'  => 'MTR',
				'cm' => 'CMT',
				'mm' => 'MMT',
				'in' => 'INH',
				'yd' => 'YRD',
			);
			$wei = array(
				'kg'  => 'KGM',
				'g'   => 'GRM',
				'lbs' => 'LBR',
				'oz'  => 'ONZ',
			);
			$data['dimension_unit'] = get_option('woocommerce_dimension_unit');
			$data['weight_unit'] = get_option('woocommerce_weight_unit');
			$data['dimension_unit'] = $dim[ $data['dimension_unit'] ];
			$data['weight_unit'] = $wei[ $data['weight_unit'] ];

			$data['width'] = array(
				'@type' 	=> 'QuantitativeValue',
				'value' 	=> $prod->get_width(),
				'unitCode' 	=> $data['dimension_unit'],
			);
			$data['height'] = array(
				'@type' 	=> 'QuantitativeValue',
				'value' 	=> $prod->get_height(),
				'unitCode' 	=> $data['dimension_unit'],
			);
			$data['length'] = array(
				'@type' 	=> 'QuantitativeValue',
				'value' 	=> $prod->get_length(),
				'unitCode' 	=> $data['dimension_unit'],
			);
			$data['weight'] = array(
				'@type' 	=> 'QuantitativeValue',
				'value' 	=> $prod->get_weight(),
				'unitCode' 	=> $data['weight_unit'],
			);



		}
	}
	
	
	if ( $ACSC ) {
		//$data['ACSC data'] = $data; ???
	}
	
	
	
	//$data['woocommerce_google_merchant_center'] = get_option('woocommerce_google_merchant_center');
	//$data['woocommerce_facebook'] = get_option('woocommerce_facebook');
	//$data['woocommerce_google_analytics_integration'] = get_option('woocommerce_google_analytics_integration');


	return $data;
}

// WP Product Meta Labels
// ------------------------------------------------------
function acsc_woocommerce_WP_meta_labels( $data = array() ){
	if ( ! acsc_is_active('woocommerce') ) return array();
    if ( ! is_admin() && ! wp_doing_cron() ) return array(); // Front End


	acsc_trc('    acsc_woocommerce_WP_meta_labels');

	$meta = array(
        'TITLE-ROW-woometa'		=> 'WOOCOMMERCE PRODUCT META',
		'_sku'					=> 'sku',
		'_visibility'			=> 'visibility',
		'_stock'				=> 'stock',
		'_stock_status'			=> 'stock status',
		'_price'				=> 'price',
		'_regular_price'		=> 'regular price',
		'_sale_price'			=> 'sale price',
		'_sale_price_dates_from'=> 'sale price from',
		'_sale_price_dates_to'	=> 'sale price to',
		'_purchase_note'		=> 'purchase note',
		'_visibility'			=> 'visibility',
		'_product_attributes'	=> 'product attributes',
		'_featured'				=> 'featured',
		'_virtual'				=> 'virtual',
		'_downloadable'			=> 'downloadable',
		'_downloadable_files'	=> 'downloadable files',
		'_variation_description'=> 'variation description',
		'_wc_average_rating'	=> 'average rating',
		'_wc_review_count'		=> 'review count',
		'_wc_rating_count'		=> 'rating count',
		'_thumbnail_id'			=> 'thumbnail id',
		'_download_expiry'		=> 'download expiry',
		'_download_limit'		=> 'download limit',
		'_tax_status'			=> 'tax status',
		'_tax_class'			=> 'tax class',
		'_manage_stock'			=> 'manage stock',
		'_backorders'			=> 'backorders',
		'_sold_individually'	=> 'sold individually',
		'_product_image_gallery'=> 'product image gallery',
		'_upsell_ids'			=> 'upsell ids',
		'_crosssell_ids'		=> 'crosssell ids',
		'woocommerce_ship_to_countries' => 'ship to countries',
		'weight'				=> 'weight',
		'length'				=> 'length',
		'width'					=> 'width',
		'height'				=> 'height',
		'availability'			=> 'availability',
		'color'					=> 'color',
		'material'				=> 'material',
		'attributes'			=> 'product attributes',
		'product_images'		=> 'product images',

        'SPACE-ROW-woometa'     => '',
	);



	if ( ! isset($data['meta']) ) $data['meta'] = array();
	$data['meta'] = array_merge($data['meta'], $meta );


	return $data;


}





// TEMPLATE
// ------------------------------------------------------
// -- sets plugin dynamic values to Product template
// -- return product-0 to $ACSC['templates']
function acsc_woocommerce_template(){
	global $ACSC;
    if ( wp_doing_ajax() ) return false;


	acsc_trc('        acsc_woocommerce_template');
	
	// TEMPLATE product
	$tmpl = array();
	if ( isset($ACSC['templates']) &&
	     isset($ACSC['templates']['Product']) )
		$tmpl = $ACSC['templates']['Product'];
//_cos( $ACSC['templates'] );


	$tmpl['@id']  = '{post_id}';
	$tmpl['name'] = '{post_title}';
	$tmpl['description'] = '{post_excerpt}';
	$tmpl['url']  = '{page_url}';


	//$tmpl['model'] = '{model}';
	$tmpl['sku']  = '{_sku}';
	$tmpl['category']  = '{product_cat}';
	//$tmpl['url']  = '{_product_url}';
	//$tmpl['url']  = '{_product_url}';

 
	
	$tmpl['offers'][0] = array(
		"@type" 		=> 'Offer',
		"availability"	=> '{availability}',
		"price"			=> '{_price}',
		"priceCurrency"	=> '{woo_currency}',
		//"validFrom"		=> '{date_published}',
		"priceValidUntil" => '{_sale_price_dates_to}',
		"itemCondition" => 'NewCondition',
		"seller" 		=> array('@id' => '#busi-1'),
		"url"			=> '{page_url}',
	);

	$tmpl['offers'][0]["shippingDetails"] = array();
	$tmpl['offers'][0]["shippingDetails"][0] = array(
		'@type' => 'OfferShippingDetails',
		'shippingDestination' => '{woo_ship_to_countries}',
		'shippingRate' => array(
			'value'    => null,
			'currency' => '{woo_currency}',
		),
	);
	

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
	//$tmpl['image'] = '{content_images}';
	$tmpl['image'] = '{product_images}';
	$tmpl["video"] = "{all_videos}";
	
	
	$tmpl["width"]  = '{width}';
	$tmpl["height"] = '{height}';
	$tmpl["depth"]  = '{length}';
	$tmpl["weight"] = '{weight}';

	
	$tmpl["color"] = "{color}";
	$tmpl["material"] = "{material}";

	$tmpl["additionalProperty"] = "{attributes}";
	



	
	$tmpl["review"] = "{reviews}";
	$tmpl['aggregateRating'] = array(
		'ratingValue' => '{_wc_average_rating}',
		'ratingCount' => '{_wc_review_count}',
		'bestRating'  => 5,
		'worstRating' => 1,
	);


	$ACSC['templates']['product-0'] = $tmpl;
	
	return $tmpl;
}
function acsc_hook_woocommerce_schemaTemplates( $templates ){
	// HOOKS on acsc_schema_templates()
	// -- sets plugin dynamic values to Product template
	global $ACSC;
	// set plugin dynamic values to Product template
	acsc_woocommerce_template();
	
	return $ACSC['templates'];
}





?>