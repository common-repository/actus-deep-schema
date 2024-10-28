<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      
acsc_trc('━━━━ wp/post-data.php');


global $acsc_tz;
$acsc_tz = get_option('gmt_offset');
$pro = '+';
if ( $acsc_tz < 0 ) { $pro = '-'; $acsc_tz = -$acsc_tz; }
$acsc_tz = $pro . str_pad($acsc_tz, 2, '0', STR_PAD_LEFT) . ':00';


// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
// gets WP post meta
// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
function acsc_get_WP_meta( $post="" ) {
	global $ACSC, $acsc_parseID;

	acsc_trc( "━━━━━━━━━━━━━ WP Meta - $acsc_parseID" );


	$data = array();

	// check if a post exists
	$acsc_parseID = 0;
	if ( is_numeric( $post ) ){
		$acsc_parseID = $post;
		$post = get_post( $post );
	}

	
	// get from $_POST if exists
	$data['_ptype'] = '';

	if ( wp_doing_ajax() ) {
		// Verify nonce
		$nonce = sanitize_key($_POST['nonce']);
		if ( ! wp_verify_nonce( $nonce, 'acsc_nonce' ) ) {
			//wp_send_json_error( 'Invalid nonce.' );
			die( __( 'Invalid nonce.', 'actus-deep-schema' ) );
		}

		if ( isset( $_POST['acsc_pid'] ) ) {
			$post_id = sanitize_text_field( wp_unslash( $_POST['acsc_pid'] ) );
			$post = get_post( $post_id );
			$data['_scope'] = sanitize_text_field( wp_unslash( $_POST['acsc_scope'] ) );
			$data['_ptype'] = sanitize_text_field( wp_unslash( $_POST['acsc_ptype'] ) );
			$data['_item']  = map_deep( wp_unslash( $_POST['acsc_item'] ), 'sanitize_text_field' );
			$mode = 'ajax';
		}
	}
	
	if ( $post ) $acsc_parseID = $post->ID;
	if ( ! $acsc_parseID && isset($post_id) ) $acsc_parseID = $post_id;
	

		// external plugins
	include_once(__DIR__.'/helpers.php');
	include_once(__DIR__.'/../acsc-plugins.php');
	//acsc_external_plugins1('before-wp');


	// ≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣
	if ( $post ) {
		$acsc_parseID = $post->ID;
		$wp_data = acsc_wp_parse_post_data( $post );
		
		$data = array_merge($data, $wp_data);


		$data['_ptype'] = $post->post_type;
		$data['_post_type'] = $post->post_type;
	}
	// ≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣
	
	$data = acsc_wp_parse_archive_data( $data );

	// HOOK: acsc-WP-meta
    // ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	$data = apply_filters( 'acsc-WP-meta', $data );
    // ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	
	// Get Post & Archive data Labels
	$data['_dynamic_labels_post'] = acsc_wp_post_labels();
	$data['_dynamic_labels_archive'] = acsc_wp_archive_labels();



	$trace_data = "";
	//if ( $acsc_parseID ) $trace_data = $data;
	acsc_trc( '━━━━━━━━━━━━━ WP Meta - end', $trace_data );



	if ( isset( $mode ) && $mode == 'ajax' ) {
		echo wp_json_encode( $data );
	    wp_die();
	
	} else {
		return $data;
	}
	
	


}
add_action('wp_ajax_acsc_get_WP_meta', 'acsc_get_WP_meta');
add_action('wp_ajax_nopriv_acsc_get_WP_meta', 'acsc_get_WP_meta' );
// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■


function acsc_wp_parse_post_data( $post ){
	global $ACSC, $parsed_media, $acsc_tz;

	acsc_trc('    acsc_wp_parse_post_data');

	// content
	$content = acsc_wp_content( $post );

	// excerpt
	$excerpt = $post->post_excerpt;
	if ( ! $excerpt || trim($excerpt) == '' ) {
		$excerpt = wp_trim_words( strip_shortcodes( $post->post_content ), 50 );
	}
	$excerpt = acsc_wp_content( $excerpt );

	// word count
	$word_count = acsc_wp_word_count( $content );
	
	

	// ▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒
	$data = array(
		'id'	  	  => $post->ID,

		'page_id'	  => '#page-' . $post->ID,
		'page_title'  => $post->post_title,
		'page_excerpt'=> $excerpt,
		'page_url'    => urldecode( home_url(sanitize_text_field( wp_unslash($_SERVER['REQUEST_URI']))) ),
		'parent'      => $post->post_parent,
		'parent_name' => get_the_title( $post->post_parent ),
		'template'    => get_page_template_slug( $post->ID ),


		'archive'     => acsc_wp_parse_archive_urls(),

		'post_id'	  => '#post-' . $post->ID,
		'post_title'  => $post->post_title,
		'headline'    => $post->post_title,
		'post_excerpt'=> $excerpt,
		'post_content'=> $content,
		'post_tag'    => "",
		'post_type'   => $post->post_type,
		'post_url'    => get_permalink($post->ID),
		'word_count'  => $word_count,

		'date_published'	=> $post->post_date . $acsc_tz,
		'date_modified' 	=> $post->post_modified . $acsc_tz,

	);

	
	
	// Get media
	// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
	if ( is_admin() ){
		$data = acsc_wp_parse_post_media( $post, $data );
		// will be retrieved in acsc_dynamic_values()
		// for each item in Front End
	}

	
	// comments
	// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
	$data['comment_status'] = comments_open($post->ID);
	if ( $data['comment_status'] )
		$data['comment_status'] = '1';
	$data['comment_count'] =
		get_comments_number($post->ID);
	$data['comments'] =
		get_comments(array('post_id' => $post->ID));
	
	
	$data['reviews'] = array();
	if ( sizeof($data['comments']) )
	foreach ($data['comments'] as $i => $row){
		$rating = get_comment_meta( $row->comment_ID,
								    'rating', true );
		if ( $row->comment_type == 'review' &&
		     $row->comment_approved == '1' ){
			$review = array(
				//"meta"		=> $meta,  ???
				"@type"		=> "Review",
				"reviewBody"	=> $row->comment_content,
				"datePublished" => substr(str_replace(" ", "T", $row->comment_date), 0, -3) . $acsc_tz,
			);
			if ( $row->comment_author )
				$review['author'] = array(
					"@type"	=> "Person",
					"name"	=> $row->comment_author,
				);
			if ( $rating )
				$review['reviewRating'] = array(
					"@type"			=> "Rating",
					"bestRating"	=> 5,
					"worstRating"	=> 1,
					"ratingValue"	=> $rating,
				);
			
			$data['reviews'][] = $review;
		}
	}


	$post_type = get_post_type_object( get_post_type() );

	if ( $post_type ) {
		//$data['post_type'] = $post_type->labels->singular_name;
	}


	if ( is_admin() )
		$data['page_url'] = urldecode( get_permalink($post->ID) );



	
	
	
	// Author
	// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
	$data['page_author'] = acsc_wp_author( $post );
	$data['author'] = $data['page_author'];
	$data['author_ID'] = $post->post_author;
	$data['author_ID'] = '#pers-wp-' . $post->post_author;
	$data['author_name'] = $data['page_author']['name'];
	$data['author_description'] = get_the_author_meta(
			'description', $post->post_author );
	$data['author_avatar'] = get_avatar_url( $post->post_author,
								array('default' => '',
									  'force_default' => true) );



	// TAXONOMIES
	// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
	$data['category'] = single_cat_title('', false);
	$taxonomies = acsc_wp_posttype_taxonomies( $post->post_type );

	foreach ($taxonomies as $key => $row){
		$terms = get_the_terms( $post, $key );

		$data[$key] = '';
		$data[$key.'_array'] = array();
		$data[$key.'_ids'] = array();
		if ( is_array($terms) ){
			foreach ($terms as $idx => $term){
				if ( $idx > 0 ) $data[$key] .= ', ';
				$data[$key] .= $term->name;
				$data[$key.'_array'][] = $term->name;
				$data[$key.'_ids'][] = $term->term_id;
			}
		}
	}
	$data['genre']    = $data['category'];
	$data['keywords'] = $data['post_tag'];
	$data['page_tag'] = $data['post_tag'];

	


	// Search Results
	// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
	if ( is_search() ) {
		$data['search'] = get_search_query();
	}


	// POST TYPE META
	// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
	$meta = acsc_wp_posttype_meta( $post->post_type );
	$data['meta'] = array();
	foreach ($meta as $idx => $key){
		//if ( get_post_meta($post->ID, $key, true) ) {
			$data[$key] =
				get_post_meta($post->ID, $key, true);
			$data['meta'][$key] =
				get_post_meta($post->ID, $key, true);
		//}
	}

/*
$dbg = get_option('ACSC_debug', true);
$dbg = array(
	'$meta'	=> $meta,
);
update_option('ACSC_debug', $dbg);
*/	
	
	return $data;


}
function acsc_wp_post_labels($labels=array()){
    if ( ! is_admin() && ! wp_doing_cron() ) return array(); // Front End

	acsc_trc('    acsc_wp_post_labels');
	
	$labels = array_merge($labels, array(
		'post_id'		=> 'Post ID',
		'post_title'	=> 'Post Title',
		'post_excerpt'	=> 'Post Excerpt',
		'post_url'		=> 'Post Url',
		'post_type'		=> 'Post Type',
		'author'		=> 'Author',
		'author_id'		=> 'Author ID',
		'author_name'	=> 'Author Name',
		'author_avatar'	=> 'Author Avatar',
		'author_description'=> 'Author Description',
		'content_images'=> 'Post Content Images',
		'archive'		=> 'Archive',
		'all_images'	=> 'Post Images',
		'all_videos'	=> 'Post Videos',
		'videos'		=> 'Post Videos',
		'audio'			=> 'Post Audio',
		'reviews'		=> 'Reviews',
		'featured_image'=> 'Featured Image',
		'featured_url'	=> 'Featured Image URL',
		'featured_alt'	=> 'Featured Image Alt',
		'featured_thumb'=> 'Featured Image Thumbnail',
		'featured_caption'=>'Featured Image Caption',
		'date_published'=> 'Date Published',
		'date_modified'	=> 'Date Modified',
		'word_count'	=> 'Word Count',
	));

	return $labels;
}
// Archive Data
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
function acsc_wp_parse_archive_data( $data = array() ){
	if ( ! is_archive() ) return $data;

	global $ACSC;

	$queried_object = get_queried_object();
	if ( isset($ACSC) &&
		 isset($ACSC['sys']) &&
		 isset($ACSC['sys']['taxonomies']) && isset($queried_object)  ) {

		if (  isset($queried_object->taxonomy) &&
				isset($ACSC['sys']['taxonomies'][$queried_object->taxonomy]) ) {

			$data['taxonomy'] 	= $ACSC['sys']['taxonomies'][$queried_object->taxonomy]->label;
			$data['taxonomy_singular'] 	= $ACSC['sys']['taxonomies'][$queried_object->taxonomy]->labels->singular_name;
			$data['taxonomy_slug'] 	= $queried_object->taxonomy;
			$data['term_id'] 	= $queried_object->term_id;
			$data['term_name'] 	= $queried_object->name;

			if ( ! $data['taxonomy_slug'] ) {
				$data['taxonomy_slug'] = $queried_object->query_var;
				$data['term_id'] 	= 'archive';
				$data['term_name'] 	= $queried_object->label;
			}

			$term = get_term($data['term_id'],
				$queried_object->taxonomy);
			$data['term_description'] = $term->description;
			$data['term_slug'] 	= $term->slug;
		} else {

			if ( isset( $queried_object->labels ) ) {
				$data['taxonomy_singular'] = $queried_object->labels->name;
				if ( isset( $queried_object->labels->singular_name ) )
					$data['taxonomy_singular'] = $queried_object->labels->singular_name;
					$data['term_name'] = ' ';
					$data['term_description'] = 'All ' . $queried_object->labels->name;
			}

		}

	}

	return $data;

}
function acsc_wp_archive_labels($labels=array()){

	$labels = array_merge($labels, array(
		'taxonomy'	=> 'Taxonomy Name',
		'taxonomy_singular'	=> 'Taxonomy Name (singular)',
		'taxonomy_slug'	=> 'Taxonomy Slug',
		'term_id'	=> 'Term ID',
		'term_name'	=> 'Term Name',
		'term_description' => 'Term Description',
		'term_slug'	=> 'Term Slug',
		'search'	=> 'Search Query',
	));
	
	return $labels;
}

function acsc_wp_parse_post_media( $post, $data=array() ){

	if ( is_archive() ) return $data;

	acsc_trc('    acsc_wp_parse_post_media');

	
	if ( isset( $post ) && is_numeric( $post) )
		$post = get_post( $post );
	
	if ( ! isset( $post ) ) return $data;
	
	// featured image
	// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
	$featured = acsc_wp_featured( $post );
	$name = $featured['alt'];
	if ( ! $name ) {
		$name = explode("/", $featured['url']);
		$name = $name[ sizeof($name)-1 ];
		$name = explode('.', $name)[0];
	}
	$featured['alt'] = $name;
	$data['featured_url'] 		= $featured['url'];
	$data['featured_alt'] 		= $name;
	$data['featured_thumb'] 	= $featured['thumb'];
	$data['featured_thumbnail'] = $featured['thumb'];
	$data['featured_caption'] 	= $featured['caption'];
	$data['featured_w'] 		= $featured['w'];
	$data['featured_h'] 		= $featured['h'];
	$data['featured_image'] 	= array(
		'_t_contentUrl'	=> $featured['thumb'] ? $featured['thumb'] : $featured['url'],
		"@type" 		=> "ImageObject",
		'name'			=> $name,
		'caption'		=> $featured['caption'],
		'url'			=> $featured['url'],
		'contentUrl'	=> $featured['url'],
		'thumbnail'		=> $featured['thumb'],
		'width'			=> $featured['w'],
		'height'		=> $featured['h'],
	);
	


	// IMAGES
	// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
	$data['images'] = acsc_wp_content_images( $post );


	$data['image'] 			= $data['images'];
	$data['content_images'] = $data['images'];
	$data['all_images'] 	= $data['images'];
	
	
	acsc_trc('    acsc_wp_parse_post_media images: ' . sizeof($data['image']) );
	
	if ( ! isset($data['featured_image']) || ! is_array($data['featured_image']) )
		$data['featured_image'] = array();

	if ( ! isset($data['all_images']) || ! is_array($data['all_images']) )
		$data['all_images'] = array();
	
	if ( $data['featured_image']['url'] )
		array_unshift( $data['all_images'], $data['featured_image'] );


	// VIDEOS
	// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
	$data['video'] = acsc_wp_content_videos( $post );
	$data['all_videos'] = $data['video'];

	

	// AUDIO
	// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
	$data['audio'] = acsc_wp_content_audio( $post );


	
//_cos( $post->ID . ' ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅ media' );
//_cos( acsc_saved_media( $post->ID ) );
	
	return $data;

	
}
function acsc_wp_parse_archive_urls(){
	//$queried_object = get_queried_object();
    $post_urls = array();

	if ( ! is_archive() ) return $post_urls;

	acsc_trc('    acsc_wp_parse_archive_urls');
	
	if ( have_posts() ) :
    while ( have_posts() ) : the_post();
        $post_url = get_permalink();
        array_push( $post_urls, $post_url );
    endwhile;
	endif;
	
	if ( sizeof( $post_urls ) == 0 ) return [];
	return $post_urls;
}
function acsc_wp_content( $post ){
	if ( ! $post ) return "";
	
	if ( is_string($post) )
		$content = $post;
	else
		$content = $post->post_content;
	$content = apply_filters('the_content', $content);
	$content = strip_shortcodes( $content );
	$content = strip_tags( $content );
	$content = str_replace("\n\n\n\n\n\n\n\n", " ", $content );
	$content = str_replace("\n\n\n\n\n\n", " ", $content );
	$content = str_replace("\n\n\n\n", " ", $content );
	$content = str_replace("\n\n", " ", $content );
	$content = str_replace("\n", " ", $content );
	$content = trim( $content );
	
	return $content;
}
function acsc_wp_word_count($str='ERR', $f=0) {
	if (empty($str) || $str == 'ERR') {
		$r = 0;
	} else {
		$as = explode(" ", $str);

		
		switch ($f) {
		case 0:
			$r = count($as);
			break;
		case 1:
		case 2:
			$r = array_values($as);
			break;
		default:
			$r = "The format can only contain 0, 1 and 2!";
			break;
		}

	}

	return $r;
}
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
function acsc_saved_media( $post_id ){
//delete_post_meta($post_id, 'ACSC-media');


	$saved = get_post_meta($post_id, 'ACSC-media', true);
	if ( ! $saved ) $saved = array();
	
	return $saved;
	
}
function acsc_save_media( $post_id, $data, $mode ){
	acsc_trc('*** acsc_save_media');
	
	$i = 0;
	foreach ($data as $key => $row){
		if ( ! $row || ! sizeof($row) )
			array_splice($data, $i, 1);
		$i++;
	}
	
	if ( sizeof($data) ) {
		update_post_meta($post_id, 'ACSC-media', $data);
//_cos([ 'save '.$mode, $post_id, $data ]);
		
	} else
		delete_post_meta($post_id, 'ACSC-media');
	
}
global $acsc_checked_media;
$acsc_checked_media = array();
function acsc_check_media( $post_id, $type, $urls ){
	global $acsc_checked_media;
	if ( ! isset($acsc_checked_media[$type]) ) $acsc_checked_media[$type] = array();
	if ( in_array($post_id, $acsc_checked_media[$type]) ) return array();
	$acsc_checked_media[$type][] = $post_id;

	$saved = acsc_saved_media( $post_id );
	$existing = array();
	$new_data = array();
	$refresh  = array();
	
//_bck('check_media - ' . $post_id);


	// ■■■■■■■■■■■■■■■■■■■■ cleanup saved media
	if ( isset($saved[ $type ]) ) {
		foreach ($saved[ $type ] as $row) {
			$var = 'contentUrl';

			if ( ! isset($row[ $var ]) || in_array($row[ $var ], $existing) ) continue;
			// ┅┅┅┅┅┅┅ skip if it's in $existing (remove duplicates)


			$existing[] = $row[ $var ];


			if ( in_array($row[ $var ], $urls) ) {
			// ┅┅┅┅┅┅┅ saved url exists in current data, add it
				if ( $row && sizeof($row) ) $new_data[] = $row;
			}

		}
	}

	
	// ■■■■■■■■■■■■■■■■■■■■ check dates && duplicates
	
	//if ( $type == 'video' ) {
	// ┅┅┅┅┅┅┅
	if ( isset($urls) && is_array($urls) ){
		foreach ($urls as $key => $url) {
			$id = $key;
			if ( ! acsc_is_youtube( $url ) ) {
				$url = explode("?", $url)[0];
			} else $id = 'yt-' . $id;

			// check if url exists in saved media
			if ( ! $new_data ) $new_data = array();
			$exists = array_search( $url,
					array_column($new_data, 'contentUrl'));
			
			$now  = date('Y-m-d');
			if ( $exists && isset($new_data[$exists]) &&
			   	 isset($new_data[$exists]['_date']) )
				$date = $new_data[$exists]['_date'];
			if ( ! isset($date) || ! $date ) $date = '2023-01-01';
			
			$date1 = strtotime( $now );
			$date2 = strtotime( $date );
			$days  = ($date1 - $date2)/60/60/24;
			//$days  = 3;

			// if saved data are older or does not exist
			if ( $exists === false || $days > 2 ) {

				$refresh[] = $url;

				if ( $exists !== false ) {
					$new_data[ $exists ]['_refresh'] = true;
				} else {
					$new_data[] = array(
						'contentUrl' => $url,
						'_refresh' => true,
						'_key' => $id,
					);
				}
			}


		}
	}
	//}
	
	
	return $new_data;
	
}
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
function acsc_wp_featured( $post ){
	if ( ! isset( $post ) ) return array();
	
	$image_id = get_post_thumbnail_id( $post->ID );
	$full = wp_get_attachment_image_src( $image_id, 'full' );
	$featured = array();
	
	$featured['url'] = $full;
	if ( is_array( $full ) ) $featured['url'] = $full[0];
	
	$featured['alt'] =
		get_post_meta($image_id, '_wp_attachment_image_alt', TRUE);
	
	$featured['caption'] =
		wp_get_attachment_caption( $image_id );
	
	
	$opt = get_option( 'ACSC-options' );
	if ( ! $opt['thumb_size'] )
		$opt['thumb_size'] = 'thumbnail';
	$featured['thumb'] =
		wp_get_attachment_image_src( $image_id,
									 $opt['thumb_size'] );
	if ( is_array($featured['thumb']) )
		$featured['thumb'] = $featured['thumb'][0];
	
	$featured['w'] = 0;
	$featured['h'] = 0;
	if ( is_array( $full ) ) {
		$featured['w'] = $full[1];
		$featured['h'] = $full[2];
	}

	
	return $featured;
}
function acsc_wp_content_images( $post, $feat = false ){
if ( ! isset( $post ) ) return array();
	
	if ( $post->post_type == 'post' ) $feat = true;

	// get image urls from content
	$doc = new DOMDocument();
	$content = $post->post_content;
	$content = apply_filters('the_content', $content);


	$content = mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8');
	$urls = array();
	$imgs = array();
	if ( $content ) {
		libxml_use_internal_errors(true);
		$doc->loadHTML( $content );
		$attimages = $doc->getElementsByTagName( 'img' );
		foreach ($attimages as $i => $img) {
			$key = $img->getAttribute("class");
			if ( $key )   $key = intval( str_replace('wp-image-','', $key) );
			if ( ! $key ) $key = attachment_url_to_postid( $img->getAttribute("src") );
			if ( ! $key ) $key = 'no-' . $i;
			
			$src = $img->getAttribute("src");
			$ext = explode('.', $src);
			$ext = $ext[ sizeof($ext)-1 ];
			if ( in_array($ext, array('jpg','jpeg','png','webp','bmp', 'gif')) ) {
				$urls[$key] = $src;
				$imgs[$key] = $img;
			}
		}
	}
	
	// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	$urls_filtered = apply_filters( 'acsc-WP-image-urls', $urls, $imgs, $post->ID );
	if ( is_array($urls_filtered) && is_array($urls_filtered) && sizeof($urls_filtered) > 1 ) {
		if ( isset($urls_filtered[0]) ) $urls = $urls_filtered[0];
		if ( isset($urls_filtered[1]) ) $imgs = $urls_filtered[1];
	}
	// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■

	

	$images = array();
	

	// check and cleanup saved media
	$image_data = acsc_check_media( $post->ID, 'images', $urls );
	
	


	foreach ($image_data as $i => $image){
	// ┅┅┅┅┅┅┅ loop through media
		
		// skip if doesn't need to be refreshed
		if ( ! $image['_refresh'] ) continue;
		unset( $image['_refresh'] );
		//$image = array_values($image);
		if ( isset($image['_key']) )
			$info = acsc_wp_extract_images( $imgs[ $image['_key'] ], $image['_key'], "img", $post->ID );

		// update data
		if ( isset($info) && $info && sizeof($info) )
		$image_data[ $i ] = $info;
		
	}
	
	//$attimages = get_attached_media('image', $post->ID);
	//$attimages = get_all_attached_images($post->ID);
	/*
	foreach ($attimages as $img) {
		$image_url = wp_get_attachment_image_src( $img->ID, 'full' )[0];
		$thumbnail = wp_get_attachment_image_src( $img->ID, 'thumbnail' )[0];
		$image_alt = get_post_meta($img->ID, '_wp_attachment_image_alt', TRUE);
		$image_caption = wp_get_attachment_caption( $img->ID );
		if ( ! $image_alt )		$image_alt = '';
		if ( ! $image_caption ) $image_caption = '';
		
		//if ( $feat['url'] != $image_url ) {
		if ( ! in_array($image_url, $existing) ) {
			$images[] = array(
				"@type" => "ImageObject",
				"url"	=> $image_url,
				"name"  => $image_alt,
				"caption" => $image_caption,
				"thumbnail"  => $thumbnail,
				"width" => "",
				"height" => "",
			);
			$existing[] = $image_url;
		}

	}
	*/

	
	
	
	// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	$image_data = apply_filters( 'acsc-content-images', $image_data, $post->ID );
	// ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
	

	

	// Cleanup duplicates ( including thumbnails )
	$tmp = $image_data;
	foreach ( $image_data as $i => $item) {
		$url = $item['_t_contentUrl'];
		$filtered = array_filter($tmp, function($arr) use ($url) {
			return $arr['_t_contentUrl'] == $url;
		});
		$removed = 0;
		if ( sizeof($filtered) > 1) {
			$i = 0;
			foreach( $filtered as $idx => $row ){
				//if ( $i == sizeof($filtered)-1 && $removed == $i ) break;
				if ( !isset($row['_t_contentUrl']) ||
				     ($row['_t_contentUrl'] == $row['contentUrl'] ) ){
					unset( $tmp[$idx] );
					$tmp = array_values($tmp);
					$removed++;
				}
				$i++;
			}
		}
		if ( ! $removed ) {
			$i = 0;
			foreach( $filtered as $idx => $row ){
				if ( $i < sizeof($filtered)-1 ) {
					unset( $tmp[$idx] );
					$tmp = array_values($tmp);
				}
				$i++;
			}
		}
	}
	$image_data = $tmp;

	
	// Load saved media
	$saved = acsc_saved_media( $post->ID );
	if ( ! isset($saved['images']) ) $saved['images'] = array();


	// order $image_data by contentUrl

	usort($image_data, function($a, $b) {
		return strcmp($a['contentUrl'], $b['contentUrl']);
	});
	usort($saved['images'], function($a, $b) {
		return strcmp($a['contentUrl'], $b['contentUrl']);
	});


	


	if ( $image_data != $saved['images'] ) {
	// ┅┅┅┅┅┅┅ if new data exist
		// save new media info
		$saved['images'] = $image_data;
		acsc_save_media( $post->ID, $saved, 'images');
			
	}

	// Return the array of audio files
	return $saved['images'];
	
}
function acsc_wp_extract_images( $img, $key, $type="img", $post_id=0 ){
	if ( ! $img ) return array();



	$opt = get_option( 'ACSC-options' );
	if ( ! $opt['thumb_size'] ) $opt['thumb_size'] = 'thumbnail';
	
	$title 	 	= '';
	$image_w 	= null;
	$image_h 	= null;
	$created 	= null;
	$filesize  	= null;

	
	if ( is_object( $img ) ) {
	// ┅┅┅┅┅┅┅┅┅┅ img object
		
		$image_url = $img->getAttribute("src");
		$image_alt = $img->getAttribute("alt");
		$title 	   = $img->getAttribute("title");
		$image_w   = intval($img->getAttribute("width"));
		$image_h   = intval($img->getAttribute("height"));
		$image_caption = $img->getAttribute("title");
		$thumb 	   = '';
		$created   = null;
		$filesize  = null;
		
		if ( $post_id ) {
			$saved = acsc_saved_media( $post_id );

			if ( is_array($saved['images']) ) {
				$exists = array_search( $image_url,
						array_column($saved['images'], 'contentUrl'));
				if ( ! $exists )
					$exists = array_search( $image_url,
							array_column($saved['images'], '_t_contentUrl'));
			}


			if ( $exists !== false ) {
				return $saved['images'][$exists];
			}
		}

		
		acsc_trc('    acsc_wp_extract_images (obj)');
		
		// get img meta
		$meta = wp_get_attachment_metadata( $key );
		if ( $meta ) {
			$filesize = $meta['filesize'];
			$image_w  = $meta['width'];
			$image_h  = $meta['height'];
			$keywords = $meta['image_meta']['keywords'];
			$created  = $meta['image_meta']['created_timestamp'];
			$credit   = $meta['image_meta']['credit'];
			$title    = $meta['image_meta']['title'];
			$sizes    = $meta['sizes'];
			

			$thumb = wp_get_attachment_image_src( $key, $opt['thumb_size'] )[0];
			
			$image_alt = get_post_meta($key, '_wp_attachment_image_alt', TRUE);
			$image_caption = wp_get_attachment_caption( $key );
			if ( ! $image_caption ) $image_caption = $meta['image_meta']['caption'];
			
			
		} else {
			
			// width & height
			$current_domain = parse_url( 
				sanitize_text_field( wp_unslash($_SERVER['REQUEST_URI']) ), PHP_URL_HOST);
			$image_domain =
				parse_url($image_url, PHP_URL_HOST);
			if ( $image_domain == $current_domain ) {
				$size = getimagesize( $image_url );
				$image_w = $size[0];
				$image_h = $size[1];
			}
			

		}
		
		if ( ! $image_alt ) 	$image_alt = '';
		if ( ! $image_caption ) $image_caption = '';
	

	} else {
	// ┅┅┅┅┅┅┅┅┅┅ ID

	
		acsc_trc('    acsc_wp_extract_images (id)');
		
		$ID = $img;
		$image_url = wp_get_attachment_image_src( $ID, 'full' )[0];
		$thumb = wp_get_attachment_image_src( $ID, $opt['thumb_size'] )[0];
		$image_caption =
			wp_get_attachment_caption( $ID );
		$image_alt = get_post_meta($ID, '_wp_attachment_image_alt', TRUE);

	}

acsc_trc('>>>> acsc_wp_extract_images 4');

	if ( ! $image_alt ) $image_alt = $title;
	if ( ! $image_alt ) {
		$image_alt = explode('/', $image_url);
		$image_alt = $image_alt[ sizeof($image_alt)-1 ];
		$image_alt = explode('.', $image_alt);
		array_pop( $image_alt );
		$image_alt = implode('.', $image_alt);
		$image_alt = str_replace('_', ' ', $image_alt);
	}

	$result = array(
		"@type" => "ImageObject",
		"_t_contentUrl" => $thumb ? $thumb : $image_url,
		"url"		=> $image_url,
		"contentUrl"=> $image_url,
		"name"  	=> $image_alt,
		"caption" 	=> $image_caption,
		"_title" 	=> $title,
		"width" 	=> $image_w,
		"height" 	=> $image_h, 
		"_key"	 	=> $key, 
		"_created"  => $created, 
		"_filesize" => $filesize, 
	);
	if ( $thumb != '-' )
		$result['thumbnail'] = $thumb;

	return $result;
}
function acsc_wp_content_audio( $post ){
	if ( ! isset( $post ) ) return array();
	

	$audios = get_attached_media( 'audio', $post->ID );
	$urls = array();
	foreach ($audios as $key => $audio){
		$urls[$key] = $audio->guid;
	}
	preg_match_all('/<audio.*?src=[\"
	\']([^\"\']*)[\"\'].*?>/i', $post->post_content, $matches);
	
	foreach ($matches[1] as $key => $url){
		if ( ! in_array( $url, $urls ) )
			$urls['no-'.$key] = $url;
	}
	
	// check and cleanup saved media
	$audio_data =
		acsc_check_media( $post->ID, 'audio', $urls );
	
	foreach ($audio_data as $i => $audio){
	// ┅┅┅┅┅┅┅ loop through media

		// skip if doesn't need to be refreshed
		if ( ! isset($audio['_refresh']) || ! $audio['_refresh'] ) continue;
		unset( $audio['_refresh'] );
		
		$info = acsc_wp_extract_audio_info( $audio, $audios );
		
		
		// update data
		$audio_data[ $i ] = $info;
		
	}
	
	
	// Load saved media
	$saved = acsc_saved_media( $post->ID );
	if ( ! isset($saved['audio']) ) $saved['audio'] = array();
	
	if ( $audio_data != $saved['audio'] ) {
	// ┅┅┅┅┅┅┅ if new data exist
		
		// save new media info
		$saved['audio'] = $audio_data;
		acsc_save_media( $post->ID, $saved, 'audio');
			
	}

	// Return the array of audio files
	return $saved['audio'];
	
	
	
}
function acsc_wp_extract_audio_info( $audio, $audios ){
	global $acsc_tz;
	acsc_trc('    acsc_wp_extract_audio_info');
	
	$url = $audio['contentUrl'];
	
	$audio['@type']	= "AudioObject";
	$audio['_date'] = date('Y-m-d');
	
	
	if ( $audio['_key'] && is_numeric( $audio['_key'] ) ) {
	// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅ WITH KEY
		
		
		$data = $audios[ $audio['_key'] ];
	
		// title - desctiption - date
		$audio['name'] = $data->post_title;
		$content = $data->post_content;
		$content = preg_replace( "/\\\+'/", "'", $content );
		$content = preg_replace( '/\\\+"/', '"', $content );
		$content = preg_replace( '/\\\+/', '\\', $content );
		if ( $content )
			$audio['description'] = $content;

		$audio['uploadDate'] =
			str_replace(' ', 'T', $data->post_date) . $acsc_tz;
		
		$meta = wp_get_attachment_metadata( $audio['_key'] );
		
		
		// artist - composer - album
		if ( $meta['composer'] ||
			 $meta['artist'] ||
			 $meta['album'] ) {
			$audio['byArtist'] = $meta['artist'];
			$audio['inAlbum']  = $meta['album'];
			$audio['creator']  = $meta['composer'];
		}
		
		
		$audio['encodingFormat'] = $meta['mime_type'];
		$audio['contentSize']	 = $meta['filesize'];
		$audio['bitrate'] 	  	 = $meta['bitrate'];
		$audio['duration'] =
			acsc_seconds_to_ISO( $meta['length'] );

		
		
	} else {
	// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅ NO KEY
		
		
		// Require the getID3 library
		require_once(includes_url() . '/ID3/getid3.php');
		$domain = "http";
		if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') 
			$domain = "https"; 
		$domain .= "://";
		$domain .= sanitize_text_field( wp_unslash($_SERVER['HTTP_HOST']) ) . '/';

		$file_info = wp_check_filetype( $url );
		$localurl = str_replace($domain, '', $url);

		if (strpos($file_info['type'], 'audio/') !== false) {
			$getID3 = new getID3();
			$info = $getID3->analyze( ABSPATH . $localurl );
			
			if ( ! isset($info['description']) )
				$info['description'] = "";
			
			$audio['name'] 			 = $info['filename'];
			$audio['contentUrl'] 	 = $url;
			$audio['encodingFormat'] = $file_info['type'];
			$audio['description']	 = $info['description'];
			$audio['contentSize']	 = $info['filesize'];
			$audio['bitrate']  = intval($info['bitrate']);
			$audio['duration'] =
				acsc_seconds_to_ISO( $info['playtime_seconds'] );
			
			
			$tags = $info['tags'];
			if ( isset( $tags['id3v1'] ) ){
				$id3v1 = $tags['id3v1'];
				
				if ( $id3v1['title'] && sizeof($id3v1['title']) )
					$audio['name'] = $id3v1['title'][0];
				
				if ( $id3v1['album'] )
					$audio['inAlbum'] = $id3v1['album'];
				if ( $id3v1['composer'] )
					$audio['creator'] = $id3v1['composer'];
				if ( $id3v1['band'] )
					$audio['byArtist'] = $id3v1['band'];
				if ( $id3v1['artist'] )
					$audio['byArtist'] = $id3v1['artist'];
				
				if ( $id3v1['genre'] )
					$audio['genre'] = $id3v1['genre'];
			}
			if ( isset( $tags['id3v2'] ) ){
				$id3v2 = $tags['id3v2'];
				
				if ( $id3v2['title'] && sizeof($id3v2['title']) )
					$audio['name'] = $id3v2['title'][0];
				
				if ( isset($id3v2['album']) )
					$audio['inAlbum'] = $id3v2['album'];
				if ( isset($id3v2['composer']) )
					$audio['creator'] = $id3v2['composer'];
				if ( isset($id3v2['band']) )
					$audio['byArtist'] = $id3v2['band'];
				if ( isset($id3v2['artist']) )
					$audio['byArtist'] = $id3v2['artist'];
				if ( isset($id3v2['genre']) )
					$audio['genre'] = $id3v2['genre'];
				
				if ( isset($id3v2['date']) )
					$audio['uploadDate'] =
					$id3v2['year'][0].'-'.
					substr($id3v2['date'][0], 2, 2).'-'.
					substr($id3v2['date'][0], 0, 2).'T12:00:00';
			}
			
			
		}
		
		
	}
	

	if ( $audio['creator'] ||
		 $audio['byArtist'] ||
		 $audio['inAlbum'] )
		$audio['@type'] = array("AudioObject", "MusicRecording");


	if ( ! $audio['name'] ) {
		$audio['name'] = explode('/', $url);
		$audio['name'] =
			$audio['name'][ sizeof($audio['name']) - 1];
		$audio['name'] = explode('.', $audio['name'])[0];
	}

	
	return $audio;
	
}
function acsc_wp_extract_audio_info1( $audio_info ){
	

	foreach ($audio_info as $i => $row){
		if ( is_string( $row ))
			$audio_info[$i] = sanitize_text_field($row);
		if ( is_array( $row )) {
			foreach ($row as $ii => $row2){
				if ( is_string( $row2 ))
					$audio_info[$i][$ii] =
						sanitize_text_field($row2);

				if ( is_array( $row2 )) {
					foreach ($row2 as $iii => $row3){
						if ( is_string( $row3 ))
							$audio_info[$i][$ii][$iii] =
							sanitize_text_field($row3);
						if ( is_array( $row3 )) {




		foreach ($row3 as $iiii => $row4){
			if ( is_string( $row4 ))
				$audio_info[$i][$ii][$iii][$iiii] =
				sanitize_text_field($row4);
			if ( is_array( $row4 )) {

				$audio_info[$i][$ii][$iii][$iiii] = array();
			}

		}



						}

					}
				}

			}
		}
	}
	
	$id3v2 = $audio_info['id3v2']['comments'];
	
	//$audio_info['id3']    = $id3v2;
	/*
	if ( is_array( $id3v2 ) ) {
		$audio_info['genre']  = implode(', ', $id3v2['genre']);
		$audio_info['title']  = implode(', ', $id3v2['title']);
		$audio_info['album']  = implode(', ', $id3v2['album']);
		$audio_info['year']   = implode(', ', $id3v2['year']);
		$audio_info['artist'] = implode(', ', $id3v2['artist']);
		$audio_info['band']   = implode(', ', $id3v2['band']);
		$audio_info['bpm']    = implode(', ', $id3v2['bpm']);
		$audio_info['composer'] =
			implode(', ', $id3v2['composer']);
		$audio_info['encoded_by'] =
			implode(', ', $id3v2['encoded_by']);
	}
	*/
		
	
	$txt = '';
	if ( $audio_info['album'] ) {
		$txt .= 'Album : ' . $audio_info['album'];
		if ( $audio_info['year'] )
			$txt .= ' (' . $audio_info['year'] . ')';
		if ( $txt ) $txt .= "\n";
	}
	
	if ( $audio_info['artist'] )
		$txt .= 'Artist : ' . $audio_info['artist'] . "\n";
	if ( $audio_info['band'] )
		$txt .= 'Band : ' . $audio_info['band'] . "\n";
	if ( $audio_info['composer'] )
		$txt .= 'Composer : ' . $audio_info['composer'] ."\n";
	if ( $audio_info['bpm'] )
		$txt .= 'BPM : ' . $audio_info['bpm'] ."\n";
	if ( $audio_info['encoded_by'] )
		$txt .= 'Encoded by : ' . $audio_info['encoded_by'] . "\n";
	
	
	
	
	$audio_info['description'] = $txt;
		
	
	return $audio_info;
}
function acsc_wp_content_videos( $post ){
	global $ACSC, $acsc_parseID;
	
	acsc_trc('    acsc_wp_content_videos' );

	$post_id = $acsc_parseID;
	$content = "";
	if ( $post ) {
		$post_id = $post->ID;
		$content = $post->post_content;
	}
	//if ( isset( $ACSC['wp'] ) ) {
		//$post_id = $ACSC['wp']['id'];
		//$content = $ACSC['wp']['post_content'];
	//}
	
	
	$result = array();
	
	// get youtube urls from content
	$urls = acsc_get_youtube_urls( $content );
	// ▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒
	$urls =
		apply_filters( 'acsc-youtube-urls', $urls, $post_id );
	// ▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒
	$urls = array_unique( $urls );
	
	
	
	
	$videos = get_attached_media( 'video', $post_id );
	foreach ($videos as $key => $video){
		$urls[$key] = $video->guid;
	}
	
	// get embeded videos
	//$videos = acsc_wp_extract_videos( $content );
	//$urls = array_merge($urls,
						//array_column($videos, 'contentUrl'));

		
	// check and cleanup saved media
	$video_data = acsc_check_media( $post_id, 'video', $urls );
	
acsc_trc('    acsc_wp_content_videos video_data: ' . sizeof($video_data) );
	foreach ($video_data as $i => $video){
	// ┅┅┅┅┅┅┅ loop through media
		
		// skip if doesn't need to be refreshed
		if ( ! isset($video['_refresh']) || ! $video['_refresh'] ) continue;
		unset( $video['_refresh'] );
		
		
		
		$info = array();
		$url = $video['contentUrl'];
		
		
		// get youtube ID
		$youtube_id = acsc_is_youtube( $url );
		
		if ( ! $youtube_id ) {
			
			$youtube_id = explode('v=', $url)[1];
			$youtube_id = explode('&', $youtube_id)[0];
		}
		
		if ( $youtube_id ) {
		// ┅┅┅┅┅┅┅ if it's youtube
			
			// get youtube info
			$info = acsc_get_youtube_info( $url );
			$info = acsc_youtube_chapters( $info, $youtube_id );
			
			// set video object
			$info["@type"] = "VideoObject";
			$info['_date'] = date('Y-m-d');
			//$info['_date'] = "2023-11-12";
			//$info["url"] = $url;
			
		} else {
		// ┅┅┅┅┅┅┅ if it's youtube embeded video
			
			// get embeded info
			$info = acsc_wp_extract_video_info( $video, $videos );
			
		}
	

		// update data
		$video_data[ $i ] = $info;
		
	}
	
	
	// Load saved media
	$saved = acsc_saved_media( $post_id );
	if ( ! isset($saved['video']) ) $saved['video'] = array();
	
	if ( $video_data != $saved['video'] ) {
	// ┅┅┅┅┅┅┅ if new data exist
		
		// save new media info
		$saved['video'] = $video_data;
		acsc_save_media( $post_id, $saved, 'video');
			
	}

	// Return the array of video files
	return $saved['video'];
	
}
function acsc_wp_extract_videos( $html ){
	$video_info = array();
	
	acsc_trc('    acsc_wp_extract_videos');
	
	
	$html =
		mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
	if ( ! $html ) return $video_info;
	
	$html = apply_filters( 'the_content', $html );
	//$embeds = get_media_embedded_in_content( $html );
	
	
	$doc = new DOMDocument();
	$doc->loadHTML( $html );
	$videos = $doc->getElementsByTagName('video');

	$urls = array();
	foreach ($videos as $video) {
		
		// Get the attributes of the video element
		$url 		= $video->getAttribute('src');
		$title 		= $video->getAttribute('title');
		$alt 		= $video->getAttribute('alt');
		$duration 	= $video->getAttribute('duration');
		$poster 	= $video->getAttribute('poster');
		
		if ( ! $title ) {
			$title = $video->getAttribute('id');
		}
		if ( ! $url && $video->childNodes )
			$url = $video->childNodes->item(0)->getAttribute('src');
		if ( ! $url && $video->childNodes )
			$url = $video->childNodes->item(1)->getAttribute('src');
			
		
		if ( ! $title ) $title = $alt; 
		if ( ! $title ) {
			$title = explode('/', $url);
			$title = $title[ sizeof($title)-1 ];
		}

		// skip if already exists
		$url = explode("?", $url)[0];
		if ( in_array($url, $urls) ) continue;
		$urls[] = $url;
		
		
		// Add the information to the array as an associative array
		$info = array(
			'@type'   	 => 'VideoObject',
			'contentUrl' => $url,
			'name' 		 => $title ? $title : $alt,
		);
		if ( $poster ) 	 $info['thumbnailUrl'] = $poster;
		if ( $duration ) $info['duration'] = $duration;
		if ( $alt ) 	 $info['description'] = $alt;
		$info['_date'] = date('Y-m-d');
		//$info['_date'] = "2023-11-12";
			
		
		$video_info[] = $info;
	}

	return $video_info;
}
function acsc_wp_extract_video_info( $video, $videos ){
	global $acsc_tz;
	acsc_trc('    acsc_wp_extract_video_info');
	
	$url = $video['contentUrl'];
	
	
	if ( $video['_key'] ) {
		$data = $videos[ $video['_key'] ];
	
		// title - desctiption - date
		$video['name'] = $data->post_title;
		$content = $data->post_content;
		$content = preg_replace( "/\\\+'/", "'", $content );
		$content = preg_replace( '/\\\+"/', '"', $content );
		$content = preg_replace( '/\\\+/', '\\', $content );
		if ( $content )
			$video['description'] = $content;
		
		$video['uploadDate'] =
			str_replace(' ', 'T', $data->post_date) . $acsc_tz;
		
		
		// get youtube ID
		$youtube_id = acsc_is_youtube( $url );
		
		if ( ! $youtube_id ) {
			
			$youtube_id = explode('v=', $url)[1];
			$youtube_id = explode('&', $youtube_id)[0];
		}
		
		if ( $youtube_id ) {
		// ┅┅┅┅┅┅┅ if it's youtube
			
			// get youtube info
			$video = acsc_get_youtube_info( $url );
			$video = acsc_youtube_chapters( $video, $youtube_id );
			
			
		}
		
		
		if ( $video['_key'] ) {
		// ┅┅┅┅┅┅┅ if it's embeded video
			
			// get video meta
			$meta = wp_get_attachment_metadata( $video['_key'] );
			
			// get embeded info
			$info = array_search( $url,
					array_column( $videos, 'contentUrl'));
			$info = $videos[ $info ];
			$video['encodingFormat'] = $meta['mime_type'];
			$video['contentSize']	 = $meta['filesize'];
			$video['_dataformat']	 = $meta['dataformat'];
			$video['_width']	 	 = $meta['width'];
			$video['_height']	 	 = $meta['height'];
			$video['duration'] =
				acsc_seconds_to_ISO( $meta['length'] );
			//$video['_created'] = $meta['created_timestamp'];
			
		}

		
	}
	$video['@type']	= "VideoObject";
	$video['_date'] = date('Y-m-d');
	//$info['_date'] = "2023-11-12";
	
	
	return $video;
}
function acsc_wp_breadcrumb() {
    $delimiter = ' &raquo; '; // delimiter between crumbs
    $delimiter = ' > '; // delimiter between crumbs
    $home = 'Home'; // text for the 'Home' link
    $showCurrent = 1; // 1 - show current post/page title in breadcrumbs, 0 - don't show
    $before = '<span class="current">'; // tag before the current crumb
    $after = '</span>'; // tag after the current crumb

    global $post;
	
	
    $homeLink = get_bloginfo('url');
	
	$bcrumb[] = array(
		'name' => $home,
		'item' => $homeLink
	);
		
	// =============
	// CATEGORY
	// =============
	if (is_category()) {
		
		$thisCat = get_category(get_query_var('cat'), false);
		
		if ($thisCat->parent != 0) {

			$categs = acsc_wp_categ_breadcrumb( $thisCat->parent, 'category' );
			foreach($categs as $categ){
				$bcrumb[] = $categ;
			}

		}
		$slug = '';
		$bcrumb[] = array(
			'name' => 'Archive by category ' . single_cat_title('', false),
			'item'  => $homeLink . '/' . $thisCat->slug
		);
		
		
	// =============
	// SEARCH
	// =============
	} elseif (is_search()) {
		
		$bcrumb[] = 'Search results for \"' . get_search_query() . '\"';
		
	// =============
	// DATES
	// =============
	} elseif (is_day()) {
		
			$bcrumb[] = array(
				'name' => get_the_time('Y'),
				'item'  => get_year_link(get_the_time('m'))
			);
			$bcrumb[] = array(
				'name' => get_the_time('F'),
				'item'  => get_month_link(get_the_time('Y'), get_the_time('m'))
			);
			$bcrumb[] = array(
				'name' => get_the_time('d'),
				'item'  => get_day_link(get_the_time('Y'), get_the_time('m'), get_the_time('d'))
			);
			
        } elseif (is_month()) {

			$bcrumb[] = array(
				'name' => get_the_time('Y'),
				'item'  => get_year_link(get_the_time('m'))
			);
			$bcrumb[] = array(
				'name' => get_the_time('F'),
				'item'  => get_month_link(get_the_time('Y'), get_the_time('m'))
			);
			
        } elseif (is_year()) {
		
			$bcrumb[] = array(
				'name' => get_the_time('Y'),
				'item'  => get_year_link(get_the_time('m'))
			);
				
        } elseif (is_single() && !is_attachment()) {
		
			// =============
			// POST TYPE
			// =============
            if (get_post_type() != 'post') {
				
                $post_type = get_post_type_object( get_post_type() );
                $slug = $post_type->rewrite;
				
				$bcrumb[] = array(
					'name' => $post_type->labels->singular_name,
					'item'  => $homeLink . '/' . $slug['slug']
				);
				$bcrumb[] = array(
					'name' => get_the_title(),
					'item'  => get_permalink()
				);
				
				
            } else {
				
				// =============
				// POST
				// =============
				$cat = get_the_category();
				$cat = $cat[0];
				$categs = acsc_wp_categ_breadcrumb( $cat, 'category' );

				$bcrumb = array_merge($bcrumb, $categs);
				$bcrumb[] = array(
					'name' => get_the_title(),
					'item'  => get_permalink()
				);
				
            }
        } elseif (!is_single() && !is_page() && get_post_type() != 'post' && !is_404()) {
		
            $post_type = 
				get_post_type_object(get_post_type());
		
			if ( $post_type ){
				$bcrumb[] = array(
					'name' => $post_type->labels->singular_name,
					'item'  => get_permalink()
				);
			}
		
        } elseif (is_attachment()) {
		
            $parent = get_post($post->post_parent);
            $cat = get_the_category($parent->ID);
            $cat = $cat[0];
         
			$bcrumb = array_merge(
				$bcrumb,
				acsc_wp_categ_breadcrumb( $cat, 'category' )
			);
			$bcrumb[] = array(
				'name' => $parent->post_title,
				'item'  => get_permalink($parent)
			);
			$bcrumb[] = array(
				'name' => get_the_title(),
				'item'  => get_permalink()
			);
		
        } elseif (is_page() && !$post->post_parent) {
			// =============
			// PAGE
			// =============
			$bcrumb[] = array(
				'name' => get_the_title(),
				'item'  => get_permalink()
			);

        } elseif (is_page() && $post->post_parent) {
			// =============
			// PAGE with PARENTS
			// =============
		
            $parent_id  = $post->post_parent;
			$parents = array();
            while ($parent_id) {
                $page = get_page($parent_id);
				
				$parents[] = array(
					'name' => get_the_title($page->ID),
					'item'  => get_permalink($page->ID)
				);
				
                $parent_id  = $page->post_parent;
            }
			$parents = array_reverse( $parents );
			$bcrumb = array_merge($bcrumb, $parents);
		
			$bcrumb[] = array(
				'name' => get_the_title(),
				'item'  => get_permalink()
			);

		
        } elseif (is_tag()) {
		
			$bcrumb[] = array(
				'name' => 'Posts tagged "' . single_tag_title('', false),
				'item'  => get_permalink()
			);
		
            
        } elseif (is_author()) {
		
            global $acsc_author;
            $userdata = get_userdata($acsc_author);
			$bcrumb[] = array(
				'name' => 'Articles posted by ' . $userdata->display_name,
				'item'  => get_permalink()
			);
		
        } elseif (is_404()) {
		
			$bcrumb[] = array(
				'name' => 'Error 404',
				'item'  => get_permalink()
			);
		
        }
	
		$idx = 1;
		$items = array();
		foreach($bcrumb as $key => $row){
			if ( is_array($row) && $row['item'] ) {
				$items[ $key ] = $row;
				$items[ $key ]['@type'] = "ListItem";
				$items[ $key ]['position'] = $idx;
				$idx++;
			}
			if ( ! is_array($row) || ! $row['name'] ) {
				$items[ $key ]['name'] = '-';
			}
		}
	
		if ( sizeof($items) ) {
			$idx = intval( sizeof($items)-1 );
			$items[ $idx ]['item'] = null;
		}
		$bcrumb = array(
			//"@context" => "https://schema.org",
			"@type"	=> "BreadcrumbList",
			"itemListElement" => $items
		);
	
	
	
	
	
		return $bcrumb;
   
}
function acsc_wp_categ_breadcrumb( $term_id, $taxonomy, $args = array() ) {
    $list = '';
	$result = array();
	
	if ( ! $term_id ) return $result;
	
	
    $term = get_term( $term_id, $taxonomy );
 
    if ( is_wp_error( $term ) ) { return $term; }
    if ( ! $term ) { return $list; }
 
    $term_id = $term->term_id;
 
    $defaults = array(
        'format'    => 'name',
        'separator' => '/',
        'link'      => true,
        'inclusive' => true,
    );
 
    $args = wp_parse_args( $args, $defaults );
 
    foreach ( array( 'link', 'inclusive' ) as $bool ) {
        $args[ $bool ] = wp_validate_boolean( $args[ $bool ] );
    }
 
    $parents = get_ancestors( $term_id, $taxonomy, 'taxonomy' );
 
    if ( $args['inclusive'] ) {
        array_unshift( $parents, $term_id );
    }
 
    foreach ( array_reverse( $parents ) as $term_id ) {
        $parent = get_term( $term_id, $taxonomy );
        $name   = ( 'slug' === $args['format'] ) ? $parent->slug : $parent->name;
 
		$list .= $name . $args['separator'];
		$result[] = array(
			'name' => $name,
			'item' => esc_url( get_term_link( $parent->term_id, $taxonomy ) )
		);			

       
    }
 
    return $result;
}
function acsc_wp_author( $post ){
	
	$url = get_author_posts_url(
		get_the_author_meta( 'ID', $post->post_author));
	if ( ! $url )
		$url = get_the_author_meta('url', $post->post_author );
		
	$acsc_author = array(
		"@type" 	=> "Person",
		"@id" 		=> "#pers-wp-" . $post->ID,
		"name"  	=> get_the_author_meta('display_name', $post->post_author ),
		"email" 	=> get_the_author_meta('user_email', $post->post_author ),
		"url"   	=> $url,
		"_wpUser" 	=> $post->post_author,
	);
	
	$author_schema =
		get_option( "ACSC-pers-wp-" . $post->post_author );
	if ( $author_schema ) {
		unset( $author_schema['_wp_user'] );
		unset( $author_schema['__type'] );
		if ( $author_schema ) {
			if ( isset( $author_schema["address"] ) &&
				is_array( $author_schema["address"] ) &&
				sizeof( $author_schema["address"] ) == 1 &&
				! $author_schema["address"][0] )
				unset( $author_schema["address"] );
			
			foreach ( $author_schema as $key => $row ) {
				if ( ! $row )
					unset( $author_schema[ $key ] );
				if ( is_array( $row ) ){
					if ( sizeof($row) == 0 ){
						unset( $item[ $key ] );
					} else {
						foreach($row as $key2 => $row2){
							if ( ! $row2 ) unset( $row[ $key2 ] );
						}
						$author_schema[ $key ] = $row;
					}
				}
			}
			
			$acsc_author = $author_schema;

			
		}

	}
	
	return $acsc_author;
}
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
function acsc_get_youtube_urls( $text ){
	
	
	$text = str_replace("\r", ' ', $text);
	$text = str_replace("\n", ' ', $text);
	$text = str_replace('\"', ' ', $text);
	$text = str_replace('"', ' ', $text);
	$text = str_replace('&nbsp;', ' ', $text);
	
	$result = array();
	$words = explode(" ", $text);
	foreach ($words as $word) {
		if ( acsc_is_youtube( $word ) ) {
			$result[] = $word;
		}
	}
	
	return $result;
}
function acsc_get_youtube_info( $url ){
	global $ACSC;
	$video_id = acsc_is_youtube( $url );
	
	acsc_trc('    acsc_get_youtube_info --- '.$url);

	$v_categories = array(
		2  => 'Cars & Vehicles',
		1  => 'Film & Animation',
		10 => 'Music',
		15 => 'Pets & Animals',
		17 => 'Sports',
		18 => 'Short Movies',
		19 => 'Travel & Events',
		20 => 'Gaming',
		21 => 'Videoblogging',
		22 => 'People & Blogs',
		23 => 'Comedy',
		24 => 'Entertainment',
		25 => 'News & Politics',
		26 => 'Howto & Style',
		27 => 'Education',
		28 => 'Science & Technology',
		29 => 'Nonprofits & Activism',
		30 => 'Movies',
		31 => 'Anime/Animation',
		32 => 'Action/Adventure',
		33 => 'Classics',
		34 => 'Comedy',
		35 => 'Documentary',
		36 => 'Drama',
		37 => 'Family',
		38 => 'Foreign',
		39 => 'Horror',
		40 => 'Sci=>Fi/Fantasy',
		41 => 'Thriller',
		42 => 'Shorts',
		43 => 'Shows',
		44 => 'Trailers'
	);

	if ( ! $ACSC['options'] )
		$ACSC['options'] = get_option( 'ACSC-options' );
	
	// API key
	$api_key = $ACSC['options']['youtube_api_key'];
	
	if ( ! $api_key ){
		return acsc_get_youtube_info_noapi( $video_id );
	}
	
	// The API endpoint for getting video information
	$api_url = "https://www.googleapis.com/youtube/v3/videos?id=$video_id&key=$api_key&part=snippet,contentDetails";

	
	$body = wp_remote_retrieve_body( wp_remote_get( $api_url ) );
	
	// Decode the JSON response into an associative array
	$data = json_decode($body, true);
	//$data = $response['body'];

	
	// Check if the data array has an items key
	if (isset($data["items"])) {
		// Get the first item from the items array (should be only one)
		$item = $data["items"][0];

		// Get the snippet and contentDetails keys from the item array
		$snippet = $item["snippet"];
		$contentDetails = $item["contentDetails"];
		// Get the title, description and thumbnail url from the snippet array
		$title = $snippet["title"];
		$description = $snippet["description"];
		$thumbnail_url = isset($snippet["thumbnails"]["standard"]["url"]) ? 
		($snippet["thumbnails"]["standard"]["url"]) : 
		($snippet["thumbnails"]["default"]["url"]);

		
		$acsc_author = $snippet["channelTitle"];
		
		
		$url 		 = urldecode( $url );
		$title 		 = sanitize_text_field( $title );
		$description = sanitize_text_field( $description );
		$acsc_author 	 = sanitize_text_field( $acsc_author );


		
		$result2 = acsc_get_youtube_chapters( $video_id );
		
		
		$category = $v_categories[ $snippet['categoryId'] ];
		$views = $result2['info']['views'];
		$views = str_replace('.','',$views);
		$views = str_replace(',','',$views);
		$views = intval( $views );
		//$acsc_author = array( $result['info']['owner'] );
		
		// Create an associative array with all information about video
		$result_array=array(
			"contentUrl"	=> $url,
			"name"			=> $title,
			"description" 	=> $description,
			"genre" 		=> $category,
			"views" 		=> $views,
			"author" 		=> array( $acsc_author ),
			"thumbnailUrl"  => $thumbnail_url,
			"duration"		=> $contentDetails['duration'],
			"videoQuality"	=> $contentDetails['definition'],
			"uploadDate"	=> substr($snippet['publishedAt'], 0, -1),
			"inLanguage"		=> array(
				'@type'	=> 'Language',
				'name' =>
				$snippet['defaultLanguage'] ?? 
				$snippet['defaultAudioLanguage'] ?? '' ),
			//"tags"		=> $snippet['tags'],
			//"item"		=> $snippet["thumbnails"]
			//"live"		=> $snippet['liveBroadcastContent'],,
		); 

		return $result_array;

	} else {
		
		return array();

	}
}
function acsc_get_youtube_info_noapi( $video_id ){
	global $ACSC;
	
	
acsc_trc('    acsc_get_youtube_info_noapi', $video_id);
	
	// The API endpoint for getting video information
	$api_url = "https://noembed.com/embed?url=https://www.youtube.com/watch?v=$video_id";

	$body = wp_remote_retrieve_body( wp_remote_get( $api_url ) );

	$data = json_decode($body, true);
	
	
	$url   = urldecode( $data['url'] );
	$title = sanitize_text_field( $data['title'] );
	
	$result = acsc_get_youtube_chapters( $video_id );

	
	$views = $result['info']['views'];
	$views = str_replace('.','',$views);
	$views = str_replace(',','',$views);
	$views = intval( $views );
	$result_array['views'] = $views;
	//$result_array['chapters'] = $result['chapters'];

	
	
	$result_array = array(
		"contentUrl"	=> $url,
		"name"			=> $title,
		"thumbnailUrl"  => $data['thumbnail_url'],
		"description"   => $result['info']['description'],
		"author"   		=> array( $result['info']['owner'] ),
		"views"   		=> $views,
		"uploadDate"	=> $result['info']['uploadDate'] .
						   'T12:00:00+00:00',
		//"chapters"   	=> $result['chapters'],
	);
	
	if ( isset( $result['chapters'] ) &&
		 is_array( $result['chapters'] ) &&
		 sizeof( $result['chapters'] ) ) {
		
		$result_array['hasPart'] = $result['chapters'];
	}
	
	
	return $result_array;

 
	
}
function acsc_youtube_chapters( $info, $video_id="" ){
	
	acsc_trc('    acsc_youtube_chapters');

	if ( isset( $info['hasPart'] ) ) {
		$result = array();
		$chapters = $info['hasPart'];
		$info['hasPart'] = array();
		foreach ($chapters as $i => $chapter){
			$end = 0;
			if ( isset($info['duration']) ) $end = $info['duration'];
			$end = acsc_parse_duration( $end, 'seconds' );
			if ( $i < sizeof($chapters)-1 )
				$end = $chapters[$i+1]['time'];

			if ( ! $chapter['time'] )
				$chapter['time'] = "0";
			$url = "https://www.youtube.com/watch?v=". $video_id ."&t=".$chapter['time'];

			$ii = $i+1;
			$result[] = array(
				"@type"		 => "Clip",
				"@id"		 => "#Clip-$video_id-$ii",
				"name"		 => $chapter['title'],
				"startOffset"=> $chapter['time'],
				"endOffset"	 => $end,
				"url"		 => $url,
			);
		}
		
		$info['hasPart'] = $result;
	}
	
	return $info;
}
function acsc_is_youtube( $url ){
	$video_id = false;
	// A regex pattern for matching YouTube domain names
	$pattern = "/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be)\/watch\?v=([a-zA-Z0-9_-]+)$/";
	
	$pattern2 = "/^(\/\/)?(www\.)?(youtube\.com|youtu\.be)\/embed\/([a-zA-Z0-9_-]+)$/";

	// Check if the URL matches the pattern
	if (preg_match($pattern, $url, $matches)) {
		// Get the video ID from the fourth element of the matches array
		$video_id = $matches[4];

		
	} else if (preg_match($pattern2, $url, $matches)) {
		// Get the video ID from the fourth element of the matches array
		$video_id = $matches[4];
		
	}
	
	return $video_id;

}
// ▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒
// ▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒
function acsc_parse_duration( $dur, $res ){
	if ( ! $dur ) return null;
	$result = array(
		'hour' => 0,
		'min'  => 0,
		'sec'  => 0,
	);
	
	$dur = str_replace('PT', '', $dur);
	$dur .= '-';
	
	if ( sizeof(explode('H',$dur)) > 1 ) {
		$result['hour'] =
			intval(explode('H',$dur)[0]);
		$dur = explode('H',$dur)[1];
	}
	if ( sizeof(explode('M',$dur)) > 1 ) {
		$result['min'] =
			intval(explode('M',$dur)[0]);
		$dur = explode('M',$dur)[1];
	}
	if ( sizeof(explode('S',$dur)) > 1 ) {
		$result['sec'] =
			intval(explode('S',$dur)[0]);
	}
	
	
	$result['seconds'] = $result['sec'] + 
					 	($result['min'] * 60) +
					 	($result['hour'] * 60 * 60);
	
	if ( $res ) return $result[ $res ];
	else return $result;
	
}
function acsc_seconds_to_ISO( $seconds ){
	$seconds = intval( $seconds );
	$hours = floor($seconds / 3600);
	$minutes = floor($seconds / 60) % 60; // !!!
	$seconds = $seconds % 60;

	return "PT{$hours}H{$minutes}M{$seconds}S";
}
function acsc_getJSONFromHTML($url, $opts = [], $scriptVariable = '', $prefix = 'var ') {
	$html = acsc_getRemote($url, $opts);
	$jsonStr = acsc_getJSONStringFromHTML( $html, $scriptVariable, $prefix );
	return json_decode($jsonStr, true);
}
function acsc_getRemote($url, $opts = []){
	[$result, $headers] = acsc_getContentsAndHeadersFromOpts($url, $opts);
	/*
	if (str_contains($headers[0], strval(HTTP_CODE_DETECTED_AS_SENDING_UNUSUAL_TRAFFIC))) {
		acsc_detectedAsSendingUnusualTraffic();
	}
	*/
	return $result;
}
function acsc_getJSONStringFromHTML($html, $scriptVariable = '', $prefix = 'var '){
	// don't use as default variable because getJSONFromHTML call this function with empty string
	if ($scriptVariable === '') {
		$scriptVariable = 'ytInitialData';
	}
	return explode(';</script>', explode("\">$prefix$scriptVariable = ", $html, 3)[1], 2)[0];
}
function acsc_getContentsAndHeadersFromOpts($url, $opts) {
	$context = acsc_getContextFromOpts($opts);
	$result = file_get_contents($url, false, $context);
	return array($result, $http_response_header);
}
function acsc_detectedAsSendingUnusualTraffic(){
	dieWithJsonMessage('YouTube has detected unusual traffic from this YouTube operational API instance. Please try your request again later.');
}
function acsc_getContextFromOpts($opts) {
	/*
	if (GOOGLE_ABUSE_EXEMPTION !== '') {
		$cookieToAdd = 'GOOGLE_ABUSE_EXEMPTION=' . GOOGLE_ABUSE_EXEMPTION;
		if (array_key_exists('http', $opts)) {
			$http = $opts['http'];
			if (array_key_exists('header', $http)) {
				$headers = $http['header'];
				foreach ($headers as $headerIndex => $header) {
					if (acsc_str_starts_with($header, 'Cookie: ')) {
						$opts['http']['header'][$headerIndex] = "$header; $cookieToAdd";
						break;
					}
				}
			}
		} else {
			$opts = array(
				'http' => array(
					'header' => array(
						"Cookie: $cookieToAdd"
					)
				)
			);
		}
	}
	*/
	$context = stream_context_create( $opts );
	return $context;
}
if ( !function_exists('acsc_str_starts_with') ) {
    function acsc_str_starts_with($haystack, $needle) {
        return (string)$needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0;
    }
}
if ( !function_exists('acsc_getIntFromDuration') ) {
	function acsc_getIntFromDuration($timeStr) {
		$isNegative = $timeStr[0] === '-';
		if ($isNegative) {
			$timeStr = substr($timeStr, 1);
		}
		$format = 'j:H:i:s';
		$timeParts = explode(':', $timeStr);
		$timePartsCount = count($timeParts);
		$minutes = $timeParts[$timePartsCount - 2];
		$timeParts[$timePartsCount - 2] = strlen($minutes) == 1 ? "0$minutes" : $minutes;
		$timeStr = implode(':', $timeParts);
		for ($timePartsIndex = 0; $timePartsIndex < 4 - $timePartsCount; $timePartsIndex++) {
			$timeStr = "00:$timeStr";
		}
		while (date_parse_from_format($format, $timeStr) === false) {
			$format = substr($format, 2);
		}
		$timeComponents = date_parse_from_format($format, $timeStr);
		$timeInt = $timeComponents['day'] * (3600 * 24) +
				   $timeComponents['hour'] * 3600 +
				   $timeComponents['minute'] * 60 +
				   $timeComponents['second'];
		return ($isNegative ? -1 : 1) * $timeInt;
	}
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
}
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
// used in  acsc-init-data.php -> acsc_DATA()
//			acsc-FE.php -> acsc_FE_init()
//			acsc-FE.php -> acsc_schema_validation()
//    ajax	tools.js -> DYN.getWPmeta




// YOUTUBE INFO
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
add_action('wp_ajax_acsc_get_youtube_data',
		   'acsc_get_youtube_data');
function acsc_get_youtube_data() {
acsc_trc('    acsc_get_youtube_data');
	global $ACSC;
	
	$video_id = 0;
	if ( wp_doing_ajax() ) {
		// Verify nonce
		$nonce = sanitize_key($_POST['nonce']);
		if ( ! wp_verify_nonce( $nonce, 'acsc_nonce' ) ) {
			//wp_send_json_error( 'Invalid nonce.' );
			die( __( 'Invalid nonce.', 'actus-deep-schema' ) );
		}
		$video_id = sanitize_text_field( wp_unslash( $_POST['id'] ) );
	}
	
	if ( ! $ACSC['options'] )
		$ACSC['options'] = get_option( 'ACSC-options' );
	
	// Your API key
	$api_key = $ACSC['options']['youtube_api_key'];
	
	// The API endpoint for getting video information
	$api_url = "https://www.googleapis.com/youtube/v3/videos?id=$video_id&key=$api_key&part=snippet,contentDetails";
	
	
	$body = wp_remote_retrieve_body( wp_remote_get( $api_url ) );
	
	// Decode the JSON response into an associative array
	$data = json_decode($body, true);
	

	
	echo wp_json_encode( $data );
	wp_die();
	
}



// YOUTUBE CHAPTERS
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
add_action('wp_ajax_acsc_get_youtube_chapters',
		   'acsc_get_youtube_chapters');
function acsc_get_youtube_chapters( $_id ){
acsc_trc('    acsc_get_youtube_chapters');
	$id = '';
	
	if ( wp_doing_ajax() ) {
		// Verify nonce
		$nonce = sanitize_key($_POST['nonce']);
		if ( ! wp_verify_nonce( $nonce, 'acsc_nonce' ) ) {
			//wp_send_json_error( 'Invalid nonce.' );
			die( __( 'Invalid nonce.', 'actus-deep-schema' ) );
		}
		if ( isset($_POST['id']) )
			$id = sanitize_text_field( wp_unslash( $_POST['id'] ) );
	}
	if ( $_id ) $id = $_id;


	//include_once plugin_dir_path( __FILE__ ) . '/includes/acsc-functions.php';
	
	$json =
		acsc_getJSONFromHTML("https://www.youtube.com/watch?v=$id&hl=en");
	
	$chapters = [];
	$areAutoGenerated = false;
	$contents = array();
	if ( isset($json['engagementPanels'][1]['engagementPanelSectionListRenderer']['content']['macroMarkersListRenderer']) )
		$contents = $json['engagementPanels'][1]['engagementPanelSectionListRenderer']['content']['macroMarkersListRenderer']['contents'];
	if ($contents !== null && sizeof($contents) ) {
		$areAutoGenerated = array_key_exists('macroMarkersInfoItemRenderer', $contents[0]);
		$contents = array_slice($contents, $areAutoGenerated ? 1 : 0);
		foreach ($contents as $chapter) {
			$chapter = $chapter['macroMarkersListItemRenderer'];
			$timeInt = acsc_getIntFromDuration($chapter['timeDescription']['simpleText']);
			array_push($chapters, [
				'title' => $chapter['title']['simpleText'],
				'time' => $timeInt,
				'thumbnails' => $chapter['thumbnail']['thumbnails']
			]);
		}
	}
	
	$contents = $json['contents']['twoColumnWatchNextResults']['results']['results']['contents'];
		
//acsc_trc('acsc_get_youtube_chapters $contents', $contents);

	$info = array(
		'description' => $contents[1]['videoSecondaryInfoRenderer']['attributedDescription']['content'],
		
		'owner' => $contents[1]['videoSecondaryInfoRenderer']['owner']['videoOwnerRenderer']['title']['runs'][0]['text'],
		
		'views' => $contents[0]['videoPrimaryInfoRenderer']['viewCount']['videoViewCountRenderer']['viewCount']['simpleText'],
		
		'uploadDate' => $contents[0]['videoPrimaryInfoRenderer']['dateText']['simpleText'],
		
		
		
	);
	
	$info['uploadDate'] =
		str_replace('Premiered ', '', $info['uploadDate']);
	$info['uploadDate'] =
		date('Y-m-d', strtotime($info['uploadDate']));
	
	if ( isset($json['frameworkUpdates']['entityBatchUpdate']['mutations'][1]['payload']['macroMarkersListEntity']) ) {
		$markers = $json['frameworkUpdates']['entityBatchUpdate']['mutations'][1]['payload']['macroMarkersListEntity']['markersList']['markers'];
		if ( is_array( $markers ) ) {
			$marker = $markers[ sizeof($markers)-1 ];
			$info['duration'] = intval($marker['startMillis']) + intval($marker['durationMillis']);
			$info['duration'] = intval($info['duration'] / 1000);
			$info['duration'] = acsc_seconds_to_ISO( $info['duration'] );
			$info['duration'] = str_replace('PT0H', 'PT', $info['duration']);
			$info['duration'] = str_replace('PT0M', 'PT', $info['duration']);
			$info['duration'] = str_replace('PT0S', 'PT', $info['duration']);
			$info['duration'] = str_replace('H0M', 'H', $info['duration']);
			$info['duration'] = str_replace('H0S', 'H', $info['duration']);
			$info['duration'] = str_replace('M0S', 'M', $info['duration']);
		}
	}
	
	$result = [
		'areAutoGenerated' => $areAutoGenerated,
		'chapters' => $chapters,
		'info' => $info,
		'json' => $json,
	];

//acsc_trc('acsc_get_youtube_chapters $info', $info);
	
	if ( $_id ) return $result;
	
	echo wp_json_encode( $result );
	wp_die();
	
}









?>