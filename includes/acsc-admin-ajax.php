<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      

acsc_trc('━━━━ acsc-admin-ajax.php');

//include_once plugin_dir_path( __FILE__ ) . '/includes/wp/post-data.php';

// VERIFY AJAX SECURITY
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
function acsc_security( $pass = false ) {
	$user_roles = ['administrator'];
	$options = get_option('ACSC-options');
	if ( is_array($options) && is_array($options['user_roles']) ) {
		$user_roles = $options['user_roles'];
	}

    // Verify nonce
    $nonce = sanitize_key($_POST['nonce']);
    if ( ! wp_verify_nonce( $nonce, 'acsc_nonce' ) ) {
       // wp_send_json_error( 'Invalid nonce.' );
		die( __( 'Invalid nonce.', 'actus-deep-schema' ) );
    }

	// check if user has a role that has access to the plugin
	$user = wp_get_current_user();
	foreach ( $user_roles as $role ) {
		if ( in_array( $role, (array) $user->roles ) ) {
			$pass = true;
		}
	}

    // Verify user permission
    if ( ! $pass ) {
		//wp_send_json_error( 'You do not have permission to perform this action.' );
		die( __( 'You do not have permission to perform this action.', 'actus-deep-schema' ) );
    }
	
	
}





// SAVE SCHEMA
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
add_action('wp_ajax_acsc_save_schema',
		   'acsc_save_schema');
function acsc_save_schema() {
	
    // Security checks
	acsc_security();

	// Save DATA
	if ( isset( $_POST['data'] ) ) {
		$data = map_deep( wp_unslash( $_POST['data'] ), 'sanitize_text_field' );
		update_option('ACSC_data', $data, false );
	}
			
	// Save SCHEMA
	$schema_name = "";
	$schema = "";
	if ( isset( $_POST['schemaname'] ) ) {
		$schema_name = sanitize_text_field( wp_unslash( $_POST['schemaname'] ) );
		$schema = map_deep( wp_unslash( $_POST['schema'] ), 'sanitize_text_field' );
		update_option('ACSC-' . wp_unslash( $schema_name ), $schema, false );
	}
	
	
	// return output to ajax call
	wp_send_json(array(
		$data,
		$schema_name,
		$schema
	));
    wp_die();
} 




// SAVE OPTIONS
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
add_action('wp_ajax_acsc_save_options', 'acsc_save_options');
function acsc_save_options() {
	$sanitized = array();
	
    // Security checks
    acsc_security();
	
	// Save OPTIONS
	if ( isset( $_POST['options'] ) ) {
		$saved = get_option('ACSC-options');
		$sanitized = map_deep( wp_unslash( $_POST['options'] ), 'sanitize_text_field' );
		$sanitized['license'] = $saved['license'];
		update_option('ACSC-options', $sanitized, false );
	}
			
	// return output to ajax call
	wp_send_json( $sanitized );
    wp_die();
} 



// GET OPTION
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
add_action('wp_ajax_acsc_get_option', 'acsc_get_option');
function acsc_get_option() {
	$result = "";
	
    // Security checks
    acsc_security( true );
	
	// Save OPTIONS
	if ( isset( $_POST['name'] ) ) {

		$name = sanitize_text_field( wp_unslash( $_POST['name'] ) );
		$result = get_option( $name );
		
	}
			
	// return output to ajax call
	if ( $result ) wp_send_json( $result );
    wp_die();
} 



// SET OPTION
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
add_action('wp_ajax_acsc_set_option', 'acsc_set_option');
function acsc_set_option() {
	
    // Security checks
    acsc_security();
	
	// Save OPTIONS
	if ( isset( $_POST['name'] ) && isset( $_POST['value'] ) ) {
		
		// Sanitize input data
		$name = sanitize_text_field( wp_unslash( $_POST['name'] ) );
		if ( is_array( $_POST['value'] ) )
			$value = map_deep( wp_unslash( $_POST['value'] ), 'sanitize_text_field' );
		else
			$value = sanitize_text_field( wp_unslash( $_POST['value'] ) );
		
		// update option
		update_option( $name, $value, false );
		
	}
			
	// return output to ajax call
	wp_send_json(array( $name, $value ));
    wp_die();
} 


// DELETE OPTION
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
add_action('wp_ajax_acsc_del_option', 'acsc_del_option');
function acsc_del_option() {
	
    // Security checks
    acsc_security();
	
	
	// DELETE OPTION
	if ( isset( $_POST['name'] ) ) {

		// Sanitize input data
		$name = sanitize_text_field( wp_unslash( $_POST['name'] ) );
		
		// delete option
		delete_option( $name );
		
	}
			
	// return output to ajax call
	wp_send_json( $name );
    wp_die();
} 


// DELETE EDIT LOCK
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
add_action('wp_ajax_acsc_del_edit_lock', 'acsc_del_edit_lock');
function acsc_del_edit_lock() {
	

    // Security checks
    acsc_security();
	
	
	// delete OPTION
	if ( isset( $_POST['idx'] ) ) {

		// Sanitize input data
		$name = sanitize_text_field( wp_unslash( $_POST['idx'] ) );
		$user = sanitize_text_field( wp_unslash( $_POST['user'] ) );
		$name = "edit-lock-$name";
		$result = get_option( $name );
		
		
		if ( is_array($result) && $result['user'] == $user ){
		
			// delete option
			delete_option( $name );

		}
		
	}
			
	// return output to ajax call
	wp_send_json( $name );
    wp_die();
} 








// SAVE DATA
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
add_action('wp_ajax_acsc_save_data', 'acsc_save_data');
function acsc_save_data() {
	
    // Security checks
    acsc_security();
	
	// Save DATA
	if ( isset( $_POST['data'] ) ) {
		$sanitized = map_deep( wp_unslash( $_POST['data'] ), 'sanitize_text_field' );
		update_option('ACSC_data', $sanitized, false );
	}
			
	// return output to ajax call
	wp_send_json(array( $sanitized));
    wp_die();
} 



// DELETE SCHEMA
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
add_action('wp_ajax_acsc_delete_schema', 'acsc_delete_schema');
function acsc_delete_schema() {
	
    // Security checks
    acsc_security();
	
	// Delete SCHEMA
	if ( $_POST['schemaname'] )
		delete_option( 'ACSC-' . sanitize_text_field( wp_unslash( $_POST['schemaname'] ) ));
	
	// Save DATA
	if ( isset( $_POST['data'] ) ) {
		$sanitized = map_deep( wp_unslash( $_POST['data'] ), 'sanitize_text_field' );
		update_option( 'ACSC_data', $sanitized, false );
	}
		
    die();
}



// SEARCH PAGE
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
add_action('wp_ajax_acsc_search_page', 'acsc_search_page');
//add_action( 'wp_ajax_nopriv_search_page', 'acsc_search_page' );
function acsc_search_page() {
	
    // Security checks
    acsc_security();
	
	$post_type = sanitize_title( wp_unslash( $_POST['posttype'] ) );
	if ( $post_type == 'both' ) $post_type = array('post', 'page');
	
	$keyword = sanitize_text_field( wp_unslash( $_POST['keyword'] ) );
	
    $query = new WP_Query(array(
		'posts_per_page' => -1,
		//'s' 		=> esc_attr( $_POST['keyword'] ),
		's' 		=> $keyword,
		'post_type' => $post_type
	));
	
    if( $query->have_posts() ) :
        while( $query->have_posts() ): $query->the_post(); 
	
			$link = get_permalink( $query->post->ID );
?>
			<div id="<?php echo esc_attr($query->post->ID); ?>"
				 alt="<?php echo esc_url($link); ?>">
				<?php echo '<span>';
						the_title();
					  echo '</span> ('.esc_html($query->post->ID).')'; ?></div>
<?php

		endwhile;
        wp_reset_postdata();
    endif;

    die();
	
} 



// SEARCH POST
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
add_action('wp_ajax_acsc_search_post', 'acsc_search_post');
//add_action( 'wp_ajax_nopriv_search_page', 'acsc_search_page' );
function acsc_search_post() {
	global $ACSC;
	
    // Security checks
    acsc_security();
	
	$post_type = sanitize_title( wp_unslash( $_POST['posttype'] ) );
	if ( ! $post_type || $post_type == 'both' ) $post_type = array('post', 'page');
	
	if ( $post_type == 'all' || $post_type == 'search' ) {
		$post_type = array('page');
		$ptypes = get_post_types($args, 'objects', 'and');
		foreach($ptypes as $key => $row){
			if ( $row->publicly_queryable == true &&
			     $key != 'attachment' )
				$post_type[] = $key;
		}
	}
	
	if ( $post_type == 'posts' ) $post_type = 'post';
	if ( $post_type == 'pages' ) $post_type = 'page';
	
	$keyword = sanitize_text_field( wp_unslash( $_POST['keyword'] ) );
	
    $query = new WP_Query(array(
		'posts_per_page' => -1,
		//'s' 		=> esc_attr( $_POST['keyword'] ),
		's' 		=> $keyword,
		'post_type' => $post_type,
	));
	


	$posts = $query->posts;
	if ( ! $posts ) $posts = array();
	
	foreach ($posts as $idx => $row){
		$posts[$idx]->url = get_permalink( $row->ID );
	}

	
	wp_send_json( $posts );
    wp_die();
	
} 




// SEARCH USER
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
add_action('wp_ajax_acsc_search_user', 'acsc_search_user');
//add_action( 'wp_ajax_nopriv_search_page', 'acsc_search_page' );
function acsc_search_user() {
	
    // Security checks
    acsc_security();
	
	$search = sanitize_text_field( wp_unslash( $_POST['search'] ) );


	$args = array(
		'search' => "*$search*",
		'search_columns' => array( 'user_login', 'user_nicename', 'user_email' )
	);
	$user_query = new WP_User_Query( $args );
	$users_found = $user_query->get_results();
	if ( ! $users_found ) $users_found = array();
	/*
	$return['total'] = $user_query->get_total();
	$return['pages'] = ceil( $return['total'] / $query['number'] );
	*/
	$result = array();
	foreach( $users_found as $user ){
		unset( $user->data->user_pass );
		$result[] = $user->data;
	}
	
	wp_send_json( $result );
    wp_die();
	
}



// GET USER
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
add_action('wp_ajax_acsc_get_user', 'acsc_get_user');
//add_action( 'wp_ajax_nopriv_search_page', 'acsc_search_page' );
function acsc_get_user() {
	
    // Security checks
    acsc_security();
	
	$uid = sanitize_text_field( wp_unslash( $_POST['uid'] ) );

	if ( ! $uid ) return array();
	/*
	$user_query = new WP_User_Query(array('id' => $uid));
	$result = (array) $user_query->results;
	$user = $result[0]->data;
	*/
	$user = get_user_by('ID', $uid);
	unset( $user->data->user_pass );
	unset( $user->data->user_activation_key );
	$user->data->description =
		get_user_meta( $user->ID, 'description', true );
	
	$user->data->social =
		get_user_meta( $user->ID, 'autodescription-user-settings', true );
		$user->data->roles = $user->caps;
		$user->data->avatar = get_avatar_url( $user->ID,
				array('default' => '', 'force_default' => true) );
		
	
	$user = $user->data;
	
	wp_send_json( $user );
    wp_die();
	
}
 




// SAVE JSON to file for validating / debugging
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
add_action( 'wp_ajax_acsc_save_json', 'acsc_save_json' );
function acsc_save_json() {
	
    // Security checks
    acsc_security();
	
	$data = sanitize_text_field( wp_unslash( $_POST['data'] ) );
	$myFile = site_url() . "/schema_debug.json";
	
	$fh = fopen($myFile, 'w') or die("can't open file");
	fwrite($fh, $data);
	fclose($fh);
	
	echo "OK";
	wp_die();
	
}





 



// Get page content
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
add_action( 'wp_ajax_acsc_get_page_content', 'acsc_get_page_content' );
function acsc_get_page_content() {
	
    // Security checks
    acsc_security();
	
	if ( isset( $_POST['acsc_pid'] ) ) {
		$post = get_post( sanitize_text_field( wp_unslash( $_POST['acsc_pid'] ) ) );
		$content = $post->post_content;
		$content = apply_filters('the_content', $content);
		$content = do_shortcode( $content );
		
		echo wp_kses_post($content);
		wp_die();
	}
	
	wp_die();
}




// Create page
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
add_action( 'wp_ajax_acsc_create_page', 'acsc_create_page' );
function acsc_create_page( $args ) {
	
	if ( wp_doing_ajax() ) {
		
		// Security checks
		acsc_security();

		if ( $_POST['args'] )
			$args = map_deep( wp_unslash( $_POST['args'] ), 'sanitize_text_field' );

	}
	
	
	$date = date('Y-m-d');
	$defaults = array(
		'comment_status' => 'close',
		'ping_status'    => 'close',
		'post_author'    => get_current_user_id(),
		'post_title'     => '',
		'post_name'      => '',
		'post_status'    => 'publish',
		'post_content'   => '',
		'post_type'      => 'page',
		'post_parent'    =>  NULL,
		'post_date'      => date("Y-m-d H:i:s",
								 strtotime ('-1 day' , strtotime($date)) )
	);
	$args = array_merge($defaults, $args);
	
	$args['post_name'] = sanitize_title( $args['post_name'] );


	$page = get_page_by_path( $args['post_name'] );
    if( $page ) {
		echo esc_html($page->post_name);
		wp_die();
    }
    
    $page_id = wp_insert_post( $args );
	update_post_meta( $page_id, '_wp_page_template', 'ads-view.php' );
	
    echo esc_html($args['post_name']);
	wp_die();
}





// GET TARGET IDS
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
add_action('wp_ajax_acsc_get_target_ids', 'acsc_get_target_ids');
function acsc_get_target_ids() {
	$result = array();
	
    // Security checks
    acsc_security();
	
	// Save OPTIONS
	if ( isset( $_POST['targets'] ) ) {
		
		$targets = map_deep( wp_unslash( $_POST['targets'] ), 'sanitize_text_field' );

		// Get the default template
        $default_template = get_option('template');
        $default_template = 'page.php';

		foreach( $targets as $idx => $target ){
			// by template
			if ( $target['type'] == 'template' ){
				
				$args = array(
					'post_type' => 'page',
					'posts_per_page' => 10,
					'fields' => 'ids',
					'meta_query' => array(
                        'relation' => 'OR',
                        array(
                            'key' => '_wp_page_template',
                            'value' => $target['value']
                        )
                    )
				);
                // If the target value is the default template, add condition to get pages with default template
                if ($target['value'] == $default_template) {
                    $args['meta_query'][] = array(
                        'key' => '_wp_page_template',
                        'compare' => 'NOT EXISTS'
                    );
                }
				$page_ids = get_posts($args);
				$result = array_merge( $result, $page_ids );

			}

			// by parent
			if ( $target['type'] == 'parent' ){
				$args = array(
					'post_type' => 'page',
					'posts_per_page' => 10,
					'fields' => 'ids',
					'post_parent' => $target['value'][0]
				);
				$page_ids = get_posts($args);
				$result = array_merge( $result, $page_ids );

			}
			

			// by author
			if ( $target['type'] == 'author' ){
				$args = array(
					'post_type' => 'page',
					'posts_per_page' => 10,
					'fields' => 'ids',
					'author' => $target['value']
				);
				$page_ids = get_posts($args);
				$result = array_merge( $result, $page_ids );

			}

		}
		
	}
			
	// return output to ajax call
	if ( $result ) wp_send_json( $result );
    wp_die();
} 










// ≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣
// Sanitize multidimensional array and other data
function acsc_sanitize( $data ) {
	
	$data = wp_unslash( $data );

    // Check if the input is an array
    if ( is_array( $data ) ) {
		
        // Initialize a new array
        $res = array();

        // Loop through each item in the array
        foreach ( $data as $key => $value ) {
			
            // Sanitize the key
            //$sanitized_key = sanitize_key( $key );

            // If the value is another array, recursively call this function to sanitize it
            if ( is_array( $value ) ) {
                $res[ $key ] = acsc_sanitize( $value );
				
            } else {
                // Otherwise, sanitize the value
                $res[ $key ] =
					wp_kses( $value, array( 
					    'a' => array(
					        'href' => array(),
					        'title' => array()
					    ),
					    'br' => array(),
					    'em' => array(),
					    'strong' => array(),
					    'span' => array(),
					) );
            }
        }

        // Return the sanitized array
        return $res;
		
    } else {
		
        // If the input is not an array, sanitize it
        return wp_kses( $data, array( 
		    'a' => array(
		        'href' => array(),
		        'title' => array()
		    ),
		    'br' => array(),
		    'em' => array(),
		    'strong' => array(),
		    'span' => array(),
		) );
		
    }
}
// Set filename for save backup
function acsc_setFilename( $fileName, $acsc_upload_dir ){
	$name = substr( $fileName, 0, -4);
	$ext = substr($fileName, -3);
	if ( substr( $fileName, -5, 1 ) == '.' ) {
		$name = substr( $fileName, 0, -5);
		$ext = substr($fileName, -4);
	}

	if ( file_exists( $acsc_upload_dir . $fileName ) )
		$fileName = $name . '-' . acsc_generateRandomString() . '.' . $ext;

	
	return $fileName;

}
// Generate Random String
function acsc_generateRandomString($length = 3) {
// **********************************************************
    $characters = 'abcdefghijklmnopqrstuvwxyz';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[wp_rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅

 
?>