<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      



// ≣≣≣≣ SEO plugins
// ≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣
function acsc_seo_plugins( $mode="" ){
	global $ACSC;
	acsc_trc('--- SEO plugins');

	$plugin_name = basename(plugin_dir_path(dirname( __FILE__ , 2 )));


    /*

    //if ( ! is_admin() ) return false;
    if ( wp_doing_ajax() || wp_doing_cron() ) return false;

    // get current screen
    if ( isset($ACSC['sys']) && is_array($ACSC['sys']['post_types']) )
        $screens = array_keys( $ACSC['sys']['post_types'] );
    else $screens = array();


    // Page check
    // run only on plugin admin page or posts/pages (for metabox)
    $screen = get_current_screen()->id;
    $base = get_current_screen()->base;
    if ( $screen != "actus_page_$plugin_name" &&
         ! in_array($screen, $screens) &&
         $base != 'post' ) return false;


    */

    // set $ACSC['sys']['seo_plugins'] && $ACSC['sys']['seo_plugins_data']
    // ------------------------------------------------------

    
	
	// HOOKS
	acsc_seo_hooks( $mode );


}




// HOOKS
// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
function acsc_seo_hooks( $mode="" ){
	global $ACSC;


/*
    if ( ! isset($ACSC['sys']['seo_plugins']) || ! is_array($ACSC['sys']['seo_plugins']) ||
            sizeof($ACSC['sys']['seo_plugins']) == 0 )
        return false;
        */

    if ( ! isset($ACSC['options'] ) ) return false;


    
    // WP Dynamic Labels for SEO plugins
    // hook: acsc-WP-global-labels ( $data ) - on wp/system-data.php
    // modifies $ACSC['sys']['dynamic_labels']
    // ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
    if ( ! $mode || $mode == 'before-wp' )
        add_filter( 'acsc-WP-global-labels', 'acsc_seo_WP_global_labels' );
    // ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■


    
    // WP global SEO data
    // hook: acsc-WP-global ( $data ) - on wp/post-data.php
    // modifies WP $data
    // ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
    if ( ! $mode || $mode == 'before-wp' )
        add_filter( 'acsc-WP-global', 'acsc_seo_WP_global' );
    // ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
    

    
    // WP meta
    // hook: acsc-WP-meta ( $data ) - on wp/post-data.php
    // modifies WP $data
    // ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
    //if ( ! $mode || $mode == 'before-wp' )
       // add_filter( 'acsc-WP-meta', 'acsc_seo_WP_meta' );
    // ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
    



}


/*
foreach ( $seo_plugins as $plugin ){
    switch ( $plugin ){
        case 'rank-math':
            add_filter( 'rank_math/frontend/title', 'acsc_hook_seo_title', 10, 1 );
            add_filter( 'rank_math/frontend/description', 'acsc_hook_seo_description', 10, 1 );
            add_filter( 'rank_math/frontend/image', 'acsc_hook_seo_image', 10, 1 );
            add_filter( 'rank_math/frontend/org', 'acsc_hook_seo_org', 10, 1 );
            add_filter( 'rank_math/frontend/robots', 'acsc_hook_seo_robots', 10, 1 );
            add_filter( 'rank_math/frontend/canonical', 'acsc_hook_seo_canonical', 10, 1 );
            add_filter( 'rank_math/frontend/schema', 'acsc_hook_seo_schema', 10, 1 );

    }
}
*/





// sets dynamic vars for SEO plugins
// ------------------------------------------------------
// hook: acsc-WP-global-labels ( $data ) - on wp/system-data.php
// modifies $ACSC['sys']['dynamic_labels']
function acsc_seo_WP_global_labels( $data ){
	global $ACSC;

	acsc_trc('    acsc_seo_WP_global_labels');

    $data['seo'] = array();

    // Rank Math
	$data['seo'] = acsc_rankMath_variables( $data['seo'] );


	return $data;

}



// parse WP data for SEO plugins
// ------------------------------------------------------
// hook: acsc-WP-global-labels ( $data ) - on wp/system-data.php
// modifies $ACSC['sys']['dynamic_labels']
function acsc_seo_WP_global( $data ){
	global $ACSC;

	acsc_trc('    acsc_seo_WP_global');

    $seo_plugins = array();
	if ( ! isset($data) ) $data = array();

    // Rank Math
	$result = acsc_rankMath_data( $seo_plugins, $data );
	if ( isset($result) && is_array($result) && sizeof($result) ) {
		$seo_plugins = $result[0];
		$data = array_merge( $data, $result[1] );
	}



    $ACSC['sys']['seo_plugins'] = $seo_plugins;
    $ACSC['sys']['seo_plugins_data'] = $data;



//_con( $data );

    //$ACSC['sys']['dynamic_labels'] =
        //array_merge( $ACSC['sys']['dynamic_labels'], $vars );

	return $data;



    

    // Yoast SEO


    // All in One SEO Pack


    // SEOPress


    // The SEO Framework


    // Squirrly SEO


    // SmartCrawl SEO


    // Premium SEO Pack


    // SEO Ultimate


    // WP Meta SEO


    // WPSSO Core


    // WP Meta and Date Remover


    // WP SEO Structured Data Schema


    // Schema – All In One Schema Rich Snippets


    // Schema & Structured Data for WP & AMP


    // Schema App Structured Data


    // Schema Pro


    // Schema JSON-LD Markup


    // Schema & Structured Data for WP & AMP




    //$ACSC['sys']['seo_plugins'] = wp_get_active_and_valid_plugins();




}




// parse WP meta for SEO plugins
// ------------------------------------------------------
function acsc_seo_WP_meta( $data ){
	global $ACSC, $acsc_parseID;


	//if ( ! isset($data['_dynamic_labels']) ) $data['_dynamic_labels'] = array();

    //$data['_dynamic_labels'] =
        //array_merge( $data['_dynamic_labels'], acsc_woocommerce_dynamic_labels() );


    return $data;

}









function acsc_rankMath_variables( $data=array() ){
    if ( ! is_plugin_active('seo-by-rank-math/rank-math.php') ) return $data;

    $vars = array(
        'TITLE-ROW-seo'     => 'RANK MATH',
        'rm_website_name'   => 'website name',
        'rm_title'          => 'title',
        'rm_description'    => 'description',
        'rm_email'          => 'email',
        'rm_phones'         => 'phones',
        'rm_address'        => 'address',
        'rm_geo'            => 'geo coords',
        'rm_org_name'       => 'org name',
        'rm_org_type'       => 'org type',
        'rm_logo'           => 'org logo',
        'rm_hours'          => 'openning hours',
        'rm_image_id'       => 'image id',
        'rm_image'          => 'image',
        'rm_price_range'    => 'price range',
        'rm_price_range'    => 'price range',
        'SPACE-ROW-seo'     => '',
    );


    $data = array_merge( $data, $vars );
    return $data;
}


function acsc_rankMath_data( $seo_plugins, $seo_plugins_data ){
    if ( ! is_plugin_active('seo-by-rank-math/rank-math.php') ) return array();

    $vars = array_keys( acsc_rankMath_variables() );

    $seo_plugins[] = 'rank-math';

    $opt = get_option('rank-math-options-titles');
    $opt['author_archive_title'] = str_replace('%name%', '{author_name}', $opt['author_archive_title']);
    $sep = $opt['title_separator'];
    $sitename = $opt['website_name'];

    // replace placeholders in strings
    foreach( $opt as $i => $row){
        if ( is_string( $row ) ) {
            $opt[$i] = str_replace('%sep%', $sep, $row);
            $opt[$i] = str_replace('%sitename%', $sitename, $opt[$i]);
            $opt[$i] = str_replace('%page%', '{page}', $opt[$i]);
            $opt[$i] = str_replace('%date%', '{date}', $opt[$i]);
            $opt[$i] = str_replace('%sitedesc%', '{site_descr}', $opt[$i]);
        }
    }
    $data = array(
        'rm_website_name'     => $opt['website_name'],
        'rm_website_alt_name' => $opt['website_alternate_name'],

        'rm_title'         => $opt['homepage_title'],
        'rm_description'   => $opt['homepage_description'],
        'rm_image'         => $opt['open_graph_image'],
        'rm_image_id'      => $opt['open_graph_image_id'],
        'rm_org_name'      => $opt['knowledgegraph_name'],
        'rm_org_type'      => $opt['local_business_type'],
        'rm_logo'          => $opt['knowledgegraph_logo'],
        'rm_hours'         => $opt['opening_hours'],
        'rm_email'         => $opt['email'],
        'rm_phones'        => $opt['phone_numbers'],
        'rm_address'       => $opt['local_address'],
        'rm_price_range'   => $opt['price_range'],
        'rm_geo'           => $opt['geo'],
        
        /*
        'about_page'            => $opt['local_seo_about_page'],
        'contact_page'          => $opt['local_seo_contact_page'],

        'separator'             => $opt['title_separator'],
        'author_archive_title'  => $opt['author_archive_title'],
        'date_archive_title'    => $opt['date_archive_title'],

        'post_type_data'=> array(),
        */
    );
    






    // get wordpress post type names
    /*
    $ptypes = get_post_types( array( 'public' => true ), 'names', 'and' );

    if ( isset($ptypes) && is_array($ptypes) ) {
        foreach ( $ptypes as $ptype ){
            $schema ='';
            $article_type ='';
            $title ='';
            $description ='';
            if ( isset($opt['pt_'. $ptype .'_default_rich_snippet']) )
                $schema = ucfirst($opt['pt_'. $ptype .'_default_rich_snippet']);
            if ( isset($opt['pt_'. $ptype .'_default_article_type']) )
                $article_type = $opt['pt_'. $ptype .'_default_article_type'];
            if ( isset($opt['pt_'. $ptype .'_default_snippet_name']) )
                $title = $opt['pt_'. $ptype .'_default_snippet_name'];
            if ( isset($opt['pt_'. $ptype .'_default_snippet_desc']) )
                $description = $opt['pt_'. $ptype .'_default_snippet_desc'];
            //if ( $ptype == 'page' ) continue;
            $data['post_type_data'][$ptype] = array(
                'schema'        => $schema,
                'article_type'  => $article_type,
                'title'         => $title,
                'description'   => $description,
            );
        }
    }

    */



    return array( $seo_plugins, $data );

}





?>