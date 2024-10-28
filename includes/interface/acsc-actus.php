<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly



// ACTUS Deep Schema content on Actus Page
// ┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅┅
function acsc_actus_content(){

	actus_hook('actus-active-plugins',
			   'acsc_active_note');


}
function acsc_active_note(){
	global $ACSC;
	$logo = ACSC_URL . 'img/acsc_logo.png';
	?>
	<div class="actus-note acsc-actus-note">
		<div class="icon">
			<img class="act-admin-logo" src="<?php echo esc_url($logo); ?>">
		</div>
		
		<div class="text">
			<h3><b>Actus Deep Schema</b></h3>
			<a href="https://deepschema.org/" target="_blank">deepschema.org</a>
		</div>
		
		<div class="banner">
			<a href="https://deepschema.org/" target="_blank">
				<h3><?php esc_html_e('Get the premium version', 'actus-deep-schema'); ?></h3>
				<p><?php esc_html_e('for deeper structured data features!', 'actus-deep-schema'); ?></p>
			</a>
		</div>
		
		<div class="buttons">
			<div class="actus-button">
				<a href="admin.php?page=actus-deep-schema">
					<?php esc_html_e('plugin page', 'actus-deep-schema'); ?>
				</a>
			</div>
		</div>
		
	</div>
	<?php
}





// ≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣≣
acsc_actus_content();
?>