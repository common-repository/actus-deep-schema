<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! isset( $ACTUS ) ) $ACTUS = array();


// ACTUS plugins page
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
if ( !function_exists( 'actus_plugins_page' ) ) {
    function actus_plugins_page() {
		$logo = ACSC_URL . 'img/actus.png';
		$logo_full = ACSC_URL . 'img/actus-logo.png';
		?>

 
		<div class="wrap act-admin">

			<div class="act_grab_notes" style="display:none;"><h2></h2></div>

			<!-- HEADER -->
			<div class="act-admin-header acsc-header">
				<img class="act-admin-logo" src="<?php echo esc_url($logo); ?>">
				<h2 class="act-admin-title">ACTUS Anima</h2>


			</div>
			
			
			

			<!-- MAIN -->
			<div class="act-admin-main">
				<div class="actus-notes">

					<?php
					// HOOKS - actus-notes
					// ≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣
					actus_hooks('actus-notes');
					?>

				</div>
				
				
				<div class="actus-full-logo">
					<img src="<?php echo esc_url($logo_full); ?>">
					<a href="mailto:info@actus.works">info@actus.works</a>
				</div>
				
				
				<?php
				// HOOKS - actus-content
				// ≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣
				actus_hooks('actus-content');
				?>


			</div>


			<div class="actus-active-plugins">
				<h2><?php esc_html_e('Active plugins', 'actus-deep-schema'); ?></h2>
				<?php
				// HOOKS - actus-active-plugins
				// ≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣
				actus_hooks('actus-active-plugins');
				?>

			</div>

			

			<!-- FOOTER -->
			<div class="act-admin-footer">
				<div class="actus"><a href="https://actus.works" target="_blank">ACTUS anima</a></div>
			</div>
			
			

		</div>



		<?php
    }
}



// HOOKS
// ≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣
// runs hooks for -name-
if ( !function_exists( 'actus_hooks' ) ) {
function actus_hooks( $name, $data = "" ){
	$hooks = actus_get_hooks( $name );
	if ( $hooks ){
		foreach( $hooks as $hook ){
			if ( function_exists( $hook ) ) {
				$data = $hook( $data );
			}
		}
	}
	
	return $data;
}
}
// hook to -name-
if ( !function_exists( 'actus_hook' ) ) {
function actus_hook( $name, $func, $data = "" ){
	global $ACTUS;
	$hooks = actus_get_hooks();

	$hooks[ $name ][] = $func;
	
	if ( $data ) {
		if ( ! isset( $hooks[ $name."_data" ] ) )
			$hooks[ $name."_data" ] = array();
		$hooks[ $name."_data" ][] = $data;
	}
	
	
	if ( ! isset( $ACTUS['hooks'] ) || ! $ACTUS['hooks'] )
		$ACTUS['hooks'] = array();
	$ACTUS['hooks'][ $name ] = $hooks[ $name ];
	
	if ( ! isset($hooks[ $name."_data" ]) )
		$hooks[ $name."_data" ] = '';
	$ACTUS['hooks'][ $name."_data" ] =
		$hooks[ $name."_data" ];
}
}
// get hooks
if ( !function_exists( 'actus_get_hooks' ) ) {
function actus_get_hooks( $name="" ){
	global $ACTUS;
	
	$hooks = array();
	
	if ( isset( $ACTUS['hooks'] ) )
		$hooks = $ACTUS['hooks'];
	
	/*
	if ( isset( $_POST['hooks'] ) ) {
		$_hooks = map_deep( wp_unslash( $_POST['hooks'] ), 'sanitize_text_field' );
		$hooks = array_merge_recursive($hooks, $_hooks);
	}
	*/

	if ( $name ) {
		if ( ! isset( $hooks[ $name ] ) ) $hooks[ $name ] = array();
		return $hooks[ $name ];
	} else {
		return $hooks;
	}
}
}
?>