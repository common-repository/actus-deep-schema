<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      





// ≣≣≣≣ LearnPress
// ≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣
function acsc_learnpress( $mode="" ){
	global $ACSC;
	if ( ! isset($ACSC['options']) ) return false;
	if ( ! acsc_is_active('learnpress') ) return false;
	acsc_trc('--- Learnpress');

	unset( $ACSC['sys']['post_types']['lp_lesson'] );
	unset( $ACSC['sys']['post_types']['lp_question'] );
	unset( $ACSC['sys']['post_types']['lp_quiz'] );

	// TARGETING
	if ( acsc_is_enabled('learnpress') ) {
		
		// set Course schema as default for lp_course post type
		if ( ! isset($ACSC['options']['targets']['lp_course']) ||
			! $ACSC['options']['targets']['lp_course'] ) {
			$ACSC['options'] = acsc_opt();
			$ACSC['options']['targets']['lp_course'] = 'Course';
			update_option( "ACSC-options", $ACSC['options'], false );
		}
		
	}
	
	if ( ! $mode ) {
	
		// TEMPLATE
		acsc_learnpress_template();


	}
	
	// HOOKS
	acsc_learnpress_hooks( $mode );
	
	
}


// HOOKS
// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
function acsc_learnpress_hooks( $mode="" ){
	global $ACSC;
	
	if ( ! isset($ACSC['options'] ) ) return false;
	
	if (  isset($ACSC['options']['plugins']['learnpress_enable']) &&
		  $ACSC['options']['plugins']['learnpress_enable'] ) {
		


				
    	// WP Course Meta
		// hook: acsc-WP-meta ( $data ) - on wp/post-data.php
		// modifies WP $data
		// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		if ( ! $mode || $mode == 'before-wp' )
			add_filter( 'acsc-WP-meta', 'acsc_learnpress_WP_meta' );
		// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		if ( ! $mode || $mode == 'before-wp' )
			add_filter( 'acsc-WP-labels', 'acsc_learnpress_WP_meta_labels' );
		// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		
		


		// HOOKS on acsc_schema_templates()
		// -- sets plugin dynamic values to Course template
		// -- return lp_course-0 template
		// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		if ( ! $mode )
		acsc_hook('schemaTemplates', 'acsc_hook_learnpress_schemaTemplates');
		// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	
		

	}
		
	$text = '<h3><b>PLUGIN integrated</b> : LearnPress</h3>';
	$clss = 'note-plugin note-plugin-learnpress acsc-sc active';
	
	
	if ( !isset($ACSC['options']['plugins']['learnpress_enable']) ||
		 ! $ACSC['options']['plugins']['learnpress_enable'] ) {
		
		$text = '<h3><b>PLUGIN detected</b> : LearnPress</h3> <p>Enable connection with this plugin.</p>';
		$clss = 'note-plugin note-plugin-learnpress acsc-sc';
		
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
				'name'	=> 'plugins.learnpress_enable',
				'label'	=> 'enabled',
				'autosave' => true,
			),
		),
	));
	// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
		
	

	// options on settings
	// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	if ( ! $mode )
	acsc_hook('js_admin_settings', 'settings', array(
		array( 'type'	=> 'seperator' ),
		array(
			'type'	=> 'toggle',
			'name'	=> 'plugins.learnpress_enable',
			'label'	=> 'LearnPress',
			'note'	=> 'Enable connection with <b>LearnPress</b> plugin.<br>The Product schema will be assigned on every product page and will automatically get values from the product.<br>(refresh the page after saving options)',
		),
	));
	// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	
	
	
	// Filter Proccessed Schemas
	// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	if ( ! $mode ) {
	add_filter( 'acsc-FE-proccessed-schemas',
			    'acsc_learnpress_proccessed_schemas', 10, 1 );
	function acsc_learnpress_proccessed_schemas( $schemas ) {
		global $ACSC;
		
        /*
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
        */
		
		return $schemas;
	}
	}
	// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	

	/*

	// Add product info to schema form
	// Add pros & cons to schema form
	// if selected default is another schema type
	// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	if ( ! $mode )
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
	// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	
	*/
	
}




// parse WP Course meta for LearnnPress
// ------------------------------------------------------
// hook: acsc-WP-meta ( $data ) - on wp/post-data.php
// modifies WP $data
function acsc_learnpress_WP_meta( $data ){
	global $ACSC, $acsc_parseID;


	if ( ! acsc_is_active('learnpress') ) return false;
	global $post;
	if ( isset($post) && $post ) {
		if ( $post->post_type != 'lp_course' ) return $data;
	} else {
		if ( ! isset($data['_ptype']) || $data['_ptype'] != 'lp_course' )  return $data;
	}

	acsc_trc('    acsc_learnpress_WP_meta');


	// parse learnpress Data
	// ------------------------------------------
	$data['lp_currency'] = get_option('learn_press_currency');
    if ( ! $data['lp_currency'] ) $data['lp_currency'] = 'EUR';


	
	// Get Meta Labels
	//$data['_dynamic_labels'] = acsc_learnpress_WP_meta_labels();
	



	if ( $acsc_parseID && isset($data['_lp_course_author']) ) {

            
        $data['lp_level']           = ucfirst( $data['_lp_level'] );
        $data['lp_requirements']    = $data['_lp_requirements'];
        $data['lp_key_features']    = $data['_lp_key_features'];
        $data['lp_students']        = $data['_lp_students'];
        $data['lp_featured_review'] = $data['_lp_featured_review'];
        $data['lp_regular_price']   = $data['_lp_regular_price'];


        $data['lp_author'] = array('@id' => "#pers-wp-" . $data['_lp_course_author'] );
        $data['lp_repeat'] = explode(" ", $data['_lp_duration'])[0];

        if ( isset( $data['_lp_duration'] ) ) {
            $freq = explode(" ", $data['_lp_duration'])[1];
            if ( $freq == 'week' ) $freq = 'Weekly';
            if ( $freq == 'day' )  $freq = 'Daily';
            if ( $freq == 'hour' ) $freq = 'Hourly';
            $data['lp_frequency'] = $freq;
        }



            
        $data['lp_price_categ'] = 'Paid';
        if ( ! $data['lp_regular_price'] ) $data['lp_price_categ'] = 'Free';

        if ( $data['_lp_course_is_sale'] ) {
            $data['lp_sale_offer'] = array(
                "@type" 		=> 'Offer',
                "category"	    => 'Paid',
                "price"			=> $data['_lp_price'],
                "priceCurrency"	=> $data['lp_currency'],
                "priceValidUntil" => $data['_lp_sale_end'],
            );
        }


		
		$data['lp_faqs'] 				= $data['_lp_faqs'];
		$data['lp_target_audiences'] 	= $data['_lp_target_audiences'];
		$data['lp_external_link'] 		= $data['_lp_external_link_buy_course'];

	}
	



	return $data;
}



// Get Meta Labels
// ------------------------------------------------------
function acsc_learnpress_WP_meta_labels( $data = array() ){
	if ( ! acsc_is_active('learnpress') ) return array();
    if ( ! is_admin() && ! wp_doing_cron() ) return array(); // Front End


	acsc_trc('    acsc_learnpress_WP_meta');


	$meta = array(
		//'_wp_page_template',
		'TITLE-ROW-learnpress'        			=> 'LEARN PRESS',

		'lp_currency'			=> 'LP currency',
		'lp_author' 			=> 'Course Author',
		'lp_level' 				=> 'Course Level',
		'lp_featured_review' 	=> 'Featured Review',
		'lp_key_features' 		=> 'Key Features',
		'lp_requirements' 		=> 'Requirements',
		'lp_repeat' 			=> 'Course Repeat',
		'lp_frequency' 			=> 'Course Frequency',
		'lp_price_categ' 		=> 'Price Category',
		'lp_regular_price' 		=> 'Regular Price',
		'lp_sale_offer' 		=> 'Sale Offer',
		'lp_students' 			=> 'Students',
		'lp_faqs' 				=> 'Course FAQs',
		'lp_target_audiences' 	=> 'Target Audiences',
		'lp_external_link' 		=> 'External Link Buy Course',

		'SPACE-ROW-learnpress'        			=> '',

		//'_lp_course_result' => 'Course Result',
		///'_lp_passing_condition' => 'Passing Condition',
		//'_lp_price' => 'Price',
		//'_lp_duration'	=> 'Duration',

		//'_lp_max_students' => 'Max Students',
		//'_lp_retake_count' => 'Retake Count',
		//'_lp_featured' => 'Featured',
		//'_lp_has_finish' => 'Has Finish',
		//'_lp_sample_data' => 'Sample Data',
		//'_lp_info_extra_fast_query' => 'Info Extra Fast Query',
		//'_lp_block_expire_duration' => 'Block Expire Duration',
		//'_lp_block_finished' => 'Block Finished',
		//'_lp_allow_course_repurchase' => 'Allow Course Repurchase',
		//'_lp_course_repurchase_option' => 'Course Repurchase Option',
		//'_lp_sale_price' => 'Sale Price',
		//'_lp_sale_start' => 'Sale Start',
		//'_lp_sale_end' => 'Sale End',
		//'_lp_no_required_enroll' => 'No Required Enroll',
		//'_lp_course_is_sale' => 'Course Is Sale',
		
	);

 
	/*
    $tmp  = acsc_wp_posttype_meta('lp_course');
	$meta = array();
	foreach ($tmp as $key){
		$meta[$key] = $key;
	}
	*/
    
	
	if ( ! isset($data['meta']) ) $data['meta'] = array();
	$data['meta'] = array_merge($data['meta'], $meta );


	return $data;


}




// TEMPLATE
// ------------------------------------------------------
// -- sets plugin dynamic values to Course template
// -- return lp_course-0 to $ACSC['templates']
function acsc_learnpress_template(){
	global $ACSC;
    if ( wp_doing_ajax() ) return false;
	
	acsc_trc('        acsc_learnpress_template');

	// TEMPLATE product
	$tmpl = array();
	if ( isset($ACSC['templates']) &&
	     isset($ACSC['templates']['Course']) )
		$tmpl = $ACSC['templates']['Course'];

	$tmpl['@id']            = '{post_id}';
	$tmpl['name']           = '{post_title}';
	$tmpl['description']    = '{lp_featured_review}';
	$tmpl['url']            = '{page_url}';


	$tmpl['educationalLevel'] = '{lp_level}';
	$tmpl['coursePrerequisites'] = '{lp_requirements}';
	$tmpl['teaches'] = '{lp_key_features}';
	$tmpl['totalHistoricalEnrollment'] = '{lp_students}';
	$tmpl['hasCourseInstance'][0] = array(
        "courseMode"    => "Online",
        "instructor"    => array( "{lp_author}" ) ,
        "courseSchedule"=> array(
            "@type" => "Schedule",
            "repeatCount"     => "{lp_repeat}",
            "repeatFrequency" => "{lp_frequency}",
        ),
    );


	$tmpl['offers'][0] = array(
		"@type" 		=> 'Offer',
		"category"	    => '{lp_price_categ}',
		"price"			=> '{lp_regular_price}',
		"priceCurrency"	=> '{lp_currency}',
	);
	$tmpl['offers'][1] = '{lp_sale_offer}';

	
	$tmpl['inLanguage'] = array(
        '@type'	=> 'Language',
        'name'	=> '{locale}',
    );
	$tmpl['datePublished'] = '{date_published}';
	$tmpl['image'] = '{all_images}';
	$tmpl["video"] = "{all_videos}";
	

	/*
	$tmpl["review"] = "{reviews}";
	$tmpl['aggregateRating'] = array(
		'ratingValue' => '{_wc_average_rating}',
		'ratingCount' => '{_wc_review_count}',
		'bestRating'  => 5,
		'worstRating' => 1,
	);
    */


	$ACSC['templates']['lp_course-0'] = $tmpl;
	

	return $tmpl;
}
function acsc_hook_learnpress_schemaTemplates( $templates ){
	// HOOKS on acsc_schema_templates()
	// -- sets plugin dynamic values to Product template
		global $ACSC;
    
		// set plugin dynamic values to Product template
		acsc_learnpress_template();
		
		return $ACSC['templates'];
	}





?>